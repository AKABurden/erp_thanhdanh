<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Plan_propose extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		//		$this->load->model('suggestion_type_model');
		$this->load->model('plan_propose_model');
		$this->load->model('costs_model');
		$this->perView = has_permission('plan_propose', '', 'view');
		$this->perViewOwn = has_permission('plan_propose', '', 'view_own');
		$this->perAdd = has_permission('plan_propose', '', 'create');
		$this->perEdit = has_permission('plan_propose', '', 'edit');
		$this->perDelete = has_permission('plan_propose', '', 'delete');
		$this->perApprove = has_permission('plan_propose', '', 'approve_accept');
		$this->perPdf = has_permission('plan_propose', '', 'print');
		$this->type_plan_propose = type_plan_propose();
		$this->type_title_plan_propose = [];
		foreach ($this->type_plan_propose as $key => $value) {
			$this->type_title_plan_propose[$value['id']] = $value['name'];
		}
		$this->type_title = [
			'delivery' => 'Giao Hàng',
			'inventory' => 'Kiểm Kê',
			'maintenance' => 'Bảo Trì/Bảo Dưỡng',
			'understanding_standard' => 'Hiểu Chuẩn',
			'train' => 'Đào Tạo',
			'overtime' => 'Tăng Ca',
			'recruitment' => 'Tuyển Dụng',
			'machining' => 'Gia công',
			'repair' => 'Sửa chữa',
			'quotes' => 'Báo giá',
			'development_patterns' => 'Phát triển mẫu',
		];
		$this->is_branch = true;
	}

	public function index($type = '')
	{
		if (!$this->perView && !$this->perViewOwn) {
			access_denied('plan_propose');
		}
		$data['title'] = _l('c_plan_propose') . ' ' . (!empty($type) ? $this->type_title[$type] : '');
		$data['type'] = $type;
		$data['group'] = $this->input->get('group');
		$data['type_title'] = $this->type_title;
		$data['type_plan_propose'] = $this->type_plan_propose;
		$this->db->select('tblunits.unitid as id,unit as name');
		$data['units'] = $this->db->get_where('tblunits')->result_array();
		$data['units_cost'] = $this->db->get_where('tblcurrencies')->result_array();
		$data['staff'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();
		// $data['category_tasks'] = $this->db->get('tblcategory_tasks')->result_array();
		$data['category_tasks'] = $this->site_model->getCategoryTasks();
		$data['costs'] = array();
		$this->costs_model->get_by_id(0, $data['costs']);

		if ($data['group'] != 'vouchers_coupon') {
			$this->load->view('admin/plan_propose/manage', $data);
		} else {
			$this->load->view('admin/plan_propose/index_vouchers_coupon', $data);
		}
	}

	public function table()
	{
		$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
		$aColumns = [
			'tblplan_propose.id as id',
			'tblplan_propose.date as date',
			'tblplan_propose.code as code',
			'2',
			'3',
			'4',
			'tblplan_propose.type_plan_propose as type_plan_propose',
			'tblinternal_proposal.category_recommended_id as category_recommended_id',
			'tblbranch.name as name_branch',
			'tblplan_propose.staff as staff',
			'tblcategory_tasks.code as code_category_tasks',
			'1',
			'tblplan_propose.money as money',
			'tblplan_propose.approved_by as approved_by',
			'tblplan_propose.content as content',
			'"" as create_tasks',
			'"" as actions',
		];
		$sIndexColumn = 'id';
		$sTable = 'tblplan_propose';
		$where = [];
		if (!empty($this->input->post('filterStatus'))) {
			$filterStatus = $this->input->post('filterStatus');
			if ($filterStatus == 2) {
				$where[] = 'AND tblplan_propose.status = 1';
			} else if ($filterStatus == 3) {
				$where[] = 'AND tblplan_propose.status = 2';
			} else if ($filterStatus == 1) {
				$where[] = 'AND tblplan_propose.status = 0';
			} else {
				$where[] = 'AND tblplan_propose.type_plan_propose = "' . $filterStatus . '"';
			}
		}
		if (!empty($this->input->post('groups_search'))) {
			$groups_search = $this->input->post('groups_search');
			$where[] = 'AND tblplan_propose.type_plan_propose = "' . $groups_search . '"';
		}
		if ($this->input->post('date_start')) {
			$where[] = 'AND DATE_FORMAT(tblplan_propose.date, "%Y-%m-%d") >= "' . to_sql_date($this->input->post('date_start')) . '"';
		}
		if ($this->input->post('date_end')) {
			$where[] = 'AND DATE_FORMAT(tblplan_propose.date, "%Y-%m-%d") <= "' . to_sql_date($this->input->post('date_end')) . '"';
		}
		if ($this->input->post('staff_search')) {
			$where[] = 'AND tblplan_propose.staff = "' . $this->input->post('staff_search') . '"';
		}
		if ($this->input->post('category_tasks')) {
			$where[] = 'AND tblplan_propose.category_tasks = "' . $this->input->post('category_tasks') . '"';
		}
		$join = [
			'LEFT JOIN tblinternal_proposal ON tblinternal_proposal.id = tblplan_propose.id_internal_proposal',
			'LEFT JOIN tblsuggestion ON tblsuggestion.id = tblplan_propose.id_suggestion',
			'LEFT JOIN tblother_payslips ON tblother_payslips.id = tblplan_propose.id_other_payslips',
			'LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tblplan_propose.category_tasks',
			'LEFT JOIN tblpurchases ON tblpurchases.id = tblplan_propose.id_purchases',
			'LEFT JOIN tbl_services ON tbl_services.id = tblplan_propose.id_service',
			'LEFT JOIN tblpurchase_order ON tblpurchase_order.id = tblplan_propose.id_purchase_order',
			'LEFT JOIN tblbranch ON tblbranch.id = tblplan_propose.id_branch',
			'LEFT JOIN tblpurchase_order tblpurchase_order_laster ON tblpurchase_order_laster.id = tblplan_propose.id_purchase_order_internal',
			'LEFT JOIN tblsuppliers ON tblsuppliers.id = tblpurchase_order_laster.suppliers_id',
		];
		if (!$this->perView) {
			$where[] = 'AND (
							EXISTS (
								SELECT 1 
								FROM tblplan_propose_assigned 
								WHERE tblplan_propose_assigned.id_plan_propose = tblplan_propose.id
								AND tblplan_propose_assigned.id_staff = "' . get_staff_user_id() . '"
							)
							OR tblplan_propose.staff = "' . get_staff_user_id() . '"
				)';
		}
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_list_branch_staff();
				if (!empty($list_branch)) {
					$where[] = 'AND (tblplan_propose.id_branch IN (' . $list_branch . '))';
				} else {
					$where[] = 'AND tblplan_propose.id = 0';
				}
			}
		}

		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
			'tblplan_propose.date_create',
			'tblplan_propose.id_suggestion',
			'tblinternal_proposal.suggest_id',
			'tblsuggestion.code as code_suggestion',
			'CONCAT(tblother_payslips.prefix, "-", tblother_payslips.code) as code_other_payslips',
			'tblplan_propose.id_other_payslips',
			'(SELECT count(tbltasks.id) FROM tbltasks WHERE rel_id = tblplan_propose.id AND rel_type="plan_propose") as countTask',
			'CONCAT(tblpurchases.prefix, tblpurchases.code) as code_purchases',
			'CONCAT(tbl_services.prefix, tbl_services.code) as code_service',
			'CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as code_purchase_order',
			'CONCAT(tblpurchase_order_laster.prefix,"-", tblpurchase_order_laster.code) as code_purchase_order_laster',
			'tblplan_propose.id_purchases',
			'tblplan_propose.id_service',
			'tblplan_propose.id_purchase_order',
			'tblplan_propose.status',
			'tblplan_propose.reason',
			'tblbranch.name as name_branch',
			'(SELECT CONCAT(tblinternal_proposal.id, "__", tblinternal_proposal.code) FROM tblinternal_proposal WHERE tblinternal_proposal.id = tblplan_propose.id_internal_proposal LIMIT 1) as internal_proposal',
			'tblplan_propose.id_internal_proposal',
			'tblsuppliers.company as name_supplier',
			'tblplan_propose.id_purchase_order_internal as id_purchase_order_internal'
		], 'ORDER BY date desc,id desc', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
		foreach ($rResult as $key => $aRow) {
			$staff_profile_image = staff_profile_image($aRow['staff'], array('staff-profile-image-small mright5'), 'small', array('data-toggle' => 'tooltip', 'data-title' => ' Vào lúc: ' . _dt($aRow['date_create'])));
			$approved_by = '';
			$edit = '';
			$print_pdf = '';
			if ($this->perApprove) {
				if ($aRow['status'] == 0) {
					$html_not_active = '<div class="not_agree">
											<div class="form-group">
												<label class="control-label">Lý do</label>
												<textarea class="form-control reason"></textarea>
											</div>
											<a id="not_agree" data-id="' . $aRow['id'] . '" data-status="2" class="btn btn-icon btn-danger">Lưu</a>
											<a class="btn btn-default btn-icon po-close">Hủy</a>
										</div>';
					$html_not_active = htmlentities($html_not_active);
					// <a data-content='$html_not_active' title='Không duyệt' class='btn btn-danger btn-icon'  data-html='true' data-toggle='popover' data-container='body' data-placement='left' style='cursor: pointer;' class='btn btn-danger btn-icon'>Không Duyệt</a>
					$html = "<p>
                    <a id='agree' value='1' data-id='" . $aRow['id'] . "' class='btn btn-success btn-icon'>Hoàn thành</a>
                    <button class='btn po-close  btn-icon'>Thoát</button></p>";
					$approved_by = '<div class="text-left mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-warning po" data-original-title="Hoàn thành">Chưa hoàn thành</span></div>';
				} else if ($aRow['status'] == 1) {
					$html = "<p>
								<a id='agree' value='0' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Chưa hoàn thành</a>
                				<button class='btn po-close  btn-icon'>Thoát</button>
							</p>";
					$approved_by = '<div class="text-left mbot5">
										<span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-success po" data-original-title="Hoàn thành">Đã hoàn thành</span>
									</div>' . '' . staff_profile_image($aRow['approved_by'], array('staff-profile-image-small mright5'), 'small', array()) . get_staff_full_name($aRow['approved_by']);
				} else if ($aRow['status'] == 2) {
					$html = "<p>
								<a id='agree' value='0' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Bỏ không duyệt</a>
                				<button class='btn po-close  btn-icon'>Thoát</button>
							</p>";
					$approved_by = '<div class="text-left mbot5">
										<span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-danger po" data-original-title="Không Duyệt">Không Duyệt</span>
									</div>' .
						'' . staff_profile_image($aRow['approved_by'], array('staff-profile-image-small mright5'), 'small', array()) . get_staff_full_name($aRow['approved_by']) .
						'<br/>Lý do: ' . $aRow['reason'];
				}
			}
			if ($this->perEdit) {
				$edit = '<a class="c_modal" href="' . admin_url('plan_propose/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('Sửa phiếu') . '</a>';
			}
			$delete = '';
			if (empty($aRow['id_suggestion']) && $this->perDelete) {
				$delete = '<a onclick="deleting(' . $aRow['id'] . '); return false;" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left"><i class="fa fa-remove width-icon-actions"></i> ' . lang('Xóa phiếu') . '</a>';
			}
			$actions = '
				<div class="dropdown text-center">
					<button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">' . lang('actions') . '
					<span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
						<li>' . $edit . '</li>
						<li>' . $print_pdf . '</li>
						<li class="not-outside">' . $delete . '</li>
					</ul>
				</div>';
			$start++;
			$column[0] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
			// $column[0] = '<div class="text-center">' . (++$key) . '</div>';
			$column[1] = _d(substr($aRow['date'], 0, -9));
			$column[2] = '<div><a class="c_modal" href="' . admin_url('plan_propose/view/' . $aRow['id']) . '">' . $aRow['code'] . '</a></div>';
			$column[3] = '';
			$column[4] = '';
			$column[5] = '';
			if (!empty($aRow['name_supplier'])) {
				$column[3] .= '<div style="margin-bottom: 2px;"><i>Nhà cung cấp: ' . $aRow['name_supplier'] . '</i></div>';
			}
			if (!empty($aRow['internal_proposal'])) {
				$internal_proposal = explode('__', $aRow['internal_proposal']);
				$column[4] .= '<p style="margin-bottom: 2px;"><span class="label label-success mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;">Phiếu: <a class="c_modal" href="' . (admin_url('internal_proposal/view/' . $internal_proposal[0])) . '">' . $internal_proposal[1] . '</a></span></p>';
			}
			if (!empty($aRow['id_purchase_order'])) {
				$column[5] .= '<p style="margin-bottom: 2px;"><span class="label label-primary	 mtop5 text-center pointer" style="padding-top: 1px;padding-bottom: 1px;" onclick="view_purchase_order(' . $aRow['id_purchase_order'] . ');">Phiếu mua hàng (PO): ' . $aRow['code_purchase_order'] . '</span></p>';
			}
			if (!empty($aRow['id_purchase_order_internal'])) {
				$column[5] .= '<p style="margin-bottom: 2px;"><span class="label label-primary	 mtop5 text-center pointer" style="padding-top: 1px;padding-bottom: 1px;" onclick="view_purchase_order(' . $aRow['id_purchase_order_internal'] . ');">Phiếu mua hàng (PO): ' . $aRow['code_purchase_order_laster'] . '</span></p>';
			}
			$column[6] = !empty($aRow['type_plan_propose']) ? ('<div>' . $this->type_title_plan_propose[$aRow['type_plan_propose']] . '</div>') : '';
			// suggest_id
			$this->db->like('id', $aRow['category_recommended_id']);
			$object_type = $this->db->get('tbl_category_recommended')->row_array();
			$data_object = '';
			if (!empty($object_type)) {
				// $row[] = $aRow['object_id'];
				$code_Suggest = '';
				$dtSuggest = get_table_where($object_type['name_table'], ['id' => $aRow['suggest_id']], '', 'row_array');
				$supplier_id = '';

				if (!empty($dtSuggest)) {
					if (!empty($dtSuggest['reference_no'])) {
						$code_Suggest = $dtSuggest['reference_no'];
					}
					if (!empty($dtSuggest['code'])) {
						$code_Suggest = $dtSuggest['code'];
					}
					$link = '';
					$name_table = explode('tbl_', $object_type['name_table']);
					if (count($name_table) > 1) {
						$link = $name_table[1];
					} else {
						$name_table_v2 = explode('tbl', $object_type['name_table']);
						if (count($name_table_v2) > 1) {
							$link = $name_table_v2[1];
						}
					}
					$html = '</div><a class="tnh-modal" href="' . base_url('admin/' . $link . '/view/' . $dtSuggest['id']) . '">' . $code_Suggest . '</a>';
					// if ($aRow['type_kpi'] == 1) {
					// 	$html = '<div><a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
					// }
					// $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
					$code_Suggest = $html;
					if ($aRow['category_recommended_id'] == 40) {
						$supplier_id = $dtSuggest['supplier_id'];
					}
					if ($aRow['category_recommended_id'] == 41) {
						$suggest_muti_id = get_table_where('tbl_suggest_muti_id', ['id_internal_proposal' => $aRow['id_internal_proposal']]);
						foreach ($suggest_muti_id as $kk => $vvs) {
							$dtSuggests = get_table_where('tbl_suggest_payslips', ['id' => $vvs['suggest_id']], '', 'row_array');
							$supplier_id = $dtSuggests['suppliers_id'];
							break;
						}
					} elseif ($aRow['category_recommended_id'] == 31) {
						$suggest_plan_outsource_item = get_table_where('tbl_suggest_plan_outsource_item', ['suggest_plan_outsource_id' => $aRow['suggest_id']], '', 'row_array');
						if (!empty($suggest_plan_outsource_item)) {
							$supplier_id = $suggest_plan_outsource_item['supplier_id'];
						}
					} else {
						// $column[3] = $aRow['category_recommended_id'];
					}
					if (!empty($supplier_id)) {
						$supplier = get_table_where('tblsuppliers', ['id' => $supplier_id], '', 'row_array');
						$column[3] = $supplier['company'];
					}
				} else {
					if ($aRow['category_recommended_id'] == 41) {
						$suggest_muti_id = get_table_where('tbl_suggest_muti_id', ['id_internal_proposal' => $aRow['id_internal_proposal']]);
						foreach ($suggest_muti_id as $kk => $vvs) {
							$dtSuggests = get_table_where('tbl_suggest_payslips', ['id' => $vvs['suggest_id']], '', 'row_array');
							if (!empty($dtSuggests)) {
								$supplier_id = $dtSuggests['suppliers_id'];
								$code_Suggest = $dtSuggests['reference_no'];
								break;
							}
						}
					}
					if (!empty($supplier_id)) {
						$supplier = get_table_where('tblsuppliers', ['id' => $supplier_id], '', 'row_array');
						$column[3] = $supplier['company'];
					}
				}
				$data_object = $code_Suggest;
			}
			$column[7] = $data_object;

			$column[8] = !empty($aRow['name_branch']) ? ('<div>' . $aRow['name_branch'] . '</div>') : '';
			$column[9] = $staff_profile_image . get_staff_full_name($aRow['staff']);
			$column[10] = (!empty($aRow['code_category_tasks']) ? ($aRow['code_category_tasks']) : '');
			$this->db->where('id_plan_propose', $aRow['id']);
			$assigned = $this->db->get('tblplan_propose_assigned')->result_array();
			$column[11] = '';
			if (!empty($assigned)) {
				foreach ($assigned as $key => $value) {
					$FullName = get_staff_full_name($value['id_staff']);
					$column[11] .= staff_profile_image($value['id_staff'], array('staff-profile-image-small mright5'), 'small', array('data-toggle' => 'tooltip', 'data-title' => $FullName));
				}
			}
			$column[12] = '<div class="text-right"><b>' . number_format($aRow['money']) . '</b></div>';
			$column[13] = $approved_by;
			$column[14] = '<div class="max-400">' . ($aRow['content']) . '</div>';
			$html = '<div>' . icon_btn('#', 'pencil', 'btn-default', array('onclick' => "add(" . $aRow['id'] . "); return false;"));
			if (true/*!$this->suggestion_type_model->isUsed($aRow['id'])*/) {
				$html .= '<a onclick="delete_suggestion_type(' . $aRow['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
                                    <i class="fa fa-remove"></i>
                                </a></div>';
			}
			if (!has_permission('tasks', '', 'create')) {
				$column[15] = '';
			} else {
				$column[15] = '<a class="btn btn-info btn-icon mbot5 c_modal_tasks" href="' . admin_url('plan_propose/create_tasks/' . $aRow['id']) . '">Tạo công việc</a>';
				if (!empty($aRow['countTask'])) {
					$column[15] .= '<br/><span class="dropdown-toggle no_background label label-info mtop10">Đã tạo ' . $aRow['countTask'] . ' phiếu công việc . </span>';
				}
			}
			$column[16] = $actions;
			$id_tasks = 0;
			$this->db->where('rel_id', $aRow['id']);
			$this->db->where('rel_type', 'plan_propose');
			$this->db->order_by('id', 'DESC');
			$tasks = $this->db->get('tbltasks')->row();
			if (!empty($tasks)) {
				$id_tasks = $tasks->id;
				$this->db->where('taskid', $id_tasks);
				$data_checklist_items = $this->db->get('tbltask_checklist_items')->result_array();
				$htmlCheckList = '<b class="text-danger">Chưa thiết lập quy trình công việc.</b>';
				if (!empty($data_checklist_items)) {
					$htmlCheckList = '';
					$rowCheckList = '';
					foreach ($data_checklist_items as $k => $v) {
						$imgStaff = '';
						if (!empty($v['finished_from'])) {
							$imgStaff = staff_profile_image($v['finished_from'], array('staff-profile-image-small mright5 img_ch'), 'small', array(
								'data-toggle' => 'tooltip',
								'data-title' => get_staff_full_name($v['finished_from'])
							));
						}
						$rowCheckList .= '<li class="pointer ' . ($v['finished'] ? 'active' : '') . '" >
								' . $v['description'] . '
								<p class="active_poin">' . (!empty($v['finished_from']) ? ('Được ' . get_staff_full_name($v['finished_from']) . ' hoàn thành') : '') . '</p>
							</li>';
					}
					$htmlCheckList = '<div class="display: table; justify-content: center;">
								<ul class="progressbar" style="display: flex;">' . $rowCheckList . '</ul>
						 </div>';
					$column[17] = $htmlCheckList;
				} else {
					$column[17] = $htmlCheckList;
				}
			} else {
				$htmlCheckList = '<b class="text-warning">Chưa tạo công việc.</b>';
				$column[18] = $htmlCheckList;
			}
			$output['aaData'][] = $column;
		}
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_list_branch_staff();
			}
		}
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				if (!empty($list_branch)) {
					$this->db->where('(tblplan_propose.id_branch IN (' . $list_branch . '))');
				} else {
					$this->db->where('tblplan_propose.id_branch = 0', false, false);
				}
			}
		}
		if (!$this->perView && $this->perViewOwn) {
			$this->db->where('(
				EXISTS (
								SELECT 1 
								FROM tblplan_propose_assigned 
								WHERE tblplan_propose_assigned.id_plan_propose = tblplan_propose.id
								AND tblplan_propose_assigned.id_staff = "' . get_staff_user_id() . '"
				) 
				OR tblplan_propose.staff = "' . get_staff_user_id() . '"
			)', false, false);
		}
		if (!empty($groups_search)) {
			$this->db->where('tblplan_propose.type_plan_propose', $groups_search);
		}
		$output['total'][1] = $this->db->get_where('tblplan_propose', ['status' => 1])->num_rows();
		$output['total'][1] = !empty($output['total'][1]) ? $output['total'][1] : 0;
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				if (!empty($list_branch)) {
					$this->db->where('(tblplan_propose.id_branch IN (' . $list_branch . '))');
				} else {
					$this->db->where('tblplan_propose.id_branch = 0', false, false);
				}
			}
		}
		if (!$this->perView && $this->perViewOwn) {
			$this->db->where('(
					EXISTS (
								SELECT 1 
								FROM tblplan_propose_assigned 
								WHERE tblplan_propose_assigned.id_plan_propose = tblplan_propose.id
								AND tblplan_propose_assigned.id_staff = "' . get_staff_user_id() . '"
				) OR tblplan_propose.staff = "' . get_staff_user_id() . '"
			)', false, false);
		}
		if (!empty($groups_search)) {
			$this->db->where('tblplan_propose.type_plan_propose', $groups_search);
		}
		$output['total'][2] = $this->db->get_where('tblplan_propose', ['status' => 2])->num_rows();
		$output['total'][2] = !empty($output['total'][2]) ? $output['total'][2] : 0;
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				if (!empty($list_branch)) {
					$this->db->where('(tblplan_propose.id_branch IN (' . $list_branch . '))');
				} else {
					$this->db->where('tblplan_propose.id_branch = 0', false, false);
				}
			}
		}
		if (!$this->perView && $this->perViewOwn) {
			$this->db->where('(
				EXISTS (
						SELECT 1 
						FROM tblplan_propose_assigned 
						WHERE tblplan_propose_assigned.id_plan_propose = tblplan_propose.id
						AND tblplan_propose_assigned.id_staff = "' . get_staff_user_id() . '"
				) 
				OR tblplan_propose.staff = "' . get_staff_user_id() . '" 
			)', false, false);
		}
		if (!empty($groups_search)) {
			$this->db->where('tblplan_propose.type_plan_propose', $groups_search);
		}
		$output['total'][0] = $this->db->get_where('tblplan_propose', ['status' => 0])->num_rows();
		$output['total'][0] = !empty($output['total'][0]) ? $output['total'][0] : 0;
		echo json_encode($output);
	}

	public function detail($id = '')
	{
		$type = $this->input->get('type');
		$data['staff_list'] = $this->getStaffWhere();
		$data['staff_list_all'] = $this->site_model->getStaffAll();
		if (!empty($id)) {
			$data['title'] = _l('edit') . ' ' . _l('c_plan_propose') . ' ' . (!empty($type) ? $this->type_title[$type] : '');
			if (!$this->perEdit) {
				ajax_access_denied();
			}
			// $data['title'] = _l('edit') . ' ' . _l('c_plan_propose') . ' ' . $this->type_title[$type];
			$data['plan_propose'] = get_table_where('tblplan_propose', array('id' => $id), '', 'row');
			$tblplan_propose_assigned = get_table_where('tblplan_propose_assigned', array('id_plan_propose' => $id));
			$staff_assigned = [];
			foreach ($tblplan_propose_assigned as $key => $value) {
				$staff_assigned[] = $value['id_staff'];
			}
			$data['plan_propose']->staff_assigned = $staff_assigned;
		} else {
			$data['title'] = _l('add') . ' ' . _l('c_plan_propose') . ' ' . (!empty($type) ? $this->type_title[$type] : '');
			if (!$this->perAdd) {
				ajax_access_denied();
			}
		}
		// $this->db->group_start();
		// $this->db->like('content', 'đề xuất');
		// $this->db->or_like('code', 'đề xuất');
		// $this->db->or_where('code like "DX%"', false, false);
		// $this->db->or_where('code like "ĐX%"', false, false);
		// $this->db->group_end();
		// $data['category_tasks'] = $this->db->get('tblcategory_tasks')->result_array();
		$arr_id = !empty($data['plan_propose']->category_tasks) ? [$data['plan_propose']->category_tasks] : null;
		$data['category_tasks'] = $this->site_model->getCategoryTasks($arr_id);
		$data['key_departments'] = [];
		$data['departments'] = $this->db->get('tbldepartments')->result_array();
		foreach ($data['departments'] as $key => $value) {
			$data['key_departments'][$value['departmentid']] = $value['name'];
		}
		$data['branch'] = $this->db->get('tblbranch')->result_array();
		$data['type_plan_propose'] = [];
		$data['type_plan_propose'] = $this->type_plan_propose;
		$this->load->view('admin/plan_propose/modal_add', $data);
	}

	public function create_tasks($id = '')
	{
		if (!has_permission('tasks', '', 'create')) {
			echo '<script>
						alert_float("danger", "Tạo không có quyền tạo phiếu công việc");
				</script>';
			die();
		}
		$this->db->where('id', $id);
		$internal_proposal = $this->db->get('plan_propose')->row();
		if ($internal_proposal->type_plan_propose == 'pay_slip' && $internal_proposal->id_internal_proposal > 0) {
			echo '<script>
						alert_float("danger", "Kế hoạch chi liên quan đến đề xuất không thể tạo");
						oTable.draw("page");
					</script>';
			die();
		} else {
			$id_tasks = $this->createTaskAuto($id);
		}
		if (!empty($id_tasks)) {
			echo '<script>
							alert_float("success", "Tạo phiếu công việc thành công");
							oTable.draw("page");
                            init_task_modal(' . $id_tasks . ');
					</script>';
			die();
		} else {
			echo '<script>
						alert_float("danger", "Tạo phiếu công việc không thành công");
						oTable.draw("page");
					</script>';
			die();
		}
	}

	public function createTaskAuto($id = '')
	{
		$this->db->where('id', $id);
		$internal_proposal = $this->db->get('plan_propose')->row();
		$name = '';
		if (!empty($internal_proposal)) {
			if (!empty($internal_proposal->category_tasks)) {
				$this->db->where('id', $internal_proposal->category_tasks);
				$category_tasks = $this->db->get('tblcategory_tasks')->row();
				$staff_department = !empty($category_tasks) ? $category_tasks->departments : NULL;
				$name = !empty($category_tasks) ? $category_tasks->content : NULL;
			}
			$_data = [
				'name' => $name,
				'hourly_rate' => 0,
				'category_tasks' => $internal_proposal->category_tasks,
				'startdate' => $internal_proposal->date,
				'duedate' => NULL,
				'priority' => 2,
				'rel_type' => 'plan_propose',
				'rel_id' => $id,
				'description' => $internal_proposal->content,
				'department_id' => !empty($staff_department) ? explode(',', $staff_department) : [],
				'id_branch' => $internal_proposal->id_branch,
			];
			$id_tasks = $this->tasks_model->add($_data, false, true);
			if (!empty($id_tasks)) {
				$staffNow = get_staff_user_id();
				$this->db->where('id_plan_propose', $internal_proposal->id);
				$this->db->where('id_staff != "' . $staffNow . '"', false, false);
				$internal_assigned = $this->db->get('tblplan_propose_assigned')->result_array();
				if (!empty($internal_assigned)) {
					foreach ($internal_assigned as $key => $value) {
						$this->db->insert('tbltask_followers', [
							'staffid' => $value['id_staff'],
							'taskid' => $id_tasks
						]);
					}
				}
				return $id_tasks;
			}
		}
		return false;
	}

	public function create_suggestion($id = '')
	{
		if (!has_permission('suggestion', '', 'create')) {
			echo '<script>alert_float("danger", "Bạn không có quyền tạo phiếu đề xuất tài chính")</script>';
			die();
		}
		$this->db->where('id', $id);
		$internal_proposal = $this->db->get('plan_propose')->row();
		if (!empty($internal_proposal)) {
			if ($internal_proposal->money == 0) {
				echo '<script>alert_float("danger", "Phiếu kế hoạch không có chi phí không thể tạo phiếu đề xuất tài chính")</script>';
				die();
			}
			if ($internal_proposal->status == 1) {
				if (!empty($internal_proposal->id_suggestion)) {
					echo '<script>alert_float("danger", "Phiếu kế hoạch đã tồn tại phiếu đề xuất tài chính nên không thể tạo!")</script>';
					die();
				}
				$ins = array();
				$ins['date'] = date('Y-m-d H:i:s');
				$ins['code'] = 'DX-' . sprintf('%06d', ch_getMaxID('id', 'tblsuggestion') + 1);
				$ins['type'] = 3;
				$ins['status'] = 2;
				$ins['note'] = '';
				$ins['staffid'] = $internal_proposal->staff;
				$ins['staff_create'] = get_staff_user_id();
				$ins['date_create'] = date('Y-m-d H:i:s');
				$ins['price_total'] = $internal_proposal->money;
				$ins['status_dn'] = 1;
				$ins['staff_status_dn'] = $internal_proposal->staff;
				$ins['date_status_dn'] = date('Y-m-d H:i:s');
				$ins['id_branch'] = $internal_proposal->id_branch;
				$this->db->insert('tblsuggestion', $ins);
				$id_suggestion = $this->db->insert_id();
				if (!empty($id_suggestion)) {
					$this->db->where('id', $id);
					$this->db->update('plan_propose', ['id_suggestion' => $id_suggestion]);
					echo '<script>
								alert_float("success", "Tạo phiếu đề xuất tài chính thành công");
                                oTable.draw("page");
						</script>';
					die();
				}
			} else {
				echo '<script>alert_float("danger", "Phiếu chưa duyệt chưa thể tạo phiếu đề xuất tài chính")</script>';
				die();
			}
		}
		echo '<script>alert_float("danger", "Không tìm thấy phiếu kế hoạch")</script>';
		die();
	}

	public function getStaffWhere()
	{
		$StringWhere = [];
		//		foreach($this->code_departments as $key => $value) {
		//			$StringWhere[] = 'tbldepartments.code = "'.$value.'"';
		//		}
		$staffDepartments = "(
			SELECT
				tblstaff_departments.staffid as staffid,
				GROUP_CONCAT(tbldepartments.name) as name_department 
			FROM tblstaff_departments
			INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
			WHERE tbldepartments.departmentid != 0 " . (!empty($StringWhere) ? ('AND (' . implode(' OR ', $StringWhere) . ')') : '') . "
			GROUP BY tblstaff_departments.staffid
		) tb_staff_departments";
		$this->db->select('
			tblstaff.staffid as staffid,
			tblstaff.firstname as firstname,
			tblstaff.lastname as lastname,
			CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname,
			tblroles.name as name_role,
			tb_staff_departments.name_department as name_department
		', false);
		$this->db->from('tblstaff');
		$this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
		$this->db->join($staffDepartments, 'tb_staff_departments.staffid = tblstaff.staffid');
		return $this->db->get()->result_array();
	}

	public function getCode($id = '')
	{
		if (empty($id)) { // create new code
			$code = 'KH' . '-' . sprintf('%06d', ch_getMaxID('id', 'tblplan_propose') + 1);
		} else { // get existed
			$code = get_table_where('tblplan_propose', ['id' => $id], '', 'row', '', 'code')->code;
		}
		return $code;
	}

	public function add()
	{
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			$id = !empty($data['id']) ? $data['id'] : '';
			unset($data['id']);
			$data['date'] = to_sql_date($data['date']);
			$data['money'] = number_format_data($data['money'], false);
			$data['content'] = $this->input->post('content', false);
			$staff_assigned = $this->input->post('staff_assigned');
			if (empty($data['id_branch'])) {
				echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Vui lòng chọn chi nhánh']);
				die();
			}
			if (empty($data['type_plan_propose'])) {
				echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Vui lòng chọn loại kế hoạch']);
				die();
			}
			if (($data['type_plan_propose'] == 'train')) {
				if (empty($data['type_train'])) {
					echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Vui lòng chọn loại đào tạo']);
					die();
				}
			}
			unset($data['tabset']);
			unset($data['staff_assigned']);
			$train = !empty($data['train']) ? $data['train'] : [];
			unset($data['train']);
			$time = !empty($data['time']) ? $data['time'] : [];
			unset($data['time']);
			if (empty($id)) { //add a new
				if (empty($this->perAdd)) {
					ajax_access_denied();
				}
				$data['code'] = $this->getCode();
				$data['create_by'] = get_staff_user_id();
				$this->db->insert('tblplan_propose', $data);
				$id = $this->db->insert_id();
				if (!empty($id)) {
					$data_train = [];
					foreach ($train as $key => $value) {
						$data_train[$key] = $value;
						$data_train[$key]['id_plan_propose'] = $id;
						$data_train[$key]['items_id'] = (!empty($value['items_id']) ? ($value['items_id']) : NULL);
						$data_train[$key]['items_replace_id'] = (!empty($value['items_replace_id']) ? ($value['items_replace_id']) : NULL);
						$data_train[$key]['costs'] = (!empty($value['costs']) ? ($value['costs']) : NULL);
						$data_train[$key]['quantity'] = (!empty($value['quantity']) ? number_unformat($value['quantity']) : 0);
						$data_train[$key]['price'] = (!empty($value['price']) ? number_unformat($value['price']) : 0);
						$data_train[$key]['date_finish'] = (!empty($value['date_finish']) ? to_sql_date($value['date_finish']) : NULL);
						$data_train[$key]['date_from'] = (!empty($value['date_from']) ? to_sql_date($value['date_from']) : NULL);
						$data_train[$key]['date_to'] = (!empty($value['date_to']) ? to_sql_date($value['date_to']) : NULL);
						$data_train[$key]['date_warehouse'] = (!empty($value['date_warehouse']) ? to_sql_date($value['date_warehouse']) : NULL);
						$data_train[$key]['workunit'] = (!empty($value['workunit']) ? ($value['workunit']) : NULL);
						$data_train[$key]['standardpass'] = (!empty($value['standardpass']) ? ($value['standardpass']) : NULL);
					}
					$data_time = [];
					foreach ($time as $key => $value) {
						$data_time[$key] = $value;
						$data_time[$key]['id_plan_propose'] = $id;
						$data_time[$key]['items_id_time'] = $value['items_id_time'];
						$data_time[$key]['staff'] = $value['staff'];
						$data_time[$key]['timestart'] = $value['timestart'];
						$data_time[$key]['timeend'] = ($value['timeend']);
						$data_time[$key]['alltime'] = ($value['alltime']);
						$data_time[$key]['allplan'] = $value['allplan'];
						$data_time[$key]['evaluate'] = $value['evaluate'];
						$data_time[$key]['exceededthequota'] = $value['exceededthequota'];
						$data_time[$key]['underperformingthenorm'] = $value['underperformingthenorm'];
						$data_time[$key]['handoverdesk'] = $value['handoverdesk'];
						$data_time[$key]['warranty'] = $value['warranty'];
						$data_time[$key]['sign'] = $value['sign'];
					}
					if (!empty($data_time)) {
						$this->plan_propose_model->insertBatchtime($data_time);
					}
					if (!empty($data_train)) {
						$this->plan_propose_model->insertBatchtrain($data_train);
					}
					$success = true;
					$message = _l('ch_added_successfuly');
					if (!empty($staff_assigned)) {
						$dataHtml = '
								<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
								Hệ thống - ' . get_staff_full_name() . ' Vừa thêm bạn vào người duyệt phiếu kế hoạch ' . $data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
							';
						foreach ($staff_assigned as $key => $value) {
							$this->db->insert('tblplan_propose_assigned', [
								'id_plan_propose' => $id,
								'id_staff' => $value,
							]);
							$notification_data = [
								'date' => date('Y-m-d H:i:s'),
								'description' => $dataHtml,
								'touserid' => $value,
								'link' => 'plan_propose/view/' . $id,
								'type' => 13,
								'object_id' => $id,
								'object_type' => 'plan_propose',
							];
							if (!empty($notification_data)) {
								$this->db->insert('tblnotifications', $notification_data);
								pusher_trigger_notification($notification_data);
							}
							send_notification_app_c($id, [
								'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa thêm bạn vào người duyệt phiếu kế hoạch ' . $data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
								'title' => 'Phiếu kế hoạch',
								'code' => $data['code'],
								'object_type' => 'plan_propose'
							], [$value], get_staff_user_id());
						}
					}
				} else {
					$success = false;
					$message = _l('ch_added_successfuly_not');
				}
			} else {
				if (empty($this->perEdit)) {
					ajax_access_denied();
				}
				$this->db->where('id', $id);
				$plan_propose = $this->db->get('tblplan_propose')->row();
				$this->db->where('id', $id);
				$success_update = $this->db->update('tblplan_propose', $data);
				if ($success_update) {
					$this->db->where('id_plan_propose', $id);
					$this->db->delete('tblplan_propose_train');
					$this->db->where('id_plan_propose', $id);
					$this->db->delete('tblplan_propose_time');
					$data_train = [];
					foreach ($train as $key => $value) {
						$data_train[$key] = $value;
						$data_train[$key]['id_plan_propose'] = $id;
						$data_train[$key]['items_id'] = (!empty($value['items_id']) ? ($value['items_id']) : NULL);
						$data_train[$key]['items_replace_id'] = (!empty($value['items_replace_id']) ? ($value['items_replace_id']) : NULL);
						$data_train[$key]['costs'] = (!empty($value['costs']) ? ($value['costs']) : NULL);
						$data_train[$key]['quantity'] = (!empty($value['quantity']) ? number_unformat($value['quantity']) : 0);
						$data_train[$key]['price'] = (!empty($value['price']) ? number_unformat($value['price']) : 0);
						$data_train[$key]['date_finish'] = (!empty($value['date_finish']) ? to_sql_date($value['date_finish']) : NULL);
						$data_train[$key]['date_from'] = (!empty($value['date_from']) ? to_sql_date($value['date_from']) : NULL);
						$data_train[$key]['date_to'] = (!empty($value['date_to']) ? to_sql_date($value['date_to']) : NULL);
						$data_train[$key]['date_warehouse'] = (!empty($value['date_warehouse']) ? to_sql_date($value['date_warehouse']) : NULL);
						$data_train[$key]['workunit'] = (!empty($value['workunit']) ? ($value['workunit']) : NULL);
						$data_train[$key]['standardpass'] = (!empty($value['standardpass']) ? ($value['standardpass']) : NULL);
					}
					$data_time = [];
					foreach ($time as $key => $value) {
						$data_time[$key] = $value;
						$data_time[$key]['id_plan_propose'] = $id;
						$data_time[$key]['items_id_time'] = $value['items_id_time'];
						$data_time[$key]['staff'] = $value['staff'];
						$data_time[$key]['timestart'] = $value['timestart'];
						$data_time[$key]['timeend'] = ($value['timeend']);
						$data_time[$key]['alltime'] = ($value['alltime']);
						$data_time[$key]['allplan'] = $value['allplan'];
						$data_time[$key]['evaluate'] = $value['evaluate'];
						$data_time[$key]['exceededthequota'] = $value['exceededthequota'];
						$data_time[$key]['underperformingthenorm'] = $value['underperformingthenorm'];
						$data_time[$key]['handoverdesk'] = $value['handoverdesk'];
						$data_time[$key]['warranty'] = $value['warranty'];
						$data_time[$key]['sign'] = $value['sign'];
					}
					if (!empty($data_time)) {
						$this->plan_propose_model->insertBatchtime($data_time);
					}
					if (!empty($data_train)) {
						$this->plan_propose_model->insertBatchtrain($data_train);
					}
					$success = true;
					$message = _l('ch_updated_successfuly');
					$this->db->where('id_plan_propose', $id);
					$list_assigned = $this->db->get('tblplan_propose_assigned')->result_array();
					$arrayList = [];
					if (!empty($list_assigned)) {
						foreach ($list_assigned as $key => $value) {
							$arrayList[$value['id_staff']] = true;
						}
					}
					$this->db->where('id_plan_propose', $id);
					$this->db->delete('tblplan_propose_assigned');
					if (!empty($staff_assigned)) {
						$dataHtml = '
								<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
								Hệ thống - ' . get_staff_full_name() . ' Vừa thêm bạn vào người duyệt phiếu kế hoạch ' . $plan_propose->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
							';
						$arraySendEmail = [];
						foreach ($staff_assigned as $key => $value) {
							$this->db->insert('tblplan_propose_assigned', [
								'id_plan_propose' => $id,
								'id_staff' => $value,
							]);
							if (empty($arrayList[$value])) {
								$arraySendEmail[] = $value;
								$notification_data = [
									'date' => date('Y-m-d H:i:s'),
									'description' => $dataHtml,
									'touserid' => $value,
									'link' => 'plan_propose/view/' . $id,
									'type' => 13,
									'object_id' => $id,
									'object_type' => 'plan_propose',
								];
								if (!empty($notification_data)) {
									$this->db->insert('tblnotifications', $notification_data);
									pusher_trigger_notification($notification_data);
								}
								send_notification_app_c($id, [
									'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa thêm bạn vào người duyệt phiếu kế hoạch ' . $plan_propose->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
									'title' => 'Theo dõi phiếu kế hoạch',
									'code' => $plan_propose->code,
									'object_type' => 'plan_propose'
								], [$value], get_staff_user_id());
							}
						}
					}
				} else {
					$success = false;
					$message = _l('Sửa không thành công');
				}
			}
			if (!empty($success) && !empty($id)) {
				$this->load->library('upload');
				if (isset($_FILES['file']['name']) && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)) {
					if (!is_array($_FILES['file']['name'])) {
						$_FILES['file']['name'] = [$_FILES['file']['name']];
						$_FILES['file']['type'] = [$_FILES['file']['type']];
						$_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
						$_FILES['file']['error'] = [$_FILES['file']['error']];
						$_FILES['file']['size'] = [$_FILES['file']['size']];
					}
					$path = 'uploads/plan_propose/' . $id . '/';
					if (!file_exists(FCPATH . 'uploads/plan_propose/')) {
						mkdir(FCPATH . 'uploads/plan_propose/');
						fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
					}
					if (!file_exists(FCPATH . 'uploads/plan_propose/' . $id)) {
						mkdir(FCPATH . 'uploads/plan_propose/' . $id);
						fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
					}
					for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
						$tmpFilePath = $_FILES['file']['tmp_name'][$i];
						if (!empty($tmpFilePath) && $tmpFilePath != '') {
							$filename = vn_to_str(unique_filename($path, $_FILES['file']['name'][$i]));
							if (!_upload_extension_allowed($filename)) {
								continue;
							}
							$newFilePath = $path . $filename;
							if (move_uploaded_file($tmpFilePath, $newFilePath)) {
								$typeFile = $_FILES['file']['type'][$i];
								if (file_exists($newFilePath)) {
									$this->db->insert('tblfiles', [
										'rel_id' => $id,
										'rel_type' => 'plan_propose',
										'file_name' => $filename,
										'filetype' => $typeFile,
										'staffid' => get_staff_user_id(),
										'dateadded' => date('Y-m-d H:i:s'),
									]);
								}
							}
						}
					}
				}
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	public function delete($id = '')
	{
		if (empty($this->perDelete)) {
			ajax_access_denied();
		}
		if ($this->plan_propose_model->isApproved($id)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Đề xuất này đã được duyệt. Không thể xóa!'
			));
			die;
		}
		$this->db->where('rel_id', $id);
		$this->db->where('rel_type', 'plan_propose');
		$tasks = $this->db->get('tbltasks')->row();
		if (!empty($tasks)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Đang tồn tại phiếu công việc không thể xóa'
			));
			die;
		}
		$isSuccess = $this->db->delete('plan_propose', array('id' => $id));
		if ($isSuccess) {
			$success = true;
			$message = _l('ch_delete_successfuly');
		} else {
			$success = false;
			$message = _l('ch_delete_successfuly_no');
		}
		echo json_encode(array(
			'success' => $success,
			'message' => $message
		));
		die;
	}

	public function approve($id)
	{
		if (!$this->perApprove) {
			ajax_access_denied();
		}
		$id_tasks = '';
		$this->db->where('id', $id);
		$internal_proposal = $this->db->get('plan_propose')->row();
		if (!$this->plan_propose_model->isApproved($id)) {
			$staff = get_staff_user_id();
			$data = [
				'approved_by' => $staff,
				'status' => 1,
				'reason' => NULL,
			];
			$this->db->where('id', $id);
			$success = $this->db->update('plan_propose', $data);
			if ($success) {
				if ($internal_proposal->type_plan_propose == 'pay_slip' && $internal_proposal->id_internal_proposal > 0) {
				} else {
					$id_tasks = $this->createTaskAuto($id);
				}
				$success = true;
				$message = _l('Hoàn thành phiếu thành công');
				$staff_assigned = $this->db->get_where('tblplan_propose_assigned', ['id_plan_propose' => $id])->result_array();
				if (!empty($staff_assigned)) {
					$dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa hoàn thành phiếu kế hoạch ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';
					foreach ($staff_assigned as $key => $value) {
						$notification_data = [
							'date' => date('Y-m-d H:i:s'),
							'description' => $dataHtml,
							'touserid' => $value['id_staff'],
							'link' => 'plan_propose/view/' . $id,
							'type' => 13,
							'object_id' => $id,
							'object_type' => 'plan_propose',
						];
						if (!empty($notification_data)) {
							$this->db->insert('tblnotifications', $notification_data);
							pusher_trigger_notification($notification_data);
						}
						send_notification_app_c($id, [
							'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa hoàn thành phiếu kế hoạch ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
							'title' => 'Đổi trạng thái phiếu kế hoạch',
							'code' => $internal_proposal->code,
							'object_type' => 'plan_propose'
						], [$value['id_staff']], get_staff_user_id());
					}
				}
			} else {
				$success = false;
				$message = _l('Hoàn thành phiếu thất bại');
			}
		} else {
			$data = [
				'approved_by' => 0,
				'status' => 0,
				'reason' => NULL,
			];
			$this->db->where('id', $id);
			$success = $this->db->update('plan_propose', $data);
			if ($success) {
				$success = true;
				$message = _l('Hủy hoàn thành phiếu thành công');
				$contentStatus = '';
				if ($internal_proposal->status == 2) {
					$contentStatus = ' không';
				}
				$staff_assigned = $this->db->get_where('tblplan_propose_assigned', ['id_plan_propose' => $id])->result_array();
				if (!empty($staff_assigned)) {
					$dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa hủy' . $contentStatus . ' hoàn thành phiếu kế hoạch ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';
					foreach ($staff_assigned as $key => $value) {
						$notification_data = [
							'date' => date('Y-m-d H:i:s'),
							'description' => $dataHtml,
							'touserid' => $value['id_staff'],
							'link' => 'internal_proposal/view/' . $id,
							'type' => 13,
							'object_id' => $id,
							'object_type' => 'internal_proposal',
						];
						if (!empty($notification_data)) {
							$this->db->insert('tblnotifications', $notification_data);
							pusher_trigger_notification($notification_data);
						}
						send_notification_app_c($id, [
							'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa hủy' . $contentStatus . ' hoàn thành phiếu kế hoạch ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
							'title' => 'Đổi trạng thái phiếu kế hoạch',
							'code' => $internal_proposal->code,
							'object_type' => 'internal_proposal'
						], [$value['id_staff']], get_staff_user_id());
					}
				}
			} else {
				$success = false;
				$message = _l('Hủy hoàn thành phiếu thành công');
			}
		}
		echo json_encode(array(
			'id_task' => $id_tasks,
			'success' => $success,
			'message' => $message
		));
		die;
	}

	public function not_approve()
	{
		if (!$this->perApprove) {
			ajax_access_denied();
		}
		$success = false;
		$message = _l('c_not_approved_fail');
		$id = $this->input->post('id');
		$this->db->where('id', $id);
		$internal_proposal = $this->db->get('plan_propose')->row();
		if (!$this->plan_propose_model->isApproved($id)) {
			$staff = get_staff_user_id();
			$reason = $this->input->post('reason');
			$data = [
				'approved_by' => $staff,
				'status' => 2,
				'reason' => $reason,
			];
			$this->db->where('id', $id);
			$success = $this->db->update('plan_propose', $data);
			if ($success) {
				$success = true;
				$message = _l('c_not_approved_success');
				$staff_assigned = $this->db->get_where('tblplan_propose_assigned', ['id_plan_propose' => $id])->result_array();
				if (!empty($staff_assigned)) {
					$dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa không duyệt phiếu kế hoạch ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';
					foreach ($staff_assigned as $key => $value) {
						$notification_data = [
							'date' => date('Y-m-d H:i:s'),
							'description' => $dataHtml,
							'touserid' => $value['id_staff'],
							'link' => 'internal_proposal/view/' . $id,
							'type' => 13,
							'object_id' => $id,
							'object_type' => 'internal_proposal',
						];
						if (!empty($notification_data)) {
							$this->db->insert('tblnotifications', $notification_data);
							pusher_trigger_notification($notification_data);
						}
						send_notification_app_c($id, [
							'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa không duyệt phiếu kế hoạch ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
							'title' => 'Đổi trạng thái phiếu kế hoạch',
							'code' => $internal_proposal->code,
							'object_type' => 'internal_proposal'
						], [$value['id_staff']], get_staff_user_id());
					}
				}
			}
		}
		echo json_encode(array(
			'success' => $success,
			'message' => $message
		));
		die;
	}

	public function view($id = '')
	{
		if (!empty($id)) {
			if (!empty($this->is_branch)) {
				if (!is_admin()) {
					$list_branch = get_array_branch_staff();
					if (!empty($list_branch)) {
						$this->db->group_start();
						$this->db->where_in('plan_propose.id_branch', $list_branch);
						$this->db->group_end();
						$this->db->where('id', $id);
						$ktData = $this->db->get('plan_propose')->row();
					} else {
						$ktData = false;
					}
					if (empty($ktData)) {
						accessDenied($js = true);
					}
				}
			}
			$this->db->select(
				'tblplan_propose.*,
				tblcategory_tasks.code as code_category, 
				tblcategory_tasks.content as content_category
				'
			);
			$this->db->where('tblplan_propose.id', $id);
			$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblplan_propose.category_tasks', 'left');
			$data['plan_propose'] = $this->db->get('tblplan_propose')->row();
			$data['type_plan_propose'] = '';
			$data['type_plan_propose'] = $this->type_title_plan_propose[$data['plan_propose']->type_plan_propose];
			$this->db->select(
				'tblplan_propose_train.*'
			);
			$this->db->where('tblplan_propose_train.id_plan_propose', $id);
			$data['train'] = $this->db->get('tblplan_propose_train')->result_array();
			$this->db->select(
				'tblplan_propose_time.*'
			);
			$this->db->where('tblplan_propose_time.id_plan_propose', $id);
			$data['time'] = $this->db->get('tblplan_propose_time')->result_array();
			// if (!$this->perView && $this->perViewOwn) {
			// 	if (!empty($data['internal_proposal'])) {
			// 		$this->db->where('plan_propose.id', $id);
			// 		$this->db->group_start();
			// 		$this->db->where('EXISTS (
			// 					SELECT 1
			// 					FROM plan_propose_assigned
			// 					WHERE plan_propose_assigned.id_plan_propose = plan_propose.id
			// 					AND (plan_propose_assigned.id_staff = "' . get_staff_user_id() . '")
			// 				)', false, false);
			// 		$this->db->or_where('plan_propose.staff', get_staff_user_id());
			// 		$this->db->group_end();
			// 		$internal_proposal = $this->db->get('plan_propose')->row();
			// 		if (empty($internal_proposal)) {
			// 			echo '<script>alert_float("danger", "Bạn không có quyền truy cập")</script>';
			// 			die();
			// 		}
			// 	}
			// }
			// $this->db->where('id_plan_propose', $id);
			// $data['internal_proposal']->assigned = $this->db->get('tblplan_propose_assigned')->result_array();
			$this->db->where('rel_type', 'plan_propose');
			$this->db->where('rel_id', $id);
			$data['files'] = $this->db->get('tblfiles')->result();
		}
		$data['title'] = 'Xem phiếu kế hoạch';
		if (empty($data['plan_propose'])) {
			echo '<script>alert_float("danger", "Không tìm thấy phiếu kế hoạch")</script>';
			die();
		}
		$this->load->view('admin/plan_propose/view', $data);
	}

	public function removeFile($id = '')
	{
		$this->db->where('id', $id);
		$this->db->where('rel_type', 'plan_propose');
		$get_file_delete = $this->db->get('tblfiles')->row();
		if (!empty($get_file_delete)) {
			$linkFile = FCPATH . 'uploads/internal_proposal/' . $get_file_delete->rel_id . '/' . $get_file_delete->file_name;
			if (!empty($linkFile)) {
				unlink($linkFile);
			}
			$this->db->where('id', $id);
			$this->db->where('rel_type', 'plan_propose');
			$this->db->delete('tblfiles');
			echo json_encode([
				'success' => true
			]);
			die();
		}
		echo json_encode([
			'success' => false
		]);
		die();
	}

	public function print_pdf($id = '')
	{
		if (!$this->perPdf) {
			access_denied();
		}
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tbl_deliveries.id_branch', $list_branch);
					$this->db->group_end();
					$this->db->where('id', $id);
					$ktData = $this->db->get('tbl_deliveries')->row();
				} else {
					$ktData = false;
				}
				if (empty($ktData)) {
					access_denied();
				}
			}
		}
		ob_start();
		$data = new stdClass();
		$data->title = lang('internal_proposal');
		$this->db->select(
			'plan_propose.*,
				CONCAT(tblpurchases.prefix, tblpurchases.code) as code_purchase,
				CONCAT(tbl_services.prefix, tbl_services.code) as code_services,
				CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as code_purchase_order'
		);
		$this->db->join('tblpurchases', 'tblpurchases.id = plan_propose.id_purchases', 'left');
		$this->db->join('tbl_services', 'tbl_services.id = plan_propose.id_service', 'left');
		$this->db->join('tblpurchase_order', 'tblpurchase_order.id = plan_propose.id_purchase_order', 'left');
		$dataMain = $this->db->get_where('plan_propose', array('plan_propose.id' => $id))->row();
		$table = '';
		$data->content = '';
		$data->content .= '<span style="text-align: right; font-style: italic;">Ngày chứng từ: ' . _dhau($dataMain->date) . '</span><br><br>';
		$data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">PHIẾU KẾ HOẠCH</span><br><br>';
		$category_tasks = $this->db->get_where('tblcategory_tasks', ['id' => $dataMain->category_tasks])->row();
		//        $status = $this->plan_propose_model->approvedBy($id);
		if (!empty($dataMain->status)) {
			$staff = get_table_where('tblstaff', array('staffid' => $dataMain->approved_by), '', 'row', '');
			if ($dataMain->status == 1) {
				$approved_by = '<tr>
								<td>
									<span style="text-align: left; font-weight: bold">Duyệt:</span>
								</td>
								<td>
									<span>' . $staff->firstname . '&nbsp;' . $staff->lastname . '</span>
								</td>
							</tr>';
			} else if ($dataMain->status == 2) {
				$approved_by = '<tr>
									<td>
										<span style="text-align: left; font-weight: bold">Không Duyệt:</span>
									</td>
									<td>
										<span>' . $staff->firstname . '&nbsp;' . $staff->lastname . '</span>
									</td>
								</tr>
								<tr>
									<td>
										<span style="text-align: left; font-weight: bold">Lý do không duyệt:</span>
									</td>
									<td>
										<span>' . $dataMain->reason . '</span>
									</td>
								</tr>';
			}
		} else {
			$approved_by = '<tr>
								<td>
									<span style="text-align: left; font-weight: bold">Duyệt:</span>
								</td>
								<td>
									<span>Chưa Duyệt</span>
								</td>
							</tr>';
		}
		$htmlType_object = '';
		if (!empty($dataMain->type_object)) {
			$htmlType_object .= '<tr>
								<td>
									<span style="text-align: left; font-weight: bold">Liên quan đến:</span>
								</td>
								<td>
									<span>' . $this->type_object[$dataMain->type_object] . '</span>
								</td>
							</tr>';
		}
		if (!empty($dataMain->code_purchase)) {
			$htmlType_object .= '<tr>
								<td>
									<span style="text-align: left; font-weight: bold">Phiếu yêu cầu mua hàng:</span>
								</td>
								<td>
									<span>' . $dataMain->code_purchase . '</span>
								</td>
							</tr>';
		}
		if (!empty($dataMain->code_purchase_order)) {
			$htmlType_object .= '<tr>
								<td>
									<span style="text-align: left; font-weight: bold">Phiếu mua hàng (PO):</span>
								</td>
								<td>
									<span>' . $dataMain->code_purchase_order . '</span>
								</td>
							</tr>';
		}
		if (!empty($dataMain->code_services)) {
			$htmlType_object .= '<tr>
								<td>
									<span style="text-align: left; font-weight: bold">Phiếu dịch vụ:</span>
								</td>
								<td>
									<span>' . $dataMain->code_services . '</span>
								</td>
							</tr>';
		}
		$table = '
            <table class="table" border="0" width="100%">
                <tbody>
                    <tr>
                        <td width="28%"><span style="font-weight: bold" >Mã đề xuất:</span></td>
                        <td width="72%">
                            <span>' . $dataMain->code . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td><span style="font-weight: bold" >Mã công việc:</span></td>
                        <td>
                            <span>' . $category_tasks->code . '</span>
                        </td>
                    </tr>' .
			$approved_by . $htmlType_object . '
                </tbody>
            </table>';
		$data->content .= $table;
		$data->content .= '<br><br><span style="font-size: 15px;font-weight: bold;">I. Phần của người đề xuất:</span><br>';
		$staff = get_table_where('tblstaff', array('staffid' => $dataMain->staff), '', 'row', '');
		$table = '
            <table class="table" border="0" width="100%">
                <thead></thead>
                <tbody>
                    <tr>
                        <td><span style="font-weight: bold">Họ và tên người đề xuất: </span><span>' . $staff->firstname . '&nbsp;' . $staff->lastname . '</span></td>
                        <td><span style="font-weight: bold">MSNV: </span><span>' . $staff->code . '</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style="font-weight: bold">Nội dung đề xuất: </span><span>' . $dataMain->content . '</span></td>
                    </tr>';
		if (!empty($dataMain->money)) {
			$table .= '<tr>
                            <td><span style="font-weight: bold">Số tiền đề xuất: </span><span>' . formatNumber($dataMain->money) . '</span></td>
                            <td></td>
                        </tr>';
		}
		$table .= '</tbody>
            </table>';
		$data->content .= $table;
		if (!empty($dataMain->money)) {
			$data->content .= '<span style="font-style: italic;">&nbsp;&nbsp;(Bằng chữ: ' . ucfirst(convert_number_to_words($dataMain->money)) . ' đồng)</span><br>';
		}
		$data->content .= '<br><br><span style="font-size: 15px;font-weight: bold;">II. Phần của BP thu mua:</span><br><br>';
		$table = '<table class="table" border="0" width="100%">
            <thead></thead>
            <tbody>
                <tr>
                    <td width="25px" ><img style="float:left;" src="' . base_url('/uploads/icon_hau/empty_checkbox.png') . '"></td>
                    <td width="98%">Xác nhận đơn giá - thành tiền: ..................................................................................................................................</td>
                </tr>
                <tr>
                    <td width="25px" ><img style="float:left;" src="' . base_url('/uploads/icon_hau/empty_checkbox.png') . '"></td>
                    <td width="98%">Xác nhận nhà cung cấp: ...........................................................................................................................................</td>
                </tr>
            </tbody>
        </table>';
		$data->content .= $table;
		$data->content .= '<br><br><span style="font-size: 15px;font-weight: bold;">III. Phần của BP Kế toán:</span><br><br>';
		$data->content .= '<span>Người nhận tiền: ..............................................................................................................................................................</span><br><br>';
		$data->content .= '<span>Ngày ký nhận: ................................................................... Ký nhận: ...............................................................................</span><br><br>';
		$table = '<table class="table table-bordered" width="100%">
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Đề xuất</span>' .
			// <span style="font-weight: bold;">' . $staff->firstname . '&nbsp;' . $staff->lastname . '</span>
			'</td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">BP Thu mua</span><br>
                            <span></span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">BP Kế toán</span><br>
                            <span></span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Xác nhận</span><br>
                            <span style="font-style: italic;">GĐNM/Trưởng BP</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Duyệt</span><br>
                            <span style="font-style: italic;">Ban giám đốc</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
		$data->content .= $table;
		$pdf = print_pdf($data);
		$type = 'I';
		$pdf->Output(slug_it('Phieu_de_xuat_tai_chinh') . '.pdf', $type);
	}

	public function SendEmailNoti($id_staff = [], $id_plan_propose = '')
	{
		return true; // tạm thời đóng do ở local
		if (!empty($id_staff) && !empty($id_plan_propose)) {
			$this->db->where_in('staffid', $id_staff);
			$this->db->where('active', 1);
			$staff = $this->db->get('tblstaff')->result_array();
			if (!empty($staff)) {
				$this->db->where('id', $id_plan_propose);
				$internal_proposal = $this->db->get('plan_propose')->row();
				$list_staff = [];
				foreach ($staff as $key => $value) {
					if (!empty($value['email'])) {
						$list_staff[] = $value['email'];
					}
				}
				$this->load->config('email');
				$template = new StdClass();
				$template->message = get_option('email_header') . '<br/> ' . get_staff_full_name() . ' Vừa thêm bạn vào người duyệt phiếu kế hoạch ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . '<br/>
				 <b>Số tiền:</b> ' . (!empty($internal_proposal->money) ? number_format_data($internal_proposal->money) : '0') . '<br/>
				 <b>Nội dung:</b> ' . $internal_proposal->content . '<br/>
				 Vui lòng theo dõi và tiến hành cập nhật!<br/>';
				$template->fromname = get_option('companyname') != '' ? get_option('companyname') : '';
				$template->subject = 'PHIẾU KẾ HOẠCH';
				$this->email->initialize();
				if (get_option('mail_engine') == 'phpmailer') {
					$this->email->set_debug_output(function ($err) {
						//						if (!isset($GLOBALS['debug'])) {
						//							$GLOBALS['debug'] = '';
						//						}
						//						$GLOBALS['debug'] .= $err . '<br />';
						//						return $err;
						return false;
					});
					//					$this->email->set_smtp_debug(3);
				}
				$this->email->set_newline(config_item('newline'));
				$this->email->set_crlf(config_item('crlf'));
				$this->email->from(get_option('smtp_email'), $template->fromname);
				$this->email->to($list_staff);
				$systemBCC = get_option('bcc_emails');
				if ($systemBCC != '') {
					$this->email->bcc($systemBCC);
				}
				$this->email->subject($template->subject);
				$this->email->message($template->message);
				if ($this->email->send(true)) {
					return true;
				} else {
					//					set_debug_alert('<h1>Your SMTP settings are not set correctly here is the debug log.</h1><br />' . $this->email->print_debugger() . (isset($GLOBALS['debug']) ? $GLOBALS['debug'] : ''));
					//					hooks()->do_action('smtp_test_email_failed');
				}
			}
		}
		return false;
	}

	public function get_plan_propose()
	{
		$type_plan_propose = $this->input->get('type_plan_propose');
		$type_plan_propose_old = $this->input->get('type_plan_propose_old');
		$id = $this->input->get('id');
		$data = [];
		$data['type_plan_propose'] = $type_plan_propose;
		$data['type_plan_propose_old'] = $type_plan_propose_old;
		$tbodytrain = '';
		$counter = 0;
		$data['edit_train'] = 0;
		if (!empty($id)) {
			$data['edit_train'] = 1;
		}
		if (!empty($id)) {
			$train = get_table_where('tblplan_propose_train', array('id_plan_propose' => $id));
			$tbodytrain = '';
			$counter = 0;
			foreach ($train as $key => $value) {
				$tdNumber = '<div class="td-number text-center"></div>';
				$tdSupplies = '<div class="td-code mbot10"><input type="hidden" id="counter" class="form-control counter" value="' .
					$counter . '"><input type="text" name="train[' . $counter . '][items_id]"  data-id="' . ($value['items_id']) . '" id="items_' . $counter . '" class="items_id" style="width: 100%;" data-placeholder="' . _l('choose') . '" value=""></div>
						<div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row" onclick="removeRow(this)">' . _l('delete') . '</a></div></div>';
				$tdObject = '<div class="td-code mbot10">
					<input type="text" name="train[' . $counter . '][object]" id="object_' . $counter . '" class="object" data-id="' . ($value['object']) . '" style="width: 100%;" data-placeholder="' . _l('choose') . '" value=""></div><div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row" onclick="removeRow(this)">' . _l('delete') . '</a></div></div>';
				$tdCost = '<div class="text-center mbot10">
						<select data-id="' . ($value['costs']) . '" name="train[' . $counter . '][costs]" style="width: 100%;" data-placeholder="Danh mục"  id="costs_' . $counter . '" class="costs modal-select2">
						</select>
						';
				$tdUnit = '<div class="text-center mbot10">
						<select data-id="' . ($value['units']) . '" name="train[' . $counter . '][units]" style="width: 100%;" data-placeholder="Danh mục"  id="units_' . $counter . '" class="units modal-select2">
						</select>
						';
				$tdUnit_cost = '<div class="text-center mbot10">
						<select data-id="' . ($value['units_cost']) . '" name="train[' . $counter . '][units_cost]" style="width: 100%;" data-placeholder="Danh mục"  id="units_cost_' . $counter . '" class="units_cost modal-select2">
						</select>
						';
				$tdSuppliesReplace = '<div class="td-code mbot10"><input type="hidden" id="counter" class="form-control counter" value="' . $counter . '">
						<input type="text" name="train[' . $counter . '][items_replace_id]"  data-id="' . ($value['items_replace_id']) . '" id="items_replace_' . $counter . '" class="items_replace_id" style="width: 100%;" data-placeholder="' . _l('choose') . '" value=""></div>';
				$tdQuantity = '<div class="td-quantity"><input onchange="totalTrain()" type="text" name="train[' . $counter . '][quantity]" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="' . number_format_data($value['quantity']) . '"></div>';
				$tdPrice = '<div class="td-price"><input onchange="totalTrain()" type="text" name="train[' . $counter . '][price]" id="price[]" class="text-right form-control price number-format" style="width: 100%;" value="' . number_format_data($value['price']) . '"></div>';
				$tdAmount = '<div class="amount text-right">' . number_format_data($value['quantity'] * $value['price']) . '</div>';
				$tdDateFinish = '<div class="td-date-from">
					<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' . $counter . '][date_finish]" id="input" class="form-control datepicker date_finish" autocomplete="off" placeholder="' . _l('date') . '" value="' . _d($value['date_finish']) . '" style="width: 100%;" title=""></div>';
				$tdDateFrom = '<div class="td-date-from">
					<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' . $counter . '][date_from]" id="input" class="form-control datepicker date_from" autocomplete="off" placeholder="' . _l('date') . '" value="' . _d($value['date_from']) . '" style="width: 100%;" title=""></div>';
				$tdDateTo = '<div class="td-date-to">
					<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' . $counter . '][date_to]" id="input" class="form-control datepicker date_to" autocomplete="off" placeholder="' . _l('date') . '" value="' . _d($value['date_to']) . '" style="width: 100%;" title=""></div>';
				$tdDateWarehouse = '<div class="td-date-to">
					<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="train[' . $counter . '][date_warehouse]" id="input" class="form-control datepicker date_warehouse" autocomplete="off" placeholder="' . _l('date') . '" value="' . _d($value['date_warehouse']) . '" style="width: 100%;" title=""></div>';
				$tdWorkUnit = '<div class="td-workunit"><input type="text" name="train[' . $counter . '][workunit]" id="workunit[]" class="form-control workunit " style="width: 100%;" value="' . ($value['workunit']) . '"></div>';
				$tdStandardPass = '<div class="td-standardpass"><input type="text" name="train[' . $counter . '][standardpass]" id="standardpass[]" class="form-control standardpass " style="width: 100%;" value="' . ($value['standardpass']) . '"></div>';
				$tdsubstitutequota = '<div class="text-center mbot10">
						<select data-id="' . ($value['substitutequota']) . '" name="train[' . $counter . '][substitutequota]" style="width: 100%;" data-placeholder="Danh mục"  id="substitutequota_' . $counter . '" class="substitutequota modal-select2">
						</select>
						';
				$tdLevel = '<div class="td-level"><input type="text" name="train[' . $counter . '][level]" id="level[]" class="form-control level " style="width: 100%;" value="' . ($value['level']) . '"></div>';
				$tdSpecialize = '<div class="td-specialize"><input type="text" name="train[' . $counter . '][specialize]" id="specialize[]" class="form-control specialize " style="width: 100%;" value="' . ($value['specialize']) . '"></div>';
				$tdStandard = '<div class="td-standard"><input type="text" name="train[' . $counter . '][standard]" id="standard[]" class="form-control standard " style="width: 100%;" value="' . ($value['standard']) . '"></div>';
				$tdAcceptance = '<div class="td-acceptance"><input type="text" name="train[' . $counter . '][acceptance]" id="acceptance[]" class="form-control acceptance " style="width: 100%;" value="' . ($value['acceptance']) . '"></div>';
				$tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row" onclick="removeRow(this)"></i></div>';
				$counter++;
				if ($type_plan_propose == 'train') {
					$tbodytrain .= '<tr>
						<td>' . $tdNumber . '</td>
						<td>' . $tdSupplies . '</td>
						<td>' . $tdCost . '</td>
						<td>' . $tdSuppliesReplace . '</td>
						<td>' . $tdQuantity . '</td>
						<td>' . $tdPrice . '</td>
						<td>' . $tdAmount . '</td>
						<td>' . $tdWorkUnit . '</td>
						<td>' . $tdDateFinish . '</td>
						<td>' . $tdDateFrom . '</td>
						<td>' . $tdDateTo . '</td>
						<td>' . $tdStandardPass . '</td>
						<td>' . $tdActions . '</td>
					</tr>';
				}
				if ($type_plan_propose == 'repair' || $type_plan_propose == 'quality' || $type_plan_propose == 'calibration' || $type_plan_propose == 'replace' || $type_plan_propose == 'check') {
					$tbodytrain .= '<tr>
						<td>' . $tdNumber . '</td>
						<td>' . $tdSupplies . '</td>
						<td>' . $tdCost . '</td>
						<td>' . $tdSuppliesReplace . '</td>
						<td>' . $tdQuantity . '</td>
						<td>' . $tdPrice . '</td>
						<td>' . $tdAmount . '</td>
						<td>' . $tdWorkUnit . '</td>
						<td>' . $tdDateFinish . '</td>
						<td>' . $tdDateFrom . '</td>
						<td>' . $tdDateTo . '</td>
						<td>' . $tdStandardPass . '</td>
						<td>' . $tdsubstitutequota . '</td>
						<td>' . $tdActions . '</td>
					</tr>';
				}
				if ($type_plan_propose == 'items') {
					$tbodytrain .= '<tr>
						<td>' . $tdNumber . '</td>
						<td>' . $tdCost . '</td>
						<td>' . $tdUnit . '</td>
						<td>' . $tdQuantity . '</td>
						<td>' . $tdPrice . '</td>
						<td>' . $tdAmount . '</td>
						<td>' . $tdWorkUnit . '</td>
						<td>' . $tdStandardPass . '</td>
						<td>' . $tdDateFinish . '</td>
						<td>' . $tdDateFrom . '</td>
						<td>' . $tdDateTo . '</td>
						<td>' . $tdDateWarehouse . '</td>
						<td>' . $tdActions . '</td>
					</tr>';
				}
				if ($type_plan_propose == 'payment') {
					$tbodytrain .= '<tr>
						<td>' . $tdNumber . '</td>
						<td>' . $tdObject . '</td>
						<td>' . $tdCost . '</td>
						<td>' . $tdUnit_cost . '</td>
						<td>' . $tdPrice . '</td>
						<td>' . $tdDateFinish . '</td>
						<td>' . $tdDateFrom . '</td>
						<td>' . $tdDateTo . '</td>
						<td>' . $tdDateWarehouse . '</td>
						<td>' . $tdActions . '</td>
					</tr>';
				}
				if ($type_plan_propose == 'recruit') {
					$tbodytrain .= '<tr>
						<td>' . $tdNumber . '</td>
						<td>' . $tdSupplies . '</td>
						<td>' . $tdLevel . '</td>
						<td>' . $tdSpecialize . '</td>
						<td>' . $tdStandard . '</td>
						<td>' . $tdQuantity . '</td>
						<td>' . $tdDateFinish . '</td>
						<td>' . $tdDateFrom . '</td>
						<td>' . $tdDateTo . '</td>
						<td>' . $tdAcceptance . '</td>
						<td>' . $tdActions . '</td>
					</tr>';
				}
			}
		}
		$data['counter'] = $counter;
		$data['tbodytrain'] = $tbodytrain;
		if ($type_plan_propose == 'test') {
			$this->load->view('admin/plan_propose/test', $data);
		} else {
			$this->load->view('admin/plan_propose/detail/' . $type_plan_propose . '/detail_items', $data);
		}
	}

	public function get_plan_propose_time()
	{
		$type_plan_propose = $this->input->get('type_plan_propose');
		$type_plan_propose_old = $this->input->get('type_plan_propose_old');
		$id = $this->input->get('id');
		$data = [];
		$data['type_plan_propose'] = $type_plan_propose;
		$data['type_plan_propose_old'] = $type_plan_propose_old;
		$tbodytime = '';
		$counter_time = 0;
		$data['edit_time'] = 0;
		if (!empty($id)) {
			$data['edit_time'] = 1;
		}
		if (!empty($id)) {
			$time = get_table_where('tblplan_propose_time', array('id_plan_propose' => $id));
			$tbodytime = '';
			$counter_time = 0;
			foreach ($time as $key => $value) {
				$tdNumber = '<div class="td-number text-center"></div>';
				$tdSupplies = '<div class="td-code mbot10" ><input type="hidden" id="counter" class="form-control counter" value="' .
					$counter_time . '">
                <input type="text" name="time[' . $counter_time . '][items_id_time]" data-id="' . ($value['items_id_time']) . '" id="items_time_' . $counter_time . '" class="items_id_time" style="width: 100%;" data-placeholder="' . _l('choose') . '" value=""></div><div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row" onclick="removeRow_time(this)">' . _l('delete') . '</a></div></div>';
				$tdStaff = '<div class="td-staff"><input type="text" name="time[' . $counter_time . '][staff]" id="staff[]" class="form-control staff " style="width: 100%;" value="' . ($value['staff']) . '"></div>';
				$tdTimestart = '<div class="td-timestart"><input type="time" onchange="totalTime()" name="time[' . $counter_time . '][timestart]" id="timestart[]" class="form-control timestart " style="width: 100%;" value="' . ($value['timestart']) . '"></div>';
				$tdTimeend = '<div class="td-timeend"><input type="time" onchange="totalTime()" name="time[' . $counter_time . '][timeend]" id="timeend[]" class="form-control timeend " style="width: 100%;" value="' . ($value['timeend']) . '"></div>';
				$tdAllTime = '<div class="td-alltime"><input  readonly type="text" name="time[' . $counter_time . '][alltime]" id="alltime[]" class="form-control alltime " style="width: 100%;" value="' . ($value['alltime']) . '"></div>';
				$tdAllPlan = '<div class="td-allplan"><input type="text" name="time[' . $counter_time . '][allplan]" id="allplan[]" class="number-format form-control allplan " style="width: 100%;" value="' . ($value['allplan']) . '"></div>';
				$tdEvaluate = '<div class="td-evaluate"><input type="text" name="time[' . $counter_time . '][evaluate]" id="evaluate[]" class="form-control evaluate " style="width: 100%;" value="' . ($value['evaluate']) . '"></div>';
				$tdExceededtheQuota = '<div class="td-exceededthequota"><input type="text" name="time[' . $counter_time . '][exceededthequota]" id="exceededthequota[]" class="number-format form-control exceededthequota " style="width: 100%;" value="' . ($value['exceededthequota']) . '"></div>';
				$tdUnderperformingtheNorm = '<div class="td-underperformingthenorm"><input type="text" name="time[' . $counter_time . '][underperformingthenorm]" id="underperformingthenorm[]" class="number-format form-control underperformingthenorm " style="width: 100%;" value="' . ($value['underperformingthenorm']) . '"></div>';
				$tdHandoverDesk = '<div class="td-handoverdesk"><input type="text" name="time[' . $counter_time . '][handoverdesk]" id="handoverdesk[]" class="form-control handoverdesk " style="width: 100%;" value="' . ($value['handoverdesk']) . '"></div>';
				$tdWarranty = '<div class="td-warranty"><input type="text" name="time[' . $counter_time . '][warranty]" id="warranty[]" class=" form-control warranty " style="width: 100%;" value="' . ($value['warranty']) . '"></div>';
				$tdSign = '<div class="td-sign"><input type="text" name="time[' . $counter_time . '][sign]" id="sign[]" class="form-control sign " style="width: 100%;" value="' . ($value['sign']) . '"></div>';
				$tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row" onclick="removeRow_time(this)"></i></div>';
				$counter_time++;
				$tbodytime .= '<tr>
                    <td>' . $tdNumber . '</td>
                    <td>' . $tdSupplies . '</td>
                    <td>' . $tdStaff . '</td>
                    <td>' . $tdTimestart . '</td>
                    <td>' . $tdTimeend . '</td>
                    <td>' . $tdAllTime . '</td>
                    <td>' . $tdAllPlan . '</td>
                    <td>' . $tdEvaluate . '</td>
                    <td>' . $tdExceededtheQuota . '</td>
                    <td>' . $tdUnderperformingtheNorm . '</td>
                    <td>' . $tdHandoverDesk . '</td>
                    <td>' . $tdWarranty . '</td>
                    <td>' . $tdSign . '</td>
                    <td>' . $tdActions . '</td>
                </tr>';
			}
		}
		$data['counter_time'] = $counter_time;
		$data['tbodytime'] = $tbodytime;
		if ($type_plan_propose == 'test') {
			$this->load->view('admin/plan_propose/test', $data);
		} else {
			$this->load->view('admin/plan_propose/detail/' . $type_plan_propose . '/detail_time', $data);
		}
	}

	function searchMachines($id = '')
	{
		$data = [];
		if ($this->input->get()) {
			$q = $this->input->get('q');
			$limit = 50;
			$this->db->select('tbl_machines.id as id, tbl_machines.name as text', false);
			$this->db->from('tbl_machines');
			if (!empty($q)) {
				$this->db->group_start();
				$this->db->like('tbl_machines.code', $q);
				$this->db->or_like('tbl_machines.name', $q);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$data['results'] = $this->db->get()->result_array();
		}
		if (!empty($id)) {
			$this->db->select('tbl_machines.id as id, tbl_machines.name as text', false);
			$this->db->from('tbl_machines');
			$this->db->where('id', $id);
			$data['row'] = $this->db->get()->row();
		}
		echo json_encode($data);
	}

	function searchTypeItems($id = '')
	{
		$data = [];
		if ($this->input->get()) {
			$q = $this->input->get('q');
			$type = $this->input->get('types');
			$limit = 50;
			$this->db->select('tbltype_items_plan_propose.id as id, tbltype_items_plan_propose.name as text', false);
			$this->db->from('tbltype_items_plan_propose');
			$this->db->where('tbltype_items_plan_propose.type', $type);
			if (!empty($q)) {
				$this->db->group_start();
				$this->db->like('tbltype_items_plan_propose.name', $q);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$data['results'] = $this->db->get()->result_array();
		}
		if (!empty($id)) {
			$this->db->select('tbltype_items_plan_propose.id as id, tbltype_items_plan_propose.name as text', false);
			$this->db->from('tbltype_items_plan_propose');
			$this->db->where('id', $id);
			$data['row'] = $this->db->get()->row();
		}
		echo json_encode($data);
	}

	function searchObject($id = '')
	{
		$data = [];
		if ($this->input->get()) {
			$q = $this->input->get('q');
			$type = $this->input->get('types');
			$limit = 50;
			if ($type == 'vouchers_coupon') {
				$this->db->select('CONCAT("client__",tblclients.userid) as id, CONCAT(tblclients.company," (",tblclients.zcode,")") as text', false);
				$this->db->from('tblclients');
				if (!empty($q)) {
					$this->db->group_start();
					$this->db->like('tblclients.name', $q);
					$this->db->or_like('tblclients.zcode', $q);
					$this->db->group_end();
				}
				$this->db->limit($limit);
				$data['results'] = $this->db->get()->result_array();
				if (!empty($id)) {
					$this->db->select('CONCAT("client__",tblclients.userid) as id, CONCAT(tblclients.company," (",tblclients.zcode,")") as text', false);
					$this->db->from('tblclients');
					$this->db->where('userid', $id);
					$data['row'] = $this->db->get()->row();
				}
			}
			if ($type == 'pay_slip') {
				$this->db->select('CONCAT("suppliers__",tblsuppliers.id) as id, CONCAT(tblsuppliers.company," (",tblsuppliers.code,")") as text', false);
				$this->db->from('tblsuppliers');
				if (!empty($q)) {
					$this->db->group_start();
					$this->db->like('tblsuppliers.name', $q);
					$this->db->or_like('tblsuppliers.code', $q);
					$this->db->group_end();
				}
				$this->db->limit($limit);
				$data['results'] = $this->db->get()->result_array();
			}
		}
		if (!empty($id)) {
			$id = explode('__', $id);
			if ($id[0] == 'suppliers') {
				$this->db->select('CONCAT("suppliers__",tblsuppliers.id) as id, CONCAT(tblsuppliers.company," (",tblsuppliers.code,")") as text', false);
				$this->db->from('tblsuppliers');
				$this->db->where('id', $id[1]);
				$data['row'] = $this->db->get()->row();
			} else {
				$this->db->select('CONCAT("client__",tblclients.userid) as id, CONCAT(tblclients.company," (",tblclients.zcode,")") as text', false);
				$this->db->from('tblclients');
				$this->db->where('userid', $id[1]);
				$data['row'] = $this->db->get()->row();
			}
		}
		echo json_encode($data);
	}

	// public function unApproveAll()
	// {
	// 	$this->db->select('tblplan_propose.id, tblplan_propose.status');
	// 	$this->db->where('tblplan_propose.status', 1);
	// 	// $this->db->where('tblplan_propose.id', 1);
	// 	$result = $this->db->get('tblplan_propose')->result_array();
	// 	foreach ($result as $value) {
	// 		$this->approve($value['id']);
	// 	}
	// 	echo '<pre>';var_dump($result);
	// }

	public function table_plan_propose()
	{
		$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
		$aColumns = [
			'tblplan_propose.id as id',
			'tblplan_propose.date as date',
			'tblplan_propose.code as code',
			'tblclients.company as company',
			'tbl_invoices.reference_no as reference_no',
			'4',
			'tblplan_propose.type_plan_propose as type_plan_propose',
			'tblbranch.name as name_branch',
			'tblplan_propose.staff as staff',
			'tblcategory_tasks.code as code_category_tasks',
			'1',
			'tblplan_propose.money as money',
			'tblplan_propose.approved_by as approved_by',
			'tblplan_propose.content as content',
			'"" as create_tasks',
			'"" as actions',
		];
		$sIndexColumn = 'id';
		$sTable = 'tblplan_propose';
		$where = [];
		if (!empty($this->input->post('filterStatus'))) {
			$filterStatus = $this->input->post('filterStatus');
			if ($filterStatus == 2) {
				$where[] = 'AND tblplan_propose.status = 1';
			} else if ($filterStatus == 3) {
				$where[] = 'AND tblplan_propose.status = 2';
			} else if ($filterStatus == 1) {
				$where[] = 'AND tblplan_propose.status = 0';
			} else {
				$where[] = 'AND tblplan_propose.type_plan_propose = "' . $filterStatus . '"';
			}
		}
		if (!empty($this->input->post('groups_search'))) {
			$groups_search = $this->input->post('groups_search');
			$where[] = 'AND tblplan_propose.type_plan_propose = "' . $groups_search . '"';
		}
		if ($this->input->post('date_start')) {
			$where[] = 'AND DATE_FORMAT(tblplan_propose.date, "%Y-%m-%d") >= "' . to_sql_date($this->input->post('date_start')) . '"';
		}
		if ($this->input->post('date_end')) {
			$where[] = 'AND DATE_FORMAT(tblplan_propose.date, "%Y-%m-%d") <= "' . to_sql_date($this->input->post('date_end')) . '"';
		}
		if ($this->input->post('staff_search')) {
			$where[] = 'AND tblplan_propose.staff = "' . $this->input->post('staff_search') . '"';
		}
		if ($this->input->post('category_tasks')) {
			$where[] = 'AND tblplan_propose.category_tasks = "' . $this->input->post('category_tasks') . '"';
		}

		$where[] = ' AND (tbl_invoices.status_payment != 2 OR tblplan_propose.date >= "' . date('Y-m-d') . ' 00:00:00")';
		$join = [
			'LEFT JOIN tblsuggestion ON tblsuggestion.id = tblplan_propose.id_suggestion',
			'LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tblplan_propose.category_tasks',
			'LEFT JOIN tblbranch ON tblbranch.id = tblplan_propose.id_branch',
			'INNER JOIN tblclients ON tblclients.userid = tblplan_propose.client_id',
			'INNER JOIN tbl_invoices ON tbl_invoices.id = tblplan_propose.coupon_invoice_id',
		];

		if (!$this->perView) {
			$where[] = 'AND (
				EXISTS (
					SELECT 1 
					FROM tblplan_propose_assigned 
					WHERE tblplan_propose_assigned.id_plan_propose = tblplan_propose.id
					AND tblplan_propose_assigned.id_staff = "' . get_staff_user_id() . '"
				)
				OR tblplan_propose.staff = "' . get_staff_user_id() . '"
			)';
		}

		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_list_branch_staff();
				if (!empty($list_branch)) {
					$where[] = 'AND (tblplan_propose.id_branch IN (' . $list_branch . '))';
				} else {
					$where[] = 'AND tblplan_propose.id = 0';
				}
			}
		}

		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
			'tblplan_propose.date_create',
			'id_suggestion',
			'tblsuggestion.code as code_suggestion',
			'"" as code_other_payslips',
			'tblplan_propose.id_other_payslips',
			'(SELECT count(tbltasks.id) FROM tbltasks WHERE rel_id = tblplan_propose.id AND rel_type="plan_propose") as countTask',
			'"" as code_purchases',
			'"" as code_service',
			'"" as code_purchase_order',
			'"" as code_purchase_order_laster',
			'tblplan_propose.id_purchases',
			'tblplan_propose.id_service',
			'tblplan_propose.id_purchase_order',
			'tblplan_propose.status',
			'tblplan_propose.reason',
			'tblbranch.name as name_branch',
			'"" as internal_proposal',
			'"" as name_supplier',
			'tblplan_propose.id_purchase_order_internal as id_purchase_order_internal',
			'tbl_invoices.status_payment as status_payment'
		], 'ORDER BY id desc', []);

		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
		foreach ($rResult as $key => $aRow) {
			$staff_profile_image = staff_profile_image($aRow['staff'], array('staff-profile-image-small mright5'), 'small', array('data-toggle' => 'tooltip', 'data-title' => ' Vào lúc: ' . _dt($aRow['date_create'])));
			$approved_by = '';
			$edit = '';
			$print_pdf = '';
			if ($this->perApprove) {
				if ($aRow['status'] == 0) {
					$html_not_active = '<div class="not_agree">
						<div class="form-group">
							<label class="control-label">Lý do</label>
							<textarea class="form-control reason"></textarea>
						</div>
						<a id="not_agree" data-id="' . $aRow['id'] . '" data-status="2" class="btn btn-icon btn-danger">Lưu</a>
						<a class="btn btn-default btn-icon po-close">Hủy</a>
					</div>';
					$html_not_active = htmlentities($html_not_active);
					$html = "<p>
				<a id='agree' value='1' data-id='" . $aRow['id'] . "' class='btn btn-success btn-icon'>Hoàn thành</a>
				<button class='btn po-close  btn-icon'>Thoát</button></p>";
					$approved_by = '<div class="text-left mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-warning po" data-original-title="Hoàn thành">Chưa hoàn thành</span></div>';
				} else if ($aRow['status'] == 1) {
					$html = "<p>
						<a id='agree' value='0' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Chưa hoàn thành</a>
						<button class='btn po-close  btn-icon'>Thoát</button>
					</p>";
					$approved_by = '<div class="text-left mbot5">
						<span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-success po" data-original-title="Hoàn thành">Đã hoàn thành</span>
					</div>' . '' . staff_profile_image($aRow['approved_by'], array('staff-profile-image-small mright5'), 'small', array()) . get_staff_full_name($aRow['approved_by']);
				} else if ($aRow['status'] == 2) {
					$html = "<p>
						<a id='agree' value='0' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Bỏ không duyệt</a>
						<button class='btn po-close  btn-icon'>Thoát</button>
					</p>";
					$approved_by = '<div class="text-left mbot5">
						<span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-danger po" data-original-title="Không Duyệt">Không Duyệt</span>
					</div>' .
						'' . staff_profile_image($aRow['approved_by'], array('staff-profile-image-small mright5'), 'small', array()) . get_staff_full_name($aRow['approved_by']) .
						'<br/>Lý do: ' . $aRow['reason'];
				}
			}

			if ($this->perEdit) {
				// $edit = '<a class="c_modal" href="' . admin_url('plan_propose/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('Sửa phiếu') . '</a>';
			}

			$delete = '';
			if (empty($aRow['id_suggestion']) && $this->perDelete) {
				$delete = '<a onclick="deleting(' . $aRow['id'] . '); return false;" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left"><i class="fa fa-remove width-icon-actions"></i> ' . lang('Xóa phiếu') . '</a>';
			}

			$actions = '
			<div class="dropdown text-center">
				<button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">' . lang('actions') . '
				<span class="caret"></span></button>
				<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
					<li>' . $edit . '</li>
					<li>' . $print_pdf . '</li>
					<li class="not-outside">' . $delete . '</li>
				</ul>
			</div>';

			$start++;
			$column[0] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
			$column[1] = _d(substr($aRow['date'], 0, -9));
			// $column[2] = '<div><a class="c_modal" href="' . admin_url('plan_propose/view/' . $aRow['id']) . '">' . $aRow['code'] . '</a></div>';
			$column[2] = '<div>' . $aRow['code'] . '</div>';
			$column[3] = $aRow['company'];

			$status_payment = $aRow['status_payment'];
			$strStatusPayment = '<span class="label label-danger">Chưa thanh toán</span>';
			if ($status_payment == 1) {
				$strStatusPayment = '<span class="label label-warning">Thánh toán 1 phần</span>';
			} else if ($status_payment == 2) {
				$strStatusPayment = '<span class="label label-success">Thanh toán đủ</span>';
			}
			$strStatusPayment = '<div class="top5">' . $strStatusPayment . '</div>';

			$column[4] = $aRow['reference_no'] . $strStatusPayment;
			$column[5] = '';
			if (!empty($aRow['name_supplier'])) {
				$column[3] .= '<div style="margin-bottom: 2px;"><i>Nhà cung cấp: ' . $aRow['name_supplier'] . '</i></div>';
			}
			if (!empty($aRow['internal_proposal'])) {
				$internal_proposal = explode('__', $aRow['internal_proposal']);
				$column[4] .= '<p style="margin-bottom: 2px;"><span class="label label-success mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;">Phiếu: <a class="c_modal" href="' . (admin_url('internal_proposal/view/' . $internal_proposal[0])) . '">' . $internal_proposal[1] . '</a></span></p>';
			}
			if (!empty($aRow['id_purchase_order'])) {
				$column[5] .= '<p style="margin-bottom: 2px;"><span class="label label-primary	 mtop5 text-center pointer" style="padding-top: 1px;padding-bottom: 1px;" onclick="view_purchase_order(' . $aRow['id_purchase_order'] . ');">Phiếu mua hàng (PO): ' . $aRow['code_purchase_order'] . '</span></p>';
			}
			if (!empty($aRow['id_purchase_order_internal'])) {
				$column[5] .= '<p style="margin-bottom: 2px;"><span class="label label-primary	 mtop5 text-center pointer" style="padding-top: 1px;padding-bottom: 1px;" onclick="view_purchase_order(' . $aRow['id_purchase_order_internal'] . ');">Phiếu mua hàng (PO): ' . $aRow['code_purchase_order_laster'] . '</span></p>';
			}
			$column[6] = !empty($aRow['type_plan_propose']) ? ('<div>' . $this->type_title_plan_propose[$aRow['type_plan_propose']] . '</div>') : '';
			$column[7] = !empty($aRow['name_branch']) ? ('<div>' . $aRow['name_branch'] . '</div>') : '';
			$column[8] = $staff_profile_image . get_staff_full_name($aRow['staff']);
			$column[9] = (!empty($aRow['code_category_tasks']) ? ($aRow['code_category_tasks']) : '');
			$this->db->where('id_plan_propose', $aRow['id']);
			$assigned = $this->db->get('tblplan_propose_assigned')->result_array();
			$column[10] = '';
			if (!empty($assigned)) {
				foreach ($assigned as $key => $value) {
					$FullName = get_staff_full_name($value['id_staff']);
					$column[10] .= staff_profile_image($value['id_staff'], array('staff-profile-image-small mright5'), 'small', array('data-toggle' => 'tooltip', 'data-title' => $FullName));
				}
			}

			$column[11] = '<div class="text-right"><b>' . number_format($aRow['money']) . '</b></div>';
			$column[12] = $approved_by;
			$column[13] = '<div class="max-400">' . ($aRow['content']) . '</div>';
			$html = '<div>' . icon_btn('#', 'pencil', 'btn-default', array('onclick' => "add(" . $aRow['id'] . "); return false;"));
			if (true/*!$this->suggestion_type_model->isUsed($aRow['id'])*/) {
				$html .= '<a onclick="delete_suggestion_type(' . $aRow['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
					<i class="fa fa-remove"></i>
				</a></div>';
			}

			if (!has_permission('tasks', '', 'create')) {
				$column[14] = '';
			} else {
				$column[14] = '<a class="btn btn-info btn-icon mbot5 c_modal_tasks" href="' . admin_url('plan_propose/create_tasks/' . $aRow['id']) . '">Tạo công việc</a>';
				if (!empty($aRow['countTask'])) {
					$column[14] .= '<br/><span class="dropdown-toggle no_background label label-info mtop10">Đã tạo ' . $aRow['countTask'] . ' phiếu công việc . </span>';
				}
			}
			$column[15] = $actions;
			$id_tasks = 0;
			$this->db->where('rel_id', $aRow['id']);
			$this->db->where('rel_type', 'plan_propose');
			$this->db->order_by('id', 'DESC');
			$tasks = $this->db->get('tbltasks')->row();
			if (!empty($tasks)) {
				$id_tasks = $tasks->id;
				$this->db->where('taskid', $id_tasks);
				$data_checklist_items = $this->db->get('tbltask_checklist_items')->result_array();
				$htmlCheckList = '<b class="text-danger">Chưa thiết lập quy trình công việc.</b>';
				if (!empty($data_checklist_items)) {
					$htmlCheckList = '';
					$rowCheckList = '';
					foreach ($data_checklist_items as $k => $v) {
						$imgStaff = '';
						if (!empty($v['finished_from'])) {
							$imgStaff = staff_profile_image($v['finished_from'], array('staff-profile-image-small mright5 img_ch'), 'small', array(
								'data-toggle' => 'tooltip',
								'data-title' => get_staff_full_name($v['finished_from'])
							));
						}
						$rowCheckList .= '<li class="pointer ' . ($v['finished'] ? 'active' : '') . '" >
							' . $v['description'] . '
							<p class="active_poin">' . (!empty($v['finished_from']) ? ('Được ' . get_staff_full_name($v['finished_from']) . ' hoàn thành') : '') . '</p>
						</li>';
					}
					$htmlCheckList = '<div class="display: table; justify-content: center;">
							<ul class="progressbar" style="display: flex;">' . $rowCheckList . '</ul>
						</div>';
					$column[16] = $htmlCheckList;
				} else {
					$column[16] = $htmlCheckList;
				}
			} else {
				$htmlCheckList = '<b class="text-warning">Chưa tạo công việc.</b>';
				$column[17] = $htmlCheckList;
			}
			$output['aaData'][] = $column;
		}
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_list_branch_staff();
			}
		}

		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				if (!empty($list_branch)) {
					$this->db->where('(tblplan_propose.id_branch IN (' . $list_branch . '))');
				} else {
					$this->db->where('tblplan_propose.id_branch = 0', false, false);
				}
			}
		}
		if (!$this->perView && $this->perViewOwn) {
			$this->db->where('(
			EXISTS (
							SELECT 1 
							FROM tblplan_propose_assigned 
							WHERE tblplan_propose_assigned.id_plan_propose = tblplan_propose.id
							AND tblplan_propose_assigned.id_staff = "' . get_staff_user_id() . '"
			) 
			OR tblplan_propose.staff = "' . get_staff_user_id() . '"
		)', false, false);
		}
		$output['total'][1] = $this->db->get_where('tblplan_propose', ['status' => 1])->num_rows();
		$output['total'][1] = !empty($output['total'][1]) ? $output['total'][1] : 0;
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				if (!empty($list_branch)) {
					$this->db->where('(tblplan_propose.id_branch IN (' . $list_branch . '))');
				} else {
					$this->db->where('tblplan_propose.id_branch = 0', false, false);
				}
			}
		}
		if (!$this->perView && $this->perViewOwn) {
			$this->db->where('(
				EXISTS (
							SELECT 1 
							FROM tblplan_propose_assigned 
							WHERE tblplan_propose_assigned.id_plan_propose = tblplan_propose.id
							AND tblplan_propose_assigned.id_staff = "' . get_staff_user_id() . '"
			) OR tblplan_propose.staff = "' . get_staff_user_id() . '"
		)', false, false);
		}
		$output['total'][2] = $this->db->get_where('tblplan_propose', ['status' => 2])->num_rows();
		$output['total'][2] = !empty($output['total'][2]) ? $output['total'][2] : 0;
		if (!empty($this->is_branch)) {
			if (!is_admin()) {
				if (!empty($list_branch)) {
					$this->db->where('(tblplan_propose.id_branch IN (' . $list_branch . '))');
				} else {
					$this->db->where('tblplan_propose.id_branch = 0', false, false);
				}
			}
		}
		if (!$this->perView && $this->perViewOwn) {
			$this->db->where('(
			EXISTS (
					SELECT 1 
					FROM tblplan_propose_assigned 
					WHERE tblplan_propose_assigned.id_plan_propose = tblplan_propose.id
					AND tblplan_propose_assigned.id_staff = "' . get_staff_user_id() . '"
			) 
			OR tblplan_propose.staff = "' . get_staff_user_id() . '" 
		)', false, false);
		}
		$output['total'][0] = $this->db->get_where('tblplan_propose', ['status' => 0])->num_rows();
		$output['total'][0] = !empty($output['total'][0]) ? $output['total'][0] : 0;
		echo json_encode($output);
	}
}
