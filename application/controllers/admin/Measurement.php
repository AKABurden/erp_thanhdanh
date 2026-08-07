<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Measurement extends AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('measurement_model');
    }
    
    public function index()
    {
        $data['title'] = _l('measurement');
        $this->load->view('admin/measurement/manage', $data);
    }

    public function table ()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tblmeasurement.id as id',
            'tblmeasurement.value as value',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tblmeasurement';
        $where        = [];
        if($this->input->post('filterType')) {
			$filterType = $this->input->post('filterType');
            switch ($filterType) {
                case 1:
                    $where[] = 'AND tblmeasurement.type = 1';
                    break;
                case 2:
                    $where[] = 'AND tblmeasurement.type = 2';
                    break;
                case 3:
                    $where[] = 'AND tblmeasurement.type = 3';
                    break;
            }
		}

        $filter = [];
        $join = [
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblmeasurement.type'], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row[0] = '<div class="text-center">' . ++$key . '</div>';
            $row[1] = '<div class="text-right">' . number_format($aRow['value']) . '</div>';
            
            $html = '<div class="text-center">' . icon_btn('#', 'pencil', 'btn-default', array('onclick' => "add(" . $aRow['id'] . "); return false;"));
            if (true/*!$this->print_type_model->isUsed($aRow['id'])*/) {
                $html .= '<a onclick="deleting (' . $aRow['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
                                    <i class="fa fa-remove"></i>
                                </a></div>';
            }
            $row[2] = $html;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function modal_add ($id = '')
    {
        $data = array();
        if (!empty($id)) {
            $object = get_table_where('tblmeasurement', array('id' => $id), '', 'row');
            if (!empty($object)) {
                $data['id'] = $object->id;
                $data['type_selected'] = $object->type;
                $data['value'] = number_format($object->value);
            }
        }
        $data['type_list'] = array(
            ['id' => 1, 'name' => _l('tnh_longs')],
            ['id' => 2, 'name' => _l('tnh_wide')],
            ['id' => 3, 'name' => _l('tnh_height').'(mm)'],
        );
        $this->load->view('admin/measurement/modal_add', $data);
    }

    public function add ()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            $id = !empty($data['id']) ? $data['id'] : '';
            unset($data['id']);
            $data['value'] = number_format_data($data['value'], false);
            if ($data['value'] < 0) {
                $data['value'] = $data['value'] * -1;
            }

            if (empty($id)) { //add a new
                $this->db->insert('tblmeasurement', $data);
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
                $id = $this->db->update('tblmeasurement', $data);
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
        if ($this->measurement_model->isUsed($id)) {
        	echo json_encode(array(
                'success' => false,
                'message' => 'Kích thước này đã được dùng. Không thể xóa!'
            ));
        	die;
        }

        $isSuccess = $this->db->delete('tblmeasurement', array('id' => $id));
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
        $data['title'] = _l('measurement_import_excel');
        $this->load->view('admin/measurement/excel_import', $data);
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

            foreach ($arraydata as $key => $row) {
                // 0: type
                // 1: value
                $value = $row[1];
                switch ($row[0]) {
                    case 'Chiều dài/ đường kính':
                        $type = 1;
                        break;
                    case 'Rộng':
                        $type = 2;
                        break;
                    case 'Chiều cao':
                        $type = 3;
                        break;
                    default:
                        $type = 0;
                        break;
                }

                $options = [
                    'type' => $type,
                    'value' => $value,
                ];
                // var_dump($options);
                $rs = $this->measurement_model->insert($options);
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