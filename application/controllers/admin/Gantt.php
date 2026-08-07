<?php

// header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Gantt extends AdminController
{
    public function __construct()
    {

        parent::__construct();
        $this->load->model('gantt_ch_model');
        $this->load->model('projects_model');
        // $this->lang->load('vietnamese/form_validation_lang');
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        // $this->upload_path = get_upload_path_by_type('products');
        $this->datetime_now = time();
        $this->tnh = true;
        $this->permission =false;
        
        if(has_permission('gantt','','view_own')&&!is_admin())
        {
            $this->permission = true;
        }
    }


	public function index_backup()
	{
		if (!has_permission('gantt', '', 'view') && !has_permission('gantt', '', 'view_own')) {
			access_denied('gantt');
		}
		if ($this->input->post('search') == 'unsearch') {
			$_POST = array();
		}
		else {
			$data['rel_id'] = $this->input->post('rel_id');
			$data['rel_type'] = $this->input->post('rel_type');
		}
		$data['title'] = lang('tnh_diagram_gantt');
		$data['tnh'] = $this->tnh;



		if(!empty($data['rel_type'])) {
			$this->db->where('rel_type', $data['rel_type']);
		}
		if(!empty($data['rel_id'])) {
			$this->db->where('rel_id', $data['rel_id']);
		}
		$this->db->where('rel_id is not null')->where('rel_type is not null')
			->group_by('rel_id, rel_type');
		$sum = $this->db->get('tbltasks')->num_rows();

		$numberPage = 10;
		$numPages = ceil($sum / $numberPage);
		$pageCurrent = !empty($this->input->get('page')) ? $this->input->get('page') : 1;

		$start = ($pageCurrent - 1) * $numberPage;
		$data['numPages'] = $numPages;
		$data['pageCurrent'] = $pageCurrent;


		$data['num_row'] = $NumRow = 5;
		//title 1 là mặc định name group
		$data['nameGroupTitle'] = [];
		$data['widthTitle'] = 600;

		$this->db->select('rel_id, rel_type');
		if(!empty($data['rel_type'])) {
			$this->db->where('rel_type', $data['rel_type']);
		}
		if(!empty($data['rel_id'])) {
			$this->db->where('rel_id', $data['rel_id']);
		}
		$this->db->where('rel_id is not null')->where('rel_type is not null')
			->group_by('rel_id, rel_type')
			->limit($numberPage, $start);
		$groupTasks = $this->db->get('tbltasks')->result_array();

		$taskGantt = [];
		$keyDetail = 0;
		$totalGrantt = [];
		foreach($groupTasks as $key => $value) {
			$task_rel_data = get_relation_data($value['rel_type'], $value['rel_id']);
			$name = get_relation_values($task_rel_data, $value['rel_type'])['name'];


			$taskGantt[$keyDetail] = [
				'production_order_id' => '',
				'name' => $name,
				'values' => '',
				'desc' => 'productions_orders',
			];
			$keyGroup = $keyDetail;
			$totalGrantt = [
				'total_day' => 0,
				'total_success' => 0
			];
			$keyDetail++;
			$this->db->select('DATEDIFF(duedate, startdate) as day, tbltasks.*');
			$this->db->where('rel_id', $value['rel_id'])
				->where('rel_type', $value['rel_type'])
				->order_by('startdate', 'desc');
			$dataTasks = $this->db->get('tbltasks')->result_array();
			foreach($dataTasks as $kTask => $vTask) {
				$totalGrantt['total_day'] += $vTask['day'];
				if($vTask['status'] == 5) {
					$totalGrantt['total_success']++;
				}

				$taskGantt[$keyDetail] = [
					'production_order_detail_id' => $vTask['id'],
					'name' => '',
					'desc' => '',
					'row' => [
						$vTask['name'],
						$vTask['day'],
						($vTask['status'] == 5 ? 100 : 0).'%',
						_d($vTask['startdate']),
						_dt($vTask['duedate'])
					],
					'values' => [
						[
							'from' => date_format(date_create($vTask['startdate']),"Y/m/d"),
							'to' => date_format(date_create($vTask['duedate']),"Y/m/d"),
							'desc' => '',
							'label' => '<a onclick="init_task_modal('.$vTask['id'].'); return false;">'.$vTask['name'].'<a/>',
							'customClass' => 'ganttRed',
							'dataObj' => [
								'id_detail' => $vTask['id']
							]
						]
					],
				];
				$keyDetail++;
			}

			$data['nameGroupTitle'][$keyGroup] = [
				'T.Hạn (Ngày) <b class="text-danger">('.$totalGrantt['total_day'].')</b>',
				'TL H.Thành <b class="text-danger">('.($totalGrantt['total_success'] / count($dataTasks) * 100).'%)</b>',
				'Ngày bắt đầu',
				'Ngày kết thúc',
			];

		}


		$data['gantt_data'] = [];
		foreach($taskGantt as $key => $value) {
			if(!empty($value['row'])) {
				for($i = 0; $i < $NumRow; $i++) {
					if($i == 0) {
						$value['desc'] .='<span class="spanLeft">' . $value['row'][$i] . '</span>';
					}
					else {
						$value['desc'] .='<span class="spanRight">' . $value['row'][$i] . '</span>';
					}
				}
			}
			$data['gantt_data'][] = $value;
		}

		$this->load->view('admin/gantt_tasks/index', $data);
	}

	public function index()
	{
		if (!has_permission('gantt', '', 'view') && !has_permission('gantt', '', 'view_own')) {
			access_denied('gantt');
		}
		if ($this->input->post('search') == 'unsearch') {
			$_POST = array();
		}
		else {
			$data['rel_id'] = $this->input->post('rel_id');
			$data['rel_type'] = $this->input->post('rel_type');
			$data['department_search'] = $this->input->post('department_search');
			$data['status_search'] = $this->input->post('status_search');
		}
		$data['title'] = lang('tnh_diagram_gantt');
		$data['tnh'] = $this->tnh;
		$date_now = date('Y/m/d');
		$date_now_ymd = date('Y-m-d');

		if(!empty($data['rel_type'])) {
			$this->db->where('rel_type', $data['rel_type']);
		}
		if(!empty($data['rel_id'])) {
			$this->db->where('rel_id', $data['rel_id']);
		}

		if (!empty($data['status_search'])) {
			$status_search = $data['status_search'];
			if ($status_search == 1) {
				//chưa tới hạn
				$this->db->where('(DATEDIFF(tbltasks.startdate, "'.$date_now_ymd.'") > 2 AND tbltasks.datefinished IS NULL)', false, false);
			} else if ($status_search == 2) {
				//sắp tới hạn
				$this->db->where('(DATEDIFF(tbltasks.startdate, "'.$date_now_ymd.'") <= 2 AND tbltasks.startdate >= "'.$date_now_ymd.' 00:00:00" AND tbltasks.datefinished IS NULL)', false, false);
			} else if ($status_search == 3) {
				//tới hạn
				$this->db->where('(tbltasks.startdate >= "'.$date_now_ymd.' 00:00:00" AND tbltasks.startdate <= "'.$date_now_ymd.' 23:59:59" AND tbltasks.datefinished IS NULL)', false, false);
			} else if ($status_search == 4) {
				//trễ hạn
				$this->db->where('tbltasks.startdate <= "'.$date_now_ymd.' 00:00:00" AND tbltasks.datefinished IS NULL', false, false);

			} else if ($status_search == 5) {
				//hoàn thành
				$this->db->where('(tbltasks.datefinished IS NOT NULL)', false, false);
			}
		}

		if (!empty($data['department_search'])) {
			$this->db->where(' EXISTS (
				SELECT 1
				FROM tbltask_department
				WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id = '.$data['department_search'].'
			)', false, false);
		}

		// $this->db->where('rel_id is not null')->where('rel_type is not null')->group_by('rel_id, rel_type');
		// $sum = $this->db->get('tbltasks')->num_rows();
		$sum = $this->db->count_all_results('tbltasks');

		$numberPage = 20;
		$numPages = ceil($sum / $numberPage);
		$pageCurrent = !empty($this->input->get('page')) ? $this->input->get('page') : 1;

		$start = ($pageCurrent - 1) * $numberPage;
		$data['numPages'] = $numPages;
		$data['pageCurrent'] = $pageCurrent;


		$data['num_row'] = $NumRow = 5;
		//title 1 là mặc định name group
		$data['nameGroupTitle'] = [];
		$data['widthTitle'] = 600;

		$this->db->select('tbltasks.id, tbltasks.name, tbltasks.startdate, tblcategory_tasks.time, tbltasks.datefinished', false);
		$this->db->from('tbltasks');
		$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tbltasks.category_tasks', 'left');
		if(!empty($data['rel_type'])) {
			$this->db->where('rel_type', $data['rel_type']);
		}

		if(!empty($data['rel_id'])) {
			$this->db->where('rel_id', $data['rel_id']);
		}

		if (!empty($data['department_search'])) {
			$this->db->where(' EXISTS (
				SELECT 1
				FROM tbltask_department
				WHERE tbltask_department.task_id = tbltasks.id AND tbltask_department.department_id = '.$data['department_search'].'
			)', false, false);
		}

		if (!empty($data['status_search'])) {
			$status_search = $data['status_search'];
			if ($status_search == 1) {
				//chưa tới hạn
				$this->db->where('(DATEDIFF(tbltasks.startdate, "'.$date_now_ymd.'") > 2 AND tbltasks.datefinished IS NULL)', false, false);
			} else if ($status_search == 2) {
				//sắp tới hạn
				$this->db->where('(DATEDIFF(tbltasks.startdate, "'.$date_now_ymd.'") <= 2 AND tbltasks.startdate >= "'.$date_now_ymd.' 00:00:00" AND tbltasks.datefinished IS NULL)', false, false);
			} else if ($status_search == 3) {
				//tới hạn
				$this->db->where('(tbltasks.startdate >= "'.$date_now_ymd.' 00:00:00" AND tbltasks.startdate <= "'.$date_now_ymd.' 23:59:59" AND tbltasks.datefinished IS NULL)', false, false);
			} else if ($status_search == 4) {
				//trễ hạn
				$this->db->where('tbltasks.startdate <= "'.$date_now_ymd.' 00:00:00" AND tbltasks.datefinished IS NULL', false, false);

			} else if ($status_search == 5) {
				//hoàn thành
				$this->db->where('(tbltasks.datefinished IS NOT NULL)', false, false);
			}
		}
		$this->db->order_by('tbltasks.id DESC');
		$this->db->limit($numberPage, $start);
		$tasks = $this->db->get()->result_array();
		// print_arrays($this->db->last_query());

		$taskGantt = [];
		$keyDetail = 0;
		$totalGrantt = [];
		$data['gantt_data'] = [];
		$date_now = date('Y/m/d');
		if (!empty($tasks)) {
			$arrTaskId = [];
			foreach ($tasks as $key => $value) {
				$arrTaskId[] = $value['id'];
			}

			if (!empty($arrTaskId)) {
				$this->db->select('
					tbltask_department.department_id,
					tbl_room.name as name_room,
					GROUP_CONCAT(tbltask_department.task_id) as task_id
				', false);
				$this->db->from('tbltask_department');
				$this->db->join('tbl_room', 'tbl_room.id = tbltask_department.department_id');
				$this->db->where_in('tbltask_department.task_id', $arrTaskId);
				$this->db->group_by('tbltask_department.department_id');
				if (!empty($data['department_search'])) {
					$this->db->where('tbl_room.id', $data['department_search'], false);
				}
				$rooms = $this->db->get()->result_array();
			}

			if (!empty($rooms)) {
				foreach ($rooms as $key => $value) {
					$arrCurrentTaskId = explode(',', $value['task_id']);
					$row = [];
					$values = [];

					$_grantt = [
						'production_order_id' => $value['department_id'],
						'name' => $value['name_room'],
						'values' => '',
						'desc' => 'productions_orders',
						'row' => [],
						'values' => [],
					];
					$data['gantt_data'][] = $_grantt;

					if (!empty($tasks)) {
						foreach ($tasks as $kT => $vT) {
							$task_id = $vT['id'];
							if (in_array($task_id, $arrCurrentTaskId)) {
								$time = $vT['time'] ?? 0;
								$startdate = $vT['startdate'];
								$datefinished = $vT['datefinished'];
								$date_end = date('d/m/Y H:i', strtotime('+'.$time.' minute', strtotime($startdate)));

								$startdate_hour = date_format(date_create($startdate), 'd/m/Y H:i');
								$datefinished_hour = $datefinished ? date_format(date_create($datefinished), 'd/m/Y H:i') : '';

								$row = [];
								$row[] = '<span class="spanLeft" title="'.$vT['name'].'" data-toggle="tooltip" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">'.$vT['name'].'</span>';
								$row[] = '<span class="spanRight">'.$time.'</span>';
								$row[] = '<span class="spanRight">'.$startdate_hour.'</span>';
								$row[] = '<span class="spanRight">'.$date_end.'</span>';
								$row[] = '<span class="spanRight">'.$datefinished_hour.'</span>';

								$row = implode('', $row);
								$from = date_format(date_create($startdate), 'Y/m/d');
								$to = date('Y/m/d', strtotime('+'.$time.' minute', strtotime($startdate)));
								$customClass = 'ganttPrimary';
								if (!empty($datefinished)) {
									$customClass = 'ganttGreen';
								} else if ($date_now == $to) {
									$customClass = 'ganttOrange';
								} else if ($date_now > $to) {
									$customClass = 'ganttRed';
								} else if (minusDateFormat($date_now, $to) <= 2) {
									$customClass = 'ganttYellow';
								}

								$_grantt = [
									'production_order_detail_id' => $task_id,
									'name' => '',
									'desc' => $row,
									'values' => [
										[
											'from' => $from,
											'to' => $to,
											'desc' => '',
											'label' => $vT['name'],
											'customClass' => $customClass,
											'dataObj' => [
												'id_detail' => $task_id
											]
										]
									],
								];
								$data['gantt_data'][] = $_grantt;
								// unset($tasks[$kT]);
							}
						}
					}
				}
			}
		}

		$data['nameGroupTitle'][0] = [
			'T.Hạn (phút) <b class="text-danger"></b>',
			'Ngày bắt đầu',
			'Ngày kết thúc',
			'TL H.Thành <b class="text-danger"></b>',
		];

		$this->load->view('admin/gantt_tasks/index', $data);
	}

//    public function index()
//    {
//        if (!has_permission('gantt', '', 'view') && !has_permission('gantt', '', 'view_own')) {
//                access_denied('gantt');
//        }
//        if ($this->input->post('search') == 'unsearch') {
//            $_POST = array();
//        }
//        $data['title'] = lang('tnh_diagram_gantt');
//        $data['tnh'] = $this->tnh;
//        $sum = $this->gantt_ch_model->countProductionsGantt();
//        $numberPage = 10;
//        $numPages = ceil($sum/$numberPage);
//        $pageCurrent = !empty($this->input->get('page')) ? $this->input->get('page') : 1;
//
//        $start = ($pageCurrent - 1) * $numberPage;
//        // $limit   = $numberPage * $pageCurrent - 1;
//        $data['numPages'] = $numPages;
//        $data['pageCurrent'] = $pageCurrent;
//        // $data['gantt_data'] = $this->site_model->loadGanttProductions($start, $numberPage);
//        $data['gantt_data'] = $this->gantt_ch_model->loadGanttProductions($start, $numberPage);
//        $this->load->view('admin/gantt/index', $data);
//    }


    public function searchwork_list($id='')
    {
            $search = $this->input->get('term');
            $this->db->select('
                    tblstaff.staffid as id,
                    concat(lastname,"",firstname) as text'
            , false);
            $this->db->group_by('tblstaff.staffid');
            $this->db->order_by('tblstaff.staffid', 'DESC');
            $this->db->join('tbltask_assigned','tbltask_assigned.staffid = tblstaff.staffid');
            $this->db->join('tbltasks','tbltasks.id = tbltask_assigned.taskid', 'left');
            $this->db->where('tbltasks.status !=',5);
            if($this->permission ==  true){
              $this->db->where('tblstaff.staffid',get_staff_user_id());
            }
            $this->db->limit(50);
            
            if (!empty($search))
            {
                $this->db->like('concat(lastname,"",firstname)', $search);
            }else {
            if($id > 0) {
                $this->db->where('tblstaff.staffid', $id);
                $items['row'] = $this->db->get('tblstaff')->row();
                }
            }

            $items['results'] = $this->db->get('tblstaff')->result_array();
            echo json_encode($items);die();
    }
}