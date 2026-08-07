<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Units extends AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('unit_model');
        if (!is_admin()) {
            access_denied('Category_tasks');
        }
        $this->title = '';
        $type_title = $this->input->get('type_title');
        if($type_title == 'materials'){
            $this->title = _l('materials');
        }
        if($type_title == 'products'){
            $this->title = _l('products');
        }
        if($type_title == 'machines'){
            $this->title = 'Thiết bị';
        }
        $this->type_title = $type_title;
    }
    /* Open also all taks if user access this /tasks url */
    public function index()
    {

        $this->list_units();
    }

    /* List all tasks */
    public function list_units()
    {


        $data['roles'] = $this->unit_model->get_roles();
        // var_dump($data['roles']);die();
        $data['title'] = _l('item_unit').' '.$this->title;
        $data['type_title'] = $this->title;
        $this->load->view('admin/units/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('units');
    }
    /* Get task data in a right pane */
    public function delete_unit($id)
    {
        if (!$id) {
            die('ch_no_items');
        }

        $this->db->from('tbl_materials');
        $this->db->where('tbl_materials.unit_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }

        $this->db->from('tbl_products');
        $this->db->where('tbl_products.unit_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }

        $this->db->from('tbl_exchange_items');
        $this->db->where('tbl_exchange_items.unit_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }

        $this->db->from('tbl_exchange_products');
        $this->db->where('tbl_exchange_products.unit_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }

        $this->db->from('tbl_element_items');
        $this->db->where('tbl_element_items.unit_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }

        $this->db->from('tbl_order_items');
        $this->db->where('tbl_order_items.unit_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }

        $this->db->from('tbl_business_plan_items');
        $this->db->where('tbl_business_plan_items.unit_id', $id);
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }

        $success    = $this->unit_model->delete_unit($id);
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function add_unit()
    {
        $success = false;
        $message = _l('Thêm không thành công');
        if ($this->input->post()) {
			$data = $this->input->post();
			$this->db->where('code_unit', trim($data['code_unit']));
			$ktUnit = $this->db->get('tblunits')->row();
			if(!empty($ktUnit)) {
				echo json_encode(array(
					'success' => false,
					'alert_type' => 'danger',
					'message' => 'Mã đơn vị tính đã tồn tại vui lòng nhập mã khác'
				));
				die;
			}
			
            $message = '';
            $id = $this->unit_model->add_unit($this->input->post(NULL, FALSE));
            if ($id) {
                $success = true;
                $message = _l('ch_added_successfuly');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
            die;
        }
    }
    public function update_unit($id = "")
    {
        if ($id != "") {
            $message    = '';
            $alert_type = 'warning';
            if ($this->input->post()) {
				
				$data = $this->input->post();
	
				$this->db->where('unitid', $id);
				$Unit = $this->db->get('tblunits')->row();
				if($Unit->code_unit != $data['code_unit']) {
					$this->db->where('code_unit', trim($data['code_unit']));
					$ktUnit = $this->db->get('tblunits')->row();
					if (!empty($ktUnit)) {
						echo json_encode(array(
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã đơn vị tính đã tồn tại vui lòng nhập mã khác'
						));
						die;
					}
				}
				
				
                $success = $this->unit_model->update_unit($this->input->post(), $id);
                if ($success) {
                    $message    = _l('ch_updatee_items');
                };
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        } else {
            if ($this->input->post()) {
                $success = $this->unit_model->add_unit($this->input->post());
                if ($success) {
                    $alert_type = 'success';
                    $message    = 'ch_adde_items';
                }
            }
            echo json_encode(array(
                'alert_type' => $alert_type,
                'message' => $message
            ));
        }
        die;
    }



    public function get_row_unit($id)
    {
        echo json_encode($this->unit_model->get_row_unit($id));
    }



    public function excel_import()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $errors = '';
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
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('J');
            $arraydata          = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $listType = [
                mb_strtoupper('ngày', 'UTF-8') => 1,
                mb_strtoupper('Tháng', 'UTF-8') => 2,
                mb_strtoupper('Năm', 'UTF-8') => 3,
            ];
            $dataArray = [];
            $parent_id_one = NULL;
            $parent_id_two = NULL;
            $parent_id_three = NULL;
            foreach ($arraydata as $key => $value) {
                if (!empty($value[0])) {
                    $parent_id_one = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[0])), 'UTF-8');
                    $parent_id_two = NULL;
                    $parent_id_three = NULL;
                    $dataArray[$parent_id_one] = [
                        'name' => preg_replace('/\s+/', ' ', trim($value[0])),
                        'code' => preg_replace('/\s+/', ' ', trim($value[0])),
                        'type' => !empty($value[5]) ? $listType[mb_strtoupper(trim($value[5]), 'UTF-8')] : NULL,
                        'note' => trim($value[6])
                    ];
                } else if (empty($parent_id_one)) {
                    $errors .= '<div>Dòng [' . ($key + 1) . '] thêm hoặc cập nhật không được vì không tìm thấy loại đề xuất</div>';
                }

                if (!empty($value[1]) && !empty($parent_id_one)) {
                    $parent_id_two = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[1])), 'UTF-8');
                    $dataArray[$parent_id_one]['items'][$parent_id_two] = [
                        'code' => preg_replace('/\s+/', ' ', trim($value[1])),
                        'name' => preg_replace('/\s+/', ' ', trim($value[2])),
                        'type' => !empty($value[5]) ? $listType[mb_strtoupper(trim($value[5]), 'UTF-8')] : NULL,
                        'note' => trim($value[6])
                    ];
                } else if (!empty($value[1]) && empty($parent_id_one)) {
                    $errors .= '<div>Dòng [' . ($key + 1) . '] thêm hoặc cập nhật không được vì không tìm thấy mã nhóm đề xuất</div>';
                }

                if (!empty($value[7]) && !empty($parent_id_one)) {
                    $dataArray[$parent_id_one]['process'][] = [
                        'name' => preg_replace('/\s+/', ' ', trim($value[7])),
                        'roles' => preg_replace('/\s+/', ' ', trim($value[8])),
                        'bod' => !empty($value[9]) ? preg_replace('/\s+/', ' ', trim($value[9])) : 0,
                    ];
                }


                if (!empty($value[3]) && !empty($parent_id_two)) {
                    $parent_id_three = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[3])), 'UTF-8');
                    $dataArray[$parent_id_one]['items'][$parent_id_two]['items'][] = [
                        'code' => preg_replace('/\s+/', ' ', trim($value[3])),
                        'name' => preg_replace('/\s+/', ' ', trim($value[4])),
                        'type' => !empty($value[5]) ? $listType[mb_strtoupper(trim($value[5]), 'UTF-8')] : NULL,
                        'note' => trim($value[6])
                    ];
                } else if (!empty($value[3]) && !empty($parent_id_two)) {
                    $errors .= '<div>Dòng [' . ($key + 1) . '] thêm hoặc cập nhật không được vì không tìm thấy mã chi tiết</div>';
                }
            }

            $data_update_batch = [];
            foreach ($dataArray as $key => $value) {
                $this->db->where('code', $value['code']);
                $recommended_list = $this->db->get('tbl_recommended_list')->row();
                $id_parent = '';
                if (!empty($recommended_list)) {
                    $id_parent = $recommended_list->id;
                    $data_update_batch[] = [
                        'id' => $recommended_list->id,
                        'code' => $value['code'],
                        'type' => $value['type'],
                        'note' => $value['note']
                    ];
                } else {
                    $success = $this->db->insert('tbl_recommended_list', [
                        'parent_id' => 0,
                        'code' => $value['code'],
                        'name' => $value['name'],
                        'note' => $value['note'],
                        'type' => $value['type'],
                    ]);
                    if (!empty($success)) {
                        $id_parent = $this->db->insert_id();
                        $count++;
                    }
                }
                if (!empty($id_parent) && !empty($value['process'])) {
                    $checkbod = 0;
                    $checkerrors = 0;
                    foreach ($value['process'] as $kprocess => $vprocess) {
                        if ($vprocess['bod'] == 1) {
                            if ($checkbod == 1) {
                                $errors .= '<div>Đề xuất ' . $value['name'] . ' Quy trình chỉ có 1 được duyệt BOD</div>';
                                $checkerrors = 1;
                            }else{
                                $checkbod = 1;
                            }
                        }
                    }
                    if ($checkbod == 0) {
                        $errors .= '<div>Đề xuất ' . $value['name'] . ' Quy trình phải có 1 duyệt BOD</div>';
                        $checkerrors = 1;
                    }
                    if ($checkerrors == 0) {
                        foreach ($value['process'] as $kprocess => $vprocess) {
                            $role = get_table_where('tblroles', array('code_role' => $vprocess['roles']), '', 'row_array');
                            $success = $this->db->insert('tbl_recommended_list_process', [
                                'recommended_list_id' => $id_parent,
                                'name' => $vprocess['name'],
                                'roles' => $role['roleid'],
                                'bod' => $vprocess['bod'],
                            ]);
                            if (!empty($success)) {
                                $count++;
                            }
                        }
                    }
                }
                if (!empty($id_parent) && !empty($value['items'])) {
                    foreach ($value['items'] as $kone => $vone) {
                        $this->db->where('code', $vone['code']);
                        $recommended_list_one = $this->db->get('tbl_recommended_list')->row();
                        $id_parent_one = '';
                        if (!empty($recommended_list_one)) {
                            $id_parent_one = $recommended_list_one->id;

                            $data_update_batch[] = [
                                'id' => $recommended_list_one->id,
                                'parent_id' => $id_parent,
                                'code' => $vone['code'],
                                'name' => $vone['name'],
                                'type' => $vone['type'],
                                'note' => $vone['note']
                            ];
                        } else {
                            $success = $this->db->insert('tbl_recommended_list', [
                                'parent_id' => $id_parent,
                                'code' => $vone['code'],
                                'name' => $vone['name'],
                                'note' => $vone['note'],
                                'type' => $vone['type'],
                            ]);
                            if (!empty($success)) {
                                $id_parent_one = $this->db->insert_id();
                                $count++;
                            }
                        }


                        if (!empty($id_parent_one) && !empty($vone['items'])) {
                            foreach ($vone['items'] as $ktwo => $vtwo) {
                                $this->db->where('code', $vtwo['code']);
                                $recommended_list_two = $this->db->get('tbl_recommended_list')->row();
                                if (!empty($recommended_list_two)) {
                                    $data_update_batch[] = [
                                        'id' => $recommended_list_two->id,
                                        'parent_id' => $id_parent_one,
                                        'code' => $vtwo['code'],
                                        'name' => $vtwo['name'],
                                        'type' => $vtwo['type'],
                                        'note' => $vtwo['note']
                                    ];
                                } else {
                                    $this->db->insert('tbl_recommended_list', [
                                        'parent_id' => $id_parent_one,
                                        'code' => $vtwo['code'],
                                        'name' => $vtwo['name'],
                                        'note' => $vtwo['note'],
                                        'type' => $vtwo['type'],
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            $count_update = 0;
            if (!empty($data_update_batch)) {
                $this->db->update_batch('tbl_recommended_list', $data_update_batch, 'id');
                $count_update = $this->db->affected_rows();
            }
            echo json_encode(
                [
                    'success' => true,
                    'errors' => $errors,
                    'alert_type' => 'success',
                    'message' => 'Import Thêm mới thành công ' . $count . ' dòng và cập nhật ' . $count_update . ' dòng',
                ]
            );
            die();



            //            $errors = '';
            //            $rows = 1;
            //            foreach ($arraydata as $key => $row) {
            //                $rows++;
            //                $code_parent = trim($row[0]);
            //                $code = trim($row[1]);
            //                $name = trim($row[2]);
            //                $type = trim($row[3]);
            //                $note = trim($row[4]);
            //
            //                $type_id = 0;
            //                if (!empty($type)) {
            //                    if ($type != 'Ngày' && $type != 'Tháng' && $type != 'Năm') {
            //                        $errors.= '<div>Dòng ['.$rows.'] thêm không được vì không đúng loại</div>';
            //                        continue;
            //                    } else if ($type == 'Ngày') {
            //                        $type_id = 1;
            //                    } else if ($type == 'Tháng') {
            //                        $type_id = 2;
            //                    } else if ($type == 'Năm') {
            //                        $type_id = 3;
            //                    }
            //                }
            //
            //                $dtRecommended = $this->recommended_list_model->getRecommendedListByCode($code);
            //                if (!empty($dtRecommended)) {
            //                    $errors.= '<div>Dòng ['.$rows.'] thêm không được vì mã ['.$code.'] đã có trong phần mềm</div>';
            //                    continue;
            //                }
            //
            //                $parent_id = 0;
            //                if (!empty($code_parent)) {
            //                    $dtRecommended = $this->recommended_list_model->getRecommendedListByCode($code_parent);
            //                    if (empty($dtRecommended)) {
            //                        $errors.= '<div>Dòng ['.$rows.'] thêm không được vì mã đề xuất cha không có trong phần mềm</div>';
            //                        continue;
            //                    }
            //                    $parent_id = $dtRecommended['id'];
            //                }
            //
            //                $options = [
            //                    'parent_id' => $parent_id,
            //                    'code' => $code,
            //                    'name' => $name,
            //                    'note' => $note,
            //                    'type' => $type_id,
            //                ];
            //                $rs = $this->recommended_list_model->insertRecommendedList($options);
            //                if ($rs) {
            //                    $count++;
            //                }
            //            }
        }
        echo json_encode([
            'success' => true,
            'errors' => $errors,
            'alert_type' => 'success',
            'message' => 'Import thành công ' . $count . ' dòng',
        ]);
        die();
    }
}
