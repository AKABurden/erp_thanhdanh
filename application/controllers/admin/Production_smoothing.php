<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Carbon\Carbon;

class Production_smoothing extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = [];
        $data['title'] = lang('Điều độ sản xuất');
        $data['machines'] = get_table_where('tbl_machines');
        $data['stage'] = get_table_where('tbl_stages',['id !=' => STAGES_MATERIAL]);
        $this->load->view('admin/production_smoothing/index', $data);
    }

    public function loadTabMachines()
    {
        $data = [];
        $arrId = $this->input->post('arrId');
        $machines = [];
        if (!empty($arrId)) {
            $this->db->select('tbl_machines.*');
            $this->db->from('tbl_machines');
            $this->db->where_in('tbl_machines.id', $arrId);
            $machines = $this->db->get()->result_array();
        }
        $data['machines'] = $machines;
        echo json_encode($data);
    }

    public function changeMachines()
    {
        $data = [];
        $machines_id = $this->input->post('id');
        $stage_search = $this->input->post('stage_search');
        if ($machines_id == 'all') {
            $result[] = [
                'id' => 'all',
                'name' => 'Quy trình'
            ];
        } elseif ($machines_id == 'all_new') {
            $this->db->select('tbl_stages.id as id,tbl_stages.name as name');
            $this->db->from('tbl_stages');
            $this->db->where('tbl_stages.id !=', STAGES_MATERIAL);
            if (!empty($stage_search)) {
                $this->db->where_in('tbl_stages.id', $stage_search);
            }
            $result = $this->db->get()->result_array();
        } else {
            $this->db->select('tbl_stages.id as id,tbl_stages.name as name');
            $this->db->from('tbl_category_stages');
            $this->db->join('tbl_machines_stage', 'tbl_machines_stage.category_stage_id = tbl_category_stages.id');
            $this->db->join('tbl_stages', 'tbl_stages.category_stages = tbl_category_stages.id');
            $this->db->where('tbl_machines_stage.machines_id', $machines_id);
            $this->db->where('tbl_stages.id !=', STAGES_MATERIAL);
            if (!empty($stage_search)) {
                $this->db->where_in('tbl_stages.id', $stage_search);
            }
            $result = $this->db->get()->result_array();
        }
        $data['result'] = $result;
        if ($machines_id == 'all_new') {
            $this->load->view('admin/production_smoothing/load_view_data_table_new', $data);
        } elseif($machines_id == 'all') {
            $this->load->view('admin/production_smoothing/load_view_data_table_smoothing', $data);
        } else {
            $this->load->view('admin/production_smoothing/load_view_data_table', $data);
        }
    }

    public function getProductionSmoothing()
    {
        $name_search = $this->input->post('name_search');
        $staff_search = $this->input->post('staff_search');
        $status_table = $this->input->post('status_table');
        $productions_orders_search = $this->input->post('productions_orders_search');
        $stage_search = $this->input->post('stage_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $machines_id = $status_table;

        $tbDateOrder = "(
            SELECT 
                tbl_order_item_shippings.order_item_id as order_item_id,
                tbl_order_item_shippings.date_shipping as date_shipping
            FROM tbl_order_item_shippings
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_date_order";

        $tbDateBusiness = "(
            SELECT 
                tbl_business_plan_items_date.business_plan_items_id as business_plan_items_id,
                tbl_business_plan_items_date.date as date_shipping
            FROM tbl_business_plan_items_date
            GROUP BY tbl_business_plan_items_date.business_plan_items_id
        ) tb_date_business";


        $tbProcessStage = "(
            SELECT 
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                COUNT(tbl_productions_orders_items_stages.id) as total_stage
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.machines_id != 0
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
        ) tb_process_stage";


        $tbProcessStageActive = "(
            SELECT 
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                tbl_productions_orders_items_stages.active as active
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.final_stage = 1
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
        ) tb_process_stage_active";

        $aColumns = [
            'tbl_productions_orders_details.id as id',
            'tbl_productions_orders_details.reference_no as reference_no',
            'tbl_productions_orders.reference_no as reference_no_production_order',
            'tbl_productions_orders_details.date_created as date_created',
            'IF(tbl_productions_orders_items.object_item_type = "business_plan",tb_date_business.date_shipping,tb_date_order.date_shipping) as date_delivery',
            'IF(tbl_productions_orders_details.object_type = "business_plan",tbl_business_plan.plan_name,tbl_orders.customer_name) as company',
        ];
        $resultNew = [];
        if ($status_table != 'all' && $status_table != 'all_new') {
            if (!empty($status_table)) {
                $this->db->select('tbl_stages.id as id_new,tbl_stages.name as name');
                $this->db->from('tbl_category_stages');
                $this->db->join('tbl_machines_stage', 'tbl_machines_stage.category_stage_id = tbl_category_stages.id');
                $this->db->join('tbl_stages', 'tbl_stages.category_stages = tbl_category_stages.id');
                $this->db->where('tbl_machines_stage.machines_id', $status_table);
                $this->db->where('tbl_stages.id !=', STAGES_MATERIAL);
                if (!empty($stage_search)) {
                    $this->db->where_in('tbl_stages.id', $stage_search);
                }
                $resultNew = $this->db->get()->result_array();
                if (!empty($resultNew)) {
                    foreach ($resultNew as $kk => $vv) {
                        $name = $vv['name'];
                        $aColumns[] = "'$name'";
                    }
                }
            }
        } else {
            $name = 'all';
            $aColumns[] = "'$name'";
            $resultNew[] = [
                'id_new' => 'all',
                'name' => 'all'
            ];
        }
        $sIndexColumn = 'id';
        $sTable = 'tbl_productions_orders_details';
        if ($machines_id != 'all' && $machines_id != 'all_new') {
            $where = [
                'AND EXISTS (
                    SELECT tbl_productions_orders_items_stages.productions_orders_items_id
                    FROM tbl_productions_orders_items_stages
                    WHERE tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_items.id
                    AND tbl_productions_orders_items_stages.machines_id = ' . $machines_id . '
                )'
            ];
        } else {
            $where = [
            ];
        }
        $filter = [];
        $join = [
            'LEFT JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"',
            'LEFT JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"',
            'INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id',
            "LEFT JOIN $tbDateOrder ON tb_date_order.order_item_id = tbl_productions_orders_items.production_plan_item_id AND tbl_productions_orders_items.object_item_type = 'orders'",
            "LEFT JOIN $tbDateBusiness ON tb_date_business.business_plan_items_id = tbl_productions_orders_items.production_plan_item_id AND tbl_productions_orders_items.object_item_type = 'business_plan'",
            "LEFT JOIN $tbProcessStage ON tb_process_stage.productions_orders_items_id = tbl_productions_orders_items.id",
        ];

        array_push($where,
            'AND EXISTS (
                    SELECT tb_process_stage_active.productions_orders_items_id
                    FROM '.$tbProcessStageActive.'
                    WHERE tb_process_stage_active.productions_orders_items_id = tbl_productions_orders_items.id
                    AND tb_process_stage_active.active = 0
                )'
        );

        if (!empty($productions_orders_search)) {
            array_push($where,
                'AND ( tbl_productions_orders_details.productions_orders_id = ' . $productions_orders_search . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders_details.date_created >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders_details.date_created <= '$end_date_search'");
        }

        if (!empty($stage_search)) {
            array_push($where,
                'AND EXISTS (
                    SELECT tbl_productions_orders_items_stages.productions_orders_items_id
                    FROM tbl_productions_orders_items_stages
                    WHERE tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_items.id
                    AND tbl_productions_orders_items_stages.stage_id IN (' . implode(',', $stage_search) . ')
                )');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id',
            'tb_process_stage.total_stage as total_stage',
            'tbl_productions_orders_items.items_id as items_id',
            'tbl_productions_orders_items.items_name as items_name',
            'tbl_productions_orders_items.items_code as items_code',
            'tbl_productions_orders_items.quantity as quantity',
        ], '', [], []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        $arrCheck = [];
        if (!empty($resultNew)) {
            foreach ($resultNew as $kk => $vv) {
                $arrCheck['check_' . $vv['id_new']] = 0;
            }
        }
        // usort($rResult, ch_make_cmp(['total_stage' => "asc"]));
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $row[0] = '<div class="text-center">' . (++$key) . '</div>';
            $row[1] = '<div style="min-width: 150px"><a target="_blank" href="' . base_url('admin/manufactures/detail_productions/' . $aRow['id']) . '">' . $aRow['reference_no'] . '</a>
            <br><span style="font-style: italic;font-size: 12px">' . $aRow['items_code'] . '</span>
            <br><span style="color: red;font-size: 11px;font-style: italic" class="total_hour_new"></span></div>';
            $row[2] = '<div style="min-width: 100px" class="text-center">' . $aRow['reference_no_production_order'] . '</div>';
            $row[3] = '<div style="min-width: 100px" class="text-center">' . _dhau($aRow['date_created']) . '</div>';
            $row[4] = '<div style="min-width: 100px" class="text-center">' . (!empty($aRow['date_delivery']) ? _dhau($aRow['date_delivery']) : '') . '</div>';
            $row[5] = '<div style="min-width: 150px">' . $aRow['company'] . '</div>';
            $i = 6;
            $totalHour = 0;
            $checkFinish = false;
            $checkFinishNew = false;
            if (!empty($resultNew)) {
                foreach ($resultNew as $kk => $vv) {
                    $workflow = '';
                    $dataStage = '';
                    $check = 0;
                    if ($vv['id_new'] == 'all') {
                        $check = 1;
                        //process
                        $productions_orders_items_id = $aRow['productions_orders_item_id'];
                        $this->db->select('
                            tbl_productions_orders_items_stages.id as id,
                            tbl_productions_orders_items_stages.active as active,
                            tbl_productions_orders_items_stages.staff_active as staff_active,
                            tbl_productions_orders_items_stages.date_active as date_active,
                            tbl_stages.name as stage_name,
                            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                            IF (tblstaff.profile_image IS NOT NULL, CONCAT("' . base_url('uploads/staff_profile_images/') . '", tblstaff.staffid, "/small__", tblstaff.profile_image), null) as staff_image,
                            tbl_productions_orders_items_stages.final_stage as final_stage,
                            tbl_productions_orders_items_stages.machines_id as machines_id,
                            tbl_productions_orders_items_stages.date_start as date_start,
                            tbl_productions_orders_items_stages.date_end as date_end,
                            tbl_machines.name as name_machines,
                            tbl_machines.quota_productivity as quota_productivity,
                            tbl_machines.preparation_time as preparation_time
                        ', false);
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                        $this->db->join('tblstaff',
                            'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
                        $this->db->join('tbl_machines',
                            'tbl_machines.id = tbl_productions_orders_items_stages.machines_id', 'left');
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id',
                            $productions_orders_items_id);
                        $this->db->where('tbl_stages.id !=', STAGES_MATERIAL);
                        $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
                        $process = $this->db->get()->result_array();
                        $workflow = '';
                        $li = '';
                        $isActive = 0;
                        if (!empty($process)) {
                            foreach ($process as $kkk => $vvv) {

                                $checkPurchase = get_table_where('tbl_purchase_products',
                                    ['pois_id' => $vvv['id']],
                                    '', 'row_array');
                                $checkEdit = false;
                                if (!empty($checkPurchase) || $vvv['active'] == 1) {
                                    $checkEdit = true;
                                }

                                $htmlMachines = '<div></div>';
                                if (!empty($vvv['machines_id'])) {
                                    $htmlMachines = '<div style="color: #d94a06">' . $vvv['name_machines'] . '</div>';
                                    $isActive = $vvv['active'];
                                }
                                $li .= '<li ' . ($vvv['machines_id'] ? 'class="active"' : '') . '>' . $vvv['stage_name'] .
                                    $htmlMachines .
                                    (empty($checkEdit) ? '<a data-tnh="modal" style="color:#03a9f4;cursor:pointer" class="tnh-modal" href="' . base_url('admin/production_smoothing/add_machines/' . $vvv['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i></a>' : '')
                                    . '</li>';

                                //new
                                $datetime1 = new Carbon($vvv['date_start']);
                                if (!empty($vvv['date_end'])) {
                                    $datetime2 = new Carbon($vvv['date_end']);
                                    $minute = $datetime2->diffInMinutes($datetime1);
                                    $hour = round($minute / 60, 2);
                                } else {
                                    $hour = 0;
                                }

                                $hour += $vvv['preparation_time'];
                                $totalHour += $hour;
//                                $items_id = $aRow['items_id'];
//                                $product = get_table_where('tbl_products',['id' => $items_id],'','row_array');
//                                $quantity_child_sheet = !empty($product['quantity_child_sheet']) ? $product['quantity_child_sheet'] : 0;
//                                $quota_productivity = !empty($vvv['quota_productivity']) ? $vvv['quota_productivity'] : 0;
//                                $quantity = $aRow['quantity'];
//
//                                if ($quantity_child_sheet != 0  && $quota_productivity != 0) {
//                                    $totalHourNew = ($quantity / $quantity_child_sheet) / $quota_productivity;
//                                    $totalHour += $totalHourNew;
//                                } else {
//                                    $totalHourNew = 0;
//                                    $totalHour += $totalHourNew;
//                                }
                                //end
                            }
                        }
                        $workflow .= '<div style="display: table; justify-content: center;">
                            <ul class="progressbar" style="display: flex;">
                            ' . $li . '
                            </ul>
                        </div>';

                    } else {
                        if ($vv['id_new'] == 'all_new'){
                            continue;
                        }
                        $dateNow = date('Y-m-d H:i:s');
                        $productions_orders_items_id = $aRow['productions_orders_item_id'];
                        $stage_id = $vv['id_new'];
                        $this->db->select('
                            tbl_productions_orders_items_stages.date_start as date_start,
                            tbl_productions_orders_items_stages.date_end as date_end,
                            tbl_machines.name as name_machines,
                            tbl_machines.quota_productivity as quota_productivity,
                            tbl_productions_orders_items_stages.active as active,
                            tbl_productions_orders_items_stages.date_active as date_active,
                            tbl_machines.preparation_time as preparation_time
                        ');
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_machines',
                            'tbl_machines.id = tbl_productions_orders_items_stages.machines_id');
                        $this->db->where('productions_orders_items_id', $productions_orders_items_id);
                        $this->db->where('tbl_productions_orders_items_stages.machines_id', $status_table);
                        $this->db->where('stage_id', $stage_id);
                        $checkItemStage = $this->db->get()->row_array();
                        $checkFinish = false;
                        $checkFinishNew = false;
                        if (!empty($checkItemStage)) {
                            if ($checkItemStage['active'] == 1) {
                                $checkFinish = true;
                            } else {
                                $checkFinish = false;
                                if (!empty($checkItemStage['date_end'])) {
                                    $date_end_new = $checkItemStage['date_end'];
                                    $dateNowNew = strtotime($dateNow);
                                    $date_end_new_vs1 = strtotime($date_end_new);
                                    if ($dateNowNew > $date_end_new_vs1) {
                                        $checkFinishNew = true;
                                    }
                                } else {
                                    $checkFinishNew = false;
                                }
                            }

                            $datetime1 = new Carbon($checkItemStage['date_start']);
                            if (!empty($checkItemStage['date_end'])) {
                                $datetime2 = new Carbon($checkItemStage['date_end']);
                                $minute = $datetime2->diffInMinutes($datetime1);
                                $hour = round($minute / 60, 2);
                            } else {
                                $hour = 0;
                            }
                            $hour += $checkItemStage['preparation_time'];
                            $totalHour += $hour;

//                            $items_id = $aRow['items_id'];
//                            $product = get_table_where('tbl_products',['id' => $items_id],'','row_array');
//                            $quantity_child_sheet = !empty($product['quantity_child_sheet']) ? $product['quantity_child_sheet'] : 0;
//                            $quota_productivity = !empty($checkItemStage['quota_productivity']) ? $checkItemStage['quota_productivity'] : 0;
//                            $quantity = $aRow['quantity'];
//
//                            if ($quantity_child_sheet != 0  && $quota_productivity != 0) {
//                                $totalHourNew = ($quantity / $quantity_child_sheet) / $quota_productivity;
//                                $totalHour += $totalHourNew;
//                            } else {
//                                $totalHourNew = 0;
//                                $totalHour += $totalHourNew;
//                            }
                            $htmlDateActive = '';
                            if (!empty($checkFinish)) {
                                if (empty($checkFinishNew)) {
                                    $htmlDateActive = '<div style="font-size: 10px;border-top:1px solid white">HT: ' . _dnew($checkItemStage['date_active']) . '</div>';
                                }
                            }
                            $check = 1;
                            $dataStage = '<div><span style="color: ' . (!empty($checkFinish) || !empty($checkFinishNew) ? 'white' : 'green') . '">' . $checkItemStage['name_machines'] . '</span> - ' . '<span  style="color: ' . (!empty($checkFinish) || !empty($checkFinishNew) ? 'white' : 'red') . '">' . $hour . ' (giờ)</span></div>
                            <div style="font-size: 10px">BĐ: ' . _dnew($checkItemStage['date_start']) . '<br>' . (!empty($checkItemStage['date_end']) ? 'KT: ' . _dnew($checkItemStage['date_end']) : '') . ' </div>' . $htmlDateActive;
                        }
                    }
                    $htmlFinish = '';
                    $htmlFinishNew = '';
                    if (!empty($checkFinish)) {
                        $htmlFinish = '<input type="hidden" class="check_finish" value="' . $checkFinish . '">';
                    }
                    if (!empty($checkFinishNew)) {
                        $htmlFinishNew = '<input type="hidden" class="check_finish_new" value="' . $checkFinishNew . '">';
                    }
                    $htmlCheckEmpty = '';
                    if (empty($check)) {
                        $htmlCheckEmpty = '<input type="hidden" data-key="' . $i . '" class="check_empty" value="1">';
                        if ($arrCheck['check_' . $vv['id_new']] == 0) {
                            $arrCheck['check_' . $vv['id_new']] = $i;
                        }
                    } else {
                        if ($arrCheck['check_' . $vv['id_new']] != -1) {
                            $arrCheck['check_' . $vv['id_new']] = -1;
                        }
                    }

                    $row[$i] = '<div class="text-left" style="min-width: 110px;">
                            ' . $workflow . '
                            ' . $dataStage . '
                            ' . $htmlFinish . '
                            ' . $htmlFinishNew . '
                            ' . $htmlCheckEmpty . '
                    </div>';
                    $i++;
                }
            }
            $attr = [
                'data-hour' => round($totalHour, 3)
            ];
            $row['DT_RowClass'] = 'tr-new';
            $row['DT_RowAttr'] = $attr;
            $output['aaData'][] = $row;
            $stt++;

        }
        $output['arrCheck'] = $arrCheck;
        echo json_encode($output);
    }

    public function getProductionSmoothingNew()
    {
        $status_table = $this->input->post('status_table');
        $productions_orders_search = $this->input->post('productions_orders_search');
        $stage_search = $this->input->post('stage_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $machines_id = $status_table;

        $tbProcessStageActive = "(
            SELECT 
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                tbl_productions_orders_items_stages.active as active
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.final_stage = 1
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
        ) tb_process_stage_active";

        $aColumns = [
            'tbl_machines.id as id',
            'tbl_machines.name as reference_no',
        ];
        $resultNew = [];
        $this->db->select('tbl_stages.id as id_new,tbl_stages.name as name');
        $this->db->from('tbl_stages');
        $this->db->where('tbl_stages.id !=', STAGES_MATERIAL);
        if (!empty($stage_search)) {
            $this->db->where_in('tbl_stages.id', $stage_search);
        }
        $resultNew = $this->db->get()->result_array();
        if (!empty($resultNew)) {
            foreach ($resultNew as $kk => $vv) {
                $name = $vv['name'];
                $aColumns[] = "'$name'";
            }
        }
        $sIndexColumn = 'id';
        $sTable = 'tbl_machines';
        $where = [

        ];
        $filter = [];
        $join = [
        ];

        $whereOrder = '';
        if (!empty($productions_orders_search)){
            $whereOrder .= ' AND tbl_productions_orders_details.productions_orders_id = '.$productions_orders_search.'';
        }

        if (!empty($start_date_search)) {
            $start_date_search_new1 = to_sql_date($start_date_search) . ' 00:00:00';
            $whereOrder .= " AND tbl_productions_orders_details.date_created >= '$start_date_search_new1'";
        }

        if (!empty($end_date_search)) {
            $end_date_search_new1 = to_sql_date($end_date_search) . ' 23:59:59';
            $whereOrder .= " AND tbl_productions_orders_details.date_created <= '$end_date_search_new1'";
        }


        array_push($where, "AND EXISTS (
                SELECT tbl_productions_orders_items_stages.machines_id
                FROM tbl_productions_orders_items_stages
                INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id
                INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
                WHERE tbl_productions_orders_items_stages.machines_id = tbl_machines.id $whereOrder
                AND EXISTS (
                    SELECT tb_process_stage_active.productions_orders_items_id
                    FROM $tbProcessStageActive
                    WHERE tb_process_stage_active.productions_orders_items_id = tbl_productions_orders_items.id
                    AND tb_process_stage_active.active = 0
                )
        )");


        if (!empty($stage_search)) {
            array_push($where, 'AND EXISTS (
                SELECT tbl_productions_orders_items_stages.machines_id
                FROM tbl_productions_orders_items_stages
                INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id
                INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
                WHERE tbl_productions_orders_items_stages.machines_id = tbl_machines.id AND tbl_productions_orders_items_stages.stage_id IN ('.implode(',',$stage_search).') '.$whereOrder.'
            )');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', [], []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        $arrCheck = [];
        if (!empty($resultNew)) {
            foreach ($resultNew as $kk => $vv) {
                $arrCheck['check_' . $vv['id_new']] = 0;
            }
        }
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $row[0] = '<div class="text-center">' . (++$key) . '</div>';
            $row[1] = '<div style="min-width: 150px">' . $aRow['reference_no'] . '
            <br><span style="color: red;font-size: 11px;font-style: italic" class="total_hour_new"></span></div>';
            $i = 2;
            $totalHour = 0;
            if (!empty($resultNew)) {
                foreach ($resultNew as $kk => $vv) {
                    $workflow = '';
                    $dataStage = '';
                    $check = 0;
                    $dateNow = date('Y-m-d H:i:s');
                    $productions_orders_items_id = $aRow['id'];
                    $stage_id = $vv['id_new'];

                    $tbProcessStageActive = "(
                        SELECT 
                            tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                            tbl_productions_orders_items_stages.active as active
                        FROM tbl_productions_orders_items_stages
                        WHERE tbl_productions_orders_items_stages.final_stage = 1
                        GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
                    ) tb_process_stage_active";

                    $this->db->select('
                        tbl_productions_orders_details.id as id,
                        tbl_productions_orders_details.reference_no as reference_no,
                        tbl_productions_orders_items_stages.date_start as date_start,
                        tbl_productions_orders_items_stages.date_end as date_end,
                        tbl_machines.name as name_machines,
                        tbl_machines.quota_productivity as quota_productivity,
                        tbl_productions_orders_items_stages.active as active,
                        tbl_productions_orders_items_stages.date_active as date_active,
                        tbl_machines.preparation_time as preparation_time
                    ');
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_machines',
                        'tbl_machines.id = tbl_productions_orders_items_stages.machines_id');
                    $this->db->join('tbl_productions_orders_items',
                        'tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id');
                    $this->db->join('tbl_productions_orders_details',
                        'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
                    $this->db->where('tbl_productions_orders_items_stages.machines_id', $productions_orders_items_id);
                    $this->db->where('stage_id', $stage_id);

                    $this->db->where('EXISTS (
                         SELECT tb_process_stage_active.productions_orders_items_id
                         FROM '.$tbProcessStageActive.'
                         WHERE tb_process_stage_active.productions_orders_items_id = tbl_productions_orders_items.id
                         AND tb_process_stage_active.active = 0
                    )');

                    if (!empty($productions_orders_search)) {
                        $this->db->where('tbl_productions_orders_details.productions_orders_id',$productions_orders_search);
                    }

                    if (!empty($start_date_search)) {
                        $start_date_search_new = to_sql_date($start_date_search) . ' 00:00:00';
                        $this->db->where("tbl_productions_orders_details.date_created >= '$start_date_search_new'");
                    }

                    if (!empty($end_date_search)) {
                        $end_date_search_new = to_sql_date($end_date_search) . ' 23:59:59';
                        $this->db->where("tbl_productions_orders_details.date_created <= '$end_date_search_new'");
                    }
                    $checkItemStage = $this->db->get()->result_array();
                    if (!empty($checkItemStage)) {
                        foreach ($checkItemStage as $kkk => $vvv ) {
                            $checkFinish = false;
                            $checkFinishNew = false;
                            if ($vvv['active'] == 1) {
                                $checkFinish = true;
                            } else {
                                $checkFinish = false;
                                if (!empty($vvv['date_end'])) {
                                    $date_end_new = $vvv['date_end'];
                                    $dateNowNew = strtotime($dateNow);
                                    $date_end_new_vs1 = strtotime($date_end_new);
                                    if ($dateNowNew > $date_end_new_vs1) {
                                        $checkFinishNew = true;
                                    }
                                } else {
                                    $checkFinishNew = false;
                                }
                            }

                            $datetime1 = new Carbon($vvv['date_start']);
                            if (!empty($vvv['date_end'])) {
                                $datetime2 = new Carbon($vvv['date_end']);
                                $minute = $datetime2->diffInMinutes($datetime1);
                                $hour = round($minute / 60, 2);
                            } else {
                                $hour = 0;
                            }
                            $hour += $vvv['preparation_time'];
                            $totalHour += $hour;

                            $htmlDateActive = '';
                            if (!empty($checkFinish)) {
                                if (empty($checkFinishNew)) {
                                    $htmlDateActive = '<div style="font-size: 10px;border-top:1px solid white">HT: ' . _dnew($vvv['date_active']) . '</div>';
                                }
                            }
                            $check = 1;
                            $styleHr = 'margin-bottom:5px;';
                            if ((count($checkItemStage)-1) == $kkk ){
                                $styleHr = '';
                            }
                            $styleFinish = '';
                            if (!empty($checkFinish)){
                                $styleFinish = 'background-color: rgb(44 133 44); color: white;';
                            }
                            $styleFinishNew = '';
                            if (!empty($checkFinishNew)){
                                $styleFinishNew = 'background-color: #b4bd2a; color: white;';
                            }

                            $styleTd = '';
                            if(empty($checkFinish) && empty($checkFinishNew)){
                                $styleTd = 'border:1px solid #787474;';
                            }

                            $dataStage .= '<div style="padding:5px;'.$styleFinish.$styleFinishNew.$styleHr.$styleTd.'"><div><span style="color: ' . (!empty($checkFinish) || !empty($checkFinishNew) ? 'white' : 'green') . '">' . $vvv['name_machines'] . '</span> - ' . '<span  style="color: ' . (!empty($checkFinish) || !empty($checkFinishNew) ? 'white' : 'red') . '">' . $hour . ' (giờ)</span></div>
                            <div style="font-size: 10px">BĐ: ' . _dnew($vvv['date_start']) . '<br>' . (!empty($vvv['date_end']) ? 'KT: ' . _dnew($vvv['date_end']) : '') . ' </div>' . $htmlDateActive.'<div><a style="color: ' . (!empty($checkFinish) || !empty($checkFinishNew) ? 'white' : 'black') . '" target="_blank" href="' . base_url('admin/manufactures/detail_productions/' . $vvv['id']) . '">'.$vvv['reference_no'].'</a></div></div>';
                        }
                    }
                    $htmlFinish = '';
                    $htmlFinishNew = '';

                    $htmlCheckEmpty = '';
                    if (empty($check)) {
                        $htmlCheckEmpty = '<input type="hidden" data-key="' . $i . '" class="check_empty" value="1">';
                        if ($arrCheck['check_' . $vv['id_new']] == 0) {
                            $arrCheck['check_' . $vv['id_new']] = $i;
                        }
                    } else {
                        if ($arrCheck['check_' . $vv['id_new']] != -1) {
                            $arrCheck['check_' . $vv['id_new']] = -1;
                        }
                    }

                    $row[$i] = '<div class="text-left" style="min-width: 110px;">
                            ' . $workflow . '
                            ' . $dataStage . '
                            ' . $htmlFinish . '
                            ' . $htmlFinishNew . '
                            ' . $htmlCheckEmpty . '
                    </div>';
                    $i++;
                }
            }
            $attr = [
                'data-hour' => round($totalHour, 3)
            ];
            $row['DT_RowClass'] = 'tr-new';
            $row['DT_RowAttr'] = $attr;
            $output['aaData'][] = $row;
            $stt++;

        }
        $output['arrCheck'] = $arrCheck;
        echo json_encode($output);
    }

    public function getProductionSmoothingVs1(){
        $status_table = $this->input->post('status_table');
        $productions_orders_search = $this->input->post('productions_orders_search');
        $stage_search = $this->input->post('stage_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $machines_id = $status_table;

        $tb_production_order_item = "(
            SELECT 
                tbl_productions_orders_items.id as productions_orders_item_id,
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tbl_productions_orders_details.id as productions_orders_detail_id
            FROM tbl_productions_orders_details
            INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
            GROUP BY tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $tbProcessStageActive = "(
            SELECT 
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                tbl_productions_orders_items_stages.productions_orders_id as productions_orders_id,
                tbl_productions_orders_items_stages.active as active
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.final_stage = 1
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id,tbl_productions_orders_items_stages.productions_orders_id
            ORDER BY tbl_productions_orders_items_stages.productions_orders_id asc,tbl_productions_orders_items_stages.active desc
        ) tb_process_stage_active";

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.reference_no as reference_no',
            'tbl_productions_orders.date as date_created',
            '" " as detail'
        ];
        $resultNew = [];
        $name = 'all';
        $aColumns[] = "'$name'";
        $resultNew[] = [
            'id_new' => 'all',
            'name' => 'all'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_productions_orders';
        $filter = [];
        $where = [];
        $join = [
            "INNER JOIN $tb_production_order_item ON tb_production_order_item.productions_orders_id = tbl_productions_orders.id",
        ];

        array_push($where,
            'AND EXISTS (
                    SELECT tb_process_stage_active.productions_orders_id
                    FROM '.$tbProcessStageActive.'
                    WHERE tb_process_stage_active.productions_orders_id = tbl_productions_orders.id
                    AND tb_process_stage_active.active = 0
                )'
        );

        if (!empty($productions_orders_search)) {
            array_push($where,
                'AND ( tbl_productions_orders.id = ' . $productions_orders_search . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_production_order_item.productions_orders_item_id as productions_orders_item_id',
            'tb_production_order_item.productions_orders_detail_id as productions_orders_detail_id',
        ], '', [], []);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 0;
        $arrCheck = [];
        if (!empty($resultNew)) {
            foreach ($resultNew as $kk => $vv) {
                $arrCheck['check_' . $vv['id_new']] = 0;
            }
        }
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $row[0] = '<div class="text-center">' . (++$key) . '</div>';
            $row[1] = '<div style="min-width: 150px"><a target="_blank" href="' . base_url('admin/manufactures/detail_productions_orders/' . $aRow['id']) . '">' . $aRow['reference_no'] . '</a>
            <br><span style="color: red;font-size: 11px;font-style: italic" class="total_hour_new"></span></div>';
            $row[2] = '<div style="min-width: 100px" class="text-center">' . _dhau($aRow['date_created']) . '</div>';

            $this->db->select('
            tbl_orders.reference_no as reference_no_order,
            tbl_business_plan.reference_no as reference_no_plan,
            tbl_productions_orders_items.quantity as quantity
            ');
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
            $this->db->join('tbl_orders','tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"','left');
            $this->db->join('tbl_business_plan','tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"','left');
            $this->db->where('tbl_productions_orders_details.productions_orders_id',$aRow['id']);
            $productionDetail = $this->db->get()->result_array();
            $htmlProductionDetail = '';
            if (!empty($productionDetail)){
                foreach ($productionDetail as $kk => $vv){
                    $htmlProductionDetail .= '<div>- '.(!empty($vv['reference_no_order']) ? $vv['reference_no_order'] : $vv['reference_no_plan']).' ('.formatNumber($vv['quantity']).')</div>';
                }
            }

            $row[3] = '<div style="min-width: 150px">'.$htmlProductionDetail.'</div>';
            $i = 4;
            $totalHour = 0;
            $checkFinish = false;
            $checkFinishNew = false;
            if (!empty($resultNew)) {
                foreach ($resultNew as $kk => $vv) {
                    $workflow = '';
                    $dataStage = '';
                    $check = 0;
                    if ($vv['id_new'] == 'all') {
                        $check = 1;
                        //process
                        $productions_orders_items_id = $aRow['productions_orders_item_id'];
                        $this->db->select('
                            tbl_productions_orders_items_stages.id as id,
                            tbl_productions_orders_items_stages.active as active,
                            tbl_productions_orders_items_stages.staff_active as staff_active,
                            tbl_productions_orders_items_stages.date_active as date_active,
                            tbl_stages.id as stage_id,
                            tbl_stages.name as stage_name,
                            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                            IF (tblstaff.profile_image IS NOT NULL, CONCAT("' . base_url('uploads/staff_profile_images/') . '", tblstaff.staffid, "/small__", tblstaff.profile_image), null) as staff_image,
                            tbl_productions_orders_items_stages.final_stage as final_stage,
                            tbl_productions_orders_items_stages.machines_id as machines_id,
                            tbl_productions_orders_items_stages.date_start as date_start,
                            tbl_productions_orders_items_stages.date_end as date_end,
                            tbl_machines.name as name_machines,
                            tbl_machines.quota_productivity as quota_productivity,
                            tbl_machines.preparation_time as preparation_time
                        ', false);
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                        $this->db->join('tblstaff',
                            'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
                        $this->db->join('tbl_machines',
                            'tbl_machines.id = tbl_productions_orders_items_stages.machines_id', 'left');
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id',
                            $productions_orders_items_id);
                        $this->db->where('tbl_stages.id !=', STAGES_MATERIAL);
                        $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
                        $process = $this->db->get()->result_array();
                        $workflow = '';
                        $li = '';
                        $isActive = 0;
                        if (!empty($process)) {
                            foreach ($process as $kkk => $vvv) {
                                $checkEdit = false;

                                $htmlMachines = '<div></div>';
                                if (!empty($vvv['machines_id'])) {
                                    $htmlMachines = '<div style="color: #d94a06">' . $vvv['name_machines'] . '</div>';
                                }
                                $li .= '<li ' . ($vvv['machines_id'] ? 'class="active"' : '') . '>' . $vvv['stage_name'] .
                                    $htmlMachines .
                                    (empty($checkEdit) ? '<a data-tnh="modal" style="color:#03a9f4;cursor:pointer" class="tnh-modal" href="' . base_url('admin/production_smoothing/add_machines/' . $vvv['id'].'/'.$aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i></a>' : '')
                                    . '</li>';

                                //new

                                $this->db->select('tbl_productions_orders_items_stages.*');
                                $this->db->from('tbl_productions_orders_items_stages');
                                $this->db->where('productions_orders_id',$aRow['id']);
                                $this->db->where('stage_id',$vvv['stage_id']);
                                $dtStageProduction = $this->db->get()->result_array();
                                if (!empty($dtStageProduction)){
                                    foreach ($dtStageProduction as $kkkk => $vvvv){
                                        $datetime1 = new Carbon($vvvv['date_start']);
                                        if (!empty($vvvv['date_end'])) {
                                            $datetime2 = new Carbon($vvvv['date_end']);
                                            $minute = $datetime2->diffInMinutes($datetime1);
                                            $hour = round($minute / 60, 2);
                                        } else {
                                            $hour = 0;
                                        }

                                        $hour += $vvv['preparation_time'];
                                        $totalHour += $hour;
                                    }
                                }
                            }
                        }
                        $workflow .= '<div style="display: table; justify-content: center;">
                            <ul class="progressbar" style="display: flex;">
                            ' . $li . '
                            </ul>
                        </div>';

                    }
                    $htmlFinish = '';
                    $htmlFinishNew = '';
                    if (!empty($checkFinish)) {
                        $htmlFinish = '<input type="hidden" class="check_finish" value="' . $checkFinish . '">';
                    }
                    if (!empty($checkFinishNew)) {
                        $htmlFinishNew = '<input type="hidden" class="check_finish_new" value="' . $checkFinishNew . '">';
                    }
                    $htmlCheckEmpty = '';
                    if (empty($check)) {
                        $htmlCheckEmpty = '<input type="hidden" data-key="' . $i . '" class="check_empty" value="1">';
                        if ($arrCheck['check_' . $vv['id_new']] == 0) {
                            $arrCheck['check_' . $vv['id_new']] = $i;
                        }
                    } else {
                        if ($arrCheck['check_' . $vv['id_new']] != -1) {
                            $arrCheck['check_' . $vv['id_new']] = -1;
                        }
                    }

                    $row[$i] = '<div class="text-left" style="min-width: 110px;">
                            ' . $workflow . '
                            ' . $dataStage . '
                            ' . $htmlFinish . '
                            ' . $htmlFinishNew . '
                            ' . $htmlCheckEmpty . '
                    </div>';
                    $i++;
                }
            }
            $attr = [
                'data-hour' => round($totalHour, 3)
            ];
            $row['DT_RowClass'] = 'tr-new';
            $row['DT_RowAttr'] = $attr;
            $output['aaData'][] = $row;
            $stt++;

        }
        $output['arrCheck'] = $arrCheck;
        echo json_encode($output);
    }

    public function add_machines($id = 0,$production_order_id = 0)
    {

        $success = false;
        if ($this->input->post()) {
            if (empty($id)) {
                $data['result'] = 0;
                $data['message'] = lang('Không tồn tại id sửa phiếu');
                echo json_encode($data);
                die();
            }
            $data = [];
            $dataPost = $this->input->post();

            $stageProduction = get_table_where('tbl_productions_orders_items_stages',['id' => $id],'','row_array');
            $this->db->select('tbl_productions_orders_items_stages.*');
            $this->db->from('tbl_productions_orders_items_stages');
            $this->db->where('productions_orders_id',$production_order_id);
            $this->db->where('stage_id',$stageProduction['stage_id']);
            $dtStageProduction = $this->db->get()->result_array();

            $successNew = false;
            if (!empty($dtStageProduction)){
                foreach ($dtStageProduction as $key => $value){
                    $option = [
                        'machines_id' => !empty($dataPost['machines_id']) ? $dataPost['machines_id'] : 0,
                        'date_start' => !empty($dataPost['date_start']) ? to_sql_date($dataPost['date_start'], true) : null,
                        'date_machines' => date('Y-m-d H:i:s'),
                        'user_machines' => get_staff_user_id(),
                    ];
                    $this->db->where('id', $value['id']);
                    $success = $this->db->update('tbl_productions_orders_items_stages', $option);
                    if ($success){
                        $successNew = true;
                        $this->db->select('
                            tbl_productions_orders_items_stages.date_start as date_start,
                            tbl_products.quantity_child_sheet as quantity_child_sheet,
                            tbl_machines.quota_productivity as quota_productivity,
                            tbl_productions_orders_items.quantity as quantity
                        ');
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_productions_orders_items',
                            'tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id');
                        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_productions_orders_items_stages.machines_id');
                        $this->db->where('tbl_productions_orders_items_stages.id', $value['id']);
                        $checkExists = $this->db->get()->row_array();
                        if (!empty($checkExists)) {
                            $quantity_child_sheet = !empty($checkExists['quantity_child_sheet']) ? $checkExists['quantity_child_sheet'] : 0;
                            $quota_productivity = !empty($checkExists['quota_productivity']) ? $checkExists['quota_productivity'] : 0;
                            $quantity = $checkExists['quantity'];

                            if ($quantity_child_sheet != 0 && $quota_productivity != 0) {
                                $totalHourNew = ($quantity / $quantity_child_sheet) / $quota_productivity;
                            } else {
                                $totalHourNew = 0;
                            }

                            $datetime1 = new Carbon($checkExists['date_start']);
                            $totalHourNew = ceil($totalHourNew * 60);
                            if ($totalHourNew > 0) {
                                $hour = to_sql_date($datetime1->addMinute($totalHourNew), true);
                            } else {
                                $hour = null;
                            }

                        } else {
                            $hour = null;
                        }

                        $this->db->where('id', $value['id']);
                        $this->db->update('tbl_productions_orders_items_stages', ['date_end' => $hour]);
                    }
                }
            }

            if ($successNew) {
                $data['result'] = 1;
                $data['message'] = lang('Thêm thành công');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Thêm thất bại');
            }
            echo json_encode($data);
            die;
        } else {

            $check = get_table_where('tbl_productions_orders_items_stages', ['id' => $id], '', 'row_array');

            $this->db->select('tbl_machines.*');
            $this->db->from('tbl_machines');
            $this->db->where('EXISTS (
                SELECT tbl_machines_stage.machines_id
                FROM tbl_machines_stage
                JOIN tbl_category_stages ON tbl_category_stages.id = tbl_machines_stage.category_stage_id
                JOIN tbl_stages ON tbl_stages.category_stages = tbl_category_stages.id
                WHERE tbl_machines_stage.machines_id = tbl_machines.id
                AND tbl_stages.id = ' . $check['stage_id'] . '
            )');
            $data['machines'] = $this->db->get()->result_array();
            $data['machines_id'] = $check['machines_id'];
            $data['date_start'] = $check['date_start'];
            $data['id'] = $id;
            $data['production_order_id'] = $production_order_id;
            $this->load->view('admin/production_smoothing/add_machines', $data);
        }
    }

    public function getListProductionsDetail()
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $this->db->simple_query('SET SESSION group_concat_max_len=150000000');

        $this->db->select("tbl_productions_orders_details.id as id, tbl_productions_orders_details.reference_no as text",
            false);
        $this->db->from('tbl_productions_orders_details');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_details.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $data['results'] = $this->db->get()->result_array();
        echo json_encode($data);
    }

    public function getListProductions()
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $this->db->simple_query('SET SESSION group_concat_max_len=150000000');

        $this->db->select("tbl_productions_orders.id as id, tbl_productions_orders.reference_no as text",
            false);
        $this->db->from('tbl_productions_orders');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $data['results'] = $this->db->get()->result_array();
        echo json_encode($data);
    }

}