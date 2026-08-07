<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Internal_proposal extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        //		$this->load->model('suggestion_type_model');
        $this->load->model('internal_proposal_model');
        $this->load->model('purchases_model');
        $this->load->model('recommended_list_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->perView = has_permission('internal_proposal', '', 'view');
        $this->perViewOwn = has_permission('internal_proposal', '', 'view_own');
        $this->perAdd = has_permission('internal_proposal', '', 'create');
        $this->perEdit = has_permission('internal_proposal', '', 'edit');
        $this->perDelete = has_permission('internal_proposal', '', 'delete');
        $this->perApprove = has_permission('internal_proposal', '', 'approve_accept');
        $this->approvett = has_permission('internal_proposal', '', 'approve');
        $this->perPdf = has_permission('internal_proposal', '', 'print');
        // $this->type_plan_propose = array(
        //     array(
        //         'id' => 'train',
        //         'name' => 'KẾ HOẠCH ĐÀO TẠO'
        //     ),
        //     array(
        //         'id' => 'repair',
        //         'name' => 'KẾ HOẠCH SỬA CHỮA'
        //     ),
        //     array(
        //         'id' => 'quality',
        //         'name' => 'KẾ HOẠCH KIỂM TRA CHẤT LƯỢNG SẢN PHẨM'
        //     ),
        //     array(
        //         'id' => 'performance',
        //         'name' => 'KẾ HOẠCH BẢO DƯỠNG ĐỊNH KỲ'
        //     ),
        //     array(
        //         'id' => 'calibration',
        //         'name' => 'KẾ HOẠCH HIỆU CHUẨN ĐỊNH KỲ'
        //     ),
        //     array(
        //         'id' => 'replace',
        //         'name' => 'KẾ HOẠCH VẬT TƯ THAY THẾ ĐỊNH KỲ'
        //     ),
        //     // array(
        //     // 	'id' => 'check',
        //     // 	'name' => 'KẾ HOẠCH KIỂM TRA CHẤT LƯỢNG SẢN PHẨM'
        //     // ),
        //     array(
        //         'id' => 'npl',
        //         'name' => 'KẾ HOẠCH MUA NPL'
        //     ),
        //     array(
        //         'id' => 'tools',
        //         'name' => 'KẾ HOẠCH MUA VĂN PHÒNG PHẨM'
        //     ),
        //     array(
        //         'id' => 'sanxuat',
        //         'name' => 'KẾ HOẠCH MUA VẬT TƯ SẢN XUẤT'
        //     ),
        //     array(
        //         'id' => 'vouchers_coupon',
        //         'name' => 'KẾ HOẠCH THU'
        //     ),
        //     array(
        //         'id' => 'pay_slip',
        //         'name' => 'KẾ HOẠCH CHI'
        //     ),
        //     array(
        //         'id' => 'purchases',
        //         'name' => 'MUA NGOÀI KẾ HOẠCH'
        //     ),
        //     array(
        //         'id' => 'recruit',
        //         'name' => 'KẾ HOẠCH TUYỂN DỤNG'
        //     ),
        //     array(
        //         'id' => 'machining',
        //         'name' => 'KẾ HOẠCH GIA CÔNG'
        //     ),
        //     array(
        //         'id' => 'system',
        //         'name' => 'KẾ HOẠCH CẬP NHẬT HỆ THỐNG'
        //     ),
        //     array(
        //         'id' => 'kpi',
        //         'name' => 'KẾ HOẠCH ĐÁNH GIÁ KPI'
        //     ),
        //     array(
        //         'id' => 'reward_discipline',
        //         'name' => 'KẾ HOẠCH KHEN THƯỞNG - KỸ LUẬT'
        //     ),
        //     array(
        //         'id' => 'reports',
        //         'name' => 'KẾ HOẠCH BÁO CÁO'
        //     )
        // );
        $this->type_plan_propose = type_plan_propose();
        $this->type_title_plan_propose = [];
        foreach ($this->type_plan_propose as $key => $value) {
            $this->type_title_plan_propose[$value['id']] = $value['name'];
        }
        $this->type_object = [
            'productions_plan' => 'Kế hoạch NPL',
            'orders' => 'Đơn đặt hàng bán',
            'customer' => 'Khách hàng',
            'supplier' => 'Nhà cung cấp',
            'quotes' => 'Báo giá',
            'import' => 'Nhập kho',
            'releases' => 'Giao hàng',
        ];
        $this->code_departments = [
            '1.BOD-CFO',
            '1.BOD-PRE',
            '1.CEO',
            '1.KH',
            '1.KH',
        ];
        $this->is_branch = true;
    }

    public function index()
    {
        if (!$this->perView && !$this->perViewOwn) {
            access_denied('internal_proposal');
        }
        $data['type_plan_propose'] = $this->type_plan_propose;
        $data['title'] = _l('internal_proposal');
        $data['staff'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();
        // $data['category_tasks'] = $this->db->get('tblcategory_tasks')->result_array();
        $data['category_tasks'] = $this->site_model->getCategoryTasks();
        $data['recommended_list'] = $this->recommended_list_model->getRecommendedListParent([0], 1);
        $this->load->view('admin/internal_proposal/manage', $data);
    }

    public function table()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $tb_tamp = "(
            SELECT
                tblinternal_proposal_recommended.id_internal_proposal as id_internal_proposal,
                GROUP_CONCAT(CONCAT('-',tbl_recommended_list.name) SEPARATOR '<br>') as name
            FROM tblinternal_proposal_recommended
            JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal_recommended.recommended_list_detail_id
            GROUP BY tblinternal_proposal_recommended.id_internal_proposal
        ) tb_tamp";

        $tb_tamp_new = "(
            SELECT
                tbl_internal_proposal_purchase_items.id_internal_proposal as id_internal_proposal,
                tblsuppliers.company as company,
                tblsuppliers_groups.name as name_category
            FROM tbl_internal_proposal_purchase_items 
            JOIN tblsuppliers ON tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id
            LEFT JOIN tblsuppliers_groups ON tblsuppliers_groups.id = tblsuppliers.groups_in
            GROUP BY tbl_internal_proposal_purchase_items.id_internal_proposal
        ) tb_tamp_new";

        $aColumns = [
            '1',
            'tblinternal_proposal.id as id',
            'tblinternal_proposal.date as date',
            'tblinternal_proposal.code as code',
            'tblbranch.name as name_branch',
            'tblinternal_proposal.date_finish as date_finish',
            'coalesce(group_rl.name, "") as name_recommended_group',
            'coalesce(tbl_recommended_list.name, "") as name_recommended_list',
            'tblinternal_proposal.type_plan_propose as type_plan_propose',
            'tblinternal_proposal.staff as staff',
            'tblcategory_tasks.code as code_category_tasks',
            'tblinternal_proposal.money as money',
            'tblinternal_proposal.content as content',
            '"" as create_tasks',
            '"" as actions',
            'tblinternal_proposal.approved_by as approved_by',
            '(
                SELECT
                    CONCAT(tblproduction_report.id, "|||", tblproduction_report.reference_no, "|||", tblproduction_report.name_report)
                FROM tblproduction_report
                WHERE
                    tblproduction_report.id_internal_proposal = tblinternal_proposal.id
                    LIMIT 1
            ) as production_report',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblinternal_proposal';
        $where = [];
        if (is_numeric($this->input->post('filterStatus'))) {
            $filterStatus = $this->input->post('filterStatus');
            if ($filterStatus == 1) {
                $where[] = 'AND tblinternal_proposal.status = 1';
            } elseif ($filterStatus == 2) {
                $where[] = 'AND tblinternal_proposal.status = 2';
            } elseif ($filterStatus == 3) {
                $checkinternal_proposal_process = "(
                    SELECT tbl_internal_proposal_process.id
                    FROM tbl_internal_proposal_process
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id AND tbl_internal_proposal_process.status = 0
                    GROUP BY tbl_internal_proposal_process.id_internal_proposal
                )";
                $where[] = 'AND NOT EXISTS (' . $checkinternal_proposal_process . ') AND tblinternal_proposal.status >= 1';
            } elseif ($filterStatus == 4) {
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
                $where[] = 'AND (EXISTS (' . $checkinternal_proposal_process . ') OR tblinternal_proposal.status = 0)';
                $where[] = 'AND NOT EXISTS (' . $checkinternal_proposal_process_cancel . ')';
            } else {
                $where[] = 'AND tblinternal_proposal.status = 0';
            }
        }
        if ($this->input->post('date_start')) {
            $where[] = 'AND DATE_FORMAT(tblinternal_proposal.date, "%Y-%m-%d") >= "' . to_sql_date($this->input->post('date_start')) . '"';
        }
        if ($this->input->post('date_end')) {
            $where[] = 'AND DATE_FORMAT(tblinternal_proposal.date, "%Y-%m-%d") <= "' . to_sql_date($this->input->post('date_end')) . '"';
        }
        if ($this->input->post('staff_search')) {
            $where[] = 'AND tblinternal_proposal.staff = "' . $this->input->post('staff_search') . '"';
        }
        if ($this->input->post('staff_follow_search')) {
            $status_follow = $this->input->post('status_follow');
            $WhereMore = '';
            if (!empty($status_follow)) {
                if ($status_follow == 1) {
                    $WhereMore = ' AND (tbl_internal_proposal_process.date_status is null)';
                } else if ($status_follow == 2) {
                    $WhereMore = ' AND tbl_internal_proposal_process.status = 2';
                } else {
                    $WhereMore = ' AND tbl_internal_proposal_process.status = 1';
                }
            }

            $where[] = 'AND (
                    EXISTS (
                        SELECT 1 
                        FROM tbl_internal_proposal_process 
                        WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id 
                        AND tbl_internal_proposal_process.staff_id = "' . $this->input->post('staff_follow_search') . '"
                        ' . $WhereMore . '
                    )
                    OR EXISTS (
                        SELECT 1 
                        FROM tblinternal_proposal_monitor 
                        WHERE tblinternal_proposal_monitor.id_internal_proposal = tblinternal_proposal.id 
                        AND tblinternal_proposal_monitor.id_staff = "' . $this->input->post('staff_follow_search') . '"
                    )
                    OR EXISTS (
                        SELECT 1 
                        FROM tblinternal_proposal_head_of_department 
                        WHERE tblinternal_proposal_head_of_department.id_internal_proposal = tblinternal_proposal.id 
                        AND tblinternal_proposal_head_of_department.id_staff = "' . $this->input->post('staff_follow_search') . '"
                    )
                    OR EXISTS (
                        SELECT 1 
                        FROM tblinternal_proposal_staff_pod 
                        WHERE tblinternal_proposal_staff_pod.id_internal_proposal = tblinternal_proposal.id 
                        AND tblinternal_proposal_staff_pod.id_staff = "' . $this->input->post('staff_follow_search') . '"
                    )
                    OR staff_controller_completes = "' . $this->input->post('staff_follow_search') . '"
                    OR staff_auditor_completes = "' . $this->input->post('staff_follow_search') . '"
                    OR auditor_id = "' . $this->input->post('staff_follow_search') . '"
                    OR manager_id = "' . $this->input->post('staff_follow_search') . '"
                    OR tblinternal_proposal.staff = "' . $this->input->post('staff_follow_search') . '"
                )';
        }
        if ($this->input->post('category_tasks')) {
            $where[] = 'AND tblinternal_proposal.category_tasks = "' . $this->input->post('category_tasks') . '"';
        }
        if ($this->input->post('type_plan_propose')) {
            $where[] = 'AND tblinternal_proposal.type_plan_propose = "' . $this->input->post('type_plan_propose') . '"';
        }
        if ($this->input->post('recommended_list_id_search')) {
            $where[] = 'AND tblinternal_proposal.recommended_list_id = "' . $this->input->post('recommended_list_id_search') . '"';
        }
        if ($this->input->post('recommended_list_group_id_search')) {
            $where[] = 'AND tblinternal_proposal.recommended_list_group_id = "' . $this->input->post('recommended_list_group_id_search') . '"';
        }

        $status_table = $this->input->post('status_table');
        if (!empty($status_table) && $status_table != 'all') {
            $where[] = 'AND group_rl.id = ' . $status_table . '';
        }
        // elseif($status_table == 3){
        //     $checkinternal_proposal_process = "(
        //         SELECT tbl_internal_proposal_process.id
        //         FROM tbl_internal_proposal_process
        //         WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id AND tbl_internal_proposal_process.status = 0
        //         GROUP BY tbl_internal_proposal_process.id_internal_proposal
        //     )";
        //     $where[] = 'AND NOT EXISTS ('.$checkinternal_proposal_process.')';
        // }
        $join = [
            'LEFT JOIN tblsuggestion ON tblsuggestion.id = tblinternal_proposal.id_suggestion',
            'LEFT JOIN tblother_payslips ON tblother_payslips.id = tblinternal_proposal.id_other_payslips',
            'LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tblinternal_proposal.category_tasks',
            'LEFT JOIN tblpurchases ON tblpurchases.id = tblinternal_proposal.id_purchases',
            'LEFT JOIN tbl_services ON tbl_services.id = tblinternal_proposal.id_service',
            'LEFT JOIN tblpurchase_order ON tblpurchase_order.id = tblinternal_proposal.id_purchase_order',
            'LEFT JOIN tblbranch ON tblbranch.id = tblinternal_proposal.id_branch',
            'LEFT JOIN tbl_recommended_list group_rl ON group_rl.id = tblinternal_proposal.recommended_list_group_id',
            'LEFT JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal.recommended_list_id',
            'LEFT JOIN ' . $tb_tamp . ' ON tb_tamp.id_internal_proposal = tblinternal_proposal.id',
            'LEFT JOIN ' . $tb_tamp_new . ' ON tb_tamp_new.id_internal_proposal = tblinternal_proposal.id',
            'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tblinternal_proposal.category_recommended_id',
        ];
        if (!$this->perView) {
            $where[] = 'AND (
							EXISTS (
								SELECT 1 
								FROM tblinternal_proposal_assigned 
								WHERE tblinternal_proposal_assigned.id_internal_proposal = tblinternal_proposal.id
								AND tblinternal_proposal_assigned.id_staff = "' . get_staff_user_id() . '"
							)
							OR tblinternal_proposal.staff = "' . get_staff_user_id() . '"
				)';
        }
        $is_admin = is_admin();
        if (!empty($this->is_branch)) {
            if (!$is_admin) {
                $list_branch = get_list_branch_staff();
                if (!empty($list_branch)) {
                    $where[] = 'AND (tblinternal_proposal.id_branch IN (' . $list_branch . '))';
                } else {
                    $where[] = 'AND tblinternal_proposal.id = 0';
                }
            }
        }
        if ($is_admin) {
            $staff_user_id = get_staff_user_id();
            $this->db->select('tblstaff.is_internal_proposal as is_internal_proposal', false);
            $this->db->from('tblstaff');
            $this->db->where('tblstaff.staffid', $staff_user_id);
            $dtStaff = $this->db->get()->row_array();
            if ($dtStaff['is_internal_proposal']) {
                $where[] = ' AND ((tblinternal_proposal.approved_by = 0) OR tblinternal_proposal.approved_by = ' . $staff_user_id . ' )';
            }
        }
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblinternal_proposal.date_create',
            'id_suggestion',
            'tblsuggestion.code as code_suggestion',
            'CONCAT(tblother_payslips.prefix, "-", tblother_payslips.code) as code_other_payslips',
            'tblinternal_proposal.id_other_payslips',
            '(SELECT count(tbltasks.id) FROM tbltasks WHERE rel_id = tblinternal_proposal.id AND rel_type="internal_proposal") as countTask',
            'CONCAT(tblpurchases.prefix, tblpurchases.code) as code_purchases',
            'CONCAT(tbl_services.prefix, tbl_services.code) as code_service',
            'CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as code_purchase_order',
            'tblinternal_proposal.id_purchases',
            'tblinternal_proposal.id_service',
            'tblinternal_proposal.id_purchase_order',
            'tblinternal_proposal.status',
            'tblinternal_proposal.reason',
            // 'tblbranch.name as name_branch',
            '(SELECT GROUP_CONCAT(tblplan_propose.id,"__",tblplan_propose.code  SEPARATOR "|||") FROM tblplan_propose WHERE tblplan_propose.id_internal_proposal = tblinternal_proposal.id LIMIT 1) as plan_propose',
            'tblinternal_proposal.status_staff as status_staff',
            'tblinternal_proposal.date_status_staff as date_status_staff',
            'tblinternal_proposal.user_status_staff as user_status_staff',
            'tb_tamp.name as name_detail_suggest',
            'tbl_category_recommended.type_kpi as type_kpi',
            'tbl_category_recommended.name_table as name_table',
            'tbl_category_recommended.name as name_category_recommended',
            'tblinternal_proposal.suggest_id as suggest_id',
            'tbl_recommended_list.type_discipline as type_discipline',
            'tb_tamp_new.name_category as name_category',
            'tblinternal_proposal.category_recommended_id as category_recommended_id',
            'tblinternal_proposal.suggest_id as suggest_id',
            'tblinternal_proposal.manager_id as manager_id',
            'tblinternal_proposal.auditor_id as auditor_id',
            'tblinternal_proposal.staff_controller_completes as staff_controller_completes',
            'tblinternal_proposal.staff_auditor_completes as staff_auditor_completes',
            '(
                SELECT CONCAT(tblproduction_report.id, "|||", tblproduction_report.reference_no, "|||", COALESCE(tblproduction_report.name_report)) 
                FROM tblproduction_report 
                WHERE tblproduction_report.id_internal_proposal = tblinternal_proposal.id
                AND (id_internal_proposal_process is null OR id_internal_proposal_process = 0)
                LIMIT 1
            ) as production_report_check',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $color = '';
            $date_finish = $aRow['date_finish'];
            if (empty($aRow['date_finish'])) {
                $date_finish = $aRow['date'];
            }
            if ($date_finish < date('Y-m-d 00:00:00')) {
                $color = 'color:red;';
            }
            $staff_profile_image = staff_profile_image(
                $aRow['staff'],
                array('staff-profile-image-small mright5'),
                'small',
                array('data-toggle' => 'tooltip', 'data-title' => ' Vào lúc: ' . _dt($aRow['date_create']))
            );
            $approved_by = '';
            $edit = '';
            $print_pdf = '';
            $notEventApprove = '';
            if (!$this->perApprove) {
                $notEventApprove = 'pointer-events: none;';
            }
            if ($this->perEdit && $aRow['status'] != 1) {
                $edit = '<a onclick="add(' . $aRow['id'] . '); return false;" class="" href=""><i class="fa fa-edit"></i> ' . lang('Sửa phiếu') . '</a>';
            }
            $delete = '';
            if (empty($aRow['id_suggestion']) && $this->perDelete) {
                $delete = '<a onclick="deleting(' . $aRow['id'] . '); return false;" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left"><i class="fa fa-remove width-icon-actions"></i> ' . lang('Xóa phiếu') . '</a>';
            }
            if ($this->perPdf) {
                $print_pdf = '<a class="" href="' . base_url('admin/internal_proposal/print_pdf/' . $aRow['id']) . '" target="_blank"><i class="fa fa-print"></i> ' . lang('In phiếu') . '</a>';
            }
            $li_create_suggestion = '';
            if ($aRow['status'] == 1 && !empty($aRow['money']) && empty($aRow['id_suggestion']) && empty($aRow['id_other_payslips'])) {
                if (has_permission('suggestion', '', 'create')) {
                    $li_create_suggestion .= '<a class="c_modal" href="' . admin_url('internal_proposal/modal_create_suggestion/' . $aRow['id']) . '"><i class="fa fa-balance-scale" aria-hidden="true"></i> Tạo đề xuất tài chính</a>';
                }
            }
            if ($aRow['type_discipline'] == 1) {
                $li_create_suggestion = '';
            }
            $actions = '
				<div class="dropdown text-center">
					<button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">' . lang('actions') . '
					<span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
						<li>' . $li_create_suggestion . '</li>
						<li>' . $edit . '</li>
						<li>' . $print_pdf . '</li>
						<li class="not-outside">' . $delete . '</li>
					</ul>
				</div>';
            $start++;
            $column[0] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
            $column[1] = '<div class="text-center">' . (++$key) . '</div>';
            // $column[1] = _d(substr($aRow['date'], 0, -9));
            $column[2] = _dt_new(($aRow['date']));
            $htmlKpi = '';
            if ($aRow['type_kpi'] == 1) {
                $dtSuggest = get_table_where($aRow['name_table'], ['id' => $aRow['suggest_id']], '', 'row_array');
                if (!empty($dtSuggest)) {
                    $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
                }
            }
            $column[3] = '<div><a class="c_modal" href="' . admin_url('internal_proposal/view/' . $aRow['id']) . '">' . $aRow['code'] . '</a></div>' . $htmlKpi;
            if (($aRow['id_suggestion'] == -1)) {
                $checks = get_table_where('tblsuggestion', array('id_internal_proposal' => $aRow['id']));
                $column[3] .= '<span class="label label-success po mtop5 text-center">' . count($checks) . ' phiếu ĐXTC</span><div class="clearfix"></div>';
                if (empty($checks)) {
                    $aRow['id_suggestion'] = 0;
                }
            } elseif (!empty($aRow['id_suggestion'])) {
                $column[3] .= '<span class="label label-success po mtop5 text-center">Phiếu ĐXTC: <a data-tnh="modal" style="" class="tnh-modal" href="' . admin_url('suggestion/view_modal/' . $aRow['id_suggestion']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['code_suggestion'] . '</a></span><div class="clearfix"></div>';
            }
            if (!empty($aRow['id_other_payslips'])) {
                $column[3] .= '<span class="label label-primary pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;">Phiếu Chi: ' . $aRow['code_other_payslips'] . '</span>';
            }
            if (!empty($aRow['id_purchases']) && ($aRow['id_purchases'] != -1)) {
                $column[3] .= '<span class="label label-info pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;" onclick="view_purchases(' . $aRow['id_purchases'] . ');">' . $aRow['code_purchases'] . '</span>';
                $check_orders = get_table_where('tblpurchase_order', array('id_internal_proposal' => $aRow['id']));
                if (!empty($check_orders)) {
                    $_data_check_count = '';
                    foreach ($check_orders as $k => $v) {
                        $_data_check_count .= '<li class="hoang"><a onclick="view_purchase_order(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
                    }
                    $_outputStatus_check = '<div class="dropdown" style="text-align: left; margin-top:5px">
							<button class="dropdown-toggle no_background label label-danger" type="button" data-toggle="dropdown">Đã tạo (' . count($check_orders) . ') PO
								</button>
								<ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
                    $_outputStatus_check .= $_data_check_count;
                    $_outputStatus_check .= '</ul></div>';
                    // $column[2] .= '<span class="label label-danger pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;" ">Đã tạo ' . count($check_orders) . ' PO</span><br>';
                    $column[3] .= $_outputStatus_check;
                }
            }
            if (($aRow['id_purchases'] == -1)) {
                $check_purchase = get_table_where(
                    'tblinternal_proposal_purchase',
                    array('id_internal_proposal' => $aRow['id'])
                );
                // $column[2] .= '<div><span class="label label-info pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;" onclick="view_purchases(' . $aRow['id_purchases'] . ');">' . count($check_purchase) . ' Phiếu YCMH: ' . $aRow['code_purchases'] . '</span></div>';
                foreach ($check_purchase as $kk => $vv) {
                    $check_purchase_detail = get_table_where(
                        'tblpurchases',
                        array('id' => $vv['id_purchases']),
                        '',
                        'row'
                    );
                    if (!empty($check_purchase_detail)) {
                        $column[3] .= '<span class="label label-info pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;" onclick="view_purchases(' . $vv['id_purchases'] . ');" >' . $check_purchase_detail->prefix . $check_purchase_detail->code . '</span><div class="clearfix"></div>';
                    }
                }
                $check_orders = get_table_where('tblpurchase_order', array('id_internal_proposal' => $aRow['id']));
                if (!empty($check_orders)) {
                    $_data_check_count = '';
                    foreach ($check_orders as $k => $v) {
                        $_data_check_count .= '<li class="hoang"><a onclick="view_purchase_order(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
                    }
                    $_outputStatus_check = '<div class="dropdown" style="text-align: left; margin-top:5px">
							<button class="dropdown-toggle no_background label label-danger" type="button" data-toggle="dropdown">Đã tạo (' . count($check_orders) . ') PO
								</button>
								<ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
                    $_outputStatus_check .= $_data_check_count;
                    $_outputStatus_check .= '</ul></div>';
                    // $column[2] .= '<span class="label label-danger pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;" ">Đã tạo ' . count($check_orders) . ' PO</span><div class="clearfix"></div>';
                    $column[3] .= $_outputStatus_check;
                }
            }
            if (!empty($aRow['plan_propose'])) {
                $plan_propose_arr = explode('|||', $aRow['plan_propose']);
                foreach ($plan_propose_arr as $kk => $vvv) {
                    $plan_propose = explode('__', $vvv);
                    $column[3] .= '<span class="label label-success pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;">Phiếu: <a class="c_modal" href="' . (admin_url('plan_propose/view/' . $plan_propose[0])) . '">' . $plan_propose[1] . '</a></span><div class="clearfix"></div>';
                }
            }
            if (!empty($aRow['id_service'])) {
                $column[3] .= '<span class="label label-success pull-left mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;">Phiếu dịch vụ: ' . $aRow['code_service'] . '</span><br>';
            }
            if (!empty($aRow['id_purchase_order'])) {
                $column[3] .= '<span class="label label-primary	 pull-left mtop5 text-center pointer" style="padding-top: 1px;padding-bottom: 1px;" onclick="view_purchase_order(' . $aRow['id_purchase_order'] . ');">Phiếu mua hàng (PO): ' . $aRow['code_purchase_order'] . '</span>';
            }
            if (!empty($aRow['name_branch'])) {
                // $column[2] .= '<div class="mtop5">' . $aRow['name_branch'] . '</div>';
            }
            $production_report = explode('|||', $aRow['production_report']);
            if (!empty($aRow['production_report'])) {

                $column[3] .= '<span class="label label-primary	 pull-left mtop5 text-center pointer" style="padding-top: 1px;padding-bottom: 1px;"><a class="c_modal" href="' . admin_url('production_report/modal/' . $production_report[0]) . '">' . $production_report[1] . '</a></span>';
                // $column[2] .= '<div class="mtop5">' . $aRow['name_branch'] . '</div>';
            }
            $assigned_process = '';
            $bod_process = '';
            $monitor_process = '';
            $head_of_department_process = '';
            $manager_process = '';
            $auditor_process = '';
            $staff_controller_completes = '';
            $staff_auditor_completes = '';
            $staff = '';
            $column[4] = !empty($aRow['name_branch']) ? ('<div>' . $aRow['name_branch'] . '</div>') : '';
            $column[5] = _dt_new(($aRow['date_finish']));
            $column[6] = !empty($aRow['name_recommended_group']) ? ('<div>' . $aRow['name_recommended_group'] . '</div>') : '';
            $column[7] = !empty($aRow['name_recommended_list']) && $aRow['name_recommended_list'] != ': ' ? ('<div>' . $aRow['name_recommended_list'] . '</div>') : '';
            $column[8] = !empty($aRow['name_detail_suggest']) ? ('<div>' . $aRow['name_detail_suggest'] . '</div>') : '';
            $column[9] = !empty($aRow['name_category']) ? ('<div>' . $aRow['name_category'] . '</div>') : '';
            $column[10] = !empty($aRow['company']) ? ('<div>' . $aRow['company'] . '</div>') : '';

            $column[11] = !empty($aRow['name_category_recommended']) ? ('<div>' . $aRow['name_category_recommended'] . '</div>') : '';
            $code_Suggest = '';
            if (!empty($aRow['name_table'])) {
                $dtSuggest = get_table_where($aRow['name_table'], ['id' => $aRow['suggest_id']], '', 'row_array');
                if (!empty($dtSuggest)) {
                    if (!empty($dtSuggest['reference_no'])) {
                        $code_Suggest = $dtSuggest['reference_no'];
                    }
                    if (!empty($dtSuggest['code'])) {
                        $code_Suggest = $dtSuggest['code'];
                    }
                    $link = '';
                    $name_table = explode('tbl_', $aRow['name_table']);
                    if (count($name_table) > 1) {
                        $link = $name_table[1];
                    } else {
                        $name_table_v2 = explode('tbl', $aRow['name_table']);
                        if (count($name_table_v2) > 1) {
                            $link = $name_table_v2[1];
                        }
                    }
                    $html = '</div><a class="tnh-modal" href="' . base_url('admin/' . $link . '/view/' . $dtSuggest['id']) . '">' . $code_Suggest . '</a>';
                    if ($aRow['type_kpi'] == 1) {
                        $html = '<div><a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
                    }
                    // $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
                    $code_Suggest = $html;
                } else {
                    $html = '';
                    if ($aRow['name_table'] = 'tbl_suggest_payslips') {
                        $suggest_muti_id = get_table_where('tbl_suggest_muti_id', ['id_internal_proposal' => $aRow['id']]);
                        foreach ($suggest_muti_id as $kk => $vvs) {
                            $dtSuggest = get_table_where('tbl_suggest_payslips', ['id' => $vvs['suggest_id']], '', 'row_array');
                            if (!empty($dtSuggest)) {
                                if (!empty($dtSuggest['reference_no'])) {
                                    $code_Suggest = $dtSuggest['reference_no'];
                                }
                                if (!empty($dtSuggest['code'])) {
                                    $code_Suggest = $dtSuggest['code'];
                                }
                                $link = '';
                                $name_table = explode('tbl_', $aRow['name_table']);
                                if (count($name_table) > 1) {
                                    $link = $name_table[1];
                                } else {
                                    $name_table_v2 = explode('tbl', $aRow['name_table']);
                                    if (count($name_table_v2) > 1) {
                                        $link = $name_table_v2[1];
                                    }
                                }
                                $html .= '<li><a class="tnh-modal" href="' . base_url('admin/' . $link . '/view/' . $dtSuggest['id']) . '">' . $code_Suggest . '</a></li>';
                                // if ($aRow['type_kpi'] == 1) {
                                //     $html = '<div><a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
                                // }
                                // $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
                            }
                            $code_Suggest = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown" aria-expanded="false">' . count($suggest_muti_id) . ' Phiếu
                        </button><ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $html . '</ul></div>';
                        }
                    }
                }
            }
            $column[12] = !empty($code_Suggest) ? ('<div>' . $code_Suggest . '</div>') : '';

            $production_report_check = $aRow['production_report_check'];
            $production_report_check = explode('|||', $production_report_check);
            $view_production_report = '';
            if (!empty($production_report_check) && count($production_report_check) > 2) {
                $view_production_report = '<a class="c_modal" href="' . admin_url('production_report/modal/' . $production_report_check[0]) . '">' . $production_report_check[1] . '</a>';
            }
            $column[13] = $view_production_report;

            $column[14] = $staff_profile_image . get_staff_full_name($aRow['staff']);
            $column[15] = (!empty($aRow['code_category_tasks']) ? ($aRow['code_category_tasks']) : '');
            $this->db->where('id_internal_proposal', $aRow['id']);
            $assigned = $this->db->get('tblinternal_proposal_assigned')->result_array();

            $column[16] = !empty($aRow['type_plan_propose']) ? ('<div>' . @$this->type_title_plan_propose[$aRow['type_plan_propose']] . '</div>') : '';
            // $column[11] = '';
            if (!empty($assigned)) {
                foreach ($assigned as $k => $value) {
                    if (!empty($value['id_staff'])) {
                        $FullName = get_staff_full_name($value['id_staff']);
                        // $column[11] .= '<div>' . staff_profile_image(
                        //     $value['id_staff'],
                        //     array('staff-profile-image-small mright5'),
                        //     'small',
                        //     array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                        // ) . $FullName . '</div>';
                        if ($assigned_process == '') {
                            $assigned_process = '<div>' . staff_profile_image(
                                $value['id_staff'],
                                array('staff-profile-image-small mright5'),
                                'small',
                                array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                            ) . $FullName . '</div>';
                        }
                    }
                }
                // $column[11] = '<div style="white-space: nowrap;">' . $column[11] . '</div>';
            }
            if (!empty($aRow['manager_id'])) {
                $FullName = get_staff_full_name($aRow['manager_id']);
                if ($manager_process == '') {
                    $manager_process = '<div>' . staff_profile_image(
                        $aRow['manager_id'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                    ) . $FullName . '</div>';
                }
            }
            if (!empty($aRow['auditor_id'])) {
                $FullName = get_staff_full_name($aRow['auditor_id']);
                if ($auditor_process == '') {
                    $auditor_process = '<div>' . staff_profile_image(
                        $aRow['auditor_id'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                    ) . $FullName . '</div>';
                }
            }
            if (!empty($aRow['staff_controller_completes'])) {
                $FullName = get_staff_full_name($aRow['staff_controller_completes']);
                if ($staff_controller_completes == '') {
                    $staff_controller_completes = '<div>' . staff_profile_image(
                        $aRow['staff_controller_completes'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                    ) . $FullName . '</div>';
                }
            }
            if (!empty($aRow['staff_auditor_completes'])) {
                $FullName = get_staff_full_name($aRow['staff_auditor_completes']);
                if ($staff_auditor_completes == '') {
                    $staff_auditor_completes = '<div>' . staff_profile_image(
                        $aRow['staff_auditor_completes'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                    ) . $FullName . '</div>';
                }
            }
            if (!empty($aRow['staff'])) {
                $FullName = get_staff_full_name($aRow['staff']);
                if ($staff == '') {
                    $staff = '<div>' . staff_profile_image(
                        $aRow['staff'],
                        array('staff-profile-image-small mright5'),
                        'small',
                        array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                    ) . $FullName . '</div>';
                }
            }
            // $column[12] = '';
            $this->db->where('id_internal_proposal', $aRow['id']);
            $assigned_pod = $this->db->get('tblinternal_proposal_staff_pod')->result_array();
            if (!empty($assigned_pod)) {
                foreach ($assigned_pod as $k => $value) {
                    if (!empty($value['id_staff'])) {
                        $FullName = get_staff_full_name($value['id_staff']);
                        // $column[12] .= '<div>' . staff_profile_image(
                        //     $value['id_staff'],
                        //     array('staff-profile-image-small mright5'),
                        //     'small',
                        //     array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                        // ) . $FullName . '</div>';
                        if ($bod_process == '') {
                            $bod_process = '<div>' . staff_profile_image(
                                $value['id_staff'],
                                array('staff-profile-image-small mright5'),
                                'small',
                                array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                            ) . $FullName . '</div>';
                        }
                    }
                }
                // $column[12] = '<div style="white-space: nowrap;">' . $column[12] . '</div>';
            }

            $this->db->where('id_internal_proposal', $aRow['id']);
            $monitor_pod = $this->db->get('tblinternal_proposal_monitor')->result_array();
            if (!empty($monitor_pod)) {
                foreach ($monitor_pod as $k => $value) {
                    if (!empty($value['id_staff'])) {
                        $FullName = get_staff_full_name($value['id_staff']);
                        if ($monitor_process == '') {
                            $monitor_process = '<div>' . staff_profile_image(
                                $value['id_staff'],
                                array('staff-profile-image-small mright5'),
                                'small',
                                array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                            ) . $FullName . '</div>';
                        }
                    }
                }
            }

            $this->db->where('id_internal_proposal', $aRow['id']);
            $proposal_head_of_department_pod = $this->db->get('tblinternal_proposal_head_of_department')->result_array();
            if (!empty($proposal_head_of_department_pod)) {
                foreach ($proposal_head_of_department_pod as $k => $value) {
                    if (!empty($value['id_staff'])) {
                        $FullName = get_staff_full_name($value['id_staff']);
                        if ($head_of_department_process == '') {
                            $head_of_department_process = '<div>' . staff_profile_image(
                                $value['id_staff'],
                                array('staff-profile-image-small mright5'),
                                'small',
                                array('data-toggle' => 'tooltip', 'data-title' => $FullName)
                            ) . $FullName . '</div>';
                        }
                    }
                }
            }

            $column[17] = '<div class="text-right" style="color: ' . ($aRow['type_discipline'] == 1 ? 'red' : 'black') . '"><b>' . number_format($aRow['money']) . '</b></div>';
            $column[18] = '<div class="max-400">' . ($aRow['content']) . '</div>';
            $html = '<div>' . icon_btn(
                '#',
                'pencil',
                'btn-default',
                array('onclick' => "add(" . $aRow['id'] . "); return false;")
            );
            if (true/*!$this->suggestion_type_model->isUsed($aRow['id'])*/) {
                $html .= '<a onclick="delete_suggestion_type(' . $aRow['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
                                    <i class="fa fa-remove"></i>
                                </a></div>';
            }
            if (!has_permission('tasks', '', 'create')) {
                $column[19] = '';
            } else {
                $column[19] = '<a class="btn btn-info btn-icon mbot5 c_modal_tasks" href="' . admin_url('internal_proposal/create_tasks/' . $aRow['id']) . '">Tạo công việc</a>';
                if (!empty($aRow['countTask'])) {
                    $data_tasks = get_table_where('tbltasks', ['rel_id' => $aRow['id'], 'rel_type' => 'internal_proposal'], '', 'result_array', '', 'tbltasks.id,tbltasks.name');
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
                    $column[19] .= $_data;
                    // $column[15] .= '<br/><span class="dropdown-toggle no_background label label-info mtop10">' . $aRow['countTask'] . ' phiếu công việc . </span>';
                    // '(SELECT count(tbltasks.id) FROM tbltasks WHERE rel_id = tblinternal_proposal.id AND rel_type="internal_proposal") as countTask',

                }
            }
            $column[20] = $actions;




            $activeStaff = '';
            $activeManager = '';
            $strStaffActive = '<a style="font-size:10px; ' . $notEventApprove . '" class="" data-toggle="tooltip" title="' . lang('Duyệt') . '"onclick="handling_status_internal(' . $aRow['id'] . ', 1); return false;"><i  class="wrap-icon-check fa fa-check-circle-o"></i></a>';
            if ($aRow['status_staff']) {
                $activeStaff = 'active';
                $full_name = get_staff_full_name($aRow['user_status_staff']);
                $date_directorate = _d($aRow['date_status_staff']);
                $strStaffActive = '<div><a class="mright5" data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $aRow['user_status_staff']) . '">' . staff_profile_image(
                    $aRow['user_status_staff'],
                    [
                        'staff-profile-image-small-2x mbot5',
                    ]
                ) . '</a> <span>' . $date_directorate . '</div>';
                $strStaffActive .= '<a style="font-size:10px; ' . $notEventApprove . '" class="" data-toggle="tooltip" title="' . lang('delete') . '" onclick="handling_status_internal(' . $aRow['id'] . ', 0); return false;"><i  class="wrap-icon-check text-danger fa fa-remove"></i></a>';
            } else {
                $notEventApprove = 'pointer-events: none;';
            }
            $strManagerAc = '';
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
				<a id='agree' value='1' data-id='" . $aRow['id'] . "' class='btn btn-success btn-icon'>Duyệt</a>
				<a data-content='$html_not_active' title='Không duyệt' class='btn btn-danger btn-icon'  data-html='true' data-toggle='popover' data-container='body' data-placement='left' style='cursor: pointer;' class='btn btn-danger btn-icon'>Không Duyệt</a>
				<button class='btn po-close  btn-icon'>Thoát</button></p>";
                $strManagerAc = '<div class="mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="right" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div>';
            } elseif ($aRow['status'] == 1) {
                $activeManager = 'active';
                $html = "<p>
							<a id='agree' value='0' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Bỏ duyệt</a>
							<button class='btn po-close  btn-icon'>Thoát</button>
						</p>";
                $strManagerAc = '<div class="mbot5">
									<span data-html="true" data-toggle="popover" data-container="body" data-placement="right" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-success po" data-original-title="Duyệt">Đã duyệt</span>
								</div>' . '' . staff_profile_image(
                    $aRow['approved_by'],
                    array('staff-profile-image-small mright5'),
                    'small',
                    array()
                ) . get_staff_full_name($aRow['approved_by']);
            } elseif ($aRow['status'] == 2) {
                $html = "<p>
							<a id='agree' value='0' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Bỏ không duyệt</a>
							<button class='btn po-close  btn-icon'>Thoát</button>
						</p>";
                $strManagerAc = '<div class="mbot5">
									<span data-html="true" data-toggle="popover" data-container="body" data-placement="right" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-danger po" data-original-title="Không Duyệt">Không Duyệt</span>
								</div>' .
                    '' . staff_profile_image(
                    $aRow['approved_by'],
                    array('staff-profile-image-small mright5'),
                    'small',
                    array()
                ) . get_staff_full_name($aRow['approved_by']) .
                    '<br/>Lý do: ' . $aRow['reason'];
            }
            $strManagerActive = '<a style="font-size:10px;" class="">
				' . $strManagerAc . '
			</a>';
            $this->db->select('tbl_internal_proposal_process.*,tbl_internal_proposal_process_child.id as childs');
            $this->db->where('tbl_internal_proposal_process.id_internal_proposal', $aRow['id']);
            $this->db->join('tbl_internal_proposal_process_child', 'tbl_internal_proposal_process_child.recommended_list_id = tbl_internal_proposal_process.id_process AND tbl_internal_proposal_process_child.id_internal_proposal = tbl_internal_proposal_process.id_internal_proposal', 'left');
            $this->db->order_by('tbl_internal_proposal_process.id_process asc');
            $this->db->group_by('tbl_internal_proposal_process.id');
            $data_checklist_items = $this->db->get('tbl_internal_proposal_process')->result_array();
            $htmlCheckList = '<b class="text-danger">Chưa thiết lập quy trình đề xuất.</b>';
            if (empty($data_checklist_items)) {
                $approved_by = '<div class="">
				<div class="wrap-content-process  ' . $activeStaff . '">
					<div class="wrap-step-process line"></div>
					<div class="wrap-title-process">
						' . lang('Quản lý duyệt') . '
					</div>
					<div class="wrap-title-process" style="">
						' . $strStaffActive . '
					</div>
				</div>
				<div class="wrap-content-process ' . $activeManager . '">
					<div class="wrap-step-process"></div>
					<div class="wrap-title-process" style="">
						' . lang('BOD duyệt') . '
					</div>
					<div class="wrap-title-process" style="' . $notEventApprove . '">
						' . $strManagerActive . '
					</div>
				</div>
			</div>';
            } else {
                $approved_by = '';
                $rowCheckList = '';
                $rowStaffList = '';
                $id_check = 0;
                foreach ($data_checklist_items as $k => $v) {
                    $date_status = '';
                    if (!empty($v['date_status'])) {
                        $date_status = explode(' ', $v['date_status'])[0] . ' 00:00:00';
                    }

                    $strManagerAc = '';
                    if ($v['status'] == 0) {
                        $color = '';
                        if ($date_finish < date('Y-m-d 00:0:00')) {
                            $color = 'color:red;';
                        }
                        $html_not_active = '<div class="not_agree">
                                <div class="form-group">
                                    <label class="control-label">Lý do</label>
                                    <textarea class="form-control reason"></textarea>
                                </div>
                                <a id="not_agree" data-id="' . $aRow['id'] . '" data-status="' . $v['id'] . '" data-status="2" class="btn btn-icon btn-danger">Lưu</a>
                                <a class="btn btn-default btn-icon po-close">Hủy</a>
                            </div>';
                        $html_not_active = htmlentities($html_not_active);
                        $html = "<p><a style='font-size:10px;' data-toggle='tooltip' title='" . lang('Duyệt tiêu chí') . "' class='c_modal btn btn-success btn-icon' href='" . admin_url('internal_proposal/inspection_criteria/' . $aRow['id'] . '/' . $v['id'] . '/' . $v['id_process']) . "'>Duyệt</a>
        <a data-content='$html_not_active' title='Không duyệt' class='btn btn-danger btn-icon'  data-html='true' data-toggle='popover' data-placement='bottom'  data-container='body' data-placement='left' style='cursor: pointer;' class='btn btn-danger btn-icon'>Không Duyệt</a>
        <button class='btn po-close  btn-icon'>Thoát</button></p>";
                        $CheckCreateBCKPH = $this->CheckCreateBCKPH($aRow['id'], $v['id_process'], $v['id']);
                        $__htmls = '';
                        if ($CheckCreateBCKPH != 1) {
                            $__htmls = '<div><span class="label label-danger">Có sự cố</span></div>';
                        }
                        $strManagerAc = '<div class="mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="right" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div><br>' . $__htmls;
                    } elseif ($v['status'] == 1) {
                        $color = '';
                        if ($date_finish . ' 23:59:59' < $date_status) {
                            $color = 'color:#ff9b00e3;';
                        }
                        $activeManager = 'active';
                        $html = "<p>
                    <a id='agree' value='0' data-id='" . $aRow['id'] . "' data-status='" . $v['id'] . "' class='btn btn-danger btn-icon'>Bỏ duyệt</a>
                    <button class='btn po-close  btn-icon'>Thoát</button>
                </p>";
                        $strManagerAc = '<div class="mbot5">
                            <span data-html="true" data-toggle="popover" data-container="body" data-placement="right" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-success po" data-original-title="Duyệt">Đã duyệt</span>
                        </div>' . '' . staff_profile_image(
                            $v['staff_id'],
                            array('staff-profile-image-small mright5'),
                            'small',
                            array()
                        ) . get_staff_full_name($v['staff_id']) . '<br><span>' . _dt($v['date_status']) . '</span>';
                        if (!empty($v['childs'])) {
                            $strManagerAc .= '<div style="border: 1px solid blue;border-radius: 5px;font-size: 10px;color: blue;width:90px;margin-left:10px"><a style="color: blue" class="c_modal" href="' . admin_url('internal_proposal/inspection_criteria_new/' . $aRow['id'] . '/' . $v['id'] . '/' . $v['id_process'] . '/' . $v['status']) . '">Xem kiểm quy trình</a></div>';
                            $check = get_table_where('tbl_tinternal_proposal_inspection_criteria_process', ['id_internal_proposal' => $aRow['id'], 'process_id' => $v['id_process'], 'id_internal_proposal_process' => $v['id'], 'isCheckNot' => 1]);
                            if (!empty($check)) {
                                foreach ($check as $kvchild => $vvchild) {
                                    $production_report = get_table_where('tblproduction_report', ['id_internal_proposal' => $aRow['id'], 'id_internal_proposal_process' => $v['id'], 'id_internal_proposal_process_child' => $vvchild['inspection_criteria']], '', 'row_array');
                                    if (!empty($production_report)) {
                                        $strManagerAc .= '<div><a class="c_modal" href="' . base_url('admin/production_report/modal/') . $production_report['id'] . '">' . $production_report['reference_no'] . '</a>,</div>';
                                    }
                                }
                            }
                            $CheckCreateBCKPH = $this->CheckCreateBCKPH($aRow['id'], $v['id_process'], $v['id']);
                            $__htmls = '';
                            if ($CheckCreateBCKPH != 1) {
                                $__htmls = '<div><span class="label label-danger">Có sự cố</span></div>';
                            }
                            $strManagerAc .= $__htmls;
                        }
                    } elseif ($v['status'] == 2) {
                        $html = "<p>
                    <a id='agree' value='0' data-id='" . $aRow['id'] . "'  data-status='" . $v['id'] . "' class='btn btn-danger btn-icon'>Bỏ không duyệt</a>
                    <button class='btn po-close  btn-icon'>Thoát</button>
                </p>";
                        $strManagerAc = '<div class="mbot5">
                            <span data-html="true" data-toggle="popover" data-container="body" data-placement="right" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-danger po" data-original-title="Không Duyệt">Không Duyệt</span>
                        </div>' .
                            '' . staff_profile_image(
                            $v['staff_id'],
                            array('staff-profile-image-small mright5'),
                            'small',
                            array()
                        ) . get_staff_full_name($v['staff_id']) .
                            '<br/>Lý do: ' . $v['reason'];
                        $production_report = get_table_where('tblproduction_report', ['id_internal_proposal' => $aRow['id'], 'id_internal_proposal_process' => $v['id'], 'id_internal_proposal_process_child' => 0], '', 'row_array');
                        if (empty($production_report)) {
                            $strManagerAc .= '<a style="font-size: 9px;" class="btn btn-info btn-icon mbot10" href="' . base_url('admin/production_report/detail') . '?id_internal_proposal=' . $aRow['id'] . '&id_internal_proposal_process=' . $v['id'] . '" target="_blank">Tạo phiếu báo cáo</a>';
                        } else {
                            $strManagerAc .= '<div><a class="c_modal" href="' . base_url('admin/production_report/modal/') . $production_report['id'] . '">' . $production_report['reference_no'] . '</a></div>';
                        }
                    }
                    $strManagerActive = '<a style="font-size:10px;" class="">
                ' . $strManagerAc . '
                </a>';
                    $rowCheckList .= '<li style="' . $color . '" class="pointer ' . ($v['staff_id'] ? 'active' : '') . '"">
                        ' . $v['name'] . '
                        <div class="wrap-title-process" >
                        ' . $strManagerActive . '
                        </div>
                    </li>';
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
                    $rowStaffList .= '<li class="initli" style="list-style-type: none;width: 110px;float: left;font-size: 9px;position: relative;text-align: center;color: #7d7d7d;z-index: 0;font-size: 9px;">
                                        ' . $staffs . '
                                    </li>';
                }

                $approved_by = '<div class="display: table; justify-content: center;">
								<ul class="progressbar" style="display: flex;">' . $rowStaffList . '</ul>
								<ul class="progressbar" style="display: flex;">' . $rowCheckList . '</ul>
						 </div>';
            }

            $column[21] = $approved_by;

            $output['aaData'][] = $column;
        }
        if ($is_admin) {
            $staff_user_id = get_staff_user_id();
            $this->db->select('tblstaff.is_internal_proposal as is_internal_proposal', false);
            $this->db->from('tblstaff');
            $this->db->where('tblstaff.staffid', $staff_user_id);
            $dtStaff = $this->db->get()->row_array();
        }
        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                $list_branch = get_list_branch_staff();
            }
        }
        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                if (!empty($list_branch)) {
                    $this->db->where('(tblinternal_proposal.id_branch IN (' . $list_branch . '))');
                } else {
                    $this->db->where('tblinternal_proposal.id_branch = 0', false, false);
                }
            }
        }
        if (!$this->perView && $this->perViewOwn) {
            $this->db->where('(
				EXISTS (
								SELECT 1 
								FROM tblinternal_proposal_assigned 
								WHERE tblinternal_proposal_assigned.id_internal_proposal = tblinternal_proposal.id
								AND tblinternal_proposal_assigned.id_staff = "' . get_staff_user_id() . '"
				) 
				OR tblinternal_proposal.staff = "' . get_staff_user_id() . '"
			)', false, false);
        }
        $output['total'][1] = $this->db->get_where('tblinternal_proposal', ['status' => 1])->num_rows();
        $output['total'][1] = !empty($output['total'][1]) ? $output['total'][1] : 0;
        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                if (!empty($list_branch)) {
                    $this->db->where('(tblinternal_proposal.id_branch IN (' . $list_branch . '))');
                } else {
                    $this->db->where('tblinternal_proposal.id_branch = 0', false, false);
                }
            }
        }
        if (!$this->perView && $this->perViewOwn) {
            $this->db->where('(
					EXISTS (
								SELECT 1 
								FROM tblinternal_proposal_assigned 
								WHERE tblinternal_proposal_assigned.id_internal_proposal = tblinternal_proposal.id
								AND tblinternal_proposal_assigned.id_staff = "' . get_staff_user_id() . '"
				) OR tblinternal_proposal.staff = "' . get_staff_user_id() . '"
			)', false, false);
        }
        $output['total'][2] = $this->db->get_where('tblinternal_proposal', ['status' => 2])->num_rows();
        $output['total'][2] = !empty($output['total'][2]) ? $output['total'][2] : 0;
        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                if (!empty($list_branch)) {
                    $this->db->where('(tblinternal_proposal.id_branch IN (' . $list_branch . '))');
                } else {
                    $this->db->where('tblinternal_proposal.id_branch = 0', false, false);
                }
            }
        }
        if (!$this->perView && $this->perViewOwn) {
            $this->db->where('(
				EXISTS (
						SELECT 1 
						FROM tblinternal_proposal_assigned 
						WHERE tblinternal_proposal_assigned.id_internal_proposal = tblinternal_proposal.id
						AND tblinternal_proposal_assigned.id_staff = "' . get_staff_user_id() . '"
				) 
				OR tblinternal_proposal.staff = "' . get_staff_user_id() . '" 
			)', false, false);
        }
        if (!empty($dtStaff['is_internal_proposal'])) {
            $this->db->where(
                '((tblinternal_proposal.status_staff = 1 AND tblinternal_proposal.approved_by = 0) OR tblinternal_proposal.approved_by = ' . $staff_user_id . ' )',
                false,
                false
            );
        }
        $output['total'][0] = $this->db->get_where('tblinternal_proposal', ['status' => 0])->num_rows();
        $output['total'][0] = !empty($output['total'][0]) ? $output['total'][0] : 0;


        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                if (!empty($list_branch)) {
                    $this->db->where('(tblinternal_proposal.id_branch IN (' . $list_branch . '))');
                } else {
                    $this->db->where('tblinternal_proposal.id_branch = 0', false, false);
                }
            }
        }
        if (!$this->perView && $this->perViewOwn) {
            $this->db->where('(
					EXISTS (
								SELECT 1 
								FROM tblinternal_proposal_assigned 
								WHERE tblinternal_proposal_assigned.id_internal_proposal = tblinternal_proposal.id
								AND tblinternal_proposal_assigned.id_staff = "' . get_staff_user_id() . '"
				) OR tblinternal_proposal.staff = "' . get_staff_user_id() . '"
			)', false, false);
        }
        $checkinternal_proposal_process = "(
            SELECT tbl_internal_proposal_process.id
            FROM tbl_internal_proposal_process
            WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id AND tbl_internal_proposal_process.status = 0
            GROUP BY tbl_internal_proposal_process.id_internal_proposal
        )";
        // $where[] = 'AND NOT EXISTS (' . $checkinternal_proposal_process . ')';
        $this->db->where('(
        NOT EXISTS (
								' . $checkinternal_proposal_process . '
				)
			) AND status >= 1', false, false);
        $output['total'][3] = $this->db->get_where('tblinternal_proposal')->num_rows();
        $output['total'][3] = !empty($output['total'][3]) ? $output['total'][3] : 0;


        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                if (!empty($list_branch)) {
                    $this->db->where('(tblinternal_proposal.id_branch IN (' . $list_branch . '))');
                } else {
                    $this->db->where('tblinternal_proposal.id_branch = 0', false, false);
                }
            }
        }
        if (!$this->perView && $this->perViewOwn) {
            $this->db->where('(
					EXISTS (
								SELECT 1 
								FROM tblinternal_proposal_assigned 
								WHERE tblinternal_proposal_assigned.id_internal_proposal = tblinternal_proposal.id
								AND tblinternal_proposal_assigned.id_staff = "' . get_staff_user_id() . '"
				) OR tblinternal_proposal.staff = "' . get_staff_user_id() . '"
			)', false, false);
        }
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
        // $where[] = 'AND NOT EXISTS (' . $checkinternal_proposal_process . ')';
        $this->db->where('(
        (EXISTS (
								' . $checkinternal_proposal_process . '
				)
			) OR status = 0)', false, false);
        $this->db->where('(
                (NOT EXISTS (
                                        ' . $checkinternal_proposal_process_cancel . '
                        )
                    ))', false, false);
        $output['total'][4] = $this->db->get_where('tblinternal_proposal')->num_rows();
        $output['total'][4] = !empty($output['total'][4]) ? $output['total'][4] : 0;

        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                if (!empty($list_branch)) {
                    $this->db->where('(tblinternal_proposal.id_branch IN (' . $list_branch . '))');
                } else {
                    $this->db->where('tblinternal_proposal.id_branch = 0', false, false);
                }
            }
        }
        echo json_encode($output);
    }

    public function create_tasks($id = '')
    {
        if (!has_permission('tasks', '', 'create')) {
            echo '<script>
						alert_float("danger", "Tạo không có quyền tạo phiếu công việc");
				</script>';
            die();
        }
        $id_tasks = $this->createTaskAuto($id);
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

    public function createPurchaseAuto($ids = '', $check_du = 0)
    {
        $plan_id_text = '';
        $plan_id_check = 0;
        $this->db->where('id', $ids);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        $items = $this->purchases_model->get_items_internal_proposal($ids);
        $name = '';
        if (!empty($internal_proposal)) {
            $items_purchase = array();
            foreach ($items as $key => $value) {
                $items_purchase[$value['suppliers_id']][] = $value;
            }
        }
        $listArrayPurchase = [];
        foreach ($items_purchase as $k => $v) {
            $suppliers_id = $k;
            $suppliert = get_table_where('tblsuppliers', array('id' => $suppliers_id), '', 'row');
            $amount_to_vnd = 1;
            $type_order = 1;
            if ($suppliert->default_currency == 1) {
                $amount_to_vnds = get_table_where(
                    'tblcurrencies',
                    array('id' => $suppliert->default_currency),
                    '',
                    'row'
                );
                if (!empty($amount_to_vnds)) {
                    $amount_to_vnd = $amount_to_vnds->amount_to_vnd;
                    $type_order = 2;
                }
            }
            $id_purchasess = [];
            foreach ($v as $key => $item) {
                $id_purchasess[] = $item['id_purchases'];
            }
            $id_purchasess = $id_purchasess;
            $quotes = array(
                'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchase_order') + 1),
                'prefix' => get_option('prefix_purchase_order'),
                'id_internal_proposal' => $ids,
                'staff_create' => get_staff_user_id(),
                'date' => date('Y-m-d'),
                'delivery_date' => date('Y-m-d'),
                'date_create' => date('Y:m:d H:i:s'),
                'suppliers_id' => $suppliers_id,
                'currency' => (!empty($suppliert->default_currency) ? $suppliert->default_currency : 5),
                'amount_to_vnd' => $amount_to_vnd,
                'type_items' => '-1',
                'check_purchase_all' => 1,
                'type_order' => $type_order,
                'tax_all' => '',
                'status' => 3,
                'note' => '',
                'history_status' => get_staff_user_id() . ',' . date('Y:m:d H:i:s') . '|' . get_staff_user_id() . ',' . date('Y:m:d H:i:s') . '|' . get_staff_user_id() . ',' . date('Y:m:d H:i:s'),
                'delivery_cost' => 0,
                'reduce_cost' => 0,
            );
            if ($check_du == 1) {
                $quotes['is_du'] = get_staff_user_id();
            }
            $delivery_cost = 0;
            $reduce_cost = 0;
            $quotes['id_purchases'] = implode(',', $id_purchasess);
            if ($this->db->insert('tblpurchase_order', $quotes)) {
                $id = $this->db->insert_id();
                $listArrayPurchase[] = $id;
                log_activity('Purchase order Insert [ID: ' . $id . ']');
                $count = 1;
            }
            $total_expected_all = 0;
            $total_suppliers_all = 0;
            $total_novat = 0;
            $promotion_expecteds = 0;
            foreach ($v as $key => $item) {
                if (!empty($item['id'])) {
                    $item['quantity'] = str_replace(',', '', $item['quantity']);
                    $item['quantity_suppliers'] = str_replace(',', '', $item['quantity']);
                    $item['price_expected'] = 0;
                    $item['price_suppliers'] = str_replace(',', '', $item['price']);
                    $item['promotion_expected'] = 0;
                    // $item['tax_rate'] = 0;
                    // $item['tax_id'] = 0;
                    $item['note'] = '';
                    $item['exchange_standard_unit'] = str_replace(',', '', $item['exchange_unit']);
                    $item['quantity_stock'] = str_replace(',', '', $item['quantity_stock']);
                    $item['exchange_stock'] = str_replace(',', '', $item['exchange_stock']);
                    $item['quantity_payment'] = str_replace(',', '', $item['quantity_payment']);
                    $item['exchange_payment'] = str_replace(',', '', $item['exchange_payment']);
                    if (($item['quantity_stock'] == 'NaN')) {
                        $item['quantity_stock'] = 0;
                    }
                    if (($item['quantity_payment'] == 'NaN')) {
                        $item['quantity_payment'] = 0;
                    }
                    $total_expected = $item['quantity_payment'] * $item['price_expected'] * (1 + ($item['tax_rate'] / 100));
                    $total_suppliers = (($item['quantity_payment'] * $item['price_suppliers'] * (1 + ($item['tax_rate'] / 100))) - $item['promotion_expected']);
                    $total_novats = ($item['quantity_payment'] * $item['price_suppliers']);
                    $promotion_expecteds += $item['promotion_expected'];
                    if (empty($item['plan_id'])) {
                        $plan_id = 0;
                    } else {
                        $plan_id = $item['plan_id'];
                        if ($item['plan_id'] != $plan_id_check) {
                            $plan_id_text .= $item['plan_id'] . ',';
                            $plan_id_check = $item['plan_id'];
                        }
                    }
                    $items = array(
                        'id_purchase_order' => $id,
                        'id_internal_proposal_purchase_items' => $item['id'],
                        'info_items' => $item['info_items'],
                        'product_id' => $item['id_items'],
                        'type' => $item['type'],
                        'quantity' => $item['quantity'],
                        'tax_id' => !empty($item['tax_id']) ? $item['tax_id'] : 0,
                        'tax_rate' => $item['tax_rate'],
                        'quantity_suppliers' => $item['quantity_suppliers'],
                        'price_expected' => $item['price_expected'],
                        'price_suppliers' => $item['price_suppliers'],
                        'promotion_expected' => $item['promotion_expected'],
                        'quantity_unit' => $item['quantity_suppliers'],
                        'exchange_unit' => $item['exchange_standard_unit'],
                        'quantity_stock' => $item['quantity_stock'],
                        'exchange_stock' => $item['exchange_stock'],
                        'quantity_payment' => $item['quantity_payment'],
                        'exchange_payment' => $item['exchange_payment'],
                        'total_expected' => $total_expected,
                        'total_suppliers' => $total_suppliers,
                        'note' => $item['note'],
                        'plan_id' => $plan_id,
                        'purchase_items_id' => $item['id_purchases_items'],
                    );
                    if ($this->db->insert('tblpurchase_order_items', $items)) {
                        $id_items_order = $this->db->insert_id();
                        $ktr_supp = get_table_where('tblmainstream_goods', array(
                            'id_suppliers' => $suppliers_id,
                            'id_items' => $item['id_items'],
                            'type' => $item['type']
                        ), '', 'row');
                        if (!empty($ktr_supp)) {
                            $this->db->update(
                                'tblmainstream_goods',
                                array('amount_to_vnd' => $amount_to_vnd, 'price' => $item['price_suppliers']),
                                array('id' => $ktr_supp->id)
                            );
                        } else {
                            $_mainstream = array(
                                'id_suppliers' => $suppliers_id,
                                'id_items' => $item['id_items'],
                                'type' => $item['type'],
                                'price' => $item['price_suppliers'],
                                'amount_to_vnd' => $amount_to_vnd,
                            );
                            $this->db->insert('tblmainstream_goods', $_mainstream);
                        }
                        if (!empty($id_purchasess)) {
                            $id_purchases = $id_purchasess;
                            $quantity = $item['quantity_suppliers'];
                            foreach ($id_purchases as $kch => $vch) {
                                $purchases_items = get_table_where(
                                    'tblpurchases_items',
                                    array('id' => $item['id_purchases_items']),
                                    '',
                                    'row'
                                );
                                if (!empty($purchases_items)) {
                                    $quantity_all = $purchases_items->quantity_net - $purchases_items->quantity_create_all - $purchases_items->quantity_create;
                                    if (!empty($purchases_items) && ($quantity > 0) && ($quantity_all > 0)) {
                                        $quantity_create_all = $purchases_items->quantity_net - $purchases_items->quantity_create_all - $purchases_items->quantity_create - $quantity;
                                        if ($quantity > 0) {
                                            if ($quantity_create_all < 0) {
                                                $this->db->update(
                                                    'tblpurchases_items',
                                                    array('quantity_create_all' => ($purchases_items->quantity_net - $purchases_items->quantity_create)),
                                                    array('id' => $purchases_items->id)
                                                );
                                                $quantity = $quantity - ($purchases_items->quantity_net - $purchases_items->quantity_create - $purchases_items->quantity_create_all);
                                                $purchase_to_order_items = array(
                                                    'id_items' => $item['id_items'],
                                                    'type' => $item['type'],
                                                    'quantity' => $purchases_items->quantity_net - $purchases_items->quantity_create - $purchases_items->quantity_create_all,
                                                    'id_purchase' => $vch,
                                                    'id_purchase_order' => $id,
                                                    'id_purchase_items' => $purchases_items->id,
                                                    'id_purchase_order_items' => $id_items_order,
                                                );
                                                $this->db->insert(
                                                    'tblpurchase_to_order_items',
                                                    $purchase_to_order_items
                                                );
                                            } else {
                                                $this->db->update(
                                                    'tblpurchases_items',
                                                    array('quantity_create_all' => ($purchases_items->quantity_create_all + $quantity)),
                                                    array('id' => $purchases_items->id)
                                                );
                                                $purchase_to_order_items = array(
                                                    'id_items' => $item['id_items'],
                                                    'type' => $item['type'],
                                                    'quantity' => $quantity,
                                                    'id_purchase' => $vch,
                                                    'id_purchase_order' => $id,
                                                    'id_purchase_items' => $purchases_items->id,
                                                    'id_purchase_order_items' => $id_items_order,
                                                );
                                                $this->db->insert(
                                                    'tblpurchase_to_order_items',
                                                    $purchase_to_order_items
                                                );
                                                $quantity = 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        log_activity('Purchase order insert [ID purchases: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                        $count++;
                        $total_expected_all += $total_expected;
                        $total_suppliers_all += $total_suppliers;
                        $total_novat += $total_novats;
                    } else {
                        exit("error");
                    }
                }
            }
            foreach ($id_purchasess as $kch => $vch) {
                $this->db->select('quantity_net,quantity_create_all,quantity_create');
                $this->db->where('purchases_id', $vch);
                $this->db->having('(quantity_net - quantity_create_all - quantity_create) >', 0);
                $items_purchase = $this->db->get('tblpurchases_items')->row();
                if (empty($items_purchase)) {
                    $purchases = get_table_where('tblpurchases', array('id' => $vch), '', 'row');
                    $staff_id = '1foso';
                    $date = date('Y-m-d H:i:s');
                    $history_status = $purchases->history_status;
                    $history_status .= '|' . $staff_id . ',' . $date;
                    $in = array(
                        'history_status' => $history_status,
                        'note_cancel' => '',
                        'status' => 4,
                    );
                    $this->db->where('id', $vch);
                    $this->db->update('tblpurchases', $in);
                }
                $this->db->insert('tblpurchase_to_order', array('id_purchases' => $vch, 'id_purchases_order' => $id));
            }
            $price_expected = 0;
            $price_suppliers = 0;
            $data['discount_percent_expected'] = 0;
            $data['discount_percent_suppliers'] = 0;
            $sub_expected = 0;
            $price_expected = $total_expected_all - $sub_expected;
            $sub_suppliers = 0;
            $price_suppliers = $total_suppliers_all - $sub_suppliers + $delivery_cost - $reduce_cost;
            $total_dqd = $price_suppliers * $amount_to_vnd;
            $total_novat_dqd = $total_novat * $amount_to_vnd;
            $plan_id_text = trim($plan_id_text, ',');
            $_items = array(
                'valtype_check_expected' => 0,
                'valtype_check_suppliers' => 0,
                'discount_percent_expected' => 0,
                'discount_percent_suppliers' => 0,
                'totalAll_expected' => $total_expected_all,
                'totalAll_suppliers' => $price_suppliers,
                'price_expected' => $price_expected,
                'price_suppliers' => $price_suppliers,
                'total_cqd' => $price_suppliers,
                'total_novat' => $total_novat,
                'promotion_expected' => $promotion_expecteds,
                'total_dqd' => $price_suppliers * $amount_to_vnd,
                'plan_id' => $plan_id_text,
            );
            $this->db->update('tblpurchase_order', $_items, array('id' => $id));
        }
        return $listArrayPurchase;
    }

    public function createTaskAuto($id = '')
    {
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        $name = '';
        if (!empty($internal_proposal)) {
            if (!empty($internal_proposal->category_tasks)) {
                $this->db->where('id', $internal_proposal->category_tasks);
                $category_tasks = $this->db->get('tblcategory_tasks')->row();
                $staff_department = !empty($category_tasks) ? $category_tasks->departments : null;
                $name = !empty($category_tasks) ? $category_tasks->content : null;
            }
            $duedate = null;
            if ($internal_proposal->category_recommended_id == 42) {
                if (!empty($internal_proposal->suggest_id)) {
                    $this->db->where('id', $internal_proposal->suggest_id);
                    $suggest_plan_purchase = $this->db->get('tbl_suggest_plan_purchase')->row_array();
                    if (!empty($suggest_plan_purchase)) {
                        $duedate = $suggest_plan_purchase['time_finish'];
                    }
                }
            }
            if ($internal_proposal->category_recommended_id == 29) {
                if (!empty($internal_proposal->suggest_id)) {
                    $this->db->where('id', $internal_proposal->suggest_id);
                    $suggest_plan_evaluate = $this->db->get('tbl_suggest_plan_evaluate')->row_array();
                    if (!empty($suggest_plan_evaluate)) {
                        $duedate = $suggest_plan_evaluate['time_finish'];
                    }
                }
            }
            if ($internal_proposal->category_recommended_id == 30) {
                if (!empty($internal_proposal->suggest_id)) {
                    $this->db->where('id', $internal_proposal->suggest_id);
                    $suggest_plan_overtime = $this->db->get('tbl_suggest_plan_overtime')->row_array();
                    if (!empty($suggest_plan_overtime)) {
                        $duedate = $suggest_plan_overtime['time_finish'];
                    }
                }
            }
            if ($internal_proposal->category_recommended_id == 33) {
                if (!empty($internal_proposal->suggest_id)) {
                    $this->db->where('id', $internal_proposal->suggest_id);
                    $suggest_plan_educate = $this->db->get('tbl_suggest_plan_educate')->row_array();
                    if (!empty($suggest_plan_educate)) {
                        $duedate = $suggest_plan_educate['time_finish'];
                    }
                }
            }
            if ($internal_proposal->category_recommended_id == 32) {
                if (!empty($internal_proposal->suggest_id)) {
                    $this->db->where('id', $internal_proposal->suggest_id);
                    $suggest_plan_recruitment = $this->db->get('tbl_suggest_plan_recruitment')->row_array();
                    if (!empty($suggest_plan_recruitment)) {
                        $duedate = $suggest_plan_recruitment['time_finish'];
                    }
                }
            }
            $_data = [
                'name' => $name,
                'hourly_rate' => 0,
                'category_tasks' => $internal_proposal->category_tasks,
                'startdate' => $internal_proposal->date,
                'duedate' => $duedate,
                'priority' => 2,
                'rel_type' => 'internal_proposal',
                'rel_id' => $id,
                'description' => $internal_proposal->content,
                'department_id' => !empty($staff_department) ? explode(',', $staff_department) : [],
                'id_branch' => $internal_proposal->id_branch,
            ];
            $id_tasks = $this->tasks_model->add_Internal_proposal($_data, false, true);
            if (!empty($id_tasks)) {
                $task_followers = [];

                $this->db->select('tbl_internal_proposal_process.*,tbl_internal_proposal_process_child.id as childs');
                $this->db->where('tbl_internal_proposal_process.id_internal_proposal', $internal_proposal->id);
                $this->db->join('tbl_internal_proposal_process_child', 'tbl_internal_proposal_process_child.recommended_list_id = tbl_internal_proposal_process.id_process AND tbl_internal_proposal_process_child.id_internal_proposal = tbl_internal_proposal_process.id_internal_proposal', 'left');
                $this->db->order_by('tbl_internal_proposal_process.id_process asc');
                $this->db->group_by('tbl_internal_proposal_process.id');
                $data_checklist_items = $this->db->get('tbl_internal_proposal_process')->result_array();
                foreach ($data_checklist_items as $kk => $vv) {
                    if ($vv['bod'] == 2) {
                        $staffNow = get_staff_user_id();
                        $this->db->where('id_internal_proposal', $internal_proposal->id);
                        $this->db->where('id_staff != "' . $staffNow . '"', false, false);
                        $internal_assigned = $this->db->get('tblinternal_proposal_assigned')->result_array();
                        if (!empty($internal_assigned)) {
                            foreach ($internal_assigned as $key => $value) {
                                $task_followers[] = $value['id_staff'];
                                // $this->db->insert('tbltask_followers', [
                                //     'staffid' => $value['id_staff'],
                                //     'taskid' => $id_tasks
                                // ]);
                            }
                        }
                    }
                    if ($vv['bod'] == 6) {
                        if (!empty($internal_proposal->manager_id)) {
                            $task_followers[] = $internal_proposal->manager_id;
                        }
                    }
                    if ($vv['bod'] == 4) {
                        if (!empty($internal_proposal->auditor_id)) {
                            $task_followers[] = $internal_proposal->auditor_id;
                        }
                    }
                    if ($vv['bod'] == 8) {
                        if (!empty($internal_proposal->staff_controller_completes)) {
                            $task_followers[] = $internal_proposal->staff_controller_completes;
                        }
                    }
                    if ($vv['bod'] == 9) {
                        if (!empty($internal_proposal->staff_auditor_completes)) {
                            $task_followers[] = $internal_proposal->staff_auditor_completes;
                        }
                    }
                    if ($vv['bod'] == 5) {
                        if (!empty($internal_proposal->staff)) {
                            $task_followers[] = $internal_proposal->staff;
                        }
                    }
                    if ($vv['bod'] == 1) {
                        // $this->db->where('id_internal_proposal', $internal_proposal->id);
                        // $assigned_pod = $this->db->get('tblinternal_proposal_staff_pod')->result_array();
                        // if (!empty($assigned_pod)) {
                        //     foreach ($assigned_pod as $key => $value) {
                        //         $task_followers[] = $value['id_staff'];
                        //     }
                        // }
                    }
                    if ($vv['bod'] == 7) {
                        $this->db->where('id_internal_proposal', $internal_proposal->id);
                        $monitor_pod = $this->db->get('tblinternal_proposal_monitor')->result_array();
                        if (!empty($monitor_pod)) {
                            foreach ($monitor_pod as $key => $value) {
                                $task_followers[] = $value['id_staff'];
                            }
                        }
                    }
                    if ($vv['bod'] == 3) {
                        $this->db->where('id_internal_proposal', $internal_proposal->id);
                        $proposal_head_of_department_pod = $this->db->get('tblinternal_proposal_head_of_department')->result_array();
                        if (!empty($proposal_head_of_department_pod)) {
                            foreach ($proposal_head_of_department_pod as $key => $value) {
                                $task_followers[] = $value['id_staff'];
                            }
                        }
                    }
                }
                $task_followers = array_unique($task_followers);
                $task_followers = array_values($task_followers);
                if (!empty($task_followers)) {
                    foreach ($task_followers as $key => $value) {
                        $this->db->insert('tbltask_assigned', [
                            'staffid' => $value,
                            'taskid' => $id_tasks,
                            'assigned_from' => get_staff_user_id(),
                        ]);
                    }
                }

                $this->db->from('tbl_internal_proposal_process');
                $this->db->where('tbl_internal_proposal_process.id_internal_proposal', $internal_proposal->id);
                $this->db->where('tbl_internal_proposal_process.bod', 1);
                $this->db->where('tbl_internal_proposal_process.status', 1);
                $internal_proposal_process = $this->db->get()->row_array();
                if (!empty($internal_proposal_process['staff_id'])) {
                    $this->db->insert('tbltask_followers', [
                        'staffid' => $internal_proposal_process['staff_id'],
                        'taskid' => $id_tasks,
                    ]);
                }
                $this->db->select('name,visible_to_client,rel_id,rel_type');
                $this->db->where('id', $id_tasks);
                $task = $this->db->get(db_prefix() . 'tasks')->row();
                if (!empty($task_followers)) {
                    foreach ($task_followers as $key => $value) {
                        $member = $this->staff_model->get($value);
                        @send_mail_task_assignees($member->email, $id_tasks);
                    }
                }
                return $id_tasks;
            }
        }
        return false;
    }

    //	public function create_tasks($id = '') {
    //		if(!has_permission('tasks', '', 'create')) {
    //			ajax_access_denied();
    //		}
    //
    //		$data = [];
    //
    //		$this->db->where('id', $id);
    //		$internal_proposal = $this->db->get('tblinternal_proposal')->row();
    //		$_GET['rel_id'] = $internal_proposal->id;
    //		$_GET['rel_type'] = 'internal_proposal';
    //
    //		if (!has_permission('tasks', '', 'edit') && !has_permission('tasks', '', 'create')) {
    //			ajax_access_denied();
    //		}
    //		$title = _l('add_new', _l('task_lowercase'));
    //		$data['id'] = '';
    //
    //		$data['title'] = $title;
    //		$data['project_end_date_attrs'] = [];
    //		$data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
    //		$data['departments'] = $this->db->get('tbldepartments')->result_array();
    //		if(!is_admin()) {
    //			$staffNow = get_staff_user_id();
    //			$this->db->select('GROUP_CONCAT(departmentid) as list_departments');
    //			$this->db->where('tblstaff_departments.staffid', $staffNow);
    //			$staff_departments = $this->db->get('tblstaff_departments')->row('list_departments');
    //			if(!empty($staff_departments)) {
    //				$staff_departments = explode(',', $staff_departments);
    //				$this->db->group_start();
    //				foreach ($staff_departments as $key => $value) {
    //					$this->db->or_where('FIND_IN_SET('.$value.', tblcategory_tasks.departments)');
    //				}
    //				$this->db->group_end();
    //			}
    //			else {
    //				$this->db->where('id', 0);
    //			}
    //		}
    //		$data['category_tasks'] = $this->db->get('tblcategory_tasks')->result_array();
    //		$data['departments_tasks'] = $this->db->get('tbldepartments_tasks')->result_array();
    //
    //
    //
    //		$this->load->view('admin/tasks/task', $data);
    //	}
    public function modal_create_suggestion($id = '')
    {
        if (!has_permission('suggestion', '', 'create')) {
            echo '<script>alert_float("danger", "Bạn không có quyền tạo phiếu đề xuất tài chính")</script>';
            die();
        }
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        if ($internal_proposal->money == 0) {
            echo '<script>alert_float("danger", "Phiếu đề xuất nội bộ không có chi phí không thể tạo phiếu đề xuất tài chính")</script>';
            die();
        }
        if ($internal_proposal->status == 1) {
            if (!empty($internal_proposal->id_suggestion)) {
                echo '<script>alert_float("danger", "Phiếu đề xuất nội bộ đã tồn tại phiếu đề xuất tài chính nên không thể tạo!")</script>';
                die();
            }
        }
        $data['id'] = $id;
        $data['title'] = 'Chọn thông tin phiếu đề xuất tài chính';
        $data['payment_modes'] = $this->db->get('tblpayment_modes')->result_array();
        $data['staff_browse'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();
        $this->load->view('admin/internal_proposal/modal_create_suggestion', $data);
    }

    public function create_suggestion($id = '')
    {
        if (!has_permission('suggestion', '', 'create')) {
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => 'Bạn không có quyền tạo phiếu đề xuất tài chính'
            ]);
            die();
            //			echo '<script>alert_float("danger", "Bạn không có quyền tạo phiếu đề xuất tài chính")</script>';die();
        }
        $id_payment_modes = $this->input->post('id_payment_modes');
        if (empty($id_payment_modes)) {
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => 'Vui lòng chọn phương thức thanh toán'
            ]);
            die();
        }
        $staff_browse = $this->input->post('staff_browse');
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        if (!empty($internal_proposal)) {
            if ($internal_proposal->money == 0) {
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => 'Phiếu đề xuất nội bộ không có chi phí không thể tạo phiếu đề xuất tài chính'
                ]);
                die();
                //				echo '<script>alert_float("danger", "Phiếu đề xuất nội bộ không có chi phí không thể tạo phiếu đề xuất tài chính")</script>';die();
            }
            if ($internal_proposal->status == 1) {
                if (!empty($internal_proposal->id_suggestion)) {
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => 'Phiếu đề xuất nội bộ đã tồn tại phiếu đề xuất tài chính nên không thể tạo!'
                    ]);
                    die();
                    //					echo '<script>alert_float("danger", "Phiếu đề xuất nội bộ đã tồn tại phiếu đề xuất tài chính nên không thể tạo!")</script>';die();
                }
                $checks = get_table_where('tblsuggestion', array('id_internal_proposal' => $id));
                if (!empty($checks)) {
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => 'Phiếu đề xuất nội bộ đã tồn tại phiếu đề xuất tài chính nên không thể tạo!'
                    ]);
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
                $ins['id_payment_modes'] = $id_payment_modes;
                $ins['staff_browse'] = !empty($staff_browse) ? $staff_browse : null;
                $this->db->insert('tblsuggestion', $ins);
                $id_suggestion = $this->db->insert_id();
                if (!empty($id_suggestion)) {
                    $this->db->where('id', $id);
                    $this->db->update('tblinternal_proposal', ['id_suggestion' => $id_suggestion]);
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => 'Tạo phiếu đề xuất tài chính thành công'
                    ]);
                    die();
                    //					echo '<script>
                    //								alert_float("success", "Tạo phiếu đề xuất tài chính thành công");
                    //                                oTable.draw("page");
                    //						</script>';die();
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => 'Phiếu chưa duyệt chưa thể tạo phiếu đề xuất tài chính'
                ]);
                die();
                //				echo '<script>alert_float("danger", "Phiếu chưa duyệt chưa thể tạo phiếu đề xuất tài chính")</script>';die();
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => 'Không tìm thấy phiếu đề xuất nội bộ'
        ]);
        die();
        //		echo '<script>alert_float("danger", "Không tìm thấy phiếu đề xuất nội bộ")</script>';die();
    }

    public function getStaffWhere()
    {
        $StringWhere = [];
        foreach ($this->code_departments as $key => $value) {
            $StringWhere[] = 'tbldepartments.code = "' . $value . '"';
        }
        $staffDepartments = "(
			SELECT
				tblstaff_departments.staffid as staffid,
				GROUP_CONCAT(tbldepartments.name) as name_department 
			FROM tblstaff_departments
			INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
			WHERE tbldepartments.departmentid != 0 " . (!empty($StringWhere) ? ('AND (' . implode(
                ' OR ',
                $StringWhere
            ) . ')') : '') . "
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

    public function get_recommended_list_excel_config()
    {
        $id = $this->input->get('id');
        $this->db->select('is_excel, excel');
        $this->db->where('id', $id);
        $res = $this->db->get('tbl_recommended_list')->row_array();
        echo json_encode($res ? $res : ['is_excel' => 0, 'excel' => '']);
        die();
    }

    public function validate_excel_proposal()
    {
        $recommended_list_id = $this->input->post('recommended_list_id');
        $excel_headers = [];
        if (!empty($recommended_list_id)) {
            $this->db->select('is_excel, excel');
            $this->db->where('id', $recommended_list_id);
            $recom_item = $this->db->get('tbl_recommended_list')->row();
            if (!empty($recom_item) && $recom_item->is_excel == 1) {
                $excel_headers = json_decode($recom_item->excel, true);
            }
        }

        if (empty($_FILES['excel_proposal_file']['tmp_name'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng chọn tệp Excel dữ liệu'
            ]);
            die();
        }

        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $fullfile = $_FILES['excel_proposal_file']['tmp_name'];
        $inputFileType = PHPExcel_IOFactory::identify($fullfile);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load("$fullfile");
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $highestRow = $objWorksheet->getHighestRow();
        
        $headerRow = 1;
        $max_score = 0;
        for ($r = 1; $r <= min(15, $highestRow); $r++) {
            $score = 0;
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $cellValue = trim($objWorksheet->getCellByColumnAndRow($col, $r)->getValue());
                if ($cellValue !== '' && is_array($excel_headers) && in_array($cellValue, $excel_headers)) {
                    $score++;
                }
            }
            if ($score > $max_score) {
                $max_score = $score;
                $headerRow = $r;
            }
        }
        
        $col_map = [];
        $found_headers = [];
        for ($col = 0; $col < $highestColumnIndex; $col++) {
            $cellValue = trim($objWorksheet->getCellByColumnAndRow($col, $headerRow)->getValue());
            if ($cellValue !== '' && is_array($excel_headers) && in_array($cellValue, $excel_headers)) {
                $col_map[$col] = $cellValue;
                $found_headers[] = $cellValue;
            }
        }

        $missing_headers = [];
        if (is_array($excel_headers)) {
            foreach ($excel_headers as $h) {
                if (!in_array($h, $found_headers)) {
                    $missing_headers[] = $h;
                }
            }
        }

        if (!empty($missing_headers)) {
            echo json_encode([
                'success' => false,
                'message' => 'Tệp Excel thiếu các cột tiêu đề bắt buộc: ' . implode(', ', $missing_headers)
            ]);
            die();
        }
        
        $rows = [];
        for ($r = $headerRow + 1; $r <= $highestRow; $r++) {
            $row_data = [];
            $has_value = false;
            if (is_array($excel_headers)) {
                foreach ($excel_headers as $h) {
                    $row_data[$h] = '';
                }
            }
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                if (isset($col_map[$col])) {
                    $cellVal = $objWorksheet->getCellByColumnAndRow($col, $r)->getValue();
                    $cellVal = ($cellVal !== null) ? trim($cellVal) : '';
                    $row_data[$col_map[$col]] = $cellVal;
                    if ($cellVal !== '') {
                        $has_value = true;
                    }
                }
            }
            if ($has_value) {
                $rows[] = $row_data;
            }
        }
        
        echo json_encode([
            'success' => true,
            'rows' => $rows
        ]);
        die();
    }

    public function add_modal($id = '')
    {
        $data['tbody'] = '';
        $data['title'] = _l('internal_proposal_add');
        $data['staff_list'] = $this->getStaffWhere();
        $data['staff_list_all'] = $this->site_model->getStaffAll();
        //		$data['proposal_type'] = $this->suggestion_type_model->get();
        if (!empty($id)) {
            if (!$this->perEdit) {
                ajax_access_denied();
            }
            $internal_proposal_purchase = get_table_where(
                'tblinternal_proposal_purchase',
                array('id_internal_proposal' => $id)
            );
            $data['internal_proposal_purchase'] = array();
            foreach ($internal_proposal_purchase as $key => $value) {
                $data['internal_proposal_purchase'][] = $value['id_purchases'];
            }
            $data['title'] = 'Sửa phiếu đề xuất nội bộ';
            $data['object'] = get_table_where('tblinternal_proposal', array('id' => $id), '', 'row');
            $data['object']->staff_bod = $this->db->select('GROUP_CONCAT(id_staff) as list_staff')->get_where(
                'tblinternal_proposal_staff_pod',
                [
                    'id_internal_proposal' => $id
                ]
            )->row('list_staff');
            $data['object']->staff_bod = !empty($data['object']->staff_bod) ? explode(
                ',',
                $data['object']->staff_bod
            ) : [];

            $data['object']->staff_assigned = $this->db->select('GROUP_CONCAT(id_staff) as list_staff')->get_where(
                'tblinternal_proposal_assigned',
                [
                    'id_internal_proposal' => $id
                ]
            )->row('list_staff');
            $data['object']->staff_assigned = !empty($data['object']->staff_assigned) ? explode(
                ',',
                $data['object']->staff_assigned
            ) : [];

            $data['object']->monitor_id = $this->db->select('GROUP_CONCAT(id_staff) as list_staff')->get_where(
                'tblinternal_proposal_monitor',
                [
                    'id_internal_proposal' => $id
                ]
            )->row('list_staff');
            $data['object']->monitor_id = !empty($data['object']->monitor_id) ? explode(
                ',',
                $data['object']->monitor_id
            ) : [];

            $data['object']->head_of_department_id = $this->db->select('GROUP_CONCAT(id_staff) as list_staff')->get_where(
                'tblinternal_proposal_head_of_department',
                [
                    'id_internal_proposal' => $id
                ]
            )->row('list_staff');
            $data['object']->head_of_department_id = !empty($data['object']->head_of_department_id) ? explode(
                ',',
                $data['object']->head_of_department_id
            ) : [];

            $data['object']->recommended_list = get_table_where('tblinternal_proposal_recommended', ['id_internal_proposal' => $id]);
            $data['object']->type_bonus = get_table_where('tbl_recommended_list', ['id' => $data['object']->recommended_list_group_id], '', 'row_array')['type_bonus'];
            $items = $this->purchases_model->get_items_internal_proposal($id);
            $i = 0;
            $tbody = '';
            $tax = get_table_where('tbltaxes');
            foreach ($items as $key => $value) {
                $purchase_items = get_table_where(
                    'tblpurchases_items',
                    array('id' => $value['id_purchases_items']),
                    '',
                    'row'
                );
                $purchase = get_table_where('tblpurchases', array('id' => $value['id_purchases']), '', 'row');
                $supplier = get_table_where('tblsuppliers', array('id' => $value['suppliers_id']), '', 'row');
                $text = '';
                if (!empty($purchase)) {
                    $text = '<br><span class="label label-danger pull-left mtop5 text-center">' . $purchase->prefix . $purchase->code . '</span>';
                }
                $unit_name = $value['unit_name'];
                if ($value['unit_name'] == null) {
                    $unit_name = '';
                }
                $unit_name_payment = $value['unit_name_payment'];
                if ($value['unit_name_payment'] == null) {
                    $unit_name_payment = '';
                }
                $unit_name_stock = $value['unit_name_stock'];
                if ($value['unit_name_stock'] == null) {
                    $unit_name_stock = '';
                }
                $mainQuantity_suppliers = $value['quantity'];
                $exchange_stock = $value['exchange_standard_unit'];
                $exchange_standard_unit = $value['exchange_unit'];
                $exchange_payment = $value['exchange_unit_payment'];
                $recipe = $value['recipe'];
                $paper = $value['paper'];
                $longs = $value['longs'];
                $wide = $value['wide'];
                $quantity_stock = ($mainQuantity_suppliers / $exchange_stock) * $exchange_standard_unit;
                if ($recipe == 1) {
                    $quantity_payment = (($mainQuantity_suppliers / $exchange_payment) * $exchange_standard_unit);
                } elseif ($recipe == 2) {
                    $quantity_payment = (($mainQuantity_suppliers / $exchange_payment) * $paper / 100);
                } elseif ($recipe == 3) {
                    $quantity_payment = (($mainQuantity_suppliers / $exchange_payment) * ($longs * $wide) / 10000);
                }
                $tbody .= '<tr>';
                $tbody .= '<td>
				<input type="hidden" class="id_purchases" id="id_purchases" name="items[' . $i . '][id_purchases]" value="' . $value['id_purchases'] . '" />
				<input type="hidden" class="id" id="id" name="items[' . $i . '][id]" value="' . $value['id_purchases_items'] . '" />
				<input type="hidden" class="id_items" id="id_items" name="items[' . $i . '][id_items]" value="' . $value['id_items'] . '" />
				<input type="hidden" class="type" id="type" name="items[' . $i . '][type]" value="' . $value['type'] . '" />
				<input type="hidden" class="recipe" id="recipe" name="items[' . $i . '][recipe]" value="' . $value['recipe'] . '" />
				<input type="hidden" class="paper" id="paper" name="items[' . $i . '][paper]" value="' . $value['paper'] . '" />
				<input type="hidden" class="longs" id="longs" name="items[' . $i . '][longs]" value="' . $value['longs'] . '" />
				<input type="hidden" class="wide" id="wide" name="items[' . $i . '][wide]" value="' . $value['wide'] . '" />
				' . $value['name_item'] . ' (' . $value['code_item'] . ')' . $text . '
				</td>';
                $tbody .= '<td class="text-center sldx">' . formatNumber($purchase_items->quantity_net) . '</td>';
                $tbody .= '<td><input style="width: 60px" onchange="formatNumBerKeyUpCus(this)" class="height_auto H_input mainQuantity_suppliers" type="text" name="items[' . $i . '][quantity_suppliers]" value="' . formatNumber($value['quantity']) . '" /><input style="width: 100px"  class="hide height_auto H_input exchange_standard_unit" type="text" name="items[' . $i . '][exchange_standard_unit]" value="' . $value['exchange_unit'] . '" /><span class="unit_name">/' . $unit_name . '</span></td>';
                $tbody .= '<td class="text-center"><span class="text_mainquantity_stock text-center">' . formatNumber($quantity_stock) . '</span><span class="unit_name_stock">/' . $unit_name_stock . '</span><input style="width: 100px"  class="hide height_auto H_input mainquantity_stock" type="text" name="items[' . $i . '][quantity_stock]" value="' . $quantity_stock . '" /><input style="width: 100px"  class=" hide height_auto H_input exchange_stock" type="text" name="items[' . $i . '][exchange_stock]" value="' . $value['exchange_standard_unit'] . '" /></td>';
                $tbody .= '<td class="text-center"><span class="text_mainquantity_payment">' . formatNumber($quantity_payment) . '</span><span class="unit_name_payment">/' . $unit_name_payment . '</span><input style="width: 100px"  class="hide height_auto H_input mainquantity_payment" type="text" name="items[' . $i . '][quantity_payment]" value="' . $quantity_payment . '" /><input style="width: 100px"  class="hide height_auto H_input exchange_payment" type="text" name="items[' . $i . '][exchange_payment]" value="' . $value['exchange_unit_payment'] . '" /></td>';
                $tbody .= '<td ><input style="width: 100%" readonly onchange="formatNumBerKeyUpCus(this)" class="no-drop height_auto H_input align_right price_suppliers"  type="text" name="items[' . $i . '][price_suppliers]" id="price_suppliers_' . $i . '" value="' . formatNumber($value['price']) . '" /></td>';
                $option = '';
                foreach ($tax as $t) {
                    $option .= '<option ' . ($t['id'] == $value['tax_id'] ? 'selected' : '') . ' value="' . $t['id'] . '" data-taxrate="' . $t['taxrate'] . '"> ' . $t['name'] . ' </option>';
                }
                $tbody .= '<td class="text-center">
					<select class="selectpicker tax" name="items[' . $i . '][tax_id]" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '">
					<option value data-taxrate="0">' . _l('no_tax') . '</option>
					' . $option . '
				</select>
				<input type="hidden" class="tax_rate" name="items[' . $i . '][tax_rate]" value="' . $value['tax_rate'] . '">
				</td>';
                $tbody .= '<td class="total_suppliers text-right">0</td>';
                $tbody .= '<td style="width:150px"><input type="hidden" class="count" value="' . $i . '" /><input style="width:200px" data-placeholder="' . _l('dropdown_non_selected_tex') . '" data-id_supp="' . $supplier->id . '"  data-company_supp="' . $supplier->company . '"  required="true" value="' . $value['suppliers_id'] . '" class="suppliers_id" id="suppliers_id_' . $i . '" name="items[' . $i . '][suppliers_id]"  style="width: 100%"></td>';
                $tbody .= '<td>' . $purchase_items->note . '</td>';
                $tbody .= '</tr>';
                $i++;
            }
            $data['tbody'] = $tbody;
            $data['id'] = $id;
        } else {
            if (!$this->perAdd) {
                ajax_access_denied();
            }
        }
        if (empty($data['object'])) {
            $data['object'] = (object) array();
            $data['object']->id = '';
            $data['object']->code = $this->internal_proposal_model->getCode();
            $data['object']->date = (date('d/m/Y H:i:s'));
            $data['object']->date_finish = '';
            $data['object']->staff = get_staff_user_id();
            $data['object']->proposal_type = '';
            $data['object']->money = 0;
            $data['object']->content = '';
        } else {
            // $data['object']->date = _d(substr($data['object']->date, 0, -9));
            $data['object']->date = _dt(($data['object']->date));
            $data['object']->date_finish = _dt(($data['object']->date_finish));
        }
        $data['object']->department = $this->site_model->getStaffByStaffId($data['object']->staff)['name_department'];
        //		if (empty($data['object']->category_tasks)) {
        //			$this->db->group_start();
        //			$this->db->like('content', 'đề xuất');
        //			$this->db->or_like('code', 'đề xuất');
        //			$this->db->or_where('code like "DX%"', false, false);
        //			$this->db->or_where('code like "ĐX%"', false, false);
        //			$this->db->group_end();
        //		}

        $this->db->group_start();
        $this->db->where('tblcategory_tasks.hide', 0);
        if (!empty($data['object']->category_tasks)) {
            $this->db->or_where_in('tblcategory_tasks.id', $data['object']->category_tasks);
        }
        $this->db->group_end();
        $data['category_tasks'] = $this->db->get('tblcategory_tasks')->result_array();

        $this->db->select('id, CONCAT(prefix, "-", code) as fullcode, total');
        $this->db->where('NOT EXISTS (
							SELECT 1 
							FROM tblinternal_proposal 
							WHERE tblinternal_proposal.id_other_payslips = tblother_payslips.id
							AND tblinternal_proposal.id != "' . $id . '"
						)');
        $this->db->where('is_advance', 0);
        $data['other_payslips'] = $this->db->get('tblother_payslips')->result_array();
        $data['key_departments'] = [];
        $data['departments'] = $this->db->get('tbldepartments')->result_array();
        foreach ($data['departments'] as $key => $value) {
            $data['key_departments'][$value['departmentid']] = $value['name'];
        }
        $data['type_object'] = $this->type_object;
        // $this->db->where('NOT EXISTS(
        // 	SELECT 1
        // 	FROM  tblpurchase_order tb_tamp
        // 	WHERE tb_tamp.id_purchases = tblpurchases.id
        // )');
        $this->db->where('tblpurchases.status >', 0);
        $this->db->where('tblpurchases.status <', 4);
        $data['purchases'] = $this->db->get('tblpurchases')->result_array();
        $this->db->select('tblpurchase_order.*, CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as fullcode, tblsuppliers.company as company');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tblpurchase_order.suppliers_id', 'left');
        $data['purchase_order'] = $this->db->get('tblpurchase_order')->result_array();
        $data['services'] = $this->db->get('tbl_services')->result_array();
        $data['branch'] = $this->db->get('tblbranch')->result_array();
        $data['type_plan_propose'] = $this->type_plan_propose;
        $data['staffs'] = $this->getStaffWhereRole();
        //hauhauhauhau
        if (empty($id)) {
            $id = 0;
        }
        $this->db->select('tbl_decision_bonus_discipline.*,
            tbl_quota_bonus_discipline.name as name_quota,
            tbl_precious.name as name_quy,
        ');
        $this->db->from('tbl_decision_bonus_discipline');
        $this->db->join('tbl_suggest_bonus_disciplines', 'tbl_suggest_bonus_disciplines.id = tbl_decision_bonus_discipline.suggest_bonus_discipline_id');
        $this->db->join('tbl_quota_bonus_discipline', 'tbl_quota_bonus_discipline.id = tbl_suggest_bonus_disciplines.quota_bonus_disciplines_id', 'inner');
        $this->db->join('tbl_precious', 'tbl_precious.id = tbl_suggest_bonus_disciplines.precious_id', 'inner');
        $this->db->where('tbl_decision_bonus_discipline.status', 1);
        $this->db->where('NOT EXISTS (
            SELECT 1
            FROM tblinternal_proposal
            WHERE tblinternal_proposal.decision_bonus_discipline_id = tbl_decision_bonus_discipline.id AND tblinternal_proposal.id != ' . $id . '
        )');
        $dtDecision = $this->db->get()->result_array();
        $data['dtDecision'] = $dtDecision;


        $dtInteral = get_table_where('tblinternal_proposal', ['id' => $id], '', 'row_array');

        $result = [];
        if (!empty($dtInteral)) {
            $this->db->select('tbl_category_recommended.id as id,tbl_category_recommended.name_table, ballot_type, type', false);
            $this->db->from('tbl_category_recommended');
            $this->db->where('tbl_category_recommended.id', $dtInteral['category_recommended_id']);
            $rs = $this->db->get()->row_array();
            if (!empty($rs)) {

                if ($rs['name_table'] == 'tblsuggest_test_item_quality') {
                    $this->db->select($rs['name_table'] . '.*');
                    if (!empty($rs['type'])) {
                        $this->db->where($rs['name_table'] . '.type', $rs['type']);
                    }
                    if (!empty($rs['ballot_type'])) {
                        if ($rs['ballot_type'] == 2) {
                            $this->db->select($rs['name_table'] . '.code_evaluate as code');
                            $this->db->where($rs['name_table'] . '.status', 1);
                        }
                    }
                    $this->db->from($rs['name_table']);
                    $result = $this->db->get()->result_array();
                } else if ($rs['id'] == CR_SUGGEST_PAYSLIPS_ID) {
                    //custom
                    $this->db->select($rs['name_table'] . '.*');
                    $this->db->from($rs['name_table']);
                    $this->db->where($rs['name_table'] . '.status', 1);
                    $this->db->group_start();
                    $this->db->where(' NOT EXISTS (
                        SELECT 1
                        FROM tblinternal_proposal
                        WHERE tblinternal_proposal.category_recommended_id = ' . CR_SUGGEST_PAYSLIPS_ID . ' AND ' . $rs['name_table'] . '.id = tblinternal_proposal.suggest_id
                    )', false, false);
                    $this->db->or_where($rs['name_table'] . '.id', $dtInteral['suggest_id']);
                    $this->db->group_end();
                    $result = $this->db->get()->result_array();
                } else {
                    if ($rs['name_table'] == 'tbl_evaluate') {
                        $type_evaluate = $this->db->get_where("tbl_evaluate", ['id' => $dtInteral['suggest_id']])->row('type');
                        if (!empty($type_evaluate)) {
                            $this->db->where('type', $type_evaluate);
                        }

                        $this->db->select('code_evaluate as reference_no, id, created_by as staff_suggest');
                    } else if ($rs['name_table'] == 'tbl_regulations') {
                        $type_regulation = $this->db->get_where("tbl_regulations", ['id' => $dtInteral['suggest_id']])->row('type');
                        if (!empty($type_regulation)) {
                            $this->db->where('type', $type_regulation);
                        }
                        $this->db->select('code as reference_no, id, create_by as staff_suggest');
                    } else if ($rs['name_table'] == 'tbldecision') {
                        $this->db->select('code as reference_no, id, create_by as staff_suggest');
                    }
                    $this->db->select($rs['name_table'] . '.*');
                    $this->db->from($rs['name_table']);
                    $result = $this->db->get()->result_array();
                }
            }
            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    $reference_no = '';
                    if (!empty($value['reference_no'])) {
                        $reference_no = $value['reference_no'];
                    } elseif (!empty($value['code'])) {
                        $reference_no = $value['code'];
                    }
                    $staff_suggest_name = "";
                    if (!empty($value['staff_suggest'])) {
                        $staff_suggest_name = get_staff_full_name($value['staff_suggest']);
                    }
                    $result[$key]['reference_no'] = $reference_no;
                    $result[$key]['staff_suggest_name'] = $staff_suggest_name;
                }
            }

            $dtCategoryRecom = get_table_where('tbl_category_recommended', ['id' => $dtInteral['category_recommended_id']], '', 'row_array');
            if (!empty($dtCategoryRecom)) {
                if ($dtCategoryRecom['type_kpi'] == 1) {
                    $dtItems = [];
                    $this->db->select('tbl_suggest_kpi.*,tblroles.name as name_role');
                    $this->db->from('tbl_suggest_kpi');
                    $this->db->from('tblroles', 'tblroles.roleid = tbl_suggest_kpi.role_id');
                    $this->db->where('tbl_suggest_kpi.id', $dtInteral['suggest_id']);
                    $dtData = $this->db->get()->row_array();

                    $this->db->select('
                        tbl_suggest_kpi_item.*,
                        tbl_category_kpi.name as name_category,
                        tbl_category_kpi_criteria.type as type,
                        tbl_category_kpi_criteria.name as name_kpi,
                        tbl_category_kpi_criteria.measure as measure,
                        tbl_category_kpi_criteria.code as code_kpi,
                        tbl_category_kpi_criteria.time as time,
                        tbl_suggest_kpi_item.weight as weight,
                        tbl_detail_task_detail.regulations as regulations,
                        0 as report,
                        tbl_result.name as name_result,
                    ');
                    $this->db->from('tbl_suggest_kpi_item');
                    $this->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tbl_suggest_kpi_item.category_kpi_id');
                    $this->db->join('tbl_category_kpi_criteria', 'tbl_category_kpi_criteria.id = tbl_suggest_kpi_item.category_kpi_criteria_id');
                    $this->db->join('tbl_detail_task_detail', 'tbl_detail_task_detail.category_kpi_criteria_id = tbl_category_kpi_criteria.id', 'left');
                    $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_kpi_item.result_id', 'left');
                    $this->db->where('tbl_suggest_kpi_item.suggest_kpi_id', $dtInteral['suggest_id']);
                    $dtItems = $this->db->get()->result_array();
                    $month = $dtData['month'];
                    $year = $dtData['year'];
                    $staff_suggest = $dtData['staff_suggest'];
                    if (!empty($dtItems)) {
                        foreach ($dtItems as $key => $value) {
                            $category_kpi_criteria_id = $value['category_kpi_criteria_id'];
                            $tb_tamp = "(
                            SELECT
                                COUNT(tbl_production_report_kpi.category_kpi_criteria_id) as total
                            FROM tblproduction_report
                            JOIN tbl_production_report_kpi ON tbl_production_report_kpi.production_report_id = tblproduction_report.id
                            WHERE DATE_FORMAT(tblproduction_report.date, '%m') = $month AND DATE_FORMAT(tblproduction_report.date, '%Y') = $year
                            AND tblproduction_report.staff_responsible = $staff_suggest AND tbl_production_report_kpi.category_kpi_criteria_id = $category_kpi_criteria_id
                        )";
                            $dtProductionKpi = $this->db->query($tb_tamp)->row_array();
                            $dtItems[$key]['report'] = !empty($dtProductionKpi['total']) ? $dtProductionKpi['total'] : 0;
                        }
                    }
                    $body1 = '';
                    $body2 = '';
                    $stt1 = 0;
                    $stt2 = 0;
                    $total_weight1 = 0;
                    $total_weight2 = 0;
                    if (!empty($dtItems)) {
                        foreach ($dtItems as $key => $value) {
                            if ($value['type'] == 2) {
                                $stt2++;
                                $body2 .= ' <tr>
                            <td>
                                <div class="text-center">' . $stt2 . '</div>
                            </td>
                            <td>
                                <div>
                                    ' . $value['name_category'] . '
                                </div>
                            </td>
                            <td><div class="td_type">Năng Lực</div></td>
                            <td><div class="td_name_kpi">' . $value['name_kpi'] . '</div></td>
                            <td><div class="td_code_kpi">' . $value['code_kpi'] . '</div></td>
                            <td><div class="td_measure">' . $value['measure'] . '</div></td>
                            <td><div class="td_target text-center">' . $value['time'] . '</div></td>
                            <td><div class="td_weight text-center">' . $value['weight'] . '</div></td>
                            <td><div class="td_regulations">' . $value['regulations'] . '</div></td>
                            <td class="text-center">' . $value['report'] . '</td>
                            <td>
                                ' . $value['name_result'] . '
                            </td>
                        </tr>';
                                $total_weight2 += $value['weight'];
                            } else {
                                $stt1++;
                                $body1 .= ' <tr>
                        <td>
                            <div class="text-center">' . $stt1 . '</div>
                        </td>
                        <td>
                            <div>
                                ' . $value['name_category'] . '
                            </div>
                        </td>
                        <td><div class="td_type">Tuân Thủ</div></td>
                        <td><div class="td_name_kpi">' . $value['name_kpi'] . '</div></td>
                        <td><div class="td_code_kpi">' . $value['code_kpi'] . '</div></td>
                        <td><div class="td_measure">' . $value['measure'] . '</div></td>
                        <td><div class="td_target text-center">' . $value['time'] . '</div></td>
                        <td><div class="td_weight text-center">' . $value['weight'] . '</div></td>
                        <td><div class="td_regulations">' . $value['regulations'] . '</div></td>
                        <td class="text-center">' . $value['report'] . '</td>
                        <td>
                            ' . $value['name_result'] . '
                        </td>
                    </tr>';
                                $total_weight1 += $value['weight'];
                            }
                        }
                    }
                    $html = '<table>
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px">' . lang('STT') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Nhóm KPIS') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Loại KPIS') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Chi tiết KPIS') . '</th>
                                <th class="text-center" style="width: 100px">' . lang('Mã KPIS') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Chỉ Số Đo Lường KPIs') . '</th>
                                <th class="text-center" style="width: 80px"><' . lang('Target') . '</th>
                                <th class="text-center" style="width: 80px">' . lang('Trọng số (%)') . '</th>
                                <th class="text-center" style="width: 200px">' . lang('Tiêu chuẩn/ quy định') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Báo Cáo Không Phù Hợp') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Kết quả') . '</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $body1 . '
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="total_weight text-center bold">' . $total_weight1 . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bold uppercase">% KPI</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-center" style="color: red">' . (80 * $total_weight1 / 100) . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>';

                    $html .= '<table style="margin-top: 30px !important;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px">' . lang('STT') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Nhóm KPIS') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Loại KPIS') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Chi tiết KPIS') . '</th>
                                <th class="text-center" style="width: 100px">' . lang('Mã KPIS') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Chỉ Số Đo Lường KPIs') . '</th>
                                <th class="text-center" style="width: 80px"><' . lang('Target') . '</th>
                                <th class="text-center" style="width: 80px">' . lang('Trọng số (%)') . '</th>
                                <th class="text-center" style="width: 200px">' . lang('Tiêu chuẩn/ quy định') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Báo Cáo Không Phù Hợp') . '</th>
                                <th class="text-center" style="width: 150px">' . lang('Kết quả') . '</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $body2 . '
                        </tbody>
                         <tfoot>
                            <tr>
                                <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="total_weight text-center bold">' . $total_weight2 . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bold uppercase">% KPI</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-center" style="color: red">' . (20 * $total_weight2 / 100) . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bold uppercase">Tổng KPI</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-center" style="color: red">' . ((80 * $total_weight1 / 100) + (20 * $total_weight2 / 100)) . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>';
                    $data['html'] = $html;
                }
            }
        } else {
            $type_object = $this->input->get('type_object');
            $id_object = $this->input->get('id_object');
            $type_append = $this->input->get('type_append');
            if ($type_object == 'evaluate') {
                $this->db->where('type', $type_append);
                $this->db->where('name_table', 'tbl_evaluate');
                $data['categoryRecommended'] = $this->db->get('tbl_category_recommended')->result_array();
                $data['object']->category_recommended_id = $data['categoryRecommended'][0]['id'];

                $this->db->select('tbl_evaluate.id, tbl_evaluate.code_evaluate as reference_no, tbl_evaluate.created_by');
                if (!empty($id_object)) {
                    $this->db->where('id', $id_object);
                }
                $result = $this->db->get_where('tbl_evaluate', ['type' => $type_append])->result_array();
                foreach ($result as $key => $value) {
                    $staff_suggest_name = "";
                    if (!empty($value['created_by'])) {
                        $staff_suggest_name = get_staff_full_name($value['created_by']);
                    }
                    $result[$key]['staff_suggest_name'] = $staff_suggest_name;
                }

                $this->db->where('type_plan_propose', $type_append);
                $recommended_list_group_id = $this->db->get('tbl_recommended_list')->row();
                $data['object']->recommended_list_group_id = $recommended_list_group_id->id;
                $data['object']->suggest_id = $id_object;
            } else if ($type_object == 'regulations') {
                $this->db->where('type', $type_append);
                $this->db->where('name_table', 'tbl_regulations');
                $data['categoryRecommended'] = $this->db->get('tbl_category_recommended')->result_array();
                $data['object']->category_recommended_id = $data['categoryRecommended'][0]['id'];

                $_type_append = '2';
                if ($type_append == 'rules') {
                    $_type_append = 1;
                }
                $this->db->select('tbl_regulations.id, tbl_regulations.code as reference_no, tbl_regulations.create_by');
                if (!empty($id_object)) {
                    $this->db->where('id', $id_object);
                }
                $result = $this->db->get_where('tbl_regulations', ['type' => $_type_append])->result_array();
                foreach ($result as $key => $value) {
                    $staff_suggest_name = "";
                    if (!empty($value['created_by'])) {
                        $staff_suggest_name = get_staff_full_name($value['created_by']);
                    }
                    $result[$key]['staff_suggest_name'] = $staff_suggest_name;
                }

                $this->db->where('type_plan_propose', $type_append);
                $recommended_list_group_id = $this->db->get('tbl_recommended_list')->row();
                $data['object']->recommended_list_group_id = $recommended_list_group_id->id;
                $data['object']->suggest_id = $id_object;
            } else if ($type_object == 'decision') {
                $this->db->where('type', $type_append);
                $this->db->where('name_table', 'tbldecision');
                $data['categoryRecommended'] = $this->db->get('tbl_category_recommended')->result_array();
                $data['object']->category_recommended_id = $data['categoryRecommended'][0]['id'];

                $this->db->select('tbldecision.id, tbldecision.code as reference_no, tbldecision.create_by');
                if (!empty($id_object)) {
                    $this->db->where('id', $id_object);
                }
                $result = $this->db->get('tbldecision')->result_array();
                foreach ($result as $key => $value) {
                    $staff_suggest_name = "";
                    if (!empty($value['created_by'])) {
                        $staff_suggest_name = get_staff_full_name($value['created_by']);
                    }
                    $result[$key]['staff_suggest_name'] = $staff_suggest_name;
                }

                $this->db->where('type_plan_propose', $type_append);
                $recommended_list_group_id = $this->db->get('tbl_recommended_list')->row();
                $data['object']->recommended_list_group_id = $recommended_list_group_id->id;
                $data['object']->suggest_id = $id_object;
            }
        }

        if (empty($data['categoryRecommended'])) {
            $data['categoryRecommended'] = $this->site_model->getCategoryRecommended();
        }

        $this->db->group_start();
        $this->db->group_start()->group_start()
            ->where('id_internal_proposal_process IS NULL', false, false)
            ->where('id_internal_proposal_process_child IS NULL', false, false)->group_end();
        $this->db->or_group_start()->where('id_internal_proposal_process', 0)->where('id_internal_proposal_process_child', 0)->group_end();
        $this->db->group_end();
        if (!empty($id)) {
            $this->db->or_where('id_internal_proposal', $id);
        }
        $this->db->group_end();
        $this->db->order_by('tblproduction_report.id', 'desc');
        $data['production_report'] = $this->db->get('tblproduction_report')->result_array();
        if (!empty($data['object']->id)) {
            $this->db->where('id_internal_proposal', $data['object']->id);
            $this->db->group_start()
                ->group_start()
                ->where('id_internal_proposal_process IS NULL', false, false)
                ->where('id_internal_proposal_process_child IS NULL', false, false)->group_end();
            $this->db->or_group_start()->where('id_internal_proposal_process', 0)->where('id_internal_proposal_process_child', 0)->group_end();
            $this->db->group_end();
            $data['object']->production_report = $this->db->get('tblproduction_report')->row('id');
        }
        $data['dtSuggest'] = $result;
        $this->load->view('admin/internal_proposal/modal_add', $data);
    }

    public function getStaffWhereRole()
    {

        $staffDepartments = "(
			SELECT
				tblstaff_departments.staffid as staffid,
				GROUP_CONCAT(tbldepartments.name) as name_department
			FROM tblstaff_departments
			INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
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
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $this->db->where('tblroles.roles_parent', 0);
        $this->db->join($staffDepartments, 'tb_staff_departments.staffid = tblstaff.staffid');
        return $this->db->get()->result_array();
    }

    public function add()
    {
        if ($this->input->post()) {

            $message = '';
            $data = $this->input->post();
            unset($data['id_board_search']);
            unset($data['id_block_search']);
            unset($data['id_departments_search']);

            $items = array();
            if (!empty($data['items'])) {
                $items = $data['items'];
                unset($data['items']);
            }
            $id_purchases = array();
            if (!empty($data['id_purchases'])) {
                $id_purchases = $data['id_purchases'];
                unset($data['id_purchases']);
                $data['id_purchases'] = -1;
            }
            $id = !empty($data['id']) ? $data['id'] : '';
            unset($data['id']);

            $is_excel_active = 0;
            $excel_headers = [];
            if (!empty($data['recommended_list_id'])) {
                $this->db->select('is_excel, excel');
                $this->db->where('id', $data['recommended_list_id']);
                $recom_item = $this->db->get('tbl_recommended_list')->row();
                if (!empty($recom_item) && $recom_item->is_excel == 1) {
                    $is_excel_active = 1;
                    $excel_headers = json_decode($recom_item->excel, true);
                }
            }

            if ($is_excel_active == 1) {
                if (empty($_FILES['excel_proposal_file']['tmp_name'])) {
                    if (empty($id)) {
                        echo json_encode([
                            'success' => false,
                            'alert_type' => 'danger',
                            'message' => 'Vui lòng chọn tệp Excel dữ liệu'
                        ]);
                        die();
                    }
                } else {
                    require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
                    $fullfile = $_FILES['excel_proposal_file']['tmp_name'];
                    $inputFileType = PHPExcel_IOFactory::identify($fullfile);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load("$fullfile");
                    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                    
                    $highestColumn = $objWorksheet->getHighestColumn();
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                    $highestRow = $objWorksheet->getHighestRow();
                    
                    $headerRow = 1;
                    $max_score = 0;
                    for ($r = 1; $r <= min(15, $highestRow); $r++) {
                        $score = 0;
                        for ($col = 0; $col < $highestColumnIndex; $col++) {
                            $cellValue = trim($objWorksheet->getCellByColumnAndRow($col, $r)->getValue());
							if ($cellValue !== '' && is_array($excel_headers) && in_array($cellValue, $excel_headers)) {
								$score++;
							}
                        }
                        if ($score > $max_score) {
                            $max_score = $score;
                            $headerRow = $r;
                        }
                    }
                    
                    $col_map = [];
                    $found_headers = [];
                    for ($col = 0; $col < $highestColumnIndex; $col++) {
                        $cellValue = trim($objWorksheet->getCellByColumnAndRow($col, $headerRow)->getValue());
						if ($cellValue !== '' && is_array($excel_headers) && in_array($cellValue, $excel_headers)) {
							$col_map[$col] = $cellValue;
                            $found_headers[] = $cellValue;
						}
                    }

                    // Check if all expected headers are present
                    $missing_headers = [];
                    if (is_array($excel_headers)) {
                        foreach ($excel_headers as $h) {
                            if (!in_array($h, $found_headers)) {
                                $missing_headers[] = $h;
                            }
                        }
                    }

                    if (!empty($missing_headers)) {
                        echo json_encode([
                            'success' => false,
                            'alert_type' => 'danger',
                            'message' => 'Tệp Excel thiếu các cột tiêu đề bắt buộc: ' . implode(', ', $missing_headers)
                        ]);
                        die();
                    }
                    
                    $rows = [];
                    for ($r = $headerRow + 1; $r <= $highestRow; $r++) {
                        $row_data = [];
                        $has_value = false;
						if (is_array($excel_headers)) {
							foreach ($excel_headers as $h) {
								$row_data[$h] = '';
							}
						}
                        for ($col = 0; $col < $highestColumnIndex; $col++) {
                            if (isset($col_map[$col])) {
                                $cellVal = $objWorksheet->getCellByColumnAndRow($col, $r)->getValue();
                                $cellVal = ($cellVal !== null) ? trim($cellVal) : '';
                                $row_data[$col_map[$col]] = $cellVal;
                                if ($cellVal !== '') {
                                    $has_value = true;
                                }
                            }
                        }
                        if ($has_value) {
                            $rows[] = $row_data;
                        }
                    }
                    $data['excel_data'] = json_encode($rows, JSON_UNESCAPED_UNICODE);
                }
            } else {
                $data['excel_data'] = '';
            }
            $data['date'] = to_sql_date(($data['date']), true);
            $data['date_finish'] = to_sql_date(($data['date_finish']), true);
            $data['money'] = number_format_data($data['money'], false);
            $data['content'] = $this->input->post('content', false);
            $staff_assigned = $this->input->post('staff_assigned');
            $staff_bod = $this->input->post('staff_bod');
            $monitor_id = $this->input->post('monitor_id');
            $head_of_department_id = $this->input->post('head_of_department_id');
            $recommended_list_group_id = !empty($this->input->post('recommended_list_group_id')) ? $this->input->post('recommended_list_group_id') : 0;

            $this->db->select('tbl_recommended_list.id as id,tbl_category_recommended.name_table,tbl_category_recommended.id as category_recommended_id', false);
            $this->db->from('tbl_recommended_list');
            $this->db->join('tbl_category_recommended', 'tbl_category_recommended.id = tbl_recommended_list.category_recommended_id');
            $this->db->where('tbl_recommended_list.id', $recommended_list_group_id);
            $rs = $this->db->get()->row_array();
            if (!empty($rs)) {
                $category_recommended_id = $rs['category_recommended_id'];
            } else {
                $category_recommended_id = 0;
            }

            $category_recommended_id = $this->input->post('category_recommended_id');
            $data['category_recommended_id'] = $category_recommended_id;
            if ($category_recommended_id == 41) {
                $data['suggest_id'] = 0;
            } else {
                $data['suggest_muti_id'] = NULL;
            }
            $suggest_muti_id = $data['suggest_muti_id'];
            unset($data['suggest_muti_id']);
            $recommended_list_detail_id = !empty($this->input->post('recommended_list_detail_id')) ? $this->input->post('recommended_list_detail_id') : [];
            if (empty($data['id_branch'])) {
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => 'Vui lòng chọn chi nhánh'
                ]);
                die();
            }
            $proposal_employee = $this->input->post('proposal_employee');

            $production_report = $this->input->post('production_report');
            if (!empty($production_report)) {
                $this->db->where('id', $production_report);
                $kt_production = $this->db->get('tblproduction_report')->row();
                if (!empty($kt_production)) {
                    if (!empty($kt_production->id_internal_proposal) && $kt_production->id_internal_proposal != $id) {
                        echo json_encode(
                            [
                                'success' => false,
                                'alert_type' => 'danger',
                                'message' => 'Phiếu BCKPH đã có liên kết với DXNB khác'
                            ]
                        );
                        die();
                    }
                }
            }

            unset($data['production_report']);
            unset($data['proposal_employee']);
            unset($data['staff_assigned']);
            unset($data['staff_bod']);
            unset($data['monitor_id']);
            unset($data['head_of_department_id']);
            unset($data['recommended_list_detail_id']);
            if (empty($id)) { //add a new
                if (empty($this->perAdd)) {
                    ajax_access_denied();
                }
                if ($data['date'] < date('Y-m-d H:00:00')) {
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => 'Ngày đề xuất không được thấp hơn ngày hiện tại'
                    ]);
                    die();
                }
                if ($this->internal_proposal_model->isExistCode($data['code'])) {
                    //					$success = false;
                    //					$message = _l('Mã đề xuất nội bộ đã tồn tại!');
                    //					echo json_encode(array(
                    //						'success' => $success,
                    //						'message' => $message
                    //					));
                    //					die;
                    $data['code'] = $this->internal_proposal_model->getCode();
                }
                $data['create_by'] = get_staff_user_id();
                $this->db->insert('tblinternal_proposal', $data);
                $id = $this->db->insert_id();
                if (!empty($id)) {

                    if (!empty($production_report)) {
                        $this->db->where('id', $production_report);
                        $kt_production = $this->db->get('tblproduction_report')->row();
                        if (!empty($kt_production)) {
                            $this->db->where('id', $production_report);
                            $this->db->update('tblproduction_report', [
                                'id_internal_proposal' => $id
                            ]);
                        }
                    }

                    if (!empty($data['recommended_list_group_id'])) {
                        $process = $this->db->get_where('tbl_recommended_list_process', ['recommended_list_id' => $data['recommended_list_group_id']])->result_array();
                        if (!empty($process)) {
                            foreach ($process as $key => $value) {
                                $this->db->insert('tbl_internal_proposal_process', [
                                    'id_internal_proposal' => $id,
                                    'id_recommended_list' => $data['recommended_list_group_id'],
                                    'id_process' => $value['id'],
                                    'name' => $value['name'],
                                    'roles' => $value['roles'],
                                    'stages' => $value['stages'],
                                    'bod' => $value['bod']
                                ]);
                                $process_child = $this->db->get_where('tbl_recommended_list_process_child', ['id_recommended_list_process' => $value['id']])->result_array();
                                foreach ($process_child as $kk => $vv) {
                                    $this->db->insert('tbl_internal_proposal_process_child', [
                                        'id_internal_proposal' => $id,
                                        'id_recommended_list_process' => $vv['id'],
                                        'recommended_list_id' => $value['id'],
                                        'name' => $vv['name'],
                                        'approval_standards' => $vv['approval_standards'],
                                        'completion_control_standards' => $vv['completion_control_standards']
                                    ]);
                                }
                            }
                        }
                    }
                    if (!empty($id_purchases)) {
                        $array_purchase = array();
                        foreach ($id_purchases as $key => $value) {
                            $array_purchase[$key]['id_internal_proposal'] = $id;
                            $array_purchase[$key]['id_purchases'] = $value;
                        }
                        if (!empty($array_purchase)) {
                            $this->db->insert_batch('tblinternal_proposal_purchase', $array_purchase);
                        }
                    }
                    if (!empty($items)) {
                        $in = array();
                        foreach ($items as $key => $value) {
                            $info_items = array();
                            $info_items['recipe'] = $value['recipe'];
                            $info_items['paper'] = $value['paper'];
                            $info_items['longs'] = $value['longs'];
                            $info_items['wide'] = $value['wide'];
                            $info_items_text = json_encode($info_items);
                            $in[$key]['id_internal_proposal'] = $id;
                            $in[$key]['id_purchases'] = $value['id_purchases'];
                            $in[$key]['id_purchases_items'] = $value['id'];
                            $in[$key]['id_items'] = $value['id_items'];
                            $in[$key]['type'] = $value['type'];
                            $in[$key]['tax_id'] = $value['tax_id'];
                            $in[$key]['tax_rate'] = $value['tax_rate'];
                            $in[$key]['quantity'] = number_unformat($value['quantity_suppliers']);
                            $in[$key]['quantity_stock'] = number_unformat($value['quantity_stock']);
                            $in[$key]['quantity_payment'] = number_unformat($value['quantity_payment']);
                            $in[$key]['price'] = number_unformat($value['price_suppliers']);
                            $in[$key]['suppliers_id'] = $value['suppliers_id'];
                            $in[$key]['quantity_unit'] = number_unformat($value['quantity_suppliers']);
                            $in[$key]['exchange_unit'] = number_unformat($value['exchange_standard_unit']);
                            $in[$key]['exchange_stock'] = number_unformat($value['exchange_stock']);
                            $in[$key]['exchange_payment'] = number_unformat($value['exchange_payment']);
                            $in[$key]['info_items'] = $info_items_text;
                        }
                        if (!empty($in)) {
                            $this->db->insert_batch('tbl_internal_proposal_purchase_items', $in);
                        }
                    }

                    // if (!empty($staff_bod)) {
                    //     foreach ($staff_bod as $key => $value) {
                    //         $this->db->insert('tblinternal_proposal_staff_pod', [
                    //             'id_internal_proposal' => $id,
                    //             'id_staff' => $value,
                    //         ]);
                    //     }
                    // }
                    // if (!empty($monitor_id)) {
                    //     foreach ($monitor_id as $key => $value) {
                    //         $this->db->insert('tblinternal_proposal_monitor', [
                    //             'id_internal_proposal' => $id,
                    //             'id_staff' => $value,
                    //         ]);
                    //     }
                    // }
                    // if (!empty($head_of_department_id)) {
                    //     foreach ($head_of_department_id as $key => $value) {
                    //         $this->db->insert('tblinternal_proposal_head_of_department', [
                    //             'id_internal_proposal' => $id,
                    //             'id_staff' => $value,
                    //         ]);
                    //     }
                    // }
                    if (!empty($suggest_muti_id)) {
                        foreach ($suggest_muti_id as $kk => $vv) {
                            $dtSuggest_payslips = get_table_where('tbl_suggest_payslips', ['id' => $vv], '', 'row_array');
                            $this->db->insert('tbl_suggest_muti_id', [
                                'id_internal_proposal' => $id,
                                'suggest_id' => $vv,
                                'total' => !empty($dtSuggest_payslips) ? $dtSuggest_payslips['total'] : 0,
                            ]);
                        }
                    }
                    if (!empty($proposal_employee)) {
                        foreach ($proposal_employee as $kk => $vv) {
                            $this->db->insert('tblinternal_proposal_employee', [
                                'internal_proposal' => $id,
                                'staff_id' => $vv
                            ]);
                        }
                    }

                    if (!empty($recommended_list_detail_id)) {
                        foreach ($recommended_list_detail_id as $key => $value) {
                            $this->db->insert('tblinternal_proposal_recommended', [
                                'id_internal_proposal' => $id,
                                'recommended_list_detail_id' => $value,
                            ]);
                        }
                    }
                    if (!empty($staff_bod)) {
                        $this->db->insert('tblinternal_proposal_staff_pod', [
                            'id_internal_proposal' => $id,
                            'id_staff' => $staff_bod,
                        ]);
                    }
                    if (!empty($monitor_id)) {
                        $this->db->insert('tblinternal_proposal_monitor', [
                            'id_internal_proposal' => $id,
                            'id_staff' => $monitor_id,
                        ]);
                    }
                    if (!empty($head_of_department_id)) {
                        $this->db->insert('tblinternal_proposal_head_of_department', [
                            'id_internal_proposal' => $id,
                            'id_staff' => $head_of_department_id,
                        ]);
                    }
                    $success = true;
                    $message = _l('ch_added_successfuly');
                    if (!empty($staff_assigned)) {
                        $dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ ' . $data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';
                        // foreach ($staff_assigned as $key => $value) {
                        //     $this->db->insert('tblinternal_proposal_assigned', [
                        //         'id_internal_proposal' => $id,
                        //         'id_staff' => $value,
                        //     ]);
                        //     $notification_data = [
                        //         'date' => date('Y-m-d H:i:s'),
                        //         'description' => $dataHtml,
                        //         'touserid' => $value,
                        //         'link' => 'internal_proposal/view/' . $id,
                        //         'type' => 13,
                        //         'object_id' => $id,
                        //         'object_type' => 'internal_proposal',
                        //     ];
                        //     if (!empty($notification_data)) {
                        //         $this->db->insert('tblnotifications', $notification_data);
                        //         pusher_trigger_notification($notification_data);
                        //     }
                        //     send_notification_app_c($id, [
                        //         'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ ' . $data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                        //         'title' => 'Theo dõi phiếu đề xuất nội bộ',
                        //         'code' => $data['code'],
                        //         'object_type' => 'internal_proposal'
                        //     ], [$value], get_staff_user_id());
                        // }
                        $this->db->insert('tblinternal_proposal_assigned', [
                            'id_internal_proposal' => $id,
                            'id_staff' => $staff_assigned,
                        ]);
                        $notification_data = [
                            'date' => date('Y-m-d H:i:s'),
                            'description' => $dataHtml,
                            'touserid' => $staff_assigned,
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
                            'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ ' . $data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                            'title' => 'Theo dõi phiếu đề xuất nội bộ',
                            'code' => $data['code'],
                            'object_type' => 'internal_proposal'
                        ], [$staff_assigned], get_staff_user_id());
                        $this->SendEmailNoti($staff_assigned, $id);
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
                $internal_proposal = $this->db->get('tblinternal_proposal')->row();
                if (!empty($internal_proposal) && $internal_proposal->status == 1) {
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => 'Phiếu được duyệt không thể chỉnh sửa'
                    ]);
                    die();
                }
                $this->db->where('id_internal_proposal', $id);
                $this->db->where('status', 1);
                $internal_proposal_process = $this->db->get('tbl_internal_proposal_process')->row();
                if (!empty($internal_proposal_process)) {
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => 'Phiếu được duyệt không thể chỉnh sửa'
                    ]);
                    die();
                }
                $this->db->where('id', $id);
                $success_update = $this->db->update('tblinternal_proposal', $data);
                if ($success_update) {
                    if (!empty($internal_proposal->id)) {
                        $this->db->where('id_internal_proposal', $internal_proposal->id);
                        $this->db->where('id_internal_proposal_process IS NULL', false, false);
                        $this->db->where('id_internal_proposal_process_child IS NULL', false, false);
                        $this->db->update('tblproduction_report', [
                            'id_internal_proposal' => null
                        ]);
                    }
                    if (!empty($production_report)) {
                        $this->db->where('id', $production_report);
                        $kt_production = $this->db->get('tblproduction_report')->row();
                        if (!empty($kt_production)) {
                            $this->db->where('id', $production_report);
                            $this->db->update('tblproduction_report', [
                                'id_internal_proposal' => $id
                            ]);
                        }
                    }

                    if (!empty($data['recommended_list_group_id'])) {
                        $this->db->delete('tbl_internal_proposal_process', array('id_internal_proposal' => $id));
                        $process = $this->db->get_where('tbl_recommended_list_process', ['recommended_list_id' => $data['recommended_list_group_id']])->result_array();
                        if (!empty($process)) {
                            foreach ($process as $key => $value) {
                                $this->db->insert('tbl_internal_proposal_process', [
                                    'id_internal_proposal' => $id,
                                    'id_recommended_list' => $data['recommended_list_group_id'],
                                    'id_process' => $value['id'],
                                    'name' => $value['name'],
                                    'roles' => $value['roles'],
                                    'bod' => $value['bod']
                                ]);
                            }
                        }
                    }
                    if (!empty($items)) {
                        $this->db->delete('tbl_internal_proposal_purchase_items', array('id_internal_proposal' => $id));
                        $in = array();
                        foreach ($items as $key => $value) {
                            $info_items = array();
                            $info_items['recipe'] = $value['recipe'];
                            $info_items['paper'] = $value['paper'];
                            $info_items['longs'] = $value['longs'];
                            $info_items['wide'] = $value['wide'];
                            $info_items_text = json_encode($info_items);
                            $in[$key]['id_internal_proposal'] = $id;
                            $in[$key]['id_purchases'] = $value['id_purchases'];
                            $in[$key]['id_purchases_items'] = $value['id'];
                            $in[$key]['id_items'] = $value['id_items'];
                            $in[$key]['type'] = $value['type'];
                            $in[$key]['tax_id'] = $value['tax_id'];
                            $in[$key]['tax_rate'] = $value['tax_rate'];
                            $in[$key]['quantity'] = number_unformat($value['quantity_suppliers']);
                            $in[$key]['quantity_stock'] = number_unformat($value['quantity_stock']);
                            $in[$key]['quantity_payment'] = number_unformat($value['quantity_payment']);
                            $in[$key]['price'] = number_unformat($value['price_suppliers']);
                            $in[$key]['suppliers_id'] = $value['suppliers_id'];
                            $in[$key]['quantity_unit'] = number_unformat($value['quantity_suppliers']);
                            $in[$key]['exchange_unit'] = number_unformat($value['exchange_standard_unit']);
                            $in[$key]['exchange_stock'] = number_unformat($value['exchange_stock']);
                            $in[$key]['exchange_payment'] = number_unformat($value['exchange_payment']);
                            $in[$key]['info_items'] = $info_items_text;
                        }
                        if (!empty($in)) {
                            $this->db->insert_batch('tbl_internal_proposal_purchase_items', $in);
                        }
                    }
                    $success = true;
                    $message = _l('ch_updated_successfuly');
                    $this->db->where('id_internal_proposal', $id);
                    $list_assigned = $this->db->get('tblinternal_proposal_assigned')->result_array();
                    $arrayList = [];
                    if (!empty($list_assigned)) {
                        foreach ($list_assigned as $key => $value) {
                            $arrayList[$value['id_staff']] = true;
                        }
                    }
                    $this->db->where('id_internal_proposal', $id);
                    $this->db->delete('tblinternal_proposal_assigned');

                    $this->db->where('id_internal_proposal', $id);
                    $this->db->delete('tblinternal_proposal_staff_pod');

                    $this->db->where('id_internal_proposal', $id);
                    $this->db->delete('tblinternal_proposal_head_of_department');

                    $this->db->where('id_internal_proposal', $id);
                    $this->db->delete('tblinternal_proposal_monitor');

                    $this->db->where('internal_proposal', $id);
                    $this->db->delete('tblinternal_proposal_employee');

                    if (!empty($proposal_employee)) {
                        foreach ($proposal_employee as $kk => $vv) {
                            $this->db->insert('tblinternal_proposal_employee', [
                                'internal_proposal' => $id,
                                'staff_id' => $vv
                            ]);
                        }
                    }
                    $this->db->where('id_internal_proposal', $id);
                    $this->db->delete('tbl_suggest_muti_id');
                    if (!empty($suggest_muti_id)) {
                        foreach ($suggest_muti_id as $kk => $vv) {
                            $dtSuggest_payslips = get_table_where('tbl_suggest_payslips', ['id' => $vv], '', 'row_array');
                            $this->db->insert('tbl_suggest_muti_id', [
                                'id_internal_proposal' => $id,
                                'suggest_id' => $vv,
                                'total' => !empty($dtSuggest_payslips) ? $dtSuggest_payslips['total'] : 0,
                            ]);
                        }
                    }
                    if (!empty($staff_bod)) {
                        $this->db->insert('tblinternal_proposal_staff_pod', [
                            'id_internal_proposal' => $id,
                            'id_staff' => $staff_bod,
                        ]);
                    }
                    if (!empty($monitor_id)) {
                        $this->db->insert('tblinternal_proposal_monitor', [
                            'id_internal_proposal' => $id,
                            'id_staff' => $monitor_id,
                        ]);
                    }
                    if (!empty($head_of_department_id)) {
                        $this->db->insert('tblinternal_proposal_head_of_department', [
                            'id_internal_proposal' => $id,
                            'id_staff' => $head_of_department_id,
                        ]);
                    }
                    $this->db->where('id_internal_proposal', $id);
                    $this->db->delete('tblinternal_proposal_recommended');


                    if (!empty($recommended_list_detail_id)) {
                        foreach ($recommended_list_detail_id as $key => $value) {
                            $this->db->insert('tblinternal_proposal_recommended', [
                                'id_internal_proposal' => $id,
                                'recommended_list_detail_id' => $value,
                            ]);
                        }
                    }

                    if (!empty($staff_assigned)) {
                        $dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';
                        $arraySendEmail = [];
                        // foreach ($staff_assigned as $key => $value) {
                        //     $this->db->insert('tblinternal_proposal_assigned', [
                        //         'id_internal_proposal' => $id,
                        //         'id_staff' => $value,
                        //     ]);
                        //     if (empty($arrayList[$value])) {
                        //         $arraySendEmail[] = $value;
                        //         $notification_data = [
                        //             'date' => date('Y-m-d H:i:s'),
                        //             'description' => $dataHtml,
                        //             'touserid' => $value,
                        //             'link' => 'internal_proposal/view/' . $id,
                        //             'type' => 13,
                        //             'object_id' => $id,
                        //             'object_type' => 'internal_proposal',
                        //         ];
                        //         if (!empty($notification_data)) {
                        //             $this->db->insert('tblnotifications', $notification_data);
                        //             pusher_trigger_notification($notification_data);
                        //         }
                        //         send_notification_app_c($id, [
                        //             'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                        //             'title' => 'Theo dõi phiếu đề xuất nội bộ',
                        //             'code' => $internal_proposal->code,
                        //             'object_type' => 'internal_proposal'
                        //         ], [$value], get_staff_user_id());
                        //     }
                        // }
                        $this->db->insert('tblinternal_proposal_assigned', [
                            'id_internal_proposal' => $id,
                            'id_staff' => $staff_assigned,
                        ]);
                        if (empty($arrayList[$staff_assigned])) {
                            $arraySendEmail[] = $staff_assigned;
                            $notification_data = [
                                'date' => date('Y-m-d H:i:s'),
                                'description' => $dataHtml,
                                'touserid' => $staff_assigned,
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
                                'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                                'title' => 'Theo dõi phiếu đề xuất nội bộ',
                                'code' => $internal_proposal->code,
                                'object_type' => 'internal_proposal'
                            ], [$staff_assigned], get_staff_user_id());
                        }
                        if (!empty($arraySendEmail)) {
                            $this->SendEmailNoti($arraySendEmail, $id);
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
                    $path = 'uploads/internal_proposal/' . $id . '/';
                    if (!file_exists(FCPATH . 'uploads/internal_proposal/')) {
                        mkdir(FCPATH . 'uploads/internal_proposal/');
                        fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
                    }
                    if (!file_exists(FCPATH . 'uploads/internal_proposal/' . $id)) {
                        mkdir(FCPATH . 'uploads/internal_proposal/' . $id);
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
                                        'rel_type' => 'internal',
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
        if ($id > 4844) {
            if ($this->internal_proposal_model->isApproved($id)) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Đề xuất này đã được duyệt. Không thể xóa!'
                ));
                die;
            }
        }
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'internal_proposal');
        $tasks = $this->db->get('tbltasks')->row();
        if (!empty($tasks)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Đang tồn tại phiếu công việc không thể xóa'
            ));
            die;
        }
        $dtData = get_table_where('tblinternal_proposal', array('id' => $id), '', 'row_array');
        $isSuccess = $this->db->delete('tblinternal_proposal', array('id' => $id));
        if ($isSuccess) {
            $this->db->delete('tbl_internal_proposal_purchase_items', array('id_internal_proposal' => $id));
            $this->db->delete('tblinternal_proposal_purchase', array('id_internal_proposal' => $id));

            $this->db->where('id_internal_proposal', $id);
            $this->db->delete('tblinternal_proposal_staff_pod');

            $this->db->where('internal_proposal', $id);
            $this->db->delete('tblinternal_proposal_employee');

            $this->db->where('id_internal_proposal', $id);
            $this->db->delete('tbl_suggest_muti_id');

            insertActivityLog([
                'type_parent_obj' => 'internal_proposal',
                'table_obj' => 'tblinternal_proposal',
                'id_obj' => $dtData['id'],
                'name_obj' => $dtData['code'] ?? null,
                'content' => lang('Xóa đề xuất nội bộ') . ' [' . ($dtData['code'] ?? null) . ']',
                'actions' => 'delete'
            ]);
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

    public function update_submit()
    {
        $id = $this->input->post('id_dxnb');
        $id_tasks = '';
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        if (!$this->internal_proposal_model->isApproved($id)) {
            $this->db->where('id_internal_proposal', $id);
            $check_process = $this->db->get('tbl_internal_proposal_process')->row();
            if (!empty($check_process)) {
                $this->db->where('id_internal_proposal', $id);
                $this->db->where('bod', 1);
                $process = $this->db->get('tbl_internal_proposal_process')->row();
                $options = [
                    'status' => 1,
                    'date_status' => date('Y-m-d H:i:s'),
                    'staff_id' => get_staff_user_id(),
                    'reason' => null,
                ];
                $this->db->where('id', $process->id);
                $success = $this->db->update('tbl_internal_proposal_process', $options);
            }
            $this->db->where('id_internal_proposal', $id);
            $internal_proposal_purchase = $this->db->get('tbl_internal_proposal_purchase_items')->result_array();
            $staff = get_staff_user_id();
            $data = [
                'approved_by' => $staff,
                'status' => 1,
                'reason' => null,
            ];
            $this->db->where('id', $id);
            $success = $this->db->update('tblinternal_proposal', $data);
            if ($success) {
                $id_tasks = $this->createTaskAuto($id);
                $listArrayPurchase = $this->createPurchaseAuto($id, 1);
                $this->create_suggestion_purchase($id);
                if (!empty($internal_proposal->type_plan_propose)) {
                    //						$this->createAutoplan_propose($id);
                    $this->createAutoplan_propose($id, $listArrayPurchase);
                }
                $check_purchase = get_table_where('tblpurchase_order', array('id_internal_proposal' => $id), '', 'row');
                if (empty($check_purchase)) {
                    $this->create_suggestion_new($id);
                }
                $success = true;
                $message = _l('ch_successful_approval');
                $staff_assigned = $this->db->get_where(
                    'tblinternal_proposal_assigned',
                    ['id_internal_proposal' => $id]
                )->result_array();
                if (!empty($staff_assigned)) {
                    $dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
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
                            'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                            'title' => 'Đổi trạng thái phiếu đề xuất nội bộ',
                            'code' => $internal_proposal->code,
                            'object_type' => 'internal_proposal'
                        ], [$value['id_staff']], get_staff_user_id());
                    }
                }
            } else {
                $success = false;
                $message = _l('ch_no_successful_approval');
            }
        }
        echo json_encode(array(
            'id_task' => $id_tasks,
            'success' => $success,
            'message' => $message
        ));
        die;
    }

    public function approve_status_staff($id)
    {
        if (!$this->perApprove) {
            echo json_encode(array(
                'success' => false,
                'message' => lang('Bạn không có quyền duyệt')
            ));
            die;
        }
        $id_tasks = '';
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();

        $this->db->where('id_internal_proposal', $id);
        $this->db->group_start();
        $this->db->where('bod', 2);
        $this->db->group_end();
        $process = $this->db->get('tbl_internal_proposal_process')->row();
        if (!$this->internal_proposal_model->isApproved_staff($id)) {

            $this->db->where('id_internal_proposal', $process->id_internal_proposal);
            $this->db->where('id <', $process->id);
            $this->db->order_by('id', 'desc');
            $check_status_bef = $this->db->get('tbl_internal_proposal_process')->row_array();
            if (!empty($check_status_bef)) {
                if ($check_status_bef['status'] == 0) {
                    $data['result'] = 0;
                    $data['message'] = lang('Bước ' . $check_status_bef['name'] . ' chưa duyệt, Không thể duyệt bước này');
                    echo json_encode($data);
                    die;
                }
            }
            $this->db->where('id_internal_proposal', $id);
            $this->db->where('bod', 2);
            $process = $this->db->get('tbl_internal_proposal_process')->row();
            $options = [
                'status' => 1,
                'date_status' => date('Y-m-d H:i:s'),
                'staff_id' => get_staff_user_id(),
                'reason' => null,
            ];
            $this->db->where('id', $process->id);
            $success = $this->db->update('tbl_internal_proposal_process', $options);

            $staff = get_staff_user_id();
            $data = [
                'user_status_staff' => $staff,
                'status_staff' => 1,
            ];
            $this->db->where('id', $id);
            $success = $this->db->update('tblinternal_proposal', $data);
            if ($success) {
                $success = true;
                $message = _l('ch_successful_approval');
            } else {
                $success = false;
                $message = _l('ch_no_successful_approval');
            }
        } else {
            if ($internal_proposal->status != 0) {
                echo json_encode(array(
                    'success' => false,
                    'message' => lang('Vui lòng bỏ duyệt BOD trước')
                ));
                die;
            }
            $this->db->where('id_internal_proposal', $id);
            $this->db->group_start();
            $this->db->where('bod', 2);
            $this->db->group_end();
            $process = $this->db->get('tbl_internal_proposal_process')->row();

            $options = [
                'status' => 0,
                'date_status' => NULL,
                'staff_id' => NULL,
                'reason' => null,
            ];
            $this->db->where('id', $process->id);
            $success = $this->db->update('tbl_internal_proposal_process', $options);
            $data = [
                'user_status_staff' => 0,
                'status_staff' => 0,
            ];
            $this->db->where('id', $id);
            $success = $this->db->update('tblinternal_proposal', $data);
            if ($success) {
                $success = true;
                $message = _l('ch_successful_approval_cance');
                $contentStatus = '';
            } else {
                $success = false;
                $message = _l('ch_no_successful_approval_cance');
            }
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
        die;
    }

    public function not_approve_status_staff()
    {
        if (!$this->perApprove) {
            echo json_encode(array(
                'success' => false,
                'message' => lang('Bạn không có quyền duyệt')
            ));
            die;
        }
        $success = false;
        $message = _l('c_not_approved_fail');
        $id = $this->input->post('id');
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();

        if (!$this->internal_proposal_model->isApproved_staff($id)) {
            $this->db->where('id_internal_proposal', $id);
            $this->db->where('bod', 2);
            $process = $this->db->get('tbl_internal_proposal_process')->row();
            $reason = $this->input->post('reason');

            $options = [
                'status' => 2,
                'date_status' => date('Y-m-d H:i:s'),
                'staff_id' => get_staff_user_id(),
                'reason' => $reason,
            ];
            $this->db->where('id', $process->id);
            $success = $this->db->update('tbl_internal_proposal_process', $options);

            $staff = get_staff_user_id();
            $data = [
                'user_status_staff' => $staff,
                'status_staff' => 2,
            ];
            $this->db->where('id', $id);
            $success = $this->db->update('tblinternal_proposal', $data);
            if ($success) {
                $success = true;
                $message = _l('c_not_approved_success');
            }
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
        die;
    }

    public function approve($id, $status = '')
    {
        if (!$this->approvett) {
            // ajax_access_denied();
            echo json_encode(array(
                'success' => false,
                'message' => lang('Bạn không có quyền duyệt')
            ));
            die;
        }
        $id_tasks = '';
        $this->db->where('id', $status);
        $this->db->where('id_internal_proposal', $id);
        $internal_proposal_process = $this->db->get('tbl_internal_proposal_process')->row_array();

        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        if ($internal_proposal_process['bod'] == 1) {
            if (!$this->internal_proposal_model->isApproved($id)) {
                $this->db->where('id_internal_proposal', $id);
                $check_process = $this->db->get('tbl_internal_proposal_process')->row();
                if (empty($check_process)) {
                    if (!$internal_proposal->status_staff) {
                        echo json_encode(array(
                            'success' => false,
                            'message' => lang('Vui lòng duyệt nhân viên trước')
                        ));
                        die;
                    }
                } else {
                    if ($internal_proposal->status_staff == 2) {
                        echo json_encode(array(
                            'success' => false,
                            'message' => lang('Quản lý không duyệt,Không thể duyệt bước này')
                        ));
                        die;
                    }
                    $this->db->where('id_internal_proposal', $id);
                    $internal_proposal_purchase = $this->db->get('tbl_internal_proposal_purchase_items')->result_array();
                    $items_array = [];
                    $dem = 0;
                    foreach ($internal_proposal_purchase as $k => $v) {
                        $purchase_order = get_table_where('tblpurchases_items', array('id' => $v['id_purchases_items']));
                        foreach ($purchase_order as $key => $value) {
                            $dem++;
                            $mainQuantity_suppliers = $value['quantity_net'] - $value['quantity_create'] - $value['quantity_create_all'] - $value['quantity'];
                            if ($mainQuantity_suppliers < 0) {
                                $items_array[$dem] = $v;
                                $items_array[$dem]['quantity_net'] = $value['quantity_net'];
                                $items_array[$dem]['mainQuantity_suppliers'] = $mainQuantity_suppliers;
                                $items_array[$dem]['quanliti_po'] = $value['quantity_create'] + $value['quantity_create_all'];
                            }
                        }
                    }
                    if (!empty($items_array)) {
                        $html = '';
                        foreach ($items_array as $key => $value) {
                            $type_item = $value['type'];
                            $items_id = $value['id_items'];
                            if ($type_item == "product") {
                                $info = $this->products_model->rowProduct($items_id);
                                $unit = $this->unit_model->rowUnit($info['unit_id']);
                            } elseif ($type_item == "items") {
                                $info = $this->items_model->rowItems($items_id);
                                $unit = $this->unit_model->rowUnit($info['unit']);
                            } elseif ($type_item == "nvl") {
                                $info = $this->items_model->rowMaterial($items_id);
                                $unit = $this->unit_model->rowUnit($info['unit_id']);
                            }
                            $items_code = $info['code'];
                            $items_name = $info['name'];
                            $html .= '<tr>';
                            $html .= "<td>$items_name ($items_code)</td>";
                            $html .= "<td class='text-center'>" . $unit['unit'] . "</td>";
                            $html .= "<td class='text-center'>" . formatNumber($value['quantity_net']) . "</td>";
                            $html .= "<td class='text-center'>" . formatNumber($value['quanliti_po']) . "</td>";
                            $html .= "<td class='text-center'>" . formatNumber($value['quantity']) . "</td>";
                            $note = 'Mặt hàng này đã được tạo Đơn Hàng Mua với số lượng ' . formatNumber($value['quanliti_po']) . ', đề xuất thêm sẽ dư ' . formatNumber($value['quantity'] - $value['mainQuantity_suppliers']) . ', Vui lòng kiểm tra lại hoặc tiếp tục tạo Đơn Hàng Mua';
                            $html .= "<td>" . $note . "</td>";
                            $html .= '</tr>';
                        }
                        echo json_encode(array(
                            'success' => 3,
                            'html' => $html
                        ));
                        die;
                    }


                    $this->db->where('id_internal_proposal', $id);
                    $this->db->group_start();
                    $this->db->where('bod', 1);
                    $this->db->group_end();
                    $process = $this->db->get('tbl_internal_proposal_process')->row();
                    // if (!is_admin()) {
                    //     $staffid = get_staff_user_id();
                    //     $this->db->where('staffid', $staffid);
                    //     $check_role = $this->db->get('tblstaff')->row();
                    //     if ($process->roles != $check_role->role) {
                    //         $data['result'] = 0;
                    //         $data['message'] = lang('Bạn không thuộc vị trí duyệt trạng thái này');
                    //         echo json_encode($data);
                    //         die;
                    //     }
                    // }
                    $this->db->where('id_internal_proposal', $id);
                    $this->db->where('status', 0);
                    $this->db->where('id <', $process->id);
                    $check_status = $this->db->get('tbl_internal_proposal_process')->row();
                    if (!empty($check_status)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng duyệt nhân viên trước');
                        echo json_encode($data);
                        die;
                    }
                    $options = [
                        'status' => 1,
                        'date_status' => date('Y-m-d H:i:s'),
                        'staff_id' => get_staff_user_id(),
                        'reason' => null,
                    ];
                    $this->db->where('id', $process->id);
                    $success = $this->db->update('tbl_internal_proposal_process', $options);
                }

                $staff = get_staff_user_id();
                $data = [
                    'approved_by' => $staff,
                    'status' => 1,
                    'reason' => null,
                ];
                $this->db->where('id', $id);
                $success = $this->db->update('tblinternal_proposal', $data);
                if ($success) {
                    $id_tasks = $this->createTaskAuto($id);
                    $listArrayPurchase = $this->createPurchaseAuto($id);
                    $this->create_suggestion_purchase($id);
                    if (!empty($internal_proposal->type_plan_propose)) {
                        $this->createAutoplan_propose($id, $listArrayPurchase);
                    }
                    $check_purchase = get_table_where('tblpurchase_order', array('id_internal_proposal' => $id), '', 'row');
                    if (empty($check_purchase)) {
                        if ($internal_proposal->category_recommended_id == 41) {
                            $this->create_suggestion_new_muiti($id);
                        } else {
                            $this->create_suggestion_new($id);
                        }
                    }
                    $success = true;
                    $message = _l('ch_successful_approval');
                    $staff_assigned = $this->db->get_where(
                        'tblinternal_proposal_assigned',
                        ['id_internal_proposal' => $id]
                    )->result_array();
                    if (!empty($staff_assigned)) {
                        $dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
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
                                'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                                'title' => 'Đổi trạng thái phiếu đề xuất nội bộ',
                                'code' => $internal_proposal->code,
                                'object_type' => 'internal_proposal'
                            ], [$value['id_staff']], get_staff_user_id());
                        }
                    }
                } else {
                    $success = false;
                    $message = _l('ch_no_successful_approval');
                }
            } else {
                $check_orders = get_table_where('tblpurchase_order', array('id_internal_proposal' => $id));
                if (!empty($check_orders)) {
                    $success = false;
                    $message = _l('Vui lòng xóa PO liên quan đến YCMH để bỏ duyệt');
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                    die;
                }
                $check_orders = get_table_where('tblplan_propose', array('id_internal_proposal' => $id));
                if (!empty($check_orders)) {
                    $success = false;
                    $message = _l('Vui lòng xóa phiếu kế hoạch để bỏ duyệt');
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                    die;
                }
                $suggestion = get_table_where('tblsuggestion', array('id_internal_proposal' => $id));
                if (!empty($suggestion)) {
                    $success = false;
                    $message = _l('Vui lòng xóa phiếu đề xuất tài chính để bỏ duyệt');
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                    die;
                }
                $this->db->where('id_internal_proposal', $id);
                $this->db->group_start();
                $this->db->where('bod', 1);
                // $this->db->or_where('bod', 5);
                $this->db->group_end();
                $process = $this->db->get('tbl_internal_proposal_process')->row();
                if (!is_admin()) {
                    $staffid = get_staff_user_id();
                    $this->db->where('staffid', $staffid);
                    $check_role = $this->db->get('tblstaff')->row();
                    if ($process->roles != $check_role->role) {
                        $data['result'] = 0;
                        $data['message'] = lang('Bạn không thuộc vị trí duyệt trạng thái này');
                        echo json_encode($data);
                        die;
                    }
                }
                $options = [
                    'status' => 0,
                    'date_status' => NULL,
                    'staff_id' => NULL,
                    'reason' => null,
                ];
                $this->db->where('id', $process->id);
                $success = $this->db->update('tbl_internal_proposal_process', $options);
                $data = [
                    'approved_by' => 0,
                    'status' => 0,
                    'reason' => null,
                ];
                $this->db->where('id', $id);
                $success = $this->db->update('tblinternal_proposal', $data);
                if ($success) {
                    $success = true;
                    $message = _l('ch_successful_approval_cance');
                    $contentStatus = '';
                    if ($internal_proposal->status == 2) {
                        $contentStatus = ' không';
                    }
                    $staff_assigned = $this->db->get_where(
                        'tblinternal_proposal_assigned',
                        ['id_internal_proposal' => $id]
                    )->result_array();
                    if (!empty($staff_assigned)) {
                        $dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa hủy' . $contentStatus . ' duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
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
                                'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa hủy' . $contentStatus . ' duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                                'title' => 'Đổi trạng thái phiếu đề xuất nội bộ',
                                'code' => $internal_proposal->code,
                                'object_type' => 'internal_proposal'
                            ], [$value['id_staff']], get_staff_user_id());
                        }
                    }
                } else {
                    $success = false;
                    $message = _l('ch_no_successful_approval_cance');
                }
            }
        } else {
            if ($internal_proposal_process['status'] != 1 && $internal_proposal_process['status'] != 2) {
                $options = [
                    'status' => 1,
                    'date_status' => date('Y-m-d H:i:s'),
                    'staff_id' => get_staff_user_id(),
                    'reason' => null,
                ];
                $success = true;
                $message = _l('Duyệt thành công');
            } else {
                $production_report = get_table_where('tblproduction_report', ['id_internal_proposal' => $internal_proposal_process['id_internal_proposal'], 'id_internal_proposal_process' => $internal_proposal_process['id'], 'id_internal_proposal_process_child' => 0], '', 'row_array');
                if (!empty($production_report)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã tồn tại phiếu báo cáo không phù hợp, Không thể bỏ duyệt bước này');
                    echo json_encode($data);
                    die;
                }

                $this->db->where('id_internal_proposal_process', $internal_proposal_process['id']);
                $this->db->where('id_internal_proposal', $internal_proposal_process['id_internal_proposal']);
                $this->db->where('isCheckNot', 1);
                $tinternal_proposal_inspection_criteria_process = $this->db->get('tbl_tinternal_proposal_inspection_criteria_process')->result_array();
                foreach ($tinternal_proposal_inspection_criteria_process as $keyv => $vvchild) {
                    $production_report = get_table_where('tblproduction_report', ['id_internal_proposal' => $internal_proposal_process['id_internal_proposal'], 'id_internal_proposal_process' => $internal_proposal_process['id'], 'id_internal_proposal_process_child' => $vvchild['inspection_criteria']], '', 'row_array');
                    if (!empty($production_report)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Đã tồn tại phiếu báo cáo không phù hợp, Không thể bỏ duyệt bước này');
                        echo json_encode($data);
                        die;
                    }
                }
                $this->db->where('id_internal_proposal', $id);
                $this->db->where('id >', $status);
                $this->db->order_by('id', 'asc');
                $check_status_bef = $this->db->get('tbl_internal_proposal_process')->row_array();
                if (!empty($check_status_bef)) {
                    if ($check_status_bef['status'] != 0) {
                        $data['result'] = 0;
                        $data['message'] = lang('Bước ' . $check_status_bef['name'] . ' chưa bỏ duyệt duyệt, Không thể bỏ duyệt bước này');
                        echo json_encode($data);
                        die;
                    }
                }
                $options = [
                    'status' => NULL,
                    'date_status' => NULL,
                    'staff_id' => NULL,
                    'reason' => null,
                ];
                $success = true;
                $message = _l('Bỏ duyệt thành công');
                $this->db->where('id_internal_proposal_process', $internal_proposal_process['id']);
                $this->db->where('id_internal_proposal', $internal_proposal_process['id_internal_proposal']);
                $success = $this->db->delete('tbl_tinternal_proposal_inspection_criteria_process');
            }
            $this->db->where('id', $status);
            $success = $this->db->update('tbl_internal_proposal_process', $options);
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
        die;
    }

    function add_task_process()
    {
        $_data = $this->input->post();
        $isCheck = !empty($_data['isCheck']) ? $_data['isCheck'] : NULL;
        $isCheckNot = !empty($_data['isCheckNot']) ? $_data['isCheckNot'] : NULL;
        $id = $this->input->post('id');
        $process_id = $this->input->post('process_id');
        $detail_id = $this->input->post('detail_id');
        $this->db->where('id', $detail_id);
        $this->db->where('id_internal_proposal', $id);
        $internal_proposal_process = $this->db->get('tbl_internal_proposal_process')->row_array();
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        $status = 1;
        $type = 1;
        if (!empty($internal_proposal_process) && !empty($internal_proposal_process['status'])) {
            $data['result'] = 0;
            $data['message'] = lang('Đã có nhân viên thay đổi trạng thái');
            echo json_encode($data);
            die;
        }
        $this->db->where('id_internal_proposal', $id);
        $this->db->where('id <', $detail_id);
        $this->db->order_by('id', 'desc');
        $check_status_bef = $this->db->get('tbl_internal_proposal_process')->row_array();
        if (!empty($check_status_bef)) {
            if ($check_status_bef['status'] == 0) {
                $data['result'] = 0;
                $data['message'] = lang('Bước ' . $check_status_bef['name'] . ' chưa duyệt, Không thể duyệt bước này');
                echo json_encode($data);
                die;
            }
        }
        $CheckCreateBCKPH = $this->CheckCreateBCKPH($id, $process_id, $detail_id);
        if ($CheckCreateBCKPH == 2) {
            $data['result'] = 0;
            $data['message'] = lang('Có quy trình không duyệt chưa Có phiếu báo cáo không phù hợp chưa hoàn thành hết, Vui lòng kiểm tra lại');
            echo json_encode($data);
            die;
        }
        if ($CheckCreateBCKPH > 2) {
            if (!in_array($CheckCreateBCKPH, array_keys($isCheck))) {
                $data['result'] = 0;
                $data['message'] = lang('Có quy trình không duyệt chưa tạo phiếu báo cáo không phù hợp, Vui lòng kiểm tra lại');
                echo json_encode($data);
                die;
            }
        }
        if ($internal_proposal_process['bod'] == 1) {
            if (!$this->internal_proposal_model->isApproved($id)) {
                $this->db->where('id_internal_proposal', $id);
                $check_process = $this->db->get('tbl_internal_proposal_process')->row();
                if (empty($check_process)) {
                    if (!$internal_proposal->status_staff) {
                        echo json_encode(array(
                            'success' => false,
                            'message' => lang('Vui lòng duyệt nhân viên trước')
                        ));
                        die;
                    }
                } else {
                    if ($internal_proposal->status_staff == 2) {
                        echo json_encode(array(
                            'success' => false,
                            'message' => lang('Quản lý không duyệt,Không thể duyệt bước này')
                        ));
                        die;
                    }
                    $this->db->where('id_internal_proposal', $id);
                    $internal_proposal_purchase = $this->db->get('tbl_internal_proposal_purchase_items')->result_array();
                    $items_array = [];
                    $dem = 0;
                    foreach ($internal_proposal_purchase as $k => $v) {
                        $purchase_order = get_table_where('tblpurchases_items', array('id' => $v['id_purchases_items']));
                        foreach ($purchase_order as $key => $value) {
                            $dem++;
                            $mainQuantity_suppliers = $value['quantity_net'] - $value['quantity_create'] - $value['quantity_create_all'] - $value['quantity'];
                            if ($mainQuantity_suppliers < 0) {
                                $items_array[$dem] = $v;
                                $items_array[$dem]['quantity_net'] = $value['quantity_net'];
                                $items_array[$dem]['mainQuantity_suppliers'] = $mainQuantity_suppliers;
                                $items_array[$dem]['quanliti_po'] = $value['quantity_create'] + $value['quantity_create_all'];
                            }
                        }
                    }
                    $this->db->where('id_internal_proposal', $id);
                    $this->db->group_start();
                    $this->db->where('bod', 1);
                    // $this->db->or_where('bod', 5);
                    $this->db->group_end();
                    $process = $this->db->get('tbl_internal_proposal_process')->row();
                    $this->db->where('id_internal_proposal', $id);
                    $this->db->where('status', 0);
                    $this->db->where('id <', $process->id);
                    $check_status = $this->db->get('tbl_internal_proposal_process')->row();
                    if (!empty($check_status)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng duyệt nhân viên trước');
                        echo json_encode($data);
                        die;
                    }
                    $options = [
                        'status' => 1,
                        'date_status' => date('Y-m-d H:i:s'),
                        'staff_id' => get_staff_user_id(),
                        'reason' => null,
                    ];
                    $this->db->where('id', $process->id);
                    $success = $this->db->update('tbl_internal_proposal_process', $options);
                }

                $staff = get_staff_user_id();
                $data = [
                    'approved_by' => $staff,
                    'status' => 1,
                    'reason' => null,
                ];
                $this->db->where('id', $id);
                $success = $this->db->update('tblinternal_proposal', $data);
                if ($success) {
                    $id_tasks = $this->createTaskAuto($id);
                    $listArrayPurchase = $this->createPurchaseAuto($id);
                    $this->create_suggestion_purchase($id);
                    if (!empty($internal_proposal->type_plan_propose)) {
                        $this->createAutoplan_propose($id, $listArrayPurchase);
                    }
                    $check_purchase = get_table_where('tblpurchase_order', array('id_internal_proposal' => $id), '', 'row');
                    if (empty($check_purchase)) {
                        if ($internal_proposal->category_recommended_id == 41) {
                            $this->create_suggestion_new_muiti($id);
                        } else {
                            $this->create_suggestion_new($id);
                        }
                    }
                    $success = true;
                    $message = _l('ch_successful_approval');
                    $staff_assigned = $this->db->get_where(
                        'tblinternal_proposal_assigned',
                        ['id_internal_proposal' => $id]
                    )->result_array();
                    if (!empty($staff_assigned)) {
                        $dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
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
                                'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                                'title' => 'Đổi trạng thái phiếu đề xuất nội bộ',
                                'code' => $internal_proposal->code,
                                'object_type' => 'internal_proposal'
                            ], [$value['id_staff']], get_staff_user_id());
                        }
                    }
                } else {
                    $success = false;
                    $message = _l('ch_no_successful_approval');
                }
            } else {
                $check_orders = get_table_where('tblpurchase_order', array('id_internal_proposal' => $id));
                if (!empty($check_orders)) {
                    $success = false;
                    $message = _l('Vui lòng xóa PO liên quan đến YCMH để bỏ duyệt');
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                    die;
                }
                $check_orders = get_table_where('tblplan_propose', array('id_internal_proposal' => $id));
                if (!empty($check_orders)) {
                    $success = false;
                    $message = _l('Vui lòng xóa phiếu để hoạch để bỏ duyệt');
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                    die;
                }
                $this->db->where('id_internal_proposal', $id);
                $this->db->group_start();
                $this->db->where('bod', 1);
                // $this->db->or_where('bod', 5);
                $this->db->group_end();
                $process = $this->db->get('tbl_internal_proposal_process')->row();
                if (!is_admin()) {
                    $staffid = get_staff_user_id();
                    $this->db->where('staffid', $staffid);
                    $check_role = $this->db->get('tblstaff')->row();
                    if ($process->roles != $check_role->role) {
                        $data['result'] = 0;
                        $data['message'] = lang('Bạn không thuộc vị trí duyệt trạng thái này');
                        echo json_encode($data);
                        die;
                    }
                }
                $options = [
                    'status' => 0,
                    'date_status' => NULL,
                    'staff_id' => NULL,
                    'reason' => null,
                ];
                $this->db->where('id', $process->id);
                $success = $this->db->update('tbl_internal_proposal_process', $options);
                $data = [
                    'approved_by' => 0,
                    'status' => 0,
                    'reason' => null,
                ];
                $this->db->where('id', $id);
                $success = $this->db->update('tblinternal_proposal', $data);
                if ($success) {
                    $success = true;
                    $message = _l('ch_successful_approval_cance');
                    $contentStatus = '';
                    if ($internal_proposal->status == 2) {
                        $contentStatus = ' không';
                    }
                    $staff_assigned = $this->db->get_where(
                        'tblinternal_proposal_assigned',
                        ['id_internal_proposal' => $id]
                    )->result_array();
                    if (!empty($staff_assigned)) {
                        $dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa hủy' . $contentStatus . ' duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
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
                                'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa hủy' . $contentStatus . ' duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                                'title' => 'Đổi trạng thái phiếu đề xuất nội bộ',
                                'code' => $internal_proposal->code,
                                'object_type' => 'internal_proposal'
                            ], [$value['id_staff']], get_staff_user_id());
                        }
                    }
                } else {
                    $success = false;
                    $message = _l('ch_no_successful_approval_cance');
                }
            }
        } else {
            $options = [
                'status' => 1,
                'date_status' => date('Y-m-d H:i:s'),
                'staff_id' => get_staff_user_id(),
                'reason' => null,
            ];
            $this->db->where('id', $detail_id);
            $success = $this->db->update('tbl_internal_proposal_process', $options);
            if ($internal_proposal_process['bod'] == 2) {
                $options = [
                    'status_staff' => $status,
                    'date_status_staff' => date('Y-m-d H:i:s'),
                    'user_status_staff' => get_staff_user_id()
                ];
                $this->db->where('id', $id);
                $success = $this->db->update('tblinternal_proposal', $options);
            }
        }
        if (!empty($success)) {
            if (!empty($isCheck)) {
                foreach ($isCheck as $key => $value) {
                    $ins_detail = [];
                    $ins_detail['id_internal_proposal'] = $id;
                    $ins_detail['id_internal_proposal_process'] = $detail_id;
                    $ins_detail['process_id	'] = $process_id;
                    $ins_detail['inspection_criteria'] = $key;
                    $ins_detail['isCheck'] = 1;
                    $this->db->insert('tbl_tinternal_proposal_inspection_criteria_process', $ins_detail);
                }
            }
            if (!empty($isCheckNot)) {
                foreach ($isCheckNot as $key => $value) {
                    $ins_detail = [];
                    $ins_detail['id_internal_proposal'] = $id;
                    $ins_detail['id_internal_proposal_process'] = $detail_id;
                    $ins_detail['process_id	'] = $process_id;
                    $ins_detail['inspection_criteria'] = $key;
                    $ins_detail['isCheckNot'] = 1;
                    $this->db->insert('tbl_tinternal_proposal_inspection_criteria_process', $ins_detail);
                }
            }
            $data['success'] = true;
            $data['id_task'] = (!empty($id_tasks) ? $id_tasks : NULL);
            $data['alert_type'] = 'success';
            $data['message'] = lang('Duyệt thành công');
            echo json_encode($data);
            die;
        } else {
            $data['result'] = true;
            $data['alert_type'] = 'warning';
            $data['message'] = lang('Duyệt không thành công');
            echo json_encode($data);
            die;
        }
        //end cong
    }
    function add_task_process_reject()
    {
        $_data = $this->input->post();
        $success = true;
        // echo '<pre>';print_arrays($_data);die;
        $isCheck = !empty($_data['isCheck']) ? $_data['isCheck'] : NULL;
        $isCheckNot = !empty($_data['isCheckNot']) ? $_data['isCheckNot'] : NULL;
        $id = $this->input->post('id');
        $process_id = $this->input->post('process_id');
        $detail_id = $this->input->post('detail_id');
        $this->db->where('id', $detail_id);
        $this->db->where('id_internal_proposal', $id);
        $internal_proposal_process = $this->db->get('tbl_internal_proposal_process')->row_array();
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        $status = 1;
        $type = 1;
        if (!empty($internal_proposal_process) && !empty($internal_proposal_process['status'])) {
            $data['result'] = 0;
            $data['message'] = lang('Đã có nhân viên thay đổi trạng thái');
            echo json_encode($data);
            die;
        }
        $this->db->where('id_internal_proposal', $id);
        $this->db->where('id <', $detail_id);
        $this->db->order_by('id', 'desc');
        $check_status_bef = $this->db->get('tbl_internal_proposal_process')->row_array();
        if (!empty($check_status_bef)) {
            if ($check_status_bef['status'] == 0) {
                $data['result'] = 0;
                $data['message'] = lang('Bước ' . $check_status_bef['name'] . ' chưa duyệt, Không thể duyệt bước này');
                echo json_encode($data);
                die;
            }
        }
        if ($internal_proposal_process['bod'] == 1) {
            if (!$this->internal_proposal_model->isApproved($id)) {
                $this->db->where('id_internal_proposal', $id);
                $check_process = $this->db->get('tbl_internal_proposal_process')->row();
                if (empty($check_process)) {
                    if (!$internal_proposal->status_staff) {
                        echo json_encode(array(
                            'success' => false,
                            'message' => lang('Vui lòng duyệt nhân viên trước')
                        ));
                        die;
                    }
                } else {
                    if ($internal_proposal->status_staff == 2) {
                        echo json_encode(array(
                            'success' => false,
                            'message' => lang('Quản lý không duyệt,Không thể duyệt bước này')
                        ));
                        die;
                    }
                    $this->db->where('id_internal_proposal', $id);
                    $internal_proposal_purchase = $this->db->get('tbl_internal_proposal_purchase_items')->result_array();
                    $items_array = [];
                    $dem = 0;
                    foreach ($internal_proposal_purchase as $k => $v) {
                        $purchase_order = get_table_where('tblpurchases_items', array('id' => $v['id_purchases_items']));
                        foreach ($purchase_order as $key => $value) {
                            $dem++;
                            $mainQuantity_suppliers = $value['quantity_net'] - $value['quantity_create'] - $value['quantity_create_all'] - $value['quantity'];
                            if ($mainQuantity_suppliers < 0) {
                                $items_array[$dem] = $v;
                                $items_array[$dem]['quantity_net'] = $value['quantity_net'];
                                $items_array[$dem]['mainQuantity_suppliers'] = $mainQuantity_suppliers;
                                $items_array[$dem]['quanliti_po'] = $value['quantity_create'] + $value['quantity_create_all'];
                            }
                        }
                    }
                    $this->db->where('id_internal_proposal', $id);
                    $this->db->group_start();
                    $this->db->where('bod', 1);
                    // $this->db->or_where('bod', 5);
                    $this->db->group_end();
                    $process = $this->db->get('tbl_internal_proposal_process')->row();
                    $this->db->where('id_internal_proposal', $id);
                    $this->db->where('status', 0);
                    $this->db->where('id <', $process->id);
                    $check_status = $this->db->get('tbl_internal_proposal_process')->row();
                    if (!empty($check_status)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng duyệt nhân viên trước');
                        echo json_encode($data);
                        die;
                    }
                }
            } else {
                $check_orders = get_table_where('tblpurchase_order', array('id_internal_proposal' => $id));
                if (!empty($check_orders)) {
                    $success = false;
                    $message = _l('Vui lòng xóa PO liên quan đến YCMH để bỏ duyệt');
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                    die;
                }
                $check_orders = get_table_where('tblplan_propose', array('id_internal_proposal' => $id));
                if (!empty($check_orders)) {
                    $success = false;
                    $message = _l('Vui lòng xóa phiếu để hoạch để bỏ duyệt');
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                    die;
                }
                $this->db->where('id_internal_proposal', $id);
                $this->db->group_start();
                $this->db->where('bod', 1);
                // $this->db->or_where('bod', 5);
                $this->db->group_end();
                $process = $this->db->get('tbl_internal_proposal_process')->row();
                if (!is_admin()) {
                    $staffid = get_staff_user_id();
                    $this->db->where('staffid', $staffid);
                    $check_role = $this->db->get('tblstaff')->row();
                    if ($process->roles != $check_role->role) {
                        $data['result'] = 0;
                        $data['message'] = lang('Bạn không thuộc vị trí duyệt trạng thái này');
                        echo json_encode($data);
                        die;
                    }
                }
            }
        }
        $CheckCreateBCKPH = $this->CheckCreateBCKPH($id, $process_id, $detail_id);
        if ($CheckCreateBCKPH == 2) {
            $data['result'] = 0;
            $data['message'] = lang('Có quy trình không duyệt chưa Có phiếu báo cáo không phù hợp chưa hoàn thành hết, Vui lòng kiểm tra lại');
            echo json_encode($data);
            die;
        }
        if ($CheckCreateBCKPH > 2) {
            $data['result'] = 0;
            $data['message'] = lang('Có quy trình không duyệt chưa tạo phiếu báo cáo không phù hợp, Vui lòng kiểm tra lại');
            echo json_encode($data);
            die;
        }
        if (!empty($success)) {
            if (!empty($isCheck)) {
                foreach ($isCheck as $key => $value) {
                    $ins_detail = [];
                    $ins_detail['id_internal_proposal'] = $id;
                    $ins_detail['id_internal_proposal_process'] = $detail_id;
                    $ins_detail['process_id	'] = $process_id;
                    $ins_detail['inspection_criteria'] = $key;
                    $check_tinternal_proposal_inspection_criteria_process = get_table_where('tbl_tinternal_proposal_inspection_criteria_process', array('id_internal_proposal' => $id, 'id_internal_proposal_process' => $detail_id, 'inspection_criteria' => $key), '', 'row_array');
                    if (!empty($check_tinternal_proposal_inspection_criteria_process)) {
                        $sins_detail['isCheck'] = 1;
                        $sins_detail['isCheckNot'] = NULL;
                        $this->db->where('id', $check_tinternal_proposal_inspection_criteria_process['id']);
                        $this->db->update('tbl_tinternal_proposal_inspection_criteria_process', $sins_detail);
                    } else {
                        $ins_detail['isCheck'] = 1;
                        $this->db->insert('tbl_tinternal_proposal_inspection_criteria_process', $ins_detail);
                    }
                }
            }
            if (!empty($isCheckNot)) {
                foreach ($isCheckNot as $key => $value) {
                    $ins_detail = [];
                    $ins_detail['id_internal_proposal'] = $id;
                    $ins_detail['id_internal_proposal_process'] = $detail_id;
                    $ins_detail['process_id	'] = $process_id;
                    $ins_detail['inspection_criteria'] = $key;
                    $check_tinternal_proposal_inspection_criteria_process = get_table_where('tbl_tinternal_proposal_inspection_criteria_process', array('id_internal_proposal' => $id, 'id_internal_proposal_process' => $detail_id, 'inspection_criteria' => $key), '', 'row_array');
                    if (!empty($check_tinternal_proposal_inspection_criteria_process)) {
                        $sins_detail['isCheck'] = NULL;
                        $sins_detail['isCheckNot'] = 1;
                        $this->db->where('id', $check_tinternal_proposal_inspection_criteria_process['id']);
                        $this->db->update('tbl_tinternal_proposal_inspection_criteria_process', $sins_detail);
                    } else {
                        $ins_detail['isCheckNot'] = 1;
                        $this->db->insert('tbl_tinternal_proposal_inspection_criteria_process', $ins_detail);
                    }
                }
            }
            $data['success'] = true;
            $data['id_task'] = (!empty($id_tasks) ? $id_tasks : NULL);
            $data['alert_type'] = 'success';
            $data['message'] = lang('Duyệt thành công');
            echo json_encode($data);
            die;
        } else {
            $data['result'] = true;
            $data['alert_type'] = 'warning';
            $data['message'] = lang('Duyệt không thành công');
            echo json_encode($data);
            die;
        }
        //end cong
    }
    public function not_approve()
    {
        if (!$this->approvett) {
            // ajax_access_denied();
            echo json_encode(array(
                'success' => false,
                'message' => lang('Bạn không có quyền duyệt')
            ));
            die;
        }
        $success = false;
        $message = _l('c_not_approved_fail');
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $reason = $this->input->post('reason');

        $this->db->where('id', $status);
        $this->db->where('id_internal_proposal', $id);
        $internal_proposal_process = $this->db->get('tbl_internal_proposal_process')->row_array();
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        if ($internal_proposal_process['bod'] != 1) {
            $options = [
                'status' => 2,
                'date_status' => date('Y-m-d H:i:s'),
                'staff_id' => get_staff_user_id(),
                'reason' => $reason,
            ];
            $this->db->where('id', $status);
            $success = $this->db->update('tbl_internal_proposal_process', $options);
        } else {
            if (!$this->internal_proposal_model->isApproved($id)) {
                $this->db->where('id_internal_proposal', $id);
                $this->db->group_start();
                $this->db->where('bod', 1);
                // $this->db->or_where('bod', 5);
                $this->db->group_end();
                $process = $this->db->get('tbl_internal_proposal_process')->row();
                if (!is_admin()) {
                    $staffid = get_staff_user_id();
                    $this->db->where('staffid', $staffid);
                    $check_role = $this->db->get('tblstaff')->row();
                    if ($process->roles != $check_role->role) {
                        $data['result'] = 0;
                        $data['message'] = lang('Bạn không thuộc vị trí duyệt trạng thái này');
                        echo json_encode($data);
                        die;
                    }
                }
                $options = [
                    'status' => 2,
                    'date_status' => date('Y-m-d H:i:s'),
                    'staff_id' => get_staff_user_id(),
                    'reason' => $reason,
                ];
                $this->db->where('id', $process->id);
                $success = $this->db->update('tbl_internal_proposal_process', $options);

                $staff = get_staff_user_id();
                $data = [
                    'approved_by' => $staff,
                    'status' => 2,
                    'reason' => $reason,
                ];
                $this->db->where('id', $id);
                $success = $this->db->update('tblinternal_proposal', $data);
            }


            if ($success) {
                $success = true;
                $message = _l('c_not_approved_success');
                $staff_assigned = $this->db->get_where(
                    'tblinternal_proposal_assigned',
                    ['id_internal_proposal' => $id]
                )->result_array();
                if (!empty($staff_assigned)) {
                    $dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name() . ' Vừa không duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
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
                            'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa không duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                            'title' => 'Đổi trạng thái phiếu đề xuất nội bộ',
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
                        $this->db->where_in('tblinternal_proposal.id_branch', $list_branch);
                        $this->db->group_end();
                        $this->db->where('id', $id);
                        $ktData = $this->db->get('tblinternal_proposal')->row();
                    } else {
                        $ktData = false;
                    }
                    if (empty($ktData)) {
                        accessDenied($js = true);
                    }
                }
            }
            $this->db->select(
                'tblinternal_proposal.*,
				tblcategory_tasks.code as code_category, 
				tblcategory_tasks.content as content_category,
				CONCAT(tblpurchases.prefix, tblpurchases.code) as code_purchase,
				CONCAT(tbl_services.prefix, tbl_services.code) as code_services,
				CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as code_purchase_order,
                tbl_category_recommended.name as name_category_recommended
				'
            );
            $this->db->where('tblinternal_proposal.id', $id);
            $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblinternal_proposal.category_tasks', 'left');
            $this->db->join('tblpurchases', 'tblpurchases.id = tblinternal_proposal.id_purchases', 'left');
            $this->db->join('tbl_services', 'tbl_services.id = tblinternal_proposal.id_service', 'left');
            $this->db->join('tbl_category_recommended', 'tbl_category_recommended.id = tblinternal_proposal.category_recommended_id', 'left');
            $this->db->join(
                'tblpurchase_order',
                'tblpurchase_order.id = tblinternal_proposal.id_purchase_order',
                'left'
            );
            $data['internal_proposal'] = $this->db->get('tblinternal_proposal')->row();
            if (!$this->perView && $this->perViewOwn) {
                if (!empty($data['internal_proposal'])) {
                    $this->db->where('tblinternal_proposal.id', $id);
                    $this->db->group_start();
                    $this->db->where('EXISTS (
								SELECT 1 
								FROM tblinternal_proposal_assigned 
								WHERE tblinternal_proposal_assigned.id_internal_proposal = tblinternal_proposal.id
								AND (tblinternal_proposal_assigned.id_staff = "' . get_staff_user_id() . '")
							)', false, false);
                    $this->db->or_where('tblinternal_proposal.staff', get_staff_user_id());
                    $this->db->group_end();
                    $internal_proposal = $this->db->get('tblinternal_proposal')->row();
                    if (empty($internal_proposal)) {
                        echo '<script>alert_float("danger", "Bạn không có quyền truy cập")</script>';
                        die();
                    }
                }
            }
            $dtTypePropose = !empty($data['internal_proposal']->type_plan_propose) ? $this->type_title_plan_propose[$data['internal_proposal']->type_plan_propose] : '';
            $data['internal_proposal']->type_plan_propose = !empty($dtTypePropose) ? $dtTypePropose : '';
            $dtRecommeded = get_table_where(
                'tbl_recommended_list',
                ['id' => $data['internal_proposal']->recommended_list_group_id],
                '',
                'row_array'
            );
            $data['internal_proposal']->recommended_list_group_name = !empty($dtRecommeded) ? $dtRecommeded['name'] : '';
            $dtDeparment = get_table_where(
                'tbl_room',
                ['id' => $data['internal_proposal']->id_departments],
                '',
                'row_array'
            );
            $data['internal_proposal']->department_name = !empty($dtDeparment) ? $dtDeparment['name'] : '';
            $dtRecommededList = get_table_where(
                'tbl_recommended_list',
                ['id' => $data['internal_proposal']->recommended_list_id],
                '',
                'row_array'
            );
            $data['internal_proposal']->recommeded_list_name = !empty($dtRecommededList) ? $dtRecommededList['name'] : '';

            $this->db->select('GROUP_CONCAT(tbl_recommended_list.name) as name');
            $this->db->from('tbl_recommended_list');
            $this->db->join('tblinternal_proposal_recommended', 'tblinternal_proposal_recommended.recommended_list_detail_id = tbl_recommended_list.id');
            $this->db->where('tblinternal_proposal_recommended.id_internal_proposal', $id);
            $dtRecommededListDetail = $this->db->get()->row_array();
            $data['internal_proposal']->recommeded_list_detail_name = !empty($dtRecommededListDetail) ? $dtRecommededListDetail['name'] : '';


            $this->db->where('id_internal_proposal', $id);
            $data['internal_proposal']->assigned = $this->db->get('tblinternal_proposal_assigned')->result_array();

            $this->db->where('id_internal_proposal', $id);
            $data['internal_proposal']->head_of_department = $this->db->get('tblinternal_proposal_head_of_department')->result_array();
            $this->db->where('id_internal_proposal', $id);
            $data['internal_proposal']->monitor = $this->db->get('tblinternal_proposal_monitor')->result_array();

            $this->db->where('id_internal_proposal', $id);
            $data['internal_proposal']->staff_pod = $this->db->get('tblinternal_proposal_staff_pod')->result_array();
            $this->db->where('rel_type', 'internal');
            $this->db->where('rel_id', $id);
            $data['files'] = $this->db->get('tblfiles')->result();

            $result = [];
            if (!empty($data['internal_proposal'])) {
                $this->db->select('tbl_category_recommended.id as id,tbl_category_recommended.name_table, ballot_type, type', false);
                $this->db->from('tbl_category_recommended');
                $this->db->where('tbl_category_recommended.id', $data['internal_proposal']->category_recommended_id);
                $rs = $this->db->get()->row_array();
                if (!empty($rs)) {
                    if ($rs['name_table'] == 'tblsuggest_test_item_quality') {
                        $this->db->select($rs['name_table'] . '.*');
                        if (!empty($rs['type'])) {
                            $this->db->where($rs['name_table'] . '.type', $rs['type']);
                        }
                        if (!empty($rs['ballot_type'])) {
                            if ($rs['ballot_type'] == 2) {
                                $this->db->select($rs['name_table'] . '.code_evaluate as code');
                                $this->db->where($rs['name_table'] . '.status', 1);
                            }
                        }
                        $this->db->where($rs['name_table'] . '.id', $data['internal_proposal']->suggest_id);
                        $this->db->from($rs['name_table']);
                        $result = $this->db->get()->row_array();
                    } else {
                        $this->db->select($rs['name_table'] . '.*');
                        $this->db->from($rs['name_table']);
                        $this->db->where($rs['name_table'] . '.id', $data['internal_proposal']->suggest_id);
                        $result = $this->db->get()->row_array();
                    }
                }
                if (!empty($result)) {
                    $reference_no = '';
                    if (!empty($result['reference_no'])) {
                        $reference_no = $result['reference_no'];
                    } elseif (!empty($result['code'])) {
                        $reference_no = $result['code'];
                    }
                    $staff_suggest_name = "";
                    if (!empty($result['staff_suggest'])) {
                        $staff_suggest_name = get_staff_full_name($result['staff_suggest']);
                    }
                    $result['reference_no'] = $reference_no;
                    $result['staff_suggest_name'] = $staff_suggest_name;
                }
            }

            $dtCategoryRecom = get_table_where('tbl_category_recommended', ['id' => $data['internal_proposal']->category_recommended_id], '', 'row_array');
            $html = '';
            if (!empty($dtCategoryRecom)) {
                if ($dtCategoryRecom['type_kpi'] == 1) {
                    $dtItems = [];
                    $this->db->select('tbl_suggest_kpi.*,tblroles.name as name_role');
                    $this->db->from('tbl_suggest_kpi');
                    $this->db->from('tblroles', 'tblroles.roleid = tbl_suggest_kpi.role_id');
                    $this->db->where('tbl_suggest_kpi.id', $data['internal_proposal']->suggest_id);
                    $dtData = $this->db->get()->row_array();

                    $this->db->select('
                        tbl_suggest_kpi_item.*,
                        tbl_category_kpi.name as name_category,
                        tbl_category_kpi_criteria.type as type,
                        tbl_category_kpi_criteria.name as name_kpi,
                        tbl_category_kpi_criteria.measure as measure,
                        tbl_category_kpi_criteria.code as code_kpi,
                        tbl_category_kpi_criteria.time as time,
                        tbl_suggest_kpi_item.weight as weight,
                        tbl_detail_task_detail.regulations as regulations,
                        0 as report,
                        tbl_result.name as name_result,
                    ');
                    $this->db->from('tbl_suggest_kpi_item');
                    $this->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tbl_suggest_kpi_item.category_kpi_id');
                    $this->db->join('tbl_category_kpi_criteria', 'tbl_category_kpi_criteria.id = tbl_suggest_kpi_item.category_kpi_criteria_id');
                    $this->db->join('tbl_detail_task_detail', 'tbl_detail_task_detail.category_kpi_criteria_id = tbl_category_kpi_criteria.id', 'left');
                    $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_kpi_item.result_id', 'left');
                    $this->db->where('tbl_suggest_kpi_item.suggest_kpi_id', $data['internal_proposal']->suggest_id);
                    $dtItems = $this->db->get()->result_array();
                    $month = $dtData['month'];
                    $year = $dtData['year'];
                    $staff_suggest = $dtData['staff_suggest'];
                    if (!empty($dtItems)) {
                        foreach ($dtItems as $key => $value) {
                            $category_kpi_criteria_id = $value['category_kpi_criteria_id'];
                            $tb_tamp = "(
                            SELECT
                                COUNT(tbl_production_report_kpi.category_kpi_criteria_id) as total
                            FROM tblproduction_report
                            JOIN tbl_production_report_kpi ON tbl_production_report_kpi.production_report_id = tblproduction_report.id
                            WHERE DATE_FORMAT(tblproduction_report.date, '%m') = $month AND DATE_FORMAT(tblproduction_report.date, '%Y') = $year
                            AND tblproduction_report.staff_responsible = $staff_suggest AND tbl_production_report_kpi.category_kpi_criteria_id = $category_kpi_criteria_id
                        )";
                            $dtProductionKpi = $this->db->query($tb_tamp)->row_array();
                            $dtItems[$key]['report'] = !empty($dtProductionKpi['total']) ? $dtProductionKpi['total'] : 0;
                        }
                    }
                    $body1 = '';
                    $body2 = '';
                    $stt1 = 0;
                    $stt2 = 0;
                    $total_weight1 = 0;
                    $total_weight2 = 0;
                    if (!empty($dtItems)) {
                        foreach ($dtItems as $key => $value) {
                            if ($value['type'] == 2) {
                                $stt2++;
                                $body2 .= ' <tr>
                            <td>
                                <div class="text-center">' . $stt2 . '</div>
                            </td>
                            <td>
                                <div>
                                    ' . $value['name_category'] . '
                                </div>
                            </td>
                            <td><div class="td_type">Năng Lực</div></td>
                            <td><div class="td_name_kpi">' . $value['name_kpi'] . '</div></td>
                            <td><div class="td_code_kpi">' . $value['code_kpi'] . '</div></td>
                            <td><div class="td_measure">' . $value['measure'] . '</div></td>
                            <td><div class="td_target text-center">' . $value['time'] . '</div></td>
                            <td><div class="td_weight text-center">' . $value['weight'] . '</div></td>
                            <td><div class="td_regulations">' . $value['regulations'] . '</div></td>
                            <td class="text-center">' . $value['report'] . ' Phiếu</td>
                            <td>
                                ' . $value['name_result'] . '
                            </td>
                        </tr>';
                                $total_weight2 += $value['weight'];
                            } else {
                                $stt1++;
                                $body1 .= ' <tr>
                        <td>
                            <div class="text-center">' . $stt1 . '</div>
                        </td>
                        <td>
                            <div>
                                ' . $value['name_category'] . '
                            </div>
                        </td>
                        <td><div class="td_type">Tuân Thủ</div></td>
                        <td><div class="td_name_kpi">' . $value['name_kpi'] . '</div></td>
                        <td><div class="td_code_kpi">' . $value['code_kpi'] . '</div></td>
                        <td><div class="td_measure">' . $value['measure'] . '</div></td>
                        <td><div class="td_target text-center">' . $value['time'] . '</div></td>
                        <td><div class="td_weight text-center">' . $value['weight'] . '</div></td>
                        <td><div class="td_regulations">' . $value['regulations'] . '</div></td>
                        <td class="text-center">' . $value['report'] . ' Phiếu</td>
                        <td>
                            ' . $value['name_result'] . '
                        </td>
                    </tr>';
                                $total_weight1 += $value['weight'];
                            }
                        }
                    }
                    $html = '<table class="table dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px">' . lang('STT') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Nhóm KPIS') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Loại KPIS') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Chi tiết KPIS') . '</th>
                                    <th class="text-center" style="width: 100px">' . lang('Mã KPIS') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Chỉ Số Đo Lường KPIs') . '</th>
                                    <th class="text-center" style="width: 80px"><' . lang('Target') . '</th>
                                    <th class="text-center" style="width: 80px">' . lang('Trọng số (%)') . '</th>
                                    <th class="text-center" style="width: 200px">' . lang('Tiêu chuẩn/ quy định') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Báo Cáo Không Phù Hợp') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Kết quả') . '</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . $body1 . '
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="total_weight text-center bold">' . $total_weight1 . '</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bold uppercase">% KPI</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center" style="color: red">' . (80 * $total_weight1 / 100) . '</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>';

                    $html .= '<table style="margin-top: 30px !important;" class="table dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px">' . lang('STT') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Nhóm KPIS') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Loại KPIS') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Chi tiết KPIS') . '</th>
                                    <th class="text-center" style="width: 100px">' . lang('Mã KPIS') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Chỉ Số Đo Lường KPIs') . '</th>
                                    <th class="text-center" style="width: 80px"><' . lang('Target') . '</th>
                                    <th class="text-center" style="width: 80px">' . lang('Trọng số (%)') . '</th>
                                    <th class="text-center" style="width: 200px">' . lang('Tiêu chuẩn/ quy định') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Báo Cáo Không Phù Hợp') . '</th>
                                    <th class="text-center" style="width: 150px">' . lang('Kết quả') . '</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . $body2 . '
                            </tbody>
                             <tfoot>
                                <tr>
                                    <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="total_weight text-center bold">' . $total_weight2 . '</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bold uppercase">% KPI</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center" style="color: red">' . (20 * $total_weight2 / 100) . '</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bold uppercase">Tổng KPI</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center" style="color: red">' . ((80 * $total_weight1 / 100) + (20 * $total_weight2 / 100)) . '</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>';
                }
            }
            $data['html'] = $html;
            $data['code_suggest'] = $result;
        }
        $data['title'] = 'Xem phiếu đề xuất nội bộ';
        if (empty($data['internal_proposal'])) {
            echo '<script>alert_float("danger", "Không tìm thấy phiếu đề xuất nội bộ")</script>';
            die();
        }
        $data['items'] = $this->purchases_model->get_items_internal_proposal($id);
        $this->load->view('admin/internal_proposal/view', $data);
    }

    public function removeFile($id = '')
    {
        $this->db->where('id', $id);
        $this->db->where('rel_type', 'internal');
        $get_file_delete = $this->db->get('tblfiles')->row();
        if (!empty($get_file_delete)) {
            $linkFile = FCPATH . 'uploads/internal_proposal/' . $get_file_delete->rel_id . '/' . $get_file_delete->file_name;
            if (!empty($linkFile)) {
                unlink($linkFile);
            }
            $this->db->where('id', $id);
            $this->db->where('rel_type', 'internal');
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
            'tblinternal_proposal.*,
				CONCAT(tblpurchases.prefix, tblpurchases.code) as code_purchase,
				CONCAT(tbl_services.prefix, tbl_services.code) as code_services,
				CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as code_purchase_order'
        );
        $this->db->join('tblpurchases', 'tblpurchases.id = tblinternal_proposal.id_purchases', 'left');
        $this->db->join('tbl_services', 'tbl_services.id = tblinternal_proposal.id_service', 'left');
        $this->db->join('tblpurchase_order', 'tblpurchase_order.id = tblinternal_proposal.id_purchase_order', 'left');
        $dataMain = $this->db->get_where('tblinternal_proposal', array('tblinternal_proposal.id' => $id))->row();
        $table = '';
        $data->content = '';
        $data->content .= '<span style="text-align: right; font-style: italic;">Ngày chứng từ: ' . _dhau($dataMain->date) . '</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">PHIẾU ĐỀ XUẤT NỘI BỘ</span><br><br>';
        $category_tasks = $this->db->get_where('tblcategory_tasks', ['id' => $dataMain->category_tasks])->row();
        //        $status = $this->internal_proposal_model->approvedBy($id);
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
            } elseif ($dataMain->status == 2) {
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
                            <span>' . ($category_tasks->code ?? '') . '</span>
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

        $qrStyle = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $data->qrCode = [
            'code' => 'internal_proposal||' . $id,
            'type' => 'QRCODE,Q',
            'x' => 170,
            'y' => 29,
            'width' => 25,
            'height' => 25,
            'style' => $qrStyle,
            'align' => 'N',
        ];

        $pdf = print_pdf($data);
        $type = 'I';
        $pdf->Output(slug_it('Phieu_de_xuat_tai_chinh') . '.pdf', $type);
    }

    public function SendEmailNoti($id_staff = [], $id_internal_proposal = '')
    {
        return true; // tạm thời đóng do ở local
        if (!empty($id_staff) && !empty($id_internal_proposal)) {
            $this->db->where_in('staffid', $id_staff);
            $this->db->where('active', 1);
            $staff = $this->db->get('tblstaff')->result_array();
            if (!empty($staff)) {
                $this->db->where('id', $id_internal_proposal);
                $internal_proposal = $this->db->get('tblinternal_proposal')->row();
                $list_staff = [];
                foreach ($staff as $key => $value) {
                    if (!empty($value['email'])) {
                        $list_staff[] = $value['email'];
                    }
                }
                $this->load->config('email');
                $template = new StdClass();
                $template->message = get_option('email_header') . '<br/> ' . get_staff_full_name() . ' Vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ ' . $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . '<br/>
				 <b>Số tiền:</b> ' . (!empty($internal_proposal->money) ? number_format_data($internal_proposal->money) : '0') . '<br/>
				 <b>Nội dung:</b> ' . $internal_proposal->content . '<br/>
				 Vui lòng theo dõi và tiến hành cập nhật!<br/>';
                $template->fromname = get_option('companyname') != '' ? get_option('companyname') : '';
                $template->subject = 'PHIẾU ĐỀ XUẤT NỘI BỘ';
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

    //dexuatnoibo
    public function items_purchases($id)
    {
        $id = trim($id, '_');
        $i = 0;
        $tbody = '';
        $tax = get_table_where('tbltaxes');
        $option = '';
        foreach ($tax as $t) {
            $option .= '<option value="' . $t['id'] . '" data-taxrate="' . $t['taxrate'] . '"> ' . $t['name'] . ' </option>';
        }
        foreach (explode('_', $id) as $k => $v) {
            $items = $this->purchases_model->get_items_purchases($v);
            $purchase = get_table_where('tblpurchases', array('id' => $v), '', 'row');
            foreach ($items as $key => $value) {
                $unit_name = $value['unit_name'];
                if ($value['unit_name'] == null) {
                    $unit_name = '';
                }
                $unit_name_payment = $value['unit_name_payment'];
                if ($value['unit_name_payment'] == null) {
                    $unit_name_payment = '';
                }
                $unit_name_stock = $value['unit_name_stock'];
                if ($value['unit_name_stock'] == null) {
                    $unit_name_stock = '';
                }
                $mainQuantity_suppliers = $value['quantity_net'] - $value['quantity_create'] - $value['quantity_create_all'];
                if ($mainQuantity_suppliers <= 0) {
                    continue;
                }
                $exchange_stock = $value['exchange_standard_unit'];
                $exchange_standard_unit = $value['exchange_unit'];
                $exchange_payment = $value['exchange_unit_payment'];
                $recipe = $value['recipe'];
                $paper = $value['paper'];
                $longs = $value['longs'];
                $wide = $value['wide'];
                $quantity_stock = ($mainQuantity_suppliers / $exchange_stock) * $exchange_standard_unit;
                if ($recipe == 1) {
                    $quantity_payment = (($mainQuantity_suppliers / $exchange_payment) * $exchange_standard_unit);
                } elseif ($recipe == 2) {
                    $quantity_payment = (($mainQuantity_suppliers / $exchange_payment) * $paper / 100);
                } elseif ($recipe == 3) {
                    $quantity_payment = (($mainQuantity_suppliers / $exchange_payment) * ($longs * $wide) / 10000);
                }
                $tbody .= '<tr>';
                $tbody .= '<td>
			<input type="hidden" class="id_purchases" id="id_purchases" name="items[' . $i . '][id_purchases]" value="' . $v . '" />
            <input type="hidden" class="id" id="id" name="items[' . $i . '][id]" value="' . $value['id'] . '" />
            <input type="hidden" class="id_items" id="id_items" name="items[' . $i . '][id_items]" value="' . $value['product_id'] . '" />
            <input type="hidden" class="type" id="type" name="items[' . $i . '][type]" value="' . $value['type'] . '" />
            <input type="hidden" class="recipe" id="recipe" name="items[' . $i . '][recipe]" value="' . $value['recipe'] . '" />
            <input type="hidden" class="paper" id="paper" name="items[' . $i . '][paper]" value="' . $value['paper'] . '" />
            <input type="hidden" class="longs" id="longs" name="items[' . $i . '][longs]" value="' . $value['longs'] . '" />
            <input type="hidden" class="wide" id="wide" name="items[' . $i . '][wide]" value="' . $value['wide'] . '" />
            ' . $value['name_item'] . ' (' . $value['code_item'] . ')<br><span class="label label-danger pull-left mtop5 text-center">' . $purchase->prefix . $purchase->code . '</span>
            </td>';
                $tbody .= '<td class="text-center sldx">' . formatNumber($mainQuantity_suppliers) . '</td>';
                $tbody .= '<td><input style="width: 60px" onchange="formatNumBerKeyUpCus(this)" class="height_auto H_input mainQuantity_suppliers" type="text" name="items[' . $i . '][quantity_suppliers]" value="' . formatNumber($mainQuantity_suppliers) . '" /><input style="width: 100px"  class="hide height_auto H_input exchange_standard_unit" type="text" name="items[' . $i . '][exchange_standard_unit]" value="' . $value['exchange_unit'] . '" /><span class="unit_name">/' . $unit_name . '</span></td>';
                $tbody .= '<td class="text-center"><span class="text_mainquantity_stock text-center">' . formatNumber($quantity_stock) . '</span><span class="unit_name_stock">/' . $unit_name_stock . '</span><input style="width: 100px"  class="hide height_auto H_input mainquantity_stock" type="text" name="items[' . $i . '][quantity_stock]" value="' . $quantity_stock . '" /><input style="width: 100px"  class=" hide height_auto H_input exchange_stock" type="text" name="items[' . $i . '][exchange_stock]" value="' . $value['exchange_standard_unit'] . '" /></td>';
                $tbody .= '<td class="text-center"><span class="text_mainquantity_payment">' . formatNumber($quantity_payment) . '</span><span class="unit_name_payment">/' . $unit_name_payment . '</span><input style="width: 100px"  class="hide height_auto H_input mainquantity_payment" type="text" name="items[' . $i . '][quantity_payment]" value="' . $quantity_payment . '" /><input style="width: 100px"  class="hide height_auto H_input exchange_payment" type="text" name="items[' . $i . '][exchange_payment]" value="' . $value['exchange_unit_payment'] . '" /></td>';
                $tbody .= '<td ><input style="width: 100%" onchange="formatNumBerKeyUpCus(this)" readonly class="no-drop height_auto H_input align_right price_suppliers"  type="text" name="items[' . $i . '][price_suppliers]" value="0" /></td>';
                $tbody .= '<td class="text-center">
					<select class="selectpicker tax" name="items[' . $i . '][tax_id]" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '">
					<option value data-taxrate="0">' . _l('no_tax') . '</option>
					' . $option . '
				</select>
				<input type="hidden" class="tax_rate" name="items[' . $i . '][tax_rate]" value="0">
				</td>';
                $tbody .= '<td class="total_suppliers text-right">0</td>';
                $tbody .= '<td class="category_supplier"></td>';
                $tbody .= '<td style="width:150px"><input type="hidden" class="count" value="' . $i . '" /><input style="width:200px" data-placeholder="' . _l('dropdown_non_selected_tex') . '" required="true" data-id_supp="' . $value['id_supp'] . '"  data-company_supp="' . $value['company_supp'] . '" class="suppliers_id" id="suppliers_id_' . $i . '" name="items[' . $i . '][suppliers_id]"  style="width: 100%"></td>';
                $tbody .= '<td>' . $value['note'] . '</td>';
                $tbody .= '<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>';
                $tbody .= '</tr>';
                $i++;
            }
        }
        $data['tbody'] = $tbody;
        $data['dem'] = $i;
        echo json_encode($data);
    }

    public function GetPirceSuppliers()
    {
        $data = $this->input->post();
        $this->db->select('tblsuppliers_price_detail.price');
        $this->db->where('product_id', $data['id_items']);
        $this->db->where('product_type', $data['type']);
        $this->db->where('supplier_id', $data['suppliers_id']);
        $this->db->join('tblsuppliers_price', 'tblsuppliers_price.id = tblsuppliers_price_detail.supplier_price_id');
        $this->db->order_by('tblsuppliers_price_detail.id desc');
        $price_data = $this->db->get('tblsuppliers_price_detail')->row();
        $price = 0;
        if (!empty($price_data)) {
            $price = $price_data->price;
        }
        echo json_encode($price);
    }

    public function getCategorySupplier()
    {
        $data = $this->input->post();
        $this->db->select('tblsuppliers_groups.name');
        $this->db->where('tblsuppliers.id', $data['suppliers_id']);
        $this->db->join('tblsuppliers_groups', 'tblsuppliers_groups.id = tblsuppliers.groups_in');
        $dtCategorySupplier = $this->db->get('tblsuppliers')->row();
        $name = '';
        if (!empty($dtCategorySupplier)) {
            $name = $dtCategorySupplier->name;
        }
        echo json_encode($name);
    }
    // public function create_suggestion_purchase($id = '')
    // {
    //     $staff_browse = get_staff_user_id();
    //     $this->db->where('id', $id);
    //     $internal_proposal = $this->db->get('tblinternal_proposal')->row();
    //     if (!empty($internal_proposal)) {
    //         if ($internal_proposal->status == 1) {
    //             $check_purchase = get_table_where('tblpurchase_order', array('id_internal_proposal' => $id));
    //             foreach ($check_purchase as $key => $value) {
    //                 $items_purchase = get_table_where(
    //                     'tblpurchase_order_items',
    //                     array('id_purchase_order' => $value['id'])
    //                 );
    //                 $items = [];
    //                 $money = 0;
    //                 foreach ($items_purchase as $k => $v) {
    //                     $items[$k]['id_items'] = $v['product_id'];
    //                     $items[$k]['type'] = $v['type'];
    //                     $items[$k]['quantity'] = $v['quantity_payment'];
    //                     $items[$k]['price'] = $v['price_suppliers'];
    //                     $items[$k]['tax_id'] = $v['tax_id'];
    //                     $items[$k]['tax_rate'] = $v['tax_rate'];
    //                     $items[$k]['amount'] = $v['price_suppliers'] * (1 + $v['tax_rate'] / 100) * $v['quantity_payment'];
    //                     $money += $v['price_suppliers'] * (1 + $v['tax_rate'] / 100) * $v['quantity_payment'];
    //                 }
    //                 $ins = array();
    //                 $ins['date'] = date('Y-m-d H:i:s');
    //                 $ins['code'] = 'DX-' . sprintf('%06d', ch_getMaxID('id', 'tblsuggestion') + 1);
    //                 $ins['type'] = 3;
    //                 $ins['status'] = 2;
    //                 $ins['note'] = '';
    //                 $ins['staffid'] = $internal_proposal->staff;
    //                 $ins['staff_create'] = get_staff_user_id();
    //                 $ins['date_create'] = date('Y-m-d H:i:s');
    //                 $ins['price_total'] = $money;
    //                 $ins['status_dn'] = 1;
    //                 $ins['staff_status_dn'] = $internal_proposal->staff;
    //                 $ins['date_status_dn'] = date('Y-m-d H:i:s');
    //                 $ins['id_branch'] = $internal_proposal->id_branch;
    //                 $ins['id_payment_modes'] = 3;
    //                 $ins['id_internal_proposal'] = $id;
    //                 $ins['purchase_order_id'] = $value['id'];
    //                 $ins['staff_browse'] = !empty($staff_browse) ? $staff_browse : null;
    //                 $this->db->insert('tblsuggestion', $ins);
    //                 $id_suggestion = $this->db->insert_id();
    //                 if (!empty($id_suggestion)) {
    //                     foreach ($items as $kk => $vv) {
    //                         $vv['id_suggestion'] = $id_suggestion;
    //                         $this->db->insert('tblsuggestion_detal', $vv);
    //                     }
    //                     $this->db->where('id', $id);
    //                     $this->db->update('tblinternal_proposal', ['id_suggestion' => -1]);
    //                 }
    //             }
    //         } else {
    //         }
    //     }
    // }
    public function create_suggestion_purchase($id = '')
    {
        $staff_browse = get_staff_user_id();
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        if (!empty($internal_proposal)) {
            if ($internal_proposal->status == 1) {
                $check_purchase = get_table_where('tblpurchase_order', array('id_internal_proposal' => $id));
                foreach ($check_purchase as $key => $value) {
                    $items_purchase = get_table_where(
                        'tblpurchase_order_items',
                        array('id_purchase_order' => $value['id'])
                    );
                    $items = [];
                    $money = 0;
                    foreach ($items_purchase as $k => $v) {
                        $items[$k]['id_items'] = $v['product_id'];
                        $items[$k]['type'] = $v['type'];
                        $items[$k]['quantity'] = $v['quantity_payment'];
                        $items[$k]['price'] = $v['price_suppliers'];
                        $items[$k]['tax_id'] = $v['tax_id'];
                        $items[$k]['tax_rate'] = $v['tax_rate'];
                        $items[$k]['amount'] = $v['price_suppliers'] * (1 + $v['tax_rate'] / 100) * $v['quantity_payment'];
                        $money += $v['price_suppliers'] * (1 + $v['tax_rate'] / 100) * $v['quantity_payment'];
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
                    $ins['price_total'] = $money;
                    $ins['status_dn'] = 1;
                    $ins['staff_status_dn'] = $internal_proposal->staff;
                    $ins['date_status_dn'] = date('Y-m-d H:i:s');
                    $ins['id_branch'] = $internal_proposal->id_branch;
                    $ins['id_payment_modes'] = 3;
                    $ins['id_internal_proposal'] = $id;
                    $ins['purchase_order_id'] = $value['id'];
                    $ins['staff_browse'] = !empty($staff_browse) ? $staff_browse : null;
                    $this->db->insert('tblsuggestion', $ins);
                    $id_suggestion = $this->db->insert_id();
                    if (!empty($id_suggestion)) {
                        foreach ($items as $kk => $vv) {
                            $vv['id_suggestion'] = $id_suggestion;
                            $this->db->insert('tblsuggestion_detal', $vv);
                        }
                        $this->db->where('id', $id);
                        $this->db->update('tblinternal_proposal', ['id_suggestion' => -1]);
                    }
                }
            } else {
            }
        }
    }

    public function getCodeplan_propose($id = '')
    {
        if (empty($id)) { // create new code
            $code = 'KH' . '-' . sprintf('%06d', ch_getMaxID('id', 'tblplan_propose') + 1);
        } else { // get existed
            $code = get_table_where('tblplan_propose', ['id' => $id], '', 'row', '', 'code')->code;
        }
        return $code;
    }

    public function createAutoplan_propose($id = '', $listIDPurchase = [])
    {
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        //			$this->db->where('id_internal_proposal', $id);
        //			$plan_propose_assigned = $this->db->get('tblinternal_proposal_assigned')->result_array();
        //			$_data = [
        //				'code' => $this->getCodeplan_propose(),
        //				'create_by' => get_staff_user_id(),
        //				'date' => date('Y-m-d'),
        //				'staff' => $internal_proposal->staff,
        //				'category_tasks' => $internal_proposal->category_tasks,
        //				'id_branch' => $internal_proposal->id_branch,
        //				'type_plan_propose' => $internal_proposal->type_plan_propose,
        //				'money' => $internal_proposal->money,
        //				'type_train' => NULL,
        //				'type_repair' => NULL,
        //				'type_items' => NULL,
        //				'type_recruit' => NULL,
        //				'content' => $internal_proposal->content,
        //				'id_internal_proposal' => $id,
        //			];
        //			$this->db->insert('tblplan_propose', $_data);
        //			$idd = $this->db->insert_id();
        //			if (!empty($plan_propose_assigned)) {
        //				$dataHtml = '
        //						<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '">
        //						Hệ thống - ' . get_staff_full_name() . ' Vừa thêm bạn vào người duyệt phiếu kế hoạch ' . $_data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
        //					';
        //				foreach ($plan_propose_assigned as $key => $value) {
        //					$this->db->insert('tblplan_propose_assigned', [
        //						'id_plan_propose' => $idd,
        //						'id_staff' => $value['id_staff']
        //					]);
        //					$notification_data = [
        //						'date' => date('Y-m-d H:i:s'),
        //						'description' => $dataHtml,
        //						'touserid' => $value['id_staff'],
        //						'link' => 'plan_propose/view/' . $idd,
        //						'type' => 13,
        //						'object_id' => $idd,
        //						'object_type' => 'plan_propose',
        //					];
        //					if (!empty($notification_data)) {
        //						$this->db->insert('tblnotifications', $notification_data);
        //						pusher_trigger_notification($notification_data);
        //					}
        //					send_notification_app_c($idd, [
        //						'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa thêm bạn vào người duyệt phiếu kế hoạch ' . $_data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
        //						'title' => 'Phiếu kế hoạch',
        //						'code' => $_data['code'],
        //						'object_type' => 'plan_propose'
        //					], [$value['id_staff']], get_staff_user_id());
        //				}
        //			}

        if (empty($listIDPurchase)) {
            //				if ($internal_proposal->money > 0 && $internal_proposal->type_plan_propose != 'pay_slip') {
            if ($internal_proposal->money > 0) {
                $this->createAutoplan_proposepay($id);
            }
        } else {
            foreach ($listIDPurchase as $key => $id_purchase_order) {
                $this->createAutoplan_proposepay($id, $id_purchase_order);
            }
        }
    }

    public function createAutoplan_proposepay($id = '', $id_purchase_order = 0)
    {
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        $this->db->where('id_internal_proposal', $id);
        $plan_propose_assigned = $this->db->get('tblinternal_proposal_assigned')->result_array();
        $_data = [
            'code' => $this->getCodeplan_propose(),
            'create_by' => get_staff_user_id(),
            'date' => date('Y-m-d'),
            'staff' => $internal_proposal->staff,
            'category_tasks' => $internal_proposal->category_tasks,
            'id_branch' => $internal_proposal->id_branch,
            'type_plan_propose' => 'pay_slip',
            'money' => $internal_proposal->money,
            'type_train' => null,
            'type_repair' => null,
            'type_items' => null,
            'type_recruit' => null,
            'content' => $internal_proposal->content,
            'id_internal_proposal' => $id,
        ];
        if (!empty($id_purchase_order)) {
            $this->db->where('id', $id_purchase_order);
            $purchase_order = $this->db->get('tblpurchase_order')->row();
            if (!empty($purchase_order)) {
                $_data['id_purchase_order_internal'] = $purchase_order->id;
                $_data['money'] = $purchase_order->totalAll_suppliers;
            }
        }
        if ($_data['money'] != 0) {
            $this->db->insert('tblplan_propose', $_data);

            $idd = $this->db->insert_id();
            if (!empty($plan_propose_assigned)) {
                $dataHtml = '
						<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
						Hệ thống - ' . get_staff_full_name() . ' Vừa thêm bạn vào người duyệt phiếu kế hoạch ' . $_data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
					';
                foreach ($plan_propose_assigned as $key => $value) {
                    $this->db->insert('tblplan_propose_assigned', [
                        'id_plan_propose' => $idd,
                        'id_staff' => $value['id_staff']
                    ]);
                    $notification_data = [
                        'date' => date('Y-m-d H:i:s'),
                        'description' => $dataHtml,
                        'touserid' => $value['id_staff'],
                        'link' => 'plan_propose/view/' . $idd,
                        'type' => 13,
                        'object_id' => $idd,
                        'object_type' => 'plan_propose',
                    ];
                    if (!empty($notification_data)) {
                        $this->db->insert('tblnotifications', $notification_data);
                        pusher_trigger_notification($notification_data);
                    }
                    send_notification_app_c($idd, [
                        'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa thêm bạn vào người duyệt phiếu kế hoạch ' . $_data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
                        'title' => 'Phiếu kế hoạch',
                        'code' => $_data['code'],
                        'object_type' => 'plan_propose'
                    ], [$value['id_staff']], get_staff_user_id());
                }
            }
        }
    }

    public function handling_status_internal()
    {
        $data = [];

        if (!$this->perApprove) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        $internal_proposal_id = $this->input->post('internal_proposal_id');
        $status = $this->input->post('status');
        $type = $this->input->post('type');
        if ($status == 0 && $type = 1) {
            $this->db->where('id', $internal_proposal_id);
            $internal_proposals = $this->db->get('tbl_internal_proposal_process')->row();
            if ($this->internal_proposal_model->isApproved($internal_proposals->id_internal_proposal)) {
                $this->db->where('id_internal_proposal', $internal_proposals->id_internal_proposal);
                $this->db->where('bod', 1);
                $this->db->where('id >', $internal_proposal_id);
                $check_status = $this->db->get('tbl_internal_proposal_process')->row();
                if (!empty($check_status)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng bỏ duyệt BOD trước');
                    echo json_encode($data);
                    die;
                }
            }
            if (!empty($internal_proposals->stages)) {
                $delivery_records_object = get_table_where('tbl_delivery_records', array('id_create' => $internal_proposals->id_internal_proposal, 'type_create' => 'internal_proposal'), '', 'row_array');
                if (!empty($delivery_records_object)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã có biên bản bàn giao ' . $delivery_records_object['reference_no'] . ', Không thể bỏ duyệt');
                    echo json_encode($data);
                    die;
                }
            }
        }
        if ($status == 1) {
            $this->db->where('id', $internal_proposal_id);
            $internal_proposals = $this->db->get('tbl_internal_proposal_process')->row();
            $this->db->where('id_internal_proposal', $internal_proposals->id_internal_proposal);
            $this->db->where('id <', $internal_proposal_id);
            $this->db->order_by('id', 'desc');
            $check_status_bef = $this->db->get('tbl_internal_proposal_process')->row_array();
            if (!empty($check_status_bef)) {
                if ($check_status_bef['status'] == 0) {
                    $data['result'] = 0;
                    $data['message'] = lang('Bước ' . $check_status_bef['name'] . ' chưa duyệt, Không thể duyệt bước này');
                    echo json_encode($data);
                    die;
                }
            }
        }
        if ($status == 0) {
            $this->db->where('id', $internal_proposal_id);
            $internal_proposals = $this->db->get('tbl_internal_proposal_process')->row();
            $this->db->where('id_internal_proposal', $internal_proposals->id_internal_proposal);
            $this->db->where('id >', $internal_proposal_id);
            $this->db->order_by('id', 'asc');
            $check_status_bef = $this->db->get('tbl_internal_proposal_process')->row_array();
            if (!empty($check_status_bef)) {
                if ($check_status_bef['status'] == 1) {
                    $data['result'] = 0;
                    $data['message'] = lang('Bước ' . $check_status_bef['name'] . ' chưa bỏ duyệt duyệt, Không thể bỏ duyệt bước này');
                    echo json_encode($data);
                    die;
                }
            }
        }
        if (empty($type)) {
            $this->db->where('id', $internal_proposal_id);
            $internal_proposal = $this->db->get('tblinternal_proposal')->row();
            if ($status == $internal_proposal->status_staff) {
                $data['result'] = 0;
                $data['message'] = lang('Đã có nhân viên thay đổi trạng thái');
                echo json_encode($data);
                die;
            }
            $options = [
                'status_staff' => $status,
                'date_status_staff' => date('Y-m-d H:i:s'),
                'user_status_staff' => get_staff_user_id()
            ];
            $this->db->where('id', $internal_proposal_id);
            $success = $this->db->update('tblinternal_proposal', $options);
        } else {
            $this->db->where('id', $internal_proposal_id);
            $internal_proposal = $this->db->get('tbl_internal_proposal_process')->row();
            if ($status == $internal_proposal->status) {
                $data['result'] = 0;
                $data['message'] = lang('Đã có nhân viên thay đổi trạng thái');
                echo json_encode($data);
                die;
            }
            // if (!is_admin()) {
            //     $this->db->where('id', $internal_proposal->id_process);
            //     $process = $this->db->get('tbl_recommended_list_process')->row();
            //     $staffid = get_staff_user_id();
            //     $this->db->where('staffid', $staffid);
            //     $check_role = $this->db->get('tblstaff')->row();
            //     if ($internal_proposal->roles != $check_role->role) {
            //         $data['result'] = 0;
            //         $data['message'] = lang('Bạn không thuộc vị trí duyệt trạng thái này');
            //         echo json_encode($data);
            //         die;
            //     }
            // }
            if ($status == 0) {
                $options = [
                    'status' => 0,
                    'date_status' => NULL,
                    'staff_id' => NULL
                ];
            } else {
                $options = [
                    'status' => $status,
                    'date_status' => date('Y-m-d H:i:s'),
                    'staff_id' => get_staff_user_id()
                ];
            }
            $this->db->where('id', $internal_proposal_id);
            $success = $this->db->update('tbl_internal_proposal_process', $options);
        }
        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function export_excel()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => array(
                'name' => 'Times New Roman'
            ),
        ]);

        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $tbrecommended_detail = '(SELECT
            tblinternal_proposal_recommended.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(tbl_recommended_list.code SEPARATOR "; ") as code,
            GROUP_CONCAT(tbl_recommended_list.name SEPARATOR "; ") as name
        FROM tblinternal_proposal_recommended
        INNER JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal_recommended.recommended_list_detail_id
        GROUP BY tblinternal_proposal_recommended.id_internal_proposal
        ) AS tbrecommended_detail';

        $tbstaff_assigned = '(SELECT
            tblinternal_proposal_assigned.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(CONCAT(tblstaff.firstname, " ", tblstaff.lastname) SEPARATOR "; ") as staff_name
            FROM tblinternal_proposal_assigned
            INNER JOIN tblstaff ON tblstaff.staffid = tblinternal_proposal_assigned.id_staff
            GROUP BY tblinternal_proposal_assigned.id_internal_proposal
        ) AS tbstaff_assigned';

        $tbstaff_bod = '(SELECT
            tblinternal_proposal_staff_pod.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(CONCAT(tblstaff.firstname, " ", tblstaff.lastname) SEPARATOR "; ") as staff_name
            FROM tblinternal_proposal_staff_pod
            INNER JOIN tblstaff ON tblstaff.staffid = tblinternal_proposal_staff_pod.id_staff
            GROUP BY tblinternal_proposal_staff_pod.id_internal_proposal
        ) AS tbstaff_bod';

        // $tbpurchase = '(SELECT
        //     tblinternal_proposal_purchase.id_internal_proposal AS id_internal_proposal,
        //     GROUP_CONCAT(CONCAT(tblpurchases.prefix, "-", tblpurchases.code) SEPARATOR "; ") as purchase
        //     FROM tblinternal_proposal_purchase
        //     INNER JOIN tblpurchases ON tblpurchases.id = tblinternal_proposal_purchase.id_purchases
        //     GROUP BY tblinternal_proposal_purchase.id_internal_proposal
        // ) AS tbpurchase';

        $tblpurchase_order = '(SELECT
            tblpurchase_order.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(CONCAT(tblpurchase_order.prefix, tblpurchase_order.code) SEPARATOR "; ") as code
        FROM tblpurchase_order
        GROUP BY tblpurchase_order.id_internal_proposal
        ) AS tblpurchase_order';

        $tblsuggestion = '(SELECT
            tblsuggestion.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(tblsuggestion.code SEPARATOR "; ") as code
        FROM tblsuggestion
        GROUP BY tblsuggestion.id_internal_proposal
        ) AS tblsuggestion';

        $tblplan_propose = '(SELECT
            tblplan_propose.id_internal_proposal AS id_internal_proposal,
            GROUP_CONCAT(tblplan_propose.code SEPARATOR "; ") as code
        FROM tblplan_propose
        GROUP BY tblplan_propose.id_internal_proposal
        ) AS tblplan_propose';

        $this->db->select('
            tblinternal_proposal.id as id,
            tblinternal_proposal.date as date,
            tblinternal_proposal.date_finish as date_finish,
            tblinternal_proposal.code as code,
            tblinternal_proposal.type_plan_propose as plan_type,
            tb_type.name as type,
            tb_group.name as group,
            tbrecommended_detail.name as detail,
            tblbranch.name as branch,
            tbl_room.name as department,
            tblcategory_tasks.content as task,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff,
            tbstaff_assigned.staff_name as staff_assigned,
            tbstaff_bod.staff_name as staff_bod,
            CONCAT(tblpurchases.prefix, tblpurchases.code) as purchase,
            CONCAT(tbl_services.prefix, tbl_services.code) as service,
            tblpurchase_order.code as purchase_order,
            tblsuggestion.code as financial_proposal,
            tblplan_propose.code as plan,
            IF(tbl_internal_proposal_purchase_items.id_purchases_items > 0,(tbl_internal_proposal_purchase_items.quantity_payment*tbl_internal_proposal_purchase_items.price*(IF(tbl_internal_proposal_purchase_items.tax_rate > 0,(1 +(tbl_internal_proposal_purchase_items.tax_rate/100)),1))),tblinternal_proposal.money) as money,
            tblinternal_proposal.content as content,
            tbl_materials.code as item_code,
            tbl_materials.name as item_name,
            tblunits.unit as item_unit,
            tblpurchases_items.quantity as item_all_quanliti,
            tblpurchases_items.quantity_net as item_all_quanliti_net,
            (tbl_internal_proposal_purchase_items.quantity) as item_all_quanliti_order,
            (tblpurchases_items.quantity_net - tbl_internal_proposal_purchase_items.quantity) as item_all_quanliti_left,
            tblsuppliers.company as company_supp
        ');

        // 'item_code' => 'Mặt hàng',
        //     'item_name' => 'Tên hàng',
        //     'item_unit' => 'ĐVT',
        //     'item_all_quanliti' => 'Tổng số lượng',
        //     'item_all_quanliti_net' => 'Số lượng duyệt',
        //     'item_all_quanliti_order' => 'Số lượng đặt',
        //     'item_all_quanliti_left' => 'Số lượng còn lại',
        $this->db->from('tblinternal_proposal');
        $this->db->join('tbl_recommended_list tb_type', 'tb_type.id = tblinternal_proposal.recommended_list_group_id', 'left');
        $this->db->join('tbl_recommended_list tb_group', 'tb_group.id = tblinternal_proposal.recommended_list_id', 'left');
        $this->db->join($tbrecommended_detail, 'tbrecommended_detail.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join('tblbranch', 'tblbranch.id = tblinternal_proposal.id_branch', 'left');
        $this->db->join('tbl_room', 'tbl_room.id = tblinternal_proposal.id_departments', 'left');
        $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblinternal_proposal.category_tasks', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblinternal_proposal.staff', 'left');
        $this->db->join($tbstaff_assigned, 'tbstaff_assigned.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join($tbstaff_bod, 'tbstaff_bod.id_internal_proposal = tblinternal_proposal.id', 'left');
        // $this->db->join($tbpurchase, 'tbpurchase.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join('tbl_services', 'tbl_services.id = tblinternal_proposal.id_service', 'left');
        $this->db->join($tblsuggestion, 'tblsuggestion.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join($tblpurchase_order, 'tblpurchase_order.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join($tblplan_propose, 'tblplan_propose.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join('tblinternal_proposal_purchase', 'tblinternal_proposal_purchase.id_internal_proposal = tblinternal_proposal.id', 'left');
        $this->db->join('tbl_internal_proposal_purchase_items', 'tbl_internal_proposal_purchase_items.id_internal_proposal = tblinternal_proposal.id', 'left');

        $this->db->join('tblpurchases', 'tblpurchases.id = tblinternal_proposal_purchase.id_purchases', 'left');
        $this->db->join('tblpurchases_items', 'tblpurchases_items.id = tbl_internal_proposal_purchase_items.id_purchases_items', 'left');
        $this->db->join('tbl_materials', 'tbl_materials.id = tblpurchases_items.product_id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id', 'left');


        if (!empty($start_date_search)) {
            $this->db->where('tblinternal_proposal.date >= "' . to_sql_date($start_date_search) . ' 00:00:00' . '"');
        }
        if (!empty($end_date_search)) {
            $this->db->where('tblinternal_proposal.date <= "' . to_sql_date($end_date_search) . ' 23:59:59' . '"');
        }

        $this->db->group_by('tblpurchases_items.id,tbl_internal_proposal_purchase_items.id,tblinternal_proposal.id');
        $this->db->order_by('tblinternal_proposal.id', 'desc');
        $this->db->order_by('tblpurchases.id', 'desc');
        $result = $this->db->get()->result_array();
        // $result = [];

        $styleTitle = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
                'size' => 18,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $styleHeader = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => true,
                // 'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '4BACC6'),
                'size' => 12,
                // 'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $stylePlain = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => false,
                // 'color' => array('rgb' => '111112'),
                'size' => 11,
                'name' => 'Times New Roman'
            ),
        ];

        $headerFillColor = [
            'A' => array('rgb' => 'BDD7EE'),
        ];

        $cloumns_excel = cloumns_excel();
        $colName = [
            'stt' => "STT",
            'date' => 'Ngày Đề Xuất',
            'code' => 'Mã Đề Xuất',
            'plan_type' => 'Loại Kế Hoạch',
            'type' => 'Loại Đề Xuất',
            'group' => 'Nhóm Đề Xuất',
            // 'detail' => 'Chi Tiết Đề Xuất',
            'item_code' => 'Mặt hàng',
            'item_name' => 'Tên hàng',
            'item_unit' => 'ĐVT',
            'item_all_quanliti' => 'Tổng số lượng',
            'item_all_quanliti_net' => 'Số lượng duyệt',
            'item_all_quanliti_order' => 'Số lượng đặt',
            'item_all_quanliti_left' => 'Số lượng còn lại',
            'branch' => 'Chi Nhánh',
            'department' => 'Khối Phòng Ban',
            'task' => 'Mã Công Việc',
            'staff' => 'Người Đề Xuất',
            'staff_assigned' => 'Quản Lý Duyệt',
            'staff_bod' => 'BOD Duyệt',
            'purchase' => 'Phiếu YCMH',
            'service' => 'Phiếu Dịch Vụ',
            'financial_proposal' => 'Phiếu ĐXTC',
            'purchase_order' => 'Phiếu PO',
            'plan' => 'Phiếu Kế Hoạch',
            'money' => 'Số Tiền',
            'company_supp' => 'Nhà Cung Cấp',
            'content' => 'Nội Dung',
            'checkis' => 'Hoàn thành quy trình',
        ];
        $aColumns = array_keys($colName);

        insertCompanyInfo($objPHPExcel, 'C1:AA2', 'B1');

        $excelRowNum = 1 + 4;
        $maxCol = count($colName) - 1;
        $objPHPExcel->getActiveSheet()->mergeCells('A' . ($excelRowNum) . ':' . $cloumns_excel[$maxCol] . $excelRowNum);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $excelRowNum, ('PHIẾU ĐỀ XUẤT NỘI BỘ'))->getStyle("A" . $excelRowNum)->applyFromArray($styleTitle);
        // $objPHPExcel->getActiveSheet()->freezePane('A1');

        $excelRowNum = 2 + 4;
        foreach ($aColumns as $key => $value) {
            foreach ($headerFillColor as $colIndex => $color) {
                if ($cloumns_excel[$key] == $colIndex) {
                    $styleHeader['fill']['color'] = $color;
                    unset($headerFillColor[$colIndex]);
                    break;
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
            $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
        }

        $excelRowNum = 3 + 4;
        foreach ($result as $key => $aRow) {
            $aRow['stt'] = ($key + 1);

            if (!empty($aRow['plan_type'])) {
                foreach ($this->type_plan_propose as $type_plan_propose) {
                    if ($aRow['plan_type'] == $type_plan_propose['id']) {
                        $aRow['plan_type'] = $type_plan_propose['name'];
                    }
                }
            }

            if (!empty($aRow['content'])) {
                $aRow['content'] = html_entity_decode(strip_tags($aRow['content']), ENT_QUOTES, 'UTF-8');
            }
            $this->db->select('tbl_internal_proposal_process.*,tbl_internal_proposal_process_child.id as childs');
            $this->db->where('tbl_internal_proposal_process.id_internal_proposal', $aRow['id']);
            $this->db->join('tbl_internal_proposal_process_child', 'tbl_internal_proposal_process_child.recommended_list_id = tbl_internal_proposal_process.id_process AND tbl_internal_proposal_process_child.id_internal_proposal = tbl_internal_proposal_process.id_internal_proposal', 'left');
            $this->db->order_by('tbl_internal_proposal_process.id_process asc');
            $this->db->group_by('tbl_internal_proposal_process.id');
            $data_checklist_items = $this->db->get('tbl_internal_proposal_process')->result_array();

            // NEW: xác định trạng thái thời hạn theo yêu cầu
            $date_start = $aRow['date']; // ngày bắt đầu
            $date_end = $aRow['date_finish']; // ngày kết thúc
            if (empty($date_end)) {
                $date_end = $date_start;
            }
            $now = date('Y-m-d H:i:s');

            $hasPending = false;
            $allDone = true;
            $max_date_status = null;

            foreach ($data_checklist_items as $v) {
                if (empty($v['status']) || $v['status'] == 0) {
                    $hasPending = true;
                    $allDone = false;
                    // không break ở đây nếu muốn thu thập thêm thông tin; nhưng pending đủ để đánh dấu chưa hoàn thành/trễ
                    break;
                } else {
                    // status == 1
                    if (!empty($v['date_status'])) {
                        // lấy max date_status
                        if ($max_date_status === null || strtotime($v['date_status']) > strtotime($max_date_status)) {
                            $max_date_status = $v['date_status'];
                        }
                    }
                }
            }

            if ($hasPending) {
                // có bước chưa xong
                if (strtotime($now) > strtotime($date_end)) {
                    $aRow['checkis'] = 'Trễ';
                } else {
                    $aRow['checkis'] = 'Chưa hoàn thành';
                }
            } elseif ($allDone) {
                // tất cả đã xong (status == 1)
                if (empty($max_date_status)) {
                    // đã hoàn thành nhưng không có date_status -> coi là đã hoàn thành (không xác định thời gian)
                    $aRow['checkis'] = 'Đã hoàn thành';
                } else {
                    if (strtotime($max_date_status) > strtotime($date_end)) {
                        $aRow['checkis'] = 'Trễ';
                    } elseif (strtotime($max_date_status) < strtotime($date_start)) {
                        $aRow['checkis'] = 'Sớm';
                    } else {
                        // max_date_status nằm trong [date_start, date_end]
                        $aRow['checkis'] = 'Đúng';
                    }
                }
            } else {
                // fallback
                $aRow['checkis'] = 'Chưa hoàn thành';
            }
            foreach ($aColumns as $colIndex => $colCode) {
                if (str_contains($colCode, 'date')) {
                    // $cellValue = (isset($aRow[$colCode]) ? _d($aRow[$colCode]) : '');
                    // $newDate = date($newDateFormat, strtotime($oldDate));
                    $cellValue = (isset($aRow[$colCode]) ? date("Y/m/d H:i:s", strtotime($aRow[$colCode])) : '');
                } else {
                    $cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
                }

                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
            }
            $excelRowNum++;
        }

        $filename = 'Phieu_DXNB' . '.xls';
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
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }
    // public function export_excel()
    // {
    //     ini_set('memory_limit', '3500M');
    //     include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
    //     $this->load->library('PHPExcel');
    //     $objPHPExcel = new PHPExcel();
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
    //     $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
    //     $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    //     $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    //     $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
    //     $objPHPExcel->getDefaultStyle()->applyFromArray([
    //         'font' => array(
    //             'name'  => 'Times New Roman'
    //         ),
    //     ]);

    //     $start_date_search = $this->input->post('start_date_search');
    //     $end_date_search = $this->input->post('end_date_search');

    //     $tbrecommended_detail = '(SELECT
    //         tblinternal_proposal_recommended.id_internal_proposal AS id_internal_proposal,
    //         GROUP_CONCAT(tbl_recommended_list.code SEPARATOR "; ") as code,
    //         GROUP_CONCAT(tbl_recommended_list.name SEPARATOR "; ") as name
    //     FROM tblinternal_proposal_recommended
    //     INNER JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal_recommended.recommended_list_detail_id
    //     GROUP BY tblinternal_proposal_recommended.id_internal_proposal
    //     ) AS tbrecommended_detail';

    //     $tbstaff_assigned = '(SELECT
    //         tblinternal_proposal_assigned.id_internal_proposal AS id_internal_proposal,
    //         GROUP_CONCAT(CONCAT(tblstaff.firstname, " ", tblstaff.lastname) SEPARATOR "; ") as staff_name
    //         FROM tblinternal_proposal_assigned
    //         INNER JOIN tblstaff ON tblstaff.staffid = tblinternal_proposal_assigned.id_staff
    //         GROUP BY tblinternal_proposal_assigned.id_internal_proposal
    //     ) AS tbstaff_assigned';

    //     $tbstaff_bod = '(SELECT
    //         tblinternal_proposal_staff_pod.id_internal_proposal AS id_internal_proposal,
    //         GROUP_CONCAT(CONCAT(tblstaff.firstname, " ", tblstaff.lastname) SEPARATOR "; ") as staff_name
    //         FROM tblinternal_proposal_staff_pod
    //         INNER JOIN tblstaff ON tblstaff.staffid = tblinternal_proposal_staff_pod.id_staff
    //         GROUP BY tblinternal_proposal_staff_pod.id_internal_proposal
    //     ) AS tbstaff_bod';

    //     $tbpurchase = '(SELECT
    //         tblinternal_proposal_purchase.id_internal_proposal AS id_internal_proposal,
    //         GROUP_CONCAT(CONCAT(tblpurchases.prefix, "-", tblpurchases.code) SEPARATOR "; ") as purchase
    //         FROM tblinternal_proposal_purchase
    //         INNER JOIN tblpurchases ON tblpurchases.id = tblinternal_proposal_purchase.id_purchases
    //         GROUP BY tblinternal_proposal_purchase.id_internal_proposal
    //     ) AS tbpurchase';

    //     $tblpurchase_order = '(SELECT
    //         tblpurchase_order.id_internal_proposal AS id_internal_proposal,
    //         GROUP_CONCAT(CONCAT(tblpurchase_order.prefix, tblpurchase_order.code) SEPARATOR "; ") as code
    //     FROM tblpurchase_order
    //     GROUP BY tblpurchase_order.id_internal_proposal
    //     ) AS tblpurchase_order';

    //     $tblsuggestion = '(SELECT
    //         tblsuggestion.id_internal_proposal AS id_internal_proposal,
    //         GROUP_CONCAT(tblsuggestion.code SEPARATOR "; ") as code
    //     FROM tblsuggestion
    //     GROUP BY tblsuggestion.id_internal_proposal
    //     ) AS tblsuggestion';

    //     $tblplan_propose = '(SELECT
    //         tblplan_propose.id_internal_proposal AS id_internal_proposal,
    //         GROUP_CONCAT(tblplan_propose.code SEPARATOR "; ") as code
    //     FROM tblplan_propose
    //     GROUP BY tblplan_propose.id_internal_proposal
    //     ) AS tblplan_propose';

    //     $this->db->select('
    //         tblinternal_proposal.date as date,
    //         tblinternal_proposal.code as code,
    //         tblinternal_proposal.type_plan_propose as plan_type,
    //         tb_type.name as type,
    //         tb_group.name as group,
    //         tbrecommended_detail.name as detail,
    //         tblbranch.name as branch,
    //         tbldepartments.name as department,
    //         tblcategory_tasks.content as task,
    //         CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff,
    //         tbstaff_assigned.staff_name as staff_assigned,
    //         tbstaff_bod.staff_name as staff_bod,
    //         tbpurchase.purchase as purchase,
    //         CONCAT(tbl_services.prefix, tbl_services.code) as service,
    //         tblpurchase_order.code as purchase_order,
    //         tblsuggestion.code as financial_proposal,
    //         tblplan_propose.code as plan,
    //         tblinternal_proposal.money as money,
    //         tblinternal_proposal.content as content,
    //     ');
    //     $this->db->from('tblinternal_proposal');
    //     $this->db->join('tbl_recommended_list tb_type', 'tb_type.id = tblinternal_proposal.recommended_list_group_id', 'left');
    //     $this->db->join('tbl_recommended_list tb_group', 'tb_group.id = tblinternal_proposal.recommended_list_id', 'left');
    //     $this->db->join($tbrecommended_detail, 'tbrecommended_detail.id_internal_proposal = tblinternal_proposal.id', 'left');
    //     $this->db->join('tblbranch', 'tblbranch.id = tblinternal_proposal.id_branch', 'left');
    //     $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblinternal_proposal.id_departments', 'left');
    //     $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblinternal_proposal.category_tasks', 'left');
    //     $this->db->join('tblstaff', 'tblstaff.staffid = tblinternal_proposal.staff', 'left');
    //     $this->db->join($tbstaff_assigned, 'tbstaff_assigned.id_internal_proposal = tblinternal_proposal.id', 'left');
    //     $this->db->join($tbstaff_bod, 'tbstaff_bod.id_internal_proposal = tblinternal_proposal.id', 'left');
    //     $this->db->join($tbpurchase, 'tbpurchase.id_internal_proposal = tblinternal_proposal.id', 'left');
    //     $this->db->join('tbl_services', 'tbl_services.id = tblinternal_proposal.id_service', 'left');
    //     $this->db->join($tblsuggestion, 'tblsuggestion.id_internal_proposal = tblinternal_proposal.id', 'left');
    //     $this->db->join($tblpurchase_order, 'tblpurchase_order.id_internal_proposal = tblinternal_proposal.id', 'left');
    //     $this->db->join($tblplan_propose, 'tblplan_propose.id_internal_proposal = tblinternal_proposal.id', 'left');

    //     if (!empty($start_date_search)) {
    //         $this->db->where('tblinternal_proposal.date >= "' . to_sql_date($start_date_search) . ' 00:00:00' . '"');
    //     }
    //     if (!empty($end_date_search)) {
    //         $this->db->where('tblinternal_proposal.date <= "' . to_sql_date($end_date_search) . ' 23:59:59' . '"');
    //     }

    //     $this->db->group_by('tblinternal_proposal.id');
    //     $this->db->order_by('tblinternal_proposal.id', 'desc');
    //     $result = $this->db->get()->result_array();
    //     // $result = [];

    //     $styleTitle = [
    //         'borders' => array(
    //             'allborders' => array(
    //                 'style' => PHPExcel_Style_Border::BORDER_THIN
    //             )
    //         ),
    //         'font' => array(
    //             'bold' => true,
    //             'size' => 18,
    //             'name' => 'Times New Roman'
    //         ),
    //         'alignment' => array(
    //             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //         )
    //     ];

    //     $styleHeader = [
    //         'borders' => array(
    //             'allborders' => array(
    //                 'style' => PHPExcel_Style_Border::BORDER_THIN
    //             )
    //         ),
    //         'font' => array(
    //             // 'bold' => true,
    //             // 'color' => array('rgb' => '111112'),
    //             'size' => 12,
    //             'name' => 'Times New Roman'
    //         ),
    //         'fill' => array(
    //             'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //             'color' => array('rgb' => '4BACC6'),
    //             'size' => 12,
    //             // 'bold' => true
    //         ),
    //         'alignment' => array(
    //             'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //             'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //         )
    //     ];

    //     $stylePlain = [
    //         'borders' => array(
    //             'allborders' => array(
    //                 'style' => PHPExcel_Style_Border::BORDER_THIN
    //             )
    //         ),
    //         'font' => array(
    //             // 'bold' => false,
    //             // 'color' => array('rgb' => '111112'),
    //             'size' => 11,
    //             'name' => 'Times New Roman'
    //         ),
    //     ];

    //     $headerFillColor = [
    //         'A' => array('rgb' => 'BDD7EE'),
    //     ];

    //     $cloumns_excel = cloumns_excel();
    //     $colName = [
    //         'stt' => "STT",
    //         'date' => 'Ngày Đề Xuất',
    //         'code' => 'Mã Đề Xuất',
    //         'plan_type' => 'Loại Kế Hoạch',
    //         'type' => 'Loại Đề Xuất',
    //         'group' => 'Nhóm Đề Xuất',
    //         'detail' => 'Chi Tiết Đề Xuất',
    //         'branch' => 'Chi Nhánh',
    //         'department' => 'Khối Phòng Ban',
    //         'task' => 'Mã Công Việc',
    //         'staff' => 'Người Đề Xuất',
    //         'staff_assigned' => 'Quản Lý Duyệt',
    //         'staff_bod' => 'BOD Duyệt',
    //         'purchase' => 'Phiếu YCMH',
    //         'service' => 'Phiếu Dịch Vụ',
    //         'financial_proposal' => 'Phiếu ĐXTC',
    //         'purchase_order' => 'Phiếu PO',
    //         'plan' => 'Phiếu Kế Hoạch',
    //         'money' => 'Số Tiền',
    //         'content' => 'Nội Dung',
    //     ];
    //     $aColumns = array_keys($colName);

    //     $excelRowNum = 1;
    //     $maxCol = count($colName) - 1;
    //     $objPHPExcel->getActiveSheet()->mergeCells('A' . ($excelRowNum) . ':' . $cloumns_excel[$maxCol] . $excelRowNum);
    //     $objPHPExcel->getActiveSheet()->setCellValue('A' . $excelRowNum, ('PHIẾU ĐỀ XUẤT NỘI BỘ'))->getStyle("A" . $excelRowNum)->applyFromArray($styleTitle);
    //     // $objPHPExcel->getActiveSheet()->freezePane('A1');

    //     $excelRowNum = 2;
    //     foreach ($aColumns as $key => $value) {
    //         foreach ($headerFillColor as $colIndex => $color) {
    //             if ($cloumns_excel[$key] == $colIndex) {
    //                 $styleHeader['fill']['color'] = $color;
    //                 unset($headerFillColor[$colIndex]);
    //                 break;
    //             }
    //         }
    //         $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
    //     }

    //     $excelRowNum = 3;
    //     foreach ($result as $key => $aRow) {
    //         $aRow['stt'] = ($key + 1);

    //         if (!empty($aRow['plan_type'])) {
    //             foreach ($this->type_plan_propose as $type_plan_propose) {
    //                 if ($aRow['plan_type'] == $type_plan_propose['id']) {
    //                     $aRow['plan_type'] = $type_plan_propose['name'];
    //                 }
    //             }
    //         }

    //         if (!empty($aRow['content'])) {
    //             $aRow['content'] = html_entity_decode(strip_tags($aRow['content']), ENT_QUOTES, 'UTF-8');
    //         }

    //         foreach ($aColumns as $colIndex => $colCode) {
    //             if (str_contains($colCode, 'date')) {
    //                 $cellValue = (isset($aRow[$colCode]) ? _d($aRow[$colCode]) : '');
    //             } else {
    //                 $cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
    //             }

    //             $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
    //         }
    //         $excelRowNum++;
    //     }

    //     $filename = 'Phieu_DXNB' . '.xls';
    //     ob_start();
    //     header('Content-Type: application/vnd.ms-excel');
    //     header('Content-Disposition: attachment;filename="$filename"');
    //     header('Cache-Control: max-age=0');
    //     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    //     $objWriter->save('php://output');
    //     $xlsData = ob_get_contents();
    //     ob_end_clean();

    //     $response =  array(
    //         'result' => 1,
    //         'message' => lang('success'),
    //         'filename' => $filename,
    //         'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
    //     );
    //     die(json_encode($response));
    // }
    public function check_qr()
    {
        $code = $this->input->get('code');
        if ($code) {
            $code = explode('||', $code);
            // purchases||467
            // purchases||376
            // purchases||382
            // purchases||464
            // $this->db->where('tblpurchases.status >', 0);
            // $this->db->where('tblpurchases.status <', 4);
            // $data['purchases'] = $this->db->get('tblpurchases')->result_array();
            if ($code[0] == 'purchases') {
                $check = get_table_where('tblpurchases', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    if ($check['status'] == 0) {
                        $data['id'] = $code[1];
                        $data['message'] = 'Phiếu chưa duyệt';
                        $data['result'] = false;
                        echo json_encode($data);
                        die;
                    }
                    if ($check['status'] >= 4) {
                        $data['id'] = $code[1];
                        $data['message'] = 'Phiếu đã kết thúc';
                        $data['result'] = false;
                        echo json_encode($data);
                        die;
                    }
                    $data['id'] = $code[1];
                    $data['message'] = 'Thành công';
                    $data['result'] = true;
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy mặt hàng';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            $data['message'] = 'Không tìm thấy số phiếu';
            $data['result'] = false;
            echo json_encode($data);
            die;
        } else {
            $data['result'] = false;
            echo json_encode($data);
            die;
        }
    }

    public function changDataDecision()
    {
        $data = [];
        $decision_bonus_discipline_id = $this->input->post('decision_bonus_discipline_id');

        $this->db->select('tbl_decision_bonus_discipline.*,
            IF(tbl_decision_bonus_discipline.object_type = "department",tbldepartments.name,CONCAT(tblstaff.firstname," ",tblstaff.lastname)) as object_name,
            tbl_suggest_bonus_disciplines.reference_no as reference_no_suggest,
        ');
        $this->db->from('tbl_decision_bonus_discipline');
        $this->db->join(
            'tbl_suggest_bonus_disciplines',
            'tbl_suggest_bonus_disciplines.id = tbl_decision_bonus_discipline.suggest_bonus_discipline_id',
            'inner'
        );
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_decision_bonus_discipline.object_id AND tbl_decision_bonus_discipline.object_type = "staff"', 'left');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_decision_bonus_discipline.object_id AND tbl_decision_bonus_discipline.object_type = "department"', 'left');
        $this->db->where('tbl_decision_bonus_discipline.id', $decision_bonus_discipline_id);
        $dtData = $this->db->get()->row_array();
        if (!empty($dtData)) {
            $note = $dtData['note'];
            $note = str_replace('{object_type}', $dtData['object_type'] == 'staff' ? 'Cá nhân' : 'Bộ phận - Phòng ban', $note);
            $note = str_replace('{object_name}', $dtData['object_name'], $note);
            $note = str_replace('{code_decision}', $dtData['reference_no'], $note);
            $dtData['note'] = $note;
        }
        $data['dtData'] = $dtData;
        echo json_encode($data);
    }

    public function getSuggestByRecommended()
    {
        $parent_id = $this->input->post('parent_id');
        if (empty($parent_id)) {
            echo json_encode([]);
            die;
        }
        $this->db->select('tbl_recommended_list.id as id,tbl_category_recommended.name_table', false);
        $this->db->from('tbl_recommended_list');
        $this->db->join('tbl_category_recommended', 'tbl_category_recommended.id = tbl_recommended_list.category_recommended_id');
        $this->db->where('tbl_recommended_list.id', $parent_id);
        $rs = $this->db->get()->row_array();
        $result = [];
        if (!empty($rs)) {
            $this->db->select($rs['name_table'] . '.*');
            $this->db->from($rs['name_table']);
            $result = $this->db->get()->result_array();
        }
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $reference_no = '';
                if (!empty($value['reference_no'])) {
                    $reference_no = $value['reference_no'];
                } elseif (!empty($value['code'])) {
                    $reference_no = $value['code'];
                }
                $result[$key]['reference_no'] = $reference_no;
            }
        }
        echo json_encode($result);
    }


    function update_recommended_list($id = '')
    {
        if (!empty($this->input->post()) && !empty($id)) {
            $data = $this->input->post();
            $ins = [];
            $ins['recommended_list_group_id'] = $data['recommended_list_group_id_new'];
            $ins['recommended_list_id'] = $data['recommended_list_id_new'];
            $this->db->where('id', $id);
            $this->db->update('tblinternal_proposal', $ins);
            $this->db->where('id_internal_proposal', $id);
            $this->db->delete('tblinternal_proposal_recommended');
            if (!empty($data['recommended_list_detail_id_new'])) {
                foreach ($data['recommended_list_detail_id_new'] as $key => $value) {
                    $this->db->insert('tblinternal_proposal_recommended', [
                        'id_internal_proposal' => $id,
                        'recommended_list_detail_id' => $value,
                    ]);
                }
            }
            $data['message'] = 'Thành công';
            $data['result'] = true;
            $data['alert_type'] = 'success';
        } else {
            $data['message'] = 'Thất bại';
            $data['result'] = false;
            $data['alert_type'] = 'danger';
        }
        echo json_encode($data);
        die;
    }
    public function getSuggestByRecommendedSingle()
    {
        $category_recommended_id = $this->input->post('category_recommended_id');

        $this->db->select('tbl_category_recommended.name_table,type_kpi,type_plan_purchase,ballot_type,type', false);
        $this->db->from('tbl_category_recommended');
        $this->db->where('tbl_category_recommended.id', $category_recommended_id);
        $rs = $this->db->get()->row_array();
        $result = [];
        if (!empty($rs)) {
            $this->db->select($rs['name_table'] . '.*');
            $this->db->from($rs['name_table']);
            if ($rs['type_kpi'] == 1) {
                $this->db->where($rs['name_table'] . '.status', 1);
            }
            if ($rs['name_table'] == 'tbl_suggest_check') {
                $this->db->where($rs['name_table'] . '.ballot_type', $rs['ballot_type']);
            }
            if ($rs['name_table'] == 'tbl_suggest_plan_purchase') {
                $this->db->where($rs['name_table'] . '.type', $rs['type_plan_purchase']);
                $this->db->where($rs['name_table'] . '.status', 1);
            }
            if ($rs['name_table'] == 'tbl_suggest_payslips') {
                $this->db->where($rs['name_table'] . '.status', 1);
                $this->db->where(' NOT EXISTS (
                    SELECT 1
                    FROM tblinternal_proposal
                    WHERE tblinternal_proposal.category_recommended_id = ' . CR_SUGGEST_PAYSLIPS_ID . ' AND ' . $rs['name_table'] . '.id = tblinternal_proposal.suggest_id
                )', false, false);
            }
            if ($rs['name_table'] == 'tbl_suggest_probationary_evaluate') {
                $this->db->where($rs['name_table'] . '.type', $rs['type']);
                $this->db->where($rs['name_table'] . '.status', 1);
            }
            if ($rs['name_table'] == 'tbl_suggest_evaluate') {
                if (!empty($rs['type'])) {
                    $this->db->where($rs['name_table'] . '.object_type', $rs['type']);
                }
            }
            if ($rs['name_table'] == 'tblsuggest_test_item_quality') {
                if (!empty($rs['type'])) {
                    $this->db->where($rs['name_table'] . '.type', $rs['type']);
                }
                if (!empty($rs['ballot_type'])) {
                    if ($rs['ballot_type'] == 2) {
                        $this->db->select($rs['name_table'] . '.code_evaluate as code');
                        $this->db->where($rs['name_table'] . '.status', 1);
                    }
                }
            }
            if ($rs['name_table'] == 'tbl_evaluate') {
                $this->db->select('code_evaluate as reference_no, id, created_by as staff_suggest');
                if (!empty($rs['type'])) {
                    $this->db->where($rs['name_table'] . '.type', $rs['type']);
                }
            }
            if ($rs['name_table'] == 'tbl_regulations') {
                $this->db->select('code as reference_no, id, create_by as staff_suggest');
                if (!empty($rs['type'])) {
                    $this->db->where($rs['name_table'] . '.type', $rs['type']);
                }
            }
            $result = $this->db->get()->result_array();
        }
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $reference_no = '';
                if (!empty($value['reference_no'])) {
                    $reference_no = $value['reference_no'];
                } elseif (!empty($value['code'])) {
                    $reference_no = $value['code'];
                }
                $staff_suggest_name = "";
                if (!empty($value['staff_suggest'])) {
                    $staff_suggest_name = get_staff_full_name($value['staff_suggest']);
                }
                $result[$key]['reference_no'] = $reference_no;
                $result[$key]['staff_suggest_name'] = $staff_suggest_name;
            }
        }
        echo json_encode($result);
    }

    public function getSuggestCategoryKpi()
    {
        $data['total'] = 0;
        $category_recommended_id = $this->input->post('category_recommended_id');
        $suggest_id = $this->input->post('suggest_id');
        $dtCategoryRecom = get_table_where('tbl_category_recommended', ['id' => $category_recommended_id], '', 'row_array');
        if (!empty($dtCategoryRecom)) {
            if ($dtCategoryRecom['type_kpi'] == 1) {
                $dtItems = [];
                $this->db->select('tbl_suggest_kpi.*,tblroles.name as name_role');
                $this->db->from('tbl_suggest_kpi');
                $this->db->from('tblroles', 'tblroles.roleid = tbl_suggest_kpi.role_id');
                $this->db->where('tbl_suggest_kpi.id', $suggest_id);
                $dtData = $this->db->get()->row_array();

                $this->db->select('
                    tbl_suggest_kpi_item.*,
                    tbl_category_kpi.name as name_category,
                    tbl_category_kpi_criteria.type as type,
                    tbl_category_kpi_criteria.name as name_kpi,
                    tbl_category_kpi_criteria.measure as measure,
                    tbl_category_kpi_criteria.code as code_kpi,
                    tbl_category_kpi_criteria.time as time,
                    tbl_suggest_kpi_item.weight as weight,
                    tbl_detail_task_detail.regulations as regulations,
                    0 as report,
                    tbl_result.name as name_result,
                ');
                $this->db->from('tbl_suggest_kpi_item');
                $this->db->join('tbl_category_kpi', 'tbl_category_kpi.id = tbl_suggest_kpi_item.category_kpi_id');
                $this->db->join('tbl_category_kpi_criteria', 'tbl_category_kpi_criteria.id = tbl_suggest_kpi_item.category_kpi_criteria_id');
                $this->db->join('tbl_detail_task_detail', 'tbl_detail_task_detail.category_kpi_criteria_id = tbl_category_kpi_criteria.id', 'left');
                $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_kpi_item.result_id', 'left');
                $this->db->where('tbl_suggest_kpi_item.suggest_kpi_id', $suggest_id);
                $dtItems = $this->db->get()->result_array();
                $month = $dtData['month'];
                $year = $dtData['year'];
                $staff_suggest = $dtData['staff_suggest'];
                if (!empty($dtItems)) {
                    foreach ($dtItems as $key => $value) {
                        $category_kpi_criteria_id = $value['category_kpi_criteria_id'];
                        $tb_tamp = "(
                            SELECT
                                COUNT(tbl_production_report_kpi.category_kpi_criteria_id) as total
                            FROM tblproduction_report
                            JOIN tbl_production_report_kpi ON tbl_production_report_kpi.production_report_id = tblproduction_report.id
                            WHERE DATE_FORMAT(tblproduction_report.date, '%m') = $month AND DATE_FORMAT(tblproduction_report.date, '%Y') = $year
                            AND tblproduction_report.staff_responsible = $staff_suggest AND tbl_production_report_kpi.category_kpi_criteria_id = $category_kpi_criteria_id
                        )";
                        $dtProductionKpi = $this->db->query($tb_tamp)->row_array();
                        $dtItems[$key]['report'] = !empty($dtProductionKpi['total']) ? $dtProductionKpi['total'] : 0;
                    }
                }
            }
            if ($dtCategoryRecom['name_table'] == 'tbl_suggest_payslips') {
                $dtSuggest_payslips = get_table_where('tbl_suggest_payslips', ['id' => $suggest_id], '', 'row_array');
                $data['total'] = $dtSuggest_payslips['total'];
            }
            if ($dtCategoryRecom['name_table'] == 'tbl_request_repair') {
                $dtSuggest_payslips = get_table_where('tbl_request_repair', ['id' => $suggest_id], '', 'row_array');
                $data['total'] = $dtSuggest_payslips['amount'];
            }
        }
        $body1 = '';
        $body2 = '';
        $stt1 = 0;
        $stt2 = 0;
        $total_weight1 = 0;
        $total_weight2 = 0;
        if (!empty($dtItems)) {
            foreach ($dtItems as $key => $value) {
                if ($value['type'] == 2) {
                    $stt2++;
                    $body2 .= ' <tr>
                            <td>
                                <div class="text-center">' . $stt2 . '</div>
                            </td>
                            <td>
                                <div>
                                    ' . $value['name_category'] . '
                                </div>
                            </td>
                            <td><div class="td_type">Năng Lực</div></td>
                            <td><div class="td_name_kpi">' . $value['name_kpi'] . '</div></td>
                            <td><div class="td_code_kpi">' . $value['code_kpi'] . '</div></td>
                            <td><div class="td_measure">' . $value['measure'] . '</div></td>
                            <td><div class="td_target text-center">' . $value['time'] . '</div></td>
                            <td><div class="td_weight text-center">' . $value['weight'] . '</div></td>
                            <td><div class="td_regulations">' . $value['regulations'] . '</div></td>
                            <td class="text-center">' . $value['report'] . ' Phiếu</td>
                            <td>
                                ' . $value['name_result'] . '
                            </td>
                        </tr>';
                    $total_weight2 += $value['weight'];
                } else {
                    $stt1++;
                    $body1 .= ' <tr>
                        <td>
                            <div class="text-center">' . $stt1 . '</div>
                        </td>
                        <td>
                            <div>
                                ' . $value['name_category'] . '
                            </div>
                        </td>
                        <td><div class="td_type">Tuân Thủ</div></td>
                        <td><div class="td_name_kpi">' . $value['name_kpi'] . '</div></td>
                        <td><div class="td_code_kpi">' . $value['code_kpi'] . '</div></td>
                        <td><div class="td_measure">' . $value['measure'] . '</div></td>
                        <td><div class="td_target text-center">' . $value['time'] . '</div></td>
                        <td><div class="td_weight text-center">' . $value['weight'] . '</div></td>
                        <td><div class="td_regulations">' . $value['regulations'] . '</div></td>
                        <td class="text-center">' . $value['report'] . ' Phiếu</td>
                        <td>
                            ' . $value['name_result'] . '
                        </td>
                    </tr>';
                    $total_weight1 += $value['weight'];
                }
            }
        }
        $html = '<table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px">' . lang('STT') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Nhóm KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Loại KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Chi tiết KPIS') . '</th>
                    <th class="text-center" style="width: 100px">' . lang('Mã KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Chỉ Số Đo Lường KPIs') . '</th>
                    <th class="text-center" style="width: 80px"><' . lang('Target') . '</th>
                    <th class="text-center" style="width: 80px">' . lang('Trọng số (%)') . '</th>
                    <th class="text-center" style="width: 200px">' . lang('Tiêu chuẩn/ quy định') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Báo Cáo Không Phù Hợp') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Kết quả') . '</th>
                </tr>
            </thead>
            <tbody>
                ' . $body1 . '
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="total_weight text-center bold">' . $total_weight1 . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" class="bold uppercase">% KPI</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center" style="color: red">' . (80 * $total_weight1 / 100) . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>';

        $html .= '<table style="margin-top: 30px !important;">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px">' . lang('STT') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Nhóm KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Loại KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Chi tiết KPIS') . '</th>
                    <th class="text-center" style="width: 100px">' . lang('Mã KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Chỉ Số Đo Lường KPIs') . '</th>
                    <th class="text-center" style="width: 80px"><' . lang('Target') . '</th>
                    <th class="text-center" style="width: 80px">' . lang('Trọng số (%)') . '</th>
                    <th class="text-center" style="width: 200px">' . lang('Tiêu chuẩn/ quy định') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Báo Cáo Không Phù Hợp') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Kết quả') . '</th>
                </tr>
            </thead>
            <tbody>
                ' . $body2 . '
            </tbody>
             <tfoot>
                <tr>
                    <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="total_weight text-center bold">' . $total_weight2 . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" class="bold uppercase">% KPI</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center" style="color: red">' . (20 * $total_weight2 / 100) . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" class="bold uppercase">Tổng KPI</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center" style="color: red">' . ((80 * $total_weight1 / 100) + (20 * $total_weight2 / 100)) . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>';
        $data['html'] = $html;
        echo json_encode($data);
    }
    public function getSuggestCategoryKpi_muiti()
    {
        $data['total'] = 0;
        $category_recommended_id = $this->input->post('category_recommended_id');
        $suggest_id = $this->input->post('suggest_id');
        $dtCategoryRecom = get_table_where('tbl_category_recommended', ['id' => $category_recommended_id], '', 'row_array');
        if (!empty($dtCategoryRecom)) {
            if ($dtCategoryRecom['name_table'] == 'tbl_suggest_payslips') {
                // $dtSuggest_payslips = get_table_where('tbl_suggest_payslips', ['id' => $suggest_id], '', 'row_array');
                $this->db->select('COALESCE(SUM(tbl_suggest_payslips.total),0) as total');
                $this->db->from('tbl_suggest_payslips');
                if (!empty($suggest_id)) {
                    $this->db->where_in('tbl_suggest_payslips.id', $suggest_id);
                } else {
                    $this->db->where('tbl_suggest_payslips.id', -1);
                }
                $dtSuggest_payslips = $this->db->get()->row_array();
                $data['total'] = $dtSuggest_payslips['total'];
            }
        }
        $body1 = '';
        $body2 = '';
        $stt1 = 0;
        $stt2 = 0;
        $total_weight1 = 0;
        $total_weight2 = 0;
        if (!empty($dtItems)) {
            foreach ($dtItems as $key => $value) {
                if ($value['type'] == 2) {
                    $stt2++;
                    $body2 .= ' <tr>
                            <td>
                                <div class="text-center">' . $stt2 . '</div>
                            </td>
                            <td>
                                <div>
                                    ' . $value['name_category'] . '
                                </div>
                            </td>
                            <td><div class="td_type">Năng Lực</div></td>
                            <td><div class="td_name_kpi">' . $value['name_kpi'] . '</div></td>
                            <td><div class="td_code_kpi">' . $value['code_kpi'] . '</div></td>
                            <td><div class="td_measure">' . $value['measure'] . '</div></td>
                            <td><div class="td_target text-center">' . $value['time'] . '</div></td>
                            <td><div class="td_weight text-center">' . $value['weight'] . '</div></td>
                            <td><div class="td_regulations">' . $value['regulations'] . '</div></td>
                            <td class="text-center">' . $value['report'] . ' Phiếu</td>
                            <td>
                                ' . $value['name_result'] . '
                            </td>
                        </tr>';
                    $total_weight2 += $value['weight'];
                } else {
                    $stt1++;
                    $body1 .= ' <tr>
                        <td>
                            <div class="text-center">' . $stt1 . '</div>
                        </td>
                        <td>
                            <div>
                                ' . $value['name_category'] . '
                            </div>
                        </td>
                        <td><div class="td_type">Tuân Thủ</div></td>
                        <td><div class="td_name_kpi">' . $value['name_kpi'] . '</div></td>
                        <td><div class="td_code_kpi">' . $value['code_kpi'] . '</div></td>
                        <td><div class="td_measure">' . $value['measure'] . '</div></td>
                        <td><div class="td_target text-center">' . $value['time'] . '</div></td>
                        <td><div class="td_weight text-center">' . $value['weight'] . '</div></td>
                        <td><div class="td_regulations">' . $value['regulations'] . '</div></td>
                        <td class="text-center">' . $value['report'] . ' Phiếu</td>
                        <td>
                            ' . $value['name_result'] . '
                        </td>
                    </tr>';
                    $total_weight1 += $value['weight'];
                }
            }
        }
        $html = '<table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px">' . lang('STT') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Nhóm KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Loại KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Chi tiết KPIS') . '</th>
                    <th class="text-center" style="width: 100px">' . lang('Mã KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Chỉ Số Đo Lường KPIs') . '</th>
                    <th class="text-center" style="width: 80px"><' . lang('Target') . '</th>
                    <th class="text-center" style="width: 80px">' . lang('Trọng số (%)') . '</th>
                    <th class="text-center" style="width: 200px">' . lang('Tiêu chuẩn/ quy định') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Báo Cáo Không Phù Hợp') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Kết quả') . '</th>
                </tr>
            </thead>
            <tbody>
                ' . $body1 . '
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="total_weight text-center bold">' . $total_weight1 . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" class="bold uppercase">% KPI</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center" style="color: red">' . (80 * $total_weight1 / 100) . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>';

        $html .= '<table style="margin-top: 30px !important;">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px">' . lang('STT') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Nhóm KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Loại KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Chi tiết KPIS') . '</th>
                    <th class="text-center" style="width: 100px">' . lang('Mã KPIS') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Chỉ Số Đo Lường KPIs') . '</th>
                    <th class="text-center" style="width: 80px"><' . lang('Target') . '</th>
                    <th class="text-center" style="width: 80px">' . lang('Trọng số (%)') . '</th>
                    <th class="text-center" style="width: 200px">' . lang('Tiêu chuẩn/ quy định') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Báo Cáo Không Phù Hợp') . '</th>
                    <th class="text-center" style="width: 150px">' . lang('Kết quả') . '</th>
                </tr>
            </thead>
            <tbody>
                ' . $body2 . '
            </tbody>
             <tfoot>
                <tr>
                    <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="total_weight text-center bold">' . $total_weight2 . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" class="bold uppercase">% KPI</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center" style="color: red">' . (20 * $total_weight2 / 100) . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" class="bold uppercase">Tổng KPI</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center" style="color: red">' . ((80 * $total_weight1 / 100) + (20 * $total_weight2 / 100)) . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>';
        $data['html'] = $html;
        echo json_encode($data);
    }
    public function hand_over($id = '')
    {
        $data['title'] = 'Bàn giao';
        $data['id'] = $id;
        $process = get_table_where('tbl_internal_proposal_process', array('id' => $id), '', 'row_array');
        $data['stages'] = $process['stages'];
        $data['staff_list_all'] = $this->site_model->getStaffAll();
        $this->load->view('admin/internal_proposal/hand_over', $data);
    }
    public function add_hand_over()
    {
        $this->load->model('hand_over_model');
        $_data = $this->input->post();
        $id = $this->input->post('id');
        $receiver = $this->input->post('receiver');
        $internal_proposal_id = $id;
        $status = 1;
        $type = 1;

        $this->db->where('id', $internal_proposal_id);
        $internal_proposal = $this->db->get('tbl_internal_proposal_process')->row();
        if ($status == $internal_proposal->status) {
            $data['result'] = 0;
            $data['message'] = lang('Đã có nhân viên thay đổi trạng thái');
            echo json_encode($data);
            die;
        }
        if ($status == 0) {
            $options = [
                'status' => 0,
                'date_status' => NULL,
                'staff_id' => NULL
            ];
        } else {
            $options = [
                'status' => $status,
                'date_status' => date('Y-m-d H:i:s'),
                'staff_id' => get_staff_user_id()
            ];
        }
        $this->db->where('id', $internal_proposal_id);
        $this->db->update('tbl_internal_proposal_process', $options);
        $process = get_table_where('tbl_internal_proposal_process', array('id' => $id), '', 'row_array');
        $id_create = $process['id_internal_proposal'];
        $internal_proposal = get_table_where('tblinternal_proposal', array('id' => $id_create), '', 'row_array');
        $branch_id = $internal_proposal['id_branch'];

        $id_delivery_records = $this->input->post('id_delivery_records');
        $hand_over_task_id = $this->input->post('hand_over_task_id');
        $category_hand = $this->input->post('category_hand');
        $task_hand_over_qualified = $this->input->post('task_hand_over_qualified');
        //cong
        if (!empty($category_hand) && !empty($hand_over_task_id)) {
            if (empty($id_delivery_records)) {
                $delivery_records = [
                    'reference_no' => get_option('prefix_delivery_records') . sprintf('%06d', ch_getMaxID('id', 'tbl_delivery_records') + 1),
                    'date' => date('Y-m-d H:i:s'),
                    'staff' => get_staff_user_id(),
                    'type_object' => 'internal_proposal',
                    'category_hand' => $category_hand,
                    'type_create' => 'internal_proposal',
                    'id_create' => $id_create,
                    'id_branch' => $branch_id,
                    'receiver' => $receiver,
                ];
                $delivery_records['created_by'] = get_staff_user_id();
                $delivery_records['date_created'] = date('Y-m-d H:i:s');
                $id_delivery_records = $this->hand_over_model->insertDeliveryRecords($delivery_records);
                if (!empty($id_delivery_records)) {
                    $this->db->insert('tbl_delivery_records_object', [
                        'id_delivery_records' => $id_delivery_records,
                        'id_object' => $id_create
                    ]);
                }
            }
        }

        $qualified = true;
        $count_task_hand_over = 0;
        $id_delivery_records_detail = 0;
        if (!empty($id_delivery_records)) {
            if (!empty($hand_over_task_id)) {
                foreach ($hand_over_task_id as $key => $value) {
                    if ($task_hand_over_qualified[$key] == 2) {
                        $qualified = false;
                        $count_task_hand_over++;
                    }

                    $arrayInsert = [
                        'delivery_records_id' => $id_delivery_records,
                        'hand_over_task_id' => $value,
                        'task_hand_over_qualified' => !empty($task_hand_over_qualified[$key]) ? $task_hand_over_qualified[$key] : 0,
                        'staff_id' => $receiver,
                    ];
                    $this->db->where('delivery_records_id', $id_delivery_records);
                    $this->db->where('hand_over_task_id', $value);
                    $delivery_records_task = $this->db->get('tbl_delivery_records_task')->row();
                    if (!empty($delivery_records_task)) {
                        $this->db->where('id', $delivery_records_task->id);
                        $this->db->update('tbl_delivery_records_task', $arrayInsert);
                        if ($task_hand_over_qualified[$key] == 2) {
                            $id_delivery_records_detail = $delivery_records_task->id;
                        }
                    } else {
                        if (!empty($arrayInsert['task_hand_over_qualified'])) {
                            $arrayInsert['staff_id'] = $receiver;
                            $arrayInsert['date_check'] = date('Y-m-d H:i:s');
                        }
                        $this->db->insert('tbl_delivery_records_task', $arrayInsert);
                        if ($task_hand_over_qualified[$key] == 2) {
                            $id_delivery_records_detail = $this->db->insert_id();
                        }
                    }
                }
                $data['id_delivery_records'] = $id_delivery_records;
            }
        }

        if (empty($qualified)) {
            $total_quantity_errors = 0;
            $data['success'] = true;
            $data['alert_type'] = 'warning';
            $data['id_delivery_records'] = $id_delivery_records;
            $data['href'] = admin_url('production_report/detail?id_delivery_records=' . $id_delivery_records . '&quantity_err=' . $total_quantity_errors . ($count_task_hand_over == 1 ? ('&id_delivery_records_detail=' . $id_delivery_records_detail) : ''));
            $data['message'] = lang('Tạo biên bản bàn giao thành công');
            echo json_encode($data);
            die;
        } else {
            $total_quantity_errors = 0;
            $data['result'] = true;
            $data['alert_type'] = 'warning';
            $data['message'] = lang('Tạo biên bản bàn giao thành công');
            echo json_encode($data);
            die;
        }
        //end cong
    }
    public function create_suggestion_new_muiti($id = '')
    {
        $id_payment_modes = 3;
        $staff_browse = get_staff_user_id();
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        $this->db->where('id_internal_proposal', $id);
        $suggest_muti_id = $this->db->get('tbl_suggest_muti_id')->result_array();
        if (!empty($internal_proposal)) {
            if ($internal_proposal->money > 0) {
                if ($internal_proposal->status == 1) {
                    foreach ($suggest_muti_id as $key => $value) {
                        $ins = array();
                        $ins['date'] = date('Y-m-d H:i:s');
                        $ins['code'] = 'DX-' . sprintf('%06d', ch_getMaxID('id', 'tblsuggestion') + 1);
                        $ins['type'] = 3;
                        $ins['status'] = 2;
                        $ins['note'] = '';
                        $ins['staffid'] = $internal_proposal->staff;
                        $ins['staff_create'] = get_staff_user_id();
                        $ins['date_create'] = date('Y-m-d H:i:s');
                        $ins['price_total'] = $value['total'];
                        $ins['detail_suggest_muti_id'] = $value['suggest_id'];
                        $ins['id_internal_proposal'] = $id;
                        $ins['status_dn'] = 1;
                        $ins['staff_status_dn'] = $internal_proposal->staff;
                        $ins['date_status_dn'] = date('Y-m-d H:i:s');
                        $ins['id_branch'] = $internal_proposal->id_branch;
                        $ins['id_payment_modes'] = $id_payment_modes;
                        $ins['staff_browse'] = !empty($staff_browse) ? $staff_browse : null;
                        $this->db->insert('tblsuggestion', $ins);
                        $id_suggestion = $this->db->insert_id();
                        if (!empty($id_suggestion)) {
                            $this->db->where('id', $value['id']);
                            $this->db->update('tbl_suggest_muti_id', ['id_suggestion' => $id_suggestion]);
                        }
                    }
                    $this->db->where('id', $id);
                    $this->db->update('tblinternal_proposal', ['id_suggestion' => -1]);
                }
            }
        }
    }
    public function create_suggestion_new($id = '')
    {
        $id_payment_modes = 3;
        $staff_browse = get_staff_user_id();
        $this->db->where('id', $id);
        $internal_proposal = $this->db->get('tblinternal_proposal')->row();
        if (!empty($internal_proposal)) {
            if ($internal_proposal->money > 0) {
                if ($internal_proposal->status == 1) {
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
                    $ins['id_payment_modes'] = $id_payment_modes;
                    $ins['staff_browse'] = !empty($staff_browse) ? $staff_browse : null;
                    $this->db->insert('tblsuggestion', $ins);
                    $id_suggestion = $this->db->insert_id();
                    if (!empty($id_suggestion)) {
                        $this->db->where('id', $id);
                        $this->db->update('tblinternal_proposal', ['id_suggestion' => $id_suggestion]);
                    }
                }
            }
        }
    }
    function inspection_criteria_new($id = '', $detail_id = '', $process_id = '', $is = 0)
    {
        $data['title'] = 'Kiểm quy trình';
        $data['id'] = $id;
        $data['process_id'] = $process_id;
        $data['detail_id'] = $detail_id;
        $data['is'] = $is;
        $this->db->select('tbl_internal_proposal_process_child.*');
        $this->db->where('tbl_internal_proposal_process_child.id_internal_proposal', $id);
        $this->db->where('tbl_internal_proposal_process_child.recommended_list_id', $process_id);
        $data['category_hand_over'] = $this->db->get('tbl_internal_proposal_process_child')->result_array();
        $this->load->view('admin/internal_proposal/inspection_criteria_new', $data);
    }
    function inspection_criteria($id = '', $detail_id = '', $process_id = '', $is = 0)
    {
        $data['title'] = 'Xem quy trình';
        $data['id'] = $id;
        $data['process_id'] = $process_id;
        $data['detail_id'] = $detail_id;
        $data['is'] = $is;
        $this->db->select('tbl_internal_proposal_process_child.*');
        $this->db->where('tbl_internal_proposal_process_child.id_internal_proposal', $id);
        $this->db->where('tbl_internal_proposal_process_child.recommended_list_id', $process_id);
        $data['category_hand_over'] = $this->db->get('tbl_internal_proposal_process_child')->result_array();
        $this->load->view('admin/internal_proposal/inspection_criteria', $data);
    }
    public function get_table_delivery_records_internal_proposal()
    {
        $process_id = $this->input->post('process_id');
        $id = $this->input->post('id');
        $is = $this->input->post('is');
        $detail_id = $this->input->post('detail_id');
        $this->db->select('tbl_internal_proposal_process_child.*');
        $this->db->where('tbl_internal_proposal_process_child.recommended_list_id', $process_id);
        $this->db->where('tbl_internal_proposal_process_child.id_internal_proposal', $id);
        $data['category_hand_over'] = $this->db->get('tbl_internal_proposal_process_child')->result_array();
        $data['id'] = $id;
        $data['process_id'] = $process_id;
        $data['detail_id'] = $detail_id;
        $this->load->view('admin/internal_proposal/table_production', $data);
    }
    public function CheckCreateBCKPH($id = '', $process_id = '', $detail_id = '')
    {
        $this->db->select('tbl_internal_proposal_process_child.*');
        $this->db->where('tbl_internal_proposal_process_child.recommended_list_id', $process_id);
        $this->db->where('tbl_internal_proposal_process_child.id_internal_proposal', $id);
        $category_hand_over = $this->db->get('tbl_internal_proposal_process_child')->result_array();

        $is_check = 1;
        foreach ($category_hand_over as $key => $value) {
            $check = get_table_where('tbl_tinternal_proposal_inspection_criteria_process', ['id_internal_proposal' => $id, 'process_id' => $process_id, 'id_internal_proposal_process' => $detail_id, 'inspection_criteria' => $value['id']], '', 'row_array');

            $isCheckNot = '';
            if (!empty($check)) {
                if ($check['isCheckNot'] == 1) {
                    $isCheckNot = 1;
                }
            }
            if ($isCheckNot == 1) {
                $production_report = get_table_where('tblproduction_report', ['id_internal_proposal' => $id, 'id_internal_proposal_process' => $detail_id, 'id_internal_proposal_process_child' => $value['id']], '', 'row_array');

                if (!empty($production_report)) {
                    $this->db->select('tbl_process_production_report.*');
                    $this->db->where('tbl_process_production_report.staff_process', 0);
                    $this->db->where('tbl_process_production_report.production_report_id', $production_report['id']);
                    $this->db->from('tbl_process_production_report');
                    $Success_process = $this->db->get()->num_rows();
                    if (!empty($Success_process)) {
                        $is_check = 2;
                    }
                } else {
                    $is_check = $value['id'];
                }
            }
        }
        return $is_check;
    }
}
