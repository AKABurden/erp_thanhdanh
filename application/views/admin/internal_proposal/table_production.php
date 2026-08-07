<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .staff-profile-image-small-check {
        width: 15px;
        height: 15px;
    }
</style>
<table id="tb-handling-products-stages" class="table dataTable tb-handling-products-stages">
    <thead>
        <tr>
            <th class="text-center" style="width:16%;">Quy Chuẩn Công Việc</th>
            <th class="text-center" style="width:16%;">Quy Chuẩn Duyệt</th>
            <th class="text-center" style="width:16%;">Quy Chuẩn Kiểm Soát Hoàn Thành</th>
            <th class="text-center" style="width:8%;">Điểm trừ</th>
            <th class="text-center" style="width:8%;">Điểm cộng</th>
            <th class="text-center" style="width:12%;">Cảnh báo</th>
            <th class="text-center" style="width:8%;">
                <label>Duyệt</label>
            </th>
            <th class="text-center" style="width:15%;">
                <label>Không duyệt</label>
            </th>
            <th class="text-center" style="width:15%;">
                Báo cáo không phù hợp
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
            $check = get_table_where('tbl_tinternal_proposal_inspection_criteria_process', ['id_internal_proposal' => $id, 'process_id' => $process_id, 'id_internal_proposal_process' => $detail_id, 'inspection_criteria' => $value['id']], '', 'row_array');
            if (!empty($check)) {
                if ($check['isCheck'] == 1) {
                    $isCheck = 'checked';
                }
                if ($check['isCheckNot'] == 1) {
                    $isCheckNot = 'checked';
                    $isCheck = '';
                }
            }
            $not_edit = true;
            ?>
            <?php if ($isCheckNot) { ?>
                <?php
                $production_report = get_table_where('tblproduction_report', ['id_internal_proposal' => $id, 'id_internal_proposal_process' => $detail_id, 'id_internal_proposal_process_child' => $value['id']], '', 'row_array');
                ?>
                <?php if (!empty($production_report)) {
                    $not_edit = false;
                } ?>
            <?php } ?>
            <?php if ($not_edit == false) { ?>
                <tr class="<?= ($not_edit == false) ? 'no-drop' : '' ?>">
                    <?php
                    $child_rec = get_table_where('tbl_recommended_list_process_child', [
                        'id' => $value['id_recommended_list_process']
                    ], '', 'row_array');
                   
                    ?>
                    <td>
                        <input type="hidden" <?= $isCheck ?> value="<?= $value['id'] ?>">
                        <?= $value['name'] ?>
                    </td>
                    <td>
                        <input type="hidden" <?= $isCheck ?> value="<?= $value['id'] ?>">
                        <?= $value['approval_standards'] ?>
                    </td>
                    <td>
                        <input type="hidden" <?= $isCheck ?> value="<?= $value['id'] ?>">
                        <?= $value['completion_control_standards'] ?>
                    </td>
                    <td class="text-center">
                        <?= !empty($child_rec) ? $child_rec['minus_point'] : 0 ?>
                    </td>
                    <td class="text-center">
                        <?= !empty($child_rec) ? $child_rec['plus_point'] : 0 ?>
                    </td>
                    <td class="text-center">
                        <?= !empty($child_rec) ? $child_rec['warning'] : '' ?>
                    </td>
                    <td class="text-center ">
                        <div class="checkbox checkbox-primary none-event">
                            <input type="checkbox" <?= $isCheck ?>
                                class="tb-handling-products-stages-child isCheck_<?= $value['id'] ?> isCheck_<?= $value['id'] ?>_yes"
                                onclick="checkResult(<?= $value['id'] ?>, this,1)">
                            <label for="isCheck"></label>
                        </div>
                    </td>
                    <td class="text-center ">
                        <div class="checkbox checkbox-danger none-event">
                            <input type="checkbox" <?= $isCheckNot ?>
                                class="tb-handling-products-stages-child_no isCheck_<?= $value['id'] ?> isCheck_<?= $value['id'] ?>_no"
                                onclick="checkResult(<?= $value['id'] ?>, this,2)">
                            <label for="isCheckNot"></label>
                        </div>
                    </td>
                    <td>
                        <?php if ($isCheckNot) { ?>
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
                            <?php } else {
                                if (!empty($value['staff_delete'])) { ?>
                                    <span class="label label-danger">Đã bị xóa bởi</span>
                                    <?php
                                    $staff_profile_image = staff_profile_image(
                                        $value['staff_delete'],
                                        array('staff-profile-image-small mright5'),
                                        'small',
                                        array('data-toggle' => 'tooltip', 'data-title' => ' Vào lúc: ' . _dt($value['date_delete']))
                                    );
                                    ?>
                                <?php }
                            } ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } else { ?>
                <tr>
                    <?php
                    $child_rec = get_table_where('tbl_recommended_list_process_child', [
                        'id' => $value['id_recommended_list_process']
                    ], '', 'row_array');
       
                    ?>
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
                        <?= !empty($child_rec) ? $child_rec['minus_point'] : 0 ?>
                    </td>
                    <td class="text-center">
                        <?= !empty($child_rec) ? $child_rec['plus_point'] : 0 ?>
                    </td>
                    <td class="text-center">
                        <?= !empty($child_rec) ? $child_rec['warning'] : '' ?>
                    </td>
                    <td class="text-center ">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" <?= $isCheck ?>
                                class="tb-handling-products-stages-child isCheck_<?= $value['id'] ?> isCheck_<?= $value['id'] ?>_yes"
                                name="isCheck[<?= $value['id'] ?>]" onclick="checkResult(<?= $value['id'] ?>, this,1)">
                            <label for="isCheck"></label>
                        </div>
                    </td>
                    <td class="text-center ">
                        <div class="checkbox checkbox-danger">
                            <input type="checkbox" <?= $isCheckNot ?>
                                class="tb-handling-products-stages-child_no isCheck_<?= $value['id'] ?> isCheck_<?= $value['id'] ?>_no"
                                name="isCheckNot[<?= $value['id'] ?>]" onclick="checkResult(<?= $value['id'] ?>, this,2)">
                            <label for="isCheckNot"></label>
                        </div>
                    </td>
                    <td>
                        <?php if ($isCheckNot) { ?>
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
                            <?php } else {
                                if (!empty($value['staff_delete'])) { ?>
                                    <span class="label label-danger">Đã bị xóa bởi</span>
                                    <?=
                                        staff_profile_image(
                                            $value['staff_delete'],
                                            array('staff-profile-image-small mright5 staff-profile-image-small-check'),
                                            'small',
                                            array('data-toggle' => 'tooltip', 'data-title' => ' Vào lúc: ' . _dt($value['date_delete']))
                                        ) . ' ' . get_staff_full_name($value['staff_delete']);
                                    ?>
                                <?php }
                            } ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
<script>
    $('.radio_check_hand_over').change(function () {
        var name = $(this).attr('name');
        if ($(this).prop('checked') == true) {
            $(`input[name="${name}"]`).prop('checked', false);
            $(this).prop('checked', true);
        }
    })
    $('.check-data-err').click(function () {
        $(this).find('p.text-danger').remove();
    })

    function validate() {
        var remember = document.getElementById('tb-handling-products-stages-check');
        var child = $('.tb-handling-products-stages-child');
        if (remember.checked) {
            $.each(child, function (key, value) {
                $(value).prop('checked', true).click();
            });
        } else {
            $.each(child, function (key, value) {
                $(value).prop('checked', false);
            });
        }
    }

    function validate_no() {
        var remember = document.getElementById('tb-handling-products-stages-check_no');
        var child = $('.tb-handling-products-stages-child_no');
        if (remember.checked) {
            $.each(child, function (key, value) {
                $(value).prop('checked', true).click();
            });
        } else {
            $.each(child, function (key, value) {
                $(value).prop('checked', false);
            });
        }
    }
    function ktCheckFalse() {
        var hand_orver_false = $('.radio_check_hand_over[value="2"]:checked');
        if (hand_orver_false.length > 0) {
            $('.save_create_production_report').prop('checked', true);
            if ($('input[name="id_delivery_records"]').val() != "") {
                $('.add-finished-stages').text('Cập nhật phiếu bàn giao và tạo báo cáo');
            } else {
                $('.add-finished-stages').text('Tạo phiếu phiếu bàn giao và tạo báo cáo');
            }
        } else {
            $('.add-finished-stages').text('Lưu lại');
            $('.save_create_production_report').prop('checked', false);
        }
        // if(hand_orver_false.length > 0) {
        //     $('.add-finished-stages').addClass('hide');
        //     $('.create_production_report').removeClass('hide');
        // }
        // else {
        //     $('.add-finished-stages').removeClass('hide');
        //     $('.create_production_report').addClass('hide');
        // }
    }
</script>