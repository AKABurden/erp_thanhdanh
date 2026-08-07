<div class="modal fade" id="view_modal" role="dialog">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? $title : ''; ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/internal_proposal/add_task_process/', array('id' => 'task')); ?>
            <div class="modal-body">
                <input class="hide" id="process_id" name="process_id" value="<?= $process_id ?>">
                <input class="hide" id="detail_id" name="detail_id" value="<?= $detail_id ?>">
                <input class="hide" id="id" name="id" value="<?= $id ?>">
                <div class="row">
                    <div class="col-md-12">
                        <table id="tb-handling-products-stages" class="table dataTable tb-handling-products-stages">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:23%;">Quy Chuẩn Công Việc</th>
                                    <th class="text-center" style="width:23%;">Quy Chuẩn Duyệt</th>
                                    <th class="text-center" style="width:23%;">Quy Chuẩn Kiểm Soát Hoàn Thành</th>
                                    <th class="text-center" style="width:15%;">
                                        <label>Duyệt</label>
                                    </th>
                                    <th class="text-center" style="width:15%;">
                                        <label>Không duyệt</label>
                                    </th>
                                    <th class="text-center" style="width:15%;">
                                        Tạo báo cáo không phù hợp
                                    </th>
                                    <th class="text-center" style="width:15%;">
                                        Trạng thái Báo cáo không phù hợp
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($category_hand_over as $key => $value) { ?>
                                    <?php
                                    $isCheck = 'checked';
                                    $isCheckNot = '';
                                    $isCheckNot_is = 0;
                                    $check = get_table_where('tbl_tinternal_proposal_inspection_criteria_process', ['id_internal_proposal' => $id, 'process_id' => $process_id, 'id_internal_proposal_process' => $detail_id, 'inspection_criteria' => $value['id']], '', 'row_array');
                                    if (!empty($check)) {
                                        if ($check['isCheck'] == 1) {
                                            $isCheck = 'checked';
                                        }
                                        if ($check['isCheckNot'] == 1) {
                                            $isCheckNot = 'checked';
                                            $isCheck = '';
                                            $isCheckNot_is = 1;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" <?= $isCheck ?> name="inspection_criteria_id[<?= $key ?>]"
                                                value="<?= $value['id'] ?>">
                                            <?= $value['name'] ?>
                                        </td>
                                        <td>
                                            <input type="hidden" <?= $isCheck ?> name="inspection_criteria_id[<?= $key ?>]"
                                                value="<?= $value['id'] ?>">
                                            <?= $value['approval_standards'] ?>
                                        </td>
                                        <td>
                                            <input type="hidden" <?= $isCheck ?> name="inspection_criteria_id[<?= $key ?>]"
                                                value="<?= $value['id'] ?>">
                                            <?= $value['completion_control_standards'] ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" <?= $isCheck ?>
                                                    class="tb-handling-products-stages-child isCheck_<?= $value['id'] ?>"
                                                    onclick="checkResult(<?= $value['id'] ?>, this)"
                                                    name="isCheck[<?= $value['id'] ?>]">
                                                <label for="isCheck"></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="checkbox checkbox-danger">
                                                <input type="checkbox" <?= $isCheckNot ?>
                                                    class="tb-handling-products-stages-child_no isCheck_<?= $value['id'] ?>"
                                                    onclick="checkResult(<?= $value['id'] ?>, this)"
                                                    name="isCheckNot[<?= $value['id'] ?>]">
                                                <label for="isCheckNot"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($isCheckNot_is) { ?>
                                                <?php
                                                $Success_process = false;
                                                if (!empty($production_report)) {
                                                    $this->db->select('tbl_process_production_report.*');
                                                    $this->db->where('tbl_process_production_report.staff_process', 0);
                                                    $this->db->where('tbl_process_production_report.production_report_id', $production_report['id']);
                                                    $this->db->from('tbl_process_production_report');
                                                    $Success_process = $this->db->get()->num_rows();
                                                }
                                                ?>
                                                <?php
                                                $production_report = get_table_where('tblproduction_report', ['id_internal_proposal' => $id, 'id_internal_proposal_process' => $detail_id, 'id_internal_proposal_process_child' => $value['id']], '', 'row_array');
                                                ?>
                                                <?php if (empty($production_report)) { ?>
                                                    <a class="btn btn-info btn-icon mbot10"
                                                        href="<?= base_url('admin/production_report/detail') . '?id_internal_proposal=' . $id . '&id_internal_proposal_process=' . $detail_id . '&id_internal_proposal_process_child=' . $value['id'] ?>"
                                                        target="_blank">Tạo phiếu báo cáo</a>
                                                <?php } else { ?>
                                                    <a class="c_modal"
                                                        href="<?= base_url('admin/production_report/modal/') . $production_report['id'] ?>"><?= $production_report['reference_no'] ?></a>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($isCheckNot) { ?>
                                                <?php if (!empty($production_report)) { ?>
                                                    <?php if ($Success_process > 0) { ?>
                                                        <span class="label label-warning">Chưa hoàn thành</span>
                                                    <?php } else { ?>
                                                        <span class="label label-success">Hoàn thành</span>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    $('#view_modal').modal('show');
</script>