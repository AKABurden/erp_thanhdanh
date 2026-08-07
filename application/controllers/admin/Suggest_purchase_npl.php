<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_purchase_npl extends AdminController
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

        $this->preViewSuggestPurchaseNPL = true;
        $this->preViewOwnSuggestPurchaseNPL = true;
        $this->preAddSuggestPurchaseNPL = true;
        $this->preEditSuggestPurchaseNPL = true;
        $this->preApproveSuggestnPurchaseNPL = true;
        $this->preDeleteSuggestPurchaseNPL= true;
    }

    public function index(){
        if (!$this->preViewSuggestPurchaseNPL && !$this->preViewOwnSuggestPurchaseNPL){
            access_denied();
        }
        $data['title'] = _l('dt_suggest_purchase_npl');
        $this->load->view('admin/suggest_purchase_npl/index', $data);
    }

    public function getSuggestPurchaseNpl(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_purchase_npl.id as id',
            'tbl_suggest_purchase_npl.reference_no as reference_no',
            'tbl_suggest_purchase_npl.date as date',
            'tbl_purchase_request_material.reference_no as reference_no_purchase_request',
            'tbl_productions_orders.reference_no as reference_no_po',
            'tbl_orders.reference_no as reference_no_order',
            'tblsuppliers.company as company',
            'tbl_suggest_purchase_npl.date_import as date_import',
            'tbl_suggest_purchase_npl.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_purchase_npl';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_purchase_request_material ON tbl_purchase_request_material.id = tbl_suggest_purchase_npl.purchase_request_material_id',
            'INNER JOIN tblsuppliers ON tblsuppliers.id = tbl_suggest_purchase_npl.supplier_id',
            'INNER JOIN tbl_orders ON tbl_orders.id = tbl_purchase_request_material.order_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_purchase_request_material.po_id',
        ];

        if (!$this->preViewSuggestPurchaseNPL) {
            array_push($where,'AND (tbl_suggest_purchase_npl.created_by = '.get_staff_user_id().')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            array_push($where, "AND tbl_suggest_purchase_npl.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_purchase_npl.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_purchase_request_material.id as purchase_request_material_id'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_purchase_npl/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/purchase_request_material/view/' . $aRow['purchase_request_material_id']) . '" data-toggle="modal" data-target="#myModal">' . ($aRow['reference_no_purchase_request']) . '</a></div>';
            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['reference_no_po']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['reference_no_order']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['company']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . _dhau($aRow['date_import']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_purchase_npl/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestPurchaseNPL? '<a href="' . base_url('admin/suggest_purchase_npl/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestPurchaseNPL ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_purchase_npl/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $row[] = '<div style="min-width: 120px">'.$actions.'</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0){
        $data = [];
        $this->db->select('tbl_suggest_purchase_npl.*');
        $this->db->from('tbl_suggest_purchase_npl');
        $this->db->where('tbl_suggest_purchase_npl.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_purchase_npl.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('date_import', lang("Ngày nhập"), 'required');
            $this->form_validation->set_rules('supplier_id', lang("Nhà cung cấp"), 'required');
            $this->form_validation->set_rules('purchase_request_material_id', lang("Phiếu yêu cầu mua"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_purchase_npl');
                    $date = to_sql_date($this->input->post('date'), true);
                    $date_import = $this->input->post('date_import');
                    $supplier_id = $this->input->post('supplier_id');
                    $purchase_request_material_id = $this->input->post('purchase_request_material_id');
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $dtPurchaseRequest = get_table_where('tbl_purchase_request_material',['id' => $purchase_request_material_id],'','row_array');
                    $items = [];
                    $totalQuantity = 0;
                    $totalQuantityImport = 0;
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $purchase_request_material_item_id = !empty($this->input->post('purchase_request_material_item_id')[$value]) ? $this->input->post('purchase_request_material_item_id')[$value] : 0;
                            if (empty($purchase_request_material_item_id)){
                                continue;
                            }
                            $dtData = get_table_where('tbl_purchase_request_material_item',['id' => $purchase_request_material_item_id],'','row_array');
                            $item_id = $dtData['item_id'];
                            $type_item = $dtData['type_item'];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $quantity_import = number_unformat($this->input->post('quantity_import')[$value]);
                            $detail = ($this->input->post('detail')[$value]);
                            $standard = ($this->input->post('standard')[$value]);

                            $items[] = [
                                'purchase_request_material_item_id' => $purchase_request_material_item_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'quantity_import' => $quantity_import,
                                'detail' => $detail,
                                'standard' => $standard,
                            ];

                            $totalQuantity += $quantity;
                            $totalQuantityImport += $quantity_import;
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
                        'date_import' => to_sql_date($date_import),
                        'supplier_id' => $supplier_id,
                        'purchase_request_material_id' => $purchase_request_material_id,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $dtPurchaseRequest['branch_id'],
                        'total_quantity' => $totalQuantity,
                        'total_quantity_import' => $totalQuantityImport,
                    ];
                    $this->db->insert('tbl_suggest_purchase_npl',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_purchase_npl') == $reference_no) {
                            updateReference('suggest_purchase_npl');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_purchase_npl_id'] = $id;
                            $this->db->insert('tbl_suggest_purchase_npl_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_purchase_npl',
                            'table_obj' => 'tbl_suggest_purchase_npl',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu nhập kho npl') . ' [' . $reference_no . ']',
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
                    $date_import = $this->input->post('date_import');
                    $supplier_id = $this->input->post('supplier_id');
                    $purchase_request_material_id = $this->input->post('purchase_request_material_id');
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $dtPurchaseRequest = get_table_where('tbl_purchase_request_material',['id' => $purchase_request_material_id],'','row_array');
                    $items = [];
                    $totalQuantity = 0;
                    $totalQuantityImport = 0;
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $purchase_request_material_item_id = !empty($this->input->post('purchase_request_material_item_id')[$value]) ? $this->input->post('purchase_request_material_item_id')[$value] : 0;
                            if (empty($purchase_request_material_item_id)){
                                continue;
                            }
                            $dtPurchaseRequestItem = get_table_where('tbl_purchase_request_material_item',['id' => $purchase_request_material_item_id],'','row_array');
                            $item_id = $dtPurchaseRequestItem['item_id'];
                            $type_item = $dtPurchaseRequestItem['type_item'];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $quantity_import = number_unformat($this->input->post('quantity_import')[$value]);
                            $detail = ($this->input->post('detail')[$value]);
                            $standard = ($this->input->post('standard')[$value]);
                            $suggest_purchase_npl_item_id = !empty($this->input->post('suggest_purchase_npl_item_id')[$value]) ? $this->input->post('suggest_purchase_npl_item_id')[$value] : 0;
                            $items[] = [
                                'id' => $suggest_purchase_npl_item_id,
                                'purchase_request_material_item_id' => $purchase_request_material_item_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'quantity_import' => $quantity_import,
                                'detail' => $detail,
                                'standard' => $standard,
                            ];
                            $totalQuantity += $quantity;
                            $totalQuantityImport += $quantity_import;
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
                        'date_import' => to_sql_date($date_import),
                        'supplier_id' => $supplier_id,
                        'purchase_request_material_id' => $purchase_request_material_id,
                        'note' => $note,
                        'branch_id' => $dtPurchaseRequest['branch_id'],
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'total_quantity' => $totalQuantity,
                        'total_quantity_import' => $totalQuantityImport,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_purchase_npl',$fields);
                    if ($success){
                        $this->db->where('suggest_purchase_npl_id',$id);
                        $this->db->delete('tbl_suggest_purchase_npl_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_purchase_npl_id'] = $id;
                            $this->db->insert('tbl_suggest_purchase_npl_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_purchase_npl',
                            'table_obj' => 'tbl_suggest_purchase_npl',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu nhập kho npl') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestPurchaseNPL){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_purchase_npl');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_purchase_npl'), 'page' => lang('dt_suggest_purchase_npl')), array('link' => '#', 'page' => lang('dt_add_suggest_purchase_npl'))];
            } else {
                if (!$this->preEditSuggestPurchaseNPL){
                    accessDenied(true);
                }

                $this->db->select('tbl_suggest_purchase_npl_item.*');
                $this->db->from('tbl_suggest_purchase_npl_item');
                $this->db->where('tbl_suggest_purchase_npl_item.suggest_purchase_npl_id',$id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_purchase_npl');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_purchase_npl'), 'page' => lang('dt_suggest_purchase_npl')), array('link' => '#', 'page' => lang('dt_edit_suggest_purchase_npl'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_purchase_npl');
        $data['dtSuppliers'] = get_table_where('tblsuppliers');
        $this->load->view('admin/suggest_purchase_npl/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_purchase_npl');

        $this->db->select('tbl_suggest_purchase_npl.*,
            tbl_orders.reference_no as reference_no_order,
            tbl_productions_orders.reference_no as reference_no_po,
            tbl_purchase_request_material.reference_no as reference_no_purchase_request,
        ');
        $this->db->from('tbl_suggest_purchase_npl');
        $this->db->join('tbl_purchase_request_material','tbl_purchase_request_material.id = tbl_suggest_purchase_npl.purchase_request_material_id','inner');
        $this->db->join('tbl_orders','tbl_orders.id = tbl_purchase_request_material.order_id','inner');
        $this->db->join('tbl_productions_orders','tbl_productions_orders.id = tbl_purchase_request_material.po_id','inner');
        $this->db->where('tbl_suggest_purchase_npl.id',$id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_purchase_npl_item.*,
            tbl_purchase_request_material_item.totalheight as totalheight
        ');
        $this->db->from('tbl_suggest_purchase_npl_item');
        $this->db->join('tbl_purchase_request_material_item','tbl_purchase_request_material_item.id = tbl_suggest_purchase_npl_item.purchase_request_material_item_id','inner');
        $this->db->where('tbl_suggest_purchase_npl_item.suggest_purchase_npl_id',$id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/suggest_purchase_npl/view',$data);
    }


    public function delete($id){
        if (!$this->preDeleteSuggestPurchaseNPL){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_purchase_npl.*');
        $this->db->from('tbl_suggest_purchase_npl');
        $this->db->where('tbl_suggest_purchase_npl.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }


        if ($dtData['status'] == 1) {
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã được duyệt không thể xóa !');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_purchase_npl');
        if ($success){
            $this->db->where('tbl_suggest_purchase_npl_item.suggest_purchase_npl_id',$id);
            $this->db->delete('tbl_suggest_purchase_npl_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_purchase_npl',
                'table_obj' => 'tbl_suggest_purchase_npl',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu nhập kho npl') . ' [' . $dtData['reference_no'] . ']',
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

    public function searchPurchaseRequestMaterial($id = 0){
        $term = $this->input->get('term');
        $supplier_id = $this->input->get('supplier_id');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_purchase_request_material.id as id, 
            CONCAT(tbl_purchase_request_material.reference_no) as text,
            tbl_purchase_request_material.supplier_id as supplier_id
        ', false);
        $this->db->from('tbl_purchase_request_material');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_purchase_request_material.reference_no', $term);
            $this->db->group_end();
        }
        if (!empty($supplier_id)) {
            $this->db->where('tbl_purchase_request_material.supplier_id', $supplier_id);
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Phiếu yêu cầu mua'), 'children' => $dtResult];
        if (!empty($id)){
            $dtData = get_table_where('tbl_purchase_request_material',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no']];
        }
        echo json_encode($data);
    }

    public function searchItemsByRequestMaterial(){
        $term = $this->input->get('term');
        $purchase_request_material_id = !empty($this->input->get('purchase_request_material_id')) ? $this->input->get('purchase_request_material_id') : 0;
        $limit = get_option('select2_limit');

        $results = [];

        $this->db->select('
            tbl_purchase_request_material_item.id as id, 
            tbl_purchase_request_material_item.item_id as item_id, 
            tbl_purchase_request_material_item.type_item as type_item, 
            tbl_purchase_request_material_item.quabtity_purchase as quabtity_purchase,
            CONCAT(tbl_materials.name,"(",tbl_materials.code,")") as text,
            tbl_materials.code as code_item,
            tbl_materials.name as name_item
        ', false);
        $this->db->from('tbl_purchase_request_material_item');
        $this->db->join('tbl_materials','tbl_materials.id = tbl_purchase_request_material_item.item_id','inner');
        $this->db->where('tbl_purchase_request_material_item.purchase_request_material_id',$purchase_request_material_id);
        $this->db->where('tbl_purchase_request_material_item.type_item',"materials");
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $term);
            $this->db->or_like('tbl_materials.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $resultsNPL = $this->db->get()->result_array();
        if (!empty($resultsNPL)){
            $results = array_merge($results,$resultsNPL);
        }

        $this->db->select('
            tbl_purchase_request_material_item.id as id, 
            tbl_purchase_request_material_item.item_id as item_id, 
            tbl_purchase_request_material_item.type_item as type_item, 
            tbl_purchase_request_material_item.quabtity_purchase as quabtity_purchase,
            CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
            tbl_products.code as code_item,
            tbl_products.name as name_item
        ', false);
        $this->db->from('tbl_purchase_request_material_item');
        $this->db->join('tbl_products','tbl_products.id = tbl_purchase_request_material_item.item_id','inner');
        $this->db->where('tbl_purchase_request_material_item.purchase_request_material_id',$purchase_request_material_id);
        $this->db->where('tbl_purchase_request_material_item.type_item',"products");
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $term);
            $this->db->or_like('tbl_products.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $resultsTP = $this->db->get()->result_array();
        if (!empty($resultsTP)){
            $results = array_merge($results,$resultsTP);
        }

        $data = [];
        $data['results'][] =
            [
                'text' => 'Mặt hàng',
                'children' => $results
            ];
        echo json_encode($data);
    }

    public function searchStage($id = 0){
        $term = $this->input->get('term');
        $params = $this->input->get('params');
        $pod_id = !empty($params['pod_id']) ? $params['pod_id'] : 0;
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_stages.id as id, 
            tbl_stages.name as text,
        ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id','inner');
        $this->db->join('tbl_productions_orders_details','tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id','inner');
        $this->db->join('tbl_stages','tbl_stages.id = tbl_productions_orders_items_stages.stage_id','inner');
        $this->db->where('tbl_productions_orders_details.id',$pod_id);
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_stages.name', $term);
            $this->db->or_like('tbl_stages.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        $data['results'][] =
            [
                'text' => lang('Công đoạn'),
                'children' => $results
            ];
        if (!empty($id)){
            $dtData = get_table_where('tbl_stages',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['name']];
        }
        echo json_encode($data);
    }

    public function exportExcel()
    {
        $columsExcel = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
        ];
        if ($this->input->post('export_excel')) {

            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_dt/phieu_yeu_cau_nhap_kho_npl.xlsx';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $BStylenumber = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '111112'),
                    'size'  => 11,
                    'name'  => 'Times New Roman'
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            );
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $row = 2;
            $staff_id = get_staff_user_id();
            $this->db->select('tbl_suggest_purchase_npl.*,
                tbl_orders.reference_no as reference_no_order,
                tbl_productions_orders.reference_no as reference_no_po,
                tbl_purchase_request_material.reference_no as reference_no_purchase_request,
                tblsuppliers.company as company,
                tbl_suggest_purchase_npl_item.item_id,
                tbl_suggest_purchase_npl_item.type_item,
                tbl_suggest_purchase_npl_item.quantity,
                tbl_suggest_purchase_npl_item.quantity_import,
                tbl_suggest_purchase_npl_item.detail,
                tbl_suggest_purchase_npl_item.standard,
                tbl_purchase_request_material_item.totalheight,
            ');
            $this->db->from('tbl_suggest_purchase_npl');
            $this->db->join('tbl_suggest_purchase_npl_item','tbl_suggest_purchase_npl_item.suggest_purchase_npl_id = tbl_suggest_purchase_npl.id','inner');
            $this->db->join('tbl_purchase_request_material_item','tbl_purchase_request_material_item.id = tbl_suggest_purchase_npl_item.purchase_request_material_item_id','inner');
            $this->db->join('tbl_purchase_request_material','tbl_purchase_request_material.id = tbl_suggest_purchase_npl.purchase_request_material_id','inner');
            $this->db->join('tbl_orders','tbl_orders.id = tbl_purchase_request_material.order_id','inner');
            $this->db->join('tbl_productions_orders','tbl_productions_orders.id = tbl_purchase_request_material.po_id','inner');
            $this->db->join('tblsuppliers','tblsuppliers.id = tbl_suggest_purchase_npl.supplier_id','inner');

            if (!$this->preViewSuggestPurchaseNPL) {
                $this->db->where('(tbl_suggest_purchase_npl.created_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_purchase_npl.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_purchase_npl.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_suggest_purchase_npl.id asc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $item_id = $value['item_id'];
                $type_item = $value['type_item'];
                $info = null;
                $dtCategory = null;
                $dtSpecies = null;
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($item_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    $dtCategory = get_table_where('tbl_category_products',['id' => $info['category_id']],'','row_array');
                    $dtSpecies = get_table_where('tbl_species',['id' => $info['species']],'','row_array');
                } elseif ($type_item == 'materials'){
                    $info = $this->items_model->rowMaterial($item_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    $dtCategory = get_table_where('tbl_category_items',['id' => $info['category_id']],'','row_array');
                    $dtSpecies = get_table_where('tbl_species',['id' => $info['species']],'','row_array');
                }
                $row++;
                $dem++;
                $colStt = 0;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $dem);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, _dt($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['reference_no_purchase_request']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['reference_no_po']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['reference_no_order']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['company']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($info['code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($info['name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($dtCategory['name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, !empty($dtSpecies) ? $dtSpecies['name'] : '');
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['detail'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, formatNumber($value['quantity']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, formatNumber($value['quantity_import']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($info['longs']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['totalheight']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, !empty($value['date_import']) ? _dhau($value['date_import']) : '', PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($info['time_stock']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['standard']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;

                if (!empty($value['barcode'])){
                    $code = $value['barcode'];
                } else {
                    $code = 'suggest_purchase_npl||'.$value['id'];
                    $this->db->where('id',$value['id']);
                    $this->db->update('tbl_suggest_purchase_npl',['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/suggest_purchase_npl/';
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
                    $objDrawing1->setWidth(90);
                    $objDrawing1->setHeight(65);
                    $objDrawing1->setOffsetX(20);
                    $objDrawing1->setOffsetY(2);
                    $objDrawing1->setCoordinates($columsExcel[$colStt] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, '')->getStyle($columsExcel[$colStt] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle("$columsExcel[0]$row:$columsExcel[$colStt]$row")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle("$columsExcel[0]$row:$columsExcel[$colStt]$row")->applyFromArray([
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
            }

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_yeu_cau_ke_nhap_kho_npl') . '.xls';
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

    public function searchPoAndOrder($id = 0,$object_type = ''){
        $term = $this->input->get('term');
        if (empty($object_type)) {
            $object_type = $this->input->get('object_type');
        }
        $limit = get_option('select2_limit');
        if ($object_type == 'po') {
            $this->db->select('
                tbl_productions_orders.id as id, 
                CONCAT(tbl_productions_orders.reference_no) as text,
            ', false);
            $this->db->from('tbl_productions_orders');
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('tbl_productions_orders.reference_no', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $dtResult = $this->db->get()->result_array();
        } else {
            $this->db->select('
                tbl_orders.id as id, 
                CONCAT(tbl_orders.reference_no) as text,
            ', false);
            $this->db->from('tbl_orders');
            $this->db->where('tbl_orders.status','approved');
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('tbl_orders.reference_no', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $dtResult = $this->db->get()->result_array();
        }

        $data['results'][] = ['text' => lang('Đơn hàng/Lệnh sản xuất'), 'children' => $dtResult];
        if (!empty($id)){
            $id = explode('_',$id);
            if ($object_type == 'po'){
                $this->db->select('tbl_productions_orders.*');
                $this->db->from('tbl_productions_orders');
                $this->db->where_in('tbl_productions_orders.id',$id);
                $dtData = $this->db->get()->result_array();
            } else {
                $this->db->select('tbl_orders.*');
                $this->db->from('tbl_orders');
                $this->db->where_in('tbl_orders.id',$id);
                $dtData = $this->db->get()->result_array();
            }
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $data['row'][] = ['id' => $value['id'], 'text' => $value['reference_no']];
                }
            }
        }
        echo json_encode($data);
    }
}