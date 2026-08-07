<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .table-debt-all-result-detail tbody tr td:nth-child(5) {
        text-align: right;
    }

    .table-debt-all-result-detail tbody tr td:nth-child(6) {
        text-align: right;
    }

    .table-debt-all-result-detail tbody tr td:nth-child(7) {
        text-align: right;
    }

    .table-detail-payment tbody tr td:nth-child(10) {
        width: 100px;
    }
</style>
<div id="detail-payment" class="view-report hide">
    <table class="table table-detail-payment scroll-responsive">
        <thead>
        <tr>
            <th class="text-center"><?php echo _l('Ngày giao'); ?></th>
            <th class="text-center"><?php echo _l('Khách hàng'); ?></th>
            <th class="text-center"><?php echo _l('Mã đơn đặt'); ?></th>
            <th class="text-center"><?php echo _l('Mã TP'); ?></th>
            <th class="text-center"><?php echo _l('Tên hàng'); ?></th>
            <th class="text-center"><?php echo _l('ĐVT'); ?></th>
            <th class="text-center"><?php echo _l('Số lượng'); ?></th>
            <th class="text-center"><?php echo _l('Đơn giá'); ?></th>
            <th class="text-center"><?php echo _l('Thuế'); ?></th>
            <th class="text-center"><?php echo _l('Tiền thuế'); ?></th>
            <th class="text-center"><?php echo _l('Thành tiền'); ?></th>
            <th class="text-center"><?php echo _l('Số PGH'); ?></th>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
        <tr>
            <td colspan="3" class="bold uppercase">Tổng cộng</td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_quantity text-center bold"></td>
            <td></td>
            <td></td>
            <td class="total_amount_tax text-right bold"></td>
            <td class="total_amount text-right bold"></td>
            <td></td>
        </tr>
        </tfoot>
    </table>
</div>