<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .pborder_padding{
        border:1px solid #b1b1b1;
        padding:15px;
    }
    .fontsize30{
        font-size: 30px;
    }
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
<div class="modal fade" id="convert_lead_to_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <?php echo form_open('admin/leads/convert_to_customerAjax', array('id' => 'lead_to_client_formfb')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('lead_convert_to_client'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="col-md-3">
                        <?php echo form_hidden('zcode', $lead->zcode); ?>
                        <?php echo form_hidden('id_facebook', $lead->id_facebook); ?>
                        <?php
                            $selected = (isset($lead) ? $lead->source : get_option('leads_default_source'));
                            echo render_leads_source_select($sources, $selected, 'lead_add_edit_source', 'sources');
                        ?>
                    </div>
                    <div class="col-md-9 center">
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
                </div>
                <div class="clearfix"></div>
                <hr class="no-mtop mbot15"/>
                <div class="col-md-12">
                    <div class="wap-left">
                        <div class="wap-left-title bold uppercase event_tab active" active-tab="1">
                          <?=_l('cong_infomation_client')?>
                        </div>
                        <div class="wap-left-title bold uppercase event_tab" active-tab="2">
                          <?=_l('cong_profile_client')?>
                        </div>
                        <div class="wap-left-title bold uppercase event_tab" active-tab="3">
                          <?=_l('cong_infomation_client_contact')?>
                        </div>
                        <div class="wap-left-title bold uppercase event_tab" active-tab="4">
                          <?=_l('cong_infomation_profile')?>
                        </div>
                        <div class="wap-left-title bold uppercase event_tab" active-tab="5">
                          <?=_l('cong_infomation_address')?>
                        </div>
                        <div class="wap-left-title bold uppercase event_tab" active-tab="6">
                          <?=_l('cong_infomation_other')?>
                        </div>
                        <?php
                            $customer_custom_fields = false;
                            if(total_rows(db_prefix().'customfields',array('fieldto' => 'leads','active'=>1)) > 0 ){
                                $customer_custom_fields = true;
                        ?>
                            <div class="wap-left-title bold uppercase event_tab" active-tab="7">
                                <?=_l('custom_fields')?>
                            </div>
                        <?php } ?>
                        <?php if(!empty($info_group)) { ?>
                          <?php $dem_temp = 8; //8 là số trường cố định + 1 ?>
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
                            <?php echo form_hidden('leadid', $lead->id); ?>
                            <?php if (mb_strpos($lead->name, ' ') !== false) {
                                $_temp = explode(' ', $lead->name);
                                $firstname = $_temp[0];
                                if (isset($_temp[2])) {
                                    $lastname = $_temp[1] . ' ' . $_temp[2];
                                } else {
                                    $lastname = $_temp[1];
                                }
                            } else {
                                $lastname = '';
                                $firstname = $lead->name;
                            }
                            ?>
                            <?php echo form_hidden('default_language', $lead->default_language); ?>
                            <?php echo form_hidden('code_type', $lead->code_type); ?>
                            <?php echo render_select('type_client', $type_client, ['id', 'name'], 'cong_type_client', $lead->type_lead)?>
                            <?php echo render_input('company', 'cong_client_company_pm', $lead->company); ?>
                            <?php echo render_input('fullname', 'cong_client_fullname', $lead->name); ?>
                            <?php echo render_date_input('date_create_company', 'cong_date_create_company', _d($lead->date_create_company));?>
                            <?php echo render_input('vat', 'cong_vat', $lead->vat); ?>
                            <div class="clearfix"></div>
                        </div>
                        <div class="fieldset" role-fieldset="2">
                            <div class="col-md-12">
                                <div class="align_right">
                                    <a type="button" name="previous" class="previous action-button">Previous</a>
                                    <a type="button" name="next" class="next action-button">Next</a>
                                </div>
                            </div>
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

                            <!--Tôn giáo -->
                            <?php $selected = (isset($lead) ? $lead->religion : ''); ?>
                            <?php echo render_select('religion', (!empty($religion) ? $religion : []), array('id', 'name'), 'cong_client_religion', $selected); ?>

                            <!--Hôn nhân -->
                            <?php $selected = (isset($lead) ? $lead->marriage : ''); ?>
                            <?php echo render_select('marriage', (!empty($marriage) ? $marriage : []), array('id', 'name'), 'cong_client_marriage', $selected); ?>
                            <div class="clearfix"></div>
                        </div>
                        <div class="fieldset" role-fieldset="3">
                            <div class="col-md-12">
                                <div class="align_right">
                                    <a type="button" name="previous" class="previous action-button">Previous</a>
                                    <a type="button" name="next" class="next action-button">Next</a>
                                </div>
                            </div>
                            <?php echo render_input('facebook', 'cong_client_facebook', $lead->facebook); ?>
                            <?php echo render_input('phonenumber', 'lead_convert_to_client_phone', $lead->phonenumber); ?>
                            <?php $value = (isset($lead) ? $lead->fax : ''); ?>
                            <?php echo render_input('fax', 'cong_client_fax', $value); ?>
                            <?php echo render_textarea('address', 'client_address', $lead->address); ?>
                            <?php echo render_input('email_client', 'lead_convert_to_email', $lead->email); ?>
                            <?php echo render_input('website', 'client_website', $lead->website); ?>
                            <div class="col-md-12">
                                <div id="div_contacts_row">
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
                                                    <div class="col-md-12 mtop10">
                                                        <div class="form-group" app-field-wrapper="contacts[<?=$i?>][lastname]">
                                                            <label for="contacts[<?=$i?>][lastname]" class="control-label"> <?=_l('cong_lastname')?></label>
                                                            <input type="text" name="contacts[<?=$i?>][lastname]" id="contacts[<?=$i?>][lastname]"  tabindex=<?=(1*($i+1))?>  class="form-control" autofocus="1" value="<?=$value['lastname']?>">
                                                        </div>
                                                        <div class="form-group" app-field-wrapper="contacts[<?=$i?>][firstname]">
                                                            <label for="contacts[<?=$i?>][firstname]" class="control-label"> <?=_l('cong_firstname')?></label>
                                                            <input type="text" name="contacts[<?=$i?>][firstname]" id="contacts[<?=$i?>][firstname]" tabindex=<?=(2*($i+1))?> class="form-control" autofocus="1" value="<?=$value['firstname']?>">
                                                        </div>
                                                        <div class="form-group" app-field-wrapper="contacts[<?=$i?>][phonenumber]">
                                                            <label for="contacts[<?=$i?>][phonenumber]" class="control-label"> <?=_l('cong_phonenumber')?></label>
                                                            <input type="text" name="contacts[<?=$i?>][phonenumber]" id="contacts[<?=$i?>][phonenumber]" tabindex=<?=(5*($i+1))?> class="form-control" autofocus="1" value="<?=$value['phonenumber']?>">
                                                        </div>
                                                        <div class="form-group" app-field-wrapper="contacts[<?=$i?>][title]">
                                                            <label for="contacts[<?=$i?>][title]" class="control-label"> <?=_l('cong_title')?></label>
                                                            <input type="text" name="contacts[<?=$i?>][title]" id="contacts[<?=$i?>][title]" tabindex=<?=(3*($i+1))?> class="form-control" autofocus="1" value="<?=$value['title']?>">
                                                        </div>
                                                        <div class="form-group" app-field-wrapper="contacts[<?=$i?>][email]">
                                                            <label for="contacts[<?=$i?>][email]" class="control-label"> <?=_l('cong_email')?></label>
                                                            <input type="text" id="contacts[<?=$i?>][email]" name="contacts[<?=$i?>][email]" tabindex=<?=(4*($i+1))?>  class="form-control" autofocus="1" value="<?=$value['email']?>">
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
                                <div class="pd20 mtop40 border-ds col-md-6 offset-md-6 text-center add-contacts" onclick="addContact_Full()">
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
                        <div class="fieldset" role-fieldset="4">
                            <div class="col-md-12">
                                <div class="align_right">
                                    <a type="button" name="previous" class="previous action-button">Previous</a>
                                    <a type="button" name="next" class="next action-button">Next</a>
                                </div>
                            </div>
                            <?php $value = (isset($lead) ? _d($lead->birtday) : ''); ?>
                            <?php echo render_date_input('birtday', 'cong_client_birtday', $value); ?>
                            <?php $selected = (isset($lead) ? $lead->dt : ''); ?>
                            <?php echo render_select('dt', (!empty($dt) ? $dt : []), array('id', 'name'), 'cong_client_dt', $selected); ?>
                            <?php $selected = (isset($lead) ? $lead->kt : ''); ?>
                            <?php echo render_select('kt', (!empty($kt) ? $kt : []), array('id', 'name'), 'cong_client_kt', $selected); ?>
                            <div class="clearfix"></div>
                        </div>
                        <div class="fieldset" role-fieldset="5">
                            <div class="col-md-12">
                                <div class="align_right">
                                    <a type="button" name="previous" class="previous action-button">Previous</a>
                                    <a type="button" name="next" class="next action-button">Next</a>
                                </div>
                            </div>
                            <?php
                            $countries = get_all_countries();
                            $customer_default_country = get_option('customer_default_country');
                            $selected = ($lead->country != 0 ? $lead->country : $customer_default_country);
                            echo render_select('country', $countries, array('country_id', array('short_name')), 'clients_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                            ?>
                            <?php $selected = (isset($lead) ? $lead->city : ''); ?>
                            <?php echo render_select('city', $city, array('provinceid', 'name'), 'cong_client_city', $selected); ?>
                            <?php $selected = (isset($lead) ? $lead->district : ''); ?>
                            <?php echo render_select('district', (!empty($district) ? $district : []), array('districtid', 'name'), 'cong_client_district', $selected); ?>
                            <?php $selected = (isset($lead) ? $lead->ward : ''); ?>
                            <?php echo render_select('ward', (!empty($ward) ? $ward : []), array('wardid', 'name'), 'cong_client_ward', $selected); ?>
                            <?php echo render_input('state', 'client_state', $lead->state); ?>
                            <?php $value = (isset($lead) ? $lead->area : ''); ?>
                            <?php echo render_input('area', 'cong_client_area', $value); ?>
                            <div class="clearfix"></div>
                        </div>
                        <div class="fieldset" role-fieldset="6">
                            <div class="col-md-12">
                                <div class="align_right">
                                    <a type="button" name="previous" class="previous action-button">Previous</a>
                                    <a type="button" name="next" class="next action-button">Next</a>
                                </div>
                            </div>
                            <?php echo render_input('zip', 'clients_zip', $lead->zip); ?>
                            <div class="clearfix"></div>
                        </div>
                        <div class="fieldset" role-fieldset="7">
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
                            $convert_to_customer = true;
                            include_once(APPPATH . 'views/admin/leads/group_info/groups_info.php');
                        ?>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">

                    <?php if (total_rows(db_prefix() . 'emailtemplates', array('slug' => 'new-client-created', 'active' => 0)) == 0) { ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="donotsendwelcomeemail" id="donotsendwelcomeemail">
                            <label for="donotsendwelcomeemail"><?php echo _l('client_do_not_send_welcome_email'); ?></label>
                        </div>
                    <?php } ?>
                    <?php if (total_rows(db_prefix() . 'notes', array('rel_type' => 'lead', 'rel_id' => $lead->id)) > 0) { ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="transfer_notes" id="transfer_notes">
                            <label for="transfer_notes"><?php echo _l('transfer_lead_notes_to_customer'); ?></label>
                        </div>
                    <?php } ?>
                    <?php if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1' && count($purposes) > 0) { ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="transfer_consent" id="transfer_consent">
                            <label for="transfer_consent"><?php echo _l('transfer_consent'); ?></label>
                        </div>
                    <?php } ?>

                </div>
                <div class="clearfix"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="init_lead(<?php echo $lead->id; ?>); return false;" data-dismiss="modal"><?php echo _l('back_to_lead'); ?></button>
                <button type="submit" data-form="#lead_to_client_formfb" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info btn-sutmit-cover">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script>
    var kContact = <?= !empty($keyContact) ? $keyContact : "0" ?>;
    var is_required_convert = {type_client:'required'};
    for(var i = 0; i < kContact; i++)
    {
        is_required_convert['contacts['+i+'][firstname]']  = 'required';
        is_required_convert['contacts['+i+'][email]']  = 'required';
        is_required_convert['contacts['+i+'][phonenumber]']  = "required";
    }
    validate_lead_convert_to_client_formFB();
    init_selectpicker();
    $('#convert_lead_to_client_modal').on('shown.bs.modal', function () {
        setTimeout(function(){ reSizeHeight(); }, 100);
    });
    function reSizeHeight() {
        var Height = $(".wap-left").height();
        console.log(Height);
        var right = document.getElementsByClassName("wap-right");
        right[0].style.height = Height+"px";
    }
    $('body').on('click', '.remove_contact_panel', function(e){
        $(this).parents('.items_contact').remove();
    })
    $('.rating').click(function(e){
        var id_star = $(this).attr('id-star');
        var div_rating = $(this).parents('#div_rating');
        div_rating.find('.rating').removeClass('checked');
        $(this).addClass('checked');
        for(var i = 1;i < id_star; i++)
        {
            div_rating.find('.rating[id-star="'+i+'"]').addClass('checked');
        }
        $('input[name="vip_rating"]').val(id_star);
    })
    $( ".btn-sutmit-cover" ).click(function() {
        setTimeout(function(){ checkValidateForm(); }, 100);
    });


    function validate_lead_convert_to_client_formFB() {
        var e = {
            firstname: "required",
            lastname: "required",
            password: {
                required: {
                    depends: function(e) {
                        if (!1 === $('input[name="send_set_password_email"]').prop("checked")) return !0
                    }
                }
            },
            email_client: "required"
        };
        if(typeof(is_required_lead) !== 'undefined')
        {
            $.each(is_required_lead, function(iLead, vLead){
                e[vLead] = "required";
            })
        }
        if(typeof(is_required_convert) !== 'undefined')
        {
            $.each(is_required_convert, function(iLead, vLead){
                e[iLead] = "required";
            })
        }
        1 == app.options.company_is_required && (e.company = "required"), appValidateForm($("#lead_to_client_formfb"), e, manageConvertToClient)
    }
    function manageConvertToClient(form)
    {
        var button = $('#lead_to_client_formfb').find('button[type="submit"]');
        button.button({loadingText: '<?=_l('cong_please_wait')?>'});
        button.button('loading');
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            console.log(response);
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                var id_facebook = $('#id_facebook').val();
                varInfoUser(id_facebook);
            }
            $('#convert_lead_to_client_modal').modal('hide');
        }).always(function() {
            button.button('reset')
        });
        return false;
    }

</script>
<script src="<?=base_url('assets/js/step_by_step.js')?>"></script>
