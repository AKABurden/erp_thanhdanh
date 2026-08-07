<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Request_overtime extends AdminController
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
			$this->preViewRequestOvertime = true;
			$this->preViewOwnRequestOvertime = true;
			$this->preAddRequestOvertime = true;
			$this->preEditRequestOvertime = true;
			$this->preApproveRequestOvertime = true;
			$this->preDeleteRequestOvertime = true;
		}
		
		public function index()
		{
			if (!$this->preViewRequestOvertime && !$this->preViewOwnRequestOvertime) {
				access_denied();
			}
			$data['title'] = _l('dt_request_overtime');
			$this->load->view('admin/request_overtime/index', $data);
		}
		
		public function getRequestOvertimes()
		{
			$end_date_search = $this->input->post('end_date_search');
			$start_date_search = $this->input->post('start_date_search');
			$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
			
			$RowsTypeObject = "
				(
					SELECT
						GROUP_CONCAT(IF(tbl_request_overtime_object.type_object = 'orders', tbl_orders.reference_no, tbl_productions_orders.reference_no) SEPARATOR '<br>') as list_reference_no,
						tbl_request_overtime_object.id_request_overtime,
						tbl_request_overtime_object.type_object
					FROM tbl_request_overtime_object
					LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_request_overtime_object.po_id AND tbl_request_overtime_object.type_object = 'productions_orders'
					LEFT JOIN tbl_orders ON tbl_orders.id = tbl_request_overtime_object.po_id AND tbl_request_overtime_object.type_object = 'orders'
					GROUP BY tbl_request_overtime_object.type_object, tbl_request_overtime_object.id_request_overtime
				) tbllist_items
			";
			$aColumns = [
				'tbl_request_overtime.id as id',
				'tbl_request_overtime.reference_no as reference_no',
				'tbl_request_overtime.date as date',
				'tbllist_items.list_reference_no as list_reference_no',
				'tbl_request_overtime.staff_plan as staff_plan',
				'(
					SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
					 FROM tblproduction_report
					 WHERE tblproduction_report.object_id = tbl_request_overtime.id AND tblproduction_report.object_type = "request_overtime"
					) as name_report',
				'tbl_request_overtime.created_by as created_by',
			];
			$sIndexColumn = 'id';
			$sTable = 'tbl_request_overtime';
			$where = [
			];
			$filter = [];
			$join = [
				'LEFT JOIN ' . $RowsTypeObject . ' ON tbllist_items.id_request_overtime = tbl_request_overtime.id'
//				'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_request_overtime.po_id',
			];
			if (!$this->preViewRequestOvertime) {
				array_push($where, 'AND (tbl_request_overtime.created_by = ' . get_staff_user_id() . ' OR tbl_request_overtime.staff_plan = ' . get_staff_user_id() . ' )');
			}
			if (!empty($start_date_search)) {
				$start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
				array_push($where, "AND tbl_request_overtime.date >= '" . $start_date_search . "'");
			}
			if (!empty($end_date_search)) {
				$end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
				array_push($where, "AND tbl_request_overtime.date <= '" . $end_date_search . "'");
			}
			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
			], '', []);
			$output = $result['output'];
			$rResult = $result['rResult'];
			foreach ($rResult as $key => $aRow) {
				$row = array();
				$row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
				$row[] = '<div class="text-left" style="min-width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_overtime/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
				$row[] = '<div class="text-left" style="min-width: 110px">' . _dt($aRow['date']) . '</div>';
				$row[] = '<div class="text-center" style="min-width: 110px">' . ($aRow['list_reference_no']) . '</div>';
				$row[] = '<div class="text-left" style="min-width: 110px">' . get_staff_full_name($aRow['staff_plan']) . '</div>';
				$arrReport = $aRow['name_report'];
				$htmlReport = '';
				if (!empty($arrReport)) {
					$arrReport = explode('||', $arrReport);
					if (!empty($arrReport)) {
						foreach ($arrReport as $kk => $vv) {
							$vv = explode('__', $vv);
							$htmlReport .= '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $vv[1])) . '">' . $vv[0] . '</a>';
						}
					}
				}
				$row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=request_overtime') . '" class="btn btn-info">Báo cáo không phù hợp</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>';
				$row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['created_by']) . '</div>';
				$view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_overtime/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';
				$edit = $this->preEditRequestOvertime ? '<a href="' . base_url('admin/request_overtime/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';
				$delete = $this->preDeleteRequestOvertime ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_overtime/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
				$row[] = '<div style="min-width: 120px">' . $actions . '</div>';
				$output['aaData'][] = $row;
			}
			echo json_encode($output);
		}
		
		public function detail($id = 0)
		{
			$data = [];
			$this->db->select('tbl_request_overtime.*');
			$this->db->from('tbl_request_overtime');
			$this->db->where('tbl_request_overtime.id', $id);
			$dtData = $this->db->get()->row_array();
			if ($this->input->post()) {
				if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
					$this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_request_overtime.reference_no]');
				}
				$this->form_validation->set_rules('date', lang("date"), 'required');
				$this->form_validation->set_rules('po_id', lang("Lệnh sản xuất"), 'required');
				$this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
				$this->form_validation->set_rules('staff_plan', lang("Người lập kế hoạch"), 'required');
				if (empty($id)) {
					if ($this->form_validation->run() == true) {
						$reference_no = getReference('request_overtime');
						$date = to_sql_date($this->input->post('date'), true);
						$po_id = $this->input->post('po_id');
						$type_object = $this->input->post('type_object');
						$branch_id = $this->input->post('branch_id');
						$staff_plan = !empty($this->input->post('staff_plan')) ? $this->input->post('staff_plan') : 0;
						$note = ($this->input->post('note'));
						$counter = $this->input->post('counter');
						$items = [];
						if (!empty($counter)) {
							foreach ($counter as $key => $value) {
								$order_id = $this->input->post('order_id')[$value];
								$pod_id = $this->input->post('pod_id')[$value];
								$order_item_id = $this->input->post('order_item_id')[$value];
								$this->db->select('tbl_productions_orders_items.items_id as items_id,tbl_productions_orders_items.type_items');
								$this->db->from('tbl_productions_orders_details');
								$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
								$this->db->where('tbl_productions_orders_details.id', $pod_id);
								$dtPod = $this->db->get()->row_array();
								if (empty($dtPod)) {
									continue;
								}
								$item_id = $dtPod['items_id'];
								$type_item = $dtPod['type_items'];
								$salary = number_unformat($this->input->post('salary')[$value]);
								$quantity_overtime = number_unformat($this->input->post('quantity_overtime')[$value]);
								$quantity = number_unformat($this->input->post('quantity')[$value]);
								$standard = ($this->input->post('standard')[$value]);
								$quota_productivity = ($this->input->post('quota_productivity')[$value]);
								$coefficient = number_unformat($this->input->post('coefficient')[$value]);
								$category_overtime = ($this->input->post('category_overtime')[$value]);
								$staff_id = ($this->input->post('staff_id')[$value]);
								$date_overtime = !empty($this->input->post('date_overtime')[$value]) ? to_sql_date($this->input->post('date_overtime')[$value]) : null;
								$hour_start = !empty($this->input->post('hour_start')[$value]) ? $this->input->post('hour_start')[$value] : null;
								$hour_end = !empty($this->input->post('hour_end')[$value]) ? $this->input->post('hour_end')[$value] : 0;
								$result_id = ($this->input->post('result_id')[$value]);
								$time_overtime = 0;
								$items[] = [
									'order_id' => $order_id,
									'order_item_id' => $order_item_id,
									'pod_id' => $pod_id,
									'item_id' => $item_id,
									'type_item' => $type_item,
									'salary' => $salary,
									'quantity_overtime' => $quantity_overtime,
									'quantity' => $quantity,
									'standard' => $standard,
									'quota_productivity' => $quota_productivity,
									'coefficient' => $coefficient,
									'time_overtime' => $time_overtime,
									'category_overtime' => $category_overtime,
									'staff_id' => $staff_id,
									'date_overtime' => $date_overtime,
									'hour_start' => $hour_start,
									'hour_end' => $hour_end,
									'result_id' => $result_id,
								];
							}
						}
						if (empty($items)) {
							$data['result'] = 0;
							$data['message'] = lang('tnh_no_items');
							echo json_encode($data);
							die;
						}
						$fields = [
							'reference_no' => $reference_no,
							'date' => $date,
							'po_id' => 0,
							'type_object' => $type_object,
							'staff_plan' => $staff_plan,
							'note' => $note,
							'created_by' => get_staff_user_id(),
							'date_created' => date('Y-m-d H:i:s'),
							'branch_id' => $branch_id,
						];
						$this->db->insert('tbl_request_overtime', $fields);
						$id = $this->db->insert_id();
						if ($id) {
							$po_id = explode(',', $po_id);
							$ArrayPoObject = [];
							foreach($po_id as $key => $value) {
								$ArrayPoObject[] = [
									'id_request_overtime' => $id,
									'po_id' => $value,
									'type_object' => $type_object,
								];
							}
							if(!empty($ArrayPoObject)) {
								$this->db->insert_batch('tbl_request_overtime_object', $ArrayPoObject);
							}
							
							if (getReference('request_overtime') == $reference_no) {
								updateReference('request_overtime');
							}
							foreach ($items as $key => $value) {
								$value['suggest_request_id'] = $id;
								$this->db->insert('tbl_request_overtime_item', $value);
							}
							insertActivityLog([
								'type_parent_obj' => 'request_overtime',
								'table_obj' => 'tbl_request_overtime',
								'id_obj' => $id,
								'name_obj' => $reference_no,
								'content' => lang('Thêm mới yêu cầu tăng ca') . ' [' . $reference_no . ']',
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
					echo json_encode($data);
					die();
				} else {
					if ($this->form_validation->run() == true) {
						$date = to_sql_date($this->input->post('date'), true);
						$po_id = $this->input->post('po_id');
						$type_object = $this->input->post('type_object');
						$branch_id = $this->input->post('branch_id');
						$staff_plan = !empty($this->input->post('staff_plan')) ? $this->input->post('staff_plan') : 0;
						$note = ($this->input->post('note'));
						$counter = $this->input->post('counter');
						$items = [];
						if (!empty($counter)) {
							foreach ($counter as $key => $value) {
								$order_id = $this->input->post('order_id')[$value];
								$pod_id = $this->input->post('pod_id')[$value];
								$order_item_id = $this->input->post('order_item_id')[$value];
								$this->db->select('tbl_productions_orders_items.items_id as items_id,tbl_productions_orders_items.type_items');
								$this->db->from('tbl_productions_orders_details');
								$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
								$this->db->where('tbl_productions_orders_details.id', $pod_id);
								$dtPod = $this->db->get()->row_array();
								if (empty($dtPod)) {
									continue;
								}
								$item_id = $dtPod['items_id'];
								$type_item = $dtPod['type_items'];
								$salary = number_unformat($this->input->post('salary')[$value]);
								$quantity_overtime = number_unformat($this->input->post('quantity_overtime')[$value]);
								$quantity = number_unformat($this->input->post('quantity')[$value]);
								$standard = ($this->input->post('standard')[$value]);
								$quota_productivity = ($this->input->post('quota_productivity')[$value]);
								$coefficient = number_unformat($this->input->post('coefficient')[$value]);
								$category_overtime = ($this->input->post('category_overtime')[$value]);
								$staff_id = ($this->input->post('staff_id')[$value]);
								$date_overtime = !empty($this->input->post('date_overtime')[$value]) ? to_sql_date($this->input->post('date_overtime')[$value]) : null;
								$hour_start = !empty($this->input->post('hour_start')[$value]) ? $this->input->post('hour_start')[$value] : null;
								$hour_end = !empty($this->input->post('hour_end')[$value]) ? $this->input->post('hour_end')[$value] : 0;
								$result_id = ($this->input->post('result_id')[$value]);
								$time_overtime = countHourCheckOutNew(countHourCheckOut($hour_start, $hour_end));
								$request_overtime_item_id = !empty($this->input->post('request_overtime_item_id')[$value]) ? $this->input->post('request_overtime_item_id')[$value] : 0;
								$items[] = [
									'id' => $request_overtime_item_id,
									'order_id' => $order_id,
									'order_item_id' => $order_item_id,
									'pod_id' => $pod_id,
									'item_id' => $item_id,
									'type_item' => $type_item,
									'salary' => $salary,
									'quantity_overtime' => $quantity_overtime,
									'quantity' => $quantity,
									'standard' => $standard,
									'quota_productivity' => $quota_productivity,
									'coefficient' => $coefficient,
									'time_overtime' => $time_overtime,
									'category_overtime' => $category_overtime,
									'staff_id' => $staff_id,
									'date_overtime' => $date_overtime,
									'hour_start' => $hour_start,
									'hour_end' => $hour_end,
									'result_id' => $result_id,
								];
							}
						}
						if (empty($items)) {
							$data['result'] = 0;
							$data['message'] = lang('tnh_no_items');
							echo json_encode($data);
							die;
						}
						$fields = [
							'date' => $date,
							'po_id' => 0,
							'type_object' => $type_object,
							'staff_plan' => $staff_plan,
							'note' => $note,
							'branch_id' => $branch_id,
							'updated_by' => get_staff_user_id(),
							'date_updated' => date('Y-m-d H:i:s'),
						];
						$this->db->where('id', $id);
						$success = $this->db->update('tbl_request_overtime', $fields);
						if ($success) {
							$this->db->where('id_request_overtime', $id);
							$this->db->delete('tbl_request_overtime_object');
							
							$po_id = explode(',', $po_id);
							$ArrayPoObject = [];
							foreach($po_id as $key => $value) {
								$ArrayPoObject[] = [
									'id_request_overtime' => $id,
									'po_id' => $value,
									'type_object' => $type_object,
								];
							}
							if(!empty($ArrayPoObject)) {
								$this->db->insert_batch('tbl_request_overtime_object', $ArrayPoObject);
							}
							
							
							$this->db->where('suggest_request_id', $id);
							$this->db->delete('tbl_request_overtime_item');
							foreach ($items as $key => $value) {
								$value['suggest_request_id'] = $id;
								$this->db->insert('tbl_request_overtime_item', $value);
							}
							insertActivityLog([
								'type_parent_obj' => 'request_overtime',
								'table_obj' => 'tbl_request_overtime',
								'id_obj' => $id,
								'name_obj' => $dtData['reference_no'],
								'content' => lang('Sửa phiếu yêu cầu tăng ca') . ' [' . $dtData['reference_no'] . ']',
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
					echo json_encode($data);
					die();
				}
			}
			else {
				if (empty($id)) {
					if (!$this->preAddRequestOvertime) {
						accessDenied(true);
					}
					$data['title'] = lang('dt_add_request_overtime');
					$data['breadcrumb'] = [array('link' => base_url('admin/request_overtime'), 'page' => lang('dt_request_overtime')), array('link' => '#', 'page' => lang('dt_add_request_overtime'))];
				} else {
					if (!$this->preEditRequestOvertime) {
						accessDenied(true);
					}
					$this->db->where('suggest_request_id', $id);
					$this->db->where('status', 1);
					$suggestPlanOvertime = $this->db->get('tbl_request_overtime_item')->row_array();
					if (!empty($suggestPlanOvertime)) {
						set_alert('danger', 'Có chi tiết phiếu yêu cầu tăng ca đã được duyệt không thể sửa !');
						redirect($_SERVER["HTTP_REFERER"]);
					}
					$this->db->select('tbl_request_overtime_item.*');
					$this->db->from('tbl_request_overtime_item');
					$this->db->where('tbl_request_overtime_item.suggest_request_id', $id);
					$dtItems = $this->db->get()->result_array();
					if(!empty($dtItems)) {
						foreach($dtItems as $key => $value) {
							if($dtData['type_object'] == 'orders') {
								$this->db->where('id', $value['order_id']);
								$dt_orders = $this->db->get('tbl_orders')->row();
								$dtItems[$key]['code_object_type'] = $dt_orders->reference_no;
							}
							else {
								$this->db->where('id', $value['order_id']);
								$dt_productions_orders = $this->db->get('tbl_productions_orders')->row();
								$dtItems[$key]['code_object_type'] = $dt_productions_orders->reference_no;
							}
						}
					}
					
					
					$data['dtData'] = $dtData;
					$data['dtData']['po_id'] = $this->db->select('GROUP_CONCAT(po_id) as list_po_id')->get_where('tbl_request_overtime_object', ['id_request_overtime' => $id])->row('list_po_id');
					$data['dtItems'] = $dtItems;
					$data['title'] = lang('dt_edit_request_overtime');
					$data['breadcrumb'] = [array('link' => base_url('admin/request_overtime'), 'page' => lang('dt_request_overtime')), array('link' => '#', 'page' => lang('dt_edit_request_overtime'))];
				}
			}
			$data['employees'] = $this->manufactures_model->getAllStaff();
			$data['id'] = $id;
			$data['reference_no'] = getReference('request_overtime');
			$data['dtResult'] = get_table_where('tbl_result');
			$data['dtCategoryStage'] = get_table_where('tbl_category_stages');
			$data['dtStaff'] = get_table_where('tblstaff', ['active' => 1]);
			$this->load->view('admin/request_overtime/detail', $data);
		}
		
		public function view($id)
		{
			$data = [];
			$data['title'] = lang('dt_view_request_overtime');
			
			
			$RowsTypeObject = "
				(
					SELECT
						GROUP_CONCAT(IF(tbl_request_overtime_object.type_object = 'orders', tbl_orders.reference_no, tbl_productions_orders.reference_no) SEPARATOR '<br>') as list_reference_no,
						tbl_request_overtime_object.id_request_overtime,
						tbl_request_overtime_object.type_object
					FROM tbl_request_overtime_object
					LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_request_overtime_object.po_id AND tbl_request_overtime_object.type_object = 'productions_orders'
					LEFT JOIN tbl_orders ON tbl_orders.id = tbl_request_overtime_object.po_id AND tbl_request_overtime_object.type_object = 'orders'
					GROUP BY tbl_request_overtime_object.type_object, tbl_request_overtime_object.id_request_overtime
				) tbllist_items
			";
			
			
			$this->db->select('tbl_request_overtime.*');
			$this->db->select('tbllist_items.list_reference_no as list_reference_no');
			$this->db->from('tbl_request_overtime');
			$this->db->where('tbl_request_overtime.id', $id);
			$this->db->join( $RowsTypeObject, 'tbllist_items.id_request_overtime = tbl_request_overtime.id', 'left');
			$dtData = $this->db->get()->row_array();
			
			
			$this->db->select('tbl_request_overtime_item.*,
				tbl_result.name as name_result,
			');
			$this->db->from('tbl_request_overtime_item');
			$this->db->join('tbl_result', 'tbl_result.id = tbl_request_overtime_item.result_id', 'left');
			$this->db->where('tbl_request_overtime_item.suggest_request_id', $id);
			$dtDataItems = $this->db->get()->result_array();
			
			if(!empty($dtDataItems)) {
				foreach($dtDataItems as $key => $value) {
					if($dtData['type_object'] == 'orders') {
						$this->db->where('id', $value['order_id']);
						$dt_orders = $this->db->get('tbl_orders')->row();
						$dtDataItems[$key]['code_object_type'] = $dt_orders->reference_no;
					}
					else {
						$this->db->where('id', $value['order_id']);
						$dt_productions_orders = $this->db->get('tbl_productions_orders')->row();
						$dtDataItems[$key]['code_object_type'] = $dt_productions_orders->reference_no;
					}
				}
			}
			
			
			$data['dtData'] = $dtData;
			$data['dtDataItems'] = $dtDataItems;
			$this->load->view('admin/request_overtime/view', $data);
		}
		
		public function agree()
		{
			if (!$this->preApproveRequestOvertime) {
				$data['result'] = 0;
				$data['message'] = lang('access_denied');
				echo json_encode($data);
				die();
			}
			$data = [];
			$suggest_id = $this->input->post('suggest_id');
			$status = $this->input->post('status');
			$this->db->select('tbl_request_overtime_item.*');
			$this->db->from('tbl_request_overtime_item');
			$this->db->where('tbl_request_overtime_item.id', $suggest_id);
			$dtData = $this->db->get()->row_array();
			if (empty($dtData)) {
				$data['result'] = 0;
				$data['message'] = lang('not_data_exists');
			} else {
				if (($dtData['status'] == $status)) {
					$data['result'] = 0;
					$data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
					echo responseData($data);
					return;
				}
				$date_status = date('Y-m-d H:i:s');
				$staff_status = get_staff_user_id();
				$options = [
					'status' => $status,
					'date_status' => $date_status,
					'staff_status' => $staff_status,
				];
				$this->db->where('id', $suggest_id);
				$up = $this->db->update('tbl_request_overtime_item', $options);
				if ($up) {
					$get_code = get_table_where('tbl_request_overtime', array('id' => $dtData['suggest_request_id']), '', 'row_array');
					insertActivityLog([
						'type_parent_obj' => 'request_overtime_item',
						'table_obj' => 'tbl_request_overtime_item',
						'id_obj' => $suggest_id,
						'name_obj' => $get_code['reference_no'],
						'content' => lang('Duyệt phiếu yêu cầu tăng ca') . ' [' . $get_code['reference_no'] . ']',
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
		
		public function delete($id)
		{
			if (!$this->preDeleteRequestOvertime) {
				$data['result'] = 0;
				$data['message'] = lang('access_denied');
				echo json_encode($data);
				die();
			}
			$data = [];
			$this->db->select('tbl_request_overtime.*');
			$this->db->from('tbl_request_overtime');
			$this->db->where('tbl_request_overtime.id', $id);
			$dtData = $this->db->get()->row_array();
			if (empty($dtData)) {
				$data['result'] = 0;
				$data['message'] = lang('not_data_exists');
				echo json_encode($data);
				die();
			}
			$this->db->where('suggest_request_id', $id);
			$this->db->where('status', 1);
			$suggestPlanOvertime = $this->db->get('tbl_request_overtime_item')->row_array();
			if (!empty($suggestPlanOvertime)) {
				set_alert('danger', 'Có chi tiết phiếu yêu cầu tăng ca đã được duyệt không thể xóa !');
				redirect(admin_url('request_overtime'));
			}
			$this->db->where('id', $id);
			$success = $this->db->delete('tbl_request_overtime');
			if ($success) {
				$this->db->where('tbl_request_overtime_item.suggest_request_id', $id);
				$this->db->delete('tbl_request_overtime_item');
				$this->db->where('tbl_moderation_overtime.suggest_overtime_id', $id);
				$this->db->delete('tbl_moderation_overtime');
				
				
				$this->db->where('id_request_overtime', $id);
				$this->db->delete('tbl_request_overtime_object');
				insertActivityLog([
					'type_parent_obj' => 'request_overtime',
					'table_obj' => 'tbl_request_overtime',
					'id_obj' => $id,
					'name_obj' => $dtData['reference_no'],
					'content' => lang('Xóa phiếu yêu cầu tăng ca') . ' [' . $dtData['reference_no'] . ']',
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
		
		public function searchPo($id = 0)
		{
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
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('tbl_productions_orders.reference_no', $term);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$dtResult = $this->db->get()->result_array();
			$data['results'][] = ['text' => lang('Lệnh sản xuất'), 'children' => $dtResult];
			if (!empty($id)) {
				$dtData = get_table_where('tbl_productions_orders', ['id' => $id], '', 'row_array');
				$data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no']];
			}
			echo json_encode($data);
		}
		
		public function searchOrders($id = 0)
		{
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
            AND tbl_productions_plan_orders.productions_order_id = ' . $po_id . '
        )');
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('tbl_orders.reference_no', $term);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$dtResult = $this->db->get()->result_array();
			$data['results'][] = ['text' => lang('Đơn hàng'), 'children' => $dtResult];
			if (!empty($id)) {
				$dtData = get_table_where('tbl_orders', ['id' => $id], '', 'row_array');
				$data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no'] . '(' . $dtData['customer_name'] . ')'];
			}
			echo json_encode($data);
		}
		
		public function searchProductByOrders()
		{
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
            tblunits.unit as unit_name,
        ', false);
			$this->db->from('tbl_productions_orders_details');
			$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
			$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'inner');
			$this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
			$this->db->where('tbl_productions_orders_details.object_id', $order_id);
			$this->db->where('tbl_productions_orders_details.object_type', "orders");
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('tbl_products.code', $term);
				$this->db->or_like('tbl_products.name', $term);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$dtResult = $this->db->get()->result_array();
			$data['results'][] = ['text' => lang('Mặt hàng'), 'children' => $dtResult];
			echo json_encode($data);
		}
		
		public function exportExcel()
		{
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
					tbl_request_overtime.id as id,
					tbl_request_overtime.reference_no as reference_no,
					tbl_request_overtime.date as date,
					tbl_request_overtime_item.quantity as quantity,
					tbl_request_overtime_item.category_overtime as category_overtime,
					tbl_request_overtime_item.quota_productivity as quota_productivity,
					tbl_request_overtime_item.quantity_overtime as quantity_overtime,
					tbl_request_overtime_item.date_overtime as date_overtime,
					tbl_request_overtime_item.hour_start as hour_start,
					tbl_request_overtime_item.hour_end as hour_end,
					tbl_result.name as name_result,
					tbl_request_overtime_item.coefficient as coefficient,
					(SELECT GROUP_CONCAT(tblproduction_report.name_report)
					 FROM tblproduction_report
					 WHERE tblproduction_report.object_id = tbl_request_overtime.id AND tblproduction_report.object_type = "request_overtime"
					) as name_report,
					tbl_request_overtime_item.salary as salary,
					tbl_request_overtime.staff_plan as staff_plan,
					tbl_request_overtime_item.staff_id as staff_id,
					tbl_request_overtime_item.standard as standard,
					tbl_request_overtime_item.status as status,
					tbl_request_overtime_item.item_id as item_id,
					tbl_request_overtime_item.type_item as type_item,
					tbl_request_overtime_item.order_id as order_id,
					tbl_request_overtime.barcode as barcode,
					tbl_request_overtime.type_object as type_object,
				');
//				$this->db->select('tbllist_items.list_reference_no as list_reference_no');
				$this->db->from('tbl_request_overtime');
//				$this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_request_overtime.po_id', 'inner');
				$this->db->join('tbl_request_overtime_item', 'tbl_request_overtime_item.suggest_request_id = tbl_request_overtime.id');
				$this->db->join('tbl_result', 'tbl_result.id = tbl_request_overtime_item.result_id');
				if (!$this->preViewRequestOvertime) {
					$this->db->where('(tbl_request_overtime.created_by = ' . get_staff_user_id() . ' OR tbl_request_overtime.staff_plan = ' . get_staff_user_id() . ' )');
				}
				if (!empty($start_date_search)) {
					$start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
					$this->db->where("tbl_request_overtime.date >= '" . $start_date_search . "'");
				}
				if (!empty($end_date_search)) {
					$end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
					$this->db->where("tbl_request_overtime.date <= '" . $end_date_search . "'");
				}
				$this->db->order_by('tbl_request_overtime.id desc');
				$dtData = $this->db->get()->result_array();
				
				if(!empty($dtData)) {
					foreach($dtData as $key => $value) {
						if($value['type_object'] == 'orders') {
							$this->db->where('id', $value['order_id']);
							$dt_orders = $this->db->get('tbl_orders')->row();
							$dtData[$key]['list_reference_no'] = $dt_orders->reference_no;
						}
						else {
							$this->db->where('id', $value['order_id']);
							$dt_productions_orders = $this->db->get('tbl_productions_orders')->row();
							$dtData[$key]['list_reference_no'] = $dt_productions_orders->reference_no;
						}
					}
				}
				
				
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
						'name' => 'Times New Roman'
					),
				]);
				$objPHPExcel->getActiveSheet()->setCellValue('A1',
					('PHIẾU YÊU CẦU TĂNG CA'))->getStyle("A1")->applyFromArray([
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
				$objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Phiếu');
				$objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập');
				$objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Mã Lệnh Sản Xuất/Đơn Hàng')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Mã SP');
				$objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Tên SP');
				$objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Nhóm SP');
				$objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Tổng SL');
				$objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Nhóm Tăng Ca')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Định Mức Năng Suất')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Số Lượng Người Tăng Ca')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Kết Quả')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Hệ Số Lương Ca')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Phiếu Báo Cáo Không Phù Hợp')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Tổng Số Lương Tăng Ca')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'Người Lập Kế Hoạch')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('Q' . $sttRow . '', 'Nhân Viên Điều Độ Tăng Ca')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('R' . $sttRow . '', 'Hành Chính Nhân Sự Duyệt')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('S' . $sttRow . '', 'Tiêu Chuẩn/ Quy Định')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('T' . $sttRow . '', 'QR')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle("A$sttRow:T$sttRow")->applyFromArray([
					'font' => array(
						'size' => 12,
						'name' => 'Times New Roman'
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
				$this->load->library('ciqrcode');
				$rowBegin = $sttRow;
				if (!empty($dtData)) {
					foreach ($dtData as $key => $value) {
						$item_id = $value['item_id'];
						$type_item = $value['type_item'];
						$info = null;
						$dtCategory = null;
						if ($type_item == "products") {
							$info = $this->products_model->rowProduct($item_id);
							$dtCategory = get_table_where('tbl_category_products', ['id' => $info['category_id']], '', 'row_array');
						}
						$rowBegin++;
						$objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
						$objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
						$objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
						$objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['list_reference_no']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($info['code']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($info['name']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", ($dtCategory['name']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['quantity'])->getStyle("H$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity']));
						$objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['category_overtime'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['quota_productivity'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['quantity_overtime'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", ($value['name_result']))->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", ($value['coefficient']))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['name_report'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['salary'])->getStyle("O$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['salary']));
						$objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", get_staff_full_name($value['staff_plan']))->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
						$htmlStatus = '';
						if ($value['status'] == 0) {
							$htmlStatus = 'Chưa duyệt';
						} else {
							$htmlStatus = 'Đã duyệt';
						}
						$objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $htmlStatus)->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $value['standard'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
						if (!empty($value['barcode'])) {
							$code = $value['barcode'];
						} else {
							$code = 'request_overtime||' . $value['id'];
							$this->db->where('id', $value['id']);
							$this->db->update('tbl_request_overtime', ['barcode' => $code]);
						}
						$qr = vn_to_str(str_replace('||', '__', $code));
						$folder = FCPATH . 'uploads/request_overtime/';
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
						$params['savename'] = $folder . 'qrcode/' . $qr . '.png';
						$this->ciqrcode->generate($params);
						$img = ($folder . 'qrcode/' . $qr . '.png');
						if (!empty($img)) {
							$objDrawing1 = new PHPExcel_Worksheet_Drawing();
							$objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
							$objDrawing1->setPath($img);
							$objDrawing1->setWidth(80);
							$objDrawing1->setHeight(53);
							$objDrawing1->setOffsetX(3);
							$objDrawing1->setOffsetY(2);
							$objDrawing1->setCoordinates('T' . ($rowBegin));
						}
						$objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
						$objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", '')->getStyle("T$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:T$rowBegin")->applyFromArray([
							'borders' => array(
								'allborders' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						]);
						$objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							),
						]);
						$objPHPExcel->getActiveSheet()->getStyle("H$rowBegin:H$rowBegin")->applyFromArray([
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							),
						]);
						$objPHPExcel->getActiveSheet()->getStyle("J$rowBegin:K$rowBegin")->applyFromArray([
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							),
						]);
						$objPHPExcel->getActiveSheet()->getStyle("M$rowBegin:N$rowBegin")->applyFromArray([
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							),
						]);
					}
				}
				$filename = lang('phieu_yeu_cau_tang_ca') . '.xls';
				$objPHPExcel->getActiveSheet()->freezePane('A1');
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
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
		
		public function search_poid_orders($id = 0) {
			$term = $this->input->get('term');
			$type_object = $this->input->get('type_object');
			$limit = get_option('select2_limit');
			if ($type_object == 'orders') {
				$this->db->select(['tbl_orders.id as id', 'CONCAT(tbl_orders.reference_no) as text'], false);
				$this->db->from('tbl_orders');
//				$this->db->where('EXISTS(
//					SELECT 1
//					FROM tbl_productions_plan_orders
//					WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.object_type = "orders"
//				)', false, false);
				if (!empty($term)) {
					$this->db->group_start();
					$this->db->like('tbl_orders.reference_no', $term);
					$this->db->group_end();
				}
				$this->db->limit($limit);
				$dtResult = $this->db->get()->result_array();
				$data['results'][] = ['text' => lang('Đơn Hàng Bán'), 'children' => $dtResult];
				if (!empty($id)) {
					$id = explode('_', $id);
					$this->db->where_in('id', $id);
					$dtData = $this->db->get('tbl_orders')->result_array();
					$data['row'] = [];
					foreach ($dtData as $key => $value) {
						$data['row'][] = ['id' => $value['id'], 'text' => $value['reference_no']];
					}
				}
			} else {
				$this->db->select(['tbl_productions_orders.id as id', 'CONCAT(tbl_productions_orders.reference_no) as text'], false);
				$this->db->from('tbl_productions_orders');
				$this->db->where('EXISTS(
					SELECT 1
					FROM tbl_productions_plan_orders
					WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.object_type = "orders"
				)', false, false);
				if (!empty($term)) {
					$this->db->group_start();
					$this->db->like('tbl_productions_orders.reference_no', $term);
					$this->db->group_end();
				}
				$this->db->limit($limit);
				$dtResult = $this->db->get()->result_array();
				$data['results'][] = ['text' => lang('Lệnh sản xuất'), 'children' => $dtResult];
				if (!empty($id)) {
					$id = explode('_', $id);
					$this->db->where_in('id', $id);
					$dtData = $this->db->get('tbl_productions_orders')->result_array();
					$data['row'] = [];
					foreach ($dtData as $key => $value) {
						$data['row'][] = ['id' => $value['id'], 'text' => $value['reference_no']];
					}
				}
			}
			echo json_encode($data);die();
		}
		
		public function searchProductByOrdersPOID()
		{
			$term = $this->input->get('term');
			$type_object = $this->input->get('type_object');
			$po_id = $this->input->get('po_id');
			$limit = get_option('select2_limit');
			if ($type_object == 'productions_orders') {
				$this->db->select(['tbl_productions_orders_details.id as id',
					'tbl_productions_orders_details.id as order_item_id',
					'tbl_productions_orders_items.items_id as item_id',
					'tbl_productions_orders_items.quantity as total_quantity_item',
					'CONCAT(tbl_products.name,"(",tbl_products.code,")") as text',
					'tbl_products.code as code_item',
					'tbl_products.name as name_item',
					'tbl_products.name_customer as name_customer',
					'tbl_products.mode as mode',
					'tblunits.unit as unit_name',
					'tbl_productions_orders.reference_no as reference_no_order',
					'tbl_productions_orders.id as order_id'
				], false);
				$this->db->from('tbl_productions_orders_details');
				$this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id', 'inner');
				$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
				$this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
				$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'inner');
				$this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
				$this->db->where('tbl_productions_orders_details.productions_orders_id IN (' . $po_id . ')');
				if (!empty($term)) {
					$this->db->group_start();
					$this->db->like('tbl_products.code', $term);
					$this->db->or_like('tbl_products.name', $term);
					$this->db->or_like('tbl_productions_orders.reference_no', $term);
					$this->db->group_end();
				}
				$this->db->limit($limit);
				$results = $this->db->get()->result_array();
			} else {
				$this->db->select('
                tbl_order_items.id as id,
                tbl_order_items.id as order_item_id,
                tbl_order_items.item_id as item_id,
                tbl_order_items.total_quantity_item as total_quantity_item,
                CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
                tbl_products.code as code_item,
                tbl_products.name as name_item,
                tbl_products.name_customer as name_customer,
                tbl_products.mode as mode,
                tblunits.unit as unit_name,
                tbl_orders.reference_no as reference_no_order,
                tbl_orders.id as order_id,
            ', false);
				$this->db->from('tbl_orders');
				$this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
				$this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id', 'inner');
				$this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
				$this->db->where('tbl_orders.id IN (' . $po_id . ')');
				if (!empty($term)) {
					$this->db->group_start();
					$this->db->like('tbl_products.code', $term);
					$this->db->or_like('tbl_products.name', $term);
					$this->db->or_like('tbl_orders.reference_no', $term);
					$this->db->group_end();
				}
				$this->db->limit($limit);
				$results = $this->db->get()->result_array();
			}
			$resultsNew = [];
			$data = [];
			if (!empty($results)) {
				foreach ($results as $key => $value) {
					if (!empty($resultsNew[$value['reference_no_order']])) {
						$resultsNew[$value['reference_no_order']]['items'][] = $value;
					} else {
						$resultsNew[$value['reference_no_order']]['items'][] = $value;
					}
				}
			}
			foreach ($resultsNew as $key => $value) {
				$data['results'][] =
					[
						'text' => $key,
						'children' => $value['items']
					];
			}
			echo json_encode($data);
		}
	}