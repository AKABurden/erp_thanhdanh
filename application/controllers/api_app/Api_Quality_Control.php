<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/api_app/Api_Controller.php');

class Api_Quality_Control extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('quality_control_model');
        $this->load->model('manufactures_model');
        $this->load->model('products_model');
        $this->load->model('unit_model');
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('check_quality');
        $tokenAccount = '';
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['tokenAccount'])) {
                    $tokenAccount = $data_post['tokenAccount'];
                }
            }
        }
        if (empty($tokenAccount)) {
            $tokenAccount = $this->input->post('tokenAccount');
        }
        $staffid = checkTokenLoginApp($tokenAccount);
        $staff = get_table_where('tblstaff', array('staffid' => $staffid), '', 'row');
        if (!empty($staff)) {
            $this->staffid = $staffid;
        } else {
            echo json_encode([
                'code' => 111,
                'message' => 'User không tồn tại',
                'result' => false,
            ]);
            die;
        }

        $this->perViewQC = has_permission('quality_control', $this->staffid, 'view');
        $this->perViewOwnQC = has_permission('quality_control', $this->staffid, 'view_own');
        $this->perAddQC = has_permission('quality_control', $this->staffid, 'create');
        $this->perEditQC = has_permission('quality_control', $this->staffid, 'edit');
        $this->perDeleteQC = has_permission('quality_control', $this->staffid, 'delete');
        $this->perExportQC = has_permission('quality_control', $this->staffid, 'export');
        $this->perPrintQC = has_permission('quality_control', $this->staffid, 'print');
        $this->isAdmin = is_admin($this->staffid);
        $this->branchID = get_staff_user_id_branch_app($this->staffid);
    }

    public function getClients($page = 1, $limit = 10)
    {
        $result = [];
        $start = ($page - 1) * $limit;
        $name_search = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['name_search'])) {
                        $name_search = $data_post['name_search'];
                    }
                }
            }
        }
        $this->db->select("
            tblclients.userid as userid,
            tblclients.zcode,
            tblclients.company as company,
            tblclients.representative,
            tblclients.phonenumber as phonenumber,
            (
                SELECT GROUP_CONCAT(staffid) 
                FROM tblstaff 
                JOIN tblcustomer_admins ON tblcustomer_admins.staff_id = tblstaff.staffid
                WHERE tblcustomer_admins.customer_id = tblclients.userid
            ) as staff_group,
            tblclients.datecreated as datecreated,
            vat,
            tblclients.address as addressClient
            ", FALSE)
            ->from('tblclients')
            ->join('tblstatus_client', 'tblstatus_client.id = tblclients.status_clients', 'left')
            ->join('tblcontacts', 'tblcontacts.userid = tblclients.userid AND tblcontacts.is_primary=1', 'left');

        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->where('tblclients.company like "%' . $name_search . '%"');
            $this->db->or_where('tblclients.phonenumber like "%' . $name_search . '%"');
            $this->db->group_end();
        }
        $this->db->order_by('tblclients.userid DESC');
        $this->db->limit($limit, $start);
        // print_arrays($this->db->get_compile_select());
        $result = $this->db->get()->result_array();
        $data['result'] = $result;

        $this->db->select('tblclients.phonenumber', false);
        $startNest = ($page) * $limit;
        $this->db->limit(1, $startNest);

        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->where('tblclients.company like "%' . $name_search . '%"');
            $this->db->or_where('tblclients.phonenumber like "%' . $name_search . '%"');
            $this->db->group_end();
        }


        $this->db->from('tblclients');
        $this->db->join('tblstatus_client', 'tblstatus_client.id = tblclients.status_clients', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.userid = tblclients.userid', 'left');
        $data['next'] = $this->db->get()->num_rows();

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getCheckQuality($page = 1, $limit = 10)
    {

        if (!$this->perViewQC && !$this->perViewOwnQC) {
            $data['result'] = null;
            $data['next'] = 0;
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die;
        }
        $result = [];
        $start = ($page - 1) * $limit;


        $name_search = '';
        $order_search = '';
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = json_decode($data_post, true);
                    if (!empty($data_post['order_search'])) {
                        $order_search = $data_post['order_search'];
                    }
                    if (!empty($data_post['name_search'])) {
                        $name_search = $data_post['name_search'];
                    }
                }
            }
        }

        $arrIDStaff = employee_manage_staff_app($this->staffid);

        $this->db->dbprefix = '';
        $this->db->select("
            tbl_check_quality.id as id,
            tbl_check_quality.date as date,
            tbl_check_quality.reference_no as reference_no,
            tblbranch.name as branch_name,
            tbl_check_quality.pod_id as pod_id,
            tblclients.company as company,
            tbl_check_quality.order_id as order_id,
            tbl_check_quality.plan_id as plan_id,
            tbl_check_quality.quantity_qc as quantity_qc,
            SUM(tbl_check_quality_items.quantity_recycling + tbl_check_quality_items.quantity_waste) as qty_item_error,
            SUM(tbl_check_quality_items.quantity_success) as qty_item_success,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
            tbl_check_quality.note as note,
            tbl_check_quality.created_by as staff_id,
            tblstaff.profile_image,
            ", FALSE)
            ->from('tbl_check_quality')
            ->join('tblstaff', 'tblstaff.staffid = tbl_check_quality.created_by', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_check_quality.customer_id', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_check_quality.id_branch', 'left')
            ->join(
                'tbl_check_quality_items',
                'tbl_check_quality_items.check_quality_id = tbl_check_quality.id',
                'left'
            )
            ->group_by('tbl_check_quality.id');

        if (!$this->perViewQC) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                $this->db->where('(tbl_check_quality.created_by IN (' . $coverStr . '))');
            }
        } else {
            if (!$this->isAdmin) {
                if ($this->branchID != 1) {
                    if ($arrIDStaff) {
                        $coverStr = implode(",", $arrIDStaff);
                        $this->db->where('(tbl_check_quality.created_by IN (' . $coverStr . ')  OR tbl_check_quality.id_branch = ' . $this->branchID . ')');
                    }
                }
            }
        }

        if (!empty($order_search)) {
            $this->db->where('tbl_check_quality_items.order_id', $order_search);
        }
        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->where('tbl_check_quality.reference_no like "%' . $name_search . '%"');
            $this->db->or_where('tbl_check_quality.note like "%' . $name_search . '%"');
            $this->db->group_end();
        }

        $this->db->order_by('tbl_check_quality.date DESC');
        $this->db->limit($limit, $start);
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $result[$key]['pod_id_array'] = $this->get_pod_api($value['pod_id']);
                $result[$key]['order_id_array'] = $this->get_order_api($value['order_id']);
                $result[$key]['plan_id_array'] = $this->get_plan_api($value['plan_id']);
                $result[$key]['pos'] = get_po_new($value['pod_id']);
                if ($value['profile_image'] !== null) {
                    $result[$key]['profile_image'] = base_url('uploads/staff_profile_images/' . $value['staff_id'] . '/thumb_' . $value['profile_image']);
                } else {
                    $result[$key]['profile_image'] = base_url('assets/images/user-placeholder.jpg');
                }
            }
        }

        $data['result'] = $result;

        //next
        $this->db->select('tbl_check_quality.*', false);
        $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.check_quality_id = tbl_check_quality.id', 'left');
        $startNest = ($page) * $limit;
        $this->db->limit(1, $startNest);
        if (!empty($order_search)) {
            $this->db->where('tbl_check_quality_items.order_id', $order_search);
        }
        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->where('tbl_check_quality.reference_no like "%' . $name_search . '%"');
            $this->db->group_end();
        }

        if (!$this->perViewQC) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                $this->db->where('(tbl_check_quality.created_by IN (' . $coverStr . '))');
            }
        } else {
            if (!$this->isAdmin) {
                if ($this->branchID != 1) {
                    if ($arrIDStaff) {
                        $coverStr = implode(",", $arrIDStaff);
                        $this->db->where('(tbl_check_quality.created_by IN (' . $coverStr . ')  OR tbl_check_quality.id_branch = ' . $this->branchID . ')');
                    }
                }
            }
        }
        $this->db->from('tbl_check_quality');
        $data['next'] = $this->db->get()->num_rows();

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getDetailCheckQuality($id = '')
    {
        $data = [];
        $qualityControl = $this->quality_control_model->rowCheckQuality($id);
        $client = get_table_where('tblclients', ['userid' => $qualityControl['customer_id']], '', 'row_array');
        $pod = get_pods($qualityControl['pod_id']);
        $items = get_table_where('tbl_check_quality_items', ['check_quality_id' => $id], '', 'result_array');
        $branch = get_table_where('tblbranch', ['id' => $qualityControl['id_branch']], '', 'row_array');
        $branch_name = '';
        if (!empty($branch)) {
            $branch_name = $branch['name'];
        }


        $data['qualityControl'] = $qualityControl;
        $data['qualityControl']['pod'] = $pod;
        $data['qualityControl']['client'] = $client;
        $data['qualityControl']['created_by'] = get_staff_full_name($qualityControl['created_by']);
        $data['qualityControl']['branch'] = $branch_name;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $items[$key]['data_json_taiche'] = json_decode($value['data_json_taiche']);
                $items[$key]['data_json_phe'] = json_decode($value['data_json_phe']);

                $items[$key]['quantity_dat'] = formatNumber($value['quantity_qc'] - ($value['quantity_recycling'] + $value['quantity_waste']));
                $items[$key]['PhanKhongTramDat'] = formatNumber(($value['quantity_recycling'] + $value['quantity_waste']) * 100 / $value['quantity_qc']);
                $items[$key]['PhanTramDat'] = formatNumber(($value['quantity_qc'] - ($value['quantity_recycling'] + $value['quantity_waste'])) * 100 / $value['quantity_qc']);

                $stage_name = '';
                $stage_name_again = '';
                $stage = get_table_where('tbl_stages', ['id' => $value['id_stage']], '', 'row_array');
                if (!empty($stage)) {
                    $stage_name = $stage['name'];
                }
                $stage_again = get_table_where('tbl_stages', ['id' => $value['id_stage_again']], '', 'row_array');
                if (!empty($stage_again)) {
                    $stage_name_again = $stage_again['name'];
                }
                $items[$key]['stage_name'] = $stage_name;
                $items[$key]['stage_name_again'] = $stage_name_again;
                $plan_order_name = '';
                if ($value['object_type'] == 'orders') {
                    $order = get_table_where(
                        'tbl_orders',
                        ['id' => $value['order_id']],
                        '',
                        'row_array'
                    );
                    if (!empty($order)) {
                        $plan_order_name = $order['reference_no'];
                    }
                } elseif ($value['object_type'] == 'business_plan') {
                    $plan = get_table_where(
                        'tbl_business_plan',
                        ['id' => $value['plan_id']],
                        '',
                        'row_array'
                    );
                    if (!empty($plan)) {
                        $plan_order_name = $plan['reference_no'];
                    }
                }
                $items[$key]['plan_order_name'] = $plan_order_name;
                $pod = get_table_where(
                    'tbl_productions_orders_details',
                    ['id' => $value['pod_id']],
                    '',
                    'row_array'
                );
                if (!empty($pod)) {
                    $pod_name = $pod['reference_no'];
                }
                $items[$key]['pod_name'] = $pod_name;
                $images = '';
                if ($value['type_item'] == "products" || $value['type_item'] == "semi_products") {
                    $info = $this->products_model->rowProduct($value['item_id']);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);

                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }
                $items[$key]['images'] = $images;
                $items[$key]['unit_name'] = $unit['unit'];
                $images = [];
                if (!empty($value['images_multiple'])) {
                    $images_multiple = explode('||', $value['images_multiple']);
                    if (!empty($images_multiple)) {
                        foreach ($images_multiple as $k => $v) {
                            $images[] = base_url('uploads/check_quality/' . $v);
                        }
                    }
                }
                $items[$key]['image'] = $images;
            }
        }
        $data['items'] = $items;
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function get_pod_api($text)
    {
        $pod = [];
        if (!empty($text)) {
            $array = explode(',', $text);
            foreach ($array as $key => $value) {
                $ktr = get_table_where('tbl_productions_orders_details', array('id' => $value), '', 'row');
                if (!empty($ktr)) {
                    $pod[] = [
                        'code' => $ktr->reference_no,
                        'id' => $ktr->id,
                    ];
                }
            }
        }

        return $pod;
    }
    public function get_order_api($text)
    {
        $order = [];
        if (!empty($text)) {
            $array = explode(',', $text);
            foreach ($array as $key => $value) {
                $ktr = get_table_where('tbl_orders', array('id' => $value), '', 'row');
                if (!empty($ktr)) {
                    $order[] = [
                        'code' => $ktr->reference_no,
                        'id' => $ktr->id,
                    ];
                }
            }
        }

        return $order;
    }
    public function get_plan_api($text)
    {
        $plan = [];
        if (!empty($text)) {
            $array = explode(',', $text);
            foreach ($array as $key => $value) {
                $ktr = get_table_where('tbl_business_plan', array('id' => $value), '', 'row');
                if (!empty($ktr)) {
                    $plan[] = [
                        'code' => $ktr->reference_no,
                        'id' => $ktr->id,
                    ];
                }
            }
        }

        return $plan;
    }

    public function searchProductByProduction()
    {
        $data = [];
        $product = [];
        $data_post = file_get_contents('php://input');
        $name_search = '';
        $id_branch = '';
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['name_search'])) {
                    $name_search = $data_post['name_search'];
                }
                if (!empty($data_post['id_branch'])) {
                    $id_branch = $data_post['id_branch'];
                }
            }
        }
        $qtQC = '
        COALESCE(
        (SELECT SUM(tbl_productions_orders_details.qty_qc) ),0)
        ';
        $this->db->select(
            '
        CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
        CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
        tbl_products.name as name,
        tbl_products.code as code,
        tbl_products.unit_id as unit_id,
        tblunits.unit as unit_name,
        tbl_products.price_import as price_import,
        tbl_products.images as images,
        CONCAT(tbl_orders.reference_no) as reference_no,
        CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
        tbl_productions_orders_details.reference_no as reference_no_production_detail,
        COALESCE(SUM(tbl_productions_orders_items.quantity),0) as total_qty,
        ' . $qtQC . ' as qty_qc,
        tbl_productions_orders_details.id as pod_id,
        tbl_colors.name as name_color,
        tbl_productions_orders_details.object_type as object_type,
        tbl_business_plan.id as plan_id,
        tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
        tbl_orders.id as idd',
            false
        );

        $this->db->from('tbl_productions_orders_details');
        $this->db->join(
            'tbl_productions_orders_items',
            'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
            'left'
        );
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id ', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
        $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
        $this->db->join(
            'tbl_products',
            'tbl_products.id = tbl_productions_orders_items.items_id ',
            'left'
        );
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
        $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id', 'left');
        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->where('tbl_productions_orders_details.reference_no like "%' . $name_search . '%"');
            $this->db->or_where('tbl_orders.reference_no like "%' . $name_search . '%"');
            $this->db->or_where('tbl_products.name like "%' . $name_search . '%"');
            $this->db->or_where('tbl_products.code like "%' . $name_search . '%"');
            $this->db->group_end();
        }
        if ($id_branch != 1) {
            $this->db->where('tbl_productions_orders.location_id', $id_branch);
        }
        $this->db->order_by('tbl_products.name', 'DESC');
        $this->db->group_by('tbl_productions_orders_details.id');
        $product = $this->db->get()->result_array();
        if (!empty($product)) {
            foreach ($product as $key => $value) {
                $items = [];
                $item_id = explode('__', $value['id']);
                $item_id = $item_id[1];
                $type_item = $item_id[0];
                $info = $this->products_model->rowProduct($item_id);
                $images = '';
                if (!empty($info)) {
                    $images = base_url('uploads/products/' . $info['images']);
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }
                $this->db->select('*');
                $this->db->from('tbl_product_stages');
                $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                $this->db->where('tbl_product_stages.product_id', $item_id);
                $stages =  $this->db->get()->row_array();
                if (!empty($stages)) {
                    $productions_orders_item_id = $value['productions_orders_item_id'];
                    $stageProduction = "(
                        SELECT active
                        FROM tbl_productions_orders_items_stages
                        WHERE tbl_productions_orders_items_stages.stage_id = tbl_stages.id AND tbl_productions_orders_items_stages.productions_orders_items_id = $productions_orders_item_id
                        LIMIT 1
                    )";
                    $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                    $this->db->from('tbl_product_stages_versions');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                    $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                    $this->db->where('tbl_stages.status_qc', 1);
                    $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                    $items = $this->db->get()->result_array();
                }
                $product[$key]['stages'] = $items;
                $product[$key]['images'] = $images;
            }
        }

        $data['product'] = $product;

        echo json_encode($data);
    }
    public function getBranch()
    {
        $data = [];
        $this->db->select('tblbranch.*');
        $this->db->from('tblbranch');
        $this->db->where('id !=', 1);
        $branch = $this->db->get()->result_array();
        $data['branch'] = $branch;

        $branch_user = get_table_where('tblbranch', ['id' => $this->branchID], '', 'row_array');
        if (!empty($branch_user)) {
            $data['branch_user'] = $branch_user;
        } else {
            $data['branch_user'] = [];
        }
        echo json_encode($data);
    }
    public function getListError()
    {
        $data = [];
        $data_post = file_get_contents('php://input');
        $name_search = '';
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['name_search'])) {
                    $name_search = $data_post['name_search'];
                }
            }
        }
        $this->db->select('tbl_detail_errors.*');
        $this->db->from('tbl_detail_errors');
        if (!empty($name_search)) {
            $this->db->group_start();
            $this->db->where('tbl_detail_errors.name like "%' . $name_search . '%"');
            $this->db->or_where('tbl_detail_errors.code like "%' . $name_search . '%"');
            $this->db->group_end();
        }
        $listError = $this->db->get()->result_array();
        $data['listError'] = $listError;
        echo json_encode($data);
    }

    public function addCheckQuantity()
    {
        $data = [];
        $checkQualityItems = [];
        $count_items = 0;
        $total_quantity = 0;

        $order_id_text = '';
        $pod_id_text = '';
        $plan_id_text = '';
        $file_old_exist = [];
        if ($this->input->post()) {

            $date = to_sql_date($this->input->post('date'), true);
            $note = $this->input->post('note');
            $id_branch = $this->input->post('id_branch');

            $items = $this->input->post('type_item');
            foreach ($items as $key => $value) {
                $uploadData = [];
                $data_json_taiche = '';
                $data_json_phe = '';
                $type_item = $value;
                $type_item = trim($type_item, '""');
                if (empty($type_item)) {
                    continue;
                }
                $arrs = explode('__', $type_item);
                $item_id = $arrs[1];
                $type_item = $arrs[0];
                $product = $this->products_model->rowProduct($item_id);
                if (empty($product)) {
                    continue;
                }
                $item_code = $product['code'];
                $item_name = $product['name'];

                $quantity_qc = number_unformat($this->input->post('quantity_success')[$key]);
                $quantity_che = number_unformat($this->input->post('quantity_recycling')[$key]);
                $quantity_phe = number_unformat($this->input->post('quantity_waste')[$key]);
                $data_json_taiche = $this->input->post('data_json_taiche')[$key];
                $data_json_taiche = ($data_json_taiche);
                $data_json_taiche = trim($data_json_taiche, '""');
                $data_json_phe = $this->input->post('data_json_phe')[$key];
                $data_json_phe = ($data_json_phe);
                $data_json_phe = trim($data_json_phe, '""');
                $pod_id = $this->input->post('pod_id')[$key];
                $pod_id = trim($pod_id, '""');
                $id_stage = $this->input->post('id_stage')[$key];
                $id_stage = trim($id_stage, '""');
                $object_type = $this->input->post('object_type')[$key];
                $object_type = trim($object_type, '""');
                $result = $this->input->post('result')[$key];
                $result = trim($result, '""');
                if ($result == 0 || $result == 1) {
                    $result = 1;
                }
                if ($result == 2) {
                    $result = 2;
                }
                if (!empty($this->input->post('id_stage_again')[$key])) {
                    $id_stage_again = $this->input->post('id_stage_again')[$key];
                    $id_stage_again = trim($id_stage_again, '""');
                } else {
                    $id_stage_again = 0;
                }
                if ($result == 1) {
                    $id_stage_again = 0;
                }

                if ($this->input->post('plan_id')[$key] == '' || $this->input->post('plan_id')[$key] == null) {
                    $plan_id = 0;
                } else {
                    $plan_id = $this->input->post('plan_id')[$key];
                    $plan_id = trim($plan_id, '""');
                }
                if ($this->input->post('order_id')[$key] == '' || $this->input->post('order_id')[$key] == null) {
                    $order_id = 0;
                } else {
                    $order_id = $this->input->post('order_id')[$key];
                    $order_id = trim($order_id, '""');
                }

                if ($data_json_phe == null) {
                    $data_json_phe = '';
                }
                if ($data_json_taiche == null) {
                    $data_json_taiche = '';
                }

                $cqis_id = !empty($this->input->post('cqis_id')[$key]) ? $this->input->post('cqis_id')[$key] : 0;

                $checkQualityItems[$key] = [
                    'type_item' => $type_item,
                    'item_id' => $item_id,
                    'item_code' => $item_code,
                    'item_name' => $item_name,
                    'quantity_qc' => $quantity_qc,
                    'quantity_recycling' => $quantity_che,
                    'quantity_waste' => $quantity_phe,
                    'quantity_success' => ($quantity_qc - ($quantity_che + $quantity_phe)),
                    'pod_id' => $pod_id,
                    'order_id' => $order_id,
                    'plan_id' => $plan_id,
                    'object_type' => $object_type,
                    'data_json_phe' => $data_json_phe,
                    'data_json_taiche' => $data_json_taiche,
                    'id_stage' => $id_stage,
                    'result' => $result,
                    'id_stage_again' => $id_stage_again,
                    'cqis_id' => $cqis_id,
                ];
                $order_id_text .= $order_id . ',';
                $pod_id_text .= $pod_id . ',';
                $plan_id_text .= $plan_id . ',';
                $total_quantity += $quantity_qc;
                $count_items++;
                $this->load->library('upload');
                $value = trim($value, '""');
                $key_image = $value . '_' . $pod_id;
                if (!empty($_FILES['attachments']) && !empty($_FILES['attachments']['size'])) {
                    $fileCount = count($_FILES['attachments']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        if (!empty($_FILES['attachments']['name'][$i][$key_image])) {
                            $_FILES['file']['name'] = time() . '_' . $_FILES['attachments']['name'][$i][$key_image];
                            $_FILES['file']['type'] = $_FILES['attachments']['type'][$i][$key_image];
                            $_FILES['file']['tmp_name'] = $_FILES['attachments']['tmp_name'][$i][$key_image];
                            $_FILES['file']['error'] = $_FILES['attachments']['error'][$i][$key_image];
                            $_FILES['file']['size'] = $_FILES['attachments']['size'][$i][$key_image];

                            $config['upload_path'] = $this->upload_path;
                            $config['allowed_types'] = $this->image_types;
                            $config['max_size'] = $this->allowed_file_size;

                            $config['encrypt_name'] = false;
                            $this->upload->initialize($config);
                            if ($this->upload->do_upload('file')) {
                                $uploadData[$key_image][] = $this->upload->file_name;
                            }
                        }
                    }
                }
                if (!empty($uploadData)) {
                    foreach ($uploadData as $k => $v) {
                        $checkQualityItems[$key]['images_multiple'] = implode('||', $v);
                    }
                } else {
                    $checkQualityItems[$key]['images_multiple'] = NULL;
                }
            }
            // print_arrays($checkQualityItems);


            $reference_no = getReference('checkQuality');
            $order_id_text = trim($order_id_text, ',');
            $pod_id_text = trim($pod_id_text, ',');
            $plan_id_text = trim($plan_id_text, ',');
            $fields = [
                'reference_no' => $reference_no,
                'date' => $date,
                'count_items' => $count_items,
                'quantity_qc' => $total_quantity,
                'id_branch' => $id_branch,
                'note' => $note,
                'pod_id' => $pod_id_text,
                'order_id' => $order_id_text,
                'plan_id' => $plan_id_text,
                'created_by' => $this->staffid,
                'date_created' => date('Y-m-d H:i:s'),
            ];
            if (empty($checkQualityItems)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_no_items');
                echo json_encode($data);
                die;
            }

            // print_arrays($checkQualityItems);
            $id = $this->quality_control_model->insertCheckQuality($fields);
            if ($id) {
                if (getReference('checkQuality') == $reference_no) {
                    updateReference('checkQuality');
                }
                foreach ($checkQualityItems as $key => $value) {
                    $value['check_quality_id'] = $id;
                    $data_json_phe = [];
                    $data_json_taiche = [];
                    if (!empty($value['data_json_phe'])) {
                        $data_json_phe = json_decode($value['data_json_phe']);
                    }
                    if (!empty($value['data_json_taiche'])) {
                        $data_json_taiche = json_decode($value['data_json_taiche']);
                    }
                    $check_quality_item_id = $this->quality_control_model->insertCheckQualityItem($value);

                    if ($check_quality_item_id) {
                        if (!empty($data_json_phe)) {
                            foreach ($data_json_phe as $k => $v) {
                                $item_phe = [
                                    'id_check_quality_item' => $check_quality_item_id,
                                    'id_error' => $v->reason_id,
                                    'quantity' => $v->quantity_quote,
                                    'type' => $v->type,
                                ];
                                $this->quality_control_model->insertCheckQualityItemError($item_phe);
                            }
                        }
                        if (!empty($data_json_taiche)) {
                            foreach ($data_json_taiche as $kk => $vv) {
                                $item_che = [
                                    'id_check_quality_item' => $check_quality_item_id,
                                    'id_error' => $vv->reason_id,
                                    'quantity' => $vv->quantity_quote,
                                    'type' => $vv->type,
                                ];
                                $this->quality_control_model->insertCheckQualityItemError($item_che);
                            }
                        }
                    }
                }

                $this->manufactures_model->handlingCheckQualityItemsStage($id);

                $notifiedUsers = [];
                $getAllStaff = get_table_where('tblstaff', ['active' => 1], '', 'result_array');
                $dataHtml = '';
                $arrPod = [];
                foreach ($checkQualityItems as $key => $value) {
                    if ($value['result'] == 2) {
                        notificationQCNotAchieved($id, $this->staffid, $value);
                        $pod = get_table_where('tbl_productions_orders_details', ['id' => $value['pod_id']], '', 'row_array');
                        $stage = get_table_where('tbl_stages', ['id' => $value['id_stage']], '', 'row_array');
                        $dataHtml = 'Sản phẩm ' . $value['item_name'] . ' (' . $pod['reference_no'] . ') Không đạt chất lượng tại công đoạn ' . $stage['name'] . ' cần sản xuất lại! Vui lòng kiểm tra ' . $reference_no;
                        if (!empty($getAllStaff)) {
                            foreach ($getAllStaff as $key => $val) {
                                if (has_permission('quality_control', $val['staffid'], 'notifications')) {
                                    $branchID = get_staff_user_id_branch_app($val['staffid']);
                                    if ($branchID == 1) {
                                        $notification_data = [
                                            'description' => "<a target='_blank' href='" . base_url('admin/manufactures/detail_productions/' . $value['pod_id']) . "'> " . $dataHtml . '</a>',
                                            'touserid' => $val['staffid'],
                                            'link' => '',
                                            'type' => 1,

                                        ];
                                        if (add_notification_app1($notification_data, $this->staffid)) {
                                            array_push($notifiedUsers, $val['staffid']);
                                        }
                                    } else {
                                        if ($id_branch == $branchID) {
                                            $notification_data = [
                                                'description' => "<a target='_blank' href='" . base_url('admin/manufactures/detail_productions/' . $value['pod_id']) . "'> " . $dataHtml . '</a>',
                                                'touserid' => $val['staffid'],
                                                'link' => '',
                                                'type' => 1,
                                            ];
                                            if (add_notification_app1($notification_data, $this->staffid)) {
                                                array_push($notifiedUsers, $val['staffid']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $arrPod[] = $value['pod_id'];
                    }
                }

                pusher_trigger_notification($notifiedUsers);
                //end

                if (!empty($arrPod)) {
                    $pod_id_achieved = implode(',', $arrPod);
                    $po_id = get_po_id($pod_id_achieved);
                    if (!empty($po_id)) {
                        $array = explode(',', $po_id);
                        foreach ($array as $kk => $vv) {
                            $arrPodNew = [];
                            $arrStage = [];
                            $this->db->select('tbl_productions_orders_details.id as pod_id,tbl_check_quality_items.id_stage as id_stage,tbl_productions_orders.id as po_id');
                            $this->db->from('tbl_productions_orders_details');
                            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id');
                            $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.pod_id = tbl_productions_orders_details.id');
                            $this->db->where_in('tbl_productions_orders_details.id', $arrPod);
                            $this->db->where('tbl_productions_orders.id', $vv);
                            $this->db->where('tbl_check_quality_items.check_quality_id', $id);
                            $pods = $this->db->get()->result_array();
                            if (!empty($pods)) {
                                foreach ($pods as $k => $v) {
                                    $arrPodNew[] = $v['pod_id'];
                                    $arrStage[] = $v['id_stage'];
                                }
                            }
                            notificationQCAchieved($id, $this->staffid, $vv, $arrStage, $arrPodNew);
                        }
                    }
                }


                @pusherTNHNotfication();
                $content = lang('tạo mới QC');
                $content = str_replace('{$1}', $reference_no, $content);
                insertActivityLog([
                    'type_parent_obj' => 'check_quality',
                    'table_obj' => 'tbl_check_quality',
                    'id_obj' => $id,
                    'name_obj' => $reference_no,
                    'content' => $content,
                    'actions' => 'add',
                ]);


                $data['result'] = 1;
                $data['message'] = lang('Thêm thành công');
                echo json_encode($data);
            } else {
                // if (!empty($file_old_exist)) {
                //     foreach ($file_old_exist as $k => $v) {
                //         foreach ($v as $kk => $vv){
                //             if (file_exists('uploads/check_quality/'.$vv)) {
                //                 @unlink('uploads/check_quality/'.$vv);
                //             }
                //         }
                //     }
                // }

                $data['result'] = 0;
                $data['message'] = lang('Thêm thất bại');
                echo json_encode($data);
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Không có dữ liệu');
            echo json_encode($data);
        }
    }

    public function scanQRProduction()
    {
        $data = [];
        $dataPost = file_get_contents('php://input');
        $code_production = '';
        if ($dataPost) {
            if (!is_array($dataPost)) {
                $dataPost = (array)json_decode($dataPost);
                if (!empty($dataPost['code_production'])) {
                    $code_production = $dataPost['code_production'];
                }
            }
        }
        $product = [];
        $order_item_id = '';
        if (!empty($code_production)) {
            $code_check = explode('-', $code_production);
            $code_check_value = '';
            $order_item_id = $code_check[0];
            if (!empty($code_check)) {
                foreach ($code_check as $k => $v) {
                    if ($k != 0) {
                        $code_check_value .= $v . '-';
                    }
                }
            }
            $code_check_value = trim($code_check_value, '-');
            $product_check = get_table_where('tbl_products', ['code' => $code_check_value], '', 'row_array');
            if (!empty($order_item_id)) {
                $qtQC = '
                COALESCE(
                (SELECT SUM(tbl_productions_orders_details.qty_qc) ),0)
                ';
                $this->db->select(
                    '
                CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
                CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
                tbl_products.name as name,
                tbl_products.code as code,
                tbl_products.unit_id as unit_id,
                tblunits.unit as unit_name,
                tbl_products.price_import as price_import,
                tbl_products.images as images,
                CONCAT(tbl_orders.reference_no) as reference_no,
                CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
                tbl_productions_orders_details.reference_no as reference_no_production_detail,
                COALESCE(SUM(tbl_productions_orders_items.quantity),0) as total_qty,
                ' . $qtQC . ' as qty_qc,
                tbl_productions_orders_details.id as pod_id,
                tbl_colors.name as name_color,
                tbl_productions_orders_details.object_type as object_type,
                tbl_business_plan.id as plan_id,
                tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
                tbl_orders.id as idd',
                    false
                );

                $this->db->from('tbl_productions_orders_details');
                $this->db->join(
                    'tbl_productions_orders_items',
                    'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
                    'left'
                );
                $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id ', 'left');
                $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
                $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
                $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
                $this->db->join(
                    'tbl_products',
                    'tbl_products.id = tbl_productions_orders_items.items_id ',
                    'left'
                );
                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
                $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
                $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id', 'left');
                $this->db->where('tbl_productions_orders_items.object_item_type', 'orders');
                $this->db->where('tbl_productions_orders_items.production_plan_item_id', $order_item_id);
                $this->db->order_by('tbl_products.name', 'DESC');
                $this->db->group_by('tbl_productions_orders_details.id');
                $product = $this->db->get()->result_array();
                if (!empty($product)) {
                    foreach ($product as $key => $value) {
                        $items = [];
                        $item_id = explode('__', $value['id']);
                        $item_id = $item_id[1];
                        $type_item = $item_id[0];
                        $images = '';
                        $info = $this->products_model->rowProduct($item_id);
                        if (!empty($info)) {
                            $images = base_url('uploads/products/' . $info['images']);
                        }
                        if (empty($images)) {
                            $images = base_url('assets/images/tnh/no_image.png');
                        }
                        $this->db->select('*');
                        $this->db->from('tbl_product_stages');
                        $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                        $this->db->where('tbl_product_stages.product_id', $item_id);
                        $stages =  $this->db->get()->row_array();
                        if (!empty($stages)) {
                            $productions_orders_item_id = $value['productions_orders_item_id'];
                            $stageProduction = "(
                                SELECT active
                                FROM tbl_productions_orders_items_stages
                                WHERE tbl_productions_orders_items_stages.stage_id = tbl_stages.id AND tbl_productions_orders_items_stages.productions_orders_items_id = $productions_orders_item_id
                                LIMIT 1
                            )";
                            $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                            $this->db->from('tbl_product_stages_versions');
                            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                            $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                            $this->db->where('tbl_stages.status_qc', 1);
                            $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                            $items = $this->db->get()->result_array();
                        }
                        $product[$key]['stages'] = $items;
                        $product[$key]['images'] = $images;
                    }
                }
                $data['result'] = $product;
            } else {
                $data['result'] = [];
            }
        } else {
            $data['result'] = [];
        }

        echo json_encode($data);
    }

    public function createQCToNotification()
    {
        $data = [];
        $dataPost = file_get_contents('php://input');
        $json_data = '';
        $id_noti = '';
        if ($dataPost) {
            if (!is_array($dataPost)) {
                $dataPost = (array)json_decode($dataPost);
                if (!empty($dataPost['json_data'])) {
                    $json_data = $dataPost['json_data'];
                }
                if (!empty($dataPost['id_noti'])) {
                    $id_noti = $dataPost['id_noti'];
                }
            }
        }
        if (!empty($id_noti)) {
            $noti = get_table_where('tblnotification_app', ['notificationID' => $id_noti], '', 'row_array');
            if (!empty($noti)) {
                $json_data = $noti['json_data'];
            }
        }
        $product = [];
        $cKey = 0;
        if (!empty($json_data)) {
            $json_data = (array) json_decode($json_data);
            // $json_data = (array)($json_data);
            $arrPOIS = $json_data['arrPOIS'];
            $arrCQIS = $json_data['arrCQIS'];

            $pois_id = $arrPOIS[0];
            $this->db->select('
                tbl_productions_orders_items_stages.id as id,
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                tbl_productions_orders_items_stages.stage_id as stage_id,
                tbl_productions_orders_items_stages.number as number,
                tbl_productions_orders.location_id,
                tblbranch.name as name_branch,
            ');
            $this->db->from('tbl_productions_orders_items_stages');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_productions_orders.location_id');
            $this->db->where('tbl_productions_orders_items_stages.id', $pois_id);
            $pois = $this->db->get()->row_array();
            if (!empty($pois)) {
                $data['branch_defaulr'] = [
                    'branch_id' => $pois['location_id'],
                    'name_branch' => $pois['name_branch'],
                ];
            }
            if (!empty($arrPOIS)) {
                $qtQC = '
                COALESCE(
                (SELECT SUM(tbl_productions_orders_details.qty_qc) ),0)
                ';
                $this->db->select(
                    '
                0 as cqis_id,
                CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
                CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
                tbl_products.name as name,
                tbl_products.code as code,
                tbl_products.unit_id as unit_id,
                tblunits.unit as unit_name,
                tbl_products.price_import as price_import,
                tbl_products.images as images,
                CONCAT(tbl_orders.reference_no) as reference_no,
                CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
                tbl_productions_orders_details.reference_no as reference_no_production_detail,
                COALESCE(SUM(tbl_productions_orders_items.quantity),0) as total_qty,
                ' . $qtQC . ' as qty_qc,
                tbl_productions_orders_details.id as pod_id,
                tbl_colors.name as name_color,
                tbl_productions_orders_details.object_type as object_type,
                tbl_business_plan.id as plan_id,
                tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
                tbl_productions_orders_items_stages.number as number,
                tbl_productions_orders_items_stages.stage_id as stage_id,
                tbl_orders.id as idd',
                    false
                );

                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->join(
                    'tbl_productions_orders_details',
                    'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
                    'left'
                );

                $this->db->join(
                    'tbl_productions_orders_items',
                    'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
                    'left'
                );
                $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id ', 'left');
                $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
                $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
                $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
                $this->db->join(
                    'tbl_products',
                    'tbl_products.id = tbl_productions_orders_items.items_id ',
                    'left'
                );
                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
                $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
                $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id', 'left');
                $this->db->where_in('tbl_productions_orders_items_stages.id', $arrPOIS);

                $this->db->order_by('tbl_products.name', 'DESC');
                $this->db->group_by('tbl_productions_orders_details.id');
                $product = $this->db->get()->result_array();
                $arrQcAchieved = [];
                if (!empty($product)) {
                    foreach ($product as $key => $value) {
                        //check qc
                        $checkQc = get_table_where('tbl_check_quality_items',['pod_id'=>$value['pod_id'],'id_stage'=>$value['stage_id']],'','row_array');
                        if(!empty($checkQc)){
                            $arrQcAchieved []=[
                                'pod' => $value['reference_no_production_detail'],
                                'stage_id' => $value['stage_id'],
                                'stage_name' => $json_data['stage_name'],
                            ];
                        }
                        //end
                        $items = [];

                        $stage_item = get_table_where('tbl_productions_orders_items_stages',['productions_orders_items_id'=>$value['productions_orders_item_id'],'number'=>($value['number'])],'','row_array');

                        $this->db->select('tbl_check_quality_items_stage.id, tbl_check_quality_items_stage.status_result');
                        $this->db->from('tbl_check_quality_items_stage');
                        $this->db->where('tbl_check_quality_items_stage.pois_id', $stage_item['id']);
                        $this->db->where('tbl_check_quality_items_stage.number', $stage_item['number']);
                        $this->db->order_by('tbl_check_quality_items_stage.id DESC');
                        $dtCQIS = $this->db->get()->row_array();
                        if (!empty($dtCQIS)) {
                            if ($dtCQIS['status_result'] != 0) {
                                continue;
                            } else {
                                $cqis_id = $dtCQIS['id'];
                                $this->db->select('
                                    tbl_check_quality_items_stage.id as cqis_id,
                                    CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
                                    CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
                                    tbl_products.name as name,
                                    tbl_products.code as code,
                                    tbl_products.unit_id as unit_id,
                                    tblunits.unit as unit_name,
                                    tbl_products.price_import as price_import,
                                    tbl_products.images as images,
                                    CONCAT(tbl_orders.reference_no) as reference_no,
                                    CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
                                    tbl_productions_orders_details.reference_no as reference_no_production_detail,
                                    tbl_check_quality_items.quantity_recycling as total_qty,
                                    tbl_check_quality_items.quantity_recycling as qty_qc,
                                    tbl_productions_orders_details.id as pod_id,
                                    tbl_colors.name as name_color,
                                    tbl_productions_orders_details.object_type as object_type,
                                    tbl_business_plan.id as plan_id,
                                    tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
                                    tbl_productions_orders_items_stages.number as number,
                                    tbl_orders.id as idd,
                                    tbl_productions_orders.location_id as location_id,
                                ', false);
                                $this->db->from('tbl_check_quality_items_stage');
                                $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_check_quality_items_stage.po_id');
                                $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.id = tbl_check_quality_items_stage.check_quality_items_id');
                                $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_check_quality_items_stage.pois_id');
                                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id', 'inner');
                                $this->db->join('tbl_productions_orders_items',
                                'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
                                $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
                                $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
                                $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
                                $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id ', 'inner');
                                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
                                $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
                                $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id AND tbl_products_colors.color_id != 0', 'left');
                                $this->db->order_by('tbl_products.name', 'DESC');
                                $this->db->group_by('tbl_productions_orders_details.id');
                                $this->db->where('tbl_check_quality_items_stage.id', $cqis_id);
                                $product_return = $this->db->get()->row_array();
                                if (!empty($product_return)) {
                                    $product[$cKey] = $product_return;
                                }
                            }
                        }

                        $item_id = explode('__', $value['id']);
                        $item_id = $item_id[1];
                        $type_item = $item_id[0];
                        $images = '';
                        $info = $this->products_model->rowProduct($item_id);
                        if (!empty($info)) {
                            $images = base_url('uploads/products/' . $info['images']);
                        }
                        if (empty($images)) {
                            $images = base_url('assets/images/tnh/no_image.png');
                        }
                        $this->db->select('*');
                        $this->db->from('tbl_product_stages');
                        $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                        $this->db->where('tbl_product_stages.product_id', $item_id);
                        $stages =  $this->db->get()->row_array();
                        if (!empty($stages)) {
                            $productions_orders_item_id = $value['productions_orders_item_id'];
                            $stageProduction = "(
                                SELECT active
                                FROM tbl_productions_orders_items_stages
                                WHERE tbl_productions_orders_items_stages.stage_id = tbl_stages.id AND tbl_productions_orders_items_stages.productions_orders_items_id = $productions_orders_item_id
                                LIMIT 1
                            )";
                            $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                            $this->db->from('tbl_product_stages_versions');
                            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                            $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                            $this->db->where('tbl_stages.status_qc', 1);
                            $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                            $items = $this->db->get()->result_array();

                            $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                            $this->db->from('tbl_product_stages_versions');
                            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                            $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                            $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                            $items_stage = $this->db->get()->result_array();
                        }

                        $product[$cKey]['stages_default'] = [
                            'id' => $value['stage_id'],
                            'name' => $json_data['stage_name']
                        ];
                        $product[$cKey]['stages'] = $items;
                        $product[$cKey]['stages_again'] = $items_stage;
                        $product[$cKey]['images'] = $images;
                        $cKey++;
                    }
                }
            }

            if (!empty($arrCQIS)) {
                foreach ($arrCQIS as $kCQIS => $vCQIS) {
                    $this->db->select('tbl_check_quality_items_stage.check_quality_id, tbl_check_quality_items_stage.check_quality_items_id, tbl_check_quality_items_stage.number');
                    $this->db->from('tbl_check_quality_items_stage');
                    $this->db->where('tbl_check_quality_items_stage.id', $vCQIS);
                    $dtCQIS = $this->db->get()->row_array();
    
                    $this->db->select('tbl_check_quality_items_stage.id');
                    $this->db->from('tbl_check_quality_items_stage');
                    $this->db->where('tbl_check_quality_items_stage.check_quality_items_id', $dtCQIS['check_quality_items_id']);
                    // $this->db->where('tbl_check_quality_items_stage.number', $dtCQIS['number'] - 1);
                    $this->db->where('tbl_check_quality_items_stage.number', $dtCQIS['number']);
                    $dtCQISPre = $this->db->get()->row_array();
                    $cqis_id = $dtCQISPre['id'];
    
                    $this->db->select('
                        tbl_check_quality_items_stage.id as cqis_id,
                        CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
                        CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
                        tbl_products.name as name,
                        tbl_products.code as code,
                        tbl_products.unit_id as unit_id,
                        tblunits.unit as unit_name,
                        tbl_products.price_import as price_import,
                        tbl_products.images as images,
                        CONCAT(tbl_orders.reference_no) as reference_no,
                        CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
                        tbl_productions_orders_details.reference_no as reference_no_production_detail,
                        tbl_check_quality_items.quantity_recycling as total_qty,
                        tbl_check_quality_items.quantity_recycling as qty_qc,
                        tbl_productions_orders_details.id as pod_id,
                        tbl_colors.name as name_color,
                        tbl_productions_orders_details.object_type as object_type,
                        tbl_business_plan.id as plan_id,
                        tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
                        tbl_productions_orders_items_stages.number as number,
                        tbl_orders.id as idd,
                        tbl_productions_orders.location_id as location_id,
                        tblbranch.name as branch_name,
                    ', false);
                    $this->db->from('tbl_check_quality_items_stage');
                    $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_check_quality_items_stage.po_id');
                    $this->db->join('tblbranch', 'tblbranch.id = tbl_productions_orders.location_id');
                    $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.id = tbl_check_quality_items_stage.check_quality_items_id');
                    $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_check_quality_items_stage.pois_id');
                    $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id', 'inner');
                    $this->db->join('tbl_productions_orders_items',
                    'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
                    $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
                    $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
                    $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
                    $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id ', 'inner');
                    $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
                    $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
                    $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id AND tbl_products_colors.color_id != 0', 'left');
                    $this->db->order_by('tbl_products.name', 'DESC');
                    $this->db->group_by('tbl_productions_orders_details.id');
                    $this->db->where('tbl_check_quality_items_stage.id', $cqis_id);
                    $product_return = $this->db->get()->result_array();
                    if (!empty($product_return)) {
                        foreach ($product_return as $kP => $vP) {
                            $temp = $vP;
                            if (empty($data['branch_defaulr'])) {
                                $data['branch_defaulr'] = [
                                    'branch_id' => $vP['location_id'],
                                    'name_branch' => $vP['branch_name'],
                                ];
                                // $data['location_id'] = $vP['location_id'];
                            }
    
                            $items = [];
                            $item_id = explode('__', $vP['id']);
                            $item_id = $item_id[1];
                            $type_item = $item_id[0];
                            $info = $this->products_model->rowProduct($item_id);
                            $this->db->select('*');
                            $this->db->from('tbl_product_stages');
                            $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                            $this->db->where('tbl_product_stages.product_id', $item_id);
                            $stages =  $this->db->get()->row_array();
                            if(!empty($stages)){
                                $productions_orders_item_id = $vP['productions_orders_item_id'];
                                $stageProduction = "(
                                    SELECT active
                                    FROM tbl_productions_orders_items_stages
                                    WHERE tbl_productions_orders_items_stages.stage_id = tbl_stages.id AND tbl_productions_orders_items_stages.productions_orders_items_id = $productions_orders_item_id
                                    LIMIT 1
                                )";
                                $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,'.$stageProduction.' as active');
                                $this->db->from('tbl_product_stages_versions');
                                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                                $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                                $this->db->where('tbl_stages.status_qc', 1);
                                $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                                $items = $this->db->get()->result_array();
        
                                $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,'.$stageProduction.' as active');
                                $this->db->from('tbl_product_stages_versions');
                                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                                $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                                $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                                $items_stage = $this->db->get()->result_array();
                            }
    
                            $stage_item = get_table_where('tbl_productions_orders_items_stages',['productions_orders_items_id'=>$vP['productions_orders_item_id'],'number'=>($vP['number'])],'','row_array');
                            if(!empty($stage_item)){
                                // $temp['stages_default'] = $stage_item['stage_id'];
                                $dtStage = get_table_where('tbl_stages', ['id' => $stage_item['stage_id']], '', 'row_array', '', 'name');
                                $temp['stages_default'] = [
                                    'id' => $stage_item['stage_id'],
                                    'name' => $dtStage['name']
                                ];
                            } else {
                                $temp['stages_default'] = [
                                    'id' => 0,
                                    'name' => ''
                                ];
                                // $temp['stages_default'] = 0;
                            }
                            $temp['stages'] = $items;
                            
                            $temp['stages_again'] = $items_stage;
                            $product[$cKey] = $temp;
                            $cKey++;
                        }
                    }
                }
            }
        } else {
            $data['result'] = [];
            $data['message'] = 'Không co dữ liệu';
        }

        if (!empty($product)) {
            $data['result'] = $product;
            $data['arrQcAchieved'] = $arrQcAchieved;
            $data['message'] = 'Danh sách sản phẩm';
        }

        echo json_encode($data);
    }

    public function getStageAgain($stage_id = false)
    {
        $data = [];
        $result = [];
        if (!empty($stage_id)) {
            $stage = get_table_where('tbl_stages', ['id' => $stage_id], '', 'row_array');
            if (!empty($stage)) {
                $stage_again = get_table_where('tbl_stages', ['id' => $stage['stage_again']], '', 'row_array');
                if (!empty($stage_again)) {
                    $result = [
                        'id' => $stage_again['id'],
                        'name' => $stage_again['name'],
                    ];
                }
            }
            $data['result'] = $result;
        } else {
            $data['result'] = [];
            $data['message'] = 'Không có dữ liệu';
        }
        echo json_encode($data);
        die;
    }
}