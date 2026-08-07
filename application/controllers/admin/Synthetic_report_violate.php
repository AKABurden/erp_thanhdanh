<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Synthetic_report_violate extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewSyntheticReportViolate = true;
        $this->preViewSyntheticDetailReportViolate = true;
    }

    public function index()
    {
        if (!$this->preViewSyntheticReportViolate) {
            access_denied('synthetic_report_violate');
        }
        $data['title'] = _l('Thống kê báo cáo vi phạm');
        $this->load->view('admin/synthetic_report_violate/index', $data);
    }

    public function getSyntheticReportViolate()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $year_search = $this->input->post('year_search') ?? date('Y');
        $aColumns = [
            'tbl_room.id as id',
            'tbl_room.name as name',
        ];
        foreach (getMonth() as $k => $v){
            if (empty($v)){
                continue;
            }
            $aColumns[] = '"" as report_'.$v.'';
            $aColumns[] = '"" as report_violate_'.$v.'';
        }
        $sIndexColumn = 'id';
        $sTable = 'tbl_room';
        $where = [

        ];
        $filter = [];
        $join = [
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $arrId = array_column($rResult,'id');
        if (!empty($arrId)){
            $this->db->select('
                tblproduction_report.id_departments AS id_room,
                LPAD(MONTH(date), 2, "0") as month,
                tblproduction_report.violate,
                COUNT(*) AS total
            ');
            $this->db->from('tblproduction_report');
            $this->db->where_in('tblproduction_report.id_departments', $arrId);
            $this->db->where('YEAR(date)', $year_search);
            $this->db->group_by([
                'tblproduction_report.id_departments',
                'MONTH(date)',
                'tblproduction_report.violate'
            ]);

            $dtListReport = $this->db->get()->result_array();
            $dtListReport = array_reduce($dtListReport, function ($acc, $item) {
                $acc[$item['id_room']][$item['month']][$item['violate']] = $item;
                return $acc;
            });
        }
        $footData = [];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">'.$aRow['name'].'</div>';
            $dtReport = $dtListReport[$aRow['id']] ?? [];
            foreach (getMonth() as $k => $v){
                if (empty($v)){
                    continue;
                }
                $report = $dtReport[$v] ?? [];
                $row[] = '<div class="text-center">'.( !empty($report[0]['total']) ? formatNumber($report[0]['total']) : '').'</div>';
                $row[] = '<div class="text-center">'.( !empty($report[1]['total']) ? formatNumber($report[1]['total']) : '').'</div>';

                $footData['foot_'.$v]        = ($footData['foot_'.$v] ?? 0) + ($report[0]['total'] ?? 0);
                $footData['foot_violate_'.$v] = ($footData['foot_violate_'.$v] ?? 0) + ($report[1]['total'] ?? 0);
            }
            $output['aaData'][] = $row;
        }
        $output['footData'] = $footData;
        echo json_encode($output);
    }

    public function detail_report_violate()
    {
        if (!$this->preViewSyntheticDetailReportViolate) {
            access_denied('detail_report_violate');
        }
        $data['dtRoom'] = get_table_where('tbl_room');
        $data['title'] = _l('Thống kê chi tiết báo cáo vi phạm');
        $this->load->view('admin/synthetic_report_violate/detail_report_violate', $data);
    }

    public function getSyntheticDetailReportViolate()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $year_search = $this->input->post('year_search') ?? date('Y');
        $room_search = $this->input->post('room_search') ?? 0;
        $whereRoom = '';
        if (!empty($room_search)){
            $whereRoom = "AND tbl_room.id = ".$room_search."";
        }
        $tb_tamp = "(
            SELECT
                tblproduction_report.recommended_list_group_id AS recommended_list_group_id,
                tblproduction_report.id_trouble AS id_trouble,
                tbltrouble.name AS name_trouble,
                tblproduction_report.id_departments AS id_departments,
                tbl_room.name AS name_room,
                COUNT(*) AS total
            FROM tblproduction_report
            INNER JOIN tbl_room ON tbl_room.id = tblproduction_report.id_departments
            LEFT JOIN tbltrouble ON tbltrouble.id = tblproduction_report.id_trouble
            WHERE YEAR(date) = ".$year_search." $whereRoom
            GROUP BY recommended_list_group_id,id_trouble,id_departments
        ) tb_tamp";
        $aColumns = [
            'tbl_relate.id as id',
            'tb_tamp.name_room as name_room',
            'tbl_relate.name as name',
            'tb_tamp.name_trouble as name_trouble',
            'tb_tamp.total as total',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_relate';
        $where = [
        ];
        $where[] = 'AND tbl_relate.parent_id = 0 AND tbl_relate.type_show = 1';
        $filter = [];
        $join = [
            'INNER JOIN '.$tb_tamp.' ON tb_tamp.recommended_list_group_id = tbl_relate.id'
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $arrId = array_column($rResult,'id');
        if (!empty($arrId)){
        }
        $footData = [];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">'.$aRow['name_room'].'</div>';
            $row[] = '<div class="text-left" style="width: 100px">'.$aRow['name'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['name_trouble'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['total'].'</div>';
            $output['aaData'][] = $row;
        }
        $output['footData'] = $footData;
        echo json_encode($output);
    }
}