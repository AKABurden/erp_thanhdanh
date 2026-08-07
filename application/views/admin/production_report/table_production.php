<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (!empty($category_hand_over)) { ?>
    <table id="tb-handling-products-stages" class="table dataTable tb-handling-products-stages">
        <thead>
            <tr>
                <th class="text-center" style="width:23%;">Tiêu chí kiểm</th>
                <th class="text-center" style="width:15%;">
                    <div class="checkbox mass_select_all_wrap">
                        <input onclick="validate()" type="checkbox" id="tb-handling-products-stages-check" class="tb-handling-products-stages-check" data-to-table="tb-handling-products-stages">
                        <label>Có</label>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($category_hand_over as $key => $value) { ?>
                <?php
                $isCheck = '';
                $check = get_table_where('tbl_setting_production_report_inspection_criteria_process', ['production_report' => $id, 'process_id' => $process_id, 'id_production_report_process' => $is, 'inspection_criteria' => $value['id']], '', 'row_array');
                if (!empty($check)) {
                    $isCheck = 'checked';
                }
                ?>
                <tr>
                    <td>
                        <input type="hidden" <?= $isCheck ?> name="inspection_criteria_id[<?= $key ?>]" value="<?= $value['id'] ?>">
                        <?= $value['name'] ?>
                    </td>
                    <td class="text-center">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" <?= $isCheck ?> class="tb-handling-products-stages-child" name="isCheck[<?= $value['id'] ?>]">
                            <label for="isCheck"></label>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div class="center">
        <h3 style="color:red;">Không có tiêu chí kiểm, Vui lòng bấm xác nhận</h3>
    </div>
<?php } ?>
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