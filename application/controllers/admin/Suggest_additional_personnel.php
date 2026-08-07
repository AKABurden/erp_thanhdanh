<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_additional_personnel extends AdminController
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

        $this->preViewSuggestAdditionalPersonnel = true;
        $this->preViewOwnSuggestAdditionalPersonnel = true;
        $this->preAddSuggestAdditionalPersonnel = true;
        $this->preEditSuggestAdditionalPersonnel = true;
        $this->preApproveSuggestAdditionalPersonnel = true;
        $this->preDeleteSuggestAdditionalPersonnel= true;
    }

    public function index()
    {
        if (!$this->preViewSuggestAdditionalPersonnel && !$this->preViewOwnSuggestAdditionalPersonnel) {
            access_denied();
        }
        $data['title'] = _l('dt_suggest_additional_personnel');
        $this->load->view('admin/suggest_additional_personnel/index', $data);
    }

    public function getSuggestAdditionalPersonnel()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_additional_personnel.id as id',
            'tbl_suggest_additional_personnel.reference_no as reference_no',
            'tbl_suggest_additional_personnel.date as date',
            'tbl_suggest_additional_personnel.staff_suggest as staff_suggest',
            'tbl_suggest_additional_personnel.note as note',
            'tbl_suggest_additional_personnel.staff_reciever as staff_reciever',
            'tbl_suggest_additional_personnel.staff_agree as staff_agree',
            'tblroles.name as position_recruitment',
            'tbl_suggest_additional_personnel.quantity as quantity',
            'tbl_suggest_additional_personnel.staff_admin as staff_admin',
            'tbl_suggest_additional_personnel.date_start as date_start',
            'tbl_suggest_additional_personnel.date_end as date_end',
            'tbl_suggest_additional_personnel.evaluate as evaluate',
            'tbl_suggest_additional_personnel.kpis as kpis',
            'tbl_suggest_additional_personnel.status as status',
            'tbl_suggest_additional_personnel.note as note',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_additional_personnel';
        $where = [

        ];
        $filter = [];

        $join = [
            'LEFT JOIN tblroles ON tblroles.roleid=tbl_suggest_additional_personnel.position_recruitment'
        ];

        if (!$this->preViewSuggestAdditionalPersonnel) {
            array_push($where, 'AND (tbl_suggest_additional_personnel.created_by = '.get_staff_user_id().' 
                OR tbl_suggest_additional_personnel.staff_suggest = '.get_staff_user_id().'
                OR tbl_suggest_additional_personnel.staff_reciever = '.get_staff_user_id().'
             )');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_additional_personnel.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_additional_personnel.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_additional_personnel.date_status',
            'tbl_suggest_additional_personnel.staff_status'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_additional_personnel/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 120px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . get_staff_full_name($aRow['staff_suggest']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['note']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . get_staff_full_name($aRow['staff_reciever']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . get_staff_full_name($aRow['staff_agree']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['position_recruitment']) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity']) . '</div>';
            $row[] = '<div class="text-left">' . @diffDate($aRow['date_start'],$aRow['date_end']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_admin']) . '</div>';
            $row[] = '<div class="text-left"></div>';
            $row[] = '<div class="text-left">'.(!empty($value['date_start']) ? _dhau($value['date_start']) : '').'</div>';
            $row[] = '<div class="text-left">'.(!empty($value['date_end']) ? _dhau($value['date_end']) : '').'</div>';
            $row[] = '<div class="text-left">' . ($aRow['evaluate']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['kpis']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } elseif ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left">' . $_data . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_additional_personnel/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestAdditionalPersonnel ? '<a class="tnh-modal" href="' . base_url('admin/suggest_additional_personnel/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestAdditionalPersonnel ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_additional_personnel/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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

    public function detail($id = 0){
        $data = [];
        $this->db->select('tbl_suggest_additional_personnel.*');
        $this->db->from('tbl_suggest_additional_personnel');
        $this->db->where('tbl_suggest_additional_personnel.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_additional_personnel.reference_no]');
                }
            } else {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_additional_personnel.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('staff_suggest', lang("Người yêu cầu"), 'required');
            $this->form_validation->set_rules('staff_reciever', lang("Nhóm tiếp nhận"), 'required');
            $this->form_validation->set_rules('date_start', lang("Thời gian bắt đầu"), 'required');
            $this->form_validation->set_rules('date_end', lang("Thời gian kết thúc"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_additional_personnel');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_suggest = $this->input->post('staff_suggest');
                    $staff_reciever = $this->input->post('staff_reciever');
                    $staff_agree = $this->input->post('staff_agree');
                    $staff_admin = $this->input->post('staff_admin');
                    $position_recruitment = $this->input->post('position_recruitment');
                    $quantity = number_unformat($this->input->post('quantity'));
                    $kpis = $this->input->post('kpis');
                    $date_start = $this->input->post('date_start');
                    $date_end = $this->input->post('date_end');
                    $note = ($this->input->post('note'));
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'staff_suggest' => $staff_suggest,
                        'staff_reciever' => $staff_reciever,
                        'staff_agree' => $staff_agree,
                        'staff_admin' => $staff_admin,
                        'position_recruitment' => $position_recruitment,
                        'quantity' => $quantity,
                        'kpis' => $kpis,
                        'date_start' => to_sql_date($date_start),
                        'date_end' => to_sql_date($date_end),
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_additional_personnel',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_additional_personnel') == $reference_no) {
                            updateReference('suggest_additional_personnel');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_additional_personnel',
                            'table_obj' => 'tbl_suggest_additional_personnel',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu bổ sung nhân sự') . ' [' . $reference_no . ']',
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
                echo json_encode($data);die();
            } else {
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_suggest = $this->input->post('staff_suggest');
                    $staff_reciever = $this->input->post('staff_reciever');
                    $staff_agree = $this->input->post('staff_agree');
                    $staff_admin = $this->input->post('staff_admin');
                    $position_recruitment = $this->input->post('position_recruitment');
                    $quantity = number_unformat($this->input->post('quantity'));
                    $kpis = $this->input->post('kpis');
                    $date_start = $this->input->post('date_start');
                    $date_end = $this->input->post('date_end');
                    $note = ($this->input->post('note'));
                    $fields = [
                        'date' => $date,
                        'staff_suggest' => $staff_suggest,
                        'staff_reciever' => $staff_reciever,
                        'staff_agree' => $staff_agree,
                        'staff_admin' => $staff_admin,
                        'quantity' => $quantity,
                        'kpis' => $kpis,
                        'position_recruitment' => $position_recruitment,
                        'date_start' => to_sql_date($date_start),
                        'date_end' => to_sql_date($date_end),
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_additional_personnel',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_additional_personnel',
                            'table_obj' => 'tbl_suggest_additional_personnel',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu bổ sung nhân sự') . ' [' . $dtData['reference_no'] . ']',
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
                echo json_encode($data);die();
            }
        } else {
            if (empty($id)){
                if (!$this->preAddSuggestAdditionalPersonnel){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_additional_personnel');
            } else {
                if (!$this->preEditSuggestAdditionalPersonnel){
                    accessDenied(true);
                }
                $this->db->select('tbl_suggest_additional_personnel.*');
                $this->db->from('tbl_suggest_additional_personnel');
                $this->db->where('tbl_suggest_additional_personnel.id',$id);
                $dtData = $this->db->get()->row_array();

                if ($dtData['status'] == 1){
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));

                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_suggest_additional_personnel');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
		// $data['roles'] = $this->roles_model->get();
        $data['roles'] = [];
		$this->get_parent(0, $data['roles']);
        $data['reference_no'] = getReference('suggest_additional_personnel');
        $this->load->view('admin/suggest_additional_personnel/detail',$data);
    }
    public function get_parent($id_parent = 0, &$array_category = [], $level = 0)
	{
		if (is_numeric($level)) {
			$this->db->where(array('roles_parent' => $id_parent));
			$current_level = $this->db->get('tblroles')->result_array();
			if ($current_level) {
				foreach ($current_level as $key => $value) {
					$sub = "";
					for ($i = 0; $i < $level; $i++) {
						$sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					$sub .= "&#10154;";
					$current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
					array_push($array_category, $current_level[$key]);
					$this->get_parent($value['roleid'], $array_category, $level + 1);
				}
			} else {
				return;
			}
		}
	}
    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_additional_personnel');

        $this->db->select('tbl_suggest_additional_personnel.*,
        ');
        $this->db->from('tbl_suggest_additional_personnel');
        $this->db->where('tbl_suggest_additional_personnel.id',$id);
        $dtData = $this->db->get()->row_array();

        $data['dtData'] = $dtData;
        $this->load->view('admin/suggest_additional_personnel/view',$data);
    }

    public function agree(){
        if (!$this->preApproveSuggestAdditionalPersonnel) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_additional_personnel.*');
        $this->db->from('tbl_suggest_additional_personnel');
        $this->db->where('tbl_suggest_additional_personnel.id',$suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if (($dtData['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data); return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'date_status' => $date_status,
                'staff_status' => $staff_status,
            ];

            $this->db->where('id',$suggest_id);
            $up = $this->db->update('tbl_suggest_additional_personnel',$options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_additional_personnel',
                    'table_obj' => 'tbl_suggest_additional_personnel',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu bổ sung nhân sự') . ' [' . $dtData['reference_no'] . ']',
                    'actions' => 'approved'
                ]);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function delete($id){
        if (!$this->preDeleteSuggestAdditionalPersonnel){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_additional_personnel.*');
        $this->db->from('tbl_suggest_additional_personnel');
        $this->db->where('tbl_suggest_additional_personnel.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        if ($dtData['status'] == 1){
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã duyệt không thể xóa !');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_additional_personnel');
        if ($success){

            insertActivityLog([
                'type_parent_obj' => 'suggest_additional_personnel',
                'table_obj' => 'tbl_suggest_additional_personnel',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu bổ sung nhân sự') . ' [' . $dtData['reference_no'] . ']',
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
            $inputFileName = 'uploads/import_dt/phieu_yeu_cau_bo_sung_nhan_su.xlsx';
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
            $this->db->select('tbl_suggest_additional_personnel.*,
                tblroles.name as position_recruitment
            ');
            $this->db->from('tbl_suggest_additional_personnel');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_additional_personnel.branch_id', 'inner');
            $this->db->join('tblroles', 'tblroles.roleid = tbl_suggest_additional_personnel.position_recruitment', 'left');
            if (!$this->preViewSuggestAdditionalPersonnel) {
                $this->db->where('(tbl_suggest_additional_personnel.created_by = ' . $staff_id . ' OR tbl_suggest_additional_personnel.staff_suggest = '.get_staff_user_id().' OR tbl_suggest_additional_personnel.staff_reciever = '.get_staff_user_id().')');
            }
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_additional_personnel.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_additional_personnel.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_suggest_additional_personnel.id asc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $colStt = 0;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $dem);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, _dt($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, get_staff_full_name($value['staff_suggest']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['note']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, get_staff_full_name($value['staff_reciever']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, get_staff_full_name($value['staff_agree']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['position_recruitment']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $value['quantity']);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, @diffDate($value['date_start'],$value['date_end']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, get_staff_full_name($value['staff_admin']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, (!empty($value['date_start']) ? _dhau($value['date_start']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, (!empty($value['date_end']) ? _dhau($value['date_end']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['evaluate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['kpis'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                if (!empty($value['barcode'])){
                    $code = $value['barcode'];
                } else {
                    $code = 'suggest_additional_personnel||'.$value['id'];
                    $this->db->where('id',$value['id']);
                    $this->db->update('tbl_suggest_additional_personnel',['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/suggest_additional_personnel/';
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
                $params['savename'] = $folder.'qrcode/'. $qr . '.png';
                $this->ciqrcode->generate($params);
                $img = ($folder.'qrcode/'. $qr . '.png');
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
            $filename = lang('Phieu_yeu_cau_ke_hoach_hieu_chuan') . '.xls';
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
}