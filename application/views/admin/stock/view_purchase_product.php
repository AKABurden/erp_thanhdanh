<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('purchase_products') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mbot10">
                    <?php
                    $edit = $this->perEditPurchaseProducts ? '<a class="tnh-edit" title="' . lang('edit') . '" href="' . base_url('admin/stock/edit_purchase_product/' . $purchaseProduct['id']) . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('edit') . ' ' . lang('tnh_purchase_warehouse') . '</a>' : '';

                    $print = $this->perPrintPurchaseProducts ? '<a href="' . base_url('admin/stock/print_purchase_product/' . $purchaseProduct['id']) . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('tnh_purchase_warehouse') . '</a>' : '';

                    $delete = $this->perDeletePurchaseProducts ? '<a type="button" class="po tnh-delete" title="' . lang('delete') . '" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                            <button href=\'' . base_url('admin/stock/delete_purchase_product/' . $purchaseProduct['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('tnh_purchase_warehouse') . '</a>' : '';

                    $actions = '
                        <div class="dropdown pull-right">
                            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                            ' . lang('actions') . '
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                                <li>' . $print . '</li>
                                <li class="not-outside">' . $delete . '</li>
                            </ul>
                        </div>';
                    if ($this->input->get('view') != 'seen') {
                        echo $actions;
                    }
                    ?>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_date_creted') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($purchaseProduct['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_purchase_products') ?>: </div>
                            <div class="ml-at t-bold"><?= $purchaseProduct['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_lsxct') ?>: </div>
                            <div class="ml-at t-bold"><?= $pod['reference_no'] ?></div>
                        </div>
                        <?php if (!empty($task)) : ?>
                            <div class="row-contro">
                                <div><?= lang('tnh_task') ?>: </div>
                                <div class="ml-at t-bold"><?= $task['name'] ?> - <?= $shiftWork['name'] ?></div>
                            </div>
                        <?php endif; ?>
                        <div class="row-contro">
                            <div><?= lang('tnh_status') ?>: </div>
                            <div class="ml-at t-bold"><?= lang($purchaseProduct['status']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Vượt') ?>: </div>
                            <div class="ml-at t-bold"><?= $purchaseProduct['is_pass'] ? 'Có' : 'Không' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_warehouses') ?>: </div>
                            <div class="ml-at t-bold"><?= $warehouse['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_costing') ?>: </div>
                            <div class="ml-at t-bold"><?php if ($purchaseProduct['grand_total'] > 0) : ?>
                                    <?= lang('tnh_calculated_cost') ?>
                                <?php else : ?>
                                    <?= lang('tnh_uncalculated_cost') ?>
                                <?php endif ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>: </div>
                            <div class="ml-at t-bold"><?= $purchaseProduct['note'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_created_by') ?>: </div>
                            <div class="ml-at t-bold"><?= $created_by ?></div>
                        </div>

                        <?php if (!empty($updated_by)) : ?>
                            <div class="row-contro">
                                <div><?= lang('tnh_updated_by') ?>: </div>
                                <div class="ml-at t-bold"><?= $updated_by ?></div>
                            </div>
                            <div class="row-contro">
                                <div><?= lang('tnh_date_updated') ?>: </div>
                                <div class="ml-at t-bold"><?= _dt($purchaseProduct['date_updated']) ?></div>
                            </div>
                        <?php endif ?>

                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">

                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><i class="icon-foso fal fa-info-circle"></i><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <?php if ($this->input->get('view') != 'seen') : ?>
                            <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                            <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?></label>
                        <?php endif ?>


                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-items" class="table dt-tnh table-hover table-condensed table-cs-border">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 40px;"><?= lang('tnh_numbers') ?></th>
                                                <th style="width: 60px;"><?= lang('tnh_images') ?></th>
                                                <th><?= lang('tnh_product_code') ?></th>
                                                <th><?= lang('tnh_product_name') ?></th>
                                                <th class="text-center"><?= lang('tnh_conversion_unit') ?></th>
                                                <th><?= lang('tnh_location_warehouse') ?></th>
                                                <th class="text-center"><?= lang('quantity') ?></th>
                                                <th style="width: 100px;"><?= lang('tnh_unit_exchange') ?></th>
                                                <th class="text-center"><?= lang('cost_price') ?></th>
                                                <th class="text-center"><?= lang('tnh_subtotal') ?></th>
                                                <th class="text-center"><?= lang('tnh_note') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?= $bodyItems ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-center bold" style="text-transform: uppercase;"><?= lang('tnh_grand_total') ?></th>
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
                                </div>
                            </section>
                            <?php if ($this->input->get('view') != 'seen') : ?>
                                <section id="view-activity-log" class="tab-panel">
                                    <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                        <?php
                                        $history = getActivityLogByObjId($purchaseProduct['id'], 'purchase_products');
                                        ?>
                                        <?php if (!empty($history)) : ?>
                                            <?php foreach ($history as $key => $value) : ?>
                                                <?php
                                                echo '<div class="feed-item">
                                                    <div class="activity-text">
                                                        ' . staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small') . '' . $value['staff_name'] . '
                                                    </div>
                                                    <div class="activity-time">
                                                        ' . time_ago($value['date']) . '<span class="activity-module">' . _l($value['type_parent_obj']) . '</span>
                                                    </div>
                                                    <div>
                                                        ' . $value['content'] . '
                                                    </div>
                                                </div>';
                                                ?>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </div>
                                </section>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($purchaseProduct['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($updated_by)) : ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($purchaseProduct['date_updated']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
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
            "lengthMenu": dataTableLengthMenu(),
            // scrollY: true,
            // scrollX: true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var apiSub = this.api(),
                    data;
                pageQuantity = apiSub
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageAmount = apiSub
                    .column(9, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(apiSub.column(6).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantity) + '</div>');
                $(apiSub.column(9).footer()).html('<div class="text-right bold">' + tnhFormatMoney(pageAmount) + '</div>');
            }
        });
    });
</script>