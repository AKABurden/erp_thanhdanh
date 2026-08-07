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
                            <div class="ml-at t-bold"><?= _dt($dtData['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Mã vị trí') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_role'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Mức độ ưu tiên') ?>: </div>
                            <div class="ml-at t-bold"><span style="color:<?= task_priority_color($dtData['priority']) ?>;" class="inline-block"><?= task_priority($dtData['priority']) ?></span></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Trạng thái') ?>: </div>
                            <?php $status = get_task_status_by_id($dtData['status']) ?>
                            <div class="ml-at t-bold"><span class="inline-block label" style="color:<?= $status['color'] ?>;border:1px solid <?= $status['color'] ?>">
                                    <?= $status['name'] ?>
                                </span>
                            </div>
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
                            <div><?= lang('Người giao việc') ?>: </div>
                            <div class="ml-at t-bold"><?= format_members_by_ids_and_names($dtData['staff_id'], $dtData['full_name']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người được phân công') ?>: </div>
                            <div class="ml-at t-bold"><?= format_members_by_ids_and_names($dtStaff['staff_task'], $dtStaff['staff_name_task']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Kết quả') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_result'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Chi tiết công việc') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['detail_task'] ?></div>
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