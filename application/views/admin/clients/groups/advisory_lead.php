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
    .text-has-action{
        padding-top:10px;
    }
</style>
<ul class="nav nav-tabs nav-justified">
    <li class="nav-item active">
        <a class="nav-link" data-toggle="tab" href="#tabs_manage_advosory_lead" aria-expanded="false"><?=_l('cong_list_advisory')?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tabs_log_advosory_lead" aria-expanded="true"><?=_l('cong_history_care_of_lead')?></a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active in" id="tabs_manage_advosory_lead">
        <div class="">
            <?php render_datatable(array(
                _l('cong_fullcode_advisory'),
                _l('cong_date_start'),
                _l('cong_product_other_buy'),
                _l('cong_address_other_buy'),
                _l('cong_date_create'),
                _l('cong_create_by'),
                _l('cong_step_advisory'),
            ),'advisory_lead'); ?>
        </div>
    </div>
    <div class="tab-pane container" id="tabs_log_advosory_lead">
        <div class="activity-feed">
            <?php if(!empty($log_advisory_lead)){
                foreach($log_advisory_lead as $key => $value){?>
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
<div class="modal fade" id="modal_advisory_lead" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>