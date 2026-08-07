<!-- Tạo công việc tự động cho nhân viên-->
<div id="infomation_<?= $unit ?>" class="tab-pane border-padding fade <?=!empty($active) ? 'in active' : ''?>">
    <div class="col-md-12">
        <h4><?= _l('create_tasks_auto')?></h4>
        <div class="form-group">
            <label for="detail[<?=$unit?>][send]" class="control-label"><?=_l('cong_people_send_task')?></label>
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
        <?php
        $receive = !empty($receive) ? explode(',', $receive): [];
        $receiveOne = "";
        $receiveTwo = "";
        foreach($receive as $key => $value)
        {
            if($value == '-1')
            {
                $receiveOne = "checked";
                unset($receive[$key]);
                continue;
            }
            if($value == '-2')
            {
                $receiveTwo = "checked";
                unset($receive[$key]);
                continue;
            }
        }
        ?>
        <div class="col-md-3">
            <div class="checkbox checkbox-primary">
                <input type="checkbox" id="detail_receive_1_<?= $unit ?>" name="detail[<?= $unit ?>][receive][]" class="is_primary" value="-1" <?= $receiveOne ?> >
                <label for="detail_receive_1_<?= $unit ?>" data-toggle="tooltip" data-original-title="" title=""><?=_l('cong_staff_create')?></label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="checkbox checkbox-primary">
                <input type="checkbox" id="detail_receive_2_<?= $unit ?>" name="detail[<?= $unit ?>][receive][]" class="is_primary" value="-2" <?= $receiveTwo ?>>
                <label for="detail_receive_2_<?= $unit ?>" data-toggle="tooltip" data-original-title="" title=""><?=_l('cong_staff_manage')?></label>
            </div>
        </div>
        <div class="clearfix"></div>
        <?php
            $selected = !empty($receive) ? $receive : [];
            echo render_select('detail['.$unit.'][receive][]', $staff, array('staffid', 'fullname'), 'cong_staff_tasks', $selected, array('multiple' => true, 'TClass' => 'receiveEmail', 'data-actions-box'=>true), [], '', '', false );
        ?>
        <?php
            $selected = !empty($staff_follow) ? $staff_follow : [];
            echo render_select('detail['.$unit.'][staff_follow]', $staff, array('staffid', 'fullname'), 'cong_staff_follow', $selected, array(), [], '', '', false );
        ?>
        <div class="clearfix"></div>
        <div class="checkbox checkbox-primary">
            <?php $checked = ((isset($public) && $public == 1) || !isset($public)) ? 'checked' : '';?>
            <input type="checkbox" id="detail[<?=$unit?>][public]" class="is_primary" name="detail[<?=$unit?>][public]" <?=$checked?> value="1">
            <label for="detail[<?=$unit?>][public]" data-toggle="tooltip" data-original-title="" title=""><?= _l('cong_public')?></label>
        </div>
        <?php
            $value = !empty($title) ? $title : '';
            echo render_input('detail['.$unit.'][title]','cong_name_task',$value);
        ?>
        <?php
            $value = !empty($content) ? $content : '';
            echo render_textarea('detail['.$unit.'][content]','cong_note_tasks',$value, array(), array(),'','tinymce');
        ?>
    </div>
    <input type="hidden" name="detail[<?=$unit?>][type]" value="3">
    <input type="hidden" name="detail[<?=$unit?>][id]" value="<?=!empty($id) ? $id :''; ?>">
    <div class="clearfix"></div>
</div>
