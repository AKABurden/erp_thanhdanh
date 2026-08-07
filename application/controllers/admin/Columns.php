<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Columns extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('columns_model');
        $this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('evaluate');
        $this->datetime_now = time();
        $this->tnh = true;
    }

    public function index() {
        $data = [];

        $data['title'] = lang('columns');
        $this->load->view('admin/columns/index', $data);
    }

    public function handling_columns($id = 0) {
        $data = [];
        $columns = $id ? $this->columns_model->getColumnsById($id) : [];
        if ($this->input->post('save')) {
            if ((!empty($columns) && $columns['code'] != $this->input->post('code')) || empty($columns['code'])) {
                $this->form_validation->set_rules('code', lang("tnh_code_columns"), 'required|is_unique[tbl_columns.code]');
            } else {
                $this->form_validation->set_rules('code', lang("tnh_code_columns"), 'required');
            }

            if ($this->form_validation->run() == true)
            {
                $code = $this->input->post('code');
                $name = $this->input->post('name');

                $option = [
                    'code' => $code,
                ];

                if ($id) {
                    $ins = $this->columns_model->updateColumns($id, $option);
                    $columns_columns_id = $id;
                } else {
                    $ins = $this->columns_model->insertColumns($option);
                    $columns_columns_id = $ins;
                }

                if (!empty($ins)) {

                    if ($id) {
                        $this->columns_model->deleteColumnsDetail($id);
                    }

                    if (!empty($name)) {
                        $arrName = [];
                        foreach ($name as $key => $value) {
                            $columns_detail_id = !empty($this->input->post('columns_detail_id')[$key]) ? $this->input->post('columns_detail_id')[$key] : 0;
                            $arrName[] = [
                                'id' => $columns_detail_id,
                                'columns_id' => $columns_columns_id,
                                'name' => $value
                            ];
                        }
                        $this->columns_model->insertBatchColumnsDetail($arrName);
                    }

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            return;
        }

        $data['columns'] = $columns;
        $data['id'] = $id;
        $data['title'] = $id ? lang('tnh_edit_columns') : lang('tnh_add_columns');
        $this->load->view('admin/columns/handling_columns', $data);
    }

    public function getColumns() {
        $code_columns_search = $this->input->post('code_columns_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=1500000000000000');

        $tbColumnsDetail = "(
            SELECT
                tbl_columns_detail.columns_id as columns_id,
                GROUP_CONCAT(tbl_columns_detail.name SEPARATOR '|') as name
            FROM tbl_columns_detail
            GROUP BY tbl_columns_detail.columns_id
        ) tb_columns_detail";

        $aColumns = [
            'tbl_columns.id as id',
            'tbl_columns.code as code',
            'tb_columns_detail.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_columns';
        $where        = [
        ];
        $filter = [
        ];
        
        $join = [
            'LEFT JOIN '.$tbColumnsDetail.' ON tbl_columns.id = tb_columns_detail.columns_id',
        ];

        if (!empty($code_columns_search)) {
            array_push($where, ' AND tbl_columns.code like "%'.$code_columns_search.'%"');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $id = $aRow['id'];

            $row[0] = $id;
            $row[1] = $aRow['code'];
            $row[2] = $aRow['name'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/columns/handling_columns/'.$id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_columns') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/columns/delete_columns/'.$id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_columns') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row[3] = $actions;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function delete_columns($id) {
        $data = [];

        $isUseColumns = $this->columns_model->isUseColumns($id);
        if (!empty($isUseColumns)) {
            $data['result'] = 0;
            $data['message'] = lang('Đã sử dụng không thể xóa');
            echo json_encode($data); die;
        }

        if ($this->columns_model->deleteColumns($id)) {
            $this->columns_model->deleteColumnsDetail($id);
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function import() {
        if ($this->input->post('save'))
        {

            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data); return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if($extension != 'XLSX' && $extension != 'XLS'){
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data); return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data); return;
            }
            
            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);

            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('C');
            $arraydata          = array();

            $row_start = $this->input->post('row_start') ? $this->input->post('row_start') : 3;
            $row_end = $this->input->post('row_end') ? $this->input->post('row_end') : $highestRow;
            for ($row = $row_start; $row <= $row_end; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $options = [];
            $count = 0;
            $errors = '';
            $cRow = 3;
            $index_parent = 0;
            $index_parent_element = 0;
            $pCode = '';
            $pElement = '';
            $dataImport = [];
          
            foreach ($arraydata as $key => $value) {
                $code = $value[1];
                $name = $value[2];

                $dtColumns = $this->columns_model->getColumnsByCode($code);
                if (!empty($dtColumns)) {
                    $errors.= '<div class="text-danger">Mã ['.$code.'] đã tồn tại trong phần mềm</div>';
                    continue;
                }

                $arrNameCs = explode(',', $name);

                $option = [
                    'code' => $code,
                ];
                $columns_columns_id = $this->columns_model->insertColumns($option);
                if (!empty($columns_columns_id)) {
                    $count++;

                    if (!empty($arrNameCs)) {
                        $arrName = [];
                        foreach ($arrNameCs as $k => $val) {
                            $arrName[] = [
                                'columns_id' => $columns_columns_id,
                                'name' => $val
                            ];
                        }
                        $this->columns_model->insertBatchColumnsDetail($arrName);
                    }
                }
            }

            //end handling import bom
            $data['errors'] = $errors;
            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_data_add');
            }
            echo json_encode($data); die;
        } else {
            $data['tnh'] = true;
            $data['title'] = _l('tnh_import_columns');
            $this->load->view('admin/columns/import', $data);
        }
    }
}