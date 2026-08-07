<?php
$counter = 0;
$trOne = '';
$trTwo = '';
$trThree = '';
$trFour = '';
if (!empty($work_plan)) {
    $items = $this->work_plan_model->getWorkPlanItems($id);
    foreach ($items as $key => $value) {
        $type = $value['type'];

        $tdNumber = '<td class="text-center td-numbers"></td>';
        $tdName = '<td>
            <input type="hidden" name="items[' . $counter . '][type]" class="form-control type" value="' . $type . '">
            <input type="hidden" name="items[' . $counter . '][number]" class="form-control number" value="">
            <input type="hidden" name="items[' . $counter . '][work_plan_items_id]" class="form-control work_plan_items_id" value="' . $value['id'] . '">
            <input type="text" name="items[' . $counter . '][name]" class="form-control name" placeholder="' . lang('Tên') . '" value="' . $value['name'] . '">
        </td>';
        $tdWeekOne = '<td>
            <input type="text" name="items[' . $counter . '][week_one]" class="form-control week_one" placeholder="' . lang('Tuần 1') . '" value="' . $value['week_one'] . '">
        </td>';
        $tdWeekTwo = '<td>
            <input type="text" name="items[' . $counter . '][week_two]" class="form-control week_two" placeholder="' . lang('Tuần 2') . '" value="' . $value['week_two'] . '">
        </td>';
        $tdWeekThree = '<td>
            <input type="text" name="items[' . $counter . '][week_three]" class="form-control week_three" placeholder="' . lang('Tuần 3') . '" value="' . $value['week_three'] . '">
        </td>';
        $tdWeekFour = '<td>
            <input type="text" name="items[' . $counter . '][week_four]" class="form-control week_four" placeholder="' . lang('Tuần 4') . '" value="' . $value['week_four'] . '">
        </td>';
        $tdPriorityLevel = '<td>
            <input type="text" name="items[' . $counter . '][priority_level]" class="form-control priority_level number-format" placeholder="' . lang('Mức độ ưu tiên') . '" value="' . $value['priority_level'] . '">
        </td>';

        $optionsProcess = '<option></option>';
        foreach ($process_work_plan as $kP => $vP) {
            $selected = $kP == $value['process'] ? 'selected' : '';
            $optionsProcess .= '<option data-content="<span style=\'color: '.$vP['color'].';\'>'.$vP['name'].'</span>" ' . $selected . ' value="' . $kP . '">' . $vP['name'] . '</option>';
        }

        $tdProcess = '<td style="max-width: 150px !important;">
            <select name="items[' . $counter . '][process]" class="form-control selectpicker process" data-live-search="true" data-none-selected-text="' . lang('Quy trình') . '">
                ' . $optionsProcess . '
            </select>
        </td>';

        $optionsStaff = '';
        $optionsManageReports = '';
        $dtWorkPlanItemsStaffs = $this->work_plan_model->getWorkPlanItemsStaffs($value['id'], 1);
        $dtWorkPlanItemsStaffsManage = $this->work_plan_model->getWorkPlanItemsStaffs($value['id'], 2);
        if (!empty($staffs)) {
            foreach ($staffs as $kS => $vS) {
                $selectedStaff = '';
                foreach ($dtWorkPlanItemsStaffs as $kWPS => $vWPS) {
                    if ($vS['staffid'] == $vWPS['staff_id']) {
                        $selectedStaff = 'selected';
                        break;
                    }
                }

                $selectedStaffManage = '';
                foreach ($dtWorkPlanItemsStaffsManage as $kWPS => $vWPS) {
                    if ($vS['staffid'] == $vWPS['staff_id']) {
                        $selectedStaffManage = 'selected';
                        break;
                    }
                }

                $optionsStaff .= '<option ' . $selectedStaff . ' value="' . $vS['staffid'] . '">' . $vS['fullname'] . '</option>';
                $optionsManageReports .= '<option ' . $selectedStaffManage . ' value="' . $vS['staffid'] . '">' . $vS['fullname'] . '</option>';
            }
        }

        $tdStaffs = '<td style="max-width: 150px !important;">
            <select name="items[' . $counter . '][staffs][]" class="form-control selectpicker" data-live-search="true" data-none-selected-text="' . lang('Người phụ trách') . '" multiple>
                ' . $optionsStaff . '
            </select>
        </td>';
        $tdManageReports = '<td style="max-width: 150px !important;">
            <select name="items[' . $counter . '][manage_reports][]" class="form-control selectpicker" data-live-search="true" data-none-selected-text="' . lang('Người giám sát - báo cáo') . '" multiple>
                ' . $optionsManageReports . '
            </select>
        </td>';
        $tdActions = '<td class="text-danger text-center">
            <i onclick="removeItemWorkPlan(this)" class="fa fa-remove" style="cursor: pointer;"></i>
        </td>';

        $trItem = '<tr>
            ' . $tdNumber . '
            ' . $tdName . '
            ' . $tdWeekOne . '
            ' . $tdWeekTwo . '
            ' . $tdWeekThree . '
            ' . $tdWeekFour . '
            ' . $tdPriorityLevel . '
            ' . $tdProcess . '
            ' . $tdStaffs . '
            ' . $tdManageReports . '
            ' . $tdActions . '
        </tr>';
        $counter++;

        if ($type == 1) {
            $trOne .= $trItem;
        } else if ($type == 2) {
            $trTwo .= $trItem;
        } else if ($type == 3) {
            $trThree .= $trItem;
        } else if ($type == 4) {
            $trFour .= $trItem;
        }
    }
}
?>
<tr class="tr-group" style="background: #ddddddd1;">
    <td class="text-center">
        <a onclick="addItemWorkPlan(this, 1)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
            </svg>
        </a>
    </td>
    <td class="text-left" colspan="8"><?= lang('KHỐI : VĂN PHÒNG ( Link Công Việc Khối)') ?></td>
    <td class="text-right" colspan="1"><a class="hide" href="<?=base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx')?>"><?= lang('Download Mẫu...') ?></a></td>
    <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="1"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>
</tr>
<?= $trOne ?>
<tr class="tr-group" style="background: #ddddddd1;">
    <td class="text-center">
        <a onclick="addItemWorkPlan(this, 2)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
            </svg>
        </a>
    </td>
    <td class="text-left" colspan="8"><?= lang('HOÀN THÀNH Cập Nhật Full Thông Tin SP') ?></td>
    <td class="text-right" colspan="1"><a class="hide" href="<?=base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx')?>"><?= lang('Download Mẫu...') ?></a></td>
    <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="2"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>

</tr>
<?= $trTwo ?>
<tr class="tr-group" style="background: #ddddddd1;">
    <td class="text-center">
        <a onclick="addItemWorkPlan(this, 3)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
            </svg>
        </a>
    </td>
    <td class="text-left" colspan="8"><?= lang('Qui Trình') ?></td>
    <td class="text-right" colspan="1"><a class="hide" href="<?=base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx')?>"><?= lang('Download Mẫu...') ?></a></td>
    <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="3"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>
</tr>
<?= $trThree ?>
<tr class="tr-group" style="background: #ddddddd1;">
    <td class="text-center">
        <a onclick="addItemWorkPlan(this, 4)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
            </svg>
        </a>
    </td>
    <td class="text-left" colspan="8"><?= lang('Sản Xuất - Chất Lượng') ?></td>
    <td class="text-right" colspan="1"><a class="hide" href="<?=base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx')?>"><?= lang('Download Mẫu...') ?></a></td>
    <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="4"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>
</tr>
<?= $trFour ?>
<script>
    $(document).ready(function () {
        counter = <?= $counter ?>;
        init_selectpicker();
        totalWorkPlan();
        $('#content').val('<?= $work_plan['content'] ?>');
        $('#id').val('<?= $work_plan['id'] ?>');
    });
</script>