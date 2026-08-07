<?php

use BraintreeHttp\Serializer\Json;

defined('BASEPATH') or exit('No direct script access allowed');

class Gate_pass extends AdminController
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('gate_pass_model');
        
        // $this->perViewsuggestion = has_permission('suggestion', '', 'view');
        // $this->perViewOwnsuggestion = has_permission('suggestion', '', 'view_own');
        // $this->perAddsuggestion = has_permission('suggestion', '', 'create');
        // $this->perEditsuggestion = has_permission('suggestion', '', 'edit');
        // $this->perDeletesuggestion = has_permission('suggestion', '', 'delete');
        // $this->perApprovesuggestion = has_permission('suggestion', '', 'approve_accept');
    }

    public function index()
    {
        // if (!$this->perViewsuggestion && !$this->perViewOwnsuggestion) {
        //     access_denied('suggestion');
        // }
        $data['title'] = _l('gate_pass');
        $this->load->view('admin/gate_pass/manage', $data);
    }

    public function table() {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tblgate_pass.id as id',
            'tblgate_pass.code as code',
            'tblgate_pass.object_type as object_type',
            'tblgate_pass.object_id as object_id',
            'tblgate_pass.enter_time as enter_time',
            'tblgate_pass.exit_time as exit_time',
            'tblgate_pass.create_by as create_by',
            'tblgate_pass.approved_by as approved_by',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tblgate_pass';
        $where        = [];
        $filter = [];

        $join = [
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $output = $result['output'];
		$rResult = $result['rResult'];
        // var_dump($result);die;
		foreach ($rResult as $key => $aRow) {
            // if ()
			$column[0] = '';
			$column[1] = '';
			$column[2] = '';
			$column[3] = '';
			$column[4] = '';
			$column[5] = '';
			$column[6] = '';
			$column[7] = '';
			$column[8] = '';

            $output['aaData'][] = $column;
        }
        echo json_encode($output);
    }

    public function add_modal($id = '')
	{
		$data['title'] = _l('gate_pass_add');

		$data['object_type_list'] = [
            0 => ['code' => 'object_clients', 'name' => _l('gp_object_clients')],
            1 => ['code' => 'object_suppliers', 'name' => _l('gp_object_suppliers')],
            2 => ['code' => 'object_staff', 'name' => _l('gp_object_staff')]
        ];
        $data['object_list'] = $this->gate_pass_model->getObjectList('');
        // var_dump($data['object_type_list']);die;
		$data['proposal_type'] = '';//$this->suggestion_type_model->get();
		if (!empty($id)) {
			$data['selected'] = get_table_where('tblgate_pass', array('id' => $id), '', 'row');
		}
		if (empty($data['selected'])) {
			$data['selected'] = new stdClass;
			$data['selected']->id = '';
			$data['selected']->code = $this->gate_pass_model->getCode();
			$data['selected']->date = _d(date('Y-m-d'));
			$data['selected']->staff = get_staff_user_id();
			$data['selected']->proposal_type = '';
			$data['selected']->money = 0;
			$data['selected']->content = '';
		} else {
			$data['selected']->date = '';//_d(substr($data['object']->date, 0, -9));
		}
		$data['selected']->department = '';//$this->site_model->getStaffByStaffId($data['object']->staff)['name_department'];
		$this->load->view('admin/gate_pass/modal_add', $data);
	}

    public function getObjectList ($objectType = '')
    {
        echo json_encode($this->gate_pass_model->getObjectList($objectType));
		die();
    }
}