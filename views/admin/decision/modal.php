<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="modal_decision" class="modal fade" role="dialog">
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
                                    <div class="ml-at t-bold"><?=_dt($decision->date)?></div>
                                </div>
                                <div class="row-contro">
                                    <div>Mã quyết định: </div>
                                    <div class="ml-at t-bold"><?=($decision->code)?></div>
                                </div>
                                <div class="row-contro">
                                    <div>Nhân viên: </div>
                                    <div class="ml-at t-bold"><?= $decision->staff_id ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div>Loại quyết định: </div>
                                    <div class="ml-at t-bold"><?=$decision->name_category?> (<?=$decision->code_category?>)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row-contro">
                                <div>Ghi chú: </div>
                                <div class="ml-at t-bold"><?=$decision->note?></div>
                            </div>
                            <div class="row-contro">
                                <div>Trưởng phòng duyệt: </div>
                                <div class="ml-at t-bold">
                                    <?php if($decision->status == 0) {?>
                                        <span class="label label-warning">Chưa duyệt</span>
                                    <?php } else if($decision->status == 1) {?>
                                        <?=$decision->user_status?> <span class="label label-success">Đã duyệt</span>
                                    <?php } else if($decision->status == 2) {?>
                                        <?=$decision->user_status?> <span class="label label-danger">Đã Hủy</span> <?=!empty($decision->person_status) ? ('<br/>Lý do hủy: ' . $decision->person_status) : ''?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr/>
                        <div class="clearfix"></div>
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab_info_violation">Nội dung quyết định</a></li>
<!--                            <li><a data-toggle="tab" href="#tab_feedback"><i class="icon-foso fa fa-comments-o"></i> FeedBack <span class="badge menu-badge bg-warning">--><?//= !empty($feedback) ? count($feedback) : '' ?><!--</span></a></li>-->
                        </ul>
                        <div class="tab-content">
                            <div id="tab_info_violation" class="tab-pane fade in active">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">NỘI DUNG QUYẾT ĐỊNH</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-md-12">
                                            <?=$decision->data_content?>
                                        </div>
                                    </div>
                                </div>
                            </div>
<!--                            <div id="tab_feedback" class="tab-pane fade">-->
<!--                                <div class="col-md-12 mtop5">-->
<!--                                    --><?php //include_once(APPPATH . 'views/admin/feedback/decision/feedback.php'); ?>
<!--                                </div>-->
<!--                                <div class="clearfix"></div>-->
<!--                            </div>-->
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
    $('#modal_decision').modal('show');
</script>
