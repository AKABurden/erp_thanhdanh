<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Coupon_invoice extends AdminController
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
        $this->load->model('coupon_invoice_model');

        $this->types = '*';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('orders');
        $this->datetime_now = time();
        $this->tnh = true;

        $this->perViewCouponInvoice = has_permission('coupon_invoice', '', 'view');
        $this->perViewOwnCouponInvoice = has_permission('coupon_invoice', '', 'view_own');
        $this->perAddCouponInvoice = has_permission('coupon_invoice', '', 'create');
        $this->perDeleteCouponInvoice = has_permission('coupon_invoice', '', 'delete');
    }

    public function index()
    {
        if (!$this->perViewCouponInvoice && !$this->perViewOwnCouponInvoice) {
            accessDenied();
        }

        $data['tnh'] = $this->tnh;
        $data['title'] = lang('coupon_invoice');
        $this->load->view('admin/coupon_invoice/index', $data);
    }

    public function getInvoices()
    {
        $clients_id = $this->input->post('clients_id');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $arrIDStaff = employee_manage_staff();

        // $tbOrders = "(
        //     SELECT
        //         tbl_invoice_items.invoice_id as invoice_id,
        //         SUM(tbl_orders.grand_total_items) as grand_total_items,
        //         SUM(IF (tbl_orders.charge_party = 'customer', tbl_orders.cost_delivery, 0)) as cost_delivery,
        //         SUM(tbl_orders.total_tax) as total_tax,
        //         SUM(tbl_orders.grand_total) as grand_total,
        //         GROUP_CONCAT(tbl_orders.reference_no SEPARATOR '<br>') as reference_orders
        //     FROM tbl_invoice_items
        //     INNER JOIN tbl_orders ON tbl_orders.id = tbl_invoice_items.object_id
        //     GROUP BY tbl_invoice_items.invoice_id
        // ) tb_orders";

        $tbOrders = "(
            SELECT
                tbl_invoice_items.invoice_id as invoice_id,
                SUM(tbl_deliveries.grand_total_items) as grand_total_items,
                0 as cost_delivery,
                SUM(tbl_deliveries.total_tax) as total_tax,
                SUM(tbl_deliveries.additional_costs) as additional_costs,
                SUM(tbl_deliveries.grand_total) as grand_total,
                GROUP_CONCAT(tbl_deliveries.reference_no SEPARATOR '<br>') as reference_orders
            FROM tbl_invoice_items
            INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_invoice_items.object_id
            GROUP BY tbl_invoice_items.invoice_id
        ) tb_orders";

        $aColumns = [
            'tbl_invoices.id as id',
            'tbl_invoices.date as date',
            'tbl_invoices.reference_no as reference_no',
            'tb_orders.reference_orders as reference_orders',
            'tblclients.company as customer_name',
            'tb_orders.grand_total_items as total',
            'tb_orders.total_tax as total_tax',
            'tb_orders.cost_delivery as cost_delivery',
            'tb_orders.additional_costs as additional_costs',
            'tb_orders.grand_total as grand_total',
            'tbl_invoices.status_payment as status_payment',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as created_by',
            'tbl_invoices.note as note',
            '"" as actions',
            'tbl_invoices.total_payment as total_payment',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_invoices';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_invoices.customer_id',
            'LEFT JOIN ' . $tbOrders . ' ON tb_orders.invoice_id = tbl_invoices.id',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_invoices.created_by',
            'LEFT JOIN tblbranch ON tblbranch.id = tbl_invoices.branch_id',
        ];

        if (!$this->perViewCouponInvoice) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                array_push($where, 'AND tbl_invoices.created_by IN (' . $coverStr . ')');
            }
        }

        if (!empty($clients_id)) {
            $clients_id = str_replace('customers__', '', $clients_id);
            array_push($where, ' AND tbl_invoices.customer_id =' . $clients_id);
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, ' AND tbl_invoices.date >= "' . $start_date_search . '"');
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, ' AND tbl_invoices.date <= "' . $end_date_search . '"');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblbranch.name as name_branch'
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $grandTotal = 0;
        $TotalPayment = 0;

        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];

            $name_branch = $aRow['name_branch'];

            $row[0] = $aRow['id'];
            $row[1] = $aRow['date'];
            $row[2] = $aRow['reference_no'].'<div><i style="font-size: 11px; padding-bottom:5px; font-style: italic;">'.($name_branch ? 'CN: '.$name_branch : '').'</i></div>';
            $row[3] = $aRow['reference_orders'];
            $row[4] = $aRow['customer_name'];
            $row[5] = $aRow['total'];
            $row[6] = $aRow['total_tax'];
            $row[7] = $aRow['cost_delivery'];
            $row[8] = $aRow['additional_costs'];
            $row[9] = $aRow['grand_total'];
            $row[10] = $aRow['status_payment'];
            $row[11] = $aRow['created_by'];
            $row[12] = $aRow['note'];

            $grandTotal += $aRow['grand_total'];
            $TotalPayment += $aRow['total_payment'];


            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/coupon_invoice/deleteInvoice/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('coupon_invoice') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[13] = $actions;
            $row[14] = $aRow['total_payment'];
            $output['aaData'][] = $row;
        }
        $output['grandTotal'] = $grandTotal;
        $output['TotalPayment'] = $TotalPayment;
        echo json_encode($output);
    }

    public function getInvoicesOld()
    {
        $clients_id = $this->input->post('clients_id');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $orders = "(
            SELECT
                GROUP_CONCAT(tbl_orders.reference_no SEPARATOR '||')
            FROM tbl_invoice_items
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_invoice_items.object_id
            WHERE tbl_invoices.id = tbl_invoice_items.invoice_id AND tbl_invoices.type = 'orders'
        )";

        $custom_select[3] = $orders;

        $this->datatables->select("
            tbl_invoices.id as id,
            tbl_invoices.date as date,
            tbl_invoices.reference_no as reference_no,
            $orders as reference_orders,
            tbl_invoices.customer_name as customer_name,
            tbl_invoices.total as total,
            tbl_invoices.total_tax as total_tax,
            tbl_invoices.cost_delivery as cost_delivery,
            tbl_invoices.grand_total as grand_total,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as created_by,
            tbl_invoices.note as note,
            ", FALSE)
            ->from('tbl_invoices')
            ->join('tblstaff', 'tblstaff.staffid = tbl_invoices.created_by', 'left');
        // sum note
        if (!empty($clients_id)) {
            $this->datatables->where('tbl_invoices.customer_id', explode('_', $clients_id)[2]);
        }
        if (!empty($start_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_invoices.date, "%Y-%m-%d H:i:s") >=', to_sql_date($start_date_search));
        }

        if (!empty($end_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_invoices.date, "%Y-%m-%d H:i:s") <=', to_sql_date($end_date_search));
        }
        // ./sum note
        $this->datatables->custom_select($custom_select);

        $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/coupon_invoice/deleteInvoice/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('coupon_invoice') . '</a>';

        $actions = '
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $data = json_decode($this->datatables->generate());
        // foreach ($data->aaData as $key => $value) {
        //     // $data->aaData[$key][0] = ++$iDisplayStart;
        // }
        echo json_encode($data);
    }

    public function deleteInvoice($id)
    {
        $data = [];
        if (!$this->perAddCouponInvoice) {
            $data['result'] = 0;
            $data['message'] = lang('Không có quyền truy cập');
            echo json_encode($data);
            die;
        }

        if ($id) {
            $vouchers_coupon_detal = get_table_where('tblvouchers_coupon_detal', array('id_order' => $id), '', 'row');
            if (!empty($vouchers_coupon_detal)) {
                $data['result'] = 0;
                $data['message'] = lang('Phiếu đã thu, Không thể xóa!');
                echo json_encode($data);die;
            }

            $plan_propose = get_table_where('tblplan_propose', array('coupon_invoice_id' => $id), '', 'row', '', 'id');
            if (!empty($plan_propose)) {
                $data['result'] = 0;
                $data['message'] = lang('Đã có đề xuất thu không thể xóa!');
                echo json_encode($data);die;
            }

            $invoice = $this->site_model->rowInvoicesById($id);
            $invoiceItems = $this->site_model->getInvoiceItems($id);

            $this->orders_model->handlingTaxDelivery($id, 'delete');
            if ($this->site_model->deleteInvoices($id)) {
                $this->site_model->deleteInvoiceItems($id);
                // if ($invoice['type'] == "orders") {
                //     foreach ($invoiceItems as $key => $value) {
                //         $tax_id = $value['tax_id_old'];
                //         $tax_name = $value['tax_name_old'];
                //         $tax_rate = $value['tax_rate_old'];
                //         $grand_total = $value['total_item'];
                //         $total_tax = 0;
                //         if ($tax_id > 0) {
                //             $total_tax = $grand_total * ($tax_rate/100);
                //         }
                //         $grand_total = $grand_total + $total_tax + $value['cost_delivery_item'];
                //         $this->orders_model->updateOrdersNew($value['object_id'], [
                //             'type_bills' => 0,
                //             'tax_id' => $tax_id,
                //             'tax_name' => $tax_name,
                //             'tax_rate' => $tax_rate,
                //             'total_tax' => $total_tax,
                //             'grand_total' => $grand_total,
                //         ]);
                //     }
                // }

                insertActivityLog([
                    'type_parent_obj' => 'invoices',
                    'table_obj' => 'tbl_invoices',
                    'id_obj' => $id,
                    'name_obj' => $invoice['reference_no'],
                    'content' => lang('Xóa hóa đơn bán hàng') . ' [' . $invoice['reference_no'] . ']',
                    'actions' => 'delete'
                ]);

                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    // sum note
    public function SearchClient($id = '', $type = '')
    {
        $data = [];
        $search = $this->input->get('term');
        if (empty($type)) {
            $type = $this->input->get('type');
        }
        $limit_one = 20;
        if ($type == 1) {
            $this->db->select(
                '
            tblclients.userid as id,
            tblclients.company as text,
            CONCAT(tblclients.prefix_client,tblclients.code_client) as code_client',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblclients.company', $search);
                $this->db->or_like('CONCAT(tblclients.prefix_client, tblclients.code_client)', $search);
                $this->db->group_end();
            }
            if (!empty($id)) {
                $this->db->where('tblclients.userid', $id);
            }
            $this->db->order_by('tblclients.company', 'DESC');
            $this->db->limit($limit_one);
            $client = $this->db->get('tblclients')->result_array();
            $data['results'] = $client;
        } elseif ($type == 2) {
            $this->db->select(
                '
            tblsuppliers.id as id,
            tblsuppliers.company as text,
            CONCAT(tblsuppliers.prefix,tblsuppliers.code) as code_client',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblsuppliers.company', $search);
                $this->db->or_like('CONCAT(tblsuppliers.prefix, tblsuppliers.code)', $search);
                $this->db->group_end();
            }
            if (!empty($id)) {
                $this->db->where('tblsuppliers.id', $id);
            }
            $this->db->order_by('tblsuppliers.company', 'DESC');
            $this->db->limit($limit_one);
            $suppliers = $this->db->get('tblsuppliers')->result_array();
            $data['results'] = $suppliers;
        } elseif ($type == 3) {
            $this->db->select(
                '
            tblstaff.staffid as id,
            CONCAT(tblstaff.lastname,tblstaff.firstname) as text',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('CONCAT(tblstaff.lastname, tblstaff.firstname)', $search);
                $this->db->group_end();
            }
            if (!empty($id)) {
                $this->db->where('tblstaff.staffid', $id);
            }
            $this->db->limit($limit_one);
            $suppliers = $this->db->get('tblstaff')->result_array();
            $data['results'] = $suppliers;
        }
        echo json_encode($data);
        die();
    }
    // ./sum note


    public function add($id = 0)
    {
        $data = [];
        if (!$this->perAddCouponInvoice) {
            accessDenied(true);
        }
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('reference_bill', lang("tnh_reference_bill"), 'trim|required|is_unique[tbl_invoices.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('customer_bill', lang("tnh_customer"), 'required');
            $this->form_validation->set_rules('tax_id', lang("tnh_taxs"), 'required');
            $this->form_validation->set_rules('orders_bill[]', lang("orders"), 'required');
            if ($this->form_validation->run() == true) {
                $date = to_sql_date($this->input->post('date'), true);
                $reference_no = $this->input->post('reference_bill');
                $tax_id = $this->input->post('tax_id');
                $tax_name = 0;
                $tax_rate = 0;
                $note = $this->input->post('note', false);
                $type = "orders";
                $object_id = $id;
                $status = 'un_approved';

                if (!empty($tax_id)) {
                    $info_tax = $this->site_model->rowTax($tax_id);
                    if (!empty($info_tax)) {
                        $tax_name = $info_tax['name'];
                        $tax_rate = $info_tax['taxrate'];
                    }
                }

                $customer_bill = $this->input->post('customer_bill');
                $customer_bill = str_replace('customers__', '', $customer_bill);
                $row_customer = $this->site_model->rowCustomer($customer_bill);
                $customer_name = $row_customer['company'];

                $orders_bill = $this->input->post('orders_bill');

                $invoiceItems = [];
                $total = 0;
                $total_tax = 0;
                $grand_total = 0;
                $cost_delivery = 0;
                $additional_costs = 0;
                if (!empty($orders_bill)) {
                    foreach ($orders_bill as $key => $value) {
                        // $order = $this->orders_model->rowOrderById($value);
                        $order = $this->deliveries_model->rowDeliveriesById($value);
                        if ($order['type_bills'] == 1) {
                            $data['result'] = 0;
                            $data['message'] = $order['reference_no'] . ' đã tạo hóa đơn thuế';
                            echo json_encode($data);
                            die;
                        }

                        $grand_total_items = $order['grand_total_items'];

                        $tax_id_old = $order['tax_id'];
                        $tax_name_old = $order['tax_name'];
                        $tax_rate_old = $order['tax_rate'];
                        $total_item = $grand_total_items;
                        // $cost_delivery_item = $order['charge_party'] == "customer" ? $order['cost_delivery'] : 0;
                        $cost_delivery_item = 0;
                        $total_tax_item = 0;
                        if ($tax_rate > 0) {
                            $total_tax_item = $total_item * ($tax_rate / 100);
                        }

                        $grand_total_item = $total_item + $total_tax_item + $cost_delivery_item;
                        $additional_cost = $order['additional_costs'];

                        $invoiceItems[] = [
                            'object_id' => $value,
                            'tax_id_old' => $tax_id_old,
                            'tax_name_old' => $tax_name_old,
                            'tax_rate_old' => $tax_rate_old,
                            'total_item' => $total_item,
                            'total_tax_item' => $total_tax_item,
                            'cost_delivery_item' => $cost_delivery_item,
                            'grand_total_item' => $grand_total_item,
                            'additional_cost' => $additional_cost,
                        ];

                        $total += $total_item;
                        $total_tax += $total_tax_item;
                        $grand_total += $grand_total_item;
                        $cost_delivery += $cost_delivery_item;
                        $additional_costs += $additional_cost;
                    }
                }

                if (empty($invoiceItems)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Không có đơn hàng để xuất hóa đơn');
                    echo json_encode($data);
                    die;
                }

                $branch_id = $this->input->post('branch_id');
                $invoice = [
                    'reference_no' => $reference_no,
                    'date' => $date,
                    'date_misa' => !empty($this->input->post('date_misa')) ? to_sql_date($this->input->post('date_misa')) : null,
                    'type' => $type,
                    'customer_id' => $customer_bill,
                    'customer_name' => $customer_name,
                    'object_id' => 0,
                    'tax_id' => $tax_id,
                    'tax_rate' => $tax_rate,
                    'tax_name' => $tax_name,
                    'total' => $total,
                    'total_tax' => $total_tax,
                    'cost_delivery' => $cost_delivery,
                    'grand_total' => $grand_total,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                    'note' => $note,
                    'status' => $status,
                    'additional_costs' => $additional_costs,
                    'branch_id' => $branch_id,
                ];
                $invoice_id = $this->site_model->insertInvoices($invoice);
                if ($invoice_id) {
                    $get_code = get_table_where('tbl_invoices', array('id' => $invoice_id), '', 'row');
                    activity_log_v2('work_debt_sales', 'tbl_invoices', $invoice_id, $get_code->reference_no, 'Thêm mới hóa đơn bán hàng [' . $get_code->reference_no . ']');

                    if (!empty($invoiceItems)) {
                        foreach ($invoiceItems as $key => $value) {
                            $value['invoice_id'] = $invoice_id;
                            $this->site_model->insertInvoiceItems($value);
                        }
                    }

                    $this->orders_model->handlingTaxDelivery($invoice_id, 'add');
                    //handling
                    $this->coupon_invoice_model->handlingPlanProposeCouponInvoice($invoice_id, $invoice);

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
            echo json_encode($data);
        } else {
            $data['id'] = 0;
            $data['branch'] = get_table_where('tblbranch');
            $data['taxs'] = $this->site_model->getTaxs();
            $this->load->view('admin/coupon_invoice/add', $data);
        }
    }

    public function loadOrdersBill()
    {
        $data = [];

        $customer_bill = $this->input->post('customer_bill');
        $customer_bill = str_replace('customers__', '', $customer_bill);

        $this->db->select('
            tbl_deliveries.id as id,
            tbl_deliveries.reference_no as reference_no,
            tbl_deliveries.additional_costs as additional_costs,
            (tbl_deliveries.grand_total_items - coalesce(tbl_deliveries.total_discount_percent, 0) - coalesce(tbl_deliveries.total_discount_direct, 0)) as grand_total_before,
        ', false);
        $this->db->from('tbl_deliveries');
        $this->db->where('tbl_deliveries.customer_id', $customer_bill);
        $this->db->group_start();
        $this->db->where('tbl_deliveries.type_bills', 0);
        $this->db->group_end();
        // $this->db->where('tbl_deliveries.status', 'approved');
        // $this->db->select('
        //     tbl_orders.id as id,
        //     tbl_orders.reference_no as reference_no
        // ', false);
        // $this->db->from('tbl_orders');
        // $this->db->where('tbl_orders.customer_id', $customer_bill);
        // $this->db->group_start();
        // $this->db->where('tbl_orders.type_bills', 0);
        // $this->db->group_end();
        // $this->db->where('tbl_orders.status', 'approved');
        $this->db->where('tbl_orders.type_orders NOT IN (2,4,11)', false, false);
        $this->db->where('tbl_deliveries.id >', 0);
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_deliveries.order_id', 'INNER');
        $orders = $this->db->get()->result_array();
        $data['orders'] = $orders;

        echo json_encode($data);
    }

    public function excel(){
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        $ids = $this->input->post('ids');

        if (empty($ids)){
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng chọn hóa đơn cần xuất');
            echo json_encode($data);die();
        }

        $tb_tamp = "(
            SELECT
                tbl_invoice_items.invoice_id,
                tbl_products.code as code,
                tbl_products.name as name,
                tblunits.unit as unit_name,
                SUM(tbl_delivery_items.quantity) as quantity,
                tbl_delivery_items.price as price,
                SUM(tbl_delivery_items.discount_direct_amount_item) as discount_direct_amount_item

            FROM tbl_delivery_items
            JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
            JOIN tbl_products ON tbl_products.id = tbl_delivery_items.item_id AND tbl_delivery_items.type_item = 'products'
            JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
            JOIN tbl_invoice_items ON tbl_invoice_items.object_id = tbl_deliveries.id
            WHERE tbl_invoice_items.invoice_id IN ($ids)
            GROUP BY tbl_invoice_items.invoice_id,tbl_delivery_items.item_id,tbl_delivery_items.type_item,tbl_delivery_items.price
            ORDER BY tbl_invoice_items.invoice_id
        )";

        $items =$this->db->query($tb_tamp)->result_array();
        $itemsNew = [];
        if (!empty($items)){
            foreach ($items as $key => $value){
                $itemsNew[$value['invoice_id']][] = $value;
            }
        }


        $this->db->select(
            'tbl_invoices.id as id,
            DATE_FORMAT(tbl_invoices.date, "%d/%m/%Y") as date,
            tbl_invoices.reference_no,
            tblclients.company,
            tblclients.address,
            tblclients.vat,
            tblclients.email_client,
            tbl_invoices.tax_rate
            '
        );
        $this->db->from('tbl_invoices');
        $this->db->join('tblclients','tblclients.userid = tbl_invoices.customer_id');
        $this->db->where_in('tbl_invoices.id',explode(',',$ids));
        $dtInvoice = $this->db->get()->result_array();


        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'HÓA ĐƠN BÁN HÀNG');
        $objPHPExcel->getActiveSheet()->mergeCells("B2:J2");

        $objPHPExcel->getActiveSheet()->getStyle("B2")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 20,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Số thứ tự hóa đơn');
        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'Ngày hóa đơn');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', 'Tên đơn vị mua hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('D4', 'Địa chỉ');
        $objPHPExcel->getActiveSheet()->setCellValue('E4', 'Mã số thuế');
        $objPHPExcel->getActiveSheet()->setCellValue('F4', 'Người mua hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('G4', 'Email');
        $objPHPExcel->getActiveSheet()->setCellValue('H4', 'Hình thức thanh toán');
        $objPHPExcel->getActiveSheet()->setCellValue('I4', 'Tên hàng hóa');
        $objPHPExcel->getActiveSheet()->setCellValue('J4', 'ĐVT');
        $objPHPExcel->getActiveSheet()->setCellValue('K4', 'Số lượng');
        $objPHPExcel->getActiveSheet()->setCellValue('L4', 'Đơn giá');
        $objPHPExcel->getActiveSheet()->setCellValue('M4', 'Thành tiền');
        $objPHPExcel->getActiveSheet()->setCellValue('N4', 'Chiết khấu(TM)');
        $objPHPExcel->getActiveSheet()->setCellValue('O4', 'Thuế GTGT');
        $objPHPExcel->getActiveSheet()->setCellValue('P4', 'Tiền thuế GTGT');


        $objPHPExcel->getActiveSheet()->getStyle("B2:P4")->applyFromArray([
            'font' => array(
                'bold' => true,
            )
        ]);

        $objPHPExcel->getActiveSheet()->getStyle("A4:P4")->applyFromArray([
            'font' => array(
                'bold' => true,
            )
        ]);

        $styleTd_left = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $styleTd_center = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ];
        $row = 4;
        $start = 0;
        if (!empty($dtInvoice)) {
            foreach ($dtInvoice as $key => $aRow) {
                $row++;
                $start++;

                $itemCheck = !empty($itemsNew[$aRow['id']]) ? $itemsNew[$aRow['id']] : [];

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $start)->getStyle('A' . $row)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $aRow['date'])->getStyle('B' . $row)->applyFromArray($styleTd_center);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $aRow['company'])->getStyle('C' . $row)->applyFromArray($styleTd_left);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $aRow['address'])->getStyle('D' . $row)->applyFromArray($styleTd_left);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, ($aRow['vat']),PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E' . $row)->applyFromArray($styleTd_left);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, ($aRow['company']))->getStyle('F' . $row)->applyFromArray($styleTd_left);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $aRow['email_client'])->getStyle('G' . $row)->applyFromArray($styleTd_left);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, '');
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, '');
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, '');
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, '')->getStyle("K$row")->getNumberFormat()->setFormatCode('');
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, '')->getStyle("L$row")->getNumberFormat()->setFormatCode('');
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, '')->getStyle("M$row")->getNumberFormat()->setFormatCode('');
                $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, '')->getStyle("N$row")->getNumberFormat()->setFormatCode('');
                $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, '')->getStyle("O$row")->getNumberFormat()->setFormatCode('');
                $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, '')->getStyle("P$row")->getNumberFormat()->setFormatCode('');
                
                if (!empty($itemCheck)){
                    foreach ($itemCheck as $kk => $vv){
                        $total_amount = $vv['quantity'] * $vv['price'];
                        $discount_direct_amount_item = $vv['discount_direct_amount_item'];
                        $totalTax = ($total_amount - $discount_direct_amount_item) * $aRow['tax_rate'] / 100;
                        if (empty($kk)){
                            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $vv['name'])->getStyle('I' . $row)->applyFromArray($styleTd_left);
                            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $vv['unit_name'])->getStyle('J' . $row)->applyFromArray($styleTd_center);
                            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $vv['quantity'])->getStyle("K$row")->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode(formatNumberExcel($vv['quantity']));
                            $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $vv['price'])->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($vv['price']));
                            $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $total_amount)->getStyle("M$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($total_amount));
                            $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $discount_direct_amount_item)->getStyle("N$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($discount_direct_amount_item));

                            $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $aRow['tax_rate'])->getStyle("O$row")->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode($aRow['tax_rate']);
                            $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $totalTax)->getStyle("P$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($totalTax));
                        } else {
                            $row++;
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $start)->getStyle('A' . $row)->applyFromArray($styleTd_center);
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, '');
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, '');
                            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, '');
                            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '');
                            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, '');
                            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, '');
                            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, '');
                            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $vv['name'])->getStyle('I' . $row)->applyFromArray($styleTd_left);
                            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $vv['unit_name'])->getStyle('J' . $row)->applyFromArray($styleTd_center);
                            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $vv['quantity'])->getStyle("K$row")->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode(formatNumberExcel($vv['quantity']));
                            $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $vv['price'])->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($vv['price']));
                            $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $total_amount)->getStyle("M$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($total_amount));
                            $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $discount_direct_amount_item)->getStyle("N$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($discount_direct_amount_item));

                            $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $aRow['tax_rate'])->getStyle("O$row")->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode($aRow['tax_rate']);
                            $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $totalTax)->getStyle("P$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($totalTax));
                        }

                    }
                }
            }
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
        $filename = lang('hoa_don_ban_hang') . '.xls';

        $objPHPExcel->getActiveSheet()->getStyle("A4:P$row")->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        ]);

        $objPHPExcel->getActiveSheet()->getStyle("A2:P$row")->getAlignment()->setWrapText(true);

        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'result' => 1,
            'filename' => $filename,
            'message' => lang('success'),
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));

    }

    public function synthetic_coupon_invoice(){
        if (!$this->perViewCouponInvoice && !$this->perViewOwnCouponInvoice) {
            accessDenied();
        }

        $data['tnh'] = $this->tnh;
        $data['title'] = lang('dt_coupon_invoice');
        $this->load->view('admin/coupon_invoice/synthetic_coupon_invoice', $data);
    }

    public function getSyntheticCouponInvoices(){
        $customer_search = $this->input->post('customer_search');
        $orders_search = $this->input->post('orders_search');
        $delivery_search = $this->input->post('delivery_search');
        $items_search = $this->input->post('items_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $arrIDStaff = employee_manage_staff();

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_tamp_category_client = "(
            SELECT
                tblcustomer_groups.customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
            FROM tblcustomers_groups
            JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
            GROUP BY tblcustomer_groups.customer_id
        ) tb_tamp_category_client";

        $aColumns = [
            'tbl_invoices.reference_no as reference_no_invoice',
            'tbl_invoices.date as date_invoice',
            'tbl_deliveries.reference_no as reference_no_delivery',
            'tbl_deliveries.date as date_delivery',
            'tbl_orders.reference_no as reference_no_order',
            'tbl_orders.reference_no_customer as reference_no_customer',
            'tbl_type_orders.name as name_type_order',
            'tbl_orders.date as date_order',
            'tbl_deliveries.date as date',
            'tblclients.zcode as zcode',
            'tblclients.company as company',
            '(SELECT
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
                FROM tblcustomers_groups
                JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
                WHERE tblclients.userid = tblcustomer_groups.customer_id) as name_group',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_invoices';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_invoice_items ON tbl_invoice_items.invoice_id = tbl_invoices.id',
            'INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_invoice_items.object_id',
            'INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id',
            'INNER JOIN tbl_type_orders ON tbl_type_orders.id = tbl_orders.type_orders',
            'INNER JOIN tblclients ON tblclients.userid = tbl_deliveries.customer_id',
        ];

        if (!empty($items_search)) {
            $items_search = explode('__', $items_search);
            array_push($where,'AND EXISTS (
                SELECT tbl_delivery_items.delivery_id
                FROM tbl_delivery_items
                WHERE tbl_delivery_items.delivery_id = tbl_deliveries.id
                AND tbl_delivery_items.item_id = ' . $items_search[0] . '
            )');
        }

        array_push($where,'AND EXISTS (
               SELECT 1
               FROM tbl_delivery_items_columns
               WHERE tbl_delivery_items_columns.delivery_id = tbl_deliveries.id
        )');

        if (!$this->perViewCouponInvoice) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                array_push($where, 'AND tbl_invoices.created_by IN (' . $coverStr . ')');
            }
        }

        if (!empty($orders_search)) {
            array_push($where,"AND FIND_IN_SET($orders_search, tbl_deliveries.order_id) > 0");
        }

        if (!empty($delivery_search)){
            array_push($where, 'AND tbl_deliveries.id = '.$delivery_search.'');
        }

        if (!empty($customer_search)) {
            array_push($where,'AND tbl_invoices.customer_id = '.$customer_search.'');
        }

        if (!empty($start_date_search)) {
            array_push($where,'AND DATE_FORMAT(tbl_invoices.date, "%Y-%m-%d") >= "'.to_sql_date($start_date_search).'"');
        }

        if (!empty($end_date_search)) {
            array_push($where,'AND DATE_FORMAT(tbl_invoices.date, "%Y-%m-%d") <= "'.to_sql_date($end_date_search).'"');
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_deliveries.id as delivery_id',
            'tbl_invoices.id as id',
            'tbl_invoices.tax_rate as tax_rate',
            'tbl_orders.cost_delivery as cost_delivery',
        ], 'ORDER BY tbl_invoices.id desc', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity = 0;
        $grand_total = 0;
        $grand_total_tax = 0;
        $grand_total_all = 0;
        foreach ($rResult as $key => $aRow) {
            $id_delivery = $aRow['delivery_id'];
            $row = array();
            $tb_tamp = '(
                SELECT 
                    (tb_tamp.delivery_id) as delivery_id,
                    (tb_tamp.delivery_item_id) as delivery_item_id,
                    (tb_tamp.command) as command,
                    SUM(tb_tamp.quantity_put) as quantity_put,
                    SUM(tb_tamp.sample_quantity_item) as sample_quantity_item,
                    SUM(tb_tamp.quantity_loss) as quantity_loss
                FROM (
                    SELECT
                        counter_items_number as counter_items_number,
                        delivery_id as delivery_id,
                        delivery_item_id as delivery_item_id,
                        MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_loss",tbl_delivery_items_columns.columns_name,"")) as "quantity_loss",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "sample_quantity_item",tbl_delivery_items_columns.columns_name,"")) as "sample_quantity_item",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_put",tbl_delivery_items_columns.columns_name,"")) as "quantity_put",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "order_code",tbl_delivery_items_columns.columns_name,"")) as "order_code",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "command",tbl_delivery_items_columns.columns_name,"")) as "command"
                    FROM `tbl_delivery_items_columns` 
                    WHERE tbl_delivery_items_columns.delivery_id = '.$id_delivery.'
                    GROUP BY counter_items_number,delivery_id,delivery_item_id
                ) tb_tamp
                GROUP BY tb_tamp.delivery_id,tb_tamp.command,tb_tamp.delivery_item_id  
            ) as tb_tamp';

            $this->db->select('
                tbl_delivery_items.item_id as item_id,
                tb_tamp.command as command,
                tbl_order_items.product_name_customer as product_name_customer,
                tbl_delivery_items.price as price,
                SUM(tb_tamp.quantity_put) as quantity,
                tbl_order_items.is_lot as is_lot,
                tbl_products.code as code_product,
                tbl_products.name as name_product,
                tbl_products.mode as mode,
                tbl_products.quantity_sheet_bale as quantity_sheet_bale,
                0 as quantity_bale,
                GROUP_CONCAT(distinct(DATE_FORMAT(tbl_order_item_shippings.date_shipping,"%d/%m/%Y"))) as date_shipping,
                tblunits.unit as unit
            ');
            $this->db->from('tbl_delivery_items');
            $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
            $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id');
            $this->db->join($tb_tamp, 'tb_tamp.delivery_item_id = tbl_delivery_items.id');
            $this->db->where('tbl_delivery_items.delivery_id', $id_delivery);
            $this->db->group_by('tbl_delivery_items.item_id, tb_tamp.command');
            $delivery_items = $this->db->get()->result_array();
            if (!empty($delivery_items)){
                foreach ($delivery_items as $kk => $vv){
                    $row = array();
                    $row[] = '<div class="text-left" style="width: 110px">' . $aRow['reference_no_invoice'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 150px">' . _dt($aRow['date_invoice']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 110px">' . $aRow['reference_no_delivery'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 150px">' . _dt($aRow['date_delivery']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . ($aRow['reference_no_order']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">'.$aRow['reference_no_customer'].'</div>';
                    $row[] = '<div class="text-left" style="width: 160px">' . $aRow['name_type_order'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . _dt($aRow['date_order']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . ($vv['date_shipping']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . $aRow['zcode'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . $aRow['company'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 100px">' . $aRow['name_group'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 70px">' . $vv['command'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . $vv['code_product'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . $vv['name_product'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . ($vv['product_name_customer']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 100px">' . ($vv['mode']) . '</div>';
                    $row[] = '<div class="text-center" style="width: 70px">' . ($vv['unit']) . '</div>';
                    $row[] = '<div class="text-center" style="width: 100px">' . formatNumber($vv['quantity_sheet_bale']) . '</div>';
                    $row[] = '<div class="text-center" style="width: 60px">' . formatNumber($vv['quantity_bale']) . '</div>';
                    $row[] = '<div class="text-center" style="width: 60px">' . formatNumber($vv['quantity']) . '</div>';
                    $row[] = '<div class="text-right" style="width: 120px">' . formatMoney($vv['price']) . '</div>';
                    if($vv['is_lot'] == 1){
                        $htmlPrice = '<div class="label label-danger">Theo Giá Lô</div>';
                        $amount = $vv['price'];
                    } else {
                        $htmlPrice = '<div class="label label-primary">Nhập Tay</div>';
                        $amount = $vv['quantity'] * $vv['price'];
                    }
                    $row[] = '<div class="text-center" style="width: 120px">' . $htmlPrice . '</div>';
                    $row[] = '<div class="text-right" style="width: 120px">' . formatMoney($amount) . '</div>';
                    $total_tax = ($amount * $aRow['tax_rate']) / 100;
                    $row[] = '<div class="text-center" style="width: 120px">'.$aRow['tax_rate'].'</div>';
                    $row[] = '<div class="text-right" style="width: 120px">'.($total_tax > 0 ? formatMoney($total_tax) : '').'</div>';
                    $row[] = '<div class="text-right" style="width: 120px">'.($aRow['cost_delivery'] > 0 ? formatMoney($aRow['cost_delivery']) : '').'</div>';
                    $total = $total_tax + $amount + $aRow['cost_delivery'];
                    $row[] = '<div class="text-right" style="width: 120px">'.formatMoney($total).'</div>';

                    $total_quantity += $vv['quantity'];
                    $grand_total += $amount;
                    $grand_total_tax += $total_tax;
                    $grand_total_all += $total;
                    $output['aaData'][] = $row;
                }
            }
        }
        $output['total_quantity'] = $total_quantity;
        $output['grand_total'] = $grand_total;
        $output['grand_total_tax'] = $grand_total_tax;
        $output['grand_total_all'] = $grand_total_all;
        echo json_encode($output);
    }

    public function searchCouponInvoice(){
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;

        $this->db->select('tbl_deliveries.id as id, CONCAT(tbl_deliveries.reference_no, "(", IF(tbl_deliveries.customer_name IS NOT NULL,tbl_deliveries.customer_name,""), ")") as text', false);
        $this->db->from('tbl_deliveries');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_deliveries.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $data['results'] =  $this->db->get()->result_array();
        echo json_encode($data);
    }

    public function exportExcelSyntheticCouponInvoice(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $customer_search = $this->input->post('customer_search');
            $orders_search = $this->input->post('orders_search');
            $delivery_search = $this->input->post('delivery_search');
            $items_search = $this->input->post('items_search');
            $end_date_search = $this->input->post('end_date_search');
            $start_date_search = $this->input->post('start_date_search');
            $arrIDStaff = employee_manage_staff();
            $strDate = 'Từ trước đến nay';
            if (empty($start_date_search) && !empty($end_date_search)) {
                $strDate = '(BAN ĐẦU - ' . $end_date_search . ')';
            }
            if (!empty($start_date_search) && empty($end_date_search)) {
                $strDate = '(' . $start_date_search . ' - HIỆN TẠI' . ')';
            }
            if (!empty($start_date_search) && !empty($end_date_search)) {
                $strDate = '(' . $start_date_search . ' - ' . $end_date_search . ')';
            }
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $tb_tamp_category_client = "(
                SELECT
                    tblcustomer_groups.customer_id,
                    GROUP_CONCAT(tblcustomers_groups.name) as name_group
                FROM tblcustomers_groups
                JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
                GROUP BY tblcustomer_groups.customer_id
            ) tb_tamp_category_client";

            $this->db->select('
                tbl_deliveries.id as delivery_id,
                tbl_invoices.id as id,
                tbl_invoices.tax_rate as tax_rate,
                tbl_orders.cost_delivery as cost_delivery,
                tbl_invoices.reference_no as reference_no_invoice,
                tbl_invoices.date as date_invoice,
                tbl_deliveries.reference_no as reference_no_delivery,
                tbl_deliveries.date as date_delivery,
                tbl_orders.reference_no as reference_no_order,
                tbl_orders.reference_no_customer as reference_no_customer,
                tbl_type_orders.name as name_type_order,
                tbl_orders.date as date_order, 
                tbl_deliveries.date as date, 
                tblclients.zcode as zcode, 
                tblclients.company as company, 
                (SELECT
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
                FROM tblcustomers_groups
                JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
                WHERE tblclients.userid = tblcustomer_groups.customer_id) as name_group, 
            ');
            $this->db->from('tbl_invoices');
            $this->db->join('tbl_invoice_items','tbl_invoice_items.invoice_id = tbl_invoices.id','inner');
            $this->db->join('tbl_deliveries','tbl_deliveries.id = tbl_invoice_items.object_id','inner');
            $this->db->join('tbl_orders','tbl_orders.id = tbl_deliveries.order_id','inner');
            $this->db->join('tbl_type_orders','tbl_type_orders.id = tbl_orders.type_orders','inner');
            $this->db->join('tblclients','tblclients.userid = tbl_deliveries.customer_id','inner');

            if (!empty($items_search)) {
                $items_search = explode('__', $items_search);
                $this->db->where('EXISTS (
                    SELECT tbl_delivery_items.delivery_id
                    FROM tbl_delivery_items
                    WHERE tbl_delivery_items.delivery_id = tbl_deliveries.id
                    AND tbl_delivery_items.item_id = ' . $items_search[0] . '
                )');
            }

            $this->db->where('EXISTS (
               SELECT 1
               FROM tbl_delivery_items_columns
               WHERE tbl_delivery_items_columns.delivery_id = tbl_deliveries.id
            )');

            if (!$this->perViewCouponInvoice) {
                if ($arrIDStaff != array()) {
                    $coverStr = implode(",", $arrIDStaff);
                    $this->db->where('tbl_invoices.created_by IN (' . $coverStr . ')');
                }
            }

            if (!empty($orders_search)) {
                $this->db->where("FIND_IN_SET($orders_search, tbl_deliveries.order_id) > 0");
            }

            if (!empty($delivery_search)){
                $this->db->where('tbl_deliveries.id = '.$delivery_search.'');
            }

            if (!empty($customer_search)) {
                $this->db->where('tbl_invoices.customer_id = '.$customer_search.'');
            }

            if (!empty($start_date_search)) {
                $this->db->where('DATE_FORMAT(tbl_invoices.date, "%Y-%m-%d") >= "'.to_sql_date($start_date_search).'"');
            }

            if (!empty($end_date_search)) {
                $this->db->where('DATE_FORMAT(tbl_invoices.date, "%Y-%m-%d") <= "'.to_sql_date($end_date_search).'"');
            }

            $this->db->order_by('tbl_invoices.id desc');
            $dtSyntheticCouponInvoice = $this->db->get()->result_array();


            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('HÓA ĐƠN BÁN HÀNG'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:AB1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'Số Hóa Đơn Bán Hàng')->getStyle("A$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Ngày Lập Hóa Đơn Bán Hàng')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Số PGH');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Ngày Lập PGH');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mã ĐĐH (TD)');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Mã ĐĐH (KH)');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Loại ĐĐH');
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Ngày Lập ĐĐH');
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Ngày Giao');
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Mã KH');
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Khách Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Brand');
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Chỉ Lệnh')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Mã Thành Phẩm (TD)')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Tên Thành Phẩm (TD)')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Tên Thành Phẩm (KH)');
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Quy Cách');
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'ĐVT');
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Số Con/Kiện')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Tổng Số Kiện')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Tổng SL')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'Đơn Giá Bán')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$sttRow.'', 'Loại Giá Áp Dụng')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$sttRow.'', 'Tổng Tiền')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$sttRow.'', '% Thuế')->getStyle("Y$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Z'.$sttRow.'', 'Tổng Tiền thuế')->getStyle("Z$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AA'.$sttRow.'', 'Chi Phí Giao Hàng')->getStyle("AA$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AB'.$sttRow.'', 'Thành Tiền')->getStyle("BB$sttRow")->getAlignment()->setWrapText(true);

            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:B$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'F4B084'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("C$sttRow:D$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("E$sttRow:X$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFF00'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("Y$sttRow:AB$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'F4B084'),
                ),
            ]);
            $rowBegin = $sttRow;
            if (!empty($dtSyntheticCouponInvoice)) {
                foreach ($dtSyntheticCouponInvoice as $key => $value) {
                    $id_delivery = $value['delivery_id'];
                    $tb_tamp = '(
                        SELECT 
                            (tb_tamp.delivery_id) as delivery_id,
                            (tb_tamp.delivery_item_id) as delivery_item_id,
                            (tb_tamp.command) as command,
                            SUM(tb_tamp.quantity_put) as quantity_put,
                            SUM(tb_tamp.sample_quantity_item) as sample_quantity_item,
                            SUM(tb_tamp.quantity_loss) as quantity_loss
                        FROM (
                            SELECT
                                counter_items_number as counter_items_number,
                                delivery_id as delivery_id,
                                delivery_item_id as delivery_item_id,
                                MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_loss",tbl_delivery_items_columns.columns_name,"")) as "quantity_loss",
                                MAX(IF(tbl_delivery_items_columns.columns_value = "sample_quantity_item",tbl_delivery_items_columns.columns_name,"")) as "sample_quantity_item",
                                MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_put",tbl_delivery_items_columns.columns_name,"")) as "quantity_put",
                                MAX(IF(tbl_delivery_items_columns.columns_value = "order_code",tbl_delivery_items_columns.columns_name,"")) as "order_code",
                                MAX(IF(tbl_delivery_items_columns.columns_value = "command",tbl_delivery_items_columns.columns_name,"")) as "command"
                            FROM `tbl_delivery_items_columns` 
                            WHERE tbl_delivery_items_columns.delivery_id = '.$id_delivery.'
                            GROUP BY counter_items_number,delivery_id,delivery_item_id
                        ) tb_tamp
                        GROUP BY tb_tamp.delivery_id,tb_tamp.command,tb_tamp.delivery_item_id  
                    ) as tb_tamp';

                    $this->db->select('
                        tbl_delivery_items.item_id as item_id,
                        tb_tamp.command as command,
                        tbl_order_items.product_name_customer as product_name_customer,
                        tbl_delivery_items.price as price,
                        SUM(tb_tamp.quantity_put) as quantity,
                        tbl_order_items.is_lot as is_lot,
                        tbl_products.code as code_product,
                        tbl_products.name as name_product,
                        tbl_products.mode as mode,
                        tbl_products.quantity_sheet_bale as quantity_sheet_bale,
                        0 as quantity_bale,
                         GROUP_CONCAT(distinct(DATE_FORMAT(tbl_order_item_shippings.date_shipping,"%d/%m/%Y"))) as date_shipping,
                        tblunits.unit as unit
                    ');
                    $this->db->from('tbl_delivery_items');
                    $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
                    $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
                    $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
                    $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id');
                    $this->db->join($tb_tamp, 'tb_tamp.delivery_item_id = tbl_delivery_items.id');
                    $this->db->where('tbl_delivery_items.delivery_id', $id_delivery);
                    $this->db->group_by('tbl_delivery_items.item_id, tb_tamp.command');
                    $delivery_items = $this->db->get()->result_array();
                    if (!empty($delivery_items)){
                        foreach ($delivery_items as $kk => $vv) {
                            $rowBegin++;
                            $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $value['reference_no_invoice']);
                            $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _dt($value['date_invoice']));
                            $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", $value['reference_no_delivery']);
                            $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", _dt($value['date_delivery']))->getStyle("B$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['reference_no_order']));
                            $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['reference_no_customer']);
                            $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", ($value['name_type_order']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", _dt($value['date_order']))->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", ($vv['date_shipping']))->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['zcode'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['company'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['name_group'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $vv['command'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $vv['code_product'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $vv['name_product'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin",
                                $vv['product_name_customer'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin",
                                $vv['mode'])->getStyle("Q$rowBegin");
                            $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin",
                                $vv['unit'])->getStyle("R$rowBegin");
                            $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin",
                                $vv['quantity_sheet_bale'])->getStyle("S$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($vv['quantity_sheet_bale']));
                            $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin",
                                $vv['quantity_bale'])->getStyle("T$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($vv['quantity_bale']));
                            $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin",
                                $vv['quantity'])->getStyle("U$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($vv['quantity']));
                            $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin",
                                $vv['price'])->getStyle("V$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($vv['price']));
                            if($vv['is_lot'] == 1){
                                $htmlPrice = 'Theo Giá Lô';
                                $amount = $vv['price'];
                            } else {
                                $htmlPrice = 'Nhập Tay';
                                $amount = $vv['quantity'] * $vv['price'];
                            }
                            $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin",
                                $htmlPrice)->getStyle("W$rowBegin");
                            $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin",
                                $amount)->getStyle("X$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($amount));
                            $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin",
                                $value['tax_rate'])->getStyle("Y$rowBegin");
                            $total_tax = ($amount * $value['tax_rate']) / 100;
                            $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin",
                                $total_tax)->getStyle("Z$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($total_tax));
                            $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin",
                                $value['cost_delivery'])->getStyle("AA$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['cost_delivery']));
                            $total = $total_tax + $amount + $value['cost_delivery'];
                            $objPHPExcel->getActiveSheet()->setCellValue("AB$rowBegin",
                                $total)->getStyle("AB$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($total));

                            $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:AB$rowBegin")->applyFromArray([
                                'borders' => array(
                                    'allborders' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN
                                    )
                                )
                            ]);
                            $objPHPExcel->getActiveSheet()->getStyle("R$rowBegin:U$rowBegin")->applyFromArray([
                                'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                ),
                            ]);
                            $objPHPExcel->getActiveSheet()->getStyle("Y$rowBegin:Y$rowBegin")->applyFromArray([
                                'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                ),
                            ]);
                        }
                    }
                }
            }
            $filename = lang('hoa_don_ban_hang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(15);
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

    public function test() {
        $this->load->model('coupon_invoice_model');
        $this->coupon_invoice_model->handlingPlanProposeCouponInvoice(1, [1]);
    }
}
