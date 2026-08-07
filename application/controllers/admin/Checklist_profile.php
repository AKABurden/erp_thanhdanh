<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Checklist_profile extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('checklist_profile_model');
        $this->load->model('propose_offer_model');
    }

    /**
     * Main list view - Dashboard
     */
    function index()
    {
        $data = [];
        $data['title'] = 'Quản Lý Tiếp Nhận (Onboarding)';
        $data['type'] = 1;
        $this->load->view('admin/checklist_profile/manage', $data);
    }

    /**
     * Get Approved Offers (chưa có trong hệ thống HR - nguồn để tạo checklist)
     */
    public function getApprovedOffers()
    {
        $search = $this->input->get('q');

        // Query offer đã duyệt nhưng chưa có checklist
        $this->db->select('
            tbl_propose_offer.id,
            tbl_propose_offer.ma_offer,
            tbl_hr_eprofile.full_name as ho_ten,
            tbl_propose_offer.vi_tri_offer as position,
            tbl_propose_offer.phong_ban_offer as department,
            tbl_propose_offer.ngay_tao as offer_date,
            tbl_propose_offer.luong_p1,
            tbl_propose_offer.luong_p2,
            tbl_propose_offer.phu_cap,
            tbl_propose_offer.kqpv_id,
            tbl_propose_offer.evaluation_employee_id
        ');
        $this->db->from('tbl_propose_offer');
        $this->db->where('tbl_propose_offer.trang_thai', 'DA_GUI'); // Đã duyệt

        // LEFT JOIN để lấy những offer chưa có checklist
        $this->db->join('tbl_checklist_profile', 'tbl_checklist_profile.offer_id = tbl_propose_offer.id', 'left');
        $this->db->join('tbl_hr_eprofile', 'tbl_hr_eprofile.id = tbl_propose_offer.kqpv_id', 'left');
        $this->db->where('tbl_checklist_profile.id IS NULL'); // Chưa có checklist

        if ($search) {
            $this->db->group_start();
            $this->db->like('tbl_hr_eprofile.full_name', $search);
            $this->db->or_like('tbl_propose_offer.ma_offer', $search);
            $this->db->group_end();
        }

        $this->db->order_by('tbl_propose_offer.ngay_tao', 'DESC');
        $this->db->limit(20);
        $offers = $this->db->get()->result_array();

        // Get candidate data from evaluation_employee
        foreach ($offers as &$offer) {
            $eval_id = $offer['evaluation_employee_id'];
            if ($eval_id) {
                $eval_data = $this->db->get_where('tbl_evaluation_employee', ['id' => $eval_id])->row();
                if ($eval_data) {
                    $offer['data'] = [
                        'ho_ten' => $offer['ho_ten'],
                        'ngay_sinh' => $eval_data->date_of_birth ?? '',
                        'sdt' => $eval_data->phone_number ?? '',
                        'email' => $eval_data->email ?? '',
                        'tai_khoan_ngan_hang' => '',
                        'ten_ngan_hang' => '',
                        'mst' => '',
                        'giam_tru_gia_canh' => false,
                        'bang_cap_cong_chung' => false,
                        'bhxh_detail' => '',
                        'luong_thoa_thuan' => number_format($offer['luong_p1'] + $offer['luong_p2'])
                    ];
                }
            }
        }

        echo json_encode(['items' => $offers]);
    }

    /**
     * DataTables server-side data endpoint
     */
    function getChecklistProfile()
    {
        $aColumns = [
            'tbl_checklist_profile.id as id',
            'tbl_checklist_profile.ma_checklist as ma_checklist',
            'tbl_hr_eprofile.full_name as ho_ten',
            'tbl_checklist_profile.position as position',
            'tbl_checklist_profile.offer_date as offer_date',
            'tbl_checklist_profile.employee_id as employee_id',
            'tbl_checklist_profile.status as status',
            'tbl_checklist_profile.id as actions'
        ];

        $sIndexColumn = 'id';
        $sTable = 'tbl_checklist_profile';
        $where = [];
        $filter = [];

        // Get filters from request
        $search_filter = $this->input->post('search_filter');
        $status_filter = $this->input->post('status_filter');

        // Build where clause for filters
        if (!empty($search_filter)) {
            $filter[] = 'AND (tbl_checklist_profile.ma_checklist LIKE "%' . $search_filter . '%" OR tbl_checklist_profile.ho_ten LIKE "%' . $search_filter . '%")';
        }
        if (!empty($status_filter)) {
            $filter[] = 'AND tbl_checklist_profile.status = "' . $status_filter . '"';
        }

        $join = [
            'LEFT JOIN tbl_propose_offer ON tbl_propose_offer.id=tbl_checklist_profile.offer_id',
            'LEFT JOIN tbl_hr_eprofile ON tbl_hr_eprofile.id=tbl_propose_offer.kqpv_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $filter, '', []);
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];
            foreach ($aColumns as $v) {
                $_data = $aRow[$v];
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    /**
     * Create checklist from offer
     */

    public function createFromOfferPut($id = '')
    {
        $_POST['offer_id'] = $id;
        $this->createFromOffer();
    }

    function createFromOffer()
    {
        if (!$this->input->post()) {
            echo json_encode(['result' => 0, 'message' => 'Invalid request']);
            return;
        }

        $offer_id = $this->input->post('offer_id');
        $offer = $this->db->get_where('tbl_propose_offer', ['id' => $offer_id])->row();

        if (!$offer) {
            echo json_encode(['result' => 0, 'message' => 'Offer không tồn tại']);
            return;
        }

        if ($offer->trang_thai != 'DA_GUI') {
            echo json_encode(['result' => 0, 'message' => 'Offer Chưa gửi Email']);
            return;
        }

        // Check if checklist already exists
        $existing = $this->db->get_where('tbl_checklist_profile', ['offer_id' => $offer_id])->row();
        if ($existing) {
            echo json_encode(['result' => 0, 'message' => 'Checklist đã tồn tại cho offer này']);
            return;
        }

        // Create checklist
        $ma_checklist = 'CL' . date('ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        $data = [
            'ma_checklist' => $ma_checklist,
            'offer_id' => $offer_id,
            'ma_offer' => $offer->ma_offer,
            'ho_ten' => $offer->ten_ung_vien,
            'position' => $offer->vi_tri_offer,
            'department' => $offer->phong_ban_offer,
            'offer_date' => $offer->ngay_tao,
            'status' => 'S6', // Bắt đầu từ S6 - Đang đối chiếu
            'checklist_data' => json_encode([
                'ho_so_phap_ly_day_du' => false,
                'bang_cap_cong_chung' => false,
                'bhxh_detail_valid' => false,
                'tai_khoan_ngan_hang_exist' => false,
                'luong_p1_p2_valid' => false
            ]),
            'candidate_data' => json_encode([
                'ho_ten' => $offer->ten_ung_vien,
                'vi_tri' => $offer->vi_tri_offer,
                'phong_ban' => $offer->phong_ban_offer,
                'luong_p1' => $offer->luong_p1,
                'luong_p2' => $offer->luong_p2,
                'phu_cap' => $offer->phu_cap
            ]),
            'staff_create' => get_staff_user_id(),
            'nguoi_tao' => get_staff_full_name(get_staff_user_id()),
            'ngay_tao' => date('Y-m-d H:i:s')
        ];

        $checklist_id = $this->checklist_profile_model->insert($data);

        if ($checklist_id) {
            echo json_encode([
                'result' => 1,
                'success' => 1,
                'alert_type' => 'success',
                'message' => 'Tạo Checklist thành công!',
                'id' => $checklist_id
            ]);
        } else {
            echo json_encode([
                'result' => 0,
                'success' => 0,
                'alert_type' => 'danger',
                'message' => 'Có lỗi khi tạo Checklist'
            ]);
        }
    }

    /**
     * Detail/Edit view
     */
    function handling($id)
    {
        $data = [];
        $checklist = $this->checklist_profile_model->getById($id);
        if (!$checklist) {
            show_404();
            return;
        }

        if ($this->input->post()) {
            // Update checklist data
            $updateData = [
                'checklist_data' => json_encode($this->input->post('checklist')),
                'candidate_data' => json_encode($this->input->post('candidate_data')),
                'status' => $this->input->post('status'),
                'date_update' => date('Y-m-d H:i:s'),
                'staff_update' => get_staff_user_id()
            ];

            if ($this->input->post('employee_id')) {
                $updateData['employee_id'] = $this->input->post('employee_id');
            }

            $success = $this->checklist_profile_model->update($id, $updateData);

            echo json_encode([
                'result' => $success ? 1 : 0,
                'message' => $success ? 'Cập nhật thành công!' : 'Có lỗi xảy ra'
            ]);
            return;
        }
        $data['offer'] = $this->propose_offer_model->getProposeOfferById($checklist->offer_id);
        // echo '<pre>';print_arrays($data['offer']);die;
        $data['offer_data'] = (array)($data['offer']);
        $this->db->where('id', $data['offer']->kqpv_id);
        $data['candidate_data'] = $this->db->get('tbl_hr_eprofile')->row_array();

        // Get attachments (CV, portfolio files)
        $data['attachments'] = [];
        if (!empty($data['offer']->kqpv_id)) {
            $data['attachments'] = $this->db->get_where('tblfiles', [
                'rel_id' => $data['offer']->kqpv_id,
                'rel_type' => 'eprofile',
            ])->result_array();
        }

        $data['checklist'] = $checklist;
        $data['checklist_data'] = json_decode($checklist->checklist_data, true);
        // echo '<pre>';print_arrays($data['candidate_data']);die;
        $this->load->view('admin/checklist_profile/handling_new', $data);
    }

    /**
     * Update status (S6 -> S7 -> S8 -> S9)
     */
    function updateStatus($id)
    {
        if (!$this->input->post()) {
            echo json_encode(['result' => 0, 'message' => 'Invalid request']);
            return;
        }

        $new_status = $this->input->post('status');
        $checklist_data = $this->input->post('checklist_data');
        $employee_id = $this->input->post('employee_id');

        $updateData = [
            'status' => $new_status,
            'date_update' => date('Y-m-d H:i:s'),
            'staff_update' => get_staff_user_id()
        ];

        if ($checklist_data) {
            $updateData['checklist_data'] = $checklist_data;
        }

        if ($employee_id) {
            $updateData['employee_id'] = $employee_id;
        }

        // Log status change
        if ($new_status == 'S7') {
            $updateData['approved_s7_date'] = date('Y-m-d H:i:s');
            $updateData['approved_s7_by'] = get_staff_user_id();
        } elseif ($new_status == 'S8') {
            // Tạo nhân viên thực sự khi chuyển sang S8
            $staff_id = $this->createStaffFromChecklist($id, $employee_id);

            if (!$staff_id) {
                echo json_encode([
                    'result' => 0,
                    'message' => 'Không thể tạo hồ sơ nhân viên. Vui lòng kiểm tra lại thông tin.'
                ]);
                return;
            }

            $updateData['created_employee_date'] = date('Y-m-d H:i:s');
            $updateData['created_employee_by'] = get_staff_user_id();
            $updateData['staff_id'] = $staff_id; // Lưu ID staff thực trong tblstaff
        } elseif ($new_status == 'S9') {
            // Chuyển nhân viên sang chính thức
            $checklist = $this->checklist_profile_model->getById($id);
            if ($checklist && $checklist->staff_id) {
                // Update status_work từ 0 (thử việc) sang 1 (chính thức)
                $this->db->where('staffid', $checklist->staff_id);
                $this->db->update(db_prefix() . 'staff', [
                    'status_work' => 1,
                    'date_status_work' => date('Y-m-d')
                ]);
            }
            $updateData['finalized_date'] = date('Y-m-d H:i:s');
            $updateData['finalized_by'] = get_staff_user_id();
        }

        $success = $this->checklist_profile_model->update($id, $updateData);

        if(!empty($checklist) && !empty($checklist->offer_id)) {
            $this->db->where('id', $checklist->offer_id);
            $propose_offer = $this->db->get('tbl_propose_offer')->row();

            $this->db->where('id_yctd', $propose_offer->id_yctd);
            $this->db->join('tbl_checklist_profile', 'tbl_checklist_profile.offer_id = tbl_propose_offer.id');
            $all = $this->db->get('tbl_propose_offer')->num_rows();

            $this->db->where('id_yctd', $propose_offer->id_yctd);
            $this->db->where('tbl_checklist_profile.status', 'S9');
            $this->db->join('tbl_checklist_profile', 'tbl_checklist_profile.offer_id = tbl_propose_offer.id');
            $count_success = $this->db->get('tbl_propose_offer')->num_rows();
            if(!empty($all) && $all == $count_success) {
                $this->db->where('id_requirements', $propose_offer->id_yctd);
                $this->db->where('id_step_default', 5);
                $this->db->update('tbl_hr_requirements_step', [
                    'status' => 1
                ]);
            }

        }

        echo json_encode([
            'result' => $success ? 1 : 0,
            'message' => $success ? 'Cập nhật trạng thái thành công!' : 'Có lỗi xảy ra',
            'staff_id' => $staff_id ?? null
        ]);
    }

    /**
     * Supplement candidate info (from checklist)
     */
    function supplementInfo()
    {
        if (!$this->input->post()) {
            echo json_encode(['result' => 0, 'message' => 'Invalid request']);
            return;
        }

        $checklist_id = $this->input->post('checklist_id');
        $checklist = $this->checklist_profile_model->getById($checklist_id);

        if (!$checklist) {
            echo json_encode(['result' => 0, 'message' => 'Checklist không tồn tại']);
            return;
        }

        // Get eprofile ID from offer
        $offer = $this->propose_offer_model->getProposeOfferById($checklist->offer_id);
        if (!$offer || !$offer->kqpv_id) {
            echo json_encode(['result' => 0, 'message' => 'Không tìm thấy hồ sơ ứng viên']);
            return;
        }

        // Update eprofile data
        $updateData = [
            // Thông tin cơ bản
            'full_name' => $this->input->post('full_name'),
            'date_of_birth' => $this->input->post('date_of_birth'),
            'phone_number' => $this->input->post('phone_number'),
            'email' => $this->input->post('email'),
            'gender' => $this->input->post('gender') ?: 'male',
            'marital_status' => $this->input->post('marital_status') ?: null,

            // Địa chỉ
            'current_address' => $this->input->post('current_address') ?: null,
            'permanent_address' => $this->input->post('permanent_address') ?: null,

            // Giấy tờ
            'id_card' => $this->input->post('id_card'),
            'date_of_issue' => $this->input->post('date_of_issue') ?: null,

            // Trình độ
            'educational' => $this->input->post('educational') ?: null,
            'training_school' => $this->input->post('training_school') ?: null,
            'academic_ranking' => $this->input->post('academic_ranking') ?: null,

            // Kinh nghiệm
            'years_of_experience' => $this->input->post('years_of_experience') ?: 0,
            'the_company_did' => $this->input->post('the_company_did') ?: null,
            'job_title' => $this->input->post('job_title') ?: null,
            'achievements' => $this->input->post('achievements') ?: null,
            'info_other' => $this->input->post('info_other') ?: null,
        ];

        $this->db->where('id', $offer->kqpv_id);
        $success = $this->db->update('tbl_hr_eprofile', $updateData);

        echo json_encode([
            'result' => $success ? 1 : 0,
            'message' => $success ? 'Cập nhật thông tin thành công!' : 'Có lỗi xảy ra'
        ]);
    }

    /**
     * Get candidate data for AJAX reload
     */
    function getCandidateData($checklist_id)
    {
        $checklist = $this->checklist_profile_model->getById($checklist_id);

        if (!$checklist) {
            echo json_encode(['result' => 0, 'message' => 'Checklist không tồn tại']);
            return;
        }

        $offer = $this->propose_offer_model->getProposeOfferById($checklist->offer_id);

        if (!$offer || !$offer->kqpv_id) {
            echo json_encode(['result' => 0, 'message' => 'Không tìm thấy hồ sơ ứng viên']);
            return;
        }

        $this->db->where('id', $offer->kqpv_id);
        $candidate_data = $this->db->get('tbl_hr_eprofile')->row_array();

        echo json_encode([
            'result' => 1,
            'data' => $candidate_data
        ]);
    }

    /**
     * Create staff member from checklist data
     */
    private function createStaffFromChecklist($checklist_id, $employee_id)
    {
        $checklist = $this->checklist_profile_model->getById($checklist_id);
        if (!$checklist) {
            return false;
        }

        // Lấy thông tin offer và candidate
        $offer = $this->propose_offer_model->getProposeOfferById($checklist->offer_id);
        if (!$offer || !$offer->kqpv_id) {
            return false;
        }

        $this->db->where('id', $offer->kqpv_id);
        $candidate = $this->db->get('tbl_hr_eprofile')->row_array();
        if (!$candidate) {
            return false;
        }

        // Chuẩn bị dữ liệu tạo staff
        $this->load->model('staff_model');
        $original_password = '123456'; // Mật khẩu mặc định
        $staff_data = [
            'email' => $candidate['email'],
            'firstname' => $this->getFirstName($candidate['full_name']),
            'lastname' => $this->getLastName($candidate['full_name']),
            'phonenumber' => $candidate['phone_number'],
            'facebook' => '',
            'linkedin' => '',
            'skype' => '',
            'password' => app_hash_password($original_password), // Password mặc định
            'datecreated' => date('Y-m-d H:i:s'),
            'admin' => 0,
            'active' => 1,
            'is_not_staff' => 0,
            'code' => $employee_id, // Mã nhân viên
            'birthday' => $candidate['date_of_birth'],
            'gender' => $candidate['gender'] == 'male' ? 1 : 2,
            'marital_status' => $this->mapMaritalStatus($candidate['marital_status']),
            'cmnd_id_passport' => $candidate['id_card'],
            'date_range' => $candidate['date_of_issue'],
            'status_work' => 0, // 0=thử việc, 1=chính thức, 2=nghỉ việc
            'day_in' => date('Y-m-d'), // Ngày vào làm
            'current_accommodation' => $candidate['current_address'],
            'domicile' => $candidate['permanent_address'],
            // Thêm các trường khác nếu cần
        ];

        // Insert vào bảng staff
        $this->db->insert(db_prefix() . 'staff', $staff_data);
        $staff_id = $this->db->insert_id();

        if ($staff_id) {
            if ($candidate['email']) {
                send_mail_template('staff_created', $candidate['email'], $staff_id, $original_password);
            }
            // Gán department nếu có từ offer
            if (!empty($offer->phong_ban_offer)) {
                $this->db->insert(db_prefix() . 'staff_departments', [
                    'staffid' => $staff_id,
                    'departmentid' => $offer->phong_ban_offer,
                ]);
            }
            $this->db->where('id_requirements', $candidate['id_requirements']);
            $this->db->where('id_step_default', 4);
            $this->db->update('tbl_hr_requirements_step', [
                'status' => 1
            ]);

            // Tạo log
            log_activity('Tạo nhân viên mới từ checklist [ID: ' . $checklist_id . ', Staff ID: ' . $staff_id . ']');
//            $this->create_probationary_assessment($staff_id, [
//                'code' => 'DGTV-' . $employee_id,
//                'level_target' => $candidate['role_level'],
//            ]);

        }

        return $staff_id;
    }
    // function test() {
    //     var_dump(send_mail_template('staff_created', 'hch1560166@gmail.com', 376, '123456'));
    // }
    /**
     * Helper: Get first name from full name
     */
    private function getFirstName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        return end($parts); // Tên thường ở cuối
    }

    /**
     * Helper: Get last name from full name
     */
    private function getLastName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        array_pop($parts); // Bỏ tên
        return implode(' ', $parts); // Họ và tên đệm
    }

    /**
     * Helper: Map marital status
     */
    private function mapMaritalStatus($status)
    {
        $map = [
            'single' => 'single',
            'marriage' => 'married',
            'divorced' => 'divorced',
        ];
        return $map[$status] ?? 'single';
    }

    /**
     * Delete checklist
     */
    function delete($id)
    {
        // Không cho phép xóa nếu đã tạo nhân viên (staff_id đã có)
        $checklist = $this->checklist_profile_model->getById($id);
        if ($checklist && !empty($checklist->staff_id)) {
            echo json_encode([
            'result' => 0,
            'message' => 'Không thể xóa checklist đã tạo nhân viên!'
            ]);
            return;
        }
        $success = $this->checklist_profile_model->delete($id);

        echo json_encode([
            'result' => $success ? 1 : 0,
            'message' => $success ? 'Xóa thành công!' : 'Có lỗi xảy ra'
        ]);
    }


    public function create_probationary_assessment($staff_id = 0, $dataPost = [])
    {
        $code = $dataPost['code'] ?? null;
        $staff_id = $staff_id;
        $level_target = $dataPost['level_target'] ?? 0;
        $level_achieved = $dataPost['level_achieved'] ?? 0;
        $rating_list = 0;
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d', strtotime('+2 months', strtotime($date_start)));
        $date_start = _d($date_start);
        $date_end = _d($date_end);
        if (empty($code)){
            $data['result'] = false;
            $data['message'] = lang('Vui lòng nhập mã phiếu');
            echo json_encode($data);die();
        }

        $this->db->where('code',$code);
        $this->db->from('tbl_probationary_assessment');
        $checkExists = $this->db->count_all_results();
        if (!empty($checkExists)){
            $data['result'] = false;
            $data['message'] = lang('Mã phiếu đã tồn tại!');
            echo json_encode($data);die();
        }
        $gate = $dataPost['gate'] ?? [];
        $point_b = 0;
        $point_c = 0;
        $point_d = 0;

        $arrItems = [];
        if (!empty($gate)){
            foreach ($gate as $k => $v){
                $note = $dataPost['note_a'][$k] ?? null;
                $arrItems[] = [
                    'type_check_list' => 'A',
                    'check_list_id' => $k,
                    'gate' => $v,
                    'note' => $note,
                ];
            }
        }
        $percent_b = $dataPost['percent_b'] ?? [];
        if (!empty($percent_b)){
            foreach ($percent_b as $k => $v){
                $point = !empty($dataPost['point_b'][$k]) ? $dataPost['point_b'][$k] : 0;
                $point_b += $point;
                $arrItems[] = [
                    'type_check_list' => 'B',
                    'check_list_id' => $k,
                    'percent' => ($v),
                    'point' => $point,
                ];
            }
        }

        $percent_c = $dataPost['percent_c'] ?? [];
        if (!empty($percent_c)){
            foreach ($percent_c as $k => $v){
                $point = !empty($dataPost['point_c'][$k]) ? $dataPost['point_c'][$k] : 0;
                $point_c += $point;
                $arrItems[] = [
                    'type_check_list' => 'C',
                    'check_list_id' => $k,
                    'percent' => ($v),
                    'point' => $point,
                ];
            }
        }

        $point_d_post = $dataPost['point_d'] ?? [];
        if (!empty($point_d_post)){
            foreach ($point_d_post as $k => $v){
                $point_d += !empty($v) ? $v : 0;
                $arrItems[] = [
                    'type_check_list' => 'D',
                    'check_list_id' => $k,
                    'point' => ($v),
                ];
            }
        }
        $point = $point_b + $point_c + $point_d;
        $dtRating = get_table_where('tbl_result_checklist',['id' => $rating_list],'','row_array');

        $option = [
            'date' => date('Y-m-d H:i:s'),
            'code' => $code,
            'staff_id' => $staff_id,
            'role_id' => $dtStaff['role'] ?? 0,
            'note' => null,
            'level_target' => $level_target,
            'level_achieved' => $level_achieved,
            'date_start' => to_sql_date($date_start),
            'date_end' => to_sql_date($date_end),
            'point_b' => $point_b,
            'point_c' => $point_c,
            'point_d' => $point_d,
            'point' => $point,
            'rating_list' => $rating_list,
            'rating' => $dtRating['name'] ?? null,
            'point_start' => $dtRating['point_start'] ?? 0,
            'point_end' => $dtRating['point_end'] ?? 0,
            'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
            'date_created' => date('Y-m-d H:i:s'),
            'created_by' => get_staff_user_id()
        ];
        $this->db->insert('tbl_probationary_assessment',$option);
        $insert_id = $this->db->insert_id();
        if ($insert_id){
            if (!empty($arrItems)){
                foreach ($arrItems as $k => $v){
                    $v['probationary_assessment_id'] = $insert_id;
                    $this->db->insert('tbl_probationary_assessment_item',$v);
                }
            }
            return true;
        }
        return false;

    }
}
