<div class="modal-dialog modal-lg" style="width: 60%;">
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
                            <div><?= lang('Loại định mức') ?>: </div>
                            <div class="ml-at t-bold"><div class="label" style="color: <?= $dtData['color'] ?>;border:1px solid <?= $dtData['color'] ?>"><?= ($dtData['name_type_bonus_discipline']) ?></div></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Loại đối tượng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['object_type'] == 'staff' ? 'Cá nhân' : 'Bộ phận - Phòng ban' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Đối tượng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['object_name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Phiếu đề xuất đánh giá KPI') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['code_internal'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Phiếu yêu cầu đánh giá KPI') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no_suggest_kpi'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Định mức khen thưởng-kỷ luật') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_quota_bonus_discipline'] ?></div>
                        </div>
                        <!-- <div class="row-contro">
                            <?php $dtPrecious = get_table_where('tbl_precious',['id' => $dtData['precious_id']],'','row_array') ?>
                            <div><?= lang('Quí') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtPrecious) ? $dtPrecious['name'] : '' ?></div>
                        </div> -->
                        <div class="row-contro">
                            <div><?= lang('Số Tiền') ?>: </div>
                            <div class="ml-at t-bold"><?= formatMoney($dtData['grand_total']) ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch',['id' => $dtData['branch_id']],'','row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Lý do') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
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