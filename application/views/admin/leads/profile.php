<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .mbot0{
        margin-bottom: 0px!important;
    }
    .mbot40{
        margin-bottom: 40px;
    }
    .mar-pad-lef15{
        margin-left: -15px;
        padding-left: 15px;
    }
    .pd20{
        padding:20px;
    }
    .border-ds{
        border:1px dashed #d0d0d0!important;
        opacity: .6!important;
        cursor: pointer;
    }
    .add-contacts:hover {
        opacity: 1!important;
    }
    .mborder{
        border-bottom:1px solid #d0d0d0!important;
    }
    .pborder{
        border:1px solid #d0d0d0!important;
    }
    .font-40{
        font-size: 40px;
    }
    a.removeImg{
        display: inherit;
        top: 0;
        float: left;
    }
</style>
<link rel="stylesheet" href="<?=base_url('assets/css/step_by_step.css')?>">
<br>
<div class="<?php if ($openEdit == true) { echo 'open-edit '; } ?>lead-wrapper" <?php if (isset($lead) && ($lead->junk == 1 || $lead->lost == 1)) { echo 'lead-is-junk-or-lost'; } ?>>
    <?php if (isset($lead)) { ?>
        <div class="btn-group pull-left lead-actions-left">
            <a href="#" lead-edit class="wap-edit mright10 font-medium-xs pull-left<?php if ($lead_locked == true) { echo ' hide'; } ?>">
                <?php echo _l('edit'); ?>
                <i class="fa fa-pencil-square-o"></i>
            </a>
            <a href="#" class="font-medium-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="lead-more-btn">
                <?php echo _l('more'); ?>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-left" id="lead-more-dropdown">
                <?php if ($lead->junk == 0) {
                    if ($lead->lost == 0 && (total_rows(db_prefix() . 'clients', array('leadid' => $lead->id)) == 0)) { ?>
                        <li>
                            <a href="#" onclick="lead_mark_as_lost(<?php echo $lead->id; ?>); return false;">
                                <i class="fa fa-mars"></i>
                                <?php echo _l('lead_mark_as_lost'); ?>
                            </a>
                        </li>
                    <?php } else if ($lead->lost == 1) { ?>
                        <li>
                            <a href="#" onclick="lead_unmark_as_lost(<?php echo $lead->id; ?>); return false;">
                                <i class="fa fa-smile-o"></i>
                                <?php echo _l('lead_unmark_as_lost'); ?>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
                <!-- mark as junk -->
                <?php if ($lead->lost == 0) {
                    if ($lead->junk == 0 && (total_rows(db_prefix() . 'clients', array('leadid' => $lead->id)) == 0)) { ?>
                        <li>
                            <a href="#" onclick="lead_mark_as_junk(<?php echo $lead->id; ?>); return false;">
                                <i class="fa fa fa-times"></i>
                                <?php echo _l('lead_mark_as_junk'); ?>
                            </a>
                        </li>
                    <?php } else if ($lead->junk == 1) { ?>
                        <li>
                            <a href="#" onclick="lead_unmark_as_junk(<?php echo $lead->id; ?>); return false;">
                                <i class="fa fa-smile-o"></i>
                                <?php echo _l('lead_unmark_as_junk'); ?>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
                <?php if (((is_lead_creator($lead->id) || has_permission('leads', '', 'delete')) && $lead_locked == false) || is_admin()) { ?>
                    <li>
                        <a href="<?php echo admin_url('leads/delete/' . $lead->id); ?>"
                           class="text-danger delete-text _delete" data-toggle="tooltip" title="">
                            <i class="fa fa-remove"></i>
                            <?php echo _l('lead_edit_delete_tooltip'); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <a data-toggle="tooltip" class="btn btn-default pull-right lead-print-btn lead-top-btn lead-view mleft5" onclick="print_lead_information(); return false;" data-placement="top" title="<?php echo _l('print'); ?>" href="#">
            <i class="fa fa-print"></i>
        </a>
        <?php
        $client = false;
        $convert_to_client_tooltip_email_exists = '';
        if (total_rows(db_prefix() . 'contacts', array('email' => $lead->email)) > 0 && total_rows(db_prefix() . 'clients', array('leadid' => $lead->id)) == 0) {
            $convert_to_client_tooltip_email_exists = _l('lead_email_already_exists');
            $text = _l('lead_convert_to_client');
        } else if (total_rows(db_prefix() . 'clients', array('leadid' => $lead->id))) {
            $client = true;
        } else {
            $text = _l('lead_convert_to_client');
        }
        ?>
        <?php if ($lead_locked == false) { ?>
            <div class="lead-edit<?php if (isset($lead)) { echo ' hide'; } ?>">
                <button type="button" class="btn btn-info pull-right mleft5 lead-top-btn lead-save-btn"
                        onclick="document.getElementById('lead-form-submit').click();">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        <?php } ?>
        <?php if ($client && (has_permission('customers', '', 'view') || is_customer_admin(get_client_id_by_lead_id($lead->id)))) { ?>
            <a data-toggle="tooltip" class="btn btn-success pull-right lead-top-btn lead-view" data-placement="top" title="<?php echo _l('lead_converted_edit_client_profile'); ?>" href="<?php echo admin_url('clients/client/' . get_client_id_by_lead_id($lead->id)); ?>">
                <i class="fa fa-user-o"></i>
            </a>
        <?php } ?>
        <?php if (total_rows(db_prefix() . 'clients', array('leadid' => $lead->id)) == 0 && !empty($lead->zcode)) { ?>
            <a href="#" data-toggle="tooltip" data-title="<?php echo $convert_to_client_tooltip_email_exists; ?>" class="btn btn-success pull-right lead-convert-to-customer lead-top-btn lead-view" onclick="convert_lead_to_customer(<?php echo $lead->id; ?>); return false;">
                <i class="fa fa-user-o"></i>
                <?php echo $text; ?>
            </a>
        <?php } ?>
    <?php } ?>
    <div class="clearfix no-margin"></div>

    <?php if (isset($lead)) { ?>
        <div class="row mbot15">
            <hr class="no-margin"/>
        </div>

        <div class="alert alert-warning hide mtop20" role="alert" id="lead_proposal_warning">
            <?php echo _l('proposal_warning_email_change', array(_l('lead_lowercase'), _l('lead_lowercase'), _l('lead_lowercase'))); ?>
            <hr/>
            <a href="#" onclick="update_all_proposal_emails_linked_to_lead(<?php echo $lead->id; ?>); return false;">
                <?php echo _l('update_proposal_email_yes'); ?>
            </a>
            <br/>
            <a href="#" onclick="init_lead_modal_data(<?php echo $lead->id; ?>); return false;">
                <?php echo _l('update_proposal_email_no'); ?>
            </a>
        </div>
    <?php } ?>
    <?php echo form_open((isset($lead) ? admin_url('leads/lead/' . $lead->id) : admin_url('leads/lead')), array('id' => 'lead_form', 'enctype' => 'multipart/form-data')); ?>
    <div class="row">
        <div class="lead-view<?php if (!isset($lead)) { echo ' hide'; } ?>" id="leadViewWrapper">
            <div class="col-md-6 col-xs-12 lead-information-col mbot10">
                <div class="lead-info-heading padding0">
                    <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                        <?php echo _l('lead_general_info'); ?>
                    </h4>
                </div>

                <!-- <div class="wap-content second">
                    <span class="text-muted lead-field-heading no-mtop"><?php echo _l('cong_code_lead'); ?>: </span>
                    <span class="bold font-medium-xs mbot15"><?php echo((isset($lead) && !empty($lead->perfix_lead) && !empty($lead->code_lead)) ? $lead->perfix_lead.$lead->code_lead : '-') ?></span>
                </div> -->
                <!-- <div class="wap-content firt">
                    <span class="text-muted lead-field-heading no-mtop bold"><?php echo _l('cong_fullname'); ?>: </span>
                    <span class="bold font-medium-xs lead-name"><?php echo(isset($lead) && $lead->name != '' ? $lead->name : '-') ?></span>
                </div> -->

                <div class="wap-content second">
                    <span class="text-muted lead-field-heading no-mtop"><?php echo _l('cong_zcode'); ?>: </span>
                    <span class="bold font-medium-xs mbot15"><?php echo(isset($lead) && !empty($lead->zcode) ? $lead->zcode : '-') ?></span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading"><?php echo _l('cong_customer_orders'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->company != '' ? $lead->company : '-') ?></span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading"><?php echo _l('representative'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->representative != '' ? $lead->representative : '-') ?></span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading"><?php echo _l('cong_date_create_company'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->date_create_company != '' ? _d($lead->date_create_company) : '-') ?></span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading">
                        <?php echo _l('cong_vat'); ?>:
                    </span>
                    <span class="bold font-medium-xs">
                        <?php echo(isset($lead) ? $lead->vat : '-') ?>
                    </span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading"><?php echo _l('lead_add_edit_phonenumber'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->phonenumber != '' ? '<a href="tel:' . $lead->phonenumber . '">' . $lead->phonenumber . '</a>' : '-') ?></span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading"><?php echo _l('cong_email'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->email != '' ? '<a href="mailto:' . $lead->email . '">' . $lead->email . '</a>' : '-') ?></span>
                </div>

                <div class="wap-content second hide">
                    <span class="text-muted lead-field-heading">
                        <?php echo _l('cong_client_area'); ?>:
                    </span>
                    <span class="bold font-medium-xs">
                        <?php echo(!empty($lead->area) ? $lead->area : '-') ?>
                    </span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading"><?php echo _l('lead_add_edit_source'); ?>: </span>
                    <span class="bold font-medium-xs mbot15"><?php echo(isset($lead) && $lead->source_name != '' ? $lead->source_name : '-') ?></span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading"><?php echo _l('lead_address'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->address != '' ? $lead->address : '-') ?></span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading"><?php echo _l('lead_city'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->city != '' ? $lead->name_city : '-') ?></span>
                </div>

                <!--Công bổ sung-->
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading">
                        <?php echo _l('cong_client_district'); ?>:
                    </span>
                    <span class="bold font-medium-xs">
                        <?php echo(isset($lead) && $lead->district != 0 ? get_district($lead->district)->name : '-') ?>
                    </span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading">
                        <?php echo _l('cong_client_ward'); ?>:
                    </span>
                    <span class="bold font-medium-xs">
                        <?php echo(isset($lead) && $lead->ward != 0 ? get_ward($lead->ward)->name : '-') ?>
                    </span>
                </div>
                <div class="wap-content firt hide">
                    <span class="text-muted lead-field-heading"><?php echo _l('lead_state'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->state != '' ? $lead->state : '-') ?></span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading"><?php echo _l('lead_country'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->country != 0 ? get_country($lead->country)->short_name : '-') ?></span>
                </div>
                      <div class="wap-content firt">
                    <span class="text-muted lead-field-heading">
                        <?php echo _l('sex'); ?>:
                    </span>
                    <span class="bold font-medium-xs">
                        <?php echo(isset($lead) ? ($lead->gender == 1 ? _l('cong_male') : ($lead->gender == 2 ? _l('cong_female') : '-')) : '-') ?>
                    </span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading">
                        <?php echo _l('cong_client_facebook'); ?>:
                    </span>
                    <span class="bold font-medium-xs">
                        <?php echo(isset($lead) ? $lead->facebook : '-') ?>
                    </span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading"><?php echo _l('lead_website'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->website != '' ? '<a href="' . maybe_add_http($lead->website) . '" target="_blank">' . $lead->website . '</a>' : '-') ?></span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading"><?php echo _l('lead_add_edit_assigned'); ?>: </span>
                    <span class="bold font-medium-xs mbot15"><?php echo(isset($lead) && $lead->assigned != 0 ? get_staff_full_name($lead->assigned) : '-') ?></span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading"><?php echo _l('tags'); ?>: </span>
                    <span class="bold font-medium-xs mbot10">
                        <?php
                        if (isset($lead)) {
                            $tags = get_tags_in($lead->id, 'lead');
                            if (count($tags) > 0) {
                                echo render_tags($tags);
                                echo '<div class="clearfix"></div>';
                            }
                            else
                            {
                                echo '-';
                            }
                        }
                        ?>
                    </span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading">
                        <?php echo _l('leads_dt_datecreated'); ?>:
                    </span>
                    <span class="bold font-medium-xs">
                        <?php echo(isset($lead) && $lead->dateadded != '' ? '<span class="text-has-action" data-toggle="tooltip" data-title="' . _dt($lead->dateadded) . '">' . time_ago($lead->dateadded) . '</span>' : '-') ?>
                    </span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading"><?php echo _l('leads_dt_last_contact'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->lastcontact != '' ? '<span class="text-has-action" data-toggle="tooltip" data-title="' . _dt($lead->lastcontact) . '">' . time_ago($lead->lastcontact) . '</span>' : '-') ?></span>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading"><?php echo _l('lead_public'); ?>: </span>
                    <span class="bold font-medium-xs mbot15">
                        <?php
                        if (isset($lead)) {
                            if ($lead->is_public == 1) {
                                echo _l('lead_is_public_yes');
                            } else {
                                echo _l('lead_is_public_no');
                            }
                        }
                        else
                        {
                            echo '-';
                        }
                        ?>
                    </span>
                </div>
                <?php if (isset($lead) && $lead->from_form_id != 0) { ?>
                    <div class="wap-content firt">
                        <span class="text-muted lead-field-heading"><?php echo _l('web_to_lead_form'); ?>: </span>
                        <span class="bold font-medium-xs mbot15"><?php echo $lead->form_data->name; ?></span>
                    </div>
                <?php } ?>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading"><?php echo _l('tnh_allowed_vat'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo !empty($lead->allowed_vat) ? lang('yes') : lang('no') ?></span>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading"><?php echo _l('cong_note'); ?>: </span>
                    <span class="bold font-medium-xs"><?php echo(isset($lead) && $lead->description != '' ? $lead->description : '-') ?></span>
                </div>
            </div>
            <div class="col-md-6 col-xs-12 lead-information-col mbot10">
                <?php
                    include_once(APPPATH . 'views/admin/leads/group_info/groups_info_lable.php');
                ?>
            </div>
            <div class="col-md-3 col-xs-12 lead-information-col mbot10">
                <?php if (total_rows(db_prefix() . 'customfields', array('fieldto' => 'leads', 'active' => 1)) > 0 && isset($lead)) { ?>
                    <div class="lead-info-heading padding0">
                        <h4 class="no-margin font-medium-xs bold backgroundBlue padding10 colorFFF uppercase">
                            <?php echo _l('custom_fields'); ?>
                        </h4>
                    </div>
                    <?php
                    $custom_fields = get_custom_fields('leads');
                    foreach ($custom_fields as $field) {
                        $value = get_custom_field_value($lead->id, $field['id'], 'leads'); ?>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop"><?php echo $field['name']; ?>: </span>
                            <span class="bold font-medium-xs"><?php echo($value != '' ? $value : '-') ?></span>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

        </div>
        <div class="clearfix"></div>
        <div class="lead-edit<?php if (isset($lead)) { echo ' hide'; } ?>">
            <div class="col-md-3">
                <?php
                    $selected = '';
                    if (isset($lead)) {
                        $selected = $lead->status;
                    } else if (isset($status_id)) {
                        $selected = $status_id;
                    }

                    $render_select = true;
                    $string_select = [];
                    foreach($statuses as $key => $value)
                    {
                        if(!empty($lead) && $lead->status == $value['id'] && $value['isdefault'] == 1)
                        {
                            $render_select = false;
                            $string_select[] = $value;
                        }

                        if($value['isdefault'] == 1)
                        {
                            unset($statuses[$key]);
                        }
                    }
                    if($render_select == true)
                    {
                        echo render_leads_status_select($statuses, $selected, 'lead_add_edit_status');
                    }
                    else
                    {
                        echo render_select('status', $string_select, ['id', 'name'], 'lead_add_edit_status', $selected, [], [], "", "", false);
                    }
                ?>
            </div>
            <div class="col-md-3">
                <?php
                $selected = (isset($lead) ? $lead->source : get_option('leads_default_source'));
                echo render_leads_source_select($sources, $selected, 'lead_add_edit_source');
                ?>
            </div>
            <div class="col-md-3">
                <?php
                $assigned_attrs = array();
                $selected = (isset($lead) ? $lead->assigned : get_staff_user_id());
                if (isset($lead) && $lead->assigned == get_staff_user_id() && $lead->addedfrom != get_staff_user_id() && !is_admin($lead->assigned) && !has_permission('leads', '', 'view')) {
                    $assigned_attrs['disabled'] = true;
                }
                echo render_select('assigned', $members, array('staffid', array('firstname', 'lastname')), 'lead_add_edit_assigned', $selected, $assigned_attrs); ?>
            </div>
            <div class="col-md-3">
                <div class="form-group no-mbot" id="inputTagsWrapper">
                    <label for="tags" class="control-label">
                        <i class="fa fa-tag" aria-hidden="true"></i>
                        <?php echo _l('tags'); ?>
                    </label>
                    <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo(isset($lead) ? prep_tags_input(get_tags_in($lead->id, 'lead')) : ''); ?>" data-role="tagsinput">
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 center">
                <?php $vip_rating = (isset($lead) ? $lead->vip_rating : '0'); ?>
                <div class="text-center" id="div_rating">
                    <h5><?=_l('cong_vip_rating')?></h5>
                    <span class="pointer fa fa-star rating <?= (1 <= $vip_rating ? 'checked' : '')?>" id-star="1" title="<?=_l('cong_1_start')?>"></span>
                    <span class="pointer fa fa-star rating <?= (2 <= $vip_rating ? 'checked' : '')?>" id-star="2" title="<?=_l('cong_2_start')?>"></span>
                    <span class="pointer fa fa-star rating <?= (3 <= $vip_rating ? 'checked' : '')?>" id-star="3" title="<?=_l('cong_3_start')?>"></span>
                    <span class="pointer fa fa-star rating <?= (4 <= $vip_rating ? 'checked' : '')?>" id-star="4" title="<?=_l('cong_4_start')?>"></span>
                    <span class="pointer fa fa-star rating <?= (5 <= $vip_rating ? 'checked' : '')?>" id-star="5" title="<?=_l('cong_5_start')?>"></span>
                    <input type="hidden" name="vip_rating" id="vip_rating" value="<?=$vip_rating?>"/>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr class="no-mtop mbot15"/>
            <div class="col-md-12">
                <div class="wap-left">
                    <div class="wap-left-title bold uppercase event_tab title-main active" active-tab="1">
                      <?=_l('cong_infomation_client')?>
                    </div>
                    <div class="wap-left-title bold uppercase event_tab title-main" active-tab="2">
                      <?=_l('cong_infomation_client_contact')?>
                    </div>

                    <?php $check_custom_fields = false; ?>
                    <?php if (total_rows(db_prefix().'customfields', array('fieldto' => 'leads', 'active' => 1)) > 0){$check_custom_fields=true; ?>
                        <div class="wap-left-title bold uppercase event_tab" active-tab="3">
                          <?=_l('custom_fields')?>
                        </div>
                    <?php } ?>
                    <?php if(!empty($info_group)) { ?>
                      <?php $dem_temp = 4; //4 là số trường cố định + 1 ?>
                      <?php foreach($info_group as $key => $value) { ?>
                        <div class="wap-left-title bold uppercase event_tab" active-tab="<?=$dem_temp?>">
                          <?=$value['name']?>
                        </div>
                      <?php $dem_temp++; ?>
                      <?php } ?>
                    <?php } ?>
                </div>
                <div class="wap-right">
                    <div class="fieldset active" role-fieldset="1">
                        <div class="col-md-12">
                            <div class="align_right">
                                <a type="button" name="next" class="next action-button">Next</a>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12 form-group input_upload <?= (!empty($lead->lead_image) ? 'hide' : '')?>">
                            <label for="lead_image" class="profile-image"><?=_l('cong_img_lead')?></label>
                            <input type="file" name="lead_image" class="form-control" id="lead_image">
                        </div>
                        <div class="col-md-6 col-xs-12 info_image <?= (empty($lead->lead_image) ? 'hide' : '')?>">
                            <?php
                                if (!empty($lead->lead_image))
                                {
                                    $profileImagePath = 'uploads/leads/'.$lead->id.'/thumb_'.$lead->lead_image;
                                    $url = base_url('download/preview_image?path='.$profileImagePath);
                                }
                                else
                                {
                                    $url = base_url('download/preview_image?path=');
                                }
                            ?>
                                <a class="removeImg pointer text-danger" name_img="<?=!empty($lead->lead_image) ? $lead->lead_image : ''?>" title="<?=_l('remove_img')?>" title="<?=_l('remove_image')?>">X</a>
                                <img src="<?=(!empty($url) ? $url : '')?>" class="staff-profile-image-thumb mbot20" id="imgLead" alt="<?=(!empty($lead->name) ? $lead->name : '')?>">
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <?php $value = (isset($lead) ? $lead->zcode : ''); ?>
                            <label for="zcode"><?php echo _l('cong_zcode'); ?></label>
                            <input type="text" name="zcode" id="zcode" class="form-control zcode" value="<?=$value?>" placeholder="<?=_l('system_default_string')?>" >
                        </div>
                        </div>
                        <div class="hide col-md-6 col-xs-12">
                        <?php $value = (isset($lead) ? $lead->type_lead : ''); ?>
                        <?php echo render_select('type_lead', $type_client, ['id', 'name'],'cong_type_lead', $value); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php $value = (isset($lead) ? $lead->company : ''); ?>
                        <?php echo render_input('company', 'cong_company_system_lead', $value); ?>
                        </div>
                        <div class="hide col-md-6 col-xs-12">
                            <?php $value = (isset($lead) ? $lead->name : '-'); ?>
                            <?php echo render_input('name', 'cong_full_name_lead', $value); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <?php $value = (isset($lead) ? $lead->representative : ''); ?>
                            <?php echo render_input('representative', 'cong_full_name_lead', $value); ?>
                        </div>
                        <input type="text" class="hide" id="id_leads_ch" value="<?= (isset($lead) ? $lead->id : ''); ?>">
                        <div class="col-md-6 col-xs-12">
                        <?php $value=( isset($lead) ? _d($lead->date_create_company) : ''); ?>
                        <?php echo render_date_input( 'date_create_company', 'cong_date_create_company', $value); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">

                        <?php $value = (isset($lead) ? $lead->vat : ''); ?>
                        <?php echo render_input('vat', 'cong_vat', $value); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php $value = (isset($lead) ? $lead->phonenumber : ''); ?>
                        <?php echo render_input('phonenumber', 'lead_add_edit_phonenumber', $value); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php $value = (isset($lead) ? $lead->email : ''); ?>
                        <?php echo render_input('email', 'lead_add_edit_email', $value); ?>
                        </div>
                        <div class="hide col-md-6 col-xs-12">
                            <?php $value = (isset($lead) ? $lead->fax : ''); ?>
                            <?php echo render_input('fax', 'cong_client_fax', $value); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php $value = (isset($lead) ? $lead->address : ''); ?>
                        <?php echo render_textarea('address', 'lead_address', $value, array('rows' => 3)); ?>
                        </div>
                        <div class="hide">
                            <!--Tôn giáo -->
                            <?php $selected = (isset($lead) ? $lead->religion : ''); ?>
                            <?php echo render_select('religion', (!empty($religion) ? $religion : []), array('id', 'name'), 'cong_client_religion', $selected); ?>
                            <!--Hôn nhân -->
                            <?php $selected = (isset($lead) ? $lead->marriage : ''); ?>
                            <?php echo render_select('marriage', (!empty($marriage) ? $marriage : []), array('id', 'name'), 'cong_client_marriage', $selected); ?>

                            <?php $value = (isset($lead) ? $lead->zip : ''); ?>
                            <?php echo render_input('zip', 'lead_zip', $value); ?>

                            <?php if (get_option('disable_language') == 0) { ?>
                                <div class="form-group">
                                    <label for="default_language" class="control-label">
                                        <?php echo _l('localization_default_language'); ?>
                                    </label>
                                    <select name="default_language" data-live-search="true" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="">
                                            <?php echo _l('system_default_string'); ?>
                                        </option>
                                        <?php foreach ($this->app->get_available_languages() as $language) {
                                            $selected = '';
                                            if (isset($lead)) {
                                                if ($lead->default_language == $language) {
                                                    $selected = 'selected';
                                                }
                                            }
                                            ?>
                                            <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php
                            $countries = get_all_countries();
                            $customer_default_country = get_option('customer_default_country');
                            $selected = (isset($lead) ? $lead->country : $customer_default_country);
                            echo render_select('country', $countries, array('country_id', array('short_name')), 'lead_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                        ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php $city = get_table_where('tblprovince', array('countries' => (isset($lead) ? $lead->country : $customer_default_country))); ?>
                        <?php $selected = (isset($lead) ? $lead->city : ''); ?>
                        <?php echo render_select('city', $city, array('provinceid', 'name'), 'cong_client_city', $selected); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php $selected = (isset($lead) ? $lead->district : ''); ?>
                        <?php echo render_select('district', (!empty($district) ? $district : []), array('districtid', 'name'), 'cong_client_district', $selected); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php $selected = (isset($lead) ? $lead->ward : ''); ?>
                        <?php echo render_select('ward', (!empty($ward) ? $ward : []), array('wardid', 'name'), 'cong_client_ward', $selected); ?>
                        </div>
                        <div class="hide">
                        <?php $value = (isset($lead) ? $lead->state : ''); ?>
                        <?php echo render_input('state', 'lead_state', $value); ?>
                        <!-- khu vực -->
                        <?php $value = (isset($lead) ? $lead->area : ''); ?>
                        <?php echo render_input('area', 'cong_client_area', $value); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php if ((isset($lead) && empty($lead->website)) || !isset($lead)) {
                            $value = (isset($lead) ? $lead->website : '');
                            echo render_input('website', 'lead_website', $value);
                        } else { ?>
                            <div class="form-group">
                                <label for="website"><?php echo _l('lead_website'); ?></label>
                                <div class="input-group">
                                    <input type="text" name="website" id="website" value="<?php echo $lead->website; ?>" class="form-control">
                                    <div class="input-group-addon">
                                         <span>
                                              <a href="<?php echo maybe_add_http($lead->website); ?>" target="_blank" tabindex="-1">
                                                <i class="fa fa-globe"></i>
                                              </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php $value = (isset($lead) ? $lead->facebook : ''); ?>
                        <?php echo render_input('facebook', 'cong_client_facebook', $value); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <div class="checkbox">
                                <input type="checkbox" id="allowed_vat" name="allowed_vat" <?= !empty($lead) ? 'checked' : '' ?> value="1">
                                <label for="allowed_vat"><?=_l('tnh_allowed_vat')?></label>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <label class="control-label"><?=_l('sex')?></label>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <div class="radio">
                                <input type="radio" id="gender_male" name="gender" value="1" <?=( ((isset($lead) && $lead->gender == 1) || empty($lead)) ? 'checked' : '')?>>
                                <label for="gender_male"><?=_l('cong_male')?></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="radio">
                                <input type="radio" id="gender_female" name="gender" value="2" <?=( (isset($lead) && $lead->gender == 2) ? 'checked' : '')?>>
                                <label for="gender_female"><?=_l('cong_female')?></label>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6 col-xs-12">
                        <?php $value = (isset($lead) ? $lead->description : ''); ?>
                        <?php echo render_textarea('description', 'cong_note', $value); ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="fieldset" role-fieldset="2">
                        <div class="col-md-12">
                            <div class="align_right">
                                <a type="button" name="previous" class="previous action-button">Previous</a>
                                <a type="button" name="next" class="next action-button" <?=($check_custom_fields == false ? 'data-stt="2"' : '')?>>Next</a>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div id="div_contacts">
                                <?php if(isset($lead->contacts)){ $i = 0;?>
                                    <?php foreach($lead->contacts as $key => $value){?>
                                        <div class="col-md-6 items_contact">
                                            <h5 class="mtop20"><?=_l('cong_contacts')?></h5>
                                            <p class="mborder"></p>
                                            <div class="pborder">
                                                <div class="text-right">
                                                    <a class="remove_contact_panel pointer text-right text-danger" title="Xóa" name-data="<?=$i?>">
                                                        <i class="fa fa-trash gf-icon-hover"></i>
                                                    </a>
                                                </div>
                                                <div class="col-md-6 mtop10">
                                                    <input type="hidden" id="contacts[<?=$i?>][id]" name="contacts[<?=$i?>][id]" value="<?=$value['id']?>"/>
                                                    <div class="form-group" app-field-wrapper="contacts[<?=$i?>][firstname]">
                                                        <label for="contacts[<?=$i?>][firstname]" class="control-label"> <?=_l('cong_last_firstname')?></label>
                                                        <input type="text" name="contacts[<?=$i?>][firstname]" id="contacts[<?=$i?>][firstname]"  tabindex=<?=(1*($i+1))?>  class="form-control" autofocus="1" value="<?=$value['firstname']?>">
                                                    </div>

                                                    <div class="form-group" app-field-wrapper="contacts[<?=$i?>][title]">
                                                        <label for="contacts[<?=$i?>][title]" class="control-label"> <?=_l('cong_title')?></label>
                                                        <input type="text" name="contacts[<?=$i?>][title]" id="contacts[<?=$i?>][title]" tabindex=<?=(3*($i+1))?> class="form-control" autofocus="1" value="<?=$value['title']?>">
                                                    </div>
                                                    <div class="form-group" app-field-wrapper="contacts[<?=$i?>][phonenumber]">
                                                        <label for="contacts[<?=$i?>][phonenumber]" class="control-label"> <?=_l('cong_phonenumber')?></label>
                                                        <input type="text" name="contacts[<?=$i?>][phonenumber]" id="contacts[<?=$i?>][phonenumber]" tabindex=<?=(5*($i+1))?> class="form-control" autofocus="1" value="<?=$value['phonenumber']?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mtop10">
                                                    <div class="form-group" app-field-wrapper="contacts[<?=$i?>][email]">
                                                        <label for="contacts[<?=$i?>][email]" class="control-label"> <?=_l('cong_email')?></label>
                                                        <input type="text" id="contacts[<?=$i?>][email]" name="contacts[<?=$i?>][email]" tabindex="<?=(4*($i+1))?>" class="form-control" autofocus="1" value="<?=$value['email']?>">
                                                    </div>
                                                    <div class="form-group" app-field-wrapper="contacts[<?=$i?>][birtday]">
                                                        <label for="contacts[<?=$i?>][birtday]" class="control-label"> <?=_l('cong_birtday')?></label>
                                                        <div class="input-group date">
                                                            <input type="text" id="contacts[<?=$i?>][birtday]" name="contacts[<?=$i?>][birtday]"   class="datepicker form-control" tabindex="<?=(4*($i+1))?>"  autofocus="1" value="<?=!empty($value['birtday']) ? _d($value['birtday']) : ''?>">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar calendar-icon"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group" app-field-wrapper="contacts[<?=$i?>][note]">
                                                        <label for="contacts[<?=$i?>][note]" class="control-label"><?=_l('cong_note')?></label>
                                                        <textarea id="contacts[<?=$i?>][note]" name="contacts[<?=$i?>][note]" tabindex=<?=(6*($i+1))?> class="form-control" rows="4"><?=$value['note']?></textarea>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                        <?php ++$i; }?>
                                    <?php $keyContact = $i;}?>
                            </div>
                            <div class="pd20 mtop40 border-ds col-md-6 offset-md-6 text-center add-contacts" onclick="addContact()">
                                <div class="col-md-12 no-padd">
                                    <i class="lnr lnr-users font-40"></i>
                                </div>
                                <a>
                                    <i class="gicon-plus mr5 mt3"></i><?=_l('cong_add_contacts')?>
                                </a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="fieldset" role-fieldset="3">
                        <div class="col-md-12">
                            <div class="align_right">
                                <a type="button" name="previous" class="previous action-button">Previous</a>
                                <a type="button" name="next" class="next action-button">Next</a>
                            </div>
                        </div>
                        <?php $rel_id = (isset($lead) ? $lead->id : false); ?>
                        <?php echo render_custom_fields('leads', $rel_id); ?>
                        <div class="clearfix"></div>
                    </div>
                    <?php
                        include_once(APPPATH . 'views/admin/leads/group_info/groups_info.php');
                    ?>
                </div>
            </div>

            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12 mtop10">
                        <?php if (!isset($lead)) { ?>
                            <div class="lead-select-date-contacted hide">
                                <?php echo render_datetime_input('custom_contact_date', 'lead_add_edit_datecontacted', '', array('data-date-end-date' => date('Y-m-d'))); ?>
                            </div>
                        <?php } else { ?>
                            <?php echo render_datetime_input('lastcontact', 'leads_dt_last_contact', _dt($lead->lastcontact), array('data-date-end-date' => date('Y-m-d'))); ?>
                        <?php } ?>
                        <div class="checkbox-inline checkbox checkbox-primary<?php if (isset($lead)) { echo ' hide'; } ?><?php if (isset($lead) && (is_lead_creator($lead->id) || has_permission('leads', '', 'edit'))) { echo ' lead-edit'; } ?>">
                            <input type="checkbox" name="is_public" <?php if (isset($lead)) { if ($lead->is_public == 1) { echo 'checked'; } }; ?> id="lead_public">
                            <label for="lead_public">
                                <?php echo _l('lead_public'); ?>
                            </label>
                        </div>
                        <?php if (!isset($lead)) { ?>
                            <div class="checkbox-inline checkbox checkbox-primary">
                                <input type="checkbox" name="contacted_today" id="contacted_today" checked>
                                <label for="contacted_today"><?php echo _l('lead_add_edit_contacted_today'); ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <?php if (isset($lead)) { ?>
        <div class="lead-latest-activity lead-view">
            <div class="lead-info-heading">
                <h4 class="no-margin bold font-medium-xs"><?php echo _l('lead_latest_activity'); ?></h4>
            </div>
            <div id="lead-latest-activity" class="pleft5"></div>
        </div>
    <?php } ?>
    <?php if ($lead_locked == false) { ?>
        <div class="lead-edit<?php if (isset($lead)) { echo ' hide'; } ?>">
            <hr/>
            <button type="submit" class="btn btn-info pull-right lead-save-btn" id="lead-form-submit">
                <?php echo _l('submit'); ?>
            </button>
            <button type="button" class="btn btn-default pull-right mright5" data-dismiss="modal">
                <?php echo _l('close'); ?>
            </button>
        </div>
    <?php } ?>
    <div class="clearfix"></div>
    <?php echo form_close(); ?>
</div>
<?php if (isset($lead) && $lead_locked == true) { ?>
    <script>
        $(function () {
            // Set all fields to disabled if lead is locked

            $.each($('.lead-wrapper').find('input, select, textarea'), function () {
                $(this).attr('disabled', true);
                if ($(this).is('select')) {
                    $(this).selectpicker('refresh');
                }
            });
        });
    </script>
<?php } ?>
<script>
    $( ".lead-save-btn" ).click(function() {
        setTimeout(function(){ checkValidateForm(); }, 100);
    });
    $(document).ready(function() {
        setTimeout(function(){ reSizeHeight(); }, 100);
    });
    $( ".wap-edit" ).click(function() {
        setTimeout(function(){ reSizeHeight(); }, 100);
    });
    function reSizeHeight() {
        var Height = $(".wap-left").height();
        Height = Number(Height) - 3;
        var right = document.getElementsByClassName("wap-right");
        right[0].style.height = Height+"px";
    }

    var kContact = <?= !empty($keyContact) ? $keyContact : "0" ?>;
    var is_required_contact_lead = {};
    for(var i = 0; i < kContact; i++)
    {
        is_required_contact_lead['contacts['+i+'][firstname]']  = 'required';
        is_required_contact_lead['contacts['+i+'][email]']  = 'required';
        is_required_contact_lead['contacts['+i+'][phonenumber]']  = "required";
    }

</script>
<script src="<?=base_url('assets/js/step_by_step.js')?>"></script>