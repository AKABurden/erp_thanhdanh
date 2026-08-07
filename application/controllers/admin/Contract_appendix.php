<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contract_appendix extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('contract_appendix_model');
    }

    /**
     * Trang danh sách phụ lục hợp đồng
     */
    public function index(){
        if (!has_permission('contract_appendix', '', 'view')){
            access_denied('contract_appendix');
        }
        $data['title'] = _l('Phụ lục hợp đồng');
        $this->load->view('admin/contract_appendix/contract_appendix', $data);
    }

    /**
     * Lấy dữ liệu phụ lục hợp đồng cho DataTable
     */
    public function getContractAppendix(){
        if (!has_permission('contract_appendix', '', 'view')){
            echo json_encode(['aaData' => []]);
            return;
        }
        $perEditContractAppendix = has_permission('contract_appendix', '', 'edit');
        $perDeleteContractAppendix = has_permission('contract_appendix', '', 'delete');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_contract_appendix.id as id',
            'tbl_contract_appendix.code as code',
            'tbl_contract_appendix.name as name',
            'tbl_contract_labor.code as contract_code',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as staff_name',
            'tbl_contract_appendix.salary as salary',
            'tbl_contract_appendix.salary_position as salary_position',
            'tbl_contract_appendix.file_path as file_path',
            'tbl_contract_appendix.status as status',
            'tbl_contract_appendix.date_created as date_created',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_contract_appendix';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tbl_contract_labor ON tbl_contract_labor.id = tbl_contract_appendix.contract_labor_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_contract_labor.staff_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_contract_appendix.user_status as user_status',
            'tbl_contract_appendix.date_status as date_status',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'.$aRow['code'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['name'].'</div>';
            $row[] = '<div class="text-left">'.$aRow['contract_code'].'</div>';
            $row[] = '<div class="text-left">'.($aRow['staff_name']).'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['salary']).'</div>';
            $row[] = '<div class="text-right">'.formatMoney($aRow['salary_position']).'</div>';
            
            // File download
            $file_link = '';
            if (!empty($aRow['file_path'])) {
                $file_link = '<a href="'.base_url($aRow['file_path']).'" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-download"></i> Tải xuống</a>';
            }
            $row[] = '<div class="text-center">'.$file_link.'</div>';
            
            $row[] = '<div class="text-center">'.(!empty($aRow['date_created']) ? _dt($aRow['date_created']) : '').'</div>';

            // Trạng thái duyệt
            $active_manager = '';
            $staff_manager = '';
            $status = $aRow['status'];
            $agree_manager = '';
            if ($status == 0) {
                $html = "<p>
                    <a id='agree' value='1' data-id='" . $aRow['id'] . "' class='btn btn-success btn-icon'>Duyệt</a>
                    <a id='reject' value='2' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Không Duyệt</a>
                    <button class='btn po-close btn-icon'>Thoát</button></p>";
                
                $agree_manager = '<div class="mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . htmlspecialchars($html, ENT_QUOTES) . '" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div>';
                $staff_manager = '';
            } elseif ($status == 1) {
                $html = "<p>
                    <a id='reject' value='2' data-id='" . $aRow['id'] . "' class='btn btn-danger btn-icon'>Không Duyệt</a>
                    <button class='btn po-close btn-icon'>Thoát</button></p>";
                
                $agree_manager = '<div class="mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . htmlspecialchars($html, ENT_QUOTES) . '" class="label label-success po" data-original-title="Trạng thái">Đã duyệt</span></div>';
                $staff_manager = '' . staff_profile_image($aRow['user_status'], ['staff-profile-image-small-2x mbot5'], 'small') . ''.get_staff_full_name($aRow['user_status']).' <br/> Vào lúc: ' . _dt($aRow['date_status']) . '';
                $active_manager = 'active';
                
            } elseif ($status == 2) {
                $html = "<p>
                    <a id='agree' value='1' data-id='" . $aRow['id'] . "' class='btn btn-success btn-icon'>Duyệt</a>
                    <button class='btn po-close btn-icon'>Thoát</button></p>";
                
                $agree_manager = '<div class="mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . htmlspecialchars($html, ENT_QUOTES) . '" class="label label-danger po" data-original-title="Trạng thái">Không duyệt</span></div>';
                $staff_manager = '' . staff_profile_image($aRow['user_status'], ['staff-profile-image-small-2x mbot5'], 'small') . ' '.get_staff_full_name($aRow['user_status']).' <br/> Vào lúc: ' . _dt($aRow['date_status']) . '';
                $active_manager = 'active';
            } else {
                $agree_manager = '<div class="mbot5"><span class="label label-default">Không xác định</span></div>';
                $staff_manager = '';
            }

            $process = '<div>
                <div class="wrap-content-process  ' . $active_manager . '">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="approve-manager">
                                ' . $agree_manager . '
                                ' . $staff_manager . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
            $row[] = $process;

            $edit = $perEditContractAppendix ? '<a class="tnh-modal" href="' . base_url('admin/contract_appendix/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>' : '';

            $delete = $perDeleteContractAppendix ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/contract_appendix/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    /**
     * Chi tiết/Thêm mới/Sửa phụ lục hợp đồng
     */
    public function detail($id = 0){
        // Kiểm tra quyền edit hoặc create tùy vào có ID hay không
        if (!empty($id)) {
            if (!has_permission('contract_appendix', '', 'edit')){
                access_denied('contract_appendix');
            }
        } else {
            if (!has_permission('contract_appendix', '', 'create')){
                access_denied('contract_appendix');
            }
        }
        
        $data = [];
        $this->db->select('tbl_contract_appendix.*');
        $this->db->from('tbl_contract_appendix');
        $this->db->where('tbl_contract_appendix.id',$id);
        $dtData = $this->db->get()->row_array();

        if (!empty($dtData) && $dtData['status'] == 1) {
            refererModel(lang('Phụ lục hợp đồng đã được duyệt, không thể chỉnh sửa'));
            return;
        }

        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['code'] != $this->input->post('code')) {
                    $this->form_validation->set_rules('code', lang("Mã phụ lục"), 'trim|required|is_unique[tbl_contract_appendix.code]');
                }
            } else {
                $this->form_validation->set_rules('code', lang("Mã phụ lục"), 'required|is_unique[tbl_contract_appendix.code]');
            }
            $this->form_validation->set_rules('name', lang("Tên phụ lục"), 'required');
            $this->form_validation->set_rules('contract_labor_id', lang("Hợp đồng lao động"), 'required');
            
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $code = $this->input->post('code');
                    $name = $this->input->post('name');
                    $contract_labor_id = $this->input->post('contract_labor_id');
                    $salary = number_unformat($this->input->post('salary'));
                    $salary_position = number_unformat($this->input->post('salary_position'));
                    $date_sign = $this->input->post('date_sign') ? to_sql_date($this->input->post('date_sign')) : null;
                    $note = $this->input->post('note');
                    
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'contract_labor_id' => $contract_labor_id,
                        'salary' => $salary,
                        'salary_position' => $salary_position,
                        'date_sign' => $date_sign,
                        'note' => $note,
                        'status' => 0,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    
                    $this->db->insert('tbl_contract_appendix',$fields);
                    $id = $this->db->insert_id();
                    
                    if ($id){
                        // Upload file
                        $upload_result = $this->contract_appendix_model->handle_file_upload($id);
                        
                        insertActivityLog([
                            'type_parent_obj' => 'contract_appendix',
                            'table_obj' => 'tbl_contract_appendix',
                            'id_obj' => $id,
                            'name_obj' => $code,
                            'content' => lang('Thêm phụ lục hợp đồng') . ' [' . $code . ']',
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
                    $code = $this->input->post('code');
                    $name = $this->input->post('name');
                    $contract_labor_id = $this->input->post('contract_labor_id');
                    $salary = str_replace(',', '', $this->input->post('salary'));
                    $salary_position = str_replace(',', '', $this->input->post('salary_position'));
                    $date_sign = $this->input->post('date_sign') ? to_sql_date($this->input->post('date_sign')) : null;
                    $note = $this->input->post('note');
                    
                    $fields = [
                        'code' => $code,
                        'name' => $name,
                        'contract_labor_id' => $contract_labor_id,
                        'salary' => $salary,
                        'salary_position' => $salary_position,
                        'date_sign' => $date_sign,
                        'note' => $note,
                    ];
                    
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_contract_appendix',$fields);
                    
                    if ($success){
                        // Upload file
                        $upload_result = $this->contract_appendix_model->handle_file_upload($id);
                        insertActivityLog([
                            'type_parent_obj' => 'contract_appendix',
                            'table_obj' => 'tbl_contract_appendix',
                            'id_obj' => $id,
                            'name_obj' => $dtData['code'],
                            'content' => lang('Sửa phụ lục hợp đồng') . ' [' . $dtData['code'] . ']',
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
                $data['title'] = lang('Thêm phụ lục hợp đồng');
            } else {
                $data['dtData'] = $dtData;
                $data['title'] = lang('Sửa phụ lục hợp đồng');
            }
        }
        $data['id'] = $id;
        $this->load->view('admin/contract_appendix/detail_contract_appendix',$data);
    }

    /**
     * Xóa phụ lục hợp đồng
     */
    public function delete($id){
        if (!has_permission('contract_appendix', '', 'delete')){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_contract_appendix.*');
        $this->db->from('tbl_contract_appendix');
        $this->db->where('tbl_contract_appendix.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        if ($dtData['status'] == 1) {
            $data['result'] = 0;
            $data['message'] = lang('Phụ lục hợp đồng đã được duyệt, không thể xóa');
            echo json_encode($data); die();
        }

        // Xóa file nếu có
        if (!empty($dtData['file_path']) && file_exists($dtData['file_path'])) {
            unlink($dtData['file_path']);
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_contract_appendix');
        if ($success){
            insertActivityLog([
                'type_parent_obj' => 'contract_appendix',
                'table_obj' => 'tbl_contract_appendix',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa phụ lục hợp đồng') . ' [' . $dtData['code'] . ']',
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

    /**
     * Thay đổi trạng thái phụ lục hợp đồng (Duyệt/Không duyệt)
     */
    public function change_status(){
        if (!has_permission('contract_appendix', '', 'edit')){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        
        $data = [];
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        
        if (empty($id)){
            $data['result'] = 0;
            $data['message'] = lang('Không tìm thấy dữ liệu');
            echo json_encode($data);
            die();
        }
        
        $this->db->select('tbl_contract_appendix.*');
        $this->db->from('tbl_contract_appendix');
        $this->db->where('tbl_contract_appendix.id', $id);
        $dtData = $this->db->get()->row_array();
        
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('Không tìm thấy phụ lục hợp đồng');
            echo json_encode($data);
            die();
        }
        
        $updateData = [
            'status' => $status,
            'date_status' => date('Y-m-d H:i:s'),
            'user_status' => get_staff_user_id()
        ];
        
        $this->db->where('id', $id);
        $success = $this->db->update('tbl_contract_appendix', $updateData);
        
        if ($success){
            $statusText = '';
            if ($status == 1){
                $statusText = 'Đã duyệt';
                
                // Lấy thông tin hợp đồng hiện tại để lưu giá trị cũ
                $this->db->select('salary_basic, salary_position, date_sign');
                $this->db->from('tbl_contract_labor');
                $this->db->where('id', $dtData['contract_labor_id']);
                $currentContract = $this->db->get()->row_array();
                
                if ($currentContract) {
                    // Lưu giá trị cũ vào phụ lục để khôi phục sau này
                    $oldValues = [
                        'old_salary' => $currentContract['salary_basic'],
                        'old_salary_position' => $currentContract['salary_position'],
                        'old_date_sign' => $currentContract['date_sign']
                    ];
                    $this->db->where('id', $id);
                    $this->db->update('tbl_contract_appendix', $oldValues);
                }
                
                // Cập nhật lương và ngày tái ký trong hợp đồng lao động
                $updateContractData = [];
                
                if (!empty($dtData['salary']) && $dtData['salary'] > 0) {
                    $updateContractData['salary_basic'] = $dtData['salary'];
                }
                
                if (!empty($dtData['salary_position']) && $dtData['salary_position'] > 0) {
                    $updateContractData['salary_position'] = $dtData['salary_position'];
                }
                
                if (!empty($dtData['date_sign'])) {
                    $updateContractData['date_sign'] = $dtData['date_sign'];
                }
                
                if (!empty($updateContractData)) {
                    $this->db->where('id', $dtData['contract_labor_id']);
                    $this->db->update('tbl_contract_labor', $updateContractData);
                    
                    $salaryInfo = [];
                    if (isset($updateContractData['salary_basic'])) {
                        $salaryInfo[] = 'Lương cơ bản: ' . formatMoney($dtData['salary']);
                    }
                    if (isset($updateContractData['salary_position'])) {
                        $salaryInfo[] = 'Lương vị trí: ' . formatMoney($dtData['salary_position']);
                    }
                    if (isset($updateContractData['date_sign'])) {
                        $salaryInfo[] = 'Ngày tái ký: ' . _dhau($dtData['date_sign']);
                    }
                    
                    insertActivityLog([
                        'type_parent_obj' => 'contract_labor',
                        'table_obj' => 'tbl_contract_labor',
                        'id_obj' => $dtData['contract_labor_id'],
                        'name_obj' => '',
                        'content' => lang('Cập nhật từ phụ lục hợp đồng') . ' [' . $dtData['code'] . '] - ' . implode(', ', $salaryInfo),
                        'actions' => 'update'
                    ]);
                }
            } elseif ($status == 2){
                $statusText = 'Không duyệt';
                
                // Nếu trước đó đã duyệt (status = 1), khôi phục lại giá trị cũ
                if ($dtData['status'] == 1) {
                    $restoreContractData = [];
                    
                    // Khôi phục lương cơ bản
                    if (isset($dtData['old_salary'])) {
                        $restoreContractData['salary_basic'] = $dtData['old_salary'];
                    }
                    
                    // Khôi phục lương vị trí
                    if (isset($dtData['old_salary_position'])) {
                        $restoreContractData['salary_position'] = $dtData['old_salary_position'];
                    }
                    
                    // Khôi phục ngày tái ký
                    if (isset($dtData['old_date_sign'])) {
                        $restoreContractData['date_sign'] = $dtData['old_date_sign'];
                    }
                    
                    if (!empty($restoreContractData)) {
                        $this->db->where('id', $dtData['contract_labor_id']);
                        $this->db->update('tbl_contract_labor', $restoreContractData);
                        
                        $restoreInfo = [];
                        if (isset($restoreContractData['salary_basic'])) {
                            $restoreInfo[] = 'Lương cơ bản: ' . formatMoney($dtData['old_salary']);
                        }
                        if (isset($restoreContractData['salary_position'])) {
                            $restoreInfo[] = 'Lương vị trí: ' . formatMoney($dtData['old_salary_position']);
                        }
                        if (isset($restoreContractData['date_sign'])) {
                            $restoreInfo[] = 'Ngày tái ký: ' . (!empty($dtData['old_date_sign']) ? _dhau($dtData['old_date_sign']) : 'Không có');
                        }
                        
                        insertActivityLog([
                            'type_parent_obj' => 'contract_labor',
                            'table_obj' => 'tbl_contract_labor',
                            'id_obj' => $dtData['contract_labor_id'],
                            'name_obj' => '',
                            'content' => lang('Khôi phục giá trị cũ do bỏ duyệt phụ lục') . ' [' . $dtData['code'] . '] - ' . implode(', ', $restoreInfo),
                            'actions' => 'update'
                        ]);
                    }
                }
            }
            
            insertActivityLog([
                'type_parent_obj' => 'contract_appendix',
                'table_obj' => 'tbl_contract_appendix',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Cập nhật trạng thái phụ lục hợp đồng') . ' [' . $dtData['code'] . '] - ' . $statusText,
                'actions' => 'update'
            ]);
            
            $data['result'] = 1;
            $data['message'] = lang('Cập nhật trạng thái thành công: ' . $statusText);
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Cập nhật trạng thái thất bại');
        }
        
        echo json_encode($data);
    }

    /**
     * Ajax lấy danh sách hợp đồng lao động cho select2 (compatible với ajaxSelectCallBack)
     */
    public function get_contract_labor_ajax($id = 0){
        $term = $this->input->get('term');
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 50;

        $this->db->select('tbl_contract_labor.id, tbl_contract_labor.code, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name');
        $this->db->from('tbl_contract_labor');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_contract_labor.staff_id', 'inner');
        $this->db->where('tbl_contract_labor.status', 1);
        
        // Nếu có ID (callback khi edit), chỉ lấy hợp đồng đó
        if (!empty($id)) {
            $this->db->where('tbl_contract_labor.id', $id);
            $result = $this->db->get()->row_array();
            
            if ($result) {
                $response = [
                    'row' => [
                        'id' => $result['id'],
                        'text' => $result['code'] . ' - ' . $result['staff_name']
                    ]
                ];
            } else {
                $response = ['row' => null];
            }
            
            echo json_encode($response);
            return;
        }
        
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_contract_labor.code', $term);
            $this->db->or_like('tblstaff.firstname', $term);
            $this->db->or_like('tblstaff.lastname', $term);
            $this->db->group_end();
        }
        
        $this->db->limit($limit);
        $this->db->order_by('tbl_contract_labor.date_created', 'DESC');
        $result = $this->db->get()->result_array();

        $data = [];
        foreach ($result as $row) {
            $data[] = [
                'id' => $row['id'],
                'text' => $row['code'] . ' - ' . $row['staff_name']
            ];
        }

        $response = ['results' => $data];
        echo json_encode($response);
    }
}
