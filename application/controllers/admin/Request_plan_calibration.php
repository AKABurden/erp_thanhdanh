<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_plan_calibration extends AdminController
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

        $this->preViewRequestPlanCalibration = true;
        $this->preViewOwnRequestPlanCalibration = true;
        $this->preAddRequestPlanCalibration = true;
        $this->preEditRequestPlanCalibration = true;
        $this->preApproveRequestPlanCalibration = true;
        $this->preDeleteRequestPlanCalibration = true;
    }

    public function index()
    {
        if (!$this->preViewRequestPlanCalibration && !$this->preViewOwnRequestPlanCalibration) {
            access_denied();
        }
        $data['title'] = _l('dt_request_plan_calibration');
        $this->load->view('admin/request_plan_calibration/index', $data);
    }

    public function getRequestPlanCalibration()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_request_plan_calibration.id as id',
            'tbl_request_plan_calibration.reference_no as reference_no',
            'tbl_request_plan_calibration.date as date',
            'tbl_request_plan_calibration.staff_id as staff_id',
            'tbl_category_plan_time.name as name_category_plan_time',
            'tbl_category_calibration.name as category_calibration',
            'tbl_request_plan_calibration.bp_calibration as bp_calibration',
            'tbl_request_plan_calibration.detail_calibration as detail_calibration',
            'tbl_request_plan_calibration.quantity as quantity',
            'tbl_category_machines.name as name_category_machines',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tblbranch.name as name_branch',
            'tbl_request_plan_calibration.procedure as procedures',
            'tbl_result.name as name_result',
            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
             FROM tblproduction_report
             WHERE tblproduction_report.object_id = tbl_request_plan_calibration.id AND tblproduction_report.object_type = "request_plan_calibration"
            ) as name_report',
            'tbl_request_plan_calibration.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_plan_calibration';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tbl_machines ON tbl_machines.id = tbl_request_plan_calibration.machines_id',
            'LEFT JOIN tbl_category_machines ON tbl_category_machines.id = tbl_machines.category_machine_id',
            'INNER JOIN tblbranch ON tblbranch.id = tbl_request_plan_calibration.branch_id',
            'INNER JOIN tbl_result ON tbl_result.id = tbl_request_plan_calibration.result_id',
            'INNER JOIN tbl_category_calibration ON tbl_category_calibration.id = tbl_request_plan_calibration.category_calibration',
            'INNER JOIN tbl_category_plan_time ON tbl_category_plan_time.id = tbl_request_plan_calibration.category_plan',
        ];

        if (!$this->preViewRequestPlanCalibration) {
            array_push($where, 'AND tbl_request_plan_calibration.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_plan_calibration.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_plan_calibration.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_plan_calibration/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 120px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . get_staff_full_name($aRow['staff_id']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_category_plan_time']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['category_calibration']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['bp_calibration']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['detail_calibration']) . '</div>';
            $row[] = '<div class="text-left">' . formatNumber($aRow['quantity']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['name_category_machines']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['code_machines']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['name_machines']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_branch']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['procedures']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_result']) . '</div>';
            $arrReport = $aRow['name_report'];
            $htmlReport = '';
            if (!empty($arrReport)) {
                $arrReport = explode('||', $arrReport);
                if (!empty($arrReport)) {
                    foreach ($arrReport as $kk => $vv) {
                        $vv = explode('__', $vv);
                        $htmlReport .= '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $vv[1])) . '">' . $vv[0] . '</a>';
                    }
                }
            }
            $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=request_plan_calibration') . '" class="btn btn-info">Tạo phiếu báo cáo không phù hợp</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>
            ';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_plan_calibration/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditRequestPlanCalibration ? '<a class="tnh-modal" href="' . base_url('admin/request_plan_calibration/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteRequestPlanCalibration ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_calibration/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $this->db->select('tbl_request_plan_calibration.*');
        $this->db->from('tbl_request_plan_calibration');
        $this->db->where('tbl_request_plan_calibration.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_request_plan_calibration.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
                $this->form_validation->set_rules('result_id', lang("Kết quả"), 'required');
                $this->form_validation->set_rules('category_calibration', lang("Nhóm hiệu chuẩn"), 'required');
                $this->form_validation->set_rules('category_plan', lang("Nhóm kế hoạch"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('request_plan_calibration');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $machines_id = $this->input->post('machines_id');
                    $result_id = $this->input->post('result_id');
                    $category_calibration = ($this->input->post('category_calibration'));
                    $bp_calibration = ($this->input->post('bp_calibration'));
                    $detail_calibration = ($this->input->post('detail_calibration'));
                    $category_plan = ($this->input->post('category_plan'));
                    $procedure = ($this->input->post('procedure'));
                    $staff_id = ($this->input->post('staff_id'));
                    $quantity = number_unformat($this->input->post('quantity'));
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'machines_id' => $machines_id,
                        'result_id' => $result_id,
                        'category_calibration' => $category_calibration,
                        'bp_calibration' => $bp_calibration,
                        'detail_calibration' => $detail_calibration,
                        'procedure' => $procedure,
                        'quantity' => $quantity,
                        'staff_id' => $staff_id,
                        'category_plan' => $category_plan,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_request_plan_calibration', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('request_plan_calibration') == $reference_no) {
                            updateReference('request_plan_calibration');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'request_plan_calibration',
                            'table_obj' => 'tbl_request_plan_calibration',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu kế hoạch hiệu chuẩn') . ' [' . $reference_no . ']',
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
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_request_plan_calibration.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
                $this->form_validation->set_rules('result_id', lang("Kết quả"), 'required');
                $this->form_validation->set_rules('category_calibration', lang("Nhóm hiệu chuẩn"), 'required');
                $this->form_validation->set_rules('category_plan', lang("Nhóm kế hoạch"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $machines_id = $this->input->post('machines_id');
                    $result_id = $this->input->post('result_id');
                    $category_calibration = ($this->input->post('category_calibration'));
                    $bp_calibration = ($this->input->post('bp_calibration'));
                    $detail_calibration = ($this->input->post('detail_calibration'));
                    $procedure = ($this->input->post('procedure'));
                    $staff_id = ($this->input->post('staff_id'));
                    $quantity = number_unformat($this->input->post('quantity'));
                    $category_plan = ($this->input->post('category_plan'));
                    $fields = [
                        'date' => $date,
                        'machines_id' => $machines_id,
                        'result_id' => $result_id,
                        'category_calibration' => $category_calibration,
                        'bp_calibration' => $bp_calibration,
                        'detail_calibration' => $detail_calibration,
                        'procedure' => $procedure,
                        'quantity' => $quantity,
                        'staff_id' => $staff_id,
                        'category_plan' => $category_plan,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_request_plan_calibration', $fields);
                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'request_plan_calibration',
                            'table_obj' => 'tbl_request_plan_calibration',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu kế hoạch hiệu chuẩn') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddRequestPlanCalibration) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_request_plan_calibration');
            } else {
                if (!$this->preEditRequestPlanCalibration) {
                    accessDenied(true);
                }
                $this->db->select('tbl_request_plan_calibration.*');
                $this->db->from('tbl_request_plan_calibration');
                $this->db->where('tbl_request_plan_calibration.id', $id);
                $dtData = $this->db->get()->row_array();
                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'request_plan_calibration');
                $this->db->where('tblproduction_report.object_id', $id);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)) {
                    refererModel(lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !'));
                }
                $data['title'] = lang('dt_edit_request_plan_calibration');
            }
        }
        $data['dtData'] = $dtData;
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('request_plan_calibration');
        $data['dtCategoryPlanTime'] = get_table_where('tbl_category_plan_time');
        $data['dtCategoryCalibration'] = get_table_where('tbl_category_calibration');
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/request_plan_calibration/detail', $data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('dt_view_request_plan_calibration');

        $this->db->select('tbl_request_plan_calibration.*,
           tbl_result.name as name_result,
           tbl_machines.status as status_machines,
           tbl_machines.code as code_machines,
           tbl_machines.name as name_machines,
           tbl_category_calibration.name as category_calibration,
           tbl_category_plan_time.name as category_plan,
        ');
        $this->db->from('tbl_request_plan_calibration');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_request_plan_calibration.machines_id', 'inner');
        $this->db->join('tbl_result', 'tbl_result.id = tbl_request_plan_calibration.result_id', 'left');
        $this->db->join('tbl_category_calibration', 'tbl_category_calibration.id = tbl_request_plan_calibration.category_calibration', 'left');
        $this->db->join('tbl_category_plan_time', 'tbl_category_plan_time.id = tbl_request_plan_calibration.category_plan', 'left');
        $this->db->where('tbl_request_plan_calibration.id', $id);
        $dtData = $this->db->get()->row_array();


        $data['dtData'] = $dtData;
        $this->load->view('admin/request_plan_calibration/view', $data);
    }


    public function delete($id)
    {
        if (!$this->preDeleteRequestPlanCalibration) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_request_plan_calibration.*');
        $this->db->from('tbl_request_plan_calibration');
        $this->db->where('tbl_request_plan_calibration.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->from('tblproduction_report');
        $this->db->where('tblproduction_report.object_type', 'request_plan_calibration');
        $this->db->where('tblproduction_report.object_id', $id);
        $checkExists = $this->db->count_all_results();
        if (!empty($checkExists)) {
            $data['result'] = 0;
            $data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_request_plan_calibration');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'request_plan_calibration',
                'table_obj' => 'tbl_request_plan_calibration',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu kế hoạch hiệu chuẩn') . ' [' . $dtData['reference_no'] . ']',
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
            $inputFileName = 'uploads/import_dt/phieu_yeu_cau_ke_hoach_hieu_chuan.xlsx';
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
            $this->db->select('tbl_request_plan_calibration.*,
                   tbl_result.name as name_result,
                   tbl_machines.status as status_machines,
                   tbl_machines.code as code_machines,
                   tbl_machines.name as name_machines,
                   tbl_category_calibration.name as category_calibration,
                   tbl_category_plan_time.name as category_plan,
                   tbl_category_machines.name as name_category_machines,
                   tblbranch.name as name_branch,
                   (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                     FROM tblproduction_report
                     WHERE tblproduction_report.object_id = tbl_request_plan_calibration.id AND tblproduction_report.object_type = "request_plan_calibration"
                   ) as name_report,
            ');
            $this->db->from('tbl_request_plan_calibration');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_request_plan_calibration.machines_id', 'inner');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_request_plan_calibration.branch_id', 'inner');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_request_plan_calibration.result_id', 'left');
            $this->db->join('tbl_category_calibration', 'tbl_category_calibration.id = tbl_request_plan_calibration.category_calibration', 'left');
            $this->db->join('tbl_category_plan_time', 'tbl_category_plan_time.id = tbl_request_plan_calibration.category_plan', 'left');
            $this->db->join('tbl_category_machines', 'tbl_category_machines.id = tbl_machines.category_machine_id', 'left');

            if (!$this->preViewRequestPlanCalibration) {
                $this->db->where('(tbl_request_plan_calibration.created_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_plan_calibration.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_plan_calibration.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_request_plan_calibration.id asc');
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
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, get_staff_full_name($value['staff_id']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['category_plan']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['category_calibration']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['bp_calibration']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['detail_calibration']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[$colStt] . $row, $value['quantity']);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['name_category_machines'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['code_machines'], PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['name_machines']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['name_branch']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['procedure']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, ($value['name_result']), PHPExcel_Cell_DataType::TYPE_STRING);
                $colStt ++;
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$colStt] . $row, $value['name_report'], PHPExcel_Cell_DataType::TYPE_STRING);
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
