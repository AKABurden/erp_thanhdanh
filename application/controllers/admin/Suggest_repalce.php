<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_repalce extends AdminController
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

        $this->preViewSuggestReplace = true;
        $this->preViewOwnSuggestReplace = true;
        $this->preAddSuggestReplace = true;
        $this->preEditSuggestReplace = true;
        $this->preApproveSuggestReplace = true;
        $this->preDeleteSuggestReplace = true;
    }

    public function index(){
        if (!$this->preViewSuggestReplace && !$this->preViewOwnSuggestReplace){
            access_denied();
        }
        $data['title'] = _l('dt_suggest_replace');
        $this->load->view('admin/suggest_repalce/index', $data);
    }

    public function getSuggestReplaces(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_repalce.id as id',
            'tbl_suggest_repalce.reference_no as reference_no',
            'tbl_suggest_repalce.date as date',
            'tbl_machines.name as name_machines',
            'tbl_suggest_repalce.finish_payment as finish_payment',
            'tbl_suggest_repalce.status as status',
            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.reference_no,"__",tblproduction_report.id) SEPARATOR "||")
             FROM tblproduction_report
             WHERE tblproduction_report.object_id = tbl_suggest_repalce.id AND tblproduction_report.object_type = "suggest_repalce"
            ) as name_report',
            'tbl_suggest_repalce.staff_suggest as staff_suggest',
            'tbl_suggest_repalce.staff_agree as staff_agree',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_repalce';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_machines ON tbl_machines.id = tbl_suggest_repalce.machines_id',
        ];

        if (!$this->preViewSuggestReplace) {
            array_push($where,'AND tbl_suggest_repalce.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            array_push($where, "AND tbl_suggest_repalce.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_repalce.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_repalce.date_status',
            'tbl_suggest_repalce.staff_status'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_repalce/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px"></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['name_machines']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['finish_payment']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 1)\' id=\'agree\' suggest_repalce_id=\''.$aRow['id'].'\' value=\'1\' class=\'btn btn-success\'>'.lang('tnh_agree').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-danger po">'.lang('Chưa duyệt').'</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 0)\' id=\'agree\' suggest_repalce_id=\''.$aRow['id'].'\' value=\'0\' class=\'btn btn-danger\'>'.lang('Hủy duyệt').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-success po">'.lang('Đã duyệt').'</span></div>';
                $_data.= '<div style="margin-top: 5px"> Người duyệt: '.get_staff_full_name($aRow['staff_status']).'</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left" style="width: 100px">'.$_data.'</div>';


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
                $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=suggest_repalce') . '" class="btn btn-info">Tạo phiếu báo cáo không phù hợp</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>
            ';
            } else {
                $row[] = '';
            }

            
            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['staff_suggest']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['staff_agree']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_repalce/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestReplace ? '<a href="' . base_url('admin/suggest_repalce/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestReplace ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_repalce/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        if ($this->input->post()){
            if (empty($id)){
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_repalce.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
                $this->form_validation->set_rules('staff_suggest', lang("Người đề xuất"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_replace');
                    $date = to_sql_date($this->input->post('date'), true);
                    $machines_id = $this->input->post('machines_id');
                    $branch_id = $this->input->post('branch_id');
                    $staff_suggest = !empty($this->input->post('staff_suggest')) ? $this->input->post('staff_suggest') : 0;
                    $staff_agree = !empty($this->input->post('staff_agree')) ? $this->input->post('staff_agree') : 0;
                    $production_report_id = !empty($this->input->post('production_report_id')) ? $this->input->post('production_report_id') : 0;
                    $report_acceptance_id = !empty($this->input->post('report_acceptance_id')) ? $this->input->post('report_acceptance_id') : 0;
                    $finish_payment = !empty($this->input->post('finish_payment')) ? $this->input->post('finish_payment') : null;
                    $note = ($this->input->post('note'));
                    $total_quantity = 0;
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $items_id = $this->input->post('item_id')[$value];
                            $arr_item = explode('__', $items_id);
                            $type_item = $arr_item[0];
                            $item_id = $arr_item[1];
                            if ($type_item == "products" || $type_item == 'semi_products' || $type_item == 'semi_products_outside') {
                                $info_item = $this->products_model->rowProduct($item_id);
                            } else if ($type_item == "materials") {
                                $info_item = $this->items_model->rowMaterial($item_id);
                            } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                                $info_item = $this->tools_supplies_model->rowToolsSupplies($item_id);
                            }
                            if (empty($info_item)) continue;

                            $suppliers_id = !empty($this->input->post('suppliers_id')[$value]) ? $this->input->post('suppliers_id')[$value] : 0;
                            $result = ($this->input->post('result')[$value]);
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $standard = ($this->input->post('standard')[$value]);
                            $items[] = [
                                'type_item' => $type_item,
                                'item_id' => $item_id,
                                'quantity' => $quantity,
                                'suppliers_id' => $suppliers_id,
                                'result' => $result,
                                'standard' => $standard,
                            ];

                            $total_quantity += $quantity;
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
                        'machines_id' => $machines_id,
                        'production_report_id' => $production_report_id,
                        'report_acceptance_id' => $report_acceptance_id,
                        'total_quantity' => $total_quantity,
                        'finish_payment' => $finish_payment,
                        'staff_suggest' => $staff_suggest,
                        'staff_agree' => $staff_agree,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_repalce',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_replace') == $reference_no) {
                            updateReference('suggest_replace');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_replace_id'] = $id;
                            $this->db->insert('tbl_suggest_replace_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_replace',
                            'table_obj' => 'tbl_suggest_repalce',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu thay thế vật tư') . ' [' . $reference_no . ']',
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
                $this->db->select('tbl_suggest_repalce.*');
                $this->db->from('tbl_suggest_repalce');
                $this->db->where('tbl_suggest_repalce.id',$id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_repalce.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
                $this->form_validation->set_rules('staff_suggest', lang("Người đề xuất"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $machines_id = $this->input->post('machines_id');
                    $branch_id = $this->input->post('branch_id');
                    $staff_suggest = !empty($this->input->post('staff_suggest')) ? $this->input->post('staff_suggest') : 0;
                    $staff_agree = !empty($this->input->post('staff_agree')) ? $this->input->post('staff_agree') : 0;
                    $production_report_id = !empty($this->input->post('production_report_id')) ? $this->input->post('production_report_id') : 0;
                    $report_acceptance_id = !empty($this->input->post('report_acceptance_id')) ? $this->input->post('report_acceptance_id') : 0;
                    $finish_payment = !empty($this->input->post('finish_payment')) ? $this->input->post('finish_payment') : null;
                    $note = ($this->input->post('note'));
                    $total_quantity = 0;
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $items_id = $this->input->post('item_id')[$value];
                            $arr_item = explode('__', $items_id);
                            $type_item = $arr_item[0];
                            $item_id = $arr_item[1];
                            if ($type_item == "products" || $type_item == 'semi_products' || $type_item == 'semi_products_outside') {
                                $info_item = $this->products_model->rowProduct($item_id);
                            } else if ($type_item == "materials") {
                                $info_item = $this->items_model->rowMaterial($item_id);
                            } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                                $info_item = $this->tools_supplies_model->rowToolsSupplies($item_id);
                            }
                            if (empty($info_item)) continue;

                            $suggest_replace_item_id = !empty($this->input->post('suggest_replace_item_id')[$value]) ? $this->input->post('suggest_replace_item_id')[$value] : 0;
                            $suppliers_id = !empty($this->input->post('suppliers_id')[$value]) ? $this->input->post('suppliers_id')[$value] : 0;
                            $result = ($this->input->post('result')[$value]);
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $standard = ($this->input->post('standard')[$value]);
                            $items[] = [
                                'id' => $suggest_replace_item_id,
                                'type_item' => $type_item,
                                'item_id' => $item_id,
                                'quantity' => $quantity,
                                'suppliers_id' => $suppliers_id,
                                'result' => $result,
                                'standard' => $standard,
                            ];

                            $total_quantity += $quantity;
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
                        'machines_id' => $machines_id,
                        'production_report_id' => $production_report_id,
                        'report_acceptance_id' => $report_acceptance_id,
                        'total_quantity' => $total_quantity,
                        'finish_payment' => $finish_payment,
                        'staff_suggest' => $staff_suggest,
                        'staff_agree' => $staff_agree,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_repalce',$fields);
                    if ($success){
                        $this->db->where('suggest_replace_id',$id);
                        $this->db->delete('tbl_suggest_replace_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_replace_id'] = $id;
                            $this->db->insert('tbl_suggest_replace_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_replace',
                            'table_obj' => 'tbl_suggest_repalce',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu vật tư thay thế') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestReplace){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_replace');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_repalce'), 'page' => lang('dt_suggest_replace')), array('link' => '#', 'page' => lang('dt_add_suggest_replace'))];
            } else {
                if (!$this->preEditSuggestReplace){
                    accessDenied(true);
                }
                $this->db->select('tbl_suggest_repalce.*');
                $this->db->from('tbl_suggest_repalce');
                $this->db->where('tbl_suggest_repalce.id',$id);
                $dtData = $this->db->get()->row_array();

                if ($dtData['status'] == 1){
                    set_alert('danger',  'Phiếu đã duyệt không thể sửa !');
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $this->db->select('tbl_suggest_replace_item.*');
                $this->db->from('tbl_suggest_replace_item');
                $this->db->where('tbl_suggest_replace_item.suggest_replace_id',$id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_replace');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_repalce'), 'page' => lang('dt_suggest_replace')), array('link' => '#', 'page' => lang('dt_edit_suggest_replace'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_replace');
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/suggest_repalce/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_replace');

        $this->db->select('tbl_suggest_repalce.*');
        $this->db->from('tbl_suggest_repalce');
        $this->db->where('tbl_suggest_repalce.id',$id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_replace_item.*,
            tbl_result.name as name_result,
            tblsuppliers.company as company,
        ');
        $this->db->from('tbl_suggest_replace_item');
        $this->db->join('tblsuppliers','tblsuppliers.id = tbl_suggest_replace_item.suppliers_id','left');
        $this->db->join('tbl_result','tbl_result.id = tbl_suggest_replace_item.result','left');
        $this->db->where('tbl_suggest_replace_item.suggest_replace_id',$id);
        $dtDataItems = $this->db->get()->result_array();
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/suggest_repalce/view',$data);
    }

    public function agree(){
        if (!$this->preApproveSuggestReplace) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_repalce_id = $this->input->post('suggest_repalce_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_repalce.*');
        $this->db->from('tbl_suggest_repalce');
        $this->db->where('tbl_suggest_repalce.id',$suggest_repalce_id);
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

            $this->db->where('id',$suggest_repalce_id);
            $up = $this->db->update('tbl_suggest_repalce',$options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_repalce',
                    'table_obj' => 'tbl_suggest_repalce',
                    'id_obj' => $suggest_repalce_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu vật tư thay thế') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteSuggestReplace){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_repalce.*');
        $this->db->from('tbl_suggest_repalce');
        $this->db->where('tbl_suggest_repalce.id',$id);
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
        $success = $this->db->delete('tbl_suggest_repalce');
        if ($success){
            $this->db->where('tbl_suggest_replace_item.suggest_replace_id',$id);
            $this->db->delete('tbl_suggest_replace_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_repalce',
                'table_obj' => 'tbl_suggest_repalce',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu vật tư thay thế') . ' [' . $dtData['reference_no'] . ']',
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
            tbl_machines.name as text,
            tbl_machines.code as code,
            tbl_machines.name as name,
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
            CONCAT(tblproduction_report.name_report,"(",coalesce(tblproduction_report.reference_no,""),")") as text
        ', false);
        $this->db->from('tblproduction_report');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblproduction_report.name_report', $term);
            $this->db->or_like('tblproduction_report.reference_no', $term);
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

    public function searchDeliveryRecords($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_delivery_records.id as id, 
            tbl_delivery_records.reference_no as text
        ', false);
        $this->db->from('tbl_delivery_records');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_delivery_records.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Biên bản nghiệm thu'), 'children' => $pod];
        if (!empty($id)){
            $dtMachines = get_table_where('tbl_delivery_records',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['reference_no']];
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
                tbl_suggest_repalce.id as id,
                tbl_suggest_repalce.reference_no as reference_no,
                tbl_suggest_repalce.date as date,
                tbl_machines.code as code_machines,
                tbl_machines.name as name_machines,
                (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                     FROM tblproduction_report
                     WHERE tblproduction_report.object_id = tbl_suggest_replace_item.id AND tblproduction_report.object_type = "suggest_repalce_item"
                ) as name_report,
                tblsuppliers.company as company,
                tbl_result.name as name_result,
                tbl_suggest_replace_item.quantity as quantity,
                tbl_suggest_replace_item.item_id as item_id,
                tbl_suggest_replace_item.type_item as type_item,
                tbl_suggest_replace_item.standard as standard,
                tbl_suggest_repalce.finish_payment as finish_payment,
                tbl_suggest_repalce.staff_suggest as staff_suggest,
                tbl_suggest_repalce.staff_agree as staff_agree,
            ');
            $this->db->from('tbl_suggest_repalce');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_repalce.machines_id', 'inner');
            $this->db->join('tbl_suggest_replace_item','tbl_suggest_replace_item.suggest_replace_id = tbl_suggest_repalce.id');
            $this->db->join('tblsuppliers','tblsuppliers.id = tbl_suggest_replace_item.suppliers_id');
            $this->db->join('tbl_result','tbl_result.id = tbl_suggest_replace_item.result');

            if (!$this->preViewSuggestReplace) {
                $this->db->where('tbl_suggest_repalce.created_by = '.get_staff_user_id().'');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_repalce.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_repalce.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_repalce.id desc');
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
                ('PHIẾU YÊU CẦU VẬT TƯ THAY THẾ'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:Q1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Nhóm Thiết Bị');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mã Thiết Bị');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Tên Thiết Bị');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Nhóm Vật Tư Thay Thế')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Tên Vật Tư Thay Thế')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Tên NCC')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Số Lượng')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Kết Quả');
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Báo Cáo Không Phù Hợp')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Hoàn Thành Thanh Toán')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Người Đề Xuất')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Người Duyệt')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Tiêu Chuẩn/ Quy Định')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:Q$sttRow")->applyFromArray([
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
                    $rowBegin++;
                    $item_id = $value['item_id'];
                    $type_item = $value['type_item'];
                    $info = null;
                    if ($type_item == "products" || $type_item == 'semi_products' || $type_item == 'semi_products_outside') {
                        $info = $this->products_model->rowProduct($item_id);
                        $dtCategory = get_table_where('tbl_category_products',['id' => $info['category_id']],'','row_array');
                    } else if ($type_item == "materials") {
                        $info = $this->items_model->rowMaterial($item_id);
                        $dtCategory = get_table_where('tbl_category_items',['id' => $info['category_id']],'','row_array');
                    } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                        $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                        $dtCategory = get_table_where('tbl_category_tools_supplies',['id' => $info['category_id']],'','row_array');
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", '');
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['code_machines']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['name_machines']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $dtCategory['name'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$info['name'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['company'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['quantity']);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", ($value['name_result']));
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", ($value['name_report']))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin",($value['finish_payment']))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", get_staff_full_name($value['staff_suggest']))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", get_staff_full_name($value['staff_agree']))->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['standard'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", '');

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:Q$rowBegin")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->getStyle("J$rowBegin:K$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_vat_tu_thay_the') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(30);
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
        $success = $this->db->update('tbl_suggest_replace_item',[
            'result' => $result_id
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