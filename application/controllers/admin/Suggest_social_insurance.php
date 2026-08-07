<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_social_insurance extends AdminController
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

        $this->preViewSuggestSocialInSurance = true;
        $this->preViewOwnSuggestSocialInSurance = true;
        $this->preAddSuggestSocialInSurance = true;
        $this->preEditSuggestSocialInSurance = true;
        $this->preApproveSuggestSocialInSurance = true;
        $this->preDeleteSuggestSocialInSurance = true;

        $this->percent_bhxh = '25.5';
        $this->percent_bhtn = '2';
        $this->percent_bhyt = '4.5';
        $this->percent_union = '2';
    }

    public function index()
    {
        if (!$this->preViewSuggestSocialInSurance && !$this->preViewOwnSuggestSocialInSurance) {
            access_denied();
        }
        $data['title'] = _l('suggest_social_insurance');
        $this->load->view('admin/suggest_social_insurance/index', $data);
    }

    public function getSuggestSocialInsurance()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_social_insurance.id as id',
            'tbl_suggest_social_insurance.reference_no as reference_no',
            'tbl_suggest_social_insurance.date as date',
            'tb_department.name_department as name_department',
            'tblroles.name as name_role',
            'tblstaff.code as code_staff',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff',
            'tbl_suggest_social_insurance.salary as salary',
            'tbl_suggest_social_insurance.salary_bhxh as salary_bhxh',
            'tbl_suggest_social_insurance.bhxh_salary as bhxh_salary',
            'tbl_suggest_social_insurance.bhtn_salary as bhtn_salary',
            'tbl_suggest_social_insurance.bhyt_salary as bhyt_salary',
            'tbl_suggest_social_insurance.union_salary as union_salary',
            'tbl_suggest_social_insurance.grand_total as grand_total',
            'tbl_suggest_social_insurance.status as status',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_social_insurance';
        $where = [];
        $filter = [];
        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbl_room.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            JOIN tbl_room ON tbl_room.id = tbldepartments.room_id
            GROUP BY tblstaff_departments.staffid
        ) tb_department";
        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_social_insurance.staff_id',
            'LEFT JOIN '.$tbDepartment.' ON tb_department.staffid = tblstaff.staffid',
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
        ];

        if (!$this->preViewSuggestSocialInSurance) {
            array_push($where, 'AND tbl_suggest_social_insurance.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_social_insurance.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_social_insurance.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_social_insurance.date_status',
            'tbl_suggest_social_insurance.staff_status',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_social_insurance/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 120px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['name_department']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['name_role']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['code_staff']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['name_staff']) . '</div>';
            $row[] = '<div class="text-center">' . formatMoney($aRow['salary']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['salary_bhxh']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['bhxh_salary']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['bhtn_salary']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['bhyt_salary']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['union_salary']) . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['grand_total']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } elseif ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left">' . $_data . '</div>';
            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_social_insurance/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestSocialInSurance ? '<a class="tnh-modal" href="' . base_url('admin/suggest_social_insurance/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestSocialInSurance ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_social_insurance/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_social_insurance.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('staff_id', lang("Nhân viên"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_social_insurance');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_id = $this->input->post('staff_id');
                    $salary = number_unformat($this->input->post('salary'));
                    $salary_bhxh = number_unformat($this->input->post('salary_bhxh'));


                    $bhxh_salary = ($salary_bhxh * $this->percent_bhxh) / 100;
                    $bhtn_salary = ($salary_bhxh * $this->percent_bhtn) / 100;
                    $bhyt_salary = ($salary_bhxh * $this->percent_bhyt) / 100;
                    $union_salary = ($salary_bhxh * $this->percent_union) / 100;

                    $grand_total = $bhxh_salary + $bhtn_salary + $bhyt_salary + $union_salary;

                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'staff_id' => $staff_id,
                        'branch_id' => $branch_id,
                        'salary' => $salary,
                        'salary_bhxh' => $salary_bhxh,
                        'percent_bhxh' => $this->percent_bhxh,
                        'percent_bhtn' => $this->percent_bhtn,
                        'percent_bhyt' => $this->percent_bhyt,
                        'percent_union' => $this->percent_union,
                        'bhxh_salary' => $bhxh_salary,
                        'bhtn_salary' => $bhtn_salary,
                        'bhyt_salary' => $bhyt_salary,
                        'union_salary' => $union_salary,
                        'grand_total' => $grand_total,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_suggest_social_insurance', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('suggest_social_insurance') == $reference_no) {
                            updateReference('suggest_social_insurance');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_social_insurance',
                            'table_obj' => 'tbl_suggest_social_insurance',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu bảo hiểm xã hội') . ' [' . $reference_no . ']',
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
                $this->db->select('tbl_suggest_social_insurance.*');
                $this->db->from('tbl_suggest_social_insurance');
                $this->db->where('tbl_suggest_social_insurance.id', $id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_social_insurance.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('staff_id', lang("Nhân viên"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_id = $this->input->post('staff_id');
                    $salary = number_unformat($this->input->post('salary'));
                    $salary_bhxh = number_unformat($this->input->post('salary_bhxh'));
                    $percent_bhxh = $dtData['percent_bhxh'];
                    $percent_bhtn = $dtData['percent_bhtn'];
                    $percent_bhyt = $dtData['percent_bhyt'];
                    $percent_union = $dtData['percent_union'];

                    $bhxh_salary = ($salary_bhxh * $percent_bhxh) / 100;
                    $bhtn_salary = ($salary_bhxh * $percent_bhtn) / 100;
                    $bhyt_salary = ($salary_bhxh * $percent_bhyt) / 100;
                    $union_salary = ($salary_bhxh * $percent_union) / 100;
                    $grand_total = $bhxh_salary + $bhtn_salary + $bhyt_salary + $union_salary;
                    $fields = [
                        'date' => $date,
                        'staff_id' => $staff_id,
                        'branch_id' => $branch_id,
                        'salary' => $salary,
                        'salary_bhxh' => $salary_bhxh,
                        'percent_bhxh' => $percent_bhxh,
                        'percent_bhtn' => $percent_bhtn,
                        'percent_bhyt' => $percent_bhyt,
                        'percent_union' => $percent_union,
                        'bhxh_salary' => $bhxh_salary,
                        'bhtn_salary' => $bhtn_salary,
                        'bhyt_salary' => $bhyt_salary,
                        'union_salary' => $union_salary,
                        'grand_total' => $grand_total,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_suggest_social_insurance', $fields);
                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_social_insurance',
                            'table_obj' => 'tbl_suggest_social_insurance',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu bảo hiểm xã hội') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestSocialInSurance) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_social_insurance');
                $data['dataPercent'] = [
                    'percent_bhxh' => $this->percent_bhxh,
                    'percent_bhtn' => $this->percent_bhtn,
                    'percent_bhyt' => $this->percent_bhyt,
                    'percent_union' => $this->percent_union,
                ];
            } else {
                if (!$this->preEditSuggestSocialInSurance) {
                    accessDenied(true);
                }
                $this->db->select('tbl_suggest_social_insurance.*');
                $this->db->from('tbl_suggest_social_insurance');
                $this->db->where('tbl_suggest_social_insurance.id', $id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['status'] == 1) {
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));
                }
                $data['dataPercent'] = [
                    'percent_bhxh' => $dtData['percent_bhxh'],
                    'percent_bhtn' => $dtData['percent_bhtn'],
                    'percent_bhyt' => $dtData['percent_bhyt'],
                    'percent_union' => $dtData['percent_union'],
                ];
                $data['title'] = lang('dt_edit_suggest_social_insurance');
            }
        }
        $data['dtData'] = $dtData;
        $data['staff'] = getPersonDeparmentdt(0);
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_social_insurance');
        $this->load->view('admin/suggest_social_insurance/detail', $data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('dt_view_suggest_social_insurance');

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbl_room.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            JOIN tbl_room ON tbl_room.id = tbldepartments.room_id
            GROUP BY tblstaff_departments.staffid
        ) tb_department";
        $this->db->select('tbl_suggest_social_insurance.*,
            tb_department.name_department as name_department,
            tblroles.name as name_role,
            tblstaff.code as code_staff,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff,
        ');
        $this->db->from('tbl_suggest_social_insurance');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_suggest_social_insurance.staff_id', 'inner');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->where('tbl_suggest_social_insurance.id', $id);
        $dtData = $this->db->get()->row_array();


        $data['dtData'] = $dtData;
        $this->load->view('admin/suggest_social_insurance/view', $data);
    }

    public function agree()
    {
        if (!$this->preApproveSuggestSocialInSurance) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_social_insurance.*');
        $this->db->from('tbl_suggest_social_insurance');
        $this->db->where('tbl_suggest_social_insurance.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

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
            $up = $this->db->update('tbl_suggest_social_insurance', $options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_social_insurance',
                    'table_obj' => 'tbl_suggest_social_insurance',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu bảo hiểm xã hội') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteSuggestSocialInSurance) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_suggest_social_insurance.*');
        $this->db->from('tbl_suggest_social_insurance');
        $this->db->where('tbl_suggest_social_insurance.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        if ($dtData['status'] == 1) {
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã duyệt không thể xóa !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_suggest_social_insurance');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'suggest_social_insurance',
                'table_obj' => 'tbl_suggest_social_insurance',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu bảo hiểm xã hội') . ' [' . $dtData['reference_no'] . ']',
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
            $inputFileName = 'uploads/import_ch/phieu_yeu_cau_bhxh.xlsx';
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
            $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbl_room.name) as name_department
                FROM tbldepartments
                JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                JOIN tbl_room ON tbl_room.id = tbldepartments.room_id
                GROUP BY tblstaff_departments.staffid
            ) tb_department";
            $this->db->select('tbl_suggest_social_insurance.*,
                tb_department.name_department as name_department,
                tblroles.name as name_role,
                tblstaff.code as code_staff,
                CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff,
            ');
            $this->db->from('tbl_suggest_social_insurance');
            $this->db->join('tblstaff', 'tblstaff.staffid = tbl_suggest_social_insurance.staff_id', 'inner');
            $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            if (!$this->preViewSuggestSocialInSurance) {
                $this->db->where('(tbl_suggest_social_insurance.created_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_social_insurance.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_repair.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_suggest_social_insurance.id asc');
            $items = $this->db->get()->result_array();

            $dem = 0;

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['name_department']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, $value['name_role']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, $value['code_staff']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['name_staff']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, ($value['salary']) )->getStyle("$columsExcel[7]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['salary']));

                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[8] . $row, ($value['salary_bhxh']))->getStyle("$columsExcel[8]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['salary_bhxh']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, $value['bhxh_salary'])->getStyle("$columsExcel[9]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['salary_bhxh']));

                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[10] . $row, ($value['bhtn_salary']))->getStyle("$columsExcel[10]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['salary_bhxh']));

                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, ($value['bhyt_salary']))->getStyle("$columsExcel[11]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['salary_bhxh']));
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[12] . $row, ($value['union_salary']))->getStyle("$columsExcel[12]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['salary_bhxh']));

                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, ($value['grand_total']))->getStyle("$columsExcel[13]$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['salary_bhxh']));


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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(20);
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
            $filename = lang('phieu_yeu_cau_bhxh') . '.xls';
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

    public function searchMachines($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            CONCAT(tbl_machines.id,"__",tblcosts.id) as id, 
            CONCAT(tbl_machines.name,"(",tblcosts.name,")") as text,
            tbl_machines.code as code,
            tbl_machines.name as name,
        ', false);
        $this->db->from('tbl_machines');
        $this->db->join('tblcosts','tbl_machines.id = tblcosts.object_id AND tblcosts.type_cost = 2');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_machines.name', $term);
            $this->db->or_like('tbl_machines.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Thiết bị'), 'children' => $pod];
        if (!empty($id)){
            $dtId = explode('__',$id);
            $dtMachines = get_table_where('tbl_machines',['id' => $dtId[0]],'','row_array');
            $dtCost = get_table_where('tblcosts',['id' => $dtId[1]],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'].'__'.$dtCost['id'], 'text' => $dtMachines['name'].'('.$dtCost['name'].')'];
        }
        echo json_encode($data);
    }
}
