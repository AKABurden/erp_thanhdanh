<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();
        // if (!has_permission('reports', '', 'view')) {
        //     access_denied('reports');
        // }
        $this->ci = &get_instance();
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
        $this->load->model('orders_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');

        $this->perViewOrdersOfQuotes = has_permission('orders_of_quotes', '', 'view');
        $this->perViewDeliverySchedules = has_permission('delivery_schedules', '', 'view');
        $this->perViewSalesOfOrder = has_permission('sales_of_order', '', 'view');
        $this->perViewNearestSellingPrice = has_permission('nearest_selling_price', '', 'view');
        $this->perViewReturnedGoods = has_permission('returned_goods', '', 'view');
        $this->perViewOrderStatus = has_permission('order_status', '', 'view');
        $this->perViewSalesAnalysis = has_permission('sales_analysis', '', 'view');
        $this->perViewSellingDiary = has_permission('selling_diary', '', 'view');

        $this->perViewMaterialNorms = has_permission('material_norms', '', 'view');
        $this->perViewUsageMaterial = has_permission('usage_material', '', 'view');
        $this->perViewProductionDetailed = has_permission('production_detailed', '', 'view');
        $this->perViewSituationOrderExecution = has_permission('situation_order_execution', '', 'view');
        $this->perViewStatusProduction = has_permission('status_production', '', 'view');
        $this->perViewUseMlAcProductionOrders = has_permission('use_ml_ac_production_orders', '', 'view');
        $this->perViewGeneralProduction = has_permission('general_production', '', 'view');
        $this->perViewProductionScheduleByOrder = has_permission('production_schedule_by_order', '', 'view');
        $this->perViewExpensesIncome = has_permission('expenses_vs_income', '', 'view');
    }

    /* No access on this url */
    public function index()
    {
        redirect(admin_url());
    }

    /* See knowledge base article reports*/
    public function knowledge_base_articles()
    {
        $this->load->model('knowledge_base_model');
        $data['groups'] = $this->knowledge_base_model->get_kbg();
        $data['title'] = _l('kb_reports');
        $this->load->view('admin/reports/knowledge_base_articles', $data);
    }

    /*
        public function tax_summary(){
           $this->load->model('taxes_model');
           $this->load->model('payments_model');
           $this->load->model('invoices_model');
           $data['taxes'] = $this->db->query("SELECT DISTINCT taxname,taxrate FROM ".db_prefix()."item_tax WHERE rel_type='invoice'")->result_array();
            $this->load->view('admin/reports/tax_summary',$data);
        }*/
    /* Repoert leads conversions */
    public function leads()
    {
        $type = 'leads';
        if ($this->input->get('type')) {
            $type = $type . '_' . $this->input->get('type');
            $data['leads_staff_report'] = json_encode($this->reports_model->leads_staff_report());
        }
        $this->load->model('leads_model');
        $data['statuses'] = $this->leads_model->get_status();
        $data['leads_this_week_report'] = json_encode($this->reports_model->leads_this_week_report());
        $data['leads_sources_report'] = json_encode($this->reports_model->leads_sources_report());
        $this->load->view('admin/reports/' . $type, $data);
    }

    public function fund_balance()
    {
        $this->load->model('costs_model');
        $type = 'fund_balance';
        $data = array();
        $data['costs'] = [];
        $this->costs_model->get_by_ids(0, $data['costs']);
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('cash' => 1));
        $data['payment_modes_bank'] = get_table_where('tblpayment_modes', array('bank' => 1));

        if ($this->input->get('type')) {
            $type = $type . '_' . $this->input->get('type');
            $data['leads_staff_report'] = json_encode($this->reports_model->leads_staff_report());
        }
        if ($this->input->get('is_type')) {
            $data['is_type'] = $this->input->get('is_type');
        }
        $this->load->view('admin/reports/' . $type, $data);
    }

    public function diary_of_collecting_money()
    {
        $this->app->get_table_data('diary_of_collecting_money');
    }

    public function diary_of_spending_money()
    {
        $this->app->get_table_data('diary_of_spending_money');
    }

    public function diary_of_revenue_and_expenditure()
    {
        $this->app->get_table_data('diary_of_revenue_and_expenditure');
    }

    public function aggregate_fund_balance()
    {
        $this->app->get_table_data('aggregate_fund_balance');
    }

    public function cash_book()
    {
        $this->app->get_table_data('cash_book');
    }

    public function cash_book_bank()
    {
        $this->app->get_table_data('cash_book_bank');
    }

    public function purchase()
    {
        $data['dataStaff'] = get_table_where('tblstaff');
        foreach ($data['dataStaff'] as $key => $value) {
            $data['dataStaff'][$key]['name'] = get_staff_full_name($value['staffid']);
        }
        $type = 'purchase';
        if ($this->input->get('type')) {
            $type = $type . '_' . $this->input->get('type');
            $data['leads_staff_report'] = json_encode($this->reports_model->leads_staff_report());
        }
        if ($this->input->get('is_type')) {
            $data['is_type'] = $this->input->get('is_type');
        }
        $this->load->model('leads_model');
        $data['statuses'] = $this->leads_model->get_status();
        $data['leads_this_week_report'] = json_encode($this->reports_model->leads_this_week_report());
        $data['leads_sources_report'] = json_encode($this->reports_model->leads_sources_report());
        $data['dataSupplier'] = get_table_where('tblsuppliers', array('type' => 0));
        $this->load->view('admin/reports/' . $type, $data);
    }

    public function general_purchase_detail_report()
    {
        $this->app->get_table_data('general_purchase_detail_report');
    }

    public function general_purchase_report()
    {
        $this->app->get_table_data('general_purchase_report');
    }

    public function detail_purchase_report()
    {
        $this->app->get_table_data('detail_purchase_report');
    }

    public function to_pay_debt_report()
    {
        $this->app->get_table_data('to_pay_debt_report');
    }

    public function detail_debt_report()
    {
        $this->app->get_table_data('detail_debt_report');
    }

    public function warehouse()
    {
        $data['weekly_payment_stats'] = json_encode($this->dashboard_model->get_weekly_payments_statistics(array()));
        $data['tnh'] = true;
        $type = 'warehouse';
        if ($this->input->get('type')) {
            $type = $type . '_' . $this->input->get('type');
            $data['leads_staff_report'] = json_encode($this->reports_model->leads_staff_report());
        }
        $this->load->model('leads_model');
        $data['statuses'] = $this->leads_model->get_status();
        $data['leads_this_week_report'] = json_encode($this->reports_model->leads_this_week_report());
        $data['leads_sources_report'] = json_encode($this->reports_model->leads_sources_report());
        $data['warehouse'] = get_table_where('tblwarehouse');
        $data['material'] = get_table_where('tbl_materials');
        $th = '';
        $targets = 16;
        $script = '';
        if (!empty($this->show_table_custom_fields)) {
            foreach ($this->show_table_custom_fields as $key => $value) {
                $th .= '<th>' . _maybe_translate_custom_field_name($value['name'], $value['slug']) . '</th>';
                $script .= '{
                    "targets": ' . $targets . ', "name": "' . $value['slug'] . '", "width": "80px"
                },';
                $targets++;
            }
        }
        $data['targets'] = $targets;
        $data['script'] = $script;
        $data['th'] = $th;
        $output = array();
        $outputs = array();
        $this->recursiveCategoryItems_new(0, $output);
        $cate_tools = $this->recursiveCategoryItemsProduct_new(0, $outputs);
        $data['CategoryItems'] = array();
        $data['CategoryItems'][0]['name'] = 'Nguyên vật liệu';
        $data['CategoryItems'][0]['detail'] = $output;
        $data['CategoryItems'][1]['name'] = 'Thành phẩm';
        $data['CategoryItems'][1]['detail'] = $cate_tools;
        $data['type_items'] = array(
            array(
                'type' => 'product',
                'name' => 'Thành phẩm - Bán thành phẩm',
            ),
            array(
                'type' => 'nvl',
                'name' => 'Nguyên vật liệu',
            ),
        );
        if ($this->input->get('is_type')) {
            $data['is_type'] = $this->input->get('is_type');
        }
        $this->load->view('admin/reports/' . $type, $data);
    }

    public function recursiveCategoryItems_new($id = 0, &$output = null, $parent_id = 0, $indent = null)
    {
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('tbl_category_items');
        $CI->db->where('tbl_category_items.parent_id', $parent_id);
        $CI->db->order_by('tbl_category_items.parent_id');
        $query = $CI->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $disabled = '';
                if ($item['id'] == $id && $id != 0) {
                    continue;
                }
                // if ($parent_id == 0) {
                // 	$disabled = 'disabled';
                // }
                // data-icon="fa fa-ellipsis-h"
                $item['id'] = 'nvl_' . $item['id'];
                $item['name'] = $indent . '➪ ' . $item['name'] . '(' . $item['code'] . ')';
                $output[] = $item;
                $this->recursiveCategoryItems_new($id, $output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
        }

        return $output;
    }

    public function recursiveCategoryItemsProduct_new($id = 0, &$output = null, $parent_id = 0, $indent = null)
    {
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('tbl_category_products');
        $CI->db->where('tbl_category_products.parent_id', $parent_id);
        $CI->db->order_by('tbl_category_products.parent_id');
        $query = $CI->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $disabled = '';
                if ($item['id'] == $id && $id != 0) {
                    continue;
                }

                $item['code'] = str_replace('\'', '', $item['code']);
                $item['code'] = str_replace('"', '', $item['code']);
                $item['name'] = str_replace('"', '', $item['name']);
                $item['name'] = str_replace('"', '', $item['name']);
                $item['name'] = str_replace('/n', '', $item['name']);
                $item['name'] = str_replace('\n', '', $item['name']);
                $item['code'] = str_replace('\n', '', $item['code']);
                $item['code'] = str_replace('/n', '', $item['code']);
                $item_s['name'] = $indent . '➪ ' . $item['name'] . '(' . $item['code'] . ')';
                $item_s['id'] = 'product_' . $item['id'];
                $output[] = $item_s;
                $this->recursiveCategoryItemsProduct_new(
                    $id,
                    $output,
                    $item['id'],
                    $indent . "&nbsp;&nbsp;&nbsp;&nbsp;"
                );
            }
        }

        return $output;
    }

    public function recursiveCategoryTools_new()
    {
        $CI = &get_instance();
        $output = array();
        $CI->db->select('*');
        $CI->db->from('tbl_category_tools_supplies');
        $query = $CI->db->get()->result_array();

        foreach ($query as $key => $item) {
            $item['id'] = 'tools_' . $item['id'];
            $item['name'] = '➪ ' . $item['name'] . '(' . $item['code'] . ')';
            $output[] = $item;
        }

        return $output;
    }

    public function chart_warehouse()
    {
        $data['title'] = _l('ch_chart_warehores');
        $this->load->view('admin/reports/warehouse/chart_warehouse', $data);
    }

    public function general_synthetic_purchase_report()
    {
        $beginMonth =  '';
        $endMonth   =  '';
        $months_report = $this->ci->input->post('report_months');

        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth   = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->ci->input->post('report_from'));
                $to_date   = to_sql_date($this->ci->input->post('report_to'));
                if ($from_date == $to_date) {
                    $beginMonth =  $to_date;
                    $endMonth   =  $to_date;
                } else {
                    $beginMonth =  $from_date;
                    $endMonth   =  $to_date;
                }
            }
        }

        $custom_item_select = $this->ci->input->post('custom_item_select');
        $type_items = $this->ci->input->post('type_items');

        $select = array(
            'tblpurchases.date',
            'concat(tblpurchases.prefix,"",tblpurchases.code) as po_code',
            'tblsuppliers.code as code_supplier',
            'tblsuppliers.company as company',
            '1',
            '2',
            '3',
            'tbl_internal_proposal_purchase_items.quantity as quantity_net',
            'tbl_internal_proposal_purchase_items.quantity as quantity',
            '6',
            '7',
            'tblpurchases.explanation',
        );
        $where = array();
        if (!empty($type_items)) {
            array_push($where, 'AND tblpurchases_items.product_id =', $custom_item_select);
            array_push($where, 'AND tblpurchases_items.type = "' . $type_items . '"');
        }
        if (!empty($beginMonth) && !empty($endMonth)) {
            array_push($where, 'AND tblpurchases.date >=' . '"' . $beginMonth . ' 00:00:00"');
            array_push($where, 'AND tblpurchases.date <=' . '"' . $endMonth . ' 23:59:59"');
        }
        $aColumns     = $select;
        $sIndexColumn = "id";
        $sTable       = 'tblpurchases_items';
        $join         = array(
            'INNER JOIN tblpurchases ON tblpurchases.id = tblpurchases_items.purchases_id',
            'INNER JOIN tbl_internal_proposal_purchase_items ON tbl_internal_proposal_purchase_items.id_purchases_items = tblpurchases_items.id',
            'INNER JOIN tblsuppliers ON tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id',
        );

        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('
            tblpurchases_items.id as id_main,
            tblpurchases_items.product_id as product_id,
            tblpurchases_items.type as type,
            tblsuppliers.id as supplier_id,
            tbl_internal_proposal_purchase_items.price as price
        '), ' ORDER BY tblsuppliers.id,tblpurchases.date desc');

        $output  = $result['output'];
        $rResult = $result['rResult'];
        $checkExists = '';
        $total_quantity = 0;
        $total_quantity_purchase = 0;
        $total_amount = 0;
        $grand_total_quantity = 0;
        $grand_total_quantity_purchase = 0;
        $grand_total_amount = 0;
        $checkExistsNew = '';
        foreach ($rResult as $key => $aRow) {
            $row = [];
            $get_items = get_items($aRow['product_id'], $aRow['type']);
            $company = $aRow['company'];
            $code_supplier = $aRow['code_supplier'];
            if ($key == 0) {
                $checkExistsNew = $aRow['supplier_id'];
            } else {
                if ($checkExistsNew == $aRow['supplier_id']) {
                    $company = '';
                    $code_supplier = '';
                }
                $checkExistsNew = $aRow['supplier_id'];
            }
            for ($i = 0; $i < count($aColumns); $i++) {
                if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                    $_data = $aRow[strafter($aColumns[$i], 'as ')];
                } else {
                    $_data = $aRow[$aColumns[$i]];
                }
                if ($aColumns[$i] == 'tblpurchases.date') {
                    $_data = _d($aRow['tblpurchases.date']);
                }
                if ($aColumns[$i] == 'tblsuppliers.code as code_supplier') {
                    $_data = '<div style="color: #008ECE">' . $code_supplier . '</div>';
                }
                if ($aColumns[$i] == 'tblsuppliers.company as company') {
                    $_data = '<div style="color: #008ECE">' . $company . '</div>';
                }
                if ($aColumns[$i] == '1') {
                    $_data = $get_items->code;
                }
                if ($aColumns[$i] == '2') {
                    $_data = $get_items->name;
                }
                if ($aColumns[$i] == '3') {
                    $_data =  '<div class="text-center">' . $get_items->unit_name_payment . '<input type="hidden" class="supplier_id" value="' . $aRow['supplier_id'] . '">';
                }
                if ($aColumns[$i] == 'tbl_internal_proposal_purchase_items.quantity as quantity_net') {
                    $_data = '<div class="text-center quantity-' . $aRow['supplier_id'] . '">' . formatNumber($aRow['quantity_net']) . '</div>';
                    $total_quantity += $aRow['quantity_net'];
                    $grand_total_quantity += $aRow['quantity_net'];
                }
                if ($aColumns[$i] == 'tbl_internal_proposal_purchase_items.quantity as quantity') {
                    $_data = '<div class="text-center" quantity-purchase-' . $aRow['supplier_id'] . '>' . formatNumber($aRow['quantity']) . '</div>';
                    $total_quantity_purchase += $aRow['quantity'];
                    $grand_total_quantity_purchase += $aRow['quantity_net'];
                }
                if ($aColumns[$i] == '6') {
                    $_data = '<div>' . formatMoney($aRow['price']) . '</div>';
                }
                if ($aColumns[$i] == '7') {
                    $_data = '<div>' . formatMoney($aRow['price'] * $aRow['quantity']) . '</div>';
                    $total_amount += $aRow['price'] * $aRow['quantity'];
                    $grand_total_amount += $aRow['price'] * $aRow['quantity'];
                }

                $row[] = $_data;
            }

            $row['DT_RowClass'] = '';
            $output['aaData'][] = $row;
            if ($key == 0) {
                $checkExists = $aRow['supplier_id'];
            }
            if ((!empty($rResult[($key + 1)]) && $rResult[($key + 1)]['supplier_id'] != $checkExists) || empty($rResult[($key + 1)])) {
                $row = array(
                    '',
                    '',
                    '',
                    'Cộng',
                    '',
                    '',
                    '',
                    '<div class="text-center">' . formatNumber($total_quantity) . '</div>',
                    '<div class="text-center"">' . formatNumber($total_quantity_purchase) . '</div>',
                    '',
                    '',
                    '',
                );
                $row['DT_RowClass'] = 'alert-header bold warning';
                $output['aaData'][] = $row;
                $total_quantity = 0;
                $total_quantity_purchase = 0;
                $total_amount = 0;
                $checkExists = !empty($rResult[$key + 1]) ? $rResult[$key + 1]['supplier_id'] : 0;
            }
        }
        $output['grand_total_quantity'] = $grand_total_quantity;
        $output['grand_total_quantity_purchase'] = $grand_total_quantity_purchase;
        $output['grand_total_amount'] = $grand_total_amount;
        echo  json_encode($output);
    }

    public function general_detail_import_report()
    {
        $beginMonth =  '';
        $endMonth   =  '';
        $months_report = $this->ci->input->post('report_months');

        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth   = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->ci->input->post('report_from'));
                $to_date   = to_sql_date($this->ci->input->post('report_to'));
                if ($from_date == $to_date) {
                    $beginMonth =  $to_date;
                    $endMonth   =  $to_date;
                } else {
                    $beginMonth =  $from_date;
                    $endMonth   =  $to_date;
                }
            }
        }

        $custom_item_select = $this->ci->input->post('custom_item_select');
        $type_items = $this->ci->input->post('type_items');

        $select = array(
            'tblpurchases.date',
            'concat(tblpurchases.prefix,"",tblpurchases.code) as po_code',
            'tblsuppliers.code as code_supplier',
            'tblsuppliers.company as company',
            '1',
            '2',
            '3',
            'tbl_internal_proposal_purchase_items.quantity as quantity_net',
            'tbl_internal_proposal_purchase_items.quantity as quantity',
            '6',
            '7',
            'tblpurchases.explanation',
        );
        $where = array();
        if (!empty($type_items)) {
            array_push($where, 'AND tblpurchases_items.product_id =', $custom_item_select);
            array_push($where, 'AND tblpurchases_items.type = "' . $type_items . '"');
        }
        if (!empty($beginMonth) && !empty($endMonth)) {
            array_push($where, 'AND tblpurchases.date >=' . '"' . $beginMonth . ' 00:00:00"');
            array_push($where, 'AND tblpurchases.date <=' . '"' . $endMonth . ' 23:59:59"');
        }
        $aColumns     = $select;
        $sIndexColumn = "id";
        $sTable       = 'tblpurchases_items';
        $join         = array(
            'INNER JOIN tblpurchases ON tblpurchases.id = tblpurchases_items.purchases_id',
            'INNER JOIN tbl_internal_proposal_purchase_items ON tbl_internal_proposal_purchase_items.id_purchases_items = tblpurchases_items.id',
            'INNER JOIN tblsuppliers ON tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id',
        );

        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('
            tblpurchases_items.id as id_main,
            tblpurchases_items.product_id as product_id,
            tblpurchases_items.type as type,
            tblsuppliers.id as supplier_id,
            tbl_internal_proposal_purchase_items.price as price
        '), ' ORDER BY tblsuppliers.id,tblpurchases.date desc');

        $output  = $result['output'];
        $rResult = $result['rResult'];
        $checkExists = '';
        $total_quantity = 0;
        $total_quantity_purchase = 0;
        $total_amount = 0;
        $grand_total_quantity = 0;
        $grand_total_quantity_purchase = 0;
        $grand_total_amount = 0;
        $checkExistsNew = '';
        foreach ($rResult as $key => $aRow) {
            $row = [];
            $get_items = get_items($aRow['product_id'], $aRow['type']);
            $company = $aRow['company'];
            $code_supplier = $aRow['code_supplier'];
            if ($key == 0) {
                $checkExistsNew = $aRow['supplier_id'];
            } else {
                if ($checkExistsNew == $aRow['supplier_id']) {
                    $company = '';
                    $code_supplier = '';
                }
                $checkExistsNew = $aRow['supplier_id'];
            }
            for ($i = 0; $i < count($aColumns); $i++) {
                if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                    $_data = $aRow[strafter($aColumns[$i], 'as ')];
                } else {
                    $_data = $aRow[$aColumns[$i]];
                }
                if ($aColumns[$i] == 'tblpurchases.date') {
                    $_data = _d($aRow['tblpurchases.date']);
                }
                if ($aColumns[$i] == 'tblsuppliers.code as code_supplier') {
                    $_data = '<div style="color: #008ECE">' . $code_supplier . '</div>';
                }
                if ($aColumns[$i] == 'tblsuppliers.company as company') {
                    $_data = '<div style="color: #008ECE">' . $company . '</div>';
                }
                if ($aColumns[$i] == '1') {
                    $_data = $get_items->code;
                }
                if ($aColumns[$i] == '2') {
                    $_data = $get_items->name;
                }
                if ($aColumns[$i] == '3') {
                    $_data =  '<div class="text-center">' . $get_items->unit_name_payment . '<input type="hidden" class="supplier_id" value="' . $aRow['supplier_id'] . '">';
                }
                if ($aColumns[$i] == 'tbl_internal_proposal_purchase_items.quantity as quantity_net') {
                    $_data = '<div class="text-center quantity-' . $aRow['supplier_id'] . '">' . formatNumber($aRow['quantity_net']) . '</div>';
                    $total_quantity += $aRow['quantity_net'];
                    $grand_total_quantity += $aRow['quantity_net'];
                }
                if ($aColumns[$i] == 'tbl_internal_proposal_purchase_items.quantity as quantity') {
                    $_data = '<div class="text-center" quantity-purchase-' . $aRow['supplier_id'] . '>' . formatNumber($aRow['quantity']) . '</div>';
                    $total_quantity_purchase += $aRow['quantity'];
                    $grand_total_quantity_purchase += $aRow['quantity_net'];
                }
                if ($aColumns[$i] == '6') {
                    $_data = '<div>' . formatMoney($aRow['price']) . '</div>';
                }
                if ($aColumns[$i] == '7') {
                    $_data = '<div>' . formatMoney($aRow['price'] * $aRow['quantity']) . '</div>';
                    $total_amount += $aRow['price'] * $aRow['quantity'];
                    $grand_total_amount += $aRow['price'] * $aRow['quantity'];
                }

                $row[] = $_data;
            }

            $row['DT_RowClass'] = '';
            $output['aaData'][] = $row;
            if ($key == 0) {
                $checkExists = $aRow['supplier_id'];
            }
            if ((!empty($rResult[($key + 1)]) && $rResult[($key + 1)]['supplier_id'] != $checkExists) || empty($rResult[($key + 1)])) {
                $row = array(
                    '',
                    '',
                    '',
                    'Cộng',
                    '',
                    '',
                    '',
                    '<div class="text-center">' . formatNumber($total_quantity) . '</div>',
                    '<div class="text-center"">' . formatNumber($total_quantity_purchase) . '</div>',
                    '',
                    '',
                    '',
                );
                $row['DT_RowClass'] = 'alert-header bold warning';
                $output['aaData'][] = $row;
                $total_quantity = 0;
                $total_quantity_purchase = 0;
                $total_amount = 0;
                $checkExists = !empty($rResult[$key + 1]) ? $rResult[$key + 1]['supplier_id'] : 0;
            }
        }
        $output['grand_total_quantity'] = $grand_total_quantity;
        $output['grand_total_quantity_purchase'] = $grand_total_quantity_purchase;
        $output['grand_total_amount'] = $grand_total_amount;
        echo  json_encode($output);
    }

    public function purchase_order_report()
    {
        $this->app->get_table_data('purchase_order_report');
    }

    public function stock_card_report()
    {
        $this->app->get_table_data('stock_card_report');
    }

    public function warehouse_import_report()
    {
        $this->app->get_table_data('warehouse_import_report');
    }

    public function warehouse_import_report_mh()
    {
        $this->app->get_table_data('warehouse_import_report_mh');
    }

    public function warehouse_export_report()
    {
        // $this->app->get_table_data('warehouse_export_report');
        $this->app->get_table_data('warehouse_export_report_delivery');
    }

    public function warehouse_exporting_producion_report()
    {
        $this->app->get_table_data('warehouse_exporting_producion_report');
    }

    public function warehouse_other_report()
    {
        $this->app->get_table_data('warehouse_other_report');
    }

    public function warehouse_transfer_report()
    {
        $this->app->get_table_data('warehouse_transfer_report');
    }

    public function warehouse_adjusted_report()
    {
        $this->app->get_table_data('warehouse_adjusted_report');
    }

    public function warehouse_inventory_report()
    {
        $this->app->get_table_data('warehouse_inventory_report_vip');
    }

    public function warehouse_all_report()
    {
        $this->app->get_table_data('warehouse_all_report');
    }

    public function warehouse_all_report_product()
    {
        $this->app->get_table_data('warehouse_all_report_product');
    }

    public function limit_user_date_report()
    {
        $this->app->get_table_data('limit_user_date_report');
    }

    public function limit_user_date_btp_report()
    {
        $this->app->get_table_data('limit_user_date_btp_report');
    }

    public function inventory_nvl_hs_report()
    {
        $this->app->get_table_data('inventory_nvl_hs_report');
    }

    public function inventory_tp_hs_report()
    {
        $this->app->get_table_data('inventory_tp_hs_report');
    }

    public function inventory_btp_hs_report()
    {
        $this->app->get_table_data('inventory_btp_hs_report');
    }

    /* Sales reportts */
    public function sales()
    {
        $data['dataStaff'] = get_table_where('tblstaff');
        foreach ($data['dataStaff'] as $key => $value) {
            $data['dataStaff'][$key]['name'] = get_staff_full_name($value['staffid']);
        }
        $data['start_date'] = $this->input->get('start_date');
        $data['end_date'] = $this->input->get('end_date');
        $data['search'] = $this->input->get('search');
        $data['customers'] = $this->input->get('customers');
        $data['orders'] = $this->input->get('orders');
        $data['title'] = _l('sales_reports');

        $month = date('m');
        $year = date('Y');

        $month_old = $month - 1;
        $year_old = $year;
        if ($month_old == 0) {
            $month_old = 12;
            $year_old = $year - 1;
        }
        $today = date('Y-m-d');
        $month = date('m');
        $year  = date('Y');
        $day   = date('d');


        $date_condition       = "YEAR({{FIELD}}) = {$year} AND MONTH({{FIELD}}) = {$month}";
        $data['filter_label'] = 'Tháng ' . date('m/Y');
        $cond_eval_date = str_replace('{{FIELD}}', 'e.date', $date_condition);

        $sql_risk = "
            SELECT
                e.id,
                e.code,
                e.staff_id,
                e.big_risk,
                e.date,
                CONCAT(s.firstname, ' ', s.lastname) AS staff_name,
                r.name AS name_role,
                rl.code AS code_role_level
            FROM tbl_evaluation_employee e
            LEFT JOIN tblstaff s ON s.staffid = e.staff_id
            LEFT JOIN tblroles r ON r.roleid = e.role_id
            LEFT JOIN tbl_role_level rl ON rl.id = e.role_level_id
            WHERE e.type = 1
            AND e.big_risk > 0
            AND (e.rating IS NULL OR e.rating = '')
            AND {$cond_eval_date}
            ORDER BY e.big_risk DESC, e.date ASC
        ";
        $data['big_risk_list'] = $this->db->query($sql_risk)->result_array();

        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE YEAR(startdate) = {$year}
                  AND MONTH(startdate) = {$month}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                  )
                  AND NOT EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 0
                  )";
        $tasks_completed_process = $this->db->query($sql)->row()->total;
        $data['tasks_completed_process'] = $tasks_completed_process;


        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE YEAR(startdate) = {$year_old}
                  AND MONTH(startdate) = {$month_old}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                  )
                  AND NOT EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 0
                  )";
        $tasks_completed_process_old = $this->db->query($sql)->row()->total;
        $data['tasks_completed_process_old'] = $tasks_completed_process_old;


        $sql = "SELECT COUNT(*) as total
                FROM tblproduction_report
                WHERE YEAR(date) = {$year}
                  AND MONTH(date) = {$month}";
        $production_report = $this->db->query($sql)->row()->total;
        $data['production_report'] = $production_report;

        $sql = "SELECT COUNT(*) as total
                FROM tblproduction_report
                WHERE YEAR(date) = {$year_old}
                  AND MONTH(date) = {$month_old}";
        $production_report_old = $this->db->query($sql)->row()->total;
        $data['production_report_old'] = $production_report_old;

        // Task đang làm trong tháng (status != 5 = chưa hoàn thành)
        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE YEAR(startdate) = {$year}
                  AND MONTH(startdate) = {$month}
                  AND status != 5";
        $tasks_in_progress = $this->db->query($sql)->row()->total;
        $data['tasks_in_progress'] = $tasks_in_progress;

        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE YEAR(startdate) = {$year_old}
                  AND MONTH(startdate) = {$month_old}
                  AND status != 5";
        $tasks_in_progress_old = $this->db->query($sql)->row()->total;
        $data['tasks_in_progress_old'] = $tasks_in_progress_old;

        // Task có quy trình chưa check hết (tồn tại ít nhất 1 checklist item finished = 0)
        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE YEAR(startdate) = {$year}
                  AND MONTH(startdate) = {$month}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 0
                  )";
        $tasks_incomplete_process = $this->db->query($sql)->row()->total;
        $data['tasks_incomplete_process'] = $tasks_incomplete_process;

        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE YEAR(startdate) = {$year_old}
                  AND MONTH(startdate) = {$month_old}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 0
                  )";
        $tasks_incomplete_process_old = $this->db->query($sql)->row()->total;
        $data['tasks_incomplete_process_old'] = $tasks_incomplete_process_old;

        // Task có quy trình nhưng chưa check được cái nào (không có item nào finished = 1)
        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE YEAR(startdate) = {$year}
                  AND MONTH(startdate) = {$month}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                  )
                  AND NOT EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 1
                  )";
        $tasks_no_check = $this->db->query($sql)->row()->total;
        $data['tasks_no_check'] = $tasks_no_check;

        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE YEAR(startdate) = {$year_old}
                  AND MONTH(startdate) = {$month_old}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                  )
                  AND NOT EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 1
                  )";
        $tasks_no_check_old = $this->db->query($sql)->row()->total;
        $data['tasks_no_check_old'] = $tasks_no_check_old;

        // Task trễ hạn trong tháng (duedate < now VÀ status != 5) - logic từ tasks.php dòng 524
        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE YEAR(startdate) = {$year}
                  AND MONTH(startdate) = {$month}
                  AND status != 5
                  AND duedate IS NOT NULL
                  AND duedate < NOW()";
        $tasks_overdue = $this->db->query($sql)->row()->total;
        $data['tasks_overdue'] = $tasks_overdue;

        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE YEAR(startdate) = {$year_old}
                  AND MONTH(startdate) = {$month_old}
                  AND status != 5
                  AND duedate IS NOT NULL
                  AND duedate < NOW()";
        $tasks_overdue_old = $this->db->query($sql)->row()->total;
        $data['tasks_overdue_old'] = $tasks_overdue_old;




        // Type 2: Phiếu vi phạm (tblproduction_report có violate = 1)
        $sql = "SELECT COUNT(*) as total
                FROM tblproduction_report
                WHERE violate = 1
                  AND YEAR(date) = {$year}
                  AND MONTH(date) = {$month}";
        $data['p3_type2_count'] = $this->db->query($sql)->row()->total;

        // Type 1: BCKPH chưa hoàn thành (tblproduction_report còn staff_process = 0)
        $sql = "SELECT COUNT(*) as total
                FROM tblproduction_report
                WHERE id != 0
                  AND YEAR(date) = {$year}
                  AND MONTH(date) = {$month}
                  AND EXISTS (
                      SELECT 1
                      FROM tbl_process_production_report
                      WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                        AND tbl_process_production_report.staff_process = 0
                  )";
        $data['p3_type1_count'] = $this->db->query($sql)->row()->total;

        // Type 3: Công việc chưa hoàn thành (tbltasks có status != 5)
        $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE status != 5
                  AND YEAR(dateadded) = {$year}
                  AND MONTH(dateadded) = {$month}";
        $data['p3_type3_count'] = $this->db->query($sql)->row()->total;

        // Type 4: Audit fail (tbl_audit có checklist status = 'no')
        $sql = "SELECT COUNT(*) as total
                FROM tbl_audit
                WHERE YEAR(audit_date) = {$year}
                  AND MONTH(audit_date) = {$month}
                  AND EXISTS (
                      SELECT 1
                      FROM tbl_audit_checklist
                      WHERE tbl_audit_checklist.audit_id = tbl_audit.id
                        AND tbl_audit_checklist.status = 'no'
                  )";
        $data['p3_type4_count'] = $this->db->query($sql)->row()->total;

        // ===== TOP 5 NHÂN VIÊN NHIỀU NHẤT THEO TỪNG LOẠI =====

        // Top 5 nhân viên có nhiều phiếu vi phạm nhất (Type 2: violate = 1)
        $sql = "SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total
                FROM tblproduction_report pr
                LEFT JOIN tblstaff s ON s.staffid = pr.staff_responsible
                WHERE pr.violate = 1
                  AND YEAR(pr.date) = {$year}
                  AND MONTH(pr.date) = {$month}
                GROUP BY pr.staff_responsible
                ORDER BY total DESC
                LIMIT 5";
        $data['top5_type2'] = $this->db->query($sql)->result_array();

        // Top 5 nhân viên có nhiều BCKPH chưa hoàn thành nhất (Type 1: staff_process = 0)
        $sql = "SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total
                FROM tblproduction_report pr
                LEFT JOIN tblstaff s ON s.staffid = pr.staff_responsible
                WHERE pr.id != 0
                  AND YEAR(pr.date) = {$year}
                  AND MONTH(pr.date) = {$month}
                  AND EXISTS (
                      SELECT 1
                      FROM tbl_process_production_report
                      WHERE tbl_process_production_report.production_report_id = pr.id
                        AND tbl_process_production_report.staff_process = 0
                  )
                GROUP BY pr.staff_responsible
                ORDER BY total DESC
                LIMIT 5";
        $data['top5_type1'] = $this->db->query($sql)->result_array();

        // Top 5 nhân viên có nhiều công việc chưa hoàn thành nhất (Type 3: status != 5)
        $sql = "SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total
                FROM tbltasks t
                LEFT JOIN tblstaff s ON s.staffid = t.addedfrom
                WHERE t.status != 5
                  AND YEAR(t.dateadded) = {$year}
                  AND MONTH(t.dateadded) = {$month}
                GROUP BY t.addedfrom
                ORDER BY total DESC
                LIMIT 5";
        $data['top5_type3'] = $this->db->query($sql)->result_array();

        // Top 5 nhân viên có nhiều audit fail nhất (Type 4: tbl_audit_checklist status = 'no')
        $sql = "SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total
                FROM tbl_audit a
                JOIN tbl_room ON tbl_room.id = a.dept_id
                JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id
                JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                LEFT JOIN tblstaff s ON s.staffid = tblstaff_departments.staffid
                WHERE YEAR(a.audit_date) = {$year}
                  AND MONTH(a.audit_date) = {$month}
                  AND EXISTS (
                      SELECT 1
                      FROM tbl_audit_checklist
                      WHERE tbl_audit_checklist.audit_id = a.id
                        AND tbl_audit_checklist.status = 'no'
                  )
                GROUP BY tblstaff_departments.staffid
                ORDER BY total DESC
                LIMIT 5";
        $data['top5_type4'] = $this->db->query($sql)->result_array();

        // ===== DANH SÁCH ĐÁNH GIÁ NHÂN SỰ (Cột mốc 3/6/9/12 tháng) =====
        // Lấy tất cả phiếu đánh giá type=1 (nhân viên CT) trong năm hiện tại
        $current_year = date('Y');
        $sql_eval = "
        SELECT
            e.id,
            e.code,
            e.staff_id,
            e.point,
            e.rating,
            e.rating_list,
            e.date,
            CONCAT(s.firstname, ' ', s.lastname) AS staff_name,
            r.name AS name_role,
            rl.code AS code_role_level,
            (
                SELECT COUNT(*)
                FROM tbl_evaluation_employee e2
                WHERE e2.staff_id = e.staff_id
                  AND e2.type = 1
                  AND YEAR(e2.date) = {$current_year}
                  AND e2.date <= e.date
            ) AS phieu_so
        FROM tbl_evaluation_employee e
        LEFT JOIN tblstaff s ON s.staffid = e.staff_id
        LEFT JOIN tblroles r ON r.roleid = e.role_id
        LEFT JOIN tbl_role_level rl ON rl.id = e.role_level_id
        WHERE e.type = 1
          AND YEAR(e.date) = {$current_year}
        ORDER BY e.staff_id ASC, e.date ASC
    ";
        $eval_rows = $this->db->query($sql_eval)->result_array();

        // Map thứ tự phiếu sang cột mốc tháng
        $milestone_map = [1 => 3, 2 => 6, 3 => 9, 4 => 12];
        foreach ($eval_rows as &$row) {
            $so = (int)$row['phieu_so'];
            $row['milestone_month'] = $milestone_map[$so] ?? ($so * 3);
        }
        unset($row);
        $data['eval_list'] = $eval_rows;
        $this->load->view('admin/reports/sales', $data);
    }

    /* Customer report */
    public function customers_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $select = [
                get_sql_select_client_company(),
                '(SELECT COUNT(clientid) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
                '(SELECT SUM(subtotal) - SUM(discount_total) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
                '(SELECT SUM(total) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
            ];

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' ' . $custom_date_select . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
            }
            $by_currency = $this->input->post('report_currency');
            $currency = $this->currencies_model->get_base_currency();
            if ($by_currency) {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' AND currency =' . $by_currency . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
                $currency = $this->currencies_model->get($by_currency);
            }
            $aColumns = $select;
            $sIndexColumn = 'userid';
            $sTable = db_prefix() . 'clients';
            $where = [];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, [
                'userid',
            ]);
            $output = $result['output'];
            $rResult = $result['rResult'];
            $x = 0;
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 0) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } elseif ($aColumns[$i] == $select[2] || $aColumns[$i] == $select[3]) {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $_data = app_format_money($_data, $currency->name);
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            echo json_encode($output);
            die();
        }
    }

    public function payments_received()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('payment_modes_model');
            $payment_gateways = $this->payment_modes_model->get_payment_gateways(true);
            $select = [
                db_prefix() . 'invoicepaymentrecords.id',
                db_prefix() . 'invoicepaymentrecords.date',
                'invoiceid',
                get_sql_select_client_company(),
                'paymentmode',
                'transactionid',
                'note',
                'amount',
            ];
            $where = [
                'AND status != 5',
            ];

            $custom_date_select = $this->get_where_report_period(db_prefix() . 'invoicepaymentrecords.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = db_prefix() . 'invoicepaymentrecords';
            $join = [
                'JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid',
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid',
                'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'invoicepaymentrecords.paymentmode',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'number',
                'clientid',
                db_prefix() . 'payment_modes.name',
                db_prefix() . 'payment_modes.id as paymentmodeid',
                'paymentmethod',
                'deleted_customer_name',
            ]);

            $output = $result['output'];
            $rResult = $result['rResult'];

            $footer_data['total_amount'] = 0;
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($aColumns[$i] == 'paymentmode') {
                        $_data = $aRow['name'];
                        if (is_null($aRow['paymentmodeid'])) {
                            foreach ($payment_gateways as $gateway) {
                                if ($aRow['paymentmode'] == $gateway['id']) {
                                    $_data = $gateway['name'];
                                }
                            }
                        }
                        if (!empty($aRow['paymentmethod'])) {
                            $_data .= ' - ' . $aRow['paymentmethod'];
                        }
                    } elseif ($aColumns[$i] == db_prefix() . 'invoicepaymentrecords.id') {
                        $_data = '<a href="' . admin_url('payments/payment/' . $_data) . '" target="_blank">' . $_data . '</a>';
                    } elseif ($aColumns[$i] == db_prefix() . 'invoicepaymentrecords.date') {
                        $_data = _d($_data);
                    } elseif ($aColumns[$i] == 'invoiceid') {
                        $_data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow[$aColumns[$i]]) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
                    } elseif ($i == 3) {
                        if (empty($aRow['deleted_customer_name'])) {
                            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                        } else {
                            $row[] = $aRow['deleted_customer_name'];
                        }
                    } elseif ($aColumns[$i] == 'amount') {
                        $footer_data['total_amount'] += $_data;
                        $_data = app_format_money($_data, $currency->name);
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            $footer_data['total_amount'] = app_format_money($footer_data['total_amount'], $currency->name);
            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function proposals_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('proposals_model');

            $proposalsTaxes = $this->distinct_taxes('proposal');
            $totalTaxesColumns = count($proposalsTaxes);

            $select = [
                'id',
                'subject',
                'proposal_to',
                'date',
                'open_till',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                'status',
            ];

            $proposalsTaxesSelect = array_reverse($proposalsTaxes);

            foreach ($proposalsTaxesSelect as $key => $tax) {
                array_splice($select, 8, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="proposal" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'proposals.id) as total_tax_single_' . $key);
            }

            $where = [];
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('proposal_status')) {
                $statuses = $this->input->post('proposal_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('proposals_sale_agents')) {
                $agents = $this->input->post('proposals_sale_agents');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND assigned IN (' . implode(', ', $_agents) . ')');
                }
            }


            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = db_prefix() . 'proposals';
            $join = [];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'rel_id',
                'rel_type',
                'discount_percent',
            ]);

            $output = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0,
            ];

            foreach ($proposalsTaxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('proposals/list_proposals/' . $aRow['id']) . '" target="_blank">' . format_proposal_number($aRow['id']) . '</a>';

                $row[] = '<a href="' . admin_url('proposals/list_proposals/' . $aRow['id']) . '" target="_blank">' . $aRow['subject'] . '</a>';

                if ($aRow['rel_type'] == 'lead') {
                    $row[] = '<a href="#" onclick="init_lead(' . $aRow['rel_id'] . ');return false;" target="_blank" data-toggle="tooltip" data-title="' . _l('lead') . '">' . $aRow['proposal_to'] . '</a>' . '<span class="hide">' . _l('lead') . '</span>';
                } elseif ($aRow['rel_type'] == 'customer') {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['rel_id']) . '" target="_blank" data-toggle="tooltip" data-title="' . _l('client') . '">' . $aRow['proposal_to'] . '</a>' . '<span class="hide">' . _l('client') . '</span>';
                } else {
                    $row[] = '';
                }

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['open_till']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($proposalsTaxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]),
                        $currency->name
                    );
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = format_proposal_status($aRow['status']);
                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function estimates_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('estimates_model');

            $estimateTaxes = $this->distinct_taxes('estimate');
            $totalTaxesColumns = count($estimateTaxes);

            $select = [
                'number',
                get_sql_select_client_company(),
                'invoiceid',
                'YEAR(date) as year',
                'date',
                'expirydate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                'reference_no',
                'status',
            ];

            $estimatesTaxesSelect = array_reverse($estimateTaxes);

            foreach ($estimatesTaxesSelect as $key => $tax) {
                array_splice($select, 9, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="estimate" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'estimates.id) as total_tax_single_' . $key);
            }

            $where = [];
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('estimate_status')) {
                $statuses = $this->input->post('estimate_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('sale_agent_estimates')) {
                $agents = $this->input->post('sale_agent_estimates');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = db_prefix() . 'estimates';
            $join = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'estimates.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'estimates.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0,
            ];

            foreach ($estimateTaxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('estimates/list_estimates/' . $aRow['id']) . '" target="_blank">' . format_estimate_number($aRow['id']) . '</a>';

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                if ($aRow['invoiceid'] === null) {
                    $row[] = '';
                } else {
                    $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoiceid']) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
                }

                $row[] = $aRow['year'];

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['expirydate']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($estimateTaxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]),
                        $currency->name
                    );
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];


                $row[] = $aRow['reference_no'];

                $row[] = format_estimate_status($aRow['status']);

                $output['aaData'][] = $row;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }
            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    private function get_where_report_period($field = 'date')
    {
        $months_report = $this->input->post('report_months');
        $custom_date_select = '';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }

                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
            } elseif ($months_report == 'this_month') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
            } elseif ($months_report == 'this_year') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' .
                    date('Y-m-d', strtotime(date('Y-01-01'))) .
                    '" AND "' .
                    date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
            } elseif ($months_report == 'last_year') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' .
                    date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
                    '" AND "' .
                    date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'AND ' . $field . ' = "' . $from_date . '"';
                } else {
                    $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            }
        }

        return $custom_date_select;
    }

    public function items()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $v = $this->db->query('SELECT VERSION() as version')->row();
            // 5.6 mysql version don't have the ANY_VALUE function implemented.

            if ($v && strpos($v->version, '5.7') !== false) {
                $aColumns = [
                    'ANY_VALUE(description) as description',
                    'ANY_VALUE((SUM(' . db_prefix() . 'itemable.qty))) as quantity_sold',
                    'ANY_VALUE(SUM(rate*qty)) as rate',
                    'ANY_VALUE(AVG(rate*qty)) as avg_price',
                ];
            } else {
                $aColumns = [
                    'description as description',
                    '(SUM(' . db_prefix() . 'itemable.qty)) as quantity_sold',
                    'SUM(rate*qty) as rate',
                    'AVG(rate*qty) as avg_price',
                ];
            }

            $sIndexColumn = 'id';
            $sTable = db_prefix() . 'itemable';
            $join = ['JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'itemable.rel_id'];

            $where = ['AND rel_type="invoice"', 'AND status != 5', 'AND status=2'];

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            if ($this->input->post('sale_agent_items')) {
                $agents = $this->input->post('sale_agent_items');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'GROUP by description');

            $output = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total_amount' => 0,
                'total_qty' => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = $aRow['description'];
                $row[] = $aRow['quantity_sold'];
                $row[] = app_format_money($aRow['rate'], $currency->name);
                $row[] = app_format_money($aRow['avg_price'], $currency->name);
                $footer_data['total_amount'] += $aRow['rate'];
                $footer_data['total_qty'] += $aRow['quantity_sold'];
                $output['aaData'][] = $row;
            }

            $footer_data['total_amount'] = app_format_money($footer_data['total_amount'], $currency->name);

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function credit_notes()
    {
        if ($this->input->is_ajax_request()) {
            $credit_note_taxes = $this->distinct_taxes('credit_note');
            $totalTaxesColumns = count($credit_note_taxes);

            $this->load->model('currencies_model');

            $select = [
                'number',
                'date',
                get_sql_select_client_company(),
                'reference_no',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT ' . db_prefix() . 'creditnotes.total - (
                  (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.credit_id=' . db_prefix() . 'creditnotes.id)
                  +
                  (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'creditnote_refunds WHERE ' . db_prefix() . 'creditnote_refunds.credit_note_id=' . db_prefix() . 'creditnotes.id)
                  )
                ) as remaining_amount',
                'status',
            ];

            $where = [];

            $credit_note_taxes_select = array_reverse($credit_note_taxes);

            foreach ($credit_note_taxes_select as $key => $tax) {
                array_splice($select, 5, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="credit_note" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'creditnotes.id) as total_tax_single_' . $key);
            }

            $custom_date_select = $this->get_where_report_period();

            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');

            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            if ($this->input->post('credit_note_status')) {
                $statuses = $this->input->post('credit_note_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = db_prefix() . 'creditnotes';
            $join = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'creditnotes.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'creditnotes.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0,
                'remaining_amount' => 0,
            ];

            foreach ($credit_note_taxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }
            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('credit_notes/list_credit_notes/' . $aRow['id']) . '" target="_blank">' . format_credit_note_number($aRow['id']) . '</a>';

                $row[] = _d($aRow['date']);

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                $row[] = $aRow['reference_no'];

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($credit_note_taxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]),
                        $currency->name
                    );
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = app_format_money($aRow['remaining_amount'], $currency->name);
                $footer_data['remaining_amount'] += $aRow['remaining_amount'];

                $row[] = format_credit_note_status($aRow['status']);

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function invoices_report()
    {
        if ($this->input->is_ajax_request()) {
            $invoice_taxes = $this->distinct_taxes('invoice');
            $totalTaxesColumns = count($invoice_taxes);

            $this->load->model('currencies_model');
            $this->load->model('invoices_model');

            $select = [
                'number',
                get_sql_select_client_company(),
                'YEAR(date) as year',
                'date',
                'duedate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id) as credits_applied',
                '(SELECT total - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'invoices.id) - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id))',
                'status',
            ];

            $where = [
                'AND status != 5',
            ];

            $invoiceTaxesSelect = array_reverse($invoice_taxes);

            foreach ($invoiceTaxesSelect as $key => $tax) {
                array_splice($select, 8, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="invoice" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'invoices.id) as total_tax_single_' . $key);
            }

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('sale_agent_invoices')) {
                $agents = $this->input->post('sale_agent_invoices');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency = $this->input->post('report_currency');
            $totalPaymentsColumnIndex = (12 + $totalTaxesColumns - 1);

            if ($by_currency) {
                $_temp = substr($select[$totalPaymentsColumnIndex], 0, -2);
                $_temp .= ' AND currency =' . $by_currency . ')) as amount_open';
                $select[$totalPaymentsColumnIndex] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
                $select[$totalPaymentsColumnIndex] = $select[$totalPaymentsColumnIndex] .= ' as amount_open';
            }

            if ($this->input->post('invoice_status')) {
                $statuses = $this->input->post('invoice_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns = $select;
            $sIndexColumn = 'id';
            $sTable = db_prefix() . 'invoices';
            $join = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'invoices.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0,
                'applied_credits' => 0,
                'amount_open' => 0,
            ];

            foreach ($invoice_taxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                $row[] = $aRow['year'];

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['duedate']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($invoice_taxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]),
                        $currency->name
                    );
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = app_format_money($aRow['credits_applied'], $currency->name);
                $footer_data['applied_credits'] += $aRow['credits_applied'];

                $amountOpen = $aRow['amount_open'];
                $row[] = app_format_money($amountOpen, $currency->name);
                $footer_data['amount_open'] += $amountOpen;

                $row[] = format_invoice_status($aRow['status']);

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function expenses($type = 'simple_report')
    {
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['currencies'] = $this->currencies_model->get();

        $data['title'] = _l('expenses_report');
        if ($type != 'simple_report') {
            $this->load->model('expenses_model');
            $data['categories'] = $this->expenses_model->get_category();
            $data['years'] = $this->expenses_model->get_expenses_years();

            if ($this->input->is_ajax_request()) {
                $aColumns = [
                    'category',
                    'amount',
                    'expense_name',
                    'tax',
                    'tax2',
                    '(SELECT taxrate FROM ' . db_prefix() . 'taxes WHERE id=' . db_prefix() . 'expenses.tax)',
                    'amount as amount_with_tax',
                    'billable',
                    'date',
                    get_sql_select_client_company(),
                    'invoiceid',
                    'reference_no',
                    'paymentmode',
                ];
                $join = [
                    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'expenses.clientid',
                    'LEFT JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
                ];
                $where = [];
                $filter = [];
                include_once(APPPATH . 'views/admin/tables/includes/expenses_filter.php');
                if (count($filter) > 0) {
                    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
                }

                $by_currency = $this->input->post('currency');
                if ($by_currency) {
                    $currency = $this->currencies_model->get($by_currency);
                    array_push($where, 'AND currency=' . $by_currency);
                } else {
                    $currency = $this->currencies_model->get_base_currency();
                }

                $sIndexColumn = 'id';
                $sTable = db_prefix() . 'expenses';
                $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                    db_prefix() . 'expenses_categories.name as category_name',
                    db_prefix() . 'expenses.id',
                    db_prefix() . 'expenses.clientid',
                    'currency',
                ]);
                $output = $result['output'];
                $rResult = $result['rResult'];
                $this->load->model('currencies_model');
                $this->load->model('payment_modes_model');

                $footer_data = [
                    'tax_1' => 0,
                    'tax_2' => 0,
                    'amount' => 0,
                    'total_tax' => 0,
                    'amount_with_tax' => 0,
                ];

                foreach ($rResult as $aRow) {
                    $row = [];
                    for ($i = 0; $i < count($aColumns); $i++) {
                        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                            $_data = $aRow[strafter($aColumns[$i], 'as ')];
                        } else {
                            $_data = $aRow[$aColumns[$i]];
                        }
                        if ($aRow['tax'] != 0) {
                            $_tax = get_tax_by_id($aRow['tax']);
                        }
                        if ($aRow['tax2'] != 0) {
                            $_tax2 = get_tax_by_id($aRow['tax2']);
                        }
                        if ($aColumns[$i] == 'category') {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['category_name'] . '</a>';
                        } elseif ($aColumns[$i] == 'expense_name') {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['expense_name'] . '</a>';
                        } elseif ($aColumns[$i] == 'amount' || $i == 6) {
                            $total = $_data;
                            if ($i != 6) {
                                $footer_data['amount'] += $total;
                            } else {
                                if ($aRow['tax'] != 0 && $i == 6) {
                                    $total += ($total / 100 * $_tax->taxrate);
                                }
                                if ($aRow['tax2'] != 0 && $i == 6) {
                                    $total += ($aRow['amount'] / 100 * $_tax2->taxrate);
                                }
                                $footer_data['amount_with_tax'] += $total;
                            }

                            $_data = app_format_money($total, $currency->name);
                        } elseif ($i == 9) {
                            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                        } elseif ($aColumns[$i] == 'paymentmode') {
                            $_data = '';
                            if ($aRow['paymentmode'] != '0' && !empty($aRow['paymentmode'])) {
                                $payment_mode = $this->payment_modes_model->get($aRow['paymentmode'], [], false, true);
                                if ($payment_mode) {
                                    $_data = $payment_mode->name;
                                }
                            }
                        } elseif ($aColumns[$i] == 'date') {
                            $_data = _d($_data);
                        } elseif ($aColumns[$i] == 'tax') {
                            if ($aRow['tax'] != 0) {
                                $_data = $_tax->name . ' - ' . app_format_number($_tax->taxrate) . '%';
                            } else {
                                $_data = '';
                            }
                        } elseif ($aColumns[$i] == 'tax2') {
                            if ($aRow['tax2'] != 0) {
                                $_data = $_tax2->name . ' - ' . app_format_number($_tax2->taxrate) . '%';
                            } else {
                                $_data = '';
                            }
                        } elseif ($i == 5) {
                            if ($aRow['tax'] != 0 || $aRow['tax2'] != 0) {
                                if ($aRow['tax'] != 0) {
                                    $total = ($total / 100 * $_tax->taxrate);
                                    $footer_data['tax_1'] += $total;
                                }
                                if ($aRow['tax2'] != 0) {
                                    $total += ($aRow['amount'] / 100 * $_tax2->taxrate);
                                    $footer_data['tax_2'] += $total;
                                }
                                $_data = app_format_money($total, $currency->name);
                                $footer_data['total_tax'] += $total;
                            } else {
                                $_data = app_format_number(0);
                            }
                        } elseif ($aColumns[$i] == 'billable') {
                            if ($aRow['billable'] == 1) {
                                $_data = _l('expenses_list_billable');
                            } else {
                                $_data = _l('expense_not_billable');
                            }
                        } elseif ($aColumns[$i] == 'invoiceid') {
                            if ($_data) {
                                $_data = '<a href="' . admin_url('invoices/list_invoices/' . $_data) . '">' . format_invoice_number($_data) . '</a>';
                            } else {
                                $_data = '';
                            }
                        }
                        $row[] = $_data;
                    }
                    $output['aaData'][] = $row;
                }

                foreach ($footer_data as $key => $total) {
                    $footer_data[$key] = app_format_money($total, $currency->name);
                }

                $output['sums'] = $footer_data;
                echo json_encode($output);
                die;
            }
            $this->load->view('admin/reports/expenses_detailed', $data);
        } else {
            if (!$this->input->get('year')) {
                $data['current_year'] = date('Y');
            } else {
                $data['current_year'] = $this->input->get('year');
            }


            $data['export_not_supported'] = ($this->agent->browser() == 'Internet Explorer' || $this->agent->browser() == 'Spartan');

            $this->load->model('expenses_model');

            $data['chart_not_billable'] = json_encode($this->reports_model->get_stats_chart_data(
                _l('not_billable_expenses_by_categories'),
                [
                    'billable' => 0,
                ],
                [
                    'backgroundColor' => 'rgba(252,45,66,0.4)',
                    'borderColor' => '#fc2d42',
                ],
                $data['current_year']
            ));

            $data['chart_billable'] = json_encode($this->reports_model->get_stats_chart_data(
                _l('billable_expenses_by_categories'),
                [
                    'billable' => 1,
                ],
                [
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor' => '#84c529',
                ],
                $data['current_year']
            ));

            $data['expense_years'] = $this->expenses_model->get_expenses_years();

            if (count($data['expense_years']) > 0) {
                // Perhaps no expenses in new year?
                if (!in_array_multidimensional($data['expense_years'], 'year', date('Y'))) {
                    array_unshift($data['expense_years'], ['year' => date('Y')]);
                }
            }

            $data['categories'] = $this->expenses_model->get_category();

            $this->load->view('admin/reports/expenses', $data);
        }
    }

    public function expenses_vs_income($year = '')
    {
        if (!$this->perViewExpensesIncome) {
            access_denied();
            return;
        }

        $_expenses_years = [];
        $_years = [];
        $this->load->model('expenses_model');
        $expenses_years = $this->expenses_model->get_expenses_years();
        $payments_years = $this->reports_model->get_distinct_payments_years();

        foreach ($expenses_years as $y) {
            array_push($_years, $y['year']);
        }
        foreach ($payments_years as $y) {
            array_push($_years, $y['year']);
        }

        $_years = array_map('unserialize', array_unique(array_map('serialize', $_years)));

        if (!in_array(date('Y'), $_years)) {
            $_years[] = date('Y');
        }

        rsort($_years, SORT_NUMERIC);
        $data['report_year'] = $year == '' ? date('Y') : $year;

        $data['years'] = $_years;
        $data['chart_expenses_vs_income_values'] = json_encode($this->reports_model->get_expenses_vs_income_report($year));
        $data['title'] = _l('expenses_vs_income');
        $this->load->view('admin/reports/expenses_vs_income_new', $data);
    }

    /* Total income report / ajax chart*/
    public function total_income_report()
    {
        echo json_encode($this->reports_model->total_income_report());
    }

    public function report_by_payment_modes()
    {
        echo json_encode($this->reports_model->report_by_payment_modes());
    }

    public function report_by_customer_groups()
    {
        echo json_encode($this->reports_model->report_by_customer_groups());
    }

    /* Leads conversion monthly report / ajax chart*/
    public function leads_monthly_report($month)
    {
        echo json_encode($this->reports_model->leads_monthly_report($month));
    }

    private function distinct_taxes($rel_type)
    {
        return $this->db->query('SELECT DISTINCT taxname,taxrate FROM ' . db_prefix() . "item_tax WHERE rel_type='" . $rel_type . "' ORDER BY taxname ASC")->result_array();
    }

    public function productions()
    {
        $data['tnh'] = true;
        $data['start_date'] = $this->input->get('start_date');
        $data['end_date'] = $this->input->get('end_date');
        $data['search'] = $this->input->get('search');
        $data['title'] = lang('productions_reports');
        $this->load->view('admin/reports/productions', $data);
    }

    public function loadSalesReport()
    {
        $data['start_date'] = $this->input->post('start_date');
        $data['end_date'] = $this->input->post('end_date');
        $data['search'] = $this->input->post('search');
        $data['customers'] = $this->input->post('customers');
        $data['orders'] = $this->input->post('orders');
        $data['scripts'] = '';
        if (!empty($data['search'])) {
            if ($data['search'] == "delivery_schedules") {
                if (empty($data['start_date'])) {
                    $data['start_date'] = date('d/m/Y');
                }
                if (empty($data['end_date'])) {
                    $data['end_date'] = date('d/m/Y');
                }

                $this->db->select('tbl_order_item_shippings.date_shipping');
                $this->db->from('tbl_order_item_shippings');
                $this->db->where('date_shipping >=', to_sql_date($data['start_date']));
                $this->db->where('date_shipping <=', to_sql_date($data['end_date']));
                $this->db->group_by('tbl_order_item_shippings.date_shipping');
                $results = $this->db->get()->result_array();
                if (!empty($results)) {
                    $targets = 7;
                    $th = '';
                    $script = '';
                    foreach ($results as $key => $value) {
                        $th .= '<th>' . _d($value['date_shipping']) . '</th>';
                        $script .= '{
                            "render": function(data, type, row) {
                                if (data > 0) {
                                    return "<div class=\'text-center\'>"+tnhFormatNumber(data)+"</div>";
                                }
                                return "";
                            },
                            "targets": ' . $targets . ', "name": "date' . $key . '", "className": "text-center",
                        },';
                        $targets++;
                    }
                    $data['scripts'] = $script;
                    $data['th'] = $th;
                }
            }
        }
        $view = $this->input->post('view');
        $this->load->view('admin/reports/sales/' . $view, $data);
    }

    public function loadReport()
    {
        $data['start_date'] = $this->input->post('start_date');
        $data['end_date'] = $this->input->post('end_date');
        $data['search'] = $this->input->post('search');
        $view = $this->input->post('view');
        // if ($view == 'material_norms') {
        $this->load->view('admin/reports/productions/' . $view, $data);
        // }
    }

    public function getMaterialNorms()
    {
        if (!$this->perViewMaterialNorms) {
            accessDenied($js = true);
        }

        $searchProduct = $this->input->post('products');
        $searchVersion = $this->input->post('versions');

        $whereVersion = '';
        if (!empty($searchVersion)) {
            $whereVersion = " AND tbl_product_versions.versions like '%$searchVersion%'";
        }

        $version = "(
            SELECT count(*)
            FROM tbl_product_versions
            WHERE tbl_product_versions.product_id = tbl_products.id $whereVersion
            LIMIT 1
        )";

        // 'products' as type_hide
        $this->datatables->select("
            tbl_products.id as id,
            '' as versions,
            tbl_products.code as code,
            tbl_products.name as name,
            tbl_products.type_products as type,
            tblunits.unit as unit_name,
            '' as quantity,
            '' as quantity1,
            '' as quantity2,
            '' as quantity4,
            '' as quantity5,
            '' as quantity6,
        ", false)
            ->from('tbl_products')
            ->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');

        $this->datatables->where('tbl_products.type_products !=', 'semi_products_outside');
        $this->datatables->where("$version > 0");

        if (!empty($searchProduct)) {
            $searchProduct = str_replace('__products', '', $searchProduct);
            $searchProduct = str_replace(',', "','", $searchProduct);
            $this->datatables->where("tbl_products.id IN ('" . $searchProduct . "')");
        }
        // print_arrays($this->db->get_compiled_select('tbl_products'), FALSE);

        $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        $index = 0;
        foreach ($data->aaData as $key => $value) {
            $product_id = $value[0];

            $data->aaData[$index][0] = '<div class="group-products">' . ++$iDisplayStart . '</div>';
            $data->aaData[$index][1] = $value[1];
            $data->aaData[$index][2] = $value[2];
            $data->aaData[$index][3] = $value[3];
            $data->aaData[$index][4] = $value[4];
            $data->aaData[$index][5] = $value[5];

            $data->aaData[$index][6] = $value[6];
            $data->aaData[$index][7] = $value[7];
            $data->aaData[$index][8] = $value[8];
            $data->aaData[$index][9] = $value[9];
            $data->aaData[$index][10] = $value[10];
            $data->aaData[$index][11] = $value[11];
            // $data->aaData[$index][11] = $value[11];

            $this->db->select('tbl_product_versions.id, tbl_product_versions.versions');
            $this->db->from('tbl_product_versions');
            $this->db->where('tbl_product_versions.product_id', $product_id);
            $versions = $this->db->get()->result_array();
            foreach ($versions as $k => $val) {
                $index++;
                $version_id = $val['id'];

                $data->aaData[$index][0] = '';
                $data->aaData[$index][1] = '';
                $data->aaData[$index][2] = '<div class="text-primary">' . lang('tnh_versions') . ' ' . $val['versions'] . '</div>';
                $data->aaData[$index][3] = '';
                $data->aaData[$index][4] = '';
                $data->aaData[$index][5] = '';
                $data->aaData[$index][6] = '';
                $data->aaData[$index][7] = '';
                $data->aaData[$index][8] = '';
                $data->aaData[$index][9] = '';
                $data->aaData[$index][10] = '';
                $data->aaData[$index][11] = '';
                // $data->aaData[$index][11] = 'version';

                //get element version
                $this->db->select('tbl_versions_element.id, tbl_versions_element.element_name, tbl_versions_element.quantity');
                $this->db->from('tbl_versions_element');
                $this->db->where('tbl_versions_element.version_id', $version_id);
                $elements = $this->db->get()->result_array();
                $location = 1;
                if (!empty($elements)) {
                    foreach ($elements as $i => $el) {
                        $index++;
                        $element_id = $el['id'];
                        $data->aaData[$index][0] = '';
                        $data->aaData[$index][1] = '';
                        $data->aaData[$index][2] = '<b>' . $location . '. ' . $el['element_name'] . '</b>';
                        $data->aaData[$index][3] = $el['element_name'];
                        $data->aaData[$index][4] = '';
                        $data->aaData[$index][5] = '';
                        $data->aaData[$index][6] = '';
                        $data->aaData[$index][7] = '';
                        $data->aaData[$index][8] = '';
                        $data->aaData[$index][9] = '';
                        $data->aaData[$index][10] = '';
                        $data->aaData[$index][11] = '';
                        // $data->aaData[$index][11] = 'element';

                        //get element items
                        $product = "(
                            SELECT CONCAT(tbl_products.code, '|||', tbl_products.name)
                            FROM tbl_products
                            WHERE tbl_element_items.item_id = tbl_products.id
                        )";
                        $material = "(
                            SELECT CONCAT(tbl_materials.code, '|||', tbl_materials.name)
                            FROM tbl_materials
                            WHERE tbl_element_items.item_id = tbl_materials.id
                        )";

                        $this->db->select("
                            tbl_element_items.type, 
                            tbl_element_items.item_id, 
                            tbl_element_items.quantity, 
                            tblunits.unit as unit_name, 
                            IF(tbl_element_items.type = 'materials', $material, $product) as code_name,
                            tbl_element_items.landscape_print_size as landscape_print_size,
                            tbl_element_items.number_children_size as number_children_size,
                            tbl_element_items.quantity as quantity,
                            tbl_element_items.paper_exchange as paper_exchange,
                            tbl_element_items.quantity_compensation as quantity_compensation,
                            tbl_stages.name as stage_name
                        ", false);
                        $this->db->from('tbl_element_items');
                        $this->db->join('tblunits', 'tblunits.unitid = tbl_element_items.unit_id', 'left');
                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_element_items.stage_id', 'left');
                        $this->db->where('tbl_element_items.element_id', $element_id);
                        $items = $this->db->get()->result_array();

                        if (!empty($items)) {
                            $ltItem = 1;
                            foreach ($items as $a => $it) {
                                $index++;
                                $codeName = explode('|||', $it['code_name']);
                                $data->aaData[$index][0] = $value[2];
                                $data->aaData[$index][1] = $val['versions'];
                                // $data->aaData[$index][1] = '&nbsp&nbsp&nbsp&nbsp' . $location . '.' . $ltItem . '. ' . $codeName[0];
                                // $data->aaData[$index][1] = '' . $location . '.' . $ltItem . '. ' . $codeName[0];
                                $data->aaData[$index][2] = $codeName[0];
                                $data->aaData[$index][3] = $codeName[1];
                                $data->aaData[$index][4] = $it['type'];
                                $data->aaData[$index][5] = $it['unit_name'];
                                $data->aaData[$index][6] = '<div class="text-center">' . $it['landscape_print_size'] . '</div>';
                                $data->aaData[$index][7] = '<div class="text-center">' . $it['number_children_size'] . '</div>';
                                $data->aaData[$index][8] = '<div class="text-center">' . $it['quantity'] . '</div>';
                                $data->aaData[$index][9] = '<div class="text-center">' . formatNumber($it['paper_exchange']) . '</div>';
                                $data->aaData[$index][10] = '<div class="text-center">' . $it['quantity_compensation'] . '</div>';
                                $data->aaData[$index][11] = '<div class="text-center">' . $it['stage_name'] . '</div>';
                                // $data->aaData[$index][11] = 'items';
                                $ltItem++;
                            }
                        }

                        $location++;
                    }
                }
            }
            $index++;

            // $data->aaData[$key][0] = ++$iDisplayStart;
        }
        $data->aaData = array_values($data->aaData);
        $data->title_excel = [handlingTitleExcel($searchVersion)['title']];
        // print_arrays($data);
        echo json_encode($data);
    }

    public function getUsageMaterial()
    {
        if (!$this->perViewUsageMaterial) {
            accessDenied($js = true);
        }

        $conditionSuggestExporting = "(
            SELECT COUNT(*)
            FROM tbl_suggest_exporting
            WHERE tbl_suggest_exporting.productions_orders_details_id = tbl_productions_orders_details.id AND tbl_suggest_exporting.status_stock IS NOT NULL
            LIMIT 1
        )";
        $this->datatables->select("
            tbl_productions_orders_details.id as id,
            tbl_productions_orders_details.reference_no as reference_no,
            tbl_productions_orders_details.date_created as date,
            '' as item_code,
            '' as item_name,
            '' as type_item,
            '' as unit_name,
            '' as quantity_export,
            '' as quantity_reenter,
            '' as quantity_used,
        ", false)
            ->from('tbl_productions_orders_details');

        $this->datatables->where("$conditionSuggestExporting > 0");

        $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        $index = 0;
        foreach ($data->aaData as $key => $value) {
            $pod_id = $value[0];
            $numbers = ++$iDisplayStart;

            $data->aaData[$index][0] = $numbers;
            $data->aaData[$index][1] = $value[1];
            $data->aaData[$index][2] = $value[2];
            $data->aaData[$index][3] = $value[3];
            $data->aaData[$index][4] = $value[4];
            $data->aaData[$index][5] = $value[5];
            $data->aaData[$index][6] = $value[6];
            $data->aaData[$index][7] = $value[7];
            $data->aaData[$index][8] = $value[8];
            $data->aaData[$index][9] = $value[9];

            $reEnterQuantity = "(
                SELECT SUM(tbl_purchase_internal_items.quantity)
                FROM tbl_purchase_internal
                INNER JOIN tbl_purchase_internal_items ON tbl_purchase_internal_items.purchase_internal_id = tbl_purchase_internal.id
                WHERE tbl_purchase_internal.pod_id = '$pod_id' AND tbl_purchase_internal_items.type_item = tbl_suggest_exporting_items.type_item AND tbl_purchase_internal_items.item_id = tbl_suggest_exporting_items.item_id AND tbl_purchase_internal_items.unit_id = tbl_suggest_exporting_items.unit_parent_id
            )";

            $tableThuHoi = "(
                SELECT
                    tbl_purchase_internal_items.type_item as type_item,
                    tbl_purchase_internal_items.item_id as item_id,
                    SUM(tbl_purchase_internal_items.quantity) as quantity, 
                    SUM(tbl_purchase_internal_items.amount) as amount
                FROM tbl_purchase_internal
                INNER JOIN tbl_purchase_internal_items ON tbl_purchase_internal.id = tbl_purchase_internal_items.purchase_internal_id
                WHERE tbl_purchase_internal.pod_id = $pod_id
                GROUP BY tbl_purchase_internal_items.type_item, tbl_purchase_internal_items.item_id
            ) as tbthuhoi";

            $this->db->select("
                tbl_suggest_exporting_items.type_item,
                tbl_suggest_exporting_items.item_id,
                tbl_suggest_exporting_items.type_item as type_item,
                tbl_suggest_exporting_items.item_id as item_id,
                tbl_suggest_exporting_items.item_code as item_code,
                tbl_suggest_exporting_items.item_name as item_name,
                tblunits.unit as unit_name,
                SUM(tbl_suggest_exporting_items.quantity_exchange) as quantity_export,
                COALESCE(tbthuhoi.quantity, 0) as quantity_reenter
            ", false);
            $this->db->from('tbl_suggest_exporting');
            $this->db->join(
                'tbl_suggest_exporting_items',
                'tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id'
            );
            $this->db->join('tblunits', 'tblunits.unitid = tbl_suggest_exporting_items.unit_parent_id', 'left');
            $this->db->join(
                $tableThuHoi,
                'tbthuhoi.type_item = tbl_suggest_exporting_items.type_item AND tbthuhoi.item_id = tbl_suggest_exporting_items.item_id',
                'left'
            );
            $this->db->where('tbl_suggest_exporting.productions_orders_details_id', $pod_id);
            $this->db->where('tbl_suggest_exporting.status_stock IS NOT NULL');

            $this->db->group_by('tbl_suggest_exporting_items.type_item, tbl_suggest_exporting_items.item_id');
            $items = $this->db->get()->result_array();
            if (!empty($items)) {
                foreach ($items as $k => $val) {
                    if ($k != 0) {
                        $index++;
                    }
                    $data->aaData[$index][0] = $numbers;
                    $data->aaData[$index][1] = $value[1];
                    $data->aaData[$index][2] = $value[2];
                    $data->aaData[$index][3] = $val['item_code'];
                    $data->aaData[$index][4] = $val['item_name'];
                    $data->aaData[$index][5] = $val['type_item'];
                    $data->aaData[$index][6] = $val['unit_name'];
                    $data->aaData[$index][7] = $val['quantity_export'];
                    $data->aaData[$index][8] = $val['quantity_reenter'];
                    $data->aaData[$index][9] = $val['quantity_export'] - $val['quantity_reenter'];
                }
            } else {
                // print_arrays($data->aaData[$index]);
                // $data->aaData[$index][0] = 0;
            }


            $index++;
        }
        $data->aaData = array_values($data->aaData);
        // print_arrays($data);
        echo json_encode($data);
    }

    public function getProductionDetailed()
    {
        if (!$this->perViewProductionDetailed) {
            accessDenied($js = true);
        }

        $searchProduct = $this->input->post('products');
        $searchPurchaseProducts = $this->input->post('purchase_products');
        $searchStartDate = $this->input->post('start_date');
        $searchEndDate = $this->input->post('end_date');

        $products = "(
            SELECT tbl_products.id as id, tbl_products.code as item_code, tbl_products.name as item_name, tblunits.unit as unit_name
            FROM tbl_products
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
        ) as products";

        $this->datatables->select("
            tbl_purchase_products.id as id,
            tbl_purchase_products.reference_no as reference_no,
            tbl_purchase_products.date as date,
            products.item_code as item_code,
            products.item_name as item_name,
            products.unit_name as unit_name,
            tbl_purchase_product_items.quantity as quantity,
        ", false)
            ->from('tbl_purchase_products')
            ->join(
                'tbl_purchase_product_items',
                'tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id',
                'inner'
            )
            ->join(
                $products,
                'products.id = tbl_purchase_product_items.item_id AND tbl_purchase_product_items.type_item = "products"',
                'left'
            );

        if (!empty($searchPurchaseProducts)) {
            $this->datatables->where('tbl_purchase_products.reference_no', $searchPurchaseProducts);
        }

        if (!empty($searchProduct)) {
            $searchProduct = str_replace('__products', '', $searchProduct);
            $searchProduct = str_replace(',', "','", $searchProduct);
            $this->datatables->where("tbl_purchase_product_items.item_id IN ('" . $searchProduct . "')");
        }

        if (!empty($searchStartDate)) {
            $this->datatables->where(
                'DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=',
                to_sql_date($searchStartDate)
            );
        }
        if (!empty($searchEndDate)) {
            $this->datatables->where(
                'DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=',
                to_sql_date($searchEndDate)
            );
        }
        // print_arrays($this->db->get_compiled_select('tbl_products'), FALSE);

        $data = json_decode($this->datatables->generate());
        echo json_encode($data);
    }

    public function getSituationOrderExecution()
    {
        if (!$this->perViewSituationOrderExecution) {
            accessDenied($js = true);
        }

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $products = "(
            SELECT
                tbl_productions_orders_items.id as poi_id,
                tbl_productions_orders_items.items_id as items_id,
                tbl_productions_orders_items_stages.stage_id as stage_id,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tblunits.unit as unit_name,
                tbl_productions_plan_items.type_object as type_object,
                tbl_productions_plan_items.object_id as object_id,
                tbl_productions_orders.reference_no as reference_no_po
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
            INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_items.id AND tbl_productions_orders_items_stages.productions_orders_items_sub_id = 0
            INNER JOIN tbl_productions_plan_items ON tbl_productions_plan_items.id = tbl_productions_orders_items.production_plan_item_id

            LEFT JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id AND tbl_productions_orders_items.type_items = 'products'
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
            GROUP BY tbl_productions_orders_items.id, tbl_productions_orders_items.items_id, tbl_productions_orders_items_stages.stage_id
        ) as products";

        $this->datatables->select("
            tbl_stages.id as id,
            CONCAT(tbl_stages.name, '</br><b>(', tbl_stages.code,')</b>') as stage_name,
            products.item_code as item_code,
            products.item_name as item_name,
            products.unit_name as unit_name,
            '' as quantity,
            products.items_id as item_id,
            products.poi_id as poi_id,
            products.type_object as type_object,
            products.object_id as object_id,
            products.reference_no_po as reference_no_po
        ", false)
            ->from('tbl_stages')
            ->join($products, 'products.stage_id = tbl_stages.id', 'inner');

        $data = json_decode($this->datatables->generate());
        $index = -1;
        // $arrQuantity = ['command', 'execution'];
        $arrQuantity = ['execution'];
        $arrDate = [];
        if (!empty($start_date) && !empty($end_date)) {
            $dateBegin = $start_date;
            $start_date = to_sql_date(trim($start_date));
            $end_date = to_sql_date(trim($end_date));

            $date1 = new DateTime($start_date);
            $date2 = new DateTime($end_date);
            $diff = $date1->diff($date2);
            $days = $diff->days;

            for ($i = 0; $i <= $days; $i++) {
                $date = date('Y-m-d', strtotime(to_sql_date(trim($dateBegin)) . " + $i days"));
                array_push($arrDate, $date);
            }
        }

        $arrData = [];
        foreach ($data->aaData as $key => $value) {
            $stage_id = $value[0];
            $item_id = $value[6];
            $pod_id = $value[7];
            $type_object = $value[8];
            $object_id = $value[9];
            $reference_no_po = $value[10];

            $strOrders = '';
            if ($type_object == "orders") {
                $orders = get_table_where('tbl_orders', ['id' => $object_id], '', 'row_array');
                $strOrders = $orders['reference_no'];
                $customer = get_table_where(
                    'tblclients',
                    ['userid' => $orders['customer_id']],
                    '',
                    'row_array'
                )['company'];
            } else {
                $business = get_table_where('tbl_business_plan', ['id' => $object_id], '', 'row_array');
                $strOrders = $business['reference_no'];
                $customer = '';
            }

            $index++;
            $color = $arrQuantity[0] == 'command' ? '#5391cc' : '#ff6f00';

            $arrData[$index][0] = $value[0];
            $arrData[$index][1] = $strOrders;
            $arrData[$index][2] = $reference_no_po;
            $arrData[$index][3] = $customer;

            $arrData[$index][4] = $value[1];
            $arrData[$index][5] = $value[2];
            $arrData[$index][6] = $value[3];
            $arrData[$index][7] = $value[4];
            $arrData[$index][8] = '<div style="color: ' . $color . '">' . lang($arrQuantity[0]) . '</div>';
            $colStartAdd = 10;
            $totalQuantityDate = 0;
            $strDateQuantity = '';
            if (!empty($arrDate)) {
                foreach ($arrDate as $lc => $v) {
                    $query = "(
                        SELECT SUM(tbl_update_info_stage.quantity_success) as quantity_finished
                        FROM tbl_productions_orders_items
                        INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_items.id AND tbl_productions_orders_items_stages.productions_orders_items_sub_id = 0
                        INNER JOIN tbl_update_info_stage ON tbl_update_info_stage.productions_ois_id = tbl_productions_orders_items_stages.id
                        WHERE
                            tbl_productions_orders_items.items_id = $item_id AND tbl_productions_orders_items_stages.stage_id = $stage_id AND DATE_FORMAT(tbl_update_info_stage.date_end, '%Y-%m-%d') = '$v' AND (
                                tbl_update_info_stage.id = (
                                    SELECT MAX(t.id)
                                    FROM tbl_update_info_stage t
                                    WHERE DATE_FORMAT(t.date_end, '%Y-%m-%d') = '$v' AND tbl_productions_orders_items_stages.id = t.productions_ois_id
                                )
                            ) AND tbl_productions_orders_items.id = " . $pod_id . "
                    )";
                    $qtyInfo = $this->db->query($query)->row_array();
                    $quantityFinished = $qtyInfo['quantity_finished'];
                    if (empty($quantityFinished)) {
                        $quantityFinished = 0;
                    }
                    // $arrData[$index][$colStartAdd] = $quantityFinished;
                    $totalQuantityDate += $qtyInfo['quantity_finished'];

                    if ($quantityFinished > 0) {
                        $strDateQuantity .= '<div>' . _d($v) . ' - ' . formatNumber($quantityFinished) . '</div>';
                    }


                    $colStartAdd++;
                }
            }
            $totalQuantityDate = !empty($totalQuantityDate) ? $totalQuantityDate : 0;
            $arrData[$index][8] = $totalQuantityDate;
            $arrData[$index][9] = $strDateQuantity;
        }
        $data->aaData = $arrData;
        echo json_encode($data);
    }

    public function createdColDataTables()
    {
        $data = [];
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $table = '<table id="tb-situation-order-execution" class="table table-hover table-bordered table-condensed"><thead><tr>
            <th class="hide">' . lang('tnh_numbers') . '</th>
            <th>' . lang('Đơn hàng') . '</th>
            <th>' . lang('LSX') . '</th>
            <th>' . lang('Khách hàng') . '</th>
            <th>' . lang('stages') . '</th>
            <th>' . lang('tnh_product_code') . '</th>
            <th>' . lang('tnh_product_name') . '</th>
            <th>' . lang('unit') . '</th>
            <th>' . lang('quantity') . '</th>
            <th>' . lang('Ngày - Số lượng') . '</th>
        ';
        $days = 0;
        if (!empty($start_date) && !empty($end_date)) {
            $dateBegin = $start_date;
            $start_date = to_sql_date(trim($start_date));
            $end_date = to_sql_date(trim($end_date));

            $date1 = new DateTime($start_date);
            $date2 = new DateTime($end_date);
            $diff = $date1->diff($date2);
            $days = $diff->days;

            // for ($i = 0; $i <= $days; $i++) {
            //     $date = date('Y-m-d', strtotime(to_sql_date(trim($dateBegin)). " + $i days"));
            //     $table.= '<th>'._d($date).'</th>';
            // }
        }

        $table .= '</tr><thead><tbody></tbody></table>';
        $data['column'] = $table;
        $data['days'] = $days;
        echo json_encode($data);
    }

    public function getStatusProduction()
    {
        if (!$this->perViewStatusProduction) {
            accessDenied($js = true);
        }

        $products = $this->input->post('products');
        $productions_orders = $this->input->post('productions_orders');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $hasProduced = "(
            SELECT SUM(tbl_purchase_product_items.quantity)
            FROM tbl_productions_orders_details
            INNER JOIN tbl_purchase_products ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id
            WHERE tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id AND tbl_purchase_product_items.type_item = 'products' AND tbl_purchase_product_items.item_id = tbl_productions_orders_items.items_id
        )";

        $this->datatables->select("
            tbl_productions_orders.id as id,
            tbl_productions_orders.reference_no as reference_no,
            tbl_productions_orders.date as date,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tblunits.unit as unit_name,
            tbl_productions_orders_items.quantity as quantity_production,
            COALESCE($hasProduced, 0) as has_produced,
            (tbl_productions_orders_items.quantity - COALESCE($hasProduced, 0)) as rest,
        ", false)
            ->from('tbl_productions_orders')
            ->join(
                'tbl_productions_orders_items',
                'tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id'
            )
            ->join(
                'tbl_products',
                'tbl_products.id = tbl_productions_orders_items.items_id AND tbl_productions_orders_items.type_items = "products"',
                'left'
            )
            ->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');

        $custom[] = ['index' => 7, 'select' => 'has_produced'];
        $custom_select[7] = $hasProduced;
        $custom[] = ['index' => 8, 'select' => 'rest'];
        $custom_select[8] = "(tbl_productions_orders_items.quantity - COALESCE($hasProduced, 0))";
        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        if (!empty($productions_orders)) {
            $productions_orders = str_replace(',', "','", $productions_orders);
            $this->datatables->where("tbl_productions_orders.id IN ('" . $productions_orders . "')");
        }

        if (!empty($products)) {
            $products = str_replace('__products', '', $products);
            $products = str_replace(',', "','", $products);
            $this->datatables->where("tbl_productions_orders_items.items_id IN ('" . $products . "')");
        }

        if (!empty($start_date)) {
            $this->datatables->where(
                'DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") >=',
                to_sql_date($start_date)
            );
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }


        // $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        // $index = 0;
        // foreach ($data->aaData as $key => $value) {
        // }
        echo json_encode($data);
    }

    public function getMaterialProductionOrders()
    {
        if (!$this->perViewUseMlAcProductionOrders) {
            accessDenied($js = true);
        }

        $productions_orders = $this->input->post('productions_orders');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $purchaseInternal = "(
            SELECT SUM(tbl_purchase_internal_items.quantity)
            FROM tbl_productions_orders_details
            INNER JOIN tbl_purchase_internal ON tbl_purchase_internal.pod_id = tbl_productions_orders_details.id
            INNER JOIN tbl_purchase_internal_items ON tbl_purchase_internal_items.purchase_internal_id = tbl_purchase_internal.id
            WHERE tbl_purchase_internal.status = 'approved' AND tbl_purchase_internal_items.type_item = mt.type_item AND tbl_purchase_internal_items.item_id = mt.item_id
        )";

        $material = "(
            SELECT
                material.productions_orders_id as productions_orders_id,
                material.type_item as type_item,
                material.item_id as item_id,
                material.item_code as item_code,
                material.item_name as item_name,
                material.unit_id as unit_id,
                SUM(material.quantity_quota) as quantity_quota,
                SUM(material.quantity_exported) as quantity_exported
            FROM (
                SELECT
                    tbl_productions_orders_items_sub.productions_orders_id as productions_orders_id,
                    tbl_productions_orders_items_sub.type as type_item,
                    tbl_productions_orders_items_sub.item_id as item_id,
                    tbl_productions_orders_items_sub.item_code as item_code,
                    tbl_productions_orders_items_sub.item_name as item_name,
                    tbl_productions_orders_items_sub.unit_parent_id as unit_id,
                    tbl_productions_orders_items_sub.quantity_primary as quantity_quota,
                    0 as quantity_exported
                FROM tbl_productions_orders_items_sub
                WHERE (tbl_productions_orders_items_sub.type = 'materials' OR  tbl_productions_orders_items_sub.type = 'semi_products_outside')
                UNION ALL
                SELECT
                    tbl_productions_orders_details.productions_orders_id as productions_orders_id,
                    tbl_suggest_exporting_items.type_item as type_item,
                    tbl_suggest_exporting_items.item_id as item_id,
                    tbl_suggest_exporting_items.item_code as item_code,
                    tbl_suggest_exporting_items.item_name as item_name,
                    tbl_suggest_exporting_items.unit_parent_id as unit_id,
                    0 as quantity_quota,
                    tbl_suggest_exporting_items.quantity_exchange as quantity_exported
                FROM tbl_productions_orders_details
                INNER JOIN tbl_suggest_exporting ON tbl_suggest_exporting.productions_orders_details_id = tbl_productions_orders_details.id
                INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                WHERE tbl_suggest_exporting.warehouseman_id != 0
            ) as material
            GROUP BY material.productions_orders_id, material.type_item, material.item_id
        ) as mt";
        // AND tbl_suggest_exporting.type != 1

        $this->datatables->select("
            tbl_productions_orders.id as id,
            tbl_productions_orders.reference_no as reference_no,
            tbl_productions_orders.date as date,
            mt.item_code as item_code,
            mt.item_name as item_name,
            tblunits.unit as unit_name,
            mt.quantity_quota as quantity_quota,
            mt.quantity_exported as quantity_exported,
            concat(round(( COALESCE(mt.quantity_exported, 0)/COALESCE(mt.quantity_quota, 0) * 100 ), 2), '%') as percent,
            (COALESCE(mt.quantity_quota, 0) - COALESCE(mt.quantity_exported, 0)) as quantity_end,
            (COALESCE(mt.quantity_exported, 0) - COALESCE($purchaseInternal, 0)) as quantity_used,
            (COALESCE(mt.quantity_exported, 0) - COALESCE($purchaseInternal, 0) - COALESCE(mt.quantity_exported, 0)) as missing,
        ", false)
            ->from('tbl_productions_orders')
            ->join($material, 'mt.productions_orders_id = tbl_productions_orders.id', 'inner')
            ->join('tblunits', 'tblunits.unitid = mt.unit_id', 'left');

        if (!empty($productions_orders)) {
            $productions_orders = str_replace(',', "','", $productions_orders);
            $this->datatables->where("tbl_productions_orders.id IN ('" . $productions_orders . "')");
        }

        if (!empty($start_date)) {
            $this->datatables->where(
                'DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") >=',
                to_sql_date($start_date)
            );
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        $custom[] = ['index' => 8, 'select' => 'percent'];
        $custom[] = ['index' => 10, 'select' => 'quantity_used'];
        $custom_select[10] = '(COALESCE(mt.quantity_exported, 0) - COALESCE($purchaseInternal, 0))';
        $custom[] = ['index' => 11, 'select' => 'missing'];
        $custom_select[11] = "(COALESCE(mt.quantity_exported, 0) - COALESCE($purchaseInternal, 0) - COALESCE(mt.quantity_exported, 0))";
        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        // $this->datatables->where('tbl_productions_orders.id', 10);
        // $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        // $index = 0;
        // foreach ($data->aaData as $key => $value) {
        // }
        echo json_encode($data);
    }

    public function getGeneralProduction()
    {
        if (!$this->perViewGeneralProduction) {
            accessDenied($js = true);
        }

        $products = $this->input->post('products');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $this->datatables->select("
            tbl_products.id as id,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tblunits.unit as unit_name,
            SUM(tbl_productions_orders_items.quantity) as quantity
        ", false)
            ->from('tbl_productions_orders')
            ->join(
                'tbl_productions_orders_items',
                'tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id',
                'left'
            )
            ->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'inner')
            ->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');

        $this->datatables->group_by('tbl_products.id');

        if (!empty($products)) {
            $products = str_replace('__products', '', $products);
            $products = str_replace(',', "','", $products);
            $this->datatables->where("tbl_products.id IN ('" . $products . "')");
        }

        if (!empty($start_date)) {
            $this->datatables->where(
                'DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") >=',
                to_sql_date($start_date)
            );
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        $custom[] = ['index' => 4, 'select' => 'quantity'];
        $custom_select[4] = 'SUM(tbl_productions_orders_items)';
        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        // $this->datatables->where('tbl_productions_orders.id', 10);
        // $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        // $index = 0;
        // foreach ($data->aaData as $key => $value) {
        // }
        echo json_encode($data);
    }

    public function getOrdersQuotes()
    {
        if (!$this->perViewOrdersOfQuotes) {
            accessDenied($js = true);
        }

        $customers = $this->input->post('customers');
        $orders = $this->input->post('orders');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $products = "(
            SELECT tbl_products.id, tbl_products.code, tbl_products.name, tblunits.unit
            FROM tbl_products
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
        ) as products";

        $items = "(
            SELECT tblitems.id, tblitems.code, tblitems.name, tblunits.unit
            FROM tblitems
            LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit
        ) as items";

        $quantityQuotes = "(
            SELECT SUM(tbl_quote_items.quantity)
            FROM tbl_quote_items
            WHERE tbl_quote_items.quote_id = tbl_quotes.id AND tbl_quote_items.type_item = tbl_order_items.type_item AND tbl_quote_items.item_id = tbl_order_items.item_id
        )";

        $this->datatables->select("
            tblclients.company as customer,
            tbl_quotes.reference_no as reference_quotes,
            tbl_quotes.date as date_quotes,
            tbl_orders.reference_no as reference_orders,
            IF(tbl_order_items.type_item = 'products', products.code, items.code) as product_code,
            IF(tbl_order_items.type_item = 'products', products.name, items.name) as product_name,
            IF(tbl_order_items.type_item = 'products', products.unit, items.unit) as unit_name,
            $quantityQuotes as quantity_quotes,
            tbl_order_items.quantity as quantity_orders,
            tbl_order_items.quantity_delivery as quantity_delivery,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as end_delivery,
        ", false)
            ->from('tbl_quotes')
            ->join('tbl_orders', 'tbl_orders.quotes_id = tbl_quotes.id')
            ->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id')
            ->join(
                $products,
                'products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"',
                'left'
            )
            ->join($items, 'items.id = tbl_order_items.item_id AND tbl_order_items.type_item = "items"', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');

        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_quotes.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_quotes.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        if (!empty($customers)) {
            $customer = explode('__', $customers);
            $this->datatables->where('tbl_orders.customer_id', $customer[1]);
        }

        if (!empty($orders)) {
            $this->datatables->where('tbl_orders.id', $orders);
        }

        $custom[] = ['index' => 7, 'select' => 'quantity_quotes'];
        $custom_select[7] = $quantityQuotes;
        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);
        // $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        // $index = 0;
        // foreach ($data->aaData as $key => $value) {
        // }

        $data->title_excel = [handlingTitleExcel()['title']];
        echo json_encode($data);
    }

    public function getDeliverySchedules()
    {
        if (!$this->perViewDeliverySchedules) {
            accessDenied($js = true);
        }

        $customers = $this->input->post('customers');
        $orders = $this->input->post('orders');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $products = "(
            SELECT tbl_products.id, tbl_products.code, tbl_products.name, tblunits.unit
            FROM tbl_products
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
        ) as products";

        $items = "(
            SELECT tblitems.id, tblitems.code, tblitems.name, tblunits.unit
            FROM tblitems
            LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit
        ) as items";

        $select_custom_fields = "";
        $custom = [];
        $custom_select = [];
        if (!empty($start_date)) {
            // $end_date = date('d/m/Y');
            $this->db->select('tbl_order_item_shippings.date_shipping');
            $this->db->from('tbl_order_item_shippings');
            $this->db->where('date_shipping >=', to_sql_date($start_date));
            $this->db->where('date_shipping <=', to_sql_date($end_date));
            $this->db->group_by('tbl_order_item_shippings.date_shipping');
            $results = $this->db->get()->result_array();
            if (!empty($results)) {
                $target = 7;
                $th = '';
                $script = '';
                foreach ($results as $key => $value) {
                    $select = "
                        COALESCE((
                            SELECT
                                SUM(tbl_order_item_shippings.quantity_shipping)
                            FROM tbl_order_item_shippings
                            WHERE tbl_order_item_shippings.order_item_id = tbl_order_items.id AND DATE_FORMAT(tbl_order_item_shippings.date_shipping, '%Y-%m-%d') = '" . $value['date_shipping'] . "'
                        ), 0)
                    ";
                    $select_custom_fields .= ", " . $select . " as date" . $key;

                    $custom[] = [
                        'index' => $target,
                        'select' => "date" . $key,
                    ];
                    $custom_select[$target] = $select;
                    $target++;
                }
            }
        }

        $this->datatables->select("
            tbl_orders.id as order_id,
            tblclients.company as customer,
            tbl_orders.reference_no as reference_orders,
            tbl_orders.date as date,
            IF(tbl_order_items.type_item = 'products', products.code, items.code) as product_code,
            IF(tbl_order_items.type_item = 'products', products.name, items.name) as product_name,
            IF(tbl_order_items.type_item = 'products', products.unit, items.unit) as unit_name
            $select_custom_fields
        ", false)
            ->from('tbl_orders')
            ->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id')
            ->join(
                $products,
                'products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"',
                'left'
            )
            ->join($items, 'items.id = tbl_order_items.item_id AND tbl_order_items.type_item = "items"', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');

        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        if (!empty($customers)) {
            $customer = explode('__', $customers);
            $this->datatables->where('tbl_orders.customer_id', $customer[1]);
        }

        if (!empty($orders)) {
            $this->datatables->where('tbl_orders.id', $orders);
        }

        // print_arrays($this->db->get_compiled_select(), FALSE);

        // $custom[] = ['index' => 7, 'select' => 'quantity_quotes'];
        // $custom_select[7] = $quantityQuotes;
        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);
        // $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        // $index = 0;
        // foreach ($data->aaData as $key => $value) {
        // }
        echo json_encode($data);
    }

    public function getSalesOfOrderBK()
    {
        if (!$this->perViewSalesOfOrder) {
            accessDenied($js = true);
        }

        $customers = $this->input->post('customers');
        $orders = $this->input->post('orders');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $products = "(
            SELECT tbl_products.id, tbl_products.code, tbl_products.name, tblunits.unit
            FROM tbl_products
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
        ) as products";

        $items = "(
            SELECT tblitems.id, tblitems.code, tblitems.name, tblunits.unit
            FROM tblitems
            LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit
        ) as items";

        $tb_customer_group = "(
            SELECT 
                tblcustomer_groups.customer_id as customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
            FROM tblcustomers_groups
            JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
            GROUP BY tblcustomer_groups.customer_id
        ) tb_customer_group";

        // tbl_order_items.total_amount as amount_order,
        //'' as amount_delivery,
        //tbl_order_items.total_amount as amount_end,

        $this->datatables->select("
            tb_customer_group.name_group as customer_group,
            tblclients.company as customer_name,
            tbl_orders.reference_no as reference_quotes,
            tbl_type_orders.name as type_order,
            tbl_orders.date as date,
            IF(tbl_order_items.type_item = 'products', products.code, items.code) as product_code,
            IF(tbl_order_items.type_item = 'products', products.name, items.name) as product_name,
            IF(tbl_order_items.type_item = 'products', products.unit, items.unit) as unit_name,
            tbl_order_items.quantity as quantity_order,
            tbl_order_items.quantity_delivery as quantity_delivery,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_end,
            tbl_orders.grand_total as amount_order,
            tbl_orders.total_payment as amount_delivery,
            (tbl_orders.grand_total - tbl_orders.total_payment) as amount_end,
        ", false)
            ->from('tbl_orders')
            ->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id')
            ->join('tbl_type_orders', 'tbl_type_orders.id = tbl_orders.type_orders', 'left')
            ->join(
                $products,
                'products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"',
                'left'
            )
            ->join($items, 'items.id = tbl_order_items.item_id AND tbl_order_items.type_item = "items"', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left')
            ->join($tb_customer_group, 'tb_customer_group.customer_id = tblclients.userid', 'left');

        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }
        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        if (!empty($customers)) {
            $customer = explode('__', $customers);
            $this->datatables->where('tbl_orders.customer_id', $customer[1]);
        }

        if (!empty($orders)) {
            $this->datatables->where('tbl_orders.id', $orders);
        }

        $data = json_decode($this->datatables->generate());
        // $index = 0;
        // foreach ($data->aaData as $key => $value) {
        // }

        $data->{'title_excel'} = [handlingTitleExcel()['title']];
        echo json_encode($data);
    }

    public function getSalesOfOrder()
    {
        if (!$this->perViewSalesOfOrder) {
            accessDenied($js = true);
        }

        $customers  = $this->input->post('customers');
        $orders     = $this->input->post('orders');
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');

        // ONLY keep mandatory joins (orders + order_items). All LEFT JOIN tables will be fetched separately and mapped.
        // If later you need to filter by columns from those tables, convert the filter to EXISTS subqueries instead of LEFT JOINs.
        $this->datatables->select("
            tbl_orders.id                as order_id,
            tbl_orders.customer_id       as customer_id,
            tbl_orders.type_orders       as type_orders_id,
            tbl_orders.reference_no      as reference_quotes,
            tbl_orders.date              as date,
            (tbl_orders.grand_total * tbl_orders.amount_to_vnd) as amount_order,
            tbl_orders.total_payment     as amount_delivery,
            ((tbl_orders.grand_total * tbl_orders.amount_to_vnd) - tbl_orders.total_payment) as amount_end,

            tbl_order_items.id           as order_item_id,
            tbl_order_items.type_item    as type_item,
            tbl_order_items.item_id      as item_id,
            tbl_order_items.quantity     as quantity_order,
            tbl_order_items.quantity_delivery as quantity_delivery,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_end
        ", false)
            ->from('tbl_orders')
            ->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');

        $this->datatables->where('(tbl_orders.is_cancel = 0 OR tbl_orders.is_end = 1)');
        // Filters (only on base tables). If later needing product/item filters -> use EXISTS instead of LEFT JOIN.
        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }
        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }
        if (!empty($customers)) {
            $customer = explode('__', $customers);
            $this->datatables->where('tbl_orders.customer_id', $customer[1]);
        }
        if (!empty($orders)) {
            $this->datatables->where('tbl_orders.id', $orders);
        }

        $raw = json_decode($this->datatables->generate());

        if (empty($raw->aaData)) {
            $raw->{'title_excel'} = [handlingTitleExcel()['title']];
            echo json_encode($raw);
            return;
        }

        // Collect distinct ids for batch fetching
        $customerIds   = [];
        $typeOrderIds  = [];
        $productIds    = [];
        $itemIds       = [];

        foreach ($raw->aaData as $r) {
            // indexes follow select order above
            $customerIds[]  = $r[1];
            $typeOrderIds[] = $r[2];
            $type_item      = $r[9];
            $item_id        = $r[10];
            if ($type_item === 'products') {
                $productIds[] = $item_id;
            } elseif ($type_item === 'items') {
                $itemIds[] = $item_id;
            }
        }

        $customerIds  = array_values(array_unique(array_filter($customerIds)));
        $typeOrderIds = array_values(array_unique(array_filter($typeOrderIds)));
        $productIds   = array_values(array_unique(array_filter($productIds)));
        $itemIds      = array_values(array_unique(array_filter($itemIds)));

        // Fetch customers
        $customersMap = [];
        if ($customerIds) {
            $rows = $this->db->select('userid, company')->where_in('userid', $customerIds)->get('tblclients')->result_array();
            foreach ($rows as $c) {
                $customersMap[$c['userid']] = $c['company'];
            }
        }

        // Fetch customer groups (group concat)
        $customerGroupsMap = [];
        if ($customerIds) {
            $rows = $this->db->select('tblcustomer_groups.customer_id, GROUP_CONCAT(tblcustomers_groups.name SEPARATOR ", ") as name_group')
                ->from('tblcustomer_groups')
                ->join('tblcustomers_groups', 'tblcustomers_groups.id = tblcustomer_groups.groupid')
                ->where_in('tblcustomer_groups.customer_id', $customerIds)
                ->group_by('tblcustomer_groups.customer_id')
                ->get()->result_array();
            foreach ($rows as $cg) {
                $customerGroupsMap[$cg['customer_id']] = $cg['name_group'];
            }
        }

        // Fetch type orders
        $typeOrdersMap = [];
        if ($typeOrderIds) {
            $rows = $this->db->select('id, name')->where_in('id', $typeOrderIds)->get('tbl_type_orders')->result_array();
            foreach ($rows as $t) {
                $typeOrdersMap[$t['id']] = $t['name'];
            }
        }

        // Fetch products (with unit)
        $productsMap = [];
        if ($productIds) {
            $rows = $this->db->select('tbl_products.id, tbl_products.code, tbl_products.name, tblunits.unit')
                ->from('tbl_products')
                ->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left')
                ->where_in('tbl_products.id', $productIds)
                ->get()->result_array();
            foreach ($rows as $p) {
                $productsMap[$p['id']] = $p;
            }
        }

        // Fetch items (with unit)
        $itemsMap = [];
        if ($itemIds) {
            $rows = $this->db->select('tblitems.id, tblitems.code, tblitems.name, tblunits.unit')
                ->from('tblitems')
                ->join('tblunits', 'tblunits.unitid = tblitems.unit', 'left')
                ->where_in('tblitems.id', $itemIds)
                ->get()->result_array();
            foreach ($rows as $i) {
                $itemsMap[$i['id']] = $i;
            }
        }

        // Rebuild aaData into final structure (remove raw technical columns, map values)
        $finalData = [];
        $order_id_group = null;
        foreach ($raw->aaData as $row) {
            $order_id        = $row[0];
            $customer_id     = $row[1];
            $type_orders_id  = $row[2];
            $reference_no    = $row[3];
            $date            = $row[4];
            $amount_order    = $row[5];
            $amount_delivery = $row[6];
            $amount_end      = $row[7];

            $order_item_id   = $row[8];
            $type_item       = $row[9];
            $item_id         = $row[10];
            $quantity_order  = $row[11];
            $quantity_delivery = $row[12];
            $quantity_end    = $row[13];

            // Map product/item
            $product_code = $product_name = $unit_name = '';
            if ($type_item === 'products' && isset($productsMap[$item_id])) {
                $product_code = $productsMap[$item_id]['code'];
                $product_name = $productsMap[$item_id]['name'];
                $unit_name    = $productsMap[$item_id]['unit'];
            } elseif ($type_item === 'items' && isset($itemsMap[$item_id])) {
                $product_code = $itemsMap[$item_id]['code'];
                $product_name = $itemsMap[$item_id]['name'];
                $unit_name    = $itemsMap[$item_id]['unit'];
            }

            if ($order_id_group !== $order_id) {
                $order_id_group = $order_id;
            } else {
                $amount_order = 0;
                $amount_delivery = 0;
                $amount_end = 0;
            }

            $finalData[] = [
                isset($customerGroupsMap[$customer_id]) ? $customerGroupsMap[$customer_id] : '',
                isset($customersMap[$customer_id]) ? $customersMap[$customer_id] : '',
                $reference_no,
                isset($typeOrdersMap[$type_orders_id]) ? $typeOrdersMap[$type_orders_id] : '',
                $date,
                $product_code,
                $product_name,
                $unit_name,
                $quantity_order,
                $quantity_delivery,
                $quantity_end,
                $amount_order,
                $amount_delivery,
                $amount_end,
            ];
        }

        $raw->aaData = $finalData;
        $raw->{'title_excel'} = [handlingTitleExcel()['title']];
        echo json_encode($raw);
    }

    public function getNearestSellingPrice()
    {
        if (!$this->perViewNearestSellingPrice) {
            accessDenied($js = true);
        }

        $products = "(
            SELECT tbl_products.id, tbl_products.code, tbl_products.name, tblunits.unit, tbl_category_products.name as category_name
            FROM tbl_products
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
            LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id
        ) as products";

        $items = "(
            SELECT tblitems.id, tblitems.code, tblitems.name, tblunits.unit, tblcategories.category as category_name
            FROM tblitems
            LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit
            LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id
        ) as items";

        $queryMax = "(
            SELECT MAX(tbl_order_items.id) as order_item_id
            FROM tbl_order_items
            GROUP BY tbl_order_items.type_item, tbl_order_items.item_id
        ) as oi";

        $this->datatables->select("
            IF(tbl_order_items.type_item = 'products', products.category_name, items.category_name) as category_name,
            IF(tbl_order_items.type_item = 'products', products.code, items.code) as product_code,
            IF(tbl_order_items.type_item = 'products', products.name, items.name) as product_name,
            tbl_order_items.type_item as type,
            IF(tbl_order_items.type_item = 'products', products.unit, items.unit) as unit_name,
            tbl_order_items.quantity as quantity,
            tbl_order_items.price as price,
        ", false)
            ->from('tbl_order_items')
            ->join($queryMax, 'oi.order_item_id = tbl_order_items.id')
            ->join(
                $products,
                'products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"',
                'left'
            )
            ->join($items, 'items.id = tbl_order_items.item_id AND tbl_order_items.type_item = "items"', 'left');

        $this->datatables->group_by('tbl_order_items.type_item, tbl_order_items.item_id');
        $data = json_decode($this->datatables->generate());
        $data->{'title_excel'} = [handlingTitleExcel()['title']];
        echo json_encode($data);
    }

    public function getReturnedGoods()
    {
        if (!$this->perViewReturnedGoods) {
            accessDenied($js = true);
        }
        $customers = $this->input->post('customers');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $products = "(
            SELECT tbl_products.id, tbl_products.code, tbl_products.name, tblunits.unit
            FROM tbl_products
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
        ) as products";

        $items = "(
            SELECT tblitems.id, tblitems.code, tblitems.name, tblunits.unit
            FROM tblitems
            LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit
        ) as items";

        $this->datatables->select("
            tbl_returned_goods.customer_name as customer_name,
            tbl_returned_goods.reference_no as reference_no,
            DATE_FORMAT(tbl_returned_goods.date, '%d/%m/%Y') as date,
            tbl_returned_goods_items.item_code as item_code,
            tbl_returned_goods_items.item_name as item_name,
            tbl_returned_goods_items.quantity as quantity,
            tbl_returned_goods_items.price as price,
            tbl_returned_goods_items.amount as amount,
        ", false)
            ->from('tbl_returned_goods')
            ->join('tbl_returned_goods_items', 'tbl_returned_goods_items.returned_goods_id = tbl_returned_goods.id');
        // ->join($products, 'products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"', 'left')
        // ->join($items, 'items.id = tbl_order_items.item_id AND tbl_order_items.type_item = "items"', 'left');

        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        if (!empty($customers)) {
            $customer = explode('__', $customers);
            $this->datatables->where('tbl_returned_goods.customer_id', $customer[1]);
        }

        $data = json_decode($this->datatables->generate());

        $data->title_excel = [handlingTitleExcel()['title']];
        echo json_encode($data);
    }

    public function getProductionScheduleOrder()
    {
        if (!$this->perViewProductionScheduleByOrder) {
            accessDenied($js = true);
        }

        $customers = $this->input->post('customers');
        $products = $this->input->post('products');
        $orders = $this->input->post('orders');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $hasProduced = "COALESCE((
            SELECT SUM(tbl_productions_orders_items.quantity)
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.production_plan_item_id = tbl_productions_plan_items.id
            WHERE tbl_productions_plan_items.type_object = 'orders' AND tbl_productions_plan_items.item_object_id = tbl_order_items.id
        ), 0)";

        $hasWarehoused = "COALESCE((
            SELECT SUM(tbl_purchase_products.total_quantity)
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.production_plan_item_id = tbl_productions_plan_items.id
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
            WHERE tbl_productions_plan_items.type_object = 'orders' AND tbl_productions_plan_items.item_object_id = tbl_order_items.id AND tbl_purchase_products.warehouseman_id > 0
        ), 0)";

        $this->datatables->select("
            tblclients.company as customer_name,
            tbl_orders.reference_no as reference_order,
            tbl_products.code as product_code,
            tbl_products.name as product_name,
            tblunits.unit as unit_name,
            tbl_order_items.quantity as quantity,
            $hasProduced as has_produced,
            $hasWarehoused as has_warehoused,
            (tbl_order_items.quantity - $hasWarehoused) as rest,
        ", false)
            ->from('tbl_orders')
            ->join(
                'tbl_order_items',
                'tbl_order_items.order_id = tbl_orders.id AND tbl_order_items.type_item = "products"'
            )
            ->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id')
            ->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');

        if (!empty($customers)) {
            $customers = str_replace('customers__', '', $customers);
            $this->datatables->where('tbl_orders.customer_id', $customers);
        }

        if (!empty($products)) {
            $products = str_replace('__products', '', $products);
            $products = str_replace(',', "','", $products);
            $this->datatables->where("tbl_products.id IN ('" . $products . "')");
        }

        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        if (!empty($orders)) {
            $this->datatables->where('tbl_orders.id', $orders);
        }

        $custom[] = ['index' => 6, 'select' => 'has_produced'];
        $custom_select[6] = "$hasProduced";
        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        $data = json_decode($this->datatables->generate());
        echo json_encode($data);
    }

    public function getOrderStatus()
    {
        if (!$this->perViewOrderStatus) {
            accessDenied($js = true);
        }

        $customers = $this->input->post('customers');
        $orders = $this->input->post('orders');
        $start_date_delivery = $this->input->post('start_date_delivery');
        $end_date_delivery = $this->input->post('end_date_delivery');
        $products_search = $this->input->post('products_search');

        $products = "(
            SELECT tbl_products.id, tbl_products.code, tbl_products.name, tblunits.unit, tbl_category_products.name as category_name
            FROM tbl_products
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
            LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id
        ) as products";

        $items = "(
            SELECT tblitems.id, tblitems.code, tblitems.name, tblunits.unit, tblcategories.category as category_name
            FROM tblitems
            LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit
            LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id
        ) as items";

        $whereDelLastPeriod = "";
        $deliveredThisPeriod = 0;
        if (!empty($start_date_delivery)) {
            $start_date_delivery = to_sql_date($start_date_delivery);
            $end_date_delivery = !empty($end_date_delivery) ? to_sql_date($end_date_delivery) : date('Y-m-d');
            $whereDelLastPeriod = " AND DATE_FORMAT(tbl_deliveries.date, '%Y-%m-%d') < '$start_date_delivery'";
            $whereDelThisPeriod = " AND DATE_FORMAT(tbl_deliveries.date, '%Y-%m-%d') >= '$start_date_delivery' AND DATE_FORMAT(tbl_deliveries.date, '%Y-%m-%d') <= '$end_date_delivery'";

            $deliveredThisPeriod = "COALESCE((
                SELECT SUM(tbl_delivery_items.quantity)
                FROM tbl_deliveries
                INNER JOIN tbl_delivery_items ON tbl_deliveries.id = tbl_delivery_items.delivery_id
                WHERE tbl_delivery_items.order_item_id = tbl_order_items.id $whereDelThisPeriod
                GROUP BY tbl_delivery_items.type_item, tbl_delivery_items.item_id
            ), 0)";
        }

        $deliveredLastPeriod = "COALESCE((
            SELECT SUM(tbl_delivery_items.quantity)
            FROM tbl_deliveries
            INNER JOIN tbl_delivery_items ON tbl_deliveries.id = tbl_delivery_items.delivery_id
            WHERE tbl_delivery_items.order_item_id = tbl_order_items.id $whereDelLastPeriod
            GROUP BY tbl_delivery_items.type_item, tbl_delivery_items.item_id
        ), 0)";

        $this->datatables->select("
            tblclients.company as customer_name,
            tbl_orders.reference_no as reference_order,
            tbl_orders.date as date,
            IF(tbl_order_items.type_item = 'products', products.code, items.code) as item_code,
            IF(tbl_order_items.type_item = 'products', products.name, items.name) as item_name,
            IF(tbl_order_items.type_item = 'products', products.unit, items.unit) as unit_name,
            tbl_order_items.quantity as quantity_order,
            $deliveredLastPeriod as delivered_last_period,
            $deliveredThisPeriod as delivered_this_period,
            ($deliveredLastPeriod + $deliveredThisPeriod) as total_delivery,
            (tbl_order_items.quantity - ($deliveredLastPeriod + $deliveredThisPeriod)) as rest,
        ", false)
            ->from('tbl_orders')
            ->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id')
            ->join(
                $products,
                'products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"',
                'left'
            )
            ->join($items, 'items.id = tbl_order_items.item_id AND tbl_order_items.type_item = "items"', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');

        if (!empty($customers)) {
            $customers = str_replace('customers__', '', $customers);
            $this->datatables->where('tbl_orders.customer_id', $customers);
        }

        if (!empty($products_search)) {
            $products_search = explode('__', $products_search);
            $this->datatables->where('products.id', $products_search[0]);
        }

        if (!empty($orders)) {
            $this->datatables->where('tbl_orders.id', $orders);
        }

        $data = json_decode($this->datatables->generate());

        $data->{'title_excel'} = [handlingTitleExcel()['title']];
        echo json_encode($data);
    }

    public function getSalesAnalysis()
    {
        if (!$this->perViewSalesAnalysis) {
            accessDenied($js = true);
        }

        $customers = $this->input->post('customers');
        $orders = $this->input->post('orders');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $products = "(
            SELECT tbl_products.id, tbl_products.code, tbl_products.name, tblunits.unit, tbl_category_products.name as category_name, tbl_products.price_import
            FROM tbl_products
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
            LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id
        ) as products";

        $items = "(
            SELECT tblitems.id, tblitems.code, tblitems.name, tblunits.unit, tblcategories.category as category_name, tblitems.price_import
            FROM tblitems
            LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit
            LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id
        ) as items";

        $this->datatables->select("
            tbl_orders.id as id,
            tbl_order_items.id as order_item_id,
            tblclients.company as customer_name,
            tbl_orders.reference_no as reference_order,
            tbl_orders.date as date,
            IF(tbl_order_items.type_item = 'products', products.code, items.code) as item_code,
            IF(tbl_order_items.type_item = 'products', products.name, items.name) as item_name,
            IF(tbl_order_items.type_item = 'products', products.unit, items.unit) as unit_name,
            tbl_order_items.quantity as quantity,
            tbl_order_items.price as price,
            tbl_order_items.amount as subtotal,
            '' as cost_price,
            '' as gross_profit,
            IF(tbl_order_items.type_item = 'products', products.price_import, items.price_import) as price_import,
            tbl_order_items.type_item as type_item
        ", false)
            ->from('tbl_orders')
            ->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id')
            ->join(
                $products,
                'products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"',
                'left'
            )
            ->join($items, 'items.id = tbl_order_items.item_id AND tbl_order_items.type_item = "items"', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');

        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        if (!empty($customers)) {
            $customer = explode('__', $customers);
            $this->datatables->where('tbl_orders.customer_id', $customer[1]);
        }

        if (!empty($orders)) {
            $this->datatables->where('tbl_orders.id', $orders);
        }

        // if (!empty($customers)) {
        //     $customers = str_replace('customers__', '', $customers);
        //     $this->datatables->where('tbl_orders.customer_id', $customers);
        // }

        $data = json_decode($this->datatables->generate());
        $index = 0;
        foreach ($data->aaData as $key => $value) {

            $order_id = $value[0];
            $order_item_id = $value[1];
            $subtotal = $value[10];
            $costPrice = 0;
            $grossProfit = 0;
            $priceImport = $value[13];
            $typeItems = $value[14];

            if ($typeItems == "products") {
                //Lấy danh sach export warehouse
                $this->db->select("tbl_export_warehous_items.id_import", false);
                $this->db->from('tbl_delivery_items');
                $this->db->join(
                    'tbl_export_warehous_items',
                    'tbl_export_warehous_items.delivery_item_id = tbl_delivery_items.id'
                );
                $this->db->where('tbl_delivery_items.order_item_id', $order_item_id);
                $this->db->where('(tbl_export_warehous_items.id_import IS not null AND tbl_export_warehous_items.id_import != "")');
                $exportWarehouseItems = $this->db->get()->result_array();
                if (!empty($exportWarehouseItems)) {
                    foreach ($exportWarehouseItems as $k => $val) {
                        $id_import = explode('|', $val['id_import']);
                        if (!empty($id_import)) {
                            foreach ($id_import as $i => $v) {
                                if (empty($v)) {
                                    continue;
                                }
                                $arr = explode('-', $v);
                                $warehouseProductId = $arr[0];
                                $quantityExport = $arr[1];

                                //Lấy giá vốn cho từng số lượng xuất kho
                                $this->db->select('tblwarehouse_product.price');
                                $this->db->from('tblwarehouse_product');
                                $this->db->where('tblwarehouse_product.id', $warehouseProductId);
                                $warehouseProduct = $this->db->get()->row_array();
                                $price = 0;
                                if (!empty($warehouseProduct)) {
                                    $price = $warehouseProduct['price'];
                                }
                                $amount = $quantityExport * $price;
                                $costPrice += $amount;
                            }
                        }
                    }
                }
            } else {
                $qty = $value[8];
                $costPrice = $priceImport * $qty;
            }

            $grossProfit = $subtotal - $costPrice;

            $data->aaData[$index][0] = $value[2];
            $data->aaData[$index][1] = $value[3];
            $data->aaData[$index][2] = $value[4];
            $data->aaData[$index][3] = $value[5];
            $data->aaData[$index][4] = $value[6];
            $data->aaData[$index][5] = $value[7];
            $data->aaData[$index][6] = $value[8];
            $data->aaData[$index][7] = $value[9];
            $data->aaData[$index][8] = $value[10];
            $data->aaData[$index][9] = $costPrice;
            $data->aaData[$index][10] = $grossProfit;

            $index++;
        }

        $data->{'title_excel'} = [handlingTitleExcel()['title']];
        echo json_encode($data);
    }

    public function getSellingDiary()
    {
        if (!$this->perViewSellingDiary) {
            accessDenied($js = true);
        }

        $customers = $this->input->post('customers');
        $orders = $this->input->post('orders');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $products = "(
            SELECT tbl_products.id, tbl_products.code, tbl_products.name, tblunits.unit, tbl_category_products.name as category_name
            FROM tbl_products
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
            LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id
        ) as products";

        $items = "(
            SELECT tblitems.id, tblitems.code, tblitems.name, tblunits.unit, tblcategories.category as category_name
            FROM tblitems
            LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit
            LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id
        ) as items";

        // tbl_orders.reference_no as reference_order,
        // tbl_orders.date as date,
        // IF(tbl_order_items.type_item = 'products', products.code, items.code) as item_code,
        // IF(tbl_order_items.type_item = 'products', products.name, items.name) as item_name,
        // IF(tbl_order_items.type_item = 'products', products.unit, items.unit) as unit_name,
        // tbl_order_items.quantity as quantity,
        // tbl_order_items.price as price,
        // tbl_order_items.amount as subtotal,
        // '' as cost_price,
        // '' as gross_profit

        $this->datatables->select("
            tbl_orders.reference_no as reference_order,
            DATE_FORMAT(tbl_orders.date, '%d/%m/%Y') as date,
            tbl_invoices.reference_no as bill,
            IF(tbl_order_items.type_item = 'products', products.code, items.code) as item_code,
            IF(tbl_order_items.type_item = 'products', products.name, items.name) as item_name,
            IF(tbl_order_items.type_item = 'products', products.unit, items.unit) as unit_name,
            tbl_order_items.quantity as quantity,
            tbl_order_items.price as price,
            tbl_order_items.amount as subtotal,
            (tbl_order_items.amount + COALESCE(tbl_order_items.amount * tbl_invoices.tax_rate/100, 0)) as subtotalTax
        ", false)
            ->from('tbl_orders')
            ->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id')
            ->join('tbl_invoices', 'tbl_invoices.object_id = tbl_orders.id AND tbl_invoices.type = "orders"', 'left')
            ->join(
                $products,
                'products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"',
                'left'
            )
            ->join($items, 'items.id = tbl_order_items.item_id AND tbl_order_items.type_item = "items"', 'left');

        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        if (!empty($customers)) {
            $customer = explode('__', $customers);
            $this->datatables->where('tbl_orders.customer_id', $customer[1]);
        }

        if (!empty($orders)) {
            $this->datatables->where('tbl_orders.id', $orders);
        }

        $this->db->order_by('tbl_orders.date ASC');

        // if (!empty($customers)) {
        //     $customers = str_replace('customers__', '', $customers);
        //     $this->datatables->where('tbl_orders.customer_id', $customers);
        // }

        $data = json_decode($this->datatables->generate());
        echo json_encode($data);
    }

    public function debt_customer()
    {
        $data['title'] = _l('debt_customer');
        $data['staff'] = get_table_where('tblstaff');
        if ($this->input->get('is_type')) {
            $data['is_type'] = $this->input->get('is_type');
        }
        $this->load->view('admin/reports/customer/manage', $data);
    }

    public function debt_all_result()
    {
        $this->app->get_table_data('debt_all_result');
    }

    public function debt_all_result_by_staff()
    {
        $this->app->get_table_data('debt_all_result_by_staff');
    }

    public function debt_all_result_detail()
    {
        $this->app->get_table_data('debt_all_result_detail');
    }

    public function table_detail_payment()
    {

        $months_report = $this->ci->input->post('report_months');
        $CI = &get_instance();
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->ci->input->post('report_from'));
                $to_date = to_sql_date($this->ci->input->post('report_to'));
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }

        $tb_tamp = '(
            SELECT 
                (tb_tamp.delivery_id) as delivery_id,
                (tb_tamp.delivery_item_id) as delivery_item_id,
                (tb_tamp.order_code) as order_code,
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
                GROUP BY counter_items_number,delivery_id,delivery_item_id
            ) tb_tamp
            GROUP BY tb_tamp.delivery_id,tb_tamp.order_code,tb_tamp.command,tb_tamp.delivery_item_id  
        ) as tb_tamp';

        $customer_select = $this->input->post('customer_select');

        $aColumns = [
            'tbl_deliveries.date as date',
            'tblclients.company as customer_name',
            '"" as code',
            'tbl_orders.reference_no as reference_no_order',
            '"" as item_code',
            '"" as item_name',
            '"" as unit',
            '0 as quantity',
            '0 as price',
            '"" as tax_name',
            '0 as tax_amount',
            '0 as amount',
            'tbl_deliveries.reference_no as reference_no'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_deliveries';
        $where = [
            'AND tbl_orders.type_orders NOT IN (2,4,11) '
        ];
        $filter = [];
        $join = [
            "INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id",
            'LEFT JOIN tbl_type_orders ON tbl_type_orders.id = tbl_orders.type_orders',
            "INNER JOIN tblclients ON tblclients.userid = tbl_deliveries.customer_id",
        ];

        if (!empty($customer_select)) {
            $customer_select = explode('__', $customer_select);
            array_push($where, 'AND tbl_deliveries.customer_id = ' . $customer_select[1] . '');
        }
        if (!empty($beginMonth)) {
            $beginMonth = $beginMonth . ' 00:00:00';
            array_push($where, 'AND tbl_deliveries.date >= "' . $beginMonth . '"');
        }
        if (!empty($endMonth)) {
            $endMonth = $endMonth . ' 23:59:59';
            array_push($where, 'AND tbl_deliveries.date <= "' . $endMonth . '"');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_deliveries.tax_rate',
            'tbl_deliveries.id',
            'tbl_type_orders.color as color_type_orders'
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');

        $fotter = [
            'total_quantity' => 0,
            'total_amount_tax' => 0,
            'total_amount' => 0,
        ];
        foreach ($rResult as $key => $aRow) {
            $color_type_orders = $aRow['color_type_orders'];
            $row = array();
            $start++;
            $id_delivery = $aRow['id'];
            $tb_tamp = '(
                SELECT 
                    (tb_tamp.delivery_id) as delivery_id,
                    (tb_tamp.delivery_item_id) as delivery_item_id,
                    (tb_tamp.order_code) as order_code,
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
                    WHERE tbl_delivery_items_columns.delivery_id = ' . $id_delivery . '
                    GROUP BY counter_items_number,delivery_id,delivery_item_id
                ) tb_tamp
                GROUP BY tb_tamp.delivery_id,tb_tamp.order_code,tb_tamp.command,tb_tamp.delivery_item_id  
            ) as tb_tamp';

            $this->db->select('
                tbl_delivery_items.item_id as item_id,
                tbl_delivery_items.item_code as item_code,
                tbl_delivery_items.price as price,
                tb_tamp.order_code as code,
                tb_tamp.command as command,
                tbl_order_items.product_name_customer as item_name,
                tbl_delivery_items.price as price,
                SUM(tb_tamp.quantity_put) as quantity,
                tbl_products.code as item_code,
                tbl_products.mode as mode,
                tbl_order_items.is_lot as is_lot,
                tblunits.unit as unit
            ');
            $this->db->from('tbl_delivery_items');
            $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_order_items.unit_id', 'left');
            $this->db->join($tb_tamp, 'tb_tamp.delivery_item_id = tbl_delivery_items.id');
            $this->db->where('tbl_delivery_items.delivery_id', $id_delivery);
            $this->db->group_by('tbl_delivery_items.item_id, tb_tamp.command, tb_tamp.order_code');
            $delivery_items = $this->db->get()->result_array();
            if (!empty($delivery_items)) {
                foreach ($delivery_items as $kk => $item) {
                    $row[0] = '<div>' . _dhau($aRow['date']) . '</div>';
                    $row[1] = '<div>' . ($aRow['customer_name']) . '</div>';
                    $row[2] = '<div>' . ($item['code']) . '</div>';

                    $row[3] = '<div class="text-center">' . $aRow['reference_no_order'] . '</div>';
                    $row[4] = '<div style="width: 150px">' . ($item['item_code']) . '</div>';
                    $row[5] = '<div>' . ($item['item_name']) . '</div>';
                    $row[6] = '<div class="text-center">' . ($item['unit']) . '</div>';
                    $row[7] = '<div class="text-center" >' . formatNumber($item['quantity']) . '</div>';
                    $row[8] = '<div class="text-right">' . (!empty($item['price']) ? formatMoney($item['price']) : '') . '</div>';
                    $row[9] = '<div class="text-center">' . (!empty($aRow['tax_name']) ? $aRow['tax_name'] : '') . '</div>';
                    $tax_rate = $aRow['tax_rate'];
                    $tax_amount = ($item['quantity'] * $item['price'] * $tax_rate) / 100;
                    if ($item['is_lot']) {
                        $tax_amount = ($item['price'] * $tax_rate) / 100;
                    }

                    $row[10] = '<div class="text-right">' . (!empty($tax_amount) ? formatMoney($tax_amount) : '') . '</div>';
                    $amount = ($item['quantity'] * $item['price']) + $tax_amount;
                    if ($item['is_lot']) {
                        $amount = ($item['price']) + $tax_amount;
                    }

                    $row[11] = '<div class="text-right" style="width: 100px">' . (!empty($amount) ? formatMoney($amount) : '') . '</div>';
                    $row[12] = '<div class="text-left" style="width: 100px">' . ($aRow['reference_no']) . '</div>';
                    $fotter['total_quantity'] += $item['quantity'];
                    $fotter['total_amount_tax'] += $tax_amount;
                    $fotter['total_amount'] += $amount;
                    $output['aaData'][] = $row;
                }
            }
        }
        $fotter['total_quantity'] = formatNumber($fotter['total_quantity']);
        $fotter['total_amount_tax'] = formatMoney($fotter['total_amount_tax']);
        $fotter['total_amount'] = formatMoney($fotter['total_amount']);
        $output['fotter'] = $fotter;
        echo json_encode($output);
    }

    public function table_detail_payment_old_vs1()
    {

        $months_report = $this->ci->input->post('report_months');
        $CI = &get_instance();
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->ci->input->post('report_from'));
                $to_date = to_sql_date($this->ci->input->post('report_to'));
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }

        $tb_tamp = '(
            SELECT 
                (tb_tamp.delivery_id) as delivery_id,
                (tb_tamp.delivery_item_id) as delivery_item_id,
                (tb_tamp.order_code) as order_code,
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
                GROUP BY counter_items_number,delivery_id,delivery_item_id
            ) tb_tamp
            GROUP BY tb_tamp.delivery_id,tb_tamp.order_code,tb_tamp.command,tb_tamp.delivery_item_id  
        ) as tb_tamp';

        $customer_select = $this->input->post('customer_select');

        $aColumns = [
            'tbl_deliveries.date as date',
            'tbl_deliveries.customer_name as customer_name',
            'tb_tamp.order_code as code',
            '"" as item_code',
            'tbl_order_items.product_name_customer as item_name',
            '"" as unit',
            'SUM(tb_tamp.quantity_put) as quantity',
            'tbl_delivery_items.price as price',
            'tbl_deliveries.tax_name as tax_name',
            '0 as tax_amount',
            '0 as amount',
            'tbl_deliveries.reference_no as reference_no'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_deliveries';
        $where = [
            'AND tbl_orders.type_orders NOT IN (2,4,11)'
        ];
        $filter = [];
        $join = [
            "INNER JOIN tbl_delivery_items ON tbl_delivery_items.delivery_id = tbl_deliveries.id",
            "INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id",
            "INNER JOIN tbl_order_items ON tbl_order_items.id = tbl_delivery_items.order_item_id",
            "INNER JOIN $tb_tamp ON tb_tamp.delivery_item_id = tbl_delivery_items.id"
        ];

        if (!empty($customer_select)) {
            $customer_select = explode('__', $customer_select);
            array_push($where, 'AND tbl_deliveries.customer_id = ' . $customer_select[1] . '');
        }
        if (!empty($beginMonth)) {
            $beginMonth = $beginMonth . ' 00:00:00';
            array_push($where, 'AND tbl_deliveries.date >= "' . $beginMonth . '"');
        }
        if (!empty($endMonth)) {
            $endMonth = $endMonth . ' 23:59:59';
            array_push($where, 'AND tbl_deliveries.date <= "' . $endMonth . '"');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_deliveries.tax_rate',
            'tbl_delivery_items.type_item',
            'tbl_delivery_items.item_id',
        ], 'GROUP BY type_item,item_id,tb_tamp.order_code,tb_tamp.command,tbl_deliveries.id', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');

        $fotter = [
            'total_quantity' => 0,
            'total_amount_tax' => 0,
            'total_amount' => 0,
        ];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $start++;

            $type_item = $aRow['type_item'];
            $items_id = $aRow['item_id'];
            if ($type_item == "products") {
                $info = $this->products_model->rowProduct($items_id);
                $unit = $this->unit_model->rowUnit($info['unit_id']);
            } elseif ($type_item == "items") {
                $info = $this->items_model->rowItems($items_id);
                $unit = $this->unit_model->rowUnit($info['unit']);
            } elseif ($type_item == "materials") {
                $info = $this->items_model->rowMaterial($items_id);
                $unit = $this->unit_model->rowUnit($info['unit_id']);
            }

            $row[0] = '<div>' . _dhau($aRow['date']) . '</div>';
            $row[1] = '<div>' . ($aRow['customer_name']) . '</div>';
            $row[2] = '<div>' . ($aRow['code']) . '</div>';
            $row[3] = '<div style="width: 150px">' . ($info['code']) . '</div>';
            $row[4] = '<div>' . ($aRow['item_name']) . '</div>';
            $row[5] = '<div class="text-center">' . ($unit['unit']) . '</div>';
            $row[6] = '<div class="text-center" >' . formatNumber($aRow['quantity']) . '</div>';
            $row[7] = '<div class="text-right">' . (!empty($aRow['price']) ? formatMoney($aRow['price']) : '') . '</div>';
            $row[8] = '<div class="text-center">' . (!empty($aRow['tax_name']) ? $aRow['tax_name'] : '') . '</div>';
            $tax_rate = $aRow['tax_rate'];
            $tax_amount = ($aRow['quantity'] * $aRow['price'] * $tax_rate) / 100;
            $row[9] = '<div class="text-right">' . (!empty($tax_amount) ? formatMoney($tax_amount) : '') . '</div>';
            $amount = ($aRow['quantity'] * $aRow['price']) + $tax_amount;
            $row[10] = '<div class="text-right" style="width: 100px">' . (!empty($amount) ? formatMoney($amount) : '') . '</div>';
            $row[11] = '<div class="text-left" style="width: 100px">' . ($aRow['reference_no']) . '</div>';
            $fotter['total_quantity'] += $aRow['quantity'];
            $fotter['total_amount_tax'] += $tax_amount;
            $fotter['total_amount'] += $amount;
            $output['aaData'][] = $row;
        }
        $fotter['total_quantity'] = formatNumber($fotter['total_quantity']);
        $fotter['total_amount_tax'] = formatMoney($fotter['total_amount_tax']);
        $fotter['total_amount'] = formatMoney($fotter['total_amount']);
        $output['fotter'] = $fotter;
        echo json_encode($output);
    }

    public function table_detail_payment_old()
    {

        $months_report = $this->ci->input->post('report_months');
        $CI = &get_instance();
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->ci->input->post('report_from'));
                $to_date = to_sql_date($this->ci->input->post('report_to'));
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }
        $customer_select = $this->input->post('customer_select');
        $this->db->select('
            tbl_deliveries.id as delivery_id,
            tbl_deliveries.date as date,
            tbl_deliveries.reference_no as reference_no,
            tbl_deliveries.customer_name as customer_name,
            tbl_deliveries.tax_rate as tax_rate,
            tbl_deliveries.tax_name as tax_name,
            tbl_delivery_items.id as id,
            tbl_delivery_items.order_item_id as order_item_id,
            tbl_delivery_items.type_item as type_item,
            tbl_delivery_items.item_id as item_id,
            tbl_delivery_items.quantity as quantity,
            tbl_delivery_items.price as price,
            tbl_delivery_items.amount as amount
            ');
        $this->db->from('tbl_deliveries');
        $this->db->join('tbl_delivery_items', 'tbl_delivery_items.delivery_id = tbl_deliveries.id');
        if (!empty($customer_select)) {
            $customer_select = explode('__', $customer_select);
            $this->db->where('tbl_deliveries.customer_id', $customer_select[1]);
        }
        if (!empty($beginMonth)) {
            $beginMonth = $beginMonth . ' 00:00:00';
            $this->db->where('tbl_deliveries.date >=', $beginMonth);
        }
        if (!empty($endMonth)) {
            $endMonth = $endMonth . ' 23:59:59';
            $this->db->where('tbl_deliveries.date <=', $endMonth);
        }
        $items = $this->db->get()->result_array();
        $orderItemsColumnsNewVs1 = [];
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $delivery_id = $value['delivery_id'];
                $order_item_id = $value['order_item_id'];
                $orderItems = get_table_where('tbl_order_items', ['id' => $order_item_id], '', 'row_array');
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                } elseif ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                }
                $dtOrderItem = $this->orders_model->rowOrderItemsById($value['order_item_id']);
                if ($type_item == "products") {
                    $thSub = '';
                    $trHtmlChild = '';
                    $ct_counter_item = $dtOrderItem['ct_counter_item'];
                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                    $this->db->select('tbl_delivery_items_columns.*');
                    $this->db->from('tbl_delivery_items_columns');
                    $this->db->where('tbl_delivery_items_columns.delivery_item_id', $value['id']);
                    $orderItemsColumns = $this->db->get()->result_array();
                    $orderItemsColumnsNew = [];
                    if ($ct_counter_item > 0) {
                        for ($i = 0; $i < $ct_counter_item; $i++) {
                            $arrNew = [];
                            foreach ($productsColumns as $k => $v) {
                                $columns_name = [];
                                foreach ($orderItemsColumns as $kO => $vO) {
                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                        $columns_name = [
                                            vn_to_str($vO['columns_value']) => $vO['columns_name']
                                        ];
                                        break;
                                    }
                                }
                                $arrNew = array_merge($arrNew, $columns_name);
                            }
                            $orderItemsColumnsNew[$i] = $arrNew;
                            foreach ($orderItemsColumns as $kO => $vO) {
                                if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                    $order_code = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['code'] = $order_code;
                                    continue;
                                } elseif ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                    $command = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['command'] = $command;
                                    continue;
                                } elseif ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                    $quantity_put = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;
                                    continue;
                                } elseif ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                    $quantity_loss = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['quantity_loss'] = $quantity_loss;
                                    continue;
                                } elseif ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                                    $sample_quantity_item = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['sample_quantity_item'] = $sample_quantity_item;
                                    continue;
                                }
                            }
                        }
                    }

                    if (!empty($orderItemsColumnsNew)) {
                        foreach ($orderItemsColumnsNew as $kkk => $vvv) {
                            if (empty($vvv)) {
                                continue;
                            }
                            $columns_name_new = 'default';
                            if (!empty($productsColumns)) {
                                foreach ($productsColumns as $k => $v) {
                                    $name_check = vn_to_str($v['name']);
                                    if (!empty($vvv[$name_check])) {
                                        $columns_name_new .= $vvv[$name_check] . '__';
                                    }
                                }
                            }
                            $columns_name_new = trim($columns_name_new, '__');
                            $check_key = $vvv['code'] . '__' . $vvv['command'] . '__' . $items_id . '__' . $type_item . '__' . $delivery_id;
                            if (!empty($orderItemsColumnsNewVs1[$check_key])) {
                                $orderItemsColumnsNewVs1[$check_key]['quantity_put'] += $vvv['quantity_put'];
                                $orderItemsColumnsNewVs1[$check_key]['quantity_loss'] += $vvv['quantity_loss'];
                                $orderItemsColumnsNewVs1[$check_key]['sample_quantity_item'] += $vvv['sample_quantity_item'];
                            } else {
                                $orderItemsColumnsNewVs1[$check_key] = $vvv;
                                $orderItemsColumnsNewVs1[$check_key]['item_id'] = $items_id;
                                $orderItemsColumnsNewVs1[$check_key]['type_item'] = $type_item;
                                $orderItemsColumnsNewVs1[$check_key]['reference_no'] = $value['reference_no'];
                                $orderItemsColumnsNewVs1[$check_key]['customer_name'] = $value['customer_name'];
                                $orderItemsColumnsNewVs1[$check_key]['date'] = $value['date'];
                                $orderItemsColumnsNewVs1[$check_key]['price'] = $value['price'];
                                $orderItemsColumnsNewVs1[$check_key]['item_name'] = $orderItems['product_name_customer'];
                                $orderItemsColumnsNewVs1[$check_key]['item_code'] = $info['code'];
                                $orderItemsColumnsNewVs1[$check_key]['unit'] = $unit['unit'];
                                $orderItemsColumnsNewVs1[$check_key]['id'] = $value['id'];
                                $orderItemsColumnsNewVs1[$check_key]['tax_name'] = $value['tax_name'];
                                $orderItemsColumnsNewVs1[$check_key]['tax_rate'] = $value['tax_rate'];
                            }
                        }
                    }
                }
            }
        }
        $tableAllItemsNew = '';
        $tableAllItemsNew .= "(";
        $tableAllItemsNew .= "( 
            SELECT 0 as id,
            '' as customer_name, 
            '' as code, 
            '' as command, 
            0 as item_id, 
            '' as type_item, 
            '' as quantity, 
            '' as reference_no, 
            '' as date, 
            '' as item_name, 
            '' as item_code, 
            '' as unit, 
            0 as tax_rate, 
            '' as tax_name, 
            0 as price) UNION ALL";

        if (!empty($orderItemsColumnsNewVs1)) {
            foreach ($orderItemsColumnsNewVs1 as $key => $value) {
                $id = $value['id'];
                $customer_name = $value['customer_name'];
                $code = $value['code'];
                $code = str_replace('"', '', $code);
                $code = str_replace("'", '', $code);
                $command = $value['command'];
                $item_id = $value['item_id'];
                $type_item = $value['type_item'];
                $quantity = $value['quantity_put'];
                $reference_no = $value['reference_no'];
                $date = $value['date'];
                $item_name = $value['item_name'];
                $item_name = str_replace('"', '', $item_name);
                $item_name = str_replace("'", '', $item_name);
                $item_code = $value['item_code'];
                $item_code = str_replace('"', '', $item_code);
                $item_code = str_replace("'", '', $item_code);
                $unit = $value['unit'];
                $tax_rate = $value['tax_rate'];
                $tax_name = $value['tax_name'];
                $price = $value['price'];
                $tableAllItemsNew .= "( SELECT 
                                    '$id' as id,
                                    '$customer_name' as customer_name,
                                    '$code' as code,
                                    '$command' as command,
                                    '$item_id' as item_id,
                                    '$type_item' as type_item,
                                    '$quantity' as quantity,
                                    '$reference_no' as reference_no,
                                    '$date' as date,
                                    '$item_name' as item_name,
                                    '$item_code' as item_code,
                                    '$unit' as unit,
                                    '$tax_rate' as tax_rate,
                                    '$tax_name' as tax_name,
                                    '$price' as price
                                    ) UNION ALL";
            }
        }

        $tableAllItemsNew = trim($tableAllItemsNew, 'UNION ALL');
        $tableAllItemsNew .= ') table_all_item_new';

        $tableAllItems = "(
            SELECT 
                table_all_item_new.id as id,
                table_all_item_new.customer_name as customer_name,
                table_all_item_new.code as code,
                (command) as command,
                (item_id) as item_id,
                (type_item) as type_item,
                quantity as quantity,
                reference_no as reference_no,
                date as date,
                item_name as item_name,
                item_code as item_code,
                unit as unit,
                tax_rate as tax_rate,
                tax_name as tax_name,
                price as price
            FROM $tableAllItemsNew
        ) table_all_item";
        //        $query = $this->db->query($tableAllItems)->result_array();
        //        print_arrays($query);

        $aColumns = [
            'table_all_item.date as date',
            'table_all_item.customer_name as customer_name',
            'table_all_item.code as code',
            'table_all_item.item_code as item_code',
            'table_all_item.item_name as item_name',
            'table_all_item.unit as unit',
            'table_all_item.quantity as quantity',
            'table_all_item.price as price',
            'table_all_item.tax_name as tax_name',
            '0 as tax_amount',
            '0 as amount',
            'table_all_item.reference_no as reference_no'
        ];
        $sIndexColumn = 'table_all_item.id';
        $sTable = $tableAllItems;
        $where = [
            'AND table_all_item.id != 0'
        ];
        $filter = [];
        $join = [];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'table_all_item.id',
            'table_all_item.tax_rate',
        ], '', [], ['union_all' => true]);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');

        $fotter = [
            'total_quantity' => 0,
            'total_amount_tax' => 0,
            'total_amount' => 0,
        ];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $start++;

            $row[0] = '<div>' . _dhau($aRow['date']) . '</div>';
            $row[1] = '<div>' . ($aRow['customer_name']) . '</div>';
            $row[2] = '<div>' . ($aRow['code']) . '</div>';
            $row[3] = '<div style="width: 150px">' . ($aRow['item_code']) . '</div>';
            $row[4] = '<div>' . ($aRow['item_name']) . '</div>';
            $row[5] = '<div class="text-center">' . ($aRow['unit']) . '</div>';
            $row[6] = '<div class="text-center" >' . formatNumber($aRow['quantity']) . '</div>';
            $row[7] = '<div class="text-right">' . (!empty($aRow['price']) ? formatMoney($aRow['price']) : '') . '</div>';
            $row[8] = '<div class="text-center">' . (!empty($aRow['tax_name']) ? $aRow['tax_name'] : '') . '</div>';
            $tax_rate = $aRow['tax_rate'];
            $tax_amount = ($aRow['quantity'] * $aRow['price'] * $tax_rate) / 100;
            $row[9] = '<div class="text-right">' . (!empty($tax_amount) ? formatMoney($tax_amount) : '') . '</div>';
            $amount = ($aRow['quantity'] * $aRow['price']) + $tax_amount;
            $row[10] = '<div class="text-right" style="width: 100px">' . (!empty($amount) ? formatMoney($amount) : '') . '</div>';
            $row[11] = '<div class="text-left" style="width: 100px">' . ($aRow['reference_no']) . '</div>';
            $fotter['total_quantity'] += $aRow['quantity'];
            $fotter['total_amount_tax'] += $tax_amount;
            $fotter['total_amount'] += $amount;
            $output['aaData'][] = $row;
        }
        $fotter['total_quantity'] = formatNumber($fotter['total_quantity']);
        $fotter['total_amount_tax'] = formatMoney($fotter['total_amount_tax']);
        $fotter['total_amount'] = formatMoney($fotter['total_amount']);
        $output['fotter'] = $fotter;
        echo json_encode($output);
    }

    public function compare_debt()
    {
        $data = $this->input->post();
        $months_report = $data['report_months'];
        $CI = &get_instance();
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }

        $html = '';
        if (isset($data['id_customer'])) {
            $id_customer = explode("__", $data['id_customer']);
            $this->db->select('tbl_orders.*');
            $this->db->where('tbl_orders.customer_id', $id_customer[1]);
            $this->db->where('tbl_orders.status', 'approved');
            if (!empty($beginMonth) && !empty($endMonth)) {
                $this->db->where('tbl_orders.date >= ', $beginMonth);
                $this->db->where('tbl_orders.date <= ', $endMonth);
            }
            $allOrder = $this->db->get('tbl_orders')->result_array();
            if ($allOrder) {
                $total1 = 0;
                $total2 = 0;
                foreach ($allOrder as $key => $value) {
                    $get_vouchers = get_table_where(
                        'tblvouchers_coupon',
                        array('customer' => $id_customer[1], 'status' => 1)
                    );
                    $key_main = 0;
                    $get_pt = array();
                    foreach ($get_vouchers as $key_vouchers => $value_vouchers) {
                        $str_to_arr = explode(",", $value_vouchers['arr_code_orders']);
                        foreach ($str_to_arr as $key_arr => $value_arr) {
                            $id_order = explode("|", $value_arr);
                            if ($id_order[0] == $value['id']) {
                                $get_pt[$key_main]['date_vouchers'] = $value_vouchers['date_vouchers'];
                                $get_pt[$key_main]['code_vouchers'] = $value_vouchers['code_vouchers'];
                                $get_pt[$key_main]['total'] = $id_order[1];
                                $key_main++;
                            }
                        }
                    }
                    $get_ptk = get_table_where(
                        'tblother_payslips_coupon',
                        array('type_vouchers' => 5, 'vouchers_id' => $value['id'], 'status' => 1)
                    );
                    $numberRow = count($get_pt) + count($get_ptk);
                    if ($numberRow == 0) {
                        $numberRow = 1;
                    }

                    $get_invoices = get_table_where(
                        'tbl_invoices',
                        array('type' => 'orders', 'object_id' => $value['id']),
                        '',
                        'row'
                    );
                    $html .= '<tr>';
                    $html .= '<td class="text-center" rowspan="' . $numberRow . '">' . _d($value['date']) . '</td>';
                    $html .= '<td class="text-center" rowspan="' . $numberRow . '">' . $value['reference_no'] . '</td>';
                    $html .= '<td class="text-center" rowspan="' . $numberRow . '">' . ($get_invoices && !empty($get_invoices->reference_no) ? $get_invoices->reference_no : '') . '</td>';
                    $html .= '<td class="text-right" rowspan="' . $numberRow . '">' . number_format($value['grand_total']) . '</td>';
                    $col1 = $value['grand_total'];
                    $total1 += $value['grand_total'];

                    if ($numberRow == 1) {
                        if ($get_pt) {
                            $col2 = 0;
                            foreach ($get_pt as $key_pt => $value_pt) {
                                $html .= '<td class="text-center">' . _d($value_pt['date_vouchers']) . '</td>';
                                $html .= '<td class="text-center">' . $value_pt['code_vouchers'] . '</td>';
                                $html .= '<td class="text-right">' . number_format($value_pt['total']) . '</td>';
                                $html .= '<td></td>';
                                $html .= '</tr>';
                                $col2 += $value_pt['total'];
                                $total2 += $value_pt['total'];
                            }
                        } elseif ($get_ptk) {
                            $col2 = 0;
                            foreach ($get_ptk as $key_ptk => $value_ptk) {
                                $html .= '<td class="text-center">' . _d($value_ptk['date']) . '</td>';
                                $html .= '<td class="text-center">' . $value_ptk['prefix'] . '-' . $value_ptk['code'] . '</td>';
                                $html .= '<td class="text-right">' . number_format($value_ptk['total']) . '</td>';
                                $html .= '<td></td>';
                                $html .= '</tr>';
                                $col2 += $value_ptk['total'];
                                $total2 += $value_ptk['total'];
                            }
                        } else {
                            $col2 = 0;
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '</tr>';
                        }
                    } else {
                        $col2 = 0;
                        if ($get_pt) {
                            foreach ($get_pt as $key_pt => $value_pt) {
                                if ($key_pt == 0) {
                                    $html .= '<td class="text-center">' . _d($value_pt['date_vouchers']) . '</td>';
                                    $html .= '<td class="text-center">' . $value_pt['code_vouchers'] . '</td>';
                                    $html .= '<td class="text-right">' . number_format($value_pt['total']) . '</td>';
                                    $html .= '<td></td>';
                                    $html .= '</tr>';
                                    $col2 += $value_pt['total'];
                                    $total2 += $value_pt['total'];
                                } else {
                                    $html .= '<tr>';
                                    $html .= '<td class="text-center">' . _d($value_pt['date_vouchers']) . '</td>';
                                    $html .= '<td class="text-center">' . $value_pt['code_vouchers'] . '</td>';
                                    $html .= '<td class="text-right">' . number_format($value_pt['total']) . '</td>';
                                    $html .= '<td></td>';
                                    $html .= '</tr>';
                                    $col2 += $value_pt['total'];
                                    $total2 += $value_pt['total'];
                                }
                            }
                        }
                        if ($get_ptk) {
                            foreach ($get_ptk as $key_ptk => $value_ptk) {
                                if ($key_ptk == 0 && empty($get_pt)) {
                                    $html .= '<td class="text-center">' . _d($value_ptk['date']) . '</td>';
                                    $html .= '<td class="text-center">' . $value_ptk['prefix'] . '-' . $value_ptk['code'] . '</td>';
                                    $html .= '<td class="text-right">' . number_format($value_ptk['total']) . '</td>';
                                    $html .= '<td></td>';
                                    $html .= '</tr>';
                                    $col2 += $value_ptk['total'];
                                    $total2 += $value_ptk['total'];
                                } else {
                                    $html .= '<tr>';
                                    $html .= '<td class="text-center">' . _d($value_ptk['date']) . '</td>';
                                    $html .= '<td class="text-center">' . $value_ptk['prefix'] . '-' . $value_ptk['code'] . '</td>';
                                    $html .= '<td class="text-right">' . number_format($value_ptk['total']) . '</td>';
                                    $html .= '<td></td>';
                                    $html .= '</tr>';
                                    $col2 += $value_ptk['total'];
                                    $total2 += $value_ptk['total'];
                                }
                            }
                        }
                    }
                    $cl = $col1 - $col2;
                    $html .= '<tr class="row-header">';
                    $html .= '<td colspan="3" class="text-left bold">Cộng</td>';
                    $html .= '<td class="text-right bold">' . number_format($col1) . '</td>';
                    $html .= '<td colspan="2" class="text-left bold">Cộng</td>';
                    $html .= '<td class="text-right bold">' . number_format($col2) . '</td>';
                    $html .= '<td class="text-right bold">' . number_format($cl) . '</td>';
                    $html .= '</tr>';
                }
                $total_all = $total1 - $total2;
                $html .= '<tr class="row-header">';
                $html .= '<td colspan="3" class="text-left bold">Tổng cộng</td>';
                $html .= '<td class="text-right bold">' . number_format($total1) . '</td>';
                $html .= '<td colspan="2"></td>';
                $html .= '<td class="text-right bold">' . number_format($total2) . '</td>';
                $html .= '<td class="text-right bold">' . number_format($total_all) . '</td>';
                $html .= '</tr>';
            }
        }
        echo $html;
    }

    public function report_financial()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('report_financial');
        }
    }

    public function dashboard_report_old($value = '')
    {
        $data = $this->input->post();

        $data['customers_ch'] = trim($data['customers_ch'], 'customers__');
        if (!empty($data['report_from']) && !empty($data['report_from'])) {
            $beginMonth = to_sql_date($data['report_from']);
            $endMonth = to_sql_date($data['report_to']);
            if ((strtotime($endMonth) - strtotime($beginMonth)) < 0) {
                $_data['labels'] = '';
                $_data['data'] = '';
                $_data['datas_payment'] = '';
                $_data['datas_cost'] = '';
                echo json_encode($_data);
                die;
            }
        }
        $where_or = '';
        if (!empty($data['search_id_staff'])) {
            foreach ($data['search_id_staff'] as $key => $v) {
                $where_or = '(tbl_orders.employee_id = ' . $v . ') or ' . $where_or;
            }
        }
        if ($data['months_report'] == 'this_year') {
            $labels[1] = 'Tháng 1';
            $labels[2] = 'Tháng 2';
            $labels[3] = 'Tháng 3';
            $labels[4] = 'Tháng 4';
            $labels[5] = 'Tháng 5';
            $labels[6] = 'Tháng 6';
            $labels[7] = 'Tháng 7';
            $labels[8] = 'Tháng 8';
            $labels[9] = 'Tháng 9';
            $labels[10] = 'Tháng 10';
            $labels[11] = 'Tháng 11';
            $labels[12] = 'Tháng 12';

            foreach ($labels as $key => $value) {
                $whereJoin = array();
                $whereJoin['where'] = array(
                    'month(tbl_deliveries.date) = ' => $key,
                    'year(tbl_deliveries.date) =' => date('Y'),
                );
                $whereJoin['where'][] = array('warehouseman_id >' => 0);
                if (!empty($data['customers_ch'])) {
                    $whereJoin['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                if (!empty($data['search_id_staff'])) {
                    $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                $whereJoin['join'] = array();
                $whereJoin['field'] = 'grand_total';
                $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));


                $_cost = 0;
                $whereJoin_cost = array();
                $whereJoin_cost['where'] = array(
                    'month(tbl_orders.date) = ' => $key,
                    'year(tbl_orders.date) =' => date('Y'),
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                if (!empty($data['customers_ch'])) {
                    $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_cost['join'] = array();
                $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                $_cost = sum_from_table_join('tbl_orders', $whereJoin_cost);
                // var_dump($this->db->last_query());die;

                $whereJoin_costc = array();
                $whereJoin_costc['where'] = array(
                    'month(tbl_orders.date) = ' => $key,
                    'year(tbl_orders.date) =' => date('Y'),
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                if (!empty($data['customers_ch'])) {
                    $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_costc['join'] = array();
                $whereJoin_costc['field'] = 'total_cost';
                $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                if (empty($sum)) {
                    $sum = 0;
                }
                $datas_payment[$key] = $sum;
                if (empty($_costc)) {
                    $_costc = $_cost;
                }
                if (empty($_costc)) {
                    $_costc = 0;
                }
                $datas_cost[$key] = $_costc;
                $datas[$key] = $sum - $datas_cost[$key];
            }
        } elseif ($data['months_report'] == 'last_year') {
            $labels[1] = 'Tháng 1';
            $labels[2] = 'Tháng 2';
            $labels[3] = 'Tháng 3';
            $labels[4] = 'Tháng 4';
            $labels[5] = 'Tháng 5';
            $labels[6] = 'Tháng 6';
            $labels[7] = 'Tháng 7';
            $labels[8] = 'Tháng 8';
            $labels[9] = 'Tháng 9';
            $labels[10] = 'Tháng 10';
            $labels[11] = 'Tháng 11';
            $labels[12] = 'Tháng 12';
            $prevyear = date('Y', strtotime("last year"));
            foreach ($labels as $key => $value) {
                $whereJoin = array();
                $whereJoin['where'] = array(
                    'month(tbl_deliveries.date) = ' => $key,
                    'year(tbl_deliveries.date) =' => $prevyear,
                );
                $whereJoin['where'][] = array('warehouseman_id >' => 0);
                if (!empty($data['customers_ch'])) {
                    $whereJoin['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                if (!empty($data['search_id_staff'])) {
                    $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                if (!empty($data['customers_ch'])) {
                    $whereJoin['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                if (!empty($data['search_id_staff'])) {
                    $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                $whereJoin['join'] = array();
                $whereJoin['field'] = 'grand_total';
                $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));
                if (empty($sum)) {
                    $sum = 0;
                }
                $whereJoin_cost = array();

                $whereJoin_cost['where'] = array(
                    'month(tbl_orders.date) = ' => $key,
                    'year(tbl_orders.date) =' => $prevyear,
                );


                if (!empty($data['search_id_staff'])) {
                    $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                if (!empty($data['customers_ch'])) {
                    $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_cost['join'] = array();
                $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));

                $whereJoin_costc = array();
                $whereJoin_costc['where'] = array(
                    'month(tbl_orders.date) = ' => $key,
                    'year(tbl_orders.date) =' => $prevyear,
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                if (!empty($data['customers_ch'])) {
                    $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_costc['join'] = array();
                $whereJoin_costc['field'] = 'total_cost';
                $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                if (empty($sum)) {
                    $sum = 0;
                }
                $datas_payment[$key] = $sum;
                if (empty($_costc)) {
                    $_costc = $_cost;
                }
                if (empty($_costc)) {
                    $_costc = 0;
                }
                $datas_cost[$key] = $_costc;
                $datas[$key] = $sum - $datas_cost[$key];
            }
        } elseif ($data['months_report'] == 'this_month') {
            for ($i = 1; $i <= last_day(date('m')); $i++) {
                $labels[$i] = _d(date(date('y') . '-' . date('m') . '-' . $i));
                $whereJoin = array();
                $whereJoin['where'] = array(
                    'day(tbl_deliveries.date) = ' => $i,
                    'month(tbl_deliveries.date) = ' => date('m'),
                    'year(tbl_deliveries.date) =' => date('Y'),
                );
                $whereJoin['where'][] = array('warehouseman_id >' => 0);
                if (!empty($data['customers_ch'])) {
                    $whereJoin['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                if (!empty($data['search_id_staff'])) {
                    $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                };
                $whereJoin['join'] = array();
                $whereJoin['field'] = 'grand_total';
                $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));

                $whereJoin_cost = array();
                $whereJoin_cost['where'] = array(
                    'day(tbl_orders.date) = ' => $i,
                    'month(tbl_orders.date) = ' => date('m'),
                    'year(tbl_orders.date) =' => date('Y'),
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                };
                if (!empty($data['customers_ch'])) {
                    $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_cost['join'] = array();
                $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));
                $whereJoin_costc = array();
                $whereJoin_costc['where'] = array(
                    'day(tbl_orders.date) = ' => $i,
                    'month(tbl_orders.date) = ' => date('m'),
                    'year(tbl_orders.date) =' => date('Y'),
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                if (!empty($data['customers_ch'])) {
                    $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_costc['join'] = array();
                $whereJoin_costc['field'] = 'total_cost';
                $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                if (empty($sum)) {
                    $sum = 0;
                }
                $datas_payment[$i] = $sum;
                if (empty($_costc)) {
                    $_costc = $_cost;
                }
                if (empty($_costc)) {
                    $_costc = 0;
                }
                $datas_cost[$i] = $_costc;
                $datas[$i] = $sum - $datas_cost[$i];
            }
        } elseif ($data['months_report'] == '1') {
            $prevmonth = date('m', strtotime("last month"));
            $prevyear = date('Y', strtotime("last month"));
            $prevyeary = date('y', strtotime("last month"));
            for ($i = 1; $i <= last_day($prevmonth); $i++) {

                $labels[$i] = _d(date($prevyeary . '-' . $prevmonth . '-' . $i));
                $whereJoin = array();
                $whereJoin['where'] = array(
                    'day(tbl_deliveries.date) = ' => $i,
                    'month(tbl_deliveries.date) = ' => $prevmonth,
                    'year(tbl_deliveries.date) =' => $prevyear,
                );
                $whereJoin['where'][] = array('warehouseman_id >' => 0);
                if (!empty($data['customers_ch'])) {
                    $whereJoin['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                if (!empty($data['search_id_staff'])) {
                    $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                };
                $whereJoin['join'] = array();
                $whereJoin['field'] = 'grand_total';
                $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));

                $whereJoin_cost = array();
                $whereJoin_cost['where'] = array(
                    'day(tbl_orders.date) = ' => $i,
                    'month(tbl_orders.date) = ' => $prevmonth,
                    'year(tbl_orders.date) =' => $prevyear,
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                };
                if (!empty($data['customers_ch'])) {
                    $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_cost['join'] = array();
                $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));


                $whereJoin_costc = array();
                $whereJoin_costc['where'] = array(
                    'day(tbl_orders.date) = ' => $i,
                    'month(tbl_orders.date) = ' => $prevmonth,
                    'year(tbl_orders.date) =' => $prevyear,
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                if (!empty($data['customers_ch'])) {
                    $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_costc['join'] = array();
                $whereJoin_costc['field'] = 'total_cost';
                $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                if (empty($sum)) {
                    $sum = 0;
                }
                $datas_payment[$i] = $sum;
                if (empty($_costc)) {
                    $_costc = $_cost;
                }
                if (empty($_costc)) {
                    $_costc = 0;
                }
                $datas_cost[$i] = $_costc;
                $datas[$i] = $sum - $datas_cost[$i];
            }
        } elseif ($data['months_report'] == 'day') {
            for ($i = 1; $i <= 24; $i++) {
                $labels[$i] = $i . ' Giờ';
                $whereJoin = array();
                $whereJoin['where'] = array(
                    'HOUR(tbl_deliveries.date) = ' => $i,
                    'day(tbl_deliveries.date) = ' => date('d'),
                    'month(tbl_deliveries.date) = ' => date('m'),
                    'year(tbl_deliveries.date) =' => date('Y'),
                );
                $whereJoin['where'][] = array('warehouseman_id >' => 0);
                if (!empty($data['customers_ch'])) {
                    $whereJoin['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                if (!empty($data['search_id_staff'])) {
                    $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                };
                $whereJoin['join'] = array();
                $whereJoin['field'] = 'grand_total';
                $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));

                $whereJoin_cost = array();
                $whereJoin_cost['where'] = array(
                    'HOUR(tbl_orders.date) = ' => $i,
                    'day(tbl_orders.date) = ' => date('d'),
                    'month(tbl_orders.date) = ' => date('m'),
                    'year(tbl_orders.date) =' => date('Y'),
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                };
                if (!empty($data['customers_ch'])) {
                    $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_cost['join'] = array();
                $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));

                $whereJoin_costc = array();
                $whereJoin_costc['where'] = array(
                    'HOUR(tbl_orders.date) = ' => $i,
                    'day(tbl_orders.date) = ' => date('d'),
                    'month(tbl_orders.date) = ' => date('m'),
                    'year(tbl_orders.date) =' => date('Y'),
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                if (!empty($data['customers_ch'])) {
                    $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_costc['join'] = array();
                $whereJoin_costc['field'] = 'total_cost';
                $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                if (empty($sum)) {
                    $sum = 0;
                }
                $datas_payment[$i] = $sum;
                if (empty($_costc)) {
                    $_costc = $_cost;
                }
                if (empty($_costc)) {
                    $_costc = 0;
                }
                $datas_cost[$i] = $_costc;
                $datas[$i] = $sum - $datas_cost[$i];
            }
        } elseif ($data['months_report'] == 'week') {
            $day_first = date('Y-m-d', strtotime('this week', time()));

            for ($i = 0; $i <= 6; $i++) {
                $week = strtotime(date("Y-m-d", strtotime($day_first)) . '+' . $i . ' day');
                $week = strftime("%Y-%m-%d", $week);
                $labels[$i] = _d($week);
                $whereJoin = array();
                $whereJoin['where'] = array(
                    'day(tbl_deliveries.date) = ' => date('d', strtotime($week)),
                    'month(tbl_deliveries.date) = ' => date('m', strtotime($week)),
                    'year(tbl_deliveries.date) =' => date('Y', strtotime($week)),
                );
                $whereJoin['where'][] = array('warehouseman_id >' => 0);
                if (!empty($data['customers_ch'])) {
                    $whereJoin['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                if (!empty($data['search_id_staff'])) {
                    $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                };
                $whereJoin['join'] = array();
                $whereJoin['field'] = 'grand_total';
                $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));

                $whereJoin_cost = array();
                $whereJoin_cost['where'] = array(
                    'day(tbl_orders.date) = ' => date('d', strtotime($week)),
                    'month(tbl_orders.date) = ' => date('m', strtotime($week)),
                    'year(tbl_orders.date) =' => date('Y', strtotime($week)),
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                };
                if (!empty($data['customers_ch'])) {
                    $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_cost['join'] = array();
                $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));


                $whereJoin_costc = array();
                $whereJoin_costc['where'] = array(
                    'day(tbl_orders.date) = ' => date('d', strtotime($week)),
                    'month(tbl_orders.date) = ' => date('m', strtotime($week)),
                    'year(tbl_orders.date) =' => date('Y', strtotime($week)),
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                if (!empty($data['customers_ch'])) {
                    $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_costc['join'] = array();
                $whereJoin_costc['field'] = 'total_cost';
                $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                if (empty($sum)) {
                    $sum = 0;
                }
                $datas_payment[$i] = $sum;
                if (empty($_costc)) {
                    $_costc = $_cost;
                }
                if (empty($_costc)) {
                    $_costc = 0;
                }
                $datas_cost[$i] = $_costc;
                $datas[$i] = $sum - $datas_cost[$i];
            }
        } elseif ($data['months_report'] == 'custom') {
            $beginMonth = to_sql_date($data['report_from']);
            $endMonth = to_sql_date($data['report_to']);
            if (date('Y', strtotime($beginMonth)) == date('Y', strtotime($endMonth))) {
                if (date('m', strtotime($beginMonth)) == date('m', strtotime($endMonth))) {
                    if (date('d', strtotime($beginMonth)) == date('d', strtotime($endMonth))) {
                        for ($i = 1; $i <= 24; $i++) {
                            $labels[$i] = $i . ' Giờ';
                            $whereJoin = array();
                            $whereJoin['where'] = array(
                                'HOUR(tbl_deliveries.date) = ' => $i,
                                'day(tbl_deliveries.date) = ' => date('d', strtotime($beginMonth)),
                                'month(tbl_deliveries.date) = ' => date('m', strtotime($beginMonth)),
                                'year(tbl_deliveries.date) =' => date('Y', strtotime($beginMonth)),
                            );
                            $whereJoin['where'][] = array('warehouseman_id >' => 0);
                            if (!empty($data['search_id_staff'])) {
                                $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                            };
                            $whereJoin['join'] = array();
                            $whereJoin['field'] = 'grand_total';
                            $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));

                            $whereJoin_cost = array();
                            $whereJoin_cost['where'] = array(
                                'HOUR(tbl_orders.date) = ' => $i,
                                'day(tbl_orders.date) = ' => date('d', strtotime($beginMonth)),
                                'month(tbl_orders.date) = ' => date('m', strtotime($beginMonth)),
                                'year(tbl_orders.date) =' => date('Y', strtotime($beginMonth)),
                            );
                            if (!empty($data['search_id_staff'])) {
                                $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                            };
                            if (!empty($data['customers_ch'])) {
                                $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                            }
                            $whereJoin_cost['join'] = array();
                            $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                            $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));

                            $whereJoin_costc = array();
                            $whereJoin_costc['where'] = array(
                                'HOUR(tbl_orders.date) = ' => $i,
                                'day(tbl_orders.date) = ' => date('d', strtotime($beginMonth)),
                                'month(tbl_orders.date) = ' => date('m', strtotime($beginMonth)),
                                'year(tbl_orders.date) =' => date('Y', strtotime($beginMonth)),
                            );
                            if (!empty($data['search_id_staff'])) {
                                $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                            }
                            if (!empty($data['customers_ch'])) {
                                $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                            }
                            $whereJoin_costc['join'] = array();
                            $whereJoin_costc['field'] = 'total_cost';
                            $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                            if (empty($sum)) {
                                $sum = 0;
                            }
                            $datas_payment[$i] = $sum;
                            if (empty($_costc)) {
                                $_costc = $_cost;
                            }
                            if (empty($_costc)) {
                                $_costc = 0;
                            }
                            $datas_cost[$i] = $_costc;
                            $datas[$i] = $sum - $datas_cost[$i];
                        }
                    } else {
                        $j = 0;
                        for ($i = date('d', strtotime($beginMonth)); $i <= date('d', strtotime($endMonth)); $i++) {

                            $labels[$j] = _d(date(date('y', strtotime($endMonth)) . '-' . date(
                                'm',
                                strtotime($endMonth)
                            ) . '-' . $i));
                            $whereJoin = array();
                            $whereJoin['where'] = array(
                                'day(tbl_deliveries.date) = ' => $i,
                                'month(tbl_deliveries.date) = ' => date('m', strtotime($endMonth)),
                                'year(tbl_deliveries.date) =' => date('Y', strtotime($endMonth)),
                            );
                            $whereJoin['where'][] = array('warehouseman_id >' => 0);
                            if (!empty($data['search_id_staff'])) {
                                $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                            };
                            $whereJoin['join'] = array();
                            $whereJoin['field'] = 'grand_total';
                            $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));

                            $whereJoin_cost = array();
                            $whereJoin_cost['where'] = array(
                                'day(tbl_orders.date) = ' => $i,
                                'month(tbl_orders.date) = ' => date('m', strtotime($endMonth)),
                                'year(tbl_orders.date) =' => date('Y', strtotime($endMonth)),
                            );
                            if (!empty($data['search_id_staff'])) {
                                $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                            };
                            if (!empty($data['customers_ch'])) {
                                $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                            }
                            $whereJoin_cost['join'] = array();
                            $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                            $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));

                            $whereJoin_costc = array();
                            $whereJoin_costc['where'] = array(
                                'day(tbl_orders.date) = ' => $i,
                                'month(tbl_orders.date) = ' => date('m', strtotime($endMonth)),
                                'year(tbl_orders.date) =' => date('Y', strtotime($endMonth)),
                            );
                            if (!empty($data['search_id_staff'])) {
                                $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                            }
                            if (!empty($data['customers_ch'])) {
                                $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                            }
                            $whereJoin_costc['join'] = array();
                            $whereJoin_costc['field'] = 'total_cost';
                            $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                            if (empty($sum)) {
                                $sum = 0;
                            }
                            $datas_payment[$j] = $sum;
                            if (empty($_costc)) {
                                $_costc = $_cost;
                            }
                            if (empty($_costc)) {
                                $_costc = 0;
                            }
                            $datas_cost[$j] = $_costc;
                            $datas[$j] = $sum - $datas_cost[$j];
                            $j++;
                        }
                    }
                } else {
                    $j = 0;
                    for ($i = date('m', strtotime($beginMonth)); $i <= date('m', strtotime($endMonth)); $i++) {
                        $labels[$j] = 'Tháng ' . $i;
                        $whereJoin = array();
                        $whereJoin['where'] = array(
                            'month(tbl_deliveries.date) = ' => $i,
                            'year(tbl_deliveries.date) =' => date('Y', strtotime($endMonth)),
                        );
                        $whereJoin['where'][] = array('warehouseman_id >' => 0);
                        if (!empty($data['search_id_staff'])) {
                            $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                        };
                        $whereJoin['join'] = array();
                        $whereJoin['field'] = 'grand_total';
                        $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));

                        $whereJoin_cost = array();
                        $whereJoin_cost['where'] = array(
                            'month(tbl_orders.date) = ' => $i,
                            'year(tbl_orders.date) =' => date('Y', strtotime($endMonth)),
                        );
                        if (!empty($data['search_id_staff'])) {
                            $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                        };
                        if (!empty($data['customers_ch'])) {
                            $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                        }
                        $whereJoin_cost['join'] = array();
                        $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                        $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));

                        $whereJoin_costc = array();
                        $whereJoin_costc['where'] = array(
                            'month(tbl_orders.date) = ' => $i,
                            'year(tbl_orders.date) =' => date('Y', strtotime($endMonth)),
                        );
                        if (!empty($data['search_id_staff'])) {
                            $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                        }
                        if (!empty($data['customers_ch'])) {
                            $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                        }
                        $whereJoin_costc['join'] = array();
                        $whereJoin_costc['field'] = 'total_cost';
                        $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                        if (empty($sum)) {
                            $sum = 0;
                        }
                        $datas_payment[$j] = $sum;
                        if (empty($_costc)) {
                            $_costc = $_cost;
                        }
                        if (empty($_costc)) {
                            $_costc = 0;
                        }
                        $datas_cost[$j] = $_costc;
                        $datas[$j] = $sum - $datas_cost[$j];
                        $j++;
                    }
                }
            } else {
                $j = 0;
                for ($i = date('Y', strtotime($beginMonth)); $i <= date('Y', strtotime($endMonth)); $i++) {
                    $labels[$j] = 'Năm ' . $i;
                    $whereJoin = array();
                    $whereJoin['where'] = array(
                        'year(tbl_deliveries.date) =' => $i,
                    );
                    $whereJoin['where'][] = array('warehouseman_id >' => 0);
                    if (!empty($data['search_id_staff'])) {
                        $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                    };
                    $whereJoin['join'] = array();
                    $whereJoin['field'] = 'grand_total';
                    $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));

                    $whereJoin_cost = array();
                    $whereJoin_cost['where'] = array(
                        'year(tbl_orders.date) =' => $i,
                    );
                    if (!empty($data['search_id_staff'])) {
                        $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                    };
                    if (!empty($data['customers_ch'])) {
                        $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                    }
                    $whereJoin_cost['join'] = array();
                    $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                    $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));

                    $whereJoin_costc = array();
                    $whereJoin_costc['where'] = array(
                        'year(tbl_orders.date) =' => $i,
                    );
                    if (!empty($data['search_id_staff'])) {
                        $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                    }
                    if (!empty($data['customers_ch'])) {
                        $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                    }
                    $whereJoin_costc['join'] = array();
                    $whereJoin_costc['field'] = 'total_cost';
                    $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                    if (empty($sum)) {
                        $sum = 0;
                    }
                    $datas_payment[$j] = $sum;
                    if (empty($_costc)) {
                        $_costc = $_cost;
                    }
                    if (empty($_costc)) {
                        $_costc = 0;
                    }
                    $datas_cost[$j] = $_costc;
                    $datas[$j] = $sum - $datas_cost[$j];
                    $j++;
                }
            }
        } else {
            foreach ($this->getYears() as $key => $value) {
                $labels[$key] = $value['year'];
                $whereJoin = array();
                $whereJoin['where'] = array(
                    'year(tbl_deliveries.date) =' => $value['year']
                );
                $whereJoin['where'][] = array('warehouseman_id >' => 0);
                if (!empty($data['customers_ch'])) {
                    $whereJoin['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                if (!empty($data['search_id_staff'])) {
                    $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                };
                $whereJoin['join'] = array();
                $whereJoin['field'] = 'grand_total';
                $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));

                $whereJoin_cost = array();
                $whereJoin_cost['where'] = array(
                    'year(tbl_orders.date) =' => $value['year'],
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                };
                if (!empty($data['customers_ch'])) {
                    $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_cost['join'] = array();
                $whereJoin_cost['field'] = 'total_cost_temporary_capital';
                $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));

                $whereJoin_costc = array();
                $whereJoin_costc['where'] = array(
                    'year(tbl_orders.date) =' => $value['year'],
                );
                if (!empty($data['search_id_staff'])) {
                    $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
                }
                if (!empty($data['customers_ch'])) {
                    $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
                }
                $whereJoin_costc['join'] = array();
                $whereJoin_costc['field'] = 'total_cost';
                $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

                if (empty($sum)) {
                    $sum = 0;
                }
                $datas_payment[$key] = $sum;
                if (empty($_costc)) {
                    $_costc = $_cost;
                }
                if (empty($_costc)) {
                    $_costc = 0;
                }
                $datas_cost[$key] = $_costc;
                $datas[$key] = $sum - $datas_cost[$key];
            }
        }
        $_data['labels'] = $labels;
        $_data['data'] = $datas;
        $_data['datas_payment'] = $datas_payment;
        $_data['datas_cost'] = $datas_cost;
        echo json_encode($_data);
    }

    public function getYears()
    {
        $this->db->distinct();
        $this->db->select('YEAR(date) as year');
        $this->db->order_by('YEAR(date)');
        $this->db->from('tbl_orders');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }

        return false;
    }

    public function top_client()
    {
        $data = $this->input->post();
        if (!empty($data['report_from']) && !empty($data['report_from'])) {
            $begin = to_sql_date($data['report_from']);
            $end = to_sql_date($data['report_to']);
            if ((strtotime($end) - strtotime($begin)) < 0) {
                $data = '';
                echo json_encode($data);
                die;
            }
        }
        $beginMonth = '';
        $endMonth = '';
        $months_report = $data['months_report'];
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'day') {
                $beginMonth = date('Y-m-d');
                $endMonth = date('Y-m-d');
            } elseif ($months_report == 'week') {
                $beginMonth = date('Y-m-d', strtotime('this week', time()));
                $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
                $endMonth = strftime("%Y-%m-%d", $week);
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }
        $data['customers_ch'] = trim($data['customers_ch'], 'customers__');
        $this->db->select('customer_id,SUM(grand_total) as grand_totals,customer_name');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_orders.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_orders.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->having('grand_totals > 0');
        if (!empty($data['customers_ch'])) {
            $this->db->where('tbl_orders.customer_id ', $data['customers_ch']);
        }
        if (!empty($data['search_id_staff'])) {
            $this->db->where_in('tbl_orders.employee_id ', $data['search_id_staff']);
        }
        $this->db->limit(5);
        $this->db->order_by('grand_totals', 'DESC');
        $this->db->group_by('customer_id');
        $datas = $this->db->get('tbl_orders')->result_array();
        $html = '';
        foreach ($datas as $key => $value) {
            $html .= '<div class="wrap_container">
                            <span style="float:left; width:70%; height: 28px; overflow: hidden;"><span class="wrap_number">' . ($key + 1) . '.</span> ' . $value['customer_name'] . '</span>
                            ' . (strlen($value['customer_name']) > 30 ? ' ...' : "") . '
                            <span style="color: #2e98ff; float: right; width: 30%; font-weight: 500; font-size: 15px; text-align: right;">' . number_format($value['grand_totals']) . '</span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="wrap_line"></div>';
        }
        echo json_encode($html);
    }

    public function top_staff()
    {
        $data = $this->input->post();
        if (!empty($data['report_from']) && !empty($data['report_from'])) {
            $begin = to_sql_date($data['report_from']);
            $end = to_sql_date($data['report_to']);
            if ((strtotime($end) - strtotime($begin)) < 0) {
                $data = '';
                echo json_encode($data);
                die;
            }
        }
        $beginMonth = '';
        $endMonth = '';
        $months_report = $data['months_report'];
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'day') {
                $beginMonth = date('Y-m-d');
                $endMonth = date('Y-m-d');
            } elseif ($months_report == 'week') {
                $beginMonth = date('Y-m-d', strtotime('this week', time()));
                $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
                $endMonth = strftime("%Y-%m-%d", $week);
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }
        $data['customers_ch'] = trim($data['customers_ch'], 'customers__');
        $this->db->select('employee_id,SUM(grand_total) as grand_totals');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_orders.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_orders.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->having('grand_totals > 0');
        if (!empty($data['customers_ch'])) {
            $this->db->where('tbl_orders.customer_id ', $data['customers_ch']);
        }
        if (!empty($data['search_id_staff'])) {
            $this->db->where_in('tbl_orders.employee_id ', $data['search_id_staff']);
        }
        $this->db->limit(5);
        $this->db->order_by('grand_totals', 'DESC');
        $this->db->group_by('employee_id');
        $datas = $this->db->get('tbl_orders')->result_array();
        $html = '';
        foreach ($datas as $key => $value) {
            $html .= '<div class="wrap_container">
                                <span style="float:left; width:70%; height: 28px; overflow: hidden;"><span class="wrap_number">' . ($key + 1) . '.</span> ' . get_staff_full_name($value['employee_id']) . '</span>
                                ' . (strlen(get_staff_full_name($value['employee_id'])) > 30 ? ' ...' : "") . '
                                <span style="color: #2e98ff; float: right; width: 30%; font-weight: 500; font-size: 15px; text-align: right;">' . number_format($value['grand_totals']) . '</span>
                                <div class="clearfix"></div>
                            </div>
                            <div class="wrap_line"></div>';
        }
        echo json_encode($html);
    }

    public function top_items()
    {
        $data = $this->input->post();
        if (!empty($data['report_from']) && !empty($data['report_from'])) {
            $begin = to_sql_date($data['report_from']);
            $end = to_sql_date($data['report_to']);
            if ((strtotime($end) - strtotime($begin)) < 0) {
                $data = '';
                echo json_encode($data);
                die;
            }
        }
        $beginMonth = '';
        $endMonth = '';
        $months_report = $data['months_report'];
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'day') {
                $beginMonth = date('Y-m-d');
                $endMonth = date('Y-m-d');
            } elseif ($months_report == 'week') {
                $beginMonth = date('Y-m-d', strtotime('this week', time()));
                $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
                $endMonth = strftime("%Y-%m-%d", $week);
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }
        $data['customers_ch'] = trim($data['customers_ch'], 'customers__');
        $this->db->select('tbl_order_items.item_id,tbl_order_items.type_item,tbl_order_items.item_code,tbl_order_items.item_name,SUM(tbl_order_items.quantity) as quantitys');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_orders.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_orders.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->having('quantitys > 0');
        if (!empty($data['customers_ch'])) {
            $this->db->where('tbl_orders.customer_id ', $data['customers_ch']);
        }
        if (!empty($data['search_id_staff'])) {
            $this->db->where_in('tbl_orders.employee_id ', $data['search_id_staff']);
        }
        $this->db->limit(5);
        $this->db->order_by('quantitys', 'DESC');
        $this->db->group_by('tbl_order_items.item_id,tbl_order_items.type_item');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id', 'left');
        $datas = $this->db->get('tbl_order_items')->result_array();
        $html = '';
        foreach ($datas as $key => $value) {
            $get_items = get_items($value['item_id'], $value['type_item']);
            $html .= '<div class="wrap_container">
                            <span style="float:left; width:70%; height: 28px; overflow: hidden;"><span class="wrap_number">' . ($key + 1) . '.</span> ' . ($get_items->name) . '</span>
                            ' . (strlen($get_items->name) > 30 ? ' ...' : "") . '
                            <span style="color: #2e98ff;float: right; width: 30%; font-weight: 500;font-size: 15px; text-align: right;">' . number_format($value['quantitys']) . '</span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="wrap_line"></div>';
        }
        echo json_encode($html);
    }

    public function count_all()
    {
        $data = $this->input->post();
        if (!empty($data['report_from']) && !empty($data['report_from'])) {
            $begin = to_sql_date($data['report_from']);
            $end = to_sql_date($data['report_to']);
            if ((strtotime($end) - strtotime($begin)) < 0) {
                // $data['subtotal'] = 0;
                // $data['subtotal1'] = 0;
                // $data['subtotal2'] = 0;

                $data['doanh_so_ban'] = 0;
                $data['doanh_thu'] = 0;
                $data['doanh_so_mua'] = 0;
                $data['doanh_so_mua_dtt'] = 0;
                $data['doanh_so_mua_ctt'] = 0;
                $data['chi_phi'] = 0;
                $data['loi_nhuan'] = 0;
                echo json_encode($data);
                die;
            }
        }
        $beginMonth = '';
        $endMonth = '';
        $months_report = $data['months_report'];
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'day') {
                $beginMonth = date('Y-m-d');
                $endMonth = date('Y-m-d');
            } elseif ($months_report == 'week') {
                $beginMonth = date('Y-m-d', strtotime('this week', time()));
                $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
                $endMonth = strftime("%Y-%m-%d", $week);
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }

        $data['customers_ch'] = trim($data['customers_ch'], 'customers__');
        // $whereJoin = array();
        // if (!empty($beginMonth) && $endMonth) {
        //     $whereJoin['where'] = array(
        //         'tbl_deliveries.date >=' => $beginMonth . ' 00:00:00',
        //         'tbl_deliveries.date <=' => $endMonth . ' 23:59:59',
        //     );
        // }
        // $whereJoin['where'][] = array('warehouseman_id >' => 0);
        // if (!empty($data['customers_ch'])) {
        //     $whereJoin['where'][] = array('customer_id ' => $data['customers_ch']);
        // }
        // if (!empty($data['search_id_staff'])) {
        //     $where_or = '';
        //     foreach ($data['search_id_staff'] as $key => $value) {
        //         $where_or = '(tbl_deliveries.employee_id = ' . $value . ') or ' . $where_or;
        //     }
        //     $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
        // }
        // $whereJoin['join'] = array();
        // $whereJoin['field'] = 'grand_total';
        // $subtotal = sum_from_table_join('tbl_deliveries', $whereJoin);
        // $whereJoin_cost = array();
        // if (!empty($beginMonth) && $endMonth) {
        //     $whereJoin_cost['where'] = array(
        //         'tbl_orders.date >=' => $beginMonth . ' 00:00:00',
        //         'tbl_orders.date <=' => $endMonth . ' 23:59:59',
        //     );
        // }
        // if (!empty($data['customers_ch'])) {
        //     $whereJoin_cost['where'][] = array('customer_id' => $data['customers_ch']);
        // }
        // if (!empty($data['search_id_staff'])) {
        //     $where_or = '';
        //     foreach ($data['search_id_staff'] as $key => $value) {
        //         $where_or = '(tbl_orders.employee_id = ' . $value . ') or ' . $where_or;
        //     }
        //     $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
        // }

        // $whereJoin_cost['join'] = array();
        // $whereJoin_cost['field'] = 'total_cost_temporary_capital';
        // $_cost = (sum_from_table_join('tbl_orders', $whereJoin_cost));

        // $whereJoin_costc = array();
        // if (!empty($beginMonth) && $endMonth) {
        //     $whereJoin_costc['where'] = array(
        //         'tbl_orders.date >=' => $beginMonth . ' 00:00:00',
        //         'tbl_orders.date <=' => $endMonth . ' 23:59:59',
        //     );
        // }
        // if (!empty($data['search_id_staff'])) {
        //     $where_or = '';
        //     foreach ($data['search_id_staff'] as $key => $value) {
        //         $where_or = '(tbl_orders.employee_id = ' . $value . ') or ' . $where_or;
        //     }
        //     $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
        // }
        // if (!empty($data['customers_ch'])) {
        //     $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
        // }
        // $whereJoin_costc['join'] = array();
        // $whereJoin_costc['field'] = 'total_cost';
        // $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));
        // if (empty($_costc)) {
        //     $_costc = $_cost;
        // }
        // $data['subtotal'] = number_format($subtotal);
        // $data['subtotal1'] = number_format($_costc);
        // $data['subtotal2'] = number_format($subtotal - $_costc);
        $data['date_update'] = "Updated " . _dt(date('Y-m-d H:i:s'));

        //Doanh số bán
        $this->db->select('
            SUM(tbl_orders.grand_total * tbl_orders.amount_to_vnd) as grand_total_vnd,
            SUM(IF(tbl_orders.amount_to_vnd > 1, tbl_orders.grand_total, 0)) as grand_total_usd
        ', false);
        $this->db->from('tbl_orders');
        $this->db->where('(tbl_orders.is_cancel = 0 OR tbl_orders.is_end = 1)');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_orders.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_orders.date <=', $endMonth . ' 23:59:59');
        }

        if (!empty($data['customers_ch'])) {
            $this->db->where('tbl_orders.customer_id', $data['customers_ch']);
        }

        if (!empty($data['search_id_staff'])) {
            $this->db->where_in('tbl_orders.employee_id', $data['search_id_staff']);
        }
        $order = $this->db->get()->row_array();

        $where = '';
        //doanh thu
        // $this->db->select('
        //     SUM(tbl_deliveries.grand_total * tbl_orders.amount_to_vnd) as grand_total_vnd
        // ', false);
        // $this->db->from('tbl_deliveries');
        // $this->db->join('tbl_orders', 'tbl_orders.id = tbl_deliveries.order_id');
        // if (!empty($beginMonth) && $endMonth) {
        //     $this->db->where('tbl_deliveries.date >=', $beginMonth . ' 00:00:00');
        //     $this->db->where('tbl_deliveries.date <=', $endMonth . ' 23:59:59');
        // }

        if (!empty($data['customers_ch'])) {
            // $this->db->where('tbl_deliveries.customer_id', $data['customers_ch']);
            $where .= ' AND tbl_deliveries.customer_id = ' . $data['customers_ch'] . '';
        }

        if (!empty($data['search_id_staff'])) {
            // $this->db->where_in('tbl_deliveries.employee_id', $data['search_id_staff']);
            $where .= ' AND tbl_deliveries.customer_id IN (' . implode($data['search_id_staff']) . ')';
        }

        // $this->db->where(' EXISTS (
        //     SELECT 1
        //     FROM tbl_invoice_items
        //     WHERE tbl_invoice_items.object_id = tbl_deliveries.id
        // )');
        // $delivery = $this->db->get()->row_array();

        // SELECT SQL_NO_CACHE SUM(tb_delivery.grand_total * tbl_orders.amount_to_vnd) as grand_total_vnd
        // FROM (
        //     SELECT tbl_deliveries.grand_total, tbl_deliveries.order_id
        //     FROM `tbl_deliveries`
        //     WHERE `tbl_deliveries`.`date` >= '2024-01-01 00:00:00' AND `tbl_deliveries`.`date` <= '2024-12-31 23:59:59' AND  EXISTS (
        //             SELECT 1
        //             FROM tbl_invoice_items
        //             WHERE tbl_invoice_items.object_id = tbl_deliveries.id
        //         )) tb_delivery

        // JOIN `tbl_orders` ON `tbl_orders`.`id` = tb_delivery.`order_id`
        $query_delivery = "
            SELECT 
                SUM(tb_delivery.grand_total * tbl_orders.amount_to_vnd) as grand_total_vnd
            FROM (
                SELECT tbl_deliveries.grand_total, tbl_deliveries.order_id
                FROM `tbl_deliveries`
                WHERE `tbl_deliveries`.`date` >= '" . $beginMonth . " 00:00:00' AND `tbl_deliveries`.`date` <= '" . $endMonth . " 23:59:59' $where AND  EXISTS (
                    SELECT 1
                    FROM tbl_invoice_items
                    WHERE tbl_invoice_items.object_id = tbl_deliveries.id
            )) tb_delivery
            JOIN `tbl_orders` ON `tbl_orders`.`id` = tb_delivery.`order_id`
        ";
        $delivery = $this->db->query($query_delivery)->row_array();
        // print_arrays($this->db->last_query());

        //doanh số mua
        $this->db->select('
            SUM(tblpurchase_order.total_dqd) as total_dqd,
            SUM(tblpurchase_order.price_other_expenses + tblpurchase_order.amount_paid) as total_payment,
        ', false);
        $this->db->from('tblpurchase_order');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tblpurchase_order.date >=', $beginMonth);
            $this->db->where('tblpurchase_order.date <=', $endMonth);
        }
        // $this->db->where('tblpurchase_order.cancel', 0);
        $purchase_order = $this->db->get()->row_array();

        //Chi phí
        $this->db->select('
            SUM(tblother_payslips.total) as total,
        ', false);
        $this->db->from('tblother_payslips');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tblother_payslips.date >=', $beginMonth);
            $this->db->where('tblother_payslips.date <=', $endMonth);
        }
        $this->db->where('tblother_payslips.id_costs !=', 0);
        $other_payslips = $this->db->get()->row_array();

        $data['doanh_so_ban'] = $order['grand_total_vnd'] ?? 0;
        $data['doanh_thu'] = $delivery['grand_total_vnd'] ?? 0;
        $data['doanh_so_mua'] = $purchase_order['total_dqd'] ?? 0;
        $data['doanh_so_mua_dtt'] = $purchase_order['total_payment'] ?? 0;
        $data['doanh_so_mua_ctt'] = (float)$purchase_order['total_dqd'] - (float)$purchase_order['total_payment'];
        $data['chi_phi'] = $other_payslips['total'] ?? 0;
        $data['loi_nhuan'] = $data['doanh_thu'] - $data['doanh_so_mua'] - $data['chi_phi'];
        $data['doanh_so_ban_usd'] = $order['grand_total_usd'] ?? 0;

        echo json_encode($data);
    }

    public function loadSalePerformance()
    {
        $month = $this->input->post('month');
        $precious = $this->input->post('precious');
        $year = $this->input->post('year');
        $branch = $this->input->post('branch');

        $data['month'] = $month;
        $data['precious'] = $precious;
        $data['year'] = $year;
        $data['branch'] = $branch;

        $data['title'] = lang('expenses_vs_income_reports');
        $this->load->view('admin/reports/load_sale_performance', $data);
    }

    public function infoSalePerformance()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }

        $arrParams = $this->input->get('arrParams');
        $start_date = $arrParams[1]['mValue'];
        $end_date = $arrParams[2]['mValue'];
        $type_object = $arrParams[3]['mValue'];
        $id_branch = $arrParams[4]['mValue'];
        $employee_id = $arrParams[5]['mValue'];
        $title = '';
        $note = '';
        $link = '';
        $typeDetail = '';

        $name_branch = '';
        if (!empty($id_branch)) {
            $branch = $this->site_model->getBranchById($id_branch);
            $name_branch = $branch['name'];
        }

        if ($type_object == "doanh_thu_ban_hang") {
            $title = "Doanh thu bán hàng $name_branch";
            $note = "Doanh thu bán hàng đã trừ thuế + CK";
            // $link = base_url('admin/reports/getOrdersSalePerformance');
            $link = base_url('admin/reports/getOrdersTotalSalePerformance');
        } elseif ($type_object == "chiet_khau_hoa_don") {
            $title = "Chiết khấu hóa đơn theo nhân viên $name_branch";
            $link = base_url('admin/reports/getOrdersDiscountStaffPerformance');
            // $link = base_url('admin/reports/getOrdersDiscountPerformance');
        } elseif ($type_object == "hang_tra_ve") {
            $title = "Hàng trả về nhân viên $name_branch";
            // $note = "Hàng trả về đã trừ thuế";
            $link = base_url('admin/reports/getReturnsStaffPerformance');
            // $link = base_url('admin/reports/getReturnsPerformance');
        } elseif ($type_object == "gia_von_hang_ban") {
            $title = "Giá vốn hàng bán $name_branch";
            $link = base_url('admin/reports/getCostPrice');
        } elseif ($type_object == "chi_phi") {
            $title = "Chi phí $name_branch";
            // $note = "Chi phí ngoại trừ [Chi phí mua hàng phân bổ vào giá vốn, Chi phí thuê xe bên Trung Quốc, Chi phí vận chuyển về Hà Nội, Các khoản giảm trừ doanh thu, Trả lại hàng bán, Chi phí mua hàng, Mua nguyên liệu, vật liệu, Mua nguyên vật liệu cho đồ nội thất ( sơn, gỗ,…), Mua hàng hóa]";
            $note = "Chi phí ngoại trừ [Phiếu không tính BCLN] + đã duyệt";
            $link = base_url('admin/reports/getChargeTotal');
        } elseif ($type_object == "thu_nhap_khac") {
            $title = "Thu nhập khác $name_branch";
            $link = base_url('admin/reports/getOtherIncome');
        } elseif ($type_object == "chi_tiet_doanh_thu") {
            $title = "Chi tiết doanh thu";
            $note = "Doanh thu bán hàng đã trừ thuế + CK";
            $link = base_url('admin/reports/getOrdersSalePerformance');
            $typeDetail = "chi_tiet";
        } elseif ($type_object == "gtgt") {
            $title = "Thuế GTGT đầu ra";
            $note = "";
            $link = base_url('admin/reports/getOrdersTax');
        } elseif ($type_object == "chi_tiet_chiet_khau_hoa_don") {
            $title = "Chi tiết chiết khấu hóa đơn";
            $link = base_url('admin/reports/getOrdersDiscountPerformance');
            $typeDetail = "chi_tiet";
        } elseif ($type_object == "chi_tiet_hang_tra_ve") {
            $title = "Chi tiết hàng trả về";
            $link = base_url('admin/reports/getReturnsPerformance');
            $typeDetail = "chi_tiet";
        } elseif ($type_object == "chi_phi_ghi_nhan") {
            $title = "Chi phí ghi nhận";
            $link = base_url('admin/reports/getTotalService');
        }

        $data['typeDetail'] = $typeDetail;
        $data['link'] = $link;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['type_object'] = $type_object;
        $data['id_branch'] = $id_branch;
        $data['employee_id'] = $employee_id;
        $data['title'] = $title;
        $data['note'] = $note;
        $this->load->view('admin/reports/info_sale_performance', $data);
    }

    public function getOrdersTotalSalePerformance()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');

        $nameDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid, 
                GROUP_CONCAT(tbldepartments.name SEPARATOR '</br>') as name_department,
                0 as number_order
            FROM tblstaff_departments
            INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) dt";

        $this->datatables->select("
            dt.name_department as department_name,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as name_employee,
            SUM(tbl_orders.grand_total + tbl_orders.total_discount_percent_items + tbl_orders.total_discount_direct_items + tbl_orders.total_discount_percent + tbl_orders.total_discount_direct - tbl_orders.total_tax) as grand_total,
            tblbranch.name as name_branch,
            tbl_orders.id_branch as id_branch,
            tbl_orders.employee_id as employee_id,
        ")
            ->from('tbl_orders')
            ->join('tblbranch', 'tblbranch.id = tbl_orders.id_branch', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_orders.employee_id', 'left')
            ->join($nameDepartment, 'dt.staffid = tblstaff.staffid', 'left');

        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', $end_date);

        $this->datatables->group_by('tbl_orders.id_branch, tbl_orders.employee_id');
        $this->db->order_by('tbl_orders.id_branch asc, dt.name_department DESC, grand_total DESC');

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->datatables->where('(tbl_orders.id_branch = 0 OR tbl_orders.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->datatables->where('tbl_orders.id_branch', $branch_id);
            }
        }

        $data = json_decode($this->datatables->generate());

        $this->db->select('*');
        $this->db->from('tblbranch');
        $this->db->where('tblbranch.id', BRANCH_DEFAULT);
        $branch_default = $this->db->get()->row_array();

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        $iDisplayStart = $this->input->post('iDisplayStart');
        $id_group_brand = -1;
        foreach ($data->aaData as $key => $value) {

            $name_department = $value[0];
            $name_employee = $value[1];
            $grand_total = $value[2];
            $name_branch = !empty($value[3]) ? $value[3] : $branch_default['name'];
            $id_branch = $value[4] == 0 ? BRANCH_DEFAULT : $value[4];
            $employee_id = $value[5];

            if ($id_branch != $id_group_brand) {
                $arrData[$index][0] = '';
                $arrData[$index][1] = '<div class="group-orders">' . $name_branch . '</div>';
                $arrData[$index][2] = '';
                $arrData[$index][3] = '';
                $index++;
                $id_group_brand = $id_branch;
            }

            $iDisplayStart++;
            $arrData[$index][0] = '<div class="text-center">' . $iDisplayStart . '</div>';
            $arrData[$index][1] = '<div class="text-left">' . $name_department . '</div>';
            $arrData[$index][2] = '<div><a class="tnh-modal-attr2" start_date="' . $start_date . '" end_date="' . $end_date . '" type_object="chi_tiet_doanh_thu" id_branch="' . $branch_id . '" employee_id="' . $employee_id . '" data-toggle="modal" data-target="#myModal2" href="' . base_url('admin/reports/infoSalePerformance') . '">' . $name_employee . '</a></div>';
            $arrData[$index][3] = '<div class="text-right">' . formatMoney($grand_total) . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }

    public function getOrdersSalePerformance()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');
        $employee_id = $this->input->post('employee_id');

        $this->datatables->select("
            tbl_orders.id as id,
            tbl_orders.date as date,
            tbl_orders.reference_no as reference_no,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as name_employee,
            (tbl_orders.grand_total + tbl_orders.total_discount_percent_items + tbl_orders.total_discount_direct_items + tbl_orders.total_discount_percent + tbl_orders.total_discount_direct - tbl_orders.total_tax) as grand_total
        ")
            ->from('tbl_orders')
            ->join('tblstaff', 'tblstaff.staffid = tbl_orders.employee_id', 'left');

        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', $end_date);

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->datatables->where('(tbl_orders.id_branch = 0 OR tbl_orders.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->datatables->where('tbl_orders.id_branch', $branch_id);
            }
        }

        if ($employee_id > 0) {
            $this->datatables->where('tbl_orders.employee_id', $employee_id);
        }

        $this->db->order_by('tbl_orders.date DESC');

        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        foreach ($data->aaData as $key => $value) {
            $order_id = $value[0];
            $date = _d($value[1]);
            $reference_no = $value[2];
            $name_employee = $value[3];
            $grand_total = $value[4];

            $arrData[$index][0] = '<div style="width: 150px;">' . $date . '</div>';
            $arrData[$index][1] = '<div><a href="' . base_url('admin/orders/view_order/' . $order_id) . '" class="tnh-modal3" data-toggle="modal" data-target="#tnhModal3">' . $reference_no . '</a></div>';
            $arrData[$index][2] = '<div>' . $name_employee . '</div>';
            $arrData[$index][3] = '<div class="text-right">' . formatMoney($grand_total) . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }


    //discount performance
    public function getOrdersDiscountStaffPerformance()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');

        $this->datatables->select("
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as name_employee,
            SUM(tbl_orders.total_discount_percent_items + tbl_orders.total_discount_direct_items + tbl_orders.total_discount_percent + tbl_orders.total_discount_direct) as grand_total,
            tblbranch.name as name_branch,
            tbl_orders.id_branch as id_branch,
            tbl_orders.employee_id as employee_id,
        ")
            ->from('tbl_orders')
            ->join('tblbranch', 'tblbranch.id = tbl_orders.id_branch', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_orders.employee_id', 'left');

        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', $end_date);
        $this->datatables->where('(tbl_orders.total_discount_percent_items + tbl_orders.total_discount_direct_items + tbl_orders.total_discount_percent + tbl_orders.total_discount_direct) > 0');

        $this->datatables->group_by('tbl_orders.id_branch, tbl_orders.employee_id');
        $this->db->order_by('tbl_orders.id_branch asc');

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->datatables->where('(tbl_orders.id_branch = 0 OR tbl_orders.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->datatables->where('tbl_orders.id_branch', $branch_id);
            }
        }

        $data = json_decode($this->datatables->generate());

        $this->db->select('*');
        $this->db->from('tblbranch');
        $this->db->where('tblbranch.id', BRANCH_DEFAULT);
        $branch_default = $this->db->get()->row_array();

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        $iDisplayStart = $this->input->post('iDisplayStart');
        $id_group_brand = -1;
        foreach ($data->aaData as $key => $value) {

            $name_employee = $value[0];
            $grand_total = $value[1];
            $name_branch = !empty($value[2]) ? $value[2] : $branch_default['name'];
            $id_branch = $value[3] == 0 ? BRANCH_DEFAULT : $value[3];
            $employee_id = $value[4];

            if ($id_branch != $id_group_brand) {
                $arrData[$index][0] = '';
                $arrData[$index][1] = '<div class="group-orders">' . $name_branch . '</div>';
                $arrData[$index][2] = '';
                $index++;
                $id_group_brand = $id_branch;
            }

            $iDisplayStart++;
            $arrData[$index][0] = '<div class="text-center">' . $iDisplayStart . '</div>';
            $arrData[$index][1] = '<div><a class="tnh-modal-attr2" start_date="' . $start_date . '" end_date="' . $end_date . '" type_object="chi_tiet_chiet_khau_hoa_don" id_branch="' . $branch_id . '" employee_id="' . $employee_id . '" data-toggle="modal" data-target="#myModal2" href="' . base_url('admin/reports/infoSalePerformance') . '">' . $name_employee . '</a></div>';
            $arrData[$index][2] = '<div class="text-right">' . formatMoney($grand_total) . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }

    public function getOrdersDiscountPerformance()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');
        $employee_id = $this->input->post('employee_id');

        $this->datatables->select("
            tbl_orders.id as id,
            tbl_orders.date as date,
            tbl_orders.reference_no as reference_no,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as name_employee,
            (tbl_orders.total_discount_percent_items + tbl_orders.total_discount_direct_items + tbl_orders.total_discount_percent + tbl_orders.total_discount_direct) as total_discount
        ")
            ->from('tbl_orders')
            ->join('tblstaff', 'tblstaff.staffid = tbl_orders.employee_id', 'left');

        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', $end_date);
        $this->datatables->where(
            '(tbl_orders.total_discount_percent_items + tbl_orders.total_discount_direct_items + tbl_orders.total_discount_percent + tbl_orders.total_discount_direct) >',
            0
        );
        $this->datatables->where('tbl_orders.employee_id', $employee_id);

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->datatables->where('(tbl_orders.id_branch = 0 OR tbl_orders.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->datatables->where('tbl_orders.id_branch', $branch_id);
            }
        }

        $this->db->order_by('tbl_orders.date DESC');

        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        foreach ($data->aaData as $key => $value) {
            $order_id = $value[0];
            $date = _d($value[1]);
            $reference_no = $value[2];
            $name_employee = $value[3];
            $grand_total = $value[4];

            $arrData[$index][0] = '<div style="width: 150px;">' . $date . '</div>';
            $arrData[$index][1] = '<div><a href="' . base_url('admin/orders/view_order/' . $order_id) . '" class="tnh-modal3" data-toggle="modal" data-target="#tnhModal3">' . $reference_no . '</a></div>';
            $arrData[$index][2] = '<div>' . $name_employee . '</div>';
            $arrData[$index][3] = '<div class="text-right">' . formatMoney($grand_total) . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }

    //returns performance
    public function getReturnsStaffPerformance()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');

        // SUM(tbl_returned_goods.grand_total - tbl_returned_goods.total_tax) as grand_total
        $this->datatables->select("
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as name_employee,
            SUM(tbl_returned_goods.grand_total) as grand_total,
            tblbranch.name as name_branch,
            tbl_returned_goods.id_branch as id_branch,
            tbl_returned_goods.employee_id as employee_id,
        ")
            ->from('tbl_returned_goods')
            ->join('tblbranch', 'tblbranch.id = tbl_returned_goods.id_branch', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_returned_goods.employee_id', 'left');

        $this->datatables->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") <=', $end_date);
        $this->datatables->group_by('tbl_returned_goods.id_branch, tbl_returned_goods.employee_id');
        $this->db->order_by('tbl_returned_goods.id_branch asc');

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->datatables->where('(tbl_returned_goods.id_branch = 0 OR tbl_returned_goods.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->datatables->where('tbl_returned_goods.id_branch', $branch_id);
            }
        }

        $data = json_decode($this->datatables->generate());

        $this->db->select('*');
        $this->db->from('tblbranch');
        $this->db->where('tblbranch.id', BRANCH_DEFAULT);
        $branch_default = $this->db->get()->row_array();

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        $iDisplayStart = $this->input->post('iDisplayStart');
        $id_group_brand = -1;
        foreach ($data->aaData as $key => $value) {

            $name_employee = $value[0];
            $grand_total = $value[1];
            $name_branch = !empty($value[2]) ? $value[2] : $branch_default['name'];
            $id_branch = $value[3] == 0 ? BRANCH_DEFAULT : $value[3];
            $employee_id = $value[4];

            if ($id_branch != $id_group_brand) {
                $arrData[$index][0] = '';
                $arrData[$index][1] = '<div class="group-orders">' . $name_branch . '</div>';
                $arrData[$index][2] = '';
                $index++;
                $id_group_brand = $id_branch;
            }

            $iDisplayStart++;
            $arrData[$index][0] = '<div class="text-center">' . $iDisplayStart . '</div>';
            $arrData[$index][1] = '<div><a class="tnh-modal-attr2" start_date="' . $start_date . '" end_date="' . $end_date . '" type_object="chi_tiet_hang_tra_ve" id_branch="' . $branch_id . '" employee_id="' . $employee_id . '" data-toggle="modal" data-target="#myModal2" href="' . base_url('admin/reports/infoSalePerformance') . '">' . $name_employee . '</a></div>';
            $arrData[$index][2] = '<div class="text-right">' . formatMoney($grand_total) . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }

    public function getReturnsPerformance()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');
        $employee_id = $this->input->post('employee_id');

        // (tbl_returned_goods.grand_total - tbl_returned_goods.total_tax)
        $this->datatables->select("
            tbl_returned_goods.id as id,
            tbl_returned_goods.date as date,
            tbl_returned_goods.reference_no as reference_no,
            (tbl_returned_goods.grand_total) as total_discount
        ")
            ->from('tbl_returned_goods');

        $this->datatables->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") <=', $end_date);
        $this->datatables->where('tbl_returned_goods.employee_id', $employee_id);

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->datatables->where('(tbl_returned_goods.id_branch = 0 OR tbl_returned_goods.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->datatables->where('tbl_returned_goods.id_branch', $branch_id);
            }
        }

        $this->db->order_by('tbl_returned_goods.date DESC');

        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        foreach ($data->aaData as $key => $value) {
            $returned_goods_id = $value[0];
            $date = _d($value[1]);
            $reference_no = $value[2];
            $grand_total = $value[3];

            $arrData[$index][0] = '<div style="width: 150px;">' . $date . '</div>';
            $arrData[$index][1] = '<div><a href="' . base_url('admin/returned_goods/view_returned_goods/' . $returned_goods_id) . '" class="tnh-modal3" data-toggle="modal" data-target="#tnhModal3">' . $reference_no . '</a></div>';
            $arrData[$index][2] = '<div class="text-right">' . formatMoney($grand_total) . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }

    public function getCostPrice()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');

        $this->datatables->select("
            tbl_orders.id as id,
            tbl_orders.date as date,
            tbl_orders.reference_no as reference_no,
            SUM(IF (tbl_orders.total_cost > 0, tbl_orders.total_cost, tbl_orders.total_cost_temporary_capital)) as cost_price
        ")
            ->from('tbl_orders');

        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', $end_date);

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->datatables->where('(tbl_orders.id_branch = 0 OR tbl_orders.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->datatables->where('tbl_orders.id_branch', $branch_id);
            }
        }

        $this->db->group_by('tbl_orders.id');
        $this->db->order_by('tbl_orders.date DESC');

        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        foreach ($data->aaData as $key => $value) {
            $order_id = $value[0];
            $date = _d($value[1]);
            $reference_no = $value[2];
            $grand_total = $value[3];

            $arrData[$index][0] = '<div style="width: 150px;">' . $date . '</div>';
            $arrData[$index][1] = '<div><a href="' . base_url('admin/orders/view_order/' . $order_id) . '" class="tnh-modal2" data-toggle="modal" data-target="#myModal2">' . $reference_no . '</a></div>';
            $arrData[$index][2] = '<div class="text-right">' . formatMoney($grand_total) . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }

    public function getChargeTotal()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');
        // WHERE tblother_payslips.id_costs NOT IN (195, 202, 203, 199, 200, 2, 58, 59, 61) AND tblother_payslips.objects != 4
        // tblpay_slip.id_costs NOT IN (195, 202, 203, 199, 200, 2, 58, 59, 61)
        // WHERE tblpay_slip.not_kqkd = 0

        $whereOtherPayslips = "";
        $wherePayslip = "";
        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $whereOtherPayslips .= ' AND (tblother_payslips.id_branch = 0 OR tblother_payslips.id_branch = ' . BRANCH_DEFAULT . ')';
                $wherePayslip .= ' AND (tblpay_slip.id_branch = 0 OR tblpay_slip.id_branch = ' . BRANCH_DEFAULT . ')';
            } else {
                $whereOtherPayslips .= ' AND (tblother_payslips.id_branch ' . $branch_id . ')';
                $wherePayslip .= ' AND (tblpay_slip.id_branch ' . $branch_id . ')';
            }
        }

        $cs = "(
            SELECT 
                0 as id,
                'otherPaySlips' as type,
                tblother_payslips.id as id_ref,
                tblother_payslips.id_costs as id_costs,
                CONCAT(tblother_payslips.prefix, '-', tblother_payslips.code) as reference_no,
                tblother_payslips.date as date,
                tblother_payslips.total as total,
                tblcosts.name as costs,
                tblother_payslips.note as note
            FROM tblother_payslips
            LEFT JOIN tblcosts ON tblcosts.id = tblother_payslips.id_costs
            WHERE tblother_payslips.not_kqkd = 0 $whereOtherPayslips
        ) cs";

        $this->datatables->select("
            tblcosts.id as id,
            tblcosts.code as code,
            0 as date,
            0 as reference_no,
            0 as name_cost,
            0 as total,
            0 as costs,
            0 as note,
        ")
            ->from('tblcosts');
        // $this->datatables->join($cs, 'cs.id = 0');
        // $this->datatables->join('tblcosts', 'tblcosts.id = cs.id_costs', 'left');

        // $this->datatables->where('DATE_FORMAT(cs.date, "%Y-%m-%d") >=', $start_date);
        // $this->datatables->where('DATE_FORMAT(cs.date, "%Y-%m-%d") <=', $end_date);

        $this->db->order_by('cs.date DESC');

        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        foreach ($data->aaData as $key => $value) {
            $id_ref = $value[0];
            $type = $value[1];
            $date = _d($value[2]);
            $reference_no = $value[3];
            $name_cost = $value[4];
            $grand_total = $value[5];
            $costs = $value[6];
            $note = $value[7];

            $arrData[$index][0] = '<div style="width: 70px;">' . $date . '</div>';
            if ($type == "otherPaySlips") {
                $arrData[$index][1] = '<div style="width: 100px;"><a href="#" onclick="view_other_payslips(' . $id_ref . ')">' . $reference_no . '</a></div>';
            } elseif ($type == "paySlips") {
                $arrData[$index][1] = '<div style="width: 100px;"><a href="#" onclick="view_pay_slip(' . $id_ref . ')">' . $reference_no . '</a></div>';
            }

            $arrData[$index][2] = '<div style="width: 200px;">' . $name_cost . '</div>';
            $arrData[$index][3] = '<div style="width: 100px;" class="text-right">' . formatMoney($grand_total) . '</div>';
            $arrData[$index][4] = '<div style="width: 100px;" class="">' . $costs . '</div>';
            $arrData[$index][5] = '<div style="width: 150px;" class="">' . $note . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }

    public function getCharge()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');
        // WHERE tblother_payslips.id_costs NOT IN (195, 202, 203, 199, 200, 2, 58, 59, 61) AND tblother_payslips.objects != 4
        // tblpay_slip.id_costs NOT IN (195, 202, 203, 199, 200, 2, 58, 59, 61)
        // WHERE tblpay_slip.not_kqkd = 0

        $whereOtherPayslips = "";
        $wherePayslip = "";
        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $whereOtherPayslips .= ' AND (tblother_payslips.id_branch = 0 OR tblother_payslips.id_branch = ' . BRANCH_DEFAULT . ')';
                $wherePayslip .= ' AND (tblpay_slip.id_branch = 0 OR tblpay_slip.id_branch = ' . BRANCH_DEFAULT . ')';
            } else {
                $whereOtherPayslips .= ' AND (tblother_payslips.id_branch ' . $branch_id . ')';
                $wherePayslip .= ' AND (tblpay_slip.id_branch ' . $branch_id . ')';
            }
        }

        $cs = "(
            SELECT 
                0 as id,
                'otherPaySlips' as type,
                tblother_payslips.id as id_ref,
                tblother_payslips.id_costs as id_costs,
                CONCAT(tblother_payslips.prefix, '-', tblother_payslips.code) as reference_no,
                tblother_payslips.date as date,
                tblother_payslips.total as total,
                tblcosts.name as costs,
                tblother_payslips.note as note
            FROM tblother_payslips
            LEFT JOIN tblcosts ON tblcosts.id = tblother_payslips.id_costs
            WHERE tblother_payslips.not_kqkd = 0 $whereOtherPayslips
        ) cs";

        // UNION ALL

        // SELECT 
        //     0 as id,
        //     'paySlips' as type,
        //     tblpay_slip.id as id_ref,
        //     tblpay_slip.id_costs as id_costs,
        //     CONCAT(tblpay_slip.prefix, '-', tblpay_slip.code) as reference_no,
        //     tblpay_slip.date as date,
        //     tblpay_slip.total as total,
        //     tblcosts.name as costs,
        //     tblpay_slip.note as note
        // FROM tblpay_slip
        // LEFT JOIN tblcosts ON tblcosts.id = tblpay_slip.id_costs
        // WHERE tblpay_slip.id != 0 $wherePayslip

        $this->datatables->select("
            cs.id_ref as id_ref,
            cs.type as type,
            cs.date as date,
            cs.reference_no as reference_no,
            tblcosts.name as name_cost,
            cs.total as total,
            cs.costs as costs,
            cs.note as note,
        ")
            ->from('(SELECT 1) as tbb');
        $this->datatables->join($cs, 'cs.id = 0');
        $this->datatables->join('tblcosts', 'tblcosts.id = cs.id_costs', 'left');

        $this->datatables->where('DATE_FORMAT(cs.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(cs.date, "%Y-%m-%d") <=', $end_date);

        $this->db->order_by('cs.date DESC');

        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        foreach ($data->aaData as $key => $value) {
            $id_ref = $value[0];
            $type = $value[1];
            $date = _d($value[2]);
            $reference_no = $value[3];
            $name_cost = $value[4];
            $grand_total = $value[5];
            $costs = $value[6];
            $note = $value[7];

            $arrData[$index][0] = '<div style="width: 70px;">' . $date . '</div>';
            if ($type == "otherPaySlips") {
                $arrData[$index][1] = '<div style="width: 100px;"><a href="#" onclick="view_other_payslips(' . $id_ref . ')">' . $reference_no . '</a></div>';
            } elseif ($type == "paySlips") {
                $arrData[$index][1] = '<div style="width: 100px;"><a href="#" onclick="view_pay_slip(' . $id_ref . ')">' . $reference_no . '</a></div>';
            }

            $arrData[$index][2] = '<div style="width: 200px;">' . $name_cost . '</div>';
            $arrData[$index][3] = '<div style="width: 100px;" class="text-right">' . formatMoney($grand_total) . '</div>';
            $arrData[$index][4] = '<div style="width: 100px;" class="">' . $costs . '</div>';
            $arrData[$index][5] = '<div style="width: 150px;" class="">' . $note . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }

    public function getOtherIncome()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');

        $this->datatables->select("
            tblother_payslips_coupon.id as id,
            tblother_payslips_coupon.date as date,
            CONCAT(tblother_payslips_coupon.prefix, '-', tblother_payslips_coupon.code) as reference_no,
            tblother_payslips_coupon.total as cost_price,
            tblother_payslips_coupon.note as note
        ")
            ->from('tblother_payslips_coupon');

        $this->datatables->where('DATE_FORMAT(tblother_payslips_coupon.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tblother_payslips_coupon.date, "%Y-%m-%d") <=', $end_date);
        $this->datatables->where('tblother_payslips_coupon.not_kqkd', 0);
        // $this->datatables->where('tblother_payslips_coupon.objects !=', 4);

        if (!empty($branch_id)) {
            $this->datatables->join('tblstaff', 'tblstaff.staffid = tblother_payslips_coupon.staff_id');
            if ($branch_id == BRANCH_DEFAULT) {
                $this->datatables->where('(tblstaff.id_branch = 0 OR tblstaff.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->datatables->where('tblstaff.id_branch', $branch_id);
            }
        }

        $this->db->order_by('tblother_payslips_coupon.date DESC');

        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        foreach ($data->aaData as $key => $value) {
            $other_payslips_coupon_id = $value[0];
            $date = _d($value[1]);
            $reference_no = $value[2];
            $grand_total = $value[3];
            $note = $value[4];

            $arrData[$index][0] = '<div style="min-width: 100px;">' . $date . '</div>';
            $arrData[$index][1] = '<div style="min-width: 120px;"><a href="#" onclick="edit_other_payslips_coupon_v1(' . $other_payslips_coupon_id . ')">' . $reference_no . '</a></div>';
            $arrData[$index][2] = '<div style="min-width: 120px;" class="text-right">' . formatMoney($grand_total) . '</div>';
            $arrData[$index][3] = '<div>' . $note . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }

    public function report_charge()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('report_charge');
        }
    }

    public function view_costs_detail_charge($id = '')
    {
        $data['id'] = $id;
        $this->load->view('admin/reports/fund_balance/view_costs_detail_charge', $data);
    }

    public function history_costs_charge($id = '')
    {
        $this->app->get_table_data('history_costs_charge', array('id' => $id));
    }

    public function getOrdersTax()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');

        $this->datatables->select("
            tbl_orders.id as id,
            tbl_orders.date as date,
            tbl_orders.reference_no as reference_no,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as name_employee,
            (tbl_orders.total_tax) as grand_total_tax
        ")
            ->from('tbl_orders')
            ->join('tblstaff', 'tblstaff.staffid = tbl_orders.employee_id', 'left');

        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', $end_date);
        $this->datatables->where('(tbl_orders.total_tax) >', 0);

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->datatables->where('(tbl_orders.id_branch = 0 OR tbl_orders.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->datatables->where('tbl_orders.id_branch', $branch_id);
            }
        }

        $this->db->order_by('tbl_orders.date DESC');

        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandTotal = 0;
        foreach ($data->aaData as $key => $value) {
            $order_id = $value[0];
            $date = _d($value[1]);
            $reference_no = $value[2];
            $name_employee = $value[3];
            $grand_total = $value[4];

            $arrData[$index][0] = '<div style="width: 150px;">' . $date . '</div>';
            $arrData[$index][1] = '<div><a href="' . base_url('admin/orders/view_order/' . $order_id) . '" class="tnh-modal2" data-toggle="modal" data-target="#myModal2">' . $reference_no . '</a></div>';
            $arrData[$index][2] = '<div>' . $name_employee . '</div>';
            $arrData[$index][3] = '<div class="text-right">' . formatMoney($grand_total) . '</div>';

            $grandTotal += $grand_total;
            $index++;
        }

        $data->aaData = $arrData;
        $data->grandTotal = $grandTotal;
        echo json_encode($data);
    }

    public function getTotalService()
    {
        if (!$this->perViewExpensesIncome) {
            accessDenied();
        }

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $branch_id = $this->input->post('id_branch');

        $this->datatables->select("
            tbl_services.id as id,
            tbl_services.date as date,
            CONCAT(tbl_services.prefix, tbl_services.code) as reference_no,
            tbl_services.subtotal as subtotal,
            tbl_services.payment as payment,
            (tbl_services.subtotal - tbl_services.payment) as rest,
        ")
            ->from('tbl_services');
        $this->datatables->join('tblstaff', 'tblstaff.staffid = tbl_services.staff_id', 'left');

        $this->datatables->where('DATE_FORMAT(tbl_services.date, "%Y-%m-%d") >=', $start_date);
        $this->datatables->where('DATE_FORMAT(tbl_services.date, "%Y-%m-%d") <=', $end_date);
        $this->datatables->where('tbl_services.status', 1);
        $this->datatables->where('tbl_services.not_kqkd !=', 1);

        if (!empty($branch_id)) {
            if ($branch_id == BRANCH_DEFAULT) {
                $this->datatables->where('(tblstaff.id_branch = 0 OR tblstaff.id_branch = ' . BRANCH_DEFAULT . ')');
            } else {
                $this->datatables->where('tblstaff.id_branch', $branch_id);
            }
        }

        // $this->db->order_by('tbl_returned_goods.date DESC');

        $data = json_decode($this->datatables->generate());

        $arrData = [];
        $index = 0;
        $grandSubTotal = 0;
        $grandSubPayment = 0;
        $grandRest = 0;
        foreach ($data->aaData as $key => $value) {
            $service_id = $value[0];
            $date = _d($value[1]);
            $reference_no = $value[2];
            $subtotal = $value[3];
            $payment = $value[4];
            $rest = $value[5];

            $arrData[$index][0] = '<div style="width: 150px;">' . $date . '</div>';
            $arrData[$index][1] = '<div><a href="#" onclick="view_detail_service(' . $service_id . '); return false;">' . $reference_no . '</a></div>';
            $arrData[$index][2] = '<div class="text-right">' . formatMoney($subtotal) . '</div>';
            $arrData[$index][3] = '<div class="text-right">' . formatMoney($payment) . '</div>';
            $arrData[$index][4] = '<div class="text-right">' . formatMoney($rest) . '</div>';

            $grandSubTotal += $subtotal;
            $grandSubPayment += $payment;
            $grandRest += $rest;
            $index++;
        }

        $data->aaData = $arrData;
        $data->tonggiatri = $grandSubTotal;
        $data->tongchi = $grandSubPayment;
        $data->tongtien = $grandRest;
        echo json_encode($data);
    }

    function exportExcelSaleListing()
    {
        if (!$this->perViewSalesOfOrder) {
            accessDenied($js = true);
        }

        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            // print_arrays($this->input->post());
            $customer_search = $this->input->post('customer_search');
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');

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

            if (empty($customer_search)) {
                $response = array(
                    'result' => 0,
                    'message' => lang('Xin vui lòng chọn khách hàng cần xuất Excel'),
                );
                echo json_encode($response);
                die;
            }

            $customer_id = explode('__', $customer_search)[1];
            $customer = $this->clients_model->rowCustomer($customer_id);

            $tbInvoice = "(
                SELECT
                    tbl_invoice_items.object_id as object_id,
                    GROUP_CONCAT(distinct tbl_invoices.reference_no SEPARATOR ', ') as reference_no
                FROM tbl_invoices
                INNER JOIN tbl_invoice_items ON tbl_invoice_items.invoice_id = tbl_invoices.id
                GROUP BY tbl_invoice_items.object_id
            ) tb_invoice";

            $this->db->select("
                tbl_deliveries.id as id,
                tbl_deliveries.reference_no as reference_no,
                tbl_deliveries.date as date,
                tbl_deliveries.total_quantity as total_quantity,
                tbl_deliveries.tax_rate as tax_rate,
                tbl_deliveries.total_amount_items as total_amount_items,
                (tbl_deliveries.total_discount_percent_items + tbl_deliveries.total_discount_direct_items + tbl_deliveries.total_discount_percent + tbl_deliveries.total_discount_direct) as grand_total_discount,
                tbl_deliveries.grand_total as grand_total,
                tbl_deliveries.total_tax as total_tax,
                tb_invoice.reference_no as reference_no_invoice
            ", false)
                ->from('tbl_deliveries');
            // ->join('tbl_delivery_items', 'tbl_delivery_items.delivery_id = tbl_deliveries.id');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_deliveries.order_id');
            $this->db->join($tbInvoice, 'tb_invoice.object_id = tbl_deliveries.id', 'left');
            $this->db->where('tbl_deliveries.customer_id', $customer_id);
            if (!empty($start_date_search)) {
                $this->db->where('DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") >=', to_sql_date($start_date_search));
            }
            if (!empty($end_date_search)) {
                $this->db->where('DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") <=', to_sql_date($end_date_search));
            }
            $this->db->where_not_in('tbl_orders.type_orders', [2, 4, 11]);
            // $this->db->order_by('tbl_deliveries.id asc');
            $deliveries = $this->db->get()->result_array();

            // if (empty($order)) {
            //     $response =  array(
            //         'result' => 0,
            //         'message' => lang('Không có dự liệu xuất excel'),
            //     );
            //     echo json_encode($response); die;
            // }

            //customers

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s", 0) : '');

            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $phonenumber = get_option('invoice_company_phonenumber');
            $email = get_option('email_company');
            $website = get_option('company_website');
            $bank_name = get_option('bank_name');
            $company_vat = get_option('company_vat');

            $objPHPExcel->getActiveSheet()->setCellValue('A1', $company);
            $objPHPExcel->getActiveSheet()->setCellValue('A2', 'MST: ' . $company_vat);
            $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Địa chỉ: ' . $address);

            $objPHPExcel->getActiveSheet()->setCellValue(
                'E1',
                'BẢNG CHI TIẾT MUA HÀNG TRONG KỲ'
            )->getStyle("E1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 16,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('E1:I1');
            $objPHPExcel->getActiveSheet()->setCellValue('E2', $strDate)->getStyle("E2")->applyFromArray([
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('E2:I2');

            $codeCustomer = $customer['zcode'];
            $company = $customer['company'];

            $debt_clients = debt_clients_date($customer_id, $start_date_search, $end_date_search);

            $objPHPExcel->getActiveSheet()->setCellValue('F3', 'MÃ KH')->getStyle("F3")->applyFromArray([
                'font' => array(
                    'bold' => true,
                )
            ]);

            $objPHPExcel->getActiveSheet()->mergeCells('F3:F3');
            $objPHPExcel->getActiveSheet()->setCellValue('F4', $codeCustomer);
            $objPHPExcel->getActiveSheet()->mergeCells('F4:F4');

            $objPHPExcel->getActiveSheet()->setCellValue('G3', 'TÊN KH')->getStyle("G3")->applyFromArray([
                'font' => array(
                    'bold' => true,
                )
            ]);;
            $objPHPExcel->getActiveSheet()->mergeCells('G3:I3');
            $objPHPExcel->getActiveSheet()->setCellValue('G4', $company);
            $objPHPExcel->getActiveSheet()->mergeCells('G4:I4');
            $objPHPExcel->getActiveSheet()->getStyle("F3:I4")->applyFromArray([
                'font' => array(
                    'bold' => true,
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            ]);


            $objPHPExcel->getActiveSheet()->setCellValue('E6', 'NỢ ĐẦU KỲ');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'H6',
                $debt_clients['begin']
            )->getStyle("H6")->getNumberFormat()->setFormatCode(formatMoneyExcel($debt_clients['begin']));

            $objPHPExcel->getActiveSheet()->setCellValue('E7', 'MUA TRONG KỲ');
            $objPHPExcel->getActiveSheet()->mergeCells('E7:E9');

            $objPHPExcel->getActiveSheet()->setCellValue('E10', 'THANH TOÁN TRONG KỲ');
            $objPHPExcel->getActiveSheet()->mergeCells('E10:E11');

            $tienHang = $debt_clients['total_import'] - $debt_clients['total_tax'];
            $objPHPExcel->getActiveSheet()->setCellValue('F7', 'TIỀN HÀNG');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'H7',
                $tienHang
            )->getStyle("H7")->getNumberFormat()->setFormatCode(formatMoneyExcel($tienHang));
            $objPHPExcel->getActiveSheet()->mergeCells('F7:G7');

            $objPHPExcel->getActiveSheet()->setCellValue('F8', 'THUẾ GTGT');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'H8',
                $debt_clients['total_tax']
            )->getStyle("H8")->getNumberFormat()->setFormatCode(formatMoneyExcel($debt_clients['total_tax']));
            $objPHPExcel->getActiveSheet()->mergeCells('F8:G8');

            $objPHPExcel->getActiveSheet()->setCellValue('F9', 'KHOẢNG GIẢM TRỪ');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'H9',
                $debt_clients['returns']
            )->getStyle("H9")->getNumberFormat()->setFormatCode(formatMoneyExcel($debt_clients['returns']));
            $objPHPExcel->getActiveSheet()->mergeCells('F9:G9');

            $objPHPExcel->getActiveSheet()->setCellValue('F10', 'T.MẶT');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'H10',
                $debt_clients['total_payment_import']
            )->getStyle("H10")->getNumberFormat()->setFormatCode(formatMoneyExcel($debt_clients['total_payment_import']));
            $objPHPExcel->getActiveSheet()->mergeCells('F10:G10');

            $objPHPExcel->getActiveSheet()->setCellValue('F11', 'C.KHOẢN');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'H10',
                $debt_clients['total_payment_import_bank']
            )->getStyle("H10")->getNumberFormat()->setFormatCode(formatMoneyExcel($debt_clients['total_payment_import_bank']));
            $objPHPExcel->getActiveSheet()->mergeCells('F11:G11');

            $objPHPExcel->getActiveSheet()->setCellValue('E12', 'NỢ CUỐI KỲ');
            $noCuoiKy = $debt_clients['begin'] + $debt_clients['total_import'] - $debt_clients['returns'] - $debt_clients['total_payment_import'] - $debt_clients['total_payment_import_bank'];
            $objPHPExcel->getActiveSheet()->setCellValue(
                'H12',
                $noCuoiKy
            )->getStyle("H12")->getNumberFormat()->setFormatCode(formatMoneyExcel($noCuoiKy));

            $objPHPExcel->getActiveSheet()->getStyle("E6:H12")->applyFromArray([
                'font' => array(
                    'bold' => true,
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            ]);


            $objPHPExcel->getActiveSheet()->setCellValue('A14', 'STT');
            $objPHPExcel->getActiveSheet()->mergeCells('A14:A15');

            $objPHPExcel->getActiveSheet()->setCellValue('B14', 'SỐ PHIẾU');
            $objPHPExcel->getActiveSheet()->mergeCells('B14:B15');

            $objPHPExcel->getActiveSheet()->setCellValue('C14', 'SỐ HÓA ĐƠN');
            $objPHPExcel->getActiveSheet()->mergeCells('C14:C15');

            $objPHPExcel->getActiveSheet()->setCellValue('D14', 'NGÀY');
            $objPHPExcel->getActiveSheet()->mergeCells('D14:D15');

            $objPHPExcel->getActiveSheet()->setCellValue('E14', 'LOẠI HÀNG');
            $objPHPExcel->getActiveSheet()->mergeCells('E14:E15');

            $objPHPExcel->getActiveSheet()->setCellValue('F14', 'TÊN HÀNG');
            $objPHPExcel->getActiveSheet()->mergeCells('F14:F15');

            $objPHPExcel->getActiveSheet()->setCellValue('G14', 'Q.CÁCH');
            $objPHPExcel->getActiveSheet()->mergeCells('G14:G15');

            $objPHPExcel->getActiveSheet()->setCellValue('H14', 'ĐVT');
            $objPHPExcel->getActiveSheet()->mergeCells('H14:H15');

            $objPHPExcel->getActiveSheet()->setCellValue('I14', 'MUA HÀNG');
            $objPHPExcel->getActiveSheet()->mergeCells('I14:N14');
            $objPHPExcel->getActiveSheet()->setCellValue('I15', 'SL');
            $objPHPExcel->getActiveSheet()->setCellValue('J15', 'ĐG');
            $objPHPExcel->getActiveSheet()->setCellValue('K15', 'TIỀN HÀNG');
            $objPHPExcel->getActiveSheet()->setCellValue('L15', 'CK');
            $objPHPExcel->getActiveSheet()->setCellValue('M15', 'THUẾ');
            $objPHPExcel->getActiveSheet()->setCellValue('N15', 'TỔNG TIỀN');

            $objPHPExcel->getActiveSheet()->getStyle("A14:N15")->applyFromArray([
                'font' => array(
                    'bold' => true,
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
            ]);

            $this->load->model('products_model');
            $this->load->model('unit_model');
            $this->load->model('items_model');

            $sumTotalQuantity = 0;
            $sumTotalAmountItems = 0;
            $sumTotalTax = 0;
            $sumTotalDiscount = 0;
            $sumGrandTotal = 0;
            $rowBegin = 15;
            if (!empty($deliveries)) {
                $iSTT = 0;
                foreach ($deliveries as $key => $value) {
                    $iSTT++;
                    $rowBegin++;
                    $delivery_id = $value['id'];
                    $reference_no = $value['reference_no'];
                    $date = _d($value['date']);
                    $total_quantity = $value['total_quantity'];
                    $total_amount_items = $value['total_amount_items'];
                    $total_tax = $value['total_tax'];
                    $grand_total_discount = $value['grand_total_discount'];
                    $grand_total = $value['grand_total'];

                    $sumTotalQuantity += $total_quantity;
                    $sumTotalAmountItems += $total_amount_items;
                    $sumTotalTax += $total_tax;
                    $sumTotalDiscount += $grand_total_discount;
                    $sumGrandTotal += $grand_total;

                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $iSTT);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $reference_no);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", $value['reference_no_invoice']);
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $date);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:N$rowBegin")->applyFromArray([
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => '14b8e9'),
                            'bold' => true
                        ),
                    ]);

                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "I$rowBegin",
                        $total_quantity
                    )->getStyle("I$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($total_quantity));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "K$rowBegin",
                        $total_amount_items
                    )->getStyle("K$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($total_amount_items));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "L$rowBegin",
                        $grand_total_discount
                    )->getStyle("L$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($grand_total_discount));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "M$rowBegin",
                        $total_tax
                    )->getStyle("M$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($total_tax));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "N$rowBegin",
                        $grand_total
                    )->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($grand_total));

                    $this->db->select('
                        tbl_delivery_items.id as id,
                        tbl_delivery_items.type_item as type_item,
                        tbl_delivery_items.item_id as item_id,
                        tbl_delivery_items.quantity as quantity,
                        tbl_delivery_items.price as price,
                        tbl_delivery_items.amount as amount,
                        tbl_delivery_items.discount_percent_item as discount_percent_item,
                        tbl_delivery_items.discount_percent_amount_item as discount_percent_amount_item,
                        tbl_delivery_items.discount_direct_amount_item as discount_direct_amount_item,
                        (tbl_delivery_items.discount_percent_amount_item + tbl_delivery_items.discount_direct_amount_item) as total_discount_item,
                        tbl_delivery_items.total_amount as total_amount,
                    ');
                    $this->db->from('tbl_delivery_items');
                    $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
                    $this->db->where('tbl_delivery_items.delivery_id', $delivery_id);
                    $deliveryItems = $this->db->get()->result_array();
                    if (!empty($deliveryItems)) {
                        foreach ($deliveryItems as $k => $v) {
                            $rowBegin++;
                            $type_item = $v['type_item'];
                            $item_id = $v['item_id'];
                            $info = null;
                            $mode = '';
                            $name = '';
                            $txtType = '';
                            if ($type_item == "products") {
                                $info = $this->products_model->rowProduct($item_id);
                                $unit = $this->unit_model->rowUnit($info['unit_id']);
                                $mode = $info['mode'];
                                $name = $info['name'];
                                $txtType = 'Thành phẩm';
                            } elseif ($type_item == "items") {
                                $info = $this->items_model->rowItems($item_id);
                                $unit = $this->unit_model->rowUnit($info['unit']);
                                $name = $info['name'];
                                $txtType = 'Hàng hóa';
                            } elseif ($type_item == "materials") {
                                $info = $this->items_model->rowMaterial($item_id);
                                $unit = $this->unit_model->rowUnit($info['unit_id']);
                                $name = $info['name'];
                                $txtType = 'Nguyên vật liệu';
                            }

                            $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $txtType);
                            $objPHPExcel->getActiveSheet()->setCellValue(
                                "F$rowBegin",
                                $name
                            )->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $mode);
                            $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $unit['unit']);
                            $objPHPExcel->getActiveSheet()->setCellValue(
                                "I$rowBegin",
                                $v['quantity']
                            )->getStyle("I$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($v['quantity']));
                            $objPHPExcel->getActiveSheet()->setCellValue(
                                "J$rowBegin",
                                $v['price']
                            )->getStyle("J$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($v['price']));
                            $objPHPExcel->getActiveSheet()->setCellValue(
                                "K$rowBegin",
                                $v['amount']
                            )->getStyle("K$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($v['amount']));

                            $total_discount_item = $v['total_discount_item'];
                            $objPHPExcel->getActiveSheet()->setCellValue(
                                "L$rowBegin",
                                $total_discount_item
                            )->getStyle("L$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($total_discount_item));

                            $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", 0);
                            $objPHPExcel->getActiveSheet()->setCellValue(
                                "N$rowBegin",
                                $v['total_amount']
                            )->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($v['total_amount']));
                        }
                    }
                }
            }

            $rowBegin++;
            $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", 'TỔNG TIỀN TRONG KỲ');
            $objPHPExcel->getActiveSheet()->mergeCells("A$rowBegin:D$rowBegin");
            $objPHPExcel->getActiveSheet()->setCellValue(
                "I$rowBegin",
                $sumTotalQuantity
            )->getStyle("I$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($sumTotalQuantity));

            $objPHPExcel->getActiveSheet()->setCellValue(
                "K$rowBegin",
                $sumTotalAmountItems
            )->getStyle("K$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($sumTotalAmountItems));
            $objPHPExcel->getActiveSheet()->setCellValue(
                "L$rowBegin",
                $sumTotalDiscount
            )->getStyle("L$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($sumTotalDiscount));
            $objPHPExcel->getActiveSheet()->setCellValue(
                "M$rowBegin",
                $sumTotalTax
            )->getStyle("M$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($sumTotalTax));
            $objPHPExcel->getActiveSheet()->setCellValue(
                "N$rowBegin",
                $sumGrandTotal
            )->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($sumGrandTotal));
            $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:N$rowBegin")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ddd'),
                    'bold' => true
                ),
            ]);

            $rowBegin++;
            $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", 'KHOẢNG GIẢM TRỪ');
            $objPHPExcel->getActiveSheet()->mergeCells("A$rowBegin:D$rowBegin");
            $objPHPExcel->getActiveSheet()->setCellValue(
                "N$rowBegin",
                $debt_clients['returns']
            )->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($debt_clients['returns']));
            $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:N$rowBegin")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ddd'),
                    'bold' => true
                ),
            ]);

            $rowBegin++;
            $grandTotalEnd = $sumGrandTotal - $debt_clients['returns'];
            $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", 'TỔNG CỘNG');
            $objPHPExcel->getActiveSheet()->mergeCells("A$rowBegin:D$rowBegin");
            $objPHPExcel->getActiveSheet()->setCellValue(
                "N$rowBegin",
                $grandTotalEnd
            )->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($grandTotalEnd));
            $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:N$rowBegin")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ddd'),
                    'bold' => true
                ),
            ]);

            $rowBegin++;
            $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", 'DANH SÁCH THANH TOÁN TRONG KỲ');
            $objPHPExcel->getActiveSheet()->mergeCells("A$rowBegin:N$rowBegin");
            $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:N$rowBegin")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '#7788'),
                    'bold' => true
                ),
            ]);

            $grandTotalPayment = 0;
            $whereVoucherCoupon = '';
            $whereOtherPayslipsCoupon = '';
            if (!empty($start_date_search)) {
                $whereVoucherCoupon .= ' AND tblvouchers_coupon.date_vouchers >= "' . to_sql_date($start_date_search) . '"';
                $whereOtherPayslipsCoupon .= ' AND tblother_payslips_coupon.date >= "' . to_sql_date($start_date_search) . '"';
            }

            if (!empty($end_date_search)) {
                $whereVoucherCoupon .= ' AND tblvouchers_coupon.date_vouchers <= "' . to_sql_date($end_date_search) . '"';
                $whereOtherPayslipsCoupon .= ' AND tblother_payslips_coupon.date >= "' . to_sql_date($start_date_search) . '"';
            }

            $tbQueryPayment = "
                SELECT
                    tblvouchers_coupon.date_vouchers as date,
                    tblvouchers_coupon.code_vouchers as code,
                    tblvouchers_coupon.note as note,
                    tblvouchers_coupon.payment as payment
                FROM tblvouchers_coupon
                WHERE tblvouchers_coupon.customer = $customer_id $whereVoucherCoupon

                UNION ALL

                SELECT 
                    tblother_payslips_coupon.date as date,
                    CONCAT(tblother_payslips_coupon.prefix, '-', tblother_payslips_coupon.code) as code,
                    tblother_payslips_coupon.note as note,
                    tblother_payslips_coupon.total as payment
                FROM tblother_payslips_coupon 
                WHERE tblother_payslips_coupon.objects_id = $customer_id AND tblother_payslips_coupon.objects = 1 $whereOtherPayslipsCoupon

                GROUP BY date ASC
            ";
            $dtPayment = $this->db->query($tbQueryPayment)->result_array();
            if (!empty($dtPayment)) {
                $iSTT = 0;
                foreach ($dtPayment as $kPayment => $vPayment) {
                    $iSTT++;
                    $rowBegin++;

                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $iSTT);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _d($vPayment['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", $vPayment['code']);
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $vPayment['note']);
                    $objPHPExcel->getActiveSheet()->mergeCells("D$rowBegin:L$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "N$rowBegin",
                        $vPayment['payment']
                    )->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($vPayment['payment']));
                    $grandTotalPayment += $vPayment['payment'];
                }
            }

            $rowBegin++;
            $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", 'TỔNG THANH TOÁN TRONG KỲ');
            $objPHPExcel->getActiveSheet()->mergeCells("A$rowBegin:D$rowBegin");
            $objPHPExcel->getActiveSheet()->setCellValue(
                "N$rowBegin",
                $grandTotalPayment
            )->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($grandTotalPayment));
            $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:N$rowBegin")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ddd'),
                    'bold' => true
                ),
                'font' => array(
                    'bold' => true,
                ),
            ]);

            $rowBegin++;
            $duNoCuoiKy = $debt_clients['begin'] + $grandTotalEnd - $grandTotalPayment;
            $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", 'DƯ NỢ CUỐI KỲ');
            $objPHPExcel->getActiveSheet()->mergeCells("A$rowBegin:D$rowBegin");
            $objPHPExcel->getActiveSheet()->setCellValue(
                "N$rowBegin",
                $duNoCuoiKy
            )->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($duNoCuoiKy));
            $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:N$rowBegin")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ddd'),
                    'bold' => true
                ),
                'font' => array(
                    'bold' => true,
                ),
            ]);

            $objPHPExcel->getActiveSheet()->getStyle("A14:N$rowBegin")->applyFromArray([
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                // 'font' => array(
                //     'bold' => true,
                // ),
            ]);

            $rowBegin++;
            $rowBegin++;
            $objPHPExcel->getActiveSheet()->setCellValue(
                "B$rowBegin",
                "Công nợ của Qúy Khách Hàng tính đến ngày $strDate là :(vnđ)"
            )->getStyle("B$rowBegin")->applyFromArray([
                'font' => array(
                    'bold' => true,
                ),
            ]);

            // $rowBegin++;
            $objPHPExcel->getActiveSheet()->setCellValue(
                "G$rowBegin",
                $duNoCuoiKy
            )->getStyle("G$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($duNoCuoiKy));
            $objPHPExcel->getActiveSheet()->getStyle("G$rowBegin")->applyFromArray([
                'font' => array(
                    'bold' => true,
                ),
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells("G$rowBegin:H$rowBegin");
            $objPHPExcel->getActiveSheet()->setCellValue(
                "J$rowBegin",
                '(đồng)'
            )->getStyle("J$rowBegin")->applyFromArray([
                'font' => array(
                    'bold' => true,
                ),
            ]);

            $rowBegin++;
            $rowBegin++;
            $objPHPExcel->getActiveSheet()->setCellValue(
                "A$rowBegin",
                'XÁC NHẬN KHÁCH HÀNG'
            )->getStyle("A$rowBegin")->applyFromArray([
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells("A$rowBegin:C$rowBegin");

            $d = date('d');
            $m = date('m');
            $y = date('m');
            $objPHPExcel->getActiveSheet()->setCellValue(
                "I$rowBegin",
                "Tp.HCM, ngày $d tháng $m năm $y"
            )->getStyle("I$rowBegin")->applyFromArray([
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells("I$rowBegin:K$rowBegin");

            $rowBegin++;
            $objPHPExcel->getActiveSheet()->setCellValue(
                "I$rowBegin",
                "GIÁM ĐỐC"
            )->getStyle("I$rowBegin")->applyFromArray([
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells("I$rowBegin:K$rowBegin");

            $filename = lang('tnh_sale_listing') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);

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

    public function view_sale_listing()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $customers = $this->input->post('customers');

        $data['start_date_search'] = $start_date;
        $data['end_date_search'] = $end_date;
        $data['customers'] = $customers;
        $this->load->view('admin/reports/sales/view_sale_listing', $data);
    }

    public function checkQuality()
    {
        $data = [];
        $data['title'] = lang('Báo cáo QC thành phẩm');
        $this->load->view('admin/reports/check_quality', $data);
    }

    public function getOrderCheckQuality()
    {
        $beginMonth = to_sql_date($this->input->post('report-from'));
        $endMonth = to_sql_date($this->input->post('report-to'));

        $reason = "(
            SELECT tbl_check_quality_items_error.id
            FROM tbl_check_quality_items_error
            INNER JOIN tbl_check_quality_items ON tbl_check_quality_items.id = tbl_check_quality_items_error.id_check_quality_item
            WHERE tbl_check_quality_items.order_id = tbl_orders.id
        )";

        $this->datatables->select("
            tbl_check_quality.id as id,
            tbl_productions_orders.reference_no as reference_no_pod,
            tbl_orders.reference_no as reference_no,
            GROUP_CONCAT(tbl_check_quality_items.id ) as id_check_quality_item,
            1 as quantity,
            'orders' as type_hide
            ,
        ", false)
            ->from('tbl_check_quality')
            ->join('tbl_check_quality_items', 'tbl_check_quality_items.check_quality_id = tbl_check_quality.id', 'left')
            ->join('tbl_orders', 'tbl_orders.id = tbl_check_quality_items.order_id AND object_type = "orders"', 'inner')
            ->join(
                'tbl_productions_orders_details',
                'tbl_productions_orders_details.id = tbl_check_quality_items.pod_id',
                'left'
            )
            ->join(
                'tbl_productions_orders',
                'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id',
                'left'
            );

        if (!empty($beginMonth) && !empty($endMonth)) {
            $this->datatables->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->datatables->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->datatables->where('tbl_check_quality_items.quantity_recycling > 0');
        $this->datatables->where("EXISTS $reason");
        $this->datatables->group_by('tbl_check_quality_items.order_id,tbl_productions_orders.id');
        $this->db->order_by('tbl_check_quality_items.order_id ASC,tbl_productions_orders.id ASC');
        // print_arrays($this->db->get_compiled_select('tbl_check_quality'), FALSE);

        $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        $index = 0;
        foreach ($data->aaData as $key => $value) {
            $check_quality_id = $value[0];
            $check_quality_item_id = $value[3];


            $data->aaData[$index][0] = ++$iDisplayStart;
            $data->aaData[$index][1] = $value[1];
            $data->aaData[$index][2] = $value[2];
            $data->aaData[$index][3] = '';
            $data->aaData[$index][4] = '';
            $data->aaData[$index][5] = 'orders';

            $this->db->select('tbl_detail_errors.name as name_reason,,SUM(tbl_check_quality_items_error.quantity) as quantity_reason');
            $this->db->from('tbl_check_quality_items_error');
            $this->db->join(
                'tbl_detail_errors',
                'tbl_detail_errors.id = tbl_check_quality_items_error.id_error',
                'left'
            );
            $this->db->where('tbl_check_quality_items_error.id_check_quality_item IN (' . $check_quality_item_id . ')');
            $this->db->group_by('tbl_check_quality_items_error.id_error');
            $this->db->having('quantity_reason > 0');
            $reasons = $this->db->get()->result_array();
            foreach ($reasons as $k => $val) {
                $index++;

                $data->aaData[$index][0] = '';
                $data->aaData[$index][1] = '';
                $data->aaData[$index][2] = '';
                $data->aaData[$index][3] = $val['name_reason'];
                $data->aaData[$index][4] = $val['quantity_reason'];
                $data->aaData[$index][5] = 'reason';
            }
            $index++;
        }
        $data->aaData = array_values($data->aaData);
        // print_arrays( $data->aaData);
        echo json_encode($data);
    }

    public function getProductionOrderQc()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $beginMonth = ($this->input->post('report-from'));
        $endMonth = ($this->input->post('report-to'));

        $whereQc = '';
        if (!empty($beginMonth)) {
            $whereQc .= 'AND DATE_FORMAT(tbl_check_quality.date, "%Y-%m-%d") >= "' . to_sql_date($beginMonth) . '"';
        }

        if (!empty($endMonth)) {
            $whereQc .= 'AND DATE_FORMAT(tbl_check_quality.date, "%Y-%m-%d") <= "' . to_sql_date($endMonth) . '"';
        }

        $tb_tamp = "(
            SELECT
                tbl_productions_orders_details.productions_orders_id,
                SUM(tbl_check_quality_items.quantity_qc) as quantity_qc,
                SUM(tbl_check_quality_items.quantity_success) as quantity_success,
                SUM(tbl_check_quality_items.quantity_recycling + tbl_check_quality_items.quantity_waste) as quantity_error
            FROM tbl_check_quality_items
            JOIN tbl_check_quality ON tbl_check_quality.id = tbl_check_quality_items.check_quality_id
            JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_check_quality_items.pod_id
            WHERE tbl_check_quality.id != 0 $whereQc
            GROUP BY tbl_productions_orders_details.productions_orders_id
        ) tb_tamp";

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.reference_no as reference_no',
            'COALESCE(tb_tamp.quantity_qc,0) as quantity_qc',
            'COALESCE(tb_tamp.quantity_error,0) as quantity_error',
            'COALESCE(tb_tamp.quantity_success,0) as quantity_success',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_productions_orders';
        $where = [];
        $filter = [];

        $join = [
            "INNER JOIN $tb_tamp ON tb_tamp.productions_orders_id = tbl_productions_orders.id"
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $row = [];
            $row[0] = '<div class="text-center">' . (++$key) . '</div>';
            $row[1] = $aRow['reference_no'];
            $row[2] = '<div class="text-center">' . formatNumber($aRow['quantity_qc']) . '</div>';
            $row[3] = '<div class="text-center">' . (!empty($aRow['quantity_error']) ? formatNumber($aRow['quantity_error']) : '') . '</div>';
            $row[4] = '<div class="text-center">' . formatNumber($aRow['quantity_success']) . '</div>';
            $row[5] = '<div class="text-center">' . (!empty(($aRow['quantity_error'] * 100) / $aRow['quantity_qc']) ? formatNumber(($aRow['quantity_error'] * 100) / $aRow['quantity_qc']) : '') . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function get_reason()
    {

        $data = $this->input->post();
        $beginMonth = to_sql_date($data['report_from']);
        $endMonth = to_sql_date($data['report_to']);
        $total_quanlity = 0;
        $reasons = [];

        $this->db->select('tbl_detail_errors.name as name_reason,SUM(tbl_check_quality_items_error.quantity) as quantity_reason,tbl_check_quality.*');
        $this->db->from('tbl_check_quality');
        $this->db->join(
            'tbl_check_quality_items',
            'tbl_check_quality_items.check_quality_id = tbl_check_quality.id',
            'left'
        );
        $this->db->join(
            'tbl_check_quality_items_error',
            'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'left'
        );
        $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items_error.id_error', 'left');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->group_by('tbl_check_quality_items_error.id_error');
        $this->db->order_by('quantity_reason DESC');
        $this->db->having('quantity_reason > 0');
        $reasons = $this->db->get()->result_array();

        if (!empty($reasons)) {
            foreach ($reasons as $key => $value) {
                $total_quanlity += $value['quantity_reason'];
            }

            foreach ($reasons as $key => $value) {
                $reasons[$key]['tyle'] = formatNumber(($value['quantity_reason'] * 100) / $total_quanlity);
            }
        }

        echo json_encode($reasons);
    }

    public function dashboard_report_pie_dt()
    {
        $data = $this->input->post();
        $beginMonth = to_sql_date($data['report_from']);
        $endMonth = to_sql_date($data['report_to']);
        $total_quanlity = 0;
        $reasons = [];

        $this->db->select('tbl_detail_errors.name as name_reason,SUM(tbl_check_quality_items_error.quantity) as quantity_reason,tbl_check_quality.*');
        $this->db->from('tbl_check_quality');
        $this->db->join(
            'tbl_check_quality_items',
            'tbl_check_quality_items.check_quality_id = tbl_check_quality.id',
            'left'
        );
        $this->db->join(
            'tbl_check_quality_items_error',
            'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'left'
        );
        $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items_error.id_error', 'left');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->group_by('tbl_check_quality_items_error.id_error');
        $this->db->order_by('quantity_reason DESC');
        $this->db->having('quantity_reason > 0');
        $reasons = $this->db->get()->result_array();
        if (!empty($reasons)) {
            foreach ($reasons as $key => $value) {
                $total_quanlity += $value['quantity_reason'];
            }

            foreach ($reasons as $key => $value) {
                $reasons[$key]['tyle'] = formatNumber(($value['quantity_reason'] * 100) / $total_quanlity);
            }
        }


        $_order = array();
        $labels = array();
        $colors = array();
        $datas = array();
        foreach ($reasons as $key => $value) {
            $labels[] = $value['name_reason'];
            $_order[] = $value['quantity_reason'];
            $colors[] = '#' . rand_color();
            $datas[] = [
                'label' => $value['name_reason'],
                'data' => [$value['quantity_reason']],
                'backgroundColor' => '#' . rand_color(),
                'borderColor' => '#' . rand_color(),
            ];
        }
        $__data['color'] = $colors;
        $__data['data'] = $_order;
        $__data['labels'] = $labels;
        $__data['datas'] = $datas;
        echo json_encode($__data);
        die;
    }

    public function get_product()
    {

        $data = $this->input->post();
        $beginMonth = to_sql_date($data['report_from']);
        $endMonth = to_sql_date($data['report_to']);
        $total_quanlity = 0;
        $products = [];

        $this->db->select('SUM(tbl_check_quality_items_error.quantity) as quantity_reason,tbl_products.name');
        $this->db->from('tbl_check_quality');
        $this->db->join(
            'tbl_check_quality_items',
            'tbl_check_quality_items.check_quality_id = tbl_check_quality.id',
            'left'
        );
        $this->db->join(
            'tbl_check_quality_items_error',
            'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'left'
        );
        $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items_error.id_error', 'left');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_check_quality_items.item_id', 'left');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->group_by('tbl_check_quality_items.item_id');
        $this->db->order_by('quantity_reason DESC');
        $this->db->having('quantity_reason > 0');
        $this->db->limit(10);
        $products = $this->db->get()->result_array();

        if (!empty($products)) {
            foreach ($products as $key => $value) {
                $total_quanlity += $value['quantity_reason'];
            }

            foreach ($products as $key => $value) {
                $products[$key]['tyle'] = formatNumber(($value['quantity_reason'] * 100) / $total_quanlity);
            }
        }


        echo json_encode($products);
    }


    function dashboard_report_pie_gt()
    {
        $data = $this->input->post();
        $beginMonth = to_sql_date($data['report_from']);
        $endMonth = to_sql_date($data['report_to']);
        $total_quanlity = 0;
        $products = [];

        $this->db->select('SUM(tbl_check_quality_items_error.quantity) as quantity_reason,tbl_products.name');
        $this->db->from('tbl_check_quality');
        $this->db->join(
            'tbl_check_quality_items',
            'tbl_check_quality_items.check_quality_id = tbl_check_quality.id',
            'left'
        );
        $this->db->join(
            'tbl_check_quality_items_error',
            'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'left'
        );
        $this->db->join('tbl_detail_errors', 'tbl_detail_errors.id = tbl_check_quality_items_error.id_error', 'left');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_check_quality_items.item_id', 'left');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_check_quality.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_check_quality.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->group_by('tbl_check_quality_items.item_id');
        $this->db->order_by('quantity_reason DESC');
        $this->db->having('quantity_reason > 0');
        $this->db->limit(10);
        $products = $this->db->get()->result_array();

        $_order = array();
        $labels = array();
        $colors = array();
        foreach ($products as $key => $value) {
            $labels[] = $value['name'];
            $_order[] = $value['quantity_reason'];
            $colors[] = '#' . rand_color();
        }
        $__data['color'] = $colors;
        $__data['data'] = $_order;
        $__data['labels'] = $labels;
        echo json_encode($__data);
        die;
    }

    public function viewclient_data($id = '', $client_id = '')
    {
        $data['id'] = $id;
        $data['client_id'] = $client_id;
        $this->load->view('admin/reports/customer/viewclient', $data);
    }

    public function table_viewclient($id = '', $client_id = '')
    {
        $data['id'] = $id;
        $data['client_id'] = $client_id;
        $this->app->get_table_data('table_viewclient', $data);
    }

    public function viewinventorywarehouse_data($id = '', $id_items = '', $type = '')
    {
        $data['id'] = $id;
        $data['id_items'] = $id_items;
        $data['type'] = $type;
        $this->load->view('admin/reports/warehouse/viewinventorywarehouse', $data);
    }

    public function table_inventory_warehouses($id = '', $id_items = '', $type = '')
    {
        $data['id'] = $id;
        $data['id_items'] = $id_items;
        $data['type'] = $type;
        $this->app->get_table_data('table_inventory_warehouses', $data);
    }

    public function top_suppplier()
    {
        $data = $this->input->post();
        if (!empty($data['report_from']) && !empty($data['report_from'])) {
            $begin = to_sql_date($data['report_from']);
            $end = to_sql_date($data['report_to']);
            if ((strtotime($end) - strtotime($begin)) < 0) {
                $data = '';
                echo json_encode($data);
                die;
            }
        }
        $beginMonth = '';
        $endMonth = '';
        $months_report = $data['months_report'];
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'day') {
                $beginMonth = date('Y-m-d');
                $endMonth = date('Y-m-d');
            } elseif ($months_report == 'week') {
                $beginMonth = date('Y-m-d', strtotime('this week', time()));
                $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
                $endMonth = strftime("%Y-%m-%d", $week);
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }
        $this->db->select('suppliers_id,SUM(total) as grand_totals,tblsuppliers.company as customer_name');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tblimport.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tblimport.date <=', $endMonth . ' 23:59:59');
        }
        $this->db->having('grand_totals > 0');
        if (!empty($data['customers_ch'])) {
            $this->db->where('tblimport.suppliers_id ', $data['customers_ch']);
        }
        if (!empty($data['search_id_staff'])) {
            $this->db->where_in('tblimport.staff_create ', $data['search_id_staff']);
        }
        $this->db->limit(5);
        $this->db->order_by('grand_totals', 'DESC');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tblimport.suppliers_id', 'left');
        $this->db->group_by('suppliers_id');
        $datas = $this->db->get('tblimport')->result_array();
        $html = '';
        foreach ($datas as $key => $value) {
            $html .= '<div class="wrap_container">
                            <span style="float:left; width:70%; height: 28px; overflow: hidden;"><span class="wrap_number">' . ($key + 1) . '.</span> ' . $value['customer_name'] . '</span>
                            ' . (strlen($value['customer_name']) > 80 ? ' ...' : "") . '
                            <span style="color: #2e98ff; float: right; width: 30%; font-weight: 500; font-size: 15px; text-align: right;">' . number_format($value['grand_totals']) . '</span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="wrap_line"></div>';
        }
        echo json_encode($html);
    }

    public function dashboard_report($value = '')
    {
        // $data = $this->input->post();

        // $data['customers_ch'] = trim($data['customers_ch'], 'customers__');
        // if (!empty($data['report_from']) && !empty($data['report_from'])) {
        //     $beginMonth = to_sql_date($data['report_from']);
        //     $endMonth = to_sql_date($data['report_to']);
        //     if ((strtotime($endMonth) - strtotime($beginMonth)) < 0) {
        //         $_data['labels'] = '';
        //         $_data['data'] = '';
        //         $_data['datas_payment'] = '';
        //         $_data['datas_cost'] = '';
        //         echo json_encode($_data);
        //         die;
        //     }
        // }

        // $where_or = '';
        // if (!empty($data['search_id_staff'])) {
        //     foreach ($data['search_id_staff'] as $key => $v) {
        //         $where_or = '(tbl_orders.employee_id = ' . $v . ') or ' . $where_or;
        //     }
        // }

        // if ($data['months_report'] == 'this_year') {
        //     $labels[1] = 'Tháng 1';
        //     $labels[2] = 'Tháng 2';
        //     $labels[3] = 'Tháng 3';
        //     $labels[4] = 'Tháng 4';
        //     $labels[5] = 'Tháng 5';
        //     $labels[6] = 'Tháng 6';
        //     $labels[7] = 'Tháng 7';
        //     $labels[8] = 'Tháng 8';
        //     $labels[9] = 'Tháng 9';
        //     $labels[10] = 'Tháng 10';
        //     $labels[11] = 'Tháng 11';
        //     $labels[12] = 'Tháng 12';

        //     foreach ($labels as $key => $value) {
        //         $whereJoin = array();
        //         $whereJoin['where'] = array(
        //             'month(tbl_deliveries.date) = ' => $key,
        //             'year(tbl_deliveries.date) =' => date('Y'),
        //         );
        //         $whereJoin['where'][] = array('warehouseman_id >' => 0);
        //         if (!empty($data['customers_ch'])) {
        //             $whereJoin['where'][] = array('customer_id = ' => $data['customers_ch']);
        //         }
        //         if (!empty($data['search_id_staff'])) {
        //             $whereJoin['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
        //         }
        //         $whereJoin['join'] = array();
        //         $whereJoin['field'] = 'grand_total';
        //         $sum = (sum_from_table_join('tbl_deliveries', $whereJoin));


        //         $_cost = 0;
        //         $whereJoin_cost = array();
        //         $whereJoin_cost['where'] = array(
        //             'month(tbl_orders.date) = ' => $key,
        //             'year(tbl_orders.date) =' => date('Y'),
        //         );
        //         if (!empty($data['search_id_staff'])) {
        //             $whereJoin_cost['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
        //         }
        //         if (!empty($data['customers_ch'])) {
        //             $whereJoin_cost['where'][] = array('customer_id = ' => $data['customers_ch']);
        //         }
        //         $whereJoin_cost['join'] = array();
        //         $whereJoin_cost['field'] = 'total_cost_temporary_capital';
        //         $_cost = sum_from_table_join('tbl_orders', $whereJoin_cost);
        //         // var_dump($this->db->last_query());die;

        //         $whereJoin_costc = array();
        //         $whereJoin_costc['where'] = array(
        //             'month(tbl_orders.date) = ' => $key,
        //             'year(tbl_orders.date) =' => date('Y'),
        //         );
        //         if (!empty($data['search_id_staff'])) {
        //             $whereJoin_costc['where_or'] = '( ' . trim($where_or, 'or ') . ' )';
        //         }
        //         if (!empty($data['customers_ch'])) {
        //             $whereJoin_costc['where'][] = array('customer_id = ' => $data['customers_ch']);
        //         }
        //         $whereJoin_costc['join'] = array();
        //         $whereJoin_costc['field'] = 'total_cost';
        //         $_costc = (sum_from_table_join('tbl_orders', $whereJoin_costc));

        //         if (empty($sum)) {
        //             $sum = 0;
        //         }
        //         $datas_payment[$key] = $sum;
        //         if (empty($_costc)) {
        //             $_costc = $_cost;
        //         }
        //         if (empty($_costc)) {
        //             $_costc = 0;
        //         }
        //         $datas_cost[$key] = $_costc;
        //         $datas[$key] = $sum - $datas_cost[$key];
        //     }
        // }

        $data = $this->input->post();
        $beginMonth = '';
        $endMonth = '';
        $months_report = $data['months_report'];
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'day') {
                $beginMonth = date('Y-m-d');
                $endMonth = date('Y-m-d');
            } elseif ($months_report == 'week') {
                $beginMonth = date('Y-m-d', strtotime('this week', time()));
                $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
                $endMonth = strftime("%Y-%m-%d", $week);
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }

        $data['customers_ch'] = trim($data['customers_ch'], 'customers__');

        $labels = [];
        $datas = [];
        $datas_payment = [];
        $datas_cost = [];

        // $labels[1] = 'Tháng 1';
        // $labels[2] = 'Tháng 2';
        // $labels[3] = 'Tháng 3';
        // $labels[4] = 'Tháng 4';
        // $labels[5] = 'Tháng 5';
        // $labels[6] = 'Tháng 6';
        // $labels[7] = 'Tháng 7';
        // $labels[8] = 'Tháng 8';
        // $labels[9] = 'Tháng 9';
        // $labels[10] = 'Tháng 10';
        // $labels[11] = 'Tháng 11';
        // $labels[12] = 'Tháng 12';

        if (empty($beginMonth) || empty($endMonth)) {
            $_data['labels'] = [];
            $_data['data'] = [];
            $_data['datas_payment'] = [];
            $_data['datas_cost'] = [];
            echo json_encode($_data);
            die;
        }

        if (!empty($beginMonth)) $date_start_before = $date_start = $beginMonth . ' 00:00:00';
        if (!empty($endMonth)) $date_end = $endMonth . ' 23:59:59';

        $date_start = date("Y-m-01", strtotime("$date_start"));
        $Dateone = new DateTime($date_start);
        $Datetwo = new DateTime($date_end);
        $year = $Datetwo->diff($Dateone);
        $MONTH = (int)(($year->y * 12) + ($year->m));
        $titleDateStart = date("m/Y", strtotime("$date_start"));
        $date_start_last = date("Y-m-01", strtotime("$date_start +1 month"));
        $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
        $dateFind[] = [
            'date_start' => date("Y-m-01", strtotime("$date_start")),
            'date_end' => $date_start_last,
        ];
        $dataDay[] = days_in_month(date("m", strtotime("$date_start")), date("Y", strtotime("$date_start")));
        $lables[] = $titleDateStart;
        for ($i = 0; $i < $MONTH; $i++) {
            $date_start = date("Y-m-01", strtotime("$date_start +1 month"));
            $date_start_last = date("Y-m-01", strtotime("$date_start +1 month"));
            $date_start_last = date("Y-m-d", strtotime("$date_start_last -1 day"));
            $lables[] = date("m/Y", strtotime("$date_start"));
            $dateFind[] = [
                'date_start' => $date_start,
                'date_end' => $date_start_last,
            ];
            $dataDay[] = days_in_month(date("m", strtotime("$date_start")), date("Y", strtotime("$date_start")));
        }

        $this->db->select('
            DATE_FORMAT(tbl_orders.date, "%m/%Y") as month,
            SUM(tbl_orders.grand_total * tbl_orders.amount_to_vnd) as grand_total_vnd
        ', false);
        $this->db->from('tbl_orders');
        $this->db->where('(tbl_orders.is_cancel = 0 OR tbl_orders.is_end = 1)');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tbl_orders.date >=', $beginMonth . ' 00:00:00');
            $this->db->where('tbl_orders.date <=', $endMonth . ' 23:59:59');
        }

        if (!empty($data['customers_ch'])) {
            $this->db->where('tbl_orders.customer_id', $data['customers_ch']);
        }

        if (!empty($data['search_id_staff'])) {
            $this->db->where_in('tbl_orders.employee_id', $data['search_id_staff']);
        }
        $this->db->group_by('DATE_FORMAT(tbl_orders.date, "%m/%Y")');
        $order = $this->db->get()->result_array();
        if (!empty($order)) {
            $order = array_reduce($order, function ($carry, $item) {
                $carry[$item['month']] = $item;
                return $carry;
            });
        }

        //
        $where = '';
        //doanh thu
        // $this->db->select('
        //     DATE_FORMAT(tbl_deliveries.date, "%m/%Y") as month,
        //     SUM(tbl_deliveries.grand_total * tbl_orders.amount_to_vnd) as grand_total_vnd
        // ', false);
        // $this->db->from('tbl_deliveries');
        // $this->db->join('tbl_orders', 'tbl_orders.id = tbl_deliveries.order_id');
        // if (!empty($beginMonth) && $endMonth) {
        //     $this->db->where('tbl_deliveries.date >=', $beginMonth . ' 00:00:00');
        //     $this->db->where('tbl_deliveries.date <=', $endMonth . ' 23:59:59');
        // }

        if (!empty($data['customers_ch'])) {
            // $this->db->where('tbl_deliveries.customer_id', $data['customers_ch']);
            $where .= ' AND tbl_deliveries.customer_id = ' . $data['customers_ch'] . '';
        }

        if (!empty($data['search_id_staff'])) {
            // $this->db->where_in('tbl_deliveries.employee_id', $data['search_id_staff']);
            $where .= ' AND tbl_deliveries.customer_id IN (' . implode($data['search_id_staff']) . ')';
        }

        // $this->db->where(' EXISTS (
        //     SELECT 1
        //     FROM tbl_invoice_items
        //     WHERE tbl_invoice_items.object_id = tbl_deliveries.id
        // )');
        // $this->db->group_by('DATE_FORMAT(tbl_deliveries.date, "%m/%Y")');
        // $delivery = $this->db->get()->result_array();

        $query_delivery = "
            SELECT DATE_FORMAT(tb_delivery.date, '%m/%Y') as month, SUM(tb_delivery.grand_total * tbl_orders.amount_to_vnd) as grand_total_vnd
            FROM (
                SELECT tbl_deliveries.grand_total, tbl_deliveries.order_id, tbl_deliveries.date
                FROM `tbl_deliveries`
                WHERE `tbl_deliveries`.`date` >= '" . $beginMonth . " 00:00:00' AND `tbl_deliveries`.`date` <= '" . $endMonth . " 23:59:59' $where AND  EXISTS (
                    SELECT 1
                    FROM tbl_invoice_items
                    WHERE tbl_invoice_items.object_id = tbl_deliveries.id
            )) tb_delivery
            JOIN `tbl_orders` ON `tbl_orders`.`id` = tb_delivery.`order_id`
            GROUP BY DATE_FORMAT(tb_delivery.date, '%m/%Y')
        ";
        $delivery = $this->db->query($query_delivery)->result_array();
        if (!empty($delivery)) {
            $delivery = array_reduce($delivery, function ($carry, $item) {
                $carry[$item['month']] = $item;
                return $carry;
            });
        }

        //doanh số mua
        $this->db->select('
            DATE_FORMAT(tblpurchase_order.date, "%m/%Y") as month,
            SUM(tblpurchase_order.total_dqd) as total_dqd,
            SUM(tblpurchase_order.price_other_expenses + tblpurchase_order.amount_paid) as total_payment,
        ', false);
        $this->db->from('tblpurchase_order');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tblpurchase_order.date >=', $beginMonth);
            $this->db->where('tblpurchase_order.date <=', $endMonth);
        }
        // $this->db->where('tblpurchase_order.cancel', 0);
        $this->db->group_by('DATE_FORMAT(tblpurchase_order.date, "%m/%Y")');
        $purchase_order = $this->db->get()->result_array();
        if (!empty($purchase_order)) {
            $purchase_order = array_reduce($purchase_order, function ($carry, $item) {
                $carry[$item['month']] = $item;
                return $carry;
            });
        }

        //Chi phí
        $this->db->select('
            DATE_FORMAT(tblother_payslips.date, "%m/%Y") as month,
            SUM(tblother_payslips.total) as total,
        ', false);
        $this->db->from('tblother_payslips');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tblother_payslips.date >=', $beginMonth);
            $this->db->where('tblother_payslips.date <=', $endMonth);
        }
        $this->db->group_by('DATE_FORMAT(tblother_payslips.date, "%m/%Y")');
        $this->db->where('tblother_payslips.id_costs !=', 0);
        $other_payslips = $this->db->get()->result_array();
        if (!empty($other_payslips)) {
            $other_payslips = array_reduce($other_payslips, function ($carry, $item) {
                $carry[$item['month']] = $item;
                return $carry;
            });
        }

        $data_doanh_so_ban = [];
        $data_doanh_thu = [];
        $data_doanh_so_mua = [];
        $data_chi_phi = [];
        $data_loi_nhuan = [];
        foreach ($lables as $key => $value) {
            $dt_order = $order[$value] ?? null;
            $dt_delivery = $delivery[$value] ?? null;
            $dt_purchase_order = $purchase_order[$value] ?? null;
            $dt_other_payslips = $other_payslips[$value] ?? null;

            $doanh_so_ban = $dt_order['grand_total_vnd'] ?? 0;
            $doanh_thu = $dt_delivery['grand_total_vnd'] ?? 0;
            $doanh_so_mua = $dt_purchase_order['total_payment'] ?? 0;
            $chi_phi = $dt_other_payslips['total'] ?? 0;
            $loi_nhuan = $doanh_thu - $doanh_so_mua - $chi_phi;

            $data_doanh_so_ban[] = $doanh_so_ban;
            $data_doanh_thu[] = $doanh_thu;
            $data_doanh_so_mua[] = $doanh_so_mua;
            $data_chi_phi[] = $chi_phi;
            $data_loi_nhuan[] = $loi_nhuan;
        }

        $_data['labels'] = $lables;
        $_data['data_doanh_so_ban'] = $data_doanh_so_ban;
        $_data['data_doanh_thu'] = $data_doanh_thu;
        $_data['data_doanh_so_mua'] = $data_doanh_so_mua;
        $_data['data_chi_phi'] = $data_chi_phi;
        $_data['data_loi_nhuan'] = $data_loi_nhuan;
        echo json_encode($_data);
    }

    public function top_chiphi()
    {
        $data = $this->input->post();
        if (!empty($data['report_from']) && !empty($data['report_from'])) {
            $begin = to_sql_date($data['report_from']);
            $end = to_sql_date($data['report_to']);
            if ((strtotime($end) - strtotime($begin)) < 0) {
                $data = '';
                echo json_encode($data);
                die;
            }
        }
        $beginMonth = '';
        $endMonth = '';
        $months_report = $data['months_report'];
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int)$months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth = date('Y-m-t');
                }
            } elseif ($months_report == 'day') {
                $beginMonth = date('Y-m-d');
                $endMonth = date('Y-m-d');
            } elseif ($months_report == 'week') {
                $beginMonth = date('Y-m-d', strtotime('this week', time()));
                $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
                $endMonth = strftime("%Y-%m-%d", $week);
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth = $to_date;
                    $endMonth = $to_date;
                } else {
                    $beginMonth = $from_date;
                    $endMonth = $to_date;
                }
            }
        }
        $data['customers_ch'] = trim($data['customers_ch'], 'customers__');

        $this->db->select('
            tblother_payslips.id_costs as id_costs,
            tblcosts.name as name,
            SUM(tblother_payslips.total) as total,
        ', false);
        $this->db->from('tblother_payslips');
        $this->db->join('tblcosts', 'tblother_payslips.id_costs = tblcosts.id');
        if (!empty($beginMonth) && $endMonth) {
            $this->db->where('tblother_payslips.date >=', $beginMonth);
            $this->db->where('tblother_payslips.date <=', $endMonth);
        }
        $this->db->where('tblother_payslips.id_costs !=', 0);
        $this->db->group_by('tblother_payslips.id_costs');
        $other_payslips = $this->db->get()->result_array();

        $html = '';
        if (!empty($other_payslips)) {
            foreach ($other_payslips as $key => $value) {
                $html .= '<div class="wrap_container">
                    <span title="' . ($value['name']) . '" style="float:left; width:70%; height: 28px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><span class="wrap_number">' . ($key + 1) . '.</span> ' . ($value['name']) . '</span>
                    <span style="color: #2e98ff;float: right; width: 30%; font-weight: 500;font-size: 15px; text-align: right; height: 28px;">' . formatMoney($value['total'], 0) . '</span>
                    <div class="clearfix"></div>
                </div>
                <div class="wrap_line"></div>';
            }
        }

        echo json_encode($html);
    }

    public function getSalesOfOrderCancel()
    {
        if (!$this->perViewSalesOfOrder) {
            accessDenied($js = true);
        }

        $customers  = $this->input->post('customers');
        $orders     = $this->input->post('orders');
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');

        // Base select only mandatory table (avoid LEFT JOINs)
        $this->datatables->select("
            tbl_orders.id                as order_id,
            tbl_orders.customer_id       as customer_id,
            tbl_orders.type_orders       as type_orders_id,
            tbl_orders.reference_no      as reference_orders,
            tbl_orders.date              as date_quotes,
            tbl_orders.note_cancel       as note_cancel,
            tbl_orders.total_quantity    as total_quantity,
            tbl_orders.amount_to_vnd     as price,
            (tbl_orders.grand_total * tbl_orders.amount_to_vnd) as amount_end,
            (IF (tbl_orders.amount_to_vnd > 1, tbl_orders.grand_total, 0)) as amount_end_usd
        ", false)->from('tbl_orders');

        // Only cancelled orders
        // $this->datatables->where('tbl_orders.is_cancel', 1);
        $this->datatables->where('(tbl_orders.is_cancel = 1 AND tbl_orders.is_end != 1)');

        // Filters
        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }
        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }
        if (!empty($customers)) {
            $parts = explode('__', $customers);
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $this->datatables->where('tbl_orders.customer_id', $parts[1]);
            }
        }
        if (!empty($orders)) {
            $this->datatables->where('tbl_orders.id', $orders);
        }

        $raw = json_decode($this->datatables->generate());
        if (empty($raw->aaData)) {
            $raw->{'title_excel'} = [handlingTitleExcel()['title']];
            echo json_encode($raw);
            return;
        }

        // Collect distinct ids for batch fetch
        $customerIds  = [];
        $typeOrderIds = [];
        $orderIds     = [];
        foreach ($raw->aaData as $r) {
            $customerIds[]  = $r[1]; // customer_id
            $typeOrderIds[] = $r[2]; // type_orders_id
            $orderIds[]     = $r[0]; // order_id
        }
        $customerIds  = array_values(array_unique(array_filter($customerIds)));
        $typeOrderIds = array_values(array_unique(array_filter($typeOrderIds)));

        // Fetch customers
        $customersMap = [];
        if ($customerIds) {
            $rows = $this->db->select('userid, company')->where_in('userid', $customerIds)->get('tblclients')->result_array();
            foreach ($rows as $c) {
                $customersMap[$c['userid']] = $c['company'];
            }
        }

        // Fetch customer groups
        $customerGroupsMap = [];
        if ($customerIds) {
            $rows = $this->db->select('tblcustomer_groups.customer_id, GROUP_CONCAT(tblcustomers_groups.name SEPARATOR ", ") as name_group')
                ->from('tblcustomers_groups')
                ->join('tblcustomer_groups', 'tblcustomer_groups.groupid = tblcustomers_groups.id')
                ->where_in('tblcustomer_groups.customer_id', $customerIds)
                ->group_by('tblcustomer_groups.customer_id')
                ->get()->result_array();
            foreach ($rows as $cg) {
                $customerGroupsMap[$cg['customer_id']] = $cg['name_group'];
            }
        }

        // Fetch type orders
        $typeOrdersMap = [];
        if ($typeOrderIds) {
            $rows = $this->db->select('id, name')->where_in('id', $typeOrderIds)->get('tbl_type_orders')->result_array();
            foreach ($rows as $t) {
                $typeOrdersMap[$t['id']] = $t['name'];
            }
        }

        //Lấy giá đơn hàng trong items
        if ($orderIds) {
            $rows = $this->db->select('order_id, price')->where_in('order_id', $orderIds)->get('tbl_order_items')->result_array();
            $orderPriceMap = [];
            foreach ($rows as $oi) {
                //Lấy ra list giá và gôm giá giống nhau trong đơn hàng
                if (!isset($orderPriceMap[$oi['order_id']])) {
                    $orderPriceMap[$oi['order_id']][] = $oi['price'];
                } else {
                    if (in_array($oi['price'], $orderPriceMap[$oi['order_id']]) === false) {
                        $orderPriceMap[$oi['order_id']][] = $oi['price'];
                    }
                }
            }
        }

        // Remap rows
        $finalData = [];
        foreach ($raw->aaData as $row) {

            $amount_to_vnd = $row[7];
            $prices = isset($orderPriceMap[$row[0]]) ? $orderPriceMap[$row[0]] : [];
            //Chuyen doi gia ve VND
            if ($amount_to_vnd > 1 && !empty($prices)) {
                foreach ($prices as $k => $v) {
                    $prices[$k] = $v * $amount_to_vnd;
                }
            }

            $order_id       = $row[0];
            $customer_id    = $row[1];
            $type_orders_id = $row[2];
            $reference_no   = $row[3];
            $date_quotes    = $row[4];
            $note_cancel    = $row[5];
            $total_quantity = $row[6];
            $prices         = $prices;
            $amount_end     = $row[8];
            $amount_end_usd = $row[9];

            $finalData[] = [
                isset($customerGroupsMap[$customer_id]) ? $customerGroupsMap[$customer_id] : '',
                isset($customersMap[$customer_id]) ? $customersMap[$customer_id] : '',
                $reference_no,
                isset($typeOrdersMap[$type_orders_id]) ? $typeOrdersMap[$type_orders_id] : '',
                $date_quotes,
                $note_cancel,
                $total_quantity,
                $prices,
                $amount_end,
                $amount_end_usd,
            ];
        }

        $raw->aaData = $finalData;
        $raw->{'title_excel'} = [handlingTitleExcel()['title']];
        echo json_encode($raw);
    }
}
