<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports_propose extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();

        $this->ci = &get_instance();
        $this->preViewSyntheticPropose = true;
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
        $this->type_plan_propose = type_plan_propose();
        $this->type_title_plan_propose = [];
        foreach ($this->type_plan_propose as $key => $value) {
            $this->type_title_plan_propose[$value['id']] = $value['name'];
        }
    }
    public function index()
    {
        if (!$this->preViewSyntheticPropose) {
            access_denied();
        }
        $data = [];
        $title = lang('Báo Cáo Tổng Hợp Đề Xuất');
        $data['title'] = $title;
        $data['type_plan_propose'] = $this->type_plan_propose;
        $data['staff'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();
        $data['category_tasks'] = $this->site_model->getCategoryTasks();
        $data['recommended_list'] = $this->recommended_list_model->getRecommendedListParent([0], 1);
        $this->load->view('admin/reports_propose/manage', $data);
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
            $htmlKpi  = '';
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
                        </button><ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso"></ul></div>';
                        }
                    }
                }
            }
            $column[12] = !empty($code_Suggest) ? ('<div>' . $code_Suggest . '</div>') : '';

            $production_report_check = $aRow['production_report_check'];
            $production_report_check = explode('|||', $production_report_check);
            $view_production_report  = '';
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
                $strManagerAc = '<div class="mbot5"></div>';
            } elseif ($aRow['status'] == 1) {
                $activeManager = 'active';
                $html = "<p>
							<a id='agree' value='0' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Bỏ duyệt</a>
							<button class='btn po-close  btn-icon'>Thoát</button>
						</p>";
                $strManagerAc = '<div class="mbot5">
									<span data-html="true" data-toggle="popover" data-container="body" data-placement="right" style="cursor: pointer;" title="" data-content="" class="label label-success po" data-original-title="Duyệt">Đã duyệt</span>
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
									<span data-html="true" data-toggle="popover" data-container="body" data-placement="right" style="cursor: pointer;" title="" data-content="" class="label label-danger po" data-original-title="Không Duyệt">Không Duyệt</span>
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
				</div>
				<div class="wrap-content-process ' . $activeManager . '">
					<div class="wrap-step-process"></div>
					<div class="wrap-title-process" style="">
						' . lang('BOD duyệt') . '
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
                        $strManagerAc = '<div class="mbot5"></div>';
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
                        </div>' . '' . staff_profile_image(
                            $v['staff_id'],
                            array('staff-profile-image-small mright5'),
                            'small',
                            array()
                        ) . get_staff_full_name($v['staff_id']);
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
                        }
                    } elseif ($v['status'] == 2) {
                        $html = "<p>
                    <a id='agree' value='0' data-id='" . $aRow['id'] . "'  data-status='" . $v['id'] . "' class='btn btn-danger btn-icon'>Bỏ không duyệt</a>
                    <button class='btn po-close  btn-icon'>Thoát</button>
                </p>";
                        $strManagerAc = '<div class="mbot5">
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

            $column[19] = $approved_by;

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
}
