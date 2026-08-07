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
                            <div class="ml-at t-bold"><?= _d($dtData['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Nhóm Hiệu Chuẩn') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['category_calibration']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Bộ Phận Hiệu Chuẩn') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['bp_calibration'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Chi Tiết Hiệu Chuẩn') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['detail_calibration'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số lượng') ?>: </div>
                            <div class="ml-at t-bold"><?= formatNumber($dtData['quantity']) ?></div>
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
                            <div><?= lang('Nhóm kế hoach') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['category_plan'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Mã thiết bị') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['code_machines'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Tên thiết bị') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_machines'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
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
    </div>
</div>
<script type="text/javascript">
</script>