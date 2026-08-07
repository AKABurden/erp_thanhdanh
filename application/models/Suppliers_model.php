<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Suppliers_model extends App_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get client object based on passed clientid if not passed clientid return array of all clients
     * @param  mixed $id    client id
     * @param  array  $where
     * @return mixed
     */
    public function add_suppliers($data)
    {
        if (!empty($data['info_detail'])) {
            $info_detail = $data['info_detail'];
            unset($data['info_detail']);
        }
        if (empty($data['code'])) {
            $data['code'] = sprintf('%06d', ch_getMaxID('id', db_prefix() . 'suppliers') + 1);
            if (!empty($data['supplier_code'])) {
                $data['code'] = $data['supplier_code'];
            }
        }
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        unset($data['supplier_code']);
        if (!empty($data['contact'])) {
            $contacts = $data['contact'];
            unset($data['contact']);
        }
        unset($data['id']);
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['prefix'] = get_option('prefix_supplier');
        if (is_staff_logged_in()) {
            $data['addedfrom'] = get_staff_user_id();
        }
        $data['active'] = 1;
        if (!empty($data['datecreated'])) {
            $_data['datecreated'] = $data['datecreated'];
        }
        
        if (!empty($data['company'])) {
            $_data['company'] = $data['company'];
        }
        if (!empty($data['prefix'])) {
            $_data['prefix'] = $data['prefix'];
        }
        if (!empty($data['renewal_date'])) {
            $_data['renewal_date'] = $data['renewal_date'];
        }
        $_data['type_suppliers'] = $data['type_suppliers'];
        if (!empty($data['tax'])) {
            $_data['tax'] = $data['tax'];
        }
        if (!empty($data['code_nxk'])) {
            $_data['code_nxk'] = $data['code_nxk'];
        }
        if (!empty($data['address_bank'])) {
            $_data['address_bank'] = $data['address_bank'];
        }
        if (!empty($data['address_delivery'])) {
            $_data['address_delivery'] = $data['address_delivery'];
        }
        if (!empty($data['addedfrom'])) {
            $_data['addedfrom'] = $data['addedfrom'];
        }
        if (!empty($data['phone'])) {
            $_data['phone'] = $data['phone'];
        }
        if (!empty($data['email'])) {
            $_data['email'] = $data['email'];
        }
        if (!empty($data['vat'])) {
            $_data['vat'] = $data['vat'];
        }
        if (!empty($data['default_currency'])) {
            $_data['default_currency'] = $data['default_currency'];
        }
        if (!empty($data['groups_in'])) {
            $_data['groups_in'] = $data['groups_in'];
        }
        if (!empty($data['address'])) {
            $_data['address'] = $data['address'];
        }
        if (!empty($data['note'])) {
            $_data['note'] = $data['note'];
        }
        if (!empty($data['active'])) {
            $_data['active'] = $data['active'];
        }
        if (!empty($data['city'])) {
            $_data['city'] = $data['city'];
        }
        if (!empty($data['district'])) {
            $_data['district'] = $data['district'];
        }
        if (!empty($data['ward'])) {
            $_data['ward'] = $data['ward'];
        }
        if (!empty($data['country'])) {
            $_data['country'] = $data['country'];
        }
        if (!empty($data['code'])) {
            $_data['code'] = $data['code'];
        }
        if (!empty($data['debt_limit'])) {
            $_data['debt_limit'] = $data['debt_limit'];
        }
        if (!empty($data['representative'])) {
            $_data['representative'] = $data['representative'];
        }

        if (!empty($data['quality_standards'])) {
            $_data['quality_standards'] = $data['quality_standards'];
        }

        if (!empty($data['certification'])) {
            $_data['certification'] = $data['certification'];
        }

        if (!empty($data['packing_regulations'])) {
            $_data['packing_regulations'] = $data['packing_regulations'];
        }

        if (!empty($data['price_list_approval'])) {
            $_data['price_list_approval'] = $data['price_list_approval'];
        }

        if (empty($data['type'])) {
            $_data['type'] = 0;
        } else {
            $_data['type'] = 1;
        }

        if (!empty($data['tm_ck'])) {
            $_data['tm_ck'] = $data['tm_ck'];
        }

        if (!empty($data['time_payment'])) {
            $_data['time_payment'] = number_unformat($data['time_payment']);
        }
        if (!empty($data['debt_begin'])) {
            $_data['debt_begin'] = number_unformat($data['debt_begin']);
        }
        if (!empty($data['bank_account'])) {
            $_data['bank_account'] = $data['bank_account'];
        }

        if (!empty($data['name_account'])) {
            $_data['name_account'] = $data['name_account'];
        }

        if (!empty($data['contract_number'])) {
            $_data['contract_number'] = $data['contract_number'];
        }
        if (!empty($data['deadline_contract'])) {
            $_data['deadline_contract'] = $data['deadline_contract'];
        }

         if (!empty($data['abbreviation'])) {
            $_data['abbreviation'] = $data['abbreviation'];
        }

        if (!empty($data['date_begin'])) {
            $_data['date_begin'] = $data['date_begin'];
        }
        
        if (!empty($data['package_specifications'])) {
            $_data['package_specifications'] = $data['package_specifications'];
        }
        
        if (!empty($data['cost_id'])) {
            $_data['cost_id'] = $data['cost_id'];
        }
        
        if (!empty($data['number_contract'])) {
            $_data['number_contract'] = $data['number_contract'];
        }

        $contract_imports = NULL;
        if (!empty($data['contract_imports'])) {
            $contract_imports = $data['contract_imports'];
            unset($data['contract_imports']);
        }

        $this->db->insert(db_prefix() . 'suppliers', $_data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer
                unset($custom_fields);
                $custom_fields['suppliers']                = $_custom_fields['suppliers'];
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            if (!empty($info_detail)) {
                foreach ($info_detail as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            if (!empty($v)) {
                                $array_group = [
                                    'id_suppliert' => $insert_id,
                                    'id_detail' => $key,
                                    'value' => $v
                                ];
                                $this->db->insert(db_prefix() . 'suppliers_value', $array_group);
                            }
                        }
                    } else {
                        if (!empty($value)) {
                            if (empty($value['date']) || empty($value['datetime'])) {
                                $array_group = [
                                    'id_suppliert' => $insert_id,
                                    'id_detail' => $key,
                                    'value' => $value
                                ];
                            } else if (!empty($value['date'])) {
                                $array_group = [
                                    'id_suppliert' => $insert_id,
                                    'id_detail' => $key,
                                    'value' => to_sql_date($value['date'])
                                ];
                            } else if (!empty($value['datetime'])) {
                                $array_group = [
                                    'id_suppliert' => $insert_id,
                                    'id_detail' => $key,
                                    'value' => to_sql_date($value['date'], true)
                                ];
                            }

                            $this->db->insert(db_prefix() . 'suppliers_value', $array_group);
                        }
                    }
                }
            }
            log_activity('New Suppliers Created [ID:' . $insert_id . ', Name:' . $data['company'] . ']');
            if (!empty($contacts)) {
                foreach ($contacts as $key => $value) {
                    if ($value['name'] != '') {
                        if (empty($value['receive_email'])) {
                            $value['receive_email'] = 0;
                        }
                        if (empty($value['main_contact'])) {
                            $value['main_contact'] = 0;
                        }
                        $contact = array(
                            'id_supplers' => $insert_id,
                            'name' => $value['name'],
                            'phone' => $value['phone'],
                            'email' => $value['email'],
                            'address' => $value['address'],
                            'sex' => $value['sex'],
                            'note' => $value['note'],
                            'birthday' => to_sql_date($value['birthday']),
                            'main_contact' => $value['main_contact'],
                            'receive_email' => $value['receive_email'],
                            'date_create' => date('Y-m-d H:i:s'),
                            'staff_create' => get_staff_user_id(),
                        );
                        $this->db->insert(db_prefix() . 'contacts_suppliers', $contact);
                    }
                }
            }

            if (!empty($contract_imports)) {
                $contract_imports['id_supplers'] = $insert_id;
                $this->db->insert(db_prefix() . 'contacts_suppliers', $contract_imports);
            }
            return $insert_id;
        }

        return false;
    }
    public function edit_suppliers($data)
    {
        if (!empty($data['info_detail'])) {
            $info_detail = $data['info_detail'];
            unset($data['info_detail']);
        }
        if ($data['supplier_code'] == '') {
            $data['code'] = $number = sprintf('%06d', $data['id']);
        } else {
            $data['code'] = $data['supplier_code'];
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $contacts = NULL;
        unset($data['supplier_code']);
        if (isset($data['contact'])) {
            $contacts = $data['contact'];
            unset($data['contact']);
        }

        $data['date_update'] = date('Y-m-d H:i:s');
        if (!empty($data['contract_imports'])) {
            $contacts = $data['contract_imports'];
            unset($data['contract_imports']);
        }
        $id = $data['id'];
        if (empty($data['type'])) {
            $data['type'] = 0;
        } else {
            $data['type'] = 1;
        }

        $_data = array(
            'debt_limit' => $data['debt_limit'],
            'company' => $data['company'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'representative' => $data['representative'],
            'vat' => $data['vat'],
            'default_currency' => $data['default_currency'],
            'groups_in' => $data['groups_in'],
            'address' => $data['address'],
            'note' => $data['note'],
            'code' => $data['code'],
            'city' => $data['city'],
            'district' => $data['district'],
            'ward' => $data['ward'],
            'country' => $data['country'],
            'type' => $data['type'],
            'quality_standards' => !empty($data['quality_standards']) ? $data['quality_standards'] : '',
            'certification' => !empty($data['certification']) ? $data['certification'] : '',
            'packing_regulations' => !empty($data['packing_regulations']) ? $data['packing_regulations'] : '',
            'price_list_approval' => !empty($data['price_list_approval']) ? $data['price_list_approval'] : '',

            'tm_ck' => !empty($data['tm_ck']) ? $data['tm_ck'] : 0,
            'time_payment' => !empty($data['time_payment']) ? $data['time_payment'] : '',
            'bank_account' => !empty($data['bank_account']) ? $data['bank_account'] : '',
            'name_account' => !empty($data['name_account']) ? $data['name_account'] : '',
            'contract_number' => !empty($data['contract_number']) ? $data['contract_number'] : '',
            'discount_id' => !empty($data['discount_id']) ? $data['discount_id'] : 0,
            'deadline_contract' => !empty($data['deadline_contract']) ? $data['deadline_contract'] : NULL,

            'abbreviation' => !empty($data['abbreviation']) ? $data['abbreviation'] : '',
            'date_begin' => !empty($data['date_begin']) ? $data['date_begin'] : null,
            'package_specifications' => !empty($data['package_specifications']) ? $data['package_specifications'] : '',
            'cost_id' => !empty($data['cost_id']) ? $data['cost_id'] : 0,
            'number_contract' => !empty($data['number_contract']) ? $data['number_contract'] : '',

        );
        if (!empty($data['debt_begin'])) {
            $_data['debt_begin'] = number_unformat($data['debt_begin']);
        }
        $_data['type_suppliers'] = $data['type_suppliers'];
        if (!empty($data['renewal_date'])) {
            $_data['renewal_date'] = $data['renewal_date'];
        }
        if (!empty($data['tax'])) {
            $_data['tax'] = $data['tax'];
        }
        if (!empty($data['code_nxk'])) {
            $_data['code_nxk'] = $data['code_nxk'];
        }
        if (!empty($data['address_bank'])) {
            $_data['address_bank'] = $data['address_bank'];
        }
        if (!empty($data['address_delivery'])) {
            $_data['address_delivery'] = $data['address_delivery'];
        }
        $this->db->where('id', $id);
        $update = $this->db->update(db_prefix() . 'suppliers', $_data);
        if ($update) {
            if (!empty($info_detail)) {
                $list_ValueNotDelete = [];
                foreach ($info_detail as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            if (!empty($v)) {

                                $array_group = [
                                    'id_suppliert' => $id,
                                    'id_detail' => $key,
                                    'value' => $v
                                ];
                                $this->db->where($array_group);
                                $GetValue = $this->db->get(db_prefix() . 'suppliers_value')->row();
                                if (!empty($GetValue)) {
                                    $list_ValueNotDelete[] = $GetValue->id;
                                } else {
                                    $this->db->insert(db_prefix() . 'suppliers_value', $array_group);
                                    $id_value = $this->db->insert_id();
                                    if (!empty($id_value)) {
                                        $list_ValueNotDelete[] = $id_value;
                                    }
                                }
                            }
                        }
                    } else {
                        if (!empty($value)) {
                            if (empty($value['date']) || empty($value['datetime'])) {
                                $array_group = [
                                    'id_suppliert' => $id,
                                    'id_detail' => $key,
                                    'value' => $value
                                ];
                            } else if (!empty($value['date'])) {
                                $array_group = [
                                    'id_suppliert' => $id,
                                    'id_detail' => $key,
                                    'value' => to_sql_date($value['date'])
                                ];
                            } else if (!empty($value['datetime'])) {
                                $array_group = [
                                    'id_suppliert' => $id,
                                    'id_detail' => $key,
                                    'value' => to_sql_date($value['date'], true)
                                ];
                            }
                            $this->db->where($array_group);
                            $GetValue = $this->db->get(db_prefix() . 'suppliers_value')->row();
                            if (!empty($GetValue)) {
                                $list_ValueNotDelete[] = $GetValue->id;
                            } else {
                                $this->db->insert(db_prefix() . 'suppliers_value', $array_group);
                                $id_value = $this->db->insert_id();
                                if (!empty($id_value)) {
                                    $list_ValueNotDelete[] = $id_value;
                                }
                            }
                        }
                    }
                }
                $this->db->where('id_suppliert', $id);
                if (!empty($list_ValueNotDelete)) {
                    $this->db->where_not_in('id', $list_ValueNotDelete);
                }
                $this->db->delete(db_prefix() . 'suppliers_value');
            }

            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                unset($custom_fields);
                $custom_fields['suppliers']                = $_custom_fields['suppliers'];
                handle_custom_fields_post($id, $custom_fields);
            }
            log_activity('Update Suppliers Created [ID:' . $id . ', Name:' . $data['company'] . ']');
            if ($contacts) {
                $array = array();
                foreach ($contacts as $key => $value) {
                    if (empty($value['receive_email'])) {
                        $value['receive_email'] = 0;
                    }
                    if (empty($value['main_contact'])) {
                        $value['main_contact'] = 0;
                    }
                    $contact = array(
                        'id_supplers' => $id,
                        'name' => $value['name'],
                        'phone' => $value['phone'],
                        'email' => $value['email'],
                        'address' => $value['address'],
                        'sex' => $value['sex'],
                        'note' => $value['note'],
                        'birthday' => to_sql_date($value['birthday']),
                        'main_contact' => $value['main_contact'],
                        'receive_email' => $value['receive_email'],
                        'date_create' => date('Y-m-d H:i:s'),
                        'staff_create' => get_staff_user_id(),
                    );
                    if (empty($value['id'])) {
                        $this->db->insert(db_prefix() . 'contacts_suppliers', $contact);
                        $id_contact = $this->db->insert_id();
                        $array[] = $id_contact;
                    } else {
                        unset($contact['id_supplers']);
                        unset($contact['date_create']);
                        unset($contact['staff_create']);
                        $this->db->where('id', $value['id']);
                        $this->db->update(db_prefix() . 'contacts_suppliers', $contact);
                        $array[] = $value['id'];
                    }
                }
            }
            $this->db->where('id_supplers', $id);
            if (!empty($array)) {
                $this->db->where_not_in('id', $array);
            }
            $this->db->delete(db_prefix() . 'contacts_suppliers');
            return true;
        }
    }
    public function get_suppliers_contacts($id = '')
    {
        $this->db->where('id_supplers', $id);
        return $this->db->get(db_prefix() . 'contacts_suppliers')->result_array();
    }
    public function get_suppliers($id = '')
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'suppliers')->row();
    }
    public function get_groups($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'suppliers_groups')->row();
        }
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'suppliers_groups')->result_array();
    }
    public function change_suppliers_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'suppliers', [
            'active' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            log_activity('Suppliers Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }
    public function add_group($data)
    {
        $this->db->insert(db_prefix() . 'suppliers_groups', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Suppliers Group Created [ID:' . $insert_id . ', Name:' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }
    public function edit_group($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update(db_prefix() . 'suppliers_groups', [
            'code' => $data['code'],
            'name' => $data['name'],
        ]);
        if ($this->db->affected_rows() > 0) {
            log_activity('Suppliers Group Updated [ID:' . $data['id'] . ']');

            return true;
        }

        return false;
    }
    public function delete_group($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'suppliers_groups');
        if ($this->db->affected_rows() > 0) {
            // $this->db->where('groupid', $id);
            // $this->db->delete(db_prefix().'customer_groups');

            // hooks()->do_action('customer_group_deleted', $id);

            log_activity('Suppliers Group Deleted [ID:' . $id . ']');

            return true;
        }

        return false;
    }
    public function delete_suppliers($id = '')
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'suppliers');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('id_supplers', $id);
            $this->db->delete(db_prefix() . 'contacts_suppliers');

            $this->db->where('id_suppliers', $id);
            $this->db->delete('tblmainstream_goods');
            $this->db->where('id_suppliert', $id);
            $this->db->delete('tblsuppliers_value');
            log_activity('Suppliers Deleted [ID:' . $id . ']');

            return true;
        }

        return false;
    }
    public function delete_contact($id = '')
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'contacts_suppliers');
        if ($this->db->affected_rows() > 0) {

            log_activity('Suppliers Contacts Deleted [ID:' . $id . ']');

            return true;
        }

        return false;
    }
    public function get_gmail($id = '')
    {
        if (!empty($id)) {
            $this->db->select('email');
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'suppliers')->row()->email;
        }
    }
    public function searchSuppliers($q, $limit = 50)
    {
        $this->db->select('tblsuppliers.id as id, tblsuppliers.company as text', false);
        $this->db->from('tblsuppliers');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tblsuppliers.company', $q);
            $this->db->or_like('tblsuppliers.code', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }
}
