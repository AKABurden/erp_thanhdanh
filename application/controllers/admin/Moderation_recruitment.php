<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_recruitment extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('manufactures_model');
        $this->load->model('business_plan_model');
        $this->load->model('orders_model');
        $this->load->model('tools_supplies_model');

        $this->preViewModerationRecruitment = true;
        $this->preViewOwnModerationRecruitment = true;
        $this->preEditModerationRecruitment = true;
    }

    public function index()
    {
        if (!$this->preViewModerationRecruitment && !$this->preViewOwnModerationRecruitment) {
            access_denied();
        }
        $data['title'] = lang('dt_moderation_recruitment');
        $this->load->view('admin/moderation_recruitment/index', $data);
    }

    public function getModerationRecruitments()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');


        $aColumns = [
            'tbl_suggest_recruitment.id as id',
            'tbl_suggest_recruitment.date as date',
            'tbl_suggest_recruitment.reference_no as reference_no',
            'tbl_suggest_recruitment.position_recruitment as position_recruitment',
            'tbl_suggest_recruitment.content_work as content_work',
            'tbl_suggest_recruitment.kpis as kpis',
            'tbl_suggest_recruitment.note as note',
            'tbl_suggest_recruitment.quantity as quantity',
            'tbl_suggest_recruitment.time_work as time_work',
            'tbl_suggest_recruitment.gender as gender',
            'tbl_suggest_recruitment.completion_time_limit as completion_time_limit',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_recruitment';
        $where = [

        ];
        $filter = [];

        $join = [
            'LEFT JOIN tbl_moderation_recruitment ON tbl_moderation_recruitment.suggest_recruitment_id = tbl_suggest_recruitment.id',
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_recruitment.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_recruitment.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_moderation_recruitment.time_expected',
            'tbl_moderation_recruitment.date_start',
            'tbl_moderation_recruitment.date_end',
            'tbl_suggest_recruitment.standard',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $suggest_recruitment_id = $aRow['id'];
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_recruitment/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . ($aRow['reference_no']) . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['position_recruitment']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['content_work']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['kpis']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['note']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['quantity']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['time_work']) . '</div>';
            if ($aRow['gender'] == "male" ){
                $htmlGender = 'Nam';
            } elseif ($aRow['gender'] == "female" ){
                $htmlGender = 'Nữ';
            } elseif ($aRow['gender'] == "other" ){
                $htmlGender = 'Khác';
            }
            $row[] = '<div class="text-center">' . $htmlGender . '</div>';
//            $row[] = '<div class="text-left">' . $aRow['completion_time_limit'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['standard'] . '</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['id'],$vv['id'],'tbl_suggest_recruitment');
                $row[] = '<div class="text-center">'.$_data.'</div>';
            }

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function updateModerationRecruitment()
    {
        $data = [];
        if (!$this->preEditModerationRecruitment) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $suggest_recruitment_id = $this->input->post('suggest_recruitment_id');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        if ($name == 'date_start' || $name == 'date_end') {
            if (!empty($value)) {
                $value = to_sql_date($value, true);
            } else {
                $value = null;
            }
        } elseif ($name == 'time_expected') {
            if (!empty($value)) {
                $value = number_unformat($value);
            } else {
                $value = 0;
            }
        }

        $this->db->from('tbl_moderation_recruitment');
        $this->db->where('tbl_moderation_recruitment.suggest_recruitment_id', $suggest_recruitment_id);
        $dtData = $this->db->get()->row_array();
        if (!empty($dtData)) {
            $this->db->where('tbl_moderation_recruitment.id', $dtData['id']);
            $success = $this->db->update('tbl_moderation_recruitment', [
                $name => $value
            ]);
        } else {
            $success = $this->db->insert('tbl_moderation_recruitment', [
                'suggest_recruitment_id' => $suggest_recruitment_id,
                $name => $value
            ]);
        }
        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }

    public function exportExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_dt/phieu_dieu_do_cong_viec_tuyen_dung.xlsx';
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $highestRow         = $objWorksheet->getHighestRow();
            $i = $highestColumnIndex;

            $BStyleCenter = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'font' => array(
                    'bold' => true,
                    'size' => 11,
                    'name' => 'Times New Roman',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ),
            );
            foreach (getListColumTable() as $kk => $vv) {
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$highestRow,
                    $vv['name'])->getStyle("$cloumns_excel[$i]$highestRow")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $i ++;
            }

            $this->db->select('
                tbl_suggest_recruitment.id as id,
                tbl_suggest_recruitment.date as date,
                tbl_suggest_recruitment.reference_no as reference_no,
                tbl_suggest_recruitment.position_recruitment as position_recruitment,
                tbl_suggest_recruitment.content_work as content_work,
                tbl_suggest_recruitment.kpis as kpis,
                tbl_suggest_recruitment.note as note,
                tbl_suggest_recruitment.quantity as quantity,
                tbl_suggest_recruitment.time_work as time_work,
                tbl_suggest_recruitment.gender as gender,
                tbl_suggest_recruitment.completion_time_limit as completion_time_limit,
                tbl_moderation_recruitment.time_expected,
                tbl_moderation_recruitment.date_start,
                tbl_moderation_recruitment.date_end,
                tbl_suggest_recruitment.standard,
            ');
            $this->db->from('tbl_suggest_recruitment');
            $this->db->join('tbl_moderation_recruitment', 'tbl_moderation_recruitment.suggest_recruitment_id = tbl_suggest_recruitment.id', 'left');

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_recruitment.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_recruitment.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_recruitment.id desc');
            $dtData = $this->db->get()->result_array();

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $rowBegin = 2;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $colStt = 0;
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", (++$key));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", _dt($value['date']));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['reference_no']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['position_recruitment']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['content_work']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['kpis']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['note'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['quantity'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['time_work'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    if ($value['gender'] == "male" ){
                        $htmlGender = 'Nam';
                    } elseif ($value['gender'] == "female" ){
                        $htmlGender = 'Nữ';
                    } elseif ($value['gender'] == "other" ){
                        $htmlGender = 'Khác';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $htmlGender)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['standard']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);

                    $colStt++;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModeration($value['id'],$vv['id'],'tbl_suggest_recruitment','',true);
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$_data)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        if ($kk != (count(getListColumTable())) - 1) {
                            $colStt++;
                        }
                    }
                    $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[0]$rowBegin:$cloumns_excel[$colStt]$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        ),
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_dieu_do_cong_viec_tuyen_dung') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
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