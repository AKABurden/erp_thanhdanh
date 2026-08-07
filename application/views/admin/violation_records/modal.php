<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="modal_violation_records" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="min-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="title">
                        <?=(!empty($title) ? $title : '' )?>
                    </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div>Ngày: </div>
                                    <div class="ml-at t-bold"><?=_dt($violation_records->date)?></div>
                                </div>
                                <div class="row-contro">
                                    <div>Mã biên bản vi phạm: </div>
                                    <div class="ml-at t-bold"><?=($violation_records->code)?></div>
                                </div>
                                <div class="row-contro">
                                    <div>Nhân viên: </div>
                                    <div class="ml-at t-bold"><?= $violation_records->staff_id ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_cal_kpi') ?>: </div>
                                    <div class="ml-at t-bold"><?= !empty($violation_records->cal_kpi) ? lang('yes') : lang('no') ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_kpi_criteria') ?>: </div>
                                    <div class="ml-at t-bold">
                                        <?php
                                            if (!empty($violation_records->kpi_criteria)) {
                                                $dtKpiCriteria = $this->kpi_model->getKpiCriteriaById($violation_records->kpi_criteria);
                                                echo $dtKpiCriteria['criteria'];
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div>Danh mục vi phạm: </div>
                                    <div class="ml-at t-bold"><?=$violation_records->name_list_protocol?> (<?=$violation_records->code_list_protocol?>)</div>
                                </div>
                                <div class="row-contro">
                                    <div>Loại phiếu liên quan: </div>
                                    <div class="ml-at t-bold"><?=_l('c_object_' . $violation_records->object_type)?></div>
                                </div>
                                <div class="row-contro">
                                    <div>Phiếu chứng từ: </div>
                                    <div class="ml-at t-bold"><?=$violation_records->object_id?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row-contro">
                                <div>Ghi chú: </div>
                                <div class="ml-at t-bold"><?=$violation_records->note?></div>
                            </div>
                            <div class="row-contro">
                                <div>Trưởng phòng duyệt: </div>
                                <div class="ml-at t-bold">
                                    <?php if($violation_records->status == 0) {?>
                                        <span class="label label-warning">Chưa duyệt</span>
                                    <?php } else if($violation_records->status == 1) {?>
                                        <?=$violation_records->user_status?> <span class="label label-success">Đã duyệt</span>
                                    <?php } else if($violation_records->status == 2) {?>
                                        <?=$violation_records->user_status?> <span class="label label-danger">Đã Hủy</span> <?=!empty($violation_records->person_status) ? ('<br/>Lý do hủy: ' . $violation_records->person_status) : ''?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row-contro">
                                <div>Nhân viên xác nhận: </div>
                                <div class="ml-at t-bold">
                                    <?php if($violation_records->status_staff == 0) {?>
                                        <span class="label label-warning">Chưa duyệt</span>
                                    <?php } else if($violation_records->status_staff == 1) {?>
                                        <?=$violation_records->user_status_staff?> <span class="label label-success">Đã duyệt</span>
                                    <?php }?>
                            </div>
                        </div>
                    </div>
                        <div class="clearfix"></div>
                        <hr/>
                        <div class="clearfix"></div>
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab_info_violation">Nội dung vi phạm</a></li>
                            <li><a data-toggle="tab" href="#tab_feedback"><i class="icon-foso fa fa-comments-o"></i> FeedBack <span class="badge menu-badge bg-warning"><?= !empty($feedback) ? count($feedback) : '' ?></span></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="tab_info_violation" class="tab-pane fade in active">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">NỘI DUNG VI PHẠM</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-md-12">
                                            <?=$violation_records->data_content?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="tab_feedback" class="tab-pane fade">
                                <div class="col-md-12 mtop5">
                                    <?php include_once(APPPATH . 'views/admin/feedback/violation_records/feedback.php'); ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#modal_violation_records').modal('show');
</script>
