<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_plan_outsource extends AdminController
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

        $this->preViewSuggestPlanOutsource = true;
        $this->preViewOwnSuggestPlanOutsource = true;
        $this->preAddSuggestPlanOutsource = true;
        $this->preEditSuggestPlanOutsource = true;
        $this->preApproveSuggestPlanOutsource = true;
        $this->preDeleteSuggestPlanOutsource = true;
    }

    public function index(){
        if (!$this->preViewSuggestPlanOutsource && !$this->preViewOwnSuggestPlanOutsource){
            access_denied();
        }
        $data['title'] = _l('dt_suggest_plan_outsource');
        $this->load->view('admin/suggest_plan_outsource/index', $data);
    }

    public function getSuggestPlanOutsource(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_plan_outsource.id as id',
            'tbl_suggest_plan_outsource.reference_no as reference_no',
            'tbl_suggest_plan_outsource.date as date',
            'tbl_productions_orders.reference_no as reference_no_productions',
            'tbl_suggest_plan_outsource.staff_plan as staff_plan',
            'tbl_suggest_plan_outsource.status as status',
//            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
//             FROM tblproduction_report
//             WHERE tblproduction_report.object_id = tbl_suggest_plan_outsource.id AND tblproduction_report.object_type = "suggest_plan_outsource"
//            ) as name_report',
            'tbl_suggest_plan_outsource.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_plan_outsource';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_suggest_plan_outsource.po_id',
        ];

        if (!$this->preViewSuggestPlanOutsource) {
            array_push($where,'AND (tbl_suggest_plan_outsource.created_by = '.get_staff_user_id().' OR tbl_suggest_plan_outsource.staff_plan = '.get_staff_user_id().' )');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            array_push($where, "AND tbl_suggest_plan_outsource.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_plan_outsource.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_plan_outsource.staff_status',
            'tbl_suggest_plan_outsource.date_status',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_plan_outsource/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">'.($aRow['reference_no_productions']).'</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['staff_plan']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 1)\' id=\'agree\' suggest_id=\''.$aRow['id'].'\' value=\'1\' class=\'btn btn-success\'>'.lang('tnh_agree').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-danger po">'.lang('Chưa duyệt').'</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 0)\' id=\'agree\' suggest_id=\''.$aRow['id'].'\' value=\'0\' class=\'btn btn-danger\'>'.lang('Hủy duyệt').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-success po">'.lang('Đã duyệt').'</span></div>';
                $_data.= '<div style="margin-top: 5px"> Người duyệt: '.get_staff_full_name($aRow['staff_status']).'</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left" style="width: 100px">'.$_data.'</div>';
//            $arrReport = $aRow['name_report'];
//            $htmlReport = '';
//            if (!empty($arrReport)){
//                $arrReport = explode('||',$arrReport);
//                if (!empty($arrReport)){
//                    foreach ($arrReport as $kk => $vv){
//                        $vv = explode('__',$vv);
//                        $htmlReport .= '<a class="c_modal" href="'.(admin_url('production_report/modal/' . $vv[1])).'">' . $vv[0] .'</a>';
//                    }
//                }
//            }
//
//            if ($aRow['status'] == 1) {
//                $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=suggest_plan_outsource') . '" class="btn btn-info">Báo cáo không phù hợp</a></div>
//                <div style="margin-top: 5px">' . $htmlReport . '</div>
//            ';
//            } else {
//                $row[] = '';
//            }
            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_plan_outsource/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestPlanOutsource ? '<a href="' . base_url('admin/suggest_plan_outsource/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestPlanOutsource ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_plan_outsource/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $row[] = '<div style="width: 120px">'.$actions.'</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0){
        $data = [];
        $this->db->select('tbl_suggest_plan_outsource.*');
        $this->db->from('tbl_suggest_plan_outsource');
        $this->db->where('tbl_suggest_plan_outsource.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_plan_outsource.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('po_id', lang("Lệnh sản xuất"), 'required');
//            $this->form_validation->set_rules('order_id', lang("Đơn hàng"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('staff_plan', lang("Người lập kế hoạch"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_plan_outsource');
                    $date = to_sql_date($this->input->post('date'), true);
                    $po_id = $this->input->post('po_id');
                    $branch_id = $this->input->post('branch_id');
                    $order_id = !empty($this->input->post('order_id')) ? $this->input->post('order_id') : 0;
                    $staff_plan = !empty($this->input->post('staff_plan')) ? $this->input->post('staff_plan') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
//                            $pod_id = $this->input->post('pod_id')[$value];
//                            $order_item_id = $this->input->post('order_item_id')[$value];
//                            $this->db->select('tbl_productions_orders_items.items_id as items_id,tbl_productions_orders_items.type_items');
//                            $this->db->from('tbl_productions_orders_details');
//                            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
//                            $this->db->where('tbl_productions_orders_details.id',$pod_id);
//                            $dtPod = $this->db->get()->row_array();
//                            if (empty($dtPod)){
//                                continue;
//                            }
//                            $item_id = $dtPod['items_id'];
//                            $type_item = $dtPod['type_items'];
							
							
                            $item_id = $this->input->post('items_id')[$value];
							$type_item = $this->input->post('type_items')[$value];
							
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $supplier_id = ($this->input->post('suppliers_id')[$value]);
                            $detail = ($this->input->post('detail')[$value]);
                            $price = number_unformat($this->input->post('price')[$value]);
                            $shipping_unit_outsource = ($this->input->post('shipping_unit_outsource')[$value]);
                            $transport_outsource = ($this->input->post('transport_outsource')[$value]);
                            $date_start_outsource = to_sql_date($this->input->post('date_start_outsource')[$value]);
                            $date_end_outsource = to_sql_date($this->input->post('date_end_outsource')[$value]);
                            $staff_id = ($this->input->post('staff_id')[$value]);
//                            $result_id = ($this->input->post('result_id')[$value]);
                            $id_stage_price_list_detail = ($this->input->post('id_stage_price_list_detail')[$value]);

                            $amount = $quantity * $price;

                            $items[] = [
//                                'order_item_id' => $order_item_id,
//                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'supplier_id' => $supplier_id,
                                'price' => $price,
                                'amount' => $amount,
                                'shipping_unit_outsource' => $shipping_unit_outsource,
                                'transport_outsource' => $transport_outsource,
                                'detail' => $detail,
                                'staff_id' => $staff_id,
                                'date_start_outsource' => $date_start_outsource,
                                'date_end_outsource' => $date_end_outsource,
//                                'result_id' => $result_id,
                                'id_stage_price_list_detail' => !empty($id_stage_price_list_detail) ? $id_stage_price_list_detail : 0,
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
                        'po_id' => $po_id,
//                        'order_id' => $order_id,
                        'staff_plan' => $staff_plan,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_plan_outsource',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_plan_outsource') == $reference_no) {
                            updateReference('suggest_plan_outsource');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_plan_outsource_id'] = $id;
                            $this->db->insert('tbl_suggest_plan_outsource_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_plan_outsource',
                            'table_obj' => 'tbl_suggest_plan_outsource',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu kế hoạch gia công') . ' [' . $reference_no . ']',
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
                    $po_id = $this->input->post('po_id');
                    $branch_id = $this->input->post('branch_id');
                    $order_id = !empty($this->input->post('order_id')) ? $this->input->post('order_id') : 0;
                    $staff_plan = !empty($this->input->post('staff_plan')) ? $this->input->post('staff_plan') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){

//                            $pod_id = $this->input->post('pod_id')[$value];
//                            $order_item_id = $this->input->post('order_item_id')[$value];
//                            $this->db->select('tbl_productions_orders_items.items_id as items_id,tbl_productions_orders_items.type_items');
//                            $this->db->from('tbl_productions_orders_details');
//                            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
//                            $this->db->where('tbl_productions_orders_details.id',$pod_id);
//                            $dtPod = $this->db->get()->row_array();
//                            if (empty($dtPod)){
//                                continue;
//                            }
//                            $item_id = $dtPod['items_id'];
//                            $type_item = $dtPod['type_items'];
	
	
	
							$item_id = $this->input->post('items_id')[$value];
							$type_item = $this->input->post('type_items')[$value];
							
							
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $supplier_id = ($this->input->post('suppliers_id')[$value]);
                            $detail = ($this->input->post('detail')[$value]);
                            $price = number_unformat($this->input->post('price')[$value]);
                            $shipping_unit_outsource = ($this->input->post('shipping_unit_outsource')[$value]);
                            $transport_outsource = ($this->input->post('transport_outsource')[$value]);
                            $date_start_outsource = to_sql_date($this->input->post('date_start_outsource')[$value]);
                            $date_end_outsource = to_sql_date($this->input->post('date_end_outsource')[$value]);
                            $staff_id = ($this->input->post('staff_id')[$value]);
//                            $result_id = ($this->input->post('result_id')[$value]);

                            $amount = $quantity * $price;
                            $suggest_plan_outsource_item_id = !empty($this->input->post('suggest_plan_outsource_item_id')[$value]) ? $this->input->post('suggest_plan_outsource_item_id')[$value] : 0;
							$id_stage_price_list_detail = ($this->input->post('id_stage_price_list_detail')[$value]);
                            $items[] = [
                                'id' => $suggest_plan_outsource_item_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'supplier_id' => $supplier_id,
                                'price' => $price,
                                'amount' => $amount,
                                'shipping_unit_outsource' => $shipping_unit_outsource,
                                'transport_outsource' => $transport_outsource,
                                'detail' => $detail,
                                'staff_id' => $staff_id,
                                'date_start_outsource' => $date_start_outsource,
                                'date_end_outsource' => $date_end_outsource,
                                'id_stage_price_list_detail' => $id_stage_price_list_detail,
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
                        'po_id' => $po_id,
//                        'order_id' => $order_id,
                        'staff_plan' => $staff_plan,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_plan_outsource',$fields);
                    if ($success){
                        $this->db->where('suggest_plan_outsource_id',$id);
                        $this->db->delete('tbl_suggest_plan_outsource_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_plan_outsource_id'] = $id;
                            $this->db->insert('tbl_suggest_plan_outsource_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_plan_outsource',
                            'table_obj' => 'tbl_suggest_plan_outsource',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu kế hoạch gia công') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestPlanOutsource){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_plan_outsource');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_outsource'), 'page' => lang('dt_suggest_plan_outsource')), array('link' => '#', 'page' => lang('dt_add_suggest_plan_outsource'))];
            } else {
                if (!$this->preEditSuggestPlanOutsource){
                    accessDenied(true);
                }

                if ($dtData['status'] == 1){
                    set_alert('danger',  'Phiếu đã duyệt không thể sửa !');
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $this->db->select('tbl_suggest_plan_outsource_item.*');
                $this->db->from('tbl_suggest_plan_outsource_item');
                $this->db->where('tbl_suggest_plan_outsource_item.suggest_plan_outsource_id',$id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_plan_outsource');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_outsource'), 'page' => lang('dt_suggest_plan_outsource')), array('link' => '#', 'page' => lang('dt_edit_suggest_plan_outsource'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_plan_outsource');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtStaff'] = get_table_where('tblstaff',['active' => 1]);
        $this->load->view('admin/suggest_plan_outsource/detail',$data);
    }
	
	public function searchPoItems($id = 0){
		$term = $this->input->get('term');
		$limit = get_option('select2_limit');
		$this->db->select('
            tbl_productions_orders.id as id,
            CONCAT(tbl_productions_orders.reference_no) as text,
         
        ', false);
		$this->db->from('tbl_productions_orders');
		// $this->db->where('EXISTS(
        //     SELECT 1
        //     FROM tbl_productions_plan_orders
        //     WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.object_type = "orders"
        // )');
		if (!empty($term))
		{
			$this->db->group_start();
			$this->db->like('tbl_productions_orders.reference_no', $term);
			$this->db->group_end();
		}
		$this->db->limit($limit);
		$dtResult = $this->db->get()->result_array();
		
		$data['results'][] = ['text' => lang('Lệnh sản xuất'), 'children' => $dtResult];
		if (!empty($id)){
			$dtData = get_table_where('tbl_productions_orders',['id' => $id],'','row_array');
			$data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no']];
		}
		echo json_encode($data);
	}

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_plan_outsource');

        $this->db->select('tbl_suggest_plan_outsource.*');
        $this->db->from('tbl_suggest_plan_outsource');
        $this->db->where('tbl_suggest_plan_outsource.id',$id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_plan_outsource_item.*,
            tbl_result.name as name_result,
            tblsuppliers.company as company,
			(
				SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
				 FROM tblproduction_report
			 	WHERE tblproduction_report.object_id = tbl_suggest_plan_outsource_item.suggest_plan_outsource_id
			 	AND tblproduction_report.suggest_id_detail = tbl_suggest_plan_outsource_item.id
			 	AND tblproduction_report.object_type = "suggest_plan_outsource_detail"
			) as name_report
        ');
        $this->db->from('tbl_suggest_plan_outsource_item');
        $this->db->join('tbl_result','tbl_result.id = tbl_suggest_plan_outsource_item.result_id','left');
        $this->db->join('tblsuppliers','tblsuppliers.id = tbl_suggest_plan_outsource_item.supplier_id','inner');
        $this->db->where('tbl_suggest_plan_outsource_item.suggest_plan_outsource_id',$id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/suggest_plan_outsource/view',$data);
    }

    public function agree(){
        if (!$this->preApproveSuggestPlanOutsource) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_plan_outsource.*');
        $this->db->from('tbl_suggest_plan_outsource');
        $this->db->where('tbl_suggest_plan_outsource.id',$suggest_id);
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
            $up = $this->db->update('tbl_suggest_plan_outsource',$options);
            if ($up) {
                insertActivityLog([
                    'type_parent_obj' => 'suggest_plan_outsource',
                    'table_obj' => 'tbl_suggest_plan_outsource',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu kế hoạch gia công') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteSuggestPlanOutsource){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_plan_outsource.*');
        $this->db->from('tbl_suggest_plan_outsource');
        $this->db->where('tbl_suggest_plan_outsource.id',$id);
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
        $success = $this->db->delete('tbl_suggest_plan_outsource');
        if ($success){
            $this->db->where('tbl_suggest_plan_outsource_item.suggest_plan_outsource_id',$id);
            $this->db->delete('tbl_suggest_plan_outsource_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_plan_outsource',
                'table_obj' => 'tbl_suggest_plan_outsource',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu kế hoạch gia công') . ' [' . $dtData['reference_no'] . ']',
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

    public function searchPo($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_productions_orders.id as id, 
            CONCAT(tbl_productions_orders.reference_no) as text,
         
        ', false);
        $this->db->from('tbl_productions_orders');
        $this->db->where('EXISTS(
            SELECT 1
            FROM tbl_productions_plan_orders
            WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.object_type = "orders"
        )');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Lệnh sản xuất'), 'children' => $dtResult];
        if (!empty($id)){
            $dtData = get_table_where('tbl_productions_orders',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no']];
        }
        echo json_encode($data);
    }

    public function searchOrders($id = 0){
        $term = $this->input->get('term');
        $po_id = !empty($this->input->get('po_id')) ? $this->input->get('po_id') : 0;
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_orders.id as id, 
            CONCAT(tbl_orders.reference_no,"(",tbl_orders.customer_name,")") as text,
         
        ', false);
        $this->db->from('tbl_orders');
        $this->db->where('EXISTS(
            SELECT 1
            FROM tbl_productions_plan_orders
            WHERE tbl_productions_plan_orders.productions_plan_id = tbl_orders.id AND tbl_productions_plan_orders.object_type = "orders"
            AND tbl_productions_plan_orders.productions_order_id = '.$po_id.'
        )');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_orders.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Đơn hàng'), 'children' => $dtResult];
        if (!empty($id)){
            $dtData = get_table_where('tbl_orders',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no'].'('.$dtData['customer_name'].')'];
        }
        echo json_encode($data);
    }

    public function searchProductByProduction($po_id = ''){
        $term = $this->input->get('term');
//        $po_id = $this->input->get('po_id');
        $limit = get_option('select2_limit');
		
        $this->db->select('
            tbl_products.id as id,
            tbl_productions_orders_items.items_id as item_id, 
            SUM(tbl_productions_orders_items.quantity) as total_quantity_item,
            CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
            tbl_products.code as code_item,
            tbl_products.name as name_item,
            tbl_products.name_customer as name_customer,
            tbl_products.mode as mode,
            tbl_products.type_products as type_item,
            tblunits.unit as unit_name,
        ', false);
		
		$this->db->group_by('tbl_products.id, tbl_products.type_products');
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id','inner');
        $this->db->join('tbl_products','tbl_products.id = tbl_productions_orders_items.items_id','inner');
        $this->db->join('tblunits','tblunits.unitid = tbl_products.unit_id','inner');
		$this->db->where('tbl_productions_orders_details.productions_orders_id', $po_id);
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $term);
            $this->db->or_like('tbl_products.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Mặt Hàng'), 'children' => $dtResult];
        echo json_encode($data);
    }
	
    public function searchProductByOrders(){
        $term = $this->input->get('term');
        $order_id = $this->input->get('order_id');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_productions_orders_details.id as id,
            tbl_productions_orders_items.production_plan_item_id as order_item_id,
            tbl_productions_orders_items.items_id as item_id,
            tbl_productions_orders_items.quantity as total_quantity_item,
            CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
            tbl_products.code as code_item,
            tbl_products.name as name_item,
            tbl_products.name_customer as name_customer,
            tbl_products.mode as mode,
            tbl_products.type_products as type_item,
            tblunits.unit as unit_name,
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id','inner');
        $this->db->join('tbl_products','tbl_products.id = tbl_productions_orders_items.items_id','inner');
        $this->db->join('tblunits','tblunits.unitid = tbl_products.unit_id','inner');
        $this->db->where('tbl_productions_orders_details.object_id',$order_id);
        $this->db->where('tbl_productions_orders_details.object_type',"orders");
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $term);
            $this->db->or_like('tbl_products.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Đơn hàng'), 'children' => $dtResult];
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

            $this->db->select('
                tbl_suggest_plan_outsource.id as id,
                tbl_suggest_plan_outsource.reference_no as reference_no,
                tbl_suggest_plan_outsource.date as date,
                tbl_productions_orders.reference_no as reference_no_po,
                tblsuppliers_groups.name as name_supplier_group,
                tblsuppliers.company as name_supplier,
                tbl_suggest_plan_outsource_item.detail as detail,
                tbl_suggest_plan_outsource_item.quantity as quantity,
                tbl_suggest_plan_outsource_item.price as price,
                tbl_suggest_plan_outsource_item.amount as amount,
                tbl_suggest_plan_outsource_item.shipping_unit_outsource as shipping_unit_outsource,
                tbl_suggest_plan_outsource_item.transport_outsource as transport_outsource,
                tbl_suggest_plan_outsource_item.date_start_outsource as date_start_outsource,
                tbl_suggest_plan_outsource_item.date_end_outsource as date_end_outsource,
                tbl_result.name as name_result,
                tbl_suggest_plan_outsource_item.staff_id as staff_id,
                (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_suggest_plan_outsource.id AND tblproduction_report.object_type = "suggest_plan_outsource"
                ) as name_report,
                tbl_suggest_plan_outsource.staff_plan as staff_plan,
                tbl_suggest_plan_outsource_item.item_id as item_id,
                tbl_suggest_plan_outsource_item.type_item as type_item,
            ');
            $this->db->from('tbl_suggest_plan_outsource');
//            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_suggest_plan_outsource.po_id', 'left');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_suggest_plan_outsource.po_id', 'left');
            $this->db->join('tbl_suggest_plan_outsource_item','tbl_suggest_plan_outsource_item.suggest_plan_outsource_id = tbl_suggest_plan_outsource.id');
            $this->db->join('tblsuppliers','tblsuppliers.id = tbl_suggest_plan_outsource_item.supplier_id');
            $this->db->join('tblsuppliers_groups','tblsuppliers_groups.id = tblsuppliers.groups_in','left');
            $this->db->join('tbl_result','tbl_result.id = tbl_suggest_plan_outsource_item.result_id', 'left');

            if (!$this->preViewSuggestPlanOutsource) {
                $this->db->where('(tbl_suggest_plan_outsource.created_by = '.get_staff_user_id().' OR tbl_suggest_plan_outsource.staff_plan = '.get_staff_user_id().' )');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_plan_outsource.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_plan_outsource.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_plan_outsource.id desc');
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
                ('PHIẾU YÊU CẦU KẾ HOẠCH GIA CÔNG'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:W1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã Thành Phẩm');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Tên Thành Phẩm');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Quy Cách');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'ĐVT');
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Mã Lệnh Sản Xuất')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Nhóm Gia Công')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Đơn Vị Gia Công(NCC)')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Chi Tiết Gia Công')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Tổng SL');
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Đơn Giá')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Thành Tiền')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Đơn Vị Vận Chuyển Gia Công')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Phương Tiện Vận Chuyển Gia Công')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Ngày Đi Gia Công')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Ngày Về Gia Công')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Kết Quả')->getStyle("AA$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Báo Cáo Không Phù Hợp')->getStyle("AB$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Người Lập Kế Hoạch')->getStyle("AC$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'Nhân Viên Điều Độ Gia Công')->getStyle("AD$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:V$sttRow")->applyFromArray([
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
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $item_id = $value['item_id'];
                    $type_item = $value['type_item'];
                    $info = null;
                    $dtBand = null;
                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($item_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        $dtBand = get_table_where('tbl_brand',['id' => $info['brand_id']],'','row_array');
                    }
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($info['code']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($info['name']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($info['mode']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $unit['unit'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['reference_no_po'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['name_supplier_group'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['name_supplier'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['detail'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['quantity'])->getStyle("M$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity']));
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['price'])->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['amount'])->getStyle("O$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['amount']));
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['shipping_unit_outsource'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['transport_outsource'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", _dC($value['date_start_outsource']))->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", _dC($value['date_end_outsource']))->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", ($value['name_result']))->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", ($value['name_report']))->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", get_staff_full_name($value['staff_plan']))->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:V$rowBegin")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->getStyle("L$rowBegin:L$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("G$rowBegin:G$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_ke_hoach_gia_cong') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(25);
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
	
	function check_result()
	{
		$_data = $this->input->get();
		if (!empty($_data['id']) && !empty($_data['result'])) {
			$this->db->where('id', $_data['id']);
			$success = $this->db->update('tbl_suggest_plan_outsource_item' , ['result_id' => $_data['result']]);
			if(!empty($success)) {
				$data['alert_type'] = 'success';
				$data['message'] = 'Cập nhật thành công';
				echo json_encode($data);
				die;
			}
		}
		$data['alert_type'] = 'danger';
		$data['message'] = 'Cập nhật thất bại';
		echo json_encode($data);
		die;
	}
}