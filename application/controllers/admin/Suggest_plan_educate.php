<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_plan_educate extends AdminController
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

        $this->preViewSuggestPlanEducation = true;
        $this->preViewOwnSuggestPlanEducation = true;
        $this->preAddSuggestPlanEducation = true;
        $this->preEditSuggestPlanEducation = true;
        $this->preApproveSuggestPlanEducation= true;
        $this->preDeleteSuggestPlanEducation = true;
    }

    public function index(){
        if (!$this->preViewSuggestPlanEducation && !$this->preViewOwnSuggestPlanEducation){
            access_denied();
        }
        $data['title'] = _l('dt_suggest_plan_educate');
        $this->load->view('admin/suggest_plan_educate/index', $data);
    }

    public function getSuggestPlanEducates(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_plan_educate.id as id',
            'tbl_suggest_plan_educate.reference_no as reference_no',
            'tbl_suggest_plan_educate.date as date',
            'tbl_suggest_plan_educate.staff_id as staff_id',
            'tbl_category_plan_time.name as name_category_plan',
            'tbl_suggest_plan_educate.staff_evaluate as staff_evaluate',
            'tbl_suggest_plan_educate.status as status',
            'tbl_suggest_plan_educate.total_tax as total_tax',
            'tbl_suggest_plan_educate.grand_total as grand_total',
            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
             FROM tblproduction_report
             WHERE tblproduction_report.object_id = tbl_suggest_plan_educate.id AND tblproduction_report.object_type = "suggest_plan_educate"
            ) as name_report',
            'tbl_suggest_plan_educate.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_plan_educate';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_category_plan_time ON tbl_category_plan_time.id = tbl_suggest_plan_educate.category_plan',
        ];

        if (!$this->preViewSuggestPlanEducation) {
            array_push($where,'AND (tbl_suggest_plan_educate.created_by = '.get_staff_user_id().' OR tbl_suggest_plan_educate.staff_id = '.get_staff_user_id().' )');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            array_push($where, "AND tbl_suggest_plan_educate.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_plan_educate.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_plan_educate.date_status',
            'tbl_suggest_plan_educate.staff_status'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_plan_educate/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">'.get_staff_full_name($aRow['staff_id']).'</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['name_category_plan']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['staff_evaluate']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 1)\' id=\'agree\' suggest_repalce_id=\''.$aRow['id'].'\' value=\'1\' class=\'btn btn-success\'>'.lang('tnh_agree').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-danger po">'.lang('Chưa duyệt').'</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 0)\' id=\'agree\' suggest_repalce_id=\''.$aRow['id'].'\' value=\'0\' class=\'btn btn-danger\'>'.lang('Hủy duyệt').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-success po">'.lang('Đã duyệt').'</span></div>';
                $_data.= '<div style="margin-top: 5px"> Người duyệt: '.get_staff_full_name($aRow['staff_status']).'</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left" style="width: 100px">'.$_data.'</div>';
            $row[] = '<div class="text-right" style="width: 100px">'.formatMoney($aRow['grand_total'] - $aRow['total_tax']).'</div>';
            $row[] = '<div class="text-right" style="width: 100px">'.formatMoney($aRow['total_tax']).'</div>';
            $row[] = '<div class="text-right" style="width: 100px">'.formatMoney($aRow['grand_total']).'</div>';
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
            if ($aRow['status'] == 1) {
                $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=suggest_plan_educate') . '" class="btn btn-info">Báo cáo không phù hợp</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>
            ';
            } else {
                $row[] = '';
            }
            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_plan_educate/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestPlanEducation ? '<a href="' . base_url('admin/suggest_plan_educate/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestPlanEducation ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_plan_educate/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $this->db->select('tbl_suggest_plan_educate.*');
        $this->db->from('tbl_suggest_plan_educate');
        $this->db->where('tbl_suggest_plan_educate.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_plan_educate.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('staff_id', lang("Người lập kế thời"), 'required');
            $this->form_validation->set_rules('category_plan', lang("Nhóm kế hoạch"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('staff_evaluate', lang("Người đánh giá"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_plan_educate');
                    $date = to_sql_date($this->input->post('date'), true);
                    $time_finish = !empty($this->input->post('time_finish')) ? to_sql_date($this->input->post('time_finish'), true) : null;
                    $staff_id = $this->input->post('staff_id');
                    $branch_id = $this->input->post('branch_id');
                    $category_plan = !empty($this->input->post('category_plan')) ? $this->input->post('category_plan') : 0;
                    $staff_evaluate = !empty($this->input->post('staff_evaluate')) ? $this->input->post('staff_evaluate') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    $totalTax = 0;
                    $grand_total = 0;
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $evaluate_id = $this->input->post('evaluate_id')[$value];
                            if (empty($evaluate_id)){
                                continue;
                            }

                            $result_id = ($this->input->post('result_id')[$value]);
                            $position_educate = ($this->input->post('position_educate')[$value]);
                            $detail = ($this->input->post('detail')[$value]);
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $staff_educate = ($this->input->post('staff_educate')[$value]);
                            $unit_educate = ($this->input->post('unit_educate')[$value]);
                            $cost_money = number_unformat($this->input->post('cost_money')[$value]);
                            $tax_id = ($this->input->post('tax_id')[$value]);
                            $standard = ($this->input->post('standard')[$value]);

                            $tax_rate_item = 0;
                            if (!empty($tax_id)) {
                                $info_tax = $this->site_model->rowTax($tax_id);
                                if (!empty($info_tax)) {
                                    $tax_rate_item = $info_tax['taxrate'];
                                }
                            }
                            $total_tax = ($cost_money * $tax_rate_item) / 100;
                            $total = $total_tax + $cost_money;
                            $items[] = [
                                'evaluate_id' => $evaluate_id,
                                'position_educate' => $position_educate,
                                'detail' => $detail,
                                'staff_educate' => $staff_educate,
                                'unit_educate' => $unit_educate,
                                'cost_money' => $cost_money,
                                'quantity' => $quantity,
                                'tax_id' => $tax_id,
                                'total_tax' => $total_tax,
                                'total' => $total,
                                'result_id' => $result_id,
                                'standard' => $standard,
                            ];
                            $totalTax += $total_tax;
                            $grand_total += $total;

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
                        'staff_id' => $staff_id,
                        'category_plan' => $category_plan,
                        'staff_evaluate' => $staff_evaluate,
                        'note' => $note,
                        'grand_total' => $grand_total,
                        'total_tax' => $totalTax,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_plan_educate',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_plan_educate') == $reference_no) {
                            updateReference('suggest_plan_educate');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_plan_educate_id'] = $id;
                            $this->db->insert('tbl_suggest_plan_educate_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_plan_educate',
                            'table_obj' => 'tbl_suggest_plan_educate',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu kế hoạch đào tạo') . ' [' . $reference_no . ']',
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
                    $time_finish = !empty($this->input->post('time_finish')) ? to_sql_date($this->input->post('time_finish'), true) : null;
                    $staff_id = $this->input->post('staff_id');
                    $branch_id = $this->input->post('branch_id');
                    $category_plan = !empty($this->input->post('category_plan')) ? $this->input->post('category_plan') : 0;
                    $staff_evaluate = !empty($this->input->post('staff_evaluate')) ? $this->input->post('staff_evaluate') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    $totalTax = 0;
                    $grand_total = 0;
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $evaluate_id = $this->input->post('evaluate_id')[$value];
                            if (empty($evaluate_id)){
                                continue;
                            }

                            $suggest_plan_educate_item_id = !empty($this->input->post('suggest_plan_educate_item_id')[$value]) ? $this->input->post('suggest_plan_educate_item_id')[$value] : 0;
                            $result_id = ($this->input->post('result_id')[$value]);
                            $position_educate = ($this->input->post('position_educate')[$value]);
                            $detail = ($this->input->post('detail')[$value]);
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $staff_educate = ($this->input->post('staff_educate')[$value]);
                            $unit_educate = ($this->input->post('unit_educate')[$value]);
                            $cost_money = number_unformat($this->input->post('cost_money')[$value]);
                            $tax_id = ($this->input->post('tax_id')[$value]);
                            $standard = ($this->input->post('standard')[$value]);

                            $tax_rate_item = 0;
                            if (!empty($tax_id)) {
                                $info_tax = $this->site_model->rowTax($tax_id);
                                if (!empty($info_tax)) {
                                    $tax_rate_item = $info_tax['taxrate'];
                                }
                            }
                            $total_tax = ($cost_money * $tax_rate_item) / 100;
                            $total = $total_tax + $cost_money;
                            $items[] = [
                                'id' => $suggest_plan_educate_item_id,
                                'evaluate_id' => $evaluate_id,
                                'position_educate' => $position_educate,
                                'detail' => $detail,
                                'staff_educate' => $staff_educate,
                                'unit_educate' => $unit_educate,
                                'cost_money' => $cost_money,
                                'quantity' => $quantity,
                                'tax_id' => $tax_id,
                                'total_tax' => $total_tax,
                                'total' => $total,
                                'result_id' => $result_id,
                                'standard' => $standard,
                            ];
                            $totalTax += $total_tax;
                            $grand_total += $total;
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
                        'staff_id' => $staff_id,
                        'category_plan' => $category_plan,
                        'staff_evaluate' => $staff_evaluate,
                        'grand_total' => $grand_total,
                        'total_tax' => $totalTax,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_plan_educate',$fields);
                    if ($success){
                        $this->db->where('suggest_plan_educate_id',$id);
                        $this->db->delete('tbl_suggest_plan_educate_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_plan_educate_id'] = $id;
                            $this->db->insert('tbl_suggest_plan_educate_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_plan_educate',
                            'table_obj' => 'tbl_suggest_plan_educate',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu kế hoạch đào tạo') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestPlanEducation){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_plan_educate');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_educate'), 'page' => lang('dt_suggest_plan_educate')), array('link' => '#', 'page' => lang('dt_add_suggest_plan_educate'))];
            } else {
                if (!$this->preEditSuggestPlanEducation){
                    accessDenied(true);
                }

                if ($dtData['status'] == 1){
                    set_alert('danger',  'Phiếu đã duyệt không thể sửa !');
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $this->db->select('tbl_suggest_plan_educate_item.*');
                $this->db->from('tbl_suggest_plan_educate_item');
                $this->db->where('tbl_suggest_plan_educate_item.suggest_plan_educate_id',$id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_plan_educate');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_educate'), 'page' => lang('dt_suggest_plan_educate')), array('link' => '#', 'page' => lang('dt_edit_suggest_plan_educate'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_plan_educate');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtCategoryPlanTime'] = get_table_where('tbl_category_plan_time');
        $data['taxs'] = $this->site_model->getTaxs();
        $this->load->view('admin/suggest_plan_educate/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_plan_educate');

        $this->db->select('tbl_suggest_plan_educate.*');
        $this->db->from('tbl_suggest_plan_educate');
        $this->db->where('tbl_suggest_plan_educate.id',$id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_plan_educate_item.*,
            tbl_result.name as name_result,
            tbl_evaluate.code_evaluate as code_evaluate,
            tbl_evaluate.name_evaluate as name_evaluate,
            tbltaxes.name as name_tax
        ');
        $this->db->from('tbl_suggest_plan_educate_item');
        $this->db->join('tbl_result','tbl_result.id = tbl_suggest_plan_educate_item.result_id','left');
        $this->db->join('tbl_evaluate','tbl_evaluate.id = tbl_suggest_plan_educate_item.evaluate_id','left');
        $this->db->join('tbltaxes','tbltaxes.id = tbl_suggest_plan_educate_item.tax_id','left');
        $this->db->where('tbl_suggest_plan_educate_item.suggest_plan_educate_id',$id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/suggest_plan_educate/view',$data);
    }

    public function agree(){
        if (!$this->preApproveSuggestPlanEducation) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_plan_educate.*');
        $this->db->from('tbl_suggest_plan_educate');
        $this->db->where('tbl_suggest_plan_educate.id',$suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 0) {
                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'suggest_plan_educate');
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
            $up = $this->db->update('tbl_suggest_plan_educate',$options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_plan_educate',
                    'table_obj' => 'tbl_suggest_plan_educate',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu kế hoạch đào tạo') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteSuggestPlanEducation){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_plan_educate.*');
        $this->db->from('tbl_suggest_plan_educate');
        $this->db->where('tbl_suggest_plan_educate.id',$id);
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
        $success = $this->db->delete('tbl_suggest_plan_educate');
        if ($success){
            $this->db->where('tbl_suggest_plan_educate_item.suggest_plan_educate_id',$id);
            $this->db->delete('tbl_suggest_plan_educate_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_plan_educate',
                'table_obj' => 'tbl_suggest_plan_educate',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu kế hoạch đào tạo') . ' [' . $dtData['reference_no'] . ']',
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
                tbl_suggest_plan_educate.id as id,
                tbl_suggest_plan_educate.reference_no as reference_no,
                tbl_suggest_plan_educate.date as date,
                tbl_suggest_plan_educate.staff_id as staff_id,
                tbl_category_plan_time.name as name_category_plan,
                tbl_evaluate.name_evaluate as name_evaluate,
                tbl_evaluate.content_evaluate as content_evaluate,
                tbl_suggest_plan_educate_item.position_educate as position_educate,
                tbl_type_evaluate.name as name_type_evaluate,
                tbl_suggest_plan_educate_item.detail as detail,
                tbl_suggest_plan_educate_item.quantity as quantity,
                tbl_suggest_plan_educate_item.staff_educate as staff_educate,
                tbl_suggest_plan_educate_item.unit_educate as unit_educate,
                tbl_result.name as name_result,
                tbl_suggest_plan_educate.staff_evaluate as staff_evaluate,
                (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_suggest_plan_educate.id AND tblproduction_report.object_type = "suggest_plan_educate"
                ) as name_report,
                tbl_suggest_plan_educate_item.cost_money as cost_money,
                tbltaxes.name as name_tax,
                tbl_suggest_plan_educate_item.total as total,
                tbl_suggest_plan_educate_item.standard as standard,
            ');
            $this->db->from('tbl_suggest_plan_educate');
            $this->db->join('tbl_category_plan_time', 'tbl_category_plan_time.id = tbl_suggest_plan_educate.category_plan', 'left');
            $this->db->join('tbl_suggest_plan_educate_item','tbl_suggest_plan_educate_item.suggest_plan_educate_id = tbl_suggest_plan_educate.id');
            $this->db->join('tbl_evaluate','tbl_evaluate.id = tbl_suggest_plan_educate_item.evaluate_id');
            $this->db->join('tbl_type_evaluate','tbl_type_evaluate.id = tbl_evaluate.type_evaluate_id','left');
            $this->db->join('tbltaxes','tbltaxes.id = tbl_suggest_plan_educate_item.tax_id','left');
            $this->db->join('tbl_result','tbl_result.id = tbl_suggest_plan_educate_item.result_id');

            if (!$this->preViewSuggestPlanEducation) {
                $this->db->where('(tbl_suggest_plan_educate.created_by = '.get_staff_user_id().' OR tbl_suggest_plan_educate.staff_id = '.get_staff_user_id().' )');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_plan_educate.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_plan_educate.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_plan_educate.id desc');
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
                ('PHIẾU YÊU CẦU KẾ HOẠCH ĐÀO TẠO'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:U1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Người Lập Kế Thời');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Nhóm Kế Hoạch');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Tên Đào Tạo');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Lý Do Đào Tạo')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Vị Trí Đào Tạo')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Loại Đào Tạo')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Chi Tiết Đánh Giá')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Số Lượng Người Tham Gia')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Người Phụ Trách Đào Tạo')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Đơn Vị Đào Tạo')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Kết Quả')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Người Đánh Giá')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Báo Cáo Không Phù Hợp')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Chi Phí Đào Tạo')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Thuế VAT')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Thành Tiền')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Tiêu Chuẩn/ Quy Định')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:U$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['name_category_plan']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['name_evaluate'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",$value['content_evaluate'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['position_educate'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['name_type_evaluate'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", ($value['detail']))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", ($value['quantity']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", get_staff_full_name($value['staff_educate']))->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", ($value['unit_educate']))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", ($value['name_result']))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", get_staff_full_name($value['staff_evaluate']))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", ($value['name_report']))->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", ($value['cost_money']))->getStyle("Q$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['cost_money']));
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", ($value['name_tax']))->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", ($value['total']))->getStyle("S$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total']));
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $value['standard'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])){
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_plan_educate||'.$value['id'];
                        $this->db->where('id',$value['id']);
                        $this->db->update('tbl_suggest_plan_educate',['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_plan_educate/';
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
                        $objDrawing1->setCoordinates('U' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", '')->getStyle("U$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:U$rowBegin")->applyFromArray([
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

                    $objPHPExcel->getActiveSheet()->getStyle("K$rowBegin:K$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("R$rowBegin:R$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_ke_hoach_dao_tao') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
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