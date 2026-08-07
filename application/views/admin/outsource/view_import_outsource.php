<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Xem nhập gia công') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= _dt($importOutsource['date']) ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span
                                class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_import_outsource') ?>:
                            </span>
                            <span class="bold font-medium-xs lead-name"><?= ($importOutsource['reference_no']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span
                                class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_outsource') ?>:
                            </span>
                            <span class="bold font-medium-xs lead-name"><?= $outsource['reference_no'] ?></span>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Kho hàng') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $warehouseTo['name'] ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('note') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $importOutsource['note'] ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('status') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= lang($importOutsource['status']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><?= lang('tnh_items') ?></label>

                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <table id="table-items" class="table table-bordered table-hover dont-responsive-table"
                                    style="max-height: 400px !important;width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                            <th style="width: 80px"><?= lang('tnh_images') ?></th>
                                            <th style="width: 200px;"><?= lang('Tên/Mã thành phẩm') ?></th>
                                            <th style="width: 140px;"><?= lang('tnh_location_warehouse') ?></th>
                                            <th style="width: 80px;"><?= lang('tnh_unit') ?></th>
                                            <th style="width: 80px;"><?= lang('quantity') ?></th>
                                            <th style="width: 100px;"><?= lang('note') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $bodyItems ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bold uppercase">
                                            <th class="text-center" colspan="3"><?= lang('tnh_grand_total') ?></th>
                                            <th></th>
                                            <th></th>
                                            <th class="text-center"></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </section>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($importOutsource['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($updated_by)): ?>
                                <div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
                                <div><?= lang('tnh_date_updated') ?>: <?= _dt($importOutsource['date_updated']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="view_order_id" id="view_order_id" class="form-control" value="<?= $id ?>">
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    var dtItems = $('#table-items').DataTable({
        "language": app.lang.datatables,
        "pageLength": app.options.tables_pagination_limit,
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "<?= lang('all') ?>"]
        ],
        // scrollY: '300px',
        scrollY: true,
        // scrollX: true,
        // fixedColumns:   {
        //     leftColumns: 3,
        //     rightColumns: 0
        // },
        // 'searching': false,
        // 'ordering': false,
        // 'paging': false,
        // "info": false,
        'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
        "initComplete": function(settings, json) {
            var t = this;
            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
        },
        "footerCallback": function(row, data, start, end, display) {
            var api = this.api(),
                data;
            pageTotalQuantity = api
                .column(5, {
                    page: 'current'
                })
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            $(api.column(5).footer()).html('<div class="text-center">' + tnhFormatNumber(
                pageTotalQuantity) + '</div>');
        }
    });

    setTimeout(function() {
        dtItems.draw();
    }, 1000);
});
</script>