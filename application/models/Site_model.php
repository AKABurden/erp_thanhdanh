<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Site_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    // http://wampserver.aviatechno.net/
    public function sumWarehouseItems($id_items, $type_items)
    {
        $this->db->select('SUM(tblwarehouse_items.product_quantity) as quantity_warehouse', false);
        $this->db->from('tblwarehouse_items');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_items.warehouse_id');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.warehouse_id !=', constant("WAREHOUSES_CAPACITY"));
        $this->db->where('tblwarehouse.supplier_id', 0);
        $this->db->group_by('tblwarehouse_items.id_items');
        // print_arrays($this->db->get_compiled_select());
        return $this->db->get()->row_array();
    }

    public function getTaxs()
    {
        $this->db->select('*');
        $this->db->from('tbltaxes');
        return $this->db->get()->result_array();
    }

    public function rowTax($id)
    {
        $this->db->select('*');
        $this->db->from('tbltaxes');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowCustomer($id)
    {
        $this->db->select('*');
        $this->db->from('tblclients');
        $this->db->where('tblclients.userid', $id);
        return $this->db->get()->row_array();
    }

    public function rowShippingClient($id)
    {
        $this->db->select('*');
        $this->db->from('tblshipping_client');
        $this->db->where('tblshipping_client.id', $id);
        return $this->db->get()->row_array();
    }

    public function rowShippingLead($id)
    {
        $this->db->select('*');
        $this->db->from('tblshipping_lead');
        $this->db->where('tblshipping_lead.id', $id);
        return $this->db->get()->row_array();
    }

    public function searchSuppliers($q, $limit, $type = 0)
    {
        $this->db->select('
            tblsuppliers.id as id, tblsuppliers.company as text,
            CONCAT(tblsuppliers.prefix, "-", tblsuppliers.code) as code1,
            tblsuppliers.code as code
        ', false);
        $this->db->from('tblsuppliers');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tblsuppliers.company', $q);
            $this->db->or_like('tblsuppliers.phone', $q);
            $this->db->group_end();
        }
        $this->db->where('tblsuppliers.type', $type);
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchSuppliersOfType($q, $limit, $type)
    {
        $this->db->select('tblsuppliers.id as id, tblsuppliers.company as text', false);
        $this->db->from('tblsuppliers');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tblsuppliers.company', $q);
            $this->db->or_like('tblsuppliers.phone', $q);
            $this->db->group_end();
        }
        $this->db->where('tblsuppliers.type', $type);
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function rowSupplier($id)
    {
        $this->db->select('*');
        $this->db->from('tblsuppliers');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getProcedureClientDetail($type = "materials")
    {
        $this->db->select('tblprocedure_client_detail.*');
        $this->db->from('tblprocedure_client');
        $this->db->join('tblprocedure_client_detail', 'tblprocedure_client_detail.id_detail = tblprocedure_client.id');
        $this->db->where('tblprocedure_client.type', $type);
        return $this->db->get()->result_array();
    }

    public function getWarehouse($not_arr_id = [])
    {
        $is_admin = is_admin();
        $staff_id = get_staff_user_id();
        $this->db->select('tblwarehouse.*');
        $this->db->from('tblwarehouse');
        if (!$is_admin) {
            // $staff_id = get_staff_user_id();
            // $this->db->join("tblwarehouse_staff", "tblwarehouse_staff.warehouse_id = tblwarehouse.id AND tblwarehouse_staff.staff_id = $staff_id");
        }
        if (!empty($not_arr_id)) {
            $this->db->where_not_in('tblwarehouse.id', $not_arr_id);
        }
        $this->db->where('tblwarehouse.id NOT IN (' . WAREHOUSES_SYSTEM . ')');
        return $this->db->get()->result_array();
    }

    public function getWarehouseNew($not_arr_id = [])
    {
        $this->db->select('tblwarehouse.*');
        $this->db->from('tblwarehouse');
        if (!is_admin()) {
            $staff_id = get_staff_user_id();
            $this->db->join("tblwarehouse_staff", "tblwarehouse_staff.warehouse_id = tblwarehouse.id AND tblwarehouse_staff.staff_id = $staff_id");
        }
        if (!empty($not_arr_id)) {
            $this->db->where_not_in('tblwarehouse.id', $not_arr_id);
        }
        $this->db->where('tblwarehouse.id !=', WAREHOUSES_CAPACITY);
        $this->db->where('tblwarehouse.id !=', WAREHOUSES_ERRORS);
        return $this->db->get()->result_array();
    }

    public function rowComboboxClient($id)
    {
        $this->db->select('*');
        $this->db->from('tblcombobox_client');
        $this->db->where('tblcombobox_client.id', $id);
        return $this->db->get()->row_array();
    }

    public function rowClientInfoDetailValue($id)
    {
        $this->db->select('*');
        $this->db->from('tblclient_info_detail_value');
        $this->db->where('tblclient_info_detail_value.id', $id);
        return $this->db->get()->row_array();
    }

    public function getProductGroupInfoByProductId($product_id)
    {
        $this->db->select('*');
        $this->db->from('tblproduct_group_info');
        $this->db->where('tblproduct_group_info.product_id', $product_id);
        return $this->db->get()->result_array();
    }

    public function deleteProductGroupInfo($id_product)
    {
        $this->db->where('tblproduct_group_info.id_product', $id_product);
        return $this->db->delete('tblproduct_group_info');
    }

    public function getClientInfoDetailProduct()
    {
        $this->db->select('*');
        $this->db->from('tblclient_info_detail');
        $this->db->where('tblclient_info_detail.view_modal', 1);
        $this->db->where('tblclient_info_detail.show_on_table', 1);
        return $this->db->get()->result_array();
    }

    public function rowClientsByLeadId($leadid)
    {
        $this->db->select('*');
        $this->db->from('tblclients');
        $this->db->where('tblclients.leadid', $leadid);
        return $this->db->get()->result_array();
    }

    public function getAllStaffId()
    {
        $this->db->select('GROUP_CONCAT(tblstaff.staffid) as staffid');
        $this->db->from('tblstaff');
        return $this->db->get()->row_array();
    }

    public function getStaff()
    {
        $this->db->select('tblstaff.staffid, tblstaff.firstname, tblstaff.lastname');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        return $this->db->get()->result_array();
    }

    public function rowSetPricesItems($id_set_prices, $type, $id_product)
    {
        $this->db->select('*');
        $this->db->from('tbl_set_prices_items');
        $this->db->where('tbl_set_prices_items.id_set_prices', $id_set_prices);
        $this->db->where('tbl_set_prices_items.type', $type);
        $this->db->where('tbl_set_prices_items.id_product', $id_product);
        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->row_array();
    }

    public function rowProduct($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_products');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowItem($id)
    {
        $this->db->select('*');
        $this->db->from('tblitems');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowSetPricesById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_set_prices');
        $this->db->where('tbl_set_prices.id', $id);
        return $this->db->get()->row_array();
    }

    public function rowSetPricesByNewId($id)
    {
        $date = date('Y-m-d');

        $this->db->select('*');
        $this->db->from('tbl_set_prices');
        $this->db->where('tbl_set_prices.id', $id);
        $this->db->where('(tbl_set_prices.date_start = "0000-00-00" OR (tbl_set_prices.date_start <= "' . $date . '" AND tbl_set_prices.date_end >= "' . $date . '"))');
        $this->db->where('tbl_set_prices.status', 1);
        // $this->db->where('tbl_set_prices.type_customer', 1);
        return $this->db->get()->row_array();
    }

    public function searchTableDiscount($term, $limit, $customer_id)
    {
        $date = date('Y-m-d');
        $this->db->select('tblcustomer_groups.*');
        $this->db->from('tblcustomer_groups');
        $this->db->where('tblcustomer_groups.customer_id', $customer_id);
        $customerGroups = $this->db->get()->result_array();

        $this->db->select('tbldiscount.id as id, tbldiscount.name_discount as text');
        $this->db->from('tbldiscount');

        $this->db->group_start();
        $this->db->like('tbldiscount.name_discount', $term);
        $this->db->group_end();

        $this->db->where('(tbldiscount.date_start <= "' . $date . '" AND tbldiscount.date_end >= "' . $date . '")');

        $wGroupClient = '';
        if (!empty($customerGroups)) {
            $wGroupClient .= ' AND (';
            foreach ($customerGroups as $key => $value) {
                $wGroupClient .= "(FIND_IN_SET(" . $value['groupid'] . ", tbldiscount.type_client) > 0) OR";
            }
            $wGroupClient = substr($wGroupClient, 0, -2);
            $wGroupClient .= ' ) ';
        }

        $wGroupClientOption2 = "(
            SELECT count(*)
            FROM tbldiscount_client
            WHERE tbldiscount_client.id_client = $customer_id AND tbldiscount_client.id_client = tbldiscount.id
            LIMIT 1
        )";
        $this->db->where("(
            (tbldiscount.id_client = 1 $wGroupClient)
            OR
            (tbldiscount.id_client = 2 AND $wGroupClientOption2 > 0)
        )");

        $this->db->where('tbldiscount.apply', 1);
        $this->db->where('tbldiscount.type', 1);
        $this->db->where('tbldiscount.status', 1);

        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->result_array();
    }

    public function rowDiscountDetail($id_discount, $id_category)
    {
        $this->db->select('*');
        $this->db->from('tbldiscount_datail');
        $this->db->where('tbldiscount_datail.id_discount', $id_discount);
        $this->db->where('tbldiscount_datail.id_category', $id_category);
        return $this->db->get()->row_array();
    }

    public function rowDiscountDetailProduct($id_discount, $id_category)
    {
        $this->db->select('*');
        $this->db->from('tbldiscount_datail_product');
        $this->db->where('tbldiscount_datail_product.id_discount', $id_discount);
        $this->db->where('tbldiscount_datail_product.id_category', $id_category);
        return $this->db->get()->row_array();
    }

    public function rowDiscountById($id)
    {
        $this->db->select('*');
        $this->db->from('tbldiscount');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowDiscountByIdNew($id)
    {
        $date = date('Y-m-d');

        $this->db->select('*');
        $this->db->from('tbldiscount');
        $this->db->where('id', $id);

        $this->db->where('(tbldiscount.date_start <= "' . $date . '" AND tbldiscount.date_end >= "' . $date . '")');
        $this->db->where('tbldiscount.apply', 1);
        $this->db->where('tbldiscount.type', 1);
        $this->db->where('tbldiscount.status', 1);

        return $this->db->get()->row_array();
    }

    public function getWarehouseItemsByItemIdAndTypeAndWarehouse($id_items, $type_items, $warehouse_id)
    {
        $this->db->select('tblwarehouse_items.*');
        $this->db->from('tblwarehouse_items');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        return $this->db->get()->result_array();
    }

    public function rowWarehouseById($id)
    {
        $this->db->select('*');
        $this->db->from('tblwarehouse');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowLocationWarehouseById($id)
    {
        $this->db->select('*');
        $this->db->from('tbllocaltion_warehouses');
        $this->db->where('tbllocaltion_warehouses.id', $id);
        return $this->db->get()->row_array();
    }

    public function getGiftForOrders($customer_id, $date, $arrItems = [])
    {
        $this->db->select('tblcustomer_groups.*');
        $this->db->from('tblcustomer_groups');
        $this->db->where('tblcustomer_groups.customer_id', $customer_id);
        $customerGroups = $this->db->get()->result_array();
        $wGroupClient = '';
        if (!empty($customerGroups)) {
            $wGroupClient .= ' AND (';
            foreach ($customerGroups as $key => $value) {
                $wGroupClient .= "(FIND_IN_SET(" . $value['groupid'] . ", tblpromotion.groups_in) > 0) OR";
            }
            $wGroupClient = substr($wGroupClient, 0, -2);
            $wGroupClient .= ' ) ';
        }

        //handling items and category
        if (!empty($arrItems)) {
            $arr_category_id = [];
            $arr_category_info = [];
            $conditionItems = '(';
            foreach ($arrItems as $key => $value) {
                $type_item = $value['type_item'] == "products" ? 'product' : 'items';
                $id_item = $value['item_id'];
                $total_quantity = $value['total_quantity'];

                $conditionItems .= "(SELECT
                    count(*)
                    FROM tblpromotion_item
                    WHERE tblpromotion_item.promotion_id = tblpromotion.id AND tblpromotion_item.id_item = $id_item AND tblpromotion_item.type_item = '$type_item' AND tblpromotion_item.quantity <= $total_quantity
                ) > 0 OR";

                //check category
                if ($value['type_item'] == "products") {
                    $info = $this->products_model->rowProduct($value['item_id']);
                } else if ($value['type_item'] == "items") {
                    $info = $this->items_model->rowItems($value['item_id']);
                }

                if (!empty($info)) {
                    $category_id = $value['type_item'] . '__' . $info['category_id'];
                    $index = array_search($category_id, $arr_category_id);
                    if (!$index) {
                        $arr_category_id[] = $category_id;
                        $arr_category_info[]['quantity'] = $total_quantity;
                    } else {
                        $arr_category_info[$index]['quantity'] = $arr_category_info[$index]['quantity'] + $total_quantity;
                    }
                }
            }
            $conditionItems = substr($conditionItems, 0, -2);
            $conditionItems .= ' ) ';

            //handing category
            $conditionCategory = '';
            if (!empty($arr_category_id)) {
                $conditionCategory = 'OR (';
                foreach ($arr_category_id as $key => $value) {
                    $ct = explode('__', $value);
                    $type_category = $ct[0] == 'products' ? 'categoriesProducts' : 'categoriesItems';
                    $category_id = $ct[1];
                    $quantity = $arr_category_info[$key]['quantity'];

                    $conditionCategory .= "(SELECT
                        count(*)
                        FROM tblpromotion_item
                        WHERE tblpromotion_item.promotion_id = tblpromotion.id AND tblpromotion_item.id_item = $category_id AND tblpromotion_item.type_item = '$type_category' AND tblpromotion_item.quantity <= $quantity
                    ) > 0 OR";
                }
                $conditionCategory = substr($conditionCategory, 0, -2);
                $conditionCategory .= ' ) ';
            }
            // print_arrays($conditionCategory);

        }

        $this->db->select('tblpromotion.*');
        $this->db->from('tblpromotion');

        $this->db->where('tblpromotion.status', 1);
        // $this->db->where("(DATE(now()) BETWEEN tblpromotion.date_active_start AND tblpromotion.date_active_end)");
        $this->db->where("(tblpromotion.date_active_start <= '$date' AND tblpromotion.date_active_end >= '$date')");
        $this->db->where('tblpromotion.type', 'item');

        $conditionOther = "((tblpromotion.method_of_application = 'other' OR tblpromotion.area_of_application = 'other')  AND (
                SELECT COUNT(*)
                FROM tblpromotion_customer
                WHERE tblpromotion_customer.promotion_id = tblpromotion.id AND tblpromotion_customer.customer_id = $customer_id
            ) > 0)";

        $conditionAll = "((tblpromotion.method_of_application = 'one' || tblpromotion.method_of_application = 'all') AND tblpromotion.area_of_application = 'all') OR";

        $conditionOneArea = '';
        if (!empty($wGroupClient)) {
            $conditionOneArea = "((tblpromotion.method_of_application = 'one' OR tblpromotion.method_of_application = 'all') AND tblpromotion.area_of_application = 'area' $wGroupClient) OR ";
        }

        $this->db->where("(
            $conditionAll
            $conditionOneArea
            $conditionOther
        )");

        //conditions items

        if (!empty($arrItems)) {
            $this->db->where("(
                $conditionItems
                $conditionCategory
            )");
        }
        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->result_array();
    }

    public function getGiftItemForOrders($customer_id, $date, $arrItems = [], $id)
    {
        $this->db->select('tblcustomer_groups.*');
        $this->db->from('tblcustomer_groups');
        $this->db->where('tblcustomer_groups.customer_id', $customer_id);
        $customerGroups = $this->db->get()->result_array();
        $wGroupClient = '';
        if (!empty($customerGroups)) {
            $wGroupClient .= ' AND (';
            foreach ($customerGroups as $key => $value) {
                $wGroupClient .= "(FIND_IN_SET(" . $value['groupid'] . ", tblpromotion.groups_in) > 0) OR";
            }
            $wGroupClient = substr($wGroupClient, 0, -2);
            $wGroupClient .= ' ) ';
        }

        //conditions items and category
        if (!empty($arrItems)) {
            $arr_category_id = [];
            $arr_category_info = [];
            $conditionItems = '(';
            foreach ($arrItems as $key => $value) {
                $type_item = $value['type_item'] == "products" ? 'product' : 'items';
                $id_item = $value['item_id'];
                $total_quantity = $value['total_quantity'];

                $conditionItems .= "(
                    tblpromotion_item.promotion_id = tblpromotion.id AND tblpromotion_item.id_item = $id_item AND tblpromotion_item.type_item = '$type_item' AND tblpromotion_item.quantity <= $total_quantity
                ) OR";

                //check category
                if ($value['type_item'] == "products") {
                    $info = $this->products_model->rowProduct($value['item_id']);
                } else if ($value['type_item'] == "items") {
                    $info = $this->items_model->rowItems($value['item_id']);
                }

                if (!empty($info)) {
                    $category_id = $value['type_item'] . '__' . $info['category_id'];
                    $index = array_search($category_id, $arr_category_id);
                    if (!$index) {
                        $arr_category_id[] = $category_id;
                        $arr_category_info[]['quantity'] = $total_quantity;
                    } else {
                        $arr_category_info[$index]['quantity'] = $arr_category_info[$index]['quantity'] + $total_quantity;
                    }
                }
            }
            $conditionItems = substr($conditionItems, 0, -2);
            $conditionItems .= ' ) ';

            //handing category
            $conditionCategory = '';
            if (!empty($arr_category_id)) {
                $conditionCategory = 'OR (';
                foreach ($arr_category_id as $key => $value) {
                    $ct = explode('__', $value);
                    $type_category = $ct[0] == 'products' ? 'categoriesProducts' : 'categoriesItems';
                    $category_id = $ct[1];
                    $quantity = $arr_category_info[$key]['quantity'];

                    $conditionCategory .= "(tblpromotion_item.promotion_id = tblpromotion.id AND tblpromotion_item.id_item = $category_id AND tblpromotion_item.type_item = '$type_category' AND tblpromotion_item.quantity <= $quantity) OR";
                }
                $conditionCategory = substr($conditionCategory, 0, -2);
                $conditionCategory .= ' ) ';
            }
        }

        $this->db->select('tblpromotion_item_gift.*, tblpromotion.name as name_gift');
        $this->db->from('tblpromotion');
        $this->db->join('tblpromotion_item', 'tblpromotion_item.promotion_id = tblpromotion.id');
        $this->db->join('tblpromotion_item_gift', 'tblpromotion_item_gift.promotion_id = tblpromotion.id AND tblpromotion_item_gift.promotion_item_id = tblpromotion_item.id_item AND tblpromotion_item_gift.promotion_item_id = tblpromotion_item.id_item AND tblpromotion_item.type_item = tblpromotion_item_gift.promotion_item_type');

        $this->db->where('tblpromotion.status', 1);
        // $this->db->where("(DATE(now()) BETWEEN tblpromotion.date_active_start AND tblpromotion.date_active_end)");
        $this->db->where("(tblpromotion.date_active_start <= '$date' AND tblpromotion.date_active_end >= '$date')");
        $this->db->where('tblpromotion.type', 'item');
        $this->db->where('tblpromotion.id', $id);

        if (!empty($arrItems)) {
            $this->db->where("(
                $conditionItems
                $conditionCategory
            )");
        }
        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->result_array();
    }

    public function getGiftItemForOrdersNew($customer_id, $date, $arrItems = [], $id)
    {
        $this->db->select('tblcustomer_groups.*');
        $this->db->from('tblcustomer_groups');
        $this->db->where('tblcustomer_groups.customer_id', $customer_id);
        $customerGroups = $this->db->get()->result_array();
        $wGroupClient = '';
        if (!empty($customerGroups)) {
            $wGroupClient .= ' AND (';
            foreach ($customerGroups as $key => $value) {
                $wGroupClient .= "(FIND_IN_SET(" . $value['groupid'] . ", tblpromotion.groups_in) > 0) OR";
            }
            $wGroupClient = substr($wGroupClient, 0, -2);
            $wGroupClient .= ' ) ';
        }

        //conditions items and category
        $it = [];
        if (!empty($arrItems)) {
            $arr_category_id = [];
            $arr_category_info = [];
            $conditionItems = '(';
            foreach ($arrItems as $key => $value) {
                $type_item = $value['type_item'] == "products" ? 'product' : 'items';
                $id_item = $value['item_id'];
                $total_quantity = $value['total_quantity'];

                // $conditionItems.= "(
                //     tblpromotion_item.promotion_id = tblpromotion.id AND tblpromotion_item.id_item = $id_item AND tblpromotion_item.type_item = '$type_item' AND tblpromotion_item.quantity <= $total_quantity
                // ) OR";

                $conditionItems = "(
                    tblpromotion_item.promotion_id = tblpromotion.id AND tblpromotion_item.id_item = $id_item AND tblpromotion_item.type_item = '$type_item' AND tblpromotion_item.quantity <= $total_quantity
                )";

                $this->db->select("tblpromotion_item_gift.*, tblpromotion.name as name_gift, FLOOR($total_quantity/tblpromotion_item.quantity) as qty_bs, tblpromotion_item.id as promotion_item_id, $total_quantity as quantity_condition");
                $this->db->from('tblpromotion');
                $this->db->join('tblpromotion_item', 'tblpromotion_item.promotion_id = tblpromotion.id');
                $this->db->join('tblpromotion_item_gift', 'tblpromotion_item_gift.promotion_id = tblpromotion.id AND tblpromotion_item_gift.promotion_item_id = tblpromotion_item.id_item AND tblpromotion_item_gift.promotion_item_id = tblpromotion_item.id_item AND tblpromotion_item.type_item = tblpromotion_item_gift.promotion_item_type');

                $this->db->where('tblpromotion.status', 1);
                $this->db->where("(tblpromotion.date_active_start <= '$date' AND tblpromotion.date_active_end >= '$date')");
                $this->db->where('tblpromotion.type', 'item');
                $this->db->where('tblpromotion.id', $id);
                $this->db->where("$conditionItems");

                $result = $this->db->get()->result_array();
                if (!empty($result)) {
                    // $it[] = $result;
                    foreach ($result as $k => $val) {
                        $it[] = $val;
                    }
                }

                //check category
                if ($value['type_item'] == "products") {
                    $info = $this->products_model->rowProduct($value['item_id']);
                } else if ($value['type_item'] == "items") {
                    $info = $this->items_model->rowItems($value['item_id']);
                }

                if (!empty($info)) {
                    $category_id = $value['type_item'] . '__' . $info['category_id'];
                    $index = array_search($category_id, $arr_category_id);
                    if (!$index) {
                        $arr_category_id[] = $category_id;
                        $arr_category_info[]['quantity'] = $total_quantity;
                    } else {
                        $arr_category_info[$index]['quantity'] = $arr_category_info[$index]['quantity'] + $total_quantity;
                    }
                }
            }
            $conditionItems = substr($conditionItems, 0, -2);
            $conditionItems .= ' ) ';

            //handing category
            $conditionCategory = '';
            if (!empty($arr_category_id)) {
                $conditionCategory = 'OR (';
                foreach ($arr_category_id as $key => $value) {
                    $ct = explode('__', $value);
                    $type_category = $ct[0] == 'products' ? 'categoriesProducts' : 'categoriesItems';
                    $category_id = $ct[1];
                    $quantity = $arr_category_info[$key]['quantity'];

                    $conditionCategory = "(tblpromotion_item.promotion_id = tblpromotion.id AND tblpromotion_item.id_item = $category_id AND tblpromotion_item.type_item = '$type_category' AND tblpromotion_item.quantity <= $quantity)";

                    $this->db->select("tblpromotion_item_gift.*, tblpromotion.name as name_gift, FLOOR($quantity/tblpromotion_item.quantity) as qty_bs, tblpromotion_item.id as promotion_item_id, $quantity as quantity_condition");
                    $this->db->from('tblpromotion');
                    $this->db->join('tblpromotion_item', 'tblpromotion_item.promotion_id = tblpromotion.id');
                    $this->db->join('tblpromotion_item_gift', 'tblpromotion_item_gift.promotion_id = tblpromotion.id AND tblpromotion_item_gift.promotion_item_id = tblpromotion_item.id_item AND tblpromotion_item_gift.promotion_item_id = tblpromotion_item.id_item AND tblpromotion_item.type_item = tblpromotion_item_gift.promotion_item_type');

                    $this->db->where('tblpromotion.status', 1);
                    $this->db->where("(tblpromotion.date_active_start <= '$date' AND tblpromotion.date_active_end >= '$date')");
                    $this->db->where('tblpromotion.type', 'item');
                    $this->db->where('tblpromotion.id', $id);
                    $this->db->where("$conditionCategory");

                    // $result = $this->db->get()->row_array();
                    // if (!empty($result)) {
                    //     $it[] = $result;
                    // }
                    $result = $this->db->get()->result_array();
                    if (!empty($result)) {
                        // $it[] = $result;
                        foreach ($result as $k => $val) {
                            $it[] = $val;
                        }
                    }
                }
                $conditionCategory = substr($conditionCategory, 0, -2);
                $conditionCategory .= ' ) ';
            }
        }

        return $it;
        // print_arrays($it);
        // print_arrays($this->db->get_compiled_select(), FALSE);
        // return $this->db->get()->result_array();
    }

    public function getPromotionForOrders($customer_id, $total_money)
    {
        $this->db->select('tblcustomer_groups.*');
        $this->db->from('tblcustomer_groups');
        $this->db->where('tblcustomer_groups.customer_id', $customer_id);
        $customerGroups = $this->db->get()->result_array();
        $wGroupClient = '';
        if (!empty($customerGroups)) {
            $wGroupClient .= ' AND (';
            foreach ($customerGroups as $key => $value) {
                $wGroupClient .= "(FIND_IN_SET(" . $value['groupid'] . ", tblpromotion.groups_in) > 0) OR";
            }
            $wGroupClient = substr($wGroupClient, 0, -2);
            $wGroupClient .= ' ) ';
        }

        $this->db->select('tblpromotion.*');
        $this->db->from('tblpromotion');

        $this->db->where('tblpromotion.status', 1);
        $this->db->where("(DATE(now()) BETWEEN tblpromotion.date_active_start AND tblpromotion.date_active_end)");

        $conditionOther = "((tblpromotion.method_of_application = 'other' OR tblpromotion.area_of_application = 'other')  AND (
                SELECT COUNT(*)
                FROM tblpromotion_customer
                WHERE tblpromotion_customer.promotion_id = tblpromotion.id AND tblpromotion_customer.customer_id = $customer_id
            ) > 0)";

        $conditionAll = "((tblpromotion.method_of_application = 'one' || tblpromotion.method_of_application = 'all') AND tblpromotion.area_of_application = 'all') OR";

        $conditionOneArea = '';
        if (!empty($wGroupClient)) {
            $conditionOneArea = "((tblpromotion.method_of_application = 'one' OR tblpromotion.method_of_application = 'all') AND tblpromotion.area_of_application = 'area' $wGroupClient) OR ";
        }

        $this->db->where("(
            $conditionAll
            $conditionOneArea
            $conditionOther
        )");

        //condition promotion discount
        $conditionPromotionDiscountFirst = "(
            SELECT
                COUNT(*)
            FROM tblpromotion_discount
            INNER JOIN tblpromotion_discount_amount ON tblpromotion_discount_amount.promotion_id = tblpromotion_discount.promotion_id
            WHERE tblpromotion_discount.promotion_id = tblpromotion.id AND tblpromotion_discount.type_discount = 1 AND  $total_money >= tblpromotion_discount_amount.limit_sales
        ) > 0";

        // $conditionPromotionDiscountSecond = "(
        //     SELECT
        //         COUNT(*)
        //     FROM tblpromotion_discount
        //     WHERE tblpromotion_discount.promotion_id = tblpromotion.id AND tblpromotion_discount.type_discount = 2
        // )";
        $this->db->where("(
            $conditionPromotionDiscountFirst
        )");

        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->result_array();
    }

    public function insertOrdersGifts($data = [])
    {
        $this->db->insert('tbl_orders_gifts', $data);
        return $this->db->insert_id();
    }

    public function rowPromotionItemGift($id)
    {
        $this->db->select('tblpromotion_item_gift.*');
        $this->db->from('tblpromotion_item_gift');
        $this->db->where('tblpromotion_item_gift.id', $id);
        return $this->db->get()->row_array();
    }

    public function checkGiftExistOrderItems($order_id, $promotion_item_gift_id)
    {
        $this->db->from('tbl_order_items');
        $this->db->where('tbl_order_items.order_id', $order_id);
        $this->db->where('tbl_order_items.promotion_item_gift_id', $promotion_item_gift_id);
        return $this->db->get()->num_rows();
    }

    public function deleteOrderGifts($order_id)
    {
        $this->db->where('order_id', $order_id);
        return $this->db->delete('tbl_orders_gifts');
    }

    public function deleteOrderItemsByGift($order_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->where('type_gift', 1);
        return $this->db->delete('tbl_order_items');
    }

    public function getOrdersGifts($order_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_orders_gifts');
        $this->db->where('tbl_orders_gifts.order_id', $order_id);
        return $this->db->get()->result_array();
    }

    public function getGiftOrderItems($order_id)
    {
        $this->db->select('tblpromotion.name as name_gift, tbl_order_items.*');
        $this->db->from('tbl_order_items');
        $this->db->join('tblpromotion_item_gift', 'tblpromotion_item_gift.id = tbl_order_items.promotion_item_gift_id', 'inner');
        $this->db->join('tblpromotion', 'tblpromotion.id = tblpromotion_item_gift.promotion_id', 'inner');
        $this->db->where('tbl_order_items.order_id', $order_id);
        $this->db->where('tbl_order_items.type_gift', 1);
        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->result_array();
    }

    public function getStaffDepartments($deparmentId)
    {
        $this->db->select('tblstaff.*');
        $this->db->from('tblstaff_departments');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblstaff_departments.staffid', 'inner');
        $this->db->where('departmentid', $deparmentId);
        return $this->db->get()->result_array();
    }

    public function getStaffDepartmentsIn($deparmentId = [])
    {
        if (empty($deparmentId)) {
            return false;
        }
        $this->db->select('tblstaff.*');
        $this->db->from('tblstaff_departments');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblstaff_departments.staffid', 'inner');
        $this->db->where_in('departmentid', $deparmentId);
        $this->db->group_by('tblstaff.staffid');
        return $this->db->get()->result_array();
    }

    public function searchProductionsOD($q, $limit = 50)
    {
        $quantity = "COALESCE((
            SELECT tbl_productions_orders_items.quantity
            FROM tbl_productions_orders_items
            WHERE tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
        ), 0)";

        $quantityShiftWork = "COALESCE((
            SELECT SUM(tbltasks.quantity_shift_work)
            FROM tbltasks
            WHERE tbltasks.rel_type = 'order_production_details' AND tbltasks.rel_id = tbl_productions_orders_details.id
        ), 0)";

        $this->db->select("tbl_productions_orders_details.id as id, 
        	tbl_productions_orders_details.reference_no as name,
        	CONCAT(tbl_productions_orders_details.reference_no, '<br/><i style=\'font-size: 11px;\'>KH: ', tbl_orders.customer_name, '</i><br/><i style=\'font-size: 11px;\'>SP: ', tbl_productions_orders_items.items_name,'</i>') as data_content,
         ($quantity - $quantityShiftWork) as quantity_rest", false);
        $this->db->from('tbl_productions_orders_details');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_details.reference_no', $q);
            $this->db->or_like('tbl_orders.customer_name', $q);
            $this->db->or_like('tbl_productions_orders_items.items_name', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_productions_orders_details.status !=', 'complete_production');
        $this->db->limit($limit);
        //cong bo sung
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
        //
        return $this->db->get()->result_array();
    }

    public function getProductionsOD($id)
    {
        $quantity = "COALESCE((
            SELECT tbl_productions_orders_items.quantity
            FROM tbl_productions_orders_items
            WHERE tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
        ), 0)";

        $quantityShiftWork = "COALESCE((
            SELECT SUM(tbltasks.quantity_shift_work)
            FROM tbltasks
            WHERE tbltasks.rel_type = 'order_production_details' AND tbltasks.rel_id = tbl_productions_orders_details.id
        ), 0)";

        $this->db->select("tbl_productions_orders_details.id as id, tbl_productions_orders_details.reference_no as name, ($quantity - $quantityShiftWork) as quantity_rest", false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->where('tbl_productions_orders_details.id', $id);
        return $this->db->get()->row_array();
    }

    public function insertTasks($data)
    {
        $this->db->insert('tbltasks', $data);
        return $this->db->insert_id();
    }

    public function insertTasksAssigned($data)
    {
        $this->db->insert('tbltask_assigned', $data);
        return $this->db->insert_id();
    }

    public function insertBatchTasksAssigned($data)
    {
        $this->db->insert_batch('tbltask_assigned', $data);
        return $this->db->insert_id();
    }

    public function deleteTasksByRelIdAndRelType($rel_id, $rel_type)
    {
        $this->db->where('tbltasks.rel_id', $rel_id);
        $this->db->where('tbltasks.rel_type', $rel_type);
        return $this->db->delete('tbltasks');
    }

    public function getTasksByRelTypeAndRelId($rel_id, $rel_type)
    {
        $this->db->select('*');
        $this->db->from('tbltasks');
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        return $this->db->get()->result_array();
    }

    public function deleteTaskAssigned($taskid)
    {
        $this->db->where('taskid', $taskid);
        return $this->db->delete('tbltask_assigned');
    }

    public function countProductionsGantt()
    {
        $productions_orders = $this->input->post('productions_orders');
        $conditionTasks = "(
            SELECT COUNT(*)
            FROM tbl_productions_orders_details
            INNER JOIN tbltasks ON tbltasks.rel_id = tbl_productions_orders_details.id AND tbltasks.rel_type = 'order_production_details'
            WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id
            LIMIT 1
        )";

        $this->db->from('tbl_productions_orders');
        // $this->db->where("($conditionTasks > 0)");
        $this->db->where('tbl_productions_orders.status_details', 1);
        if (!empty($productions_orders)) {
            $this->db->where("tbl_productions_orders.id", $productions_orders);
        }
        return $this->db->get()->num_rows();
    }

    public function loadGanttProductions($start, $limit)
    {
        $data = [];
        $productions_orders = $this->input->post('productions_orders');
        //Lấy danh sách đơn hàng tổng có tạo chi tiết và đã phân công việc
        $conditionTasks = "(
            SELECT COUNT(*)
            FROM tbl_productions_orders_details
            INNER JOIN tbltasks ON tbltasks.rel_id = tbl_productions_orders_details.id AND tbltasks.rel_type = 'order_production_details'
            WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id
            LIMIT 1
        )";

        $this->db->select("
            tbl_productions_orders.id as id,
            tbl_productions_orders.reference_no as reference_no,
        ", false);
        $this->db->from('tbl_productions_orders');
        $this->db->where('tbl_productions_orders.status_details', 1);
        // $this->db->where("($conditionTasks > 0)");
        $this->db->limit($limit, $start);
        $this->db->order_by('tbl_productions_orders.date DESC');
        if (!empty($productions_orders)) {
            $this->db->where("tbl_productions_orders.id", $productions_orders);
        }

        // print_arrays($this->db->get_compiled_select(), FALSE);
        $productions_orders = $this->db->get()->result_array();

        if (!empty($productions_orders)) {
            foreach ($productions_orders as $key => $value) {
                $productionOrderId = $value['id'];
                $referenceNo = $value['reference_no'];

                $productionOrder = [
                    'production_order_id' => $productionOrderId,
                    'values' => false,
                    'desc' => 'productions_orders',
                    'name' => $referenceNo,
                ];
                array_push($data, $productionOrder);

                //lấy danh sách lenh sản xuất chi tiết có phân công
                $conditionTasksForDetail = "(
                    SELECT COUNT(*)
                    FROM tbltasks
                    WHERE tbltasks.rel_id = tbl_productions_orders_details.id AND tbltasks.rel_type = 'order_production_details'
                    LIMIT 1
                )";
                $this->db->select('
                    tbl_productions_orders_details.id as id,
                    tbl_productions_orders_details.reference_no as reference_no,
                    tbl_productions_orders_details.date_created as date_created,
                    tbl_productions_orders_details.deadline as deadline,
                    tbl_productions_orders_details.status as status,
                    tbl_productions_orders_items.items_code as item_code,
                    tbl_productions_orders_items.items_name as items_name,
                    tbl_productions_orders_items.quantity as quantity,
                ');
                $this->db->from('tbl_productions_orders_details');
                $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
                $this->db->where('tbl_productions_orders_details.productions_orders_id', $productionOrderId);
                // $this->db->where("($conditionTasksForDetail > 0)");
                $productionOrderDetails = $this->db->get()->result_array();

                $warningDuePOD = get_option('warning_due_pod');
                if (!empty($productionOrderDetails)) {
                    foreach ($productionOrderDetails as $k => $val) {
                        $productionOrderDetailId = $val['id'];
                        $referenceDetailNo = $val['reference_no'];

                        $dateStart = strftime('%Y/%m/%d', strtotime($val['date_created']));
                        $dateEnd = strftime('%Y/%m/%d', strtotime($val['deadline']));
                        $product = $val['items_name'] . ' - ' . $val['item_code'];


                        $descPOD = '
                            <div>' . lang('tnh_reference_no') . ': ' . $referenceDetailNo . '</div>
                            <div>' . lang('date_start') . ': ' . _d($val['date_created']) . '</div>
                            <div>' . lang('date_end') . ': ' . _d($val['deadline']) . '</div>
                            <div>' . lang('tnh_product_code') . ': ' . $val['item_code'] . '</div>
                            <div>' . lang('tnh_product_name') . ': ' . $val['items_name'] . '</div>
                            <div>' . lang('quantity') . ': ' . $val['quantity'] . '</div>
                        ';

                        //handling status pod
                        $customClassPOD = 'ganttPrimary';
                        $statusPOD = $val['status'];
                        if ($statusPOD == "complete_production") {
                            //hoàn thành
                            $customClassPOD = 'ganttGray';
                        } else {
                            $dateDuePOD = minusDateNotFormat($val['deadline'], date('Y-m-d'));
                            if ($dateDuePOD < 0) {
                                //trễ hạn
                                $customClassPOD = 'ganttRed';
                                //
                            } else if ($dateDuePOD == 0) {
                                //Tới hạn
                                $customClassPOD = 'ganttGreen';
                            } else {
                                //sắp tới hạn
                                if (($dateDuePOD - $warningDuePOD) < 0) {
                                    $customClassPOD = 'ganttYellow';
                                }
                            }
                        }
                        //end handling status pod

                        $dataPOD = [
                            'production_order_detail_id' => $productionOrderDetailId,
                            'values' => [
                                [
                                    'from' => $dateStart,
                                    'to' => $dateEnd,
                                    'desc' => $descPOD,
                                    'label' => $referenceDetailNo,
                                    'customClass' => $customClassPOD,
                                    'dataObj' => [
                                        'production_order_detail_id' => $productionOrderDetailId
                                    ]
                                ]
                            ],
                            'desc' => '<b>' . $referenceDetailNo . '</b>',
                            'name' => '',
                        ];
                        array_push($data, $dataPOD);

                        //Lấy danh sách task trong lệnh sản xuất chi tiết
                        $this->db->select('
                            tbltasks.id as id,
                            tbltasks.name as name,
                            tbltasks.description as description,
                            tbltasks.startdate as startdate,
                            tbltasks.duedate as duedate,
                            tbltasks.status as status,
                        ');
                        $this->db->from('tbltasks');
                        $this->db->where('tbltasks.rel_id', $productionOrderDetailId);
                        $this->db->where('tbltasks.rel_type', 'order_production_details');
                        $tasks = $this->db->get()->result_array();
                        $values = [];

                        if (!empty($tasks)) {
                            foreach ($tasks as $i => $v) {
                                $taskId = $v['id'];
                                $dateStart = strftime('%Y/%m/%d', strtotime($v['startdate']));
                                $dateEnd = strftime('%Y/%m/%d', strtotime($v['duedate']));
                                $description = $v['description'];
                                // $label = $v['name'];
                                $label = $product;

                                //handling status tasks
                                $customClassTask = 'ganttPrimary';
                                $statusTask = $v['status'];
                                if ($statusTask == 5) {
                                    //hoàn thành
                                    $customClassTask = 'ganttGray';
                                } else {
                                    $dateDueTask = minusDateNotFormat($v['duedate'], date('Y-m-d'));
                                    if ($dateDueTask < 0) {
                                        //trễ hạn
                                        $customClassTask = 'ganttRed';
                                        //
                                    } else if ($dateDueTask == 0) {
                                        //Tới hạn
                                        $customClassTask = 'ganttGreen';
                                    } else {
                                        //sắp tới hạn
                                        if (($dateDueTask - $warningDuePOD) < 0) {
                                            $customClassTask = 'ganttYellow';
                                        }
                                    }
                                }
                                //end handling status tasks

                                $dataTask = [
                                    'task_id' => $taskId,
                                    'values' => [
                                        [
                                            'from' => $dateStart,
                                            'to' => $dateEnd,
                                            'desc' => $description,
                                            'label' => $label,
                                            'customClass' => $customClassTask,
                                            'dataObj' => [
                                                'task_id' => $taskId
                                            ]
                                        ]
                                    ],
                                    'desc' => '<div class="text-primary">&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-tag"></i> ' . $label . '</div>',
                                    'name' => '',
                                ];
                                array_push($data, $dataTask);
                            }
                        }
                    }
                }
            }
        }

        // print_arrays($data);

        return $data;
    }

    public function countOrders()
    {
        $order_id = $this->input->post('order_id');
        $this->db->from('tbl_orders');
        if (!empty($order_id)) {
            $this->db->where('tbl_orders.id', $order_id);
        }
        if (!$this->perViewGanntOrders) {
            $this->db->where('tbl_orders.created_by', get_staff_user_id());
        }
        return $this->db->get()->num_rows();
    }

    public function loadGanttOrders($start, $limit)
    {
        $data = [];
        $order_id = $this->input->post('order_id');
        $warningDue = get_option('warning_due_pod');
        $this->db->select("
            tbl_orders.id as id,
            tbl_orders.reference_no as reference_no,
            tbl_orders.total_quantity as total_quantity,
            tbl_orders.total_quantity_had_delivery as total_quantity_had_delivery,
            tbl_orders.date as date,
        ", false);
        $this->db->from('tbl_orders');
        $this->db->limit($limit, $start);
        if (!empty($order_id)) {
            $this->db->where('tbl_orders.id', $order_id);
        }
        if (!$this->perViewGanntOrders) {
            $this->db->where('tbl_orders.created_by', get_staff_user_id());
        }
        $this->db->order_by('tbl_orders.date DESC, tbl_orders.reference_no DESC');
        $orders = $this->db->get()->result_array();

        if (!empty($orders)) {
            foreach ($orders as $key => $value) {
                $orderId = $value['id'];
                $referenceNo = $value['reference_no'];
                $dateStart = strftime('%Y/%m/%d', strtotime($value['date']));

                $dataOrder = [
                    'order_id' => $orderId,
                    'values' => false,
                    'desc' => 'orders',
                    'name' => $referenceNo,
                ];
                array_push($data, $dataOrder);

                $flag = false;
                $customClass = 'ganttPrimary';
                if ($value['total_quantity'] == $value['total_quantity_had_delivery']) {
                    $customClass = 'ganttGray';
                    $flag = true;
                }

                $maxDateShipping = "(
                    SELECT
                        MAX(tbl_order_item_shippings.date_shipping)
                    FROM tbl_order_item_shippings
                    WHERE tbl_order_item_shippings.order_item_id = tbl_order_items.id
                )";

                $this->db->select("
                    tbl_order_items.id as id,
                    tbl_order_items.item_code as item_code,
                    tbl_order_items.item_name as item_name,
                    tbl_order_items.quantity as quantity,
                    tbl_order_items.quantity_delivery as quantity_delivery,
                    $maxDateShipping as max_date
                ", false);
                $this->db->from('tbl_order_items');
                $this->db->where('tbl_order_items.order_id', $orderId);
                $orderItems = $this->db->get()->result_array();
                if (!empty($orderItems)) {
                    foreach ($orderItems as $k => $val) {
                        $orderItemId = $val['id'];
                        $itemName = $val['item_name'];
                        $itemCode = $val['item_code'];
                        $quantity = $val['quantity'];
                        $maxDate = strftime('%Y/%m/%d', strtotime($val['max_date']));
                        $descOrderItem = $itemName . '(' . $itemCode . ')' . '(SL: ' . $quantity . ')';
                        $descriptionValues = '
                            <div>' . lang('tnh_reference_no') . ': ' . $referenceNo . '</div>
                            <div>' . lang('date_start') . ': ' . _d($value['date']) . '</div>
                            <div>' . lang('date_end') . ': ' . _d($val['max_date']) . '</div>
                            <div>' . lang('tnh_item_code') . ': ' . $itemCode . '</div>
                            <div>' . lang('tnh_item_name') . ': ' . $itemName . '</div>
                            <div>' . lang('quantity') . ': ' . $val['quantity'] . '</div>
                        ';

                        //handling status order
                        if (!$flag) {
                            if ($val['quantity'] == $val['quantity_delivery']) {
                                //giao hàng hoàn thành trên từng mặt hàng
                                $customClass = 'ganttGray';
                            } else {
                                $dateDue = minusDateNotFormat($val['max_date'], date('Y-m-d'));
                                // print_arrays($dateDue);
                                if ($dateDue < 0) {
                                    //trễ hạn
                                    $customClass = 'ganttRed';
                                } else if ($dateDue == 0) {
                                    //Tới hạn
                                    $customClass = 'ganttGreen';
                                } else {
                                    //sắp tới hạn
                                    if (($dateDue - $warningDue) < 0) {
                                        $customClass = 'ganttYellow';
                                    }
                                }
                            }
                        }
                        //end handling status order

                        $dataOrderItem = [
                            'order_item_id' => $orderItemId,
                            'values' => [
                                [
                                    'from' => $dateStart,
                                    'to' => $maxDate,
                                    'desc' => $descriptionValues,
                                    'label' => $descOrderItem,
                                    'customClass' => $customClass,
                                    'dataObj' => [
                                        'order_id' => $orderId
                                    ]
                                ]
                            ],
                            'desc' => '<div class="bold" data-toggle="tooltip" title="' . $descOrderItem . '">' . $descOrderItem . '</div>',
                            'name' => '',
                        ];
                        array_push($data, $dataOrderItem);

                        $this->db->select("
                            tbl_order_item_shippings.id as id,
                            tbl_order_item_shippings.date_shipping as date_shipping,
                            tbl_order_item_shippings.quantity_shipping as quantity_shipping,
                        ", false);
                        $this->db->from('tbl_order_item_shippings');
                        $this->db->where('tbl_order_item_shippings.order_item_id', $orderItemId);
                        $shippings = $this->db->get()->result_array();

                        if (!empty($shippings)) {
                            $quantityMinusDelivery = $val['quantity_delivery'];
                            foreach ($shippings as $i => $v) {
                                $orderItemShippingId = $v['id'];
                                $dateEnd = strftime('%Y/%m/%d', strtotime($v['date_shipping']));
                                $label = _d($v['date_shipping']) . ' - SL: ' . $v['quantity_shipping'];
                                $description = '
                                    <div>' . lang('tnh_reference_no') . ': ' . $referenceNo . '</div>
                                    <div>' . lang('date_start') . ': ' . _d($value['date']) . '</div>
                                    <div>' . lang('date_end') . ': ' . _d($v['date_shipping']) . '</div>
                                    <div>' . lang('tnh_item_code') . ': ' . $itemCode . '</div>
                                    <div>' . lang('tnh_item_name') . ': ' . $itemName . '</div>
                                    <div>' . lang('quantity') . ': ' . $v['quantity_shipping'] . '</div>
                                ';

                                $customClassSub = $customClass;
                                if ($customClassSub != 'ganttGray') {
                                    if ($quantityMinusDelivery > $v['quantity_shipping']) {
                                        //hoàn thành
                                        $customClassSub = 'ganttGray';
                                    } else {
                                        $dateDue = minusDateNotFormat($v['date_shipping'], date('Y-m-d'));
                                        if ($dateDue < 0) {
                                            //trễ hạn
                                            $customClassSub = 'ganttRed';
                                        } else if ($dateDue == 0) {
                                            //Tới hạn
                                            $customClassSub = 'ganttGreen';
                                        } else {
                                            //sắp tới hạn
                                            if (($dateDue - $warningDue) < 0) {
                                                $customClassSub = 'ganttYellow';
                                            }
                                        }
                                    }
                                }
                                $quantityMinusDelivery -= $v['quantity_shipping'];

                                $dataShipping = [
                                    'order_item_shipping_id' => $orderItemShippingId,
                                    'values' => [
                                        [
                                            'from' => $dateStart,
                                            'to' => $dateEnd,
                                            'desc' => $description,
                                            'label' => $label,
                                            'customClass' => $customClassSub,
                                            'dataObj' => [
                                                'order_id' => $orderId
                                            ]
                                        ]
                                    ],
                                    'desc' => '<div class="text-primary">&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-tag"></i> ' . $label . '</div>',
                                    'name' => '',
                                ];
                                array_push($data, $dataShipping);
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function insertInvoices($data)
    {
        $this->db->insert('tbl_invoices', $data);
        return $this->db->insert_id();
    }

    public function insertInvoiceItems($data)
    {
        $this->db->insert('tbl_invoice_items', $data);
        return $this->db->insert_id();
    }

    public function deleteInvoices($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_invoices');
    }

    public function deleteInvoiceItems($invoice_id)
    {
        $this->db->where('invoice_id', $invoice_id);
        return $this->db->delete('tbl_invoice_items');
    }

    public function rowInvoicesById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_invoices');
        $this->db->where('tbl_invoices.id', $id);
        return $this->db->get()->row_array();
    }

    public function getInvoiceItems($invoice_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_invoice_items');
        $this->db->where('tbl_invoice_items.invoice_id', $invoice_id);
        return $this->db->get()->result_array();
    }

    public function rowStaffById($id)
    {
        $this->db->select('tblstaff.firstname, tblstaff.lastname');
        $this->db->from('tblstaff');
        $this->db->where('tblstaff.staffid', $id);
        return $this->db->get()->row_array();
    }

    public function rowQuotesById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_quotes');
        $this->db->where('tbl_quotes.id', $id);
        return $this->db->get()->row_array();
    }

    public function getQuoteItemsByQuoteId($quote_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_quote_items');
        $this->db->where('quote_id', $quote_id);
        return $this->db->get()->result_array();
    }

    public function getStaffDepratmentsAndAdmin()
    {
        $user_id = get_staff_user_id();
        $this->db->select('tblstaff_departments.departmentid');
        $this->db->from('tblstaff_departments');
        $this->db->where('tblstaff_departments.staffid', $user_id);
        $departmentsStaff = $this->db->get()->result_array();

        $department = [];
        foreach ($departmentsStaff as $key => $value) {
            $department[] = $value['departmentid'];
        }

        $this->db->select("tblstaff.staffid, CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as name, tblstaff.firstname, tblstaff.lastname");
        $this->db->from('tblstaff');
        if (empty($department)) {
            $this->db->where("tblstaff.admin", 1);
        } else {
            $this->db->where("
                tblstaff.admin = 1 OR (
                    SELECT count(*)
                    FROM tblstaff_departments
                    WHERE tblstaff_departments.staffid = tblstaff.staffid AND tblstaff_departments.departmentid IN (" . implode(',', $department) . ")
                    LIMIT 1
                ) > 0
            ");
        }

        $this->db->where('tblstaff.active', 1);
        return $this->db->get()->result_array();
    }

    public function insertBatchEmployeeManageStaff($data)
    {
        return $this->db->insert_batch('tbl_employee_manage_staff', $data);
    }

    public function deleteEmployeeManageStaff($staff_id)
    {
        $this->db->where('staff_id', $staff_id);
        return $this->db->delete('tbl_employee_manage_staff');
    }

    public function getEmployeeManageStaffByStaffId($staff_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_employee_manage_staff');
        $this->db->where('tbl_employee_manage_staff.staff_id', $staff_id);
        return $this->db->get()->result_array();
    }

    public function getNotificationCustom()
    {
        return null;
        $user_id = get_staff_user_id();

        $conditionStaffQuotes = '';
        $conditionStaffOrders = '';
        $conditionStaffBusinessPlan = '';
        $conditionStaffDeliveries = '';
        $conditionStaffExportWarehouses = '';
        $conditionStaffProductionsPlan = '';
        $conditionStaffProductionsCapacity = '';
        $conditionStaffProductionsOrders = '';
        $conditionStaffSuggestExporting = '';
        $conditionStaffExportingProducion = '';
        $conditionStaffPurchaseProducts = '';
        $conditionStaffPurchaseInternal = '';
        $conditionStaffReturnedGoods = '';

        if (!is_admin()) {
            $conditionStaffQuotes = " AND (
                tbl_quotes.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_quotes.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffOrders = " AND (
                tbl_orders.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_orders.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffBusinessPlan = " AND (
                tbl_business_plan.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_business_plan.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffDeliveries = " AND (
                tbl_deliveries.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_deliveries.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffExportWarehouses = " AND (
                tbl_export_warehouses.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_export_warehouses.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffProductionsPlan = " AND (
                tbl_productions_plan.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_productions_plan.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffProductionsCapacity = " AND (
                tbl_productions_capacity.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_productions_capacity.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffProductionsOrders = " AND (
                tbl_productions_orders.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_productions_orders.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffSuggestExporting = " AND (
                tbl_suggest_exporting.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_suggest_exporting.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffExportingProducion = " AND (
                tbl_suggest_exporting.user_stock = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_suggest_exporting.user_stock AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffPurchaseProducts = " AND (
                tbl_purchase_products.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_purchase_products.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffPurchaseInternal = " AND (
                tbl_purchase_internal.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_purchase_internal.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";

            $conditionStaffReturnedGoods = " AND (
                tbl_returned_goods.created_by = $user_id OR (
                    SELECT COUNT(*)
                    FROM tbl_employee_manage_staff
                    WHERE tbl_employee_manage_staff.staff_id = tbl_returned_goods.created_by AND tbl_employee_manage_staff.employee_id = $user_id
                    LIMIT 1
                ) > 0
            )";
        }

        // UNION ALL

        // SELECT
        //     'deliveries' as type_cs,
        //     tbl_deliveries.id as id,
        //     tbl_deliveries.date as date,
        //     tbl_deliveries.reference_no as reference_no,
        //     CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by
        // FROM tbl_deliveries
        // LEFT JOIN tblstaff ON tblstaff.staffid = tbl_deliveries.created_by
        // WHERE tbl_deliveries.status = 'un_approved' $conditionStaffDeliveries

        $query = "
            SELECT
                'quotes' as type_cs,
                tbl_quotes.id as id,
                tbl_quotes.date as date,
                tbl_quotes.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_quotes
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_quotes.created_by
            WHERE tbl_quotes.status = 'un_approved' $conditionStaffQuotes

            UNION ALL

            SELECT
                'orders' as type_cs,
                tbl_orders.id as id,
                tbl_orders.date as date,
                tbl_orders.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_orders
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_orders.created_by
            WHERE tbl_orders.status = 'un_approved' $conditionStaffOrders

            UNION ALL

            SELECT
                'business_plan' as type_cs,
                tbl_business_plan.id as id,
                tbl_business_plan.date as date,
                tbl_business_plan.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_business_plan
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_business_plan.created_by
            WHERE tbl_business_plan.status = 'un_approved' $conditionStaffBusinessPlan

            UNION ALL

            SELECT
                'export_warehouses' as type_cs,
                tbl_export_warehouses.id as id,
                tbl_export_warehouses.date as date,
                tbl_export_warehouses.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_export_warehouses
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_export_warehouses.created_by
            WHERE tbl_export_warehouses.warehouseman_id = 0 $conditionStaffExportWarehouses

            UNION ALL

            SELECT
                'productions_plan' as type_cs,
                tbl_productions_plan.id as id,
                tbl_productions_plan.date as date,
                tbl_productions_plan.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_productions_plan
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_productions_plan.created_by
            WHERE tbl_productions_plan.status = 'un_approved' $conditionStaffProductionsPlan

            UNION ALL

            SELECT
                'productions_capacity' as type_cs,
                tbl_productions_capacity.id as id,
                tbl_productions_capacity.date as date,
                tbl_productions_capacity.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_productions_capacity
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_productions_capacity.created_by
            WHERE tbl_productions_capacity.status = 'un_approved' $conditionStaffProductionsCapacity

            UNION ALL

            SELECT
                'productions_orders' as type_cs,
                tbl_productions_orders.id as id,
                tbl_productions_orders.date as date,
                tbl_productions_orders.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_productions_orders
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_productions_orders.created_by
            WHERE tbl_productions_orders.status = 'un_approved' $conditionStaffProductionsOrders

            UNION ALL

            SELECT
                'list_suggest_exporting' as type_cs,
                tbl_suggest_exporting.id as id,
                tbl_suggest_exporting.date as date,
                tbl_suggest_exporting.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_suggest_exporting
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_suggest_exporting.created_by
            WHERE tbl_suggest_exporting.status = 'un_approved' AND tbl_suggest_exporting.type != 2 $conditionStaffSuggestExporting

            UNION ALL

            SELECT
                'exporting_producion' as type_cs,
                tbl_suggest_exporting.id as id,
                tbl_suggest_exporting.date_stock as date,
                tbl_suggest_exporting.reference_stock as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_suggest_exporting
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_suggest_exporting.created_by
            WHERE tbl_suggest_exporting.status_stock = 'un_approved_stock' $conditionStaffExportingProducion

            UNION ALL

            SELECT
                'purchase_products' as type_cs,
                tbl_purchase_products.id as id,
                tbl_purchase_products.date as date,
                tbl_purchase_products.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_purchase_products
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_purchase_products.created_by
            WHERE tbl_purchase_products.warehouseman_id = 0 $conditionStaffPurchaseProducts

            UNION ALL

            SELECT
                'purchase_internal' as type_cs,
                tbl_purchase_internal.id as id,
                tbl_purchase_internal.date as date,
                tbl_purchase_internal.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_purchase_internal
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_purchase_internal.created_by
            WHERE tbl_purchase_internal.status = 'un_approved' $conditionStaffPurchaseInternal

            UNION ALL

            SELECT
                'returned_goods' as type_cs,
                tbl_returned_goods.id as id,
                tbl_returned_goods.date as date,
                tbl_returned_goods.reference_no as reference_no,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
                tblstaff.staffid as staffid
            FROM tbl_returned_goods
            LEFT JOIN tblstaff ON tblstaff.staffid = tbl_returned_goods.created_by
            WHERE tbl_returned_goods.status = 'un_approved' $conditionStaffReturnedGoods

            ORDER BY date DESC
            LIMIT 30
        ";
        $result = $this->db->query($query)->result_array();
        return $result;
    }


    public function countProductionsGanttNews()
    {
        $productions_orders = $this->input->post('productions_orders');
        $conditionTasks = "(
            SELECT COUNT(*)
            FROM tbl_productions_orders_details
            INNER JOIN tbltasks ON tbltasks.rel_id = tbl_productions_orders_details.id AND tbltasks.rel_type = 'order_production_details'
            WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id
            LIMIT 1
        )";

        $this->db->from('tbl_productions_orders');
        // $this->db->where("($conditionTasks > 0)");
        $this->db->where('tbl_productions_orders.status_details', 1);
        if (!empty($productions_orders)) {
            $this->db->where("tbl_productions_orders.id", $productions_orders);
        }
        return $this->db->get()->num_rows();
    }

    public function loadGanttProductionsNews($start, $limit)
    {
        $data = [];
        $productions_orders = $this->input->post('productions_orders');

        $conditionReportStage = "(
            SELECT COUNT(*)
            FROM tbl_productions_orders_details
            INNER JOIN tbl_update_info_stage ON tbl_update_info_stage.productions_od_id = tbl_productions_orders_details.id
            WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id
            LIMIT 1
        )";

        $this->db->select("
            tbl_productions_orders.id as id,
            tbl_productions_orders.reference_no as reference_no,
        ", false);
        $this->db->from('tbl_productions_orders');
        $this->db->where('tbl_productions_orders.status_details', 1);
        $this->db->limit($limit, $start);
        $this->db->order_by('tbl_productions_orders.date DESC');
        if (!empty($productions_orders)) {
            $this->db->where("tbl_productions_orders.id", $productions_orders);
        }

        // $this->db->where("($conditionReportStage > 0)");

        // print_arrays($this->db->get_compiled_select(), FALSE);
        $productions_orders = $this->db->get()->result_array();

        if (!empty($productions_orders)) {
            foreach ($productions_orders as $key => $value) {
                $productionOrderId = $value['id'];
                $referenceNo = $value['reference_no'];

                $productionOrder = [
                    'production_order_id' => $productionOrderId,
                    'values' => false,
                    'desc' => 'productions_orders',
                    'name' => $referenceNo,
                ];
                array_push($data, $productionOrder);

                //lấy danh sách lenh sản xuất chi tiết có phân công
                $conditionTasksForDetail = "(
                    SELECT COUNT(*)
                    FROM tbltasks
                    WHERE tbltasks.rel_id = tbl_productions_orders_details.id AND tbltasks.rel_type = 'order_production_details'
                    LIMIT 1
                )";

                $conditionReportStageItem = "(
                    SELECT COUNT(*)
                    FROM tbl_update_info_stage
                    WHERE tbl_update_info_stage.productions_od_id = tbl_productions_orders_details.id
                    LIMIT 1
                )";

                $this->db->select('
                    tbl_productions_orders_details.id as id,
                    tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
                    tbl_productions_orders_details.reference_no as reference_no,
                    tbl_productions_orders_details.date_created as date_created,
                    tbl_productions_orders_details.deadline as deadline,
                    tbl_productions_orders_details.status as status,
                    tbl_productions_orders_items.items_code as item_code,
                    tbl_productions_orders_items.items_name as items_name,
                    tbl_productions_orders_items.quantity as quantity,
                ');
                $this->db->from('tbl_productions_orders_details');
                $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
                $this->db->where('tbl_productions_orders_details.productions_orders_id', $productionOrderId);
                // $this->db->where("($conditionTasksForDetail > 0)");
                // $this->db->where("($conditionReportStageItem > 0)");

                $productionOrderDetails = $this->db->get()->result_array();

                $warningDuePOD = get_option('warning_due_pod');
                if (!empty($productionOrderDetails)) {
                    foreach ($productionOrderDetails as $k => $val) {
                        $productionOrderDetailId = $val['id'];
                        $referenceDetailNo = $val['reference_no'];

                        $dateStart = strftime('%Y/%m/%d', strtotime($val['date_created']));
                        $dateEnd = strftime('%Y/%m/%d', strtotime($val['deadline']));
                        $product = $val['items_name'] . ' - ' . $val['item_code'];


                        $descPOD = '
                            <div>' . lang('tnh_reference_no') . ': ' . $referenceDetailNo . '</div>
                            <div>' . lang('date_start') . ': ' . _d($val['date_created']) . '</div>
                            <div>' . lang('date_end') . ': ' . _d($val['deadline']) . '</div>
                            <div>' . lang('tnh_product_code') . ': ' . $val['item_code'] . '</div>
                            <div>' . lang('tnh_product_name') . ': ' . $val['items_name'] . '</div>
                            <div>' . lang('quantity') . ': ' . $val['quantity'] . '</div>
                        ';

                        //handling status pod
                        $customClassPOD = 'ganttPrimary';
                        $statusPOD = $val['status'];
                        if ($statusPOD == "complete_production") {
                            //hoàn thành
                            $customClassPOD = 'ganttGray';
                        } else {
                            $dateDuePOD = minusDateNotFormat($val['deadline'], date('Y-m-d'));
                            if ($dateDuePOD < 0) {
                                //trễ hạn
                                $customClassPOD = 'ganttRed';
                                //
                            } else if ($dateDuePOD == 0) {
                                //Tới hạn
                                $customClassPOD = 'ganttGreen';
                            } else {
                                //sắp tới hạn
                                if (($dateDuePOD - $warningDuePOD) < 0) {
                                    $customClassPOD = 'ganttYellow';
                                }
                            }
                        }
                        //end handling status pod

                        $dataPOD = [
                            'production_order_detail_id' => $productionOrderDetailId,
                            'values' => [
                                [
                                    'from' => $dateStart,
                                    'to' => $dateEnd,
                                    'desc' => $descPOD,
                                    'label' => $referenceDetailNo,
                                    'customClass' => $customClassPOD,
                                    'dataObj' => [
                                        'production_order_detail_id' => $productionOrderDetailId
                                    ]
                                ]
                            ],
                            'desc' => '<b>' . $referenceDetailNo . '</b>',
                            'name' => '',
                        ];
                        array_push($data, $dataPOD);
                        $dateStartPOD = $dateStart;
                        $dateEndPOD = $dateEnd;
                        //Lấy danh sách giai đoạn trong lệnh sản xuất chi tiết
                        $productions_orders_item_id = $val['productions_orders_item_id'];

                        $this->db->select('
                            tbl_productions_orders_items_stages.id as id,
                            tbl_stages.name as stage_name,
                            tbl_productions_orders_items_stages.final_stage
                            ', false);
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'left');
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_item_id);
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_sub_id', 0);
                        // $this->db->order_by('tbl_productions_orders_items_stages.number DESC');
                        $stages = $this->db->get()->result_array();
                        $values = [];

                        // print_arrays($stages);

                        if (!empty($stages)) {
                            foreach ($stages as $i => $v) {
                                $final_stage = $v['final_stage'] == 1 ? '<span class="text-danger">(' . lang('tnh_final_stage') . ')</span>' : '';
                                $productions_ois_id = $v['id'];
                                // $dateStart = strftime('%Y/%m/%d', strtotime($v['startdate']));
                                // $dateEnd = strftime('%Y/%m/%d', strtotime($v['duedate']));
                                $description = '';
                                $label = $v['stage_name'] . $final_stage;

                                $customClassStage = 'ganttPrimary';

                                $dataStages = [
                                    'productions_ois_id' => $productions_ois_id,
                                    'desc' => '<div class="text-primary">&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> ' . $label . '</div>',
                                    'name' => '',
                                ];
                                array_push($data, $dataStages);

                                $this->db->select('
                                    CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as employee,
                                    tbl_update_info_stage.id as id,
                                    tbl_update_info_stage.date_start,
                                    tbl_update_info_stage.date_end,
                                    tbl_update_info_stage.quantity_bad,
                                    tbl_update_info_stage.quantity_success
                                ');
                                $this->db->from('tbl_update_info_stage');
                                $this->db->join('tblstaff', 'tblstaff.staffid = tbl_update_info_stage.employee_id', 'left');
                                $this->db->where('tbl_update_info_stage.productions_od_id', $productionOrderDetailId);
                                $this->db->where('tbl_update_info_stage.productions_ois_id', $productions_ois_id);
                                $this->db->order_by('tbl_update_info_stage.id ASC');
                                $updateInfoStage = $this->db->get()->result_array();

                                if (!empty($updateInfoStage)) {
                                    foreach ($updateInfoStage as $kk => $vv) {
                                        $employee = $vv['employee'];
                                        $infoStageId = $vv['id'];
                                        $dateStart = !empty($vv['date_start']) ? strftime('%Y/%m/%d', strtotime($vv['date_start'])) : $dateStartPOD;
                                        $dateEnd = !empty($vv['date_end']) ? strftime('%Y/%m/%d', strtotime($vv['date_end'])) : date('Y/m/d');

                                        $description = '
                                            <div>' . lang('tnh_employees') . ': ' . $employee . '</div>
                                            <div>' . lang('date_start') . ': ' . _d($vv['date_start']) . '</div>
                                            <div>' . lang('date_end') . ': ' . _d($vv['date_end']) . '</div>
                                            <div>' . lang('tnh_quantity_success') . ': ' . formatNumber($vv['quantity_success']) . '</div>
                                            <div>' . lang('tnh_quantity_bad') . ': ' . formatNumber($vv['quantity_bad']) . '</div>
                                        ';
                                        $label = $vv['quantity_success'];

                                        $customClassStage = 'ganttPrimary';

                                        $dataUp = [
                                            'infoStageId' => $infoStageId,
                                            'values' => [
                                                [
                                                    'from' => $dateStart,
                                                    'to' => $dateEnd,
                                                    'desc' => $description,
                                                    'label' => $employee . '(' . $label . ')',
                                                    'customClass' => $customClassStage,
                                                    'dataObj' => [
                                                        'infoStageId' => $infoStageId
                                                    ]
                                                ]
                                            ],
                                            'desc' => '<div class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="">' . (++$kk) . '</i>. ' . $employee . '(SL:' . $label . ')' . '</div>',
                                            'name' => '',
                                        ];
                                        array_push($data, $dataUp);
                                    }
                                }

                                /*//Lấy danh sách các cập nhật cuối cùng trong giai đoạn
                                $this->db->select('
                                    CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as employee,
                                    tbl_update_info_stage.id as id,
                                    tbl_update_info_stage.date_start,
                                    tbl_update_info_stage.date_end,
                                    tbl_update_info_stage.quantity_bad,
                                    tbl_update_info_stage.quantity_success
                                ');
                                $this->db->from('tbl_update_info_stage');
                                $this->db->join('tblstaff', 'tblstaff.staffid = tbl_update_info_stage.employee_id', 'left');
                                $this->db->where('tbl_update_info_stage.productions_od_id', $productionOrderDetailId);
                                $this->db->where('tbl_update_info_stage.productions_ois_id', $productions_ois_id);
                                $this->db->order_by('tbl_update_info_stage.id DESC');
                                $this->db->limit(1);
                                $updateInfoStage = $this->db->get()->result_array();
                                // array_push($data, $dataStages);
                                if (!empty($updateInfoStage)) {
                                    array_push($data, $dataStages);
                                    break;
                                    foreach ($updateInfoStage as $kk => $vv) {
                                        $employee = $vv['employee'];
                                        $infoStageId = $vv['id'];
                                        $dateStart = strftime('%Y/%m/%d', strtotime($vv['date_start']));
                                        $dateEnd = strftime('%Y/%m/%d', strtotime($vv['date_end']));
                                        $description = '
                                            <div>'.lang('tnh_employees').': '.$employee.'</div>
                                            <div>'.lang('date_start').': '._d($vv['date_start']).'</div>
                                            <div>'.lang('date_end').': '._d($vv['date_end']).'</div>
                                            <div>'.lang('tnh_quantity_success').': '.formatNumber($vv['quantity_success']).'</div>
                                            <div>'.lang('tnh_quantity_bad').': '.formatNumber($vv['quantity_bad']).'</div>
                                        ';
                                        $label = $vv['quantity_success'];

                                        $customClassStage = 'ganttPrimary';

                                        $dataUp = [
                                            'infoStageId' => $infoStageId,
                                            'values' => [
                                                [
                                                    'from' => $dateStart,
                                                    'to' => $dateEnd,
                                                    'desc' => $description,
                                                    'label' => $label,
                                                    'customClass' => $customClassStage,
                                                    'dataObj' => [
                                                        'infoStageId' => $infoStageId
                                                    ]
                                                ]
                                            ],
                                            'desc' => '<div class="text-warning">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-ellipsis-h"></i> '.$label.'</div>',
                                            'name' => '',
                                        ];
                                        array_push($data, $dataUp);
                                    }
                                }*/
                            }
                        }
                    }
                }
            }
        }

        // print_arrays($data);

        return $data;
    }

    public function rowContact($id)
    {
        $this->db->select('*');
        $this->db->from('tblcontacts');
        $this->db->where('tblcontacts.id', $id);
        return $this->db->get()->row_array();
    }

    public function rowContactLead($id)
    {
        $this->db->select('*');
        $this->db->from('tblcontacts_lead');
        $this->db->where('tblcontacts_lead.id', $id);
        return $this->db->get()->row_array();
    }

    public function getQuotesNoteDefault()
    {
        $this->db->select('*');
        $this->db->from('tbl_quotes_note_default');
        $this->db->order_by('tbl_quotes_note_default.note ASC');
        return $this->db->get()->result_array();
    }

    public function insertNotificationForm($data = [])
    {
        $this->db->insert('tbl_notification_form', $data);
        return $this->db->insert_id();
    }

    public function updateNotificationForm($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_notification_form', $data);
    }

    public function rowNotificationForm($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_notification_form');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function deleteNotificationForm($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_notification_form');
    }

    public function getNotificationFormByType($type)
    {
        $this->db->select('*');
        $this->db->from('tbl_notification_form');
        $this->db->where('tbl_notification_form.type', $type);
        $this->db->order_by('tbl_notification_form.name ASC');
        return $this->db->get()->result_array();
    }

    public function getNotificationFormText($arrId = [])
    {
        $this->db->select('GROUP_CONCAT(tbl_notification_form.name SEPARATOR ",") as note_default');
        $this->db->from('tbl_notification_form');
        $this->db->where_in('tbl_notification_form.id', $arrId);
        // print_arrays($this->db->get_compiled_select(), FALSE);

        return $this->db->get()->row_array();
    }

    public function checkNotificationForm($id)
    {
        $this->db->from('tbl_orders');
        $this->db->where("FIND_IN_SET($id, tbl_orders.noti_phone) > 0");
        $this->db->or_where("FIND_IN_SET($id, tbl_orders.noti_email) > 0");
        $this->db->or_where("FIND_IN_SET($id, tbl_orders.noti_zalo) > 0");
        $this->db->or_where("FIND_IN_SET($id, tbl_orders.noti_note_other) > 0");
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function rowDeliveryArea($city, $district)
    {
        $this->db->select('tbldelivery_area.*');
        $this->db->from('tbldelivery_area');
        $this->db->join('tbldelivery_area_detail', 'tbldelivery_area_detail.id_delivery_area = tbldelivery_area.id');
        $this->db->where('tbldelivery_area.city', $city);
        $this->db->where('tbldelivery_area_detail.id_district', $district);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getZaloClient($client_id)
    {
        $this->db->select('tblclient_value.value as zalo');
        $this->db->from('tblclient_info_detail');
        $this->db->join('tblclient_value', 'tblclient_value.id_detail = tblclient_info_detail.id');
        $this->db->where('tblclient_info_detail.slug', 'zalo');
        $this->db->where('tblclient_value.client', $client_id);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function rowProvince($id)
    {
        $this->db->select('*');
        $this->db->from('tblprovince');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function searchProvince($term, $limit)
    {
        $this->db->select('tblprovince.provinceid as id, tblprovince.name as text', false);
        $this->db->from('tblprovince');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tblprovince.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function rowDistrict($id)
    {
        $this->db->select('*');
        $this->db->from('tbldistrict');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function searchDistrictByProvince($term, $limit, $provinceid)
    {
        $this->db->select('tbldistrict.districtid as id, tbldistrict.name as text', false);
        $this->db->from('tbldistrict');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbldistrict.name', $term);
            $this->db->group_end();
        }
        $this->db->where('tbldistrict.provinceid', $provinceid);
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertTransferWarehouse($data = [])
    {
        $this->db->insert('tbltransfer_warehouse', $data);
        return $this->db->insert_id();
    }

    public function insertTransferWarehouseDetail($data = [])
    {
        $this->db->insert('tbltransfer_warehouse_detail', $data);
        return $this->db->insert_id();
    }

    public function getTransferByOursource($id_source = '')
    {
        $this->db->select('tbltransfer_warehouse.id as id_tranfer,tbltransfer_warehouse.warehouse_id as id_warehouse,tbltransfer_warehouse_detail.*');
        $this->db->from('tbltransfer_warehouse');
        $this->db->where('tbltransfer_warehouse.outsource_id', $id_source);
        $this->db->join('tbltransfer_warehouse_detail', 'tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id', 'left');
        return $this->db->get()->result_array();
    }

    public function insertExportDiffWarehouse($data = [])
    {
        $this->db->insert('tblexport_different', $data);
        return $this->db->insert_id();
    }

    public function insertExportDiffWarehouseDetail($data = [])
    {
        $this->db->insert('tbltblexport_different_items', $data);
        return $this->db->insert_id();
    }

    public function insertWarehouse($data = [])
    {
        $this->db->insert('tblwarehouse', $data);
        return $this->db->insert_id();
    }

    public function insertWarehouseItems($data = [])
    {
        $this->db->insert('tblwarehouse_items', $data);
        return $this->db->insert_id();
    }

    public function checkWarehouseSupplier($warehouse_id, $supplier_id)
    {
        $this->db->from('tblwarehouse');
        $this->db->where('tblwarehouse.id', $warehouse_id);
        $this->db->where('tblwarehouse.supplier_id', $supplier_id);
        return $this->db->get()->num_rows();
    }

    public function createWarehouseSupplier($supplier_id)
    {
        //check exist warehouse
        $this->db->select('*');
        $this->db->from('tblwarehouse');
        $this->db->where('supplier_id', $supplier_id);
        $rs = $this->db->get()->row_array();

        if (!empty($rs)) {
            return $rs['id'];
        } else {
            $supplier = $this->rowSupplier($supplier_id);
            $this->db->select('tblgroup_warehouse.id');
            $this->db->from('tblgroup_warehouse');
            $this->db->where('tblgroup_warehouse.type', 'outsource');
            $categoryWS = $this->db->get()->row_array();
            $category_id = $categoryWS['id'];
            $code = $supplier['prefix'] . $supplier['code'];
            $name = lang('tnh_ws') . ' ' . $supplier['company'];

            return $this->insertWarehouse([
                'id_group_warehouse' => $category_id,
                'code' => $code,
                'name' => $name,
                'address' => '',
                'note' => '',
                'supplier_id' => $supplier_id,
            ]);
        }
    }

    public function createDefaultLocationWarehouse($warehouse_id)
    {
        $this->db->select('tbllocaltion_warehouses.id as id');
        $this->db->from('tbllocaltion_warehouses');
        $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
        //        $this->db->where('tbllocaltion_warehouses.code', 'default');
        $this->db->where('tbllocaltion_warehouses.type_excel', 1);
        $this->db->limit(1);
        $warehouseLocation = $this->db->get()->row_array();
        if (empty($warehouseLocation)) {
            $dataLW = [
                'name' => lang('default'),
                'code' => 'default',
                'warehouse' => $warehouse_id,
                'id_parent' => 0,
                'name_parent' => lang('default'),
                'child' => 1,
                'create_by' => get_staff_user_id(),
                'date_create' => date('Y-m-d H:i:s'),
                'status' => 0,
                'lever' => 1,
                'type_excel' => 1,
            ];
            $this->db->insert('tbllocaltion_warehouses', $dataLW);
            $locationWId = $this->db->insert_id();
        } else {
            $locationWId = $warehouseLocation['id'];
        }
        return $locationWId;
    }

    public function createDefaultWarehouseItems($data = [])
    {
        $warehouse_id = $data['warehouse_id'];
        $location_id = $data['location_id'];
        $type_items = $data['type_item'];
        $id_items = $data['item_id'];
        $this->db->from('tblwarehouse_items');
        $this->db->where('id_items', $id_items);
        $this->db->where('warehouse_id', $warehouse_id);
        $this->db->where('localtion', $location_id);
        $this->db->where('type_items', $type_items);
        $rs = $this->db->get()->num_rows();
        if (empty($rs)) {
            return $this->insertWarehouseItems([
                'id_items' => $id_items,
                'warehouse_id' => $warehouse_id,
                'localtion' => $location_id,
                'product_quantity' => 0,
                'type_items' => $type_items,
            ]);
        } else {
            return true;
        }
    }

    public function rowTranferWarehouseById($id)
    {
        $this->db->select('*');
        $this->db->from('tbltransfer_warehouse');
        $this->db->where('tbltransfer_warehouse.id', $id);
        return $this->db->get()->row_array();
    }

    public function insertOrdersWorkflow($data = [])
    {
        $this->db->insert('tbl_orders_workflow', $data);
        return $this->db->insert_id();
    }

    public function rowOrdersWorkflow($workflow_id, $order_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_orders_workflow');
        $this->db->where('tbl_orders_workflow.workflow_id', $workflow_id);
        $this->db->where('tbl_orders_workflow.order_id', $order_id);
        return $this->db->get()->row_array();
    }

    public function getOrdersWorkflow($order_id)
    {
        $this->db->select('tbl_orders_workflow.*, tblstaff.firstname, tblstaff.lastname');
        $this->db->from('tbl_orders_workflow');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_orders_workflow.created_by', 'left');
        $this->db->where('order_id', $order_id);
        return $this->db->get()->result_array();
    }

    public function deleteOrdersWorkflowByOrderId($order_id)
    {
        $this->db->where('order_id', $order_id);
        return $this->db->delete('tbl_orders_workflow');
    }

    public function getTicketsByOrderId($order_id)
    {
        $tags = "(SELECT GROUP_CONCAT(name SEPARATOR ',') FROM  tbltaggables JOIN tbltags ON tbltaggables.tag_id = tbltags.id WHERE rel_id = tbltickets.ticketid and rel_type='ticket' ORDER by tag_order ASC)";

        $this->db->select("tbltickets.*, $tags as tags, tbldepartments.name as name_deparment");
        $this->db->from('tbltickets');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->where('order_id', $order_id);
        return $this->db->get()->result_array();
    }

    public function getTaggables($ticketid)
    {
        $this->db->select('GROUP_CONCAT(tbltags.name SEPARATOR ",") as tags');
        $this->db->from('tbltaggables');
        $this->db->join('tbltags', 'tbltags.id = tbltaggables.tag_id');
        $this->db->where('tbltaggables.rel_id', $ticketid);
        $this->db->where('tbltaggables.rel_type', 'ticket');
        return $this->db->get()->row_array();
    }

    public function getOrderByReferenceNo($reference_no)
    {
        $this->db->select('*');
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.reference_no', $reference_no);
        return $this->db->get()->row_array();
    }

    public function getLocationWarehouse($warehouse, $child = false)
    {
        $this->db->select('*');
        $this->db->from('tbllocaltion_warehouses');
        $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse);
        if (!empty($child)) {
            $this->db->where('tbllocaltion_warehouses.child', $child);
        }
        return $this->db->get()->result_array();
    }

    public function rowTranferByImportOutsourceId($import_outsource_id)
    {
        $this->db->select('*');
        $this->db->from('tbltransfer_warehouse');
        $this->db->where('tbltransfer_warehouse.import_outsource_id', $import_outsource_id);
        return $this->db->get()->row_array();
    }

    public function getQuantityWarehouseItems($warehouse_id, $id_items, $type_items)
    {
        $this->db->select('SUM(tblwarehouse_items.product_quantity) as total_quantity', false);
        $this->db->from('tblwarehouse_items');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        $this->db->where('tblwarehouse_items.type_items', $type_items);

        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->row_array();
    }

    public function rowStagesById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_stages');
        $this->db->where('tbl_stages.id', $id);
        return $this->db->get()->row_array();
    }

    public function getNotifiProductionDetailDeadline($options)
    {
        $setLeadTime = 3;
        $date = date('Y-m-d');

        $this->db->select("
            tbl_productions_orders_details.id as id,
            tbl_productions_orders_details.reference_no,
            tbl_productions_orders_items.items_name,
            tbl_productions_orders_items.items_code,
            tbl_productions_orders_items.quantity,
            tbl_products.images as images,
            tbl_productions_orders_details.deadline,
            tblstaff.id_zalo
        ", false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'left');

        $this->db->join('tbl_pod_employees', 'tbl_pod_employees.productions_orders_details_id = tbl_productions_orders_details.id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_pod_employees.employee_id AND tblstaff.id_zalo != ""', 'inner');

        $this->db->where('tbl_productions_orders_details.status !=', 'complete_production');

        if ($options == "due_soon") {
            $this->db->where("(DATEDIFF(DATE_FORMAT(tbl_productions_orders_details.deadline, '%Y-%m-%d'), '$date') BETWEEN '0' AND '3')");
        } else if ($options == "due") {
            $this->db->where('DATE_FORMAT(tbl_productions_orders_details.deadline, "%Y-%m-%d") =', $date);
        } else if ($options = "out_of_date") {
            $this->db->where('DATE_FORMAT(tbl_productions_orders_details.deadline, "%Y-%m-%d") <', $date);
        } else {
            return false;
        }
        return $this->db->get()->result_array();
    }

    public function getNotiMaterial()
    {
        $this->db->select("
            tbl_productions_orders_details.id as id,
            tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
            tbl_productions_orders_details.reference_no,
            tbl_productions_orders_items.items_name,
            tbl_productions_orders_items.items_code,
            tbl_productions_orders_items.quantity,
            tbl_products.images as images,
            DATE_FORMAT(tbl_productions_orders_details.date_created, '%Y-%m-%d') as date_created,
            tblstaff.id_zalo
        ", false);

        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'left');

        $this->db->join('tbl_pod_employees', 'tbl_pod_employees.productions_orders_details_id = tbl_productions_orders_details.id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_pod_employees.employee_id AND tblstaff.id_zalo != ""', 'inner');

        $this->db->where('tbl_productions_orders_details.status !=', 'complete_production');
        $pod = $this->db->get()->result_array();
        return $pod;
    }

    public function getTaskAssignedNotStaffId($taskid, $staffid)
    {

        $staffTask = "(
            SELECT custom.staffid
            FROM (
                SELECT tbltask_assigned.staffid as staffid
                FROM tbltask_assigned
                WHERE tbltask_assigned.taskid = $taskid AND tbltask_assigned.notif_zalo = 1
                UNION ALL
                SELECT tbltask_followers.staffid as staffid
                FROM tbltask_followers
                WHERE tbltask_followers.taskid = $taskid AND tbltask_followers.notif_zalo = 1
            ) as custom
            GROUP BY custom.staffid
        ) as staff_task";

        $this->db->select('tblstaff.id_zalo');
        $this->db->from('tblstaff');
        $this->db->join($staffTask, 'staff_task.staffid = tblstaff.staffid');
        $this->db->where('tblstaff.staffid !=', $staffid);
        $this->db->where('(tblstaff.id_zalo IS NOT null AND tblstaff.id_zalo != "")');
        $this->db->group_by('tblstaff.staffid');
        return $this->db->get()->result_array();
    }

    public function rowNotifiZalo($taskid, $staffid)
    {
        $staffTask = "(
            SELECT custom.staffid, custom.notif_zalo
            FROM (
                SELECT tbltask_assigned.staffid as staffid, tbltask_assigned.notif_zalo as notif_zalo
                FROM tbltask_assigned
                WHERE tbltask_assigned.taskid = $taskid
                UNION ALL
                SELECT tbltask_followers.staffid as staffid, tbltask_followers.notif_zalo as notif_zalo
                FROM tbltask_followers
                WHERE tbltask_followers.taskid = $taskid
            ) as custom
            GROUP BY custom.staffid
        ) as staff_task";

        $this->db->select('staff_task.notif_zalo');
        $this->db->from('tblstaff');
        $this->db->join($staffTask, 'staff_task.staffid = tblstaff.staffid');
        $this->db->where('tblstaff.staffid', $staffid);
        $this->db->group_by('tblstaff.staffid');
        return $this->db->get()->row_array();
    }

    public function rowTasks($task_id)
    {
        $this->db->select('*');
        $this->db->from('tbltasks');
        $this->db->where('tbltasks.id', $task_id);
        return $this->db->get()->row_array();
    }

    public function getPurchaseItems($purchase_id)
    {
        $this->db->select('tblpurchases_items.*');
        $this->db->from('tblpurchases_items');
        $this->db->where('tblpurchases_items.purchases_id', $purchase_id);
        return $this->db->get()->result_array();
    }

    public function getQuantityImportPurchase($purchase_id, $type, $product_id)
    {
        $this->db->select('SUM(tblimport_items.quantity_net) as quantity_net', false);
        $this->db->from('tblpurchase_order');
        $this->db->join('tblimport', 'tblimport.id_order = tblpurchase_order.id');
        $this->db->join('tblimport_items', 'tblimport_items.id_import = tblimport.id');
        $this->db->group_start();
        $this->db->where('tblpurchase_order.id_purchase_proce', $purchase_id);
        $this->db->or_where("FIND_IN_SET($purchase_id, tblpurchase_order.id_purchases)");
        $this->db->group_end();
        $this->db->where('tblimport_items.type', $type);
        $this->db->where('tblimport_items.product_id', $product_id);
        $this->db->where('tblimport.warehouseman_id >', 0);
        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->row_array();
    }

    public function getImportByIdOrder($id_order)
    {
        $this->db->select('tblimport.id_order, tblimport.id, tblimport.prefix, tblimport.code');
        $this->db->from('tblimport');
        $this->db->where('tblimport.id_order', $id_order);
        return $this->db->get()->result_array();
    }

    public function getTotalQuantityWarehouseItems($id_items, $type_items)
    {
        $this->db->select('SUM(tblwarehouse_items.product_quantity) as total_quantity', false);
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tbllocaltion_warehouses.status !=', 2);
        return $this->db->get()->row_array();
    }

    public function getTotalQuantityWarehouseItemsDelivery($id_items, $type_items)
    {
        $this->db->select('SUM(tblwarehouse_items.product_quantity) as total_quantity', false);
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        return $this->db->get()->row_array();
    }

    public function getWarehouseProductLIFO_FiFO($itemType, $itemId)
    {
        $this->db->select('tblwarehouse_product.quantity_left, tblwarehouse_product.price');
        $this->db->from('tblwarehouse_product');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_product.warehouse_id');
        $this->db->where('tblwarehouse_product.type_items', $itemType);
        $this->db->where('tblwarehouse_product.product_id', $itemId);
        $this->db->where('tblwarehouse_product.quantity_left >', 0);
        $this->db->where('tblwarehouse.supplier_id', 0);
        $this->db->order_by('tblwarehouse_product.date_warehouse ASC');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function getPriceLast($itemType, $itemId)
    {
        $this->db->select('tblwarehouse_product.quantity_left, tblwarehouse_product.price');
        $this->db->from('tblwarehouse_product');
        $this->db->where('tblwarehouse_product.type_items', $itemType);
        $this->db->where('tblwarehouse_product.product_id', $itemId);
        // $this->db->where('tblwarehouse_product.quantity_left >', 0);
        $this->db->order_by('tblwarehouse_product.date_warehouse DESC');
        $this->db->limit(1);
        $result = $this->db->get()->row_array();
        return $result;
    }

    public function getOrdersItemSellFirst($itemId, $itemTypeTerm, $orderId = 0)
    {
        $this->db->select('tbl_order_items.price');
        $this->db->from('tbl_orders');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');
        $this->db->where('tbl_order_items.item_id', $itemId);
        $this->db->where('tbl_order_items.type_item', $itemTypeTerm);
        if (!empty($orderId)) {
            $this->db->where('tbl_orders.id !=', $orderId);
        }
        $this->db->order_by('tbl_orders.date DESC');
        $this->db->limit(1);
        $rs = $this->db->get()->row_array();
        return $rs;
    }

    //
    public function countNotApproveQuotes()
    {
        $perViewQuotes = has_permission('quotes', '', 'view');
        $perViewOwnQuotes = has_permission('quotes', '', 'view_own');
        if (empty($perViewQuotes) && empty($perViewOwnQuotes)) return 0;
        $this->db->from('tbl_quotes');
        $this->db->where('tbl_quotes.status', 'un_approved');
        if (!$perViewQuotes) {
            $this->db->where('tbl_quotes.created_by', get_staff_user_id());
        }
        return $this->db->get()->num_rows();
    }

    public function countNotApproveOrders()
    {
        $perViewOrders = has_permission('orders', '', 'view');
        $perViewOwnOrders = has_permission('orders', '', 'view_own');
        if (empty($perViewOrders) && empty($perViewOwnOrders)) return 0;
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.status', 'un_approved');
        if (!$perViewOrders) {
            $this->db->where('tbl_orders.created_by', get_staff_user_id());
        }
        return $this->db->get()->num_rows();
    }

    public function countNotReturnedGoods()
    {
        $this->db->from('tbl_returned_goods');
        $this->db->where('tbl_returned_goods.status', 'un_approved');
        return $this->db->get()->num_rows();
    }

    public function countNotBusinessPlan()
    {
        $perViewBusinessPlan = has_permission('business_plan', '', 'view');
        $perViewOwnBusinessPlan = has_permission('business_plan', '', 'view_own');
        if (empty($perViewBusinessPlan) && empty($perViewOwnBusinessPlan)) return 0;
        $this->db->from('tbl_business_plan');
        $this->db->where('tbl_business_plan.status', 'un_approved');
        if (!$perViewBusinessPlan) {
            $this->db->where('tbl_business_plan.created_by', get_staff_user_id());
        }
        return $this->db->get()->num_rows();
    }

    //08-01-2020
    public function rowWarehouseProduct($id)
    {
        $this->db->select('*');
        $this->db->from('tblwarehouse_product');
        $this->db->where('tblwarehouse_product.id', $id);
        return $this->db->get()->row_array();
    }
    //

    public function getClientByZcodeOrCompany($customer)
    {
        $this->db->select('tblclients.userid, tblclients.company');
        $this->db->from('tblclients');
        $this->db->group_start();
        $this->db->where('tblclients.company', $customer);
        $this->db->or_where('tblclients.zcode', $customer);
        $this->db->or_where('tblclients.company_short', $customer);
        $this->db->group_end();
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getShippingClientByClientAndAddress($customer_id, $address)
    {
        $this->db->select('tblshipping_client.id, tblshipping_client.address');
        $this->db->from('tblshipping_client');
        $this->db->where('tblshipping_client.client', $customer_id);
        $this->db->where('tblshipping_client.address', $address);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getStaffByName($name)
    {
        $this->db->select('tblstaff.staffid');
        $this->db->from('tblstaff');
        $this->db->where('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) =', $name);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getItemsByCode($code)
    {
        $this->db->select('tblitems.id, tblitems.code, tblitems.name');
        $this->db->from('tblitems');
        $this->db->where('tblitems.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getProductsByCode($code)
    {
        $this->db->select('tbl_products.id, tbl_products.code, tbl_products.name, tbl_products.unit_id, tbl_products.conversion_unit, tbl_products.conversion_quantity_unit, tbl_products.quantity_child_sheet, tbl_products.quantity_sheet_bale as quantity_sheet_bale, tbl_products.loss as loss, tbl_products.price_import as price_import');
        $this->db->from('tbl_products');
        $this->db->where('tbl_products.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getTaxesByName($name)
    {
        $this->db->select('*');
        $this->db->from('tbltaxes');
        $this->db->where('tbltaxes.name', $name);
        return $this->db->get()->row_array();
    }

    public function getContractByFirstName($firstname, $id_customer)
    {
        $this->db->select('tblcontacts.id as id');
        $this->db->from('tblcontacts');
        $this->db->where('tblcontacts.firstname', $firstname);
        $this->db->where('userid', $id_customer);
        return $this->db->get()->row_array();
    }

    public function getTransportByName($name)
    {
        $this->db->select('tblsuppliers.id as id');
        $this->db->from('tblsuppliers');
        $this->db->where('tblsuppliers.company', $name);
        $this->db->where('tblsuppliers.type', 1);
        return $this->db->get()->row_array();
    }

    public function rowPaymentMode($id)
    {
        $this->db->select('*');
        $this->db->from('tblpayment_modes');
        $this->db->where('tblpayment_modes.id', $id);
        return $this->db->get()->row_array();
    }

    public function getLocationWarehouseQuantity($warehouse_id, $id_items, $type_items)
    {
        $this->db->select("
            CONCAT($warehouse_id, '__', tblwarehouse_items.localtion) as id,
            tbllocaltion_warehouses.code as code_location,
            tbllocaltion_warehouses.name as name_location,
            tblwarehouse_items.product_quantity as quantity_warehouse,
        ");
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tblwarehouse_items.localtion = tbllocaltion_warehouses.id');
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        return $this->db->get()->result_array();
    }

    public function getLocationWarehouseQuantityNew($warehouse_id, $id_items, $type_items, $order_id = 0)
    {

        $tb_tranfer_bussiness = "(
            SELECT 
                tbl_productions_orders_details.id as productions_orders_details_id,
                SUM(tbl_tranfer_business_item.quantity) as quantity_tranfer_business
            FROM tbl_productions_orders_items
            JOIN tbl_tranfer_business_item ON tbl_tranfer_business_item.business_plan_item_id = tbl_productions_orders_items.production_plan_item_id 
            JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id 
            AND tbl_productions_orders_items.object_item_type = 'business_plan'
            GROUP BY tbl_productions_orders_details.id
        ) tb_tranfer_bussiness";

        $this->db->select("
            CONCAT($warehouse_id, '__', tblwarehouse_items.localtion) as id,
            tbllocaltion_warehouses.code as code_location,
            tbllocaltion_warehouses.name as name_location,
            tblwarehouse_items.product_quantity as quantity_warehouse,
            tblwarehouse_items.lot_code as lot,
            tblwarehouse_items.date_sx as date_sx,
            tblwarehouse_items.date_sd as date_sd,
            tblwarehouse_items.date_use as date_use,
        ");
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tblwarehouse_items.localtion = tbllocaltion_warehouses.id');
        $this->db->join($tb_tranfer_bussiness, 'tb_tranfer_bussiness.productions_orders_details_id = tbllocaltion_warehouses.pod_id', 'left');
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tbllocaltion_warehouses.stage_id', 0);
        $this->db->where('tbllocaltion_warehouses.productions_plan_id', 0);
        $this->db->where('tblwarehouse_items.product_quantity >', 0);
        $this->db->where('(tbllocaltion_warehouses.order_id != 0 OR tbllocaltion_warehouses.pod_id != 0)');
        $this->db->group_start();
        if (!empty($order_id)) {
            $this->db->where('tbllocaltion_warehouses.order_id', $order_id);
            $this->db->or_where('EXISTS (
                SELECT tbl_productions_orders_details.id
                FROM tbl_productions_orders_details
                WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
                AND tbl_productions_orders_details.object_id = ' . $order_id . ' AND tbl_productions_orders_details.object_type = "orders"
            )');
        }
        $this->db->group_end();
        return $this->db->get()->result_array();
    }

    public function getLocationWarehouseQuantityLotDate($warehouse_id, $id_items, $type_items)
    {
        $this->db->select("
            CONCAT(tblwarehouse_items.warehouse_id, '__', tblwarehouse_items.localtion, '__', coalesce(tblwarehouse_items.lot_code, 'NULL'), '__', coalesce(tblwarehouse_items.date_sx, 'NULL'), '__', coalesce(tblwarehouse_items.date_sd, 'NULL'), '__', coalesce(tblwarehouse_items.date_use, 'NULL')) as id,
            tbllocaltion_warehouses.code as code_location,
            tbllocaltion_warehouses.name as name_location,
            SUM(tblwarehouse_items.product_quantity) as quantity_warehouse,
            tblwarehouse_items.lot_code as lot_code, 
            tblwarehouse_items.date_sx as date_sx,
            tblwarehouse_items.date_sd as date_sd, 
            tblwarehouse_items.date_use as date_use,
            tblwarehouse_items.localtion
        ");
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tblwarehouse_items.localtion = tbllocaltion_warehouses.id');
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.product_quantity >', 0);
        $this->db->group_by('tblwarehouse_items.localtion, tblwarehouse_items.lot_code, tblwarehouse_items.date_sx, tblwarehouse_items.date_sd, tblwarehouse_items.date_use');
        return $this->db->get()->result_array();
    }

    public function getLocationWarehouseQuantityLotDateUnit($warehouse_id, $id_items, $type_items)
    {
        // SUM(tblwarehouse_items.product_quantity_unit) as quantity_warehouse,
        $this->db->select("
            CONCAT(tblwarehouse_items.warehouse_id, '__', tblwarehouse_items.localtion, '__', coalesce(tblwarehouse_items.lot_code, 'NULL'), '__', coalesce(tblwarehouse_items.date_sx, 'NULL'), '__', coalesce(tblwarehouse_items.date_sd, 'NULL'), '__', coalesce(tblwarehouse_items.date_use, 'NULL')) as id,
            tbllocaltion_warehouses.code as code_location,
            tbllocaltion_warehouses.name as name_location,
            SUM(tblwarehouse_items.product_quantity) as quantity_warehouse,
            tblwarehouse_items.lot_code as lot_code, 
            tblwarehouse_items.date_sx as date_sx,
            tblwarehouse_items.date_sd as date_sd, 
            tblwarehouse_items.date_use as date_use,
            tblwarehouse_items.localtion
        ");
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tblwarehouse_items.localtion = tbllocaltion_warehouses.id');
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        // $this->db->where('tblwarehouse_items.product_quantity_unit >', 0);
        $this->db->where('tblwarehouse_items.product_quantity >', 0);
        $this->db->where('tbllocaltion_warehouses.stage_id', 0);
        $this->db->group_start();
        $this->db->where('tbllocaltion_warehouses.pod_id', 0);
        $this->db->or_where('exists (
            SELECT tbl_productions_orders_details.id
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "business_plan"
        )', false, false);
        $this->db->group_end();
        $this->db->group_by('tblwarehouse_items.localtion, tblwarehouse_items.lot_code, tblwarehouse_items.date_sx, tblwarehouse_items.date_sd, tblwarehouse_items.date_use');
        // print_arrays($this->db->get_compiled_select());
        return $this->db->get()->result_array();
    }

    public function getStaffChildPermission($permission, $fields)
    {
        $this->db->select('tbl_staff_child_permission_v2.id, tbl_staff_child_permission_v2.id_staff');
        $this->db->from('tbl_staff_child_permission_v2');
        $this->db->where('tbl_staff_child_permission_v2.obj_permission', $permission);
        $this->db->where($fields, 1);
        return $this->db->get()->result_array();
    }

    public function insertBatchWarehouseStaff($data = [])
    {
        return $this->db->insert_batch('tblwarehouse_staff', $data);
    }

    public function deleteWarehouseStaff($warehouse_id)
    {
        $this->db->where('warehouse_id', $warehouse_id);
        return $this->db->delete('tblwarehouse_staff');
    }

    public function getStaffWarehouse($warehouse_id)
    {
        $this->db->select('*');
        $this->db->from('tblwarehouse_staff');
        $this->db->where('tblwarehouse_staff.warehouse_id', $warehouse_id);
        return $this->db->get()->result_array();
    }

    public function getColorById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_colors');
        $this->db->where('tbl_colors.id', $id);
        return $this->db->get()->row_array();
    }

    public function getWarehouseProductLocationLIFO_FiFO($itemType, $itemId)
    {
        $this->db->select('tblwarehouse_product.quantity_left, tblwarehouse_product.price, tblwarehouse_product.warehouse_id, tblwarehouse_product.localtion, tblwarehouse.name as name_warehouse', false);
        $this->db->from('tblwarehouse_product');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_product.warehouse_id');
        $this->db->where('tblwarehouse_product.type_items', $itemType);
        $this->db->where('tblwarehouse_product.product_id', $itemId);
        $this->db->where('tblwarehouse_product.quantity_left >', 0);
        $this->db->where('tblwarehouse.supplier_id', 0);
        $this->db->order_by('tblwarehouse_product.date_warehouse ASC');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function getWarehouseProductLocationLIFO_FiFOStatus($itemType, $itemId, $status)
    {
        $this->db->select('tblwarehouse_product.quantity_left, tblwarehouse_product.price, tblwarehouse_product.warehouse_id, tblwarehouse_product.localtion, tblwarehouse.name as name_warehouse', false);
        $this->db->from('tblwarehouse_product');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_product.warehouse_id');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_product.localtion');
        $this->db->where('tblwarehouse_product.type_items', $itemType);
        $this->db->where('tblwarehouse_product.product_id', $itemId);
        $this->db->where('tblwarehouse_product.quantity_left >', 0);
        $this->db->where('tbllocaltion_warehouses.status !=', $status);
        $this->db->where('tblwarehouse.supplier_id', 0);
        $this->db->order_by('tblwarehouse_product.date_warehouse ASC');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function getLocationWarehouseStatus($warehouse_id, $status)
    {
        $this->db->select('*');
        $this->db->from('tbllocaltion_warehouses');
        $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
        $this->db->where('tbllocaltion_warehouses.status', $status);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getTransferWarehouse($order_id)
    {
        $this->db->select('*');
        $this->db->from('tbltransfer_warehouse');
        $this->db->where('tbltransfer_warehouse.order_id', $order_id);
        return $this->db->get()->row_array();
    }

    public function getTransferWarehouseById($id)
    {
        $this->db->select('*');
        $this->db->from('tbltransfer_warehouse');
        $this->db->where('tbltransfer_warehouse.id', $id);
        return $this->db->get()->row_array();
    }

    public function getWarehouseProductLocationLIFO_FiFOCapacity($itemType, $itemId)
    {
        $this->db->select('
            tblwarehouse_product.quantity_left, 
            tblwarehouse_product.price, 
            tblwarehouse_product.warehouse_id, 
            tblwarehouse_product.localtion, 
            tblwarehouse.name as name_warehouse
        ', false);
        $this->db->from('tblwarehouse_product');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_product.warehouse_id');
        // $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_product.localtion');
        $this->db->where('tblwarehouse_product.type_items', $itemType);
        $this->db->where('tblwarehouse_product.product_id', $itemId);
        $this->db->where('tblwarehouse_product.quantity_left >', 0);
        $this->db->where('tblwarehouse_product.warehouse_id !=', constant("WAREHOUSES_CAPACITY"));
        $this->db->where('tblwarehouse.supplier_id', 0);
        // $this->db->where('tbllocaltion_warehouses.status !=', $status);
        $this->db->order_by('tblwarehouse_product.date_warehouse ASC');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function getLocationWarehouseFirst($warehouse_id)
    {
        $this->db->select('*');
        $this->db->from('tbllocaltion_warehouses');
        $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
        $this->db->where('tbllocaltion_warehouses.child', 1);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getTransferByProductionsCapacityId($productions_capacity_id)
    {
        $this->db->select('*');
        $this->db->from('tbltransfer_warehouse');
        $this->db->where('productions_capacity_id', $productions_capacity_id);
        return $this->db->get()->row_array();
    }

    public function getShiftWork()
    {
        $this->db->select('*');
        $this->db->from('tbl_shift_work');
        return $this->db->get()->result_array();
    }

    public function getShiftWorkById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_shift_work');
        $this->db->where('tbl_shift_work.id', $id);
        return $this->db->get()->row_array();
    }

    //reports sale performance
    public function getTotalSales($start_date, $end_date, $branch_id = false)
    {
        $this->db->select("
            SUM(tbl_orders.grand_total + tbl_orders.total_discount_percent_items + tbl_orders.total_discount_direct_items + tbl_orders.total_discount_percent + tbl_orders.total_discount_direct - tbl_orders.total_tax) as grand_total,
            SUM(tbl_orders.total_discount_percent_items + tbl_orders.total_discount_direct_items + tbl_orders.total_discount_percent + tbl_orders.total_discount_direct) as total_discount,
            SUM(tbl_orders.total_tax) as grand_total_tax
        ", false);
        $this->db->from('tbl_orders');
        $this->db->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', $end_date);

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->db->where('(tbl_orders.id_branch = 0 OR tbl_orders.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->db->where('tbl_orders.id_branch', $branch_id);
            }
        }

        return $this->db->get()->row_array();
    }

    public function getReturnsSales($start_date, $end_date, $branch_id = false)
    {
        //SUM(tbl_returned_goods.grand_total - tbl_returned_goods.total_tax) as grand_total
        $this->db->select("
            SUM(tbl_returned_goods.grand_total) as grand_total,
        ", false);
        $this->db->from('tbl_returned_goods');
        $this->db->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") <=', $end_date);

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->db->where('(tbl_returned_goods.id_branch = 0 OR tbl_returned_goods.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->db->where('tbl_returned_goods.id_branch', $branch_id);
            }
        }

        return $this->db->get()->row_array();
    }

    public function getCostPriceSales($start_date, $end_date, $branch_id = false)
    {
        $this->db->select("
            SUM(IF (tbl_orders.total_cost > 0, tbl_orders.total_cost, tbl_orders.total_cost_temporary_capital)) as cost_price
        ", false);
        $this->db->from('tbl_orders');
        $this->db->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', $end_date);

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->db->where('(tbl_orders.id_branch = 0 OR tbl_orders.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->db->where('tbl_orders.id_branch', $branch_id);
            }
        }

        return $this->db->get()->row_array();
    }

    public function getCharge($start_date, $end_date, $branch_id = false)
    {
        $this->db->select("
            SUM(tblother_payslips.total) as total_other_payslips
        ", false);
        $this->db->from('tblother_payslips');
        $this->db->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") <=', $end_date);
        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->db->where('(tblother_payslips.id_branch = 0 OR tblother_payslips.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->db->where('tblother_payslips.id_branch', $branch_id);
            }
        }

        $this->db->where('tblother_payslips.status', 1);
        $this->db->where('tblother_payslips.not_kqkd', 0);
        $this->db->where('tblother_payslips.cost_allocation', 0);
        $otherPaySlips = $this->db->get()->row_array();

        //cost allocation
        $this->db->select("
            SUM(tbl_other_payslips_date.money_item) as total_other_payslips
        ", false);
        $this->db->from('tblother_payslips');
        $this->db->join('tbl_other_payslips_date', 'tbl_other_payslips_date.other_payslips_id = tblother_payslips.id');
        $this->db->where('DATE_FORMAT(tbl_other_payslips_date.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_other_payslips_date.date, "%Y-%m-%d") <=', $end_date);
        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->db->where('(tblother_payslips.id_branch = 0 OR tblother_payslips.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->db->where('tblother_payslips.id_branch', $branch_id);
            }
        }

        $this->db->where('tblother_payslips.status', 1);
        $this->db->where('tblother_payslips.not_kqkd', 0);
        $this->db->where('tblother_payslips.cost_allocation', 1);
        $otherPaySlipsCostAllocation = $this->db->get()->row_array();
        $paySlips['total_pay_slip'] = 0;

        $totalOtherPaySlips = !empty($otherPaySlips['total_other_payslips']) ? $otherPaySlips['total_other_payslips'] : 0;
        $totalPaySlips = !empty($paySlips['total_pay_slip']) ? $paySlips['total_pay_slip'] : 0;
        $totalOtherPaySlipsCost = !empty($otherPaySlipsCostAllocation['total_other_payslips']) ? $otherPaySlipsCostAllocation['total_other_payslips'] : 0;

        return $totalOtherPaySlips + $totalPaySlips + $totalOtherPaySlipsCost;
    }

    public function getOtherPayslipsCoupon($start_date, $end_date, $branch_id = false)
    {
        $this->db->select("
            SUM(tblother_payslips_coupon.total) as total
        ", false);
        $this->db->from('tblother_payslips_coupon');
        $this->db->where('DATE_FORMAT(tblother_payslips_coupon.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tblother_payslips_coupon.date, "%Y-%m-%d") <=', $end_date);
        $this->db->where('tblother_payslips_coupon.not_kqkd', 0);
        if (!empty($branch_id)) {
            $this->db->join('tblstaff', 'tblstaff.staffid = tblother_payslips_coupon.staff_id');
            if ($branch_id == BRANCH_DEFAULT) {
                $this->db->where('(tblstaff.id_branch = 0 OR tblstaff.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->db->where('tblstaff.id_branch', $branch_id);
            }
        }
        return $this->db->get()->row_array();
    }

    public function getTotalService($start_date, $end_date, $branch_id = false)
    {
        $this->db->select("
            SUM(tbl_services.subtotal - tbl_services.payment) as grand_total
        ", false);
        $this->db->from('tbl_services');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_services.staff_id', 'left');
        $this->db->where('DATE_FORMAT(tbl_services.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(tbl_services.date, "%Y-%m-%d") <=', $end_date);
        $this->db->where('tbl_services.status', 1);
        $this->db->where('tbl_services.not_kqkd !=', 1);

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->db->where('(tblstaff.id_branch = 0 OR tblstaff.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->db->where('tblstaff.id_branch', $branch_id);
            }
        }
        return $this->db->get()->row_array();
    }

    public function getBranch($arr_id_not = [])
    {
        $this->db->select('*');
        $this->db->from('tblbranch');
        // if (!empty($arr_id_not)) {
        //     $this->db->where_not_in('tblbranch.id', $arr_id_not);
        // } else {
        //     $this->db->where_not_in('tblbranch.id', [BRANCH_DEFAULT]);
        // }
        return $this->db->get()->result_array();
    }

    public function getBranchById($id)
    {
        $this->db->select('*');
        $this->db->from('tblbranch');
        $this->db->where('tblbranch.id', $id);
        return $this->db->get()->row_array();
    }

    public function getExchangeProducts($product_id)
    {
        $this->db->select('tbl_exchange_products.*, tblunits.unit as unit_name');
        $this->db->from('tbl_exchange_products');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_exchange_products.unit_id');
        $this->db->where('tbl_exchange_products.product_id', $product_id);
        return $this->db->get()->result_array();
    }

    public function getLocationToPlan($productions_plan_id)
    {
        $this->db->select('tbllocaltion_warehouses.id as id');
        $this->db->from('tbllocaltion_warehouses');
        $this->db->where('tbllocaltion_warehouses.productions_plan_id', $productions_plan_id);
        $this->db->where('tbllocaltion_warehouses.warehouse', WAREHOUSES_CAPACITY);
        $this->db->limit(1);
        $warehouseLocation = $this->db->get()->row_array();
        if (empty($warehouseLocation)) {

            $productions_plan = get_table_where('tbl_productions_plan', ['id' => $productions_plan_id], "", "row_array", 'reference_no');

            $dataLW = [
                'name' => $productions_plan['reference_no'],
                'code' => $productions_plan['reference_no'],
                'warehouse' => WAREHOUSES_CAPACITY,
                'id_parent' => 0,
                'name_parent' => $productions_plan['reference_no'],
                'child' => 1,
                'create_by' => get_staff_user_id(),
                'date_create' => date('Y-m-d H:i:s'),
                'status' => 0,
                'lever' => 1,
                'type_excel' => 1,
                'productions_plan_id' => $productions_plan_id,
            ];
            $this->db->insert('tbllocaltion_warehouses', $dataLW);
            $locationWId = $this->db->insert_id();
        } else {
            $locationWId = $warehouseLocation['id'];
        }
        return $locationWId;
    }

    //
    public function getTotalQuantityWarehouse($id_items, $type_items, $warehouse_id, $location_id)
    {
        $this->db->select('SUM(tblwarehouse_items.product_quantity) as total_quantity', false);
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        $this->db->where('tblwarehouse_items.localtion', $location_id);
        return $this->db->get()->row_array();
    }

    public function getProductionsOrdersItemsStages($object_type, $object_item_id, $plan_id = 0)
    {

        $this->db->select('
            tbl_productions_orders_items.id as productions_orders_items_id,
            tbl_productions_orders.reference_no as reference_no,
            tbl_productions_orders_items.quantity as quantity,
            tbl_productions_orders.status_orders as status_orders,
        ', false);
        $this->db->from('tbl_productions_orders_items');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id');
        $this->db->where('tbl_productions_orders_items.production_plan_item_id', $object_item_id);
        $this->db->where('tbl_productions_orders_items.object_item_type', $object_type);
        if (!empty($plan_id)) {
            $this->db->where('tbl_productions_orders_items.plan_id', $plan_id);
        }
        $data = $this->db->get()->result_array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $productions_orders_items_id = $value['productions_orders_items_id'];
                $this->db->select('
                    tbl_productions_orders_items_stages.id as id,
                    tbl_productions_orders_items_stages.active as active,
                    tbl_productions_orders_items_stages.staff_active as staff_active,
                    tbl_productions_orders_items_stages.date_active as date_active,
                    tbl_stages.name as stage_name,
                    CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                    IF (tblstaff.profile_image IS NOT NULL, CONCAT("' . base_url('uploads/staff_profile_images/') . '", tblstaff.staffid, "/small__", tblstaff.profile_image), null) as staff_image,
                    tbl_productions_orders_items_stages.final_stage as final_stage
                ', false);
                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                $this->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
                $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
                $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
                $data[$key]['process'] = $this->db->get()->result_array();
            }
        }
        return $data;
    }

    public function getLocationPOD($pod_id, $warehouse_id, $stage_id = 0)
    {
        $this->db->select('tbllocaltion_warehouses.id as id');
        $this->db->from('tbllocaltion_warehouses');
        $this->db->where('tbllocaltion_warehouses.pod_id', $pod_id);
        $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
        if (!empty($stage_id)) {
            $this->db->where('tbllocaltion_warehouses.stage_id', $stage_id);
        } else {
            $this->db->where('tbllocaltion_warehouses.stage_id', 0);
        }
        $this->db->limit(1);
        $warehouseLocation = $this->db->get()->row_array();
        if (empty($warehouseLocation)) {

            $pod = get_table_where('tbl_productions_orders_details', ['id' => $pod_id], "", "row_array", 'reference_no');
            $name = $pod['reference_no'];
            if ($stage_id) {
                $stage = get_table_where('tbl_stages', ['id' => $stage_id], "", "row_array", 'name');
                $name .= '(' . $stage['name'] . ')';
            }

            $dataLW = [
                'name' => $name,
                'code' => $name,
                'warehouse' => $warehouse_id,
                'id_parent' => 0,
                'name_parent' => $name,
                'child' => 1,
                'create_by' => get_staff_user_id(),
                'date_create' => date('Y-m-d H:i:s'),
                'status' => 0,
                'lever' => 1,
                'type_excel' => 1,
                'pod_id' => $pod_id,
                'stage_id' => $stage_id,
            ];
            $this->db->insert('tbllocaltion_warehouses', $dataLW);
            $locationWId = $this->db->insert_id();
        } else {
            $locationWId = $warehouseLocation['id'];
        }
        return $locationWId;
    }

    public function getStages()
    {
        $this->db->select('tbl_stages.*', false);
        $this->db->from('tbl_stages');
        $this->db->where('tbl_stages.parent_id', 0);
        $stages = $this->db->get()->result_array();
        return $stages;
    }

    public function getCurrencies()
    {
        $this->db->select('*');
        $this->db->from('tblcurrencies');
        return $this->db->get()->result_array();
    }

    public function getCurrenciesById($id)
    {
        $this->db->select('*');
        $this->db->from('tblcurrencies');
        $this->db->where('tblcurrencies.id', $id);
        return $this->db->get()->row_array();
    }

    public function getSize()
    {
        $this->db->select('*');
        $this->db->from('tblsize');
        return $this->db->get()->result_array();
    }

    public function getSizeById($id)
    {
        $this->db->select('*');
        $this->db->from('tblsize');
        $this->db->where('tblsize.id', $id);
        return $this->db->get()->row_array();
    }

    public function getColors()
    {
        $this->db->select('*');
        $this->db->from('tbl_colors');
        return $this->db->get()->result_array();
    }

    public function getQuantityWarehouse($id_items, $type_items, $warehouse_id, $localtion, $lot_code, $date_sx, $date_sd)
    {
        $this->db->select('
            SUM(tblwarehouse_items.product_quantity) as product_quantity,
            tblwarehouse.name as name_warehouse,
            tbllocaltion_warehouses.name as name_location,
        ', false);
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tblwarehouse_items.localtion = tbllocaltion_warehouses.id');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_items.warehouse_id');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        $this->db->where('tblwarehouse_items.localtion', $localtion);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.lot_code', $lot_code);
        $this->db->where('tblwarehouse_items.date_sx', $date_sx);
        $this->db->where('tblwarehouse_items.date_sd', $date_sd);
        return $this->db->get()->row_array();
    }

    public function getCustomersGroups()
    {
        $this->db->select('*');
        $this->db->from('tblcustomers_groups');
        return $this->db->get()->result_array();
    }

    public function getTotalWarehouse($type_items, $id_items)
    {
        $this->db->select('SUM(tblwarehouse_items.product_quantity) as product_quantity', false);
        $this->db->from('tblwarehouse_items');
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        return $this->db->get()->row_array();
    }

    public function getStaffAll()
    {

        $staffDepartments = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department 
            FROM tblstaff_departments
            INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_staff_departments";

        $this->db->select('
            tblstaff.staffid as staffid,
            tblstaff.firstname as firstname,
            tblstaff.lastname as lastname,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname,
            tblroles.name as name_role,
            tb_staff_departments.name_department as name_department,
            tblstaff.active as active,
        ', false);
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join($staffDepartments, 'tb_staff_departments.staffid = tblstaff.staffid', 'left');
        return $this->db->get()->result_array();
    }

    public function getStaffByStaffId($staff_id)
    {
        $staffDepartments = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.departmentid) as departmentid,
                GROUP_CONCAT(tbldepartments.name) as name_department 
            FROM tblstaff_departments
            INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_staff_departments";

        $this->db->select('
            tblstaff.staffid as staffid,
            tblstaff.firstname as firstname,
            tblstaff.lastname as lastname,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname,
            tblroles.name as name_role,
            tb_staff_departments.name_department as name_department,
            tblstaff.role as role_id,
            tb_staff_departments.departmentid as departmentid
        ', false);
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join($staffDepartments, 'tb_staff_departments.staffid = tblstaff.staffid', 'left');
        $this->db->where('tblstaff.staffid', $staff_id);
        return $this->db->get()->row_array();
    }

    public function getBranchByName($name)
    {
        $this->db->select('*');
        $this->db->from('tblbranch');
        $this->db->where('tblbranch.name', $name);
        return $this->db->get()->row_array();
    }

    public function getCurrenciesByName($name)
    {
        $this->db->select('*');
        $this->db->from('tblcurrencies');
        $this->db->where('tblcurrencies.name', $name);
        return $this->db->get()->row_array();
    }

    public function getCategoryTasks($arr_id = [], $room_task = null)
    {
        $this->db->select('tblcategory_tasks.*');
        $this->db->from('tblcategory_tasks');
        $this->db->group_start();
        $this->db->where('tblcategory_tasks.hide', 0);
        if (!empty($arr_id)) {
            $this->db->or_where_in('tblcategory_tasks.id', $arr_id);
        }
        $this->db->group_end();

        if (!empty($room_task)) {
            $this->db->group_start();
            foreach ($room_task as $key => $value) {
                if($key == 0) {
                    $this->db->where('FIND_IN_SET('.$value.', tblcategory_tasks.departments)', false, false);
                } else {
                    $this->db->or_where('FIND_IN_SET('.$value.', tblcategory_tasks.departments)', false, false);
                }
            }
            $this->db->group_end();
        }
        return $this->db->get()->result_array();
    }

    public function getDepartmentsActive($active = [])
    {
        $this->db->select('*');
        $this->db->from('tbldepartments');
        if (!empty($active)) {
            $this->db->where_in('tbldepartments.active_departments', $active);
        }
        return $this->db->get()->result_array();
    }

    public function getPOTranferBusinessItem($tranfer_business_id)
    {
        $this->db->select('
            GROUP_CONCAT(distinct tbl_productions_orders.reference_no SEPARATOR ", ") as reference_no_po
        ', false);
        $this->db->from('tbl_tranfer_business_item');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.production_plan_item_id = tbl_tranfer_business_item.business_plan_item_id AND tbl_productions_orders_items.object_item_type = "business_plan"');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id');
        $this->db->where('tbl_tranfer_business_item.tranfer_business_id', $tranfer_business_id);
        return $this->db->get()->row_array();
    }

    public function getPOTranferBusinessItemByItem($business_plan_item_id)
    {
        $this->db->select('
            GROUP_CONCAT(distinct tbl_productions_orders.reference_no SEPARATOR ", ") as reference_no_po
        ', false);
        $this->db->from('tbl_tranfer_business_item');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.production_plan_item_id = tbl_tranfer_business_item.business_plan_item_id AND tbl_productions_orders_items.object_item_type = "business_plan"');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id');
        $this->db->where('tbl_tranfer_business_item.business_plan_item_id', $business_plan_item_id);
        return $this->db->get()->row_array();
    }

    public function getStagesByCodeName($str)
    {
        $this->db->select('tbl_stages.*');
        $this->db->from('tbl_stages');
        $this->db->group_start();
        $this->db->like('tbl_stages.code', $str);
        $this->db->or_like('tbl_stages.name', $str);
        $this->db->group_end();
        return $this->db->get()->row_array();
    }
    public function getRoom()
    {
        $this->db->select('*');
        $this->db->from('tbl_room');
        return $this->db->get()->result_array();
    }
    public function getBoard()
    {
        $this->db->select('*');
        $this->db->from('tbl_board');
        return $this->db->get()->result_array();
    }
    public function getBlock()
    {
        $this->db->select('*');
        $this->db->from('tbl_block');
        return $this->db->get()->result_array();
    }

    public function getCategoryRecommended() {
        $this->db->select('tbl_category_recommended.*');
        $this->db->from('tbl_category_recommended');
        return $this->db->get()->result_array();
    }

    public function getDiscount($status = 2) {
        $this->db->select('tbl_discount.*', false);
        $this->db->from('tbl_discount');
        $this->db->where('tbl_discount.status', $status);
        return $this->db->get()->result_array();
    }

    public function rowDiscount($id) {
        $this->db->select('tbl_discount.*', false);
        $this->db->from('tbl_discount');
        $this->db->where('tbl_discount.id', $id);
        return $this->db->get()->row_array();
    }
}
