<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mbot10">
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_date_creted') ?>:</div>
                            <div class="ml-at t-bold"><?= _dt($dtData['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>:</div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người lập kế thời') ?>:</div>
                            <div class="ml-at t-bold"><?= !empty($dtData['staff_id']) ? get_staff_full_name($dtData['staff_id']) : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                            $dtCategoryPlan = get_table_where('tbl_category_plan_time',
                                ['id' => $dtData['category_plan']], '', 'row_array');
                            ?>
                            <div><?= lang('Mã nhóm kế hoạch') ?>:</div>
                            <div class="ml-at t-bold"><?= !empty($dtCategoryPlan) ? $dtCategoryPlan['name'] : '' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch', ['id' => $dtData['branch_id']], '', 'row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>:</div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời gian hoàn thành') ?>:</div>
                            <div class="ml-at t-bold"><?= _dt($dtData['time_finish']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>:</div>
                            <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">

                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><i class="icon-foso fal fa-info-circle"></i><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                        <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?>
                        </label>


                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <?php if ($dtData['type'] == 3) { ?>
                                        <?php $this->load->view('admin/suggest_plan_purchase/table_view') ?>
                                    <?php } else { ?>
                                        <table id="table-items"
                                               class="table dt-tnh table-hover table-condensed table-cs-border">
                                            <thead>
                                            <tr>
                                                <?php
                                                if ($dtData['type'] == 1) {
                                                    $title_code = lang('Mã nguyên liệu');
                                                    $title_name = lang('Tên nguyên liệu');
                                                } elseif ($dtData['type'] == 2) {
                                                    $title_code = lang('Mã vật tư');
                                                    $title_name = lang('Tên vật tư');
                                                } elseif ($dtData['type'] == 3) {
                                                    $title_code = lang('Mã thiết bị');
                                                    $title_name = lang('Tên thiết bị');
                                                }
                                                ?>
                                                <th class="text-center"
                                                    style="width: 40px;"><?= lang('tnh_numbers') ?></th>
                                                <th><?= $title_code ?></th>
                                                <th><?= $title_name ?></th>
                                                <th><?= lang('Nhà cung cấp') ?></th>
                                                <th class="text-center"><?= lang('ĐVT') ?></th>
                                                <th class="text-center"><?= lang('quantity') ?></th>
                                                <th class="text-center"><?= lang('Đơn giá') ?></th>
                                                <th class="text-center"><?= lang('Thành tiền') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (!empty($dtDataItems)) { ?>
                                                <?php foreach ($dtDataItems as $key => $value) { ?>
                                                    <?php
                                                    $item_id = $value['item_id'];
                                                    $type_item = $value['type_item'];
                                                    $info = null;
                                                    $images = '';
                                                    if ($type_item == "products") {
                                                        $info = $this->products_model->rowProduct($item_id);
                                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                    } elseif ($type_item == "materials") {
                                                        $info = $this->items_model->rowMaterial($item_id);
                                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                    } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                                                        $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                    }

                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?= (++$key) ?></td>
                                                        <td>
                                                            <div class="code_item">
                                                                <?= $info['code'] ?>
                                                            </div>
                                                            <div style="color: green"><?= $value['reference_no'] ?></div>
                                                        </td>
                                                        <td>
                                                            <div class="name_item"><?= $info['name'] ?></div>
                                                        </td>
                                                        <td>
                                                            <div class="supplier_name"><?= $value['company'] ?></div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="unit_item"><?= $unit['unit'] ?></div>
                                                        </td>
                                                        <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                                        <td class="text-right"><?= formatMoney($value['price']) ?></td>
                                                        <td class="text-right"><?= formatMoney($value['amount']) ?></td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-center bold"
                                                    style="text-transform: uppercase;"><?= lang('tnh_grand_total') ?></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    <?php } ?>
                                </div>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                    if ($dtData['type'] == 1) {
                                        $history = getActivityLogByObjId($dtData['id'], 'suggest_plan_purchase_nvl');
                                    } elseif ($dtData['type'] == 2) {
                                        $history = getActivityLogByObjId($dtData['id'], 'suggest_plan_purchase_vt');
                                    } elseif ($dtData['type'] == 3) {
                                        $history = getActivityLogByObjId($dtData['id'],
                                            'suggest_plan_purchase_machines');
                                    }
                                    ?>
                                    <?php if (!empty($history)) : ?>
                                        <?php foreach ($history as $key => $value) : ?>
                                            <?php
                                            echo '<div class="feed-item">
                                                <div class="activity-text">
                                                    ' . staff_profile_image($value['staff_id'],
                                                    array('staff-profile-image-small'),
                                                    'small') . '' . $value['staff_name'] . '
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
                                <div><?= lang('tnh_created_by') ?>
                                    : <?= get_staff_full_name($dtData['created_by']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($dtData['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty(get_staff_full_name($dtData['updated_by']))) : ?>
                                    <div><?= lang('tnh_updated_by') ?>
                                        : <?= get_staff_full_name($dtData['updated_by']) ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($dtData['date_updated']) ?></div>
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
    $(document).ready(function () {
        var dtItems = $('#table-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // scrollY: true,
            // scrollX: true,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function (settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function (row, data, start, end, display) {
                var apiSub = this.api(),
                    data;
                <?php if ($dtData['type'] == 3){ ?>
                pageQuantity = apiSub
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageAmount = apiSub
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(apiSub.column(4).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantity) + '</div>');
                $(apiSub.column(6).footer()).html('<div class="text-right bold">' + tnhFormatMoney(pageAmount) + '</div>');
                <?php } else { ?>
                pageQuantity = apiSub
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageAmount = apiSub
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(apiSub.column(5).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantity) + '</div>');
                $(apiSub.column(7).footer()).html('<div class="text-right bold">' + tnhFormatMoney(pageAmount) + '</div>');
                <?php } ?>
            }
        });
    });
</script>