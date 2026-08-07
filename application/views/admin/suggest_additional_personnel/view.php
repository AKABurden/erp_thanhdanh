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
                            <div><?= lang('Người yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_suggest']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người tiếp nhận') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_reciever']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người duyệt') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_agree']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Vị trí cần bổ sung') ?>: </div>
                            <?php
                            $roles = get_table_where('tblroles',['roleid'=>$dtData['position_recruitment']],'','row_array');
                            ?>
                            <div class="ml-at t-bold"><?= (!empty($roles) ? $roles['name'] : '' ) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Lý do yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('KPIS') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['kpis'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Số lượng người bổ sung') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['quantity'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời gian bổ sung') ?>: </div>
                            <div class="ml-at t-bold"><?= @diffDate($dtData['date_start'],$dtData['date_end']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người quản lý tạm thời') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_admin']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời gian bắt đầu') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtData['date_start']) ? _dhau($dtData['date_start']) : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời gian kết thúc') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtData['date_end']) ? _dhau($dtData['date_end']) : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Đánh giá') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['evaluate'] ?></div>
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
    </div>
</div>
<script type="text/javascript">
</script>