<style>
    ._second {
        background-color: aliceblue;
    }
    ._first{
        background-color: #adc9e2;
    }
    .nav-item.active{
        background-color: #c0dbe6;
        border-bottom: 1px #1d9acd solid;
    }
</style>
<ul class="nav nav-tabs nav-justified">
    <li class="nav-item active">
        <a class="nav-link" data-toggle="tab" href="#tabs_manage_care_of_client" aria-expanded="false"><?=_l('cong_list_care_of')?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tabs_log_care_of_client" aria-expanded="true"><?=_l('cong_history_care_of_client')?></a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active in" id="tabs_manage_care_of_client">
        <div class="">
            <?php render_datatable(array(
                _l('cong_fullcode_care_of'),
                _l('cong_date_start'),
                _l('cong_priority'),
                _l('cong_rating'),
                _l('cong_orders'),
                _l('cong_theme_of'),
                _l('cong_event_care_of'),
                _l('cong_solution'),
                _l('cong_staff_success'),
                _l('cong_date_create'),
                _l('cong_create_by'),
                _l('cong_date_client_contact'),
                '<p class="mw600 text-center">'._l('cong_step_care_of').'</p>',
            ),'care_of_clients'); ?>
        </div>
    </div>
    <div class="tab-pane container" id="tabs_log_care_of_client">
        <div class="activity-feed">
            <?php
                if(!empty($log_care_of_client)){
                foreach($log_care_of_client as $key => $value){?>
                    <div class="feed-item <?=($key%2 == 0 ? '_second' : '_first')?>">
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
            <?php }
                else
                {
                    echo '<p class="text-center text-danger">'._l("cong_not_found_log").'</p>';
                }
            ?>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_care_of_clients" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>