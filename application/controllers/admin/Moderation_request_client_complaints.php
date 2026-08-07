<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Moderation_request_client_complaints extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewModerationRequestClientComplaints = true;
        $this->preViewModerationOwnRequestClientComplaints = true;
    }

    public function index()
    {
        if (!$this->preViewModerationRequestClientComplaints && !$this->preViewModerationOwnRequestClientComplaints) {
            access_denied();
        }
        $data['title'] = _l('Điều độ Kế Hoạch Điều Độ Xử LÝ Khiếu Nại Khách Hàng');
        $this->load->view('admin/moderation_request_client_complaints/index', $data);
    }

    public function getModerationRequestClientComplaints()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_request_client_complaints.id as id',
            'tbl_request_client_complaints.reference_no as reference_no',
            'tbl_request_client_complaints.date as date',
            'tbl_brand.name as name_brand',
            'tblclients.company as company',
            'CONCAT(employees.firstname," ",employees.lastname) as fullname',
            'tbl_category_complaints.name as category_complaints',
            'tbl_request_client_complaints.detail_complaints as detail_complaints',
            'tbl_request_client_complaints.staff_tn as staff_tn',
            'tbl_request_client_complaints.timequota as timequota',
            'tbl_request_client_complaints.causal as causal',
            'tbl_request_client_complaints.processing_procedures as processing_procedures',
            'tbl_result.name as name_result',
            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
                FROM tblproduction_report
                WHERE tblproduction_report.object_id = tbl_request_client_complaints.id AND tblproduction_report.object_type = "request_client_complaints"
            ) as name_report',
            'tbl_request_client_complaints.prevention_procedures as prevention_procedures',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_client_complaints';
        $where = [];
        $filter = [];

        $join = [
            'left JOIN tbl_result ON tbl_result.id = tbl_request_client_complaints.result_id',
            'INNER JOIN tblclients ON tblclients.userid = tbl_request_client_complaints.client_id',
            'INNER JOIN tblstaff employees ON employees.staffid = tbl_request_client_complaints.employees',
            'left JOIN tbl_brand ON tbl_brand.id = tbl_request_client_complaints.brand_id',
            'left JOIN tbl_category_complaints ON tbl_category_complaints.id = tbl_request_client_complaints.category_complaints',
        ];

        if (!$this->preViewModerationRequestClientComplaints) {
            array_push($where, 'AND tbl_request_client_complaints.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_client_complaints.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_client_complaints.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$start) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_client_complaints/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_brand']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['company']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['fullname']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['category_complaints']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['detail_complaints']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['staff_tn']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['timequota']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['causal']) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['processing_procedures'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_result'] . '</div>';

            $arrReport = $aRow['name_report'];
            $htmlReport = '';
            if (!empty($arrReport)) {
                $arrReport = explode('||', $arrReport);
                if (!empty($arrReport)) {
                    foreach ($arrReport as $kk => $vv) {
                        $vv = explode('__', $vv);
                        $htmlReport .= '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $vv[1])) . '">' . $vv[0] . '</a>';
                    }
                }
            }

            $row[] = '<div style="margin-top: 5px">' . $htmlReport . '</div>';

            $row[] = '<div class="text-left">' . $aRow['prevention_procedures'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
}