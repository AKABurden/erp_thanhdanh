<?php
// Sửa thành Loại in (tbl_type_print)
defined('BASEPATH') or exit('No direct script access allowed');

class Print_type extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('print_type_model');
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
        $data['title'] = _l('print_type');
        $this->load->view('admin/print_type/manage', $data);
    }

    public function table()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_type_print.id as id',
            'tbl_type_print.code as code',
            'tbl_type_print.name as name',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_type_print';
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
            if (true/*!$this->print_type_model->isUsed($aRow['id'])*/) {
                $html .= '<a onclick="deleting (' . $aRow['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
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
            $object = get_table_where('tbl_type_print', array('id' => $id), '', 'row');
            if (!empty($object)) {
                $data['id'] = $object->id;
                $data['code'] = $object->code;
                $data['name'] = $object->name;
            }
        }
        $this->load->view('admin/print_type/modal_add', $data);
    }

    public function add()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            $id = !empty($data['id']) ? $data['id'] : '';
            unset($data['id']);

            if (empty($id)) { //add a new
                if ($this->print_type_model->isExistCode($data['code'])) {
                    $success = false;
                    $message = _l('Mã loại in đã tồn tại!');
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                    die;
                }
                
                $this->db->insert('tbl_type_print', $data);
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
                $id = $this->db->update('tbl_type_print', $data);
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
        if ($this->print_type_model->isUsed($id)) {
        	echo json_encode(array(
                'success' => false,
                'message' => 'Loại in này đã được dùng. Không thể xóa!'
            ));
        	die;    
        }

        $isSuccess = $this->db->delete('tbl_type_print', array('id' => $id));
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
        $data['title'] = _l('print_type_import_excel');
        $this->load->view('admin/print_type/excel_import', $data);
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
                $rs = $this->print_type_model->insert($options);
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