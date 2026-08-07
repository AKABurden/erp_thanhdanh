    <style type="text/css">
        .img_ch {
            height: 20px;
            width: 20px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }
    </style>
    <div class="modal fade in" id="view_inventory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="book-title"><?php echo _l('ch_inventory_warehouse_t'); ?> </span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6  pull-left">
                            <div class="panel panel-success">
                                <?php
                                $type = '';
                                if (!isset($items))
                                    $type = 'warning';
                                elseif ($items->status == 1)
                                    $type = 'success';
                                elseif ($items->status == 0)
                                    $type = 'danger';
                                elseif ($items->status == 3)
                                    $type = 'success';

                                ?>
                                <div style="right: 10px;" class="ribbon <?= $type ?>" project-status-ribbon-2="">
                                    <?php
                                    if (isset($items)) {
                                        $status = format_status_inventory($items->status, '', false);
                                    } else {
                                        $status = format_status_inventory(-1, '', false);
                                    }
                                    ?>
                                    <span><?= $status ?></span>
                                </div>
                                <div class="panel-heading">
                                    <h3 class="panel-title">Thông tin</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="well well-sm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div><?= format_purchase_order_father($items->id, '', true, '12px') ?></div>
                                                <div>
                                                    <b><?= _l('ch_code_p') ?>: </b><?php echo $items->prefix . $items->code ?>
                                                </div>
                                                <div><b><?= _l('ch_staff_crate_rfq') ?>: </b><?php echo staff_profile_image($items->staff_id, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                                                'data-toggle' => 'tooltip',
                                                                                                'data-title' => get_staff_full_name($items->staff_id)
                                                                                            )) . get_staff_full_name($items->staff_id) ?></div>
                                                <div><b><?= _l('ch_date_p') ?>: </b><?php echo _d($items->date) ?></div>

                                                <p></p>
                                            </div>
                                            <div class="col-md-6">
                                                <div><b><?= _l('warehouse') ?>: </b><?php echo $items->namewareide ?></div>
                                                <div><b><?= _l('ch_note_t') ?>: </b><?php echo $items->note ?></div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $totalQuantity = 0;
                    $total = 0;
                    $total_sub = 0;
                    $totalQuantity_time = 0;
                    if (isset($items->items) && (count($items->items) > 0)) { ?>
                        <div class="table-responsive">
                            <table id="view-enquiry" class="table table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?= _l('image') ?><input type="hidden" id="itemID" value="" /></th>
                                        <th style="width: 250px;" class="text-center"><?php echo _l('ch_items_name_t'); ?></th>
                                        <th class="text-center"><?php echo _l('item_unit'); ?></th>
                                        <th class="text-center"><?php echo _l('cong_price_thinh'); ?></th>
                                        <th class="text-center"><?php echo _l('warehouse_localtion'); ?></th>
                                        <th class="text-center"><?php echo _l('item_quantity'); ?></th>
                                        <th class="text-center"><?php echo _l('ch_quantity_time'); ?></th>
                                        <th class="text-center"><?php echo _l('ch_difference'); ?></th>
                                        <th class="text-center"><?php echo _l('amount_suppliers_vnd'); ?></th>
                                        <th class="text-center"><?php echo _l('ch_handling'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items->items as $key => $value) { ?>
                                        <tr>
                                            <td class="center">
                                                <div class="preview_image text-center" style="width: auto;margin-bottom:0;margin-top:0">
                                                    <div class="display-block contract-attachment-wrapper img-<?= $value['id'] ?>">
                                                        <div>
                                                            <a href="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg')) ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                <img class="mbot5" style="border-radius: 50%;width: 2em;height: 2em;" src="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg')) ?>">
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                            <td style="width: 250px;">
                                                <?php echo $value['name_item'] . ' (' . $value['code_item'] . ')'; ?><?= (isset($value['mode']) ? '<br><span style="font-size: 10px;font-style: italic;">' . _l('ch_items_specification') . ': ' . $value['mode'] . '</span>' : '') ?><br><?= format_item_color($value['product_id'], $value['type']) ?>
                                                <div style="font-size: 11px;font-style: italic;">
                                                    <?= _l('Lot') ?>:<?= $value['lot_code'] ?>
                                                    <?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                                        <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                                        <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo $value['unit_name_stock']; ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo formatNumber($value['price']); ?>
                                            </td>
                                            <td>
                                                <?php echo $value['localtion_name_id']; ?>
                                            </td>
                                            <td class="center">
                                                <?php echo formatNumber($value['quantity']); ?>
                                            </td>
                                            <td class="center">
                                                <?php echo formatNumber($value['quantity_net']); ?>
                                            </td>

                                            <td class="center">

                                                <?php echo formatNumber(abs($value['quantity'] - $value['quantity_net']));
                                                $totalQuantity_time += abs($value['quantity'] - $value['quantity_net']);
                                                ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo formatNumber($value['amount']); ?>
                                            </td>
                                            <td>
                                                <?php echo $value['handling']; ?>
                                            </td>
                                        </tr>
                                    <?php
                                        $totalQuantity += $value['quantity'];
                                        $total += $value['quantity_net'];
                                        $total_sub += $value['amount'];
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab"><?= _l('ch_information') ?></a>
                        </li>
                        <li role="presentation">
                            <a href="#item_activity" aria-controls="item_activity" role="tab" data-toggle="tab"><?= _l('activity_log_puchases') ?></a>
                        </li>
                    </ul>
                    <div role="tabpanel" class="tab-pane active" id="item_info">
                        <div id="bottom-total" class="well well-sm" style="margin-bottom: 5px;">
                            <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                                <tbody>
                                    <tr class="success">
                                        <td><?= _l('item_quantity_all') ?>:<span class="pull-right"><?= formatNumber($totalQuantity) ?></span></td>
                                        <td><?= _l('ch_quantity_time_total') ?>:<span class="pull-right"><?= formatNumber($total) ?></span></td>
                                        <td><?= _l('ch_difference_total') ?>:<span class="pull-right"><?= formatNumber($totalQuantity_time) ?></span></td>
                                        <td><?= _l('cong_info_money') ?>:<span class="pull-right"><?= formatNumber($total_sub) ?></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="item_activity">
                        <div class="activity-container">
                            <?php foreach ($dataLog as $key => $value) { ?>
                                <div class="feed-item">
                                    <div class="activity-text">
                                        <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?> <?= get_staff_full_name($value['staff_id']); ?>
                                    </div>
                                    <div class="activity-time">
                                        <?= time_ago($value['date']) ?> <span class="activity-module"><?= _l($value['table_obj']) ?></span>
                                    </div>
                                    <div>
                                        <?= $value['content'] ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.tip').tooltip();
            });
            $('body').on('hidden.bs.modal', '#view_inventory', function() {
                $('#import_data').html('');
                tAPI.draw('page');
            });
            $(document).ready(function() {
                var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
                dtItems = $('#view-enquiry').DataTable({
                    "language": app.lang.datatables,
                    "pageLength": app.options.tables_pagination_limit,
                    "lengthMenu": [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "<?= lang('all') ?>"]
                    ],
                    scrollY: '250px',
                    scrollX: true,
                    // fixedColumns:   {
                    //     leftColumns: 4,
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
                    }
                });
                setTimeout(function() {
                    dtItems.draw('page');
                }, 150);
            });
        </script>

    </div>