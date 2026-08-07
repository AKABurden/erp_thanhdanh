<!-- Gửi thông báo cho khách hàng qua EMAIL-->
<div id="infomation_<?=$unit?>" class="tab-pane border-padding fade <?=!empty($active) ? 'in active' : ''?>">
    <div class="col-md-12 hide">
        <h4><?= _l('cong_send_email_staff_client')?></h4>
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
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <div class="clearfix"></div>
        <?php
            $value = !empty($title) ? $title : '';
            echo render_input('detail['.$unit.'][title]','cong_title_send_email', $value);
        ?>
        <?php
            $value = !empty($content) ? $content : '';
            echo render_textarea('detail['.$unit.'][content]','cong_note_tasks', $value, array(), array(),'','tinymce');
        ?>
    </div>
    <input type="hidden" name="detail[<?=$unit?>][type]" value="2">
    <input type="hidden" name="detail[<?=$unit?>][id]" value="<?=!empty($id) ? $id :''; ?>">
    <div class="clearfix"></div>
</div>
