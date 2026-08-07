<?php
    $this->load->model('products_model');
    $this->load->model('unit_model');
    $this->load->model('items_model');

    $customer_id = explode('__', $customers)[1];
    $customer = $this->clients_model->rowCustomer($customer_id);

    $codeCustomer = $customer['zcode'];
    $company = $customer['company'];

    $debt_clients = debt_clients_date($customer_id, $start_date_search, $end_date_search);

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
    $this->db->order_by('tbl_deliveries.date ASC');
    $deliveries = $this->db->get()->result_array();
?>
<table class="table table-hover dataTable tnh-table" style="width: 50%;">
    <tbody>
        <tr>
            <td><?= lang('Mã KH') ?></td>
            <td colspan="2"><?= $codeCustomer ?></td>
        </tr>
        <tr>
            <td><?= lang('Tên KH') ?></td>
            <td colspan="2"><?= $company ?></td>
        </tr>
        <tr>
            <td class="bold"><?= lang('Nợ đầu kỳ') ?></td>
            <td colspan="2" class="text-right bold"><?= formatMoney($debt_clients['begin']) ?></td>
        </tr>
        <tr>
            <td rowspan="3"><?= lang('Mua trong kỳ') ?></td>
            <td><?= lang('Tiền hàng') ?></td>
            <td class="text-right"><?= formatMoney($debt_clients['total_import'] - $debt_clients['total_tax']) ?></td>
        </tr>
        <tr>
            <td><?= lang('Thuế GTGT') ?></td>
            <td class="text-right">
                <?= formatMoney($debt_clients['total_tax']) ?>
            </td>
        </tr>
        <tr>
            <td><?= lang('Khoảng giảm trừ') ?></td>
            <td class="text-right">
                <?= formatMoney($debt_clients['returns']) ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2"><?= lang('Thanh toán trong kỳ') ?></td>
            <td><?= lang('T.Mặt') ?></td>
            <td class="text-right"><?= formatMoney($debt_clients['total_payment_import']) ?></td>
        </tr>
        <tr>
            <td><?= lang('C.Khoản') ?></td>
            <td class="text-right"><?= formatMoney($debt_clients['total_payment_import_bank']) ?></td>
        </tr>
        <tr>
            <td class="bold"><?= lang('Nợ cuối kỳ') ?></td>
            <td colspan="2" class="text-right bold"><?= formatMoney($debt_clients['begin'] +  $debt_clients['total_import'] - $debt_clients['returns'] - $debt_clients['total_payment_import'] - $debt_clients['total_payment_import_bank']) ?></td>
        </tr>
    </tbody>
</table>
<hr>
<table class="table dataTable" style="width: 100%;">
    <thead>
        <tr>
            <th class="text-center"><?= lang('STT') ?></th>
            <th class="text-center"><?= lang('SỐ PHIẾU') ?></th>
            <th class="text-center"><?= lang('SỐ HÓA ĐƠN') ?></th>
            <th class="text-center"><?= lang('NGÀY') ?></th>
            <th class="text-center"><?= lang('LOẠI HÀNG') ?></th>
            <th class="text-center"><?= lang('TÊN HÀNG') ?></th>
            <th class="text-center"><?= lang('Q.CÁCH') ?></th>
            <th class="text-center"><?= lang('ĐVT') ?></th>
            <th class="text-center"><?= lang('SL') ?></th>
            <th class="text-center"><?= lang('ĐG') ?></th>
            <th class="text-center"><?= lang('TIỀN HÀNG') ?></th>
            <th class="text-center"><?= lang('CK') ?></th>
            <th class="text-center"><?= lang('THUẾ') ?></th>
            <th class="text-center"><?= lang('TỔNG TIỀN') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $iSTT = 0;
            $sumTotalQuantity = 0;
            $sumTotalAmountItems = 0;
            $sumTotalTax = 0;
            $sumTotalDiscount = 0;
            $sumGrandTotal = 0;
        ?>
        <?php if(!empty($deliveries)): ?>
            <?php foreach($deliveries as $key => $value): ?>
                <?php 
                    $iSTT++;
                    $delivery_id = $value['id'];
                    $reference_no = $value['reference_no'];
                    $date = _d($value['date']);
                    $total_quantity = $value['total_quantity'];
                    $total_amount_items = $value['total_amount_items'];
                    $total_tax = $value['total_tax'];
                    $grand_total_discount = $value['grand_total_discount'];
                    $grand_total = $value['grand_total'];

                    $sumTotalQuantity+= $total_quantity;
                    $sumTotalAmountItems+= $total_amount_items;
                    $sumTotalTax+= $total_tax;
                    $sumTotalDiscount+= $grand_total_discount;
                    $sumGrandTotal+= $grand_total;

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
                ?>
                <tr class="bold bg-danger">
                    <td class="text-center"><?= ++$key ?></td>
                    <td>
                        <?= $reference_no ?>
                    </td>
                    <td>
                        <?= $value['reference_no_invoice'] ?>
                    </td>
                    <td>
                        <?= $date ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td class="text-center"><?= formatNumber($total_quantity) ?></td>
                    <td class="text-right"></td>
                    <td class="text-right"><?= formatMoney($total_amount_items) ?></td>
                    <td class="text-right"><?= formatMoney($grand_total_discount) ?></td>
                    <td class="text-right"><?= formatMoney($total_tax) ?></td>
                    <td class="text-right"><?= formatMoney($grand_total) ?></td>
                </tr>
                <?php if(!empty($deliveryItems)): ?>
                    <?php foreach($deliveryItems as $k => $v): ?>
                        <?php
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
                            } else if ($type_item == "items") {
                                $info = $this->items_model->rowItems($item_id);
                                $unit = $this->unit_model->rowUnit($info['unit']);
                                $name = $info['name'];
                                $txtType = 'Hàng hóa';
                            } else if ($type_item == "materials") {
                                $info = $this->items_model->rowMaterial($item_id);
                                $unit = $this->unit_model->rowUnit($info['unit_id']);
                                $name = $info['name'];
                                $txtType = 'Nguyên vật liệu';
                            }

                            $total_discount_item = $v['total_discount_item'];
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td><?= $txtType ?></td>
                            <td><?= $name ?></td>
                            <td><?= $mode ?></td>
                            <td class="text-center"><?= $unit['unit'] ?></td>
                            <td class="text-center"><?= formatNumber($v['quantity']) ?></td>
                            <td class="text-right"><?= formatMoney($v['price']) ?></td>
                            <td class="text-right"><?= formatMoney($v['amount']) ?></td>
                            <td class="text-right"><?= formatMoney($total_discount_item) ?></td>
                            <td class="text-right">0</td>
                            <td class="text-right"><?= formatMoney($v['total_amount']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <tr class="bold" style="background: #ffff0057;">
            <td colspan="5"><?= lang('Tổng tiền hàng trong kỳ') ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center"><?= formatNumber($sumTotalQuantity) ?></td>
            <td></td>
            <td class="text-right"><?= formatMoney($sumTotalAmountItems) ?></td>
            <td class="text-right"><?= formatMoney($sumTotalDiscount) ?></td>
            <td class="text-right"><?= formatMoney($sumTotalTax) ?></td>
            <td class="text-right"><?= formatMoney($sumGrandTotal) ?></td>
        </tr>
        
        <?php
            $this->db->select("
                SUM(tbl_returned_goods.grand_total) total_return
            ", false);
            $this->db->from('tbl_returned_goods');
            $this->db->where('tbl_returned_goods.handling_solution', 'debt_reduction');
            $this->db->where('tbl_returned_goods.customer_id', $customer_id);
            if (!empty($start_date_search)) {
                $this->db->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") >=', to_sql_date($start_date_search));
            }
            if (!empty($end_date_search)) {
                $this->db->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") <=', to_sql_date($end_date_search));
            }
            $returned_goods = $this->db->get()->row_array();
            $totalReturnGoods = !empty($returned_goods) ? $returned_goods['total_return'] : 0;
            $grandTotalEnd = $sumGrandTotal - $totalReturnGoods;
        ?>
        <tr class="bold" style="background: #ffff0057;">
            <td colspan="5"><?= lang('Khoảng giảm trừ') ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center"></td>
            <td></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"><?= formatMoney($totalReturnGoods) ?></td>
        </tr>

        <tr class="bold" style="background: #ffff0057;">
            <td colspan="5"><?= lang('Tổng cộng') ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center"></td>
            <td></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"><?= formatMoney($grandTotalEnd) ?></td>
        </tr>
        <tr class="bold" style="background: #ddd;">
            <td colspan="14"><?= lang('Danh sách thanh toán trong kỳ') ?></td>
        </tr>
        <?php
            $grandTotalPayment = 0;
            $whereVoucherCoupon = '';
            $whereOtherPayslipsCoupon = '';
            if (!empty($start_date_search)) {
                $whereVoucherCoupon .= ' AND tblvouchers_coupon.date_vouchers >= "' . to_sql_date($start_date_search) . '"';
                $whereOtherPayslipsCoupon .= ' AND tblother_payslips_coupon.date >= "' . to_sql_date($start_date_search). '"';
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
        ?>
        <?php if(!empty($dtPayment)): ?>
            <?php foreach($dtPayment as $kPayment => $vPayment): ?>
                <tr>
                    <td class="text-center">
                        <?= ++$kPayment ?>
                    </td>
                    <td><?= _d($vPayment['date']) ?></td>
                    <td colspan="1"><?= $vPayment['code'] ?></td>
                    <td colspan="10"><?= $vPayment['note'] ?></td>
                    <td colspan="1" class="text-right"><?= formatMoney($vPayment['payment']) ?></td>
                </tr>
                <?php
                    $grandTotalPayment+= $vPayment['payment'];
                ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <tr class="bold" style="background: #ddd;">
            <td colspan="5"><?= lang('Tổng thanh toán trong kỳ') ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center"></td>
            <td></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"><?= formatMoney($grandTotalPayment) ?></td>
        </tr>
        <?php
            $duNoCuoiKy = $debt_clients['begin'] + $grandTotalEnd - $grandTotalPayment;
        ?>
        <tr class="bold" style="background: #ddd;">
            <td colspan="5"><?= lang('Dư nợ cuối kỳ') ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center"></td>
            <td></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"><?= formatMoney($duNoCuoiKy) ?></td>
        </tr>
    </tbody>
</table>
