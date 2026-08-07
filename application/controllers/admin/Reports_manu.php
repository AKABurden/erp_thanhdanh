<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports_manu extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();

        $this->ci = &get_instance();
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
        $this->load->model('orders_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');

        $this->preViewSyntheticPlanNPL = true;
        $this->preViewSyntheticProductionReport= true;
    }

    public function syntheticPlanNPL(){
        if (!$this->preViewSyntheticPlanNPL) {
            access_denied();
        }
        $data = [];
        $title = lang('Báo Cáo Tổng Hợp Kế Hoạch NPL');
        $data['title'] = $title;
        $this->load->view('admin/reports_manu/synthetic_plan_npl', $data);
    }

    public function getSyntheticPlanNPL(){
        $category_search = $this->input->post('category_search');
        $materials_search = $this->input->post('materials_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $quantityInventory = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0
        )";

        $aColumns = [
            'tbl_suggest_plan_purchase.id as id',
            'tbl_suggest_plan_purchase.reference_no as reference_no',
            'tbl_category_items.code as code_category',
            'tbl_materials.code as code',
            'tbl_materials.name as name',
            'tbl_materials.allowable as allowable',
            'tbl_productions_plan.date as date_plan',
            "tbl_suggest_plan_purchase_item.quantity_inventory as quantity_inventory",
            "tbl_suggest_plan_purchase_item.quantity as quantity",
            '0 as quantity_export',
            '"" as data_export',
            '0 as quantity_import',
            '"" as data_import',
            'COALESCE('.$quantityInventory.', 0) as quantity_old',
            '"" as "1"',
            '"" as "2"',
            '"" as "3"',
            '"" as "4"',
            '"" as "5"',
            '"" as "6"',
            '"" as "7"',
            '"" as "8"'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_plan_purchase';

        $where = [
            'AND tbl_suggest_plan_purchase.type = 1'
        ];

        if (!empty($category_search)){
            array_push($where,'AND tbl_materials.category_id = '.$category_search.'');
        }

        if (!empty($materials_search)){
            $materials_search = explode('__',$materials_search);
            array_push($where,'AND tbl_materials.id = '.$materials_search[0].'');
        }

        $join = [
            'INNER JOIN tbl_suggest_plan_purchase_item ON tbl_suggest_plan_purchase_item.suggest_plan_purchase_id = tbl_suggest_plan_purchase.id',
            'INNER JOIN tbl_materials ON tbl_materials.id = tbl_suggest_plan_purchase_item.item_id AND tbl_suggest_plan_purchase_item.type_item = "materials"',
            'INNER JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id',
            'INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_suggest_plan_purchase_item.plan_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_plan_purchase_item.plan_id as plan_id',
            'tbl_suggest_plan_purchase_item.item_id as item_id',
        ], '', []);
        $output = $result['output'];
        $rResult = $result['rResult'];

        $arrPlanId = [-1];
        $arrSuggestPlanId = [0];
        foreach ($rResult as $key => $value){
            $arrPlanId[] = $value['plan_id'];
            $arrSuggestPlanId[] = $value['id'];
        }

        $arrPlanId = array_unique($arrPlanId);
        $this->db->select('
            tbl_productions_plan.id as plan_id,
            tbl_suggest_exporting.reference_stock as code,
            tbl_suggest_exporting_items.item_id as item_id,
            tbl_suggest_exporting_items.type_item as type_item,
            tbl_suggest_exporting_items.quantity_export as quantity,
        ');
        $this->db->from('tbl_suggest_exporting');
        $this->db->join('tbl_suggest_exporting_items','tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id','inner');
        $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.productions_orders_id = tbl_suggest_exporting.po_id','inner');
        $this->db->join('tbl_productions_plan','tbl_productions_plan.id = tbl_productions_orders_items.plan_id','inner');
        $this->db->where_in('tbl_productions_plan.id',$arrPlanId);
        $this->db->where('tbl_suggest_exporting.po_id != 0 AND tbl_suggest_exporting.type = 2');
        $this->db->group_by('tbl_productions_plan.id, tbl_suggest_exporting_items.item_id');
        $dtDataExport = $this->db->get()->result_array();

        if (!empty($dtDataExport)) {
            $dtDataExportNew = array_reduce($dtDataExport, function($carry, $item) {
                if (isset($carry[$item['item_id'].'__'.$item['plan_id']])) {
                    $carry[$item['item_id'].'__'.$item['plan_id']] += $item['quantity'];
                } else {
                    $carry[$item['item_id'].'__'.$item['plan_id']] = $item['quantity'];
                }
                return $carry;
            }, []);

            $dtDataExportDetail = array_reduce($dtDataExport, function ($carry, $item) {
                $carry[$item['item_id'].'__'.$item['plan_id']][] = $item;
                return $carry;
            });
        }

        $this->db->select('
            tblpurchases_items.id_plan as plan_id,
            CONCAT(tblimport.prefix,"-",tblimport.code) as code,
            tblimport_items.quantity_net as quantity,
            tblimport_items.product_id as item_id,
            tblpurchases.suggest_plan_purchase_id as suggest_plan_purchase_id
        ');
        $this->db->from('tblimport');
        $this->db->join('tblimport_items','tblimport_items.id_import = tblimport.id','inner');
        $this->db->join('tblpurchase_order_items','tblpurchase_order_items.id = tblimport_items.id_purchase_order_items','inner');
        $this->db->join('tblpurchases_items','tblpurchases_items.id = tblpurchase_order_items.purchase_items_id','inner');
        $this->db->join('tblpurchases','tblpurchases.id = tblpurchases_items.purchases_id','inner');
        $this->db->where_in('tblpurchases_items.id_plan',$arrPlanId);
        $dtDataImport = $this->db->get()->result_array();

        if (!empty($dtDataImport)) {
            $dtDataImportNew = array_reduce($dtDataImport, function($carry, $item) {
                if (isset($carry[$item['item_id'].'__'.$item['plan_id'].'__'.$item['suggest_plan_purchase_id']])) {
                    $carry[$item['item_id'].'__'.$item['plan_id'].'__'.$item['suggest_plan_purchase_id']] += $item['quantity'];
                } else {
                    $carry[$item['item_id'].'__'.$item['plan_id'].'__'.$item['suggest_plan_purchase_id']] = $item['quantity'];
                }
                return $carry;
            }, []);

            $dtDataImportDetail = array_reduce($dtDataImport, function ($carry, $item) {
                $carry[$item['item_id'].'__'.$item['plan_id'].'__'.$item['suggest_plan_purchase_id']][] = $item;
                return $carry;
            });
        }

        foreach ($rResult as $key => $aRow) {
            $plan_id = $aRow['plan_id'];
            $item_id = $aRow['item_id'];
            $suggest_plan_purchase_id = $aRow['id'];
            $checkKey = $item_id.'__'.$plan_id;
            $checkKeyImport = $item_id.'__'.$plan_id.'__'.$suggest_plan_purchase_id;
            $dtDataExportDetailNew = !empty($dtDataExportDetail[$checkKey]) ? $dtDataExportDetail[$checkKey] : [];
            $dtDataImportDetailNew = !empty($dtDataImportDetail[$checkKeyImport]) ? $dtDataImportDetail[$checkKeyImport] : [];
            $dtDataDetail = [];
            $type_check = 0;
            if (count($dtDataExportDetailNew) >= count($dtDataImportDetailNew)){
                $dtDataDetail = $dtDataExportDetailNew;
                $type_check = 2;
            } else {
                $dtDataDetail = $dtDataImportDetailNew;
                $type_check = 1;
            }
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.$aRow['reference_no'].'</div>';
            $row[] = '<div class="text-left" style="min-width:100px">'.$aRow['code_category'].'</div>';
            $row[] = '<div class="text-left" style="min-width:100px">'.$aRow['code'].'</div>';
            $row[] = '<div class="text-left" style="min-width:100px">'.$aRow['name'].'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.formatNumber($aRow['allowable']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.(!empty($aRow['date_plan']) ? _dt($aRow['date_plan']) : '').'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.formatNumber($aRow['quantity_inventory']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.formatNumber($aRow['quantity']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataExportNew[$checkKey]) ? formatNumber($dtDataExportNew[$checkKey]) : '').'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.($aRow['data_export']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataImportNew[$checkKeyImport]) ? formatNumber($dtDataImportNew[$checkKeyImport]) : '').'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.($aRow['data_import']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.formatNumber($aRow['quantity_old']).'</div>';
            $row[] = '<div class="text-center" style="width:100px"></div>';
            $row[] = '<div class="text-center" style="width:100px"></div>';
            $row[] = '<div class="text-center" style="width:100px"></div>';
            $row[] = '<div class="text-center" style="width:100px"></div>';
            $row[] = '<div class="text-center" style="width:100px"></div>';
            $row[] = '<div class="text-center" style="width:100px"></div>';
            $row[] = '<div class="text-center" style="width:100px"></div>';
            $row[] = '<div class="text-center" style="width:100px"></div>';
            $output['aaData'][] = $row;
            if (!empty($dtDataDetail)){
                foreach ($dtDataDetail as $k => $v){
                    $row = array();
                    $row[] = '<div class="text-center"></div>';
                    $row[] = '<div class="text-left" style="min-width:150px"></div>';
                    $row[] = '<div class="text-left" style="min-width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    if ($type_check == 2){
                        $row[] = '<div class="text-center" style="width:100px">'.(!empty($v['quantity']) ? formatNumber($v['quantity']) : '').'</div>';
                        $row[] = '<div class="text-center" style="min-width:100px">'.$v['code'].'</div>';
                        $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? formatNumber($dtDataImportDetailNew[$k]['quantity']) : '').'</div>';
                        $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? ($dtDataImportDetailNew[$k]['code']) : '').'</div>';
                    } else {
                        $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? formatNumber($dtDataExportDetailNew[$k]['quantity']) : '').'</div>';
                        $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? ($dtDataExportDetailNew[$k]['code']) : '').'</div>';
                        $row[] = '<div class="text-center" style="width:100px">'.(!empty($v['quantity']) ? formatNumber($v['quantity']) : '').'</div>';
                        $row[] = '<div class="text-center" style="min-width:100px">'.$v['code'].'</div>';
                    }
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $output['aaData'][] = $row;
                }
            }
        }
        echo json_encode($output);
    }

    public function exportExcelSyntheticPlanNpl()
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
            $inputFileName = 'uploads/import_ch/bao_cao_tong_hop_ke_hoach_npl.xlsx';
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
            $category_search = $this->input->post('category_search');
            $materials_search = $this->input->post('materials_search');
            $row = 2;
            $staff_id = get_staff_user_id();
            $quantityInventory = "(
                SELECT
                    SUM(tblwarehouse_items.product_quantity)
                FROM tblwarehouse_items
                INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0
            )";
            $this->db->select(
                'tbl_suggest_plan_purchase.id as id,
                tbl_suggest_plan_purchase.reference_no as reference_no,
                tbl_category_items.code as code_category,
                tbl_materials.code as code,
                tbl_materials.name as name,
                tbl_materials.allowable as allowable,
                tbl_productions_plan.date as date_plan,
                tbl_suggest_plan_purchase_item.quantity_inventory as quantity_inventory,
                tbl_suggest_plan_purchase_item.quantity as quantity,
                0 as quantity_export,
                "" as data_export,
                0 as quantity_import,
                "" as data_import,
                COALESCE('.$quantityInventory.', 0) as quantity_old,
                "" as "1",
                "" as "2",
                "" as "3",
                "" as "4",
                "" as "5",
                "" as "6",
                "" as "7",
                "" as "8",
                tbl_suggest_plan_purchase_item.plan_id as plan_id,
                tbl_suggest_plan_purchase_item.item_id as item_id,
            ');
            $this->db->from('tbl_suggest_plan_purchase');
            $this->db->join('tbl_suggest_plan_purchase_item', 'tbl_suggest_plan_purchase_item.suggest_plan_purchase_id = tbl_suggest_plan_purchase.id', 'inner');
            $this->db->join('tbl_materials', 'tbl_materials.id = tbl_suggest_plan_purchase_item.item_id AND tbl_suggest_plan_purchase_item.type_item = "materials"', 'inner');
            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'inner');
            $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_suggest_plan_purchase_item.plan_id', 'inner');
            if (!empty($category_search)) {
                $this->db->where("tbl_materials.category_id = '" . $category_search . "'");
            }

            if (!empty($materials_search)) {
                $materials_search = explode('__',$materials_search);
                $this->db->where("tbl_materials.id = '" . $materials_search[0] . "'");
            }
            $this->db->where('tbl_suggest_plan_purchase.type',1);
            $this->db->order_by('tbl_suggest_plan_purchase.id desc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $arrPlanId = [-1];
            $arrSuggestPlanId = [0];
            foreach ($items as $key => $value){
                $arrPlanId[] = $value['plan_id'];
                $arrSuggestPlanId[] = $value['id'];
            }

            $arrPlanId = array_unique($arrPlanId);
            $this->db->select('
            tbl_productions_plan.id as plan_id,
            tbl_suggest_exporting.reference_stock as code,
            tbl_suggest_exporting_items.item_id as item_id,
            tbl_suggest_exporting_items.type_item as type_item,
            tbl_suggest_exporting_items.quantity_export as quantity,
        ');
            $this->db->from('tbl_suggest_exporting');
            $this->db->join('tbl_suggest_exporting_items','tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id','inner');
            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.productions_orders_id = tbl_suggest_exporting.po_id','inner');
            $this->db->join('tbl_productions_plan','tbl_productions_plan.id = tbl_productions_orders_items.plan_id','inner');
            $this->db->where_in('tbl_productions_plan.id',$arrPlanId);
            $this->db->where('tbl_suggest_exporting.po_id != 0 AND tbl_suggest_exporting.type = 2');
            $this->db->group_by('tbl_productions_plan.id, tbl_suggest_exporting_items.item_id');
            $dtDataExport = $this->db->get()->result_array();

            if (!empty($dtDataExport)) {
                $dtDataExportNew = array_reduce($dtDataExport, function($carry, $item) {
                    if (isset($carry[$item['item_id'].'__'.$item['plan_id']])) {
                        $carry[$item['item_id'].'__'.$item['plan_id']] += $item['quantity'];
                    } else {
                        $carry[$item['item_id'].'__'.$item['plan_id']] = $item['quantity'];
                    }
                    return $carry;
                }, []);

                $dtDataExportDetail = array_reduce($dtDataExport, function ($carry, $item) {
                    $carry[$item['item_id'].'__'.$item['plan_id']][] = $item;
                    return $carry;
                });
            }

            $this->db->select('
                tblpurchases_items.id_plan as plan_id,
                CONCAT(tblimport.prefix,"-",tblimport.code) as code,
                tblimport_items.quantity_net as quantity,
                tblimport_items.product_id as item_id,
                tblpurchases.suggest_plan_purchase_id as suggest_plan_purchase_id
            ');
            $this->db->from('tblimport');
            $this->db->join('tblimport_items','tblimport_items.id_import = tblimport.id','inner');
            $this->db->join('tblpurchase_order_items','tblpurchase_order_items.id = tblimport_items.id_purchase_order_items','inner');
            $this->db->join('tblpurchases_items','tblpurchases_items.id = tblpurchase_order_items.purchase_items_id','inner');
            $this->db->join('tblpurchases','tblpurchases.id = tblpurchases_items.purchases_id','inner');
            $this->db->where_in('tblpurchases_items.id_plan',$arrPlanId);
            $dtDataImport = $this->db->get()->result_array();

            if (!empty($dtDataImport)) {
                $dtDataImportNew = array_reduce($dtDataImport, function($carry, $item) {
                    if (isset($carry[$item['item_id'].'__'.$item['plan_id'].'__'.$item['suggest_plan_purchase_id']])) {
                        $carry[$item['item_id'].'__'.$item['plan_id'].'__'.$item['suggest_plan_purchase_id']] += $item['quantity'];
                    } else {
                        $carry[$item['item_id'].'__'.$item['plan_id'].'__'.$item['suggest_plan_purchase_id']] = $item['quantity'];
                    }
                    return $carry;
                }, []);

                $dtDataImportDetail = array_reduce($dtDataImport, function ($carry, $item) {
                    $carry[$item['item_id'].'__'.$item['plan_id'].'__'.$item['suggest_plan_purchase_id']][] = $item;
                    return $carry;
                });
            }

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $plan_id = $value['plan_id'];
                $item_id = $value['item_id'];
                $suggest_plan_purchase_id = $value['id'];
                $checkKey = $item_id.'__'.$plan_id;
                $checkKeyImport = $item_id.'__'.$plan_id.'__'.$suggest_plan_purchase_id;
                $dtDataExportDetailNew = !empty($dtDataExportDetail[$checkKey]) ? $dtDataExportDetail[$checkKey] : [];
                $dtDataImportDetailNew = !empty($dtDataImportDetail[$checkKeyImport]) ? $dtDataImportDetail[$checkKeyImport] : [];
                $dtDataDetail = [];
                $type_check = 0;
                if (count($dtDataExportDetailNew) >= count($dtDataImportDetailNew)){
                    $dtDataDetail = $dtDataExportDetailNew;
                    $type_check = 2;
                } else {
                    $dtDataDetail = $dtDataImportDetailNew;
                    $type_check = 1;
                }
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, ($value['code_category']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, ($value['allowable']))->getStyle("$columsExcel[5]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['allowable']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, (!empty($value['date_plan']) ? _dt($value['date_plan']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, ($value['quantity_inventory']))->getStyle("$columsExcel[7]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_inventory']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row, ($value['quantity']))->getStyle("$columsExcel[8]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, (!empty($dtDataExportNew[$checkKey]) ? ($dtDataExportNew[$checkKey]) : 0))->getStyle("$columsExcel[9]$row")->getNumberFormat()->setFormatCode(formatNumberExcel((!empty($dtDataExportNew[$checkKey]) ? ($dtDataExportNew[$checkKey]) : 0)));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, (!empty($dtDataImportNew[$checkKeyImport]) ? ($dtDataImportNew[$checkKeyImport]) : 0))->getStyle("$columsExcel[11]$row")->getNumberFormat()->setFormatCode(formatNumberExcel((!empty($dtDataImportNew[$checkKeyImport]) ? ($dtDataImportNew[$checkKeyImport]) : 0)));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, ($value['quantity_old']))->getStyle("$columsExcel[13]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_old']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[19] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[21] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);

                if (!empty($dtDataDetail)){
                    foreach ($dtDataDetail as $k => $v){
                        $row++;
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[1] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[2] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[3] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[4] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row, '');
                        if ($type_check == 2){
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, $v['quantity'])->getStyle("$columsExcel[9]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($v['quantity']));
                            $htmlReferenceNo = $v['code'];
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[10] . $row, $htmlReferenceNo);
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, (!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? ($dtDataImportDetailNew[$k]['quantity']) : ''))->getStyle("$columsExcel[11]$row")->getNumberFormat()->setFormatCode((!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? formatNumberExcel($dtDataImportDetailNew[$k]['quantity']) : ''));

                            $reference_no_new = (!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? ($dtDataImportDetailNew[$k]['reference_no']) : '');
                            $htmlReferenceNoNew = $reference_no_new;
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[12] . $row, $htmlReferenceNoNew);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, (!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? ($dtDataExportDetailNew[$k]['quantity']) : ''))->getStyle("$columsExcel[9]$row")->getNumberFormat()->setFormatCode((!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? formatNumberExcel($dtDataExportDetailNew[$k]['quantity']) : ''));
                            $reference_no_new = (!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? ($dtDataExportDetailNew[$k]['code']) : '');
                            $htmlReferenceNoNew = $reference_no_new;
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[10] . $row, $htmlReferenceNoNew);
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, $v['quantity'])->getStyle("$columsExcel[11]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($v['quantity']));
                            $htmlReferenceNo = $v['code'];
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[12] . $row, $htmlReferenceNo);
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[16] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[18] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[20] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[21] . $row, '');
                    }
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle('A3:V' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A3:V' . $row)->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(20);
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

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('bao_cao_tong_hop_ke_hoach_npl') . '.xls';
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


    public function syntheticMaterialWarehouse(){
        if (!$this->preViewSyntheticMaterialWarehouse) {
            access_denied();
        }
        $data = [];
        $title = lang('Báo Cáo Tổng Hợp Tồn Kho NPL');
        $data['title'] = $title;
        $this->load->view('admin/reports_warehouse/synthetic_material_warehouse', $data);
    }

    public function getSyntheticMaterialWarehouse(){
        $category_search = $this->input->post('category_search');
        $materials_search = $this->input->post('materials_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $quantityInventory = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0 
        )";



        $aColumns = [
            'tbl_materials.id as id',
            'tbl_materials.code as code',
            'tbl_materials.name as name',
            "COALESCE($quantityInventory, 0) as quantity_invertory",
            'tbl_materials.allowable as allowable',
            '0 as quantity_export',
            '"" as data_export',
            '0 as quantity_import',
            '"" as data_import',
            '0 as quantity_old',
            'tbl_materials.time_stock as time_stock',
            '"" as overdue_warning',
            '"" as date_foso',
            '"" as date_adjust',
            '"" as date_not_use',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_materials';

        $where = [];
        if (!empty($category_search)){
            array_push($where,'AND tbl_materials.category_id = '.$category_search.'');
        }

        if (!empty($materials_search)){
            $materials_search = explode('__',$materials_search);
            array_push($where,'AND tbl_materials.id = '.$materials_search[0].'');
        }

        $join = [
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_materials.date_status',
            'tbl_materials.status',
        ], '', []);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $arrMaterialId = [0];
        foreach ($rResult as $key => $value){
            $arrMaterialId[] = $value['id'];
        }
        $tb_export = "(
            SELECT 
                tb_export.id,
                tb_export.item_id,
                tb_export.reference_no,
                tb_export.object_type,
                2 as type,
                (tb_export.quantity) as quantity
            FROM
            (
            SELECT 
                tbl_delivery_items.item_id as item_id,
                tbl_deliveries.id as id,
                tbl_deliveries.reference_no as reference_no,
                'delivery' as object_type,
                tbl_delivery_items.quantity_stock as quantity
            FROM tbl_deliveries
            INNER JOIN tbl_delivery_items ON tbl_delivery_items.delivery_id = tbl_deliveries.id
            WHERE tbl_delivery_items.type_item IN ('materials') AND tbl_delivery_items.item_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tblreturn_suppliers_items.product_id as item_id,
                tblreturn_suppliers.id as id,
                CONCAT(tblreturn_suppliers.prefix,'',tblreturn_suppliers.code) as reference_no,
                'return_suppliers' as object_type,
                tblreturn_suppliers_items.quantity_net as quantity
            FROM tblreturn_suppliers
            INNER JOIN tblreturn_suppliers_items ON tblreturn_suppliers_items.id_return = tblreturn_suppliers.id
            WHERE tblreturn_suppliers_items.type IN ('nvl') AND tblreturn_suppliers_items.product_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbladjusted_items.product_id as item_id,
                tbladjusted.id as id,
                CONCAT(tbladjusted.prefix,'-',tbladjusted.code) as reference_no,
                'adjusted_items' as object_type,
                tbladjusted_items.quantity_net as quantity
            FROM tbladjusted
            INNER JOIN tbladjusted_items ON tbladjusted_items.id_adjusted = tbladjusted.id
            WHERE tbladjusted_items.type IN ('nvl') AND tbladjusted.type = 2 AND tbladjusted_items.product_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbltransfer_warehouse_detail.id_items as item_id,
                tbltransfer_warehouse.id as id,
                CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as reference_no,
                'transfer_warehouse' as object_type,
                tbltransfer_warehouse_detail.quantity_net as quantity
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse_detail.type IN ('nvl') AND tbltransfer_warehouse_detail.id_items IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_suggest_exporting_items.item_id as item_id,
                tbl_suggest_exporting.id as id,
                tbl_suggest_exporting.reference_no as reference_no,
                'suggest_exporting' as object_type,
                tbl_suggest_exporting_items.quantity_warehouse as quantity
            FROM tbl_suggest_exporting
            INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
            WHERE tbl_suggest_exporting_items.type_item IN ('materials') AND tbl_suggest_exporting_items.item_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbltblexport_different_items.product_id as item_id,
                tblexport_different.id as id,
                CONCAT(tblexport_different.prefix,'-',tblexport_different.code) as reference_no,
                'export_different' as object_type,
                tbltblexport_different_items.quantity_net as quantity
            FROM tblexport_different
            INNER JOIN tbltblexport_different_items ON tbltblexport_different_items.id_export_different = tblexport_different.id
            WHERE tbltblexport_different_items.type IN ('nvl') AND tbltblexport_different_items.product_id IN (" . implode(',', $arrMaterialId) . ")
            ) tb_export
        ) ";
        $dtDataExport = $this->db->query($tb_export)->result_array();
        if (!empty($dtDataExport)) {
            $dtDataExportNew = array_reduce($dtDataExport, function($carry, $item) {
                if (isset($carry[$item['item_id']])) {
                    $carry[$item['item_id']] += $item['quantity'];
                } else {
                    $carry[$item['item_id']] = $item['quantity'];
                }
                return $carry;
            }, []);

            $dtDataExportDetail = array_reduce($dtDataExport, function ($carry, $item) {
                $carry[$item['item_id']][] = $item;
                return $carry;
            });
        }

        $tb_import = "(
            SELECT 
                tb_import.id,
                tb_import.item_id,
                tb_import.reference_no,
                tb_import.object_type,
                1 as type,
                (tb_import.quantity) as quantity
            FROM
            (
            SELECT 
                tblimport_items.product_id as item_id,
                tblimport.id as id,
                CONCAT(tblimport.prefix,'-',tblimport.code) as reference_no,
                'import' as object_type,
                tblimport_items.quantity_stock as quantity
            FROM tblimport
            INNER JOIN tblimport_items ON tblimport_items.id_import = tblimport.id
            WHERE tblimport_items.type IN ('nvl') AND tblimport_items.product_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbladjusted_items.product_id as item_id,
                tbladjusted.id as id,
                CONCAT(tbladjusted.prefix,'-',tbladjusted.code) as reference_no,
                'adjusted_items' as object_type,
                tbladjusted_items.quantity_net as quantity
            FROM tbladjusted
            INNER JOIN tbladjusted_items ON tbladjusted_items.id_adjusted = tbladjusted.id
            WHERE tbladjusted_items.type IN ('nvl') AND tbladjusted.type = 1 AND tbladjusted_items.product_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbltransfer_warehouse_detail.id_items as item_id,
                tbltransfer_warehouse.id as id,
                CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as reference_no,
                'transfer_warehouse' as object_type,
                tbltransfer_warehouse_detail.quantity_net as quantity
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse_detail.type IN ('nvl') AND tbltransfer_warehouse_detail.id_items IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_purchase_product_items.item_id as item_id,
                tbl_purchase_products.id as id,
                tbl_purchase_products.reference_no as reference_no,
                'purchase_products' as object_type,
                tbl_purchase_product_items.quantity as quantity
            FROM tbl_purchase_products
            INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
            WHERE tbl_purchase_product_items.type_item IN ('materials') AND tbl_purchase_product_items.item_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_purchase_internal_items.item_id as item_id,
                tbl_purchase_internal.id as id,
                tbl_purchase_internal.reference_no as reference_no,
                'purchase_internal' as object_type,
                tbl_purchase_internal_items.quantity as quantity
            FROM tbl_purchase_internal
            INNER JOIN tbl_purchase_internal_items ON tbl_purchase_internal_items.purchase_internal_id = tbl_purchase_internal.id
            WHERE tbl_purchase_internal_items.type_item IN ('nvl') AND tbl_purchase_internal_items.item_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_returned_goods_items.item_id as item_id,
                tbl_returned_goods.id as id,
                tbl_returned_goods.reference_no as reference_no,
                'returned_goods' as object_type,
                tbl_returned_goods_items.quantity as quantity
            FROM tbl_returned_goods
            INNER JOIN tbl_returned_goods_items ON tbl_returned_goods_items.returned_goods_id = tbl_returned_goods.id
            WHERE tbl_returned_goods_items.type_item IN ('nvl') AND tbl_returned_goods_items.item_id IN (" . implode(',', $arrMaterialId) . ")
            ) tb_import
        ) ";
        $dtDataImport = $this->db->query($tb_import)->result_array();
        if (!empty($dtDataImport)) {
            $dtDataImportNew = array_reduce($dtDataImport, function($carry, $item) {
                if (isset($carry[$item['item_id']])) {
                    $carry[$item['item_id']] += $item['quantity'];
                } else {
                    $carry[$item['item_id']] = $item['quantity'];
                }
                return $carry;
            }, []);

            $dtDataImportDetail = array_reduce($dtDataImport, function ($carry, $item) {
                $carry[$item['item_id']][] = $item;
                return $carry;
            });
        }
        foreach ($rResult as $key => $aRow) {
            $material_id = $aRow['id'];
            $this->db->select('tblimport.date');
            $this->db->from('tblimport');
            $this->db->join('tblimport_items','tblimport_items.id_import = tblimport.id');
            $this->db->where('tblimport_items.product_id',$material_id);
            $this->db->where("tblimport_items.type IN ('nvl')");
            $this->db->order_by('tblimport.date desc');
            $this->db->limit(1);
            $dtImport = $this->db->get()->row_array();
            $date_import = !empty($dtImport) ? $dtImport['date'] : null;

            $this->db->select('tbladjusted.date');
            $this->db->from('tbladjusted');
            $this->db->join('tbladjusted_items','tbladjusted_items.id_adjusted = tbladjusted.id');
            $this->db->where('tbladjusted_items.product_id',$material_id);
            $this->db->where("tbladjusted_items.type IN ('nvl')");
            $this->db->order_by('tbladjusted.date desc');
            $this->db->limit(1);
            $dtDieuChinh = $this->db->get()->row_array();
            $date_adjust = !empty($dtDieuChinh) ? $dtDieuChinh['date'] : null;

            $time_stock = $aRow['time_stock'];
            $overdue_warning = null;
            if (!empty($date_import)){
                $dateEndTamp = strtotime("+$time_stock days", strtotime($date_import));
                $dateEnd = date("Y-m-d", $dateEndTamp);
                if (strtotime($dateEnd) > strtotime(date('Y-m-d'))){
                    $overdue_warning = 'Quá hạn';
                }
            }
            $date_status = '';
            if ($aRow['status'] == 0){
                $date_status = $aRow['date_status'];
            }
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.$aRow['code'].'</div>';
            $row[] = '<div class="text-left" style="min-width:100px">'.$aRow['name'].'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.formatNumber($aRow['quantity_invertory']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.formatNumber($aRow['allowable']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataExportNew[$material_id]) ? formatNumber($dtDataExportNew[$material_id]) : '').'</div>';
            $row[] = '<div class="text-center" style="min-width:100px"></div>';
            $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataImportNew[$material_id]) ? formatNumber($dtDataImportNew[$material_id]) : '').'</div>';
            $row[] = '<div class="text-center" style="min-width:100px"></div>';
            $row[] = '<div class="text-center" style="width:100px">'.formatNumber($aRow['quantity_invertory']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.($aRow['time_stock']).'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$overdue_warning.'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.(!empty($date_import) ? _dt($date_import) : '').'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.(!empty($date_adjust) ? _dt($date_adjust) : '').'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.(!empty($date_status) ? _dt($date_status) : '').'</div>';
            $output['aaData'][] = $row;
            $dtDataExportDetailNew = !empty($dtDataExportDetail[$material_id]) ? $dtDataExportDetail[$material_id] : [];
            $dtDataImportDetailNew = !empty($dtDataImportDetail[$material_id]) ? $dtDataImportDetail[$material_id] : [];
            $dtDataDetail = [];
            $type_check = 0;
            if (count($dtDataExportDetailNew) >= count($dtDataImportDetailNew)){
                $dtDataDetail = $dtDataExportDetailNew;
                $type_check = 2;
            } else {
                $dtDataDetail = $dtDataImportDetailNew;
                $type_check = 1;
            }
            if (!empty($dtDataDetail)){
                foreach ($dtDataDetail as $k => $v){
                    $row = array();
                    $row[] = '<div class="text-center"></div>';
                    $row[] = '<div class="text-left" style="min-width:150px"></div>';
                    $row[] = '<div class="text-left" style="min-width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    if ($type_check == 2){
                        $row[] = '<div class="text-center" style="width:100px">'.(!empty($v['quantity']) ? formatNumber($v['quantity']) : '').'</div>';
                        $htmlReferenceNo = '';
                        if ($v['object_type'] == 'import'){
                            $htmlReferenceNo = '<a href="#" onclick="view_import('.$v['id'].')">'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'purchase_products'){
                            $htmlReferenceNo = '<a class="tnh-modal" href="' . base_url('admin/stock/view_purchase_product/'.$v['id'].'') . '">'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'return_suppliers'){
                            $htmlReferenceNo = '<a href="#" onclick="view_return_suppliers(' . $v['id'] . '); return false;" >'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'adjusted_items'){
                            $htmlReferenceNo = '<a href="#" onclick="view_adjusted(' . $v['id'] . '); return false;" >'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'transfer_warehouse'){
                            $htmlReferenceNo = '<a href="#" onclick="view_transfer(' . $v['id'] . '); return false;" >'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'purchase_internal'){
                            $htmlReferenceNo = '<a class="tnh-modal" href="' . base_url('admin/stock/view_purchase_internal/'.$v['id'].'') . '">'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'returned_goods'){
                            $htmlReferenceNo = '<a class="tnh-modal" href="' . admin_url('admin/returned_goods/view_returned_goods/' . $v['id_main']) . '" >'.$v['reference_no'].'</a>';
                        }
                        $row[] = '<div class="text-center" style="min-width:100px">'.$htmlReferenceNo.'</div>';
                        $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? formatNumber($dtDataImportDetailNew[$k]['quantity']) : '').'</div>';

                        $reference_no_new = (!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? ($dtDataImportDetailNew[$k]['reference_no']) : '');
                        $id_new = (!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? ($dtDataImportDetailNew[$k]['id']) : '');
                        $object_type = (!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? ($dtDataImportDetailNew[$k]['object_type']) : '');
                        $htmlReferenceNoNew = '';
                        if ($object_type == 'delivery'){
                            $htmlReferenceNoNew = '<a class="tnh-modal" href="' . base_url('admin/releases/view_delivery/'.$id_new.'') . '">'.$reference_no_new.'</a>';
                        } elseif ($object_type == 'return_suppliers'){
                            $htmlReferenceNoNew = '<a href="#" onclick="view_return_suppliers(' . $id_new . '); return false;" >'.$reference_no_new.'</a>';
                        } elseif ($object_type == 'adjusted_items'){
                            $htmlReferenceNoNew = '<a href="#" onclick="view_adjusted(' . $id_new . '); return false;" >'.$reference_no_new.'</a>';
                        } elseif ($object_type == 'transfer_warehouse'){
                            $htmlReferenceNoNew = '<a href="#" onclick="view_transfer(' . $id_new . '); return false;" >'.$reference_no_new.'</a>';
                        } elseif ($object_type == 'suggest_exporting'){
                            $htmlReferenceNoNew = '<a class="tnh-modal" href="' . base_url('admin/stock/view_exporting_production/'.$id_new.'') . '">'.$reference_no_new.'</a>';
                        } elseif ($object_type== 'export_different'){
                            $htmlReferenceNoNew = '<a href="#" onclick="view_export_different(' . $id_new . '); return false;" >'.$reference_no_new.'</a>';
                        }

                        $row[] = '<div class="text-center" style="min-width:100px">'.$htmlReferenceNoNew.'</div>';
                    } else {
                        $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? formatNumber($dtDataExportDetailNew[$k]['quantity']) : '').'</div>';
                        $reference_no_new = (!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? ($dtDataExportDetailNew[$k]['reference_no']) : '');
                        $id_new = (!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? ($dtDataExportDetailNew[$k]['id']) : '');
                        $object_type = (!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? ($dtDataExportDetailNew[$k]['object_type']) : '');
                        $htmlReferenceNoNew = '';
                        if ($object_type == 'delivery'){
                            $htmlReferenceNoNew = '<a class="tnh-modal" href="' . base_url('admin/releases/view_delivery/'.$id_new.'') . '">'.$reference_no_new.'</a>';
                        } elseif ($object_type == 'return_suppliers'){
                            $htmlReferenceNoNew = '<a href="#" onclick="view_return_suppliers(' . $id_new . '); return false;" >'.$reference_no_new.'</a>';
                        } elseif ($object_type == 'adjusted_items'){
                            $htmlReferenceNoNew = '<a href="#" onclick="view_adjusted(' . $id_new . '); return false;" >'.$reference_no_new.'</a>';
                        } elseif ($object_type == 'transfer_warehouse'){
                            $htmlReferenceNoNew = '<a href="#" onclick="view_transfer(' . $id_new . '); return false;" >'.$reference_no_new.'</a>';
                        } elseif ($object_type == 'suggest_exporting'){
                            $htmlReferenceNoNew = '<a class="tnh-modal" href="' . base_url('admin/stock/view_exporting_production/'.$id_new.'') . '">'.$reference_no_new.'</a>';
                        } elseif ($object_type== 'export_different'){
                            $htmlReferenceNoNew = '<a href="#" onclick="view_export_different(' . $id_new . '); return false;" >'.$reference_no_new.'</a>';
                        }
                        $row[] = '<div class="text-center" style="min-width:100px">'.$htmlReferenceNoNew.'</div>';
                        $row[] = '<div class="text-center" style="width:100px">'.(!empty($v['quantity']) ? formatNumber($v['quantity']) : '').'</div>';
                        $htmlReferenceNo = '';
                        if ($v['object_type'] == 'import'){
                            $htmlReferenceNo = '<a href="#" onclick="view_import('.$v['id'].')">'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'purchase_products'){
                            $htmlReferenceNo = '<a class="tnh-modal" href="' . base_url('admin/stock/view_purchase_product/'.$v['id'].'') . '">'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'return_suppliers'){
                            $htmlReferenceNo = '<a href="#" onclick="view_return_suppliers(' . $v['id'] . '); return false;" >'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'adjusted_items'){
                            $htmlReferenceNo = '<a href="#" onclick="view_adjusted(' . $v['id'] . '); return false;" >'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'transfer_warehouse'){
                            $htmlReferenceNo = '<a href="#" onclick="view_transfer(' . $v['id'] . '); return false;" >'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'purchase_internal'){
                            $htmlReferenceNo = '<a class="tnh-modal" href="' . base_url('admin/stock/view_purchase_internal/'.$v['id'].'') . '">'.$v['reference_no'].'</a>';
                        } elseif ($v['object_type'] == 'returned_goods'){
                            $htmlReferenceNo = '<a class="tnh-modal" href="' . admin_url('admin/returned_goods/view_returned_goods/' . $v['id_main']) . '" >'.$v['reference_no'].'</a>';
                        }
                        $row[] = '<div class="text-center" style="min-width:100px">'.$htmlReferenceNo.'</div>';
                    }
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-center" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $row[] = '<div class="text-left" style="width:100px"></div>';
                    $output['aaData'][] = $row;
                }
            }
        }
        echo json_encode($output);
    }

    public function exportExcelWarehouseMaterial()
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
            $inputFileName = 'uploads/import_ch/bao_cao_tong_hop_ton_kho_nvl.xlsx';
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
            $category_search = $this->input->post('category_search');
            $materials_search = $this->input->post('materials_search');
            $row = 2;
            $staff_id = get_staff_user_id();
            $quantityInventory = "(
                SELECT
                    SUM(tblwarehouse_items.product_quantity)
                FROM tblwarehouse_items
                INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0
            )";
            $this->db->select(
                'tbl_materials.id as id,
                tbl_materials.code as code,
                tbl_materials.name as name,
                COALESCE('.$quantityInventory.', 0) as quantity_invertory,
                tbl_materials.allowable as allowable,
                0 as quantity_export,
                "" as data_export,
                0 as quantity_import,
                "" as data_import,
                0 as quantity_old,
                tbl_materials.time_stock as time_stock,
                "" as overdue_warning,
                "" as date_foso,
                "" as date_adjust,
                "" as date_not_use,
                tbl_materials.date_status as date_status,
                tbl_materials.status as status,
            ');
            $this->db->from('tbl_materials');
            if (!empty($category_search)) {
                $this->db->where("tbl_materials.category_id = '" . $category_search . "'");
            }

            if (!empty($materials_search)) {
                $materials_search = explode('__',$materials_search);
                $this->db->where("tbl_materials.id = '" . $materials_search[0] . "'");
            }
            $this->db->order_by('tbl_materials.id desc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $arrMaterialId = [0];
            foreach ($items as $key => $value){
                $arrMaterialId[] = $value['id'];
            }
            $tb_export = "(
            SELECT 
                tb_export.id,
                tb_export.item_id,
                tb_export.reference_no,
                tb_export.object_type,
                2 as type,
                (tb_export.quantity) as quantity
            FROM
            (
            SELECT 
                tbl_delivery_items.item_id as item_id,
                tbl_deliveries.id as id,
                tbl_deliveries.reference_no as reference_no,
                'delivery' as object_type,
                tbl_delivery_items.quantity_stock as quantity
            FROM tbl_deliveries
            INNER JOIN tbl_delivery_items ON tbl_delivery_items.delivery_id = tbl_deliveries.id
            WHERE tbl_delivery_items.type_item IN ('materials') AND tbl_delivery_items.item_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tblreturn_suppliers_items.product_id as item_id,
                tblreturn_suppliers.id as id,
                CONCAT(tblreturn_suppliers.prefix,'',tblreturn_suppliers.code) as reference_no,
                'return_suppliers' as object_type,
                tblreturn_suppliers_items.quantity_net as quantity
            FROM tblreturn_suppliers
            INNER JOIN tblreturn_suppliers_items ON tblreturn_suppliers_items.id_return = tblreturn_suppliers.id
            WHERE tblreturn_suppliers_items.type IN ('nvl') AND tblreturn_suppliers_items.product_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbladjusted_items.product_id as item_id,
                tbladjusted.id as id,
                CONCAT(tbladjusted.prefix,'-',tbladjusted.code) as reference_no,
                'adjusted_items' as object_type,
                tbladjusted_items.quantity_net as quantity
            FROM tbladjusted
            INNER JOIN tbladjusted_items ON tbladjusted_items.id_adjusted = tbladjusted.id
            WHERE tbladjusted_items.type IN ('nvl') AND tbladjusted.type = 2 AND tbladjusted_items.product_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbltransfer_warehouse_detail.id_items as item_id,
                tbltransfer_warehouse.id as id,
                CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as reference_no,
                'transfer_warehouse' as object_type,
                tbltransfer_warehouse_detail.quantity_net as quantity
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse_detail.type IN ('nvl') AND tbltransfer_warehouse_detail.id_items IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_suggest_exporting_items.item_id as item_id,
                tbl_suggest_exporting.id as id,
                tbl_suggest_exporting.reference_no as reference_no,
                'suggest_exporting' as object_type,
                tbl_suggest_exporting_items.quantity_warehouse as quantity
            FROM tbl_suggest_exporting
            INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
            WHERE tbl_suggest_exporting_items.type_item IN ('materials') AND tbl_suggest_exporting_items.item_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbltblexport_different_items.product_id as item_id,
                tblexport_different.id as id,
                CONCAT(tblexport_different.prefix,'-',tblexport_different.code) as reference_no,
                'export_different' as object_type,
                tbltblexport_different_items.quantity_net as quantity
            FROM tblexport_different
            INNER JOIN tbltblexport_different_items ON tbltblexport_different_items.id_export_different = tblexport_different.id
            WHERE tbltblexport_different_items.type IN ('nvl') AND tbltblexport_different_items.product_id IN (" . implode(',', $arrMaterialId) . ")
            ) tb_export
        ) ";
            $dtDataExport = $this->db->query($tb_export)->result_array();
            if (!empty($dtDataExport)) {
                $dtDataExportNew = array_reduce($dtDataExport, function($carry, $item) {
                    if (isset($carry[$item['item_id']])) {
                        $carry[$item['item_id']] += $item['quantity'];
                    } else {
                        $carry[$item['item_id']] = $item['quantity'];
                    }
                    return $carry;
                }, []);

                $dtDataExportDetail = array_reduce($dtDataExport, function ($carry, $item) {
                    $carry[$item['item_id']][] = $item;
                    return $carry;
                });
            }

            $tb_import = "(
            SELECT 
                tb_import.id,
                tb_import.item_id,
                tb_import.reference_no,
                tb_import.object_type,
                1 as type,
                (tb_import.quantity) as quantity
            FROM
            (
            SELECT 
                tblimport_items.product_id as item_id,
                tblimport.id as id,
                CONCAT(tblimport.prefix,'-',tblimport.code) as reference_no,
                'import' as object_type,
                tblimport_items.quantity_stock as quantity
            FROM tblimport
            INNER JOIN tblimport_items ON tblimport_items.id_import = tblimport.id
            WHERE tblimport_items.type IN ('nvl') AND tblimport_items.product_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbladjusted_items.product_id as item_id,
                tbladjusted.id as id,
                CONCAT(tbladjusted.prefix,'-',tbladjusted.code) as reference_no,
                'adjusted_items' as object_type,
                tbladjusted_items.quantity_net as quantity
            FROM tbladjusted
            INNER JOIN tbladjusted_items ON tbladjusted_items.id_adjusted = tbladjusted.id
            WHERE tbladjusted_items.type IN ('nvl') AND tbladjusted.type = 1 AND tbladjusted_items.product_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbltransfer_warehouse_detail.id_items as item_id,
                tbltransfer_warehouse.id as id,
                CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as reference_no,
                'transfer_warehouse' as object_type,
                tbltransfer_warehouse_detail.quantity_net as quantity
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse_detail.type IN ('nvl') AND tbltransfer_warehouse_detail.id_items IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_purchase_product_items.item_id as item_id,
                tbl_purchase_products.id as id,
                tbl_purchase_products.reference_no as reference_no,
                'purchase_products' as object_type,
                tbl_purchase_product_items.quantity as quantity
            FROM tbl_purchase_products
            INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
            WHERE tbl_purchase_product_items.type_item IN ('materials') AND tbl_purchase_product_items.item_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_purchase_internal_items.item_id as item_id,
                tbl_purchase_internal.id as id,
                tbl_purchase_internal.reference_no as reference_no,
                'purchase_internal' as object_type,
                tbl_purchase_internal_items.quantity as quantity
            FROM tbl_purchase_internal
            INNER JOIN tbl_purchase_internal_items ON tbl_purchase_internal_items.purchase_internal_id = tbl_purchase_internal.id
            WHERE tbl_purchase_internal_items.type_item IN ('nvl') AND tbl_purchase_internal_items.item_id IN (" . implode(',', $arrMaterialId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_returned_goods_items.item_id as item_id,
                tbl_returned_goods.id as id,
                tbl_returned_goods.reference_no as reference_no,
                'returned_goods' as object_type,
                tbl_returned_goods_items.quantity as quantity
            FROM tbl_returned_goods
            INNER JOIN tbl_returned_goods_items ON tbl_returned_goods_items.returned_goods_id = tbl_returned_goods.id
            WHERE tbl_returned_goods_items.type_item IN ('nvl') AND tbl_returned_goods_items.item_id IN (" . implode(',', $arrMaterialId) . ")
            ) tb_import
        ) ";
            $dtDataImport = $this->db->query($tb_import)->result_array();
            if (!empty($dtDataImport)) {
                $dtDataImportNew = array_reduce($dtDataImport, function($carry, $item) {
                    if (isset($carry[$item['item_id']])) {
                        $carry[$item['item_id']] += $item['quantity'];
                    } else {
                        $carry[$item['item_id']] = $item['quantity'];
                    }
                    return $carry;
                }, []);

                $dtDataImportDetail = array_reduce($dtDataImport, function ($carry, $item) {
                    $carry[$item['item_id']][] = $item;
                    return $carry;
                });
            }
            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $material_id = $value['id'];
                $this->db->select('tblimport.date');
                $this->db->from('tblimport');
                $this->db->join('tblimport_items','tblimport_items.id_import = tblimport.id');
                $this->db->where('tblimport_items.product_id',$material_id);
                $this->db->where("tblimport_items.type IN ('nvl')");
                $this->db->order_by('tblimport.date desc');
                $this->db->limit(1);
                $dtImport = $this->db->get()->row_array();
                $date_import = !empty($dtImport) ? $dtImport['date'] : null;

                $this->db->select('tbladjusted.date');
                $this->db->from('tbladjusted');
                $this->db->join('tbladjusted_items','tbladjusted_items.id_adjusted = tbladjusted.id');
                $this->db->where('tbladjusted_items.product_id',$material_id);
                $this->db->where("tbladjusted_items.type IN ('product')");
                $this->db->order_by('tbladjusted.date desc');
                $this->db->limit(1);
                $dtDieuChinh = $this->db->get()->row_array();
                $date_adjust = !empty($dtDieuChinh) ? $dtDieuChinh['date'] : null;

                $time_stock = $value['time_stock'];
                $overdue_warning = null;
                if (!empty($date_import)){
                    $dateEndTamp = strtotime("+$time_stock days", strtotime($date_import));
                    $dateEnd = date("Y-m-d", $dateEndTamp);
                    if (strtotime($dateEnd) > strtotime(date('Y-m-d'))){
                        $overdue_warning = 'Quá hạn';
                    }
                }
                $date_status = '';
                if ($value['status'] == 0){
                    $date_status = $value['date_status'];
                }
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, ($value['code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, ($value['name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[3] . $row, ($value['quantity_invertory']))->getStyle("$columsExcel[3]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_invertory']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[4] . $row, ($value['allowable']))->getStyle("$columsExcel[4]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['allowable']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, (!empty($dtDataExportNew[$material_id]) ? ($dtDataExportNew[$material_id]) : ''))->getStyle("$columsExcel[5]$row")->getNumberFormat()->setFormatCode(formatNumberExcel((!empty($dtDataExportNew[$material_id]) ? ($dtDataExportNew[$material_id]) : 0)));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, '');
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, (!empty($dtDataImportNew[$material_id]) ? ($dtDataImportNew[$material_id]) : 0))->getStyle("$columsExcel[7]$row")->getNumberFormat()->setFormatCode(formatNumberExcel((!empty($dtDataImportNew[$material_id]) ? ($dtDataImportNew[$material_id]) : 0)));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row, '');
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, ($value['quantity_invertory']))->getStyle("$columsExcel[9]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_invertory']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['time_stock']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, $overdue_warning, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, (!empty($date_purchase) ? _dt($date_purchase) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, (!empty($date_adjust) ? _dt($date_adjust) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, (!empty($date_status) ? _dt($date_status) : ''), PHPExcel_Cell_DataType::TYPE_STRING);

                $dtDataExportDetailNew = !empty($dtDataExportDetail[$material_id]) ? $dtDataExportDetail[$material_id] : [];
                $dtDataImportDetailNew = !empty($dtDataImportDetail[$material_id]) ? $dtDataImportDetail[$material_id] : [];
                $dtDataDetail = [];
                $type_check = 0;
                if (count($dtDataExportDetailNew) >= count($dtDataImportDetailNew)){
                    $dtDataDetail = $dtDataExportDetailNew;
                    $type_check = 2;
                } else {
                    $dtDataDetail = $dtDataImportDetailNew;
                    $type_check = 1;
                }
                if (!empty($dtDataDetail)){
                    foreach ($dtDataDetail as $k => $v){
                        $row++;
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[1] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[2] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[3] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[4] . $row, '');
                        if ($type_check == 2){
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, $v['quantity'])->getStyle("$columsExcel[5]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($v['quantity']));
                            $htmlReferenceNo = $v['reference_no'];
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, $htmlReferenceNo);
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, (!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? ($dtDataImportDetailNew[$k]['quantity']) : ''))->getStyle("$columsExcel[7]$row")->getNumberFormat()->setFormatCode((!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? formatNumberExcel($dtDataImportDetailNew[$k]['quantity']) : ''));

                            $reference_no_new = (!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? ($dtDataImportDetailNew[$k]['reference_no']) : '');
                            $id_new = (!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? ($dtDataImportDetailNew[$k]['id']) : '');
                            $object_type = (!empty($dtDataImportDetailNew) && !empty($dtDataImportDetailNew[$k]) ? ($dtDataImportDetailNew[$k]['object_type']) : '');
                            $htmlReferenceNoNew = $reference_no_new;
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row, $htmlReferenceNoNew);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, (!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? ($dtDataExportDetailNew[$k]['quantity']) : ''))->getStyle("$columsExcel[5]$row")->getNumberFormat()->setFormatCode((!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? formatNumberExcel($dtDataExportDetailNew[$k]['quantity']) : ''));
                            $reference_no_new = (!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? ($dtDataExportDetailNew[$k]['reference_no']) : '');
                            $id_new = (!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? ($dtDataExportDetailNew[$k]['id']) : '');
                            $object_type = (!empty($dtDataExportDetailNew) && !empty($dtDataExportDetailNew[$k]) ? ($dtDataExportDetailNew[$k]['object_type']) : '');
                            $htmlReferenceNoNew = $reference_no_new;
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, $htmlReferenceNoNew);
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, $v['quantity'])->getStyle("$columsExcel[7]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($v['quantity']));
                            $htmlReferenceNo = $v['reference_no'];
                            $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row, $htmlReferenceNo);
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[10] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[12] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, '');
                        $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, '');
                    }
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle('A3:O' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A3:O' . $row)->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(20);
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

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('bao_cao_tong_hop_ton_kho_nvl') . '.xls';
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

    public function syntheticProductionReport(){
        if (!$this->preViewSyntheticProductionReport) {
            access_denied();
        }
        $data = [];
        $title = lang('Báo Cáo Tổng Hợp BCKPH');
        $data['title'] = $title;
        $this->db->select('staffid, CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, "")) as fullname');
        $data['staff'] = $this->db->get_where('tblstaff')->result_array();
        $this->load->view('admin/reports_manu/synthetic_production_report', $data);
    }

    public function getSyntheticProductionReport(){
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $staff_task = $this->input->post('staff_task');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbl_room.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            JOIN tbl_room ON tbl_room.id = tbldepartments.room_id
            GROUP BY tblstaff_departments.staffid
        ) tb_department";
        $aColumns = [
            'tblproduction_report.id as id',
            'tblbranch.name as name_brand',
            'tb_department.name_department as name_department',
            'tblstaff.code as code_staff',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff',
            'tblproduction_report.reference_no as reference_no',
            'tblproduction_report.reference_no as reference_no_vp',
            'tbl_violation_group.name as name_violation_group',
            'tblproduction_report.countViolate as countViolate',
            'tblproduction_report.date as date_production_report',
            'tbltrouble.name as name_trouble',
            'tblproduction_report.damage_cost as damage_cost',
            '"" as "3"',
            '"" as "4"',
            '"" as "5"',
            '"" as "6"',
            '"" as "7"',
            '"" as "8"',
            '"" as "9"',
            '"" as "10"',
            '"" as "11"',
            '"" as "12"',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblproduction_report';

        $where = [
        ];

        if (!empty($start_date_search)){
            $start_date_search = to_sql_date($start_date_search) .' 00:00:00';
            array_push($where,'AND tblproduction_report.date >= "'.$start_date_search.'"');
        }

        if (!empty($end_date_search)){
            $end_date_search = to_sql_date($end_date_search) .' 23:59:59';
            array_push($where,'AND tblproduction_report.date <= "'.$end_date_search.'"');
        }

        if (!empty($staff_task)){
            array_push($where,'AND tblproduction_report.staff_responsible IN('.implode(',',$staff_task).')');
        }

        $join = [
            'INNER JOIN tblbranch ON tblbranch.id = tblproduction_report.id_branch',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tblproduction_report.staff_responsible',
            'LEFT JOIN '.$tbDepartment.' ON tb_department.staffid = tblstaff.staffid',
            'LEFT JOIN tbl_violation_group ON tbl_violation_group.id = tblproduction_report.violation_group',
            'LEFT JOIN tbltrouble ON tbltrouble.id = tblproduction_report.id_trouble',
            'LEFT JOIN tbl_suggest_bonus_disciplines ON tbl_suggest_bonus_disciplines.production_report_id = tblproduction_report.id',
            'LEFT JOIN tbl_quota_bonus_discipline ON tbl_quota_bonus_discipline.id = tbl_suggest_bonus_disciplines.quota_bonus_disciplines_id',
            'LEFT JOIN tbl_quota_precious ON tbl_quota_precious.quota_id = tbl_quota_bonus_discipline.id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblproduction_report.type_report',
            'tbl_suggest_bonus_disciplines.date as date_suggest_bonus',
            'tbl_quota_precious.form as name_suggest_bonus',
        ], '', []);
        $output = $result['output'];
        $rResult = $result['rResult'];

        $arrProductionId = [-1];
        foreach ($rResult as $key => $value){
            $arrProductionId[] = $value['id'];
        }
        $this->db->where('ischeck', '1');
        $this->db->where_in('id_production_report', $arrProductionId);
        $this->db->group_start();
        $this->db->where('type', 'fix');
        $this->db->or_where('type', 'procedure');
        $this->db->group_end();
        $dtData = $this->db->get('tblproduction_report_items')->result_array();
        if (!empty($dtData)) {
            $dtData = array_reduce($dtData, function ($carry, $item) {
                $carry[$item['id_production_report'].'__'.$item['type']][] = $item;
                return $carry;
            });
        }
        $fix = 'fix';
        $procedure = 'procedure';
        foreach ($rResult as $key => $aRow) {
            $id_production_report = $aRow['id'];
            $dtFix = !empty($dtData[$id_production_report.'__'.$fix]) ? $dtData[$id_production_report.'__'.$fix] : [];
            $dtProcedure = !empty($dtData[$id_production_report.'__'.$procedure]) ? $dtData[$id_production_report.'__'.$procedure] : [];
            $type_report = $aRow['type_report'];
            $row = array();
            $htmlFix = '';
            $htmlProcedure = '';
            if (!empty($dtFix)){
                foreach ($dtFix as $k => $v){
                    $htmlFix .= '<div>'.$v['name'].'</div>';
                }
            }

            if (!empty($dtProcedure)){
                foreach ($dtProcedure as $k => $v){
                    $htmlProcedure .= '<div>'.$v['name'].'</div>';
                }
            }

            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.$aRow['name_brand'].'</div>';
            $row[] = '<div class="text-left" style="min-width:100px">'.$aRow['name_department'].'</div>';
            $row[] = '<div class="text-left" style="min-width:100px">'.$aRow['code_staff'].'</div>';
            $row[] = '<div class="text-left" style="min-width:100px">'.$aRow['name_staff'].'</div>';
            if ($type_report == 4){
                $row[] = '<div class="text-left" style="min-width:100px"></div>';
                $row[] = '<div class="text-left" style="min-width:120px"><a class="c_modal" href="' . (admin_url('production_report/modal/' . $aRow['id'])) . '">'.$aRow['reference_no_vp'].'</div>';
            } else {
                $row[] = '<div class="text-left" style="min-width:120px"><a class="c_modal" href="' . (admin_url('production_report/modal/' . $aRow['id'])) . '">'.$aRow['reference_no'].'</a></div>';
                $row[] = '<div class="text-left" style="min-width:100px"></div>';
            }
            $row[] = '<div class="text-left" style="min-width:100px">'.$aRow['name_violation_group'].'</div>';
            $row[] = '<div class="text-center" style="min-width:100px">'.$aRow['countViolate'].'</div>';
            $row[] = '<div class="text-left" style="min-width:100px">'.(!empty($aRow['date_production_report']) ? _dt($aRow['date_production_report']) : '').'</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.$aRow['name_trouble'].'</div>';
            $row[] = '<div class="text-right" style="min-width:100px">'.formatMoney($aRow['damage_cost']).'</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.$htmlProcedure.'</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.$htmlFix.'</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.(!empty($aRow['date_production_report']) ? _dt($aRow['date_production_report']) : '').'</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.(!empty($aRow['date_production_report']) ? _dt($aRow['date_production_report']) : '').'</div>';
            $row[] = '<div class="text-left" style="width:100px"></div>';
            $row[] = '<div class="text-left" style="width:100px"></div>';
            $row[] = '<div class="text-left" style="width:100px"></div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['name_suggest_bonus'].'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.(!empty($aRow['date_suggest_bonus']) ? _dt($aRow['date_suggest_bonus']) : '').'</div>';
            $row[] = '<div class="text-left" style="width:100px"></div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcelSyntheticProductionReport()
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
            $inputFileName = 'uploads/import_ch/bao_cao_tong_hop_bckph.xlsx';
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
            $staff_task = $this->input->post('staff_task');

            $row = 2;
            $staff_id = get_staff_user_id();
            $tbDepartment = "(
                SELECT
                    tblstaff_departments.staffid as staffid,
                    GROUP_CONCAT(tbl_room.name) as name_department
                FROM tbldepartments
                JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                JOIN tbl_room ON tbl_room.id = tbldepartments.room_id
                GROUP BY tblstaff_departments.staffid
            ) tb_department";
            $this->db->select(
                'tblproduction_report.id as id,
                tblbranch.name as name_brand,
                tb_department.name_department as name_department,
                tblstaff.code as code_staff,
                CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff,
                tblproduction_report.reference_no as reference_no,
                tblproduction_report.reference_no as reference_no_vp,
                tbl_violation_group.name as name_violation_group,
                tblproduction_report.countViolate as countViolate,
                tblproduction_report.date as date_production_report,
                tbltrouble.name as name_trouble,
                tblproduction_report.damage_cost as damage_cost,
                tblproduction_report.type_report,
                tbl_suggest_bonus_disciplines.date as date_suggest_bonus,
                tbl_quota_precious.form as name_suggest_bonus,
            ');
            $this->db->from('tblproduction_report');
            $this->db->join('tblbranch','tblbranch.id = tblproduction_report.id_branch');
            $this->db->join('tblstaff','tblstaff.staffid = tblproduction_report.staff_responsible','left');
            $this->db->join($tbDepartment,'tb_department.staffid = tblstaff.staffid','left');
            $this->db->join('tbl_violation_group','tbl_violation_group.id = tblproduction_report.violation_group','left');
            $this->db->join('tbltrouble','tbltrouble.id = tblproduction_report.id_trouble','left');
            $this->db->join('tbl_suggest_bonus_disciplines','tbl_suggest_bonus_disciplines.production_report_id = tblproduction_report.id','left');
            $this->db->join('tbl_quota_bonus_discipline','tbl_quota_bonus_discipline.id = tbl_suggest_bonus_disciplines.quota_bonus_disciplines_id','left');
            $this->db->join('tbl_quota_precious','tbl_quota_precious.quota_id = tbl_quota_bonus_discipline.id','left');
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) .' 00:00:00';
                $this->db->where("tblproduction_report.date >= '".$start_date_search."'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) .' 23:59:59';
                $this->db->where("tblproduction_report.date <= '".$end_date_search."'");
            }

            if (!empty($staff_task)){
                $this->db->where('tblproduction_report.staff_responsible IN('.implode(',',$staff_task).')');
            }

            $this->db->order_by('tblproduction_report.id desc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $arrProductionId = [-1];
            foreach ($items as $key => $value){
                $arrProductionId[] = $value['id'];
            }
            $this->db->where('ischeck', '1');
            $this->db->where_in('id_production_report', $arrProductionId);
            $this->db->group_start();
            $this->db->where('type', 'fix');
            $this->db->or_where('type', 'procedure');
            $this->db->group_end();
            $dtData = $this->db->get('tblproduction_report_items')->result_array();
            if (!empty($dtData)) {
                $dtData = array_reduce($dtData, function ($carry, $item) {
                    $carry[$item['id_production_report'].'__'.$item['type']][] = $item;
                    return $carry;
                });
            }
            $fix = 'fix';
            $procedure = 'procedure';
            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $id_production_report = $value['id'];
                $dtFix = !empty($dtData[$id_production_report.'__'.$fix]) ? $dtData[$id_production_report.'__'.$fix] : [];
                $dtProcedure = !empty($dtData[$id_production_report.'__'.$procedure]) ? $dtData[$id_production_report.'__'.$procedure] : [];
                $type_report = $value['type_report'];
                $htmlFix = '';
                $htmlProcedure = '';
                if (!empty($dtFix)){
                    foreach ($dtFix as $k => $v){
                        $htmlFix .= $v['name']."\n";
                    }
                }

                if (!empty($dtProcedure)){
                    foreach ($dtProcedure as $k => $v){
                        $htmlProcedure .= $v['name']."\n";
                    }
                }
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, ($value['name_brand']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, ($value['name_department']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['code_staff']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['name_staff']), PHPExcel_Cell_DataType::TYPE_STRING);
                if ($type_report == 4){
                    $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, '');
                    $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, $value['reference_no_vp']);
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, $value['reference_no_vp']);
                    $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, '');
                }
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['name_violation_group']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['countViolate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, (!empty($value['date_production_report']) ? _dt($value['date_production_report']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['name_trouble']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, ($value['damage_cost']))->getStyle("$columsExcel[11]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['damage_cost']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, $htmlProcedure, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, $htmlFix, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, (!empty($value['date_production_report']) ? _dt($value['date_production_report']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, (!empty($value['date_production_report']) ? _dt($value['date_production_report']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[19] . $row, $value['name_suggest_bonus'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, (!empty($value['date_suggest_bonus']) ? _dt($value['date_suggest_bonus']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[21] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A3:V' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A3:V' . $row)->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(20);
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

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('bao_cao_tong_hop_bckph') . '.xls';
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
