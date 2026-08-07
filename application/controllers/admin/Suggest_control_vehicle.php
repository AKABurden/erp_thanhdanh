<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_control_vehicle extends AdminController
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
        $this->load->model('transfer_model');

        $this->preViewSuggestControlVehicle = true;
        $this->preViewOwnSuggestControlVehicle = true;
        $this->preAddSuggestControlVehicle = true;
        $this->preEditSuggestControlVehicle = true;
        $this->preApproveSuggestControlVehicle = true;
        $this->preDeleteSuggestControlVehicle = true;
    }

    public function index(){
		redirect(admin_url('request_control_vehicle_bussiness'));
        if (!$this->preViewSuggestControlVehicle && !$this->preViewOwnSuggestControlVehicle){
            access_denied();
        }
        $data['title'] = _l('dt_suggest_control_vehicle');
        $this->load->view('admin/suggest_control_vehicle/index', $data);
    }

    public function getSuggestControlVehicles(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_control_vehicle.id as id',
            'tbl_suggest_control_vehicle.reference_no as reference_no',
            'tbl_suggest_control_vehicle.date as date',
            'tblclients.company as company',
            'tbl_suggest_control_vehicle.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_control_vehicle';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_suggest_control_vehicle.customer_id',
        ];

        if (!$this->preViewSuggestControlVehicle) {
            array_push($where,'AND (tbl_suggest_control_vehicle.created_by = '.get_staff_user_id().')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            array_push($where, "AND tbl_suggest_control_vehicle.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_control_vehicle.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_control_vehicle/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['company']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_control_vehicle/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestControlVehicle ? '<a href="' . base_url('admin/suggest_control_vehicle/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestControlVehicle ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_control_vehicle/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div style="width: 150px">'.$actions.'</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0){
        $data = [];
        $this->db->select('tbl_suggest_control_vehicle.*');
        $this->db->from('tbl_suggest_control_vehicle');
        $this->db->where('tbl_suggest_control_vehicle.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_control_vehicle.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('customer_id', lang("Khách hàng"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_control_vehicle');
                    $date = to_sql_date($this->input->post('date'), true);
                    $customer_id = $this->input->post('customer_id');
                    $branch_id = $this->input->post('branch_id');
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $delivery_id = $this->input->post('delivery_id')[$value];
                            $delivery_item_id = $this->input->post('delivery_item_id')[$value];
                            $item_id = $this->input->post('item_id')[$value];
                            $dtDeliveryItem = get_table_where('tbl_delivery_items',['id' => $delivery_item_id],'','row_array');
                            if (empty($dtDeliveryItem)){
                                continue;
                            }
                            $type_item = $dtDeliveryItem['type_item'];
                            $number_ky = number_unformat($this->input->post('number_ky')[$value]);
                            $total_kien = number_unformat($this->input->post('total_kien')[$value]);
                            $total_ky = number_unformat($this->input->post('total_ky')[$value]);
                            $quota_vehicle = number_unformat($this->input->post('quota_vehicle')[$value]);
                            $vehicle = ($this->input->post('vehicle')[$value]);
                            $route = ($this->input->post('route')[$value]);
                            $transport_unit_id = !empty($this->input->post('transport_unit_id')[$value]) ? $this->input->post('transport_unit_id')[$value] : 0;
                            $price = !empty($this->input->post('price')[$value]) ? number_unformat($this->input->post('price')[$value]) : 0;
                            $standard = !empty($this->input->post('standard')[$value]) ? ($this->input->post('standard')[$value]) : null;

                            $items[] = [
                                'delivery_id' => $delivery_id,
                                'delivery_item_id' => $delivery_item_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'number_ky' => $number_ky,
                                'total_kien' => $total_kien,
                                'total_ky' => $total_ky,
                                'quota_vehicle' => $quota_vehicle,
                                'vehicle' => $vehicle,
                                'route' => $route,
                                'transport_unit_id' => $transport_unit_id,
                                'price' => $price,
                                'amount' => $price,
                                'standard' => $standard,
                            ];

                        }
                    }
                    if (empty($items)){
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_no_items');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'customer_id' => $customer_id,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_control_vehicle',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_control_vehicle') == $reference_no) {
                            updateReference('suggest_control_vehicle');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_control_vehicle_id'] = $id;
                            $this->db->insert('tbl_suggest_control_vehicle_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_control_vehicle',
                            'table_obj' => 'tbl_suggest_control_vehicle',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu điều xe') . ' [' . $reference_no . ']',
                            'actions' => 'add'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);die();
            } else {
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $customer_id = $this->input->post('customer_id');
                    $branch_id = $this->input->post('branch_id');
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $delivery_id = $this->input->post('delivery_id')[$value];
                            $delivery_item_id = $this->input->post('delivery_item_id')[$value];
                            $item_id = $this->input->post('item_id')[$value];
                            $dtDeliveryItem = get_table_where('tbl_delivery_items',['id' => $delivery_item_id],'','row_array');
                            if (empty($dtDeliveryItem)){
                                continue;
                            }
                            $type_item = $dtDeliveryItem['type_item'];
                            $number_ky = number_unformat($this->input->post('number_ky')[$value]);
                            $total_kien = number_unformat($this->input->post('total_kien')[$value]);
                            $total_ky = number_unformat($this->input->post('total_ky')[$value]);
                            $quota_vehicle = number_unformat($this->input->post('quota_vehicle')[$value]);
                            $vehicle = ($this->input->post('vehicle')[$value]);
                            $route = ($this->input->post('route')[$value]);
                            $transport_unit_id = !empty($this->input->post('transport_unit_id')[$value]) ? $this->input->post('transport_unit_id')[$value] : 0;
                            $price = !empty($this->input->post('price')[$value]) ? number_unformat($this->input->post('price')[$value]) : 0;
                            $standard = !empty($this->input->post('standard')[$value]) ? ($this->input->post('standard')[$value]) : null;
                            $suggest_control_vehicle_item_id = !empty($this->input->post('suggest_control_vehicle_item')[$value]) ? $this->input->post('suggest_control_vehicle_item')[$value] : 0;

                            $items[] = [
                                'id' => $suggest_control_vehicle_item_id,
                                'delivery_id' => $delivery_id,
                                'delivery_item_id' => $delivery_item_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'number_ky' => $number_ky,
                                'total_kien' => $total_kien,
                                'total_ky' => $total_ky,
                                'quota_vehicle' => $quota_vehicle,
                                'vehicle' => $vehicle,
                                'route' => $route,
                                'transport_unit_id' => $transport_unit_id,
                                'price' => $price,
                                'amount' => $price,
                                'standard' => $standard,
                            ];

                        }
                    }
                    if (empty($items)){
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_no_items');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'date' => $date,
                        'customer_id' => $customer_id,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_control_vehicle',$fields);
                    if ($success){
                        $this->db->where('suggest_control_vehicle_id',$id);
                        $this->db->delete('tbl_suggest_control_vehicle_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_control_vehicle_id'] = $id;
                            $this->db->insert('tbl_suggest_control_vehicle_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_control_vehicle',
                            'table_obj' => 'tbl_suggest_control_vehicle',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu điều xe') . ' [' . $dtData['reference_no'] . ']',
                            'actions' => 'edit'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Sửa thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);die();
            }
        } else {
            if (empty($id)){
                if (!$this->preAddSuggestControlVehicle){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_control_vehicle');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_control_vehicle'), 'page' => lang('dt_suggest_control_vehicle')), array('link' => '#', 'page' => lang('dt_add_suggest_control_vehicle'))];
            } else {
                if (!$this->preEditSuggestControlVehicle){
                    accessDenied(true);
                }

                $this->db->select('tbl_suggest_control_vehicle_item.*');
                $this->db->from('tbl_suggest_control_vehicle_item');
                $this->db->where('tbl_suggest_control_vehicle_item.suggest_control_vehicle_id',$id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_control_vehicle');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_control_vehicle'), 'page' => lang('dt_suggest_control_vehicle')), array('link' => '#', 'page' => lang('dt_edit_suggest_control_vehicle'))];
            }
        }
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_control_vehicle');
        $data['dtTrans'] = get_table_where('tblsuppliers',['type' => 1]);
        $this->load->view('admin/suggest_control_vehicle/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_control_vehicle');

        $this->db->select('tbl_suggest_control_vehicle.*');
        $this->db->from('tbl_suggest_control_vehicle');
        $this->db->where('tbl_suggest_control_vehicle.id',$id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_control_vehicle_item.*,
            tbl_deliveries.reference_no as reference_no,
            tblsuppliers.company as company,
        ');
        $this->db->from('tbl_suggest_control_vehicle_item');
        $this->db->join('tbl_deliveries','tbl_deliveries.id = tbl_suggest_control_vehicle_item.delivery_id','left');
        $this->db->join('tblsuppliers','tblsuppliers.id = tbl_suggest_control_vehicle_item.transport_unit_id','left');
        $this->db->where('tbl_suggest_control_vehicle_item.suggest_control_vehicle_id',$id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/suggest_control_vehicle/view',$data);
    }

    public function agree(){
        if (!$this->preApproveSuggestPlanOvertime) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_plan_overtime_item.*');
        $this->db->from('tbl_suggest_plan_overtime_item');
        $this->db->where('tbl_suggest_plan_overtime_item.id',$suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if (($dtData['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data); return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'date_status' => $date_status,
                'staff_status' => $staff_status,
            ];

            $this->db->where('id',$suggest_id);
            $up = $this->db->update('tbl_suggest_plan_overtime_item',$options);
            if ($up) {
                $get_code = get_table_where('tbl_suggest_plan_overtime', array('id' => $dtData['suggest_plan_overtime_id']), '', 'row_array');
                insertActivityLog([
                    'type_parent_obj' => 'suggest_plan_overtime_item',
                    'table_obj' => 'tbl_suggest_plan_overtime_item',
                    'id_obj' => $suggest_id,
                    'name_obj' => $get_code['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu kế hoạch tăng ca') . ' [' . $get_code['reference_no'] . ']',
                    'actions' => 'approved'
                ]);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function delete($id){
        if (!$this->preDeleteSuggestControlVehicle){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_control_vehicle.*');
        $this->db->from('tbl_suggest_control_vehicle');
        $this->db->where('tbl_suggest_control_vehicle.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_control_vehicle');
        if ($success){
            $this->db->where('tbl_suggest_control_vehicle_item.suggest_control_vehicle_id',$id);
            $this->db->delete('tbl_suggest_control_vehicle_item');

            $this->db->where('tbl_moderation_control_vehicle.suggest_control_vehicle',$id);
            $this->db->delete('tbl_moderation_control_vehicle');

            insertActivityLog([
                'type_parent_obj' => 'suggest_control_vehicle',
                'table_obj' => 'tbl_suggest_control_vehicle',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu điều xe') . ' [' . $dtData['reference_no'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function searchItemDeliveryByCustomer(){
        $data = [];
        $term = $this->input->get('term');
        $customer_id = $this->input->get('customer_id');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_delivery_items.id as id, 
            CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
            tbl_products.code as code_item,
            tbl_products.name as name_item,
            tbl_products.quantity_sheet_bale as quantity_sheet_bale,
            tbl_deliveries.id as delivery_id,
            tbl_deliveries.reference_no as reference_no,
            tbl_delivery_items.item_id as item_id,
            tbl_deliveries.reference_no as reference_no,
            tbl_orders.transporter_id as transporter_id,
        ', false);
        $this->db->from('tbl_delivery_items');
        $this->db->join('tbl_deliveries','tbl_deliveries.id = tbl_delivery_items.delivery_id','inner');
        $this->db->join('tbl_orders','tbl_orders.id = tbl_deliveries.order_id','inner');
        $this->db->join('tbl_products','tbl_products.id = tbl_delivery_items.item_id','inner');
        $this->db->where('tbl_deliveries.customer_id',$customer_id);
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $term);
            $this->db->or_like('tbl_products.name', $term);
            $this->db->or_like('tbl_deliveries.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        $resultsNew = [];
        if (!empty($results)){
            foreach ($results as $key => $value){
                if (!empty($resultsNew[$value['reference_no']])){
                    $resultsNew[$value['reference_no']]['items'][] = $value;
                } else {
                    $resultsNew[$value['reference_no']]['items'][] = $value;
                }
            }
        }
        foreach ($resultsNew as $key => $value){
            $data['results'][] =
                [
                    'text' => $key,
                    'children' => $value['items']
                ];
        }
        echo json_encode($data);
    }

    public function exportExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();


            $tb_category_client = "(
                SELECT
                   tblcustomer_groups.customer_id,
                   GROUP_CONCAT(tblcustomers_groups.name) as name_group     
                FROM tblcustomer_groups
                JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                GROUP BY tblcustomer_groups.customer_id
            ) tb_category_client";

            $this->db->select('
                tbl_suggest_control_vehicle.id as id,
                tbl_suggest_control_vehicle.reference_no as reference_no,
                tbl_suggest_control_vehicle.date as date,
                tbl_deliveries.reference_no as reference_no_delivery,
                tblclients.company as company,
                COALESCE(tb_category_client.name_group,"") as name_group,
                tbl_suggest_control_vehicle_item.number_ky as number_ky,
                tbl_suggest_control_vehicle_item.total_kien as total_kien,
                tbl_suggest_control_vehicle_item.total_ky as total_ky,
                tbl_suggest_control_vehicle_item.quota_vehicle as quota_vehicle,
                tbl_suggest_control_vehicle_item.vehicle as vehicle,
                tblshipping_client.address as address_delivery,
                tbl_suggest_control_vehicle_item.route as route,
                tblsuppliers.company as company_supplier,
                tbl_suggest_control_vehicle_item.price as price,
                tbl_suggest_control_vehicle_item.amount as amount,
                tbl_suggest_control_vehicle_item.standard as standard,
                tbl_suggest_control_vehicle_item.item_id as item_id,
                tbl_suggest_control_vehicle_item.type_item as type_item,
            ');
            $this->db->from('tbl_suggest_control_vehicle');
            $this->db->join('tblclients', 'tblclients.userid = tbl_suggest_control_vehicle.customer_id', 'left');
            $this->db->join('tbl_suggest_control_vehicle_item','tbl_suggest_control_vehicle_item.suggest_control_vehicle_id = tbl_suggest_control_vehicle.id');
            $this->db->join('tblsuppliers','tblsuppliers.id = tbl_suggest_control_vehicle_item.transport_unit_id','left');
            $this->db->join('tbl_deliveries','tbl_deliveries.id = tbl_suggest_control_vehicle_item.delivery_id','left');
            $this->db->join('tblshipping_client','tblshipping_client.id = tbl_deliveries.address_delivery_id','left');
            $this->db->join($tb_category_client,'tb_category_client.customer_id = tblclients.userid','left');

            if (!$this->preViewSuggestControlVehicle) {
                $this->db->where('(tbl_suggest_control_vehicle.created_by = '.get_staff_user_id().')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_control_vehicle.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_control_vehicle.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_control_vehicle.id desc');
            $dtData = $this->db->get()->result_array();


            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('PHIẾU YÊU CẦU ĐIỀU XE'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:U1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Hình Ảnh SP');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mã Sản Phẩm');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Phiếu Giao Hàng')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Tên KH');
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Phân Loại Khách Hàng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Số Con/Kiện');
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Số Ký /Kiện')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Tổng Số Kiện')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Tổng Số Ký')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Định Mức Phương Tiện')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Phương Tiện')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Địa Chỉ Giao Hàng')->getStyle("0$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Mã Lộ Trình')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Đơn Vị Vận Chuyển')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Đơn Giá')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Thành Tiền')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Tiêu Chuẩn/Quy Định')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'QR')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:U$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $rowBegin = $sttRow;
            $this->load->library('ciqrcode');
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $item_id = $value['item_id'];
                    $type_item = $value['type_item'];
                    $info = null;
                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($item_id);
                    }
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    if (!empty($info['images'])) {
                        $images = 'uploads/products/' . $info['images'];
                    }
                    if (empty($images)) {
                        $images = 'assets/images/tnh/no_image.png';
                    }
                    if (!empty($images) && file_exists($images)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($images);
                        $objDrawing1->setWidth(30);
                        $objDrawing1->setHeight(30);
                        $objDrawing1->setOffsetX(20);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates('B' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(30);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", '')->getStyle("B$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($info['code']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['reference_no_delivery']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", ($value['company']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", ($value['name_group']))->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",!empty($value['number_ky']) ? ($value['number_ky']) : 0)->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin",!empty($info['quantity_sheet_bale']) ? ($value['quantity_sheet_bale']) : 0)->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['total_kien'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['total_ky'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['quota_vehicle'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['vehicle'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", ($value['address_delivery']))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", ($value['route']))->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", ($value['company_supplier']))->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", formatMoney($value['price']))->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", formatMoney($value['amount']))->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $value['standard'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])){
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_control_vehicle||'.$value['id'];
                        $this->db->where('id',$value['id']);
                        $this->db->update('tbl_suggest_control_vehicle',['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_control_vehicle/';
                    if (!file_exists($folder)) {
                        mkdir($folder);
                        fopen($folder . 'index.html', 'w');
                    }
                    if (!file_exists($folder . 'qrcode' . '/')) {
                        mkdir($folder . 'qrcode' . '/');
                        fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
                    }
                    $params['data'] = $code;
                    $params['level'] = 'H';
                    $params['size'] = 40;
                    $params['savename'] = $folder.'qrcode/'. $qr . '.png';
                    $this->ciqrcode->generate($params);
                    $img = ($folder.'qrcode/'. $qr . '.png');
                    if (!empty($img)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($img);
                        $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates('U' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", '')->getStyle("U$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:U$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:M$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_dieu_xe') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }
}