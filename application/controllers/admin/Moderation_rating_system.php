<?php
defined('BASEPATH') or exit('No direct script access allowed');

class moderation_rating_system extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewModerationEvaluate = true;
        $this->preViewOwnModerationEvaluate = true;
        $this->preAddModerationEvaluate = true;
        $this->preEditModerationEvaluate = true;
        $this->preApproveModerationEvaluate = true;
        $this->preDeleteModerationEvaluate = true;
    }
    public function index()
    {
        if (!$this->preViewModerationEvaluate && !$this->preViewOwnModerationEvaluate) {
            access_denied();
        }
        $data['title'] = _l('Kế Hoạch Điều Độ Đánh Đánh Giá Hệ Thống');
        $this->load->view('admin/moderation_rating_system/index', $data);
    }
    public function getModerationEvaluate()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $type = $this->input->post('type');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_rating_system_item.id as id',
            'tbl_suggest_rating_system.date as date',
            'tbl_suggest_rating_system.reference_no as reference_no',
            'tblbranch.name as name_branch',
            'tbl_suggest_rating_system.note as note',
            'tbl_system.code as code',
            'tbl_system.name as name',
            'tbl_suggest_rating_system_item.detail as detail',
            'tbl_suggest_rating_system_item.standard as standard',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_rating_system_item';
        $where = [];
        $filter = [];
        if (!$this->preViewModerationEvaluate) {
            array_push($where, 'AND (tbl_suggest_rating_system.created_by = ' . get_staff_user_id() . ')');
        }
        array_push($where, 'AND (tbl_suggest_rating_system.status = 1)');

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_rating_system.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_rating_system.date <= '" . $end_date_search . "'");
        }
        $join = [
            'INNER JOIN tbl_suggest_rating_system ON tbl_suggest_rating_system.id = tbl_suggest_rating_system_item.suggest_rating_system_id',
            'INNER JOIN tblbranch ON tblbranch.id = tbl_suggest_rating_system.branch_id',
            'INNER JOIN tbl_system ON tbl_system.id = tbl_suggest_rating_system_item.system_id'
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_suggest_rating_system.id as idmain'], 'GROUP BY tbl_suggest_rating_system_item.id', [], '', 'HAVING tbl_suggest_rating_system_item.id > 0');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _d($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['reference_no']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['name_branch']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['note']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['code']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['detail']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['standard']) . '</div>';


            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function exportExcel()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $type = $this->input->post('type');
            $type = $this->input->post('type');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $this->db->select('
                tbl_suggest_rating_system_item.id as id,
                tbl_suggest_rating_system.date as date,
                tbl_suggest_rating_system.reference_no as reference_no,
                tblbranch.name as name_branch,
                tbl_suggest_rating_system.note as note,
                tbl_system.code as code,
                tbl_system.name as name,
                tbl_suggest_rating_system_item.detail as detail,
                tbl_suggest_rating_system_item.standard as standard
            ');
            $this->db->from('tbl_suggest_rating_system_item');
            $this->db->join('tbl_suggest_rating_system', 'tbl_suggest_rating_system.id = tbl_suggest_rating_system_item.suggest_rating_system_id');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_rating_system.branch_id', 'left');
            $this->db->join('tbl_system', 'tbl_system.id = tbl_suggest_rating_system_item.system_id');

            if (!$this->preViewModerationEvaluate) {
                $this->db->where('(tbl_suggest_rating_system.created_by = ' . get_staff_user_id() . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_rating_system.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_rating_system.date <= '" . $end_date_search . "'");
            }
            $this->db->where('(tbl_suggest_rating_system.status = 1)');
            if (!empty($type)) {
                $this->db->where('(tbl_suggest_rating_system.object_type = "' . $type . '")');
            }

            $this->db->order_by('tbl_suggest_rating_system.id desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
                "%0" . $decimals_number . "s",
                0
            ) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);

            $title = _l('Kế Hoạch Điều Độ Đánh Đánh Giá Hệ Thống');

            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ($title)
            )->getStyle("A1")->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Số phiếu yêu cầu');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Chi nhánh')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Ghi chú')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Mã Hệ Thống')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Tên Hệ Thống')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Chi tiết Hệ Thống')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Tiêu chuẩn/ quy định')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
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
            $rowBegin = $sttRow;

            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['reference_no']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['name_branch'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['note'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['code'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['name'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['detail'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", ($value['standard']))->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                }
            }

            $filename = lang('phieu_ke_hoach_dieu_do') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
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