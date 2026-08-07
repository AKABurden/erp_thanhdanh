<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_export_products extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewModerationExportProducts = true;
        $this->preViewOwnModerationExportProducts = true;
        $this->preAddModerationExportProducts = true;
        $this->preEditModerationExportProducts = true;
        $this->preApproveModerationExportProducts = true;
        $this->preDeleteModerationExportProducts = true;
    }

    public function index()
    {
        if (!$this->preViewModerationExportProducts && !$this->preViewOwnModerationExportProducts) {
            access_denied();
        }
        $data['title'] = _l('ch_moderation_export_products');
    $this->load->view('admin/moderation_export_products/index', $data);
    }
    public function getModerationExportProducts()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $warehouses = '
            (Select
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.id_items = tbl_products.id 
                  AND tblwarehouse_items.type_items = "product" 
                  AND tblwarehouse_items.product_quantity > 0
                  AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
        ';
        $aColumns = [
            'tbl_request_export_products.id as idMain',
            'tbl_request_export_products_item.id as id',
            'tbl_request_export_products.date as date',
            'tbl_request_export_products.reference_no as reference_no',
            'tbl_productions_orders.reference_no as reference_no_productions_orders',
            'tbl_orders.reference_no as reference_no_order',
            'tbl_brand.name as brand_name',
            'tbl_products.code as code_item',
            'tbl_products.name as name_item',
            'tbl_category_items.name as name_category',
            'tbl_species.name as name_species',
            'tbl_productions_orders_items.quantity as total_quantity_item',
            $warehouses . ' as product_quantity',
            'tbl_request_export_products_item.quabtity_manufactures as quabtity_manufactures',
            'tbl_request_export_products_item.quabtity_allow as quabtity_allow',
            'tbl_request_export_products_item.quabtity_purchase as quabtity_purchase',
            'tbl_products.height as height',
            'tbl_products.wide as wide',
            'tbl_request_export_products_item.totalcon as totalcon',
            'tbl_request_export_products_item.totalkien as totalkien',
            'tbl_request_export_products_item.totalkg as totalkg',
            'tbl_request_export_products_item.totalallkien as totalallkien',
            'tbl_request_export_products_item.timequota as timequota',
            'tbl_request_export_products_item.timeregulations as timeregulations',
            'tbl_request_export_products_item.expected_date as expected_date',
            'tbl_request_export_products_item.start_date as start_date',
            'tbl_request_export_products_item.end_date as end_date',
            'IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/materials/", "", tbl_products.images, ""), "") as image'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_export_products_item';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tbl_request_export_products ON tbl_request_export_products.id = tbl_request_export_products_item.request_export_products_id',
            'LEFT JOIN tbl_products ON tbl_products.id = tbl_request_export_products_item.item_id',
            'LEFT JOIN tbl_category_items ON tbl_category_items.id = tbl_products.category_id',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_products.species',
            'LEFT JOIN tbl_orders ON tbl_orders.id = tbl_request_export_products.order_id',
            'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_request_export_products.po_id',
            'LEFT JOIN tblunits ON tbl_products.unit_id = tblunits.unitid',
            'LEFT JOIN tbl_brand ON tbl_brand.id = tbl_products.brand_id',
            'inner JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_request_export_products_item.pod_id',
            'inner JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
        ];
       
        if (!$this->preViewModerationExportProducts) {
            array_push($where, 'AND (tbl_request_export_products.staff_create = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_export_products.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_export_products.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_request_export_products.id as idmain'], 'GROUP BY tbl_request_export_products_item.id', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _d($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_export_products/view/' . $aRow['idmain']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-center" style="width: 120px">' . ($aRow['reference_no_productions_orders']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['reference_no_order']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['brand_name']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['code_item']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['name_item']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['name_category']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . ($aRow['name_species']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['total_quantity_item']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['product_quantity']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['quabtity_manufactures']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['quabtity_allow']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['quabtity_purchase']) . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . ($aRow['height']) . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . ($aRow['wide']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['totalcon']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['totalkien']) . '</div>';
            $row[] = '<div class="text-right" style="width: 80px">' . formatMoney($aRow['totalkg']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['totalallkien']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['timeregulations']) . '</div>';
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
                $_data = getDataModeration($aRow['idMain'],$vv['id'],'tbl_request_export_products');
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
            $result = $this->db->update('tbl_request_export_products_item', $ins);
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
            $inputFileName = 'uploads/import_ch/Phieu_dieu_do_cong_viec_xuat_kho_TP_ton.xlsx';
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
            $highestColumn = $objWorksheet->getHighestDataColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
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
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $row = 4;
            $staff_id = get_staff_user_id();
            $warehouses = '
            (Select
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.id_items = tbl_products.id 
                  AND tblwarehouse_items.type_items = "nvl" 
                  AND tblwarehouse_items.product_quantity > 0
                  AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
        ';
            $this->db->select('
            tbl_request_export_products.id as idMain,
            tbl_request_export_products_item.id as id,
            tbl_request_export_products.date as date,
            tbl_request_export_products.reference_no as reference_no,
            tbl_productions_orders.reference_no as reference_no_productions_orders,
            tbl_orders.reference_no as reference_no_order,
            tbl_brand.name as brand_name,
            tbl_products.code as code_item,
            tbl_products.name as name_item,
            tbl_category_items.name as name_category,
            tbl_species.name as name_species,
            tbl_productions_orders_items.quantity as total_quantity_item,
            '.$warehouses . ' as product_quantity,
            tbl_request_export_products_item.quabtity_manufactures as quabtity_manufactures,
            tbl_request_export_products_item.quabtity_allow as quabtity_allow,
            tbl_request_export_products_item.quabtity_purchase as quabtity_purchase,
            tbl_products.height as height,
            tbl_products.wide as wide,
            tbl_request_export_products_item.totalcon as totalcon,
            tbl_request_export_products_item.totalkien as totalkien,
            tbl_request_export_products_item.totalkg as totalkg,
            tbl_request_export_products_item.totalallkien as totalallkien,
            tbl_request_export_products_item.timequota as timequota,
            tbl_request_export_products_item.timeregulations as timeregulations,
            tbl_request_export_products_item.expected_date as expected_date,
            tbl_request_export_products_item.start_date as start_date,
            tbl_request_export_products_item.end_date as end_date,
            tbl_products.images as images
            ');
            $this->db->from('tbl_request_export_products_item');
            $this->db->join('tbl_request_export_products', 'tbl_request_export_products.id = tbl_request_export_products_item.request_export_products_id', 'left');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_request_export_products_item.item_id', 'left');
            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_products.category_id', 'inner');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_request_export_products.order_id', 'inner');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_request_export_products.po_id', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
            $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_request_export_products_item.pod_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
            $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');

            if (!$this->preViewModerationExportProducts) {
                $this->db->where('(tbl_request_export_products.staff_create = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_export_products.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_export_products.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_request_export_products.id desc');
            $items = $this->db->get()->result_array();
            $dem = 0;
            $this->load->library('ciqrcode');
            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['reference_no_productions_orders']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['reference_no_order']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['brand_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['code_item']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['name_item']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['name_category']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['name_species']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[10] . $row, $value['total_quantity_item'])->getStyle($columsExcel[10] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, $value['product_quantity'])->getStyle($columsExcel[11] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[12] . $row, $value['quabtity_manufactures'])->getStyle($columsExcel[12] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, $value['quabtity_allow'])->getStyle($columsExcel[13] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, $value['quabtity_purchase'])->getStyle($columsExcel[14] . $row);

                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, $value['height'])->getStyle($columsExcel[15] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[16] . $row, $value['wide'])->getStyle($columsExcel[16] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, $value['totalcon'])->getStyle($columsExcel[18] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[18] . $row, $value['totalkien'])->getStyle($columsExcel[19] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19] . $row, $value['totalkg'])->getStyle($columsExcel[21] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[20] . $row, $value['totalallkien'])->getStyle($columsExcel[19] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[21] . $row, $value['timeregulations'])->getStyle($columsExcel[23] . $row);

                $images = '';
                if (!empty($value['images'])) {
                    $images = ('uploads/products/' . $value['images']);
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
                    $objDrawing1->setCoordinates($columsExcel[22] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[22] . $row, '')->getStyle($columsExcel[22] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);




                if (!empty($value['barcode'])){
                    $code = $value['barcode'];
                } else {
                    $code = 'purchase_request_material||'.$value['id'];
                    $this->db->where('id',$value['id']);
                    $this->db->update('tbl_request_export_products',['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/purchase_request_material/';
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
                    $objDrawing1->setWidth(90);
                    $objDrawing1->setHeight(65);
                    $objDrawing1->setOffsetX(20);
                    $objDrawing1->setOffsetY(2);
                    $objDrawing1->setCoordinates($columsExcel[23] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[23] . $row, '')->getStyle($columsExcel[23] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $colStt = 24;
                foreach (getListColumTable() as $kk => $vv) {
                    $_data = getDataModeration($value['idMain'],$vv['id'],'tbl_request_export_products','',true);
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(20);
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[24])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[25])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[26])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[27])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[28])->setWidth(20);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_dieu_do_cong_viec_xuat_kho_TP_ton') . '.xls';
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
