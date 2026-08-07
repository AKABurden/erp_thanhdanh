<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Handover_item extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('print_type_model');
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
        $data['title'] = _l('handover_item');
        $this->load->view('admin/handover_item/manage', $data);
    }

    public function table()
    {
        $aColumns = [
            'tbltype_object_internal_proposal.id as id',
            'tbltype_object_internal_proposal.name as name',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbltype_object_internal_proposal';
        $where        = [
			'AND type_hide = 0'
		];
        $filter = [];
        $join = [];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        // $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            // $start++;
            $row[0] = '<div class="text-center">' . (++$key) . '</div>';
            $row[1] = $aRow['name'];

            $actions = '<div>' . icon_btn('#', 'pencil', 'btn-default', array('onclick' => "add(" . $aRow['id'] . "); return false;"));
            if (true/*!$this->print_type_model->isUsed($aRow['id'])*/) {
                $actions .= '<a onclick="deleting (' . $aRow['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
                                    <i class="fa fa-remove"></i>
                                </a></div>';
            }
            $row[2] = $actions;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function add_modal($id = '')
    {
        $data = array();
        if (!empty($id)) {
            $object = get_table_where('tbltype_object_internal_proposal', array('id' => $id), '', 'row');
            if (!empty($object)) {
                $data['id'] = $object->id;
                $data['name'] = $object->name;
            }
            $data['title'] = 'Sửa ' . _l('handover_item');
        } else {
            $data['title'] = 'Thêm ' . _l('handover_item');
        }
        $this->load->view('admin/handover_item/add_modal', $data);
    }

    public function add()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            $id = !empty($data['id']) ? $data['id'] : '';
            unset($data['id']);

            if ($this->isExistName($data['name'], $id)) {
                $success = false;
                $message = _l('handover_item_name') . ' này đã tồn tại';
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
                die;
            }
            if (empty($id)) { //add a new

                // $this->db->select_max('id');
                // $data['key_object'] = $this->db->get('tbltype_object_internal_proposal')->row()->id + 1;

                $this->db->insert('tbltype_object_internal_proposal', $data);
                $id = $this->db->insert_id();
                if ($id) {
                    $this->db->where('id', $id);
                    $this->db->update('tbltype_object_internal_proposal', ['key_object' => $id]);

                    $success = true;
                    $message = _l('ch_added_successfuly');
                } else {
                    $success = false;
                    $message = _l('ch_added_successfuly_not');
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
                die;
            } else { // update exist
                $this->db->where('id', $id);
                $id = $this->db->update('tbltype_object_internal_proposal', $data);
                if ($id) {
                    $success = true;
                    $message = _l('ch_updated_successfuly');
                } else {
                    $success = false;
                    $message = _l('ch_no_updated_successfuly');
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
                die;
            }
        }
    }

    public function delete ($id = '')
    {
        $handover_item = get_table_where('tbltype_object_internal_proposal', ['id' => $id], '', 'row', '', 'key_object');
        $handover_item_key_object = (!empty($handover_item->key_object) ? $handover_item->key_object : '');
        if ($this->isUsed($handover_item_key_object)) {
        	echo json_encode(array(
                'success' => false,
                'message' => _l('handover_item') . ' này đã được dùng, không thể xóa!'
            ));
        	die;    
        }

        $isSuccess = $this->db->delete('tbltype_object_internal_proposal', array('id' => $id));
        if ($isSuccess) {
            $success = true;
            $message = _l('ch_delete_successfuly');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
        die;
    }

    public function modal_excel_import () {
        $data['title'] = 'Import ' . _l('handover_item');
        $this->load->view('admin/handover_item/excel_import', $data);
    }

    public function excel_import()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('B');
            $arraydata          = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            foreach ($arraydata as $key => $value) {
                // 0: name
                $name = $value[0];

                $options = [
                    'name' => $name
                ];
                $rs = $this->insert($options);
                if ($rs) {
                    $count++;
                }
            }
        }
        echo json_encode(
            [
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]
        );
        die();
    }

    public function isExistName ($name, $exceptionId = '')
    {
        $this->db->from('tbltype_object_internal_proposal');
        $this->db->where('tbltype_object_internal_proposal.name', $name);
        if (!empty($exceptionId)) {
            $this->db->where('tbltype_object_internal_proposal.id <>', $exceptionId);
        }
        $this->db->limit(1);
        $result = $this->db->get()->num_rows();
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }

    public function isUsed($type_object = '')
    {
        $this->db->where('type_object', $type_object);
        $result = $this->db->get('tbl_delivery_records')->row();

        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    public function insert($data)
    {
        if (empty($data['name']) || $this->isExistName($data['name'])) {
            return 0;
        }

        $this->db->insert('tbltype_object_internal_proposal', $data);
        $rs = $this->db->insert_id();
        if (!empty($rs)) {
            $this->db->where('id', $rs);
            $this->db->update('tbltype_object_internal_proposal', ['key_object' => $rs]);
        }
        return $rs;
    }
}