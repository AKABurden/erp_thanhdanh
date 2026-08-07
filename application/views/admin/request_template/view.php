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
                            $dtOrder = get_table_where('tbl_quotes', ['id' => $dtData['id_quotes']], '', 'row_array');
                            ?>
                            <div><?= lang('Báo giá') ?>: </div>
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
                            <?php
                            $dtClients = get_table_where('tblclients', ['userid' => $dtData['client_id']], '', 'row_array');
                            ?>
                            <div><?= lang('Khách hàng') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtClients['company']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Chạy mẫu lại') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['is_rerun_sample'] == 1 ? 'Có' : '' ?></div>
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
                                                <th><?= lang('Tên Nhóm SP') ?></th>
                                                <th><?= lang('Tên Chủng Loại') ?></th>
                                                <th><?= lang('ĐV Tính SP') ?></th>
                                                <th><?= lang('Height') ?></th>
                                                <th><?= lang('Width') ?></th>
                                                <th><?= lang('ĐV Đo SP') ?></th>
                                                <th><?= lang('Mã Thành Phẩm') ?></th>
                                                <th><?= lang('Tên Thành Phẩm') ?></th>
                                                <th><?= lang('Brand') ?></th>
                                                <th><?= lang('Tiêu Chuẩn Đóng Gói') ?></th>
                                                <th><?= lang('Số Lượng Tồn Cho Phép') ?></th>
                                                <th><?= lang('Thời Gian Tồn Kho') ?></th>
                                                <th><?= lang('Định Mức Thời Gian') ?></th>
                                                <th><?= lang('Hình Ảnh SP') ?></th>
                                                <th><?= lang('Ngày Chạy Mẫu') ?></th>
                                                <th><?= lang('Ngày Hoàn Thành Mẫu') ?></th>
                                                <th><?= lang('Ngày Gửi Mẫu') ?></th>
                                                <th><?= lang('Ngày Duyệt Mẫu') ?></th>
                                                <th><?= lang('Chạy Hàng Lấy Mẫu') ?></th>
                                                <th><?= lang('Ngày Hoàn Thành Mẫu SX') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($dtDataItems)) { ?>
                                                <?php foreach ($dtDataItems as $key => $value) { ?>
                                                    <?php
                                                    $item_id = $value['items_id'];
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
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?= (++$key) ?></td>
                                                        <td class="text-left">
                                                            <div class="td_mode"><?= $info['category_name'] ?></div>
                                                        </td>
                                                        <td class="text-left">
                                                            <div class="td_unit"><?= $info['species_name'] ?></div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="td_unit"><?= $unit['unit'] ?></div>
                                                        </td>
                                                        <td class="text-center"><?= ($info['height']) ?></td>
                                                        <td class="text-center"><?= ($info['wide']) ?></td>
                                                        <td class="text-left"><?= ($info['unit_measure']) ?></td>
                                                        <td>
                                                            <div class="code_item">
                                                                <?= $info['code'] ?>
                                                            </div>
                                                        </td>
                                                        <td class="text-left">
                                                            <div><?= $info['name'] ?></div>
                                                        </td>
                                                        <td class="text-center"><?= ($info['brand_name']) ?></td>
                                                        <td class="text-center"><?= ($info['packing']) ?></td>
                                                        <td class="text-center"><?= formatNumber($info['quantity_max']) ?></td>
                                                        <td class="text-center"><?= formatNumber($info['time_inventory']) ?></td>
                                                        <td class="text-center"><?= formatNumber($info['quota_time_change_one']) ?></td>
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
                                                        <td class="text-center"><?= !empty($value['date_run_sample']) ? _d($value['date_run_sample']) : '' ?></td>
                                                        <td class="text-center"><?= !empty($value['date_finished']) ? _d($value['date_finished']) : '' ?></td>
                                                        <td class="text-center"><?= !empty($value['date_request_sample']) ? _d($value['date_request_sample']) : '' ?></td>
                                                        <td class="text-center"><?= !empty($value['date_approved_sample']) ? _d($value['date_approved_sample']) : '' ?></td>
                                                        <td class="text-center"><?= !empty($value['date_runs_sample']) ? _d($value['date_runs_sample']) : '' ?></td>
                                                        <td class="text-center"><?= !empty($value['date_finished_manufactures']) ? _d($value['date_finished_manufactures']) : '' ?></td>
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
            "lengthMenu": dataTableLengthMenu(),
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