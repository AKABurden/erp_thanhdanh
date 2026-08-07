<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
        $this->load->library('ResizeCS');
    }

    /* This is admin dashboard view */
    public function index()
    {
        close_setup_menu();
        // $this->load->model('departments_model');
        // $this->load->model('todo_model');
        // $data['departments'] = $this->departments_model->get();

        // $data['todos'] = $this->todo_model->get_todo_items(0);
        // // Only show last 5 finished todo items
        // $this->todo_model->setTodosLimit(5);
        // $data['todos_finished']            = $this->todo_model->get_todo_items(1);
        // $data['upcoming_events_next_week'] = $this->dashboard_model->get_upcoming_events_next_week();
        // $data['upcoming_events']           = $this->dashboard_model->get_upcoming_events();
        // $data['title']                     = _l('dashboard_string');
        // $this->load->model('currencies_model');
        // $data['currencies']    = $this->currencies_model->get();
        // $data['base_currency'] = $this->currencies_model->get_base_currency();
        // $data['activity_log']  = $this->misc_model->get_activity_log();
        // // Tickets charts
        // $tickets_awaiting_reply_by_status     = $this->dashboard_model->tickets_awaiting_reply_by_status();
        // $tickets_awaiting_reply_by_department = $this->dashboard_model->tickets_awaiting_reply_by_department();

        // $data['tickets_reply_by_status']              = json_encode($tickets_awaiting_reply_by_status);
        // $data['tickets_awaiting_reply_by_department'] = json_encode($tickets_awaiting_reply_by_department);
        $data['tickets_awaiting_reply_by_department'] = json_encode([]);
        // $data['tickets_reply_by_status_no_json']              = $tickets_awaiting_reply_by_status;
        // $data['tickets_awaiting_reply_by_department_no_json'] = $tickets_awaiting_reply_by_department;
        $data['tickets_awaiting_reply_by_department_no_json'] = json_encode([]);
        $data['tickets_reply_by_status_no_json'] = json_encode([]);
        $data['tickets_reply_by_status'] = json_encode([]);
        $data['tickets_chart_departments'] = json_encode([]);
        $data['tickets_awaiting_reply_by_department'] = json_encode([]);
        $data['leads_status_stats'] = json_encode([]);
        $data['projects_status_stats'] = json_encode([]);
        $data['weekly_payment_stats'] = json_encode([]);
        // $data['projects_status_stats'] = json_encode($this->dashboard_model->projects_status_stats());

        // $data['leads_status_stats']    = json_encode($this->dashboard_model->leads_status_stats());
        // $data['google_ids_calendars']  = $this->misc_model->get_google_calendar_ids();
        // $data['bodyclass']             = 'dashboard invoices-total-manual';
        // $this->load->model('announcements_model');
        // $data['staff_announcements']             = $this->announcements_model->get();
        // $data['total_undismissed_announcements'] = $this->announcements_model->get_total_undismissed_announcements();

        // $this->load->model('projects_model');
        // $data['projects_activity'] = $this->projects_model->get_activity('', hooks()->apply_filters('projects_activity_dashboard_limit', 20));
        // add_calendar_assets();
        // $this->load->model('utilities_model');
        // $this->load->model('estimates_model');
        // $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        // $this->load->model('proposals_model');
        // $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        // $wps_currency = 'undefined';
        // if (is_using_multiple_currencies()) {
        //     $wps_currency = $data['base_currency']->id;
        // }
        // $data['weekly_payment_stats'] = json_encode($this->dashboard_model->get_weekly_payments_statistics(array()));

        // $data['dashboard'] = true;

        // $data['user_dashboard_visibility'] = get_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility');

        // if (!$data['user_dashboard_visibility']) {
        //     $data['user_dashboard_visibility'] = [];
        // } else {
        //     $data['user_dashboard_visibility'] = unserialize($data['user_dashboard_visibility']);
        // }
        // $data['user_dashboard_visibility'] = json_encode($data['user_dashboard_visibility']);

        // $data = hooks()->apply_filters('before_dashboard_render', $data);
        // $this->load->view('admin/dashboard/dashboard', $data);
        // $data = [];
        $this->load->view('admin/dashboard/dashboard_new', $data);
    }

    /* Chart weekly payments statistics on home page / ajax */
    public function weekly_payments_statistics()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            echo json_encode($this->dashboard_model->get_weekly_payments_statistics($data));
            die();
        }
    }

    //Công bổ sung
    public function load_menu_child()
    {
        if ($this->input->get('data_object')) {
            $object = $this->input->get('data_object');
            $menu_child = array();
            $aside_menu_active  = json_decode(get_option('aside_menu_active'));

            if (!empty($aside_menu_active->{$object}->children)) {
                $menu_child = $aside_menu_active->{$object}->children;
            }
            $string = "";
            foreach ($menu_child as $key => $value) {
                $string .= '<li class="menu-item-' . (isset($value->id) ? $value->id : '') . '">'
                    . '       <a href="' . admin_url($value->url) . '" aria-expanded="false">'
                    . '           <i class="' . (!empty($value->icon) ? $value->icon : '') . ' menu-icon"></i>'
                    . '           <span class="menu-text">' . _l($value->name, '', false) . '</span>'
                    . '       </a>';
                if (!empty($value->children)) {
                    $string .= '<ul class="nav nav-second-level collapse in" aria-expanded="true">';
                    foreach ($value->children as $_key => $_value) {
                        $string .= '<li class="H_li sub-menu-item-' . (isset($_value->id) ? $_value->id : '') . '">'
                            . '          <a href="' . admin_url($_value->url) . '" aria-expanded="false">'
                            . '              <i class="' . (!empty($_value->icon) ? $_value->icon : '') . ' menu-icon"></i>'
                            . '              <span class="menu-text">' . _l($_value->name, '', false) . '</span>'
                            . '          </a>'
                            . '       </li>';
                    }
                    $string .= '</ul>';
                }
                $string .= '   </li>';
            }
            echo $string;
        }
    }

    //bổ xung giao diện chính
    public function staff_birthday_of_month()
    {
        $data['title'] = _l('staff_birthday_of_month');
        $this->load->view('admin/dashboard/staff_birthday_of_month', $data);
    }

    public function table_staff_birthday_of_month()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('staff_birthday_of_month');
        }
    }
    //end

    public function loadNotificationCustom()
    {
        // $this->load->view('admin/includes/newsfeed');
    }

    public function loadNotification()
    {
        $type_noti = $this->input->get('type_noti');
        $data['type_noti'] = $type_noti;
        $this->load->view('admin/includes/tab_notifications', $data);
    }

    public function countNoti()
    {
        $staff_id = get_staff_user_id();
        $read = 0;
        $sql_manu = 'SELECT COUNT(*) as total FROM ' . db_prefix() . 'notifications WHERE isread_inline=' . $read . ' AND touserid=' . $staff_id . ' AND type=1';
        $manu = $this->db->query($sql_manu)->row();

        $sql_order = 'SELECT COUNT(*) as total FROM ' . db_prefix() . 'notifications WHERE isread_inline=' . $read . ' AND touserid=' . $staff_id . ' AND type=2';
        $order = $this->db->query($sql_order)->row();

        $sql_purchase = 'SELECT COUNT(*) as total FROM ' . db_prefix() . 'notifications WHERE isread_inline=' . $read . ' AND touserid=' . $staff_id . ' AND type=3';
        $purchase = $this->db->query($sql_purchase)->row();

        $sql_warehouse = 'SELECT COUNT(*) as total FROM ' . db_prefix() . 'notifications WHERE isread_inline=' . $read . ' AND touserid=' . $staff_id . ' AND type=4';
        $warehouse = $this->db->query($sql_warehouse)->row();

        $sql_kpi = 'SELECT COUNT(*) as total FROM ' . db_prefix() . 'notifications WHERE isread_inline=' . $read . ' AND touserid=' . $staff_id . ' AND type = 14';
        $kpi = $this->db->query($sql_kpi)->row();

        $total_manu = 0;
        $total_order = 0;
        $total_purchase = 0;
        $total_warehouse = 0;

        $data['total_manu'] = $manu->total;
        $data['total_order'] = $order->total;
        $data['total_purchase'] = $purchase->total;
        $data['total_warehouse'] = $warehouse->total;
        $data['total_kpi'] = $kpi->total;

        echo json_encode($data);
    }

    public function getQuantityPO()
    {

        $period_time = $this->input->post('period_time');
        if (!empty($period_time)) {
            $period_time = explode('-', $period_time);
            $start_date_search = trim($period_time[0]);
            $end_date_search = trim($period_time[1]);
        }

        $aColumns = [
            'tbl_products.images as images',
            'CONCAT(tbl_products.name, "(", tbl_products.code, ")") as item_name',
            'SUM(tbl_productions_orders_items.quantity) as quantity',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_productions_orders';
        $where        = [];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
        }

        $join = [
            'INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id'
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_productions_orders_items.type_items as type_items',
            'tbl_productions_orders_items.items_id as items_id',
        ], 'GROUP BY tbl_productions_orders_items.type_items, tbl_productions_orders_items.items_id', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantity = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;

            $images = $aRow['images'];
            if (!empty($images)) {
                $images = base_url('uploads/products/' . $images);
            } else {
                $images = base_url('assets/images/tnh/no_image.png');
            }

            $tdImages = '<div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px; margin: auto;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="' . $images . '" style="border-radius: 50%"></div></a></div></div></div></div>';

            $row[0] = $tdImages;
            $row[1] = $aRow['item_name'];
            $row[2] = $aRow['quantity'];
            if ($aRow['quantity'] > $totalQuantity) {
                $totalQuantity = $aRow['quantity'];
            }
            // $totalQuantity+= $aRow['quantity'];
            $output['aaData'][] = $row;
        }
        $output['totalQuantity'] = $totalQuantity;
        echo json_encode($output);
    }

    public function chartQC()
    {
        $period_time = $this->input->post('period_time');
        if (!empty($period_time)) {
            $period_time = explode('-', $period_time);
            $start_date_search = trim($period_time[0]);
            $end_date_search = trim($period_time[1]);
        }
        $total_quanlity = 0;
        $reasons = [];

        $this->db->select('tbl_detail_errors.name as name_reason,SUM(tbl_check_quality_items_error.quantity) as quantity_reason,tbl_check_quality.*');
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
        $this->db->where('tbl_check_quality.date >=', to_sql_date($start_date_search) . ' 00:00:00');
        $this->db->where('tbl_check_quality.date <=', to_sql_date($end_date_search) . ' 23:59:59');

        $this->db->group_by('tbl_check_quality_items_error.id_error');
        $this->db->order_by('quantity_reason DESC');
        $this->db->having('quantity_reason > 0');
        $reasons = $this->db->get()->result_array();
        if (!empty($reasons)) {
            foreach ($reasons as $key => $value) {
                $total_quanlity += $value['quantity_reason'];
            }

            foreach ($reasons as $key => $value) {
                $reasons[$key]['tyle'] = round(($value['quantity_reason'] * 100) / $total_quanlity,
                    1,
                    PHP_ROUND_HALF_EVEN
                );
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
        $__data['datas'] = $datas;
        echo json_encode($__data);
        die;
    }

    public function getQCDashboard()
    {

        $period_time = $this->input->post('period_time');
        if (!empty($period_time)) {
            $period_time = explode('-', $period_time);
            $start_date_search = trim($period_time[0]);
            $end_date_search = trim($period_time[1]);
        }

        $aColumns = [
            'tbl_detail_errors.name as name_reason',
            'SUM(tbl_check_quality_items_error.quantity) as quantity_reason',
            '"0" as precent',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_check_quality';
        $where        = [
            'INNER JOIN tbl_check_quality_items ON tbl_check_quality_items.check_quality_id = tbl_check_quality.id',
            'INNER JOIN tbl_check_quality_items_error ON tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'INNER JOIN tbl_detail_errors ON tbl_detail_errors.id = tbl_check_quality_items_error.id_error'
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_check_quality.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_check_quality.date <= '$end_date_search'");
        }

        $join = [];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_check_quality_items_error.id_error'
        ], 'GROUP BY tbl_check_quality_items_error.id_error', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantityQC = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row[0] = $aRow['name_reason'];
            $row[1] = $aRow['quantity_reason'];
            $row[2] = $aRow['quantity_reason'];
            $totalQuantityQC += $aRow['quantity_reason'];
            $output['aaData'][] = $row;
        }
        $output['totalQuantityQC'] = $totalQuantityQC;
        echo json_encode($output);
    }

    public function chartPP()
    {
        $data = [];
        $period_time = $this->input->post('period_time');
        if (!empty($period_time)) {
            $period_time = explode('-', $period_time);
            $start_date_search = trim($period_time[0]);
            $end_date_search = trim($period_time[1]);
        }
        $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
        $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';

        $this->db->select('
            tbl_products.id as id,
            SUM(tbl_purchase_product_items.quantity) as total_quantity,
            tbl_products.images as images,
            tbl_products.code as item_code,
            tbl_products.name as item_name
        ', false);
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_purchase_product_items.item_id');
        $this->db->where('tbl_products.type_products', 'products');
        $this->db->where('tbl_purchase_products.date >=', $start_date_search);
        $this->db->where('tbl_purchase_products.date <=', $end_date_search);
        $this->db->order_by('total_quantity DESC');
        $this->db->group_by('tbl_products.id');
        $purchase_products = $this->db->get()->result_array();

        $categories = [];
        $quanttiy = [];
        if (!empty($purchase_products)) {
            foreach ($purchase_products as $key => $value) {
                $images = $value['images'];
                if (!empty($images)) {
                    $images = base_url('uploads/products/' . $images);
                } else {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $categories[] = '<div class="text-center">
                        <img src="' . $images . '" style="width: 40px; height: 40px;"/>
                    </div>
                    <div class="text-center">' . $value['item_name'] . '(' . $value['item_code'] . ')</div>';
                $quanttiy[] = (float)$value['total_quantity'];
            }
        }
        $data['categories'] = $categories;
        $data['quanttiy'] = $quanttiy;
        echo json_encode($data);
    }

    public function get_number_orders_un_approved()
    {
        $arrIDStaff = employee_manage_staff();
        $this->perViewOrders = has_permission('orders', '', 'view');
        if (!$this->perViewOrders) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                $this->db->where('tbl_orders.created_by IN (' . $coverStr . ') OR tbl_orders.employee_id IN (' . $coverStr . ')', false, false);
            }
        }
        $this->db->where('status', 'un_approved');
        $numberOrders = $this->db->get('tbl_orders')->num_rows();
        echo !empty($numberOrders) ? $numberOrders : 0;
    }

    public function get_total_tasks()
    {
        //		$arrIDStaff = employee_manage_staff();
        //		$this->perViewOrders = has_permission('orders', '', 'view');
        //		if (!$this->perViewOrders) {
        //			if ($arrIDStaff != array()) {
        //				$coverStr = implode(",", $arrIDStaff);
        ////				$this->db->where('tbl_orders.created_by IN (' . $coverStr . ') OR tbl_orders.employee_id IN (' . $coverStr . ')', false, false);
        //			}
        //		}

        if (has_permission('tasks', '', 'view_own') && !is_admin()) {
            $this->db->join('tbltask_assigned', 'tbltask_assigned.taskid = tbltasks.id', 'left');
            $this->db->where('tbltask_assigned.staffid', get_staff_user_id());
        }
        $this->db->where('status', '1');
        $numberTasks = $this->db->get('tbltasks')->num_rows();
        echo !empty($numberTasks) ? $numberTasks : 0;
    }
}
