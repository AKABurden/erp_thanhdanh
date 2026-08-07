<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Machine_information extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');

        $this->task_status = $this->tasks_model->get_statuses();
        $this->tasksPriorities = get_tasks_priorities();
    }

    public function index()
    {
        $preViewMachineInfomation = true;
        $preViewOwnMachineInfomation = true;
        if (!$preViewMachineInfomation && !$preViewOwnMachineInfomation) {
            access_denied();
        }
        $data['title'] = _l('Phiếu thông tin máy');
        $this->load->view('admin/machine_information/index', $data);
    }

    public function getMachineInfomation(){
        $preViewMachineInfomation = true;
        $machines_search = $this->input->post('machines_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_machines.id as id',
            '"" as date',
            'tbl_category_machines.code as code_category',
            'tbl_category_machines.name as name_category',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tbl_machines.model as model',
            'tbl_machines.origin as origin',
            'tbl_machines.year_manu as year_manu',
            'tbl_machines.specifications as specifications',
            'tbl_machines.performance as performance',
            'tbl_machines.recording_technique as recording_technique',
            'tbl_machines.physical_characteristics as physical_characteristics',
            'tblsuppliers.company as name_supplier',
            'tbl_machines.paper_size_max as paper_size_max',
            'tbl_machines.paper_size_min as paper_size_min',
            'tbl_machines.voltage as voltage',
            'tbl_machines.speed as speed',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_machines';
        $where = [];
        $filter = [];

        if (!empty($machines_search)){
            array_push($where,'AND tbl_machines.id = '.$machines_search.'');
        }

        $join = [
            'LEFT JOIN tbl_category_machines ON tbl_category_machines.id = tbl_machines.category_machine_id',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_machines.supplier_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $j = 0;
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . (date('d/m/Y')) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_category']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['name_category']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['code_machines']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['name_machines']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['model']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['origin']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['year_manu']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['specifications']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['performance']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['recording_technique']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['physical_characteristics']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['name_supplier']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['paper_size_max']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['paper_size_min']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['voltage']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['speed']) . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $machines_search = $this->input->post('machines_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();


            $this->db->select('
               tbl_machines.id as id,
               tbl_category_machines.code as code_category,
               tbl_category_machines.name as name_category,
               tbl_machines.code as name_type_maintenance,
               tbl_machines.name as code_machines,
               tbl_machines.name as name_machines,
               tbl_machines.model as model,
               tbl_machines.origin as origin,
               tbl_machines.year_manu as year_manu,
               tbl_machines.specifications as specifications,
               tbl_machines.performance as performance,
               tbl_machines.recording_technique as recording_technique,
               tbl_machines.physical_characteristics as physical_characteristics,
               tblsuppliers.company as name_supplier,
               tbl_machines.paper_size_max as paper_size_max,
               tbl_machines.paper_size_min as paper_size_min,
               tbl_machines.voltage as voltage,
               tbl_machines.speed as speed
            ');
            $this->db->from('tbl_machines');
            $this->db->join('tbl_category_machines','tbl_category_machines.id = tbl_machines.category_machine_id','left');
            $this->db->join('tblsuppliers','tblsuppliers.id = tbl_machines.supplier_id','left');

            if (!empty($machines_search)) {
                $this->db->where("tbl_machines.id =  ".$machines_search."");
            }
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
                ('PHIẾU THÔNG TIN LÝ LỊCH MÁY'))->getStyle("A1")->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$sttRow.':'.'A'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Ngày');
            $objPHPExcel->getActiveSheet()->mergeCells('B'.$sttRow.':'.'B'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã Nhóm Thiết Bị');
            $objPHPExcel->getActiveSheet()->mergeCells('C'.$sttRow.':'.'C'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Tên Nhóm Thiết Bị');
            $objPHPExcel->getActiveSheet()->mergeCells('D'.$sttRow.':'.'D'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mã Thiết Bị Máy Móc');
            $objPHPExcel->getActiveSheet()->mergeCells('E'.$sttRow.':'.'E'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Tên Thiết Bị Máy Móc')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells('F'.$sttRow.':'.'F'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Model')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells('G'.$sttRow.':'.'G'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Xuất Xứ')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells('H'.$sttRow.':'.'H'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Năm Sản Xuất')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells('I'.$sttRow.':'.'I'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Thông Số Kỹ Thuật Chung')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells('J'.$sttRow.':'.'J'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Hiệu Suất')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells('K'.$sttRow.':'.'K'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Kỹ Thuật Ghi Bản')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells('L'.$sttRow.':'.'L'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Đặc Điểm Vật Lý')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells('M'.$sttRow.':'.'M'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Lắp Đặt Bởi (Nhà Cung Cấp)')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells('N'.$sttRow.':'.'N'.($sttRow + 1));
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Đặc Tính')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells('O'.$sttRow.':'.'R'.($sttRow));
            $objPHPExcel->getActiveSheet()->setCellValue('O'.($sttRow+1).'', 'Khổ Giấy (Max)');
            $objPHPExcel->getActiveSheet()->setCellValue('P'.($sttRow+1).'', 'Khổ Giấy (Min)');
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.($sttRow+1).'', 'Điện Áp');
            $objPHPExcel->getActiveSheet()->setCellValue('R'.($sttRow+1).'', 'Tốc Độ');
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
            $objPHPExcel->getActiveSheet()->getStyle("A".($sttRow + 1).":R".($sttRow + 1)."")->applyFromArray([
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
            $rowBegin = $sttRow + 1;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $specifications = $value['specifications'];
                    $specifications = str_replace('<br>',"\n",$specifications);
                    $specifications = str_replace('<br\>',"\n",$specifications);
                    $specifications = str_replace('<\br>',"\n",$specifications);
                    $specifications = strip_tags($specifications);
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", date('d/m/Y'));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['code_category']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['name_category'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['code_machines']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['name_machines'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['model'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['origin'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['year_manu'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $specifications)->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['performance'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['recording_technique'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['physical_characteristics'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['name_supplier'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['paper_size_max'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['paper_size_min'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['voltage'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $value['speed'])->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);


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
                }
            }
            $filename = lang('phieu_thong_tin_ly_lich_may') . '.xls';
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