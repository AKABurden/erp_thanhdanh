<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Propose_offer extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('propose_offer_model');
    }

    /**
     * Main list view
     */
    function index()
    {
        $data = [];
        $data['title'] = 'Quản Lý Offer';
        $data['type'] = 1;
        $this->load->view('admin/propose_offer/manage', $data);
    }

    /**
     * Get YCTD List for Select2 AJAX
     */
    public function getYCTDList()
    {
        $search = $this->input->get('q');
        $page = $this->input->get('page') ?: 1;
        $limit = 30;
        $offset = ($page - 1) * $limit;

        // Query tbl_hr_requirements
        $this->db->select('tbl_hr_requirements.id, tbl_hr_requirements.code, tbl_hr_requirements.name, tbl_hr_requirements.budget_start, tbl_hr_requirements.budget_end,tblroles.name as role_name,tblroles.code_role as role_code, tbl_room.name as room_name,tbl_hr_requirements.role_id,tbl_role_level.name as role_level,tbl_role_level.id as role_level_id');
        $this->db->from('tbl_hr_requirements');
        $this->db->where('tbl_hr_requirements.status', 'approved'); // Only approved YCTD

        if ($search) {
            $this->db->group_start();
            $this->db->like('code', $search);
            $this->db->or_like('name', $search);
            $this->db->group_end();
        }
        $this->db->join('tbl_job_detail', 'tbl_job_detail.id = tbl_hr_requirements.id_jd', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tbl_hr_requirements.role_id', 'left');
        $this->db->join('tbl_role_level', 'tbl_role_level.id = tbl_hr_requirements.role_level', 'left');
        $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        // Get total count
        $total_count = $this->db->count_all_results('', false);


        // $this->db->select('tbl_job_detail.*, tblroles.name as role_name');
        //         $this->db->from('tbl_job_detail');
        //         $this->db->join('tblroles','tblroles.roleid = tbl_job_detail.role_id','left');
        //         $this->db->where('tbl_job_detail.id', $data['requirement']['id_jd']);
        // Get paginated results
        $this->db->limit($limit, $offset);
        $this->db->order_by('tbl_hr_requirements.id', 'DESC');
        $requirements = $this->db->get()->result_array();

        // Format for Select2
        $items = [];
        foreach ($requirements as $req) {
            $items[] = [
                'id' => $req['id'],
                'text' => $req['code'] . ' - ' . ($req['name'] ?? '') . ' (' . number_format($req['budget_start']) . ' - ' . number_format($req['budget_end']) . ')',
                'yctd_data' => [
                    'id' => $req['id'],
                    'code' => $req['code'],
                    'ma_yctd' => $req['code'],
                    'budget_start' => $req['budget_start'],
                    'budget_end' => $req['budget_end'],
                    'role_id' => $req['role_id'],
                    'role_name' => $req['role_name'] . ' (' . $req['role_code'] . ')',
                    'role_level_id' => $req['role_level_id'],
                    'role_level' => $req['role_level'],
                    'room_name' => $req['room_name']
                ]
            ];
        }

        echo json_encode([
            'items' => $items,
            'total_count' => $total_count
        ]);
    }

    /**
     * DataTables server-side data endpoint
     */
    function getProposeOffer()
    {
        $aColumns = [
            'tbl_propose_offer.id as id',
            'tbl_propose_offer.ma_offer as ma_offer',
            'tbl_hr_eprofile.full_name as ten_ung_vien',
            'tbl_propose_offer.vi_tri_offer as vi_tri_offer',
            'tbl_propose_offer.phong_ban_offer as phong_ban_offer',
            'tbl_propose_offer.ngay_tao as ngay_tao',
            'tbl_propose_offer.luong_p1 as luong_p1',
            'tbl_propose_offer.luong_p2 as luong_p2',
            'tbl_propose_offer.luong_p3 as luong_p3',
            'tbl_propose_offer.phu_cap as cp_luong_p3',
            'tbl_propose_offer.trang_thai as trang_thai',
            'tbl_propose_offer.id as actions'
        ];

        $sIndexColumn = 'id';
        $sTable = 'tbl_propose_offer';
        $where = [];
        $filter = [];

        // Get filters from request
        $search_filter = $this->input->post('search_filter');
        $status_filter = $this->input->post('status_filter');
        $department_filter = $this->input->post('department_filter');
        $type_search = $this->input->post('type_search');

        // Build where clause for filters
        if (!empty($search_filter)) {
            $filter[] = 'AND (tbl_propose_offer.ma_offer LIKE "%' . $search_filter . '%" OR tbl_propose_offer.ten_ung_vien LIKE "%' . $search_filter . '%")';
        }
        if (!empty($status_filter)) {
            $filter[] = 'AND tbl_propose_offer.trang_thai = "' . $status_filter . '"';
        }
        if (!empty($department_filter)) {
            $filter[] = 'AND tbl_propose_offer.phong_ban_offer = "' . $department_filter . '"';
        }

        $join = [
            'LEFT JOIN tbl_hr_eprofile ON tbl_hr_eprofile.id=tbl_propose_offer.kqpv_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $filter, '', []);
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];
            foreach ($aColumns as $v) {
                // $row[$v] = $aRow[$v];
                $_data = $aRow[$v];
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    /**
     * Create/Edit form modal
     */
    function handling($id = '', $id_eprofile = '0')
    {
        $data = [];
        $offer = $id ? $this->propose_offer_model->getProposeOfferById($id) : null;

        if ($this->input->post()) {
            // Validation rules
            $this->form_validation->set_rules('ten_ung_vien', 'Tên ứng viên', 'required');
            $this->form_validation->set_rules('luong_p1', 'Lương P1', 'required');

            if ($this->form_validation->run() == true) {
                $ma_yctd = get_table_where('tbl_hr_requirements', ['id' => $this->input->post('ma_yctd')], '', 'row_array')['code'] ?? '';
                $postData = [
                    'id_yctd' => $this->input->post('ma_yctd'),
                    'ma_yctd' => $ma_yctd,
                    'ten_ung_vien' => $this->input->post('ten_ung_vien'),
                    'kqpv_id' => $this->input->post('kqpv_id'),
                    'evaluation_employee_id' => $this->input->post('evaluation_employee_id'),
                    'vi_tri_offer' => $this->input->post('vi_tri_offer'),
                    'phong_ban_offer' => $this->input->post('phong_ban_offer'),
                    'ngay_bat_dau_du_kien' => $this->input->post('ngay_bat_dau_du_kien'),
                    'luong_p1' => number_unformat($this->input->post('luong_p1')),
                    'luong_p2' => number_unformat($this->input->post('luong_p2')),
                    'luong_p3' => number_unformat($this->input->post('luong_p3')),
                    'phu_cap' => number_unformat($this->input->post('phu_cap')),
                    'thoi_han_offer' => $this->input->post('thoi_han_offer') ?: '7 Ngày',
                    'trang_thai' => $this->input->post('trang_thai') ?: 'DRAFT'
                ];

                if ($id) {
                    // Update existing offer
                    $postData['staff_update'] = get_staff_user_id();
                    $postData['date_update'] = date('Y-m-d H:i:s');
                    $success = $this->propose_offer_model->updateProposeOffer($id, $postData);
                    $message = $success ? 'Cập nhật Offer thành công!' : 'Có lỗi xảy ra';
                    $offer_id = $id;
                } else {
                    // Generate unique offer code
                    $postData['ma_offer'] = 'OFR' . date('ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                    $postData['staff_create'] = get_staff_user_id();
                    $postData['date_create'] = date('Y-m-d H:i:s');
                    $postData['nguoi_tao'] = get_staff_full_name(get_staff_user_id());
                    $postData['ngay_tao'] = date('Y-m-d H:i:s');

                    $offer_id = $this->propose_offer_model->insertProposeOffer($postData);
                    $success = $offer_id > 0;
                    $message = $success ? 'Tạo Offer thành công!' : 'Có lỗi xảy ra';
                }

                if ($success) {
                    $data['result'] = 1;
                    $data['message'] = $message;
                    $data['id'] = $offer_id;
                } else {
                    $data['result'] = 0;
                    $data['message'] = $message;
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }

            echo json_encode($data);
            return;
        }
        $kpv_id = $this->input->get('kpv_id');
        $data['candidate'] = '';
        if ($kpv_id) {
            $data['candidate'] = get_table_where('tbl_hr_eprofile', ['id' => $kpv_id], '', 'row');
        }
        // echo '<pre>';print_arrays($data['candidate']);die;
        // Load form view
        if ($offer) {
            $data['offer'] = $offer;
        }
        $data['id'] = $id;
        $data['title'] = $id ? 'Chỉnh sửa Offer' : 'Tạo Offer mới';
        // $data['departments'] = $this->propose_offer_model->getDepartments();
        $data['staff_create'] = $offer ? get_staff_full_name($offer->staff_create) : get_staff_full_name();
        $data['staff_roles_create'] = $offer ? $this->propose_offer_model->getStaffRoles($offer->staff_create) : $this->propose_offer_model->getStaffRoles(get_staff_user_id());
        // echo '<pre>';print_arrays($data);die;
        $this->load->view('admin/propose_offer/handling', $data);
    }

    /**
     * Preview offer letter
     */
    function preview($id)
    {
        $data['offer'] = $this->propose_offer_model->getProposeOfferById($id);

        if (!$data['offer']) {
            show_404();
            return;
        }
        $offer = $data['offer'];
        $data['content'] = $this->replace_pdf($offer);
        $this->load->view('admin/propose_offer/preview', $data);
    }
    public function replace_pdf($offer)
    {
        $content = get_table_where('tbl_template_propose_offer', ['id' => 1], '', 'row')->content ?? '';

        // Replace placeholders with actual data
        $content = str_replace('{offer_ma_offer}', $offer->ma_offer, $content);
        $content = str_replace('{today_dmy}', date('d/m/Y'), $content);
        $content = str_replace('{invoice_company_name}', get_option('invoice_company_name'), $content);
        $content = str_replace('{invoice_company_address}', get_option('invoice_company_address'), $content);
        $content = str_replace('{invoice_company_phonenumber}', get_option('invoice_company_phonenumber'), $content);
        $content = str_replace('{email_company}', get_option('smtp_email'), $content);
        $content = str_replace('{offer_ten_ung_vien}', $offer->ten_ung_vien, $content);
        $content = str_replace('{offer_vi_tri_offer}', $offer->vi_tri_offer, $content);
        $content = str_replace('{offer_phong_ban_offer}', $offer->phong_ban_offer, $content);

        // Format dates
        $ngay_bat_dau_dmy = $offer->ngay_bat_dau_du_kien ? date('d/m/Y', strtotime($offer->ngay_bat_dau_du_kien)) : '';
        $content = str_replace('{offer_ngay_bat_dau_du_kien_dmy}', $ngay_bat_dau_dmy, $content);

        // Format salary values
        $content = str_replace('{offer_luong_p1_vnd}', number_format($offer->luong_p1, 0, ',', '.'), $content);
        $content = str_replace('{offer_luong_p2_vnd}', number_format($offer->luong_p2, 0, ',', '.'), $content);
        $content = str_replace('{offer_total_luong_p1_p2_vnd}', number_format($offer->luong_p1 + $offer->luong_p2, 0, ',', '.'), $content);

        // Handle conditional section for phu_cap
        if (!empty($offer->phu_cap) && $offer->phu_cap > 0) {
            $phu_cap_section = '<li>
    <strong>Phụ cấp khác:</strong> ' . number_format($offer->phu_cap, 0, ',', '.') . ' VNĐ / tháng.
    <em>(Bao gồm: ăn trưa, đi lại, điện thoại...)</em>
  </li>';
            $content = str_replace('{if_phu_cap}', '', $content);
            $content = str_replace('{/if_phu_cap}', '', $content);
            $content = str_replace('{offer_phu_cap_vnd}', number_format($offer->phu_cap, 0, ',', '.'), $content);
        } else {
            // Remove the conditional section if no phu_cap
            $content = preg_replace('/\{if_phu_cap\}.*?\{\/if_phu_cap\}/s', '', $content);
        }

        $content = str_replace('{offer_thoi_han_offer}', $offer->thoi_han_offer, $content);

        // Company representative info (can be configured in settings or use default)
        $content = str_replace('{company_representative_name}', get_option('company_representative_name') ?: 'GIÁM ĐỐC', $content);
        $content = str_replace('{company_representative_title}', get_option('company_representative_title') ?: 'Giám đốc Công ty', $content);
        return $content;
    }


    /**
     * Print offer as HTML page
     */
    function in_pdf($id)
    {
        $offer = $this->propose_offer_model->getProposeOfferById($id);

        if (!$offer) {
            show_404();
            return;
        }

        $data['offer'] = $offer;
        $data['content'] = $this->replace_pdf($offer);
        $this->load->view('admin/propose_offer/pdf', $data);
    }

    /**
     * Get offer content as JSON for printing
     */
    function get_content($id)
    {
        $offer = $this->propose_offer_model->getProposeOfferById($id);

        if (!$offer) {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy Offer'
            ]);
            return;
        }

        $content = $this->replace_pdf($offer);

        echo json_encode([
            'success' => true,
            'content' => $content
        ]);
    }

    /**
     * Send email to candidate
     */
    function send_email($id)
    {
        $offer = $this->propose_offer_model->getProposeOfferById($id);

        if (!$offer) {
            echo json_encode([
                'result' => 0,
                'message' => 'Không tìm thấy Offer'
            ]);
            return;
        }
        $tbl_hr_eprofile = $this->db->get_where('tbl_hr_eprofile', ['id' => $offer->kqpv_id])->row();
        if (!$tbl_hr_eprofile || empty($tbl_hr_eprofile->email)) {
            echo json_encode([
                'result' => 0,
                'message' => 'Ứng viên không có email hợp lệ'
            ]);
            return;
        }
        $content = $this->replace_pdf($offer);
        $this->load->config('email');
        $template = new StdClass();
        $template->message = $content;
        $template->fromname = get_option('companyname') != '' ? get_option('companyname') : '';
        $template->subject = 'Thư mời nhận việc - ' . $offer->ma_offer;
        $this->email->initialize();
        $this->email->set_newline(config_item('newline'));
        $this->email->set_crlf(config_item('crlf'));
        $this->email->from(get_option('smtp_email'), $template->fromname);
        $this->email->to($tbl_hr_eprofile->email);

        $this->email->subject($template->subject);
        $this->email->message($template->message);
        $this->email->send(true);

        // Update status to DA_GUI (Sent)
        $updated = $this->propose_offer_model->updateProposeOffer($id, [
            'trang_thai' => 'DA_GUI',
            'staff_update' => get_staff_user_id(),
            'date_update' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            echo json_encode([
                'result' => 1,
                'message' => 'Đã gửi email Offer thành công!'
            ]);
        } else {
            echo json_encode([
                'result' => 0,
                'message' => 'Có lỗi khi cập nhật trạng thái'
            ]);
        }
    }

    /**
     * Approve offer
     */
    function approve($id)
    {
        $offer = $this->propose_offer_model->getProposeOfferById($id);

        if (!$offer) {
            echo json_encode([
                'result' => 0,
                'message' => 'Không tìm thấy Offer'
            ]);
            return;
        }

        // Check if already approved
        if ($offer->trang_thai == 'DA_DUYET') {
            echo json_encode([
                'result' => 0,
                'message' => 'Offer này đã được duyệt trước đó'
            ]);
            return;
        }

        $updated = $this->propose_offer_model->updateProposeOffer($id, [
            'trang_thai' => 'DA_DUYET',
            'staff_approve' => get_staff_user_id(),
            'date_approve' => date('Y-m-d H:i:s'),
            'staff_update' => get_staff_user_id(),
            'date_update' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            echo json_encode([
                'result' => 1,
                'message' => 'Duyệt Offer thành công! Bây giờ bạn có thể gửi email cho ứng viên.'
            ]);
        } else {
            echo json_encode([
                'result' => 0,
                'message' => 'Có lỗi khi duyệt Offer'
            ]);
        }
    }

    /**
     * Delete offer
     */
    function delete($id)
    {
        // Không cho phép xóa nếu offer đã gửi mail
        $offer = $this->propose_offer_model->getProposeOfferById($id);
        // if ($offer && $offer->trang_thai == 'DA_GUI') {
        //     echo json_encode([
        //     'result' => 0,
        //     'message' => 'Không thể xóa Offer đã gửi email!'
        //     ]);
        //     return;
        // }
        // Không cho phép xóa nếu offer đã được check_list r
        $checkList = $this->db->get_where('tbl_checklist_profile', ['offer_id' => $id])->row();
        if ($checkList) {
            echo json_encode([
                'result' => 0,
                'message' => 'Không thể xóa Offer đã được checklist!'
            ]);
            return;
        }
        $success = $this->propose_offer_model->deleteProposeOffer($id);

        echo json_encode([
            'result' => $success ? 1 : 0,
            'message' => $success ? 'Xóa Offer thành công!' : 'Có lỗi xảy ra'
        ]);
    }

    /**
     * Update offer status
     */
    function update_status($id)
    {
        $status = $this->input->post('status');

        if (!in_array($status, ['DRAFT', 'DANG_CHO_DUYET', 'DA_GUI', 'CHAP_NHAN', 'TU_CHOI'])) {
            echo json_encode([
                'result' => 0,
                'message' => 'Trạng thái không hợp lệ'
            ]);
            return;
        }

        $updated = $this->propose_offer_model->updateProposeOffer($id, [
            'trang_thai' => $status,
            'staff_update' => get_staff_user_id(),
            'date_update' => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'result' => $updated ? 1 : 0,
            'message' => $updated ? 'Cập nhật trạng thái thành công!' : 'Có lỗi xảy ra'
        ]);
    }

    /**
     * Export to Excel
     */
    function export_excel()
    {
        // Get all offers
        $offers = $this->propose_offer_model->getAllProposeOffers();

        // Load PHPExcel or use simple CSV export
        // This is a placeholder - implement based on your export library

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=offers_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, [
            'Mã Offer',
            'Mã YCTD',
            'Tên ứng viên',
            'Vị trí',
            'Phòng ban',
            'Ngày bắt đầu',
            'Lương P1',
            'Lương P2',
            'Phụ cấp',
            'Tổng thu nhập',
            'Thời hạn',
            'Trạng thái',
            'Người tạo',
            'Ngày tạo'
        ]);

        // Data rows
        foreach ($offers as $offer) {
            fputcsv($output, [
                $offer->ma_offer,
                $offer->ma_yctd,
                $offer->ten_ung_vien,
                $offer->vi_tri_offer,
                $offer->phong_ban_offer,
                $offer->ngay_bat_dau_du_kien,
                number_format($offer->luong_p1, 0, ',', '.'),
                number_format($offer->luong_p2, 0, ',', '.'),
                number_format($offer->phu_cap, 0, ',', '.'),
                number_format($offer->luong_p1 + $offer->luong_p2, 0, ',', '.'),
                $offer->thoi_han_offer,
                $offer->trang_thai,
                $offer->nguoi_tao,
                $offer->ngay_tao
            ]);
        }

        fclose($output);
        exit;
    }
    public function getCandidatesByYctd()
    {
        $id = $this->input->get('ma_yctd');
        $kqpv_id = $this->input->get('kqpv_id');

        if (empty($id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Mã YCTD không hợp lệ',
                'data' => []
            ]);
            return;
        }

        // Lấy thông tin YCTD để lấy id_requirements
        $this->db->select('id');
        $this->db->from('tbl_hr_requirements');

        $this->db->where('id', $id);
        $requirement = $this->db->get()->row();

        if (!$requirement) {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy YCTD',
                'data' => []
            ]);
            return;
        }

        // Lấy danh sách ứng viên đã được đánh giá (có trong tbl_evaluation_employee với type = 2)
        $this->db->select('
            tbl_hr_eprofile.id, 
            tbl_hr_eprofile.full_name, 
            tbl_hr_eprofile.email, 
            tbl_hr_eprofile.phone_number, 
            tbl_hr_eprofile.expected_salary, 
            tbl_hr_eprofile.avatar, 
            tbl_hr_eprofile.role_level, 
            tbl_hr_eprofile.years_of_experience,
            tbl_evaluation_employee.id as evaluation_id,
            tbl_evaluation_employee.point,
            tbl_evaluation_employee.rating,
            tbl_evaluation_employee.warning
        ');
        $this->db->from('tbl_hr_eprofile');
        $this->db->join('tbl_evaluation_employee', 'tbl_evaluation_employee.staff_id = tbl_hr_eprofile.id AND tbl_evaluation_employee.type = 2', 'inner');
        $this->db->where('tbl_hr_eprofile.id_requirements', $requirement->id);
        if (!empty($kqpv_id)) {
            $this->db->where('tbl_hr_eprofile.id', $kqpv_id);
        } else {
            // Loại bỏ các ứng viên đã có offer cho YCTD này
            $this->db->where('tbl_hr_eprofile.id NOT IN (SELECT kqpv_id FROM tbl_propose_offer)', null, false);
        }
        $this->db->order_by('tbl_evaluation_employee.point', 'DESC'); // Sắp xếp theo điểm cao nhất
        $candidates = $this->db->get()->result_array();
        // echo '<pre>';
        // print_arrays($this->db->last_query());
        // die;
        // Format dữ liệu để trả về
        $items = [];
        foreach ($candidates as $candidate) {
            $items[] = [
                'id' => $candidate['id'],
                'text' => $candidate['full_name'] . ' - ' . $candidate['email'] . ' (Điểm: ' . $candidate['point'] . ')',
                'evaluation_id' => $candidate['evaluation_id'],
                'candidate_data' => [
                    'full_name' => $candidate['full_name'],
                    'email' => $candidate['email'],
                    'phone_number' => $candidate['phone_number'],
                    'expected_salary' => $candidate['expected_salary'],
                    'avatar' => $candidate['avatar'],
                    'role_level' => $candidate['role_level'],
                    'years_of_experience' => $candidate['years_of_experience'],
                    'point' => $candidate['point'],
                    'rating' => $candidate['rating'],
                    'warning' => $candidate['warning']
                ]
            ];
        }

        echo json_encode([
            'success' => true,
            'message' => 'Lấy danh sách ứng viên thành công',
            'data' => $items,
            'total' => count($items)
        ]);
    }
    public function getSalaryByRole()
    {
        $role_id = $this->input->get('role_id');
        $role_level_id = $this->input->get('role_level_id');
        $seniority_months = (int) $this->input->get('seniority_months') ?: 0;

        if (!$role_id) {
            echo json_encode(['success' => false, 'message' => '']);
            return;
        }

        $this->db->select('tbl_salary_3p.*, tbl_grade.code as grade_code, tbl_grade.seniority_from_month, tbl_grade.seniority_to_month');
        $this->db->from('tbl_salary_3p');
        $this->db->join('tbl_grade', 'tbl_grade.id = tbl_salary_3p.grade_id', 'inner');
        $this->db->where('tbl_salary_3p.role_id', $role_id);
        $this->db->where('tbl_salary_3p.role_level_id', $role_level_id);
        $this->db->where('tbl_salary_3p.status', 1);
        $this->db->where('tbl_salary_3p.effective_from <=', date('Y-m-d'));
        $this->db->group_start();
        $this->db->where('tbl_salary_3p.effective_to >=', date('Y-m-d'));
        $this->db->or_where('tbl_salary_3p.effective_to IS NULL');
        $this->db->group_end();
        $this->db->where('tbl_grade.seniority_from_month <=', $seniority_months);
        $this->db->where('tbl_grade.seniority_to_month >=', $seniority_months);
        $this->db->order_by('tbl_salary_3p.version', 'DESC');
        $this->db->limit(1);

        $salary = $this->db->get()->row();

        if ($salary) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'salary_p1' => (float) $salary->salary_p1,
                    'salary_p2' => (float) $salary->salary_p2,
                    'salary_p3' => (float) $salary->salary_p3,
                    'phu_cap' => (float) $salary->allowed_p3,
                    'p2_min' => (float) $salary->salary_p2 * 0.8,
                    'p2_max' => (float) $salary->salary_p2 * 1.2,
                    'grade_code' => $salary->grade_code
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy khung lương cho vị trí này, Vui lòng cập nhật để tạo phiếu']);
        }
    }
    function getYCTDById()
    {
        $id = $this->input->get('id');
        if (empty($id)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID không hợp lệ',
                'data' => []
            ]);
            return;
        }

        // $requirement = $this->propose_offer_model->getYCTDById($id);
        $this->db->select('tbl_hr_requirements.id, tbl_hr_requirements.code, tbl_hr_requirements.name, tbl_hr_requirements.budget_start, tbl_hr_requirements.budget_end,tblroles.name as role_name, tbl_room.name as room_name,tbl_hr_requirements.role_id,tbl_role_level.name as role_level,tbl_role_level.id as role_level_id');
        $this->db->from('tbl_hr_requirements');
        $this->db->join('tbl_job_detail', 'tbl_job_detail.id = tbl_hr_requirements.id_jd', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tbl_hr_requirements.role_id', 'left');
        $this->db->join('tbl_role_level', 'tbl_role_level.id = tbl_hr_requirements.role_level', 'left');
        $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        // Get total count
        $this->db->where('tbl_hr_requirements.id', $id); // Only 


        $this->db->order_by('tbl_hr_requirements.id', 'DESC');
        $requirements = $this->db->get()->result_array();

        // Format for Select2
        $items = [];
        foreach ($requirements as $req) {
            $items = [
                'id' => $req['id'],
                'text' => $req['code'] . ' - ' . ($req['name'] ?? '') . ' (' . number_format($req['budget_start']) . ' - ' . number_format($req['budget_end']) . ')',
                'yctd_data' => [
                    'id' => $req['id'],
                    'code' => $req['code'],
                    'ma_yctd' => $req['code'],
                    'budget_start' => $req['budget_start'],
                    'budget_end' => $req['budget_end'],
                    'role_id' => $req['role_id'],
                    'role_name' => $req['role_name'] . ' (' . $req['role_code'] . ')',
                    'role_level_id' => $req['role_level_id'],
                    'role_level' => $req['role_level'],
                    'room_name' => $req['room_name']
                ]
            ];
        }

        if ($items) {
            echo json_encode([
                'success' => true,
                'data' => $items
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy YCTD',
                'data' => []
            ]);
        }
    }
}
