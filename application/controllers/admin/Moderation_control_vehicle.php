<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_control_vehicle extends AdminController
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

        $this->preViewModerationControlVehicle = true;
        $this->preViewOwnModerationControlVehicle = true;
        $this->preEditModerationControlVehicle = true;
    }

    public function index()
    {
        if (!$this->preViewModerationControlVehicle && !$this->preViewOwnModerationControlVehicle) {
            access_denied();
        }
        $data['title'] = lang('dt_moderation_plan_stage_dieu_xe');
        $this->load->view('admin/moderation_control_vehicle/index', $data);
    }

    public function getModerationPlanStagesDieuXe()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');


        $aColumns = [
            'tbl_request_control_vehicle_bussiness.id as id',
            'tbl_request_control_vehicle_bussiness.reference_no as reference_no',
            'tbl_request_control_vehicle_bussiness.date as date',
            'tbl_list_vehicle.code_vehicle as vehicle_name',
            'CONCAT(employees.firstname," ",employees.lastname) as fullname',
            'IF(
				tbl_request_control_vehicle_bussiness.object_type = "delivery",
					(
						SELECT GROUP_CONCAT(tbl_deliveries.reference_no SEPARATOR "<br/>")
						FROM tbl_deliveries WHERE FIND_IN_SET(tbl_deliveries.id, tbl_request_control_vehicle_bussiness.object_id)
					),
					(
						IF(tbl_request_control_vehicle_bussiness.object_type = "purchase_order",
							(
								SELECT GROUP_CONCAT(CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code) SEPARATOR "<br/>")
								FROM tblpurchase_order WHERE FIND_IN_SET(tblpurchase_order.id, tbl_request_control_vehicle_bussiness.object_id)
							),
							IF(tbl_request_control_vehicle_bussiness.object_type = "request_bussiness",
								(
									SELECT GROUP_CONCAT(tbl_request_bussiness.reference_no SEPARATOR "<br/>")
									FROM tbl_request_bussiness WHERE tbl_request_bussiness.id = cast(tbl_request_control_vehicle_bussiness.object_id as int)
								),
								IF(
									tbl_request_control_vehicle_bussiness.object_type = "suggest_outsource", (
										SELECT GROUP_CONCAT(tbl_suggest_outsource.reference_no SEPARATOR "<br/>")
										FROM tbl_suggest_outsource WHERE tbl_suggest_outsource.id = cast(tbl_request_control_vehicle_bussiness.object_id as int)
									),
									IF(tbl_request_control_vehicle_bussiness.object_type = "other", tbl_request_control_vehicle_bussiness.object_id, "")
								)
							)
						)
					)
			) as reference_no_bussiness',
            'tbl_request_control_vehicle_bussiness.type_vehicle as type_vehicle',
            'tbl_request_control_vehicle_bussiness.number_km as number_km',
            'tbl_request_control_vehicle_bussiness.quota_gasoline as quota_gasoline',
            'tbl_request_control_vehicle_bussiness.cost_tolls as cost_tolls',
            'tbl_request_control_vehicle_bussiness.price as price',
            'tbl_request_control_vehicle_bussiness.amount as amount',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_control_vehicle_bussiness';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tblstaff employees ON employees.staffid = tbl_request_control_vehicle_bussiness.staff_id',
            'LEFT JOIN tbl_list_vehicle ON tbl_list_vehicle.id = tbl_request_control_vehicle_bussiness.vehicle_name',
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_control_vehicle_bussiness.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_control_vehicle_bussiness.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_request_control_vehicle_bussiness.object_type'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $object_type = '';
            if(!empty($aRow['object_type'])) {
                $object_type= _l('object_type_' . $aRow['object_type']).'';
            }

            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_control_vehicle_bussiness/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['vehicle_name']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['fullname']) . '</div>';
            $row[] = '<div class="text-left">' .$object_type. '</div>';
            $row[] = '<div class="text-left">' . $aRow['reference_no_bussiness'] . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['type_vehicle']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['number_km']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['quota_gasoline']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['cost_tolls']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['price']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['amount']) . '</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['id'],$vv['id'],$sTable);
                $row[] = '<div class="text-center">'.$_data.'</div>';
            }

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function updateModerationControlVehicle()
    {
        $data = [];
        if (!$this->preEditModerationControlVehicle) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $suggest_control_vehicle_id = $this->input->post('suggest_control_vehicle_id');
        $suggest_control_vehicle_item_id = $this->input->post('suggest_control_vehicle_item_id');
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

        $this->db->from('tbl_moderation_control_vehicle');
        $this->db->where('tbl_moderation_control_vehicle.suggest_control_vehicle', $suggest_control_vehicle_id);
        $this->db->where('tbl_moderation_control_vehicle.suggest_control_vehicle_item_id', $suggest_control_vehicle_item_id);
        $dtData = $this->db->get()->row_array();
        if (!empty($dtData)) {
            $this->db->where('tbl_moderation_control_vehicle.id', $dtData['id']);
            $success = $this->db->update('tbl_moderation_control_vehicle', [
                $name => $value
            ]);
        } else {
            $success = $this->db->insert('tbl_moderation_control_vehicle', [
                'suggest_control_vehicle' => $suggest_control_vehicle_id,
                'suggest_control_vehicle_item_id' => $suggest_control_vehicle_item_id,
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
            $inputFileName = 'uploads/import_dt/phieu_dieu_do_dieu_xe.xlsx';
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

            $tb_category_client = "(
                SELECT
                   tblcustomer_groups.customer_id,
                   GROUP_CONCAT(tblcustomers_groups.name) as name_group     
                FROM tblcustomer_groups
                JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                GROUP BY tblcustomer_groups.customer_id
            ) tb_category_client";

            $this->db->select('
                tbl_request_control_vehicle_bussiness.id as id,
                tbl_request_control_vehicle_bussiness.reference_no as reference_no,
                tbl_request_control_vehicle_bussiness.date as date,
                tbl_list_vehicle.code_vehicle as vehicle_name,
                CONCAT(employees.firstname," ",employees.lastname) as fullname,
                IF(
                    tbl_request_control_vehicle_bussiness.object_type = "delivery",
                        (
                        SELECT GROUP_CONCAT(tbl_deliveries.reference_no SEPARATOR "\n")
                            FROM tbl_deliveries WHERE FIND_IN_SET(tbl_deliveries.id, tbl_request_control_vehicle_bussiness.object_id)
                        ),
                        (
                            IF(tbl_request_control_vehicle_bussiness.object_type = "purchase_order",
                                (
                                SELECT GROUP_CONCAT(CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code) SEPARATOR "\n")
                                    FROM tblpurchase_order WHERE FIND_IN_SET(tblpurchase_order.id, tbl_request_control_vehicle_bussiness.object_id)
                                ),
                                IF(tbl_request_control_vehicle_bussiness.object_type = "request_bussiness",
                                    (
                                    SELECT GROUP_CONCAT(tbl_request_bussiness.reference_no SEPARATOR "\n")
                                        FROM tbl_request_bussiness WHERE tbl_request_bussiness.id = cast(tbl_request_control_vehicle_bussiness.object_id as int)
                                    ),
                                    IF(
                                        tbl_request_control_vehicle_bussiness.object_type = "suggest_outsource", (
                SELECT GROUP_CONCAT(tbl_suggest_outsource.reference_no SEPARATOR "\n")
                                            FROM tbl_suggest_outsource WHERE tbl_suggest_outsource.id = cast(tbl_request_control_vehicle_bussiness.object_id as int)
                                        ),
                                        IF(tbl_request_control_vehicle_bussiness.object_type = "other", tbl_request_control_vehicle_bussiness.object_id, "")
                                    )
                                )
                            )
                        )
                ) as reference_no_bussiness,
                tbl_request_control_vehicle_bussiness.type_vehicle as type_vehicle,
                tbl_request_control_vehicle_bussiness.number_km as number_km,
                tbl_request_control_vehicle_bussiness.quota_gasoline as quota_gasoline,
                tbl_request_control_vehicle_bussiness.cost_tolls as cost_tolls,
                tbl_request_control_vehicle_bussiness.price as price,
                tbl_request_control_vehicle_bussiness.amount as amount,
                tbl_request_control_vehicle_bussiness.object_type
            ');
            $this->db->from('tbl_request_control_vehicle_bussiness');
            $this->db->join('tblstaff employees', 'employees.staffid = tbl_request_control_vehicle_bussiness.staff_id', 'inner');
            $this->db->join('tbl_list_vehicle', 'tbl_list_vehicle.id = tbl_request_control_vehicle_bussiness.vehicle_name', 'left');

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_control_vehicle_bussiness.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_control_vehicle_bussiness.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_request_control_vehicle_bussiness.id desc');
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
                    $object_type = '';
                    if(!empty($value['object_type'])) {
                        $object_type= _l('object_type_' . $value['object_type']).'';
                    }
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", (++$key));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['reference_no'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", _dt($value['date']));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['vehicle_name']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['fullname'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $object_type)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['reference_no_bussiness']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $value['type_vehicle'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",$value['number_km'])->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",($value['quota_gasoline']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",($value['cost_tolls']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",formatNumber($value['price']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",formatNumber($value['amount']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModeration($value['id'],$vv['id'],'tbl_request_control_vehicle_bussiness','',true);
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
            $filename = lang('phieu_dieu_do_cong_dien_dieu_xe') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
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