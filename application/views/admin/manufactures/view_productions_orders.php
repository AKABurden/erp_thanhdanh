<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_view_productions_orders') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mbot10">
                    <?php
                    $edit = $this->perEditProductionsOrders ? '<a class="" title="' . lang('edit') . '" href="' . base_url('admin/manufactures/edit_productions_orders/' . $productions_orders['id']) . '"><i class="fa fa-pencil width-icon-actions"></i> ' . lang('edit') . ' ' . lang('tnh_command') . '</a>' : '';

                    $delete = $this->perDeleteProductionsOrders ? '<a type="button" class="po" title="' . lang('delete') . '" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                            <button href=\'' . base_url('admin/manufactures/delete_productions_orders/' . $productions_orders['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('tnh_command') . '</a>' : '';

                    $print = '<a href="' . base_url('admin/manufactures/print_productions_orders/' . $productions_orders['id']) . '" target="_blank"><i class="fa fa-print"> </i> ' . lang('print') . ' ' . lang('tnh_command') . '</a>';

                    $created_productions_detail = $this->perAddProductionsOrders ? '<a class="tnh-modal created-detail" data-tnh="modal" data-toggle="modal" data-target="#myModal" title="' . lang('created_productions_detail') . '" href="' . base_url('admin/manufactures/created_productions_detail/' . $productions_orders['id']) . '"><i class="fa fa-plus width-icon-actions"></i> ' . lang('created_productions_detail') . '</a>' : '';

                    $deleted_productions_detail = $this->perDeleteProductionsOrders ? '<a type="button" data-toggle="popover" class="po-delete delete-detail" title="' . lang('delete') . '" data-container="body" data-html="true" data-placement="left" data-content="
                        <button href=\'' . base_url('admin/manufactures/delete_productions_orders_detail/' . $productions_orders['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close-new\'>' . lang('close') . '</button>
                        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_productions_order_detail') . '</a>' : '';

                    $actions = '
                        <div class="dropdown pull-right">
                            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                            ' . lang('actions') . '
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                                <li>' . $created_productions_detail . '</li>
                                <li>' . $print . '</li>
                                <li class="not-outside">' . $delete . '</li>
                            </ul>
                        </div>';
                    echo $actions;
                    ?>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= _dt($productions_orders['date']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_productions_orders') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $productions_orders['reference_no'] ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_location') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $location['name'] ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('ĐHB/KHBTP') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $productions_orders['productions_plan_reference_no'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('note') ?>: </span>
                            <span class="bold font-medium-xs lead-name"></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_status') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= lang($productions_orders['status']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_user_agree') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $user_status ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><?= lang('tnh_items') ?></label>
                        <!-- Tab 2 -->
                        <input type="radio" name="tabset" id="tab2" aria-controls="view-bom">
                        <label for="tab2"><?= lang('tnh_plan_material') ?></label>
                        <!-- Tab 5 -->
                        <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                        <label for="tab5"><?= lang('activity_log_puchases') ?></label>

                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-items" class="dt-table table table-hover dont-responsive-table" style="max-height: 400px !important;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
                                                <th class="text-center" style="width: 80px;"><?= lang('tnh_images') ?></th>
                                                <th><?= lang('tnh_product_code') ?></th>
                                                <th><?= lang('tnh_product_name') ?></th>
                                                <th class="text-center"><?= lang('quantity') ?></th>
                                                <th class="text-center"><?= lang('tnh_quantity_finished') ?></th>
                                                <th class="text-center"><?= lang('tnh_order_finised') ?></th>
                                                <th><?= lang('note') ?></th>
                                                <th class="hide"><?= lang('sub') ?></th>
                                                <th class="text-center"><?= lang('actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $trIndex = 0; ?>
                                            <?php foreach ($productions_orders_items as $key => $value) : ?>
                                                <?php
                                                $production_plan = '';
                                                if (!empty($value['production_plan_item_id'])) {
                                                    $production_plan = $this->manufactures_model->rowProductionsPlanByItem($value['production_plan_item_id'])['reference_no'];
                                                }
                                                $podI = $this->manufactures_model->rowProductionsOrdersDetailsByPOI($value['id']);
                                                $images = base_url() . 'assets/images/tnh/no_image.png';
                                                if (!empty($value['images'])) {
                                                    $images = base_url('uploads/products/' . $value['images']);
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <?= (++$key) ?>
                                                    </td>
                                                    <td>
                                                        <div class="td-image">
                                                            <div class="preview_image" style="width: auto;">
                                                                <div class="display-block contract-attachment-wrapper img">
                                                                    <div style="width:45px; margin: auto;">
                                                                        <a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                            <div class="">
                                                                                <img src="<?= $images ?>" style="border-radius: 50%">
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?= $value['item_code'] ?>
                                                        <?php if (!empty($podI)) : ?>
                                                            <div>
                                                                <a target="_blank" href="<?= base_url('admin/manufactures/detail_productions/' . $podI['id']) ?>"><i class="fa fa-file-text-o"></i> <?= $podI['reference_no'] ?></a>
                                                            </div>
                                                        <?php endif ?>
                                                    </td>
                                                    <td>
                                                        <?= $value['item_name'] ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= formatNumber($value['quantity']) ?>
                                                    </td>
                                                    <td class="text-center txt-quantity-finished">
                                                        <?php echo !empty($podI) ? formatNumber($podI['quantity_warehoused']) : 0 ?>
                                                    </td>
                                                    <td class="text-center txt-finished-<?= !empty($podI) ? $podI['id'] : 0 ?>">
                                                        <?php if (!empty($podI)) : ?>
                                                            <?php
                                                            $now = date('Y-m-d');
                                                            $deadline = $podI['deadline'];
                                                            $dateDiff = minusDateNotFormat($deadline, $now);
                                                            if ($podI['status'] == "complete_production") {
                                                                $strStatus = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_status') . '" data-content="<p><a id=\'agree-items\' productions_orders_details_id=\'' . $podI['id'] . '\' value=\'un_produced\' class=\'btn btn-danger\'>' . lang('tnh_un_agree_finished') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('tnh_completed') . '</span></div>';
                                                            } else if ($dateDiff < 0) {
                                                                $strStatus = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_status') . '" data-content="<p><a id=\'agree-items\' productions_orders_details_id=\'' . $podI['id'] . '\' value=\'complete_production\' class=\'btn btn-success\'>' . lang('tnh_agree_finished') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('tnh_delay_progress') . '</span></div>';
                                                            } else {
                                                                $strStatus = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_status') . '" data-content="<p><a id=\'agree-items\' productions_orders_details_id=\'' . $podI['id'] . '\' value=\'complete_production\' class=\'btn btn-success\'>' . lang('tnh_agree_finished') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-warning po">' . lang('tnh_producing') . '</span></div>';
                                                            }
                                                            echo $strStatus;
                                                            ?>
                                                        <?php endif ?>
                                                    </td>
                                                    <td>
                                                        <?= $value['note_items'] ?>
                                                    </td>
                                                    <td class="hide">
                                                        <table class="tnh-table table-bordered" style="width: 90%; float: right;">

                                                            <body>
                                                                <tr>
                                                                    <td class="text-center" style="width: 80px;"><?= lang('tnh_numbers') ?></td>
                                                                    <td><?= lang('tnh_stage_name') ?></td>
                                                                    <td><?= lang('tnh_machines') ?></td>
                                                                    <td><?= lang('tnh_number_hours') ?></td>
                                                                </tr>
                                                                <?php
                                                                $stages = $this->manufactures_model->getProductionsOrdersItemsStagesView($value['id']);
                                                                ?>
                                                                <?php foreach ($stages as $k => $val) : ?>
                                                                    <tr>
                                                                        <td class="text-center"><?= (++$k) ?></td>
                                                                        <td><?= $val['stage_name'] ?></td>
                                                                        <td><?= $val['machine_name'] ?></td>
                                                                        <td><?= $val['number_hours'] ?></td>
                                                                    </tr>
                                                                <?php endforeach ?>
                                                            </body>
                                                        </table>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if (!empty($podI)) : ?>
                                                            <?php
                                                            $exportWarehouse = $this->perAddExportingProducion ? '<a target="_blank" href="' . base_url('admin/stock/add_exporting_production/' . $podI['id']) . '"><i class="fa fa-plus"></i> ' . lang('tnh_add_exporting_production') . '</a>' : '';

                                                            $warehousing = '<a class="tnh-modal2 tnh-warehousing" title="' . lang('tnh_warehousing') . '" tr-index="' . $trIndex . '" data-tnh="modal" data-toggle="modal" data-target="#myModal2" href="' . base_url('admin/manufactures/addWarehousing/' . $podI['id']) . '"><i class="fa fa-plus width-icon-actions"></i> ' . lang('tnh_warehousing') . '</a>';

                                                            $actions = '
                                                            <div class="dropdown text-center">
                                                                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                                                ' . lang('actions') . '
                                                                <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                                                                    <li>' . $exportWarehouse . '</li>
                                                                    <li>' . $warehousing . '</li>
                                                                </ul>
                                                            </div>';
                                                            echo $actions;
                                                            ?>
                                                        <?php endif ?>
                                                    </td>
                                                </tr>
                                                <?php $trIndex++; ?>
                                            <?php endforeach ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bold">
                                                <td colspan="3" class="text-center"><?= lang('tnh_grand_total') ?></th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </section>
                            <section id="view-bom" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-bom" class="dt-table table table-hover dont-responsive-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                                <th style="width: 150px;"><?= lang('type') ?></th>
                                                <th><?= lang('code') ?></th>
                                                <th><?= lang('name') ?></th>
                                                <th class="text-center"><?= lang('unit') ?></th>
                                                <th class="text-center"><?= lang('quantity') ?></th>
                                                <th class="text-center"><?= lang('tnh_exported') ?></th>
                                                <th class="text-center"><?= lang('tnh_rest') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($subs as $key => $value) : ?>
                                                <?php
                                                $quantityExport = $this->manufactures_model->getQuantityExportMaterial($value['type'], $value['item_id'], $id)['quantity_export'];
                                                $rest = $value['total_quantity'] - $quantityExport;
                                                if ($rest < 0) $rest = 0;
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <?= (++$key) ?>
                                                    </td>
                                                    <td><?= lang($value['type']) ?></td>
                                                    <td><?= $value['item_code'] ?></td>
                                                    <td><?= $value['item_name'] ?></td>
                                                    <td class="text-center"><?= $value['unit'] ?></td>
                                                    <td class="text-center"><?= formatNumber($value['total_quantity']) ?></td>
                                                    <td class="text-center"><?= formatNumber($quantityExport) ?></td>
                                                    <td class="text-center"><?= formatNumber($rest) ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bold">
                                                <td colspan="4" class="text-center"><?= lang('tnh_grand_total') ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                    $history = getActivityLogByObjId($productions_orders['id'], 'productions_orders');
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
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($productions_orders['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($updated_by)) : ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($productions_orders['date_updated']) ?></div>
                                <?php endif ?>
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
    var arr = [];
    var dtItems = '';

    function formatProductionsOrders(d) {
        sub = d[9];
        return sub;
    }

    $(document).ready(function() {
        dtItems = $('#table-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // scrollY: true,
            // scrollX: true,
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

                pageTotalQuantityFinished = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(4).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantity) + '</div>');
                $(api.column(5).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantityFinished) + '</div>');
            }
        });

        $('#table-items tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var records = tr.find('#records').val();
            var row = dtItems.row(tr);

            if (row.child.isShown()) {
                arr = removeArray(arr, records);
                row.child.hide();
                tr.removeClass('shown');
            } else {
                if (!arr.includes(records)) {
                    arr.push(records);
                }
                row.child(formatProductionsOrders(row.data())).show();
                tr.addClass('shown');
            }
        });

        var dtBOM = $('#table-bom').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // scrollY: true,
            // scrollX: true,
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

                pageTotalQuantityExport = api
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageTotalQuantityRest = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(5).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantity) + '</div>');
                $(api.column(6).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantityExport) + '</div>');
                $(api.column(7).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantityRest) + '</div>');
            }
        });
    });
</script>