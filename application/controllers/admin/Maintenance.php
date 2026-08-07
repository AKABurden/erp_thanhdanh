<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Maintenance extends AdminController
{
    public function __construct()
    {
        parent::__construct();
		$this->view_category = has_permission('category_maintenance', '', 'view');
		$this->create_category = has_permission('category_maintenance', '', 'create');
		$this->edit_category = has_permission('category_maintenance', '', 'edit');
		$this->delete_category = has_permission('category_maintenance', '', 'delete');
		$this->import_category = has_permission('category_maintenance', '', 'import');
		$this->type = [
			'day' => 'Ngày',
			'quarterly' => 'Quý',
			'periodic' => 'Định kỳ'
		];

		$this->view = has_permission('maintenance', '', 'view');
		$this->view_own = has_permission('maintenance', '', 'view_own');
		$this->create = has_permission('maintenance', '', 'create');
		$this->edit = has_permission('maintenance', '', 'edit');
		$this->delete = has_permission('maintenance', '', 'delete');
		$this->print = has_permission('maintenance', '', 'print');
		$this->is_branch = true;
    }
	

    public function index() {
		if(!$this->view && !$this->view_own) {
			access_denied();
		}
        $data['title'] = _l('c_maintenance');
		$data['machines'] = $this->db->get('tbl_machines')->result_array();
		$data['maintenance'] = $this->db->get('tbl_machines_maintenance')->result_array();
        $this->load->view('admin/maintenance/manage', $data);
    }

	public function table() {
		$this->app->get_table_data('maintenance_stick', ['is_branch' => $this->is_branch]);
	}

	public function calendar()
	{
		if(!$this->view && !$this->view_own) {
			access_denied();
		}
		$data['title'] = lang('Lịch bảo trì');
		add_calendar_assets();
		$this->create_maintenance();
		$data['machines'] = $this->db->get('tbl_machines')->result_array();
		$data['maintenance'] = $this->db->get('tbl_machines_maintenance')->result_array();
		$this->load->view('admin/maintenance/calendar', $data);
	}

	public function create_maintenance() {
		$date = date('Y-m-d');
		$dateBefore = strtotime ( '+7 day' , strtotime ( $date ) ) ;
		$dateBefore = date ( 'Y-m-d' , $dateBefore );

		$arrayCount = [];

		$this->db->select([
			'tbl_machines.id',
			'tbl_machines.day_operation',
			'DATE_FORMAT(DATE_ADD(tbl_machines.day_operation, INTERVAL tbl_machines.number_day_operation DAY), "%Y-%m-%d") as dateTask'
		]);
		$this->db->where('tbl_machines.day_operation IS NOT NULL');
		$this->db->where('tbl_machines.number_day_operation IS NOT NULL');
		$this->db->where('DATE_FORMAT(DATE_ADD(tbl_machines.day_operation, INTERVAL tbl_machines.number_day_operation DAY), "%Y-%m-%d") <= "' . $dateBefore . '"');
		$this->db->where('NOT EXISTS (SELECT 1 FROM tblmaintenance WHERE tblmaintenance.id_machines = tbl_machines.id)', false, false);
		$machines = $this->db->get('tbl_machines')->result_array();

		if(!empty($machines)) {
			foreach($machines as $key => $value) {
				$this->db->insert('tblmaintenance', [
					'date' => $value['dateTask'],
					'id_machines' => $value['id'],
					'id_maintenance' => NULL,
					'name_maintenance' => NULL,
					'create_by' => 0,
					'date_create' => date('Y-m-d H:i:s'),
				]);
				$arrayCount[$value['dateTask']][$value['id']] = 1;
			}
		}

		$query_maintenance = "(
								SELECT 
									MAX(tblmaintenance.date) as date, 
									max(number_maintenance) as number_maintenance, 
									tblmaintenance.id_machines 
								FROM tblmaintenance 
								WHERE tblmaintenance.status != 3
								group by id_machines
							) tblmachines_max";

		$query_maintenance_list = "(
								SELECT 
									MAX(tblmaintenance_ticket.date) as date, 
									tblmaintenance_ticket.id_machines
								FROM tblmaintenance_ticket
								group by id_machines
							) tblmaintenance_list_max";
		$this->db->select([
			'tbl_machines.id',
			'tbl_machines.day_operation',
			'tblmachines_max.number_maintenance as number_maintenance',
			'tblmachines_max.date as date_max',
			'tblmaintenance_list_max.date as date_list',
			'DATE_FORMAT(DATE_ADD((IF((COALESCE(tblmaintenance_list_max.date, "") = "" || tblmachines_max.date >= tblmaintenance_list_max.date), tblmachines_max.date, tblmaintenance_list_max.date)), INTERVAL tbl_machines.number_day_operation DAY), "%Y-%m-%d") as dateTask'

		]);
		$this->db->where('tbl_machines.day_operation IS NOT NULL');
		$this->db->where('tbl_machines.number_day_operation IS NOT NULL');
		$this->db->join($query_maintenance, 'tblmachines_max.id_machines = tbl_machines.id', 'left');
		$this->db->join($query_maintenance_list, 'tblmaintenance_list_max.id_machines = tbl_machines.id', 'left');
		$this->db->where('DATE_FORMAT(DATE_ADD((IF((COALESCE(tblmaintenance_list_max.date, "") = "" || tblmachines_max.date >= tblmaintenance_list_max.date), tblmachines_max.date, tblmaintenance_list_max.date)), INTERVAL tbl_machines.number_day_operation DAY), "%Y-%m-%d") <= "'.$dateBefore.'"');
		$machines_list = $this->db->get('tbl_machines')->result_array();
//		print_arrays($machines_list);
		foreach($machines_list as $key => $value) {
			$success = $this->db->insert('tblmaintenance', [
				'date' => $value['dateTask'],
				'id_machines' => $value['id'],
				'id_maintenance' => NULL,
				'name_maintenance' => NULL,
				'create_by' => 0,
				'date_create' => date('Y-m-d H:i:s'),
				'number_maintenance' => ($value['number_maintenance'] + 1),
			]);
			if(!empty($success)) {
				$arrayCount[$value['dateTask']][$value['id']] = 1;
			}
		}

		if(!empty($arrayCount)) {
			$this->db->where('active', 1);
			$list_staff = $this->db->get('tblstaff')->result_array();
			if(!empty($list_staff)) {
				$arrayStaff = [];
				foreach($list_staff as $key => $value) {
					foreach($arrayCount as $date => $v) {
						$dataHtml = '
									<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
									Có '.count($v).' Thiết bị máy móc cần được bảo trì </b> vào lúc ' . _dhau($date) . '
						';
						$notification_data = [
							'date' => $date,
							'description' => $dataHtml,
							'touserid' => $value['staffid'],
							'link' => 'maintenance/calendar',
							'type' => 10,
							'object_id' => '',
							'object_type' => 'maintenance',
						];
						if (!empty($notification_data)) {
							$this->db->insert('tblnotifications', $notification_data);
						}
					}
					$arrayStaff[] = $value['staffid'];
				}
				pusher_trigger_notification($arrayStaff);
			}
		}
	}

	public function getCalendar() {
		$start = $this->input->post('start');
		$end = $this->input->post('end');
		$machines_search = $this->input->post('machines_search');
		$maintenance_search = $this->input->post('maintenance_search');
		$data = [];
//		if (has_permission('tasks', '', 'view_own') && !is_admin()) {
//			$this->db->where('EXISTS (SELECT 1 FROM tbltask_assigned WHERE tbltask_assigned.taskid = tbltasks.id AND tbltask_assigned.staffid = ' . get_staff_user_id() . ')');
//		}

		if(!empty($machines_search)) {
			$this->db->where_in('tbl_machines.id', $machines_search);
		}
		if(!empty($maintenance_search)) {
			$this->db->where_in('tblmaintenance.id_maintenance', $maintenance_search);
		}

		$this->db->select('tblmaintenance.*, 
			tbl_machines.name as name_machines, tblmaintenance_ticket.id as id_ticket, 
			tblmaintenance_ticket.note_main as note_main_stick, 
			tbl_machines_maintenance.note_main,
			(
				SELECT GROUP_CONCAT(CONCAT("- ", tbldetail_main.name, " / ", tbldetail_main.note_main) SEPARATOR "<br>") 
				FROM tbl_machines_maintenance tbldetail_main 
				WHERE tbldetail_main.machines_id = tblmaintenance.id_machines
			) as list_note_main
		', false);
		$this->db->where('DATE_FORMAT(tblmaintenance.date, "%Y-%m-%d") >= "' . ($start) . '"');
		$this->db->where('DATE_FORMAT(tblmaintenance.date, "%Y-%m-%d") <= "' . ($end) . '"');
		$this->db->join('tbl_machines', 'tbl_machines.id = tblmaintenance.id_machines');
		$this->db->join('tblmaintenance_ticket_machines', 'tblmaintenance_ticket_machines.id_maintenance_list = tblmaintenance.id', 'left');
		$this->db->join('tblmaintenance_ticket', 'tblmaintenance_ticket.id = tblmaintenance_ticket_machines.id_maintenance_ticket', 'left');
		$this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.id = tblmaintenance.id_maintenance', 'left');
//		$this->db->where('tblmaintenance.id_maintenance is null', false, false);
//		$this->db->where('tblmaintenance.status != 3', false, false);
		$events_maintenance = $this->db->get('tblmaintenance')->result_array();
		$data_status = [];
		foreach ($events_maintenance as $key => $value) {
			$htmlStatus = '';
			$styleStatus = '';
			$_data = '';
			$divFinised = '';
			if ($value['status'] == 2) {
				$divFinised = '<div class="panel-finished" style="margin: auto;width:unset">
                    <div class="">' . lang('tnh_finished_production') . '</div>
                </div>';
			}
			else if($value['status'] == 0){
				$htmlStatus = '<span class="inline-block label" style="color:green;border:1px solid green" >Chưa tạo phiếu bảo trì</span>';
			}
			else if($value['status'] == 1){
				$htmlStatus = '<span class="inline-block label" style="color:blue;border:1px solid blue" >Đã tạo phiếu bảo trì</span>';
			}
			else if($value['status'] == 3) {
				$styleStatus = 'text-decoration: line-through;';
			}

			$noteMain = $value['note_main'];
			if(!empty($value['note_main_stick'])) {
				$noteMain = $value['note_main_stick'];
			}
			if(empty($value['id_maintenance'])) {
				$noteMain = "<br/><i style='font-size: 9px;'>" . $value['list_note_main'].'</i>';
			}

			$dataStatus = $htmlStatus;
			if (!empty($divFinised)) {
				$style = 'text-decoration: line-through;color:#0e5daa;';
			} else {
				$style = 'color:#0e5daa;';
			}

//			$urlEvent = admin_url('maintenance/create_maintenance_stick?id_maintenance=' . $value['id']);
			$urlEvent = '';
			if($value['status'] > 0) {
				$urlEvent = admin_url('maintenance/view_maintenance_stick?id_maintenance=' . $value['id']);
			}

			$typeLast = 'color:white;background:#1b4d99;';
			$class_color = '';
			if(strtotime(date('Y-m-d')) > strtotime($value['date'])) {
				$class_color = 'bg-danger-fc';
			}

			$name = '';
//			$name .= '<div style="color: black;"><i><b style="text-transform: capitalize;">Bộ phận: </b></i> ' . ($value['name_maintenance']) . '</div>';
			$name .= '<div style="color: black;"><i><b style="text-transform: capitalize;">Ngày cần bảo trì: </b></i> ' . _d($value['date']) . '</div>';
			$name .= '<div style="color: black;"><i><b style="text-transform: capitalize;">Ghi chú cách thức bảo trì: </b></i> ' . $noteMain . '</div>';
			$content = '<div style="margin-top:10px;' . $style .$styleStatus. '" class="'.(!empty($urlEvent) ? 'c_modal' : '').' '.$class_color.'" href="' . ($urlEvent) . '">
                            <div class="bold uppercase" style="'.$typeLast.'padding:3px;margin-bottom:3px;margin-left:-8px;margin-right:-8px;margin-top:-8px">
                            <a style="color: white;"><i class="fa fa-cogs"></i> ' . $value['name_machines'] . '</a>
                            </div>
                            ' . $name . '
						</div>
                        ' . '<div class="mbot10"><div style="padding: 5px;' . $style . '">' . $dataStatus . '</div></div>
                        ' . $_data;
			$event_order['_tooltip'] = '';
			$event_order['title'] = $content;
			$event_order['start'] = $value['date'];
			$event_order['end'] = $value['date'];
			$event_order['public'] = 1;
			$event_order['onclick'] = true;
			$event_order['eventid'] = $value['id'];
			$time = date("H:i", strtotime($value['date']));
			$event_order['time'] = $time;
			$event_order['color'] = '#fff';
			array_push($data, $event_order);
		}
		echo json_encode(hooks()->apply_filters('calendar_data', $data, [
			'start' => $start,
			'end' => $end,
		]));
	}

	public function create_maintenance_stick() {
		if(!$this->create) {
			ajax_access_denied();
		}
		if($this->input->post()) {
			$data = $this->input->post();
			if(!empty($data)) {
				$items = !empty($data['items']) ? $data['items'] : [];
				unset($data['items']);
				$checked = !empty($data['checked']) ? $data['checked'] : [];
				unset($data['checked']);
				$note_main = $this->input->post('note_main', true);
				if(empty($id)) {

					$this->db->where('id', $data['id_maintenance']);
					$maintenance = $this->db->get('tblmaintenance')->row();

					if(empty($data['id_branch'])) {
						echo json_encode([
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Vui lòng chọn chi nhánh'
						]);die();
					}
					$date_maintenance = to_sql_date(_dC(to_sql_date($data['date'], true)));
					$options = [
						'date' => to_sql_date($data['date'], true),
						'name' => !empty($data['name']) ? $data['name'] : '',
						'category_tasks' => !empty($data['category_tasks']) ? $data['category_tasks'] : '',
						'id_maintenance' => !empty($data['id_maintenance']) ? $data['id_maintenance'] : '',
						'id_trouble' => !empty($data['id_trouble']) ? $data['id_trouble'] : '',
						'id_machines' => !empty($data['id_machines']) ? $data['id_machines'] : '',
						'type_stage_1' => !empty($data['type_stage_1']) ? $data['type_stage_1'] : 0,
						'type_stage_2' => !empty($data['type_stage_2']) ? $data['type_stage_2'] : 0,
						'type_stage_3' => !empty($data['type_stage_3']) ? $data['type_stage_3'] : 0,
						'type_stage_4' => !empty($data['type_stage_4']) ? $data['type_stage_4'] : 0,
						'quantity_pcs' => number_format_data($data['quantity_pcs'], false),
						'quantity' => !empty($data['quantity']) ? number_format_data($data['quantity'], false) : 0,
						'time_of_recording' => !empty($data['time_of_recording']) ? to_sql_date($data['time_of_recording'], true) : NULL,
						'action_now_1' => !empty($data['action_now_1']) ? $data['action_now_1'] : 0,
						'action_now_2' => !empty($data['action_now_2']) ? $data['action_now_2'] : 0,
						'action_now_3' => !empty($data['action_now_3']) ? $data['action_now_3'] : 0,
						'action_now_4' => !empty($data['action_now_4']) ? $data['action_now_4'] : 0,
						'reason' => !empty($data['reason']) ? $data['reason'] : NULL,
						'described' => !empty($data['described']) ? $data['described'] : NULL,
						'overcome' => !empty($data['overcome']) ? $data['overcome'] : NULL,
						'note_main' => $note_main,
						'note' => !empty($data['note']) ? $data['note'] : NULL,
						'type' => !empty($data['type']) ? $data['type'] : NULL,
						'id_branch' => !empty($data['id_branch']) ? $data['id_branch'] : NULL,
						'create_by' => get_staff_user_id(),
						'date_create' => date('Y-m-d H:i:s'),
					];
					$success = $this->db->insert('tblmaintenance_ticket', $options);
					if(!empty($success)) {
						$id = $this->db->insert_id();
						$array_id_maintenance = [];
//						foreach($id_maintenance_list as $key => $value) {
//							$this->db->select('
//								tblmaintenance.*,
//								tbl_machines.name as name_machines,
//								tbl_machines_maintenance.name as name_maintenance,
//								tbl_machines_maintenance.note_main as note_main,
//								tbl_machines.product_in_month,
//								CONCAT(tbl_machines.name, " - ", tbl_machines_maintenance.name) as full_name_maintenance
//							');
//							$this->db->where('tblmaintenance.id', $value);
//							$this->db->join('tbl_machines', 'tbl_machines.id = tblmaintenance.id_machines', 'left');
//							$this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.id = tblmaintenance.id_maintenance');
//							$this->db->order_by('date', 'asc');
//							$list_maintenance = $this->db->get('tblmaintenance')->row();
//
//
//							if(!empty($list_maintenance)) {
//								$success_mainList = $this->db->insert('tblmaintenance_ticket_machines', [
//									'id_maintenance_ticket' => $id,
//									'id_machines' => $list_maintenance->id_machines,
//									'id_maintenance' => $list_maintenance->id_maintenance,
//									'id_maintenance_list' => $value,
//								]);
//								if(!empty($success_mainList)) {
//									$this->db->where('id', $value);
//									$this->db->update('tblmaintenance', [
//										'status' => 1
//									]);
//									$array_id_maintenance[] = $list_maintenance->id_maintenance;
//									$array_check_list[] = $list_maintenance->full_name_maintenance;
//								}
//							}
//						}

						$this->db->where('machines_id', $data['id_machines']);
						$machines_maintenance = $this->db->get('tbl_machines_maintenance')->result_array();

						foreach($items as $type => $item) {
							foreach($item as $key => $value) {
								$this->db->insert('tblmaintenance_ticket_items', [
									'type' => $type,
									'id_maintenance_ticket' => $id,
									'name' => $value,
									'ischeck' => !empty($checked[$type][$key]) ? $checked[$type][$key] : 0,
								]);
							}
						}

						$this->db->where('id_machines', $data['id_machines']);
						$this->db->where('type', $data['type']);
						$category_maintenance = $this->db->get('tblcategory_maintenance')->result_array();
						if(!empty($category_maintenance)) {
							foreach($category_maintenance as $key => $value) {
								$this->db->insert('tblmaintenance_stick_category', [
									'id_category' => $value['id'],
									'maintenance_stick' => $id,
								]);
							}
						}



						$_data = [
							'name' => $data['name'],
							'hourly_rate' => 0,
							'category_tasks' => !empty($data['category_tasks']) ? $data['category_tasks'] : '',
							'startdate' => $data['date'],
							'duedate' => NULL,
							'priority' => 2,
							'repeat_every_custom' => 1,
							'repeat_type_custom' => 'day',
							'rel_type' => 'maintenance_ticket',
							'id_branch' => !empty($data['id_branch']) ? $data['id_branch'] : NULL,
							'rel_id' => $id,
							'description' => !empty($data['note_main']) ? $data['note_main'] : NULL,
						];
						$id_tasks = $this->tasks_model->add($_data, false, false);
						if(!empty($id_tasks)) {
							foreach($machines_maintenance as $key => $value) {
								$array_id_maintenance[] = $value['id'];
							}


							$this->db->where('type', 'procedure');
							$this->db->where('ischeck', '1');
							$this->db->where('id_maintenance_ticket', $id);
							$procedure = $this->db->get('tblmaintenance_ticket_items')->result_array();
							if(!empty($procedure)) {
								foreach ($procedure as $key => $value) {
									$this->db->insert('tbltask_checklist_items', [
										'taskid' => $id_tasks,
										'description' => $value['name'],
										'dateadded' => date('Y-m-d H:i:s'),
										'addedfrom' => get_staff_user_id(),
										'list_order' => ($key + 1),
									]);
								}
							}
							if(!empty($array_id_maintenance)) {
								$this->db->where_in('rel_id', $array_id_maintenance);
								$this->db->where('rel_type', 'rel_main');
								$files = $this->db->get('tblfiles')->result_array();
								foreach ($files as $key => $value) {
									if (!file_exists(FCPATH . 'uploads/machines_maintenance/' . $value['rel_id'] . '/')) {
										mkdir(FCPATH . 'uploads/machines_maintenance/' . $value['rel_id'] . '/');
										fopen(FCPATH . 'uploads/machines_maintenance/' . $value['rel_id'] . '/index.html', 'w');
									}
									$file_from = FCPATH . 'uploads/machines_maintenance/' . $value['rel_id'] . '/' . $value['file_name'];
									$file_to = get_upload_path_by_type('task') . $id_tasks . '/';
									if (!file_exists($file_to)) {
										mkdir($file_to);
										fopen($file_to . 'index.html', 'w');
									}
									$file_to .= $value['file_name'];
									@copy($file_from, $file_to);
									if (explode('/', $value['filetype'])[0] == 'image') {
										if (is_image($file_to)) {
											create_img_thumb($file_to, $value['file_name']);
										}
									}
									$fileArray = [
										'rel_id' => $id_tasks,
										'rel_type' => 'task',
										'file_name' => $value['file_name'],
										'filetype' => $value['filetype'],
										'staffid' => $value['staffid'],
										'dateadded' => date('Y-m-d H:i:s'),
										'attachment_key' => app_generate_hash(),
									];
									$this->db->insert('tblfiles', $fileArray);
								}
							}

							$this->db->where('id', $data['id_maintenance']);
							$this->db->update('tblmaintenance', ['status' => 1]);


							$this->db->where('DATE_FORMAT(date, "%Y-%m-%d") < "'.$date_maintenance.'"', false, false);
							$this->db->where('DATE_FORMAT(date, "%Y-%m-%d") > "'.$maintenance->date.'"', false, false);
							$this->db->where('status', 0);
							$this->db->update('tblmaintenance', ['status' => 3]);
						}
						echo json_encode([
							'success' => true,
							'alert_type' => 'success',
							'message' => 'Thêm dữ liệu thành công',
							'idtask' => $id_tasks
						]);die();
					}
					else {
						echo json_encode([
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Thêm dữ liệu không thành công'
						]);die();
					}
				}
				else {

				}
			}

		}
		else {
			$data['id_maintenance'] = $id_maintenance = $this->input->get('id_maintenance');
			$data['title'] = _l('Tạo phiếu bảo trì');
			$data['departments'] = $this->db->get('tbldepartments')->result_array();
			$data['trouble'] = $this->db->get('tbltrouble')->result_array();
			// $data['category_tasks'] = $this->db->get('tblcategory_tasks')->result_array();
			

			if(!empty($id_maintenance)) {
				$this->db->select('
					tblmaintenance.*, 
					tbl_machines.name as name_machines,
					(
						SELECT GROUP_CONCAT(CONCAT("- ", tbl_machines_maintenance.name, " - ", COALESCE(tbl_machines_maintenance.note_main, "")) SEPARATOR ", \n") 
						FROM tbl_machines_maintenance
						WHERE tbl_machines_maintenance.machines_id = tblmaintenance.id_machines
					) as note_main, 
					tbl_machines.product_in_month'
				);
				$this->db->where('tblmaintenance.id', $id_maintenance);
				$this->db->where('tblmaintenance.status', 0);
				$this->db->join('tbl_machines', 'tbl_machines.id = tblmaintenance.id_machines');
				$this->db->group_by('tblmaintenance.id_machines');
				$data['maintenance'] = $this->db->get('tblmaintenance')->row();
			}
			else {
				$this->db->select('
					tblmaintenance.*,
					tbl_machines.name as name_machines,
					(
						SELECT GROUP_CONCAT(CONCAT("- ", tbl_machines_maintenance.name, " - ", COALESCE(tbl_machines_maintenance.note_main, "")) SEPARATOR ", \n") 
						FROM tbl_machines_maintenance
						WHERE tbl_machines_maintenance.machines_id = tblmaintenance.id_machines
					) as note_main, 
					tbl_machines.product_in_month
				');
				$this->db->where('tblmaintenance.status', 0);
				$this->db->join('tbl_machines', 'tbl_machines.id = tblmaintenance.id_machines');
//				$this->db->where('tblmaintenance.id', $id_maintenance);
				$this->db->order_by('date', 'asc');
				$data['list_maintenance'] = $this->db->get('tblmaintenance')->result_array();
			}
		}

		$data['category_tasks'] = $this->site_model->getCategoryTasks();
		$data['branch'] = $this->db->get('tblbranch')->result_array();
		$data['type'] = $this->type;
		$this->load->view('admin/maintenance/modal', $data);
	}

	public function get_maintenance_to_machines() {
		$id = $this->input->post('id');
		$this->db->select('
			tblmaintenance.*,
			tbl_machines_maintenance.name as name_maintenance,
			tbl_machines_maintenance.note_main as note_main,
		');
		$this->db->where('tblmaintenance.status', 0);
		$this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.id = tblmaintenance.id_maintenance');
		$this->db->where('tblmaintenance.id_machines', $id);
		$this->db->order_by('date', 'asc');
		$this->db->group_by('tblmaintenance.id');
		$list_maintenance = $this->db->get('tblmaintenance')->result_array();
		foreach($list_maintenance as $key => $value) {
			$list_maintenance[$key]['note_main'] = htmlentities($value['note_main']);
			$list_maintenance[$key]['date'] = _dC($value['date']);
		}
		echo json_encode($list_maintenance);die();
	}

	public function view_maintenance_stick($id = '') {
		if(!$this->view) {
			ajax_access_denied();
		}
		$id_maintenance = $this->input->get('id_maintenance');
		if(!empty($id_maintenance) && empty($id)) {
			$this->db->where('id', $id_maintenance);
			$maintenance = $this->db->get('tblmaintenance')->row();
//			if (!empty($maintenance)) {
//				$this->db->select([
//					'tblmaintenance_ticket.*',
//					'tblcategory_tasks.code as code_category_task',
//				]);
//				$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblmaintenance_ticket.category_tasks', 'left');
//				$this->db->where('tblmaintenance_ticket.id', $id_maintenance);
//				$data['maintenance_ticket'] = $this->db->get('tblmaintenance_ticket')->row_array();
//
//				$this->db->select(['tbl_machines.name as name_machines', 'tbl_machines_maintenance.name as name_maintenance']);
//				$this->db->where('tblmaintenance_ticket_machines.id_maintenance_ticket', $id_maintenance);
//				$this->db->join('tbl_machines', 'tbl_machines.id = tblmaintenance_ticket_machines.id_machines', 'left');
//				$this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.id = tblmaintenance_ticket_machines.id_maintenance', 'left');
//				$data['maintenance_ticket']['items_ticket'] = $this->db->get('tblmaintenance_ticket_machines')->result_array();
//			}

			if (!empty($maintenance)) {
				$this->db->where('id_maintenance_list', $id_maintenance);
				$maintenance_ticket_machines = $this->db->get('tblmaintenance_ticket_machines')->row_array();



				if(!empty($this->is_branch)) {
					if (!is_admin()) {
						$list_branch = get_array_branch_staff();
						if (!empty($list_branch)) {
							$this->db->group_start();
							$this->db->where_in('tblmaintenance_ticket.id_branch', $list_branch);
							$this->db->group_end();
						} else {
							$this->db->where('tblmaintenance_ticket.id = 0', false, false);
						}
						$this->db->where('tblmaintenance_ticket.id', $maintenance_ticket_machines['id_maintenance_ticket']);
						$ktMaintenance_ticket = $this->db->get('tblmaintenance_ticket')->row();
						if (empty($ktMaintenance_ticket)) {
							accessDenied($js = true);
						}
					}
				}

				$this->db->select([
					'tblmaintenance_ticket.*'
				]);
				$this->db->where('tblmaintenance_ticket.id', $maintenance_ticket_machines['id_maintenance_ticket']);
				$data['maintenance_ticket'] = $this->db->get('tblmaintenance_ticket')->row_array();
				$id = $data['maintenance_ticket']['id'];

				$this->db->select(['tbl_machines_maintenance.name as name_maintenance', 'tblmaintenance.date']);
				$this->db->where('tblmaintenance_ticket_machines.id_maintenance_ticket', $id);
				$this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.id = tblmaintenance_ticket_machines.id_maintenance', 'left');
				$this->db->join('tblmaintenance', 'tblmaintenance.id = tblmaintenance_ticket_machines.id_maintenance_list', 'left');
				$data['maintenance_ticket']['items_ticket'] = $this->db->get('tblmaintenance_ticket_machines')->result_array();


				$data['machines'] = $this->db->get('tbl_machines', ['id' => $data['maintenance_ticket']['id_machines']])->row();
			}
			if (empty($data['maintenance_ticket'])) {
				echo "<script>alert_float('danger', 'Không tìm thấy phiếu bảo trì');</script>";
				die();
			}
		}
		else {
			if(!empty($this->is_branch)) {
				if (!is_admin()) {
					$list_branch = get_array_branch_staff();
					if (!empty($list_branch)) {
						$this->db->group_start();
						$this->db->where_in('tblmaintenance_ticket.id_branch', $list_branch);
						$this->db->group_end();
					} else {
						$this->db->where('tblmaintenance_ticket.id = 0', false, false);
					}
					$this->db->where('id', $id);
					$ktMaintenance_ticket = $this->db->get('tblmaintenance_ticket')->row();
					if (empty($ktMaintenance_ticket)) {
						accessDenied($js = true);
					}
				}
			}


			$this->db->select([
				'tblmaintenance_ticket.*',
				'tblcategory_tasks.code as code_category_task',
				'tblmaintenance.date as date_maintenance'
			]);
			$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblmaintenance_ticket.category_tasks', 'left');
			$this->db->join('tblmaintenance', 'tblmaintenance.id = tblmaintenance_ticket.id_maintenance', 'left');
			$this->db->where('tblmaintenance_ticket.id', $id);
			$data['maintenance_ticket'] = $this->db->get('tblmaintenance_ticket')->row_array();

			$this->db->select(['tbl_machines_maintenance.name as name_maintenance', 'tblmaintenance.date']);
			$this->db->where('tblmaintenance_ticket_machines.id_maintenance_ticket', $id);
//			$this->db->join('tbl_machines', 'tbl_machines.id = tblmaintenance_ticket_machines.id_machines', 'left');
			$this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.id = tblmaintenance_ticket_machines.id_maintenance', 'left');
			$this->db->join('tblmaintenance', 'tblmaintenance.id = tblmaintenance_ticket_machines.id_maintenance_list', 'left');
			$data['maintenance_ticket']['items_ticket'] = $this->db->get('tblmaintenance_ticket_machines')->result_array();

			$data['machines'] = $this->db->get('tbl_machines', ['id' => $data['maintenance_ticket']['id_machines']])->row();
		}

		$data['title'] = 'Xem phiếu bảo trì';
		$data['type'] = $this->type;

		$this->db->select('tblmaintenance_stick_category.*, tblcategory_maintenance.name, tblcategory_maintenance.code');
		$this->db->join('tblcategory_maintenance', 'tblcategory_maintenance.id = tblmaintenance_stick_category.id_category');
		$this->db->where('tblmaintenance_stick_category.maintenance_stick', $id);
		$data['category_maintenance'] = $this->db->get_where('tblmaintenance_stick_category')->result_array();
		$this->load->view('admin/maintenance/view_maintenance_stick', $data);
	}

	public function update_maintenance_stick_category() {
		$id = $this->input->post('id');
		$active = $this->input->post('active');
		if(!empty($active)) {
			$option = [
				'active' => $active,
				'staff_active' => get_staff_user_id(),
				'date_active' => date('Y-m-d H:i:s')
			];
		}
		else {
			$option = [
				'active' => NULL,
				'staff_active' => NULL,
				'date_active' => NULL
			];
		}

		$this->db->where('id', $id);
		$success = $this->db->update('tblmaintenance_stick_category', $option);
		if(!empty($success)) {
			echo json_encode([
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Cập nhật thành công',
				'active' => !empty($option['active']) ? $option['active'] : NULL,
				'staff_active' => !empty($option['staff_active']) ? get_staff_full_name($option['staff_active']) : '-',
				'date_active' => !empty($option['date_active']) ? _dt($option['date_active']) : NULL
			]);die();
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Cập nhật không thành công',
		]);die();
	}

	public function get_table_category($type = '', $id_machines = '') {
		$this->db->where('type', $type);
		$this->db->where('id_machines', $id_machines);
		$category_maintenance = $this->db->get('tblcategory_maintenance')->result_array();

		$htmlBody = '';
		$html = '';
		if(!empty($category_maintenance)) {
			foreach($category_maintenance as $key => $value) {
				$htmlBody .= '<tr><td>'.$value['code'].'</td><td>'.$value['name'].'</td></tr>';
			}
			$html = '<table class="table dataTable mtop20">
						<thead style="background: #cedae6;">
							<tr>
								<td>Mã hạng mục bảo trì</td>
								<td>Tên hạng mục bảo trì</td>
							</tr>
						</thead>
						<tbody>'.$htmlBody.'</tbody>
					</table>';
		}
		echo $html;
	}

	public function pdf($id = '') {

		if(!$this->print) {
			access_denied();
		}
		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tblmaintenance_ticket.id_branch', $list_branch);
					$this->db->group_end();
				} else {
					$this->db->where('tblmaintenance_ticket.id = 0', false, false);
				}
				$this->db->where('id', $id);
				$ktMaintenance_ticket = $this->db->get('tblmaintenance_ticket')->row();
				if (empty($ktMaintenance_ticket)) {
					access_denied();
				}
			}
		}

		ob_end_clean();
		$data = [];

		$this->db->select([
			'tblmaintenance_ticket.*',
			'tblcategory_tasks.code as code_category_task',
			'tblmaintenance.date as date_maintenance'
		]);
		$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblmaintenance_ticket.category_tasks', 'left');
		$this->db->join('tblmaintenance', 'tblmaintenance.id = tblmaintenance_ticket.id_maintenance', 'left');
		$this->db->where('tblmaintenance_ticket.id', $id);
		$items = $this->db->get('tblmaintenance_ticket')->row_array();

		$machines = $this->db->get_where('tbl_machines', ['id' => $items['id_machines']])->row();

		$this->db->select('tblmaintenance_stick_category.*, tblcategory_maintenance.name, tblcategory_maintenance.code');
		$this->db->join('tblcategory_maintenance', 'tblcategory_maintenance.id = tblmaintenance_stick_category.id_category');
		$this->db->where('tblmaintenance_stick_category.maintenance_stick', $id);
		$category_maintenance = $this->db->get_where('tblmaintenance_stick_category')->result_array();
		$htmlBody = '';
		if(!empty($category_maintenance)) {
			foreach($category_maintenance as $key => $value) {
				$htmlBody .= '<tr>
								<td>'.$value['code'].'</td>
								<td>'.$value['name'].'</td>
								<td style="text-align: center;">'.($value['active'] == 1 ? 'X' : '').'</td>
								<td style="text-align: center;">'.($value['active'] == 2 ? 'X' : '').'</td>
								<td style="text-align: center;">'.(!empty($value['staff_active']) ? (get_staff_full_name($value['staff_active']) . '<br/>') : '').''.(!empty($value['date_active']) ? _dt($value['date_active']) : '').'</td>
							</tr>';
			}
		}

		$htmlCategory = '<table cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
							<tr>
								<th style="text-align: center;width: 20%;"><b>Mã hạng mục bảo trì</b></th>
								<th style="text-align: center;width: 30%;"><b>Tên hạng mục bảo trì</b></th>
								<th style="text-align: center;width: 10%;"><b>Đạt</b></th>
								<th style="text-align: center;width: 15%;"><b>Không Đạt</b></th>
								<th style="text-align: center;width: 25%;"><b>Nhân viên đánh giá</b></th>
							</tr>'.$htmlBody.'
						</table>';


		$htmlItems = '';
		if(!empty($items)) {
			$this->db->select([
				'tbl_machines_maintenance.*'
			]);
			$this->db->where('tbl_machines_maintenance.machines_id', $machines->id);
			$machines_maintenance = $this->db->get('tbl_machines_maintenance')->result_array();
			$htmlItems .= '<tr nobr="true">
								<td colspan="2"><b>Thiết bị: </b> ' . ($machines->name) . '</td>
							</tr>';
			$htmlItems .= '<tr nobr="true">
								<td colspan="2"><b>Ngày bảo trì theo lịch trình: </b> ' . _d($items['date_maintenance']) . '</td>
							</tr>';
			$htmlItems .= '<tr nobr="true"><td colspan="2"><b>Tổng Số lượng :</b> '.number_format_data($items['quantity_pcs']).'</td></tr>';

			$htmlItemsMM = '';
			foreach($machines_maintenance as $key => $value) {
					$htmlItemsMM .= '<tr nobr="true">
											<td width="40%" class="text-center">' . ($value['name']) . '</td>
											<td width="60%">' . $value['note_main'] . '</td>
										</tr>';
			}
			$htmlItemsMM= '<table cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
								<tr>
									<th width="40%" class="text-center"><b>Bộ phận</b></th>
									<th width="60%" class="text-center"><b>Nội dung</b></th>
								</tr>'.$htmlItemsMM.'
							</table>';


		}
		$data['type'] = 'P';
		$data['img'] = '';

		$bodyItems = '';
		$totalBox = 0;

		$day = date_format(date_create($items['date']), 'd');
		$month = date_format(date_create($items['date']), 'm');
		$year = date_format(date_create($items['date']), 'Y');
		$message = "";
		ob_start();
		stylePdf();

		$items['note_main'] = explode("\n", $items['note_main']);
		$items['note_main'] = implode('<br/>', $items['note_main']);
		echo '
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="8"><h1 class="text-center uppercase" style="font-size: 20px;">' . _l('PHIẾU BẢO TRÌ MÁY MÓC') . '</h1></td>
                </tr>
            </table>
            <br><br>
            <table cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
                <tr>
                    <td colspan="2"><b>Ngày '.$day.' Tháng '.$month.' Năm '.$year.'</b></td>
                </tr>
                <tr>
                	<td><b>Phiếu bảo trì:</b> '.$items['name'].'</td>
                	<td><b>Mã công việc:</b> '.$items['code_category_task'].'</td>
                </tr>
                 '.$htmlItems.'
            </table>
            '.$htmlItemsMM.'
            <br>
            <br>
            '.$htmlCategory.'
            <br><br>
            <table cellspacing="0" cellpadding="5"  style="width: 100%;">
				<tr>
					<td>
						<b>Ghi chú cách thức bảo trì:</b>
					</td>
				</tr>
				<tr>
					<td width="5%;"></td>
					<td>
						'.$items['note_main'].'
					</td>
				</tr>
            </table>
            <br>
            <br>
            <br>
            <br>
            <br>
            <table cellspacing="0" cellpadding="5"  style="width: 100%;">
				<tr>
					<td width="50%" class="text-center">
						<b>Nhân viên bảo trì:</b>
						<br/>
						(Họ tên và chữ ký)
					</td>
					<td width="50%" class="text-center">
						<b>Trưởng phòng xác nhận:</b>
						<br/>
						(Họ tên và chữ ký)
					</td>
				</tr>
            </table>
        ';
//
		$content = ob_get_contents();
		ob_end_clean();

		$data['content'] = $content;
		$data['is_c'] = true;
		$pdf = @print_pdf_tnh($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}

	public function delete($id = '') {
		if(!$this->delete) {
			ajax_access_denied();
		}

		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tblmaintenance_ticket.id_branch', $list_branch);
					$this->db->group_end();
				} else {
					$this->db->where('tblmaintenance_ticket.id = 0', false, false);
				}
				$this->db->where('id', $id);
				$ktMaintenance_ticket = $this->db->get('tblmaintenance_ticket')->row();
				if (empty($ktMaintenance_ticket)) {
					ajax_access_denied();
				}
			}
		}
		if(!empty($id)) {
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'maintenance_ticket');
			$ktTask = $this->db->get('tbltasks')->num_rows();
			if(!empty($ktTask)) {
				echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Đang có tồn tại phiếu công việc liên quan đến phiếu bảo trì nên không thể xóa']);die();
			}

			$this->db->where('id', $id);
			$maintenance_ticket = $this->db->get('tblmaintenance_ticket')->row();
			if(!empty($maintenance_ticket)) {
				$this->db->where('id', $id);
				$success = $this->db->delete('tblmaintenance_ticket');
				if (!empty($success)) {
					$this->db->where('id_maintenance_ticket', $id);
					$this->db->delete('tblmaintenance_ticket_items');


					$this->db->where('id_maintenance_ticket', $id);
					$maintenance_ticket_machines = $this->db->get('tblmaintenance_ticket_machines')->result_array();

					$this->db->where('id_maintenance_ticket', $id);
					$this->db->delete('tblmaintenance_ticket_machines');

					foreach($maintenance_ticket_machines as $key => $value) {
						$this->db->where('id_maintenance_list', $value['id_maintenance_list']);
						$kt_maintenance_ticket = $this->db->get('tblmaintenance_ticket_machines')->row();
						if (empty($kt_maintenance_ticket)) {
							$this->db->where('id', $value['id_maintenance_list']);
							$this->db->update('tblmaintenance', ['status' => 0]);
						}
					}
					echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa báo cáo thành công']);
					die();
				}
			}
		}
		echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa báo cáo không thành công']);die();
	}

	public function pdf_before($id = '') {
		if(!$this->print) {
			access_denied();
		}
		ob_end_clean();
		$data = [];

		$this->db->select([
			'tblmaintenance_ticket.*',
			'tblcategory_tasks.code as code_category_task',
		]);
		$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblmaintenance_ticket.category_tasks', 'left');
		$this->db->where('tblmaintenance_ticket.id', $id);
		$items = $this->db->get('tblmaintenance_ticket')->row_array();

		$machines = $this->db->get_where('tbl_machines', ['id' => $items['id_machines']])->row();

		$this->db->select('tblmaintenance_stick_category.*, tblcategory_maintenance.name, tblcategory_maintenance.code');
		$this->db->join('tblcategory_maintenance', 'tblcategory_maintenance.id = tblmaintenance_stick_category.id_category');
		$this->db->where('tblmaintenance_stick_category.maintenance_stick', $id);
		$category_maintenance = $this->db->get_where('tblmaintenance_stick_category')->result_array();
		$htmlBody = '';
		if(!empty($category_maintenance)) {
			foreach($category_maintenance as $key => $value) {
				$htmlBody .= '<tr>
								<td>'.$value['code'].'</td>
								<td>'.$value['name'].'</td>
								<td style="text-align: center;">'.($value['active'] == 1 ? 'X' : '').'</td>
								<td style="text-align: center;">'.($value['active'] == 2 ? 'X' : '').'</td>
								<td style="text-align: center;">'.(!empty($value['staff_active']) ? (get_staff_full_name($value['staff_active']) . '<br/>') : '').''.(!empty($value['date_active']) ? _dt($value['date_active']) : '').'</td>
							</tr>';
			}
		}

		$htmlCategory = '<table cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
							<tr>
								<th style="text-align: center;width: 20%;"><b>Mã hạng mục bảo trì</b></th>
								<th style="text-align: center;width: 30%;"><b>Tên hạng mục bảo trì</b></th>
								<th style="text-align: center;width: 10%;"><b>Đạt</b></th>
								<th style="text-align: center;width: 15%;"><b>Không Đạt</b></th>
								<th style="text-align: center;width: 25%;"><b>Nhân viên đánh giá</b></th>
							</tr>'.$htmlBody.'
						</table>';


		$htmlItems = '';
		if(!empty($items)) {
			$this->db->select([
				'tbl_machines.id as id_machines',
				'tbl_machines.name as name_machines',
				'tbl_machines_maintenance.id as id_machines_maintenance',
				'tbl_machines_maintenance.name as name_maintenance',
				'tbl_machines_maintenance.note_main as note_main',
				'tblmaintenance.date as date'
			]);
			$this->db->where('tblmaintenance_ticket_machines.id_maintenance_ticket', $id);
			$this->db->join('tbl_machines', 'tbl_machines.id = tblmaintenance_ticket_machines.id_machines', 'left');
			$this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.id = tblmaintenance_ticket_machines.id_maintenance', 'left');
			$this->db->join('tblmaintenance', 'tblmaintenance.id = tblmaintenance_ticket_machines.id_maintenance_list', 'left');
			$this->db->order_by('tbl_machines.id', 'desc');
			$this->db->order_by('tbl_machines_maintenance.id', 'asc');
			$items_ticket = $this->db->get('tblmaintenance_ticket_machines')->result_array();
			$keyItems = [];
			foreach($items_ticket as $key => $value) {
				$keyItems[$value['id_machines']]['date'] = $value['date'];
				$keyItems[$value['id_machines']]['items'][] = $value['name_maintenance'];
				$keyItems[$value['id_machines']]['note_main'][] = $value['note_main'];
			}

			$htmlItems .= '<tr nobr="true">
								<td colspan="2"><b>Thiết bị: </b> ' . ($machines->name) . '</td>
							</tr>';
			$htmlItems .= '<tr nobr="true"><td colspan="2"><b>Tổng Số lượng :</b> '.number_format_data($items['quantity_pcs']).'</td></tr>';

			$htmlItemsMM = '';
			foreach($keyItems as $key => $value) {
				foreach($value['items'] as $k => $v) {
					$htmlItemsMM .= '<tr nobr="true">
											<td width="30%" class="text-center">' . ($v) . '</td>
											<td width="20%" class="text-center">'._dC($value['date']).'</td>
											<td width="50%">' . $value['note_main'][$k] . '</td>
										</tr>';
				}
			}
			$htmlItemsMM= '<table cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
								<tr>
									<th width="30%" class="text-center"><b>Bộ phận</b></th>
									<th width="20%" class="text-center"><b>Thời gian</b></th>
									<th width="50%" class="text-center"><b>Nội dung</b></th>
								</tr>'.$htmlItemsMM.'
							</table>';


		}
		$data['type'] = 'P';
		$data['img'] = '';

		$bodyItems = '';
		$totalBox = 0;

		$day = date_format(date_create($items['date']), 'd');
		$month = date_format(date_create($items['date']), 'm');
		$year = date_format(date_create($items['date']), 'Y');
		$message = "";
		ob_start();
		stylePdf();
		$items['note_main'] = explode("\n", $items['note_main']);
		$items['note_main'] = implode('<br/>', $items['note_main']);
		echo '
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="8"><h1 class="text-center uppercase" style="font-size: 20px;">' . _l('PHIẾU BẢO TRÌ MÁY MÓC') . '</h1></td>
                </tr>
            </table>
            <br><br>
            <table cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
                <tr>
                    <td colspan="2"><b>Ngày '.$day.' Tháng '.$month.' Năm '.$year.'</b></td>
                </tr>
                <tr>
                	<td><b>Phiếu bảo trì:</b> '.$items['name'].'</td>
                	<td><b>Mã công việc:</b> '.$items['code_category_task'].'</td>
                </tr>
                 '.$htmlItems.'
            </table>
            '.$htmlItemsMM.'
            <br>
            <br>
            '.$htmlCategory.'
            <br><br>
            <table cellspacing="0" cellpadding="5"  style="width: 100%;">
				<tr>
					<td>
						<b>Ghi chú cách thức bảo trì:</b>
					</td>
				</tr>
				<tr>
					<td width="5%;"></td>
					<td>
						'.$items['note_main'].'
					</td>
				</tr>
            </table>
            <br>
            <br>
            <br>
            <br>
            <br>
            <table cellspacing="0" cellpadding="5"  style="width: 100%;">
				<tr>
					<td width="50%" class="text-center">
						<b>Nhân viên bảo trì:</b>
						<br/>
						(Họ tên và chữ ký)
					</td>
					<td width="50%" class="text-center">
						<b>Trưởng phòng xác nhận:</b>
						<br/>
						(Họ tên và chữ ký)
					</td>
				</tr>
            </table>
        ';
//
		$content = ob_get_contents();
		ob_end_clean();

		$data['content'] = $content;
		$pdf = @print_pdf_tnh($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}



	public function category() {
		if(!$this->view_category) {
			access_denied();
		}
		$data['title'] = _l('c_category_maintenance');
		$data['type'] = $this->type;
		$this->load->view('admin/maintenance/category/manage', $data);
	}

	public function create_category($id = '') {
		if(!$this->create_category && empty($id)) {
			ajax_access_denied();
		}
		if(!$this->edit_category && !empty($id)) {
			ajax_access_denied();
		}

		if($this->input->post()) {
			$data = $this->input->post();
			if(!empty($data)) {
				if(empty($id)) {
					$this->db->where('code', $data['code']);
					$isset_category_maintenance = $this->db->get('tblcategory_maintenance')->row();
					if(!empty($isset_category_maintenance)) {
						echo json_encode([
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã hạng mục đã tồn tại'
						]);die();
					}

					$options = [
						'code' => $data['code'],
						'name' => !empty($data['name']) ? $data['name'] : NULL,
						'id_machines' => !empty($data['id_machines']) ? $data['id_machines'] : NULL,
						'type' => !empty($data['type']) ? $data['type'] : NULL,
						'create_by' => get_staff_user_id(),
					];
					$success = $this->db->insert('tblcategory_maintenance', $options);
					if(!empty($success)) {
						echo json_encode([
							'success' => true,
							'alert_type' => 'success',
							'message' => 'Thêm hạng mục thành công',
						]);die();
					}
					else {
						echo json_encode([
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Thêm hạng mục không thành công'
						]);die();
					}
				}
				else {

					$this->db->where('code', $data['code']);
					$this->db->where('id != "'.$id.'"');
					$isset_category_maintenance = $this->db->get('tblcategory_maintenance')->row();
					if(!empty($isset_category_maintenance)) {
						echo json_encode([
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã hạng mục đã tồn tại vui lòng chọn mã khác'
						]);die();
					}

					$options = [
						'code' => $data['code'],
						'name' => !empty($data['name']) ? $data['name'] : NULL,
						'id_machines' => !empty($data['id_machines']) ? $data['id_machines'] : NULL,
						'type' => !empty($data['type']) ? $data['type'] : NULL
					];
					$this->db->where('id', $id);
					$success = $this->db->update('tblcategory_maintenance', $options);
					if(!empty($success)) {
						echo json_encode([
							'success' => true,
							'alert_type' => 'success',
							'message' => 'Sửa hạng mục thành công',
						]);die();
					}
					else {
						echo json_encode([
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Sửa hạng mục không thành công'
						]);die();
					}
				}
			}
		}

		$data['title'] = 'Thêm hạng mục bảo trì';
		if(!empty($id)) {
			$data['category'] = $this->db->get_where('tblcategory_maintenance', ['id' => $id])->row();
			$data['title'] = 'Sửa hạng mục bảo trì';
		}

		$data['machines'] = $this->db->get('tbl_machines')->result_array();

		$data['type'] = $this->type;

		$this->load->view('admin/maintenance/category/modal', $data);
	}


	public function table_category()
	{
		$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
		$aColumns = [
			'tblcategory_maintenance.id as id',
			'tblcategory_maintenance.code as code',
			'tblcategory_maintenance.name as name',
			'tbl_machines.code as code_machines',
			'tbl_machines.name as name_machines',
			'tblcategory_maintenance.type as type',
		];
		$sIndexColumn = 'id';
		$sTable = 'tblcategory_maintenance';
		$where = [];

		if($this->input->post('category_search')) {
			$where[] = 'AND (tblcategory_maintenance.code LIKE "%' . $this->input->post('category_search') . '%" 
							OR tblcategory_maintenance.name LIKE "%' . $this->input->post('category_search') . '%")';
		}

		if($this->input->post('type_category')) {
			$type_category = $this->input->post('type_category');
			$where[] = 'AND tblcategory_maintenance.type = "' . $type_category . '"';
		}

		$join = [
			'LEFT JOIN tbl_machines ON tbl_machines.id = tblcategory_maintenance.id_machines'
		];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
		foreach ($rResult as $key => $aRow) {
			$row = [];
			$row[] = $aRow['id'];
			$row[] = $aRow['code'];
			$row[] = $aRow['name'];
			$row[] = $aRow['code_machines'];
			$row[] = $aRow['name_machines'];
			$row[] = !empty($this->type[$aRow['type']]) ? $this->type[$aRow['type']] : '-';

			$options = '';
			if(!empty($this->edit_category)) {
				$options = '<a href="' . admin_url('maintenance/create_category/' . $aRow['id']) . '" class="btn btn-default btn-icon c_modal">
								<i class="fa fa-pencil-square-o"></i>
							</a>';
			}
			if(!empty($this->delete_category)) {
				$options .= '<a data-id="'.$aRow['id'].'" class="btn btn-danger _delete_category btn-icon">
								<i class="fa fa-remove"></i>
							</a>';
			}
			$row[] = $options;

			$output['aaData'][] = $row;
		}
		echo json_encode($output);
	}

	public function delete_category($id = '') {
		if (empty($this->delete_category)) {
			ajax_access_denied();
		}
		if(!empty($id)) {
			$this->db->where('id', $id);
			$success = $this->db->delete('tblcategory_maintenance');
			if(!empty($success)) {
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Xóa dữ liệu thành công'
				]);die();
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa dữ liệu không thành công'
		]);die();
	}

	public function modal_excel_category()
	{
		if (empty($this->import_category)) {
			ajax_access_denied();
		}
		$data['title'] = _l('c_import_maintenance_category');
		$this->load->view('admin/maintenance/category/import', $data);
	}

	public function import_category()
	{
		if (empty($this->import_category)) {
			ajax_access_denied();
		}
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
				// 2: mã máy móc
				// 3: type

				$code = trim($value[0]);
				$name = trim($value[1]);
				$code_machines = trim($value[2]);
				$type = trim($value[3]);

				if (empty($name) || empty($type) || empty($code_machines)) {
					continue;
				}

				$id_machines = $this->db->get_where('tbl_machines', ['code' => $code_machines])->row('id');
				if(empty($id_machines)) {
					continue;
				}

				if(!empty($code)) {
					$checkCode = $this->db->get_where('tblcategory_maintenance', ['code' => $code])->row();
					if (!empty($checkCode)) continue;
				}

				$options = [
					'code' => !empty($code) ? $code : NULL,
					'name' => $name,
					'id_machines' => $id_machines,
					'type' => $type,
					'create_by' => get_staff_user_id()
				];

				$rs = $this->db->insert('tblcategory_maintenance', $options);
				if ($rs) {
					if(empty($code)) {
						$id = $this->db->insert_id();
						$code = 'MHBT-' . sprintf("%06s", ($id));
						$this->db->where('id', $id);
						$this->db->update('tblcategory_maintenance', ['code' => $code]);
					}
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
