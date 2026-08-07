<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/api_app/Api_Controller.php');

class Api_Report_tnh extends Api_Controller
{
    private $ci;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');

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


        $this->perViewOrdersOfQuotes = has_permission('orders_of_quotes', $this->staffid, 'view');
        $this->perViewDeliverySchedules = has_permission('delivery_schedules', $this->staffid, 'view');
        $this->perViewSalesOfOrder = has_permission('sales_of_order', $this->staffid, 'view');
        $this->perViewNearestSellingPrice = has_permission('nearest_selling_price', $this->staffid, 'view');
        $this->perViewReturnedGoods = has_permission('returned_goods', $this->staffid, 'view');
        $this->perViewOrderStatus = has_permission('order_status', $this->staffid, 'view');
        $this->perViewSalesAnalysis = has_permission('sales_analysis', $this->staffid, 'view');
        $this->perViewSellingDiary = has_permission('selling_diary', $this->staffid, 'view');

        $this->perViewMaterialNorms = has_permission('material_norms', $this->staffid, 'view');
        $this->perViewUsageMaterial = has_permission('usage_material', $this->staffid, 'view');
        $this->perViewProductionDetailed = has_permission('production_detailed', $this->staffid, 'view');
        $this->perViewSituationOrderExecution = has_permission('situation_order_execution', $this->staffid, 'view');
        $this->perViewStatusProduction = has_permission('status_production', $this->staffid, 'view');
        $this->perViewUseMlAcProductionOrders = has_permission('use_ml_ac_production_orders', $this->staffid, 'view');
        $this->perViewGeneralProduction = has_permission('general_production', $this->staffid, 'view');
        $this->perViewProductionScheduleByOrder = has_permission('production_schedule_by_order', $this->staffid, 'view');
        $this->perViewExpensesIncome = has_permission('expenses_vs_income', $this->staffid, 'view');
       
        $this->isAdmin = is_admin($this->staffid);
        $this->branchID = get_staff_user_id_branch_app($this->staffid);
        if ($this->branchID == 'main') $this->branchID = 0;
    }

    public function getProductionsOrders($page = 1, $limit = 10)
    {

        if (!$this->perViewUseMlAcProductionOrders) {
            $data['result'] = null;
            $data['next'] = 0;
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }

        $result = [];
        $start = ($page - 1) * $limit;

        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $start_date_search = !empty($data_post['start_date_search']) ? $data_post['start_date_search'] : null;
        $end_date_search = !empty($data_post['end_date_search']) ? $data_post['end_date_search'] : null;

        $tbProductionsPlanOrdersByOrders = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') SEPARATOR '|||') reference_no_orders,
                GROUP_CONCAT(tbl_orders.note SEPARATOR '</br>') as note
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
       
        $this->db->dbprefix = '';
        $this->db->select("
            tbl_productions_orders.id as id,
            tbl_productions_orders.date as date,
            tbl_productions_orders.reference_no as reference_no,
            tbl_productions_orders.note as note,
            tb_orders.note as note_orders,
            tblbranch.name as location_name,
            tbl_productions_orders.status_details,
            tbl_productions_orders.options1 as options1,
            tbl_productions_orders.options2 as options2,
            tb_orders.reference_no_orders as reference_no_orders,
            tb_business_plan.reference_no_business_plan as reference_no_business_plan,
            tbl_productions_orders.total_quantity as total_quantity,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
            tbl_productions_orders.status_orders as status_orders
        ", FALSE)
        ->from('tbl_productions_orders');
        $this->db->join('tblbranch', 'tblbranch.id = tbl_productions_orders.location_id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders.created_by', 'left');
        $this->db->join($tbProductionsPlanOrdersByOrders, 'tb_orders.productions_order_id = tbl_productions_orders.id', 'left');
        $this->db->join($tbProductionsPlanOrdersByBusinessPlan, 'tb_business_plan.productions_order_id = tbl_productions_orders.id', 'left');

        $isFinished = "(
            SELECT tbl_productions_orders_items_stages.id
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id AND tbl_productions_orders_items_stages.final_stage = 1 AND tbl_productions_orders_items_stages.active = 0
        )";

        $this->db->where("(NOT exists ($isFinished))");

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            $this->db->where("tbl_productions_orders.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            $this->db->where("tbl_productions_orders.date <= '$end_date_search'");
        }

      
        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $aRow) {
                $productions_orders_id = $aRow['id'];
                $options1 = $aRow['options1'];
                $options2 = $aRow['options2'];
                $reference_no_orders = $aRow['reference_no_orders'];
                $reference_no_business_plan = $aRow['reference_no_business_plan'];

                $result[$key]['reference_no_orders'] = $reference_no_orders;
                $result[$key]['reference_no_business_plan'] = $reference_no_business_plan;
                $this->db->select('
                    tbl_productions_orders_items.id as id,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    tbl_products.images as images,
                    tbl_productions_orders_items.quantity,
                    tbl_productions_orders_items.object_item_type as object_item_type,
                    tbl_productions_orders_items.production_plan_item_id as production_plan_item_id
                ');
                $this->db->from('tbl_productions_orders_items');
                $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
                $productions_orders_items = $this->db->get()->result_array();

                $dateEnd = '';
                if (!empty($productions_orders_items)) {
                    foreach ($productions_orders_items as $k => $v) {
                        $object_item_type = $v['object_item_type'];
                        $production_plan_item_id = $v['production_plan_item_id'];
                        $productions_orders_items_id = $v['id'];
                        $reference_no = '';
                        $images = '';
                        if (!empty($v['images'])) {
                            $images = base_url('uploads/products/'.$v['images']);
                        }
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

                        $productions_orders_items[$k]['images'] = $images;
                        $productions_orders_items[$k]['reference_no'] = $reference_no;
    
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
                        $workflow = '';
                        $li = '';
                        $isActive = 0;
                        if (!empty($process)) {
                            foreach ($process as $kkk => $vvv) {
                                if (!empty($vvv['active'])) {
                                    $isActive = $vvv['active'];
                                }
                                $staff_active_name = get_staff_full_name($vvv['staff_active']);
                                $process[$kkk]['staff_active_name'] = $staff_active_name;
    
                                if ($kkk == 1 && empty($vvv['active'])) {
                                    $isNotProducedYet = 1;
                                }
    
                                if (!empty($vvv['active'])) {
                                    $isProduction = 1;
                                }
    
                                if ($vvv['final_stage'] && empty($vvv['active'])) {
                                    $isFinished = 1;
                                }
    
                                if ($vvv['final_stage'] && $vvv['date_active'] > $dateEnd) {
                                    $dateEnd = $vvv['date_active'];
                                }
                            }
                        }
                        
                        $productions_orders_items[$k]['workflow'] = $workflow;
                    }

                    $days = '';
                    $h = '';
                    if (!empty($dateEnd)) {
                        $dateStart = new DateTime($aRow['date']);
                        $dateEnd = new DateTime($dateEnd);
                        $diff = $dateStart->diff($dateEnd);
                        $h = $diff->h;
                        $days = $diff->days;
        
                    }
        
        
                    $result[$key]['days'] = $days;
                    $result[$key]['h'] = $h;
                    $result[$key]['productions_orders_items'] = $productions_orders_items;
                }
                
            }
        }
        $data['result'] = $result;

        //next
        $this->db->select('tbl_productions_orders.id', false);
        $startNest = ($page) * $limit;
        $this->db->limit(1, $startNest);
        $this->db->from('tbl_productions_orders');
        if (!empty($start_date_search)) {
            $this->db->where("tbl_productions_orders.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $this->db->where("tbl_productions_orders.date <= '$end_date_search'");
        }

        $isFinished = "(
            SELECT tbl_productions_orders_items_stages.id
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id AND tbl_productions_orders_items_stages.final_stage = 1 AND tbl_productions_orders_items_stages.active = 0
        )";
        $this->db->where("(NOT exists ($isFinished))");
        $data['next'] = $this->db->get()->num_rows();

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getProcessOrders($page = 1, $limit = 10)
    {
        $isAdmin = $this->isAdmin;
        if (!$this->perViewUseMlAcProductionOrders) {
            $data['result'] = null;
            $data['next'] = 0;
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }

        $result = [];
        $start = ($page - 1) * $limit;

        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $start_date_search = !empty($data_post['start_date_search']) ? $data_post['start_date_search'] : null;
        $end_date_search = !empty($data_post['end_date_search']) ? $data_post['end_date_search'] : null;
        $items_search = !empty($data_post['items_search']) ? $data_post['items_search'] : null;
        $orders_and_business_plan = !empty($data_post['orders_and_business_plan']) ? $data_post['orders_and_business_plan'] : null;

        $tbStatus = "(
            SELECT
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                MAX(tbl_stages.name) as stage_name,
                MAX(tbl_productions_orders_items_stages.final_stage) as final_stage,
                MAX(tbl_productions_orders_items_stages.number) as number
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
                tbl_orders.note as note,
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
        if (!$isAdmin) {
            $selectCost = 0;
        }
       
        $this->db->dbprefix = '';
        $this->db->select('
            tbl_productions_orders_details.id as id,
            tbl_productions_orders.reference_no as reference_no_order,
            tbl_productions_orders_details.reference_no as reference_no,
            CONCAT(COALESCE(tbl_products.images, ""), "||", tbl_products.code) as item_name,
            tbl_products.images as images,
            tbl_productions_orders_details.quantity_warehoused as quantity_finished,
            tb_status.stage_name as status,
            '.$selectCost.' as total_cost,
            tb_orders.note as note_orders,
            tblbranch.name as branch_name,
            tbl_productions_orders_details.object_type as object_type,
            tb_orders.reference_no as reference_no_orders,
            tb_orders.company as company,
            tb_orders.object_id as order_id,
            tb_business_plan.object_id as business_plan_id,
            tb_business_plan.reference_no as reference_no_business_plan,
            tbl_productions_orders_items.quantity as quantity,
            tb_status.final_stage as final_stage,
            tb_status.number as number,
            tbl_productions_orders_items.id as poi_id
        ', FALSE)
        ->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'inner');
        $this->db->join($tbStatus, 'tb_status.productions_orders_items_id = tbl_productions_orders_items.id', 'left');
        $this->db->join('tblbranch', 'tbl_productions_orders.location_id  = tblbranch.id', 'left');
        $this->db->join($tbOrders, 'tb_orders.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
        $this->db->join($tbBusinessPlan, 'tb_business_plan.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');

        if ($isAdmin) {
            $this->db->join($priceMaterial, 'tb_price_material.pod_id = tbl_productions_orders_details.id', 'left');
            $this->db->join($payslips, 'tb_price_material.pod_id = tbl_productions_orders_details.id', 'left');
            $this->db->join($purchaseInternal, 'tb_purchase_internal.pod_id = tbl_productions_orders_details.id', 'left');
        }

        if (!empty($items_search)) {
            $arrItem = explode('__', $items_search);
            $item_id = $arrItem[0];
            $this->db->where("tbl_productions_orders_items.items_id = ".$item_id."");
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            $this->db->where("tbl_productions_orders_details.date_created >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            $this->db->where("tbl_productions_orders_details.date_created <= '$end_date_search'");
        }

        if (!empty($orders_and_business_plan)) {
            $orders_and_business_plan = explode('__', $orders_and_business_plan);
            if ($orders_and_business_plan[0] == "orders") {
                $orders_search = $orders_and_business_plan[1];
                $this->db->where("tb_orders.object_id = '$orders_search'");
            } else if ($orders_and_business_plan[0] == "business_plan") {
                $business_plan_search = $orders_and_business_plan[1];
                $this->db->where("tb_business_plan.object_id = '$business_plan_search'");
            }
        }

      
        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $aRow) {

                $images = '';
                if (!empty($aRow['images'])) {
                    $images = base_url('uploads/products/'.$aRow['images']);
                }
                $result[$key]['images'] = $images;
                           

                $poi_id = $aRow['poi_id'];
                $maxStages = "(
                    SELECT
                        MAX(tbl_productions_orders_items_stages.number) as number
                    FROM tbl_productions_orders_items_stages
                    INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
                    WHERE tbl_productions_orders_items_stages.productions_orders_items_id = '$poi_id'
                )";
                $max = $this->db->query($maxStages)->row_array()['number'];
                $number = $aRow['number'];
                $percent = 0;
                if ($max > 0) {
                    $percent = $number/$max *100;
                }
                $result[$key]['percent'] = $percent;
            }
        }
        $data['result'] = $result;

        //next
        $this->db->select('tbl_productions_orders_details.id', false);
        $startNest = ($page) * $limit;
        $this->db->limit(1, $startNest);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
        if (!empty($start_date_search)) {
            $this->db->where("tbl_productions_orders_details.date_created >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $this->db->where("tbl_productions_orders_details.date_created <= '$end_date_search'");
        }

        if (!empty($items_search)) {
            $arrItem = explode('__', $items_search);
            $item_id = $arrItem[0];
            $this->db->where("tbl_productions_orders_items.items_id = ".$item_id."");
        }

        if (!empty($orders_and_business_plan)) {
            $orders_and_business_plan = explode('__', $orders_and_business_plan);
            $this->db->join($tbOrders, 'tb_orders.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
            $this->db->join($tbBusinessPlan, 'tb_business_plan.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
            if ($orders_and_business_plan[0] == "orders") {
                $orders_search = $orders_and_business_plan[1];
                $this->db->where("tb_orders.object_id = '$orders_search'");
            } else if ($orders_and_business_plan[0] == "business_plan") {
                $business_plan_search = $orders_and_business_plan[1];
                $this->db->where("tb_business_plan.object_id = '$business_plan_search'");
            }
        }
        $data['next'] = $this->db->get()->num_rows();

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getProductionsProducts($page = 1, $limit = 10)
    {
        $isAdmin = $this->isAdmin;
        if (!$this->perViewUseMlAcProductionOrders) {
            $data['result'] = null;
            $data['next'] = 0;
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }

        $result = [];
        $start = ($page - 1) * $limit;

        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $start_date_search = !empty($data_post['start_date_search']) ? $data_post['start_date_search'] : null;
        $end_date_search = !empty($data_post['end_date_search']) ? $data_post['end_date_search'] : null;
        $stages_search = !empty($data_post['stages_search']) ? $data_post['stages_search'] : null;

        $this->db->select('
            tbl_purchase_product_items.item_id as item_id,
            tbl_products.images as images,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            SUM(tbl_purchase_product_items.quantity) as quantity,
            SUM(TIMESTAMPDIFF(SECOND, tbl_productions_orders.date, tbl_purchase_products.date)) as date_diff
        ', false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_purchase_product_items.item_id', 'inner');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_purchase_products.pois_id', 'inner');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id', 'inner');

        $this->db->where('tbl_purchase_product_items.type_item', 'products');
        $this->db->where('tbl_products.type_products', 'products');
        $this->db->where('tbl_productions_orders_items_stages.stage_id', $stages_search);
        $this->db->group_by('tbl_purchase_product_items.item_id');

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            $this->db->where("tbl_purchase_products.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            $this->db->where("tbl_purchase_products.date <= '$end_date_search'");
        }
        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $aRow) {
                $quantity = $aRow['quantity'];
                $date_diff = $aRow['date_diff'];
                $time_average = $date_diff/$quantity;

                // $s = $ss%60;
                $m = floor(($time_average%3600)/60);
                $h = floor(($time_average%86400)/3600);
                $d = floor(($time_average%2592000)/86400);

                $result[$key]['m'] = $m;
                $result[$key]['h'] = $h;
                $result[$key]['d'] = $d;
            }
        }
        $data['result'] = $result;

        //next
        $this->db->select('tbl_purchase_products.id', false);
        $startNest = ($page) * $limit;
        $this->db->limit(1, $startNest);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_purchase_product_items.item_id', 'inner');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_purchase_products.pois_id', 'inner');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id', 'inner');

        $this->db->where('tbl_purchase_product_items.type_item', 'products');
        $this->db->where('tbl_products.type_products', 'products');
        $this->db->where('tbl_productions_orders_items_stages.stage_id', $stages_search);
        $this->db->group_by('tbl_purchase_product_items.item_id');

        if (!empty($start_date_search)) {
            $this->db->where("tbl_purchase_products.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $this->db->where("tbl_purchase_products.date <= '$end_date_search'");
        }
        
        $data['next'] = $this->db->get()->num_rows();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getProductionsCategory($page = 1, $limit = 10)
    {
        $isAdmin = $this->isAdmin;
        if (!$this->perViewUseMlAcProductionOrders) {
            $data['result'] = null;
            $data['next'] = 0;
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }

        $result = [];
        $start = ($page - 1) * $limit;

        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                }
            }
        }

        $start_date_search = !empty($data_post['start_date_search']) ? $data_post['start_date_search'] : null;
        $end_date_search = !empty($data_post['end_date_search']) ? $data_post['end_date_search'] : null;
        $stages_search = !empty($data_post['stages_search']) ? $data_post['stages_search'] : null;

        $this->db->select('
            tbl_category_products.id as category_id,
            tbl_category_products.code as item_code,
            tbl_category_products.name as item_name,
            SUM(tbl_purchase_product_items.quantity) as quantity,
            SUM(TIMESTAMPDIFF(SECOND, tbl_productions_orders.date, tbl_purchase_products.date)) as date_diff
        ', false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_purchase_product_items.item_id', 'inner');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_purchase_products.pois_id', 'inner');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id', 'inner');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'inner');

        $this->db->where('tbl_purchase_product_items.type_item', 'products');
        $this->db->where('tbl_products.type_products', 'products');
        $this->db->where('tbl_productions_orders_items_stages.stage_id', $stages_search);
        $this->db->group_by('tbl_category_products.id');

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            $this->db->where("tbl_purchase_products.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            $this->db->where("tbl_purchase_products.date <= '$end_date_search'");
        }
        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $aRow) {
                $quantity = $aRow['quantity'];
                $date_diff = $aRow['date_diff'];
                $time_average = $date_diff/$quantity;

                // $s = $ss%60;
                $m = floor(($time_average%3600)/60);
                $h = floor(($time_average%86400)/3600);
                $d = floor(($time_average%2592000)/86400);

                $result[$key]['m'] = $m;
                $result[$key]['h'] = $h;
                $result[$key]['d'] = $d;
            }
        }
        $data['result'] = $result;

        //next
        $this->db->select('tbl_purchase_products.id', false);
        $startNest = ($page) * $limit;
        $this->db->limit(1, $startNest);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_purchase_product_items.item_id', 'inner');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_purchase_products.pois_id', 'inner');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id', 'inner');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'inner');

        $this->db->where('tbl_purchase_product_items.type_item', 'products');
        $this->db->where('tbl_products.type_products', 'products');
        $this->db->where('tbl_productions_orders_items_stages.stage_id', $stages_search);
        $this->db->group_by('tbl_category_products.id');

        if (!empty($start_date_search)) {
            $this->db->where("tbl_purchase_products.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $this->db->where("tbl_purchase_products.date <= '$end_date_search'");
        }
        
        $data['next'] = $this->db->get()->num_rows();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getStages() {
        echo json_encode($this->site_model->getStages());
    }
}