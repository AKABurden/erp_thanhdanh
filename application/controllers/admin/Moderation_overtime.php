<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_overtime extends AdminController
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

        $this->preViewModerationOvertime = true;
        $this->preViewOwnModerationOvertime = true;
        $this->preEditModerationOvertime  = true;
    }

    public function index()
    {
        if (!$this->preViewModerationOvertime && !$this->preViewOwnModerationOutsource) {
            access_denied();
        }
        $data['title'] = lang('dt_moderation_overtime');
        $this->load->view('admin/moderation_overtime/index', $data);
    }

    public function getModerationOvertimes()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');


        $aColumns = [
            'tbl_request_overtime.id as id',
            'tbl_request_overtime.reference_no as reference_no',
            'tbl_request_overtime.date as date',
            'tbl_request_overtime_item.quantity as quantity',
            'tbl_request_overtime_item.category_overtime as category_overtime',
            'tbl_request_overtime_item.quota_productivity as quota_productivity',
            'tbl_moderation_overtime.time_expected as time_expected',
            'tbl_request_overtime_item.quantity_overtime as quantity_overtime',
            'tbl_moderation_overtime.date as date_overtime',
            'tbl_moderation_overtime.hour_start as hour_start',
            'tbl_moderation_overtime.hour_end as hour_end',
            'tbl_result.name as name_result',
            'tbl_request_overtime_item.coefficient as coefficient',
            '(SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_request_overtime.id AND tblproduction_report.object_type = "request_outsource"
            ) as name_report',
            'tbl_request_overtime_item.salary as salary',
            'tbl_request_overtime.staff_plan as staff_plan',
            'tbl_request_overtime_item.staff_id as staff_id',
            'tbl_request_overtime_item.status as status',
            'tbl_request_overtime_item.standard as standard',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_overtime';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_request_overtime_item ON tbl_request_overtime_item.suggest_request_id = tbl_request_overtime.id',
            'LEFT JOIN tbl_result ON tbl_result.id = tbl_request_overtime_item.result_id',
            'LEFT JOIN tbl_moderation_overtime ON tbl_moderation_overtime.suggest_overtime_id = tbl_request_overtime.id AND tbl_moderation_overtime.suggest_overtime_item_id = tbl_request_overtime_item.id',
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_overtime.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_overtime.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_request_overtime_item.id as request_overtime_item_id',
            'tbl_request_overtime_item.item_id as item_id',
            'tbl_request_overtime_item.type_item as type_item',
            'tbl_request_overtime_item.order_id as order_id',
            'tbl_request_overtime.type_object'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $request_overtime_id = $aRow['id'];
            $request_overtime_item_id = $aRow['request_overtime_item_id'];
            $item_id = $aRow['item_id'];
            $type_item = $aRow['type_item'];
            $info = null;
            $dtCategory = null;
            if ($type_item == "products") {
                $info = $this->products_model->rowProduct($item_id);
                $dtCategory = get_table_where('tbl_category_products',['id' => $info['category_id']],'','row_array');
            }
            if($aRow['type_object'] == 'orders') {
                $this->db->where('id', $aRow['order_id']);
                $dt_orders = $this->db->get('tbl_orders')->row();
                $list_reference_no = $dt_orders->reference_no;
            } else {
                $this->db->where('id', $aRow['order_id']);
                $dt_productions_orders = $this->db->get('tbl_productions_orders')->row();
                $list_reference_no = $dt_productions_orders->reference_no;
            }
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_overtime/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . ($aRow['reference_no']) . '</a></div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . _dt($aRow['date']) . '</div>';
            if ($aRow['type_object'] == 'orders'){
                $row[] = '<div class="text-left" style="min-width: 100px"><a class="tnh-modal" href="' . base_url('admin/orders/view_order/' . $aRow['order_id']) . '">' . $list_reference_no . '</a></div>';
            } else {
                $row[] = '<div class="text-left" style="min-width: 100px"><a target="_blank" href="' . base_url('admin/manufactures/view_productions_orders/' . $aRow['order_id']) . '">' . $list_reference_no . '</a></div>';
            }
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($info['code']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($info['name']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">' . ($dtCategory['name']) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity']) . '</div>';
            $row[] = '<div class="text-left"  style="min-width: 100px">' . ($aRow['category_overtime']) . '</div>';
            $row[] = '<div class="text-center">' . $aRow['quota_productivity'] . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModeration(this,'. $request_overtime_id .','.$request_overtime_item_id.',\'time_expected\')" name="time_expected" class="form-control time_expected number-format" autocomplete="off" value="' . (!empty($aRow['time_expected']) ? ($aRow['time_expected']) : '') . '">
            </div>';
            $row[] = '<div class="text-center" style="min-width: 50px">' . $aRow['quantity_overtime'] . '</div>';

            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModeration(this,' . $request_overtime_id .','.$request_overtime_item_id.',\'date\')" name="date" class="form-control date datepicker" autocomplete="off" value="' . (!empty($aRow['date_overtime']) ? _dhau($aRow['date_overtime']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="time" style="width: 130px;" onchange="updateModeration(this,' . $request_overtime_id .','.$request_overtime_item_id.',\'hour_start\')" name="hour_start" class="form-control hour_start" autocomplete="off" value="' . (!empty($aRow['hour_start']) ? $aRow['hour_start'] : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="time" style="width: 130px;" onchange="updateModeration(this,' . $request_overtime_id . ','.$request_overtime_item_id.',\'hour_end\')" name="hour_end" class="form-control hour_end" autocomplete="off" value="' . (!empty($aRow['hour_end']) ? $aRow['hour_end'] : '') . '">
            </div>';
            $row[] = '<div class="text-left" style="min-width: 100px">'.$aRow['name_result'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['coefficient'].'</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">'.$aRow['name_report'].'</div>';
            $row[] = '<div class="text-right" style="min-width: 100px">'.formatMoney($aRow['salary']).'</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">'.get_staff_full_name($aRow['staff_plan']).'</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">'.get_staff_full_name($aRow['staff_id']).'</div>';
            $htmlStatus = '';
            if ($aRow['status'] == 0){
                $htmlStatus = 'Chưa duyệt';
            } else {
                $htmlStatus = 'Đã duyệt';
            }
            $row[] = '<div class="text-left" style="min-width: 100px">'.$htmlStatus.'</div>';
            $row[] = '<div class="text-left" style="min-width: 100px">'.($aRow['standard']).'</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['id'],$vv['id'],'tbl_request_overtime');
                $row[] = '<div class="text-center">'.$_data.'</div>';
            }

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
            $highestColumn = $objWorksheet->getHighestDataColumn();
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $highestRow         = $objWorksheet->getHighestDataRow();
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
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$highestRow, $vv['name'])->getStyle("$cloumns_excel[$i]$highestRow")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $i ++;
            }
            $this->db->select('
                tbl_request_overtime.id as id,
                tbl_request_overtime.reference_no as reference_no,
                tbl_request_overtime.date as date,
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
                tbl_request_overtime_item.order_id as order_id,
                tbl_request_overtime.type_object
            ');
            $this->db->from('tbl_request_overtime');
            $this->db->join('tbl_request_overtime_item', 'tbl_request_overtime_item.suggest_request_id = tbl_request_overtime.id', 'inner');
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
                    if($value['type_object'] == 'orders') {
                        $this->db->where('id', $value['order_id']);
                        $dt_orders = $this->db->get('tbl_orders')->row();
                        $list_reference_no = $dt_orders->reference_no;
                    } else {
                        $this->db->where('id', $value['order_id']);
                        $dt_productions_orders = $this->db->get('tbl_productions_orders')->row();
                        $list_reference_no = $dt_productions_orders->reference_no;
                    }
                    $colStt = 0;
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", (++$key));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['reference_no']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", _dt($value['date']));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $list_reference_no)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
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
                    $colStt ++;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModeration($value['id'],$vv['id'],'tbl_request_overtime','',true);
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