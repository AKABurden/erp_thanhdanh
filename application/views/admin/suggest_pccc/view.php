<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content" id="view_suggest_pccc">
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
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <?php $dtBranch = get_table_where('tblbranch',['id' => $dtData['branch_id']],'','row_array'); ?>
                            <div><?= lang('Chi nhánh') ?>: </div>
                            <div class="ml-at t-bold"><?= !empty($dtBranch['name']) ?  $dtBranch['name'] : '-'?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Ghi chú') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="wap-content mtop20">
                        <table class="table dataTable">
                            <thead>
                            <tr>
                                <th style="" class="text-center">STT</th>
                                <th style="" class="text-center">Danh mục kiểm tra</th>
                                <th style="" class="text-center">Quy định PCCC</th>
                                <th style="" class="text-center">Hình Ảnh</th>
                                <th style="" class="text-center">Người kiểm tra</th>
                                <th style="" class="text-center">Kết quả</th>
                                <th style="" class="text-center">Đánh giá</th>
                                <th style="" class="text-center">Quản lý khu vực/thiết bị</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($dtItems)){ ?>
                                <?php foreach($dtItems as $iItem => $vItem) {?>
									<?php $rowID = $vItem['item_type'] . '_' . $vItem['item_id'];?>
                                    <tr class="bg-danger <?=$rowID?>">
                                        <td class="text-center event-plus show" data-class="rowID_<?=$rowID?>"><i class="fa fa-minus-square-o" aria-hidden="true"></i></td>
                                        <td colspan="8">
                                            <b><?=$vItem['item_type'] == 'machines' ? 'Thiết Bị' : 'Máy Móc'?>:</b>  <?=$vItem['name']?>
                                        </td>
                                    </tr>
                                    <?php foreach ($vItem['detail'] as $key => $value){ ?>
                                        <tr class="rowID_<?=$rowID?>">
                                            <td class="text-center"><?= (++$key) ?></td>
                                            <td><?= $value['name_machines_maintenance'] ?></td>
                                            <td><?= $value['regulation_5s'] ?></td>
                                            <td><?= ViewHtmlImagesDt(!empty($value['img']) ? $value['img'] : '') ?></td>
                                            <td><?= !empty($value['staff_check']) ? get_staff_full_name($value['staff_check']) : '' ?></td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" value="1" id="check_result_true_<?=$key?>" name="check_result_<?=$value['id']?>" data-id="<?=$value['id']?>" data-value="1" <?=$value['result_id'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, this)">
                                                        <label for="check_result_true_<?=$key?>">Đạt</label>
                                                    </div>
                                                    <div class="checkbox checkbox-danger">
                                                        <input type="checkbox" value="2" id="check_result_false_<?=$key?>" name="check_result_<?=$value['id']?>" data-id="<?=$value['id']?>" data-value="2" <?=$value['result_id'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, this)">
                                                        <label for="check_result_false_<?=$key?>">Không Đạt</label>
                                                    </div>
                                                </div>
                                                <div class="check_production_report_false_<?=$value['id']?> <?=(empty($value['result_id']) || $value['result_id'] == 1) ? 'hide' : ''?>">
                                                    <a class="btn btn-info btn-icon mbot10" href="<?=admin_url('production_report/detail?id_suggest_pccc_detail=' . $value['id'])?>" target="_blank">Tạo phiếu báo cáo</a>
                                                </div>
                                            </td>
                                            <td><?= $value['evaluate'] ?></td>
                                            <td><?= !empty($value['staff_manager']) ? get_staff_full_name($value['staff_manager']) : '' ?></td>
                                        </tr>
                                    <?php }?>
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
    $('#view_suggest_pccc').on('click', '.event-plus', function() {
        var TrClass = $(this).attr('data-class');
        if($(this).hasClass('show')) {
            $(this).removeClass('show')
            $(this).find('i').removeClass('fa-minus-square-o');
            $(this).find('i').addClass('fa-plus-square-o');
            $(`.${TrClass}`).addClass('hide');
        }
        else {
            $(this).addClass('show');
            $(this).find('i').removeClass('fa-plus-square-o');
            $(this).find('i').addClass('fa-minus-square-o');
            $(`.${TrClass}`).removeClass('hide');
        }
    })
    
    
    function checkResult(id, _this) {
        if($(_this).prop('checked')) {
            var result = $(_this).attr('data-value');
            $(`input[name="check_result_${id}"]`).prop('checked', false);
            $(_this).prop('checked', true);
        }
        else {
            result = 0;
        }
        var data = {};
        data['id'] = id;
        data['result'] = result;
        $.get(admin_url + 'suggest_pccc/check_result', data, function(resultData) {
            resultData = JSON.parse(resultData);
            alert_float(resultData.alert_type, resultData.message);
            if(result == 2) {
                $(`.check_production_report_false_${id}`).removeClass('hide');
            }
            else {
                $(`.check_production_report_false_${id}`).addClass('hide');
            }
        })
    }
</script>