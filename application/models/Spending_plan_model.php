<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Spending_plan_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function submit($formData, $id = null)
    {
        $arrField = [
            'create_by',
            'code',
            'date',
            'group_plan',
            'detail',
            'receiver',
            'approve_staff_id',
            'spending_staff_id',
            'price',
            'tax_id',
            'tax_rate',
            'amount',
            'payment_method_id',
            'currency_id',
            'exchange_rate',
            'category_spend',
            'expense',
            'deadline',
        ];

        $formData['last_change'] = date('Y-m-d H:i:s');
        if (!empty($formData['date'])) {
            $formData['date'] = to_sql_date($formData['date']);
        } else {
            $formData['date'] = date('Y-m-d');
        }
        if (!empty($formData['deadline'])) {
            $formData['deadline'] = to_sql_date($formData['deadline']);
        }

        if (!empty($formData['tax_id'])) {
            $tax = get_table_where('tbltaxes', ['id' => $formData['tax_id']], '', 'row_array');
            $formData['tax_rate'] = $tax['taxrate'] ?? 0;
        }

        if (!empty($formData['price'])) {
            $formData['price'] = number_unformat($formData['price']);
        }
        if (!empty($formData['exchange_rate'])) {
            $formData['exchange_rate'] = number_unformat($formData['exchange_rate']);
        }

        $formData['amount'] = (float)($formData['price'] ?? 0) * (1 + (float)($formData['tax_rate'] ?? 0) / 100) * (float)($formData['exchange_rate'] ?? 0);


        if (empty($id)) { //insert
            $formData['code'] = get_option('prefix_spending_plan') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblspending_plan') + 1);
            $formData['create_by'] = get_staff_user_id();
        } else { //update
            unset($arrField['code']);
            unset($arrField['create_by']);
        }

        $submitData = [];
        foreach ($arrField as $field) {
            if (isset($formData[$field])) {
                $submitData[$field] = $formData[$field];
            }
        }

        if (empty($id)) { // insert
            $this->db->insert('tblspending_plan', $submitData);
            $submitId = $this->db->insert_id();
        } else { //update
            if ($this->db->update('tblspending_plan', $submitData, ['id' => $id])) {
                $submitId = $id;
            } else {
                $submitId = false;
            }
        }
        $response['submitId'] =  $submitId;
        $response['message'] =  (($submitId) ? 'Thành công' : 'Thất bại');
        return $response;
    }

    function delete($id)
    {
        $response['isSuccess'] = false;
        $response['message'] = 'Xóa thất bại';
        
            $isSuccess = $this->db->delete('tblspending_plan', array('id' => $id));
            if ($isSuccess) {
                $response['isSuccess'] = true;
                $response['message'] = 'Xóa thành công';
                $response['deleteId'] = $id;
            }
        // }

        return $response;
    }
}
