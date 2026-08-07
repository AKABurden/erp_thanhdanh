<div class="modal-dialog modal-lg" style="min-width: 75%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <span class="title"><?= $title ?></span>
            </h4>
        </div>
        <div class="modal-body">
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
                        <div><?= lang('Mã vị trí đánh giá') ?>: </div>
                        <div class="ml-at t-bold"><?= $dtData['name_role'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="lead-view" id="leadViewWrapper">
                    <div class="row-contro">
                        <div><?= lang('Người yêu cầu đánh giá') ?>: </div>
                        <div class="ml-at t-bold"><?= get_staff_full_name($dtData['staff_suggest']) ?></div>
                    </div>
                    <div class="row-contro">
                        <?php
                        $dtBranch = get_table_where('tblbranch',['id' => $dtData['branch_id']],'','row_array');
                        ?>
                        <div><?= lang('Chi nhánh') ?>: </div>
                        <div class="ml-at t-bold"><?= $dtBranch['name'] ?></div>
                    </div>
                    <div class="row-contro">
                        <div><?= lang('Lý do') ?>: </div>
                        <div class="ml-at t-bold"><?= $dtData['note'] ?></div>
                    </div>
                </div>
            </div>
            <div class="row mtop10">
                <div class="col-md-12">
                    <table id="tb-suggest-kpi" class="table dataTable">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px"><?= lang('STT') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Nhóm KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Loại KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Chi tiết KPIS') ?></th>
                            <th class="text-center" style="width: 100px"><?= lang('Mã KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Chỉ Số Đo Lường KPIs') ?></th>
                            <th class="text-center" style="width: 80px"><?= lang('Target') ?></th>
                            <th class="text-center" style="width: 80px"><?= lang('Trọng số (%)') ?></th>
                            <th class="text-center" style="width: 200px"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Báo Cáo Không Phù Hợp') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Kết quả') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $stt = 0; $total_weight = 0;$total_weight1 = 0;
                        if (!empty($dtItems)) { ?>
                            <?php foreach ($dtItems as $key => $value) { ?>
                                <?php
                                if ($value['type'] == 2){
                                    continue;
                                } else {
                                    $stt ++;
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div class="text-center"><?= ($stt) ?></div>
                                    </td>
                                    <td>
                                        <div>
                                            <?= $value['name_category'] ?>
                                        </div>
                                    </td>
                                    <td><div class="td_type"><?= $value['type'] == 1 ? "Năng Lực" : "Tuân Thủ" ?></div></td>
                                    <td><div class="td_name_kpi"><?= $value['name_kpi'] ?></div></td>
                                    <td><div class="td_code_kpi"><?= $value['code_kpi'] ?></div></td>
                                    <td><div class="td_measure"><?= $value['measure'] ?></div></td>
                                    <td><div class="td_target text-center"><?= $value['time'] ?></div></td>
                                    <td><div class="td_weight text-center"><?= $value['weight'] ?></div></td>
                                    <td><div class="td_regulations"><?= $value['regulations'] ?></div></td>
                                    <td class="text-center"><?= $value['report'] ?> Phiếu</td>
                                    <td>
                                        <?= $value['name_result'] ?>
                                    </td>
                                </tr>
                                <?php
                                $total_weight += $value['weight'];
                            } ?>
                        <?php } $total_weight1 = $total_weight; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="total_weight text-center bold"><?= $total_weight ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="bold uppercase">% KPI</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center" style="color: red"><?= 80 * $total_weight / 100 ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tfoot>

                    </table>
                    <table id="tb-suggest-kpi-new" class="table dataTable" style="margin-top: 20px !important;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px"><?= lang('STT') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Nhóm KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Loại KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Chi tiết KPIS') ?></th>
                            <th class="text-center" style="width: 100px"><?= lang('Mã KPIS') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Chỉ Số Đo Lường KPIs') ?></th>
                            <th class="text-center" style="width: 80px"><?= lang('Target') ?></th>
                            <th class="text-center" style="width: 80px"><?= lang('Trọng số (%)') ?></th>
                            <th class="text-center" style="width: 200px"><?= lang('Tiêu chuẩn/ quy định') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Báo Cáo Không Phù Hợp') ?></th>
                            <th class="text-center" style="width: 150px"><?= lang('Kết quả') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $stt = 0;$total_weight = 0;
                        if (!empty($dtItems)) { ?>
                            <?php foreach ($dtItems as $kkk => $value) { ?>
                                <?php
                                if ($value['type'] == 1){
                                    continue;
                                } else {
                                    $stt ++;
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div class="text-center"><?= ($stt) ?></div>
                                    </td>
                                    <td>
                                        <div>
                                            <?= $value['name_category'] ?>
                                        </div>
                                    </td>
                                    <td><div class="td_type"><?= $value['type'] == 1 ? "Năng Lực" : "Tuân Thủ" ?></div></td>
                                    <td><div class="td_name_kpi"><?= $value['name_kpi'] ?></div></td>
                                    <td><div class="td_code_kpi"><?= $value['code_kpi'] ?></div></td>
                                    <td><div class="td_measure"><?= $value['measure'] ?></div></td>
                                    <td><div class="td_target text-center"><?= $value['time'] ?></div></td>
                                    <td><div class="td_weight text-center"><?= $value['weight'] ?></div></td>
                                    <td><div class="td_regulations"><?= $value['regulations'] ?></div></td>
                                    <td class="text-center"><?= $value['report'] ?> Phiếu</td>
                                    <td>
                                        <?= $value['name_result'] ?>
                                    </td>
                                </tr>
                                <?php
                                $total_weight += $value['weight'];
                            } ?>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" class="bold uppercase">Tổng Cộng</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="total_weight text-center bold"><?= $total_weight ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="bold uppercase">% KPI</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center" style="color: red"><?= (20 * $total_weight / 100) ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="bold uppercase">Tổng KPI</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center" style="color: red"><?= (80 * $total_weight1 / 100) + (20 * $total_weight / 100) ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        </div>
    </div>
</div>