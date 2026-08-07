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
                            <div><?= lang('Người lập kế thời') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_id']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Nhóm kế hoạch') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_category_plan'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Vị trí tuyển dụng') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['position_recruitment']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Mô tả công việc') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['content_work'] ?></div>
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
                            <div><?= lang('Số lượng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['quantity'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời gian làm việc') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['time_work']) ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                            if ($dtData['gender'] == "male" ){
                                $htmlGender = 'Nam';
                            } elseif ($dtData['gender'] == "female" ){
                                $htmlGender = 'Nữ';
                            } elseif ($dtData['gender'] == "other" ){
                                $htmlGender = 'Khác';
                            }
                            ?>
                            <div><?= lang('Giới tính') ?>: </div>
                            <div class="ml-at t-bold"><?= $htmlGender ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Định mức thời gian hoàn thành') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['completion_time_limit'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Tiêu chuẩn/ quy định') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['standard'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời gian hoàn thành') ?>:</div>
                            <div class="ml-at t-bold"><?= _dt($dtData['time_finish']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Lý do') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
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