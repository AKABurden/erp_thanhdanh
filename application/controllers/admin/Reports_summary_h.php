<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Reports_summary_h extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSummaryQuotes()
    {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers_search = $this->input->post('customers_search');

        $aColumns = [
            'tbl_quotes.id as id',
            'tbl_quotes.customer_id as customer_id',
            '"" as customer_name',
            '"" as brand',
            'tbl_quotes.quotation_request_id as quotation_request_id',
            'tbl_quotes.reference_no as reference_no',
            'tbl_quotes.date as date',
            'IF(tbl_quotes.date_updated IS NOT NULL, tbl_quotes.date_updated, tbl_quotes.date_created) as date_finished',
            '"" as item_code',
            '"" as item_name',
            '"" as is_lot',
            '"" as is_child',
            '"" as name_discount',
            'IF(tbl_quotes.status = "approved", tbl_quotes.date_status, "") as date_status',
            'tbl_quotes.date_updated as date_updated',
            '"" as is_order',
            '"" as is_not_order',
            'tbl_quotes.is_quote_again as is_quote_again',
            '"" as code_bckph',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_quotes';
        $join = [];

        $groupByAndOrderBy = '';
        $where        = [];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_quotes.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_quotes.date <= '$end_date_search'");
        }

        if (!empty($customers_search)) {
            $customers_search = $this->db->escape($customers_search);
            array_push($where, "AND tbl_quotes.customer_id = $customers_search");
        }

        $groupByAndOrderBy = 'ORDER BY tbl_quotes.id DESC';
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], $groupByAndOrderBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];


        if (!empty($rResult)) {
            $arrCustomerId = [];
            $arrQuoteId = [];
            $arrQuotationRequestId = [];
            foreach ($rResult as $key => $value) {
                $arrCustomerId[] = $value['customer_id'];
                $arrQuoteId[] = $value['id'];
                $arrQuotationRequestId[] = $value['quotation_request_id'];
            }

            if (!empty($arrCustomerId)) {
                $arrCustomerId = array_unique($arrCustomerId);

                $tbGroupCustomer = '(
                    SELECT
                        tblcustomer_groups.customer_id as customer_id,
                        GROUP_CONCAT(tblcustomers_groups.name) as group_name
                    FROM tblcustomer_groups
                    INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                    WHERE tblcustomer_groups.customer_id IN (' . implode(',', $arrCustomerId) . ')
                    GROUP BY tblcustomer_groups.customer_id
                ) tb_customer_group';

                $this->db->select('
                    tblclients.userid,
                    tblclients.company_short,
                    tblclients.company,
                    tb_customer_group.group_name as brand,
                    tbl_discount.name as name_discount
                ', false);
                $this->db->from('tblclients');
                $this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
                $this->db->join('tbl_discount', 'tbl_discount.id = tblclients.discount_id', 'left');
                $this->db->where_in('tblclients.userid', $arrCustomerId, false);
                $listCustomers = $this->db->get()->result_array();
                if (!empty($listCustomers)) {
                    $listCustomers = array_reduce($listCustomers, function ($carry, $item) {
                        $carry[$item['userid']] = $item;
                        return $carry;
                    });
                }
            }

            //quote items

            $group_price_detail = "(
                SELECT 
                    tblgroup_price_detail.product_id as product_id,
                    tblgroup_price_detail.quotes_id as quotes_id,
                    tblgroup_price_detail.is_lot as is_lot

                FROM tblgroup_price_detail
                WHERE tblgroup_price_detail.quotes_id IN (" . implode(',', $arrQuoteId) . ")
                GROUP BY tblgroup_price_detail.product_id, tblgroup_price_detail.quotes_id
            ) tb_group_price_detail";

            $this->db->select('
                tbl_quote_items.quote_id as quote_id,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tb_group_price_detail.is_lot as is_lot,
            ');
            $this->db->from('tbl_quote_items');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_quote_items.item_id');
            $this->db->join($group_price_detail, 'tb_group_price_detail.product_id = tbl_quote_items.item_id AND tb_group_price_detail.quotes_id = tbl_quote_items.quote_id', 'left');
            $this->db->where_in('tbl_quote_items.quote_id', $arrQuoteId, false);
            $this->db->where('tbl_quote_items.type_item', 'products');
            $quoteItems = $this->db->get()->result_array();
            if (!empty($quoteItems)) {
                $quoteItems = array_reduce($quoteItems, function ($carry, $item) {
                    $carry[$item['quote_id']][] = $item;
                    return $carry;
                });
            }

            //is orders
            $this->db->select('
                tbl_orders.quotes_id,
                1 as is_order
            ', false);
            $this->db->from('tbl_orders');
            $this->db->where_in('tbl_orders.quotes_id', $arrQuoteId, false);
            $this->db->group_by('tbl_orders.quotes_id');
            $orders = $this->db->get()->result_array();
            if (!empty($orders)) {
                $orders = array_reduce($orders, function ($carry, $item) {
                    $carry[$item['quotes_id']] = $item;
                    return $carry;
                });
            }

            //yêu cầu
            if (!empty($arrQuotationRequestId)) {
                $arrQuotationRequestId = array_unique($arrQuotationRequestId);
                $this->db->select('
                    tblquotation_request.id,
                    tblquotation_request.code as code
                ', false);
                $this->db->from('tblquotation_request');
                $this->db->where_in('tblquotation_request.id', $arrQuotationRequestId, false);
                $quotationRequests = $this->db->get()->result_array();
                if (!empty($quotationRequests)) {
                    $quotationRequests = array_reduce($quotationRequests, function ($carry, $item) {
                        $carry[$item['id']] = $item;
                        return $carry;
                    });
                }
            }

            //Báo cáo không phù hợp
            $this->db->select('
                tblproduction_report.id_quotes as id_quotes,
                tblproduction_report.id as id_production_report,
                tblproduction_report.reference_no as reference_no,
            ', false);
            $this->db->from('tblproduction_report');
            $this->db->where_in('tblproduction_report.id_quotes', $arrQuoteId, false);
            $list_production_report = $this->db->get()->result_array();
            if (!empty($list_production_report)) {
                $list_production_report = array_reduce($list_production_report, function ($carry, $item) {
                    $carry[$item['id_quotes']][] = $item;
                    return $carry;
                });
            }
        }

        $aColumns = handlingColumns($aColumns);
        $stt = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $stt++;
            $customer_id = $aRow['customer_id'];
            $dtCustomer = $listCustomers[$customer_id] ?? null;
            $company_short = $dtCustomer['company_short'] ?? '';
            $customer_name = $dtCustomer['company'] ?? '';
            $name_discount = $dtCustomer['name_discount'] ?? '';
            $brand = $dtCustomer['brand'] ?? '';
            $items = $quoteItems[$aRow['id']] ?? [];
            $order = $orders[$aRow['id']] ?? 0;
            $is_order = !empty($order['is_order']) ? 1 : 0;
            $quotation_request = $quotationRequests[$aRow['quotation_request_id']] ?? null;
            $production_report = $list_production_report[$aRow['id']] ?? null;
            $html_production_report = '';
            if (!empty($production_report)) {
                foreach ($production_report as $kP => $vP) {
                    $html_production_report .= '<div><a class="c_modal" href="' . base_url('admin/production_report/modal/' . $vP['id_production_report']) . '">' . $vP['reference_no'] . '</a></div>';
                }
            }

            if (!empty($items)) {
                foreach ($items as $kI => $item) {
                    $row = [];

                    $is_lot = $item['is_lot'] ?? 0;
                    foreach ($aColumns as $k => $v) {
                        $_data = $aRow[$v];
                        if ($kI != 0 && in_array($v, ['id', 'customer_id', 'customer_name', 'brand', 'date', 'date_finished', 'reference_no', 'name_discount', 'date_status', 'date_updated', 'is_order', 'is_not_order', 'quotation_request_id', 'is_quote_again', 'code_bckph'])) {
                            $row[] = '';
                        } else {
                            if ($v == 'id') {
                                $row[] = '<div class="text-center">' . $stt . '</div>';
                            } else if ($v == 'customer_id') {
                                $row[] = '<a target="_blank" href="' . base_url('admin/clients/client/' . $aRow['customer_id'] . '?view') . '">' . $company_short . '</a>';
                            } else if ($v == 'customer_name') {
                                $row[] = $customer_name;
                            } else if ($v == 'brand') {
                                $row[] = $brand;
                            } else if ($v == 'date') {
                                $row[] = _dt($_data);
                            } else if ($v == 'date_finished') {
                                $row[] = _dt($_data);
                            } else if ($v == 'item_code') {
                                $row[] = $item['item_code'];
                            } else if ($v == 'item_name') {
                                $row[] = '<div style="width: 120px; word-break: break-all;">' . $item['item_name'] . '</div>';
                            } else if ($v == 'is_lot') {
                                $row[] = $is_lot == 1 ? 'Theo lô' : '';
                            } else if ($v == 'is_child') {
                                $row[] = $is_lot == 0 ? 'Theo con' : '';
                            } else if ($v == 'name_discount') {
                                $row[] = $name_discount;
                            } else if ($v == 'date_status') {
                                $row[] = $_data ? _dt($_data) : '';
                            } else if ($v == 'date_updated') {
                                $row[] = $_data ? _dt($_data) : '';
                            } else if ($v == 'is_order') {
                                $row[] = $is_order == 1 ? '1' : '';
                            } else if ($v == 'is_not_order') {
                                $row[] = $is_order == 0 ? '1' : '';
                            } else if ($v == 'quotation_request_id') {
                                $row[] = !empty($quotation_request) ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/quotation_request/view/' . $quotation_request['id']) . '" data-toggle="modal" data-target="#myModal">' . $quotation_request['code'] . '</a>' : '';
                            } else if ($v == 'is_quote_again') {
                                $row[] = $_data ? 'Có' : '';
                            } else if ($v == 'code_bckph') {
                                $row[] = $html_production_report;
                            } else if ($v == 'reference_no') {
                                $row[] = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/quotes/view_quotes/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a>';
                            } else {
                                $row[] = $_data;
                            }
                        }
                    }
                    $output['aaData'][] = $row;
                }
            }
        }

        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function exportExcelQuotes()
    {
        // if (!$this->perViewOvenOut && !$this->perViewOwnOvenOut) {
        //     $response = array(
        //         'result' => 0,
        //         'message' => lang('Bạn không có quyền truy cập'),
        //     );
        //     echo json_encode($response);
        // }

        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers_search = $this->input->post('customers_search');
        if (empty($start_date_search) || empty($end_date_search)) {
            $response = array(
                'result' => 0,
                'message' => lang('Vui lòng nhập ngày bắt đầu và kết thúc'),
            );
            echo json_encode($response);
            return;
        }

        $excel = cloumns_excel();
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $excel = cloumns_excel();
        insertCompanyInfo($objPHPExcel, 'C1:P2');

        $rowBegin = 5;
        $iExcel = -1;
        
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('STT'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã khách hàng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tên khách hàng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã Brand'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã phiếu yêu cầu báo giá'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Bảng báo giá'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày báo giá'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày hoàn thành báo giá'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã sản phẩm'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tên sản phẩm'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Theo lô'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Báo giá theo con'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Chiết khấu'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày duyệt báo giá'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày cập nhật Foso'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Có đơn hàng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Không có đơn hàng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Báo giá lại'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã BCKPH'));

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $excel[$iExcel] . $rowBegin)->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $aColumns = [
            'tbl_quotes.id as id',
            'tbl_quotes.customer_id as customer_id',
            '"" as customer_name',
            '"" as brand',
            'tbl_quotes.quotation_request_id as quotation_request_id',
            'tbl_quotes.reference_no as reference_no',
            'tbl_quotes.date as date',
            'IF(tbl_quotes.date_updated IS NOT NULL, tbl_quotes.date_updated, tbl_quotes.date_created) as date_finished',
            '"" as item_code',
            '"" as item_name',
            '"" as is_lot',
            '"" as is_child',
            '"" as name_discount',
            'IF(tbl_quotes.status = "approved", tbl_quotes.date_status, "") as date_status',
            'tbl_quotes.date_updated as date_updated',
            '"" as is_order',
            '"" as is_not_order',
            'tbl_quotes.is_quote_again as is_quote_again',
            '"" as code_bckph',
        ];
        
        // Thiết lập dữ liệu truy vấn
        $sIndexColumn = 'id';
        $sTable       = 'tbl_quotes';
        $join = [];
        
        $groupByAndOrderBy = '';
        $where        = [];
        
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_quotes.date >= '$start_date_search'");
        }
        
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_quotes.date <= '$end_date_search'");
        }
        
        if (!empty($customers_search)) {
            $customers_search = $this->db->escape($customers_search);
            array_push($where, "AND tbl_quotes.customer_id = $customers_search");
        }
        
        $groupByAndOrderBy = 'ORDER BY tbl_quotes.id DESC';
        
        // Thực hiện truy vấn
        $query = "SELECT " . implode(', ', $aColumns) . " FROM $sTable " . implode(' ', $join) . " WHERE 1 " . implode(' ', $where) . " $groupByAndOrderBy";
        $rResult = $this->db->query($query)->result_array();
        if (!empty($rResult)) {
            $arrCustomerId = [];
            $arrQuoteId = [];
            $arrQuotationRequestId = [];
            foreach ($rResult as $key => $value) {
                $arrCustomerId[] = $value['customer_id'];
                $arrQuoteId[] = $value['id'];
                $arrQuotationRequestId[] = $value['quotation_request_id'];
            }

            if (!empty($arrCustomerId)) {
                $arrCustomerId = array_unique($arrCustomerId);

                $tbGroupCustomer = '(
                    SELECT
                        tblcustomer_groups.customer_id as customer_id,
                        GROUP_CONCAT(tblcustomers_groups.name) as group_name
                    FROM tblcustomer_groups
                    INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                    WHERE tblcustomer_groups.customer_id IN (' . implode(',', $arrCustomerId) . ')
                    GROUP BY tblcustomer_groups.customer_id
                ) tb_customer_group';

                $this->db->select('
                    tblclients.userid,
                    tblclients.company_short,
                    tblclients.company,
                    tb_customer_group.group_name as brand,
                    tbl_discount.name as name_discount
                ', false);
                $this->db->from('tblclients');
                $this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
                $this->db->join('tbl_discount', 'tbl_discount.id = tblclients.discount_id', 'left');
                $this->db->where_in('tblclients.userid', $arrCustomerId, false);
                $listCustomers = $this->db->get()->result_array();
                if (!empty($listCustomers)) {
                    $listCustomers = array_reduce($listCustomers, function ($carry, $item) {
                        $carry[$item['userid']] = $item;
                        return $carry;
                    });
                }
            }

            //quote items

            $group_price_detail = "(
                SELECT 
                    tblgroup_price_detail.product_id as product_id,
                    tblgroup_price_detail.quotes_id as quotes_id,
                    tblgroup_price_detail.is_lot as is_lot

                FROM tblgroup_price_detail
                WHERE tblgroup_price_detail.quotes_id IN (" . implode(',', $arrQuoteId) . ")
                GROUP BY tblgroup_price_detail.product_id, tblgroup_price_detail.quotes_id
            ) tb_group_price_detail";

            $this->db->select('
                tbl_quote_items.quote_id as quote_id,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tb_group_price_detail.is_lot as is_lot,
            ');
            $this->db->from('tbl_quote_items');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_quote_items.item_id');
            $this->db->join($group_price_detail, 'tb_group_price_detail.product_id = tbl_quote_items.item_id AND tb_group_price_detail.quotes_id = tbl_quote_items.quote_id', 'left');
            $this->db->where_in('tbl_quote_items.quote_id', $arrQuoteId, false);
            $this->db->where('tbl_quote_items.type_item', 'products');
            $quoteItems = $this->db->get()->result_array();
            if (!empty($quoteItems)) {
                $quoteItems = array_reduce($quoteItems, function ($carry, $item) {
                    $carry[$item['quote_id']][] = $item;
                    return $carry;
                });
            }

            //is orders
            $this->db->select('
                tbl_orders.quotes_id,
                1 as is_order
            ', false);
            $this->db->from('tbl_orders');
            $this->db->where_in('tbl_orders.quotes_id', $arrQuoteId, false);
            $this->db->group_by('tbl_orders.quotes_id');
            $orders = $this->db->get()->result_array();
            if (!empty($orders)) {
                $orders = array_reduce($orders, function ($carry, $item) {
                    $carry[$item['quotes_id']] = $item;
                    return $carry;
                });
            }

            //yêu cầu
            if (!empty($arrQuotationRequestId)) {
                $arrQuotationRequestId = array_unique($arrQuotationRequestId);
                $this->db->select('
                    tblquotation_request.id,
                    tblquotation_request.code as code
                ', false);
                $this->db->from('tblquotation_request');
                $this->db->where_in('tblquotation_request.id', $arrQuotationRequestId, false);
                $quotationRequests = $this->db->get()->result_array();
                if (!empty($quotationRequests)) {
                    $quotationRequests = array_reduce($quotationRequests, function ($carry, $item) {
                        $carry[$item['id']] = $item;
                        return $carry;
                    });
                }
            }

            //Báo cáo không phù hợp
            $this->db->select('
                tblproduction_report.id_quotes as id_quotes,
                tblproduction_report.id as id_production_report,
                tblproduction_report.reference_no as reference_no,
            ', false);
            $this->db->from('tblproduction_report');
            $this->db->where_in('tblproduction_report.id_quotes', $arrQuoteId, false);
            $list_production_report = $this->db->get()->result_array();
            if (!empty($list_production_report)) {
                $list_production_report = array_reduce($list_production_report, function ($carry, $item) {
                    $carry[$item['id_quotes']][] = $item;
                    return $carry;
                });
            }
        }

        $aColumns = handlingColumns($aColumns);
        $stt = 0;
        foreach ($rResult as $key => $aRow) {
            $stt++;
            $customer_id = $aRow['customer_id'];
            $dtCustomer = $listCustomers[$customer_id] ?? null;
            $company_short = $dtCustomer['company_short'] ?? '';
            $customer_name = $dtCustomer['company'] ?? '';
            $name_discount = $dtCustomer['name_discount'] ?? '';
            $brand = $dtCustomer['brand'] ?? '';
            $items = $quoteItems[$aRow['id']] ?? [];
            $order = $orders[$aRow['id']] ?? 0;
            $is_order = !empty($order['is_order']) ? 1 : 0;
            $quotation_request = $quotationRequests[$aRow['quotation_request_id']] ?? null;
            $production_report = $list_production_report[$aRow['id']] ?? null;
            $html_production_report = '';
            if (!empty($production_report)) {
                foreach ($production_report as $kP => $vP) {
                    $html_production_report .= $vP['reference_no']."\n";
                }
            }

            if (!empty($items)) {
                foreach ($items as $kI => $item) {
                    $row = [];

                    $iExcel = -1;
                    $rowBegin++;
                    $is_lot = $item['is_lot'] ?? 0;
                    foreach ($aColumns as $k => $v) {
                        $_data = $aRow[$v];
                        if ($kI != 0 && in_array($v, ['id', 'customer_id', 'customer_name', 'brand', 'date', 'date_finished', 'reference_no', 'name_discount', 'date_status', 'date_updated', 'is_order', 'is_not_order', 'quotation_request_id', 'is_quote_again', 'code_bckph'])) {
                            ++$iExcel;
                        } else {
                            if ($v == 'id') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $stt);
                            } else if ($v == 'customer_id') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $company_short);
                            } else if ($v == 'customer_name') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $customer_name);
                            } else if ($v == 'brand') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $brand);
                            } else if ($v == 'date') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, _dt($_data));
                            } else if ($v == 'date_finished') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, _dt($_data));
                            } else if ($v == 'item_code') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $item['item_code']);
                            } else if ($v == 'item_name') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $item['item_name']);
                            } else if ($v == 'is_lot') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $is_lot == 1 ? 'Theo lô' : '');
                            } else if ($v == 'is_child') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $is_lot == 0 ? 'Theo con' : '');
                            } else if ($v == 'name_discount') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $name_discount);
                            } else if ($v == 'date_status') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data ? _dt($_data) : '');
                            } else if ($v == 'date_updated') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data ? _dt($_data) : '');
                            } else if ($v == 'is_order') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $is_order == 1 ? '1' : '');
                            } else if ($v == 'is_not_order') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $is_order == 0 ? '1' : '');
                            } else if ($v == 'quotation_request_id') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, !empty($quotation_request) ? $quotation_request['code'] : '');
                            } else if ($v == 'is_quote_again') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data ? 'Có' : '');
                            } else if ($v == 'code_bckph') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, strip_tags($html_production_report));
                            } else if ($v == 'reference_no') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $aRow['reference_no']);
                            } else {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data);
                            }
                        }
                    }
                    $output['aaData'][] = $row;
                }
            }
        }

        $objPHPExcel->getActiveSheet()->getStyle('A5:' . $excel[$iExcel] . ($rowBegin))->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        ]);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $excel[$iExcel] . ($rowBegin))->getAlignment()->setWrapText(true);

        foreach ($excel as $key => $value) {
            if ($value == $excel[$iExcel]) {
                break;
            }

            $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(15);
        }

        $filename = 'baocaotonghopbaogiathang' . '.xls';
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
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        echo json_encode($response);
    }

    //Báo cáo tổng hợp PTM/tháng
    public function getSampleDevelopment() {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers_search = $this->input->post('customers_search');

        $aColumns = [
            'tbl_request_template.id as id',
            'tbl_request_template.client_id as customer_id',
            '"" as customer_name',
            'tbl_request_template.reference_no as reference_no',
            '"" as brand',
            '"" as item_code',
            '"" as item_name',
            '"" as date_run_sample',
            '"" as date_finished',
            '"" as date_request_sample',
            '"" as date_approved_sample',
            '"" as date_runs_sample',
            '"" as date_finished_manufactures',
            'tbl_quotes.date as date_quote',
            '"" as date_bom',
            '"" as is_order',
            '"" as is_not_order',
            'tbl_request_template.is_rerun_sample as is_rerun_sample',
            '"" as code_bckph',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_request_template';
        $join = [
            'LEFT JOIN tbl_quotes ON tbl_quotes.id = tbl_request_template.id_quotes',
        ];

        $groupByAndOrderBy = '';
        $where        = [];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tbl_request_template.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tbl_request_template.date <= '$end_date_search'");
        }

        if (!empty($customers_search)) {
            $customers_search = $this->db->escape($customers_search);
            array_push($where, "AND tbl_request_template.customer_id = $customers_search");
        }

        $groupByAndOrderBy = 'ORDER BY tbl_request_template.id DESC';
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_quotes.id as quote_id'], $groupByAndOrderBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];


        if (!empty($rResult)) {
            $arrCustomerId = [];
            $arrRequestTemplateId = [];
            $arrQuoteId = [];
            // $arrQuotationRequestId = [];
            foreach ($rResult as $key => $value) {
                $arrCustomerId[] = $value['customer_id'];
                $arrRequestTemplateId[] = $value['id'];
                $arrQuoteId[] = $value['quote_id'];
            }

            if (!empty($arrCustomerId)) {
                $arrCustomerId = array_unique($arrCustomerId);

                $tbGroupCustomer = '(
                    SELECT
                        tblcustomer_groups.customer_id as customer_id,
                        GROUP_CONCAT(tblcustomers_groups.name) as group_name
                    FROM tblcustomer_groups
                    INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                    WHERE tblcustomer_groups.customer_id IN (' . implode(',', $arrCustomerId) . ')
                    GROUP BY tblcustomer_groups.customer_id
                ) tb_customer_group';

                $this->db->select('
                    tblclients.userid,
                    tblclients.company_short,
                    tblclients.company,
                    tb_customer_group.group_name as brand,
                    tbl_discount.name as name_discount
                ', false);
                $this->db->from('tblclients');
                $this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
                $this->db->join('tbl_discount', 'tbl_discount.id = tblclients.discount_id', 'left');
                $this->db->where_in('tblclients.userid', $arrCustomerId, false);
                $listCustomers = $this->db->get()->result_array();
                if (!empty($listCustomers)) {
                    $listCustomers = array_reduce($listCustomers, function ($carry, $item) {
                        $carry[$item['userid']] = $item;
                        return $carry;
                    });
                }
            }

            //request template items
            $arrProductId = [];
            $this->db->select('
                tbl_request_template_item.request_template_id as request_template_id,
                tbl_request_template_item.items_id as items_id,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tbl_request_template_item.date_run_sample as date_run_sample,
                tbl_request_template_item.date_finished as date_finished,
                tbl_request_template_item.date_request_sample as date_request_sample,
                tbl_request_template_item.date_approved_sample as date_approved_sample,
                tbl_request_template_item.date_runs_sample as date_runs_sample,
                tbl_request_template_item.date_finished_manufactures as date_finished_manufactures,
            ');
            $this->db->from('tbl_request_template_item');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_request_template_item.items_id');
            $this->db->where_in('tbl_request_template_item.request_template_id', $arrRequestTemplateId, false);
            $requestTemplateItems = $this->db->get()->result_array();
            if (!empty($requestTemplateItems)) {
                $requestTemplateItems = array_reduce($requestTemplateItems, function ($carry, $item) use (&$arrProductId) {
                    $carry[$item['request_template_id']][] = $item;
                    $arrProductId[] = $item['items_id'];
                    return $carry;
                });
            }

            //product versions
            if (!empty($arrProductId)) {
                $arrProductId = array_unique($arrProductId);
                $this->db->select('
                    tbl_product_versions.product_id as product_id,
                    MAX(tbl_product_versions.date_updated) as date_updated
                ', false);
                $this->db->from('tbl_product_versions');
                $this->db->where_in('tbl_product_versions.product_id', $arrProductId, false);
                $this->db->group_by('tbl_product_versions.product_id');
                $listProductVersions = $this->db->get()->result_array();
                if (!empty($listProductVersions)) {
                    $listProductVersions = array_reduce($listProductVersions, function ($carry, $item) {
                        $carry[$item['product_id']] = $item;
                        return $carry;
                    });
                }
            }

            //is orders
            if (!empty($arrQuoteId)) {
                $this->db->select('
                    tbl_orders.quotes_id,
                    1 as is_order
                ', false);
                $this->db->from('tbl_orders');
                $this->db->where_in('tbl_orders.quotes_id', $arrQuoteId, false);
                $this->db->group_by('tbl_orders.quotes_id');
                $orders = $this->db->get()->result_array();
                if (!empty($orders)) {
                    $orders = array_reduce($orders, function ($carry, $item) {
                        $carry[$item['quotes_id']] = $item['is_order'];
                        return $carry;
                    });
                }
            }

            //Báo cáo không phù hợp
            if (!empty($arrQuoteId)) {
                $this->db->select('
                    tblproduction_report.id_quotes as id_quotes,
                    tblproduction_report.id as id_production_report,
                    tblproduction_report.reference_no as reference_no,
                ', false);
                $this->db->from('tblproduction_report');
                $this->db->where_in('tblproduction_report.id_quotes', $arrQuoteId, false);
                $list_production_report = $this->db->get()->result_array();
                if (!empty($list_production_report)) {
                    $list_production_report = array_reduce($list_production_report, function ($carry, $item) {
                        $carry[$item['id_quotes']][] = $item;
                        return $carry;
                    });
                }
            }

        }

        $aColumns = handlingColumns($aColumns);
        $stt = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $stt++;
            $customer_id = $aRow['customer_id'];
            $dtCustomer = $listCustomers[$customer_id] ?? null;
            $company_short = $dtCustomer['company_short'] ?? '';
            $customer_name = $dtCustomer['company'] ?? '';
            $name_discount = $dtCustomer['name_discount'] ?? '';
            $brand = $dtCustomer['brand'] ?? '';
            $items = $requestTemplateItems[$aRow['id']] ?? [];
            $order = $orders[$aRow['id']] ?? 0;
            $is_order = !empty($order['is_order']) ? 1 : 0;

            $product_version = $listProductVersions[$aRow['id']] ?? null;
            $date_bom = !empty($product_version['date_updated']) ? _d($product_version['date_updated']) : '';

            $quotation_request = $quotationRequests[$aRow['quotation_request_id'] ?? null] ?? null;
            $production_report = $list_production_report[$aRow['id']] ?? null;
            $html_production_report = '';
            if (!empty($production_report)) {
                foreach ($production_report as $kP => $vP) {
                    $html_production_report .= '<div><a class="c_modal" href="' . base_url('admin/production_report/modal/' . $vP['id_production_report']) . '">' . $vP['reference_no'] . '</a></div>';
                }
            }

            if (!empty($items)) {
                foreach ($items as $kI => $item) {
                    $row = [];

                    foreach ($aColumns as $k => $v) {
                        $_data = $aRow[$v];
                        if ($kI != 0 && in_array($v, ['id', 'customer_id', 'customer_name', 'reference_no', 'brand', 'date_quote', 'is_order', 'is_not_order', 'is_rerun_sample', 'code_bckph'])) {
                            $row[] = '';
                        } else {
                            if ($v == 'id') {
                                $row[] = '<div class="text-center">' . $stt . '</div>';
                            } else if ($v == 'customer_id') {
                                $row[] = '<a target="_blank" href="' . base_url('admin/clients/client/' . $aRow['customer_id'] . '?view') . '">' . $company_short . '</a>';
                            } else if ($v == 'customer_name') {
                                $row[] = $customer_name;
                            } else if ($v == 'brand') {
                                $row[] = $brand;
                            } else if ($v == 'date_run_sample') {
                                $row[] = $item['date_run_sample'] ? _d($item['date_run_sample']) : '';
                            } else if ($v == 'date_finished') {
                                $row[] = $item['date_finished'] ? _d($item['date_finished']) : '';
                            } else if ($v == 'date_request_sample') {
                                $row[] = $item['date_request_sample'] ? _d($item['date_request_sample']) : '';
                            } else if ($v == 'date_approved_sample') {
                                $row[] = $item['date_approved_sample'] ? _d($item['date_approved_sample']) : '';
                            } else if ($v == 'date_runs_sample') {
                                $row[] = $item['date_runs_sample'] ? _d($item['date_runs_sample']) : '';
                            } else if ($v == 'date_finished_manufactures') {
                                $row[] = $item['date_finished_manufactures'] ? _d($item['date_finished_manufactures']) : '';
                            } else if ($v == 'item_code') {
                                $row[] = $item['item_code'];
                            } else if ($v == 'item_name') {
                                $row[] = '<div style="width: 120px; word-break: break-all;">' . $item['item_name'] . '</div>';
                            } else if ($v == 'date_quote') {
                                $row[] = $_data ? _dt($_data) : '';
                            } else if ($v == 'is_order') {
                                $row[] = $is_order == 1 ? '1' : '';
                            } else if ($v == 'is_not_order') {
                                $row[] = $is_order == 0 ? '1' : '';
                            } else if ($v == 'code_bckph') {
                                $row[] = $html_production_report;
                            } else if ($v == 'reference_no') {
                                $row[] = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_template/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a>';
                            } else if ($v == 'date_bom') {
                                $row[] = $date_bom;
                            } else if ($v == 'is_rerun_sample') {
                                $row[] = $_data == 1 ? '1' : '';
                            } else {
                                $row[] = $_data;
                            }
                        }
                    }
                    $output['aaData'][] = $row;
                }
            }
        }

        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function exportExcelDevelopment() {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers_search = $this->input->post('customers_search');
        if (empty($start_date_search) || empty($end_date_search)) {
            $response = array(
                'result' => 0,
                'message' => lang('Vui lòng nhập ngày bắt đầu và kết thúc'),
            );
            echo json_encode($response);
            return;
        }

        $excel = cloumns_excel();
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $excel = cloumns_excel();
        insertCompanyInfo($objPHPExcel, 'C1:P2');

        $rowBegin = 5;
        $iExcel = -1;
        
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('STT'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã Khách Hàng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tên Khách Hàng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Phiếu Yêu Cầu PTM'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã Brand'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã Sản Phẩm'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tên Sản Phẩm'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Chạy Mẫu'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Hoàn Thành Mẫu'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Gửi Mẫu'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Duyệt Mẫu'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Chạy Hàng Lấy Mẫu'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Hoàn Thành Mẫu SX'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Duyệt Báo Giá'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Cập Nhật BOM Foso'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Có Đơn Hàng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Không Có Đơn Hàng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Chạy Mẫu Lại'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã BCKPH'));

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $excel[$iExcel] . $rowBegin)->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $aColumns = [
            'tbl_request_template.id as id',
            'tbl_request_template.client_id as customer_id',
            '"" as customer_name',
            'tbl_request_template.reference_no as reference_no',
            '"" as brand',
            '"" as item_code',
            '"" as item_name',
            '"" as date_run_sample',
            '"" as date_finished',
            '"" as date_request_sample',
            '"" as date_approved_sample',
            '"" as date_runs_sample',
            '"" as date_finished_manufactures',
            'tbl_quotes.date as date_quote',
            '"" as date_bom',
            '"" as is_order',
            '"" as is_not_order',
            'tbl_request_template.is_rerun_sample as is_rerun_sample',
            '"" as code_bckph',
        ];
        
        // Thiết lập dữ liệu truy vấn
        $sIndexColumn = 'id';
        $sTable       = 'tbl_request_template';
        $join = [
            'LEFT JOIN tbl_quotes ON tbl_quotes.id = tbl_request_template.id_quotes',
        ];
        
        $groupByAndOrderBy = '';
        $where        = [];
        
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tbl_request_template.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tbl_request_template.date <= '$end_date_search'");
        }

        if (!empty($customers_search)) {
            $customers_search = $this->db->escape($customers_search);
            array_push($where, "AND tbl_request_template.customer_id = $customers_search");
        }
        
        $groupByAndOrderBy = 'ORDER BY tbl_request_template.id DESC';
        
        // Thực hiện truy vấn
        $query = "SELECT tbl_quotes.id as quote_id, " . implode(', ', $aColumns) . " FROM $sTable " . implode(' ', $join) . " WHERE 1 " . implode(' ', $where) . " $groupByAndOrderBy";
        $rResult = $this->db->query($query)->result_array();
        if (!empty($rResult)) {
            $arrCustomerId = [];
            $arrRequestTemplateId = [];
            $arrQuoteId = [];
            // $arrQuotationRequestId = [];
            foreach ($rResult as $key => $value) {
                $arrCustomerId[] = $value['customer_id'];
                $arrRequestTemplateId[] = $value['id'];
                $arrQuoteId[] = $value['quote_id'];
            }

            if (!empty($arrCustomerId)) {
                $arrCustomerId = array_unique($arrCustomerId);

                $tbGroupCustomer = '(
                    SELECT
                        tblcustomer_groups.customer_id as customer_id,
                        GROUP_CONCAT(tblcustomers_groups.name) as group_name
                    FROM tblcustomer_groups
                    INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                    WHERE tblcustomer_groups.customer_id IN (' . implode(',', $arrCustomerId) . ')
                    GROUP BY tblcustomer_groups.customer_id
                ) tb_customer_group';

                $this->db->select('
                    tblclients.userid,
                    tblclients.company_short,
                    tblclients.company,
                    tb_customer_group.group_name as brand,
                    tbl_discount.name as name_discount
                ', false);
                $this->db->from('tblclients');
                $this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
                $this->db->join('tbl_discount', 'tbl_discount.id = tblclients.discount_id', 'left');
                $this->db->where_in('tblclients.userid', $arrCustomerId, false);
                $listCustomers = $this->db->get()->result_array();
                if (!empty($listCustomers)) {
                    $listCustomers = array_reduce($listCustomers, function ($carry, $item) {
                        $carry[$item['userid']] = $item;
                        return $carry;
                    });
                }
            }

            //request template items
            $arrProductId = [];
            $this->db->select('
                tbl_request_template_item.request_template_id as request_template_id,
                tbl_request_template_item.items_id as items_id,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tbl_request_template_item.date_run_sample as date_run_sample,
                tbl_request_template_item.date_finished as date_finished,
                tbl_request_template_item.date_request_sample as date_request_sample,
                tbl_request_template_item.date_approved_sample as date_approved_sample,
                tbl_request_template_item.date_runs_sample as date_runs_sample,
                tbl_request_template_item.date_finished_manufactures as date_finished_manufactures,
            ');
            $this->db->from('tbl_request_template_item');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_request_template_item.items_id');
            $this->db->where_in('tbl_request_template_item.request_template_id', $arrRequestTemplateId, false);
            $requestTemplateItems = $this->db->get()->result_array();
            if (!empty($requestTemplateItems)) {
                $requestTemplateItems = array_reduce($requestTemplateItems, function ($carry, $item) use (&$arrProductId) {
                    $carry[$item['request_template_id']][] = $item;
                    $arrProductId[] = $item['items_id'];
                    return $carry;
                });
            }

            //product versions
            if (!empty($arrProductId)) {
                $arrProductId = array_unique($arrProductId);
                $this->db->select('
                    tbl_product_versions.product_id as product_id,
                    MAX(tbl_product_versions.date_updated) as date_updated
                ', false);
                $this->db->from('tbl_product_versions');
                $this->db->where_in('tbl_product_versions.product_id', $arrProductId, false);
                $this->db->group_by('tbl_product_versions.product_id');
                $listProductVersions = $this->db->get()->result_array();
                if (!empty($listProductVersions)) {
                    $listProductVersions = array_reduce($listProductVersions, function ($carry, $item) {
                        $carry[$item['product_id']] = $item;
                        return $carry;
                    });
                }
            }

            //is orders
            if (!empty($arrQuoteId)) {
                $this->db->select('
                    tbl_orders.quotes_id,
                    1 as is_order
                ', false);
                $this->db->from('tbl_orders');
                $this->db->where_in('tbl_orders.quotes_id', $arrQuoteId, false);
                $this->db->group_by('tbl_orders.quotes_id');
                $orders = $this->db->get()->result_array();
                if (!empty($orders)) {
                    $orders = array_reduce($orders, function ($carry, $item) {
                        $carry[$item['quotes_id']] = $item['is_order'];
                        return $carry;
                    });
                }
            }

            //Báo cáo không phù hợp
            if (!empty($arrQuoteId)) {
                $this->db->select('
                    tblproduction_report.id_quotes as id_quotes,
                    tblproduction_report.id as id_production_report,
                    tblproduction_report.reference_no as reference_no,
                ', false);
                $this->db->from('tblproduction_report');
                $this->db->where_in('tblproduction_report.id_quotes', $arrQuoteId, false);
                $list_production_report = $this->db->get()->result_array();
                if (!empty($list_production_report)) {
                    $list_production_report = array_reduce($list_production_report, function ($carry, $item) {
                        $carry[$item['id_quotes']][] = $item;
                        return $carry;
                    });
                }
            }
        }

        $aColumns = handlingColumns($aColumns);
        $stt = 0;
        foreach ($rResult as $key => $aRow) {
            $stt++;
            $customer_id = $aRow['customer_id'];
            $dtCustomer = $listCustomers[$customer_id] ?? null;
            $company_short = $dtCustomer['company_short'] ?? '';
            $customer_name = $dtCustomer['company'] ?? '';
            $name_discount = $dtCustomer['name_discount'] ?? '';
            $brand = $dtCustomer['brand'] ?? '';
            $items = $requestTemplateItems[$aRow['id']] ?? [];
            $order = $orders[$aRow['id']] ?? 0;
            $is_order = !empty($order['is_order']) ? 1 : 0;

            $product_version = $listProductVersions[$aRow['id']] ?? null;
            $date_bom = !empty($product_version['date_updated']) ? _d($product_version['date_updated']) : '';

            $quotation_request = $quotationRequests[$aRow['quotation_request_id'] ?? null] ?? null;
            $production_report = $list_production_report[$aRow['id']] ?? null;
            $html_production_report = '';
            if (!empty($production_report)) {
                foreach ($production_report as $kP => $vP) {
                    $html_production_report .= $vP['reference_no']."\n";
                }
            }

            if (!empty($items)) {
                foreach ($items as $kI => $item) {
                    $row = [];

                    $iExcel = -1;
                    $rowBegin++;
                    foreach ($aColumns as $k => $v) {
                        $_data = $aRow[$v];
                        if ($kI != 0 && in_array($v, ['id', 'customer_id', 'customer_name', 'reference_no', 'brand', 'date_quote', 'is_order', 'is_not_order', 'is_rerun_sample', 'code_bckph'])) {
                            ++$iExcel;
                        } else {
                            if ($v == 'id') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $stt);
                            } else if ($v == 'customer_id') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $company_short);
                            } else if ($v == 'customer_name') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $customer_name);
                            } else if ($v == 'brand') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $brand);
                            } else if ($v == 'date_run_sample') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $item['date_run_sample'] ? _d($item['date_run_sample']) : '');
                            } else if ($v == 'date_finished') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $item['date_finished'] ? _d($item['date_finished']) : '');
                            } else if ($v == 'date_request_sample') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $item['date_request_sample'] ? _d($item['date_request_sample']) : '');
                            } else if ($v == 'date_approved_sample') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $item['date_approved_sample'] ? _d($item['date_approved_sample']) : '');
                            } else if ($v == 'date_runs_sample') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $item['date_runs_sample'] ? _d($item['date_runs_sample']) : '');
                            } else if ($v == 'date_finished_manufactures') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $item['date_finished_manufactures'] ? _d($item['date_finished_manufactures']) : '');
                            } else if ($v == 'item_code') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $item['item_code']);
                            } else if ($v == 'item_name') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $item['item_name']);
                            } else if ($v == 'date_quote') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data ? _dt($_data) : '');
                            } else if ($v == 'is_order') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $is_order == 1 ? '1' : '');
                            } else if ($v == 'is_not_order') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $is_order == 0 ? '1' : '');
                            } else if ($v == 'code_bckph') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $html_production_report);
                            } else if ($v == 'reference_no') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $aRow['reference_no']);
                            } else if ($v == 'date_bom') {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $date_bom);
                            } else if ($v == 'is_rerun_sample') {
                                $row[] = $_data == 1 ? '1' : '';
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data == 1 ? '1' : '');
                            } else {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data);
                            }
                        }
                    }
                    $output['aaData'][] = $row;
                }
            }
        }

        $objPHPExcel->getActiveSheet()->getStyle('A5:' . $excel[$iExcel] . ($rowBegin))->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        ]);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $excel[$iExcel] . ($rowBegin))->getAlignment()->setWrapText(true);

        foreach ($excel as $key => $value) {
            if ($value == $excel[$iExcel]) {
                break;
            }

            $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(15);
        }

        $filename = 'baocaotonghopptm/thang' . '.xls';
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
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        echo json_encode($response);
    }

    //Báo cáo tổng hợp thu/tháng
    public function getOrders() {
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $customers_search = $this->input->post('customers_search');
        $orders_search = $this->input->post('orders_search');

        $aColumns = [
            'tbl_orders.id as id',
            'tbl_orders.customer_id as customer_id',
            '"" as customer_name',
            'tbl_orders.reference_no as reference_no',
            '"" as item_code',
            '"" as item_name',
            '"" as quantity',
            '"" as reference_no_delivery',
            '"" as date_delivery',
            '"" as date_finished_delivery',
            '"" as reference_no_voucher',
            '"" as reference_no_customs',
            '"" as date_customs',
            '"" as reference_no_invoice',
            '"" as vat',
            '"" as grand_total',
            '"" as number_date_debt_collection', //số ngày thu nợ
            '"" as date_debt_collection',
            '"" as report_nh',
            '"" as date_report_nh',
            '"" as code_receipt',
            '"" as amount_collected',
            '"" as amount_remaining', //số tiền còn lại
            '"" as date_misa',
            '"" as date_foso',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_orders';
        $join = [
        ];

        $groupByAndOrderBy = '';
        $where        = [];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tbl_orders.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tbl_orders.date <= '$end_date_search'");
        }

        if (!empty($customers_search)) {
            $customers_search = explode('__',$customers_search);
            $customers_search = $this->db->escape($customers_search[1]);
            array_push($where, "AND tbl_orders.customer_id = $customers_search");
        }

        if (!empty($orders_search)){
            array_push($where,'AND tbl_orders.id = '.$orders_search.'');
        }

        $groupByAndOrderBy = 'ORDER BY tbl_orders.id DESC';
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], $groupByAndOrderBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];


        if (!empty($rResult)) {
            $arrCustomerId = [];
            $arrOrderId = [];
            foreach ($rResult as $key => $value) {
                $arrCustomerId[] = $value['customer_id'];
                $arrOrderId[] = $value['id'];
            }

            if (!empty($arrCustomerId)) {
                $arrCustomerId = array_unique($arrCustomerId);

                $tbGroupCustomer = '(
                    SELECT
                        tblcustomer_groups.customer_id as customer_id,
                        GROUP_CONCAT(tblcustomers_groups.name) as group_name
                    FROM tblcustomer_groups
                    INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                    WHERE tblcustomer_groups.customer_id IN (' . implode(',', $arrCustomerId) . ')
                    GROUP BY tblcustomer_groups.customer_id
                ) tb_customer_group';
                // tb_customer_group.group_name as brand,
                // tbl_discount.name as name_discount
                $this->db->select('
                    tblclients.userid,
                    tblclients.company_short,
                    tblclients.company,
                    tblclients.time_payment,
                    
                ', false);
                $this->db->from('tblclients');
                // $this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
                // $this->db->join('tbl_discount', 'tbl_discount.id = tblclients.discount_id', 'left');
                $this->db->where_in('tblclients.userid', $arrCustomerId, false);
                $listCustomers = $this->db->get()->result_array();
                if (!empty($listCustomers)) {
                    $listCustomers = array_reduce($listCustomers, function ($carry, $item) {
                        $carry[$item['userid']] = $item;
                        return $carry;
                    });
                }
            }

            if (!empty($arrOrderId)) {
                $arrOrderId = array_unique($arrOrderId);
                // SUM(IF((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) 
                $this->db->select('
                    tbl_order_items.order_id as order_id,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    (tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) as quantity
                ');
                $this->db->from('tbl_order_items');
                $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id', 'left');
                $this->db->where_in('tbl_order_items.order_id', $arrOrderId, false);
                $listOrderItems = $this->db->get()->result_array();
                if (!empty($listOrderItems)) {
                    $listOrderItems = array_reduce($listOrderItems, function ($carry, $item) {
                        $carry[$item['order_id']][] = $item;
                        return $carry;
                    });
                }

                //delivery
                $arrDeliveryId = [];
                $this->db->select('
                    tbl_deliveries.order_id as order_id,
                    tbl_deliveries.id as id,
                    tbl_deliveries.date,
                    tbl_deliveries.code_custom,
                    tbl_deliveries.date_custom,
                    tbl_deliveries.reference_no,
                    tbl_deliveries.date_warehouseman
                ');
                $this->db->from('tbl_deliveries');
                $this->db->where_in('tbl_deliveries.order_id', $arrOrderId, false);
                $listDelivery = $this->db->get()->result_array();
                if (!empty($listDelivery)) {
                    $listDelivery = array_reduce($listDelivery, function ($carry, $item) use (&$arrDeliveryId) {
                        $carry[$item['order_id']][] = $item;
                        $arrDeliveryId[] = $item['id'];
                        return $carry;
                    });
                }

                //invoice hóa đơn
                if (!empty($arrDeliveryId)) {
                    $arrInvoiceId = [];
                    $this->db->select('
                        tbl_invoices.id as id,
                        tbl_invoices.date as date,
                        tbl_invoices.date_misa as date_misa,
                        tbl_invoice_items.object_id as object_id,
                        tbl_invoices.reference_no as reference_no,
                        tbl_invoices.tax_rate as tax_rate,
                        tbl_invoice_items.grand_total_item as grand_total_item,
                    ', false);
                    $this->db->from('tbl_invoices');
                    $this->db->join('tbl_invoice_items', 'tbl_invoice_items.invoice_id = tbl_invoices.id', 'inner');
                    $this->db->where_in('tbl_invoice_items.object_id', $arrDeliveryId, false);
                    $listInvoice = $this->db->get()->result_array();
                    if (!empty($listInvoice)) {
                        $listInvoiceTotal = array_reduce($listInvoice, function ($carry, $item) {
                            if (isset($carry[$item['object_id']])) {
                                $carry[$item['object_id']] += $item['grand_total_item'];
                            } else {
                                $carry[$item['object_id']] = $item['grand_total_item'];
                            }
                            return $carry;
                        });
                        $listInvoice = array_reduce($listInvoice, function ($carry, $item) use (&$arrInvoiceId) {
                            $carry[$item['object_id']][] = $item;
                            $arrInvoiceId[] = $item['id'];
                            return $carry;
                        });
                    }

                     if (!empty($arrInvoiceId)) {
                         $arrInvoiceId = array_unique($arrInvoiceId);
                         $this->db->select('
                             tblvouchers_coupon.id as id,
                             tblvouchers_coupon.code_bank as code_bank,
                             tblvouchers_coupon.date_bank as date_bank,
                             tblvouchers_coupon_detal.id_order as object_id,
                             tblvouchers_coupon.code_vouchers as reference_no,
                             tblvouchers_coupon_detal.payment as payment,
                             tbl_deliveries.order_id as order_id,
                         ', false);
                         $this->db->from('tblvouchers_coupon');
                         $this->db->join('tblvouchers_coupon_detal', 'tblvouchers_coupon_detal.id_vouchers = tblvouchers_coupon.id', 'inner');
                         $this->db->join('tbl_invoice_items', 'tbl_invoice_items.invoice_id = tblvouchers_coupon_detal.id_order', 'inner');
                         $this->db->join('tbl_deliveries', 'tbl_deliveries.id = tbl_invoice_items.object_id', 'inner');
                         $this->db->where_in('tblvouchers_coupon_detal.id_order', $arrInvoiceId, false);
                         $this->db->group_by('tblvouchers_coupon.id, tbl_deliveries.order_id');
                         $listVoucher = $this->db->get()->result_array();
                         if (!empty($listVoucher)) {
                             $listVoucherTotal = array_reduce($listVoucher, function ($carry, $item) {
                                 if (isset($carry[$item['order_id']])) {
                                     $carry[$item['order_id']] += $item['payment'];
                                 } else {
                                     $carry[$item['order_id']] = $item['payment'];
                                 }
                                 return $carry;
                             });
                             $listVoucher = array_reduce($listVoucher, function ($carry, $item) {
                                 $carry[$item['order_id']][] = $item;
                                 return $carry;
                             });
                         }
                     }
                }
            }
        }
        $aColumns = handlingColumns($aColumns);
        $stt = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $stt++;
            $customer_id = $aRow['customer_id'];
            $dtCustomer = $listCustomers[$customer_id] ?? null;
            $company_short = $dtCustomer['company_short'] ?? '';
            $customer_name = $dtCustomer['company'] ?? '';
            $time_payment = $dtCustomer['time_payment'] ?? 0;
            $name_discount = $dtCustomer['name_discount'] ?? '';
            $brand = $dtCustomer['brand'] ?? '';
            $items = $listOrderItems[$aRow['id']] ?? [];
            $deliveries = $listDelivery[$aRow['id']] ?? [];
            $vouchers = $listVoucher[$aRow['id']] ?? [];
            $invoiceTotal = 0;
            $voucherTotal = $listVoucherTotal[$aRow['id']] ?? 0;
            if (!empty($deliveries)){
                foreach ($deliveries as $k => $v){
                    $total = $listInvoiceTotal[$v['id']] ?? 0;
                    $invoiceTotal += $total;
                }
            }
            $maxCount = count($items);
            if ($maxCount) {
                for ($i = 0; $i < $maxCount; $i++) {
                    $item = $items[$i] ?? [];
                    $row = [];
                    foreach ($aColumns as $k => $v) {
                        $_data = $aRow[$v];
                        if ($i != 0 && in_array($v, ['id', 'customer_id', 'customer_name', 'reference_no'])) {
                            $row[] = '';
                        } else {
                            if ($v == 'id') {
                                $row[] = '<div class="text-center">' . $stt . '</div>';
                            } else if ($v == 'customer_id') {
                                $row[] = '<a target="_blank" href="' . base_url('admin/clients/client/' . $aRow['customer_id'] . '?view') . '">' . $company_short . '</a>';
                            } else if ($v == 'customer_name') {
                                $row[] = $customer_name;
                            } else if ($v == 'reference_no') {
                                $row[] = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/view_order/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a>';
                            } else if ($v == 'item_code') {
                                $row[] = $item['item_code'] ?? '';
                            } else if ($v == 'item_name') {
                                $row[] = $item['item_name'] ?? '';
                            } else if ($v == 'quantity') {
                                $row[] = !empty($item['quantity']) && $item['quantity'] <= 5000 ? '<div class="text-center">'.formatNumber($item['quantity']).'</div>' : '';
                            } else if ($v == 'grand_total') {
                                $row[] = !empty($invoiceTotal) ? '<div class="text-right">'.formatMoney($invoiceTotal).'</div>' : '';
                            } else if ($v == 'number_date_debt_collection') {
                                $row[] = $time_payment;
                            } else if ($v == 'amount_collected') {
                                $row[] = !empty($voucherTotal) ? '<div class="text-right">'.formatMoney($voucherTotal).'</div>' : '';
                            } else if ($v == 'amount_remaining') {
                                $amount_remaining = $invoiceTotal - $voucherTotal;
                                $row[] = !empty($amount_remaining) ? '<div class="text-right">'.formatMoney($amount_remaining).'</div>' : '';
                            } else {
                                $row[] = $_data;
                            }
                        }
                    }
                    $output['aaData'][] = $row;

                    $maxCountDelivery = max(count($deliveries),count($vouchers));
                    if ($maxCountDelivery){
                        for ($i = 0;$i < $maxCountDelivery;$i++){
                            $delivery = $deliveries[$i] ?? [];
                            $voucher = $vouchers[$i] ?? [];
                            $invoice = [];
                            if (!empty($delivery)) {
                                // chi có 1 hóa đơn
                                $invoices = $listInvoice[$delivery['id']] ?? [];
                                $invoice = $invoices[0] ?? [];
                            }
                            $date_invoice = !empty($invoice) ? $invoice['date'] : '';
                            $date_invoice_new = null;
                            if (!empty($date_invoice)) {
                                $date_invoice_new = date('Y-m-d',
                                    strtotime('first day of next month', strtotime($date_invoice)));
                                $date_invoice_new = date('Y-m-d', strtotime('+'.$time_payment.' days', strtotime($date_invoice_new)));
                            }
                            $row = [];
                            foreach ($aColumns as $k => $v) {
                                $_data = $aRow[$v];
                                if ($v == 'reference_no_delivery') {
                                    $row[] = !empty($delivery) ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/releases/view_delivery/' . $delivery['id']) . '" data-toggle="modal" data-target="#myModal">' . $delivery['reference_no'] . '</a>' : '';
                                } else if ($v == 'date_delivery') {
                                    $row[] = !empty($delivery) ? _dt($delivery['date']) : '';
                                } else if ($v == 'date_finished_delivery') {
                                    $row[] = !empty($delivery) ? _dt($delivery['date_warehouseman']) : '';
                                } else if ($v == 'reference_no_invoice') {
                                    $row[] = !empty($invoice) ? ($invoice['reference_no']) : '';
                                } else if ($v == 'vat') {
                                    $row[] = !empty($invoice) ? ($invoice['tax_rate']) : '';
                                } else if ($v == 'grand_total') {
                                    $row[] = !empty($invoice) ? '<div class="text-right">'.formatMoney($invoice['grand_total_item']).'</div>' : '';
                                } else if ($v == 'reference_no_voucher') {
                                    $row[] = !empty($voucher) ? '<div class="text-left" style="min-width: 100px">'.$voucher['reference_no'].'</div>' : '';
                                } else if ($v == 'code_receipt') {
                                    $row[] = !empty($voucher) ? '<div class="text-left" style="min-width: 100px">'.$voucher['reference_no'].'</div>' : '';
                                } else if ($v == 'amount_collected') {
                                    $row[] = !empty($voucher) ? '<div class="text-right">'.formatMoney($voucher['payment']).'</div>' : '';
                                } else if ($v == 'date_debt_collection') {
                                    $row[] = '<div style="min-width: 100px">'.(!empty($date_invoice_new) ? _dhau($date_invoice_new) : '').'</div>';
                                } else if ($v == 'date_foso') {
                                    $row[] = '<div style="min-width: 100px">'.(!empty($date_invoice_new) ? _dhau($date_invoice_new) : '').'</div>';
                                } else if ($v == 'date_misa') {
                                    $row[] = '<div style="min-width: 100px">'.(!empty($invoice['date_misa']) ? _dhau($invoice['date_misa']) : '').'</div>';
                                } else if ($v == 'date_customs') {
                                    $row[] = '<div style="min-width: 100px">'.(!empty($delivery['date_custom']) ? _dhau($delivery['date_custom']) : '').'</div>';
                                } else if ($v == 'reference_no_customs') {
                                    $row[] = !empty($delivery) ? '<div class="text-left" style="min-width: 100px">'.($delivery['code_custom']).'</div>' : '';
                                } else if ($v == 'report_nh') {
                                    $row[] = !empty($voucher) ? '<div class="text-left" style="min-width: 100px">'.($voucher['code_bank']).'</div>' : '';
                                } else if ($v == 'date_report_nh') {
                                    $row[] = '<div style="min-width: 100px">'.(!empty($voucher['date_bank']) ? _dhau($voucher['date_bank']) : '').'</div>';
                                } else {
                                    $row[] = '';
                                }
                            }
                            $output['aaData'][] = $row;
                        }
                    }

                }
            }
        }

        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function exportExcelOrders()
    {
        $columsExcel = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
        ];
        if ($this->input->post()) {

            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_ch/bao_cao_tong_hop_thu_thang.xlsx';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $BStylenumber = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '111112'),
                    'size'  => 11,
                    'name'  => 'Times New Roman'
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            );
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $customers_search = $this->input->post('customers_search');
            $orders_search = $this->input->post('orders_search');
            $row = 2;
            $staff_id = get_staff_user_id();
            $quantityInventory = "(
                SELECT
                    SUM(tblwarehouse_items.product_quantity)
                FROM tblwarehouse_items
                INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0
            )";
            $this->db->select(
                'tbl_orders.id as id,
                tbl_orders.customer_id as customer_id,
                "" as customer_name,
                tbl_orders.reference_no as reference_no,
            ');
            $this->db->from('tbl_orders');
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search);
                $this->db->where("tbl_orders.date >= '$start_date_search'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search);
                $this->db->where("tbl_orders.date <= '$end_date_search'");
            }

            if (!empty($customers_search)) {
                $customers_search = explode('__',$customers_search);
                $customers_search = $this->db->escape($customers_search[1]);
                $this->db->where("tbl_orders.customer_id = $customers_search");
            }

            if (!empty($orders_search)){
                $this->db->where('tbl_orders.id = '.$orders_search.'');
            }
            $this->db->order_by('tbl_orders.id desc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            if (!empty($items)) {
                $arrCustomerId = [];
                $arrOrderId = [];
                foreach ($items as $key => $value) {
                    $arrCustomerId[] = $value['customer_id'];
                    $arrOrderId[] = $value['id'];
                }

                if (!empty($arrCustomerId)) {
                    $arrCustomerId = array_unique($arrCustomerId);

                    $tbGroupCustomer = '(
                    SELECT
                        tblcustomer_groups.customer_id as customer_id,
                        GROUP_CONCAT(tblcustomers_groups.name) as group_name
                    FROM tblcustomer_groups
                    INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                    WHERE tblcustomer_groups.customer_id IN (' . implode(',', $arrCustomerId) . ')
                    GROUP BY tblcustomer_groups.customer_id
                ) tb_customer_group';
                    // tb_customer_group.group_name as brand,
                    // tbl_discount.name as name_discount
                    $this->db->select('
                    tblclients.userid,
                    tblclients.company_short,
                    tblclients.company,
                    tblclients.time_payment,
                    
                ', false);
                    $this->db->from('tblclients');
                    // $this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
                    // $this->db->join('tbl_discount', 'tbl_discount.id = tblclients.discount_id', 'left');
                    $this->db->where_in('tblclients.userid', $arrCustomerId, false);
                    $listCustomers = $this->db->get()->result_array();
                    if (!empty($listCustomers)) {
                        $listCustomers = array_reduce($listCustomers, function ($carry, $item) {
                            $carry[$item['userid']] = $item;
                            return $carry;
                        });
                    }
                }

                if (!empty($arrOrderId)) {
                    $arrOrderId = array_unique($arrOrderId);
                    // SUM(IF((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1))
                    $this->db->select('
                    tbl_order_items.order_id as order_id,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    (tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) as quantity
                ');
                    $this->db->from('tbl_order_items');
                    $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id', 'left');
                    $this->db->where_in('tbl_order_items.order_id', $arrOrderId, false);
                    $listOrderItems = $this->db->get()->result_array();
                    if (!empty($listOrderItems)) {
                        $listOrderItems = array_reduce($listOrderItems, function ($carry, $item) {
                            $carry[$item['order_id']][] = $item;
                            return $carry;
                        });
                    }

                    //delivery
                    $arrDeliveryId = [];
                    $this->db->select('
                    tbl_deliveries.order_id as order_id,
                    tbl_deliveries.id as id,
                    tbl_deliveries.date,
                    tbl_deliveries.code_custom,
                    tbl_deliveries.date_custom,
                    tbl_deliveries.reference_no,
                    tbl_deliveries.date_warehouseman
                ');
                    $this->db->from('tbl_deliveries');
                    $this->db->where_in('tbl_deliveries.order_id', $arrOrderId, false);
                    $listDelivery = $this->db->get()->result_array();
                    if (!empty($listDelivery)) {
                        $listDelivery = array_reduce($listDelivery, function ($carry, $item) use (&$arrDeliveryId) {
                            $carry[$item['order_id']][] = $item;
                            $arrDeliveryId[] = $item['id'];
                            return $carry;
                        });
                    }

                    //invoice hóa đơn
                    if (!empty($arrDeliveryId)) {
                        $arrInvoiceId = [];
                        $this->db->select('
                        tbl_invoices.id as id,
                        tbl_invoices.date as date,
                        tbl_invoices.date_misa as date_misa,
                        tbl_invoice_items.object_id as object_id,
                        tbl_invoices.reference_no as reference_no,
                        tbl_invoices.tax_rate as tax_rate,
                        tbl_invoice_items.grand_total_item as grand_total_item,
                    ', false);
                        $this->db->from('tbl_invoices');
                        $this->db->join('tbl_invoice_items', 'tbl_invoice_items.invoice_id = tbl_invoices.id', 'inner');
                        $this->db->where_in('tbl_invoice_items.object_id', $arrDeliveryId, false);
                        $listInvoice = $this->db->get()->result_array();
                        if (!empty($listInvoice)) {
                            $listInvoiceTotal = array_reduce($listInvoice, function ($carry, $item) {
                                if (isset($carry[$item['object_id']])) {
                                    $carry[$item['object_id']] += $item['grand_total_item'];
                                } else {
                                    $carry[$item['object_id']] = $item['grand_total_item'];
                                }
                                return $carry;
                            });
                            $listInvoice = array_reduce($listInvoice, function ($carry, $item) use (&$arrInvoiceId) {
                                $carry[$item['object_id']][] = $item;
                                $arrInvoiceId[] = $item['id'];
                                return $carry;
                            });
                        }

                        if (!empty($arrInvoiceId)) {
                            $arrInvoiceId = array_unique($arrInvoiceId);
                            $this->db->select('
                             tblvouchers_coupon.id as id,
                             tblvouchers_coupon.code_bank as code_bank,
                             tblvouchers_coupon.date_bank as date_bank,
                             tblvouchers_coupon_detal.id_order as object_id,
                             tblvouchers_coupon.code_vouchers as reference_no,
                             tblvouchers_coupon_detal.payment as payment,
                             tbl_deliveries.order_id as order_id,
                         ', false);
                            $this->db->from('tblvouchers_coupon');
                            $this->db->join('tblvouchers_coupon_detal', 'tblvouchers_coupon_detal.id_vouchers = tblvouchers_coupon.id', 'inner');
                            $this->db->join('tbl_invoice_items', 'tbl_invoice_items.invoice_id = tblvouchers_coupon_detal.id_order', 'inner');
                            $this->db->join('tbl_deliveries', 'tbl_deliveries.id = tbl_invoice_items.object_id', 'inner');
                            $this->db->where_in('tblvouchers_coupon_detal.id_order', $arrInvoiceId, false);
                            $this->db->group_by('tblvouchers_coupon.id, tbl_deliveries.order_id');
                            $listVoucher = $this->db->get()->result_array();
                            if (!empty($listVoucher)) {
                                $listVoucherTotal = array_reduce($listVoucher, function ($carry, $item) {
                                    if (isset($carry[$item['order_id']])) {
                                        $carry[$item['order_id']] += $item['payment'];
                                    } else {
                                        $carry[$item['order_id']] = $item['payment'];
                                    }
                                    return $carry;
                                });
                                $listVoucher = array_reduce($listVoucher, function ($carry, $item) {
                                    $carry[$item['order_id']][] = $item;
                                    return $carry;
                                });
                            }
                        }
                    }
                }
            }
            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $customer_id = $value['customer_id'];
                $dtCustomer = $listCustomers[$customer_id] ?? null;
                $company_short = $dtCustomer['company_short'] ?? '';
                $customer_name = $dtCustomer['company'] ?? '';
                $time_payment = $dtCustomer['time_payment'] ?? 0;
                $name_discount = $dtCustomer['name_discount'] ?? '';
                $brand = $dtCustomer['brand'] ?? '';
                $items = $listOrderItems[$value['id']] ?? [];
                $deliveries = $listDelivery[$value['id']] ?? [];
                $vouchers = $listVoucher[$value['id']] ?? [];
                $invoiceTotal = 0;
                $voucherTotal = $listVoucherTotal[$value['id']] ?? 0;
                if (!empty($deliveries)){
                    foreach ($deliveries as $k => $v){
                        $total = $listInvoiceTotal[$v['id']] ?? 0;
                        $invoiceTotal += $total;
                    }
                }
                $maxCount = count($items);
                if ($maxCount) {
                    for ($i = 0; $i < $maxCount; $i++) {
                        $item = $items[$i] ?? [];
                        $amount_remaining = $invoiceTotal - $voucherTotal;
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[0] . $row, $dem, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $company_short, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $customer_name, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, $item['item_code'], PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, $item['item_name'], PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, !empty($item['quantity']) && $item['quantity'] <= 5000 ? $item['quantity'] : 0, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, $invoiceTotal, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, $time_payment, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[19] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[21] . $row, $voucherTotal, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[22] . $row, $amount_remaining, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[23] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[24] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);

                        $maxCountDelivery = max(count($deliveries),count($vouchers));
                        if ($maxCountDelivery){
                            for ($i = 0;$i < $maxCountDelivery;$i++){
                                $row++;
                                $delivery = $deliveries[$i] ?? [];
                                $voucher = $vouchers[$i] ?? [];
                                $invoice = [];
                                if (!empty($delivery)) {
                                    // chi có 1 hóa đơn
                                    $invoices = $listInvoice[$delivery['id']] ?? [];
                                    $invoice = $invoices[0] ?? [];
                                }
                                $date_invoice = !empty($invoice) ? $invoice['date'] : '';
                                $date_invoice_new = null;
                                if (!empty($date_invoice)) {
                                    $date_invoice_new = date('Y-m-d',
                                        strtotime('first day of next month', strtotime($date_invoice)));
                                    $date_invoice_new = date('Y-m-d', strtotime('+'.$time_payment.' days', strtotime($date_invoice_new)));
                                }
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[0] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, !empty($delivery) ? $delivery['reference_no'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, !empty($delivery) ? _dt($delivery['date']) : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, !empty($delivery) ? _dt($delivery['date_warehouseman']) : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, !empty($voucher) ? $voucher['reference_no'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, !empty($delivery) ? $delivery['code_custom'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, !empty($delivery['date_custom']) ? _dhau($delivery['date_custom']) : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, !empty($invoice) ? ($invoice['reference_no']) : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, !empty($invoice) ? ($invoice['tax_rate']) : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, !empty($invoice) ? ($invoice['grand_total_item']) : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, (!empty($date_invoice_new) ? _dhau($date_invoice_new) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, !empty($voucher) ? $voucher['code_bank'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[19] . $row, !empty($voucher['date_bank']) ? _dhau($voucher['date_bank']) : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, !empty($voucher) ? $voucher['reference_no'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[21] . $row, !empty($voucher) ? $voucher['payment'] : '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[22] . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[23] . $row, (!empty($invoice['date_misa']) ? _dhau($invoice['date_misa']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[24] . $row, (!empty($date_invoice_new) ? _dhau($date_invoice_new) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                            }
                        }

                    }
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle('A3:Y' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A3:Y' . $row)->applyFromArray([
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[0])->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[1])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[11])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[12])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[13])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[23])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[24])->setWidth(20);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('bao_cao_tong_hop_thu_thang') . '.xls';
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

    //
    public function getPlanning()
    {
        $productions_orders_search = $this->input->post('productions_orders_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $aColumns = [
            'tbl_productions_orders.id as id',
            '"" as ngay_ke_hoach',
            'tbl_productions_orders.reference_no as lenh_san_xuat',
            '"" as qui_cach_van_hanh',
            'tbl_productions_orders.total_quantity as tong_so_luong',
            '"" as so_con_lan_van_hanh',
            '"" as tong_so_lan_van_hanh',
            '"" as phieu_xuat_mau_san_xuat',
            '"" as phieu_xuat_khuan_be',
            '"" as phieu_xuat_npl',
            '"" as phieu_xuat_kem',
            '"" as ghep_size',
            '"" as dan_trang',
            '"" as npl_canh_bai',
            '"" as ngay_ve_npl',
            '"" as da_co_npl',
            '"" as phieu_cat_giay',
            '"" as ma_bckph',
            'tbl_productions_orders.date as ngay_cap_nhat_foso',
            'tbl_productions_orders.status_gdsx as xac_nhan_ban_giao',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_productions_orders';
        $join = [

        ];

        $groupByAndOrderBy = '';
        $where        = [];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
        }

        if (!empty($productions_orders_search)) {
            $productions_orders_search = $this->db->escape($productions_orders_search);
            array_push($where, "AND tbl_productions_orders.id = $productions_orders_search");
        }

        $groupByAndOrderBy = 'ORDER BY tbl_productions_orders.id DESC';
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], $groupByAndOrderBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];


        if (!empty($rResult)) {
            $arrPOId = [];
            foreach ($rResult as $key => $value) {
                $arrPOId[] = $value['id'];
            }

            if (!empty($arrPOId)) {
                $arrPOId = array_unique($arrPOId);

                $this->db->select('
                    tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                    tbl_productions_orders_items.items_id as items_id,
                    tbl_productions_orders_items.quantity as quantity,
                    tbl_productions_plan.date as date,
                    tbl_productions_plan_items.productions_plan_id as productions_plan_id,
                    tbl_products.id_standard_sample_code as id_standard_sample_code
                ', false);
                $this->db->from('tbl_productions_orders_items');
                $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = tbl_productions_orders_items.plan_item_id');
                $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_plan_items.productions_plan_id');
                $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                $this->db->where_in('tbl_productions_orders_items.productions_orders_id', $arrPOId, false);
                $productions_orders_items = $this->db->get()->result_array();
                $arrStandardSampleCode = [];
                $arrPPlanId = [];
                if (!empty($productions_orders_items)) {
                    $productions_orders_items = array_reduce($productions_orders_items, function ($result, $item) use(&$arrStandardSampleCode, &$arrPPlanId) {
                        $result[$item['productions_orders_id']][] = $item;
                        if (!empty($item['id_standard_sample_code'])) {
                            $arrStandardSampleCode[] = $item['id_standard_sample_code'];
                        }

                        if (!empty($item['productions_plan_id'])) {
                            $arrPPlanId[] = $item['productions_plan_id'];
                        }

                        return $result;
                    });
                }

                if (!empty($arrStandardSampleCode)) {
                    $this->db->where_in('id', $arrStandardSampleCode);
					$this->db->where('type', 'standard_sample_code');
                    $this->db->select('id, standard', false);
					$listSampleCoverCode = $this->db->get('tbllist_other')->result_array();
                    if (!empty($listSampleCoverCode)) {
                        $listSampleCoverCode = array_reduce($listSampleCoverCode, function($carry, $item) {
                            $carry[$item['id']] = $item;
                            return $carry;
                        });
                    }
                }

                //productions orders stage
                $this->db->select('
                    pois.productions_orders_id as po_id,
                    tbl_stages.name as stage_name,
                    tbl_machines.name as machine_name,
                    tbl_machines.operating_gauge as operating_gauge
                ', false);
                $this->db->from('tbl_productions_orders_items_stages pois');
                $this->db->join('tbl_stages', 'tbl_stages.id = pois.stage_id');
                // $this->db->join('tbl_machines', 'tbl_machines.id = pois.machines_id', 'left');
                $this->db->join('tbl_machines', 'tbl_machines.id = pois.machines', 'left');
                $this->db->where_in('pois.productions_orders_id', $arrPOId, false);
                $listStages = $this->db->get()->result_array();
                if (!empty($listStages)) {
                    $listStages = array_reduce($listStages, function ($result, $item) {
                        $result[$item['po_id']][] = $item;
                        return $result;
                    });
                }

                //BOM
                $this->db->select('
                    tbl_productions_orders_items_sub.productions_orders_id as po_id,
                    tbl_productions_orders_items_sub.type, 
                    tbl_productions_orders_items_sub.item_id, 
                    tbl_productions_orders_items_sub.landscape_print_size, 
                    tbl_productions_orders_items_sub.number_children_size,
                    tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
                    MAX(tbl_productions_orders_items_sub.quantity_compensation) as quantity_compensation, 
                    SUM(tbl_productions_orders_items_sub.quantity) as quantity,
                    tbl_productions_orders_items_sub.quantity_single as quantity_single,
                    tblunits.unit as unit_name_parent,
                    unit_b.unit as unit_bom,

                ', false);
                $this->db->from('tbl_productions_orders_items_sub');
                $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_parent_id', 'left');
                $this->db->join('tblunits unit_b', 'unit_b.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
                $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
                $this->db->where_in('tbl_productions_orders_items_sub.productions_orders_id', $arrPOId, false);
                $this->db->group_by('tbl_productions_orders_items_sub.productions_orders_id, tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.landscape_print_size, tbl_productions_orders_items_sub.number_children_size, tbl_productions_orders_items_sub.unit_parent_id, tbl_productions_orders_items_sub.quantity_single');
                $listBoms = $this->db->get()->result_array();
                if (!empty($listBoms)) {
                    $listBoms = array_reduce($listBoms, function ($result, $item) {
                        $result[$item['po_id']][] = $item;
                        return $result;
                    });
                }

                //xuất kho khác
                $this->db->select('
                    tbltblexport_different_items.po_id,
                    tblexport_different.type_po,
                    CONCAT(tblexport_different.prefix, "-", tblexport_different.code) as reference_no
                ', false);
                $this->db->from('tblexport_different');
                $this->db->join('tbltblexport_different_items', 'tbltblexport_different_items.id_export_different = tblexport_different.id');
                // $this->db->where_in('tblexport_different.po_id', $arrPOId, false);
                $this->db->where_in('tbltblexport_different_items.po_id', $arrPOId, false);
                $this->db->group_by('tblexport_different.id');
                $listExportDifferent = $this->db->get()->result_array();
                if (!empty($listExportDifferent)) {
                    $listExportDifferent = array_reduce($listExportDifferent, function ($result, $item) {
                        $result[$item['po_id']][] = $item;
                        return $result;
                    }); 
                }

                //xuất kho sản xuất
                $this->db->select('
                    tbl_suggest_exporting.po_id,
                    tbl_suggest_exporting.reference_stock as reference_stock
                ', false);
                $this->db->from('tbl_suggest_exporting');
                $this->db->where_in('tbl_suggest_exporting.po_id', $arrPOId, false);
                $listSuggestExporting = $this->db->get()->result_array();
                if (!empty($listSuggestExporting)) {
                    $listSuggestExporting = array_reduce($listSuggestExporting, function ($result, $item) {
                        $result[$item['po_id']][] = $item;
                        return $result;
                    });
                }

                //mua hàng
                if (!empty($arrPPlanId)) {
                    $arrPPlanId = array_unique($arrPPlanId);
                    $this->db->select('
                        tblpurchases.id as pplan_id,
                        tblpurchases.delivery_date,
                    ', false);
                    $this->db->from('tbl_purchases_plans');
                    $this->db->join('tblpurchases', 'tblpurchases.id = tbl_purchases_plans.purchases_id');
                    $this->db->where_in('tbl_purchases_plans.productions_plan_id', $arrPPlanId, false);
                    $listPurchasesPlans = $this->db->get()->result_array();
                    if (!empty($listPurchasesPlans)) {
                        $listPurchasesPlans = array_reduce($listPurchasesPlans, function ($result, $item) {
                            $result[$item['pplan_id']] = $item;
                            return $result;
                        });
                    }
                }

                //Xả khổ
                $this->db->select('
                    id_production_detail as po_id,
                    tbl_manufactures.reference_no as reference_no,
                ', false);
                $this->db->from('tbl_manufactures');
                $this->db->where_in('tbl_manufactures.id_production_detail', $arrPOId, false);
                $listManufacture = $this->db->get()->result_array();
                if (!empty($listManufacture)) {
                    $listManufacture = array_reduce($listManufacture, function ($result, $item) {
                        $result[$item['po_id']][] = $item;
                        return $result;
                    });
                }

                //báo cáo vi phạm
                $this->db->select('
                    tblproduction_report.id_production_orders as id_production_orders,
                    tblproduction_report.reference_no as reference_no,
                ', false);
                $this->db->from('tblproduction_report');
                $this->db->where_in('tblproduction_report.id_production_orders', $arrPOId, false);
                $listProductionReport = $this->db->get()->result_array();
                if (!empty($listProductionReport)) {
                    $listProductionReport = array_reduce($listProductionReport, function ($result, $item) {
                        $result[$item['id_production_orders']][] = $item;
                        return $result;
                    });
                }
            }
        }

        $aColumns = handlingColumns($aColumns);
        $stt = $this->input->post('start');
        foreach ($rResult as $kRow => $aRow) {
            $stt++;

            $po_id = $aRow['id'];
            $items = $productions_orders_items[$po_id] ?? null;
            $ngay_ke_hoach = $items[0]['date'] ?? null;
            $pPlan_id = $items[0]['productions_plan_id'] ?? null;

            $phieu_xuat_mau_san_xuat = '';
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    //phiếu xuất mẫu sản xuất
                    $id_standard_sample_code = $value['id_standard_sample_code'];
                    if (!empty($id_standard_sample_code)) {
                        $sampleCoverCode = $listSampleCoverCode[$id_standard_sample_code] ?? null;
                        if (!empty($sampleCoverCode)) {
                            $phieu_xuat_mau_san_xuat = $sampleCoverCode['standard'];
                        }
                    }
                }
            }

            $stages = $listStages[$po_id] ?? null;

            $qui_cach_van_hanh = '';
            $keywords_dan_trang = ['dàn trang'];
            $keywords_ghep_size = ['ghép size'];
            $is_dan_trang = '';
            $is_ghep_size = '';
            if (!empty($stages)) {
                foreach ($stages as $stage) {
                    if ($stage['operating_gauge'] && empty($qui_cach_van_hanh)) {
                        $qui_cach_van_hanh.= $stage['operating_gauge'];
                    }

                    $stage_name = mb_strtolower($stage['stage_name']);
                    foreach ($keywords_dan_trang as $keyword) {
                        if (strpos($stage_name, $keyword) !== false) {
                            $is_dan_trang = 'có';
                            break;
                        }
                    }

                    foreach ($keywords_ghep_size as $keyword) {
                        if (strpos($stage_name, $keyword) !== false) {
                            $is_ghep_size = 'có';
                            break;
                        }
                    }
                }
            }

            //BOM
            $bom = $listBoms[$po_id] ?? null;
            if (FIX_QUANTITY_COMPENSATION) {
                $arrCountItems = [];
                if (!empty($bom)) {
                    foreach ($bom as $key => $value) {
                        $strKey = $value['type'].'__'.$value['item_id'];
                        if (!empty($arrCountItems[$strKey])) {
                            $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                        } else {
                            $arrCountItems[$strKey]['count'] = 1;
                            $arrCountItems[$strKey]['decimal'] = 0;
                        }
                    }
                }
            }

            $number_children_size = '';
            $total_quantity_single = 0;
            $total_quantity_compensation = 0;
            if (!empty($bom)) {
                foreach ($bom as $key => $value) {
                    $item_id = $value['item_id'];
                    $type = $value['type'];
                    $height = 0;
                    $mode = '';
    
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($pPlan_id, $value['item_id'], $value['type']);
                    $quantity_compensation_bom = $value['quantity_compensation'];
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];
                    //fix quantity compensation
                    if (FIX_QUANTITY_COMPENSATION) {
                        $strKey = $value['type'].'__'.$value['item_id'];
                        $count_item = $arrCountItems[$strKey]['count'];
                        $division = $quantity_compensation/$count_item;
                        if (is_decimal($division)) {
                            if ($arrCountItems[$strKey]['decimal']) {
                                $quantity_compensation = floor($division);
                            } else {
                                $arrCountItems[$strKey]['decimal'] = 1;
                                $quantity_compensation = ceil($division);
                            }
                        } else {
                            $quantity_compensation = $division;
                        }
                    }
                    //
    
                    $quantity = ceil(round($value['quantity'], 3));
                    $quantity_single = $value['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need/$quantity_single) : 0;

                    $number_children_size = !empty($value['number_children_size']) ? $value['number_children_size'] : $number_children_size;
                    // $total_quantity_single+= $quantity_single;
                    if ($value['unit_bom'] == 'tờ') {
                        $so_con_lan_van_hanh = $number_children_size;
                        $tong_so_lan_van_hanh = $quantity_single;
                        $total_quantity_single = 1/$tong_so_lan_van_hanh;
                    }
                    $total_quantity_compensation+= $quantity_compensation;
                }
            }

            //xuất kho khác
            $exportDifferent = $listExportDifferent[$po_id] ?? null;
            $arrPhieuXuatKhuanBe = [];
            $arrPhieuXuatKem = [];
            if (!empty($exportDifferent)) {
                foreach ($exportDifferent as $key => $value) {
                    $type_po = $value['type_po'];
                    if ($type_po == 1) {
                        $arrPhieuXuatKhuanBe[] = $value['reference_no'];
                    } else if ($type_po == 2) {
                        $arrPhieuXuatKem[] = $value['reference_no'];
                    }
                }
            }

            //xuất kho sản xuất
            $is_co_npl = '';
            $suggestExporting = $listSuggestExporting[$po_id] ?? null;
            $arrPhieuXuatNPL = [];
            if (!empty($suggestExporting)) {
                foreach ($suggestExporting as $key => $value) {
                    $arrPhieuXuatNPL[] = $value['reference_stock'];
                    $is_co_npl = 'có';
                }
            }

            //mua hàng
            $purchases = $listPurchasesPlans[$pPlan_id] ?? null;
            $ngay_ve_npl = !empty($purchases['delivery_date']) ? date_format(date_create($purchases['delivery_date']), 'd/m/Y') : null;
            
            //xả khổ
            $manufacture = $listManufacture[$po_id] ?? null;
            $arrPhieuCatGiay = [];
            if (!empty($manufacture)) {
                foreach ($manufacture as $key => $value) {
                    $arrPhieuCatGiay[] = $value['reference_no'];
                }
            }

            //mã BCKPH
            $productionReport = $listProductionReport[$po_id] ?? null;
            $arrBCKPH = [];
            if (!empty($productionReport)) {
                foreach ($productionReport as $key => $value) {
                    $arrBCKPH[] = $value['reference_no'];
                }
            }

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'id') {
                    $row[] = '<div class="text-center">' . $stt . '</div>';
                } else if ($v == 'ngay_ke_hoach') {
                    $row[] = _dt($ngay_ke_hoach);
                } else if ($v == 'qui_cach_van_hanh') {
                    $row[] = $qui_cach_van_hanh;
                } else if ($v == 'tong_so_luong') {
                    $row[] = '<div class="text-right">'.formatNumber($aRow['tong_so_luong']).'</div>';
                } else if ($v == 'so_con_lan_van_hanh') {
                    $row[] = '<div class="text-center">'.$number_children_size.'</div>';
                } else if ($v == 'tong_so_lan_van_hanh') {
                    $row[] = '<div class="text-center">'.formatNumber($total_quantity_single).'</div>';
                } else if ($v == 'phieu_xuat_mau_san_xuat') {
                    $row[] = $phieu_xuat_mau_san_xuat;
                } else if ($v == 'phieu_xuat_khuan_be') {
                    $row[] = '<div class="text-center">'.implode('</br>', $arrPhieuXuatKhuanBe).'</div>';
                } else if ($v == 'phieu_xuat_kem') {
                    $row[] = '<div class="text-center">'.implode('</br>', $arrPhieuXuatKem).'</div>';
                } else if ($v == 'phieu_xuat_npl') {
                    $row[] = '<div class="text-center">'.implode('</br>', $arrPhieuXuatNPL).'</div>';
                } else if ($v == 'npl_canh_bai') {
                    $row[] = '<div class="text-center">'.($total_quantity_compensation).'</div>';
                } else if ($v == 'ghep_size') {
                    $row[] = '<div class="text-center">'.$is_ghep_size.'</div>';
                } else if ($v == 'dan_trang') {
                    $row[] = '<div class="text-center">'.$is_dan_trang.'</div>';
                } else if ($v == 'ngay_ve_npl') {
                    $row[] = '<div class="text-center">'.$ngay_ve_npl.'</div>';
                } else if ($v == 'da_co_npl') {
                    $row[] = '<div class="text-center">'.$is_co_npl.'</div>';
                } else if ($v == 'phieu_cat_giay') {
                    $row[] = '<div class="text-center">'.implode('</br>', $arrPhieuCatGiay).'</div>';
                } else if ($v == 'ma_bckph') {
                    $row[] = '<div class="text-center">'.implode('</br>', $arrBCKPH).'</div>';
                } else if ($v == 'ngay_cap_nhat_foso') {
                    $row[] = date_format(date_create($_data), 'd/m/Y');
                } else if ($v == 'xac_nhan_ban_giao') {
                    $row[] = '<div class="text-center">'.($_data > 0 ? 'Đã xác nhận' : 'Chưa xác nhận').'</div>';
                }
                else {
                    $row[] = $_data;
                }
            }
            $output['aaData'][] = $row;
        }

        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function exportExcelPlanning()
    {
        $productions_orders_search = $this->input->post('productions_orders_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        if (empty($start_date_search) || empty($end_date_search)) {
            $response = array(
                'result' => 0,
                'message' => lang('Vui lòng nhập ngày bắt đầu và kết thúc'),
            );
            echo json_encode($response);
            return;
        }

        $excel = cloumns_excel();
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $excel = cloumns_excel();
        insertCompanyInfo($objPHPExcel, 'C1:P2');

        $rowBegin = 5;
        $iExcel = -1;
        
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('STT'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Kế Hoạch'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Lệnh Sản Xuất'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Qui Cách Vận Hành'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tổng Số Lượng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Số Con/Lần Vận Hành'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tổng Số Lần Vận Hành'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Phiếu Xuất Mẫu Sản Xuất'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Phiếu Xuất Khuân Bế'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Phiếu Xuất NPL'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Phiếu Xuất Kẽm'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ghép Size'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Dàn Trang'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('NPL Canh Bài'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Về NPL'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Đã Có NPL'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Phiếu Cắt Giấy'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã BCKPH'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Cập Nhật Foso'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Xác Nhận Bàn Giao Cho GĐSX'));

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $excel[$iExcel] . $rowBegin)->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $aColumns = [
            'tbl_productions_orders.id as id',
            '"" as ngay_ke_hoach',
            'tbl_productions_orders.reference_no as lenh_san_xuat',
            '"" as qui_cach_van_hanh',
            'tbl_productions_orders.total_quantity as tong_so_luong',
            '"" as so_con_lan_van_hanh',
            '"" as tong_so_lan_van_hanh',
            '"" as phieu_xuat_mau_san_xuat',
            '"" as phieu_xuat_khuan_be',
            '"" as phieu_xuat_npl',
            '"" as phieu_xuat_kem',
            '"" as ghep_size',
            '"" as dan_trang',
            '"" as npl_canh_bai',
            '"" as ngay_ve_npl',
            '"" as da_co_npl',
            '"" as phieu_cat_giay',
            '"" as ma_bckph',
            'tbl_productions_orders.date as ngay_cap_nhat_foso',
            'tbl_productions_orders.status_gdsx as xac_nhan_ban_giao',
        ];
        
        // Thiết lập dữ liệu truy vấn
        $sIndexColumn = 'id';
        $sTable       = 'tbl_productions_orders';
        $join = [];
        
        $groupByAndOrderBy = '';
        $where        = [];
        
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
        }

        if (!empty($productions_orders_search)) {
            $productions_orders_search = $this->db->escape($productions_orders_search);
            array_push($where, "AND tbl_productions_orders.id = $productions_orders_search");
        }
        
        $groupByAndOrderBy = 'ORDER BY tbl_productions_orders.id DESC';
        
        // Thực hiện truy vấn
        $query = "SELECT " . implode(', ', $aColumns) . " FROM $sTable " . implode(' ', $join) . " WHERE 1 " . implode(' ', $where) . " $groupByAndOrderBy";
        $rResult = $this->db->query($query)->result_array();
        if (!empty($rResult)) {
            $arrPOId = [];
            foreach ($rResult as $key => $value) {
                $arrPOId[] = $value['id'];
            }

            if (!empty($arrPOId)) {
                $arrPOId = array_unique($arrPOId);

                $this->db->select('
                    tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                    tbl_productions_orders_items.items_id as items_id,
                    tbl_productions_orders_items.quantity as quantity,
                    tbl_productions_plan.date as date,
                    tbl_productions_plan_items.productions_plan_id as productions_plan_id,
                    tbl_products.id_standard_sample_code as id_standard_sample_code
                ', false);
                $this->db->from('tbl_productions_orders_items');
                $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = tbl_productions_orders_items.plan_item_id');
                $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_plan_items.productions_plan_id');
                $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                $this->db->where_in('tbl_productions_orders_items.productions_orders_id', $arrPOId, false);
                $productions_orders_items = $this->db->get()->result_array();
                $arrStandardSampleCode = [];
                $arrPPlanId = [];
                if (!empty($productions_orders_items)) {
                    $productions_orders_items = array_reduce($productions_orders_items, function ($result, $item) use(&$arrStandardSampleCode, &$arrPPlanId) {
                        $result[$item['productions_orders_id']][] = $item;
                        if (!empty($item['id_standard_sample_code'])) {
                            $arrStandardSampleCode[] = $item['id_standard_sample_code'];
                        }

                        if (!empty($item['productions_plan_id'])) {
                            $arrPPlanId[] = $item['productions_plan_id'];
                        }

                        return $result;
                    });
                }

                if (!empty($arrStandardSampleCode)) {
                    $this->db->where_in('id', $arrStandardSampleCode);
					$this->db->where('type', 'standard_sample_code');
                    $this->db->select('id, standard', false);
					$listSampleCoverCode = $this->db->get('tbllist_other')->result_array();
                    if (!empty($listSampleCoverCode)) {
                        $listSampleCoverCode = array_reduce($listSampleCoverCode, function($carry, $item) {
                            $carry[$item['id']] = $item;
                            return $carry;
                        });
                    }
                }

                //productions orders stage
                $this->db->select('
                    pois.productions_orders_id as po_id,
                    tbl_stages.name as stage_name,
                    tbl_machines.name as machine_name,
                    tbl_machines.operating_gauge as operating_gauge
                ', false);
                $this->db->from('tbl_productions_orders_items_stages pois');
                $this->db->join('tbl_stages', 'tbl_stages.id = pois.stage_id');
                // $this->db->join('tbl_machines', 'tbl_machines.id = pois.machines_id', 'left');
                $this->db->join('tbl_machines', 'tbl_machines.id = pois.machines', 'left');
                $this->db->where_in('pois.productions_orders_id', $arrPOId, false);
                $listStages = $this->db->get()->result_array();
                if (!empty($listStages)) {
                    $listStages = array_reduce($listStages, function ($result, $item) {
                        $result[$item['po_id']][] = $item;
                        return $result;
                    });
                }

                //BOM
                $this->db->select('
                    tbl_productions_orders_items_sub.productions_orders_id as po_id,
                    tbl_productions_orders_items_sub.type, 
                    tbl_productions_orders_items_sub.item_id, 
                    tbl_productions_orders_items_sub.landscape_print_size, 
                    tbl_productions_orders_items_sub.number_children_size,
                    tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
                    MAX(tbl_productions_orders_items_sub.quantity_compensation) as quantity_compensation, 
                    SUM(tbl_productions_orders_items_sub.quantity) as quantity,
                    tbl_productions_orders_items_sub.quantity_single as quantity_single,
                    tblunits.unit as unit_name_parent,
                    unit_b.unit as unit_bom,

                ', false);
                $this->db->from('tbl_productions_orders_items_sub');
                $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_parent_id', 'left');
                $this->db->join('tblunits unit_b', 'unit_b.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
                $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
                $this->db->where_in('tbl_productions_orders_items_sub.productions_orders_id', $arrPOId, false);
                $this->db->group_by('tbl_productions_orders_items_sub.productions_orders_id, tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.landscape_print_size, tbl_productions_orders_items_sub.number_children_size, tbl_productions_orders_items_sub.unit_parent_id, tbl_productions_orders_items_sub.quantity_single');
                $listBoms = $this->db->get()->result_array();
                if (!empty($listBoms)) {
                    $listBoms = array_reduce($listBoms, function ($result, $item) {
                        $result[$item['po_id']][] = $item;
                        return $result;
                    });
                }

                //xuất kho khác
                // $this->db->select('
                //     tblexport_different.po_id,
                //     tblexport_different.type_po,
                //     CONCAT(tblexport_different.prefix, "-", tblexport_different.code) as reference_no
                // ', false);
                // $this->db->from('tblexport_different');
                // $this->db->where_in('tblexport_different.po_id', $arrPOId, false);
                // $listExportDifferent = $this->db->get()->result_array();
                // if (!empty($listExportDifferent)) {
                //     $listExportDifferent = array_reduce($listExportDifferent, function ($result, $item) {
                //         $result[$item['po_id']][] = $item;
                //         return $result;
                //     }); 
                // }
                $this->db->select('
                    tbltblexport_different_items.po_id,
                    tblexport_different.type_po,
                    CONCAT(tblexport_different.prefix, "-", tblexport_different.code) as reference_no
                ', false);
                $this->db->from('tblexport_different');
                $this->db->join('tbltblexport_different_items', 'tbltblexport_different_items.id_export_different = tblexport_different.id');
                // $this->db->where_in('tblexport_different.po_id', $arrPOId, false);
                $this->db->where_in('tbltblexport_different_items.po_id', $arrPOId, false);
                $this->db->group_by('tblexport_different.id');
                $listExportDifferent = $this->db->get()->result_array();
                if (!empty($listExportDifferent)) {
                    $listExportDifferent = array_reduce($listExportDifferent, function ($result, $item) {
                        $result[$item['po_id']][] = $item;
                        return $result;
                    }); 
                }

                //xuất kho sản xuất
                $this->db->select('
                    tbl_suggest_exporting.po_id,
                    tbl_suggest_exporting.reference_stock as reference_stock
                ', false);
                $this->db->from('tbl_suggest_exporting');
                $this->db->where_in('tbl_suggest_exporting.po_id', $arrPOId, false);
                $listSuggestExporting = $this->db->get()->result_array();
                if (!empty($listSuggestExporting)) {
                    $listSuggestExporting = array_reduce($listSuggestExporting, function ($result, $item) {
                        $result[$item['po_id']][] = $item;
                        return $result;
                    });
                }

                //mua hàng
                if (!empty($arrPPlanId)) {
                    $arrPPlanId = array_unique($arrPPlanId);
                    $this->db->select('
                        tblpurchases.id as pplan_id,
                        tblpurchases.delivery_date,
                    ', false);
                    $this->db->from('tbl_purchases_plans');
                    $this->db->join('tblpurchases', 'tblpurchases.id = tbl_purchases_plans.purchases_id');
                    $this->db->where_in('tbl_purchases_plans.productions_plan_id', $arrPPlanId, false);
                    $listPurchasesPlans = $this->db->get()->result_array();
                    if (!empty($listPurchasesPlans)) {
                        $listPurchasesPlans = array_reduce($listPurchasesPlans, function ($result, $item) {
                            $result[$item['pplan_id']] = $item;
                            return $result;
                        });
                    }
                }

                //Xả khổ
                $this->db->select('
                    id_production_detail as po_id,
                    tbl_manufactures.reference_no as reference_no,
                ', false);
                $this->db->from('tbl_manufactures');
                $this->db->where_in('tbl_manufactures.id_production_detail', $arrPOId, false);
                $listManufacture = $this->db->get()->result_array();
                if (!empty($listManufacture)) {
                    $listManufacture = array_reduce($listManufacture, function ($result, $item) {
                        $result[$item['po_id']][] = $item;
                        return $result;
                    });
                }

                //báo cáo vi phạm
                $this->db->select('
                    tblproduction_report.id_production_orders as id_production_orders,
                    tblproduction_report.reference_no as reference_no,
                ', false);
                $this->db->from('tblproduction_report');
                $this->db->where_in('tblproduction_report.id_production_orders', $arrPOId, false);
                $listProductionReport = $this->db->get()->result_array();
                if (!empty($listProductionReport)) {
                    $listProductionReport = array_reduce($listProductionReport, function ($result, $item) {
                        $result[$item['id_production_orders']][] = $item;
                        return $result;
                    });
                }
            }
        }

        $aColumns = handlingColumns($aColumns);
        $stt = 0;
        foreach ($rResult as $key => $aRow) {
            $stt++;
            $po_id = $aRow['id'];
            $items = $productions_orders_items[$po_id] ?? null;
            $ngay_ke_hoach = $items[0]['date'] ?? null;
            $pPlan_id = $items[0]['productions_plan_id'] ?? null;

            $phieu_xuat_mau_san_xuat = '';
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    //phiếu xuất mẫu sản xuất
                    $id_standard_sample_code = $value['id_standard_sample_code'];
                    if (!empty($id_standard_sample_code)) {
                        $sampleCoverCode = $listSampleCoverCode[$id_standard_sample_code] ?? null;
                        if (!empty($sampleCoverCode)) {
                            $phieu_xuat_mau_san_xuat = $sampleCoverCode['standard'];
                        }
                    }
                }
            }

            $stages = $listStages[$po_id] ?? null;

            $qui_cach_van_hanh = '';
            $keywords_dan_trang = ['dàn trang'];
            $keywords_ghep_size = ['ghép size'];
            $is_dan_trang = '';
            $is_ghep_size = '';
            if (!empty($stages)) {
                foreach ($stages as $stage) {
                    if ($stage['operating_gauge'] && empty($qui_cach_van_hanh)) {
                        $qui_cach_van_hanh.= $stage['operating_gauge'];
                    }

                    $stage_name = mb_strtolower($stage['stage_name']);
                    foreach ($keywords_dan_trang as $keyword) {
                        if (strpos($stage_name, $keyword) !== false) {
                            $is_dan_trang = 'có';
                            break;
                        }
                    }

                    foreach ($keywords_ghep_size as $keyword) {
                        if (strpos($stage_name, $keyword) !== false) {
                            $is_ghep_size = 'có';
                            break;
                        }
                    }
                }
            }

            //BOM
            $bom = $listBoms[$po_id] ?? null;
            if (FIX_QUANTITY_COMPENSATION) {
                $arrCountItems = [];
                if (!empty($bom)) {
                    foreach ($bom as $key => $value) {
                        $strKey = $value['type'].'__'.$value['item_id'];
                        if (!empty($arrCountItems[$strKey])) {
                            $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                        } else {
                            $arrCountItems[$strKey]['count'] = 1;
                            $arrCountItems[$strKey]['decimal'] = 0;
                        }
                    }
                }
            }

            $number_children_size = '';
            $total_quantity_single = 0;
            $total_quantity_compensation = 0;
            if (!empty($bom)) {
                foreach ($bom as $key => $value) {
                    $item_id = $value['item_id'];
                    $type = $value['type'];
                    $height = 0;
                    $mode = '';
    
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($pPlan_id, $value['item_id'], $value['type']);
                    $quantity_compensation_bom = $value['quantity_compensation'];
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];
                    //fix quantity compensation
                    if (FIX_QUANTITY_COMPENSATION) {
                        $strKey = $value['type'].'__'.$value['item_id'];
                        $count_item = $arrCountItems[$strKey]['count'];
                        $division = $quantity_compensation/$count_item;
                        if (is_decimal($division)) {
                            if ($arrCountItems[$strKey]['decimal']) {
                                $quantity_compensation = floor($division);
                            } else {
                                $arrCountItems[$strKey]['decimal'] = 1;
                                $quantity_compensation = ceil($division);
                            }
                        } else {
                            $quantity_compensation = $division;
                        }
                    }
                    //
    
                    $quantity = ceil(round($value['quantity'], 3));
                    $quantity_single = $value['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need/$quantity_single) : 0;

                    $number_children_size = !empty($value['number_children_size']) ? $value['number_children_size'] : $number_children_size;
                    // $total_quantity_single+= $quantity_single;
                    if ($value['unit_bom'] == 'tờ') {
                        $so_con_lan_van_hanh = $number_children_size;
                        $tong_so_lan_van_hanh = $quantity_single;
                        $total_quantity_single = 1/$tong_so_lan_van_hanh;
                    }
                    $total_quantity_compensation+= $quantity_compensation;
                }
            }

            //xuất kho khác
            $exportDifferent = $listExportDifferent[$po_id] ?? null;
            $arrPhieuXuatKhuanBe = [];
            $arrPhieuXuatKem = [];
            if (!empty($exportDifferent)) {
                foreach ($exportDifferent as $key => $value) {
                    $type_po = $value['type_po'];
                    if ($type_po == 1) {
                        $arrPhieuXuatKhuanBe[] = $value['reference_no'];
                    } else if ($type_po == 2) {
                        $arrPhieuXuatKem[] = $value['reference_no'];
                    }
                }
            }

            //xuất kho sản xuất
            $is_co_npl = '';
            $suggestExporting = $listSuggestExporting[$po_id] ?? null;
            $arrPhieuXuatNPL = [];
            if (!empty($suggestExporting)) {
                foreach ($suggestExporting as $key => $value) {
                    $arrPhieuXuatNPL[] = $value['reference_stock'];
                    $is_co_npl = 'có';
                }
            }

            //mua hàng
            $purchases = $listPurchasesPlans[$pPlan_id] ?? null;
            $ngay_ve_npl = !empty($purchases['delivery_date']) ? date_format(date_create($purchases['delivery_date']), 'd/m/Y') : null;
            
            //xả khổ
            $manufacture = $listManufacture[$po_id] ?? null;
            $arrPhieuCatGiay = [];
            if (!empty($manufacture)) {
                foreach ($manufacture as $key => $value) {
                    $arrPhieuCatGiay[] = $value['reference_no'];
                }
            }

            //mã BCKPH
            $productionReport = $listProductionReport[$po_id] ?? null;
            $arrBCKPH = [];
            if (!empty($productionReport)) {
                foreach ($productionReport as $key => $value) {
                    $arrBCKPH[] = $value['reference_no'];
                }
            }

            $iExcel = -1;
            $rowBegin++;
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'id') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $stt);
                } else if ($v == 'ngay_ke_hoach') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, _dt($ngay_ke_hoach));
                } else if ($v == 'qui_cach_van_hanh') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $qui_cach_van_hanh);
                } else if ($v == 'tong_so_luong') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, formatNumber($aRow['tong_so_luong']));
                } else if ($v == 'so_con_lan_van_hanh') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $number_children_size);
                } else if ($v == 'tong_so_lan_van_hanh') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, formatNumber($total_quantity_single));
                } else if ($v == 'phieu_xuat_mau_san_xuat') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $phieu_xuat_mau_san_xuat);
                } else if ($v == 'phieu_xuat_khuan_be') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, implode("\n", $arrPhieuXuatKhuanBe));
                } else if ($v == 'phieu_xuat_kem') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, implode("\n", $arrPhieuXuatKem));
                } else if ($v == 'phieu_xuat_npl') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, implode("\n", $arrPhieuXuatNPL));
                } else if ($v == 'npl_canh_bai') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $total_quantity_compensation);
                } else if ($v == 'ghep_size') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $is_ghep_size);
                } else if ($v == 'dan_trang') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $is_dan_trang);
                } else if ($v == 'ngay_ve_npl') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $ngay_ve_npl);
                } else if ($v == 'da_co_npl') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $is_co_npl);
                } else if ($v == 'phieu_cat_giay') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, implode("\n", $arrPhieuCatGiay));
                } else if ($v == 'ma_bckph') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, implode("\n", $arrBCKPH));
                } else if ($v == 'ngay_cap_nhat_foso') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, date_format(date_create($_data), 'd/m/Y'));
                } else if ($v == 'xac_nhan_ban_giao') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, ($_data > 0 ? 'Đã xác nhận' : 'Chưa xác nhận'));
                }
                else {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data);
                }
            }
        }

        $objPHPExcel->getActiveSheet()->getStyle('A5:' . $excel[$iExcel] . ($rowBegin))->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        ]);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $excel[$iExcel] . ($rowBegin))->getAlignment()->setWrapText(true);

        foreach ($excel as $key => $value) {
            if ($value == $excel[$iExcel]) {
                break;
            }

            $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(15);
        }

        $filename = 'baocaotonghopkhsanxuat' . '.xls';
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
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        echo json_encode($response);
    }

    public function getProductivity()
    {
        $productions_orders_search = $this->input->post('productions_orders_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $thoi_gian_ket_thuc = "(
            SELECT pois.date_active
            FROM tbl_productions_orders_items_stages pois
            WHERE pois.productions_orders_id = tbl_production_lists_items.po_id AND pois.stage_id = tbl_production_lists_items.stage_id AND pois.active = 1
            LIMIT 1
        )";
        $aColumns = [
            'tbl_production_lists_items.id as id',
            'tbl_production_lists_items.ngay_now as ngay_dieu_do',
            'tbl_production_lists_items.stage_id as nhom_cong_doan_thiet_bi',
            'tbl_production_lists_items.may_in as qui_cach_van_hanh',
            '"" as ma_cong_doan_thiet_bi',
            '"" as ten_cong_doan_thiet_bi',
            '"" as dinh_muc_nang_suat_thang',
            '"" as dinh_muc_nang_suat_gio',
            'tbl_production_lists_items.thoi_gian_canh_bai as thoi_gian_chuan_bi_canh_bai',
            'tbl_productions_orders.reference_no as lenh_san_xuat',
            'tbl_production_lists_items.item_id as ten_sp',
            'tbl_production_lists_items.so_luong_san_xuat as tong_so_luong',
            'tbl_production_lists_items.so_con_tren_to_in as so_con_lan_van_hanh',
            '"" as tong_so_lan_van_hanh',
            'tbl_production_lists_items.ngay_bat_dau_du_kien as thoi_gian_ke_hoach',
            'tbl_production_lists_items.ngay_hoan_thanh_in as thoi_gian_hoan_thanh_thuc_te',
            'tbl_production_lists_items.ngay_now as thoi_gian_bat_dau',
            ''.$thoi_gian_ket_thuc.' as thoi_gian_ket_thuc',
            'tbl_production_lists_items.hoan_thanh as danh_gia',
            '"" as dinh_muc_npl_canh_bai',
            '"" as npl_canh_bai_thuc_te',
            '"" as ma_bckph',
            'tbl_productions_orders.date as ngay_cap_nhat_foso'
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_production_lists_items';
        $join = [
            'INNER JOIN tbl_productions_orders ON tbl_production_lists_items.po_id = tbl_productions_orders.id'
        ];

        $groupByAndOrderBy = '';
        $where        = [];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
        }

        if (!empty($productions_orders_search)) {
            $productions_orders_search = $this->db->escape($productions_orders_search);
            array_push($where, "AND tbl_production_lists_items.po_id = $productions_orders_search");
        }

        $groupByAndOrderBy = 'ORDER BY tbl_production_lists_items.id DESC';
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], $groupByAndOrderBy, []);

        $output = $result['output'];
        $rResult = $result['rResult'];


        if (!empty($rResult)) {
            $arrPOId = [];
            $arrMayIn = [];
            $arrItemId = [];
            $arrStageId = [];
            foreach ($rResult as $key => $value) {
                $arrPOId[] = $value['id'];
                $arrItemId[] = $value['ten_sp'];

                if (!empty($value['qui_cach_van_hanh'])) {
                    $arrMayIn[] = $value['qui_cach_van_hanh'];
                }

                if (!empty($value['nhom_cong_doan_thiet_bi'])) {
                    $arrStageId[] = $value['nhom_cong_doan_thiet_bi'];
                }
            }

            if (!empty($arrMayIn)) {
                $arrMayIn = array_unique($arrMayIn);

                $this->db->select('
                    tbl_machines.id as id,
                    tbl_machines.operating_gauge as operating_gauge,
                    tbl_machines.code as machine_code,
                    tbl_machines.name as machine_name,
                    tbl_machines.product_in_month as product_in_month,
                    tbl_machines.quota_productivity as quota_productivity
                ', false);
                $this->db->from('tbl_machines');
                $this->db->where_in('tbl_machines.id', $arrMayIn, false);
                $listMachines = $this->db->get()->result_array();
                if (!empty($listMachines)) {
                    $listMachines = array_reduce($listMachines, function ($result, $item) {
                        $result[$item['id']] = $item;
                        return $result;
                    });
                }
            }

            if (!empty($arrItemId)) {
                $arrItemId = array_unique($arrItemId);
                $this->db->select('
                    tbl_products.id,
                    tbl_products.code,
                    tbl_products.name,
                ', false);
                $this->db->from('tbl_products');
                $this->db->where_in('tbl_products.id', $arrItemId, false);
                $listProducts = $this->db->get()->result_array();
                if (!empty($listProducts)) {
                    $listProducts = array_reduce($listProducts, function ($result, $item) {
                        $result[$item['id']] = $item;
                        return $result;
                    });
                }
            }

            //BOM
            $this->db->select('
                tbl_productions_orders_items_sub.productions_orders_id as po_id,
                tbl_productions_orders_items_sub.type, 
                tbl_productions_orders_items_sub.item_id, 
                tbl_productions_orders_items_sub.landscape_print_size, 
                tbl_productions_orders_items_sub.number_children_size,
                tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
                MAX(tbl_productions_orders_items_sub.quantity_compensation) as quantity_compensation, 
                SUM(tbl_productions_orders_items_sub.quantity) as quantity,
                tbl_productions_orders_items_sub.quantity_single as quantity_single,
                tblunits.unit as unit_name_parent,
                unit_b.unit as unit_bom,
                tbl_productions_orders_items.plan_id as plan_id
            ', false);
            $this->db->from('tbl_productions_orders_items_sub');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_parent_id', 'left');
            $this->db->join('tblunits unit_b', 'unit_b.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_sub.productions_orders_items_id', 'left');
            $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
            $this->db->where_in('tbl_productions_orders_items_sub.productions_orders_id', $arrPOId, false);
            $this->db->group_by('tbl_productions_orders_items_sub.productions_orders_id, tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.landscape_print_size, tbl_productions_orders_items_sub.number_children_size, tbl_productions_orders_items_sub.unit_parent_id, tbl_productions_orders_items_sub.quantity_single');
            $listBoms = $this->db->get()->result_array();
            if (!empty($listBoms)) {
                $listBoms = array_reduce($listBoms, function ($result, $item) {
                    $result[$item['po_id']][] = $item;
                    return $result;
                });
            }

            //
            if (!empty($arrStageId)) {
                $arrStageId = array_unique($arrStageId);
                $this->db->select('
                    tbl_stages.id,
                    tbl_stages.code,
                    tbl_stages.name,
                    tbl_category_stages.name as name_category_stage
                ', false);
                $this->db->from('tbl_stages');
                $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages', 'left');
                $this->db->where_in('tbl_stages.id', $arrStageId, false);
                $listStages = $this->db->get()->result_array();
                if (!empty($listStages)) {
                    $listStages = array_reduce($listStages, function ($result, $item) {
                        $result[$item['id']] = $item;
                        return $result;
                    });
                }
            }

            //báo cáo vi phạm
            $this->db->select('
                tblproduction_report.id_production_orders as id_production_orders,
                tblproduction_report.reference_no as reference_no,
            ', false);
            $this->db->from('tblproduction_report');
            $this->db->where_in('tblproduction_report.id_production_orders', $arrPOId, false);
            $listProductionReport = $this->db->get()->result_array();
            if (!empty($listProductionReport)) {
                $listProductionReport = array_reduce($listProductionReport, function ($result, $item) {
                    $result[$item['id_production_orders']][] = $item;
                    return $result;
                });
            }
        }

        $aColumns = handlingColumns($aColumns);
        $stt = $this->input->post('start');
        foreach ($rResult as $kRow => $aRow) {
            $stt++;

            $po_id = $aRow['id'];
            $machine_id = $aRow['qui_cach_van_hanh'];
            $qui_cach_van_hanh = '';
            $ma_cong_doan_thiet_bi = '';
            $ten_cong_doan_thiet_bi = '';
            $dinh_muc_nang_suat_thang = '';
            $dinh_muc_nang_suat_gio = '';
            $machine = $listMachines[$machine_id] ?? null;
            if (!empty($machine)) {
                $qui_cach_van_hanh = $machine['operating_gauge'];
                $ma_cong_doan_thiet_bi = $machine['machine_code'];
                $ten_cong_doan_thiet_bi = $machine['machine_name'];
                $dinh_muc_nang_suat_thang = $machine['product_in_month'];
                $dinh_muc_nang_suat_gio = $machine['quota_productivity'];
            }

            $product_id = $aRow['ten_sp'];
            $product = $listProducts[$product_id] ?? null;
            $ten_sp = $product['name'] ?? '';

            $bom = $listBoms[$po_id] ?? null;
            if (FIX_QUANTITY_COMPENSATION) {
                $arrCountItems = [];
                if (!empty($bom)) {
                    foreach ($bom as $key => $value) {
                        $strKey = $value['type'].'__'.$value['item_id'];
                        if (!empty($arrCountItems[$strKey])) {
                            $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                        } else {
                            $arrCountItems[$strKey]['count'] = 1;
                            $arrCountItems[$strKey]['decimal'] = 0;
                        }
                    }
                }
            }

            $number_children_size = '';
            $total_quantity_single = 0;
            $total_quantity_compensation = 0;
            $so_con_lan_van_hanh = 0;
            $tong_so_lan_van_hanh = 0;
            $sl_bu_hao_theo_bom = 0;
            $sl_bu_hao_thuc_te = 0;
            if (!empty($bom)) {
                foreach ($bom as $key => $value) {
                    $item_id = $value['item_id'];
                    $type = $value['type'];
                    $height = 0;
                    $mode = '';
    
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($value['plan_id'], $value['item_id'], $value['type']);
                    $quantity_compensation_bom = $value['quantity_compensation'];
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];
                    //fix quantity compensation
                    if (FIX_QUANTITY_COMPENSATION) {
                        $strKey = $value['type'].'__'.$value['item_id'];
                        $count_item = $arrCountItems[$strKey]['count'];
                        $division = $quantity_compensation/$count_item;
                        if (is_decimal($division)) {
                            if ($arrCountItems[$strKey]['decimal']) {
                                $quantity_compensation = floor($division);
                            } else {
                                $arrCountItems[$strKey]['decimal'] = 1;
                                $quantity_compensation = ceil($division);
                            }
                        } else {
                            $quantity_compensation = $division;
                        }
                    }
                    //
    
                    $quantity = ceil(round($value['quantity'], 3));
                    $quantity_single = $value['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need/$quantity_single) : 0;

                    $number_children_size = !empty($value['number_children_size']) ? $value['number_children_size'] : $number_children_size;
                    $total_quantity_single+= $quantity_single;
                    $total_quantity_compensation+= $quantity_compensation;

                    if ($value['unit_bom'] == 'tờ') {
                        $so_con_lan_van_hanh = $number_children_size;
                        $tong_so_lan_van_hanh = $quantity_single;
                        if ($tong_so_lan_van_hanh == 1) {
                            $tong_so_lan_van_hanh = 1;
                        } else if ($tong_so_lan_van_hanh == 0.5) {
                            $tong_so_lan_van_hanh = 2;
                        } else if ($tong_so_lan_van_hanh == 0.25) {
                            $tong_so_lan_van_hanh = 3;
                        }  else if ($tong_so_lan_van_hanh == 0.125) {
                            $tong_so_lan_van_hanh = 4;
                        }
                    }

                    $sl_bu_hao_theo_bom+= $quantity_compensation_bom;
                    $sl_bu_hao_thuc_te+= $quantity_compensation;
                }
            }

            $stage_id = $aRow['nhom_cong_doan_thiet_bi'];
            $stage = $listStages[$stage_id] ?? null;
            $nhom_cong_doan_thiet_bi = $stage['name_category_stage'] ?? '';
            $code_stage = $stage['code'] ?? '';
            $name_stage = $stage['name'] ?? '';
            
            //mã BCKPH
            $productionReport = $listProductionReport[$po_id] ?? null;
            $arrBCKPH = [];
            if (!empty($productionReport)) {
                foreach ($productionReport as $key => $value) {
                    $arrBCKPH[] = $value['reference_no'];
                }
            }

            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'id') {
                    $row[] = '<div class="text-center">' . $stt . '</div>';
                } else if ($v == 'ngay_cap_nhat_foso') {
                    $row[] = date_format(date_create($_data), 'd/m/Y');
                } else if ($v == 'qui_cach_van_hanh') {
                    $row[] = $qui_cach_van_hanh;
                } else if ($v == 'ma_cong_doan_thiet_bi') {
                    $row[] = $code_stage.' - '.$ma_cong_doan_thiet_bi;
                } else if ($v == 'ten_cong_doan_thiet_bi') {
                    $row[] = $name_stage.' - '.$ten_cong_doan_thiet_bi;
                } else if ($v == 'ngay_dieu_do') {
                    $row[] = date_format(date_create($_data), 'd/m/Y');
                } else if ($v == 'dinh_muc_nang_suat_thang') {
                    $row[] = $dinh_muc_nang_suat_thang;
                } else if ($v == 'dinh_muc_nang_suat_gio') {
                    $row[] = $dinh_muc_nang_suat_gio;
                } else if ($v == 'ten_sp') {
                    $row[] = $ten_sp;
                } else if ($v == 'tong_so_luong') {
                    $row[] = '<div class="text-right">' . formatNumber($_data) . '</div>';
                } else if ($v == 'nhom_cong_doan_thiet_bi') {
                    $row[] = $nhom_cong_doan_thiet_bi;
                } else if ($v == 'so_con_lan_van_hanh') {
                    $row[] = $so_con_lan_van_hanh;
                } else if ($v == 'tong_so_lan_van_hanh') {
                    $row[] = $tong_so_lan_van_hanh;
                } else if ($v == 'thoi_gian_ke_hoach') {
                    $row[] = $_data ? date_format(date_create($_data), 'd/m/Y') : '';
                } else if ($v == 'thoi_gian_hoan_thanh_thuc_te') {
                    $row[] = $_data ? date_format(date_create($_data), 'd/m/Y') : '';
                } else if ($v == 'danh_gia') {
                    $row[] = '<div class="">' . ($_data == 'HT' ? 'Đạt' : 'Chưa đạt') . '</div>';
                } else if ($v == 'thoi_gian_bat_dau') {
                    $row[] = $_data ? date_format(date_create($_data), 'd/m/Y') : '';
                } else if ($v == 'thoi_gian_ket_thuc') {
                    $row[] = $_data ? date_format(date_create($_data), 'd/m/Y') : '';
                } else if ($v == 'dinh_muc_npl_canh_bai') {
                    $row[] = '<div class="text-right">' . formatNumber($sl_bu_hao_theo_bom) . '</div>';
                } else if ($v == 'npl_canh_bai_thuc_te') {
                    $row[] = '<div class="text-right">' . formatNumber($sl_bu_hao_thuc_te) . '</div>';
                } else if ($v == 'ma_bckph') {
                    $row[] = '<div class="text-center">'.implode('</br>', $arrBCKPH).'</div>';
                } 
                else {
                    $row[] = $_data;
                }
            }
            $output['aaData'][] = $row;
        }

        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function exportExcelProductivity()
    {
        $productions_orders_search = $this->input->post('productions_orders_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        if (empty($start_date_search) || empty($end_date_search)) {
            $response = array(
                'result' => 0,
                'message' => lang('Vui lòng nhập ngày bắt đầu và kết thúc'),
            );
            echo json_encode($response);
            return;
        }

        $excel = cloumns_excel();
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $excel = cloumns_excel();
        insertCompanyInfo($objPHPExcel, 'C1:P2');

        $rowBegin = 5;
        $iExcel = -1;
        
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('STT'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Điều Độ'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Nhóm Công Đoạn-Thiết Bị'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Qui Cách Vận Hành'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã Công Đoạn - Thiết Bị'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tên Công Đoạn - Thiết Bị'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Định Mức Năng Suất /Tháng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Định Mức Năng Suất /H'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Thời Gian Chuẩn Bị, Canh Bài'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Lệnh Sản Xuất'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tên SP'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tổng Số Lượng'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Số Con/Lần Vận Hành'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tổng Số Lần Vận Hành'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Thời Gian Kế Hoạch'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Thời Gian Hoàn Thành Thực Tế'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Thời Gian Bắt Đầu'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Thời Gian Kết Thúc'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Đánh Giá'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Định Mức NPL Canh Bài'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('NPL Canh Bài Thực Tế'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Mã BCKPH'));
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Ngày Cập Nhật Foso'));
        

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $excel[$iExcel] . $rowBegin)->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $thoi_gian_ket_thuc = "(
            SELECT pois.date_active
            FROM tbl_productions_orders_items_stages pois
            WHERE pois.productions_orders_id = tbl_production_lists_items.po_id AND pois.stage_id = tbl_production_lists_items.stage_id AND pois.active = 1
            LIMIT 1
        )";

        $aColumns = [
            'tbl_production_lists_items.id as id',
            'tbl_production_lists_items.ngay_now as ngay_dieu_do',
            'tbl_production_lists_items.stage_id as nhom_cong_doan_thiet_bi',
            'tbl_production_lists_items.may_in as qui_cach_van_hanh',
            '"" as ma_cong_doan_thiet_bi',
            '"" as ten_cong_doan_thiet_bi',
            '"" as dinh_muc_nang_suat_thang',
            '"" as dinh_muc_nang_suat_gio',
            'tbl_production_lists_items.thoi_gian_canh_bai as thoi_gian_chuan_bi_canh_bai',
            'tbl_productions_orders.reference_no as lenh_san_xuat',
            'tbl_production_lists_items.item_id as ten_sp',
            'tbl_production_lists_items.so_luong_san_xuat as tong_so_luong',
            'tbl_production_lists_items.so_con_tren_to_in as so_con_lan_van_hanh',
            '"" as tong_so_lan_van_hanh',
            'tbl_production_lists_items.ngay_bat_dau_du_kien as thoi_gian_ke_hoach',
            'tbl_production_lists_items.ngay_hoan_thanh_in as thoi_gian_hoan_thanh_thuc_te',
            'tbl_production_lists_items.ngay_now as thoi_gian_bat_dau',
            ''.$thoi_gian_ket_thuc.' as thoi_gian_ket_thuc',
            'tbl_production_lists_items.hoan_thanh as danh_gia',
            '"" as dinh_muc_npl_canh_bai',
            '"" as npl_canh_bai_thuc_te',
            '"" as ma_bckph',
            'tbl_productions_orders.date as ngay_cap_nhat_foso'
        ];
        
        // Thiết lập dữ liệu truy vấn
        $sIndexColumn = 'id';
        $sTable       = 'tbl_production_lists_items';
        $join = [
            'INNER JOIN tbl_productions_orders ON tbl_production_lists_items.po_id = tbl_productions_orders.id'
        ];
        
        $groupByAndOrderBy = '';
        $where        = [];
        
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
        }

        if (!empty($productions_orders_search)) {
            $productions_orders_search = $this->db->escape($productions_orders_search);
            array_push($where, "AND tbl_production_lists_items.po_id = $productions_orders_search");
        }
        
        $groupByAndOrderBy = 'ORDER BY tbl_production_lists_items.id DESC';
        
        // Thực hiện truy vấn
        $query = "SELECT " . implode(', ', $aColumns) . " FROM $sTable " . implode(' ', $join) . " WHERE 1 " . implode(' ', $where) . " $groupByAndOrderBy";
        $rResult = $this->db->query($query)->result_array();
        if (!empty($rResult)) {
            $arrPOId = [];
            $arrMayIn = [];
            $arrItemId = [];
            $arrStageId = [];
            foreach ($rResult as $key => $value) {
                $arrPOId[] = $value['id'];
                $arrItemId[] = $value['ten_sp'];

                if (!empty($value['qui_cach_van_hanh'])) {
                    $arrMayIn[] = $value['qui_cach_van_hanh'];
                }

                if (!empty($value['nhom_cong_doan_thiet_bi'])) {
                    $arrStageId[] = $value['nhom_cong_doan_thiet_bi'];
                }
            }

            if (!empty($arrMayIn)) {
                $arrMayIn = array_unique($arrMayIn);

                $this->db->select('
                    tbl_machines.id as id,
                    tbl_machines.operating_gauge as operating_gauge,
                    tbl_machines.code as machine_code,
                    tbl_machines.name as machine_name,
                    tbl_machines.product_in_month as product_in_month,
                    tbl_machines.quota_productivity as quota_productivity
                ', false);
                $this->db->from('tbl_machines');
                $this->db->where_in('tbl_machines.id', $arrMayIn, false);
                $listMachines = $this->db->get()->result_array();
                if (!empty($listMachines)) {
                    $listMachines = array_reduce($listMachines, function ($result, $item) {
                        $result[$item['id']] = $item;
                        return $result;
                    });
                }
            }

            if (!empty($arrItemId)) {
                $arrItemId = array_unique($arrItemId);
                $this->db->select('
                    tbl_products.id,
                    tbl_products.code,
                    tbl_products.name,
                ', false);
                $this->db->from('tbl_products');
                $this->db->where_in('tbl_products.id', $arrItemId, false);
                $listProducts = $this->db->get()->result_array();
                if (!empty($listProducts)) {
                    $listProducts = array_reduce($listProducts, function ($result, $item) {
                        $result[$item['id']] = $item;
                        return $result;
                    });
                }
            }

            //BOM
            $this->db->select('
                tbl_productions_orders_items_sub.productions_orders_id as po_id,
                tbl_productions_orders_items_sub.type, 
                tbl_productions_orders_items_sub.item_id, 
                tbl_productions_orders_items_sub.landscape_print_size, 
                tbl_productions_orders_items_sub.number_children_size,
                tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
                MAX(tbl_productions_orders_items_sub.quantity_compensation) as quantity_compensation, 
                SUM(tbl_productions_orders_items_sub.quantity) as quantity,
                tbl_productions_orders_items_sub.quantity_single as quantity_single,
                tblunits.unit as unit_name_parent,
                unit_b.unit as unit_bom,
                tbl_productions_orders_items.plan_id as plan_id
            ', false);
            $this->db->from('tbl_productions_orders_items_sub');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_parent_id', 'left');
            $this->db->join('tblunits unit_b', 'unit_b.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_sub.productions_orders_items_id', 'left');
            $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
            $this->db->where_in('tbl_productions_orders_items_sub.productions_orders_id', $arrPOId, false);
            $this->db->group_by('tbl_productions_orders_items_sub.productions_orders_id, tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.landscape_print_size, tbl_productions_orders_items_sub.number_children_size, tbl_productions_orders_items_sub.unit_parent_id, tbl_productions_orders_items_sub.quantity_single');
            $listBoms = $this->db->get()->result_array();
            if (!empty($listBoms)) {
                $listBoms = array_reduce($listBoms, function ($result, $item) {
                    $result[$item['po_id']][] = $item;
                    return $result;
                });
            }

            //
            if (!empty($arrStageId)) {
                $arrStageId = array_unique($arrStageId);
                $this->db->select('
                    tbl_stages.id,
                    tbl_stages.code,
                    tbl_stages.name,
                    tbl_category_stages.name as name_category_stage
                ', false);
                $this->db->from('tbl_stages');
                $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages', 'left');
                $this->db->where_in('tbl_stages.id', $arrStageId, false);
                $listStages = $this->db->get()->result_array();
                if (!empty($listStages)) {
                    $listStages = array_reduce($listStages, function ($result, $item) {
                        $result[$item['id']] = $item;
                        return $result;
                    });
                }
            }

            //báo cáo vi phạm
            $this->db->select('
                tblproduction_report.id_production_orders as id_production_orders,
                tblproduction_report.reference_no as reference_no,
            ', false);
            $this->db->from('tblproduction_report');
            $this->db->where_in('tblproduction_report.id_production_orders', $arrPOId, false);
            $listProductionReport = $this->db->get()->result_array();
            if (!empty($listProductionReport)) {
                $listProductionReport = array_reduce($listProductionReport, function ($result, $item) {
                    $result[$item['id_production_orders']][] = $item;
                    return $result;
                });
            }
        }

        $aColumns = handlingColumns($aColumns);
        $stt = 0;
        foreach ($rResult as $key => $aRow) {
            $stt++;
            $po_id = $aRow['id'];
            $machine_id = $aRow['qui_cach_van_hanh'];
            $qui_cach_van_hanh = '';
            $ma_cong_doan_thiet_bi = '';
            $ten_cong_doan_thiet_bi = '';
            $dinh_muc_nang_suat_thang = '';
            $dinh_muc_nang_suat_gio = '';
            $machine = $listMachines[$machine_id] ?? null;
            if (!empty($machine)) {
                $qui_cach_van_hanh = $machine['operating_gauge'];
                $ma_cong_doan_thiet_bi = $machine['machine_code'];
                $ten_cong_doan_thiet_bi = $machine['machine_name'];
                $dinh_muc_nang_suat_thang = $machine['product_in_month'];
                $dinh_muc_nang_suat_gio = $machine['quota_productivity'];
            }

            $product_id = $aRow['ten_sp'];
            $product = $listProducts[$product_id] ?? null;
            $ten_sp = $product['name'] ?? '';

            $bom = $listBoms[$po_id] ?? null;
            if (FIX_QUANTITY_COMPENSATION) {
                $arrCountItems = [];
                if (!empty($bom)) {
                    foreach ($bom as $key => $value) {
                        $strKey = $value['type'].'__'.$value['item_id'];
                        if (!empty($arrCountItems[$strKey])) {
                            $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                        } else {
                            $arrCountItems[$strKey]['count'] = 1;
                            $arrCountItems[$strKey]['decimal'] = 0;
                        }
                    }
                }
            }

            $number_children_size = '';
            $total_quantity_single = 0;
            $total_quantity_compensation = 0;
            $so_con_lan_van_hanh = 0;
            $tong_so_lan_van_hanh = 0;
            $sl_bu_hao_theo_bom = 0;
            $sl_bu_hao_thuc_te = 0;
            if (!empty($bom)) {
                foreach ($bom as $key => $value) {
                    $item_id = $value['item_id'];
                    $type = $value['type'];
                    $height = 0;
                    $mode = '';
    
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($value['plan_id'], $value['item_id'], $value['type']);
                    $quantity_compensation_bom = $value['quantity_compensation'];
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];
                    //fix quantity compensation
                    if (FIX_QUANTITY_COMPENSATION) {
                        $strKey = $value['type'].'__'.$value['item_id'];
                        $count_item = $arrCountItems[$strKey]['count'];
                        $division = $quantity_compensation/$count_item;
                        if (is_decimal($division)) {
                            if ($arrCountItems[$strKey]['decimal']) {
                                $quantity_compensation = floor($division);
                            } else {
                                $arrCountItems[$strKey]['decimal'] = 1;
                                $quantity_compensation = ceil($division);
                            }
                        } else {
                            $quantity_compensation = $division;
                        }
                    }
                    //
    
                    $quantity = ceil(round($value['quantity'], 3));
                    $quantity_single = $value['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need/$quantity_single) : 0;

                    $number_children_size = !empty($value['number_children_size']) ? $value['number_children_size'] : $number_children_size;
                    $total_quantity_single+= $quantity_single;
                    $total_quantity_compensation+= $quantity_compensation;

                    if ($value['unit_bom'] == 'tờ') {
                        $so_con_lan_van_hanh = $number_children_size;
                        $tong_so_lan_van_hanh = $quantity_single;
                        if ($tong_so_lan_van_hanh == 1) {
                            $tong_so_lan_van_hanh = 1;
                        } else if ($tong_so_lan_van_hanh == 0.5) {
                            $tong_so_lan_van_hanh = 2;
                        } else if ($tong_so_lan_van_hanh == 0.25) {
                            $tong_so_lan_van_hanh = 3;
                        }  else if ($tong_so_lan_van_hanh == 0.125) {
                            $tong_so_lan_van_hanh = 4;
                        }
                    }

                    $sl_bu_hao_theo_bom+= $quantity_compensation_bom;
                    $sl_bu_hao_thuc_te+= $quantity_compensation;
                }
            }

            $stage_id = $aRow['nhom_cong_doan_thiet_bi'];
            $stage = $listStages[$stage_id] ?? null;
            $nhom_cong_doan_thiet_bi = $stage['name_category_stage'] ?? '';
            $code_stage = $stage['code'] ?? '';
            $name_stage = $stage['name'] ?? '';
            
            //mã BCKPH
            $productionReport = $listProductionReport[$po_id] ?? null;
            $arrBCKPH = [];
            if (!empty($productionReport)) {
                foreach ($productionReport as $key => $value) {
                    $arrBCKPH[] = $value['reference_no'];
                }
            }

            $iExcel = -1;
            $rowBegin++;
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'id') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $stt);
                } else if ($v == 'ngay_ke_hoach') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, _dt($ngay_ke_hoach));
                } else if ($v == 'ngay_cap_nhat_foso') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, date_format(date_create($_data), 'd/m/Y'));
                } else if ($v == 'qui_cach_van_hanh') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $qui_cach_van_hanh);
                } else if ($v == 'ma_cong_doan_thiet_bi') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $code_stage.' - '.$ma_cong_doan_thiet_bi);
                } else if ($v == 'ten_cong_doan_thiet_bi') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $name_stage.' - '.$ten_cong_doan_thiet_bi);
                } else if ($v == 'ngay_dieu_do') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, date_format(date_create($_data), 'd/m/Y'));
                } else if ($v == 'dinh_muc_nang_suat_thang') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $dinh_muc_nang_suat_thang);
                } else if ($v == 'dinh_muc_nang_suat_gio') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $dinh_muc_nang_suat_gio);
                } else if ($v == 'ten_sp') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $ten_sp);
                } else if ($v == 'tong_so_luong') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, formatNumber($_data));
                } else if ($v == 'nhom_cong_doan_thiet_bi') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $nhom_cong_doan_thiet_bi);
                } else if ($v == 'so_con_lan_van_hanh') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $so_con_lan_van_hanh);
                } else if ($v == 'tong_so_lan_van_hanh') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $tong_so_lan_van_hanh);
                } else if ($v == 'thoi_gian_ke_hoach') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data ? date_format(date_create($_data), 'd/m/Y') : '');
                } else if ($v == 'thoi_gian_hoan_thanh_thuc_te') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data ? date_format(date_create($_data), 'd/m/Y') : '');
                } else if ($v == 'danh_gia') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, ($_data == 'HT' ? 'Đạt' : 'Chưa đạt'));
                } else if ($v == 'thoi_gian_bat_dau') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data ? date_format(date_create($_data), 'd/m/Y') : '');
                } else if ($v == 'thoi_gian_ket_thuc') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data ? date_format(date_create($_data), 'd/m/Y') : '');
                } else if ($v == 'dinh_muc_npl_canh_bai') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, formatNumber($sl_bu_hao_theo_bom));
                } else if ($v == 'npl_canh_bai_thuc_te') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, formatNumber($sl_bu_hao_thuc_te));
                } else if ($v == 'ma_bckph') {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $tong_so_lan_van_hanh);
                    $row[] = '<div class="text-center">'.implode('</br>', $arrBCKPH).'</div>';
                }
                else {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $_data);
                }
            }
        }

        $objPHPExcel->getActiveSheet()->getStyle('A5:' . $excel[$iExcel] . ($rowBegin))->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        ]);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $excel[$iExcel] . ($rowBegin))->getAlignment()->setWrapText(true);

        foreach ($excel as $key => $value) {
            if ($value == $excel[$iExcel]) {
                break;
            }

            $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(15);
        }

        $filename = 'baocaotonghopnangsuatcongdoan' . '.xls';
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
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        echo json_encode($response);
    }
}
