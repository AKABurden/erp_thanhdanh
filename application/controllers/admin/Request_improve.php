<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_improve extends AdminController
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

        $this->preViewRequestImprove = true;
        $this->preViewOwnRequestImprove = true;
        $this->preAddRequestImprove = true;
        $this->preEditRequestImprove = true;
        $this->preApproveRequestImprove = true;
        $this->preDeleteRequestImprove = true;
    }

    public function index()
    {
        if (!$this->preViewRequestImprove && !$this->preViewOwnRequestImprove) {
            access_denied();
        }
        $data['title'] = _l('ch_request_improve');
        $this->load->view('admin/request_improve/index', $data);
    }

    public function getRequestImprove()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_request_improve.id as id',
            'tbl_request_improve.reference_no as reference_no',
            'tbl_request_improve.date as date',
            'tbl_type_improve.name as type_improve',
            'tbl_category_improve.name as category_improve',
            'tbl_request_improve.detail_improve as detail_improve',
            'tbl_request_improve.employees as employees',
            'tbl_request_improve.employees_receive as employees_receive',
            'tbl_request_improve.employees_evaluate as employees_evaluate',
            'tbl_result.name as name_result',
            'tbl_request_improve.propose_improve as propose_improve',
            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
             FROM tblproduction_report
             WHERE tblproduction_report.object_id = tbl_request_improve.id AND tblproduction_report.object_type = "request_improve"
            ) as name_report',
            'tbl_request_improve.standard as standard',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_improve';
        $where = [];
        $filter = [];

        $join = [
            'LEFT JOIN tbl_result ON tbl_result.id = tbl_request_improve.result_id',
            'INNER JOIN tbl_category_improve ON tbl_category_improve.id = tbl_request_improve.category_improve',
            'INNER JOIN tbl_type_improve ON tbl_type_improve.id = tbl_request_improve.type_improve',
        ];

        if (!$this->preViewRequestImprove) {
            array_push($where, 'AND tbl_request_improve.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_improve.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_improve.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['result_id'], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_improve/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['type_improve']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['category_improve']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['detail_improve']) . '</div>';
            $row[] = '<div class="text-right">' . get_staff_full_name($aRow['employees']) . '</div>';
            $row[] = '<div class="text-right">' . get_staff_full_name($aRow['employees_receive']) . '</div>';
            $row[] = '<div class="text-right">' . get_staff_full_name($aRow['employees_evaluate']) . '</div>';
            // $row[] = '<div class="text-left">' . $aRow['name_result'] . '</div>';
            $row[] = '<div class="form-group " style="width: 150px;">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" value="1" id="check_result_true" name="" data-value="1" ' . ($aRow['result_id'] == 1 ? 'checked' : '') . ' onclick="checkResult(' . $aRow['id'] . ', 1)">
                            <label for="check_result_true">Đạt</label>
                        </div>
                        <div class="checkbox checkbox-danger">
                            <input type="checkbox" value="2" id="check_result_false" name="" data-value="2" ' . ($aRow['result_id'] == 2 ? 'checked' : '') . ' onclick="checkResult(' . $aRow['id'] . ', 2)">
                            <label for="check_result_false">Không Đạt</label>
                        </div>
                    </div>';
            $row[] = '<div class="text-left">' . ($aRow['propose_improve']) . '</div>';
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
            if ($aRow['result_id'] == 2) {
                $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=request_improve') . '" class="btn btn-info">Tạo phiếu báo cáo không phù hợp</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>
            ';
            } else {
                $row[] = '';
            }
            $row[] = '<div class="text-left">' . ($aRow['standard']) . '</div>';


            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_improve/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditRequestImprove ? '<a class="tnh-modal" href="' . base_url('admin/request_improve/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteRequestImprove ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_improve/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_request_improve.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                // $this->form_validation->set_rules('result_id', lang("Kết quả"), 'required');
                $this->form_validation->set_rules('category_improve', lang("Nhóm cải tiến"), 'required');
                $this->form_validation->set_rules('type_improve', lang("Loại cải tiến"), 'required');
                $this->form_validation->set_rules('employees', lang("Người đề xuất"), 'required');
                $this->form_validation->set_rules('employees_receive', lang("Người tiếp nhận"), 'required');
                $this->form_validation->set_rules('employees_evaluate', lang("Người đánh giá"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('request_improve');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $category_improve = $this->input->post('category_improve');
                    $type_improve = $this->input->post('type_improve');
                    $detail_improve = $this->input->post('detail_improve');
                    $result_id = $this->input->post('result_id');
                    $standard = $this->input->post('standard');
                    $employees = ($this->input->post('employees'));
                    $employees_receive = ($this->input->post('employees_receive'));
                    $employees_evaluate = ($this->input->post('employees_evaluate'));
                    $propose_improve = ($this->input->post('propose_improve'));
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'category_improve' => $category_improve,
                        'result_id' => $result_id,
                        'standard' => $standard,
                        'type_improve' => $type_improve,
                        'detail_improve' => $detail_improve,
                        'employees' => $employees,
                        'employees_receive' => $employees_receive,
                        'employees_evaluate' => $employees_evaluate,
                        'propose_improve' => $propose_improve,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_request_improve', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('request_improve') == $reference_no) {
                            updateReference('request_improve');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'request_improve',
                            'table_obj' => 'tbl_request_improve',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu cải tiến') . ' [' . $reference_no . ']',
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
                $this->db->select('tbl_request_improve.*');
                $this->db->from('tbl_request_improve');
                $this->db->where('tbl_request_improve.id', $id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_request_improve.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                // $this->form_validation->set_rules('result_id', lang("Kết quả"), 'required');
                $this->form_validation->set_rules('category_improve', lang("Nhóm cải tiến"), 'required');
                $this->form_validation->set_rules('type_improve', lang("Loại cải tiến"), 'required');
                $this->form_validation->set_rules('employees', lang("Người đề xuất"), 'required');
                $this->form_validation->set_rules('employees_receive', lang("Người tiếp nhận"), 'required');
                $this->form_validation->set_rules('employees_evaluate', lang("Người đánh giá"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $category_improve = $this->input->post('category_improve');
                    $type_improve = $this->input->post('type_improve');
                    $detail_improve = $this->input->post('detail_improve');
                    $result_id = $this->input->post('result_id');
                    $standard = $this->input->post('standard');
                    $employees = ($this->input->post('employees'));
                    $employees_receive = ($this->input->post('employees_receive'));
                    $employees_evaluate = ($this->input->post('employees_evaluate'));
                    $propose_improve = ($this->input->post('propose_improve'));
                    $fields = [
                        'date' => $date,
                        'category_improve' => $category_improve,
                        'result_id' => $result_id,
                        'standard' => $standard,
                        'type_improve' => $type_improve,
                        'detail_improve' => $detail_improve,
                        'employees' => $employees,
                        'employees_receive' => $employees_receive,
                        'employees_evaluate' => $employees_evaluate,
                        'propose_improve' => $propose_improve,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_request_improve', $fields);
                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'request_improve',
                            'table_obj' => 'tbl_request_improve',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu cải tiến') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddRequestImprove) {
                    accessDenied(true);
                }
                $data['title'] = lang('ch_add_request_improve');
            } else {
                if (!$this->preEditRequestImprove) {
                    accessDenied(true);
                }
                $this->db->select('tbl_request_improve.*');
                $this->db->from('tbl_request_improve');
                $this->db->where('tbl_request_improve.id', $id);
                $dtData = $this->db->get()->row_array();
                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'request_improve');
                $this->db->where('tblproduction_report.object_id', $id);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)) {
                    refererModel(lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !'));
                }
                $data['title'] = lang('ch_edit_request_improve');
            }
        }
        $data['dtData'] = $dtData;
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('request_improve');
        $data['dtCategoryImprove'] = get_table_where('tbl_category_improve');
        $data['dtTypeImprove'] = get_table_where('tbl_type_improve');
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/request_improve/detail', $data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('ch_view_request_improve');

        $this->db->select('tbl_request_improve.*,
           tbl_result.name as name_result,
           tbl_category_improve.name as category_improve,
           tbl_type_improve.name as type_improve
        ');
        $this->db->from('tbl_request_improve');
        $this->db->join('tbl_result', 'tbl_result.id = tbl_request_improve.result_id', 'left');
        $this->db->join('tbl_category_improve', 'tbl_category_improve.id = tbl_request_improve.category_improve', 'left');
        $this->db->join('tbl_type_improve', 'tbl_type_improve.id = tbl_request_improve.type_improve', 'left');
        $this->db->where('tbl_request_improve.id', $id);
        $dtData = $this->db->get()->row_array();


        $data['dtData'] = $dtData;
        $this->load->view('admin/request_improve/view', $data);
    }

    public function agree()
    {
        if (!$this->preApproveRequestImprove) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_request_improve.*');
        $this->db->from('tbl_request_improve');
        $this->db->where('tbl_request_improve.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 0) {
                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'request_improve');
                $this->db->where('tblproduction_report.object_id', $suggest_id);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
                    echo json_encode($data);
                    die();
                }
            }

            if (($dtData['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data);
                return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'date_status' => $date_status,
                'staff_status' => $staff_status,
            ];

            $this->db->where('id', $suggest_id);
            $up = $this->db->update('tbl_request_improve', $options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'request_improve',
                    'table_obj' => 'tbl_request_improve',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu cải tiến') . ' [' . $dtData['reference_no'] . ']',
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

    public function delete($id)
    {
        if (!$this->preDeleteRequestImprove) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_request_improve.*');
        $this->db->from('tbl_request_improve');
        $this->db->where('tbl_request_improve.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->from('tblproduction_report');
        $this->db->where('tblproduction_report.object_type', 'request_improve');
        $this->db->where('tblproduction_report.object_id', $id);
        $checkExists = $this->db->count_all_results();
        if (!empty($checkExists)) {
            $data['result'] = 0;
            $data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_request_improve');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'request_improve',
                'table_obj' => 'tbl_request_improve',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu cải tiến') . ' [' . $dtData['reference_no'] . ']',
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
            $inputFileName = 'uploads/import_ch/Phieu_yeu_cau_cai_tien.xlsx';
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
            $row = 3;
            $staff_id = get_staff_user_id();
            $this->db->select('tbl_request_improve.*,
            tbl_result.name as name_result,
            tbl_category_improve.name as category_improve,
            tbl_type_improve.name as type_improve,
            (SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
                FROM tblproduction_report
                WHERE tblproduction_report.object_id = tbl_request_improve.id AND tblproduction_report.object_type = "request_improve"
                ) as name_report
            ');
            $this->db->from('tbl_request_improve');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_request_improve.result_id', 'left');
            $this->db->join('tbl_category_improve', 'tbl_category_improve.id = tbl_request_improve.category_improve', 'left');
            $this->db->join('tbl_type_improve', 'tbl_type_improve.id = tbl_request_improve.type_improve', 'left');
            if (!$this->preViewRequestImprove) {
                $this->db->where('(tbl_request_improve.created_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_improve.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_improve.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_request_improve.id asc');
            $items = $this->db->get()->result_array();
            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['type_improve']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['category_improve']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['detail_improve']), PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, get_staff_full_name($value['employees']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, get_staff_full_name($value['employees_receive']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, get_staff_full_name($value['employees_evaluate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['name_result']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['propose_improve']), PHPExcel_Cell_DataType::TYPE_STRING);
                $arrReport = $value['name_report'];
                $htmlReport = '';
                if (!empty($arrReport)) {
                    $arrReport = explode('||', $arrReport);
                    if (!empty($arrReport)) {
                        foreach ($arrReport as $kk => $vv) {
                            $vv = explode('__', $vv);
                            $htmlReport .= $vv[0] . ',';
                        }
                    }
                }
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, ($htmlReport), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($value['standard']), PHPExcel_Cell_DataType::TYPE_STRING);

                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'request_improve||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_request_improve', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/request_improve/';
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
                    $objDrawing1->setCoordinates($columsExcel[13] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, '')->getStyle($columsExcel[13] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A4:N' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:N' . $row)->applyFromArray([
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[0])->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[1])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[11])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[12])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[13])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[23])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[24])->setWidth(17);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_yeu_cau_cai_tien') . '.xls';
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
    function check_result()
    {
        $_data = $this->input->get();
        // tbl_request_improve.result_id

        if (!empty($_data['id']) && !empty($_data['status'])) {
            $this->db->where('id', $_data['id']);
            $this->db->update('tbl_request_improve' , ['result_id' => $_data['status']]);
            $data['alert_type'] = 'success';
            $data['message'] = 'Cập nhật thành công';
            echo json_encode($data);
            die;
        }
        $data['alert_type'] = 'danger';
        $data['message'] = 'Cập nhật thất bại';
        echo json_encode($data);
        die;
    }
}
