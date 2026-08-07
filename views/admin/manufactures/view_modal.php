<style>
    #tnhModal2 {
        z-index: 10002;
    }
</style>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Thông tin mặt hàng') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="info">
                        <div class="body-modal">
                            <table class="table table table-striped table-detail-productions_plan_purchase">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo ucwords(_l('Số kế hoạch NPL')); ?></th>
                                        <th class="text-center"><?php echo ucwords(_l('Lệnh sản xuất tổng')); ?></th>
                                        <th class="text-center"><?php echo ucwords(_l('Số lượng kế hoạch')); ?></th>
                                        <th class="text-center"><?php echo ucwords(_l('Đã giữ')); ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <footer>
                                    <tr>
                                        <td>Tổng</td>
                                        <td></td>
                                        <td class="total_quanliti text-center"></td>
                                        <td class="total_transfer text-center"></td>
                                    </tr>
                                </footer>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var notSortableAndSearchableItemColumns = [];
        initDataTable('.table-detail-productions_plan_purchase', '<?= admin_url('manufactures_temp/detail_productions_plan_purchase/' . $id) ?>', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns, 'undefined', [0, 'asc']);
        $('.table-detail-productions_plan_purchase').on('draw.dt', function() {
            var invoiceReportsTable = $(this).DataTable();
            var sums = invoiceReportsTable.ajax.json().sums;
            $('.total_quanliti').text(sums.all);
            $('.total_transfer').text(sums.transfer);
        });
    </script>