<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Infomation_machines extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('orders_model');
        $this->load->model('tools_supplies_model');

        $this->preViewInfomationMachines = true;
        $this->preViewOwnInfomationMachines = true;
        $this->preEditInfomationMachines  = true;
    }

    public function index()
    {
        if (!$this->preViewInfomationMachines && !$this->preViewOwnInfomationMachines) {
            access_denied();
        }
        $data['title'] = lang('dt_infomation_machines');
        $this->load->view('admin/infomation_machines/index', $data);
    }

    public function getInfomationMachines()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');


        $aColumns = [
            'tbl_machines.id as id',
            'tbl_category_machines.code as code_category_machines',
            'tbl_category_machines.name as name_category_machines',
            'tbl_category_machines.code_species as code_species',
            'tbl_category_machines.name_species as name_species',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tbl_packaging.name as name_standard',
            'tbl_machines.quota_productivity as quota_productivity',
            'tbl_machines.soup_ingredients as soup_ingredients'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_machines';
        $where = [

        ];
        $filter = [];

        $join = [
            'LEFT JOIN tbl_category_machines ON tbl_category_machines.id = tbl_machines.category_machine_id',
            'LEFT JOIN tbl_packaging ON tbl_packaging.id = tbl_machines.standard'
        ];



        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $machines_id = $aRow['id'];
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 120px">'.$aRow['code_category_machines'].'</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($aRow['name_category_machines']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($aRow['code_species']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($aRow['name_species']) . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 100px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 100px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($aRow['code_machines']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($aRow['name_machines']) . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left"  style="min-width: 100px">' . ($aRow['name_standard']) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['quota_productivity'] . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . $aRow['soup_ingredients'] . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateInfomationMachines(this,'. $machines_id .',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function updateModeration()
    {
        $data = [];
        if (!$this->preEditModerationOvertime) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $suggest_overtime_id = $this->input->post('suggest_overtime_id');
        $suggest_overtime_item_id = $this->input->post('suggest_overtime_item_id');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        if ($name == 'date') {
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

        $this->db->from('tbl_moderation_overtime');
        $this->db->where('tbl_moderation_overtime.suggest_overtime_id', $suggest_overtime_id);
        $this->db->where('tbl_moderation_overtime.suggest_overtime_item_id', $suggest_overtime_item_id);
        $dtData = $this->db->get()->row_array();
        if (!empty($dtData)) {
            $this->db->where('tbl_moderation_overtime.id', $dtData['id']);
            $success = $this->db->update('tbl_moderation_overtime', [
                $name => $value
            ]);
        } else {
            $success = $this->db->insert('tbl_moderation_overtime', [
                'suggest_overtime_id' => $suggest_overtime_id,
                'suggest_overtime_item_id' => $suggest_overtime_item_id,
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
            $inputFileName = 'uploads/import_dt/phieu_dieu_do_cong_viec_tang_ca.xlsx';
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

            $this->db->select('
                tbl_request_overtime.id as id,
                tbl_request_overtime.reference_no as reference_no,
                tbl_request_overtime.date as date,
                tbl_productions_orders.reference_no as reference_no_po,
                tbl_request_overtime_item.quantity as quantity,
                tbl_request_overtime_item.category_overtime as category_overtime,
                tbl_request_overtime_item.quota_productivity as quota_productivity,
                tbl_moderation_overtime.time_expected as time_expected,
                tbl_request_overtime_item.quantity_overtime as quantity_overtime,
                tbl_moderation_overtime.date as date_overtime,
                tbl_moderation_overtime.hour_start as hour_start,
                tbl_moderation_overtime.hour_end as hour_end,
                tbl_result.name as name_result,
                tbl_request_overtime_item.coefficient as coefficient,
                (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                     FROM tblproduction_report
                     WHERE tblproduction_report.object_id = tbl_request_overtime.id AND tblproduction_report.object_type = "request_outsource"
                ) as name_report,
                tbl_request_overtime_item.salary as salary,
                tbl_request_overtime.staff_plan as staff_plan,
                tbl_request_overtime_item.staff_id as staff_id,
                tbl_request_overtime_item.status as status,
                tbl_request_overtime_item.standard as standard,
                tbl_request_overtime_item.item_id as item_id,
                tbl_request_overtime_item.type_item as type_item,
            ');
            $this->db->from('tbl_request_overtime');
            $this->db->join('tbl_request_overtime_item', 'tbl_request_overtime_item.suggest_request_id = tbl_request_overtime.id', 'inner');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_request_overtime.po_id', 'inner');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_request_overtime_item.result_id', 'left');
            $this->db->join('tbl_moderation_overtime', 'tbl_moderation_overtime.suggest_overtime_id = tbl_request_overtime.id AND tbl_moderation_overtime.suggest_overtime_item_id = tbl_request_overtime_item.id', 'left');

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_overtime.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_overtime.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_request_overtime.id desc');
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
                    $item_id = $value['item_id'];
                    $type_item = $value['type_item'];
                    $info = null;
                    $dtCategory = null;
                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($item_id);
                        $dtCategory = get_table_where('tbl_category_products',['id' => $info['category_id']],'','row_array');
                    }
                    $colStt = 0;
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", (++$key));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['reference_no']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", _dt($value['date']));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['reference_no_po']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($info['code']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($info['name']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($dtCategory['name']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['quantity'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity']));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['category_overtime'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['quota_productivity'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['time_expected'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",!empty($value['date_overtime']) ? _dhau($value['date_overtime']) : '')->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['quantity_overtime'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['hour_start'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['hour_end'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['name_result'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['coefficient'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['name_report'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['salary'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['salary']));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",get_staff_full_name($value['staff_plan']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",get_staff_full_name($value['staff_id']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $htmlStatus = '';
                    if ($value['status'] == 0){
                        $htmlStatus = 'Chưa duyệt';
                    } else {
                        $htmlStatus = 'Đã duyệt';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$htmlStatus)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['standard'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);

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
            $filename = lang('phieu_dieu_do_cong_viec_tang_ca') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);;
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