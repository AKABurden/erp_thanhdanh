<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Costs_lever extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('costs_model');
	}

	public function index()
	{

		//        if (!has_permission('costs', '', 'view')) {
		//                access_denied('Debt suppliers');
		//        }
		$type_cost = $this->input->get('type');
		$data['type_cost'] = $type_cost;
		$title = '';
		$data['title'] = _l('Nhóm chi phí');
		//		$data['costs'] = [];
		//		$this->costs_model->get_by_id(0, $data['costs']);
		$data['costs'] = $this->db->get_where('tblcosts', ['costs_parent' => 0])->result_array();

		$full_costs = $this->costs_model->get_full_costs();
		$data['full_costs'] = $full_costs;
		$this->load->view('admin/costs_lever/manage', $data);
	}
	public function getCosts()
	{

		$this->db->simple_query('SET SESSION group_concat_max_len=15000000');

		$aColumns = [
			'tblcosts.id as id',
			'tblcosts.code as code',
			'tblcosts.name as name',
			'tblcosts.type as type',
			'"" as actions',
		];
		$sIndexColumn = 'id';
		$sTable       = 'tblcosts';
		$where        = [];
		$filter = [];
		$type_cost = $this->input->post('type_cost');
		if (!empty($type_cost)) {
			$where[]        = 'AND tblcosts.type = ' . $type_cost;
		}
		$where[]        = 'AND tblcosts.costs_parent = 0';

		$join = [
			'LEFT JOIN tblcosts tb_cost ON tb_cost.id = tblcosts.costs_parent',
		];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
		foreach ($rResult as $key => $aRow) {
			$start++;
			$cost_id = $aRow['id'];

			$row[0] = '<div class="text-center">' . (++$key) . '</div>';
			$row[1] = $aRow['code'];
			$row[2] = $aRow['name'];
			$strtype = '';
			if ($aRow['type'] == 1) {
				$strtype = lang('tnh_cpncsx');
			} else if ($aRow['type'] == 2) {
				$strtype = lang('tnh_cpsxc');
			} else if ($aRow['type'] == 3) {
				$strtype = lang('Chi Phí Hợp Lý');
			} else if ($aRow['type'] == 4) {
				$strtype = lang('Chi Phí Hợp Lý');
			} else if ($aRow['type'] == 5) {
				$strtype = lang('Chi Phí Khấu Trừ');
			} else if ($aRow['type'] == 6) {
				$strtype = lang('Chi Phí Giảm Trừ');
			}
			$row[3] = $strtype;
			$html = '<div>' . icon_btn('#', 'pencil', 'btn-default', array('onclick' => "edit_costs(" . $aRow['id'] . ",'" . $aRow['code'] . "','" . $aRow['name'] . "', 0, '" . $aRow['type'] . "'); return false;"));
			$ktr = get_table_where('tblcosts', array('costs_parent' => $aRow['id']), '', 'row');
			if (empty($ktr) && !exsit_costs($aRow['id'])) {
				$html .= '<a onclick="delete_costs(' . $aRow['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
                                    <i class="fa fa-remove"></i>
                                </a></div>';
			}
			if ($aRow['id'] == 95) {
				$html = '';
			}
			$row[4] = $html;
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}
}