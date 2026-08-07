<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .staff-profile-image-small-check {
        width: 15px;
        height: 15px;
    }

    /* ============================================================ */
    /* MOBILE: Bảng Kiểm Tra Quy Trình → Dạng Card Dọc             */
    /* ============================================================ */
    @media (max-width: 768px) {

        /* Wrapper bao ngoài bảng: bỏ scroll ngang */
        .table-responsive {
            overflow-x: hidden !important;
            border: none !important;
        }

        /* Ẩn dải tiêu đề ngang */
        #tb-handling-products-stages thead {
            display: none !important;
        }

        /* Bảng trở thành block */
        #tb-handling-products-stages,
        #tb-handling-products-stages tbody {
            display: block !important;
            width: 100% !important;
        }

        /* Mỗi hàng = 1 Card */
        #tb-handling-products-stages tbody tr {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            margin-bottom: 16px !important;
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 12px !important;
            padding: 14px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
            box-sizing: border-box !important;
        }

        /* Hàng trạng thái "không duyệt" (no-drop) — nền khác biệt */
        #tb-handling-products-stages tbody tr.no-drop {
            background: #fff5f5 !important;
            border-color: #fecaca !important;
        }

        /* Mỗi ô = 1 dòng thông tin trong Card */
        #tb-handling-products-stages tbody tr td {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            border: none !important;
            border-bottom: 1px dashed #f3f4f6 !important;
            padding: 8px 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
            white-space: normal !important;
            word-break: break-word !important;
            font-size: 14px !important;
        }
        #tb-handling-products-stages tbody tr td:last-child {
            border-bottom: none !important;
        }

        /* Nhãn ảo thay tiêu đề cột (::before) */
        #tb-handling-products-stages tbody tr td::before {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #6b7280 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 4px !important;
            display: block !important;
        }

        /* Gán tên nhãn cho từng cột */
        #tb-handling-products-stages tbody tr td:nth-child(1)::before { content: "Vị trí được phân"; }
        #tb-handling-products-stages tbody tr td:nth-child(2)::before { content: "Quy chuẩn công việc"; }
        #tb-handling-products-stages tbody tr td:nth-child(3)::before { content: "Quy chuẩn duyệt"; }
        #tb-handling-products-stages tbody tr td:nth-child(4)::before { content: "Quy chuẩn kiểm soát"; }
        #tb-handling-products-stages tbody tr td:nth-child(5)::before { content: "KPI xét duyệt"; }
        #tb-handling-products-stages tbody tr td:nth-child(6)::before { content: "✅ Duyệt"; }
        #tb-handling-products-stages tbody tr td:nth-child(7)::before { content: "❌ Không duyệt"; }
        #tb-handling-products-stages tbody tr td:nth-child(8)::before { content: "Báo cáo không phù hợp"; }
        #tb-handling-products-stages tbody tr td:nth-child(9)::before { content: "Trạng thái báo cáo"; }

        /* Cột Duyệt + Không duyệt: checkbox nằm ngang, to hơn dễ bấm */
        #tb-handling-products-stages tbody tr td:nth-child(6),
        #tb-handling-products-stages tbody tr td:nth-child(7) {
            flex-direction: row !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 10px 0 !important;
        }
        #tb-handling-products-stages tbody tr td:nth-child(6)::before,
        #tb-handling-products-stages tbody tr td:nth-child(7)::before {
            margin-bottom: 0 !important;
            min-width: 120px !important;
        }

        /* Checkbox to hơn, dễ bấm ngón tay */
        #tb-handling-products-stages .checkbox {
            margin: 0 !important;
            padding: 0 !important;
        }
        #tb-handling-products-stages .checkbox input[type="checkbox"] {
            width: 24px !important;
            height: 24px !important;
            cursor: pointer !important;
            accent-color: #2563eb; /* màu check native */
        }
        #tb-handling-products-stages .checkbox label {
            padding-left: 8px !important;
            font-size: 14px !important;
        }

        /* Nút Tạo phiếu báo cáo full-width */
        #tb-handling-products-stages .btn {
            width: 100% !important;
            padding: 10px !important;
            font-size: 14px !important;
            border-radius: 8px !important;
            margin-top: 4px !important;
        }

        /* Badge label trạng thái */
        #tb-handling-products-stages .label {
            display: inline-block !important;
            padding: 5px 10px !important;
            font-size: 13px !important;
            border-radius: 6px !important;
        }

        /* Hàng cột vai trò (td rowspan) — nổi bật */
        #tb-handling-products-stages tbody tr td:nth-child(1) {
            background: #f0f9ff !important;
            border-radius: 6px !important;
            padding: 8px !important;
            font-weight: 600 !important;
            color: #0369a1 !important;
            font-size: 13px !important;
        }
        #tb-handling-products-stages tbody tr td.text-danger:nth-child(1) {
            background: #fff1f2 !important;
            color: #dc2626 !important;
        }
    }
</style>
<table id="tb-handling-products-stages" class="table dataTable tb-handling-products-stages">
    <thead>
        <tr>
            <th class="text-center" style="width:20%;">Vị trí được phân</th>
            <th class="text-center" style="width:20%;">Quy Chuẩn Công Việc</th>
            <th class="text-center" style="width:20%;">Quy Chuẩn Duyệt</th>
            <th class="text-center" style="width:20%;">Quy Chuẩn Kiểm Soát Hoàn Thành</th>
            <th class="text-center" style="width:20%;"> KPI xét duyệt</th>
            <th class="text-center" style="width:15%;">
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
        <?php $roleCode = ''; ?>
        <?php foreach ($category_hand_over as $key => $value) { ?>
            <?php
            // $isCheck = 'checked';
            $isCheck = ''; // Mặc định không checked
            $isCheck = !empty($value['not_role']) ? '' : $isCheck;
            $isCheckNot = '';
            $check = get_table_where('tbl_tasks_inspection_criteria_process', ['tasks' => $id, 'process_id' => $process_id, 'id_tasks_process' => $detail_id, 'inspection_criteria' => $value['id']], '', 'row_array');
            if (!empty($check)) {
                if ($check['isCheck'] == 1) {
                    $isCheck = 'checked';
                }
                if ($check['isCheckNot'] == 1) {
                    $isCheckNot = 'checked';
                    $isCheck = '';
                }
            }


            ?>
            <?php if ($isCheckNot) { ?>
                <?php
                $production_report = get_table_where('tblproduction_report', ['id_tasks' => $id, 'id_tasks_process' => $detail_id, 'id_tasks_process_child' => $value['id']], '', 'row_array');
                ?>
                <?php if (!empty($production_report)) {
                    $not_edit = false;
                } ?>
            <?php } ?>
            <?php
            $not_edit = true;
            if (!empty($value['not_role'])) {
            }
            ?>
            <?php if ($not_edit == false) { ?>
                <tr class="<?= ($not_edit == false) ? 'no-drop' : '' ?>" data-code_role="<?= $value['code_role'] ?? '' ?> data-name_role=" <?= $value['name_role'] ?? '' ?>">
                    <td></td>
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
                    <td>
                        <input type="hidden" <?= $isCheck ?> value="<?= $value['id'] ?>">
                        <?= $value['name_category_tasks_process'] ?>
                    </td>
                    <td class="text-center ">
                        <div class="checkbox checkbox-primary none-event">
                            <input type="checkbox" <?= $isCheck ?>
                                class="tb-handling-products-stages-child isCheck_<?= $value['id'] ?> isCheck_<?= $value['id'] ?>_yes"
                                onclick="checkResult(<?= $value['id'] ?>, this,1)">
                            <?= !empty($value['not_role']) ? 'disabled' : '' ?>
                            <label for="isCheck"></label>
                        </div>
                    </td>
                    <td class="text-center ">
                        <div class="checkbox checkbox-danger none-event">
                            <input type="checkbox" <?= $isCheckNot ?>
                                class="tb-handling-products-stages-child_no isCheck_<?= $value['id'] ?> isCheck_<?= $value['id'] ?>_no"
                                onclick="checkResult(<?= $value['id'] ?>, this,2)">
                            <?= !empty($value['not_role']) ? 'disabled' : '' ?>
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
                                    href="<?= base_url('admin/production_report/detail') . '?id_tasks=' . $id . '&id_tasks_process=' . $detail_id . '&id_tasks_process_child=' . $value['id'] ?>"
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

            <?php } else { ?>
                <tr data-code_role="<?= $value['code_role'] ?? '' ?>" data-name_role="<?= $value['name_role'] ?? '' ?>">
                    <td></td>
                    <td>
                        <input type="hidden" <?= $isCheck ?> name="inspection_criteria_id[<?= $key ?>]"
                            value="<?= $value['id'] ?>">
                        <input type="hidden" name="role_processing[<?= $value['id'] ?>]"
                            value="<?= $value['role_processing'] ?? '' ?>">
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
                    <td>
                        <input type="hidden" <?= $isCheck ?> name="inspection_criteria_id[<?= $key ?>]"
                            value="<?= $value['id'] ?>">
                        <?= $value['name_category_tasks_process'] ?>
                    </td>
                    <td class="text-center ">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" <?= $isCheck ?>
                                class="tb-handling-products-stages-child isCheck_<?= $value['id'] ?> isCheck_<?= $value['id'] ?>_yes"
                                name="isCheck[<?= $value['id'] ?>]" onclick="checkResult(<?= $value['id'] ?>, this,1)"
                                <?= !empty($value['not_role']) ? 'disabled' : '' ?>>
                            <label for="isCheck"></label>
                        </div>
                    </td>
                    <td class="text-center ">
                        <div class="checkbox checkbox-danger">
                            <input type="checkbox" <?= $isCheckNot ?>
                                class="tb-handling-products-stages-child_no isCheck_<?= $value['id'] ?> isCheck_<?= $value['id'] ?>_no"
                                name="isCheckNot[<?= $value['id'] ?>]" onclick="checkResult(<?= $value['id'] ?>, this, 2)"
                                <?= !empty($value['not_role']) ? 'disabled' : '' ?>>
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
                                    href="<?= base_url('admin/production_report/detail') . '?id_tasks=' . $id . '&id_tasks_process=' . $detail_id . '&id_tasks_process_child=' . $value['id'] ?>"
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
    $('.radio_check_hand_over').change(function() {
        var name = $(this).attr('name');
        if ($(this).prop('checked') == true) {
            $(`input[name="${name}"]`).prop('checked', false);
            $(this).prop('checked', true);
        }
    })
    $('.check-data-err').click(function() {
        $(this).find('p.text-danger').remove();
    })

    function validate() {
        var remember = document.getElementById('tb-handling-products-stages-check');
        var child = $('.tb-handling-products-stages-child');
        if (remember.checked) {
            $.each(child, function(key, value) {
                $(value).prop('checked', true);
            });
        } else {
            $.each(child, function(key, value) {
                $(value).prop('checked', false);
            });
        }
    }


    function groupRole() {
        let table = $('#tb-handling-products-stages tbody');
        let rows = table.find('tr');

        let currentCode = null;
        let firstRow = null;
        let rowspan = 0;

        rows.each(function() {

            let code = $(this).data('code_role');
            let name_role = $(this).data('name_role');
            let firstCell = $(this).find('td:first');

            let isEmpty = !code; // null, undefined, ""

            let displayText = isEmpty ?
                "Chưa phân vai trò phân công" :
                (code + '<br/><i style="font-style: italic;">(' + name_role + ')</i>');

            if (currentCode === code) {

                rowspan++;
                firstCell.remove();

            } else {

                // Set rowspan cho group trước
                if (firstRow) {
                    firstRow.find('td:first').attr('rowspan', rowspan);
                }

                currentCode = code;
                firstRow = $(this);
                rowspan = 1;

                firstCell.html(displayText);

                // Nếu rỗng thì thêm class đỏ
                if (isEmpty) {
                    firstCell.addClass('text-danger');
                }
            }

        });

        // Set rowspan cho group cuối
        if (firstRow) {
            firstRow.find('td:first').attr('rowspan', rowspan);
        }

    }
    groupRole();

    // $(document).ready(function () {
    //     let table = $('#tb-handling-products-stages tbody');
    //     let rows = table.find('tr');
    //
    //     let groups = {};
    //
    //     // Gom nhóm theo code_role
    //     rows.each(function () {
    //         let code = $(this).data('code_role');
    //
    //         if (!groups[code]) {
    //             groups[code] = [];
    //         }
    //
    //         groups[code].push($(this));
    //     });
    //
    //     // Xử lý từng nhóm
    //     $.each(groups, function (code, groupRows) {
    //
    //         if (groupRows.length > 1) {
    //
    //             let firstRow = groupRows[0];
    //             let rowspan = groupRows.length;
    //
    //             // ví dụ: gộp cột đầu tiên (td đầu)
    //             let firstCell = firstRow.find('td:first');
    //
    //             firstCell.attr('rowspan', rowspan);
    //
    //             // Ẩn cell đầu tiên của các dòng sau
    //             for (let i = 1; i < groupRows.length; i++) {
    //                 groupRows[i].find('td:first').remove();
    //             }
    //         }
    //
    //     });
    // });

    // function ktCheckFalse() {
    //     var hand_orver_false = $('.radio_check_hand_over[value="2"]:checked');
    //     if (hand_orver_false.length > 0) {
    //         $('.save_create_production_report').prop('checked', true);
    //         if ($('input[name="id_delivery_records"]').val() != "") {
    //             $('.add-finished-stages').text('Cập nhật phiếu bàn giao và tạo báo cáo');
    //         } else {
    //             $('.add-finished-stages').text('Tạo phiếu phiếu bàn giao và tạo báo cáo');
    //         }
    //     } else {
    //         $('.add-finished-stages').text('Lưu lại');
    //         $('.save_create_production_report').prop('checked', false);
    //     }
    //     // if(hand_orver_false.length > 0) {
    //     //     $('.add-finished-stages').addClass('hide');
    //     //     $('.create_production_report').removeClass('hide');
    //     // }
    //     // else {
    //     //     $('.add-finished-stages').removeClass('hide');
    //     //     $('.create_production_report').addClass('hide');
    //     // }
    // }
</script>