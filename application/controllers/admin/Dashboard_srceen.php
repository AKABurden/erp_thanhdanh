<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_srceen extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
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
        $rows = $this->db->from('tbl_demo_manufacture_orders')
            ->where('status', 'active')
            ->order_by('id', 'asc')
            ->get()->result_array();

        header('Content-Type: application/json');
        echo json_encode([
            'success'    => true,
            'rows'       => array_map([$this, '_api_row'], $rows),
            'changed_id' => null
        ]);
    }

    public function manufacture_control()
    {
        $rows = $this->db->from('tbl_demo_manufacture_orders')
            ->order_by('id', 'asc')
            ->get()->result_array();

        $data = ['rows' => array_map([$this, '_api_row'], $rows)];
        $this->load->view('admin/dashboard_srceen/manufacture/manufacture_control', $data);
    }

    // API thêm order (emit action: add, newRow)
    public function addOrder()
    {
        $stageNames = ['Công đoạn Cắt giấy', 'Công đoạn In Offset', 'Công đoạn Bế khuôn', 'Công đoạn Đóng gói', 'Công đoạn Cán màng'];

        $orderCode  = 'LSXCT-' . rand(70, 99) . '-' . rand(100, 999);
        $productSKU = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3)
            . '-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3)
            . '-' . rand(10, 99);

        $qtyPlan  = rand(5000, 20000);

        $row = [
            'order_code' => $orderCode,
            'sku'        => $productSKU,
            'stage'      => $stageNames[array_rand($stageNames)],
            'qty_plan'   => $qtyPlan,
            'qty_done'   => 0,
            'qty_todo'   => $qtyPlan,
            'percent'    => 0,
            'bar_color'  => '#ef4444',
            'status'     => 'active',
            'overdue'    => rand(0, 1),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('tbl_demo_manufacture_orders', $row);
        $row['id'] = $this->db->insert_id();

        $newRow = $this->_api_row($row);

        // Emit socket
        sendSocket([
            'action' => 'add',
            'newRow' => $newRow,
        ], [], 'loadProgress');

        echo json_encode(['success' => true, 'newRow' => $newRow]);
    }

    // API tăng tiến độ (emit action: update, updatedRow, removed nếu hoàn thành)
    public function increase($orderCode)
    {
        $r = $this->db->from('tbl_demo_manufacture_orders')
            ->where('order_code', $orderCode)
            ->limit(1)->get()->row_array();

        if (!$r) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy order.']);
            return;
        }

        // Chỉ thao tác khi còn active
        if ($r['status'] !== 'active') {
            echo json_encode(['success' => false, 'message' => 'Order đã hoàn tất.']);
            return;
        }

        $inc = rand(500, 1500);
        $qty_done = min($r['qty_plan'], $r['qty_done'] + $inc);
        $qty_todo = max(0, $r['qty_plan'] - $qty_done);
        $percent  = $r['qty_plan'] > 0 ? (int) round($qty_done / $r['qty_plan'] * 100) : 0;
        $bar_color = $this->_getColor($percent);

        $removed = false;

        $update = [
            'qty_done'   => $qty_done,
            'qty_todo'   => $qty_todo,
            'percent'    => $percent,
            'bar_color'  => $bar_color,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($percent >= 100) {
            // Mark done (không xóa DB), frontend sẽ tự loại khỏi danh sách active
            $update['status'] = 'done';
            $removed = true;
        }

        $this->db->where('id', $r['id'])->update('tbl_demo_manufacture_orders', $update);

        // Lấy snapshot mới nhất để emit
        $newRow = array_merge($r, $update);
        $updatedRow = $this->_api_row($newRow);

        sendSocket([
            'action'     => 'update',
            'updatedRow' => $updatedRow,
            'removed'    => $removed,
        ], [], 'loadProgress');

        echo json_encode([
            'success'    => true,
            'updatedRow' => $updatedRow,
            'removed'    => $removed,
        ]);
    }

    // API xóa order (hard delete) (emit action: delete, deleted_id)
    public function delete($orderCode)
    {
        $this->db->where('order_code', $orderCode)->delete('tbl_demo_manufacture_orders');

        sendSocket([
            'action'     => 'delete',
            'deleted_id' => $orderCode,
        ], [], 'loadProgress');

        echo json_encode(['success' => true, 'deleted_id' => $orderCode]);
    }

    // ---------- Helpers ----------

    private function _getColor($percent)
    {
        if ($percent >= 75) return '#22c55e'; // green
        if ($percent >= 40) return '#facc15'; // yellow
        return '#ef4444'; // red
    }

    private function _api_row($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'order_code' => $r['order_code'],
            'sku'        => $r['sku'],
            'stage'      => $r['stage'],
            'qty_plan'   => (int) $r['qty_plan'],
            'qty_done'   => (int) $r['qty_done'],
            'qty_todo'   => (int) $r['qty_todo'],
            'percent'    => (int) $r['percent'],
            'bar_color'  => $r['bar_color'],
        ];
    }

    private function _seed_demo_rows($n = 8)
    {
        $stageNames = ['Công đoạn Cắt giấy', 'Công đoạn In Offset', 'Công đoạn Bế khuôn', 'Công đoạn Đóng gói', 'Công đoạn Cán màng'];
        for ($i = 0; $i < $n; $i++) {
            $orderCode  = 'LSXCT-' . rand(70, 99) . '-' . rand(100, 999);
            $productSKU = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3)
                . '-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3)
                . '-' . rand(10, 99);

            $qtyPlan  = rand(5000, 20000);
            $qtyDone  = rand(0, $qtyPlan);
            $qtyTodo  = max(0, $qtyPlan - $qtyDone);
            $percent  = $qtyPlan > 0 ? (int) round($qtyDone / $qtyPlan * 100) : 0;

            $row = [
                'order_code' => $orderCode,
                'sku'        => $productSKU,
                'stage'      => $stageNames[array_rand($stageNames)],
                'qty_plan'   => $qtyPlan,
                'qty_done'   => $qtyDone,
                'qty_todo'   => $qtyTodo,
                'percent'    => $percent,
                'bar_color'  => $this->_getColor($percent),
                'status'     => $percent >= 100 ? 'done' : 'active',
                'overdue'    => rand(0, 1),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('tbl_demo_manufacture_orders', $row);
        }
    }
    // xuất khi giao hàng
    public function export_delivery()
    {
        $this->UpdateDateDelivery();
        // $this->db->select('tbl_delivery_items.quantity,tbl_delivery_items.id,tbl_delivery_items.item_code,tbl_delivery_items.item_name,tbl_deliveries.date,tbl_deliveries.reference_no,tblclients.company,tbl_orders.reference_no as code_orders,tbl_deliveries.warehouseman_id,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee');
        // $this->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        // $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
        // $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
        // $this->db->join('tblstaff', 'tblstaff.staffid=tbl_deliveries.employee_id', 'left');
        // $this->db->where('tbl_deliveries.warehouseman_id ', 0);
        // $rows = $this->db->get('tbl_delivery_items')->result_array();
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
        $this->db->select('tbl_delivery_items.quantity,tbl_delivery_items.id,tbl_delivery_items.item_code,tbl_delivery_items.item_name,tbl_deliveries.date,tbl_deliveries.reference_no,tblclients.company,tblclients.zcode,tbl_orders.reference_no as code_orders,tbl_deliveries.warehouseman_id,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tbl_deliveries.employee_id');
        $this->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
        $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid=tbl_deliveries.employee_id', 'left');
        $this->db->where('tbl_deliveries.date >', '2025-01-01 00:00:00');
        $this->db->where('tbl_deliveries.date_dashboard_srceen', date('Y-m-d'));
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
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _dt($r['date']),
            'code_orders' => $r['code_orders'],
            'reference_no' => $r['reference_no'],
            'item_code'        => $r['item_code'],
            'item_name'        => $r['item_name'],
            'zcode'        => $r['zcode'],
            'company'        => $r['company'],
            'quantity'   => (int) $r['quantity'],
            'warehouseman_id'   => $r['warehouseman_id'],
            'fullname_employee'   => (int) $r['fullname_employee'],
            'image_employee'   => staff_profile_image($r['employee_id'], [
                'staff-profile-image-small-2x mbot5',
            ],'thumb')
        ];
    }
    function UpdateDateDelivery()
    {
        $this->db->select('tbl_deliveries.id');
        $this->db->where('tbl_deliveries.warehouseman_id ', 0);
        $this->db->group_start();
        $this->db->where('tbl_deliveries.date_dashboard_srceen IS NULL');
        $this->db->or_where('tbl_deliveries.date_dashboard_srceen !=', date('Y-m-d'));
        $this->db->group_end();
        $rows = $this->db->get('tbl_deliveries')->result_array();
        foreach ($rows as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tbl_deliveries', ['date_dashboard_srceen' => date('Y-m-d')]);
        }
    }
}
