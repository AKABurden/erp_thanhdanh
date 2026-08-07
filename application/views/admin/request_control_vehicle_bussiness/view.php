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
                            <div><?= lang('Ngày hiệu lực') ?>: </div>
                            <div class="ml-at t-bold"><?= _d($dtData['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Lý do điều xe') ?>: </div>
                            <div class="ml-at t-bold"><?= _l('object_type_' . $dtData['object_type']) ?></div>
                        </div>
						<?php if($dtData['object_type'] == 'delivery') {?>
                            <div class="row-contro">
                                <div><?= lang('Khách hàng') ?>: </div>
                                <div class="ml-at t-bold"><?=!empty($dtData['company']) ? $dtData['company'] : '-'?></div>
                            </div>
						<?php } ?>
                        
                        <?php if($dtData['object_type'] == 'purchase_order') {?>
                            <div class="row-contro">
                                <div><?= lang('Nhà cung cấp') ?>: </div>
                                <div class="ml-at t-bold"><?=!empty($dtData['company']) ? $dtData['company'] : '-'?></div>
                            </div>
						<?php } ?>
                        <div class="row-contro">
                            <div><?= _l('object_type_' . $dtData['object_type']) ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['reference_no_bussiness']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người phụ trách') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['fullname']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Địa chỉ đến') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['address']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số điện thoại liên hệ') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['phone']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Tên phương tiện') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['vehicle_name']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Loại phương tiện') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['type_vehicle']) ?></div>
                        </div>
                        <div class="row-contro">
                            <?php
                                $dtBranch = get_table_where('tblbranch', ['id' => $dtData['branch_id']], '', 'row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Ghi chú') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['note']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Nhân viên') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['employees'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người khác ngoài nhân viên') ?>: </div>
                            <div class="ml-at t-bold"><?= ($dtData['employees_other']) ?></div>
                        </div>
                        
                        <div class="row-contro">
                            <div><?= lang('Số km đi') ?>: </div>
                            <div class="ml-at t-bold"><?= formatNumber($dtData['number_km']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Định mức xăng dầu') ?>: </div>
                            <div class="ml-at t-bold"><?= formatNumber($dtData['quota_gasoline']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Phí cầu đường/phá') ?>: </div>
                            <div class="ml-at t-bold"><?= formatMoney($dtData['cost_tolls']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Đơn giá') ?>: </div>
                            <div class="ml-at t-bold"><?= formatMoney($dtData['price']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thành tiền') ?>: </div>
                            <div class="ml-at t-bold"><?= formatMoney($dtData['amount']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời gian bắt đầu') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtData['time_start']) ? _dt($dtData['time_start']) : '-' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Thời gian kết thúc') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtData['time_end']) ? _dt($dtData['time_end']) : '-' ?></div>
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