<?php

defined('BASEPATH') or exit('No direct script access allowed');

class suggestion_type extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('suggestion_type_model');
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
        $data['title'] = _l('suggestion_type');
        $this->load->view('admin/suggestion_type/manage', $data);
    }

    public function table()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tblsuggestion_type.id as id',
            'tblsuggestion_type.code as code',
            'tblsuggestion_type.name as name',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tblsuggestion_type';
        $where        = [];
        $filter = [];
        $join = [
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row[0] = '<div class="text-center">' . (++$key) . '</div>';
            $row[1] = $aRow['code'];
            $row[2] = $aRow['name'];
            
            $html = '<div>' . icon_btn('#', 'pencil', 'btn-default', array('onclick' => "add(" . $aRow['id'] . "); return false;"));
            if (true/*!$this->suggestion_type_model->isUsed($aRow['id'])*/) {
                $html .= '<a onclick="delete_suggestion_type(' . $aRow['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
                                    <i class="fa fa-remove"></i>
                                </a></div>';
            }
            $row[3] = $html;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function add_modal($id = '')
    {
        $data = array();
        if (!empty($id)) {
            $collect_categories = get_table_where('tblsuggestion_type', array('id' => $id), '', 'row');
            if (!empty($collect_categories)) {
                $data['id'] = $collect_categories->id;
                $data['code'] = $collect_categories->code;
                $data['name'] = $collect_categories->name;
            }
        }
        $this->load->view('admin/suggestion_type/modal_add', $data);
    }

    public function add()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            $id = !empty($data['id']) ? $data['id'] : '';
            unset($data['id']);

            if (empty($id)) { //add a new
                if ($this->suggestion_type_model->isExistCode($data['code'])) {
                    $success = false;
                    $message = _l('Mã đề xuất nội bộ đã tồn tại!');
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                    die;
                }
                
                $this->db->insert('tblsuggestion_type', $data);
                $id = $this->db->insert_id();
                if ($id) {
                    $success = true;
                    $message = _l('ch_added_successfuly');
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
                die;
            } else { // update exist
                $this->db->where('id', $id);
                $id = $this->db->update('tblsuggestion_type', $data);
                if ($id) {
                    $success = true;
                    $message = _l('ch_updated_successfuly');
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
                die;
            }
        }
    }

    public function delete($id = '')
    {
        if ($this->suggestion_type_model->isUsed($id)) {
        	echo json_encode(array(
                'success' => false,
                'message' => 'Loại đề xuất này đã được dùng. Không thể xóa!'
            ));
        	die;    
        }

        $isSuccess = $this->db->delete('tblsuggestion_type', array('id' => $id));
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

    public function modal_excel_import()
    {
        $data['title'] = _l('colcat_import_excel');
        $this->load->view('admin/suggestion_type/excel_import', $data);
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
                // 0: code
                // 1: name
                $code = $value[0];
                $name = $value[1];

                $options = [
                    'code' => $code,
                    'name' => $name,
                ];
                $rs = $this->suggestion_type_model->insert($options);
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
}