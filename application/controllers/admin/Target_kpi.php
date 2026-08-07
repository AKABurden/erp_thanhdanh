<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Target_kpi extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function target_room()
    {
        $data['title'] = _l('Định mức ngân sách');
        $dtDepartment = get_table_where('tbldepartments', ['room_id !=' => 0]);
        $data['dtDepartment'] = $dtDepartment;
        $this->load->view('admin/target_kpi/room/target_room', $data);
    }

    public function getTargetRoom()
    {
        $department_search = $this->input->post('department_search') ?? [];
        $whereCost = "";
        if (!empty($department_search)) {
            $whereCost = 'AND tblcost_target.department_id IN (' . implode(',', $department_search) . ')';
        }
        $tb_cost_department = "(
            SELECT 
                tblcost_department.cost_id,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tblcost_department
            JOIN tbldepartments ON tblcost_department.department_id = tbldepartments.departmentid
            GROUP BY tblcost_department.cost_id
        ) tb_cost_department ";
        $aColumns = [
            'tbl_type_cost.id as id_type',
            'tbl_type_cost.code as code_type',
            'tbl_type_cost.name as name_type',
            'tb_cost.code as code_parent',
            'tb_cost.name as name_parent',
            'tblcosts.code as code',
            'tblcosts.name as name',
            '"" as stt',
            'CASE 
                WHEN tblcosts.type_cost = 1 THEN tbl_category_items.name
                WHEN tblcosts.type_cost = 2 THEN tbl_machines.name
                WHEN tblcosts.type_cost = 3 THEN tbl_category_payslip.name
                ELSE tblcosts.detail
             END as detail',
            'tb_cost_department.name_department as name_department'
        ];

        $year = $this->input->post('year_search');
        for ($i = 1; $i <= 12; $i++) {
            $aColumns[] = '(SELECT SUM(money) FROM tblcost_target WHERE tblcost_target.id_cost = tblcosts.id AND month = "' . $i . '" AND year = "' . $year . '" ' . $whereCost . ' LIMIT 1) as money_month_' . $i;
        }
        $where = [
            'AND tblcosts.active = 1 AND tblcosts.lever > 1'
        ];
//        if($this->input->post('start_date')) {
//            $where[] = 'AND DATE_FORMAT(tblclients.datecreated, "%Y-%m-%d") >= "' . to_sql_date($this->input->post('start_date')).'"';
//        }
//        if($this->input->post('ennd_date')) {
//            $where[] = 'AND DATE_FORMAT(tblclients.datecreated, "%Y-%m-%d") <= "' . to_sql_date($this->input->post('start_date')).'"';
//        }

        if (!empty($department_search)) {
            $where[] = 'AND EXISTS (
                SELECT 1
                FROM tblcost_department
                WHERE tblcost_department.department_id IN (' . implode(',', $department_search) . ')
                AND tblcost_department.cost_id = tblcosts.id
            )
            ';
        }

        $sIndexColumn = 'id';
        $sTable = 'tblcosts';
        $join = [
            'LEFT JOIN tblcosts tb_cost ON tb_cost.id = tblcosts.costs_parent',
            'LEFT JOIN tbl_type_cost ON tbl_type_cost.id = tblcosts.type',
            'LEFT JOIN '.$tb_cost_department.' ON tb_cost_department.cost_id = tblcosts.id',
            'LEFT JOIN tbl_machines ON tbl_machines.id = tblcosts.object_id AND tblcosts.type_cost = 2',
            'LEFT JOIN tbl_category_payslip ON tbl_category_payslip.id = tblcosts.object_id AND tblcosts.type_cost = 3',
            'LEFT JOIN tbl_category_items ON tbl_category_items.id = tblcosts.object_id AND tblcosts.type_cost = 1',
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,
            ['tblcosts.id', 'tblcosts.type', 'tblcosts.type_cost', 'tblcosts.object_id']);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $group = "";
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $cost_id = $aRow['id'];
            $row = array();
            if ($group != $aRow['code_parent']) {
                $stt = 1;
            }
            $group = $aRow['code_parent'];
            $row[] = $start;
//            $row[] = $aRow['id'];
            $row[] = '<div>' . $aRow['code_type'] . '</div>';
            $row[] = '<div>' . $aRow['name_type'] . '</div>';
            $row[] = $aRow['code_parent'];
            $row[] = $aRow['name_parent'];
            $row[] = $aRow['code'];
            $row[] = $aRow['name'];
            $row[] = '<div class="text-center">' . $stt . '</div>';
            $row[] = $aRow['detail'];
            $row[] = $aRow['name_department'];
            for ($i = 1; $i <= 12; $i++) {
                $classDanger = '';
                if (empty($aRow['money_month_' . $i])) {
                    $classDanger = 'text-danger';
                }
                $row[] = '<div class="text-center" style="white-space: nowrap;"><a class="c_modal ' . $classDanger . '" href="' . admin_url('target_kpi/detail/' . $aRow['id'] . '/' . $year . '/' . $i) . '">' . number_format_data($aRow['money_month_' . $i]) . ' <i class="fa fa-pencil"></i></a></div>';
            }
            $stt++;
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        die();

    }

    public function detail($id = '', $year = '', $month = '')
    {
        $this->db->where('id_cost', $id);
        $this->db->where('year', $year);
        $this->db->where('month', $month);
        $cost_target = $this->db->get('tblcost_target')->result_array();

        if ($this->input->post()) {
            $counter = $this->input->post('counter');
            $items = [];
            if (!empty($counter)){
                foreach ($counter as $key => $value){
                    $id_item = !empty($this->input->post('id_item')[$key]) ? $this->input->post('id_item')[$key] : 0;
                    $money = !empty($this->input->post('money')[$key]) ? number_unformat($this->input->post('money')[$key]) : 0;
                    $department_id = $this->input->post('department_id')[$key];
                    if (empty($department_id)){
                        continue;
                    }
                    $items[] = [
                        'id' => $id_item,
                        'department_id' => $department_id,
                        'id_cost' => $id,
                        'month' => $month,
                        'year' => $year,
                        'start_date' => $year.'-'.sprintf("%02s", $month).'-01',
                        'end_date' => date($year.'-'.sprintf("%02s", $month).'-t'),
                        'money' => $money,
                        'upadate_by' => get_staff_user_id(),
                        'date_update' => date('Y-m-d H:i:s'),
                    ];
                }
            }
            $count = 0;
            $this->db->where('tblcost_target.id_cost',$id);
            $this->db->where('tblcost_target.month',$month);
            $this->db->where('tblcost_target.year',$year);
            $count = $this->db->delete('tblcost_target');
            foreach ($items as $key => $value){
                $check = get_table_where('tblcost_target',['id' => $value['id']],'','row_array');
                if (!empty($check)){
                    $this->db->where('id',$value['id']);
                    $this->db->update('tblcost_target',[
                        'id_cost' => $value['id_cost'],
                        'month' => $value['month'],
                        'year' => $value['year'],
                        'department_id' => $value['department_id'],
                        'start_date' => $value['start_date'],
                        'end_date' => $value['end_date'],
                        'money' => $value['money'],
                        'upadate_by' => $value['upadate_by'],
                        'date_update' => $value['date_update'],
                    ]);
                    $count ++;
                } else {
                    $this->db->insert('tblcost_target',[
                        'id' => $value['id'],
                        'id_cost' => $value['id_cost'],
                        'month' => $value['month'],
                        'year' => $value['year'],
                        'department_id' => $value['department_id'],
                        'start_date' => $value['start_date'],
                        'end_date' => $value['end_date'],
                        'money' => $value['money'],
                        'upadate_by' => $value['upadate_by'],
                        'date_update' => $value['date_update'],
                    ]);
                    $count ++;
                }
            }
            if ($count > 0){
                $data['success'] = true;
                $data['message'] = lang('Cập nhập thành công');
            } else {
                $data['success'] = false;
                $data['message'] = lang('Cập nhập thất bại');
            }
            echo json_encode($data);die();
        } else {
            $data['cost_target'] = $cost_target;
            $data['title'] = 'Cập nhật mục tiêu ngân sách';
            $data['id'] = $id;
            $data['year'] = $year;
            $data['month'] = $month;
            $dtDepartment = get_table_where('tbldepartments', ['room_id !=' => 0]);
            $data['dtDepartment'] = $dtDepartment;
            $this->load->view('admin/target_kpi/room/detail', $data);
        }
    }

    public function import_excel()
    {
        $data['title'] = 'Import Excel';
        $this->load->view('admin/target_kpi/room/import_excel', $data);
    }

    public function action_import_excel()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('Q');
            $arraydata = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $data_insert = [];
            $data_update = [];
            foreach ($arraydata as $key => $value) {
                $code = trim($value[1]);
                $name = trim($value[2]);
                $year = trim($value[3]);
                $data_month = [
                    '1' => !empty($value[4]) ? trim($value[4]) : null,
                    '2' => !empty($value[5]) ? trim($value[5]) : null,
                    '3' => !empty($value[6]) ? trim($value[6]) : null,
                    '4' => !empty($value[7]) ? trim($value[7]) : null,
                    '5' => !empty($value[8]) ? trim($value[8]) : null,
                    '6' => !empty($value[9]) ? trim($value[9]) : null,
                    '7' => !empty($value[10]) ? trim($value[10]) : null,
                    '8' => !empty($value[11]) ? trim($value[11]) : null,
                    '9' => !empty($value[12]) ? trim($value[12]) : null,
                    '10' => !empty($value[13]) ? trim($value[13]) : null,
                    '11' => !empty($value[14]) ? trim($value[14]) : null,
                    '12' => !empty($value[15]) ? trim($value[15]) : null,
                ];
                $department_code = trim($value[16]);
                $this->db->from('tbldepartments');
                $this->db->where('tbldepartments.code', $department_code);
                $this->db->where('tbldepartments.room_id !=', 0);
                $dtDerpartment = $this->db->get()->row_array();
                $department_id = 0;
                if (!empty($dtDerpartment)) {
                    $department_id = $dtDerpartment['departmentid'];
                }

                if (empty($code) || empty($year)) {
                    continue;
                }
                $dtCost = $this->db->get_where('tblcosts', ['code' => $code])->row();
                if (!empty($dtCost)) {
                    foreach ($data_month as $month => $money) {
//                        print_arrays($money);
                        if (empty($money)) {
                            continue;
                        }
                        $this->db->where('year', $year);
                        $this->db->where('month', $month);
                        $this->db->where('id_cost', $dtCost->id);
                        $this->db->where('department_id', $department_id);
                        $cost_target = $this->db->get('tblcost_target')->row();
                        if (!empty($cost_target)) {
                            $data_update[] = [
                                'id' => $cost_target->id,
                                'money' => number_format_data($money, false),
                                'department_id' => $department_id,
                                'upadate_by' => get_staff_user_id(),
                                'date_update' => date('Y-m-d H:i:s'),
                            ];
                        } else {
                            $data_insert[] = [
                                'id_cost' => $dtCost->id,
                                'month' => $month,
                                'year' => $year,
                                'department_id' => $department_id,
                                'start_date' => $year . '-' . sprintf("%02s", $month) . '-01',
                                'end_date' => date($year . '-' . sprintf("%02s", $month) . '-t'),
                                'money' => number_format_data($money, false),
                                'upadate_by' => get_staff_user_id(),
                                'date_update' => date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                }
            }
            if (!empty($data_update)) {
                $success_update = $this->db->update_batch('tblcost_target', $data_update, 'id');
            }
            if (!empty($data_insert)) {
                $success_insert = $this->db->insert_batch('tblcost_target', $data_insert);
            }
        }
        if (empty($data_insert) && empty($data_update)) {
            echo json_encode(
                [
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => 'Import không thành công',
                ]
            );
            die();
        }
        echo json_encode(
            [
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . count($data_insert) . ' dòng và cập nhật ' . count($data_update) . ' dòng',
            ]
        );
        die();
    }

    public function getTargetRoomExcel()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
//            $start_date_search = $this->input->post('start_date_search');
//            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();
            $year = $this->input->post('year');
            $this->db->select([
                'tblcosts.id as id',
                'tbl_type_cost.code as code_type',
                'tbl_type_cost.name as name_type',
                'tb_cost.code as code_parent',
                'tb_cost.name as name_parent',
                'tblcosts.code as code',
                'tblcosts.name as name',
                '"" as stt',
            ]);
            $this->db->select('CASE 
                    WHEN tblcosts.type_cost = 1 THEN (tbl_category_items.name)
                    WHEN tblcosts.type_cost = 2 THEN (tbl_machines.name)
                    WHEN tblcosts.type_cost = 3 THEN (tbl_category_payslip.name)
                    ELSE tblcosts.detail
                 END as detail', false);
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('(SELECT money FROM tblcost_target WHERE tblcost_target.id_cost = tblcosts.id AND month = "' . $i . '" AND year = "' . $year . '" LIMIT 1) as money_month_' . $i);
            }
            $this->db->where('tblcosts.active = 1 AND tblcosts.lever > 1', false, false);
            $this->db->join('tblcosts tb_cost', 'tb_cost.id = tblcosts.costs_parent', 'left');
            $this->db->join('tbl_type_cost', 'tbl_type_cost.id = tblcosts.type', 'left');
            $this->db->join('tbl_machines', 'tbl_machines.id = tblcosts.object_id AND tblcosts.type_cost = 2', 'left');
            $this->db->join('tbl_category_payslip',
                'tbl_category_payslip.id = tblcosts.object_id AND tblcosts.type_cost = 3', 'left');
            $this->db->join('tbl_category_items',
                'tbl_category_items.id = tblcosts.object_id AND tblcosts.type_cost = 1', 'left');
            $this->db->order_by('tbl_type_cost.id asc,tb_cost.code asc');
            $dtData = $this->db->get('tblcosts')->result_array();

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
                    'name' => 'Times New Roman'
                ),
            ]);
            $titleAppend = '';
            if (!empty($start_date_search) && !empty($end_date_search)) {
                $titleAppend = 'Từ ngày ' . _d($start_date_search) . ' đến ngày ' . _d($end_date_search);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('THỐNG KÊ KHÁCH HÀNG') . $titleAppend)->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Loại');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Tên Loại');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Mã Chi Phí Cha');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Tên Chi Phí Cha');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '',
                'Mã Chi Phí')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '',
                'Tên Chi Phí')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '',
                'STT')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '',
                'Mô Tả')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '',
                'Năm')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '',
                'Tháng 1')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '',
                'Tháng 2')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '',
                'Tháng 3')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '',
                'Tháng 4')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '',
                'Tháng 5')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '',
                'Tháng 6')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $sttRow . '',
                'Tháng 7')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $sttRow . '',
                'Tháng 8')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $sttRow . '',
                'Tháng 9')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $sttRow . '',
                'Tháng 10')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $sttRow . '',
                'Tháng 11')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $sttRow . '',
                'Tháng 12')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:V$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name' => 'Times New Roman'
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
                $group = '';
                $stt = 0;
                foreach ($dtData as $key => $value) {
                    if ($group != $value['code_parent']) {
                        $stt = 1;
                    }
                    $group = $value['code_parent'];

                    $rowBegin++;
                    $row = [];
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['code_type']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_type']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin",
                        $value['code_parent'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin",
                        ($value['name_parent']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin",
                        $value['code'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",
                        $value['name'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",
                        $stt)->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",
                        $value['detail'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin",
                        $year)->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin",
                        $value['money_month_1'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin",
                        $value['money_month_2'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin",
                        $value['money_month_3'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin",
                        $value['money_month_4'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin",
                        $value['money_month_5'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin",
                        $value['money_month_6'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin",
                        $value['money_month_7'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin",
                        $value['money_month_8'])->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin",
                        $value['money_month_9'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin",
                        $value['money_month_10'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin",
                        $value['money_month_11'])->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin",
                        $value['money_month_12'])->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:V$rowBegin")->applyFromArray([
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
            $filename = lang('muc_tieu_ngan_sach') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
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

    public function synthetic_target_cost()
    {
        $data['title'] = 'Tổng hợp mục tiêu ngân sách';
        $dtDepartment = get_table_where('tbldepartments', ['room_id !=' => 0]);
        $data['dtDepartment'] = $dtDepartment;
        $this->load->view('admin/target_kpi/synthetic_target_cost', $data);
    }

    public function getSyntheticTagertCost()
    {
        $tb_cost_department = "(
            SELECT 
                tblcost_department.cost_id,
                GROUP_CONCAT(tbldepartments.departmentid) as department_id,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tblcost_department
            JOIN tbldepartments ON tblcost_department.department_id = tbldepartments.departmentid
            GROUP BY tblcost_department.cost_id
        ) tb_cost_department ";
        $aColumns = [
            'tbl_type_cost.id as id_type',
            'tbl_type_cost.code as code_type',
            'tbl_type_cost.name as name_type',
            'tb_cost.code as code_parent',
            'tb_cost.name as name_parent',
            'tblcosts.code as code',
            'tblcosts.name as name',
            '"" as stt',
            'CASE 
                WHEN tblcosts.type_cost = 1 THEN tbl_category_items.name
                WHEN tblcosts.type_cost = 2 THEN tbl_machines.name
                WHEN tblcosts.type_cost = 3 THEN tbl_category_payslip.name
                ELSE tblcosts.detail
             END as detail',
            'tb_cost_department.name_department as name_department',
        ];

        $year = $this->input->post('year_search');
        $department_search = $this->input->post('department_search') ?? 0;
        $whereCost = "";
        if (!empty($department_search)) {
            $whereCost = 'AND tblcost_target.department_id = '.$department_search.'';
        }
        $columnNew = '';
        for ($i = 1; $i <= 12; $i++) {
            $iNew = $i;
            if ($i < 10) {
                $i = '0' . $i;
            }
            $month_year = $i . '-' . $year;
            $aColumns[] = '(
                SELECT SUM(tblother_payslip_cost.total) as total_payslip 
                FROM tblother_payslip_cost 
                JOIN tblother_payslips ON tblother_payslips.id =  tblother_payslip_cost.other_payslip_id
                WHERE tblother_payslip_cost.cost_id = tblcosts.id 
                AND DATE_FORMAT(tblother_payslips.date, "%m-%Y") = "' . $month_year . '"
            ) as money_month_' . $i;
            if ($i == 12) {
                $columnNew .= '(SELECT SUM(money) FROM tblcost_target WHERE tblcost_target.id_cost = tblcosts.id AND month = "' . $iNew . '" AND year = "' . $year . '" ' . $whereCost . ' LIMIT 1) as money_month_tagert_' . $i;
            } else {
                $columnNew .= '(SELECT SUM(money) FROM tblcost_target WHERE tblcost_target.id_cost = tblcosts.id AND month = "' . $iNew . '" AND year = "' . $year . '" ' . $whereCost . ' LIMIT 1) as money_month_tagert_' . $i . ',';
            }
        }
        $where = [
            'AND tblcosts.active = 1 AND tblcosts.lever > 1'
        ];

        if (!empty($department_search)) {
            $where[] = 'AND EXISTS (
                SELECT 1
                FROM tblcost_department
                WHERE tblcost_department.department_id = '.$department_search.'
                AND tblcost_department.cost_id = tblcosts.id
            )
            ';
        }

        $sIndexColumn = 'id';
        $sTable = 'tblcosts';
        $join = [
            'LEFT JOIN tblcosts tb_cost ON tb_cost.id = tblcosts.costs_parent',
            'LEFT JOIN tbl_type_cost ON tbl_type_cost.id = tblcosts.type',
            'LEFT JOIN '.$tb_cost_department.' ON tb_cost_department.cost_id = tblcosts.id',
            'LEFT JOIN tbl_machines ON tbl_machines.id = tblcosts.object_id AND tblcosts.type_cost = 2',
            'LEFT JOIN tbl_category_payslip ON tbl_category_payslip.id = tblcosts.object_id AND tblcosts.type_cost = 3',
            'LEFT JOIN tbl_category_items ON tbl_category_items.id = tblcosts.object_id AND tblcosts.type_cost = 1',
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblcosts.id',
            'tblcosts.type',
            'tblcosts.type_cost',
            'tblcosts.object_id',
            'tb_cost_department.department_id',
            $columnNew
        ]);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $group = "";
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $cost_id = $aRow['id'];
            $row = array();
            if ($group != $aRow['code_parent']) {
                $stt = 1;
            }
            $countDepartment = 1;
            $department_id = $aRow['department_id'];
            $department_id = explode(',', $department_id);
            $countDepartment = count($department_id);
            $group = $aRow['code_parent'];
            $row[] = '<div class="text-center">' . $start . '</div>';
            $row[] = '<div>' . $aRow['code_type'] . '</div>';
            $row[] = '<div>' . $aRow['name_type'] . '</div>';
            $row[] = $aRow['code_parent'];
            $row[] = $aRow['name_parent'];
            $row[] = $aRow['code'];
            $row[] = $aRow['name'];
            $row[] = '<div class="text-center">' . $stt . '</div>';
            $row[] = '<div style="min-width: 120px">' . $aRow['detail'] . '</div>';
            $row[] = $aRow['name_department'];
            $totalPaySlip = 0;
            $totalTagert = 0;
            for ($i = 1; $i <= 12; $i++) {
                if ($i < 10) {
                    $i = '0' . $i;
                }
                $money_month_tagert = (!empty($aRow['money_month_tagert_' . $i]) ? ($aRow['money_month_tagert_' . $i]) : 0);
                if (in_array($department_search,$department_id)){
                    $money_month = (!empty($aRow['money_month_' . $i]) ? ($aRow['money_month_' . $i]) : 0);
                    $money_month = $money_month / $countDepartment;
                } else {
                    $money_month = (!empty($aRow['money_month_' . $i]) ? ($aRow['money_month_' . $i]) : 0);
                }
                $hmtlTagert = '';
                $hmtlPayslip = '';
                $hmtlTagert = '<div style="display: flex;color: green"><span style="width: 50px;margin-right: 5px">Chỉ tiêu:</span> <span style="font-weight: 600"> ' . (!empty($money_month_tagert) ? formatMoney($money_month_tagert) : '-') . '</span></div>';
                $hmtlPayslip = '<div style="display: flex"><span style="width: 50px;margin-right: 5px">Thực tế: </span><span style="font-weight: 600"> ' . (!empty($money_month) ? formatMoney($money_month) : '-') . '</span></div>';
                $htmlResult = '';
                if ($money_month > $money_month_tagert) {
                    $htmlResult = '<div style="display: flex"><span style="width: 50px;margin-right: 5px">Kết quả: </span><span style="color: red">Vượt</span></div>';
                } elseif ($money_month < $money_month_tagert) {
                    $htmlResult = '<div style="display: flex"><span style="width: 50px;margin-right: 5px">Kết quả: </span><span>Kém</span></div>';
                } else {
                    $htmlResult = '<div style="display: flex"><span style="width: 50px;margin-right: 5px">Kết quả: </span><span>Đạt</span></div>';
                }
                $row[] = '<div class="text-right">
                ' . $hmtlTagert . '
                ' . $hmtlPayslip . '
                ' . $htmlResult . '
               </div>';
                $totalPaySlip += $money_month;
                $totalTagert += $money_month_tagert;
            }

            $hmtlTagertAll = '';
            $hmtlPayslipAll = '';
            $hmtlTagertAll = '<div style="display: flex;color: green"><span style="width: 50px;margin-right: 5px">Chỉ tiêu:</span> <span style="font-weight: 600"> ' . (!empty($totalTagert) ? formatMoney($totalTagert) : '-') . '</span></div>';
            $hmtlPayslipAll = '<div style="display: flex"><span style="width: 50px;margin-right: 5px">Thực tế: </span><span style="font-weight: 600"> ' . (!empty($totalPaySlip) ? formatMoney($totalPaySlip) : '-') . '</span></div>';
            $htmlResultAll = '';
            if ($totalPaySlip > $totalTagert) {
                $htmlResultAll = '<div style="display: flex"><span style="width: 50px;margin-right: 5px">Kết quả: </span><span>Vượt</span></div>';
            } elseif ($totalPaySlip < $totalTagert) {
                $htmlResultAll = '<div style="display: flex"><span style="width: 50px;margin-right: 5px">Kết quả: </span><span>Kém</span></div>';
            } else {
                $htmlResultAll = '<div style="display: flex"><span style="width: 50px;margin-right: 5px">Kết quả: </span><span>Đạt</span></div>';
            }

            $row[] = '<div class="text-right">
                ' . $hmtlTagertAll . '
                ' . $hmtlPayslipAll . '
                ' . $htmlResultAll . '
            </div>';
            $stt++;
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        die();
    }

    public function excelSyntheticTagertCost()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();
            $year = $this->input->post('year');
            $department_search = $this->input->post('department_search') ?? 0;
            $whereCost = "";
            if (!empty($department_search)) {
                $whereCost = 'AND tblcost_target.department_id = '.$department_search.'';
            }
            $this->db->dbprefix = '';
            $tb_cost_department = "(
                SELECT 
                    tblcost_department.cost_id,
                    GROUP_CONCAT(tbldepartments.departmentid) as department_id,
                    GROUP_CONCAT(tbldepartments.name) as name_department
                FROM tblcost_department
                JOIN tbldepartments ON tblcost_department.department_id = tbldepartments.departmentid
                GROUP BY tblcost_department.cost_id
            ) tb_cost_department ";
            $this->db->select([
                'tblcosts.id as id',
                'tbl_type_cost.code as code_type',
                'tbl_type_cost.name as name_type',
                'tb_cost.code as code_parent',
                'tb_cost.name as name_parent',
                'tblcosts.code as code',
                'tblcosts.name as name',
                '"" as stt',
                'tb_cost_department.name_department as name_department',
                'tb_cost_department.department_id as department_id',
            ]);
            $this->db->select('CASE 
                    WHEN tblcosts.type_cost = 1 THEN (tbl_category_items.name)
                    WHEN tblcosts.type_cost = 2 THEN (tbl_machines.name)
                    WHEN tblcosts.type_cost = 3 THEN (tbl_category_payslip.name)
                    ELSE tblcosts.detail
                 END as detail', false);
            for ($i = 1; $i <= 12; $i++) {
                $iNew = $i;
                if ($i < 10) {
                    $i = '0' . $i;
                }
                $month_year = $i . '-' . $year;
                $this->db->select('(SELECT SUM(tblother_payslip_cost.total) as total_payslip 
                FROM tblother_payslip_cost 
                JOIN tblother_payslips ON tblother_payslips.id =  tblother_payslip_cost.other_payslip_id
                WHERE tblother_payslip_cost.cost_id = tblcosts.id 
                AND DATE_FORMAT(tblother_payslips.date, "%m-%Y") = "' . $month_year . '") as money_month_' . $i);
                $this->db->select('(SELECT money FROM tblcost_target WHERE tblcost_target.id_cost = tblcosts.id AND month = "' . $iNew . '" AND year = "' . $year . '" '.$whereCost.' LIMIT 1) as money_month_tagert_' . $i);
            }
            $this->db->where('tblcosts.active = 1 AND tblcosts.lever > 1', false, false);
            $this->db->join('tblcosts tb_cost', 'tb_cost.id = tblcosts.costs_parent', 'left');
            $this->db->join('tbl_type_cost', 'tbl_type_cost.id = tblcosts.type', 'left');
            $this->db->join($tb_cost_department, 'tb_cost_department.cost_id = tblcosts.id', 'left');
            $this->db->join('tbl_machines', 'tbl_machines.id = tblcosts.object_id AND tblcosts.type_cost = 2', 'left');
            $this->db->join('tbl_category_payslip',
                'tbl_category_payslip.id = tblcosts.object_id AND tblcosts.type_cost = 3', 'left');
            $this->db->join('tbl_category_items',
                'tbl_category_items.id = tblcosts.object_id AND tblcosts.type_cost = 1', 'left');
            $this->db->order_by('tbl_type_cost.id asc,tb_cost.code desc');
            if (!empty($department_search)) {
                $this->db->where('EXISTS (
                SELECT 1
                FROM tblcost_department
                WHERE tblcost_department.department_id = '.$department_search.'
                AND tblcost_department.cost_id = tblcosts.id
            )');
            }
            $dtData = $this->db->get('tblcosts')->result_array();

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
                    'name' => 'Times New Roman'
                ),
            ]);
            $titleAppend = '';
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('TỔNG HỢP MỤC TIÊU NGÂN SÁCH') . $titleAppend)->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:X1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Loại');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Tên Loại');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Mã Chi Phí Cha');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Tên Chi Phí Cha');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '',
                'Mã Chi Phí')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '',
                'Tên Chi Phí')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '',
                'STT')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '',
                'Mô Tả')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '',
                'Phòng Ban')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '',
                'Năm')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '',
                'Tháng 1')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '',
                'Tháng 2')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '',
                'Tháng 3')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '',
                'Tháng 4')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '',
                'Tháng 5')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $sttRow . '',
                'Tháng 6')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $sttRow . '',
                'Tháng 7')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $sttRow . '',
                'Tháng 8')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $sttRow . '',
                'Tháng 9')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $sttRow . '',
                'Tháng 10')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $sttRow . '',
                'Tháng 11')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $sttRow . '',
                'Tháng 12')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X' . $sttRow . '',
                'Tổng Hợp')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:X$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name' => 'Times New Roman'
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
                $group = '';
                $stt = 0;
                foreach ($dtData as $key => $value) {
                    if ($group != $value['code_parent']) {
                        $stt = 1;
                    }
                    $group = $value['code_parent'];
                    $countDepartment = 1;
                    $department_id = $value['department_id'];
                    $department_id = explode(',', $department_id);
                    $countDepartment = count($department_id);
                    $rowBegin++;
                    $row = [];
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['code_type']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_type']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin",
                        $value['code_parent'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin",
                        ($value['name_parent']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin",
                        $value['code'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",
                        $value['name'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",
                        $stt)->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",
                        $value['detail'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin",
                        $value['name_department'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin",
                        $year)->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $rowStt = 11;
                    $totalPaySlip = 0;
                    $totalTagert = 0;
                    for ($i = 1; $i <= 12; $i++) {
                        if ($i < 10) {
                            $i = '0' . $i;
                        }
                        $money_month_tagert = (!empty($value['money_month_tagert_' . $i]) ? ($value['money_month_tagert_' . $i]) : 0);
                        if (in_array($department_search,$department_id)){
                            $money_month = (!empty($value['money_month_' . $i]) ? ($value['money_month_' . $i]) : 0);
                            $money_month = $money_month / $countDepartment;
                        } else {
                            $money_month = (!empty($value['money_month_' . $i]) ? ($value['money_month_' . $i]) : 0);
                        }
                        $hmtlTagert = '';
                        $hmtlPayslip = '';
                        $hmtlTagert = 'Chỉ tiêu: ' . (!empty($money_month_tagert) ? formatMoney($money_month_tagert) : '-');
                        $hmtlPayslip = 'Thực tế: ' . (!empty($money_month) ? formatMoney($money_month) : '-');
                        $htmlResult = '';
                        if ($money_month > $money_month_tagert) {
                            $htmlResult = 'Kết quả: Vượt';
                        } elseif ($money_month < $money_month_tagert) {
                            $htmlResult = 'Kết quả: Kém';
                        } else {
                            $htmlResult = 'Kết quả: Đạt';
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$rowStt]$rowBegin",
                            $hmtlTagert . "\n" . $hmtlPayslip . "\n" . $htmlResult)->getStyle("$cloumns_excel[$rowStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $rowStt++;
                        $totalPaySlip += $money_month;
                        $totalTagert += $money_month_tagert;
                    }
                    $hmtlTagertAll = '';
                    $hmtlPayslipAll = '';
                    $hmtlTagertAll = 'Chỉ tiêu: ' . (!empty($totalTagert) ? formatMoney($totalTagert) : '-');
                    $hmtlPayslipAll = 'Thực tế: ' . (!empty($totalPaySlip) ? formatMoney($totalPaySlip) : '-');
                    $htmlResultAll = '';
                    if ($totalPaySlip > $totalTagert) {
                        $htmlResultAll = 'Kết quả: Vượt';
                    } elseif ($totalPaySlip < $totalTagert) {
                        $htmlResultAll = 'Kết quả: Kém';
                    } else {
                        $htmlResultAll = 'Kết quả: Đạt';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$rowStt]$rowBegin",
                        $hmtlTagertAll . "\n" . $hmtlPayslipAll . "\n" . $htmlResultAll)->getStyle("$cloumns_excel[$rowStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $stt++;
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:X$rowBegin")->applyFromArray([
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
            $filename = lang('tong_hop_muc_tieu_ngan_sach') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
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
