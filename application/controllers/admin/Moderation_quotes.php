<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_quotes extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewModerationQuotes = true;
        $this->preViewOwnModerationQuotes = true;
        $this->preAddModerationQuotes = true;
        $this->preEditModerationQuotes = true;
        $this->preApproveModerationQuotes = true;
        $this->preDeleteModerationQuotes = true;
    }

    public function index()
    {
        if (!$this->preViewModerationQuotes && !$this->preViewOwnModerationQuotes) {
            access_denied();
        }
        $data['title'] = _l('ch_moderation_quotes');
        $this->load->view('admin/moderation_quotes/index', $data);
    }
    public function getModerationQuotes()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $tb_brand = '(
            SELECT
                tbl_brand.name as name,
                tbl_brand.id as id
            FROM tbl_brand
            GROUP BY tbl_brand.id
        ) as tb_brand';
        $tb_customer_group = '(
            SELECT
                GROUP_CONCAT(tblcustomers_groups.name) as name,
                tblcustomer_groups.customer_id AS client_id
            FROM tblcustomer_groups
            INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
            GROUP BY tblcustomer_groups.customer_id
        ) as tb_customer_group';
        $aColumns = [
            'tblquotation_request_item.id as id',
            'tblquotation_request.date as date',
            'tblquotation_request.code as reference_no',
            'GROUP_CONCAT(tb_brand.name SEPARATOR ", ") as brand_name',
            'tb_customer_group.name as client_group',
            'tblclients.zcode as client_code',
            'tblclients.company as client_name',
            'tbl_category_products.name as category_name',
            'tbl_species.name as specie_name',
            'tblunits.unit as unit_name',
            'tbl_products.height as height',
            'tbl_products.wide as wide',
            'tb_unit_measure.unit as unit_measure',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_products.packing as packing',
            'tbl_products.quantity_max as quantity_max',
            'tbl_products.time_inventory as time_inventory',
            'tbl_products.quota_time_change_one as quota_time_change_one',
            'tblquotation_request_item.expected_date as expected_date',
            'tblquotation_request_item.start_date as start_date',
            'tblquotation_request_item.end_date as end_date',
            'IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as image'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblquotation_request_item';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblquotation_request ON tblquotation_request.id = tblquotation_request_item.quotation_request_id',
            'LEFT JOIN tbl_products ON tbl_products.id = tblquotation_request_item.item_id AND tblquotation_request_item.item_type = "products"',
            'LEFT JOIN ' . $tb_brand . ' ON FIND_IN_SET(tb_brand.id, tblquotation_request_item.brand_ids) > 0',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_products.species',
            'LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id',
            'LEFT JOIN tblunits ON tbl_products.unit_id = tblunits.unitid',
            'LEFT JOIN tblunits tb_unit_measure ON tbl_products.unit_measure = tb_unit_measure.unitid',
            'LEFT JOIN tblclients ON tblclients.userid = tblquotation_request.client_id',
            'LEFT JOIN ' . $tb_customer_group . ' ON tb_customer_group.client_id = tblquotation_request.client_id',
        ];
        if (!$this->preViewModerationQuotes) {
            array_push($where, 'AND (tblquotation_request.create_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tblquotation_request.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tblquotation_request.date <= '" . $end_date_search . "'");
        }

        array_push($where, "AND tblquotation_request.id > 0");

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblquotation_request.id as quotation_request_id'
        ], 'GROUP BY tblquotation_request_item.id', [], '', 'HAVING tblquotation_request_item.id > 0');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _d($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/quotation_request/view/' . $aRow['quotation_request_id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-center" style="width: 120px">' . ($aRow['brand_name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['client_group']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['client_code']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['client_name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['category_name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['specie_name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . ($aRow['unit_name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . ($aRow['height']) . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . ($aRow['wide']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['unit_measure']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['item_code']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['item_name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['packing']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['quantity_max']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['time_inventory']) . '</div>';
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
                $_data = getDataModeration($aRow['quotation_request_id'],$vv['id'],'tblquotation_request');
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
            $result = $this->db->update('tblquotation_request_item', $ins);
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
            $inputFileName = 'uploads/import_ch/Phieu_dieu_do_cong_viec_bao_gia.xlsx';
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
            $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
            $tb_brand = '(
                SELECT
                    tbl_brand.name as name,
                    tbl_brand.id as id
                FROM tbl_brand
                GROUP BY tbl_brand.id
            ) as tb_brand';
            $tb_customer_group = '(
                SELECT
                    GROUP_CONCAT(tblcustomers_groups.name) as name,
                    tblcustomer_groups.customer_id AS client_id
                FROM tblcustomer_groups
                INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                GROUP BY tblcustomer_groups.customer_id
            ) as tb_customer_group';
            $this->db->select('
                tblquotation_request.id as idMain,
                tblquotation_request_item.id as id,
                tblquotation_request.date as date,
                tblquotation_request.code as reference_no,
                GROUP_CONCAT(tb_brand.name SEPARATOR ", ") as brand_name,
                tb_customer_group.name as client_group,
                tblclients.zcode as client_code,
                tblclients.company as client_name,
                tbl_category_products.name as category_name,
                tbl_species.name as specie_name,
                tblunits.unit as unit_name,
                tbl_products.height as height,
                tbl_products.wide as wide,
                tb_unit_measure.unit as unit_measure,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tbl_products.packing as packing,
                tbl_products.quantity_max as quantity_max,
                tbl_products.time_inventory as time_inventory,
                tbl_products.quota_time_change_one as quota_time_change_one,
                tblquotation_request_item.expected_date as expected_date,
                tblquotation_request_item.start_date as start_date,
                tblquotation_request_item.end_date as end_date,
                IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as image
            ');
            $this->db->from('tblquotation_request_item');
            $this->db->join('tblquotation_request', 'tblquotation_request.id = tblquotation_request_item.quotation_request_id', 'left');
            $this->db->join('tbl_products', 'tbl_products.id = tblquotation_request_item.item_id AND tblquotation_request_item.item_type = "products"', 'left');
            $this->db->join($tb_brand, 'FIND_IN_SET(tb_brand.id, tblquotation_request_item.brand_ids) > 0', 'left');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
            $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
            $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
            $this->db->join('tblunits tb_unit_measure', 'tbl_products.unit_measure = tb_unit_measure.unitid', 'left');
            $this->db->join('tblclients', 'tblclients.userid = tblquotation_request.client_id', 'left');
            $this->db->join($tb_customer_group, 'tb_customer_group.client_id = tblquotation_request.client_id', 'left');
            $this->db->group_by('tblquotation_request_item.id');
            if (!$this->preViewModerationQuotes) {
                $this->db->where('(tblquotation_request.create_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tblquotation_request.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tblquotation_request.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tblquotation_request.id asc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['brand_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['client_group']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['client_code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['client_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['category_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['specie_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['unit_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[10] . $row, $value['height'])->getStyle($columsExcel[10] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, $value['wide'])->getStyle($columsExcel[11] . $row);

                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($value['unit_measure']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, ($value['item_code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, ($value['item_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, ($value['packing']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[16] . $row, $value['quantity_max'])->getStyle($columsExcel[16] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, $value['time_inventory'])->getStyle($columsExcel[17] . $row);

                $images = '';
                if (!empty($value['image'])) {
                    $images = $value['image'];
                }
                if ($value['image'] != '' && file_exists($images)) {
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
                    $objDrawing1->setCoordinates($columsExcel[18] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[18] . $row, '')->getStyle($columsExcel[18] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'quotation_request||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tblquotation_request', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/quotation_request/';
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
                    $objDrawing1->setCoordinates($columsExcel[19] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19] . $row, '')->getStyle($columsExcel[19] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $colStt = 20;
                foreach (getListColumTable() as $kk => $vv) {
                    $_data = getDataModeration($value['idMain'],$vv['id'],'tblquotation_request','',true);
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

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_dieu_do_cong_viec_bao_gia') . '.xls';
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
