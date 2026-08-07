<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table id="tb-handling-products-stages" class="table dataTable">
    <thead>
    <tr>
        <th colspan="5" class="text-center">Phiếu bàn giao: <?=!empty($delivery_records) ? $delivery_records->reference_no : 'Chưa tạo'?></th>
    </tr>
    <tr>
        <th class="text-center" style="width:23%;">Nội dung bàn giao</th>
        <th class="text-center" style="width:23%;">Tiêu chuẩn</th>
        <th class="text-center" style="width:23%;">Phương thức</th>
        <th class="text-center" style="width:15%;">Đạt</th>
        <th class="text-center" style="width:15%;">Không đạt</th>
    </tr>
    </thead>
    <tbody>
	<?php if(!empty($category_hand_over->task)) {?>
        <input name="id_delivery_records" type="hidden" value="<?=!empty($delivery_records) ? $delivery_records->id : ''?>">
        <input name="category_hand" type="hidden" value="<?=!empty($category_hand_over) ? $category_hand_over->id : ''?>">
		<?php foreach($category_hand_over->task as $key => $value) {?>
			<?php
			$ktDelivery = false;
			if(!empty($delivery_records)) {
				$this->db->where('delivery_records_id', $delivery_records->id);
				$this->db->where('hand_over_task_id', $value['id']);
				$ktDelivery = $this->db->get('tbl_delivery_records_task')->row();
			}
			?>
            <tr>
                <td>
                    <input type="hidden" name="hand_over_task_id[<?=$key?>]" value="<?=$value['id']?>">
					<?=$value['name']?>
                </td>
                <td><?=$value['standard']?></td>
                <td><?=$value['method']?></td>
				<?php if(empty($ktDelivery)) {?>
                    <td class="text-center">
                        <div class="checkbox checkbox-info check-data-err">
                            <input type="radio" class="radio_check_hand_over" name="task_hand_over_qualified[<?=$key?>]" id="task_hand_over_qualified_<?=$key?>" value="1" checked>
                            <label for="task_hand_over_qualified_<?=$key?>"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="checkbox checkbox-info check-data-err">
                            <input type="radio" class="radio_check_hand_over" name="task_hand_over_qualified[<?=$key?>]" id="task_hand_over_un_qualified_<?=$key?>" value="2">
                            <label for="task_hand_over_un_qualified_<?=$key?>"></label>
                        </div>
                    </td>
				<?php } else {?>
                    <td class="text-center">
                        <div class="checkbox checkbox-info check-data-err">
                            <input type="radio" class="radio_check_hand_over" name="task_hand_over_qualified[<?=$key?>]" id="task_hand_over_qualified_<?=$key?>" value="1" <?=$ktDelivery->task_hand_over_qualified == 1 ? 'checked' : ''?>>
                            <label for="task_hand_over_qualified_<?=$key?>"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="checkbox checkbox-info check-data-err">
                            <input type="radio" class="radio_check_hand_over" name="task_hand_over_qualified[<?=$key?>]" id="task_hand_over_un_qualified_<?=$key?>" value="2"  <?=$ktDelivery->task_hand_over_qualified == 2 ? 'checked' : ''?>>
                            <label for="task_hand_over_un_qualified_<?=$key?>"></label>
                        </div>
                    </td>
                    <td class="text-center hide">
                        <?=$ktDelivery->task_hand_over_qualified == 1 ? 'X' : ''?>
                    </td>
                    <td class="text-center hide">
                        <?=$ktDelivery->task_hand_over_qualified == 2 ? 'X' : ''?>
                    </td>
				<?php } ?>
            </tr>
		<?php } ?>
	<?php } else { ?>
        <tr><td colspan="5" class="text-danger">Không tìm thấy tiêu chí bàn giao</td></tr>
	<?php } ?>
    </tbody>
</table>
<script>
    ktCheckFalse();
    $('.radio_check_hand_over').change(function() {
        var name = $(this).attr('name');
        if( $(this).prop('checked') == true) {
            $(`input[name="${name}"]`).prop('checked', false);
            $(this).prop('checked', true);
        }
        ktCheckFalse();
    })
    $('.check-data-err').click(function() {
        $(this).find('p.text-danger').remove();
    })

    function ktCheckFalse() {
        var hand_orver_false = $('.radio_check_hand_over[value="2"]:checked');
        if(hand_orver_false.length > 0) {
            $('.save_create_production_report').prop('checked', true);
            if($('input[name="id_delivery_records"]').val() != "") {
                $('.add-finished-stages').text('Cập nhật phiếu bàn giao và tạo báo cáo');
            }
            else {
                $('.add-finished-stages').text('Tạo phiếu phiếu bàn giao và tạo báo cáo');
            }
        }
        else {
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