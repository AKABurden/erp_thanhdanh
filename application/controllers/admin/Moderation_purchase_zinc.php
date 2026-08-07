<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_purchase_zinc extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewModerationPurchaseZinc = true;
        $this->preViewOwnModerationPurchaseZinc = true;
        $this->preAddModerationPurchaseZinc = true;
        $this->preEditModerationPurchaseZinc = true;
        $this->preApproveModerationPurchaseZinc = true;
        $this->preDeleteModerationPurchaseZinc = true;
    }

    public function index()
    {
        if (!$this->preViewModerationPurchaseZinc && !$this->preViewOwnModerationPurchaseZinc) {
            access_denied();
        }
        $data['title'] = _l('ch_moderation_purchase_zinc');
        $this->load->view('admin/moderation_purchase_zinc/index', $data);
    }
    public function getModerationPurchaseZinc()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_purchase_request_zinc_item.id as id',
            'tbl_purchase_request_zinc.date as date',
            'tbl_purchase_request_zinc.reference_no as reference_no',
            'tbl_materials.code as code_item',
            'tbl_materials.name as name_item',
            'tbl_materials.height as height',
            'tbl_materials.wide as wide',
            'tblunits.unit as unit_name',
            'tbl_purchase_request_zinc_item.quabtity_total as quabtity_total',
            'tbl_purchase_request_zinc_item.code_file as code_file',
            'tbl_purchase_request_zinc_item.link_file as link_file',
            'tbl_machines.name as operating_equipment',
            'tbl_purchase_request_zinc_item.productivity_norms as productivity_norms',
            'tbl_purchase_request_zinc_item.expected_date as expected_date',
            'tbl_purchase_request_zinc_item.start_date as start_date',
            'tbl_purchase_request_zinc_item.end_date as end_date',
            'tbl_productions_orders.reference_no as reference_no_productions_orders',
            'IF(tbl_materials.images IS NOT NULL && tbl_materials.images != "", CONCAT("uploads/materials/", "", tbl_materials.images, ""), "") as image'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_purchase_request_zinc_item';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tbl_purchase_request_zinc ON tbl_purchase_request_zinc.id = tbl_purchase_request_zinc_item.purchase_request_zinc_id',
            'LEFT JOIN tbl_materials ON tbl_materials.id = tbl_purchase_request_zinc_item.item_id',
            'LEFT JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_materials.species',
            'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_purchase_request_zinc.po_id',
            'LEFT JOIN tblunits ON tbl_materials.unit_id = tblunits.unitid',
            'LEFT JOIN tbl_productions_plan_bom ON tbl_productions_plan_bom.id = tbl_purchase_request_zinc_item.pod_id',
            'LEFT JOIN tbl_machines ON tbl_machines.id = tbl_purchase_request_zinc_item.operating_equipment',
        ];

        if (!$this->preViewModerationPurchaseZinc) {
            array_push($where, 'AND (tbl_purchase_request_zinc.staff_create = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_purchase_request_zinc.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_purchase_request_zinc.date <= '" . $end_date_search . "'");
        }

        array_push($where, "AND tbl_purchase_request_zinc.id > 0");

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_purchase_request_zinc.id as idmain'], 'GROUP BY tbl_purchase_request_zinc_item.id', [], '', 'HAVING tbl_purchase_request_zinc_item.id > 0');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _d($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/purchase_request_zinc/view/' . $aRow['idmain']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['code_item']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['name_item']) . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . ($aRow['height']) . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . ($aRow['wide']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . ($aRow['unit_name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['quabtity_total']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . ($aRow['code_file']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . ($aRow['link_file']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['operating_equipment']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['productivity_norms']) . '</div>';
            $row[] = '<div class="text-center" style="width: 120px">' . ($aRow['reference_no_productions_orders']) . '</div>';
            $images = base_url('assets/images/tnh/no_image.png');
            if (!empty($aRow['image']) && file_exists($aRow['image'])) {
                $images = $aRow['image'];
            }
            $row[] = '<div class="td-image">
            <div class="preview_image" style="width: auto;">
                <div class="display-block contract-attachment-wrapper img">
                    <div style="width:45px; margin: auto;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">
                            <div class=""><img src="' . $images . '" style="border-radius: 50%"></div>
                        </a></div>
                </div>
            </div>
        </div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['idmain'],$vv['id'],'tbl_purchase_request_zinc');
                $row[] = '<div class="text-center">'.$_data.'</div>';
            }
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
            $result = $this->db->update('tbl_purchase_request_zinc_item', $ins);
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
            $inputFileName = 'uploads/import_ch/Phieu_dieu_do_cong_doan_ghi_kem.xls';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
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
                $highestRowMin = $highestRow - 1;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[($i)].$highestRowMin,
                    $vv['name'])->getStyle("$columsExcel[$i]$highestRowMin")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[($i)].$highestRow,'')->getStyle("$columsExcel[$i]$highestRow")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->mergeCells($columsExcel[($i)].''.($highestRow-1).':'.$columsExcel[($i)].$highestRow);
                $i ++;
            }
            $row = 4;
            $staff_id = get_staff_user_id();
            $this->db->select('
                tbl_purchase_request_zinc.id as idMain,
                tbl_purchase_request_zinc.reference_no,
                tbl_productions_orders.reference_no as reference_no_productions_orders,
                tbl_purchase_request_zinc.date,
                tbl_purchase_request_zinc_item.*,
                CONCAT(tbl_materials.name,"(",tbl_materials.code,")") as text,
                tbl_materials.code as code_item,
                tbl_materials.name as name_item,
                tbl_materials.name_customer as name_customer,
                tbl_materials.height as height,
                tbl_materials.wide as wide,
                tbl_materials.longs as longs,
                tbl_materials.images as images,
                tblunits.unit as unit_name,
                tbl_machines.name as name_machines
            ');
            // $images = base_url('uploads/materials/' . $info['images']);

            $this->db->from('tbl_purchase_request_zinc_item');
            $this->db->join('tbl_purchase_request_zinc', 'tbl_purchase_request_zinc.id = tbl_purchase_request_zinc_item.purchase_request_zinc_id', 'left');
            $this->db->join('tbl_materials', 'tbl_materials.id = tbl_purchase_request_zinc_item.item_id', 'left');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_purchase_request_zinc.po_id', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'inner');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_purchase_request_zinc_item.operating_equipment', 'left');
            if (!$this->preViewModerationPurchaseZinc) {
                $this->db->where('(tbl_purchase_request_zinc.staff_create = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_purchase_request_zinc.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_purchase_request_zinc.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_purchase_request_zinc.id desc');
            $items = $this->db->get()->result_array();
            $dem = 0;
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
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['code_item']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['name_item']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, $value['height'])->getStyle($columsExcel[8] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, $value['wide'])->getStyle($columsExcel[9] . $row);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['unit_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row, $value['quabtity_total'])->getStyle($columsExcel[9] . $row);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['code_file']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['link_file']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, ($value['name_machines']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[12] . $row, $value['productivity_norms'])->getStyle($columsExcel[12] . $row);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, ($value['reference_no_productions_orders']), PHPExcel_Cell_DataType::TYPE_STRING);
                $images = '';
                if (!empty($value['images'])) {
                    $images = ('uploads/materials/' . $value['images']);
                }
                if ($value['images'] != '' && file_exists($images)) {
                    $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                    // $objDrawing1->setName('Sample image');
                    // $objDrawing1->setDescription('Sample image');
                    $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                    $objDrawing1->setPath($images);
                    // $objDrawing1->setResizeProportional(false);
                    $objDrawing1->setWidth(90);
                    $objDrawing1->setHeight(65);
                    // $objDrawing1->setWidthAndHeight(50,20);
                    $objDrawing1->setOffsetX(20);
                    $objDrawing1->setOffsetY(5);
                    $objDrawing1->setCoordinates($columsExcel[14] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, '')->getStyle($columsExcel[14] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'purchase_request_zinc||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_purchase_request_zinc', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/purchase_request_zinc/';
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
                    $objDrawing1->setCoordinates($columsExcel[15] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, '')->getStyle($columsExcel[15] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $colStt = 16;
                foreach (getListColumTable() as $kk => $vv) {
                    $_data = getDataModeration($value['idMain'],$vv['id'],'tbl_purchase_request_zinc','',true);
                    $objPHPExcel->getActiveSheet()->setCellValue("$columsExcel[$colStt]$row",$_data)->getStyle("$columsExcel[$colStt]$row")->getAlignment()->setWrapText(true);
                    if ($kk != (count(getListColumTable())) - 1) {
                        $colStt++;
                    }
                }
                $objPHPExcel->getActiveSheet()->getStyle("$columsExcel[0]$row:$columsExcel[$colStt]$row")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle("$columsExcel[0]$row:$columsExcel[$colStt]$row")->applyFromArray([
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
            }
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[0])->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[1])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[11])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[12])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[13])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[23])->setWidth(20);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_dieu_do_cong_doan_ghi_kem') . '.xls';
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
