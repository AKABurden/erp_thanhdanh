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
                            <div><?= lang('tnh_date_creted') ?>:</div>
                            <div class="ml-at t-bold"><?= _dt($dtData['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Số phiếu yêu cầu') ?>:</div>
                            <div class="ml-at t-bold"><?= $dtData['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Người đánh giá') ?>:</div>
                            <div class="ml-at t-bold"><?= !empty($dtData['staff_evaluate']) ? get_staff_full_name($dtData['staff_evaluate']) : '' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <?php
                            $dtBranch = get_table_where('tblbranch', ['id' => $dtData['branch_id']], '', 'row_array');
                            ?>
                            <div><?= lang('Chi nhánh') ?>:</div>
                            <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('note') ?>:</div>
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
                        <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?>
                        </label>


                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="table-items" class="table dt-tnh table-hover table-condensed table-cs-border">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 40px;"><?= lang('tnh_numbers') ?></th>
                                                <th><?= lang('Mã đào tạo') ?></th>
                                                <th><?= lang('Tên đào tạo') ?></th>
                                                <th><?= lang('Vị trí đào tạo') ?></th>
                                                <th><?= lang('Chi tiết đào tạo') ?></th>
                                                <th><?= lang('Số người tham gia') ?></th>
                                                <th><?= lang('Người phụ trách đào tạo') ?></th>
                                                <th><?= lang('Đơn vị đào tạo') ?></th>
                                                <th><?= lang('Chi phí đào tạo') ?></th>
                                                <th><?= lang('Thuế vat') ?></th>
                                                <th><?= lang('Thành tiền') ?></th>
                                                <th style="width: 120px;"><?= lang('Kết quả') ?></th>
                                                <th><?= lang('Tiêu chuẩn/ quy định') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($dtDataItems)) { ?>
                                                <?php foreach ($dtDataItems as $key => $value) { ?>
                                                    <tr>
                                                        <td class="text-center"><?= (++$key) ?></td>
                                                        <td>
                                                            <div class="code_item">
                                                                <?= $value['code_evaluate'] ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="name_item"><?= $value['name_evaluate'] ?></div>
                                                        </td>
                                                        <td>
                                                            <div class="position_educate"><?= $value['position_educate'] ?></div>
                                                        </td>
                                                        <td class="text-left">
                                                            <div class="detail"><?= $value['detail'] ?></div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div><?= formatNumber($value['quantity']) ?></div>
                                                        </td>
                                                        <td class="text-left">
                                                            <div><?= get_staff_full_name($value['staff_educate']) ?></div>
                                                        </td>
                                                        <td class="text-left">
                                                            <div><?= ($value['unit_educate']) ?></div>
                                                        </td>
                                                        <td class="text-right">
                                                            <?= formatMoney($value['cost_money']) ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <div><?= ($value['name_tax']) ?></div>
                                                        </td>
                                                        <td class="text-right">
                                                            <?= formatMoney($value['total']) ?>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <div class="checkbox checkbox-primary">
                                                                    <input type="checkbox" value="1" id="check_result_true_<?= $key ?>" name="check_result_<?= $value['id'] ?>" data-id="<?= $value['id'] ?>" data-value="1" <?= $value['result_id'] == 1 ? 'checked' : '' ?> onclick="checkResult(<?= $value['id'] ?>, this)">
                                                                    <label for="check_result_true_<?= $key ?>">Đạt</label>
                                                                </div>
                                                                <div class="checkbox checkbox-danger">
                                                                    <input type="checkbox" value="2" id="check_result_false_<?= $key ?>" name="check_result_<?= $value['id'] ?>" data-id="<?= $value['id'] ?>" data-value="2" <?= $value['result_id'] == 2 ? 'checked' : '' ?> onclick="checkResult(<?= $value['id'] ?>, this)">
                                                                    <label for="check_result_false_<?= $key ?>">Không Đạt</label>
                                                                </div>
                                                            </div>
                                                            <div class="check_production_report_false_<?= $value['id'] ?> <?= (empty($value['result_id']) || $value['result_id'] == 1) ? 'hide' : '' ?>">
                                                                <a class="btn btn-info btn-icon mbot10" href="<?= admin_url('production_report/detail?id_suggest_educate_detail=' . $value['id']) ?>" target="_blank">Tạo phiếu báo cáo</a>
                                                                <?php
                                                                $querys = 'SELECT GROUP_CONCAT(CONCAT(tblproduction_report.reference_no,"__",tblproduction_report.id) SEPARATOR "||") as name_report
                                                                 FROM tblproduction_report
                                                                 WHERE tblproduction_report.object_id = ' . $value['suggest_plan_educate_id'] . ' AND tblproduction_report.object_type = "suggest_educate" and tblproduction_report.suggest_id_detail = ' . $value['id'] . '
                                                                 ';
                                                                $data_production_report = $this->db->query($querys)->row_array();
                                                                if (!empty($data_production_report)) {
                                                                    $arrReport = $data_production_report['name_report'];
                                                                    $htmlReport = '';
                                                                    if (!empty($arrReport)) {
                                                                        $arrReport = explode('||', $arrReport);
                                                                        if (!empty($arrReport)) {
                                                                            foreach ($arrReport as $kk => $vv) {
                                                                                $vv = explode('__', $vv);
                                                                                $htmlReport .= '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $vv[1])) . '">' . $vv[0] . '</a>';
                                                                            }
                                                                        }
                                                                    }
                                                                    echo '
                                                                            <div style="margin-top: 5px">' . $htmlReport . '</div>
                                                                        ';
                                                                }
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td class="text-left">
                                                            <div class="standard"><?= $value['standard'] ?></div>
                                                        </td>
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
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                    $history = getActivityLogByObjId(
                                        $dtData['id'],
                                        'suggest_educate'
                                    );
                                    ?>
                                    <?php if (!empty($history)) : ?>
                                        <?php foreach ($history as $key => $value) : ?>
                                            <?php
                                            echo '<div class="feed-item">
                                                <div class="activity-text">
                                                    ' . staff_profile_image(
                                                $value['staff_id'],
                                                array('staff-profile-image-small'),
                                                'small'
                                            ) . '' . $value['staff_name'] . '
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
                                <div><?= lang('tnh_created_by') ?>
                                    : <?= get_staff_full_name($dtData['created_by']) ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($dtData['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty(get_staff_full_name($dtData['updated_by']))) : ?>
                                    <div><?= lang('tnh_updated_by') ?>
                                        : <?= get_staff_full_name($dtData['updated_by']) ?></div>
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
                    .column(8, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                pageAmount = apiSub
                    .column(10, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(apiSub.column(8).footer()).html('<div class="text-right bold">' + tnhFormatMoney(pageQuantity) + '</div>');
                $(apiSub.column(10).footer()).html('<div class="text-right bold">' + tnhFormatMoney(pageAmount) + '</div>');
            }
        });
    });

    function checkResult(id, _this) {
        if ($(_this).prop('checked')) {
            var result = $(_this).attr('data-value');
            $(`input[name="check_result_${id}"]`).prop('checked', false);
            $(_this).prop('checked', true);
        } else {
            result = 0;
        }
        var data = {};
        data['id'] = id;
        data['result'] = result;
        $.get(admin_url + 'suggest_educate/check_result', data, function(resultData) {
            resultData = JSON.parse(resultData);
            alert_float(resultData.alert_type, resultData.message);
            if (result == 2) {
                $(`.check_production_report_false_${id}`).removeClass('hide');
            } else {
                $(`.check_production_report_false_${id}`).addClass('hide');
            }
        })
    }
</script>