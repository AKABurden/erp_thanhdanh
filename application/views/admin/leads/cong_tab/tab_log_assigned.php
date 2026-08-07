<?php if(!empty($log_assigned)){
    foreach($log_assigned as $key => $value){?>
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
                <?=$value['note']?>
            </div>
        </div>
    <?php }?>
<?php }?>