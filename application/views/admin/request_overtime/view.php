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
                            <div><?= $dtData['type_object'] == 'orders' ? 'Đơn Hàng Bán' : 'Lệnh Sản Xuất' ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['list_reference_no'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch',['id' => $dtData['branch_id']],'','row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người lập kế hoạch') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_plan']) ?></div>
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
                                            <th style="width: 100px;"><?= lang('Mã thành phẩm)') ?></th>
                                            <th style="width: 150px;"><?= lang('Tên thành phẩm)') ?></th>
                                            <th style="width: 100px;"><?= lang('Tổng SL') ?></th>
                                            <th style="width: 100px;"><?= lang('Nhóm tăng ca') ?></th>
                                            <th style="width: 100px;"><?= lang('Định mức năng suất') ?></th>
                                            <th style="width: 100px;"><?= lang('Số Lượng Người Tăng Ca') ?></th>
                                            <th style="width: 100px;"><?= lang('Hệ Số Lương Ca') ?></th>
                                            <th style="width: 100px;"><?= lang('Tổng Số Lương Tăng Ca') ?></th>
                                            <th style="width: 100px;"><?= lang('Nhân Viên Điều Độ Tăng Ca') ?></th>
                                            <th style="width: 100px;"><?= lang('Kết quả') ?></th>
                                            <th style="width: 100px;"><?= lang('Hành chính nhân sự duyệt') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (!empty($dtDataItems)){ ?>
                                            <?php foreach ($dtDataItems as $key => $value){ ?>
                                                <?php
                                                $item_id = $value['item_id'];
                                                $type_item = $value['type_item'];
                                                $info = null;
                                                if ($type_item == "products") {
                                                    $info = $this->products_model->rowProduct($item_id);
                                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                }
                                                if ($value['status'] == 0) {
                                                    $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$value['id'].', 1)\' id=\'agree\' suggest_id=\''.$value['id'].'\' value=\'1\' class=\'btn btn-success\'>'.lang('tnh_agree').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-danger po">'.lang('Chưa duyệt').'</span></div>';
                                                } else if ($value['status'] == 1) {
                                                    $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$value['id'].', 0)\' id=\'agree\' suggest_id=\''.$value['id'].'\' value=\'0\' class=\'btn btn-danger\'>'.lang('Hủy duyệt').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-success po">'.lang('Đã duyệt').'</span></div>';
                                                    $_data.= '<div style="margin-top: 5px"> Người duyệt: '.get_staff_full_name($value['staff_status']).'</div>';
                                                } else {
                                                    $_data = '';
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= (++$key) ?></td>
                                                    <td><div class="code_item">
                                                            <?= $info['code'] ?>
                                                        </div>
                                                        <div style="color: green"><?=!empty($value['code_object_type']) ? $value['code_object_type'] : ''?></div>
                                                    </td>
                                                    <td><div><?= $info['name'] ?></div></td>
                                                    <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                                    <td class="text-left"><?= ($value['category_overtime']) ?></td>
                                                    <td class="text-center"><?= ($value['quota_productivity']) ?></td>
                                                    <td class="text-center"><?= ($value['quantity_overtime']) ?></td>
                                                    <td class="text-center"><?= ($value['coefficient']) ?></td>
                                                    <td class="text-right"><?= formatMoney($value['salary']) ?></td>
                                                    <td><div class="text-left"><?= get_staff_full_name($value['staff_id']) ?></div></td>
                                                    <td><div class="text-left"><?= ($value['name_result']) ?></div></td>
                                                    <td><div class="text-left"><?= $_data ?></td>
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
                                    $history = getActivityLogByObjId($dtData['id'], 'request_overtime');
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
           href=" <?= base_url() ?>admin/request_overtime/view/<?= $dtData['id'] ?>" data-toggle="modal"
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
            }
        });
    });
</script>