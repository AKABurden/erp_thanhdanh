<div class="modal-dialog modal-lg" style="width: 90%;">
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
                            <div><?= lang('Phiếu yêu cầu mua') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no_purchase_request'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Đơn hàng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no_order'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Lệnh sản xuất') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no_po'] ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch',['id' => $dtData['branch_id']],'','row_array');
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
                                            <th style="width: 100px;"><?= lang('Mã NPL') ?></th>
                                            <th style="width: 150px;"><?= lang('Tên NPL') ?></th>
                                            <th style="width: 100px;"><?= lang('Nhóm NPL') ?></th>
                                            <th style="width: 100px;"><?= lang('Tên Chủng Loại') ?></th>
                                            <th style="width: 100px;"><?= lang('Chi Tiết') ?></th>
                                            <th style="width: 100px;"><?= lang('Số Lượng Thực Mua') ?></th>
                                            <th style="width: 100px;"><?= lang('Số Lượng Nhập') ?></th>
                                            <th style="width: 100px;"><?= lang('Kích Thước NPL') ?></th>
                                            <th style="width: 100px;"><?= lang('Độ Dày NPL') ?></th>
                                            <th style="width: 100px;"><?= lang('Tổng Chiều Cao') ?></th>
                                            <th style="width: 100px;"><?= lang('Ngày Nhập') ?></th>
                                            <th style="width: 100px;"><?= lang('Thời Gian Lưu Kho') ?></th>
                                            <th style="width: 150px;"><?= lang('Tiêu Chuẩn/Quy Định') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (!empty($dtDataItems)){ ?>
                                            <?php foreach ($dtDataItems as $key => $value){ ?>
                                                <?php
                                                $item_id = $value['item_id'];
                                                $type_item = $value['type_item'];
                                                $info = null;
                                                $dtCategory = null;
                                                $dtSpecies = null;
                                                if ($type_item == "products") {
                                                    $info = $this->products_model->rowProduct($item_id);
                                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                    $dtCategory = get_table_where('tbl_category_products',['id' => $info['category_id']],'','row_array');
                                                    $dtSpecies = get_table_where('tbl_species',['id' => $info['species']],'','row_array');
                                                } elseif ($type_item == 'materials'){
                                                    $info = $this->items_model->rowMaterial($item_id);
                                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                    $dtCategory = get_table_where('tbl_category_items',['id' => $info['category_id']],'','row_array');
                                                    $dtSpecies = get_table_where('tbl_species',['id' => $info['species']],'','row_array');
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= (++$key) ?></td>
                                                    <td><div class="code_item">
                                                            <?= $info['code'] ?>
                                                        </div>
                                                    </td>
                                                    <td><div><?= $info['name'] ?></div></td>
                                                    <td><div><?= $dtCategory['name'] ?></div></td>
                                                    <td><div><?= !empty($dtSpecies['name']) ? $dtSpecies['name'] : ''  ?></div></td>
                                                    <td><div class="text-left"><?= ($value['detail'])?></div></td>
                                                    <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                                    <td class="text-center"><?= formatMoney($value['quantity_import']) ?></td>
                                                    <td class="text-left"></td>
                                                    <td class="text-left"><?= ($info['longs']) ?></td>
                                                    <td class="text-left"><?= ($value['totalheight']) ?></td>
                                                    <td class="text-left"><?= !empty($dtData['date_import']) ? _dhau($dtData['date_import']) : '' ?></td>
                                                    <td class="text-left"><?= ($info['time_stock']) ?></td>
                                                    <td class="text-left"><?= ($value['standard']) ?></td>
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
                                    $history = getActivityLogByObjId($dtData['id'], 'suggest_purchase_npl');
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
                                <div><?= lang('tnh_created_by') ?>: <?= get_staff_full_name($dtData['created_by']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($dtData['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty(get_staff_full_name($dtData['updated_by']))) : ?>
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
        <a data-tnh="modal" class="tnh-modal hide click1"
           href=" <?= base_url() ?>admin/suggest_plan_overtime/view/<?= $dtData['id'] ?>" data-toggle="modal"
           data-target="#myModal"></a>
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
            }
        });
    });
</script>