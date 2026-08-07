<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<div class="row">
    <div class="col-md-12">
        <div class="activity-container">
            <?php foreach ($dataLog as $key => $value) { ?>
                <div class="feed-item">
                    <div class="activity-text">
                        <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?> <?= get_staff_full_name($value['staff_id']); ?>
                    </div>
                    <div class="activity-time">
                        <?= time_ago($value['date']) ?> <span class="activity-module"><?=_l($value['table_obj'])?></span>
                    </div>
                    <div>
                        <?=$value['content']?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>