<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Report_quotes extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
    }

    // public function quotes()
    // {
    //     $data = [];
    //     $data['title'] = lang('BÁO CÁO BÁO GIÁ');
    //     $this->load->view('admin/report_quotes/quotes', $data);
    // }

    public function quotes()
    {
        $data = [];
        $data['title'] = lang('Báo cáo báo giá');
        $this->load->view('admin/report_quotes/manage', $data);
    }

    public function countQuotes() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->db->select('
            COUNT(tbl_quotes.id) as count_quotes,
            SUM(IF (tbl_orders.type_orders = 13, 1, 0)) as count_quotes_sample
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_quotes.order_id', 'left');
        if ($start_date) {
            $this->db->where('tbl_quotes.date >=', $start_date);
        }

        if ($start_date) {
            $this->db->where('tbl_quotes.date <=', $end_date);
        }

        $quotes = $this->db->get()->row_array();
        $data['count_quotes'] = !empty($quotes['count_quotes']) ? abbreviateNumber($quotes['count_quotes']) : 0;
        $data['count_quotes_sample'] = !empty($quotes['count_quotes_sample']) ? abbreviateNumber($quotes['count_quotes_sample']) : 0;

        //yêu cầu phát triển mẫu
        $this->db->select('
            COUNT(tbl_request_template.id) as count_request_template,
        ', false);
        $this->db->from('tbl_request_template');
        $this->db->where('tbl_request_template.id_quotes >', 0);
        if ($start_date) {
            $this->db->where('tbl_request_template.date >=', $start_date);
        }

        if ($start_date) {
            $this->db->where('tbl_request_template.date <=', $end_date);
        }

        $request_template = $this->db->get()->row_array();
        $data['count_request_template'] = !empty($request_template['count_request_template']) ? abbreviateNumber($request_template['count_request_template']) : 0;

        //số mẫu đạt, không đạt
        $this->db->select('
            SUM(IF (tbl_quotes.status = "cancel", 1, 0)) as count_quotes_fail,
            SUM(IF (tbl_orders.type_orders = 1, 1, 0)) as count_quotes_pass
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_quotes.order_id', 'left');
        $this->db->where(' EXISTS (
            SELECT 1
            FROM tbl_request_template
            WHERE tbl_request_template.id_quotes = tbl_quotes.id
        ) ', false, false);

        if ($start_date) {
            $this->db->where('tbl_quotes.date >=', $start_date);
        }

        if ($start_date) {
            $this->db->where('tbl_quotes.date <=', $end_date);
        }
        $quotes_sample = $this->db->get()->row_array();

        $data['count_quotes_sample_fail'] = !empty($quotes_sample['count_quotes_fail']) ? abbreviateNumber($quotes_sample['count_quotes_fail']) : 0;
        $data['count_quotes_sample_pass'] = !empty($quotes_sample['count_quotes_pass']) ? abbreviateNumber($quotes_sample['count_quotes_pass']) : 0;
        
        echo responseData($data);
    }

    public function chartDetailQuotes() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->db->select('
            tbl_quotes.customer_id as customer_id,
            tblclients.zcode as zcode,
            COUNT(tbl_quotes.id) as count_quotes,
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->join('tblclients', 'tblclients.userid = tbl_quotes.customer_id');
        $this->db->group_by('tbl_quotes.customer_id');
        $this->db->order_by('count_quotes DESC');
        $this->db->limit(20);
        if ($start_date) {
            $this->db->where('tbl_quotes.date >=', $start_date);
        }

        if ($start_date) {
            $this->db->where('tbl_quotes.date <=', $end_date);
        }
        $quotes = $this->db->get()->result_array();

        $series = [];
        $categories = [];
        if ($quotes) {
            foreach ($quotes as $key => $value) {
                $categories[] = $value['zcode'];
                $series[] = ($value['count_quotes'] ?? 0) * 1;
            }
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        echo responseData($data);
    }

    public function chartQuotesPassFail() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->db->select('
            SUM(IF (tbl_quotes.status = "cancel", 1, 0)) as count_quotes_fail,
            SUM(IF (tbl_orders.type_orders = 1, 1, 0)) as count_quotes_pass
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_quotes.order_id', 'left');
        if ($start_date) {
            $this->db->where('tbl_quotes.date >=', $start_date);
        }

        if ($start_date) {
            $this->db->where('tbl_quotes.date <=', $end_date);
        }
        $quotes = $this->db->get()->row_array();

        $seriesData = [
            [
                'name' => 'Đạt',
                'y' => ($quotes['count_quotes_pass'] ?? 0) * 1
            ],
            [
                'name' => 'Không Đạt',
                'y' => ($quotes['count_quotes_fail'] ?? 0) * 1
            ],
        ];
        $data['seriesData'] = $seriesData;

        echo responseData($data);
    }

    public function chartDetailRequestTemplate() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $this->db->select('
            tbl_request_template.client_id as client_id,
            tblclients.zcode as zcode,
            COUNT(tbl_request_template.id) as count_request_template,
        ', false);
        $this->db->from('tbl_request_template');
        $this->db->join('tblclients', 'tblclients.userid = tbl_request_template.client_id');
        $this->db->where('tbl_request_template.id_quotes >', 0);
        $this->db->group_by('tbl_request_template.client_id');
        $this->db->order_by('count_request_template DESC');
        $this->db->limit(20);
        if ($start_date) {
            $this->db->where('tbl_request_template.date >=', $start_date);
        }

        if ($start_date) {
            $this->db->where('tbl_request_template.date <=', $end_date);
        }
        $request_template = $this->db->get()->result_array();

        $series = [];
        $categories = [];
        if ($request_template) {
            foreach ($request_template as $key => $value) {
                $categories[] = $value['zcode'];
                $series[] = ($value['count_request_template'] ?? 0) * 1;
            }
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        echo responseData($data);
    }

    public function chartProductsSampleMoreTwo() {
        $data = [];

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!empty($start_date)) $start_date = to_sql_date($start_date).' 00:00:00';
        if (!empty($end_date)) $end_date = to_sql_date($end_date). ' 23:59:59';

        $tbQuotesSubOrdersSample = "(
            SELECT
                tbl_orders_sub.quote_id_chonse as quote_id_chonse,
                COUNT(tbl_orders_sub.order_id) as count_develop
            FROM tbl_orders_sub
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_orders_sub.order_id
            WHERE tbl_orders.type_orders = ".TYPE_SAMPLE_ORDER."
            GROUP BY tbl_orders_sub.quote_id_chonse
        ) tb_order_sub";

        $this->db->select('
            tbl_quote_items.item_id as item_id,
            tbl_products.code as code,
            COUNT(DISTINCT tbl_quotes.id) as count_quote
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->join('tbl_quote_items', 'tbl_quote_items.quote_id = tbl_quotes.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_quote_items.item_id');
        $this->db->join($tbQuotesSubOrdersSample, 'tb_order_sub.quote_id_chonse = tbl_quotes.id', 'inner');
        $this->db->where(' COALESCE(tb_order_sub.count_develop) > 1 ', false, false);
        if ($start_date) {
            $this->db->where('tbl_quotes.date >=', $start_date);
        }

        if ($start_date) {
            $this->db->where('tbl_quotes.date <=', $end_date);
        }
        $this->db->group_by('tbl_quote_items.item_id');
        $quotes = $this->db->get()->result_array();

        $series = [];
        $categories = [];
        if ($quotes) {
            foreach ($quotes as $key => $value) {
                $categories[] = $value['code'];
                $series[] = ($value['count_quote'] ?? 0) * 1;
            }
        }

        $data['categories'] = $categories;
        $data['series'] = $series;
        echo responseData($data);
    }

    public function loadReport()
    {
        $view = $this->input->post('view');
        $data = [];
        $this->load->view('admin/report_quotes/' . $view, $data);
    }

    public function getDetailQuotes()
    {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers = $this->input->post('customers');

        $aColumns = [
            'tbl_quotes.date as date',
            'tbl_quotes.reference_no as reference_no',
            'tblclients.zcode as zcode',
            'tbl_quotes.status as status',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_stage_quote.name as name_stage_quote',
            'tbl_quote_items.technical_explanation as technical_explanation',
            'tblunits.unit as unit',
            'CONCAT(tbl_quote_items.moq, "-", tbl_quote_items.moq_to) as moq',
            'tbl_quote_items.unit_price as unit_price',
            'tbl_quote_items.discount_precent_item as discount_precent_item',
            'tbl_quote_items.lead_time as lead_time',
            'tbl_quote_items.note_item as note_item',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_quotes';
        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_quotes.customer_id',
            'INNER JOIN tbl_quote_items ON tbl_quote_items.quote_id = tbl_quotes.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_quote_items.item_id',
            'LEFT JOIN tbl_stage_quote ON tbl_stage_quote.id = tbl_quote_items.quote_stage_id',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
        ];

        $groupBy = '';
        $where        = [
            
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_quotes.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_quotes.date <= '$end_date_search'");
        }

        if (!empty($customers)) {
            array_push($where, "AND tbl_quotes.customer_id = '$customers'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_quotes.id as id_quote'
        ], $groupBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $aColumns = handlingColumns($aColumns);
        $stt = 0;

        foreach ($rResult as $key => $aRow) {
            $stt++;

            $row = [];
            foreach ($aColumns as $k => $v) {
                if ($v == 'reference_no') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/quotes/view_quotes/'.$aRow['id_quote']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'date') {
                    $row[] = _d($aRow[$v]);
                } else if ($v == 'status') {
                    $row[] = '<div class="text-center">'.($aRow[$v] == 'approved' ? 'Đã duyệt' : ($aRow[$v] == 'un_approved' ? 'Chưa duyệt' : 'Hủy')).'</div>';
                } else {
                    $row[] = '<div class="text-center">'.$aRow[$v].'</div>';
                }
            }
            $output['aaData'][] = $row;
        }

        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getTotalQuotes() {
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $customers = $this->input->get('customers');

        if (!empty($start_date_search)) $start_date_search = to_sql_date($start_date_search).' 00:00:00';
        if (!empty($end_date_search)) $end_date_search = to_sql_date($end_date_search). ' 23:59:59';

        $this->db->select('
            COUNT(tbl_quotes.id) as count_quotes,
        ', false);
        $this->db->from('tbl_quotes');
        if ($start_date_search) {
            $this->db->where('tbl_quotes.date >=', $start_date_search);
        }

        if ($end_date_search) {
            $this->db->where('tbl_quotes.date <=', $end_date_search);
        }

        if ($customers) {
            $this->db->where('tbl_quotes.customer_id', $customers);
        }
        $quotes = $this->db->get()->row_array();

        $data['count_quotes'] = !empty($quotes['count_quotes']) ? $quotes['count_quotes'] : 0;
        echo responseData($data);
    }

    public function getTotalQuotesPass() {
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $customers = $this->input->get('customers');

        if (!empty($start_date_search)) $start_date_search = to_sql_date($start_date_search).' 00:00:00';
        if (!empty($end_date_search)) $end_date_search = to_sql_date($end_date_search). ' 23:59:59';

        $this->db->select('
            COUNT(tbl_quotes.id) as count_quotes,
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->where(' EXISTS (
            SELECT 1
            FROM tbl_orders
            WHERE tbl_orders.id = tbl_quotes.order_id AND tbl_orders.type_orders = 1
        ) ', false, false);
        if ($start_date_search) {
            $this->db->where('tbl_quotes.date >=', $start_date_search);
        }

        if ($end_date_search) {
            $this->db->where('tbl_quotes.date <=', $end_date_search);
        }

        if ($customers) {
            $this->db->where('tbl_quotes.customer_id', $customers);
        }
        $quotes = $this->db->get()->row_array();

        $data['count_quotes'] = !empty($quotes['count_quotes']) ? $quotes['count_quotes'] : 0;
        echo responseData($data);
    }

    public function getTotalQuotesFail() {
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $customers = $this->input->get('customers');

        if (!empty($start_date_search)) $start_date_search = to_sql_date($start_date_search).' 00:00:00';
        if (!empty($end_date_search)) $end_date_search = to_sql_date($end_date_search). ' 23:59:59';

        $this->db->select('
            COUNT(tbl_quotes.id) as count_quotes,
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->where('tbl_quotes.status', 'cancel');
        if ($start_date_search) {
            $this->db->where('tbl_quotes.date >=', $start_date_search);
        }

        if ($end_date_search) {
            $this->db->where('tbl_quotes.date <=', $end_date_search);
        }

        if ($customers) {
            $this->db->where('tbl_quotes.customer_id', $customers);
        }
        $quotes = $this->db->get()->row_array();

        $data['count_quotes'] = !empty($quotes['count_quotes']) ? $quotes['count_quotes'] : 0;
        echo responseData($data);
    }

    public function getQuotesPass()
    {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers = $this->input->post('customers');

        $aColumns = [
            'tbl_quotes.date as date',
            'tbl_quotes.reference_no as reference_no',
            'tblclients.zcode as zcode',
            'tbl_quotes.status as status',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_stage_quote.name as name_stage_quote',
            'tbl_quote_items.technical_explanation as technical_explanation',
            'tblunits.unit as unit',
            'CONCAT(tbl_quote_items.moq, "-", tbl_quote_items.moq_to) as moq',
            'tbl_quote_items.unit_price as unit_price',
            'tbl_quote_items.discount_precent_item as discount_precent_item',
            'tbl_quote_items.lead_time as lead_time',
            'tbl_quote_items.note_item as note_item',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_quotes';
        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_quotes.customer_id',
            'LEFT JOIN tbl_orders ON tbl_orders.id = tbl_quotes.order_id',
            'INNER JOIN tbl_quote_items ON tbl_quote_items.quote_id = tbl_quotes.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_quote_items.item_id',
            'LEFT JOIN tbl_stage_quote ON tbl_stage_quote.id = tbl_quote_items.quote_stage_id',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
        ];

        $groupBy = '';
        $where        = [
            ' AND tbl_orders.type_orders = 1 '
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_quotes.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_quotes.date <= '$end_date_search'");
        }

        if (!empty($customers)) {
            array_push($where, "AND tbl_quotes.customer_id = '$customers'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_quotes.id as id_quote'
        ], $groupBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $aColumns = handlingColumns($aColumns);
        $stt = 0;

        foreach ($rResult as $key => $aRow) {
            $stt++;

            $row = [];
            foreach ($aColumns as $k => $v) {
                if ($v == 'reference_no') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/quotes/view_quotes/'.$aRow['id_quote']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'date') {
                    $row[] = _d($aRow[$v]);
                } else if ($v == 'status') {
                    $row[] = '<div class="text-center">'.($aRow[$v] == 'approved' ? 'Đã duyệt' : ($aRow[$v] == 'un_approved' ? 'Chưa duyệt' : 'Hủy')).'</div>';
                } else {
                    $row[] = '<div class="text-center">'.$aRow[$v].'</div>';
                }
            }
            $output['aaData'][] = $row;
        }
        
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getQuotesFail()
    {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers = $this->input->post('customers');

        $aColumns = [
            'tbl_quotes.date as date',
            'tbl_quotes.reference_no as reference_no',
            'tblclients.zcode as zcode',
            'tbl_quotes.status as status',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_stage_quote.name as name_stage_quote',
            'tbl_quote_items.technical_explanation as technical_explanation',
            'tblunits.unit as unit',
            'CONCAT(tbl_quote_items.moq, "-", tbl_quote_items.moq_to) as moq',
            'tbl_quote_items.unit_price as unit_price',
            'tbl_quote_items.discount_precent_item as discount_precent_item',
            'tbl_quote_items.lead_time as lead_time',
            'tbl_quote_items.note_item as note_item',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_quotes';
        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_quotes.customer_id',
            'INNER JOIN tbl_quote_items ON tbl_quote_items.quote_id = tbl_quotes.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_quote_items.item_id',
            'LEFT JOIN tbl_stage_quote ON tbl_stage_quote.id = tbl_quote_items.quote_stage_id',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
        ];

        $groupBy = '';
        $where        = [
            ' AND tbl_quotes.status = "cancel" '
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_quotes.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_quotes.date <= '$end_date_search'");
        }

        if (!empty($customers)) {
            array_push($where, "AND tbl_quotes.customer_id = '$customers'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_quotes.id as id_quote'
        ], $groupBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $aColumns = handlingColumns($aColumns);
        $stt = 0;

        foreach ($rResult as $key => $aRow) {
            $stt++;

            $row = [];
            foreach ($aColumns as $k => $v) {
                if ($v == 'reference_no') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/quotes/view_quotes/'.$aRow['id_quote']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'date') {
                    $row[] = _d($aRow[$v]);
                } else if ($v == 'status') {
                    $row[] = '<div class="text-center">'.($aRow[$v] == 'approved' ? 'Đã duyệt' : ($aRow[$v] == 'un_approved' ? 'Chưa duyệt' : 'Hủy')).'</div>';
                } else {
                    $row[] = '<div class="text-center">'.$aRow[$v].'</div>';
                }
            }
            $output['aaData'][] = $row;
        }
        
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getQuotesSample()
    {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers = $this->input->post('customers');

        $aColumns = [
            'tbl_quotes.date as date',
            'tbl_quotes.reference_no as reference_no',
            'tbl_request_template.reference_no as reference_no_template',
            'tblclients.zcode as zcode',
            'tbl_quotes.status as status',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_stage_quote.name as name_stage_quote',
            'tbl_quote_items.technical_explanation as technical_explanation',
            'tblunits.unit as unit',
            'CONCAT(tbl_quote_items.moq, "-", tbl_quote_items.moq_to) as moq',
            'tbl_quote_items.unit_price as unit_price',
            'tbl_quote_items.discount_precent_item as discount_precent_item',
            'tbl_quote_items.lead_time as lead_time',
            'tbl_quote_items.note_item as note_item',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_quotes';
        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_quotes.customer_id',
            'INNER JOIN tbl_quote_items ON tbl_quote_items.quote_id = tbl_quotes.id',
            'INNER JOIN tbl_request_template ON tbl_request_template.id_quotes = tbl_quotes.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_quote_items.item_id',
            'LEFT JOIN tbl_stage_quote ON tbl_stage_quote.id = tbl_quote_items.quote_stage_id',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
        ];

        $groupBy = '';
        $where        = [
            
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_quotes.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_quotes.date <= '$end_date_search'");
        }

        if (!empty($customers)) {
            array_push($where, "AND tbl_quotes.customer_id = '$customers'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_quotes.id as id_quote',
            'tbl_request_template.id as id_request_template',
        ], $groupBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $aColumns = handlingColumns($aColumns);
        $stt = 0;

        foreach ($rResult as $key => $aRow) {
            $stt++;

            $row = [];
            foreach ($aColumns as $k => $v) {
                if ($v == 'reference_no') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/quotes/view_quotes/'.$aRow['id_quote']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'reference_no_template') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/request_template/view/'.$aRow['id_request_template']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'date') {
                    $row[] = _d($aRow[$v]);
                } else if ($v == 'status') {
                    $row[] = '<div class="text-center">'.($aRow[$v] == 'approved' ? 'Đã duyệt' : ($aRow[$v] == 'un_approved' ? 'Chưa duyệt' : 'Hủy')).'</div>';
                } else {
                    $row[] = '<div class="text-center">'.$aRow[$v].'</div>';
                }
            }
            $output['aaData'][] = $row;
        }
        
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getTotalQuotesSample() {
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $customers = $this->input->get('customers');

        if (!empty($start_date_search)) $start_date_search = to_sql_date($start_date_search).' 00:00:00';
        if (!empty($end_date_search)) $end_date_search = to_sql_date($end_date_search). ' 23:59:59';

        $this->db->select('
            COUNT(tbl_quotes.id) as count_quotes,
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->where(' EXISTS (
            SELECT 1
            FROM tbl_request_template
            WHERE tbl_request_template.id_quotes = tbl_quotes.id
        )', false, false);
        if ($start_date_search) {
            $this->db->where('tbl_quotes.date >=', $start_date_search);
        }

        if ($end_date_search) {
            $this->db->where('tbl_quotes.date <=', $end_date_search);
        }

        if ($customers) {
            $this->db->where('tbl_quotes.customer_id', $customers);
        }
        $quotes = $this->db->get()->row_array();

        $data['count_quotes'] = !empty($quotes['count_quotes']) ? $quotes['count_quotes'] : 0;
        echo responseData($data);
    }

    public function getTotalRequestSample() {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers = $this->input->post('customers');

        $aColumns = [
            'tbl_request_template.date as date',
            'tbl_request_template.reference_no as reference_no_template',
            'tbl_quotes.reference_no as reference_no',
            'tblclients.zcode as zcode',
            'tbl_category_products.name as name_category_product',
            'tbl_species.name as name_species',
            'tblunits.unit as unit_product',
            'tbl_products.height as height',
            'tbl_products.wide as width',
            'tb_unit_measure.unit as unit_measure_product',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_brand.name as name_branch',
            'tbl_products.quantity_max as quantity_inventory',
            'tbl_products.time_inventory as time_inventory',
            'tbl_products.quota_time_change_one as quote_time',
            
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_request_template';
        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_request_template.client_id',
            'INNER JOIN tbl_request_template_item ON tbl_request_template_item.request_template_id = tbl_request_template.id',
            'LEFT JOIN tbl_quotes ON tbl_request_template.id_quotes = tbl_quotes.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_request_template_item.items_id',
            'LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_products.species',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
            'LEFT JOIN tblunits unit_stock ON tbl_products.conversion_unit = unit_stock.unitid',
            'LEFT JOIN tblunits tb_unit_measure ON tbl_products.unit_measure = tb_unit_measure.unitid',
            'LEFT JOIN tbl_brand ON tbl_brand.id = tbl_products.brand_id',
        ];

        $groupBy = '';
        $where        = [
            
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tbl_request_template.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tbl_request_template.date <= '$end_date_search'");
        }

        if (!empty($customers)) {
            array_push($where, "AND tbl_request_template.client_id = '$customers'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_quotes.id as id_quote',
            'tbl_request_template.id as id_request_template',
        ], $groupBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $aColumns = handlingColumns($aColumns);
        $stt = 0;

        foreach ($rResult as $key => $aRow) {
            $stt++;

            $row = [];
            foreach ($aColumns as $k => $v) {
                if ($v == 'reference_no') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/quotes/view_quotes/'.$aRow['id_quote']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'reference_no_template') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/request_template/view/'.$aRow['id_request_template']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'date') {
                    $row[] = _d($aRow[$v]);
                } else if ($v == 'status') {
                    $row[] = '<div class="text-center">'.($aRow[$v] == 'approved' ? 'Đã duyệt' : ($aRow[$v] == 'un_approved' ? 'Chưa duyệt' : 'Hủy')).'</div>';
                } else if ($v == 'quantity_inventory' || $v == 'time_inventory' || $v == 'quote_time') {
                    $row[] = '<div class="text-center">'.formatNumber($aRow[$v]).'</div>';
                } else {
                    $row[] = '<div class="text-center">'.$aRow[$v].'</div>';
                }
            }
            $output['aaData'][] = $row;
        }
        
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getTotalRequestQuotesSample() {
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $customers = $this->input->get('customers');

        if (!empty($start_date_search)) $start_date_search = to_sql_date($start_date_search);
        if (!empty($end_date_search)) $end_date_search = to_sql_date($end_date_search);

        $this->db->select('
            COUNT(tbl_request_template.id) as count_request_template,
        ', false);
        $this->db->from('tbl_request_template');
        if ($start_date_search) {
            $this->db->where('tbl_request_template.date >=', $start_date_search);
        }

        if ($end_date_search) {
            $this->db->where('tbl_request_template.date <=', $end_date_search);
        }

        if ($customers) {
            $this->db->where('tbl_request_template.client_id', $customers);
        }
        $quotes = $this->db->get()->row_array();

        $data['count_request_template'] = !empty($quotes['count_request_template']) ? $quotes['count_request_template'] : 0;
        echo responseData($data);
    }

    public function getTotalRequestSamplePass() {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers = $this->input->post('customers');

        $aColumns = [
            'tbl_request_template.date as date',
            'tbl_request_template.reference_no as reference_no_template',
            'tbl_quotes.reference_no as reference_no',
            'tblclients.zcode as zcode',
            'tbl_category_products.name as name_category_product',
            'tbl_species.name as name_species',
            'tblunits.unit as unit_product',
            'tbl_products.height as height',
            'tbl_products.wide as width',
            'tb_unit_measure.unit as unit_measure_product',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_brand.name as name_branch',
            'tbl_products.quantity_max as quantity_inventory',
            'tbl_products.time_inventory as time_inventory',
            'tbl_products.quota_time_change_one as quote_time',
            
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_request_template';
        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_request_template.client_id',
            'INNER JOIN tbl_request_template_item ON tbl_request_template_item.request_template_id = tbl_request_template.id',
            'INNER JOIN tbl_quotes ON tbl_request_template.id_quotes = tbl_quotes.id',
            'INNER JOIN tbl_orders ON tbl_orders.id = tbl_quotes.order_id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_request_template_item.items_id',
            'LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_products.species',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
            'LEFT JOIN tblunits unit_stock ON tbl_products.conversion_unit = unit_stock.unitid',
            'LEFT JOIN tblunits tb_unit_measure ON tbl_products.unit_measure = tb_unit_measure.unitid',
            'LEFT JOIN tbl_brand ON tbl_brand.id = tbl_products.brand_id',
        ];

        $groupBy = '';
        $where        = [
            ' AND tbl_orders.type_orders = 1'
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tbl_request_template.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tbl_request_template.date <= '$end_date_search'");
        }

        if (!empty($customers)) {
            array_push($where, "AND tbl_request_template.client_id = '$customers'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_quotes.id as id_quote',
            'tbl_request_template.id as id_request_template',
        ], $groupBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $aColumns = handlingColumns($aColumns);
        $stt = 0;

        foreach ($rResult as $key => $aRow) {
            $stt++;

            $row = [];
            foreach ($aColumns as $k => $v) {
                if ($v == 'reference_no') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/quotes/view_quotes/'.$aRow['id_quote']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'reference_no_template') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/request_template/view/'.$aRow['id_request_template']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'date') {
                    $row[] = _d($aRow[$v]);
                } else if ($v == 'status') {
                    $row[] = '<div class="text-center">'.($aRow[$v] == 'approved' ? 'Đã duyệt' : ($aRow[$v] == 'un_approved' ? 'Chưa duyệt' : 'Hủy')).'</div>';
                } else if ($v == 'quantity_inventory' || $v == 'time_inventory' || $v == 'quote_time') {
                    $row[] = '<div class="text-center">'.formatNumber($aRow[$v]).'</div>';
                } else {
                    $row[] = '<div class="text-center">'.$aRow[$v].'</div>';
                }
            }
            $output['aaData'][] = $row;
        }
        
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getCountRequestSamplePass() {
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $customers = $this->input->get('customers');

        if (!empty($start_date_search)) $start_date_search = to_sql_date($start_date_search);
        if (!empty($end_date_search)) $end_date_search = to_sql_date($end_date_search);

        $this->db->select('
            COUNT(tbl_request_template.id) as count_request_template,
        ', false);
        $this->db->from('tbl_request_template');
        $this->db->where(' EXISTS (
            SELECT 1
            FROM tbl_quotes
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_quotes.order_id
            WHERE tbl_quotes.id = tbl_request_template.id_quotes AND tbl_orders.type_orders = 1
        )', false, false);
        if ($start_date_search) {
            $this->db->where('tbl_request_template.date >=', $start_date_search);
        }

        if ($end_date_search) {
            $this->db->where('tbl_request_template.date <=', $end_date_search);
        }

        if ($customers) {
            $this->db->where('tbl_request_template.client_id', $customers);
        }
        $quotes = $this->db->get()->row_array();

        $data['count_request_template'] = !empty($quotes['count_request_template']) ? $quotes['count_request_template'] : 0;
        echo responseData($data);
    }

    public function getTotalRequestSampleFail() {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers = $this->input->post('customers');

        $aColumns = [
            'tbl_request_template.date as date',
            'tbl_request_template.reference_no as reference_no_template',
            'tbl_quotes.reference_no as reference_no',
            'tblclients.zcode as zcode',
            'tbl_category_products.name as name_category_product',
            'tbl_species.name as name_species',
            'tblunits.unit as unit_product',
            'tbl_products.height as height',
            'tbl_products.wide as width',
            'tb_unit_measure.unit as unit_measure_product',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_brand.name as name_branch',
            'tbl_products.quantity_max as quantity_inventory',
            'tbl_products.time_inventory as time_inventory',
            'tbl_products.quota_time_change_one as quote_time',
            
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_request_template';
        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_request_template.client_id',
            'INNER JOIN tbl_request_template_item ON tbl_request_template_item.request_template_id = tbl_request_template.id',
            'INNER JOIN tbl_quotes ON tbl_request_template.id_quotes = tbl_quotes.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_request_template_item.items_id',
            'LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_products.species',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
            'LEFT JOIN tblunits unit_stock ON tbl_products.conversion_unit = unit_stock.unitid',
            'LEFT JOIN tblunits tb_unit_measure ON tbl_products.unit_measure = tb_unit_measure.unitid',
            'LEFT JOIN tbl_brand ON tbl_brand.id = tbl_products.brand_id',
        ];

        $groupBy = '';
        $where        = [
            ' AND tbl_quotes.status = "cancel"'
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tbl_request_template.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tbl_request_template.date <= '$end_date_search'");
        }

        if (!empty($customers)) {
            array_push($where, "AND tbl_request_template.client_id = '$customers'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_quotes.id as id_quote',
            'tbl_request_template.id as id_request_template',
        ], $groupBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $aColumns = handlingColumns($aColumns);
        $stt = 0;

        foreach ($rResult as $key => $aRow) {
            $stt++;

            $row = [];
            foreach ($aColumns as $k => $v) {
                if ($v == 'reference_no') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/quotes/view_quotes/'.$aRow['id_quote']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'reference_no_template') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/request_template/view/'.$aRow['id_request_template']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'date') {
                    $row[] = _d($aRow[$v]);
                } else if ($v == 'status') {
                    $row[] = '<div class="text-center">'.($aRow[$v] == 'approved' ? 'Đã duyệt' : ($aRow[$v] == 'un_approved' ? 'Chưa duyệt' : 'Hủy')).'</div>';
                } else if ($v == 'quantity_inventory' || $v == 'time_inventory' || $v == 'quote_time') {
                    $row[] = '<div class="text-center">'.formatNumber($aRow[$v]).'</div>';
                } else {
                    $row[] = '<div class="text-center">'.$aRow[$v].'</div>';
                }
            }
            $output['aaData'][] = $row;
        }
        
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getCountRequestSampleFail() {
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $customers = $this->input->get('customers');

        if (!empty($start_date_search)) $start_date_search = to_sql_date($start_date_search);
        if (!empty($end_date_search)) $end_date_search = to_sql_date($end_date_search);

        $this->db->select('
            COUNT(tbl_request_template.id) as count_request_template,
        ', false);
        $this->db->from('tbl_request_template');
        $this->db->where(' EXISTS (
            SELECT 1
            FROM tbl_quotes
            WHERE tbl_quotes.id = tbl_request_template.id_quotes AND tbl_quotes.status = "cancel"
        )', false, false);
        if ($start_date_search) {
            $this->db->where('tbl_request_template.date >=', $start_date_search);
        }

        if ($end_date_search) {
            $this->db->where('tbl_request_template.date <=', $end_date_search);
        }

        if ($customers) {
            $this->db->where('tbl_request_template.client_id', $customers);
        }
        $quotes = $this->db->get()->row_array();

        $data['count_request_template'] = !empty($quotes['count_request_template']) ? $quotes['count_request_template'] : 0;
        echo responseData($data);
    }

    public function getDetailQuotesOrders()
    {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers = $this->input->post('customers');

        $tbQuotesSubOrdersSample = "(
            SELECT
                tbl_orders_sub.quote_id_chonse as quote_id_chonse,
                COUNT(tbl_orders_sub.order_id) as count_develop
            FROM tbl_orders_sub
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_orders_sub.order_id
            WHERE tbl_orders.type_orders = ".TYPE_SAMPLE_ORDER."
            GROUP BY tbl_orders_sub.quote_id_chonse
        ) tb_order_sub";

        $aColumns = [
            'tbl_quotes.date as date',
            'tbl_quotes.reference_no as reference_no',
            'tblclients.zcode as zcode',
            'tbl_quotes.status as status',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_stage_quote.name as name_stage_quote',
            'tbl_quote_items.technical_explanation as technical_explanation',
            'tblunits.unit as unit',
            'CONCAT(tbl_quote_items.moq, "-", tbl_quote_items.moq_to) as moq',
            'tbl_quote_items.unit_price as unit_price',
            'tbl_quote_items.discount_precent_item as discount_precent_item',
            'tbl_quote_items.lead_time as lead_time',
            'tbl_quote_items.note_item as note_item',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_quotes';
        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_quotes.customer_id',
            'INNER JOIN '.$tbQuotesSubOrdersSample.' ON tb_order_sub.quote_id_chonse = tbl_quotes.id',
            'INNER JOIN tbl_quote_items ON tbl_quote_items.quote_id = tbl_quotes.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_quote_items.item_id',
            'LEFT JOIN tbl_stage_quote ON tbl_stage_quote.id = tbl_quote_items.quote_stage_id',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
        ];

        $groupBy = '';
        $where        = [
            ' AND COALESCE(tb_order_sub.count_develop) > 1 '
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_quotes.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_quotes.date <= '$end_date_search'");
        }

        if (!empty($customers)) {
            array_push($where, "AND tbl_quotes.customer_id = '$customers'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_quotes.id as id_quote'
        ], $groupBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $aColumns = handlingColumns($aColumns);
        $stt = 0;

        foreach ($rResult as $key => $aRow) {
            $stt++;

            $row = [];
            foreach ($aColumns as $k => $v) {
                if ($v == 'reference_no') {
                    $row[] = '<a class="tnh-modal" href="'.base_url('admin/quotes/view_quotes/'.$aRow['id_quote']).'">'.$aRow[$v].'</a>';
                } else if ($v == 'date') {
                    $row[] = _d($aRow[$v]);
                } else if ($v == 'status') {
                    $row[] = '<div class="text-center">'.($aRow[$v] == 'approved' ? 'Đã duyệt' : ($aRow[$v] == 'un_approved' ? 'Chưa duyệt' : 'Hủy')).'</div>';
                } else {
                    $row[] = '<div class="text-center">'.$aRow[$v].'</div>';
                }
            }
            $output['aaData'][] = $row;
        }
        
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getTotalSampleOrders() {
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $customers = $this->input->get('customers');

        $tbQuotesSubOrdersSample = "(
            SELECT
                tbl_orders_sub.quote_id_chonse as quote_id_chonse,
                COUNT(tbl_orders_sub.order_id) as count_develop
            FROM tbl_orders_sub
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_orders_sub.order_id
            WHERE tbl_orders.type_orders = ".TYPE_SAMPLE_ORDER."
            GROUP BY tbl_orders_sub.quote_id_chonse
        ) tb_order_sub";

        if (!empty($start_date_search)) $start_date_search = to_sql_date($start_date_search).' 00:00:00';
        if (!empty($end_date_search)) $end_date_search = to_sql_date($end_date_search). ' 23:59:59';

        $this->db->select('
            COUNT(tbl_quotes.id) as count_quotes,
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->join($tbQuotesSubOrdersSample, 'tb_order_sub.quote_id_chonse = tbl_quotes.id', 'inner');
        $this->db->where(' COALESCE(tb_order_sub.count_develop) > 1 ', false, false);
        if ($start_date_search) {
            $this->db->where('tbl_quotes.date >=', $start_date_search);
        }

        if ($end_date_search) {
            $this->db->where('tbl_quotes.date <=', $end_date_search);
        }

        if ($customers) {
            $this->db->where('tbl_quotes.customer_id', $customers);
        }
        $quotes = $this->db->get()->row_array();

        $data['count_quotes'] = !empty($quotes['count_quotes']) ? $quotes['count_quotes'] : 0;
        echo responseData($data);
    }
}