<div class="modal-dialog modal-lg" style="width: 85%;">
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
                            <?php
                                $dtPo = get_table_where('tbl_productions_orders',['id' => $dtData['po_id']],'','row_array');
                            ?>
                            <div><?= lang('Lệnh sản xuất') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtPo['reference_no'] ?></div>
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
                            <div><?= lang('Người lập kế hoạch') ?>: </div>
                            <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_plan']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>: </div>
                            <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">

                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><i class="icon-foso fal fa-info-circle"></i><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                        <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?></label>


                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-items" class="table dt-tnh table-hover table-condensed table-cs-border">
                                        <thead>
                                        <tr>
                                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                                            <th style="width: 100px;"><?= lang('Mã thành phẩm') ?></th>
                                            <th style="width: 150px;"><?= lang('Tên thành phẩm') ?></th>
                                            <th style="width: 100px;"><?= lang('Quy cách') ?></th>
                                            <th style="width: 50px;"><?= lang('ĐVT') ?></th>
                                            <th style="width: 100px;"><?= lang('Đơn Vị Gia Công(NCC)') ?></th>
                                            <th style="width: 100px;"><?= lang('Chi Tiết Gia Công') ?></th>
                                            <th style="width: 100px;"><?= lang('Tổng SL') ?></th>
                                            <th style="width: 100px;"><?= lang('Đơn Giá') ?></th>
                                            <th style="width: 100px;"><?= lang('Thành Tiền') ?></th>
                                            <th style="width: 150px;"><?= lang('Đơn Vị Vận Chuyển Gia Công') ?></th>
                                            <th style="width: 100px;"><?= lang('Phương Tiện Vận Chuyển Gia Công') ?></th>
                                            <th style="width: 100px;"><?= lang('Ngày Đi Gia Công') ?></th>
                                            <th style="width: 100px;"><?= lang('Ngày Về Gia Công') ?></th>
                                            <th style="width: 100px;"><?= lang('Kết quả') ?></th>
                                            <th style="width: 100px;"><?= lang('Nhân viên') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (!empty($dtDataItems)){ ?>
                                            <?php foreach ($dtDataItems as $key => $value){ ?>
                                                <?php
                                                $item_id = $value['item_id'];
                                                $type_item = $value['type_item'];
                                                $info = null;
                                                if ($type_item == "products") {
                                                    $info = $this->products_model->rowProduct($item_id);
                                                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= (++$key) ?></td>
                                                    <td>
                                                        <div class="code_item"><?= $info['code'] ?></div>
                                                    </td>
                                                    <td><div><?= $info['name'] ?></div></td>
                                                    <td><div><?= $info['mode'] ?></div></td>
                                                    <td class="text-center"><div class="unit_item"><?= $unit['unit'] ?></div></td>
                                                    <td class="text-left"><?= ($value['company']) ?></td>
                                                    <td class="text-left"><?= ($value['detail']) ?></td>
                                                    <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                                    <td class="text-center"><?= formatMoney($value['price']) ?></td>
                                                    <td class="text-right"><?= formatMoney($value['amount']) ?></td>
                                                    <td class="text-left"><?= ($value['shipping_unit_outsource']) ?></td>
                                                    <td class="text-left"><?= ($value['transport_outsource']) ?></td>
                                                    <td><div class="text-left"><?= !empty($value['date_start_outsource']) ? _dhau($value['date_start_outsource']) : '' ?></div></td>
                                                    <td><div class="text-left"><?= !empty($value['date_end_outsource']) ? _dhau($value['date_end_outsource']) : '' ?></div></td>
                                                    <td style="width: 150px;">
<!--                                                        <div class="text-left">--><?//= ($value['name_result']) ?><!--</div>-->
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="result_id_<?=$value['id']?>" name="check_result_<?=$value['id']?>" data-id="<?=$value['id']?>" data-value="1" <?=$value['result_id'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, this)">
                                                                <label for="result_id_<?=$value['id']?>">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="result_id_<?=$value['id']?>" name="check_result_<?=$value['id']?>" data-id="<?=$value['id']?>" data-value="2" <?=$value['result_id'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, this)">
                                                                <label for="result_id_<?=$value['id']?>">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                        <?php
                                                            if(!empty($value['name_report'])) {
																$arrReport = $value['name_report'];
																$htmlReport = '';
																if (!empty($arrReport)) {
																	$arrReport = explode('||', $arrReport);
																	if (!empty($arrReport)) {
																		foreach ($arrReport as $kk => $vv) {
																			$vv = explode('__', $vv);
																			$htmlReport .= '<a class="c_modal mtop20" href="' . (admin_url('production_report/modal/' . $vv[1])) . '">' . $vv[0] . '</a>';
																		}
																	}
																}
																echo '<div class="mtop20 check_production_report_false_'.$value['id'].'">' . $htmlReport . '</div>';
															}
                                                            else {?>
                                                                <div class="check_production_report_false_<?=$value['id']?> <?=(empty($value['result_id']) || $value['result_id'] == 1) ? 'hide' : ''?>">
                                                                    <a class="mtop10 btn btn-info btn-icon mbot10" href="<?=admin_url('production_report/detail?id_suggest_plan_outsource_detail=' . $value['id'])?>" target="_blank">Tạo phiếu báo cáo</a>
                                                                </div>
                                                            <?php } ?>
                                                    </td>
                                                    <td><div class="text-left"><?= get_staff_full_name($value['staff_id']) ?></div></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="uppercase bold">Tổng cộng</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                    $history = getActivityLogByObjId($dtData['id'], 'suggest_plan_outsource');
                                    ?>
                                    <?php if (!empty($history)) : ?>
                                        <?php foreach ($history as $key => $value) : ?>
                                            <?php
                                            echo '<div class="feed-item">
                                                <div class="activity-text">
                                                    ' . staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small') . '' . $value['staff_name'] . '
                                                </div>
                                                <div class="activity-time">
                                                    ' . time_ago($value['date']) . '<span class="activity-module">' . _l($value['type_parent_obj']) . '</span>
                                                </div>
                                                <div>
                                                    ' . $value['content'] . '
                                                </div>
                                            </div>';
                                            ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>
                            </section>
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
        <a data-tnh="modal" class="tnh-modal hide click1"
           href=" <?= base_url() ?>admin/suggest_plan_overtime/view/<?= $dtData['id'] ?>" data-toggle="modal"
           data-target="#myModal"></a>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var dtItems = $('#table-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // scrollY: true,
            // scrollX: true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var apiSub = this.api(),
                    data;
                pageQuantity = apiSub
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageAmount = apiSub
                    .column(9, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(apiSub.column(7).footer()).html('<div class="text-center bold">' + tnhFormatNumber(pageQuantity) + '</div>');
                $(apiSub.column(9).footer()).html('<div class="text-right bold">' + tnhFormatMoney(pageAmount) + '</div>');
            }
        });
    });


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
        $.get(admin_url + 'suggest_plan_outsource/check_result', data, function(resultData) {
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