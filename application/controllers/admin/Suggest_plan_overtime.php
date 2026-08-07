<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_plan_overtime extends AdminController
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

        $this->preViewSuggestPlanOvertime = true;
        $this->preViewOwnSuggestPlanOvertime = true;
        $this->preAddSuggestPlanOvertime = true;
        $this->preEditSuggestPlanOvertime = true;
        $this->preApproveSuggestPlanOvertime = true;
        $this->preDeleteSuggestPlanOvertime = true;
    }

    public function index(){
        if (!$this->preViewSuggestPlanOvertime && !$this->preViewOwnSuggestPlanOvertime){
            access_denied();
        }
        $data['title'] = _l('dt_suggest_plan_overtime');
        $this->load->view('admin/suggest_plan_overtime/index', $data);
    }

    public function getSuggestPlanOvertime(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_plan_overtime.id as id',
            'tbl_suggest_plan_overtime.reference_no as reference_no',
            'tbl_suggest_plan_overtime.date as date',
            'tbl_orders.reference_no as reference_no_order',
            'tblclients.company as company',
            'tbl_suggest_plan_overtime.staff_plan as staff_plan',
            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
             FROM tblproduction_report
             WHERE tblproduction_report.object_id = tbl_suggest_plan_overtime.id AND tblproduction_report.object_type = "suggest_plan_overtime"
            ) as name_report',
            'tbl_suggest_plan_overtime.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_plan_overtime';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_orders ON tbl_orders.id = tbl_suggest_plan_overtime.order_id',
            'INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id',
        ];

        if (!$this->preViewSuggestPlanOvertime) {
            array_push($where,'AND (tbl_suggest_plan_overtime.created_by = '.get_staff_user_id().' OR tbl_suggest_plan_overtime.staff_plan = '.get_staff_user_id().' )');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            array_push($where, "AND tbl_suggest_plan_overtime.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_plan_overtime.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_plan_overtime/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">'.($aRow['reference_no_order']).'</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['company']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['staff_plan']) . '</div>';
            $arrReport = $aRow['name_report'];
            $htmlReport = '';
            if (!empty($arrReport)){
                $arrReport = explode('||',$arrReport);
                if (!empty($arrReport)){
                    foreach ($arrReport as $kk => $vv){
                        $vv = explode('__',$vv);
                        $htmlReport .= '<a class="c_modal" href="'.(admin_url('production_report/modal/' . $vv[1])).'">' . $vv[0] .'</a>';
                    }
                }
            }
            $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=suggest_plan_overtime') . '" class="btn btn-info">Báo cáo không phù hợp</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_plan_overtime/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestPlanOvertime ? '<a href="' . base_url('admin/suggest_plan_overtime/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestPlanOvertime ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_plan_overtime/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $this->db->select('tbl_suggest_plan_overtime.*');
        $this->db->from('tbl_suggest_plan_overtime');
        $this->db->where('tbl_suggest_plan_overtime.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_plan_overtime.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('po_id', lang("Lệnh sản xuất"), 'required');
            $this->form_validation->set_rules('order_id', lang("Đơn hàng"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('staff_plan', lang("Người lập kế hoạch"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_plan_overtime');
                    $date = to_sql_date($this->input->post('date'), true);
                    $time_finish = !empty($this->input->post('time_finish')) ? to_sql_date($this->input->post('time_finish'), true) : NULL;
                    $po_id = $this->input->post('po_id');
                    $branch_id = $this->input->post('branch_id');
                    $order_id = !empty($this->input->post('order_id')) ? $this->input->post('order_id') : 0;
                    $staff_plan = !empty($this->input->post('staff_plan')) ? $this->input->post('staff_plan') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $pod_id = $this->input->post('pod_id')[$value];
                            $order_item_id = $this->input->post('order_item_id')[$value];
                            $this->db->select('tbl_productions_orders_items.items_id as items_id,tbl_productions_orders_items.type_items');
                            $this->db->from('tbl_productions_orders_details');
                            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
                            $this->db->where('tbl_productions_orders_details.id',$pod_id);
                            $dtPod = $this->db->get()->row_array();
                            if (empty($dtPod)){
                                continue;
                            }
                            $item_id = $dtPod['items_id'];
                            $type_item = $dtPod['type_items'];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $stage_id = ($this->input->post('stage_id')[$value]);
                            $capacity_level = ($this->input->post('capacity_level')[$value]);
                            $category_overtime = ($this->input->post('category_overtime')[$value]);
                            $detail = ($this->input->post('detail')[$value]);
                            $staff_id = ($this->input->post('staff_id')[$value]);
                            $date_overtime = to_sql_date($this->input->post('date_overtime')[$value]);
                            $hour_start = ($this->input->post('hour_start')[$value]);
                            $hour_end = ($this->input->post('hour_end')[$value]);
                            $result_id = ($this->input->post('result_id')[$value]);
                            $time_overtime = countHourCheckOutNew(countHourCheckOut($hour_start, $hour_end));

                            $items[] = [
                                'order_item_id' => $order_item_id,
                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'stage_id' => $stage_id,
                                'capacity_level' => $capacity_level,
                                'time_overtime' => $time_overtime,
                                'category_overtime' => $category_overtime,
                                'detail' => $detail,
                                'staff_id' => $staff_id,
                                'date_overtime' => $date_overtime,
                                'hour_start' => $hour_start,
                                'hour_end' => $hour_end,
                                'result_id' => $result_id,
                                
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
                        'time_finish' => $time_finish,
                        'po_id' => $po_id,
                        'order_id' => $order_id,
                        'staff_plan' => $staff_plan,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_plan_overtime',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_plan_overtime') == $reference_no) {
                            updateReference('suggest_plan_overtime');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_plan_overtime_id'] = $id;
                            $this->db->insert('tbl_suggest_plan_overtime_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_plan_overtime',
                            'table_obj' => 'tbl_suggest_plan_overtime',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu kế hoạch tăng ca') . ' [' . $reference_no . ']',
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
                    $time_finish = !empty($this->input->post('time_finish')) ? to_sql_date($this->input->post('time_finish'), true) : NULL;
                    $po_id = $this->input->post('po_id');
                    $branch_id = $this->input->post('branch_id');
                    $order_id = !empty($this->input->post('order_id')) ? $this->input->post('order_id') : 0;
                    $staff_plan = !empty($this->input->post('staff_plan')) ? $this->input->post('staff_plan') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){

                            $pod_id = $this->input->post('pod_id')[$value];
                            $order_item_id = $this->input->post('order_item_id')[$value];
                            $this->db->select('tbl_productions_orders_items.items_id as items_id,tbl_productions_orders_items.type_items');
                            $this->db->from('tbl_productions_orders_details');
                            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
                            $this->db->where('tbl_productions_orders_details.id',$pod_id);
                            $dtPod = $this->db->get()->row_array();
                            if (empty($dtPod)){
                                continue;
                            }
                            $item_id = $dtPod['items_id'];
                            $type_item = $dtPod['type_items'];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $stage_id = ($this->input->post('stage_id')[$value]);
                            $capacity_level = ($this->input->post('capacity_level')[$value]);
                            $category_overtime = ($this->input->post('category_overtime')[$value]);
                            $detail = ($this->input->post('detail')[$value]);
                            $staff_id = ($this->input->post('staff_id')[$value]);
                            $date_overtime = to_sql_date($this->input->post('date_overtime')[$value]);
                            $hour_start = ($this->input->post('hour_start')[$value]);
                            $hour_end = ($this->input->post('hour_end')[$value]);
                            $result_id = ($this->input->post('result_id')[$value]);
                            $time_overtime = countHourCheckOutNew(countHourCheckOut($hour_start, $hour_end));
                            $suggest_plan_overtime_item_id = !empty($this->input->post('suggest_plan_overtime_item_id')[$value]) ? $this->input->post('suggest_plan_overtime_item_id')[$value] : 0;

                            $items[] = [
                                'id' => $suggest_plan_overtime_item_id,
                                'order_item_id' => $order_item_id,
                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'stage_id' => $stage_id,
                                'capacity_level' => $capacity_level,
                                'time_overtime' => $time_overtime,
                                'category_overtime' => $category_overtime,
                                'detail' => $detail,
                                'staff_id' => $staff_id,
                                'date_overtime' => $date_overtime,
                                'hour_start' => $hour_start,
                                'hour_end' => $hour_end,
                                'result_id' => $result_id,
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
                        'time_finish' => $time_finish,
                        'po_id' => $po_id,
                        'order_id' => $order_id,
                        'staff_plan' => $staff_plan,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_plan_overtime',$fields);
                    if ($success){
                        $this->db->where('suggest_plan_overtime_id',$id);
                        $this->db->delete('tbl_suggest_plan_overtime_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_plan_overtime_id'] = $id;
                            $this->db->insert('tbl_suggest_plan_overtime_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_plan_overtime',
                            'table_obj' => 'tbl_suggest_plan_overtime',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu kế hoạch tăng ca') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestPlanOvertime){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_plan_overtime');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_overtime'), 'page' => lang('dt_suggest_plan_overtime')), array('link' => '#', 'page' => lang('dt_add_suggest_plan_overtime'))];
            } else {
                if (!$this->preEditSuggestPlanOvertime){
                    accessDenied(true);
                }

                $this->db->where('suggest_plan_overtime_id', $id);
                $this->db->where('status', 1);
                $suggestPlanOvertime = $this->db->get('tbl_suggest_plan_overtime_item')->row_array();

                if (!empty($suggestPlanOvertime)) {
                    set_alert('danger',  'Có chi tiết phiếu yêu cầu phép đã được duyệt không thể sửa !');
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $this->db->select('tbl_suggest_plan_overtime_item.*');
                $this->db->from('tbl_suggest_plan_overtime_item');
                $this->db->where('tbl_suggest_plan_overtime_item.suggest_plan_overtime_id',$id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_plan_overtime');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_overtime'), 'page' => lang('dt_suggest_plan_overtime')), array('link' => '#', 'page' => lang('dt_edit_suggest_plan_overtime'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_plan_overtime');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtCategoryStage'] = get_table_where('tbl_category_stages');
        $data['dtStaff'] = get_table_where('tblstaff',['active' => 1]);
        $this->load->view('admin/suggest_plan_overtime/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_plan_overtime');

        $this->db->select('tbl_suggest_plan_overtime.*');
        $this->db->from('tbl_suggest_plan_overtime');
        $this->db->where('tbl_suggest_plan_overtime.id',$id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_plan_overtime_item.*,
            tbl_result.name as name_result,
            tbl_category_stages.name as name_category_stage,
        ');
        $this->db->from('tbl_suggest_plan_overtime_item');
        $this->db->join('tbl_result','tbl_result.id = tbl_suggest_plan_overtime_item.result_id','left');
        $this->db->join('tbl_category_stages','tbl_category_stages.id = tbl_suggest_plan_overtime_item.stage_id','left');
        $this->db->where('tbl_suggest_plan_overtime_item.suggest_plan_overtime_id',$id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/suggest_plan_overtime/view',$data);
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
        if (!$this->preDeleteSuggestPlanOvertime){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_plan_overtime.*');
        $this->db->from('tbl_suggest_plan_overtime');
        $this->db->where('tbl_suggest_plan_overtime.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('suggest_plan_overtime_id', $id);
        $this->db->where('status', 1);
        $suggestPlanOvertime = $this->db->get('tbl_suggest_plan_overtime_item')->row_array();

        if (!empty($suggestPlanOvertime)) {
            set_alert('danger',  'Có chi tiết phiếu yêu cầu phép đã được duyệt không thể xóa !');
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_plan_overtime');
        if ($success){
            $this->db->where('tbl_suggest_plan_overtime_item.suggest_plan_overtime_id',$id);
            $this->db->delete('tbl_suggest_plan_overtime_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_plan_overtime',
                'table_obj' => 'tbl_suggest_plan_overtime',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu kế hoạch tăng ca') . ' [' . $dtData['reference_no'] . ']',
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
        if (!empty($term))
        {
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

            $tb_tamp_date = "(
                SELECT
                    tbl_order_item_shippings.order_item_id as order_item_id,
                    tbl_order_item_shippings.date_shipping as date_delivery
                FROM tbl_order_item_shippings
                GROUP BY tbl_order_item_shippings.order_item_id
            ) tb_tamp_date";

            $this->db->select('
                tbl_suggest_plan_overtime.id as id,
                tbl_suggest_plan_overtime.reference_no as reference_no,
                tbl_suggest_plan_overtime.date as date,
                tbl_orders.reference_no as reference_no_order,
                tbl_orders.reference_no_customer as reference_no_customer,
                tbl_type_orders.name as name_type_order,
                tbl_orders.date as date_order,
                COALESCE(tb_tamp_date.date_delivery,"") as date_delivery,
                tblclients.zcode as code_client,
                tblclients.company as company,
                tbl_productions_orders.reference_no as reference_no_po,
                tbl_category_stages.name as name_category_stage,
                tbl_suggest_plan_overtime_item.quantity as quantity,
                tbl_suggest_plan_overtime_item.capacity_level as capacity_level,
                tbl_suggest_plan_overtime_item.time_overtime as time_overtime,
                tbl_suggest_plan_overtime_item.category_overtime as category_overtime,
                tbl_suggest_plan_overtime_item.detail as detail,
                tbl_suggest_plan_overtime_item.staff_id as staff_id,
                tbl_suggest_plan_overtime_item.hour_start as hour_start,
                tbl_suggest_plan_overtime_item.hour_end as hour_end,
                tbl_result.name as name_result,
                (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_suggest_plan_overtime.id AND tblproduction_report.object_type = "suggest_plan_overtime"
                ) as name_report,
                tbl_suggest_plan_overtime.staff_plan as staff_plan,
                tbl_suggest_plan_overtime_item.status as status,
                tbl_suggest_plan_overtime_item.item_id as item_id,
                tbl_suggest_plan_overtime_item.type_item as type_item,
                tbl_suggest_plan_overtime_item.date_overtime as date_overtime,
                tbl_suggest_plan_overtime.note as note,
            ');
            $this->db->from('tbl_suggest_plan_overtime');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_suggest_plan_overtime.order_id', 'left');
            //            $this->db->join('tbl_type_orders', 'tbl_type_orders.id = tbl_orders.type_orders', 'inner');
            $this->db->join('tbl_type_orders', 'tbl_type_orders.id = tbl_orders.type_orders', 'left');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_suggest_plan_overtime.po_id', 'left');
            $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
            $this->db->join('tbl_suggest_plan_overtime_item','tbl_suggest_plan_overtime_item.suggest_plan_overtime_id = tbl_suggest_plan_overtime.id');
            $this->db->join($tb_tamp_date,'tb_tamp_date.order_item_id = tbl_suggest_plan_overtime_item.order_item_id','left');
            $this->db->join('tbl_category_stages','tbl_category_stages.id = tbl_suggest_plan_overtime_item.stage_id');
            $this->db->join('tbl_result','tbl_result.id = tbl_suggest_plan_overtime_item.result_id', 'left');

            if (!$this->preViewSuggestPlanOvertime) {
                $this->db->where('(tbl_suggest_plan_overtime.created_by = '.get_staff_user_id().' OR tbl_suggest_plan_overtime.staff_plan = '.get_staff_user_id().' )');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_plan_overtime.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_plan_overtime.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_plan_overtime.id desc');
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
                ('PHIẾU YÊU CẦU KẾ HOẠCH TĂNG CA'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:AF1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã ĐĐH(TD)');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mã ĐĐH(KH)');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Loại ĐĐH');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Ngày Lập ĐĐH');
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Ngày Giao');
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Mã KH')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Khách Hàng')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Brand')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Chỉ Lệnh')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Mã Thành Phẩm(TD)')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Tên Thành Phẩm(TD)');
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Tên Thành Phẩm(KH)')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Quy Cách')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'ĐVT')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Mã Lệnh Sản Xuất')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Nhóm Công Đoạn Tăng Ca')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Tổng SL')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Định Mức Năng Suất')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'Số Thời Gian Tăng Ca')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$sttRow.'', 'Nhóm Tăng Ca')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$sttRow.'', 'Chi Tiết')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$sttRow.'', 'Danh Sách Nhân Viên')->getStyle("Y$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Z'.$sttRow.'', 'Thời Gian Bắt Đầu Tăng Ca')->getStyle("Z$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AA'.$sttRow.'', 'Thời Gian Kết Thúc Tăng Ca')->getStyle("AA$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AB'.$sttRow.'', 'Kết Quả')->getStyle("AB$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AC'.$sttRow.'', 'Báo Cáo Không Phù Hợp')->getStyle("AC$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AD'.$sttRow.'', 'Người Lập Kế Hoạch')->getStyle("AD$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AE'.$sttRow.'', 'Nhân Viên Điều Độ Tăng Ca')->getStyle("AE$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AF'.$sttRow.'', 'Hành Chính Nhân Sự Duyệt')->getStyle("AF$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AG'.$sttRow.'', 'Ngày Đề Xuất')->getStyle("AG$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AH'.$sttRow.'', 'Ghi Chú')->getStyle("AH$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:AH$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['reference_no_order']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['reference_no_customer']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['name_type_order']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", _dt($value['date_order']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",!empty($value['date_delivery']) ? _dhau($value['date_delivery']) : '')->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['code_client'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['company'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", !empty($dtBand) ? $dtBand['name'] : '')->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", '')->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", ($info['code']))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", ($info['name']))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", ($info['name_customer']))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", ($info['mode']))->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $unit['unit'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $value['reference_no_po'])->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $value['name_category_stage'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $value['quantity'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", $value['capacity_level'])->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", $value['time_overtime'])->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", $value['category_overtime'])->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", $value['detail'])->getStyle("X$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("Y$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin", ($value['hour_start']))->getStyle("Z$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin", ($value['hour_end']))->getStyle("AA$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AB$rowBegin", ($value['name_result']))->getStyle("AB$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AC$rowBegin", ($value['name_report']))->getStyle("AC$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AD$rowBegin", get_staff_full_name($value['staff_plan']))->getStyle("AD$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AE$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("AE$rowBegin")->getAlignment()->setWrapText(true);
                    $htmlStatus = '';
                    if ($value['status'] == 0){
                        $htmlStatus = 'Chưa duyệt';
                    } else {
                        $htmlStatus = 'Đã duyệt';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("AF$rowBegin", $htmlStatus)->getStyle("AF$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AG$rowBegin", _dC($value['date_overtime']))->getStyle("AG$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("AH$rowBegin", $value['note'])->getStyle("AH$rowBegin")->getAlignment()->setWrapText(true);


                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:AH$rowBegin")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->getStyle("Q$rowBegin:Q$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("T$rowBegin:T$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("Z$rowBegin:AA$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_ke_hoach_tang_ca') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
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
        $id = $this->input->get('id');
        $result = $this->input->get('result');

        if (empty($result)) {
            $result = 0;
        }
        $messaAdd = 'Duyệt';
        if ($result == 0) {
            $messaAdd = 'Bỏ Duyệt';
        }

        $this->db->where('id', $id);
        $success = $this->db->update('tbl_suggest_plan_overtime_item', [
            'result_id' => $result
        ]);
        if (!empty($success)) {

            echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Check ' . $messaAdd . ' kết quả thành công']);
            die();
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Check ' . $messaAdd . ' kết quả không thành công']);
        die();
    }
}