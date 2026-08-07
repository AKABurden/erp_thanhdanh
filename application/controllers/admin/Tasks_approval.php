<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tasks_approval extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
        $this->load->model('tasks_approval_model');
    }

    /**
     * Xử lý duyệt tiêu chí và gửi mail cho vị trí được phân
     */
    public function approve_criteria()
    {
        $id = $this->input->post('id'); // tasks id
        $process_id = $this->input->post('process_id');
        $detail_id = $this->input->post('detail_id'); // id_tasks_process
        $inspection_criteria_current_id = $this->input->post('inspection_criteria_current_id');
        $type = $this->input->post('type'); // 1 = duyệt, 2 = không duyệt
        $role_processing = $this->input->post('role_processing');

        if (empty($id) || empty($process_id) || empty($detail_id) || empty($inspection_criteria_current_id)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin cần thiết']);
            die;
        }

        $this->db->where('id', $detail_id);
		$internal_proposal = $this->db->get('tbltask_checklist_items')->row();
		if (!empty($internal_proposal) && !empty($internal_proposal->finished)) {
			echo json_encode(['success' => false, 'message' => lang('Đã có nhân viên thay đổi trạng thái')]);
			die;
		}

        $this->db->where('taskid', $id);
		$this->db->where('id <', $internal_proposal->id);
		$this->db->order_by('id', 'desc');
		$check_status_bef = $this->db->get('tbltask_checklist_items')->row_array();
		if (!empty($check_status_bef)) {
			if ($check_status_bef['finished'] == 0) {
				echo json_encode(['success' => false, 'message' => lang('Bước ' . $check_status_bef['description'] . ' chưa duyệt, Không thể duyệt bước này')]);
				die;
			}
		}

        $inspection_criteria_current = get_table_where('tbl_tasks_inspection_criteria_process', ['id' => $inspection_criteria_current_id], '', 'row_array');
        if (!empty($inspection_criteria_current['isCheck'])) {
            echo json_encode(['success' => true, 'message' => lang('Quy trình này đã được duyệt')]);
				die;
        }

        $inspection_criteria_id = $this->input->post('inspection_criteria_id');
        $isCheck = !empty($this->input->post('isCheck')) ? $this->input->post('isCheck') : NULL;
        $isCheckNot = !empty($this->input->post('isCheckNot')) ? $this->input->post('isCheckNot') : array();
		$inspection_criteria_all = !empty($inspection_criteria_id) ? $inspection_criteria_id : array();
		foreach ($inspection_criteria_all as $criteria_id) {
			if (!isset($isCheck[$criteria_id]) && !isset($isCheckNot[$criteria_id])) {
				$data['success'] = 0;
				$data['message'] = lang('Vui lòng duyệt tiêu chí phía trên');
				echo json_encode($data);
				die;
			}

			if (isset($isCheckNot[$criteria_id])) {
				$production_report = get_table_where('tblproduction_report', ['id_tasks' => $id, 'id_tasks_process' => $detail_id, 'id_tasks_process_child' => $criteria_id], '', 'row_array');
				if (empty($production_report)) {
					$data['success'] = 0;
					$data['message'] = lang('Có quy trình không duyệt chưa tạo phiếu báo cáo không phù hợp, Vui lòng kiểm tra lại');
					echo json_encode($data);
					die;
				} else {
					$this->db->select('tbl_process_production_report.*');
					$this->db->where('tbl_process_production_report.staff_process', 0);
					$this->db->where('tbl_process_production_report.production_report_id', $production_report['id']);
					$this->db->from('tbl_process_production_report');
					$Success_process = $this->db->get()->num_rows();
					if (!empty($Success_process)) {
						$data['success'] = 0;
						$data['message'] = lang('Có quy trình không duyệt chưa Có phiếu báo cáo không phù hợp chưa hoàn thành hết, Vui lòng kiểm tra lại');
						echo json_encode($data);
						die;
					}
				}
			}

            if ($criteria_id == $inspection_criteria_current_id) {
                break;
            }
		}

        $CheckCreateBCKPH = $this->tasks_approval_model->CheckCreateBCKPH($id, $process_id, $detail_id);
		if ($CheckCreateBCKPH == 2) {
			echo json_encode(['success' => false, 'message' => lang('Có quy trình không duyệt chưa Có phiếu báo cáo không phù hợp chưa hoàn thành hết, Vui lòng kiểm tra lại')]);
			die;
		}

        if ($CheckCreateBCKPH > 2) {
            if ($inspection_criteria_current_id != $CheckCreateBCKPH) {
                echo json_encode(['success' => false, 'message' => lang('Có quy trình không duyệt chưa tạo phiếu báo cáo không phù hợp, Vui lòng kiểm tra lại')]);
                die;
            }
		}

        $result = $this->tasks_approval_model->approve_criteria([
            'id' => $id,
            'process_id' => $process_id,
            'detail_id' => $detail_id,
            'inspection_criteria_id' => $inspection_criteria_current_id,
            'type' => $type,
            'role_processing' => $role_processing
        ]);

        echo json_encode($result);
        die;
    }

}
