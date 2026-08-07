<!-- Gửi thông báo cho khách hàng qua EMAIL-->
<div id="infomation_<?=$unit?>" class="tab-pane border-padding fade <?=!empty($active) ? 'in active' : ''?>">
    <h3><?= _l('cong_send_email_staff_client')?></h3>
    <div class="col-md-12">
        <div class="form-group">
            <label for="detail[<?=$unit?>][send]" class="control-label"><?=_l('cong_people_send_infomation')?></label>
            <select id="detail[<?=$unit?>][send]" name="detail[<?=$unit?>][send]" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                <option value=""></option>
                <?php foreach($staff_send as $key => $value){
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
        <div class="col-md-3">
            <div class="radio radio-primary">
                <?php $checked = !empty($type_send) && $type_send == 1 ? 'checked' : '';?>
                <input type="radio" id="type_send_<?=$unit?>" class="is_primary type_send" name="detail[<?=$unit?>][type_send]" <?=$checked?> value="1">
                <label for="type_send_<?=$unit?>" data-toggle="tooltip" data-original-title="" title=""><?= _l('cong_client')?></label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="radio radio-primary">
                <?php $checked = !empty($type_send) && $type_send == 2 ? 'checked' : '';?>
                <input type="radio" id="type_send_<?=$unit?>" class="is_primary type_send" name="detail[<?=$unit?>][type_send]" <?=$checked?> value="2">
                <label for="type_send_<?=$unit?>" data-toggle="tooltip" data-original-title="" title=""><?= _l('cong_staff')?></label>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">

    </div>
    <div class="col-md-12">

        <?php

        $selected = !empty($receive) ? explode(',', $receive) : [];
        if(!empty($client))
        {
            echo render_select('detail['.$unit.'][receive][]', $client, array('userid', 'company'), '', $selected, array('multiple' => true, 'TClass' => 'receiveEmail') );
        }
        else if(!empty($staff))
        {
            echo render_select('detail['.$unit.'][receive][]', $staff, array('staffid', 'fullname'), '', $selected, array('multiple' => true, 'TClass' => 'receiveEmail') );
        }
        ?>

        <?php
            $value = !empty($content) ? $content : '';
            echo render_textarea('detail['.$unit.'][content]','cong_content_send_mail',$value, array(), array(),'','tinymce');
        ?>
    </div>
    <input type="hidden" name="detail[<?=$unit?>][type]" value="2">
    <input type="hidden" name="detail[<?=$unit?>][id]" value="<?=!empty($id) ? $id :''; ?>">
    <div class="clearfix"></div>
</div>
