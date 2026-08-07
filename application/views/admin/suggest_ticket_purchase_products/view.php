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
                            <div><?= lang('tnh_date_creted') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($dtSuggestPurchase['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtSuggestPurchase['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                                $dtPod = get_table_where('tbl_productions_orders_details',['id' => $dtSuggestPurchase['pod_id']],'','row_array');
                            ?>
                            <div><?= lang('tnh_lsxct') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtPod['reference_no'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch',['id' => $dtSuggestPurchase['branch_id']],'','row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Ngày nhập') ?>: </div>
                            <div class="ml-at t-bold"><?= _dhau($dtSuggestPurchase['date_import']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtSuggestPurchase['note'] ?></div>
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
                        <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?></label>


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
                                            <th class="text-center"><?= lang('ĐVT') ?></th>
                                            <th class="text-center"><?= lang('quantity') ?></th>
                                            <th class="text-center"><?= lang('Số kiện') ?></th>
                                            <th class="text-center"><?= lang('Số Kg') ?></th>
                                            <th class="text-center"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (!empty($dtSuggestPurchaseItems)){ ?>
                                            <?php foreach ($dtSuggestPurchaseItems as $key => $value){ ?>
                                                <?php
                                                $item_id = $value['item_id'];
                                                $type_item = $value['type_item'];
                                                $info = null;
                                                $images = '';
                                                if ($type_item == "products") {
                                                    $info = $this->products_model->rowProduct($item_id);
                                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                    if (!empty($info['images'])) {
                                                        $images = base_url('uploads/products/' . $info['images']);
                                                    }
                                                }
                                                if (empty($images)) {
                                                    $images = base_url('assets/images/tnh/no_image.png');
                                                }

                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= (++$key) ?></td>
                                                    <td><div class="td-image">
                                                            <div class="preview_image" style="width: auto;">
                                                                <div class="display-block contract-attachment-wrapper img">
                                                                    <div style="width:45px;">
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
                                                    <td><div class="code_item">
                                                            <?= $info['code'] ?>
                                                        </div>
                                                    </td>
                                                    <td><div class="name_item"><?= $info['name'] ?></div></td>
                                                    <td class="text-center"><div class="unit_item"><?= $unit['unit'] ?></div></td>
                                                    <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                                    <td class="text-center"><?= formatNumber($value['quantity_kien']) ?></td>
                                                    <td class="text-center"><?= formatNumber($value['quantity_kg']) ?></td>
                                                    <td><div class="standard_item"><?= $value['standard'] ?></div></td>
                                                </tr>
                                                <?php } ?>
                                        <?php } ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-center bold" style="text-transform: uppercase;"><?= lang('tnh_grand_total') ?></th>
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
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                    $history = getActivityLogByObjId($dtSuggestPurchase['id'], 'suggest_ticket_purchase_product');
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
                                <div><?= lang('tnh_created_by') ?>: <?= get_staff_full_name($dtSuggestPurchase['created_by']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($dtSuggestPurchase['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty(get_staff_full_name($dtSuggestPurchase['updated_by']))) : ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= get_staff_full_name($dtSuggestPurchase['updated_by']) ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($dtSuggestPurchase['date_updated']) ?></div>
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
    $(document).ready(function() {
        var dtItems = $('#table-items').DataTable({
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
                var apiSub = this.api(),
                    data;
                pageQuantity = apiSub
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageQuantityKien = apiSub
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageQuantityKg = apiSub
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                $(apiSub.column(5).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantity) + '</div>');
                $(apiSub.column(6).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantityKien) + '</div>');
                $(apiSub.column(7).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantityKg) + '</div>');;
            }
        });
    });
</script>