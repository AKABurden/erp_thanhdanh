<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_criteria extends AdminController
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

        $this->preViewDeliveryCriteria = true;
        $this->preViewOwnDeliveryCriteria = true;
        $this->preAddDeliveryCriteria = true;
        $this->preEditDeliveryCriteria = true;
        $this->preApproveDeliveryCriteria = true;
        $this->preDeleteDeliveryCriteria = true;
    }

    public function index()
    {
        if (!$this->preViewDeliveryCriteria && !$this->preViewOwnDeliveryCriteria) {
            access_denied();
        }
        $data['title'] = _l('dt_delivery_criteria');
        $this->load->view('admin/delivery_criteria/index', $data);
    }

    public function getDeliveryCriteria()
    {

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_delivery_criteria.id as id',
            'tbl_stages.name as name_stage',
            'tbl_category_hand_over.name as name_category_hand_over',
            'tbl_delivery_criteria.procedure_name as procedure_name',
            'tbl_hand_over_task.name as name_hand_over_task',
            'tbl_delivery_criteria.standard as standard',
            'tbl_delivery_criteria.method_name as method_name',
            'tbl_delivery_criteria.created_by as created_by'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_delivery_criteria';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_stages ON tbl_stages.id = tbl_delivery_criteria.stage_id',
            'LEFT JOIN tbl_category_hand_over ON tbl_category_hand_over.id = tbl_delivery_criteria.hand_over_category_id',
            'LEFT JOIN tbl_hand_over_task ON tbl_hand_over_task.id = tbl_delivery_criteria.hand_over_id',
        ];

        if (!$this->preViewDeliveryCriteria) {
            array_push($where, 'AND tbl_delivery_criteria.created_by =', get_staff_user_id());
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_stage']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_category_hand_over']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['procedure_name']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_hand_over_task']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['standard']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['method_name']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $edit = $this->preEditDeliveryCriteria ? '<a class="tnh-modal" href="' . base_url('admin/delivery_criteria/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteDeliveryCriteria ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/delivery_criteria/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
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
                $this->form_validation->set_rules('stage_id', lang("Công đoạn"), 'required');
                $this->form_validation->set_rules('name', lang("Tên tiêu chí giao hàng"), 'trim|required|is_unique[tbl_delivery_criteria.name]');
                if ($this->form_validation->run() == true) {
                    $stage_id = $this->input->post('stage_id');
                    $name = $this->input->post('name');
                    $hand_over_category_id = $this->input->post('hand_over_category_id');
                    $hand_over_id = $this->input->post('hand_over_id');
                    $standard = ($this->input->post('standard'));
                    $method_name = ($this->input->post('method_name'));
                    $procedure_name = ($this->input->post('procedure_name'));
                    $fields = [
                        'stage_id' => $stage_id,
                        'name' => $name,
                        'hand_over_category_id' => $hand_over_category_id,
                        'hand_over_id' => $hand_over_id,
                        'standard' => $standard,
                        'method_name' => $method_name,
                        'procedure_name' => $procedure_name,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_delivery_criteria',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        insertActivityLog([
                            'type_parent_obj' => 'delivery_criteria',
                            'table_obj' => 'tbl_delivery_criteria',
                            'id_obj' => $id,
                            'name_obj' => $name,
                            'content' => lang('Thêm tiêu chí giao hàng') . ' [' . $name . ']',
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
                $this->db->select('tbl_delivery_criteria.*');
                $this->db->from('tbl_delivery_criteria');
                $this->db->where('tbl_delivery_criteria.id',$id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['name'] != $this->input->post('name')) {
                    $this->form_validation->set_rules('name', lang("Tên tiêu chí giao hàng"), 'trim|required|is_unique[tbl_delivery_criteria.name]');
                }
                $this->form_validation->set_rules('stage_id', lang("Công đoạn"), 'required');
                if ($this->form_validation->run() == true) {
                    $stage_id = $this->input->post('stage_id');
                    $name = $this->input->post('name');
                    $hand_over_category_id = $this->input->post('hand_over_category_id');
                    $hand_over_id = $this->input->post('hand_over_id');
                    $standard = ($this->input->post('standard'));
                    $method_name = ($this->input->post('method_name'));
                    $procedure_name = ($this->input->post('procedure_name'));
                    $fields = [
                        'stage_id' => $stage_id,
                        'name' => $name,
                        'hand_over_category_id' => $hand_over_category_id,
                        'hand_over_id' => $hand_over_id,
                        'standard' => $standard,
                        'method_name' => $method_name,
                        'procedure_name' => $procedure_name,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_delivery_criteria',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'delivery_criteria',
                            'table_obj' => 'tbl_delivery_criteria',
                            'id_obj' => $id,
                            'name_obj' => $dtData['name'],
                            'content' => lang('Sửa phiếu yêu cầu đánh giá thiết bị') . ' [' . $dtData['name'] . ']',
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
                if (!$this->preAddDeliveryCriteria){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_delivery_criteria');
            } else {
                if (!$this->preEditDeliveryCriteria){
                    accessDenied(true);
                }
                $this->db->select('tbl_delivery_criteria.*');
                $this->db->from('tbl_delivery_criteria');
                $this->db->where('tbl_delivery_criteria.id',$id);
                $dtData = $this->db->get()->row_array();

                $this->db->select('id,code,name');
                $this->db->from('tbl_hand_over_task');
                $this->db->where('tbl_hand_over_task.category_hand_over_id',$dtData['hand_over_category_id']);
                $this->db->where('tbl_hand_over_task.type_hide',0);
                $dtHandOver = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtHandOver'] = $dtHandOver;
                $data['title'] = lang('dt_edit_delivery_criteria');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['dtCategoryHandOver'] = get_table_where('tbl_category_hand_over',['type_show' => 1]);
        $data['id'] = $id;
        $this->load->view('admin/delivery_criteria/detail',$data);
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

    public function delete($id){
        if (!$this->preDeleteDeliveryCriteria){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_delivery_criteria.*');
        $this->db->from('tbl_delivery_criteria');
        $this->db->where('tbl_delivery_criteria.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }


        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_delivery_criteria');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'delivery_criteria',
                'table_obj' => 'tbl_delivery_criteria',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa tiêu chí giao hàng') . ' [' . $dtData['name'] . ']',
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
                tbl_delivery_criteria.id as id, 
                tbl_delivery_criteria.name as name,
                tbl_stages.name as name_stage,
                tbl_category_hand_over.name as name_category_hand_over,
                tbl_delivery_criteria.procedure_name as procedure_name,
                tbl_hand_over_task.name as name_hand_over_task,
                tbl_delivery_criteria.standard as standard,
                tbl_delivery_criteria.method_name as method_name,
                tbl_delivery_criteria.created_by as created_by
            ');
            $this->db->from('tbl_delivery_criteria');
            $this->db->join('tbl_stages','tbl_stages.id = tbl_delivery_criteria.stage_id','inner');
            $this->db->join('tbl_category_hand_over','tbl_category_hand_over.id = tbl_delivery_criteria.hand_over_category_id','left');
            $this->db->join('tbl_hand_over_task','tbl_hand_over_task.id = tbl_delivery_criteria.hand_over_id','left');


            if (!$this->preViewDeliveryCriteria) {
                $this->db->where('tbl_delivery_criteria.created_by = '.get_staff_user_id().'');
            }
            $this->db->order_by('tbl_delivery_criteria.id desc');
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
                ('TIÊU CHÍ GIAO HÀNG'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Tên Tiêu Chí');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã Công Đoạn');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Loại Bàn Giao');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Quy Trình');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Tiêu Chí Bàn Giao')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Tiêu Chuẩn')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Phương Pháp')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:I$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_stage']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['name_category_hand_over'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['procedure_name']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['name_hand_over_task'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['standard'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['method_name'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])){
                        $code = $value['barcode'];
                    } else {
                        $code = 'delivery_criteria||'.$value['id'];
                        $this->db->where('id',$value['id']);
                        $this->db->update('tbl_delivery_criteria',['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/delivery_criteria/';
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
                        $objDrawing1->setCoordinates('I' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin", '')->getStyle("I$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:I$rowBegin")->applyFromArray([
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
            $filename = lang('tieu_chi_giao_hang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
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

    public function searchStages($id = 0){
        $data = [];
        $limit = 50;
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_stages.id as id, 
            tbl_stages.name as text, 
        ', false);
        $this->db->from('tbl_stages');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_stages.code', $term);
            $this->db->or_like('tbl_stages.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $data['results'] =  $this->db->get()->result_array();
        if ($id) {
            $dtData = get_table_where('tbl_stages',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['name']];
        }
        echo json_encode($data);
    }

    public function getHandOverByCategory(){
        $hand_over_category_id = $this->input->post('hand_over_category_id');

        $this->db->select('
            id,
            code,
            name
        ');
        $this->db->from('tbl_hand_over_task');
        $this->db->where('tbl_hand_over_task.category_hand_over_id',$hand_over_category_id);
        $this->db->where('tbl_hand_over_task.type_hide',0);
        $dtData = $this->db->get()->result_array();
        $data['dtData'] = $dtData;
        echo json_encode($data);
    }
}