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
                            <div><?= lang('Đơn vị sửa chữa') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['unit_repair'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Nhóm bảo dưỡng') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['category_maintenance']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Bộ Phận bảo dưỡng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['bp_maintenance'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Chi Tiết bảo dưỡng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['detail_maintenance'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số lượng') ?>: </div>
                            <div class="ml-at t-bold"><?= formatNumber($dtData['quantity']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Đơn Giá') ?>: </div>
                            <div class="ml-at t-bold"><?= formatNumber($dtData['price']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thành Tiền') ?>: </div>
                            <div class="ml-at t-bold"><?= formatNumber($dtData['amount']) ?></div>
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
                            <div><?= lang('Nhóm công đoạn') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_stage'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Mã thiết bị') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['code_machines'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Tên thiết bị') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_machines'].'('.$dtData['name_cost'].')' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Biên Bản Nghiệm Thu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['test_records'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Đánh Giá Đơn Vị Sửa Chữa') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['evaluate'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Tiêu Chuẩn/ Quy Định') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['standard'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_suppliers') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['company_supp'] ?></div>
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
    </div>
</div>
<script type="text/javascript">
</script>