<div class="activity-feed">
    <?php if(!empty($log_advisory)){
        foreach($log_advisory as $key => $value){?>
            <div class="feed-item">
                <div class="date">
                    <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($value['date_create']); ?>">
                        <?php echo time_ago($value['date_create']); ?>
                    </span>
                </div>
                <div class="text">
                    <a href="<?php echo admin_url('profile/' . $value["staff"]); ?>">
                        <?php echo staff_profile_image($value['staff'], array('staff-profile-xs-image pull-left mright5'));
                        ?>
                    </a>
                    <?php

                    $note_add = "";
                    if($value['type'] ==0 ){
                        $note_add = _l('cong_restore_advisory').' '.$value['note'];
                    }
                    else if($value['type'] == 1)
                    {
                        $note_add = _l('cong_finish_advisory').' '.$value['note'];
                    }
                    else if($value['type'] == 2)
                    {
                        $note_add = _l('cong_break_advisory').' '.$value['note'];
                    }
                    else if($value['type'] == 3)
                    {
                        $note_add = _l('cong_break_advisory').' '.$value['note'];
                    }
                    ?>
                    <?=$note_add?>
                </div>
            </div>
        <?php }?>
    <?php }?>
</div>
