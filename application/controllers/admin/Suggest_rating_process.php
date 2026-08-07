<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_rating_process extends AdminController
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

        $this->preViewSuggestRatingProcess = true;
        $this->preViewOwnSuggestRatingProcess = true;
        $this->preAddSuggestRatingProcess = true;
        $this->preEditSuggestRatingProcess = true;
        $this->preApproveSuggestRatingProcess = true;
        $this->preDeleteSuggestRatingProcess = true;
    }

    public function index(){
        if (!$this->preViewSuggestRatingProcess && !$this->preViewOwnSuggestRatingProcess){
            access_denied();
        }
        $data['title'] = _l('dt_suggest_rating_process');
        $this->load->view('admin/suggest_rating_process/index', $data);
    }

    public function getSuggestRatingProcess(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_rating_process.id as id',
            'tbl_suggest_rating_process.reference_no as reference_no',
            'tbl_suggest_rating_process.date as date',
            'tbl_suggest_rating_process.status as status',
            'tbl_suggest_rating_process.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_rating_process';
        $where = [

        ];
        $filter = [];

        $join = [
        ];

        if (!$this->preViewSuggestRatingProcess) {
            array_push($where,'AND tbl_suggest_rating_process.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            array_push($where, "AND tbl_suggest_rating_process.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_rating_process.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_rating_process.date_status',
            'tbl_suggest_rating_process.staff_status'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_rating_process/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 1)\' id=\'agree\' suggest_id=\''.$aRow['id'].'\' value=\'1\' class=\'btn btn-success\'>'.lang('tnh_agree').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-danger po">'.lang('Chưa duyệt').'</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 0)\' id=\'agree\' suggest_id=\''.$aRow['id'].'\' value=\'0\' class=\'btn btn-danger\'>'.lang('Hủy duyệt').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-success po">'.lang('Đã duyệt').'</span></div>';
                $_data.= '<div style="margin-top: 5px"> Người duyệt: '.get_staff_full_name($aRow['staff_status']).'</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left">'.$_data.'</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_rating_process/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestRatingProcess ? '<a href="' . base_url('admin/suggest_rating_process/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestRatingProcess ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_rating_process/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $row[] = '<div>'.$actions.'</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0){
        $data = [];
        if ($this->input->post()){
            if (empty($id)){
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_rating_process.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_rating_process');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $system_id = $this->input->post('system_id')[$value];
                            if (empty($system_id)) continue;

                            $result = ($this->input->post('result')[$value]);
                            $detail = !empty($this->input->post('detail')[$value]) ? $this->input->post('detail')[$value] : null ;
                            $standard = ($this->input->post('standard')[$value]);
                            $items[] = [
                                'system_id' => $system_id,
                                'detail' => $detail,
                                'result_id' => $result,
                                'standard' => $standard,
                            ];
                        }
                    }
                    if (empty($items)){
                        $data['result'] = 0;
                        $data['message'] = lang('Không có chi tiết!');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_rating_process',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_rating_process') == $reference_no) {
                            updateReference('suggest_rating_process');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_rating_process_id'] = $id;
                            $this->db->insert('tbl_suggest_rating_process_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_rating_process',
                            'table_obj' => 'tbl_suggest_rating_process',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu đánh giá quy trình') . ' [' . $reference_no . ']',
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
                $this->db->select('tbl_suggest_rating_process.*');
                $this->db->from('tbl_suggest_rating_process');
                $this->db->where('tbl_suggest_rating_process.id',$id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_rating_process.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $system_id = $this->input->post('system_id')[$value];
                            if (empty($system_id)) continue;

                            $result = ($this->input->post('result')[$value]);
                            $detail = !empty($this->input->post('detail')[$value]) ? $this->input->post('detail')[$value] : null ;
                            $suggest_rating_process_item_id = !empty($this->input->post('suggest_rating_process_item_id')[$value]) ? $this->input->post('suggest_rating_process_item_id')[$value] : 0 ;
                            $standard = ($this->input->post('standard')[$value]);
                            $items[] = [
                                'id' => $suggest_rating_process_item_id,
                                'system_id' => $system_id,
                                'detail' => $detail,
                                'result_id' => $result,
                                'standard' => $standard,
                            ];

                        }
                    }
                    if (empty($items)){
                        $data['result'] = 0;
                        $data['message'] = lang('Không có chi tiết!');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'date' => $date,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_rating_process',$fields);
                    if ($success){
                        $this->db->where('suggest_rating_process_id',$id);
                        $this->db->delete('tbl_suggest_rating_process_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_rating_process_id'] = $id;
                            $this->db->insert('tbl_suggest_rating_process_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_rating_process',
                            'table_obj' => 'tbl_suggest_rating_process',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu đánh giá hệ thống') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestRatingProcess){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_rating_process');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_rating_process'), 'page' => lang('dt_suggest_rating_process')), array('link' => '#', 'page' => lang('dt_add_suggest_rating_process'))];
            } else {
                if (!$this->preEditSuggestRatingProcess){
                    accessDenied(true);
                }
                $this->db->select('tbl_suggest_rating_process.*');
                $this->db->from('tbl_suggest_rating_process');
                $this->db->where('tbl_suggest_rating_process.id',$id);
                $dtData = $this->db->get()->row_array();

                if ($dtData['status'] == 1){
                    set_alert('danger',  'Phiếu đã duyệt không thể sửa !');
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $this->db->select('tbl_suggest_rating_process_item.*,tbl_system.code as code_system,tbl_system.name as name_system');
                $this->db->from('tbl_suggest_rating_process_item');
                $this->db->join('tbl_system','tbl_system.id = tbl_suggest_rating_process_item.system_id');
                $this->db->where('tbl_suggest_rating_process_item.suggest_rating_process_id',$id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_rating_process');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_rating_process'), 'page' => lang('dt_suggest_rating_process')), array('link' => '#', 'page' => lang('dt_edit_suggest_rating_process'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_rating_process');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtSystem'] = get_table_where('tbl_system',['type' => 2]);
        $this->load->view('admin/suggest_rating_process/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_rating_process');

        $this->db->select('tbl_suggest_rating_process.*');
        $this->db->from('tbl_suggest_rating_process');
        $this->db->where('tbl_suggest_rating_process.id',$id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_rating_process_item.*,
            tbl_result.name as name_result,
            tbl_system.code as code_system,
            tbl_system.name as name_system,
        ');
        $this->db->from('tbl_suggest_rating_process_item');
        $this->db->join('tbl_system','tbl_system.id = tbl_suggest_rating_process_item.system_id','inner');
        $this->db->join('tbl_result','tbl_result.id = tbl_suggest_rating_process_item.result_id','left');
        $this->db->where('tbl_suggest_rating_process_item.suggest_rating_process_id',$id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/suggest_rating_process/view',$data);
    }

    public function agree(){
        if (!$this->preApproveSuggestRatingProcess) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_rating_process.*');
        $this->db->from('tbl_suggest_rating_process');
        $this->db->where('tbl_suggest_rating_process.id',$suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 0) {
                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'suggest_rating_process');
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
            $up = $this->db->update('tbl_suggest_rating_process',$options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_rating_process',
                    'table_obj' => 'tbl_suggest_rating_process',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu đánh giá quy trình') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteSuggestRatingProcess){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_rating_process.*');
        $this->db->from('tbl_suggest_rating_process');
        $this->db->where('tbl_suggest_rating_process.id',$id);
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
        $success = $this->db->delete('tbl_suggest_rating_process');
        if ($success){
            $this->db->where('tbl_suggest_rating_process_item.suggest_rating_process_id',$id);
            $this->db->delete('tbl_suggest_rating_process_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_rating_process',
                'table_obj' => 'tbl_suggest_rating_process',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu đánh giá quy trình') . ' [' . $dtData['reference_no'] . ']',
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

    public function searchMachines($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_machines.id as id, 
            tbl_machines.name as text
        ', false);
        $this->db->from('tbl_machines');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_machines.name', $term);
            $this->db->or_like('tbl_machines.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Thiết bị'), 'children' => $pod];
        if (!empty($id)){
            $dtMachines = get_table_where('tbl_machines',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name']];
        }
        echo json_encode($data);
    }

    public function searchProductionReports($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tblproduction_report.id as id, 
            tblproduction_report.name_report as text
        ', false);
        $this->db->from('tblproduction_report');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblproduction_report.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Phiếu báo cáo không phù hợp'), 'children' => $pod];
        if (!empty($id)){
            $dtMachines = get_table_where('tblproduction_report',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name_report']];
        }
        echo json_encode($data);
    }

    public function searchSuppliers($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tblsuppliers.id as id, 
            tblsuppliers.company as text
        ', false);
        $this->db->from('tblsuppliers');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblsuppliers.company', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Nhà cung cấp'), 'children' => $pod];
        if (!empty($id)){
            $dtMachines = get_table_where('tblsuppliers',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['company']];
        }
        echo json_encode($data);
    }

    public function SearchSystems($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $type = $this->input->get('types');
        $this->db->select('
            tbl_system.id as id, 
            tbl_system.name as text,
            tbl_system.code as code,
            tbl_system.name as name,
        ', false);
        $this->db->from('tbl_system');
        $this->db->where('tbl_system.type',$type);
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_system.code', $term);
            $this->db->or_like('tbl_system.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Mã hệ thống'), 'children' => $pod];
        if (!empty($id)){
            $dtMachines = get_table_where('tbl_system',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name']];
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
                tbl_suggest_rating_process.id as id,
                tbl_suggest_rating_process.reference_no as reference_no,
                tbl_suggest_rating_process.date as date,
                tbl_system.code as code_system,
                tbl_system.name as name_system,
                tbl_suggest_rating_process_item.detail as detail,
                tbl_result.name as name_result,
                (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_suggest_rating_process_item.id AND tblproduction_report.object_type = "suggest_rating_process_item"
                ) as name_report,
                tbl_result.name as name_result,
                tbl_suggest_rating_process_item.standard as standard,
                tbl_suggest_rating_process.barcode as barcode,
            ');
            $this->db->from('tbl_suggest_rating_process');
            $this->db->join('tbl_suggest_rating_process_item','tbl_suggest_rating_process_item.suggest_rating_process_id = tbl_suggest_rating_process.id');
            $this->db->join('tbl_system','tbl_system.id = tbl_suggest_rating_process_item.system_id');
            $this->db->join('tbl_result','tbl_result.id = tbl_suggest_rating_process_item.result_id');

            if (!$this->preViewSuggestRatingProcess) {
                $this->db->where('tbl_suggest_rating_process.created_by = '.get_staff_user_id().'');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_rating_process.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_rating_process.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_rating_process.id desc');
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
                ('PHIẾU YÊU CẦU ĐÁNH GIÁ QUY TRÌNH'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã Quy Trình');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Tên Quy Trình');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Chi Tiết Quy Trình')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Kết Quả')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Báo Cáo Không Phù Hợp')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Tiêu Chuẩn/ Quy Định')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:J$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code_system'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['name_system']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['detail']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['name_result'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['name_report'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['standard'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])){
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_rating_process||'.$value['id'];
                        $this->db->where('id',$value['id']);
                        $this->db->update('tbl_suggest_rating_process',['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_rating_process/';
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
                        $objDrawing1->setCoordinates('J' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", '')->getStyle("J$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:J$rowBegin")->applyFromArray([
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
                }
            }
            $filename = lang('phieu_yeu_cau_danh_gia_quy_trinh') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
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

    public function changeResult(){
        $id = $this->input->post('id');
        $result_id = $this->input->post('result_id');
        $this->db->where('id',$id);
        $success = $this->db->update('tbl_suggest_rating_process_item',[
            'result_id' => $result_id
        ]);
        if ($success){
            $data['result'] = true;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = false;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }
}