<style type="text/css">

</style>
<div class="modal fade in" id="viewclient" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo ($id == 1) ? 'Thống kê nợ' : 'Thống kê có' ?> </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table table-striped table-viewclient">
                            <thead>
                                <tr class="bold" style="text-align: center;font-weight: bold;">
                                    <th style="text-align: center;"><?php echo ucwords(_l('ch_date_p')); ?></th>
                                    <th style="text-align: center;"><?php echo ucwords(_l('ch_code_p')); ?></th>
                                    <th style="text-align: center;"><?php echo ucwords(_l('note')); ?></th>
                                    <?php if ($id == 1) { ?>
                                        <th style="text-align: center;"><?php echo ucwords(_l('công nợ')); ?></th>
                                    <?php } else { ?>
                                        <th style="text-align: center;"><?php echo ucwords(_l('Thu tiền')); ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td>Tổng</td>
                                    <td></td>
                                    <td></td>
                                    <td class="grand_total text-right"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            if ($.fn.DataTable.isDataTable('.table-viewclient')) {
                $('.table-viewclient').DataTable().destroy();
            }
            initDataTable('.table-viewclient', admin_url + 'reports/table_viewclient/<?= $id ?>/<?= $client_id ?>', false, false, fnServerParams, [0, 'asc']);
        });
        $('.table-viewclient').on('draw.dt', function() {
            var paymentReceivedReportsTable = $(this).DataTable();
            var sums = paymentReceivedReportsTable.ajax.json().sums;
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td.grand_total').html(sums.grand_total);
        });
    </script>