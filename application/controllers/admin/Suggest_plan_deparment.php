<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_plan_deparment extends AdminController
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

        $this->preViewSuggestPlanDeparment = true;
        $this->preViewOwnSuggestPlanDeparment = true;
        $this->preAddSuggestPlanDeparment = true;
        $this->preEditSuggestPlanDeparment = true;
        $this->preApproveSuggestPlanDeparment = true;
        $this->preDeleteSuggestPlanDeparment= true;
    }

    public function index()
    {
        if (!$this->preViewSuggestPlanDeparment && !$this->preViewOwnSuggestPlanDeparment) {
            access_denied();
        }
        $data['title'] = _l('dt_suggest_plan_deparment');
        $this->load->view('admin/suggest_plan_deparment/index', $data);
    }

    public function getSuggestPlanDeparment()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_plan_deparment.id as id',
            'tbl_suggest_plan_deparment.date as date',
            'tbl_suggest_plan_deparment.reference_no as reference_no',
            'tbldepartments.name as name_deparment',
            'tblcategory_tasks.code as code_task',
            'tblcategory_tasks.content as name_task',
            'tbl_suggest_plan_deparment.date_start as date_start',
            'tbl_suggest_plan_deparment.date_reminder as date_reminder',
            'tbl_suggest_plan_deparment.staff_id as staff_id',
            'tbl_suggest_plan_deparment.status as status',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_plan_deparment';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbldepartments ON tbldepartments.departmentid = tbl_suggest_plan_deparment.deparment_id',
            'INNER JOIN tblcategory_tasks ON tblcategory_tasks.id = tbl_suggest_plan_deparment.category_task_id',
        ];

        if (!$this->preViewSuggestPlanDeparment) {
            array_push($where, 'AND (tbl_suggest_plan_deparment.created_by = '.get_staff_user_id().' OR tbl_suggest_plan_deparment.staff_id = '.get_staff_user_id().' )');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_plan_deparment.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_plan_deparment.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_plan_deparment.date_status',
            'tbl_suggest_plan_deparment.staff_status'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_plan_deparment/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . ($aRow['name_deparment']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_task']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_task']) . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date_start']) ? _dhau($aRow['date_start']) : '') . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date_reminder']) ? _dhau($aRow['date_reminder']) : '') . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_id']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } elseif ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left">' . $_data . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_plan_deparment/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestPlanDeparment ? '<a class="tnh-modal" href="' . base_url('admin/suggest_plan_deparment/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestPlanDeparment ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_plan_deparment/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $this->db->select('tbl_suggest_plan_deparment.*');
        $this->db->from('tbl_suggest_plan_deparment');
        $this->db->where('tbl_suggest_plan_deparment.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_plan_deparment.reference_no]');
                }
            } else {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_plan_deparment.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('deparment_id', lang("Phòng ban"), 'required');
            $this->form_validation->set_rules('category_task_id', lang("Mã công việc"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_plan_deparment');
                    $date = to_sql_date($this->input->post('date'), true);
                    $date_start = !empty($this->input->post('date_start')) ? to_sql_date($this->input->post('date_start')) : null;
                    $date_reminder = !empty($this->input->post('date_reminder')) ? to_sql_date($this->input->post('date_reminder')) : null;
                    $branch_id = $this->input->post('branch_id');
                    $staff_id = $this->input->post('staff_id');
                    $deparment_id = $this->input->post('deparment_id');
                    $category_task_id = $this->input->post('category_task_id');
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'date_start' => $date_start,
                        'staff_id' => $staff_id,
                        'category_task_id' => $category_task_id,
                        'deparment_id' => $deparment_id,
                        'date_reminder' => $date_reminder,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_plan_deparment',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_plan_deparment') == $reference_no) {
                            updateReference('suggest_plan_deparment');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_plan_deparment',
                            'table_obj' => 'tbl_suggest_plan_deparment',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu kế hoạch phòng ban') . ' [' . $reference_no . ']',
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
                    $date_start = !empty($this->input->post('date_start')) ? to_sql_date($this->input->post('date_start')) : null;
                    $date_reminder = !empty($this->input->post('date_reminder')) ? to_sql_date($this->input->post('date_reminder')) : null;
                    $branch_id = $this->input->post('branch_id');
                    $staff_id = $this->input->post('staff_id');
                    $deparment_id = $this->input->post('deparment_id');
                    $category_task_id = $this->input->post('category_task_id');
                    $fields = [
                        'date' => $date,
                        'date_start' => $date_start,
                        'staff_id' => $staff_id,
                        'category_task_id' => $category_task_id,
                        'deparment_id' => $deparment_id,
                        'date_reminder' => $date_reminder,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_plan_deparment',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_plan_deparment',
                            'table_obj' => 'tbl_suggest_plan_deparment',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu kế hoạch phòng ban') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestPlanDeparment){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_plan_deparment');
            } else {
                if (!$this->preEditSuggestPlanDeparment){
                    accessDenied(true);
                }
                $this->db->select('tbl_suggest_plan_deparment.*');
                $this->db->from('tbl_suggest_plan_deparment');
                $this->db->where('tbl_suggest_plan_deparment.id',$id);
                $dtData = $this->db->get()->row_array();

                if ($dtData['status'] == 1){
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));

                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_suggest_plan_deparment');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_plan_deparment');
        $data['departments'] = get_table_where('tbldepartments', ['room_id !=' => 0]);
        $data['category_tasks'] = $this->site_model->getCategoryTasks([]);
        $this->load->view('admin/suggest_plan_deparment/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_plan_deparment');

        $this->db->select('tbl_suggest_plan_deparment.*,
            tbldepartments.name as name_deparment,
            CONCAT(tblcategory_tasks.code,"-",tblcategory_tasks.content) as code_task,
        ');
        $this->db->from('tbl_suggest_plan_deparment');
        $this->db->join('tbldepartments','tbldepartments.departmentid = tbl_suggest_plan_deparment.id');
        $this->db->join('tblcategory_tasks','tblcategory_tasks.id = tbl_suggest_plan_deparment.category_task_id');
        $this->db->where('tbl_suggest_plan_deparment.id',$id);
        $dtData = $this->db->get()->row_array();

        $data['dtData'] = $dtData;
        $this->load->view('admin/suggest_plan_deparment/view',$data);
    }

    public function agree(){
        if (!$this->preApproveSuggestPlanDeparment) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_plan_deparment.*');
        $this->db->from('tbl_suggest_plan_deparment');
        $this->db->where('tbl_suggest_plan_deparment.id',$suggest_id);
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
            $up = $this->db->update('tbl_suggest_plan_deparment',$options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_plan_deparment',
                    'table_obj' => 'tbl_suggest_plan_deparment',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu kế hoạch phòng ban') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteSuggestPlanDeparment){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_plan_deparment.*');
        $this->db->from('tbl_suggest_plan_deparment');
        $this->db->where('tbl_suggest_plan_deparment.id',$id);
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
        $success = $this->db->delete('tbl_suggest_plan_deparment');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'suggest_plan_deparment',
                'table_obj' => 'tbl_suggest_plan_deparment',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu kế hoạch phòng ban') . ' [' . $dtData['reference_no'] . ']',
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
               tbl_suggest_plan_recruitment.id as id,
               tbl_suggest_plan_recruitment.reference_no as reference_no,
               tbl_suggest_plan_recruitment.date as date,
               tbl_suggest_plan_recruitment.staff_id as staff_id,
               tbl_category_plan_time.name as name_category_plan,
               tbl_suggest_plan_recruitment.position_recruitment as position_recruitment,
               tbl_suggest_plan_recruitment.content_work as content_work,
               tbl_suggest_plan_recruitment.kpis as kpis,
               tbl_suggest_plan_recruitment.note as note,
               tbl_suggest_plan_recruitment.quantity as quantity,
               tbl_suggest_plan_recruitment.time_work as time_work,
               tbl_suggest_plan_recruitment.gender as gender,
               tbl_suggest_plan_recruitment.completion_time_limit as completion_time_limit,
               tbl_suggest_plan_recruitment.standard as standard,
               tbl_suggest_plan_recruitment.barcode as barcode,
            ');
            $this->db->from('tbl_suggest_plan_recruitment');
            $this->db->join('tbl_category_plan_time','tbl_category_plan_time.id = tbl_suggest_plan_recruitment.category_plan','inner');


            if (!$this->preViewSuggestPlanRecruitment) {
                $this->db->where('(tbl_suggest_plan_recruitment.created_by = '.get_staff_user_id().' OR tbl_suggest_plan_recruitment.staff_id = '.get_staff_user_id().')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_plan_recruitment.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_plan_recruitment.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_plan_recruitment.id desc');
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
                ('PHIẾU YÊU CẦU KẾ HOẠCH TUYỂN DỤNG'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Người Lập Kế Thời');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Nhóm Kế Hoạch');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Vị Trí Tuyển Dụng')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Mô Tả Công Việc')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'KPIs')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Lý Do')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Số Lượng')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Thời Gian Làm Việc')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Giới Tính')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Định Mức Thời Gian Hoàn Thành)')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Tiêu Chuẩn/ Quy Định')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'QR')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:O$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['position_recruitment'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['content_work'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['kpis'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",$value['note'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['quantity'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['time_work'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    if ($value['gender'] == "male" ){
                        $htmlGender = 'Nam';
                    } elseif ($value['gender'] == "female" ){
                        $htmlGender = 'Nữ';
                    } elseif ($value['gender'] == "other" ){
                        $htmlGender = 'Khác';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $htmlGender)->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['completion_time_limit'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['standard'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])){
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_plan_recruitment||'.$value['id'];
                        $this->db->where('id',$value['id']);
                        $this->db->update('tbl_suggest_plan_recruitment',['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_plan_recruitment/';
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
                        $objDrawing1->setCoordinates('O' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", '')->getStyle("O$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:O$rowBegin")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->getStyle("J$rowBegin:J$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("L$rowBegin:L$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_ke_hoach_tuyen_dung') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
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