<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class List_vehicle extends AdminController
	{
		function __construct()
		{
			parent::__construct();
//			$this->HasView = has_permission('list_vehicle', '', 'view');
//			$this->HasCreate = has_permission('list_vehicle', '', 'create');
//			$this->HasEdit = has_permission('list_vehicle', '', 'edit');
//			$this->HasDelete = has_permission('list_vehicle', '', 'delete');
			
			$this->HasView = true;
			$this->HasCreate = true;
			$this->HasEdit = true;
			$this->HasDelete = true;
		}
		
		public function index() {
			if(!$this->HasView) {
				access_denied();
			}
			$data['title'] = _l('Bảng giá phương tiện - lộ trình');
			$this->load->view('admin/list_vehicle/manage', $data);
		}
		
		public function table()
		{
			$aColumns = [
				'tbl_list_vehicle.id as id',
				'tbl_list_vehicle.transporters as transporters',
				'tbl_list_vehicle.img as img',
				'tbl_list_vehicle.code_vehicle as code_vehicle',
				'tbl_list_vehicle.type_vehicle as type_vehicle',
				'tbl_list_vehicle.unit_name as unit_name',
				'tbl_list_vehicle.departure_point as departure_point',
				'tbl_list_vehicle.destination as destination',
				'tbl_list_vehicle.number_km as number_km',
				'tbl_list_vehicle.price as price',
				'tbl_list_vehicle.currency_unit as currency_unit',
			];
			$sIndexColumn = 'id';
			$sTable = 'tbl_list_vehicle';
			$where = [];
			if($this->input->post('search_transporters')) {
				$where[] = 'AND tbl_list_vehicle.transporters like "%'.$this->input->post('search_transporters').'%"';
			}
			if($this->input->post('search_code_vehicle')) {
				$where[] = 'AND tbl_list_vehicle.code_vehicle like "%'.$this->input->post('search_code_vehicle').'%"';
			}
			if($this->input->post('search_type_vehicle')) {
				$where[] = 'AND tbl_list_vehicle.type_vehicle like "%'.$this->input->post('search_type_vehicle').'%"';
			}
			$join = [];
			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
			$output = $result['output'];
			$rResult = $result['rResult'];
			foreach ($rResult as $key => $aRow) {
				$row = [];
				$row[] = $aRow['id'];
				$row[] = $aRow['transporters'];
				$row[] = !empty($aRow['img']) ? ViewHtmlImagesDt(base_url($aRow['img'])) : '';
				$row[] = $aRow['code_vehicle'];
				$row[] = $aRow['type_vehicle'];
				$row[] = $aRow['unit_name'];
				$row[] = $aRow['departure_point'];
				$row[] = $aRow['destination'];
				$row[] = '<div class="text-center">' . number_format_data($aRow['number_km']) . '</div>';
				$row[] = '<div class="text-center">' . number_format_data($aRow['price']) . '</div>';
				$row[] = $aRow['currency_unit'];
				$_options = '';
				if($this->HasEdit) {
					$_options .= '<a href="' . admin_url('list_vehicle/detail/' . $aRow['id']) . '" class="btn btn-default btn-icon c_modal"><i class="fa fa-edit"></i></a>';
				}
				if($this->HasDelete) {
					$_options .= '<a data-href="' . admin_url('list_vehicle/delete/' . $aRow['id']) . '" class="btn btn-danger btn-icon deleteItems"><i class="fa fa fa-remove"></i></a>';
				}
				$row[] = $_options;
				
				$output['aaData'][] = $row;
			}
			echo json_encode($output);
		}
		
		public function detail($id = '') {
			if($this->input->post()) {
				$data = $this->input->post();
				if(!empty($data)) {
					if(!empty($id)) {
						if(!$this->HasEdit) {
							ajax_access_denied();
						}
						$list_vehicle = $this->db->get_where('tbl_list_vehicle', ['id' => $id])->row();
						if($list_vehicle->code_vehicle != $data['code_vehicle']) {
							$this->db->where('code_vehicle', $data['code_vehicle']);
							$kt_isset_code_vehicle = $this->db->get('tbl_list_vehicle')->row();
							if(!empty($kt_isset_code_vehicle)) {
								echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Mã phương tiện - lộ trình đã tồn tại vui lòng nhập mã khác']);return;
							}
						}
						
						
						$this->db->where('id', $id);
						$success = $this->db->update('tbl_list_vehicle', [
							'transporters' => $data['transporters'],
							'code_vehicle' => $data['code_vehicle'],
							'type_vehicle' => $data['type_vehicle'],
							'unit_name' => $data['unit_name'],
							'departure_point' => !empty($data['departure_point']) ? $data['departure_point'] : NULL,
							'destination' => !empty($data['destination']) ? $data['destination'] : NULL,
							'number_km' => number_format_data($data['number_km'], false),
							'price' => number_format_data($data['price'], false),
							'currency_unit' => !empty($data['currency_unit']) ? $data['currency_unit'] : NULL,
							'note' => !empty($data['note']) ? $this->db->input('note', false) : NULL,
						]);
						if(!empty($success)) {
							if (!empty($_FILES['img'])) {
								$pathLinkMain = LIST_VEHICLE . '/';
								if (!file_exists($pathLinkMain)) {
									mkdir($pathLinkMain);
								}
								
								$pathLink = LIST_VEHICLE . '/' . $id . '/';
								if (!file_exists($pathLink)) {
									mkdir($pathLink);
								}
								$_FILES['img']['name'] = time() . '_' . rand(1, 100000) . rand(1, 100000) . '_' . mb_strtolower(preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['img']['name'])), 'UTF-8');
								if (is_uploaded_file($_FILES['img']['tmp_name'])) {
									$source_path_image = $_FILES['img']['tmp_name'];
									$target_path_image = $pathLink . $_FILES['img']['name'];
									if (move_uploaded_file($source_path_image, $target_path_image)) {
										$img = 'uploads/list_vehicle/' . $id . '/' . $_FILES['img']['name'];
										if(!empty($img)) {
											$this->db->where('id', $id);
											$this->db->update('tbl_list_vehicle', [
												'img' => $img
											]);
											if(!empty($list_vehicle->img)) {
												$linkUnlink = FCPATH . $list_vehicle->img;
												unlink($linkUnlink);
											}
										}
									}
								}
							}
							
							echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Cập nhật thành công']);return;
						}
						echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Cập nhật không thành công']);return;
					}
					else {
						if(!$this->HasCreate) {
							ajax_access_denied();
						}
						
						$this->db->where('code_vehicle', $data['code_vehicle']);
						$kt_isset_code_vehicle = $this->db->get('tbl_list_vehicle')->row();
						if(!empty($kt_isset_code_vehicle)) {
							echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Mã phương tiện - lộ trình đã tồn tại vui lòng nhập mã khác']);return;
						}
						
						$success = $this->db->insert('tbl_list_vehicle', [
							'transporters' => $data['transporters'],
							'code_vehicle' => $data['code_vehicle'],
							'type_vehicle' => $data['type_vehicle'],
							'unit_name' => $data['unit_name'],
							'departure_point' => !empty($data['departure_point']) ? $data['departure_point'] : NULL,
							'destination' => !empty($data['destination']) ? $data['destination'] : NULL,
							'number_km' => number_format_data($data['number_km'], false),
							'price' => number_format_data($data['price'], false),
							'currency_unit' => !empty($data['currency_unit']) ? $data['currency_unit'] : NULL,
							'create_by' => get_staff_user_id(),
							'note' => !empty($data['note']) ? $this->db->input('note', false) : NULL,
						]);
						if(!empty($success)) {
							$id = $this->db->insert_id();
							if (!empty($_FILES['img'])) {
								$pathLinkMain = LIST_VEHICLE . '/';
								if (!file_exists($pathLinkMain)) {
									mkdir($pathLinkMain);
								}
								
								$pathLink = LIST_VEHICLE . '/' . $id . '/';
								if (!file_exists($pathLink)) {
									mkdir($pathLink);
								}
								$_FILES['img']['name'] = time() . '_' . rand(1, 100000) . rand(1, 100000) . '_' . mb_strtolower(preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['img']['name'])), 'UTF-8');
								if (is_uploaded_file($_FILES['img']['tmp_name'])) {
									$source_path_image = $_FILES['img']['tmp_name'];
									$target_path_image = $pathLink . $_FILES['img']['name'];
									if (move_uploaded_file($source_path_image, $target_path_image)) {
										$img = 'uploads/list_vehicle/' . $id . '/' . $_FILES['img']['name'];
										if(!empty($img)) {
											$this->db->where('id', $id);
											$this->db->update('tbl_list_vehicle', [
												'img' => $img
											]);
										}
									}
								}
							}
							echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Thêm dữ liệu thành công']);return;
						}
						else {
							echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Thêm dữ liệu không thành công']);return;
						}
					}
				}
			}
			else {
				$data['title'] = 'Thêm bảng giá phương tiện - lộ trình';
				if(!empty($id)) {
					if(!$this->HasEdit) {
						access_denied();
					}
					$data['title'] = 'Sửa bảng giá phương tiện - lộ trình';
					$data['list_vehicle'] = $this->db->get_where('tbl_list_vehicle', ['id' => $id])->row();
				}
				else {
					if(!$this->HasCreate) {
						access_denied();
					}
				}
				$this->load->view('admin/list_vehicle/detail', $data);
			}
		}
		
		public function delete($id = '') {
			if(!$this->HasDelete) {
				ajax_access_denied();
			}
			$is_delete = $this->input->post('is_delete');
			if(!empty($id) && !empty($is_delete)) {
				$this->db->where('id', $id);
				$list_vehicle = $this->db->get('tbl_list_vehicle')->row();
				if(!empty($list_vehicle)) {
					$this->db->where('id', $id);
					$delete = $this->db->delete('tbl_list_vehicle');
					if(!empty($delete)) {
						if(!empty($list_vehicle->img)) {
							$linkUnlink = FCPATH . $list_vehicle->img;
							unlink($linkUnlink);
						}
						echo json_encode([
							'success' => true,
							'alert_type' => 'success',
							'message' => 'Xóa dữ liệu thành công'
						]);die();
					}
				}
			}
			echo json_encode([
				'success' => false,
				'alert_type' => 'danger',
				'message' => 'Xóa dữ liệu không thành công'
			]);die();
		}
	}
