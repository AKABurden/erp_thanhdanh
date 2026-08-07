<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div class="modal-dialog modal-lg" style="width: 70%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('sum_returned_goods') ?></h4>
		</div>
		<div class="modal-body">
            <div class="row">
                <div class="row-modal">
                    <div class="row-group">
                        <div class="lead-view" id="leadViewWrapper">
                            <div class="row-contro firt">
                                <div><?= lang('date') ?>: </div>
                                <!-- <div><?= lang('date') ?>: </div> -->
                                <div class="ml-at t-bold"><?= _dt($returned_goods['date']) ?></div>
                            </div>
                            <div class="row-contro second">
                                <div><?= lang('tnh_reference_no_returned_goods') ?>: </div>
                                <!-- <div><?= lang('tnh_reference_no_returned_goods') ?>: </div> -->
                                <div class="ml-at t-bold"><?= ($returned_goods['reference_no']) ?></div>
                            </div>
                            <div class="row-contro firt">
                                <div><?= lang('customers') ?>: </div>
                                <!-- <div><?= lang('customers') ?>: </div> -->
                                <div class="ml-at t-bold"><?= $returned_goods['customer_name'] ?></div>
                            </div>
                            <div class="row-contro second">
                                <div><?= lang('tnh_reference_orders') ?>: </div>
                                <!-- <div><?= lang('tnh_reference_orders') ?>: </div> -->
                                <div class="ml-at t-bold"><?= !empty($order['reference_no']) ? $order['reference_no'] : '' ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="row-group">
                        <div class="lead-view" id="leadViewWrapper">
                            <div class="row-contro firt">
                                <div><?= lang('tnh_handling_solution') ?>: </div>
                                <!-- <div><?= lang('tnh_handling_solution') ?>: </div> -->
                                <div class="ml-at t-bold"><?= lang('tnh_'.$returned_goods['handling_solution']) ?></div>
                            </div>
                            <div class="row-contro second">
                                <div><?= lang('tnh_employees') ?>: </div>
                                <!-- <div><?= lang('tnh_employees') ?>: </div> -->
                                <div class="ml-at t-bold"><?= $employee ?></div>
                            </div>
                            <div class="row-contro firt">
                                <div><?= lang('note') ?>: </div>
                                <!-- <div><?= lang('note') ?>: </div> -->
                                <div class="ml-at t-bold"><?= $returned_goods['note'] ?></div>
                            </div>
                            <div class="row-contro second">
                                <div><?= lang('status') ?>: </div>
                                <!-- <div><?= lang('status') ?>: </div> -->
                                <div class="ml-at t-bold"><?= $returned_goods['warehouseman_id'] > 0 ? lang('tnh_approved') : lang('tnh_un_approved') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><i class="icon-foso fal fa-info-circle"></i><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                        <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?></label>

                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <table id="table-items" class="table table-breturned_goodsed table-hover dont-responsive-table" style="max-height: 400px !important;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
                                            <th class="text-center" style="width: 80px"><?= lang('tnh_images') ?></th>
                                            <th class="text-center" style="width: 150px;"><?= lang('tnh_product_code') ?></th>
                                            <th class="text-center" style="width: 150px;"><?= lang('tnh_product_name') ?></th>
                                            <th class="text-center" style="width: 50px;"><?= lang('tnh_unit') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('quantity') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('tnh_unit_price') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('tnh_total_amount') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_loss') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('tnh_sample_quantity') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('tnh_grand_total') ?></th>
                                            <th class="text-center" style="width: 100px;"><?= lang('note') ?></th>
                                            <th class="text-center" style="width: 150px;"><?= lang('tblwarehouse') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $bodyItems ?>
                                    </tbody>
                                    <tfoot class="bold">
                                        <tr>
                                            <th class="text-center" style="text-transform: uppercase;" colspan="3"><?= lang('tnh_grand_total') ?></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                        $history = getActivityLogByObjId($returned_goods['id'], 'returned_goods');
                                    ?>
                                    <?php if (!empty($history)): ?>
                                        <?php foreach ($history as $key => $value): ?>
                                            <?php
                                                echo '<div class="feed-item">
                                                    <div class="activity-text">
                                                        '.staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small').''.$value['staff_name'].'
                                                    </div>
                                                    <div class="activity-time">
                                                        '.time_ago($value['date']).'<span class="activity-module">'._l($value['type_parent_obj']).'</span>
                                                    </div>
                                                    <div>
                                                        '.$value['content'].'
                                                    </div>
                                                </div>';
                                            ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-6">
                    <table class="table tnh-tb table-bordered table-hover" style="margin-top: 10px;">
                        <tbody>
                            <tr>
                                <td style="width: 40%;"><?= lang('tax', 'tax') ?></td>
                                <td class="text-right"><?= $returned_goods['tax_name'] ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('tnh_discount_percent', 'discount_percent') ?></td>
                                <td class="text-right">
                                    <div><?= $returned_goods['discount_percent'] ?>%</div>
                                    <div><?= formatMoney($returned_goods['total_discount_percent']) ?></div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('tnh_discount_direct', 'discount_direct') ?></td>
                                <td class="text-right"><?= formatMoney($returned_goods['total_discount_direct']) ?></td>
                            </tr>
                            <tr class="success" style="font-weight: 700;">
                                <td><?= lang('tnh_grand_total', 'grand_total') ?></td>
                                <td class="td-grand-total-all text-right"><?= formatMoney($returned_goods['grand_total']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($returned_goods['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($updated_by)): ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($returned_goods['date_updated']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<input type="hidden" name="view_returned_goods_id" id="view_returned_goods_id" class="form-control" value="<?= $id ?>">
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
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            scrollX: true,
            scrollY: true,
            fixedColumns:   {
                leftColumns: 3,
                rightColumns: 0
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                pageTotalQuantity = api
                    .column( 5, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                pageTotalAmount = api
                    .column( 7, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                pageGrandTotalAmount = api
                    .column( 10, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                $( api.column( 5 ).footer() ).html('<div class="text-center">'+tnhFormatNumber(pageTotalQuantity)+'</div>');
                $( api.column( 7 ).footer() ).html('<div class="text-right">'+tnhFormatMoney(pageTotalAmount)+'</div>');
                $( api.column( 10 ).footer() ).html('<div class="text-right">'+tnhFormatMoney(pageGrandTotalAmount)+'</div>');
            }
        });
    });
</script>