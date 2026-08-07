<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Import_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        $this->db->select('tblimport.*')->distinct();
        $this->db->from('tblimport');
        $this->db->where('id', $id);
        $purchases = $this->db->get()->row();
        $purchases->items = $this->get_items_import($id);
        return $purchases;
    }
    public function get_items_import($id)
    {
        $items = get_table_where('tblimport_items', array('id_import' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item,tblunits.unit as unit,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_warehouses_id']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name' => $localton));
            } elseif ($value['type'] == 'nvl') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit,exchange_unit,exchange_standard_unit,exchange_unit_payment,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock,exchange_unit,exchange_standard_unit,exchange_unit_payment,recipe,paper,longs,wide')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton = get_listname_localtion_warehouse($value['localtion_warehouses_id']);
                $items[$key] = array_merge($items[$key], array('localtion_name' => $localton));
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton = get_listname_localtion_warehouse($value['localtion_warehouses_id']);
                $items[$key] = array_merge($items[$key], array('localtion_name' => $localton));
            }
        }
        return $items;
    }
    public function get_targert($id)
    {
        $this->db->select('tblevaluation_criteria.*')->distinct();
        $this->db->from('tblevaluation_criteria');
        $targert = $this->db->get()->result_array();
        foreach ($targert as $key => $value) {
            $targert[$key]['targert'] = get_table_where('tblevaluation_criteria_children', array('id_evaluation' => $value['id']));
            foreach ($targert[$key]['targert'] as $k => $v) {
                $targert[$key]['targert'][$k]['point'] = get_table_where('tblevaluate_suppliers', array('id_rfq_ask_price' => $id, 'id_evaluation_criteria' => $value['id'], 'id_evaluation_criteria_children' => $v['id']));
            }
        }
        foreach ($targert as $key => $value) {
            if (empty($value['targert'])) {
                unset($targert[$key]);
            }
        }
        return $targert;
    }
    public function add($data = '')
    {
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $plan_id_text = '';
        $plan_id_check = 0;
        $import = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblimport') + 1),
            'prefix' => get_option('prefix_import'),
            'note' => $data['reason'],
            'suppliers_id' => $data['suppliers_id'],
            'warehouse_id' => $data['warehouse_id'],
            'delivery_supplier_code' => $data['delivery_supplier_code'],
            'date' => to_sql_date($data['date'], true),
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y:m:d H:i:s'),
            'status' => 2,
            'history_status'=>'|' . get_staff_user_id() . ',' . date('y-m-d H:i:s')
        );
        $lot_code = '';
        $suppliers = get_table_where('tblsuppliers', array('id' => $data['suppliers_id']), '', 'row');
        $code = sprintf('%03d', GetLotCode($data['suppliers_id'], $import['date']));
        $time_to = strtotime($import['date']);
        $day = strftime("%d", $time_to);
        $month = strftime("%m", $time_to);
        $year = strftime("%y", $time_to);
        $lot_code = $day . $month . $year . $suppliers->code . $code;
        if (empty($data['id_order'])) {
            unset($data['id_order']);
        } else {
            $id_order = get_table_where('tblpurchase_order', array('id' => $data['id_order']), '', 'row');
            $import['type_plan'] = $id_order->type_plan;
            $import['id_order'] = $data['id_order'];
        }
        $import['number_code'] = $lot_code;
        if ($this->db->insert('tblimport', $import)) {
            $id = $this->db->insert_id();

            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                unset($custom_fields);
                $custom_fields['imports'] = $_custom_fields['imports'];
                handle_custom_fields_post($id, $custom_fields);
            }
            log_activity('Import Insert [ID: ' . $id . ']');
            $count = 0;
            $items = $data['items'];
            $total = 0;
            $total_amount = 0;
            foreach ($items as $key => $item) {
                $item_id = explode("__", $item['id']);
                $item['id'] = $item_id[0];
                if (!empty($item['id'])) {
                    if (empty($item['promotion_suppliers_1'])) {
                        $item['promotion_suppliers_1'] = 0;
                    }
                    if (empty($item['promotion_suppliers'])) {
                        $item['promotion_suppliers'] = 0;
                    }

                    if (empty($item['localtion_warehouses_id'])) {
                        $plan = get_table_where('tbl_productions_plan', ['id' => $item['plan_id']], '', 'row_array');
                        $nameLocation = $plan['reference_no'];

                        $ktr_local = get_table_where('tbllocaltion_warehouses', array(
                            'name' => $nameLocation,
                            'warehouse' => $data['warehouse_id'],
                            'productions_plan_id' => $item['plan_id']
                        ), '', 'row');
                        if (empty($ktr_local)) {
                            $in = [
                                'name' => $nameLocation,
                                'code' => $nameLocation,
                                'warehouse' => $data['warehouse_id'],
                                'name_parent' => $nameLocation,
                                'child' => 1,
                                'create_by' => get_staff_user_id(),
                                'date_create' => date('Y-m-d H:i:s'),
                                'status' => 0,
                                'lever' => 1,
                                'productions_plan_id' => $item['plan_id'],
                            ];
                            $this->db->insert('tbllocaltion_warehouses', $in);
                            $localtion_warehouses_id = $this->db->insert_id();
                        } else {
                            $localtion_warehouses_id = $ktr_local->id;
                        }
                    } else {
                        $localtion_warehouses_id = $item['localtion_warehouses_id'];
                    }
                    if (empty($item['plan_id'])) {
                        $plan_id = 0;
                    } else {
                        $plan_id = $item['plan_id'];
                        if ($item['plan_id'] != $plan_id_check) {
                            $plan_id_text .= $item['plan_id'] . ',';
                            $plan_id_check = $item['plan_id'];
                        }
                    }
                    if ($item['type'] == 'tools') {
                        $item['date_sx'] = NULL;
                        $item['date_sd'] = NULL;
                        $item['date_use'] = NULL;
                    } else {
                        $item['date_sx'] = to_sql_date($item['date_sx']);
                        $item['date_sd'] = to_sql_date($item['date_sd']);
                        $item['date_use'] = str_replace(',', '', $item['date_use']);
                    }
                    $info_items = array();
                    $info_items['recipe'] = $item['recipe'];
                    $info_items['paper'] = $item['paper'];
                    $info_items['longs'] = $item['longs'];
                    $info_items['wide'] = $item['wide'];
                    $info_items_text = json_encode($info_items);
                    $item['exchange_standard_unit'] = str_replace(',', '', $item['exchange_standard_unit']);
                    $item['quantity_stock'] = str_replace(',', '', $item['quantity_stock']);
                    $item['exchange_stock'] = str_replace(',', '', $item['exchange_stock']);
                    $item['quantity_payment'] = str_replace(',', '', $item['quantity_payment']);
                    $item['exchange_payment'] = str_replace(',', '', $item['exchange_payment']);

                    $itemss = array(
                        'id_import' => $id,
                        'product_id' => $item['id'],
                        'date_sx' => $item['date_sx'],
                        'date_sd' => $item['date_sd'],
                        'date_use' => $item['date_use'],
                        'order_item_id ' => $item['idd'],
                        'quantity' => str_replace(',', '', $item['quantity']),
                        'quantity_net' => str_replace(',', '', $item['quantity_net']),
                        'tax_id' => str_replace(',', '', $item['tax_id']),
                        'tax_rate' => $item['tax_rate'],
                        'localtion_warehouses_id' => $localtion_warehouses_id,
                        'type' => $item['type'],
                        'note' => $item['note'],
                        'id_purchase_order_items' => $item['idd'],
                        // 'lot_code' => $item['lot_code'],
                        'lot_code' => $lot_code,
                        'barcode' => $item['barcode'],
                        'price' => str_replace(',', '', $item['price']),
                        'promotion_suppliers_1' => str_replace(',', '', $item['promotion_suppliers_1']),
                        'promotion_suppliers' => str_replace(',', '', $item['promotion_suppliers']),
                        'plan_id' => $plan_id,
                        'quantity_unit' => str_replace(',', '', $item['quantity_net']),
                        'exchange_unit' => $item['exchange_standard_unit'],
                        'quantity_stock' => $item['quantity_stock'],
                        'exchange_stock' => $item['exchange_stock'],
                        'quantity_payment' => $item['quantity_payment'],
                        'exchange_payment' => $item['exchange_payment'],
                        'info_items' => $info_items_text,
                    );

                   
                    $total += $itemss['price'] * $itemss['quantity_payment'] - $itemss['promotion_suppliers_1'] * $itemss['quantity_payment'];

                    
                    $amount = ($itemss['price'] * $itemss['quantity_payment'] - $itemss['promotion_suppliers_1'] * $itemss['quantity_payment']) * ($itemss['tax_rate'] / 100) + ($itemss['price'] * $itemss['quantity_payment'] - $itemss['promotion_suppliers_1'] * $itemss['quantity_payment']);
                    $itemss['amount'] = $amount;
                    $total_amount += $amount;
                    if ($this->db->insert('tblimport_items', $itemss)) {
                        $id_items_import = $this->db->insert_id();
                        if (!empty($data['id_order'])) {
                            $ktr_items = get_table_where('tblpurchase_to_order_items', array('id_items' => $item['id'], 'type' => $item['type'], 'id_purchase_order' => $data['id_order'], 'id_purchase_order_items' => $item['idd']));
                            if (!empty($ktr_items)) {
                                $htmls = '';
                                $quantity_ch = $item['quantity_net'];
                                foreach ($ktr_items as $key => $value) {
                                    $quantity_v1 = $quantity_ch - ($value['quantity'] - $value['quantyti_import']);
                                    if ($quantity_v1 > 0) {
                                        $this->db->update('tblpurchase_to_order_items', array('quantyti_import' => $value['quantity']), array('id' => $value['id']));
                                        $htmls .= $value['id'] . '||' . (($value['quantity'] - $value['quantyti_import'])) . ',';
                                        $quantity_ch = $quantity_ch - ($value['quantity'] - $value['quantyti_import']);
                                    } else {
                                        $this->db->update('tblpurchase_to_order_items', array('quantyti_import' => ($value['quantyti_import'] + $quantity_ch)), array('id' => $value['id']));
                                        $htmls .= $value['id'] . '||' . ($quantity_ch) . ',';
                                        $quantity_ch = 0;
                                    }
                                }
                                $this->db->update('tblimport_items', array('id_check' => $htmls), array('id' => $id_items_import));
                            }
                        }
                        $count++;
                        log_activity('Imports items insert [ID Import: ' . $id . ', ID Product: ' . $itemss['product_id'] . ']');
                    } else {
                        exit("error");
                    }
                }
            }
        }
        if ($count > 0) {
            $plan_id_text = trim($plan_id_text, ',');
            $this->db->update('tblimport', array('total' => $total_amount, 'total_novat' => $total, 'plan_id' => $plan_id_text), array('id' => $id));
            return $id;
        }

        return false;
    }
    public function update($data = NULL, $id = '')
    {
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $plan_id_text = '';
        $plan_id_check = 0;
        $import_main = get_table_where('tblimport', array('id' => $id),'','row');

        $import = array(
            'note' => $data['reason'],
            'suppliers_id' => $data['suppliers_id'],
            'warehouse_id' => $data['warehouse_id'],
            'delivery_supplier_code' => $data['delivery_supplier_code'],
            'date' => to_sql_date($data['date'], true),
        );
        $lot_code = $import_main->number_code;

        $this->db->where('id', $id);
        if ($this->db->update('tblimport', $import)) {
            $import_items = get_table_where('tblimport_items', array('id_import' => $id));
            foreach ($import_items as $key => $value) {
                $_data = explode(',', $value['id_check']);
                foreach ($_data as $k => $v) {
                    if (!empty($v)) {
                        $_data_v1 = explode('||', $v);
                        $data_check = get_table_where('tblpurchase_to_order_items', array('id' => $_data_v1[0]), '', 'row');
                        $this->db->update('tblpurchase_to_order_items', array('quantyti_import' => ($data_check->quantyti_import - $_data_v1[1])), array('id' => $_data_v1[0]));
                    }
                }
            }
            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                unset($custom_fields);
                $custom_fields['imports'] = $_custom_fields['imports'];
                handle_custom_fields_post($id, $custom_fields);
            }
            log_activity('Imports updateted [ID: ' . $id . ']');
            $count = true;
            $counts = 0;
        }
        $total = 0;
        $total_amount = 0;
        $itemss = $data['items'];

        if ($count) {
            $affected_id = array();
            foreach ($itemss as $key => $item) {
                if (!empty($item['id'])) {
                    $item['date_sx'] = to_sql_date($item['date_sx']);
                    $item['date_sd'] = to_sql_date($item['date_sd']);
                    $item['date_use'] = str_replace(',', '', $item['date_use']);
                    if ($item['type'] == 'tools') {
                        $item['date_sx'] = NULL;
                        $item['date_sd'] = NULL;
                        $item['date_use'] = NULL;
                    }
                    if (empty($item['promotion_suppliers_1'])) {
                        $item['promotion_suppliers_1'] = 0;
                    }
                    if (empty($item['promotion_suppliers'])) {
                        $item['promotion_suppliers'] = 0;
                    }

                    $it = get_table_where('tblimport_items', array('id_import' => $id, 'id' => $item['id_import_items']), '', 'row');
                    if (!empty($it)) {
                        $affected_id[] = $it->id;
                        if (empty($item['plan_id'])) {
                            $plan_id = 0;
                        } else {
                            $plan_id = $item['plan_id'];
                            if ($item['plan_id'] != $plan_id_check) {
                                $plan_id_text .= $item['plan_id'] . ',';
                                $plan_id_check = $item['plan_id'];
                            }
                        }
                        if (empty($item['localtion_warehouses_id'])) {
                            $plan = get_table_where('tbl_productions_plan', ['id' => $item['plan_id']], '', 'row_array');
                            $nameLocation = $plan['reference_no'];

                            $ktr_local = get_table_where('tbllocaltion_warehouses', array(
                                'name' => $nameLocation,
                                'warehouse' => $data['warehouse_id'],
                                'productions_plan_id' => $item['plan_id']
                            ), '', 'row');
                            if (empty($ktr_local)) {
                                $in = [
                                    'name' => $nameLocation,
                                    'code' => $nameLocation,
                                    'warehouse' => $data['warehouse_id'],
                                    'name_parent' => $nameLocation,
                                    'child' => 1,
                                    'create_by' => get_staff_user_id(),
                                    'date_create' => date('Y-m-d H:i:s'),
                                    'status' => 0,
                                    'lever' => 1,
                                    'productions_plan_id' => $item['plan_id'],
                                ];
                                $this->db->insert('tbllocaltion_warehouses', $in);
                                $localtion_warehouses_id = $this->db->insert_id();
                            } else {
                                $localtion_warehouses_id = $ktr_local->id;
                            }
                        } else {
                            $localtion_warehouses_id = $item['localtion_warehouses_id'];
                        }


                        $items = array(
                            'quantity' => str_replace(',', '', $item['quantity']),
                            'quantity_net' => str_replace(',', '', $item['quantity_net']),
                            'tax_id' => str_replace(',', '', $item['tax_id']),
                            'date_sx' => $item['date_sx'],
                            'date_sd' => $item['date_sd'],
                            'date_use' => $item['date_use'],
                            'tax_rate' => $item['tax_rate'],
                            'localtion_warehouses_id' => $localtion_warehouses_id,
                            'type' => $item['type'],
                            'lot_code' => $lot_code,
                            'note' => $item['note'],
                            'price' => str_replace(',', '', $item['price']),
                            'promotion_suppliers_1' => str_replace(',', '', $item['promotion_suppliers_1']),
                            'promotion_suppliers' => str_replace(',', '', $item['promotion_suppliers']),
                            'plan_id' => $plan_id,
                        );

                        $total += $items['price'] * $items['quantity_net'] - $items['promotion_suppliers_1'] * $items['quantity_net'];
                        $amount = ($items['price'] * $items['quantity_net'] - $items['promotion_suppliers_1'] * $items['quantity_net']) * ($items['tax_rate'] / 100) + ($items['price'] * $items['quantity_net'] - $items['promotion_suppliers_1'] * $items['quantity_net']);
                        $items['amount'] = $amount;
                        $total_amount += $amount;
                        $success = $this->db->update('tblimport_items', $items, array('id' => $it->id));
                        if ($this->db->affected_rows()) {
                            logActivity('Imports items updateted [ID Purchase: ' . $id . ', ID Product: ' . $it->product_id . ']');
                            $counts++;
                        }
                    } else {
                        if (empty($item['plan_id'])) {
                            $plan_id = 0;
                        } else {
                            $plan_id = $item['plan_id'];
                            if ($item['plan_id'] != $plan_id_check) {
                                $plan_id_text .= $item['plan_id'] . ',';
                                $plan_id_check = $item['plan_id'];
                            }
                        }
                        if (empty($item['localtion_warehouses_id'])) {
                            $plan = get_table_where('tbl_productions_plan', ['id' => $item['plan_id']], '', 'row_array');
                            $nameLocation = $plan['reference_no'];

                            $ktr_local = get_table_where('tbllocaltion_warehouses', array(
                                'name' => $nameLocation,
                                'warehouse' => $data['warehouse_id'],
                                'productions_plan_id' => $item['plan_id']
                            ), '', 'row');
                            if (empty($ktr_local)) {
                                $in = [
                                    'name' => $nameLocation,
                                    'code' => $nameLocation,
                                    'warehouse' => $data['warehouse_id'],
                                    'name_parent' => $nameLocation,
                                    'child' => 1,
                                    'create_by' => get_staff_user_id(),
                                    'date_create' => date('Y-m-d H:i:s'),
                                    'status' => 0,
                                    'lever' => 1,
                                    'productions_plan_id' => $item['plan_id'],
                                ];
                                $this->db->insert('tbllocaltion_warehouses', $in);
                                $localtion_warehouses_id = $this->db->insert_id();
                            } else {
                                $localtion_warehouses_id = $ktr_local->id;
                            }
                        } else {
                            $localtion_warehouses_id = $item['localtion_warehouses_id'];
                        }
                        $items = array(
                            'id_import' => $id,
                            'product_id' => $item['id'],
                            'quantity' => str_replace(',', '', $item['quantity']),
                            'quantity_net' => str_replace(',', '', $item['quantity_net']),
                            'tax_id' => str_replace(',', '', $item['tax_id']),
                            'tax_rate' => $item['tax_rate'],
                            'date_sx' => $item['date_sx'],
                            'date_sd' => $item['date_sd'],
                            'date_use' => $item['date_use'],
                            'lot_code' => $lot_code,
                            'id_purchase_order_items' => $item['idd'],
                            'localtion_warehouses_id' => $localtion_warehouses_id,
                            'type' => $item['type'],
                            'note' => $item['note'],
                            'price' => str_replace(',', '', $item['price']),
                            'promotion_suppliers_1' => str_replace(',', '', $item['promotion_suppliers_1']),
                            'promotion_suppliers' => str_replace(',', '', $item['promotion_suppliers']),
                            'plan_id' => $plan_id,
                        );
                        $total += $items['price'] * $items['quantity_net'] - $items['promotion_suppliers_1'] * $items['quantity_net'];
                        $amount = ($items['price'] * $items['quantity_net'] - $items['promotion_suppliers_1'] * $items['quantity_net']) * ($items['tax_rate'] / 100) + ($items['price'] * $items['quantity_net'] - $items['promotion_suppliers_1'] * $items['quantity_net']);
                        $items['amount'] = $amount;
                        $total_amount += $amount;
                        if ($this->db->insert('tblimport_items', $items)) {
                            $affected_id[] = $this->db->insert_id();
                            log_activity('Imports items insert [ID Purchase: ' . $id . ', ID Product: ' . $item['id'] . ']');
                            $counts++;
                        }
                    }
                    $id_order = get_table_where('tblimport', array('id' => $id), '', 'row')->id_order;
                    if (!empty($data['id_order'])) {
                        $ktr_items = get_table_where('tblpurchase_to_order_items', array('id_items' => $item['id'], 'type' => $item['type'], 'id_purchase_order' => $id_order));
                        if (!empty($ktr_items)) {
                            $htmls = '';
                            $quantity_ch = $item['quantity_net'];
                            foreach ($ktr_items as $key => $value) {
                                $quantity_v1 = $quantity_ch - ($value['quantity'] - $value['quantyti_import']);
                                if ($quantity_v1 > 0) {
                                    $this->db->update('tblpurchase_to_order_items', array('quantyti_import' => $value['quantity']), array('id' => $value['id']));
                                    $htmls .= $value['id'] . '||' . (($value['quantity'] - $value['quantyti_import'])) . ',';
                                    $quantity_ch = $quantity_ch - ($value['quantity'] - $value['quantyti_import']);
                                } else {
                                    $this->db->update('tblpurchase_to_order_items', array('quantyti_import' => ($value['quantyti_import'] + $quantity_ch)), array('id' => $value['id']));
                                    $htmls .= $value['id'] . '||' . ($quantity_ch) . ',';
                                    $quantity_ch = 0;
                                }
                            }
                            $this->db->update('tblimport_items', array('id_check' => $htmls), array('id' => $id_items_import));
                        }
                    }
                }
            }
        }
        if (empty($affected_id)) {
            $this->db->where('id_import', $id);
            $this->db->delete('tblimport_items');
        } else {
            $this->db->where('id_import', $id);
            $this->db->where_not_in('id', $affected_id);
            $this->db->delete('tblimport_items');
        }
        if ($counts > 0) {
            $plan_id_text = trim($plan_id_text, ',');
            $this->db->update('tblimport', array('total' => $total_amount, 'total_novat' => $total, 'plan_id' => $plan_id_text), array('id' => $id));
            return true;
        }

        return false;
    }
    public function delete($id = '')
    {
        $import = get_table_where('tblimport', array('id' => $id), '', 'row');
        if ($this->isInvoiced($id)) {
            return 99;
        }
        $this->db->where('id', $id);
        $this->db->delete('tblimport');
        if ($this->db->affected_rows() > 0) {
            if (!empty($import->warehouseman_id) || ($import->warehouseman_id != 0)) {
                $this->decreaseWarehouse($id, $import->suppliers_id);
            }
            $import_items = get_table_where('tblimport_items', array('id_import' => $id));
            foreach ($import_items as $key => $value) {
                $_data = explode(',', $value['id_check']);
                foreach ($_data as $k => $v) {
                    if (!empty($v)) {
                        $_data_v1 = explode('||', $v);
                        $data_check = get_table_where('tblpurchase_to_order_items', array('id' => $_data_v1[0]), '', 'row');
                        $this->db->update('tblpurchase_to_order_items', array('quantyti_import' => ($data_check->quantyti_import - $_data_v1[1])), array('id' => $_data_v1[0]));
                    }
                }
            }
            $this->db->where('id_import', $id);
            $this->db->delete('tblimport_items');
            if (!empty($import->id_order)) {
                $ktr = get_table_where('tblpurchase_order', array('id' => $import->id_order), '', 'row');
                if (!empty($ktr)) {
                    if (explode(',', $ktr->cancel)[0] == '1foso') {
                        $cancels = 0;
                        $cancel = array(
                            'cancel' => $cancels
                        );
                        $this->db->where('id', $import->id_order);
                        $this->db->update('tblpurchase_order', $cancel);
                    }
                }
            }
            log_activity('Imports Deleted [ID:' . $id . ']');
            return true;
        }

        return false;
    }
    public function update_status($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tblimport', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function get_items_import_v2($id)
    {
        $items = get_table_where('tblimport_items', array('id_import' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_warehouses_id']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name' => $localton));
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton = get_listname_localtion_warehouse($value['localtion_warehouses_id']);
                $items[$key] = array_merge($items[$key], array('localtion_name' => $localton));
            }
        }
        return $items;
    }
    //tăng kho
    public function increaseWarehouse($id)
    {
        $import = $this->get($id);
        $this->db->select('tblimport_items.*,SUM(quantity_stock) as quantity_nets,SUM(quantity_unit) as quantity_units,SUM(quantity_payment) as quantity_payments');
        $this->db->where('id_import', $id);
        $this->db->group_by('tblimport_items.product_id,tblimport_items.type,lot_code,date_sx,date_sd,date_use');
        $import->items = $this->db->get('tblimport_items')->result_array();
        $count = 0;
        if ($import) {
            $date_warehouse = date('Y-m-d H:i:s');
            $warehouse_id = $import->warehouse_id;
            $date_import = $import->date;
            foreach ($import->items as $key => $value) {
                $localtion =  $value['localtion_warehouses_id'];
                $product_id = $value['product_id'];
                $type_items = $value['type'];
                $quantity = $value['quantity_nets'];
                $quantity_unit = $value['quantity_units'];
                $quantity_payment = $value['quantity_payments'];
                $pirce = $value['price'];
                $idd = $value['id'];
                $lot_code = $value['lot_code'];
                $date_sx = $value['date_sx'];
                $date_sd = $value['date_sd'];
                $date_use = empty($value['date_use']) ? NULL : $value['date_use'];
                $count = increaseProductQuantity($warehouse_id, $id, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $pirce, $idd, $lot_code, $date_sx, $date_sd, $date_use,$quantity_unit,$quantity_payment);
                //tăng kho tổng
                increaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items, $lot_code, $date_sx, $date_sd, $date_use,$quantity_unit,$quantity_payment);
                increaseSuppliertsQuantity($import->suppliers_id, 1, $product_id, $quantity, $type_items, $lot_code, $date_sx, $date_sd, $date_use,$quantity_unit,$quantity_payment);
            }
        }
        if ($count) {
            return true;
        }
        return false;
    }
    //giảm kho phiếu nhập xóa dữ liệu trong kho
    public function decreaseWarehouse($id, $suppliers_id = '')
    {
        if (is_numeric($id)) {
            $warehouse_product = get_table_where("tblwarehouse_product", array('import_id' => $id, 'type_export' => 1));
            $this->db->delete('tblwarehouse_product', array('import_id' => $id, 'type_export' => 1));
            //Giảm kho tổng
            foreach ($warehouse_product as $key => $value) {
                decreaseWarehuseQuantity($value['warehouse_id'], $value['localtion'], $value['product_id'], $value['quantity'], $value['type_items'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'], $value['product_quantity_unit'], $value['product_quantity_payment']);
                decreaseSuppliersQuantity($suppliers_id, 1, $value['product_id'], $value['quantity'], $value['type_items'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'], $value['product_quantity_unit'], $value['product_quantity_payment']);
            }
        }
        return true;
    }

    // Cập nhật lại Tổng tiền của Nhập hàng
    public function refreshTotalMoney ($import_id)
    {
        $items = get_table_where('tblimport_items', array('id_import' => $import_id));
        $total = 0; $total_novat = 0;
        foreach ($items as $item) {
            $total += $item['amount'];
            $total_novat += (($item['price'] * $item['quantity_payment']) - $item['promotion_suppliers']);
        }
        $this->db->where('id', $import_id);
        $isSuccess = $this->db->update('tblimport', ['total' => $total, 'total_novat' => $total_novat]);
        return $isSuccess;
    }

    // Cập nhật lại cột id Hóa đơn 
    public function refreshInvoice_id ($import_id)
    {
        $this->db->select('id, id_import');
        $this->db->from('tblpurchase_invoice');
        $this->db->like('id_import', $import_id);
        $arrInvoice = $this->db->get()->result_array();
        $arrInvoiceId = [];
        foreach ($arrInvoice as $invoice) {
            $arrImport_id = explode(',', $invoice['id_import']);
            if (in_array($import_id, $arrImport_id)) {
                $arrInvoiceId[] = $invoice['id'];
            }
        }

        if (!empty($arrInvoiceId)) {
            $this->db->where('id', $import_id);
            $isSuccess = $this->db->update('tblimport', ['invoice_id' => implode(',', $arrInvoiceId)]);
            return $isSuccess;
        } else {
            $this->db->where('id', $import_id);
            $isSuccess = $this->db->update('tblimport', ['invoice_id' => '0']);
            return $isSuccess;
        }
    }

    // Cập nhật lại cột Xuất hóa đơn hết chưa
    public function refreshRed_invoice($import_id)
    {
        $arrImportItems = get_table_where('tblimport_items', array('id_import' => $import_id));
        $arrImportItemId = [];
        foreach ($arrImportItems as $importItem) {
            $arrImportItemId[] = $importItem['id'];
        }
        $this->db->select('*');
        $this->db->from('tblpurchase_invoice_items');
        $this->db->where_in('id_import_item', $arrImportItemId);
        $this->db->group_by('id_import_item');
        $invoice_items = $this->db->get()->result_array();
        $invoice_items_id = [];
        foreach ($invoice_items as $invoice_item) {
            $invoice_items_id[] = $invoice_item['id_import_item'];
        }
        $diff = array_diff($arrImportItemId, $invoice_items_id);
        if (empty($diff)) {
            $this->db->where('id', $import_id);
            $isSuccess = $this->db->update('tblimport', ['red_invoice' => 1]);
        } else {
            $this->db->where('id', $import_id);
            $isSuccess = $this->db->update('tblimport', ['red_invoice' => 0]);
        }
        return $isSuccess;
    }

    // Kiểm tra Phiếu nhập đã xuất hóa đơn chưa
    public function isInvoiced ($import_id)
    {
        $result = get_table_where('tblimport', array('id' => $import_id), '', 'row', '', 'invoice_id');
        if (!empty($result)) {
            if (!empty($result->invoice_id)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
