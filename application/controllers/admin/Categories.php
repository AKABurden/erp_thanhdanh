<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Categories extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('category_model');
		$this->load->model('personnel_model');
        $this->listTypeCategoryMachines = [
            'refrigeration' => 'Điện Lạnh',
            'electricitywater' => 'Điện Nước Gia Dụng',
            'camera' => 'Camera',
            'ctp' => 'Hệ Thống CTP',
            'wastewater' => 'Hệ Thống Nước Thải',
            'hardware' => 'Phần Cứng',
            'software' => 'Phần Mềm',
            'pccc' => 'Thiết Bị Phòng Cháy Chữa Cháy',
            'transportation' => 'Phương Tiện Vận Chuyển',
            'sever' => 'Sever',
            'laborsafety' => 'Thiết Bị An Toàn Lao Động',
            'testingequipment' => 'Thiết Bị Đo Kiểm',
            'office' => 'Thiết Bị Văn Phòng',
            'equipmentproductivity' => 'Năng Suất Thiết Bị',
            'other' => 'Thiết Bị Khác',
            'special' => 'Thiết Bị Đặc Biệt',
        ];
	}

	public function index()
	{
		//		if (!has_permission('categories', '', 'view')) {
		//			access_denied('categories');
		//		}
		$data['categories'] = [];
		$this->category_model->get_by_id(0, $data['categories']);
		$full_categories = $this->category_model->get_full_detail();
		$data['full_categories'] = $full_categories;
		$data['title'] = _l('ch_categories');
		$this->load->view('admin/categories/manage', $data);
	}

	public function get_exist($id = '')
	{
		$items = get_table_where('tblitems', array('category_id' => $id), '', 'row');
		if (!empty($items)) {
			echo json_encode(true);
			die;
		} else {
			$parent = get_table_where('tblcategories', array('category_parent' => $id), '', 'row');
			if (!empty($parent)) {
				echo json_encode(true);
				die;
			}
			$success = $this->db->delete('tblcategories', array('id' => $id));
			if ($success) {
				$success = true;
				$message = _l('ch_delete_successfuly', _l('ch_categories'));
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	public function table($value = '')
	{
		$this->app->get_table_data('categories_items');
	}

	public function delete_categories()
	{
		if ($this->input->post()) {
			//			if (!has_permission('categories', '', 'delete')) {
			//				echo json_encode(array(
			//					'success' => 'warning',
			//					'message' => _l('ch_no_delete')
			//				));
			//				die;
			//			}
			$message = '';
			$id = $this->category_model->delete_categories($this->input->post(NULL, FALSE));
			if ($id) {
				$success = true;
				$message = _l('ch_delete_successfuly', _l('ch_categories'));
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	public function add_category()
	{
		if ($this->input->post()) {
			//			if (!has_permission('categories', '', 'create')) {
			//				echo json_encode(array(
			//					'success' => true,
			//					'message' => _l('Bạn không có quyền thêm mới!')
			//				));
			//				die;
			//			}
			$message = '';
			$id = $this->category_model->add_category($this->input->post(NULL, FALSE));
			if ($id) {
				$success = true;
				$message = _l('ch_added_successfuly', _l('ch_categories'));
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	public function update_category($id = "")
	{
		if (!has_permission('categories', '', 'edit')) {
			//			echo json_encode(array(
			//				'success' => true,
			//				'message' => _l('Bạn không có quyền sửa!')
			//			));
			//			die;
		}
		if ($id != "") {
			$message = '';
			$alert_type = 'warning';
			if ($this->input->post()) {
				$success = $this->category_model->update_category($this->input->post(), $id);
				if ($success) {
					$message = _l('ch_updated_successfuly', _l('ch_categories'));
				};
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
		} else {
			if ($this->input->post()) {
				$success = $this->category_model->add_category($this->input->post());
				if ($success) {
					$alert_type = 'success';
					$message = _l('ch_added_successfuly', _l('ch_categories'));
				}
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		die;
	}

	public function capacity()
	{
		$data['tnh'] = true;
		$data['title'] = _l('tnh_categories_capacity');
		$this->load->view('admin/categories/capacity', $data);
	}

	public function add_capacity()
	{
		$data = [];
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_capacity.code]');
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$note = $this->input->post('note');
				$options = [
					'name' => $name,
					'code' => $code,
					'note' => $note,
				];
				$id = $this->category_model->insertCapacity($options);
				if ($id) {
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
		} else {
			$this->load->view('admin/categories/add_capacity', $data);
		}
	}

	public function edit_capacity($id)
	{
		$data = [];
		$capacity = $this->category_model->rowCapacity($id);
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ($capacity['code'] != $this->input->post('code')) {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_capacity.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$note = $this->input->post('note');
				$options = [
					'name' => $name,
					'code' => $code,
					'note' => $note,
				];
				$id = $this->category_model->updateCapacity($id, $options);
				if ($id) {
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
		} else {
			$data['capacity'] = $capacity;
			$this->load->view('admin/categories/edit_capacity', $data);
		}
	}

	function getCapacity()
	{
		$this->datatables->select("
            tbl_capacity.id as id,
            tbl_capacity.code as code,
            tbl_capacity.name as name,
            tbl_capacity.note as note,
            ", FALSE)
			->from('tbl_capacity');
		$this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/edit_capacity/$1"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/categories/delete_capacity/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
		$result = json_decode($this->datatables->generate());
		echo (json_encode($result));
	}

	function delete_capacity($id)
	{
		$data = [];
		if ($id) {
			// if (!$this->items_model->checkExistCategory($id)) {
			//     $data['result'] = 0;
			//     $data['message'] = lang('tnh_exist_not_delete');
			//     echo json_encode($data);
			//     return;
			// }
			// return;
			if ($this->category_model->deleteCapacity($id)) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function transportation_vehicles()
	{
		$data['tnh'] = true;
		$data['title'] = _l('tnh_categories_transportation_vehicles');
		$this->load->view('admin/categories/transportation_vehicles', $data);
	}

	public function add_transportation_vehicles()
	{
		$data = [];
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_transportation_vehicles.code]');
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$note = $this->input->post('note');
				$options = [
					'name' => $name,
					'code' => $code,
					'note' => $note,
				];
				$id = $this->category_model->insertTransportationVehicles($options);
				if ($id) {
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
		} else {
			$this->load->view('admin/categories/add_transportation_vehicles', $data);
		}
	}

	public function edit_transportation_vehicles($id)
	{
		$data = [];
		$vehicle = $this->category_model->rowTransportationVehicles($id);
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ($vehicle['code'] != $this->input->post('code')) {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_transportation_vehicles.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$note = $this->input->post('note');
				$options = [
					'name' => $name,
					'code' => $code,
					'note' => $note,
				];
				$id = $this->category_model->updateTransportationVehicles($id, $options);
				if ($id) {
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
		} else {
			$data['vehicle'] = $vehicle;
			$this->load->view('admin/categories/edit_transportation_vehicles', $data);
		}
	}

	function getTransportationVehicles()
	{
		$this->datatables->select("
            tbl_transportation_vehicles.id as id,
            tbl_transportation_vehicles.code as code,
            tbl_transportation_vehicles.name as name,
            tbl_transportation_vehicles.note as note,
            ", FALSE)
			->from('tbl_transportation_vehicles');
		$this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/edit_transportation_vehicles/$1"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/categories/delete_transportation_vehicles/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
		$result = json_decode($this->datatables->generate());
		echo (json_encode($result));
	}

	function delete_transportation_vehicles($id)
	{
		$data = [];
		if ($id) {
			if ($this->category_model->deleteTransportationVehicles($id)) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function machines($is_type = '')
	{
		$data['is_type'] = $is_type;
		$data['tnh'] = true;
        if(!empty($is_type)) {
            $this->db->where('is_type', $is_type);
        }
        $data['category'] = $this->db->get_where('tbl_category_machines')->result_array();
		$data['title'] = _l('tnh_categories_machines');
        if(!empty($is_type)) {
            $data['title'] .= '('.$this->listTypeCategoryMachines[$is_type].')';
        }
		$this->load->view('admin/categories/machines', $data);
	}

	public function add_machines($is_type = '')
	{
		$data = [];
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			$this->form_validation->set_rules('status', lang("status"), 'required');
			$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_machines.code]');
			if ($this->form_validation->run() == true) {
				$category_machine_id = $this->input->post('category_machine_id');
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$product_in_month = number_unformat($this->input->post('product_in_month'));
				$efficiency_coefficient = number_unformat($this->input->post('efficiency_coefficient'));
				$capacity_cycle = number_unformat($this->input->post('capacity_cycle'));
				$time_cycle = number_unformat($this->input->post('time_cycle'));
				$time_before_produce = number_unformat($this->input->post('time_before_produce'));
				$time_after_produce = number_unformat($this->input->post('time_after_produce'));
				$cost_hour = number_unformat($this->input->post('cost_hour'));
				$status = $this->input->post('status');
				$specifications = $this->input->post('specifications');
				$note = $this->input->post('note');
				$standard = $this->input->post('standard');
				$pp_measure = $this->input->post('pp_measure');
				$quota_productivity = number_unformat($this->input->post('quota_productivity'));
				$operating_gauge = $this->input->post('operating_gauge');
				$day_operation = @to_sql_date($this->input->post('day_operation'));
				$process = $this->input->post('process');
				$items_5s = $this->input->post('items_5s');
				$month = $this->input->post('month');
				$maintenance = $this->input->post('maintenance');
				$note_main = $this->input->post('note_main');
				$preparation_time = $this->input->post('preparation_time');
				$product_color = $this->input->post('product_color');
				$number_day_operation = $this->input->post('number_day_operation');
				$soup_ingredients = number_unformat($this->input->post('soup_ingredients'));
				$time_change_size = number_unformat($this->input->post('time_change_size'));
				$items_pccc = $this->input->post('items_pccc');
				$items_accreditation = $this->input->post('items_accreditation');
				$refrigeration = $this->input->post('refrigeration');
				$depreciation_rates = $this->input->post('depreciation_rates');
				$depreciation_period = $this->input->post('depreciation_period');
				$used_time = $this->input->post('used_time');
				$model = $this->input->post('model');
				$origin = $this->input->post('origin');
				$year_manu = $this->input->post('year_manu');
				$performance = $this->input->post('performance');
				$recording_technique = $this->input->post('recording_technique');
				$physical_characteristics = $this->input->post('physical_characteristics');
				$paper_size_max = $this->input->post('paper_size_max');
				$paper_size_min = $this->input->post('paper_size_min');
				$voltage = $this->input->post('voltage');
				$speed = $this->input->post('speed');

				$options = [
					'name' => $name,
					'code' => $code,
					'day_operation' => !empty($day_operation) ? $day_operation : NULL,
					'product_in_month' => $product_in_month,
					'efficiency_coefficient' => $efficiency_coefficient,
					'capacity_cycle' => $capacity_cycle,
					'time_cycle' => $time_cycle,
					'time_before_produce' => $time_before_produce,
					'time_after_produce' => $time_after_produce,
					'cost_hour' => $cost_hour,
					'status' => $status,
					'specifications' => $specifications,
					'note' => $note,
					'standard' => $standard,
					'pp_measure' => $pp_measure,
					'quota_productivity' => $quota_productivity,
					'operating_gauge' => $operating_gauge,
					'preparation_time' => !empty($preparation_time) ? number_format_data($preparation_time, false) : 0,
					'product_color' => !empty($product_color) ? number_format_data($product_color, false) : NULL,
					'number_day_operation' => !empty($number_day_operation) ? number_format_data($number_day_operation, false) : NULL,
					'soup_ingredients' => $soup_ingredients,
					'time_change_size' => $time_change_size,
					'category_machine_id' => $category_machine_id,
					'depreciation_rates' => $depreciation_rates,
					'depreciation_period' => $depreciation_period,
					'used_time' => $used_time,
					'model' => $model,
					'origin' => $origin,
					'year_manu' => $year_manu,
					'performance' => $performance,
					'recording_technique' => $recording_technique,
					'physical_characteristics' => $physical_characteristics,
					'paper_size_max' => $paper_size_max,
					'paper_size_min' => $paper_size_min,
					'voltage' => $voltage,
					'speed' => $speed,
				];
				$id = $this->category_model->insertMachines($options);
				if ($id) {
					if (!empty($process)) {
						$arrProcess = [];
						foreach ($process as $key => $value) {
							$insert_items = $this->db->insert('tbl_machines_process', [
								'machines_id' => $id,
								'process' => $value,
							]);
							if (!empty($insert_items)) {
								$id_items = $this->db->insert_id();
								if (!empty($_FILES['file']['name'][$key])) {
									if (!file_exists(FCPATH . 'uploads/machines/')) {
										mkdir(FCPATH . 'uploads/machines/');
										fopen(FCPATH . 'uploads/machines/', 'w');
									}
									if (!file_exists(FCPATH . 'uploads/machines/' . $id_items . '/')) {
										mkdir(FCPATH . 'uploads/machines/' . $id_items . '/');
										fopen(FCPATH . 'uploads/machines/' . $id_items . '/index.html', 'w');
									}
									$filename = $_FILES['file']['name'][$key] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name'][$key]));
									if (is_uploaded_file($_FILES['file']['tmp_name'][$key])) {
										$typeFile = $_FILES['file']['type'][$key];
										$source_path = $_FILES['file']['tmp_name'][$key];
										$target_path = FCPATH . 'uploads/machines/' . $id_items . '/' . $_FILES['file']['name'][$key];
										if (move_uploaded_file($source_path, $target_path)) {
											$this->db->insert('tblfiles', [
												'rel_id' => $id_items,
												'rel_type' => 'rel_process',
												'file_name' => $filename,
												'filetype' => $typeFile,
												'staffid' => get_staff_user_id(),
												'dateadded' => date('Y-m-d H:i:s'),
											]);
										}
									}
								}
							}
						}
						//                        $this->category_model->insertBatchMachinesProcess($arrProcess);
					}
					if (!empty($items_5s)) {
						foreach ($items_5s as $key => $value) {
							$insert_items_5s = $this->db->insert('tbl_machines_5s', [
								'machines_id' => $id,
								'name' => $value['name'],
								'ballot_type' => 0,
								'note' => !empty($value['note']) ? $value['note'] : '',
							]);
							if (!empty($insert_items_5s)) {
								$id_items = $this->db->insert_id();
								if (!empty($_FILES['file_5s']['name'][$key])) {
									if (!file_exists(FCPATH . 'uploads/machines_fives/')) {
										mkdir(FCPATH . 'uploads/machines_fives/');
										fopen(FCPATH . 'uploads/machines_fives/', 'w');
									}
									if (!file_exists(FCPATH . 'uploads/machines_fives/' . $id_items . '/')) {
										mkdir(FCPATH . 'uploads/machines_fives/' . $id_items . '/');
										fopen(FCPATH . 'uploads/machines_fives/' . $id_items . '/index.html', 'w');
									}
									$filename = $_FILES['file_5s']['name'][$key] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file_5s']['name'][$key]));
									if (is_uploaded_file($_FILES['file_5s']['tmp_name'][$key])) {
										$typeFile = $_FILES['file_5s']['type'][$key];
										$source_path = $_FILES['file_5s']['tmp_name'][$key];
										$target_path = FCPATH . 'uploads/machines_fives/' . $id_items . '/' . $_FILES['file_5s']['name'][$key];
										if (move_uploaded_file($source_path, $target_path)) {
											$this->db->where('id', $id_items);
											$this->db->update('tbl_machines_5s', [
												'img' => 'uploads/machines_fives/' . $id_items . '/' . $filename
											]);
										}
									}
								}
							}
						}
						//                        $this->category_model->insertBatchMachinesProcess($arrProcess);
					}
					// pccc
					if (!empty($items_pccc)) {
						foreach ($items_pccc as $key => $value) {
							$insert_items_pccc = $this->db->insert('tbl_machines_5s', [
								'machines_id' => $id,
								'name' => $value['name'],
								'ballot_type' => 1,
								'note' => !empty($value['note']) ? $value['note'] : '',
							]);
							if (!empty($insert_items_pccc)) {
								$id_items = $this->db->insert_id();
								if (!empty($_FILES['file_pccc']['name'][$key])) {
									if (!file_exists(FCPATH . 'uploads/machines_fives/')) {
										mkdir(FCPATH . 'uploads/machines_fives/');
										fopen(FCPATH . 'uploads/machines_fives/', 'w');
									}
									if (!file_exists(FCPATH . 'uploads/machines_fives/' . $id_items . '/')) {
										mkdir(FCPATH . 'uploads/machines_fives/' . $id_items . '/');
										fopen(FCPATH . 'uploads/machines_fives/' . $id_items . '/index.html', 'w');
									}
									$filename = $_FILES['file_pccc']['name'][$key] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file_pccc']['name'][$key]));
									if (is_uploaded_file($_FILES['file_pccc']['tmp_name'][$key])) {
										$typeFile = $_FILES['file_pccc']['type'][$key];
										$source_path = $_FILES['file_pccc']['tmp_name'][$key];
										$target_path = FCPATH . 'uploads/machines_fives/' . $id_items . '/' . $_FILES['file_pccc']['name'][$key];
										if (move_uploaded_file($source_path, $target_path)) {
											$this->db->where('id', $id_items);
											$this->db->update('tbl_machines_5s', [
												'img' => 'uploads/machines_fives/' . $id_items . '/' . $filename
											]);
										}
									}
								}
							}
						}
						//                        $this->category_model->insertBatchMachinesProcess($arrProcess);
					}
					if (!empty($items_accreditation)) {
						foreach ($items_accreditation as $key => $value) {
							$insert_items_accreditation = $this->db->insert('tbl_machines_5s', [
								'machines_id' => $id,
								'name' => $value['name'],
								'ballot_type' => 2,
								'note' => !empty($value['note']) ? $value['note'] : '',
							]);
							if (!empty($insert_items_accreditation)) {
								$id_items = $this->db->insert_id();
								if (!empty($_FILES['file_accreditation']['name'][$key])) {
									if (!file_exists(FCPATH . 'uploads/machines_fives/')) {
										mkdir(FCPATH . 'uploads/machines_fives/');
										fopen(FCPATH . 'uploads/machines_fives/', 'w');
									}
									if (!file_exists(FCPATH . 'uploads/machines_fives/' . $id_items . '/')) {
										mkdir(FCPATH . 'uploads/machines_fives/' . $id_items . '/');
										fopen(FCPATH . 'uploads/machines_fives/' . $id_items . '/index.html', 'w');
									}
									$filename = $_FILES['file_accreditation']['name'][$key] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file_accreditation']['name'][$key]));
									if (is_uploaded_file($_FILES['file_accreditation']['tmp_name'][$key])) {
										$typeFile = $_FILES['file_accreditation']['type'][$key];
										$source_path = $_FILES['file_accreditation']['tmp_name'][$key];
										$target_path = FCPATH . 'uploads/machines_fives/' . $id_items . '/' . $_FILES['file_accreditation']['name'][$key];
										if (move_uploaded_file($source_path, $target_path)) {
											$this->db->where('id', $id_items);
											$this->db->update('tbl_machines_5s', [
												'img' => 'uploads/machines_fives/' . $id_items . '/' . $filename
											]);
										}
									}
								}
							}
						}
					}
					if (!empty($refrigeration)) {
						foreach ($refrigeration as $key => $value) {
							$insert_refrigeration = $this->db->insert('tbl_machines_maintenance_h', [
								'machines_id' => $id,
								'name' => $value['name'],
								'type' => 'refrigeration'
							]);
							if (!empty($insert_refrigeration)) {
								$id_items = $this->db->insert_id();
							}
						}
					}
					if (!empty($month) && !empty($maintenance)) {
						foreach ($maintenance as $key => $value) {
							$this->db->insert('tbl_machines_maintenance', [
								'machines_id' => $id,
								'name' => $value,
								'month' => $month[$key],
								'note_main' => $note_main[$key]
							]);
							$id_maintenance_detail = $this->db->insert_id();
							if (!empty($_FILES['file_main']['name'][$key])) {
								if (!file_exists(FCPATH . 'uploads/machines_maintenance/')) {
									mkdir(FCPATH . 'uploads/machines_maintenance/');
									fopen(FCPATH . 'uploads/machines_maintenance/index.html', 'w');
								}
								if (!file_exists(FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/')) {
									mkdir(FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/');
									fopen(FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/index.html', 'w');
								}
								foreach ($_FILES['file_main']['name'][$key] as $k => $v) {
									$filename = $_FILES['file_main']['name'][$key][$k] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file_main']['name'][$key][$k]));
									if (is_uploaded_file($_FILES['file_main']['tmp_name'][$key][$k])) {
										$typeFile = $_FILES['file_main']['type'][$key][$k];
										$source_path = $_FILES['file_main']['tmp_name'][$key][$k];
										$target_path = FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/' . $_FILES['file_main']['name'][$key][$k];
										if (move_uploaded_file($source_path, $target_path)) {
											$this->db->insert('tblfiles', [
												'rel_id' => $id_maintenance_detail,
												'rel_type' => 'rel_main',
												'file_name' => $filename,
												'filetype' => $typeFile,
												'staffid' => get_staff_user_id(),
												'dateadded' => date('Y-m-d H:i:s'),
											]);
										}
									}
								}
							}
						}
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
        else {
			$data['standard'] = $this->db->get('tbl_packaging')->result_array();
            $data['title'] = 'Thêm Thiết Bị';
            if(!empty($is_type)) {
                $data['title'] .= '('.$this->listTypeCategoryMachines[$is_type].')';
                $this->db->where('is_type', $is_type);
            }
			$data['dtCategoryMachines'] = $this->db->get('tbl_category_machines')->result_array();
			$this->load->view('admin/categories/add_machines', $data);
		}
	}

	public function edit_machines($id, $is_type = '')
	{

		$data = [];
		$machines = $this->category_model->rowMachines($id);
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ($machines['code'] != $this->input->post('code')) {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_machines.code]');
			}
			if ($this->form_validation->run() == true) {
				$category_machine_id = $this->input->post('category_machine_id');
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$day_operation = @to_sql_date($this->input->post('day_operation'));
				$product_in_month = number_unformat($this->input->post('product_in_month'));
				$efficiency_coefficient = number_unformat($this->input->post('efficiency_coefficient'));
				$capacity_cycle = number_unformat($this->input->post('capacity_cycle'));
				$time_cycle = number_unformat($this->input->post('time_cycle'));
				$time_before_produce = number_unformat($this->input->post('time_before_produce'));
				$time_after_produce = number_unformat($this->input->post('time_after_produce'));
				$cost_hour = number_unformat($this->input->post('cost_hour'));
				$status = $this->input->post('status');
				$specifications = $this->input->post('specifications');
				$note = $this->input->post('note');
				$standard = $this->input->post('standard');
				$pp_measure = $this->input->post('pp_measure');
				$quota_productivity = number_unformat($this->input->post('quota_productivity'));
				$operating_gauge = $this->input->post('operating_gauge');
				$process = $this->input->post('process');
				$id_maintenance = $this->input->post('id_maintenance');
				$maintenance = $this->input->post('maintenance');
				$note_main = $this->input->post('note_main');
				$month = $this->input->post('month');
				$preparation_time = $this->input->post('preparation_time');
				$product_color = $this->input->post('product_color');
				$number_day_operation = $this->input->post('number_day_operation');
				$soup_ingredients = number_unformat($this->input->post('soup_ingredients'));
				$time_change_size = number_unformat($this->input->post('time_change_size'));
				$items_5s = $this->input->post('items_5s');
				$items_pccc = $this->input->post('items_pccc');
				$items_accreditation = $this->input->post('items_accreditation');
				$refrigeration = $this->input->post('refrigeration');

                $depreciation_rates = $this->input->post('depreciation_rates');
                $depreciation_period = $this->input->post('depreciation_period');
                $used_time = $this->input->post('used_time');

                $model = $this->input->post('model');
                $origin = $this->input->post('origin');
                $year_manu = $this->input->post('year_manu');
                $performance = $this->input->post('performance');
                $recording_technique = $this->input->post('recording_technique');
                $physical_characteristics = $this->input->post('physical_characteristics');
                $paper_size_max = $this->input->post('paper_size_max');
                $paper_size_min = $this->input->post('paper_size_min');
                $voltage = $this->input->post('voltage');
                $speed = $this->input->post('speed');

				$options = [
					'name' => $name,
					'code' => $code,
					'day_operation' => !empty($day_operation) ? $day_operation : NULL,
					'product_in_month' => $product_in_month,
					'efficiency_coefficient' => $efficiency_coefficient,
					'capacity_cycle' => $capacity_cycle,
					'time_cycle' => $time_cycle,
					'time_before_produce' => $time_before_produce,
					'time_after_produce' => $time_after_produce,
					'cost_hour' => $cost_hour,
					'status' => $status,
					'specifications' => $specifications,
					'note' => $note,
					'standard' => $standard,
					'pp_measure' => $pp_measure,
					'quota_productivity' => $quota_productivity,
					'operating_gauge' => $operating_gauge,
					'preparation_time' => !empty($preparation_time) ? number_format_data($preparation_time, false) : 0,
					'product_color' => !empty($product_color) ? number_format_data($product_color, false) : NULL,
					'number_day_operation' => !empty($number_day_operation) ? number_format_data($number_day_operation, false) : NULL,
					'soup_ingredients' => $soup_ingredients,
					'time_change_size' => $time_change_size,
					'category_machine_id' => $category_machine_id,
					'depreciation_rates' => $depreciation_rates,
					'depreciation_period' => $depreciation_period,
					'used_time' => $used_time,
                    'model' => $model,
                    'origin' => $origin,
                    'year_manu' => $year_manu,
                    'performance' => $performance,
                    'recording_technique' => $recording_technique,
                    'physical_characteristics' => $physical_characteristics,
                    'paper_size_max' => $paper_size_max,
                    'paper_size_min' => $paper_size_min,
                    'voltage' => $voltage,
                    'speed' => $speed,
				];
                $options['date_update'] = date('Y-m-d H:i:s');
				$category_id = $id;
				$success = $this->category_model->updateMachines($id, $options);
				if ($success) {
					//                    $this->category_model->deleteMachinesProcess($category_id);
					$arrayNotDelete = [];
					if (!empty($process)) {
						$arrProcess = [];
						foreach ($process as $key => $value) {
							$machines_process_id = !empty($this->input->post('machines_process_id')[$key]) ? $this->input->post('machines_process_id')[$key] : 0;
							//                            $arrProcess[] = [
							//                                'id' => $machines_process_id,
							//                                'machines_id' => $category_id,
							//                                'process' => $value,
							//                            ];
							if (!file_exists(FCPATH . 'uploads/machines/')) {
								mkdir(FCPATH . 'uploads/machines/');
								fopen(FCPATH . 'uploads/machines/index.html', 'w');
							}
							if (!empty($machines_process_id)) {
								$this->db->where('id', $machines_process_id);
								$this->db->update('tbl_machines_process', ['process' => $value]);
								$arrayNotDelete[] = $machines_process_id;
								if (!empty($_FILES['file']['name'][$key])) {
									if (!file_exists(FCPATH . 'uploads/machines/' . $machines_process_id . '/')) {
										mkdir(FCPATH . 'uploads/machines/' . $machines_process_id . '/');
										fopen(FCPATH . 'uploads/machines/' . $machines_process_id . '/index.html', 'w');
									}
									$filename = $_FILES['file']['name'][$key] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name'][$key]));
									if (is_uploaded_file($_FILES['file']['tmp_name'][$key])) {
										$typeFile = $_FILES['file']['type'][$key];
										$source_path = $_FILES['file']['tmp_name'][$key];
										$target_path = FCPATH . 'uploads/machines/' . $machines_process_id . '/' . $_FILES['file']['name'][$key];
										if (move_uploaded_file($source_path, $target_path)) {
											$this->db->insert('tblfiles', [
												'rel_id' => $machines_process_id,
												'rel_type' => 'rel_process',
												'file_name' => $filename,
												'filetype' => $typeFile,
												'staffid' => get_staff_user_id(),
												'dateadded' => date('Y-m-d H:i:s'),
											]);
										}
									}
								}
							} else {
								//								$this->db->where('id', $machines_process_id);
								$insert_items = $this->db->insert('tbl_machines_process', [
									'machines_id' => $category_id,
									'process' => $value,
								]);
								if (!empty($insert_items)) {
									$id_items = $this->db->insert_id();
									$arrayNotDelete[] = $id_items;
									if (!empty($_FILES['file']['name'][$key])) {
										if (!file_exists(FCPATH . 'uploads/machines/' . $id_items . '/')) {
											mkdir(FCPATH . 'uploads/machines/' . $id_items . '/');
											fopen(FCPATH . 'uploads/machines/' . $id_items . '/index.html', 'w');
										}
										$filename = $_FILES['file']['name'][$key] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name'][$key]));
										if (is_uploaded_file($_FILES['file']['tmp_name'][$key])) {
											$typeFile = $_FILES['file']['type'][$key];
											$source_path = $_FILES['file']['tmp_name'][$key];
											$target_path = FCPATH . 'uploads/machines/' . $id_items . '/' . $_FILES['file']['name'][$key];
											if (move_uploaded_file($source_path, $target_path)) {
												$this->db->insert('tblfiles', [
													'rel_id' => $id_items,
													'rel_type' => 'rel_process',
													'file_name' => $filename,
													'filetype' => $typeFile,
													'staffid' => get_staff_user_id(),
													'dateadded' => date('Y-m-d H:i:s'),
												]);
											}
										}
									}
								}
							}
						}
						//                        $this->category_model->insertBatchMachinesProcess($arrProcess);
					}
					if (!empty($arrayNotDelete)) {
						$this->db->where_not_in('id', $arrayNotDelete);
					}
					$this->db->where('machines_id', $category_id);
					$machines_process_delete = $this->db->get('tbl_machines_process')->result_array();
					if (!empty($machines_process_delete)) {
						foreach ($machines_process_delete as $key => $value) {
							$this->db->where('tblfiles.rel_id', $value['id']);
							$this->db->where('tblfiles.rel_type', 'rel_process');
							$files = $this->db->get('tblfiles')->row();
							if (!empty($files)) {
								$link = FCPATH . 'uploads/machines/' . $files->id . '/' . $files->file_name;
								@unlink($link);
								$this->db->where('id', $files->id);
								$this->db->delete('tblfiles');
							}
						}
						if (!empty($arrayNotDelete)) {
							$this->db->where_not_in('id', $arrayNotDelete);
						}
						$this->db->where('machines_id', $category_id);
						$this->db->delete('tbl_machines_process');
					}
					$arrayMaintenanceNotDelete = [];

					$arrItems_5s = [];
					if (!empty($items_5s)) {
						$arrayNotDelete5s = [];

						foreach ($items_5s as $key => $value) {
							$id_items = '';
							if (!empty($value['id'])) {
								$this->db->where('id', $value['id']);
								$this->db->where('machines_id', $id);
								$insert_items_5s = $this->db->update('tbl_machines_5s', [
									'name' => $value['name'],
									'note' => !empty($value['note']) ? $value['note'] : '',
								]);
								if (!empty($insert_items_5s)) {
									$id_items = $value['id'];
								}
							} else {
								$insert_items_5s = $this->db->insert('tbl_machines_5s', [
									'machines_id' => $id,
									'name' => $value['name'],
									'ballot_type' => 0,
									'note' => !empty($value['note']) ? $value['note'] : '',
								]);
								if (!empty($insert_items_5s)) {
									$id_items = $this->db->insert_id();
								}
							}
							if (!empty($id_items)) {
								$arrItems_5s[] = $id_items;
								if (!empty($_FILES['file_5s']['name'][$key])) {
									if (!file_exists(FCPATH . 'uploads/machines_fives/')) {
										mkdir(FCPATH . 'uploads/machines_fives/');
										fopen(FCPATH . 'uploads/machines_fives/', 'w');
									}
									if (!file_exists(FCPATH . 'uploads/machines_fives/' . $id_items . '/')) {
										mkdir(FCPATH . 'uploads/machines_fives/' . $id_items . '/');
										fopen(FCPATH . 'uploads/machines_fives/' . $id_items . '/index.html', 'w');
									}
									$filename = $_FILES['file_5s']['name'][$key] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file_5s']['name'][$key]));
									if (is_uploaded_file($_FILES['file_5s']['tmp_name'][$key])) {
										$typeFile = $_FILES['file_5s']['type'][$key];
										$source_path = $_FILES['file_5s']['tmp_name'][$key];
										$target_path = FCPATH . 'uploads/machines_fives/' . $id_items . '/' . $_FILES['file_5s']['name'][$key];
										if (move_uploaded_file($source_path, $target_path)) {
											$this->db->where('id', $id_items);
											$this->db->update('tbl_machines_5s', ['img' => 'uploads/machines_fives/' . $id_items . '/' . $filename]);
										}
									}
								}
							}
						}

						$this->db->select('GROUP_CONCAT(id) as list_id');
						if (!empty($arrItems_5s)) {
							$this->db->where_not_in('id', $arrItems_5s);
						}
						$this->db->where('machines_id', $id);
						$this->db->where('img is not null', false, false);
						$machines_5s_file = $this->db->get('tbl_machines_5s')->result_array();
						if (!empty($machines_5s_file)) {
							foreach ($machines_5s_file as $key => $value) {
								if (!empty($value['img']) && file_exists($value['img'])) {
									$Unlink = FCPATH . $value['img'];
									@unlink($Unlink);
								}
							}
						}
					}
					if (!empty($arrItems_5s)) {
						$this->db->where_not_in('id', $arrItems_5s);
					}
					$this->db->where('machines_id', $id);
					$this->db->where('ballot_type', 0);
					$this->db->delete('tbl_machines_5s');


					if (!empty($refrigeration)) {
						$arrmachines_maintenance = [];
						foreach ($refrigeration as $key => $value) {
							$id_items = '';
							if (!empty($value['id'])) {
								$this->db->where('id', $value['id']);
								$this->db->where('machines_id', $id);
								$insert_refrigeration = $this->db->update('tbl_machines_maintenance_h', [
									'name' => $value['name'],
									'type' => 'refrigeration'
								]);
								if (!empty($insert_refrigeration)) {
									$id_items = $value['id'];
								}
							} else {
								$insert_refrigeration = $this->db->insert('tbl_machines_maintenance_h', [
									'machines_id' => $id,
									'name' => $value['name'],
									'type' => 'refrigeration'
								]);
								if (!empty($insert_refrigeration)) {
									$id_items = $this->db->insert_id();
								}
							}
							if (!empty($id_items)) {
								$arrmachines_maintenance[] = $id_items;
							}
						}
					}
					if (!empty($arrmachines_maintenance)) {
						$this->db->where_not_in('id', $arrmachines_maintenance);
					}
					$this->db->where('machines_id', $id);
					$this->db->where('type', 'refrigeration');
					$this->db->delete('tbl_machines_maintenance_h');
					//pccc
					$arrItems_pccc = [];
					if (!empty($items_pccc)) {
						$arrayNotDeletepccc = [];

						foreach ($items_pccc as $key => $value) {
							$id_items = '';
							if (!empty($value['id'])) {
								$this->db->where('id', $value['id']);
								$this->db->where('machines_id', $id);
								$insert_items_pccc = $this->db->update('tbl_machines_5s', [
									'name' => $value['name'],
									'note' => !empty($value['note']) ? $value['note'] : '',
								]);
								if (!empty($insert_items_pccc)) {
									$id_items = $value['id'];
								}
							} else {
								$insert_items_pccc = $this->db->insert('tbl_machines_5s', [
									'machines_id' => $id,
									'ballot_type' => 1,
									'name' => $value['name'],
									'note' => !empty($value['note']) ? $value['note'] : '',
								]);
								if (!empty($insert_items_pccc)) {
									$id_items = $this->db->insert_id();
								}
							}
							if (!empty($id_items)) {
								$arrItems_pccc[] = $id_items;
								if (!empty($_FILES['file_pccc']['name'][$key])) {
									if (!file_exists(FCPATH . 'uploads/machines_fives/')) {
										mkdir(FCPATH . 'uploads/machines_fives/');
										fopen(FCPATH . 'uploads/machines_fives/', 'w');
									}
									if (!file_exists(FCPATH . 'uploads/machines_fives/' . $id_items . '/')) {
										mkdir(FCPATH . 'uploads/machines_fives/' . $id_items . '/');
										fopen(FCPATH . 'uploads/machines_fives/' . $id_items . '/index.html', 'w');
									}
									$filename = $_FILES['file_pccc']['name'][$key] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file_pccc']['name'][$key]));
									if (is_uploaded_file($_FILES['file_pccc']['tmp_name'][$key])) {
										$typeFile = $_FILES['file_pccc']['type'][$key];
										$source_path = $_FILES['file_pccc']['tmp_name'][$key];
										$target_path = FCPATH . 'uploads/machines_fives/' . $id_items . '/' . $_FILES['file_pccc']['name'][$key];
										if (move_uploaded_file($source_path, $target_path)) {
											$this->db->where('id', $id_items);
											$this->db->update('tbl_machines_5s', ['img' => 'uploads/machines_fives/' . $id_items . '/' . $filename]);
										}
									}
								}
							}
						}

						$this->db->select('GROUP_CONCAT(id) as list_id');
						if (!empty($arrItems_pccc)) {
							$this->db->where_not_in('id', $arrItems_pccc);
						}
						$this->db->where('machines_id', $id);
						$this->db->where('img is not null', false, false);
						$machines_5s_file = $this->db->get('tbl_machines_5s')->result_array();
						if (!empty($machines_5s_file)) {
							foreach ($machines_5s_file as $key => $value) {
								if (!empty($value['img']) && file_exists($value['img'])) {
									$Unlink = FCPATH . $value['img'];
									@unlink($Unlink);
								}
							}
						}
					}
					if (!empty($arrItems_pccc)) {
						$this->db->where_not_in('id', $arrItems_pccc);
					}
					$this->db->where('machines_id', $id);
					$this->db->where('ballot_type', 1);
					$this->db->delete('tbl_machines_5s');
					///
					//test
					$arrItems_accreditation = [];
					if (!empty($items_accreditation)) {
						$arrayNotDeleteaccreditation = [];

						foreach ($items_accreditation as $key => $value) {
							$id_items = '';
							if (!empty($value['id'])) {
								$this->db->where('id', $value['id']);
								$this->db->where('machines_id', $id);
								$insert_items_accreditation = $this->db->update('tbl_machines_5s', [
									'name' => $value['name'],
									'note' => !empty($value['note']) ? $value['note'] : '',
								]);
								if (!empty($insert_items_accreditation)) {
									$id_items = $value['id'];
								}
							} else {
								$insert_items_accreditation = $this->db->insert('tbl_machines_5s', [
									'machines_id' => $id,
									'ballot_type' => 2,
									'name' => $value['name'],
									'note' => !empty($value['note']) ? $value['note'] : '',
								]);
								if (!empty($insert_items_accreditation)) {
									$id_items = $this->db->insert_id();
								}
							}
							if (!empty($id_items)) {
								$arrItems_accreditation[] = $id_items;
								if (!empty($_FILES['file_accreditation']['name'][$key])) {
									if (!file_exists(FCPATH . 'uploads/machines_fives/')) {
										mkdir(FCPATH . 'uploads/machines_fives/');
										fopen(FCPATH . 'uploads/machines_fives/', 'w');
									}
									if (!file_exists(FCPATH . 'uploads/machines_fives/' . $id_items . '/')) {
										mkdir(FCPATH . 'uploads/machines_fives/' . $id_items . '/');
										fopen(FCPATH . 'uploads/machines_fives/' . $id_items . '/index.html', 'w');
									}
									$filename = $_FILES['file_accreditation']['name'][$key] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file_accreditation']['name'][$key]));
									if (is_uploaded_file($_FILES['file_accreditation']['tmp_name'][$key])) {
										$typeFile = $_FILES['file_accreditation']['type'][$key];
										$source_path = $_FILES['file_accreditation']['tmp_name'][$key];
										$target_path = FCPATH . 'uploads/machines_fives/' . $id_items . '/' . $_FILES['file_accreditation']['name'][$key];
										if (move_uploaded_file($source_path, $target_path)) {
											$this->db->where('id', $id_items);
											$this->db->update('tbl_machines_5s', ['img' => 'uploads/machines_fives/' . $id_items . '/' . $filename]);
										}
									}
								}
							}
						}

						$this->db->select('GROUP_CONCAT(id) as list_id');
						if (!empty($arrItems_accreditation)) {
							$this->db->where_not_in('id', $arrItems_accreditation);
						}
						$this->db->where('machines_id', $id);
						$this->db->where('img is not null', false, false);
						$machines_5s_file = $this->db->get('tbl_machines_5s')->result_array();
						if (!empty($machines_5s_file)) {
							foreach ($machines_5s_file as $key => $value) {
								if (!empty($value['img']) && file_exists($value['img'])) {
									$Unlink = FCPATH . $value['img'];
									@unlink($Unlink);
								}
							}
						}
					}
					if (!empty($arrItems_accreditation)) {
						$this->db->where_not_in('id', $arrItems_accreditation);
					}
					$this->db->where('machines_id', $id);
					$this->db->where('ballot_type', 2);
					$this->db->delete('tbl_machines_5s');
					///
					if (!empty($month) && !empty($maintenance)) {
						foreach ($maintenance as $key => $value) {
							if (empty($id_maintenance[$key])) {
								$successInsert = $this->db->insert('tbl_machines_maintenance', [
									'machines_id' => $category_id,
									'name' => $value,
									'month' => $month[$key],
									'note_main' => $note_main[$key]
								]);
								if (!empty($successInsert)) {
									$id_maintenance_detail = $this->db->insert_id();
									$arrayMaintenanceNotDelete[] = $id_maintenance_detail;
									if (!empty($_FILES['file_main']['name'][$key])) {
										if (!file_exists(FCPATH . 'uploads/machines_maintenance/')) {
											mkdir(FCPATH . 'uploads/machines_maintenance/');
											fopen(FCPATH . 'uploads/machines_maintenance/index.html', 'w');
										}
										if (!file_exists(FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/')) {
											mkdir(FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/');
											fopen(FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/index.html', 'w');
										}
										foreach ($_FILES['file_main']['name'][$key] as $k => $v) {
											$filename = $_FILES['file_main']['name'][$key][$k] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file_main']['name'][$key][$k]));
											if (is_uploaded_file($_FILES['file_main']['tmp_name'][$key][$k])) {
												$typeFile = $_FILES['file_main']['type'][$key][$k];
												$source_path = $_FILES['file_main']['tmp_name'][$key][$k];
												$target_path = FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/' . $_FILES['file_main']['name'][$key][$k];
												if (move_uploaded_file($source_path, $target_path)) {
													$this->db->insert('tblfiles', [
														'rel_id' => $id_maintenance_detail,
														'rel_type' => 'rel_main',
														'file_name' => $filename,
														'filetype' => $typeFile,
														'staffid' => get_staff_user_id(),
														'dateadded' => date('Y-m-d H:i:s'),
													]);
												}
											}
										}
									}
								}
							} else {
								$this->db->where('id', $id_maintenance[$key]);
								$successUpdate = $this->db->update('tbl_machines_maintenance', [
									'name' => $value,
									'month' => $month[$key],
									'note_main' => $note_main[$key]
								]);
								if (!empty($successUpdate)) {
									$arrayMaintenanceNotDelete[] = $id_maintenance[$key];
									$id_maintenance_detail = $id_maintenance[$key];
									if (!empty($_FILES['file_main']['name'][$key])) {
										if (!file_exists(FCPATH . 'uploads/machines_maintenance/')) {
											mkdir(FCPATH . 'uploads/machines_maintenance/');
											fopen(FCPATH . 'uploads/machines_maintenance/index.html', 'w');
										}
										if (!file_exists(FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/')) {
											mkdir(FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/');
											fopen(FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/index.html', 'w');
										}
										foreach ($_FILES['file_main']['name'][$key] as $k => $v) {
											$filename = $_FILES['file_main']['name'][$key][$k] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file_main']['name'][$key][$k]));
											if (is_uploaded_file($_FILES['file_main']['tmp_name'][$key][$k])) {
												$typeFile = $_FILES['file_main']['type'][$key][$k];
												$source_path = $_FILES['file_main']['tmp_name'][$key][$k];
												$target_path = FCPATH . 'uploads/machines_maintenance/' . $id_maintenance_detail . '/' . $_FILES['file_main']['name'][$key][$k];
												if (move_uploaded_file($source_path, $target_path)) {
													$this->db->insert('tblfiles', [
														'rel_id' => $id_maintenance_detail,
														'rel_type' => 'rel_main',
														'file_name' => $filename,
														'filetype' => $typeFile,
														'staffid' => get_staff_user_id(),
														'dateadded' => date('Y-m-d H:i:s'),
													]);
												}
											}
										}
									}
								}
							}
						}
					}

					if (!empty($arrayMaintenanceNotDelete)) {
						$this->db->where_not_in('id', $arrayMaintenanceNotDelete);
					}
					$this->db->where('machines_id', $category_id);
					$machines_maintenance_delete = $this->db->get('tbl_machines_maintenance')->result_array();
					if (!empty($machines_maintenance_delete)) {
						foreach ($machines_maintenance_delete as $key => $value) {
							$this->db->where('tblfiles.rel_id', $value['id']);
							$this->db->where('tblfiles.rel_type', 'rel_main');
							$files = $this->db->get('tblfiles')->row();
							if (!empty($files)) {
								$link = FCPATH . 'uploads/machines_maintenance/' . $files->id . '/' . $files->file_name;
								@unlink($link);
								$this->db->where('id', $files->id);
								$this->db->delete('tblfiles');
							}
						}
						if (!empty($arrayMaintenanceNotDelete)) {
							$this->db->where_not_in('id', $arrayMaintenanceNotDelete);
						}
						$this->db->where('machines_id', $category_id);
						$this->db->delete('tbl_machines_maintenance');
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
		} else {
			$data['machines'] = $machines;
            $data['title'] = 'Cập nhật Thiết Bị';
            if(!empty($is_type)) {
                $data['title'] .= '('.$this->listTypeCategoryMachines[$is_type].')';
                $this->db->where('is_type', $is_type);
            }
			$data['dtCategoryMachines'] = $this->db->get('tbl_category_machines')->result_array();
			$data['standard'] = $this->db->get('tbl_packaging')->result_array();
			$this->load->view('admin/categories/edit_machines', $data);
		}
	}

	function getMachines()
	{
		$this->datatables->select("
            tbl_machines.id as id,
            tbl_category_machines.name as name_category,
            tbl_machines.code as code,
            tbl_machines.name as name,
            tbl_machines.product_in_month as product_in_month,
            tbl_machines.used_time as used_time,
            tbl_machines.status as status,
            tbl_machines.specifications as specifications,
            1 as stage,
            tbl_machines.supplier_id as supplier_id,
            tbl_machines.note as note,
            tbl_category_machines.is_type as is_type,
        ", FALSE)
        ->from('tbl_machines')
        ->join('tbl_category_machines', 'tbl_category_machines.id = tbl_machines.category_machine_id', 'left');

        if($this->input->post('filterCategory')) {
            $this->datatables->where('tbl_machines.category_machine_id', $this->input->post('filterCategory'));
        }
        if($this->input->post('filterCategoryAll')) {
            $this->datatables->where('tbl_category_machines.is_type', $this->input->post('filterCategoryAll'));
        }
		$this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-primary btn-icon tip" data-tnh="modal" title="' . lang('history') . '" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/history_machines/$1"><i class="fa fa-history"></i></a>
                <a class="tnh-modal btn btn-success btn-icon tip" title="' . lang('edit') . '"  data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/edit_machines/$1/$2"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon tip" title="' . lang('delete') . '" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/categories/delete_machines/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id,is_type');
		$result = json_decode($this->datatables->generate());
		foreach ($result->aaData as $key => $value) {
			$stage_id = get_table_where('tbl_machines_stage', ['machines_id' => $value[0]]);
			$arrSelect = [];
			if (!empty($stage_id)) {
				foreach ($stage_id as $kk => $vv) {
					$arrSelect[] = $vv['category_stage_id'];
				}
			}
			$htmlCategory = '<option></option>';
			$categoiesStage = get_table_where('tbl_category_stages');
			if (!empty($categoiesStage)) {
				foreach ($categoiesStage as $kk => $vv) {
					$htmlCategory .= '<option ' . ((!empty($arrSelect) && in_array($vv['id'], $arrSelect)) ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
				}
			}
			$html = '<select class="selectpicker stage_id" onchange="changeStage(this)" multiple data-live-search="true" name="stage_id[]">
                    ' . $htmlCategory . '
            </select>';
			$result->aaData[$key][8] = $html;


            $supplier_id = $value[9];
            $htmlSupplier = '<option></option>';
            $dtSupplier = get_table_where('tblsuppliers');
            if (!empty($dtSupplier)) {
                foreach ($dtSupplier as $kk => $vv) {
                    $htmlSupplier .= '<option ' . ($supplier_id == $vv['id'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['company'] . '</option>';
                }
            }
            $html = '<select class="selectpicker supplier_id" onchange="changeSupplier(this)" data-live-search="true" name="supplier_id">
                    ' . $htmlSupplier . '
            </select>';
            $result->aaData[$key][9] = $html;

		}
		echo (json_encode($result));return;
	}

	function add_category_stage()
	{
		$data = [];
		$id = $this->input->post('id');
		$stage_id = $this->input->post('stage_id');
		$data['result'] = 0;
		$data['message'] = 'Thêm thất bại';
		$success = false;
		$items = [];
		if (empty($stage_id)) {
			$this->db->where('machines_id', $id);
			$this->db->delete('tbl_machines_stage');
			$data['result'] = 1;
			$data['message'] = 'Xóa thành công';
			echo json_encode($data);
			die();
		}
		foreach ($stage_id as $key => $value) {
			if (empty($value)) {
				continue;
			}
			$items[] = [
				'machines_id' => $id,
				'category_stage_id' => $value,
			];
		}
		if (empty($items)) {
			$data['result'] = 0;
			$data['message'] = 'Không có nhóm công đoạn';
			echo json_encode($data);
			die();
		}
		$arrId = [];
		foreach ($items as $key => $value) {
			$checkExist = get_table_where('tbl_machines_stage', ['machines_id' => $id, 'category_stage_id' => $value['category_stage_id']], '', 'row_array');
			if (!empty($checkExist)) {
				$arrId[] = $checkExist['id'];
				$success = true;
			} else {
				$success = $this->db->insert('tbl_machines_stage', $value);
				$insert_id = $this->db->insert_id();
				$arrId[] = $insert_id;
			}
		}
		if (empty($arrId)) {
			$this->db->where('machines_id', $id);
			$this->db->delete('tbl_machines_stage');
		} else {
			$this->db->where('machines_id', $id);
			$this->db->where_not_in('id', $arrId);
			$this->db->delete('tbl_machines_stage');
		}
		if ($success) {
			$data['result'] = 1;
			$data['message'] = 'Thêm thành công';
		} else {
			$data['result'] = 0;
			$data['message'] = 'Thêm thất bại';
		}
		echo json_encode($data);
	}

    function add_supplier_machines()
	{
		$data = [];
		$id = $this->input->post('id');
		$supplier_id = $this->input->post('supplier_id') ?? 0;
		$success = false;
        $this->db->where('id',$id);
        $success = $this->db->update('tbl_machines',[
            'supplier_id' => $supplier_id
        ]);
		if ($success) {
			$data['result'] = 1;
			$data['message'] = 'Thêm thành công';
		} else {
			$data['result'] = 0;
			$data['message'] = 'Thêm thất bại';
		}
		echo json_encode($data);
	}

	function delete_machines($id)
	{
		$data = [];
		if ($id) {
			$isUsed = get_table_where('tbl_production_lists_items', ['may_in' => $id], '', 'row_array', '', 'id');
			if ($isUsed['id']) {
				$data['result'] = 0;
				$data['message'] = lang('tnh_exist_not_delete');
				echo json_encode($data);
				return;
			}


			if ($this->category_model->deleteMachines($id)) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	function searchMachines()
	{
		$data = [];
		if ($this->input->get()) {
			$q = $this->input->get('q');
			$_stage_id = $this->input->get('_stage_id');
			$limit = 50;
			$data = $this->category_model->searchMachines($q, $limit, $_stage_id);
		}
		echo json_encode($data);
	}

	function history_machines($id)
	{
		$data['id'] = $id;
		$this->load->view('admin/categories/history_machines', $data);
	}

	function getHistoryMachines()
	{
		$machine_id = $this->input->post('machine_id');
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$this->datatables->select("
            tbl_history_machines.date as date,
            tbl_history_machines.production_id as production,
            tbl_products.name as product_name,
            tbl_machines.name as machine_name,
            tbl_history_machines.time_used as time_used,
            0 as time_rest
            ", FALSE)
			->from('tbl_history_machines')
			->join('tbl_products', 'tbl_products.id = tbl_history_machines.product_id')
			->join('tbl_machines', 'tbl_machines.id = tbl_history_machines.machine_id');
		$this->datatables->where('tbl_history_machines.machine_id', $machine_id);
		if ($start_date) {
			$this->datatables->where('DATE_FORMAT(tbl_history_machines.date, "%Y-%m-%d") >=', to_sql_date($start_date));
		}
		if ($end_date) {
			$this->datatables->where('DATE_FORMAT(tbl_history_machines.date, "%Y-%m-%d") <=', to_sql_date($end_date));
		}
		echo $this->datatables->generate();
	}

	public function packaging()
	{
		$data['tnh'] = true;
		$data['title'] = _l('Tiêu chuẩn');
		$this->load->view('admin/categories/packaging', $data);
	}

	public function add_packaging()
	{
		$data = [];
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_packaging.code]');
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$constitutive = $this->input->post('constitutive');
				$note = $this->input->post('note');
				$options = [
					'name' => $name,
					'code' => $code,
					'constitutive' => $constitutive,
					'note' => $note,
				];
				$id = $this->category_model->insertPackaging($options);
				if ($id) {
					$data['result'] = 1;
					$data['message'] = lang('Thêm thành công');
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
		} else {
			$this->load->view('admin/categories/add_packaging', $data);
		}
	}

	public function edit_packaging($id)
	{
		$data = [];
		$packaging = $this->category_model->rowPackaging($id);
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ($packaging['code'] != $this->input->post('code')) {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_packaging.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$constitutive = $this->input->post('constitutive');
				$note = $this->input->post('note');
				$options = [
					'name' => $name,
					'code' => $code,
					'constitutive' => $constitutive,
					'note' => $note,
				];
				$id = $this->category_model->updatePackaging($id, $options);
				if ($id) {
					$data['result'] = 1;
					$data['message'] = lang('Sửa thành công');
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
		} else {
			$data['packaging'] = $packaging;
			$this->load->view('admin/categories/edit_packaging', $data);
		}
	}

	function getPackaging()
	{
		$this->datatables->select("
            tbl_packaging.id as id,
            tbl_packaging.code as code,
            tbl_packaging.name as name
            ", FALSE)
			->from('tbl_packaging');
		$this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/edit_packaging/$1"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/categories/delete_packaging/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
		$result = json_decode($this->datatables->generate());
		echo (json_encode($result));
	}

	function delete_packaging($id)
	{
		$data = [];
		if ($id) {
			// if (!$this->items_model->checkExistCategory($id)) {
			//     $data['result'] = 0;
			//     $data['message'] = lang('tnh_exist_not_delete');
			//     echo json_encode($data);
			//     return;
			// }
			// return;
			if ($this->category_model->deletePackaging($id)) {
				$data['result'] = 1;
				$data['message'] = lang('Xóa thành công');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function change_items_calculated($id, $status)
	{
		if ($this->input->is_ajax_request()) {
			$this->db->where('id', $id);
			$update_true = $this->db->update('tblcategories', [
				'calculated_on_sales' => $status,
			]);
			if ($update_true) {
				$arrID_child = array();
				$this->get_childs_id_items($id, $arrID_child);
				if ($status == 1) {
					$this->db->set('calculated_on_sales', 1);
					$this->db->where_in('category_id', $arrID_child);
					$this->db->update('tblitems');
				} else {
					$this->db->set('calculated_on_sales', 0);
					$this->db->where_in('category_id', $arrID_child);
					$this->db->update('tblitems');
				}
			}
		}
	}

	function get_childs_id_items($parent_id = '', &$result = array())
	{
		array_push($result, $parent_id);
		$this->db->where('category_parent', $parent_id);
		$items = $this->db->get('tblcategories')->result();
		foreach ($items as $value) {
			$this->get_childs_id_items($value->id, $result);
		}
	}

	public function locations()
	{
		$data['tnh'] = true;
		$data['title'] = _l('tnh_vt');
		$this->load->view('admin/categories/locations', $data);
	}

	public function handlingLocation($id = 0, $actions = 'add')
	{
		$data = [];
		$location = $this->category_model->rowLocations($id);
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ((!empty($location) && $location['code'] != $this->input->post('code')) || $actions == 'add') {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_locations.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$note = $this->input->post('note');
				$options = [
					'name' => $name,
					'code' => $code,
					'note' => $note,
				];
				if (!empty($location)) {
					$id = $this->category_model->updateLocations($id, $options);
				} else {
					$id = $this->category_model->insertLocations($options);
				}
				if ($id) {
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
		} else {
			$data['id'] = $id;
			$data['actions'] = $actions;
			$data['location'] = $location;
			$this->load->view('admin/categories/handling_location', $data);
		}
	}

	public function getLocations()
	{
		$this->datatables->select("
            tbl_locations.id as id,
            tbl_locations.code as code,
            tbl_locations.name as name,
            tbl_locations.note as note,
            ", FALSE)
			->from('tbl_locations');
		$this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/handlingLocation/$1/edit"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/categories/deleteLocations/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
		$result = json_decode($this->datatables->generate());
		echo (json_encode($result));
	}

	public function deleteLocations($id)
	{
		$data = [];
		if ($id) {
			if ($this->category_model->deleteLocations($id)) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function workplace()
	{
		$data['tnh'] = true;
		$data['title'] = _l('tnh_workplace');
		$this->load->view('admin/categories/workplace', $data);
	}

	public function handlingWorkplace($id = 0, $actions = 'add')
	{
		$data = [];
		$workplace = $this->category_model->rowWorkplace($id);
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ((!empty($workplace) && $workplace['code'] != $this->input->post('code')) || $actions == 'add') {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_workplace.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$note = $this->input->post('note');
				$options = [
					'name' => $name,
					'code' => $code,
					'note' => $note,
				];
				if (!empty($workplace)) {
					$id = $this->category_model->updateWorkplace($id, $options);
				} else {
					$id = $this->category_model->insertWorkplace($options);
				}
				if ($id) {
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
		} else {
			$data['id'] = $id;
			$data['actions'] = $actions;
			$data['workplace'] = $workplace;
			$this->load->view('admin/categories/handling_workplace', $data);
		}
	}

	public function getWorkplace()
	{
		$this->datatables->select("
            tbl_workplace.id as id,
            tbl_workplace.code as code,
            tbl_workplace.name as name,
            tbl_workplace.note as note,
            ", FALSE)
			->from('tbl_workplace');
		$this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/handlingWorkplace/$1/edit"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/categories/deleteWorkplace/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
		$result = json_decode($this->datatables->generate());
		echo (json_encode($result));
	}

	public function deleteWorkplace($id)
	{
		$data = [];
		if ($id) {
			if ($this->category_model->deleteWorkplace($id)) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function allowance()
	{
		$data['tnh'] = true;
		$data['title'] = _l('tnh_allowance');
		$this->load->view('admin/categories/allowance', $data);
	}

	public function handlingAllowance($id = 0, $actions = 'add')
	{
		$data = [];
		$allowance = $this->category_model->rowAllowance($id);
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ((!empty($allowance) && $allowance['code'] != $this->input->post('code')) || $actions == 'add') {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_allowance.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$note = $this->input->post('note');
				$money = number_unformat($this->input->post('money'));
				$options = [
					'name' => $name,
					'code' => $code,
					'note' => $note,
					'money' => $money,
				];
				if (!empty($allowance)) {
					$id = $this->category_model->updateAllowance($id, $options);
				} else {
					$id = $this->category_model->insertAllowance($options);
				}
				if ($id) {
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
		} else {
			$data['id'] = $id;
			$data['actions'] = $actions;
			$data['allowance'] = $allowance;
			$this->load->view('admin/categories/handling_allowance', $data);
		}
	}

	public function getAllowance()
	{
		$this->datatables->select("
            tbl_allowance.id as id,
            tbl_allowance.code as code,
            tbl_allowance.name as name,
            tbl_allowance.money as money,
            tbl_allowance.note as note,
            ", FALSE)
			->from('tbl_allowance');
		$this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/handlingAllowance/$1/edit"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/categories/deleteAllowance/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
		$result = json_decode($this->datatables->generate());
		echo (json_encode($result));
	}

	public function deleteAllowance($id)
	{
		$data = [];
		if ($id) {
			if ($this->category_model->deleteAllowance($id)) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function salary_form()
	{
		$data['tnh'] = true;
		$data['title'] = _l('tnh_salary_form');
		$this->load->view('admin/categories/salary_form', $data);
	}

	public function handlingSalaryForm($id = 0, $actions = 'add')
	{
		$data = [];
		$salaryForm = $this->category_model->rowSalaryForm($id);
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ((!empty($salaryForm) && $salaryForm['code'] != $this->input->post('code')) || $actions == 'add') {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_salary_form.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$note = $this->input->post('note');
				$money = number_unformat($this->input->post('money'));
				$options = [
					'name' => $name,
					'code' => $code,
					'note' => $note,
					'money' => $money,
				];
				if (!empty($salaryForm)) {
					$id = $this->category_model->updateSalaryForm($id, $options);
				} else {
					$id = $this->category_model->insertSalaryForm($options);
				}
				if ($id) {
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
		} else {
			$data['id'] = $id;
			$data['actions'] = $actions;
			$data['salaryForm'] = $salaryForm;
			$this->load->view('admin/categories/handling_salary_form', $data);
		}
	}

	public function getSalaryForm()
	{
		$this->datatables->select("
            tbl_salary_form.id as id,
            tbl_salary_form.code as code,
            tbl_salary_form.name as name,
            tbl_salary_form.money as money,
            tbl_salary_form.note as note,
            ", FALSE)
			->from('tbl_salary_form');
		$this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/handlingSalaryForm/$1/edit"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/categories/deleteSalaryForm/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
		$result = json_decode($this->datatables->generate());
		echo (json_encode($result));
	}

	public function deleteSalaryForm($id)
	{
		$data = [];
		if ($id) {
			if ($this->category_model->deleteSalaryForm($id)) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function insurrance()
	{
		$data['tnh'] = true;
		$data['title'] = _l('tnh_insurrance');
		$this->load->view('admin/categories/insurrance', $data);
	}

	public function handlingInsurrance($id = 0, $actions = 'add')
	{
		$data = [];
		$insurrance = $this->category_model->rowInsurrance($id);
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			$this->form_validation->set_rules('ht', lang("tnh_hinhthuc"), 'required');
			$this->form_validation->set_rules('rate_company', lang("tnh_rate_company"), 'required');
			$this->form_validation->set_rules('rate_worker', lang("tnh_rate_worker"), 'required');
			if ((!empty($insurrance) && $insurrance['code'] != $this->input->post('code')) || $actions == 'add') {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_insurrance.code]');
			}
			if ($this->form_validation->run() == true) {
				$ht = $this->input->post('ht');
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$note = $this->input->post('note');
				$money = number_unformat($this->input->post('money'));
				$rate_company = number_unformat($this->input->post('rate_company'));
				$rate_worker = number_unformat($this->input->post('rate_worker'));
				$options = [
					'name' => $name,
					'code' => $code,
					'note' => $note,
					'money' => $money,
					'form' => $ht,
					'rate_company' => $rate_company,
					'rate_worker' => $rate_worker,
				];
				if (!empty($insurrance)) {
					$id = $this->category_model->updateInsurrance($id, $options);
				} else {
					$id = $this->category_model->insertInsurrance($options);
				}
				if ($id) {
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
		} else {
			$data['id'] = $id;
			$data['actions'] = $actions;
			$data['insurrance'] = $insurrance;
			$this->load->view('admin/categories/handling_insurrance', $data);
		}
	}

	public function getInsurrance()
	{
		$this->datatables->select("
            tbl_insurrance.id as id,
            tbl_insurrance.form as ht,
            tbl_insurrance.code as code,
            tbl_insurrance.name as name,
            tbl_insurrance.money as money,
            tbl_insurrance.rate_company as rate_company,
            tbl_insurrance.rate_worker as rate_worker,
            tbl_insurrance.note as note,
            ", FALSE)
			->from('tbl_insurrance');
		$this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/handlingInsurrance/$1/edit"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/categories/deleteInsurrance/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
		$result = json_decode($this->datatables->generate());
		echo (json_encode($result));
	}

	public function deleteInsurrance($id)
	{
		$data = [];
		if ($id) {
			if ($this->category_model->deleteInsurrance($id)) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function searchInsurrance($id = 0)
	{
		$data = [];
		$term = $this->input->get('term', TRUE);
		$limit = get_option('select2_limit');
		$params = $this->input->get('params');
		$type = $params['type'];
		$insurrance = $this->category_model->searchInsurrance($term, $limit, $type);
		$results = $insurrance;
		$data['results'] = $results;
		if ($id) {
			$insurrance = $this->personnel_model->getInsurranceById($id);
			if (!empty($insurrance)) {
				$data['row'] = ['id' => $insurrance['id'], 'text' => $insurrance['name']];
			} else {
				$data['row'] = ['id' => 0, 'text' => ''];
			}
		}
		echo json_encode($data);
	}

	public function searchHospitalInsurrance($id = 0)
	{
		$data = [];
		$term = $this->input->get('term', TRUE);
		$limit = get_option('select2_limit');
		$params = $this->input->get('params');
		$province_id = $params['province_id'];
		$hospitalInsurrance = $this->category_model->searchHospitalInsurrance($term, $limit, $province_id);
		$results = $hospitalInsurrance;
		$data['results'] = $results;
		if ($id) {
			$hospital = $this->personnel_model->getHospitalInsurranceById($id);
			if (!empty($hospital)) {
				$data['row'] = ['id' => $hospital['id'], 'text' => $hospital['name']];
			} else {
				$data['row'] = ['id' => 0, 'text' => ''];
			}
		}
		echo json_encode($data);
	}

	public function remove_file_machines($id = "")
	{
		if (!empty($id)) {
			$this->db->where('tblfiles.id', $id);
			$this->db->where('tblfiles.rel_type', 'rel_process');
			$files = $this->db->get('tblfiles')->row();
			if (!empty($files)) {
				$link = FCPATH . 'uploads/machines/' . $files->id . '/' . $files->file_name;
				@unlink($link);
				$this->db->where('id', $id);
				$success = $this->db->delete('tblfiles');
				if (!empty($success)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Xóa file thành công'
					]);
					die();
				}
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa file không thành công'
		]);
		die();
	}

	public function remove_file_main($id = "")
	{
		if (!empty($id)) {
			$this->db->where('tblfiles.id', $id);
			$this->db->where('tblfiles.rel_type', 'rel_main');
			$files = $this->db->get('tblfiles')->row();
			if (!empty($files)) {
				$link = FCPATH . 'uploads/machines_maintenance/' . $files->id . '/' . $files->file_name;
				@unlink($link);
				$this->db->where('id', $id);
				$success = $this->db->delete('tblfiles');
				if (!empty($success)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Xóa file thành công'
					]);
					die();
				}
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa file không thành công'
		]);
		die();
	}

	public function modal_excel_machines()
	{
		$data['title'] = _l('Import excel máy móc');
		$this->load->view('admin/categories/import_machines', $data);
	}


	public function import_machines()
	{
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');

		//		$type_items = $this->db->get('tbltype_items')->result_array();
		//		foreach($type_items as $key => $value) {
		//			$data_type_items[mb_strtolower($value['name'],'UTF-8')] = $value['type'];
		//		}


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
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load("$fullfile");
			$total_sheets = $objPHPExcel->getSheetCount();
			$allSheetName = $objPHPExcel->getSheetNames();

			$vaKey = '';
			$process = [];
			$month = [];
			$maintenance = [];
			$note_main = [];
			$listRow = [
				1 => 'code',
				2 => 'name',
				3 => 'status',
				4 => 'product_in_month',
				5 => 'standard',
				6 => 'pp_measure',
				7 => 'quota_productivity',
				8 => 'day_operation',
				9 => 'operating_gauge',
				10 => 'preparation_time',
				11 => 'specifications',
				12 => 'note',
				13 => 'process',
				14 => 'maintenance',
				15 => 'month',
				16 => 'note_main',
				17 => 'product_color',
				18 => 'soup_ingredients',
				19 => 'time_change_size',
			];
			for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
				$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$vaKey = '';
				for ($i = 2; $i <= $highestRow; $i++) {
					$redata = [];
					for ($j = 1; $j < $highestColumnIndex; $j++) {
						$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
						if ($j == 2) {
							if (!empty($Val)) {
								$vaKey = $Val;
								$process = [];
								$maintenance = [];
								$month = [];
								$note_main = [];
							}
						} else if ($j == 8) {
							if (is_numeric($Val)) {
								$Val = PHPExcel_Shared_Date::ExcelToPHPObject($Val)->format('d-m-Y H:i:s');
							}
						} else if ($j == 13) {
							$process[] = $Val;
						} else if ($j == 14) {
							$maintenance[] = $Val;
						} else if ($j == 15) {
							$month[] = $Val;
						} else if ($j == 16) {
							$note_main[] = $Val;
						}

						$redata[$listRow[$j]] = trim($Val);
					}
					if (!empty($vaKey)) {
						if (!empty($data[$vaKey])) {
							$data[$vaKey]['process'] = $process;
							$data[$vaKey]['maintenance'] = $maintenance;
							$data[$vaKey]['month'] = $month;
							$data[$vaKey]['note_main'] = $note_main;
						} else {
							$data[$vaKey] = $redata;
						}
					}
				}
			}
		}
		$count = 0;
		if (!empty($data)) {

			$status_machine = status_machine_new();
			$data_status_machine = [];
			foreach ($status_machine as $key => $value) {
				$data_status_machine[mb_strtolower($value, 'UTF-8')] = $key;
			}

			foreach ($data as $key => $value) {
				//				$value['standard'] = $this->db->get_where('tbl_packaging', ['code' => $value['standard']])->row('id');
				//				if(empty($value['standard'])) {
				//					$value['standard'] = NULL;
				//				}


				$options = [
					'code' => $value['code'],
					'name' => $value['name'],
					'status' => !empty($data_status_machine[mb_strtolower($value['status'], 'UTF-8')]) ? $data_status_machine[mb_strtolower($value['status'], 'UTF-8')] : 0,
					'product_in_month' => $value['product_in_month'],
					'standard' => $value['standard'],
					'pp_measure' => $value['pp_measure'],
					'quota_productivity' => $value['quota_productivity'],
					'day_operation' => to_sql_date($value['day_operation']),
					'operating_gauge' => $value['operating_gauge'],
					'preparation_time' => $value['preparation_time'],
					'specifications' => $value['specifications'],
					'note' => $value['note'],
					'product_color' => $value['product_color'],
					'soup_ingredients' => !empty($value['soup_ingredients']) ? number_unformat($value['soup_ingredients']) : 0,
					'time_change_size' => !empty($value['time_change_size']) ? number_unformat($value['time_change_size']) : 0,
				];
				$this->db->where('code', $value['code']);
				$ktmachines = $this->db->get('tbl_machines')->row();
				if (!empty($ktmachines)) {
					continue;
				}

				$id = $this->category_model->insertMachines($options);
				if (!empty($id)) {
					$process = $value['process'];
					$maintenance = $value['maintenance'];
					$month = $value['month'];
					$note_main = $value['note_main'];
					if (!empty($process)) {
						foreach ($process as $k => $v) {
							if (!empty($v)) {
								$this->db->insert('tbl_machines_process', [
									'machines_id' => $id,
									'process' => $v,
								]);
							}
						}
					}

					if (!empty($month) && !empty($maintenance)) {
						foreach ($maintenance as $k => $v) {
							if (!empty($v) && isset($month[$k])) {
								$this->db->insert('tbl_machines_maintenance', [
									'machines_id' => $id,
									'name' => $v,
									'month' => $month[$k],
									'note_main' => $note_main[$k],
								]);
							}
						}
					}
					$count++;
				}
			}
		}
		echo json_encode(
			[
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Import thành công ' . $count . ' Items',
			]
		);
		die();
	}

	public function export_machines()
	{


		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');

		// print_arrays($this->input->post());
		$cloumns = $this->input->post('cloumns');
		$style_excel = style_excel();
		$cloumns_excel = cloumns_excel();

		$status_machine = status_machine_new();


		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


		$numberRow = 2;
		$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(15);
		$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'STT')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'Mã Thiết Bị/Công Việc')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'Tên Thiết Bị/Công Việc')->getStyle("C$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'Trạng Thái')->getStyle("D$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Định Mức Năng Suất/Tháng')->getStyle("E$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'Tiêu Chuẩn')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", 'Phương Pháp Kiểm')->getStyle("G$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("H$numberRow", 'Định Mức Năng Suất/h')->getStyle("H$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("I$numberRow", 'Ngày Bắt Đầu Bảo Trì')->getStyle("I$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("J$numberRow", 'Khổ Vận Hành')->getStyle("J$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("K$numberRow", 'Thời Gian Chuẩn Bị (Giờ))')->getStyle("K$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("L$numberRow", 'Thông số kỹ thuật')->getStyle("L$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("M$numberRow", 'Ghi chú')->getStyle("M$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("N$numberRow", 'Quy Trình')->getStyle("N$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("O$numberRow", 'Bộ Phận Máy Móc')->getStyle("O$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("P$numberRow", 'Số Ngày Cần Bảo Trì')->getStyle("P$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("Q$numberRow", 'Ghi Chú Cách Thức Bảo Trì')->getStyle("Q$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("R$numberRow", 'Định Mức Thời Gian Duyệt Màu')->getStyle("R$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("S$numberRow", 'Nhóm Công Đoạn')->getStyle("S$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("T$numberRow", 'NPL canh bài')->getStyle("T$numberRow")->applyFromArray($style_excel['Background_header']);
		$numberRow++;

		$stt = 1;

		$this->db->select('tbl_machines.*, tbl_packaging.code as standard');
		$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_machines.standard', 'left');
		$machines = $this->db->get('tbl_machines')->result_array();
		if (!empty($machines)) {
			foreach ($machines as $key => $value) {
				$numBer = 1;
				$this->db->where('machines_id', $value['id']);
				$process = $this->db->get('tbl_machines_process')->result_array();
				if (count($process) > $numBer) {
					$numBer = count($process);
				}

				$this->db->where('machines_id', $value['id']);
				$maintenance = $this->db->get('tbl_machines_maintenance')->result_array();
				if (count($maintenance) > $numBer) {
					$numBer = count($maintenance);
				}


				$number_start = $numberRow;
				$number_end = $numberRow - 1;
				$code = $value['code'];
				$name = $value['name'];
				$status = $value['status'];
				$product_in_month = $value['product_in_month'];
				$standard = $value['standard'];
				$pp_measure = $value['pp_measure'];
				$quota_productivity = $value['quota_productivity'];
				$day_operation = _d($value['day_operation']);
				$operating_gauge = $value['operating_gauge'];
				$preparation_time = $value['preparation_time'];
				$specifications = $value['specifications'];
				$note = $value['note'];
				$product_color = $value['product_color'];
				$soup_ingredients = $value['soup_ingredients'];

				$this->db->select('GROUP_CONCAT(name) as list_group');
				$this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_machines_stage.category_stage_id');
				$this->db->where('machines_id', $value['id']);
				$list_stage = $this->db->get('tbl_machines_stage')->row('list_group');


				$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", $stt)->getStyle("A$numberRow")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", $code)->getStyle("B$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", $name)->getStyle("C$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", (!empty($status_machine[$status]) ? $status_machine[$status] : ''))->getStyle("D$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);

				$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", $product_in_month)->getStyle("E$numberRow")->getNumberFormat()->setFormatCode('#,##0.0');
				$objPHPExcel->getActiveSheet()->getStyle("E$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);

				$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", $standard)->getStyle("F$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", $pp_measure)->getStyle("G$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);

				$objPHPExcel->getActiveSheet()->SetCellValue("H$numberRow", $quota_productivity)->getStyle("H$numberRow")->getNumberFormat()->setFormatCode('#,##0.0');
				$objPHPExcel->getActiveSheet()->getStyle("H$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);

				$objPHPExcel->getActiveSheet()->SetCellValue("I$numberRow", $day_operation)->getStyle("I$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("J$numberRow", $operating_gauge)->getStyle("J$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);

				$objPHPExcel->getActiveSheet()->SetCellValue("K$numberRow", $preparation_time)->getStyle("K$numberRow")->getNumberFormat()->setFormatCode('#,##0.0');
				$objPHPExcel->getActiveSheet()->getStyle("K$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);

				$objPHPExcel->getActiveSheet()->SetCellValue("L$numberRow", $specifications)->getStyle("L$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("M$numberRow", $note)->getStyle("M$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("R$numberRow", $product_color)->getStyle("R$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("S$numberRow", $list_stage)->getStyle("S$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("T$numberRow", $soup_ingredients)->getStyle("T$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_center']);

				for ($i = 0; $i < $numBer; $i++) {
					$objPHPExcel->getActiveSheet()->SetCellValue("N$numberRow", (!empty($process[$i]['process']) ? $process[$i]['process'] : ''))->getStyle("N$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("N$numberRow")->applyFromArray($style_excel['BStyle_left']);

					$objPHPExcel->getActiveSheet()->SetCellValue("O$numberRow", (!empty($maintenance[$i]['name']) ? $maintenance[$i]['name'] : ''))->getStyle("O$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("O$numberRow")->applyFromArray($style_excel['BStyle_left']);

					$objPHPExcel->getActiveSheet()->SetCellValue("P$numberRow", (!empty($maintenance[$i]['month']) ? $maintenance[$i]['month'] : ''))->getStyle("P$numberRow")->getNumberFormat()->setFormatCode('#,##0.0');
					$objPHPExcel->getActiveSheet()->getStyle("P$numberRow")->applyFromArray($style_excel['BStyle_center']);

					$objPHPExcel->getActiveSheet()->SetCellValue("Q$numberRow", (!empty($maintenance[$i]['note_main']) ? $maintenance[$i]['note_main'] : ''))->getStyle("Q$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("Q$numberRow")->applyFromArray($style_excel['BStyle_left']);

					$number_end++;
					$numberRow++;
				}

				$objPHPExcel->getActiveSheet()->mergeCells("A$number_start:A$number_end")->getStyle("A$number_start:A$number_end")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->mergeCells("B$number_start:B$number_end")->getStyle("B$number_start:B$number_end")->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->mergeCells("C$number_start:C$number_end")->getStyle("C$number_start:C$number_end")->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->mergeCells("D$number_start:D$number_end")->getStyle("D$number_start:D$number_end")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->mergeCells("E$number_start:E$number_end")->getStyle("E$number_start:E$number_end")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->mergeCells("F$number_start:F$number_end")->getStyle("F$number_start:F$number_end")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->mergeCells("G$number_start:G$number_end")->getStyle("G$number_start:G$number_end")->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->mergeCells("H$number_start:H$number_end")->getStyle("H$number_start:H$number_end")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->mergeCells("I$number_start:I$number_end")->getStyle("I$number_start:I$number_end")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->mergeCells("J$number_start:J$number_end")->getStyle("J$number_start:J$number_end")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->mergeCells("K$number_start:K$number_end")->getStyle("K$number_start:K$number_end")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->mergeCells("L$number_start:L$number_end")->getStyle("L$number_start:L$number_end")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->mergeCells("M$number_start:M$number_end")->getStyle("M$number_start:M$number_end")->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->mergeCells("R$number_start:R$number_end")->getStyle("R$number_start:R$number_end")->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->mergeCells("S$number_start:S$number_end")->getStyle("S$number_start:S$number_end")->applyFromArray($style_excel['BStyle_left']);
				$objPHPExcel->getActiveSheet()->mergeCells("T$number_start:T$number_end")->getStyle("T$number_start:T$number_end")->applyFromArray($style_excel['BStyle_left']);
				$stt++;
			}
		}


		$filename = lang('Danh_sach_may_moc_thiet_bi') . '.xls';
		$objPHPExcel->getActiveSheet()->freezePane('A1');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	public function mode_materials()
	{
		$data['tnh'] = true;
		$data['title'] = _l('tnh_mode_materials');
		$this->load->view('admin/categories/mode_materials', $data);
	}

	public function add_mode_materials()
	{
		$data = [];
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_mode_materials.code]');
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$options = [
					'name' => $name,
					'code' => $code,
				];
				$id = $this->category_model->insertModeMaterials($options);
				if ($id) {
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
		} else {
			$this->load->view('admin/categories/add_mode_materials', $data);
		}
	}

	public function edit_mode_materials($id)
	{
		$data = [];
		$mode_materials = $this->category_model->rowModeMaterials($id);
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ($mode_materials['code'] != $this->input->post('code')) {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_mode_materials.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$options = [
					'name' => $name,
					'code' => $code,
				];
				$id = $this->category_model->updateModeMaterials($id, $options);
				if ($id) {
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
		} else {
			$data['mode_materials'] = $mode_materials;
			$this->load->view('admin/categories/edit_mode_materials', $data);
		}
	}

	function getModeMaterials()
	{
		$this->datatables->select("
			tbl_mode_materials.id as id,
			tbl_mode_materials.code as code,
            tbl_mode_materials.name as name,
            ", FALSE)
			->from('tbl_mode_materials');
		$this->datatables->add_column('actions', '
            <div>
                <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/categories/edit_mode_materials/$1"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/categories/delete_mode_materials/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button>
            </div>
        ', 'id');
		$result = json_decode($this->datatables->generate());
		echo (json_encode($result));
	}

	function delete_mode_materials($id)
	{
		$data = [];
		if ($id) {
			if (!$this->category_model->checkExistCategoryModeMaterials($id)) {
				$data['result'] = 0;
				$data['message'] = lang('tnh_exist_not_delete');
				echo json_encode($data);
				return;
			}

			if ($this->category_model->deleteModeMaterials($id)) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function modal_excel_mode_materials()
	{
		$data['title'] = _l('tnh_import_mode_materials');
		$this->load->view('admin/categories/import_mode_materials', $data);
	}

	// yct start
	public function modal_excel_capacity()
	{
		if (false) {
			echo json_encode(array(
				'success' => true,
				'message' => _l('Bạn không có quyền thêm mới!')
			));
			die;
		}
		$data['title'] = _l('t_import_capacity');
		$this->load->view('admin/categories/import_capacity', $data);
	}

	public function modal_excel_transportation_vehicles()
	{
		$data['title'] = _l('t_import_transportation_vehicles');
		$this->load->view('admin/categories/import_transportation_vehicles', $data);
	}

	public function import_capacity()
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

				$checkCode = $this->category_model->checkCodeExistCapacity($code);
				if (!empty($checkCode)) continue;

				$options = [
					'code' => $code,
					'name' => $name,
					'note' => $note
				];

				$rs = $this->category_model->insertCapacity($options);
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

	public function import_mode_materials()
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
				// 3: note

				$code = $value[0];
				$name = $value[1];
				$note = $value[2];

				if (empty($code) || empty($name)) {
					continue;
				}

				$options = [
					'code' => $code,
					'name' => $name,
					'note' => $note,
				];
				$id = $this->category_model->getModeMaterialsIdByCode($code);
				if (!empty($id)) { //update
					$result = $this->category_model->updateModeMaterials($id, $options);
					if ($result) {
						$count++;
					}
				} else { //add
					$rs = $this->category_model->insertModeMaterials($options);
					if ($rs) {
						$count++;
					}
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

	public function print_qr_machine()
	{
		ob_start();
		$data = [];
		$product_id = $this->input->get('ids');
		$arrId = explode(',', $product_id);
		$title = lang('IN QR CÔNG ĐOẠN');
		$items = null;
		if (!empty($arrId)) {
			$this->db->select('tbl_machines.id as id, tbl_machines.code as code, tbl_machines.name as name');
			$this->db->from('tbl_machines');
			$this->db->where_in('tbl_machines.id', $arrId);
			$items = $this->db->get()->result_array();
		}

		$data['items'] = $items;

		$content = ob_get_contents();

		$data['object'] = "machine";
		$data['hide'] = 'hide';
		$data['title'] = $title;
		$data['content'] = $content;
		ob_end_clean();
		$pdf = print_pdf_qr_dt($data);
		$type = 'I';
		if ($type == "S") {
			return $pdf->Output(slug_it('qr') . '.pdf', $type);
		} else {
			$pdf->Output(slug_it('qr') . '.pdf', $type);
		}
	}

	public function category_machines()
	{
		$data['tnh'] = true;
		$data['title'] = _l('dt_category_machines');
		$this->load->view('admin/categories/category_machines', $data);
	}

	public function handlingCategoryMachines($id = 0, $actions = 'add')
	{
		$data = [];
		$dtCategoryMachines = get_table_where('tbl_category_machines', ['id' => $id], '', 'row_array');
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ((!empty($dtCategoryMachines) && $dtCategoryMachines['code'] != $this->input->post('code')) || $actions == 'add') {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_machines.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$code_species = $this->input->post('code_species');
				$name_species = $this->input->post('name_species');
				$note = $this->input->post('note');
				$is_type = $this->input->post('is_type');

				if (!empty($dtCategoryMachines)) {
					$options = [
						'name' => $name,
						'code' => $code,
						'code_species' => $code_species,
						'name_species' => $name_species,
						'note' => $note,
						'is_type' => $is_type,
					];
					$this->db->where('id', $id);
					$id = $this->db->update('tbl_category_machines', $options);
				} else {
					$options = [
						'name' => $name,
						'code' => $code,
						'code_species' => $code_species,
						'name_species' => $name_species,
						'note' => $note,
						'created_by' => get_staff_user_id(),
						'date_created' => date('Y-m-d H:i:s'),
                        'is_type' => $is_type,
					];
					$this->db->insert('tbl_category_machines', $options);
					$id = $this->db->insert_id();
				}
				if ($id) {
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
			die();
		} else {
			if (!empty($dtCategoryMachines)) {
				$actions = 'edit';
			}
			$data['id'] = $id;
			$data['actions'] = $actions;
			$data['dtCategoryMachines'] = $dtCategoryMachines;
//            $data['listTypeCategoryMachines'] = [
//                'refrigeration' => 'Điện Lạnh',
//                'camera' => 'Camera',
//                'ctp' => 'CTP',
//                'wastewater' => 'Nước Thải',
//                'hardware' => 'Phần Cứng',
//                'software' => 'Phần Mềm',
//                'pccc' => 'Phòng Cháy Chữa Cháy',
//                'transportation' => 'Phương Tiện Vận Chuyển',
//                'sever' => 'Sever',
//                'laborsafety' => 'An Toàn Lao Động',
//                'testingequipment' => 'Đo Kiểm',
//                'office' => 'Văn Phòng',
//                'office' => 'Văn Phòng',
//            ];
            $data['listTypeCategoryMachines'] = $this->listTypeCategoryMachines;
			$this->load->view('admin/categories/handling_category_machines', $data);
		}
	}

	public function getCategoryMachines()
	{

		$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
		$aColumns = [
			'tbl_category_machines.id as id',
			'tbl_category_machines.code as code',
			'tbl_category_machines.name as name',
			'tbl_category_machines.code_species as code_species',
			'tbl_category_machines.name_species as name_species',
			'tbl_category_machines.is_type as is_type',
			'tbl_category_machines.note as note',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_category_machines';
		$where = [];
		$filter = [];
		$join = [];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $key => $aRow) {
			$row = array();
			$row[] = '<div class="text-center" style="min-width: 40px">' . (++$key) . '</div>';
			$row[] = '<div class="text-left" style="min-width: 120px">' . $aRow['code'] . '</a></div>';
			$row[] = '<div class="text-left" style="min-width: 110px">' . ($aRow['name']) . '</div>';
			$row[] = '<div class="text-left" style="min-width: 110px">' . ($aRow['code_species']) . '</div>';
			$row[] = '<div class="text-left" style="min-width: 110px">' . ($aRow['name_species']) . '</div>';
			$row[] = '<div class="text-left"><span class="label label-warning">' . (!empty($this->listTypeCategoryMachines[$aRow['is_type']]) ? $this->listTypeCategoryMachines[$aRow['is_type']] : 'Chưa Phân Loại') . '</span></div>';
			$row[] = '<div class="text-left" style="min-width: 110px">' . ($aRow['note']) . '</div>';

			$edit = '<a class="tnh-modal" href="' . base_url('admin/categories/handlingCategoryMachines/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>';

			$delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories/deleteCategoryMachines/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>';
			$actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
			$row[] = '<div style="min-width: 120px">' . $actions . '</div>';
			$output['aaData'][] = $row;
		}
		echo json_encode($output);
	}

	public function deleteCategoryMachines($id)
	{
		$data = [];
		if ($id) {
			$this->db->where('tbl_machines.category_machine_id', $id);
			$this->db->from('tbl_machines');
			$checkExists = $this->db->get()->row_array();
			if (!empty($checkExists)) {
				$data['result'] = 0;
				$data['message'] = lang('Nhóm thiết bị đã được sử dụng');
				echo json_encode($data);
				die();
			}
			$this->db->where('tbl_category_machines.id', $id);
			$success = $this->db->delete('tbl_category_machines');
			if ($success) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function board()
	{
		$data['tnh'] = true;
		$data['title'] = _l('dt_board');
		$this->load->view('admin/board/index', $data);
	}

	public function handlingBoard($id = 0, $actions = 'add')
	{
		$data = [];
		$dtCategoryMachines = get_table_where('tbl_category_machines', ['id' => $id], '', 'row_array');
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if ((!empty($dtCategoryMachines) && $dtCategoryMachines['code'] != $this->input->post('code')) || $actions == 'add') {
				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_machines.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$code = $this->input->post('code');
				$code_species = $this->input->post('code_species');
				$name_species = $this->input->post('name_species');
				$note = $this->input->post('note');

				if (!empty($dtCategoryMachines)) {
					$options = [
						'name' => $name,
						'code' => $code,
						'code_species' => $code_species,
						'name_species' => $name_species,
						'note' => $note,
					];
					$this->db->where('id', $id);
					$id = $this->db->update('tbl_category_machines', $options);
				} else {
					$options = [
						'name' => $name,
						'code' => $code,
						'code_species' => $code_species,
						'name_species' => $name_species,
						'note' => $note,
						'created_by' => get_staff_user_id(),
						'date_created' => date('Y-m-d H:i:s'),
					];
					$this->db->insert('tbl_category_machines', $options);
					$id = $this->db->insert_id();
				}
				if ($id) {
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
			die();
		} else {
			if (!empty($dtCategoryMachines)) {
				$actions = 'edit';
			}
			$data['id'] = $id;
			$data['actions'] = $actions;
			$data['dtCategoryMachines'] = $dtCategoryMachines;
			$this->load->view('admin/categories/handling_category_machines', $data);
		}
	}

	public function getBoard()
	{
		$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
		$aColumns = [
			'tbl_category_machines.id as id',
			'tbl_category_machines.code as code',
			'tbl_category_machines.name as name',
			'tbl_category_machines.code_species as code_species',
			'tbl_category_machines.name_species as name_species',
			'tbl_category_machines.note as note',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_category_machines';
		$where = [];
		$filter = [];

		$join = [];

		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);


		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $key => $aRow) {
			$row = array();
			$row[] = '<div class="text-center" style="min-width: 40px">' . (++$key) . '</div>';
			$row[] = '<div class="text-left" style="min-width: 120px">' . $aRow['code'] . '</a></div>';
			$row[] = '<div class="text-left" style="min-width: 110px">' . ($aRow['name']) . '</div>';
			$row[] = '<div class="text-left" style="min-width: 110px">' . ($aRow['code_species']) . '</div>';
			$row[] = '<div class="text-left" style="min-width: 110px">' . ($aRow['name_species']) . '</div>';
			$row[] = '<div class="text-left" style="min-width: 110px">' . ($aRow['note']) . '</div>';

			$edit = '<a class="tnh-modal" href="' . base_url('admin/categories/handlingCategoryMachines/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>';

			$delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/categories/deleteCategoryMachines/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>';
			$actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
			$row[] = '<div style="min-width: 120px">' . $actions . '</div>';
			$output['aaData'][] = $row;
		}
		echo json_encode($output);
	}

	public function deleteBoard($id)
	{
		$data = [];
		if ($id) {
			$this->db->where('tbl_machines.category_machine_id', $id);
			$this->db->from('tbl_machines');
			$checkExists = $this->db->get()->row_array();
			if (!empty($checkExists)) {
				$data['result'] = 0;
				$data['message'] = lang('Nhóm thiết bị đã được sử dụng');
				echo json_encode($data);
				die();
			}
			$this->db->where('tbl_category_machines.id', $id);
			$success = $this->db->delete('tbl_category_machines');
			if ($success) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}


	public function category_test_item_quality($type = 'products', $type_event = '1')
	{
		$data['tnh'] = true;

		$data['title'] = _l('Hạng mục kiểm tra chất lượng');
		if (!empty($type)) {
			if ($type == 'products') {
				if ($type_event == '1') {
					$data['title'] = 'Kiểm tra các tham số chung (Sản Phẩm)';
				} else if ($type_event == '2') {
					$data['title'] = 'Kiểm tra chất lượng ngoại quan (Sản Phẩm)';
				}
			} else {
				if ($type_event == '1') {
					$data['title'] = 'Kiểm tra các tham số chung (NPL)';
				} else if ($type_event == '2') {
					$data['title'] = 'Kiểm tra chất lượng ngoại quan (NPL)';
				}
			}
		}
		$data['type'] = $type;
		$data['type_event'] = $type_event;
		$this->load->view('admin/categories/category_test_item_quality/manage', $data);
	}

	public function detail_category_test_item_quality($id = '')
	{
		if (empty($id)) {
			$type = $this->input->get('type');
			$type_event = $this->input->get('type_event');
			if (empty($type)) {
				$type = 'products';
			}
			if (empty($type_event)) {
				$type_event = '1';
			}
		}

		$data = [];
		if (!empty($id)) {
			$data['item_quality'] = $this->db->get_where('tblcategory_test_item_quality', ['id' => $id])->row_array();
		}
		if ($this->input->post()) {
			$code = $this->input->post('code');
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if (empty($id) || $data['item_quality']['code'] != $code) {
				if (!empty($id)) {
					$this->db->where('id', $id);
					$ktCodeCode = $this->db->get('tblcategory_test_item_quality')->row();
					if (!empty($ktCodeCode)) {
						$this->db->where('type', $ktCodeCode->type);
						$this->db->where('type_event', $ktCodeCode->type_event);
						$this->db->where('code', $code);
						$ktCode = $this->db->get('tblcategory_test_item_quality')->row();
						if (!empty($ktCode)) {
							echo json_encode(['result' => false, 'alert_type' => 'danger', 'Mã đã tồn tại vui lòng nhập mã khác']);
							die();
						}
					}
				} else {
					$this->db->where('type', $type);
					$this->db->where('type_event', $type_event);
					$this->db->where('code', $code);
					$ktCode = $this->db->get('tblcategory_test_item_quality')->row();
					if (!empty($ktCode)) {
						echo json_encode(['result' => false, 'alert_type' => 'danger', 'Mã đã tồn tại vui lòng nhập mã khác']);
						die();
					}
				}



				$this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tblcategory_test_item_quality.code]');
			}
			if ($this->form_validation->run() == true) {
				$name = $this->input->post('name');
				$constitutive = $this->input->post('constitutive');
				$note = $this->input->post('note');
				$standard = $this->input->post('standard');
				$tools = $this->input->post('tools');
				if (!empty($id)) {
					$options = [
						'name' => $name,
						'code' => $code,
						'constitutive' => $constitutive,
						'standard' => $standard,
						'tools' => $tools,
						'note' => $note,
					];
					$this->db->where('id', $id);
					$success = $this->db->update('tblcategory_test_item_quality', $options);
					if ($success) {
						$data['result'] = 1;
						$data['message'] = lang('Cập nhật thành công');
					} else {
						$data['result'] = 0;
						$data['message'] = lang('fail');
					}
				} else {
					$options = [
						'name' => $name,
						'code' => $code,
						'constitutive' => $constitutive,
						'standard' => $standard,
						'tools' => $tools,
						'note' => $note,
						'type' => $type,
						'type_event' => $type_event,
					];
					$success = $this->db->insert('tblcategory_test_item_quality', $options);
					if ($success) {
						$data['result'] = 1;
						$data['message'] = lang('Thêm thành công');
					} else {
						$data['result'] = 0;
						$data['message'] = lang('fail');
					}
				}
			} else {
				$data['result'] = 0;
				$data['message'] = validation_errors();
			}
			echo json_encode($data);
			return;
		} else {
			if (!empty($id)) {
				$data['title'] = 'Sửa hạng mục kiểm tra chất lượng';
				$data['item_quality'] = $this->db->get_where('tblcategory_test_item_quality', ['id' => $id])->row_array();
			} else {
				$data['title'] = 'Thêm mới hạng mục kiểm tra chất lượng';
				$data['type'] = $type;
				$data['type_event'] = $type_event;
			}
			$this->load->view('admin/categories/category_test_item_quality/modal', $data);
		}
	}

	public function get_category_test_item_quality($type = 'products', $type_event = '1')
	{
		$aColumns = [
			'id',
			'code',
			'name',
			'standard',
			'tools',
		];
		$sWhere = [
			'AND type = "' . $type . '"',
			'AND type_event = "' . $type_event . '"'
		];
		$sIndexColumn = 'id';
		$sTable       = 'tblcategory_test_item_quality';
		$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $sWhere);
		$output       = $result['output'];
		$rResult      = $result['rResult'];
		foreach ($rResult as $aRow) {
			$row = [];
			$row[] = $aRow['id'];
			$row[] = $aRow['code'];
			$row[] = $aRow['name'];
			$row[] = $aRow['standard'];
			$row[] = $aRow['tools'];
			$options = '';
			$options .= '<a class="btn btn-icon btn-default c_modal" href="' . (admin_url('categories/detail_category_test_item_quality/' . $aRow['id'])) . '" ><i class="fa fa-edit"></i></a>';
			$options .= '<a class="btn btn-icon btn-danger c_delete" href="' . (admin_url('categories/remove_category_test_item_quality')) . '" data-id="' . $aRow['id'] . '" ><i class="fa fa-remove"></i></a>';
			$row[] = $options;
			$output['aaData'][] = $row;
		}
		echo json_encode($output);
		die();
	}

	public function remove_category_test_item_quality()
	{
		$id = $this->input->post('id');
		if (!empty($id)) {
			$this->db->where('id', $id);
			$success = $this->db->delete('tblcategory_test_item_quality');
			if (!empty($success)) {
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Xóa dữ liệu thành công'
				]);
				return;
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa dữ liệu không thành công'
		]);
		return;
	}
}
