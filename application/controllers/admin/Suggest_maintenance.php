<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_maintenance extends AdminController
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

        $this->preViewSuggestMaintenance = true;
        $this->preViewOwnSuggestMaintenance = true;
        $this->preAddSuggestMaintenance = true;
        $this->preEditSuggestMaintenance = true;
        $this->preApproveSuggestMaintenance = true;
        $this->preDeleteSuggestMaintenance = true;
    }

    public function index()
    {
        if (!$this->preViewSuggestMaintenance && !$this->preViewOwnSuggestMaintenance) {
            access_denied();
        }
        $data['title'] = _l('dt_suggest_maintenance');
        $this->load->view('admin/suggest_maintenance/index', $data);
    }

    public function getSuggestMaintenances()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_maintenance.id as id',
            'tbl_suggest_maintenance.reference_no as reference_no',
            'tbl_suggest_maintenance.date as date',
            'tbl_type_maintenance.name as name_type_maintenance',
            'tbl_category_maintenance.name as name_category_maintenance',
            'tbldepartments.name as name_department',
            'tbl_machines.name as name_machines',
            'tbl_suggest_maintenance.detail as detail',
            'tbl_suggest_maintenance.status as status',
            'tbl_suggest_maintenance.status_finish as status_finish',
            'tbl_suggest_maintenance.created_by as created_by',
            'tbl_suggest_maintenance.downtime as downtime',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_maintenance';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_category_maintenance ON tbl_category_maintenance.id = tbl_suggest_maintenance.category_maintenance',
            'INNER JOIN tbl_type_maintenance ON tbl_type_maintenance.id = tbl_suggest_maintenance.type_maintenance',
            'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_suggest_maintenance.department_id',
            'INNER JOIN tbl_machines ON tbl_machines.id = tbl_suggest_maintenance.machines_id',
        ];

        if (!$this->preViewSuggestMaintenance) {
            array_push($where, 'AND tbl_suggest_maintenance.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_maintenance.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_maintenance.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_maintenance.date_status',
            'tbl_suggest_maintenance.staff_status',
            'tbl_suggest_maintenance.date_status_finish',
            'tbl_suggest_maintenance.staff_status_finish',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_maintenance/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_type_maintenance']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_category_maintenance']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_department']) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_machines'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['detail'] . '</div>';
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
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['downtime'] . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_maintenance/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestMaintenance ? '<a class="tnh-modal" href="' . base_url('admin/suggest_maintenance/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestMaintenance ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_maintenance/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $this->db->select('tbl_suggest_maintenance.*');
        $this->db->from('tbl_suggest_maintenance');
        $this->db->where('tbl_suggest_maintenance.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (empty($id)){
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_maintenance.reference_no]');
            } else {
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_maintenance.reference_no]');
                }
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
            $this->form_validation->set_rules('category_maintenance', lang("Nhóm bảo dưỡng"), 'required');
            $this->form_validation->set_rules('type_maintenance', lang("Loại bảo dưỡng"), 'required');
            $this->form_validation->set_rules('department_id', lang("Khu vực bảo dưỡng"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_maintenance');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $machines_id = $this->input->post('machines_id');
                    $category_maintenance = $this->input->post('category_maintenance');
                    $type_maintenance = $this->input->post('type_maintenance');
                    $department_id = ($this->input->post('department_id'));
                    $quantity = number_unformat($this->input->post('quantity'));
                    $detail = ($this->input->post('detail'));
                    $downtime = ($this->input->post('downtime'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $machines_maintenance_id = $this->input->post('machines_maintenance_id')[$value];
                            if (empty($machines_maintenance_id)){
                                continue;
                            }
                            $result_id = $this->input->post('result_id')[$value];
                            $standard = $this->input->post('standard')[$value];

                            $items[] = [
                                'machines_maintenance_id' => $machines_maintenance_id,
                                'result_id' => $result_id,
                                'standard' => $standard,
                            ];
                        }
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'machines_id' => $machines_id,
                        'category_maintenance' => $category_maintenance,
                        'type_maintenance' => $type_maintenance,
                        'department_id' => $department_id,
                        'detail' => $detail,
                        'quantity' => $quantity,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                        'downtime' => $downtime,
                    ];
                    $this->db->insert('tbl_suggest_maintenance',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_maintenance') == $reference_no) {
                            updateReference('suggest_maintenance');
                        }
                        if (!empty($items)){
                            foreach ($items as $key => $value){
                                $value['suggest_maintenance_id'] = $id;
                                $this->db->insert('tbl_suggest_maintenance_item',$value);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_maintenance',
                            'table_obj' => 'tbl_suggest_maintenance',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu bảo dưỡng') . ' [' . $reference_no . ']',
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
                    $branch_id = $this->input->post('branch_id');
                    $machines_id = $this->input->post('machines_id');
                    $category_maintenance = $this->input->post('category_maintenance');
                    $type_maintenance = $this->input->post('type_maintenance');
                    $department_id = ($this->input->post('department_id'));
                    $quantity = number_unformat($this->input->post('quantity'));
                    $detail = ($this->input->post('detail'));
                    $counter = $this->input->post('counter');
                    $downtime = $this->input->post('downtime');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $machines_maintenance_id = $this->input->post('machines_maintenance_id')[$value];
                            if (empty($machines_maintenance_id)){
                                continue;
                            }
                            $result_id = $this->input->post('result_id')[$value];
                            $standard = $this->input->post('standard')[$value];
                            $suggest_maintenance_item_id = !empty($this->input->post('suggest_maintenance_item_id')[$value]) ? $this->input->post('suggest_maintenance_item_id')[$value] : 0;

                            $items[] = [
                                'id' => $suggest_maintenance_item_id,
                                'machines_maintenance_id' => $machines_maintenance_id,
                                'result_id' => $result_id,
                                'standard' => $standard,
                            ];
                        }
                    }
                    $fields = [
                        'date' => $date,
                        'machines_id' => $machines_id,
                        'category_maintenance' => $category_maintenance,
                        'type_maintenance' => $type_maintenance,
                        'department_id' => $department_id,
                        'detail' => $detail,
                        'quantity' => $quantity,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'downtime' => $downtime,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_maintenance',$fields);
                    if ($success){
                        if (!empty($items)){
                            foreach ($items as $key => $value){
                                $this->db->where('id',$value['id']);
                                $this->db->update('tbl_suggest_maintenance_item',$value);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_maintenance',
                            'table_obj' => 'tbl_suggest_maintenance',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu bảo dưỡng') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestMaintenance){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_maintenance');
            } else {
                if (!$this->preEditSuggestMaintenance){
                    accessDenied(true);
                }
                $this->db->select('
                    tbl_suggest_maintenance_item.*,
                    tbl_machines_maintenance.name as name_machines_maintenance
                ');
                $this->db->from('tbl_suggest_maintenance_item');
                $this->db->join('tbl_machines_maintenance','tbl_machines_maintenance.id = tbl_suggest_maintenance_item.machines_maintenance_id');
                $this->db->where('tbl_suggest_maintenance_item.suggest_maintenance_id',$id);
                $dtItems = $this->db->get()->result_array();
                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_maintenance');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_maintenance');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtCategoryMaintenance'] = get_table_where('tbl_category_maintenance');
        $data['dtTypeMaintenance'] = get_table_where('tbl_type_maintenance');
        $data['dtDepartment'] = get_table_where('tbldepartments');
        $this->load->view('admin/suggest_maintenance/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_maintenance');

        $this->db->select('
            tbl_suggest_maintenance.*,
            tbl_machines.name as name_machines,
            tbl_category_maintenance.name as name_category_maintenance,
            tbl_type_maintenance.name as name_type_maintenance,
            tbldepartments.name as name_department,
        ');
        $this->db->from('tbl_suggest_maintenance');
        $this->db->join('tbl_machines','tbl_machines.id = tbl_suggest_maintenance.machines_id','inner');
        $this->db->join('tbl_category_maintenance','tbl_category_maintenance.id = tbl_suggest_maintenance.category_maintenance','left');
        $this->db->join('tbl_type_maintenance','tbl_type_maintenance.id = tbl_suggest_maintenance.type_maintenance','left');
        $this->db->join('tbldepartments','tbldepartments.departmentid = tbl_suggest_maintenance.department_id','left');
        $this->db->where('tbl_suggest_maintenance.id',$id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_maintenance_item.*,
            tbl_machines_maintenance.name as name_machines_maintenance,
            tbl_result.name as name_result,
        ');
        $this->db->from('tbl_suggest_maintenance_item');
        $this->db->join('tbl_machines_maintenance','tbl_machines_maintenance.id = tbl_suggest_maintenance_item.machines_maintenance_id');
        $this->db->join('tbl_result','tbl_result.id = tbl_suggest_maintenance_item.result_id');
        $this->db->where('tbl_suggest_maintenance_item.suggest_maintenance_id',$id);
        $dtItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtItems'] = $dtItems;
        $this->load->view('admin/suggest_maintenance/view',$data);
    }

    public function agree()
    {
        if (!$this->preApproveSuggestMaintenance) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_maintenance.*');
        $this->db->from('tbl_suggest_maintenance');
        $this->db->where('tbl_suggest_maintenance.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 0) {
                if ($dtData['status_finish'] == 1) {
                    $data['result'] = 0;
                    $data['message'] = lang('Phiếu đã hoàn thành không thể hủy duyệt !');
                    echo json_encode($data);
                    die();
                }
            }

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
            $up = $this->db->update('tbl_suggest_maintenance', $options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_maintenance',
                    'table_obj' => 'tbl_suggest_maintenance',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu bảo dưỡng') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preApproveSuggestMaintenance) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_maintenance.*');
        $this->db->from('tbl_suggest_maintenance');
        $this->db->where('tbl_suggest_maintenance.id',$suggest_id);
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
            $up = $this->db->update('tbl_suggest_maintenance',$options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_maintenance',
                    'table_obj' => 'tbl_suggest_maintenance',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt hoàn thành phiếu yêu cầu bảo dưỡng') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteSuggestMaintenance){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_maintenance.*');
        $this->db->from('tbl_suggest_maintenance');
        $this->db->where('tbl_suggest_maintenance.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_maintenance');
        if ($success){

            $this->db->where('tbl_suggest_maintenance_item.suggest_maintenance_id',$id);
            $this->db->delete('tbl_suggest_maintenance_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_maintenance',
                'table_obj' => 'tbl_suggest_maintenance',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu bảo dưỡng') . ' [' . $dtData['reference_no'] . ']',
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

    public function getMaintenaceMachines(){
        $data = [];
        $machines_id = $this->input->post('machines_id');
        $this->db->select('tbl_machines_maintenance.*');
        $this->db->from('tbl_machines_maintenance');
        $this->db->where('tbl_machines_maintenance.machines_id',$machines_id);
        $dtData = $this->db->get()->result_array();
        $data['dtMaintenaceMachines'] = $dtData;
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
               tbl_suggest_maintenance.id as id,
               tbl_suggest_maintenance.reference_no as reference_no,
               tbl_suggest_maintenance.date as date,
               tbl_type_maintenance.name as name_type_maintenance,
               tbl_category_maintenance.name as name_category_maintenance,
               tbl_machines_maintenance.name as name_machines_maintenance,
               tbldepartments.name as name_department,
               tbl_suggest_maintenance.detail as detail,
               tbl_suggest_maintenance.quantity as quantity,
               tbl_machines.code as code_machines,
               tbl_machines.name as name_machines,
               tblbranch.name as name_branch,
               tbl_result.name as name_result,
               (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_suggest_maintenance_item.id AND tblproduction_report.object_type = "suggest_maintenance"
               ) as name_report,
                tbl_suggest_maintenance_item.standard as standard
            ');
            $this->db->from('tbl_suggest_maintenance');
            $this->db->join('tblbranch','tblbranch.id = tbl_suggest_maintenance.branch_id','inner');
            $this->db->join('tbl_machines','tbl_machines.id = tbl_suggest_maintenance.machines_id','inner');
            $this->db->join('tbl_suggest_maintenance_item','tbl_suggest_maintenance_item.suggest_maintenance_id = tbl_suggest_maintenance.id','left');
            $this->db->join('tbl_type_maintenance','tbl_type_maintenance.id = tbl_suggest_maintenance.type_maintenance','left');
            $this->db->join('tbl_category_maintenance','tbl_category_maintenance.id = tbl_suggest_maintenance.category_maintenance','left');
            $this->db->join('tbl_machines_maintenance','tbl_machines_maintenance.id = tbl_suggest_maintenance_item.machines_maintenance_id','left');
            $this->db->join('tbldepartments','tbldepartments.departmentid = tbl_suggest_maintenance.department_id','left');
            $this->db->join('tbl_result','tbl_result.id = tbl_suggest_maintenance_item.result_id','left');


            if (!$this->preViewSuggestMaintenance) {
                $this->db->where('tbl_suggest_maintenance.created_by = '.get_staff_user_id().'');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_maintenance.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_maintenance.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_maintenance.id desc');
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
                ('PHIẾU YÊU CẦU BẢO DƯỠNG'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Bảo Dưỡng');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Loại Bảo Dưỡng');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Nhóm Bảo Dưỡng');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Bộ Phận Thiết Bị')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Khu Vực Bảo Dưỡng')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Chi tiết Bảo Dưỡng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Số Lượng')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Nhóm Thiết Bị')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Mã Thiết Bị')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Tên Thiết Bị')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Chi Nhánh')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Quy Trình')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Kết Quả')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Báo Cáo Sự Cố')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Tiêu Chuẩn/ Quy Định')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:R$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['name_type_maintenance'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['name_category_maintenance']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['name_machines_maintenance'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['name_department'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['detail'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['quantity'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", '')->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['code_machines'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['name_machines'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['name_branch'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", '')->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['name_result'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['name_report'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['standard'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])){
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_maintenance||'.$value['id'];
                        $this->db->where('id',$value['id']);
                        $this->db->update('tbl_suggest_maintenance',['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_maintenance/';
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
                        $objDrawing1->setCoordinates('R' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", '')->getStyle("R$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:R$rowBegin")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_bao_duong') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
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