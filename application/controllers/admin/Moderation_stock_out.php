<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_stock_out extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewModerationStockOut = true;
        $this->preViewOwnModerationStockOut = true;
        $this->preAddModerationStockOut = true;
        $this->preEditModerationStockOut = true;
        $this->preApproveModerationStockOut = true;
        $this->preDeleteModerationStockOut = true;
    }

    public function index()
    {
        if (!$this->preViewModerationStockOut && !$this->preViewOwnModerationStockOut) {
            access_denied();
        }
        $data['title'] = _l('ch_moderation_stock_out');
        $this->load->view('admin/moderation_stock_out/index', $data);
    }
    public function getModerationStockOut()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $warehouses = '
            (Select
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.id_items = tbl_materials.id 
                AND tblwarehouse_items.type_items = "nvl" 
                AND tblwarehouse_items.product_quantity > 0
                AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
        ';
        $aColumns = [
            'tblstock_out_request_item.id as id',
            'tblstock_out_request.date as date',
            'tblstock_out_request.code as reference_no',
            'tbl_productions_orders.reference_no as production_order_code',
            'tbl_orders.reference_no as order_code',
            'tbl_materials.name_supplier as supplier_name',
            'tbl_materials.code as item_code',
            'tbl_materials.name as item_name',
            'tbl_category_items.name as category_name',
            'tbl_species.name as specie_name',
            'tblstock_out_request_item.production_quantity as production_quantity',
            $warehouses . ' as stock_quantity',
            'tblstock_out_request_item.production_require_quantity as production_require_quantity',
            '"" as stock_quantity_max',
            'tblstock_out_request_item.purchase_require_quantity as purchase_require_quantity',
            'tbl_materials.height as height',
            'tbl_materials.wide as wide',
            'tbl_materials.longs as longs',
            '(tbl_materials.height * tbl_materials.wide * tbl_materials.longs) as total_height',
            '"" as quota_time_change_one',
            'tblstock_out_request_item.expected_date as expected_date',
            'tblstock_out_request_item.start_date as start_date',
            'tblstock_out_request_item.end_date as end_date',
            '"" as limited_time',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblstock_out_request_item';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tblstock_out_request ON tblstock_out_request.id = tblstock_out_request_item.stock_out_request_id',
            'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tblstock_out_request.production_order_id',
            'LEFT JOIN tbl_orders ON tbl_orders.id = tblstock_out_request_item.order_id',
            'LEFT JOIN tbl_materials ON tbl_materials.id = tblstock_out_request_item.item_id AND tblstock_out_request_item.item_type = "materials"',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_materials.species',
            'LEFT JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id',
        ];
        if (!$this->preViewModerationStockOut) {
            array_push($where, 'AND (tblstock_out_request.create_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tblstock_out_request.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tblstock_out_request.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'GROUP BY tblstock_out_request_item.id', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _d($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/stock_out_request/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-center" style="width: 120px">' . ($aRow['production_order_code']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['order_code']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['supplier_name']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['item_code']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['item_name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['category_name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . ($aRow['specie_name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . ($aRow['production_quantity']) . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . ($aRow['stock_quantity']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['production_require_quantity']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['stock_quantity_max']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['purchase_require_quantity']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['height']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['wide']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['longs']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['total_height']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['quota_time_change_one']) . '</div>';
            $row[] = '<div><input type="text" style="width: 150px;" onchange="updateDate(this,' . $aRow['id'] . ', \'expected_date\')" name="expected_date" class="form-control datetimepicker " value="' . (!empty($aRow['expected_date']) ? date_format(date_create($aRow['expected_date']), 'd/m/Y H:i') : '') . '"></div>';
            $row[] = '<div><input type="text" style="width: 150px;" onchange="updateDate(this,' . $aRow['id'] . ', \'start_date\')" name="start_date" class="form-control datetimepicker " value="' . (!empty($aRow['start_date']) ? date_format(date_create($aRow['start_date']), 'd/m/Y H:i') : '') . '"></div>';
            $row[] = '<div><input type="text" style="width: 150px;" onchange="updateDate(this,' . $aRow['id'] . ', \'end_date\')" name="end_date" class="form-control datetimepicker " value="' . (!empty($aRow['end_date']) ? date_format(date_create($aRow['end_date']), 'd/m/Y H:i') : '') . '"></div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['limited_time']) . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    function updateDate()
    {
        $data = $this->input->post();
        if (!empty($data['id']) && !empty($data['_value']) && !empty($data['name'])) {
            $ins = [];
            $ins[$data['name']] = to_sql_date($data['_value'], true);
            $this->db->where('id', $data['id']);
            $result = $this->db->update('tblstock_out_request_item', $ins);
            if (!empty($result)) {
                $data['result'] = 1;
                $data['message'] = lang('Cập nhật thành công');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Cập nhật thất bại');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Cập nhật thất bại');
        }
        echo json_encode($data);
    }
    public function exportExcel()
    {
        $columsExcel = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
        ];
        if ($this->input->post('export_excel')) {

            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_ch/Phieu_dieu_do_cong_viec_xuat_kho_NPL_ton.xlsx';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $BStylenumber = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '111112'),
                    'size'  => 11,
                    'name'  => 'Times New Roman'
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            );
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $row = 4;
            $staff_id = get_staff_user_id();
            $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
            $warehouses = '
                (Select
                    SUM(tblwarehouse_items.product_quantity)
                FROM tblwarehouse_items
                WHERE tblwarehouse_items.id_items = tbl_materials.id 
                    AND tblwarehouse_items.type_items = "nvl" 
                    AND tblwarehouse_items.product_quantity > 0
                    AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
            ';
            $this->db->select('
                tblstock_out_request_item.id as id,
                tblstock_out_request.date as date,
                tblstock_out_request.code as reference_no,
                tbl_productions_orders.reference_no as production_order_code,
                tbl_orders.reference_no as order_code,
                tbl_materials.name_supplier as supplier_name,
                tbl_materials.code as item_code,
                tbl_materials.name as item_name,
                tbl_category_items.name as category_name,
                tbl_species.name as specie_name,
                tblstock_out_request_item.production_quantity as production_quantity,
                ' . $warehouses . '  as stock_quantity,
                tblstock_out_request_item.production_require_quantity as production_require_quantity,
                "" as stock_quantity_max,
                tblstock_out_request_item.purchase_require_quantity as purchase_require_quantity,
                tbl_materials.height as height,
                tbl_materials.wide as wide,
                tbl_materials.longs as longs,
                (tbl_materials.height * tbl_materials.wide * tbl_materials.longs) as total_height,
                "" as quota_time_change_one,
                tblstock_out_request_item.expected_date as expected_date,
                tblstock_out_request_item.start_date as start_date,
                tblstock_out_request_item.end_date as end_date,
                "" as limited_time,
            ');
            $this->db->from('tblstock_out_request_item');
            $this->db->join('tblstock_out_request', 'tblstock_out_request.id = tblstock_out_request_item.stock_out_request_id', 'INNER');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tblstock_out_request.production_order_id', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id = tblstock_out_request_item.order_id', 'left');
            $this->db->join('tbl_materials', 'tbl_materials.id = tblstock_out_request_item.item_id AND tblstock_out_request_item.item_type = "materials"', 'left');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_materials.species', 'left');
            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'left');

            $this->db->group_by('tblstock_out_request_item.id');
            if (!$this->preViewModerationStockOut) {
                $this->db->where('(tblstock_out_request.create_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tblstock_out_request.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tblstock_out_request.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tblstock_out_request.id asc');
            $items = $this->db->get()->result_array();
            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['production_order_code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['order_code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['supplier_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['item_code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['item_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['category_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['specie_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[10] . $row, $value['production_quantity']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, $value['stock_quantity']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[12] . $row, $value['production_require_quantity']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, $value['stock_quantity_max']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, $value['purchase_require_quantity']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, $value['height']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[16] . $row, $value['wide']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, $value['longs']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[18] . $row, $value['total_height']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19] . $row, $value['quota_time_change_one']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, _d($value['expected_date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[21] . $row, _d($value['start_date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[22] . $row, _d($value['end_date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[23] . $row, $value['limited_time']);
                $code = 'stock_out_request||' . $value['id'];
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/stock_out_request/';
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
                $params['savename'] = $folder . 'qrcode/' . $qr . '.png';
                $this->ciqrcode->generate($params);
                $img = ($folder . 'qrcode/' . $qr . '.png');
                if (!empty($img)) {
                    $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                    $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                    $objDrawing1->setPath($img);
                    $objDrawing1->setWidth(90);
                    $objDrawing1->setHeight(65);
                    $objDrawing1->setOffsetX(20);
                    $objDrawing1->setOffsetY(2);
                    $objDrawing1->setCoordinates($columsExcel[24] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[24] . $row, '')->getStyle($columsExcel[24] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A5:Y' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A5:Y' . $row)->applyFromArray([
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[0])->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[1])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[11])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[12])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[13])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[23])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[24])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[25])->setWidth(20);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_dieu_do_cong_viec_xuat_kho_NPL_ton') . '.xls';
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
