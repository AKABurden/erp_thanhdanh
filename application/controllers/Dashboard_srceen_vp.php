<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_srceen_vp extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    function loadpage()
    {
        sendSocket(['data' => 123], [], 'update_dashboard');
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
        $data['departments'] = $this->db->get('tbl_room')->result_array();
        $this->db->select('tbl_category_stages.*');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.check_productivity', 1);
        $data['categoryStage'] = $this->db->get()->result_array();




        $this->load->view('admin/dashboard_srceen_office/dashboard_orchestrator', $data);
    }
    // function index()
    // {
    //     $this->updatadashboardthu();
    //     $this->updatadashboardDXNB();
    //     $this->updatadashboardKHQ();
    //     $this->updatadashboardhdm();
    //     $this->updatadashboardycc();
    //     $this->updatadashboarddxtc();
    //     $this->updatadashwarehouseimport();
    //     $this->updatadashwarehouseimportDXNB();
    //     $data = [];
    //     // Ngày giờ
    //     $weekdayMap = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
    //     $w = (int)date('w');
    //     $dateStr = $weekdayMap[$w] . ', ' . date('d/m/Y');
    //     $timeStr = date('g:i A');
    //     $link_connect_socket = get_option('link_connect_socket');

    //     $data = [
    //         'dateStr' => $dateStr,
    //         'timeStr' => $timeStr,
    //         'dbname'  => APP_DB_NAME,
    //         'link_connect_socket'  => $link_connect_socket,
    //     ];
    //     $this->load->view('admin/dashboard_srceen_vp/dashboard_orchestrator', $data);
    // }
    public function countPreventiveProducts($start_date = '2025-01-01')
    {
        $sql = "
        SELECT 
            SUM(CASE WHEN quantity_preventive > 0 THEN 1 ELSE 0 END) AS count_has_preventive,
            SUM(CASE WHEN quantity_preventive = 0 THEN 1 ELSE 0 END) AS count_no_preventive
        FROM (
            SELECT 
                tbl_products.id,
                IF(
                    COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0) > 0,
                    COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0),
                    0
                ) AS quantity_preventive
            FROM tbl_products
            INNER JOIN (
                SELECT
                    tbl_business_plan_items.items_id AS item_id,
                    SUM(tbl_business_plan_items.quantity) AS total_quantity_item
                FROM tbl_business_plan
                INNER JOIN tbl_business_plan_items ON tbl_business_plan_items.business_plan_id = tbl_business_plan.id
                INNER JOIN tbl_business_plan_items_date ON tbl_business_plan_items.id = tbl_business_plan_items_date.business_plan_items_id
                WHERE tbl_business_plan.productions_plan_preventive_id > 0
                      AND tbl_business_plan_items_date.date >= '$start_date'
                GROUP BY tbl_business_plan_items.items_id
            ) tb_perventive ON tb_perventive.item_id = tbl_products.id
            LEFT JOIN (
                SELECT
                    tbl_productions_orders_items.items_id AS items_id,
                    SUM(tbl_purchase_products.total_quantity) AS quantity_po
                FROM tbl_productions_orders_details
                INNER JOIN tbl_productions_orders_items 
                    ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
                INNER JOIN tbl_purchase_products 
                    ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
                INNER JOIN tbl_business_plan 
                    ON tbl_business_plan.id = tbl_productions_orders_details.object_id
                WHERE tbl_productions_orders_details.object_type = 'business_plan'
                      AND tbl_business_plan.productions_plan_preventive_id != 0
                      AND tbl_purchase_products.date >= '$start_date'
                GROUP BY tbl_productions_orders_items.items_id
            ) tb_productions_orders_detail_perventive 
            ON tb_productions_orders_detail_perventive.items_id = tbl_products.id
            WHERE tbl_products.type_products IN ('products','semi_products')
        ) AS tb
    ";

        $data = $this->db->query($sql)->row_array();
        return [
            'count_has_preventive' => (int)$data['count_has_preventive'],
            'count_no_preventive' => (int)$data['count_no_preventive'],
        ];
    }

    function updatePlanningDepartment()
    {
        // Tính tổng số đơn approved, un_approved, đã giao, chưa giao
        $date_dashboard_srceen_planning = get_option('date_dashboard_srceen_planning') ? get_option('date_dashboard_srceen_planning') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select("
            SUM(CASE WHEN tbl_orders.status = 'approved' THEN 1 ELSE 0 END) AS total_approved,
            SUM(CASE WHEN tbl_orders.status = 'un_approved' THEN 1 ELSE 0 END) AS total_un_approved,
            SUM(CASE WHEN tbl_orders.count_delivery > 0 THEN 1 ELSE 0 END) AS total_delivered,
            SUM(CASE WHEN tbl_orders.count_delivery = 0 THEN 1 ELSE 0 END) AS total_not_delivered
        ");
        $this->db->join('tbl_deliveries', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
        // $this->db->where('YEARWEEK(tbl_orders.date, 1) = YEARWEEK(CURDATE(), 1)');
        $this->db->where('tbl_orders.date >', $date_dashboard_srceen_planning);
        $stats = $this->db->get('tbl_orders')->row_array();

        $this->db->select("
            SUM(CASE WHEN (tblexport_different.warehouseman_id = '1' AND tblexport_different.type_po = 2) THEN 1 ELSE 0 END) AS export_total_approved,
            SUM(CASE WHEN (tblexport_different.warehouseman_id = '0' AND tblexport_different.type_po = 2) THEN 1 ELSE 0 END) AS export_total_un_approved,
            SUM(CASE WHEN (tblexport_different.warehouseman_id = '1' AND tblexport_different.type_po = 1) THEN 1 ELSE 0 END) AS export_kb_total_approved,
            SUM(CASE WHEN (tblexport_different.warehouseman_id = '0' AND tblexport_different.type_po = 1) THEN 1 ELSE 0 END) AS export_kb_total_un_approved
        ");
        // $this->db->where('YEARWEEK(tbl_orders.date, 1) = YEARWEEK(CURDATE(), 1)');
        $this->db->where('tblexport_different.date >', $date_dashboard_srceen_planning);
        $export = $this->db->get('tblexport_different')->row_array();
        $stats['export_total_approved'] = $export['export_total_approved'];
        $stats['export_total_un_approved'] = $export['export_total_un_approved'];
        $stats['export_kb_total_approved'] = $export['export_kb_total_approved'];
        $stats['export_kb_total_un_approved'] = $export['export_kb_total_un_approved'];

        $this->db->select("
            SUM(CASE WHEN (tbltransfer_warehouse_detail.type = 'nvl' AND tbltransfer_warehouse.warehouseman_id > 0) THEN quantity_net ELSE 0 END) AS transfer_nvl_total_dx,
            SUM(CASE WHEN (tbltransfer_warehouse_detail.type = 'nvl' AND tbltransfer_warehouse.warehouseman_id = 0) THEN quantity_net ELSE 0 END) AS transfer_nvl_total_cx,
            SUM(CASE WHEN (tbltransfer_warehouse_detail.type = 'product' AND tbltransfer_warehouse.warehouseman_id > 0) THEN quantity_net ELSE 0 END) AS transfer_product_total_dx,
            SUM(CASE WHEN (tbltransfer_warehouse_detail.type = 'product' AND tbltransfer_warehouse.warehouseman_id = 0) THEN quantity_net ELSE 0 END) AS transfer_product_total_cx
        ");
        $this->db->where('tbltransfer_warehouse.date >', $date_dashboard_srceen_planning);
        $this->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        $export = $this->db->get('tbltransfer_warehouse_detail')->row_array();
        $stats['transfer_nvl_total_dx'] = $export['transfer_nvl_total_dx'];
        $stats['transfer_product_total_dx'] = $export['transfer_product_total_dx'];
        $stats['transfer_nvl_total_cx'] = $export['transfer_nvl_total_cx'];
        $stats['transfer_product_total_cx'] = $export['transfer_product_total_cx'];


        // Đếm số lượng active = 1 và active = 0 cho từng loại type_productionlist_id
        $counts = [];
        foreach ([24 => 'rows_dan_trang_in', 25 => 'rows_ghep_size'] as $typeId => $prefix) {
            foreach ([1, 0] as $active) {
                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
                $this->db->join(
                    'tbl_productions_orders_details',
                    'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
                    'left'
                );
                $this->db->where('tbl_category_stages.type_use', 1);
                $this->db->where('tbl_category_stages.type_productionlist_id', $typeId);
                $this->db->where('tbl_productions_orders_items_stages.active', $active);
                $this->db->where('tbl_productions_orders_items_stages.stage_id !=', STAGES_MATERIAL);
                $this->db->where('tbl_productions_orders_details.date_created >', $date_dashboard_srceen_planning);
                $this->db->group_by('tbl_productions_orders_items_stages.id');
                $counts["{$prefix}_active_{$active}"] = $this->db->get()->num_rows();
                // echo '<pre>';print_arrays($this->db->last_query());die;
            }
        }
        $rows_dan_trang_in_active = $counts['rows_dan_trang_in_active_1'];
        $rows_dan_trang_in_inactive = $counts['rows_dan_trang_in_active_0'];
        $rows_ghep_size_active = $counts['rows_ghep_size_active_1'];
        $rows_ghep_size_inactive = $counts['rows_ghep_size_active_0'];

        $stats['rows_dan_trang_in_active'] = $rows_dan_trang_in_active;
        $stats['rows_dan_trang_in_inactive'] = $rows_dan_trang_in_inactive;
        $stats['rows_ghep_size_active'] = $rows_ghep_size_active;
        $stats['rows_ghep_size_inactive'] = $rows_ghep_size_inactive;
        $datacountPreventiveProducts = $this->countPreventiveProducts($date_dashboard_srceen_planning);
        $stats['count_has_preventive'] = $datacountPreventiveProducts['count_has_preventive'];
        $stats['count_no_preventive'] = $datacountPreventiveProducts['count_no_preventive'];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function updatadashboardDXNB()
    {
        $checkinternal_proposal_process = "(
                    SELECT tbl_internal_proposal_process.id
                    FROM tbl_internal_proposal_process
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id AND tbl_internal_proposal_process.status = 0
                    GROUP BY tbl_internal_proposal_process.id_internal_proposal
                )";
        $checkinternal_proposal_process_cancel = "(
                    SELECT tbl_internal_proposal_process.id
                    FROM tbl_internal_proposal_process
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id AND tbl_internal_proposal_process.status = 2
                    GROUP BY tbl_internal_proposal_process.id_internal_proposal
                )";

        $this->db->select("
        tblinternal_proposal.id,
        tblinternal_proposal.date,
        tblinternal_proposal.code,
        tblinternal_proposal.money,
        tblinternal_proposal.date_finish,
        coalesce(tbl_recommended_list.name, '') as name_recommended_list,
        tblcategory_tasks.code as code_category_tasks,
        tblinternal_proposal.staff as employee_id,
        CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname_employee
        ");
        $this->db->where(
            '((EXISTS (' . $checkinternal_proposal_process . ') OR tblinternal_proposal.status = 0))',
            false,
            false
        );
        $this->db->where('NOT EXISTS (' . $checkinternal_proposal_process_cancel . ')', false, false);
        $this->db->where(
            'tblinternal_proposal.date >',
            get_option('date_dashboard_srceen_accounting_dxnb') ? get_option('date_dashboard_srceen_accounting_dxnb') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00'
        );
        $this->db->join(
            'tbl_recommended_list',
            'tbl_recommended_list.id = tblinternal_proposal.recommended_list_id',
            'left'
        );
        $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblinternal_proposal.category_tasks', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid=tblinternal_proposal.staff', 'left');
        $this->db->group_start();
        $this->db->where('tblinternal_proposal.date_dashboard_srceen IS NULL');
        $this->db->or_where('tblinternal_proposal.date_dashboard_srceen !=', date('Y-m-d'));
        $this->db->or_where('tblinternal_proposal.check_update_dashboard !=', get_option('check_update_dashboard'));
        $this->db->group_end();

        $internal_proposal = $this->db->get('tblinternal_proposal')->result_array();
        foreach ($internal_proposal as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tblinternal_proposal', [
                'date_dashboard_srceen' => date('Y-m-d'),
                'check_update_dashboard' => get_option('check_update_dashboard')
            ]);
        }
    }

    public function countDeliveriesKHQ()
    {
        $this->db->select("
            SUM(CASE WHEN tbl_deliveries.code_custom IS NULL THEN 1 ELSE 0 END) AS count_code_custom_null,
            SUM(CASE WHEN tbl_deliveries.code_custom IS NOT NULL THEN 1 ELSE 0 END) AS count_code_custom_not_null
        ");
        $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
        $this->db->where('tblclients.declare_customs', 1);
        $this->db->where('tbl_deliveries.date_dashboard_srceen_khq', date('Y-m-d'));
        $this->db->where('tbl_deliveries.check_update_dashboard', get_option('check_update_dashboard'));
        $data = $this->db->get('tbl_deliveries')->row_array();
        return [
            'count_code_custom_null' => (int)$data['count_code_custom_null'],
            'count_code_custom_not_null' => (int)$data['count_code_custom_not_null'],
        ];
    }

    public function countFinancialAccountingDXNB()
    {
        $this->db->select("
        tblinternal_proposal.id,
        tblinternal_proposal.date,
        tblinternal_proposal.code,
        tblinternal_proposal.money,
        tblinternal_proposal.date_finish
    ");
        $this->db->where('tblinternal_proposal.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tblinternal_proposal.check_update_dashboard', get_option('check_update_dashboard'));
        $internal_proposal = $this->db->get('tblinternal_proposal')->result_array();

        $count_finish = 0;
        $count_unfinish = 0;

        foreach ($internal_proposal as $aRow) {
            // Lấy danh sách process của phiếu
            $this->db->select('status, bod');
            $this->db->where('id_internal_proposal', $aRow['id']);
            $data_checklist_items = $this->db->get('tbl_internal_proposal_process')->result_array();

            $status = 0;
            $quahan = 0;
            $status_finish = 1;

            foreach ($data_checklist_items as $v) {
                if ($v['status'] == 0 || $v['status'] == 2) {
                    $quahan = 1;
                    $status_finish = 0;
                }
                if ($v['bod'] == 1 && $v['status'] == 1) {
                    $status = 1;
                }
            }

            // Đếm
            if ($status_finish == 1) {
                $count_finish++;
            } else {
                $count_unfinish++;
            }
        }
        return [
            'status_finish_1' => $count_finish,
            'status_finish_0' => $count_unfinish,
        ];
    }
    //dxnb
    // public function updatefinancialAccountingDXNB()
    // {

    //     $this->db->select("
    //     tblinternal_proposal.id,
    //     tblinternal_proposal.date,
    //     tblinternal_proposal.code,
    //     tblinternal_proposal.money,
    //     tblinternal_proposal.date_finish,
    //     coalesce(tbl_recommended_list.name, '') as name_recommended_list,
    //     tblcategory_tasks.code as code_category_tasks,
    //     tblinternal_proposal.staff as employee_id,
    //     CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname_employee
    //     ");

    //     $this->db->where('tblinternal_proposal.date_dashboard_srceen', date('Y-m-d'));
    //     $this->db->where('tblinternal_proposal.check_update_dashboard', get_option('check_update_dashboard'));
    //     $this->db->join('tbl_recommended_list', 'tbl_recommended_list.id = tblinternal_proposal.recommended_list_id', 'left');
    //     $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblinternal_proposal.category_tasks', 'left');
    //     $this->db->join('tblstaff', 'tblstaff.staffid=tblinternal_proposal.staff', 'left');
    //     $internal_proposal = $this->db->get('tblinternal_proposal')->result_array();
    //     foreach ($internal_proposal as $key => $aRow) {
    //         $this->db->select('tbl_internal_proposal_process.*,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tblstaff.staffid as staff,tbl_internal_proposal_process_child.id as childs,tbl_internal_proposal_process.bod,tbl_internal_proposal_process.name as name_process,tblstaff.profile_image as avatar_url');
    //         $this->db->where('tbl_internal_proposal_process.id_internal_proposal', $aRow['id']);
    //         $this->db->join('tbl_internal_proposal_process_child', 'tbl_internal_proposal_process_child.recommended_list_id = tbl_internal_proposal_process.id_process AND tbl_internal_proposal_process_child.id_internal_proposal = tbl_internal_proposal_process.id_internal_proposal', 'left');
    //         $this->db->join('tblstaff', 'tblstaff.staffid=tbl_internal_proposal_process.staff_id', 'left');
    //         $this->db->order_by('tbl_internal_proposal_process.id_process asc');
    //         $this->db->group_by('tbl_internal_proposal_process.id');
    //         $data_checklist_items = $this->db->get('tbl_internal_proposal_process')->result_array();
    //         $internal_proposal[$key]['progress'] = [];
    //         $status = 0;
    //         $quahan = 0;
    //         $status_finish = 1;
    //         foreach ($data_checklist_items as $k => $v) {
    //             // Chỉ lấy process cuối cùng có status = 1 (nếu có nhiều cái status = 1 thì chỉ lấy cái cuối cùng)
    //             if ($v['status'] == 1) {
    //                 // Chỉ lấy process cuối cùng có status = 1
    //                 $isLastStatus1 = true;
    //                 for ($j = $k + 1; $j < count($data_checklist_items); $j++) {
    //                     if ($data_checklist_items[$j]['status'] == 1) {
    //                         $isLastStatus1 = false;
    //                         break;
    //                     }
    //                 }
    //                 if ($isLastStatus1) {
    //                     // Lấy thông tin user/avatar theo mẫu bạn gửi
    //                     $user = '';
    //                     $avatar_url = '';
    //                     if (!empty($v['staff_id'])) {
    //                         $FullName = get_staff_full_name($v['staff_id']);
    //                         $user = $FullName;
    //                         $avatar_url = (!empty($v['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $v['staff'] . '/small_' . $v['avatar_url']) : '';
    //                     } else {
    //                         $user = get_staff_full_name($v['staff_id']);
    //                         $avatar_url = (!empty($v['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $v['staff'] . '/small_' . $v['avatar_url']) : '';
    //                     }
    //                     $internal_proposal[$key]['progress'][] = [
    //                         'bod'      => $v['bod'],
    //                         'title'    => preg_replace('/^(2\.|3\.)\s*/', '', $v['name_process']),
    //                         'user'     => $user,
    //                         'status'   => 'done_dxnt',
    //                         'avatar_url' => $avatar_url,
    //                     ];
    //                 }
    //             } elseif ($v['status'] == 0) {
    //                 // Tìm process tiếp theo sau cái cuối cùng status=1
    //                 $lastDoneIndex = -1;
    //                 for ($j = $k - 1; $j >= 0; $j--) {
    //                     if ($data_checklist_items[$j]['status'] == 1) {
    //                         $lastDoneIndex = $j;
    //                         break;
    //                     }
    //                 }
    //                 if ($lastDoneIndex >= 0 && $k == $lastDoneIndex + 1) {
    //                     $user = '';
    //                     $avatar_url = '';
    //                     if (!empty($v['staff_id'])) {
    //                         $FullName = get_staff_full_name($v['staff_id']);
    //                         $user = $FullName;
    //                         $avatar_url = staff_profile_image(
    //                             $v['staff_id'],
    //                             array('staff-profile-image-small mright5'),
    //                             'small',
    //                             array('data-toggle' => 'tooltip', 'data-title' => $FullName)
    //                         );
    //                     } else {
    //                         $user = $v['fullname_employee'];
    //                         $avatar_url = (!empty($v['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $v['staff'] . '/small_' . $v['avatar_url']) : '';
    //                     }
    //                     $internal_proposal[$key]['progress'][] = [
    //                         'bod'      => $v['bod'],
    //                         'title'    => preg_replace('/^(2\.|3\.)\s*/', '', $v['name_process']),
    //                         'user'     => $user,
    //                         'status'   => 'pending_dxnt',
    //                         'avatar_url' => $avatar_url,
    //                     ];
    //                 }
    //             }
    //             if ($v['status'] == 0) {
    //                 $quahan = 1;
    //                 $status_finish = 0;
    //             }
    //             if ($v['status'] == 2) {
    //                 $quahan = 1;
    //                 $status_finish = 0;
    //             }
    //             if ($v['bod'] == 1) {
    //                 if ($v['status'] == 1) {
    //                     $status = 1;
    //                 }
    //             }
    //         }
    //         $internal_proposal[$key]['status_finish'] = $status_finish;

    //         if ($status == 0) {
    //             $internal_proposal[$key]['status_color'] = 'yellow_dxnt'; //chưa duyệt
    //         }
    //         if ($status == 1) {
    //             $internal_proposal[$key]['status_color'] = 'green_dxnt'; //đã duyệt
    //         }
    //         if ($quahan == 1) {
    //             if ($aRow['date_finish'] < date('Y-m-d')) {
    //                 $internal_proposal[$key]['status_color'] = 'red_dxnt'; //quá hạn
    //             }
    //         }
    //     }
    //     // $countFinancialAccountingDXNB = $this->countFinancialAccountingDXNB();
    //     // $stats['status_finish_1'] = $countFinancialAccountingDXNB['status_finish_1'];
    //     // $stats['status_finish_0'] = $countFinancialAccountingDXNB['status_finish_0'];
    //     // $countDeliveriesKHQ = $this->countDeliveriesKHQ();
    //     // $stats['count_code_custom_null'] = $countDeliveriesKHQ['count_code_custom_null'];
    //     // $stats['count_code_custom_not_null'] = $countDeliveriesKHQ['count_code_custom_not_null'];

    //     // $countDeliverieshdm = $this->countDeliverieshdm();
    //     // $stats['count_delivery_supplier_code_null'] = $countDeliverieshdm['count_delivery_supplier_code_null'];
    //     // $stats['count_delivery_supplier_code_not_null'] = $countDeliverieshdm['count_delivery_supplier_code_not_null'];

    //     // $countycc = $this->countycc();
    //     // $stats['count_ycc'] = $countycc['count_ycc'];

    //     // $countdxtc = $this->countdxtc();
    //     // $stats['countdxtc'] = $countdxtc['countdxtc'];

    //     // $countDeliveriesthu = $this->countDeliveriesthu();
    //     // $stats['count_delivery_thu'] = $countDeliveriesthu['count_delivery_thu'];
    //     // $countDeliverieshdbcx = $this->countDeliverieshdbcx();
    //     // $stats['count_delivery_cx'] = $countDeliverieshdbcx['count_delivery_cx'];
    //     header('Content-Type: application/json');
    //     echo json_encode([
    //         'success'    => true,
    //         'stats' => [],
    //         'internal_proposal'  => array_map([$this, '_api_row'], $internal_proposal),
    //         'changed_id' => null
    //     ]);
    // }
    public function updatefinancialAccountingDXNB()
    {
        // --- 1) Lấy danh sách đề xuất + các cột người liên quan (giống table()) ---
        $tb_tamp = "(
            SELECT
                tblinternal_proposal_recommended.id_internal_proposal as id_internal_proposal,
                GROUP_CONCAT(CONCAT('-',tbl_recommended_list.name) SEPARATOR '<br>') as name
            FROM tblinternal_proposal_recommended
            JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal_recommended.recommended_list_detail_id
            GROUP BY tblinternal_proposal_recommended.id_internal_proposal
        ) tb_tamp";
        $this->db->select("
        ip.id,
        ip.date,
        ip.code,
        ip.money,
        ip.date_finish,
        COALESCE(group_rl.name, '') AS name_recommended_list,
        ct.code AS code_category_tasks,
        ip.staff AS employee_id,
        CONCAT(s.firstname,' ',s.lastname) AS fullname_employee,
        ip.manager_id,
        ip.auditor_id,
        ip.staff_controller_completes,
        ip.staff_auditor_completes
    ");
        $this->db->from('tblinternal_proposal AS ip');
        $this->db->where('ip.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('ip.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->join('tbl_recommended_list AS rl', 'rl.id = ip.recommended_list_id', 'left');
        $this->db->join('tbl_recommended_list group_rl', 'group_rl.id = ip.recommended_list_group_id', 'left');
        $this->db->join('tblcategory_tasks AS ct', 'ct.id = ip.category_tasks', 'left');
        $this->db->join('tblstaff AS s', 's.staffid = ip.staff', 'left');

        $internal_proposal = $this->db->get()->result_array();
        if (empty($internal_proposal)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'stats' => [],
                'internal_proposal' => [],
                'changed_id' => null
            ]);
            return;
        }

        $ids = array_column($internal_proposal, 'id');

        // --- 2) Lấy toàn bộ process cho các đề xuất ---
        $this->db->select("
        p.id,
        p.id_internal_proposal,
        p.id_process,
        p.status,
        p.bod,
        p.name AS name_process,
        p.staff_id,
        st.staffid AS staff_numeric_id,
        st.profile_image AS staff_profile_image
    ");
        $this->db->from('tbl_internal_proposal_process AS p');
        $this->db->join('tblstaff AS st', 'st.staffid = p.staff_id', 'left');
        $this->db->where_in('p.id_internal_proposal', $ids);
        $this->db->order_by('p.id_internal_proposal ASC, p.id_process ASC');
        $rows = $this->db->get()->result_array();

        // --- 3) Prefetch người theo các bảng phụ (lấy 1 người đầu tiên giống table()) ---
        $pickFirstByProposal = function ($table, $fieldStaff = 'id_staff') use ($ids) {
            if (empty($ids)) {
                return [];
            }

            // Lấy 1 staff "ổn định" cho mỗi proposal: MIN(...) theo id_internal_proposal
            $this->db->select("MIN(t.$fieldStaff) AS staff_id, t.id_internal_proposal", false);
            $this->db->from("$table AS t");
            $this->db->where_in('t.id_internal_proposal', $ids);
            $this->db->group_by('t.id_internal_proposal');

            $rows = $this->db->get()->result_array();
            $out = [];
            foreach ($rows as $r) {
                $pid = (int)$r['id_internal_proposal'];
                if (!empty($r['staff_id'])) {
                    $out[$pid] = (int)$r['staff_id'];
                }
            }
            return $out;
        };

        $assignedMap = $pickFirstByProposal('tblinternal_proposal_assigned');               // bod=2
        $bodPodMap = $pickFirstByProposal('tblinternal_proposal_staff_pod');              // bod=1
        $monitorMap = $pickFirstByProposal('tblinternal_proposal_monitor');                // bod=7
        $hodMap = $pickFirstByProposal('tblinternal_proposal_head_of_department');     // bod=3

        // --- 4) Build info nhân sự: name + avatar_url (URL file, không trả HTML) ---
        $nameOf = function ($staffId) {
            return $staffId ? get_staff_full_name($staffId) : '';
        };
        $avatarUrlOf = function ($staffId) {
            if (empty($staffId)) {
                return '';
            }
            // tìm file ảnh nhỏ giống cách table() render (small_)
            $this->db->select('profile_image');
            $this->db->from('tblstaff');
            $this->db->where('staffid', $staffId);
            $row = $this->db->get()->row_array();
            if (!empty($row['profile_image'])) {
                return base_url('uploads/staff_profile_images/' . intval($staffId) . '/small_' . $row['profile_image']);
            }
            return '';
        };

        // --- 5) Group process theo đề xuất ---
        $byProposal = [];
        foreach ($rows as $r) {
            $byProposal[$r['id_internal_proposal']][] = $r;
        }

        // --- 6) Resolver: chọn nhân sự hiển thị theo bod (y chang table()) ---
        $resolvePersonByBod = function (array $prop, int $bod) use (
            $assignedMap,
            $bodPodMap,
            $monitorMap,
            $hodMap,
            $nameOf,
            $avatarUrlOf
        ) {
            $staffId = null;
            switch ($bod) {
                case 1:
                    $staffId = $bodPodMap[$prop['id']] ?? null;
                    break;                        // Người duyệt thực thi
                case 2:
                    $staffId = $assignedMap[$prop['id']] ?? null;
                    break;                      // Người duyệt đề xuất
                case 3:
                    $staffId = $hodMap[$prop['id']] ?? null;
                    break;                           // Người Hoàn Thành 2
                case 4:
                    $staffId = $prop['auditor_id'] ?? null;
                    break;                            // Kiểm toán
                case 5:
                    $staffId = $prop['employee_id'] ?? null;
                    break;                           // Người lập
                case 6:
                    $staffId = $prop['manager_id'] ?? null;
                    break;                            // Hoàn thành 2
                case 7:
                    $staffId = $monitorMap[$prop['id']] ?? null;
                    break;                       // Monitor
                case 8:
                    $staffId = $prop['staff_controller_completes'] ?? null;
                    break;            // Kiểm soát hoàn thành
                case 9:
                    $staffId = $prop['staff_auditor_completes'] ?? null;
                    break;               // Kiểm toán hoàn thành
                default:
                    $staffId = null;
            }
            $staffId = $staffId ? (int)$staffId : null;
            return [
                'name' => $nameOf($staffId),
                'avatar_url' => $avatarUrlOf($staffId),
                'id' => $staffId
            ];
        };

        // --- 7) Utils ---
        $normalizeTitle = function ($name) {
            return trim(preg_replace('/^\s*\d+[\.\)]\s*/', '', (string)$name));
        };
        $safeDate = function ($dateStr) {
            $t = $dateStr ? strtotime($dateStr) : false;
            return $t ?: null;
        };

        // --- 8) Build progress + status giống trước, nhưng user/avatar theo bod-map ---
        foreach ($internal_proposal as $k => $prop) {
            $list = $byProposal[$prop['id']] ?? [];
            $progress = [];

            $lastDoneIdx = -1;
            $hasPendingOrLate = false;
            $hasBODApproved = 0;

            $n = count($list);
            for ($i = 0; $i < $n; $i++) {
                $stt = (int)$list[$i]['status'];
                if ($stt === 1) {
                    $lastDoneIdx = $i;
                }
                if ($stt === 0 || $stt === 2) {
                    $hasPendingOrLate = true;
                }
                if ((int)$list[$i]['bod'] === 1 && $stt === 1) {
                    $hasBODApproved = 1;
                }
            }

            if ($lastDoneIdx >= 0) {
                $v = $list[$lastDoneIdx];
                // Ưu tiên tên/avatar theo vai trò (bod) như table(); nếu trống thì fallback staff_id của process
                $picked = $resolvePersonByBod($prop, (int)$v['bod']);
                if (empty($picked['name']) && !empty($v['staff_id'])) {
                    $picked = [
                        'name' => get_staff_full_name($v['staff_id']),
                        'avatar_url' => $avatarUrlOf($v['staff_id']),
                        'id' => (int)$v['staff_id']
                    ];
                }
                $progress[] = [
                    'bod' => (int)$v['bod'],
                    'title' => $normalizeTitle($v['name_process']),
                    'user' => $picked['name'],
                    'status' => 'done_dxnt',
                    'avatar_url' => $picked['avatar_url'],
                ];
            }

            $nextIdx = $lastDoneIdx + 1;
            if ($nextIdx >= 0 && $nextIdx < $n && (int)$list[$nextIdx]['status'] === 0) {
                $v = $list[$nextIdx];
                $picked = $resolvePersonByBod($prop, (int)$v['bod']);
                if (empty($picked['name'])) {
                    if (!empty($v['staff_id'])) {
                        $picked = [
                            'name' => get_staff_full_name($v['staff_id']),
                            'avatar_url' => $avatarUrlOf($v['staff_id']),
                            'id' => (int)$v['staff_id']
                        ];
                    } else {
                        // fallback cuối cùng: tên nhân sự của process join (nếu bạn có fullname ở select)
                        $picked = [
                            'name' => $prop['fullname_employee'] ?? '',
                            'avatar_url' => $avatarUrlOf($prop['employee_id'] ?? null),
                            'id' => $prop['employee_id'] ?? null
                        ];
                    }
                }
                $progress[] = [
                    'bod' => (int)$v['bod'],
                    'title' => $normalizeTitle($v['name_process']),
                    'user' => $picked['name'],
                    'status' => 'pending_dxnt',
                    'avatar_url' => $picked['avatar_url'],
                ];
            }

            $status_finish = $hasPendingOrLate ? 0 : 1;
            $status_color = ($hasBODApproved === 1) ? 'green_dxnt' : 'yellow_dxnt';

            $finishTs = $safeDate($prop['date_finish']);
            $todayMidnight = strtotime(date('Y-m-d'));
            if ($hasPendingOrLate && $finishTs && $finishTs < $todayMidnight) {
                $status_color = 'red_dxnt';
            }

            $internal_proposal[$k]['progress'] = $progress;
            $internal_proposal[$k]['status_finish'] = $status_finish;
            $internal_proposal[$k]['status_color'] = $status_color;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => [],
            'internal_proposal' => array_map([$this, '_api_row'], $internal_proposal),
            'changed_id' => null
        ]);
    }

    private function _api_row($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _d($r['date']),
            'status_finish' => ($r['status_finish']),
            'name_recommended_list' => ($r['name_recommended_list']),
            'code' => $r['code'],
            'money' => formatMoney($r['money']),
            'progress' => $r['progress'],
            'code_category_tasks' => $r['code_category_tasks'],
            'status_color' => $r['status_color'],
            'fullname_employee' => (int)$r['fullname_employee'],
            'image_employee' => staff_profile_image($r['employee_id'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee'] . '</span>'
        ];
    }

    //khai hai quan
    function updatadashboardKHQ()
    {
        $this->db->select("
        tbl_deliveries.id,
        tbl_deliveries.date,
        tbl_deliveries.reference_no,
        tbl_orders.reference_no as order_reference_no,
        tblclients.company_short as company,
        ");
        $this->db->where(
            'tbl_deliveries.date >',
            get_option('date_dashboard_srceen_accounting_ckhq') ? get_option('date_dashboard_srceen_accounting_ckhq') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00'
        );
        $this->db->where('tbl_deliveries.code_custom', null);
        $this->db->where('tblclients.declare_customs', 1);
        $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
        $this->db->group_start();
        $this->db->where('tbl_deliveries.date_dashboard_srceen_khq IS NULL');
        $this->db->or_where('tbl_deliveries.date_dashboard_srceen_khq !=', date('Y-m-d'));
        $this->db->or_where('tbl_deliveries.check_update_dashboard !=', get_option('check_update_dashboard'));
        $this->db->group_end();
        $deliveries = $this->db->get('tbl_deliveries')->result_array();
        foreach ($deliveries as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tbl_deliveries', [
                'date_dashboard_srceen_khq' => date('Y-m-d'),
                'check_update_dashboard' => get_option('check_update_dashboard')
            ]);
        }
    }

    public function updatefinancialAccountingKHQ()
    {

        $this->db->select("
        tbl_deliveries.id,
        tbl_deliveries.date,
        tbl_deliveries.reference_no,
        tbl_orders.reference_no as order_reference_no,
        tblclients.company_short as company,
        tbl_deliveries.code_custom as code_custom,
        ");
        $this->db->where('tblclients.declare_customs', 1);
        $this->db->where('tbl_deliveries.date_dashboard_srceen_khq', date('Y-m-d'));
        $this->db->where('tbl_deliveries.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
        $deliveries = $this->db->get('tbl_deliveries')->result_array();

        // $countFinancialAccountingDXNB = $this->countFinancialAccountingDXNB();
        // $stats['status_finish_1'] = $countFinancialAccountingDXNB['status_finish_1'];
        // $stats['status_finish_0'] = $countFinancialAccountingDXNB['status_finish_0'];
        // $countDeliveriesKHQ = $this->countDeliveriesKHQ();
        // $stats['count_code_custom_null'] = $countDeliveriesKHQ['count_code_custom_null'];
        // $stats['count_code_custom_not_null'] = $countDeliveriesKHQ['count_code_custom_not_null'];

        // $countDeliverieshdm = $this->countDeliverieshdm();
        // $stats['count_delivery_supplier_code_null'] = $countDeliverieshdm['count_delivery_supplier_code_null'];
        // $stats['count_delivery_supplier_code_not_null'] = $countDeliverieshdm['count_delivery_supplier_code_not_null'];

        // $countycc = $this->countycc();
        // $stats['count_ycc'] = $countycc['count_ycc'];

        // $countdxtc = $this->countdxtc();
        // $stats['countdxtc'] = $countdxtc['countdxtc'];

        // $countDeliveriesthu = $this->countDeliveriesthu();
        // $stats['count_delivery_thu'] = $countDeliveriesthu['count_delivery_thu'];
        // $countDeliverieshdbcx = $this->countDeliverieshdbcx();
        // $stats['count_delivery_cx'] = $countDeliverieshdbcx['count_delivery_cx'];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => [],
            'deliveries' => array_map([$this, '_api_rowdeliveries'], $deliveries),
            'changed_id' => null
        ]);
    }

    private function _api_rowdeliveries($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _d($r['date']),
            'reference_no' => ($r['reference_no']),
            'order_reference_no' => ($r['order_reference_no']),
            'company' => $r['company'],
            'code_custom' => $r['code_custom']
        ];
    }

    //kế toán tài chính mua hàng
    public function countDeliverieshdm()
    {
        $this->db->select("
            SUM(CASE WHEN (tblimport.delivery_supplier_code IS NULL OR tblimport.delivery_supplier_code = '') THEN 1 ELSE 0 END) AS count_delivery_supplier_code_null,
            SUM(CASE WHEN (tblimport.delivery_supplier_code IS NOT NULL AND tblimport.delivery_supplier_code != '') THEN 1 ELSE 0 END) AS count_delivery_supplier_code_not_null
        ");
        $this->db->where('tblimport.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tblimport.check_update_dashboard', get_option('check_update_dashboard'));
        $data = $this->db->get('tblimport')->row_array();
        return [
            'count_delivery_supplier_code_null' => (int)$data['count_delivery_supplier_code_null'],
            'count_delivery_supplier_code_not_null' => (int)$data['count_delivery_supplier_code_not_null'],
        ];
    }

    function updatadashboardhdm()
    {
        $this->db->select("tblimport.id");
        $this->db->where(
            'tblimport.date >',
            get_option('date_dashboard_srceen_accounting_hdmckk') ? get_option('date_dashboard_srceen_accounting_hdmckk') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00'
        );
        $this->db->group_start();
        $this->db->where('tblimport.delivery_supplier_code', null);
        $this->db->or_where('tblimport.delivery_supplier_code', '');
        $this->db->group_end();
        $this->db->group_start();
        $this->db->where('tblimport.date_dashboard_srceen IS NULL');
        $this->db->or_where('tblimport.date_dashboard_srceen !=', date('Y-m-d'));
        $this->db->or_where('tblimport.check_update_dashboard !=', get_option('check_update_dashboard'));
        $this->db->group_end();
        $deliveries = $this->db->get('tblimport')->result_array();
        foreach ($deliveries as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tblimport', [
                'date_dashboard_srceen' => date('Y-m-d'),
                'check_update_dashboard' => get_option('check_update_dashboard')
            ]);
        }
    }

    public function updatefinancialAccountinghdm()
    {

        $this->db->select("
        tblimport.id,
        tblimport.date,
        CONCAT(tblimport.prefix,'-',tblimport.code) as code_import,
        CONCAT(tblpurchase_order.prefix,'-',tblpurchase_order.code) as code_orders,
        tblsuppliers.company as company,
        tblimport.delivery_supplier_code as number_code,
        tblimport.staff_create as staff_create,
        CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname_employee

        ");
        $this->db->where('tblimport.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tblimport.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->join('tblsuppliers', 'tblsuppliers.id=tblimport.suppliers_id', 'left');
        $this->db->join('tblpurchase_order', 'tblpurchase_order.id=tblimport.id_order', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid=tblimport.staff_create', 'left');

        $import = $this->db->get('tblimport')->result_array();

        // $countFinancialAccountingDXNB = $this->countFinancialAccountingDXNB();
        // $stats['status_finish_1'] = $countFinancialAccountingDXNB['status_finish_1'];
        // $stats['status_finish_0'] = $countFinancialAccountingDXNB['status_finish_0'];
        // $countDeliveriesKHQ = $this->countDeliveriesKHQ();
        // $stats['count_code_custom_null'] = $countDeliveriesKHQ['count_code_custom_null'];
        // $stats['count_code_custom_not_null'] = $countDeliveriesKHQ['count_code_custom_not_null'];

        // $countDeliverieshdm = $this->countDeliverieshdm();
        // $stats['count_delivery_supplier_code_null'] = $countDeliverieshdm['count_delivery_supplier_code_null'];
        // $stats['count_delivery_supplier_code_not_null'] = $countDeliverieshdm['count_delivery_supplier_code_not_null'];

        // $countycc = $this->countycc();
        // $stats['count_ycc'] = $countycc['count_ycc'];

        // $countdxtc = $this->countdxtc();
        // $stats['countdxtc'] = $countdxtc['countdxtc'];

        // $countDeliveriesthu = $this->countDeliveriesthu();
        // $stats['count_delivery_thu'] = $countDeliveriesthu['count_delivery_thu'];
        // $countDeliverieshdbcx = $this->countDeliverieshdbcx();
        // $stats['count_delivery_cx'] = $countDeliverieshdbcx['count_delivery_cx'];

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => [],
            'import' => array_map([$this, '_api_rowimport'], $import),
            'changed_id' => null
        ]);
    }

    private function _api_rowimport($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _d($r['date']),
            'code_orders' => ($r['code_orders']),
            'code_import' => ($r['code_import']),
            'company' => $r['company'],
            'fullname_employee' => (int)$r['fullname_employee'],
            'proposerImg' => staff_profile_image($r['staff_create'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee'] . '</span>'
        ];
    }

    //kế toán tài chính hóa đơn bán chưa xuất
    public function countDeliverieshdbcx()
    {
        $this->db->where('tbl_deliveries.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tbl_deliveries.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->where('tbl_deliveries.warehouseman_id', 0);
        $this->db->from('tbl_deliveries');
        $data = $this->db->get()->num_rows();
        return [
            'count_delivery_cx' => (int)$data
        ];
    }

    public function updatefinancialAccountinghdbcx()
    {

        $this->db->select("
        tbl_deliveries.id,
        tbl_deliveries.date,
        tbl_deliveries.reference_no,
        tbl_orders.reference_no as order_reference_no,
        tblclients.company_short as company,
        tbl_deliveries.code_custom as code_custom,
        tbl_deliveries.warehouseman_id as warehouseman_id,
        ");
        $this->db->where('tbl_deliveries.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tbl_deliveries.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
        $deliveries = $this->db->get('tbl_deliveries')->result_array();

        // $countFinancialAccountingDXNB = $this->countFinancialAccountingDXNB();
        // $stats['status_finish_1'] = $countFinancialAccountingDXNB['status_finish_1'];
        // $stats['status_finish_0'] = $countFinancialAccountingDXNB['status_finish_0'];
        // $countDeliveriesKHQ = $this->countDeliveriesKHQ();
        // $stats['count_code_custom_null'] = $countDeliveriesKHQ['count_code_custom_null'];
        // $stats['count_code_custom_not_null'] = $countDeliveriesKHQ['count_code_custom_not_null'];

        // $countDeliverieshdm = $this->countDeliverieshdm();
        // $stats['count_delivery_supplier_code_null'] = $countDeliverieshdm['count_delivery_supplier_code_null'];
        // $stats['count_delivery_supplier_code_not_null'] = $countDeliverieshdm['count_delivery_supplier_code_not_null'];

        // $countycc = $this->countycc();
        // $stats['count_ycc'] = $countycc['count_ycc'];

        // $countdxtc = $this->countdxtc();
        // $stats['countdxtc'] = $countdxtc['countdxtc'];

        // $countDeliveriesthu = $this->countDeliveriesthu();
        // $stats['count_delivery_thu'] = $countDeliveriesthu['count_delivery_thu'];
        // $countDeliverieshdbcx = $this->countDeliverieshdbcx();
        // $stats['count_delivery_cx'] = $countDeliverieshdbcx['count_delivery_cx'];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => [],
            'deliveriescx' => array_map([$this, '_api_rowdeliveriescx'], $deliveries),
            'changed_id' => null
        ]);
    }

    private function _api_rowdeliveriescx($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _d($r['date']),
            'reference_no' => ($r['reference_no']),
            'order_reference_no' => ($r['order_reference_no']),
            'company' => $r['company'],
            'code_custom' => $r['code_custom'],
            // 'tre' => ($r['warehouseman_id'] == 0 && strtotime($r['date']) < strtotime(date('Y-m-d'))) ? 1 : 0,
            'tre' => (($r['warehouseman_id'] == 0 && strtotime($r['date']) < strtotime(date('Y-m-d')))
                ? (int)floor((strtotime(date('Y-m-d')) - strtotime($r['date'])) / (60 * 60 * 24))
                : 0) . ' ngày',
        ];
    }

    //ke toan tai chinh phieu yeu cau chi
    function updatadashboardycc()
    {
        $this->db->select("tbl_suggest_payslips.id");
        $this->db->where(
            'tbl_suggest_payslips.date >',
            get_option('date_dashboard_srceen_accounting_ycc') ? get_option('date_dashboard_srceen_accounting_ycc') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00'
        );
        // $this->db->group_start();
        // $this->db->where('tbl_suggest_payslips.status', NULL);
        // $this->db->or_where('tbl_suggest_payslips.status', 0);
        // $this->db->group_end();
        $this->db->group_start();
        $this->db->where('tbl_suggest_payslips.date_dashboard_srceen IS NULL');
        $this->db->or_where('tbl_suggest_payslips.date_dashboard_srceen !=', date('Y-m-d'));
        $this->db->or_where('tbl_suggest_payslips.check_update_dashboard !=', get_option('check_update_dashboard'));
        $this->db->group_end();
        $this->db->join('tblsuggestion', 'tblsuggestion.detail_suggest_muti_id=tbl_suggest_payslips.id', 'left');
        $this->db->group_start();
        $this->db->where('tblsuggestion.payments', 0);
        $this->db->or_where('tblsuggestion.id', null);
        $this->db->group_end();
        $deliveries = $this->db->get('tbl_suggest_payslips')->result_array();
        foreach ($deliveries as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tbl_suggest_payslips', [
                'date_dashboard_srceen' => date('Y-m-d'),
                'check_update_dashboard' => get_option('check_update_dashboard')
            ]);
        }
    }

    public function countycc()
    {
        $this->db->select("
            SUM(CASE WHEN (tblsuggestion.id IS NULL OR tblsuggestion.payments = 0) THEN 1 ELSE 0 END) AS count_ycc,
        ");
        $this->db->where('tbl_suggest_payslips.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tbl_suggest_payslips.check_update_dashboard', get_option('check_update_dashboard'));

        $this->db->join('tblsuggestion', 'tblsuggestion.detail_suggest_muti_id=tbl_suggest_payslips.id', 'left');
        $data = $this->db->get('tbl_suggest_payslips')->row_array();
        return [
            'count_ycc' => (int)$data['count_ycc']
        ];
    }

    public function updatefinancialAccountingycc()
    {

        $this->db->select("
        tbl_suggest_payslips.id,
        tbl_suggest_payslips.date,
        tbl_suggest_payslips.reference_no as code,
        tblsuppliers.company as company,
        tbl_suggest_payslips.staff_id as staff_id,
        tbl_suggest_payslips.status as status,
        CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname_employee,
        tblsuggestion.payments as payments,
        ");
        $this->db->where('tbl_suggest_payslips.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tbl_suggest_payslips.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->join('tblsuppliers', 'tblsuppliers.id=tbl_suggest_payslips.suppliers_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid=tbl_suggest_payslips.staff_id', 'left');
        $this->db->join('tblsuggestion', 'tblsuggestion.detail_suggest_muti_id=tbl_suggest_payslips.id', 'left');
        $ycc = $this->db->get('tbl_suggest_payslips')->result_array();
        foreach ($ycc as $key => $value) {
            $this->db->select('tbl_category_payslip.name');
            $this->db->where('suggest_payslips_id', $value['id']);
            $this->db->join(
                'tbl_category_payslip',
                'tbl_category_payslip.id=tbl_suggest_payslips_items.category_payslip',
                'left'
            );
            $suggest_payslips_items = $this->db->get('tbl_suggest_payslips_items')->row_array();
            $ycc[$key]['note_item'] = isset($suggest_payslips_items) ? ($suggest_payslips_items['name']) : '';
            $ycc[$key]['status_color'] = '';
            $ycc[$key]['status_hide'] = 0;
            if ($value['status'] == 0) {
                $ycc[$key]['status_color'] = 'green_dxnt';
            } else {
                if ($value['payments'] == 0) {
                    $ycc[$key]['status_color'] = 'yellow_dxnt';
                } else {
                    $ycc[$key]['status_hide'] = 1;
                }
            }
        }
        // $countFinancialAccountingDXNB = $this->countFinancialAccountingDXNB();
        // $stats['status_finish_1'] = $countFinancialAccountingDXNB['status_finish_1'];
        // $stats['status_finish_0'] = $countFinancialAccountingDXNB['status_finish_0'];
        // $countDeliveriesKHQ = $this->countDeliveriesKHQ();
        // $stats['count_code_custom_null'] = $countDeliveriesKHQ['count_code_custom_null'];
        // $stats['count_code_custom_not_null'] = $countDeliveriesKHQ['count_code_custom_not_null'];

        // $countDeliverieshdm = $this->countDeliverieshdm();
        // $stats['count_delivery_supplier_code_null'] = $countDeliverieshdm['count_delivery_supplier_code_null'];
        // $stats['count_delivery_supplier_code_not_null'] = $countDeliverieshdm['count_delivery_supplier_code_not_null'];

        // $countycc = $this->countycc();
        // $stats['count_ycc'] = $countycc['count_ycc'];

        // $countdxtc = $this->countdxtc();
        // $stats['countdxtc'] = $countdxtc['countdxtc'];

        // $countDeliveriesthu = $this->countDeliveriesthu();
        // $stats['count_delivery_thu'] = $countDeliveriesthu['count_delivery_thu'];
        // $countDeliverieshdbcx = $this->countDeliverieshdbcx();
        // $stats['count_delivery_cx'] = $countDeliverieshdbcx['count_delivery_cx'];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => [],
            'ycc' => array_map([$this, '_api_rowycc'], $ycc),
            'changed_id' => null
        ]);
    }

    private function _api_rowycc($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _d($r['date']),
            'code' => ($r['code']),
            'status' => $r['status'],
            'status_color' => $r['status_color'],
            'status_hide' => $r['status_hide'],
            'company' => $r['company'],
            'note_item' => $r['note_item'],
            'fullname_employee' => (int)$r['fullname_employee'],
            'proposerImg' => staff_profile_image($r['staff_id'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee'] . '</span>'
        ];
    }

    //Phòng kinh doanh
    public function updateBusinessDepartment()
    {
        // hauhau
        $this->db->select("
        tbl_quotes.id,
        tbl_quotes.reference_no,
        tbl_quotes.created_by as staff,
        tbl_quotes.order_id as order_id,
        tblclients.company_short as company,
        tbl_quotes.status as status,
        CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname_employee
        ");
        $this->db->where(
            'tbl_quotes.date >',
            get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00'
        );
        $this->db->where('tbl_quotes.order_id', 0);
        $this->db->join('tblstaff', 'tblstaff.staffid=tbl_quotes.created_by', 'left');
        $this->db->join('tblclients', 'tblclients.userid=tbl_quotes.customer_id', 'left');
        $quotes = $this->db->get('tbl_quotes')->result_array();


        $this->db->select("
        tbl_request_template.id,
        tbl_request_template.reference_no,
        tbl_request_template.staff_create as staff,
        tbl_quotes.order_id as order_id,
        tblclients.company_short as company,
        tbl_quotes.status as status,
        CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname_employee
        ");
        $this->db->where(
            'tbl_request_template.date >',
            get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00'
        );
        $this->db->where('tbl_quotes.order_id', 0);
        $this->db->join('tbl_quotes', 'tbl_quotes.id=tbl_request_template.id_quotes', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid=tbl_quotes.created_by', 'left');
        $this->db->join('tblclients', 'tblclients.userid=tbl_request_template.client_id', 'left');
        $request_template = $this->db->get('tbl_request_template')->result_array();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'quotes' => array_map([$this, '_api_row_quotes'], $quotes),
            'request_template' => array_map([$this, '_api_row_quotes'], $request_template),
            'changed_id' => null
        ]);
    }

    private function _api_row_quotes($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'reference_no' => $r['reference_no'],
            'company' => $r['company'],
            'status' => $r['status'],
            'order_id' => $r['order_id'],
            'percent' => '',
            'fullname_employee' => (int)$r['fullname_employee'],
            'image_employee' => staff_profile_image($r['staff'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee'] . '</span>'
        ];
    }

    //phong de xuat tai chinh
    function updatadashboarddxtc()
    {
        $this->db->select("tblsuggestion.id");
        $this->db->where(
            'tblsuggestion.date >',
            get_option('date_dashboard_srceen_accounting_dxtc') ? get_option('date_dashboard_srceen_accounting_dxtc') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00'
        );
        $this->db->group_start();
        $this->db->where('tblsuggestion.status_tp', 0);
        $this->db->or_where('tblsuggestion.status_dn', 0);
        $this->db->or_where('tblsuggestion.treasurer', null);
        $this->db->group_end();
        $this->db->group_start();
        $this->db->where('tblsuggestion.date_dashboard_srceen IS NULL');
        $this->db->or_where('tblsuggestion.date_dashboard_srceen !=', date('Y-m-d'));
        $this->db->or_where('tblsuggestion.check_update_dashboard !=', get_option('check_update_dashboard'));
        $this->db->group_end();
        $this->db->group_start();
        $this->db->where('tblsuggestion.payments', 0);
        $this->db->or_where('tblsuggestion.id', null);
        $this->db->group_end();
        $deliveries = $this->db->get('tblsuggestion')->result_array();
        foreach ($deliveries as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tblsuggestion', [
                'date_dashboard_srceen' => date('Y-m-d'),
                'check_update_dashboard' => get_option('check_update_dashboard')
            ]);
        }
    }

    public function countdxtc()
    {
        $this->db->group_start();
        $this->db->where('tblsuggestion.status_tp', 0);
        $this->db->or_where('tblsuggestion.status_dn', 0);
        $this->db->or_where('tblsuggestion.treasurer', null);
        $this->db->group_end();
        $this->db->group_start();
        $this->db->where('tblsuggestion.payments', 0);
        $this->db->or_where('tblsuggestion.id', null);
        $this->db->group_end();
        $this->db->where('tblsuggestion.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tblsuggestion.check_update_dashboard', get_option('check_update_dashboard'));
        $count = $this->db->count_all_results('tblsuggestion');
        return [
            'countdxtc' => (int)$count
        ];
    }

    public function updatefinancialAccountingdxtc()
    {

        $this->db->select("
        tblsuggestion.id,
        tblsuggestion.date,
        tblsuggestion.code as code,
        tblsuggestion.payments as payments,
        tblsuggestion.status_tp as status_tp,
        tblsuggestion.staff_status_tp as staff_status_tp,
        CONCAT(staff_tp.firstname,' ',staff_tp.lastname) as fullname_employee_staff_tp,
        tblsuggestion.status_dn as status_dn,
        tblsuggestion.staff_status_dn as staff_status_dn,
        CONCAT(staff_dn.firstname,' ',staff_dn.lastname) as fullname_employee_staff_dn,
        tblsuggestion.treasurer as treasurer,
        CONCAT(staff_treasurer.firstname,' ',staff_treasurer.lastname) as fullname_employee_staff_treasurer,
        tblsuggestion.staffid as staffid,
        CONCAT(staff_treasurer.firstname,' ',staff_treasurer.lastname) as fullname_employee_staff_treasurer,
        CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname_employee,
        tblsuggestion.staff_browse as staff_browse,
        CONCAT(staff_browse.firstname,' ',staff_browse.lastname) as fullname_employee_staff_browse,
        ");
        $this->db->where('tblsuggestion.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tblsuggestion.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->join('tblstaff ', 'tblstaff.staffid=tblsuggestion.staffid', 'left');
        $this->db->join('tblstaff staff_browse', 'staff_browse.staffid=tblsuggestion.staff_browse', 'left');
        $this->db->join('tblstaff staff_tp', 'staff_tp.staffid=tblsuggestion.staff_status_tp', 'left');
        $this->db->join('tblstaff staff_dn', 'staff_dn.staffid=tblsuggestion.staff_status_dn', 'left');
        $this->db->join('tblstaff staff_treasurer', 'staff_treasurer.staffid=tblsuggestion.treasurer', 'left');
        $dxtc = $this->db->get('tblsuggestion')->result_array();

        foreach ($dxtc as $key => $value) {
            // $dxtc[$key]['status'] = '<span class="dot_dxnt ${statusClass}"></span>';
            $dxtc[$key]['status'] = '<span class="inline-block label-text label-warning">Chưa Chi</span>';
            $dxtc[$key]['statustq'] = '<span class="dot_dxtc red_dxtc"></span>';
            $dxtc[$key]['statustp'] = '<span class="dot_dxtc red_dxtc"></span>';
            $dxtc[$key]['statusdn'] = '<span class="dot_dxtc red_dxtc"></span>';
            $dxtc[$key]['status_hide'] = 0;
            if ($value['payments'] > 0) {
                $dxtc[$key]['status'] = '<span class="inline-block label-text label-success">Đã Chi</span>';
            }
            if ($value['treasurer'] > 0) {
                // $dxtc[$key]['statustq'] = '<span class="dot_dxtc green_dxtc"></span><br>' . staff_profile_image($value['treasurer'], [
                //     'staff-profile-image-small-2x mbot5',
                // ], 'thumb') . '<br><span>' . $value['fullname_employee_staff_treasurer'] . '</span>';
                $dxtc[$key]['statustq'] = '<span class="dot_dxtc green_dxtc"></span>';
            }
            if ($value['status_tp'] > 0) {
                // $dxtc[$key]['statustp'] = '<span class="dot_dxtc green_dxtc"></span><br>' . staff_profile_image($value['staff_status_tp'], [
                //     'staff-profile-image-small-2x mbot5',
                // ], 'thumb') . '<br><span>' . $value['fullname_employee_staff_tp'] . '</span>';
                $dxtc[$key]['statustp'] = '<span class="dot_dxtc green_dxtc"></span>';
            }
            if ($value['status_dn'] > 0) {
                // $dxtc[$key]['statusdn'] = '<span class="dot_dxtc green_dxtc"></span><br>' . staff_profile_image($value['staff_status_dn'], [
                //     'staff-profile-image-small-2x mbot5',
                // ], 'thumb') . '<br><span>' . $value['fullname_employee_staff_dn'] . '</span>';
                $dxtc[$key]['statusdn'] = '<span class="dot_dxtc green_dxtc"></span>';
            }
        }
        // $countFinancialAccountingDXNB = $this->countFinancialAccountingDXNB();
        // $stats['status_finish_1'] = $countFinancialAccountingDXNB['status_finish_1'];
        // $stats['status_finish_0'] = $countFinancialAccountingDXNB['status_finish_0'];
        // $countDeliveriesKHQ = $this->countDeliveriesKHQ();
        // $stats['count_code_custom_null'] = $countDeliveriesKHQ['count_code_custom_null'];
        // $stats['count_code_custom_not_null'] = $countDeliveriesKHQ['count_code_custom_not_null'];

        // $countDeliverieshdm = $this->countDeliverieshdm();
        // $stats['count_delivery_supplier_code_null'] = $countDeliverieshdm['count_delivery_supplier_code_null'];
        // $stats['count_delivery_supplier_code_not_null'] = $countDeliverieshdm['count_delivery_supplier_code_not_null'];

        // $countycc = $this->countycc();
        // $stats['count_ycc'] = $countycc['count_ycc'];

        // $countdxtc = $this->countdxtc();
        // $stats['countdxtc'] = $countdxtc['countdxtc'];

        // $countDeliveriesthu = $this->countDeliveriesthu();
        // $stats['count_delivery_thu'] = $countDeliveriesthu['count_delivery_thu'];
        // $countDeliverieshdbcx = $this->countDeliverieshdbcx();
        // $stats['count_delivery_cx'] = $countDeliverieshdbcx['count_delivery_cx'];


        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => [],
            'dxtc' => array_map([$this, '_api_rowdxtc'], $dxtc),
            'changed_id' => null
        ]);
    }

    private function _api_rowdxtc($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _d($r['date']),
            'code' => ($r['code']),
            'status' => $r['status'],
            'payments' => $r['payments'],
            'statustq' => $r['statustq'],
            'statustp' => $r['statustp'],
            'statusdn' => $r['statusdn'],
            'status_hide' => $r['status_hide'],
            'fullname_employee' => (int)$r['fullname_employee'],
            'image_employee' => staff_profile_image($r['staffid'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee'] . '</span>',
            'fullname_employee_staff_browse' => (int)$r['fullname_employee_staff_browse'],
            'image_employee_staff_browse' => staff_profile_image($r['staff_browse'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee_staff_browse'] . '</span>'
        ];
    }

    //thutien
    function updatadashboardthu()
    {
        $this->db->select("
        tbl_deliveries.id,
        tbl_deliveries.date,
        tbl_deliveries.reference_no,
        tbl_orders.reference_no as order_reference_no,
        tblclients.company_short as company,
        tbl_invoices.status_payment as status_payment,
        ");
        $this->db->where(
            'tbl_deliveries.date >',
            get_option('date_dashboard_srceen_accounting_ckhq') ? get_option('date_dashboard_srceen_accounting_ckhq') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00'
        );
        $this->db->where('tbl_invoices.status_payment < ', 2);
        // 'INNER JOIN tbl_invoice_items ON tbl_invoice_items.invoice_id = tbl_invoices.id',
        // 'INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_invoice_items.object_id',
        $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'INNER');
        $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'INNER');
        $this->db->join('tbl_invoice_items', 'tbl_invoice_items.object_id=tbl_deliveries.id', 'left');
        $this->db->join('tbl_invoices', 'tbl_invoices.id=tbl_invoice_items.invoice_id', 'left');
        $this->db->group_start();
        $this->db->where('tbl_deliveries.date_dashboard_srceen_chuathu IS NULL');
        $this->db->or_where('tbl_deliveries.date_dashboard_srceen_chuathu !=', date('Y-m-d'));
        $this->db->or_where('tbl_deliveries.check_update_dashboard !=', get_option('check_update_dashboard'));
        $this->db->group_end();
        $deliveries = $this->db->get('tbl_deliveries')->result_array();
        foreach ($deliveries as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tbl_deliveries', [
                'date_dashboard_srceen_chuathu' => date('Y-m-d'),
                'check_update_dashboard' => get_option('check_update_dashboard')
            ]);
        }
    }

    public function countDeliveriesthu()
    {
        $this->db->where('tbl_deliveries.date_dashboard_srceen_chuathu', date('Y-m-d'));
        $this->db->where('tbl_deliveries.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->where('tbl_invoices.status_payment <', 2);
        $this->db->from('tbl_deliveries');
        $this->db->join('tbl_invoice_items', 'tbl_invoice_items.object_id=tbl_deliveries.id', 'left');
        $this->db->join('tbl_invoices', 'tbl_invoices.id=tbl_invoice_items.invoice_id', 'left');
        $data = $this->db->get()->num_rows();
        return [
            'count_delivery_thu' => (int)$data
        ];
    }

    public function updatefinancialAccountingthu()
    {

        $this->db->select("
        tbl_deliveries.id,
        tbl_deliveries.date,
        tbl_deliveries.reference_no,
        tbl_orders.reference_no as order_reference_no,
        tblclients.company_short as company,
        tbl_deliveries.warehouseman_id as warehouseman_id,
        tbl_invoices.status_payment as status_payment,
        ");
        $this->db->where('tbl_deliveries.date_dashboard_srceen_chuathu', date('Y-m-d'));
        $this->db->where('tbl_deliveries.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
        $this->db->join('tbl_invoice_items', 'tbl_invoice_items.object_id=tbl_deliveries.id', 'left');
        $this->db->join('tbl_invoices', 'tbl_invoices.id=tbl_invoice_items.invoice_id', 'left');
        $deliveries = $this->db->get('tbl_deliveries')->result_array();

        // $countFinancialAccountingDXNB = $this->countFinancialAccountingDXNB();
        // $stats['status_finish_1'] = $countFinancialAccountingDXNB['status_finish_1'];
        // $stats['status_finish_0'] = $countFinancialAccountingDXNB['status_finish_0'];
        // $countDeliveriesKHQ = $this->countDeliveriesKHQ();
        // $stats['count_code_custom_null'] = $countDeliveriesKHQ['count_code_custom_null'];
        // $stats['count_code_custom_not_null'] = $countDeliveriesKHQ['count_code_custom_not_null'];

        // $countDeliverieshdm = $this->countDeliverieshdm();
        // $stats['count_delivery_supplier_code_null'] = $countDeliverieshdm['count_delivery_supplier_code_null'];
        // $stats['count_delivery_supplier_code_not_null'] = $countDeliverieshdm['count_delivery_supplier_code_not_null'];

        // $countycc = $this->countycc();
        // $stats['count_ycc'] = $countycc['count_ycc'];

        // $countdxtc = $this->countdxtc();
        // $stats['countdxtc'] = $countdxtc['countdxtc'];

        // $countDeliveriesthu = $this->countDeliveriesthu();
        // $stats['count_delivery_thu'] = $countDeliveriesthu['count_delivery_thu'];
        // $countDeliverieshdbcx = $this->countDeliverieshdbcx();
        // $stats['count_delivery_cx'] = $countDeliverieshdbcx['count_delivery_cx'];

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => [],
            'deliveriescx' => array_map([$this, '_api_rowdeliveriesthu'], $deliveries),
            'changed_id' => null
        ]);
    }

    private function _api_rowdeliveriesthu($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _d($r['date']),
            'reference_no' => ($r['reference_no']),
            'order_reference_no' => ($r['order_reference_no']),
            'company' => $r['company'],
            'status_payment' => $r['status_payment'],
            'status_payment_text' => $r['status_payment'] == 0 ? 'Chưa thu' : ($r['status_payment'] == 1 ? 'Thu một phần' : 'Đã thu'),
        ];
    }

    function count_accounting()
    {

        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select("
            SUM(
            CASE 
                WHEN tbl_internal_proposal_process.bod = 1 AND tbl_internal_proposal_process.status = 0 THEN 1
                WHEN (tbl_internal_proposal_process.bod IS NULL) THEN 1
                ELSE 0
            END
            ) AS internal_proposal_money_pending,
            SUM(
            CASE 
                WHEN tbl_internal_proposal_process.bod = 1 AND tbl_internal_proposal_process.status > 0 THEN 1
                ELSE 0
            END
            ) AS internal_proposal_money_approved
        ");
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $this->db->where('tblinternal_proposal.money >', 0);
        $this->db->join(
            'tbl_internal_proposal_process',
            'tbl_internal_proposal_process.id_internal_proposal=tblinternal_proposal.id',
            'left'
        );
        $internal_proposal_money = $this->db->get('tblinternal_proposal')->row_array();
        $stats = [
            'internal_proposal_money_pending' => (float)$internal_proposal_money['internal_proposal_money_pending'],
            'internal_proposal_money_approved' => (float)$internal_proposal_money['internal_proposal_money_approved'],
        ];

        $this->db->select("
            SUM(CASE WHEN ((tblsuggestion.payments + 0.1) <= tblsuggestion.price_total) THEN 1 ELSE 0 END) AS count_not_payed,
            SUM(CASE WHEN ((tblsuggestion.payments + 0.1) >= tblsuggestion.price_total) THEN 1 ELSE 0 END) AS count_payed,
        ");
        $this->db->where('tblsuggestion.date >=', $date_dashboard_srceen_sales);
        $suggestion = $this->db->get('tblsuggestion')->row_array();
        $stats['count_not_payed'] = (float)$suggestion['count_not_payed'];
        $stats['count_payed'] = (float)$suggestion['count_payed'];


        $this->db->select("
            SUM(CASE WHEN tbl_deliveries.code_custom IS NULL THEN 1 ELSE 0 END) AS count_code_custom_null,
            SUM(CASE WHEN tbl_deliveries.code_custom IS NOT NULL THEN 1 ELSE 0 END) AS count_code_custom_not_null
        ");
        $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
        $this->db->where('tblclients.declare_customs', 1);
        $this->db->where('tbl_deliveries.date >=', $date_dashboard_srceen_sales);
        $deliveries = $this->db->get('tbl_deliveries')->row_array();
        $stats['count_code_custom_null'] = (float)$deliveries['count_code_custom_null'];
        $stats['count_code_custom_not_null'] = (float)$deliveries['count_code_custom_not_null'];


        $sql = "
        SELECT
            COUNT(DISTINCT i.id) AS total_imports,
            COUNT(DISTINCT CASE WHEN EXISTS (
                SELECT 1
                FROM tblimport_items ii
                JOIN tblpurchase_invoice_items pii ON pii.id_import_item = ii.id
                JOIN tblpurchase_invoice pi ON pi.id = pii.purchase_invoice_id AND pi.type_create = 0
                WHERE ii.id_import = i.id
            ) THEN i.id END) AS imports_with_invoice,
            COUNT(DISTINCT CASE WHEN NOT EXISTS (
                SELECT 1
                FROM tblimport_items ii
                JOIN tblpurchase_invoice_items pii ON pii.id_import_item = ii.id
                JOIN tblpurchase_invoice pi ON pi.id = pii.purchase_invoice_id AND pi.type_create = 0
                WHERE ii.id_import = i.id
            ) THEN i.id END) AS imports_without_invoice
        FROM tblimport i
        WHERE i.date >= ?
        ";

        $row = $this->db->query($sql, [$date_dashboard_srceen_sales])->row_array();

        // trả về hoặc gán vào $stats tuỳ chỗ bạn dùng
        $import = [
            'total_imports' => (int)($row['total_imports'] ?? 0),
            'imports_with_invoice' => (int)($row['imports_with_invoice'] ?? 0),
            'imports_without_invoice' => (int)($row['imports_without_invoice'] ?? 0),
        ];
        $stats['total_imports'] = (float)$import['total_imports'];
        $stats['imports_with_invoice'] = (float)$import['imports_with_invoice'];
        $stats['imports_without_invoice'] = (float)$import['imports_without_invoice'];


        $sqlsuggestion = "
        SELECT
            COUNT(DISTINCT s.id) AS total_suggestion,
            COUNT(DISTINCT CASE WHEN EXISTS (
            SELECT 1
            FROM tblpurchase_invoice_items pii
            JOIN tblpurchase_invoice pi ON pi.id = pii.purchase_invoice_id AND pi.type_create = 1
            WHERE pii.id_import_item = s.id
            ) THEN s.id END) AS suggestions_with_invoice,
            COUNT(DISTINCT CASE WHEN NOT EXISTS (
            SELECT 1
            FROM tblpurchase_invoice_items pii
            JOIN tblpurchase_invoice pi ON pi.id = pii.purchase_invoice_id AND pi.type_create = 1
            WHERE pii.id_import_item = s.id
            ) THEN s.id END) AS suggestions_without_invoice
        FROM tblsuggestion s
        WHERE s.date >= ?
        ";

        $rowsuggestion = $this->db->query($sqlsuggestion, [$date_dashboard_srceen_sales])->row_array();
        // trả về hoặc gán vào $stats tuỳ chỗ bạn dùng
        $suggestion = [
            'total_suggestion' => (int)($rowsuggestion['total_suggestion'] ?? 0),
            'suggestions_with_invoice' => (int)($rowsuggestion['suggestions_with_invoice'] ?? 0),
            'suggestions_without_invoice' => (int)($rowsuggestion['suggestions_without_invoice'] ?? 0),
        ];
        $stats['total_suggestion'] = (float)$suggestion['total_suggestion'];
        $stats['suggestions_with_invoice'] = (float)$suggestion['suggestions_with_invoice'];
        $stats['suggestions_without_invoice'] = (float)$suggestion['suggestions_without_invoice'];


        $this->db->select("
            COUNT(DISTINCT tbl_invoices.id) AS total_invoices,
            SUM(CASE WHEN tbl_invoices.status_payment < 2 THEN 1 ELSE 0 END) AS count_paymented_not,
            SUM(CASE WHEN tbl_invoices.status_payment = 2 THEN 1 ELSE 0 END) AS count_paymented
        ");
        $this->db->where('tbl_invoices.date >=', $date_dashboard_srceen_sales);
        $invoices = $this->db->get('tbl_invoices')->row_array();
        $stats['total_invoices'] = (float)$invoices['total_invoices'];
        $stats['count_paymented_not'] = (float)$invoices['count_paymented_not'];
        $stats['count_paymented'] = (float)$invoices['count_paymented'];


        $this->db->select("
            SUM(CASE WHEN tbl_deliveries.received_certificate = 1 THEN 1 ELSE 0 END) AS count_received_certificate,
            SUM(CASE WHEN tbl_deliveries.received_certificate = 0 THEN 1 ELSE 0 END) AS count_received_certificate_not
        ");
        $this->db->where('tbl_deliveries.date >=', $date_dashboard_srceen_sales);
        $deliveries = $this->db->get('tbl_deliveries')->row_array();
        $stats['count_received_certificate'] = (float)$deliveries['count_received_certificate'];
        $stats['count_received_certificate_not'] = (float)$deliveries['count_received_certificate_not'];

        $this->db->select("
            SUM(CASE WHEN tbl_invoice_items.id IS NULL THEN 1 ELSE 0 END) AS count_not_invoice,
            SUM(CASE WHEN tbl_invoice_items.id IS NOT NULL THEN 1 ELSE 0 END) AS count_invoice
        ");
        $this->db->where('tbl_deliveries.date >=', $date_dashboard_srceen_sales);
        $this->db->join(
            'tbl_invoice_items',
            'tbl_invoice_items.object_id=tbl_deliveries.id',
            'left'
        );
        $deliveries_invoice = $this->db->get('tbl_deliveries')->row_array();
        $stats['count_not_invoice'] = (float)$deliveries_invoice['count_not_invoice'];
        $stats['count_invoice'] = (float)$deliveries_invoice['count_invoice'];

        // Count supplier contracts whose latest contract per supplier has already ended
        // Count supplier contracts: latest contract per supplier ended (need re-sign) and re-signed (still active)
        $sql = "
            SELECT
            SUM(CASE WHEN c.date_end < ? THEN 1 ELSE 0 END) AS cnt_ended,
            SUM(CASE WHEN c.date_end >= ? THEN 1 ELSE 0 END) AS cnt_active
            FROM (
            SELECT MAX(id) AS id
            FROM tbl_contracts_supplier
            WHERE date_end IS NOT NULL
            GROUP BY supplier_id
            ) t
            JOIN tbl_contracts_supplier c ON c.id = t.id
        ";
        $row = $this->db->query($sql, [date('Y-m-d'), date('Y-m-d')])->row_array();
        $stats['contract_ncc_ctk'] = (int)($row['cnt_ended'] ?? 0);      // cần tái kí (date_end < today)
        $stats['contract_ncc_dtk'] = (int)($row['cnt_active'] ?? 0); // đã tái kí (date_end >= today)

        // Count client contracts whose latest contract per customer has already ended
        $sql2 = "
            SELECT
            SUM(CASE WHEN c.date_end < ? THEN 1 ELSE 0 END) AS cnt_ended,
            SUM(CASE WHEN c.date_end >= ? THEN 1 ELSE 0 END) AS cnt_active
            FROM (
            SELECT MAX(id) AS id
            FROM tbl_contracts_sales
            WHERE date_end IS NOT NULL
            GROUP BY customer_id
            ) t
            JOIN tbl_contracts_sales c ON c.id = t.id
        ";
        $row2 = $this->db->query($sql2, [date('Y-m-d'), date('Y-m-d')])->row_array();
        $stats['contract_clients_ctk'] = (int)($row2['cnt_ended'] ?? 0); // cần tái kí
        $stats['contract_clients_dtk'] = (int)($row2['cnt_active'] ?? 0); // đã tái kí

        $this->db->select("
            COUNT(tbl_request_repair.id) AS count_all_repair,
            SUM(
                CASE 
                    WHEN tbl_request_repair.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_repair,
            SUM(
                CASE 
                    WHEN tbl_request_repair.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_repair,
            SUM(
                CASE 
                    WHEN status_finish = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_finish_repair,
            SUM(
                CASE 
                    WHEN status_finish = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_finish_repair
        ", false);

        $this->db->from('tbl_request_repair');
        $this->db->where('tbl_request_repair.date >=', $date_dashboard_srceen_sales);
        $tb_request_repair = $this->db->get()->row_array();

        $stats['count_approved_repair'] = (int)$tb_request_repair['count_approved_repair'];
        $stats['count_un_approve_repair'] = (int)$tb_request_repair['count_un_approve_repair'];
        $stats['count_approved_finish_repair'] = (int)$tb_request_repair['count_approved_finish_repair'];
        $stats['count_un_approve_finish_repair'] = (int)$tb_request_repair['count_un_approve_finish_repair'];


        $this->db->select("
            SUM(
                CASE 
                    WHEN tblplan_propose.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_plan_propose_hh,
            SUM(
                CASE 
                    WHEN tblplan_propose.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_plan_propose_chh
        ", false);

        $this->db->from('tblplan_propose');
        $this->db->where('tblplan_propose.type_plan_propose', 'pay_slip');
        $this->db->where('tblplan_propose.date >=', $date_dashboard_srceen_sales);
        $plan_propose_chi = $this->db->get()->row_array();
        $stats['count_plan_propose_chi_hh'] = (int)$plan_propose_chi['count_plan_propose_hh'];
        $stats['count_plan_propose_chi_chh'] = (int)$plan_propose_chi['count_plan_propose_chh'];

        $this->db->select("
            SUM(
                CASE 
                    WHEN tblplan_propose.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_plan_propose_hh,
            SUM(
                CASE 
                    WHEN tblplan_propose.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_plan_propose_chh
        ", false);

        $this->db->from('tblplan_propose');
        $this->db->where('tblplan_propose.type_plan_propose', 'vouchers_coupon');
        $this->db->where('tblplan_propose.date >=', $date_dashboard_srceen_sales);
        $plan_propose_thu = $this->db->get()->row_array();
        $stats['count_plan_propose_thu_hh'] = (int)$plan_propose_thu['count_plan_propose_hh'];
        $stats['count_plan_propose_thu_chh'] = (int)$plan_propose_thu['count_plan_propose_chh'];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
    }

    function countkinhdoanh()
    {
        $this->updatadashboardthu();
        $this->updatadashboardDXNB();
        $this->updatadashboardKHQ();
        $this->updatadashboardhdm();
        $this->updatadashboardycc();
        $this->updatadashboarddxtc();
        $this->updatadashwarehouseimport();
        $this->updatadashwarehouseimportDXNB();
        $countFinancialAccountingDXNB = $this->countFinancialAccountingDXNB();
        $stats['status_finish_1'] = $countFinancialAccountingDXNB['status_finish_1'];
        $stats['status_finish_0'] = $countFinancialAccountingDXNB['status_finish_0'];
        $countDeliveriesKHQ = $this->countDeliveriesKHQ();
        $stats['count_code_custom_null'] = $countDeliveriesKHQ['count_code_custom_null'];
        $stats['count_code_custom_not_null'] = $countDeliveriesKHQ['count_code_custom_not_null'];

        $countDeliverieshdm = $this->countDeliverieshdm();
        $stats['count_delivery_supplier_code_null'] = $countDeliverieshdm['count_delivery_supplier_code_null'];
        $stats['count_delivery_supplier_code_not_null'] = $countDeliverieshdm['count_delivery_supplier_code_not_null'];

        $countycc = $this->countycc();
        $stats['count_ycc'] = $countycc['count_ycc'];

        $countdxtc = $this->countdxtc();
        $stats['countdxtc'] = $countdxtc['countdxtc'];

        $countDeliveriesthu = $this->countDeliveriesthu();
        $stats['count_delivery_thu'] = $countDeliveriesthu['count_delivery_thu'];
        $countDeliverieshdbcx = $this->countDeliverieshdbcx();
        $stats['count_delivery_cx'] = $countDeliverieshdbcx['count_delivery_cx'];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
    }

    //kho hàng
    public function countWarehouseImport()
    {
        $this->db->select("
            tblinternal_proposal.id,
            tblinternal_proposal.date,
            tblinternal_proposal.code,
            tblinternal_proposal.money,
            tblinternal_proposal.date_finish
        ");
        $this->db->group_start();
        $this->db->where('tblinternal_proposal.id_purchases >', 0);
        $this->db->or_where('tblinternal_proposal.id_purchases', -1);
        $this->db->group_end();
        $this->db->where('tblinternal_proposal.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tblinternal_proposal.check_update_dashboard', get_option('check_update_dashboard'));
        $internal_proposal = $this->db->get('tblinternal_proposal')->result_array();

        $count_finish = 0;
        $count_unfinish = 0;

        foreach ($internal_proposal as $aRow) {
            // Lấy danh sách process của phiếu
            $this->db->select('status, bod');
            $this->db->where('id_internal_proposal', $aRow['id']);
            $data_checklist_items = $this->db->get('tbl_internal_proposal_process')->result_array();

            $status = 1;
            $quahan = 0;
            $status_finish = 1;

            foreach ($data_checklist_items as $v) {
                if ($v['bod'] == 2 || $v['bod'] == 1) {
                    if ($v['status'] == 0) {
                        $status = 0;
                    }
                }
            }

            // Đếm
            if ($status == 1) {
                $count_finish++;
            } else {
                $count_unfinish++;
            }
        }
        return [
            'status_finish_1' => $count_finish,
            'status_finish_0' => $count_unfinish,
        ];
    }
    // public function updateWarehouseImport()
    // {

    //     $this->db->select("
    //     tblinternal_proposal.id,
    //     tblinternal_proposal.date,
    //     tblinternal_proposal.code,
    //     tblinternal_proposal.money,
    //     tblinternal_proposal.date_finish,
    //     coalesce(tbl_recommended_list.name, '') as name_recommended_list,
    //     tblcategory_tasks.code as code_category_tasks,
    //     tblinternal_proposal.staff as employee_id,
    //     CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname_employee
    //     ");
    //     $this->db->group_start();
    //     $this->db->where('tblinternal_proposal.id_purchases >', 0);
    //     $this->db->or_where('tblinternal_proposal.id_purchases', -1);
    //     $this->db->group_end();
    //     $this->db->where('tblinternal_proposal.date_dashboard_srceen', date('Y-m-d'));
    //     $this->db->where('tblinternal_proposal.check_update_dashboard', get_option('check_update_dashboard'));
    //     $this->db->join('tbl_recommended_list', 'tbl_recommended_list.id = tblinternal_proposal.recommended_list_id', 'left');
    //     $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblinternal_proposal.category_tasks', 'left');
    //     $this->db->join('tblstaff', 'tblstaff.staffid=tblinternal_proposal.staff', 'left');
    //     $internal_proposal = $this->db->get('tblinternal_proposal')->result_array();
    //     foreach ($internal_proposal as $key => $aRow) {
    //         $this->db->select('tbl_internal_proposal_process.*,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tblstaff.staffid as staff,tbl_internal_proposal_process_child.id as childs,tbl_internal_proposal_process.bod,tbl_internal_proposal_process.name as name_process,tblstaff.profile_image as avatar_url');
    //         $this->db->where('tbl_internal_proposal_process.id_internal_proposal', $aRow['id']);
    //         $this->db->join('tbl_internal_proposal_process_child', 'tbl_internal_proposal_process_child.recommended_list_id = tbl_internal_proposal_process.id_process AND tbl_internal_proposal_process_child.id_internal_proposal = tbl_internal_proposal_process.id_internal_proposal', 'left');
    //         $this->db->join('tblstaff', 'tblstaff.staffid=tbl_internal_proposal_process.staff_id', 'left');
    //         $this->db->order_by('tbl_internal_proposal_process.id_process asc');
    //         $this->db->group_by('tbl_internal_proposal_process.id');
    //         $data_checklist_items = $this->db->get('tbl_internal_proposal_process')->result_array();
    //         $internal_proposal[$key]['progress'] = [];
    //         $status = 1;
    //         $quahan = 0;
    //         $status_finish = 1;
    //         foreach ($data_checklist_items as $k => $v) {
    //             if ($v['bod'] == 1 || $v['bod'] == 2) {
    //                 if ($v['status'] == 1) {
    //                     $internal_proposal[$key]['progress'][] = [
    //                         'bod'      => $v['bod'],
    //                         'title'      => preg_replace('/^(2\.|3\.)\s*/', '', $v['name_process']),
    //                         'user'       => $v['fullname_employee'],
    //                         'status'     => $v['status'] == 1 ? 'done_puwa' : 'pending_puwa',
    //                         'avatar_url'       => (!empty($v['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $v['staff'] . '/small_' . $v['avatar_url']) : '',
    //                     ];
    //                 } elseif ($v['status'] == 0) {
    //                     if ($v['bod'] == 1) {
    //                         $this->db->select('CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tblstaff.staffid as staff,tblstaff.profile_image as avatar_url');
    //                         $this->db->where('id_internal_proposal', $aRow['id']);
    //                         $this->db->where('id_staff !=', NULL);
    //                         $this->db->join('tblstaff', 'tblstaff.staffid=tblinternal_proposal_staff_pod.id_staff', 'left');
    //                         $assigned_pod = $this->db->get('tblinternal_proposal_staff_pod')->row_array();
    //                         if (!empty($assigned_pod)) {
    //                             $internal_proposal[$key]['progress'][] = [
    //                                 'bod'      => $v['bod'],
    //                                 'title'      => preg_replace('/^(2\.|3\.)\s*/', '', $v['name_process']),
    //                                 'user'       => $assigned_pod['fullname_employee'],
    //                                 'status'     => $v['status'] == 1 ? 'done_puwa' : 'pending_puwa',
    //                                 'avatar_url'       => (!empty($assigned_pod['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $assigned_pod['staff'] . '/small_' . $assigned_pod['avatar_url']) : '',
    //                             ];
    //                         }
    //                     }
    //                     if ($v['bod'] == 2) {
    //                         $this->db->select('CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tblstaff.staffid as staff,tblstaff.profile_image as avatar_url');
    //                         $this->db->where('id_internal_proposal', $aRow['id']);
    //                         $this->db->where('id_staff !=', NULL);
    //                         $this->db->join('tblstaff', 'tblstaff.staffid=tblinternal_proposal_assigned.id_staff', 'left');
    //                         $this->db->where('id_internal_proposal', $aRow['id']);
    //                         $assigned = $this->db->get('tblinternal_proposal_assigned')->row_array();
    //                         if (!empty($assigned)) {
    //                             $internal_proposal[$key]['progress'][] = [
    //                                 'bod'      => $v['bod'],
    //                                 'title'      => preg_replace('/^(2\.|3\.)\s*/', '', $v['name_process']),
    //                                 'user'       => $assigned['fullname_employee'],
    //                                 'status'     => $v['status'] == 1 ? 'done_puwa' : 'pending_puwa',
    //                                 'avatar_url'       => (!empty($assigned['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $assigned['staff'] . '/small_' . $assigned['avatar_url']) : '',
    //                             ];
    //                         }
    //                     }
    //                 }
    //             }
    //             if ($v['bod'] == 2 || $v['bod'] == 1) {
    //                 if ($v['status'] == 0) {
    //                     $status = 0;
    //                 }
    //             }
    //             if ($v['status'] == 2) {
    //                 $status = 0;
    //             }
    //         }
    //         $internal_proposal[$key]['status_finish'] = $status;

    //         if ($status == 0) {
    //             $internal_proposal[$key]['status_color'] = 'yellow_puwa'; //chưa duyệt
    //         }
    //         if ($status == 1) {
    //             $internal_proposal[$key]['status_color'] = 'green_puwa'; //đã duyệt
    //         }
    //     }

    //     header('Content-Type: application/json');
    //     echo json_encode([
    //         'success'    => true,
    //         'stats' => [],
    //         'purchase_order'  => array_map([$this, '_api_rowwarehouse_import'], $internal_proposal),
    //         'changed_id' => null
    //     ]);
    // }
    public function updateWarehouseImport()
    {
        // 1) Lấy danh sách phiếu có liên quan đến mua hàng (id_purchases > 0 hoặc = -1)
        $this->db->select("
        ip.id,
        ip.date,
        ip.code,
        ip.money,
        ip.date_finish,
        COALESCE(rl.name, '') AS name_recommended_list,
        ct.code AS code_category_tasks,
        ip.staff AS employee_id,
        CONCAT(st.firstname,' ',st.lastname) AS fullname_employee
    ");
        $this->db->from('tblinternal_proposal AS ip');
        $this->db->group_start();
        $this->db->where('ip.id_purchases >', 0);
        $this->db->or_where('ip.id_purchases', -1);
        $this->db->group_end();
        $this->db->where('ip.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('ip.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->join('tbl_recommended_list AS rl', 'rl.id = ip.recommended_list_id', 'left');
        $this->db->join('tblcategory_tasks AS ct', 'ct.id = ip.category_tasks', 'left');
        $this->db->join('tblstaff AS st', 'st.staffid = ip.staff', 'left');
        $proposals = $this->db->get()->result_array();

        if (empty($proposals)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'stats' => [],
                'purchase_order' => [],
                'changed_id' => null
            ]);
            return;
        }

        $ids = array_column($proposals, 'id');

        // 2) Lấy toàn bộ process cho các phiếu trên (chỉ cần những trường tối thiểu)
        $this->db->select("
        p.id,
        p.id_internal_proposal,
        p.id_process,
        p.status,
        p.bod,
        p.name AS name_process,
        p.staff_id
    ");
        $this->db->from('tbl_internal_proposal_process AS p');
        $this->db->where_in('p.id_internal_proposal', $ids);
        $this->db->order_by('p.id_internal_proposal ASC, p.id_process ASC');
        $processRows = $this->db->get()->result_array();

        // 3) Prefetch map nhân sự theo vai trò (lấy 1 người "đầu tiên" bằng MIN(id_staff))
        $pickFirstByProposal = function ($table, $fieldStaff = 'id_staff') use ($ids) {
            if (empty($ids)) {
                return [];
            }
            $this->db->select("MIN(t.$fieldStaff) AS staff_id, t.id_internal_proposal", false);
            $this->db->from("$table AS t");
            $this->db->where_in('t.id_internal_proposal', $ids);
            $this->db->group_by('t.id_internal_proposal');
            $rows = $this->db->get()->result_array();
            $out = [];
            foreach ($rows as $r) {
                $pid = (int)$r['id_internal_proposal'];
                if (!empty($r['staff_id'])) {
                    $out[$pid] = (int)$r['staff_id'];
                }
            }
            return $out;
        };

        $bod1Map = $pickFirstByProposal('tblinternal_proposal_staff_pod');   // bod=1
        $bod2Map = $pickFirstByProposal('tblinternal_proposal_assigned');    // bod=2

        // 4) Helper: name & avatar URL (small_)
        $getName = function ($staffId) {
            return $staffId ? get_staff_full_name($staffId) : '';
        };
        $getAvatarUrl = function ($staffId) {
            if (empty($staffId)) {
                return '';
            }
            $this->db->select('profile_image');
            $this->db->from('tblstaff');
            $this->db->where('staffid', $staffId);
            $row = $this->db->get()->row_array();
            if (!empty($row['profile_image'])) {
                return base_url('uploads/staff_profile_images/' . intval($staffId) . '/small_' . $row['profile_image']);
            }
            return '';
        };
        $normalizeTitle = function ($name) {
            return trim(preg_replace('/^\s*\d+[\.\)]\s*/', '', (string)$name));
        };

        // 5) Group process theo phiếu
        $byProposal = [];
        foreach ($processRows as $r) {
            $byProposal[$r['id_internal_proposal']][] = $r;
        }

        // 6) Build progress (chỉ bod 1 & 2) + status_color (green/yellow)
        foreach ($proposals as $k => $prop) {
            $list = $byProposal[$prop['id']] ?? [];
            // Lọc chỉ các bước bod 1 & 2 giữ nguyên thứ tự id_process
            $steps = array_values(array_filter($list, function ($x) {
                return in_array((int)$x['bod'], [1, 2], true);
            }));

            $progress = [];
            $lastDoneIdx = -1;
            $hasPending = false;

            for ($i = 0, $n = count($steps); $i < $n; $i++) {
                $st = (int)$steps[$i]['status'];
                if ($st === 1) {
                    $lastDoneIdx = $i;
                }
                if ($st === 0 || $st === 2) {
                    $hasPending = true;
                }
            }

            // Helper: pick user theo bod (1: staff_pod, 2: assigned), fallback staff_id của process
            $resolveUser = function ($proposalId, $bod, $fallbackStaffId = null) use (
                $bod1Map,
                $bod2Map,
                $getName,
                $getAvatarUrl
            ) {
                $staffId = null;
                if ($bod === 1) {
                    $staffId = $bod1Map[$proposalId] ?? null;
                }
                if ($bod === 2) {
                    $staffId = $bod2Map[$proposalId] ?? null;
                }
                if (!$staffId && $fallbackStaffId) {
                    $staffId = (int)$fallbackStaffId;
                }

                return [
                    'name' => $getName($staffId),
                    'avatar_url' => $getAvatarUrl($staffId),
                ];
            };

            // Bước "đã duyệt" gần nhất (done_puwa)
            if ($lastDoneIdx >= 0) {
                $v = $steps[$lastDoneIdx];
                $user = $resolveUser($prop['id'], (int)$v['bod'], $v['staff_id']);
                $progress[] = [
                    'bod' => (int)$v['bod'],
                    'title' => $normalizeTitle($v['name_process']),
                    'user' => $user['name'],
                    'status' => 'done_puwa',
                    'avatar_url' => $user['avatar_url'],
                ];
            }

            // Bước kế tiếp đang chờ (pending_puwa)
            $nextIdx = $lastDoneIdx + 1;
            if ($nextIdx >= 0 && $nextIdx < count($steps) && (int)$steps[$nextIdx]['status'] === 0) {
                $v = $steps[$nextIdx];
                $user = $resolveUser($prop['id'], (int)$v['bod'], $v['staff_id']);
                $progress[] = [
                    'bod' => (int)$v['bod'],
                    'title' => $normalizeTitle($v['name_process']),
                    'user' => $user['name'],
                    'status' => 'pending_puwa',
                    'avatar_url' => $user['avatar_url'],
                ];
            }

            // Status chung: tất cả bod 1&2 đều done => green, ngược lại yellow
            $status_finish = $hasPending ? 0 : 1;
            $status_color = $status_finish ? 'green_puwa' : 'yellow_puwa';

            $proposals[$k]['progress'] = $progress;
            $proposals[$k]['status_finish'] = $status_finish;
            $proposals[$k]['status_color'] = $status_color;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => [],
            'purchase_order' => array_map([$this, '_api_rowwarehouse_import'], $proposals),
            'changed_id' => null
        ]);
    }

    private function _api_rowwarehouse_import($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _d($r['date']),
            'status_finish' => ($r['status_finish']),
            'name_recommended_list' => ($r['name_recommended_list']),
            'code' => $r['code'],
            'money' => formatMoney($r['money']),
            'progress' => $r['progress'],
            'code_category_tasks' => $r['code_category_tasks'],
            'status_color' => $r['status_color'],
            'fullname_employee' => (int)$r['fullname_employee'],
            'image_employee' => staff_profile_image($r['employee_id'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee'] . '</span>'
        ];
    }

    public function countpurchase()
    {
        $this->db->select("
            SUM(CASE WHEN SUBSTRING(tblpurchase_order.cancel, 1, 5) = '1foso' THEN 1 ELSE 0 END) AS count_purchase_order_import,
            SUM(CASE WHEN SUBSTRING(tblpurchase_order.cancel, 1, 5) != '1foso' THEN 1 ELSE 0 END) AS count_purchase_order_not_import
        ");
        $this->db->where('tblpurchase_order.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tblpurchase_order.check_update_dashboard', get_option('check_update_dashboard'));
        $data = $this->db->get('tblpurchase_order')->row_array();
        return [
            'count_purchase_order_import' => (int)$data['count_purchase_order_import'],
            'count_purchase_order_not_import' => (int)$data['count_purchase_order_not_import'],
        ];
    }

    function countwarehouse()
    {
        $countWarehouseImport = $this->countWarehouseImport();
        $stats['status_finish_1'] = $countWarehouseImport['status_finish_1'];
        $stats['status_finish_0'] = $countWarehouseImport['status_finish_0'];

        $countpurchase = $this->countpurchase();
        $stats['count_purchase_order_import'] = $countpurchase['count_purchase_order_import'];
        $stats['count_purchase_order_not_import'] = $countpurchase['count_purchase_order_not_import'];

        $countBusinessPlan = $this->countBusinessPlan();
        $stats['total_bp'] = $countBusinessPlan['total_bp'] - $countBusinessPlan['total_pp'];
        $stats['total_pp'] = $countBusinessPlan['total_pp'];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
    }

    private function _api_row_warehouse_import($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _d($r['date']),
            'code' => ($r['code']),
            'code_internal_proposal' => ($r['code_internal_proposal']),
            'money' => formatMoney($r['totalAll_suppliers']),
            'progress' => $r['progress'],
            'status_color' => $r['status_color'],
            'fullname_employee' => (int)$r['fullname_employee'],
            'image_employee' => staff_profile_image($r['employee_id'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee'] . '</span>',
            'fullname_employee_dx' => (int)$r['fullname_employee_dx'],
            'image_employee_dx' => staff_profile_image($r['staff'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee_dx'] . '</span>'
        ];
    }

    function updatadashwarehouseimport()
    {
        $this->db->select("
        tblpurchase_order.id
        ");
        $this->db->where(
            'tblpurchase_order.date >=',
            get_option('date_dashboard_srceen_warehouse_import') ? get_option('date_dashboard_srceen_warehouse_import') : date('Y-m-d')
        );
        $this->db->group_start();
        $this->db->where('tblpurchase_order.date_dashboard_srceen IS NULL');
        $this->db->or_where('tblpurchase_order.date_dashboard_srceen !=', date('Y-m-d'));
        $this->db->or_where('tblpurchase_order.check_update_dashboard !=', get_option('check_update_dashboard'));
        $this->db->group_end();
        $this->db->where('SUBSTRING(tblpurchase_order.cancel, 1, 5) != "1foso"', false, false);
        $purchase_order = $this->db->get('tblpurchase_order')->result_array();
        foreach ($purchase_order as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tblpurchase_order', [
                'date_dashboard_srceen' => date('Y-m-d'),
                'check_update_dashboard' => get_option('check_update_dashboard')
            ]);
        }
    }

    public function updateWarehousePurchases()
    {
        $this->updatadashwarehouseimport();
        $query = '(SELECT COALESCE(SUM(tblimport_items.quantity_stock),0) as quantity_stock,id_purchase_order_items FROM tblimport_items GROUP BY id_purchase_order_items) import_items';
        $this->db->select("
        tblpurchase_order.id,
        tblpurchase_order.date,
        CONCAT(tblpurchase_order.prefix,'-', tblpurchase_order.code) as code,
        tbl_materials.code as code_items,
        tbl_materials.name as name_items,
        tblpurchase_order_items.type as type,
        tblpurchase_order_items.product_id as product_id,
        tblpurchase_order.plan_id as plan_id,
        tblpurchase_order.id as id_purchase_order_items,
        import_items.quantity_stock as quantity_stock_import,
        tblpurchase_order_items.quantity_unit as quantity_unit,
        tblpurchase_order_items.quantity_stock as quantity_stock,
        tblpurchase_order_items.quantity_payment as quantity_payment,
        tblpurchase_order_items.id as idd,
        tblunits.unit as unit,
        payment_unit.unit as unit_name_payment,
        stock_unit.unit as unit_name_stock,
        ");

        $this->db->where('tblpurchase_order.date_dashboard_srceen', date('Y-m-d'));
        $this->db->where('tblpurchase_order.check_update_dashboard', get_option('check_update_dashboard'));
        $this->db->group_start();
        $this->db->where('import_items.quantity_stock IS NULL', null, false);
        $this->db->or_where('tblpurchase_order_items.quantity_stock > import_items.quantity_stock', null, false);
        $this->db->group_end();
        $this->db->join(
            'tblpurchase_order',
            'tblpurchase_order.id = tblpurchase_order_items.id_purchase_order',
            'left'
        );
        $this->db->join('tbl_materials', 'tbl_materials.id = tblpurchase_order_items.product_id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid=tbl_materials.unit_id', 'left');
        $this->db->join('tblunits payment_unit', 'payment_unit.unitid=tbl_materials.unit_payment', 'left');
        $this->db->join('tblunits stock_unit', 'stock_unit.unitid=tbl_materials.standard_unit', 'left');
        $this->db->join($query, 'import_items.id_purchase_order_items = tblpurchase_order_items.id', 'left', false);

        $purchase_order = $this->db->get('tblpurchase_order_items')->result_array();
        $stats = [];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'purchase_order' => array_map([$this, '_api_row_warehousepurchaseOrder'], $purchase_order),
            'changed_id' => null
        ]);
    }

    private function _api_row_warehousepurchaseOrder($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'idd' => $r['idd'],
            'date' => _d($r['date']),
            'code' => ($r['code']),
            'quantity_stock_import' => formatNumber($r['quantity_stock_import']),
            'quantity_unit' => formatNumber($r['quantity_unit']),
            'quantity_stock' => formatNumber($r['quantity_stock']),
            'quantity_payment' => formatNumber($r['quantity_payment']),
            'quantity_stock_left' => formatNumber($r['quantity_stock'] - $r['quantity_stock_import']),
            'code_items' => $r['code_items'],
            'name_items' => $r['name_items'],
            'unit' => $r['unit'],
            'unit_name_payment' => $r['unit_name_payment'],
            'unit_name_stock' => $r['unit_name_stock'],
            'statusClass' => $r['quantity_stock_import'] == 0 ? 'red_purchaseorder' : 'yellow_purchaseorder'
        ];
    }

    function countBusinessPlan()
    {
        // 1) Tổng từ business_plan (không dính nhân bản)
        $q_bp = $this->db->select('SUM(bp.total_quantity) AS total_bp', false)
            ->from('tbl_business_plan bp')
            ->where('bp.date >', '2025-09-29 00:00:00')
            ->get();
        $total_bp = (float)($q_bp->row()->total_bp ?? 0);

        // 2) Tổng từ purchase_products, lọc theo business_plan qua pod, KHỬ NHÂN BẢN bằng GROUP trước
        $q_pp = $this->db->select('SUM(pp.total_quantity) AS total_pp', false)
            ->from('tbl_business_plan bp')
            ->join(
                'tbl_productions_orders_details pod',
                'pod.object_id = bp.id AND pod.object_type = "business_plan"',
                'inner'
            )
            ->join(
                'tbl_purchase_products pp',
                'pp.productions_orders_details_id = pod.id and pp.final_stage = 1',
                'inner'
            )
            ->where('bp.date >', '2025-09-29 00:00:00')
            // nếu 1 pp có thể nhân bản do JOIN khác, thì group trước rồi sum ngoài:
            ->group_by('pp.id') // đảm bảo mỗi pp chỉ tính 1 lần
            ->get();
        // khi có group_by, CI trả nhiều dòng → cộng lại:
        $total_pp = 0;
        foreach ($q_pp->result() as $r) {
            $total_pp += (float)$r->total_pp;
        }

        $result = [
            'total_bp' => $total_bp,
            'total_pp' => $total_pp,
            'grand_total' => $total_bp + $total_pp,
        ];
        return $result;
    }

    function updatefinancialAccountingncc_contract()
    {
        $this->db->select("
            tbl_contracts_supplier.id,
            tbl_contracts_supplier.code,
            tbl_contracts_supplier.date_start,
            tbl_contracts_supplier.date_end,
            tblsuppliers.company as company
        ");
        $this->db->where('tbl_contracts_supplier.date_end !=', null);
        $this->db->where('tbl_contracts_supplier.date_end <', date('Y-m-d'));
        $this->db->join('tblsuppliers', 'tblsuppliers.id=tbl_contracts_supplier.supplier_id', 'left');
        // Lấy hợp đồng có id lớn nhất cho mỗi supplier
        $this->db->where('tbl_contracts_supplier.id IN (
            SELECT MAX(id) FROM tbl_contracts_supplier
            WHERE date_end IS NOT NULL AND date_end < "' . date('Y-m-d') . '"
            GROUP BY supplier_id
        )', null, false);

        $contracts_supplier = $this->db->get('tbl_contracts_supplier')->result_array();
        $stats = [];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'contracts_supplier' => array_map([$this, '_api_row_ncc_contract'], $contracts_supplier),
            'changed_id' => null
        ]);
    }

    function updatefinancialAccountingclient_contract()
    {
        $this->db->select("
            tbl_contracts_sales.id,
            CONCAT(tbl_contracts_sales.prefix,'', tbl_contracts_sales.code) as code,
            tbl_contracts_sales.date_start,
            tbl_contracts_sales.date_end,
            tblclients.company_short as company
        ");
        $this->db->where('tbl_contracts_sales.date_end !=', null);
        $this->db->where('tbl_contracts_sales.date_end <', date('Y-m-d'));
        $this->db->join('tblclients', 'tblclients.userid=tbl_contracts_sales.customer_id', 'left');
        // Lấy hợp đồng có id lớn nhất cho mỗi customer
        $this->db->where('tbl_contracts_sales.id IN (
            SELECT MAX(id) FROM tbl_contracts_sales
            WHERE date_end IS NOT NULL AND date_end < "' . date('Y-m-d') . '"
            GROUP BY customer_id
        )', null, false);
        $this->db->order_by('tbl_contracts_sales.date_end ASC');
        $contracts_clients = $this->db->get('tbl_contracts_sales')->result_array();
        $stats = [];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'contracts_clients' => array_map([$this, '_api_row_ncc_contract'], $contracts_clients),
            'changed_id' => null
        ]);
    }

    private function _api_row_ncc_contract($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date_start' => _d($r['date_start']),
            'date_end' => _d($r['date_end']),
            'code' => ($r['code']),
            'company' => ($r['company'])
        ];
    }

    function updatadashwarehouseimportDXNB()
    {
        $this->db->select("
        tblinternal_proposal.id
        ");
        $this->db->where(
            'tblinternal_proposal.date >=',
            get_option('date_dashboard_srceen_purchases_internal') ? get_option('date_dashboard_srceen_purchases_internal') : date('Y-m-d')
        );
        $this->db->group_start();
        $this->db->where('tblinternal_proposal.date_dashboard_srceen_purchase IS NULL');
        $this->db->or_where('tblinternal_proposal.date_dashboard_srceen_purchase !=', date('Y-m-d'));
        $this->db->or_where(
            'tblinternal_proposal.check_update_dashboard_purchase !=',
            get_option('check_update_dashboard')
        );
        $this->db->group_end();
        $this->db->where('SUBSTRING(tblpurchase_order.cancel, 1, 5) != "1foso"', false, false);
        $this->db->join(
            'tblpurchase_order',
            'tblpurchase_order.id_internal_proposal = tblinternal_proposal.id',
            'inner'
        );
        $purchase_order = $this->db->get('tblinternal_proposal')->result_array();
        foreach ($purchase_order as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tblinternal_proposal', [
                'date_dashboard_srceen_purchase' => date('Y-m-d'),
                'check_update_dashboard_purchase' => get_option('check_update_dashboard')
            ]);
        }
    }

    public function updateWarehousePurchasesDXNB()
    {
        $this->updatadashwarehouseimportDXNB();
        $query = '(SELECT COALESCE(SUM(tblimport_items.quantity_stock),0) as quantity_stock,id_purchase_order_items FROM tblimport_items GROUP BY id_purchase_order_items) import_items';
        $this->db->select("
        tblpurchase_order.id,
        tblpurchase_order.date,
        CONCAT(tblpurchase_order.prefix,'-', tblpurchase_order.code) as code,
        tblinternal_proposal.code as code_internal_proposal,
        tbl_materials.code as code_items,
        tbl_materials.name as name_items,
        tblpurchase_order_items.type as type,
        tblpurchase_order_items.product_id as product_id,
        tblpurchase_order.plan_id as plan_id,
        tblpurchase_order.id as id_purchase_order_items,
        import_items.quantity_stock as quantity_stock_import,
        tblpurchase_order_items.quantity_unit as quantity_unit,
        tblpurchase_order_items.quantity_stock as quantity_stock,
        tblpurchase_order_items.quantity_payment as quantity_payment,
        tblpurchase_order_items.id as idd,
        tblunits.unit as unit,
        payment_unit.unit as unit_name_payment,
        stock_unit.unit as unit_name_stock,
        ");

        $this->db->where('tblinternal_proposal.date_dashboard_srceen_purchase', date('Y-m-d'));
        $this->db->where('tblinternal_proposal.check_update_dashboard_purchase', get_option('check_update_dashboard'));
        $this->db->group_start();
        $this->db->where('import_items.quantity_stock IS NULL', null, false);
        $this->db->or_where('tblpurchase_order_items.quantity_stock > import_items.quantity_stock', null, false);
        $this->db->group_end();
        $this->db->join(
            'tblpurchase_order',
            'tblpurchase_order.id = tblpurchase_order_items.id_purchase_order',
            'left'
        );
        $this->db->join(
            'tblinternal_proposal',
            'tblinternal_proposal.id = tblpurchase_order.id_internal_proposal',
            'inner'
        );
        $this->db->join('tbl_materials', 'tbl_materials.id = tblpurchase_order_items.product_id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid=tbl_materials.unit_id', 'left');
        $this->db->join('tblunits payment_unit', 'payment_unit.unitid=tbl_materials.unit_payment', 'left');
        $this->db->join('tblunits stock_unit', 'stock_unit.unitid=tbl_materials.standard_unit', 'left');
        $this->db->join($query, 'import_items.id_purchase_order_items = tblpurchase_order_items.id', 'left', false);

        $purchase_order = $this->db->get('tblpurchase_order_items')->result_array();
        $stats = [];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'purchase_order_dxnb' => array_map([$this, '_api_row_warehousepurchaseOrderDXNB'], $purchase_order),
            'changed_id' => null
        ]);
    }

    private function _api_row_warehousepurchaseOrderDXNB($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'idd' => $r['idd'],
            'date' => _d($r['date']),
            'code' => ($r['code']),
            'code_internal_proposal' => ($r['code_internal_proposal']),
            'quantity_stock_import' => formatNumber($r['quantity_stock_import']),
            'quantity_unit' => formatNumber($r['quantity_unit']),
            'quantity_stock' => formatNumber($r['quantity_stock']),
            'quantity_payment' => formatNumber($r['quantity_payment']),
            'quantity_stock_left' => formatNumber($r['quantity_stock'] - $r['quantity_stock_import']),
            'code_items' => $r['code_items'],
            'name_items' => $r['name_items'],
            'unit' => $r['unit'],
            'unit_name_payment' => $r['unit_name_payment'],
            'unit_name_stock' => $r['unit_name_stock'],
            'statusClass' => $r['quantity_stock_import'] == 0 ? 'red_dxnb_purchases' : 'yellow_dxnb_purchases'
        ];
    }

    function modal_quotes_no_order($type = 1)
    {
        $this->load->view('admin/dashboard_srceen_office/modal/quotes_no_order', ['type' => $type]);
    }

    function table_quotes_no_order($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tbl_quotes.id as id',
            'tbl_quotes.date as date',
            'tbl_quotes.reference_no as reference_no',
            'tblclients.company as company',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_employee',
        );
        $sIndexColumn = "id";
        $sTable = 'tbl_quotes';
        $where = array(
            'AND tbl_quotes.date >="' . $date_dashboard_srceen_sales . '"'
        );
        if (!empty($type)) {
            if ($type == 1) {
                $where[] = 'AND (tbl_quotes.order_id = 0 OR tbl_quotes.order_id IS NULL)';
            } elseif ($type == 2) {
                $where[] = 'AND tbl_quotes.status = "un_approved"';
            }
        }
        $join = array(
            'LEFT JOIN tblclients ON tblclients.userid = tbl_quotes.customer_id',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_quotes.created_by',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tbl_quotes.created_by as created_by')
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
                if ($aColumns[$i] == 'tbl_quotes.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tbl_quotes.date as date') {
                    $_data = _dt($aRow['date']);
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['created_by'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array()
                    ) . ($aRow['fullname_employee']) . '<br>';
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    function modal_sample_no_order($type = 1)
    {
        $this->load->view('admin/dashboard_srceen_office/modal/sample', ['type' => $type]);
    }

    function table_sample($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tbl_quotes.id as id',
            'tbl_quotes.date as date',
            'tbl_quotes.reference_no as reference_no',
            'tblclients.company as company',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_employee',
        );
        $sIndexColumn = "id";
        $sTable = 'tbl_request_template';
        $where = array(
            'AND tbl_request_template.date >="' . $date_dashboard_srceen_sales . '"'
        );
        if (!empty($type)) {
            if ($type == 1) {
                $where[] = 'AND (tbl_quotes.order_id = 0 OR tbl_quotes.order_id IS NULL)';
            } elseif ($type == 2) {
                $where[] = 'AND tbl_quotes.status = "un_approved"';
            }
        }
        $join = array(
            'LEFT JOIN tbl_quotes ON tbl_quotes.id=tbl_request_template.id_quotes',
            'LEFT JOIN tblclients ON tblclients.userid = tbl_quotes.customer_id',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_quotes.created_by',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tbl_quotes.created_by as created_by')
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
                if ($aColumns[$i] == 'tbl_quotes.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tbl_quotes.date as date') {
                    $_data = _dt($aRow['date']);
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['created_by'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array()
                    ) . ($aRow['fullname_employee']) . '<br>';
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    function order_plan($type = 1)
    {
        $this->load->view('admin/dashboard_srceen_office/modal/order_plan', ['type' => $type]);
    }

    function table_order_plan($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tbl_orders.id as id',
            'tbl_orders.date as date',
            'tbl_orders.reference_no as reference_no',
            'tblclients.company as company',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_employee',
        );
        $sIndexColumn = "id";
        $sTable = 'tbl_orders';
        $where = array(
            'AND tbl_orders.date >="' . $date_dashboard_srceen_sales . '"'
        );
        if (!empty($type)) {
            if ($type == 1) {
                $where[] = 'AND (tbl_orders.count_delivery = 0)';
            } elseif ($type == 2) {
                $where[] = 'AND tbl_orders.status = "un_approved"';
            }
        }
        $join = array(
            'LEFT JOIN tblclients ON tblclients.userid = tbl_orders.customer_id',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_orders.employee_id',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tbl_orders.employee_id as created_by')
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
                if ($aColumns[$i] == 'tbl_orders.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tbl_orders.date as date') {
                    $_data = _dt($aRow['date']);
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['created_by'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array()
                    ) . ($aRow['fullname_employee']) . '<br>';
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    function kh_plan($type = 1)
    {
        $this->load->view('admin/dashboard_srceen_office/modal/kh_plan', ['type' => $type]);
    }

    function table_kh_plan($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tbl_business_plan.id as id',
            'tbl_business_plan.date as date',
            'tbl_business_plan.reference_no as reference_no',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_employee',
        );
        $sIndexColumn = "id";
        $sTable = 'tbl_business_plan';
        $where = array(
            'AND tbl_business_plan.date >="' . $date_dashboard_srceen_sales . '"'
        );
        $where[] = 'AND tbl_business_plan.status = "un_approved"';

        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_business_plan.created_by',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tbl_business_plan.created_by as created_by')
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
                if ($aColumns[$i] == 'tbl_business_plan.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tbl_business_plan.date as date') {
                    $_data = _dt($aRow['date']);
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['created_by'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array()
                    ) . ($aRow['fullname_employee']) . '<br>';
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    function open_production($type = 1)
    {
        $this->load->view('admin/dashboard_srceen_office/modal/open_production', ['type' => $type]);
    }

    function table_open_production($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales')
            ? get_option('date_dashboard_srceen_sales') . ' 00:00:00'
            : date('Y-m-d') . ' 00:00:00';

        // ===== NHÁNH ĐƠN HÀNG: DÙNG EXISTS, BỎ SUM/GROUP BY =====
        $ordersBranch = "
    (SELECT
        o.id AS id,
        o.date AS date,
        o.reference_no AS reference_no,
        c.company AS company,
        CONCAT(s.firstname,' ',s.lastname) AS fullname_employee,
        o.employee_id AS created_by,
        'orders' AS source_type
     FROM tbl_orders o
     LEFT JOIN tblclients c ON c.userid = o.customer_id
     LEFT JOIN tblstaff   s ON s.staffid = o.employee_id
     WHERE o.status = 'approved'
       AND o.is_cancel = 0
       AND o.date >= " . $this->db->escape($date_dashboard_srceen_sales) . "
       /* Có ÍT NHẤT 1 item còn nhu cầu > 0 */
       AND EXISTS (
            SELECT 1
            FROM tbl_order_items oi
            INNER JOIN tbl_products p ON p.id = oi.item_id
            LEFT JOIN (
                SELECT d.order_id_item, SUM(d.quantity_net) AS quantity_keep
                FROM tbltransfer_warehouse_detail d
                WHERE d.tranfer_business_item_id = 0
                GROUP BY d.order_id_item
            ) k ON k.order_id_item = oi.id
            LEFT JOIN (
                SELECT x.order_item_id, SUM(x.quantity) AS quantity_transfer
                FROM tbl_tranfer_business_item x
                GROUP BY x.order_item_id
            ) t ON t.order_item_id = oi.id
            WHERE oi.order_id = o.id
              AND oi.type_item = 'products'
              AND oi.total_quantity_item > 0
              AND (
                    (oi.total_quantity_item * IF(p.unit_id <> oi.unit_id, p.conversion_quantity_unit, 1))
                    - oi.quantity_plan
                    - COALESCE(k.quantity_keep, 0)
                    - COALESCE(t.quantity_transfer, 0)
                  ) > 0
            LIMIT 1
       )
       /* Chưa tạo chi tiết lệnh sản xuất */
       AND NOT EXISTS (
           SELECT 1
           FROM tbl_productions_orders_details pod
           WHERE pod.object_id = o.id AND pod.object_type = 'orders'
           LIMIT 1
       )
    )";

        // ===== NHÁNH KẾ HOẠCH KD: DÙNG EXISTS, BỎ SUM/GROUP BY =====
        $bpBranch = "
    (SELECT
        bp.id AS id,
        bp.date AS date,
        bp.reference_no AS reference_no,
        '' AS company,
        '' AS fullname_employee,
        0 AS created_by,
        'business' AS source_type
     FROM tbl_business_plan bp
     WHERE bp.status = 'approved'
       AND bp.productions_plan_preventive_id = 0
       AND bp.date >= " . $this->db->escape($date_dashboard_srceen_sales) . "
       /* Có ÍT NHẤT 1 item còn nhu cầu > 0 */
       AND EXISTS (
           SELECT 1
           FROM tbl_business_plan_items bpi
           WHERE bpi.business_plan_id = bp.id
             AND (bpi.quantity - bpi.quantity_plan) > 0
           LIMIT 1
       )
       /* Chưa tạo chi tiết lệnh sản xuất */
       AND NOT EXISTS (
           SELECT 1
           FROM tbl_productions_orders_details pod
           WHERE pod.object_id = bp.id AND pod.object_type = 'business'
           LIMIT 1
       )
    )";

        // UNION
        $unionSQL = "(
        $ordersBranch
        UNION ALL
        $bpBranch
    ) src";

        // DataTables
        $aColumns = array(
            'src.id as id',
            'src.date as date',
            'src.reference_no as reference_no',
            'src.company as company',
            'src.fullname_employee as fullname_employee',
            // nếu cần hiển thị nguồn:
            // 'src.source_type as source_type',
        );
        $sIndexColumn = "id";
        $sTable = $unionSQL;

        // Không cần where/join ngoài; đã lọc đủ
        $where = array();
        $join = array();

        // GỢI Ý: ép order theo cột có index để LIMIT hiệu quả
        $additionalOrder = "";

        // TẮT SQL_CALC_FOUND_ROWS nếu data_tables_init có hỗ trợ
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('src.created_by as created_by', 'src.source_type as source_type'),
            $additionalOrder,
            [],
            ['union_all' => true, 'no_found_rows' => true] // tuỳ implement của bạn
        );

        $output = $result['output'];
        $rResult = $result['rResult'];
        $iDisplayStart = (int)$this->input->post('iDisplayStart');

        foreach ($rResult as $aRow) {
            $row = array();

            foreach ($aColumns as $col) {
                if (strpos($col, ' as ') !== false && !isset($aRow[$col])) {
                    $_data = $aRow[strafter($col, 'as ')];
                } else {
                    $_data = $aRow[$col];
                }

                if ($col === 'src.id as id') {
                    $_data = ++$iDisplayStart; // STT
                }

                if ($col === 'src.date as date') {
                    $_data = _dt($aRow['date']);
                }

                if ($col === 'src.fullname_employee as fullname_employee') {
                    if ($aRow['created_by']) {
                        $_data = staff_profile_image(
                            $aRow['created_by'],
                            array('staff-profile-image-small mright5'),
                            'small',
                            array()
                        ) . ($aRow['fullname_employee'] ?? '');
                    } else {
                        $_data = '';
                    }
                }

                $row[] = $_data;
            }

            // Nếu muốn hiển thị type
            // $row[] = $aRow['source_type'] === 'orders'
            //     ? '<span class="badge bg-info">Đơn hàng</span>'
            //     : '<span class="badge bg-warning">Kế hoạch KD</span>';

            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    function export_khuonbe($type = 1)
    {
        $this->load->view('admin/dashboard_srceen_office/modal/export_khuonbe', ['type' => $type]);
    }

    function table_export_khuonbe($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales')
            ? get_option('date_dashboard_srceen_sales') . ' 00:00:00'
            : date('Y-m-d') . ' 00:00:00';

        // 1) KHÔNG alias trong $sTable
        $sTable = 'tbl_productions_orders';
        $sIndexColumn = 'id';
        $aColumns = [
            'tbl_productions_orders.id',
            'tbl_productions_orders.date',
            'tbl_productions_orders.reference_no',
        ];
        $join = [];
        $where = [];
        $where_type = '';
        $ed = '';
        $type == 1 ? $where_type = ' s.is_be = 1 ' : $where_type = ' s.is_ghikem = 1 ';
        $type == 1 ? $ed = ' ed.type_po = 1 ' : $ed = ' ed.type_po = 2 ';
        // ❌ BỎ DÒNG NÀY (đang gây lệch) :
        // $where[] = 'AND tbl_productions_orders.date >= "'.$date_dashboard_srceen_sales.'"';

        // ✅ Giữ lọc theo pod.date_created trong EXISTS (BE)
        $where[] = 'AND EXISTS (
                        SELECT 1
                        FROM tbl_productions_orders_details AS pod
                        INNER JOIN tbl_productions_orders_items_stages AS pois
                            ON pois.productions_orders_items_id = pod.productions_orders_item_id
                        INNER JOIN tbl_stages AS s ON s.id = pois.stage_id
                        INNER JOIN tbl_category_stages AS cs ON cs.id = s.category_stages
                        WHERE pod.productions_orders_id = tbl_productions_orders.id
                        AND cs.type_use = 0
                        AND ' . $where_type . ' 
                        AND pod.date_created >= "' . $date_dashboard_srceen_sales . '"
                    )';

        // ✅ Chưa xuất (chỉnh đúng tên bảng của bạn nếu khác)
        $where[] = 'AND NOT EXISTS (
                SELECT 1
                FROM tbltblexport_different_items AS edi
                INNER JOIN tblexport_different AS ed
                    ON ed.id = edi.id_export_different AND ' . $ed . '
                WHERE edi.po_id = tbl_productions_orders.id
            )';

        $additionalSelect = [];

        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            $additionalSelect,
            'GROUP BY tbl_productions_orders.id'
        );

        $output = $result['output'];
        $rResult = $result['rResult'];

        $iDisplayStart = (int)$this->input->post('iDisplayStart');

        foreach ($rResult as $aRow) {
            $row = [];
            // STT
            $row[] = ++$iDisplayStart;

            // Lấy theo alias đã add
            $row[] = _dt($aRow['tbl_productions_orders.date']);
            $row[] = $aRow['tbl_productions_orders.reference_no'];

            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    function modal_mo_lenh($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = 'Tổng lệnh chưa tới bước ghép size';
        }
        if ($type == 2) {
            $title = 'Tổng lệnh chưa tới bước dàn trang';
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_mo_lenh', ['type' => $type, 'title' => $title]);
    }

    function table_mo_lenh($type = 1)
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
        $where[] = 'AND tbl_category_stages.type_use = 0';
        if ($type == 1) {
            $where[] = 'AND tbl_stages.is_ghepsize = 1';
        } elseif ($type == 2) {
            $where[] = 'AND tbl_stages.is_dantrang = 1';
        } elseif ($type == 2) {
            $where[] = 'AND tbl_stages.is_ghikem = 1';
        }
        $where[] = 'AND tbl_productions_orders_items_stages.active = 0';

        $join = array(
            'LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id',
            'LEFT JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id',
            'LEFT JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id',
            'LEFT JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages',
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

    function export_npl_tp($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = 'Tổng phiếu xuất chưa duyệt NPL';
        }
        if ($type == 2) {
            $title = 'Tổng phiếu xuất chưa duyệt TP';
        }
        $this->load->view('admin/dashboard_srceen_office/modal/export_npl_tp', ['type' => $type, 'title' => $title]);
    }

    function table_export_npl_tp($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $query = '(
            (SELECT 
                tbltransfer_warehouse.id as id,
                tbltransfer_warehouse.date as date,
                CONCAT(tbltransfer_warehouse.prefix,"-",tbltransfer_warehouse.code) as reference_no,
                tbltransfer_warehouse.staff_id as staff_id
            FROM tbltransfer_warehouse
            LEFT JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse.date >= "' . $date_dashboard_srceen_sales . '"
            AND tbltransfer_warehouse.warehouseman_id = 0
            AND tbltransfer_warehouse_detail.type = "' . ($type == 1 ? 'nvl' : 'product') . '"
            GROUP BY tbltransfer_warehouse.id
            )
            UNION ALL 
            (SELECT 
                tblexport_different.id as id,
                tblexport_different.date as date,
                CONCAT(tblexport_different.prefix,"-",tblexport_different.code) as reference_no,
                tblexport_different.staff_id as staff_id
            FROM tblexport_different
            LEFT JOIN tbltblexport_different_items ON tbltblexport_different_items.id_export_different = tblexport_different.id
            WHERE tblexport_different.date >= "' . $date_dashboard_srceen_sales . '"
            AND tblexport_different.warehouseman_id = 0
            AND tbltblexport_different_items.type =  "' . ($type == 1 ? 'nvl' : 'product') . '"
            GROUP BY tblexport_different.id
            )
        ) src
        ';
        $aColumns = array(
            'src.id as id',
            'src.date as date',
            'src.reference_no as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee'
        );
        $sIndexColumn = "id";
        $sTable = $query;
        $where = array();
        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = src.staff_id',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('src.staff_id'),
            '',
            [],
            ['union_all' => true]
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
                if ($aColumns[$i] == 'src.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'src.date as date') {
                    $_data = _dt($aRow['date']);
                }
                if ($aColumns[$i] == 'src.reference_no as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['staff_id'],
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

    function purchases_dxnb($type = 1)
    {
        $title = 'Tổng phiếu đề xuất chưa duyệt';
        $this->load->view('admin/dashboard_srceen_office/modal/purchases_dxnb', ['type' => $type, 'title' => $title]);
    }

    function table_purchases_dxnb($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tblinternal_proposal.id as id',
            'tblinternal_proposal.date as date',
            'tblinternal_proposal.code as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee',
            '"" as qt'
        );
        $sIndexColumn = "id";
        $sTable = 'tblinternal_proposal';
        $where = array(
            'AND tblinternal_proposal.date >="' . $date_dashboard_srceen_sales . '"'
        );
        $where[] = 'AND EXISTS (SELECT 1 FROM tblinternal_proposal_purchase WHERE tblinternal_proposal_purchase.id_internal_proposal = tblinternal_proposal.id)';
        $where[] = 'AND (tbl_internal_proposal_process.status = 0 OR tbl_internal_proposal_process.id is NULL) ';
        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tblinternal_proposal.staff',
            'LEFT JOIN tbl_internal_proposal_process ON tbl_internal_proposal_process.id_internal_proposal=tblinternal_proposal.id AND tbl_internal_proposal_process.bod = 1',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tblinternal_proposal.staff,manager_id,auditor_id,staff_controller_completes,staff_auditor_completes'),
            ' GROUP BY tblinternal_proposal.id'
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
                if ($aColumns[$i] == 'tblinternal_proposal.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tblinternal_proposal.date as date') {
                    $_data = _dt($aRow['date']);
                }
                if ($aColumns[$i] == 'tblinternal_proposal.code as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['staff'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array()
                    ) . ($aRow['fullname_employee'] ?? '');
                }
                if ($aColumns[$i] == '"" as qt') {
                    $this->db->select('tbl_internal_proposal_process.*,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tblstaff.staffid as staff,tbl_internal_proposal_process_child.id as childs,tbl_internal_proposal_process.bod,tbl_internal_proposal_process.name as name_process,tblstaff.profile_image as avatar_url');
                    $this->db->where('tbl_internal_proposal_process.id_internal_proposal', $aRow['id']);
                    $this->db->join(
                        'tbl_internal_proposal_process_child',
                        'tbl_internal_proposal_process_child.recommended_list_id = tbl_internal_proposal_process.id_process AND tbl_internal_proposal_process_child.id_internal_proposal = tbl_internal_proposal_process.id_internal_proposal',
                        'left'
                    );
                    $this->db->join('tblstaff', 'tblstaff.staffid=tbl_internal_proposal_process.staff_id', 'left');
                    $this->db->order_by('tbl_internal_proposal_process.id_process asc');
                    $this->db->group_by('tbl_internal_proposal_process.id');
                    $data_checklist_items = $this->db->get('tbl_internal_proposal_process')->result_array();
                    $internal_proposal['progress'] = [];
                    $status = 0;
                    $quahan = 0;
                    $status_finish = 1;
                    $assigned_process = '';
                    $bod_process = '';
                    $monitor_process = '';
                    $head_of_department_process = '';
                    $manager_process = '';
                    $auditor_process = '';
                    $staff_controller_completes = '';
                    $staff_auditor_completes = '';
                    $staff = '';
                    if (!empty($aRow['manager_id'])) {
                        if ($manager_process == '') {
                            $manager_process = $aRow['manager_id'];
                        }
                    }
                    if (!empty($aRow['auditor_id'])) {
                        if ($auditor_process == '') {
                            $auditor_process = $aRow['auditor_id'];
                        }
                    }
                    if (!empty($aRow['staff_controller_completes'])) {
                        if ($staff_controller_completes == '') {
                            $staff_controller_completes = $aRow['staff_controller_completes'];
                        }
                    }
                    if (!empty($aRow['staff_auditor_completes'])) {
                        if ($staff_auditor_completes == '') {
                            $staff_auditor_completes = $aRow['staff_auditor_completes'];
                        }
                    }
                    if (!empty($aRow['staff'])) {
                        if ($staff == '') {
                            $staff = $aRow['staff'];
                        }
                    }
                    $this->db->where('id_internal_proposal', $aRow['id']);
                    $assigned_pod = $this->db->get('tblinternal_proposal_staff_pod')->row_array();
                    if (!empty($assigned_pod)) {
                        $bod_process = $assigned_pod['id_staff'];
                    }
                    $this->db->where('id_internal_proposal', $aRow['id']);
                    $assigned = $this->db->get('tblinternal_proposal_assigned')->row_array();
                    if (!empty($assigned)) {
                        $assigned_process = $assigned['id_staff'];
                    }
                    foreach ($data_checklist_items as $k => $v) {
                        // Chỉ lấy process cuối cùng có status = 1 (nếu có nhiều cái status = 1 thì chỉ lấy cái cuối cùng)
                        if ($v['status'] == 1) {
                            // Chỉ lấy process cuối cùng có status = 1
                            $isLastStatus1 = true;
                            for ($j = $k + 1; $j < count($data_checklist_items); $j++) {
                                if ($data_checklist_items[$j]['status'] == 1) {
                                    $isLastStatus1 = false;
                                    break;
                                }
                            }
                            if ($isLastStatus1) {
                                // Lấy thông tin user/avatar theo mẫu bạn gửi
                                $user = '';
                                $avatar_url = '';
                                if (!empty($v['staff_id'])) {
                                    $FullName = get_staff_full_name($v['staff_id']);
                                    $user = $FullName;
                                    // $avatar_url = (!empty($v['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $v['staff'] . '/small_' . $v['avatar_url']) : '';
                                    $avatar_url = staff_profile_image(
                                        $v['staff'],
                                        array('staff-profile-image-small mright5'),
                                        'small',
                                        array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                                    );
                                } else {
                                    $user = get_staff_full_name($v['staff_id']);
                                    $avatar_url = ''(!empty($v['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $v['staff'] . '/small_' . $v['avatar_url']) : '';
                                    $avatar_url = '<img src="' . $avatar_url . '" class="avatar-sm_dxnt" alt="avatar">';
                                }
                                $internal_proposal['progress'][] = [
                                    'bod' => $v['bod'],
                                    'title' => preg_replace('/^(2\.|3\.)\s*/', '', $v['name_process']),
                                    'user' => $user,
                                    'status' => 'done_puwa',
                                    'avatar_url' => $avatar_url,
                                ];
                            }
                        } elseif ($v['status'] == 0) {
                            // Tìm process tiếp theo sau cái cuối cùng status=1
                            $lastDoneIndex = -1;
                            for ($j = $k - 1; $j >= 0; $j--) {
                                if ($data_checklist_items[$j]['status'] == 1) {
                                    $lastDoneIndex = $j;
                                    break;
                                }
                            }
                            if ($lastDoneIndex >= 0 && $k == $lastDoneIndex + 1) {
                                $user = '';
                                $avatar_url = '';
                                if ($staff == '') {
                                    $staff = 'Người lập đề xuất';
                                }
                                if ($bod_process == '') {
                                    $bod_process = 'Người duyệt thực thi';
                                }
                                if ($assigned_process == '') {
                                    $assigned_process = 'Người duyệt đề xuất';
                                }
                                if ($head_of_department_process == '') {
                                    $head_of_department_process = 'Người Hoàn Thành 2';
                                }
                                if ($monitor_process == '') {
                                    $monitor_process = 'Người kiểm toán hoàn thành';
                                }
                                if ($manager_process == '') {
                                    $manager_process = 'Người hoàn thành 2';
                                }
                                if ($auditor_process == '') {
                                    $auditor_process = 'Người kiểm soát hoàn thành';
                                }
                                if ($staff_controller_completes == '') {
                                    $staff_controller_completes = 'Người kiểm soát hoàn thành';
                                }
                                if ($staff_auditor_completes == '') {
                                    $staff_auditor_completes = 'Người kiểm toán hoàn thành';
                                }

                                $staffs = '';
                                if ($v['bod'] == 1) {
                                    $staffs = $bod_process;
                                }
                                if ($v['bod'] == 5) {
                                    $staffs = $staff;
                                }
                                if ($v['bod'] == 2) {
                                    $staffs = $assigned_process;
                                }
                                if ($v['bod'] == 3) {
                                    $staffs = $head_of_department_process;
                                }
                                if ($v['bod'] == 4) {
                                    $staffs = $auditor_process;
                                }
                                if ($v['bod'] == 6) {
                                    $staffs = $manager_process;
                                }
                                if ($v['bod'] == 7) {
                                    $staffs = $monitor_process;
                                }
                                if ($v['bod'] == 8) {
                                    $staffs = $staff_controller_completes;
                                }
                                if ($v['bod'] == 9) {
                                    $staffs = $staff_auditor_completes;
                                }
                                if (!empty($staffs) && is_numeric($staffs)) {
                                    $FullName = get_staff_full_name($staffs);
                                    $user = $FullName;
                                    $avatar_url = staff_profile_image(
                                        $staffs,
                                        array('staff-profile-image-small mright5'),
                                        'small',
                                        array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                                    );
                                } else {
                                    $user = $staffs;
                                    $avatar_url = (!empty($v['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $v['staff'] . '/small_' . $v['avatar_url']) : '';
                                    $avatar_url = '<img src="' . $avatar_url . '" class="avatar-sm_dxnt" alt="avatar">';
                                }
                                $internal_proposal['progress'][] = [
                                    'bod' => $v['bod'],
                                    'title' => preg_replace('/^(2\.|3\.)\s*/', '', $v['name_process']),
                                    'user' => $user,
                                    'status' => 'pending_puwa',
                                    'avatar_url' => $avatar_url,
                                ];
                            }
                        }
                    }
                    // echo '<pre>';print_arrays($internal_proposal['progress']);die;
                    $_data = '<div style="text-align: left;">' . $this->render_progress_proposal($internal_proposal['progress']) . '</div>';
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    /**
     * Render progress steps as HTML for frontend.
     * @param array $progress
     * @return string
     */
    public function render_progress_proposal($progress)
    {
        if (!is_array($progress) || empty($progress)) {
            return '';
        }
        $html = '';
        foreach ($progress as $idx => $step) {
            $title = htmlspecialchars($step['title'] ?? '', ENT_QUOTES, 'UTF-8');
            $user = htmlspecialchars($step['user'] ?? '', ENT_QUOTES, 'UTF-8');
            $avatarUrl = !empty($step['avatar_url']) ? $step['avatar_url'] : '<img src="' . base_url('assets/images/user-placeholder.jpg') . '" class="avatar-sm_dxnt" alt="avatar">';
            $st = htmlspecialchars($step['status'] ?? '', ENT_QUOTES, 'UTF-8');
            $userStyle = $idx === 1 ? 'style="font-size:14px;"' : 'style="font-size:12px;"';
            $html .= '
                <div class="step_puwa ' . $st . '">
                    <div class="dot_puwa_progress"></div>
                    <div class="content_puwa">
                        <div class="title_puwa" ' . $userStyle . '>' . $title . '</div>
                        <div class="user_puwa" ' . $userStyle . '>
                            ' . $avatarUrl . '
                            ' . $user . '
                        </div>
                    </div>
                </div>';
        }
        return $html;
    }

    function suggest_plan_purchase($type = 1)
    {
        $title = 'Tổng phiếu yêu cầu kế hoạch mua chưa duyệt';
        $this->load->view(
            'admin/dashboard_srceen_office/modal/suggest_plan_purchase',
            ['type' => $type, 'title' => $title]
        );
    }

    function table_suggest_plan_purchase($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tbl_suggest_plan_purchase.id as id',
            'tbl_suggest_plan_purchase.date as date',
            'tbl_suggest_plan_purchase.reference_no as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee'
        );
        $sIndexColumn = "id";
        $sTable = 'tbl_suggest_plan_purchase';
        $where = array(
            'AND tbl_suggest_plan_purchase.date >="' . $date_dashboard_srceen_sales . '"'
        );
        $where[] = 'AND tbl_suggest_plan_purchase.status = 0';

        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_suggest_plan_purchase.created_by',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tbl_suggest_plan_purchase.created_by'),
            ' GROUP BY tbl_suggest_plan_purchase.id'
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
                if ($aColumns[$i] == 'tbl_suggest_plan_purchase.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tbl_suggest_plan_purchase.date as date') {
                    $_data = _dt($aRow['date']);
                }
                if ($aColumns[$i] == 'tbl_suggest_plan_purchase.reference_no as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['created_by'],
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

    function purchase_orders_import($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = 'Tổng phiếu chưa nhập';
        }
        if ($type == 2) {
            $title = 'Tổng phiếu nhập 1 phần';
        }
        $this->load->view(
            'admin/dashboard_srceen_office/modal/purchase_orders_import',
            ['type' => $type, 'title' => $title]
        );
    }

    function table_purchase_orders_import($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tblpurchase_order.id as id',
            'tblpurchase_order.date as date',
            'CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code) as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee'
        );
        $sIndexColumn = "id";
        $sTable = 'tblpurchase_order';
        $where = array(
            'AND tblpurchase_order.date >="' . $date_dashboard_srceen_sales . '"'
        );
        if ($type == 1) {
            $where[] = 'AND NOT EXISTS (
                SELECT 1
                FROM tblimport
                WHERE tblimport.id_order = tblpurchase_order.id
            )';
        }
        if ($type == 2) {
            $where[] = 'AND (SUBSTRING(tblpurchase_order.cancel, 1, 5) != "1foso" AND tblpurchase_order.id IN (select tblimport.id_order from tblimport))';
        }
        $where[] = 'AND tblpurchase_order.is_end = 0';

        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tblpurchase_order.staff_create',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tblpurchase_order.staff_create'),
            ' GROUP BY tblpurchase_order.id'
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
                if ($aColumns[$i] == 'tblpurchase_order.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tblpurchase_order.date as date') {
                    $_data = _d($aRow['date']);
                }
                if ($aColumns[$i] == 'CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code) as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['staff_create'],
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

    function purchase_products_plan_kd($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = 'Tổng phiếu KHKD chưa duyệt';
        }
        if ($type == 2) {
            $title = 'Tổng phiếu KHKD chưa duyệt kho';
        }
        $this->load->view(
            'admin/dashboard_srceen_office/modal/purchase_products_plan_kd',
            ['type' => $type, 'title' => $title]
        );
    }

    function table_purchase_products_plan_kd($type = 1)
    {
        // Mốc ngày lọc
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales')
            ? get_option('date_dashboard_srceen_sales') . ' 00:00:00'
            : date('Y-m-d') . ' 00:00:00';

        // Các cột hiển thị
        $aColumns = array(
            'tbl_business_plan.id as id',
            'tbl_business_plan.date as date',
            'tbl_business_plan.reference_no as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee',
        );

        $sIndexColumn = 'id';
        $sTable = 'tbl_business_plan';

        // WHERE cơ bản
        $where = array();
        $where[] = 'AND tbl_business_plan.date >= "' . $date_dashboard_srceen_sales . '"';

        // Phải có ít nhất một productions_orders_details gắn tới business_plan
        $where[] = 'AND EXISTS (
        SELECT 1
        FROM tbl_productions_orders_details pdx
        WHERE pdx.object_id = tbl_business_plan.id
          AND pdx.object_type = "business_plan"
    )';

        // Lọc theo type: 1 = chưa có import (final_stage=1), 2 = đã có import (final_stage=1)
        if ((int)$type === 1) {
            $where[] = 'AND NOT EXISTS (
            SELECT 1
            FROM tbl_productions_orders_details pd
            JOIN tbl_purchase_products p
              ON p.productions_orders_details_id = pd.id
             AND p.final_stage = 1
            WHERE pd.object_id   = tbl_business_plan.id
              AND pd.object_type = "business_plan"
        )';
        } elseif ((int)$type === 2) {
            $where[] = 'AND EXISTS (
            SELECT 1
            FROM tbl_productions_orders_details pd
            JOIN tbl_purchase_products p
              ON p.productions_orders_details_id = pd.id
             AND p.final_stage = 1
            WHERE pd.object_id   = tbl_business_plan.id
              AND pd.object_type = "business_plan" AND p.warehouseman_id = 0
        )';
        }

        // JOIN
        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_business_plan.created_by',
        );

        // Cột bổ sung để render avatar nhân viên
        $additionalSelect = array(
            'tbl_business_plan.created_by as staff_create',
        );

        // GROUP BY để tránh nhân bản dòng
        $group_by = ' GROUP BY tbl_business_plan.id';

        // Gọi DataTables
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect, $group_by);
        $output = $result['output'];
        $rResult = $result['rResult'];

        // Lấy chỉ số phân trang hiện tại từ DataTables
        $iDisplayStart = (int)$this->input->post('iDisplayStart');

        foreach ($rResult as $aRow) {
            $row = array();

            foreach ($aColumns as $col) {
                // Lấy alias sau " as "
                if (stripos($col, ' as ') !== false) {
                    $alias = trim(substr($col, strrpos($col, ' as ') + 4));
                    $_data = isset($aRow[$alias]) ? $aRow[$alias] : null;
                } else {
                    $_data = isset($aRow[$col]) ? $aRow[$col] : null;
                }

                // Tuỳ biến hiển thị
                if ($col === 'tbl_business_plan.id as id') {
                    $_data = ++$iDisplayStart; // Số thứ tự
                }

                if ($col === 'tbl_business_plan.date as date') {
                    $_data = _d($aRow['date']); // Format theo helper của bạn
                }

                if ($col === 'tbl_business_plan.reference_no as reference_no') {
                    $_data = $aRow['reference_no'];
                }

                if ($col === 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['staff_create'],
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

    function modal_accounting_dxnb($type = 1)
    {
        $title = '';
        $title = 'Tổng đề xuất nội bộ chưa duyệt';
        $this->load->view(
            'admin/dashboard_srceen_office/modal/modal_accounting_dxnb',
            ['type' => $type, 'title' => $title]
        );
    }

    function table_accounting_dxnb($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tblinternal_proposal.id as id',
            'tblinternal_proposal.date as date',
            'tblinternal_proposal.code as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee',
            '"" as qt'
        );
        $sIndexColumn = "id";
        $sTable = 'tblinternal_proposal';
        $where = array(
            'AND tblinternal_proposal.date >="' . $date_dashboard_srceen_sales . '"',
            'AND tblinternal_proposal.money > 0',
        );
        // $where[] = 'AND EXISTS (SELECT 1 FROM tblinternal_proposal_purchase WHERE tblinternal_proposal_purchase.id_internal_proposal = tblinternal_proposal.id)';
        $where[] = 'AND (tbl_internal_proposal_process.status = 0 OR tbl_internal_proposal_process.id is NULL) ';
        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tblinternal_proposal.staff',
            'LEFT JOIN tbl_internal_proposal_process ON tbl_internal_proposal_process.id_internal_proposal=tblinternal_proposal.id AND tbl_internal_proposal_process.bod = 1',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tblinternal_proposal.staff,manager_id,auditor_id,staff_controller_completes,staff_auditor_completes'),
            ' GROUP BY tblinternal_proposal.id'
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
                if ($aColumns[$i] == 'tblinternal_proposal.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tblinternal_proposal.date as date') {
                    $_data = _dt($aRow['date']);
                }
                if ($aColumns[$i] == 'tblinternal_proposal.code as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['staff'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array()
                    ) . ($aRow['fullname_employee'] ?? '');
                }
                if ($aColumns[$i] == '"" as qt') {
                    $this->db->select('tbl_internal_proposal_process.*,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tblstaff.staffid as staff,tbl_internal_proposal_process_child.id as childs,tbl_internal_proposal_process.bod,tbl_internal_proposal_process.name as name_process,tblstaff.profile_image as avatar_url');
                    $this->db->where('tbl_internal_proposal_process.id_internal_proposal', $aRow['id']);
                    $this->db->join(
                        'tbl_internal_proposal_process_child',
                        'tbl_internal_proposal_process_child.recommended_list_id = tbl_internal_proposal_process.id_process AND tbl_internal_proposal_process_child.id_internal_proposal = tbl_internal_proposal_process.id_internal_proposal',
                        'left'
                    );
                    $this->db->join('tblstaff', 'tblstaff.staffid=tbl_internal_proposal_process.staff_id', 'left');
                    $this->db->order_by('tbl_internal_proposal_process.id_process asc');
                    $this->db->group_by('tbl_internal_proposal_process.id');
                    $data_checklist_items = $this->db->get('tbl_internal_proposal_process')->result_array();
                    $internal_proposal['progress'] = [];
                    $status = 0;
                    $quahan = 0;
                    $status_finish = 1;
                    $assigned_process = '';
                    $bod_process = '';
                    $monitor_process = '';
                    $head_of_department_process = '';
                    $manager_process = '';
                    $auditor_process = '';
                    $staff_controller_completes = '';
                    $staff_auditor_completes = '';
                    $staff = '';
                    if (!empty($aRow['manager_id'])) {
                        if ($manager_process == '') {
                            $manager_process = $aRow['manager_id'];
                        }
                    }
                    if (!empty($aRow['auditor_id'])) {
                        if ($auditor_process == '') {
                            $auditor_process = $aRow['auditor_id'];
                        }
                    }
                    if (!empty($aRow['staff_controller_completes'])) {
                        if ($staff_controller_completes == '') {
                            $staff_controller_completes = $aRow['staff_controller_completes'];
                        }
                    }
                    if (!empty($aRow['staff_auditor_completes'])) {
                        if ($staff_auditor_completes == '') {
                            $staff_auditor_completes = $aRow['staff_auditor_completes'];
                        }
                    }
                    if (!empty($aRow['staff'])) {
                        if ($staff == '') {
                            $staff = $aRow['staff'];
                        }
                    }
                    $this->db->where('id_internal_proposal', $aRow['id']);
                    $assigned_pod = $this->db->get('tblinternal_proposal_staff_pod')->row_array();
                    if (!empty($assigned_pod)) {
                        $bod_process = $assigned_pod['id_staff'];
                    }
                    $this->db->where('id_internal_proposal', $aRow['id']);
                    $assigned = $this->db->get('tblinternal_proposal_assigned')->row_array();
                    if (!empty($assigned)) {
                        $assigned_process = $assigned['id_staff'];
                    }
                    foreach ($data_checklist_items as $k => $v) {
                        // Chỉ lấy process cuối cùng có status = 1 (nếu có nhiều cái status = 1 thì chỉ lấy cái cuối cùng)
                        if ($v['status'] == 1) {
                            // Chỉ lấy process cuối cùng có status = 1
                            $isLastStatus1 = true;
                            for ($j = $k + 1; $j < count($data_checklist_items); $j++) {
                                if ($data_checklist_items[$j]['status'] == 1) {
                                    $isLastStatus1 = false;
                                    break;
                                }
                            }
                            if ($isLastStatus1) {
                                // Lấy thông tin user/avatar theo mẫu bạn gửi
                                $user = '';
                                $avatar_url = '';
                                if (!empty($v['staff_id'])) {
                                    $FullName = get_staff_full_name($v['staff_id']);
                                    $user = $FullName;
                                    // $avatar_url = (!empty($v['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $v['staff'] . '/small_' . $v['avatar_url']) : '';
                                    $avatar_url = staff_profile_image(
                                        $v['staff'],
                                        array('staff-profile-image-small mright5'),
                                        'small',
                                        array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                                    );
                                } else {
                                    $user = get_staff_full_name($v['staff_id']);
                                    $avatar_url = ''(!empty($v['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $v['staff'] . '/small_' . $v['avatar_url']) : '';
                                    $avatar_url = '<img src="' . $avatar_url . '" class="avatar-sm_dxnt" alt="avatar">';
                                }
                                $internal_proposal['progress'][] = [
                                    'bod' => $v['bod'],
                                    'title' => preg_replace('/^(2\.|3\.)\s*/', '', $v['name_process']),
                                    'user' => $user,
                                    'status' => 'done_puwa',
                                    'avatar_url' => $avatar_url,
                                ];
                            }
                        } elseif ($v['status'] == 0) {
                            // Tìm process tiếp theo sau cái cuối cùng status=1
                            $lastDoneIndex = -1;
                            for ($j = $k - 1; $j >= 0; $j--) {
                                if ($data_checklist_items[$j]['status'] == 1) {
                                    $lastDoneIndex = $j;
                                    break;
                                }
                            }
                            if ($lastDoneIndex >= 0 && $k == $lastDoneIndex + 1) {
                                $user = '';
                                $avatar_url = '';
                                if ($staff == '') {
                                    $staff = 'Người lập đề xuất';
                                }
                                if ($bod_process == '') {
                                    $bod_process = 'Người duyệt thực thi';
                                }
                                if ($assigned_process == '') {
                                    $assigned_process = 'Người duyệt đề xuất';
                                }
                                if ($head_of_department_process == '') {
                                    $head_of_department_process = 'Người Hoàn Thành 2';
                                }
                                if ($monitor_process == '') {
                                    $monitor_process = 'Người kiểm toán hoàn thành';
                                }
                                if ($manager_process == '') {
                                    $manager_process = 'Người hoàn thành 2';
                                }
                                if ($auditor_process == '') {
                                    $auditor_process = 'Người kiểm soát hoàn thành';
                                }
                                if ($staff_controller_completes == '') {
                                    $staff_controller_completes = 'Người kiểm soát hoàn thành';
                                }
                                if ($staff_auditor_completes == '') {
                                    $staff_auditor_completes = 'Người kiểm toán hoàn thành';
                                }

                                $staffs = '';
                                if ($v['bod'] == 1) {
                                    $staffs = $bod_process;
                                }
                                if ($v['bod'] == 5) {
                                    $staffs = $staff;
                                }
                                if ($v['bod'] == 2) {
                                    $staffs = $assigned_process;
                                }
                                if ($v['bod'] == 3) {
                                    $staffs = $head_of_department_process;
                                }
                                if ($v['bod'] == 4) {
                                    $staffs = $auditor_process;
                                }
                                if ($v['bod'] == 6) {
                                    $staffs = $manager_process;
                                }
                                if ($v['bod'] == 7) {
                                    $staffs = $monitor_process;
                                }
                                if ($v['bod'] == 8) {
                                    $staffs = $staff_controller_completes;
                                }
                                if ($v['bod'] == 9) {
                                    $staffs = $staff_auditor_completes;
                                }
                                if (!empty($staffs) && is_numeric($staffs)) {
                                    $FullName = get_staff_full_name($staffs);
                                    $user = $FullName;
                                    $avatar_url = staff_profile_image(
                                        $staffs,
                                        array('staff-profile-image-small mright5'),
                                        'small',
                                        array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                                    );
                                } else {
                                    $user = $staffs;
                                    $avatar_url = (!empty($v['avatar_url'])) ? base_url('uploads/staff_profile_images/' . $v['staff'] . '/small_' . $v['avatar_url']) : '';
                                    $avatar_url = '<img src="' . $avatar_url . '" class="avatar-sm_dxnt" alt="avatar">';
                                }
                                $internal_proposal['progress'][] = [
                                    'bod' => $v['bod'],
                                    'title' => preg_replace('/^(2\.|3\.)\s*/', '', $v['name_process']),
                                    'user' => $user,
                                    'status' => 'pending_puwa',
                                    'avatar_url' => $avatar_url,
                                ];
                            }
                        }
                    }
                    // echo '<pre>';print_arrays($internal_proposal['progress']);die;
                    $_data = '<div style="text-align: left;">' . $this->render_progress_proposal($internal_proposal['progress']) . '</div>';
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    function modal_accounting_dxtc($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = 'Tổng đề xuất tài chính chưa chi';
        }
        if ($type == 2) {
            $title = 'Tổng đề xuất tài chính chưa tạo hóa đơn';
        }
        $this->load->view(
            'admin/dashboard_srceen_office/modal/modal_accounting_dxtc',
            ['type' => $type, 'title' => $title]
        );
    }

    function table_accounting_dxtc($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tblsuggestion.id as id',
            'tblsuggestion.date as date',
            'tblsuggestion.code as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee'
        );
        $sIndexColumn = "id";
        $sTable = 'tblsuggestion';
        $where = array(
            'AND tblsuggestion.date >="' . $date_dashboard_srceen_sales . '"',
        );
        if ($type == 1) {
            $where[] = 'AND ((tblsuggestion.payments + 0.1) <= tblsuggestion.price_total)';
        }
        if ($type == 2) {
            $where[] = 'AND NOT EXISTS (SELECT 1
            FROM tblpurchase_invoice_items pii
            JOIN tblpurchase_invoice pi ON pi.id = pii.purchase_invoice_id AND pi.type_create = 1
            WHERE pii.id_import_item = tblsuggestion.id)';
        }

        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tblsuggestion.staff_create',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tblsuggestion.staff_create'),
            ' GROUP BY tblsuggestion.id'
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
                if ($aColumns[$i] == 'tblsuggestion.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tblsuggestion.date as date') {
                    $_data = _d($aRow['date']);
                }
                if ($aColumns[$i] == 'CONCAT(tblsuggestion.prefix,"-",tblsuggestion.code) as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['staff_create'],
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

    function modal_accounting_nhapkho($type = 1)
    {
        $title = '';
        $title = 'Tổng nhập kho chưa tạo hóa đơn';
        $this->load->view(
            'admin/dashboard_srceen_office/modal/accounting_nhapkho',
            ['type' => $type, 'title' => $title]
        );
    }

    function table_accounting_nhapkho($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tblimport.id as id',
            'tblimport.date as date',
            'CONCAT(tblimport.prefix,"-",tblimport.code) as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee'
        );
        $sIndexColumn = "id";
        $sTable = 'tblimport';
        $where = array(
            'AND tblimport.date >="' . $date_dashboard_srceen_sales . '"',
        );
        $where[] = 'AND NOT EXISTS (
                SELECT 1
                FROM tblimport_items ii
                JOIN tblpurchase_invoice_items pii ON pii.id_import_item = ii.id
                JOIN tblpurchase_invoice pi ON pi.id = pii.purchase_invoice_id AND pi.type_create = 0
                WHERE ii.id_import = tblimport.id
            )';

        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tblimport.staff_create',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tblimport.staff_create'),
            ' GROUP BY tblimport.id'
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
                if ($aColumns[$i] == 'tblimport.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tblimport.date as date') {
                    $_data = _d($aRow['date']);
                }
                if ($aColumns[$i] == 'CONCAT(tblimport.prefix,"-",tblimport.code) as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['staff_create'],
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

    function modal_accounting_delivery($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = 'Tổng giao hàng chưa nhận chứng từ';
        }
        if ($type == 2) {
            $title = 'Tổng giao hàng chưa tạo hóa đơn';
        }
        $this->load->view(
            'admin/dashboard_srceen_office/modal/modal_accounting_delivery',
            ['type' => $type, 'title' => $title]
        );
    }

    function table_accounting_delivery($type = 1)
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
        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_deliveries.employee_id',
        );
        $where = array(
            'AND tbl_deliveries.date >="' . $date_dashboard_srceen_sales . '"'
        );
        if ($type == 1) {
            $where[] = 'AND tbl_deliveries.received_certificate = 0';
        }
        if ($type == 2) {
            $where[] = 'AND NOT EXISTS (
                SELECT 1
                FROM tbl_invoice_items sii
                WHERE sii.object_id = tbl_deliveries.id
            )';
        }
        if ($type == 3) {
            $where[] = 'AND tbl_deliveries.code_custom IS NULL';
            $where[] = 'AND tblclients.declare_customs = 1';
            $join[] = 'LEFT JOIN tblclients ON tblclients.userid=tbl_deliveries.customer_id';
        }

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

    function modal_accounting_invoice($type = 1)
    {
        $title = '';
        $title = 'Hóa đơn chưa thu tiền';
        $this->load->view(
            'admin/dashboard_srceen_office/modal/modal_accounting_invoice',
            ['type' => $type, 'title' => $title]
        );
    }

    function table_accounting_invoice($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tbl_invoices.id as id',
            'tbl_invoices.date as date',
            'tbl_invoices.reference_no as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee'
        );
        $sIndexColumn = "id";
        $sTable = 'tbl_invoices';
        $where = array(
            'AND tbl_invoices.date >="' . $date_dashboard_srceen_sales . '"',
            'AND tbl_invoices.status_payment < 2',
        );


        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_invoices.created_by',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tbl_invoices.created_by'),
            ' GROUP BY tbl_invoices.id'
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
                if ($aColumns[$i] == 'tbl_invoices.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tbl_invoices.date as date') {
                    $_data = _d($aRow['date']);
                }
                if ($aColumns[$i] == 'tbl_invoices.reference_no as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['created_by'],
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

    function modal_accounting_plan_propose($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = 'Kế hoạch chi chưa hoàn thành';
        }
        if ($type == 2) {
            $title = 'Kế hoạch thu chưa hoàn thành';
        }
        $this->load->view(
            'admin/dashboard_srceen_office/modal/modal_accounting_plan_propose',
            ['type' => $type, 'title' => $title]
        );
    }

    function table_accounting_plan_propose($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = array(
            'tblplan_propose.id as id',
            'tblplan_propose.date as date',
            'tblplan_propose.code as reference_no',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee'
        );
        $sIndexColumn = "id";
        $sTable = 'tblplan_propose';
        $where = array(
            'AND tblplan_propose.date >= "' . $date_dashboard_srceen_sales . '"',
            'AND tblplan_propose.status = 0',
        );
        if ($type == 1) {
            $where[] = 'AND tblplan_propose.type_plan_propose = "pay_slip"';
        }
        if ($type == 2) {
            $where[] = 'AND tblplan_propose.type_plan_propose = "vouchers_coupon"';
        }

        $join = array(
            'LEFT JOIN tblstaff ON tblstaff.staffid = tblplan_propose.create_by',
        );
        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            array('tblplan_propose.create_by'),
            ' GROUP BY tblplan_propose.id'
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
                if ($aColumns[$i] == 'tblplan_propose.id as id') {
                    $_data = ++$iDisplayStart;
                }
                if ($aColumns[$i] == 'tblplan_propose.date as date') {
                    $_data = _d($aRow['date']);
                }
                if ($aColumns[$i] == 'tblplan_propose.code as reference_no') {
                    $_data = $aRow['reference_no'];
                }
                if ($aColumns[$i] == 'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee') {
                    $_data = staff_profile_image(
                        $aRow['create_by'],
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

    function export_vtsx($type = 1)
    {
        $this->load->view('admin/dashboard_srceen_office/modal/export_vtsx', ['type' => $type]);
    }

    function table_export_vtsx($type = 1)
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales')
            ? get_option('date_dashboard_srceen_sales') . ' 00:00:00'
            : date('Y-m-d') . ' 00:00:00';

        // 1) KHÔNG alias trong $sTable
        $sTable = 'tbl_productions_orders';
        $sIndexColumn = 'id';
        $aColumns = [
            'tbl_productions_orders.id',
            'tbl_productions_orders.date',
            'tbl_productions_orders.reference_no',
        ];
        $join = [];
        $where = ['  tbl_productions_orders.date >= "' . $date_dashboard_srceen_sales . '" '];
        if ($type == 1) {
            $where[] = ' AND tbl_productions_orders.is_export_npl = 0 ';
        } else if ($type == 2) {
            $where[] = ' AND tbl_productions_orders.is_export_npl = 1 ';
        } else if ($type == 3) {
            $where[] = ' AND tbl_productions_orders.is_export_vtsx = 0 ';
        } else if ($type == 4) {
            $where[] = ' AND tbl_productions_orders.is_export_vtsx = 1 ';
        }

        $where = [];

        $where_type = '';
        $ed = '';

        $additionalSelect = [];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(), '', $additionalSelect);

        $output = $result['output'];
        $rResult = $result['rResult'];

        $iDisplayStart = (int)$this->input->post('iDisplayStart');

        foreach ($rResult as $aRow) {
            $row = [];
            // STT
            $row[] = ++$iDisplayStart;

            // Lấy theo alias đã add
            $row[] = _dt($aRow['tbl_productions_orders.date']);
            $row[] = $aRow['tbl_productions_orders.reference_no'];

            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }
}
