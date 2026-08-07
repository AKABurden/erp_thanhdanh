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
                <div class="col-md-4">
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
                            <div><?= lang('Mã thiết bị') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['code_machines'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Tên thiết bị') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_machines'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Trạng thái') ?>: </div>
                            <?php
                            if ($dtData['status_machines'] == "producing") {
                                $htmlStatus = '<span class="label label-primary">'.status_machine_new()[$dtData['status_machines']].'</span>';
                            } else if ($dtData['status_machines'] == "maintenance") {
                                $htmlStatus = '<span class="label label-warning">'.status_machine_new()[$dtData['status_machines']].'</span>';
                            } else if ($dtData['status_machines'] == "damaged") {
                                $htmlStatus =  '<span class="label label-danger">'.status_machine_new()[$dtData['status_machines']].'</span>';
                            } else {
                                $htmlStatus = '';
                            }
                            ?>
                            <div class="ml-at t-bold"><?= $htmlStatus ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Định Mức Năng Suất/Tháng') ?>: </div>
                            <div class="ml-at t-bold"><?= formatNumber($dtData['product_in_month']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Tiêu Chuẩn') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_standard'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Phương Pháp Kiểm') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['pp_measure'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Định Mức Năng Suất/h') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['quota_productivity'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Ngày Bắt Đầu Bảo Trì') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtData['day_operation']) ? _dhau($dtData['day_operation']) : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Khổ Vận Hành') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['operating_gauge'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời Gian Chuẩn Bị (Giờ))') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['preparation_time'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thông số kỹ thuật') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['specifications'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Định Mức Thời Gian Duyệt Màu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['product_color'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('NPL canh bài') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['soup_ingredients'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Nhóm Công Đoạn') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_stage'] ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch',['id' => $dtData['branch_id']],'','row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Tiêu chuẩn/quy định') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['standard'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Kết quả') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['name_result'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Nội dung đánh giá') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['content'] ?></div>
                        </div>

                    </div>
                </div>

                <div class="wap-content mtop20">
                    <table class="table tb-view dataTable">
                        <thead>
                        <tr style="background: #cedae6;">
                            <td style="width: 20%;" class="text-center">Bộ phận máy móc</td>
                            <td style="width: 20%;" class="text-center">Số ngày bảo trì</td>
                            <td style="width: 40%;" class="text-center">Ghi chú cách thức bảo trì</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($dtMachinesMain)){ ?>
                            <?php foreach ($dtMachinesMain as $key => $value){ ?>
                                <tr>
                                    <td><?= $value['name'] ?></td>
                                    <td><?= $value['month'] ?></td>
                                    <td><?= $value['note_main'] ?></td>
                                </tr>
                        <?php }?>
                        <?php }?>
                        </tbody>
                    </table>
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