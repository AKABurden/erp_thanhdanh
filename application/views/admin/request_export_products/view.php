<div class="modal-dialog modal-lg" style="width: 85%;">
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
                            <div class="ml-at t-bold"><?= _dt($dtData['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                            $dtOrder = get_table_where('tbl_orders', ['id' => $dtData['order_id']], '', 'row_array');
                            ?>
                            <div><?= lang('Đơn hàng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtOrder['reference_no'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch', ['id' => $dtData['branch_id']], '', 'row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>: </div>
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
                        <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?></label>


                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-items" class="table dt-tnh table-hover table-condensed table-cs-border">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                                                <th style="width: 100px;"><?= lang('Tên Brand') ?></th>
                                                <th style="width: 100px;"><?= lang('Mã TP') ?></th>
                                                <th style="width: 150px;"><?= lang('Tên TP') ?></th>
                                                <th style="width: 100px;"><?= lang('Tên nhóm SP') ?></th>
                                                <th style="width: 50px;"><?= lang('Tên Mã Chủng Loại SP') ?></th>
                                                <th style="width: 100px;"><?= lang('Tổng Số Lượng SX') ?></th>
                                                <th style="width: 150px;"><?= lang('Số Lượng Tồn Kho ') ?></th>
                                                <th style="width: 100px;"><?= lang('Số Lượng Cần SX') ?></th>
                                                <th style="width: 100px;"><?= lang('Số Lượng Tồn Cho Phép') ?></th>
                                                <th style="width: 100px;"><?= lang('Số Lượng Cần Mua') ?></th>
                                                <th style="width: 100px;"><?= lang('Height TP') ?></th>
                                                <th style="width: 100px;"><?= lang('Width TP') ?></th>
                                                <th style="width: 100px;"><?= lang('Số Con') ?></th>
                                                <th style="width: 100px;"><?= lang('Số Kiện') ?></th>
                                                <th style="width: 100px;"><?= lang('Số Kg') ?></th>
                                                <th style="width: 100px;"><?= lang('Tổng Số Kiện') ?></th>
                                                <th style="width: 100px;"><?= lang('Định Mức Thời Gian') ?></th>
                                                <th style="width: 100px;"><?= lang('Thời Gian Quy Định') ?></th>
                                                <th style="width: 100px;"><?= lang('Hình Ảnh SP') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($dtDataItems)) { ?>
                                                <?php foreach ($dtDataItems as $key => $value) { ?>
                                                    <?php
                                                    $item_id = $value['item_id'];
                                                    $type_item = $value['type_item'];
                                                    $info = null;
                                                    $images = base_url('assets/images/tnh/no_image.png');
                                                    if ($type_item == "products") {
                                                        $info = $this->products_model->rowProductALL($item_id);
                                                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                        if (!empty($info['images'])) {
                                                            $images = base_url('uploads/products/' . $info['images']);
                                                        }
                                                    }
                                                    $warehouses = '
                                                    (Select
                                                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                                                    FROM tblwarehouse_items
                                                    WHERE tblwarehouse_items.id_items = ' . $item_id . '
                                                        AND tblwarehouse_items.type_items = "product" 
                                                        AND tblwarehouse_items.product_quantity > 0
                                                        AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
                                                    ';
                                                    $productquantity = $this->db->query($warehouses)->row_array();
                                                    if (!empty($productquantity)) {
                                                        $product_quantity = $productquantity['product_quantity'];
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?= (++$key) ?></td>
                                                        <td>
                                                            <div class="brand_name">
                                                                <?= $info['brand_name'] ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="code_item">
                                                                <?= $info['code'] ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div><?= $info['name'] ?></div>
                                                        </td>
                                                        <td class="text-left">
                                                            <div class="td_mode"><?= $info['category_name'] ?></div>
                                                        </td>
                                                        <td class="text-left">
                                                            <div class="td_unit"><?= $info['species_name'] ?></div>
                                                        </td>
                                                        <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                                        <td class="text-center"><?= formatNumber($product_quantity) ?></td>
                                                        <td class="text-center"><?= formatNumber($value['quabtity_manufactures']) ?></td>
                                                        <td class="text-center"><?= formatNumber($value['quabtity_allow']) ?></td>
                                                        <td class="text-center"><?= formatNumber($value['quabtity_purchase']) ?></td>
                                                        <td class="text-center"><?= ($info['height']) ?></td>
                                                        <td class="text-center"><?= ($info['wide']) ?></td>
                                                        <td class="text-center"><?= formatNumber($value['totalcon']) ?></td>
                                                        <td class="text-center"><?= formatNumber($value['totalkien']) ?></td>
                                                        <td class="text-center"><?= formatNumber($value['totalkg']) ?></td>
                                                        <td class="text-center"><?= formatNumber($value['totalkg']) ?></td>
                                                        <td class="text-center"><?= formatNumber($value['totalallkien']) ?></td>
                                                        <td class="text-center"><?= formatNumber($value['timeregulations']) ?></td>
                                                        <td>
                                                            <div class="td-image">
                                                                <div class="preview_image" style="width: auto;">
                                                                    <div class="display-block contract-attachment-wrapper img">
                                                                        <div style="width:45px; margin: auto;"><a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                                <div class=""><img src="<?= $images ?>" style="border-radius: 50%"></div>
                                                                            </a></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                    $history = getActivityLogByObjId($dtData['id'], 'purchase_request_material');
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
                                <div><?= lang('tnh_created_by') ?>: <?= get_staff_full_name($dtData['staff_create']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($dtData['date_create']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty(($dtData['updated_by']))) : ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= get_staff_full_name($dtData['updated_by']) ?></div>
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
        <a data-tnh="modal" class="tnh-modal hide click1" href=" <?= base_url() ?>admin/purchase_request_material/view/<?= $dtData['id'] ?>" data-toggle="modal" data-target="#myModal"></a>
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
            "footerCallback": function(row, data, start, end, display) {}
        });
    });
</script>