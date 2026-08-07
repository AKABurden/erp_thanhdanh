<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ptm extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('orders_model');
        $this->load->model('clients_model');
        
        // Permission check: standard ptm view permission
        if (!has_permission('ptm', '', 'view')) {
            access_denied();
        }
    }

    public function index()
    {
        $data['title'] = 'Danh sách Phiếu Yêu Cầu Phát Triển Mẫu (PTM)';
        
        // Fetch customers with active PTMs
        $this->db->select('tblclients.userid as id, tblclients.company as name');
        $this->db->from('tblclients');
        $this->db->join('tbl_orders', 'tbl_orders.customer_id = tblclients.userid');
        $this->db->join('tbl_orders_ptm', 'tbl_orders_ptm.order_id = tbl_orders.id');
        $this->db->group_by('tblclients.userid');
        $data['customers'] = $this->db->get()->result_array();
        
        // Fetch orders with active PTMs
        $this->db->select('tbl_orders.id, tbl_orders.reference_no');
        $this->db->from('tbl_orders');
        $this->db->join('tbl_orders_ptm', 'tbl_orders_ptm.order_id = tbl_orders.id');
        $this->db->group_by('tbl_orders.id');
        $data['orders'] = $this->db->get()->result_array();
        
        // Fetch quotes with active PTMs
        $this->db->select('tbl_quotes.id, tbl_quotes.reference_no');
        $this->db->from('tbl_quotes');
        $this->db->join('tbl_orders_sub', 'tbl_orders_sub.quote_id_chonse = tbl_quotes.id');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_orders_sub.order_id');
        $this->db->join('tbl_orders_ptm', 'tbl_orders_ptm.order_id = tbl_orders.id');
        $this->db->group_by('tbl_quotes.id');
        $data['quotes'] = $this->db->get()->result_array();

        $this->load->view('admin/ptm/manage', $data);
    }

    public function get_ptms()
    {
        $aColumns = [
            'tbl_orders_ptm.id as id',
            'tbl_orders_ptm.ptm_no as ptm_no',
            'tbl_orders_ptm.date as date',
            'tbl_orders_ptm.order_id as order_id',
            'tbl_orders.reference_no as order_ref',
            'tbl_quotes.reference_no as quote_ref',
            'tblclients.company as customer_name',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as creator_name'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_orders_ptm';
        $join = [
            'LEFT JOIN tbl_orders ON tbl_orders.id = tbl_orders_ptm.order_id',
            'LEFT JOIN tbl_orders_sub ON tbl_orders_sub.order_id = tbl_orders.id',
            'LEFT JOIN tbl_quotes ON tbl_quotes.id = tbl_orders_sub.quote_id_chonse',
            'LEFT JOIN tblclients ON tblclients.userid = tbl_orders.customer_id',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_orders_ptm.created_by'
        ];
        $where = [];
        
        $customer_id = $this->input->post('filter_customer');
        if (!empty($customer_id)) {
            $where[] = 'AND tbl_orders.customer_id = ' . $this->db->escape_str($customer_id);
        }
        
        $order_id = $this->input->post('filter_order');
        if (!empty($order_id)) {
            $where[] = 'AND tbl_orders.id = ' . $this->db->escape_str($order_id);
        }
        
        $quote_id = $this->input->post('filter_quote');
        if (!empty($quote_id)) {
            $where[] = 'AND tbl_orders_sub.quote_id_chonse = ' . $this->db->escape_str($quote_id);
        }
        
        $this->db->group_by('tbl_orders_ptm.ptm_no');
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output = $result['output'];
        $rResult = $result['rResult'];

        $has_edit = has_permission('ptm', '', 'edit');
        $has_delete = has_permission('ptm', '', 'delete');
        $has_export = has_permission('ptm', '', 'export');
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/ptm/view_modal/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['ptm_no'] . '</a></div>';
            $row[] = '<div class="text-center">' . _d($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['order_ref'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['quote_ref'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['customer_name'] . '</div>';
            $task = $this->db->query("SELECT * FROM " . db_prefix() . "tasks WHERE rel_type = 'PTM' AND rel_id = ?", [$aRow['id']])->row_array();

            if (!empty($task)) {
                $row[] = '<span class="inline-block label mleft5 mtop5 pointer" style="font-size: 9px;color:#bd4e4e;border:1px solid #bd4e4e" onclick="init_task_modal(' . $task['id'] . ')">1 Phiếu Công Việc</span>';
            } else {
                $row[] = '<div class="text-center"><button class="btn btn-info btn-xs" onclick="create_ptm_task(' . $aRow['id'] . ')">Tạo phiếu công việc</button></div>';
            }

            $row[] = '<div class="text-left">' . $aRow['creator_name'] . '</div>';
            
            $actions = '<div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" data-toggle="dropdown" aria-expanded="true">
                Tác vụ <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" style="width: 180px;">
                    <li><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/ptm/view_modal/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-eye"></i> Xem Chi Tiết</a></li>';
            
            if ($has_edit) {
                $actions .= '<li><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/ptm/create_modal/' . $aRow['order_id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil-square-o"></i> Chỉnh Sửa</a></li>';
            }
            if ($has_export) {
                $actions .= '<li><a href="' . base_url('admin/ptm/export_excel/' . $aRow['id']) . '"><i class="fa fa-file-excel-o"></i> Xuất File Excel</a></li>';
            }
            if ($has_delete) {
                $actions .= '<li><a href="' . base_url('admin/ptm/delete/' . $aRow['id']) . '" class="text-danger _delete"><i class="fa fa-remove"></i> Xóa</a></li>';
            }
            
            $actions .= '</ul></div>';


            $row[] = $actions;
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function create_task_ajax($ptm_id)
    {
        if (!has_permission('ptm', '', 'create') && !has_permission('ptm', '', 'edit')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            die();
        }
        $ptm = $this->db->get_where('tbl_orders_ptm', ['id' => $ptm_id])->row_array();
        if (empty($ptm)) {
            echo json_encode(['success' => false, 'message' => 'Phiếu YCPTM không tồn tại']);
            die();
        }
        
        // Check if task already exists
        $existing_task = $this->db->query("SELECT * FROM " . db_prefix() . "tasks WHERE rel_type = 'PTM' AND rel_id = ?", [$ptm_id])->row_array();
        if (!empty($existing_task)) {
            echo json_encode(['success' => true, 'message' => 'Phiếu phân công đã tồn tại']);
            die();
        }
        
        $this->auto_create_ptm_task($ptm_id, $ptm['ptm_no'], $ptm['order_id'], $ptm['created_by']);
        
        echo json_encode(['success' => true, 'message' => 'Tạo phiếu phân công thành công']);
        die();
    }

    public function create_modal($order_id)
    {
        $order = $this->orders_model->rowOrderById($order_id);
        if (empty($order)) {
            echo "Đơn hàng không tồn tại";
            return;
        }
        
        // Fetch order items (products)
        $this->db->select('tbl_order_items.item_id, tbl_order_items.item_code, tbl_order_items.item_name');
        $this->db->from('tbl_order_items');
        $this->db->where('order_id', $order_id);
        $items = $this->db->get()->result_array();
        
        // Check if there is already an existing PTM for this order
        $this->db->where('order_id', $order_id);
        $existing_ptm = $this->db->get('tbl_orders_ptm')->row_array();
        
        if (!empty($existing_ptm)) {
            if (!has_permission('ptm', '', 'edit')) {
                access_denied();
            }
        } else {
            if (!has_permission('ptm', '', 'create')) {
                access_denied();
            }
        }
        $existing_by_product = [];
        $ptm_no = getReference('ptm');
        if (!empty($existing_ptm)) {
            $ptm_no = $existing_ptm['ptm_no'];
            $this->db->where('ptm_id', $existing_ptm['id']);
            $existing_details = $this->db->get('tbl_orders_ptm_detail')->result_array();
            foreach ($existing_details as $ed) {
                $existing_by_product[$ed['product_id']] = $ed;
            }
        }
        
        $this->db->select('quote_id_chonse');
        $this->db->from('tbl_orders_sub');
        $this->db->where('order_id', $order_id);
        $order_sub = $this->db->get()->row_array();
        $quote_id = !empty($order_sub) ? $order_sub['quote_id_chonse'] : 0;
        
        // For each product, fetch its quote/materials/stages data
        $products_data = [];
        foreach ($items as $item) {
            $product_id = $item['item_id'];
            
            // Fetch quote item details
            $this->db->select('*');
            $this->db->from('tbl_quote_items');
            $this->db->where('quote_id', $quote_id);
            $this->db->where('item_id', $product_id);
            $quote_item = $this->db->get()->row_array();
            
            if (empty($quote_item)) {
                // fallback to product table details
                $product_row = $this->db->get_where('tbl_products', ['id' => $product_id])->row_array();
                if (!empty($product_row)) {
                    $quote_item = $product_row;
                    $quote_item['item_code'] = $product_row['code'];
                    $quote_item['item_name'] = $product_row['name'];
                    $quote_item['data_price_json'] = '';
                } else {
                    continue;
                }
            }
            
            $arrDataJson = !empty($quote_item['data_price_json']) ? json_decode($quote_item['data_price_json'], true) : [];
            
            // Client/Customer Info
            $client = $this->clients_model->get($order['customer_id']);
            
            // Group/Brand info
            $this->db->select('tblcustomers_groups.code, tblcustomers_groups.name');
            $this->db->from('tblcustomer_groups');
            $this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tblcustomer_groups.groupid');
            $this->db->where('tblcustomer_groups.customer_id', $order['customer_id']);
            $group = $this->db->get()->row_array();
            
            // Product unit
            $this->db->select('unit_id, conversion_unit');
            $this->db->from('tbl_products');
            $this->db->where('id', $product_id);
            $product_row = $this->db->get()->row_array();
            
            $final_unit_id = 0;
            if (!empty($quote_item['unit_id'])) {
                $final_unit_id = $quote_item['unit_id'];
            } elseif (!empty($quote_item['conversion_unit'])) {
                $final_unit_id = $quote_item['conversion_unit'];
            } elseif (!empty($product_row['unit_id'])) {
                $final_unit_id = $product_row['unit_id'];
            } elseif (!empty($product_row['conversion_unit'])) {
                $final_unit_id = $product_row['conversion_unit'];
            }
            
            $this->db->select('unit');
            $this->db->from('tblunits');
            $this->db->where('unitid', $final_unit_id);
            $unit_row = $this->db->get()->row_array();
            $unit_name = !empty($unit_row) ? $unit_row['unit'] : '';
    
            // Product dimensions
            $height = isset($arrDataJson['height']) ? $arrDataJson['height'] : (!empty($quote_item['height']) ? $quote_item['height'] / 10 : 0);
            $width = isset($arrDataJson['width']) ? $arrDataJson['width'] : (!empty($quote_item['wide']) ? $quote_item['wide'] / 10 : 0);
            $margin = isset($arrDataJson['corner_boundary_height']) ? $arrDataJson['corner_boundary_height'] : (!empty($quote_item['loss']) ? $quote_item['loss'] : 0);
            
            // Fetch materials (Section C)
            $materials = [];
            $material_ids = [];
            if (!empty($arrDataJson['arrItemsNPL'])) {
                foreach ($arrDataJson['arrItemsNPL'] as $npl) {
                    $material_ids[] = $npl['item_id'];
                }
            }
            if (!empty($arrDataJson['ItemsPrice'])) {
                foreach ($arrDataJson['ItemsPrice'] as $ip) {
                    if ($ip['type_price'] == 'materials') {
                        $material_ids[] = $ip['item_id_price'];
                    }
                }
            }
            if (!empty($arrDataJson['itemsPriceBackside'])) {
                foreach ($arrDataJson['itemsPriceBackside'] as $ipb) {
                    if ($ipb['type_price_backside'] == 'materials') {
                        $material_ids[] = $ipb['item_id_price_backside'];
                    }
                }
            }
            $material_ids = array_unique($material_ids);
            if (!empty($material_ids)) {
                $this->db->select('id, code, name');
                $this->db->from('tbl_materials');
                $this->db->where_in('id', $material_ids);
                $materials = $this->db->get()->result_array();
            }
            
            // Fetch stages (Section D) - Strictly from data_price_json
            $stages = [];
            $stage_ids = [];
            if (!empty($arrDataJson['itemsStagesProducts'])) {
                foreach ($arrDataJson['itemsStagesProducts'] as $stg) {
                    if (isset($stg['type_price_products']) && $stg['type_price_products'] == 'stages') {
                        if (!empty($stg['stage_id_price_products'])) {
                            $stage_ids[] = $stg['stage_id_price_products'];
                        } elseif (!empty($stg['item_id_price_products'])) {
                            $stage_ids[] = $stg['item_id_price_products'];
                        }
                    }
                }
            }
            $stage_keys = ['arrItemsLStage', 'arrItemsPsStage', 'arrItemsIStage', 'arrItemsPStage', 'arrItemsDStage', 'arrItemsCStage'];
            foreach ($stage_keys as $sk) {
                if (!empty($arrDataJson[$sk])) {
                    foreach ($arrDataJson[$sk] as $stg) {
                        if (!empty($stg['item_id'])) {
                            $stage_ids[] = $stg['item_id'];
                        } elseif (!empty($stg['stage_id'])) {
                            $stage_ids[] = $stg['stage_id'];
                        }
                    }
                }
            }
            $stage_ids = array_unique($stage_ids);
            if (!empty($stage_ids)) {
                $this->db->select('id, name as stage_name');
                $this->db->from('tbl_stages');
                $this->db->where_in('id', $stage_ids);
                $stages_rows = $this->db->get()->result_array();
                
                $stages_by_id = [];
                foreach ($stages_rows as $row) {
                    $stages_by_id[$row['id']] = $row['stage_name'];
                }
                
                $number = 1;
                foreach ($stage_ids as $id) {
                    if (isset($stages_by_id[$id])) {
                        $stages[] = [
                            'number' => $number++,
                            'stage_name' => $stages_by_id[$id]
                        ];
                    }
                }
            }
            
            // Delivery Address
            $delivery_address = '';
            if (!empty($order['address_delivery_id'])) {
                $da_row = $this->db->get_where('tblshipping_client', ['id' => $order['address_delivery_id']])->row_array();
                if (!empty($da_row)) {
                    $delivery_address = $da_row['address'];
                }
            }
            if (empty($delivery_address) && !empty($client)) {
                $delivery_address = $client->address;
            }
            
            $products_data[$product_id] = [
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'],
                'client_classify' => (!empty($client) && $client->code_type) ? $client->code_type : 'TN',
                'brand_code' => !empty($group) ? $group['code'] : '',
                'brand_name' => !empty($group) ? $group['name'] : '',
                'client_code' => !empty($client) ? $client->code_client : '',
                'client_name' => !empty($client) ? $client->company : '',
                'product_code' => $quote_item['item_code'],
                'product_name' => $quote_item['item_name'],
                'unit_name' => $unit_name,
                'product_height' => $height,
                'product_width' => $width,
                'product_margin' => $margin,
                'materials' => $materials,
                'stages' => $stages,
                'delivery_address' => $delivery_address,
                'existing' => isset($existing_by_product[$product_id]) ? $existing_by_product[$product_id] : null
            ];
        }
        
        $data['order'] = $order;
        $data['ptm_no'] = $ptm_no;
        $data['products_data'] = $products_data;
        $this->load->view('admin/ptm/create_modal', $data);
    }

    public function save()
    {
        if ($this->input->post()) {
            $order_id = $this->input->post('order_id');
            
            // Check if PTM already exists for this order
            $this->db->where('order_id', $order_id);
            $existing = $this->db->get('tbl_orders_ptm')->row_array();
            
            if (!empty($existing)) {
                if (!has_permission('ptm', '', 'edit')) {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền chỉnh sửa PTM.']);
                        exit;
                    }
                    access_denied();
                }
            } else {
                if (!has_permission('ptm', '', 'create')) {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền tạo mới PTM.']);
                        exit;
                    }
                    access_denied();
                }
            }
            
            $ptm_no = !empty($existing) ? $existing['ptm_no'] : getReference('ptm');
            
            // Delete existing details and general records for this order to overwrite
            if (!empty($existing)) {
                $this->db->where('ptm_id', $existing['id']);
                $this->db->delete('tbl_orders_ptm_detail');
                
                $this->db->where('id', $existing['id']);
                $this->db->delete('tbl_orders_ptm');
            }
            
            $date = to_sql_date($this->input->post('date'));
            
            // Insert new general record in tbl_orders_ptm
            $general_data = [
                'order_id' => $order_id,
                'date' => $date,
                'ptm_no' => $ptm_no,
                'created_by' => !empty($existing) ? $existing['created_by'] : get_staff_user_id(),
                'date_created' => !empty($existing) ? $existing['date_created'] : date('Y-m-d H:i:s'),
            ];
            if (!empty($existing)) {
                $general_data['updated_by'] = get_staff_user_id();
                $general_data['date_updated'] = date('Y-m-d H:i:s');
            }
            $this->db->insert('tbl_orders_ptm', $general_data);
            $ptm_id = $this->db->insert_id();
            
            // Insert product details in tbl_orders_ptm_detail
            $products_post = $this->input->post('products');
            foreach ($products_post as $prod_id => $fields) {
                $detail_data = [
                    'ptm_id' => $ptm_id,
                    'product_id' => $prod_id,
                    'mau' => isset($fields['mau']) ? $fields['mau'] : '',
                    'yeu_cau_dac_biet' => isset($fields['yeu_cau_dac_biet']) ? $fields['yeu_cau_dac_biet'] : '',
                    'tieu_chuan_test' => isset($fields['tieu_chuan_test']) ? $fields['tieu_chuan_test'] : '',
                    'cach_xem_mau' => isset($fields['cach_xem_mau']) ? $fields['cach_xem_mau'] : '',
                    'quy_trinh_do_mau' => isset($fields['quy_trinh_do_mau']) ? $fields['quy_trinh_do_mau'] : '',
                    'tieu_chuan_do_mau' => isset($fields['tieu_chuan_do_mau']) ? $fields['tieu_chuan_do_mau'] : '',
                    'dia_chi_giao_hang' => isset($fields['dia_chi_giao_hang']) ? $fields['dia_chi_giao_hang'] : '',
                    'tem_dong_goi' => isset($fields['tem_dong_goi']) ? $fields['tem_dong_goi'] : '',
                    'dong_kien' => isset($fields['dong_kien']) ? $fields['dong_kien'] : '',
                    'carton' => isset($fields['carton']) ? $fields['carton'] : '',
                ];
                $this->db->insert('tbl_orders_ptm_detail', $detail_data);
            }
            
            if (empty($existing)) {
                updateReference('ptm');
                $this->auto_create_ptm_task($ptm_id, $ptm_no, $order_id, $general_data['created_by']);
            }
            
            // update orders ptm status column to 1
            $this->db->where('id', $order_id);
            $this->db->update('tbl_orders', ['ptm' => 1]);
            
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Lưu Phiếu YCPTM thành công'
                ]);
                exit;
            } else {
                set_alert('success', 'Lưu Phiếu YCPTM thành công');
                redirect(admin_url('ptm'));
            }
        }
    }

    private function auto_create_ptm_task($ptm_id, $ptm_no, $order_id, $created_by = 0)
    {
        $this->load->model('tasks_model');
        $category_task = $this->db->get_where('tblcategory_tasks', ['code' => 'TAS-P03-DLY-037'])->row_array();
        $order = $this->db->get_where('tbl_orders', ['id' => $order_id])->row_array();
        
        if (!empty($category_task)) {
            $task_name = $category_task['content'] . ' - ' . $ptm_no;
            
            $duedate = date('Y-m-d H:i:s');
            if ($category_task['time'] > 0) {
                if ($category_task['type'] == 1) { // Days
                    $duedate = date('Y-m-d H:i:s', strtotime('+' . $category_task['time'] . ' days'));
                } elseif ($category_task['type'] == 2) { // Months
                    $duedate = date('Y-m-d H:i:s', strtotime('+' . $category_task['time'] . ' months'));
                } elseif ($category_task['type'] == 3) { // Years
                    $duedate = date('Y-m-d H:i:s', strtotime('+' . $category_task['time'] . ' years'));
                }
            }
            
            $task_data = [
                'name' => $task_name,
                'category_tasks' => $category_task['id'],
                'status' => 1, // Not started
                'startdate' => date('Y-m-d H:i:s'),
                'duedate' => $duedate,
                'rel_id' => $ptm_id,
                'rel_type' => 'PTM',
                'id_branch' => !empty($order) ? $order['id_branch'] : 0,
                'priority' => 2, // Medium
                'description' => 'Tự động tạo từ phiếu phát triển mẫu ' . $ptm_no,
                'billable' => 0,
                'visible_to_client' => 0
            ];
            
            $task_id = $this->tasks_model->add($task_data);
            
            if ($task_id) {
                // Resolve staff members based on departments (rooms) and roles
                $staff_ids = [];
                
                // 1. Resolve departments (rooms)
                if (!empty($category_task['departments'])) {
                    $room_ids = explode(',', $category_task['departments']);
                    
                    // Get roles in these rooms
                    $this->db->select('roleid');
                    $this->db->from('tblroles');
                    $this->db->where_in('id_room', $room_ids);
                    $roles = $this->db->get()->result_array();
                    $role_ids = array_column($roles, 'roleid');
                    
                    if (!empty($role_ids)) {
                        $this->db->select('staffid');
                        $this->db->from('tblstaff');
                        $this->db->where_in('role', $role_ids);
                        $this->db->where('active', 1);
                        $staff = $this->db->get()->result_array();
                        $staff_ids = array_merge($staff_ids, array_column($staff, 'staffid'));
                    }
                    
                    // Get staff linked to these rooms via tblstaff_departments and tbldepartments.room_id
                    $this->db->select('departmentid');
                    $this->db->from('tbldepartments');
                    $this->db->where_in('room_id', $room_ids);
                    $depts = $this->db->get()->result_array();
                    $dept_ids = array_column($depts, 'departmentid');
                    if (!empty($dept_ids)) {
                        $this->db->select('staffid');
                        $this->db->from('tblstaff_departments');
                        $this->db->where_in('departmentid', $dept_ids);
                        $staff_dept = $this->db->get()->result_array();
                        $staff_ids = array_merge($staff_ids, array_column($staff_dept, 'staffid'));
                    }
                }
                
                // 2. Resolve roles
                $roles_direct = [];
                if (!empty($category_task['role_id_1'])) {
                    $roles_direct[] = $category_task['role_id_1'];
                }
                if (!empty($category_task['role_id_2'])) {
                    $roles_direct[] = $category_task['role_id_2'];
                }
                
                // 3. Resolve role_processing from process children
                $this->db->select('role_processing');
                $this->db->from('tblcategory_tasks_process_child');
                $this->db->where('id_category_tasks', $category_task['id']);
                $this->db->where('role_processing IS NOT NULL');
                $this->db->where('role_processing !=', 0);
                $process_children = $this->db->get()->result_array();
                if (!empty($process_children)) {
                    $roles_direct = array_merge($roles_direct, array_column($process_children, 'role_processing'));
                }
                
                if (!empty($roles_direct)) {
                    $this->db->select('staffid');
                    $this->db->from('tblstaff');
                    $this->db->where_in('role', $roles_direct);
                    $this->db->where('active', 1);
                    $staff_direct = $this->db->get()->result_array();
                    $staff_ids = array_merge($staff_ids, array_column($staff_direct, 'staffid'));
                }
                
                $staff_ids = array_unique($staff_ids);
                
                if (!empty($staff_ids)) {
                    foreach ($staff_ids as $staff_id) {
                        $this->db->insert('tbltask_assigned', [
                            'taskid' => $task_id,
                            'staffid' => $staff_id,
                            'assigned_from' => get_staff_user_id() ? get_staff_user_id() : $created_by
                        ]);
                    }
                }
            }
        }
    }

    public function view_modal($id)
    {
        $ptm_row = $this->db->get_where('tbl_orders_ptm', ['id' => $id])->row_array();
        if (empty($ptm_row)) {
            echo "Phiếu YCPTM không tồn tại";
            return;
        }
        $ptm_no = $ptm_row['ptm_no'];
        
        // Fetch all details for this ptm_id
        $this->db->select('tbl_orders_ptm_detail.*, tbl_orders_ptm.ptm_no, tbl_orders_ptm.date, tbl_orders_ptm.order_id, tbl_orders_ptm.created_by, tbl_orders_ptm.date_created, tbl_orders_ptm.date_updated, tbl_orders.reference_no as order_ref, tbl_orders.customer_name as client_name, tbl_products.code as product_code, tbl_products.name as product_name, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as creator_name');
        $this->db->from('tbl_orders_ptm_detail');
        $this->db->join('tbl_orders_ptm', 'tbl_orders_ptm.id = tbl_orders_ptm_detail.ptm_id');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_orders_ptm.order_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_orders_ptm_detail.product_id');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_orders_ptm.created_by', 'left');
        $this->db->where('tbl_orders_ptm_detail.ptm_id', $id);
        $ptms = $this->db->get()->result_array();
        
        $products_data = [];
        $order = null;
        foreach ($ptms as $ptm) {
            if (!$order) {
                $order = $this->orders_model->rowOrderById($ptm['order_id']);
            }
            $this->db->select('quote_id_chonse');
            $this->db->from('tbl_orders_sub');
            $this->db->where('order_id', $ptm['order_id']);
            $order_sub = $this->db->get()->row_array();
            $quote_id = !empty($order_sub) ? $order_sub['quote_id_chonse'] : 0;
            $product_id = $ptm['product_id'];
            
            $this->db->select('*');
            $this->db->from('tbl_quote_items');
            $this->db->where('quote_id', $quote_id);
            $this->db->where('item_id', $product_id);
            $quote_item = $this->db->get()->row_array();
            
            if (empty($quote_item)) {
                $quote_item = $this->db->get_where('tbl_products', ['id' => $product_id])->row_array();
                $quote_item['item_code'] = $quote_item['code'];
                $quote_item['item_name'] = $quote_item['name'];
                $quote_item['data_price_json'] = '';
            }
            
            $arrDataJson = !empty($quote_item['data_price_json']) ? json_decode($quote_item['data_price_json'], true) : [];
            $client = $this->clients_model->get($order['customer_id']);
            
            $this->db->select('tblcustomers_groups.code, tblcustomers_groups.name');
            $this->db->from('tblcustomer_groups');
            $this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tblcustomer_groups.groupid');
            $this->db->where('tblcustomer_groups.customer_id', $order['customer_id']);
            $group = $this->db->get()->row_array();
            
            // Product unit
            $this->db->select('unit_id, conversion_unit');
            $this->db->from('tbl_products');
            $this->db->where('id', $ptm['product_id']);
            $product_row = $this->db->get()->row_array();
            
            $final_unit_id = 0;
            if (!empty($quote_item['unit_id'])) {
                $final_unit_id = $quote_item['unit_id'];
            } elseif (!empty($quote_item['conversion_unit'])) {
                $final_unit_id = $quote_item['conversion_unit'];
            } elseif (!empty($product_row['unit_id'])) {
                $final_unit_id = $product_row['unit_id'];
            } elseif (!empty($product_row['conversion_unit'])) {
                $final_unit_id = $product_row['conversion_unit'];
            }
            
            $this->db->select('unit');
            $this->db->from('tblunits');
            $this->db->where('unitid', $final_unit_id);
            $unit_row = $this->db->get()->row_array();
            $unit_name = !empty($unit_row) ? $unit_row['unit'] : '';
    
            $height = isset($arrDataJson['height']) ? $arrDataJson['height'] : (!empty($quote_item['height']) ? $quote_item['height'] / 10 : 0);
            $width = isset($arrDataJson['width']) ? $arrDataJson['width'] : (!empty($quote_item['wide']) ? $quote_item['wide'] / 10 : 0);
            $margin = isset($arrDataJson['corner_boundary_height']) ? $arrDataJson['corner_boundary_height'] : (!empty($quote_item['loss']) ? $quote_item['loss'] : 0);
            
            // Materials
            $materials = [];
            $material_ids = [];
            if (!empty($arrDataJson['arrItemsNPL'])) {
                foreach ($arrDataJson['arrItemsNPL'] as $npl) {
                    $material_ids[] = $npl['item_id'];
                }
            }
            if (!empty($arrDataJson['ItemsPrice'])) {
                foreach ($arrDataJson['ItemsPrice'] as $ip) {
                    if ($ip['type_price'] == 'materials') {
                        $material_ids[] = $ip['item_id_price'];
                    }
                }
            }
            if (!empty($arrDataJson['itemsPriceBackside'])) {
                foreach ($arrDataJson['itemsPriceBackside'] as $ipb) {
                    if ($ipb['type_price_backside'] == 'materials') {
                        $material_ids[] = $ipb['item_id_price_backside'];
                    }
                }
            }
            $material_ids = array_unique($material_ids);
            if (!empty($material_ids)) {
                $this->db->select('id, code, name');
                $this->db->from('tbl_materials');
                $this->db->where_in('id', $material_ids);
                $materials = $this->db->get()->result_array();
            }
            
            // Fetch stages (Section D) - Strictly from data_price_json
            $stages = [];
            $stage_ids = [];
            if (!empty($arrDataJson['itemsStagesProducts'])) {
                foreach ($arrDataJson['itemsStagesProducts'] as $stg) {
                    if (isset($stg['type_price_products']) && $stg['type_price_products'] == 'stages') {
                        if (!empty($stg['stage_id_price_products'])) {
                            $stage_ids[] = $stg['stage_id_price_products'];
                        } elseif (!empty($stg['item_id_price_products'])) {
                            $stage_ids[] = $stg['item_id_price_products'];
                        }
                    }
                }
            }
            $stage_keys = ['arrItemsLStage', 'arrItemsPsStage', 'arrItemsIStage', 'arrItemsPStage', 'arrItemsDStage', 'arrItemsCStage'];
            foreach ($stage_keys as $sk) {
                if (!empty($arrDataJson[$sk])) {
                    foreach ($arrDataJson[$sk] as $stg) {
                        if (!empty($stg['item_id'])) {
                            $stage_ids[] = $stg['item_id'];
                        } elseif (!empty($stg['stage_id'])) {
                            $stage_ids[] = $stg['stage_id'];
                        }
                    }
                }
            }
            $stage_ids = array_unique($stage_ids);
            if (!empty($stage_ids)) {
                $this->db->select('id, name as stage_name');
                $this->db->from('tbl_stages');
                $this->db->where_in('id', $stage_ids);
                $stages_rows = $this->db->get()->result_array();
                
                $stages_by_id = [];
                foreach ($stages_rows as $row) {
                    $stages_by_id[$row['id']] = $row['stage_name'];
                }
                
                $number = 1;
                foreach ($stage_ids as $id) {
                    if (isset($stages_by_id[$id])) {
                        $stages[] = [
                            'number' => $number++,
                            'stage_name' => $stages_by_id[$id]
                        ];
                    }
                }
            }
            
            $products_data[$product_id] = [
                'ptm' => $ptm,
                'client_classify' => (!empty($client) && $client->code_type) ? $client->code_type : 'TN',
                'brand_code' => !empty($group) ? $group['code'] : '',
                'brand_name' => !empty($group) ? $group['name'] : '',
                'client_code' => !empty($client) ? $client->code_client : '',
                'client_name' => !empty($client) ? $client->company : '',
                'product_code' => $quote_item['item_code'],
                'product_name' => $quote_item['item_name'],
                'unit_name' => $unit_name,
                'product_height' => $height,
                'product_width' => $width,
                'product_margin' => $margin,
                'materials' => $materials,
                'stages' => $stages
            ];
        }
        
        $data['ptm_no'] = $ptm_no;
        $data['order_id'] = !empty($ptms) ? $ptms[0]['order_id'] : '';
        $data['order_ref'] = !empty($ptms) ? $ptms[0]['order_ref'] : '';
        $data['date'] = !empty($ptms) ? $ptms[0]['date'] : '';
        $data['creator_name'] = !empty($ptms) ? $ptms[0]['creator_name'] : '';
        $data['date_created'] = !empty($ptms) ? $ptms[0]['date_created'] : '';
        $data['date_updated'] = !empty($ptms) ? $ptms[0]['date_updated'] : '';
        $data['products_data'] = $products_data;
        $data['id'] = $id; // Pass original id for export/delete actions
        
        $this->load->view('admin/ptm/view_modal', $data);
    }

    public function export_excel($id)
    {
        if (!has_permission('ptm', '', 'export')) {
            access_denied();
        }
        restore_error_handler();
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);
        ini_set('display_errors', 0);
        
        $ptm_row = $this->db->get_where('tbl_orders_ptm', ['id' => $id])->row_array();
        if (empty($ptm_row)) {
            set_alert('danger', 'Phiếu YCPTM không tồn tại');
            redirect(admin_url('ptm'));
        }
        
        // Fetch all details for this ptm_id
        $this->db->select('tbl_orders_ptm_detail.*, tbl_orders_ptm.ptm_no, tbl_orders_ptm.date, tbl_orders_ptm.order_id, tbl_orders_ptm.created_by, tbl_orders_ptm.date_created, tbl_orders_ptm.date_updated, tbl_orders.reference_no as order_ref, tbl_orders.customer_name as client_name, tbl_products.code as product_code, tbl_products.name as product_name, tbl_products.images as product_image, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as creator_name');
        $this->db->from('tbl_orders_ptm_detail');
        $this->db->join('tbl_orders_ptm', 'tbl_orders_ptm.id = tbl_orders_ptm_detail.ptm_id');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_orders_ptm.order_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_orders_ptm_detail.product_id');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_orders_ptm.created_by', 'left');
        $this->db->where('tbl_orders_ptm_detail.ptm_id', $id);
        $ptm_products = $this->db->get()->result_array();
        
        // Export using PHPExcel
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $inputFileName = 'uploads/template/PTM.xlsx';
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        $templateSheet = $objPHPExcel->getActiveSheet();
        foreach ($ptm_products as $ptm) {
            // Fetch quote item details for sections B, C, D
            $order = $this->orders_model->rowOrderById($ptm['order_id']);
            $this->db->select('quote_id_chonse');
            $this->db->from('tbl_orders_sub');
            $this->db->where('order_id', $ptm['order_id']);
            $order_sub = $this->db->get()->row_array();
            $quote_id = !empty($order_sub) ? $order_sub['quote_id_chonse'] : 0;
            
            $this->db->select('*');
            $this->db->from('tbl_quote_items');
            $this->db->where('quote_id', $quote_id);
            $this->db->where('item_id', $ptm['product_id']);
            $quote_item = $this->db->get()->row_array();
            
            if (empty($quote_item)) {
                $quote_item = $this->db->get_where('tbl_products', ['id' => $ptm['product_id']])->row_array();
                $quote_item['item_code'] = $quote_item['code'];
                $quote_item['item_name'] = $quote_item['name'];
                $quote_item['data_price_json'] = '';
            }
            
            $arrDataJson = !empty($quote_item['data_price_json']) ? json_decode($quote_item['data_price_json'], true) : [];
            $client = $this->clients_model->get($order['customer_id']);
            
            $this->db->select('tblcustomers_groups.code, tblcustomers_groups.name');
            $this->db->from('tblcustomer_groups');
            $this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tblcustomer_groups.groupid');
            $this->db->where('tblcustomer_groups.customer_id', $order['customer_id']);
            $group = $this->db->get()->row_array();
            
            // Product unit
            $this->db->select('unit_id, conversion_unit');
            $this->db->from('tbl_products');
            $this->db->where('id', $ptm['product_id']);
            $product_row = $this->db->get()->row_array();
            
            $final_unit_id = 0;
            if (!empty($quote_item['unit_id'])) {
                $final_unit_id = $quote_item['unit_id'];
            } elseif (!empty($quote_item['conversion_unit'])) {
                $final_unit_id = $quote_item['conversion_unit'];
            } elseif (!empty($product_row['unit_id'])) {
                $final_unit_id = $product_row['unit_id'];
            } elseif (!empty($product_row['conversion_unit'])) {
                $final_unit_id = $product_row['conversion_unit'];
            }
            
            $this->db->select('unit');
            $this->db->from('tblunits');
            $this->db->where('unitid', $final_unit_id);
            $unit_row = $this->db->get()->row_array();
            $unit_name = !empty($unit_row) ? mb_strtolower($unit_row['unit'], 'UTF-8') : '';
    
            $height = isset($arrDataJson['height']) ? $arrDataJson['height'] : (!empty($quote_item['height']) ? $quote_item['height'] / 10 : 0);
            $width = isset($arrDataJson['width']) ? $arrDataJson['width'] : (!empty($quote_item['wide']) ? $quote_item['wide'] / 10 : 0);
            $margin = isset($arrDataJson['corner_boundary_height']) ? $arrDataJson['corner_boundary_height'] : (!empty($quote_item['loss']) ? $quote_item['loss'] : 0);
            
            // Materials
            $materials = [];
            $material_ids = [];
            if (!empty($arrDataJson['arrItemsNPL'])) {
                foreach ($arrDataJson['arrItemsNPL'] as $npl) {
                    $material_ids[] = $npl['item_id'];
                }
            }
            if (!empty($arrDataJson['ItemsPrice'])) {
                foreach ($arrDataJson['ItemsPrice'] as $ip) {
                    if ($ip['type_price'] == 'materials') {
                        $material_ids[] = $ip['item_id_price'];
                    }
                }
            }
            if (!empty($arrDataJson['itemsPriceBackside'])) {
                foreach ($arrDataJson['itemsPriceBackside'] as $ipb) {
                    if ($ipb['type_price_backside'] == 'materials') {
                        $material_ids[] = $ipb['item_id_price_backside'];
                    }
                }
            }
            $material_ids = array_unique($material_ids);
            if (!empty($material_ids)) {
                $this->db->select('id, code, name');
                $this->db->from('tbl_materials');
                $this->db->where_in('id', $material_ids);
                $materials = $this->db->get()->result_array();
            }
            
            // Fetch stages (Section D) - Strictly from data_price_json
            $stages = [];
            $stage_ids = [];
            if (!empty($arrDataJson['itemsStagesProducts'])) {
                foreach ($arrDataJson['itemsStagesProducts'] as $stg) {
                    if (isset($stg['type_price_products']) && $stg['type_price_products'] == 'stages') {
                        if (!empty($stg['stage_id_price_products'])) {
                            $stage_ids[] = $stg['stage_id_price_products'];
                        } elseif (!empty($stg['item_id_price_products'])) {
                            $stage_ids[] = $stg['item_id_price_products'];
                        }
                    }
                }
            }
            $stage_keys = ['arrItemsLStage', 'arrItemsPsStage', 'arrItemsIStage', 'arrItemsPStage', 'arrItemsDStage', 'arrItemsCStage'];
            foreach ($stage_keys as $sk) {
                if (!empty($arrDataJson[$sk])) {
                    foreach ($arrDataJson[$sk] as $stg) {
                        if (!empty($stg['item_id'])) {
                            $stage_ids[] = $stg['item_id'];
                        } elseif (!empty($stg['stage_id'])) {
                            $stage_ids[] = $stg['stage_id'];
                        }
                    }
                }
            }
            $stage_ids = array_unique($stage_ids);
            if (!empty($stage_ids)) {
                $this->db->select('id, name as stage_name');
                $this->db->from('tbl_stages');
                $this->db->where_in('id', $stage_ids);
                $stages_rows = $this->db->get()->result_array();
                
                $stages_by_id = [];
                foreach ($stages_rows as $row) {
                    $stages_by_id[$row['id']] = $row['stage_name'];
                }
                
                $number = 1;
                foreach ($stage_ids as $id) {
                    if (isset($stages_by_id[$id])) {
                        $stages[] = [
                            'number' => $number++,
                            'stage_name' => $stages_by_id[$id]
                        ];
                    }
                }
            }

            // Clone sheet
            $sheet = clone $templateSheet;
            $reflector = new ReflectionClass('PHPExcel_Worksheet');
            $property = $reflector->getProperty('_parent');
            $property->setAccessible(true);
            $property->setValue($sheet, null);

            $sheetTitle = substr($ptm['product_code'], 0, 30);
            $sheetTitle = str_replace(['*', ':', '?', '/', '\\', '[', ']'], '-', $sheetTitle);
            $sheet->setTitle($sheetTitle);
            
            $objPHPExcel->addSheet($sheet);
            
            // Re-link drawings in the cloned sheet to point to the cloned sheet itself
            foreach ($sheet->getDrawingCollection() as $drawing) {
                $reflectorDrawing = new ReflectionClass('PHPExcel_Worksheet_BaseDrawing');
                $propertyDrawing = $reflectorDrawing->getProperty('_worksheet');
                $propertyDrawing->setAccessible(true);
                $propertyDrawing->setValue($drawing, $sheet);
            }
            
            // On-the-fly crop image1.jpeg to create correct banner
            $src_banner = 'uploads/template/image1.jpeg';
            $dest_banner = 'uploads/template/temp_banner.jpeg';
            if (file_exists($src_banner) && !file_exists($dest_banner)) {
                $im_banner = @imagecreatefromjpeg($src_banner);
                if ($im_banner) {
                    $cropped_banner = imagecrop($im_banner, ['x' => 410, 'y' => 0, 'width' => 1284, 'height' => 120]);
                    if ($cropped_banner !== FALSE) {
                        imagejpeg($cropped_banner, $dest_banner);
                        imagedestroy($cropped_banner);
                    }
                    imagedestroy($im_banner);
                }
            }

            // Remove the incorrect drawing 'Picture 2'
            $drawings = $sheet->getDrawingCollection();
            $iterator = $drawings->getIterator();
            while ($iterator->valid()) {
                $drawing = $iterator->current();
                if ($drawing->getName() == 'Picture 2') {
                    $reflectorDrawing = new ReflectionClass('PHPExcel_Worksheet_BaseDrawing');
                    $propertyDrawing = $reflectorDrawing->getProperty('_worksheet');
                    $propertyDrawing->setAccessible(true);
                    $propertyDrawing->setValue($drawing, null);
                    
                    $drawings->offsetUnset($iterator->key());
                } else {
                    $iterator->next();
                }
            }

            // Insert the correct cropped banner drawing
            if (file_exists($dest_banner)) {
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setName('Picture 2');
                $objDrawing->setDescription('Logo Thanh Danh Banner');
                $objDrawing->setPath($dest_banner);
                $objDrawing->setCoordinates('H1');
                $objDrawing->setResizeProportional(false);
                $objDrawing->setWidth(541);
                $objDrawing->setHeight(50);
                $objDrawing->setOffsetX(0);
                $objDrawing->setOffsetY(0);
                $objDrawing->setWorksheet($sheet);
            }
            
            // Date & PTM No
            $sheet->setCellValue('C4', 'Ngày: ' . _d($ptm['date']));
            $sheet->setCellValue('Q4', $ptm['ptm_no']);
            
            // Section A
            $sheet->setCellValue('F8', $ptm['mau']);
            $sheet->setCellValue('F10', $ptm['yeu_cau_dac_biet']);
            $sheet->setCellValue('F13', $ptm['tieu_chuan_test']);
            $sheet->setCellValue('F15', $ptm['cach_xem_mau']);
            
            // Quy Trình Đo Màu - write each step to its own row (F17, F18, F19, F20)
            $color_processes = explode(', ', $ptm['quy_trinh_do_mau']);
            $start_row = 17;
            foreach ($color_processes as $index => $cp) {
                $current_row = $start_row + $index;
                if ($current_row > 20) {
                    break;
                }
                $sheet->setCellValue('F' . $current_row, trim($cp));
            }
            
            $sheet->setCellValue('F22', $ptm['tieu_chuan_do_mau']);
            $sheet->setCellValue('F24', $ptm['dia_chi_giao_hang']);
            
            // Section B
            $sheet->setCellValue('F28', (!empty($client) && $client->code_type) ? $client->code_type : 'TN');
            $sheet->setCellValue('H30', !empty($group) ? $group['code'] : '');
            $sheet->setCellValue('H32', !empty($group) ? $group['name'] : '');
            $sheet->setCellValue('F34', !empty($client) ? $client->code_client : '');
            $sheet->setCellValue('F36', !empty($client) ? $client->company : '');
            
            $sheet->setCellValue('R28', $quote_item['item_code']);
            $sheet->setCellValue('R30', $quote_item['item_name']);
            $sheet->setCellValue('P32', $unit_name);
            $sheet->getStyle('P32')->getFont()->setBold(false);
            $sheet->setCellValue('S34', $height . ' cm');
            $sheet->setCellValue('Z34', $width . ' cm');
            $sheet->setCellValue('Q36', $margin . ' cm');
            
            // Offset tracker for dynamic row insertions
            $offset = 0;

            // Section C - Materials list
            $cnt_mat = count($materials);
            if ($cnt_mat > 3) {
                $num_insert_mat = $cnt_mat - 3;
                $sheet->insertNewRowBefore(45, $num_insert_mat);
                
                // Format new rows C:U merged and styles cloned
                for ($i = 0; $i < $num_insert_mat; $i++) {
                    $new_row = 45 + $i;
                    $sheet->mergeCells("C{$new_row}:U{$new_row}");
                    $sheet->duplicateStyle($sheet->getStyle('C44'), "C{$new_row}:U{$new_row}");
                    $sheet->duplicateStyle($sheet->getStyle('B44'), "B{$new_row}");
                }
                $offset += $num_insert_mat;
            }

            // Write materials
            $start_mat_row = 42;
            foreach ($materials as $index => $m) {
                $current_mat_row = $start_mat_row + $index;
                $sheet->setCellValue('B' . $current_mat_row, $index + 1);
                $sheet->setCellValue('C' . $current_mat_row, mb_strtolower($m['name'], 'UTF-8'));

                // Align left and make normal (not bold) for STT and Name
                $sheet->getStyle('B' . $current_mat_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B' . $current_mat_row)->getFont()->setBold(false);

                $sheet->getStyle('C' . $current_mat_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C' . $current_mat_row)->getFont()->setBold(false);
            }
            
            // Section D - Stages list
            $cnt_stages = count($stages);
            if ($cnt_stages > 11) {
                $num_insert_stages = $cnt_stages - 11;
                $sheet->insertNewRowBefore(60 + $offset, $num_insert_stages);
                
                // Format new rows C:U merged and styles cloned
                for ($i = 0; $i < $num_insert_stages; $i++) {
                    $new_row = 60 + $offset + $i;
                    $sheet->mergeCells("C{$new_row}:U{$new_row}");
                    $sheet->duplicateStyle($sheet->getStyle("C" . (49 + $offset)), "C{$new_row}:U{$new_row}");
                    $sheet->duplicateStyle($sheet->getStyle("B" . (49 + $offset)), "B{$new_row}");
                }
                $offset += $num_insert_stages;
            }

            // Write stages
            $start_stage_row = 49 + ($cnt_mat > 3 ? $cnt_mat - 3 : 0);
            foreach ($stages as $index => $stage) {
                $current_stage_row = $start_stage_row + $index;
                $sheet->setCellValue('B' . $current_stage_row, $stage['number']);
                $sheet->setCellValue('C' . $current_stage_row, $stage['stage_name']);
            }
            
            // Set header label B44 (with offset of materials shift)
            $sheet->setCellValue('C' . (48 + ($cnt_mat > 3 ? $cnt_mat - 3 : 0)), 'Tên Công đoạn Sản Xuất');

            // Bottom fields (shifted by offset)
            $sheet->setCellValue('F' . (61 + $offset), $ptm['tem_dong_goi']);
            $sheet->setCellValue('E' . (63 + $offset), $ptm['dong_kien']);
            $sheet->setCellValue('F' . (65 + $offset), $ptm['carton']);
            
            $sheet->setCellValue('S' . (61 + $offset), $ptm['creator_name']); // overwrites (26)
            $sheet->setCellValue('S' . (63 + $offset), _d($ptm['date_created'])); // overwrites (27)
            $sheet->setCellValue('S' . (65 + $offset), !empty($ptm['date_updated']) ? _d($ptm['date_updated']) : ''); // overwrites (28)

            // Product Image (U8 - Y15)
            $product_image = $ptm['product_image'];
            if (!empty($product_image)) {
                $imgs = explode(',', $product_image);
                $img_file = trim($imgs[0]);
                $img_path = 'uploads/products/' . $img_file;
                if (file_exists($img_path)) {
                    list($img_width, $img_height) = getimagesize($img_path);
                    if ($img_width > 0 && $img_height > 0) {
                        $max_width = 180;
                        $max_height = 130;
                        $ratio = $img_width / $img_height;
                        
                        if ($max_width / $max_height > $ratio) {
                            // height is the limiting factor
                            $new_height = $max_height;
                            $new_width = $max_height * $ratio;
                        } else {
                            // width is the limiting factor
                            $new_width = $max_width;
                            $new_height = $max_width / $ratio;
                        }
                        
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Product Image');
                        $objDrawing->setDescription('Product Image');
                        $objDrawing->setPath($img_path);
                        $objDrawing->setCoordinates('U8');
                        
                        $objDrawing->setResizeProportional(true);
                        $objDrawing->setHeight((int)$new_height);
                        
                        // Center it inside the bounding box
                        $offset_x = (int)(($max_width - $new_width) / 2) + 5;
                        $offset_y = (int)(($max_height - $new_height) / 2) + 5;
                        $objDrawing->setOffsetX($offset_x);
                        $objDrawing->setOffsetY($offset_y);
                        
                        $objDrawing->setWorksheet($sheet);
                    }
                }
            }

        }
        
        // Remove the original template sheet
        $objPHPExcel->removeSheetByIndex(0);

        // Output file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PTM_' . $ptm_products[0]['ptm_no'] . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function delete($id)
    {
        if (!has_permission('ptm', '', 'delete')) {
            access_denied();
        }
        $task = $this->db->query("SELECT * FROM " . db_prefix() . "tasks WHERE rel_type = 'PTM' AND rel_id = ?", [$id])->row_array();
        if(!empty($task)) {
            set_alert('danger', 'Phiếu YCPTM đang có phiếu công việc không thể xóa');
        }

        $ptm = $this->db->get_where('tbl_orders_ptm', ['id' => $id])->row_array();
        if (!empty($ptm)) {
            $order_id = $ptm['order_id'];
            
            // Delete details
            $this->db->where('ptm_id', $id);
            $this->db->delete('tbl_orders_ptm_detail');
            
            // Delete general
            $this->db->where('id', $id);
            if ($this->db->delete('tbl_orders_ptm')) {
                // Update orders ptm status to 0
                $this->db->where('id', $order_id);
                $this->db->update('tbl_orders', ['ptm' => 0]);
                
                set_alert('success', 'Xóa Phiếu YCPTM thành công');
            } else {
                set_alert('danger', 'Xóa Phiếu YCPTM thất bại');
            }
        }
        redirect(admin_url('ptm'));
    }
}
