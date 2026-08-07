<?php
    $group = $this->input->post('group');
    $date_start = $this->input->post('date_start');
    $date_end = $this->input->post('date_end');
    if (empty($date_start) || empty($date_end)) {
        echo lang('Vui lòng chọn ngày bắt đầu và kết thúc'); die;
    }
    $_date_start = to_sql_date($date_start);
    $_date_end = to_sql_date($date_end);
    $productions_orders_search = $this->input->post('productions_orders_search');
    $status_filter = $this->input->post('status_filter');
    $date_start_expected = $this->input->post('date_start_expected');
    $date_end_expected = $this->input->post('date_end_expected');
    $date_start_finished = $this->input->post('date_start_finished');
    $date_end_finished = $this->input->post('date_end_finished');

    $this->db->select('
        tbl_category_stages.*,
        tbl_type_productionlist.code as code_type_productionlist
    ', false);
    $this->db->from('tbl_category_stages');
    $this->db->join('tbl_type_productionlist', 'tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id');
    $this->db->where('tbl_category_stages.id', $group);
    $dtCategoryStages = $this->db->get()->row_array();

    if (empty($dtCategoryStages)) {
        echo lang('Không tìm thấy loại nhóm công đoạn'); die;
    }

    $category_stage_id = $dtCategoryStages['id'];
    $type_productionlist_id = $dtCategoryStages['type_productionlist_id'];

    $this->db->dbprefix  = '';
    //handling data

    // GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
    $tbProductionsOrderItems = "(
        SELECT
            tbl_productions_orders_items.items_id, 
            tbl_productions_orders_items.productions_orders_id, 
            tbl_productions_plan.note as note_plan,
            SUM(tbl_productions_orders_items.quantity) as quantity,
            SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
            tbl_productions_orders_items.plan_id as plan_id

        FROM tbl_productions_orders_items
        INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
        INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
        WHERE 1 
        GROUP BY tbl_productions_orders_items.productions_orders_id
    ) tb_production_order_item";

    $this->db->select('
        tbl_productions_orders.id as id,
        tbl_productions_orders.date as date,
        tb_production_order_item.items_id, 
        tb_production_order_item.productions_orders_id, 
        tbl_productions_orders_items_stages.stage_id,
        tbl_productions_orders_items_stages.face, 
        tbl_productions_orders_items_stages.face_after,
        tbl_productions_orders.reference_no as reference_no_po,
        tbl_products.id as item_id,
        tbl_products.code as item_code,
        tbl_products.name as item_name,
        tbl_products.images as images,
        tbl_stages.name as name_stage,
        (tb_production_order_item.quantity) as quantity,
        tb_production_order_item.plan_id as plan_id,
        tbl_productions_orders_items_stages.number_face as number_face,
        tbl_productions_orders_items_stages.number_operations as number_operations,
        tbl_productions_orders_items_stages.number_cutting as number_cutting,
        tbl_productions_orders_items_stages.quota_time_f1 as quota_time_f1,
        tbl_productions_orders_items_stages.quota_time_f2 as quota_time_f2,
        tbl_products.quantity_child_sheet as quantity_child_sheet,
        tbl_products.quantity_child_molds_offset as quantity_child_molds_offset,
        tbl_products.quantity_child_molds_flexo as quantity_child_molds_flexo,
        tbl_products.quantity_child_molds as quantity_child_molds,
        tb_production_order_item.note_plan as note_plan,
        tbl_productions_orders.is_ptm as is_ptm,
        tbl_productions_orders.is_color as is_color,
        tbl_productions_orders.is_layout as is_layout,
        tbl_productions_orders.is_sewing as is_sewing,
        tbl_productions_orders.is_npl as is_npl,
        tbl_productions_orders.is_material as is_material,
        tbl_productions_orders.is_cutting as is_cutting,
        tbl_productions_orders.date_npl as date_npl,
        tbl_productions_orders.is_number_printed as is_number_printed
    ');
    $this->db->from('tbl_productions_orders');
    $this->db->join($tbProductionsOrderItems, 'tb_production_order_item.productions_orders_id = tbl_productions_orders.id');
    $this->db->join('tbl_products', 'tbl_products.id = tb_production_order_item.items_id');
    $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id');
    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
    $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
    $this->db->where('tbl_productions_orders.date >=', $_date_start.' 00:00:00');
    $this->db->where('tbl_productions_orders.date <=', $_date_end.' 23:59:59');
    $this->db->where('tbl_category_stages.id', $group);
    $this->db->where('tbl_category_stages.type_productionlist_id', $type_productionlist_id);

    if (!empty($productions_orders_search)) {
        $this->db->where_in('tbl_productions_orders.id', $productions_orders_search);
    }

    if ($type_productionlist_id == 1) {
        $this->db->where('(tbl_productions_orders_items_stages.face > 0 OR tbl_productions_orders_items_stages.face_after > 0 OR (tbl_productions_orders_items_stages.face = 0 OR tbl_productions_orders_items_stages.face_after = 0))', false, false);
        $this->db->group_by('tbl_productions_orders.id, tb_production_order_item.items_id, tbl_productions_orders_items_stages.stage_id, tbl_productions_orders_items_stages.face, tbl_productions_orders_items_stages.face_after');
    } else if ($type_productionlist_id == 2 || $type_productionlist_id == 3 || $type_productionlist_id == 7 || $type_productionlist_id == 6 || $type_productionlist_id == 10 || $type_productionlist_id == 11 || $type_productionlist_id == 12 || $type_productionlist_id == 8 || $type_productionlist_id == 9 || $type_productionlist_id == 13 || $type_productionlist_id == 14 || $type_productionlist_id == 15 || $type_productionlist_id == 16) {
        $this->db->where('(tbl_productions_orders_items_stages.face > 0 OR tbl_productions_orders_items_stages.face_after > 0 OR (tbl_productions_orders_items_stages.face = 0 OR tbl_productions_orders_items_stages.face_after = 0))', false, false);
        $this->db->group_by('tbl_productions_orders.id, tb_production_order_item.items_id, tbl_productions_orders_items_stages.stage_id, tbl_productions_orders_items_stages.face, tbl_productions_orders_items_stages.face_after');

        // $this->db->group_by('tbl_productions_orders.id, tb_production_order_item.items_id, tbl_productions_orders_items_stages.stage_id');
    } else if ($type_productionlist_id == 4 || $type_productionlist_id == 5 || $type_productionlist_id == 17 || $type_productionlist_id == 20 || $type_productionlist_id == 19 || $type_productionlist_id == 26) {
        $this->db->where('(tbl_productions_orders_items_stages.face > 0 OR tbl_productions_orders_items_stages.face_after > 0 OR (tbl_productions_orders_items_stages.face = 0 OR tbl_productions_orders_items_stages.face_after = 0))', false, false);
        $this->db->group_by('tbl_productions_orders.id, tb_production_order_item.items_id, tbl_productions_orders_items_stages.stage_id, tbl_productions_orders_items_stages.face, tbl_productions_orders_items_stages.face_after');
    }

    $whereProductionListsItems = '';

    if (!empty($status_filter) && $status_filter != 'ALL') {
        // if ($status_filter == 'CHT') {
        //     $whereProductionListsItems.= ' AND tbl_production_lists_items.hoan_thanh != "HT"';
        // } else if ($status_filter == 'HT') {
        //     $whereProductionListsItems.= ' AND tbl_production_lists_items.hoan_thanh = "HT"';
        // }

        // if ($status_filter == 'CHT') {
        //     $this->db->where(' NOT EXISTS (
        //         SELECT 1
        //         FROM tbl_production_lists_items
        //         WHERE tbl_production_lists_items.po_id = tbl_productions_orders.id AND tb_production_order_item.items_id = tbl_production_lists_items.item_id AND tbl_productions_orders_items_stages.stage_id = tbl_production_lists_items.stage_id AND tbl_productions_orders_items_stages.face = tbl_production_lists_items.face AND tbl_productions_orders_items_stages.face_after =tbl_production_lists_items.face_after AND tbl_production_lists_items.hoan_thanh = "HT"
        //     )', false, false);
        // } else if ($status_filter == 'HT') {
        //     $this->db->where(' EXISTS (
        //         SELECT 1
        //         FROM tbl_production_lists_items
        //         WHERE tbl_production_lists_items.po_id = tbl_productions_orders.id AND tb_production_order_item.items_id = tbl_production_lists_items.item_id AND tbl_productions_orders_items_stages.stage_id = tbl_production_lists_items.stage_id AND tbl_productions_orders_items_stages.face = tbl_production_lists_items.face AND tbl_productions_orders_items_stages.face_after =tbl_production_lists_items.face_after AND tbl_production_lists_items.hoan_thanh = "HT"
        //     )', false, false);
        // }
    }

    if (!empty($date_start_expected)) {
        $date_start_expected = to_sql_date($date_start_expected);
        $whereProductionListsItems.= ' AND tbl_production_lists_items.ngay_bat_dau_du_kien >= "'.$date_start_expected.'"';
    }

    if (!empty($date_end_expected)) {
        $date_end_expected = to_sql_date($date_end_expected);
        $whereProductionListsItems.= ' AND tbl_production_lists_items.ngay_bat_dau_du_kien <= "'.$date_end_expected.'"';

    }

    if (!empty($date_start_finished)) {
        $date_start_finished = to_sql_date($date_start_finished);
        $whereProductionListsItems.= ' AND tbl_production_lists_items.ngay_hoan_thanh_in >= "'.$date_start_finished.'"';
    }

    if (!empty($date_end_finished)) {
        $date_end_finished = to_sql_date($date_end_finished);
        $whereProductionListsItems.= ' AND tbl_production_lists_items.ngay_hoan_thanh_in <= "'.$date_end_finished.'"';
    }

    if (!empty($whereProductionListsItems)) {
        $this->db->where(' EXISTS (
            SELECT 1
            FROM tbl_production_lists_items
            WHERE tbl_production_lists_items.po_id = tbl_productions_orders.id AND tb_production_order_item.items_id = tbl_production_lists_items.item_id AND tbl_productions_orders_items_stages.stage_id = tbl_production_lists_items.stage_id '.$whereProductionListsItems.'
        )', false, false);
    }
    
    $this->db->order_by('tbl_productions_orders.id ASC, tbl_productions_orders_items_stages.id ASC');
    $productions_orders_items = $this->db->get()->result_array();
    // print_arrays($this->db->last_query());

    // $this->db->select('
    //     tbl_productions_orders.id as id,
    //     tbl_productions_orders.date as date,
    //     tbl_productions_orders_items.items_id, 
    //     tbl_productions_orders_items.productions_orders_id, 
    //     tbl_productions_orders_items_stages.stage_id,
    //     tbl_productions_orders_items_stages.face, 
    //     tbl_productions_orders_items_stages.face_after,
    //     tbl_productions_orders.reference_no as reference_no_po,
    //     tbl_products.id as item_id,
    //     tbl_products.code as item_code,
    //     tbl_products.name as item_name,
    //     tbl_stages.name as name_stage,
    //     SUM(tbl_productions_orders_items.quantity) as quantity,
    //     tbl_productions_orders_items.plan_id as plan_id,
    //     tbl_productions_orders_items_stages.number_face as number_face,
    //     tbl_productions_orders_items_stages.number_operations as number_operations,
    //     tbl_productions_orders_items_stages.number_cutting as number_cutting,
    //     tbl_productions_orders_items_stages.quota_time_f1 as quota_time_f1,
    //     tbl_productions_orders_items_stages.quota_time_f2 as quota_time_f2,
    //     tbl_products.quantity_child_sheet as quantity_child_sheet,
    //     tbl_products.quantity_child_molds_offset as quantity_child_molds_offset,
    //     tbl_products.quantity_child_molds_flexo as quantity_child_molds_flexo,
    //     tbl_products.quantity_child_molds as quantity_child_molds
    // ');
    // $this->db->from('tbl_productions_orders');
    // $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id');
    // $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
    // $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id');
    // $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
    // $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
    // $this->db->where('tbl_productions_orders.date >=', $_date_start.' 00:00:00');
    // $this->db->where('tbl_productions_orders.date <=', $_date_end.' 23:59:59');
    // $this->db->where('tbl_category_stages.id', $group);
    // $this->db->where('tbl_category_stages.type_productionlist_id', $type_productionlist_id);
    // $this->db->group_by('tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id, tbl_productions_orders_items_stages.stage_id, tbl_productions_orders_items_stages.face, tbl_productions_orders_items_stages.face_after');
    // $this->db->order_by('tbl_productions_orders.id ASC, tbl_productions_orders_items_stages.id ASC');
    // $productions_orders_items = $this->db->get()->result_array();
    //

    $counter = 0;

    //handling date
    $start_date = $date_start;
    $end_date  = date('t/m/Y');

    $_start_date = to_sql_date($start_date);
    $minus_date = minusDate($start_date, $end_date);
    $arrDate = [];
    for ($i = 0; $i <= $minus_date; $i++) {
        $_date = date('d/m/Y', strtotime($_start_date . ' + ' . $i . ' days'));

        $arr_date = explode('/', $_date);
        $day_month = $arr_date[0] . '/' . $arr_date[1];

        $arrDate[] = [
            '_date' => $_date,
            'day_month' => $day_month,
        ];
    }

    $chunkArrDate = array_chunk($arrDate, 7);
    //

    $trHtml = '';
    $timeCardAlignment = $this->production_list_model->getTimeCardAlignment();
    if ($type_productionlist_id != 1 && $type_productionlist_id != 2 && $type_productionlist_id != 4 && $type_productionlist_id != 5 && $type_productionlist_id != 17 && $type_productionlist_id != 20 && $type_productionlist_id != 19) {
        //barcode
        $timeCardAlignment = [$timeCardAlignment[0]];
    }

    if (!empty($productions_orders_items)) {

        $optionMachine = $this->production_list_model->getMachines($category_stage_id);
        $dtStatusProductionsLists = $this->production_list_model->getStatusProductionsLists();
        $optionDauIn = [
            [
                'id' => 300,
                'name' => 300,
            ],
            [
                'id' => 600,
                'name' => 600,
            ],
        ];

        $optionLoaiFlexo = [
            [
                'id' => 1,
                'name' => 'Thường',
            ],
            [
                'id' => 2,
                'name' => 'UV',
            ],
        ];

        $optionLoaiMay = [
            [
                'id' => 1,
                'name' => 'Tự động',
            ],
            [
                'id' => 2,
                'name' => 'Đặt tay',
            ],
        ];

        $optionLoaiBoi = [
            [
                'id' => 1,
                'name' => 1,
            ],
            [
                'id' => 2,
                'name' => 2,
            ],
        ];

        $optionLoaiGiay = [
            [
                'id' => 1,
                'name' => 'Thường',
            ],
            [
                'id' => 2,
                'name' => 'Bìa/Pet',
            ],
        ];

        $optionLoaiXa = [
            [
                'id' => 1,
                'name' => 1,
            ],
            [
                'id' => 2,
                'name' => 2,
            ],
        ];

        $optionStatusProductionList = optionStatusProductionList();

        $arrPOID = [];
        $arrItemID = [];
        $arrStageID = [];
        $arrPlanID = [];
        foreach ($productions_orders_items as $kOI => $vOI) {
            $arrPOID[] = $vOI['productions_orders_id'];
            $arrItemID[] = $vOI['items_id'];
            $arrStageID[] = $vOI['stage_id'];
            $arrPlanID[] = $vOI['plan_id'];
        }

        if (!empty($arrPOID)) {
            $arrPOID = array_unique($arrPOID);
            $arrItemID = array_unique($arrItemID);
            $arrStageID = array_unique($arrStageID);
            $arrPlanID = array_unique($arrPlanID);

            //BOM
            $this->db->select('
                tbl_productions_orders_items.productions_orders_id, 
                ppb_materials.item_type as type, 
                ppb_materials.item_id, 
                ppb_materials.landscape_print_size, 
                ppb_materials.number_children_size, 
                ppb_materials.unit_parent_id, 
                SUM(ppb_materials.quantity) as quantity,
                ppb_materials.quantity_single as quantity_single,
            ', false);
            $this->db->from('tbl_productions_plan_bom ppb_primary');
            $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
            $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
            $this->db->where_in('tbl_productions_orders_items.productions_orders_id', $arrPOID);
            $this->db->where('ppb_primary.parent_id', 0);
            // $this->db->where_in('tbl_productions_orders_items.items_id', $arrItemID);
            $this->db->where('(
                ppb_materials.item_type IN ("semi_products", "semi_products_outside")
                OR exists (
                    SELECT
                        tbl_materials.id
                    FROM tbl_materials
                    INNER JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id
                    WHERE ppb_materials.item_type = "materials" AND tbl_materials.id = ppb_materials.item_id AND tbl_category_items.is_primary = 1
                )
            )', false, false);

            $this->db->group_by('tbl_productions_orders_items.productions_orders_id, ppb_materials.item_type, ppb_materials.item_id, ppb_materials.landscape_print_size, ppb_materials.number_children_size, ppb_materials.unit_parent_id, ppb_materials.quantity_single', false);
            $listBom = $this->db->get()->result_array();
            if ($listBom) {
                $listBom = array_reduce($listBom, function($carry, $item) {
                    $carry[$item['productions_orders_id']][] = $item;
                    return $carry;
                });
            }

            //danh sách kẽm
            if ($arrPlanID) {
                $this->db->select('
                    tbl_productions_plan_compensation.productions_plan_id as plan_id,
                    SUM(tbl_productions_plan_compensation.quantity_compensation) as quantity_compensation
                ', false);
                $this->db->from('tbl_productions_plan_compensation');
                $this->db->where('tbl_productions_plan_compensation.is_zinc', 1);
                $this->db->where_in('tbl_productions_plan_compensation.productions_plan_id', $arrPlanID);
                $this->db->group_by('tbl_productions_plan_compensation.productions_plan_id');
                $listPlanZinc = $this->db->get()->result_array();
                if ($listPlanZinc) {
                    $listPlanZinc = array_reduce($listPlanZinc, function($carry, $item) {
                        $carry[$item['plan_id']][] = $item;
                        return $carry;
                    });
                }
            }

            //ngày giao hàng hệ thống
            $tbDateDelivery = "
                SELECT
                    tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                    MIN(tbl_productions_plan_details.date) as date_shipping
                FROM tbl_productions_plan_items
                INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
                JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
                WHERE tbl_productions_plan_items.is_preventive = 0 AND tbl_productions_orders_items.productions_orders_id IN (".implode(',', $arrPOID).")
                GROUP BY tbl_productions_orders_items.productions_orders_id
            ";
            $listDateDelivery = $this->db->query($tbDateDelivery)->result_array();
            if ($listDateDelivery) {
                $listDateDelivery = array_reduce($listDateDelivery, function($carry, $item) {
                    $carry[$item['productions_orders_id']] = $item;
                    return $carry;
                });
            }
        }

        $productionListItems = $this->production_list_model->getProductionListsItemsMul($arrPOID, true);

        $temp_production_lists_total = $this->production_list_model->rowProductionListsTotalDateEnd($_date_end, $type_productionlist_id, true);
        $production_lists_total = $temp_production_lists_total['production_lists_total'];
        if (empty($temp_production_lists_total['result']) && $production_lists_total['items_canh_bai']) {
            $timeCardAlignment = json_decode($production_lists_total['items_canh_bai'], true);
        }

        $productions_orders_items_new = [];
        foreach ($productions_orders_items as $item) {
            if ($item['face'] && $item['face_after'] && ($type_productionlist_id == 1 || $type_productionlist_id == 2 || $type_productionlist_id == 3 || $type_productionlist_id == 7 || $type_productionlist_id == 6 || $type_productionlist_id == 10 || $type_productionlist_id == 11 || $type_productionlist_id == 12 || $type_productionlist_id == 8 || $type_productionlist_id == 9 || $type_productionlist_id == 13 || $type_productionlist_id == 14 || $type_productionlist_id == 15 || $type_productionlist_id == 16 || $type_productionlist_id == 4 || $type_productionlist_id == 5 || $type_productionlist_id == 17 || $type_productionlist_id == 20 || $type_productionlist_id == 19 || $type_productionlist_id == 26)) {
                $newItem = $item;
                $newItem['face'] = 1;
                $newItem['face_after'] = 0;
                $newItem['_key'] = 1;
                $productions_orders_items_new[] = $newItem;

                $newItem = $item;
                $newItem['face'] = 0;
                $newItem['face_after'] = 2;
                $newItem['_key'] = 2;
                $productions_orders_items_new[] = $newItem;
            } else {
                $productions_orders_items_new[] = $item;
            }
        }

        // print_arrays($productions_orders_items_new);
        // foreach ($productions_orders_items as $kOI => $vOI) {
        foreach ($productions_orders_items_new as $kOI => $vOI) {
            $po_id = $vOI['id'];
            $plan_id = $vOI['plan_id'];
            $bom = $listBom[$po_id] ?? null;
            $_item_id = $vOI['item_id'];
            $name_stage = $vOI['name_stage'];
            $stage_id = $vOI['stage_id'];

            $arrCountItems = [];
            if (FIX_QUANTITY_COMPENSATION) {
                if (!empty($bom)) {
                    foreach ($bom as $kB => $vB) {
                        $strKey = $vB['type'] . '__' . $vB['item_id'];
                        if (!empty($arrCountItems[$strKey])) {
                            $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                        } else {
                            $arrCountItems[$strKey]['count'] = 1;
                            $arrCountItems[$strKey]['decimal'] = 0;
                        }
                    }
                }
            }

            $total_paper_exchange = 0;
            $total_quantity_compensation = 0;
            $quantity_zinc = 0;
            $number_children_size = 0;
            $is_number_printed = $vOI['is_number_printed'];
            if (!empty($bom)) {
                foreach ($bom as $kB => $vB) {
                    $item_id = $vB['item_id'];
                    $type = $vB['type'];
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];

                    if ($type == 'materials' && empty($number_children_size)) {
                        $number_children_size = $vB['number_children_size'];
                    }

                    if ($type_productionlist_id == 1 && $is_number_printed == 1 && $type != 'materials') {
                        continue;
                    }

                    //fix quantity compensation
                    if (FIX_QUANTITY_COMPENSATION) {
                        $strKey = $vB['type'] . '__' . $vB['item_id'];
                        $count_item = $arrCountItems[$strKey]['count'];
                        $division = $quantity_compensation / $count_item;
                        if (is_decimal($division)) {
                            if ($arrCountItems[$strKey]['decimal']) {
                                $quantity_compensation = floor($division);
                            } else {
                                $arrCountItems[$strKey]['decimal'] = 1;
                                $quantity_compensation = ceil($division);
                            }
                        } else {
                            $quantity_compensation = $division;
                        }
                    }
                    //

                    $quantity = ceil(round($vB['quantity'], 4));
                    $quantity_single = $vB['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need / $quantity_single) : 0;
                    $total_paper_exchange += $paper_exchange;

                    $quantity_compensation = $quantity_compensation > 0 ? ceil($quantity_compensation / $quantity_single) : 0;
                    $total_quantity_compensation += $quantity_compensation;
                }
            }

            //tờ in
            $so_to_in = $total_paper_exchange;

            //mặt in
            $face = $vOI['face'];
            $face_after = $vOI['face_after'];
            $countFace = 0;
            $mat = '';
            if ($face > 0) {
                $countFace++;
                $mat = 'A';
            }

            if ($face_after > 0) {
                $countFace++;
                $mat = 'B';
            }

            if (empty($countFace)) $countFace = 1;

            //số lượng kẽm
            $dtZinc = $listPlanZinc[$plan_id] ?? null;
            $quantity_zinc = 0;
            if (!empty($dtZinc)) {
                $quantity_zinc = $dtZinc['quantity_compensation'] ?? 0;
            }
            
            $ngay_mo_lenh_sx = date_format(date_create($vOI['date']), 'd/m/Y');

            $dtDateDelivery = $listDateDelivery[$po_id] ?? null;
            $ngay_giao_hang = !empty($dtDateDelivery['date_shipping']) ? _d($dtDateDelivery['date_shipping']) : '';

            //số con tờ tin
            $so_con_tren_to_in = $vOI['quantity_child_sheet'];
            $so_con_tren_kb_flexo = $vOI['quantity_child_molds_flexo'];
            $so_con_tren_kb_offset = $vOI['quantity_child_molds_offset'];
            $so_con_tren_kb = $vOI['quantity_child_molds'];
            $note_plan = $vOI['note_plan'];

            $so_luong_san_xuat = $vOI['quantity'];
            $_key = !empty($vOI['_key']) ? $vOI['_key'] : 0;

            $_index = $po_id.'__'.$_item_id.'__'.$face.'__'.$face_after.'__'.$_key.'__'.$stage_id;
            $dtProductionListsItem = $productionListItems[$_index] ?? null;
            $thoi_gian_thay_size = 0;
            $thoi_gian_rua_may = 0;
            $to_in_bu_hao = $total_quantity_compensation;

            $tdLenhSanXuat = '<td class="text-center">
                <div style="width: 120px;">'.$vOI['reference_no_po'].'</div>
            </td>';

            $tdMaSanPham = '<td class="text-center">
                <a class="tnh-modal" href="'.base_url('admin/products/view_product/'.$_item_id).'">'.$vOI['item_code'].'</a>
            </td>';

            $tdTenSanPham = '<td class="text-center">
                '.$vOI['item_name'].'
            </td>';

            $tdCongDoan = '<td class="text-center">
                '.$vOI['name_stage'].'
            </td>';

            $tdSoToIn = '<td class="text-right td-to-in">' . formatNumber($so_to_in) . '</td>';
            $tdSoMatIn = '<td class="text-right td-so-mat-in ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][so_mat_in]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px; width: 80px;" class="form-control number-format so_mat_in" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . ' value="' . (!empty($dtProductionListsItem) ? $dtProductionListsItem['so_mat_in'] : $countFace) . '">
            </td>';

            $tdTongTua = '<td class="text-right td-tong-tua"></td>';
            $tdThoiGianIn = '<td class="text-right td-thoi-gian-in"></td>';
            $tdThoiGianCanhBai = '<td class="text-right td-thoi-gian-canh-bai ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['thoi_gian_canh_bai']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][thoi_gian_canh_bai]" placeholder="'.lang('Thời gian canh bài').'" onchange="totalProductionList('.$type_productionlist_id.', 1)" style="padding: 5; height: 30px; width: 80px;" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['thoi_gian_canh_bai']) ? 'readonly' : 'readonly') . ' class="form-control number-format thoi_gian_canh_bai" value="'.(!empty($dtProductionListsItem['thoi_gian_canh_bai']) ? ($dtProductionListsItem['thoi_gian_canh_bai']) : '').'">
            </td>';

            $tdThoiGianKhac = '<td class="text-right td-thoi-gian-khac ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['thoi_gian_khac']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][thoi_gian_khac]" placeholder="'.lang('Thời gian khác').'" onchange="totalProductionList('.$type_productionlist_id.', 1)" style="padding: 5; height: 30px; width: 80px;" class="form-control number-format thoi_gian_khac" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['thoi_gian_khac']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['thoi_gian_khac']) ? ($dtProductionListsItem['thoi_gian_khac']) : '').'">
            </td>';

            $tdThoiGianXuLy = '<td class="text-right td-thoi-gian-xu-ly"></td>';
            $tdNgayMoLSX = '<td class="text-right td-ngay-mo-lsx">
                <input type="hidden" name="items[' . $counter . '][po_id]" class="form-control" value="'.$po_id.'">
                <input type="hidden" name="items[' . $counter . '][item_id]" class="form-control" value="'.$_item_id.'">
                <input type="hidden" name="items[' . $counter . '][to_in]" class="form-control" value="'.$so_to_in.'">
                <input type="hidden" name="items[' . $counter . '][ngay_mo_lsx]" class="form-control" value="'.$ngay_mo_lenh_sx.'">
                <input type="hidden" name="items[' . $counter . '][ngay_giao_hang_he_thong]" class="form-control" value="'.$ngay_giao_hang.'">
                <input type="hidden" name="items[' . $counter . '][stage_name]" class="form-control" value="'.$name_stage.'">
                <input type="hidden" name="items[' . $counter . '][str_stage_id]" class="form-control" value="'.$stage_id.'">
                <input type="hidden" name="items[' . $counter . '][so_con_tren_to_in]" class="form-control" value="'.$so_con_tren_to_in.'">
                <input type="hidden" name="items[' . $counter . '][so_con_tren_kb_offset]" class="form-control" value="'.$so_con_tren_kb_offset.'">
                <input type="hidden" name="items[' . $counter . '][thoi_gian_con_lai]" class="form-control thoi_gian_con_lai" value="0">
                <input type="hidden" name="items[' . $counter . '][so_luong_san_xuat]" class="form-control so_luong_san_xuat" value="'.$so_luong_san_xuat.'">
                <input type="hidden" name="items[' . $counter . '][so_con_tren_kb_flexo]" class="form-control so_con_tren_kb_flexo" value="'.$so_con_tren_kb_flexo.'">
                <input type="hidden" name="items[' . $counter . '][so_con_tren_kb]" class="form-control so_con_tren_kb" value="'.$so_con_tren_kb.'">
                <input type="hidden" name="items[' . $counter . '][stage_id]" class="form-control stage_id" value="'.$stage_id.'">
                <input type="hidden" name="items[' . $counter . '][face]" class="form-control face" value="'.$face.'">
                <input type="hidden" name="items[' . $counter . '][face_after]" class="form-control face_after" value="'.$face_after.'">
                <input type="hidden" name="items[' . $counter . '][thoi_gian_thay_size]" class="form-control thoi_gian_thay_size" value="'.$thoi_gian_thay_size.'">
                <input type="hidden" name="items[' . $counter . '][thoi_gian_rua_may]" class="form-control thoi_gian_rua_may" value="'.$thoi_gian_rua_may.'">
                <input type="hidden" name="items[' . $counter . '][mat]" class="form-control mat" value="'.$mat.'">
                <input type="hidden" name="items[' . $counter . '][_key]" class="form-control _key" value="'.$_key.'">
                <input type="hidden" name="items[' . $counter . '][to_in_bu_hao]" class="form-control to_in_bu_hao" value="'.$to_in_bu_hao.'">
                '.$ngay_mo_lenh_sx.'
            </td>';
            $tdNgayGiaoHangHeThong = '<td class="text-center td-ngay-giao-hang-he-thong">'.$ngay_giao_hang.'</td>';
            $tdNgayGiaoHang = '<td class="text-right td-ngay-giao-hang ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_giao_hang']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][ngay_giao_hang]" placeholder="'.lang('Ngày giao hàng').'" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px; width: 100px;" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_giao_hang']) ? 'readonly' : 'readonly') . ' class="form-control ngay_giao_hang" value="'.(!empty($dtProductionListsItem['ngay_giao_hang']) ? _d($dtProductionListsItem['ngay_giao_hang']) : $ngay_giao_hang).'">
            </td>';

            $tdNgayVeNVLDuKien = '<td class="text-right td-ngay-ve-nvl-du-kien ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_ve_nvl_du_kien']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][ngay_ve_nvl_du_kien]" placeholder="'.lang('Ngày về NVL dự kiến').'" style="padding: 5; height: 30px; width: 100px;" class="form-control ngay_ve_nvl_du_kien datepicker" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_ve_nvl_du_kien']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['ngay_ve_nvl_du_kien']) ? _d($dtProductionListsItem['ngay_ve_nvl_du_kien']) : (!empty($vOI['date_npl']) ? _d($vOI['date_npl']) : '')).'">
            </td>';

            $tdNgayBanGiaoSanXuat = '<td class="text-right td-ngay-ban-giao-san-xuat ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_ban_giao_san_xuat']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][ngay_ban_giao_san_xuat]" placeholder="'.lang('Ngày bàn giao sản xuất').'" style="padding: 5; height: 30px; width: 100px;" class="form-control ngay_ban_giao_san_xuat datepicker" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_ban_giao_san_xuat']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['ngay_ban_giao_san_xuat']) ? _d($dtProductionListsItem['ngay_ban_giao_san_xuat']) : '').'">
            </td>';

            $tdNgayBatDauDuKien = '<td class="text-right td-ngay-bat-dau-du-kien ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_bat_dau_du_kien']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][ngay_bat_dau_du_kien]" placeholder="'.lang('Ngày bắt đầu dự kiến').'" onchange="totalProductionList('.$type_productionlist_id.', 1)" style="padding: 5; height: 30px; width: 100px;" class="form-control ngay_bat_dau_du_kien datepicker" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_bat_dau_du_kien']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['ngay_bat_dau_du_kien']) ? _d($dtProductionListsItem['ngay_bat_dau_du_kien']) : '').'">
            </td>';

            $tdNgayHoanThanhIn = '<td class="text-right td-ngay-hoan-thanh-in ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_hoan_thanh_in']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][ngay_hoan_thanh_in]" placeholder="'.lang('Ngày hoàn thành in').'" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px; width: 100px;" class="form-control ngay_hoan_thanh_in datepicker" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_hoan_thanh_in']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['ngay_hoan_thanh_in']) ? _d($dtProductionListsItem['ngay_hoan_thanh_in']) : '').'">
            </td>';

            $tdTinhTrang = '<td class="text-right td-tinh-trang ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['tinh_trang']) ? 'readonly' : '') . '" style="min-width: 120px !important; max-width: 120px !important; width: 120px !important;">
                '.render_select('items[' . $counter . '][tinh_trang]', $dtStatusProductionsLists, ['id', 'code'], '', (!empty($dtProductionListsItem['tinh_trang']) ? ($dtProductionListsItem['tinh_trang']) : ''), ['style' => 'width: 80px !important;', (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['tinh_trang']) ? 'disabled1' : '') => (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['tinh_trang']) ? 'disabled1' : '')]).'
            </td>';

            $tdMayIn = '<td class="text-right td-may-in ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['may_in']) ? 'readonly' : '') . '" style="min-width: 120px !important; max-width: 120px !important; width: 120px !important;">
                '.render_select('items[' . $counter . '][may_in]', $optionMachine, ['id', 'code', 'name'], '', (!empty($dtProductionListsItem['may_in']) ? ($dtProductionListsItem['may_in']) : ''), ['data-class' => 'may_in', 'onchange' => 'totalProductionList('.$type_productionlist_id.')', (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['may_in']) ? 'disabled1' : '') => (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['may_in']) ? 'disabled1' : '')]).'
            </td>';

            $tdThoiGianConLai = '<td class="text-right td-thoi-gian-con-lai"></td>';

            $tdGhiChu = '<td class="text-right td-ghi-chu ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ghi_chu']) ? 'readonly' : '') . '">
                <textarea type="text" name="items[' . $counter . '][ghi_chu]" placeholder="'.lang('Ghi chú').'" style="padding: 5; height: 50px; width: 200px;" class="form-control ghi_chu" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ghi_chu']) ? 'readonly' : '') . '>'.(!empty($dtProductionListsItem['ghi_chu']) ? ($dtProductionListsItem['ghi_chu']) : $note_plan).'</textarea>
            </td>';

            // $tdSoConTrenToIn = '<td class="text-right td-so-con-tren-to-in">'.$so_con_tren_to_in.'</td>';
            $tdSoConTrenToIn = '<td class="text-right td-so-con-tren-to-in">'.$number_children_size.'</td>';
            $tdSoConTrenKBOffset = '<td class="text-right td-so-con-tren-kb-flexo">'.$so_con_tren_kb_offset.'</td>';
            $tdSoConTrenKBFlexo = '<td class="text-right td-so-con-tren-kb-flexo">'.$so_con_tren_kb_flexo.'</td>';

            $tdTuaSauIn = '<td class="text-right td-tua-sau-in"></td>';
            $tdBongMang = '<td class="text-right td-bong-mang ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['bong_mang']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][bong_mang]" placeholder="'.lang('Bóng màng').'" style="padding: 5; height: 30px; width: 100px;" class="form-control bong_mang" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['bong_mang']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['bong_mang']) ? ($dtProductionListsItem['bong_mang']) : '').'">
            </td>';

            // $tdHoanThanh = '<td class="text-right td-hoan-thanh">
            //     <input type="text" name="items[' . $counter . '][hoan_thanh]" placeholder="'.lang('Trạng thái').'" style="padding: 5; height: 30px; width: 100px;" class="form-control hoan-thanh" value="'.(!empty($dtProductionListsItem['hoan_thanh']) ? ($dtProductionListsItem['hoan_thanh']) : '').'">
            // </td>';

            $_optionHT = '';
            foreach ($optionStatusProductionList as $kO => $vO) {
                $_optionHT.= '<option '.(!empty($dtProductionListsItem['hoan_thanh']) && $dtProductionListsItem['hoan_thanh'] == $vO['id'] ? 'selected' : '').' value="'.$vO['id'].'">'.$vO['name'].'</option>';
            }

            $tdHoanThanh = '<td class="text-right td-hoan-thanh ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['hoan_thanh']) ? 'readonly' : '') . '">
                <select name="items[' . $counter . '][hoan_thanh]" placeholder="'.lang('Trạng thái').'" style="width: 150px;" class="form-control" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['hoan_thanh']) ? 'disabled1' : '') . '>
                    <option value=""></option>
                    '.$_optionHT.'
                </select>
            </td>';

            if ($status_filter == 'CHT') {
                if ($dtProductionListsItem['hoan_thanh'] && $dtProductionListsItem['hoan_thanh'] == 'HT' ) {
                    continue;
                }
            } else if ($status_filter == 'HT') {
                if (empty($dtProductionListsItem['hoan_thanh']) || $dtProductionListsItem['hoan_thanh'] != 'HT' ) {
                    continue;
                }
            }


            $tdSoLuongSanXuat = '<td class="text-right td-so-luong-san-xuat">' . formatNumber($so_luong_san_xuat) . '</td>';
            $tdMatIn = '<td class="text-center">'.$mat.'</td>';

            $defaultLoaiCanhBai = ($type_productionlist_id == 3 || $type_productionlist_id == 7) ? 1 : '';
            $hideLoaiCanhBai = ($type_productionlist_id == 3 || $type_productionlist_id == 7) ? 'hide' : '';
            $tdLoaiCanhBai = '<td class="td-loai-canh-bai '.$hideLoaiCanhBai.' ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_canh_bai']) ? 'readonly' : '') . '" style="min-width: 80px !important; max-width: 80px !important; width: 80px !important;">
                '.render_select('items[' . $counter . '][loai_canh_bai]', $timeCardAlignment, ['id', 'name'], '', (!empty($dtProductionListsItem['loai_canh_bai']) ? ($dtProductionListsItem['loai_canh_bai']) : $defaultLoaiCanhBai), ['style' => 'width: 80px !important;', 'data-class' => 'loai_canh_bai', 'onchange' => 'totalProductionList('.$type_productionlist_id.')', (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_canh_bai']) ? 'disabled1' : '') => (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_canh_bai']) ? 'disabled1' : '')], [], '', '').'
            </td>';

            $tdSoLanThaySize = '<td class="td-so-lan-thay-size ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['so_lan_thay_size']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][so_lan_thay_size]" onchange="totalProductionList('.$type_productionlist_id.')" placeholder="'.lang('Số lần thay size').'" style="padding: 5; height: 30px; width: 80px;" class="form-control so_lan_thay_size number-format" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['so_lan_thay_size']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['so_lan_thay_size']) ? ($dtProductionListsItem['so_lan_thay_size']) : '').'">
            </td>';

            $tdSoLanRuaMay = '<td class="td-so-lan-rua-may ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['so_lan_rua_may']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][so_lan_rua_may]" onchange="totalProductionList('.$type_productionlist_id.')" placeholder="'.lang('Số lần rửa máy').'" style="padding: 5; height: 30px; width: 80px;" class="form-control so_lan_rua_may number-format" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['so_lan_rua_may']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['so_lan_rua_may']) ? ($dtProductionListsItem['so_lan_rua_may']) : '').'">
            </td>';

            $tdSoTuaInFlexo = '<td class="text-right td-so-tua-in-flexo"></td>';

            $tdNgayKetThuc = '<td class="text-right td-ngay-ket-thuc ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_ket_thuc']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][ngay_ket_thuc]" placeholder="'.lang('Ngày kết thúc').'" style="padding: 5; height: 30px; width: 100px;" class="form-control ngay_ket_thuc datepicker" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_ket_thuc']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['ngay_ket_thuc']) ? _d($dtProductionListsItem['ngay_ket_thuc']) : '').'">
            </td>';

            $tdToInBuHao = '<td class="text-right td-to-in-bu-hao">' . formatNumber($to_in_bu_hao) . '</td>';

            $so_mau_in = 1;
            if ($vOI['name_stage']) {
                $so_mau_in = preg_replace('/\D/', '', $vOI['name_stage']);
                if (empty($so_mau_in)) $so_mau_in = 1;
            }

            $tdSoMauIn = '<td class="text-right td-so-mat-in ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][so_mau_in]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px; width: 80px;" class="form-control number-format so_mau_in" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . ' value="' . (!empty($dtProductionListsItem) ? $dtProductionListsItem['so_mau_in'] : $so_mau_in) . '">
            </td>';

            $images = $vOI['images'] ? base_url("uploads/products/".$vOI['images']) : base_url("assets/images/tnh/no_image.png");
            $tdImage = '<td class="text-center">
                <div class="preview_image" style="width: auto;">
                    <div class="display-block contract-attachment-wrapper img">
                        <div style="width:30px; margin: auto;">
                            <a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5">
                                <div class="">
                                    <img src="'.$images.'"/>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </td>';

            $tdDauIn = '<td class="text-right td-dau-in ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['dau_in']) ? 'readonly' : '') . '" style="min-width: 80px !important; max-width: 80px !important; width: 80px !important;">
                '.render_select('items[' . $counter . '][dau_in]', $optionDauIn, ['id', 'name'], '', (!empty($dtProductionListsItem['dau_in']) ? ($dtProductionListsItem['dau_in']) : '300'), ['data-class' => 'dau_in', 'onchange' => 'totalProductionList('.$type_productionlist_id.')', (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['dau_in']) ? 'disabled1' : '') => (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['dau_in']) ? 'disabled1' : '')], [], '', '', false).'
            </td>';
            $tdNangSuat = '<td class="text-right td-nang-suat"></td>';

            $tdLoaiFlexo = '<td class="text-right td-loai-in-flexo ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_in_flexo']) ? 'readonly' : '') . '" style="min-width: 80px !important; max-width: 80px !important; width: 80px !important;">
                '.render_select('items[' . $counter . '][loai_in_flexo]', $optionLoaiFlexo, ['id', 'name'], '', (!empty($dtProductionListsItem['loai_in_flexo']) ? ($dtProductionListsItem['loai_in_flexo']) : '1'), ['data-class' => 'loai_in_flexo', 'onchange' => 'totalProductionList('.$type_productionlist_id.')', (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_in_flexo']) ? 'disabled1' : '') => (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_in_flexo']) ? 'disabled1' : '')], [], '', '', false).'
            </td>';

            $tdSoMatPhunBong = '<td class="text-right td-so-mat-phun-bong ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][so_mat_phun_bong]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;  width: 80px;" class="form-control number-format so_mat_phun_bong" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['so_mat_phun_bong']) : $countFace).'">
            </td>';

            $tdLoaiMay = '<td class="text-right td-loai-may ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai']) ? 'readonly' : '') . '" style="min-width: 80px !important; max-width: 80px !important; width: 80px !important;">
                '.render_select('items[' . $counter . '][loai]', $optionLoaiMay, ['id', 'name'], '', (!empty($dtProductionListsItem['loai']) ? ($dtProductionListsItem['loai']) : '1'), ['data-class' => 'loai_may', 'onchange' => 'totalProductionList('.$type_productionlist_id.')', (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai']) ? 'disabled1' : '') => (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai']) ? 'disabled1' : '')], [], '', '', false).'
            </td>';

            $tdSoLanCanhDao = '<td class="text-right td-so-lan-canh-dao ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][so_lan_canh_dao]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;  width: 80px;" class="form-control number-format so_lan_canh_dao" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['so_lan_canh_dao']) : '1').'">
            </td>';

            $tdSoLanVanHanh = '<td class="text-right td-so-lan-van-hanh ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][so_lan_van_hanh]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;  width: 80px;" class="form-control number-format so_lan_van_hanh" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['so_lan_van_hanh']) : '1').'">
            </td>';

            $tdLoaiBoi = '<td class="text-right td-loai-boi ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_boi']) ? 'readonly' : '') . '" style="min-width: 80px !important; max-width: 80px !important; width: 80px !important;">
                '.render_select('items[' . $counter . '][loai_boi]', $optionLoaiBoi, ['id', 'name'], '', (!empty($dtProductionListsItem['loai_boi']) ? ($dtProductionListsItem['loai_boi']) : '1'), ['data-class' => 'loai_boi', 'onchange' => 'totalProductionList('.$type_productionlist_id.')', (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_boi']) ? 'disabled1' : '') => (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_boi']) ? 'disabled1' : '')], [], '', '', false).'
            </td>';

            $tdLoaiGiay = '<td class="text-right td-loai-giay ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_giay']) ? 'readonly' : '') . '" style="min-width: 80px !important; max-width: 80px !important; width: 80px !important;">
                '.render_select('items[' . $counter . '][loai_giay]', $optionLoaiGiay, ['id', 'name'], '', (!empty($dtProductionListsItem['loai_giay']) ? ($dtProductionListsItem['loai_giay']) : '1'), ['data-class' => 'loai_giay', 'onchange' => 'totalProductionList('.$type_productionlist_id.')', (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_giay']) ? 'disabled1' : '') => (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_giay']) ? 'disabled1' : '')], [], '', '', false).'
            </td>';

            $tdLoaiXa = '<td class="text-right td-loai-giay ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_giay']) ? 'readonly' : '') . '" style="min-width: 80px !important; max-width: 80px !important; width: 80px !important;">
                '.render_select('items[' . $counter . '][loai_giay]', $optionLoaiXa, ['id', 'name'], '', (!empty($dtProductionListsItem['loai_giay']) ? ($dtProductionListsItem['loai_giay']) : '1'), ['data-class' => 'loai_xa', 'onchange' => 'totalProductionList('.$type_productionlist_id.')', (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_giay']) ? 'disabled1' : '') => (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['loai_giay']) ? 'disabled1' : '')], [], '', '', false).'
            </td>';

            $tdSoDuongDaoCat = '<td class="text-right td-so-duong-dao-cat ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][so_duong_dao_cat]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;  width: 80px;" class="form-control number-format so_duong_dao_cat" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['so_duong_dao_cat']) : '1').'">
            </td>';

            $so_lan_phun_bong = 1;
            if ($vOI['name_stage']) {
                $so_lan_phun_bong = preg_replace('/\D/', '', $vOI['name_stage']);
                $so_lan_phun_bong = max(str_split($so_lan_phun_bong));
                if (empty($so_lan_phun_bong)) $so_lan_phun_bong = 1;
            }
            $tdSoLanPhunBong = '<td class="text-right td-so-lan-phun-bong ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][so_lan_phun_bong]" onchange="totalProductionList('.$type_productionlist_id.')" style="padding: 5; height: 30px;  width: 80px;" class="form-control number-format so_lan_phun_bong" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['so_lan_phun_bong']) : $so_lan_phun_bong).'">
            </td>';

            $tdPhieuPTM = '<td><div class="text-center">' . ($vOI['is_ptm'] ? '<span class="text-danger">✓</span>' : '-') . '</div></td>';
            $tdMauSanXuat = '<td><div class="text-center">' . ($vOI['is_color'] ? '<span class="text-danger">✓</span>' : '-') . '</div></td>';
            $tdLayoutGhep = '<td><div class="text-center">' . ($vOI['is_layout'] ? '<span class="text-danger">✓</span>' : '-') . '</div></td>';
            $tdKhuanBe = '<td><div class="text-center">' . ($vOI['is_sewing'] ? '<span class="text-danger">✓</span>' : '-') . '</div></td>';
            $tdNPL = '<td><div class="text-center">' . ($vOI['is_npl'] ? '<span class="text-danger">✓</span>' : '-') . '</div></td>';
            $tdVatTu = '<td><div class="text-center">' . ($vOI['is_material'] ? '<span class="text-danger">✓</span>' : '-') . '</div></td>';
            $tdPhieuCatGiay = '<td><div class="text-center">' . ($vOI['is_cutting'] ? '<span class="text-danger">✓</span>' : '-') . '</div></td>';

            $tdNgayBatDauKeHoach = '<td class="' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_bat_dau_ke_hoach']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][ngay_bat_dau_ke_hoach]" placeholder="'.lang('Ngày bắt đầu kế hoạch').'" class="form-control datetimepicker" style="width: 140px;" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_bat_dau_ke_hoach']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['ngay_bat_dau_ke_hoach']) ? date_format(date_create($dtProductionListsItem['ngay_bat_dau_ke_hoach']), 'd/m/Y H:i') : '').'">
            </td>';
            $tdNgayKetThucKeHoach = '<td class="' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_ket_thuc_ke_hoach']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][ngay_ket_thuc_ke_hoach]" placeholder="'.lang('Ngày kết thúc kế hoạch').'" class="form-control datetimepicker" style="width: 140px;" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_ket_thuc_ke_hoach']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['ngay_ket_thuc_ke_hoach']) ? date_format(date_create($dtProductionListsItem['ngay_ket_thuc_ke_hoach']), 'd/m/Y H:i') : '').'">
            </td>';
            $tdNgayBatDauThucTe = '<td class="' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_bat_dau_thuc_te']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][ngay_bat_dau_thuc_te]" placeholder="'.lang('Ngày bắt đầu thực tế').'" class="form-control datetimepicker" style="width: 140px;" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_bat_dau_thuc_te']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['ngay_bat_dau_thuc_te']) ? date_format(date_create($dtProductionListsItem['ngay_bat_dau_thuc_te']), 'd/m/Y H:i') : '').'">
            </td>';
            $tdNgayKetThucThucTe = '<td class="' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_ket_thuc_thuc_te']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][ngay_ket_thuc_thuc_te]" placeholder="'.lang('Ngày kết thúc thực tế').'" class="form-control datetimepicker" style="width: 140px;" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['ngay_ket_thuc_thuc_te']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem['ngay_ket_thuc_thuc_te']) ? date_format(date_create($dtProductionListsItem['ngay_ket_thuc_thuc_te']), 'd/m/Y H:i') : '').'">
            </td>';
            $tdSoLuongThucTe = '<td class="' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['so_luong_thuc_te']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][so_luong_thuc_te]" placeholder="'.lang('Số lượng thực tế').'" class="form-control number-format" style="width: 80px;" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['so_luong_thuc_te']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem) ? formatNumber($dtProductionListsItem['so_luong_thuc_te']) : '').'">
            </td>';

            $tdThoiGianCanhBaiThucTe = '<td class="' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['thoi_gian_canh_bai_thuc_te']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][thoi_gian_canh_bai_thuc_te]" placeholder="'.lang('Thời gian canh bài thực tế').'" class="form-control number-format" style="width: 80px;" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['thoi_gian_canh_bai_thuc_te']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem) ? formatNumber($dtProductionListsItem['thoi_gian_canh_bai_thuc_te']) : '').'">
            </td>';

            $tdNPLCanhBaiThucTe = '<td class="' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['npl_canh_bai_thuc_te']) ? 'readonly' : '') . '">
                <input type="text" name="items[' . $counter . '][npl_canh_bai_thuc_te]" placeholder="'.lang('NPL canh bài thực tế').'" class="form-control" style="width: 80px;" ' . (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($dtProductionListsItem['npl_canh_bai_thuc_te']) ? 'readonly' : '') . ' value="'.(!empty($dtProductionListsItem) ? ($dtProductionListsItem['npl_canh_bai_thuc_te']) : '').'">
            </td>';
            
            if ($type_productionlist_id == 1) {
                //offset
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 2) {
                //flexo
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMauIn . '
                    ' . $tdSoConTrenKBFlexo.'
                    ' . $tdLoaiFlexo.'
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';

            } else if ($type_productionlist_id == 3) {
                //barcode
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdDauIn . '
                    ' . $tdNangSuat . '
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 7) {
                //Phun bóng
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatPhunBong . '
                    ' . $tdSoLanPhunBong . '
                    ' . $tdTongTua . '
                    ' . $tdLoaiMay . '
                    ' . $tdNangSuat . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 6) {
                //Cán màng
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 10) {
                //Định hình
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 11) {
                //cắt demi
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdSoLanCanhDao . '
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 12) {
                //Cán băng keo
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 8) {
                // Bồi
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdSoLanVanHanh . '
                    ' . $tdTongTua . '
                    ' . $tdLoaiBoi . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '
                    
                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 9) {
                // Bế
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdSoLanVanHanh . '
                    ' . $tdTongTua . '
                    ' . $tdLoaiGiay . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 13) {
                // Xả TP
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdSoDuongDaoCat . '
                    ' . $tdTongTua . '
                    ' . $tdLoaiXa . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 14) {
                // Khoan lỗ
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 15) {
                // Gở bể
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 16) {
                // Soạn
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            } else if ($type_productionlist_id == 4 || $type_productionlist_id == 5 || $type_productionlist_id == 17 || $type_productionlist_id == 20 || $type_productionlist_id == 19 || $type_productionlist_id == 26) {
                //HP
                $trHtml .= '<tr>
                    ' . $tdLenhSanXuat . '
                    ' . $tdMaSanPham . '
                    ' . $tdTenSanPham . '
                    ' . $tdImage . '
                    ' . $tdCongDoan . '
                    ' . $tdSoLuongSanXuat . '
                    ' . $tdSoConTrenToIn . '
                    ' . $tdSoToIn . '
                    ' . $tdToInBuHao . '
                    ' . $tdMatIn . '
                    ' . $tdSoMatIn . '
                    ' . $tdTongTua . '
                    ' . $tdThoiGianIn . '
                    ' . $tdLoaiCanhBai . '
                    ' . $tdThoiGianCanhBai . '
                    ' . $tdSoLanThaySize . '
                    ' . $tdSoLanRuaMay . '
                    ' . $tdThoiGianKhac . '
                    ' . $tdThoiGianXuLy . '
                    ' . $tdNgayMoLSX . '
                    ' . $tdNgayGiaoHang . '
                    ' . $tdThoiGianConLai.'

                    ' . $tdPhieuPTM.'
                    ' . $tdMauSanXuat.'
                    ' . $tdLayoutGhep.'
                    ' . $tdKhuanBe.'
                    ' . $tdNPL.'
                    ' . $tdVatTu.'
                    ' . $tdPhieuCatGiay.'
                    ' . $tdNgayVeNVLDuKien . '
                    ' . $tdNgayBatDauDuKien . '
                    ' . $tdNgayHoanThanhIn . '
                    ' . $tdMayIn . '

                    ' . $tdNgayBatDauKeHoach . '
                    ' . $tdNgayKetThucKeHoach . '
                    ' . $tdNgayBatDauThucTe . '
                    ' . $tdNgayKetThucThucTe . '
                    ' . $tdThoiGianCanhBaiThucTe . '
                    ' . $tdNPLCanhBaiThucTe . '
                    ' . $tdSoLuongThucTe . '

                    ' . $tdHoanThanh . '
                    ' . $tdGhiChu . '

                </tr>';
            }

            $counter++;
        }
    }
?>

<style>
    .div-table tr th {
        min-width: 80px !important;
    }

    .td-tinh-trang .dropdown-toggle {
        max-width: 120px !important;
        width: 120px !important;
    }

    .td-may-in .dropdown-toggle{
        max-width: 120px !important;
        width: 120px !important;
    }

    .td-loai-canh-bai .dropdown-toggle{
        width: 80px !important;
        max-width: 80px !important;
    }

    .td-dau-in .dropdown-toggle{
        width: 80px !important;
        max-width: 80px !important;
    }

    .td-loai-in-flexo .dropdown-toggle{
        width: 80px !important;
        max-width: 80px !important;
    }

    .td-loai-may .dropdown-toggle{
        width: 80px !important;
        max-width: 80px !important;
    }

    .td-loai-boi .dropdown-toggle{
        width: 80px !important;
        max-width: 80px !important;
    }

    .td-loai-giay .dropdown-toggle{
        width: 80px !important;
        max-width: 80px !important;
    }

    .DTFC_LeftBodyWrapper {
        border-right: 1px solid #ddd;
    }
</style>

<input type="hidden" name="type_productionlist_id" id="type_productionlist_id" class="form-control" value="<?= $type_productionlist_id ?>">
<?php if ($type_productionlist_id == 1) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['so_luong_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thoi_gian_cho_kho']) ? 'readonly' : '') ?>"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nhom_tho']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['bong_os_nhung']) ? 'readonly' : '') ?>"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <!-- <tr>
                            <td><?//= lang('Năng suất máy:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?//= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?//= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '' ?>">
                            </td>
                            <td><?//= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr> -->
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '5500' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                        <!-- <tr>
                            <td style="" class="bold"><?//= lang('Tổng tua in') ?></td>
                            <td class="text-right td-tong_tua_in">
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                        <!-- <th class="text-center"><?//= lang('Ngày giao hàng') ?></th> -->

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <!-- <th class="text-center"><?//= lang('Ngày bàn giao SX') ?></th> -->
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <!-- <th class="text-center"><?//= lang('Tình trạng') ?></th> -->
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>
                        <!-- <th class="text-center"><?//= lang('Số con/Tờ in') ?></th> -->
                        <!-- <th class="text-center"><?//= lang('Số con/KB') ?></th> -->
                        <!-- <th class="text-center"><?//= lang('Tua sau in') ?></th> -->
                        <!-- <th class="text-center"><?//= lang('Bóng màng') ?></th> -->

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($type_productionlist_id == 2) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-5 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 50px;"></td>
                            <td style="width: 80px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Số lượng thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_tho" style="padding: 0; height: 20px;" class="form-control number-format so_luong_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_tho']) : '' ?>">
                            </td>
                            <td><?= lang('thợ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td>
                                <!-- <input type="text" onchange="totalProductionList(<?//= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?//= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '' ?>"> -->
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="hide">
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may nang_suat_may_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '5000' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td class=""></td>
                            <td class=""></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy UV:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_uv']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_uv" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_uv nang_suat_may_2" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_uv']) : '3500' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td class=""></td>
                            <td class=""></td>
                        </tr>
                        <tr class="">
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>" style="width: 80px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr class="">
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 hide">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                    <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số màu in') ?></th>
                        <th class="text-center"><?= lang('Số con/khuôn bể') ?></th>
                        <th class="text-center"><?= lang('Loại in Flexo') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($type_productionlist_id == 3) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-5 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="7" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 10px;"></td>
                            <td style="width: 10px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Số lượng thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_tho" style="padding: 0; height: 20px;" class="form-control number-format so_luong_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_tho']) : '' ?>">
                            </td>
                            <td><?= lang('thợ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất đầu in 300:') ?></td>
                            <td>
                                <!-- <input type="text" onchange="totalProductionList(<?//= $type_productionlist_id ?>, 1)" name="nang_suat_may_in_300" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_in_300" value="<?//= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_in_300']) : '' ?>"> -->
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất đầu in 600:') ?></td>
                            <td>
                                <!-- <input type="text" onchange="totalProductionList(<?//= $type_productionlist_id ?>, 1)" name="nang_suat_may_in_600" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_in_600" value="<?//= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_in_600']) : '' ?>"> -->
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 hide">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất đầu in 300:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_in_300']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_in_300" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_in_300" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_in_300']) : '4000' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td class=""></td>
                            <td class=""></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất đầu in 600:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_in_600']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_in_600" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_in_600" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_in_600']) : '3000' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td class=""></td>
                            <td class=""></td>
                        </tr>
                        <tr class="">
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '5' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr class="hide">
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?//= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Đầu in') ?></th>
                        <th class="text-center"><?= lang('Năng suất') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center hide"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 7) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-5 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="7" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?></td>
                            <td><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;" >
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 10px;"></td>
                            <td style="width: 10px;"></td>
                        </tr>
                        <tr>
                            <td><?= lang('Số lượng thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_tho" style="padding: 0; height: 20px;" class="form-control number-format so_luong_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_tho']) : '' ?>">
                            </td>
                            <td><?= lang('thợ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất đầu in 300:') ?></td>
                            <td>
                                <!-- <input type="text" onchange="totalProductionList(<?//= $type_productionlist_id ?>, 1)" name="nang_suat_may_in_300" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_in_300" value="<?//= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_in_300']) : '' ?>"> -->
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất đầu in 600:') ?></td>
                            <td>
                                <!-- <input type="text" onchange="totalProductionList(<?//= $type_productionlist_id ?>, 1)" name="nang_suat_may_in_600" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_in_600" value="<?//= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_in_600']) : '' ?>"> -->
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian canh bài:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                            </td>
                            <td><?= lang('giờ/bài') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 hide">
                <table class="table dataTable table-bordered table-date" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy tự động:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_tu_dong']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_tu_dong" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_tu_dong nang_suat_may_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_tu_dong']) : '3000' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td class=""></td>
                            <td class=""></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy đặt tay:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_dat_tay']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_dat_tay" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_dat_tay nang_suat_may_2" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_dat_tay']) : '1500' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td class=""></td>
                            <td class=""></td>
                        </tr>
                        <tr class="hide">
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '5' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr class="">
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?//= $value['name'] ?></td>
                                <td style="width: 80px;">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt phun bóng') ?></th>
                        <th class="text-center"><?= lang('Số lần phun bóng') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Loại máy') ?></th>
                        <th class="text-center"><?= lang('Năng suất') ?></th>
                        <th class="text-center"><?= lang('Thời gian phun bóng') ?></th>
                        <th class="text-center hide"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 6) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '2000' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 10) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;" >
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '5500' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 11) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '5500' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Số lần canh dao') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 12) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '5500' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 8) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy bồi 1 mặt:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_boi_mot_mat']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_boi_mot_mat" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_boi_mot_mat" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_boi_mot_mat']) : '500' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy bồi 2 mặt:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_boi_hai_mat']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may_boi_hai_mat" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_boi_hai_mat" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_boi_hai_mat']) : '300' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Số lần vận hành') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Loại bồi') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 9) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy (bế giấy thường):') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_be_giay_thuong']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="nang_suat_may_be_giay_thuong" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_be_giay_thuong" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_be_giay_thuong']) : '1000' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy (demi, bế giấy bồi/ PET):') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_demi_be_giay_boi_pet']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nang_suat_may_demi_be_giay_boi_pet" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_demi_be_giay_boi_pet" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_demi_be_giay_boi_pet']) : '500' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Số lần vận hành') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Loại giấy') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 13) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất xả trên 32 con/tờ (1):') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_be_giay_thuong']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="nang_suat_may_be_giay_thuong" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_be_giay_thuong" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_be_giay_thuong']) : '1500' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất xả dưới 32 con/tờ (2):') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may_demi_be_giay_boi_pet']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nang_suat_may_demi_be_giay_boi_pet" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may_demi_be_giay_boi_pet" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may_demi_be_giay_boi_pet']) : '2000' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Số đường dao cắt') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Loại xả') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 14) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '10000' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 15) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '10000' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 16) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)"  name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '10000' ?>">
                            </td>
                            <td><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($type_productionlist_id == 4 || $type_productionlist_id == 5 || $type_productionlist_id == 17 || $type_productionlist_id == 20 || $type_productionlist_id == 19 || $type_productionlist_id == 26) : ?>
    <div class="div-table div-type_productionlist_id-<?= $type_productionlist_id ?>">
        <div class="row">
            <div class="col-md-6 hide">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td rowspan="6" class="text-center bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?><br>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                            <td style="min-width: 160px;"><?= lang('Số lượng máy:') ?></td>
                            <td style="width: 150px;">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="so_luong_may" style="padding: 0; height: 20px;" class="form-control number-format so_luong_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['so_luong_may']) : '' ?>">
                            </td>
                            <td><?= lang('máy') ?></td>
                            <td style="width: 100px;"><?= lang('Thời gian chờ khô') ?></td>
                            <td style="width: 100px;"></td>
                            <td style="width: 100px;"><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_cho_kho" style="padding: 0; height: 20px;" class="form-control thoi_gian_cho_kho" value="<?= !empty($production_lists_total) ? ($production_lists_total['thoi_gian_cho_kho']) : '' ?>"></td>
                            <td style="width: 50px;" class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Nhóm thợ:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="nhom_tho" style="padding: 0; height: 20px;" class="form-control number-format nhom_tho" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nhom_tho']) : '' ?>">
                            </td>
                            <td><?= lang('nhóm') ?></td>
                            <td><?= lang('Bóng OS/ Nhung') ?></td>
                            <td></td>
                            <td><input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="bong_os_nhung" style="padding: 0; height: 20px;" class="form-control number-format bong_os_nhung" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['bong_os_nhung']) : '' ?>"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <!-- <tr>
                            <td><?//= lang('Năng suất máy:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?//= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?//= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '' ?>">
                            </td>
                            <td><?//= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr> -->
                        <tr>
                            <td><div class="hide"><?= lang('Thời gian canh bài:') ?></div></td>
                            <td>
                                <div class="hide">
                                    <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="_thoi_gian_canh_bai" style="padding: 0; height: 20px;" class="form-control number-format _thoi_gian_canh_bai" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['_thoi_gian_canh_bai']) : '' ?>">
                                </div>
                            </td>
                            <td><div class="hide"><?= lang('giờ/bài') ?></div></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-1 text-right">
                                <input type="text" name="capacity_1" style="padding: 0; height: 20px;" class="form-control number-format capacity_1" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_1']) : '' ?>">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc chuẩn:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_chuan" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_chuan" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_chuan']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-2 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_2']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><?= lang('Thời gian làm việc có OT:') ?></td>
                            <td>
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thoi_gian_lam_viec_ot" style="padding: 0; height: 20px;" class="form-control number-format thoi_gian_lam_viec_ot" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['thoi_gian_lam_viec_ot']) : '' ?>">
                            </td>
                            <td><?= lang('giờ') ?></td>
                            <td><?= lang('Capacity') ?></td>
                            <td class="td-capacity-3 text-right"><?= !empty($production_lists_total) ? formatNumber($production_lists_total['capacity_3']) : '' ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;"><?= $dtCategoryStages['code'] ?>(<?= $dtCategoryStages['code_type_productionlist'] ?>)</td>
                        </tr>
                        <tr>
                            <td><?= lang('Năng suất máy:') ?></td>
                            <td class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['nang_suat_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>, 1)" name="nang_suat_may" style="padding: 0; height: 20px;" class="form-control number-format nang_suat_may" value="<?= !empty($production_lists_total) ? formatNumber($production_lists_total['nang_suat_may']) : '5500' ?>">
                            </td>
                            <td class="text-center"><?= lang('tua/giờ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            
                            <td><?= lang('Thay size:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['thay_size']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="thay_size" style="padding: 0; height: 20px;" class="form-control number-format thay_size" value="<?= isset($production_lists_total['thay_size']) ? formatNumber($production_lists_total['thay_size']) : '15' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-thay-size-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Rửa máy:') ?></td>
                            <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($production_lists_total['rua_may']) ? 'readonly' : '') ?>">
                                <input type="text" onchange="totalProductionList(<?= $type_productionlist_id ?>)" name="rua_may" style="padding: 0; height: 20px;" class="form-control number-format rua_may" value="<?= isset($production_lists_total['rua_may']) ? formatNumber($production_lists_total['rua_may']) : '30' ?>">
                            </td>
                            <td class="text-center"><?= lang('Phút') ?></td>
                            <td style="width: 80px;" class="td-rua-may-gio text-right"></td>
                            <td class="text-center"><?= lang('Giờ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <?php $counterCanhBai = 0; ?>
                        <?php if(!empty($timeCardAlignment)): ?>
                            <tr>
                                <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Canh bài</td>
                            </tr>
                            <?php foreach($timeCardAlignment as $key => $value): ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td style="width: 80px;" class="<?= (!$this->perEditProductionList && $this->perUpdateProductionList && !empty($value['minute']) ? 'readonly' : '') ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][id]" value="<?= $value['id'] ?>">
                                    <input type="hidden" name="items_canh_bai[<?= $counterCanhBai ?>][name]" value="<?= $value['name'] ?>">
                                    <input type="text" onchange="changeCanhBai(this, <?= $value['id'] ?>, <?= $type_productionlist_id ?>)" name="items_canh_bai[<?= $counterCanhBai ?>][minute]" style="padding: 0; height: 20px;" class="form-control number-format canh_bai_<?= $value['id'] ?>" value="<?= $value['minute'] ?>">
                                </td>
                                <td class="text-center"><?= lang('Phút') ?></td>
                                <td style="width: 80px;" class="td-gio text-right"><?= formatNumber($value['minute']/60, 2) ?></td>
                                <td class="text-center"><?= lang('Giờ') ?></td>
                            </tr>
                            <?php $counterCanhBai++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table id="tb-machines" class="table dataTable table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Máy móc') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng số tua') ?></td>
                            <td style="width: 100px; background: #607d8b; padding: 10px 10px 5px 10px;" class="text-left bold color-white"><?= lang('Tổng thời gian') ?></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table dataTable table-bordered" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-left bold color-white" style="background: #607d8b;">Còn lại</td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng thời gian còn lại') ?></td>
                            <td class="text-right td-tong_thoi_gian_con_lai">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 200px;" class="bold"><?= lang('Tổng tua in còn lại') ?></td>
                            <td class="text-right td-tong_tua_in_con_lai">
                            </td>
                        </tr>
                        <!-- <tr>
                            <td style="" class="bold"><?//= lang('Tổng tua in') ?></td>
                            <td class="text-right td-tong_tua_in">
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table dataTable table-bordered table-date hide" style="width: 100%;">
                    <tbody>
                        <?php if (!empty($chunkArrDate)) : ?>
                            <?php foreach ($chunkArrDate as $key => $arDate) : ?>
                                <tr>
                                    <?php
                                    $tdRow2 = '';
                                    ?>
                                    <?php foreach ($arDate as $k => $v) : ?>
                                        <td class="text-center"><?= $v['day_month'] ?></td>
                                        <?php
                                        $tdRow2 .= '<td style="padding: 0;" class="text-center text-danger '.$v['_date'].'" data-date="'.$v['_date'].'"></td>';
                                        ?>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="tr-sum">
                                    <?= $tdRow2 ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mtop10">
            <table id="tb-items" class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th class="text-center"><?= lang('Lệnh sản xuất') ?></th>
                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                        <th class="text-center"><?= lang('Tổng số con') ?></th>
                        <th class="text-center"><?= lang('Số con/tờ') ?></th>
                        <th class="text-center"><?= lang('Tổng tờ in') ?></th>
                        <th class="text-center"><?= lang('Tờ in bù hao') ?></th>
                        <th class="text-center"><?= lang('Mặt in') ?></th>
                        <th class="text-center"><?= lang('Số mặt in') ?></th>
                        <th class="text-center"><?= lang('Tổng tua') ?></th>
                        <th class="text-center"><?= lang('Thời gian in') ?></th>
                        <th class="text-center"><?= lang('Loại') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài') ?></th>
                        <th class="text-center"><?= lang('Số lần thay size') ?></th>
                        <th class="text-center"><?= lang('Số lần rửa máy') ?></th>
                        <th class="text-center"><?= lang('Thời gian khác') ?></th>
                        <th class="text-center"><?= lang('Thời gian xử lý') ?></th>
                        <th class="text-center"><?= lang('Ngày mở lệnh') ?></th>
                        <th class="text-center"><?= lang('Ngày GH hệ thống') ?></th>
                        <th class="text-center"><?= lang('Thời gian còn lại') ?></th>
                        <!-- <th class="text-center"><?//= lang('Ngày giao hàng') ?></th> -->

                        <th class="text-center"><?= lang('Phiếu PTM') ?></th>
                        <th class="text-center"><?= lang('Mẫu Sản Xuất') ?></th>
                        <th class="text-center"><?= lang('Layout Ghép') ?></th>
                        <th class="text-center"><?= lang('Khuân Bế') ?></th>
                        <th class="text-center"><?= lang('NPL') ?></th>
                        <th class="text-center"><?= lang('Vật Tư') ?></th>
                        <th class="text-center"><?= lang('Phiếu Cắt Giấy') ?></th>
                        <th class="text-center"><?= lang('Ngày về NVL dự kiến') ?></th>
                        <!-- <th class="text-center"><?//= lang('Ngày bàn giao SX') ?></th> -->
                        <th class="text-center"><?= lang('Ngày bắt đầu dự kiến') ?></th>
                        <th class="text-center"><?= lang('Ngày hoàn thành in') ?></th>
                        <!-- <th class="text-center"><?//= lang('Tình trạng') ?></th> -->
                        <th class="text-center"><?= lang('Máy in dự kiến') ?></th>
                        <!-- <th class="text-center"><?//= lang('Số con/Tờ in') ?></th> -->
                        <!-- <th class="text-center"><?//= lang('Số con/KB') ?></th> -->
                        <!-- <th class="text-center"><?//= lang('Tua sau in') ?></th> -->
                        <!-- <th class="text-center"><?//= lang('Bóng màng') ?></th> -->

                        <th class="text-center"><?= lang('Ngày bắt đầu kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc kế hoạch') ?></th>
                        <th class="text-center"><?= lang('Ngày bắt đầu thực tế') ?></th>
                        <th class="text-center"><?= lang('Ngày kết thúc thực tế') ?></th>
                        <th class="text-center"><?= lang('Thời gian canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('NPL canh bài thực tế') ?></th>
                        <th class="text-center"><?= lang('Số lượng thực tế') ?></th>

                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                        <th class="text-center"><?= lang('Ghi chú') ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?= $trHtml ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
    var dtItems = '';
    function appSelectPickerBody(e) {
        void 0 === e && (e = $("body").find("select.selectpicker")), e.length && e.selectpicker({showSubtext: !0, container: 'body'})
    }

    $(document).ready(function () {
        totalProductionList(<?= $type_productionlist_id ?>, 1);
        init_datepicker();
        appSelectPickerBody();

        _height_body = parseInt(height_body, 10);
        _height_body = (_height_body - 180)+'px';

        dtItems = $('#tb-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            scrollY: _height_body,
            scrollX: true,
            fixedColumns: {
                leftColumns: 8,
                rightColumns: 0
            },
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
            },
        });

        $(window).on('load', function() {
            dtItems.columns.adjust();
        });

        $(document).ready(function () {
            dtItems.columns.adjust();
        });

        <?php if ($type_productionlist_id != 1): ?>
            // $('.exportExcelModerationPlan').hide();
        <?php endif; ?>
    });
</script>