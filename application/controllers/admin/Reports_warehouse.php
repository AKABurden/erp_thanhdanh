<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports_warehouse extends AdminController
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

        $this->preViewSyntheticProductWarehouse = true;
        $this->preViewSyntheticMaterialWarehouse = true;
    }

    public function syntheticProductWarehouse(){
        if (!$this->preViewSyntheticProductWarehouse) {
            access_denied();
        }
        $data = [];
        $title = lang('Báo Cáo Tổng Hợp Tồn Kho Thành Phẩm');
        $data['title'] = $title;
        $this->load->view('admin/reports_warehouse/synthetic_product_warehouse', $data);
    }

    public function getSyntheticProductWarehouse(){
        $category_search = $this->input->post('category_search');
        $products_search = $this->input->post('products_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $quantityInventory = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_products.id AND tblwarehouse_items.type_items = 'product' AND tblwarehouse.supplier_id = 0
        )";



        $aColumns = [
            'tbl_products.id as id',
            'tbl_brand.name as name_brand',
            'tbl_products.code as code',
            'tbl_products.name as name',
            "COALESCE($quantityInventory, 0) as quantity_invertory",
            'tbl_products.allowable as allowable',
            '0 as quantity_export',
            '0 as quantity_import',
            '0 as quantity_old',
            'tbl_products.time_stock as time_stock',
            '"" as overdue_warning',
            '"" as date_foso',
            '"" as date_adjust',
            '"" as date_not_use',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_products';

        $where = [];
        if (!empty($category_search)){
            array_push($where,'AND tbl_products.category_id = '.$category_search.'');
        }

        if (!empty($products_search)){
            $products_search = explode('__',$products_search);
            array_push($where,'AND tbl_products.id = '.$products_search[0].'');
        }

        $join = [
            'LEFT JOIN tbl_brand ON tbl_brand.id = tbl_products.brand_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $arrProductId = [0];
        foreach ($rResult as $key => $value){
            $arrProductId[] = $value['id'];
        }
        $tb_export = "(
            SELECT 
                tb_export.item_id,
                SUM(tb_export.quantity) as quantity
            FROM
            (
            SELECT 
                tbl_delivery_items.item_id as item_id,
                tbl_delivery_items.quantity_stock as quantity
            FROM tbl_deliveries
            INNER JOIN tbl_delivery_items ON tbl_delivery_items.delivery_id = tbl_deliveries.id
            WHERE tbl_delivery_items.type_item IN ('products','semi_products') AND tbl_delivery_items.item_id IN (" . implode(',', $arrProductId) . ")
            
            UNION ALL
            
            SELECT 
                tblreturn_suppliers_items.product_id as item_id,
                tblreturn_suppliers_items.quantity_net as quantity
            FROM tblreturn_suppliers
            INNER JOIN tblreturn_suppliers_items ON tblreturn_suppliers_items.id_return = tblreturn_suppliers.id
            WHERE tblreturn_suppliers_items.type IN ('product') AND tblreturn_suppliers_items.product_id IN (" . implode(',', $arrProductId) . ")
            
            UNION ALL
            
            SELECT 
                tbladjusted_items.product_id as item_id,
                tbladjusted_items.quantity_net as quantity
            FROM tbladjusted
            INNER JOIN tbladjusted_items ON tbladjusted_items.id_adjusted = tbladjusted.id
            WHERE tbladjusted_items.type IN ('product') AND tbladjusted.type = 2 AND tbladjusted_items.product_id IN (" . implode(',', $arrProductId) . ")
            
            UNION ALL
            
            SELECT 
                tbltransfer_warehouse_detail.id_items as item_id,
                tbltransfer_warehouse_detail.quantity_net as quantity
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse_detail.type IN ('product') AND tbltransfer_warehouse_detail.id_items IN (" . implode(',', $arrProductId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_suggest_exporting_items.item_id as item_id,
                tbl_suggest_exporting_items.quantity_warehouse as quantity
            FROM tbl_suggest_exporting
            INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
            WHERE tbl_suggest_exporting_items.type_item IN ('products','semi_products') AND tbl_suggest_exporting_items.item_id IN (" . implode(',', $arrProductId) . ")
            
            UNION ALL
            
            SELECT 
                tbltblexport_different_items.product_id as item_id,
                tbltblexport_different_items.quantity_net as quantity
            FROM tblexport_different
            INNER JOIN tbltblexport_different_items ON tbltblexport_different_items.id_export_different = tblexport_different.id
            WHERE tbltblexport_different_items.type IN ('product') AND tbltblexport_different_items.product_id IN (" . implode(',', $arrProductId) . ")
            ) tb_export
            GROUP BY tb_export.item_id
        ) ";
        $dtDataExport = $this->db->query($tb_export)->result_array();
        if (!empty($dtDataExport)) {
            $dtDataExport = array_reduce($dtDataExport, function ($carry, $item) {
                $carry[$item['item_id']] = $item;
                return $carry;
            });
        }

        $tb_import = "(
            SELECT 
                tb_import.item_id,
                SUM(tb_import.quantity) as quantity
            FROM
            (
            SELECT 
                tblimport_items.product_id as item_id,
                tblimport_items.quantity_stock as quantity
            FROM tblimport
            INNER JOIN tblimport_items ON tblimport_items.id_import = tblimport.id
            WHERE tblimport_items.type IN ('product') AND tblimport_items.product_id IN (" . implode(',', $arrProductId) . ")
            
            UNION ALL
            
            SELECT 
                tbladjusted_items.product_id as item_id,
                tbladjusted_items.quantity_net as quantity
            FROM tbladjusted
            INNER JOIN tbladjusted_items ON tbladjusted_items.id_adjusted = tbladjusted.id
            WHERE tbladjusted_items.type IN ('product') AND tbladjusted.type = 1 AND tbladjusted_items.product_id IN (" . implode(',', $arrProductId) . ")
            
            UNION ALL
            
            SELECT 
                tbltransfer_warehouse_detail.id_items as item_id,
                tbltransfer_warehouse_detail.quantity_net as quantity
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse_detail.type IN ('product') AND tbltransfer_warehouse_detail.id_items IN (" . implode(',', $arrProductId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_purchase_product_items.item_id as item_id,
                tbl_purchase_product_items.quantity as quantity
            FROM tbl_purchase_products
            INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
            WHERE tbl_purchase_product_items.type_item IN ('products','semi_products') AND tbl_purchase_product_items.item_id IN (" . implode(',', $arrProductId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_purchase_internal_items.item_id as item_id,
                tbl_purchase_internal_items.quantity as quantity
            FROM tbl_purchase_internal
            INNER JOIN tbl_purchase_internal_items ON tbl_purchase_internal_items.purchase_internal_id = tbl_purchase_internal.id
            WHERE tbl_purchase_internal_items.type_item IN ('product') AND tbl_purchase_internal_items.item_id IN (" . implode(',', $arrProductId) . ")
            
            UNION ALL
            
            SELECT 
                tbl_returned_goods_items.item_id as item_id,
                tbl_returned_goods_items.quantity as quantity
            FROM tbl_returned_goods
            INNER JOIN tbl_returned_goods_items ON tbl_returned_goods_items.returned_goods_id = tbl_returned_goods.id
            WHERE tbl_returned_goods_items.type_item IN ('product') AND tbl_returned_goods_items.item_id IN (" . implode(',', $arrProductId) . ")
            ) tb_import
            GROUP BY tb_import.item_id
        ) ";
        $dtDataImport= $this->db->query($tb_import)->result_array();
        if (!empty($dtDataImport)) {
            $dtDataImport = array_reduce($dtDataImport, function ($carry, $item) {
                $carry[$item['item_id']] = $item;
                return $carry;
            });
        }


        foreach ($rResult as $key => $aRow) {
            $product_id = $aRow['id'];
            $this->db->select('tbl_purchase_products.date');
            $this->db->from('tbl_purchase_products');
            $this->db->join('tbl_purchase_product_items','tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
            $this->db->where('tbl_purchase_product_items.item_id',$product_id);
            $this->db->where("tbl_purchase_product_items.type_item IN ('products','semi_products')");
            $this->db->order_by('tbl_purchase_products.date desc');
            $this->db->limit(1);
            $dtPurchaseProduct = $this->db->get()->row_array();
            $date_purchase = !empty($dtPurchaseProduct) ? $dtPurchaseProduct['date'] : null;

            $this->db->select('tbladjusted.date');
            $this->db->from('tbladjusted');
            $this->db->join('tbladjusted_items','tbladjusted_items.id_adjusted = tbladjusted.id');
            $this->db->where('tbladjusted_items.product_id',$product_id);
            $this->db->where("tbladjusted_items.type IN ('product')");
            $this->db->order_by('tbladjusted.date desc');
            $this->db->limit(1);
            $dtDieuChinh = $this->db->get()->row_array();
            $date_adjust = !empty($dtDieuChinh) ? $dtDieuChinh['date'] : null;

            $time_stock = $aRow['time_stock'];
            $overdue_warning = null;
            if (!empty($date_purchase)){
                $dateEndTamp = strtotime("+$time_stock days", strtotime($date_purchase));
                $dateEnd = date("Y-m-d", $dateEndTamp);
                if (strtotime($dateEnd) > strtotime(date('Y-m-d'))){
                    $overdue_warning = 'Quá hạn';
                }
            }

            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$aRow['name_brand'].'</div>';
            $row[] = '<div class="text-left" style="min-width:150px">'.$aRow['code'].'</div>';
            $row[] = '<div class="text-left" style="min-width:100px">'.$aRow['name'].'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.formatNumber($aRow['quantity_invertory']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.formatNumber($aRow['allowable']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataExport[$product_id]) ? formatNumber($dtDataExport[$product_id]['quantity']) : '').'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.(!empty($dtDataImport[$product_id]) ? formatNumber($dtDataImport[$product_id]['quantity']) : '').'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.formatNumber($aRow['quantity_invertory']).'</div>';
            $row[] = '<div class="text-center" style="width:100px">'.($aRow['time_stock']).'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.$overdue_warning.'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.(!empty($date_purchase) ? _dt($date_purchase) : '').'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.(!empty($date_adjust) ? _dt($date_adjust) : '').'</div>';
            $row[] = '<div class="text-left" style="width:100px">'.($aRow['date_not_use']).'</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcelWarehouseProduct()
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
            $inputFileName = 'uploads/import_ch/bao_cao_tong_hop_ton_kho_thanh_pham.xlsx';
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
            $products_search = $this->input->post('products_search');
            $row = 2;
            $staff_id = get_staff_user_id();
            $quantityInventory = "(
                SELECT
                    SUM(tblwarehouse_items.product_quantity)
                FROM tblwarehouse_items
                INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                WHERE tblwarehouse_items.id_items = tbl_products.id AND tblwarehouse_items.type_items = 'product' AND tblwarehouse.supplier_id = 0
            )";
            $this->db->select(
                'tbl_products.id as id,
                tbl_brand.name as name_brand,
                tbl_products.code as code,
                tbl_products.name as name,
                COALESCE('.$quantityInventory.', 0) as quantity_invertory,
                tbl_products.allowable as allowable,
                0 as quantity_export,
                0 as quantity_import,
                0 as quantity_old,
                tbl_products.time_stock as time_stock,
                "" as overdue_warning,
                "" as date_foso,
                "" as date_adjust,
                "" as date_not_use,
            ');
            $this->db->from('tbl_products');
            $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
            if (!empty($category_search)) {
                $this->db->where("tbl_products.category_id = '" . $category_search . "'");
            }

            if (!empty($products_search)) {
                $products_search = explode('__',$products_search);
                $this->db->where("tbl_products.id = '" . $products_search[0] . "'");
            }
            $this->db->order_by('tbl_products.id desc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $arrProductId = [0];
            foreach ($items as $key => $value){
                $arrProductId[] = $value['id'];
            }
            $tb_export = "(
                SELECT 
                    tb_export.item_id,
                    SUM(tb_export.quantity) as quantity
                FROM
                (
                SELECT 
                    tbl_delivery_items.item_id as item_id,
                    tbl_delivery_items.quantity_stock as quantity
                FROM tbl_deliveries
                INNER JOIN tbl_delivery_items ON tbl_delivery_items.delivery_id = tbl_deliveries.id
                WHERE tbl_delivery_items.type_item IN ('products','semi_products') AND tbl_delivery_items.item_id IN (" . implode(',', $arrProductId) . ")
                
                UNION ALL
                
                SELECT 
                    tblreturn_suppliers_items.product_id as item_id,
                    tblreturn_suppliers_items.quantity_net as quantity
                FROM tblreturn_suppliers
                INNER JOIN tblreturn_suppliers_items ON tblreturn_suppliers_items.id_return = tblreturn_suppliers.id
                WHERE tblreturn_suppliers_items.type IN ('product') AND tblreturn_suppliers_items.product_id IN (" . implode(',', $arrProductId) . ")
                
                UNION ALL
                
                SELECT 
                    tbladjusted_items.product_id as item_id,
                    tbladjusted_items.quantity_net as quantity
                FROM tbladjusted
                INNER JOIN tbladjusted_items ON tbladjusted_items.id_adjusted = tbladjusted.id
                WHERE tbladjusted_items.type IN ('product') AND tbladjusted.type = 2 AND tbladjusted_items.product_id IN (" . implode(',', $arrProductId) . ")
                
                UNION ALL
                
                SELECT 
                    tbltransfer_warehouse_detail.id_items as item_id,
                    tbltransfer_warehouse_detail.quantity_net as quantity
                FROM tbltransfer_warehouse
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
                WHERE tbltransfer_warehouse_detail.type IN ('product') AND tbltransfer_warehouse_detail.id_items IN (" . implode(',', $arrProductId) . ")
                
                UNION ALL
                
                SELECT 
                    tbl_suggest_exporting_items.item_id as item_id,
                    tbl_suggest_exporting_items.quantity_warehouse as quantity
                FROM tbl_suggest_exporting
                INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                WHERE tbl_suggest_exporting_items.type_item IN ('products','semi_products') AND tbl_suggest_exporting_items.item_id IN (" . implode(',', $arrProductId) . ")
                
                UNION ALL
                
                SELECT 
                    tbltblexport_different_items.product_id as item_id,
                    tbltblexport_different_items.quantity_net as quantity
                FROM tblexport_different
                INNER JOIN tbltblexport_different_items ON tbltblexport_different_items.id_export_different = tblexport_different.id
                WHERE tbltblexport_different_items.type IN ('product') AND tbltblexport_different_items.product_id IN (" . implode(',', $arrProductId) . ")
                ) tb_export
                GROUP BY tb_export.item_id
            ) ";
            $dtDataExport = $this->db->query($tb_export)->result_array();
            if (!empty($dtDataExport)) {
                $dtDataExport = array_reduce($dtDataExport, function ($carry, $item) {
                    $carry[$item['item_id']] = $item;
                    return $carry;
                });
            }

            $tb_import = "(
                SELECT 
                    tb_import.item_id,
                    SUM(tb_import.quantity) as quantity
                FROM
                (
                SELECT 
                    tblimport_items.product_id as item_id,
                    tblimport_items.quantity_stock as quantity
                FROM tblimport
                INNER JOIN tblimport_items ON tblimport_items.id_import = tblimport.id
                WHERE tblimport_items.type IN ('product') AND tblimport_items.product_id IN (" . implode(',', $arrProductId) . ")
                
                UNION ALL
                
                SELECT 
                    tbladjusted_items.product_id as item_id,
                    tbladjusted_items.quantity_net as quantity
                FROM tbladjusted
                INNER JOIN tbladjusted_items ON tbladjusted_items.id_adjusted = tbladjusted.id
                WHERE tbladjusted_items.type IN ('product') AND tbladjusted.type = 1 AND tbladjusted_items.product_id IN (" . implode(',', $arrProductId) . ")
                
                UNION ALL
                
                SELECT 
                    tbltransfer_warehouse_detail.id_items as item_id,
                    tbltransfer_warehouse_detail.quantity_net as quantity
                FROM tbltransfer_warehouse
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
                WHERE tbltransfer_warehouse_detail.type IN ('product') AND tbltransfer_warehouse_detail.id_items IN (" . implode(',', $arrProductId) . ")
                
                UNION ALL
                
                SELECT 
                    tbl_purchase_product_items.item_id as item_id,
                    tbl_purchase_product_items.quantity as quantity
                FROM tbl_purchase_products
                INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
                WHERE tbl_purchase_product_items.type_item IN ('products','semi_products') AND tbl_purchase_product_items.item_id IN (" . implode(',', $arrProductId) . ")
                
                UNION ALL
                
                SELECT 
                    tbl_purchase_internal_items.item_id as item_id,
                    tbl_purchase_internal_items.quantity as quantity
                FROM tbl_purchase_internal
                INNER JOIN tbl_purchase_internal_items ON tbl_purchase_internal_items.purchase_internal_id = tbl_purchase_internal.id
                WHERE tbl_purchase_internal_items.type_item IN ('product') AND tbl_purchase_internal_items.item_id IN (" . implode(',', $arrProductId) . ")
                
                UNION ALL
                
                SELECT 
                    tbl_returned_goods_items.item_id as item_id,
                    tbl_returned_goods_items.quantity as quantity
                FROM tbl_returned_goods
                INNER JOIN tbl_returned_goods_items ON tbl_returned_goods_items.returned_goods_id = tbl_returned_goods.id
                WHERE tbl_returned_goods_items.type_item IN ('product') AND tbl_returned_goods_items.item_id IN (" . implode(',', $arrProductId) . ")
                ) tb_import
                GROUP BY tb_import.item_id
            ) ";
            $dtDataImport= $this->db->query($tb_import)->result_array();
            if (!empty($dtDataImport)) {
                $dtDataImport = array_reduce($dtDataImport, function ($carry, $item) {
                    $carry[$item['item_id']] = $item;
                    return $carry;
                });
            }
            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $product_id = $value['id'];
                $this->db->select('tbl_purchase_products.date');
                $this->db->from('tbl_purchase_products');
                $this->db->join('tbl_purchase_product_items','tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
                $this->db->where('tbl_purchase_product_items.item_id',$product_id);
                $this->db->where("tbl_purchase_product_items.type_item IN ('products','semi_products')");
                $this->db->order_by('tbl_purchase_products.date desc');
                $this->db->limit(1);
                $dtPurchaseProduct = $this->db->get()->row_array();
                $date_purchase = !empty($dtPurchaseProduct) ? $dtPurchaseProduct['date'] : null;

                $this->db->select('tbladjusted.date');
                $this->db->from('tbladjusted');
                $this->db->join('tbladjusted_items','tbladjusted_items.id_adjusted = tbladjusted.id');
                $this->db->where('tbladjusted_items.product_id',$product_id);
                $this->db->where("tbladjusted_items.type IN ('product')");
                $this->db->order_by('tbladjusted.date desc');
                $this->db->limit(1);
                $dtDieuChinh = $this->db->get()->row_array();
                $date_adjust = !empty($dtDieuChinh) ? $dtDieuChinh['date'] : null;

                $time_stock = $value['time_stock'];
                $overdue_warning = null;
                if (!empty($date_purchase)){
                    $dateEndTamp = strtotime("+$time_stock days", strtotime($date_purchase));
                    $dateEnd = date("Y-m-d", $dateEndTamp);
                    if (strtotime($dateEnd) > strtotime(date('Y-m-d'))){
                        $overdue_warning = 'Quá hạn';
                    }
                }
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['name_brand'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, ($value['code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[4] . $row, ($value['quantity_invertory']))->getStyle("$columsExcel[4]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_invertory']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, ($value['allowable']))->getStyle("$columsExcel[5]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['allowable']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, (!empty($dtDataExport[$product_id]) ? ($dtDataExport[$product_id]['quantity']) : ''))->getStyle("$columsExcel[6]$row")->getNumberFormat()->setFormatCode(formatNumberExcel((!empty($dtDataExport[$product_id]) ? ($dtDataExport[$product_id]['quantity']) : 0)));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, (!empty($dtDataImport[$product_id]) ? ($dtDataImport[$product_id]['quantity']) : 0))->getStyle("$columsExcel[7]$row")->getNumberFormat()->setFormatCode(formatNumberExcel((!empty($dtDataImport[$product_id]) ? ($dtDataImport[$product_id]['quantity']) : 0)));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row, ($value['quantity_invertory']))->getStyle("$columsExcel[8]$row")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_invertory']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['time_stock']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, $overdue_warning, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, (!empty($date_purchase) ? _dt($date_purchase) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, (!empty($date_adjust) ? _dt($date_adjust) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, $value['date_not_use'], PHPExcel_Cell_DataType::TYPE_STRING);

            }
            $objPHPExcel->getActiveSheet()->getStyle('A4:N' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:N' . $row)->applyFromArray([
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
            $filename = lang('bao_cao_tong_hop_ton_kho_thanh_pham') . '.xls';
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
}
