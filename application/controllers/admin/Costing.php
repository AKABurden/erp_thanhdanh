<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Costing extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('costing_model');
        $this->load->model('products_model');
    }

    public function index()
    {
        $data['title'] = lang('tnh_list_costing');
        $this->load->view('admin/costing/index', $data);
    }

    public function add_costing()
    {
        ini_set('max_execution_time', 300);
        if ($this->input->post('add'))
        {
            $data = [];
            $this->form_validation->set_rules('start_date', lang("start_date"), 'required');
            $this->form_validation->set_rules('end_date', lang("end_date"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true)
            {
                $start_date = to_sql_date($this->input->post('start_date'));
                $end_date = to_sql_date($this->input->post('end_date'));
                $name = $this->input->post('name');
                // $pp_id = $this->input->post('pp_id');
                $directMaterial = number_unformat($this->input->post('direct_material'));
                $directLaborCosting = number_unformat($this->input->post('direct_labor_costing'));
                $generalCosting = number_unformat($this->input->post('general_costing'));
                $count_items = 0;

                $pp_arr_id = [];

                $product_id = $this->input->post('product_id');
                if (!empty($product_id)) {
                    foreach ($product_id as $key => $value) {
                        $product = $this->products_model->rowProduct($value);
                        if (empty($product)) continue;

                        $pp_item_id = $this->input->post('pp_id')[$key];
                        $type_item = $this->input->post('type_item')[$key];
                        $soLuongHT = number_unformat($this->input->post('soLuongHT')[$key]);
                        $chiPhiNVLMatHang = number_unformat($this->input->post('chiPhiNVLMatHang')[$key]);
                        // $chiPhiSXMatHang = $this->input->post('chiPhiSXMatHang')[$key];
                        // $chiPhiNCTTMatHang = $this->input->post('chiPhiNCTTMatHang')[$key];
                        $chiPhiNVLDoDangDK = number_unformat($this->input->post('chiPhiNVLDoDangDK')[$key]);
                        $chiPhiNVLDoDangCK = number_unformat($this->input->post('chiPhiNVLDoDangCK')[$key]);

                        if ($directMaterial == 0) {
                            $chiPhiSXMatHang = 0;
                            $chiPhiNCTTMatHang = 0;
                        } else {
                            //Công thức CP Sản xuất cho mặt hàng: (Chi phí sản xuất chung/Chi phí NVL trực tiếp) * CP NVL mặt hàng
                            $chiPhiSXMatHang = ($generalCosting/$directMaterial) * $chiPhiNVLMatHang;

                            //Công thức CP nhân công trực tiếp MH: (Chi phí nhân công trực tiếp/Chi phí NVL trực tiếp) * CP NVL mặt hàng
                            $chiPhiNCTTMatHang = ($directLaborCosting/$directMaterial) * $chiPhiNVLMatHang;
                        }

                        //Tổng giá thành: $chiPhiNVLDoDangDK + ($chiPhiNVLMatHang + $chiPhiNCTTMatHang + $chiPhiSXMatHang) - $chiPhiNVLDoDangCK;
                        $tongGiaThanh = $chiPhiNVLDoDangDK + ($chiPhiNVLMatHang + $chiPhiNCTTMatHang + $chiPhiSXMatHang) - $chiPhiNVLDoDangCK;

                        //Giá thành đơn vị: $tongGiaThanh/$soLuongHT;
                        $giaThanhDonVi = $tongGiaThanh/$soLuongHT;

                        $items[] = [
                            'type_item' => $type_item,
                            'item_id' => $value,
                            'item_code' => $product['code'],
                            'item_name' => $product['name'],
                            'soLuongHT' => $soLuongHT,
                            'chiPhiNVLMatHang' => $chiPhiNVLMatHang,
                            'chiPhiSXMatHang' => $chiPhiSXMatHang,
                            'chiPhiNCTTMatHang' => $chiPhiNCTTMatHang,
                            'chiPhiNVLDoDangDK' => $chiPhiNVLDoDangDK,
                            'chiPhiNVLDoDangCK' => $chiPhiNVLDoDangCK,
                            'tongGiaThanh' => $tongGiaThanh,
                            'giaThanhDonVi' => $giaThanhDonVi,
                            'pp_item_id' => $pp_item_id,
                        ];

                        array_push($pp_arr_id, $pp_item_id);
                        $count_items++;
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }

                $pp_arr_id = implode(',', $pp_arr_id);
                $pp_arr_id = explode(',', $pp_arr_id);
                $pp_arr_id = array_unique($pp_arr_id);
                $pp_id = implode(',', $pp_arr_id);
                // print_arrays($pp_id);

                $options = [
                    'pp_id' => $pp_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'name' => $name,
                    'direct_material' => $directMaterial,
                    'direct_labor_costing' => $directLaborCosting,
                    'general_costing' => $generalCosting,
                    'count_items' => $count_items,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                ];
                // print_arrays($items);

                $costing_id = $this->costing_model->insertCosting($options);

                if ($costing_id) {
                    $pp_id = explode(',', $pp_id);
                    foreach ($items as $key => $value) {
                        $value['costing_id'] = $costing_id;
                        $costing_item_id = $this->costing_model->insertCostingItems($value);
                        if ($costing_item_id) {
                            $this->costing_model->updatePurchaseProductItems($pp_id, $value['item_id'], $value['giaThanhDonVi']);
                            $this->costing_model->updatePriceWarehouseProduct($pp_id, $value['item_id'], $value['giaThanhDonVi']);
                        }
                    }

                    foreach ($pp_id as $key => $value) {
                        $this->costing_model->calGrandTotalPurchaseProduct($value);
                    }

                    //xủ lý đơn hàng có sự dụng phiếu nhập để tính lại giá thành
                    $this->load->model('deliveries_model');
                    $this->load->model('orders_model');
                    $exWarehouseObject = [];

                    $this->db->select('tbl_ex_warehouse_object.*, tblwarehouse_product.price as price_new');
                    $this->db->from('tblwarehouse_product');
                    $this->db->join('tbl_ex_warehouse_object', 'tbl_ex_warehouse_object.id_import = tblwarehouse_product.id');
                    $this->db->where_in('tblwarehouse_product.import_id', $pp_id);
                    $exWarehouse = $this->db->get()->result_array();

                    $arrTotalOld = [];
                    if (!empty($exWarehouse)) {
                        foreach ($exWarehouse as $k => $val) {
                            $qtyIm = $val['quantity'];
                            $price = $val['price_new'];
                            $total = $qtyIm * $price;

                            $exWarehouseObject[] = [
                                'id' => $val['id'],
                                'type' => 1,
                                'object_id' => $val['object_id'],
                                'object_item_id' => $val['object_item_id'],
                                'object_id_more' => $val['object_id_more'],
                                'id_import' => $val['id_import'],
                                'quantity' => $qtyIm,
                                'price' => $price,
                                'total' => $total,
                            ];

                            $arrTotalOld[] = $val['total'];
                        }

                        $arrOrder = [];
                        if (!empty($exWarehouseObject))
                        {
                            $up = $this->deliveries_model->updateBatchExWarehouseObject($exWarehouseObject);
                            $up = true;
                            if ($up)
                            {
                                foreach ($exWarehouseObject as $k => $val) {
                                    $cost = $val['total'];
                                    $costOldOb = $arrTotalOld[$k];
                                    $delivery_item_id = $val['object_item_id'];
                                    // $delivery_item_id = $val['object_id_more'];

                                    $this->db->select('tbl_order_items.order_id, tbl_order_items.id, tbl_order_items.total_amount, tbl_order_items.cost');
                                    $this->db->from('tbl_delivery_items');
                                    $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
                                    $this->db->where('tbl_delivery_items.id', $delivery_item_id);
                                    $order_item = $this->db->get()->result_array();
                                    if (!empty($order_item)) {
                                        foreach ($order_item as $i => $v) {
                                            $order_id = $v['order_id'];
                                            $order_item_id = $v['id'];
                                            if (!in_array($order_id, $arrOrder)) {
                                                array_push($arrOrder, $order_id);
                                            }
                                            $totalAmount = $v['total_amount'];
                                            $costOld = $v['cost'];
                                            $costNew = $cost - $costOldOb + $costOld;
                                            $profitNew = $totalAmount - $costNew;

                                            // print_arrays($cost, '</br>', $costOldOb, '</br>', $costOld);

                                            $this->orders_model->updateOrderItemNew($order_item_id, ['cost' => $costNew, 'profit' => $profitNew]);
                                        }
                                    }

                                }

                                //Update lại đơn hàng cha
                                if (!empty($arrOrder)) {
                                    foreach ($arrOrder as $k => $val) {
                                        $this->db->select('tbl_orders.charge_party, tbl_orders.cost_delivery, tbl_orders.grand_total, tbl_orders.total_tax, SUM(tbl_order_items.cost) as total_cost', false);
                                        $this->db->from('tbl_orders');
                                        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');
                                        $this->db->where('tbl_orders.id', $val);
                                        $orders = $this->db->get()->result_array();
                                        if (!empty($orders)) {
                                            foreach ($orders as $i => $v) {
                                                $totalCost = $v['total_cost'];
                                                $grandTotal = $v['grand_total'] - $v['total_tax'];
                                                $chargeParty = $v['charge_party'];
                                                $costDelivery = $v['cost_delivery'];
                                                
                                                if ($chargeParty == "customer") {
                                                    $grandTotal = $grandTotal - $costDelivery;
                                                }
                                                $totalProfit = $grandTotal - $costDelivery - $totalCost;

                                                $this->orders_model->updateOrdersNew($val, ['total_cost' => $totalCost, 'total_profit' => $totalProfit]);
                                            }
                                        }
                                    }
                                }
                                // end
                            }
                        }
                    }
                    $data['result'] = 1;
                    set_alert('success', lang('success'));
                    $data['message'] = lang('success');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }
        $data['title'] = lang('tnh_add_costing');
        $data['breadcrumb'] = [array('link' => base_url('admin/costing'), 'page' => lang('tnh_list_costing')), array('link' => '#', 'page' => lang('tnh_add_costing'))];
        $this->load->view('admin/costing/add_costing', $data);
    }

    public function showInfoCosting()
    {
        if ($this->input->post())
        {
            $start_date = $this->input->post('start_date') ? to_sql_date($this->input->post('start_date')) : date('Y-m-d');
            $end_date = $this->input->post('end_date') ? to_sql_date($this->input->post('end_date')) : date('Y-m-d');
            //Chi phí nhân công sản xuất trực tiếp
            $directLaborCosting = $this->costing_model->costingOther($start_date, $end_date, 1);
            //Chi phí sản xuất chung
            $generalCosting = $this->costing_model->costingOther($start_date, $end_date, 2);
            //Chi phí NVL trực tiếp
            $directMaterial = $this->costing_model->directMaterialExport($start_date, $end_date);
            $truPheLieu = $this->costing_model->truPheLieu($start_date, $end_date);
            //Danh sách sản phẩm trong kỳ
            $products = $this->costing_model->getProductsCosting($start_date, $end_date);

            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['directLaborCosting'] = $directLaborCosting['total'];
            $data['generalCosting'] = $generalCosting['total'];
            $data['directMaterial'] = $directMaterial['total'] - $truPheLieu['total'];
            $data['products'] = $products;
            $this->load->view('admin/costing/info_costing', $data);
        }
    }

    public function getCosting()
    {
        //hoàng crm bổ xung
        // $arrStaffId = get_group_branch();
        // CASE tblstaff.id_branch
        //         WHEN 0 THEN 'Tổng Công Ty'
        //         ELSE tblbranch.name
        //     END as branch_name,
        //end

        $this->datatables->select("
            tbl_costing.id as id,
            tbl_costing.start_date as start_date,
            tbl_costing.end_date as end_date,
            tbl_costing.name as name,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as created_by,
            
            ", FALSE)

        ->from('tbl_costing')
        ->join('tblstaff', 'tblstaff.staffid = tbl_costing.created_by', 'left');

        //hoàng crm bổ xung
        // ->join('tblbranch', 'tblbranch.id = tblstaff.id_branch', 'left');
        //end

        //hoàng crm bổ xung
        // if($arrStaffId != array()) {
        //     $this->datatables->where('tbl_costing.created_by IN ('.implode(",", $arrStaffId).')');
        // }
        //end

        $view = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/costing/view_costing/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> '.lang('view').'</a>';

        $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\''.base_url('admin/orders/deleteOrders/$1').'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
            <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
        "><i class="fa fa-remove width-icon-actions"></i> '.lang('delete').' '.lang('tnh_order').'</a>';

        $delete = '';

        $actions = '
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            '.lang('actions').'
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>'.$view.'</li>
                <li class="not-outside">'.$delete.'</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $data = json_decode($this->datatables->generate());
        echo json_encode($data);
    }

    public function view_costing($id)
    {
        $data['id'] = $id;
        $costing = $this->costing_model->rowCosting($id);
        $items = $this->costing_model->getCostingItems($id);

        $data['items'] = $items;
        $data['costing'] = $costing;
        $data['created_by'] = get_staff_full_name($costing['created_by']);
        $this->load->view('admin/costing/view_costing', $data);
    }

    public function infoDetailCosting()
    {   
        $arrParams = $this->input->get('arrParams');
        $start_date = $arrParams[1]['mValue'];
        $end_date = $arrParams[2]['mValue'];
        $type_object = $arrParams[3]['mValue'];
        $item_id = $arrParams[4]['mValue'];
        $type_item = $arrParams[5]['mValue'];

        $title = '';
        $note = '';
        $link = '';
        $typeDetail = '';
        
        if ($type_object == "so_luong_ht") {
            $title = "Số lượng hoàn thành mặt hàng";
            $info = $this->products_model->rowProduct($item_id);
            $note = "Mặt hàng: ".$info['code']."(".$info['name'].")";
            $link = base_url('admin/costing/getTotalFinished');
        } else if ($type_object == "chi_phi_nvl_mh") {
            $title = "Chi phí NVL mặt hàng";
            $info = $this->products_model->rowProduct($item_id);
            $note = "Mặt hàng: ".$info['code']."(".$info['name'].")";
            $link = base_url('admin/costing/getChiPhiNVL');
        } else if ($type_object == "chi_phi_nhan_cong_truc_tiep") {
            $title = "Chi phí nhân công trực tiếp";
            $link = base_url('admin/costing/getChiPhiNhanCongTT');
        } else if ($type_object == "chi_phi_san_xuat_chung") {
            $title = "Chi phí sản xuất chung";
            $link = base_url('admin/costing/getChiPhiSanXuatChung');
        } 

        $data['item_id'] = $item_id;
        $data['type_item'] = $type_item;
        $data['typeDetail'] = $typeDetail;
        $data['link'] = $link;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['type_object'] = $type_object;
        $data['title'] = $title;
        $data['note'] = $note;
        $this->load->view('admin/costing/info_detail_costing', $data);
    }

    public function getTotalFinished()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $item_id = $this->input->post('item_id');
        $type_item = $this->input->post('type_item');

        $this->datatables->select("
            tbl_purchase_products.id as id,
            tbl_purchase_products.date as date,
            tbl_purchase_products.reference_no as reference_no,
            SUM(tbl_purchase_product_items.quantity) as quantity,
        ", false);
        $this->datatables->from('tbl_purchase_products');
        $this->datatables->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        $this->datatables->where('tbl_purchase_products.warehouseman_id >', 0);
        $this->datatables->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=', $end_date);
        $this->datatables->where('tbl_purchase_product_items.item_id', $item_id);
        $this->datatables->where('tbl_purchase_product_items.type_item', $type_item);

        $this->datatables->group_by('tbl_purchase_products.id');

        // print_arrays($this->db->get_compiled_select('tbl_purchase_products'));
        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        $iDisplayStart = $this->input->post('iDisplayStart');
        foreach ($data->aaData as $key => $value) {
            $purchase_products_id = $value[0];
            $date = _dt($value[1]);
            $reference_no = $value[2];
            $grand_total = $value[3];

            $iDisplayStart++;
            $arrData[$index][0] = '<div class="text-center">'.$iDisplayStart.'</div>';
            $arrData[$index][1] = '<div class="">'.$date.'</div>';
            $arrData[$index][2] = '<div><a class="tnh-modal2" data-toggle="modal" data-target="#myModal2" href="'.base_url('admin/stock/view_purchase_product/'.$purchase_products_id).'">'.$reference_no.'</a></div>';
            $arrData[$index][3] = '<div class="text-center">'.formatNumber($grand_total).'</div>';

            $grandTotal+= $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = formatNumber($grandTotal);
        echo json_encode($data);
    }

    public function getChiPhiNVL()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $item_id = $this->input->post('item_id');
        $type_item = $this->input->post('type_item');

        $this->db->select("
            GROUP_CONCAT(DISTINCT(tbl_purchase_products.productions_orders_details_id)) as pod_id
        ", false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        $this->db->where('tbl_purchase_products.warehouseman_id >', 0);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=', $end_date);
        $this->db->where('tbl_purchase_product_items.item_id', $item_id);
        $purchaseProduct = $this->db->get()->row_array();


        $tb = "(
            SELECT
                'suggest_exporting' as type,
                tbl_suggest_exporting.id as id,
                tbl_suggest_exporting.date_convert_stock as date,
                tbl_suggest_exporting.reference_stock as reference_no,
                tbl_suggest_exporting.grand_total as grand_total
            FROM tbl_suggest_exporting
            WHERE tbl_suggest_exporting.warehouseman_id > 0 AND tbl_suggest_exporting.productions_orders_details_id IN (".$purchaseProduct['pod_id'].")

            UNION ALL

            SELECT
                'purchase_internal' as type,
                tbl_purchase_internal.id as id,
                tbl_purchase_internal.date as date,
                tbl_purchase_internal.reference_no as reference_no,
                tbl_purchase_internal.grand_total as grand_total
            FROM tbl_purchase_internal
            WHERE tbl_purchase_internal.warehouseman_id > 0 AND tbl_purchase_internal.pod_id IN (".$purchaseProduct['pod_id'].")

        ) tb";

        $this->db->dbprefix = '';
        $this->datatables->select("
            tb.id as id,
            tb.date as date,
            tb.reference_no as reference_no,
            tb.grand_total as grand_total,
            tb.type as type
        ", false);
        $this->datatables->from('(SELECT 1 as id) cs');
        $this->datatables->join($tb, "cs.id = 1");

        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        $grandTotalPheLieu = 0;
        $iDisplayStart = $this->input->post('iDisplayStart');
        foreach ($data->aaData as $key => $value) {
            $suggest_exporting_id = $value[0];
            $date = _dt($value[1]);
            $reference_no = $value[2];
            $grand_total = $value[3];
            $type = $value[4];

            $iDisplayStart++;
            $arrData[$index][0] = '<div class="text-center">'.$iDisplayStart.'</div>';
            $arrData[$index][1] = '<div>'.$date.'</div>';
            if ($type == "suggest_exporting") {
                $arrData[$index][2] = '<div><a class="tnh-modal2" data-toggle="modal" data-target="#myModal2" href="'.base_url('admin/stock/view_exporting_production/'.$suggest_exporting_id).'">'.$reference_no.'</a><div><span class="label label-success">Xuất kho sản xuất</span></div></div>';
                $arrData[$index][3] = '<div class="text-right">'.formatNumber($grand_total).'</div>';
                $arrData[$index][4] = '<div class="text-right">0</div>';
                $grandTotal+= $grand_total;
            } else {
                $arrData[$index][2] = '<div><a class="tnh-modal2" data-toggle="modal" data-target="#myModal2" href="'.base_url('admin/stock/view_purchase_internal/'.$suggest_exporting_id).'">'.$reference_no.'</a><div><span class="label label-danger">Thu hồi phế liệu</span></div></div>';
                $arrData[$index][3] = '<div class="text-right">0</div>';
                $arrData[$index][4] = '<div class="text-right">'.formatNumber($grand_total).'</div>';
                // $grandTotal-= $grand_total;
                $grandTotalPheLieu+= $grand_total;
            }
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = formatMoney($grandTotal);
        $data->grandTotalPheLieu = formatMoney($grandTotalPheLieu);
        echo json_encode($data);
    }

    public function getChiPhiNhanCongTT()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $this->datatables->select("
            tblother_payslips.id as id,
            tblother_payslips.date as date,
            CONCAT(tblother_payslips.prefix, '', tblother_payslips.code) as reference_no,
            tblcosts.name as name_costs,
            tblother_payslips.total as total,
        ", false);
        $this->datatables->from('tblother_payslips');
        $this->datatables->join('tblcosts', 'tblcosts.id = tblother_payslips.id_costs');
        $this->datatables->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") <=', $end_date);
        $this->datatables->where('tblcosts.type', 1);


        // print_arrays($this->db->get_compiled_select('tbl_purchase_products'));
        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        $iDisplayStart = $this->input->post('iDisplayStart');
        foreach ($data->aaData as $key => $value) {
            $other_payslips_id = $value[0];
            $date = _d($value[1]);
            $reference_no = $value[2];
            $name_costs = $value[3];
            $grand_total = $value[4];

            $iDisplayStart++;
            $arrData[$index][0] = '<div class="text-center">'.$iDisplayStart.'</div>';
            $arrData[$index][1] = '<div class="">'.$date.'</div>';
            $arrData[$index][2] = '<div><a href="#" onclick="view_other_payslips('.$other_payslips_id.'); return false;">'.$reference_no.'</a></div>';
            $arrData[$index][3] = '<div class="">'.$name_costs.'</div>';
            $arrData[$index][4] = '<div class="text-right">'.formatMoney($grand_total).'</div>';

            $grandTotal+= $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = formatMoney($grandTotal);
        echo json_encode($data);
    }

    public function getChiPhiSanXuatChung()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $this->datatables->select("
            tblother_payslips.id as id,
            tblother_payslips.date as date,
            CONCAT(tblother_payslips.prefix, '', tblother_payslips.code) as reference_no,
            tblcosts.name as name_costs,
            tblother_payslips.total as total,
        ", false);
        $this->datatables->from('tblother_payslips');
        $this->datatables->join('tblcosts', 'tblcosts.id = tblother_payslips.id_costs');
        $this->datatables->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") <=', $end_date);
        $this->datatables->where('tblcosts.type', 2);


        // print_arrays($this->db->get_compiled_select('tbl_purchase_products'));
        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        $iDisplayStart = $this->input->post('iDisplayStart');
        foreach ($data->aaData as $key => $value) {
            $other_payslips_id = $value[0];
            $date = _d($value[1]);
            $reference_no = $value[2];
            $name_costs = $value[3];
            $grand_total = $value[4];

            $iDisplayStart++;
            $arrData[$index][0] = '<div class="text-center">'.$iDisplayStart.'</div>';
            $arrData[$index][1] = '<div class="">'.$date.'</div>';
            $arrData[$index][2] = '<div><a href="#" onclick="view_other_payslips('.$other_payslips_id.'); return false;">'.$reference_no.'</a></div>';
            $arrData[$index][3] = '<div class="">'.$name_costs.'</div>';
            $arrData[$index][4] = '<div class="text-right">'.formatMoney($grand_total).'</div>';

            $grandTotal+= $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = formatMoney($grandTotal);
        echo json_encode($data);
    }

}