<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style>
    #tnhModal2 {
        z-index: 10002;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Xem giữ kho trên chuyền') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">

                        <div class="row-contro">
                            <div><?= lang('date') ?>:</div>
                            <div class="ml-at t-bold"><?= _dt($transfer_bussiness['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_orders') ?>:</div>
                            <div class="ml-at t-bold"><?= ($transfer_bussiness['reference_no']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('note') ?>:</div>
                            <div class="ml-at t-bold"><?= $transfer_bussiness['note'] ?></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <table id="table-items" class="table table-hover dont-responsive-table" style="width: 100%;">
                            <thead>
                            <tr>
                                <th style="width: 30px;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                <th style="width: 120px;" class="text-center"><?= lang('Mã TP') ?></th>
                                <th style="width: 120px;"
                                    class="text-center"><?= lang('Tên TP') ?></th>
                                <th style="width: 50px;" class="text-center"><?= lang('Đơn hàng') ?></th>
                                <th style="width: 50px;" class="text-center"><?= lang('KHKD') ?></th>
                                <th style="width: 70px;" class="text-center"><?= lang('Số lượng') ?></th>

                            </tr>
                            </thead>
                            <tbody>
                            <?= $bodyItems ?>
                            </tbody>
                            <tfoot class="bold">
                            <tr>
                                <td class="text-center" style="text-transform: uppercase;" colspan="2">
                                    <?= lang('tnh_grand_total') ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= get_staff_full_name($transfer_bussiness['created_by']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($transfer_bussiness['date_created']) ?></div>
                            </div>
                        </div>
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
    $(document).ready(function () {
        var dtItems = $('#table-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": dataTableLengthMenu(),
            "responsive": true,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function (settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(),
                    data;

                totalQuantity = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(5).footer()).html('<div class="text-center">' + tnhFormatNumber(
                    totalQuantity) + '</div>');
            }
        });
    });

</script>
