<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('view') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= _dt($outsource['date']) ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span
                                class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_outsource') ?>:
                            </span>
                            <span class="bold font-medium-xs lead-name"><?= ($outsource['reference_no']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_supplies') ?>:
                            </span>
                            <span class="bold font-medium-xs lead-name"><?= $supplier['company'] ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Lệnh sản xuất tổng') ?>:
                            </span>
                            <span class="bold font-medium-xs lead-name"><?= get_po_new($outsource['pod_id']) ?></span>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Chi nhánh xưởng') ?>:
                            </span>
                            <span class="bold font-medium-xs lead-name"><?= $branch_name ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('note') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $outsource['note'] ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('status') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= lang($outsource['status']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><?= lang('tnh_items') ?></label>
                        <!-- Tab 2 -->
                        <input type="radio" name="tabset" id="tab2" aria-controls="items-export">
                        <label for="tab2"><?= lang('tnh_materials_export') ?></label>

                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <table id="table-items" class="table table-hover dont-responsive-table"
                                    style="max-height: 400px !important;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
                                            <th style="width: 60px"><?= lang('tnh_images') ?></th>
                                            <!-- <th style="width: 100px;"><?= lang('code') ?></th> -->
                                            <th style="width: 160px;"><?= lang('Tên/Mã thành phẩm') ?></th>
                                            <!-- <th style="width: 50px;"><?= lang('tnh_unit') ?></th> -->
                                            <th style="width: 170px;"><?= lang('Công đoạn') ?></th>
                                            <th style="width: 60px;"><?= lang('quantity') ?></th>
                                            <th style="width: 70px;"><?= lang('tnh_unit_price') ?></th>
                                            <th style="width: 80px;"><?= lang('tnh_subtotal') ?></th>
                                            <th style="width: 100px;"><?= lang('note') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $bodyItems ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bold">
                                            <th class="text-center" colspan="4"><?= lang('tnh_grand_total') ?></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </section>
                            <section id="items-export" class="tab-panel">
                                <div class="tb-height-more">
                                    <div class="table-responsive">
                                        <table id="tb-items-export" class="dt-tnh table table-hover"
                                            style="width: 100%;">
                                            <thead>
                                                <th class="text-center" style="width: 30px;">
                                                    <?= lang('tnh_numbers') ?>
                                                </th>
                                                <th style="width: 100px;"><?= lang('Hình ảnh') ?></th>
                                                <th style="width: 260px;"><?= lang('Tên/Mã mặt hàng') ?></th>
                                                <th style="width: 150px;"><?= lang('Loại') ?></th>
                                                <th style="width: 80px;"><?= lang('tnh_unit') ?></th>
                                                <th class="text-center" style="width: 150px;"><?= lang('quantity') ?>
                                                </th>
                                                <th class="text-center" style="width: 150px;"><?= lang('price') ?></th>
                                                <th class="text-right" style="width: 100px;"><?= lang('tnh_subtotal') ?>
                                                </th>
                                                <th style="width: 200px;"><?= lang('note') ?></th>
                                            </thead>
                                            <tbody>
                                                <?= $bodyMaterial ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="bold uppercase">
                                                    <th></th>
                                                    <th></th>
                                                    <th><?= lang('tnh_grand_total') ?></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
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
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($outsource['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($updated_by)): ?>
                                <div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
                                <div><?= lang('tnh_date_updated') ?>: <?= _dt($outsource['date_updated']) ?></div>
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
        // "lengthMenu": [
        //     [10, 25, 50, 100, -1],
        //     [10, 25, 50, 100, "<?= lang('all') ?>"]
        // ],
        // scrollY: '300px',
        scrollY: true,
        // scrollX: true,
        fixedColumns: {
            leftColumns: 0,
            rightColumns: 0
        },
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
                .column(4, {
                    page: 'current'
                })
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            pageTotalAmount = api
                .column(6, {
                    page: 'current'
                })
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            $(api.column(4).footer()).html('<div class="text-center">' + tnhFormatNumber(
                pageTotalQuantity) + '</div>');
            $(api.column(6).footer()).html('<div class="text-right">' + tnhFormatMoney(
                pageTotalAmount) + '</div>');
        }
    });

    setTimeout(function() {
        dtItems.draw();
    }, 1000);
    $('#tab1').click(function(event) {
        dtItems.draw();
    });

    var dtItemsMaterial = $('#tb-items-export').DataTable({
        "language": app.lang.datatables,
        "pageLength": app.options.tables_pagination_limit,
        // "lengthMenu": [
        //     [10, 25, 50, 100, -1],
        //     [10, 25, 50, 100, "<?= lang('all') ?>"]
        // ],
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

            pageTotalAmount = api
                .column(7, {
                    page: 'current'
                })
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            $(api.column(7).footer()).html('<div class="text-right">' + tnhFormatMoney(
                pageTotalAmount) + '</div>');
        }
    });
});
</script>