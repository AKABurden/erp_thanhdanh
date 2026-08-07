<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .table-general-synthetic-purchase-report thead tr th {
        text-align: center;
    }
    .table-general-synthetic-purchase-report tbody tr td:nth-child(2) {
        white-space: inherit;
        min-width: 100px;
    }
    .table-general-synthetic-purchase-report tbody tr td:nth-child(3) {
        white-space: inherit;
        min-width: 100px;
    }

    .table-general-synthetic-purchase-report tbody tr td:nth-child(4) {
        white-space: inherit;
        min-width: 250px;
    }


    .table-general-synthetic-purchase-report tbody tr td:nth-child(5) {
        white-space: inherit;
        min-width: 120px;
    }

    .table-general-synthetic-purchase-report tbody tr td:nth-child(6) {
        white-space: inherit;
        min-width: 150px;
    }

</style>
<div id="general-detail-import-report" class="hide">
    <table class="table table-general-detail-import-report scroll-responsive">
        <thead>
        <tr>
            <th class="text-center"><?php echo ucwords(_l('Ngày đề xuất')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('Mã chứng từ')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('Mã NCC')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('Tên NCC')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('code_item')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('name_item')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('item_unit')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('Số lượng yêu cầu')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('Số lượng đã đặt')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('Đơn giá')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('Thành tiền')); ?></th>
            <th class="text-center"><?php echo ucwords(_l('note')); ?></th>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
        <td colspan="7"><?= _l('invoice_dt_table_heading_amount') ?></td>
        <td class="quantityyc text-center"></td>
        <td class="quantitydt text-center"></td>
        <td></td>
        <td class="grand_total text-right"></td>
        <td class=""></td>
        </tfoot>
    </table>
</div>