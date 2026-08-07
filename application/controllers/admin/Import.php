<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Import extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_items_model');
        $this->load->model('purchase_order_model');
        $this->load->model('import_model');
        $this->load->model('costs_model');


        $this->load->model('orders_model');
        $this->load->model('quotes_orders_model');
        $this->load->model('manufactures_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('deliveries_model');
        $this->load->model('returned_goods_model');
        $this->load->model('type_orders_model');
        $this->load->model('status_orders_model');
        $this->load->model('price_list_model');
        $this->load->model('transfer_model');


    }

    public function import()
    {
        if ($this->input->post()) {
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->helper('security');
            $data = $this->input->post();
            $CountAdd = 0;
            $CountAll = 0;
            if (!empty($_FILES['file_excel'])) {
                $fullfile = $_FILES['file_excel']['tmp_name'];
                $extension = strtoupper(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));
                if ($extension != 'XLSX' && $extension != 'XLS') {
                    $this->session->set_flashdata('warning', lang('Không đúng định dạng excel'));
                    redirect($_SERVER["HTTP_REFERER"]);
                    return;
                }

                $inputFileType = PHPExcel_IOFactory::identify($fullfile);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load("$fullfile");
                $total_sheets = $objPHPExcel->getSheetCount();
                $allSheetName = $objPHPExcel->getSheetNames();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                for ($row = 9; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                        $rows[$row - 1][$col] = $value;
                    }
                }
                $data['total_rows_post'] = count($rows);
                if (count($rows) <= 1) {
                    set_alert('warning', 'Not enought rows for importing');
                    redirect(admin_url('clients/import'));
                }
                $import = array();
                $dem_key = 0;
                $dong = 8;
                $data['loi'] = '';
                foreach ($rows as $key => $row) {
                    $dem_key++;
                    $dong++;
                    if (!empty($row[0]) && is_numeric($row[0])) {
                        if (!empty($import)) {
                            $data_ok = true;
                            $ktr_import = get_table_where('tblimport', array('code' => $import['code']), '', 'row');

                            if (!empty($ktr_import)) {
                                $data['loi'] .= 'Trùng phiếu nhập tại dòng ' . $import['dong'] . '<br>';
                                $data_ok = false;
                            }
                            $suppliers = explode(' - ', $import['suppliers']);
                            $ktr_supp = get_table_where('tblsuppliers', array('company' => $suppliers[0]), '', 'row');
                            if (!empty($ktr_supp)) {
                                $import['suppliers_id'] = $ktr_supp->id;
                            } else {
                                $data['loi'] .= 'Không tìm thấy nhà cung cấp tại dòng ' . $import['dong'] . '<br>';
                                $data_ok = false;
                            }
                            if ($data_ok) {
                                foreach ($import['items'] as $k => $v) {
                                    $items = explode(' - ', $v['item']);
                                    $ktr_items = get_table_where('tbl_materials', array('name' => $items[1]), '', 'row');
                                    if (!empty($ktr_items)) {
                                        $import['items'][$k]['product_id'] = $ktr_items->id;
                                    } else {
                                        $data['loi'] .= 'Không tìm thấy nguyên vật liệu tại dòng ' . $v['dong'] . '<br>';
                                        $data_ok = false;
                                    }
                                }
                            }
                            if ($data_ok == true) {
                                $_import = array(
                                    'code' => $import['code'],
                                    'prefix' => get_option('prefix_import'),
                                    'note' => $import['note'],
                                    'suppliers_id' => $import['suppliers_id'],
                                    'warehouse_id' => $data['warehouse_id'],
                                    'date' => $import['date'],
                                    'staff_create' => get_staff_user_id(),
                                    'date_create' => date('Y:m:d H:i:s'),
                                    'status' => 2,
                                );
                                foreach ($import['items'] as $k_1 => $v_1) {
                                    $_import['items'][] = array(
                                        'id' => $v_1['product_id'],
                                        'quantity' => $v_1['quantity'],
                                        'quantity_net' => $v_1['quantity'],
                                        'tax_id' => 0,
                                        'tax_rate' => 0,
                                        'localtion_warehouses_id' => $data['localtion_warehouses_id'],
                                        'price' => $v_1['price'],
                                        'promotion_suppliers' => 0,
                                        'promotion_suppliers_1' => 0,
                                        'note' => '',
                                        'type' => 'nvl',
                                    );
                                }
                                $id_import = $this->add_import($_import);

                                $id_order = $this->add_order($_import);
                                if (!empty($id_import)) {
                                    $__data = array(
                                        'warehouseman_id' => get_staff_user_id(),
                                        'warehouseman_date' => date('Y-m-d H:i:s')
                                    );
                                    $success = $this->db->update('tblimport', $__data, array('id' => $id_import));
                                    $this->import_model->increaseWarehouse($id_import);
                                    if (!empty($id_order)) {
                                        $this->db->update('tblimport', array('id_order' => $id_order), array('id' => $id_import));
                                    }
                                }
                            }
                        }
                        $import = array();
                        $dem = 0;
                        $import['dong'] = $dong;
                        $import['date'] = date("Y-m-d", ($row[0] - 25569) * 86400);
                        $import['code'] = $row[1];
                        $import['suppliers'] = $row[2];
                    } else {
                        if (!empty($import)) {
                            if (empty($row[4])) {
                                $import['note'] = $row[2];
                            } else {
                                if ($row[4] != 'Cộng:') {
                                    $import['items'][$dem]['dong'] = $dong;
                                    $import['items'][$dem]['item'] = $row[2];
                                    $import['items'][$dem]['quantity'] = $row[4];
                                    $import['items'][$dem]['price'] = $row[5];
                                    $dem++;
                                }
                            }
                        }
                    }
                    if ($dem_key == (count($rows))) {

                        if (!empty($import)) {
                            $data_ok = true;
                            $ktr_import = get_table_where('tblimport', array('code' => $import['code']), '', 'row');
                            if (!empty($ktr_import)) {
                                $data['loi'] .= 'Trùng phiếu nhập tại dòng ' . $import['dong'] . '<br>';
                                $data_ok = false;
                            }
                            $suppliers = explode(' - ', $import['suppliers']);
                            $ktr_supp = get_table_where('tblsuppliers', array('company' => $suppliers[0]), '', 'row');
                            if (!empty($ktr_supp)) {
                                $import['suppliers_id'] = $ktr_supp->id;
                            } else {
                                $data['loi'] .= 'Không tìm thấy nhà cung cấp tại dòng ' . $import['dong'] . '<br>';
                                $data_ok = false;
                            }
                            if ($data_ok) {
                                foreach ($import['items'] as $k => $v) {
                                    $items = explode(' - ', $v['item']);
                                    $ktr_items = get_table_where('tbl_materials', array('name' => $items[1]), '', 'row');
                                    if (!empty($ktr_items)) {
                                        $import['items'][$k]['product_id'] = $ktr_items->id;
                                    } else {
                                        $data['loi'] .= 'Không tìm thấy nguyên vật liệu tại dòng ' . $v['dong'] . '<br>';
                                        $data_ok = false;
                                    }
                                }
                            }
                            if ($data_ok) {

                                $_import = array(
                                    'code' => $import['code'],
                                    'prefix' => get_option('prefix_import'),
                                    'note' => $import['note'],
                                    'suppliers_id' => $import['suppliers_id'],
                                    'warehouse_id' => $data['warehouse_id'],
                                    'date' => $import['date'],
                                    'staff_create' => get_staff_user_id(),
                                    'date_create' => date('Y:m:d H:i:s'),
                                    'status' => 2,
                                );
                                foreach ($import['items'] as $k_1 => $v_1) {
                                    $_import['items'][] = array(
                                        'id' => $v_1['product_id'],
                                        'quantity' => $v_1['quantity'],
                                        'quantity_net' => $v_1['quantity'],
                                        'tax_id' => 0,
                                        'tax_rate' => 0,
                                        'localtion_warehouses_id' => $data['localtion_warehouses_id'],
                                        'price' => $v_1['price'],
                                        'promotion_suppliers' => 0,
                                        'promotion_suppliers_1' => 0,
                                        'note' => '',
                                        'type' => 'nvl',
                                    );
                                }
                                $id_import = $this->add_import($_import);

                                $id_order = $this->add_order($_import);
                                if (!empty($id_import)) {
                                    $__data = array(
                                        'warehouseman_id' => get_staff_user_id(),
                                        'warehouseman_date' => date('Y-m-d H:i:s')
                                    );
                                    $success = $this->db->update('tblimport', $__data, array('id' => $id_import));
                                    $this->import_model->increaseWarehouse($id_import);
                                    if (!empty($id_order)) {
                                        $this->db->update('tblimport', array('id_order' => $id_order), array('id' => $id_import));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $data['title'] = _l('Import phiếu nhập');
        $data['warehouse'] = get_table_where('tblwarehouse');

        $this->load->view('admin/import/import', $data);
    }

    public function add_order($data = array())
    {
        $quotes = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchase_order') + 1),
            'prefix' => get_option('prefix_purchase_order'),
            'staff_create' => get_staff_user_id(),
            'date' => $data['date'],
            'delivery_date' => date('Y-m-d'),
            'date_create' => date('Y:m:d H:i:s'),
            'suppliers_id' => $data['suppliers_id'],
            'type_items' => -1,
            'tax_all' => 0,
            'status' => 3,
            'note' => '',
            'history_status' => get_staff_user_id() . ',' . date('Y:m:d H:i:s') . '|' . get_staff_user_id() . ',' . date('Y:m:d H:i:s') . '|' . get_staff_user_id() . ',' . date('Y:m:d H:i:s'),
            'delivery_cost' => 0,
            'reduce_cost' => 0,
        );
        $delivery_cost = 0;
        $reduce_cost = 0;
        if ($this->db->insert('tblpurchase_order', $quotes)) {
            $id = $this->db->insert_id();
            $count = 1;
        }
        $items = $data['items'];
        if ($id) {
            $total_expected_all = 0;
            $total_suppliers_all = 0;
            $total_novat = 0;
            $promotion_expecteds = 0;

            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {
                    $item['quantity'] = $item['quantity'];
                    $item['quantity_suppliers'] = $item['quantity'];
                    $item['price_expected'] = 0;
                    $item['price_suppliers'] = $item['price'];
                    $item['promotion_expected'] = 0;
                    $total_expected = $item['quantity'] * $item['price_expected'] * (1 + ($item['tax_rate'] / 100));
                    $total_suppliers = (($item['quantity_suppliers'] * $item['price_suppliers'] * (1 + ($item['tax_rate'] / 100))) - $item['promotion_expected']);
                    $total_novats = ($item['quantity_suppliers'] * $item['price_suppliers']);
                    $promotion_expecteds += $item['promotion_expected'];
                    $items = array(
                        'id_purchase_order' => $id,
                        'product_id' => $item['id'],
                        'type' => 'nvl',
                        'quantity' => $item['quantity'],
                        'tax_id' => 0,
                        'tax_rate' => 0,
                        'quantity_suppliers' => $item['quantity'],
                        'price_expected' => 0,
                        'price_suppliers' => $item['price'],
                        'promotion_expected' => 0,
                        'total_expected' => $total_expected,
                        'total_suppliers' => $total_suppliers,
                        'note' => $item['note'],
                    );
                    if ($this->db->insert('tblpurchase_order_items', $items)) {
                        $ktr_supp = get_table_where('tblmainstream_goods', array('id_suppliers' => $data['suppliers_id'], 'id_items' => $item['id'], 'type' => $item['type']), '', 'row');
                        if (!empty($ktr_supp)) {
                            $this->db->update('tblmainstream_goods', array('price' => $item['price_suppliers']), array('id' => $ktr_supp->id));
                        } else {
                            $_mainstream = array(
                                'id_suppliers' => $data['suppliers_id'],
                                'id_items' => $item['id'],
                                'type' => $item['type'],
                                'price' => $item['price_suppliers'],
                            );
                            $this->db->insert('tblmainstream_goods', $_mainstream);
                        }
                        $count++;
                        $total_expected_all += $total_expected;
                        $total_suppliers_all += $total_suppliers;
                        $total_novat += $total_novats;
                    } else {
                        exit("error");
                    }
                }
            }
            $price_expected = 0;
            $price_suppliers = 0;

            $data['discount_percent_expected'] = 0;
            $data['discount_percent_suppliers'] = 0;
            $sub_expected = 0;

            $price_expected = $total_expected_all - $sub_expected;


            $sub_suppliers = $data['discount_percent_suppliers'];

            $price_suppliers = $total_suppliers_all - $sub_suppliers + $delivery_cost - $reduce_cost;
            $_items = array(
                'valtype_check_expected' => 0,
                'valtype_check_suppliers' => 0,
                'discount_percent_expected' => $data['discount_percent_expected'],
                'discount_percent_suppliers' => $data['discount_percent_suppliers'],
                'totalAll_expected' => $total_expected_all,
                'totalAll_suppliers' => $price_suppliers,
                'price_expected' => $price_expected,
                'price_suppliers' => $price_suppliers,
                'total_novat' => $total_novat,
                'promotion_expected' => $promotion_expecteds,
            );
            $this->db->update('tblpurchase_order', $_items, array('id' => $id));
        }
        if ($count > 0) {
            return $id;
        }
        return false;
    }

    public function add_import($data = array())
    {
        $import = array(
            'code' => $data['code'],
            'prefix' => get_option('prefix_import'),
            'note' => $data['note'],
            'suppliers_id' => $data['suppliers_id'],
            'warehouse_id' => $data['warehouse_id'],
            'date' => $data['date'],
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y:m:d H:i:s'),
            'status' => 2,
        );

        if (empty($data['id_order'])) {
            unset($data['id_order']);
        } else {
            $id_order = get_table_where('tblpurchase_order', array('id' => $data['id_order']), '', 'row');
            $import['type_plan'] = $id_order->type_plan;
            $import['id_order'] = $data['id_order'];
        }
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
                if (!empty($item['id'])) {
                    if (empty($item['promotion_suppliers_1'])) {
                        $item['promotion_suppliers_1'] = 0;
                    }
                    if (empty($item['promotion_suppliers'])) {
                        $item['promotion_suppliers'] = 0;
                    }
                    $itemss = array(
                        'id_import' => $id,
                        'product_id' => $item['id'],
                        'quantity' => str_replace(',', '', $item['quantity']),
                        'quantity_net' => str_replace(',', '', $item['quantity_net']),
                        'tax_id' => str_replace(',', '', $item['tax_id']),
                        'tax_rate' => $item['tax_rate'],
                        'localtion_warehouses_id' => $item['localtion_warehouses_id'],
                        'type' => $item['type'],
                        'note' => $item['note'],
                        'price' => str_replace(',', '', $item['price']),
                        'promotion_suppliers_1' => str_replace(',', '', $item['promotion_suppliers_1']),
                        'promotion_suppliers' => str_replace(',', '', $item['promotion_suppliers']),

                    );
                    $total += $itemss['price'] * $itemss['quantity_net'] - $itemss['promotion_suppliers_1'] * $itemss['quantity_net'];
                    $amount = ($itemss['price'] * $itemss['quantity_net'] - $itemss['promotion_suppliers_1'] * $itemss['quantity_net']) * ($itemss['tax_rate'] / 100) + ($itemss['price'] * $itemss['quantity_net'] - $itemss['promotion_suppliers_1'] * $itemss['quantity_net']);
                    $itemss['amount'] = $amount;
                    $total_amount += $amount;
                    if ($this->db->insert('tblimport_items', $itemss)) {
                        $count++;
                        log_activity('Imports items insert [ID Import: ' . $id . ', ID Product: ' . $itemss['product_id'] . ']');
                    } else {
                        exit("error");
                    }
                }
            }
        }
        if ($count > 0) {
            $this->db->update('tblimport', array('total' => $total_amount, 'total_novat' => $total), array('id' => $id));
            return $id;
        }
    }

    public function count_all()
    {
        if (has_permission('import', '', 'view_own') && !is_admin()) {
            $count = get_table_where_select('count(*) as alls', 'tblimport', array('staff_create' => get_staff_user_id()), '', 'row');
            $ch_confirm_22 = get_table_where_select('count(*) as ch_confirm_22', 'tblimport', array('status' => 1, 'staff_create' => get_staff_user_id()), '', 'row');
            $dont_approve = get_table_where_select('count(*) as dont_approve', 'tblimport', array('status' => 2, 'staff_create' => get_staff_user_id()), '', 'row');
            $ch_warehouse_d = get_table_where_select('count(*) as ch_warehouse_d', 'tblimport', array('warehouseman_id !=' => 0, 'staff_create' => get_staff_user_id()), '', 'row');
            $ch_warehouse_nd = get_table_where_select('count(*) as ch_warehouse_nd', 'tblimport', array('warehouseman_id' => 0, 'staff_create' => get_staff_user_id()), '', 'row');
        } else {
            $count = get_table_where_select('count(*) as alls', 'tblimport', array(), '', 'row');
            $ch_confirm_22 = get_table_where_select('count(*) as ch_confirm_22', 'tblimport', array('status' => 1), '', 'row');
            $dont_approve = get_table_where_select('count(*) as dont_approve', 'tblimport', array('status' => 2), '', 'row');
            $ch_warehouse_d = get_table_where_select('count(*) as ch_warehouse_d', 'tblimport', array('warehouseman_id !=' => 0), '', 'row');
            $ch_warehouse_nd = get_table_where_select('count(*) as ch_warehouse_nd', 'tblimport', array('warehouseman_id' => 0), '', 'row');
        }
        $data['all'] = $count->alls;
        $data['dont_approve'] = $dont_approve->dont_approve;
        $data['ch_confirm_22'] = $ch_confirm_22->ch_confirm_22;
        $data['ch_warehouse_d'] = $ch_warehouse_d->ch_warehouse_d;
        $data['ch_warehouse_nd'] = $ch_warehouse_nd->ch_warehouse_nd;

        echo json_encode($data);
    }

    public function SearchItems_ch($id = '', $types = '')
    {
        $data = [];
        $search = $this->input->get('term');
        $type = $this->input->get('type');
        if (empty($type)) {
            $type = $types;
        }
        $limit_one = 12;
        $limit_two = 12;
        $limit_three = 12;
        $limit_all = 50;
        if ($type == -1) {
            $this->db->select(
                '
                    id,
                    tblitems.name as text,
                    tblitems.code,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tblitems.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->order_by('name', 'DESC');
            $this->db->limit($limit_one);
            $items = $this->db->get('tblitems')->result_array();
            if (!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
            $count_items = count($items);
            $this->db->select(
                '
                id as id,
                tbl_products.name as text,
                tbl_products.code,
                tbl_products.price_sell as price,
                concat("product") as type,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            // $this->db->where('tbl_products.type_products', 'semi_products_outside');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Thành phẩm'),
                        'children' => $product
                    ];
            }

            $count_product = count($product);
            $this->db->select(
                '
                id as id,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.code,
                tbl_tools_supplies.price_import as price,
                concat("tools") as type,
                CONCAT("uploads/tools_supplies/", "", tbl_tools_supplies.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_tools_supplies.name', $search);
                $this->db->or_like('tbl_tools_supplies.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_tools_supplies.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
            $tools = $this->db->get('tbl_tools_supplies')->result_array();
            if (!empty($tools)) {
                $data['results'][] =
                    [
                        'text' => _l('Công cụ - Vật tư'),
                        'children' => $tools
                    ];
            }
            $count_tools = count($tools);
            $this->db->select(
                '
                id as id,
                tbl_materials.name as text,
                tbl_materials.code,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            }
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(($limit_all - $count_tools - $count_product - $count_items));
            $product = $this->db->get('tbl_materials')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        } else
			if ($type == 'items') {
            $this->db->select(
                '
                    id as id,
                    tblitems.name as text,
                    tblitems.code,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->where('tblitems.id', $id);
                }
            }
            $this->db->order_by('name', 'DESC');
            $this->db->limit(50);
            $items = $this->db->get('tblitems')->result_array();
            if (!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
        } else
				if ($type == 'product') {
            $this->db->select(
                '
                id as id,
                tbl_products.name as text,
                tbl_products.code,
                tbl_products.price_sell as price,
                concat("product") as type,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_products.id', $id);
                    $this->db->group_end();
                }
            }
            // $this->db->where('tbl_products.type_products', 'semi_products_outside');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit(50);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Bán thành phẩm'),
                        'children' => $product
                    ];
            }
        } elseif ($type == 'nvl') {
            $this->db->select(
                '
                id as id,
                tbl_materials.name as text,
                tbl_materials.code,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_materials.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(50);
            $product = $this->db->get('tbl_materials')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        } elseif ($type == 'tools') {
            $this->db->select(
                '
                id as id,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.code,
                tbl_tools_supplies.price_import as price,
                concat("tools") as type,
                CONCAT("uploads/tools_supplies/", "", tbl_tools_supplies.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_tools_supplies.name', $search);
                $this->db->or_like('tbl_tools_supplies.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_tools_supplies.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->order_by('tbl_tools_supplies.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
            $tools = $this->db->get('tbl_tools_supplies')->result_array();
            if (!empty($tools)) {
                $data['results'][] =
                    [
                        'text' => _l('Công cụ - Vật tư'),
                        'children' => $tools
                    ];
            }
        }
        echo json_encode($data);
        die();
    }

    public function index()
    {
        if (!has_permission('import', '', 'view') && !has_permission('import', '', 'view_own')) {
            access_denied('import');
        }
        // $data['suppliers'] = get_table_where('tblsuppliers');
        $this->db->select('tblsuppliers.*');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tblimport.suppliers_id');
        $this->db->group_by('tblsuppliers.id');
        $data['suppliers'] = $this->db->get('tblimport')->result_array();

        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $data['dataStaff'] = $this->db->get()->result_array();

        $this->db->select('tblsuppliers.id, tblsuppliers.company, CONCAT(prefix,"-",code) as code');
        $this->db->from('tblsuppliers');
        $data['dataSupplier'] = $this->db->get()->result_array();

        $data['title'] = _l('ch_imports');
        $data['id_modal'] = $this->session->flashdata('import_id_modal');
        if (is_mobile()) {
            $this->load->view('admin/themes_mobile/manage_import', $data);
        } else {
            $this->load->view('admin/import/manage', $data);
        }
    }

    public function table()
    {
        $this->app->get_table_data('table_import');
    }

    public function get_invoice($id = '')
    {
        $purchase_invoice = get_table_where('tblpurchase_invoice', array('id_import' => $id), '', 'row');
        $purchase_invoice->date_invoice = _d($purchase_invoice->date_invoice);

        echo json_encode($purchase_invoice);
        die;
    }

    public function detail($id = '')
    {
        if (!has_permission('import', '', 'create')) {
            ajax_access_denied();
        }
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('import', '', 'create')) {
                    access_denied('import');
                }

                $data = $this->input->post();

                if (isset($data['items']) && count($data['items']) > 0) {
                    $id = $this->import_model->add($data);
                }

                if ($id) {
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('import'));
                }
            } else {
                if (!has_permission('import', '', 'edit')) {
                    access_denied('import');
                }
                $success = $this->import_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('import/detail/' . $id));
            }
        }
        if ($id != '') {
            if (!has_permission('import', '', 'edit')) {
                access_denied('import');
            }
            $data['title'] = _l('ch_edit_imports');
            $data['items'] = $this->import_model->get($id);
        } else {
            if (!has_permission('import', '', 'create')) {
                ajax_access_denied();
            }
            $data['title'] = _l('ch_add_imports');
        }
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['tax'] = get_table_where('tbltaxes');
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['warehouse'] = get_table_where('tblwarehouse');
        $data['localtion_warehouses'] = array();

        $this->load->view('admin/import/detail', $data);
    }

    public function test_quantity()
    {
        $type_of_document = $this->input->post('type_of_document');
        $id = $this->input->post('id');
        $id_import = $this->input->post('id_import');
        $test_quantity = 0;
        if ($type_of_document == 1) {
            $product = explode(',', trim($this->input->post('product_id'), ','));
            foreach ($product as $key => $v) {
                $product_id = explode('|', $v);
                $data['items'][$key]['quantity'] = $this->purchase_order_model->sum_quantity_import($product_id[0], $id, $product_id[1], $product_id[3], $product_id[4]);
                if ($data['items'][$key]['quantity'] != NULL) {
                    $data['items'][$key]['type'] = $product_id[0];
                    $data['items'][$key]['id_product'] = $product_id[1];
                    $data['items'][$key]['plan_id'] = $product_id[3];
                    $quantity = get_table_where('tblpurchase_order_items', array('id' => $product_id[4]), '', 'row')->quantity_suppliers;
                    $quantityss = ($quantity - $data['items'][$key]['quantity']);
                    $quantity_old = 0;
                    if (!empty($id_import)) {
                        $check_qty = get_table_where('tblimport_items', array('id_import' => $id_import, 'product_id' => $product_id[1], 'type' => $product_id[0], 'plan_id' => $product_id[3], 'id_purchase_order_items' => $product_id[4]), '', 'row');
                        if (!empty($check_qty)) {
                            $quantity_old = $check_qty->quantity_net;
                        }
                    }
                    if ($product_id[2] > ($quantity + $quantity_old - $data['items'][$key]['quantity'])) {
                        $test_quantity++;
                    }
                    $data['items'][$key]['quantity'] = $quantityss;
                } else {
                    $data['items'][$key]['type'] = $product_id[0];
                    $data['items'][$key]['id_product'] = $product_id[1];
                    $data['items'][$key]['plan_id'] = $product_id[3];
                    $quantity = get_table_where('tblpurchase_order_items', array('id' => $product_id[4]), '', 'row')->quantity_suppliers;
                    $quantityss = ($quantity);
                    $quantity_old = 0;
                    if (!empty($id_import)) {
                        $check_qty = get_table_where('tblimport_items', array('id_import' => $id_import, 'product_id' => $product_id[1], 'type' => $product_id[0], 'plan_id' => $product_id[3], 'id_purchase_order_items' => $product_id[4]), '', 'row');
                        if (!empty($check_qty)) {
                            $quantity_old = $check_qty->quantity_net;
                        }
                    }
                    if ($product_id[2] > ($quantity + $quantity_old)) {
                        $test_quantity++;
                    }
                    $data['items'][$key]['quantity'] = $quantityss;
                }
            }
        }
        $data['test_quantity'] = $test_quantity;
        echo json_encode($data);
        die;
    }

    public function create_detail($id = '')
    {
        if (!has_permission('import', '', 'create')) {
            ajax_access_denied();
        }
        if ($this->input->post()) {
            if (!has_permission('import', '', 'create')) {
                access_denied('import');
            }

            $data = $this->input->post();
            if (isset($data['items']) && count($data['items']) > 0) {
                $idd = $this->import_model->add($data);
            }

            if ($idd) {
                $this->session->set_flashdata('import_id_modal', $idd);
                dataPurchases($id);
                UpdateLotCode($data['suppliers_id'], to_sql_date($data['date'], true));
                create_order_to_import($idd);
                $count_items_import = get_items_import($id);
                if ($count_items_import == 0) {
                    $cancels = '1foso,' . date('Y-m-d H:i:s');
                    $cancel = array(
                        'cancel' => $cancels
                    );
                    $this->db->where('id', $id);
                    $this->db->update('tblpurchase_order', $cancel);
                }

                $get_code = get_table_where('tblimport', array('id' => $idd), '', 'row');
                activity_log_v2('purchase', 'tblimport', $idd, $get_code->prefix . '-' . $get_code->code, 'Thêm mới phiếu nhập hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
                set_alert('success', _l('ch_added_successfuly'));
                redirect(admin_url('import'));
            }
        }
        $data['id_import'] = 0;
        $data['title'] = _l('ch_add_imports');
        $data['purchase_order'] = $this->purchase_order_model->get_purchase_order($id);
        $purchase_order = $this->purchase_order_model->get_create_purchase_order_import($id);
        $data['items_purchase_order'] = $this->purchase_order_model->get_items_purchase_order_import_v2($id);
        $html = '<option></option>';

        foreach ($purchase_order->items as $key => $value) {
            if ($key == 0) {
                $html .= '<optgroup data-text="' . $value['name'] . '" label="' . $value['name'] . '">';
            } else if ($value['id'] == 'h') {
                $html .= '</optgroup>';
                $html .= '<optgroup data-text="' . $value['name'] . '" label="' . $value['name'] . '">';
            } else {
                $value['name'] = str_replace('"', '', $value['name']);
                $value['code_item'] = str_replace('"', '', $value['code_item']);
                $value['name'] = str_replace("'", '', $value['name']);
                $value['code_item'] = str_replace("'", '', $value['code_item']);
                if ($value['type_items'] == 'nvl' || $value['type_items'] == 'product') {
                    $html .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-qc="' . $value['mode'] . '" data-idd=' . $value['idd'] . ' data-id=' . $value['type_items'] . ' data-text="[' . $value['code_item'] . '] ' . $value['name'] . '" data-plan="' . $value['plan_id'] . '" value="' . $value['id'] . '__' . $value['idd'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
                } else {
                    $html .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-idd=' . $value['idd'] . ' data-id=' . $value['type_items'] . '  data-text="[' . $value['code_item'] . '] ' . $value['name'] . '" data-plan=' . $value['plan_id'] . ' value="' . $value['id'] . '__' . $value['idd'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
                }
            }
        }
        $html .= '</optgroup>';
        $data['html'] = $html;

        $html1 = '';

        foreach ($purchase_order->items as $key => $value) {
            if ($key == 0) {
                $html1 .= '<optgroup label="' . $value['name'] . '">';
            } else if ($value['id'] == 'h') {
                $html1 .= '</optgroup>';
                $html1 .= '<optgroup label="' . $value['name'] . '">';
            } else {
                $get_items = get_items($value['id'], $value['type_items']);
                $value['name'] = str_replace('"', '', $value['name']);
                $value['code_item'] = str_replace('"', '', $value['code_item']);
                $value['name'] = str_replace("'", '', $value['name']);
                $value['code_item'] = str_replace("'", '', $value['code_item']);
                $text = '<span><b>[' . $value['code_item'] . '] ' . $value['name'] . '</b></span>';
                if ($value['type_items'] == 'nvl') {
                    $text .= "<span class='text-muted'><br><span class='label label-primary'>Nguyên vật liệu</span></span>";
                    $text .= "<span class='text-muted'><br>" . _l('ch_items_specification') . ":" . $get_items->mode . "<br></span>";
                } elseif ($value['type_items'] == 'product') {
                    $text .= "<span class='text-muted'><br><span class='label label-warning'>Bán thành phẩm</span></span>";
                    $text .= "<span class='text-muted'><br>" . _l('ch_items_specification') . ":" . $get_items->mode . "<br></span>";
                }
                if ($value['code_plan'] != null) {
                    $text .= "<span class='text-muted'><br>KHSX:" . $value['code_plan'] . "<br></span>";
                }
                $html1 .= '<option data-content="' . $text . '" data-idd=' . $value['idd'] . ' quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' data-plan=' . $value['plan_id'] . ' value="' . $value['id'] . '__' . $value['idd'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
            }
        }
        $html1 .= '</optgroup>';
        $data['html1'] = $html1;

        $data['id'] = $id;
        $data['type_of_document'] = 1;
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['tax'] = get_table_where('tbltaxes');
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
        $data['type_plan'] = false;
        $data['order_id'] = false;
        if ($order->type_plan == 1) {
            $data['type_plan'] = true;
        }
        $id_purchases = '';
        if (!empty($order->id_purchase_proce)) {
            $id_purchases = $order->id_purchase_proce;
        } elseif (!empty($order->id_purchases)) {
            $id_purchases = $order->id_purchases;
        }
        if (!empty($id_purchases)) {
            $purchases = get_table_where('tblpurchases', array('id' => $id_purchases), '', 'row');
            if ($purchases->order_id) {
                $data['order_id'] = true;
            }
        }
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['warehouse'] = get_table_where('tblwarehouse');
        $data['localtion_warehouses'] = array();

        $this->load->view('admin/import/create_detail', $data);
    }

    public function detail_order($id = '')
    {
        if (!has_permission('import', '', 'create')) {
            ajax_access_denied();
        }
        if ($this->input->post()) {
            if (!has_permission('import', '', 'create')) {
                access_denied('import');
            }

            $data = $this->input->post();

            $success = $this->import_model->update($this->input->post(), $id);
            if ($success == true) {
                $import = get_table_where('tblimport', array('id' => $id), '', 'row');

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
                $get_code = get_table_where('tblimport', array('id' => $id), '', 'row');
                activity_log_v2('purchase', 'tblimport', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật phiếu nhập hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
                set_alert('success', _l('ch_updated_successfuly'));
            }
            redirect(admin_url('import/detail_order/' . $id));
        }
        if ($id != '') {
            if (!has_permission('import', '', 'create')) {
                access_denied('import');
            }
            $data['title'] = _l('ch_edit_imports');
            $data['items'] = $this->import_model->get($id);
            $data['purchase_order'] = $this->purchase_order_model->get($data['items']->id_order);
        }
        $data['id'] = $data['items']->id_order;
        $data['id_import'] = $id;
        $data['type_of_document'] = 1;
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['tax'] = get_table_where('tbltaxes');
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['warehouse'] = get_table_where('tblwarehouse');
        $data['localtion_warehouses'] = array();
        $data['type_plan'] = false;
        if ($data['items']->type_plan == 1) {
            $data['type_plan'] = true;
        }
        $purchase_order = $this->purchase_order_model->get_create_purchase_order_import($data['items']->id_order, $id);
        // $html = '<option></option>';
        // foreach ($purchase_order->items as $key => $value) {
        //     if ($key == 0) {
        //         $html .= '<optgroup label="' . $value['name'] . '">';
        //     } else if ($value['id'] == 'h') {
        //         $html .= '</optgroup>';
        //         $html .= '<optgroup label="' . $value['name'] . '">';
        //     } else {
        //         $value['name'] = str_replace('"', '', $value['name']);
        //         $value['code_item'] = str_replace('"', '', $value['code_item']);
        //         $value['name'] = str_replace("'", '', $value['name']);
        //         $value['code_item'] = str_replace("'", '', $value['code_item']);
        //         $html .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' value="' . $value['id'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
        //     }
        // }
        // $html .= '</optgroup>';
        // $data['html'] = $html;

        // $html1 = '';
        // foreach ($purchase_order->items as $key => $value) {
        //     if ($key == 0) {
        //         $html1 .= '<optgroup label="' . $value['name'] . '">';
        //     } else if ($value['id'] == 'h') {
        //         $html1 .= '</optgroup>';
        //         $html1 .= '<optgroup label="' . $value['name'] . '">';
        //     } else {
        //         $html1 .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' value="' . $value['id'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
        //     }
        // }
        // $html1 .= '</optgroup>';
        // $data['html1'] = $html1;
        $html = '<option></option>';
        foreach ($purchase_order->items as $key => $value) {
            if ($key == 0) {
                $html .= '<optgroup data-text="' . $value['name'] . '" label="' . $value['name'] . '">';
            } else if ($value['id'] == 'h') {
                $html .= '</optgroup>';
                $html .= '<optgroup data-text="' . $value['name'] . '" label="' . $value['name'] . '">';
            } else {
                $value['name'] = str_replace('"', '', $value['name']);
                $value['code_item'] = str_replace('"', '', $value['code_item']);
                $value['name'] = str_replace("'", '', $value['name']);
                $value['code_item'] = str_replace("'", '', $value['code_item']);
                if ($value['type_items'] == 'nvl' || $value['type_items'] == 'product') {
                    $html .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-qc="' . $value['mode'] . '" data-idd=' . $value['idd'] . ' data-id=' . $value['type_items'] . ' data-text="[' . $value['code_item'] . '] ' . $value['name'] . '" data-plan="' . $value['plan_id'] . '" value="' . $value['id'] . '__' . $value['idd'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
                } else {
                    $html .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-idd=' . $value['idd'] . ' data-id=' . $value['type_items'] . '  data-text="[' . $value['code_item'] . '] ' . $value['name'] . '" data-plan=' . $value['plan_id'] . ' value="' . $value['id'] . '__' . $value['idd'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
                }
            }
        }
        $html .= '</optgroup>';
        $data['html'] = $html;

        $html1 = '';
        foreach ($purchase_order->items as $key => $value) {
            if ($key == 0) {
                $html1 .= '<optgroup label="' . $value['name'] . '">';
            } else if ($value['id'] == 'h') {
                $html1 .= '</optgroup>';
                $html1 .= '<optgroup label="' . $value['name'] . '">';
            } else {
                $get_items = get_items($value['id'], $value['type_items']);
                $value['name'] = str_replace('"', '', $value['name']);
                $value['code_item'] = str_replace('"', '', $value['code_item']);
                $value['name'] = str_replace("'", '', $value['name']);
                $value['code_item'] = str_replace("'", '', $value['code_item']);
                $text = '<span><b>[' . $value['code_item'] . '] ' . $value['name'] . '</b></span>';
                if ($value['type_items'] == 'nvl') {
                    $text .= "<span class='text-muted'><br><span class='label label-primary'>Nguyên vật liệu</span></span>";
                    $text .= "<span class='text-muted'><br>" . _l('ch_items_specification') . ":" . $get_items->mode . "<br></span>";
                } elseif ($value['type_items'] == 'product') {
                    $text .= "<span class='text-muted'><br><span class='label label-warning'>Bán thành phẩm</span></span>";
                    $text .= "<span class='text-muted'><br>" . _l('ch_items_specification') . ":" . $get_items->mode . "<br></span>";
                }
                if ($value['code_plan'] != null) {
                    $text .= "<span class='text-muted'><br>KHSX:" . $value['code_plan'] . "<br></span>";
                }
                $html1 .= '<option data-content="' . $text . '" data-idd=' . $value['idd'] . ' quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' data-plan=' . $value['plan_id'] . ' value="' . $value['id'] . '__' . $value['idd'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
            }
        }
        $html1 .= '</optgroup>';
        $data['html1'] = $html1;
        $this->load->view('admin/import/create_detail', $data);
    }

    public function get_items($id = '', $type = '')
    {
        $data = $this->invoice_items_model->get_full_item($id, $type);
        $data->html = format_item_color($id, $type);
        $data->avatar = (!empty($data->avatar) ? (file_exists($data->avatar) ? base_url($data->avatar) : (file_exists('uploads/materials/' . $data->avatar) ? base_url('uploads/materials/' . $data->avatar) : (file_exists('uploads/products/' . $data->avatar) ? base_url('uploads/products/' . $data->avatar) : (file_exists('uploads/tools_supplies/' . $data->avatar) ? base_url('uploads/tools_supplies/' . $data->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));

        echo json_encode($data);
    }

    public function get_items_order($id = '', $type = '', $id_order = '', $plan_id = '', $idd = '')
    {
        $data = $this->purchase_order_model->get_items_import_order($id, $type, $id_order, $plan_id, $idd);
        $suppliers = get_table_where('tblsuppliers', array('id' => $data->suppliers_id), '', 'row');
        $code = sprintf('%03d', GetLotCode($data->suppliers_id));
        $data->lot_code = date('d') . date('m') . date('y') . $suppliers->code . $code;
        if ($data->avatar) {
            $data->avatar = (!empty($data->avatar) ? (file_exists($data->avatar) ? base_url($data->avatar) : (file_exists('uploads/materials/' . $data->avatar) ? base_url('uploads/materials/' . $data->avatar) : (file_exists('uploads/products/' . $data->avatar) ? base_url('uploads/products/' . $data->avatar) : (file_exists('uploads/tools_supplies/' . $data->avatar) ? base_url('uploads/tools_supplies/' . $data->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
        } else {
            $data->avatar = base_url('assets/images/preview-not-available.jpg');
        }

        echo json_encode($data);
    }

    public function getLocaltion_warehouses($warehouse_id = '')
    {
        echo json_encode(get_table_where('tbllocaltion_warehouses', array('warehouse' => $warehouse_id)));
    }

    public function views_import($id = '')
    {
        $data['items'] = $this->import_model->get($id);
        $data['warehouse_name'] = get_table_where('tblwarehouse', array('id' => $data['items']->warehouse_id), '', 'row');
        $data['dataLog'] = get_table_where('tblactivity_log_v2', array('table_obj' => 'tblimport', 'id_obj' => $id), 'id DESC');

        $this->db->where('id_import', $id);
        $this->db->order_by('date_create', 'desc');
        $data['feedback'] = $this->db->get('tblimport_feedback')->result();
        foreach ($data['feedback'] as $key => $value) {
            $this->db->where('rel_id', $value->id);
            $this->db->where('rel_type', 'feedback_i');
            $data['feedback'][$key]->file = $this->db->get('tblfiles')->result();
        }

        $this->load->view('admin/import/view_modal', $data);
    }

    public function delete($id)
    {
        if (!has_permission('import', '', 'delete')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }
        $dtData = get_table_where('tblimport', array('id' => $id), '', 'row_array');
        $response = $this->import_model->delete($id);
        $alert_type = 'warning';
        $message = _l('ch_no_delete');
        if ($response) {
            insertActivityLog([
                'type_parent_obj' => 'import',
                'table_obj' => 'tblimport',
                'id_obj' => $id,
                'name_obj' => $dtData['prefix'].'-'.$dtData['code'],
                'content' => lang('Xóa phiếu nhập') . ' [' . $dtData['prefix'].'-'.$dtData['code'] . ']',
                'actions' => 'delete'
            ]);
            $alert_type = 'success';
            $message = _l('ch_delete');
        }
        if ($response === 99) {
            $alert_type = 'warning';
            $message = _l('Đã có hóa đơn, Không thể xóa');
        }

        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function update_status($value = '')
    {
        if (!has_permission('import', '', 'approve_qc')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not')
            ));
            die;
        }
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $import = get_table_where('tblimport', array('id' => $id), '', 'row');
            $import = array();
            $import['status_qc'] = get_staff_user_id();
            $import['date_qc'] = date('Y-m-d H:i:s');
            $success = $this->db->update('tblimport', $import, array('id' => $id));
            // if ($import->status == 2) {
            //     die;
            // }
            // $staff_id = get_staff_user_id();
            // $date = date('Y-m-d H:i:s');
            // $history_status = $import->history_status;
            // $history_status .= '|' . $staff_id . ',' . $date;
            // $data = array(
            //     'history_status' => $history_status,
            //     'status_qc' => ($status + 1),
            // );
            // $success = $this->import_model->update_status($id, $data);
        }
        if ($success) {
            // approve_import_app($id);
            $get_code = get_table_where('tblimport', array('id' => $id), '', 'row');
            activity_log_v2('purchase', 'tblimport', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật trạng thái phiếu nhập hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'success',
                'message' => _l('ch_successful_approval')
            ));
        } else {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'danger',
                'message' => _l('ch_no_successful_approval')
            ));
        }
        die;
    }

    public function confirm_warehous()
    {
        if (!has_permission('import', '', 'approve_warehouse')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not')
            ));
            die;
        }
        $id = $this->input->post('id');
        $test_quantity = get_table_where('tblwarehouse_product', array('import_id' => $id, 'quantity_export >' => 0, 'type_export' => 1), '', 'row');
        $import = get_table_where('tblimport', array('id' => $id), '', 'row');
        $warehouseman_id = $this->input->post('warehouseman_id');
        if (!$id) {
            die('ch_no_items');
        }

        $data = array(
            'warehouseman_id' => get_staff_user_id(),
            'warehouseman_date' => date('Y-m-d H:i:s')
        );
        if ($warehouseman_id) {
            if (!empty($test_quantity)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_cance_confirm_export_warehouse')
                ));
                die;
            }
            if (empty($import->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_exsit_cancel_confirm_warehouse')
                ));
                die;
            }
            $data = array(
                'warehouseman_id' => NULL,
                'warehouseman_date' => NULL
            );
        } else {
            if (!empty($import->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_exsit_confirm_warehouse')
                ));
                die;
            }
        }
        $success = $this->db->update('tblimport', $data, array('id' => $id));
        $alert_type = 'warning';
        $message = _l('ch_no_successful_approval');
        if ($warehouseman_id) {
            $message = _l('ch_no_successful_approval_cance');
        }
        if ($success) {
            $get_code = get_table_where('tblimport', array('id' => $id), '', 'row');
            activity_log_v2('purchase', 'tblimport', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật trạng thái duyệt kho phiếu nhập hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
            $alert_type = 'success';
            $message = _l('ch_successful_approval');
            if ($warehouseman_id) {
                $message = _l('ch_successful_approval_cance');
            }
            if (empty($warehouseman_id)) {
                log_activity('Warehouse items approved [ID Import: ' . $id);
                $this->import_model->increaseWarehouse($id);
            } else {
                log_activity('Warehouse items cancel approved [ID Import: ' . $id);
                $import = get_table_where('tblimport', array('id' => $id), '', 'row');
                $this->import_model->decreaseWarehouse($id, $import->suppliers_id);
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        $data->title = lang('Phiếu nhập hàng');
        $dataField = get_table_where('tbl_field_pdf', array('parent_field' => 'import'), '', 'row');
        $dataMain = get_table_where('tblimport', array('id' => $id), '', 'row');
        $dataSub = get_table_where('tblimport_items', array('id_import' => $id));
        $supplier = get_table_where('tblsuppliers', array('id' => $dataMain->suppliers_id), '', 'row');
        $table = '';
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">____________________________________________________________________________________________________________________________________________</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">NHẬP HÀNG</span><br><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
        $data->content .= '
            <span style="font-weight: bold;">' . _l('ch_staff_p') . ': </span><span>' . get_staff_full_name($dataMain->staff_create) . '</span><br>
            <span style="font-weight: bold;">' . _l('supplier') . ': </span><span>' . $supplier->company . '</span><br>';
        $purchase_order = format_purchase_order_father_in($dataMain->id_order);
        if ($purchase_order) {
            $data->content .= '<span style="font-weight: bold;">' . _l('Mã YC') . ': </span><span>' . $purchase_order . '</span><br>';
        }
        if (!empty($dataMain->id_order)) {
            $purchase_orders = get_table_where('tblpurchase_order', array('id' =>$dataMain->id_order), '', 'row');
            $data->content .= '<span style="font-weight: bold;">' . _l('code_old_purchase') . ': </span><span>' . $purchase_orders->prefix .'-'. $purchase_orders->code . '</span><br>';
        }
        if (empty($dataMain->id_order)) {
            
            $purchase_order = format_purchase_order_father_all_in($dataMain->id_order);
        }
       
        $warehouse = get_table_where('tblwarehouse', array('id' => $dataMain->warehouse_id), '', 'row');
        $data->content .= '
        <span style="font-weight: bold;">' . _l('tblwarehouse') . ': </span><span>' . $warehouse->name . '</span><br>
        <span style="font-weight: bold;">' . _l('ch_note_t') . ': </span><span>' . $dataMain->note . '</span><br><br>
        ';

        $width1 = '';
        $width2 = '';
        $width3 = '';
        $width4 = '';
        $width5 = '';
        $width6 = '';
        $width7 = '';
        $width8 = '';
        $width9 = '';
        $width10 = '';
        $width11 = '';
        $dem_temp = 4;
        if (isset($dataField->arr_field)) {
            $arr = explode(',', $dataField->arr_field);
            foreach ($arr as $key => $value) {
                if ($value == 'item_warehouse_localtion_import') {
                    $item_warehouse_localtion_import = true;
                    $dem_temp++;
                }
                if ($value == 'item_unit_import') {
                    $item_unit_import = true;
                    $dem_temp++;
                }
                if ($value == 'item_quantity_import') {
                    $item_quantity_import = true;
                }
                if ($value == 'item_quantity_confirm_import') {
                    $item_quantity_confirm_import = true;
                }
                if ($value == 'item_price_import') {
                    $item_price_import = true;
                }
                if ($value == 'item_promotion_suppliers_import') {
                    $item_promotion_suppliers_import = true;
                }
                if ($value == 'item_tax_import') {
                    $item_tax_import = true;
                }
                if ($value == 'item_invoice_total_import') {
                    $item_invoice_total_import = true;
                }
                if ($value == 'item_note_import') {
                    $item_note_import = true;
                }
            }
            if (!has_permission('import', '', 'view_price')) {
                unset($item_price_import);
                unset($item_promotion_suppliers_import);
                unset($item_tax_import);
                unset($item_invoice_total_import);
            }
            // if(isset($item_warehouse_localtion_import) && isset($item_unit_import) && isset($item_quantity_import) && isset($item_quantity_confirm_import) && isset($item_price_import) && isset($item_promotion_suppliers_import) && isset($item_tax_import) && isset($item_invoice_total_import) && isset($item_note_import)) {
            //     $width1 = 'width: 5%;';
            //     $width2 = 'width: 16%;';
            //     $width3 = 'width: 13%;';
            //     $width4 = 'width: 7%;';
            //     $width5 = 'width: 7%;';
            //     $width6 = 'width: 7%;';
            //     $width7 = 'width: 9%;';
            //     $width8 = 'width: 9%;';
            //     $width9 = 'width: 5%;';
            //     $width10 = 'width: 13%;';
            //     $width11 = 'width: 9%;';
            // }
        }
        $width1 = 'width: 6%;';
        $width2 = 'width: 20%;';
        $width12 = 'width: 10%;';
        $width3 = 'width: 12%;';
        $width4 = 'width: 8%;';
        // $width5 = 'width: 7%;';
        $width6 = 'width: 10%;';
        $width7 = 'width: 12%;';
        // $width8 = 'width: 9%;';
        // $width9 = 'width: 5%;';
        $width10 = 'width: 12%;';
        $width11 = 'width: 13%;';

        $table = '
            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td style="' . $width1 . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
        $table .= '<td style="' . $width2 . 'text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';
        $table .= '<td style="' . $width12 . 'text-align: center;font-weight: bold;">' . _l('Lot') . '</td>';
        // if (isset($item_warehouse_localtion_import)) {
        $table .= '<td style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('warehouse_localtion') . '</td>';
        // }
        // if (isset($item_unit_import)) {
        $table .= '<td style="' . $width4 . 'text-align: center;font-weight: bold;">' . _l('tnh_dvt') . '</td>';
        // }
        // if (isset($item_quantity_import)) {
        // $table .= '<td style="' . $width5 . 'text-align: center;font-weight: bold;">' . _l('item_quantity_confirm') . '</td>';
        // }
        // if (isset($item_quantity_confirm_import)) {
        $table .= '<td style="' . $width6 . 'text-align: center;font-weight: bold;">' . _l('item_quantity') . '</td>';
        // }
        if (isset($item_price_import)) {
            $table .= '<td style="' . $width7 . 'text-align: center;font-weight: bold;">' . _l('tnh_price_import') . '</td>';
            // }
            // if (isset($item_promotion_suppliers_import)) {
            // $table .= '<td style="' . $width8 . 'text-align: center;font-weight: bold;">' . _l('promotion_suppliers') . '</td>';
            // }
            // if (isset($item_tax_import)) {
            // $table .= '<td style="' . $width9 . 'text-align: center;font-weight: bold;">' . _l('tax') . '</td>';
        }
        // if (isset($item_invoice_total_import)) {
        $table .= '<td style="' . $width10 . 'text-align: center;font-weight: bold;">' . _l('invoice_total') . '</td>';
        // }
        // if (isset($item_note_import)) {
        $table .= '<td style="' . $width11 . 'text-align: center;font-weight: bold;">' . _l('note') . '</td>';
        // }
        $table .= '</tr>
                </thead>
                <tbody>';
        $sum_quantity = 0;
        $sum_quantity_net = 0;
        $sum_price = 0;
        $sum_promotion_suppliers = 0;
        $sum_amount = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr nobr="true">';
            $dataItem = $this->invoice_items_model->get_full_item($value['product_id'], $value['type']);
            $dataLocaltion = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_warehouses_id']), '', 'row');

            $table .= '<td style="' . $width1 . 'text-align: center;">' . ++$key . '</td>';
            $table .= '<td style="' . $width2 . 'text-align: left;">' . $dataItem->name . '(' . $dataItem->code . ')' . GetQuycach($value['product_id'], $value['type']) . '</td>';
            $table .= '<td style="' . $width12 . 'text-align: center;">' . $value['lot_code'] . '</td>';

            // if (isset($item_warehouse_localtion_import)) {
            if (!empty($dataLocaltion)) {
                // $name_parent = str_replace("<i class='fa fa-caret-right text-danger' aria-hidden='true'>","a",$dataLocaltion->name_parent);
                $table .= '<td style="' . $width3 . 'text-align: center;">' . $dataLocaltion->name_parent . '</td>';
            } else {
                $table .= '<td></td>';
            }
            // }
            // if (isset($item_unit_import)) {
            $table .= '<td style="' . $width4 . 'text-align: center;">' . $dataItem->unit_name . '</td>';
            // }
            // if (isset($item_quantity_import)) {
            // $table .= '<td style="' . $width5 . 'text-align: center;">' . formatNumber($value['quantity']) . '</td>';
            // $sum_quantity += $value['quantity'];
            // }
            // if (isset($item_quantity_confirm_import)) {
            $table .= '<td style="' . $width6 . 'text-align: center;">' . formatNumber($value['quantity_net']) . '</td>';
            $sum_quantity_net += $value['quantity_net'];
            // }
            // if (isset($item_price_import)) {
            $table .= '<td style="' . $width7 . 'text-align: right;">' . number_format($value['price']) . '</td>';
            $sum_price += $value['price'];
            // }
            // if (isset($item_promotion_suppliers_import)) {
            // $table .= '<td style="' . $width8 . 'text-align: right;">' . number_format($value['promotion_suppliers']) . '</td>';
            // $sum_promotion_suppliers += $value['promotion_suppliers'];
            // }
            // if (isset($item_tax_import)) {
            // $table .= '<td style="' . $width9 . 'text-align: center;">' . number_format($value['tax_rate']) . ' %</td>';
            // }
            // if (isset($item_invoice_total_import)) {
            $table .= '<td style="' . $width10 . 'text-align: right;">' . number_format($value['amount']) . '</td>';
            $sum_amount += $value['amount'];
            // }
            // if (isset($item_note_import)) {
            $table .= '<td style="' . $width11 . 'text-align: center;">' . $value['note'] . '</td>';
            // }
            $table .= '</tr>';
        }
        $table .= '<tr>
                <td colspan="' . $dem_temp . '" style="text-align: center;font-weight: bold;">' . _l('invoice_dt_table_heading_amount') . '</td>';
        // if (isset($item_quantity_import)) {
        // $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity) . '</td>';
        // }
        // if (isset($item_quantity_confirm_import)) {
        $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity_net) . '</td>';
        // }
        // if (isset($item_price_import)) {
        $table .= '<td style="text-align: right;">' . number_format($sum_price) . '</td>';
        // }
        // if (isset($item_promotion_suppliers_import)) {
        // $table .= '<td style="text-align: right;">' . number_format($sum_promotion_suppliers) . '</td>';
        // }
        // if (isset($item_tax_import)) {
        // $table .= '<td></td>';
        // }
        // if (isset($item_invoice_total_import)) {
        $table .= '<td style="text-align: right;">' . number_format($sum_amount) . '</td>';
        // }
        // if (isset($item_note_import)) {
        $table .= '<td></td>';
        // }
        $table .= '</tr>';
        $table .= '</tbody>
            </table>';
        $data->content .= $table;


        $table = '<table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Đề Nghị</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Giao</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Nhận</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Thủ Kho</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $pdf = print_pdf($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }
    public function print_pdf_qc($id = '')
    {
        ob_start();
        $data = new stdClass();
        $dataField = get_table_where('tbl_field_pdf', array('parent_field' => 'import'), '', 'row');
        $dataMain = get_table_where('tblimport', array('id' => $id), '', 'row');
        $dataSub = get_table_where('tblimport_items', array('id_import' => $id));
        $supplier = get_table_where('tblsuppliers', array('id' => $dataMain->suppliers_id), '', 'row');
        $table = '';
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">____________________________________________________________________________________________________________________________________________</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">PHIẾU KIỂM TRA</span><br><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
        $data->content .= '
            <span style="font-weight: bold;">' . _l('ch_staff_p') . ': </span><span>' . get_staff_full_name($dataMain->staff_create) . '</span><br>
            <span style="font-weight: bold;">' . _l('supplier') . ': </span><span>' . $supplier->company . '</span><br>';
        $purchase_order = format_purchase_order_father_in($dataMain->id_order);
        if (empty($purchase_order)) {
            $purchase_order = format_purchase_order_father_all_in($dataMain->id_order);
        }
        if ($purchase_order) {
            $data->content .= '<span style="font-weight: bold;">' . _l('code_old_purchase') . ': </span><span>' . $purchase_order . '</span><br>';
        }
        $warehouse = get_table_where('tblwarehouse', array('id' => $dataMain->warehouse_id), '', 'row');
        $data->content .= '
        <span style="font-weight: bold;">' . _l('tblwarehouse') . ': </span><span>' . $warehouse->name . '</span><br>
        <span style="font-weight: bold;">' . _l('ch_note_t') . ': </span><span>' . $dataMain->note . '</span><br><br>
        ';

        $width1 = '';
        $width2 = '';
        $width3 = '';
        $width4 = '';
        $width5 = '';
        $width6 = '';
        $width7 = '';
        $width8 = '';
        $width9 = '';
        $width10 = '';
        $width11 = '';
        $dem_temp = 2;
        if (isset($dataField->arr_field)) {
            $arr = explode(',', $dataField->arr_field);
            foreach ($arr as $key => $value) {
                if ($value == 'item_warehouse_localtion_import') {
                    $item_warehouse_localtion_import = true;
                    $dem_temp++;
                }
                if ($value == 'item_unit_import') {
                    $item_unit_import = true;
                    $dem_temp++;
                }
                if ($value == 'item_quantity_import') {
                    $item_quantity_import = true;
                }
                if ($value == 'item_quantity_confirm_import') {
                    $item_quantity_confirm_import = true;
                }
                if ($value == 'item_price_import') {
                    $item_price_import = true;
                }
                if ($value == 'item_promotion_suppliers_import') {
                    $item_promotion_suppliers_import = true;
                }
                if ($value == 'item_tax_import') {
                    $item_tax_import = true;
                }
                if ($value == 'item_invoice_total_import') {
                    $item_invoice_total_import = true;
                }
                if ($value == 'item_note_import') {
                    $item_note_import = true;
                }
            }
        }
        $width1 = 'width: 6%;';
        $width2 = 'width: 30%;';
        $width12 = 'width: 15%;';
        $width3 = 'width: 30%;';
        $width4 = 'width: 9%;';
        $width6 = 'width: 10%;';

        $table = '
            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td style="' . $width1 . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
        $table .= '<td style="' . $width2 . 'text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';
        $table .= '<td style="' . $width12 . 'text-align: center;font-weight: bold;">' . _l('Lot') . '</td>';
        $table .= '<td style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('warehouse_localtion') . '</td>';
        $table .= '<td style="' . $width4 . 'text-align: center;font-weight: bold;">' . _l('tnh_dvt') . '</td>';
        $table .= '<td style="' . $width6 . 'text-align: center;font-weight: bold;">' . _l('item_quantity') . '</td>';
        $table .= '</tr>
                </thead>
                <tbody>';
        $sum_quantity = 0;
        $sum_quantity_net = 0;
        $sum_price = 0;
        $sum_promotion_suppliers = 0;
        $sum_amount = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr nobr="true">';
            $dataItem = $this->invoice_items_model->get_full_item($value['product_id'], $value['type']);
            $dataLocaltion = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_warehouses_id']), '', 'row');

            $table .= '<td style="' . $width1 . 'text-align: center;">' . ++$key . '</td>';
            $table .= '<td style="' . $width2 . 'text-align: left;">' . $dataItem->name . '(' . $dataItem->code . ')' . GetQuycach($value['product_id'], $value['type']) . '</td>';
            $table .= '<td style="' . $width12 . 'text-align: center;">' . $value['lot_code'] . '</td>';
            if (!empty($dataLocaltion)) {
                $table .= '<td style="' . $width3 . 'text-align: center;">' . $dataLocaltion->name_parent . '</td>';
            } else {
                $table .= '<td></td>';
            }
            $table .= '<td style="' . $width4 . 'text-align: center;">' . $dataItem->unit_name . '</td>';
            $table .= '<td style="' . $width6 . 'text-align: center;">' . formatNumber($value['quantity_net']) . '</td>';
            $sum_quantity_net += $value['quantity_net'];
            $table .= '</tr>';
        }
        $table .= '<tr>
                <td colspan="' . $dem_temp . '" style="text-align: center;font-weight: bold;">' . _l('invoice_dt_table_heading_amount') . '</td>';
        $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity_net) . '</td>';
        $table .= '<td style="text-align: right;">' . number_format($sum_price) . '</td>';
        $table .= '<td style="text-align: right;">' . number_format($sum_amount) . '</td>';
        $table .= '<td></td>';
        $table .= '</tr>';
        $table .= '</tbody>
            </table>';
        $data->content .= $table;


        $table = '<table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Đề Nghị</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Giao</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Nhận</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Thủ Kho</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $data->title = lang('Phiếu kiểm tra');
        $pdf = print_pdf($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }
    public function payment_all()
    {
        $data['costs'] = array();
        $this->costs_model->get_by_id(0, $data['costs']);
        $data['code'] = get_option('prefix_pay_slip') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
        $datas = $this->input->post();
        $data['total'] = 0;
        $data['id_old'] = trim($datas['ids'], ',');
        foreach (explode(',', trim($datas['ids'], ',')) as $key => $value) {
            $import = get_table_where('tblimport', array('id' => $value), '', 'row');
            $data['total'] += $import->total - $import->price_other_expenses - $import->amount_paid;
        }
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $this->load->view('admin/import/payment_all_modal', $data);
    }

    public function payment($id = '')
    {
        $data['costs'] = array();
        $this->costs_model->get_by_id(0, $data['costs']);
        $data['id_import'] = $id;
        $data['code'] = get_option('prefix_pay_slip') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
        $data['import'] = get_table_where('tblimport', array('id' => $id), '', 'row');
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $this->load->view('admin/import/payment_modal', $data);
    }

    public function pay_slip_all()
    {
        $success = false;
        $alert_type = 'warning';
        $message = _l('ch_pay_false');
        if ($this->input->post()) {
            $data = $this->input->post();
            $_data['day_vouchers'] = to_sql_date($data['day_vouchers']);
            $_data['date'] = date('Y-m-d H:i:s');
            $_data['id_costs'] = $data['id_costs'];
            $_data['staff_id'] = get_staff_user_id();
            $_data['receiver'] = $data['receiver'];
            $_data['payment_mode'] = $data['payment_mode'];
            $_data['payment'] = str_replace(',', '', $data['payment']);
            $_data['total'] = str_replace(',', '', $data['total']);
            $_data['note'] = $data['note'];
            $_data['id_supplierss'] = $data['id_supplierss'];
            $_data['type'] = 2;
            $_data['id_old'] = $data['id_old'];
            $_data['prefix'] = get_option('prefix_pay_slip');
            $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
            $this->db->insert('tblpay_slip', $_data);
            $id_pay = $this->db->insert_id();
            if ($id_pay) {
                $id_old = explode(',', $data['id_old']);
                foreach ($id_old as $key => $value) {
                    $__data['id_old'] = $value;
                    $__data['id_pay_slip'] = $id_pay;
                    $__data['type'] = 2;
                    $import = get_table_where('tblimport', array('id' => $value), '', 'row');
                    $__data['total'] = $import->total - $import->price_other_expenses;
                    $__data['payment'] = $import->total - $import->price_other_expenses;
                    $this->db->insert('tblpay_slip_detail', $__data);

                    $this->db->update('tblimport', array('amount_paid' => ($import->total - $import->price_other_expenses), 'money_arises' => ($import->total - $import->price_other_expenses - $import->amount_paid), 'status_pay' => 2), array('id' => $import->id));
                }
                $success = true;
                $alert_type = 'success';
                $message = _l('ch_pay_succes');
            }
        }
        echo json_encode(array(
            'success' => $success,
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }

    public function pay_slip($id = '')
    {
        $success = false;
        $alert_type = 'warning';
        $message = _l('ch_added_successfuly_not');
        if ($this->input->post()) {
            $data = $this->input->post();
            $_data['day_vouchers'] = to_sql_date($data['day_vouchers']);
            $_data['date'] = date('Y-m-d H:i:s');
            $_data['staff_id'] = get_staff_user_id();
            $_data['receiver'] = $data['receiver'];
            $_data['id_costs'] = $data['id_costs'];
            $_data['payment_mode'] = $data['payment_mode'];
            $_data['payment'] = str_replace(',', '', $data['payment']);
            $_data['total'] = str_replace(',', '', $data['total']);
            $_data['note'] = $data['note'];
            $imports = get_table_where('tblimport', array('id' => $id), '', 'row');
            $_data['id_supplierss'] = $imports->suppliers_id;
            $_data['type'] = 2;
            $_data['id_old'] = $id;
            $_data['prefix'] = get_option('prefix_pay_slip');
            $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
            $this->db->insert('tblpay_slip', $_data);
            $id_pay = $this->db->insert_id();
            if ($id_pay) {
                $__data['id_old'] = $id;
                $__data['id_pay_slip'] = $id_pay;
                $__data['type'] = 2;
                $__data['total'] = str_replace(',', '', $data['total']);
                $__data['payment'] = str_replace(',', '', $data['payment']);
                $this->db->insert('tblpay_slip_detail', $__data);
                $import = get_table_where('tblimport', array('id' => $id), '', 'row');
                $amount_paid = $import->amount_paid + $__data['payment'];
                if (($amount_paid + $import->price_other_expenses) == $import->total) {
                    $status = 2;
                } else {
                    $status = 1;
                }
                $this->db->update('tblimport', array('amount_paid' => $amount_paid, 'status_pay' => $status), array('id' => $import->id));
                $success = true;
                $alert_type = 'success';
                $message = _l('ch_added_successfuly');
            }
        }
        echo json_encode(array(
            'success' => $success,
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }

    public function print_pdf_code_v2($id = '', $id_items = '')
    {
        $items = get_table_where('tblimport_items', array('id' => $id_items), '', 'row');
        $main = get_table_where('tblimport', array('id' => $id), '', 'row');
        ob_end_clean();
        $data = [];
        $data['title'] = lang('In tem');
        // $data['type'] = 'P';
        $data['type'] = 'L';
        $data['img'] = '';
        if (empty($items->barcode)) {
            $codes = $items->product_id . 'F' . $items->type . 'F' . $items->quantity_net . 'F' . time() . 'F' . $main->suppliers_id;
            $this->db->update('tblimport_items', array('barcode' => $codes), array('id' => $items->id));
        } else {
            $codes = $items->barcode;
            $detail_code = explode('F', $codes);
            if ($detail_code[2] != $items->quantity_net) {
                $codes = $items->product_id . 'F' . $items->type . 'F' . $items->quantity_net . 'F' . time() . 'F' . $main->suppliers_id;
                $this->db->update('tblimport_items', array('barcode' => $codes), array('id' => $items->id));
            }
        }
        $detail_code = explode('F', $codes);
        if ($detail_code[1] == 1) {
            $detail_code[1] = 'items';
        } elseif ($detail_code[1] == 2) {
            $detail_code[1] = 'nvl';
        } elseif ($detail_code[1] == 3) {
            $detail_code[1] = 'product';
        } elseif ($detail_code[1] == 4) {
            $detail_code[1] = 'tools';
        }
        $suppliers = get_table_where('tblsuppliers', array('id' => $detail_code[4]), '', 'row');
        $items = get_items($detail_code[0], $detail_code[1]);
        // var_dump($items);die;
        $code = '<span style="font-size: 6px;white-space: unset;"><b>(' . $items->code . ') ' . $items->name . '</b></span>';
        $barcode = file_get_contents(genBarcode($codes, 'code128', 20, 0));
        $barcode = '<img width="180" height="30" src="data:image/png;base64,' . base64_encode($barcode) . '"/>';
        $series['data'] = $items;
        // $data->content.= '<div class="text-center">'.$barcode.'</div>';
        $series['code'] = $code;
        $series['barcode'] = $barcode;
        $series['quanliti'] = '<span style="font-size: 6px;float:left;"><b> Date: ' . _d(date('Y-m-d')) . '<br> Weight: ' . formatNumber($detail_code[2]) . 'kg</b></span>';
        ob_start();
        stylePdf();
        $content = ob_get_contents();
        ob_end_clean();
        $data['hide'] = 'hide';
        $data['content'] = $content;
        $data['series'] = $series;
        $pdf = print_pdf_dh_v2($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }

    public function print_pdf_code($codes)
    {
        ob_end_clean();
        $data = [];
        $data['title'] = lang('In tem');
        // $data['type'] = 'P';
        $data['type'] = 'L';
        $data['img'] = '';
        $detail_code = explode('F', $codes);
        if ($detail_code[1] == 1) {
            $detail_code[1] = 'items';
        } elseif ($detail_code[1] == 2) {
            $detail_code[1] = 'nvl';
        } elseif ($detail_code[1] == 3) {
            $detail_code[1] = 'product';
        } elseif ($detail_code[1] == 4) {
            $detail_code[1] = 'tools';
        }
        $suppliers = get_table_where('tblsuppliers', array('id' => $detail_code[4]), '', 'row');
        $items = get_items($detail_code[0], $detail_code[1]);
        // var_dump($items);die;
        $code = '<span style="font-size: 6px;white-space: unset;"><b>(' . $items->code . ') ' . $items->name . '</b></span>';
        $barcode = file_get_contents(genBarcode($codes, 'code128', 20, 0));
        $barcode = '<img width="180" height="30" src="data:image/png;base64,' . base64_encode($barcode) . '"/>';
        $series['data'] = $items;
        // $data->content.= '<div class="text-center">'.$barcode.'</div>';
        $series['code'] = $code;
        $series['barcode'] = $barcode;
        $series['quanliti'] = '<span style="font-size: 6px;float:left;"><b> Date: ' . _d(date('Y-m-d')) . '<br> Weight: ' . formatNumber($detail_code[2]) . 'kg</b></span>';
        ob_start();
        stylePdf();
        $content = ob_get_contents();
        ob_end_clean();
        $data['hide'] = 'hide';
        $data['content'] = $content;
        $data['series'] = $series;
        $pdf = print_pdf_dh_v2($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }
    public function get_print_tem()
    {
        // if (!$this->perPrintOrders) {
        //     accessDenied();
        //     die;
        // }
        ob_end_clean();
        $order_id = $this->input->post('order_id');
        $p_id = $this->input->post('p_id');
        $type_print = $this->input->post('type_print');
        $vt1 = $this->input->post('vt1');
        $vt2 = $this->input->post('vt2');
        $vt3 = $this->input->post('vt3');
        $vt4 = $this->input->post('vt4');

        $order = $this->orders_model->rowOrderById($order_id);
        $customer = $this->clients_model->rowCustomer($order['customer_id']);
        $tableTem = '';
        if (!empty($p_id)) {
            $vt1_ar = array();
            $vt1_id = array();
            if (!empty($vt1)) {
                $vt1_ar = explode('_____', $vt1);
                foreach ($vt1_ar as $ka => $va) {
                    $vas = explode('|_|', $va);
                    $vt1_id[$vas[0]] = $vas[1];
                }
            }
            $vt2_ar = array();
            $vt2_id = array();
            if (!empty($vt2)) {
                $vt2_ar = explode('_____', $vt2);
                foreach ($vt2_ar as $ka => $va) {
                    $vas = explode('|_|', $va);
                    $vt2_id[$vas[0]] = $vas[1];
                }
            }

            $vt3_ar = array();
            $vt3_id = array();
            if (!empty($vt3)) {
                $vt3_ar = explode('_____', $vt3);
                foreach ($vt3_ar as $ka => $va) {
                    $vas = explode('|_|', $va);
                    $vt3_id[$vas[0]] = $vas[1];
                }
            }
            $vt4_ar = array();
            $vt4_id = array();
            if (!empty($vt4)) {
                $vt4_ar = explode('_____', $vt4);
                foreach ($vt4_ar as $ka => $va) {
                    $vas = explode('|_|', $va);
                    $vt4_id[$vas[0]] = $vas[1];
                }
            }
            $p_id = explode(',', $p_id);
            $this->db->select('tbl_orders.*, tblclients.company as company,tblclients.company_short as company_short, tblclients.zcode as code_customer', false);
            $this->db->from('tbl_orders');
            $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
            $this->db->where('tbl_orders.id', $order_id);
            $order = $this->db->get()->row_array();

            $this->db->select('
                tbl_order_items.*
            ', false);
            $this->db->from('tbl_order_items');
            $this->db->where('tbl_order_items.order_id', $order_id);
            $this->db->where_in('tbl_order_items.id', $p_id);
            $order_items = $this->db->get()->result_array();
            if (!empty($order_items)) {
                foreach ($order_items as $key => $value) {
                    $order_item_id = $value['id'];
                    $type_item = $value['type_item'];
                    $items_id = $value['item_id'];

                    $product_name_customer =  '';
                    $mode = '';
                    $quantity_child_sheet = 0;
                    $lost = '';

                    $even_quantity = '';
                    $odd_quantity = '';

                    $even_quantity_bale = '';
                    $odd_quantity_bale = '';

                    $item_code = '';
                    $color_size = '';
                    $gw = '';
                    $carton_size = '';

                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($value['product_name_customer'])) {
                            $product_name_customer = $value['product_name_customer'];
                        } else if (!empty($info['product_name_customer'])) {
                            $product_name_customer = $info['product_name_customer'];
                        } else {
                            $product_name_customer = $info['name'] . ' ' . $info['code'];
                        }

                        $mode = $info['mode_product'];
                        $lost = $info['loss'];
                        $quantity_child_sheet = $info['quantity_child_sheet'];
                        $quantity_sheet_bale = $info['quantity_sheet_bale'];
                        if (empty($quantity_child_sheet)) {
                            $quantity_child_sheet = 1;
                        }
                        if (empty($quantity_sheet_bale)) {
                            $quantity_sheet_bale = 1;
                        }
                        if ($quantity_child_sheet > 0) {
                            $quantity_sheet = $value['quantity'] / $quantity_child_sheet;
                            $even_quantity = floor($quantity_sheet);
                            $quantity_ceil = ceil($quantity_sheet);
                            $odd_quantity = $quantity_ceil - $even_quantity;
                        }

                        $quantity_bale = 0;
                        $quantity_ceil_bale_chan = 0;
                        if ($quantity_sheet_bale > 0) {
                            $quantity_bale = $value['quantity'] / $quantity_sheet_bale;
                            $even_quantity_bale = floor($quantity_bale);
                            $quantity_ceil_bale = ceil($quantity_bale);
                            $quantity_ceil_bale_chan = floor($quantity_bale);
                            $odd_quantity_bale = $quantity_ceil_bale - $even_quantity_bale;
                        }

                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/' . $info['images']);
                        }

                        $item_code = $info['code'];
                        $color_size = $info['color_size'];
                        $gw = $info['gw'];
                        $carton_size = $info['carton_size'];
                    } else if ($type_item == "materials") {
                        $info = $this->items_model->rowMaterial($items_id);

                        if (!empty($value['product_name_customer'])) {
                            $product_name_customer = $value['product_name_customer'];
                        } else if (!empty($info['name_customer'])) {
                            $product_name_customer = $info['name_customer'];
                        } else {
                            $product_name_customer = $info['name'] . ' ' . $info['code'];
                        }

                        $mode = $info['mode'];

                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/materials/' . $info['images']);
                        }

                        $item_code = $info['code'];
                    }

                    if ($order['type_orders'] == ORDER_DEFAULT || $order['type_orders'] == ORDER_CHANGE) {
                        if ($type_print == 1) {
                            if (!empty($quantity_ceil_bale)) {
                                for ($i = 0; $i < $quantity_ceil_bale; $i++) {
                                    $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                        <tr nobr="true" style="">
                                            <td class="" style="width: 10%;">Mã KH</td>
                                            <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                            <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                            <td class="" style="width: 15%;">Qui Cách</td>
                                            <td class="text-center" style="width: 10%;">ĐVT</td>
                                            <td class="" style="width: 15%;">Ghi Chú</td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="" style="width: 10%;">Mã ĐĐH</td>
                                            <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                            <td class="text-center" rowspan="2" colspan="3" style="width: 35%; line-height: 45px;font-family: kozgopromedium;font-size:11px">' . $product_name_customer . '</td>
                                            <td class="" style="width: 15%;">' . $mode . '</td>
                                            <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                            <td class="" style="width: 15%;">' . $value['note_item'] . '</td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="" style="width: 10%;">Số lượng giao</td>
                                            <td class="text-right" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>
                                            <td class="" style="width: 15%;">SL Con/Tờ</td>
                                            <td class="text-center" style="width: 10%;">' . formatNumber($quantity_child_sheet) . '</td>
                                            <td class="" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;">Tờ Chẵn</td>
                                            <td class="text-center" style="width: 15%;">Tờ Lẻ</td>
                                            <td class="text-center" style="width: 15%;">Kiện Chẵn</td>
                                            <td class="text-center" style="width: 10%;">Kiện Lẻ</td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;">Lost</td>
                                            <td class="text-center" style="width: 10%;">' . $lost . '</td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;">' . $even_quantity . '</td>
                                            <td class="text-center" style="width: 15%;">' . $odd_quantity . '</td>
                                            <td class="text-center" style="width: 15%;">' . $quantity_sheet_bale . '</td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;">QC Kiểm</td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;">Ngày giao</td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                    </table><br><br><br>';
                                }
                            }


                            if (!empty($odd_quantity_bale)) {
                                $quantity_odd = $value['quantity'] - $quantity_sheet_bale * $even_quantity_bale;
                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                    <tr nobr="true" style="">
                                        <td class="" style="width: 10%;">Mã KH</td>
                                        <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                        <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                        <td class="" style="width: 15%;">Qui Cách</td>
                                        <td class="text-center" style="width: 10%;">ĐVT</td>
                                        <td class="" style="width: 15%;">Ghi Chú</td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="" style="width: 10%;">Mã ĐĐH</td>
                                        <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                        <td class="text-center" rowspan="2" colspan="3" style="width: 35%; line-height: 45px;font-family: kozgopromedium;font-size:11px">' . $product_name_customer . '</td>
                                        <td class="" style="width: 15%;">' . $mode . '</td>
                                        <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                        <td class="" style="width: 15%;">' . $value['note_item'] . '</td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="" style="width: 10%;">Số lượng giao</td>
                                        <td class="text-right" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>
                                        <td class="" style="width: 15%;">SL Con/Tờ</td>
                                        <td class="text-center" style="width: 10%;">' . formatNumber($quantity_child_sheet) . '</td>
                                        <td class="" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;">Tờ Chẵn</td>
                                        <td class="text-center" style="width: 15%;">Tờ Lẻ</td>
                                        <td class="text-center" style="width: 15%;">Kiện Chẵn</td>
                                        <td class="text-center" style="width: 10%;">Kiện Lẻ</td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;">Lost</td>
                                        <td class="text-center" style="width: 10%;">' . $lost . '</td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;">' . $even_quantity . '</td>
                                        <td class="text-center" style="width: 15%;">' . $odd_quantity . '</td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;">' . $quantity_odd . '</td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;">QC Kiểm</td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;">Ngày giao</td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                </table><br><br><br>';
                            }
                        } elseif ($type_print == 3) {
                            if ($quantity_child_sheet > 0) {
                                $order_items_column = get_table_where('tbl_order_items_columns', array('order_id' => $order_id, 'order_item_id' => $order_item_id));
                                if ($type_item == "products") {
                                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                                    $trHtmlChild = '';
                                    $thSub = '';
                                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                                    $ct_counter_item = $value['ct_counter_item'];
                                    $trHtmlChild = '';
                                    $trHtmlColumns = '';
                                    $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                                    $dateDelivery = '';
                                    if (!empty($dtDateDelivery)) {
                                        $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                                    }
                                    if ($ct_counter_item > 0) {
                                        $check_key = 0;
                                        for ($i = 0; $i < $ct_counter_item; $i++) {
                                            $trHtmlColumns = '';
                                            foreach ($productsColumns as $k => $v) {
                                                $columns_name = '';
                                                foreach ($orderItemsColumns as $kO => $vO) {
                                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                        $columns_name = $vO['columns_name'];
                                                        break;
                                                    }
                                                }

                                                $trHtmlColumns .= '
                                                <td class="text-center">
                                                    ' . $columns_name . '
                                                </td>
                                            ';
                                            }

                                            $order_code = '';
                                            $command = '';
                                            $quantity_put = '';
                                            $quantity_loss = '';
                                            foreach ($orderItemsColumns as $kO => $vO) {
                                                if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                                    $order_code = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                                    $command = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                                    $quantity_put = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                                    $quantity_loss = $vO['columns_name'];
                                                    continue;
                                                }
                                            }
                                            if (empty($trHtmlColumns) && empty($order_code)) continue;
                                            $quantity_colum = floor($quantity_put / $quantity_child_sheet);
                                            $quantity_odd = $quantity_put - $quantity_child_sheet * $quantity_colum;
                                            if ($check_key == 0) {
                                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="0"><tr nobr="true" style="">';
                                            }

                                            $tableTem .= '<td class="" style="width: 50%;"><table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                            <tr nobr="true" style="">
                                                <td class=""  colspan="2">K/H: ' . $order['company_short'] . '</td>
                                                <td colspan="4" >M/Đ: ' . $order_code . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="6" style="font-family: kozgopromedium;font-size:11px" >T/T: ' . $product_name_customer . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="3"  >CL: ' . $command . '</td>
                                                <td class=""  >SL: </td>
                                                <td class="text-right" >' . formatNumber($quantity_put) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="3"  >QC</td>
                                                <td class="" style="border-right: none;border-bottom: none;">Tờ: </td>
                                                <td class="text-right" style="border-left: none;border-right: none;border-bottom: none;">' . $quantity_colum . '</td>
                                                <td class="" style="border-left: none;border-bottom: none;"></td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="3">N/Giao: ' . $dateDelivery . '</td>
                                                <td class="" style="border-right: none;border-top: none;border-right: none;">Lẻ: </td>
                                                <td class="text-right" style="border-left: none;border-right: none;border-top: none;">' . formatNumber($quantity_odd) . '</td>
                                                <td class="" style="border-left: none;border-top: none;"></td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td class="text-center" colspan="3">' . $order['reference_no'] . '</td>
                                                <td class="" >SL Thêm: </td>
                                                <td class="text-right" >' . formatNumber($quantity_loss) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                            </tr>
                                        </table></td>';
                                            if ($check_key == 1) {
                                                $tableTem .= '</tr></table><br><br>';
                                                $check_key = 0;
                                            } else {
                                                if (($i + 1) == ($ct_counter_item)) {
                                                    $tableTem .= '</tr></table><br><br>';
                                                }
                                                $check_key++;
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($type_print == 4) {
                            if ($quantity_sheet_bale > 0) {
                                $order_items_column = get_table_where('tbl_order_items_columns', array('order_id' => $order_id, 'order_item_id' => $order_item_id));
                                if ($type_item == "products") {
                                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                                    $trHtmlChild = '';
                                    $thSub = '';
                                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                                    $ct_counter_item = $value['ct_counter_item'];
                                    $trHtmlChild = '';
                                    $trHtmlColumns = '';
                                    $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                                    $dateDelivery = '';
                                    if (!empty($dtDateDelivery)) {
                                        $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                                    }
                                    if ($ct_counter_item > 0) {
                                        for ($i = 0; $i < $ct_counter_item; $i++) {
                                            $trHtmlColumns = '';
                                            foreach ($productsColumns as $k => $v) {
                                                $columns_name = '';
                                                foreach ($orderItemsColumns as $kO => $vO) {
                                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                        $columns_name = $vO['columns_name'];
                                                        break;
                                                    }
                                                }

                                                $trHtmlColumns .= '
                                                <td class="text-center">
                                                    ' . $columns_name . '
                                                </td>
                                            ';
                                            }

                                            $order_code = '';
                                            $command = '';
                                            $quantity_put = '';
                                            $quantity_loss = '';
                                            foreach ($orderItemsColumns as $kO => $vO) {
                                                if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                                    $order_code = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                                    $command = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                                    $quantity_put = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                                    $quantity_loss = $vO['columns_name'];
                                                    continue;
                                                }
                                            }
                                            // $quantity_sheet_bale = 2;
                                            if (empty($trHtmlColumns) && empty($order_code)) continue;
                                            $quantity_colum = floor($quantity_put / $quantity_sheet_bale);
                                            $quantity_odd = $quantity_put - $quantity_sheet_bale * $quantity_colum;
                                            $loss = $quantity_odd + $quantity_put * ($lost / 100);
                                            $quantity_colum_show = $quantity_sheet_bale;
                                            $quantity_colum_loss_show = $quantity_sheet_bale;
                                            if ($quantity_sheet_bale == 1) {
                                                $quantity_colum = 1;
                                                $quantity_colum_show = $quantity_put;
                                                $quantity_colum_loss_show = $quantity_put + $quantity_loss;
                                            }
                                            $limits = $quantity_colum;
                                            $page = 0;
                                            if ($quantity_odd > 0) {
                                                $limits++;
                                            }
                                            for ($j = 0; $j < $quantity_colum; $j++) {
                                                $page++;
                                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                            <tr  nobr="true" style="">
                                                <td style="width: 18%;" class=""  colspan="2">' . $order['company_short'] . '</td>
                                                <td style="width: 8%;">T/T</td>
                                                <td style="width: 40%;font-family: kozgopromedium;font-size:11px" class="text-left;white-space: unset;" colspan="4">' . $product_name_customer . '</td>
                                                <td style="width: 9%;">PO#</td>
                                                <td style="width: 25%;" colspan="3">' . $order_code . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td style="width: 10%;" class=""  >SL: </td>
                                                <td style="width: 8%;" class="text-right" >' . formatNumber($quantity_colum_show) . '</td>
                                                <td style="width: 8%;" class="" >' . $unit['unit'] . '</td>
                                                <td style="width: 8%;" class="">1 kiện: </td>
                                                <td style="width: 10%;" class="">' . formatNumber($quantity_sheet_bale) . '</td>
                                                <td style="width: 10%;" class="">Số kiện: </td>
                                                <td style="width: 12%;" >' . $page . '/' . $limits . '</td>
                                                <td style="width: 9%;" class="" >CL: </td>
                                                <td style="width: 25%;" colspan="3" >' . $command . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td class=""  >SL+Loss: </td>
                                                <td class="text-right" >' . formatNumber($quantity_colum_loss_show) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                                <td class=""  colspan="2">' . $order['reference_no'] . '</td>
                                                <td class=""  >Lẻ</td>
                                                <td ></td>
                                                <td class="" style="width: 9%;" >N/GIAO:</td>
                                                <td style="width: 12%;">' . $dateDelivery . '</td>
                                                <td style="width: 5%;" class="" >QC: </td>
                                                <td style="width: 8%;"></td>
                                            </tr>
                                        </table><br><br><br>';
                                            }
                                            if ($quantity_odd > 0) {
                                                $page++;
                                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                            <tr  nobr="true" style="">
                                                <td style="width: 18%;" class=""  colspan="2">' . $order['company_short'] . '</td>
                                                <td style="width: 8%;">T/T</td>
                                                <td style="width: 40%;font-family: kozgopromedium;font-size:11px" class="text-left;white-space: unset;" colspan="4">' . $product_name_customer . '</td>
                                                <td style="width: 9%;">PO#</td>
                                                <td style="width: 25%;" colspan="3">' . $order_code . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td style="width: 10%;" class=""  >SL: </td>
                                                <td style="width: 8%;" class="text-right" >' . formatNumber($quantity_odd) . '</td>
                                                <td style="width: 8%;" class="" >' . $unit['unit'] . '</td>
                                                <td style="width: 8%;" class="">1 kiện: </td>
                                                <td style="width: 10%;" class="">' . formatNumber($quantity_sheet_bale) . '</td>
                                                <td style="width: 10%;" class="">Số kiện: </td>
                                                <td style="width: 12%;" >' . $page . '/' . $limits . '</td>
                                                <td style="width: 9%;" class="" >CL: </td>
                                                <td style="width: 25%;" colspan="3" >' . $command . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td class=""  >SL+Loss: </td>
                                                <td class="text-right" >' . number_format($loss) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                                <td class=""  colspan="2">' . $order['reference_no'] . '</td>
                                                <td class=""  >Lẻ</td>
                                                <td ></td>
                                                <td class="" style="width: 9%;" >N/GIAO:</td>
                                                <td style="width: 12%;">' . $dateDelivery . '</td>
                                                <td style="width: 5%;" class="" >QC: </td>
                                                <td style="width: 8%;"></td>
                                            </tr>
                                        </table><br><br><br>';
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($type_print == 5) {

                            if ($quantity_child_sheet > 0 && !empty($vt2_id)) {
                                $order_items_column = get_table_where('tbl_order_items_columns', array('order_id' => $order_id, 'order_item_id' => $order_item_id));
                                if ($type_item == "products") {
                                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                                    $trHtmlChild = '';
                                    $thSub = '';
                                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                                    $ct_counter_item = $value['ct_counter_item'];
                                    $trHtmlChild = '';
                                    $trHtmlColumns = '';
                                    $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                                    $dateDelivery = '';
                                    if (!empty($dtDateDelivery)) {
                                        $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                                    }
                                    $value_size = array();
                                    if ($ct_counter_item > 0) {
                                        for ($i = 0; $i < $ct_counter_item; $i++) {
                                            $trHtmlColumns = '';
                                            foreach ($productsColumns as $k => $v) {
                                                $columns_name = '';
                                                foreach ($orderItemsColumns as $kO => $vO) {
                                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                        $columns_name = $vO['columns_name'];
                                                        break;
                                                    }
                                                }

                                                $trHtmlColumns .= '
                                                <td class="text-center">
                                                    ' . $columns_name . '
                                                </td>
                                            ';
                                            }

                                            $order_code = '';
                                            $command = '';
                                            $quantity_put = '';
                                            $quantity_loss = '';
                                            $Size = '';
                                            $vt1_text_show = '';
                                            $vt3_text_show = '';
                                            $vt4_text_show = '';

                                            $text_size = '';
                                            $tv1_text = '';
                                            $tv3_text = '';
                                            $tv4_text = '';
                                            $text_size = '';
                                            if (!empty($vt1_id[$value['id']])) {
                                                $tv1_text = $vt1_id[$value['id']];
                                            }
                                            if (!empty($vt3_id[$value['id']])) {
                                                $tv3_text = $vt3_id[$value['id']];
                                            }
                                            if (!empty($vt4_id[$value['id']])) {
                                                $tv4_text = $vt4_id[$value['id']];
                                            }

                                            if (!empty($vt2_id[$value['id']])) {
                                                $text_size = $vt2_id[$value['id']];
                                            } else {
                                                continue;
                                            }
                                            foreach ($orderItemsColumns as $kO => $vO) {
                                                if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                                    $order_code = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                                    $command = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                                    $quantity_put = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                                    $quantity_loss = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == $text_size && $i == $vO['counter_items_number']) {
                                                    $Size = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == $tv1_text && $i == $vO['counter_items_number']) {
                                                    $vt1_text_show = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == $tv3_text && $i == $vO['counter_items_number']) {
                                                    $vt3_text_show = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == $tv4_text && $i == $vO['counter_items_number']) {
                                                    $vt4_text_show = $vO['columns_name'];
                                                    continue;
                                                }
                                            }
                                            if (empty($trHtmlColumns) && empty($order_code)) continue;
                                            $__data['order_code'] = $order_code;
                                            $__data['command'] = $command;
                                            $__data['quantity_put_sum'] = $quantity_put;
                                            $__data['quantity_put'] = $quantity_put + $quantity_loss;
                                            $__data['quantity_loss'] = $quantity_loss;
                                            $__data['Size'] = $Size;
                                            $__data['vt1_text_show'] = $vt1_text_show;
                                            $__data['vt3_text_show'] = $vt3_text_show;
                                            $__data['vt4_text_show'] = $vt4_text_show;
                                            $value_size[$order_code . $command][] = $__data;
                                        }
                                        foreach ($value_size as $h => $hv) {
                                            $total_quanliti = 0;
                                            foreach ($hv as $ks => $vs) {
                                                $total_quanliti += $vs['quantity_put_sum'];
                                            }
                                            // $quantity_colum = floor($quantity_put / $quantity_sheet_bale);
                                            // $quantity_odd = $quantity_put - $quantity_sheet_bale * $quantity_colum;
                                            // $loss = $quantity_odd + $quantity_put * ($lost / 100);
                                            $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                            <tr nobr="true" style="">
                                                <td style="width: 6%;">K/H:</td>
                                                <td style="width: 12%;" class=""  colspan="2">' . $order['company_short'] . '</td>
                                                <td style="width: 8%;">T/T</td>
                                                <td style="width: 36%;font-family: kozgopromedium;font-size:11px" colspan="6">' . $product_name_customer . '</td>
                                                <td style="width: 4%;">SL</td>
                                                <td style="width: 16%;" class="text-right" colspan="2">' . formatNumber($total_quanliti)  . '</td>
                                                <td style="width: 8%;">' . $unit['unit'] . '</td>
                                                <td style="width: 12%;" colspan="2">QC</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td style="width: 6%;">MĐ: </td>
                                                <td style="width: 12%;"> ' . $hv[0]['order_code'] . '</td>
                                                <td style="width: 8%;" class="">C/L</td>
                                                <td style="width: 12%;" colspan="2">' . $hv[0]['command'] . '</td>
                                                <td style="width: 12%;font-family: kozgopromedium;font-size:11px" colspan="2" class="">' . $hv[0]['vt3_text_show'] . '</td>
                                                <td style="width: 12%;font-family: kozgopromedium;font-size:11px" colspan="2" class="">' . $hv[0]['vt4_text_show'] . '</td>
                                                <td style="width: 20%;" >' . $order['reference_no'] . '</td>
                                                <td style="width: 8%;" class="" >N/GIAO </td>
                                                <td style="width: 12%;">' . $dateDelivery . '</td>
                                            </tr>';
                                            $tableTem .= '<tr nobr="true" style="">
                                                <td style="width: 6%;"></td>';
                                            $width_size = '4%';
                                            $width_size_main = '6%';
                                            foreach ($hv as $ks => $vs) {
                                                $tableTem .= '<td style="width: ' . $width_size . ';font-size:10px" class="text-center">' . $vs['vt1_text_show'] . ' </td>';
                                            }
                                            $tableTem .= '</tr><tr nobr="true" style="">
                                                <td style="width: 6%;">Size: </td>';
                                            $width_size = '4%';
                                            $width_size_main = '6%';
                                            foreach ($hv as $ks => $vs) {
                                                $tableTem .= '<td style="width: ' . $width_size . ';font-size:10px" class="text-center">' . $vs['Size'] . ' </td>';
                                            }
                                            $tableTem .= '</tr>
                                            <tr nobr="true" style="">
                                                <td style="width: ' . $width_size_main . ';">SL: </td>';
                                            foreach ($hv as $ks => $vs) {
                                                $tableTem .= '<td style="width: ' . $width_size . ';font-size:10px"  class="text-center">' . $vs['quantity_put'] . ' </td>';
                                            }
                                            $tableTem .= '</tr>
                                            <tr nobr="true" style="">
                                                <td style="width: ' . $width_size_main . ';">SX: </td>';
                                            foreach ($hv as $ks => $vs) {
                                                $clost = ($vs['quantity_put'] + ($vs['quantity_put'] >= 51 ? ($vs['quantity_put'] * $lost / 100)  : 0));
                                                $tableTem .= '<td style="width: ' . $width_size . ';font-size:10px" class="text-center">' . number_format($clost) . ' </td>';
                                            }
                                            $tableTem .= '</tr>
                                            <tr nobr="true" style="">
                                                <td style="width: ' . $width_size_main . ';">Tờ: </td>';
                                            foreach ($hv as $ks => $vs) {
                                                $clost = ($vs['quantity_put'] + ($vs['quantity_put'] >= 51 ? ($vs['quantity_put'] * $lost / 100)  : 0));
                                                $quantity_colum = floor($clost / $quantity_child_sheet);
                                                $tableTem .= '<td style="width: ' . $width_size . ';font-size:10px" class="text-center">' . number_format($quantity_colum) . ' </td>';
                                            }
                                            $tableTem .= '</tr>
                                            <tr nobr="true" style="">
                                                <td style="width: ' . $width_size_main . ';">Lẻ: </td>';
                                            foreach ($hv as $ks => $vs) {
                                                $clost = ($vs['quantity_put'] + ($vs['quantity_put'] >= 51 ? ($vs['quantity_put'] * $lost / 100)  : 0));
                                                $quantity_colum = floor($clost / $quantity_child_sheet);
                                                $quantity_odd = $clost - $quantity_child_sheet * $quantity_colum;
                                                $tableTem .= '<td style="width: ' . $width_size . ';font-size:10px" class="text-center">' . number_format($quantity_odd) . ' </td>';
                                            }
                                            $tableTem .= '</tr>
                                        </table><br><br><br>';
                                        }
                                    }
                                }
                            }
                        } else {
                            $this->load->library('ciqrcode');
                            if (!empty($quantity_ceil_bale_chan)) {
                                $quantity_new = $value['quantity'] - ($quantity_sheet_bale * $quantity_ceil_bale_chan);
                                $check_new = 0;
                                if ($quantity_new > 0) {
                                    $quantity_ceil_bale_chan = $quantity_ceil_bale_chan + 1;
                                    $check_new = 1;
                                }
                                $quantity_double = ceil($quantity_ceil_bale_chan / 2);

                                // print_arrays($quantity_ceil_bale);
                                $sttQ = 0;
                                $isBreak = false;
                                for ($i = 0; $i < $quantity_double; $i++) {
                                    if ($i == 55) break;
                                    $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="0"><tr nobr="true" style="">';
                                    for ($j = 0; $j < 2; $j++) {
                                        $sttQ++;
                                        if (($sttQ == $quantity_ceil_bale_chan && $check_new == 1)) {
                                            $quantity_sheet_bale = $quantity_new;
                                        }

                                        $qr = $order['so'] . '-' . $sttQ;

                                        $params['data'] = $qr;
                                        $params['level'] = 'H';
                                        $params['size'] = 20;
                                        $params['savename'] = FCPATH . 'uploads/orders/qrcode/' . $qr . '.png';
                                        $this->ciqrcode->generate($params);
                                        $img = file_get_contents(FCPATH . 'uploads/orders/qrcode/' . $qr . '.png');

                                        $tableTem .= '<td class="" style="width: 50%;">
                                            <table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                                <tr>
                                                    <td colspan="3" class="text-center" style="width: 100%;">' . $customer['company'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold" style="width: 30%;">Vendor code:</td>
                                                    <td style="width: 40%;"></td>
                                                    <td rowspan="5" style="width: 30%;" class="text-center">
                                                        <img width="80" src="data:image/png;base64,' . base64_encode($img) . '"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">SO#</td>
                                                    <td>' . $order['so'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">PI#:</td>
                                                    <td>' . $order['pi'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">PO#/Style:</td>
                                                    <td class="text-left">' . $order['po_style'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Item code:</td>
                                                    <td>' . $order['item_code'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Color/ Size:</td>
                                                    <td>' . $color_size . '</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Q\'ty:</td>
                                                    <td>' . formatNumber($quantity_sheet_bale) . '</td>
                                                    <td>pcs</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">G.W:</td>
                                                    <td>' . $gw . '</td>
                                                    <td>kg</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Carton:</td>
                                                    <td>' . ($sttQ . ' of ' . $quantity_ceil_bale_chan) . '</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Carton size:</td>
                                                    <td>' . $carton_size . '</td>
                                                    <td>cm</td>
                                                </tr>
                                            </table>
                                        </td>';

                                        if ($quantity_ceil_bale_chan == $sttQ) {
                                            $isBreak = true;
                                            break;
                                        }
                                    }
                                    $tableTem .= '</tr></table><br><br>';
                                    if ($isBreak) break;
                                }
                            }
                            // $tableTem.= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="0">
                            //     <tr nobr="true" style="">
                            //         <td class="" style="width: 50%;">
                            //             <table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                            //                 <tr>
                            //                     <td colspan="3" class="text-center" style="width: 100%;">'.$customer['company'].'</td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Vendor code:</td>
                            //                     <td></td>
                            //                     <td rowspan="5" class="text-center">
                            //                         <img width="80" src="data:image/png;base64,' . base64_encode($img) . '"/>
                            //                     </td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">SO#</td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">PI#:</td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">PO#/Style:</td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Item code:</td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Color/ Size:</td>
                            //                     <td></td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Q\'ty:</td>
                            //                     <td></td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">G.W:</td>
                            //                     <td></td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Carton:</td>
                            //                     <td></td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Carton size:</td>
                            //                     <td></td>
                            //                     <td></td>
                            //                 </tr>
                            //             </table>
                            //         </td>
                            //         <td class="" style="width: 50%;">
                            //         </td>
                            //     </tr>
                            // </table><br><br>';
                        }
                    } else if ($order['type_orders'] == ORDER_CHANGE) {

                        $arrSize = [1];
                        $trSize = '<td style="width: 10%;"></td>';
                        $trQuantityChild = '';
                        $trEvenQuantityChild = '';
                        $trOddQuantityChild = '';
                        $trEvenQuantityBaleChild = '';
                        $trOddQuantityBaleChild = '';

                        $this->db->select('
                            tbl_order_items_size.*,
                            tblsize.name as name_size,
                            tbl_colors.name as name_color,
                        ', false);
                        $this->db->from('tbl_order_items_size');
                        $this->db->join('tblsize', 'tblsize.id = tbl_order_items_size.size', 'left');
                        $this->db->join('tbl_colors', 'tbl_colors.id = tbl_order_items_size.color', 'left');
                        $this->db->where('tbl_order_items_size.order_item_id', $order_item_id);
                        $this->db->order_by('tblsize.name ASC');
                        $order_items_size = $this->db->get()->result_array();
                        if (!empty($order_items_size)) {
                            foreach ($order_items_size as $kS => $vS) {
                                // if (!empty($vS['size']) && !in_array($vS['size'], $arrSize)) {
                                $arrSize[] = $vS['size'];
                                $trSize .= '<td class="text-center">' . $vS['name_size'] . '</td>';
                                // }

                                $quantity_child = $vS['quantity'];
                                $even_quantity_child = '';
                                $odd_quantity_child = '';

                                $even_quantity_bale_child = '';
                                $odd_quantity_bale_child = '';

                                if ($quantity_child_sheet > 0) {
                                    $quantity_sheet = $quantity_child / $quantity_child_sheet;
                                    $even_quantity_child = floor($quantity_sheet);
                                    $quantity_ceil = ceil($quantity_sheet);
                                    $odd_quantity_child = $quantity_ceil - $even_quantity_child;
                                }


                                if ($quantity_sheet_bale > 0) {
                                    $quantity_bale = $value['quantity'] / $quantity_sheet_bale;
                                    $even_quantity_bale_child = floor($quantity_bale);
                                    $quantity_ceil_bale = ceil($quantity_bale);
                                    $odd_quantity_bale_child = $quantity_ceil_bale - $even_quantity_bale_child;
                                }

                                $trQuantityChild .= '<td class="text-center">' . $quantity_child . '</td>';
                                $trEvenQuantityChild .= '<td class="text-center">' . $even_quantity_child . '</td>';
                                $trOddQuantityChild .= '<td class="text-center">' . $odd_quantity_child . '</td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center">' . $even_quantity_bale_child . '</td>';
                                $trOddQuantityBaleChild .= '<td class="text-center">' . $odd_quantity_bale_child . '</td>';
                            }
                        }

                        if (count($arrSize) < 10) {
                            $nS = 10 - count($arrSize);
                            for ($i = 0; $i < $nS; $i++) {
                                $trSize .= '<td class="text-center" style="width: 10%;"></td>';
                                $trQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                            }
                        }

                        $tableTem .= '<table nobr="true" class="" cellspacing="0" cellpadding="5" border="1">
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã KH</td>
                                <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                <td class="" style="width: 10%;">Qui Cách</td>
                                <td class="text-center" style="width: 10%;">ĐVT</td>
                                <td class="text-center" style="width: 10%;">Mã Đơn</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã ĐĐH</td>
                                <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;font-family: kozgopromedium;font-size:11px">' . $product_name_customer . '</td>
                                <td class="" style="width: 10%;">' . $mode . '</td>
                                <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                <td class="text-center" style="width: 10%;">Chỉ Lệnh</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="text-center" style="width: 10%;">Size SP</td>
                                <td class="text-center" style="width: 10%;">Size ĐC</td>
                                <td class="text-center" style="width: 10%;">Style Number</td>
                                <td class="text-center" style="width: 10%;">Color Name</td>
                                <td class="text-center" style="width: 10%;">Số Lượng</td>
                                <td class="text-center" style="width: 10%;">SL Tờ Chẵn</td>
                                <td class="text-center" style="width: 10%;">SL Tờ Lẻ</td>
                                <td class="text-center" style="width: 10%;">Kiện Chẵn</td>
                                <td class="text-center" style="width: 10%;">Kiện Lẻ</td>
                                <td class="text-center" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" style="">
                                ' . $trSize . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Số Lượng</td>
                                ' . $trQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Tờ Chẵn</td>
                                ' . $trEvenQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Tờ Lẻ</td>
                                ' . $trOddQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Kiện Chặn</td>
                                ' . $trEvenQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Kiện Lẻ</td>
                                ' . $trOddQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td style="width: 10%;">QC Kiểm</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;">Ngày Giao</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                            </tr>
                        </table><br><br><br>';
                    } else if ($order['type_orders'] == ORDER_CHANGE_SIZE) {

                        $this->db->select('tbl_order_items_change_size.*');
                        $this->db->from('tbl_order_items_change_size');
                        $this->db->where('tbl_order_items_change_size.order_item_id', $order_item_id);
                        $this->db->order_by('tbl_order_items_change_size.number_size ASC');
                        $order_items_change_size = $this->db->get()->result_array();

                        $trSizeChild = '';
                        $trQuantityChild = '';
                        $trEvenQuantityChild = '';
                        $trOddQuantityChild = '';
                        $trEvenQuantityBaleChild = '';
                        $trOddQuantityBaleChild = '';

                        $arrSize = [];
                        if (!empty($order_items_change_size)) {
                            foreach ($order_items_change_size as $kS => $vS) {
                                $arrSize[] = $vS['number_size'];

                                $quantity_child = $vS['quantity'];
                                $even_quantity_child = $vS['even_sheet'];
                                $odd_quantity_child = $vS['odd_sheet'];
                                $even_quantity_bale_child = $vS['even_bale'];
                                $odd_quantity_bale_child = $vS['odd_bale'];

                                $trSizeChild .= '<td class="text-center" style="width: 10%;">' . $vS['number_size'] . '</td>';
                                $trQuantityChild .= '<td class="text-center" style="width: 10%;">' . $quantity_child . '</td>';
                                $trEvenQuantityChild .= '<td class="text-center" style="width: 10%;">' . $even_quantity_child . '</td>';
                                $trOddQuantityChild .= '<td class="text-center" style="width: 10%;">' . $odd_quantity_child . '</td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center" style="width: 10%;">' . $even_quantity_bale_child . '</td>';
                                $trOddQuantityBaleChild .= '<td class="text-center" style="width: 10%;">' . $odd_quantity_bale_child . '</td>';
                            }
                        }

                        if (count($arrSize) < 10) {
                            $nS = 10 - count($arrSize);
                            for ($i = 1; $i < $nS; $i++) {
                                $trSizeChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                            }
                        }

                        $tableTem .= '<table nobr="true" class="" cellspacing="0" cellpadding="5" border="1">
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã KH</td>
                                <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                <td class="" style="width: 10%;">Qui Cách</td>
                                <td class="text-center" style="width: 10%;">ĐVT</td>
                                <td class="text-center" style="width: 10%;">Mã Đơn</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã ĐĐH</td>
                                <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;font-family: kozgopromedium;font-size:11px">' . $product_name_customer . '</td>
                                <td class="" style="width: 10%;">' . $mode . '</td>
                                <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                <td class="text-center" style="width: 10%;">Chỉ Lệnh</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Size Đối Chiếu</td>
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Số Size</td>
                                ' . $trSizeChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Số Lượng</td>
                                ' . $trQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Tờ Chẵn</td>
                                ' . $trEvenQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Tờ Lẻ</td>
                                ' . $trOddQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Kiện Chẵn</td>
                                ' . $trEvenQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Kiện Lẻ</td>
                                ' . $trOddQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td style="width: 10%;">QC Kiểm</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;">Ngày Giao</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                            </tr>
                        </table><br><br><br>';
                    }
                }
            }
        }

        $data = [];
        $data['title'] = lang('tnh_print_tem');
        if ($type_print == 1) {
            $data['type'] = 'L';
        } else {
            $data['type'] = 'P';
        }

        $data['img'] = '';

        ob_start();
        stylePdf();
        echo $tableTem;
        $content = ob_get_contents();
        ob_end_clean();

        $data['showHeader'] = 'hide';
        $data['content'] = $content;
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function synthetic_import(){
        if (!has_permission('import', '', 'view') && !has_permission('import', '', 'view_own')) {
            access_denied('import');
        }

        $data['title'] = _l('dt_import');
        $this->db->select('tblsuppliers.id, tblsuppliers.company, CONCAT(prefix,"-",code) as code');
        $this->db->from('tblsuppliers');
        $data['dataSupplier'] = $this->db->get()->result_array();
        $this->load->view('admin/import/synthetic_import', $data);
    }

    public function getSyntheticImport(){
        $search_code = $this->input->post('search_code');
        $search_id_suppliers = $this->input->post('search_id_suppliers');
        $custom_item_select = $this->input->post('custom_item_select');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tblimport.id as id',
            'CONCAT(tblimport.prefix,"-",tblimport.code) as code_import',
            'CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code) as code_purchase_order',
            'tblimport.date as date_import',
            'tblpurchase_order.date as date_purchase_order',
            'CONCAT(tblpurchases.prefix,"",tblpurchases.code) as code_purchase',
            'tblpurchases.name_purchase as name_purchase',
            'tblpurchases.date as date',
            'tblpurchases.delivery_date as delivery_date',
            'tblsuppliers.company as company',
            'supplier_import.code as code_supplier',
            'supplier_import.company as company_import',
            'supplier_import.time_payment as time_payment',
            'supplier_import.tm_ck as tm_ck',
            'tblimport.status_qc as status_qc',
            'tblimport.warehouseman_id as warehouseman_id',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblimport';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tblsuppliers supplier_import ON supplier_import.id = tblimport.suppliers_id',
            'INNER JOIN tblimport_items ON tblimport_items.id_import = tblimport.id',
            'INNER JOIN tblpurchase_order ON tblpurchase_order.id = tblimport.id_order',
            'INNER JOIN tblpurchase_order_items ON tblpurchase_order_items.id_purchase_order = tblpurchase_order.id',
            'LEFT JOIN tbl_internal_proposal_purchase_items ON tbl_internal_proposal_purchase_items.id = tblpurchase_order_items.id_internal_proposal_purchase_items',
            'LEFT JOIN tblpurchases ON tblpurchases.id = tbl_internal_proposal_purchase_items.id_purchases',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id',
        ];


        if (!empty($search_code)){
            if (is_numeric($search_code)) {
                array_push($where, "AND tblimport.id = '" . $search_code . "'");
            }
        }

        if (!empty($custom_item_select)) {
            array_push($where, "AND tblimport_items.product_id = '" . $custom_item_select . "'");
        }

        if ($search_id_suppliers) {
            array_push($where, 'AND tblimport.suppliers_id IN (' . implode(', ',
                    $search_id_suppliers) . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tblimport.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tblimport.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblimport_items.product_id as id_items, 
             tblimport_items.type as type, 
             tblimport_items.quantity_net as quantity,
             tblimport_items.quantity_payment as quantity_payment,
             tblimport_items.price as price,
             tblimport_items.tax_rate as tax_rate
             '
        ], 'GROUP BY tblimport_items.id ORDER BY tblimport.id DESC', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity = 0;
        $total_amount = 0;
        $total_tax = 0;
        $grand_total = 0;
        foreach ($rResult as $key => $aRow) {
            $type_item = $aRow['type'];
            $items_id = $aRow['id_items'];
            $getItem = get_full_item_new($items_id, $type_item);

            $row = array();
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_import'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_purchase_order'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dhau($aRow['date_import']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_purchase_order'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dhau($aRow['date_purchase_order']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_supplier'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $aRow['company_import'] . '</div>';
            $row[] = '<div class="text-left" style="width: 90px">' . (!empty($aRow['time_payment']) ? $aRow['time_payment'] : '') . '</div>';
            $htmlPaymentMode = '';
            if ( $aRow['tm_ck'] == 1){
                $htmlPaymentMode = 'Tiền mặt';
            } elseif ($aRow['tm_ck'] == 2){
                $htmlPaymentMode = 'Chuyển khoản';
            }
            $row[] = '<div class="text-left" style="width: 90px">' .$htmlPaymentMode. '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_purchase'] . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . $aRow['name_purchase'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . _dt($aRow['delivery_date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 160px">' . $aRow['company'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $getItem->name_category . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $getItem->name_species . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $getItem->code . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $getItem->name . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $getItem->name_mode . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . $getItem->unit_name . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . $getItem->unit_name_stock . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . $getItem->unit_name_payment . '</div>';
            $row[] = '<div class="text-center" style="width: 90px">' . formatNumber($aRow['quantity_payment']) . '</div>';
            $row[] = '<div class="text-right" style="width: 100px">' . formatMoney($aRow['price']) . '</div>';
            $row[] = '<div class="text-right" style="width: 100px">' . formatMoney($aRow['quantity_payment'] * $aRow['price']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px"></div>';
            $row[] = '<div class="text-center" style="width: 80px">' . $getItem->time_stock . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . $getItem->quantity_minimum . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">'.(!empty($aRow['tax_rate']) ? $aRow['tax_rate'] : '').'</div>';
            $totalTax = ($aRow['quantity_payment'] * $aRow['price']) * $aRow['tax_rate'] / 100;
            $row[] = '<div class="text-right" style="width: 100px">'.($totalTax > 0 ? formatMoney($totalTax) : '').'</div>';
            $row[] = '<div class="text-right" style="width: 100px">'.formatMoney(($aRow['quantity_payment'] * $aRow['price']) + $totalTax).'</div>';
            $htmlQc = '';
            if ($aRow['status_qc'] == 0){
                $htmlQc = 'Chưa kiểm tra';
            } else{
                $htmlQc = 'Đã kiểm tra';
            }
            $row[] = '<div class="text-left" style="width: 100px">'.$htmlQc.'<div>';
            $htmlWarehouse = '';
            if(empty($aRow['warehouseman_id'])){
                $htmlWarehouse = 'Chưa duyệt kho';
            } else {
                $htmlWarehouse = 'Đã duyệt kho. Thủ kho :'.get_staff_full_name($aRow['warehouseman_id']).'';
            }
            $row[] = '<div class="text-left" style="width: 150px">'.$htmlWarehouse.'</div>';

            $total_quantity += $aRow['quantity_payment'];
            $total_amount += ($aRow['quantity_payment'] * $aRow['price']);
            $total_tax += $totalTax;
            $grand_total += ($aRow['quantity_payment'] * $aRow['price']) + $totalTax;
            $output['aaData'][] = $row;
        }
        $output['total_quantity'] = $total_quantity;
        $output['total_amount'] = $total_amount;
        $output['total_tax'] = $total_tax;
        $output['grand_total'] = $grand_total;
        echo json_encode($output);
    }

    public function exportExcelSyntheticImport(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $search_id_suppliers = $this->input->post('search_id_suppliers');
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $search_code = $this->input->post('search_code');
            $custom_item_select = $this->input->post('custom_item_select');
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

            $this->db->select('
                tblimport.id as id,
                CONCAT(tblimport.prefix,"-",tblimport.code) as code_import,
                CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code) as code_purchase_order,
                tblimport.date as date_import,
                tblpurchase_order.date as date_purchase_order,
                CONCAT(tblpurchases.prefix,"",tblpurchases.code) as code_purchase,
                tblpurchases.name_purchase as name_purchase,
                tblpurchases.date as date,
                tblpurchases.delivery_date as delivery_date,
                tblsuppliers.company as company,
                supplier_import.code as code_supplier,
                supplier_import.company as company_import,
                supplier_import.time_payment as time_payment,
                supplier_import.tm_ck as tm_ck,
                tblimport.status_qc as status_qc,
                tblimport.warehouseman_id as warehouseman_id,
                tblimport_items.product_id as id_items, 
                 tblimport_items.type as type, 
                 tblimport_items.quantity_net as quantity,
                 tblimport_items.quantity_payment as quantity_payment,
                 tblimport_items.price as price,
                 tblimport_items.tax_rate as tax_rate
            ');
            $this->db->from('tblimport');
            $this->db->join('tblsuppliers supplier_import','supplier_import.id = tblimport.suppliers_id');
            $this->db->join('tblimport_items','tblimport_items.id_import = tblimport.id');
            $this->db->join('tblpurchase_order','tblpurchase_order.id = tblimport.id_order');
            $this->db->join('tblpurchase_order_items','tblpurchase_order_items.id_purchase_order = tblpurchase_order.id');
            $this->db->join('tbl_internal_proposal_purchase_items','tbl_internal_proposal_purchase_items.id = tblpurchase_order_items.id_internal_proposal_purchase_items','left');
            $this->db->join('tblpurchases','tblpurchases.id = tbl_internal_proposal_purchase_items.id_purchases','left');
            $this->db->join('tblsuppliers','tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id','left');

            if (!empty($search_code)){
                if (is_numeric($search_code)) {
                    $this->db->where("tblimport.id = '" . $search_code . "'");
                }
            }

            if (!empty($custom_item_select)) {
                $this->db->where("tblimport_items.product_id = '" . $custom_item_select . "'");
            }

            if ($search_id_suppliers) {
                $this->db->where('tblimport.suppliers_id IN (' . implode(', ',
                        $search_id_suppliers) . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search);
                $this->db->where("tblimport.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search);
                $this->db->where("tblimport.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tblimport.id desc');
            $this->db->group_by('tblimport_items.id');
            $dtSyntheticImport = $this->db->get()->result_array();


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

            insertCompanyInfo($objPHPExcel, 'C1:AG2');

            $objPHPExcel->getActiveSheet()->setCellValue('A5',
                ('PHIẾU NHẬP KHO NPL'))->getStyle("A5")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A5:AG5');

            $sttRow = 2 + 4;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'Mã Nhập Kho');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Tham Chiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Nhập Kho');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'PO-Đơn Mua Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Ngày Lập PO');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Mã NCC');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Thời Hạn Thanh Toán')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Phương Pháp Thanh Toán')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Mã YCMHH');
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Tên Mã YCMHH');
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Ngày Lập YCMHH');
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Ngày Về NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Loại Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Nhóm NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Chủng Loại NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Mã NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Tên NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Quy Cách');
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Đơn Vị Chuẩn')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Đơn Vị Vào Kho')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'Đơn Vị Thanh toán')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$sttRow.'', 'Số Lượng');
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$sttRow.'', 'Giá Nhập');
            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$sttRow.'', 'Tổng Tiền');
            $objPHPExcel->getActiveSheet()->setCellValue('Z'.$sttRow.'', 'Tiêu Chuẩn Đóng Gói')->getStyle("Z$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AA'.$sttRow.'', 'Thời Gian Lưu Kho')->getStyle("AA$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AB'.$sttRow.'', 'Tồn Cho Phép')->getStyle("AB$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AC'.$sttRow.'', '% Thuế')->getStyle("AC$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AD'.$sttRow.'', 'Tổng Tiền Thuế')->getStyle("AD$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AE'.$sttRow.'', 'Thành Tiền')->getStyle("AE$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AF'.$sttRow.'', 'QC')->getStyle("AF$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AG'.$sttRow.'', 'Duyệt Kho')->getStyle("AG$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:AG$sttRow")->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:C$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'BDD7EE'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("D$sttRow:I$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'A9D08E'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("AC$sttRow:AE$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'A9D08E'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("AF$sttRow:AG$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'BDD7EE'),
                ),
            ]);
            $rowBegin = $sttRow;
            if (!empty($dtSyntheticImport)) {
                foreach ($dtSyntheticImport as $key => $value) {
                    $type_item = $value['type'];
                    $items_id = $value['id_items'];
                    $getItem = get_full_item_new($items_id, $type_item);
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $value['code_import']);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['code_purchase_order']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dhau($value['date_import']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['code_purchase_order']));
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", _dhau($value['date_purchase_order']));
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['code_supplier']));
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", ($value['company_import']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", (!empty($value['time_payment']) ? $value['time_payment'] : ''));
                    $htmlPaymentMode = '';
                    if ( $value['tm_ck'] == 1){
                        $htmlPaymentMode = 'Tiền mặt';
                    } elseif ($value['tm_ck'] == 2){
                        $htmlPaymentMode = 'Chuyển khoản';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $htmlPaymentMode);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['code_purchase']);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['name_purchase'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", _dt($value['delivery_date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", ($value['company']))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $getItem->name_category)->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $getItem->name_species)->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $getItem->code)->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $getItem->name)->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $getItem->name_mode)->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", $getItem->unit_name);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", $getItem->unit_name_stock);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", $getItem->unit_name_payment);
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin",
                        $value['quantity_payment'])->getStyle("W$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_payment']));
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin",
                        $value['price'])->getStyle("X$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin",
                        ($value['quantity_payment'] * $value['price']))->getStyle("Y$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_payment'] * $value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin",
                        '')->getStyle("Z$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin",
                        $getItem->time_stock)->getStyle("AA$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue("AB$rowBegin",
                        $getItem->quantity_minimum)->getStyle("AB$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue("AC$rowBegin",
                        (!empty($value['tax_rate']) ? $value['tax_rate'] : ''))->getStyle("AC$rowBegin");

                    $totalTax = ($value['quantity_payment'] * $value['price']) * $value['tax_rate'] / 100;
                    $objPHPExcel->getActiveSheet()->setCellValue("AD$rowBegin",
                        ($totalTax > 0 ? ($totalTax) : ''))->getStyle("AD$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($totalTax));
                    $objPHPExcel->getActiveSheet()->setCellValue("AE$rowBegin",
                        ($value['quantity_payment'] * $value['price']) + $totalTax)->getStyle("AE$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel(($value['quantity_payment'] * $value['price']) + $totalTax));
                    $htmlQc = '';
                    if ($value['status_qc'] == 0){
                        $htmlQc = 'Chưa kiểm tra';
                    } else{
                        $htmlQc = 'Đã kiểm tra';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("AF$rowBegin",
                        $htmlQc)->getStyle("AF$rowBegin")->getAlignment()->setWrapText(true);
                    $htmlWarehouse = '';
                    if(empty($value['warehouseman_id'])){
                        $htmlWarehouse = 'Chưa duyệt kho';
                    } else {
                        $htmlWarehouse = 'Đã duyệt kho. Thủ kho :'.get_staff_full_name($value['warehouseman_id']).'';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("AG$rowBegin",
                        $htmlWarehouse)->getStyle("AG$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:AG$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("H$rowBegin:H$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("T$rowBegin:W$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("AA$rowBegin:AC$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_nhap_kho_npl') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(30);
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

    public function add_delivery_supplier_code($import_id = 0){
        if ($this->input->post()){
            $delivery_supplier_code = $this->input->post('delivery_supplier_code');
            $this->db->where('id', $import_id);
            $success = $this->db->update('tblimport', ['delivery_supplier_code' => $delivery_supplier_code]);
            if ($success){
                $data['result'] = true;
                $data['message'] = lang('Cập nhập thành công');
            } else {
                $data['result'] = false;
                $data['message'] = lang('Cập nhập thất bại');
            }
            echo json_encode($data);die();
        }
        $this->db->select('id, code,delivery_supplier_code');
        $this->db->from('tblimport');
        $this->db->where('tblimport.id',$import_id);
        $data['dataImport'] = $this->db->get()->row_array();
        $data['id'] = $import_id;
        $this->load->view('admin/import/add_delivery_supplier_code', $data);
    }
}
