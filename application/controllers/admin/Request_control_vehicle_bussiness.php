<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_control_vehicle_bussiness extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('manufactures_model');
        $this->load->model('purchases_model');
        $this->load->model('business_plan_model');
        $this->load->model('orders_model');
        $this->load->model('departments_model');
        $this->load->model('stock_model');
        $this->load->model('tools_supplies_model');
        $this->load->model('transfer_model');

        $this->preViewRequestControlVehicleBussiness = true;
        $this->preViewOwnRequestControlVehicleBussiness = true;
        $this->preAddRequestControlVehicleBussiness = true;
        $this->preEditRequestControlVehicleBussiness = true;
        $this->preApproveRequestControlVehicleBussiness = true;
        $this->preDeleteRequestControlVehicleBussiness = true;
    }

    public function index()
    {
        if (!$this->preViewRequestControlVehicleBussiness && !$this->preViewOwnRequestControlVehicleBussiness) {
            access_denied();
        }
        $data['title'] = lang('dt_request_control_vehicle_bussiness');
        $this->load->view('admin/request_control_vehicle_bussiness/index', $data);
    }

    public function getRequestControlVehicleBussiness()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_request_control_vehicle_bussiness.id as id',
            'tbl_request_control_vehicle_bussiness.reference_no as reference_no',
            'tbl_request_control_vehicle_bussiness.date as date',
            'tbl_list_vehicle.code_vehicle as vehicle_name',
            'CONCAT(employees.firstname," ",employees.lastname) as fullname',
			'IF(
				tbl_request_control_vehicle_bussiness.object_type = "delivery",
					(
						SELECT GROUP_CONCAT(tbl_deliveries.reference_no SEPARATOR "<br/>")
						FROM tbl_deliveries WHERE FIND_IN_SET(tbl_deliveries.id, tbl_request_control_vehicle_bussiness.object_id)
					),
					(
						IF(tbl_request_control_vehicle_bussiness.object_type = "purchase_order",
							(
								SELECT GROUP_CONCAT(CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code) SEPARATOR "<br/>")
								FROM tblpurchase_order WHERE FIND_IN_SET(tblpurchase_order.id, tbl_request_control_vehicle_bussiness.object_id)
							),
							IF(tbl_request_control_vehicle_bussiness.object_type = "request_bussiness",
								(
									SELECT GROUP_CONCAT(tbl_request_bussiness.reference_no SEPARATOR "<br/>")
									FROM tbl_request_bussiness WHERE tbl_request_bussiness.id = cast(tbl_request_control_vehicle_bussiness.object_id as int)
								),
								IF(
									tbl_request_control_vehicle_bussiness.object_type = "suggest_outsource", (
										SELECT GROUP_CONCAT(tbl_suggest_outsource.reference_no SEPARATOR "<br/>")
										FROM tbl_suggest_outsource WHERE tbl_suggest_outsource.id = cast(tbl_request_control_vehicle_bussiness.object_id as int)
									),
									IF(tbl_request_control_vehicle_bussiness.object_type = "other", tbl_request_control_vehicle_bussiness.object_id, "")
								)
							)
						)
					)
			) as reference_no_bussiness',
            'tbl_request_control_vehicle_bussiness.type_vehicle as type_vehicle',
            'tbl_request_control_vehicle_bussiness.number_km as number_km',
            'tbl_request_control_vehicle_bussiness.quota_gasoline as quota_gasoline',
            'tbl_request_control_vehicle_bussiness.cost_tolls as cost_tolls',
            'tbl_request_control_vehicle_bussiness.price as price',
            'tbl_request_control_vehicle_bussiness.amount as amount',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_control_vehicle_bussiness';
        $where = [];

        $join = [
            'INNER JOIN tblstaff employees ON employees.staffid = tbl_request_control_vehicle_bussiness.staff_id',
            'LEFT JOIN tbl_list_vehicle ON tbl_list_vehicle.id = tbl_request_control_vehicle_bussiness.vehicle_name',
//            'LEFT JOIN tbl_request_bussiness ON tbl_request_bussiness.id = tbl_request_control_vehicle_bussiness.object_id AND tbl_request_control_vehicle_bussiness.object_type = "request_bussiness"',
//            'LEFT JOIN tbl_deliveries ON FIND_IN_SET(tbl_deliveries.id, tbl_request_control_vehicle_bussiness.object_id) AND tbl_request_control_vehicle_bussiness.object_type = "delivery"',
        ];

        if (!$this->preViewRequestControlVehicleBussiness) {
            array_push($where, 'AND tbl_request_control_vehicle_bussiness.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_control_vehicle_bussiness.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_control_vehicle_bussiness.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
//            'tbl_request_bussiness.id as request_bussiness_id',
			'tbl_request_control_vehicle_bussiness.object_type'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
			$object_type = '';
			if(!empty($aRow['object_type'])) {
				$object_type= _l('object_type_' . $aRow['object_type']).'';
			}
			
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_control_vehicle_bussiness/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['vehicle_name']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['fullname']) . '</div>';
            $row[] = '<div class="text-left">' .$object_type. '</div>';
            $row[] = '<div class="text-left">' . $aRow['reference_no_bussiness'] . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['type_vehicle']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['number_km']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['quota_gasoline']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['cost_tolls']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['price']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['amount']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_control_vehicle_bussiness/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditRequestControlVehicleBussiness ? '<a class="tnh-modal" href="' . base_url('admin/request_control_vehicle_bussiness/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteRequestControlVehicleBussiness ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_control_vehicle_bussiness/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0)
    {
        $data = [];
        $dtData = [];
        $this->db->select('tbl_request_control_vehicle_bussiness.*');
        $this->db->from('tbl_request_control_vehicle_bussiness');
        $this->db->where('tbl_request_control_vehicle_bussiness.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
			$object_id = $this->input->post('object_id');
			$employees = $this->input->post('employees');
			$object_type = $this->input->post('object_type');
			$object_main = $this->input->post('object_main');
			$employees_other = $this->input->post('employees_other');
			$time_start = $this->input->post('time_start');
			$time_start = !empty($time_start) ? to_sql_date($time_start, true) : NULL;
			$time_end = $this->input->post('time_end');
			$time_end = !empty($time_end) ? to_sql_date($time_end, true) : NULL;
			$note = $this->input->post('note', false);
	
			$address = $this->input->post('address');
			$phone = $this->input->post('phone');
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_request_control_vehicle_bussiness.reference_no]');
				$this->form_validation->set_rules('date', lang("date"), 'required');
				$this->form_validation->set_rules('staff_id', lang("Người phụ trách"), 'required');
				$this->form_validation->set_rules('object_type', lang("Lý do điều xe"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('request_control_vehicle_bussiness');
                    $date = to_sql_date($this->input->post('date'), true);
                    $staff_id = $this->input->post('staff_id');
                    $request_bussiness_id = $this->input->post('request_bussiness_id');
                    $vehicle_name = $this->input->post('vehicle_name');
                    $type_vehicle = $this->input->post('type_vehicle');
                    $number_km = number_unformat($this->input->post('number_km'));
					if(empty($number_km)) {
						$number_km = 0;
					}
                    $quota_gasoline = number_unformat($this->input->post('quota_gasoline'));
					if(empty($quota_gasoline)) {
						$quota_gasoline = 0;
					}
                    $cost_tolls = number_unformat($this->input->post('cost_tolls'));
					if(empty($cost_tolls)) {
						$cost_tolls = 0;
					}
                    $price = number_unformat($this->input->post('price'));
					if(empty($price)) {
						$price = 0;
					}
					$amount = ($price * $number_km) + $quota_gasoline + $cost_tolls;
	
					$branch_id = $this->input->post('branch_id');
					$branch_id = !empty($branch_id) ? $branch_id : NULL;

//                    $dtRequestBussiness = get_table_where('tbl_request_bussiness',['id' => $request_bussiness_id],'','row_array');
//                    $branch_id = !empty($dtRequestBussiness['branch_id']) ? $dtRequestBussiness['branch_id'] : NULL;

                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'staff_id' => $staff_id,
                        'vehicle_name' => $vehicle_name,
                        'type_vehicle' => $type_vehicle,
                        'number_km' => $number_km,
                        'quota_gasoline' => $quota_gasoline,
                        'cost_tolls' => $cost_tolls,
                        'price' => $price,
                        'amount' => $amount,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => !empty($branch_id) ? $branch_id : NULL,
						
                        'object_type' => $object_type,
                        'object_main' => $object_main,
                        'object_id' => $object_id,
                        'employees_other' => $employees_other,
                        'time_start' => $time_start,
                        'time_end' => $time_end,
                        'address' => $address,
                        'phone' => $phone,
                        'note' => $note,
                    ];
                    $this->db->insert('tbl_request_control_vehicle_bussiness', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('request_control_vehicle_bussiness') == $reference_no) {
                            updateReference('request_control_vehicle_bussiness');
                        }
						
						if($object_type == 'delivery' || $object_type == 'purchase_order') {
							$list_object_id = explode(',', $object_id);
							foreach($list_object_id as $key => $value) {
								$this->db->insert('tbl_request_control_vehicle_bussiness_object', [
									'id_vehicle_bussiness' => $id,
									'object_id' => $value,
								]);
							}
						}
						else {
							$this->db->insert('tbl_request_control_vehicle_bussiness_object', [
								'id_vehicle_bussiness' => $id,
								'object_id' => $object_id,
							]);
						}
						
						if(!empty($employees) && is_array($employees)) {
							foreach($employees as $key => $value) {
								$this->db->insert('tbl_request_control_vehicle_bussiness_employees', [
									'id_vehicle_bussiness' => $id,
									'employees' => $value,
								]);
							}
						}
						
                        insertActivityLog([
                            'type_parent_obj' => 'request_control_vehicle_bussiness',
                            'table_obj' => 'tbl_request_control_vehicle_bussiness',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu điều xe công tác') . ' [' . $reference_no . ']',
                            'actions' => 'add'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            } else {
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_request_control_vehicle_bussiness.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('staff_id', lang("Người phụ trách"), 'required');
                $this->form_validation->set_rules('object_type', lang("Lý do điều xe"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $date = to_sql_date($this->input->post('date'), true);
                    $staff_id = $this->input->post('staff_id');
                    $vehicle_name = $this->input->post('vehicle_name');
                    $type_vehicle = $this->input->post('type_vehicle');
					$number_km = $this->input->post('number_km');
					$note = $this->input->post('note', false);
					if(empty($number_km)) {
						$number_km = 0;
					}
					$quota_gasoline = number_unformat($this->input->post('quota_gasoline'));
					if(empty($quota_gasoline)) {
						$quota_gasoline = 0;
					}
					$cost_tolls = number_unformat($this->input->post('cost_tolls'));
					if(empty($cost_tolls)) {
						$cost_tolls = 0;
					}
					$price = number_unformat($this->input->post('price'));
					if(empty($price)) {
						$price = 0;
					}
					$amount = ($price * $number_km) + $quota_gasoline + $cost_tolls;

                    $branch_id = $this->input->post('branch_id');
					$branch_id = !empty($branch_id) ? $branch_id : NULL;
                    $fields = [
                        'date' => $date,
                        'staff_id' => $staff_id,
                        'vehicle_name' => $vehicle_name,
                        'type_vehicle' => $type_vehicle,
                        'number_km' => $number_km,
                        'quota_gasoline' => $quota_gasoline,
                        'cost_tolls' => $cost_tolls,
                        'price' => $price,
                        'amount' => $amount,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
	
						'object_type' => $object_type,
						'object_main' => $object_main,
						'object_id' => $object_id,
						'employees_other' => $employees_other,
						'time_start' => $time_start,
						'time_end' => $time_end,
						'address' => $address,
						'phone' => $phone,
						'note' => $note,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_request_control_vehicle_bussiness', $fields);
                    if ($success) {
	
	
						if($object_type == 'delivery' || $object_type == 'purchase_order') {
							$list_object_id = explode(',', $object_id);
							if(!empty($list_object_id)) {
								$this->db->where_not_in('object_id', $list_object_id);
							}
							$this->db->where('id_vehicle_bussiness', $id);
							$this->db->delete('tbl_request_control_vehicle_bussiness_object');
							if(!empty($list_object_id)) {
								foreach ($list_object_id as $key => $value) {
									$this->db->insert('tbl_request_control_vehicle_bussiness_object', [
										'id_vehicle_bussiness' => $id,
										'object_id' => $value,
									]);
								}
							}
						}
						else {
							$this->db->insert('tbl_request_control_vehicle_bussiness_object', [
								'id_vehicle_bussiness' => $id,
								'object_id' => $object_id,
							]);
						}
	
						if(!empty($employees) && is_array($employees)) {
							if(!empty($employees)) {
								$this->db->where_not_in('employees', $employees);
							}
							$this->db->where('id_vehicle_bussiness', $id);
							$this->db->delete('tbl_request_control_vehicle_bussiness_employees');
							if(!empty($employees)) {
								foreach ($employees as $key => $value) {
									$this->db->insert('tbl_request_control_vehicle_bussiness_employees', [
										'id_vehicle_bussiness' => $id,
										'employees' => $value,
									]);
								}
							}
						}
						
						
                        insertActivityLog([
                            'type_parent_obj' => 'request_control_vehicle_bussiness',
                            'table_obj' => 'tbl_request_control_vehicle_bussiness',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu điều xe công tác') . ' [' . $dtData['reference_no'] . ']',
                            'actions' => 'edit'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Sửa thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            }
        } else {
            if (empty($id)) {
                if (!$this->preAddRequestControlVehicleBussiness) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_request_control_vehicle_bussiness');
            } else {
                if (!$this->preEditRequestControlVehicleBussiness) {
                    accessDenied(true);
                }
				
				$this->db->select('GROUP_CONCAT(employees) as employees');
				$this->db->where('id_vehicle_bussiness',  $id);
				$dtData['employees'] = $this->db->get('tbl_request_control_vehicle_bussiness_employees')->row('employees');
				$dtData['employees'] = explode(',', $dtData['employees']);

                $data['title'] = lang('dt_edit_request_control_vehicle_bussiness');
            }
        }
        $data['dtData'] = $dtData;
        $data['employees'] = $this->manufactures_model->getAllStaffRole();
		$data['list_vehicle'] = $this->db->get_where('tbl_list_vehicle')->result_array();
        $data['id'] = $id;
        $data['reference_no'] = getReference('request_control_vehicle_bussiness');
        $this->load->view('admin/request_control_vehicle_bussiness/detail', $data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('dt_view_request_control_vehicle_bussiness');

        $this->db->select('
            tbl_request_control_vehicle_bussiness.id as id,
            tbl_request_control_vehicle_bussiness.reference_no as reference_no,
            tbl_request_control_vehicle_bussiness.date as date,
            tbl_list_vehicle.code_vehicle as vehicle_name,
            tbl_request_control_vehicle_bussiness.branch_id as branch_id,
            tbl_request_control_vehicle_bussiness.created_by as created_by,
            tbl_request_control_vehicle_bussiness.date_created as date_created,
            tbl_request_control_vehicle_bussiness.updated_by as updated_by,
            tbl_request_control_vehicle_bussiness.date_updated as date_updated,
            CONCAT(employees.firstname," ",employees.lastname) as fullname,
            tbl_request_control_vehicle_bussiness.type_vehicle as type_vehicle,
            tbl_request_control_vehicle_bussiness.number_km as number_km,
            tbl_request_control_vehicle_bussiness.quota_gasoline as quota_gasoline,
            tbl_request_control_vehicle_bussiness.cost_tolls as cost_tolls,
            tbl_request_control_vehicle_bussiness.price as price,
            tbl_request_control_vehicle_bussiness.amount as amount,
            tbl_request_control_vehicle_bussiness.object_type as object_type,
            tbl_request_control_vehicle_bussiness.object_main as object_main,
            tbl_request_control_vehicle_bussiness.time_start as time_start,
            tbl_request_control_vehicle_bussiness.time_end as time_end,
            tbl_request_control_vehicle_bussiness.address as address,
            tbl_request_control_vehicle_bussiness.phone as phone,
            tbl_request_control_vehicle_bussiness.employees_other as employees_other,
        ');
		$this->db->select('IF(
								tbl_request_control_vehicle_bussiness.object_type = "delivery",
										(
										SELECT GROUP_CONCAT(tbl_deliveries.reference_no SEPARATOR "<br/>")
											FROM tbl_deliveries WHERE FIND_IN_SET(tbl_deliveries.id, tbl_request_control_vehicle_bussiness.object_id)
										),
										(
											IF(tbl_request_control_vehicle_bussiness.object_type = "purchase_order",
												(
												SELECT GROUP_CONCAT(CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code) SEPARATOR "<br/>")
													FROM tblpurchase_order WHERE FIND_IN_SET(tblpurchase_order.id, tbl_request_control_vehicle_bussiness.object_id)
												),
												IF(tbl_request_control_vehicle_bussiness.object_type = "request_bussiness",
													(
													SELECT GROUP_CONCAT(tbl_request_bussiness.reference_no SEPARATOR "<br/>")
														FROM tbl_request_bussiness WHERE tbl_request_bussiness.id = cast(tbl_request_control_vehicle_bussiness.object_id as int)
													),
													IF(tbl_request_control_vehicle_bussiness.object_type = "other", tbl_request_control_vehicle_bussiness.object_id, "")
												)
											)
										)
								) as reference_no_bussiness'
		);
        $this->db->from('tbl_request_control_vehicle_bussiness');
        $this->db->join('tblstaff employees', 'employees.staffid = tbl_request_control_vehicle_bussiness.staff_id', 'inner');
        $this->db->join('tbl_list_vehicle', 'tbl_list_vehicle.id = tbl_request_control_vehicle_bussiness.vehicle_name', 'left');
	
        $this->db->where('tbl_request_control_vehicle_bussiness.id', $id);
        $dtData = $this->db->get()->row_array();
		
		if($dtData['object_type'] == 'delivery') {
			$dtData['company'] = $this->db->get_where('tblclients', ['userid' => $dtData['object_main']])->row('company');
		}
		else if($dtData['object_type'] == 'purchase_order') {
			$dtData['company'] = $this->db->get_where('tblsuppliers', ['id' => $dtData['object_main']])->row('company');
		}
		
		
		$this->db->select('GROUP_CONCAT(concat(COALESCE(firstname, ""), " ", COALESCE(lastname, "")) SEPARATOR "<br/>") as list_full_staff');
		$this->db->join('tblstaff', 'tblstaff.staffid = tbl_request_control_vehicle_bussiness_employees.employees');
		$dtData['employees'] = $this->db->get_where('tbl_request_control_vehicle_bussiness_employees', ['id_vehicle_bussiness' => $dtData['id']])->row('list_full_staff');
		

        $data['dtData'] = $dtData;
        $this->load->view('admin/request_control_vehicle_bussiness/view', $data);
    }

    public function delete($id)
    {
        if (!$this->preDeleteRequestControlVehicleBussiness) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_request_control_vehicle_bussiness.*');
        $this->db->from('tbl_request_control_vehicle_bussiness');
        $this->db->where('tbl_request_control_vehicle_bussiness.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_request_control_vehicle_bussiness');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'request_control_vehicle_bussiness',
                'table_obj' => 'tbl_request_control_vehicle_bussiness',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu điều xe công tác') . ' [' . $dtData['reference_no'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function exportExcel()
    {
        $columsExcel = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
        ];
        if ($this->input->post('export_excel')) {

            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_dt/phieu_yeu_cau_dieu_xe.xlsx';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $BStylenumber = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '111112'),
                    'size'  => 11,
                    'name'  => 'Times New Roman'
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            );
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $row = 2;
            $staff_id = get_staff_user_id();
            $this->db->select('
                tbl_request_control_vehicle_bussiness.id as id,
                tbl_request_control_vehicle_bussiness.reference_no as reference_no,
                tbl_request_control_vehicle_bussiness.date as date,
                tbl_list_vehicle.code_vehicle as vehicle_name,
                tbl_request_control_vehicle_bussiness.branch_id as branch_id,
                tbl_request_control_vehicle_bussiness.created_by as created_by,
                tbl_request_control_vehicle_bussiness.date_created as date_created,
                tbl_request_control_vehicle_bussiness.updated_by as updated_by,
                tbl_request_control_vehicle_bussiness.date_updated as date_updated,
                CONCAT(employees.firstname," ",employees.lastname) as fullname,
                tbl_request_bussiness.reference_no as reference_no_bussiness,
                tbl_request_control_vehicle_bussiness.type_vehicle as type_vehicle,
                tbl_request_control_vehicle_bussiness.number_km as number_km,
                tbl_request_control_vehicle_bussiness.quota_gasoline as quota_gasoline,
                tbl_request_control_vehicle_bussiness.cost_tolls as cost_tolls,
                tbl_request_control_vehicle_bussiness.price as price,
                tbl_request_control_vehicle_bussiness.amount as amount,
                tbl_request_control_vehicle_bussiness.object_type as object_type,
            ');
			$this->db->select('IF(
				tbl_request_control_vehicle_bussiness.object_type = "delivery",
					(
						SELECT GROUP_CONCAT(tbl_deliveries.reference_no SEPARATOR "</br>")
						FROM tbl_deliveries WHERE FIND_IN_SET(tbl_deliveries.id, tbl_request_control_vehicle_bussiness.object_id)
					),
					(
						IF(tbl_request_control_vehicle_bussiness.object_type = "purchase_order",
							(
								SELECT GROUP_CONCAT(CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code) SEPARATOR "</br>")
								FROM tblpurchase_order WHERE FIND_IN_SET(tblpurchase_order.id, tbl_request_control_vehicle_bussiness.object_id)
							),
							IF(tbl_request_control_vehicle_bussiness.object_type = "request_bussiness",
								(
									SELECT GROUP_CONCAT(tbl_request_bussiness.reference_no SEPARATOR "</br>")
									FROM tbl_request_bussiness WHERE tbl_request_bussiness.id = cast(tbl_request_control_vehicle_bussiness.object_id as int)
								),
								IF(
									tbl_request_control_vehicle_bussiness.object_type = "suggest_outsource", (
										SELECT GROUP_CONCAT(tbl_suggest_outsource.reference_no SEPARATOR "</br>")
										FROM tbl_suggest_outsource WHERE tbl_suggest_outsource.id = cast(tbl_request_control_vehicle_bussiness.object_id as int)
									),
									IF(tbl_request_control_vehicle_bussiness.object_type = "other", tbl_request_control_vehicle_bussiness.object_id, "")
								)
							)
						)
					)
			) as reference_no_bussiness');
            $this->db->from('tbl_request_control_vehicle_bussiness');
            $this->db->join('tblstaff employees', 'employees.staffid = tbl_request_control_vehicle_bussiness.staff_id', 'inner');
            $this->db->join('tbl_request_bussiness', 'tbl_request_bussiness.id = tbl_request_control_vehicle_bussiness.request_bussiness_id', 'left');
            $this->db->join('tbl_list_vehicle', 'tbl_list_vehicle.id = tbl_request_control_vehicle_bussiness.vehicle_name', 'left');
            if (!$this->preViewRequestControlVehicleBussiness) {
                $this->db->where('(tbl_request_control_vehicle_bussiness.created_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_control_vehicle_bussiness.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_control_vehicle_bussiness.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_request_control_vehicle_bussiness.id asc');
            $items = $this->db->get()->result_array();
            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
				$reference_no_bussiness = str_replace('</br>', "\n", $value['reference_no_bussiness']);
                $type_vehicle = str_replace('&lt;', "<", $value['type_vehicle']);
                $row++;
                $dem++;
                $colStt = 0;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $dem);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['vehicle_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['fullname']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
				
				$object_type = '';
				if(!empty($value['object_type'])) {
					$object_type = lang('object_type_' . $value['object_type']);
				}
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $object_type, PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
				$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $reference_no_bussiness, PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $value['type_vehicle']);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['number_km']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['quota_gasoline']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['cost_tolls']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, formatMoney($value['price']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, formatMoney($value['amount']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'request_control_vehicle_bussiness||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_request_control_vehicle_bussiness', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/request_control_vehicle_bussiness/';
                if (!file_exists($folder)) {
                    mkdir($folder);
                    fopen($folder . 'index.html', 'w');
                }
                if (!file_exists($folder . 'qrcode' . '/')) {
                    mkdir($folder . 'qrcode' . '/');
                    fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
                }
                $params['data'] = $code;
                $params['level'] = 'H';
                $params['size'] = 40;
                $params['savename'] = $folder . 'qrcode/' . $qr . '.png';
                $this->ciqrcode->generate($params);
                $img = ($folder . 'qrcode/' . $qr . '.png');
                if (!empty($img)) {
                    $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                    $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                    $objDrawing1->setPath($img);
                    $objDrawing1->setWidth(90);
                    $objDrawing1->setHeight(65);
                    $objDrawing1->setOffsetX(20);
                    $objDrawing1->setOffsetY(2);
                    $objDrawing1->setCoordinates($columsExcel[$colStt] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, '')->getStyle($columsExcel[$colStt] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle("$columsExcel[0]$row:$columsExcel[$colStt]$row")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle("$columsExcel[0]$row:$columsExcel[$colStt]$row")->applyFromArray([
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
            }


            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_yeu_cau_dieu_xe') . '.xls';
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }
	
    public function searchRequestBussiness($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_request_bussiness.id as id, 
            tbl_request_bussiness.reference_no as text,
        ', false);
        $this->db->from('tbl_request_bussiness');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_request_bussiness.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtData = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Phiếu yêu cầu công tác'), 'children' => $dtData];
        if (!empty($id)) {
            $dtDataRow = get_table_where('tbl_request_bussiness', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtDataRow['id'], 'text' => $dtDataRow['reference_no']];
        }
        echo json_encode($data);
    }
	
	public function searchObject($id = 0) {
		$type_object = $this->input->get('type');
		$object_main = $this->input->get('object_main');
		$term = $this->input->get('term');
		$limit = get_option('select2_limit');
		if(empty($id)) {
			if ($type_object == 'request_bussiness') {
				$this->db->select('
					tbl_request_bussiness.id as id,
					tbl_request_bussiness.reference_no as text,
					tbl_request_bussiness.address as address,
					tbl_request_bussiness.phone as phone,
					tbl_request_bussiness.branch_id as branch_id
				', false);
				$this->db->from('tbl_request_bussiness');
				if (!empty($term)) {
					$this->db->group_start();
					$this->db->like('tbl_request_bussiness.reference_no', $term);
					$this->db->group_end();
				}
				$this->db->limit($limit);
				$pod = $this->db->get()->result_array();
				$data['results'][] = [
					'text' => lang('Phiếu yêu cầu công tác'),
					'children' => $pod
				];
			}
			else if ($type_object == 'suggest_outsource') {
				$this->db->select('
					tbl_suggest_outsource.id as id,
					tbl_suggest_outsource.reference_no as text,
					"" as address,
					"" as phone,
					tbl_suggest_outsource.branch_id as branch_id
				', false);
				$this->db->from('tbl_suggest_outsource');
				if (!empty($term)) {
					$this->db->group_start();
					$this->db->like('tbl_suggest_outsource.reference_no', $term);
					$this->db->group_end();
				}
				$this->db->limit($limit);
				$pod = $this->db->get()->result_array();
				$data['results'][] = [
					'text' => lang('Phiếu yêu cầu gia công'),
					'children' => $pod
				];
			}
			else if ($type_object == 'delivery') {
				if(!empty($object_main)) {
					$this->db->select('
						tbl_deliveries.id as id,
						tbl_deliveries.reference_no as text,
						tblshipping_client.address as address,
						tblshipping_client.phone as phonenumber,
						tbl_deliveries.id_branch as branch_id
						', false);
					$this->db->where('tbl_deliveries.customer_id', $object_main);
					$this->db->from('tbl_deliveries');
					$this->db->join('tblshipping_client', 'tblshipping_client.id = tbl_deliveries.address_delivery_id', 'left');
					if (!empty($term)) {
						$this->db->group_start();
						$this->db->like('tbl_deliveries.reference_no', $term);
						$this->db->group_end();
					}
					$this->db->limit($limit);
					$pod = $this->db->get()->result_array();
				}
				else {
					$pod = [];
				}
				$data['results'][] = [
					'text' => lang('Giao hàng'),
					'children' => $pod
				];
			}
			else if ($type_object == 'purchase_order') {
				if(!empty($object_main)) {
					$this->db->select('
					tblpurchase_order.id as id,
					CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as text,
					tblsuppliers.address as address,
					tblsuppliers.phone as phonenumber,
					tblpurchase_order.id_branch as branch_id
					', false);
					$this->db->where('tblpurchase_order.suppliers_id', $object_main);
					$this->db->from('tblpurchase_order');
					$this->db->join('tblsuppliers', 'tblsuppliers.id = tblpurchase_order.suppliers_id', 'left');
					if (!empty($term)) {
						$this->db->group_start();
						$this->db->like('CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code)', $term);
						$this->db->group_end();
					}
					$this->db->limit($limit);
					$pod = $this->db->get()->result_array();
				}
				$data['results'][] = [
					'text' => lang('Mua hàng'),
					'children' => $pod
				];
			}
		}
		else if (!empty($id)) {
			if($type_object == 'request_bussiness') {
				$this->db->where('id', $id);
				$dtRequest_bussiness = $this->db->get('tbl_request_bussiness')->row_array();
				$data['row'] = [
					'id' => $dtRequest_bussiness['id'],
					'text' => $dtRequest_bussiness['reference_no'],
					'phonenumber' => $dtRequest_bussiness['phone'],
					'address' => $dtRequest_bussiness['address'],
					'branch_id' => $dtRequest_bussiness['branch_id'],
				];
			}
			else if($type_object == 'delivery'){
				$id = trim($id, '-');
				$id = explode('-', $id);
				$this->db->select('
					tbl_deliveries.id as id,
					tbl_deliveries.reference_no as reference_no,
					tblshipping_client.address as address,
					tblshipping_client.phone as phonenumber,
					tbl_deliveries.id_branch as branch_id
				', false);
				
				$this->db->where_in('tbl_deliveries.id', $id);
				$this->db->join('tblshipping_client', 'tblshipping_client.id = tbl_deliveries.address_delivery_id', 'left');
				$dtDeliveries = $this->db->get('tbl_deliveries')->result_array();
				$data['row'] = [];
				foreach($dtDeliveries as $key => $value) {
					$data['row'][] = [
						'id' => $value['id'],
						'text' => $value['reference_no'],
						'address' => $value['address'],
						'phonenumber' => $value['phonenumber'],
						'branch_id' => $value['branch_id'],
					];
				}
				
			}
			else if($type_object == 'purchase_order'){
				$id = trim($id, '-');
				$id = explode('-', $id);
				
				$this->db->select('
					tblpurchase_order.id as id,
					CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as reference_no,
					tblsuppliers.address as address,
					tblsuppliers.phone as phonenumber,
					tblpurchase_order.id_branch as branch_id
				', false);
				$this->db->where_in('tblpurchase_order.id', $id);
				$this->db->join('tblsuppliers', 'tblsuppliers.id = tblpurchase_order.suppliers_id', 'left');
				$dtPurOrder = $this->db->get('tblpurchase_order')->result_array();
				$data['row'] = [];
				foreach($dtPurOrder as $key => $value) {
					$data['row'][] = [
						'id' => $value['id'],
						'text' => $value['reference_no'],
						'address' => $value['address'],
						'phonenumber' => $value['phonenumber'],
						'branch_id' => $value['branch_id'],
					];
				}
			}
			
		}
		echo json_encode($data);
	}
	
	public function searchObjectMain($id = 0) {
		$type_object = $this->input->get('type');
		$term = $this->input->get('term');
		$limit = get_option('select2_limit');
		if(empty($id)) {
			if ($type_object == 'delivery') {
				$this->db->select('
				tblclients.userid as id,
				tblclients.company as text,
				tblclients.address as address,
				tblclients.phonenumber as phonenumber,
			', false);
				$this->db->from('tblclients');
				if (!empty($term)) {
					$this->db->group_start();
					$this->db->like('tblclients.company', $term);
					$this->db->or_like('tblclients.zcode', $term);
					$this->db->group_end();
				}
				$this->db->limit($limit);
				$pod = $this->db->get()->result_array();
				$data['results'][] = [
					'text' => lang('Khách Hàng'),
					'children' => $pod
				];
			} else if ($type_object == 'purchase_order') {
				$this->db->select('
					tblsuppliers.id as id,
					tblsuppliers.company as text,
					tblsuppliers.address as address,
					tblsuppliers.phone as phonenumber,
				', false);
				$this->db->from('tblsuppliers');
				if (!empty($term)) {
					$this->db->group_start();
					$this->db->like('tblsuppliers.company', $term);
					$this->db->or_like('tblsuppliers.code', $term);
					$this->db->group_end();
				}
				$this->db->limit($limit);
				$pod = $this->db->get()->result_array();
				$data['results'][] = [
					'text' => lang('Nhà Cung Cấp'),
					'children' => $pod
				];
			}
		}
		else if (!empty($id)) {
			if($type_object == 'delivery') {
				$dtClient = get_table_where('tblclients', ['userid' => $id], '', 'row_array');
				$data['row'] = ['id' => $dtClient['userid'], 'text' => $dtClient['company']];
			}
			else if($type_object == 'purchase_order'){
				$dtSuppliers = get_table_where('tblsuppliers', ['id' => $id], '', 'row_array');
				$data['row'] = ['id' => $dtSuppliers['id'], 'text' => $dtSuppliers['company']];
			}
			
		}
		echo json_encode($data);
	}
}
