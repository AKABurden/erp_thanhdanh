<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_view_kpi') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_no') ?>: </div>
                            <div class="ml-at t-bold"><?= $kpi['reference_no'] ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Họ và tên') ?>: </div>
                            <div class="ml-at t-bold"><?= $staff_name ?></div>
                        </div>
                        <div class="row-contro hide">
                            <div><?= lang('tnh_target_reception_time') ?>: </div>
                            <div class="ml-at t-bold"><?= _d($kpi['target_reception_time']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('start_date') ?>: </div>
                            <div class="ml-at t-bold"><?= _d($kpi['start_date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Chức danh') ?>: </div>
                            <div class="ml-at t-bold"><?= $role ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('end_date') ?>: </div>
                            <div class="ml-at t-bold"><?= _d($kpi['end_date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Phòng ban') ?>: </div>
                            <div class="ml-at t-bold"><?= $department ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="bold"><?= lang('tnh_title_kpi_1') ?></div>
                    <table id="tb-kpi" class="table table-hover dataTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" rowspan="2" style="width: 50px;"><?= lang('STT') ?></th>
                                <th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Tiêu chí') ?></th>
                                <th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Đơn vị tính') ?></th>
                                <th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Mục tiêu') ?></th>
                                <th class="text-center" rowspan="2" style="width: 80px;"><?= lang('tnh_weight_number') ?></th>
                                <th class="text-center" style="width: 80px;">1.<?= lang('tnh_not_reached') ?></th>
                                <th class="text-center" style="width: 80px;">2.<?= lang('tnh_need_keep_trying') ?></th>
                                <th class="text-center" style="width: 80px;">3.<?= lang('tnh_obtain') ?></th>
                                <th class="text-center" style="width: 80px;">4.<?= lang('tnh_pass') ?></th>
                                <th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Tổng điểm') ?></th>
                                <th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Phương pháp đánh giá') ?></th>
                            </tr>
                            <tr>
                                <th class="text-center" style="border-bottom: 1px solid;"><?= lang('(1 điểm)') ?></th>
                                <th class="text-center" style="border-bottom: 1px solid;"><?= lang('(2 điểm)') ?></th>
                                <th class="text-center" style="border-bottom: 1px solid;"><?= lang('(3 điểm)') ?></th>
                                <th class="text-center" style="border-bottom: 1px solid;"><?= lang('(4 điểm)') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_weight_number = 0;
                            $total_result = 0;
                            $counter = 0;
                            ?>
                            <?php if (!empty($kpi)) : ?>
                                <?php
                                $this->db->select('tbl_kpi_items.*, tbl_kpi_criteria.criteria, tbl_kpi_criteria.unit, tbl_kpi_criteria.note_criteria as note_criteria, tbl_kpi_criteria.id as id_kpi_criteria', false);
                                $this->db->from('tbl_kpi_items');
                                $this->db->join('tbl_kpi_criteria', 'tbl_kpi_criteria.id = tbl_kpi_items.kpi_criteria_id');
                                $this->db->where('tbl_kpi_items.kpi_id', $kpi['id']);
                                $this->db->where('tbl_kpi_items.type', 0);
                                $this->db->order_by('tbl_kpi_items.id ASC');
                                $kpi_items = $this->db->get()->result_array();
                                ?>
                                <?php if (!empty($kpi_items)) : ?>
                                    <?php foreach ($kpi_items as $key => $value) : ?>
                                        <tr>
                                            <td class="text-center td-numbers"><?= ++$key ?></td>
                                            <td><?= $value['criteria'] ?></td>
                                            <td class="text-center"><?= $value['unit'] ?></td>
                                            <td class="text-center">
                                                <span class="txt-target"><?= $value['target'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="txt-weight_number"><?= $value['weight_number'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                // echo $value['not_reached'] ? calRecipe($value['not_reached']) : '';
                                                echo ' ' . $value['not_reached_from'];
                                                ?>
                                                <div class="radio radio-primary" style="pointer-events: none;">
                                                    <input type="radio" name="chonse[<?= $counter ?>]" onchange="totalKpi()" class="chonse_not_reached chonse" <?= $value['chonse'] == 1 ? 'checked' : '' ?> id="chonse_not_reached_<?= $counter ?>" value="1">
                                                    <label for="chonse_not_reached_<?= $counter ?>"><?= lang('choose') ?></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                // echo $value['need_keep_trying'] ? calRecipe($value['need_keep_trying']) : '';
                                                echo ' ' . $value['need_keep_trying_from'];
                                                ?>
                                                <div class="radio radio-primary">
                                                    <input type="radio" name="chonse[<?= $counter ?>]" onchange="totalKpi()" class="chonse_need_keep_trying chonse" <?= $value['chonse'] == 2 ? 'checked' : '' ?> id="chonse_need_keep_trying_<?= $counter ?>" value="2">
                                                    <label for="chonse_need_keep_trying_<?= $counter ?>"><?= lang('choose') ?></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                // echo $value['obtain'] ? calRecipe($value['obtain']) : '';
                                                echo ' ' . $value['obtain_from'];
                                                ?>
                                                <div class="radio radio-primary" style="pointer-events: none;">
                                                    <input type="radio" name="chonse[<?= $counter ?>]" <?= $value['chonse'] == 3 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_obtain chonse" id="chonse_obtain_<?= $counter ?>" value="3">
                                                    <label for="chonse_obtain_<?= $counter ?>"><?= lang('choose') ?></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                // echo $value['pass'] ? calRecipe($value['pass']) : '';
                                                echo ' ' . $value['pass_from'];
                                                ?>
                                                <div class="radio radio-primary" style="pointer-events: none;">
                                                    <input type="radio" name="chonse[<?= $counter ?>]" <?= $value['chonse'] == 4 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_pass chonse" id="chonse_pass_<?= $counter ?>" value="4">
                                                    <label for="chonse_pass_<?= $counter ?>"><?= lang('choose') ?></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-center div-result"><?= !empty($value['result']) ? $value['result'] : 0 ?></div>
                                            </td>
                                            <td>
                                                <?= $value['note_criteria'] ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $total_weight_number += $value['weight_number'];
                                        $total_result += $value['result'];
                                        $counter++;
                                        ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="not-tr bold uppercase">
                                <td style="border-top: 1px solid #cedae6;" colspan="4" class="text-center"><?= lang('tnh_total') ?></td>
                                <td style="border-top: 1px solid #cedae6;" class="text-center txt-total-weight text-danger"><?= formatNumber($total_weight_number) ?></td>
                                <td style="border-top: 1px solid #cedae6;"></td>
                                <td style="border-top: 1px solid #cedae6;"></td>
                                <td style="border-top: 1px solid #cedae6;"></td>
                                <td style="border-top: 1px solid #cedae6;"></td>
                                <td style="border-top: 1px solid #cedae6;" class="txt-total_point_with_coefficient text-center text-danger"><?= formatNumber($total_result) ?></td>
                                <td style="border-top: 1px solid #cedae6;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12 mtop15">
                    <div class="bold"><?= lang('tnh_title_kpi_2') ?></div>
                    <table id="tb-kpi-2" class="table table-hover dataTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" rowspan="2" style="width: 50px;"><?= lang('STT') ?></th>
                                <th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Tiêu chí') ?></th>
                                <th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Đơn vị tính') ?></th>
                                <th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Mục tiêu') ?></th>
                                <th class="text-center" rowspan="2" style="width: 80px;"><?= lang('tnh_weight_number') ?></th>
                                <th class="text-center" style="width: 80px;">1.<?= lang('tnh_not_reached') ?></th>
                                <th class="text-center" style="width: 80px;">2.<?= lang('tnh_need_keep_trying') ?></th>
                                <th class="text-center" style="width: 80px;">3.<?= lang('tnh_obtain') ?></th>
                                <th class="text-center" style="width: 80px;">4.<?= lang('tnh_pass') ?></th>
                                <th class="text-center" rowspan="2" style="width: 80px;"><?= lang('Tổng điểm') ?></th>
                                <th class="text-center" rowspan="2" style="width: 150px;"><?= lang('Phương pháp đánh giá') ?></th>
                                <!-- <th class="text-center" rowspan="2" style="width: 50px;"><i class="fa fa-trash-o"></i></th> -->
                            </tr>
                            <tr>
                                <th class="text-center" style="border-bottom: 1px solid;"><?= lang('(1 điểm)') ?></th>
                                <th class="text-center" style="border-bottom: 1px solid;"><?= lang('(2 điểm)') ?></th>
                                <th class="text-center" style="border-bottom: 1px solid;"><?= lang('(3 điểm)') ?></th>
                                <th class="text-center" style="border-bottom: 1px solid;"><?= lang('(4 điểm)') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_weight_number_2 = 0;
                            $total_result_2 = 0;
                            ?>
                            <?php if (empty($kpi) || !empty($kpi)) : ?>
                                <?php
                                $this->db->select('tbl_kpi_items.*, tbl_kpi_criteria.criteria, tbl_kpi_criteria.unit, tbl_kpi_criteria.note_criteria as note_criteria, tbl_kpi_criteria.id as id_kpi_criteria', false);
                                $this->db->from('tbl_kpi_items');
                                $this->db->join('tbl_kpi_criteria', 'tbl_kpi_criteria.id = tbl_kpi_items.kpi_criteria_id');
                                $this->db->where('tbl_kpi_items.kpi_id', $kpi['id']);
                                $this->db->where('tbl_kpi_items.type', 1);
                                $this->db->order_by('tbl_kpi_items.id ASC');
                                $kpi_criteria = $this->db->get()->result_array();
                                ?>
                                <?php if (!empty($kpi_criteria)) : ?>
                                    <?php foreach ($kpi_criteria as $key => $value) : ?>
                                        <tr>
                                            <td class="text-center td-numbers"><?= ++$key ?></td>
                                            <td><?= $value['criteria'] ?></td>
                                            <td class="text-center"><?= $value['unit'] ?></td>
                                            <td class="text-center">
                                                <span class="txt-target"><?= $value['target'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="txt-weight_number"><?= $value['weight_number'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                // echo $value['not_reached'] ? calRecipe($value['not_reached']) : '';
                                                echo ' ' . $value['not_reached_from'];
                                                ?>
                                                <div class="radio radio-primary" style="pointer-events: none;">
                                                    <input type="radio" name="chonse[<?= $counter ?>]" <?= !empty($value['chonse']) && $value['chonse'] == 1 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_not_reached chonse" id="chonse_not_reached_<?= $counter ?>" value="1">
                                                    <label for="chonse_not_reached_<?= $counter ?>"><?= lang('choose') ?></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                // echo $value['need_keep_trying'] ? calRecipe($value['need_keep_trying']) : '';
                                                echo ' ' . $value['need_keep_trying_from'];
                                                ?>
                                                <div class="radio radio-primary" style="pointer-events: none;">
                                                    <input type="radio" name="chonse[<?= $counter ?>]" <?= !empty($value['chonse']) && $value['chonse'] == 2 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_need_keep_trying chonse" id="chonse_need_keep_trying_<?= $counter ?>" value="2">
                                                    <label for="chonse_need_keep_trying_<?= $counter ?>"><?= lang('choose') ?></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                // echo $value['obtain'] ? calRecipe($value['obtain']) : '';
                                                echo ' ' . $value['obtain_from'];
                                                ?>
                                                <div class="radio radio-primary" style="pointer-events: none;">
                                                    <input type="radio" name="chonse[<?= $counter ?>]" <?= !empty($value['chonse']) && $value['chonse'] == 3 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_obtain chonse" id="chonse_obtain_<?= $counter ?>" value="3">
                                                    <label for="chonse_obtain_<?= $counter ?>"><?= lang('choose') ?></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                // echo $value['pass'] ? calRecipe($value['pass']) : '';
                                                echo ' ' . $value['pass_from'];
                                                ?>
                                                <div class="radio radio-primary" style="pointer-events: none;">
                                                    <input type="radio" name="chonse[<?= $counter ?>]" <?= !empty($value['chonse']) && $value['chonse'] == 4 ? 'checked' : '' ?> onchange="totalKpi()" class="chonse_pass chonse" id="chonse_pass_<?= $counter ?>" value="4">
                                                    <label for="chonse_pass_<?= $counter ?>"><?= lang('choose') ?></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-center div-result"><?= !empty($value['result']) ? $value['result'] : 0 ?></div>
                                            </td>
                                            <td>
                                                <?= $value['note_criteria'] ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $total_weight_number_2 += $value['weight_number'];
                                        $total_result_2 += $value['result'];
                                        $counter++;
                                        ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="not-tr bold uppercase">
                                <td style="border-top: 1px solid #cedae6;" colspan="4" class="text-center"><?= lang('tnh_total') ?></td>
                                <td style="border-top: 1px solid #cedae6;" class="text-center txt-total-weight text-danger"><?= formatNumber($total_weight_number_2) ?></td>
                                <td style="border-top: 1px solid #cedae6;"></td>
                                <td style="border-top: 1px solid #cedae6;"></td>
                                <td style="border-top: 1px solid #cedae6;"></td>
                                <td style="border-top: 1px solid #cedae6;"></td>
                                <td style="border-top: 1px solid #cedae6;" class="txt-total_point_with_coefficient text-center text-danger"><?= formatNumber($total_result_2) ?></td>
                                <td style="border-top: 1px solid #cedae6;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php
                $not_reached = get_option('not_reached');
                $not_reached_from = get_option('not_reached_from');
                $not_reached_to = get_option('not_reached_to');
                $need_keep_trying = get_option('need_keep_trying');
                $need_keep_trying_from = get_option('need_keep_trying_from');
                $need_keep_trying_to = get_option('need_keep_trying_to');
                $obtain = get_option('obtain');
                $obtain_from = get_option('obtain_from');
                $obtain_to = get_option('obtain_to');
                $pass = get_option('pass');
                $pass_from = get_option('pass_from');
                $pass_to = get_option('pass_to');
                ?>

                <div class="col-md-12 mtop15">
                    <div class="bold"><?= lang('III. LỖI - SỰ CỐ') ?></div>
                    <table id="tb-error" class="table table-hover dataTable">
                        <thead>
                            <th class="text-center" style="width: 50px; background: #f443366b !important;"><?= lang('tnh_numbers') ?></th>
                            <th class="text-center" style="background: #f443366b !important;"><?= lang('Vi phạm') ?></th>
                            <th class="text-center" style="background: #f443366b !important;"><?= lang('Số phiếu') ?></th>
                            <th class="text-center" style="background: #f443366b !important;"><?= lang('Điểm') ?></th>
                        </thead>
                        <tbody>
                            <?php
                            $counterPoint = 0;
                            if (!empty($kpi)) {
                                $this->db->simple_query('SET SESSION group_concat_max_len=1844674407370955161');
                                $tb_kpi_trouble_violation_items = "(
                                        SELECT
                                            tbl_kpi_trouble_violation_items.kpi_trouble_violation_id as kpi_trouble_violation_id,
                                            GROUP_CONCAT(tbl_kpi_trouble_violation_items.production_report_id SEPARATOR '|||') as production_report_id
                                        FROM tbl_kpi_trouble_violation_items
                                        WHERE tbl_kpi_trouble_violation_items.kpi_id = " . $kpi['id'] . "
                                        GROUP BY tbl_kpi_trouble_violation_items.kpi_trouble_violation_id
                                    ) tb_kpi_trouble_violation_items";

                                $this->db->select('
                                        tbl_kpi_trouble_violation.*,
                                        tbltrouble_violation_point.name,
                                        tb_kpi_trouble_violation_items.production_report_id as production_report_id
                                    ', false);
                                $this->db->from('tbl_kpi_trouble_violation');
                                $this->db->join('tbltrouble_violation_point', 'tbltrouble_violation_point.id = tbl_kpi_trouble_violation.trouble_violation_point_id');
                                $this->db->join($tb_kpi_trouble_violation_items, 'tb_kpi_trouble_violation_items.kpi_trouble_violation_id = tbl_kpi_trouble_violation.id', 'left');
                                $this->db->where('tbl_kpi_trouble_violation.kpi_id', $kpi['id']);
                                $kpi_trouble_violation = $this->db->get()->result_array();
                            }
                            ?>
                            <?php
                            $total_violation_point = 0;
                            ?>
                            <?php if (!empty($kpi_trouble_violation)) : ?>
                                <?php foreach ($kpi_trouble_violation as $key => $value) : ?>
                                    <?php
                                    $tdNumber = '<td class="text-center td-numbers">' . (++$key) . '</td>';
                                    $tdProblem = '<td>' . $value['name'] . '</td>';
                                    $tdVote = '<td class="text-center">' . $value['count_vote'] . ' phiếu</td>';
                                    $tdPoint = '<td class="text-center">
                                            ' . $value['violation_point'] . '
                                        </td>';

                                    echo '<tr>
                                            ' . $tdNumber . '
                                            ' . $tdProblem . '
                                            ' . $tdVote . '
                                            ' . $tdPoint . '
                                        </tr>';
                                    $counterPoint++;
                                    $total_violation_point += $value['violation_point'];
                                    ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bold">
                                <td></td>
                                <td class="text-center"><?= lang('TỔNG') ?></td>
                                <td></td>
                                <td class="text-center txt-grand-total-error text-danger"><?= formatNumber($total_violation_point) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12 mtop15">
                    <div class="bold"><?= lang('IV. KHEN THƯỞNG') ?></div>
                    <table id="tb-bonus" class="table table-hover dataTable">
                        <thead>
                            <th class="text-center" style="width: 50px; background: #4caf507d !important;">
                                <?= lang('tnh_numbers') ?>
                            </th>
                            <th class="text-center" style="background: #4caf507d !important;"><?= lang('Nội dung khen thưởng') ?></th>
                            <th class="text-center" style="background: #4caf507d !important;"><?= lang('Điểm') ?></th>
                        </thead>
                        <tbody>
                            <?php
                            $counterBonus = 0;
                            $kpi_bonus = !empty($kpi) ? $this->kpi_model->getKpiBonus($kpi['id']) : null;
                            ?>
                            <?php if (!empty($kpi_bonus)) : ?>
                                <?php foreach ($kpi_bonus as $key => $value) : ?>
                                    <?php
                                    $tdNumber = '<td class="text-center td-numbers">' . (++$key) . '</td>';
                                    $tdContent = '<td>
                                        ' . $value['content'] . '
									</td>';
                                    $tdPoint = '<td class="text-center">
                                        ' . $value['point'] . '
									</td>';

                                    echo '<tr>
										' . $tdNumber . '
										' . $tdContent . '
										' . $tdPoint . '
									</tr>';
                                    ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bold">
                                <td></td>
                                <td class="text-center uppercase"><?= lang('tnh_total') ?></td>
                                <td class="text-danger text-center td-total-bonus"><?= formatnumber($kpi['total_point_bonus']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12 mtop15">
                    <div class="bold"><?= lang('KẾT QUẢ ĐÁNH GIÁ CHUNG') ?></div>
                    <table class="table table-hover dataTable">
                        <tr class="not-tr bold text-danger">
                            <td colspan="4" style="border-top: 1px solid #cedae6;" class="text-center"><?= lang('Tổng số') ?></td>
                            <td colspan="9" style="border-top: 1px solid #cedae6;" class="txt-point-kpi text-center"><?= formatNumber($kpi['point_kpi']) ?></td>
                        </tr>
                        <tr class="not-tr text-center bold text-primary">
                            <td colspan="3">
                                <?php
                                echo $not_reached ? calRecipe($not_reached) : '';
                                if (!empty($not_reached)) {
                                    echo ' ' . $not_reached_from . ($not_reached == 4 ? ' - ' . $not_reached_to : '');
                                }
                                ?>
                            </td>
                            <td colspan="3">
                                <?php
                                echo $need_keep_trying ? calRecipe($need_keep_trying) : '';
                                if (!empty($need_keep_trying)) {
                                    echo ' ' . $need_keep_trying_from . ($need_keep_trying == 4 ? ' - ' . $need_keep_trying_to : '');
                                }
                                ?>
                            </td>
                            <td colspan="3">
                                <?php
                                echo $obtain ? calRecipe($obtain) : '';
                                if (!empty($obtain)) {
                                    echo ' ' . $obtain_from . ($obtain == 4 ? ' - ' . $obtain_to : '');
                                }
                                ?>
                            </td>
                            <td colspan="3">
                                <?php
                                echo $pass ? calRecipe($pass) : '';
                                if (!empty($pass)) {
                                    echo ' ' . $pass_from . ($pass == 4 ? ' - ' . $pass_to : '');
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="not-tr text-center bold text-primary">
                            <td colspan="3" <?= $kpi['result_kpi'] == 1 ? 'class="bg-primary"' : '' ?>><?= lang('tnh_not_reached') ?></td>
                            <td colspan="3" <?= $kpi['result_kpi'] == 2 ? 'class="bg-primary"' : '' ?>><?= lang('tnh_need_keep_trying') ?></td>
                            <td colspan="3" <?= $kpi['result_kpi'] == 3 ? 'class="bg-primary"' : '' ?>><?= lang('tnh_obtain') ?></td>
                            <td colspan="3" <?= $kpi['result_kpi'] == 4 ? 'class="bg-primary"' : '' ?>><?= lang('tnh_pass') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-12 mtop15">
                    <div class="bold"><?= lang('III. NHẬN XÉT TỔNG THỂ') ?></div>
                    <table class="table table-hover dataTable">
                        <tfoot>
                            <tr class="not-tr">
                                <td colspan="1" class="bold" style="border-top: 1px solid #cedae6; width: 150px;"><?= lang('Ưu điểm:') ?></td>
                                <td colspan="1" style="border-top: 1px solid #cedae6;">
                                    <?= !empty($kpi) ? $kpi['advantage'] : '' ?>
                                </td>
                            </tr>
                            <tr class="not-tr">
                                <td colspan="1" class="bold"><?= lang('Những mặt cần khác phục, cố gắng hơn:') ?></td>
                                <td colspan="1">
                                    <?= !empty($kpi) ? $kpi['fix_try'] : '' ?>
                                </td>
                            </tr>
                            <tr class="not-tr">
                                <td colspan="1" class="bold"><?= lang('Các nhận xét khác:') ?></td>
                                <td colspan="1">
                                    <?= !empty($kpi) ? $kpi['note'] : '' ?>
                                </td>
                            </tr>
                            </tbody>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>