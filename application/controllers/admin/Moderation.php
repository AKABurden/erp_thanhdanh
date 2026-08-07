<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');

        $this->task_status = $this->tasks_model->get_statuses();
        $this->tasksPriorities = get_tasks_priorities();
    }

    public function moderation_task()
    {
        $preViewModerationTask = true;
        $preViewOwnModerationTask = true;
        if (!$preViewModerationTask && !$preViewOwnModerationTask) {
            access_denied();
        }
        $data['room'] = $this->db->get_where('tbl_room')->result_array();

        $data['title'] = _l('dt_moderation_task');
        $this->load->view('admin/moderation/moderation_task', $data);
    }
    public function getModerationTask()
    {
        $preViewModerationTask = true;
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbltasks.id as id',
            'tblcategory_tasks.code as task_code',
            'tbltasks.startdate as date',
            '(
                SELECT 
                GROUP_CONCAT(tbl_room.name) 
                FROM tbltask_department 
                JOIN tbl_room ON tbl_room.id = tbltask_department.department_id
                WHERE tbltask_department.task_id = tbltasks.id
            ) as name_departments_tasks',
            'tbltasks.description as detail_task',
            '1 as regulations',
            'tbltasks.startdate as date_start',
            'tbltasks.datefinished as date_finish',
            'tbltasks.duedate as date_end',
            'tbltasks.addedfrom as addedfrom',
            get_sql_select_task_asignees_full_names(true) . ' as assignees',
            'tbltasks.priority as priority',
            'tbltasks.status as status',
            '(
                SELECT FLOOR(SUM(TIMESTAMPDIFF(SECOND, DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.start_time), "%Y-%m-%d %H:%i:%s"), DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.end_time), "%Y-%m-%d %H:%i:%s")))/60)
                FROM tbltaskstimers 
                WHERE tbltaskstimers.task_id = tbltasks.id
            ) as _minute',
            'tblcategory_tasks.time as time'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbltasks';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbltasks.addedfrom',

        ];
        $join[] = 'LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tbltasks.category_tasks';
        $join[] = 'LEFT JOIN tblinternal_proposal ON tblinternal_proposal.id = tbltasks.rel_id AND tbltasks.rel_type = "internal_proposal"';
        $join[] = 'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tblinternal_proposal.category_recommended_id';
        $join[] = 'LEFT JOIN tbl_category_recommended new_tasks ON new_tasks.id = tbltasks.category_recommended_id';
        // if (!$preViewModerationTask) {
        //     array_push($where, 'AND (tbl_suggest_task.created_by = '.get_staff_user_id().' OR tbl_suggest_task.staff_id = '.get_staff_user_id().' 
        //     OR EXISTS (
        //         SELECT 1
        //         FROM tbl_suggest_task_staff
        //         WHERE tbl_suggest_task_staff.suggest_task_id = tbl_suggest_task.id
        //         AND tbl_suggest_task_staff.staff_id = '.get_staff_user_id().'
        //     )
        //     )');
        // }
        if ($this->input->post('room_task')) {
            $where[] = 'AND EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN (' . implode(',', $this->input->post('room_task')) . '))';
            $whereTotal[] = 'AND EXISTS (SELECT 1 FROM tbltask_department WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id IN (' . implode(',', $this->input->post('room_task')) . '))';
        }
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbltasks.startdate >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbltasks.startdate <= '" . $end_date_search . "'");
        }

        if (has_permission('tasks', '', 'view_own') && !is_admin()) {
            $where[] = 'AND EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid IN (' . get_staff_user_id() . '))';
            $whereTotal[] = 'AND EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid IN (' . get_staff_user_id() . '))';
        }
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_category_recommended.name_table as name_table',
            'tbl_category_recommended.name as name_category_recommended',
            'tblinternal_proposal.suggest_id as suggest_id',
            get_sql_select_task_assignees_ids(true) . ' as assignees_ids',
            'tbl_category_recommended.type_kpi as type_kpi',
            'tbltasks.category_tasks',
            'new_tasks.name_table as name_table_task',
            'tbltasks.suggest_id as suggest_id_task',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as full_name,tbltasks.name as task_name'
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $j = 0;

        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" class="display-block main-tasks-table-href-name" onclick="init_task_modal(' . $aRow['id'] . '); return false;">#' . $aRow['id'] . '</a>';
            $row[] = '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" class="display-block main-tasks-table-href-name" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['task_code'] . '</a>';
            $row[] = '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" class="display-block main-tasks-table-href-name" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['task_name'] . '</a>';
            // $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_task/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['task_code'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $code_Suggest = '';
            if (!empty($aRow['name_table'])) {
                $dtSuggest = get_table_where($aRow['name_table'], ['id' => $aRow['suggest_id']], '', 'row_array');
                if (!empty($dtSuggest)) {
                    if (!empty($dtSuggest['reference_no'])) {
                        $code_Suggest = $dtSuggest['reference_no'];
                    }
                    if (!empty($dtSuggest['code'])) {
                        $code_Suggest = $dtSuggest['code'];
                    }
                    $link = '';
                    $name_table = explode('tbl_', $aRow['name_table']);
                    if (count($name_table) > 1) {
                        $link = $name_table[1];
                    } else {
                        $name_table_v2 = explode('tbl', $aRow['name_table']);
                        if (count($name_table_v2) > 1) {
                            $link = $name_table_v2[1];
                        }
                    }
                    $html = '</div><a class="tnh-modal" href="' . base_url('admin/' . $link . '/view/' . $dtSuggest['id']) . '">' . $code_Suggest . '</a>';
                    if ($aRow['type_kpi'] == 1) {
                        $html = '<div><a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
                    }
                    // $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
                    $code_Suggest = $html;
                }
            }
            if (!empty($aRow['name_table_task'])) {
                $dtSuggest = get_table_where($aRow['name_table_task'], ['id' => $aRow['suggest_id_task']], '', 'row_array');
                if (!empty($dtSuggest)) {
                    if (!empty($dtSuggest['reference_no'])) {
                        $code_Suggest = $dtSuggest['reference_no'];
                    }
                    if (!empty($dtSuggest['code'])) {
                        $code_Suggest = $dtSuggest['code'];
                    }
                    $link = '';
                    $name_table = explode('tbl_', $aRow['name_table_task']);
                    if (count($name_table) > 1) {
                        $link = $name_table[1];
                    } else {
                        $name_table_v2 = explode('tbl', $aRow['name_table_task']);
                        if (count($name_table_v2) > 1) {
                            $link = $name_table_v2[1];
                        }
                    }
                    $html = '</div><a class="tnh-modal" href="' . base_url('admin/' . $link . '/view/' . $dtSuggest['id']) . '">' . $code_Suggest . '</a>';
                    if ($aRow['type_kpi'] == 1) {
                        $html = '<div><a class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
                    }
                    // $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
                    $code_Suggest = $html;
                }
            }
        
            $row[] = '<div class="text-center">' . $code_Suggest . '</div>';

            $row[] = '<div class="text-left">' . ($aRow['name_departments_tasks']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['detail_task']) . '</div>';

            $row[] = '<div class="text-left"></div>';
            $row[] = '<div class="text-left"></div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date_start']) ? _dt($aRow['date_start']) : '') . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date_finish']) ? _dt($aRow['date_finish']) : '') . '</div>';
            $row[] = '<div class="text-left">' . (!empty($aRow['date_end']) ? _dt($aRow['date_end']) : '') . '</div>';
            $fullname_CREATE = get_staff_full_name($aRow['addedfrom']);
            $profile_CREATE = '<p class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['addedfrom']) . '">' . staff_profile_image($aRow['addedfrom'], [
                'staff-profile-image-small',
            ]) . '</a><div class="hide">' . $fullname_CREATE . '</div></p>';
            $row[] = '<div class="text-left">' . $profile_CREATE . '</div>';
            $row[] = format_members_by_ids_and_names($aRow['assignees_ids'], $aRow['assignees']);

            $priority = '';
            foreach ($this->tasksPriorities as $kk => $vv) {
                if ($aRow['priority'] == $vv['id']) {
                    $priority = $vv['name'];
                }
            }
            $htmlStatus = '';
            foreach ($this->task_status as $kk => $vv) {
                if ($aRow['status'] == $vv['id']) {
                    $htmlStatus = $vv['name'];
                }
            }

            $row[] = '<div class="text-left">' . $priority . '</div>';
            $row[] = '<div class="text-left">' . $htmlStatus . '</div>';
            $resultTime = '<span class="label label-default">Chưa tính giờ</span>';
            if (empty($aRow['category_tasks'])) {
                $resultTime = '<span class="label label-warning">Chưa chọn mã công việc</span>';
            } else if (!empty($aRow['_minute'])) {
                if ($aRow['_minute'] > $aRow['time']) {
                    $resultTime = '<span class="label label-danger">Chưa đạt</span>';
                } elseif ($aRow['_minute'] == $aRow['time']) {
                    $resultTime = '<span class="label label-success">Đạt</span>';
                } else {
                    $resultTime = '<span class="label label-info">Vượt KPI</span>';
                }
            } else {
                $resultTime = '<span class="label label-default">Chưa tính giờ</span>';
            }
            if (!empty($aRow['_minute'])) {
                $resultTime .= '<br/><span class="label label-primary lableMinus"> Tổng TG thực hiện 	' . number_format_data($aRow['_minute']) . ' (Phút)' . '</span>';
            }
            $row[] = '<div class="text-center">' . $resultTime . '</div>';
            $row[] = '<div class="text-left">' . $aRow['time'] . '</div>';
            // foreach (getListColumTable() as $kk => $vv) {
            //     $_data = getDataModeration($aRow['id'], $vv['id'], $sTable);
            //     $row[] = '<div class="text-center">' . $_data . '</div>';
            // }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    // public function getModerationTask(){
    //     $preViewModerationTask = true;
    //     $end_date_search = $this->input->post('end_date_search');
    //     $start_date_search = $this->input->post('start_date_search');

    //     $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
    //     $tb_tamp = "(
    //         SELECT
    //             tbl_suggest_task_staff.suggest_task_id as suggest_task_id,
    //             GROUP_CONCAT(tblstaff.staffid) as staff_task,
    //             GROUP_CONCAT(CONCAT(tblstaff.firstname,' ',tblstaff.lastname)) as staff_name_task
    //         FROM tbl_suggest_task_staff
    //         JOIN tblstaff ON tblstaff.staffid = tbl_suggest_task_staff.staff_id
    //         GROUP BY tbl_suggest_task_staff.suggest_task_id
    //     ) tb_tamp";
    //     $aColumns = [
    //         'tbl_suggest_task.id as id',
    //         'tbl_suggest_task.reference_no as reference_no',
    //         'tbl_suggest_task.date as date',
    //         'tblroles.code_role as code_role',
    //         'tbl_suggest_task.detail_task as detail_task',
    //         'tbl_suggest_task.regulations as regulations',
    //         'tbl_suggest_task.date_start as date_start',
    //         'tbl_suggest_task.date_finish as date_finish',
    //         'tbl_suggest_task.date_end as date_end',
    //         'tbl_suggest_task.staff_id as staff_id',
    //         'tbl_suggest_task.priority as priority',
    //         'tbl_suggest_task.status as status',
    //         'tbl_result.name as name_result',
    //     ];
    //     $sIndexColumn = 'id';
    //     $sTable = 'tbl_suggest_task';
    //     $where = [];
    //     $filter = [];

    //     $join = [
    //         'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_task.staff_id',
    //         'INNER JOIN tblroles ON tblroles.roleid = tbl_suggest_task.role_id',
    //         'LEFT JOIN '.$tb_tamp.' ON tb_tamp.suggest_task_id = tbl_suggest_task.id',
    //         'LEFT JOIN tbl_result ON tbl_result.id = tbl_suggest_task.result_id'
    //     ];

    //     if (!$preViewModerationTask) {
    //         array_push($where, 'AND (tbl_suggest_task.created_by = '.get_staff_user_id().' OR tbl_suggest_task.staff_id = '.get_staff_user_id().' 
    //         OR EXISTS (
    //             SELECT 1
    //             FROM tbl_suggest_task_staff
    //             WHERE tbl_suggest_task_staff.suggest_task_id = tbl_suggest_task.id
    //             AND tbl_suggest_task_staff.staff_id = '.get_staff_user_id().'
    //         )
    //         )');
    //     }

    //     if (!empty($start_date_search)) {
    //         $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
    //         array_push($where, "AND tbl_suggest_task.date >= '" . $start_date_search . "'");
    //     }

    //     if (!empty($end_date_search)) {
    //         $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
    //         array_push($where, "AND tbl_suggest_task.date <= '" . $end_date_search . "'");
    //     }


    //     $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    //         'tb_tamp.staff_task',
    //         'tb_tamp.staff_name_task',
    //         'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as full_name'
    //     ], '', []);

    //     $output = $result['output'];
    //     $rResult = $result['rResult'];
    //     $j = 0;
    //     foreach ($rResult as $key => $aRow) {
    //         $row = array();
    //         $row[] = '<div class="text-center">' . (++$key) . '</div>';
    //         $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_task/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
    //         $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
    //         $row[] = '<div class="text-left">' . ($aRow['code_role']) . '</div>';
    //         $row[] = '<div class="text-left">' . ($aRow['detail_task']) . '</div>';
    //         $row[] = '<div class="text-left"></div>';
    //         $row[] = '<div class="text-left">' . ($aRow['regulations']) . '</div>';
    //         $row[] = '<div class="text-left">' . (!empty($aRow['date_start']) ? _dhau($aRow['date_start']) : '') . '</div>';
    //         $row[] = '<div class="text-left">' . (!empty($aRow['date_finish']) ? _dhau($aRow['date_finish']) : '') . '</div>';
    //         $row[] = '<div class="text-left">' . (!empty($aRow['date_end']) ? _dhau($aRow['date_end']) : '') . '</div>';
    //         $row[] = '<div class="text-left">'.format_members_by_ids_and_names($aRow['staff_id'], $aRow['full_name']).'</div>';
    //         if (!empty($aRow['staff_task'])){
    //             $row[] = '<div class="text-left">'.format_members_by_ids_and_names($aRow['staff_task'], $aRow['staff_name_task']).'</div>';
    //         } else {
    //             $row[] =  '';
    //         }
    //         $priority = '';
    //         foreach ($this->tasksPriorities as $kk => $vv) {
    //             if ($aRow['priority'] == $vv['id']) {
    //                 $priority = $vv['name'];
    //             }
    //         }
    //         $htmlStatus = '';
    //         foreach ($this->task_status as $kk => $vv) {
    //             if ($aRow['status'] == $vv['id']) {
    //                 $htmlStatus = $vv['name'];
    //             }
    //         }

    //         $row[] = '<div class="text-left">'.$priority.'</div>';
    //         $row[] = '<div class="text-left">'.$htmlStatus.'</div>';
    //         $row[] = '<div class="text-left">'.$aRow['name_result'].'</div>';
    //         foreach (getListColumTable() as $kk => $vv) {
    //             $_data = getDataModeration($aRow['id'],$vv['id'],$sTable);
    //             $row[] = '<div class="text-center">'.$_data.'</div>';
    //         }
    //         $output['aaData'][] = $row;
    //     }
    //     echo json_encode($output);
    // }

    // public function exportExcelModerationTask()
    // {
    //     $preViewModerationTask = true;
    //     if ($this->input->post('export_excel')) {
    //         ini_set('memory_limit', '3500M');
    //         include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
    //         $this->load->library('PHPExcel');
    //         // print_arrays($this->input->post());
    //         $start_date_search = $this->input->post('start_date_search');
    //         $end_date_search = $this->input->post('end_date_search');
    //         $style_excel = style_excel();
    //         $cloumns_excel = cloumns_excel();

    //         $staff_id = get_staff_user_id();
    //         $tb_tamp = "(
    //             SELECT
    //                 tbl_suggest_task_staff.suggest_task_id as suggest_task_id,
    //                 GROUP_CONCAT(CONCAT(tblstaff.firstname,' ',tblstaff.lastname)) as staff_name_task
    //             FROM tbl_suggest_task_staff
    //             JOIN tblstaff ON tblstaff.staffid = tbl_suggest_task_staff.staff_id
    //             GROUP BY tbl_suggest_task_staff.suggest_task_id
    //         ) tb_tamp";

    //         $this->db->select('
    //            tbl_suggest_task.id as id,
    //            tbl_suggest_task.reference_no as reference_no,
    //            tbl_suggest_task.date as date,
    //            tblroles.code_role as code_role,
    //            tbl_suggest_task.detail_task as detail_task,
    //            tbl_suggest_task.regulations as regulations,
    //            tbl_suggest_task.date_start as date_start,
    //            tbl_suggest_task.date_finish as date_finish,
    //            tbl_suggest_task.date_end as date_end,
    //            tbl_suggest_task.staff_id as staff_id,
    //            COALESCE(tb_tamp.staff_name_task,"") as staff_name_task,
    //            tbl_suggest_task.priority as priority,
    //            tbl_suggest_task.status as status,
    //            tbl_result.name as name_result,
    //            (SELECT GROUP_CONCAT(tblproduction_report.name_report)
    //              FROM tblproduction_report
    //              WHERE tblproduction_report.object_id = tbl_suggest_task.id AND tblproduction_report.object_type = "suggest_task"
    //             ) as name_report
    //         ');
    //         $this->db->from('tbl_suggest_task');
    //         $this->db->join('tblroles', 'tblroles.roleid = tbl_suggest_task.role_id', 'inner');
    //         $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_task.result_id', 'left');
    //         $this->db->join($tb_tamp, 'tb_tamp.suggest_task_id = tbl_suggest_task.id', 'left');


    //         if (!$preViewModerationTask) {
    //             $this->db->where('(tbl_suggest_task.created_by = ' . $staff_id . ' OR tbl_suggest_task.staff_id = ' . $staff_id . ' 
    //                 OR EXISTS (
    //                     SELECT 1
    //                     FROM tbl_suggest_task_staff
    //                     WHERE tbl_suggest_task_staff.suggest_task_id = tbl_suggest_task.id
    //                     AND tbl_suggest_task_staff.staff_id = ' . $staff_id . '
    //                 )
    //             )');
    //         }

    //         if (!empty($start_date_search)) {
    //             $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
    //             $this->db->where("tbl_suggest_task.date >= '" . $start_date_search . "'");
    //         }

    //         if (!empty($end_date_search)) {
    //             $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
    //             $this->db->where("tbl_suggest_task.date <= '" . $end_date_search . "'");
    //         }

    //         $this->db->order_by('tbl_suggest_task.id desc');
    //         $dtData = $this->db->get()->result_array();


    //         $objPHPExcel = new PHPExcel();
    //         $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
    //         $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
    //         $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
    //         $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
    //         $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
    //         $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
    //         $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    //         $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    //         $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
    //             ->setWidth(20);
    //         $decimals_money = get_option('decimals_money');
    //         $decimals_number = get_option('decimals_number');
    //         $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
    //         $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
    //             "%0" . $decimals_number . "s",
    //             0
    //         ) : '');
    //         $company = get_option('invoice_company_name');
    //         $address = get_option('invoice_company_address');
    //         $company_vat = get_option('company_vat');
    //         $objPHPExcel->getDefaultStyle()->applyFromArray([
    //             'font' => array(
    //                 'name'  => 'Times New Roman'
    //             ),
    //         ]);
    //         $objPHPExcel->getActiveSheet()->setCellValue(
    //             'A1',
    //             ('KÊ HOẠCH ĐIỀU ĐỘ CÔNG VIỆC')
    //         )->getStyle("A1")->applyFromArray([
    //             'font' => array(
    //                 'bold' => true,
    //                 'size' => 18,
    //             ),
    //             'alignment' => array(
    //                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //                 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //             )
    //         ]);
    //         $objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
    //         $sttRow = 2;
    //         $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
    //         $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Số Phiếu Công Việc')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Tạo');
    //         $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Mã Vị Trí');
    //         $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Chi Tiết Công Việc')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Quy Trình')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Quy Định')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Ngày Bắt Đầu')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Ngày Hoàn Thành')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Hạn Chót')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Người Giao Việc')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Người Được Phân Công')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Mức Độ Ưu Tiên')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Trạng Thái')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
    //         $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Kết Quả')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
    //         $i = 15;
    //         foreach (getListColumTable() as $kk => $vv) {
    //             $objPHPExcel->getActiveSheet()->setCellValue(
    //                 $cloumns_excel[($i)] . $sttRow,
    //                 $vv['name']
    //             )->getStyle("$cloumns_excel[$i]$sttRow")->getAlignment()->setWrapText(true);
    //             if ($kk != (count(getListColumTable())) - 1) {
    //                 $i++;
    //             }
    //         }
    //         $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:$cloumns_excel[$i]$sttRow")->applyFromArray([
    //             'font' => array(
    //                 'size' => 12,
    //                 'name'  => 'Times New Roman'
    //             ),
    //             'borders' => array(
    //                 'allborders' => array(
    //                     'style' => PHPExcel_Style_Border::BORDER_THIN
    //                 )
    //             ),
    //             'alignment' => array(
    //                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //                 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    //             ),
    //             'fill' => array(
    //                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //                 'color' => array('rgb' => '92D050'),
    //             ),
    //         ]);
    //         $rowBegin = $sttRow;
    //         if (!empty($dtData)) {
    //             foreach ($dtData as $key => $value) {
    //                 $rowBegin++;
    //                 $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
    //                 $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
    //                 $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code_role'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['detail_task']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", '')->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['regulations'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", !empty($value['date_start']) ? _dhau($value['date_start']) : '')->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", !empty($value['date_finish']) ? _dhau($value['date_finish']) : '')->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", !empty($value['date_end']) ? _dhau($value['date_end']) : '')->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['staff_name_task'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
    //                 $priority = task_priority($value['priority']);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", !empty($priority) ? $priority : '')->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
    //                 $status = get_task_status_by_id($value['status']);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", !empty($status) ? $status['name'] : '')->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
    //                 $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['name_result'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
    //                 $colStt = 15;
    //                 foreach (getListColumTable() as $kk => $vv) {
    //                     $_data = getDataModeration($value['id'], $vv['id'], 'tbl_suggest_task', '', true);
    //                     $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $_data)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
    //                     if ($kk != (count(getListColumTable())) - 1) {
    //                         $colStt++;
    //                     }
    //                 }

    //                 $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:$cloumns_excel[$colStt]$rowBegin")->applyFromArray([
    //                     'borders' => array(
    //                         'allborders' => array(
    //                             'style' => PHPExcel_Style_Border::BORDER_THIN
    //                         )
    //                     )
    //                 ]);
    //                 $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
    //                     'alignment' => array(
    //                         'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //                     ),
    //                 ]);
    //             }
    //         }
    //         $filename = lang('ke_hoach_dieu_do_cong_viec') . '.xls';
    //         $objPHPExcel->getActiveSheet()->freezePane('A1');
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
    //         ob_start();
    //         header('Content-Type: application/vnd.ms-excel');
    //         header('Content-Disposition: attachment;filename="$filename"');
    //         header('Cache-Control: max-age=0');
    //         $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    //         $objWriter->save('php://output');
    //         $xlsData = ob_get_contents();
    //         ob_end_clean();
    //         $response = array(
    //             'result' => 1,
    //             'filename' => $filename,
    //             'message' => lang('success'),
    //             'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
    //         );
    //         die(json_encode($response));
    //     }
    // }
    public function exportExcelModerationTask()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');

            $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

            $this->db->select("
            tbltasks.id,
            tbltasks.name as task_name,
            tblcategory_tasks.code as task_code,
            tbltasks.startdate,
            tbltasks.datefinished,
            tbltasks.duedate,
            tbltasks.description,
            tbltasks.priority,
            tbltasks.status,
            tblcategory_tasks.time,
            (
                SELECT FLOOR(SUM(TIMESTAMPDIFF(SECOND, FROM_UNIXTIME(tbltaskstimers.start_time), FROM_UNIXTIME(tbltaskstimers.end_time))) / 60)
                FROM tbltaskstimers
                WHERE tbltaskstimers.task_id = tbltasks.id
            ) as _minute,
            (
                SELECT GROUP_CONCAT(tbl_room.name)
                FROM tbltask_department
                JOIN tbl_room ON tbl_room.id = tbltask_department.department_id
                WHERE tbltask_department.task_id = tbltasks.id
            ) as departments,
            tbltasks.addedfrom,
            tbltasks.category_tasks,
            tbl_category_recommended.type_kpi as type_kpi,
            tbl_category_recommended.name_table as name_table,
            tbl_category_recommended.name as name_category_recommended,
            tblinternal_proposal.suggest_id as suggest_id,
            new_tasks.name_table as name_table_task,
            tbltasks.suggest_id as suggest_id_task,
            " . get_sql_select_task_asignees_full_names(true) . " as assignees
        ");
            $this->db->from('tbltasks');
            $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tbltasks.category_tasks', 'left');
            $this->db->join('tblinternal_proposal', 'tblinternal_proposal.id = tbltasks.rel_id AND tbltasks.rel_type = "internal_proposal"', 'left');
            $this->db->join('tbl_category_recommended', 'tbl_category_recommended.id = tblinternal_proposal.category_recommended_id', 'left');
            $this->db->join('tbl_category_recommended new_tasks', 'new_tasks.id = tbltasks.category_recommended_id', 'left');

            if ($this->input->post('room_task')) {
                $room_task = implode(',', $this->input->post('room_task'));
                $this->db->where("EXISTS (
                SELECT 1 FROM tbltask_department 
                WHERE tbltask_department.task_id = tbltasks.id 
                AND tbltask_department.department_id IN ($room_task)
            )");
            }
            if (!empty($start_date_search)) {
                $this->db->where('tbltasks.startdate >=', to_sql_date($start_date_search) . ' 00:00:00');
            }
            if (!empty($end_date_search)) {
                $this->db->where('tbltasks.startdate <=', to_sql_date($end_date_search) . ' 23:59:59');
            }

            if (has_permission('tasks', '', 'view_own') && !is_admin()) {
                $this->db->where('EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid = ' . get_staff_user_id() . ')');
            }

            $this->db->order_by('tbltasks.id', 'desc');
            $tasks = $this->db->get()->result_array();

            // Init Excel
            $excel = new PHPExcel();
            $excel->getDefaultStyle()->getFont()->setName('Times New Roman');
            $excel->getActiveSheet()->setTitle('Kế hoạch điều độ công việc');

            $excel->getActiveSheet()->setCellValue('A1', 'KẾ HOẠCH ĐIỀU ĐỘ CÔNG VIỆC');
            $excel->getActiveSheet()->mergeCells('A1:R1');
            $excel->getActiveSheet()->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 18],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER]
            ]);

            $header = [
                'STT',
                'Số Phiếu',
                'Mã Công Việc',
                'Tên Công Việc',
                'Ngày Tạo',
                'Phiếu yêu cầu',
                'Phòng Ban',
                'Chi Tiết Công Việc',
                'Quy Trình',
                'Quy Định',
                'Ngày Bắt Đầu',
                'Ngày Hoàn Thành',
                'Hạn Chót',
                'Người Giao Việc',
                'Người Được Phân Công',
                'Mức Độ Ưu Tiên',
                'Trạng Thái',
                'Kết Quả',
                'Định Mức Thời Gian'
            ];

            $col = 'A';
            foreach ($header as $title) {
                $excel->getActiveSheet()->setCellValue($col . '2', $title);
                $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }

            // Style tiêu đề
            $excel->getActiveSheet()->getStyle("A2:R2")->applyFromArray([
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'D9EAD3']],
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
            ]);

            $rowIndex = 3;
            foreach ($tasks as $i => $task) {
                $index = $rowIndex;
                $excel->getActiveSheet()->setCellValue("A$index", $i + 1);
                $excel->getActiveSheet()->setCellValue("B$index", '#' . $task['id']);
                $excel->getActiveSheet()->setCellValue("C$index", $task['task_code']);
                $excel->getActiveSheet()->setCellValue("D$index", $task['task_name']);
                $excel->getActiveSheet()->setCellValue("E$index", (!empty($task['startdate']) ? _dt($task['startdate']) : ''));
                $code_Suggest = '';
                if (!empty($task['name_table'])) {
                    $dtSuggest = get_table_where($task['name_table'], ['id' => $task['suggest_id']], '', 'row_array');
                    if (!empty($dtSuggest)) {
                        if (!empty($dtSuggest['reference_no'])) {
                            $code_Suggest = $dtSuggest['reference_no'];
                        }
                        if (!empty($dtSuggest['code'])) {
                            $code_Suggest = $dtSuggest['code'];
                        }
                        $link = '';
                        $name_table = explode('tbl_', $task['name_table']);
                        if (count($name_table) > 1) {
                            $link = $name_table[1];
                        } else {
                            $name_table_v2 = explode('tbl', $task['name_table']);
                            if (count($name_table_v2) > 1) {
                                $link = $name_table_v2[1];
                            }
                        }
                        $html = $code_Suggest;
                        if ($task['type_kpi'] == 1) {
                            $html = $dtSuggest['reference_no'];
                        }
                        $code_Suggest = $html;
                    }
                }
                if (!empty($task['name_table_task'])) {
                $dtSuggest = get_table_where($task['name_table_task'], ['id' => $task['suggest_id_task']], '', 'row_array');
                if (!empty($dtSuggest)) {
                    if (!empty($dtSuggest['reference_no'])) {
                        $code_Suggest = $dtSuggest['reference_no'];
                    }
                    if (!empty($dtSuggest['code'])) {
                        $code_Suggest = $dtSuggest['code'];
                    }
                    $link = '';
                    $name_table = explode('tbl_', $task['name_table_task']);
                    if (count($name_table) > 1) {
                        $link = $name_table[1];
                    } else {
                        $name_table_v2 = explode('tbl', $task['name_table_task']);
                        if (count($name_table_v2) > 1) {
                            $link = $name_table_v2[1];
                        }
                    }
                    $html = $code_Suggest;
                    if ($task['type_kpi'] == 1) {
                        $html =$dtSuggest['reference_no'];
                    }
                    // $htmlKpi = '<div style="border: 1px solid green;border-radius: 5px;padding: 5px;color: green"><div>Phiếu YCĐG KPI</div><a style="color: green" class="tnh-modal" href="' . base_url('admin/kpi/view_suggest_kpi/' . $dtSuggest['id']) . '">' . $dtSuggest['reference_no'] . '</a></div>';
                    $code_Suggest = $html;
                }
            }
        
                $row[] = !empty($code_Suggest) ? ('<div>' . $code_Suggest . '</div>') : '';
                $excel->getActiveSheet()->setCellValue("F$index", $code_Suggest);

                $excel->getActiveSheet()->setCellValue("G$index", $task['departments']);
                $excel->getActiveSheet()->setCellValue("H$index", $task['description']);
                $excel->getActiveSheet()->setCellValue("I$index", '');
                $excel->getActiveSheet()->setCellValue("J$index", '');
                $excel->getActiveSheet()->setCellValue("K$index", (!empty($task['startdate']) ? _dt($task['startdate']) : ''));
                $excel->getActiveSheet()->setCellValue("L$index", (!empty($task['datefinished']) ? _dt($task['datefinished']) : ''));
                $excel->getActiveSheet()->setCellValue("M$index", (!empty($task['duedate']) ? _dt($task['duedate']) : ''));
                $excel->getActiveSheet()->setCellValue("N$index", get_staff_full_name($task['addedfrom']));
                $excel->getActiveSheet()->setCellValue("O$index", $task['assignees']);

                $priority = '';
                foreach ($this->tasksPriorities as $item) {
                    if ($item['id'] == $task['priority']) {
                        $priority = $item['name'];
                    }
                }
                $excel->getActiveSheet()->setCellValue("P$index", $priority);

                $status = '';
                foreach ($this->task_status as $item) {
                    if ($item['id'] == $task['status']) {
                        $status = $item['name'];
                    }
                }
                $excel->getActiveSheet()->setCellValue("Q$index", $status);

                $result = 'Chưa tính giờ';
                if (!empty($task['category_tasks'])) {
                    if (!empty($task['_minute'])) {
                        if ($task['_minute'] > $task['time']) {
                            $result = 'Chưa đạt';
                        } elseif ($task['_minute'] == $task['time']) {
                            $result = 'Đạt';
                        } else {
                            $result = 'Vượt KPI';
                        }
                        $result .= " ({$task['_minute']} phút)";
                    } else {
                        $result = 'Chưa tính giờ';
                    }
                } else {
                    $result = 'Chưa chọn mã công việc';
                }

                $excel->getActiveSheet()->setCellValue("R$index", $result);
                $excel->getActiveSheet()->setCellValue("S$index", $task['time']);

                // Kẻ khung + căn lề đẹp
                $excel->getActiveSheet()->getStyle("A$index:S$index")->applyFromArray([
                    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
                    'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER]
                ]);
                $excel->getActiveSheet()->getStyle("A$index:C$index")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $excel->getActiveSheet()->getStyle("J$index:L$index")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $excel->getActiveSheet()->getStyle("O$index:P$index")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $excel->getActiveSheet()->getStyle("D$index:G$index")->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle("M$index:N$index")->getAlignment()->setWrapText(true);

                $rowIndex++;
            }

            $filename = 'KeHoachDieuDoCongViec_' . date('Ymd_His') . '.xls';
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=\"$filename\"");
            header('Cache-Control: max-age=0');
            $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $response = [
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            ];
            die(json_encode($response));
        }
    }

    public function moderation_educate()
    {
        $preViewModerationEducate = true;
        $preViewOwnModerationEducate = true;
        if (!$preViewModerationEducate && !$preViewOwnModerationEducate) {
            access_denied();
        }
        $data['title'] = _l('dt_moderation_educate');
        $this->load->view('admin/moderation/moderation_educate', $data);
    }

    public function getModerationEducate()
    {
        $preViewModerationEducate = true;
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_educate.id as id',
            'tbl_suggest_educate.reference_no as reference_no',
            'tbl_suggest_educate.date as date',
            'tblbranch.name as name_branch',
            'tbl_suggest_educate.staff_evaluate as staff_evaluate',
            'tbl_evaluate.code_evaluate as code_evaluate',
            'tbl_evaluate.name_evaluate as name_evaluate',
            'tbl_suggest_educate_item.position_educate as position_educate',
            'tbl_suggest_educate_item.detail as detail',
            'tbl_suggest_educate_item.quantity as quantity',
            'tbl_suggest_educate_item.staff_educate as staff_educate',
            'tbl_suggest_educate_item.unit_educate as unit_educate',
            'tbl_suggest_educate_item.cost_money as cost_money',
            'tbltaxes.name as name_tax',
            'tbl_suggest_educate_item.total_tax as total_tax',
            'tbl_suggest_educate_item.total as total',
            'tbl_result.name as name_result',
            'tbl_suggest_educate_item.standard as standard',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_educate';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblbranch ON tblbranch.id = tbl_suggest_educate.branch_id',
            'INNER JOIN tbl_suggest_educate_item ON tbl_suggest_educate_item.suggest_plan_educate_id = tbl_suggest_educate.id',
            'LEFT JOIN tbl_result ON tbl_result.id = tbl_suggest_educate_item.result_id',
            'LEFT JOIN tbl_evaluate ON tbl_evaluate.id = tbl_suggest_educate_item.evaluate_id',
            'LEFT JOIN tbltaxes ON tbltaxes.id = tbl_suggest_educate_item.tax_id',
        ];
        if (!$preViewModerationEducate) {
            array_push($where, 'AND (tbl_suggest_educate.created_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_educate.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_educate.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_educate.date_status',
            'tbl_suggest_educate.staff_status'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_educate/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['name_branch']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['staff_evaluate']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $aRow['code_evaluate'] . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $aRow['name_evaluate'] . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . $aRow['position_educate'] . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . $aRow['detail'] . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . formatNumber($aRow['quantity']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . get_staff_full_name($aRow['staff_educate']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . ($aRow['unit_educate']) . '</div>';
            $row[] = '<div class="text-right" style="width: 100px">' . formatMoney($aRow['cost_money']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . ($aRow['name_tax']) . '</div>';
            $row[] = '<div class="text-right" style="width: 100px">' . formatMoney($aRow['total_tax']) . '</div>';
            $row[] = '<div class="text-right" style="width: 100px">' . formatMoney($aRow['total']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['name_result']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['standard']) . '</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['id'], $vv['id'], $sTable);
                $row[] = '<div class="text-center">' . $_data . '</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcelModerationEducate()
    {
        $preViewModerationEducate = true;
        $columsExcel = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ'
        ];
        if ($this->input->post('export_excel')) {

            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_dt/phieu_ke_hoach_dieu_do_dao_tao.xlsx';
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
            $i = $highestColumnIndex - 1;

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
                $objPHPExcel->getActiveSheet()->setCellValue(
                    $columsExcel[($i)] . $highestRow,
                    $vv['name']
                )->getStyle("$columsExcel[$i]$highestRow")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $i++;
            }

            $row = 2;
            $staff_id = get_staff_user_id();
            $this->db->select('
                tbl_suggest_educate.id as id,
                tbl_suggest_educate.reference_no as reference_no,
                tbl_suggest_educate.date as date,
                tblbranch.name as name_branch,
                tbl_suggest_educate.staff_evaluate as staff_evaluate,
                tbl_evaluate.code_evaluate as code_evaluate,
                tbl_evaluate.name_evaluate as name_evaluate,
                tbl_suggest_educate_item.position_educate as position_educate,
                tbl_suggest_educate_item.detail as detail,
                tbl_suggest_educate_item.quantity as quantity,
                tbl_suggest_educate_item.staff_educate as staff_educate,
                tbl_suggest_educate_item.unit_educate as unit_educate,
                tbl_suggest_educate_item.cost_money as cost_money,
                tbltaxes.name as name_tax,
                tbl_suggest_educate_item.total_tax as total_tax,
                tbl_suggest_educate_item.total as total,
                tbl_result.name as name_result,
                tbl_suggest_educate_item.standard as standard,
            ');
            $this->db->from('tbl_suggest_educate');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_educate.branch_id', 'inner');
            $this->db->join('tbl_suggest_educate_item', 'tbl_suggest_educate_item.suggest_plan_educate_id = tbl_suggest_educate.id', 'inner');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_educate_item.result_id', 'left');
            $this->db->join('tbl_evaluate', 'tbl_evaluate.id = tbl_suggest_educate_item.evaluate_id', 'left');
            $this->db->join('tbltaxes', 'tbltaxes.id = tbl_suggest_educate_item.tax_id', 'left');
            if (!$preViewModerationEducate) {
                $this->db->where('(tbl_suggest_educate.created_by = ' . $staff_id . ')');
            }
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_educate.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_educate.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_suggest_educate.id desc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $colStt = 0;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $dem);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, _dt($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['name_branch']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, get_staff_full_name($value['staff_evaluate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['code_evaluate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['name_evaluate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['position_educate'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['detail']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['quantity'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, get_staff_full_name($value['staff_educate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['unit_educate'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['cost_money'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['name_tax'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['total_tax'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle("$columsExcel[$colStt]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total_tax']));
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['total'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle("$columsExcel[$colStt]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['total']));
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['name_result'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['standard'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                foreach (getListColumTable() as $kk => $vv) {
                    $_data = getDataModeration($value['id'], $vv['id'], 'tbl_suggest_educate', '', true);
                    $objPHPExcel->getActiveSheet()->setCellValue("$columsExcel[$colStt]$row", $_data)->getStyle("$columsExcel[$colStt]$row")->getAlignment()->setWrapText(true);
                    if ($kk != (count(getListColumTable())) - 1) {
                        $colStt++;
                    }
                }
                $objPHPExcel->getActiveSheet()->getStyle("$columsExcel[0]$row:$columsExcel[$colStt]$row")->applyFromArray([
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

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('phieu_ke_hoach_dieu_do_dao_tao') . '.xls';
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

    public function moderation_evaluate()
    {
        $preViewModerationEvaluate = true;
        $preViewOwnModerationEvaluate = true;
        if (!$preViewModerationEvaluate && !$preViewOwnModerationEvaluate) {
            access_denied();
        }
        $data['title'] = _l('dt_moderation_evaluate');
        $this->load->view('admin/moderation/moderation_evaluate', $data);
    }

    public function getModerationEvaluate()
    {
        $preViewModerationEvaluate = true;
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_evaluate.id as id',
            'tbl_suggest_evaluate.reference_no as reference_no',
            'tbl_suggest_evaluate.date as date',
            'tblbranch.name as name_branch',
            'tbl_suggest_evaluate.staff_evaluate as staff_evaluate',
            'tbl_category_evaluate.name as name_type_evaluate',
            'tbl_category_evaluate_detail.name as name_category_evaluate',
            'tbl_suggest_evaluate.object_type as object_type',
            'tbl_evaluate.code_evaluate as code_evaluate',
            'tbl_evaluate.name_evaluate as name_evaluate',
            'tbl_evaluate.content_evaluate as content_evaluate',
            'tbl_suggest_evaluate_item.content as content',
            'tbl_suggest_evaluate_item.actual_situation as actual_situation',
            'tbl_result.name as name_result',
            'tbl_suggest_evaluate_item.standard as standard',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_evaluate';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblbranch ON tblbranch.id = tbl_suggest_evaluate.branch_id',
            'INNER JOIN tbl_suggest_evaluate_item ON tbl_suggest_evaluate_item.suggest_plan_evaluate_id = tbl_suggest_evaluate.id',
            'INNER JOIN tbl_evaluate ON tbl_evaluate.id = tbl_suggest_evaluate_item.evaluate_id',
            'INNER JOIN tbl_category_evaluate ON tbl_category_evaluate.id = tbl_suggest_evaluate.type_evaluate_id',
            'LEFT JOIN tbl_category_evaluate_detail ON tbl_category_evaluate_detail.id = tbl_suggest_evaluate_item.category_evaluate_id',
            'LEFT JOIN tbl_result ON tbl_result.id = tbl_suggest_evaluate_item.result_id',
            'LEFT JOIN tblclients ON tblclients.userid = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="customer"',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="supplier"',
        ];
        if (!$preViewModerationEvaluate) {
            array_push($where, 'AND (tbl_suggest_educate.created_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_educate.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_educate.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblclients.company as name_client',
            'tblsuppliers.company as name_supplier',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_evaluate/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['name_branch']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['staff_evaluate']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $aRow['name_type_evaluate'] . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $aRow['name_category_evaluate'] . '</div>';
            $object = '';
            if ($aRow['object_type'] == 'customer') {
                $object = $aRow['name_client'];
            } elseif ($aRow['object_type'] == 'supplier') {
                $object = $aRow['name_supplier'];
            }
            $row[] = '<div class="text-left" style="width: 120px">' . $object . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . $aRow['code_evaluate'] . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['name_evaluate']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['content_evaluate']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['content']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['actual_situation']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['name_result']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['standard']) . '</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['id'], $vv['id'], $sTable);
                $row[] = '<div class="text-center">' . $_data . '</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcelModerationEvaluate()
    {
        $preViewModerationEvaluate = true;
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $type = $this->input->post('type');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $this->db->select('
                tbl_suggest_evaluate.id as id,
                tbl_suggest_evaluate.reference_no as reference_no,
                tbl_suggest_evaluate.date as date,
                tblbranch.name as name_branch,
                tbl_suggest_evaluate.staff_evaluate as staff_evaluate,
                tbl_category_evaluate.name as name_type_evaluate,
                tbl_category_evaluate_detail.name as name_category_evaluate,
                tbl_suggest_evaluate.object_type as object_type,
                tbl_evaluate.code_evaluate as code_evaluate,
                tbl_evaluate.name_evaluate as name_evaluate,
                tbl_evaluate.content_evaluate as content_evaluate,
                tbl_suggest_evaluate_item.content as content,
                tbl_suggest_evaluate_item.actual_situation as actual_situation,
                tbl_result.name as name_result,
                tbl_suggest_evaluate_item.standard as standard,
                tblclients.company as name_client,
                tblsuppliers.company as name_supplier
            ');
            $this->db->from('tbl_suggest_evaluate');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_evaluate.branch_id');
            $this->db->join('tbl_suggest_evaluate_item', 'tbl_suggest_evaluate_item.suggest_plan_evaluate_id = tbl_suggest_evaluate.id');
            $this->db->join('tbl_evaluate', 'tbl_evaluate.id = tbl_suggest_evaluate_item.evaluate_id');
            $this->db->join('tbl_category_evaluate', 'tbl_category_evaluate.id = tbl_suggest_evaluate.type_evaluate_id', 'inner');
            $this->db->join('tbl_category_evaluate_detail', 'tbl_category_evaluate_detail.id = tbl_suggest_evaluate_item.category_evaluate_id', 'left');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_evaluate_item.result_id', 'left');
            $this->db->join('tblclients', 'tblclients.userid = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="customer"', 'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="supplier"', 'left');

            if (!$preViewModerationEvaluate) {
                $this->db->where('(tbl_suggest_evaluate.created_by = ' . get_staff_user_id() . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_evaluate.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_evaluate.date <= '" . $end_date_search . "'");
            }

            if (!empty($type)) {
                $this->db->where('(tbl_suggest_evaluate.object_type = "' . $type . '")');
            }

            $this->db->order_by('tbl_suggest_evaluate.id desc');
            $dtData = $this->db->get()->result_array();


            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
                "%0" . $decimals_number . "s",
                0
            ) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $title = 'PHIẾU KẾ HOẠCH ĐIỀU ĐỘ ĐÁNH GIÁ';
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ($title)
            )->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Chi Nhánh');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Người Đánh Giá')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Loại Đánh Giá')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Nhóm Đánh Giá')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Đối Tượng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Mã Đánh Giá')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Tên Đánh Giá')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Nội Dung Đánh Giá')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Chi Tiết Đánh Giá')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Hiện Trạng Thực Tế')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Kết Quả')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Tiêu Chuẩn/ Quy Định')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $i = 15;
            foreach (getListColumTable() as $kk => $vv) {
                $objPHPExcel->getActiveSheet()->setCellValue(
                    $cloumns_excel[($i)] . $sttRow,
                    $vv['name']
                )->getStyle("$cloumns_excel[$i]$sttRow")->getAlignment()->setWrapText(true);
                if ($kk != (count(getListColumTable())) - 1) {
                    $i++;
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:$cloumns_excel[$i]$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['name_branch'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", get_staff_full_name($value['staff_evaluate']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['name_type_evaluate'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['name_category_evaluate'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $object = '';
                    if ($value['object_type'] == 'customer') {
                        $object = $value['name_client'];
                    } elseif ($value['object_type'] == 'supplier') {
                        $object = $value['name_supplier'];
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $object)->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", ($value['code_evaluate']))->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", ($value['name_evaluate']))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", ($value['content_evaluate']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", ($value['content']))->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", ($value['actual_situation']))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", ($value['name_result']))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['standard'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt = 15;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModeration($value['id'], $vv['id'], 'tbl_suggest_evaluate', $type, true);
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $_data)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        if ($kk != (count(getListColumTable())) - 1) {
                            $colStt++;
                        }
                    }

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:$cloumns_excel[$colStt]$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_ke_hoach_dieu_do_danh_gia') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
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

    public function moderation_check()
    {
        $preViewModerationCheck = true;
        $preViewOwnModerationCheck = true;
        if (!$preViewModerationCheck && !$preViewOwnModerationCheck) {
            access_denied();
        }
        $data['title'] = _l('dt_moderation_check');
        $this->load->view('admin/moderation/moderation_check', $data);
    }

    public function getModerationCheck()
    {
        $preViewModerationCheck = true;
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            '
            tbl_suggest_check.id as id,
            tbl_suggest_check.reference_no as reference_no,
            tbl_suggest_check.date as date,
            tbl_suggest_check_item.item_type as item_type,
            IF(tbl_suggest_check_item.item_type = "machines", tbl_machines.name, tbl_cleaning.name) as name_machines,
            IF(tbl_suggest_check_item.item_type = "machines", tbl_machines_5s.name, tbl_cleaning_detail.name) as name_machines_detail,
            tbl_suggest_check_item.regulation_5s as regulation_5s,
            IF(tbl_suggest_check_item.item_type = "machines", tbl_machines_5s.img, tbl_cleaning_detail.img) as img,
            tbl_suggest_check_item.staff_check as staff_check,
            tbl_result.name as name_result,
            tbl_suggest_check_item.evaluate as evaluate,
            tbl_suggest_check_item.staff_manager as staff_manager
            '
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_check';
        $where = [
            'AND tbl_suggest_check.ballot_type = 0'
        ];
        $filter = [];
        $join = [
            'INNER JOIN tblbranch ON tblbranch.id = tbl_suggest_check.branch_id',
            'INNER JOIN tbl_suggest_check_item ON tbl_suggest_check_item.suggest_check_id = tbl_suggest_check.id',
            'LEFT JOIN tbl_machines ON tbl_machines.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "machines"',
            'LEFT JOIN tbl_cleaning ON tbl_cleaning.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "cleaning"',
            'LEFT JOIN tbl_machines_5s ON tbl_machines_5s.id = tbl_suggest_check_item.machines_maintenance_id AND tbl_suggest_check_item.item_type = "machines"',
            'LEFT JOIN tbl_cleaning_detail ON tbl_cleaning_detail.id = tbl_suggest_check_item.machines_maintenance_id AND tbl_suggest_check_item.item_type = "cleaning"',
            'LEFT JOIN tbl_result ON tbl_result.id = tbl_suggest_check_item.result_id'
        ];
        if (!$preViewModerationCheck) {
            array_push($where, 'AND (tbl_suggest_check.created_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_check.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_check.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_check_item.current_status as current_status',
            'tbl_suggest_check_item.date_check as date_check',
            'tbl_suggest_check_item.item_id as item_id'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_check/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['item_type'] == 'machines' ? 'Thiết Bị' : ($aRow['item_type'] == 'cleaning' ? 'Khu Vực' : '')) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['name_machines_detail']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $aRow['name_machines'] . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $aRow['regulation_5s'] . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ViewHtmlImagesDt(!empty($aRow['img']) ? base_url($aRow['img']) : '') . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . (!empty($aRow['staff_check']) ? get_staff_full_name($aRow['staff_check']) : '') . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['name_result']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['evaluate']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . (!empty($aRow['staff_manager']) ? get_staff_full_name($aRow['staff_manager']) : '') . '</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['id'], $vv['id'], $sTable, 0);
                $row[] = '<div class="text-center">' . $_data . '</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcelModerationCheck()
    {
        $preViewModerationCheck = true;
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();
            $staff_id = get_staff_user_id();

            $this->db->select('
               tbl_suggest_check.id as id,
               tbl_suggest_check.reference_no as reference_no,
               tbl_suggest_check.date as date,
               IF(tbl_suggest_check_item.item_type = "machines", tbl_machines.name, tbl_cleaning.name) as name_machines,
               IF(tbl_suggest_check_item.item_type = "machines", tbl_machines_5s.img, tbl_cleaning_detail.img) as img,
               IF(tbl_suggest_check_item.item_type = "machines", tbl_machines_5s.name, tbl_cleaning_detail.name) as name_machines_detail,
               tbl_suggest_check_item.current_status as current_status,
               tbl_suggest_check_item.date_check as date_check,
               tbl_suggest_check_item.staff_check as staff_check,
               tbl_result.name as name_result,
               tbl_suggest_check_item.evaluate as evaluate,
               tbl_suggest_check_item.regulation_5s as regulation_5s,
               tbl_suggest_check_item.staff_manager as staff_manager,
               tbl_suggest_check_item.item_type as item_type,
               tbl_suggest_check_item.item_id as item_id,
            ');
            $this->db->from('tbl_suggest_check');
            $this->db->join('tbl_suggest_check_item', 'tbl_suggest_check_item.suggest_check_id = tbl_suggest_check.id', 'left');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_check.branch_id', 'inner');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "machines"', 'left');
            $this->db->join('tbl_cleaning', 'tbl_cleaning.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "cleaning"', 'left');
            $this->db->join('tbl_machines_5s', 'tbl_machines_5s.id = tbl_suggest_check_item.machines_maintenance_id AND tbl_suggest_check_item.item_type = "machines"', 'left');
            $this->db->join('tbl_cleaning_detail', 'tbl_cleaning_detail.id = tbl_suggest_check_item.machines_maintenance_id AND tbl_suggest_check_item.item_type = "cleaning"', 'left');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_check_item.result_id', 'left');

            if (!$preViewModerationCheck) {
                $this->db->group_start();
                $this->db->where('tbl_suggest_check.created_by', $staff_id);
                $this->db->or_where('EXISTS (
						SELECT 1
						FROM tbl_suggest_check_item
						WHERE tbl_suggest_check_item.suggest_check_id = tbl_suggest_check.id
						AND staff_check = ' . $staff_id . ' OR staff_manager = ' . $staff_id . '
						LIMIT 1
					)
					OR tbl_suggest_check.created_by =' . $staff_id, false, false);
                $this->db->group_end();
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_check.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_check.date <= '" . $end_date_search . "'");
            }
            $this->db->where("tbl_suggest_check.ballot_type", 0);
            $this->db->order_by('tbl_suggest_check.id desc');
            $dtData = $this->db->get()->result_array();

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
                "%0" . $decimals_number . "s",
                0
            ) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ('PHIẾU KẾ HOẠCH ĐIỀU ĐỘ VSAT-5S Nhà Xưởng, VP')
            )->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:P1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Khu Vực/Thiết Bị');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Tên Khu vực/ Tên Thiết bị')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Danh Mục Kiểm Tra')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Quy Định 5S')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Hình Ảnh')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Người Kiểm Tra')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Kết Quả')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Đánh Giá')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Quản Lý Khu Vực/Thiết Bị')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $i = 11;
            foreach (getListColumTable() as $kk => $vv) {
                $objPHPExcel->getActiveSheet()->setCellValue(
                    $cloumns_excel[($i)] . $sttRow,
                    $vv['name']
                )->getStyle("$cloumns_excel[$i]$sttRow")->getAlignment()->setWrapText(true);
                if ($kk != (count(getListColumTable())) - 1) {
                    $i++;
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:$cloumns_excel[$i]$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['item_type'] == 'machines' ? 'Thiết Bị' : ($value['item_type'] == 'cleaning' ? 'Khu Vực' : '')))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['name_machines_detail'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['name_machines'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['regulation_5s'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['img'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", !empty($value['staff_check']) ? get_staff_full_name($value['staff_check']) : '')->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['name_result'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['evaluate'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", !empty($value['staff_manager']) ? get_staff_full_name($value['staff_manager']) : '')->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);

                    $img = $value['img'];
                    if (!empty($img)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($img);
                        $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates('H' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", '')->getStyle("H$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


                    $colStt = 11;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModeration($value['id'], $vv['id'], 'tbl_suggest_check', 0, true);
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $_data)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        if ($kk != (count(getListColumTable())) - 1) {
                            $colStt++;
                        }
                    }
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:$cloumns_excel[$colStt]$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_ke_hoach_dieu_do_kiem_tra') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);;
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

    public function moderation_evaluate_supplier()
    {
        $preViewModerationEvaluateSupplier = true;
        $preViewOwnModerationEvaluateSupplierk = true;
        if (!$preViewModerationEvaluateSupplier && !$preViewOwnModerationEvaluateSupplierk) {
            access_denied();
        }
        $data['title'] = _l('dt_moderation_evaluate_supplier');
        $this->load->view('admin/moderation/moderation_evaluate_supplier', $data);
    }

    public function getModerationEvaluateSupplier()
    {
        $preViewModerationEvaluateSupplier = true;
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_evaluate.id as id',
            'tbl_suggest_evaluate.reference_no as reference_no',
            'tbl_suggest_evaluate.date as date',
            'tblbranch.name as name_branch',
            'tbl_suggest_evaluate.staff_evaluate as staff_evaluate',
            'tbl_category_evaluate.name as name_type_evaluate',
            'tbl_category_evaluate_detail.name as name_category_evaluate',
            'tbl_suggest_evaluate.object_type as object_type',
            'tbl_evaluate.code_evaluate as code_evaluate',
            'tbl_evaluate.name_evaluate as name_evaluate',
            'tbl_evaluate.content_evaluate as content_evaluate',
            'tbl_suggest_evaluate_item.content as content',
            'tbl_suggest_evaluate_item.actual_situation as actual_situation',
            'tbl_result.name as name_result',
            'tbl_suggest_evaluate_item.standard as standard',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_evaluate';
        $where = [
            'AND tbl_suggest_evaluate.object_type = "supplier"'
        ];
        $filter = [];

        $join = [
            'INNER JOIN tblbranch ON tblbranch.id = tbl_suggest_evaluate.branch_id',
            'INNER JOIN tbl_suggest_evaluate_item ON tbl_suggest_evaluate_item.suggest_plan_evaluate_id = tbl_suggest_evaluate.id',
            'INNER JOIN tbl_evaluate ON tbl_evaluate.id = tbl_suggest_evaluate_item.evaluate_id',
            'INNER JOIN tbl_category_evaluate ON tbl_category_evaluate.id = tbl_suggest_evaluate.type_evaluate_id',
            'LEFT JOIN tbl_category_evaluate_detail ON tbl_category_evaluate_detail.id = tbl_suggest_evaluate_item.category_evaluate_id',
            'LEFT JOIN tbl_result ON tbl_result.id = tbl_suggest_evaluate_item.result_id',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="supplier"',
        ];
        if (!$preViewModerationEvaluateSupplier) {
            array_push($where, 'AND (tbl_suggest_educate.created_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_educate.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_educate.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblsuppliers.company as name_supplier',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_evaluate/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['name_branch']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['staff_evaluate']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $aRow['name_type_evaluate'] . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $aRow['name_category_evaluate'] . '</div>';
            $object = $aRow['name_supplier'];
            $row[] = '<div class="text-left" style="width: 120px">' . $object . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . $aRow['code_evaluate'] . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['name_evaluate']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['content_evaluate']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['content']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['actual_situation']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['name_result']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['standard']) . '</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['id'], $vv['id'], $sTable, 'supplier');
                $row[] = '<div class="text-center">' . $_data . '</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcelModerationEvaluateSupplier()
    {
        $preViewModerationEvaluateSupplier = true;
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $type = $this->input->post('type');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $this->db->select('
                tbl_suggest_evaluate.id as id,
                tbl_suggest_evaluate.reference_no as reference_no,
                tbl_suggest_evaluate.date as date,
                tblbranch.name as name_branch,
                tbl_suggest_evaluate.staff_evaluate as staff_evaluate,
                tbl_category_evaluate.name as name_type_evaluate,
                tbl_category_evaluate_detail.name as name_category_evaluate,
                tbl_suggest_evaluate.object_type as object_type,
                tbl_evaluate.code_evaluate as code_evaluate,
                tbl_evaluate.name_evaluate as name_evaluate,
                tbl_evaluate.content_evaluate as content_evaluate,
                tbl_suggest_evaluate_item.content as content,
                tbl_suggest_evaluate_item.actual_situation as actual_situation,
                tbl_result.name as name_result,
                tbl_suggest_evaluate_item.standard as standard,
                tblsuppliers.company as name_supplier
            ');
            $this->db->from('tbl_suggest_evaluate');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_evaluate.branch_id');
            $this->db->join('tbl_suggest_evaluate_item', 'tbl_suggest_evaluate_item.suggest_plan_evaluate_id = tbl_suggest_evaluate.id');
            $this->db->join('tbl_evaluate', 'tbl_evaluate.id = tbl_suggest_evaluate_item.evaluate_id');
            $this->db->join('tbl_category_evaluate', 'tbl_category_evaluate.id = tbl_suggest_evaluate.type_evaluate_id', 'inner');
            $this->db->join('tbl_category_evaluate_detail', 'tbl_category_evaluate_detail.id = tbl_suggest_evaluate_item.category_evaluate_id', 'left');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_evaluate_item.result_id', 'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_evaluate.object_id AND tbl_suggest_evaluate.object_type="supplier"', 'left');

            if (!$preViewModerationEvaluateSupplier) {
                $this->db->where('(tbl_suggest_evaluate.created_by = ' . get_staff_user_id() . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_evaluate.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_evaluate.date <= '" . $end_date_search . "'");
            }

            $this->db->where('(tbl_suggest_evaluate.object_type = "supplier")');

            $this->db->order_by('tbl_suggest_evaluate.id desc');
            $dtData = $this->db->get()->result_array();


            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
                "%0" . $decimals_number . "s",
                0
            ) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $title = 'PHIẾU KẾ HOẠCH ĐIỀU ĐỘ ĐÁNH GIÁ NHÀ CUNG CẤP';
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ($title)
            )->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Chi Nhánh');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Người Đánh Giá')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Loại Đánh Giá')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Nhóm Đánh Giá')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Đối Tượng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Mã Đánh Giá')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Tên Đánh Giá')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Nội Dung Đánh Giá')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Chi Tiết Đánh Giá')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Hiện Trạng Thực Tế')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Kết Quả')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Tiêu Chuẩn/ Quy Định')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $i = 15;
            foreach (getListColumTable() as $kk => $vv) {
                $objPHPExcel->getActiveSheet()->setCellValue(
                    $cloumns_excel[($i)] . $sttRow,
                    $vv['name']
                )->getStyle("$cloumns_excel[$i]$sttRow")->getAlignment()->setWrapText(true);
                if ($kk != (count(getListColumTable())) - 1) {
                    $i++;
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:$cloumns_excel[$i]$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['name_branch'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", get_staff_full_name($value['staff_evaluate']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['name_type_evaluate'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['name_category_evaluate'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $object = $value['name_supplier'];
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $object)->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", ($value['code_evaluate']))->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", ($value['name_evaluate']))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", ($value['content_evaluate']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", ($value['content']))->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", ($value['actual_situation']))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", ($value['name_result']))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['standard'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt = 15;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModeration($value['id'], $vv['id'], 'tbl_suggest_evaluate', 'supplier', true);
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $_data)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        if ($kk != (count(getListColumTable())) - 1) {
                            $colStt++;
                        }
                    }

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:$cloumns_excel[$colStt]$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_ke_hoach_dieu_do_danh_gia_nha_cung_cap') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
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

    public function moderation_evaluate_skill()
    {
        $preViewModerationEvaluateSkill = true;
        $preViewOwnModerationEvaluateSkill = true;
        if (!$preViewModerationEvaluateSkill && !$preViewOwnModerationEvaluateSkill) {
            access_denied();
        }
        $data['title'] = _l('dt_moderation_evaluate_skill');
        $this->load->view('admin/moderation/moderation_evaluate_skill', $data);
    }

    public function getModerationEvaluateSkill()
    {
        $preViewModerationEvaluateSkill = true;
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $type = $this->input->post('type');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_department = "(
            SELECT
                tblstaff_departments.staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $aColumns = [
            'tbl_suggest_probationary_evaluate.id as id',
            'tbl_suggest_probationary_evaluate.reference_no as reference_no',
            'tbl_suggest_probationary_evaluate.date as date',
            'tbl_probationary_evaluate.reference_no as reference_no_evaluate',
            'tbl_suggest_probationary_evaluate.staff_id as staff_id',
            'tblroles.name as code_role',
            'COALESCE(tb_department.name_department,"") as name_department',
            'tbl_suggest_probationary_evaluate.date_start_probationary as date_start_probationary',
            'tbl_suggest_probationary_evaluate.date_end_probationary as date_end_probationary',
            'tbl_probationary_evaluate_item.evaluation_criteria_id as evaluation_criteria_id',
            'result.name as result',
            'result_manager.name as result_manager',
            'result_manager_hr.name as result_manager_hr',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_probationary_evaluate';
        $where = [
            'AND tbl_suggest_probationary_evaluate.type = 3'
        ];
        $filter = [];
        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_probationary_evaluate.staff_id',
            'LEFT JOIN tbl_probationary_evaluate ON tbl_probationary_evaluate.suggest_probationary_evaluate_id = tbl_suggest_probationary_evaluate.id',
            'LEFT JOIN tbl_probationary_evaluate_item ON tbl_probationary_evaluate_item.probationary_evaluate_id = tbl_probationary_evaluate.id',
            'LEFT JOIN tbl_category_department_kpi ON tbl_category_department_kpi.id = tbl_probationary_evaluate_item.evaluation_criteria_id AND tbl_probationary_evaluate_item.type = 2',
            'LEFT JOIN tbl_result result ON result.id = tbl_probationary_evaluate_item.result',
            'LEFT JOIN tbl_result result_manager ON result_manager.id = tbl_probationary_evaluate_item.result_manager',
            'LEFT JOIN tbl_result result_manager_hr ON result_manager_hr.id = tbl_probationary_evaluate_item.result_manager_hr',
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
            'LEFT JOIN ' . $tb_department . ' ON tb_department.staffid = tblstaff.staffid',
        ];

        if (!$preViewModerationEvaluateSkill) {
            array_push($where, 'AND tbl_suggest_probationary_evaluate.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_probationary_evaluate.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_probationary_evaluate.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_category_department_kpi.name as name_evaluation_criteria',
            'tbl_probationary_evaluate_item.type as type',
            'tbl_probationary_evaluate.id as probationary_evaluate_id',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $name_evaluation_criteria = '';
            if ($aRow['type'] == 1) {
                $name_evaluation_criteria = getListFiveCoreValue($aRow['evaluation_criteria_id'])['name'];
            } elseif ($aRow['type'] == 2) {
                $name_evaluation_criteria = $aRow['name_evaluation_criteria'];
            } elseif ($aRow['type'] == 3) {
                $name_evaluation_criteria = getListFollow($aRow['evaluation_criteria_id'])['name'];
            }
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_probationary_evaluate/view/' . $aRow['id'] . '?type=' . $type) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/probationary_evaluate/view/' . $aRow['probationary_evaluate_id'] . '?type=' . $type) . '" data-toggle="modal" data-target="#myModal">' . ($aRow['reference_no_evaluate']) . '</a></div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_id']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['code_role']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_department']) . '</div>';
            $row[] = '<div class="text-left">' . _dhau($aRow['date_start_probationary']) . '</div>';
            $row[] = '<div class="text-left">' . _dhau($aRow['date_end_probationary']) . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . $name_evaluation_criteria . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['result']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['result_manager']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['result_manager_hr']) . '</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['id'], $vv['id'], $sTable, 3);
                $row[] = '<div class="text-center">' . $_data . '</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcelModerationEvaluateSkill()
    {
        $preViewModerationEvaluateSkill = true;
        $columsExcel = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ'
        ];
        if ($this->input->post('export_excel')) {

            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_dt/phieu_dieu_do_danh_gia_tay_nghe.xlsx';
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
            $i = $highestColumnIndex - 1;

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
                $objPHPExcel->getActiveSheet()->setCellValue(
                    $columsExcel[($i)] . $highestRow,
                    $vv['name']
                )->getStyle("$columsExcel[$i]$highestRow")->applyFromArray($BStyleCenter)->getAlignment()->setWrapText(true);
                $i++;
            }
            $row = 2;
            $staff_id = get_staff_user_id();
            $tb_department = "(
                SELECT
                    tblstaff_departments.staffid,
                    GROUP_CONCAT(tbldepartments.name) as name_department
                FROM tbldepartments
                JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                WHERE tblstaff_departments.staffid = $staff_id
                GROUP BY tblstaff_departments.staffid
            ) tb_department";
            $this->db->select('tbl_suggest_probationary_evaluate.*,
                tbl_probationary_evaluate.reference_no as reference_no_evaluate,
                tbl_category_department_kpi.name as name_evaluation_criteria,
                tbl_probationary_evaluate_item.type as type,
                tbl_probationary_evaluate_item.evaluation_criteria_id as evaluation_criteria_id,
                result.name as result,
                result_manager.name as result_manager,
                result_manager_hr.name as result_manager_hr,
                tblroles.name as code_role,
                COALESCE(tb_department.name_department,"") as name_department
            ');
            $this->db->from('tbl_suggest_probationary_evaluate');
            $this->db->join('tblstaff', 'tblstaff.staffid = tbl_suggest_probationary_evaluate.staff_id', 'inner');
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->join($tb_department, 'tb_department.staffid = tblstaff.staffid', 'left');
            $this->db->join('tbl_probationary_evaluate', 'tbl_probationary_evaluate.suggest_probationary_evaluate_id = tbl_suggest_probationary_evaluate.id', 'left');
            $this->db->join('tbl_probationary_evaluate_item', 'tbl_probationary_evaluate_item.probationary_evaluate_id = tbl_probationary_evaluate.id', 'left');
            $this->db->join('tbl_category_department_kpi', 'tbl_category_department_kpi.id = tbl_probationary_evaluate_item.evaluation_criteria_id AND tbl_probationary_evaluate_item.type = 2', 'left');
            $this->db->join('tbl_result result', 'result.id = tbl_probationary_evaluate_item.result', 'left');
            $this->db->join('tbl_result result_manager', 'result_manager.id = tbl_probationary_evaluate_item.result_manager', 'left');
            $this->db->join('tbl_result result_manager_hr', 'result_manager_hr.id = tbl_probationary_evaluate_item.result_manager_hr', 'left');
            if (!$preViewModerationEvaluateSkill) {
                $this->db->where('(tbl_suggest_probationary_evaluate.created_by = ' . $staff_id . ')');
            }
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_probationary_evaluate.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_probationary_evaluate.date <= '" . $end_date_search . "'");
            }
            $this->db->where('tbl_suggest_probationary_evaluate.type', 3);
            $this->db->order_by('tbl_suggest_probationary_evaluate.id asc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $this->load->library('ciqrcode');
            foreach ($items as $key => $value) {
                $name_evaluation_criteria = '';
                if ($value['type'] == 1) {
                    $name_evaluation_criteria = getListFiveCoreValue($value['evaluation_criteria_id'])['name'];
                } elseif ($value['type'] == 2) {
                    $name_evaluation_criteria = $value['name_evaluation_criteria'];
                } elseif ($value['type'] == 3) {
                    $name_evaluation_criteria = getListFollow($value['evaluation_criteria_id'])['name'];
                }
                $row++;
                $dem++;
                $colStt = 0;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $dem);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, _dt($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['reference_no_evaluate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, get_staff_full_name($value['staff_id']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['code_role']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['name_department']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit(
                    $columsExcel[$colStt] . $row,
                    _dhau($value['date_start_probationary']),
                    PHPExcel_Cell_DataType::TYPE_STRING
                );
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit(
                    $columsExcel[$colStt] . $row,
                    _dhau($value['date_end_probationary']),
                    PHPExcel_Cell_DataType::TYPE_STRING
                );
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $name_evaluation_criteria);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['result'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['result_manager']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['result_manager_hr'], PHPExcel_Cell_DataType::TYPE_STRING);

                $colStt++;
                foreach (getListColumTable() as $kk => $vv) {
                    $_data = getDataModeration($value['id'], $vv['id'], 'tbl_suggest_probationary_evaluate', 3, true);
                    $objPHPExcel->getActiveSheet()->setCellValue("$columsExcel[$colStt]$row", $_data)->getStyle("$columsExcel[$colStt]$row")->getAlignment()->setWrapText(true);
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
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
            }

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_dieu_do_danh_gia_tay_nghe') . '.xls';
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

    public function moderation_maintenance()
    {
        $preViewModerationMaintenanceMuscleGroup = true;
        $preViewOwnModerationMaintenanceMuscleGroup = true;
        if (!$preViewModerationMaintenanceMuscleGroup && !$preViewOwnModerationMaintenanceMuscleGroup) {
            access_denied();
        }
        $type = "muscle_group";
        if ($this->input->get('type')) {
            $type = $this->input->get('type');
        }
        $data['type'] = $type;
        if ($type == 'muscle_group') {
            $title = lang('dt_moderation_maintenance_muscle_group');
        } elseif ($type == 'electrical_group') {
            $title = lang('dt_moderation_maintenance_electrical_group');
        } elseif ($type == 'infrastructure_group') {
            $title = lang('dt_moderation_maintenance_infrastructure_group');
        } elseif ($type == 'compressed_air_group') {
            $title = lang('dt_moderation_maintenance_compressed_air_group');
        } elseif ($type == 'refrigeration_group') {
            $title = lang('dt_moderation_maintenance_refrigeration_group');
        } elseif ($type == 'network_group') {
            $title = lang('dt_moderation_maintenance_network_group');
        } elseif ($type == 'server_group') {
            $title = lang('dt_moderation_maintenance_server_group');
        } elseif ($type == 'pccc_group') {
            $title = lang('dt_moderation_maintenance_pccc_group');
        } elseif ($type == 'computer_group') {
            $title = lang('dt_moderation_maintenance_computer_group');
        }
        $data['title'] = $title;
        $this->load->view('admin/moderation/moderation_maintenance', $data);
    }

    public function getModerationMaintenance()
    {
        $preViewModerationEvaluateSupplier = true;
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $type = $this->input->post('type');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_maintenance.id as id',
            'tbl_suggest_maintenance.reference_no as reference_no',
            'tbl_suggest_maintenance.date as date',
            'tbl_type_maintenance.name as name_type_maintenance',
            'tbl_category_maintenance.name as name_category_maintenance',
            'tbl_machines_maintenance.name as name_machines_maintenance',
            'tbldepartments.name as name_department',
            'tbl_suggest_maintenance.detail as detail',
            'tbl_suggest_maintenance.quantity as quantity',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tblbranch.name as name_branch',
            'tbl_result.name as name_result',
            'tbl_suggest_maintenance_item.standard as standard'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_maintenance';
        $where = [
            'AND tbl_category_maintenance.type = "' . $type . '"'
        ];
        $filter = [];

        $join = [
            'LEFT JOIN tbl_suggest_maintenance_item ON tbl_suggest_maintenance_item.suggest_maintenance_id = tbl_suggest_maintenance.id',
            'INNER JOIN tbl_category_maintenance ON tbl_category_maintenance.id = tbl_suggest_maintenance.category_maintenance',
            'INNER JOIN tbl_type_maintenance ON tbl_type_maintenance.id = tbl_suggest_maintenance.type_maintenance',
            'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_suggest_maintenance.department_id',
            'INNER JOIN tbl_machines ON tbl_machines.id = tbl_suggest_maintenance.machines_id',
            'LEFT JOIN tbl_machines_maintenance ON tbl_machines_maintenance.id = tbl_suggest_maintenance_item.machines_maintenance_id',
            'LEFT JOIN tbl_result ON tbl_result.id = tbl_suggest_maintenance_item.result_id',
            'INNER JOIN tblbranch ON tblbranch.id = tbl_suggest_maintenance.branch_id',
        ];
        if (!$preViewModerationEvaluateSupplier) {
            array_push($where, 'AND (tbl_category_maintenance.created_by = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_maintenance.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_maintenance.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_maintenance/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_type_maintenance']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_category_maintenance']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_machines_maintenance']) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_department'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['detail'] . '</div>';
            $row[] = '<div class="text-center">' . $aRow['quantity'] . '</div>';
            $row[] = '<div class="text-left"></div>';
            $row[] = '<div class="text-left">' . $aRow['code_machines'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_machines'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_branch'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_result'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['standard'] . '</div>';
            foreach (getListColumTable() as $kk => $vv) {
                $_data = getDataModeration($aRow['id'], $vv['id'], $sTable, $type);
                $row[] = '<div class="text-center">' . $_data . '</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcelModerationMaintenance()
    {
        $preViewModerationMaintenace = true;
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $type = $this->input->post('type');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();


            $this->db->select('
               tbl_suggest_maintenance.id as id,
               tbl_suggest_maintenance.reference_no as reference_no,
               tbl_suggest_maintenance.date as date,
               tbl_type_maintenance.name as name_type_maintenance,
               tbl_category_maintenance.name as name_category_maintenance,
               tbl_machines_maintenance.name as name_machines_maintenance,
               tbldepartments.name as name_department,
               tbl_suggest_maintenance.detail as detail,
               tbl_suggest_maintenance.quantity as quantity,
               tbl_machines.code as code_machines,
               tbl_machines.name as name_machines,
               tblbranch.name as name_branch,
               tbl_result.name as name_result,
                tbl_suggest_maintenance_item.standard as standard
            ');
            $this->db->from('tbl_suggest_maintenance');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_maintenance.branch_id', 'inner');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_maintenance.machines_id', 'inner');
            $this->db->join('tbl_suggest_maintenance_item', 'tbl_suggest_maintenance_item.suggest_maintenance_id = tbl_suggest_maintenance.id', 'left');
            $this->db->join('tbl_type_maintenance', 'tbl_type_maintenance.id = tbl_suggest_maintenance.type_maintenance', 'left');
            $this->db->join('tbl_category_maintenance', 'tbl_category_maintenance.id = tbl_suggest_maintenance.category_maintenance', 'left');
            $this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.id = tbl_suggest_maintenance_item.machines_maintenance_id', 'left');
            $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_suggest_maintenance.department_id', 'left');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_maintenance_item.result_id', 'left');


            if (!$preViewModerationMaintenace) {
                $this->db->where('tbl_suggest_maintenance.created_by = ' . get_staff_user_id() . '');
            }

            $this->db->where("tbl_category_maintenance.type = '" . $type . "'");

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_maintenance.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_maintenance.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_maintenance.id desc');
            $dtData = $this->db->get()->result_array();

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
                "%0" . $decimals_number . "s",
                0
            ) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            if ($type == 'muscle_group') {
                $title = lang('dt_moderation_maintenance_muscle_group');
            } elseif ($type == 'electrical_group') {
                $title = lang('dt_moderation_maintenance_electrical_group');
            } elseif ($type == 'infrastructure_group') {
                $title = lang('dt_moderation_maintenance_infrastructure_group');
            } elseif ($type == 'compressed_air_group') {
                $title = lang('dt_moderation_maintenance_compressed_air_group');
            } elseif ($type == 'refrigeration_group') {
                $title = lang('dt_moderation_maintenance_refrigeration_group');
            } elseif ($type == 'network_group') {
                $title = lang('dt_moderation_maintenance_network_group');
            } elseif ($type == 'server_group') {
                $title = lang('dt_moderation_maintenance_server_group');
            }
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ($title)
            )->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Bảo Dưỡng');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Loại Bảo Dưỡng');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Nhóm Bảo Dưỡng');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Bộ Phận Thiết Bị')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Khu Vực Bảo Dưỡng')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Chi tiết Bảo Dưỡng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Số Lượng')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Nhóm Thiết Bị')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Mã Thiết Bị')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Tên Thiết Bị')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Chi Nhánh')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Quy Trình')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Kết Quả')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'Tiêu Chuẩn/ Quy Định')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $i = 16;
            foreach (getListColumTable() as $kk => $vv) {
                $objPHPExcel->getActiveSheet()->setCellValue(
                    $cloumns_excel[($i)] . $sttRow,
                    $vv['name']
                )->getStyle("$cloumns_excel[$i]$sttRow")->getAlignment()->setWrapText(true);
                if ($kk != (count(getListColumTable())) - 1) {
                    $i++;
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:$cloumns_excel[$i]$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['name_type_maintenance'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['name_category_maintenance']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['name_machines_maintenance'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['name_department'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['detail'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['quantity'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", '')->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['code_machines'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['name_machines'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['name_branch'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", '')->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['name_result'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['standard'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $colStt = 16;
                    foreach (getListColumTable() as $kk => $vv) {
                        $_data = getDataModeration($value['id'], $vv['id'], 'tbl_suggest_maintenance', $type, true);
                        $objPHPExcel->getActiveSheet()->setCellValue("$cloumns_excel[$colStt]$rowBegin", $_data)->getStyle("$cloumns_excel[$colStt]$rowBegin")->getAlignment()->setWrapText(true);
                        if ($kk != (count(getListColumTable())) - 1) {
                            $colStt++;
                        }
                    }

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:$cloumns_excel[$colStt]$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = vn_to_str($title) . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
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
