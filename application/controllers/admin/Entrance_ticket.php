<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Entrance_ticket - Phiếu mang hàng ra/vào cổng (FOSO)
 *
 * Luồng trạng thái chuẩn:
 *  0 = Chờ duyệt       (Draft / Pending)
 *  1 = QA duyệt đi     (QA đã duyệt cho phép mang hàng ra)
 *  2 = BV xác nhận ra  (Bảo vệ xác nhận hàng/người đã ra khỏi cổng)
 *  3 = BV xác nhận về  (Bảo vệ xác nhận hàng/người đã về)
 *  4 = QA hoàn tất     (QA đóng phiếu - hoàn thành)
 */
class Entrance_ticket extends AdminController
{
    // Tên bảng chính
    const TABLE_MAIN  = 'tbl_entrance_ticket';
    const TABLE_ITEMS = 'tbl_entrance_ticket_items';
    const REF_PREFIX          = 'entrance_ticket';
    const TABLE_LOCATIONS      = 'tbl_entrance_ticket_locations';
    const TABLE_LOCATION_ROLES = 'tbl_entrance_ticket_location_roles';

    // Nhãn trạng thái
    const STATUS_LABELS = [
        -1 => ['label' => 'Từ chối',      'class' => 'label-danger'],
        0  => ['label' => 'Chờ duyệt',    'class' => 'label-default'],
        1  => ['label' => 'QA duyệt đi',  'class' => 'label-info'],
        2  => ['label' => 'BV xác nhận ra','class' => 'label-warning'],
        3  => ['label' => 'BV xác nhận về','class' => 'label-primary'],
        4  => ['label' => 'Hoàn tất',     'class' => 'label-success'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('manufactures_model');
        $this->load->model('departments_model');
        $this->load->model('Entrance_ticket_mail_model');

        // Quyền – đặt true tạm, có thể bind với has_permission() sau
        $this->preView           = true;
        $this->preViewOwn        = true;
        $this->preAdd            = true;
        $this->preEdit           = true;
        $this->preDelete         = true;
        $this->preApproveQA      = true; // QA duyệt / hoàn tất
        $this->preApproveSecurity = true; // Bảo vệ xác nhận ra / về
    }

    /* =====================================================================
     * INDEX – Danh sách phiếu
     * =================================================================== */
    public function index()
    {
        if (!$this->preView && !$this->preViewOwn) {
            access_denied();
        }
        $data['title'] = lang('Phiếu mang hàng ra/vào cổng');
        $data['locations'] = $this->db->get(self::TABLE_LOCATIONS)->result_array();
        $this->load->view('admin/entrance_ticket/index', $data);
    }

    /* =====================================================================
     * DETAIL – Thêm mới / Chỉnh sửa phiếu
     * =================================================================== */
    public function detail($id = 0)
    {
        $data    = [];
        $dtData  = [];
        $dtItems = [];

        if (!empty($id)) {
            $this->db->select('t.*, 
                CONCAT(s.firstname," ",s.lastname) as fullname,
                s.code as staff_code,
                r.name as name_roles,
                (SELECT GROUP_CONCAT(d.name SEPARATOR ", ") 
                 FROM tblstaff_departments sd 
                 JOIN tbldepartments d ON d.departmentid = sd.departmentid 
                 WHERE sd.staffid = t.id_staff) as name_departments,
                s.phone as staff_phone
            ')
            ->from(self::TABLE_MAIN . ' t')
            ->join('tblstaff s',      's.staffid = t.id_staff',         'LEFT')
            ->join('tblroles r',      'r.roleid = s.role',              'LEFT')
            ->where('t.id', $id);
            $dtData = $this->db->get()->row_array();

            if (empty($dtData)) {
                refererModel(lang('Không tìm thấy phiếu!'));
            }
            // The instruction removes the item handling, so dtItems will no longer be populated from the database.
            // $this->db->from(self::TABLE_ITEMS)->where('entrance_ticket_id', $id);
            // $dtItems = $this->db->get()->result_array();
        }

        if ($this->input->post()) {
            // ---------- Validate ----------
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang('Mã Phiếu'), 'trim|required|is_unique[' . self::TABLE_MAIN . '.reference_no]');
            } else {
                if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang('Mã Phiếu'), 'trim|required|is_unique[' . self::TABLE_MAIN . '.reference_no]');
                }
            }
            $this->form_validation->set_rules('date',          lang('date'),      'required');
            $this->form_validation->set_rules('id_staff',      lang('Nhân viên'), 'required');
            $this->form_validation->set_rules('executor_name', 'Người thực hiện', 'required');
            $this->form_validation->set_rules('item_type',     'Phân loại hàng', 'required');
            $this->form_validation->set_rules('item_code_name','Mã/Tên hàng',    'required');

            if ($this->form_validation->run() == true) {
                // ---------- Gom items ---------- (Removed as per instruction)
                // $counter = $this->input->post('counter');
                // $items   = [];
                // if (!empty($counter)) {
                //     foreach ($counter as $idx) {
                //         $items[] = [
                //             'detail_reference_no' => $this->input->post('detail_reference_no')[$idx] ?? '',
                //             'detail_items'        => $this->input->post('detail_items')[$idx] ?? '',
                //             'detail_qty'          => $this->input->post('detail_qty')[$idx] ?? '',
                //             'detail_unit'         => $this->input->post('detail_unit')[$idx] ?? '',
                //             'detail_note'         => $this->input->post('detail_note')[$idx] ?? '',
                //             'id'                  => $this->input->post('entrance_ticket_items_id')[$idx] ?? null,
                //         ];
                //     }
                // }

                // if (empty($items)) {
                //     echo json_encode(['result' => 0, 'message' => lang('Vui lòng thêm ít nhất 1 mặt hàng!')]);
                //     die();
                // }

                // $reference_no = $this->input->post('reference_no') ?: getReference(self::REF_PREFIX);
                $reference_no = getReference(self::REF_PREFIX);
                
                // Xử lý đối tác: nếu KH hoặc NCC thì lấy tên từ DB, nếu Khác thì lấy text nhập tay
                $partner_type = (int) $this->input->post('partner_type') ?: 3;
                $partner_id   = (int) $this->input->post('partner_id') ?: 0;
                $partner_name = $this->input->post('partner_name');
                if ($partner_type == 1 && $partner_id > 0) {
                    $cl = $this->db->select('company')->where('userid', $partner_id)->get('tblclients')->row_array();
                    if (!empty($cl)) $partner_name = $cl['company'];
                } elseif ($partner_type == 2 && $partner_id > 0) {
                    $sp = $this->db->select('company')->where('id', $partner_id)->get('tblsuppliers')->row_array();
                    if (!empty($sp)) $partner_name = $sp['company'];
                }

                $fields = [
                    'date'                => to_sql_date($this->input->post('date'), true),
                    'id_staff'            => $this->input->post('id_staff'),
                    'priority'            => $this->input->post('priority'),
                    'note_reason'         => $this->input->post('note_reason'),
                    'phone'               => $this->input->post('phone'),
                    'destination'         => $this->input->post('destination'),
                    'type'                => $this->input->post('type') ?: 'out',

                    'partner_type'        => $partner_type,
                    'partner_id'          => $partner_id,
                    'partner_name'        => $partner_name,
                    'executor_name'       => $this->input->post('executor_name'),
                    'executor_phone'      => $this->input->post('executor_phone'),

                    'item_type'           => $this->input->post('item_type'),
                    'item_product_id'     => (int) $this->input->post('item_product_id') ?: 0,
                    'item_code_name'      => $this->input->post('item_code_name'),
                    'quantity'            => $this->input->post('quantity'),
                    'package_count'       => $this->input->post('package_count'),
                    'kg_weight'           => $this->input->post('kg_weight'),

                    'vehicle_type'        => $this->input->post('vehicle_type'),
                    'license_plate'       => $this->input->post('license_plate'),
                    'driver_name'         => $this->input->post('driver_name'),
                    'route'               => $this->input->post('route'),
                    'route_price'         => str_replace(',', '', $this->input->post('route_price')),

                    'planned_date_out'    => to_sql_date($this->input->post('planned_date_out')),
                    'planned_date_return' => to_sql_date($this->input->post('planned_date_return')),

                    'doc_delivery_signed' => $this->input->post('doc_delivery_signed'),
                    'doc_invoice'         => $this->input->post('doc_invoice'),
                    'doc_handover'        => $this->input->post('doc_handover'),
                ];

                if (empty($id)) {
                    // ---------- INSERT ----------
                    $fields['status']       = 0;
                    $fields['staff_create'] = get_staff_user_id();
                    $fields['date_create']  = date('Y-m-d H:i:s');
                    $fields['reference_no'] = $reference_no;

                    $this->db->insert(self::TABLE_MAIN, $fields);
                    $newId = $this->db->insert_id();

                    if ($newId) {
                        if (getReference(self::REF_PREFIX) == $reference_no) {
                            updateReference(self::REF_PREFIX);
                        }
                        // Removed item insertion as per instruction
                        // foreach ($items as $item) {
                        //     unset($item['id']);
                        //     $item['entrance_ticket_id'] = $newId;
                        //     $this->db->insert(self::TABLE_ITEMS, $item);
                        // }
                        insertActivityLog([
                            'type_parent_obj' => self::REF_PREFIX,
                            'table_obj'       => self::TABLE_MAIN,
                            'id_obj'          => $newId,
                            'name_obj'        => $reference_no,
                            'content'         => 'Tạo phiếu mang hàng ra cổng [' . $reference_no . ']',
                            'actions'         => 'add',
                        ]);
                        
                        // Gửi email thông báo tạo phiếu
                        $this->Entrance_ticket_mail_model->send_created_email($newId);

                        echo json_encode(['result' => 1, 'message' => lang('Tạo phiếu thành công!')]);
                    } else {
                        echo json_encode(['result' => 0, 'message' => lang('Tạo phiếu thất bại!')]);
                    }
                } else {
                    // ---------- UPDATE ----------
                    if (!empty($dtData) && $dtData['status'] > 1) {
                        echo json_encode(['result' => 0, 'message' => lang('Phiếu đã được duyệt, không thể chỉnh sửa!')]);
                        die();
                    }
                    $fields['staff_update'] = get_staff_user_id();
                    $fields['date_update']  = date('Y-m-d H:i:s');

                    $this->db->where('id', $id)->update(self::TABLE_MAIN, $fields);

                    // Removed item deletion and re-insertion as per instruction
                    // // Xóa items cũ, thêm mới
                    // $this->db->where('entrance_ticket_id', $id)->delete(self::TABLE_ITEMS);
                    // foreach ($items as $item) {
                    //     unset($item['id']);
                    //     $item['entrance_ticket_id'] = $id;
                    //     $this->db->insert(self::TABLE_ITEMS, $item);
                    // }
                    insertActivityLog([
                        'type_parent_obj' => self::REF_PREFIX,
                        'table_obj'       => self::TABLE_MAIN,
                        'id_obj'          => $id,
                        'name_obj'        => $dtData['reference_no'],
                        'content'         => 'Sửa phiếu mang hàng ra cổng [' . $dtData['reference_no'] . ']',
                        'actions'         => 'edit',
                    ]);

                    // Gửi email thông báo sửa phiếu
                    $this->Entrance_ticket_mail_model->send_edited_email($id);

                    echo json_encode(['result' => 1, 'message' => lang('Cập nhật phiếu thành công!')]);
                }
            } else {
                echo json_encode(['result' => 0, 'message' => validation_errors()]);
            }
            die();
        }

        // ---------- Load view ----------
        if (empty($id)) {
            if (!$this->preAdd) {
                accessDenied(true);
            }
            $data['title'] = lang('Tạo phiếu mang hàng ra cổng');
        } else {
            if (!$this->preEdit) {
                accessDenied(true);
            }
            if (!empty($dtData) && $dtData['status'] > 1) {
                refererModel(lang('Phiếu đã được duyệt, không thể sửa!'));
            }
            $data['title']   = lang('Sửa phiếu mang hàng ra cổng');
            $data['dtData']  = $dtData;
            $data['dtItems'] = $dtItems;
        }

        $data['id']                     = $id;
        $data['reference_no']           = getReference(self::REF_PREFIX);
        $data['employees']              = $this->manufactures_model->getAllStaff();
        $data['dtDepartment']           = $this->db->get('tbldepartments')->result_array();
        $data['transportation_vehicles']= $this->db->order_by('name')->get('tbl_vehicle')->result_array();
        $data['list_vehicles']          = $this->db->order_by('departure_point')->get('tbl_list_vehicle')->result_array();
        $this->load->view('admin/entrance_ticket/detail', $data);
    }

    /* =====================================================================
     * VIEW – Modal xem chi tiết phiếu
     * =================================================================== */
    public function view($id)
    {
        $this->db->select('t.*, 
            CONCAT(s.firstname," ",s.lastname) as fullname,
            s.code as staff_code,
            r.name as name_roles,
            (SELECT GROUP_CONCAT(d.name SEPARATOR ", ") 
             FROM tblstaff_departments sd 
             JOIN tbldepartments d ON d.departmentid = sd.departmentid 
             WHERE sd.staffid = t.id_staff) as name_departments,
            s.phone as staff_phone
        ')
            ->from(self::TABLE_MAIN . ' t')
            ->join('tblstaff s',      's.staffid = t.id_staff',         'LEFT')
            ->join('tblroles r',      'r.roleid = s.role',              'LEFT')
            ->where('t.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->from(self::TABLE_ITEMS)->where('entrance_ticket_id', $id);
        $dtItems = $this->db->get()->result_array();

        $data['dtData']        = $dtData ?: [];
        $data['dtItems']       = $dtItems ?: [];
        $data['title']         = lang('Xem phiếu mang hàng ra cổng');
        $data['status_labels'] = self::STATUS_LABELS;
        $this->load->view('admin/entrance_ticket/view', $data);
    }

    /* =====================================================================
     * GET TABLE – DataTable JSON
     * =================================================================== */
    public function getTable()
    {
        $start_date = $this->input->post('start_date_search');
        $end_date   = $this->input->post('end_date_search');
        $status_filter = $this->input->post('status_filter');
        $location_ids  = $this->input->post('location_ids'); // Array of location IDs
        $id_filter     = $this->input->post('id'); // ID filter for deep linking

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        // QUAN TRỌNG: $aColumns phải có ĐÚNG số phần tử = số <th> trong HTML (13 cột)
        // datatables_helper loop count($aColumns) để map columns[] POST → sai số = Undefined offset
        $aColumns = [
            /* 0  */ 'tbl_entrance_ticket.id as id',
            /* 1  */ 'tbl_entrance_ticket.reference_no as reference_no',
            /* 2  */ 'tbl_entrance_ticket.date as date',
            /* 3  */ 's.code as code_staff',
            /* 4  */ 'CONCAT(s.firstname," ",s.lastname) as fullname',
            /* 5  */ 'r.name as name_roles',
            /* 6  */ '(SELECT GROUP_CONCAT(d.name SEPARATOR ", ") FROM tblstaff_departments sd JOIN tbldepartments d ON d.departmentid = sd.departmentid WHERE sd.staffid = tbl_entrance_ticket.id_staff) as name_departments',
            /* 7  */ 'tbl_entrance_ticket.note_reason as note_reason',
            /* 8  */ 'tbl_entrance_ticket.destination as destination',
            /* 9  */ 'tbl_entrance_ticket.route as route',
            /* 10 */ 'tbl_entrance_ticket.route_price as route_price',
            /* 11 */ 'tbl_entrance_ticket.license_plate as vehicle_no',
            /* 12 */ 'tbl_entrance_ticket.quantity as total_items',
            /* 13 */ 'tbl_entrance_ticket.status as status',
            /* 14 */ 'tbl_entrance_ticket.id as _id_action', 
        ];

        // Các cột phụ cần để render (không search/sort) → dùng additionalSelect
        $additionalSelect = [
            'tbl_entrance_ticket.qa_approve_staff as qa_approve_staff',
            'tbl_entrance_ticket.qa_approve_date as qa_approve_date',
            'tbl_entrance_ticket.bv_out_staff as bv_out_staff',
            'tbl_entrance_ticket.bv_out_date as bv_out_date',
            'tbl_entrance_ticket.bv_in_staff as bv_in_staff',
            'tbl_entrance_ticket.bv_in_date as bv_in_date',
            'tbl_entrance_ticket.qa_done_staff as qa_done_staff',
            'tbl_entrance_ticket.qa_done_date as qa_done_date',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_entrance_ticket';
        $where        = [];

        $join = [
            'LEFT JOIN tblstaff s      ON s.staffid = tbl_entrance_ticket.id_staff',
            'LEFT JOIN tblroles r      ON r.roleid = s.role',
        ];

        if (!$this->preView) {
            array_push($where, 'AND tbl_entrance_ticket.staff_create =', get_staff_user_id());
        }

        if (!empty($start_date)) {
            array_push($where, "AND tbl_entrance_ticket.date >= '" . to_sql_date($start_date) . " 00:00:00'");
        }
        if (!empty($end_date)) {
            array_push($where, "AND tbl_entrance_ticket.date <= '" . to_sql_date($end_date) . " 23:59:59'");
        }
        if ($status_filter !== '' && $status_filter !== null) {
            array_push($where, "AND tbl_entrance_ticket.status = " . (int) $status_filter);
        }
        if (!empty($id_filter)) {
            array_push($where, "AND tbl_entrance_ticket.id = " . (int) $id_filter);
        }

        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $key => $aRow) {
            $row = [];
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/entrance_ticket/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['code_staff'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['fullname'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_roles'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_departments'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['note_reason'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['destination'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['route'] . '</div>';
            $row[] = '<div class="text-right">' . number_format((float)$aRow['route_price'], 0, '.', ',') . '</div>';
            $row[] = '<div class="text-left">' . $aRow['vehicle_no'] . '</div>';
            $row[] = '<div class="text-center">' . $aRow['total_items'] . '</div>';

            // ---- Cột trạng thái + nút Duyệt nhanh ----
            $status      = (int) $aRow['status'];
            $statusInfo  = self::STATUS_LABELS[$status] ?? ['label' => 'N/A', 'class' => 'label-default'];
            
            // Build Quick Action Buttons (Yes/No)
            $quickBtn = '';

            $statusHtml  = '<span class="label ' . $statusInfo['class'] . '">' . $statusInfo['label'] . '</span>';

            // Thêm thông tin người thực hiện bước gần nhất
            $stepInfo = '';
            if ($status >= 1) $stepInfo .= '<div class="text-muted" style="font-size:11px">QA duyệt: ' . get_staff_full_name($aRow['qa_approve_staff']) . '</div>';
            if ($status >= 2) $stepInfo .= '<div class="text-muted" style="font-size:11px">BV ra: '   . get_staff_full_name($aRow['bv_out_staff']) . '</div>';
            if ($status >= 3) $stepInfo .= '<div class="text-muted" style="font-size:11px">BV về: '   . get_staff_full_name($aRow['bv_in_staff']) . '</div>';
            if ($status >= 4) $stepInfo .= '<div class="text-muted" style="font-size:11px">QA xong: ' . get_staff_full_name($aRow['qa_done_staff']) . '</div>';

            $row[] = '<div class="text-center">' . $statusHtml . $quickBtn . $stepInfo . '</div>';

            // ---- Actions dropdown ----
            $view   = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/entrance_ticket/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-eye"></i> ' . lang('view') . '</a>';
            $edit   = ($this->preEdit && $status <= 1)
                ? '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('entrance_ticket/detail/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i> ' . lang('edit') . '</a>'
                : '';
            $delete = ($this->preDelete && $status == 0)
                ? '<a class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                    <button href=\'' . base_url('admin/entrance_ticket/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                    <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                "><i class="fa fa-trash-o"></i> ' . lang('delete') . '</a>'
                : '';

            // Print
            $print = '<a href="' . base_url('admin/entrance_ticket/print_ticket/' . $aRow['id']) . '" target="_blank"><i class="fa fa-print"></i> In phiếu</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    ' . (!empty($edit)   ? '<li>' . $edit   . '</li>' : '') . '
                    ' . (!empty($delete) ? '<li class="not-outside">' . $delete . '</li>' : '') . '
                    <li>' . $print . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    /* =====================================================================
     * GET STAFF – AJAX lấy thông tin nhân viên
     * =================================================================== */
    public function getStaff($id = '')
    {
        $dtData = $this->db->select('s.*, r.name as name_roles, (SELECT GROUP_CONCAT(d.name SEPARATOR ", ") FROM tblstaff_departments sd JOIN tbldepartments d ON d.departmentid = sd.departmentid WHERE sd.staffid = s.staffid) as name_departments')
            ->from('tblstaff s')
            ->join('tblroles r',      'r.roleid = s.role',               'LEFT')
            ->where('s.staffid', $id)
            ->get()->row_array();
        echo json_encode($dtData);
    }

    /* =====================================================================
     * APPROVE – Xử lý các bước duyệt theo luồng
     *
     * Luồng:   0 → 1 (QA duyệt đi)
     *          1 → 2 (BV xác nhận ra)
     *          2 → 3 (BV xác nhận về)
     *          3 → 4 (QA hoàn tất)
     *  Cho phép hủy về bước trước: next_status < current
     * =================================================================== */
    public function approve()
    {
        $data         = [];
        $id           = (int) $this->input->post('id');
        $next_status  = (int) $this->input->post('status');
        $note         = $this->input->post('note');

        // Lấy phiếu
        $ticket = $this->db->where('id', $id)->get(self::TABLE_MAIN)->row_array();
        if (empty($ticket)) {
            echo json_encode(['result' => 0, 'message' => lang('Không tìm thấy phiếu!')]);
            die();
        }

        $current_status = (int) $ticket['status'];

        // ---------- KIỂM TRA BÁO CÁO CÓ ĐANG PENDING KHÔNG ----------
        $this->db->where('entrance_ticket_id', $id);
        $this->db->where('status_production_report', 0);
        $pending_rep = $this->db->get('tbl_entrance_ticket_step')->row_array();
        if (!empty($pending_rep)) {
            echo json_encode(['result' => 0, 'message' => 'Vui lòng hoàn thành quy trình báo cáo thì mới bấm được!']);
            die();
        }

        // ---------- Kiểm tra quyền theo từng bước ----------
        // Bước 0→1 hoặc hủy bước 1: QA
        // Bước 1→2 hoặc 2→3 hoặc hủy: BV
        // Bước 3→4: QA
        $stepPermissionOk = true;

        if (in_array($next_status, [1, 4]) || ($next_status == 0 && $current_status == 1) || ($next_status == 3 && $current_status == 4)) {
            // Cần quyền QA
            if (!$this->preApproveQA) {
                $stepPermissionOk = false;
            }
        } elseif (in_array($next_status, [2, 3]) || ($next_status == 1 && $current_status == 2) || ($next_status == 2 && $current_status == 3)) {
            // Cần quyền BV
            if (!$this->preApproveSecurity) {
                $stepPermissionOk = false;
            }
        }

        if (!$stepPermissionOk) {
            echo json_encode(['result' => 0, 'message' => lang('Bạn không có quyền thực hiện bước này!')]);
            die();
        }

        // ---------- TÍNH TOÁN LOGICAL STATUS NẾU CÓ BÁO CÁO ----------
        $logical_status = $current_status;
        for ($s = $current_status + 1; $s <= 4; $s++) {
            $this->db->select('production_report_id,status_production_report');
            $this->db->where('entrance_ticket_id', $id);
            $this->db->where('step', $s);
            $rep = $this->db->get('tbl_entrance_ticket_step')->row_array();
            if (!empty($rep)) {
                $logical_status = $s;
            } else {
                break;
            }
        }

        // ---------- Kiểm tra thứ tự hợp lệ ----------
        // Cho phép đi tiếp 1 bước từ current_status HOẶC 1 bước từ logical_status
        $validNext = [$current_status + 1, $current_status - 1, -1, $logical_status + 1];
        if (!in_array($next_status, $validNext)) {
            echo json_encode(['result' => 0, 'message' => lang('Trạng thái không hợp lệ. Vui lòng thực hiện đúng thứ tự!')]);
            die();
        }

        // ---------- Cập nhật các trường theo bước ----------
        $update  = ['status' => $next_status];
        $now     = date('Y-m-d H:i:s');
        $staffId = get_staff_user_id();

        if ($next_status > $current_status && $next_status != -1) {
            // Nhận các Checklist từ POST
            if ($next_status == 1) {
                $update['qa_approve_staff'] = $staffId;
                $update['qa_approve_date']  = $now;
                $update['qa_approve_note']  = $note;
                $update['qa_out_valid']     = $this->input->post('qa_out_valid') ? 1 : 0;
                $update['qa_out_allow']     = $this->input->post('qa_out_allow') ? 1 : 0;
            } elseif ($next_status == 2) {
                $update['bv_out_staff'] = $staffId;
                $update['bv_out_date']  = $now;
                $update['bv_out_note']  = $note;
                $update['bv_out_match'] = $this->input->post('bv_out_match') ? 1 : 0;
                $update['actual_time_out'] = $now;
            } elseif ($next_status == 3) {
                $update['bv_in_staff'] = $staffId;
                $update['bv_in_date']  = $now;
                $update['bv_in_note']  = $note;
                $update['bv_return_goods_ok'] = $this->input->post('bv_return_goods_ok') ? 1 : 0;
                $update['bv_return_docs_ok']  = $this->input->post('bv_return_docs_ok') ? 1 : 0;
                $update['bv_return_qty_ok']   = $this->input->post('bv_return_qty_ok')  ? 1 : 0;
                $update['actual_time_in'] = $now;
            } elseif ($next_status == 4) {
                $update['qa_done_staff']     = $staffId;
                $update['qa_done_date']      = $now;
                $update['qa_done_note']      = $note;
                $update['qa_close_goods_ok'] = $this->input->post('qa_close_goods_ok') ? 1 : 0;
                $update['qa_close_docs_ok']  = $this->input->post('qa_close_docs_ok')  ? 1 : 0;
                $update['qa_close_qty_ok']   = $this->input->post('qa_close_qty_ok')   ? 1 : 0;
                $update['qa_close_done']     = $this->input->post('qa_close_done')     ? 1 : 0;
            }
        } elseif ($next_status == -1) {
            // Từ chối (No) - VIOLATION
            $update['status'] = -1;
            $update['violation_code'] = 'VIO-' . $id . '-' . time();
            if ($current_status == 0)      $update['qa_approve_note'] = "[TỪ CHỐI] " . $note;
            elseif ($current_status == 1) $update['bv_out_note']     = "[TỪ CHỐI] " . $note;
            elseif ($current_status == 2) $update['bv_in_note']      = "[TỪ CHỐI] " . $note;
            elseif ($current_status == 3) $update['qa_done_note']     = "[TỪ CHỐI] " . $note;
        } else {
            // Hủy bước (rollback về status thấp hơn hoặc reset)
            if ($current_status == 1) {
                $update['qa_approve_staff'] = null;
                $update['qa_approve_date']  = null;
                $update['qa_approve_note']  = null;
            } elseif ($current_status == 2) {
                $update['bv_out_staff'] = null;
                $update['bv_out_date']  = null;
                $update['bv_out_note']  = null;
            } elseif ($current_status == 3) {
                $update['bv_in_staff'] = null;
                $update['bv_in_date']  = null;
                $update['bv_in_note']  = null;
            } elseif ($current_status == 4) {
                $update['qa_done_staff'] = null;
                $update['qa_done_date']  = null;
                $update['qa_done_note']  = null;
            }
        }

        $this->db->where('id', $id)->update(self::TABLE_MAIN, $update);

        $nextLabel   = self::STATUS_LABELS[$next_status]['label'] ?? 'N/A';
        $actionLabel = $next_status > $current_status ? 'Cập nhật' : 'Hủy';

        insertActivityLog([
            'type_parent_obj' => self::REF_PREFIX,
            'table_obj'       => self::TABLE_MAIN,
            'id_obj'          => $id,
            'name_obj'        => $ticket['reference_no'],
            'content'         => $actionLabel . ' trạng thái phiếu ra cổng [' . $ticket['reference_no'] . '] → ' . $nextLabel,
            'actions'         => 'approved',
        ]);

        // Gửi email thông báo phê duyệt / từ chối
        if ($next_status > $current_status || $next_status == -1) {
            $this->Entrance_ticket_mail_model->send_approve_reject_email($id, $next_status);
        }

        echo json_encode(['result' => 1, 'message' => lang('Cập nhật thành công!'), 'new_status' => $next_status, 'new_label' => $nextLabel]);
        die();
    }

    /* =====================================================================
     * SET_NO_STEP – Ghi nhận bước Không đạt (No) mà KHÔNG đổi status chính
     *
     * Khi user bấm "No" tại bước N:
     *   - Ghi note "[KHÔNG ĐẠT] ..." vào cột note tương ứng của bước đó
     *   - Status KHÔNG đổi → bước đó vẫn "đang chờ"
     *   - User phải tạo báo cáo không phù hợp, sau đó có thể bấm Yes để tiếp tục
     * =================================================================== */
    public function set_no_step()
    {
        $id   = (int) $this->input->post('id');
        $step = (int) $this->input->post('step');
        $note = $this->input->post('note');

        $ticket = $this->db->where('id', $id)->get(self::TABLE_MAIN)->row_array();
        if (empty($ticket)) {
            echo json_encode(['result' => 0, 'message' => lang('Không tìm thấy phiếu!')]);
            die();
        }

        $current_status = (int) $ticket['status'];

        // ---------- KIỂM TRA BÁO CÁO CÓ ĐANG PENDING KHÔNG ----------
        $this->db->where('entrance_ticket_id', $id);
        $this->db->where('status_production_report', 0);
        $pending_rep = $this->db->get('tbl_entrance_ticket_step')->row_array();
        if (!empty($pending_rep)) {
            echo json_encode(['result' => 0, 'message' => 'Vui lòng hoàn thành quy trình báo cáo thì mới bấm được!']);
            die();
        }

        // Bước chỉ được ghi "no" khi đến lượt (status == step - 1)
        if ($current_status != $step - 1) {
            echo json_encode(['result' => 0, 'message' => lang('Bước không hợp lệ hoặc chưa đến lượt!')]);
            die();
        }

        // Map bước → cột note tương ứng
        $noteColMap = [
            1 => 'qa_approve_note',
            2 => 'bv_out_note',
            3 => 'bv_in_note',
            4 => 'qa_done_note',
        ];
        // Map bước → cột staff và date để ghi người thực hiện
        $staffColMap = [
            1 => 'qa_approve_staff',
            2 => 'bv_out_staff',
            3 => 'bv_in_staff',
            4 => 'qa_done_staff',
        ];
        $dateColMap = [
            1 => 'qa_approve_date',
            2 => 'bv_out_date',
            3 => 'bv_in_date',
            4 => 'qa_done_date',
        ];

        if (!isset($noteColMap[$step])) {
            echo json_encode(['result' => 0, 'message' => lang('Bước không tồn tại!')]);
            die();
        }

        $update = [
            $noteColMap[$step]  => $note,
            $staffColMap[$step] => get_staff_user_id(),
            $dateColMap[$step]  => date('Y-m-d H:i:s'),
            // Không đổi status → vẫn ở bước N-1
        ];

        $this->db->where('id', $id)->update(self::TABLE_MAIN, $update);

        insertActivityLog([
            'type_parent_obj' => self::REF_PREFIX,
            'table_obj'       => self::TABLE_MAIN,
            'id_obj'          => $id,
            'name_obj'        => $ticket['reference_no'],
            'content'         => 'Ghi nhận KHÔNG ĐẠT bước ' . $step . ' phiếu ra cổng [' . $ticket['reference_no'] . ']',
            'actions'         => 'approved',
        ]);

        echo json_encode(['result' => 1, 'message' => lang('Ghi nhận thành công!')]);
        die();
    }

    /* =====================================================================
     * DELETE – Xóa phiếu (chỉ khi status = 0)
     * =================================================================== */
    public function delete($id)
    {
        if (!$this->preDelete) {
            echo json_encode(['result' => 0, 'message' => lang('access_denied')]);
            die();
        }

        $ticket = $this->db->where('id', $id)->get(self::TABLE_MAIN)->row_array();
        if (empty($ticket)) {
            echo json_encode(['result' => 0, 'message' => lang('Không tìm thấy phiếu!')]);
            die();
        }
        if ($ticket['status'] > 0) {
            echo json_encode(['result' => 0, 'message' => lang('Phiếu đã được duyệt, không thể xóa!')]);
            die();
        }

        $this->db->where('id', $id)->delete(self::TABLE_MAIN);
        $this->db->where('entrance_ticket_id', $id)->delete(self::TABLE_ITEMS);

        insertActivityLog([
            'type_parent_obj' => self::REF_PREFIX,
            'table_obj'       => self::TABLE_MAIN,
            'id_obj'          => $id,
            'name_obj'        => $ticket['reference_no'],
            'content'         => 'Xóa phiếu mang hàng ra cổng [' . $ticket['reference_no'] . ']',
            'actions'         => 'delete',
        ]);

        echo json_encode(['result' => 1, 'message' => lang('Xóa phiếu thành công!')]);
        die();
    }

    /* =====================================================================
     * PRINT_TICKET – In phiếu (view riêng)
     * =================================================================== */
    public function print_ticket($id)
    {
        $this->db->select('t.*,
            CONCAT(s.firstname," ",s.lastname) as fullname,
            s.code as staff_code,
            r.name as name_roles,
            (SELECT GROUP_CONCAT(d.name SEPARATOR ", ") 
             FROM tblstaff_departments sd 
             JOIN tbldepartments d ON d.departmentid = sd.departmentid 
             WHERE sd.staffid = t.id_staff) as name_departments,
            s.phone as staff_phone
        ')
            ->from(self::TABLE_MAIN . ' t')
            ->join('tblstaff s',      's.staffid = t.id_staff',          'LEFT')
            ->join('tblroles r',      'r.roleid = s.role',               'LEFT')
            ->where('t.id', $id);
        $dtData = $this->db->get()->row_array();

        if (empty($dtData)) {
            show_404();
        }

        $this->db->from(self::TABLE_ITEMS)->where('entrance_ticket_id', $id);
        $dtItems = $this->db->get()->result_array();

        $data['dtData']        = $dtData;
        $data['dtItems']       = $dtItems;
        $data['title']         = 'In phiếu - ' . $dtData['reference_no'];
        $data['status_labels'] = self::STATUS_LABELS;
        $data['company']       = get_option('invoice_company_name');
        $data['address']       = get_option('invoice_company_address');
        $this->load->view('admin/entrance_ticket/print', $data);
    }

    /* =====================================================================
     * EXPORT_EXCEL – Xuất Excel danh sách
     * =================================================================== */
    public function exportExcel()
    {
        if (!$this->input->post('export_excel')) {
            return;
        }

        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $start_date = $this->input->post('start_date_search');
        $end_date   = $this->input->post('end_date_search');

        $this->db->select('t.*,
            CONCAT(s.firstname," ",s.lastname) as fullname,
            s.code as staff_code,
            r.name as name_roles,
            (SELECT GROUP_CONCAT(d.name SEPARATOR ", ") 
             FROM tblstaff_departments sd 
             JOIN tbldepartments d ON d.departmentid = sd.departmentid 
             WHERE sd.staffid = t.id_staff) as name_departments
        ')
            ->from(self::TABLE_MAIN . ' t')
            ->join('tblstaff s',      's.staffid = t.id_staff',          'LEFT')
            ->join('tblroles r',      'r.roleid = s.role',               'LEFT');

        if (!empty($start_date)) {
            $this->db->where("t.date >= '" . to_sql_date($start_date) . " 00:00:00'");
        }
        if (!empty($end_date)) {
            $this->db->where("t.date <= '" . to_sql_date($end_date) . " 23:59:59'");
        }
        $this->db->order_by('t.id DESC');
        $dtData = $this->db->get()->result_array();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->applyFromArray(['font' => ['name' => 'Times New Roman']]);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Tiêu đề
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', get_option('invoice_company_name'))
            ->mergeCells('A1:P1');
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A2', 'PHIẾU MANG HÀNG RA/VÀO CỔNG')
            ->getStyle('A2')->applyFromArray([
                'font'      => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
            ]);
        $objPHPExcel->getActiveSheet()->mergeCells('A2:P2');

        $headerRow = 3;
        $headers = [
            'STT',
            'Mã Phiếu',
            'Ngày Lập',
            'Mã NV',
            'Họ Tên',
            'Vị Trí',
            'Phòng Ban',
            'Lý Do',
            'Điểm Đến',
            'Số Xe',
            'Chứng Từ',
            'Tên Hàng',
            'SL',
            'ĐVT',
            'Ghi Chú',
            'Trạng Thái'
        ];
        foreach ($headers as $col => $header) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $headerRow, $header);
        }
        $objPHPExcel->getActiveSheet()->getStyle('A' . $headerRow . ':P' . $headerRow)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11],
            'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
        ]);

        $row = $headerRow;
        $stt = 0;
        foreach ($dtData as $v) {
            $row++;
            $stt++;
            $statusLabel = self::STATUS_LABELS[(int)$v['status']]['label'] ?? '';
            $data = [
                $stt,
                $v['reference_no'],
                _dt($v['date']),
                $v['staff_code'],
                $v['fullname'],
                $v['name_roles'],
                $v['name_departments'],
                $v['note_reason'],
                $v['destination'],
                $v['vehicle_no'],
                '', // Legacy columns
                '',
                '',
                '',
                '',
                $statusLabel
            ];
            foreach ($data as $col => $val) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $val);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':P' . $row)->applyFromArray([
                'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
            ]);
        }

        $filename = 'Phieu_mang_hang_ra_cong_' . date('Ymd') . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        die(json_encode([
            'result'   => 1,
            'filename' => $filename,
            'message'  => lang('success'),
            'file'     => 'data:application/vnd.ms-excel;base64,' . base64_encode($xlsData),
        ]));
    }

    /* =====================================================================
     * LOCATIONS – Quản lý vị trí (QA, BV...)
     * =================================================================== */
    public function locations()
    {
        if (!is_admin()) {
            access_denied();
        }
        $data['title'] = lang('Quản lý vị trí phiếu mang hàng');
        
        // Fetch locations and their roles
        $locations = $this->db->get(self::TABLE_LOCATIONS)->result_array();
        foreach ($locations as &$loc) {
            $this->db->select('role_id');
            $this->db->where('location_id', $loc['id']);
            $roles = $this->db->get(self::TABLE_LOCATION_ROLES)->result_array();
            $loc['role_ids'] = array_column($roles, 'role_id');
        }
        $data['locations'] = $locations;
        
        $roles = $this->db->get('tblroles')->result_array();
        $roles_map = [];
        foreach ($roles as $r) {
            $roles_map[$r['roleid']] = $r['name'];
        }
        $data['roles_map'] = $roles_map;
        
        $this->load->view('admin/entrance_ticket/locations/index', $data);
    }

    public function location_detail($id = 0)
    {
        if (!is_admin()) {
            access_denied();
        }
        if ($this->input->post()) {
            $name = $this->input->post('name');
            $role_ids = $this->input->post('role_ids'); // Array of role IDs

            if ($id > 0) {
                $this->db->where('id', $id)->update(self::TABLE_LOCATIONS, ['name' => $name]);
                $location_id = $id;
                set_alert('success', lang('updated_successfully'));
            } else {
                $this->db->insert(self::TABLE_LOCATIONS, ['name' => $name]);
                $location_id = $this->db->insert_id();
                set_alert('success', lang('added_successfully'));
            }

            // Sync roles
            $this->db->where('location_id', $location_id)->delete(self::TABLE_LOCATION_ROLES);
            if (!empty($role_ids) && is_array($role_ids)) {
                $insert_batch = [];
                foreach ($role_ids as $rid) {
                    $insert_batch[] = [
                        'location_id' => $location_id,
                        'role_id'     => $rid
                    ];
                }
                $this->db->insert_batch(self::TABLE_LOCATION_ROLES, $insert_batch);
            }

            redirect(admin_url('entrance_ticket/locations'));
        }

        $data['roles'] = $this->db->get('tblroles')->result_array();
        $data['id']    = $id;
        if ($id > 0) {
            $data['location'] = $this->db->where('id', $id)->get(self::TABLE_LOCATIONS)->row_array();
            $roles = $this->db->where('location_id', $id)->get(self::TABLE_LOCATION_ROLES)->result_array();
            $data['location']['role_ids'] = array_column($roles, 'role_id');
        }
        $this->load->view('admin/entrance_ticket/locations/detail', $data);
    }

    public function delete_location($id)
    {
        if (!is_admin()) {
            access_denied();
        }
        $this->db->where('id', $id)->delete(self::TABLE_LOCATIONS);
        $this->db->where('location_id', $id)->delete(self::TABLE_LOCATION_ROLES);
        set_alert('success', lang('deleted_successfully'));
        redirect(admin_url('entrance_ticket/locations'));
    }

    /* =====================================================================
     * SEARCH CLIENTS – AJAX select2 tìm kiếm khách hàng
     * GET /admin/entrance_ticket/searchClients       → {results:[{id,text}]}
     * GET /admin/entrance_ticket/searchClients/{id}  → {row:{id,text}}
     * =================================================================== */
    public function searchClients($id = 0)
    {
        if ($id > 0) {
            $row = $this->db->select('userid as id, company as text')
                ->where('userid', $id)->get('tblclients')->row_array();
            echo json_encode(['row' => $row ?: ['id' => 0, 'text' => '']]);
            die();
        }
        $term  = $this->input->get_post('term');
        $limit = (int) $this->input->get_post('limit') ?: 50;
        $this->db->select('userid as id, company as text')
            ->from('tblclients')
            ->like('company', $term)
            ->order_by('company')
            ->limit($limit);
        echo json_encode(['results' => $this->db->get()->result_array()]);
        die();
    }

    /* =====================================================================
     * SEARCH SUPPLIERS – AJAX select2 tìm kiếm nhà cung cấp
     * =================================================================== */
    public function searchSuppliers($id = 0)
    {
        if ($id > 0) {
            $row = $this->db->select('id, company as text')
                ->where('id', $id)->get('tblsuppliers')->row_array();
            echo json_encode(['row' => $row ?: ['id' => 0, 'text' => '']]);
            die();
        }
        $term  = $this->input->get_post('term');
        $limit = (int) $this->input->get_post('limit') ?: 50;
        $this->db->select('id, company as text')
            ->from('tblsuppliers')
            ->like('company', $term)
            ->order_by('company')
            ->limit($limit);
        echo json_encode(['results' => $this->db->get()->result_array()]);
        die();
    }

    /* =====================================================================
     * SEARCH PRODUCTS – AJAX select2 tìm kiếm sản phẩm
     * =================================================================== */
    public function searchProducts($id = 0)
    {
        if ($id > 0) {
            $row = $this->db->select('id, CONCAT(tbl_products.code, " - ", tbl_products.name) as text')
                ->where('id', $id)->get('tbl_products')->row_array();
            echo json_encode(['row' => $row ?: ['id' => 0, 'text' => '']]);
            die();
        }
        $term  = $this->input->get_post('term');
        $limit = (int) $this->input->get_post('limit') ?: 50;
        $this->db->select('id, CONCAT(tbl_products.code, " - ", tbl_products.name) as text')
            ->from('tbl_products')
            ->group_start()
                ->like('name', $term)
                ->or_like('code', $term)
            ->group_end()
            // ->where('status', 1)
            ->order_by('name')
            ->limit($limit);
        echo json_encode(['results' => $this->db->get()->result_array()]);
        die();
    }
}
