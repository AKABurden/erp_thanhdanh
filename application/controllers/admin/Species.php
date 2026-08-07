<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Species extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('species_model');
        $this->load->model('misc_model');
        $this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('species');
        $this->datetime_now = time();
		$this->hasView = has_permission('species', '', 'view');
		$this->hasEdit = has_permission('species', '', 'edit');
		$this->hasAdd = has_permission('species', '', 'create');
		$this->hasDelete = has_permission('species', '', 'delete');
        $this->title = '';
        $type_title = $this->input->get('type_title');
        if($type_title == 'materials'){
            $this->title = _l('materials');
        }
        if($type_title == 'products'){
            $this->title = _l('products');
        }
        $this->type_title = $type_title;
    }

    public function index()
    {
		if(empty($this->hasView)) {
			access_denied();
		}
        $data['title'] = lang('tnh_species').' '.$this->title;
        $data['type_title'] = $this->type_title;
        $this->load->view('admin/species/index', $data);
    }

    public function add($id = 0)
    {
        $data = [];
        $species = $id ? $this->species_model->getSpeciesById($id) : [];
        if ($this->input->post('save')) {
            if ((!empty($species) && $species['code'] != $this->input->post('code')) || empty($species['code'])) {
                $this->form_validation->set_rules('code', lang("tnh_code_species"), 'required|is_unique[tbl_species.code]');
            }
            $this->form_validation->set_rules('name', lang("tnh_name_species"), 'required');
            if ($this->form_validation->run() == true) {
                $code = $this->input->post('code');
                $name = $this->input->post('name');
                $note = $this->input->post('note', false);

                $option = [
                    'code' => $code,
                    'name' => $name,
                    'note' => $note,
                ];

                if ($id) {
					if(!$this->hasEdit) {
						ajax_access_denied();
					}
                    $ins = $this->species_model->updateSpecies($id, $option);
                    $species_id = $id;
                } else {
					if(!$this->hasAdd) {
						ajax_access_denied();
					}
                    $ins = $this->species_model->insertSpecies($option);
                    $species_id = $ins;
                }

                if (!empty($ins)) {
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
		
		if(!empty($id)) {
			if(!$this->hasEdit) {
				accessDenied(true);
			}
		}
		else {
			if(!$this->hasAdd) {
				accessDenied(true);
			}
		}

        $data['species'] = $species;
        $data['id'] = $id;
        $data['title'] = ($id ? lang('tnh_edit_species') : lang('tnh_add_species')).' '.$this->title;
        $this->load->view('admin/species/add', $data);
    }

    public function getSpecies()
    {

        $aColumns = [
            'tbl_species.id as id',
            'tbl_species.code as code',
            'tbl_species.name as name',
            'tbl_species.note as note',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_species';
        $where        = [];
        $filter = [];
        $join = [];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $id = $aRow['id'];

            $row[0] = $id;
            $row[1] = $aRow['code'];
            $row[2] = $aRow['name'];
            $row[3] = $aRow['note'];

            // $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/species/view/'.$id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('tnh_view') . '</a>';
            $edit = '<a class="tnh-modal" href="' . base_url('admin/species/add/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_species') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/species/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_species') . '</a>';
			
			if(!$this->hasEdit) {
				$edit = '';
			}
			
			if(!$this->hasDelete) {
				$delete = '';
			}

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

            $row[4] = $actions;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function delete($id)
    {
		if(!$this->hasDelete) {
			ajax_access_denied();
		}
        $data = [];
        if ($this->species_model->deleteSpecies($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    // yct start
    public function modal_excel_species()
    {
		if(!$this->hasAdd) {
			access_denied();
		}
        $data['title'] = _l('t_import_species');
        $this->load->view('admin/species/import_species', $data);
    }

    public function import_species()
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
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('C');
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
                // 2: note

                $code = $value[0];
                $name = $value[1];
                $note = $value[2];

                if (empty($code) || empty($name)) {
                    continue;
                }

                $checkCode = $this->species_model->checkCodeExistSpecies($code);
                if (!empty($checkCode)) continue;

                $options = [
                    'code' => $code,
                    'name' => $name,
                    'note' => $note
                ];

                $rs = $this->species_model->insertSpecies($options);
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
    // yct end
}
