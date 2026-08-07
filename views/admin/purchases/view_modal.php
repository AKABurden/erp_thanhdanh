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

    .well.well-sm {
        background-color: transparent;
        border: none;
    }
</style>
<div class="modal fade in" id="views_purchases" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-backdrop="static" data-keyboard="false" aria-hidden="false" style="display: block;">
    <div class="modal-dialog modal-lg no-modal-header" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('ch_purchases_detail'); ?> </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="info">
                            <?php
                            $type = '';
                            if (!isset($purchase)) {
                                $type = 'warning';
                            } elseif ($purchase->status == 1) {
                                $type = 'warning';
                            } elseif ($purchase->status == 2) {
                                $type = 'danger';
                            } elseif ($purchase->status == 3) {
                                $type = 'success';
                            } elseif ($purchase->status == 4) {
                                $type = 'danger';
                            }
                            ?>

                            <div style="right: 10px;" class="ribbon <?= $type ?>" project-status-ribbon-2="">
                                <?php
                                if (isset($purchase)) {
                                    $status = format_purchase_status($purchase->status, '', false);
                                } else {
                                    $status = format_purchase_status(-1, '', false);
                                }
                                ?>
                                <span><?= $status ?></span>
                            </div>
                            <div class="title-modal">
                                <h3>Thông tin</h3>
                            </div>
                            <div class="body-modal">
                                <div class="row-modal">
                                    <div class="row-group">
                                        <div class="row-contro">
                                            <div><?= _l('ch_code_p') ?>:</div>
                                            <div class="ml-at t-bold"><?php echo $purchase->prefix . $purchase->code ?>
                                            </div>
                                        </div>
                                        <div class="row-contro">
                                            <div><?= _l('ch_date_p') ?>:</div>
                                            <div class="ml-at t-bold"><?php echo _dt($purchase->date) ?></div>
                                        </div>
                                        <div class="row-contro">
                                            <div><?= _l('ch_name_p') ?>:</div>
                                            <div class="ml-at t-bold"><?php echo $purchase->name_purchase ?></div>
                                        </div>
                                        <div class="row-contro">
                                            <div><?= _l('ch_note_t') ?>:</div>
                                            <div class="ml-at t-bold"><?php echo $purchase->explanation ?></div>
                                        </div>
                                        <p></p>
                                    </div>
                                    <div class="row-group">
                                        <div class="row-contro">
                                            <div><?= _l('ch_staff_p') ?>:</div>
                                            <div class="ml-at t-bold"> <?php echo staff_profile_image($purchase->staff_create,
                                                        array('staff-profile-image-small mright5 img_ch'), 'small',
                                                        array(
                                                            'data-toggle' => 'tooltip',
                                                            'data-title' => get_staff_full_name($purchase->staff_create)
                                                        )) . get_staff_full_name($purchase->staff_create) ?>
                                            </div>
                                        </div>

                                        <div class="row-contro" style="color: red">
                                            <div><?= _l('Ngày cần hàng') ?>:</div>
                                            <div class="ml-at t-bold">
                                                <?= !empty($purchase->date_need) ? _dhau($purchase->date_need) : ''  ?>
                                            </div>
                                        </div>

                                        <?php
                                        $history_status = explode('|', $purchase->history_status);
                                        foreach ($history_status as $key => $value) {
                                            $data = explode(',', $value);
                                            if (is_numeric($data[0])) {
                                                if ($key == 1) {
                                                    ?>
                                                    </p>
                                                    <div class="row-contro">
                                                        <div><?= _l('ch_status_confirm') ?></div>
                                                        :
                                                        <div class="ml-at t-bold"> <?php echo staff_profile_image($data[0],
                                                                    array('staff-profile-image-small mright5 img_ch'),
                                                                    'small', array(
                                                                        'data-toggle' => 'tooltip',
                                                                        'data-title' => _l('ch_time') . ': ' . _dt($data[1])
                                                                    )) . get_staff_full_name($data[0]) ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                } elseif ($key == 2) {
                                                    ?>
                                                    </p>
                                                    <div class="row-contro">
                                                        <div> <?= _l('ch_status_import') ?></div>
                                                        :
                                                        <div class="ml-at t-bold"><?php echo staff_profile_image($data[0],
                                                                    array('staff-profile-image-small mright5 img_ch'),
                                                                    'small', array(
                                                                        'data-toggle' => 'tooltip',
                                                                        'data-title' => _l('ch_time') . ': ' . _dt($data[1])
                                                                    )) . get_staff_full_name($data[0]) ?></div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        } ?>
                                        <p></p>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab"><i
                                    class="icon-foso fal fa-info-circle"></i><?= _l('ch_information') ?></a>
                    </li>
                    <li role="presentation">
                        <a href="#tab_feedback" aria-controls="tab_feedback" role="tab" data-toggle="tab">
                            <i class="icon-foso fa fa-comments-o"></i>
                            <?= _l('FeedBack') ?><span
                                    class="badge menu-badge bg-warning"><?= !empty($feedback) ? count($feedback) : '' ?></span>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#item_activity" aria-controls="item_activity" role="tab" data-toggle="tab"><i
                                    class="icon-foso fal fa-history"></i><?= _l('activity_log_puchases') ?></a>
                    </li>
                </ul>
                <div role="tabpanel" class="tab-pane active" id="item_info">
                    <div class="table-responsive">
                        <table id="view-enquiry" class="table" style="width: 100%;">
                            <thead>
                            <tr>
                                <th class="border-top text-center" style="width: 50px;"><?= _l('#') ?></th>
                                <th class="border-top text-center" style="width: 100px;"><?= _l('ch_image') ?></th>
                                <th class="border-top text-center" style="width: 250px;">
                                    <?= _l('Mã hàng') ?></th>
                                <th class="border-top text-center" style="width: 250px;">
                                    <?= _l('ch_items_name_t') ?></th>
                                <th class="border-top text-center" style="width: 100px;"><?= _l('ch_color') ?></th>
                                <th class="border-top text-center" style="width: 100px;"><?= _l('item_unit') ?></th>
                                <th class="border-top text-center" style="width: 100px;">
                                    <?= _l('item_quantity_all') ?></th>
                                <th class="border-top text-center" style="width: 100px;">
                                    <?= _l('item_quantity_confirm') ?></th>
                                <th class="border-top text-center" style="width: 100px;">
                                    <?= _l('item_quantity_po') ?></th>
                                <th class="border-top text-center" style="width: 100px;">
                                    <?= _l('item_quantity_left') ?></th>
                                <th class="border-top text-center" style="width: 200px;"><?= _l('note') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $totalQuantity_approve = 0;
                            $totalQuantity = 0;
                            if (isset($purchase->items) && (count($purchase->items) > 0)) {

                                ?>
                                <?php foreach ($purchase->items as $key => $value) { ?>
                                    <?php
                                    $this->db->select('SUM(tblwarehouse_items.product_quantity) as product_quantity');
                                    $this->db->from('tblwarehouse_items');
                                    $this->db->where('id_items', $value['product_id']);
                                    $this->db->where('type_items', $value['type']);
                                    $this->db->group_by('id_items,type_items');
                                    $quantity_warehouse = $this->db->get()->row_array()['product_quantity'];
                                    ?>
                                    <tr>
                                        <td class="text-center border-left"><?= ($key + 1) ?></td>
                                        <td class="text-center">
                                            <div class="preview_image text-center"
                                                 style="width: 105px;margin-bottom:0;margin-top:0">
                                                <div
                                                        class="display-block contract-attachment-wrapper img-<?= $value['id'] ?>">
                                                    <div>
                                                        <a href="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : (file_exists('uploads/tools_supplies/' . $value['avatar']) ? base_url('uploads/tools_supplies/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg')) ?>"
                                                           data-lightbox="customer-profile" class="display-block mbot5">
                                                            <img class="mbot5"
                                                                 style="border-radius: 50%;width: 2em;height: 2em;"
                                                                 src="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : (file_exists('uploads/tools_supplies/' . $value['avatar']) ? base_url('uploads/tools_supplies/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg')) ?>"><br>
                                                            <?= format_item_purchases($value['type']) ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div><?= $value['code_item'] ?></div>
                                        </td>
                                        <td>
                                            <div><?= ($value['name_item']) ?><?= GetQuycach($value['product_id'],
                                                    $value['type']) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <?= format_item_color($value['product_id'], $value['type'], 1) ?></td>
                                        <td class="text-center">
                                            <div><?= ($value['unit']) ?></div>
                                        </td>
                                        <td class="text-center"><?= (formatNumber($value['quantity'])) ?>
                                        </td>
                                        <td class="text-center"><?= (formatNumber($value['quantity_net'])) ?></td>
                                        <?php $quantili_po = get_quantili_po($value['id']); ?>
                                        <td class="text-center"><?= formatNumber($quantili_po) ?></td>
                                        <td class="text-center" style="color: red;font-weight: bold;font-size: 16px">
                                            <?= ((formatNumber($value['quantity_net'] - $quantili_po)) < 0 ? 0 : (formatNumber($value['quantity_net'] - $quantili_po))) ?>
                                        </td>
                                        <td class="text-center"><?= $value['note'] ?></td>
                                    </tr>
                                    <?php
                                    $totalQuantity_approve += $value['quantity_net'];
                                    $totalQuantity += $value['quantity'];
                                }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="bottom-total" class="well well-sm" style="margin-bottom: 5px;">
                        <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                            <tbody>
                            <tr class="success">
                                <td><?= _l('item_quantity_all') ?>:<span
                                            class="pull-right"><?= $totalQuantity ?></span></td>
                                <td><?= _l('item_quantity_approve') ?>:<span
                                            class="pull-right"><?= $totalQuantity_approve ?></span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="tab_feedback">
                    <div class="col-md-12 mtop5">
                        <?php include_once(APPPATH . 'views/admin/feedback/purchases/feedback.php'); ?>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="item_activity">
                    <div class="activity-container">
                        <?php foreach ($dataLog as $key => $value) { ?>
                            <div class="feed-item">
                                <div class="activity-text">
                                    <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'),
                                        'small'); ?>
                                    <?= get_staff_full_name($value['staff_id']); ?>
                                </div>
                                <div class="activity-time">
                                    <?= time_ago($value['date']) ?> <span
                                            class="activity-module"><?= _l($value['table_obj']) ?></span>
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
        $(document).ready(function () {
            $('.tip').tooltip();
            // $('#view-enquiry').dataTable();
        });
        $(document).ready(function () {
            var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
            dtItems = $('#view-enquiry').DataTable({

                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                // scrollY: '300px',
                scrollX: true,
                // fixedColumns:   {
                //     leftColumns: 4,
                //     rightColumns: 0
                // },
                // 'searching': false,
                // 'ordering': false,
                // 'paging': false,
                // "info": false,
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                },
                "initComplete": function (settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                },
                "footerCallback": function (row, data, start, end, display) {

                }
            });
            setTimeout(function () {
                dtItems.draw('page');
            }, 150);
        });
    </script>