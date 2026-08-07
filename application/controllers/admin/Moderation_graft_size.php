<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_graft_size extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewModerationGraftSize = true;
        $this->preViewOwnModerationGraftSize = true;
        $this->preAddModerationGraftSize = true;
        $this->preEditModerationGraftSize = true;
        $this->preApproveModerationGraftSize = true;
        $this->preDeleteModerationGraftSize = true;
    }

    public function index()
    {
        if (!$this->preViewModerationGraftSize && !$this->preViewOwnModerationGraftSize) {
            access_denied();
        }
        $data['title'] = _l('ch_moderation_graft_size');
        $this->load->view('admin/moderation_graft_size/index', $data);
    }
    public function getModerationGraftSize()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_request_graft_size.id as idMain',
            'tbl_request_graft_size_item.id as id',
            'tbl_request_graft_size.date as date',
            'tbl_request_graft_size.reference_no as reference_no',
            'tbl_machines.name as name',
            'tbl_products.height as height',
            'tbl_products.wide as wide',
            'tbl_request_graft_size_item.childsheet as childsheet',
            'tbl_request_graft_size_item.columnssheets1 as columnssheets1',
            'tbl_request_graft_size_item.rowssheets1 as rowssheets1',
            'tbl_request_graft_size_item.quantity_print_color1 as quantity_print_color1',
            'tbl_request_graft_size_item.quantity_zinc1 as quantity_zinc1',
            'tbl_request_graft_size_item.number_operations1 as number_operations1',
            'tbl_request_graft_size_item.columnssheets2 as columnssheets2',
            'tbl_request_graft_size_item.rowssheets2 as rowssheets2',
            'tbl_request_graft_size_item.quantity_print_color2 as quantity_print_color2',
            'tbl_request_graft_size_item.quantity_zinc2 as quantity_zinc2',
            'tbl_request_graft_size_item.number_operations2 as number_operations2',
            'tbl_request_graft_size_item.quantity_total_zinc as quantity_total_zinc',
            'tbl_request_graft_size_item.sizegraft as sizegraft',
            'tbl_request_graft_size_item.totalsize as totalsize',
            'tbl_request_graft_size_item.layout as layout',
            'tbl_request_graft_size_item.timequota as timequota',
            'tbl_request_graft_size_item.expected_date as expected_date',
            'tbl_request_graft_size_item.start_date as start_date',
            'tbl_request_graft_size_item.end_date as end_date',
            'IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as image'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_graft_size_item';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tbl_request_graft_size ON tbl_request_graft_size.id = tbl_request_graft_size_item.request_graft_size_id',
            'LEFT JOIN tbl_products ON tbl_products.id = tbl_request_graft_size_item.id_products',
            'LEFT JOIN tbl_machines ON tbl_machines.id = tbl_request_graft_size_item.machines',
            'LEFT JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.id = tbl_request_graft_size_item.id_items_stages',
            'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id',
        ];
        if (!$this->preViewModerationGraftSize) {
            array_push($where, 'AND (tbl_request_graft_size.staff_create = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_graft_size.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_graft_size.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'GROUP BY tbl_request_graft_size_item.id', [], '');
        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _d($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_graft_size/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['name']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['height']) . '</div>';
            $row[] = '<div class="text-center" style="width: 110px">' . ($aRow['wide']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['childsheet']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['columnssheets1']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['rowssheets1']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['quantity_print_color1']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['quantity_zinc1']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['number_operations1']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['columnssheets2']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['rowssheets2']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['quantity_print_color2']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['quantity_zinc2']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['number_operations2']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['quantity_total_zinc']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['sizegraft']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['totalsize']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . ($aRow['layout']) . '</div>';
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
                $_data = getDataModeration($aRow['idMain'],$vv['id'],'tbl_request_graft_size');
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
            $result = $this->db->update('tbl_request_graft_size_item', $ins);
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
            $inputFileName = 'uploads/import_ch/Phieu_dieu_do_cong_doan_ghep_size.xls';
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
            $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
            $this->db->select('
            tbl_request_graft_size.id as idMain,
            tbl_request_graft_size_item.id as id,
            tbl_request_graft_size.date as date,
            tbl_request_graft_size.reference_no as reference_no,
            tbl_machines.name as name,
            tbl_products.height as height,
            tbl_products.wide as wide,
            tbl_request_graft_size_item.childsheet as childsheet,
            tbl_request_graft_size_item.columnssheets1 as columnssheets1,
            tbl_request_graft_size_item.rowssheets1 as rowssheets1,
            tbl_request_graft_size_item.quantity_print_color1 as quantity_print_color1,
            tbl_request_graft_size_item.quantity_zinc1 as quantity_zinc1,
            tbl_request_graft_size_item.number_operations1 as number_operations1,
            tbl_request_graft_size_item.columnssheets2 as columnssheets2,
            tbl_request_graft_size_item.rowssheets2 as rowssheets2,
            tbl_request_graft_size_item.quantity_print_color2 as quantity_print_color2,
            tbl_request_graft_size_item.quantity_zinc2 as quantity_zinc2,
            tbl_request_graft_size_item.number_operations2 as number_operations2,
            tbl_request_graft_size_item.quantity_total_zinc as quantity_total_zinc,

            tbl_request_graft_size_item.sizegraft as sizegraft,
            tbl_request_graft_size_item.totalsize as totalsize,
            tbl_request_graft_size_item.layout as layout,

            tbl_request_graft_size_item.timequota as timequota,
            tbl_request_graft_size_item.expected_date as expected_date,
            tbl_request_graft_size_item.start_date as start_date,
            tbl_request_graft_size_item.end_date as end_date,
            IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as image
            ');
            $this->db->from('tbl_request_graft_size_item');
            $this->db->join('tbl_request_graft_size', 'tbl_request_graft_size.id = tbl_request_graft_size_item.request_graft_size_id', 'left');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_request_graft_size_item.id_products', 'left');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_request_graft_size_item.machines', 'left');
            $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_request_graft_size_item.id_items_stages', 'left');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id', 'left');
            $this->db->group_by('tbl_request_graft_size_item.id');
            if (!$this->preViewModerationGraftSize) {
                $this->db->where('(tbl_request_graft_size.staff_create = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_graft_size.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_graft_size.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_request_graft_size.id desc');
            $items = $this->db->get()->result_array();
            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[4] . $row, $value['height']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, $value['wide']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, $value['childsheet']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, $value['columnssheets1']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row, $value['rowssheets1']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, $value['quantity_print_color1']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[10] . $row, $value['quantity_zinc1']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, $value['number_operations1']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[12] . $row, $value['columnssheets2']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, $value['rowssheets2']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, $value['quantity_print_color2']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, $value['quantity_zinc2']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[16] . $row, $value['number_operations2']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, $value['quantity_total_zinc']);

                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[18] . $row, $value['sizegraft']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19] . $row, $value['totalsize']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[20] . $row, $value['layout']);

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
                    $objDrawing1->setCoordinates($columsExcel[21] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[21] . $row, '')->getStyle($columsExcel[21] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'request_graft_size||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_request_graft_size', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/request_graft_size/';
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
                    $objDrawing1->setCoordinates($columsExcel[22] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[22] . $row, '')->getStyle($columsExcel[22] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $colStt = 23;
                foreach (getListColumTable() as $kk => $vv) {
                    $_data = getDataModeration($value['idMain'],$vv['id'],'tbl_request_graft_size','',true);
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(30);
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[25])->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[26])->setWidth(22);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_dieu_do_cong_doan_ghep_size') . '.xls';
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
