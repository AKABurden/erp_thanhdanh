<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_plan_stage extends AdminController
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

        $this->type = 1;
        if (!empty($this->input->get('type'))) {
            $this->type = $this->input->get('type');
        }
        $this->arr = [];
        $this->arr[1] = [
            'id' => 1,
            'title' => lang('dt_moderation_plan_stage_offset'),
        ];
        $this->arr[6] = [
            'id' => 6,
            'title' => lang('dt_moderation_plan_stage_can_mang'),
        ];
        $this->arr[7] = [
            'id' => 7,
            'title' => lang('dt_moderation_plan_stage_phun_bong'),
        ];
        $this->arr[8] = [
            'id' => 8,
            'title' => lang('dt_moderation_plan_stage_boi'),
        ];
        $this->arr[9] = [
            'id' => 9,
            'title' => lang('dt_moderation_plan_stage_be'),
        ];
        $this->arr[17] = [
            'id' => 17,
            'title' => lang('dt_moderation_plan_stage_ep_nhu'),
        ];
        $this->arr[18] = [
            'id' => 18,
            'title' => lang('dt_moderation_plan_stage_cat_tp'),
        ];
        $this->arr[19] = [
            'id' => 19,
            'title' => lang('dt_moderation_plan_stage_kiem_kim_loai'),
        ];
        $this->arr[14] = [
            'id' => 14,
            'title' => lang('dt_moderation_plan_stage_khoan_lo'),
        ];

        $this->preViewModerationPlanStage = true;
        $this->preViewOwnModerationPlanStage = true;
        $this->preEditModerationPlanStage = true;
    }

    public function index()
    {
        if (!$this->preViewModerationPlanStage && !$this->preViewOwnModerationPlanStage) {
            access_denied();
        }
        if (!empty($this->arr[$this->type])){
            $data['title'] = $this->arr[$this->type]['title'];
            $data['type'] = $this->arr[$this->type]['id'];
        } else {
            redirect(admin_url('moderation_plan_stage?type=1'));
        }
        $this->load->view('admin/moderation_plan_stage/index', $data);
    }

    public function getModerationPlanStages()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $type = $this->input->post('type');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_material = "(
            SELECT
                tbl_productions_orders_items_sub.productions_orders_items_id,
                tbl_productions_orders_items_sub.stage_item_id,
                SUM(tbl_productions_orders_items_sub.quantity_primary) as quantity_primary,
                tbl_materials.code as code_material,
                tbl_materials.name as name_material
            FROM tbl_productions_orders_items_sub
            JOIN tbl_materials ON tbl_materials.id = tbl_productions_orders_items_sub.item_id AND tbl_productions_orders_items_sub.type = 'materials'
            GROUP BY tbl_productions_orders_items_sub.item_id,tbl_productions_orders_items_sub.stage_item_id,tbl_productions_orders_items_sub.productions_orders_items_id
        ) tb_material";

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as reference_no',
            'tbl_products.images as images',

            'tbl_productions_orders.is_ptm as is_ptm',
            'tbl_productions_orders.is_color as is_color',
            'tbl_productions_orders.is_layout as is_layout',
            'tbl_productions_orders.is_sewing as is_sewing',
            'tbl_productions_orders.is_npl as is_npl',
            'tbl_productions_orders.is_material as is_material',
            'tbl_productions_orders.is_cutting as is_cutting',
            'tbl_productions_orders.date_npl as date_npl',
            
            'tbl_machines.name as name_machines',
            'tbl_products.height as height',
            'tbl_products.wide as wide',
            'tbl_products.quantity_child_sheet as quantity_child_sheet',
            'tb_material.name_material as name_material',
            'COALESCE(tb_material.quantity_primary,0) as quantity_primary',
            'tbl_productions_orders_items_stages.number_face as number_face',
            'tbl_machines.time_change_size as time_change_size',
            'tbl_machines.soup_ingredients as soup_ingredients',
            'tbl_productions_orders_items_stages.face as face',
            'tbl_productions_orders_items_stages.face_after as face_after',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_productions_orders';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id',
            'INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id',
            'INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages',
            'INNER JOIN tbl_type_productionlist ON tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id',
            'INNER JOIN tbl_machines ON tbl_machines.id = tbl_productions_orders_items_stages.machines',
            'LEFT JOIN ' . $tb_material . ' ON tb_material.productions_orders_items_id = tbl_productions_orders_items.id AND tbl_productions_orders_items_stages.stage_id = tb_material.stage_item_id',
        ];

        array_push($where, 'AND tbl_type_productionlist.id = '.$type.'');
        array_push($where,
            'AND (tbl_productions_orders_items_stages.face != 0 OR tbl_productions_orders_items_stages.face_after != 0)');

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_type_productionlist.id as type_productionlist_id',
            'tbl_products.id as item_id',
            'tbl_stages.id as stage_id',
            'tbl_productions_orders_items_stages.id as pois_id',
            'tbl_productions_orders_items_stages.productions_orders_items_id as poi_id',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $stage_id = $aRow['stage_id'];
            $item_id = $aRow['item_id'];
            $total1 = 0;
            $total2 = 0;
            $total3 = 0;
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['reference_no']) . '</div>';
            if(!empty($aRow['images'])){
                 $images = base_url('uploads/products/'.$aRow['images']);
            } else {
                $images = base_url('assets/images/tnh/no_image.png');
            }
            $row[] = '<div class="text-left"><div class="preview_image" style="width: auto;">
                <div class="display-block contract-attachment-wrapper img">
                    <div style="width:30px; margin: auto;">
                        <a href="'.$images. '" data-lightbox="customer-profile" class="display-block mbot5">
                            <div class="">
                                <img src="' .$images. '" style="border-radius: 50%" />
                            </div>
                        </a>
                    </div>
                </div>
            </div></div>';

            $row[] = '<div class="text-center">' . ($aRow['is_ptm'] ? '<span class="text-primary">✓</span>' : '-') . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['is_color'] ? '<span class="text-primary">✓</span>' : '-') . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['is_layout'] ? '<span class="text-primary">✓</span>' : '-') . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['is_sewing'] ? '<span class="text-primary">✓</span>' : '-') . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['is_npl'] ? '<span class="text-primary">✓</span>' : '-') . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['is_material'] ? '<span class="text-primary">✓</span>' : '-') . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['is_cutting'] ? '<span class="text-primary">✓</span>' : '-') . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['date_npl'] ? _d($aRow['date_npl']) : '-') . '</div>';

            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['name_machines']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['height']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['wide']) . '</div>';
            if ($aRow['face'] != 0) {
                $row[] = '<div class="text-center">' . ($aRow['quantity_child_sheet']) . '</div>';
                $row[] = '<div class="text-left" style="width: 150px">' . $aRow['name_material'] . '</div>';
                $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_primary']) . '</div>';
                $row[] = '<div class="text-center">' . formatNumber($aRow['number_face']) . '</div>';
                $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_primary'] * $aRow['number_face']) . '</div>';
                $row[] = '<div class="text-left">' . $aRow['time_change_size'] . '</div>';
                $row[] = '<div class="text-left">' . $aRow['soup_ingredients'] . '</div>';
                $total1 += $aRow['time_change_size'];
                $total2 += $aRow['soup_ingredients'];
                $total3 += $aRow['quantity_primary'] * $aRow['number_face'];
            } else {
                $row[] = '<div class="text-center"></div>';
                $row[] = '<div class="text-left"></div>';
                $row[] = '<div class="text-center"></div>';
                $row[] = '<div class="text-center"></div>';
                $row[] = '<div class="text-center"></div>';
                $row[] = '<div class="text-left"></div>';
                $row[] = '<div class="text-left"></div>';
            }
            if ($aRow['face_after'] != 0) {
                $row[] = '<div class="text-center">' . ($aRow['quantity_child_sheet']) . '</div>';
                $row[] = '<div class="text-left" style="width: 150px">' . $aRow['name_material'] . '</div>';
                $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_primary']) . '</div>';
                $row[] = '<div class="text-center">' . formatNumber($aRow['number_face']) . '</div>';
                $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_primary'] * $aRow['number_face']) . '</div>';
                $row[] = '<div class="text-left">' . $aRow['time_change_size'] . '</div>';
                $row[] = '<div class="text-left">' . $aRow['soup_ingredients'] . '</div>';
                $total1 += $aRow['time_change_size'];
                $total2 += $aRow['soup_ingredients'];
                $total3 += $aRow['quantity_primary'] * $aRow['number_face'];
            } else {
                $row[] = '<div class="text-center"></div>';
                $row[] = '<div class="text-left"></div>';
                $row[] = '<div class="text-center"></div>';
                $row[] = '<div class="text-center"></div>';
                $row[] = '<div class="text-center"></div>';
                $row[] = '<div class="text-left"></div>';
                $row[] = '<div class="text-left"></div>';
            }
            $row[] = '<div class="text-center">' . formatNumber($total1) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($total2) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($total3) . '</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModerationNew($aRow['id'],$aRow['stage_id'],$vv['id']);
                $row[] = '<div class="text-center">'.$_data.'</div>';
            }

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function updateModerationPlanStage()
    {
        $data = [];
        if (!$this->preEditModerationPlanStage) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $po_id = $this->input->post('po_id');
        $poi_id = $this->input->post('poi_id');
        $pois_id = $this->input->post('pois_id');
        $item_id = $this->input->post('item_id');
        $type_productionlist_id = $this->input->post('type_productionlist_id');
        $stage_id = $this->input->post('stage_id');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        if ($name == 'date_start' || $name == 'date_end') {
            if (!empty($value)) {
                $value = to_sql_date($value, true);
            } else {
                $value = null;
            }
        } elseif ($name == 'total_height' || $name == 'quota_time' || $name == 'time_expected') {
            if (!empty($value)) {
                $value = number_unformat($value);
            } else {
                $value = 0;
            }
        }

        $this->db->from('tbl_moderation_plan_stage');
        $this->db->where('tbl_moderation_plan_stage.po_id', $po_id);
        $this->db->where('tbl_moderation_plan_stage.poi_id', $poi_id);
        $this->db->where('tbl_moderation_plan_stage.pois_id', $pois_id);
        $this->db->where('tbl_moderation_plan_stage.item_id', $item_id);
        $this->db->where('tbl_moderation_plan_stage.type_productionlist_id', $type_productionlist_id);
        $this->db->where('tbl_moderation_plan_stage.stage_id', $stage_id);
        $dtData = $this->db->get()->row_array();
        if (!empty($dtData)) {
            $this->db->where('tbl_moderation_plan_stage.id', $dtData['id']);
            $success = $this->db->update('tbl_moderation_plan_stage', [
                $name => $value
            ]);
        } else {
            $success = $this->db->insert('tbl_moderation_plan_stage', [
                'po_id' => $po_id,
                'poi_id' => $poi_id,
                'pois_id' => $pois_id,
                'item_id' => $item_id,
                'type_productionlist_id' => $type_productionlist_id,
                'stage_id' => $stage_id,
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
            $type = $this->input->post('type');
            if ($type == 1) {
                $inputFileName = 'uploads/import_dt/phieu_dieu_do_cong_doan_in_offset.xlsx';
            } else {
                $inputFileName = 'uploads/import_dt/phieu_dieu_do_cong_doan.xlsx';
            }
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
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$highestRowMin, $vv['name'])->getStyle("$cloumns_excel[$i]$highestRowMin")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$highestRow,'')->getStyle("$cloumns_excel[$i]$highestRow")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->mergeCells($cloumns_excel[($i)].''.($highestRow-1).':'.$cloumns_excel[($i)].$highestRow);
                $i ++;
            }
            $tb_material = "(
                SELECT
                    tbl_productions_orders_items_sub.productions_orders_items_id,
                    tbl_productions_orders_items_sub.stage_item_id,
                    SUM(tbl_productions_orders_items_sub.quantity_primary) as quantity_primary,
                    tbl_materials.code as code_material,
                    tbl_materials.name as name_material
                FROM tbl_productions_orders_items_sub
                JOIN tbl_materials ON tbl_materials.id = tbl_productions_orders_items_sub.item_id AND tbl_productions_orders_items_sub.type = 'materials'
                GROUP BY tbl_productions_orders_items_sub.item_id,tbl_productions_orders_items_sub.stage_item_id,tbl_productions_orders_items_sub.productions_orders_items_id
            ) tb_material";
            $this->db->select('
                tbl_productions_orders.id as id,
                tbl_productions_orders.date as date,
                tbl_productions_orders.reference_no as reference_no,
                tbl_products.images as images,

                tbl_productions_orders.is_ptm as is_ptm,
                tbl_productions_orders.is_color as is_color,
                tbl_productions_orders.is_layout as is_layout,
                tbl_productions_orders.is_sewing as is_sewing,
                tbl_productions_orders.is_npl as is_npl,
                tbl_productions_orders.is_material as is_material,
                tbl_productions_orders.is_cutting as is_cutting,
                tbl_productions_orders.date_npl as date_npl,

                tbl_machines.name as name_machines,
                tbl_products.height as height,
                tbl_products.wide as wide,
                tbl_products.quantity_child_sheet as quantity_child_sheet,
                tb_material.name_material as name_material,
                COALESCE(tb_material.quantity_primary,0) as quantity_primary,
                tbl_productions_orders_items_stages.number_face as number_face,
                tbl_machines.time_change_size as time_change_size,
                tbl_machines.soup_ingredients as soup_ingredients,
                tbl_productions_orders_items_stages.face as face,
                tbl_productions_orders_items_stages.face_after as face_after,
                 tbl_products.id as item_id,
                tbl_stages.id as stage_id,
                tbl_productions_orders_items_stages.id as pois_id,
                tbl_productions_orders_items_stages.productions_orders_items_id as poi_id,
                tbl_moderation_plan_stage.total_height,
                tbl_moderation_plan_stage.quota_time,
                tbl_moderation_plan_stage.time_expected,
                tbl_moderation_plan_stage.date_start,
                tbl_moderation_plan_stage.date_end,
                tbl_moderation_plan_stage.standard,
            ');
            $this->db->from('tbl_productions_orders');
            $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id', 'inner');
            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id');
            $this->db->join('tbl_products','tbl_products.id = tbl_productions_orders_items.items_id','inner');
            $this->db->join('tbl_stages','tbl_stages.id = tbl_productions_orders_items_stages.stage_id','inner');
            $this->db->join('tbl_category_stages','tbl_category_stages.id = tbl_stages.category_stages','inner');
            $this->db->join('tbl_type_productionlist','tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id','inner');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_productions_orders_items_stages.machines', 'inner');
            $this->db->join($tb_material, 'tb_material.productions_orders_items_id = tbl_productions_orders_items.id AND tbl_productions_orders_items_stages.stage_id = tb_material.stage_item_id', 'left');
            $this->db->join('tbl_moderation_plan_stage', 'tbl_moderation_plan_stage.po_id = tbl_productions_orders.id AND tbl_moderation_plan_stage.pois_id = tbl_productions_orders_items_stages.id AND tbl_moderation_plan_stage.stage_id = tbl_stages.id AND tbl_moderation_plan_stage.type_productionlist_id = tbl_type_productionlist.id', 'left');

            $this->db->where("tbl_type_productionlist.id = $type");
            $this->db->where("(tbl_productions_orders_items_stages.face != 0 OR tbl_productions_orders_items_stages.face_after != 0)");

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_productions_orders.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_productions_orders.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_productions_orders.id desc');
            $dtData = $this->db->get()->result_array();

            $title = ($this->arr[$type]['title']);

            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                $title)->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $rowBegin = 3;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $colStt = 0;
                    $rowBegin++;
                    $total1 = 0;
                    $total2 = 0;
                    $total3 = 0;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", (++$key));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", _dt($value['date']));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['reference_no']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    if (!empty($value['images'])) {
                        $images = 'uploads/products/' . $value['images'];
                    }
                    if (empty($images)) {
                        $images = 'assets/images/tnh/no_image.png';
                    }
                    if (!empty($images) && file_exists($images)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($images);
                        $objDrawing1->setWidth(40);
                        $objDrawing1->setHeight(40);
                        $objDrawing1->setOffsetX(40);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates($cloumns_excel[$colStt] . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(40);
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '')->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['is_ptm'] ? '✓' : '-'))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['is_color'] ? '✓' : '-'))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['is_layout'] ? '✓' : '-'))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['is_sewing'] ? '✓' : '-'))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['is_npl'] ? '✓' : '-'))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['is_material'] ? '✓' : '-'))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['is_cutting'] ? '✓' : '-'))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['date_npl'] ? _d($value['date_npl']) : '-'))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    

                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['name_machines']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['height']));
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['wide']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    if ($value['face'] != 0){
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['quantity_child_sheet']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",($value['name_material']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['quantity_primary']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['number_face']));
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['quantity_primary'] *  $value['number_face']));
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['time_change_size']));
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['soup_ingredients']));
                        $colStt++;
                        $total1 += $value['time_change_size'];
                        $total2 += $value['soup_ingredients'];
                        $total3 += $value['quantity_primary'] * $value['number_face'];
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '')->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",'');
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '')->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '');
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '');
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '');
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '');
                        $colStt++;
                    }

                    if ($value['face_after'] != 0){
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['quantity_child_sheet']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",($value['name_material']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['quantity_primary']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['number_face']));
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['quantity_primary'] *  $value['number_face']));
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['time_change_size']));
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['soup_ingredients']));
                        $colStt++;
                        $total1 += $value['time_change_size'];
                        $total2 += $value['soup_ingredients'];
                        $total3 += $value['quantity_primary'] * $value['number_face'];
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '')->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",'');
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '')->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '');
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '');
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '');
                        $colStt++;
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '');
                        $colStt++;
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($total1))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",formatNumber($total2))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($total3))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['total_height']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['standard']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt ++;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModerationNew($value['id'],$value['stage_id'],$vv['id'],true);
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
            $filename = lang('phieu_dieu_do_cong_doan') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(25);
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

    public function moderation_plan_stage_kiem()
    {
        if (!$this->preViewModerationPlanStage && !$this->preViewOwnModerationPlanStage) {
            access_denied();
        }
        $data['title'] = lang('dt_moderation_plan_stage_kiem');
        $this->load->view('admin/moderation_plan_stage/moderation_plan_stage_kiem', $data);
    }

    public function getModerationPlanStagesKiem()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id,
                tbl_productions_orders_items.items_id
            FROM tbl_productions_orders_items
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as reference_no',
            'tbl_products.images as images',
            'tbl_products.code as code_product',
            'tbl_category_products.name as name_category_product',
            'tbl_species.name as name_species',
            'tbl_brand.name as name_brand',
            'tblunits.unit as unit_name',
            'tbl_category_stages.name as name_category_stages',
            'tbl_productions_orders_items_stages.face as face',
            'tbl_productions_orders_items_stages.face_after as face_after',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_productions_orders';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN '.$tbProductionsOrderItems.' ON tb_production_order_item.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tb_production_order_item.items_id',
            'INNER JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id',
            'INNER JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_products.species',
            'LEFT JOIN tbl_brand ON tbl_brand.id = tbl_products.brand_id',
            'INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id',
            'INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages',
            'INNER JOIN tbl_type_productionlist ON tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id',
            'LEFT JOIN tbl_moderation_plan_stage_kiem ON tbl_moderation_plan_stage_kiem.po_id = tbl_productions_orders.id AND tbl_moderation_plan_stage_kiem.stage_id = tbl_stages.id
            AND tbl_moderation_plan_stage_kiem.type_productionlist_id = tbl_type_productionlist.id AND tbl_moderation_plan_stage_kiem.item_id = tb_production_order_item.items_id AND tbl_moderation_plan_stage_kiem.type = 1',
        ];

        array_push($where, 'AND tbl_type_productionlist.id = '.STAGE_TYPE_KIEM.'');
//        array_push($where, 'AND (tbl_productions_orders_items_stages.face != 0 OR tbl_productions_orders_items_stages.face_after != 0)');

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_type_productionlist.id as type_productionlist_id',
            'tbl_products.id as item_id',
            'tbl_stages.id as stage_id',
            'tbl_moderation_plan_stage_kiem.type_customer',
            'tbl_moderation_plan_stage_kiem.type_kiem_1',
            'tbl_moderation_plan_stage_kiem.number_kiem_1',
            'tbl_moderation_plan_stage_kiem.quota_productivity_1',
            'tbl_moderation_plan_stage_kiem.type_kiem_2',
            'tbl_moderation_plan_stage_kiem.number_kiem_2',
            'tbl_moderation_plan_stage_kiem.quota_productivity_2',
            'tbl_moderation_plan_stage_kiem.time_expected',
            'tbl_moderation_plan_stage_kiem.date_start',
            'tbl_moderation_plan_stage_kiem.date_end',
            'tbl_moderation_plan_stage_kiem.standard',
        ], 'GROUP BY tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id,tb_production_order_item.items_id', []);


        $output = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $key => $aRow) {
            $po_id = $aRow['id'];
            $stage_id = $aRow['stage_id'];
            $type_productionlist_id = $aRow['type_productionlist_id'];
            $item_id = $aRow['item_id'];
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            if(!empty($aRow['images'])){
                $images = base_url('uploads/products/'.$aRow['images']);
            } else {
                $images = base_url('assets/images/tnh/no_image.png');
            }
            $row[] = '<div class="text-left"><div class="preview_image" style="width: auto;">
                <div class="display-block contract-attachment-wrapper img">
                    <div style="width:30px; margin: auto;">
                        <a href="'.$images. '" data-lightbox="customer-profile" class="display-block mbot5">
                            <div class="">
                                <img src="' .$images. '" style="border-radius: 50%" />
                            </div>
                        </a>
                    </div>
                </div>
            </div></div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['reference_no']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 150px"><a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/products/view_product/'.$item_id).'" data-toggle="modal" data-target="#myModal">' . ($aRow['code_product']) . '</a></div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['name_category_product']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['name_species']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['name_brand']) . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageKiem(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'type_customer\')" name="type_customer" class="form-control type_customer" value="' . (!empty($aRow['type_customer']) ? ($aRow['type_customer']) : '') . '">
            </div>';
            $row[] = '<div class="text-center">' . ($aRow['unit_name']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['name_category_stages']) . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageKiem(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'type_kiem_1\')" name="type_kiem_1" class="form-control type_kiem_1" value="' . (!empty($aRow['type_kiem_1']) ? ($aRow['type_kiem_1']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageKiem(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'number_kiem_1\')" name="number_kiem_1" class="form-control number_kiem_1 number-format" value="' . (!empty($aRow['number_kiem_1']) ? formatNumber($aRow['number_kiem_1']) : 0) . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageKiem(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'quota_productivity_1\')" name="quota_productivity_1" class="form-control quota_productivity_1 number-format" value="' . (!empty($aRow['quota_productivity_1']) ? formatNumber($aRow['quota_productivity_1']) : 0) . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageKiem(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'type_kiem_2\')" name="type_kiem_2" class="form-control type_kiem_2 " value="' . (!empty($aRow['type_kiem_2']) ? ($aRow['type_kiem_2']) : '') . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageKiem(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'number_kiem_2\')" name="number_kiem_2" class="form-control number_kiem_2 number-format" value="' . (!empty($aRow['number_kiem_2']) ? formatNumber($aRow['number_kiem_2']) : 0) . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageKiem(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'quota_productivity_2\')" name="quota_productivity_2" class="form-control total_height number-format" value="' . (!empty($aRow['quota_productivity_2']) ? formatNumber($aRow['quota_productivity_2']) : 0) . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageKiem(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'standard\')" name="standard" class="form-control standard" value="' . (!empty($aRow['standard']) ? ($aRow['standard']) : '') . '">
            </div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModerationNew($aRow['id'],$aRow['stage_id'],$vv['id']);
                $row[] = '<div class="text-center">'.$_data.'</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function updateModerationPlanStageKiem()
    {
        $data = [];
        if (!$this->preEditModerationPlanStage) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $po_id = $this->input->post('po_id');
        $item_id = $this->input->post('item_id');
        $type_productionlist_id = $this->input->post('type_productionlist_id');
        $stage_id = $this->input->post('stage_id');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        $arrDate = [
            'date_start',
            'date_end'
        ];
        $arrDouble = [
            'number_kiem_1',
            'quota_productivity_1',
            'number_kiem_2',
            'quota_productivity_2',
            'time_expected',
        ];
        if (in_array($name,$arrDate)) {
            if (!empty($value)) {
                $value = to_sql_date($value, true);
            } else {
                $value = null;
            }
        } elseif (in_array($name,$arrDouble)) {
            if (!empty($value)) {
                $value = number_unformat($value);
            } else {
                $value = 0;
            }
        }

        $this->db->from('tbl_moderation_plan_stage_kiem');
        $this->db->where('tbl_moderation_plan_stage_kiem.po_id', $po_id);
        $this->db->where('tbl_moderation_plan_stage_kiem.item_id', $item_id);
        $this->db->where('tbl_moderation_plan_stage_kiem.type_productionlist_id', $type_productionlist_id);
        $this->db->where('tbl_moderation_plan_stage_kiem.stage_id', $stage_id);
        $this->db->where('tbl_moderation_plan_stage_kiem.type', 1);
        $dtData = $this->db->get()->row_array();
        if (!empty($dtData)) {
            $this->db->where('tbl_moderation_plan_stage_kiem.id', $dtData['id']);
            $success = $this->db->update('tbl_moderation_plan_stage_kiem', [
                $name => $value
            ]);
        } else {
            $success = $this->db->insert('tbl_moderation_plan_stage_kiem', [
                'po_id' => $po_id,
                'item_id' => $item_id,
                'type_productionlist_id' => $type_productionlist_id,
                'stage_id' => $stage_id,
                'type' => 1,
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

    public function exportExcelStageKiem(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_dt/phieu_dieu_do_cong_doan_kiem.xlsx';
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
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$highestRowMin, $vv['name'])->getStyle("$cloumns_excel[$i]$highestRowMin")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$highestRow,'')->getStyle("$cloumns_excel[$i]$highestRow")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->mergeCells($cloumns_excel[($i)].''.($highestRow-1).':'.$cloumns_excel[($i)].$highestRow);
                $i ++;
            }

            $tbProductionsOrderItems = "(
                SELECT
                    tbl_productions_orders_items.productions_orders_id,
                    tbl_productions_orders_items.items_id
                FROM tbl_productions_orders_items
                GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
            ) tb_production_order_item";
            $this->db->select('
                tbl_productions_orders.id as id, 
                tbl_productions_orders.date as date,
                tbl_productions_orders.reference_no as reference_no,
                tbl_products.images as images,
                tbl_products.code as code_product,
                tbl_category_products.name as name_category_product,
                tbl_species.name as name_species,
                tbl_brand.name as name_brand,
                tblunits.unit as unit_name,
                tbl_category_stages.name as name_category_stages,
                tbl_productions_orders_items_stages.face as face,
                tbl_productions_orders_items_stages.face_after as face_after,
                tbl_type_productionlist.id as type_productionlist_id,
                tbl_products.id as item_id,
                tbl_stages.id as stage_id,
                tbl_moderation_plan_stage_kiem.type_customer,
                tbl_moderation_plan_stage_kiem.type_kiem_1,
                tbl_moderation_plan_stage_kiem.number_kiem_1,
                tbl_moderation_plan_stage_kiem.quota_productivity_1,
                tbl_moderation_plan_stage_kiem.type_kiem_2,
                tbl_moderation_plan_stage_kiem.number_kiem_2,
                tbl_moderation_plan_stage_kiem.quota_productivity_2,
                tbl_moderation_plan_stage_kiem.standard,
            ');
            $this->db->from('tbl_productions_orders');
            $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id', 'inner');
            $this->db->join($tbProductionsOrderItems,'tb_production_order_item.productions_orders_id = tbl_productions_orders.id');
            $this->db->join('tbl_products','tbl_products.id = tb_production_order_item.items_id','inner');
            $this->db->join('tbl_category_products','tbl_category_products.id = tbl_products.category_id','inner');
            $this->db->join('tbl_stages','tbl_stages.id = tbl_productions_orders_items_stages.stage_id','inner');
            $this->db->join('tbl_category_stages','tbl_category_stages.id = tbl_stages.category_stages','inner');
            $this->db->join('tbl_type_productionlist','tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id','inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
            $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
            $this->db->join('tbl_moderation_plan_stage_kiem', 'tbl_moderation_plan_stage_kiem.po_id = tbl_productions_orders.id AND tbl_moderation_plan_stage_kiem.stage_id = tbl_stages.id
            AND tbl_moderation_plan_stage_kiem.type_productionlist_id = tbl_type_productionlist.id AND tbl_moderation_plan_stage_kiem.item_id = tb_production_order_item.items_id AND tbl_moderation_plan_stage_kiem.type = 1', 'left');

            $this->db->where("tbl_type_productionlist.id = ".STAGE_TYPE_KIEM."");
//            $this->db->where("(tbl_productions_orders_items_stages.face != 0 OR tbl_productions_orders_items_stages.face_after != 0)");

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_productions_orders.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_productions_orders.date <= '" . $end_date_search . "'");
            }

            $this->db->group_by('tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id,tb_production_order_item.items_id');
            $this->db->order_by('tbl_productions_orders.id desc');
            $dtData = $this->db->get()->result_array();

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $rowBegin = 3;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $colStt = 0;
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", (++$key));
                    $colStt++;
                    $images = '';
                    if (!empty($value['images'])) {
                        $images = 'uploads/products/' . $value['images'];
                    }
                    if (empty($images)) {
                        $images = 'assets/images/tnh/no_image.png';
                    }
                    if (!empty($images) && file_exists($images)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($images);
                        $objDrawing1->setWidth(40);
                        $objDrawing1->setHeight(40);
                        $objDrawing1->setOffsetX(40);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates($cloumns_excel[$colStt] . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(40);
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '')->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['reference_no']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", _dt($value['date']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['code_product']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['name_category_product']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['name_species']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['name_brand']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",($value['type_customer']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['unit_name']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['name_category_stages']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['type_kiem_1']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['number_kiem_1']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['quota_productivity_1']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['type_kiem_2']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['number_kiem_2']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['quota_productivity_2']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['standard']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt ++;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModerationNew($value['id'],$value['stage_id'],$vv['id'],true);
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
            $filename = lang('phieu_dieu_do_cong_doan_kiem') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(25);
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

    public function moderation_plan_stage_phan_don()
    {
        if (!$this->preViewModerationPlanStage && !$this->preViewOwnModerationPlanStage) {
            access_denied();
        }
        $data['title'] = lang('dt_moderation_plan_stage_phan_don');
        $this->load->view('admin/moderation_plan_stage/moderation_plan_stage_phan_don', $data);
    }

    public function getModerationPlanStagesPhanDon()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id,
                tbl_productions_orders_items.items_id
            FROM tbl_productions_orders_items
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as reference_no',
            'tbl_products.images as images',
            'tbl_products.code as code_product',
            'tbl_category_products.name as name_category_product',
            'tbl_species.name as name_species',
            'tbl_brand.name as name_brand',
            'tblunits.unit as unit_name',
            'tbl_category_stages.name as name_category_stages',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_productions_orders';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN '.$tbProductionsOrderItems.' ON tb_production_order_item.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tb_production_order_item.items_id',
            'INNER JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id',
            'INNER JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_products.species',
            'LEFT JOIN tbl_brand ON tbl_brand.id = tbl_products.brand_id',
            'INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id',
            'INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages',
            'INNER JOIN tbl_type_productionlist ON tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id',
            'LEFT JOIN tbl_moderation_plan_stage_kiem ON tbl_moderation_plan_stage_kiem.po_id = tbl_productions_orders.id AND tbl_moderation_plan_stage_kiem.stage_id = tbl_stages.id
            AND tbl_moderation_plan_stage_kiem.type_productionlist_id = tbl_type_productionlist.id AND tbl_moderation_plan_stage_kiem.item_id = tb_production_order_item.items_id AND tbl_moderation_plan_stage_kiem.type = 2',
        ];

        array_push($where, 'AND tbl_type_productionlist.id = '.STAGE_TYPE_PHAN_DON.'');

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_type_productionlist.id as type_productionlist_id',
            'tbl_products.id as item_id',
            'tbl_stages.id as stage_id',
            'tbl_moderation_plan_stage_kiem.type_customer',
            'tbl_moderation_plan_stage_kiem.time_expected',
            'tbl_moderation_plan_stage_kiem.date_start',
            'tbl_moderation_plan_stage_kiem.date_end',
            'tbl_moderation_plan_stage_kiem.standard',
            'tbl_moderation_plan_stage_kiem.quantity_size',
            'tbl_moderation_plan_stage_kiem.number_soan',
            'tbl_moderation_plan_stage_kiem.quota_productivity',
            'tbl_moderation_plan_stage_kiem.standard',
        ], 'GROUP BY tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id,tb_production_order_item.items_id', []);


        $output = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $key => $aRow) {
            $po_id = $aRow['id'];
            $stage_id = $aRow['stage_id'];
            $type_productionlist_id = $aRow['type_productionlist_id'];
            $item_id = $aRow['item_id'];
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            if(!empty($aRow['images'])){
                $images = base_url('uploads/products/'.$aRow['images']);
            } else {
                $images = base_url('assets/images/tnh/no_image.png');
            }
            $row[] = '<div class="text-left"><div class="preview_image" style="width: auto;">
                <div class="display-block contract-attachment-wrapper img">
                    <div style="width:30px; margin: auto;">
                        <a href="'.$images. '" data-lightbox="customer-profile" class="display-block mbot5">
                            <div class="">
                                <img src="' .$images. '" style="border-radius: 50%" />
                            </div>
                        </a>
                    </div>
                </div>
            </div></div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['reference_no']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 150px"><a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/products/view_product/'.$item_id).'" data-toggle="modal" data-target="#myModal">' . ($aRow['code_product']) . '</a></div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['name_category_product']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['name_species']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['name_brand']) . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStagePhanDon(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'type_customer\')" name="type_customer" class="form-control type_customer" value="' . (!empty($aRow['type_customer']) ? ($aRow['type_customer']) : '') . '">
            </div>';
            $row[] = '<div class="text-center">' . ($aRow['unit_name']) . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStagePhanDon(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'quantity_size\')" name="quantity_size" class="form-control quantity_size number-format" value="' . (!empty($aRow['quantity_size']) ? formatNumber($aRow['quantity_size']) : 0) . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStagePhanDon(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'number_soan\')" name="number_soan" class="form-control number_soan number-format" value="' . (!empty($aRow['number_soan']) ? formatNumber($aRow['number_soan']) : 0) . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStagePhanDon(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.',' . $stage_id . ',\'standard\')" name="standard" class="form-control standard" value="' . (!empty($aRow['standard']) ? ($aRow['standard']) : '') . '">
            </div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModerationNew($aRow['id'],$aRow['stage_id'],$vv['id']);
                $row[] = '<div class="text-center">'.$_data.'</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function updateModerationPlanStagePhanDon()
    {
        $data = [];
        if (!$this->preEditModerationPlanStage) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $po_id = $this->input->post('po_id');
        $item_id = $this->input->post('item_id');
        $type_productionlist_id = $this->input->post('type_productionlist_id');
        $stage_id = $this->input->post('stage_id');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        $arrDate = [
            'date_start',
            'date_end'
        ];
        $arrDouble = [
            'number_kiem_1',
            'quota_productivity_1',
            'number_kiem_2',
            'quota_productivity_2',
            'time_expected',
        ];
        if (in_array($name,$arrDate)) {
            if (!empty($value)) {
                $value = to_sql_date($value, true);
            } else {
                $value = null;
            }
        } elseif (in_array($name,$arrDouble)) {
            if (!empty($value)) {
                $value = number_unformat($value);
            } else {
                $value = 0;
            }
        }

        $this->db->from('tbl_moderation_plan_stage_kiem');
        $this->db->where('tbl_moderation_plan_stage_kiem.po_id', $po_id);
        $this->db->where('tbl_moderation_plan_stage_kiem.item_id', $item_id);
        $this->db->where('tbl_moderation_plan_stage_kiem.type_productionlist_id', $type_productionlist_id);
        $this->db->where('tbl_moderation_plan_stage_kiem.stage_id', $stage_id);
        $this->db->where('tbl_moderation_plan_stage_kiem.type', 2);
        $dtData = $this->db->get()->row_array();
        if (!empty($dtData)) {
            $this->db->where('tbl_moderation_plan_stage_kiem.id', $dtData['id']);
            $success = $this->db->update('tbl_moderation_plan_stage_kiem', [
                $name => $value
            ]);
        } else {
            $success = $this->db->insert('tbl_moderation_plan_stage_kiem', [
                'po_id' => $po_id,
                'item_id' => $item_id,
                'type_productionlist_id' => $type_productionlist_id,
                'stage_id' => $stage_id,
                'type' => 2,
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

    public function exportExcelStagePhanDon(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_dt/phieu_dieu_do_cong_doan_phan_don.xlsx';
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
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$highestRowMin, $vv['name'])->getStyle("$cloumns_excel[$i]$highestRowMin")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$highestRow,'')->getStyle("$cloumns_excel[$i]$highestRow")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->mergeCells($cloumns_excel[($i)].''.($highestRow-1).':'.$cloumns_excel[($i)].$highestRow);
                $i ++;
            }
            $tbProductionsOrderItems = "(
                SELECT
                    tbl_productions_orders_items.productions_orders_id,
                    tbl_productions_orders_items.items_id
                FROM tbl_productions_orders_items
                GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
            ) tb_production_order_item";
            $this->db->select('
                tbl_productions_orders.id as id, 
                tbl_productions_orders.date as date,
                tbl_productions_orders.reference_no as reference_no,
                tbl_products.images as images,
                tbl_products.code as code_product,
                tbl_category_products.name as name_category_product,
                tbl_species.name as name_species,
                tbl_brand.name as name_brand,
                tblunits.unit as unit_name,
                tbl_type_productionlist.id as type_productionlist_id,
                tbl_products.id as item_id,
                tbl_stages.id as stage_id,
                tbl_moderation_plan_stage_kiem.type_customer,
                tbl_moderation_plan_stage_kiem.quantity_size,
                tbl_moderation_plan_stage_kiem.number_soan,
                tbl_moderation_plan_stage_kiem.standard,
            ');
            $this->db->from('tbl_productions_orders');
            $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id', 'inner');
            $this->db->join($tbProductionsOrderItems,'tb_production_order_item.productions_orders_id = tbl_productions_orders.id');
            $this->db->join('tbl_products','tbl_products.id = tb_production_order_item.items_id','inner');
            $this->db->join('tbl_category_products','tbl_category_products.id = tbl_products.category_id','inner');
            $this->db->join('tbl_stages','tbl_stages.id = tbl_productions_orders_items_stages.stage_id','inner');
            $this->db->join('tbl_category_stages','tbl_category_stages.id = tbl_stages.category_stages','inner');
            $this->db->join('tbl_type_productionlist','tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id','inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
            $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
            $this->db->join('tbl_moderation_plan_stage_kiem', 'tbl_moderation_plan_stage_kiem.po_id = tbl_productions_orders.id AND tbl_moderation_plan_stage_kiem.stage_id = tbl_stages.id
            AND tbl_moderation_plan_stage_kiem.type_productionlist_id = tbl_type_productionlist.id AND tbl_moderation_plan_stage_kiem.item_id = tb_production_order_item.items_id AND tbl_moderation_plan_stage_kiem.type = 2', 'left');

            $this->db->where("tbl_type_productionlist.id = ".STAGE_TYPE_PHAN_DON."");

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_productions_orders.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_productions_orders.date <= '" . $end_date_search . "'");
            }

            $this->db->group_by('tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id,tb_production_order_item.items_id');
            $this->db->order_by('tbl_productions_orders.id desc');
            $dtData = $this->db->get()->result_array();

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $rowBegin = 3;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $colStt = 0;
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", (++$key));
                    $colStt++;
                    $images = '';
                    if (!empty($value['images'])) {
                        $images = 'uploads/products/' . $value['images'];
                    }
                    if (empty($images)) {
                        $images = 'assets/images/tnh/no_image.png';
                    }
                    if (!empty($images) && file_exists($images)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($images);
                        $objDrawing1->setWidth(40);
                        $objDrawing1->setHeight(40);
                        $objDrawing1->setOffsetX(40);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates($cloumns_excel[$colStt] . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(40);
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '')->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['reference_no']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", _dt($value['date']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['code_product']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['name_category_product']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['name_species']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['name_brand']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",($value['type_customer']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['unit_name']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['quantity_size']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['number_soan']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['standard']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt ++;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModerationNew($value['id'],$value['stage_id'],$vv['id'],true);
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
            $filename = lang('phieu_dieu_do_cong_doan_phan_don') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(25);
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

    public function moderation_plan_stage_giao_hang()
    {
        if (!$this->preViewModerationPlanStage && !$this->preViewOwnModerationPlanStage) {
            access_denied();
        }
        $data['title'] = lang('dt_moderation_plan_stage_phieu_giao_hang');
        $this->load->view('admin/moderation_plan_stage/moderation_plan_stage_giao_hang', $data);
    }

    public function getModerationPlanStagesGiaoHang()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id,
                tbl_productions_orders_details.object_id as order_id,
                tbl_orders.reference_no as reference_no_order,
                tblclients.company as company,
                tbl_productions_orders_items.items_id
            FROM tbl_productions_orders_items
            JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            LEFT JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id
            LEFT JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_productions_orders_details.object_type = 'orders'
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id,tbl_productions_orders_details.object_id
        ) tb_production_order_item";

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as reference_no',
            'tb_production_order_item.reference_no_order as reference_no_order',
            'tb_production_order_item.company as company',
            'tbl_products.images as images',
            'tbl_products.code as code_product',
            'tbl_brand.name as name_brand',
            'tbl_products.quantity_sheet_bale as quantity_sheet_bale',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_productions_orders';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN '.$tbProductionsOrderItems.' ON tb_production_order_item.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tb_production_order_item.items_id',
            'INNER JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id',
            'LEFT JOIN tbl_brand ON tbl_brand.id = tbl_products.brand_id',
            'INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id',
            'INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages',
            'INNER JOIN tbl_type_productionlist ON tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id',
            'LEFT JOIN tbl_moderation_plan_stage_giao_hang ON tbl_moderation_plan_stage_giao_hang.po_id = tbl_productions_orders.id AND tbl_moderation_plan_stage_giao_hang.stage_id = tbl_stages.id
            AND tbl_moderation_plan_stage_giao_hang.type_productionlist_id = tbl_type_productionlist.id AND tbl_moderation_plan_stage_giao_hang.item_id = tb_production_order_item.items_id AND tbl_moderation_plan_stage_giao_hang.order_id = tb_production_order_item.order_id',
        ];

        array_push($where, 'AND tbl_type_productionlist.id = '.STAGE_TYPE_GIAO_HANG.'');

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_type_productionlist.id as type_productionlist_id',
            'tbl_products.id as item_id',
            'tbl_stages.id as stage_id',
            'tb_production_order_item.order_id as order_id',
            'tbl_moderation_plan_stage_giao_hang.quantity_ky',
            'tbl_moderation_plan_stage_giao_hang.total_ky',
            'tbl_moderation_plan_stage_giao_hang.total_kien',
            'tbl_moderation_plan_stage_giao_hang.time_expected',
            'tbl_moderation_plan_stage_giao_hang.date_start',
            'tbl_moderation_plan_stage_giao_hang.date_end',
            'tbl_moderation_plan_stage_giao_hang.standard',
        ], 'GROUP BY tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id,tb_production_order_item.items_id,tb_production_order_item.order_id', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $po_id = $aRow['id'];
            $stage_id = $aRow['stage_id'];
            $type_productionlist_id = $aRow['type_productionlist_id'];
            $item_id = $aRow['item_id'];
            $order_id = $aRow['order_id'];
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            if(!empty($aRow['images'])){
                $images = base_url('uploads/products/'.$aRow['images']);
            } else {
                $images = base_url('assets/images/tnh/no_image.png');
            }
            $row[] = '<div class="text-left"><div class="preview_image" style="width: auto;">
                <div class="display-block contract-attachment-wrapper img">
                    <div style="width:30px; margin: auto;">
                        <a href="'.$images. '" data-lightbox="customer-profile" class="display-block mbot5">
                            <div class="">
                                <img src="' .$images. '" style="border-radius: 50%" />
                            </div>
                        </a>
                    </div>
                </div>
            </div></div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['reference_no']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="min-width: 150px"><a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/products/view_product/'.$item_id).'" data-toggle="modal" data-target="#myModal">' . ($aRow['code_product']) . '</a></div>';
            $row[] = '<div class="text-left" style="min-width: 150px"><a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/orders/view_order/'.$order_id).'" data-toggle="modal" data-target="#myModal">' . ($aRow['reference_no_order']) . '</a></div>';
            $row[] = '<div class="text-left" style="width: 150px">' . ($aRow['company']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['name_brand']) . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageGiaoHang(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.','.$order_id.',' . $stage_id . ',\'type_customer\')" name="type_customer" class="form-control type_customer" value="' . (!empty($aRow['type_customer']) ? ($aRow['type_customer']) : '') . '">
            </div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_sheet_bale']) . '</div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageGiaoHang(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.','.$order_id.',' . $stage_id . ',\'quantity_ky\')" name="quantity_ky" class="form-control quantity_ky number-format" value="' . (!empty($aRow['quantity_ky']) ? formatNumber($aRow['quantity_ky']) : 0) . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageGiaoHang(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.','.$order_id.',' . $stage_id . ',\'total_ky\')" name="total_ky" class="form-control total_ky number-format" value="' . (!empty($aRow['total_ky']) ? formatNumber($aRow['total_ky']) : 0) . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageGiaoHang(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.','.$order_id.',' . $stage_id . ',\'total_kien\')" name="total_kien" class="form-control total_kien number-format" value="' . (!empty($aRow['total_kien']) ? formatNumber($aRow['total_kien']) : 0) . '">
            </div>';
            $row[] = '<div class="text-left">
                <input type="text" style="width: 150px;" onchange="updateModerationPlanStageGiaoHang(this,' . $po_id . ',' . $item_id . ','.$type_productionlist_id.','.$order_id.',' . $stage_id . ',\'standard\')" name="standard" class="form-control standard" value="' . (!empty($aRow['standard']) ? ($aRow['standard']) : '') . '">
            </div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModerationNew($aRow['id'],$aRow['stage_id'],$vv['id']);
                $row[] = '<div class="text-center">'.$_data.'</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function updateModerationPlanStageGiaoHang()
    {
        $data = [];
        if (!$this->preEditModerationPlanStage) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $po_id = $this->input->post('po_id');
        $item_id = $this->input->post('item_id');
        $type_productionlist_id = $this->input->post('type_productionlist_id');
        $stage_id = $this->input->post('stage_id');
        $order_id = $this->input->post('order_id');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        $arrDate = [
            'date_start',
            'date_end'
        ];
        $arrDouble = [
            'total_ky',
            'quantity_ky',
            'total_kien',
            'time_expected',
        ];
        if (in_array($name,$arrDate)) {
            if (!empty($value)) {
                $value = to_sql_date($value, true);
            } else {
                $value = null;
            }
        } elseif (in_array($name,$arrDouble)) {
            if (!empty($value)) {
                $value = number_unformat($value);
            } else {
                $value = 0;
            }
        }

        $this->db->from('tbl_moderation_plan_stage_giao_hang');
        $this->db->where('tbl_moderation_plan_stage_giao_hang.po_id', $po_id);
        $this->db->where('tbl_moderation_plan_stage_giao_hang.item_id', $item_id);
        $this->db->where('tbl_moderation_plan_stage_giao_hang.type_productionlist_id', $type_productionlist_id);
        $this->db->where('tbl_moderation_plan_stage_giao_hang.stage_id', $stage_id);
        $this->db->where('tbl_moderation_plan_stage_giao_hang.order_id', $order_id);
        $dtData = $this->db->get()->row_array();
        if (!empty($dtData)) {
            $this->db->where('tbl_moderation_plan_stage_giao_hang.id', $dtData['id']);
            $success = $this->db->update('tbl_moderation_plan_stage_giao_hang', [
                $name => $value
            ]);
        } else {
            $success = $this->db->insert('tbl_moderation_plan_stage_giao_hang', [
                'po_id' => $po_id,
                'item_id' => $item_id,
                'type_productionlist_id' => $type_productionlist_id,
                'stage_id' => $stage_id,
                'order_id' => $order_id,
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

    public function exportExcelStageGiaoHang(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_dt/phieu_dieu_do_cong_doan_mo_phieu_giao_hang.xlsx';
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
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$highestRowMin, $vv['name'])->getStyle("$cloumns_excel[$i]$highestRowMin")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[($i)].$highestRow,'')->getStyle("$cloumns_excel[$i]$highestRow")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->mergeCells($cloumns_excel[($i)].''.($highestRow-1).':'.$cloumns_excel[($i)].$highestRow);
                $i ++;
            }
            $tbProductionsOrderItems = "(
                SELECT
                    tbl_productions_orders_items.productions_orders_id,
                    tbl_productions_orders_details.object_id as order_id,
                    tbl_orders.reference_no as reference_no_order,
                    tblclients.company as company,
                    tbl_productions_orders_items.items_id
                FROM tbl_productions_orders_items
                JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
                LEFT JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id
                LEFT JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
                WHERE tbl_productions_orders_details.object_type = 'orders'
                GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id,tbl_productions_orders_details.object_id
            ) tb_production_order_item";
            $this->db->select('
                tbl_productions_orders.id as id, 
                tbl_productions_orders.date as date,
                tbl_productions_orders.reference_no as reference_no,
                tb_production_order_item.reference_no_order as reference_no_order,
                tb_production_order_item.company as company,
                tbl_products.images as images,
                tbl_products.code as code_product,
                tbl_brand.name as name_brand,
                tbl_products.quantity_sheet_bale as quantity_sheet_bale,
                tbl_type_productionlist.id as type_productionlist_id,
                tbl_products.id as item_id,
                tbl_stages.id as stage_id,
                tbl_moderation_plan_stage_giao_hang.type_customer,
                tbl_moderation_plan_stage_giao_hang.quantity_ky,
                tbl_moderation_plan_stage_giao_hang.total_ky,
                tbl_moderation_plan_stage_giao_hang.total_kien,
                tbl_moderation_plan_stage_giao_hang.time_expected,
                tbl_moderation_plan_stage_giao_hang.date_start,
                tbl_moderation_plan_stage_giao_hang.date_end,
                tbl_moderation_plan_stage_giao_hang.standard,
            ');
            $this->db->from('tbl_productions_orders');
            $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id', 'inner');
            $this->db->join($tbProductionsOrderItems,'tb_production_order_item.productions_orders_id = tbl_productions_orders.id');
            $this->db->join('tbl_products','tbl_products.id = tb_production_order_item.items_id','inner');
            $this->db->join('tbl_stages','tbl_stages.id = tbl_productions_orders_items_stages.stage_id','inner');
            $this->db->join('tbl_category_stages','tbl_category_stages.id = tbl_stages.category_stages','inner');
            $this->db->join('tbl_type_productionlist','tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id','inner');
            $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
            $this->db->join('tbl_moderation_plan_stage_giao_hang', 'tbl_moderation_plan_stage_giao_hang.po_id = tbl_productions_orders.id AND tbl_moderation_plan_stage_giao_hang.stage_id = tbl_stages.id
            AND tbl_moderation_plan_stage_giao_hang.type_productionlist_id = tbl_type_productionlist.id AND tbl_moderation_plan_stage_giao_hang.item_id = tb_production_order_item.items_id AND tbl_moderation_plan_stage_giao_hang.order_id = tb_production_order_item.order_id', 'left');

            $this->db->where("tbl_type_productionlist.id = ".STAGE_TYPE_GIAO_HANG."");

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_productions_orders.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_productions_orders.date <= '" . $end_date_search . "'");
            }

            $this->db->group_by('tbl_productions_orders.id, tbl_productions_orders_items_stages.stage_id,tb_production_order_item.items_id,tb_production_order_item.order_id');
            $this->db->order_by('tbl_productions_orders.id desc');
            $dtData = $this->db->get()->result_array();

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $rowBegin = 3;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $colStt = 0;
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", (++$key));
                    $colStt++;
                    $images = '';
                    if (!empty($value['images'])) {
                        $images = 'uploads/products/' . $value['images'];
                    }
                    if (empty($images)) {
                        $images = 'assets/images/tnh/no_image.png';
                    }
                    if (!empty($images) && file_exists($images)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($images);
                        $objDrawing1->setWidth(40);
                        $objDrawing1->setHeight(40);
                        $objDrawing1->setOffsetX(40);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates($cloumns_excel[$colStt] . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(40);
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", '')->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['reference_no']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", _dt($value['date']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['code_product']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['reference_no_order']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['company']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['name_brand']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin",($value['type_customer']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['quantity_sheet_bale']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['quantity_ky']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['total_ky']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", formatNumber($value['total_kien']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", ($value['standard']))->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt++;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModerationNew($value['id'],$value['stage_id'],$vv['id'],true);
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
            $filename = lang('phieu_dieu_do_cong_doan_mo_phieu_giao_hang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(25);
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