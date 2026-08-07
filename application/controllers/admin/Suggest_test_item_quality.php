<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_test_item_quality extends AdminController
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

        $this->preView = has_permission('suggest_test_item_quality', '', 'view');
        $this->preViewOwn = has_permission('suggest_test_item_quality', '', 'view_own');
        $this->preAdd = has_permission('suggest_test_item_quality', '', 'create');;
        $this->preEdit = has_permission('suggest_test_item_quality', '', 'edit');;
        $this->preApprove = true;
        $this->preDelete = has_permission('suggest_test_item_quality', '', 'delete');
		
		$this->type_object_check = [
			'products' => [
				'tableType' => 'tbl_products',
				'tableDataObject' => 'tblclients',
				'IDDataObject' => 'userid',
				'tableDataOrder' => 'tbl_orders',
				'selectColums' => 'reference_no',
				'tableDataOrderDetail' => 'tbl_order_items',
				'IDDataOrderDetail' => 'order_id',
				'name_po' => 'Đơn hàng bán (SO)',
				'name_object' => 'Khách Hàng',
				'name_type' => 'Sản Phẩm',
			],
			'materials' =>  [
				'tableType' => 'tbl_materials',
				'tableDataObject' => 'tblsuppliers',
				'IDDataObject' => 'id',
				'tableDataOrder' => 'tblpurchase_order',
				'selectColums' => 'CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code)',
				'tableDataOrderDetail' => 'tblpurchase_order_items',
				'IDDataOrderDetail' => 'id_purchase_order',
				'name_po' => 'Đơn đặt hàng (PO)',
				'name_object' => 'Nhà Cung Cấp',
				'name_type' => 'NPL',
			]
		];
    }

    public function check($type = 'products')
    {
        if (!$this->preView && !$this->preViewOwn) {
            access_denied();
        }
		if(!empty($type)) {
			if($type == 'products') {
				$data['title'] = 'Phiếu yêu cầu kiểm tra chất lượng (Sản Phẩm)';
			}
			else {
				$data['title'] = 'Phiếu yêu cầu kiểm tra chất lượng (NPL)';
			}
		}
		$data['type'] = $type;
		$data['type_object'] = $this->type_object_check[$type];
        $this->load->view('admin/suggest_test_item_quality/manage_check', $data);
    }
	
	public function table_check($type = 'products') {
		if($type == 'products') {
			$_data_type = $this->type_object_check[$type];
			
		}
		else {
			$_data_type = $this->type_object_check['materials'];
		}
		
		
		
		$selectNameDetail = "
			(
				SELECT GROUP_CONCAT(CONCAT('- ',tbldata_items.name, ' <b class=\"text-danger\">(', tblsuggest_test_item_quality_detail.quantity, ')</b>') SEPARATOR '<br>')
				FROM tblsuggest_test_item_quality_detail
				JOIN ".$_data_type['tableType']." tbldata_items ON tbldata_items.id = tblsuggest_test_item_quality_detail.product_id
				WHERE tblsuggest_test_item_quality_detail.id_suggest_test_item_quality = tblsuggest_test_item_quality.id
			)
		";
		
		
		$aColumns = [
			'tblsuggest_test_item_quality.id as id',
			'tblsuggest_test_item_quality.code as code',
			'tblsuggest_test_item_quality.date as date',
			$_data_type['selectColums'] . ' as code_purchase_order',
			$_data_type['tableDataObject'] . '.company as company',
			$selectNameDetail . ' as list_item',
			'tblsuggest_test_item_quality.status as status',
			'(
				SELECT count(tbltasks.id)
					FROM tbltasks
					LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tbltasks.category_recommended_id
					WHERE tbltasks.suggest_id = tblsuggest_test_item_quality.id
					AND tbl_category_recommended.name_table="tblsuggest_test_item_quality"
					AND tbl_category_recommended.ballot_type = 1
					AND tbl_category_recommended.type = "'.$type.'"
				) as countTask',
			'tblsuggest_test_item_quality.note as note',
			'tblsuggest_test_item_quality.create_by as create_by',
		];
		$sWhere = [
			'AND tblsuggest_test_item_quality.type = "'.$type.'"',
		];
		if($this->input->post('start_date_search')) {
			$sWhere[] = 'AND tblsuggest_test_item_quality.date >= "'.to_sql_date($this->input->post('start_date_search'), true).'"';
		}
		if($this->input->post('end_date_search')) {
			$sWhere[] = 'AND tblsuggest_test_item_quality.date <= "'.to_sql_date($this->input->post('end_date_search'), true).'"';
		}
		$join = [];
		$join[] = 'LEFT JOIN '.$_data_type['tableDataObject'].' ON '.$_data_type['tableDataObject'].'.'.$_data_type['IDDataObject'].' = tblsuggest_test_item_quality.id_supplier';
		$join[] = 'LEFT JOIN '.$_data_type['tableDataOrder'].' ON '.$_data_type['tableDataOrder'].'.id = tblsuggest_test_item_quality.id_purchase_order';
		$join[] = 'LEFT JOIN tbl_category_recommended
			ON tbl_category_recommended.name_table = "tblsuggest_test_item_quality"
			AND tbl_category_recommended.ballot_type = 1
			AND tbl_category_recommended.type = "'.$type.'"';
		$sIndexColumn = 'id';
		$sTable       = 'tblsuggest_test_item_quality';
		$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere, [
			'tblsuggest_test_item_quality.staff_status',
			'tblsuggest_test_item_quality.date_status',
			'tbl_category_recommended.id as category_recommended_id'
		]);
		$output       = $result['output'];
		$rResult      = $result['rResult'];
		foreach ($rResult as $aRow) {
			$row = [];
			$row[] = $aRow['id'];
			$row[] = $aRow['code'];
			$row[] = _dt($aRow['date']);
			$row[] = $aRow['code_purchase_order'];
			$row[] = $aRow['company'];
			$row[] = $aRow['list_item'];
			if ($aRow['status'] == 0) {
				$_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 1)\' id=\'agree\' suggest_repalce_id=\''.$aRow['id'].'\' value=\'1\' class=\'btn btn-success\'>'.lang('tnh_agree').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-danger po">'.lang('Chưa duyệt').'</span></div>';
			} else if ($aRow['status'] == 1) {
				$_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 0)\' id=\'agree\' suggest_repalce_id=\''.$aRow['id'].'\' value=\'0\' class=\'btn btn-danger\'>'.lang('Hủy duyệt').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-success po">'.lang('Đã duyệt').'</span></div>';
				$_data.= '<div style="margin-top: 5px"> Người duyệt: '.get_staff_full_name($aRow['staff_status']).'</div>';
			} else {
				$_data = '';
			}
			$row[] = '<div class="text-left" style="width: 100px">'.$_data.'</div>';
			
			
			if (!has_permission('tasks', '', 'create')) {
				$row[] = '';
			} else {
				$task = '<a class="btn btn-info btn-icon mbot5" onclick="new_task(\'' . admin_url('tasks/task?suggest_id=' . $aRow['id'] . '&category_recommended_id=' . $aRow['category_recommended_id']) . '\')">Tạo công việc</a>';
				if (!empty($aRow['countTask'])) {
					$data_tasks = get_table_where('tbltasks', ['suggest_id' => $aRow['id'], 'category_recommended_id' => $aRow['category_recommended_id']], '', 'result_array', '', 'tbltasks.id,tbltasks.name');
					$__data = '';
					$_data = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . $aRow['countTask'] . ' Phiếu
                        </button>';
					foreach ($data_tasks as $kk => $vv) {
						$__data .= '<li><a href="' . admin_url('tasks/view') . $vv['id'] . '" class="display-block main-tasks-table-href-name mbot5" onclick="init_task_modal(' . $vv['id'] . '); return false;">' . $vv['name'] . '</a>';
					}
					$_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $__data;
					$_data .= '</ul>';
					$_data .= '</div>';
					$task .= $_data;
					// $column[15] .= '<br/><span class="dropdown-toggle no_background label label-info mtop10">' . $aRow['countTask'] . ' phiếu công việc . </span>';
					// '(SELECT count(tbltasks.id) FROM tbltasks WHERE rel_id = tblinternal_proposal.id AND rel_type="internal_proposal") as countTask',
					
				}
				$row[] = $task;
			}
			
			
			$row[] = $aRow['note'];
			$fullname_CREATE = get_staff_full_name($aRow['create_by']);
			$profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
					'staff-profile-image-small',
				]) . '</a>';
			$row[] = $profile_CREATE . ' ' . $fullname_CREATE;
			$options = '';
			$options .= '<a class="btn btn-icon btn-default c_modal" href="'.(admin_url('suggest_test_item_quality/detail_check/' . $type . '/' . $aRow['id'])).'" ><i class="fa fa-edit"></i></a>';
			$options .= '<a class="btn btn-icon btn-danger c_delete" href="'.(admin_url('suggest_test_item_quality/remove_detail_check/' . $type)).'" data-id="'.$aRow['id'].'" ><i class="fa fa-remove"></i></a>';
			$row[] = $options;
			$output['aaData'][] = $row;
		}
		echo json_encode($output);die();
	}
	
	public function remove_detail_check($type = 'products') {
		$id = $this->input->post('id');
		if(!empty($id)) {
			$this->db->where('id', $id);
			$ktQuality = $this->db->get('tblsuggest_test_item_quality')->row();
			if(!empty($ktQuality)) {
				if($ktQuality->status) {
					echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Phiếu đã được duyệt không thể xóa']);die();
				}
				else {
					$this->db->where('id', $id);
					$success = $this->db->delete('tblsuggest_test_item_quality');
					
					$this->db->where('id_suggest_test_item_quality', $id);
					$this->db->delete('tblsuggest_test_item_quality_detail');
					
					$this->db->where('id_suggest_test_item_quality', $id);
					$this->db->delete('tblsuggest_test_item_quality_category');
					if(!empty($success)) {
						echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa Phiếu thành công']);die();
					}
				}
			}
		}
		
		echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa Phiếu không thành công']);die();
	}
	
	public function detail_check($type = '', $id = 0){
		if ($this->input->post()){
			$dataResut = [];
			$data = $this->input->post();
			if (empty($id)){
				if(empty($data['code'])) {
					if($type == 'products') {
						$code = 'KTCLSP-' . date('YmdHis');
					}
					else {
						$code = 'KTCLNPL-' . date('YmdHis');
					}
					
				}
				else {
					$code = $data['code'];
				}
				$fields = [
					'code' => $code,
					'date' => to_sql_date($data['date'], true),
					'id_purchase_order' => $data['id_purchase_order'],
					'id_supplier' => $data['id_supplier'],
					'type' => $type,
					'note' => $this->input->post('note', false),
					'create_by' => get_staff_user_id(),
				];
				$success = $this->db->insert('tblsuggest_test_item_quality',$fields);
				if (!empty($success)){
					$id = $this->db->insert_id();
					if (!empty($data['id_items'])){
						foreach ($data['id_items'] as $key => $value){
							$this->db->insert('tblsuggest_test_item_quality_detail', [
								'id_suggest_test_item_quality' => $id,
								'type' => $type,
								'product_id' => $value,
								'quantity' => !empty($data['quantity'][$value]) ? $data['quantity'][$value] : 0,
							]);
						}
					}
					insertActivityLog([
						'type_parent_obj' => 'suggest_test_item_quality',
						'table_obj' => 'tblsuggest_test_item_quality',
						'id_obj' => $id,
						'name_obj' => $code,
						'content' => lang('Thêm mới yêu cầu kiểm tra') . ' [' . $code . ']',
						'actions' => 'add'
					]);
					$dataResut['success'] = true;
					$dataResut['alert_type'] = 'success';
					$dataResut['message'] = lang('Thêm mới thành công');
				} else {
					$dataResut['success'] = false;
					$dataResut['alert_type'] = 'danger';
					$dataResut['message'] = lang('Thêm mới không thành công');
				}
				
				echo json_encode($dataResut);return;
			}
			else {
				$this->db->where('id', $id);
				$suggest_test_item_quality = $this->db->get('tblsuggest_test_item_quality')->row();
				$fields = [
					'date' => to_sql_date($data['date'], true),
					'id_purchase_order' => $data['id_purchase_order'],
					'id_supplier' => $data['id_supplier'],
					'type' => $type,
					'note' => $this->input->post('note'),
				];
				$this->db->where('id', $id);
				$success = $this->db->update('tblsuggest_test_item_quality',$fields);
				if (!empty($success)){
					if(!empty($data['id_items'])) {
						$this->db->where_not_in('product_id', $data['id_items']);
					}
					$this->db->where('type', $type);
					$this->db->where('id_suggest_test_item_quality', $id);
					$this->db->delete('tblsuggest_test_item_quality_detail');
					
					if (!empty($data['id_items'])) {
						foreach ($data['id_items'] as $key => $value) {
							$this->db->insert('tblsuggest_test_item_quality_detail', [
								'id_suggest_test_item_quality' => $id,
								'type' => $type,
								'product_id' => $value,
								'quantity' => !empty($data['quantity'][$value]) ? $data['quantity'][$value] : 0,
							]);
						}
					}
					insertActivityLog([
						'type_parent_obj' => 'suggest_test_item_quality',
						'table_obj' => 'tblsuggest_test_item_quality',
						'id_obj' => $id,
						'name_obj' => $suggest_test_item_quality->code,
						'content' => lang('Sửa yêu cầu kiểm tra') . ' [' . $suggest_test_item_quality->code . ']',
						'actions' => 'edit'
					]);
					$dataResut['success'] = true;
					$dataResut['alert_type'] = 'success';
					$dataResut['message'] = lang('Cập nhật thành công');
				} else {
					$dataResut['success'] = false;
					$dataResut['alert_type'] = 'danger';
					$dataResut['message'] = lang('Cập nhật không thành công');
				}
				
				echo json_encode($dataResut);return;
			}
		}
		else {
			if (empty($id)){
				if (!$this->preAdd){
					accessDenied(true);
				}
				if($type == 'products') {
					$data['title'] = 'Thêm phiếu yêu cầu kiểm tra chất lượng (Sản Phẩm)';
				}
				else {
					$data['title'] = 'Thêm phiếu yêu cầu kiểm tra chất lượng (NPL)';
				}
				
				$data['codeDefault'] = 'YCKTCL-' . date('YmdHis');
			} else {
				if (!$this->preEdit){
					accessDenied(true);
				}
				
				if($type == 'products') {
					$_data_type = $this->type_object_check[$type];
				}
				else {
					$_data_type = $this->type_object_check['materials'];
				}
				
				
				
				
				
				
				$this->db->select(['tblsuggest_test_item_quality.*',
					$_data_type['selectColums'] . ' as code_purchase_order',
					$_data_type['tableDataObject'] . '.company'
				]);
				$this->db->where('tblsuggest_test_item_quality.id', $id);
				$this->db->join($_data_type['tableDataOrder'], $_data_type['tableDataOrder'] . '.id = tblsuggest_test_item_quality.id_purchase_order', 'left');
				$this->db->join($_data_type['tableDataObject'], $_data_type['tableDataObject'] .'.'.$_data_type['IDDataObject'].' = tblsuggest_test_item_quality.id_supplier', 'left');
				$data['suggest_test_item_quality'] = $this->db->get('tblsuggest_test_item_quality')->row();
				
				$data['suggest_test_item_quality']->detail = $this->db->get_where('tblsuggest_test_item_quality_detail', ['id_suggest_test_item_quality' => $id])->result_array();
				$data['id_items'] = [];
				foreach($data['suggest_test_item_quality']->detail as $key => $value) {
					$data['id_items'][$value['product_id']] = true;
				}
				
				
				if($type == 'products') {
					// lấy chi tiết đơn hàng bán
					$this->db->where(($_data_type['tableDataOrderDetail'] . '.' . $_data_type['IDDataOrderDetail']), $data['suggest_test_item_quality']->id_purchase_order);
					$this->db->where(($_data_type['tableDataOrderDetail'] . '.type_item'), $type);
					$this->db->select('
						tbldataitems.id as id,
						tbldataitems.name as name,
						tbldataitems.code as code,
						'.$_data_type['tableDataOrderDetail'].'.quantity as quantity,
					', false);
					$this->db->from($_data_type['tableDataOrderDetail']);
					$this->db->join('tbl_products tbldataitems', 'tbldataitems.id = '.$_data_type['tableDataOrderDetail'].'.item_id');
					$data['items'] = $this->db->get()->result_array();
				}
				else {
					
					// lấy chi tiết đơn hàng PO
					if($type == 'products') {
						$_type = 'product';
					}
					else {
						$_type = 'nvl';
					}
					
					$this->db->where(($_data_type['tableDataOrderDetail'] . '.' . $_data_type['IDDataOrderDetail']), $data['suggest_test_item_quality']->id_purchase_order);
					$this->db->where(($_data_type['tableDataOrderDetail'] . '.type'), $_type);
					$this->db->select('
						tbldataitems.id as id,
						tbldataitems.name as name,
						tbldataitems.code as code,
						'.$_data_type['tableDataOrderDetail'].'.quantity as quantity,
					', false);
					$this->db->from($_data_type['tableDataOrderDetail']);
					if($_type == 'product') {
						$this->db->join('tbl_products tbldataitems', 'tbldataitems.id = '.$_data_type['tableDataOrderDetail'].'.product_id');
					}
					else {
						$this->db->join('tbl_materials tbldataitems', 'tbldataitems.id = '.$_data_type['tableDataOrderDetail'].'.product_id');
					}
					$data['items'] = $this->db->get()->result_array();
				}
				
				
				
				
				if($type == 'products') {
					$data['title'] = 'Sửa phiếu yêu cầu kiểm tra chất lượng (Sản Phẩm)';
				}
				else {
					$data['title'] = 'Sửa phiếu yêu cầu kiểm tra chất lượng (NPL)';
				}
			}
		}
		
		$data['type_object'] = $this->type_object_check[$type];
		$data['type'] = $type;
		$data['id'] = $id;
		$this->load->view('admin/suggest_test_item_quality/detail_check',$data);
	}
	
	public function agree() {
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		
		if(!empty($status)) {
			$this->db->where('id', $id);
			$suggest_test_item_quality = $this->db->get('tblsuggest_test_item_quality')->row();
			if($suggest_test_item_quality->type == 'products') {
				$code_evaluate = 'YCĐGSP-'.date('YmdHis');
			}
			else {
				$code_evaluate = 'YCĐGNPL-'.date('YmdHis');
			}
			$this->db->where('id', $id);
			$success = $this->db->update('tblsuggest_test_item_quality', [
				'status' => $status,
				'date_status' => date('Y-m-d H:i:s'),
				'staff_status' => get_staff_user_id(),
				'code_evaluate' => $code_evaluate
			]);
			
			if(!empty($success)) {
				$this->db->where('id_suggest_test_item_quality', $id);
				$items = $this->db->get('tblsuggest_test_item_quality_detail')->result_array();
				if(!empty($items)) {
					$this->db->where('type', $suggest_test_item_quality->type);
					$this->db->order_by('type_event', 'asc');
					$list_category = $this->db->get('tblcategory_test_item_quality')->result_array();
					$arrayItems = [];
					foreach($items as $key => $value) {
						foreach($list_category as $k => $v) {
							$arrayItems[] = [
								'id_suggest_test_item_quality' => $id,
								'id_suggest_test_item_quality_detail' => $value['id'],
								'id_category' => $v['id'],
							];
						}
					}
					if(!empty($arrayItems)) {
						$this->db->insert_batch('tblsuggest_test_item_quality_category', $arrayItems);
					}
				}
				
				echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Duyệt thành công']);die();
			}
			else {
				echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Duyệt không thành công']);die();
			}
		}
		else {
			$this->db->where('id_suggest_test_item_quality', $id);
			$this->db->where('is_result != 0', false, false);
			$ktSuccess = $this->db->get('tblsuggest_test_item_quality_category')->row();
			if(!empty($ktSuccess)) {
				echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Phiếu đã đánh giá chất lượng không thể đổi trạng thái']);die();
			}
			
			$this->db->where('id', $id);
			$success = $this->db->update('tblsuggest_test_item_quality', [
				'status' => 0,
				'date_status' => NULL,
				'staff_status' => NULL,
				'code_evaluate' => NULL
			]);
			if(!empty($success)) {
				$this->db->where('id_suggest_test_item_quality_detail', $id);
				$this->db->delete('tblsuggest_test_item_quality_category');
				echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Bỏ duyệt thành công']);die();
			}
			else {
				echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Bỏ duyệt không thành công']);die();
			}
		}
	}
	
	public function search_supplier_or_supplier($table = 'tblclients', $id = '') {
		$term = $this->input->get('term');
		$limit = get_option('select2_limit');
		if($table == 'tblclients') {
			$this->db->select(['tblclients.userid as id',
				'tblclients.company as text'
			], false);
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('tblclients.company', $term);
				$this->db->or_like('tblclients.zcode', $term);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$DataClient = $this->db->get('tblclients')->result_array();
			$data['results'][] = [
				'text' => lang('Khách hàng'),
				'children' => $DataClient
			];
			
			if (!empty($id)){
				$dtClient = $this->db->get_where('tblclients', [
					'userid' => $id
				])->row_array();
				$data['row'] = [
					'id' => $dtClient['userid'],
					'text' => $dtClient['company'],
				];
				
			}
		}
		else {
			$this->db->select(['tblsuppliers.id as id',
				'tblsuppliers.company as text'], false);
			$this->db->from($table);
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like($table . '.company', $term);
				$this->db->or_like('CONCAT(tblsuppliers.prefix, "-", tblsuppliers.code)', $term);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$DataSuppier = $this->db->get()->result_array();
			$data['results'][] = [
				'text' => lang('Nhà Cung Cấp'),
				'children' => $DataSuppier
			];
			
			if (!empty($id)){
				$dtSupplier = $this->db->get_where('tblsuppliers', [
					'id' => $id
				])->row_array();
				$data['row'] = [
					'id' => $dtSupplier['id'],
					'text' => $dtSupplier['company'],
				];
				
			}
		}
		
		echo json_encode($data);die();
	}
	
	public function search_purchase_order($table = 'tbl_orders' ,$id_supplier = '', $id = '') {
		$term = $this->input->get('term');
		$limit = get_option('select2_limit');
		if($table == 'tbl_orders') {
			if (!empty($id_supplier)) {
				$this->db->where('customer_id', $id_supplier);
			}
			$this->db->select('
				tbl_orders.id as id,
				tbl_orders.reference_no as text,
			', false);
			$this->db->from('tbl_orders');
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('tbl_orders.reference_no', $term);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$DataOrder = $this->db->get()->result_array();
			$data['results'][] = [
				'text' => lang('Đơn hàng mua (SO)'),
				'children' => $DataOrder
			];
			if (!empty($id)) {
				$dtOrder = $this->db->get_where('tbl_orders', [
					'id' => $id
				])->row_array();
				$data['row'] = [
					'id' => $dtOrder['id'],
					'text' => $dtOrder['reference_no'],
				];
			}
		}
		else {
			if (!empty($id_supplier)) {
				$this->db->where('suppliers_id', $id_supplier);
			}
			$this->db->select('
			tblpurchase_order.id as id,
			CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code) as text,
		', false);
			$this->db->from('tblpurchase_order');
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code)', $term);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$DataPurchaseOrder = $this->db->get()->result_array();
			$data['results'][] = [
				'text' => lang('Đơn Đặt Hàng (PO)'),
				'children' => $DataPurchaseOrder
			];
			if (!empty($id)) {
				$dtPurchaseOrder = $this->db->get_where('tblpurchase_order', [
					'id' => $id
				])->row_array();
				$data['row'] = [
					'id' => $dtPurchaseOrder['id'],
					'text' => $dtPurchaseOrder['prefix'] . '-' . $dtPurchaseOrder['code'],
				];
			}
		}
		echo json_encode($data);die();
	}
	
	public function get_items_purchase_order($table = 'tbl_order_items', $type = 'products', $id_purchase_order = '') {
		if($table == 'tbl_order_items') {
			if(!empty($id_purchase_order)) {
				$this->db->where('tbl_order_items.order_id', $id_purchase_order);
			}
			$name_type = '';
			if($type == 'products') {
				$type = 'products';
				$name_type = 'Sản Phẩm';
			}
			else {
				$type = 'nvl';
				$name_type = 'Nguyên Phụ Liệu';
			}
			$this->db->where('tbl_order_items.type_item', $type);
			$this->db->select('
				tbldataitems.id as id,
				tbldataitems.name as name,
				tbldataitems.code as code,
				tbl_order_items.quantity as quantity,
			', false);
			$this->db->from('tbl_order_items');
			if($type == 'products') {
				$this->db->join('tbl_products tbldataitems', 'tbldataitems.id = tbl_order_items.item_id');
			}
			else {
				$this->db->join('tbl_materials tbldataitems', 'tbldataitems.id = tbl_order_items.item_id');
			}
			$data = $this->db->get()->result_array();
		}
		else {
			if(!empty($id_purchase_order)) {
				$this->db->where('tblpurchase_order_items.id_purchase_order', $id_purchase_order);
			}
			$name_type = '';
			if($type == 'products') {
				$type = 'product';
				$name_type = 'Sản Phẩm';
			}
			else {
				$type = 'nvl';
				$name_type = 'Nguyên Phụ Liệu';
			}
			$this->db->where('tblpurchase_order_items.type', $type);
			$this->db->select('
			tbldataitems.id as id,
			tbldataitems.name as name,
			tbldataitems.code as code,
			tblpurchase_order_items.quantity as quantity,
		', false);
			$this->db->from('tblpurchase_order_items');
			if($type == 'product') {
				$this->db->join('tbl_products tbldataitems', 'tbldataitems.id = tblpurchase_order_items.product_id');
			}
			else {
				$this->db->join('tbl_materials tbldataitems', 'tbldataitems.id = tblpurchase_order_items.product_id');
			}
			$data = $this->db->get()->result_array();
		}
		
		echo json_encode($data);die();
	}
	
	
	public function evaluate($type = 'products')
	{
		if (!$this->preView && !$this->preViewOwn) {
			access_denied();
		}
		if(!empty($type)) {
			if($type == 'products') {
				$data['title'] = 'Phiếu yêu cầu đánh giá chất lượng (Sản Phẩm)';
			}
			else {
				$data['title'] = 'Phiếu yêu cầu đánh giá chất lượng (NPL)';
			}
		}
		$data['type'] = $type;
		$data['type_object'] = $this->type_object_check[$type];
		$this->load->view('admin/suggest_test_item_quality/manage_evaluate', $data);
	}
	
	public function table_evaluate($type = 'products') {
		if($type == 'products') {
			$_data_type = $this->type_object_check[$type];
			
		}
		else {
			$_data_type = $this->type_object_check['materials'];
		}
		
		$selectNameDetail = "
			(
				SELECT GROUP_CONCAT(CONCAT('- ',tbldata_items.name, ' <b class=\"text-danger\">(', tblsuggest_test_item_quality_detail.quantity, ')</b>') SEPARATOR '<br>')
				FROM tblsuggest_test_item_quality_detail
				JOIN ".$_data_type['tableType']." tbldata_items ON tbldata_items.id = tblsuggest_test_item_quality_detail.product_id
				WHERE tblsuggest_test_item_quality_detail.id_suggest_test_item_quality = tblsuggest_test_item_quality.id
			)
		";
		
		
		$aColumns = [
			'tblsuggest_test_item_quality.id as id',
			'tblsuggest_test_item_quality.code_evaluate as code_evaluate',
			'tblsuggest_test_item_quality.code as code',
			'tblsuggest_test_item_quality.date_status as date',
			$_data_type['selectColums'] . ' as code_purchase_order',
			$_data_type['tableDataObject']. '.company as company',
			$selectNameDetail . ' as list_item',
			'(
				SELECT count(tbltasks.id)
				FROM tbltasks
				LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tbltasks.category_recommended_id
				WHERE tbltasks.suggest_id = tblsuggest_test_item_quality.id
				AND tbl_category_recommended.name_table="tblsuggest_test_item_quality"
				AND tbl_category_recommended.ballot_type = 2
				AND tbl_category_recommended.type = "'.$type.'"
			) as countTask',
			'tblsuggest_test_item_quality.note as note',
			'tblsuggest_test_item_quality.create_by as create_by',
		];
		$sWhere = [
			'AND tblsuggest_test_item_quality.type = "'.$type.'"',
			'AND tblsuggest_test_item_quality.status = "1"',
		];
		if($this->input->post('start_date_search')) {
			$sWhere[] = 'AND tblsuggest_test_item_quality.date_status >= "'.to_sql_date($this->input->post('start_date_search'), true).'"';
		}
		if($this->input->post('end_date_search')) {
			$sWhere[] = 'AND tblsuggest_test_item_quality.date_status <= "'.to_sql_date($this->input->post('end_date_search'), true).'"';
		}
		
		$join = [];
		$join[] = 'LEFT JOIN '.$_data_type['tableDataObject'].' ON '.$_data_type['tableDataObject'].'.'.$_data_type['IDDataObject'].' = tblsuggest_test_item_quality.id_supplier';
		$join[] = 'LEFT JOIN '.$_data_type['tableDataOrder'].' ON '.$_data_type['tableDataOrder'].'.id = tblsuggest_test_item_quality.id_purchase_order';
		$join[] = 'LEFT JOIN tbl_category_recommended
			ON tbl_category_recommended.name_table = "tblsuggest_test_item_quality"
			AND tbl_category_recommended.ballot_type = 2
			AND tbl_category_recommended.type = "'.$type.'"';
		$sIndexColumn = 'id';
		$sTable       = 'tblsuggest_test_item_quality';
		$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere, [
			'tblsuggest_test_item_quality.staff_status',
			'tblsuggest_test_item_quality.date_status',
			'tbl_category_recommended.id as category_recommended_id'
		]);
		$output = $result['output'];
		$rResult  = $result['rResult'];
		foreach ($rResult as $aRow) {
			$row = [];
			$row[] = $aRow['id'];
			$row[] = '<a class="c_modal" href="'.admin_url('suggest_test_item_quality/view_evaluate/' . $aRow['id']).'">' . $aRow['code_evaluate'] . '</a>';
			$row[] = $aRow['code'];
			$row[] = _dt($aRow['date']);
			$row[] = $aRow['code_purchase_order'];
			$row[] = $aRow['company'];
			$row[] = $aRow['list_item'];
			if (!has_permission('tasks', '', 'create')) {
				$row[] = '';
			} else {
				$task = '<a class="btn btn-info btn-icon mbot5" onclick="new_task(\'' . admin_url('tasks/task?suggest_id=' . $aRow['id'] . '&category_recommended_id=' . $aRow['category_recommended_id']) . '\')">Tạo công việc</a>';
				if (!empty($aRow['countTask'])) {
					$data_tasks = get_table_where('tbltasks', ['suggest_id' => $aRow['id'], 'category_recommended_id' => $aRow['category_recommended_id']], '', 'result_array', '', 'tbltasks.id,tbltasks.name');
					$__data = '';
					$_data = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . $aRow['countTask'] . ' Phiếu
                        </button>';
					foreach ($data_tasks as $kk => $vv) {
						$__data .= '<li><a href="' . admin_url('tasks/view') . $vv['id'] . '" class="display-block main-tasks-table-href-name mbot5" onclick="init_task_modal(' . $vv['id'] . '); return false;">' . $vv['name'] . '</a>';
					}
					$_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $__data;
					$_data .= '</ul>';
					$_data .= '</div>';
					$task .= $_data;
				}
				$row[] = $task;
			}
			$row[] = $aRow['note'];
			$fullname_CREATE = get_staff_full_name($aRow['create_by']);
			$profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
					'staff-profile-image-small',
				]) . '</a>';
			$row[] = $profile_CREATE . ' ' . $fullname_CREATE;
			$options = '';
			$options .= '<a class="btn btn-icon btn-default c_modal" href="'.(admin_url('suggest_test_item_quality/view_evaluate/' . $aRow['id'])).'" ><i class="fa fa-edit"></i></a>';
			$options .= '<a class="btn btn-icon btn-default" target="_blank" href="'.(admin_url('suggest_test_item_quality/export_excel/' . $aRow['id'])).'"><i class="fa fa-file-excel-o"></i></a>';
			$row[] = $options;
			$output['aaData'][] = $row;
		}
		echo json_encode($output);die();
	}
	
	public function update_note_result() {
		$id = $this->input->post('id');
		$note = $this->input->post('note', false);
		
		$this->db->where('id', $id);
		$success = $this->db->update('tblsuggest_test_item_quality_category', ['note' => $note]);
		if(!empty($success)){
			echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Cập nhật dữ liệu thành công']);return;
		}
		echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Cập nhật dữ liệu không thành công']);return;
	}
	
	public function view_evaluate($id = '') {
		$_is_suggest_test_item_quality = $this->db->get_where('tblsuggest_test_item_quality', ['id' => $id])->row();
		$data['type_object'] = $_data_type = $this->type_object_check[$_is_suggest_test_item_quality->type];
		$this->db->select([
			'tblsuggest_test_item_quality.*',
			$_data_type['selectColums'] . ' as code_purchase_order',
			$_data_type['tableDataObject'].'.company as company',
		]);
		$this->db->where('tblsuggest_test_item_quality.id', $id);
		$this->db->where('tblsuggest_test_item_quality.status', 1);
		$this->db->join($_data_type['tableDataOrder'], $_data_type['tableDataOrder'] . '.id = tblsuggest_test_item_quality.id_purchase_order', 'left');
		$this->db->join($_data_type['tableDataObject'], $_data_type['tableDataObject'].'.'.$_data_type['IDDataObject'].' = tblsuggest_test_item_quality.id_supplier', 'left');
		$suggest_test_item_quality = $this->db->get('tblsuggest_test_item_quality')->row();
		if(!empty($suggest_test_item_quality)) {
			$this->db->select('tblsuggest_test_item_quality_detail.*, tbldataItems.name as name_product, tbldataItems.code as code_product');
			if($suggest_test_item_quality->type == 'products') {
				$this->db->join('tbl_products tbldataItems', 'tbldataItems.id = tblsuggest_test_item_quality_detail.product_id');
			}
			else {
				$this->db->join('tbl_materials tbldataItems', 'tbldataItems.id = tblsuggest_test_item_quality_detail.product_id');
			}
			$this->db->where('id_suggest_test_item_quality', $suggest_test_item_quality->id);
			$suggest_test_item_quality->detail = $this->db->get('tblsuggest_test_item_quality_detail')->result_array();
			foreach($suggest_test_item_quality->detail as $key => $value) {
				$this->db->select([
					'tblsuggest_test_item_quality_category.*',
					'tblcategory_test_item_quality.name as name_category',
					'tblcategory_test_item_quality.standard as standard',
					'tblcategory_test_item_quality.tools as tools',
				]);
				$this->db->where('id_suggest_test_item_quality_detail', $value['id']);
				$this->db->where('type_event', 1);
				$this->db->join('tblcategory_test_item_quality', 'tblcategory_test_item_quality.id = tblsuggest_test_item_quality_category.id_category');
				$suggest_test_item_quality->detail[$key]['list_category'][1] = $this->db->get('tblsuggest_test_item_quality_category')->result_array();
				
				$this->db->select([
					'tblsuggest_test_item_quality_category.*',
					'tblcategory_test_item_quality.name as name_category',
					'tblcategory_test_item_quality.standard as standard',
					'tblcategory_test_item_quality.tools as tools',
				]);
				$this->db->where('id_suggest_test_item_quality_detail', $value['id']);
				$this->db->where('type_event', 2);
				$this->db->join('tblcategory_test_item_quality', 'tblcategory_test_item_quality.id = tblsuggest_test_item_quality_category.id_category');
				$suggest_test_item_quality->detail[$key]['list_category'][2] = $this->db->get('tblsuggest_test_item_quality_category')->result_array();
			}
		}
		$data['suggest_test'] = $suggest_test_item_quality;
		$data['title'] = 'Chi tiết phiếu đánh giá';
		if(!empty($suggest_test_item_quality->type)) {
			if($suggest_test_item_quality->type == 'products') {
				$data['title'] = 'Chi tiết phiếu yêu cầu đánh giá chất lượng (Sản Phẩm)';
			}
			else {
				$data['title'] = 'Chi tiết phiếu yêu cầu đánh giá chất lượng (NPL)';
			}
		}
		$data['type'] = $suggest_test_item_quality->type;
		$this->load->view('admin/suggest_test_item_quality/view_evaluete', $data);
	}
	
	public function check_result() {
		$result = $this->input->get('result');
		$colums = $this->input->get('colums');
		$id = $this->input->get('id');
		if(!empty($id) && !empty($colums)) {
			if($colums == 'sample_one') {
				if(!empty($result)) {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_one' => $result,
						'staff_sample_one' => get_staff_user_id(),
						'date_sample_one' => date('Y-m-d H:i:s')
					]);
				}
				else {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_one' => 0,
						'staff_sample_one' => NULL,
						'date_sample_one' => NULL
					]);
				}
			}
			else if($colums == 'sample_two') {
				if(!empty($result)) {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_two' => $result,
						'staff_sample_two' => get_staff_user_id(),
						'date_sample_two' => date('Y-m-d H:i:s')
					]);
				}
				else {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_two' => 0,
						'staff_sample_two' => NULL,
						'date_sample_two' => NULL
					]);
				}
			}
			else if($colums == 'sample_three') {
				if(!empty($result)) {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_three' => $result,
						'staff_sample_three' => get_staff_user_id(),
						'date_sample_three' => date('Y-m-d H:i:s')
					]);
				}
				else {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_three' => 0,
						'staff_sample_three' => NULL,
						'date_sample_three' => NULL
					]);
				}
			}
			else if($colums == 'sample_four') {
				if(!empty($result)) {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_four' => $result,
						'staff_sample_four' => get_staff_user_id(),
						'date_sample_four' => date('Y-m-d H:i:s')
					]);
				}
				else {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_four' => 0,
						'staff_sample_four' => NULL,
						'date_sample_four' => NULL
					]);
				}
			}
			else if($colums == 'sample_five') {
				if(!empty($result)) {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_five' => $result,
						'staff_sample_five' => get_staff_user_id(),
						'date_sample_five' => date('Y-m-d H:i:s')
					]);
				}
				else {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_five' => 0,
						'staff_sample_five' => NULL,
						'date_sample_five' => NULL
					]);
				}
			}
			else if($colums == 'is_result') {
				if(!empty($result)) {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'is_result' => $result,
						'staff_result' => get_staff_user_id(),
						'date_result' => date('Y-m-d H:i:s')
					]);
				}
				else {
					$this->db->where('id', $id);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'is_result' => 0,
						'staff_result' => NULL,
						'date_result' => NULL
					]);
				}
			}
			
			if(!empty($result)) {
				if(!empty($success)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Duyệt kết quả thành công'
					]);die();
				}
				else {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Duyệt kết quả không thành công'
					]);die();
				}
			}
			else {
				if(!empty($success)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Bỏ duyệt kết quả thành công'
					]);
					die();
				}
				else {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Duyệt kết quả không thành công'
					]);die();
				}
			}
		}
	}
	
	public function check_result_list() {
		$result = $this->input->post('result');
		$colums = $this->input->post('colums');
		$id = $this->input->post('id');
		$type_event = $this->input->post('type');
		if(!empty($id) && !empty($colums)) {
			$this->db->select('GROUP_CONCAT(id) as list_id');
			$this->db->where('type_event', $type_event);
			$list_category = $this->db->get('tblcategory_test_item_quality')->row('list_id');
			$list_category = explode(',', $list_category);
			if($colums == 'sample_one') {
				if(!empty($result)) {
					$this->db->where('id_suggest_test_item_quality_detail', $id);
					$this->db->where_in('id_category', $list_category);
					$this->db->where($colums, $result);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_one' => $result,
						'staff_sample_one' => get_staff_user_id(),
						'date_sample_one' => date('Y-m-d H:i:s')
					]);
				}
			}
			else if($colums == 'sample_two') {
				if(!empty($result)) {
					$this->db->where('id_suggest_test_item_quality_detail', $id);
					$this->db->where_in('id_category', $list_category);
					$this->db->where($colums, $result);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_two' => $result,
						'staff_sample_two' => get_staff_user_id(),
						'date_sample_two' => date('Y-m-d H:i:s')
					]);
				}
			}
			else if($colums == 'sample_three') {
				if(!empty($result)) {
					$this->db->where('id_suggest_test_item_quality_detail', $id);
					$this->db->where_in('id_category', $list_category);
					$this->db->where($colums, $result);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_three' => $result,
						'staff_sample_three' => get_staff_user_id(),
						'date_sample_three' => date('Y-m-d H:i:s')
					]);
				}
			}
			else if($colums == 'sample_four') {
				if(!empty($result)) {
					$this->db->where('id_suggest_test_item_quality_detail', $id);
					$this->db->where_in('id_category', $list_category);
					$this->db->where($colums, $result);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_four' => $result,
						'staff_sample_four' => get_staff_user_id(),
						'date_sample_four' => date('Y-m-d H:i:s')
					]);
				}
			}
			else if($colums == 'sample_five') {
				if(!empty($result)) {
					$this->db->where('id_suggest_test_item_quality_detail', $id);
					$this->db->where_in('id_category', $list_category);
					$this->db->where($colums, $result);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'sample_five' => $result,
						'staff_sample_five' => get_staff_user_id(),
						'date_sample_five' => date('Y-m-d H:i:s')
					]);
				}
			}
			else if($colums == 'is_result') {
				if(!empty($result)) {
					$this->db->where('id_suggest_test_item_quality_detail', $id);
					$this->db->where_in('id_category', $list_category);
					$this->db->where($colums, $result);
					$success = $this->db->update('tblsuggest_test_item_quality_category', [
						'is_result' => $result,
						'staff_result' => get_staff_user_id(),
						'date_result' => date('Y-m-d H:i:s')
					]);
				}
			}
			
			if(!empty($result)) {
				if(!empty($success)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Duyệt kết quả thành công'
					]);die();
				}
				else {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Duyệt kết quả không thành công'
					]);die();
				}
			}
			else {
				if(!empty($success)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Bỏ duyệt kết quả thành công'
					]);
					die();
				}
				else {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Duyệt kết quả không thành công'
					]);die();
				}
			}
		}
		
		
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Duyệt kết quả không thành công'
		]);die();
	}
	
	public function export_excel($id = '') {
		$_is_suggest_test_item_quality = $this->db->get_where('tblsuggest_test_item_quality', ['id' => $id])->row();
		$data['type_object'] = $_data_type = $this->type_object_check[$_is_suggest_test_item_quality->type];
		
		$this->db->select([
			'tblsuggest_test_item_quality.*',
			$_data_type['selectColums'] . ' as code_purchase_order',
			$_data_type['tableDataObject'].'.company as company',
		]);
		$this->db->where('tblsuggest_test_item_quality.id', $id);
		$this->db->where('tblsuggest_test_item_quality.status', 1);
		$this->db->join($_data_type['tableDataOrder'], $_data_type['tableDataOrder'] . '.id = tblsuggest_test_item_quality.id_purchase_order', 'left');
		$this->db->join($_data_type['tableDataObject'], $_data_type['tableDataObject'].'.'.$_data_type['IDDataObject'].' = tblsuggest_test_item_quality.id_supplier', 'left');
		$suggest_test_item_quality = $this->db->get('tblsuggest_test_item_quality')->row();
		if(!empty($suggest_test_item_quality)) {
			$this->db->select('tblsuggest_test_item_quality_detail.*, tbldataItems.name as name_product, tbldataItems.code as code_product');
			if($suggest_test_item_quality->type == 'products') {
				$this->db->join('tbl_products tbldataItems', 'tbldataItems.id = tblsuggest_test_item_quality_detail.product_id');
			}
			else {
				$this->db->join('tbl_materials tbldataItems', 'tbldataItems.id = tblsuggest_test_item_quality_detail.product_id');
			}
			$this->db->where('id_suggest_test_item_quality', $suggest_test_item_quality->id);
			$suggest_test_item_quality->detail = $this->db->get('tblsuggest_test_item_quality_detail')->result_array();
			foreach($suggest_test_item_quality->detail as $key => $value) {
				$this->db->select([
					'tblsuggest_test_item_quality_category.*',
					'tblcategory_test_item_quality.name as name_category',
					'tblcategory_test_item_quality.standard as standard',
					'tblcategory_test_item_quality.tools as tools',
				]);
				$this->db->where('id_suggest_test_item_quality_detail', $value['id']);
				$this->db->where('type_event', 1);
				$this->db->join('tblcategory_test_item_quality', 'tblcategory_test_item_quality.id = tblsuggest_test_item_quality_category.id_category');
				$suggest_test_item_quality->detail[$key]['list_category'][1] = $this->db->get('tblsuggest_test_item_quality_category')->result_array();
				
				$this->db->select([
					'tblsuggest_test_item_quality_category.*',
					'tblcategory_test_item_quality.name as name_category',
					'tblcategory_test_item_quality.standard as standard',
					'tblcategory_test_item_quality.tools as tools',
				]);
				$this->db->where('id_suggest_test_item_quality_detail', $value['id']);
				$this->db->where('type_event', 2);
				$this->db->join('tblcategory_test_item_quality', 'tblcategory_test_item_quality.id = tblsuggest_test_item_quality_category.id_category');
				$suggest_test_item_quality->detail[$key]['list_category'][2] = $this->db->get('tblsuggest_test_item_quality_category')->result_array();
			}
		}
		
		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');
		
		$style_excel = style_excel();
		$cloumns_excel = cloumns_excel();
		
		$style_excel['Background_header_one'] = $style_excel['Background_header'];
		$style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';
		
		$style_excel['Background_header_two'] = $style_excel['Background_header'];
		$style_excel['Background_header_two']['fill']['color']['rgb'] = 'f79e83';
		
		$style_excel['Background_header_three'] = $style_excel['Background_header'];
		$style_excel['Background_header_three']['fill']['color']['rgb'] = '8ac78c';
		$style_excel['Background_header']['font']['bold'] = true;
		$style_excel['Background_header']['fill']['color']['rgb'] = 'fef7e2';
		
		$style_excel['Background_header_left'] = $style_excel['Background_header'];
		$style_excel['Background_header_left']['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
		
		
		
		$_objPHPExcel = new PHPExcel();
		foreach ($suggest_test_item_quality->detail as $key => $value) {
			$code_product = $value['code_product'];
			if (mb_strlen($value['code_product'], 'UTF-8') >= 28) {
				$code_product = mb_substr($value['code_product']	, 0, 28, 'UTF-8').'...';
			}
			
			
			if ($key > 0) {
				$objPHPExcelRow = new PHPExcel_Worksheet($_objPHPExcel, $code_product);
				$sheetIndex = $_objPHPExcel->addSheet($objPHPExcelRow, $key);
			} else {
				$sheetIndex = $_objPHPExcel->getActiveSheet();
				$sheetIndex->setTitle($code_product);
			}
			$sheetIndex->getPageMargins()->setTop(0.2); // ~ 1.78cm
			$sheetIndex->getPageMargins()->setHeader(0.2); // ~1.02cm
			$sheetIndex->getPageMargins()->setRight(0.2); // ~
			$sheetIndex->getPageMargins()->setLeft(0.2); // ~1.78cm
			$sheetIndex->getPageMargins()->setBottom(0.2); // ~1.73cm
			$sheetIndex->getPageMargins()->setFooter(0); // ~1.02cm
			$sheetIndex->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$sheetIndex->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$sheetIndex->getColumnDimension("A")->setWidth(5);
			$sheetIndex->getColumnDimension("B")->setWidth(10);
			$sheetIndex->getColumnDimension("C")->setWidth(10);
			$sheetIndex->getColumnDimension("D")->setWidth(10);
			$sheetIndex->getColumnDimension("E")->setWidth(10);
			$sheetIndex->getColumnDimension("F")->setWidth(10);
			$sheetIndex->getColumnDimension("G")->setWidth(10);
			$sheetIndex->getColumnDimension("H")->setWidth(10);
			$sheetIndex->getColumnDimension("I")->setWidth(10);
			$sheetIndex->getColumnDimension("J")->setWidth(15);
			$sheetIndex->getColumnDimension("K")->setWidth(15);
			$sheetIndex->getColumnDimension("L")->setWidth(15);
			$sheetIndex->getColumnDimension("M")->setWidth(15);
			$sheetIndex->getColumnDimension("N")->setWidth(15);
			$sheetIndex->getColumnDimension("O")->setWidth(20);
			$sheetIndex->getColumnDimension("P")->setWidth(10);
			$sheetIndex->getColumnDimension("Q")->setWidth(10);
			
			$img = FCPATH . 'uploads/company/' . get_option('company_logo');
			if (!empty($img)) {
				$objDrawing1 = new PHPExcel_Worksheet_Drawing();
				$objDrawing1->setWorksheet($sheetIndex);
				$objDrawing1->setPath($img);
				$objDrawing1->setWidth(80);
				$objDrawing1->setHeight(80);
				$objDrawing1->setOffsetX(3);
				$objDrawing1->setOffsetY(2);
				$objDrawing1->setCoordinates('A1');
			}
			$sheetIndex->getRowDimension(1)->setRowHeight(42);
			$sheetIndex->setCellValue('A1', '')->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheetIndex->mergeCells("A1:B3")->getStyle("A1:B3");
			
			
			$sheetIndex->setCellValue('C1', 'PHIẾU KIỂM TRA '.($suggest_test_item_quality->type == 'products' ? 'SẢN PHẨM' : 'NPL').' ĐẦU VÀO')->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheetIndex->mergeCells("C1:Q3")->getStyle("C1:Q3")->applyFromArray($style_excel['title']);
			
			
			$s = 0;
			$numberRow = 4;
			$sheetIndex->SetCellValue("A4", 'MÃ SỐ')->getStyle("A4")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("A4:B4")->getStyle("A4:B4")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("C4", $suggest_test_item_quality->code_evaluate)->getStyle("C4")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->mergeCells("C4:F4")->getStyle("C4:F4")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->SetCellValue("G4", 'NGÀY HIỆU LỰC')->getStyle("G4")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("G4:H4")->getStyle("G4:H4")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("I4", _dt($suggest_test_item_quality->date_status))->getStyle("J4")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->mergeCells("I4:K4")->getStyle("I4:K4")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->SetCellValue("L4", 'LẦN BAN HÀNH')->getStyle("L4")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("L4:M4")->getStyle("L4:M4")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("N4", '1')->getStyle("N4")->applyFromArray($style_excel['c_td_center']);
			$sheetIndex->SetCellValue("O4", 'TRANG/TỔNG')->getStyle("O4")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("O4:P4")->getStyle("O4:P4")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("Q4", '1/1')->getStyle("Q4")->applyFromArray($style_excel['c_td_center']);
			$sheetIndex->SetCellValue("A5", ('Loại ' . $_data_type['name_type']))->getStyle("A5")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("A5:B5")->getStyle("A5:B5")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("C5", 'Giấy')->getStyle("C5")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("D5", '')->getStyle("D5")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->SetCellValue("F5", 'Decal')->getStyle("F5")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("G5", '')->getStyle("G5")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->SetCellValue("I5", 'Mực')->getStyle("I5")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("J5", '')->getStyle("J5")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->SetCellValue("L5", 'Khác')->getStyle("L5")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("M5", '')->getStyle("M5")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->mergeCells("M5:Q5")->getStyle("M5:Q5")->applyFromArray($style_excel['c_td_left']);
			
			$numberRow = 6;
			$sheetIndex->SetCellValue("A6", ('Tên '  . $_data_type['name_type']))->getStyle("A6")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("A6:B6")->getStyle("A6:B6")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("C6", $value['code_product'])->getStyle("C6")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->mergeCells("C6:G6")->getStyle("C6:G6")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->SetCellValue("H6", 'Ngày Kiểm Tra')->getStyle("H6")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("H6:I6")->getStyle("H6:I6")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->SetCellValue("J6", _dt($suggest_test_item_quality->date_status))->getStyle("J6")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->mergeCells("J6:Q6")->getStyle("J6:Q6")->applyFromArray($style_excel['c_td_left']);
			
			$sheetIndex->SetCellValue("A7", 'Mô tả')->getStyle("A7")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("A7:B7")->getStyle("A7:B7")->applyFromArray($style_excel['Background_header']);
			
			$sheetIndex->SetCellValue("C7", '')->getStyle("C7")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->mergeCells("C7:G7")->getStyle("C7:G7")->applyFromArray($style_excel['c_td_left']);
			
			$sheetIndex->SetCellValue("H7", 'Số PO:')->getStyle("H7")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("H7:I7")->getStyle("H7:I7")->applyFromArray($style_excel['Background_header']);
			
			$sheetIndex->SetCellValue("J7", $suggest_test_item_quality->code_purchase_order)->getStyle("J6")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->mergeCells("J7:Q7")->getStyle("J7:Q7")->applyFromArray($style_excel['c_td_left']);
			
			
			$sheetIndex->SetCellValue("A8", 'Số lượng')->getStyle("A8")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("A8:B8")->getStyle("A8:B8")->applyFromArray($style_excel['Background_header']);
			
			$sheetIndex->SetCellValue("C8", $value['quantity'])->getStyle("C8")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->mergeCells("C8:G8")->getStyle("C8:G8")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->SetCellValue("H8", ($_data_type['name_object'].':'))->getStyle("H8")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("H8:I8")->getStyle("H8:I8")->applyFromArray($style_excel['Background_header']);
			
			$sheetIndex->SetCellValue("J8", $suggest_test_item_quality->company)->getStyle("J8")->applyFromArray($style_excel['c_td_left']);
			$sheetIndex->mergeCells("J8:Q8")->getStyle("J8:Q8")->applyFromArray($style_excel['c_td_left']);
			
			
			$numberRow = 9;
			$sheetIndex->SetCellValue("A$numberRow", 'I. Kiểm tra các tham số chung')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header_left']);
			$sheetIndex->mergeCells("A$numberRow:Q$numberRow")->getStyle("A$numberRow:Q$numberRow")->applyFromArray($style_excel['Background_header_left']);
			$numberRow++;
			$numberRowNext = ($numberRow + 1);
			$sheetIndex->SetCellValue("A$numberRow", 'STT')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("A$numberRow:A$numberRowNext")->getStyle("A$numberRow:A$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("B$numberRow", 'Hạng mục kiểm tra')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("B$numberRow:E$numberRowNext")->getStyle("B$numberRow:E$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("F$numberRow", 'Tiêu Chuẩn')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("F$numberRow:G$numberRowNext")->getStyle("F$numberRow:G$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("H$numberRow", 'Công Cụ')->getStyle("H$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("H$numberRow:I$numberRowNext")->getStyle("H$numberRow:I$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("J$numberRow", 'Kết Quả')->getStyle("J$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("J$numberRow:N$numberRow")->getStyle("J$numberRow:N$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("O$numberRow", "Kết Quả\n(Đạt/Không Đạt)")->getStyle("J$numberRow")->applyFromArray($style_excel['Background_header'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("O$numberRow:O$numberRowNext")->getStyle("O$numberRow:O$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("P$numberRow", 'Remarks')->getStyle("P$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("P$numberRow:Q$numberRowNext")->getStyle("P$numberRow:Q$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("J$numberRowNext", 'Mẫu 1')->getStyle("J$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("K$numberRowNext", 'Mẫu 2')->getStyle("K$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("L$numberRowNext", 'Mẫu 3')->getStyle("L$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("M$numberRowNext", 'Mẫu 4')->getStyle("M$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("N$numberRowNext", 'Mẫu 5')->getStyle("N$numberRowNext")->applyFromArray($style_excel['Background_header']);
			$numberRow = $numberRowNext + 1;
			foreach ($value['list_category'][1] as $k => $v) {
				$sheetIndex->SetCellValue("A$numberRow", ($k + 1))->getStyle("A$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("B$numberRow", $v['name_category'])->getStyle("B$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->mergeCells("B$numberRow:E$numberRow")->getStyle("B$numberRow:E$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("F$numberRow", $v['standard'])->getStyle("F$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->mergeCells("F$numberRow:G$numberRow")->getStyle("F$numberRow:G$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("H$numberRow", $v['tools'])->getStyle("H$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->mergeCells("H$numberRow:I$numberRow")
					->getStyle("H$numberRow:I$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("J$numberRow", (!empty($v['sample_one']) ? ($v['sample_one'] == 1 ? '√' : 'X') : ''))->getStyle("J$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("K$numberRow", (!empty($v['sample_two']) ? ($v['sample_two'] == 1 ? '√' : 'X') : ''))->getStyle("K$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("L$numberRow", (!empty($v['sample_three']) ? ($v['sample_three'] == 1 ? '√' : 'X') : ''))->getStyle("L$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("M$numberRow", (!empty($v['sample_four']) ? ($v['sample_four'] == 1 ? '√' : 'X') : ''))->getStyle("M$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("N$numberRow", (!empty($v['sample_five']) ? ($v['sample_five'] == 1 ? '√' : 'X') : ''))->getStyle("N$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("O$numberRow", (!empty($v['is_result']) ? ($v['is_result'] == 1 ? '√' : 'X') : ''))->getStyle("O$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("P$numberRow", $v['note'])->getStyle("P$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->mergeCells("P$numberRow:Q$numberRow")->getStyle("P$numberRow:Q$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->getRowDimension($numberRow)->setRowHeight(-1);
				$numberRow++;
			}
			$sheetIndex->SetCellValue("A$numberRow", "II. Kiểm tra chất lượng ngoại quan ".($suggest_test_item_quality->type == 'products' ? 'sản phẩm' : 'NPL'))->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header_left']);
			$sheetIndex->mergeCells("A$numberRow:Q$numberRow")->getStyle("A$numberRow:Q$numberRow")->applyFromArray($style_excel['Background_header_left']);
			$numberRow++;
			$sheetIndex->SetCellValue("A$numberRow", 'STT')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("A$numberRow:A$numberRow")->getStyle("A$numberRow:A$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("B$numberRow", 'Hạng mục kiểm tra')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("B$numberRow:E$numberRow")->getStyle("B$numberRow:E$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->SetCellValue("F$numberRow", 'Tiêu chuẩn kiểm tra')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("F$numberRow:I$numberRow")->getStyle("F$numberRow:I$numberRow")->applyFromArray($style_excel['Background_header'])->getAlignment()->setWrapText(true);
			$sheetIndex->SetCellValue("J$numberRow", "Mẫu 1\n(Đạt/Không đạt)")->getStyle("J$numberRow")->applyFromArray($style_excel['Background_header'])->getAlignment()->setWrapText(true);
			$sheetIndex->SetCellValue("K$numberRow", "Mẫu 2\n(Đạt/Không đạt)")->getStyle("K$numberRow")->applyFromArray($style_excel['Background_header'])->getAlignment()->setWrapText(true);
			$sheetIndex->SetCellValue("L$numberRow", "Mẫu 3\n(Đạt/Không đạt)")->getStyle("L$numberRow")->applyFromArray($style_excel['Background_header'])->getAlignment()->setWrapText(true);
			$sheetIndex->SetCellValue("M$numberRow", "Mẫu 4\n(Đạt/Không đạt)")->getStyle("M$numberRow")->applyFromArray($style_excel['Background_header'])->getAlignment()->setWrapText(true);
			$sheetIndex->SetCellValue("N$numberRow", "Mẫu 5\n(Đạt/Không đạt)")->getStyle("N$numberRow")->applyFromArray($style_excel['Background_header'])->getAlignment()->setWrapText(true);
			$sheetIndex->SetCellValue("O$numberRow", 'Ghi Chú')->getStyle("O$numberRow")->applyFromArray($style_excel['Background_header']);
			$sheetIndex->mergeCells("O$numberRow:Q$numberRow")->getStyle("O$numberRow:Q$numberRow")->applyFromArray($style_excel['Background_header']);
			$numberRow++;
			foreach ($value['list_category'][2] as $k => $v) {
				$sheetIndex->SetCellValue("A$numberRow", ($k + 1))->getStyle("A$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("B$numberRow", $v['name_category'])->getStyle("B$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->mergeCells("B$numberRow:E$numberRow")->getStyle("B$numberRow:E$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("F$numberRow", $v['standard'])->getStyle("F$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->mergeCells("F$numberRow:I$numberRow")->getStyle("F$numberRow:I$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("J$numberRow", (!empty($v['sample_one']) ? ($v['sample_one'] == 1 ? '√' : 'X') : ''))->getStyle("J$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("K$numberRow", (!empty($v['sample_two']) ? ($v['sample_two'] == 1 ? '√' : 'X') : ''))->getStyle("K$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("L$numberRow", (!empty($v['sample_three']) ? ($v['sample_three'] == 1 ? '√' : 'X') : ''))->getStyle("L$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("M$numberRow", (!empty($v['sample_four']) ? ($v['sample_four'] == 1 ? '√' : 'X') : ''))->getStyle("M$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("N$numberRow", (!empty($v['sample_five']) ? ($v['sample_five'] == 1 ? '√' : 'X') : ''))->getStyle("N$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
				$sheetIndex->SetCellValue("O$numberRow", $v['note'])->getStyle("O$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->mergeCells("O$numberRow:Q$numberRow")->getStyle("O$numberRow:Q$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
				$sheetIndex->getRowDimension($numberRow)->setRowHeight(-1);
				$numberRow++;
			}
			
			$numberRowNext = $numberRow + 2;
			$sheetIndex->SetCellValue("A$numberRow", "Trạng thái kiểm tra đơn hàng")->getStyle("A$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("A$numberRow:J$numberRowNext")->getStyle("A$numberRow:J$numberRowNext")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("K$numberRow", "Số lượng kiểm")->getStyle("K$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("K$numberRow:N$numberRow")->getStyle("K$numberRow:N$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("O$numberRow", "")->getStyle("O$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("O$numberRow:Q$numberRow")->getStyle("O$numberRow:Q$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			
			$numberRow++;
			$sheetIndex->SetCellValue("K$numberRow", "Số lượng đạt")->getStyle("K$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("K$numberRow:N$numberRow")->getStyle("K$numberRow:N$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$sheetIndex->SetCellValue("O$numberRow", "")->getStyle("O$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("O$numberRow:Q$numberRow")->getStyle("O$numberRow:Q$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$numberRow++;
			
			$sheetIndex->SetCellValue("K$numberRow", "Số lượng lỗi")->getStyle("K$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("K$numberRow:N$numberRow")->getStyle("K$numberRow:N$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$sheetIndex->SetCellValue("O$numberRow", "")->getStyle("O$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("O$numberRow:Q$numberRow")->getStyle("O$numberRow:Q$numberRow")->applyFromArray($style_excel['c_td_left'])->getAlignment()->setWrapText(true);
			$numberRow++;
			
			$numberRowNext = $numberRow + 1;
			$sheetIndex->SetCellValue("A$numberRow", "Kết luận về ".($suggest_test_item_quality->type == 'products' ? 'sản phẩm' : 'nguyên phụ liệu').":")->getStyle("A$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("A$numberRow:E$numberRowNext")->getStyle("A$numberRow:E$numberRowNext")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("F$numberRow", "ĐẠT")->getStyle("F$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("F$numberRow:G$numberRow")->getStyle("F$numberRow:G$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("H$numberRow", "KHÔNG ĐẠT")->getStyle("H$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("H$numberRow:J$numberRow")->getStyle("H$numberRow:J$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("K$numberRow", "KHÁC")->getStyle("K$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("K$numberRow:Q$numberRow")->getStyle("K$numberRow:Q$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$numberRow++;
			
			$sheetIndex->SetCellValue("F$numberRow", "")->getStyle("F$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("F$numberRow:G$numberRow")->getStyle("F$numberRow:G$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("H$numberRow", "")->getStyle("H$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("H$numberRow:J$numberRow")->getStyle("H$numberRow:J$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("K$numberRow", "")->getStyle("K$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("K$numberRow:Q$numberRow")->getStyle("K$numberRow:Q$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$numberRow = $numberRowNext + 1;
			
			$sheetIndex->SetCellValue("A$numberRow", "Ghi chú:")->getStyle("A$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("A$numberRow:E$numberRow")->getStyle("A$numberRow:E$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("F$numberRow", "")->getStyle("F$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("F$numberRow:Q$numberRow")->getStyle("F$numberRow:Q$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$numberRow++;
			
			$sheetIndex->SetCellValue("A$numberRow", "Người kiểm tra")->getStyle("A$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("A$numberRow:E$numberRow")->getStyle("A$numberRow:E$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("F$numberRow", "")->getStyle("F$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("F$numberRow:J$numberRow")->getStyle("F$numberRow:J$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("K$numberRow", "Người xác nhận")->getStyle("K$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("K$numberRow:N$numberRow")->getStyle("K$numberRow:N$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			
			$sheetIndex->SetCellValue("O$numberRow", "")->getStyle("O$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$sheetIndex->mergeCells("O$numberRow:Q$numberRow")->getStyle("O$numberRow:Q$numberRow")->applyFromArray($style_excel['c_td_center'])->getAlignment()->setWrapText(true);
			$numberRow++;
		}

		$filename = lang('phieu_danh_gia_' . $suggest_test_item_quality->code_evaluate) . '.xls';
		$_objPHPExcel->getActiveSheet()->freezePane('A1');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($_objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
}