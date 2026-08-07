<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .table-general-purchase-report thead tr th {
        text-align: center;
    }

    .table-general-purchase-report tbody tr td:nth-child(2) {
        white-space: inherit;
        min-width: 300px;
    }

    .table-general-purchase-report tbody tr td:nth-child(3) {
        white-space: inherit;
        min-width: 80px;
        text-align: center;
    }

    .table-general-purchase-report tbody tr td:nth-child(4) {
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }

    .table-general-purchase-report tbody tr td:nth-child(5) {
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }

    .table-general-purchase-report tbody tr td:nth-child(6) {
        white-space: inherit;
        min-width: 110px;
        text-align: center;
    }

    .table-general-purchase-report tbody tr td:nth-child(7) {
        white-space: inherit;
        min-width: 100px;
        text-align: right;
    }

    .table-general-purchase-report tbody tr td:nth-child(8) {
        white-space: inherit;
        min-width: 110px;
        text-align: right;
    }

    .table-general-purchase-report tbody tr td:nth-child(9) {
        white-space: inherit;
        min-width: 120px;
        text-align: right;
    }
</style>
<div id="general-purchase-detail-report" class="hide">
    <table class="table table-general-purchase-detail-report scroll-responsive">
        <thead>
            <tr>
                <th class="text-center"><?php echo ucwords(_l('Ngày chứng từ')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('Mã chứng từ')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('code_item')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('name_item')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('item_unit')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('Số lượng yêu cầu')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('Số lượng đã đặt')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('Số lượng còn lại')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('Tình trạng')); ?></th>
                <th class="text-center"><?php echo ucwords(_l('note')); ?></th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <td colspan="5"><?= _l('invoice_dt_table_heading_amount') ?></td>
            <td class="quantityyc text-center"></td>
            <td class="quantitydt text-center"></td>
            <td class="quantitycl text-center"></td>
            <td class=""></td>
            <td class=""></td>
         </tfoot>
    </table>
</div>