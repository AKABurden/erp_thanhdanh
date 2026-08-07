<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_rating_machines extends AdminController
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

        $this->preViewSuggestRatingMachines = true;
        $this->preViewOwnSuggestRatingMachines = true;
        $this->preAddSuggestRatingMachines = true;
        $this->preEditSuggestRatingMachines = true;
        $this->preApproveSuggestRatingMachines = true;
        $this->preDeleteSuggestRatingMachines = true;
    }

    public function index()
    {
        if (!$this->preViewSuggestRatingMachines && !$this->preViewOwnSuggestRatingMachines) {
            access_denied();
        }
        $data['title'] = _l('dt_suggest_rating_machines');
        $this->load->view('admin/suggest_rating_machines/index', $data);
    }

    public function getSuggestRatingMachines()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_rating_machines.id as id',
            'tbl_suggest_rating_machines.reference_no as reference_no',
            'tbl_suggest_rating_machines.date as date',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tbl_machines.status as status_machines',
            'tbl_machines.product_in_month as product_in_month',
            'tbl_packaging.name as name_standard',
            'tbl_machines.pp_measure as pp_measure',
            'tbl_machines.quota_productivity as quota_productivity',
            'tbl_machines.day_operation as day_operation',
            'tbl_machines.operating_gauge as operating_gauge',
            'tbl_machines.preparation_time as preparation_time',
            'tbl_machines.specifications as specifications',
            'tbl_machines_maintenance.name as name_machines_maintenance',
            'tbl_machines_maintenance.month as month',
            'tbl_machines_maintenance.note_main as note_main',
            'tbl_machines.product_color as product_color',
            '(SELECT GROUP_CONCAT(tbl_category_stages.name) as name_stage
                FROM tbl_machines_stage 
                JOIN tbl_category_stages ON tbl_category_stages.id = tbl_machines_stage.category_stage_id
                WHERE tbl_machines_stage.machines_id = tbl_machines.id
               ) as name_stage',
            'tbl_machines.soup_ingredients as soup_ingredients',
            'tbl_suggest_rating_machines.status as status',
            'tbl_suggest_rating_machines.status_finish as status_finish',
            'tbl_suggest_rating_machines.content as content',
            'tbl_result.name as name_result',
            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
             FROM tblproduction_report
             WHERE tblproduction_report.object_id = tbl_suggest_rating_machines.id AND tblproduction_report.object_type = "suggest_rating_machines"
            ) as name_report',
            'tbl_suggest_rating_machines.standard as standard',
            'tbl_suggest_rating_machines.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_rating_machines';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_machines ON tbl_machines.id = tbl_suggest_rating_machines.machines_id',
            'LEFT JOIN tbl_packaging ON tbl_packaging.id = tbl_machines.standard',
            'LEFT JOIN tbl_machines_maintenance ON tbl_machines_maintenance.machines_id = tbl_machines.id',
            'INNER JOIN tbl_result ON tbl_result.id = tbl_suggest_rating_machines.result_id',
        ];

        if (!$this->preViewSuggestRatingMachines) {
            array_push($where, 'AND tbl_suggest_rating_machines.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_rating_machines.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_rating_machines.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_rating_machines.date_status',
            'tbl_suggest_rating_machines.staff_status',
            'tbl_suggest_rating_machines.date_status_finish',
            'tbl_suggest_rating_machines.staff_status_finish',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_rating_machines/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_machines']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_machines']) . '</div>';
            if(!empty($value['status_machines'])){
                $htmlStatus =  status_machine_new()[$value['status_machines']];
            } else {
                $htmlStatus = '';
            }
            $row[] = '<div class="text-left">' . $htmlStatus . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['product_in_month']) . '</div>';
            $row[] = '<div class="text-left" style="width: 200px">' . ($aRow['name_standard']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['pp_measure']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['quota_productivity']) . '</div>';
            $row[] = '<div class="text-left">' . (!empty($value['day_operation']) ? _dhau($value['day_operation']) : '') . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['operating_gauge']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['preparation_time']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['specifications']) . '</div>';
            $row[] = '<div class="text-left"></div>';
            $row[] = '<div class="text-left">' . ($aRow['name_machines_maintenance']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['month']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['note_main']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['product_color']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_stage']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['soup_ingredients']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } elseif ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left">' . $_data . '</div>';

            if ($aRow['status_finish'] == 0) {
                $_data_finish = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agreeFinish(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('Hoàn thành') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa hoàn thành') . '</span></div>';
            } elseif ($aRow['status_finish'] == 1) {
                $_data_finish = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agreeFinish(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy hoàn thành') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã hoàn thành') . '</span></div>';
                $_data_finish .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status_finish']) . '</div>';
            } else {
                $_data_finish = '';
            }
            $row[] = '<div class="text-left">' . $_data_finish . '</div>';

            $row[] = '<div class="text-left">' . $aRow['content'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_result'] . '</div>';
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
            if ($aRow['status'] == 1) {
                $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=suggest_rating_machines') . '" class="btn btn-info">Tạo phiếu báo cáo không phù hợp</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>
            ';
            } else {
                $row[] = '';
            }
            $row[] = '<div class="text-left">' . ($aRow['standard']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_rating_machines/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestRatingMachines ? '<a class="tnh-modal" href="' . base_url('admin/suggest_rating_machines/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestRatingMachines ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_rating_machines/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0){
        $data = [];
        if ($this->input->post()){
            if (empty($id)){
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_rating_machines.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
                $this->form_validation->set_rules('result_id', lang("Kết quả"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_rating_machines');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $machines_id = $this->input->post('machines_id');
                    $result_id = $this->input->post('result_id');
                    $standard = $this->input->post('standard');
                    $content = ($this->input->post('content'));
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'machines_id' => $machines_id,
                        'result_id' => $result_id,
                        'standard' => $standard,
                        'content' => $content,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_rating_machines',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_rating_machines') == $reference_no) {
                            updateReference('suggest_rating_machines');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_rating_machines',
                            'table_obj' => 'tbl_suggest_rating_machines',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu đánh giá thiết bị') . ' [' . $reference_no . ']',
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
                $this->db->select('tbl_suggest_rating_machines.*');
                $this->db->from('tbl_suggest_rating_machines');
                $this->db->where('tbl_suggest_rating_machines.id',$id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_rating_machines.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
                $this->form_validation->set_rules('result_id', lang("Kết quả"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $machines_id = $this->input->post('machines_id');
                    $result_id = $this->input->post('result_id');
                    $standard = $this->input->post('standard');
                    $content = ($this->input->post('content'));
                    $fields = [
                        'date' => $date,
                        'machines_id' => $machines_id,
                        'result_id' => $result_id,
                        'standard' => $standard,
                        'content' => $content,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_rating_machines',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_rating_machines',
                            'table_obj' => 'tbl_suggest_rating_machines',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu đánh giá thiết bị') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestRatingMachines){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_rating_machines');
            } else {
                if (!$this->preEditSuggestRatingMachines){
                    accessDenied(true);
                }
                $this->db->select('tbl_suggest_rating_machines.*');
                $this->db->from('tbl_suggest_rating_machines');
                $this->db->where('tbl_suggest_rating_machines.id',$id);
                $dtData = $this->db->get()->row_array();

                if ($dtData['status'] == 1){
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));

                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_suggest_rating_machines');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_rating_machines');
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/suggest_rating_machines/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_rating_machines');

        $this->db->select('tbl_suggest_rating_machines.*,
           tbl_result.name as name_result,
           tbl_machines.*,
           tbl_machines.status as status_machines,
           tbl_machines.code as code_machines,
           tbl_machines.name as name_machines,
           tbl_packaging.name as name_standard,
           (SELECT GROUP_CONCAT(tbl_category_stages.name) as name_stage
            FROM tbl_machines_stage 
            JOIN tbl_category_stages ON tbl_category_stages.id = tbl_machines_stage.category_stage_id
            WHERE tbl_machines_stage.machines_id = tbl_machines.id
           ) as name_stage
        ');
        $this->db->from('tbl_suggest_rating_machines');
        $this->db->join('tbl_machines','tbl_machines.id = tbl_suggest_rating_machines.machines_id','inner');
        $this->db->join('tbl_packaging','tbl_packaging.id = tbl_machines.standard','left');
        $this->db->join('tbl_result','tbl_result.id = tbl_suggest_rating_machines.result_id','left');
        $this->db->where('tbl_suggest_rating_machines.id',$id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_machines_maintenance.*');
        $this->db->from('tbl_machines_maintenance');
        $this->db->where('tbl_machines_maintenance.machines_id',(!empty($dtData) ? $dtData['machines_id'] : 0));
        $dtMachinesMain = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtMachinesMain'] = $dtMachinesMain;
        $this->load->view('admin/suggest_rating_machines/view',$data);
    }

        public function agree(){
        if (!$this->preApproveSuggestRatingMachines) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_rating_machines.*');
        $this->db->from('tbl_suggest_rating_machines');
        $this->db->where('tbl_suggest_rating_machines.id',$suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 0) {

                if ($dtData['status_finish'] == 1) {
                    $data['result'] = 0;
                    $data['message'] = lang('Phiếu đã hoàn thành không thể hủy duyệt !');
                    echo responseData($data); return;
                }

                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'suggest_rating_machines');
                $this->db->where('tblproduction_report.object_id', $suggest_id);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
                    echo json_encode($data);
                    die();
                }
            }

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
            $up = $this->db->update('tbl_suggest_rating_machines',$options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_rating_machines',
                    'table_obj' => 'tbl_suggest_rating_machines',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu đánh giá thiết bị') . ' [' . $dtData['reference_no'] . ']',
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

    public function agreeFinish(){
        if (!$this->preApproveSuggestRatingMachines) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_rating_machines.*');
        $this->db->from('tbl_suggest_rating_machines');
        $this->db->where('tbl_suggest_rating_machines.id',$suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 1) {
                if ($dtData['status'] == 0) {
                    $data['result'] = 0;
                    $data['message'] = lang('Phiếu chưa được duyệt không thể hoàn thành !');
                    echo responseData($data); return;
                }
            }

            if (($dtData['status_finish'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data); return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status_finish' => $status,
                'date_status_finish' => $date_status,
                'staff_status_finish' => $staff_status,
            ];

            $this->db->where('id',$suggest_id);
            $up = $this->db->update('tbl_suggest_rating_machines',$options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_rating_machines',
                    'table_obj' => 'tbl_suggest_rating_machines',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt hoàn thành phiếu yêu cầu đánh giá thiết bị') . ' [' . $dtData['reference_no'] . ']',
                    'actions' => 'approved_finish'
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
        if (!$this->preDeleteSuggestRatingMachines){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_rating_machines.*');
        $this->db->from('tbl_suggest_rating_machines');
        $this->db->where('tbl_suggest_rating_machines.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        if ($dtData['status'] == 1){
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã duyệt không thể xóa !');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_rating_machines');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'suggest_rating_machines',
                'table_obj' => 'tbl_suggest_rating_machines',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu đánh giá thiết bị') . ' [' . $dtData['reference_no'] . ']',
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
               tbl_suggest_rating_machines.*,
               tbl_suggest_rating_machines.id as suggest_rating_machines_id,
               tbl_result.name as name_result,
               tbl_machines.*,
               tbl_machines.id as machines_id,
               tbl_machines.status as status_machines,
               tbl_machines.code as code_machines,
               tbl_machines.name as name_machines,
               tbl_packaging.name as name_standard,
               (SELECT GROUP_CONCAT(tbl_category_stages.name) as name_stage
                FROM tbl_machines_stage 
                JOIN tbl_category_stages ON tbl_category_stages.id = tbl_machines_stage.category_stage_id
                WHERE tbl_machines_stage.machines_id = tbl_machines.id
               ) as name_stage,
               tbl_machines_maintenance.name as name_machines_maintenance,
               tbl_machines_maintenance.month as month,
               tbl_machines_maintenance.note_main as note_main,
               (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_suggest_rating_machines.id AND tblproduction_report.object_type = "suggest_rating_machines"
                ) as name_report
            ');
            $this->db->from('tbl_suggest_rating_machines');
            $this->db->join('tbl_machines','tbl_machines.id = tbl_suggest_rating_machines.machines_id','inner');
            $this->db->join('tbl_packaging','tbl_packaging.id = tbl_machines.standard','left');
            $this->db->join('tbl_machines_maintenance','tbl_machines_maintenance.machines_id = tbl_machines.id','left');
            $this->db->join('tbl_result','tbl_result.id = tbl_suggest_rating_machines.result_id','left');


            if (!$this->preViewSuggestRatingMachines) {
                $this->db->where('tbl_suggest_rating_machines.created_by = '.get_staff_user_id().'');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_rating_machines.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_rating_machines.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_rating_machines.id desc');
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
                ('PHIẾU YÊU CẦU ĐÁNH GIÁ THIẾT BỊ'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:Z1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã Thiết Bị/Công Việc');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Tên Thiết Bị/Công Việc');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Trạng Thái')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Định Mức Năng Suất/Tháng')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Tiêu Chuẩn')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Phương Pháp Kiểm')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Định Mức Năng Suất/h')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Ngày Bắt Đầu Bảo Trì')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Khổ Vận Hành')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Thời Gian Chuẩn Bị (Giờ))')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Thông số kỹ thuật)')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Quy Trình')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Bộ Phận Máy Móc')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Số Ngày Cần Bảo Trì')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Ghi Chú Cách Thức Bảo Trì')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Định Mức Thời Gian Duyệt Màu')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Nhóm Công Đoạn')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'NPL canh bài')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'Nội Dung Đánh Giá')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$sttRow.'', 'Kết Quả')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$sttRow.'', 'Báo Cáo Không Phù Hợp')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$sttRow.'', 'Tiêu Chuẩn/ Quy Định')->getStyle("Y$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Z'.$sttRow.'', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:Z$sttRow")->applyFromArray([
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
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code_machines'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['name_machines']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    if(!empty($value['status_machines'])){
                        $htmlStatus =  status_machine_new()[$value['status_machines']];
                    } else {
                        $htmlStatus = '';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $htmlStatus)->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['product_in_month'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['name_standard'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['pp_measure'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['quota_productivity'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", !empty($value['day_operation']) ? _dhau($value['day_operation']) : '')->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['operating_gauge'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['preparation_time'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['specifications'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", '')->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['name_machines_maintenance'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['month'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $value['note_main'])->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $value['product_color'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $value['name_stage'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", $value['soup_ingredients'])->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", $value['content'])->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", $value['name_result'])->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", $value['name_report'])->getStyle("X$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin", $value['standard'])->getStyle("Y$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])){
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_rating_machines||'.$value['suggest_rating_machines_id'];
                        $this->db->where('id',$value['suggest_rating_machines_id']);
                        $this->db->update('tbl_suggest_rating_machines',['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_rating_machines/';
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
                        $objDrawing1->setCoordinates('Z' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin", '')->getStyle("Z$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:Z$rowBegin")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->getStyle("J$rowBegin:M$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_danh_gia_thiet_bi') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(10);
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