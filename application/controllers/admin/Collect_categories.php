<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Collect_categories extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('collect_categories_model');
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
        $data['title'] = _l('collect_categories');
        $this->load->view('admin/collect_categories/manage', $data);
    }

    public function table()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tblcollect_categories.id as id',
            'tblcollect_categories.code as code',
            'tblcollect_categories.name as name',
            'tblcollect_categories_parent.code as code_parent',
            'tblcollect_categories_parent.name as name_parent',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tblcollect_categories';
        $where        = [];
        $filter = [];

        $join = [
            'LEFT JOIN tblcollect_categories tblcollect_categories_parent ON tblcollect_categories_parent.id = tblcollect_categories.costs_parent',
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            // $cost_id = $aRow['id'];

            $row[0] = '<div class="text-center">' . (++$key) . '</div>';
            $row[1] = $aRow['code'];
            $row[2] = $aRow['name'];
            $row[3] = $aRow['code_parent'];
            $row[4] = $aRow['name_parent'];
            // $strtype = '';
            // if ($aRow['type'] == 1) {
            //     $strtype = lang('tnh_cpncsx');
            // } else if ($aRow['type'] == 2) {
            //     $strtype = lang('tnh_cpsxc');
            // }
            // $row[5] = $strtype;
            $html = '<div>' . icon_btn('#', 'pencil', 'btn-default', array('onclick' => "add(" . $aRow['id'] . "); return false;"));
            $ktr = get_table_where('tblcollect_categories', array('costs_parent' => $aRow['id']), '', 'row');
            if (empty($ktr) && !$this->collect_categories_model->isUsed($aRow['id'])) {
                $html .= '<a onclick="delete_collect_categories(' . $aRow['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
                                    <i class="fa fa-remove"></i>
                                </a></div>';
            }
            $row[5] = $html;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function add_modal($id = '')
    {
        if (!empty($id)) {
            $collect_categories = get_table_where('tblcollect_categories', array('id' => $id), '', 'row');
            if (!empty($collect_categories)) {
                $data['id'] = $collect_categories->id;
                $data['code'] = $collect_categories->code;
                $data['name'] = $collect_categories->name;
                $data['costs_parent'] = $collect_categories->costs_parent;
                $data['type'] = $collect_categories->type;
                $data['parent'] = $this->db->get_where('tblcollect_categories', ['costs_parent' => 0, 'id <>' => $id])->result_array();
            }
        } else {
            $data['parent'] = $this->db->get_where('tblcollect_categories', ['costs_parent' => 0])->result_array();
        }
        $this->load->view('admin/collect_categories/modal_add', $data);
    }

    public function add()
    {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            $id = !empty($data['collect_categories_id']) ? $data['collect_categories_id'] : '';
            unset($data['collect_categories_id']);

            if (empty($id)) { //add a new
                if ($this->isExistCode($data['code'])) {
                    $success = false;
                    $message = _l('Mã danh mục thu đã tồn tại!');
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                    die;
                }
                if ($data['costs_parent'] == NULL || $data['costs_parent'] == '') {
                    $data['lever'] = 1;
                } else {
                    $lever = 1;
                    $parent = $data['costs_parent'];
                    while ($parent > 0) {
                        $ktr = get_table_where('tblcollect_categories', array('id' => $parent), '', 'row');
                        $parent = $ktr->costs_parent;
                        $lever++;
                    }
                    $data['lever'] = $lever;
                }
                $this->db->insert('tblcollect_categories', $data);
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
                if ($data['costs_parent'] == NULL || $data['costs_parent'] == '') {
                    $data['lever'] = 1;
                } else {
                    $lever = 1;
                    $parent = $data['costs_parent'];
                    while ($parent > 0) {
                        $ktr = get_table_where('tblcollect_categories', array('id' => $parent), '', 'row');
                        $parent = $ktr->costs_parent;
                        $lever++;
                    }
                    $data['lever'] = $lever;
                }
                $this->db->where('id', $id);
                $id = $this->db->update('tblcollect_categories', $data);
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
        if ($this->collect_categories_model->isUsed($id)) {
        	echo json_encode(array(
                'success' => false,
                'message' => 'Danh mục thu này đã được dùng. Không thể xóa!'
            ));
        	die;    
        } 
        $parent = get_table_where('tblcollect_categories', array('costs_parent' => $id), '', 'row');
        if (!empty($parent)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Danh mục thu này  có chứa danh mục con. Không thể xóa!'
            ));
            die;
        }
        $isSuccess = $this->db->delete('tblcollect_categories', array('id' => $id));
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
        $this->load->view('admin/collect_categories/excel_import', $data);
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
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('D');
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
                // 2: parent_code
                // 3: parent_name

                $code = $value[0];
                $name = $value[1];
                $parent_code = $value[2];
                $parent_name = $value[3];

                if (empty($code) || empty($name)) {
                    continue;
                }

                $parent_id = '';
                if (!empty($parent_code)) {
                    $parent = get_table_where('tblcollect_categories', array('code' => $parent_code), '', 'row');
                    if (!empty($parent)) {
                        $parent_id = $parent->id;
                    }
                }
                if (empty($parent_id)) {
                    if (!empty($parent_name)) {
                        $data_parent = [
                            'code' => $parent_code,
                            'name' => $parent_name,
                            'costs_parent' => ''
                        ];
                        $parent_id = $this->insert($data_parent);

                        if (empty($parent_id)) {
                            continue;
                        } else {
                            $count++;
                        }
                    } else {
                        continue;
                    }
                }

                $options = [
                    'code' => $code,
                    'name' => $name,
                    'costs_parent' => $parent_id,
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

    public function insert($data)
    {
        // var_dump($data);
        // return 0;
        if ($this->isExistCode($data['code'])) {
            return 0;
        }

        if ($data['costs_parent'] == NULL || $data['costs_parent'] == '') {
            $data['lever'] = 1;
        } else {
            $lever = 1;
            $parent = $data['costs_parent'];
            while (!empty($parent)) {
                $ktr = get_table_where('tblcollect_categories', array('id' => $parent), '', 'row');
                $parent = $ktr->costs_parent;
                $lever++;
            }
            $data['lever'] = $lever;
        }

        $this->db->insert('tblcollect_categories', $data);
        $rs = $this->db->insert_id();
        return $rs;
    }

    public function isExistCode($code)
    {
        $this->db->from('tblcollect_categories');
        $this->db->where('tblcollect_categories.code', $code);
        $this->db->limit(1);
        $result = $this->db->get()->num_rows();
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }
	
    public function isExistId($id)
    {
        $this->db->from('tblcollect_categories');
        $this->db->where('tblcollect_categories.id', $id);
        $this->db->limit(1);
        $result = $this->db->get()->num_rows();
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }
}
