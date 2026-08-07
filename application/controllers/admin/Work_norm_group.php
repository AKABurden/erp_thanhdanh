<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Work_norm_group extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('work_norm_group_model');
    }

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $data = [];
            $this->app->get_table_data('work_norm_group', $data);
        }

        $data['title'] = _l('work_norm_group');
        $this->load->view('admin/work_norm_group/index', $data);
    }

    public function modal_excel()
    {
        $data['title'] = _l('Import excel ') . _l('work_norm_group');
        $this->load->view('admin/work_norm_group/import', $data);
    }

    public function import()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $successRow = 0;

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
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");
            $allSheet = $objPHPExcel->getSheetCount();
            $allSheetName = $objPHPExcel->getSheetNames();

            $arrField = [
                'stt',
                'code',
                'name',
                'task',
                'unit_id',
                'productivity_hour',
                'formula',
                'norm',
                'number_execution',
            ];
            for ($sheetNum = 0; $sheetNum < $allSheet; $sheetNum++) {
                $objWorksheet = $objPHPExcel->setActiveSheetIndex($sheetNum);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                
                for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
                    $submitData = [];
                    for ($columnNum = 1; $columnNum < $highestColumnIndex; $columnNum++) { // Bỏ qua cột 0:STT
                        $cellValue = $objWorksheet->getCellByColumnAndRow($columnNum, $rowNum)->getValue();
                        $cellValue = ($cellValue === '') ? null : $cellValue;

                        if (!empty($arrField[$columnNum])) {
                            if ($arrField[$columnNum] == 'unit_id' && !empty($cellValue)) { // link unit
                                $this->db->select('*');
                                $this->db->where('LOWER(TRIM(BOTH " " FROM unit)) = ', mb_strtolower(trim($cellValue), 'UTF-8'));
                                $unit = $this->db->get('tblunits')->row_array();
                                $cellValue = $unit['unitid'] ?? null;
                            }

                            $submitData[$arrField[$columnNum]] = $cellValue;
                        }
                    }

                    // var_dump($submitData);
                    if (!empty($submitData['code'])) {
                        $existedData = $this->work_norm_group_model->getByCode($submitData['code']);
                        if (!empty($existedData['id'])) { // update
                            $isSuccess = $this->work_norm_group_model->submit($submitData, $existedData['id'])['submitId'] ?? false;
                        } else { // insert
                            $isSuccess = $this->work_norm_group_model->submit($submitData)['submitId'] ?? false;
                        }

                        if ($isSuccess) {
                            $successRow++;
                        }
                    } else {
                        continue;
                    }
                }
            }
        }
        echo json_encode(
            [
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . formatNumber($successRow) . ' dòng',
            ]
        );
        die();
    }

    public function delete($id) {
        $response = $this->work_norm_group_model->delete($id);
        $response['alert_type'] = 'danger';
        if ($response['isSuccess']) {
            $response['alert_type'] = 'success';
        }
        echo json_encode($response);die;
    }

    public function export_excel() {
        $this->load->library('ciqrcode');
        $search_date = $this->input->post('search_date');

        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => array(
                'name'  => 'Times New Roman'
            ),
        ]);

        $tb_brand = '(
            SELECT
                tbl_brand.name as name,
                tbl_brand.id as id
            FROM tbl_brand
            GROUP BY tbl_brand.id
        ) as tb_brand';

        $this->db->select('
            tbl_work_norm_group.*,
            tblunits.unit
        ');
        $this->db->from('tbl_work_norm_group');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_work_norm_group.unit_id', 'left');
        $result = $this->db->get()->result_array();

        $styleTitle = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'bold' => true,
                'size' => 18,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $styleHeader = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => true,
                // 'color' => array('rgb' => '111112'),
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '4BACC6'),
                'size' => 12,
                // 'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];

        $stylePlain = [
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                // 'bold' => false,
                // 'color' => array('rgb' => '111112'),
                'size' => 11,
                'name' => 'Times New Roman'
            ),
        ];

        $headerFillColor = [
            'A' => array('rgb' => '92D050'),
        ];

        $cloumns_excel = cloumns_excel();
        $colName = [
            'stt' => 'STT',
            'code' => 'Mã Nhóm CV',
            'name' => 'Tên Nhóm CV',
            'task' => 'Công Việc',
            'unit' => 'ĐVT',
            'productivity_hour' => 'Năng Suất/Giờ',
            'formula' => 'Công Thức Tính Định Mức',
            'norm' => 'Định Mức',
            'number_execution' => 'Số Lần Thực Hiện',
        ];
        $aColumns = array_keys($colName);

        $title = mb_strtoupper(_l('work_norm_group'));
        $excelRowNum = 1;
        $maxCol = count($colName) - 1;
        $objPHPExcel->getActiveSheet()->mergeCells('A'.($excelRowNum).':'.$cloumns_excel[$maxCol].$excelRowNum);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$excelRowNum, $title)->getStyle("A".$excelRowNum)->applyFromArray($styleTitle);
        // $objPHPExcel->getActiveSheet()->freezePane('A1');
        
        $excelRowNum = 2;
        $mergeRowNum = 1;
        foreach ($aColumns as $key => $value) {
            foreach($headerFillColor as $colIndex => $color) {
                if ($cloumns_excel[$key] == $colIndex) {
                    $styleHeader['fill']['color'] = $color;
                    unset($headerFillColor[$colIndex]);
                    break;
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
            
            if ($value == 'qr') {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setWidth(10);
            } else {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
            }
        }

        $excelRowNum = 3;
        foreach ($result as $key => $aRow) {
			// $aRow['id'] = ($key+1);

            foreach ($aColumns as $colIndex => $colCode) {
                if (str_contains($colCode, 'date')) {
                    $cellValue = (isset($aRow[$colCode]) ? _d($aRow[$colCode]) : '');
                } else if ($colCode == 'stt') {
                    $cellValue = ($key+1);
                } else {
                    $cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
                }

                if ($colCode == 'image') {
                    $imageUrl = 'assets/images/tnh/no_image.png';
                    if (!empty($aRow['image'])) {
                        // $images = 'uploads/products/' . $value['image'];
                        $imageUrl = $aRow['image'];
                    }
                    if (!empty($imageUrl) && file_exists($imageUrl)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($imageUrl);
                        // $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates($cloumns_excel[$colIndex] . $excelRowNum);
    
                        $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, '')->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getRowDimension($excelRowNum)->setRowHeight(30);
                    }
                } else if ($colCode == 'qr') {
                    $code = 'production_order_request||'.$aRow['production_order_request_id'];

                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/production_order_request/';
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
                        $objDrawing1->setCoordinates($cloumns_excel[$colIndex] . $excelRowNum);
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($excelRowNum)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, '')->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
                }
            }
            $excelRowNum++;
        }

        $filename = slug_it(_l('work_norm_group')) . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="$filename"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'result' => 1,
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }
}
