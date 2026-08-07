<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_evaluate extends AdminController
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
        $this->type = 0;
        if (!empty($this->input->get('type'))) {
            $this->type = $this->input->get('type');
        }
    }
    public function index()
    {
        if (!$this->preViewModerationEvaluate && !$this->preViewOwnModerationEvaluate) {
            access_denied();
        }
        if ($this->type == 'customer') {
            $data['title'] = _l('Kế Hoạch Điều Độ Đánh Giá Khách Hàng');
        } elseif ($this->type == 'supplier') {
            $data['title'] = _l('Kế Hoạch Điều Độ Đánh Giá Nhà Cung Cấp');
        } elseif ($this->type == 'quality') {
            $data['title'] = _l('Kế Hoạch Điều Độ Đánh Kiểm Tra Chất Lượng');
        } elseif ($this->type == 'procedure') {
            $data['title'] = _l('Kế Hoạch Điều Độ Đánh Đánh Giá Quy Trình');
        } elseif ($this->type == 'system') {
            $data['title'] = _l('Kế Hoạch Điều Độ Đánh Đánh Giá Hệ Thống');
        } else {
            $data['title'] = _l('Kế Hoạch Điều Độ');
        }
        $this->load->view('admin/moderation_evaluate/index', $data);
    }
    public function getModerationEvaluate()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $type = $this->input->post('type');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_evaluate_item.id as id',
            'tbl_suggest_evaluate.date as date',
            'tbl_suggest_evaluate.reference_no as reference_no',
            'CONCAT(firstname," ",lastname) as fullname',
            'tblbranch.name as name_branch',
            'tbl_suggest_evaluate.note as note',
            'tbl_evaluate.code_evaluate as code_evaluate',
            'tbl_suggest_evaluate_item.content as content',
            'tbl_suggest_evaluate_item.actual_situation as actual_situation',
            'tbl_suggest_evaluate_item.standard as standard',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_evaluate_item';
        $where = [];
        $filter = [];
        if (!$this->preViewModerationEvaluate) {
            array_push($where, 'AND (tbl_suggest_evaluate.created_by = ' . get_staff_user_id() . ')');
        }
        array_push($where, 'AND (tbl_suggest_evaluate.status = 1)');
        array_push($where, 'AND (tbl_suggest_evaluate.object_type = "' . $type . '")');

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_evaluate.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_evaluate.date <= '" . $end_date_search . "'");
        }
        $join = [
            'INNER JOIN tbl_suggest_evaluate ON tbl_suggest_evaluate.id = tbl_suggest_evaluate_item.suggest_plan_evaluate_id',
            'INNER JOIN tbl_category_evaluate_detail ON tbl_category_evaluate_detail.id = tbl_suggest_evaluate_item.category_evaluate_id',
            'LEFT JOIN tblclients ON tblclients.userid = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="customer"',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="supplier"',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_evaluate.staff_evaluate',
            'INNER JOIN tblbranch ON tblbranch.id = tbl_suggest_evaluate.branch_id',
            'INNER JOIN tbl_evaluate ON tbl_evaluate.id = tbl_suggest_evaluate_item.evaluate_id',
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_suggest_evaluate.id as idmain', 'tbl_suggest_evaluate.object_type', 'tblclients.company as name_client', 'tblsuppliers.company as name_supplier',], 'GROUP BY tbl_suggest_evaluate_item.id', [], '', 'HAVING tbl_suggest_evaluate_item.id > 0');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _d($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['reference_no']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['fullname']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['name_branch']) . '</div>';
            $object = '';
            if ($aRow['object_type'] == 'customer') {
                $object = $aRow['name_client'];
            } elseif ($aRow['object_type'] == 'supplier') {
                $object = $aRow['name_supplier'];
            }
            $row[] = '<div class="text-center" style="width: 110px">' . $object . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['note']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['code_evaluate']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['content']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['actual_situation']) . '</div>';
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
                tbl_suggest_evaluate_item.id as id,
                tbl_suggest_evaluate.date as date,
                tbl_suggest_evaluate.reference_no as reference_no,
                CONCAT(firstname," ",lastname) as fullname,
                tblbranch.name as name_branch,
                tbl_suggest_evaluate.note as note,
                tbl_evaluate.code_evaluate as code_evaluate,
                tbl_suggest_evaluate_item.content as content,
                tbl_suggest_evaluate_item.actual_situation as actual_situation,
                tbl_suggest_evaluate_item.standard as standard,
                tbl_suggest_evaluate.object_type as object_type,
                tblclients.company as name_client, 
                tblsuppliers.company as name_supplier
            ');
            $this->db->from('tbl_suggest_evaluate_item');
            $this->db->join('tbl_suggest_evaluate', 'tbl_suggest_evaluate.id = tbl_suggest_evaluate_item.suggest_plan_evaluate_id');
            $this->db->join('tbl_category_evaluate_detail', 'tbl_category_evaluate_detail.id = tbl_suggest_evaluate_item.category_evaluate_id');
            $this->db->join('tblclients', 'tblclients.userid = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="customer"', 'inner');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="supplier"', 'left');
            $this->db->join('tblstaff', 'tblstaff.staffid = tbl_suggest_evaluate.staff_evaluate', 'left');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_evaluate.branch_id', 'left');
            $this->db->join('tbl_evaluate', 'tbl_evaluate.id = tbl_suggest_evaluate_item.evaluate_id', 'left');

            if (!$this->preViewModerationEvaluate) {
                $this->db->where('(tbl_suggest_evaluate.created_by = ' . get_staff_user_id() . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_evaluate.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_evaluate.date <= '" . $end_date_search . "'");
            }
            $this->db->where('(tbl_suggest_evaluate.status = 1)');
            if (!empty($type)) {
                $this->db->where('(tbl_suggest_evaluate.object_type = "' . $type . '")');
            }

            $this->db->order_by('tbl_suggest_evaluate.id desc');
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

            if ($type == 'customer') {
                $title = _l('Kế Hoạch Điều Độ Đánh Giá Khách Hàng');
            } elseif ($type == 'supplier') {
                $title = _l('Kế Hoạch Điều Độ Đánh Giá Nhà Cung Cấp');
            } elseif ($type == 'quality') {
                $title = _l('Kế Hoạch Điều Độ Đánh Kiểm Tra Chất Lượng');
            } elseif ($type == 'procedure') {
                $title = _l('Kế Hoạch Điều Độ Đánh Đánh Giá Quy Trình');
            } elseif ($type == 'system') {
                $title = _l('Kế Hoạch Điều Độ Đánh Đánh Giá Hệ Thống');
            } else {
                $title = _l('Kế Hoạch Điều Độ');
            }
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
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Mã phiếu đánh giá');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Người đánh giá')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Chi nhánh')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Đối tượng')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Ghi chú')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Mã đánh giá')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Chi tiết đánh giá')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Hiện trạng thực tế')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Tiêu chuẩn/ quy định')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:K$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['fullname'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['name_branch'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['note'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $object = '';
                    if ($value['object_type'] == 'customer') {
                        $object = $value['name_client'];
                    } elseif ($value['object_type'] == 'supplier') {
                        $object = $value['name_supplier'];
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $object)->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['code_evaluate'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", ($value['content']))->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", ($value['actual_situation']))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", ($value['standard']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
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
