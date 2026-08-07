<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Vouchers_coupon extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('clients_model');
        $this->load->model('collect_categories_model');
    }

    public function index()
    {
        if (!has_permission('vouchers_coupon', '', 'view') && !has_permission('vouchers_coupon', '', 'view_own')) {
                access_denied('Vouchers coupon');
        }
        $data['title'] = _l('vouchers_for_coupon');
        $this->load->view('admin/vouchers_coupon/manage', $data);
    }

    public function table()
    {
        $this->app->get_table_data('vouchers_coupon');
    }

    public function view_vouchers_coupon($id='')
    {
        $this->app->get_table_data('view_vouchers_coupon',array('id'=>$id));
    }

    public function modal()
    {
        $data['code'] = get_option('prefix_coupon').sprintf('%06d', ch_getMaxID('id', 'tblvouchers_coupon') + 1);
        $data['payment_modes'] = get_table_where('tblpayment_modes',array('active'=>1));

        $staff = get_table_where('tblstaff',array('active'=>1));
        $arr_staff = array();
        foreach ($staff as $key => $value) {
            $arr_staff[$key]['staffid'] = $value['staffid'];
            $arr_staff[$key]['fullname'] = $value['firstname'].' '.$value['lastname'];
        }
        $data['staff'] = $arr_staff;

        // yct start
        $data['collect_categories'] = array();
		$this->collect_categories_model->get_by_id(0, $data['collect_categories']);
		$data['colcat_parent'] = [];
		$data['colcat_list'] = array();
		foreach ($data['collect_categories'] as $key => $value) {
			if (empty($value['costs_parent'])) {
				$data['colcat_parent'][$value['id']] = $value;
				$data['colcat_list'][$value['id']]['name'] = $value['name'];
			} else {
				$data['colcat_list'][$value['costs_parent']]['data'][] = $value;
			}
		}
        // yct end

        //cost other payslips
        $this->load->model('costs_model');
        $data['costs'] = array();
        $this->costs_model->get_by_id(0, $data['costs']);
        $data['cost_parent'] = [];
        $data['costs_list'] = array();
        foreach ($data['costs'] as $key => $value) {
            if (empty($value['costs_parent'])) {
                $data['cost_parent'][$value['id']] = $value;
                $data['costs_list'][$value['id']]['name'] = $value['name'];
            } else {
                $data['costs_list'][$value['costs_parent']]['data'][] = $value;
            }
        }

        $this->load->view('admin/vouchers_coupon/add_modal',$data);
    }

    public function view($id = '')
    {
        $data['items'] = get_table_where('tblvouchers_coupon',array('id'=>$id),'','row');
        // yct start
        $data['items']->colcat_name = $this->collect_categories_model->getColcatName($data['items']->id_costs);
        // yct end
        $data['dataLog'] = get_table_where('tblactivity_log_v2',array('table_obj'=>'tblvouchers_coupon','id_obj'=>$id),'id DESC');
        $this->load->view('admin/vouchers_coupon/view',$data);
    }

    public function searchCustomers($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');

        $this->db->select('tblclients.userid as id, CONCAT(tblclients.company, " - ", tblclients.zcode) as text, COALESCE(tblclients.fullname, "") as fullname, COALESCE(tblclients.phonenumber, "") as phonenumber, discount as discount, tblclients.table_price_id as table_price_id', false);
        $this->db->from('tblclients');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblclients.company', $term);
            $this->db->or_like('tblclients.fullname', $term);
            $this->db->or_like('tblclients.phonenumber', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $customers = $this->db->get()->result_array();
        $data['results'] = [
            [
                'text' => lang('customers'), 'children' => $customers
            ]
        ];
        if ($id) {
            $customer_id = $id;
            if ($type_customer == "customers") {
                $customer = $this->clients_model->rowCustomer($customer_id);
                $data['row'] = ['id' => $customer['userid'], 'text' => $customer['fullname']];
            } else {
                $data['row'] = ['id' => 0, 'text' => 'Not found!'];
            }
        }
        echo json_encode($data);
    }

    public function getData_order()
    {
        $data = $this->input->post();
        if($data['customer'] != '') {

            $tbOrders = "(
                SELECT
                    tbl_invoice_items.invoice_id as invoice_id,
                    SUM(tbl_deliveries.grand_total_items) as grand_total_items,
                    0 as cost_delivery,
                    SUM(tbl_deliveries.total_tax) as total_tax,
                    SUM(tbl_deliveries.grand_total) as grand_total,
                    GROUP_CONCAT(tbl_deliveries.reference_no SEPARATOR '<br>') as reference_orders
                FROM tbl_invoice_items
                INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_invoice_items.object_id
                GROUP BY tbl_invoice_items.invoice_id
            ) tb_orders";
            // 'INNER JOIN ' . $tbOrders . ' ON tb_orders.invoice_id = tbl_invoices.id',

            $this->db->select('tbl_invoices.*,tb_orders.grand_total as grand_total');
            $this->db->where('customer_id',$data['customer']);
            $this->db->where('status_payment <>',2); //loại trừ phiếu đã thu đủ
            $this->db->join($tbOrders, 'tb_orders.invoice_id=tbl_invoices.id', 'left');
            $result = $this->db->get('tbl_invoices')->result_array();
        }
        else {
            $result = array();
        }
        echo json_encode($result);
        // $data = $this->input->post();
        // if($data['customer'] != '') {
        //     $this->db->select('tbl_orders.*');
        //     $this->db->where('customer_id',$data['customer']);
        //     $this->db->where('status','approved');
        //     $this->db->where('status_payment <>',2); //loại trừ phiếu đã thu đủ
        //     $result = $this->db->get('tbl_orders')->result_array();
        // }
        // else {
        //     $result = array();
        // }
        // echo json_encode($result);
    }

    public function add($id = '')
    {
         if (!has_permission('vouchers_coupon', '', 'create')) {
                echo json_encode(['success'=>true, 'alert_type'=>'warning', 'message'=>_l('Bạn không có quyền thêm')]);
        }
        $data = $this->input->post();
        if(isset($data['code_orders'])) {
            $data['arr_code_orders'] = implode(',', $data['code_orders']);
        }
        else {
            $data['arr_code_orders'] = '';
        }
        sort($data['code_orders']);
        $count = count($data['code_orders']);
        $transfer_fees = number_unformat($data['transfer_fees'] ?? 0);
        $cost_other_id = $data['cost_other_id'] ?? 0;
        if ($transfer_fees > 0 && empty($cost_other_id)) {
            echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => lang('Vui lòng chọn mục chi phí nếu nhập phí chuyển khoản để sinh ra phiếu chi')]);
            die;
        }

        $in = array(
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y-m-d'),
            'date_vouchers' => to_sql_date($data['date_vouchers']),
            'code_vouchers' => $data['code_vouchers'],
            'customer' => $data['customer'],
            'staff' => $data['staff'],
            'payment_mode' => $data['payment_mode'],
            'total' => str_replace(',', '', $data['total']),
            'payment' => str_replace(',', '', $data['payment']),
            'id_costs' => $data['id_costs'],
            'note' => $data['note'],
            'transfer_fees' => $transfer_fees,
            'cost_other_id' => $cost_other_id,
        );
        $insert_true = $this->db->insert('tblvouchers_coupon',$in);
        if($insert_true) {
            $idd = $this->db->insert_id();
            $get_code = get_table_where('tblvouchers_coupon',array('id'=>$idd),'','row');
            activity_log_v2('work_debt_sales','tblvouchers_coupon',$idd,$get_code->code_vouchers,'Thêm mới phiếu thu bán hàng ['.$get_code->code_vouchers.']');
            // 'arr_code_orders'=>$data['arr_code_orders'],
            //cập nhật lại đơn hàng, đang mặt định thu đủ
            $arr_code_orders = '';
            $total_payment = (int)(str_replace(',', '', $data['payment']));
            if(isset($data['code_orders'])) {
                $count = count($data['code_orders']);
                foreach ($data['code_orders'] as $key => $value) {
                    // $get_detail_orders = get_table_where('tbl_invoices',array('id'=>$value),'','row');
                    $tbOrders = "(
                        SELECT
                            tbl_invoice_items.invoice_id as invoice_id,
                            SUM(tbl_deliveries.grand_total_items) as grand_total_items,
                            0 as cost_delivery,
                            SUM(tbl_deliveries.total_tax) as total_tax,
                            SUM(tbl_deliveries.grand_total) as grand_total,
                            GROUP_CONCAT(tbl_deliveries.reference_no SEPARATOR '<br>') as reference_orders
                        FROM tbl_invoice_items
                        INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_invoice_items.object_id
                        GROUP BY tbl_invoice_items.invoice_id
                    ) tb_orders";
                    // 'INNER JOIN ' . $tbOrders . ' ON tb_orders.invoice_id = tbl_invoices.id',
        
                    $this->db->select('tbl_invoices.*,tb_orders.grand_total as grand_total');
                    $this->db->where('id',$value);
                    $this->db->join($tbOrders, 'tb_orders.invoice_id=tbl_invoices.id', 'left');
                    $get_detail_orders = $this->db->get('tbl_invoices')->row();

                    $payment_total = $total_payment;
                    $total_payment = $total_payment - ($get_detail_orders->grand_total  - (int)$get_detail_orders->total_payment);
                    if($total_payment >= 0)
                    {
                        if($key == ($count - 1 ))
                        {
                            $this->db->where('id',$value);
                            $this->db->update('tbl_invoices',['status_payment'=>2,'total_payment'=>($get_detail_orders->total_payment + $payment_total)]);
                            $arr_code_orders.=$value.'|'.($payment_total).',';
                            $_pay_detail['id_order'] = $value;
                            $_pay_detail['id_vouchers'] = $idd;
                            $_pay_detail['payment'] = $payment_total;
                            $this->db->insert('tblvouchers_coupon_detal',$_pay_detail);  
                            break; 
                        }else
                        {
                            $this->db->where('id',$value);
                            $this->db->update('tbl_invoices',['status_payment'=>2,'total_payment'=>($get_detail_orders->total_payment + ($get_detail_orders->grand_total - $get_detail_orders->total_payment))]);

                            $arr_code_orders.=$value.'|'.((($get_detail_orders->grand_total   - $get_detail_orders->total_payment))).',';    
                            $_pay_detail['id_order'] = $value;
                            $_pay_detail['id_vouchers'] = $idd;
                            $_pay_detail['payment'] = ((($get_detail_orders->grand_total   - $get_detail_orders->total_payment)));
                            $this->db->insert('tblvouchers_coupon_detal',$_pay_detail);
                        }
                    }else
                    {        
                            if(($get_detail_orders->total_payment + $payment_total) == 0)
                            {
                                $status = 0;
                            }else
                            {
                                $status = 1;
                            }
                            $this->db->where('id',$value);
                            $this->db->update('tbl_invoices',['status_payment'=>$status,'total_payment'=>($get_detail_orders->total_payment + $payment_total)]);
                            $arr_code_orders.=$value.'|'.($payment_total).',';  
                            $_pay_detail['id_order'] = $value;
                            $_pay_detail['id_vouchers'] = $idd;
                            $_pay_detail['payment'] = $payment_total;
                            $this->db->insert('tblvouchers_coupon_detal',$_pay_detail);
                    break;
                    }
                }
            }
            $arr_code_orders = trim($arr_code_orders,',');
            $this->db->update('tblvouchers_coupon',array('arr_code_orders'=>$arr_code_orders),array('id'=>$idd));

            //handling other payslips
            if ($transfer_fees > 0) {
                $this->load->model('other_payslips_model');
                $this->other_payslips_model->handlingPayslips($idd, $in);
            }

            echo json_encode(['success'=>true, 'alert_type'=>'success', 'message'=>_l('add_coupon_success')]);
        }
        else {
            echo json_encode(['success'=>false, 'alert_type'=>'danger', 'message'=>_l('add_coupon_fail')]);
        }
    }
    public function delete_vouchers_coupon($id='')
    {
        if (!has_permission('vouchers_coupon', '', 'delete')) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('delete_coupon_fail')
            ));
            die;
        }
        $get_detail = get_table_where('tblvouchers_coupon', array('id' => $id), '', 'row');
        if (!empty($get_detail->arr_code_orders)) {
            $arr_id = explode(',', $get_detail->arr_code_orders);
        }

        $this->load->model('other_payslips_model');
        $isCheck = $this->other_payslips_model->checkUseVouchersCoupon($id);
        if (!empty($isCheck['result'])) {
            echo json_encode(array(
                'success' => false,
                'alert_type' => 'danger',
                'message' => $isCheck['message']
            ));
            die;
        }

        $get_code = get_table_where('tblvouchers_coupon', array('id' => $id), '', 'row');
        $this->db->where('id', $id);
        $delete_true = $this->db->delete('tblvouchers_coupon');

        if ($delete_true) {
            $this->db->delete('tblvouchers_coupon_detal', array('id_vouchers' => $id));
            //cập nhật lại đơn hàng, xóa trạng thái phiếu thu, số tiên thu
            foreach ($arr_id as $key => $value) {
                $id_order = explode('|', $value);
                // $get_detail_orders = get_table_where('tbl_invoices', array('id' => $id_order[0]), '', 'row');
                $tbOrders = "(
                    SELECT
                        tbl_invoice_items.invoice_id as invoice_id,
                        SUM(tbl_deliveries.grand_total_items) as grand_total_items,
                        0 as cost_delivery,
                        SUM(tbl_deliveries.total_tax) as total_tax,
                        SUM(tbl_deliveries.grand_total) as grand_total,
                        GROUP_CONCAT(tbl_deliveries.reference_no SEPARATOR '<br>') as reference_orders
                    FROM tbl_invoice_items
                    INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_invoice_items.object_id
                    GROUP BY tbl_invoice_items.invoice_id
                ) tb_orders";
                // 'INNER JOIN ' . $tbOrders . ' ON tb_orders.invoice_id = tbl_invoices.id',
    
                $this->db->select('tbl_invoices.*,tb_orders.grand_total as grand_total');
                $this->db->where('id',$id_order[0]);
                $this->db->join($tbOrders, 'tb_orders.invoice_id=tbl_invoices.id', 'left');
                $get_detail_orders = $this->db->get('tbl_invoices')->row();
                if ((($get_detail_orders->total_payment - $id_order[1])) == 0) {
                    $status = 0;
                } else {
                    $status = 1;
                }
                $this->db->where('id', $id_order[0]);
                $this->db->update('tbl_invoices', ['status_payment' => $status, 'total_payment' => ($get_detail_orders->total_payment - $id_order[1])]);
            }

            insertActivityLog([
                'type_parent_obj' => 'vouchers_coupon',
                'table_obj' => 'tblvouchers_coupon',
                'id_obj' => $id,
                'name_obj' => $get_code->code_vouchers,
                'content' => lang('Xóa phiếu thu bán hàng') . ' [' . $get_code->code_vouchers . ']',
                'actions' => 'delete'
            ]);

            echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => _l('delete_coupon_success')]);
        } else {
            echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('delete_coupon_fail')]);
        }
    }
    // public function delete_vouchers_coupon($id='')
    // {
    //     if (!has_permission('vouchers_coupon', '', 'delete')) {
    //         echo json_encode(array(
    //             'success' => false,
    //             'message' => _l('delete_coupon_fail')
    //         ));die;
    //         }
    //     $get_detail = get_table_where('tblvouchers_coupon',array('id'=>$id),'','row');
    //     if(!empty($get_detail->arr_code_orders)) {
    //         $arr_id = explode(',', $get_detail->arr_code_orders);
    //     }

    //     $get_code = get_table_where('tblvouchers_coupon',array('id'=>$id),'','row');
    //     activity_log_v2('work_debt_sales','tblvouchers_coupon',$id,$get_code->code_vouchers,'Xóa phiếu thu bán hàng ['.$get_code->code_vouchers.']');
    //     $this->db->where('id',$id);
    //     $delete_true = $this->db->delete('tblvouchers_coupon');

    //     if($delete_true) {
    //         //cập nhật lại đơn hàng, xóa trạng thái phiếu thu, số tiên thu
    //         foreach ($arr_id as $key => $value) {
    //             $id_order = explode('|', $value);
    //             $get_detail_orders = get_table_where('tbl_orders',array('id'=>$id_order[0]),'','row');
    //             if((($get_detail_orders->total_payment-$id_order[1]) + $get_detail_orders->price_other_expenses) == 0)
    //             {
    //                 $status = 0;
    //             }else
    //             {
    //                 $status = 1;
    //             }
    //             $this->db->where('id',$id_order[0]);
    //             $this->db->update('tbl_orders',['status_payment'=>$status,'total_payment'=>($get_detail_orders->total_payment - $id_order[1])]);
    //         }
    //         echo json_encode(['success'=>true, 'alert_type'=>'success', 'message'=>_l('delete_coupon_success')]);
    //     }
    //     else {
    //         echo json_encode(['success'=>false, 'alert_type'=>'danger', 'message'=>_l('delete_coupon_fail')]);
    //     }
    // }

    public function update_status()
    {
        if (!has_permission('vouchers_coupon', '', 'approve')) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('ch_approve_not')
            ));die;
            }
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $history_status = $staff_id.'|'.$date;
            $data =array(
                'history_status'=>$history_status,
                'status' => ($status+1),
            );
            $success = $this->db->update('tblvouchers_coupon',$data,array('id'=>$id));
        }
        if($success) {
            $get_code = get_table_where('tblvouchers_coupon',array('id'=>$id),'','row');
            activity_log_v2('work_debt_sales','tblvouchers_coupon',$id,$get_code->code_vouchers,'Cập nhật trạng thái phiếu thu bán hàng ['.$get_code->code_vouchers.']');
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'success',
                'message' => _l('ch_successful_approval')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'danger',
                'message' => _l('ch_no_successful_approval')
            ));
        }
        die;
    }

    public function print_pdf_2($id='')
    {
        ob_start();
        $data = new stdClass();
        $dataMain = get_table_where('tblvouchers_coupon',array('id'=>$id),'','row');
        $data->code_vouchers = $dataMain->code_vouchers;
        $table = '';
        $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data->img = '';
        $data->content = '<br><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold; text-transform: uppercase;">'._l('coupon').'</span><br>';
        $data->content .= '<span style="text-align: center;font-style: italic;">'._l('ch_number').': '.$dataMain->code_vouchers.'</span><br>';

        $day = date('d', strtotime($dataMain->date_vouchers));
        $month = date('m', strtotime($dataMain->date_vouchers));
        $year = date('Y', strtotime($dataMain->date_vouchers));
        $date = _l('ch_day') . ' ' . $day . ' ' . _l('ch_month') . ' ' . $month . ' ' . _l('ch_year') . ' ' . $year;
        $data->content .= '<span style="text-align: center;font-style: italic;">'.$date.'</span><br><br>';
        $customer = get_table_where('tblclients',array('userid'=>$dataMain->customer),'','row');
        $pay_modes = get_table_where('tblpayment_modes',array('id'=>$dataMain->payment_mode),'','row');
        $data->content .= '
            <span style="font-weight: bold;">'._l('ch_units_in').': </span><span style="font-weight: bold;">'.$customer->company.'</span><br>
            <span style="font-weight: bold;">'._l('acs_sales_payment_modes_submenu').': </span><span>'.$pay_modes->name.'</span><br>
            <span style="font-weight: bold;">'._l('expense_add_edit_amount').': </span><span>'.number_format($dataMain->payment).'</span><br>
            <span style="font-weight: bold;">'._l('ch_write_in_words').': </span><span>'.ucfirst(convert_number_to_words($dataMain->payment)).' đồng</span><br>
            <span style="font-weight: bold;">'._l('note').': </span><span>'.$dataMain->note.'</span><br>';
        $date_2 = _l('ch_day') . ' ........ ' . _l('ch_month') . ' ........ ' . _l('ch_year') . ' ........';
        $data->content .= '<span style="text-align: right;font-style: italic;">'.$date_2.'</span><br>';
        $table = '<table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('ch_ceo').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('ch_chief_accountant').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('ch_cashier').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('ch_vote_maker').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">'._l('staff_coupon').'</span><br>
                            <span>'._l('ch_signature').'</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $datas = '<br><br><br><br><br><span style="text-align: center;">__________________________________________________________________</span><br>';
        $company_logo = get_option('company_logo');
        $img = file_get_contents(base_url('uploads/company/').$company_logo);
        $html= '<table  class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 20%;">
                        <img width="112" height="50" src="data:image/png;base64,'.base64_encode($img).'"/>
                        </td>
                        <td style="width: 80%;">
                            <span style="font-weight: bold;font-size: 14px;">'.get_option('invoice_company_name').'</span><br>
                            <span style="font-size: 12px;">'.lang('tnh_address').': '.get_option('invoice_company_address').'</span><br>
                            <span style="font-size: 12px;">'.lang('tnh_phone').': '.get_option('invoice_company_phonenumber').'</span><br>
                            <span style="font-size: 12px;">'.lang('Fax').': '.get_option('fax_company').'</span><br>
                            <span style="font-size: 12px;">'._l('Email').': '.get_option('email_company').'</span><br>
                        </td>
                    </tr>
                </tbody>
            </table>';

//        $data->content .=$datas.$html.$data->content;
        $pdf      = print_pdf_dt($data);
        $type     = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }

    public function table_single_client($clientid = '')
    {
        $this->app->get_table_data('vouchers_coupon_single_client', [
            'clientid' => $clientid,
        ]);
    }
    public function add_all($id = '')
    {
         if (!has_permission('vouchers_coupon', '', 'create')) {
                echo json_encode(['success'=>true, 'alert_type'=>'warning', 'message'=>_l('Bạn không có quyền thêm')]);
        }
        $data = $this->input->post();
        $data['code_orders'] = trim($data['code_orders'],',');
        if(isset($data['code_orders'])) {
            $data['arr_code_orders'] = explode(',', $data['code_orders']);
            $data['code_orders'] = explode(',', $data['code_orders']);
        }
        else {
            $data['arr_code_orders'] = '';
        }
        sort($data['code_orders']);
            $count = count($data['code_orders']);
        $in = array(
            'staff_create'=>get_staff_user_id(),
            'date_create'=>date('Y-m-d H:i:s'),
            'date_vouchers'=>to_sql_date($data['date_vouchers']),
            'code_vouchers'=>get_option('prefix_coupon').sprintf('%06d', ch_getMaxID('id', 'tblvouchers_coupon') + 1),
            'customer'=>$data['id_clients'],
            'staff'=>get_staff_user_id(),
            'payment_mode'=>$data['payment_mode'],
            'total'=>str_replace(',', '', $data['total']),
            'payment'=>str_replace(',', '', $data['payment']),
            'note'=>$data['note']
        );
        $insert_true = $this->db->insert('tblvouchers_coupon',$in);
        if($insert_true) {
            $idd = $this->db->insert_id();
            $get_code = get_table_where('tblvouchers_coupon',array('id'=>$idd),'','row');
            activity_log_v2('work_debt_sales','tblvouchers_coupon',$idd,$get_code->code_vouchers,'Thêm mới phiếu thu bán hàng ['.$get_code->code_vouchers.']');
            // 'arr_code_orders'=>$data['arr_code_orders'],
            //cập nhật lại đơn hàng, đang mặt định thu đủ
            $arr_code_orders = '';
            $total_payment = (int)(str_replace(',', '', $data['payment']));
            if(isset($data['code_orders'])) {
                $count = count($data['code_orders']);
                foreach ($data['code_orders'] as $key => $value) {
                    $get_detail_orders = get_table_where('tbl_orders',array('id'=>$value),'','row');
                    $payment_total = $total_payment;

                    $total_payment = $total_payment - ((int)$get_detail_orders->grand_total - (int)$get_detail_orders->price_other_expenses - (int)$get_detail_orders->total_payment);

                    if($total_payment >= 0)
                    {
                        if($key == ($count - 1 ))
                        {
                            $this->db->where('id',$value);
                            $this->db->update('tbl_orders',['status_payment'=>2,'total_payment'=>($get_detail_orders->total_payment + $payment_total)]);

                            $arr_code_orders.=$value.'|'.($payment_total).',';
                            break; 
                        }else
                        {
                            $this->db->where('id',$value);
                            $this->db->update('tbl_orders',['status_payment'=>2,'total_payment'=>($get_detail_orders->total_payment + ($get_detail_orders->grand_total -$get_detail_orders->price_other_expenses  - $get_detail_orders->total_payment))]);

                            $arr_code_orders.=$value.'|'.(($get_detail_orders->total_payment + ($get_detail_orders->grand_total -$get_detail_orders->price_other_expenses  - $get_detail_orders->total_payment))).',';    
                        }
                    }else
                    {        
                            if(($get_detail_orders->total_payment + $get_detail_orders->price_other_expenses) == 0)
                            {
                                $status = 0;
                            }else
                            {
                                $status = 1;
                            }
                            $this->db->where('id',$value);
                            $this->db->update('tbl_orders',['status_payment'=>$status,'total_payment'=>($get_detail_orders->total_payment + $payment_total)]);
                            $arr_code_orders.=$value.'|'.($payment_total).',';    
                    break;
                    }
                }
            }
            $arr_code_orders = trim($arr_code_orders,',');
            $this->db->update('tblvouchers_coupon',array('arr_code_orders'=>$arr_code_orders),array('id'=>$idd));
            echo json_encode(['success'=>true, 'alert_type'=>'success', 'message'=>_l('add_coupon_success')]);
        }
        else {
            echo json_encode(['success'=>false, 'alert_type'=>'danger', 'message'=>_l('add_coupon_fail')]);
        }
    }

    public function edit_bank($id){
        $data = [];
        $voucher = get_table_where('tblvouchers_coupon',['id' => $id],'','row_array');
        if ($this->input->post()){
            $this->db->where('tblvouchers_coupon.id',$id);
            $success = $this->db->update('tblvouchers_coupon',[
                'code_bank' => !empty($this->input->post('code_bank')) ? $this->input->post('code_bank') : null,
                'date_bank' => !empty($this->input->post('date_bank')) ? to_sql_date($this->input->post('date_bank')) : null,
            ]);
            if ($success){
                $data['result'] = true;
                $data['message'] = lang('Cập nhập thành công');
            } else {
                $data['result'] = false;
                $data['message'] = lang('Cập nhập thất bại');
            }
            echo json_encode($data);die();
        }
        $data['id'] = $id;
        $data['voucher'] = $voucher;
        $this->load->view('admin/vouchers_coupon/edit_bank', $data);
    }
}