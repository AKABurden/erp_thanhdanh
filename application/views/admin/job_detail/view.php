<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab_info">Thông tin chung</a></li>
                        <li><a data-toggle="tab" href="#tab_info_other1">Trách nhiệm</a></li>
                        <li><a data-toggle="tab" href="#tab_info_other2">Phạm vi quyền hạn</a></li>
                        <li><a data-toggle="tab" href="#tab_info_other3">Yêu cầu công việc</a></li>
                        <li><a data-toggle="tab" href="#tab_info_other4">Tiêu chuẩn năng lực</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab_info" class="tab-pane fade in active">
                            <div class="col-md-6">
                                <div class="lead-view" id="leadViewWrapper">
                                    <div class="row-contro">
                                        <div><?= lang('tnh_date_creted') ?>: </div>
                                        <div class="ml-at t-bold"><?= _dt($dtData['date']) ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('Mã mô tả công việc') ?>: </div>
                                        <div class="ml-at t-bold"><?= $dtData['code'] ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('Mã vị trí') ?>: </div>
                                        <div class="ml-at t-bold"><?= $dtData['code_role'] ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('Tiêu đề công việc') ?>: </div>
                                        <div class="ml-at t-bold"><?= $dtData['title'] ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('Version') ?>: </div>
                                        <div class="ml-at t-bold"><?= $dtData['version'] ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="lead-view" id="leadViewWrapper">
                                    <div class="row-contro">
                                        <div><?= lang('Ngày ban hành') ?>: </div>
                                        <div class="ml-at t-bold"><?= !empty($dtData['date_issue']) ? _dhau($dtData['date_issue']) : '' ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('Thời gian hết hạn') ?>: </div>
                                        <div class="ml-at t-bold"><?= $dtData['month_review'] ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('Ngày ban hành mới nhất') ?>: </div>
                                        <div class="ml-at t-bold"><?= !empty($dtData['last_review_date']) ? _dhau($dtData['last_review_date']) : '' ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('Ngày hết hạn') ?>: </div>
                                        <div class="ml-at t-bold"><?= !empty($dtData['date_end']) ? _dhau($dtData['date_end']) : '' ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('Đường dẫn tài liệu') ?>: </div>
                                        <div class="ml-at t-bold"><?= $dtData['link_jd_doc'] ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('Mục tiêu') ?>: </div>
                                        <div class="ml-at t-bold"><?= $dtData['goal'] ?></div>
                                    </div>
                                    <div class="row-contro">
                                        <div><?= lang('Ghi chú') ?>: </div>
                                        <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div id="tab_info_other1" class="tab-pane fade in">
                            <table id="tb-other1"
                                   class="dt-tnh table tnh-table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;"><?= lang('STT') ?></th>
                                    <th><?= lang('Tiêu chí') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $stt = 0; if (!empty($dtDataChild)) : ?>
                                    <?php foreach ($dtDataChild as $key => $value) : ?>
                                        <?php if ($value['type'] == 1) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other1 text-center"><?= ++$stt ?></div>
                                                </td>
                                                <td>
                                                    <?= $value['name'] ?>
                                                </td>
                                            </tr>
                                    <?php endif ?>
                                    <?php endforeach ?>
                                <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="tab_info_other2" class="tab-pane fade in">
                            <table id="tb-other1"
                                   class="dt-tnh table tnh-table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;"><?= lang('STT') ?></th>
                                    <th><?= lang('Tiêu chí') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $stt = 0; if (!empty($dtDataChild)) : ?>
                                    <?php foreach ($dtDataChild as $key => $value) : ?>
                                        <?php if ($value['type'] == 2) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other1 text-center"><?= ++$stt ?></div>
                                                </td>
                                                <td>
                                                    <?= $value['name'] ?>
                                                </td>
                                            </tr>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="tab_info_other3" class="tab-pane fade in">
                            <table id="tb-other1"
                                   class="dt-tnh table tnh-table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;"><?= lang('STT') ?></th>
                                    <th><?= lang('Tiêu chí') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $stt = 0; if (!empty($dtDataChild)) : ?>
                                    <?php foreach ($dtDataChild as $key => $value) : ?>
                                        <?php if ($value['type'] == 3) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other1 text-center"><?= ++$stt ?></div>
                                                </td>
                                                <td>
                                                    <?= $value['name'] ?>
                                                </td>
                                            </tr>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="tab_info_other4" class="tab-pane fade in">
                            <table id="tb-other1"
                                   class="dt-tnh table tnh-table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;"><?= lang('STT') ?></th>
                                    <th><?= lang('Tiêu chí') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $stt = 0; if (!empty($dtDataChild)) : ?>
                                    <?php foreach ($dtDataChild as $key => $value) : ?>
                                        <?php if ($value['type'] == 4) : ?>
                                            <tr>
                                                <td>
                                                    <div class="stt-other1 text-center"><?= ++$stt ?></div>
                                                </td>
                                                <td>
                                                    <?= $value['name'] ?>
                                                </td>
                                            </tr>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                <?php endif ?>
                                </tbody>
                            </table>
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