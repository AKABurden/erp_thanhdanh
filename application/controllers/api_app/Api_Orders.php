<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/api_app/Api_Controller.php');

class Api_Orders extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('orders_model');
        $this->load->model('quotes_orders_model');
        $this->load->model('manufactures_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('deliveries_model');
        $this->load->model('returned_goods_model');
        $this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('orders');
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

        $this->perViewOrders = has_permission('orders', $this->staffid, 'view');
        $this->perViewOwnOrders = has_permission('orders', $this->staffid, 'view_own');
        $this->perAddOrders = has_permission('orders', $this->staffid, 'create');
        $this->perEditOrders = has_permission('orders', $this->staffid, 'edit');
        $this->perDeleteOrders = has_permission('orders', $this->staffid, 'delete');
        $this->perExportOrders = has_permission('orders', $this->staffid, 'export');
        $this->perApproveOrders = has_permission('orders', $this->staffid, 'approve');
        $this->perPrintOrders = has_permission('orders', $this->staffid, 'print');
        $this->perCostOrders = has_permission('orders', $this->staffid, 'cost');
        $this->perProfitOrders = has_permission('orders', $this->staffid, 'profit');
        $this->perViewWarehouses = has_permission('orders', $this->staffid, 'profit');
        $this->isAdmin = is_admin($this->staffid);
        $this->branchID = get_staff_user_id_branch_app($this->staffid);
        if ($this->branchID == 'main') $this->branchID = 0;
    }

    public function getClients($page = 1, $limit = 10)
    {
        $result = [];
        $start = ($page - 1) * $limit;
        $name_search = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['name_search'])) {
                        $name_search = $data_post['name_search'];
                    }
                }
            }
        }
        $this->db->select("
            tblclients.userid as userid,
            tblclients.zcode,
            tblclients.company as company,
            tblclients.representative,
            tblclients.phonenumber as phonenumber,
            (
                SELECT GROUP_CONCAT(staffid) 
                FROM tblstaff 
                JOIN tblcustomer_admins ON tblcustomer_admins.staff_id = tblstaff.staffid
                WHERE tblcustomer_admins.customer_id = tblclients.userid
            ) as staff_group,
            tblclients.datecreated as datecreated,
            vat,
            tblclients.address as addressClient
            ", FALSE)
            ->from('tblclients')
            ->join('tblstatus_client', 'tblstatus_client.id = tblclients.status_clients', 'left')
            ->join('tblcontacts', 'tblcontacts.userid = tblclients.userid AND tblcontacts.is_primary=1', 'left');

        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->where('tblclients.company like "%' . $name_search . '%"');
            $this->db->or_where('tblclients.phonenumber like "%' . $name_search . '%"');
            $this->db->group_end();
        }
        $this->db->order_by('tblclients.userid DESC');
        $this->db->limit($limit, $start);
        // print_arrays($this->db->get_compile_select());
        $result = $this->db->get()->result_array();
        $data['result'] = $result;

        $this->db->select('tblclients.phonenumber', false);
        $startNest = ($page) * $limit;
        $this->db->limit(1, $startNest);

        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->where('tblclients.company like "%' . $name_search . '%"');
            $this->db->or_where('tblclients.phonenumber like "%' . $name_search . '%"');
            $this->db->group_end();
        }


        $this->db->from('tblclients');
        $this->db->join('tblstatus_client', 'tblstatus_client.id = tblclients.status_clients', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.userid = tblclients.userid', 'left');
        $data['next'] = $this->db->get()->num_rows();

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getOrders($page = 1, $limit = 10)
    {

        if (!$this->perViewOrders && !$this->perViewOwnOrders) {
            $data['result'] = null;
            $data['next'] = 0;
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }
        $result = [];
        $start = ($page - 1) * $limit;


        $staff_search = '';

        $clientid = '';
        $date_start = '';
        $date_end = '';
        $name_search = '';
        $staff_search = '';
        $order_search = '';
        $status = 'all';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['customers_search'])) {
                        $clientid = $data_post['customers_search'];
                    }
                    if (!empty($data_post['start_date_search'])) {
                        $date_start = $data_post['start_date_search'];
                    }
                    if (!empty($data_post['end_date_search'])) {
                        $date_end = $data_post['end_date_search'];
                    }
                    if (!empty($data_post['name_search'])) {
                        $name_search = $data_post['name_search'];
                    }
                    if (!empty($data_post['order_search'])) {
                        $order_search = $data_post['order_search'];
                    }
                    if (!empty($data_post['status'])) {
                        $status = $data_post['status'];
                    }
                }
            }
        }

        $arrIDStaff = employee_manage_staff_app($this->staffid);

        $ckView = "(
            SELECT FIND_IN_SET($this->staffid, tbl_orders.list_users)
        )";

        $isPlan = "(
            SELECT
                tbl_productions_plan.id as id
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_plan_items.productions_plan_id
            WHERE tbl_productions_plan_items.type_object = 'orders' AND tbl_productions_plan_items.object_id = tbl_orders.id
        )";

        $isPo = "(
            SELECT
                tbl_productions_plan_orders.id
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id
            WHERE tbl_productions_plan_orders.productions_plan_id = tbl_orders.id AND tbl_productions_plan_orders.object_type = 'orders'
        )";

        $isPP = "(
            SELECT
                tbl_purchase_products.id as id
            FROM tbl_productions_orders_details
            INNER JOIN tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
            WHERE tbl_productions_orders_details.object_id = tbl_orders.id AND tbl_productions_orders_details.object_type = 'orders' AND tbl_purchase_products.final_stage = 1
        )";

        $isOutsource = "(
            SELECT
                tbl_outsource.id as id, 
                tbl_outsource.reference_no
            FROM tbl_outsource_items
            INNER JOIN tbl_outsource ON tbl_outsource.id = tbl_outsource_items.outsource_id
            WHERE tbl_outsource_items.object_type = 'orders' AND tbl_outsource_items.order_id = tbl_orders.id
        )";

        $isImportOutsource = "(
            SELECT
                tbl_import_outsource.id as id, 
                tbl_import_outsource.reference_no
            FROM tbl_import_outsource_items
            INNER JOIN tbl_import_outsource ON tbl_import_outsource.id = tbl_import_outsource_items.import_outsource_id
            WHERE tbl_import_outsource_items.object_type = 'orders' AND tbl_import_outsource_items.order_id = tbl_orders.id
        )";

        $delivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                SUM(tbl_delivery_items.quantity) as total_quantity_delivery
            FROM tbl_delivery_items
            WHERE tbl_delivery_items.id_import IS NOT NULL AND tbl_delivery_items.id_import != ''
            GROUP BY tbl_delivery_items.order_item_id
        ) dv";

        $orderItems = "(
            SELECT
                ot.order_id as order_id,
                ot.type_item as type_item,
                ot.item_id as item_id,
                SUM(ot.quantity_check) as quantity_check
            FROM (
                SELECT
                    tbl_order_items.order_id,
                    tbl_order_items.type_item,
                    tbl_order_items.item_id,
                    tbl_order_items.quantity - COALESCE(dv.total_quantity_delivery, 0) as quantity_check
                FROM tbl_order_items
                LEFT JOIN $delivery ON tbl_order_items.id = dv.order_item_id
            ) ot
            GROUP BY ot.order_id, ot.type_item, ot.item_id
        )";

        $warehouseTotal = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                IF(tblwarehouse_items.type_items = 'product', 'products', IF(tblwarehouse_items.type_items = 'nvl', 'materials', 'items')) as type_items,
                SUM(tblwarehouse_items.product_quantity) as quantity_warehouse
            FROM tblwarehouse_items
            WHERE (tblwarehouse_items.type_items = 'product' OR tblwarehouse_items.type_items = 'items' OR tblwarehouse_items.type_items = 'nvl')
            GROUP BY tblwarehouse_items.type_items, tblwarehouse_items.id_items
        )";

        $isQtyWarehouse = "(
            SELECT count(*)
            FROM $orderItems ott
            LEFT JOIN $warehouseTotal wt ON wt.id_items = ott.item_id AND wt.type_items = ott.type_item
            WHERE ott.order_id = tbl_orders.id AND ott.quantity_check > COALESCE(wt.quantity_warehouse, 0)
        )";


        $orderItemDelivery = "(
            SELECT COUNT(*)
            FROM tbl_order_items
            WHERE tbl_order_items.order_id = tbl_orders.id AND tbl_order_items.quantity > tbl_order_items.quantity_delivery
            LIMIT 1
        )";

        $statusDelivery = "(
            IF (tbl_orders.count_delivery > 0, $orderItemDelivery, -1)
        )";

        $this->db->dbprefix = '';
        $this->db->select("
            tbl_orders.id as id,
            tbl_orders.date as date,
            tbl_orders.reference_no as reference_no,
            tblclients.company as customer_name,
            tblshipping_client.address as address_delivery,
            tbl_orders.grand_total as grand_total,
            0 as cost_price,
            0 as profit,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as created_by,
            tbl_orders.status as status,
            CONCAT(staff_status.firstname, ' ', staff_status.lastname, '') as user_status,
            $ckView as list_users,
            0 as workflow,
            $isQtyWarehouse as status_warehouses,
            tbl_orders.type_bills as type_bills,
            tbl_orders.note as note,
            '' as khsxText,
            '' as lsxctText,
            '' as sxxText,
            '' as xkghText,
            '' as xgcText,
            '' as ngcText,
            $statusDelivery as statusDelivery,
            CONCAT(tbl_contracts_sales.prefix, '-', tbl_contracts_sales.code) as status_contracts,
            tbl_orders.status_payment as status_payment,
            tblbranch.name as name_branch
            ", FALSE)
            ->from('tbl_orders')
            ->join('tblshipping_client', 'tblshipping_client.id = tbl_orders.address_delivery_id', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_orders.created_by', 'left')
            ->join('tbl_contracts_sales', 'tbl_contracts_sales.id = tbl_orders.contract_id', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_orders.id_branch', 'left')
            ->join('tblstaff staff_status', 'staff_status.staffid = tbl_orders.user_status', 'left');

        if (!$this->perViewOrders) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                $this->db->where('(tbl_orders.created_by IN (' . $coverStr . ') OR tbl_orders.employee_id IN (' . $coverStr . '))');
            }
        }

        if ($status != 'all') {
            if ($status == "un_approved" || $status == "approved") {
                $this->db->where('tbl_orders.status', $status);
            } else {
                if ($status == "lkhsx") {
                    $this->db->where("(exists ($isPlan))");
                } else if ($status == "dsxtcty") {
                    $this->db->where("(exists ($isPo))");
                } else if ($status == "sxx") {
                    $this->db->where("(exists ($isPP))");
                } else if ($status == "xgcn") {
                    $this->db->where("(exists ($isOutsource))");
                } else if ($status == "ngcn") {
                    $this->db->where("(exists ($isImportOutsource))");
                } else if ($status == "gh") {
                    $this->db->where("tbl_orders.count_delivery >", 0);
                }
            }
        }
        if (!empty($order_search)) {
            $this->db->where('tbl_orders.id', $order_search);
        }
        if (!empty($clientid)) {
            $this->db->where('tbl_orders.customer_id', $clientid);
        }
        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->where('tbl_orders.reference_no like "%' . $name_search . '%"');
            $this->db->or_where('tblclients.phonenumber like "%' . $name_search . '%"');
            $this->db->or_where('tblclients.company like "%' . $name_search . '%"');
            $this->db->or_where('tbl_orders.note like "%' . $name_search . '%"');
            $this->db->group_end();
        }
        if (!empty($date_start)) {
            $this->db->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', to_sql_date($date_start));
        }

        if (!empty($date_end)) {
            $this->db->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', to_sql_date($date_end));
        }
        $this->db->order_by('tbl_orders.date DESC');
        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $aRow) {
                $order_id = $aRow['id'];
                $plan = "(
                    SELECT
                        tbl_productions_plan.id as id, 
                        tbl_productions_plan.reference_no
                    FROM tbl_productions_plan_items
                    INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_plan_items.productions_plan_id
                    WHERE tbl_productions_plan_items.type_object = 'orders' AND tbl_productions_plan_items.object_id = $order_id
                    GROUP BY tbl_productions_plan.id
                )";
                $dtPlan = $this->db->query($plan)->result_array();
                $result[$key]['dtPlan'] = $dtPlan;

                $po = "(
                    SELECT
                        tbl_productions_orders.id as id,
                        tbl_productions_orders.reference_no
                    FROM tbl_productions_plan_orders
                    INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id
                    WHERE tbl_productions_plan_orders.productions_plan_id = $order_id AND tbl_productions_plan_orders.object_type = 'orders'
                )";
                $dtPO = $this->db->query($po)->result_array();
                $result[$key]['dtPO'] = $dtPO;

                $pp = "(
                    SELECT
                        tbl_purchase_products.id as id,
                        tbl_purchase_products.reference_no
                    FROM tbl_productions_orders_details
                    INNER JOIN tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
                    WHERE tbl_productions_orders_details.object_id = $order_id AND tbl_productions_orders_details.object_type = 'orders' AND tbl_purchase_products.final_stage = 1
                )";
                $dtPP = $this->db->query($pp)->result_array();
                $result[$key]['dtPP'] = $dtPP;

                $outsource = "(
                    SELECT
                        tbl_outsource.id as id, 
                        tbl_outsource.reference_no
                    FROM tbl_outsource_items
                    INNER JOIN tbl_outsource ON tbl_outsource.id = tbl_outsource_items.outsource_id
                    WHERE tbl_outsource_items.object_type = 'orders' AND tbl_outsource_items.order_id = $order_id
                    GROUP BY tbl_outsource.id
                )";
                $dtOutsource = $this->db->query($outsource)->result_array();
                $result[$key]['dtOutsource'] = $dtOutsource;

                $import = "(
                    SELECT
                        tbl_import_outsource.id as id, 
                        tbl_import_outsource.reference_no
                    FROM tbl_import_outsource_items
                    INNER JOIN tbl_import_outsource ON tbl_import_outsource.id = tbl_import_outsource_items.import_outsource_id
                    WHERE tbl_import_outsource_items.object_type = 'orders' AND tbl_import_outsource_items.order_id = $order_id
                    GROUP BY tbl_import_outsource.id
                )";
                $dtImport = $this->db->query($import)->result_array();
                $result[$key]['dtImport'] = $dtImport;

                $delivery = "(
                    SELECT
                        tbl_deliveries.id as id,
                        tbl_deliveries.reference_no
                    FROM tbl_orders_deliveries
                    INNER JOIN tbl_deliveries ON tbl_orders_deliveries.delivery_id = tbl_deliveries.id
                    WHERE tbl_orders_deliveries.order_id = $order_id
                )";
                $dtDelivery = $this->db->query($delivery)->result_array();
                $result[$key]['dtDelivery'] = $dtDelivery;
            }
        }
        $data['result'] = $result;

        //next
        $this->db->select('tbl_orders.*,tblclients.phonenumber', false);
        $startNest = ($page) * $limit;
        $this->db->limit(1, $startNest);
        if (!empty($clientid)) {
            $this->db->where('tbl_orders.customer_id', $clientid);
        }
        if (!empty($order_search)) {
            $this->db->where('tbl_orders.id', $order_search);
        }
        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->where('tbl_orders.reference_no like "%' . $name_search . '%"');
            $this->db->or_where('tblclients.phonenumber like "%' . $name_search . '%"');
            $this->db->or_where('tblclients.company like "%' . $name_search . '%"');
            $this->db->or_where('tbl_orders.note like "%' . $name_search . '%"');
            $this->db->group_end();
        }
        if (!empty($date_start)) {
            $this->db->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', to_sql_date($date_start));
        }
        if (!empty($date_end)) {
            $this->db->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', to_sql_date($date_end));
        }
        if ($status != 'all') {
            if ($status == "un_approved" || $status == "approved") {
                $this->db->where('tbl_orders.status', $status);
            } else {
                if ($status == "lkhsx") {
                    $this->db->where("(exists ($isPlan))");
                } else if ($status == "dsxtcty") {
                    $this->db->where("(exists ($isPo))");
                } else if ($status == "sxx") {
                    $this->db->where("(exists ($isPP))");
                } else if ($status == "xgcn") {
                    $this->db->where("(exists ($isOutsource))");
                } else if ($status == "ngcn") {
                    $this->db->where("(exists ($isImportOutsource))");
                } else if ($status == "gh") {
                    $this->db->where("tbl_orders.count_delivery >", 0);
                }
            }
        }
        
        if (!$this->perViewOrders) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                $this->db->where('(tbl_orders.created_by IN (' . $coverStr . ') OR tbl_orders.employee_id IN (' . $coverStr . '))');
            }
        }
        $this->db->from('tbl_orders');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
        $data['next'] = $this->db->get()->num_rows();

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getDetailOrders($id = '')
    {
        if (!$this->perViewOrders && !$this->perViewOwnOrders) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }
        $order = $this->orders_model->rowOrderById($id);
        if (empty($order)) {
            $data['result'] = 0;
            $data['message'] = lang('Phiếu không tồn tại');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }
        $client = get_table_where('tblclients', ['userid' => $order['customer_id']], '', 'row_array');


        $data['order'] = $order;
        $data['order']['phonenumber'] = $client['phonenumber'];
        $address_delivery = $this->site_model->rowShippingClient($order['address_delivery_id']);
        $data['order']['address_delivery'] = !empty($address_delivery) ? $address_delivery['address'] : '';

        $person_contact = $this->site_model->rowContact($order['person_contact_id']);
        $data['order']['person_contact'] = !empty($person_contact) ? $person_contact['firstname'] : '';

        $employee = get_staff_full_name($order['employee_id']);
        $data['order']['employee'] = !empty($employee) ? $employee : '';

        $table_discount = $this->site_model->rowDiscountById($order['table_discount_id']);
        $data['order']['table_discount'] = !empty($table_discount) ? $table_discount['name_discount'] : '';

        $table_price = $this->site_model->rowSetPricesById($order['table_price_id']);

        $data['order']['table_price'] = !empty($table_price) ? $table_price['name'] : '';
        $data['order']['status'] = lang($order['status']);
        $name_branch = get_table_where('tblbranch', array('id' => $order['id_branch']), '', 'row_array');
        $data['order']['name_branch'] = !empty($name_branch) ? $name_branch['name'] : '';

        $transport = $this->site_model->rowSupplier($order['transporter_id']);
        $data['order']['transport'] = !empty($transport) ? $transport['company'] : '';

        $data['order']['created_by_name'] = get_staff_full_name($data['order']['created_by']);

        $data['order']['user_name_status'] = get_staff_full_name($data['order']['user_status']);
        $data['order']['total_returns'] = $this->orders_model->getTotalOrdersReturn($id)['total_return'];

        $data['order']['totalPayment'] = $order['total_payment'];
        $data['order']['staff_coupon'] = get_staff_full_name($order['staff_coupon']);
        $data['order']['payment_mode'] = $this->site_model->rowPaymentMode($order['payment_mode']);



        $items = $this->orders_model->getOrderItemsByOrderId($id);

        foreach ($items as $key => $value) {
            $type_item = $value['type_item'];
            $items_id = $value['item_id'];
            $images = '';

            $name_color = '';
            $size = '';
            $mt = '';
            $images = '';
            $nameMode = '';
            $namePack = '';
            if ($type_item == "products") {
                $info = $this->products_model->rowProduct($items_id);
                $unit = $this->unit_model->rowUnit($info['unit_id']);
                if (!empty($info['images'])) {
                    $images = base_url('uploads/products/' . $info['images']);
                }
                $color = $this->products_model->getColorsByProductId($items_id);
            } elseif ($type_item == "items") {
                $info = $this->items_model->rowItems($items_id);
                $unit = $this->unit_model->rowUnit($info['unit']);
                if (!empty($info['avatar'])) {
                    $images = base_url($info['avatar']);
                }
                $color = $this->site_model->getColorById($info['color_id']);
            } elseif ($type_item == "materials") {
                $info = $this->items_model->rowMaterial($items_id);
                $unit = $this->unit_model->rowUnit($info['unit_id']);
                if (!empty($info['images'])) {
                    $images = base_url('uploads/materials/' . $info['images']);
                }
            }
            if (empty($images)) {
                $images = base_url('assets/images/tnh/no_image.png');
            }
            $sub_date = $this->orders_model->getOrderItemShippingsByOrderItemId($value['id']);
            $items[$key]['items'] = $info;
            $items[$key]['items']['unit'] = $unit['unit'];
            $items[$key]['items']['images'] = $images;
            $items[$key]['items']['sub_date'] = $sub_date;

            $items[$key]['items']['type_name'] = (($type_item == "products") ? lang($type_item) : lang('ch_items'));

            $itemsProcess = $this->site_model->getProductionsOrdersItemsStages('orders', $value['id']);
            $items[$key]['items']['items_process'] = $itemsProcess;
            $items[$key]['items']['message_items_process'] = lang('tnh_no_productions_orders_yet');
        }
        $data['items'] = $items;

        $purchases = $this->orders_model->getPurchasesByOrders($id);
        $data['purchases'] = $purchases;
        $deliveries = $this->orders_model->getDeliveriesByOrderId($id);
        $data['deliveries'] = $deliveries;

        $deliveriesItems = $this->orders_model->getDeliveriesItemsByOrderId($id);
        if (!empty($deliveriesItems)) {
            foreach ($deliveriesItems as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];

                $name_color = '';
                $size = '';
                $mt = '';
                $images = '';
                $nameMode = '';
                $namePack = '';

                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    $color = $this->products_model->getColorsByProductId($items_id);
                    $name_color = !empty($color) ? $color[0]['name'] : '';

                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    $color = $this->site_model->getColorById($info['color_id']);
                    $name_color = !empty($color['name']) ? $color['name'] : '';

                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $deliveriesItems[$key]['unit_name'] = $unit['unit'];
                $deliveriesItems[$key]['images'] = $images;
            }
        }

        $data['deliveriesItems'] = $deliveriesItems;

        $data['gifts'] = $this->site_model->getGiftOrderItems($id);
        $array_attachments = array();
        if (!empty($order['attachments'])) {
            $attachments = explode('||', $order['attachments']);
            foreach ($attachments as $key => $value) {
                $array_attachments['name'] = $value;
                $array_attachments['link'] = base_url('uploads/orders/' . $value);
            }
        }
        $data['array_attachments'] = $array_attachments;
        $data['returned_goods'] = $this->returned_goods_model->getReturnedGoodsByOrder($id);
        $data['complains'] = $this->site_model->getTicketsByOrderId($id);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die;
    }



    
    public function has_permission() {
        $permission = [
            'manufactures_productions_orders_success' => has_permission('manufactures_productions_orders', $this->staffid, 'qc'),
            'quality_control_view' => has_permission('quality_control', $this->staffid, 'view'),
            'quality_control_view_own' => has_permission('quality_control', $this->staffid, 'view_own'),
            'quality_control_create' => has_permission('quality_control', $this->staffid, 'create'),
        ];

        echo json_encode($permission);die();
    }
}