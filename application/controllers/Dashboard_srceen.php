<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_srceen extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }
    function index()
    {
        $data = [];
        // Ngày giờ
        $weekdayMap = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        $w = (int)date('w');
        $dateStr = $weekdayMap[$w] . ', ' . date('d/m/Y');
        $timeStr = date('g:i A');
        $link_connect_socket = get_option('link_connect_socket');
        $data = [
            'dateStr' => $dateStr,
            'timeStr' => $timeStr,
            'dbname' => APP_DB_NAME,
            'link_connect_socket' => $link_connect_socket,
        ];
        $this->db->select('tbl_category_stages.*');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.is_bangiao', 1);
        $data['categoryStage'] = $this->db->get()->result_array();
        $this->load->view('admin/dashboard_srceen_sx/dashboard_orchestrator', $data);
    }
    function countmanufactures($id = 0)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select('DISTINCT (tbl_productions_orders_details.productions_orders_id)');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stage_bangiao');
        $this->db->join(
            'tbl_productions_orders_details',
            'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'left'
        );
        $this->db->where('tbl_category_stages.id', $id);
        $this->db->where('tbl_stages.is_bangiao', 1);
        $this->db->where('tbl_productions_orders_items_stages.active', 1);
        $this->db->where('tbl_productions_orders_details.date_created >=', $date_dashboard_srceen_sales);
        $this->db->group_by('tbl_productions_orders_items_stages.id');
        $query = $this->db->get();
        $stats["total_manufactures_approved"] = $query->num_rows();

        $this->db->select('DISTINCT (tbl_productions_orders_details.productions_orders_id)');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stage_bangiao');
        $this->db->join(
            'tbl_productions_orders_details',
            'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'left'
        );
        $this->db->where('tbl_category_stages.id', $id);
        $this->db->where('tbl_stages.is_bangiao', 1);
        $this->db->where('tbl_productions_orders_items_stages.active', 0);
        $this->db->where('tbl_productions_orders_details.date_created >=', $date_dashboard_srceen_sales);
        $this->db->group_by('tbl_productions_orders_items_stages.id');
        $query = $this->db->get();
        $stats["total_manufactures_pending"] = $query->num_rows();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }
    function countdelivery()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select('DISTINCT (tbl_deliveries.id)');
        $this->db->from('tbl_deliveries');
        $this->db->where('tbl_deliveries.warehouseman_id >', 0);
        $this->db->where('tbl_deliveries.date >=', $date_dashboard_srceen_sales);
        $query = $this->db->get();
        $stats["total_delivery_approved"] = $query->num_rows();

        $this->db->select('tbl_deliveries.id');
        $this->db->from('tbl_deliveries');
        $this->db->where('tbl_deliveries.warehouseman_id ', 0);
        $this->db->where('tbl_deliveries.date >=', $date_dashboard_srceen_sales);
        $query = $this->db->get();
        $stats["total_delivery_pending"] = $query->num_rows();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }
    public function manufacture()
    {
        // Nếu bảng trống thì seed demo tối thiểu 8 dòng
        $exists = $this->db->where('status', 'active')->count_all_results('tbl_demo_manufacture_orders');
        if ($exists == 0) {
            $this->_seed_demo_rows(8);
        }

        // Lấy danh sách active theo thứ tự tạo (không sắp xếp theo percent)
        $rows = $this->db->from('tbl_demo_manufacture_orders')
            ->where('status', 'active')
            ->order_by('id', 'asc')
            ->get()->result_array();

        // Tính thống kê
        $totalOrders = $this->db->where('status', 'active')->count_all_results('tbl_demo_manufacture_orders');
        $totalStages = $totalOrders; // mỗi dòng 1 công đoạn
        $completedCount = $this->db->where('status', 'done')->count_all_results('tbl_demo_manufacture_orders');
        $uncompleted = $totalStages - $completedCount;
        $overdue = $this->db->where('status', 'active')->where('overdue', 1)->count_all_results('tbl_demo_manufacture_orders');

        $avgPercent = 0;
        if ($totalOrders > 0) {
            $sumPercent = $this->db->select_sum('percent')->where('status', 'active')->get('tbl_demo_manufacture_orders')->row()->percent ?? 0;
            $avgPercent = (int) round($sumPercent / $totalOrders);
        }
        // Ngày giờ
        $weekdayMap = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        $w = (int)date('w');
        $dateStr = $weekdayMap[$w] . ', ' . date('d/m/Y');
        $timeStr = date('g:i A');

        $data = [
            'stats' => [
                'totalOrders' => $totalOrders,
                'totalStages' => $totalStages,
                'completed'   => $completedCount,
                'uncompleted' => $uncompleted,
                'overdue'     => $overdue,
                'avgPercent'  => $avgPercent,
            ],
            'progressCounts' => [
                'done' => $completedCount,
                'todo' => $uncompleted,
            ],
            'rows'    => array_map([$this, '_api_row'], $rows),
            'dateStr' => $dateStr,
            'timeStr' => $timeStr,
            'dbname'  => APP_DB_NAME,
        ];

        $this->load->view('admin/dashboard_srceen/manufacture/manufacture', $data);
    }

    public function updateProgress()
    {
        // $rows = $this->db->from('tbl_demo_manufacture_orders')
        //     ->where('status', 'active')
        //     ->order_by('id', 'asc')
        //     ->get()->result_array();
        $this->db->select('
            tbl_productions_orders_items_stages.id as id,
            tbl_stages.name as stage_name, 
            tbl_stages.id as stage_id,
            tbl_productions_orders.reference_no,
            tbl_productions_orders_items.items_code,
            tbl_productions_orders_items.items_name,    
            tbl_productions_orders_items.quantity,    
            SUM(tbl_purchase_products.total_quantity) as quantity_done,    
        ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id', 'left');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id', 'left');
        $this->db->join('tbl_purchase_products', 'tbl_purchase_products.pois_id = tbl_productions_orders_items_stages.id', 'left');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id', 'left');
        // $this->db->where('tbl_productions_orders_items_stages.active', 0);
        // $this->db->where('tbl_productions_orders_items_stages.stage_id !=', 2);
        $this->db->where('tbl_productions_orders_items_stages.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tbl_productions_orders_details.date_created >', get_option('date_dashboard_srceen_production') ? get_option('date_dashboard_srceen_production') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00');
        $this->db->group_by('tbl_productions_orders_items_stages.id');
        $this->db->order_by('tbl_stages.id DESC');
        $rows = $this->db->get()->result_array();
        header('Content-Type: application/json');
        echo json_encode([
            'success'    => true,
            'rows'       => array_map([$this, '_api_row'], $rows),
            'changed_id' => null
        ]);
    }

    private function _api_row($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'stage_id' => $r['id'],
            'stage_idd' => $r['stage_id'],
            'order_code' => $r['reference_no'],
            'sku'        => $r['items_code'],
            'stage'      => $r['stage_name'],
            'qty_plan'   => (int) $r['quantity'],
            'qty_done'   => (int) $r['quantity_done'],
            'qty_todo'   => (int) ($r['quantity'] - $r['quantity_done']),
            'percent'    => round(($r['quantity_done'] / $r['quantity'] * 100), 2),
            'bar_color'  => $this->_getColor(($r['quantity_done'] / $r['quantity'] * 100)),
        ];
    }
    // ---------- Helpers ----------

    private function _getColor($percent)
    {
        if ($percent >= 75) return '#22c55e'; // green
        if ($percent >= 40) return '#facc15'; // yellow
        return '#ef4444'; // red
    }
    function testmanu()
    {
        $this->db->select('
            tbl_productions_orders_items_stages.id as id,
            tbl_stages.name as stage_name, 
            tbl_productions_orders_details.reference_no,
            tbl_productions_orders_items.items_code,
            tbl_productions_orders_items.items_name,    
            tbl_productions_orders_items.quantity,    
            SUM(tbl_purchase_products.total_quantity) as quantity_done,    
        ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id', 'left');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id', 'left');
        $this->db->join('tbl_purchase_products', 'tbl_purchase_products.pois_id = tbl_productions_orders_items_stages.id', 'left');
        $this->db->where('tbl_productions_orders_items_stages.active', 0);
        $this->db->where('tbl_productions_orders_details.id', 64481);
        $this->db->where('tbl_productions_orders_items_stages.stage_id !=', 2);
        // $this->db->where('tbl_productions_orders_items_stages.type !=', 6);
        $this->db->where('tbl_productions_orders_details.date_created >', '2025-01-01 00:00:00');
        $this->db->group_by('tbl_productions_orders_items_stages.id');
        $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
        $productions_orders_items_stages = $this->db->get()->result_array();
        echo '<pre>';
        print_arrays($productions_orders_items_stages);
        die;
    }
    // xuất khi giao hàng
    public function export_delivery()
    {
        $this->UpdateDateDelivery();
        $rows = [];
        // Tính thống kê
        $totalOrders = $this->db->where('status', 'active')->count_all_results('tbl_demo_manufacture_orders');
        $totalStages = $totalOrders; // mỗi dòng 1 công đoạn
        $completedCount = $this->db->where('status', 'done')->count_all_results('tbl_demo_manufacture_orders');
        $uncompleted = $totalStages - $completedCount;
        $overdue = $this->db->where('status', 'active')->where('overdue', 1)->count_all_results('tbl_demo_manufacture_orders');

        $avgPercent = 0;
        if ($totalOrders > 0) {
            $sumPercent = $this->db->select_sum('percent')->where('status', 'active')->get('tbl_demo_manufacture_orders')->row()->percent ?? 0;
            $avgPercent = (int) round($sumPercent / $totalOrders);
        }

        // Ngày giờ
        $weekdayMap = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        $w = (int)date('w');
        $dateStr = $weekdayMap[$w] . ', ' . date('d/m/Y');
        $timeStr = date('g:i A');

        $data = [
            'stats' => [
                'totalOrders' => $totalOrders,
                'totalStages' => $totalStages,
                'completed'   => $completedCount,
                'uncompleted' => $uncompleted,
                'overdue'     => $overdue,
                'avgPercent'  => $avgPercent,
            ],
            'progressCounts' => [
                'done' => $completedCount,
                'todo' => $uncompleted,
            ],
            'rows'    => array_map([$this, '_api_row_export_delivery'], $rows),
            'dateStr' => $dateStr,
            'timeStr' => $timeStr,
            'dbname'  => APP_DB_NAME,
        ];

        $this->load->view('admin/dashboard_srceen/export_delivery/export_delivery', $data);
    }
    public function updateProgressExportDelivery()
    {
        $this->db->select('tbl_delivery_items.quantity,tbl_delivery_items.id,tbl_delivery_items.item_code,tbl_delivery_items.item_name,tbl_deliveries.date,tbl_deliveries.reference_no,tblclients.company,tblclients.zcode,tbl_orders.reference_no as code_orders,tbl_deliveries.warehouseman_id,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tbl_deliveries.employee_id,tblunits.unit as unit_name,tblclients.company_short');
        $this->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
        $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid=tbl_deliveries.employee_id', 'left');
        $this->db->join('tbl_products', 'tbl_products.id=tbl_delivery_items.item_id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid=tbl_products.unit_id', 'left');
        $this->db->where('tbl_deliveries.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tbl_deliveries.date >', get_option('date_dashboard_srceen_export') ? get_option('date_dashboard_srceen_export') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00');
        $rows = $this->db->get('tbl_delivery_items')->result_array();
        header('Content-Type: application/json');
        echo json_encode([
            'success'    => true,
            'rows'       => array_map([$this, '_api_row_export_delivery'], $rows),
            'changed_id' => null
        ]);
    }
    private function _api_row_export_delivery($r)
    {
        if ($r['employee_id'] == 1) {
            $r['employee_id'] = 26;
            $r['fullname_employee'] = get_staff_full_name(26);
        }
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _dt($r['date']),
            'code_orders' => $r['code_orders'],
            'reference_no' => $r['reference_no'],
            'item_code'        => $r['item_code'],
            'item_name'        => $r['item_name'],
            'unit_name'        => $r['unit_name'],
            'zcode'        => $r['company_short'],
            'company'        => $r['company'],
            'quantity'   => (int) $r['quantity'],
            'warehouseman_id'   => $r['warehouseman_id'],
            'fullname_employee'   => (int) $r['fullname_employee'],
            'image_employee'   => staff_profile_image($r['employee_id'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee'] . '</span>'
        ];
    }
    function UpdateDateDelivery()
    {
        $this->db->select('tbl_deliveries.id');
        $this->db->where('tbl_deliveries.warehouseman_id ', 0);
        $this->db->group_start();
        $this->db->where('tbl_deliveries.date_dashboard_srceen IS NULL');
        $this->db->or_where('tbl_deliveries.date_dashboard_srceen !=', date('Y-m-d'));
        $this->db->or_where('tbl_deliveries.check_update_dashboard !=', get_option('check_update_dashboard'));
        $this->db->group_end();

        $this->db->where('tbl_deliveries.date >', get_option('date_dashboard_srceen_export') ? get_option('date_dashboard_srceen_export') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00');
        $rows = $this->db->get('tbl_deliveries')->result_array();
        foreach ($rows as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tbl_deliveries', ['date_dashboard_srceen' => date('Y-m-d')]);
        }
    }
    function UpdateDateProductionsOrdersItemsStages()
    {
        $this->db->select('
            tbl_productions_orders_items_stages.id as id,
            tbl_stages.name as stage_name, 
            tbl_productions_orders_details.reference_no,
            tbl_productions_orders_items.items_code,
            tbl_productions_orders_items.items_name,    
            tbl_productions_orders_items.quantity,    
            SUM(tbl_purchase_products.total_quantity) as quantity_done,    
        ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id', 'left');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id', 'left');
        $this->db->join('tbl_purchase_products', 'tbl_purchase_products.pois_id = tbl_productions_orders_items_stages.id', 'left');
        $this->db->where('tbl_productions_orders_items_stages.active', 0);
        $this->db->where('tbl_productions_orders_items_stages.stage_id !=', STAGES_MATERIAL);
        $this->db->where('tbl_productions_orders_details.date_created >', get_option('date_dashboard_srceen_production') ? get_option('date_dashboard_srceen_production') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00');
        $this->db->group_start();
        $this->db->where('tbl_productions_orders_items_stages.date_dashboard_srceen IS NULL');
        $this->db->or_where('tbl_productions_orders_items_stages.date_dashboard_srceen !=', date('Y-m-d'));
        $this->db->or_where('tbl_productions_orders_items_stages.check_update_dashboard !=', get_option('check_update_dashboard'));
        $this->db->group_end();

        $this->db->group_by('tbl_productions_orders_items_stages.id');
        $this->db->order_by('tbl_productions_orders_items_stages.id ASC,tbl_productions_orders_items_stages.number ASC');
        $rows = $this->db->get()->result_array();
        foreach ($rows as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tbl_productions_orders_items_stages', ['date_dashboard_srceen' => date('Y-m-d')]);
        }
    }
    function modal_delivery($type = 1)
    {
        $title = '';
        $title = 'Phiếu giao chưa duyệt kho';
        $this->load->view(
            'admin/dashboard_srceen_sx/modal/delivery',
            ['type' => $type, 'title' => $title]
        );
    }

    function table_delivery($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tbl_deliveries.id as id',
            'tbl_deliveries.date as date',
            'tbl_deliveries.reference_no as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee'
        );
        $sIndexColumn = "id";
        $sTable = 'tbl_deliveries';
        $where = array(
            'AND tbl_deliveries.date >="' . $date_dashboard_srceen_sales . '"',
            'AND tbl_deliveries.warehouseman_id = 0',
        );


        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_deliveries.employee_id',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tbl_deliveries.employee_id'),
            ' GROUP BY tbl_deliveries.id'
        );
        $output = $result['output'];
        $rResult = $result['rResult'];
        $iDisplayStart = $this->input->post('iDisplayStart');
        $j = 0;
        foreach ($rResult as $aRow) {
            $row = array();
            $j++;
            for ($i = 0; $i < count($aColumns); $i++) {
                if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                    $_data = $aRow[strafter($aColumns[$i], 'as ')];
                } else {
                    $_data = $aRow[$aColumns[$i]];
                }
                if ($aColumns[$i] == 'tbl_deliveries.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tbl_deliveries.date as date') {
                    $_data = _d($aRow['date']);
                }
                if ($aColumns[$i] == 'tbl_deliveries.reference_no as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['employee_id'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array()
                    ) . ($aRow['fullname_employee'] ?? '');
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    function modal_manufactures($id = '')
    {
        $title = '';
        $this->db->select('tbl_category_stages.*');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.id', $id);
        $categoryStage = $this->db->get()->row_array();
        $title = 'Lệnh sản xuất chưa hoàn thành: ' . $categoryStage['name'];
        $this->load->view(
            'admin/dashboard_srceen_sx/modal/manufactures',
            ['type' => $id, 'title' => $title]
        );
    }

    function table_manufactures($id = 0)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as reference_no'
        );
        $sIndexColumn = "id";
        $sTable = 'tbl_productions_orders';
        $where = array(
            'AND tbl_productions_orders_details.date_created >="' . $date_dashboard_srceen_sales . '"'
        );
        $where[] = 'AND tbl_category_stages.id = '.$id;
        $where[] = 'AND tbl_productions_orders_items_stages.active = 0';
        $where[] = 'AND tbl_stages.is_bangiao = 1';
        $join = array(
            'LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id',
            'LEFT JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id',
            'LEFT JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id',
            'LEFT JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stage_bangiao',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array(),
            ' GROUP BY tbl_productions_orders.id'
        );
        $output = $result['output'];
        $rResult = $result['rResult'];
        $iDisplayStart = $this->input->post('iDisplayStart');
        $j = 0;
        foreach ($rResult as $aRow) {
            $row = array();
            $j++;
            for ($i = 0; $i < count($aColumns); $i++) {
                if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                    $_data = $aRow[strafter($aColumns[$i], 'as ')];
                } else {
                    $_data = $aRow[$aColumns[$i]];
                }
                if ($aColumns[$i] == 'tbl_productions_orders.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tbl_productions_orders.date as date') {
                    $_data = _dt($aRow['date']);
                }
                if ($aColumns[$i] == 'tbl_productions_orders.reference_no as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
}
