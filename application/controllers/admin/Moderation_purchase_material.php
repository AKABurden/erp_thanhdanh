<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_purchase_material extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewModerationPurchaseMaterial = true;
        $this->preViewOwnModerationPurchaseMaterial = true;
        $this->preAddModerationPurchaseMaterial = true;
        $this->preEditModerationPurchaseMaterial = true;
        $this->preApproveModerationPurchaseMaterial = true;
        $this->preDeleteModerationPurchaseMaterial = true;
    }

    public function index()
    {
        if (!$this->preViewModerationPurchaseMaterial && !$this->preViewOwnModerationPurchaseMaterial) {
            access_denied();
        }
        $data['title'] = _l('ch_moderation_purchase_material');
        $this->load->view('admin/moderation_purchase_material/index', $data);
    }
    public function getModerationPurchaseMaterial()
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
            'tbl_purchase_request_material_item.id as id',
            'tbl_purchase_request_material.date as date',
            'tbl_purchase_request_material.reference_no as reference_no',
            'tbl_productions_orders.reference_no as reference_no_productions_orders',
            'tbl_orders.reference_no as reference_no_order',
            'tblsuppliers.company as company',
            'tbl_materials.code as code_item',
            'tbl_materials.name as name_item',
            'tbl_category_items.name as name_category',
            'tbl_species.name as name_species',
            'tbl_productions_plan_bom.quantity as total_quantity_item',
            $warehouses . ' as product_quantity',
            'tbl_purchase_request_material_item.quabtity_manufactures as quabtity_manufactures',
            'tbl_purchase_request_material_item.quabtity_allow as quabtity_allow',
            'tbl_purchase_request_material_item.quabtity_purchase as quabtity_purchase',
            'tbl_materials.height as height',
            'tbl_materials.wide as wide',
            'tbl_materials.longs as longs',
            'tbl_purchase_request_material_item.totalheight as totalheight',
            'tbl_purchase_request_material_item.price as price',
            'tbl_purchase_request_material_item.tax_rate as tax_rate',
            'tbl_purchase_request_material_item.amount as amount',
            'tbl_purchase_request_material_item.timequota as timequota',
            'tbl_purchase_request_material_item.timeregulations as timeregulations',
            'tbl_purchase_request_material_item.expected_date as expected_date',
            'tbl_purchase_request_material_item.start_date as start_date',
            'tbl_purchase_request_material_item.end_date as end_date',
            'IF(tbl_materials.images IS NOT NULL && tbl_materials.images != "", CONCAT("uploads/materials/", "", tbl_materials.images, ""), "") as image'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_purchase_request_material_item';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tbl_purchase_request_material ON tbl_purchase_request_material.id = tbl_purchase_request_material_item.purchase_request_material_id',
            'LEFT JOIN tbl_materials ON tbl_materials.id = tbl_purchase_request_material_item.item_id',
            'LEFT JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_materials.species',
            'LEFT JOIN tbl_orders ON tbl_orders.id = tbl_purchase_request_material.order_id',
            'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_purchase_request_material.po_id',
            'LEFT JOIN tblunits ON tbl_materials.unit_id = tblunits.unitid',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_purchase_request_material.supplier_id',
            'LEFT JOIN tbl_productions_plan_bom ON tbl_productions_plan_bom.id = tbl_purchase_request_material_item.pod_id',
        ];

        if (!$this->preViewModerationPurchaseMaterial) {
            array_push($where, 'AND (tbl_purchase_request_material.staff_create = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_purchase_request_material.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_purchase_request_material.date <= '" . $end_date_search . "'");
        }

        array_push($where, "AND tbl_purchase_request_material.id > 0");

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_purchase_request_material.id as idmain'], 'GROUP BY tbl_purchase_request_material_item.id', [], '', 'HAVING tbl_purchase_request_material_item.id > 0');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _d($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/purchase_request_material/view/' . $aRow['idmain']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-center" style="width: 120px">' . ($aRow['reference_no_productions_orders']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['reference_no_order']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['company']) . '</div>';
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
            $row[] = '<div class="text-center" style="width: 70px">' . ($aRow['longs']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['totalheight']) . '</div>';
            $row[] = '<div class="text-right" style="width: 80px">' . formatMoney($aRow['price']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['tax_rate']) . '%</div>';
            $row[] = '<div class="text-right" style="width: 80px">' . formatMoney($aRow['amount']) . '</div>';
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
                $_data = getDataModeration($aRow['idmain'],$vv['id'],'tbl_purchase_request_material');
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
            $result = $this->db->update('tbl_purchase_request_material_item', $ins);
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
            $inputFileName = 'uploads/import_ch/Phieu_dieu_do_cong_viec_mua_NPL.xlsx';
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
                tbl_purchase_request_material.id as idMain,
                tbl_purchase_request_material.reference_no,
                tbl_orders.reference_no as reference_no_order,
                tbl_productions_orders.reference_no as reference_no_productions_orders,
                tbl_purchase_request_material.date,
                tbl_purchase_request_material_item.*,
                CONCAT(tbl_materials.name,"(",tbl_materials.code,")") as text,
                tbl_materials.code as code_item,
                tbl_materials.name as name_item,
                tbl_materials.name_customer as name_customer,
                tbl_materials.height as height,
                tbl_materials.wide as wide,
                tbl_materials.longs as longs,
                tbl_category_items.name as name_category,
                tbl_species.name as name_species,
                tblunits.unit as unit_name,
                tblsuppliers.company as company,
                tbl_productions_plan_bom.quantity as total_quantity_item,
                ' . $warehouses . ' as product_quantity
            ');
            $this->db->from('tbl_purchase_request_material_item');
            $this->db->join('tbl_purchase_request_material', 'tbl_purchase_request_material.id = tbl_purchase_request_material_item.purchase_request_material_id', 'left');
            $this->db->join('tbl_materials', 'tbl_materials.id = tbl_purchase_request_material_item.item_id', 'left');
            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'inner');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_materials.species', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_purchase_request_material.order_id', 'inner');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_purchase_request_material.po_id', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'inner');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_purchase_request_material.supplier_id', 'inner');

            $this->db->join('tbl_productions_plan_bom', 'tbl_productions_plan_bom.id = tbl_purchase_request_material_item.pod_id', 'inner');
            if (!$this->preViewModerationPurchaseMaterial) {
                $this->db->where('(tbl_purchase_request_material.staff_create = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_purchase_request_material.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_purchase_request_material.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_purchase_request_material.id asc');
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
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['company']), PHPExcel_Cell_DataType::TYPE_STRING);
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
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, $value['longs'])->getStyle($columsExcel[17] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[18] . $row, $value['totalheight'])->getStyle($columsExcel[18] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19] . $row, $value['price'])->getStyle($columsExcel[19] . $row);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, ($value['tax_rate'] . '%'), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[21] . $row, $value['amount'])->getStyle($columsExcel[21] . $row);
                if (!empty($value['barcode'])){
                    $code = $value['barcode'];
                } else {
                    $code = 'purchase_request_material||'.$value['id'];
                    $this->db->where('id',$value['id']);
                    $this->db->update('tbl_purchase_request_material',['barcode' => $code]);
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
                    $objDrawing1->setCoordinates($columsExcel[22] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[22] . $row, '')->getStyle($columsExcel[27] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $colStt = 23;
                foreach (getListColumTable() as $kk => $vv) {
                    $_data = getDataModeration($value['idMain'],$vv['id'],'tbl_purchase_request_material','',true);
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(30);
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
            $filename = lang('Phieu_dieu_do_cong_viec_mua_NPL') . '.xls';
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
