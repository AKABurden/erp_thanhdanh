<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Other_payslips_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function handlingPayslips($vouchers_coupon_id, $data) {
        if (empty($data)) return false;

        $code = Getinfocode('code_pck');
        $date = $data['date_create'];
        $staff_id = get_staff_user_id();
        $prefix = get_option('prefix_other_payslips');
        $date_create = date('Y-m-d H:i:s');
        $total = $data['transfer_fees'];
        $objects = 4; //other
        $objects_id = 0;
        $vouchers = 0;
        $vouchers_id = 0;
        $objects_text = '';
        $note = 'Chi chuyển khoản';
        $payment_modes = $data['payment_mode'];
        $id_costs = $data['cost_other_id'];
        $status = 0;
        $history_status = '';
        $type_vouchers = 0;
        $detai = '';
        $type_manager = 0;

        $dataIns = [
            'prefix' => $prefix,
            'code' => $code,
            'objects' => $objects,
            'objects_id' => $objects_id,
            'vouchers' => $vouchers,
            'vouchers_id' => $vouchers_id,
            'note' => $note,
            'total' => $total,
            'date' => $date,
            'staff_id' => $staff_id,
            'date_create' => $date_create,
            'payment_modes' => $payment_modes,
            'id_costs' => $id_costs,
            'objects_text' => $objects_text,
            'status' => $status,
            'history_status' => $history_status,
            'type_vouchers' => $type_vouchers,
            'detai' => $detai,
            'type_manager' => $type_manager,
            'vouchers_coupon_id' => $vouchers_coupon_id,
        ];

        $this->db->insert('tblother_payslips', $dataIns);
        $id_pay = $this->db->insert_id();
        if ($id_pay) {
            activity_log_v2(
                'work_debt_buy',
                'tblother_payslips',
                $id_pay,
                $prefix . '-' . $code,
                'Thêm mới phiếu chi chuyển khoản [' . $prefix . '-' . $code . ']'
            );

            return true;
        } else {
            return false;
        }
    }

    public function checkUseVouchersCoupon($vouchers_coupon_id) {
        $data = [];

        $this->db->from('tblother_payslips');
        $this->db->where('tblother_payslips.vouchers_coupon_id', $vouchers_coupon_id);
        $this->db->limit(1);
        $rs = $this->db->get()->num_rows();
        if ($rs) {
            $data['result'] = 1;
            $data['message'] = lang('Đã có tạo phiếu chi không thể xóa');
        }

        return $data;
    }

    public function getOtherPayslipsArrVoucherCouponId($arrId, $key = '') {
        if (empty($arrId)) return null;

        $this->db->select('
            tblother_payslips.vouchers_coupon_id as vouchers_coupon_id,
            CONCAT(tblother_payslips.prefix, "-", tblother_payslips.code) as reference_no
        ');
        $this->db->from('tblother_payslips');
        $this->db->where_in('tblother_payslips.vouchers_coupon_id', $arrId);
        $rs = $this->db->get()->result_array();
        if (!empty($rs) && $key) {
            $rs = array_reduce($rs, function ($carry, $item) use ($key) {
                $id = $item[$key];
                $carry[$id] = $item;
                return $carry;
            }, []);
        }
        return $rs;
    }

    public function getVouchersCouponId($id) {
        $this->db->select('
            tblvouchers_coupon.id as id,
            tblvouchers_coupon.code_vouchers as code_vouchers
        ');
        $this->db->from('tblvouchers_coupon');
        $this->db->where_in('tblvouchers_coupon.id', $id);
        $rs = $this->db->get()->row_array();
        return $rs;
    }
}
