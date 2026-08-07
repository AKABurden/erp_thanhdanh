<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Coupon_invoice_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function handlingPlanProposeCouponInvoice($invoice_id, $data) {
        if (empty($data)) return null;
        
        $code = 'KH' . '-' . sprintf('%06d', ch_getMaxID('id', 'tblplan_propose') + 1);
        $client_id = $data['customer_id'];
        $dtClient = get_Table_where('tblclients', ['userid' => $client_id], '', 'row_array', '', 'time_payment');
        $time_payment = $dtClient['time_payment'] ?? 0;
        $date = plusDate($data['date'], $time_payment);
        $money = $data['grand_total'];
        $created_by = get_staff_user_id();
        $date_create = date('Y-m-d H:i:s');
        $type_plan_propose = 'vouchers_coupon';
        $coupon_invoice_id = $invoice_id;
        $category_tasks = 2418;
        $id_branch = $data['branch_id'];

        $dataIns = [
            'code' => $code,
            'date' => $date,
            'date_create' => $date_create,
            'money' => $money,
            'create_by' => $created_by,
            'type_plan_propose' => $type_plan_propose,
            'category_tasks' => $category_tasks,
            'id_branch' => $id_branch,
            'coupon_invoice_id' => $coupon_invoice_id,
            'client_id' => $client_id,
            'time_payment' => $time_payment,
            'staff' => $created_by,
        ];

        $this->db->insert('tblplan_propose', $dataIns);
		$id = $this->db->insert_id();
        if (!empty($id)) {
            return true;
        } else {
            return false;
        }
    }
}