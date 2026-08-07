<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Outsource extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('orders_model');
        $this->load->model('quotes_orders_model');
        $this->load->model('manufactures_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('deliveries_model');
        $this->load->model('outsource_model');
        $this->load->model('transfer_model');
        $this->load->model('export_different_model');
        $this->load->model('manufactures_model');
        $this->tnh = true;

        $this->perViewOutsource= has_permission('outsource', '', 'view');
        $this->perViewOwnOutsource = has_permission('outsource', '', 'view_own');
        $this->perAddOutsource = has_permission('outsource', '', 'create');
        $this->perEditOutsource = has_permission('outsource', '', 'edit');
        $this->perApproveOutsource = has_permission('outsource', '', 'approve');
        $this->perDeleteOutsource = has_permission('outsource', '', 'delete');
        $this->perDeleteExportOutsource= has_permission('outsource', '', 'export_outsource');
        $this->perPrintOutsource = has_permission('outsource', '', 'print');

        $this->perViewImportOutsource = has_permission('import_outsource', '', 'view');
        $this->perViewOwnImportOutsource= has_permission('import_outsource', '', 'view_own');
        $this->perAddImportOutsource = has_permission('import_outsource', '', 'create');
        $this->perDeleteImportOutsource = has_permission('import_outsource', '', 'delete');
        $this->perApproveImportOutsource = has_permission('import_outsource', '', 'approve_warehouse');
        $this->branchID = get_staff_user_id_branch();
        $this->isAdmin = is_admin();

    }

    public function index()
    {
        if (!$this->perViewOutsource && !$this->perViewOwnOutsource) {
            accessDenied();
        }
        $data['tnh'] = $this->tnh;
        $data['title'] = lang('outsource');

        $data['un_approved'] = $this->outsource_model->countOutsourceByStatus('un_approved');
        $data['approved'] = $this->outsource_model->countOutsourceByStatus('approved');
        $data['invoice_status_not_paid_completely'] = $this->outsource_model->countOutsourceByStatus('invoice_status_not_paid_completely');
        $data['invoice_status_unpaid'] = $this->outsource_model->countOutsourceByStatus('invoice_status_unpaid');
        $data['invoice_status_paid'] = $this->outsource_model->countOutsourceByStatus('invoice_status_paid');

        $this->db->select('tblsuppliers.*');
        $this->db->join('tblsuppliers','tblsuppliers.id = tblpurchase_order.suppliers_id');
        $this->db->group_by('tblsuppliers.id');
        $data['suppliers'] = $this->db->get('tblpurchase_order')->result_array();

        $data['all'] = $this->outsource_model->countOutsourceByStatus('all');

        $this->load->view('admin/outsource/index', $data);
    }

    public function getOutsource()
    {
        $status_table = $this->input->post('status_table');
        $suppliers_id = $this->input->post('suppliers_id');
        $supplier_search = $this->input->post('supplier_search');
        $orders_search = $this->input->post('orders_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $arrIDStaff = employee_manage_staff();

        $referenceOrder = "(
            SELECT
                GROUP_CONCAT(tbl_orders.reference_no SEPARATOR '</br>')
            FROM tbl_orders_outsource
            INNER JOIN tbl_orders ON tbl_orders_outsource.order_id = tbl_orders.id
            WHERE tbl_orders_outsource.outsource_id = tbl_outsource.id
        )";

        $referenceImportOutsource = "(
            SELECT
                GROUP_CONCAT(CONCAT(tbl_import_outsource.reference_no,'__',tbl_import_outsource.id) SEPARATOR '||')
            FROM tbl_import_outsource
            WHERE tbl_import_outsource.outsource_id = tbl_outsource.id
        )";
        $referenceExportDifferent = "(
            SELECT
                GROUP_CONCAT(CONCAT(tblexport_different.prefix, '', tblexport_different.code,'__',tblexport_different.id) SEPARATOR '||')
            FROM tblexport_different
            WHERE tblexport_different.id_outsource = tbl_outsource.id
        )";

        $custom[] = ['index' => 4, 'select' => 'reference_orders'];
        $custom_select[4] = $referenceOrder;

        $custom[] = ['index' => 16, 'select' => 'import_outsource'];
        $custom_select[16] = $referenceImportOutsource;

        $this->datatables->select("
            tbl_outsource.id as id,
            tbl_outsource.date as date,
            CONCAT(tbl_outsource.reference_no, '||',tblbranch.name) as reference_no,
            tblsuppliers.company as supplier_company,
            tblwarehouse.name as warehouses,
            tbl_outsource.grand_total as grand_total,
            tbl_outsource.amount_paid as amount_paid,
            (tbl_outsource.grand_total - tbl_outsource.amount_paid) as rest,
            tbl_outsource.status_pay as status_pay,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as created_by,
            tbl_outsource.status as status,
            tbl_outsource.workflow as workflow,
            CONCAT(tbltransfer_warehouse.prefix, '', tbltransfer_warehouse.code) as export_outsource,
            $referenceImportOutsource as import_outsource,
            $referenceExportDifferent as export_different,
            tbl_outsource.note as note,
            CONCAT(staff_status.firstname, ' ', staff_status.lastname, '') as user_status,
            tbl_outsource.pod_id as pod_id,
        ", FALSE)

            ->from('tbl_outsource')
            ->join('tbltransfer_warehouse', 'tbltransfer_warehouse.id = tbl_outsource.tranfer_id', 'left')
            ->join('tblexport_different', 'tblexport_different.id = tbl_outsource.id_export_different', 'left')
            ->join('tblsuppliers', 'tblsuppliers.id = tbl_outsource.supplier_id', 'left')
            ->join('tblwarehouse', 'tblwarehouse.id = tbl_outsource.warehouse_id', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_outsource.created_by', 'left')
            ->join('tblstaff employees', 'employees.staffid = tbl_outsource.employee_id', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_outsource.id_branch', 'left')
            ->join('tblstaff staff_status', 'staff_status.staffid = tbl_outsource.user_status', 'left');

        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        if (!empty($status_table) && $status_table != 'all')
        {
            if ($status_table == "un_approved") {
                $this->datatables->where('tbl_outsource.status', 'un_approved');
            } elseif ($status_table == "approved") {
                $this->datatables->where('tbl_outsource.status', 'approved');
            } elseif ($status_table == "invoice_status_unpaid") {
                $this->datatables->where('tbl_outsource.status_pay', 0);
            } elseif ($status_table == "invoice_status_not_paid_completely") {
                $this->datatables->where('tbl_outsource.status_pay', 1);
            } elseif ($status_table == "invoice_status_paid") {
                $this->datatables->where('tbl_outsource.status_pay', 2);
            }
        }

        if (!$this->perViewOutsource) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                $this->datatables->where('tbl_outsource.created_by IN (' . $coverStr . ')');
            }
        } else {
            if (!$this->isAdmin) {
                if ($this->branchID == 1) {
                    if ($arrIDStaff) {
                        $coverStr = implode(",", $arrIDStaff);
                        $this->db->where('(tbl_outsource.created_by IN (' . $coverStr . ')  OR tbl_outsource.id_branch != -1 )');
                    }
                } else {
                    if ($arrIDStaff) {
                        $coverStr = implode(",", $arrIDStaff);
                        $this->db->where('(tbl_outsource.created_by IN (' . $coverStr . ')  OR tbl_outsource.id_branch = ' . $this->branchID . ')');
                    }
                }
            }
        }
        if (!empty($suppliers_id))
        {
            $this->datatables->where('tbl_outsource.supplier_id', $suppliers_id);
            $this->datatables->where('tbl_outsource.status', 'approved');
            $this->datatables->where('tbl_outsource.status_pay !=', 2);
        }
        if ($orders_search) {
            $isOrder = "(
                SELECT
                tbl_outsource_items.id as id
                FROM tbl_outsource_items
                INNER JOIN tbl_orders ON tbl_orders.id = tbl_outsource_items.order_id AND tbl_outsource_items.object_type = 'orders' 
                WHERE tbl_outsource_items.outsource_id = tbl_outsource.id AND tbl_orders.id = $orders_search
            )";
            $this->datatables->where("EXISTS $isOrder");
        }
        if ($supplier_search) {
            $this->datatables->where('tbl_outsource.supplier_id', $supplier_search);
        }
        if (!empty($start_date_search)) {
            $this->datatables->where('tbl_outsource.date >=', to_sql_date($start_date_search).' 00:00:00');
        }

        if (!empty($end_date_search)) {
            $this->datatables->where('tbl_outsource.date <=', to_sql_date($end_date_search).' 23:59:59');
        }
        $view = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/outsource/view_outsource/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> '.lang('view').'</a>';

        $edit =  $this->perEditOutsource ? '<a href="'.base_url('admin/outsource/edit/$1').'"><i class="fa fa-edit"></i> '.lang('edit').'</a>' : '';
        $print = '<a href="'.base_url('admin/releases/print_delivery/$1').'" target="_blank"><i class="fa fa-print"></i> '.lang('print').' '.lang('deliveries').'</a>';

        // $export_outsource = '<a class="export-outsource" onClick="exportOutsource(this)" value="$1" href="javascript:void(0)"><i class="fa fa-exchange"></i> '.lang('Xuất NVL/BTP gia công').'</a>';
        $export_outsource = $this->perDeleteExportOutsource ? '<a data-tnh="modal" class="tnh-modal export-outsource" href="' . base_url('admin/outsource/exportOutsource/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-sign-in"></i> ' . lang('Xuất NVL/BTP') . '</a>': '';

        $import_outsource = $this->perAddImportOutsource ? '<a data-tnh="modal" class="tnh-modal tnh-import-outsource" href="'.base_url('admin/outsource/import_outsource/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-sign-in"></i> '.lang('tnh_import_outsource').'</a>' : '';

        $delete = $this->perDeleteOutsource ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\''.base_url('admin/outsource/delete/$1').'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
            <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
        "><i class="fa fa-remove width-icon-actions"></i> '.lang('delete').'</a>' : '';

        $paymentProcessing = $this->perAddOutsource ? '<a data-tnh="modal" class="tnh-modal tnh-payment-processing" href="'.base_url('admin/outsource/payment_processing/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-money"></i> '.lang('Thanh toán').'</a>' : '';

        $print = $this->perPrintOutsource ? '<a href="'.base_url('admin/outsource/print_outsource/$1').'" target="_blank"><i class="fa fa-print"></i> '.lang('print_vote').'</a>': '';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            '.lang('actions').'
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>'.$view.'</li>
                <li>'.$edit.'</li>
                <li>'.$print.'</li>
                <li>'.$export_outsource.'</li>
                <li>'.$import_outsource.'</li>
                <li>'.$paymentProcessing.'</li>
                <li class="not-outside">'.$delete.'</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $data = json_decode($this->datatables->generate());
        foreach ($data->aaData as $key => $value) {
            $exportDifferent = $value[14];
            $arrExport = explode('||',$exportDifferent);

            $importOutsource = $value[13];
            $arrImport = explode('||',$importOutsource);

            $pod_id = $value[17];

            $labelOptionsImport = '';
            $codeImport = '';
            $countImport= 0;

            $labelOptions = '';
            $codeExport = '';
            $countPod = 0;

            $countPo = 0;
            $labelOptionsPo = '';
            if (!empty($arrExport)) {
                foreach ($arrExport as $k => $v){
                    if(!empty($v)){
                        $countPod ++;
                        $arr = explode('__', $v);
                        $codeExport .=$arr[0].'<br>';
                    }
                }
            }
            if($countPod > 0){
                $labelOptions = '<div>  <a data-tnh="modal" class="tnh-modal" href="' .base_url().'admin/outsource/view_export_different/' .$value[0].'" data-toggle="modal" data-target="#myModal"><span data-toggle="tooltip" data-html="true" title="'.$codeExport.'" class="label label-primary pointer">'.lang('PXGC').' ['.$countPod.'] '.lang('phiếu').'</span></a></div>';
            }

            if (!empty($arrImport)) {
                foreach ($arrImport as $k => $v){
                    if(!empty($v)){
                        $countImport ++;
                        $arr = explode('__', $v);
                        $codeImport .=$arr[0].'<br>';
                    }
                }
            }
            if($countImport > 0){
                $labelOptionsImport = '<div>  <a data-tnh="modal" class="tnh-modal" href="' .base_url().'admin/outsource/view_import_outsource_ajax/' .$value[0].'" data-toggle="modal" data-target="#myModal"><span data-toggle="tooltip" data-html="true" title="'.$codeImport.'" class="label label-warning pointer">'.lang('PNGC').' ['.$countImport.'] '.lang('phiếu').'</span></a></div>';
            }


            $data->aaData[$key][13] = $labelOptionsImport;
            $data->aaData[$key][14] = $labelOptions;

            if (!empty($pod_id)) {
                $countPo = count_po($pod_id);
            }
            if($countPo > 0){
                // $labelOptionsPo = '<div><span data-toggle="tooltip" data-html="true" title="'.get_po($pod_id).'" class="label label-warning pointer">'.lang('Lệnh SX tổng').' ['.$countPo.'] '.lang('tnh_single').'</span></div>';
                $labelOptionsPo = get_po_new($pod_id);
            }
            $data->aaData[$key][17] = $labelOptionsPo;

        }
        echo json_encode($data);
    }
    public function view_import_outsource_ajax($id) {
        $data['id'] = $id;
        $this->load->view('admin/outsource/view_import_outsource_ajax', $data);
    }
    public function getImportOutsourceAjax()
    {
        $view_outsource_id = $this->input->post('view_outsource_id');
        $this->datatables->select("
            tbl_import_outsource.id,
            tbl_import_outsource.date,
            tbl_import_outsource.reference_no as code,
            tbl_import_outsource.status,
            tbl_import_outsource.warehouseman_id,
            tblwarehouse.name as warehouse,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as staff_id,
        ", FALSE)

        ->from('tbl_import_outsource')
        ->join('tblstaff', 'tblstaff.staffid = tbl_import_outsource.created_by', 'left')
        ->join('tblwarehouse', 'tblwarehouse.id = tbl_import_outsource.warehouse_to', 'left');

        $this->datatables->where('tbl_import_outsource.outsource_id',$view_outsource_id);
        $styleDelete = '';

        $delete = '<div class="text-center "><span class="btn btn-danger fa fa-trash" onclick="deleteImport(this,$1)" style="'.$styleDelete.'"></span></div>';

        $this->datatables->add_column('actions', $delete, 'tbl_import_outsource.id');
        $data = json_decode($this->datatables->generate());
        foreach ($data->aaData as $key => $value) {
        }
        echo json_encode($data);
    }
    public function loadItemsExportOutsource() {
        $export_outsource = $this->input->get('export_outsource');
        $data['export_outsource'] = $export_outsource;
        $this->load->view('admin/outsource/load_items_export_outsource', $data);
    }

    public function loadItemsImportOutsource() {
        $import_outsource = $this->input->get('import_outsource');
        $data['import_outsource'] = $import_outsource;
        $this->load->view('admin/outsource/load_items_import_outsource', $data);
    }
    
    public function view_export_different($id) {
        $data['id'] = $id;
        $this->load->view('admin/outsource/view_export_different', $data);
    }
    public function getExportOutsource()
    {
        $view_outsource_id = $this->input->post('view_outsource_id');
        $this->datatables->select("
            tblexport_different.id,
            tblexport_different.date,
            CONCAT(tblexport_different.prefix, ' ', tblexport_different.code, '') as code,
            tblexport_different.object,
            tblexport_different.id_object,
            tblexport_different.status,
            tblexport_different.warehouseman_id,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as staff_id,
            tblexport_different.object_text,
        ", FALSE)

        ->from('tblexport_different')
        ->join('tblstaff', 'tblstaff.staffid = tblexport_different.staff_id', 'left');

        $this->datatables->where('tblexport_different.id_outsource',$view_outsource_id);
        $styleDelete = '';

        $delete = '<div class="text-center "><span class="btn btn-danger fa fa-trash" onclick="deleteExportOutsource(this,$1)" style="'.$styleDelete.'"></span></div>';

        $this->datatables->add_column('actions', $delete, 'tblexport_different.id');
        $data = json_decode($this->datatables->generate());
        foreach ($data->aaData as $key => $value) {
            $_data = '';
            if($value[3] == 2){
                $supplier = get_table_where('tblsuppliers',array('id'=>$value[4]),'','row');
                $_data = $supplier->company;
            } elseif($value[3] ==1){
                $client = get_table_where('tblclients',array('userid'=>$value[4]),'','row');
                $_data = $client->company;
            } elseif($value[3] ==3){
                $_data = get_staff_full_name($value[4]);
            } elseif($value[3] ==4){
                $_data = $value[8];
            }
            $data->aaData[$key][1] = _dhau($value[1]);
            $data->aaData[$key][4] = $_data;
        }
        echo json_encode($data);
    }

    public function add()
    {
        if (!$this->perAddOutsource) {
            accessDenied();
        }
        if ($this->input->post('add'))
        {
            $data = [];
            $this->form_validation->set_rules('reference_no', lang("tnh_reference_outsource"), 'trim|required|is_unique[tbl_outsource.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('supplies', lang("tnh_supplies"), 'required');
            // $this->form_validation->set_rules('warehouses', lang("tnh_warehouses"), 'required');
            $this->form_validation->set_rules('id_branch', lang("Chi nhánh xưởng"), 'required');
            if ($this->form_validation->run() == true)
            {
                // print_arrays($this->input->post());
                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $supplier_id = $this->input->post('supplies');
                $warehouse_id = $this->input->post('warehouses');
                $id_branch = $this->input->post('id_branch');
                $note = $this->input->post('note');
                $status = 'un_approved';

                $totalAll = 0;
                $totalQuantityAll = 0;
                $grandTotalAll = 0;
                $countItemsAll = 0;

    
                $counter = $this->input->post('counter');
                $order_id_text = '';
                $pod_id_text = '';
                $plan_id_text = '';
                foreach ($counter as $key => $value) {
                    $items_id = $this->input->post('item_id')[$value];
                    if (empty($items_id)) continue;
                    $arrs = explode('__', $items_id);
                    $item_id = $arrs[1];
                    $type_item = $arrs[0];

                    if ($type_item == "products" || $type_item == "semi_products") {
                        $info = $this->products_model->rowProduct($item_id);
                    } else if ($type_item == "items") {
                        $info = $this->items_model->rowItems($item_id);
                    }
                    if (empty($info)) {
                        continue;
                    }

                    $items_code = $info['code'];
                    $items_name = $info['name'];
                    $quantity_outsource = number_unformat($this->input->post('quantity_outsource')[$value]);
                    $quantity = $quantity_outsource;
                    $note_item = $this->input->post('note_item')[$value];
                    $pod_id = $this->input->post('pod_id')[$value];
                    $id_stage = $this->input->post('id_stage')[$value];
                    $object_type = $this->input->post('object_type')[$value];
                    $order_id = $this->input->post('order_id')[$value];
                    $plan_id = $this->input->post('plan_id')[$value];

                    $price = number_unformat($this->input->post('price_outsource')[$value]);
                    $amount = $quantity * $price;

                    $items_in[] = [
                        'order_item_id' => 0,
                        'object_type' => $object_type,
                        'pod_id' => $pod_id,
                        'order_id' => $order_id,
                        'plan_id' => $plan_id,
                        'id_stage' => $id_stage,
                        'type_item' => $type_item,
                        'item_id' => $item_id,
                        'item_code' => $items_code,
                        'item_name' => $items_name,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'note_item' => $note_item,
                    ];

                    $order_id_text .= $order_id.',';
                    $pod_id_text .= $pod_id.',';
                    $plan_id_text .= $plan_id.',';

                    $totalAll+= $amount;
                    $totalQuantityAll+= $quantity;
                    $grandTotalAll+= $amount;
                    $countItemsAll++;
                }
     

                if (!empty($errorItems)) {
                    $data['result'] = 0;
                    $data['message'] = $errorItems;
                    echo json_encode($data);
                    die;
                }

                if (empty($items_in)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }
                $order_id_text = trim($order_id_text, ',');
                $pod_id_text = trim($pod_id_text, ',');
                $plan_id_text = trim($plan_id_text, ',');

                $options = [
                    'date' => $date,
                    'reference_no' => $reference_no,
                    'supplier_id' => $supplier_id,
                    'order_id' => $order_id_text,
                    'pod_id' => $pod_id_text,
                    'plan_id' => $plan_id_text,
                    // 'warehouse_id' => $warehouse_id,
                    'note' => $note,
                    'count_items' => $countItemsAll,
                    'total_quantity' => $totalQuantityAll,
                    'total' => $totalAll,
                    'grand_total' => $grandTotalAll,
                    'status' => $status,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                    'id_branch' => $id_branch
                ];

                $outsource_id = $this->outsource_model->insertOutsource($options);
                if ($outsource_id) {
                    if (getReference('outsource') == $reference_no) {
                        updateReference('outsource');
                    }


                    foreach ($items_in as $key => $value) {
                        $value['outsource_id'] = $outsource_id;
                        $outsource_item_id = $this->outsource_model->insertOutsourceItems($value);
                        
                        // $pod = get_table_where('tbl_productions_orders_details', ['id' => $value['pod_id']], '',
                        // 'row_array');
                        // $this->db->where('id', $value['pod_id']);
                        // $this->db->update('tbl_productions_orders_details',
                        // ['qty_outsource' => $pod['qty_outsource'] + $value['quantity']]);
                    }

                    $this->manufactures_model->updateFinishedStagesOutsourcing($outsource_id);
                    set_alert('message', lang('success'));
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                    @pusherTNHNotfication();
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $data['employees'] = $this->manufactures_model->getAllStaff();
            $data['warehouses'] = $this->site_model->getWarehouse();
            $data['reference_no'] = getReference('outsource');
            $data['tnh'] = $this->tnh;
            $data['title'] = lang('tnh_add_outsource');
            $data['breadcrumb'] = [array('link' => base_url('admin/outsource'), 'page' => lang('outsource')), array('link' => '#', 'page' => lang('tnh_add_outsource'))];
            $this->load->view('admin/outsource/add', $data);
        }
    }

    public function edit($id)
    {
        if (!$this->perEditOutsource) {
            accessDenied();
        }
        $outsource = $this->outsource_model->rowOutSourceById($id);
        if (empty($outsource)) {
            set_alert('danger', lang('no_data_exists'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }
        if ($outsource['status'] == "approved") {
            set_alert('danger', lang('browsed_cannot_be_edited'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

        if ($this->input->post('edit'))
        {
            $data = [];
            $export_form = $this->input->post('export_form');
            if ($outsource['reference_no'] != $this->input->post('reference_no'))
            {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_outsource"), 'trim|required|is_unique[tbl_outsource.reference_no]');
            } else {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_outsource"), 'trim|required');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('supplies', lang("tnh_supplies"), 'required');
            // $this->form_validation->set_rules('warehouses', lang("tnh_warehouses"), 'required');
            $this->form_validation->set_rules('id_branch', lang("Chi nhánh xưởng"), 'required');
            if ($this->form_validation->run() == true)
            {
                // print_arrays($this->input->post());
                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $supplier_id = $this->input->post('supplies');
                $id_branch = $this->input->post('id_branch');
                $warehouse_id = $this->input->post('warehouses');
                $note = $this->input->post('note');
                $status = 'un_approved';

            

                $totalAll = 0;
                $totalQuantityAll = 0;
                $grandTotalAll = 0;
                $countItemsAll = 0;
                
                $order_id_text = '';
                $pod_id_text = '';
                $plan_id_text = '';
              
                $counter = $this->input->post('counter');
                foreach ($counter as $key => $value) {
                    $items_id = $this->input->post('item_id')[$value];
                    if (empty($items_id)) continue;
                    $arrs = explode('__', $items_id);
                    $item_id = $arrs[1];
                    $type_item = $arrs[0];

                    if ($type_item == "products" || $type_item == "semi_products") {
                        $info = $this->products_model->rowProduct($item_id);
                    } else if ($type_item == "items") {
                        $info = $this->items_model->rowItems($item_id);
                    }
                    if (empty($info)) {
                        continue;
                    }

                    $items_code = $info['code'];
                    $items_name = $info['name'];
                    $quantity_outsource = number_unformat($this->input->post('quantity_outsource')[$value]);
                    $quantity = $quantity_outsource;
                    $note_item = $this->input->post('note_item')[$value];
                    $pod_id = $this->input->post('pod_id')[$value];
                    $id_stage = $this->input->post('id_stage')[$value];
                    $object_type = $this->input->post('object_type')[$value];
                    $order_id = $this->input->post('order_id')[$value];
                    $plan_id = $this->input->post('plan_id')[$value];

                    $price = number_unformat($this->input->post('price_outsource')[$value]);
                    $amount = $quantity * $price;

                    $items_in[] = [
                        'order_item_id' => 0,
                        'object_type' => $object_type,
                        'pod_id' => $pod_id,
                        'order_id' => $order_id,
                        'plan_id' => $plan_id,
                        'id_stage' => $id_stage,
                        'type_item' => $type_item,
                        'item_id' => $item_id,
                        'item_code' => $items_code,
                        'item_name' => $items_name,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'note_item' => $note_item,
                    ];
                    $order_id_text .= $order_id.',';
                    $pod_id_text .= $pod_id.',';
                    $plan_id_text .= $plan_id.',';

                    $totalAll+= $amount;
                    $totalQuantityAll+= $quantity;
                    $grandTotalAll+= $amount;
                    $countItemsAll++;
                }

                if (!empty($errorItems)) {
                    $data['result'] = 0;
                    $data['message'] = $errorItems;
                    echo json_encode($data);
                    die;
                }

                if (empty($items_in)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }
                $order_id_text = trim($order_id_text, ',');
                $pod_id_text = trim($pod_id_text, ',');
                $plan_id_text = trim($plan_id_text, ',');


                $options = [
                    'date' => $date,
                    'reference_no' => $reference_no,
                    'supplier_id' => $supplier_id,
                    'order_id' => $order_id_text,
                    'pod_id' => $pod_id_text,
                    'plan_id' => $plan_id_text,
                    // 'warehouse_id' => $warehouse_id,
                    'note' => $note,
                    'count_items' => $countItemsAll,
                    'total_quantity' => $totalQuantityAll,
                    'total' => $totalAll,
                    'grand_total' => $grandTotalAll,
                    'status' => $status,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                    'id_branch' => $id_branch
                ];
                // print_arrays($options);

                $ordersOutsourceOld = $this->outsource_model->getOrdersOutsource($id);
                $itemsOld = $this->outsource_model->getOutSourceItemsByOutsourceId($id);
                $up = $this->outsource_model->updateOutsource($id, $options);
                if ($up) {
                    //delete
                    $this->outsource_model->deleteOutsourceItems($id);
                    $this->outsource_model->deleteOrdersOutsource($id);


                    foreach ($items_in as $key => $value) {
                        $value['outsource_id'] = $id;
                        $outsource_item_id = $this->outsource_model->insertOutsourceItems($value);
                        
                    }

                    $this->manufactures_model->updateFinishedStagesOutsourcing($id, 1);
                    set_alert('message', lang('success'));
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $items = $this->outsource_model->getOutSourceItemsByOutsourceId($id);
            $bodyItems = '';
            $counter = 0;
            if (!empty($items))
            {
                $export_form = $outsource['export_form'];
                foreach ($items as $key => $value) {
                    $type_item = $value['type_item'];
                    $items_id = $value['item_id'];
                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/'.$info['images']);
                        }
                    } elseif ($type_item == "semi_products") {
                        $info = $this->products_model->rowProduct($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/'.$info['images']);
                        }
                    } elseif ($type_item == "semi_products_outside") {
                        $info = $this->products_model->rowProduct($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/'.$info['images']);
                        }
                    } elseif ($type_item == "items") {
                        $info = $this->items_model->rowItems($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit']);
                        if (!empty($info['avatar'])) {
                            $images = base_url($info['avatar']);
                        }
                    }
                    if (empty($images)) {
                        $images = base_url('assets/images/tnh/no_image.png');
                    }

                    $qtOutsource = '
                    COALESCE(
                    (SELECT SUM(tbl_outsource_items.quantity) 
                    FROM tbl_outsource_items
                    WHERE tbl_outsource_items.id_stage = '.$value['id_stage'].' AND tbl_outsource_items.pod_id = '.$value['pod_id'].' AND tbl_outsource_items.id != '.$value['id'].' ),0)
                    ';
                    $this->db->select('tbl_productions_orders_items.quantity,'.$qtOutsource.' as qty_outsource');
                    $this->db->from('tbl_productions_orders_details');
                    $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id','left');
                    $this->db->where('tbl_productions_orders_details.id',$value['pod_id']);
                    $result = $this->db->get()->row_array();


                    $items = [];
                    $this->db->select('*');
                    $this->db->from('tbl_product_stages');
                    $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                    $this->db->where('tbl_product_stages.product_id', $items_id);
                    $stages =  $this->db->get()->row_array();
                    if(!empty($stages)){
                        $items = $this->products_model->getProductStagesVersions($stages['id']);
                    }
                    $optionStage = '<option value=""></option>';
                    if (!empty($items)) {
                        foreach ($items as $k => $val) {
                            $selected = $val['stage_id'] == $value['id_stage'] ? 'selected' : '';
                            $optionStage.= '<option '.$selected.' value="'.$val['stage_id'].'">'.$val['stage_name'].'</option>';
                        }
                    }
                    $td_detail = '';
                    $title = '';
                    $reference_no = '';
                    if($value['object_type'] == "business_plan"){
                        $title = ' KHKD';
                        $order = get_table_where('tbl_business_plan',['id'=>$value['plan_id']],'','row_array');
                        $reference_no = $order['reference_no'];
                    } elseif($value['object_type'] == "orders"){
                        $title = 'Đơn hàng';
                        $order = get_table_where('tbl_orders',['id'=>$value['order_id']],'','row_array');
                        $reference_no = $order['reference_no'];
                    }
                    $productionOrderDetail = get_table_where('tbl_productions_orders_details',['id'=>$value['pod_id']],'','row_array');
                    $td_detail = '' .
                        '<div class="bold" style="font-size: 12px;"></div>' .
                        '<div>Lệnh SXCT: ' .$productionOrderDetail['reference_no']. ' - '.$title.': ' .$reference_no . '</div>' .
                        '';
                    
                    $this->db->select_sum('quantity');
                    $this->db->from('tbl_productions_orders_items');
                    $this->db->where('tbl_productions_orders_items.id',$productionOrderDetail['productions_orders_item_id']);
                    $quatity = $this->db->get()->row_array()['quantity'];


                    $tdNumber = '<div class="td-number text-center">'.(++$key).'</div>';

                    $tdCode = '<div class="td-code mbot10">'.
                        '<input type="hidden" name="item_id['.$counter.']" id="item_id" class="form-control item_id" value="'.$value['type_item'].'__'.$value['item_id'].'">'.
                        '<input type="hidden" name="counter['.$counter.']" id="counter" class="form-control counter" value="'.$counter.'">
                        <input type="hidden" name="pod_id['.$counter.']" id="pod_id" class="pod_id" style="width: 100%;"  value="'.$value['pod_id'].'">
                        <input type="hidden" name="order_id['.$counter.']" id="order_id" class="order_id" style="width: 100%;"  value="'.$value['order_id'].'">
                        <input type="hidden" name="object_type['.$counter.']" id="object_type" class="object_type" style="width: 100%;"  value="'.$value['object_type'].'">
                        <input type="hidden" name="plan_id['.$counter.']" id="plan_id" class="plan_id" style="width: 100%;"  value="'.$value['plan_id'].'">
                        <input type="hidden" name="outsource_item_id['.$counter.']" id="outsource_item_id" class="outsource_item_id" style="width: 100%;"  value="'.$value['id'].'">
                        <input type="hidden" class="check_item" value="'.$type_item.'__'.$items_id.'__'.$value['pod_id'].'">'.$value['item_name'].'('. $value['item_code'] .')</div>'.
                        '<div style="font-style: italic;font-size: 11px">'.$td_detail.'</div>'.
                        '<div class="type-item"></div>'.
                        '<div><div class=""><a href="javascript:void(0)" onclick="removeRow(this)" class="text-danger delete-remind remove-row">'.lang('delete').'</a></div></div>'.
                        '</div>';
                    $tdImage = '<div class="td-image">'.
                        '<div class="preview_image" style="width: auto;">'.
                        '<div class="display-block contract-attachment-wrapper img">'.
                        '<div style="width:45px;">'.
                        '<a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5">'.
                        '<div class="">'.
                        '<img src="'.$images.'" style="border-radius: 50%">'.
                        '</div>'.
                        '</a>'.
                        '</div>'.
                        '</div>'.
                        '</div>'.
                        '</div>';
                    $tdName = '<div class="td-item-name">'.$value['item_name'].'</div>';
                    $tdUnit = '<div class="td-unit">'.$unit['unit'].'</div>';
                    $tdStage = '<div class="td-stage" style="width:200px"><select required class="id_stage" style="width: 100%; height: 30px" id="id_stage_'.$counter.'" name="id_stage['.$counter.']">'.$optionStage.'</select></div>';
                    $tdQuantity = '<div class="td-quantity text-center">'.formatNumber($quatity).'</div>';
                    $tdQuantityHadOutsource = '<div class="td-quantity-had-outsource text-center">'.formatNumber($result['qty_outsource']).'</div>';

                    $quantityOutsource = $value['quantity'];
                    $tdQuantityOutsource = '<div class="td-quantity-outsource"><input style="width:100%" type="text" name="quantity_outsource['.$counter.']" id="quantity_outsource[]" onchange="totalOutsource()" class="form-control quantity_outsource number-format" value="'.formatNumber($quantityOutsource).'"><div class="show-error-item text-danger"></div></div>';

                    $tdPrice = '<div class="td-price-outsource"><input type="text" style="width:100%" onchange="totalOutsource()" name="price_outsource['.$counter.']" id="price_outsource[]" class="form-control price_outsource money-format" value="'.formatMoney($value['price']).'"></div>';
                    $tdSubtotal = '<div class="td-subtotal text-right">'.formatMoney($value['amount']).'</div>';
                    $tdNote = '<div class="td-note">'.
                        '<textarea name="note_item['.$counter.']" id="note_item[]" class="form-control" rows="3"></textarea>'.
                        '</div>';
                    $tdActions = '<div class="td-actions text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row btn btn-danger"></i></div>';

                    // <td>'.$tdQuantity.'</td>
                    // <td>'.$tdQuantityHadOutsource.'</td>
                    $bodyItems.= '<tr>
                        <td>'.$tdNumber.'</td>
                        <td>'.$tdCode.'</td>
                        <td>'.$tdImage.'</td>
                        <td>'.$tdUnit.'</td>
                        <td>'.$tdStage.'</td>
                        <td>'.$tdQuantityOutsource.'</td>
                        <td>'.$tdPrice.'</td>
                        <td>'.$tdSubtotal.'</td>
                        <td>'.$tdNote.'</td>
                        <td>'.$tdActions.'</td>
                    </tr>';
                    $counter++;
                }
            }

            //handling material
            $itemsMaterial = $this->outsource_model->getOutsourceMaterial($id);
            $bodyMaterial = '';
            $counterMaterial = 0;
            if (!empty($itemsMaterial))
            {
                foreach ($itemsMaterial as $key => $value) {
                    $type_item = $value['type_item'];
                    $item_id = $value['item_id'];
                    if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                        $info = $this->products_model->rowProduct($item_id);
                    } else if ($type_item == "materials") {
                        $info = $this->items_model->rowMaterial($item_id);
                    } else if ($type_item == "tools_supplies") {
                        $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                    }
                    $unit = $this->unit_model->rowUnit($info['unit_id']);

                    $tdNumber = '<div class="text-center td-number">'.(++$key).'</div>';
                    $tdItem = '<input type="hidden" name="counter_material[]" id="input" class="form-control" value="'.$counterMaterial.'">
                        <input type="text" name="items_material_id[]" id="items_material_id_'.$counterMaterial.'" class="items_material_id" style="width: 100%;" data-placeholder="'.lang('choose').'" value="'.$type_item.'__'.$item_id.'">';
                    $tdItemName = '<div class="td-item-name-material">'.$info['name'].'</div>';
                    $tdUnit = '<div class="td-unit-material">'.$unit['unit'].'</div>';
                    $tdQuantity = '<div class="td-quantity-material"><input type="text" onchange="formatNumBerKeyUpCus(this)" name="quantity_material[]" id="quantity_material[]" class="form-control quantity_material" style="width: 100%;" value="'.formatNumber($value['quantity']).'"></div>';
                    $tdPrice = '<div class="td-price-material"><input type="text" style="width: 100%;" onchange="formatNumBerKeyUpCus(this)" name="price_material[]" id="price_material[]" class="form-control price_material" value="'.formatMoney($value['price']).'"></div>';
                    $tdAmount = '<div class="td-amount-material text-right">'.formatMoney($value['amount']).'</div>';
                    $tdNote = '<div class="text-center td-note"><textarea name="note_material_item[]" id="note_item" style="width: 100%;" class="form-control note_item" rows="3">'.$value['note_item'].'</textarea></div>';
                    $tdActions = '<div class="text-center"><i onclick="removeRowMaterial(this)" class="fa fa-remove btn btn-danger remove-row-material"></i></div>';

                    $bodyMaterial.= '<tr>
                        <td>'.$tdNumber.'</td>
                        <td>'.$tdItem.'</td>
                        <td>'.$tdItemName.'</td>
                        <td>'.$tdUnit.'</td>
                        <td>'.$tdQuantity.'</td>
                        <td>'.$tdNote.'</td>
                    </tr>';

                    $counterMaterial++;
                }
            }
            //
            $referenceOrder = $this->outsource_model->rowRefereceNoOrderByOutsourceId($id);

            $data['bodyItems'] = $bodyItems;
            $data['bodyMaterial'] = $bodyMaterial;
            $data['counter'] = $counter;
            $data['counterMaterial'] = $counterMaterial;
            $data['referenceOrder'] = $referenceOrder;
            $data['id'] = $id;
            $data['outsource'] = $outsource;
            $data['employees'] = $this->manufactures_model->getAllStaff();
            $data['warehouses'] = $this->site_model->getWarehouse();
            $data['tnh'] = $this->tnh;
            $data['title'] = lang('tnh_edit_outsource');
            $data['breadcrumb'] = [array('link' => base_url('admin/outsource'), 'page' => lang('outsource')), array('link' => '#', 'page' => lang('tnh_edit_outsource'))];
            $this->load->view('admin/outsource/edit', $data);
        }
    }

    public function agree()
    {
        if (!$this->perApproveOutsource) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        $data = [];
        if ($this->input->get())
        {
            $outsource_id = $this->input->get('outsource_id');
            $status = $this->input->get('status');
            $outsource = $this->outsource_model->rowOutSourceById($outsource_id);
            $date = date('Y-m-d H:i:s');
            $user_id = get_staff_user_id();

            if ($outsource['status'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data); die;
            }

            if ($outsource['status_pay'] != 0) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_dtpckdbd');
                echo json_encode($data); die;
            }

            if ($outsource['workflow'] > 0) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_created_export_outsource_not_un_approved');
                echo json_encode($data); die;
            }

            $up = $this->outsource_model->updateOutsource($outsource_id, [
                'status' => $status,
                'date_status' => $date,
                'user_status' => $user_id
            ]);
            if ($up) {
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
        if (!$this->perDeleteOutsource) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        $data = [];
        if ($id) {
            $outsource = $this->outsource_model->rowOutSourceById($id);
            $ordersOutsource = $this->outsource_model->getOrdersOutsource($id);
            $items = $this->outsource_model->getOutSourceItemsByOutsourceId($id);
            if ($outsource['status'] == "un_approved") {
                if ($this->outsource_model->deleteOutsource($id)) {
                    $this->outsource_model->deleteOutsourceItems($id);
                    $this->outsource_model->deleteOrdersOutsource($id);

                    foreach($items as $key => $value){
                        //update pod stage
                        if ($this->outsource_model->checkExistOutsourcingByPod($value['pod_id'], $value['id_stage']) == 0){
                            $this->outsource_model->updateNotFinishedStagesOutsourcing($value);
                        }
                        //end
                    }

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('browsed_cannot_be_deleted');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Đã duyệt không thể xoá');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function view_outsource($id) {
        $outsource = $this->outsource_model->rowOutSourceById($id);
        $referenceOrder = $this->outsource_model->rowRefereceNoOrderByOutsourceId($id);
        $items = $this->outsource_model->getOutSourceItemsByOutsourceId($id);
        $branch = get_table_where('tblbranch',['id'=>$outsource['id_branch']], '', 'row_array');
        $branch_name = '';
        if(!empty($branch)){
            $branch_name = $branch['name'];
        }
        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/'.$info['images']);
                    }
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                } else if ($type_item = 'semi_products'){
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/'.$info['images']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }
                $type_name  = "";
                if($type_item == 'products'){
                    $type_name = '<span class="label label-success">'.lang($type_item).'</span>';
                } else if($type_item == 'semi_products'){
                    $type_name = '<span class="label label-success">'.lang($type_item).'</span>';
                } else if($type_item == 'semi_products_outside' ){
                    $type_name = '<span class="label label-success">'.lang($type_item).'</span>';
                } else {
                    $type_name = '<span class="label label-primary">'.lang('ch_items').'</span>';
                }

                $stage_name = '';
                $stage = get_table_where('tbl_stages',['id'=>$value['id_stage']],'','row_array');
                if(!empty($stage)){
                    $stage_name = $stage['name'];
                }
                $title_name = '';
                $order_name = '';
                if($value['object_type'] == 'orders'){ 
                    $order = get_table_where('tbl_orders',
                    ['id' => $value['order_id']], '', 'row_array');
                    if (!empty($order)) {
                        $order_name = $order['reference_no'];
                    }
                    $title_name = 'Đơn hàng:';
                } elseif($value['object_type'] == 'business_plan'){
                    $plan = get_table_where('tbl_business_plan',
                    ['id' => $value['plan_id']], '', 'row_array');
                    if (!empty($plan)) {
                        $order_name = $plan['reference_no'];
                    }
                    $title_name = 'KHKD:';
                }
                $tdNumber = '<td class="text-center">'.(++$key).'</td>';
                $tdImages = '<td>
                    <div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px;"><a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="'.$images.'" style="border-radius: 50%"></div></a></div></div></div></div>
                </td>';
                $tdCode = '<td>'.$info['code'].'<div class="type-item mtop10">'.$type_name.'</div></td>';
                $tdName = '<td>'.$info['name'].'('.$info['code'].')'.' <div style="font-size:11px">'.$title_name.'<span style="color: red;">'.$order_name.'</span></div></td>';
                $tdUnit = '<td>'.$unit['unit'].'</td>';
                $tdStage = '<td>'.$stage_name.'</td>';
                $tdQuantity = '<td class="text-center">'.formatNumber($value['quantity']).'</td>';
                $tdUnitPrice = '<td class="text-right">'.formatMoney($value['price']).'</td>';
                $tdTotalAmount = '<td class="text-right">'.formatMoney($value['amount']).'</td>';
                $tdNote = '<td>'.$value['note_item'].'</td>';

                $bodyItems.= '<tr>
                    '.$tdNumber.'
                    '.$tdImages.'
                    '.$tdName.'
                    '.$tdStage.'
                    '.$tdQuantity.'
                    '.$tdUnitPrice.'
                    '.$tdTotalAmount.'
                    '.$tdNote.'
                </tr>';
            }
        }

        //handling material
        $itemsMaterial = $this->outsource_model->getOutsourceMaterial($id);
        $bodyMaterial = '';
        $counterMaterial = 0;
        if (!empty($itemsMaterial))
        {
            foreach ($itemsMaterial as $key => $value) {
                $type_item = $value['type_item'];
                $item_id = $value['item_id'];
                if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                    $info = $this->products_model->rowProduct($item_id);
                    if (!empty($info['images'])) {
                        $images_material = base_url('uploads/products/'.$info['images']);
                    }
                } elseif ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($item_id);
                    if (!empty($info['images'])) {
                        $images_material = base_url('uploads/materials/' . $info['images']);
                    }
                } elseif ($type_item == "tools_supplies") {
                    $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                }
                if (empty($images_material)) {
                    $images_material = base_url('assets/images/tnh/no_image.png');
                }
                $unit = $this->unit_model->rowUnit($info['unit_id']);
                $type_name_material  = "";
                if($type_item == 'products'){
                    $type_name_material = '<span class="label label-warning">'.lang($type_item).'</span>';
                } elseif($type_item == 'semi_products'){
                    $type_name_material = '<span class="label label-warning">'.lang($type_item).'</span>';
                } elseif($type_item == 'semi_products_outside' ){
                    $type_name_material = '<span class="label label-warning">'.lang($type_item).'</span>';
                }elseif($type_item == 'materials' ){
                    $type_name_material = '<span class="label label-success">'.lang($type_item).'</span>';
                }
                else {
                    $type_name_material = '<span class="label label-primary">'.lang('ch_items').'</span>';
                }

                $tdNumber = '<div class="text-center td-number">'.(++$key).'</div>';
                $tdImages = '<div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px;"><a href="'.$images_material.'" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="'.$images_material.'" style="border-radius: 50%"></div></a></div></div></div></div>';
                $tdItem = '<div>'.$info['name'].'('.$info['code'].')'.'</div>';
                $tdItemName = '<div class="td-item-name-material">'.$type_name_material.'</div>';
                $tdUnit = '<div class="td-unit-material">'.$unit['unit'].'</div>';
                $tdQuantity = formatNumber($value['quantity']);
                $tdPrice = formatMoney($value['price']);
                $tdAmount = formatMoney($value['amount']);
                $tdNote = '<div class="text-center td-note">'.$value['note_item'].'</div>';

                $bodyMaterial.= '<tr>
                    <td>'.$tdNumber.'</td>
                    <td>'.$tdImages.'</td>
                    <td>'.$tdItem.'</td>
                    <td>'.$tdItemName.'</td>
                    <td class="text-center">'.$tdUnit.'</td>
                    <td class="text-center">'.$tdQuantity.'</td>
                    <td class="text-right">'.$tdPrice.'</td>
                    <td class="text-right">'.$tdAmount.'</td>
                    <td>'.$tdNote.'</td>
                </tr>';

                $counterMaterial++;
            }
        }
        //

        $data['bodyItems'] = $bodyItems;
        $data['referenceOrder'] = $referenceOrder;

        $data['bodyMaterial'] = $bodyMaterial;
        $data['branch_name'] = $branch_name;

        $data['supplier'] = $this->site_model->rowSupplier($outsource['supplier_id']);
        $data['warehouse'] = $this->site_model->rowWarehouseById($outsource['warehouse_id']);
        $data['employee'] = !empty($outsource['employee_id']) ? get_staff_full_name($outsource['employee_id']) : '';
        $data['created_by'] = get_staff_full_name($outsource['created_by']);
        $data['updated_by'] = !empty($outsource['updated_by']) ? get_staff_full_name($outsource['updated_by']) : '';
        $data['user_status'] = !empty($outsource['user_status']) ? get_staff_full_name($outsource['user_status']) : '';
        $data['id'] = $id;
        $data['outsource'] = $outsource;
        $this->load->view('admin/outsource/view_outsource', $data);
    }

    // public function exportOutsource($id)
    // {
    //     $data = [];
    //     $outsource = $this->outsource_model->rowOutSourceById($id);
    //     if ($outsource['status'] == "un_approved") {
    //         $data['result'] = 0;
    //         $data['message'] = lang('tnh_please_approved');
    //         echo json_encode($data); die;
    //     }
    //     if ($outsource['workflow'] > 0) {
    //         $data['result'] = 0;
    //         $data['message'] = lang('tnh_created_export_outsource');
    //         echo json_encode($data); die;
    //     }

    //     $grand_total = 0;
    //     // $items = $this->outsource_model->getOutSourceItemsByOutsourceId($id);
    //     $items = $this->outsource_model->getOutsourceMaterial($id);
    //     foreach ($items as $key => $value) {
    //         /*
    //         Type: items (HH), product(SP), nvl(material), tools
    //          */
    //         $type = $value['type_item'];
    //         if ($type == "products") {
    //             $type = 'product';
    //         } else if ($type == "semi_products" || $type == "semi_products_outside") {
    //             $type = 'product';
    //         } else if ($type == "materials") {
    //             $type = 'nvl';
    //         } else if ($type == "items") {
    //             $type = 'items';
    //         } else if ($type == "tools_supplies") {
    //             $type = 'tools';
    //         }
    //         $item_id = $value['item_id'];
    //         $quatity = $value['quantity'];
    //         $price = $value['price'];
    //         $amount = $value['amount'];
    //         $note_item = $value['note_item'];

    //         $tranferItems[] = array(
    //             'id_items' => $item_id,
    //             'quantity' => $quatity,
    //             'quantity_net' => $quatity,
    //             'type' => $type,
    //             'note' => $note_item,
    //             'localtion_id' => 0,
    //             'localtion_to' => 0,
    //             'price' => $price,
    //             'amount' => $amount
    //         );

    //         $grand_total+= $amount;
    //     }

    //     $warehouse_from = $outsource['warehouse_id'];
    //     $warehouse_to = $this->site_model->createWarehouseSupplier($outsource['supplier_id']);

    //     $transfer = array(
    //         'code' => sprintf('%06d', ch_getMaxID('id', 'tbltransfer_warehouse') + 1),
    //         'prefix' => get_option('prefix_transfer'),
    //         'note' => $outsource['note'],
    //         'warehouse_id' => $warehouse_from,
    //         'warehouse_to' => $warehouse_to,
    //         'date' => date('Y-m-d H:i:s'),
    //         'staff_id' => get_staff_user_id(),
    //         'date_create' => date('Y:m:d H:i:s'),
    //         'status' => 2,
    //         'total' => $grand_total,
    //         'outsource_id' => $id
    //     );

    //     $transfer_id = $this->site_model->insertTransferWarehouse($transfer);
    //     if ($transfer_id) {
    //         if (!empty($tranferItems)) {
    //             $locationFrom = $this->site_model->createDefaultLocationWarehouse($warehouse_from);
    //             $locationTo = $this->site_model->createDefaultLocationWarehouse($warehouse_to);

    //             foreach ($tranferItems as $key => $value) {
    //                 $value['id_transfer'] = $transfer_id;
    //                 $value['localtion_id'] = $locationFrom;
    //                 $value['localtion_to'] = $locationTo;
    //                 $value['warehouses_to'] = $warehouse_to;
    //                 $value['warehouses_id'] = $warehouse_from;

    //                 $this->site_model->createDefaultWarehouseItems([
    //                     'warehouse_id' => $warehouse_from,
    //                     'location_id' => $locationFrom,
    //                     'type_item' => $value['type'],
    //                     'item_id' => $value['id_items'],
    //                 ]);

    //                 $this->site_model->createDefaultWarehouseItems([
    //                     'warehouse_id' => $warehouse_to,
    //                     'location_id' => $locationTo,
    //                     'type_item' => $value['type'],
    //                     'item_id' => $value['id_items'],
    //                 ]);

    //                 $this->site_model->insertTransferWarehouseDetail($value);
    //             }
    //         }

    //         $this->outsource_model->updateOutsource($id, ['tranfer_id' => $transfer_id, 'workflow' => 1]);

    //         //duyet thu kho

    //         if(!test_quantity_tranfer($transfer_id))
    //         {
    //             echo json_encode(array(
    //                 'result' => '0',
    //                 'message' => _l('Chưa duyệt thủ kho. Số lượng không đủ .'),
    //             ));die;
    //         } else {
    //             $data = array(
    //                 'warehouseman_id' => get_staff_user_id(),
    //                 'warehouseman_date' => date('Y-m-d H:i:s')
    //             );
    //             log_activity('Transfer Warehouse items approved [ID Import: ' . $transfer_id);
    //             $this->transfer_model->increaseTranfersWarehouse($transfer_id);
    //             $success    = $this->db->update('tbltransfer_warehouse',$data,array('id' => $transfer_id));
    //         }
    //         //

    //         $data['result'] = 1;
    //         $data['message'] = lang('success');
    //     } else {
    //         $data['result'] = 0;
    //         $data['message'] = lang('fail');
    //     }

    //     echo json_encode($data);
    // }

    public function exportOutsource($id)
    {
        $data = [];
        $outsource = $this->outsource_model->rowOutSourceById($id);
        if ($outsource['status'] == "un_approved") {
            $data['result'] = 0;
            $data['message'] = lang('tnh_please_approved');
            echo json_encode($data);
            die;
        }

        $total_quantity_material = 0;
        $total_material = 0;
        $grand_total_material = 0;


        //end
        $exportDifferentItems = [];
        $grand_total_export =0;
        $count_error = 0;
        if ($this->input->post('save')) {
            // print_arrays($this->input->post());
            $note = $this->input->post('note');
            $date = $this->input->post('date');
            $items_material_id = $this->input->post('items_material_id');
            if (!empty($items_material_id)) {
                foreach ($items_material_id as $key => $value) {
                    if (empty($value)) {
                        continue;
                    }
               
                    $arr_item = explode('__', $value);
                    $type_item = $arr_item[0];
                    $item_id = $arr_item[1];
                    if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                        $info_item = $this->products_model->rowProduct($item_id);
                    } elseif ($type_item == "materials") {
                        $info_item = $this->items_model->rowMaterial($item_id);
                    } elseif ($type_item == "tools_supplies") {
                        $info_item = $this->tools_supplies_model->rowToolsSupplies($item_id);
                    }
                    if (empty($info_item)) {
                        continue;
                    }

                    $quantity = number_unformat($this->input->post('quantity_material')[$key]);
                    $price = 0;
                    $amount = $quantity * $price;
                    $note_item = $this->input->post('note_material_item')[$key];
                    $localtion_id = $this->input->post('locations')[$key];
                    if($localtion_id == 0){
                        $count_error ++;   
                        continue;
                    }
                    $item_code = $info_item['code'];
                    $item_name = $info_item['name'];

                    $itemsMaterial[] = [
                        'type_item' => $type_item,
                        'item_id' => $item_id,
                        'item_code' => $item_code,
                        'item_name' => $item_name,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'note_item' => $note_item,
                    ];

                    $total_quantity_material += $quantity;
                    $total_material += $amount;
                    $grand_total_material += $amount;

                    $type = $type_item;
                    if ($type == "products" || $type == "semi_products") {
                        $type = 'product';
                    } elseif ($type == "materials") {
                        $type = 'nvl';
                    } elseif ($type == "items") {
                        $type = 'items';
                    } elseif ($type == "tools_supplies") {
                        $type = 'tools';
                    }
                    $arr_location = explode("__",$localtion_id);
                    $warehouses_id = $arr_location[0];
                    $localtion_warehouses_id = $arr_location[1];
                    
                    $exportDifferentItems[] = [
                        'product_id' => $item_id ,
                        'quantity_net' => $quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'type'  => $type,
                        'note' => $note_item,
                        'warehouses_id' => $warehouses_id ,
                        'localtion_warehouses_id' => $localtion_warehouses_id ,
                    ];
                    $grand_total_export+= $amount;
                }
            }
            if($count_error > 0){
                $data['result'] = 0;
                $data['message'] = lang('Lưu không thành công, Vui lòng kiểm tra lại dữ liệu');
                echo json_encode($data);
                die;
            }
            if (empty($itemsMaterial)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_materials_export_not_empty');
                echo json_encode($data);
                die;
            }

            // print_arrays($exportDifferentItems);
            //xuat kho khac
            $exportDifferent = array(
                'code' => sprintf('%06d', ch_getMaxID('id', 'tblexport_different') + 1),
                'prefix' => get_option('prefix_export_different'),
                'object' => 2,
                'id_object' => $outsource['supplier_id'],
                'id_outsource'  => $outsource['id'],
                'object_text' => '',
                'date' => date('Y:m:d H:i:s'),
                'staff_id' => get_staff_user_id(),
                'date_create' => date('Y:m:d H:i:s'),
                'status' => 1,
                'subtotal' => $grand_total_export,
                'id_branch' => $outsource['id_branch'],
                );
             
                $export_diff_id = $this->site_model->insertExportDiffWarehouse($exportDifferent);
                if ($export_diff_id) {
                        
                    foreach ($exportDifferentItems as $key => $value) {
                        $value['id_export_different'] = $export_diff_id;
                        $this->site_model->insertExportDiffWarehouseDetail($value);
                    }
                    //update item material
                    if (!empty($itemsMaterial)) {
                        foreach ($itemsMaterial as $key_material => $value_material) {
                            $value_material['outsource_id'] = $outsource['id'];
                            $value_material['export_different_id'] = $export_diff_id;
                            $this->outsource_model->insertOutsourceMaterial($value_material);
                        }
                    }
                    $total_quantity_material_update = 0;
                    $total_material_update = 0;
                    $grand_total_material_update = 0;
                    //update outsource
                    $dataMaterals = get_table_where('tbl_outsource_material', ['outsource_id' => $id], '', 'result_array');
                    if (!empty($dataMaterals)) {
                        foreach ($dataMaterals as $key => $value) {
                            $total_quantity_material_update += $value['quantity'];
                            $total_material_update += $value['amount'];
                            $grand_total_material_update += $value['amount'];
                        }
                    }
                    $dataimport = get_table_where('tbl_import_outsource', ['outsource_id' => $id], '', 'row_array');
                    if (!empty($dataimport)) {
                        $workflow = 2;
                    } else {
                        $workflow = 1;
                    }
                    $this->db->where('id', $id);
                    $this->db->update('tbl_outsource', [
                        'workflow' => $workflow,
                        'total_quantity_material' => $total_quantity_material_update,
                        'total_material' => $total_material_update,
                        'grand_total_material' => $grand_total_material_update,
                    ]);
                    //duyet thu kho
                    if ($export_diff_id){
                        if (!test_quantity_export_different_warehouses($export_diff_id)) {
                            $data['result'] = false;
                            $data['message'] = _l('Chưa duyệt thủ kho. Số lượng không đủ .');
                            echo json_encode($data);
                            die;
                        } else {
                            $_data = array(
                                'warehouseman_id' => get_staff_user_id(),
                                'warehouseman_date' => date('Y-m-d H:i:s')
                            );
                            $success    = $this->db->update('tblexport_different', $_data, array('id' => $export_diff_id));
                            if ($success) {
                                log_activity('Export Warehouses items approved [ID export_warehouses: ' . $export_diff_id);
                                $this->export_different_model->decreaseWarehouse($export_diff_id);
                            }
                        }
                    }

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
          
            //end

            echo json_encode($data);
        } else {

            

            $data['warehouses'] = $this->site_model->getWarehouse();
            $data['outsource'] = $outsource;
            $data['id'] = $id;
            $this->load->view('admin/outsource/tranfer_outsource', $data);
        }
    }

    public function import_outsource($id)
    {
        $outsource = $this->outsource_model->rowOutSourceById($id);

        $transfer = $this->outsource_model->getTransferWarehouseByOutsourceId($id);

        if ($this->input->post('save')) {
            $data = [];
            if ($outsource['status'] == "un_approved") {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_approved');
                echo json_encode($data); die;
            }

            if ($outsource['workflow'] < 1) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_created_export_outsource');
                echo json_encode($data); die;
            }

            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('warehouse_to', lang("tnh_warehouse_to"), 'required');
            if ($this->form_validation->run() == true)
            {
                // print_arrays($this->input->post());
                $date = to_sql_date($this->input->post('date'), true);
                $warehouse_to = $this->input->post('warehouse_to');
                $note = $this->input->post('note');
                $status = "approved";
                $count_items = 0;
                $total_quantity = 0;
                $grand_total = 0;
                $errorItems = '';
                $po_id = 0;
                $arrPois = [];
                $counter = $this->input->post('counter');
                foreach ($counter as $key => $value) {
                    $outsource_item_id = $this->input->post('outsource_item_id')[$value];
                    if (empty($outsource_item_id)) continue;
                    $outsource_item = $this->outsource_model->rowOutsourceItemsById($outsource_item_id);
                    if (empty($outsource_item)) continue;

                    $item_id = $outsource_item['item_id'];
                    $items_code = $outsource_item['item_code'];
                    $items_name = $outsource_item['item_name'];
                    $type_item = $outsource_item['type_item'];
                    $pod_id = $outsource_item['pod_id'];
                    $order_id = $outsource_item['order_id'];
                    $plan_id = $outsource_item['plan_id'];
                    $object_type = $outsource_item['object_type'];
                    $quantity_import_outsource = number_unformat($this->input->post('quantity_outsource')[$value]);
                    $note_item = $this->input->post('note_item')[$value];
                    $locaiton_to = !empty($this->input->post('location_to')[$value]) ? $this->input->post('location_to')[$value] : 0;

                    $stage = get_table_where('tbl_stages',['stage_import_outsource'=>1],'','row_array');
                    $stage_name = '';
                    if(!empty($stage)){
                        $stage_id_import_outsource = $stage['id'];
                        $stage_name = $stage['name'];
                    } else {
                        $stage_id_import_outsource = 0;
                        $stage_name = '';
                    }
                    if (empty($locaiton_to)) {
                        $pod = get_table_where('tbl_productions_orders_details',['id'=>$pod_id],'','row_array');
                        $nameLocation = $pod['reference_no'].'('.$stage_name.')';

                        $ktr_local = get_table_where('tbllocaltion_warehouses', array(
                            'name' => $nameLocation,
                            'warehouse' => $warehouse_to,
                            'pod_id' => $pod_id,
                            'stage_id' => $stage_id_import_outsource,
                            'stage_id_import_outsource' => $stage_id_import_outsource,
                        ), '', 'row');
                        if (empty($ktr_local)) {
                            $in = [
                                'name' => $nameLocation,
                                'code' => $nameLocation,
                                'warehouse' => $warehouse_to,
                                'name_parent' => $nameLocation,
                                'child' => 1,
                                'create_by' => get_staff_user_id(),
                                'date_create' => date('Y-m-d H:i:s'),
                                'status' => 0,
                                'lever' => 1,
                                'pod_id' => $pod_id,
                                'stage_id' => $stage_id_import_outsource,
                                'stage_id_import_outsource' => $stage_id_import_outsource,
                            ];
                            $this->db->insert('tbllocaltion_warehouses', $in);
                            $locaiton_to = $this->db->insert_id();
                        } else {
                            $locaiton_to = $ktr_local->id;
                        }
                    }

                    //check quantity outsource
                    $quantity_outsource = $outsource_item['quantity'];
                    $quantity_had_outsource = $outsource_item['qty_ip_outsource'];
                    $quantity_max = $quantity_outsource - $quantity_had_outsource;
                    if ($quantity_import_outsource > $quantity_max) {
                        $errorItems.= lang('tnh_quantity_outsource_have_change_please_referesh');
                        break;
                    }

                    $price = number_unformat($this->input->post('price')[$value]);
                    $amount = $quantity_import_outsource * $price;

                    $items_in[] = [
                        'outsource_item_id' => $outsource_item_id,
                        'type_item' => $type_item,
                        'item_id' => $item_id,
                        'item_code' => $items_code,
                        'item_name' => $items_name,
                        'locaiton_to' => $locaiton_to,
                        'quantity' => $quantity_import_outsource,
                        'price' => $price,
                        'amount' => $amount,
                        'note_item' => $note_item,
                        'pod_id' => $pod_id,
                        'object_type' => $object_type,
                        'order_id' => $order_id,
                        'plan_id' => $plan_id,
                        'stage_id_default'=> $stage_id_import_outsource,
                    ];

                    $type = $type_item;
                    if ($type == "products") {
                        $type = 'product';
                    } else if ($type == "materials") {
                        $type = 'nvl';
                    } else if ($type == "items") {
                        $type = 'items';
                    } else if ($type == "tools_supplies") {
                        $type = 'tools';
                    }

                    $pod = get_table_where('tbl_productions_orders_details',['id'=>$pod_id],'','row_array');
                    $po_id = $pod['productions_orders_id']; 

                    $this->db->select('tbl_productions_orders_items_stages.id as poi_id');
                    $this->db->from('tbl_productions_orders_details');
                    $this->db->join('tbl_productions_orders_items_stages','tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
                    $this->db->where('tbl_productions_orders_details.id',$pod_id);
                    $this->db->where('tbl_productions_orders_items_stages.stage_id',$stage_id_import_outsource);
                    $pois =  $this->db->get()->row_array();
                    if(!empty($pois)){
                        $arrPois[] = $pois['poi_id'];
                    }


                    //xuat kho khac
                    // $product = $this->products_model->rowProduct($item_id);
                    // $versions_bom = $product['versions'];
                    // if (!empty($versions_bom)) {
                    //     $version = $this->products_model->getBomByProductIdAndVersions($item_id, $versions_bom);
                    //     $elements = $this->products_model->getVersionsElementByVersionId($version['id']);
                    //     if(!empty($elements)){
                    //         foreach ($elements as $k => $v){
                    //             $quantity_element = $v['quantity'];
                    //             $total_quantity_element = $quantity_import_outsource * $quantity_element;
                    //             $element_items = $this->products_model->getElementItemsByElementId($v['id']);
                    //             if (!empty($element_items)) {
                    //                 foreach ($element_items as $i => $el) {
                    //                     $quantity_single = $el['quantity'];
                    //                     $total_quantity_item = $total_quantity_element * $quantity_single;
                    //                     $row_exchange = $this->products_model->rowExchangeItems($el['item_id'], $el['unit_id']);
                    //                     $quantity_exchange = 1;
                    //                     if (!empty($row_exchange)) {
                    //                         $quantity_exchange = $row_exchange['number_exchange'];
                    //                     }
                    //                     $quantity_primary = $total_quantity_item/$quantity_exchange;

                    //                     $item_id_key = $el['item_id'].'_'.$el['type'];
                    //                     if (!empty($exportDifferentItems[$item_id_key]))
                    //                     {
                    //                         $exportDifferentItems[$item_id_key]['quantity_net'] = $exportDifferentItems[$item_id_key]['quantity_net'] + $quantity_primary;
                    //                     } else {
                    //                         $exportDifferentItems[$item_id_key] = array(
                    //                             'product_id' => $el['item_id'] ,
                    //                             'quantity_net' => $quantity_primary,
                    //                             'type'  => $el['type'],
                    //                         );
                    //                     } 
                    //                 }
                    //             }
                    //         }
                    //     }
                    // }


                    $total_quantity+= $quantity_import_outsource;
                    $grand_total+= $amount;
                }
                if (!empty($errorItems)) {
                    $data['result'] = 0;
                    $data['message'] = $errorItems;
                    echo json_encode($data);
                    die;
                }

                if (empty($items_in)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }
                // print_arrays($items_in);

                $options = [
                    'date' => $date,
                    'reference_no' => getReference('import_outsource'),
                    'warehouse_to' => $warehouse_to,
                    'note' => $note,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'grand_total' => $grand_total,
                    'status' => $status,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                    'outsource_id' => $id,
                    'supplier_id' =>$outsource['supplier_id'],
                    'id_branch' =>$outsource['id_branch'],
                ];
    
                //xuat kho khac
                // $transferOld = $this->site_model->getTransferByOursource($outsource['id']);
                // $grand_total_export = 0;
                // if(!empty($transferOld)){
                //     foreach ($transferOld as $k => $v){
                //         foreach($exportDifferentItems as $kk => $vv){
                //             $exportDifferentItems[$kk]['price'] = $v['price'];
                //             $exportDifferentItems[$kk]['amount'] = $vv['quantity_net'] * $v['price'];
                //             $exportDifferentItems[$kk]['amount'] = $vv['quantity_net'] * $v['price'];
                //             $exportDifferentItems[$kk]['type'] = $v['type'];
                //             $exportDifferentItems[$kk]['warehouses_id'] = $v['warehouses_to'];
                //             $exportDifferentItems[$kk]['localtion_warehouses_id'] = $v['localtion_to'];
                //         }
                //     }
                // }
                // foreach ($exportDifferentItems as $kkk => $vvv){
                //     $grand_total_export+= $vvv['price'] * $vvv['quantity_net'];
                // }
                // $exportDifferent = array(
                //     'code' => sprintf('%06d', ch_getMaxID('id', 'tblexport_different') + 1),
                //     'prefix' => get_option('prefix_export_different'),
                //     'object' => 2,
                //     'id_object' => $outsource['supplier_id'],
                //     'id_outsource'  => $outsource['id'],
                //     'object_text' => '',
                //     'date' => date('Y:m:d H:i:s'),
                //     'staff_id' => get_staff_user_id(),
                //     'date_create' => date('Y:m:d H:i:s'),
                //     'status' => 1,
                //     'subtotal' => $grand_total_export,
                // );


                // print_arrays($options);
                $import_outsource_id = $this->outsource_model->insertImportOutsource($options);
                if ($import_outsource_id) {
                    updateReference('import_outsource');
                    //dt
                    // $exportDifferent['id_import_outsource'] = $import_outsource_id;

                    // $export_diff_id = $this->site_model->insertExportDiffWarehouse($exportDifferent);
                    // if ($export_diff_id) {
                    //     foreach ($exportDifferentItems as $key => $value) {
                    //         $value['id_export_different'] = $export_diff_id;
                    //         $this->site_model->insertExportDiffWarehouseDetail($value);
                    //     }
                    //     //update xuat gia cong
                    //     $this->db->where('id',$outsource['id']);
                    //     $this->db->update('tbl_outsource',['id_export_different' => $export_diff_id]);
                    // }
                    //

                    foreach ($items_in as $key => $value) {
                        $value['import_outsource_id'] = $import_outsource_id;
                        $import_outsource_item_id = $this->outsource_model->insertImportOutsourceItems($value);
                        if ($import_outsource_item_id) {
                            $this->outsource_model->updateQuantityOutsourceItems($value['outsource_item_id'], $value['quantity'], $plus = 0);
                        }
                    }

                    $this->outsource_model->updateQuantityOutsource($id, $total_quantity, 2, $plus = 0);
                    //duyet kho nhap
                    $data=array(
                        'warehouseman_id'=>get_staff_user_id(),
                        'warehouseman_date'=>date('Y-m-d H:i:s')
                    );
                    $success1    = $this->db->update('tbl_import_outsource',$data,array('id' => $import_outsource_id));
                    if($success1){
                        log_activity('Warehouse items approved [ID warehouse_product: ' . $import_outsource_id);
                        $this->outsource_model->increaseWarehouse($import_outsource_id);
                    }
                    //
                    //duyet thu kho
                    // if ($export_diff_id){
                    //     if (!test_quantity_export_different_warehouses($export_diff_id)) {
                    //         $data['result'] = 0;
                    //         $data['message'] = _l('Chưa duyệt thủ kho. Số lượng xuất kho  không đủ .');
                    //         echo json_encode($data);
                    //         die;
                    //     } else {
                    //         $_data = array(
                    //             'warehouseman_id' => get_staff_user_id(),
                    //             'warehouseman_date' => date('Y-m-d H:i:s')
                    //         );
                    //         $success    = $this->db->update('tblexport_different', $_data, array('id' => $export_diff_id));
                    //         if ($success) {
                    //             log_activity('Export Warehouses items approved [ID export_warehouses: ' . $export_diff_id);
                    //             $this->export_different_model->decreaseWarehouse($export_diff_id);
                    //         }
                    //     }
                    // }

                    $this->manufactures_model->updateFinishedStagesImportOutsourcing($import_outsource_id);

                    $stage = get_table_where('tbl_stages',['stage_import_outsource'=>1],'','row_array');
                    // notificationImportOutsource($import_outsource_id,get_staff_user_id(),[
                    //     'stage_id' => $stage['id'],
                    //     'po_id' => $po_id,
                    //     'arrPois' => $arrPois
                    // ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data); die;
        } else {
            // $items = $this->outsource_model->getOutSourceItemsByOutsourceId($id);
            $items = $this->outsource_model->getOutSourceItemsByOutsourceIdNew($id);
            // print_arrays($items);
            $this->db->select('*');
            $this->db->where('tblwarehouse.id !=', 8);
            $warehouses = $this->db->get('tblwarehouse')->result_array();
            $data['warehouses'] = $warehouses;
            $data['locationWarehouseFrom'] = $this->site_model->getLocationWarehouse($transfer['warehouse_to']);
            $data['staff'] = $this->site_model->getStaff();
            $data['outsource'] = $outsource;
            $data['transfer'] = $transfer;
            $data['items'] = $items;
            $data['id'] = $id;
            $this->load->view('admin/outsource/import_outsource', $data);
        }
    }

    public function getOutsourceItems()
    {
        $data = [];
        if ($this->input->post())
        {
            $outsource_item_id = $this->input->post('outsource_item_id');
            if (!empty($outsource_item_id)) {
                $outsource_item = $this->outsource_model->rowOutsourceItemsByIpOutsource($outsource_item_id);
                if(!empty($outsource_item)){
                    if($outsource_item['object_type'] == 'orders'){
                        $order = get_table_where('tbl_orders',['id'=>$outsource_item['order_id']],'','row_array');
                        if(!empty($order)){
                            $outsource_item['reference_no'] = $order['reference_no'];
                            $outsource_item['title'] = 'Đơn hàng';
                        }
                    } elseif($outsource_item['object_type'] == 'business_plan'){
                        $order = get_table_where('tbl_business_plan',['id'=>$outsource_item['plan_id']],'','row_array');
                        if(!empty($order)){
                            $outsource_item['reference_no'] = $order['reference_no'];
                            $outsource_item['title'] = 'KHKD';
                        }
                    }
                }
                $data['outsource_item'] = $outsource_item;
            }

            $outsource_id = $this->input->post('outsource_id');
            if (!empty($outsource_id)) {
                $items = $this->outsource_model->getOutsourceItemsByIpOutsource($outsource_id);
                if(!empty($items)){
                    foreach($items as $key => $value){
                        if($value['object_type'] == 'orders'){
                            $order = get_table_where('tbl_orders',['id'=>$value['order_id']],'','row_array');
                            if(!empty($order)){
                                $items[$key]['reference_no'] = $order['reference_no'];
                                $items[$key]['title'] = 'Đơn hàng';
                            }
                        } elseif($value['object_type'] == 'business_plan'){
                            $order = get_table_where('tbl_business_plan',['id'=>$value['plan_id']],'','row_array');
                            if(!empty($order)){
                                $items[$key]['reference_no'] = $order['reference_no'];
                                $items[$key]['title'] = 'KHKD';
                            }
                        }
                    }
                }
                $data['items'] = $items;
            }
        }
        echo json_encode($data);
    }

    public function getLocationWarehouses()
    {
        $data = [];
        if ($this->input->post()) {
            $warehouse = $this->input->post('warehouse');
            $location = $this->site_model->getLocationWarehouse($warehouse, $child = 1);
            $data['location'] = $location;
        }
        echo json_encode($data);
    }
   
    public function searchSuppliers($id = false)
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $params = $this->input->get('params');
        $type = $params['type'];
        $data['results'] = $this->site_model->searchSuppliersOfType($term, $limit, $type);
        if ($id) {
            $supplier = $this->site_model->rowSupplier($id);
            $data['row'] = ['id' => $supplier['id'], 'text' => $supplier['company']];
        }
        echo json_encode($data);
    }

    public function getOrdersByOutsource($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $results = false;
        $results = $this->outsource_model->searchOrderForOutsource($term, $limit);
        $data['results'] = $results;
        if ($id) {
        }
        echo json_encode($data);
    }

    public function getOrdersItemsByOrderId()
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $orders_id = $params['orders_id'];
        $edit = $params['edit'];
        $results = false;
        if (!empty($orders_id)) {
            $orders_id = explode(",", $orders_id);
            $results = $this->outsource_model->searchOrderItemForOutsource($term, $limit, $orders_id, $edit);
        }
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function rowOrderItem()
    {
        $data = [];
        if ($this->input->get())
        {
            $order_item_id = $this->input->get('order_item_id');
            $order_item = $this->orders_model->rowOrderItemsById($order_item_id);
            $outsource_id = $this->input->get('outsource_id');
            if (!empty($order_item)) {
                $order = $this->orders_model->rowOrderById($order_item['order_id']);
                $type_item = $order_item['type_item'];
                $items_id = $order_item['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/'.$info['images']);
                    }
                    $price_processing = $info['price_processing'];
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                    $price_processing = 0;
                }
                if ($outsource_id > 0) {
                    $quantityOutsourceEdit = $this->outsource_model->rowOutsourceItemByOrderItemId($order_item_id, $outsource_id)['quantity_outsource'];
                    $order_item['quantity_outsource'] = $order_item['quantity_outsource'] - $quantityOutsourceEdit;
                }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }
                $order_item['unit'] = $unit['unit'];
                $order_item['images'] = $images;
                $order_item['reference_no'] = $order['reference_no'];
                $order_item['price_processing'] = $price_processing;
            }
            $data['order_item'] = $order_item;
        }
        echo json_encode($data);
    }

    public function getOrderItem()
    {
        $data = [];
        if ($this->input->post())
        {
            $orders_id = $this->input->post('orders_id');
            $items = false;
            if (!empty($orders_id)) {
                $orders_id = explode(",", $orders_id);
                $items = $this->outsource_model->getOrderItemForOutsource($orders_id);
                foreach ($items as $key => $value) {
                    $type_item = $value['type_item'];
                    $items_id = $value['item_id'];
                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/'.$info['images']);
                        }
                        $price_processing = $info['price_processing'];
                    } else if ($type_item == "items") {
                        $info = $this->items_model->rowItems($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit']);
                        if (!empty($info['avatar'])) {
                            $images = base_url($info['avatar']);
                        }
                        $price_processing = 0;
                    }
                    if (empty($images)) {
                        $images = base_url('assets/images/tnh/no_image.png');
                    }
                    $items[$key]['unit'] = $unit['unit'];
                    $items[$key]['images'] = $images;
                    $items[$key]['price_processing'] = $price_processing;
                }
            }
            $data['items'] = $items;
        }
        echo json_encode($data);
    }

    public function refereshReferenceOutsource()
    {
        $data = [];
        if ($this->input->get('referesh'))
        {
            $reference_no = getReference('outsource');
            if ($this->outsource_model->checkExistOutsource($reference_no)) {
                $ct = countReferenceMinus('outsource');
                $this->db->select("MAX(right(tbl_outsource.reference_no, char_length(tbl_outsource.reference_no) - $ct) + 0) as reference_no", false);
                $this->db->from('tbl_outsource');
                $rs = $this->db->get()->row_array();

                $max = $rs['reference_no'];
                $max++;
                updateReferenceNormal('outsource', $max);
                $reference_no = getReference('outsource');
            }
            $data['reference_no'] = $reference_no;
            $data['message'] = lang('tnh_referesh_success');
        }
        echo json_encode($data);
    }

    public function list_import_outsource()
    {
        if (!$this->perViewImportOutsource && !$this->perViewOwnImportOutsource) {
            accessDenied();
        }
        $data['tnh'] = $this->tnh;
        $data['title'] = lang('list_import_outsource');
        $this->load->view('admin/outsource/list_import_outsource', $data);
    }

    public function getImportOutsource()
    {

        $arrIDStaff = employee_manage_staff();
        $this->datatables->select("
            tbl_import_outsource.id as id,
            tbl_import_outsource.date as date,
            CONCAT(tbl_import_outsource.reference_no,'__',tblbranch.name) as reference_no,
            tbl_outsource.reference_no as reference_outsource,
            CONCAT(tbltransfer_warehouse.prefix, ' ', tbltransfer_warehouse.code) as reference_transfer,
            CONCAT(tblexport_different.prefix, ' ', tblexport_different.code) as reference_exportDiff,
            tbl_import_outsource.enter_name as enter_name,
            ws_from.name as warehouse_from,
            ws_to.name as warehouse_to,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as employee,
            tblsuppliers.company as company,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as created_by,
            tbl_import_outsource.status as status,
            tbl_import_outsource.warehouseman_id as status_warehouse,
            tbl_import_outsource.note as note,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as user_status,
        ", FALSE)

            ->from('tbl_import_outsource')
            ->join('tbl_outsource', 'tbl_outsource.id = tbl_import_outsource.outsource_id', 'left')
            ->join('tblsuppliers', 'tblsuppliers.id = tbl_import_outsource.supplier_id', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_import_outsource.id_branch', 'left')
            ->join('tblwarehouse as ws_from', 'ws_from.id = tbl_import_outsource.warehouse_from', 'left')
            ->join('tblwarehouse as ws_to', 'ws_to.id = tbl_import_outsource.warehouse_to', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_outsource.created_by', 'left')
            ->join('tblstaff employees', 'employees.staffid = tbl_outsource.employee_id', 'left')
            ->join('tblstaff staff_status', 'staff_status.staffid = tbl_outsource.user_status', 'left')
            ->join('tbltransfer_warehouse', 'tbltransfer_warehouse.import_outsource_id = tbl_import_outsource.id', 'left')
            ->join('tblexport_different', 'tblexport_different.id_import_outsource = tbl_import_outsource.id', 'left');

            if (!$this->perViewImportOutsource) {
                if ($arrIDStaff != array()) {
                    $coverStr = implode(",", $arrIDStaff);
                    $this->datatables->where('tbl_import_outsource.created_by IN (' . $coverStr . ')');
                }
            } else {
                if (!$this->isAdmin) {
                    if ($this->branchID == 1) {
                        if ($arrIDStaff) {
                            $coverStr = implode(",", $arrIDStaff);
                            $this->db->where('(tbl_import_outsource.created_by IN (' . $coverStr . ')  OR tbl_import_outsource.id_branch != -1 )');
                        }
                    } else {
                        if ($arrIDStaff) {
                            $coverStr = implode(",", $arrIDStaff);
                            $this->db->where('(tbl_import_outsource.created_by IN (' . $coverStr . ')  OR tbl_import_outsource.id_branch = ' . $this->branchID . ')');
                        }
                    }
                }
            }

        $view = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/outsource/view_import_outsource/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> '.lang('view').'</a>';

        $delete = $this->perDeleteImportOutsource ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\''.base_url('admin/outsource/deleteImportOutsource/$1').'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
            <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
        "><i class="fa fa-remove width-icon-actions"></i> '.lang('delete').'</a>' : '';

        $print = '';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            '.lang('actions').'
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>'.$view.'</li>
                <li class="not-outside">'.$delete.'</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $data = json_decode($this->datatables->generate());
        echo json_encode($data);
    }

    public function deleteImportOutsource($id)
    {
        if (!$this->perDeleteImportOutsource) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        $data = [];
        if ($id) {
          
            $importOutsource = $this->outsource_model->rowImportOutsource($id);
            $outsource = $this->outsource_model->rowOutSourceById($importOutsource['outsource_id']);
            $items = $this->outsource_model->getImportOutsourceItems($id);

            $tranfer = $this->outsource_model->rowTransferWarehouse($id);
            $ckExportTranfer = get_table_where('tblwarehouse_product', array('import_id' => $tranfer['id'], 'quantity_export >' => 0,'type_export' => 2), '', 'row');
            if (!empty($ckExportTranfer)) {
                $data['result'] = 0;
                $data['message'] = lang('Phiếu nhập kho này đã được sự dụng');
                echo json_encode($data); die;
            }
            $test_quantity = get_table_where('tblwarehouse_product',array('import_id'=>$id,'quantity_export >'=>0,'type_export'=>31),'','row');
            $stage_name = '';
            $check_item = get_table_where('tbl_import_outsource_items',['import_outsource_id'=>$id],'','row_array','','stage_id_default');
            if(!empty($check_item)){
                $stage = get_table_where('tbl_stages',['id'=>$check_item['stage_id_default']],'','row_array');
                if(!empty($stage)){
                    $stage_name  = $stage['name'];
                }
            }
            if(!empty($test_quantity))
            {
                $data['result'] = 0;
                $data['message'] = lang('Thành phẩm ở công đoạn '.$stage_name.' đã xuất ra sử dụng không thể bỏ duyệt hoặc xoá phiếu');
                $items = get_items_import_outsource($id);
                $data['items'] = $items;
                echo json_encode($data); die;
            }

            // if ($importOutsource['status'] == "un_approved") {
            if ($this->outsource_model->deleteImportOutsource($id)) {
                $this->outsource_model->deleteImportOutsourceItems($id);

              

                $this->db->where('id_import_outsource',$id);
                $exportDifferents = $this->db->get('tblexport_different')->result_array();
                if(!empty($exportDifferents)){
                    foreach($exportDifferents as $k => $v){
                        $this->export_different_model->delete($v['id']);
                    }
                }

                foreach ($items as $key => $value) {
                    $this->outsource_model->updateQuantityOutsourceItems($value['outsource_item_id'], $value['quantity'], $minus = 1);

                    //update pod stage
                    if ($this->outsource_model->checkExistOutsourceByPod($value['pod_id']) == 0){
                        $this->outsource_model->updateNotFinishedStagesImportOutsourcing($value);
                    }
                    //end
                }

                $workflow = 1;
                if ($this->outsource_model->checkExistsOutsourceInImport($importOutsource['outsource_id'])) $workflow = 2;
                $this->outsource_model->updateQuantityOutsource($importOutsource['outsource_id'], $importOutsource['total_quantity'], $workflow, $minus = 1);

                $this->transfer_model->delete($tranfer['id']);
                
                if(!empty($importOutsource['warehouseman_id'])){
                    $this->outsource_model->decreaseWarehouse($id);
                }

              

                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('browsed_cannot_be_deleted');
            }
            // } else {
            //     $data['result'] = 0;
            //     $data['message'] = lang('Đã duyệt không thể xoá');
            // }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Không tồn tại phiếu');
        }
        echo json_encode($data);
    }

    public function view_import_outsource($id) {
        $importOutsource = $this->outsource_model->rowImportOutsource($id);
        $outsource = $this->outsource_model->rowOutSourceById($importOutsource['outsource_id']);
        $items = $this->outsource_model->getImportOutsourceItems($id);
        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products" || $type_item == 'semi_products' || $type_item == 'semi_products_outside') {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/'.$info['images']);
                    }
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $location_from = $this->site_model->rowLocationWarehouseById($value['location_from']);
                $location_to = $this->site_model->rowLocationWarehouseById($value['locaiton_to']);

                $type_name  = "";
                if($type_item == 'products'){
                    $type_name = '<span class="label label-success">'.lang($type_item).'</span>';
                } else if($type_item == 'semi_products'){
                    $type_name = '<span class="label label-success">'.lang($type_item).'</span>';
                } else if($type_item == 'semi_products_outside' ){
                    $type_name = '<span class="label label-success">'.lang($type_item).'</span>';
                } else {
                    $type_name = '<span class="label label-primary">'.lang('ch_items').'</span>';
                }

                $tdNumber = '<td class="text-center">'.(++$key).'</td>';
                $tdImages = '<td>
                    <div style="display: flex;justify-content: center;" class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px;"><a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="'.$images.'" style="border-radius: 50%"></div></a></div></div></div></div>
                </td>';
                $tdCode = '<td>'.$info['name'].'('.$info['code'].')'.'<div class="type-item mtop10">'.$type_name.'</div></td>';
                $tdName = '<td>'.$info['name'].'</td>';
                $tdLocationFrom = '<td>'.$location_from['name'].'</td>';
                $tdLocationTo = '<td>'.$location_to['name'].'</td>';
                $tdUnit = '<td class="text-center">'.$unit['unit'].'</td>';
                $tdQuantity = '<td class="text-center">'.formatNumber($value['quantity']).'</td>';
                $tdUnitPrice = '<td class="text-right">'.formatMoney($value['price']).'</td>';
                $tdUnitPriceImport = '<td class="text-right">'.formatMoney($value['price_import']).'</td>';
                $tdTotalAmount = '<td class="text-right">'.formatMoney($value['amount']).'</td>';
                $tdNote = '<td>'.$value['note_item'].'</td>';

                $bodyItems.= '<tr>
                    '.$tdNumber.'
                    '.$tdImages.'
                    '.$tdCode.'
                    '.$tdLocationTo.'
                    '.$tdUnit.'
                    '.$tdQuantity.'
                    '.$tdNote.'
                </tr>';
            }
        }

        $data['bodyItems'] = $bodyItems;

        $data['transfer'] = $this->site_model->rowTranferByImportOutsourceId($id);
        $data['warehouseFrom'] = $this->site_model->rowWarehouseById($importOutsource['warehouse_from']);
        $data['warehouseTo'] = $this->site_model->rowWarehouseById($importOutsource['warehouse_to']);
        $data['employee'] = !empty($importOutsource['employee_id']) ? get_staff_full_name($importOutsource['employee_id']) : '';
        $data['created_by'] = get_staff_full_name($importOutsource['created_by']);
        $data['updated_by'] = !empty($importOutsource['updated_by']) ? get_staff_full_name($importOutsource['updated_by']) : '';
        $data['user_status'] = !empty($importOutsource['user_status']) ? get_staff_full_name($importOutsource['user_status']) : '';
        $data['id'] = $id;
        $data['importOutsource'] = $importOutsource;
        $data['outsource'] = $outsource;
        $this->load->view('admin/outsource/view_import_outsource', $data);
    }

    public function agreeImportOutsource()
    {
        $data = [];
        if ($this->input->get())
        {
            $import_outsource_id = $this->input->get('import_outsource_id');
            $status = $this->input->get('status');
            $importOutsource = $this->outsource_model->rowImportOutsource($import_outsource_id);
            $date = date('Y-m-d H:i:s');
            $user_id = get_staff_user_id();

            if ($importOutsource['status'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data); die;
            }
            if (!empty($importOutsource['warehouseman_id'])) {
                $data['result'] = 0;
                $data['message'] = lang('Đã duyệt kho không thể bỏ duyệt');
                echo json_encode($data); die;
            }
            $up = $this->outsource_model->updateImportOutsource($import_outsource_id, [
                'status' => $status,
                'date_status' => $date,
                'user_status' => $user_id
            ]);
            if ($up) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function searchProductAndSemiProduct($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $semi_products_outside = $this->outsource_model->searchProductAndSemiProductsAndUnit($term, $limit, 'semi_products_outside');
        $semi_products = $this->outsource_model->searchProductAndSemiProductsAndUnit($term, $limit, 'semi_products');
        $products = $this->outsource_model->searchProductAndSemiProductsAndUnit($term, $limit, 'products');

        $results = [];
        if (!empty($products)) {
            $results[] = ['text' => lang('products'), 'children' => $products];
        }

        if (!empty($semi_products_outside)) {
            $results[] = ['text' => lang('semi_products_outside'), 'children' => $semi_products_outside];
        }
        if (!empty($semi_products)) {
            $results[] = ['text' => lang('semi_products'), 'children' => $semi_products];
        }
        $data['results'] = $results;

        if ($id) {
            $arr = explode('__', $id);
            $type_item = $arr[0];
            $item_id = $arr[1];
            if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                $info = $this->products_model->rowProduct($item_id);
            } else if ($type_item == "materials") {
                $info = $this->items_model->rowMaterial($item_id);
            } else if ($type_item == "tools_supplies") {
                $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
            }
            $data['row'] = ['id' => $info['id'], 'text' => $info['code']];
        }
        echo json_encode($data);
    }

    public function searchItemsOutside($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $material = $this->outsource_model->searchMaterialsAndUnit($term, $limit);
        $semi_product_outside = $this->outsource_model->searchSemiProductsOutsideAndUnit($term, $limit, ['semi_products_outside']);
        $tools_supplies = $this->outsource_model->searchToolsSuppliesAndUnit($term, $limit);

        $results = [];
        if (!empty($material))
        {
            $results[] = ['text' => lang('tnh_materials'), 'children' => $material];
        }
        if (!empty($semi_product_outside)) {
            $results[] = ['text' => lang('semi_products_outside'), 'children' => $semi_product_outside];
        }
        if (!empty($tools_supplies)) {
            $results[] = ['text' => lang('tnh_tools_supplies'), 'children' => $tools_supplies];
        }
        $data['results'] = $results;

        if ($id) {
            $arr = explode('__', $id);
            $type_item = $arr[0];
            $item_id = $arr[1];
            if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                $info = $this->products_model->rowProduct($item_id);
            } else if ($type_item == "materials") {
                $info = $this->items_model->rowMaterial($item_id);
            } else if ($type_item == "tools_supplies") {
                $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
            }
            $data['row'] = ['id' => $info['id'], 'text' => $info['code']];
        }
        echo json_encode($data);
    }

    public function loadBom()
    {
        $data = [];
        if ($this->input->post())
        {
            $counter = $this->input->post('counter');
            $itemsBom = [];
            if (!empty($counter))
            {
                foreach ($counter as $key => $value) {
                    $item_id = $this->input->post('item_id')[$key];
                    $arr = explode('__', $item_id);
                    $item_id = $arr[1];
                    $item_type = $arr[0];
                    $quantity = number_unformat($this->input->post('quantity_outsource')[$key]);
                    if ($item_type == "products" || $item_type == "semi_products" || $item_type == "semi_products_outside")
                    {
                        $product = $this->products_model->rowProduct($item_id);
                        $versions_bom = $product['versions'];
                        if (!empty($versions_bom)) {
                            $version = $this->products_model->getBomByProductIdAndVersions($item_id, $versions_bom);
                            $elements = $this->products_model->getVersionsElementByVersionId($version['id']);
                            if (!empty($elements))
                            {
                                foreach ($elements as $k => $val) {
                                    $quantity_element = $val['quantity'];
                                    $total_quantity_element = $quantity * $quantity_element;
                                    $element_items = $this->products_model->getElementItemsByElementId($val['id']);
                                    if (!empty($element_items)) {
                                        foreach ($element_items as $i => $el) {
                                            $quantity_single = $el['quantity'];
                                            $total_quantity_item = $total_quantity_element * $quantity_single;

                                            if ($el['type'] == "semi_products")
                                            {
                                                //Bán thành phẩm quy đổi sang nvl
                                                $semi_p = $this->products_model->rowProduct($el['item_id']);
                                                $version_semi = $this->products_model->getBomByProductIdAndVersions($el['item_id'], $semi_p['versions']);
                                                if (!empty($version_semi)) {
                                                    $elements_semi = $this->products_model->getVersionsElementByVersionId($version_semi['id']);
                                                    if (!empty($elements_semi)) {
                                                        foreach ($elements_semi as $ksm => $vsm) {
                                                            $quantity_element_semi = $total_quantity_item * $vsm['quantity'];
                                                            $items_semi = $this->products_model->getElementItemsByElementId($vsm['id']);
                                                            foreach ($items_semi as $nn => $vv) {
                                                                $info_material = $this->items_model->rowMaterial($vv['item_id']);
                                                                $unit_id_material = $vv['unit_id'];
                                                                $quantity_single_sub = $vv['quantity'];
                                                                $quantity_sub = $quantity_element_semi * $quantity_single_sub;
                                                                $unit_parent_id = $info_material['unit_id'];
                                                                $row_exchange = $this->products_model->rowExchangeItems($vv['item_id'], $unit_id_material);
                                                                $quantity_exchange = 1;
                                                                if (!empty($row_exchange)) {
                                                                    $quantity_exchange = $row_exchange['number_exchange'];
                                                                }
                                                                $quantity_primary = $quantity_sub/$quantity_exchange;

                                                                //cộng dồn tồn tại mặt hàng
                                                                $keyCustom = $vv['type'].'__'.$vv['item_id'];
                                                                if (!empty($itemsBom[$keyCustom]))
                                                                {
                                                                    $itemsBom[$keyCustom]['quantity'] = $itemsBom[$keyCustom]['quantity'] + $quantity_primary;
                                                                } else {
                                                                    $cUnit = $this->unit_model->rowUnit($info_material['unit_id']);
                                                                    $itemsBom[$keyCustom] = [
                                                                        'id' => $keyCustom,
                                                                        'name' => $info_material['name'],
                                                                        'unit_name' => $cUnit['unit'],
                                                                        'quantity' => $quantity_primary
                                                                    ];
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                            } else if ($el['type'] == "semi_products_outside") {
                                                //Bán thành phẩm mua ngoài
                                                $quantity_primary = $total_quantity_item;

                                            } else if ($el['type'] = "materials") {
                                                //Nguyên vật liệu
                                                $info = $this->items_model->rowMaterial($el['item_id']);
                                                $unit_id = $el['unit_id'];
                                                $unit_parent_id = $info['unit_id'];
                                                $row_exchange = $this->products_model->rowExchangeItems($el['item_id'], $unit_id);
                                                $quantity_exchange = 1;
                                                if (!empty($row_exchange)) {
                                                    $quantity_exchange = $row_exchange['number_exchange'];
                                                }
                                                $quantity_primary = $total_quantity_item/$quantity_exchange;
                                            }
                                            if ($el['type'] == "semi_products") continue;
                                            //cộng dồn tồn tại mặt hàng
                                            $keyCustom = $el['type'].'__'.$el['item_id'];
                                            if (!empty($itemsBom[$keyCustom]))
                                            {
                                                $itemsBom[$keyCustom]['quantity'] = $itemsBom[$keyCustom]['quantity'] + $quantity_primary;
                                            } else {
                                                $cUnit = $this->unit_model->rowUnit($info['unit_id']);
                                                $itemsBom[$keyCustom] = [
                                                    'id' => $keyCustom,
                                                    'name' => $info['name'],
                                                    'unit_name' => $cUnit['unit'],
                                                    'quantity' => $quantity_primary
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $data['itemsBom'] = $itemsBom;
            // print_arrays($itemsBom);
        }
        echo json_encode($data);
    }

    public function payment_processing($id) {
        $outsource = $this->outsource_model->rowOutSourceById($id);
        if ($this->input->post())
        {

            $data = $this->input->post();
            if(str_replace(',', '', $data['payment']) == 0){
                echo json_encode(array(
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => 'Số lượng thanh toán phải lơn hơn 0 !'
                ));die;
            }
            $importsS = get_table_where('tbl_outsource',array('id'=>$id),'','row');
            if(($importsS->grand_total - $importsS->amount_paid) < str_replace(',', '', $data['payment']))
            {
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'warning',
                    'message' => 'Có sự thay đổi về giá trị vui lòng xem lại !'
                ));die;
            }
            $success = false;
            $alert_type = 'warning';
            $message    = _l('ch_added_successfuly_not');
            // if (!has_permission('pay_slip', '', 'create')) {
            //     echo json_encode(array(
            //         'success' => $success,
            //         'alert_type' => $alert_type,
            //         'message' => 'Bạn không có quyền tạo phiếu chi mua hàng!'
            //     ));die;
            // }
            $_data['day_vouchers'] = to_sql_date($data['day_vouchers']);
            $_data['date'] = date('Y-m-d H:i:s');
            $_data['staff_id'] = get_staff_user_id();
            $_data['receiver'] = $data['receiver'];
            $_data['id_costs'] = $data['id_costs'];
            $_data['payment_mode'] = $data['payment_mode'];
            $_data['payment'] = str_replace(',', '', $data['payment']);
            $_data['total'] = str_replace(',', '', $data['total']);
            $_data['note'] = $data['note'];
            $imports = get_table_where('tbl_outsource',array('id'=>$id),'','row');
            $_data['id_supplierss'] = $imports->supplier_id;
            $_data['type'] = 5;
            $_data['id_old'] = $id;
            $_data['prefix'] = get_option('prefix_pay_slip');
            $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
            $this->db->insert('tblpay_slip',$_data);
            $id_pay = $this->db->insert_id();
            if($id_pay)
            {
                $__data['id_old'] = $id;
                $__data['id_pay_slip'] = $id_pay;
                $__data['type'] = 5;
                $__data['total'] = str_replace(',', '', $data['total']);
                $__data['payment'] = str_replace(',', '', $data['payment']);
                $this->db->insert('tblpay_slip_detail',$__data);
                $import = get_table_where('tbl_outsource',array('id'=>$id),'','row');
                $amount_paid =  $import->amount_paid + $__data['payment'];
                if($amount_paid == $import->grand_total){
                    $status = 2;
                } elseif($amount_paid != $import->grand_total){
                    $status = 1;
                }else{
                    $status = 0;
                }
                $this->db->update('tbl_outsource',array('amount_paid'=>$amount_paid,'status_pay'=>$status),array('id'=>$import->id));
                $success = true;
                $alert_type = 'success';
                $message    = _l('ch_added_successfuly');
            }
            echo json_encode(array(
                'success' => $success,
                'alert_type' => $alert_type,
                'message' => $message
            ));die;
        } else {
            $success = false;
            $alert_type = 'warning';
            $message    = _l('ch_added_successfuly_not');
            // if (!has_permission('pay_slip', '', 'create')) {
            //     echo json_encode(array(
            //         'success' => $success,
            //         'alert_type' => $alert_type,
            //         'message' => 'Bạn không có quyền tạo phiếu chi mua hàng!'
            //     ));die;
            // }
            $this->load->model('costs_model');
            $data['costs'] = array();
            $this->costs_model->get_by_id(0, $data['costs']);
            $data['outsource'] = $outsource;
            $rest = $outsource['grand_total'] - $outsource['amount_paid'];
            $rest = $rest < 0 ? 0 : $rest;
            $data['rest'] = $rest;
            $data['payment_modes'] = get_table_where('tblpayment_modes', array('active'=>1));
            $data['id'] = $id;
            $this->load->view('admin/outsource/payment_processing', $data);
        }
    }

    public function gen_barcode($code = NULL, $bcs = 'code128', $height = 30, $text = 1)
    {
        // ob_end_clean();
        $drawText = ($text != 1) ? FALSE : TRUE;
        $this->load->library('zend');
        $this->zend->load('Zend/Barcode');
        $barcodeOptions = array('text' => $code, 'barHeight' => $height, 'drawText' => $drawText);
        $rendererOptions = array('horizontalPosition' => 'center', 'verticalPosition' => 'middle');
        //Zend_Barcode::render('code128', 'image', $barcodeOptions, $rendererOptions);
        $renderer = Zend_Barcode::factory('code128', 'image', $barcodeOptions, $rendererOptions);
        $file = $renderer->draw();
        $pathName = 'file/barcode/barcode_po.png';
        $store_image = @imagepng($file, $pathName);
        return $pathName;
    }

    public function print_outsource($id) {
        ob_end_clean();
        $data = [];

        $outsource = $this->outsource_model->rowOutSourceById($id);
        $items = $this->outsource_model->getOutSourceItemsByOutsourceId($id);
        $supplier = $this->site_model->rowSupplier($outsource['supplier_id']);
        $warehouse = $this->site_model->rowWarehouseById($outsource['warehouse_id']);
        $employee = !empty($outsource['employee_id']) ? get_staff_full_name($outsource['employee_id']) : '';
        $data['title'] = lang('outsource');
        $data['type'] = 'P';
        $data['img'] = '';

        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {

                $type_item = $value['type_item'];
                $items_id = $value['item_id'];

                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/'.$info['images']);
                    }
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                }

                $tdNumber = '<td class="text-center" style="width: 6%;">'.(++$key).'</td>';
                $tdCode = '<td style="width: 25%;">
                    '.$info['code'].'
                </td>';
                $tdName = '<td style="width: 25%;">
                    '.$info['name'].'
                </td>';
                $tdUnit = '<td class="text-center" style="width: 10%;">'.$unit['unit'].'</td>';
                $tdQuantity = '<td class="text-center" style="width: 15%;">'.formatNumber($value['quantity']).'</td>';
                $tdNoteItem = '<td style="width: 19%;">'.$value['note_item'].'</td>';

                $bodyItems.= '<tr nobr="true">
                    '.$tdNumber.'
                    '.$tdCode.'
                    '.$tdName.'
                    '.$tdUnit.'
                    '.$tdQuantity.'
                    '.$tdNoteItem.'
                </tr>';
            }
        }

        $bodyMaterials = '';
        $totalQuantityMaterial = 0;
        $materials = $this->outsource_model->getOutsourceMaterial($id);
        if (!empty($materials)) {
            foreach ($materials as $key => $value) {
                $type_item = $value['type_item'];
                $item_id = $value['item_id'];
                if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                    $info = $this->products_model->rowProduct($item_id);
                } else if ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($item_id);
                } else if ($type_item == "tools_supplies") {
                    $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                }
                $unit = $this->unit_model->rowUnit($info['unit_id']);

                $tdNumber = '<td class="text-center" style="width: 6%;">'.(++$key).'</td>';
                $tdCode = '<td style="width: 30%;">
                    '.$info['code'].'
                </td>';
                $tdName = '<td style="width: 30%;">
                    '.$info['name'].'
                </td>';
                $tdUnit = '<td class="text-center" style="width: 15%;">'.$unit['unit'].'</td>';
                $tdQuantity = '<td class="text-center" style="width: 19%;">'.formatNumber($value['quantity']).'</td>';

                $bodyMaterials.= '<tr nobr="true">
                    '.$tdNumber.'
                    '.$tdCode.'
                    '.$tdName.'
                    '.$tdUnit.'
                    '.$tdQuantity.'
                </tr>';

                $totalQuantityMaterial+= $value['quantity'];
            }
        }

        // $day = date_format(date_create($outsource['date']), 'd');
        // $month = date_format(date_create($outsource['date']), 'm');
        // $year = date_format(date_create($outsource['date']), 'Y');

        $day = date('d');
        $month = date('m');
        $year = date('Y');
        $message = "";

        // $barcode = file_get_contents($this->gen_barcode($outsource['reference_no']));
        // $barcode = '<img src="data:image/png;base64,'.base64_encode($barcode).'"/>';
        $barcode = '';
        ob_start();
        stylePdf();

        echo '
            <h1 class="text-center uppercase">'.lang('outsource').'</h1>
            <span class="text-right">
                '.$barcode.'
            </span>
            <table cellspacing="0" cellpadding="3" style="width: 100%;">
                <tr nobr="true">
                    <td style="width: 20%;"><b>'._l('outsource').':</b></td>
                    <td style="width: 80%;">'.$outsource['reference_no'].'</td>
                </tr>
                <tr nobr="true">
                    <td style="width: 20%;"><b>'._l('tnh_supplies').':</b></td>
                    <td style="width: 80%;">'.$supplier['company'].'</td>
                </tr>
                <tr nobr="true">
                    <td style="width: 20%;"><b>'._l('tnh_export_name').':</b></td>
                    <td style="width: 80%;">'.$outsource['export_name'].'</td>
                </tr>
                <tr nobr="true">
                    <td style="width: 20%;"><b>'._l('tnh_warehouses').':</b></td>
                    <td style="width: 80%;">'.$warehouse['name'].'</td>
                </tr>
                <tr nobr="true">
                    <td style="width: 20%;"><b>'._l('tnh_employees_charge').':</b></td>
                    <td style="width: 80%;">'.(!empty($employee) ? $employee : '').'</td>
                </tr>
            </table>
            <br>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr nobr="true">
                        <th class="bold uppercase" colspan="6" style="width: 100%;">'._l('tnh_product_outsource').'</th>
                    </tr>
                    <tr nobr="true">
                        <th class="bold text-center" style="width: 6%;">'._l('tnh_numbers').'</th>
                        <th class="bold text-center" style="width: 25%;">'._l('tnh_product_code').'</th>
                        <th class="bold text-center" style="width: 25%;">'._l('tnh_product_name').'</th>
                        <th class="bold text-center" style="width: 10%;">'._l('tnh_dvt').'</th>
                        <th class="bold text-center" style="width: 15%;">'._l('quantity').'</th>
                        <th class="bold text-center" style="width: 19%;">'._l('note').'</th>
                    </tr>
                </thead>
                <tbody>
                    '.$bodyItems.'
                </tbody>
                <tfoot>
                    <tr nobr="true" class="bold">
                        <th class="text-right" colspan="3">'._l('tnh_grand_total').'</th>
                        <th></th>
                        <th class="text-center">'.formatNumber($outsource['total_quantity']).'</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <br>
            <br>
            <table class="table-material" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr>
                        <th class="bold uppercase" colspan="6" style="width: 100%;">'._l('tnh_materials_need_used').'</th>
                    </tr>
                    <tr>
                        <th class="bold text-center" style="width: 6%;">'._l('tnh_numbers').'</th>
                        <th class="bold text-center" style="width: 30%;">'._l('tnh_material_code').'</th>
                        <th class="bold text-center" style="width: 30%;">'._l('tnh_material_name').'</th>
                        <th class="bold text-center" style="width: 15%;">'._l('tnh_dvt').'</th>
                        <th class="bold text-center" style="width: 19%;">'._l('quantity').'</th>
                    </tr>
                </thead>
                <tbody>
                    '.$bodyMaterials.'
                </tbody>
                <tfoot>
                    <tr class="bold">
                        <th class="text-right" colspan="3">'._l('tnh_grand_total').'</th>
                        <th></th>
                        <th class="text-center">'.formatNumber($totalQuantityMaterial).'</th>
                    </tr>
                </tfoot>
            </table>
            <p class="text-right"><span>Ngày '.$day.' tháng '.$month.' năm '.$year.'</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">'._l('tnh_deliver').'</span><br>
                        <span>'._l('tnh_sign_write_full_name').'</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">'._l('tnh_receiver').'</span><br>
                        <span>'._l('tnh_sign_write_full_name').'</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">'._l('tnh_stocker').'</span><br>
                        <span>'._l('tnh_sign_write_full_name').'</span>
                    </td>
                </tr>
            </table>
        ';


        $content = ob_get_contents();
        ob_end_clean();
        // echo $content;
        // die;
        $data['content'] = $content;
        $pdf = print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }
    public function confirm_warehous()
    {
        if (!has_permission('import_outsource', '', 'approve_warehouse')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not')
            ));die;
        }
        $id = $this->input->post('id');
        $test_quantity = get_table_where('tblwarehouse_product',array('import_id'=>$id,'quantity_export >'=>0,'type_export'=>31),'','row');
        $import = get_table_where('tbl_import_outsource',array('id'=>$id),'','row');
        $stage_name = '';
        $items = get_table_where('tbl_import_outsource_items',['import_outsource_id'=>$id],'','row_array','','stage_id_default');
        if(!empty($items)){
            $stage = get_table_where('tbl_stages',['id'=>$items['stage_id_default']],'','row_array');
            if(!empty($stage)){
                $stage_name  = $stage['name'];
            }
        }
        $warehouseman_id=$this->input->post('warehouseman_id');
        if (!$id) {
            die('ch_no_items');
        }

        $data=array(
            'warehouseman_id'=>get_staff_user_id(),
            'warehouseman_date'=>date('Y-m-d H:i:s')
        );
        if($warehouseman_id)
        {
            if(!empty($test_quantity))
            {
                echo json_encode(array(
                    'alert_type' => 'danger',
                    'message' => _l('Thành phẩm ở công đoạn '.$stage_name.' đã xuất ra sử dụng không thể bỏ duyệt hoặc xoá phiếu')
                ));die;
            }
            if(empty($import->warehouseman_id))
            {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_exsit_cancel_confirm_warehouse')
                ));die;
            }
            $data=array(
                'warehouseman_id'=>NULL,
                'warehouseman_date'=>NULL
            );
        }else
        {
            if(!empty($import->warehouseman_id))
            {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_exsit_confirm_warehouse')
                ));die;
            }
        }
        $success    = $this->db->update('tbl_import_outsource',$data,array('id'=>$id));
        $alert_type = 'warning';
        $message    = _l('ch_no_successful_approval');
        if($warehouseman_id)
        {
            $message    = _l('ch_no_successful_approval_cance');
        }
        if ($success) {
            $get_code = get_table_where('tbl_import_outsource',array('id'=>$id),'','row');
            activity_log_v2('purchase','tbl_import_outsource',$id,$get_code->reference_no ,'Cập nhật trạng thái duyệt kho phiếu nhập gia công ['.$get_code->reference_no.']');
            $alert_type = 'success';
            $message    = _l('ch_successful_approval');
            if($warehouseman_id)
            {
                $message    = _l('ch_successful_approval_cance');
            }
            if(empty($warehouseman_id))
            {
                log_activity('Warehouse items approved [ID warehouse_product: ' . $id);
                $this->outsource_model->increaseWarehouse($id);
            }
            else
            {
                log_activity('Warehouse items cancel approved [ID warehouse_product: ' . $id);
                $this->outsource_model->decreaseWarehouse($id);
            }
        }
        $alert_type = 'success';
        $message    = _l('ch_successful_approval');
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function searchProductByProduction($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $id_branch = $this->input->get('id_branch');
        if(!empty($params['outsourceId'])){
            $outsourceId = $params['outsourceId'];
            $stage_id_text = 0;
            // $stage_id_text = $params['stage_id'];
        }
        $id_customer = 0;
        if (!empty($customer_ids)) {
            $customer_id = $customer_ids;
        } else {
            $customer_id = $this->input->get('customer_id');
            if (!empty($customer_id)) {
                $customer_id = explode('__', $customer_id);
                $id_customer = $customer_id[1];
            }
        }

        $product = false;
    
        if(!empty($outsourceId)){
            $qtOutsource = '
            COALESCE(
            (SELECT SUM(tbl_productions_orders_details.qty_outsource) 
            WHERE tbl_productions_orders_details.id NOT IN ('.$outsourceId.') ),0)
            ';
            // $tbQC= "(
            //     SELECT
            //     tbl_outsource_items.pod_id as pod_id,
            //         GROUP_CONCAT(CONCAT(tbl_stages.name,'__',tbl_outsource_items.quantity) SEPARATOR 'FF') as name_stages
            //     FROM tbl_outsource_items
            //     LEFT JOIN tbl_stages on  tbl_stages.id = tbl_outsource_items.id_stage
            //     WHERE tbl_outsource_items.id_stage  NOT IN ('$stage_id_text')
            // ) tb_pod";
        } else {
            $qtOutsource = '
            COALESCE(
            (SELECT SUM(tbl_productions_orders_details.qty_outsource) ),0)
            ';
            // $tbQC= "(
            //     SELECT
            //     tbl_outsource_items.pod_id as pod_id,
            //         GROUP_CONCAT(CONCAT(tbl_stages.name,'__',tbl_outsource_items.quantity) SEPARATOR 'FF') as name_stages
            //     FROM tbl_outsource_items
            //     LEFT JOIN tbl_stages on  tbl_stages.id = tbl_outsource_items.id_stage
            // ) tb_pod";
        }
        $this->db->select('
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
        '.$qtOutsource.' as qty_outsource,
        tbl_productions_orders_details.id as pod_id,
        tbl_colors.name as name_color,
        tbl_productions_orders_details.object_type as object_type,
        tbl_business_plan.id as plan_id,
        tbl_orders.id as idd',
            false
        );

        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items',
            'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id ', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
        $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
        $this->db->join('tbl_products',
            'tbl_products.id = tbl_productions_orders_items.items_id ',
            'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
        $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id', 'left');
        // $this->db->join($tbQC, 'tb_pod.pod_id = tbl_productions_orders_details.id', 'left');
        // $this->db->having('(total_qty-qty_outsource) > 0');
        if($id_branch != 1){
            $this->db->where('tbl_productions_orders.location_id',$id_branch);
        }
        if(!empty($term)){
            $this->db->group_start();
            $this->db->where('tbl_productions_orders_details.reference_no like "%' . $term . '%"');
            $this->db->or_where('tbl_orders.reference_no like "%' . $term . '%"');
            $this->db->or_where('tbl_products.name like "%' . $term . '%"');
            $this->db->or_where('tbl_products.code like "%' . $term . '%"');
            $this->db->group_end();
        }
        $this->db->order_by('tbl_products.name', 'DESC');
        $this->db->group_by('tbl_productions_orders_details.id');
        $product = $this->db->get()->result_array();
        if(!empty($product)){
            foreach ($product as $key => $value){
                $items = [];
                $item_id = explode('__', $value['id']);
                $item_id = $item_id[1];
                $type_item = $item_id[0];
                $info = $this->products_model->rowProduct($item_id);
                $this->db->select('*');
                $this->db->from('tbl_product_stages');
                $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                $this->db->where('tbl_product_stages.product_id', $item_id);
                $stages =  $this->db->get()->row_array();
                if(!empty($stages)){
                    $items = $this->products_model->getProductStagesVersions($stages['id']);
                }
                $product[$key]['stages'] = $items;
                $stage_default = get_table_where('tbl_stages',['status_default_outsource'=>1],'','row_array');
                if(!empty($stage_default)){
                    $product[$key]['stage_default'] = $stage_default['id'];
                } else {
                    $product[$key]['stage_default'] = 0;
                }
            }
        }

        $results = [];
        if (!empty($product)) {
            $results[] = ['text' => lang('products'), 'children' => $product];
        }
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function getQuantityOutsource(){
        $data = [];
        $id_stage = $this->input->get('id_stage');
        $pod_id = $this->input->get('pod_id');
        $outsource_item_id = $this->input->get('outsource_item_id');
        if(!empty($id_stage) && !empty($pod_id)){
            if($outsource_item_id !=0){
                $qtQC = '
                COALESCE(
                (SELECT SUM(tbl_outsource_items.quantity) 
                FROM tbl_outsource_items
                WHERE tbl_outsource_items.id_stage = '.$id_stage.' AND tbl_outsource_items.pod_id = '.$pod_id.' AND tbl_outsource_items.id != '.$outsource_item_id.' ),0)
            ';
            } else {
                $qtQC = '
                    COALESCE(
                    (SELECT SUM(tbl_outsource_items.quantity) 
                    FROM tbl_outsource_items
                    WHERE tbl_outsource_items.id_stage = '.$id_stage.' AND tbl_outsource_items.pod_id = '.$pod_id.' ),0)
                ';
            }
            $this->db->select('tbl_productions_orders_items.quantity,'.$qtQC.' as qty_outsource');
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id','left');
            $this->db->where('tbl_productions_orders_details.id',$pod_id);
            $result = $this->db->get()->row_array();
            if(!empty($result)){
                $data['result'] = $result;
            }
        }
        echo json_encode($data);
    }

    public function SearchItems( $id = '') {

        $data = [];
        $search = $this->input->get('term');
        $type = $this->input->get('type');

        $limit_one = 12;
        $limit_two = 12;
        $limit_three = 12;
        $limit_all = 50;

        if ($type == -1) {

            $this->db->select(
                '
                CONCAT("materials__", tbl_materials.id) as id,
                CONCAT(tbl_materials.name,"(",tbl_materials.code,")") as text,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                tblunits.unit as unit_name,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
           
            $this->db->group_by('tbl_materials.id');
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            }
            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit((100));

            $product_order = $this->db->get('tbl_materials')->result_array();
            if (!empty($product_order)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product_order,
                    ];
            }

            $this->db->select(
                '
                CONCAT(tbl_products.type_products,"__", tbl_products.id) as id,
                CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
                tbl_products.price_sell as price,
                tbl_products.type_products as type,
                tblunits.unit as unit_name,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
        
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            $this->db->where('tbl_products.type_products','semi_products');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit(50);
            $product = $this->db->get('tbl_products')->result_array();

            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Thành phẩm'),
                        'children' => $product,
                    ];
            }
        } else {
            if ($type == 'product') {
                    $this->db->select(
                    '
                    CONCAT(tbl_products.type_products,"__", tbl_products.id) as id,
                    CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
                    tbl_products.price_sell as price,
                    tbl_products.type_products as type,
                    tblunits.unit as unit_name,
                    CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                        false
                    );

                if (!empty($search)) {
                    $this->db->group_start();
                    $this->db->like('tbl_products.name', $search);
                    $this->db->or_like('tbl_products.code', $search);
                    $this->db->group_end();
                } 
                if ($id) {
                    $id = explode('__', $id);
                    $this->db->group_start();
                    $this->db->where('tbl_products.id', $id[1]);
                    $this->db->group_end();
                }
                $this->db->where('tbl_products.type_products','semi_products');
                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
                $this->db->order_by('tbl_products.name', 'DESC');
                $this->db->limit(50);
                $product = $this->db->get('tbl_products')->result_array();
                if (!empty($product)) {
                    $data['results'][] =
                        [
                            'text' => _l('Thành phẩm'),
                            'children' => $product,
                        ];
                }
            } elseif ($type == 'nvl') {

                $this->db->select(
                '
                CONCAT("materials__", tbl_materials.id) as id,
                CONCAT(tbl_materials.name,"(",tbl_materials.code,")") as text,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                tblunits.unit as unit_name,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                    false
                );
             
                if (!empty($search)) {
                    $this->db->group_start();
                    $this->db->like('tbl_materials.name', $search);
                    $this->db->or_like('tbl_materials.code', $search);
                    $this->db->group_end();
                }
                if ($id) {
                    $id = explode('__', $id);
                    $this->db->group_start();
                    $this->db->where('tbl_materials.id', $id[1]);
                    $this->db->group_end();
                }
            
                $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
                $this->db->order_by('tbl_materials.name', 'DESC');
                $this->db->limit((100));

                $product_order = $this->db->get('tbl_materials')->result_array();
                if (!empty($product_order)) {
                    $data['results'][] =
                        [
                            'text' => _l('Nguyên vật liệu'),
                            'children' => $product_order,
                        ];
                }
            } 
        }
        echo json_encode($data);
        die();
    }

    public function getWarehousesLocation()
    {
        $data = [];
        $item_id = $this->input->post('item_id');
        $type_item = $this->input->post('type_item');
        $arr = explode("__",$item_id);
        $item_id = $arr[1];
        $type_item = $arr[0];

        if ($type_item == "materials") {
            $type_item = "nvl";
        } elseif ($type_item == "products" || $type_item == 'semi_products') {
            $type_item = "product";
        } elseif ($type_item == "items") {
            $type_item = "items";
        } else if ($type_item == "tools_supplies") {
            $type_item = "tools";
        }

        $warehouses = $this->site_model->getWarehouse();
        $group = '';
        $option = '';
        $check = 0;
        $checkOption = 0;
        // $option = '<option value=""></option>';
        foreach ($warehouses as $key => $value) {
            $warehouseId = $value['id'];
            if ($group != $warehouseId) {
                if ($group != '') {
                    $option .= '</optgroup>';
                }
                $option .= '<optgroup label="' . $value['name'] . '">';
            }
            $locationWarehouse = $this->site_model->getLocationWarehouseQuantity($warehouseId, $item_id, $type_item);
            if (!empty($locationWarehouse)) {
                $checkOption ++;
                foreach ($locationWarehouse as $k => $val) {
                    if($val['quantity_warehouse'] > 0){
                        $check = $val['id'];
                    } 
                    $option .= '<option data-quantity="' . $val['quantity_warehouse'] . '" value="' . $val['id'] . '">' . $val['name_location'] . ' - ' . formatNumber($val['quantity_warehouse']) . '</option>';
                }
            }

            $group = $warehouseId;
        }
        if ($group != '') {
            $option .= '</optgroup>';
        }
        $data['option'] = $option;
        $data['check'] = $check;
        $data['checkOption'] = $checkOption;
        echo json_encode($data);
    }

    public function searchProduction(){
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $id_branch = $this->input->get('id_branch');
        $id_customer = 0;
        if (!empty($customer_ids)) {
            $customer_id = $customer_ids;
        } else {
            $customer_id = $this->input->get('customer_id');
            if (!empty($customer_id)) {
                $customer_id = explode('__', $customer_id);
                $id_customer = $customer_id[1];
            }
        }
        $product = false;
        $tbProductionsPlanOrdersByOrders = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') SEPARATOR '|||') reference_no_orders
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_productions_plan_orders.object_type = 'orders'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_orders";

        $tbProductionsPlanOrdersByBusinessPlan = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(tbl_business_plan.reference_no SEPARATOR '|||') reference_no_business_plan
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
            WHERE tbl_productions_plan_orders.object_type = 'business_plan'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_business_plan";
        $this->db->select('
        tbl_productions_orders.id as id,
        tbl_productions_orders.reference_no as text,
        tb_orders.reference_no_orders as reference_no_orders,
        tb_business_plan.reference_no_business_plan as reference_no_business_plan',
            false
        );

        $this->db->from('tbl_productions_orders');
        $this->db->join("$tbProductionsPlanOrdersByOrders","tb_orders.productions_order_id = tbl_productions_orders.id","left");
        $this->db->join("$tbProductionsPlanOrdersByBusinessPlan","tb_business_plan.productions_order_id = tbl_productions_orders.id","left");
        if($id_branch != 1){
            $this->db->where('tbl_productions_orders.location_id',$id_branch);
        }
        if(!empty($term)){
            $this->db->group_start();
            $this->db->where('tbl_productions_orders.reference_no like "%' . $term . '%"');
            $this->db->or_where('tb_business_plan.reference_no_business_plan like "%' . $term . '%"');
            $this->db->or_where('tb_orders.reference_no_orders like "%' . $term . '%"');
            $this->db->group_end();
        }
        $this->db->order_by('tbl_productions_orders.id desc');
        $production = $this->db->get()->result_array();

        $results = [];
        if (!empty($production)) {
            $results[] = ['text' => lang('Lệnh sản xuất'), 'children' => $production];
        }
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function searchProductbyProductions(){
        $data = [];
        $production_id = $this->input->post('production_id');

        $product = false;
        if (!empty($production_id)) {
         
            $qtQC = '
            COALESCE(
            (SELECT SUM(tbl_productions_orders_details.qty_qc) ),0)
            ';
            $this->db->select('
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
            '.$qtQC.' as qty_qc,
            tbl_productions_orders_details.id as pod_id,
            tbl_colors.name as name_color,
            tbl_productions_orders_details.object_type as object_type,
            tbl_business_plan.id as plan_id,
            tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
            tbl_orders.id as idd',
                false
            );

            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items',
                'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id ', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
            $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
            $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
            $this->db->join('tbl_products',
                'tbl_products.id = tbl_productions_orders_items.items_id ',
                'left');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
            $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
            $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id', 'left');
            $this->db->where('tbl_productions_orders.id',$production_id);

            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->group_by('tbl_productions_orders_details.id');
            $product = $this->db->get()->result_array();
            if(!empty($product)){
                foreach ($product as $key => $value){
                    $items = [];
                    $item_id = explode('__', $value['id']);
                    $item_id = $item_id[1];
                    $type_item = $item_id[0];
                    $info = $this->products_model->rowProduct($item_id);
                    $this->db->select('*');
                    $this->db->from('tbl_product_stages');
                    $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                    $this->db->where('tbl_product_stages.product_id', $item_id);
                    $stages =  $this->db->get()->row_array();
                    if(!empty($stages)){
                        $items = $this->products_model->getProductStagesVersions($stages['id']);
                    }
                    $product[$key]['stages'] = $items;
                    $stage_default = get_table_where('tbl_stages',['status_default_outsource'=>1],'','row_array');
                    if(!empty($stage_default)){
                        $product[$key]['stage_default'] = $stage_default['id'];
                    } else {
                        $product[$key]['stage_default'] = 0;
                    }
                    
                }
            }
        }

        $results = [];
        if (!empty($product)) {
            $results = $product;
        }
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function searchImportOutsouce(){
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $outsource_id = $params['outsource_id'];
        $products = [];
        if($outsource_id){
            $this->db->select('
            tbl_outsource_items.*,
            CONCAT(tbl_orders.reference_no) as reference_no,
            CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
            tbl_orders.note as note_order
            ');
            $this->db->from('tbl_outsource_items');
            $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_outsource_items.pod_id', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
            $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
            if(!empty($term)){
                $this->db->group_start();
                $this->db->where('tbl_orders.reference_no like "%' . $term . '%"');
                $this->db->or_where('tbl_business_plan.reference_no like "%' . $term . '%"');
                $this->db->or_where('tbl_orders.note like "%' . $term . '%"');
                $this->db->group_end();
            }
            $this->db->where('(tbl_outsource_items.quantity - tbl_outsource_items.qty_ip_outsource) >', 0);
            $this->db->where('outsource_id', $outsource_id);
            $products = $this->db->get()->result_array();

        }
        $results = [];
        if (!empty($products)) {
            $results = $products;
        }
        $data['results'] = $results;
        echo json_encode($data);
    }

     public function searchSupplier($id = false){
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $this->db->select('tblsuppliers.id as id, tblsuppliers.company as text', false);
        $this->db->from('tblsuppliers');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tblsuppliers.company', $term);
            $this->db->or_like('tblsuppliers.company', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $suppliers = $this->db->get()->result_array();
        $data['results'] = $suppliers;
        if ($id) {
            $supplier = get_table_where('tblsuppliers', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $supplier['id'], 'text' => $supplier['company']];
        }
        echo json_encode($data);
    }
}