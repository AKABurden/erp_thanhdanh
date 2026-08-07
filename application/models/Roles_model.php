<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Roles_model extends App_Model
{
	/**
	 * Add new employee role
	 * @param mixed $data
	 */
	public function add($data)
	{
		$in_main = array(
			'name' => $data['name'],
			'departments_id' => $data['departments_id'],
			'roles_parent' => $data['roles_parent'],
			'coefficient' => !empty($data['coefficient']) ? number_unformat($data['coefficient']) : 0,
		);
		if (isset($data['code_role'])) {
			$in_main['code_role'] = $data['code_role'];
		}
		$arrCapacity = [];
		if (!empty($data['capacity'])) {
			$capacity = $data['capacity'];
			foreach ($capacity as $key => $value) {
				$type = !empty($data['type'][$key]) ? $data['type'][$key] : 0;
				// $name = !empty($data['name'][$key]) ? ;
				$point_weight = $data['point_weight'][$key];
			}
		}
		$this->db->insert('tblroles', $in_main);
		$insert_id_role = $this->db->insert_id();
		if ($insert_id_role) {
			log_activity('New Role Added [ID: ' . $insert_id_role . '.' . $data['name'] . ']');
			if (isset($data['permission'])) {
				foreach ($data['permission'] as $key => $value) {
					//					if (isset($value['parent'])) {
					//kiểm tra và thêm quyền parent
					$checkExists_parent = get_table_where('tbl_parents_permissions_v2', array('obj' => $key), '', 'row');
					if (!$checkExists_parent) {
						$this->db->insert('tbl_parents_permissions_v2', ['obj' => $key]);
						$insert_id_parent = $this->db->insert_id();
					} else {
						$insert_id_parent = $checkExists_parent->id;
					}
					//thêm quyền đc xem parent
					$obj_parent_permission = get_table_where('tbl_parents_permissions_v2', array('id' => $insert_id_parent), '', 'row');
					$this->db->insert('tbl_roles_parent_permission_v2', ['id_role' => $insert_id_role, 'obj_parent_permission' => $obj_parent_permission->obj, 'can_view' => 1]);
					//thêm quyền child
					if (isset($value['child'])) {
						foreach ($value['child'] as $key_child => $value_child) {
							//kiểm tra và thêm quyền parent
							$checkExists_child = get_table_where('tbl_permission_v2', array('obj_parent_permission' => $obj_parent_permission->obj, 'obj' => $key_child), '', 'row');
							if (!$checkExists_child) {
								$this->db->insert('tbl_permission_v2', ['obj_parent_permission' => $obj_parent_permission->obj, 'obj' => $key_child]);
								$insert_id_child = $this->db->insert_id();
							} else {
								$insert_id_child = $checkExists_child->id;
							}
							//phân quyền
							$obj_permission = get_table_where('tbl_permission_v2', array('id' => $insert_id_child), '', 'row');
							foreach ($value_child as $key_v => $value_v) {
								$checkExists_permission = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $insert_id_role, 'obj_permission' => $obj_permission->obj), '', 'row');
								if (!$checkExists_permission) {
									$this->db->insert('tbl_roles_child_permission_v2', ['id_role' => $insert_id_role, 'obj_permission' => $obj_permission->obj]);
									$insert_id_permission = $this->db->insert_id();
								} else {
									$insert_id_permission = $checkExists_permission->id;
								}
								$colum = 'can_' . $key_v;
								$this->db->set($colum, 1);
								$this->db->where('id', $insert_id_permission);
								$this->db->update('tbl_roles_child_permission_v2');
							}
						}
					}
					//					}
				}
			}
			return $insert_id_role;
		}
		return false;
	}

	/**
	 * Update employee role
	 * @param array $data role data
	 * @param mixed $id role id
	 * @return boolean
	 */
	public function update($data, $id)
	{
		$in_main = array(
			'name' => $data['name'],
			'departments_id' => $data['departments_id'],
			//				'roles_parent' => $data['roles_parent'],
			'coefficient' => !empty($data['coefficient']) ? number_unformat($data['coefficient']) : 0,
		);
		if (isset($data['code_role'])) {
			$in_main['code_role'] = $data['code_role'];
		}
		$in_main['name_position'] = $data['name_position'];
		$in_main['email'] = !empty($data['email']) ? $data['email'] : NULL;
		$in_main['coefficient_overtime'] = !empty($data['coefficient_overtime']) ? number_unformat($data['coefficient_overtime']) : 0;
		$in_main['id_board'] = !empty($data['id_board']) ? $data['id_board'] : NULL;
		$in_main['id_block'] = !empty($data['id_block']) ? $data['id_block'] : 0;
		$in_main['id_room'] = !empty($data['id_room']) ? $data['id_room'] : 0;
		$in_main['id_nest'] = !empty($data['id_nest']) ? $data['id_nest'] : 0;
		$in_main['id_group'] = !empty($data['id_group']) ? $data['id_group'] : 0;
		$in_main['salary_id'] = !empty($data['salary_id']) ? $data['salary_id'] : 0;
		$in_main['coefficient_salary_id'] = !empty($data['coefficient_salary_id']) ? $data['coefficient_salary_id'] : 0;
		$in_main['kpi_category_id'] = !empty($data['kpi_category_id']) ? $data['kpi_category_id'] : 0;
		$in_main['contract_id'] = !empty($data['contract_id']) ? $data['contract_id'] : 0;
		$in_main['paid_holiday_id'] = !empty($data['paid_holiday_id']) ? $data['paid_holiday_id'] : 0;

		$this->db->where('roleid', $id);
		$update_true = $this->db->update('tblroles', $in_main);

		if ($update_true) {
			log_activity('Role Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');
			//reset tất cả quyền
			$this->db->set('can_view', 0);
			$this->db->where('id_role', $id);
			$this->db->update('tbl_roles_parent_permission_v2');
			$reset_can_permission = array(
				'can_view' => 0,
				'can_view_own' => 0,
				'can_create' => 0,
				'can_edit' => 0,
				'can_print' => 0,
				'can_approve' => 0,
				'can_approve_warehouse' => 0,
				'can_approve_accept' => 0,
				'can_approve_qc' => 0,
				'can_approve_cancel' => 0,
				'can_import' => 0,
				'can_export' => 0,
				'can_delete' => 0,
				'can_cost' => 0,
				'can_profit' => 0,
				'can_notifications' => 0,
				'can_agree_notifications' => 0,
				'can_add_notifications' => 0,
				'can_qc' => 0,
				'can_export_outsource' => 0,
				'can_approve_manager' => 0,
				'can_approve_lbc' => 0,
				'can_approve_ncnxl' => 0,
				'can_approve_gspn' => 0,
				'can_approve_dg' => 0,
			);
			$this->db->where('id_role', $id);
			$this->db->update('tbl_roles_child_permission_v2', $reset_can_permission);
			//end
			if (isset($data['permission'])) {
				foreach ($data['permission'] as $key => $value) {
					// if (isset($value['parent'])) {
						//kiểm tra và thêm quyền parent
						$checkExists_parent = get_table_where('tbl_parents_permissions_v2', array('obj' => $key), '', 'row');
						if (!$checkExists_parent) {
							$this->db->insert('tbl_parents_permissions_v2', ['obj' => $key]);
							$insert_id_parent = $this->db->insert_id();
						} else {
							$insert_id_parent = $checkExists_parent->id;
						}
						//thêm quyền đc xem parent
						$obj_parent_permission = get_table_where('tbl_parents_permissions_v2', array('id' => $insert_id_parent), '', 'row');
						$check_can_view = get_table_where('tbl_roles_parent_permission_v2', array('id_role' => $id, 'obj_parent_permission' => $obj_parent_permission->obj), '', 'row');
						if (!$check_can_view) {
							$this->db->insert('tbl_roles_parent_permission_v2', ['id_role' => $id, 'obj_parent_permission' => $obj_parent_permission->obj, 'can_view' => 1]);
						} else {
							$this->db->set('can_view', 1);
							$this->db->where('id', $check_can_view->id);
							$this->db->update('tbl_roles_parent_permission_v2');
						}
						//thêm quyền child
						if (isset($value['child'])) {
							foreach ($value['child'] as $key_child => $value_child) {
								//kiểm tra và thêm quyền parent
								$checkExists_child = get_table_where('tbl_permission_v2', array('obj_parent_permission' => $obj_parent_permission->obj, 'obj' => $key_child), '', 'row');
								if (!$checkExists_child) {
									$this->db->insert('tbl_permission_v2', ['obj_parent_permission' => $obj_parent_permission->obj, 'obj' => $key_child]);
									$insert_id_child = $this->db->insert_id();
								} else {
									$insert_id_child = $checkExists_child->id;
								}
								//phân quyền
								$obj_permission = get_table_where('tbl_permission_v2', array('id' => $insert_id_child), '', 'row');
								foreach ($value_child as $key_v => $value_v) {
									$checkExists_permission = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $id, 'obj_permission' => $obj_permission->obj), '', 'row');
									if (!$checkExists_permission) {
										$this->db->insert('tbl_roles_child_permission_v2', ['id_role' => $id, 'obj_permission' => $obj_permission->obj]);
										$insert_id_permission = $this->db->insert_id();
									} else {
										$insert_id_permission = $checkExists_permission->id;
									}
									$colum = 'can_' . $key_v;
									$this->db->set($colum, 1);
									$this->db->where('id', $insert_id_permission);
									$this->db->update('tbl_roles_child_permission_v2');
								}
							}
						}
					// }
				}
			}
			if (isset($data['update_staff_permissions'])) {
				$get_all_staff = get_table_where('tblstaff', array('role' => $id));
				foreach ($get_all_staff as $key_staff => $value_staff) {
					//reset tất cả quyền
					$this->db->set('can_view', 0);
					$this->db->where('id_staff', $value_staff['staffid']);
					$this->db->update('tbl_staff_parent_permission_v2');
					$reset_can_permission = array(
						'can_view' => 0,
						'can_view_own' => 0,
						'can_create' => 0,
						'can_edit' => 0,
						'can_print' => 0,
						'can_approve' => 0,
						'can_approve_warehouse' => 0,
						'can_approve_accept' => 0,
						'can_approve_qc' => 0,
						'can_approve_cancel' => 0,
						'can_import' => 0,
						'can_export' => 0,
						'can_delete' => 0,
						'can_cost' => 0,
						'can_profit' => 0,
						'can_notifications' => 0,
						'can_agree_notifications' => 0,
						'can_add_notifications' => 0,
						'can_qc' => 0,
						'can_export_outsource' => 0,
						'can_approve_manager' => 0,
						'can_approve_lbc' => 0,
						'can_approve_ncnxl' => 0,
						'can_approve_gspn' => 0,
						'can_approve_dg' => 0,
					);
					$this->db->where('id_staff', $value_staff['staffid']);
					$this->db->update('tbl_staff_child_permission_v2', $reset_can_permission);
					//end
					if (isset($data['permission'])) {
						foreach ($data['permission'] as $key => $value) {
							// if (isset($value['parent'])) {
								//kiểm tra và thêm quyền parent
								$checkExists_parent = get_table_where('tbl_parents_permissions_v2', array('obj' => $key), '', 'row');
								if (!$checkExists_parent) {
									$this->db->insert('tbl_parents_permissions_v2', ['obj' => $key]);
									$insert_id_parent = $this->db->insert_id();
								} else {
									$insert_id_parent = $checkExists_parent->id;
								}
								//thêm quyền đc xem parent
								$obj_parent_permission = get_table_where('tbl_parents_permissions_v2', array('id' => $insert_id_parent), '', 'row');
								$check_can_view = get_table_where('tbl_staff_parent_permission_v2', array('id_staff' => $value_staff['staffid'], 'obj_parent_permission' => $obj_parent_permission->obj), '', 'row');
								if (!$check_can_view) {
									$this->db->insert('tbl_staff_parent_permission_v2', ['id_staff' => $value_staff['staffid'], 'obj_parent_permission' => $obj_parent_permission->obj, 'can_view' => 1]);
								} else {
									$this->db->set('can_view', 1);
									$this->db->where('id', $check_can_view->id);
									$this->db->update('tbl_staff_parent_permission_v2');
								}
								//thêm quyền child
								if (isset($value['child'])) {
									foreach ($value['child'] as $key_child => $value_child) {
										//kiểm tra và thêm quyền parent
										$checkExists_child = get_table_where('tbl_permission_v2', array('obj_parent_permission' => $obj_parent_permission->obj, 'obj' => $key_child), '', 'row');
										if (!$checkExists_child) {
											$this->db->insert('tbl_permission_v2', ['obj_parent_permission' => $obj_parent_permission->obj, 'obj' => $key_child]);
											$insert_id_child = $this->db->insert_id();
										} else {
											$insert_id_child = $checkExists_child->id;
										}
										//phân quyền
										$obj_permission = get_table_where('tbl_permission_v2', array('id' => $insert_id_child), '', 'row');
										foreach ($value_child as $key_v => $value_v) {
											$checkExists_permission = get_table_where('tbl_staff_child_permission_v2', array('id_staff' => $value_staff['staffid'], 'obj_permission' => $obj_permission->obj), '', 'row');
											if (!$checkExists_permission) {
												$this->db->insert('tbl_staff_child_permission_v2', ['id_staff' => $value_staff['staffid'], 'obj_permission' => $obj_permission->obj]);
												$insert_id_permission = $this->db->insert_id();
											} else {
												$insert_id_permission = $checkExists_permission->id;
											}
											$colum = 'can_' . $key_v;
											$this->db->set($colum, 1);
											$this->db->where('id', $insert_id_permission);
											$this->db->update('tbl_staff_child_permission_v2');
										}
									}
								}
							// }
						}
					}
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Get employee role by id
	 * @param mixed $id Optional role id
	 * @return mixed     array if not id passed else object
	 */
	public function get($id = '')
	{
		if (is_numeric($id)) {
			$role = $this->app_object_cache->get('role-' . $id);
			if ($role) {
				return $role;
			}
			$this->db->where('roleid', $id);
			$role = $this->db->get(db_prefix() . 'roles')->row();
			$role->permissions = !empty($role->permissions) ? unserialize($role->permissions) : [];
			$this->app_object_cache->add('role-' . $id, $role);
			return $role;
		}
		return $this->db->get(db_prefix() . 'roles')->result_array();
	}

	/**
	 * Delete employee role
	 * @param mixed $id role id
	 * @return mixed
	 */
	public function delete($id)
	{
		$current = $this->get($id);
		// Check first if role is used in table
		if (is_reference_in_table('role', db_prefix() . 'staff', $id)) {
			return [
				'referenced' => true,
			];
		}
		$affectedRows = 0;
		$this->db->where('roleid', $id);
		$this->db->delete(db_prefix() . 'roles');
		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}
		if ($affectedRows > 0) {
			log_activity('Role Deleted [ID: ' . $id);
			return true;
		}
		return false;
	}

	public function get_contact_permissions($id)
	{
		$this->db->where('userid', $id);
		return $this->db->get(db_prefix() . 'contact_permissions')->result_array();
	}

	public function get_role_staff($role_id)
	{
		$this->db->where('role', $role_id);
		return $this->db->get(db_prefix() . 'staff')->result_array();
	}

	public function getLevel()
	{
		$this->db->select('*');
		$this->db->from('tbl_level');
		return $this->db->get()->result_array();
	}

	public function getRoles()
	{
		$this->db->select('*');
		$this->db->from('tblroles');
		return $this->db->get()->result_array();
	}

	public function insertBatchRoleCapacity($data)
	{
		return $this->db->insert_batch('tbl_role_capacity', $data);
	}

	public function deleteRoleCapacity($role_id)
	{
		$this->db->where('tbl_role_capacity.role_id', $role_id);
		return $this->db->delete('tbl_role_capacity');
	}
}
