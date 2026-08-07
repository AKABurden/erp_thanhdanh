<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/api_app/Api_Controller.php');

class Api_Manufactures extends Api_Controller
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

    public function getProductionsOrders($page = 1, $limit = 10)
    {
        $data = [];
        if (!$this->perViewProductionsOrders && !$this->perViewOwnProductionsOrders) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        $start = ($page - 1) * $limit;
        $name_search = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $productions_orders_search = !empty($data_post['productions_orders_search']) ? $data_post['productions_orders_search'] : null;
        $start_date_search = !empty($data_post['start_date_search']) ? to_sql_date($data_post['start_date_search']) . ' 00:00:00' : null;
        $end_date_search = !empty($data_post['end_date_search']) ? to_sql_date($data_post['end_date_search']) . ' 23:59:59' : null;
        $search = !empty($data_post['search']) ? $data_post['search'] : null;

        $tbProductionsPlanOrdersByOrders = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') SEPARATOR '|||') reference_no_orders,
                GROUP_CONCAT(distinct tbl_orders.note) as note
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_productions_plan_orders.object_type = 'orders'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_orders";

        $tbProductionsPlanOrdersByBusinessPlan = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(tbl_business_plan.reference_no SEPARATOR '|||') reference_no_business_plan
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
            WHERE tbl_productions_plan_orders.object_type = 'business_plan'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_business_plan";

        $this->db->select("
            tbl_productions_orders.id as id,
            tbl_productions_orders.date as date,
            tbl_productions_orders.reference_no as reference_no,
            tbl_productions_orders.note as note,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
            tblbranch.name as branch_name,
            tbl_productions_orders.status_details,
            tbl_productions_orders.options1 as options1,
            tbl_productions_orders.options2 as options2,
            tb_orders.reference_no_orders as reference_no_orders,
            tb_business_plan.reference_no_business_plan as reference_no_business_plan,
            tbl_productions_orders.total_quantity as total_quantity
        ", FALSE)
            ->from('tbl_productions_orders')
            ->join('tblbranch', 'tblbranch.id = tbl_productions_orders.location_id', 'inner')
            ->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders.created_by', 'left')
            ->join($tbProductionsPlanOrdersByOrders, 'tb_orders.productions_order_id = tbl_productions_orders.id', 'left')
            ->join($tbProductionsPlanOrdersByBusinessPlan, 'tb_business_plan.productions_order_id = tbl_productions_orders.id', 'left');
        $this->db->order_by('tbl_productions_orders.date DESC');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders.reference_no', $search);
            $this->db->or_like('tb_orders.note', $search);
            $this->db->group_end();
        }

        if (!empty($productions_orders_search)) {
            $this->db->where('tbl_productions_orders.id', $productions_orders_search);
        }

        if (!empty($start_date_search)) {
            $this->db->where('tbl_productions_orders.date >=', $start_date_search);
        }

        if (!empty($end_date_search)) {
            $this->db->where('tbl_productions_orders.date <=', $end_date_search);
        }

        if (!$this->perViewProductionsOrders) {
            $this->db->where('tbl_productions_orders.created_by =', $this->staffid);
        }
        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $productions_orders_id = $value['id'];
                $this->db->where('productions_orders_id', $productions_orders_id);
                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tblorder_production_details_feedback.id_order_production_details');
                $quantitycomment = $this->db->count_all_results('tblorder_production_details_feedback');
                $result[$key]['quantity_comment'] = $quantitycomment;

                $this->db->select('
                    tbl_productions_orders_items.id as id,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    tbl_products.id as item_id,
                    tbl_products.images as images,
                    tbl_productions_orders_items.quantity,
                    tbl_productions_orders_items.object_item_type as object_item_type,
                    tbl_productions_orders_items.production_plan_item_id as production_plan_item_id,
                    tbl_productions_orders_details.id as pod_id
                ');
                $this->db->from('tbl_productions_orders_items');
                $this->db->join(' tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
                $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
                $productions_orders_items = $this->db->get()->result_array();
                if (!empty($productions_orders_items)) {
                    foreach ($productions_orders_items as $k => $v) {
                        $object_item_type = $v['object_item_type'];
                        $production_plan_item_id = $v['production_plan_item_id'];
                        $productions_orders_items_id = $v['id'];
                        $reference_no = '';
                        if ($object_item_type == "orders") {
                            $this->db->select('
                                tbl_orders.reference_no as reference_no,
                                tblclients.company as company
                            ');
                            $this->db->from('tbl_order_items');
                            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id');
                            $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
                            $this->db->where('tbl_order_items.id', $production_plan_item_id);
                            $order = $this->db->get()->row_array();
                            if (!empty($order)) {
                                $reference_no = $order['reference_no'].' - '.$order['company'];
                            }
                        } else if ($object_item_type == "business_plan") {
                            $this->db->select('
                                tbl_business_plan.reference_no as reference_no,
                            ');
                            $this->db->from('tbl_business_plan_items');
                            $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_business_plan_items.business_plan_id');
                            $this->db->where('tbl_business_plan_items.id', $production_plan_item_id);
                            $business_plan = $this->db->get()->row_array();
                            if (!empty($business_plan)) {
                                $reference_no = $business_plan['reference_no'];
                            }
                        }
                        $productions_orders_items[$k]['reference_no'] = $reference_no;
                        
                        if ($v['images']) {
                            $productions_orders_items[$k]['images'] = base_url('uploads/products/'.$v['images']);
                        }
                        //process
                        $this->db->select('
                            tbl_productions_orders_items_stages.id as id,
                            tbl_productions_orders_items_stages.active as active,
                            tbl_productions_orders_items_stages.staff_active as staff_active,
                            tbl_productions_orders_items_stages.date_active as date_active,
                            tbl_stages.name as stage_name,
                            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                            IF (tblstaff.profile_image IS NOT NULL, CONCAT("'.base_url('uploads/staff_profile_images/').'", tblstaff.staffid, "/small__", tblstaff.profile_image), null) as staff_image,
                            tbl_productions_orders_items_stages.final_stage as final_stage
                        ', false);
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
                        $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
                        $process = $this->db->get()->result_array();
                        $isActive = 0;
                        if (!empty($process)) {
                            // foreach ($process as $kkk => $vvv) {
                            // }
                        }
                        $productions_orders_items[$k]['process'] = $process;
                    }
                }
                $result[$key]['productions_orders_items'] = $productions_orders_items;
            }
        }
        $data['data'] = $result;

        $startNest = ($page) * $limit;
        $this->db->select('tbl_productions_orders.id', false);
        $this->db->from('tbl_productions_orders');
        $this->db->join('tblbranch', 'tblbranch.id = tbl_productions_orders.location_id', 'inner');
        $this->db->join($tbProductionsPlanOrdersByOrders, 'tb_orders.productions_order_id = tbl_productions_orders.id', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders.reference_no', $search);
            $this->db->or_like('tb_orders.note', $search);
            $this->db->group_end();
        }

        if (!empty($productions_orders_search)) {
            $this->db->where('tbl_productions_orders.id', $productions_orders_search);
        }
        if (!empty($start_date_search)) {
            $this->db->where('tbl_productions_orders.date >=', $start_date_search);
        }

        if (!empty($end_date_search)) {
            $this->db->where('tbl_productions_orders.date <=', $end_date_search);
        }

        if (!$this->perViewProductionsOrders) {
            $this->db->where('tbl_productions_orders.created_by =', $this->staffid);
        }
        $this->db->limit(1, $startNest);
        $data['next'] = $this->db->get()->num_rows();
        $data['result'] = 1;
        $data['message'] = lang('success');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getDetailProductionsOrders($id)
    {
        $data = [];
        if (!$this->perViewProductionsOrders && !$this->perViewOwnProductionsOrders) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }

        $productions_orders = $this->manufactures_model->rowProductionsOrdersById($id);
        $tbProductionsPlanOrdersByOrders = "(
            SELECT
                tbl_orders.reference_no as reference_no,
                tblclients.company as company
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_productions_plan_orders.object_type = 'orders' AND tbl_productions_plan_orders.productions_order_id = " . $this->db->escape($id) . "
        )";
        $orders = $this->db->query($tbProductionsPlanOrdersByOrders)->result_array();

        $tbProductionsPlanOrdersByBusinessPlan = "(
            SELECT
                tbl_business_plan.reference_no as reference_no
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
            WHERE tbl_productions_plan_orders.object_type = 'business_plan' AND tbl_productions_plan_orders.productions_order_id = " . $this->db->escape($id) . "
        )";
        $business_plan = $this->db->query($tbProductionsPlanOrdersByBusinessPlan)->result_array();
        $branch = $this->site_model->getBranchById($productions_orders['location_id']);

        $data['productions_orders'] = $productions_orders;
        $data['branch'] = $branch;
        $data['orders'] = $orders;
        $data['business_plan'] = $business_plan;
        $data['created_by'] = get_staff_full_name($productions_orders['created_by']);
        echo json_encode($data);
    }

    public function getDetailItemsProductionsOrders($page = 1, $limit = 10)
    {
        $data = [];
        if (!$this->perViewProductionsOrders && !$this->perViewOwnProductionsOrders) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }

        $start = ($page - 1) * $limit;
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $productions_orders_id = $data_post['productions_orders_id'];
        $this->db->select('
            tbl_productions_plan_orders.productions_plan_id as productions_plan_id,
            tbl_productions_plan_orders.total_quantity as total_quantity,
            tbl_productions_plan_orders.object_type as object_type,
        ', false);
        $this->db->from('tbl_productions_plan_orders');
        $this->db->where('tbl_productions_plan_orders.productions_order_id', $productions_orders_id);
        $this->db->where('tbl_productions_plan_orders.total_quantity >', 0);
        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $aRow) {
                $object_type = $aRow['object_type'];
                $productions_plan_id = $aRow['productions_plan_id'];

                $orders = [];
                $business_plan = [];
                if ($object_type == "orders") {
                    $tbProductionsPlanOrdersByOrders = "(
                        SELECT
                            tbl_orders.reference_no as reference_no,
                            tblclients.company as company,
                            tbl_orders.date as date
                        FROM tbl_productions_plan_orders
                        INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
                        INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
                        WHERE tbl_productions_plan_orders.object_type = 'orders' AND tbl_productions_plan_orders.productions_order_id = " . $this->db->escape($productions_orders_id) . " AND tbl_productions_plan_orders.productions_plan_id = '$productions_plan_id'
                    )";
                    $orders = $this->db->query($tbProductionsPlanOrdersByOrders)->row_array();
                } else if ($object_type == "business_plan") {
                    $tbProductionsPlanOrdersByBusinessPlan = "(
                        SELECT
                            tbl_business_plan.reference_no as reference_no,
                            tbl_business_plan.date as date
                        FROM tbl_productions_plan_orders
                        INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
                        WHERE tbl_productions_plan_orders.object_type = 'business_plan' AND tbl_productions_plan_orders.productions_order_id = " . $this->db->escape($productions_orders_id) . " AND tbl_productions_plan_orders.productions_plan_id = '$productions_plan_id'
                    )";
                    $business_plan = $this->db->query($tbProductionsPlanOrdersByBusinessPlan)->row_array();
                }
                $result[$key]['orders'] = $orders;
                $result[$key]['business_plan'] = $business_plan;
                if ($productions_plan_id) {
                    $this->db->select('
                        tbl_productions_orders_items.id as productions_orders_items_id,
                        tbl_productions_orders_items.production_plan_item_id as production_plan_item_id,
                        tbl_products.code as item_code,
                        tbl_products.name as item_name,
                        tbl_products.images as item_images,
                        tblsize.name as size_name,
                        tbl_productions_orders_items.quantity as quantity
                    ', false);
                    $this->db->from('tbl_productions_orders_items');
                    if ($object_type == "orders") {
                        $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_productions_orders_items.production_plan_item_id');
                        $this->db->where('tbl_order_items.order_id', $productions_plan_id);
                    } else if ($object_type == "orders") {
                        $this->db->join('tbl_business_plan_items', 'tbl_business_plan_items.id = tbl_productions_orders_items.production_plan_item_id');
                        $this->db->where('tbl_business_plan_items.business_plan_id', $productions_plan_id);
                    }
                    $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                    $this->db->join('tblsize', 'tblsize.id = tbl_products.size', 'left');
                    $this->db->where('tbl_productions_orders_items.object_item_type', $object_type);
                    $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
                    $productions_orders_items = $this->db->get()->result_array();
                    foreach ($productions_orders_items as $k => $v) {
                        $productions_orders_items_id = $v['productions_orders_items_id'];
                        $pod = $this->manufactures_model->rowProductionsOrdersDetailsByPOI($productions_orders_items_id);
                        $pod_id = $pod['id'];

                        $images = $v['item_images'];
                        if (!empty($images)) {
                            $images = base_url('uploads/products/' . $images);
                        }
                        $productions_orders_items[$k]['images'] = $images;

                        //processs
                        $production_plan_item_id = $v['production_plan_item_id'];
                        $this->db->select('
                            tbl_productions_orders_items_stages.id as id,
                            tbl_stages.name as stage_name, 
                            tbl_productions_orders_items_stages.active as active, 
                            tbl_productions_orders_items_stages.date_active as date_active,
                            tbl_productions_orders_items_stages.staff_active as staff_active,
                            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                            tblstaff.profile_image as profile_image,
                            tbl_stages.status_qc as status_qc,
                            tbl_stages.id as stage_id,
                            tbl_productions_orders_items_stages.final_stage as final_stage,
                            tbl_stages.type as type,
                            tbl_productions_orders_items_stages.begin_productions as begin_productions,
                            tbl_productions_orders_items_stages.date_productions as date_productions,
                            tbl_productions_orders_items_stages.staff_productions as staff_productions,
                            CONCAT(staff_productions.firstname, " ", staff_productions.lastname) as staff_name_productions,
                            staff_productions.profile_image as profile_image_productions,
                            IF (tblstaff.profile_image IS NOT NULL, CONCAT("' . base_url('uploads/staff_profile_images/') . '", tblstaff.staffid, "/small__", tblstaff.profile_image), null) as staff_image
                        ', false);
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
                        $this->db->join('tblstaff staff_productions', 'staff_productions.staffid = tbl_productions_orders_items_stages.staff_productions', 'left');
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
                        $this->db->order_by('tbl_productions_orders_items_stages.number DESC');
                        $productions_orders_items_stages = $this->db->get()->result_array();
                        if (!empty($productions_orders_items_stages)) {
                            $arrActiveLast = [];
                            $ctStages = count($productions_orders_items_stages);
                            $keyFlash = 1;
                            foreach ($productions_orders_items_stages as $kPS => $vPS) {
                                $pois_id = $vPS['id'];
                                $status_qc = $vPS['status_qc'];
                                $active = $vPS['active'] ? "active" : '';
                                $stage_id = $vPS['stage_id'];
                                $type = $vPS['type'];
                                if (!empty($active)) {
                                    if ($status_qc == 1) {
                                        $queryQC = "(
                                            SELECT
                                                tbl_check_quality.id as id,
                                                tbl_check_quality.date as date,
                                                tbl_check_quality.reference_no as reference_no,
                                                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as staff_created_name,
                                                tbl_check_quality_items.quantity_qc as quantity_qc
                                            FROM tbl_check_quality_items
                                            INNER JOIN tbl_check_quality ON tbl_check_quality.id = tbl_check_quality_items.check_quality_id
                                            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_check_quality.created_by
                                            WHERE tbl_check_quality_items.pod_id = '$pod_id' AND tbl_check_quality_items.id_stage = '$stage_id'
                                        )";
                                        $dbQC = $this->db->query($queryQC)->result_array();
                                        $productions_orders_items_stages[$kPS]['outsource'] = $dbQC;
                                    }

                                    $qcReturn = handlingQC($stage_id, $pod_id, false,[], $type, $pois_id, true)['qcReturn'];
                                    $productions_orders_items_stages[$kPS]['qcReturn'] = $qcReturn;

                                    $queryOutsource = "(
                                        SELECT
                                            tbl_outsource.id as id,
                                            tbl_outsource.reference_no as reference_no,
                                            tbl_outsource.date as date,
                                            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as staff_created_name,
                                            tbl_outsource_items.quantity as quantity,
                                            tblsuppliers.company as suppliers_company
                                        FROM tbl_outsource_items
                                        INNER JOIN tbl_outsource ON tbl_outsource.id = tbl_outsource_items.outsource_id
                                        INNER JOIN tblsuppliers ON tbl_outsource.supplier_id = tblsuppliers.id
                                        LEFT JOIN tblstaff ON tblstaff.staffid = tbl_outsource.created_by
                                        WHERE tbl_outsource_items.pod_id = '$pod_id' AND tbl_outsource_items.id_stage = '$stage_id'
                                    )";
                                    $dbOutsource = $this->db->query($queryOutsource)->result_array();
                                    $productions_orders_items_stages[$kPS]['outsource'] = $dbOutsource;

                                    $queryImportOutsource = "(
                                        SELECT
                                            tbl_import_outsource.id as id,
                                            tbl_import_outsource.reference_no as reference_no,
                                            tbl_import_outsource.date as date,
                                            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as staff_created_name,
                                            tbl_import_outsource_items.quantity as quantity,
                                            tblsuppliers.company as suppliers_company
                                        FROM  tbl_import_outsource_items
                                        INNER JOIN tbl_import_outsource ON tbl_import_outsource.id = tbl_import_outsource_items.import_outsource_id
                                        INNER JOIN tblsuppliers ON tbl_import_outsource.supplier_id = tblsuppliers.id
                                        LEFT JOIN tblstaff ON tblstaff.staffid = tbl_import_outsource.created_by
                                        WHERE tbl_import_outsource_items.pod_id = '$pod_id' AND tbl_import_outsource_items.stage_id_default = '$stage_id'
                                    )";
                                    $dbImportOutsource = $this->db->query($queryImportOutsource)->result_array();
                                    $productions_orders_items_stages[$kPS]['import_outsource'] = $dbImportOutsource;
                                }

                                $this->db->select('
                                    tbl_products.id as id,
                                    tbl_products.code as code,
                                    tbl_products.name as name,
                                    SUM(tbl_purchase_product_items.quantity) as quantity,
                                    tbl_products.images as images
                                ', false);
                                $this->db->from('tbl_purchase_products');
                                $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
                                $this->db->join('tbl_products', 'tbl_products.id = tbl_purchase_product_items.item_id');
                                $this->db->where('tbl_purchase_products.productions_orders_details_id', $pod_id);
                                $this->db->where('tbl_purchase_products.pois_id', $pois_id);
                                $this->db->group_by('tbl_products.id');
                                $purchase_products_items = $this->db->get()->result_array();
                                if (!empty($purchase_products_items)) {
                                    foreach ($purchase_products_items as $kPPI => $vPPI) {
                                        if (!empty($vPPI['images'])) {
                                            $purchase_products_items[$kPPI]['images'] = base_url('uploads/products/'.$vPPI['images']);
                                        }
                                    }
                                }
                                $productions_orders_items_stages[$kPS]['purchase_products_items'] = $purchase_products_items;


                            }
                        }
                        $productions_orders_items[$k]['process'] = $productions_orders_items_stages;
                        $productions_orders_items[$k]['pod_id'] = $pod['id'];

                        

                        //feedback
                        $this->db->select('
                            tblorder_production_details_feedback.*
                        ', false);
                        $this->db->where('tbl_productions_orders_details.productions_orders_item_id', $productions_orders_items_id);
                        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tblorder_production_details_feedback.id_order_production_details');
                        $this->db->order_by('tblorder_production_details_feedback.date_create', 'desc');
                        $this->db->limit(4);
                        $order_production_details_feedback = $this->db->get('tblorder_production_details_feedback')->result_array();
                        foreach ($order_production_details_feedback as $kD => $vD) {
                            $id_order_production_details = $vD['id_order_production_details'];
                            $feedback = strip_tags($vD['feedback']);
                            $order_production_details_feedback[$kD]['feedback'] = $feedback;

                            $staff = get_table_where('tblstaff', ['staffid' => $vD['create_by']], '', 'row_array', '', 'firstname, lastname, staffid, profile_image');
                            $fullnameStaff = $staff['firstname'] . ' ' . $staff['lastname'];
                            $staffImage = base_url('assets/images/user-placeholder.jpg');
                            if (!empty($staff['profile_image'])) {
                                $staffImage = base_url('uploads/staff_profile_images/' . $staff['staffid'] . '/small_' . $staff['profile_image']);
                            }

                            $this->db->where('rel_id', $vD['id']);
                            $this->db->where('rel_type', 'feedback_opd');
                            $file = $this->db->get('tblfiles')->result_array();
                            if (!empty($file)) {
                                foreach ($file as $keyFile => $valFile) {
                                    $file[$keyFile]['link'] = base_url('uploads/order_production_details_feedback/' . $vD['id'] . '/' . $valFile['file_name']);
                                }
                            }
                            $order_production_details_feedback[$kD]['file'] = $file;
                            $order_production_details_feedback[$kD]['fullnameStaff'] = $fullnameStaff;
                            $order_production_details_feedback[$kD]['staffImage'] = $staffImage;
                        }
                        $productions_orders_items[$k]['comment'] = $order_production_details_feedback;
                    }
                    $result[$key]['productions_orders_items'] = $productions_orders_items;
                }
            }
        }

        $data['data'] = $result;
        $startNest = ($page) * $limit;
        $this->db->select('tbl_productions_plan_orders.id');
        $this->db->from('tbl_productions_plan_orders');
        $this->db->where('tbl_productions_plan_orders.productions_order_id', $productions_orders_id);
        $this->db->where('tbl_productions_plan_orders.total_quantity >', 0);
        $this->db->limit(1, $startNest);
        $data['next'] = $this->db->get()->num_rows();
        $data['result'] = 1;
        $data['message'] = lang('success');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getProductionsOrdersDetailsNew($page = 1, $limit = 10)
    {
        $data = [];
        if (!$this->perViewOPD && !$this->perViewOwnOPD) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        $start = ($page - 1) * $limit;
        $name_search = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }
        $search = !empty($data_post['search']) ? $data_post['search'] : null;
        $barcode = !empty($data_post['barcode']) ? $data_post['barcode'] : null;
        $start_date_search = !empty($data_post['start_date_search']) ? $data_post['start_date_search'] : null;
        $end_date_search = !empty($data_post['end_date_search']) ? $data_post['end_date_search'] : null;

        $tbStatus = "(
            SELECT
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                MAX(tbl_stages.name) as stage_name
            FROM tbl_productions_orders_items_stages
            INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
            WHERE tbl_productions_orders_items_stages.active = 1
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
            ORDER BY tbl_productions_orders_items_stages.active DESC, MAX(tbl_productions_orders_items_stages.number) DESC
        ) tb_status";

        $tbOrders = "(
            SELECT
                tbl_orders.id as object_id,
                tbl_orders.reference_no as reference_no,
                tblclients.company as company,
                tbl_orders.date as date
            FROM tbl_orders
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_orders.status_productions_orders != 0
        ) tb_orders";

        $tbBusinessPlan = "(
            SELECT
                tbl_business_plan.id as object_id,
                tbl_business_plan.reference_no reference_no
            FROM tbl_business_plan
            WHERE tbl_business_plan.status_productions_orders != 0
        ) tb_business_plan";

        $priceMaterial = "(
            SELECT
                tbl_suggest_exporting.productions_orders_details_id as pod_id,
                SUM(tbl_suggest_exporting.grand_total) as grand_total
            FROM tbl_suggest_exporting
            WHERE tbl_suggest_exporting.reference_stock IS NOT NULL AND tbl_suggest_exporting.grand_total > 0 
            GROUP BY tbl_suggest_exporting.productions_orders_details_id
        ) tb_price_material";

        $payslips = "(
            SELECT
                tblother_payslips.vouchers_id as pod_id,
                SUM(tblother_payslips.total) as grand_total
            FROM tblother_payslips
            WHERE tblother_payslips.type_vouchers = 9
            GROUP BY tblother_payslips.vouchers_id
        ) tb_payslips";

        $purchaseInternal = "(
            SELECT
                tbl_purchase_internal.pod_id as pod_id,
                SUM(tbl_purchase_internal.grand_total) as grand_total
            FROM tbl_purchase_internal
            WHERE tbl_purchase_internal.grand_total > 0
            GROUP BY tbl_purchase_internal.pod_id
        ) tb_purchase_internal";

        $selectCost = "(COALESCE(tb_price_material.grand_total, 0) + COALESCE(tb_payslips.grand_total, 0) - COALESCE(tb_purchase_internal.grand_total, 0))";
        if (!$this->isAdmin) {
            $selectCost = 0;
        }

        $this->db->select('
            tbl_productions_orders_details.id as id,
            tbl_productions_orders.reference_no as reference_no_order,
            tbl_productions_orders_details.reference_no as reference_no,
            tbl_products.code as item_code,
            tbl_productions_orders_items.quantity as quantity,
            tbl_productions_orders_details.quantity_warehoused as quantity_finished,
            tb_status.stage_name as status,
            IF (tbl_products.images IS NOT NULL, CONCAT("' . base_url('uploads/products/') . '", tbl_products.images), null) as item_image,
            tblbranch.name as branch_name,
            tbl_productions_orders_details.object_type as object_type,
            tb_orders.reference_no as reference_no_orders,
            tb_orders.company as company,
            tb_business_plan.reference_no as reference_no_business_plan,
            ' . $selectCost . ' as total_cost,
        ', FALSE)
            ->from('tbl_productions_orders_details')
            ->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner')
            ->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id', 'inner')
            ->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'inner')
            ->join($tbStatus, 'tb_status.productions_orders_items_id = tbl_productions_orders_items.id', 'left')
            ->join('tblbranch', 'tbl_productions_orders.location_id  = tblbranch.id', 'left')
            ->join($tbOrders, 'tb_orders.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left')
            ->join($tbBusinessPlan, 'tb_business_plan.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');

        if ($this->isAdmin) {
            $this->db->join($priceMaterial, "tb_price_material.pod_id = tbl_productions_orders_details.id", 'left');
            $this->db->join($payslips, "tb_payslips.pod_id = tbl_productions_orders_details.id", 'left');
            $this->db->join($purchaseInternal, "tb_purchase_internal.pod_id = tbl_productions_orders_details.id", 'left');
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_details.reference_no', $search);
            $this->db->or_like('tbl_productions_orders.reference_no', $search);
            $this->db->or_like('tbl_products.name', $search);
            $this->db->or_like('tbl_products.code', $search);
            $this->db->or_like('tb_orders.reference_no', $search);
            $this->db->or_like('tb_business_plan.reference_no', $search);
            $this->db->group_end();
        }

        if (!empty($barcode)) {
            $barcode = explode('-', $barcode);
            $order_item_id = $barcode[0];
            $this->db->where('tbl_productions_orders_items.object_item_type', 'orders');
            $this->db->where('tbl_productions_orders_items.production_plan_item_id', $order_item_id);
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            $this->db->where('tbl_productions_orders_details.date_created >=', $start_date_search);
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            $this->db->where('tbl_productions_orders_details.date_created <=', $end_date_search);
        }

        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        // if (!empty($result)) {
        //     foreach ($result as $key => $value) {
        //     }
        // }
        $data['data'] = $result;

        $startNest = ($page) * $limit;
        $this->db->select('tbl_productions_orders_details.id', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'inner');


        if (!empty($search)) {
            $this->db->join($tbOrders, 'tb_orders.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
            $this->db->join($tbBusinessPlan, 'tb_business_plan.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_details.reference_no', $search);
            $this->db->or_like('tbl_productions_orders.reference_no', $search);
            $this->db->or_like('tbl_products.name', $search);
            $this->db->or_like('tbl_products.code', $search);
            $this->db->or_like('tb_orders.reference_no', $search);
            $this->db->or_like('tb_business_plan.reference_no', $search);
            $this->db->group_end();
        }

        if (!empty($order_item_id)) {
            $this->db->where('tbl_productions_orders_items.object_item_type', 'orders');
            $this->db->where('tbl_productions_orders_items.production_plan_item_id', $order_item_id);
        }

        if (!empty($start_date_search)) {
            $this->db->where('tbl_productions_orders_details.date_created >=', $start_date_search);
        }

        if (!empty($end_date_search)) {
            $this->db->where('tbl_productions_orders_details.date_created <=', $end_date_search);
        }
        $this->db->limit(1, $startNest);
        $data['next'] = $this->db->get()->num_rows();
        $data['result'] = 1;
        $data['message'] = lang('success');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getDetailProductions($id = 0)
    {
        $data = [];
        if (!$this->perViewOPD && !$this->perViewOwnOPD) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }

        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
            }
        }
        $reference_no = !empty($data_post['reference_no']) ? $data_post['reference_no'] : null;
        if (!empty($reference_no)) {
            $pod = get_table_where('tbl_productions_orders_details', ['reference_no' => $reference_no], '', 'row_array', '', 'id');
            if (empty($pod)) {
                $data['result'] = 0;
                $data['message'] = lang('not_data');
                echo json_encode($data);
                die;
            }
            $id = $pod['id'];
        }

        $production_detail = $this->manufactures_model->rowProductionsOrdersByDetail($id);
        $semi_products = $this->manufactures_model->getProductionsOrdersItemsSubByDetail($production_detail['productions_orders_item_id']);
        if (!empty($production_detail['images'])) {
            $production_detail['images'] = base_url('uploads/products/' . $production_detail['images']);
        }

        $priceMaterial = "(
            SELECT
                SUM(tbl_suggest_exporting.grand_total) as total_material
            FROM tbl_suggest_exporting
            WHERE tbl_suggest_exporting.reference_stock IS NOT NULL AND tbl_suggest_exporting.grand_total > 0 AND tbl_suggest_exporting.productions_orders_details_id = $id
        )";
        $totalMaterial = $this->db->query($priceMaterial)->row_array();
        $totalMaterial = !empty($totalMaterial) ? $totalMaterial['total_material'] : 0;

        $purchaseInternal = "(
            SELECT
                SUM(tbl_purchase_internal.grand_total) as total_internal
            FROM tbl_purchase_internal
            WHERE tbl_purchase_internal.grand_total > 0 AND tbl_purchase_internal.pod_id = $id
        )";
        $totalInternal = $this->db->query($purchaseInternal)->row_array();
        $totalInternal = !empty($totalInternal) ? $totalInternal['total_internal'] : 0;
        $data['totalInternal'] = $totalInternal;

        $payslips = "(
            SELECT
                SUM(tblother_payslips.total) as total_payslips
            FROM tblother_payslips
            WHERE tblother_payslips.type_vouchers = 9 AND tblother_payslips.vouchers_id = $id
        )";
        $totalPayslips = $this->db->query($payslips)->row_array();
        $totalPayslips = !empty($totalPayslips) ? $totalPayslips['total_payslips'] : 0;
        $data['totalMaterial'] = $totalMaterial;
        $data['totalPayslips'] = $totalPayslips;
        $data['production_detail'] = $production_detail;
        $data['semi_products'] = $semi_products;

        $object_type = $production_detail['object_type'];
        $object_id = $production_detail['object_id'];
        $production_plan_item_id = $production_detail['production_plan_item_id'];
        if ($object_type == "orders") {
            $this->db->select('tbl_orders.reference_no as reference_no, tblclients.company as company', false);
            $this->db->from('tbl_orders');
            $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
            $this->db->where('tbl_orders.id', $object_id);
            $order = $this->db->get()->row_array();
            $data['order'] = $order;
        } else if ($object_type == "business_plan") {
            $this->db->select('tbl_business_plan.reference_no as reference_no', false);
            $this->db->from('tbl_business_plan');
            $this->db->where('tbl_business_plan.id', $object_id);
            $business_plan = $this->db->get()->row_array();
            $data['business_plan'] = $business_plan;
        }

        $this->db->where('id_order_production_details', $id);
        $this->db->order_by('date_create', 'desc');
        $data['feedback'] = $this->db->get('tblorder_production_details_feedback')->result();
        $folder = 'order_production_details_feedback';
        foreach ($data['feedback'] as $key => $value) {
            $this->db->where('rel_id', $value->id);
            $this->db->where('rel_type', 'feedback_opd');
            $data['feedback'][$key]->feedback = strip_tags($value->feedback);
            $data['feedback'][$key]->file = $this->db->get('tblfiles')->result();
            if (!empty($data['feedback'][$key]->file)) {
                foreach ($data['feedback'][$key]->file as $k => $valFile) {
                    $data['feedback'][$key]->file[$k]->link_file = base_url('uploads/' . $folder . '/' . $value->id . '/' . $valFile->file_name);
                }
            }

            $staff = get_table_where('tblstaff', ['staffid' => $value->create_by], '', 'row_array', '', 'firstname, lastname, staffid, profile_image');
            $fullnameStaff = $staff['firstname'] . ' ' . $staff['lastname'];
            $staffImage = '';
            if (!empty($staff['profile_image'])) {
                $staffImage = base_url('uploads/staff_profile_images/' . $staff['staffid'] . '/small_' . $staff['profile_image']);
            }
            $data['feedback'][$key]->fullnameStaff = $fullnameStaff;
            $data['feedback'][$key]->staffImage = $staffImage;
        }

        $data['cpnvl'] = (float)$totalMaterial - (float)$totalInternal;
        $data['cpk'] = (float)$totalPayslips;
        $data['tongcp'] = (float)$totalMaterial - (float)$totalInternal + (float)$totalPayslips;

        $dtProcess = showProcessDetailProductions($production_detail['productions_orders_item_id'], $object_type, $production_plan_item_id, $isData = true);
        $data['process'] = $dtProcess['dataProcess'];

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getExportMaterialsWarehouses($page = 1, $limit = 10)
    {
        $data = [];
        if (!$this->perViewOPD && !$this->perViewOwnOPD) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        $start = ($page - 1) * $limit;
        $name_search = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }
        $pod_id = !empty($data_post['pod_id']) ? $data_post['pod_id'] : null;
        $search = !empty($data_post['search']) ? $data_post['search'] : null;

        $tbItems = "
            SELECT tb_cs.*
            FROM (
            (
                SELECT
                    tbl_suggest_exporting_items.item_id as item_id,
                    tbl_suggest_exporting_items.type_item as type_item,
                    tbl_materials.code as item_code,
                    tbl_materials.name as item_name,
                    tbl_materials.images as images,
                    SUM(tbl_suggest_exporting_items.quantity_exchange) as quantity
                FROM tbl_suggest_exporting
                INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                INNER JOIN tbl_materials ON tbl_suggest_exporting_items.item_id = tbl_materials.id
                WHERE tbl_suggest_exporting_items.type_item = 'materials' AND tbl_suggest_exporting.productions_orders_details_id = " . $this->db->escape($pod_id) . "
                GROUP BY tbl_suggest_exporting_items.item_id
            )
            UNION ALL
            (
                SELECT
                    tbl_suggest_exporting_items.item_id as item_id,
                    tbl_suggest_exporting_items.type_item as type_item,
                    tbl_tools_supplies.code as item_code,
                    tbl_tools_supplies.name as item_name,
                    tbl_tools_supplies.images as images,
                    SUM(tbl_suggest_exporting_items.quantity_exchange) as quantity
                FROM tbl_suggest_exporting
                INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                INNER JOIN tbl_tools_supplies ON tbl_suggest_exporting_items.item_id = tbl_tools_supplies.id
                WHERE tbl_suggest_exporting_items.type_item = 'tools_supplies' AND tbl_suggest_exporting.productions_orders_details_id = " . $this->db->escape($pod_id) . "
                GROUP BY tbl_suggest_exporting_items.item_id
            )
            UNION ALL
            (
                SELECT
                    tbl_suggest_exporting_items.item_id as item_id,
                    tbl_suggest_exporting_items.type_item as type_item,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    tbl_products.images as images,
                    SUM(tbl_suggest_exporting_items.quantity_exchange) as quantity
                FROM tbl_suggest_exporting
                INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                INNER JOIN tbl_products ON tbl_suggest_exporting_items.item_id = tbl_products.id
                WHERE tbl_suggest_exporting_items.type_item NOT IN ('tools_supplies', 'materials') AND tbl_suggest_exporting.productions_orders_details_id = " . $this->db->escape($pod_id) . "
                GROUP BY tbl_suggest_exporting_items.item_id
            )
        ) tb_cs
        params_where
        LIMIT prams_limit 
        ";
        if (!empty($search)) {
            $search = $this->db->escape_like_str($search);
            $tbItems = str_replace('params_where', "WHERE (
                tb_cs.item_code like '%$search%' OR tb_cs.item_name like '%$search%'
            )", $tbItems);
        } else {
            $tbItems = str_replace('params_where', "", $tbItems);
        }
        $tbSelectAll = str_replace('prams_limit', "$start, $limit", $tbItems);

        $result = $this->db->query($tbSelectAll)->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $aRow) {
                $type_item = $aRow['type_item'];
                $images = $aRow['images'];

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                } else if ($type_item == "materials") {
                    $images = base_url('uploads/materials/' . $images);
                } else if ($type_item == "tools_supplies") {
                    $images = base_url('uploads/tools_supplies/' . $images);
                } else {
                    $images = base_url('uploads/products/' . $images);
                }

                $result[$key]['images'] = $images;
            }
        }
        $data['data'] = $result;

        $startNest = ($page) * $limit;
        $tbSelectNext = str_replace('prams_limit', "$startNest, 1", $tbItems);
        $next = $this->db->query($tbSelectNext)->num_rows();
        $data['next'] = $next;
        $data['result'] = 1;
        $data['message'] = lang('success');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function agreeProcess()
    {
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $pois_id = $data_post['pois_id'];
        $status = $data_post['status'];
        $pod_id = $data_post['pod_id'];

        $active = !empty($status) ? 1 : 0;
        $staff_active = $this->staffid;
        $date_active = date('Y-m-d H:i:s');
        $type = !empty($data_post['type']) ? $data_post['type'] : '';

        if ($active == 1 && $type != "begins_production") {
            $this->db->select('
                tbl_productions_orders_items_stages.id as id,
                tbl_stages.name as stage_name, 
                tbl_productions_orders_items_stages.active as active, 
                tbl_productions_orders_items_stages.date_active as date_active,
                tbl_productions_orders_items_stages.staff_active as staff_active,
                CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                tblstaff.profile_image as profile_image,
                tbl_stages.status_qc as status_qc,
                tbl_stages.id as stage_id,
                tbl_productions_orders_items_stages.final_stage as final_stage,
                tbl_stages.type as type,
                tbl_productions_orders_items_stages.begin_productions as begin_productions,
                tbl_productions_orders_items_stages.date_productions as date_productions,
                tbl_productions_orders_items_stages.staff_productions as staff_productions,
                CONCAT(staff_productions.firstname, " ", staff_productions.lastname) as staff_name_productions,
                staff_productions.profile_image as profile_image_productions,
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                tbl_productions_orders_items_stages.number as number
            ', false);
            $this->db->from('tbl_productions_orders_items_stages');
            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
            $this->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
            $this->db->join('tblstaff staff_productions', 'staff_productions.staffid = tbl_productions_orders_items_stages.staff_productions', 'left');
            $this->db->where('tbl_productions_orders_items_stages.id', $pois_id);
            $dtPois = $this->db->get()->row_array();
            if (!empty($dtPois)) {

                $qc = $this->manufactures_model->isQCPre(0, $dtPois['productions_orders_items_id'], 0, $dtPois['number']);
                if ($qc == 2 || $qc == 3) {
                    $data['result'] = 0;
                    $data['message'] = lang('Có mặt hàng chưa QC công đoạn trước hoặc chưa đạt');
                    echo json_encode($data); die;
                }

                $this->db->select('count(tbl_productions_orders_items_stages.id) as ct', false);
                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $dtPois['productions_orders_items_id']);
                $this->db->where('tbl_productions_orders_items_stages.number <', $dtPois['number']);
                $this->db->where_in('tbl_stages.type', [2]);
                $ct = $this->db->get()->row_array()['ct'];

                if ($dtPois['type'] || !empty($ct)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Bạn không được phép hoàn thành giai đoạn này');
                    echo json_encode($data);
                    die;
                }
            }
        }


        $data['result'] = 0;
        $data['message'] = lang('fail');

        $pois = $this->manufactures_model->getProductionsOrderItemsStagesById($pois_id);
        $stage_id = $pois['stage_id'];
        $productions_orders_items_id = $pois['productions_orders_items_id'];
        if ($active == $pois['active']) {
            $data['result'] = 0;
            $data['message'] = lang('Trạng thái hiện tại đã thay đổi');
            echo json_encode($data);
            die;
        }

        if ($status == 0) {
            $queryQC = "(
                SELECT
                    count(tbl_check_quality_items.id) as ct_qc
                FROM tbl_check_quality_items
                INNER JOIN tbl_check_quality ON tbl_check_quality.id = tbl_check_quality_items.check_quality_id
                LEFT JOIN tblstaff ON tblstaff.staffid = tbl_check_quality.created_by
                WHERE tbl_check_quality_items.pod_id = " . $this->db->escape($pod_id) . " AND tbl_check_quality_items.id_stage = '$stage_id'
            )";
            $isQC = $this->db->query($queryQC)->row_array()['ct_qc'];
            if (!empty($isQC)) {
                $data['result'] = 0;
                $data['message'] = lang('Đã có QC giai đoạn này không thể bỏ duyệt');
                echo json_encode($data);
                die;
            }

            //is suggest and purchase products
            $this->db->select('tbl_suggest_exporting.id as id');
            $this->db->from('tbl_suggest_exporting');
            $this->db->where('tbl_suggest_exporting.productions_orders_details_id', $pod_id);
            $this->db->where('tbl_suggest_exporting.pois_id', $pois_id);
            $this->db->where('tbl_suggest_exporting.type <', 10);
            $suggest_exporting = $this->db->get()->result_array();

            $this->db->select('tbl_purchase_products.*');
            $this->db->from('tbl_purchase_products');
            $this->db->where('tbl_purchase_products.productions_orders_details_id', $pod_id);
            $this->db->where('tbl_purchase_products.pois_id', $pois_id);
            $this->db->where('tbl_purchase_products.type <', 10);
            $purchase_products = $this->db->get()->result_array();
            if (!empty($purchase_products)) {
                foreach ($purchase_products as $key => $value) {
                    $test_quantity = get_table_where('tblwarehouse_product', array('import_id' => $value['id'], 'quantity_export >' => 0, 'type_export ' => 18), '', 'row');
                    if (!empty($test_quantity)) {
                        $data['result'] = 2;
                        $data['message'] = lang('ch_quantity_nd');
                        echo json_encode($data);
                        die;
                    }
                }
            }


            $staff_active = 0;
            $date_active = null;
        }

        if ($type == "begins_production") {
            //đang sản xuất
            $up = $this->manufactures_model->updateProductionsOrderItemsStages($pois_id, [
                'begin_productions' => $active,
                'staff_productions' => $staff_active,
                'date_productions' => $date_active,
            ]);
            if (!empty($up)) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            }
        } else {
            $up = $this->manufactures_model->updateProductionsOrderItemsStages($pois_id, [
                'active' => $active,
                'staff_active' => $staff_active,
                'date_active' => $date_active,
            ]);
            if (!empty($up)) {
                $pod = $this->manufactures_model->rowProductionsOrdersDetais($pod_id);
                //remove suggest exporting
                if (!empty($suggest_exporting)) {
                    foreach ($suggest_exporting as $key => $value) {
                        $items = get_table_where('tbl_suggest_exporting_items', array('suggest_exporting_id' => $value['id']));
                        $_data = array(
                            'warehouseman_id' => 0,
                            'date_warehouseman' => NULL
                        );
                        $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $value['id']));
                        if ($success) {
                            $this->stock_model->increaseadWarehouse($value['id'], $items, 0);
                            $this->manufactures_model->deleteSuggestExportingById($value['id']);
                            $this->manufactures_model->deleteSuggestExportingItems($value['id']);
                        }
                    }
                }

                //remove purchase products
                if (!empty($purchase_products)) {
                    foreach ($purchase_products as $key => $value) {
                        $_data = array(
                            'status' => 'un_approved',
                            'warehouseman_id' => 0,
                            'date_warehouseman' => NULL
                        );
                        $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $value['id']));
                        if ($success) {
                            $this->stock_model->increaseadWarehouse_purchase_products($value['id']);
                            $this->stock_model->deletePurchaseProducts($value['id']);
                            $this->stock_model->deletePurchaseProductItems($value['id']);
                            $this->manufactures_model->deletePurchaseProductPoisub($value['id']);
                            if (!empty($value['final_stage'])) {
                                $this->manufactures_model->updateQuantityWarehoused($value['productions_orders_details_id'], $value['total_quantity'], $minus = 1);
                            }
                        }
                    }
                }

                if ($status == 1 && $pois['final_stage']) {
                    // noti_custom('pod', $pod_id, $staff_active, $pois_id, 'finishedPOD');
                }

                if ($status == 1) {
                    noti_custom('qc_po_detail', $pod_id, $staff_active, 0, 'finishedQCPODetail', [
                        'arrPOIS' => [$pois_id],
                        'pod_id' => $pod_id,
                        'stage_id' => $stage_id,
                    ]);
                }

                $data['result'] = 1;
                $data['message'] = lang('success');
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    public function loadAllSemiProducts()
    {
        $data = [];
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $data['pod_id'] = $data_post['pod_id'];
        $data['type'] = $data_post['type'];
        $data['pois_id'] = $data_post['pois_id'];
        $data['dtPois'] = $this->manufactures_model->getProductionsOrdersItemsStagesById($data['pois_id']);
        $data['warehouses'] = $this->stock_model->getWarehouses(false, [WAREHOUSES_CAPACITY], $this->staffid);
        $data['productions_plan'] = $this->manufactures_model->getProductionsPlanPOD($data['pod_id']);

        $pod_id = $data['pod_id'];
        $pois_id = $data['pois_id'];
        $type = $data['type'];
        $production_plan_id = $data['productions_plan']['id'];

        $quantity = !empty($data['quantity']) ? number_unformat($data['quantity'], false) : 0;
        $actions = !empty($data['actions']) ? $data['actions'] : '';
        $productions_orders_subs = $this->manufactures_model->loadDataSemiProducts($pod_id, $production_plan_id, $quantity, $actions);
        $data['items'] = $productions_orders_subs['productions_orders_subs'];
        $data['isWarehouses'] = $productions_orders_subs['isWarehouses'];
        $data['message_is_warehouses'] = lang('tnh_enough_semi_product_plan_materials');
        echo json_encode($data);
    }

    function searchWarehousers()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
            }
        }

        $term = !empty($data_post['term']) ? $data_post['term'] : '';
        $limit = get_option('select2_limit');
        $item_cs_id = $data_post['item_id'];
        $production_plan_id = !empty($data_post['production_plan_id']) ? $data_post['production_plan_id'] : null;

        $item_id = 0;
        $item_type = '';
        if (!empty($item_cs_id)) {
            $item_cs_id = explode('__', $item_cs_id);
            $item_type = $item_cs_id[0];
            $item_id = $item_cs_id[1];
            if ($item_type == "materials") {
                $item_type = "nvl";
            } else if ($item_type == "tools_supplies") {
                $item_type = "tools";
            } else {
                $item_type = "product";
            }
        }

        $results = [];
        $this->db->select('tblwarehouse.id, tblwarehouse.name');
        $this->db->from('tblwarehouse');
        if (!empty($item_id)) {
            $this->db->where('(
                EXISTS (
                    SELECT tblwarehouse_items.id
                    FROM tblwarehouse_items
                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                )
            )');
        }
        $warehouses = $this->db->get()->result_array();
        if (!empty($warehouses)) {
            foreach ($warehouses as $key => $value) {
                $warehouse_id = $value['id'];

                $tbQuantityWarehouses = '(
                    SELECT
                        tblwarehouse_items.localtion as localtion_id,
                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                    FROM tblwarehouse_items
                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                    GROUP BY tblwarehouse_items.localtion
                ) tb_quantity_warehouses';

                $this->db->select('
                    CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                    CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                    tb_quantity_warehouses.product_quantity as product_quantity
                ', false);
                $this->db->from('tbllocaltion_warehouses');
                $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                if (!empty($production_plan_id)) {
                    $this->db->group_start();
                    $this->db->where('tbllocaltion_warehouses.productions_plan_id', $production_plan_id);
                    $this->db->or_where('tbllocaltion_warehouses.productions_plan_id', 0);
                    $this->db->group_end();
                }
                if ($term) {
                    $this->db->group_start();
                    $this->db->like('tbllocaltion_warehouses.name', $term);
                    $this->db->group_end();
                }
                $this->db->limit($limit);
                $location_warehouses = $this->db->get()->result_array();
                $results[] = ['text' => $value['name'], 'children' => $location_warehouses];
            }
        }
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function handlingSemiProducts()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
            }
        }

        $warehouses_semi_product = $data_post['warehouses_semi_product'];
        if (empty($warehouses_semi_product)) {
            $data['result'] = 0;
            $data['message'] = 'Vui lòng chọn kho bán thành phẩm';
            echo json_encode($data, JSON_UNESCAPED_UNICODE); die;
        }

        $isWarehouses = !empty($data_post['isWarehouses']) ? $data_post['isWarehouses'] : '';
        $sp_actions = !empty($data_post['actions']) ? $data_post['actions'] : '';
        $finished_productions = !empty($data_post['finished_productions']) ? $data_post['finished_productions'] : 0;
        $sp_type = 1;
        $sp_pod_id = $data_post['pod_id'];
        $sp_pois_id = $data_post['pois_id'];
        $use_productions_plan = 1;

        $dtPois = get_table_where('tbl_productions_orders_items_stages', ['id' => $sp_pois_id], "", "row_array", 'stage_id, active, productions_orders_items_id, number');
        $active = $dtPois['active'];

        $qc = $this->manufactures_model->isQCPre(0, $dtPois['productions_orders_items_id'], 0, $dtPois['number']);
        if ($qc == 2 || $qc == 3) {
            $data['result'] = 0;
            $data['message'] = lang('Có mặt hàng chưa QC công đoạn trước hoặc chưa đạt');
            echo json_encode($data); die;
        }

        $sp_cqi_id = !empty($data_post['cqi_id']) ? $data_post['cqi_id'] : 0;
        $stage_id = $dtPois['stage_id'];
        $qc_remake_id = 0;
        $typePurchase = 2;
        $typeSuggestExporting = 5;
        if ($sp_actions == 'qc') {
            $dtQcRemake = $this->manufactures_model->getQCRemakeCQIId($sp_cqi_id);
            if (!empty($dtQcRemake) && $dtQcRemake['status']) {
                $data['result'] = 0;
                $data['message'] = lang('Giai đoạn sản xuất lại này đã được hoàn thành');
                echo json_encode($data); die;
            }
            $qc_remake_id = $dtQcRemake['id'];
            $typePurchase = 10;
            $typeSuggestExporting = 10;
        } else {
            if ($active) {
                $data['result'] = 0;
                $data['message'] = lang('giai đoạn này đã được hoàn thành');
                echo json_encode($data);
                die;
            }
        }

        $localtion_semi_product = $this->site_model->getLocationPOD($sp_pod_id, $warehouses_semi_product);
        $dataItems = [];
        $arrSaveBom = [];

        $total_quantity_purchases = 0;
        $count_quantity_purchases = 0;

        $total_quantity_se = 0;
        $total_quantity_exchange_se = 0;
        $count_quantity_se = 0;

        $items = $data_post['items'];
        $isErrors = true;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $item_type_sp = "products";
                $arr_id_sp = explode('__', $value['id']);
                $type_order_sp = $arr_id_sp[0];
                $item_id_sp = $arr_id_sp[1];
                $info = $this->products_model->rowProduct($item_id_sp);
                if (empty($info)) {
                    if (empty($info_mt)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không tìm thấy dữ liệu BTP');
                        echo json_encode($data);
                        die;
                    }
                }
                $item_code_sp = $info['code'];
                $item_name_sp = $info['name'];

                $quantity_exchange_sp = number_unformat($value['quantity_exchange']);
                $quantity_single_sp = number_unformat($value['quantity_single']);
                $quantity_semi_product = number_unformat($value['quantity_semi_product']);
                $unit_id_sp = $value['unit_id'];
                $unit_parent_id_sp = $value['unit_parent_id'];

                $materials = !empty($value['subItems']) ? $value['subItems'] : null;
                $is_save_dm = !empty($value['is_save_dm']) ? $value['is_save_dm'] : 0;
                $arrMaterials = [];
                $arrMaterialsBOM = [];
                if (!empty($materials)) {
                    $isErrors = false;
                    foreach ($materials as $k => $v) {
                        $quantity_exchange_mt = number_unformat($v['quantity_exchange']);
                        if (empty($quantity_exchange_mt)) $quantity_exchange_mt = 1;
                        $quantity_single_mt = number_unformat($v['quantity_single']);
                        $arr_item_cs_id_mt = explode('__', $v['item_cs_id']);
                        $item_id_mt = $arr_item_cs_id_mt[1];
                        $item_type_mt = $arr_item_cs_id_mt[0];
                        $unit_id = $v['unit_id'];
                        $unit_parent_id = $v['unit_parent_id'];
                        if ($item_type_mt == "materials") {
                            $info_mt = $this->items_model->rowMaterial($item_id_mt);
                        } else if ($item_type_mt == "tools_supplies") {
                            $info_mt = $this->tools_supplies_model->rowToolsSupplies($item_id_mt);
                        } else {
                            $info_mt = $this->products_model->rowProduct($item_id_mt);
                        }

                        if (empty($info_mt)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Không tìm thấy dữ liệu NVL');
                            echo json_encode($data);
                            die;
                        }

                        // $quantity_materials_mt = $v['quantity_materials'];
                        $unit_id_mt = $v['unit_id'];
                        $unit_parent_id_mt = $v['unit_parent_id'];
                        $warehouses_items_mt = $v['warehouses_items'];
                        if (empty($warehouses_items_mt)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Xin vui lòng chọn kho NVL');
                            echo json_encode($data);
                            die;
                        } else if ($warehouses_items_mt) {
                            foreach ($warehouses_items_mt as $kW => $vW) {
                                if (empty($vW['warehouse_id'])) {
                                    $data['result'] = 0;
                                    $data['message'] = lang('Xin vui lòng chọn kho NVL');
                                    echo json_encode($data);
                                    die;
                                }
                                $vWW = explode('__', $vW['warehouse_id']);
                                $warehouse_item_id = $vWW[0];
                                $location_id = $vWW[1];
                                $quantity_items = !empty($vW['quantity_items']) ? number_unformat($vW['quantity_items']) : 0;
                                if (empty($quantity_items)) continue;
                                $quantity_exchange_se =  $quantity_items / $quantity_exchange_mt;
                                $quantityW = $this->manufactures_model->getTotalQuantitW($item_id_mt, $item_type_mt, $location_id, $warehouse_item_id);
                                if ($quantity_exchange_se > $quantityW['total_quantity']) {
                                    $data['result'] = 0;
                                    $data['message'] = 'Mã '.$info_mt['code'].' không đủ số lượng trong kho để xuất';
                                    echo json_encode($data); die;
                                }

                                $arrMaterials[] = [
                                    'type_item' => $item_type_mt,
                                    'item_id' => $item_id_mt,
                                    'item_code' => $info_mt['code'],
                                    'item_name' => $info_mt['name'],
                                    'unit_id' => $unit_id_mt,
                                    'quantity_export' => $quantity_items,
                                    'unit_parent_id' => $unit_parent_id_mt,
                                    'number_exchange' => $quantity_exchange_mt,
                                    'quantity_exchange' => $quantity_exchange_se,
                                    'location_id' => $location_id,
                                    'warehouse_item_id' => $warehouse_item_id,
                                ];
                                $total_quantity_se += $quantity_items;
                                $total_quantity_exchange_se += $quantity_exchange_se;
                                $count_quantity_se++;
                            }
                        }

                        if ($is_save_dm) {
                            $arrMaterialsBOM[] = [
                                'type' => $item_type_mt,
                                'item_id' => $item_id_mt,
                                'unit_id' => $unit_id_mt,
                                'quantity' => $quantity_single_mt,
                            ];
                        }
                    }
                }

                if (!empty($is_save_dm) && !empty($arrMaterialsBOM)) {
                    $arrSaveBom[$key] = [
                        'product_id' => $item_id_sp,
                        'arrMaterialsBOM' => $arrMaterialsBOM
                    ];
                }

                $poisub_id = $value['poisub_id'];
                $arrPOISub = [];
                if (!empty($poisub_id)) {
                    $tempArrPOISub = explode(',', $poisub_id);
                    foreach ($tempArrPOISub as $k => $v) {
                        $arrPOISub[$k]['poisub_id'] = $v;
                    }
                }

                $dataItems[] = [
                    'productions_orders_details_id' => $sp_pod_id,
                    'type_item' => $item_type_sp,
                    'item_id' => $item_id_sp,
                    'location_id' => $localtion_semi_product,
                    'item_code' => $item_code_sp,
                    'item_name' => $item_name_sp,
                    'quantity' => $quantity_semi_product,
                    'quantity_exchange' => $quantity_exchange_sp,
                    'quantity_single' => $quantity_single_sp,
                    'quantity_semi_product' => $quantity_semi_product,
                    'type_order' => $type_order_sp,
                    'arrPOISub' => $arrPOISub,
                    'arrMaterials' => $arrMaterials
                ];
                $total_quantity_purchases += $quantity_semi_product;
                $count_quantity_purchases++;
            }
        }

        if (!empty($isErrors) && !$isWarehouses) {
            $data['result'] = 0;
            $data['message'] = lang('Không có NVL xuất ra');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }

        if (empty($dataItems) && !$isWarehouses) {
            $data['result'] = 0;
            $data['message'] = lang('Không có dữ liệu');
            echo json_encode($data); die;
        }

        $dateGerenal = date('Y-m-d H:i:s');
        $staffGerenal = $this->staffid;

        if (!empty($dataItems)) {
            $reference_purchase = getReference('purchase_products');
            $purchases = [
                'reference_no' => $reference_purchase,
                'date' => $dateGerenal,
                'productions_orders_details_id' => $sp_pod_id,
                'warehouse_id' => $warehouses_semi_product,
                'count_items' => $count_quantity_purchases,
                'total_quantity' => $total_quantity_purchases,
                'created_by' => $staffGerenal,
                'date_created' => $dateGerenal,
                'status' => 'un_approved',
                'pois_id' => $sp_pois_id,
                'type' => $typePurchase,
                'sp_type' => $sp_type,
                'cqi_id' => $sp_cqi_id
            ];

            $save_and_warehouse = 1;
            $export_name = 'Xuất kho NVL';
            $reference_suggest_exporting = getReference('stock');
            $suggest_exporting = [
                'productions_orders_details_id' => $sp_pod_id,
                'reference_no' => null,
                'reference_stock' => $reference_suggest_exporting,
                'date' => $dateGerenal,
                'export_name' => $export_name,
                'note' => '',
                'status' => 'un_approved',
                'total_quantity' => $total_quantity_se,
                'count_items' => $count_quantity_se,
                'total_quantity_exchange' => $total_quantity_exchange_se,
                'created_by' => $staffGerenal,
                'date_created' => $dateGerenal,
                'convert_stock_by' => $staffGerenal,
                'date_convert_stock' => $dateGerenal,
                'status_stock' => 'approved',
                'date_stock' => $dateGerenal,
                'user_stock' => $staffGerenal,
                'type' => $typeSuggestExporting,
                'date_convert_stock' => $dateGerenal,
                'warehouse_id' => 0,
                'save_and_warehouse' => $save_and_warehouse,
                'pois_id' => $sp_pois_id,
                'use_productions_plan' => $use_productions_plan,
                'cqi_id' => $sp_cqi_id
            ];

            $errors = '';
            // print_arrays($dataItems);
            $purchase_product_id = $this->stock_model->insertPurchaseProducts($purchases);
            if (!empty($purchase_product_id)) {
                updateReference('purchase_products');
                if ($sp_actions != 'qc') {
                    if ($sp_actions != "enter_semi_products" || ($sp_actions == "enter_semi_products" && !empty($finished_productions))) {
                        $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                            'active' => 1,
                            'staff_active' => $staffGerenal,
                            'date_active' => $dateGerenal,
                        ]);
                    }
                } else {
                    $op = [
                        'pod_id' => $sp_pod_id,
                        'stage_id' => $stage_id,
                        'check_quality_items_id' => $sp_cqi_id,
                        'pois_id' => $sp_pois_id,
                        'type' => 1
                    ];
                    if (!empty($finished_productions)) {
                        $op['status'] = 1;
                        $op['staff_status'] = $staffGerenal;
                        $op['status_date'] = $dateGerenal;
                    }
                    if ($qc_remake_id) {
                        $up = $this->manufactures_model->updateQCRemake($qc_remake_id, $op);
                    } else {
                        $ins = $this->manufactures_model->insertQCRemake($op);
                    }
                }

                $suggest_exporting['purchase_product_id'] = $purchase_product_id;
                $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($suggest_exporting);
                if ($suggest_exporting_id) {
                    updateReference('stock');
                }

                if (!empty($dataItems)) {
                    foreach ($dataItems as $key => $value) {
                        $arrPOISub = $value['arrPOISub'];
                        $arrMaterials = $value['arrMaterials'];
                        unset($value['arrPOISub']);
                        unset($value['arrMaterials']);
                        $value['purchase_product_id'] = $suggest_exporting['purchase_product_id'];
                        $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                        if ($purchase_product_item_id) {
                            if (!empty($arrPOISub)) {
                                foreach ($arrPOISub as $k => $v) {
                                    $arrPOISub[$k]['purchase_product_id'] = $purchase_product_id;
                                    $arrPOISub[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                }
                            }

                            if (!empty($arrMaterials)) {
                                foreach ($arrMaterials as $k => $v) {
                                    $arrMaterials[$k]['suggest_exporting_id'] = $suggest_exporting_id;
                                    $arrMaterials[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                }
                            }
                            if (!empty($arrMaterials)) {
                                $this->manufactures_model->insertBatchSuggestExportingItems($arrMaterials);
                            }
                        }

                        if (!empty($arrPOISub)) {
                            $this->manufactures_model->insertBatchPurchaseProductPoisub($arrPOISub);
                        }
                    }
                }

                //suggest exporting
                if (!empty($save_and_warehouse)) {
                    $id = $suggest_exporting_id;
                    $_data = array(
                        'warehouseman_id' => $staffGerenal,
                        'date_warehouseman' => date('Y-m-d H:i:s')
                    );

                    if (!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                        $errors = lang('test_quantyti_time_return');
                    } else {
                        $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                        if ($success) {
                            log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                            $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                            $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                            $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                            $content = lang('tnh_his_warehouse_exporting_producion');
                            $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                            $content = str_replace('{$2}', $pod['reference_no'], $content);

                            insertActivityLog([
                                'type_parent_obj' => 'exporting_producion',
                                'table_obj' => 'tbl_suggest_exporting',
                                'id_obj' => $id,
                                'name_obj' => $suggest_exporting['reference_stock'],
                                'content' => $content,
                                'actions' => 'warehouse'
                            ], $this->staffid);
                        }
                    }
                }

                $pod = $this->manufactures_model->rowProductionsOrdersDetais($sp_pod_id);
                $content = lang('tnh_his_add_exporting_producion');
                $content = str_replace('{$1}', $reference_suggest_exporting, $content);
                $content = str_replace('{$2}', $pod['reference_no'], $content);

                insertActivityLog([
                    'type_parent_obj' => 'exporting_producion',
                    'table_obj' => 'tbl_suggest_exporting',
                    'id_obj' => $suggest_exporting_id,
                    'name_obj' => $reference_suggest_exporting,
                    'content' => $content,
                    'actions' => 'add'
                ], $staffGerenal);

                //warehouses purchase products
                if (!empty($save_and_warehouse)) {
                    $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_id);
                    $_data = array(
                        'status' => 'approved',
                        'warehouseman_id' => $staffGerenal,
                        'date_warehouseman' => date('Y-m-d H:i:s')
                    );
                    $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_id));
                    if ($success) {
                        log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_id);
                        $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_id);

                        //activity log
                        if (!empty($purchaseProduct['productions_orders_details_id'])) {
                            $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                            $content = lang('tnh_his_warehouse_purchase_product_pod');
                            $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                            $content = str_replace('{$2}', $pod['reference_no'], $content);
                        } else {
                            $content = lang('tnh_his_warehouse_purchase_product');
                            $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'purchase_products',
                            'table_obj' => 'tbl_purchase_products',
                            'id_obj' => $purchase_product_id,
                            'name_obj' => $purchaseProduct['reference_no'],
                            'content' => $content,
                            'actions' => 'warehouse'
                        ], $staffGerenal);
                        //end activity log
                    }
                }

                $content = lang('tnh_his_add_purchase_product');
                $content = str_replace('{$1}', $reference_purchase, $content);
                insertActivityLog([
                    'type_parent_obj' => 'purchase_products',
                    'table_obj' => 'tbl_purchase_products',
                    'id_obj' => $purchase_product_id,
                    'name_obj' => $reference_purchase,
                    'content' => $content,
                    'actions' => 'add'
                ], $staffGerenal);

                //handling bom
                if (!empty($arrSaveBom)) {
                    $this->products_model->handlingBOMPOD($arrSaveBom);
                }
                $data['result'] = 1;
                $data['message'] = lang('success');
                $data['error'] = $errors;
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }

        if ($isWarehouses) {
            if ($sp_actions != "enter_semi_products" || ($sp_actions == "enter_semi_products" && !empty($finished_productions))) {
                $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                    'active' => 1,
                    'staff_active' => $staffGerenal,
                    'date_active' => $dateGerenal,
                ]);
                $data['process'] = showProcessDetailProductions($dtPois['productions_orders_items_id'])['process'];
                $data['result'] = 1;
                $data['message'] = lang('success');
            }
        }

        noti_custom('qc_po_detail', $sp_pod_id, $staffGerenal, 0, 'finishedQCPODetail', [
            'arrPOIS' => [$sp_pois_id],
            'pod_id' => $sp_pod_id,
            'stage_id' => $stage_id,
        ]);

        echo json_encode($data);
    }

    public function searchSemiProductsPOD()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
            }
        }

        $term = !empty($data_post['term']) ? $data_post['term'] : '';
        $pod_id = !empty($data_post['pod_id']) ? $data_post['pod_id'] : 0;
        $limit = get_option('select2_limit');

        $this->db->select('
            CONCAT("pod__", tbl_products.id) as id,
            tbl_products.id as product_id,
            CONCAT(tbl_products.name, "(", tbl_products.code,")") as text,
            tbl_products.name as item_name,
            tbl_products.code as item_code,
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items_sub.item_id');
        $this->db->where('tbl_productions_orders_details.id', $pod_id);
        $this->db->where('tbl_productions_orders_items_sub.type', 'semi_products');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $term);
            $this->db->or_like('tbl_products.name', $term);
            $this->db->group_end();
        }
        $this->db->group_by('tbl_products.id');
        $productions_orders_sub = $this->db->get()->result_array();
        $data['results'][] = ['type' => 'pod', 'text' => lang('tnh_semi_products_in_order'), 'children' => $productions_orders_sub];

        $arrIDRemove = [];
        if (!empty($productions_orders_sub)) {
            foreach ($productions_orders_sub as $key => $value) {
                $arrIDRemove[] = $value['product_id'];
            }
        }

        //
        $this->db->select('
            CONCAT("outside__", tbl_products.id) as id,
            tbl_products.id as product_id,
            CONCAT(tbl_products.name, "(", tbl_products.code,")") as text,
            tbl_products.name as item_name,
            tbl_products.code as item_code,
        ');
        $this->db->from('tbl_products');
        $this->db->where('tbl_products.type_products', 'semi_products');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $term);
            $this->db->or_like('tbl_products.name', $term);
            $this->db->group_end();
        }
        if (!empty($arrIDRemove)) {
            $this->db->where_not_in('tbl_products.id', $arrIDRemove);
        }
        $this->db->limit($limit);
        $semi_products = $this->db->get()->result_array();
        $data['results'][] = ['type' => 'outside', 'text' => lang('tnh_additional_semi_products'), 'children' => $semi_products];

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function rowSemiProductPOD()
    {
        $data = [];
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $data['pod_id'] = $data_post['pod_id'];
        $data['type'] = !empty($data_post['type']) ? $data_post['type'] : 1;
        $data['pois_id'] = $data_post['pois_id'];
        $data['dtPois'] = $this->manufactures_model->getProductionsOrdersItemsStagesById($data['pois_id']);
        $data['productions_plan'] = $this->manufactures_model->getProductionsPlanPOD($data['pod_id']);

        $pod_id = $data['pod_id'];
        $pois_id = $data['pois_id'];
        $type = $data['type'];
        $product_id = $data_post['product_id'];
        $arrProduct = explode('__', $product_id);
        $product_id = $arrProduct[1];
        $product_type = $arrProduct[0];
        $production_plan_id = $data['productions_plan']['id'];

        if ($product_type == "pod") {
            $this->db->select('
                CONCAT("pod__", tbl_products.id) as id,
                tbl_products.id as product_id,
                tbl_products.name as name,
                tbl_products.code as code,
                GROUP_CONCAT(DISTINCT tbl_productions_orders_items_sub.id) as poisub_id,
                tbl_productions_orders_items_sub.quantity_exchange as quantity_exchange,
                tbl_productions_orders_items_sub.quantity_single as quantity_single,
                SUM(tbl_productions_orders_items_sub.quantity_primary) as quantity_primary,
                SUM(tbl_productions_orders_items_sub.quantity) as quantity,
                tbl_products.images as images,
                tbl_productions_orders_items_sub.unit_id as unit_id,
                tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
                tbl_products.versions as versions
            ', false);
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items_sub.item_id');
            $this->db->where('tbl_productions_orders_details.id', $pod_id);
            $this->db->where('tbl_productions_orders_items_sub.type', 'semi_products');
            $this->db->where('tbl_products.id', $product_id);
            $this->db->group_by('tbl_products.id');
            $productions_orders_sub = $this->db->get()->row_array();
            if (!empty($productions_orders_sub)) {
                if (!empty($productions_orders_sub['images'])) {
                    $productions_orders_sub['images'] = base_url('uploads/products/' . $productions_orders_sub['images']);
                }
                $poisub_id = $productions_orders_sub['poisub_id'];

                $query = "(
                    SELECT
                        CONCAT(sub1.type, '__', sub1.item_id) as item_cs_id,
                        sub1.type as item_type,
                        sub1.item_id as item_id,
                        (sub1.quantity_single/sub1.quantity_exchange) as quantity_singe_primary,
                        sub1.quantity_exchange as quantity_exchange,
                        sub1.quantity_single as quantity_single,
                        SUM(sub1.quantity_primary) as quantity_primary,
                        SUM(sub1.quantity) as quantity,
                        sub1.unit_id as unit_id,
                        sub1.unit_parent_id as unit_parent_id
                    FROM tbl_productions_orders_items_sub
                    INNER JOIN tbl_productions_orders_items_sub sub1 ON sub1.parent_id = tbl_productions_orders_items_sub.id
                    WHERE tbl_productions_orders_items_sub.parent_id IN ('$poisub_id')
                    GROUP BY sub1.type, sub1.item_id
                )";
                $subItems = $this->db->query($query)->result_array();
                if (!empty($subItems)) {
                    foreach ($subItems as $k => $v) {
                        $item_type = $v['item_type'];
                        $item_id = $v['item_id'];

                        $images = '';
                        if ($item_type == "materials") {
                            $info = $this->items_model->rowMaterial($item_id);
                            if (!empty($info['images'])) {
                                $images = base_url('uploads/materials/' . $info['images']);
                            }
                        } else if ($item_type == "tools_supplies") {
                            $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                            if (!empty($info['images'])) {
                                $images = base_url('uploads/tools_supplies/' . $info['images']);
                            }
                        } else {
                            $info = $this->products_model->rowProduct($item_id);
                            if (!empty($info['images'])) {
                                $images = base_url('uploads/products/' . $info['images']);
                            }
                        }

                        if (empty($images)) {
                            $images = base_url('assets/images/tnh/no_image.png');
                        }
                        $warehousePlan = $this->manufactures_model->getWarehouseProductionsPlan($production_plan_id, $item_type, $item_id);

                        $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type, $production_plan_id);

                        //warehouses
                        if ($item_type == "materials") {
                            $item_type = "nvl";
                        } else if ($item_type == "tools_supplies") {
                            $item_type = "tools";
                        } else {
                            $item_type = "product";
                        }
                        $this->db->select('tblwarehouse.id, tblwarehouse.name');
                        $this->db->from('tblwarehouse');
                        if (!empty($item_id)) {
                            $this->db->where('(
                                EXISTS (
                                    SELECT tblwarehouse_items.id
                                    FROM tblwarehouse_items
                                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                                )
                            )');
                        }
                        $warehouses = $this->db->get()->result_array();
                        $w = [];
                        if (!empty($warehouses)) {
                            foreach ($warehouses as $kW => $vW) {
                                $warehouse_id = $vW['id'];

                                $tbQuantityWarehouses = '(
                                    SELECT
                                        tblwarehouse_items.localtion as localtion_id,
                                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                                    FROM tblwarehouse_items
                                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                                    GROUP BY tblwarehouse_items.localtion
                                ) tb_quantity_warehouses';

                                $this->db->select('
                                    CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                                    CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                                    tb_quantity_warehouses.product_quantity as product_quantity
                                ', false);
                                $this->db->from('tbllocaltion_warehouses');
                                $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                                $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                                $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                                if (!empty($production_plan_id)) {
                                    $this->db->group_start();
                                    $this->db->where('tbllocaltion_warehouses.productions_plan_id', $production_plan_id);
                                    $this->db->or_where('tbllocaltion_warehouses.productions_plan_id', 0);
                                    $this->db->group_end();
                                }
                                $location_warehouses = $this->db->get()->result_array();
                                $w[] = ['text' => $vW['name'], 'children' => $location_warehouses];
                            }
                        }
                        $subItems[$k]['warehouses'] = $w;

                        $item_code = $info['code'];
                        $item_name = $info['name'];
                        $subItems[$k]['item_code'] = $item_code;
                        $subItems[$k]['item_name'] = $item_name;
                        $subItems[$k]['images'] = $images;
                        $subItems[$k]['isWarehouses'] = $isWarehouses;
                        $subItems[$k]['warehousePlan'] = ($warehousePlan);
                    }
                } else {
                    $product_id = $productions_orders_sub['product_id'];
                    $versions = $productions_orders_sub['versions'];
                    $this->db->select("
                    tbl_product_versions.id as version_id,
                    ");
                    $this->db->from('tbl_product_versions');
                    $this->db->where('tbl_product_versions.product_id', $product_id);
                    $this->db->where('tbl_product_versions.versions', $versions);
                    $product_verions = $this->db->get()->row_array();
                    if (!empty($product_verions)) {
                        $version_id = $product_verions['version_id'];
                        $this->db->select('
                            CONCAT(tbl_element_items.type, "__", tbl_element_items.item_id) as item_cs_id,
                            tbl_element_items.item_id as item_id,
                            tbl_element_items.type as item_type,
                            (tbl_versions_element.quantity * tbl_element_items.quantity) as quantity,
                            tbl_element_items.unit_id as unit_id,
                        ');
                        $this->db->from('tbl_versions_element');
                        $this->db->join('tbl_element_items', 'tbl_element_items.element_id = tbl_versions_element.id');
                        $this->db->where('tbl_versions_element.version_id', $version_id);
                        $this->db->group_by('tbl_element_items.item_id, tbl_element_items.type');
                        $versions_element = $this->db->get()->result_array();
                        if (!empty($versions_element)) {
                            foreach ($versions_element as $k => $v) {
                                $item_type = $v['item_type'];
                                $item_id = $v['item_id'];

                                $quantity_primary = $v['quantity'];
                                $quantity_singe_primary = $v['quantity'];

                                $images = '';
                                if ($item_type == "materials") {
                                    $info = $this->items_model->rowMaterial($item_id);
                                    $unit_id = $v['unit_id'];
                                    $row_exchange = $this->products_model->rowExchangeItems($item_id, $unit_id);
                                    $quantity_exchange = 1;
                                    if (!empty($row_exchange)) {
                                        $quantity_exchange = $row_exchange['number_exchange'];
                                    }
                                    $quantity_primary = $v['quantity'] / $quantity_exchange;
                                    if (!empty($info['images'])) {
                                        $images = base_url('uploads/materials/' . $info['images']);
                                    }
                                } else if ($item_type == "tools_supplies") {
                                    $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                                    if (!empty($info['images'])) {
                                        $images = base_url('uploads/tools_supplies/' . $info['images']);
                                    }
                                } else {
                                    $info = $this->products_model->rowProduct($item_id);
                                    if (!empty($info['images'])) {
                                        $images = base_url('uploads/products/' . $info['images']);
                                    }
                                }

                                if (empty($images)) {
                                    $images = base_url('assets/images/tnh/no_image.png');
                                }

                                $warehousePlan = $this->manufactures_model->getWarehouseProductionsPlan($production_plan_id, $item_type, $item_id);

                                $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type, $production_plan_id);

                                $item_code = $info['code'];
                                $item_name = $info['name'];
                                $versions_element[$k]['unit_parent_id'] = $info['unit_id'];
                                $versions_element[$k]['item_code'] = $item_code;
                                $versions_element[$k]['item_name'] = $item_name;
                                $versions_element[$k]['quantity_primary'] = $quantity_primary;
                                $versions_element[$k]['quantity_singe_primary'] = $quantity_primary;
                                $versions_element[$k]['quantity_exchange'] = $quantity_exchange;
                                $versions_element[$k]['quantity_single'] = $quantity_primary;
                                $versions_element[$k]['images'] = $images;
                                $versions_element[$k]['isWarehouses'] = $isWarehouses;
                                $versions_element[$k]['warehousePlan'] = json_encode($warehousePlan);

                                //warehouses
                                if ($item_type == "materials") {
                                    $item_type = "nvl";
                                } else if ($item_type == "tools_supplies") {
                                    $item_type = "tools";
                                } else {
                                    $item_type = "product";
                                }
                                $this->db->select('tblwarehouse.id, tblwarehouse.name');
                                $this->db->from('tblwarehouse');
                                if (!empty($item_id)) {
                                    $this->db->where('(
                                        EXISTS (
                                            SELECT tblwarehouse_items.id
                                            FROM tblwarehouse_items
                                            WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                                        )
                                    )');
                                }
                                $warehouses = $this->db->get()->result_array();
                                $w = [];
                                if (!empty($warehouses)) {
                                    foreach ($warehouses as $kW => $vW) {
                                        $warehouse_id = $vW['id'];

                                        $tbQuantityWarehouses = '(
                                            SELECT
                                                tblwarehouse_items.localtion as localtion_id,
                                                SUM(tblwarehouse_items.product_quantity) as product_quantity
                                            FROM tblwarehouse_items
                                            WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                                            GROUP BY tblwarehouse_items.localtion
                                        ) tb_quantity_warehouses';

                                        $this->db->select('
                                            CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                                            CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                                            tb_quantity_warehouses.product_quantity as product_quantity
                                        ', false);
                                        $this->db->from('tbllocaltion_warehouses');
                                        $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                                        $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                                        $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                                        if (!empty($production_plan_id)) {
                                            $this->db->group_start();
                                            $this->db->where('tbllocaltion_warehouses.productions_plan_id', $production_plan_id);
                                            $this->db->or_where('tbllocaltion_warehouses.productions_plan_id', 0);
                                            $this->db->group_end();
                                        }
                                        $location_warehouses = $this->db->get()->result_array();
                                        $w[] = ['text' => $vW['name'], 'children' => $location_warehouses];
                                    }
                                }
                                $versions_element[$k]['warehouses'] = $w;
                            }
                        }
                    }
                    $subItems = !empty($versions_element) ? $versions_element : null;
                }
                $productions_orders_sub['subItems'] = $subItems;
            }
            $data['items'] = $productions_orders_sub;
        } else if ($product_type == "outside") {
            $this->db->select('
                CONCAT("outside__", tbl_products.id) as id,
                tbl_products.id as product_id,
                tbl_products.name as name,
                tbl_products.code as code,
                0 as poisub_id,
                1 as quantity_primary,
                1 as quantity,
                1 as quantity_exchange,
                1 as quantity_single,
                tbl_products.versions as versions,
                tbl_products.images as images,
                tbl_products.unit_id as unit_id,
                tbl_products.unit_id as unit_parent_id,
            ');
            $this->db->from('tbl_products');
            $this->db->where('tbl_products.id', $product_id);
            $semi_products = $this->db->get()->row_array();
            if (!empty($semi_products)) {
                $versions = $semi_products['versions'];
                $product_id = $semi_products['product_id'];

                $this->db->select("
                    tbl_product_versions.id as version_id,
                ");
                $this->db->from('tbl_product_versions');
                $this->db->where('tbl_product_versions.product_id', $product_id);
                $this->db->where('tbl_product_versions.versions', $versions);
                $product_verions = $this->db->get()->row_array();
                if (!empty($product_verions)) {
                    $version_id = $product_verions['version_id'];
                    $this->db->select('
                        CONCAT(tbl_element_items.type, "__", tbl_element_items.item_id) as item_cs_id,
                        tbl_element_items.item_id as item_id,
                        tbl_element_items.type as item_type,
                        (tbl_versions_element.quantity * tbl_element_items.quantity) as quantity,
                        tbl_element_items.unit_id as unit_id,
                    ');
                    $this->db->from('tbl_versions_element');
                    $this->db->join('tbl_element_items', 'tbl_element_items.element_id = tbl_versions_element.id');
                    $this->db->where('tbl_versions_element.version_id', $version_id);
                    $this->db->group_by('tbl_element_items.item_id, tbl_element_items.type');
                    $versions_element = $this->db->get()->result_array();
                    if (!empty($versions_element)) {
                        foreach ($versions_element as $k => $v) {
                            $item_type = $v['item_type'];
                            $item_id = $v['item_id'];

                            $quantity_primary = $v['quantity'];
                            $quantity_singe_primary = $v['quantity'];

                            $images = '';
                            if ($item_type == "materials") {
                                $info = $this->items_model->rowMaterial($item_id);
                                $unit_id = $v['unit_id'];
                                $row_exchange = $this->products_model->rowExchangeItems($item_id, $unit_id);
                                $quantity_exchange = 1;
                                if (!empty($row_exchange)) {
                                    $quantity_exchange = $row_exchange['number_exchange'];
                                }
                                $quantity_primary = $v['quantity'] / $quantity_exchange;
                                if (!empty($info['images'])) {
                                    $images = base_url('uploads/materials/' . $info['images']);
                                }
                            } else if ($item_type == "tools_supplies") {
                                $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                                if (!empty($info['images'])) {
                                    $images = base_url('uploads/tools_supplies/' . $info['images']);
                                }
                            } else {
                                $info = $this->products_model->rowProduct($item_id);
                                if (!empty($info['images'])) {
                                    $images = base_url('uploads/products/' . $info['images']);
                                }
                            }

                            if (empty($images)) {
                                $images = base_url('assets/images/tnh/no_image.png');
                            }

                            $warehousePlan = $this->manufactures_model->getWarehouseProductionsPlan($production_plan_id, $item_type, $item_id);

                            $item_code = $info['code'];
                            $item_name = $info['name'];
                            $versions_element[$k]['unit_parent_id'] = $info['unit_id'];
                            $versions_element[$k]['item_code'] = $item_code;
                            $versions_element[$k]['item_name'] = $item_name;
                            $versions_element[$k]['quantity_primary'] = $quantity_primary;
                            $versions_element[$k]['quantity_singe_primary'] = $quantity_primary;
                            $versions_element[$k]['quantity_exchange'] = $quantity_exchange;
                            $versions_element[$k]['quantity_single'] = $quantity_primary;
                            $versions_element[$k]['images'] = $images;
                            $versions_element[$k]['warehousePlan'] = json_encode($warehousePlan);

                            //warehouses
                            if ($item_type == "materials") {
                                $item_type = "nvl";
                            } else if ($item_type == "tools_supplies") {
                                $item_type = "tools";
                            } else {
                                $item_type = "product";
                            }
                            $this->db->select('tblwarehouse.id, tblwarehouse.name');
                            $this->db->from('tblwarehouse');
                            if (!empty($item_id)) {
                                $this->db->where('(
                                    EXISTS (
                                        SELECT tblwarehouse_items.id
                                        FROM tblwarehouse_items
                                        WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                                    )
                                )');
                            }
                            $warehouses = $this->db->get()->result_array();
                            $w = [];
                            if (!empty($warehouses)) {
                                foreach ($warehouses as $kW => $vW) {
                                    $warehouse_id = $vW['id'];

                                    $tbQuantityWarehouses = '(
                                        SELECT
                                            tblwarehouse_items.localtion as localtion_id,
                                            SUM(tblwarehouse_items.product_quantity) as product_quantity
                                        FROM tblwarehouse_items
                                        WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
                                        GROUP BY tblwarehouse_items.localtion
                                    ) tb_quantity_warehouses';

                                    $this->db->select('
                                        CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id) as id,
                                        CONCAT(tbllocaltion_warehouses.name, "(SL: ", tb_quantity_warehouses.product_quantity,")") as text,
                                        tb_quantity_warehouses.product_quantity as product_quantity
                                    ', false);
                                    $this->db->from('tbllocaltion_warehouses');
                                    $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                                    $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                                    $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                                    if (!empty($production_plan_id)) {
                                        $this->db->group_start();
                                        $this->db->where('tbllocaltion_warehouses.productions_plan_id', $production_plan_id);
                                        $this->db->or_where('tbllocaltion_warehouses.productions_plan_id', 0);
                                        $this->db->group_end();
                                    }
                                    $location_warehouses = $this->db->get()->result_array();
                                    $w[] = ['text' => $vW['name'], 'children' => $location_warehouses];
                                }
                            }
                            $versions_element[$k]['warehouses'] = $w;
                        }
                    }
                }
                $semi_products['subItems'] = !empty($versions_element) ? $versions_element : null;
            }
            $data['items'] = $semi_products;
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    //products

    public function loadALLProducts() {

        $data = [];
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $data['pod_id'] = $data_post['pod_id'];
        $data['type'] = !empty($data_post['type']) ? $data_post['type']: 2;
        $data['pois_id'] = $data_post['pois_id'];
        $data['dtPois'] = $this->manufactures_model->getProductionsOrdersItemsStagesById($data['pois_id']);
        $data['warehouses'] = $this->stock_model->getWarehouses(false, [WAREHOUSES_CAPACITY], $this->staffid);
        $data['productions_plan'] = $this->manufactures_model->getProductionsPlanPOD($data['pod_id']);
        $production_plan_id = $data['productions_plan']['id'];

        $quantity = !empty($data['quantity']) ? number_unformat($data['quantity'], false) : 0;
        $actions = !empty($data['actions']) ? $data['actions'] : '';

        $productions_orders_items = $this->manufactures_model->loadDataProducts($data['pod_id'], $production_plan_id, $quantity, $actions);
        $data['items'] = $productions_orders_items;
        echo json_encode($data);
    }

    public function handlingProducts()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
            }
        }

        $warehouses_semi_product = $data_post['warehouses_semi_product'];
        if (empty($warehouses_semi_product)) {
            $data['result'] = 0;
            $data['message'] = 'Vui lòng chọn kho thành phẩm';
            echo json_encode($data, JSON_UNESCAPED_UNICODE); die;
        }

        $sp_actions = !empty($data_post['actions']) ? $data_post['actions'] : '';
        $finished_productions = !empty($data_post['finished_productions']) ? $data_post['finished_productions'] : 0;
        $sp_type = 2;
        $sp_pod_id = $data_post['pod_id'];
        $sp_pois_id = $data_post['pois_id'];
        $use_productions_plan = 0;

        $dtPois = get_table_where('tbl_productions_orders_items_stages', ['id' => $sp_pois_id], "", "row_array", 'stage_id, active, productions_orders_items_id, number');

        $qc = $this->manufactures_model->isQCPre(0, $dtPois['productions_orders_items_id'], 0, $dtPois['number']);
        if ($qc == 2 || $qc == 3) {
            $data['result'] = 0;
            $data['message'] = lang('Có mặt hàng chưa QC công đoạn trước hoặc chưa đạt');
            echo json_encode($data); die;
        }

        $active = $dtPois['active'];
        $stage_id = $dtPois['stage_id'];
        $sp_cqi_id = !empty($data_post['cqi_id']) ? $data_post['cqi_id'] : 0;
        
        $qc_remake_id = 0;
        $typePurchase = 3;
        $typeSuggestExporting = 6;
        $active = $dtPois['active'];
        if ($sp_actions == 'qc') {
            $dtQcRemake = $this->manufactures_model->getQCRemakeCQIId($sp_cqi_id);
            if (!empty($dtQcRemake) && $dtQcRemake['status']) {
                $data['result'] = 0;
                $data['message'] = lang('Giai đoạn sản xuất lại này đã được hoàn thành');
                echo json_encode($data); die;
            }
            $qc_remake_id = $dtQcRemake['id'];
            $typePurchase = 11;
            $typeSuggestExporting = 11;
        } else {
            if ($active) {
                $data['result'] = 0;
                $data['message'] = lang('Giai đoạn này đã được hoàn thành');
                echo json_encode($data); die;
            }
        }

        $localtion_semi_product = $this->site_model->getLocationPOD($sp_pod_id, $warehouses_semi_product, $stage_id);
        $dataItems = [];
        $arrSaveBom = [];

        $total_quantity_purchases = 0;
        $count_quantity_purchases = 0;

        $total_quantity_se = 0;
        $total_quantity_exchange_se = 0;
        $count_quantity_se = 0;

        $items = $data_post['items'];
        $isErrors = true;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $item_type_sp = "products";
                $arr_id_sp = explode('__', $value['id']);
                $type_order_sp = $arr_id_sp[0];
                $item_id_sp = $arr_id_sp[1];
                $info = $this->products_model->rowProduct($item_id_sp);
                if (empty($info)) {
                    if (empty($info_mt)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không tìm thấy dữ liệu BTP');
                        echo json_encode($data);
                        die;
                    }
                }
                $item_code_sp = $info['code'];
                $item_name_sp = $info['name'];

                $quantity_exchange_sp = number_unformat($value['quantity_exchange']);
                $quantity_single_sp = number_unformat($value['quantity_single']);
                $quantity_semi_product = number_unformat($value['quantity_semi_product']);
                $unit_id_sp = $value['unit_id'];
                $unit_parent_id_sp = $value['unit_parent_id'];

                $materials = !empty($value['subItems']) ? $value['subItems'] : null;
                $is_save_dm = !empty($value['is_save_dm']) ? $value['is_save_dm'] : 0;
                $arrMaterials = [];
                $arrMaterialsBOM = [];
                if (!empty($materials)) {
                    $isErrors = false;
                    foreach ($materials as $k => $v) {
                        $quantity_exchange_mt = number_unformat($v['quantity_exchange']);
                        if (empty($quantity_exchange_mt)) $quantity_exchange_mt = 1;
                        $quantity_single_mt = number_unformat($v['quantity_single']);
                        $arr_item_cs_id_mt = explode('__', $v['item_cs_id']);
                        $item_id_mt = $arr_item_cs_id_mt[1];
                        $item_type_mt = $arr_item_cs_id_mt[0];
                        $unit_id = $v['unit_id'];
                        $unit_parent_id = $v['unit_parent_id'];
                        if ($item_type_mt == "materials") {
                            $info_mt = $this->items_model->rowMaterial($item_id_mt);
                        } else if ($item_type_mt == "tools_supplies") {
                            $info_mt = $this->tools_supplies_model->rowToolsSupplies($item_id_mt);
                        } else {
                            $info_mt = $this->products_model->rowProduct($item_id_mt);
                        }

                        if (empty($info_mt)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Không tìm thấy dữ liệu NVL');
                            echo json_encode($data);
                            die;
                        }

                        // $quantity_materials_mt = $v['quantity_materials'];
                        $unit_id_mt = $v['unit_id'];
                        $unit_parent_id_mt = $v['unit_parent_id'];
                        $warehouses_items_mt = $v['warehouses_items'];
                        if (empty($warehouses_items_mt)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Xin vui lòng chọn kho NVL');
                            echo json_encode($data);
                            die;
                        } else if ($warehouses_items_mt) {
                            foreach ($warehouses_items_mt as $kW => $vW) {
                                if (empty($vW['warehouse_id'])) {
                                    $data['result'] = 0;
                                    $data['message'] = lang('Xin vui lòng chọn kho NVL');
                                    echo json_encode($data);
                                    die;
                                }
                                $vWW = explode('__', $vW['warehouse_id']);
                                $warehouse_item_id = $vWW[0];
                                $location_id = $vWW[1];
                                $quantity_items = !empty($vW['quantity_items']) ? number_unformat($vW['quantity_items']) : 0;
                                if (empty($quantity_items)) continue;
                                $quantity_exchange_se =  $quantity_items / $quantity_exchange_mt;
                                $quantityW = $this->manufactures_model->getTotalQuantitW($item_id_mt, $item_type_mt, $location_id, $warehouse_item_id);
                                if ($quantity_exchange_se > $quantityW['total_quantity']) {
                                    $data['result'] = 0;
                                    $data['message'] = 'Mã '.$info_mt['code'].' không đủ số lượng trong kho để xuất';
                                    echo json_encode($data); die;
                                }

                                $arrMaterials[] = [
                                    'type_item' => $item_type_mt,
                                    'item_id' => $item_id_mt,
                                    'item_code' => $info_mt['code'],
                                    'item_name' => $info_mt['name'],
                                    'unit_id' => $unit_id_mt,
                                    'quantity_export' => $quantity_items,
                                    'unit_parent_id' => $unit_parent_id_mt,
                                    'number_exchange' => $quantity_exchange_mt,
                                    'quantity_exchange' => $quantity_exchange_se,
                                    'location_id' => $location_id,
                                    'warehouse_item_id' => $warehouse_item_id,
                                ];
                                $total_quantity_se += $quantity_items;
                                $total_quantity_exchange_se += $quantity_exchange_se;
                                $count_quantity_se++;
                            }
                        }

                        if ($is_save_dm) {
                            $arrMaterialsBOM[] = [
                                'type' => $item_type_mt,
                                'item_id' => $item_id_mt,
                                'unit_id' => $unit_id_mt,
                                'quantity' => $quantity_single_mt,
                            ];
                        }
                    }
                }

                if (!empty($is_save_dm) && !empty($arrMaterialsBOM)) {
                    $arrSaveBom[$key] = [
                        'product_id' => $item_id_sp,
                        'arrMaterialsBOM' => $arrMaterialsBOM
                    ];
                }

                $poisub_id = $value['poisub_id'];
                $arrPOISub = [];
                if (!empty($poisub_id)) {
                    $tempArrPOISub = explode(',', $poisub_id);
                    foreach ($tempArrPOISub as $k => $v) {
                        $arrPOISub[$k]['poisub_id'] = $v;
                    }
                }

                $dataItems[] = [
                    'productions_orders_details_id' => $sp_pod_id,
                    'type_item' => $item_type_sp,
                    'item_id' => $item_id_sp,
                    'location_id' => $localtion_semi_product,
                    'item_code' => $item_code_sp,
                    'item_name' => $item_name_sp,
                    'quantity' => $quantity_semi_product,
                    'quantity_exchange' => $quantity_exchange_sp,
                    'quantity_single' => $quantity_single_sp,
                    'quantity_semi_product' => $quantity_semi_product,
                    'type_order' => $type_order_sp,
                    'arrPOISub' => $arrPOISub,
                    'arrMaterials' => $arrMaterials
                ];
                $total_quantity_purchases += $quantity_semi_product;
                $count_quantity_purchases++;
            }
        }

        if (!empty($isErrors)) {
            $data['result'] = 0;
            $data['message'] = lang('Không có BTP xuất ra');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }

        if (empty($dataItems)) {
            $data['result'] = 0;
            $data['message'] = lang('Không có dữ liệu');
            echo json_encode($data); die;
        }

        $dateGerenal = date('Y-m-d H:i:s');
        $staffGerenal = $this->staffid;
        $reference_purchase = getReference('purchase_products');
        $purchases = [
            'reference_no' => $reference_purchase,
            'date' => $dateGerenal,
            'productions_orders_details_id' => $sp_pod_id,
            'warehouse_id' => $warehouses_semi_product,
            'count_items' => $count_quantity_purchases,
            'total_quantity' => $total_quantity_purchases,
            'created_by' => $staffGerenal,
            'date_created' => $dateGerenal,
            'status' => 'un_approved',
            'pois_id' => $sp_pois_id,
            'type' => $typePurchase,
            'sp_type' => $sp_type,
            'cqi_id' => $sp_cqi_id
        ];

        $save_and_warehouse = 1;
        $export_name = 'Xuất kho NVL';
        $reference_suggest_exporting = getReference('stock');
        $suggest_exporting = [
            'productions_orders_details_id' => $sp_pod_id,
            'reference_no' => null,
            'reference_stock' => $reference_suggest_exporting,
            'date' => $dateGerenal,
            'export_name' => $export_name,
            'note' => '',
            'status' => 'un_approved',
            'total_quantity' => $total_quantity_se,
            'count_items' => $count_quantity_se,
            'total_quantity_exchange' => $total_quantity_exchange_se,
            'created_by' => $staffGerenal,
            'date_created' => $dateGerenal,
            'convert_stock_by' => $staffGerenal,
            'date_convert_stock' => $dateGerenal,
            'status_stock' => 'approved',
            'date_stock' => $dateGerenal,
            'user_stock' => $staffGerenal,
            'type' => $typeSuggestExporting,
            'date_convert_stock' => $dateGerenal,
            'warehouse_id' => 0,
            'save_and_warehouse' => $save_and_warehouse,
            'pois_id' => $sp_pois_id,
            'use_productions_plan' => $use_productions_plan,
            'cqi_id' => $sp_cqi_id
        ];

        $errors = '';
        // print_arrays($dataItems);
        $purchase_product_id = $this->stock_model->insertPurchaseProducts($purchases);
        if (!empty($purchase_product_id)) {
            updateReference('purchase_products');
            if ($sp_actions != 'qc') {
                if ($sp_actions != "products" || ($sp_actions == "products" && !empty($finished_productions))) {
                    $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                        'active' => 1,
                        'staff_active' => $staffGerenal,
                        'date_active' => $dateGerenal,
                    ]);
                }
            } else {
                $op = [
                    'pod_id' => $sp_pod_id,
                    'stage_id' => $stage_id,
                    'check_quality_items_id' => $sp_cqi_id,
                    'pois_id' => $sp_pois_id,
                    'type' => 2
                ];
                if (!empty($finished_productions)) {
                    $op['status'] = 1;
                    $op['staff_status'] = $staffGerenal;
                    $op['status_date'] = $dateGerenal;
                }
                if ($qc_remake_id) {
                    $up = $this->manufactures_model->updateQCRemake($qc_remake_id, $op);
                } else {
                    $ins = $this->manufactures_model->insertQCRemake($op);
                }
            }

            $suggest_exporting['purchase_product_id'] = $purchase_product_id;
            $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($suggest_exporting);
            if ($suggest_exporting_id) {
                updateReference('stock');
            }

            if (!empty($dataItems)) {
                foreach ($dataItems as $key => $value) {
                    $arrPOISub = $value['arrPOISub'];
                    $arrMaterials = $value['arrMaterials'];
                    unset($value['arrPOISub']);
                    unset($value['arrMaterials']);
                    $value['purchase_product_id'] = $suggest_exporting['purchase_product_id'];
                    $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                    if ($purchase_product_item_id) {
                        if (!empty($arrPOISub)) {
                            foreach ($arrPOISub as $k => $v) {
                                $arrPOISub[$k]['purchase_product_id'] = $purchase_product_id;
                                $arrPOISub[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                            }
                        }

                        if (!empty($arrMaterials)) {
                            foreach ($arrMaterials as $k => $v) {
                                $arrMaterials[$k]['suggest_exporting_id'] = $suggest_exporting_id;
                                $arrMaterials[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                            }
                        }
                        if (!empty($arrMaterials)) {
                            $this->manufactures_model->insertBatchSuggestExportingItems($arrMaterials);
                        }
                    }

                    if (!empty($arrPOISub)) {
                        $this->manufactures_model->insertBatchPurchaseProductPoisub($arrPOISub);
                    }
                }
            }

            //suggest exporting
            if (!empty($save_and_warehouse)) {
                $id = $suggest_exporting_id;
                $_data = array(
                    'warehouseman_id' => $staffGerenal,
                    'date_warehouseman' => date('Y-m-d H:i:s')
                );

                if (!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                    $errors = lang('test_quantyti_time_return');
                } else {
                    $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                    if ($success) {
                        log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                        $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                        $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                        $content = lang('tnh_his_warehouse_exporting_producion');
                        $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);

                        insertActivityLog([
                            'type_parent_obj' => 'exporting_producion',
                            'table_obj' => 'tbl_suggest_exporting',
                            'id_obj' => $id,
                            'name_obj' => $suggest_exporting['reference_stock'],
                            'content' => $content,
                            'actions' => 'warehouse'
                        ], $this->staffid);
                    }
                }
            }

            $pod = $this->manufactures_model->rowProductionsOrdersDetais($sp_pod_id);
            $content = lang('tnh_his_add_exporting_producion');
            $content = str_replace('{$1}', $reference_suggest_exporting, $content);
            $content = str_replace('{$2}', $pod['reference_no'], $content);

            insertActivityLog([
                'type_parent_obj' => 'exporting_producion',
                'table_obj' => 'tbl_suggest_exporting',
                'id_obj' => $suggest_exporting_id,
                'name_obj' => $reference_suggest_exporting,
                'content' => $content,
                'actions' => 'add'
            ], $staffGerenal);

            //warehouses purchase products
            if (!empty($save_and_warehouse)) {
                $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_id);
                $_data = array(
                    'status' => 'approved',
                    'warehouseman_id' => $staffGerenal,
                    'date_warehouseman' => date('Y-m-d H:i:s')
                );
                $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_id));
                if ($success) {
                    log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_id);
                    $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_id);

                    //activity log
                    if (!empty($purchaseProduct['productions_orders_details_id'])) {
                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                        $content = lang('tnh_his_warehouse_purchase_product_pod');
                        $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                    } else {
                        $content = lang('tnh_his_warehouse_purchase_product');
                        $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                    }
                    insertActivityLog([
                        'type_parent_obj' => 'purchase_products',
                        'table_obj' => 'tbl_purchase_products',
                        'id_obj' => $purchase_product_id,
                        'name_obj' => $purchaseProduct['reference_no'],
                        'content' => $content,
                        'actions' => 'warehouse'
                    ], $staffGerenal);
                    //end activity log
                }
            }

            $content = lang('tnh_his_add_purchase_product');
            $content = str_replace('{$1}', $reference_purchase, $content);
            insertActivityLog([
                'type_parent_obj' => 'purchase_products',
                'table_obj' => 'tbl_purchase_products',
                'id_obj' => $purchase_product_id,
                'name_obj' => $reference_purchase,
                'content' => $content,
                'actions' => 'add'
            ], $staffGerenal);

            //handling bom
            if (!empty($arrSaveBom)) {
                $this->products_model->handlingBOMPOD($arrSaveBom);
            }

            noti_custom('qc_po_detail', $sp_pod_id, $staffGerenal, 0, 'finishedQCPODetail', [
                'arrPOIS' => [$sp_pois_id],
                'pod_id' => $sp_pod_id,
                'stage_id' => $stage_id,
            ]);

            $data['result'] = 1;
            $data['message'] = lang('success');
            $data['error'] = $errors;
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function loadALLProductsStep() {
        $data = [];
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $data['pod_id'] = $data_post['pod_id'];
        $data['type'] = !empty($data_post['type']) ? $data_post['type']: 2;
        $data['pois_id'] = $data_post['pois_id'];
        $data['dtPois'] = $this->manufactures_model->getProductionsOrdersItemsStagesById($data['pois_id']);
        $data['warehouses'] = $this->stock_model->getWarehouses(false, [WAREHOUSES_CAPACITY], $this->staffid);
        $data['productions_plan'] = $this->manufactures_model->getProductionsPlanPOD($data['pod_id']);
        $production_plan_id = $data['productions_plan']['id'];

        $quantity = !empty($data['quantity']) ? number_unformat($data['quantity'], false) : 0;
        $actions = !empty($data['actions']) ? $data['actions'] : '';
        $productions_orders_items = $this->manufactures_model->loadDataProductsStep($data['pod_id'], $data['pois_id'], $quantity, $actions);
        $data['items'] = $productions_orders_items;
        echo json_encode($data);
    }

    public function handlingProductsStep()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
            }
        }

        $warehouses_semi_product = $data_post['warehouses_semi_product'];
        if (empty($warehouses_semi_product)) {
            $data['result'] = 0;
            $data['message'] = 'Vui lòng chọn kho thành phẩm';
            echo json_encode($data, JSON_UNESCAPED_UNICODE); die;
        }

        $sp_actions = !empty($data_post['actions']) ? $data_post['actions'] : '';
        $finished_productions = !empty($data_post['finished_productions']) ? $data_post['finished_productions'] : 0;
        $sp_type = 3;
        $sp_pod_id = $data_post['pod_id'];
        $sp_pois_id = $data_post['pois_id'];
        $use_productions_plan = 0;

        $dtPois = get_table_where('tbl_productions_orders_items_stages', ['id' => $sp_pois_id], "", "row_array", 'stage_id, final_stage, active, number, productions_orders_items_id');

        $qc = $this->manufactures_model->isQCPre(0, $dtPois['productions_orders_items_id'], 0, $dtPois['number']);
        if ($qc == 2 || $qc == 3) {
            $data['result'] = 0;
            $data['message'] = lang('Có mặt hàng chưa QC công đoạn trước hoặc chưa đạt');
            echo json_encode($data); die;
        }

        $active = $dtPois['active'];
        $stage_id = $dtPois['stage_id'];
        $final_stage = $dtPois['final_stage'];
        $sp_cqi_id = !empty($data_post['cqi_id']) ? $data_post['cqi_id'] : 0;
        $qc_remake_id = 0;
        $typePurchase = 4;
        $typeSuggestExporting = 7;
        if ($sp_actions == 'qc') {
            $dtQcRemake = $this->manufactures_model->getQCRemakeCQIId($sp_cqi_id);
            if (!empty($dtQcRemake) && $dtQcRemake['status']) {
                $data['result'] = 0;
                $data['message'] = lang('Giai đoạn sản xuất lại này đã được hoàn thành');
                echo json_encode($data); die;
            }
            $qc_remake_id = $dtQcRemake['id'];
            $typePurchase = 12;
            $typeSuggestExporting = 12;
        } else {
            if ($active) {
                $data['result'] = 0;
                $data['message'] = lang('Giai đoạn này đã được hoàn thành');
                echo json_encode($data); die;
            }
        }

        $stage_id_w = $stage_id;
        if ($final_stage) {
            $stage_id_w = 0;
        }
        $localtion_semi_product = $this->site_model->getLocationPOD($sp_pod_id, $warehouses_semi_product, $stage_id_w);
        $dataItems = [];
        $arrSaveBom = [];

        $total_quantity_purchases = 0;
        $count_quantity_purchases = 0;

        $total_quantity_se = 0;
        $total_quantity_exchange_se = 0;
        $count_quantity_se = 0;

        $items = $data_post['items'];
        $isErrors = true;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $item_type_sp = "products";
                $arr_id_sp = explode('__', $value['id']);
                $type_order_sp = $arr_id_sp[0];
                $item_id_sp = $arr_id_sp[1];
                $info = $this->products_model->rowProduct($item_id_sp);
                if (empty($info)) {
                    if (empty($info_mt)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không tìm thấy dữ liệu BTP');
                        echo json_encode($data);
                        die;
                    }
                }
                $item_code_sp = $info['code'];
                $item_name_sp = $info['name'];

                $quantity_exchange_sp = number_unformat($value['quantity_exchange']);
                $quantity_single_sp = number_unformat($value['quantity_single']);
                $quantity_semi_product = number_unformat($value['quantity_semi_product']);
                $unit_id_sp = $value['unit_id'];
                $unit_parent_id_sp = $value['unit_parent_id'];

                $materials = !empty($value['subItems']) ? $value['subItems'] : null;
                $is_save_dm = !empty($value['is_save_dm']) ? $value['is_save_dm'] : 0;
                $arrMaterials = [];
                $arrMaterialsBOM = [];
                if (!empty($materials)) {
                    $isErrors = false;
                    foreach ($materials as $k => $v) {
                        $quantity_exchange_mt = number_unformat($v['quantity_exchange']);
                        if (empty($quantity_exchange_mt)) $quantity_exchange_mt = 1;
                        $quantity_single_mt = number_unformat($v['quantity_single']);
                        $arr_item_cs_id_mt = explode('__', $v['item_cs_id']);
                        $item_id_mt = $arr_item_cs_id_mt[1];
                        $item_type_mt = $arr_item_cs_id_mt[0];
                        $unit_id = $v['unit_id'];
                        $unit_parent_id = $v['unit_parent_id'];
                        if ($item_type_mt == "materials") {
                            $info_mt = $this->items_model->rowMaterial($item_id_mt);
                        } else if ($item_type_mt == "tools_supplies") {
                            $info_mt = $this->tools_supplies_model->rowToolsSupplies($item_id_mt);
                        } else {
                            $info_mt = $this->products_model->rowProduct($item_id_mt);
                        }

                        if (empty($info_mt)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Không tìm thấy dữ liệu NVL');
                            echo json_encode($data);
                            die;
                        }

                        // $quantity_materials_mt = $v['quantity_materials'];
                        $unit_id_mt = $v['unit_id'];
                        $unit_parent_id_mt = $v['unit_parent_id'];
                        $warehouses_items_mt = $v['warehouses_items'];
                        if (empty($warehouses_items_mt)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Xin vui lòng chọn kho NVL');
                            echo json_encode($data);
                            die;
                        } else if ($warehouses_items_mt) {
                            foreach ($warehouses_items_mt as $kW => $vW) {
                                if (empty($vW['warehouse_id'])) {
                                    $data['result'] = 0;
                                    $data['message'] = lang('Xin vui lòng chọn kho NVL');
                                    echo json_encode($data);
                                    die;
                                }
                                $vWW = explode('__', $vW['warehouse_id']);
                                $warehouse_item_id = $vWW[0];
                                $location_id = $vWW[1];
                                $quantity_items = !empty($vW['quantity_items']) ? number_unformat($vW['quantity_items']) : 0;
                                if (empty($quantity_items)) continue;
                                $quantity_exchange_se =  $quantity_items / $quantity_exchange_mt;
                                $quantityW = $this->manufactures_model->getTotalQuantitW($item_id_mt, $item_type_mt, $location_id, $warehouse_item_id);
                                if ($quantity_exchange_se > $quantityW['total_quantity']) {
                                    $data['result'] = 0;
                                    $data['message'] = 'Mã '.$info_mt['code'].' không đủ số lượng trong kho để xuất';
                                    echo json_encode($data); die;
                                }
                                
                                $arrMaterials[] = [
                                    'type_item' => $item_type_mt,
                                    'item_id' => $item_id_mt,
                                    'item_code' => $info_mt['code'],
                                    'item_name' => $info_mt['name'],
                                    'unit_id' => $unit_id_mt,
                                    'quantity_export' => $quantity_items,
                                    'unit_parent_id' => $unit_parent_id_mt,
                                    'number_exchange' => $quantity_exchange_mt,
                                    'quantity_exchange' => $quantity_exchange_se,
                                    'location_id' => $location_id,
                                    'warehouse_item_id' => $warehouse_item_id,
                                ];
                                $total_quantity_se += $quantity_items;
                                $total_quantity_exchange_se += $quantity_exchange_se;
                                $count_quantity_se++;
                            }
                        }

                        if ($is_save_dm) {
                            $arrMaterialsBOM[] = [
                                'type' => $item_type_mt,
                                'item_id' => $item_id_mt,
                                'unit_id' => $unit_id_mt,
                                'quantity' => $quantity_single_mt,
                            ];
                        }
                    }
                }

                if (!empty($is_save_dm) && !empty($arrMaterialsBOM)) {
                    $arrSaveBom[$key] = [
                        'product_id' => $item_id_sp,
                        'arrMaterialsBOM' => $arrMaterialsBOM
                    ];
                }

                $poisub_id = $value['poisub_id'];
                $arrPOISub = [];
                if (!empty($poisub_id)) {
                    $tempArrPOISub = explode(',', $poisub_id);
                    foreach ($tempArrPOISub as $k => $v) {
                        $arrPOISub[$k]['poisub_id'] = $v;
                    }
                }

                $dataItems[] = [
                    'productions_orders_details_id' => $sp_pod_id,
                    'type_item' => $item_type_sp,
                    'item_id' => $item_id_sp,
                    'location_id' => $localtion_semi_product,
                    'item_code' => $item_code_sp,
                    'item_name' => $item_name_sp,
                    'quantity' => $quantity_semi_product,
                    'quantity_exchange' => $quantity_exchange_sp,
                    'quantity_single' => $quantity_single_sp,
                    'quantity_semi_product' => $quantity_semi_product,
                    'type_order' => $type_order_sp,
                    'arrPOISub' => $arrPOISub,
                    'arrMaterials' => $arrMaterials
                ];
                $total_quantity_purchases += $quantity_semi_product;
                $count_quantity_purchases++;
            }
        }

        if (!empty($isErrors)) {
            $data['result'] = 0;
            $data['message'] = lang('Không có thành phẩm xuất ra ở công đoạn trước');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }

        if (empty($dataItems)) {
            $data['result'] = 0;
            $data['message'] = lang('Không có dữ liệu');
            echo json_encode($data); die;
        }

        $dateGerenal = date('Y-m-d H:i:s');
        $staffGerenal = $this->staffid;
        $reference_purchase = getReference('purchase_products');
        $purchases = [
            'reference_no' => $reference_purchase,
            'date' => $dateGerenal,
            'productions_orders_details_id' => $sp_pod_id,
            'warehouse_id' => $warehouses_semi_product,
            'count_items' => $count_quantity_purchases,
            'total_quantity' => $total_quantity_purchases,
            'created_by' => $staffGerenal,
            'date_created' => $dateGerenal,
            'status' => 'un_approved',
            'pois_id' => $sp_pois_id,
            'type' => $typePurchase,
            'sp_type' => $sp_type,
            'final_stage' => $final_stage,
            'cqi_id' => $sp_cqi_id
        ];

        $save_and_warehouse = 1;
        $export_name = 'Xuất kho NVL';
        $reference_suggest_exporting = getReference('stock');
        $suggest_exporting = [
            'productions_orders_details_id' => $sp_pod_id,
            'reference_no' => null,
            'reference_stock' => $reference_suggest_exporting,
            'date' => $dateGerenal,
            'export_name' => $export_name,
            'note' => '',
            'status' => 'un_approved',
            'total_quantity' => $total_quantity_se,
            'count_items' => $count_quantity_se,
            'total_quantity_exchange' => $total_quantity_exchange_se,
            'created_by' => $staffGerenal,
            'date_created' => $dateGerenal,
            'convert_stock_by' => $staffGerenal,
            'date_convert_stock' => $dateGerenal,
            'status_stock' => 'approved',
            'date_stock' => $dateGerenal,
            'user_stock' => $staffGerenal,
            'type' => $typeSuggestExporting,
            'date_convert_stock' => $dateGerenal,
            'warehouse_id' => 0,
            'save_and_warehouse' => $save_and_warehouse,
            'pois_id' => $sp_pois_id,
            'use_productions_plan' => $use_productions_plan,
            'cqi_id' => $sp_cqi_id
        ];

        $errors = '';
        // print_arrays($dataItems);
        $purchase_product_id = $this->stock_model->insertPurchaseProducts($purchases);
        if (!empty($purchase_product_id)) {
            updateReference('purchase_products');
            if ($sp_actions != 'qc') {
                if ($sp_actions != "products" || ($sp_actions == "products" && !empty($finished_productions))) {
                    $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                        'active' => 1,
                        'staff_active' => $staffGerenal,
                        'date_active' => $dateGerenal,
                    ]);
                }
            } else {
                $op = [
                    'pod_id' => $sp_pod_id,
                    'stage_id' => $stage_id,
                    'check_quality_items_id' => $sp_cqi_id,
                    'pois_id' => $sp_pois_id,
                    'type' => 3
                ];
                if (!empty($finished_productions)) {
                    $op['status'] = 1;
                    $op['staff_status'] = $staffGerenal;
                    $op['status_date'] = $dateGerenal;
                }
                if ($qc_remake_id) {
                    $up = $this->manufactures_model->updateQCRemake($qc_remake_id, $op);
                } else {
                    $ins = $this->manufactures_model->insertQCRemake($op);
                }
            }

            $suggest_exporting['purchase_product_id'] = $purchase_product_id;
            $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($suggest_exporting);
            if ($suggest_exporting_id) {
                updateReference('stock');
            }

            if (!empty($dataItems)) {
                foreach ($dataItems as $key => $value) {
                    $arrPOISub = $value['arrPOISub'];
                    $arrMaterials = $value['arrMaterials'];
                    unset($value['arrPOISub']);
                    unset($value['arrMaterials']);
                    $value['purchase_product_id'] = $suggest_exporting['purchase_product_id'];
                    $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                    if ($purchase_product_item_id) {
                        if (!empty($arrPOISub)) {
                            foreach ($arrPOISub as $k => $v) {
                                $arrPOISub[$k]['purchase_product_id'] = $purchase_product_id;
                                $arrPOISub[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                            }
                        }

                        if (!empty($arrMaterials)) {
                            foreach ($arrMaterials as $k => $v) {
                                $arrMaterials[$k]['suggest_exporting_id'] = $suggest_exporting_id;
                                $arrMaterials[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                            }
                        }
                        if (!empty($arrMaterials)) {
                            $this->manufactures_model->insertBatchSuggestExportingItems($arrMaterials);
                        }
                    }

                    if (!empty($arrPOISub)) {
                        $this->manufactures_model->insertBatchPurchaseProductPoisub($arrPOISub);
                    }
                }
            }

            //suggest exporting
            if (!empty($save_and_warehouse)) {
                $id = $suggest_exporting_id;
                $_data = array(
                    'warehouseman_id' => $staffGerenal,
                    'date_warehouseman' => date('Y-m-d H:i:s')
                );

                if (!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                    $errors = lang('test_quantyti_time_return');
                } else {
                    $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                    if ($success) {
                        log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                        $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                        $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                        $content = lang('tnh_his_warehouse_exporting_producion');
                        $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);

                        insertActivityLog([
                            'type_parent_obj' => 'exporting_producion',
                            'table_obj' => 'tbl_suggest_exporting',
                            'id_obj' => $id,
                            'name_obj' => $suggest_exporting['reference_stock'],
                            'content' => $content,
                            'actions' => 'warehouse'
                        ], $this->staffid);
                    }
                }
            }

            if ($final_stage) {
                $itemsPurchases = $this->stock_model->getPurchaseProductItems($purchase_product_id);
                foreach ($itemsPurchases as $k => $val) {
                    $this->manufactures_model->updateQuantityWarehoused($val['productions_orders_details_id'], $val['quantity'], $plus = 0);
                }
                noti_custom('pod', $sp_pod_id, $staffGerenal, $sp_pois_id, 'finishedPODOld');
            }

            $pod = $this->manufactures_model->rowProductionsOrdersDetais($sp_pod_id);
            $content = lang('tnh_his_add_exporting_producion');
            $content = str_replace('{$1}', $reference_suggest_exporting, $content);
            $content = str_replace('{$2}', $pod['reference_no'], $content);

            insertActivityLog([
                'type_parent_obj' => 'exporting_producion',
                'table_obj' => 'tbl_suggest_exporting',
                'id_obj' => $suggest_exporting_id,
                'name_obj' => $reference_suggest_exporting,
                'content' => $content,
                'actions' => 'add'
            ], $staffGerenal);

            //warehouses purchase products
            if (!empty($save_and_warehouse)) {
                $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_id);
                $_data = array(
                    'status' => 'approved',
                    'warehouseman_id' => $staffGerenal,
                    'date_warehouseman' => date('Y-m-d H:i:s')
                );
                $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_id));
                if ($success) {
                    log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_id);
                    $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_id);

                    //activity log
                    if (!empty($purchaseProduct['productions_orders_details_id'])) {
                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                        $content = lang('tnh_his_warehouse_purchase_product_pod');
                        $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                    } else {
                        $content = lang('tnh_his_warehouse_purchase_product');
                        $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                    }
                    insertActivityLog([
                        'type_parent_obj' => 'purchase_products',
                        'table_obj' => 'tbl_purchase_products',
                        'id_obj' => $purchase_product_id,
                        'name_obj' => $purchaseProduct['reference_no'],
                        'content' => $content,
                        'actions' => 'warehouse'
                    ], $staffGerenal);
                    //end activity log
                }
            }

            $content = lang('tnh_his_add_purchase_product');
            $content = str_replace('{$1}', $reference_purchase, $content);
            insertActivityLog([
                'type_parent_obj' => 'purchase_products',
                'table_obj' => 'tbl_purchase_products',
                'id_obj' => $purchase_product_id,
                'name_obj' => $reference_purchase,
                'content' => $content,
                'actions' => 'add'
            ], $staffGerenal);

            //handling bom
            if (!empty($arrSaveBom)) {
                $this->products_model->handlingBOMPOD($arrSaveBom);
            }

            noti_custom('qc_po_detail', $sp_pod_id, $staffGerenal, 0, 'finishedQCPODetail', [
                'arrPOIS' => [$sp_pois_id],
                'pod_id' => $sp_pod_id,
                'stage_id' => $stage_id,
            ]);

            $data['result'] = 1;
            $data['message'] = lang('success');
            $data['error'] = $errors;
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function getStagesParent($id = 0) {
        // $this->db->select('tbl_stages.*', false);
        // $this->db->from('tbl_stages');
        // $this->db->where('tbl_stages.parent_id', 0);
        // $stages = $this->db->get()->result_array();
        $stages = $this->manufactures_model->getStagesPO($id);
        $data['stages'] = $stages;
        $data['STAGE_PRINT_BARCODE'] = STAGE_PRINT_BARCODE;
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function clickActiveStages() {
        $data = [];
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $stage_id = $data_post['stage_id'];
        $productions_orders_id = $data_post['productions_orders_id'];
        $warehouses = $this->stock_model->getWarehouses(false, [WAREHOUSES_CAPACITY], $this->staffid);

        $stage = get_table_where('tbl_stages', ['id' => $stage_id], '', 'row_array', 'name, type');

        $tbPOStages = "(
            SELECT
                tbl_productions_orders_items_stages.id as pois_id,
                tbl_productions_orders_items_stages.productions_orders_items_id as poi_id,
                tbl_productions_orders_items_stages.number as number,
                tbl_productions_orders_items_stages.active
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.productions_orders_id = '$productions_orders_id' AND tbl_productions_orders_items_stages.stage_id = '$stage_id' AND tbl_productions_orders_items_stages.active = 0
        ) tb_po_stages";
        
        $this->db->select('
                tbl_productions_orders_details.id as pod_id,
                tbl_productions_orders_items.id as poi_id,
                tb_po_stages.pois_id as pois_id,
                tbl_products.images as images,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tbl_productions_orders_items.quantity as quantity,
                tblsize.name as size_name,
                tb_po_stages.number as number,
            ');
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
        $this->db->join($tbPOStages, 'tb_po_stages.poi_id = tbl_productions_orders_items.id');
        $this->db->join('tblsize', 'tblsize.id = tbl_products.size', 'left');
        $this->db->where('tbl_productions_orders_details.productions_orders_id', $productions_orders_id);
        $productions_orders_details = $this->db->get()->result_array();

        if (!empty($productions_orders_details)) {
            $this->db->select('count(tbl_productions_orders_items_stages.id) as ct', false);
            $this->db->from('tbl_productions_orders_items_stages');
            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
            $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_details[0]['poi_id']);
            $this->db->where('tbl_productions_orders_items_stages.number <', $productions_orders_details[0]['number']);
            $this->db->where_in('tbl_stages.type', [2]);
            $ct = $this->db->get()->row_array()['ct'];
            if ($ct) {
                $stage['type'] = 3;
            }
        }

        $arrPOISQC = [];
        if (!empty($productions_orders_details)) {
            foreach ($productions_orders_details as $key => $value) {
                $pod_id = $value['pod_id'];
                $pois_id = $value['pois_id'];
                $poi_id = $value['poi_id'];
                $images = $value['images'];
                $number = $value['number'];

                if (!empty($images)) {
                    $images = base_url('uploads/products/' . $images);
                }  else {
                    $images = null;
                }
                $productions_orders_details[$key]['images'] = $images;
                $qc = $this->manufactures_model->isQCPre($pod_id, $poi_id, $pois_id, $number);
                $message_qc = '';
                if ($qc == 1) {
                    $message_qc = lang('tnh_da_qc_trc');
                } else if ($qc == 2 || $qc == 3) {
                    $message_qc = lang('tnh_chua_qc_trc');
                    $arrPOISQC[] = $pois_id;
                }
                $productions_orders_details[$key]['qc'] = $qc;
                $productions_orders_details[$key]['arrPOISQC'] = $arrPOISQC;
                $productions_orders_details[$key]['message_qc'] = $message_qc;
            }
        }

        $data['productions_orders_details'] = $productions_orders_details;
        $data['message'] = '';
        if (empty($productions_orders_details)) {
            $data['message'] = lang('Không có mặt hàng chưa hoàn thành');
        }
        $data['warehouses'] = $warehouses;
        $data['stage'] = $stage;
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function agreeProcessMultiple() {
        $data = [];
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $arrPOIS = $data_post['arrPOIS'];
        if (empty($arrPOIS)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn mặt hàng muốn hoàn thành');
            echo json_encode($data); die;
        }
        $active = 1;
        $staff_active = $this->staffid;
        $date_active = date('Y-m-d H:i:s');
        $isSuccess = false;
        $productions_orders_id = 0;
        $stage_id = 0;

        if (!empty($arrPOIS)) {
            foreach ($arrPOIS as $key => $value) {
                $pois_id = $value;
                if ($active == 1) {
                    $this->db->select('
                        tbl_productions_orders_items_stages.id as id,
                        tbl_stages.name as stage_name, 
                        tbl_productions_orders_items_stages.active as active, 
                        tbl_productions_orders_items_stages.date_active as date_active,
                        tbl_productions_orders_items_stages.staff_active as staff_active,
                        CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                        tblstaff.profile_image as profile_image,
                        tbl_stages.status_qc as status_qc,
                        tbl_stages.id as stage_id,
                        tbl_productions_orders_items_stages.final_stage as final_stage,
                        tbl_stages.type as type,
                        tbl_productions_orders_items_stages.begin_productions as begin_productions,
                        tbl_productions_orders_items_stages.date_productions as date_productions,
                        tbl_productions_orders_items_stages.staff_productions as staff_productions,
                        CONCAT(staff_productions.firstname, " ", staff_productions.lastname) as staff_name_productions,
                        staff_productions.profile_image as profile_image_productions,
                        tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                        tbl_productions_orders_items_stages.productions_orders_id as productions_orders_id,
                        tbl_productions_orders_items_stages.number as number
                    ', false);
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                    $this->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
                    $this->db->join('tblstaff staff_productions', 'staff_productions.staffid = tbl_productions_orders_items_stages.staff_productions', 'left');
                    $this->db->where('tbl_productions_orders_items_stages.id', $pois_id);
                    $dtPois = $this->db->get()->row_array();
                    if (!empty($dtPois)) {

                        $productions_orders_id = $dtPois['productions_orders_id'];
                        $stage_id = $dtPois['stage_id'];
                        $qc = $this->manufactures_model->isQCPre(0, $dtPois['productions_orders_items_id'], 0, $dtPois['number']);
                        if ($qc == 2 || $qc == 3) {
                            $data['result'] = 0;
                            $data['message'] = lang('Có mặt hàng chưa QC công đoạn trước hoặc chưa đạt');
                            echo json_encode($data); die;
                        }

                        $this->db->select('count(tbl_productions_orders_items_stages.id) as ct', false);
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $dtPois['productions_orders_items_id']);
                        $this->db->where('tbl_productions_orders_items_stages.number <', $dtPois['number']);
                        $this->db->where_in('tbl_stages.type', [2]);
                        $ct = $this->db->get()->row_array()['ct'];

                        if ($dtPois['type'] || !empty($ct)) {
                            continue;
                        }
                    }
                }

                $up = $this->manufactures_model->updateProductionsOrderItemsStages($pois_id, [
                    'active' => $active,
                    'staff_active' => $staff_active,
                    'date_active' => $date_active,
                ]);
                if (!empty($up)) {
                    $isSuccess = true;
                }
            }
        }

        if (!empty($isSuccess)) {
            noti_custom('qc_po', $productions_orders_id, $staff_active, 0, 'finishedQCPO', [
                'arrPOIS' => $arrPOIS,
                'po_id' => $productions_orders_id,
                'stage_id' => $stage_id,
            ]);

            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function agreeProcessSemiProduct() {
        $data = [];
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $data['result'] = 0;
        $data['message'] = lang('Chức năng này không còn được sử dụng');
        echo json_encode($data); die;

        $warehouse_import = $data_post['warehouse_import'];
        if (empty($warehouse_import)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn kho bán thành phẩm');
            echo json_encode($data); die;
        }

        $arrPOIS = $data_post['arrPOIS'];
        if (empty($arrPOIS)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn mặt hàng muốn hoàn thành');
            echo json_encode($data); die;
        }

        $arrData = [];
        $arrErrors = [];
        $arrSumMaterials = [];
        foreach($arrPOIS as $key => $pois_id) {
            $pois = $this->manufactures_model->getProductionsOrderItemsStagesById($pois_id);
            if (!empty($pois)) {
                $active = $pois['active'];
                if ($active) {
                    continue;
                }

                $productions_orders_id = $pois['productions_orders_id'];
                $productions_orders_items_id = $pois['productions_orders_items_id'];
                
                $this->db->select('
                    tbl_productions_orders_details.id as pod_id,
                ', false);
                $this->db->from('tbl_productions_orders_items');
                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
                $this->db->where('tbl_productions_orders_items.id', $productions_orders_items_id);
                $pod = $this->db->get()->row_array();
                $pod_id = $pod['pod_id'];
                $productions_plan = $this->manufactures_model->getProductionsPlanPOD($pod_id);
                $production_plan_id = $productions_plan['id'];
                $productions_orders_subs = $this->manufactures_model->loadDataSemiProducts($pod_id, $production_plan_id);
                $flagSuccess = true;
                if (!empty($productions_orders_subs)) {
                    foreach ($productions_orders_subs as $k => $val) {
                        if (empty($val['subItems'])) {
                            $arrErrors[$pois_id] = [
                                'pois_id' => $pois_id,
                                'pod_id' => $pod_id,
                                'message' => lang('Không có nguyên vật liệu để xuất bán thành phẩm')
                            ];
                            $flagSuccess = false;
                            break;
                        } else {
                            foreach ($val['subItems'] as $kS => $vS) {
                                $item_cs_id = $vS['item_cs_id'].'__'.$production_plan_id;
                                $quantity_primary = $vS['quantity_primary'];
                                if (empty($vS['warehousePlan']) || $vS['warehousePlan'] == 'null') {
                                    $arrErrors[$pois_id] = [
                                        'pois_id' => $pois_id,
                                        'pod_id' => $pod_id,
                                        'message' => lang('NVL hoặc BTP không đủ để xuất kho sản xuất')
                                    ];
                                    $flagSuccess = false;
                                    break;
                                }

                                if (empty($arrSumMaterials[$item_cs_id])) {
                                    $arrSumMaterials[$item_cs_id] = [
                                        'item_cs_id' => $item_cs_id,
                                        'production_plan_id' => $production_plan_id,
                                        'item_type' => $vS['item_type'],
                                        'item_code' => $vS['item_code'],
                                        'item_name' => $vS['item_name'],
                                        'item_id' => $vS['item_id'],
                                        'images' => $vS['images'],
                                        'quantity_primary' => $vS['quantity_primary'],
                                    ];
                                } else {
                                    $arrSumMaterials[$item_cs_id]['quantity_primary'] = $arrSumMaterials[$item_cs_id]['quantity_primary'] + $quantity_primary;
                                }
                            }
                        }
                    }
                } else {
                    $arrErrors[$pois_id] = [
                        'pois_id' => $pois_id,
                        'pod_id' => $pod_id,
                        'message' => lang('Không có bán thành phẩm để hoàn thành')
                    ];
                    continue;
                }

                if ($flagSuccess) {
                    $arrData[$pois_id]['pod_id'] = $pod_id;
                    $arrData[$pois_id]['pois_id'] = $pois_id;
                    $arrData[$pois_id]['stage_id'] = $pois['stage_id'];
                    $arrData[$pois_id]['productions_orders_subs'] = $productions_orders_subs;
                }
            }
        }

        //check warehouses
        $warehouse_id = WAREHOUSES_CAPACITY;
        $errorsWarehouses = [];
        if (!empty($arrSumMaterials)) {
            foreach ($arrSumMaterials as $key => $value) {
                $item_type = $value['item_type'];
                $item_id = $value['item_id'];
                $item_code = $value['item_code'];
                $quantity = $value['quantity_primary'];
                $production_plan_id = $value['production_plan_id'];
                $isQuantityWarehouses = $this->manufactures_model->isQuantityWarehouses($item_id, $item_type, $quantity, $warehouse_id, $production_plan_id);
                if (!empty($isQuantityWarehouses)) {
                    $warehouse_name = $isQuantityWarehouses['warehouses_name'];
                    $location_name = $isQuantityWarehouses['location_name'];

                    $errorsWarehouses[$key] = $value;
                    $errorsWarehouses[$key]['warehouse_name'] = $warehouse_name;
                    $errorsWarehouses[$key]['location_name'] = $location_name;
                    $errorsWarehouses[$key]['quantity_lack'] = $quantity - (float)$isQuantityWarehouses['product_quantity'];
                    $errorsWarehouses[$key]['link_image'] = base_url('assets/uploads');

                    // $errorsWarehouses= '<div>NVL ['.$item_code.'] trong kho ['.$warehouse_name.'] vị trí ['.$location_name.'] không đủ số lượng '.formatNumber($quantity).'</div>';
                }
            }
        }

        if (!empty($errorsWarehouses) || !empty($arrErrors)) {
            $data['result'] = 2;
            $data['errorsWarehouses'] = $errorsWarehouses;
            $data['arrErrors'] = $arrErrors;
            $data['message'] = lang('Không đủ kho hoặc BTP không có NVL xuất');
            echo json_encode($data, JSON_UNESCAPED_UNICODE); die;
        }

        $data['result'] = 0;
        $data['message'] = lang('fail');

        $flagSuccess = false;
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $sp_pod_id = $v['pod_id'];
                $sp_pois_id = $v['pois_id'];
                
                $dtPois = get_table_where('tbl_productions_orders_items_stages', ['id' => $sp_pois_id], "", "row_array", 'stage_id, active');
                $active = $dtPois['active'];
                if ($active) {
                    continue;
                }
                $stage_id = $dtPois['stage_id'];
                $warehouses_semi_product = $warehouse_import;
                $use_productions_plan = 1;
                $localtion_semi_product = $this->site_model->getLocationPOD($sp_pod_id, $warehouses_semi_product);
                $dataItems = [];
                $total_quantity_purchases = 0;
                $count_quantity_purchases = 0;
                $total_quantity_se = 0;
                $total_quantity_exchange_se = 0;
                $count_quantity_se = 0;
                $items = $v['productions_orders_subs'];
                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        $item_type_sp = "products";
                        $arr_id_sp = explode('__', $value['id']);
                        $type_order_sp = $arr_id_sp[0];
                        $item_id_sp = $arr_id_sp[1];
                        $info = $this->products_model->rowProduct($item_id_sp);
                        if (empty($info)) {
                            continue;
                        }
                        $item_code_sp = $info['code']; 
                        $item_name_sp = $info['name']; 

                        $quantity_exchange_sp = number_unformat($value['quantity_exchange']);
                        $quantity_single_sp = number_unformat($value['quantity_single']);
                        $quantity_semi_product = number_unformat($value['quantity']);
                        $unit_id_sp = $value['unit_id'];
                        $unit_parent_id_sp = $value['unit_parent_id'];

                        $materials = !empty($value['subItems']) ? $value['subItems'] : null;
                        $is_save_dm = 0;
                        $arrMaterials = [];
                        $arrMaterialsBOM = [];
                        if (!empty($materials)) {
                            foreach ($materials as $k => $v) {
                                $quantity_exchange_mt = number_unformat($v['quantity_exchange']);
                                if (empty($quantity_exchange_mt)) $quantity_exchange_mt = 1;
                                $quantity_single_mt = number_unformat($v['quantity_single']);
                                $arr_item_cs_id_mt = explode('__', $v['item_cs_id']);
                                $item_id_mt = $arr_item_cs_id_mt[1];
                                $item_type_mt = $arr_item_cs_id_mt[0];
                                $unit_id = $v['unit_id'];
                                $unit_parent_id = $v['unit_parent_id'];
                                if ($item_type_mt == "materials") {
                                    $info_mt = $this->items_model->rowMaterial($item_id_mt);
                                } else if ($item_type_mt == "tools_supplies") {
                                    $info_mt = $this->tools_supplies_model->rowToolsSupplies($item_id_mt);
                                }  else {
                                    $info_mt = $this->products_model->rowProduct($item_id_mt);
                                }

                                if (empty($info_mt)) {
                                    continue;
                                }

                                $quantity_materials_mt = $v['quantity'];
                                $unit_id_mt = $v['unit_id'];
                                $unit_parent_id_mt = $v['unit_parent_id'];
                                $warehouses_items_mt = $v['warehousePlan'];
                                if (empty($warehouses_items_mt)) {
                                    continue;
                                } else if ($warehouses_items_mt) {
                                    $warehouses_items_mt = [json_decode($warehouses_items_mt, true)];
                                    foreach ($warehouses_items_mt as $kW => $vW) {
                                        if (empty($vW)) {
                                            continue;
                                        }
                                        $vW = explode('__', $vW['id']);
                                        $warehouse_item_id = $vW[0];
                                        $location_id = $vW[1];
                                        $quantity_items = $quantity_materials_mt;
                                        $quantity_exchange_se =  $quantity_items/$quantity_exchange_mt;
                                        $arrMaterials[] = [
                                            'type_item' => $item_type_mt,
                                            'item_id' => $item_id_mt,
                                            'item_code' => $info_mt['code'],
                                            'item_name' => $info_mt['name'],
                                            'unit_id' => $unit_id_mt,
                                            'quantity_export' => $quantity_items,
                                            'unit_parent_id' => $unit_parent_id_mt,
                                            'number_exchange' => $quantity_exchange_mt,
                                            'quantity_exchange' => $quantity_exchange_se,
                                            'location_id' => $location_id,
                                            'warehouse_item_id' => $warehouse_item_id,
                                        ];
                                        $total_quantity_se+= $quantity_items;
                                        $total_quantity_exchange_se+= $quantity_exchange_se;
                                        $count_quantity_se++;
                                    }
                                }

                                if ($is_save_dm) {
                                    $arrMaterialsBOM[] = [
                                        'type' => $item_type_mt,
                                        'item_id' => $item_id_mt,
                                        'unit_id' => $unit_id_mt,
                                        'quantity' => $quantity_single_mt,
                                    ];
                                }
                            }
                        }

                        $poisub_id = $value['poisub_id'];
                        $arrPOISub = [];
                        if (!empty($poisub_id)) {
                            $tempArrPOISub = explode(',', $poisub_id);
                            foreach ($tempArrPOISub as $k => $v) {
                                $arrPOISub[$k]['poisub_id'] = $v;
                            }
                        }

                        $dataItems[] = [
                            'productions_orders_details_id' => $sp_pod_id,
                            'type_item' => $item_type_sp,
                            'item_id' => $item_id_sp,
                            'location_id' => $localtion_semi_product,
                            'item_code' => $item_code_sp,
                            'item_name' => $item_name_sp,
                            'quantity' => $quantity_semi_product,
                            'quantity_exchange' => $quantity_exchange_sp,
                            'quantity_single' => $quantity_single_sp,
                            'quantity_semi_product' => $quantity_semi_product,
                            'type_order' => $type_order_sp,
                            'arrPOISub' => $arrPOISub,
                            'arrMaterials' => $arrMaterials
                        ];
                        $total_quantity_purchases+= $quantity_semi_product;
                        $count_quantity_purchases++;
                    }
                }

                if (empty($dataItems)) {
                    continue;
                }

                $dateGerenal = date('Y-m-d H:i:s');
                $staffGerenal = $this->staffid;
                $reference_purchase = getReference('purchase_products');
                $sp_type = 1;
                $purchases = [
                    'reference_no' => $reference_purchase,
                    'date' => $dateGerenal,
                    'productions_orders_details_id' => $sp_pod_id,
                    'warehouse_id' => $warehouses_semi_product,
                    'count_items' => $count_quantity_purchases,
                    'total_quantity' => $total_quantity_purchases,
                    'created_by' => $staffGerenal,
                    'date_created' => $dateGerenal,
                    'status' => 'un_approved',
                    'pois_id' => $sp_pois_id,
                    'type' => 2,
                    'sp_type' => $sp_type
                ];

                $save_and_warehouse = 1;
                $export_name = 'Xuất kho NVL';
                $reference_suggest_exporting = getReference('stock');
                $suggest_exporting = [
                    'productions_orders_details_id' => $sp_pod_id,
                    'reference_no' => null,
                    'reference_stock' => $reference_suggest_exporting,
                    'date' => $dateGerenal,
                    'export_name' => $export_name,
                    'note' => '',
                    'status' => 'un_approved',
                    'total_quantity' => $total_quantity_se,
                    'count_items' => $count_quantity_se,
                    'total_quantity_exchange' => $total_quantity_exchange_se,
                    'created_by' => $staffGerenal,
                    'date_created' => $dateGerenal,
                    'convert_stock_by' => $staffGerenal,
                    'date_convert_stock' => $dateGerenal,
                    'status_stock' => 'approved',
                    'date_stock' => $dateGerenal,
                    'user_stock' => $staffGerenal,
                    'type' => '5',
                    'date_convert_stock' => $dateGerenal,
                    'warehouse_id' => 0,
                    'save_and_warehouse' => $save_and_warehouse,
                    'pois_id' => $sp_pois_id,
                    'use_productions_plan' => $use_productions_plan,
                ];

                $errors = '';
                $purchase_product_id = $this->stock_model->insertPurchaseProducts($purchases);
                if (!empty($purchase_product_id)) {
                    updateReference('purchase_products');
                    $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                        'active' => 1,
                        'staff_active' => $staffGerenal,
                        'date_active' => $dateGerenal,
                    ]);

                    $suggest_exporting['purchase_product_id'] = $purchase_product_id;
                    $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($suggest_exporting);
                    if ($suggest_exporting_id) {
                        updateReference('stock');
                    }

                    if (!empty($dataItems)) {
                        foreach ($dataItems as $key => $value) {
                            $arrPOISub = $value['arrPOISub'];
                            $arrMaterials = $value['arrMaterials'];
                            unset($value['arrPOISub']);
                            unset($value['arrMaterials']);
                            $value['purchase_product_id'] = $suggest_exporting['purchase_product_id'];
                            $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                            if ($purchase_product_item_id) {
                                if (!empty($arrPOISub)) {
                                    foreach ($arrPOISub as $k => $v) {
                                        $arrPOISub[$k]['purchase_product_id'] = $purchase_product_id;
                                        $arrPOISub[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                    }
                                }

                                if (!empty($arrMaterials)) {
                                    foreach ($arrMaterials as $k => $v) {
                                        $arrMaterials[$k]['suggest_exporting_id'] = $suggest_exporting_id;
                                        $arrMaterials[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                    }
                                }
                                if (!empty($arrMaterials)) {
                                    $this->manufactures_model->insertBatchSuggestExportingItems($arrMaterials);
                                }

                            }

                            if (!empty($arrPOISub)) {
                                $this->manufactures_model->insertBatchPurchaseProductPoisub($arrPOISub);
                            }
                        }
                    }

                    //suggest exporting
                    if (!empty($save_and_warehouse)) {
                        $id = $suggest_exporting_id;
                        $_data = array(
                            'warehouseman_id' => $this->staffid,
                            'date_warehouseman' => date('Y-m-d H:i:s')
                        );

                        if(!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                            $errors.= '<div>'.lang('test_quantyti_time_return').'</div>';
                        } else {
                            $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                            if ($success) {
                                log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                                $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                                $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                                $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                                $content = lang('tnh_his_warehouse_exporting_producion');
                                $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                                $content = str_replace('{$2}', $pod['reference_no'], $content);

                                insertActivityLog([
                                    'type_parent_obj' => 'exporting_producion',
                                    'table_obj' => 'tbl_suggest_exporting',
                                    'id_obj' => $id,
                                    'name_obj' => $suggest_exporting['reference_stock'],
                                    'content' => $content,
                                    'actions' => 'warehouse'
                                ], $staffGerenal);
                            }
                        }
                    }

                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($sp_pod_id);
                    $content = lang('tnh_his_add_exporting_producion');
                    $content = str_replace('{$1}', $reference_suggest_exporting, $content);
                    $content = str_replace('{$2}', $pod['reference_no'], $content);

                    insertActivityLog([
                        'type_parent_obj' => 'exporting_producion',
                        'table_obj' => 'tbl_suggest_exporting',
                        'id_obj' => $suggest_exporting_id,
                        'name_obj' => $reference_suggest_exporting,
                        'content' => $content,
                        'actions' => 'add'
                    ], $staffGerenal);

                    //warehouses purchase products
                    if (!empty($save_and_warehouse)) {
                        $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_id);
                        $_data = array(
                            'status' => 'approved',
                            'warehouseman_id' => $this->staffid,
                            'date_warehouseman' => date('Y-m-d H:i:s')
                        );
                        $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_id));
                        if ($success) {
                            log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_id);
                            $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_id);

                            //activity log
                            if (!empty($purchaseProduct['productions_orders_details_id']))
                            {
                                $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                                $content = lang('tnh_his_warehouse_purchase_product_pod');
                                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                $content = str_replace('{$2}', $pod['reference_no'], $content);
                            } else {
                                $content = lang('tnh_his_warehouse_purchase_product');
                                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                            }
                            insertActivityLog([
                                'type_parent_obj' => 'purchase_products',
                                'table_obj' => 'tbl_purchase_products',
                                'id_obj' => $purchase_product_id,
                                'name_obj' => $purchaseProduct['reference_no'],
                                'content' => $content,
                                'actions' => 'warehouse'
                            ], $staffGerenal);
                            //end activity log
                        }
                    }

                    $content = lang('tnh_his_add_purchase_product');
                    $content = str_replace('{$1}', $reference_purchase, $content);
                    insertActivityLog([
                        'type_parent_obj' => 'purchase_products',
                        'table_obj' => 'tbl_purchase_products',
                        'id_obj' => $purchase_product_id,
                        'name_obj' => $reference_purchase,
                        'content' => $content,
                        'actions' => 'add'
                    ], $staffGerenal);

                    //handling bom
                    if (!empty($arrSaveBom)) {
                        $this->products_model->handlingBOMPOD($arrSaveBom);
                    }
                    $flagSuccess = true;
                }
            }
        }

        if ($flagSuccess == true) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        }
        echo json_encode($data);
    }

    public function agreeProcessProduct() {
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $warehouse_import = $data_post['warehouse_import'];
        if (empty($warehouse_import)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn kho thành phẩm');
            echo json_encode($data); die;
        }

        $arrPOIS = $data_post['arrPOIS'];
        if (empty($arrPOIS)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn mặt hàng muốn hoàn thành');
            echo json_encode($data); die;
        }
        $type = 1;

        $arrData = [];
        $arrErrors = [];
        $arrSumMaterials = [];
        $productions_orders_id = 0;
        $stage_id = 0;
        foreach($arrPOIS as $key => $pois_id) {
            $pois = $this->manufactures_model->getProductionsOrderItemsStagesById($pois_id);
            if (!empty($pois)) {
                $active = $pois['active'];
                if ($active) {
                    continue;
                }

                $productions_orders_id = $pois['productions_orders_id'];
                $productions_orders_items_id = $pois['productions_orders_items_id'];

                $qc = $this->manufactures_model->isQCPre(0, $pois['productions_orders_items_id'], 0, $pois['number']);
                if ($qc == 2 || $qc == 3) {
                    $data['result'] = 0;
                    $data['message'] = lang('Có mặt hàng chưa QC công đoạn trước hoặc chưa đạt');
                    echo json_encode($data); die;
                }
                
                $this->db->select('
                    tbl_productions_orders_details.id as pod_id,
                ', false);
                $this->db->from('tbl_productions_orders_items');
                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
                $this->db->where('tbl_productions_orders_items.id', $productions_orders_items_id);
                $pod = $this->db->get()->row_array();
                $pod_id = $pod['pod_id'];
                $productions_plan = $this->manufactures_model->getProductionsPlanPOD($pod_id);
                $production_plan_id = $productions_plan['id'];

                $productions_orders_subs = $this->manufactures_model->loadDataProducts($pod_id, $production_plan_id);
                // print_arrays($productions_orders_subs);
                $flagSuccess = true;
                if (!empty($productions_orders_subs)) {
                    foreach ($productions_orders_subs as $k => $val) {
                        if (empty($val['subItems'])) {
                            $arrErrors[$pois_id] = [
                                'pois_id' => $pois_id,
                                'pod_id' => $pod_id,
                                'message' => lang('Không có bán thành phẩm để xuất')
                            ];
                            $flagSuccess = false;
                            break;
                        } else {
                            foreach ($val['subItems'] as $kS => $vS) {
                                $quantity_primary = $vS['quantity_primary'];
                                if (empty($vS['warehousePlan']) || $vS['warehousePlan'] == 'null') {
                                    $arrErrors[$pois_id] = [
                                        'pois_id' => $pois_id,
                                        'pod_id' => $pod_id,
                                        'message' => lang('NVL hoặc BTP không đủ để xuất kho sản xuất')
                                    ];
                                    $flagSuccess = false;
                                    break;
                                }

                                $warehouse_plan = json_decode($vS['warehousePlan'], true);
                                $w_id = $warehouse_plan['id'];
                                $item_cs_id = $vS['item_cs_id'].'__'.$w_id;
                                if (empty($arrSumMaterials[$item_cs_id])) {
                                    $arrSumMaterials[$item_cs_id] = [
                                        'item_cs_id' => $item_cs_id,
                                        'w_id' => $w_id,
                                        'pod_id' => $pod_id,
                                        'production_plan_id' => $production_plan_id,
                                        'item_type' => $vS['item_type'],
                                        'item_code' => $vS['item_code'],
                                        'item_name' => $vS['item_name'],
                                        'item_id' => $vS['item_id'],
                                        'images' => $vS['images'],
                                        'quantity_primary' => $vS['quantity_primary'],
                                    ];
                                } else {
                                    $arrSumMaterials[$item_cs_id]['quantity_primary'] = $arrSumMaterials[$item_cs_id]['quantity_primary'] + $quantity_primary;
                                }
                            }
                        }
                    }
                } else {
                    $arrErrors[$pois_id] = [
                        'pois_id' => $pois_id,
                        'pod_id' => $pod_id,
                        'message' => lang('Không có thành phẩm để hoàn thành')
                    ];
                    continue;
                }

                if ($flagSuccess) {
                    $arrData[$pois_id]['pod_id'] = $pod_id;
                    $arrData[$pois_id]['pois_id'] = $pois_id;
                    $arrData[$pois_id]['stage_id'] = $pois['stage_id'];
                    $arrData[$pois_id]['productions_orders_subs'] = $productions_orders_subs;
                }
            }
        }

        $errorsWarehouses = [];
        if (!empty($arrSumMaterials)) {
            foreach ($arrSumMaterials as $key => $value) {
                $item_type = $value['item_type'];
                $item_id = $value['item_id'];
                $item_code = $value['item_code'];
                $quantity = $value['quantity_primary'];
                $production_plan_id = $value['production_plan_id'];
                $w_id = explode('__', $value['w_id']);
                if (empty($w_id[1])) {
                    $data['result'] = 0;
                    $data['message'] = lang('NVL hoặc BTP không đủ để xuất kho sản xuất');
                    echo json_encode($data, JSON_UNESCAPED_UNICODE); die;
                }

                $warehouse_id = $w_id[0];
                $location_id = $w_id[1];
                

                $isQuantityWarehouses = $this->manufactures_model->isQuantityWarehouses($item_id, $item_type, $quantity, $warehouse_id, 0, $location_id);
                if (!empty($isQuantityWarehouses)) {
                    $warehouse_name = $isQuantityWarehouses['warehouses_name'];
                    $location_name = $isQuantityWarehouses['location_name'];
                    $errorsWarehouses[$key] = $value;
                    $errorsWarehouses[$key]['warehouse_name'] = $warehouse_name;
                    $errorsWarehouses[$key]['location_name'] = $location_name;
                    $errorsWarehouses[$key]['quantity_lack'] = $quantity - (float)$isQuantityWarehouses['product_quantity'];
                    $errorsWarehouses[$key]['link_image'] = base_url('assets/uploads');
                }
            }
        }

        if (!empty($errorsWarehouses) || !empty($arrErrors)) {
            $data['result'] = 2;
            $data['errorsWarehouses'] = $errorsWarehouses;
            $data['arrErrors'] = $arrErrors;
            $data['message'] = lang('Không đủ kho hoặc thành phẩm không có BTP xuất');
            echo json_encode($data, JSON_UNESCAPED_UNICODE); die;
        }

        $data['result'] = 0;
        $data['message'] = lang('fail');

        $flagSuccess = false;
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $sp_pod_id = $v['pod_id'];
                $sp_pois_id = $v['pois_id'];
                
                $dtPois = get_table_where('tbl_productions_orders_items_stages', ['id' => $sp_pois_id], "", "row_array", 'stage_id, active');
                $active = $dtPois['active'];
                if ($active) {
                    continue;
                }
                $stage_id = $dtPois['stage_id'];
                $warehouses_semi_product = $warehouse_import;
                $use_productions_plan = 1;
                $localtion_semi_product = $this->site_model->getLocationPOD($sp_pod_id, $warehouses_semi_product, $stage_id);
                $dataItems = [];
                $total_quantity_purchases = 0;
                $count_quantity_purchases = 0;
                $total_quantity_se = 0;
                $total_quantity_exchange_se = 0;
                $count_quantity_se = 0;
                $items = $v['productions_orders_subs'];
                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        $item_type_sp = "products";
                        $arr_id_sp = explode('__', $value['id']);
                        $type_order_sp = $arr_id_sp[0];
                        $item_id_sp = $arr_id_sp[1];
                        $info = $this->products_model->rowProduct($item_id_sp);
                        if (empty($info)) {
                            continue;
                        }
                        $item_code_sp = $info['code']; 
                        $item_name_sp = $info['name']; 

                        $quantity_exchange_sp = number_unformat($value['quantity_exchange']);
                        $quantity_single_sp = number_unformat($value['quantity_single']);
                        $quantity_semi_product = number_unformat($value['quantity']);
                        $unit_id_sp = $value['unit_id'];
                        $unit_parent_id_sp = $value['unit_parent_id'];

                        $materials = !empty($value['subItems']) ? $value['subItems'] : null;
                        $is_save_dm = 0;
                        $arrMaterials = [];
                        $arrMaterialsBOM = [];
                        if (!empty($materials)) {
                            foreach ($materials as $k => $v) {
                                $quantity_exchange_mt = number_unformat($v['quantity_exchange']);
                                if (empty($quantity_exchange_mt)) $quantity_exchange_mt = 1;
                                $quantity_single_mt = number_unformat($v['quantity_single']);
                                $arr_item_cs_id_mt = explode('__', $v['item_cs_id']);
                                $item_id_mt = $arr_item_cs_id_mt[1];
                                $item_type_mt = $arr_item_cs_id_mt[0];
                                $unit_id = $v['unit_id'];
                                $unit_parent_id = $v['unit_parent_id'];
                                if ($item_type_mt == "materials") {
                                    $info_mt = $this->items_model->rowMaterial($item_id_mt);
                                } else if ($item_type_mt == "tools_supplies") {
                                    $info_mt = $this->tools_supplies_model->rowToolsSupplies($item_id_mt);
                                }  else {
                                    $info_mt = $this->products_model->rowProduct($item_id_mt);
                                }

                                if (empty($info_mt)) {
                                    continue;
                                }

                                $quantity_materials_mt = $v['quantity'];
                                $unit_id_mt = $v['unit_id'];
                                $unit_parent_id_mt = $v['unit_parent_id'];
                                $warehouses_items_mt = $v['warehousePlan'];
                                if (empty($warehouses_items_mt)) {
                                    continue;
                                } else if ($warehouses_items_mt) {
                                    $warehouses_items_mt = [json_decode($warehouses_items_mt, true)];
                                    foreach ($warehouses_items_mt as $kW => $vW) {
                                        if (empty($vW)) {
                                            continue;
                                        }
                                        $vW = explode('__', $vW['id']);
                                        $warehouse_item_id = $vW[0];
                                        $location_id = $vW[1];
                                        $quantity_items = $quantity_materials_mt;
                                        $quantity_exchange_se =  $quantity_items/$quantity_exchange_mt;
                                        $arrMaterials[] = [
                                            'type_item' => $item_type_mt,
                                            'item_id' => $item_id_mt,
                                            'item_code' => $info_mt['code'],
                                            'item_name' => $info_mt['name'],
                                            'unit_id' => $unit_id_mt,
                                            'quantity_export' => $quantity_items,
                                            'unit_parent_id' => $unit_parent_id_mt,
                                            'number_exchange' => $quantity_exchange_mt,
                                            'quantity_exchange' => $quantity_exchange_se,
                                            'location_id' => $location_id,
                                            'warehouse_item_id' => $warehouse_item_id,
                                        ];
                                        $total_quantity_se+= $quantity_items;
                                        $total_quantity_exchange_se+= $quantity_exchange_se;
                                        $count_quantity_se++;
                                    }
                                }

                                if ($is_save_dm) {
                                    $arrMaterialsBOM[] = [
                                        'type' => $item_type_mt,
                                        'item_id' => $item_id_mt,
                                        'unit_id' => $unit_id_mt,
                                        'quantity' => $quantity_single_mt,
                                    ];
                                }
                            }
                        }

                        $poisub_id = $value['poisub_id'];
                        $arrPOISub = [];
                        if (!empty($poisub_id)) {
                            $tempArrPOISub = explode(',', $poisub_id);
                            foreach ($tempArrPOISub as $k => $v) {
                                $arrPOISub[$k]['poisub_id'] = $v;
                            }
                        }

                        $dataItems[] = [
                            'productions_orders_details_id' => $sp_pod_id,
                            'type_item' => $item_type_sp,
                            'item_id' => $item_id_sp,
                            'location_id' => $localtion_semi_product,
                            'item_code' => $item_code_sp,
                            'item_name' => $item_name_sp,
                            'quantity' => $quantity_semi_product,
                            'quantity_exchange' => $quantity_exchange_sp,
                            'quantity_single' => $quantity_single_sp,
                            'quantity_semi_product' => $quantity_semi_product,
                            'type_order' => $type_order_sp,
                            'arrPOISub' => $arrPOISub,
                            'arrMaterials' => $arrMaterials
                        ];
                        $total_quantity_purchases+= $quantity_semi_product;
                        $count_quantity_purchases++;
                    }
                }

                if (empty($dataItems)) {
                    continue;
                }

                $dateGerenal = date('Y-m-d H:i:s');
                $staffGerenal = $this->staffid;
                $reference_purchase = getReference('purchase_products');
                $sp_type = 1;
                $purchases = [
                    'reference_no' => $reference_purchase,
                    'date' => $dateGerenal,
                    'productions_orders_details_id' => $sp_pod_id,
                    'warehouse_id' => $warehouses_semi_product,
                    'count_items' => $count_quantity_purchases,
                    'total_quantity' => $total_quantity_purchases,
                    'created_by' => $staffGerenal,
                    'date_created' => $dateGerenal,
                    'status' => 'un_approved',
                    'pois_id' => $sp_pois_id,
                    'type' => 3,
                    'sp_type' => $sp_type
                ];

                $save_and_warehouse = 1;
                $export_name = 'Xuất kho NVL';
                $reference_suggest_exporting = getReference('stock');
                $suggest_exporting = [
                    'productions_orders_details_id' => $sp_pod_id,
                    'reference_no' => null,
                    'reference_stock' => $reference_suggest_exporting,
                    'date' => $dateGerenal,
                    'export_name' => $export_name,
                    'note' => '',
                    'status' => 'un_approved',
                    'total_quantity' => $total_quantity_se,
                    'count_items' => $count_quantity_se,
                    'total_quantity_exchange' => $total_quantity_exchange_se,
                    'created_by' => $staffGerenal,
                    'date_created' => $dateGerenal,
                    'convert_stock_by' => $staffGerenal,
                    'date_convert_stock' => $dateGerenal,
                    'status_stock' => 'approved',
                    'date_stock' => $dateGerenal,
                    'user_stock' => $staffGerenal,
                    'type' => '6',
                    'date_convert_stock' => $dateGerenal,
                    'warehouse_id' => 0,
                    'save_and_warehouse' => $save_and_warehouse,
                    'pois_id' => $sp_pois_id,
                    'use_productions_plan' => $use_productions_plan,
                ];

                $errors = '';
                $purchase_product_id = $this->stock_model->insertPurchaseProducts($purchases);
                if (!empty($purchase_product_id)) {
                    updateReference('purchase_products');
                    $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                        'active' => 1,
                        'staff_active' => $staffGerenal,
                        'date_active' => $dateGerenal,
                    ]);

                    $suggest_exporting['purchase_product_id'] = $purchase_product_id;
                    $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($suggest_exporting);
                    if ($suggest_exporting_id) {
                        updateReference('stock');
                    }

                    if (!empty($dataItems)) {
                        foreach ($dataItems as $key => $value) {
                            $arrPOISub = $value['arrPOISub'];
                            $arrMaterials = $value['arrMaterials'];
                            unset($value['arrPOISub']);
                            unset($value['arrMaterials']);
                            $value['purchase_product_id'] = $suggest_exporting['purchase_product_id'];
                            $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                            if ($purchase_product_item_id) {
                                if (!empty($arrPOISub)) {
                                    foreach ($arrPOISub as $k => $v) {
                                        $arrPOISub[$k]['purchase_product_id'] = $purchase_product_id;
                                        $arrPOISub[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                    }
                                }

                                if (!empty($arrMaterials)) {
                                    foreach ($arrMaterials as $k => $v) {
                                        $arrMaterials[$k]['suggest_exporting_id'] = $suggest_exporting_id;
                                        $arrMaterials[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                    }
                                }
                                if (!empty($arrMaterials)) {
                                    $this->manufactures_model->insertBatchSuggestExportingItems($arrMaterials);
                                }

                            }

                            if (!empty($arrPOISub)) {
                                $this->manufactures_model->insertBatchPurchaseProductPoisub($arrPOISub);
                            }
                        }
                    }

                    //suggest exporting
                    if (!empty($save_and_warehouse)) {
                        $id = $suggest_exporting_id;
                        $_data = array(
                            'warehouseman_id' => $this->staffid,
                            'date_warehouseman' => date('Y-m-d H:i:s')
                        );

                        if(!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                            $errors.= '<div>'.lang('test_quantyti_time_return').'</div>';
                        } else {
                            $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                            if ($success) {
                                log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                                $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                                $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                                $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                                $content = lang('tnh_his_warehouse_exporting_producion');
                                $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                                $content = str_replace('{$2}', $pod['reference_no'], $content);

                                insertActivityLog([
                                    'type_parent_obj' => 'exporting_producion',
                                    'table_obj' => 'tbl_suggest_exporting',
                                    'id_obj' => $id,
                                    'name_obj' => $suggest_exporting['reference_stock'],
                                    'content' => $content,
                                    'actions' => 'warehouse'
                                ], $staffGerenal);
                            }
                        }
                    }

                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($sp_pod_id);
                    $content = lang('tnh_his_add_exporting_producion');
                    $content = str_replace('{$1}', $reference_suggest_exporting, $content);
                    $content = str_replace('{$2}', $pod['reference_no'], $content);

                    insertActivityLog([
                        'type_parent_obj' => 'exporting_producion',
                        'table_obj' => 'tbl_suggest_exporting',
                        'id_obj' => $suggest_exporting_id,
                        'name_obj' => $reference_suggest_exporting,
                        'content' => $content,
                        'actions' => 'add'
                    ], $staffGerenal);

                    //warehouses purchase products
                    if (!empty($save_and_warehouse)) {
                        $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_id);
                        $_data = array(
                            'status' => 'approved',
                            'warehouseman_id' => $this->staffid,
                            'date_warehouseman' => date('Y-m-d H:i:s')
                        );
                        $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_id));
                        if ($success) {
                            log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_id);
                            $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_id);

                            //activity log
                            if (!empty($purchaseProduct['productions_orders_details_id']))
                            {
                                $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                                $content = lang('tnh_his_warehouse_purchase_product_pod');
                                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                $content = str_replace('{$2}', $pod['reference_no'], $content);
                            } else {
                                $content = lang('tnh_his_warehouse_purchase_product');
                                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                            }
                            insertActivityLog([
                                'type_parent_obj' => 'purchase_products',
                                'table_obj' => 'tbl_purchase_products',
                                'id_obj' => $purchase_product_id,
                                'name_obj' => $purchaseProduct['reference_no'],
                                'content' => $content,
                                'actions' => 'warehouse'
                            ], $staffGerenal);
                            //end activity log
                        }
                    }

                    $content = lang('tnh_his_add_purchase_product');
                    $content = str_replace('{$1}', $reference_purchase, $content);
                    insertActivityLog([
                        'type_parent_obj' => 'purchase_products',
                        'table_obj' => 'tbl_purchase_products',
                        'id_obj' => $purchase_product_id,
                        'name_obj' => $reference_purchase,
                        'content' => $content,
                        'actions' => 'add'
                    ], $staffGerenal);

                    //handling bom
                    if (!empty($arrSaveBom)) {
                        $this->products_model->handlingBOMPOD($arrSaveBom);
                    }
                    $flagSuccess = true;
                }
            }
        }

        if ($flagSuccess == true) {
            noti_custom('qc_po', $productions_orders_id, $this->staffid, 0, 'finishedQCPO', [
                'arrPOIS' => $arrPOIS,
                'po_id' => $productions_orders_id,
                'stage_id' => $stage_id,
            ]);

            // noti_custom('pod_new', $productions_orders_id, $this->staffid, 0, 'finishedPOD',[
            //     'arrPOIS' => $arrPOIS,
            //     'po_id' => $productions_orders_id,
            //     'stage_id' => $stage_id,
            // ]);
            $data['result'] = 1;
            $data['message'] = lang('success');
        }
        echo json_encode($data);
    }

    public function agreeProcessProductStep() {
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $warehouse_import = $data_post['warehouse_import'];
        if (empty($warehouse_import)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn kho thành phẩm');
            echo json_encode($data); die;
        }

        $arrPOIS = $data_post['arrPOIS'];
        if (empty($arrPOIS)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn mặt hàng muốn hoàn thành');
            echo json_encode($data); die;
        }
        $type = 1;

        $arrData = [];
        $arrErrors = [];
        $arrSumMaterials = [];
        $productions_orders_id = 0;
        $stage_id = 0;
        foreach($arrPOIS as $key => $pois_id) {
            $pois = $this->manufactures_model->getProductionsOrderItemsStagesById($pois_id);
            if (!empty($pois)) {
                $active = $pois['active'];
                if ($active) {
                    continue;
                }

                $productions_orders_id = $pois['productions_orders_id'];
                $productions_orders_items_id = $pois['productions_orders_items_id'];
                $qc = $this->manufactures_model->isQCPre(0, $pois['productions_orders_items_id'], 0, $pois['number']);
                if ($qc == 2 || $qc == 3) {
                    $data['result'] = 0;
                    $data['message'] = lang('Có mặt hàng chưa QC công đoạn trước hoặc chưa đạt');
                    echo json_encode($data); die;
                }
                
                $this->db->select('
                    tbl_productions_orders_details.id as pod_id,
                ', false);
                $this->db->from('tbl_productions_orders_items');
                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
                $this->db->where('tbl_productions_orders_items.id', $productions_orders_items_id);
                $pod = $this->db->get()->row_array();
                $pod_id = $pod['pod_id'];
                $productions_plan = $this->manufactures_model->getProductionsPlanPOD($pod_id);
                $production_plan_id = $productions_plan['id'];
                $pois_id = $pois['id'];
                $productions_orders_subs = $this->manufactures_model->loadDataProductsStep($pod_id, $pois_id);
                $flagSuccess = true;
                if (!empty($productions_orders_subs)) {
                    foreach ($productions_orders_subs as $k => $val) {
                        if (empty($val['subItems'])) {
                            $arrErrors[$pois_id] = [
                                'pois_id' => $pois_id,
                                'pod_id' => $pod_id,
                                'message' => lang('Không có thành phẩm để xuất')
                            ];
                            $flagSuccess = false;
                            break;
                        } else {
                            foreach ($val['subItems'] as $kS => $vS) {
                                $quantity_primary = $vS['quantity_primary'];
                                if (empty($vS['arrW'][0]['warehousePlan'])) {
                                    $arrErrors[$pois_id] = [
                                        'pois_id' => $pois_id,
                                        'pod_id' => $pod_id,
                                        'message' => lang('TP không đủ để xuất kho sản xuất')
                                    ];
                                    $flagSuccess = false;
                                    break;
                                }

                                $warehouse_plan = ($vS['arrW'][0]);
                                $w_id = $warehouse_plan['warehousePlan']['id'];
                                if (empty($w_id)) {
                                    $arrErrors[$pois_id] = [
                                        'pois_id' => $pois_id,
                                        'pod_id' => $pod_id,
                                        'message' => lang('TP không đủ để xuất kho sản xuất')
                                    ];
                                    $flagSuccess = false;
                                    break;
                                }
                                $item_cs_id = $vS['item_cs_id'].'__'.$w_id;
                                if (empty($arrSumMaterials[$item_cs_id])) {
                                    $arrSumMaterials[$item_cs_id] = [
                                        'item_cs_id' => $item_cs_id,
                                        'w_id' => $w_id,
                                        'pod_id' => $pod_id,
                                        'production_plan_id' => $production_plan_id,
                                        'item_type' => $vS['item_type'],
                                        'item_code' => $vS['item_code'],
                                        'item_name' => $vS['item_name'],
                                        'item_id' => $vS['item_id'],
                                        'images' => $vS['images'],
                                        'quantity_primary' => $vS['quantity_primary'],
                                    ];
                                } else {
                                    $arrSumMaterials[$item_cs_id]['quantity_primary'] = $arrSumMaterials[$item_cs_id]['quantity_primary'] + $quantity_primary;
                                }
                            }
                        }
                    }
                } else {
                    $arrErrors[$pois_id] = [
                        'pois_id' => $pois_id,
                        'pod_id' => $pod_id,
                        'message' => lang('Không có thành phẩm để hoàn thành')
                    ];
                    continue;
                }

                if ($flagSuccess) {
                    $arrData[$pois_id]['pod_id'] = $pod_id;
                    $arrData[$pois_id]['pois_id'] = $pois_id;
                    $arrData[$pois_id]['stage_id'] = $pois['stage_id'];
                    $arrData[$pois_id]['productions_orders_subs'] = $productions_orders_subs;
                }
            }
        }

        $errorsWarehouses = [];
        if (!empty($arrSumMaterials)) {
            foreach ($arrSumMaterials as $key => $value) {
                $item_type = $value['item_type'];
                $item_id = $value['item_id'];
                $item_code = $value['item_code'];
                $quantity = $value['quantity_primary'];
                $production_plan_id = $value['production_plan_id'];
                $w_id = explode('__', $value['w_id']);
                if (empty($w_id[1])) {
                    $data['result'] = 0;
                    $data['message'] = lang('TP không đủ để xuất kho sản xuất');
                    echo json_encode($data, JSON_UNESCAPED_UNICODE); die;
                }
                $warehouse_id = $w_id[0];
                $location_id = $w_id[1];

                $isQuantityWarehouses = $this->manufactures_model->isQuantityWarehouses($item_id, $item_type, $quantity, $warehouse_id, 0, $location_id);
                if (!empty($isQuantityWarehouses)) {
                    $warehouse_name = $isQuantityWarehouses['warehouses_name'];
                    $location_name = $isQuantityWarehouses['location_name'];
                    $errorsWarehouses[$key] = $value;
                    $errorsWarehouses[$key]['warehouse_name'] = $warehouse_name;
                    $errorsWarehouses[$key]['location_name'] = $location_name;
                    $errorsWarehouses[$key]['quantity_lack'] = $quantity - (float)$isQuantityWarehouses['product_quantity'];
                    $errorsWarehouses[$key]['link_image'] = base_url('assets/uploads');
                }
            }
        }

        if (!empty($errorsWarehouses) || !empty($arrErrors)) {
            $data['result'] = 2;
            $data['errorsWarehouses'] = $errorsWarehouses;
            $data['arrErrors'] = $arrErrors;
            $data['message'] = lang('Không đủ kho hoặc thành phẩm xuất ra ở công đoạn trước');
            echo json_encode($data, JSON_UNESCAPED_UNICODE); die;
        }

        $data['result'] = 0;
        $data['message'] = lang('fail');

        $flagSuccess = false;
        $temp_final_stage = 0;
        $stage_id = 0;
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $sp_pod_id = $v['pod_id'];
                $sp_pois_id = $v['pois_id'];
                
                $dtPois = get_table_where('tbl_productions_orders_items_stages', ['id' => $sp_pois_id], "", "row_array", 'stage_id, final_stage, active');
                $active = $dtPois['active'];
                if ($active) {
                    continue;
                }

                $flagSuccess = true;
                $final_stage = $dtPois['final_stage'];
                $stage_id = $dtPois['stage_id'];
                $warehouses_semi_product = $warehouse_import;
                $use_productions_plan = 1;
                $stage_id_w = $stage_id;
                if ($final_stage) {
                    $stage_id_w = 0;
                    $temp_final_stage = 1;
                }
                $localtion_semi_product = $this->site_model->getLocationPOD($sp_pod_id, $warehouses_semi_product, $stage_id_w);

                $dataItems = [];
                $total_quantity_purchases = 0;
                $count_quantity_purchases = 0;
                $total_quantity_se = 0;
                $total_quantity_exchange_se = 0;
                $count_quantity_se = 0;
                $items = $v['productions_orders_subs'];
                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        $item_type_sp = "products";
                        $arr_id_sp = explode('__', $value['id']);
                        $type_order_sp = $arr_id_sp[0];
                        $item_id_sp = $arr_id_sp[1];
                        $info = $this->products_model->rowProduct($item_id_sp);
                        if (empty($info)) {
                            continue;
                        }
                        $item_code_sp = $info['code']; 
                        $item_name_sp = $info['name']; 

                        $quantity_exchange_sp = number_unformat($value['quantity_exchange']);
                        $quantity_single_sp = number_unformat($value['quantity_single']);
                        $quantity_semi_product = number_unformat($value['quantity']);
                        $unit_id_sp = $value['unit_id'];
                        $unit_parent_id_sp = $value['unit_parent_id'];

                        $materials = !empty($value['subItems']) ? $value['subItems'] : null;
                        $is_save_dm = 0;
                        $arrMaterials = [];
                        $arrMaterialsBOM = [];
                        if (!empty($materials)) {
                            foreach ($materials as $k => $v) {
                                $quantity_exchange_mt = number_unformat($v['quantity_exchange']);
                                if (empty($quantity_exchange_mt)) $quantity_exchange_mt = 1;
                                $quantity_single_mt = number_unformat($v['quantity_single']);
                                $arr_item_cs_id_mt = explode('__', $v['item_cs_id']);
                                $item_id_mt = $arr_item_cs_id_mt[1];
                                $item_type_mt = $arr_item_cs_id_mt[0];
                                $unit_id = $v['unit_id'];
                                $unit_parent_id = $v['unit_parent_id'];
                                if ($item_type_mt == "materials") {
                                    $info_mt = $this->items_model->rowMaterial($item_id_mt);
                                } else if ($item_type_mt == "tools_supplies") {
                                    $info_mt = $this->tools_supplies_model->rowToolsSupplies($item_id_mt);
                                }  else {
                                    $info_mt = $this->products_model->rowProduct($item_id_mt);
                                }

                                if (empty($info_mt)) {
                                    continue;
                                }

                                $quantity_materials_mt = $v['quantity'];
                                $unit_id_mt = $v['unit_id'];
                                $unit_parent_id_mt = $v['unit_parent_id'];
                                // $warehouses_items_mt = $v['warehousePlan'];
                                $warehouses_items_mt = $v['arrW'];
                                if (empty($warehouses_items_mt)) {
                                    continue;
                                } else if ($warehouses_items_mt) {
                                    // $warehouses_items_mt = json_decode($warehouses_items_mt, true);
                                    $warehouses_items_mt = $warehouses_items_mt;
                                    foreach ($warehouses_items_mt as $kW => $vW) {
                                        $vW = $vW['warehousePlan'];
                                        if (empty($vW)) {
                                            continue;
                                        }
                                        $vW = explode('__', $vW['id']);
                                        $warehouse_item_id = $vW[0];
                                        $location_id = $vW[1];
                                        $quantity_items = $quantity_materials_mt;
                                        $quantity_exchange_se =  $quantity_items/$quantity_exchange_mt;
                                        $arrMaterials[] = [
                                            'type_item' => $item_type_mt,
                                            'item_id' => $item_id_mt,
                                            'item_code' => $info_mt['code'],
                                            'item_name' => $info_mt['name'],
                                            'unit_id' => $unit_id_mt,
                                            'quantity_export' => $quantity_items,
                                            'unit_parent_id' => $unit_parent_id_mt,
                                            'number_exchange' => $quantity_exchange_mt,
                                            'quantity_exchange' => $quantity_exchange_se,
                                            'location_id' => $location_id,
                                            'warehouse_item_id' => $warehouse_item_id,
                                        ];
                                        $total_quantity_se+= $quantity_items;
                                        $total_quantity_exchange_se+= $quantity_exchange_se;
                                        $count_quantity_se++;
                                    }
                                }

                                if ($is_save_dm) {
                                    $arrMaterialsBOM[] = [
                                        'type' => $item_type_mt,
                                        'item_id' => $item_id_mt,
                                        'unit_id' => $unit_id_mt,
                                        'quantity' => $quantity_single_mt,
                                    ];
                                }
                            }
                        }

                        $poisub_id = $value['poisub_id'];
                        $arrPOISub = [];
                        if (!empty($poisub_id)) {
                            $tempArrPOISub = explode(',', $poisub_id);
                            foreach ($tempArrPOISub as $k => $v) {
                                $arrPOISub[$k]['poisub_id'] = $v;
                            }
                        }

                        $dataItems[] = [
                            'productions_orders_details_id' => $sp_pod_id,
                            'type_item' => $item_type_sp,
                            'item_id' => $item_id_sp,
                            'location_id' => $localtion_semi_product,
                            'item_code' => $item_code_sp,
                            'item_name' => $item_name_sp,
                            'quantity' => $quantity_semi_product,
                            'quantity_exchange' => $quantity_exchange_sp,
                            'quantity_single' => $quantity_single_sp,
                            'quantity_semi_product' => $quantity_semi_product,
                            'type_order' => $type_order_sp,
                            'arrPOISub' => $arrPOISub,
                            'arrMaterials' => $arrMaterials
                        ];
                        $total_quantity_purchases+= $quantity_semi_product;
                        $count_quantity_purchases++;
                    }
                }

                if (empty($dataItems)) {
                    continue;
                }

                $dateGerenal = date('Y-m-d H:i:s');
                $staffGerenal = $this->staffid;
                $reference_purchase = getReference('purchase_products');
                $sp_type = 1;
                $purchases = [
                    'reference_no' => $reference_purchase,
                    'date' => $dateGerenal,
                    'productions_orders_details_id' => $sp_pod_id,
                    'warehouse_id' => $warehouses_semi_product,
                    'count_items' => $count_quantity_purchases,
                    'total_quantity' => $total_quantity_purchases,
                    'created_by' => $staffGerenal,
                    'date_created' => $dateGerenal,
                    'status' => 'un_approved',
                    'pois_id' => $sp_pois_id,
                    'type' => 4,
                    'sp_type' => $sp_type,
                    'final_stage' => $final_stage
                ];

                $save_and_warehouse = 1;
                $export_name = 'Xuất kho NVL';
                $reference_suggest_exporting = getReference('stock');
                $suggest_exporting = [
                    'productions_orders_details_id' => $sp_pod_id,
                    'reference_no' => null,
                    'reference_stock' => $reference_suggest_exporting,
                    'date' => $dateGerenal,
                    'export_name' => $export_name,
                    'note' => '',
                    'status' => 'un_approved',
                    'total_quantity' => $total_quantity_se,
                    'count_items' => $count_quantity_se,
                    'total_quantity_exchange' => $total_quantity_exchange_se,
                    'created_by' => $staffGerenal,
                    'date_created' => $dateGerenal,
                    'convert_stock_by' => $staffGerenal,
                    'date_convert_stock' => $dateGerenal,
                    'status_stock' => 'approved',
                    'date_stock' => $dateGerenal,
                    'user_stock' => $staffGerenal,
                    'type' => '7',
                    'date_convert_stock' => $dateGerenal,
                    'warehouse_id' => 0,
                    'save_and_warehouse' => $save_and_warehouse,
                    'pois_id' => $sp_pois_id,
                    'use_productions_plan' => $use_productions_plan,
                ];

                $errors = '';
                $purchase_product_id = $this->stock_model->insertPurchaseProducts($purchases);
                if (!empty($purchase_product_id)) {
                    updateReference('purchase_products');
                    $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                        'active' => 1,
                        'staff_active' => $staffGerenal,
                        'date_active' => $dateGerenal,
                    ]);

                    $suggest_exporting['purchase_product_id'] = $purchase_product_id;
                    $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($suggest_exporting);
                    if ($suggest_exporting_id) {
                        updateReference('stock');
                    }

                    if (!empty($dataItems)) {
                        foreach ($dataItems as $key => $value) {
                            $arrPOISub = $value['arrPOISub'];
                            $arrMaterials = $value['arrMaterials'];
                            unset($value['arrPOISub']);
                            unset($value['arrMaterials']);
                            $value['purchase_product_id'] = $suggest_exporting['purchase_product_id'];
                            $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                            if ($purchase_product_item_id) {
                                if (!empty($arrPOISub)) {
                                    foreach ($arrPOISub as $k => $v) {
                                        $arrPOISub[$k]['purchase_product_id'] = $purchase_product_id;
                                        $arrPOISub[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                    }
                                }

                                if (!empty($arrMaterials)) {
                                    foreach ($arrMaterials as $k => $v) {
                                        $arrMaterials[$k]['suggest_exporting_id'] = $suggest_exporting_id;
                                        $arrMaterials[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                    }
                                }
                                if (!empty($arrMaterials)) {
                                    $this->manufactures_model->insertBatchSuggestExportingItems($arrMaterials);
                                }

                            }

                            if (!empty($arrPOISub)) {
                                $this->manufactures_model->insertBatchPurchaseProductPoisub($arrPOISub);
                            }
                        }
                    }

                    if ($final_stage) {
                        $itemsPurchases = $this->stock_model->getPurchaseProductItems($purchase_product_id);
                        foreach ($itemsPurchases as $k => $val) {
                            $this->manufactures_model->updateQuantityWarehoused($val['productions_orders_details_id'], $val['quantity'], $plus = 0);
                        }
                    }

                    //suggest exporting
                    if (!empty($save_and_warehouse)) {
                        $id = $suggest_exporting_id;
                        $_data = array(
                            'warehouseman_id' => $this->staffid,
                            'date_warehouseman' => date('Y-m-d H:i:s')
                        );

                        if(!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                            $errors.= '<div>'.lang('test_quantyti_time_return').'</div>';
                        } else {
                            $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                            if ($success) {
                                log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                                $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                                $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                                $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                                $content = lang('tnh_his_warehouse_exporting_producion');
                                $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                                $content = str_replace('{$2}', $pod['reference_no'], $content);

                                insertActivityLog([
                                    'type_parent_obj' => 'exporting_producion',
                                    'table_obj' => 'tbl_suggest_exporting',
                                    'id_obj' => $id,
                                    'name_obj' => $suggest_exporting['reference_stock'],
                                    'content' => $content,
                                    'actions' => 'warehouse'
                                ], $staffGerenal);
                            }
                        }
                    }

                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($sp_pod_id);
                    $content = lang('tnh_his_add_exporting_producion');
                    $content = str_replace('{$1}', $reference_suggest_exporting, $content);
                    $content = str_replace('{$2}', $pod['reference_no'], $content);

                    insertActivityLog([
                        'type_parent_obj' => 'exporting_producion',
                        'table_obj' => 'tbl_suggest_exporting',
                        'id_obj' => $suggest_exporting_id,
                        'name_obj' => $reference_suggest_exporting,
                        'content' => $content,
                        'actions' => 'add'
                    ], $staffGerenal);

                    //warehouses purchase products
                    if (!empty($save_and_warehouse)) {
                        $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_id);
                        $_data = array(
                            'status' => 'approved',
                            'warehouseman_id' => $this->staffid,
                            'date_warehouseman' => date('Y-m-d H:i:s')
                        );
                        $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_id));
                        if ($success) {
                            log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_id);
                            $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_id);

                            //activity log
                            if (!empty($purchaseProduct['productions_orders_details_id']))
                            {
                                $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                                $content = lang('tnh_his_warehouse_purchase_product_pod');
                                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                $content = str_replace('{$2}', $pod['reference_no'], $content);
                            } else {
                                $content = lang('tnh_his_warehouse_purchase_product');
                                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                            }
                            insertActivityLog([
                                'type_parent_obj' => 'purchase_products',
                                'table_obj' => 'tbl_purchase_products',
                                'id_obj' => $purchase_product_id,
                                'name_obj' => $purchaseProduct['reference_no'],
                                'content' => $content,
                                'actions' => 'warehouse'
                            ], $staffGerenal);
                            //end activity log
                        }
                    }

                    $content = lang('tnh_his_add_purchase_product');
                    $content = str_replace('{$1}', $reference_purchase, $content);
                    insertActivityLog([
                        'type_parent_obj' => 'purchase_products',
                        'table_obj' => 'tbl_purchase_products',
                        'id_obj' => $purchase_product_id,
                        'name_obj' => $reference_purchase,
                        'content' => $content,
                        'actions' => 'add'
                    ], $staffGerenal);

                    //handling bom
                    if (!empty($arrSaveBom)) {
                        $this->products_model->handlingBOMPOD($arrSaveBom);
                    }
                    $flagSuccess = true;
                }
            }
        }

        if ($flagSuccess == true) {
            noti_custom('qc_po', $productions_orders_id, $this->staffid, 0, 'finishedQCPO', [
                'arrPOIS' => $arrPOIS,
                'po_id' => $productions_orders_id,
                'stage_id' => $stage_id,
            ]);
            
            if ($temp_final_stage) {
                noti_custom('pod_new', $productions_orders_id, $this->staffid, 0, 'finishedPOD',[
                    'arrPOIS' => $arrPOIS,
                    'po_id' => $productions_orders_id,
                    'stage_id' => $stage_id,
                ]);
            }
            $data['result'] = 1;
            $data['message'] = lang('success');
        }
        echo json_encode($data);
    }

    public function agreeProcessRemake() {
        $data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
            }
        }

        $cqi_id = $data_post['cqi_id'];
        $status = $data_post['status'];
        $pod_id = $data_post['pod_id'];
        $pois_id = $data_post['pois_id'];
        $staff_status = $this->staffid;
        $status_date = date('Y-m-d H:i:s');
        $data['result'] = 0;
        $data['message'] = lang('fail');

        $pod = $this->manufactures_model->rowProductionsOrdersDetais($pod_id);
        $productions_orders_items_id = $pod['productions_orders_item_id'];
        $check_quality_items = $this->manufactures_model->getCheckQualityItemsById($cqi_id);
        if (!empty($check_quality_items)) {
            $stage_id = $check_quality_items['id_stage'];
            if ($status) {
                $option = [
                    'pod_id' => $pod_id,
                    'stage_id' => $stage_id,
                    'check_quality_items_id' => $cqi_id,
                    'pois_id' => $pois_id,
                    'status' => $status,
                    'staff_status' => $staff_status,
                    'status_date' => $status_date,
                ];
                $ins = $this->manufactures_model->insertQCRemake($option);
                if ($ins) {
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                }
            } else if ($status == 0) {
                $qcRemake = $this->manufactures_model->getQCRemakeCQIId($cqi_id);
                if ($qcRemake['type'] != 0) {
                    $this->db->select('tbl_suggest_exporting.id as id');
                    $this->db->from('tbl_suggest_exporting');
                    $this->db->where('tbl_suggest_exporting.productions_orders_details_id', $pod_id);
                    $this->db->where('tbl_suggest_exporting.pois_id', $pois_id);
                    $this->db->where('tbl_suggest_exporting.type >=', 10);
                    $this->db->where('tbl_suggest_exporting.cqi_id', $cqi_id);
                    $suggest_exporting = $this->db->get()->result_array();

                    $this->db->select('tbl_purchase_products.*');
                    $this->db->from('tbl_purchase_products');
                    $this->db->where('tbl_purchase_products.productions_orders_details_id', $pod_id);
                    $this->db->where('tbl_purchase_products.pois_id', $pois_id);
                    $this->db->where('tbl_purchase_products.type >=', 10);
                    $this->db->where('tbl_purchase_products.cqi_id', $cqi_id);
                    $purchase_products = $this->db->get()->result_array();
                    if (!empty($purchase_products)) {
                        foreach ($purchase_products as $key => $value) {
                            $test_quantity = get_table_where('tblwarehouse_product', array('import_id' => $value['id'], 'quantity_export >' => 0, 'type_export ' => 18), '', 'row');
                            if (!empty($test_quantity)) {
                                $data['result'] = 2;
                                $data['message'] = lang('ch_quantity_nd');
                                echo json_encode($data); die;
                            }
                        }
                    }
                }
                $delete = $this->manufactures_model->manufactures_model->deleteQCRemakeCqiId($cqi_id);
                if ($delete) {

                    //remove suggest exporting
                    if (!empty($suggest_exporting)) {
                        foreach($suggest_exporting as $key => $value) {
                            $ktr = get_table_where('tbl_suggest_exporting', array('id' => $value['id']), '', 'row');
                            $items = get_table_where('tbl_suggest_exporting_items', array('suggest_exporting_id' => $value['id']));
                            $_data = array(
                                'warehouseman_id' => 0,
                                'date_warehouseman' => NULL
                            );
                            $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $value['id']));
                            if ($success) {
                                // $this->stock_model->increaseadWarehouse($value['id'], $items, 0);
                                if ($ktr->warehouseman_id != 0) {
                                	$this->stock_model->increaseadWarehouse($value['id'], $items, 0);
                            	}
                                $this->manufactures_model->deleteSuggestExportingById($value['id']);
                                $this->manufactures_model->deleteSuggestExportingItems($value['id']);
                            }
                        }
                    }

                    //remove purchase products
                    if (!empty($purchase_products)) {
                        foreach ($purchase_products as $key => $value) {
                            $ktr = get_table_where('tbl_purchase_products', array('id' => $value['id']), '', 'row');
                            $_data = array(
                                'status' => 'un_approved',
                                'warehouseman_id' => 0,
                                'date_warehouseman' => NULL
                            );
                            $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $value['id']));
                            if ($success) {
                                // $this->stock_model->increaseadWarehouse_purchase_products($value['id']);
                                if ($ktr->warehouseman_id != 0) {
                                	$this->stock_model->increaseadWarehouse_purchase_products($value['id']);
                            	}
                                $this->stock_model->deletePurchaseProducts($value['id']);
                                $this->stock_model->deletePurchaseProductItems($value['id']);
                                $this->manufactures_model->deletePurchaseProductPoisub($value['id']);
                                if (!empty($value['final_stage'])) {
                                    $this->manufactures_model->updateQuantityWarehoused($value['productions_orders_details_id'], $value['total_quantity'], $minus = 1);
                                }
                            }
                        }
                    }

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                }
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function agreeProcessSemiProductNew() {
        $data = [];
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $warehouse_import = WAREHOUSES_CAPACITY;
        if (empty($warehouse_import)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn kho bán thành phẩm');
            echo json_encode($data); die;
        }

        $arrPOIS = $data_post['arrPOIS'];
        if (empty($arrPOIS)) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn mặt hàng muốn hoàn thành');
            echo json_encode($data); die;
        }

        $items = $this->manufactures_model->getHandlingPOISSemiProducts($arrPOIS);
        $arrPOIId = [];
        $arr_pp_id = [];
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $arr_pp_id[] = $value['pp_id'];
                $arrPOIId[] = $value['poi_id'];
            }
        }

        $dtWarehouses = [];
        $counterWarehouses = 0;
        if (!empty($arrPOIId)) {
            $this->db->select('
                tbl_productions_orders_items_sub.item_id as item_id
            ', false);
            $this->db->from('tbl_productions_orders_items_sub');
            $this->db->where_in('tbl_productions_orders_items_sub.productions_orders_items_id', $arrPOIId);
            $this->db->where('tbl_productions_orders_items_sub.type', 'semi_products');
            $this->db->group_by('tbl_productions_orders_items_sub.item_id');
            $semi_products = $this->db->get()->result_array();
            if (!empty($semi_products)) {
                $arr_pp_id = array_unique($arr_pp_id);
                if (!empty($arr_pp_id)) {
                    foreach ($arr_pp_id as $kPP => $vPP) {
                        foreach ($semi_products as $key => $value) {
                            $this->db->select('SUM(tblwarehouse_items.product_quantity) as quantity_warehouses');
                            $this->db->from('tblwarehouse_items');
                            $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
                            $this->db->where('tblwarehouse_items.warehouse_id', WAREHOUSES_CAPACITY);
                            $this->db->where('tbllocaltion_warehouses.productions_plan_id', $vPP);
                            $this->db->where('tblwarehouse_items.id_items', $value['item_id']);
                            $this->db->where('tblwarehouse_items.type_items', 'product');
                            $quantity_warehouses = $this->db->get()->row_array()['quantity_warehouses'];
                            if (!empty($quantity_warehouses)) {
                                $dtWarehouses['semi_products_' . $value['item_id'] . '_' . $vPP] = $quantity_warehouses;
                            }
                        }
                    }
                }
            }
        }

        foreach ($items as $key => $value) {
            $pod_id = $value['pod_id'];
            $production_plan_id = $value['pp_id'];
            $qc = $this->manufactures_model->isQCPre(0, $value['poi_id'], 0, $value['number']);
            if ($qc == 2 || $qc == 3) {
                $data['result'] = 0;
                $data['message'] = lang('Có mặt hàng chưa QC công đoạn trước hoặc chưa đạt');
                echo json_encode($data); die;
            }

            $productions_orders_subs = $this->manufactures_model->loadDataSemiProducts($pod_id, $production_plan_id, 0, '', 0)['productions_orders_subs'];
            $items[$key]['productions_orders_subs'] = null;
            if (!empty($productions_orders_subs)) {
                foreach ($productions_orders_subs as $k => $dtData) {
                    $product_id = $dtData['product_id'];
                    $semi_product_code = $dtData['code'];
                    $semi_product_name = $dtData['name'];
                    $quantity_primary = $dtData['quantity_primary'];
                    $quantity = $dtData['quantity'];
                    if (!empty($dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id])) {
                        $quantity_warehouse = $dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id];
                        if ($quantity_warehouse > 0) {
                            $tempQuantity = $quantity_warehouse - $quantity;
                            if ($tempQuantity >= 0) {
                                $dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id] = $tempQuantity;

                                $quantity = 0;
                                $productions_orders_subs[$k]['quantity'] = $quantity;
                                $isSemi = 1;
                                unset($productions_orders_subs[$k]);
                                $items[$key]['productions_orders_subs'] = $productions_orders_subs;
                                continue;
                            } else {
                                $quantity = abs($tempQuantity);

                            }
                            $dtWarehouses['semi_products_' . $product_id . '_' . $production_plan_id] = $tempQuantity;
                        }
                    }
                    $productions_orders_subs[$k]['quantity'] = $quantity;
                    $isSemi = 1;
                    $items[$key]['productions_orders_subs'] = $productions_orders_subs;
                }
            }
        }

        $productions_orders_subs = array_column($items, 'productions_orders_subs');
        array_multisort($productions_orders_subs, SORT_DESC, $items);


        $arrData = [];
        $arrErrors = [];
        $arrSumMaterials = [];
        $productions_orders_id = 0;
        $stage_id = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $pod_id = $value['pod_id'];
                $production_plan_id = $value['pp_id'];
                $isSemi = 0;
                $productions_orders_subs = $value['productions_orders_subs'];
                if (!empty($productions_orders_subs)) {
                    $isSemi = 1;
                    foreach ($productions_orders_subs as $k => $dtData) {
                        $quantity = $dtData['quantity'];
                        if (!empty($dtData['subItems'])) {
                            $subItems = $dtData['subItems'];
                            foreach ($subItems as $kSub => $vSub) {
                                $jsonWarehouses = $vSub['warehousePlan'];
                                $isWarehouses = $vSub['isWarehouses'];
                                $quantitySub = $quantity * $vSub['quantity_single'];
                                $items[$key]['productions_orders_subs'][$k]['subItems'][$kSub]['quantity'] = $quantitySub;
                            }
                        }
                    }
                }
                $items[$key]['isSemi'] = $isSemi;
            }

            foreach ($items as $kItem => $item) {
                $productions_orders_subs = $item['productions_orders_subs'];
                if (!empty($productions_orders_subs)) {
                    foreach ($productions_orders_subs as $k => $val) {
                        foreach ($val['subItems'] as $kS => $vS) {
                            if (empty($vS)) continue;
                            $item_cs_id = $vS['item_cs_id'];
                            $arr_item_cs_id_mt = explode('__', $vS['item_cs_id']);
                            $item_id_mt = $arr_item_cs_id_mt[1];
                            $item_type_mt = $arr_item_cs_id_mt[0];
                            $unit_id = $vS['unit_id'];
                            $unit_parent_id = $vS['unit_parent_id'];
                            $images = '';
                            if ($item_type_mt == "materials") {
                                $info_mt = $this->items_model->rowMaterial($item_id_mt);
                                if (!empty($info_mt['images'])) {
                                    $images = base_url('upload/materials/'.$info_mt['images']);
                                }
                            } else if ($item_type_mt == "tools_supplies") {
                                $info_mt = $this->tools_supplies_model->rowToolsSupplies($item_id_mt);
                                if (!empty($info_mt['images'])) {
                                    $images = base_url('upload/tools_supplies/'.$info_mt['images']);
                                }
                            }  else {
                                $info_mt = $this->products_model->rowProduct($item_id_mt);
                                if (!empty($info_mt['images'])) {
                                    $images = base_url('upload/products/'.$info_mt['images']);
                                }
                            }
                            if (empty($images)) {
                                $images = base_url('assets/images/tnh/no_image.png');
                            }


                            $quantity_items = $vS['quantity'];
                            $quantity_exchange_mt = $vS['quantity_exchange'];

                            $warehouses_items_mt = [json_decode($vS['warehousePlan'], true)];
                            foreach ($warehouses_items_mt as $kW => $w) {
                                if (empty($w)) continue;
                                $vW = $w['id'];
                                if (empty($vW)) continue;
                                $item_cs_id.= '__'.$vW;
                                $vW = explode('__', $vW);
                                $warehouse_item_id = $vW[0];
                                $location_id = $vW[1];
                                $quantity_exchange_se =  $quantity_items/$quantity_exchange_mt;

                                if (empty($arrSumMaterials[$item_cs_id])) {
                                    $arrSumMaterials[$item_cs_id] = [
                                        'item_cs_id' => $item_cs_id,
                                        'location_id' => $location_id,
                                        'warehouse_item_id' => $warehouse_item_id,
                                        'item_type' => $item_type_mt,
                                        'item_code' => $info_mt['code'],
                                        'item_name' => $info_mt['name'],
                                        'item_id' => $item_id_mt,
                                        'images' => $images,
                                        'quantity_primary' => $quantity_exchange_se,
                                    ];
                                } else {
                                    $arrSumMaterials[$item_cs_id]['quantity_primary'] = $arrSumMaterials[$item_cs_id]['quantity_primary'] + $quantity_exchange_se;
                                }
                            }
                        }
                    }
                }
            }
        }

        $errorsWarehouses = [];
        if (!empty($arrSumMaterials)) {
            foreach ($arrSumMaterials as $key => $value) {
                $item_type = $value['item_type'];
                $item_id = $value['item_id'];
                $item_code = $value['item_code'];
                $quantity = $value['quantity_primary'];
                $warehouse_item_id = $value['warehouse_item_id'];
                $location_id = $value['location_id'];
                $isQuantityWarehouses = $this->manufactures_model->isQuantityWarehouses($item_id, $item_type, $quantity, $warehouse_item_id, 0, $location_id);
                if (!empty($isQuantityWarehouses)) {
                    $warehouse_name = $isQuantityWarehouses['warehouses_name'];
                    $location_name = $isQuantityWarehouses['location_name'];

                    $errorsWarehouses[$key] = $value;
                    $errorsWarehouses[$key]['warehouse_name'] = $warehouse_name;
                    $errorsWarehouses[$key]['location_name'] = $location_name;
                    $errorsWarehouses[$key]['product_quantity'] = $isQuantityWarehouses['product_quantity'];
                }
            }
        }

        if (!empty($errorsWarehouses) || !empty($arrErrors)) {
            $data['result'] = 2;
            $data['errorsWarehouses'] = $errorsWarehouses;
            $data['arrErrors'] = $arrErrors;
            $data['message'] = lang('Không đủ kho để xuất');
            echo json_encode($data, JSON_UNESCAPED_UNICODE); die;
        }

        $data['result'] = 0;
        $data['messasge'] = lang('fail');
        $dateGerenal = date('Y-m-d H:i:s');
        $staffGerenal = $this->staffid;
        $flagSuccess = false;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $sp_type = 1;
                $sp_actions = '';
                $sp_pod_id = $value['pod_id'];
                $sp_pois_id = $value['pois_id'];
                $dtPois = get_table_where('tbl_productions_orders_items_stages', ['id' => $sp_pois_id], "", "row_array", 'stage_id, active, productions_orders_id');
                $productions_orders_id = $dtPois['productions_orders_id'];
                $stage_id = $dtPois['stage_id'];
                $qc_remake_id = 0;
                $typePurchase = 2;
                $typeSuggestExporting = 5;
                $sp_cqi_id = 0;
                if ($sp_actions == 'qc') {
                    $dtQcRemake = $this->manufactures_model->getQCRemakeCQIId($sp_cqi_id);
                    if (!empty($dtQcRemake) && $dtQcRemake['status']) {
                        $data['result'] = 0;
                        $data['message'] = lang('Giai đoạn sản xuất lại này đã được hoàn thành');
                        echo json_encode($data); die;
                    }
                    $qc_remake_id = $dtQcRemake['id'];
                    $typePurchase = 10;
                    $typeSuggestExporting = 10;
                } else {
                    $active = $dtPois['active'];
                    if ($active) {
                        $isHandlingActive = 0;
                        continue;
                    }
                }

                $sp_cqi_id = 0;
                $use_productions_plan = 1;

                // $localtion_semi_product = $this->site_model->getLocationPOD($sp_pod_id, $warehouse_import);
                $localtion_semi_product = $this->site_model->getLocationToPlan($value['pp_id']);
                $dataItems = [];
                $arrSaveBom = [];

                $total_quantity_purchases = 0;
                $count_quantity_purchases = 0;

                $total_quantity_se = 0;
                $total_quantity_exchange_se = 0;
                $count_quantity_se = 0;
                $items =  $value['productions_orders_subs'];
                $isErrors = true;
                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        $item_type_sp = "products";
                        $arr_id_sp = explode('__', $value['id']);
                        $type_order_sp = $arr_id_sp[0];
                        $item_id_sp = $arr_id_sp[1];
                        $info = $this->products_model->rowProduct($item_id_sp);
                        if (empty($info)) {
                            if (empty($info_mt)) {
                                continue;
                            }
                        }
                        $item_code_sp = $info['code']; 
                        $item_name_sp = $info['name']; 

                        $quantity_exchange_sp = number_unformat($value['quantity_exchange']);
                        $quantity_single_sp = number_unformat($value['quantity_single']);
                        $quantity_semi_product = number_unformat($value['quantity']);
                        $unit_id_sp = $value['unit_id'];
                        $unit_parent_id_sp = $value['unit_parent_id'];

                        $materials = !empty($value['subItems']) ? $value['subItems'] : null;
                        $is_save_dm = 0;
                        $arrMaterials = [];
                        $arrMaterialsBOM = [];
                        if (!empty($materials)) {
                            $isErrors = false;
                            foreach ($materials as $k => $v) {
                                $quantity_exchange_mt = number_unformat($v['quantity_exchange']);
                                if (empty($quantity_exchange_mt)) $quantity_exchange_mt = 1;
                                $quantity_single_mt = number_unformat($v['quantity_single']);
                                $arr_item_cs_id_mt = explode('__', $v['item_cs_id']);
                                $item_id_mt = $arr_item_cs_id_mt[1];
                                $item_type_mt = $arr_item_cs_id_mt[0];
                                $unit_id = $v['unit_id'];
                                $unit_parent_id = $v['unit_parent_id'];
                                if ($item_type_mt == "materials") {
                                    $info_mt = $this->items_model->rowMaterial($item_id_mt);
                                } else if ($item_type_mt == "tools_supplies") {
                                    $info_mt = $this->tools_supplies_model->rowToolsSupplies($item_id_mt);
                                }  else {
                                    $info_mt = $this->products_model->rowProduct($item_id_mt);
                                }

                                if (empty($info_mt)) {
                                    continue;
                                }

                                $quantity_items = $v['quantity'];
                                $unit_id_mt = $v['unit_id'];
                                $unit_parent_id_mt = $v['unit_parent_id'];
                                $warehouses_items_mt = [json_decode($v['warehousePlan'], true)];
                                if (empty($warehouses_items_mt)) {
                                    continue;
                                } else if ($warehouses_items_mt) {
                                    foreach ($warehouses_items_mt as $kW => $w) {
                                        if (empty($w)) {
                                            continue;
                                        }
                                        $vW = $w['id'];
                                        $vW = explode('__', $vW);
                                        $warehouse_item_id = $vW[0];
                                        $location_id = $vW[1];
                                        $quantity_exchange_se =  $quantity_items/$quantity_exchange_mt;
                                        $arrMaterials[] = [
                                            'type_item' => $item_type_mt,
                                            'item_id' => $item_id_mt,
                                            'item_code' => $info_mt['code'],
                                            'item_name' => $info_mt['name'],
                                            'unit_id' => $unit_id_mt,
                                            'quantity_export' => $quantity_items,
                                            'unit_parent_id' => $unit_parent_id_mt,
                                            'number_exchange' => $quantity_exchange_mt,
                                            'quantity_exchange' => $quantity_exchange_se,
                                            'location_id' => $location_id,
                                            'warehouse_item_id' => $warehouse_item_id,
                                        ];
                                        $total_quantity_se+= $quantity_items;
                                        $total_quantity_exchange_se+= $quantity_exchange_se;
                                        $count_quantity_se++;
                                    }
                                }

                                if ($is_save_dm) {
                                    $arrMaterialsBOM[] = [
                                        'type' => $item_type_mt,
                                        'item_id' => $item_id_mt,
                                        'unit_id' => $unit_id_mt,
                                        'quantity' => $quantity_single_mt,
                                    ];
                                }
                            }
                        }

                        if (!empty($is_save_dm) && !empty($arrMaterialsBOM)) {
                            $arrSaveBom[$key] = [
                                'product_id' => $item_id_sp,
                                'arrMaterialsBOM' => $arrMaterialsBOM
                            ];
                        }

                        $poisub_id = $value['poisub_id'];
                        $arrPOISub = [];
                        if (!empty($poisub_id)) {
                            $tempArrPOISub = explode(',', $poisub_id);
                            foreach ($tempArrPOISub as $k => $v) {
                                $arrPOISub[$k]['poisub_id'] = $v;
                            }
                        }

                        $dataItems[] = [
                            'productions_orders_details_id' => $sp_pod_id,
                            'type_item' => $item_type_sp,
                            'item_id' => $item_id_sp,
                            'location_id' => $localtion_semi_product,
                            'item_code' => $item_code_sp,
                            'item_name' => $item_name_sp,
                            'quantity' => $quantity_semi_product,
                            'quantity_exchange' => $quantity_exchange_sp,
                            'quantity_single' => $quantity_single_sp,
                            'quantity_semi_product' => $quantity_semi_product,
                            'type_order' => $type_order_sp,
                            'arrPOISub' => $arrPOISub,
                            'arrMaterials' => $arrMaterials
                        ];
                        $total_quantity_purchases+= $quantity_semi_product;
                        $count_quantity_purchases++;
                    }
                }

                if (!empty($dataItems)) {
                    $reference_purchase = getReference('purchase_products');
                    $purchases = [
                        'reference_no' => $reference_purchase,
                        'date' => $dateGerenal,
                        'productions_orders_details_id' => $sp_pod_id,
                        'warehouse_id' => $warehouse_import,
                        'count_items' => $count_quantity_purchases,
                        'total_quantity' => $total_quantity_purchases,
                        'created_by' => $staffGerenal,
                        'date_created' => $dateGerenal,
                        'status' => 'un_approved',
                        'pois_id' => $sp_pois_id,
                        'type' => $typePurchase,
                        'sp_type' => $sp_type,
                        'cqi_id' => $sp_cqi_id
                    ];

                    $save_and_warehouse = 1;
                    $export_name = 'Xuất kho NVL';
                    $reference_suggest_exporting = getReference('stock');
                    $suggest_exporting = [
                        'productions_orders_details_id' => $sp_pod_id,
                        'reference_no' => null,
                        'reference_stock' => $reference_suggest_exporting,
                        'date' => $dateGerenal,
                        'export_name' => $export_name,
                        'note' => '',
                        'status' => 'un_approved',
                        'total_quantity' => $total_quantity_se,
                        'count_items' => $count_quantity_se,
                        'total_quantity_exchange' => $total_quantity_exchange_se,
                        'created_by' => $staffGerenal,
                        'date_created' => $dateGerenal,
                        'convert_stock_by' => $staffGerenal,
                        'date_convert_stock' => $dateGerenal,
                        'status_stock' => 'approved',
                        'date_stock' => $dateGerenal,
                        'user_stock' => $staffGerenal,
                        'type' => $typeSuggestExporting,
                        'date_convert_stock' => $dateGerenal,
                        'warehouse_id' => 0,
                        'save_and_warehouse' => $save_and_warehouse,
                        'pois_id' => $sp_pois_id,
                        'use_productions_plan' => $use_productions_plan,
                        'cqi_id' => $sp_cqi_id
                    ];

                    $errors = '';
                    $purchase_product_id = $this->stock_model->insertPurchaseProducts($purchases);
                    if (!empty($purchase_product_id)) {
                        updateReference('purchase_products');

                        $suggest_exporting['purchase_product_id'] = $purchase_product_id;
                        $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($suggest_exporting);
                        if ($suggest_exporting_id) {
                            updateReference('stock');
                        }

                        if (!empty($dataItems)) {
                            foreach ($dataItems as $key => $value) {
                                $arrPOISub = $value['arrPOISub'];
                                $arrMaterials = $value['arrMaterials'];
                                unset($value['arrPOISub']);
                                unset($value['arrMaterials']);
                                $value['purchase_product_id'] = $suggest_exporting['purchase_product_id'];
                                $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                                if ($purchase_product_item_id) {
                                    if (!empty($arrPOISub)) {
                                        foreach ($arrPOISub as $k => $v) {
                                            $arrPOISub[$k]['purchase_product_id'] = $purchase_product_id;
                                            $arrPOISub[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                        }
                                    }

                                    if (!empty($arrMaterials)) {
                                        foreach ($arrMaterials as $k => $v) {
                                            $arrMaterials[$k]['suggest_exporting_id'] = $suggest_exporting_id;
                                            $arrMaterials[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                        }
                                    }
                                    if (!empty($arrMaterials)) {
                                        $this->manufactures_model->insertBatchSuggestExportingItems($arrMaterials);
                                    }

                                }

                                if (!empty($arrPOISub)) {
                                    $this->manufactures_model->insertBatchPurchaseProductPoisub($arrPOISub);
                                }
                            }
                        }

                        //suggest exporting
                        if (!empty($save_and_warehouse)) {
                            $id = $suggest_exporting_id;
                            $_data = array(
                                'warehouseman_id' => $this->staffid,
                                'date_warehouseman' => date('Y-m-d H:i:s')
                            );

                            if(!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                                $errors.= '<div>'.lang('test_quantyti_time_return').'</div>';
                            } else {
                                $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                                if ($success) {
                                    log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                                    $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                                    $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                                    $content = lang('tnh_his_warehouse_exporting_producion');
                                    $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                                    $content = str_replace('{$2}', $pod['reference_no'], $content);

                                    insertActivityLog([
                                        'type_parent_obj' => 'exporting_producion',
                                        'table_obj' => 'tbl_suggest_exporting',
                                        'id_obj' => $id,
                                        'name_obj' => $suggest_exporting['reference_stock'],
                                        'content' => $content,
                                        'actions' => 'warehouse'
                                    ], $this->staffid);
                                }
                            }
                        }

                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($sp_pod_id);
                        $content = lang('tnh_his_add_exporting_producion');
                        $content = str_replace('{$1}', $reference_suggest_exporting, $content);
                        $content = str_replace('{$2}', $pod['reference_no'], $content);

                        insertActivityLog([
                            'type_parent_obj' => 'exporting_producion',
                            'table_obj' => 'tbl_suggest_exporting',
                            'id_obj' => $suggest_exporting_id,
                            'name_obj' => $reference_suggest_exporting,
                            'content' => $content,
                            'actions' => 'add'
                        ], $this->staffid);

                        //warehouses purchase products
                        if (!empty($save_and_warehouse)) {
                            $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_id);
                            $_data = array(
                                'status' => 'approved',
                                'warehouseman_id' => $this->staffid,
                                'date_warehouseman' => date('Y-m-d H:i:s')
                            );
                            $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_id));
                            if ($success) {
                                log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_id);
                                $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_id);

                                //activity log
                                if (!empty($purchaseProduct['productions_orders_details_id']))
                                {
                                    $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                                    $content = lang('tnh_his_warehouse_purchase_product_pod');
                                    $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                    $content = str_replace('{$2}', $pod['reference_no'], $content);
                                } else {
                                    $content = lang('tnh_his_warehouse_purchase_product');
                                    $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                }
                                insertActivityLog([
                                    'type_parent_obj' => 'purchase_products',
                                    'table_obj' => 'tbl_purchase_products',
                                    'id_obj' => $purchase_product_id,
                                    'name_obj' => $purchaseProduct['reference_no'],
                                    'content' => $content,
                                    'actions' => 'warehouse'
                                ], $this->staffid);
                                //end activity log
                            }
                        }

                        $content = lang('tnh_his_add_purchase_product');
                        $content = str_replace('{$1}', $reference_purchase, $content);
                        insertActivityLog([
                            'type_parent_obj' => 'purchase_products',
                            'table_obj' => 'tbl_purchase_products',
                            'id_obj' => $purchase_product_id,
                            'name_obj' => $reference_purchase,
                            'content' => $content,
                            'actions' => 'add'
                        ], $this->staffid);

                        //handling bom
                        if (!empty($arrSaveBom)) {
                            $this->products_model->handlingBOMPOD($arrSaveBom);
                        }
                        
                        $data['result'] = 1;
                        $data['message'] = lang('success').''.$errors;
                    }
                }

                $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                    'active' => 1,
                    'staff_active' => $staffGerenal,
                    'date_active' => $dateGerenal,
                ]);

                $flagSuccess = true;
                $data['result'] = 1;
                $data['messasge'] = lang('success');
            }
        }

        if ($flagSuccess == true) {
            noti_custom('qc_po', $productions_orders_id, $this->staffid, 0, 'finishedQCPO', [
                'arrPOIS' => $arrPOIS,
                'po_id' => $productions_orders_id,
                'stage_id' => $stage_id,
            ]);
            $data['result'] = 1;
            $data['message'] = lang('success');
        }
        
        echo json_encode($data);
    }

}