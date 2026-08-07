<!--Gửi thông báo cho nhân viên-->
<div id="infomation_<?=$unit?>" class="tab-pane border-padding fade <?=!empty($active) ? 'in active' : ''?>">
    <h3><?=_l('cong_send_infomation_client')?></h3>
    <?php
        $value = !empty($content) ? $content : '';
        echo render_input('detail['.$unit.'][content]','cong_message', $value);
    ?>
    <div class="form-group">
        <label for="detail[<?=$unit?>][send]" class="control-label"><?=_l('cong_people_send_infomation')?></label>
        <select id="detail[<?=$unit?>][send]" name="detail[<?=$unit?>][send]" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
            <option value=""></option>
            <?php foreach($staff as $key => $value){
                $selected = "";
                if(!empty($send) && $send == $value['staffid'])
                {
                    $selected = "selected";
                }
                ?>
                <option value="<?= $value['staffid'] ?>" <?=$selected?>><?=$value['fullname']?> </option>
            <?php } ?>
        </select>
    </div>
    <?php
        $selected = !empty($receive) ? explode(',', $receive) : array();
        echo render_select('detail['.$unit.'][receive][]', $staff, array('staffid', 'fullname'),'cong_people_infomation', $selected, array('multiple' => 'true'))
    ?>
    <input type="hidden" name="detail[<?=$unit?>][type]" value="1">
    <input type="hidden" name="detail[<?=$unit?>][id]" value="<?=!empty($id) ? $id :''; ?>">
    <input type="hidden" name="detail[<?=$unit?>][type_send]" value="2">
</div>