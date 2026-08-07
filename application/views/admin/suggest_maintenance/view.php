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
                            <div><?= lang('Loại bảo dưỡng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_type_maintenance'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Nhóm bảo dưỡng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_category_maintenance'] ?></div>
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
                            <div><?= lang('Khu vực bảo dưỡng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_department'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Tên thiết bị') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['name_machines']) ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch',['id' => $dtData['branch_id']],'','row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Chi tiết bảo dưỡng') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['detail'] ?></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="wap-content mtop20">
                        <table class="table dataTable">
                            <thead>
                            <tr>
                                <th style="width: 10%;" class="text-center">STT</th>
                                <th style="width: 30%;" class="text-center">Bộ phận thiết bị</th>
                                <th style="width: 15%;" class="text-center">Kết quả</th>
                                <th style="width: 25%;" class="text-center">Tiêu chuẩn/ quy định</th>
                                <th style="width: 20%;" class="text-center">Báo cáo sự cố</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($dtItems)){ ?>
                                <?php foreach ($dtItems as $key => $value){ ?>
                                    <tr>
                                        <td class="text-center"><?= (++$key) ?></td>
                                        <td><?= $value['name_machines_maintenance'] ?></td>
                                        <td><?= $value['name_result'] ?></td>
                                        <td><?= $value['standard'] ?></td>
                                        <td>
                                            <div class="text-center">
                                                <a target="_blank" href="<?= base_url('admin/production_report/detail?object_id=' . $value['id'] . '&object_type=suggest_maintenance') ?>" class="btn btn-info">Tạo phiếu báo cáo</a></div>
                                            <div style="margin-top: 5px">

                                            </div>
                                        </td>
                                    </tr>
                                <?php }?>
                            <?php }?>
                            </tbody>
                        </table>
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