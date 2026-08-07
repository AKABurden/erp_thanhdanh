<?php echo form_open('messager/detail_Listfb', array('id' => 'form_action_client', 'class' => 'form_listfb')); ?>
    <div class="customer-info col-md-12" id="listfb-info">
        <div class="left-info col-md-12">
            <div class="img-customer">
                <div id="img-customer">
                    <?php
                    if(!empty($data->img))
                    {
                        echo '<img src="'.$data->img.'">';
                    }
                    else
                    {
                        echo '<img class="noneImg" src="'.base_url('assets/images/user-placeholder.jpg').'">';
                    }
                    ?>
                </div>
                <?php if(!empty($data)){
                    $alert_type ='info';
                    $type_messs = _l('cong_data_listid_new');
                }
                ?>
                <div class="ribbon <?=$alert_type?>">
                    <span><?=$type_messs?></span>
                </div>
                <?php
                $value = !empty($data->id_facebook) ? $data->id_facebook : (!empty($id_facebook) ? $id_facebook : '');
                ?>
                <input type="hidden" id="id_facebook" name="id_facebook" value="<?=$value?>">

                <?php
                $value = (!empty($data->id) ? $data->id : '');
                ?>
                <input type="hidden" id="id" name="id" value="<?=$value?>">
            </div>
            <div class="profile-customer text-center">
                <span id="name-customer-right">
                    <?=!empty($data->name) ? $data->name : ''?>
                </span>
            </div>
            <div class="action_profile mtop20 row">
                <div class="mtop20 text-center">
                    <button class="btn btn-info war_lead font10  btn-icon" id-data="fb" type="button">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        <?=_l('cong_war_lead_short')?>
                    </button>
                    <button class="btn btn-success war_client font10  btn-icon" id-data="fb" type="button">
                        <?=_l('cong_war_client_short')?>
                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
                <p class="text-center">
                    <button class="mtop15 btn btn-info btn-icon font10" onclick="CreateOrders(<?=$data->id?>, 'listfb')" type="button">
                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                        <?=_l('cong_create_orders')?>
                    </button>
                </p>
            </div>
        </div>
        <div class="right-info mleft5">
            <div class="view_customer">
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading no-mtop"><?=_l('cong_code_inbox')?>: </span>
                    <span class="bold font-medium-xs mbot15">
                        <?=!empty($data->prefix) ? $data->prefix : ''?> - <?=!empty($data->orders_day) ? $data->orders_day : ''?>
                    </span>
                </div>

                <div class="wap-content second">
                    <span class="text-muted lead-field-heading no-mtop"><?=_l('cong_name_client')?>: </span>
                    <span class="bold font-medium-xs mbot15">
                        <?=!empty($data->name) ? $data->name : '-'?>
                    </span>
                    <?php
                        $DataInput =
                        "<input type='text' id='name' name='name' class='name_listfb form-control'  placeholder='"._l('cong_name_lead')."' value='".(!empty($data->name) ? $data->name : '')."'>
                        <div class='mtop10'>
                            <button type='submit' class='btn btn-icon btn-info'> "._l('submit')."</button>
                            <button type='button' class='btn btn-icon btn-default ClosePopover'>"._l('close')."</button>
                        </div>";
                    ?>
                    <a data-toggle="popover" class="pull-right" title="<?=_l('edit').' '._l('cong_name_lead') ?>" data-placement="left" data-html="true" data-content="<?=$DataInput?>">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                </div>

                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading no-mtop"><?=_l('cong_company')?>: </span>
                    <span class="bold font-medium-xs mbot15">
                        <?=!empty($data->company) ? $data->company : '-'?>
                    </span>
                    <?php
                        $DataInput =
                        "<input type='text' id='company' name='company' class='company_listfb form-control'  placeholder='"._l('cong_company')."' value='".(!empty($data->company) ? $data->company : '')."'>
                        <div class='mtop10'>
                            <button type='submit' class='btn btn-icon btn-info'> "._l('submit')."</button>
                            <button type='button' class='btn btn-icon btn-default ClosePopover'>"._l('close')."</button>
                        </div>";
                    ?>
                    <a data-toggle="popover" class="pull-right" title="<?=_l('edit').' '._l('cong_company') ?>" data-placement="left" data-html="true" data-content="<?=$DataInput?>">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                </div>
                <div class="wap-content second">
                    <span class="text-muted lead-field-heading no-mtop"><?=_l('cong_phonenumber')?>: </span>
                    <span class="bold font-medium-xs mbot15">
                        <?=!empty($data->phonenumber) ? $data->phonenumber : '-'?>
                    </span>
                    <?php
                        $DataInput =
                        "<input type='text' id='phonenumber' name='phonenumber' class='phone_number_listfb form-control'  placeholder='"._l('cong_phonenumber')."' value='".(!empty($data->phonenumber) ? $data->phonenumber : '')."'>
                        <div class='mtop10'>
                            <button type='submit' class='btn btn-icon btn-info'> "._l('submit')."</button>
                            <button type='button' class='btn btn-icon btn-default ClosePopover'>"._l('close')."</button>
                        </div>";
                    ?>
                    <a data-toggle="popover" class="pull-right" title="<?=_l('edit').' '._l('cong_phonenumber') ?>" data-placement="left" data-html="true" data-content="<?=$DataInput?>">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading no-mtop"><?=_l('cong_address')?>: </span>
                    <span class="bold font-medium-xs mbot15">
                        <?=!empty($data->address) ? $data->address : '-'?>
                    </span>
                    <?php
                        $DataInput = "
                                <input type='text' id='address' class='address_listfb form-control' name='address' placeholder='"._l('cong_address')."' value='".(!empty($data->address) ? $data->address : '')."'>
                                <div class='mtop10'>
                                    <button type='submit' class='btn btn-icon btn-info'> "._l('submit')."</button>
                                    <button type='button' class='btn btn-icon btn-default'>"._l('close')."</button>
                                </div>";
                    ?>
                    <a data-toggle="popover" class="pull-right" title="<?=_l('edit').' '._l('cong_address')?>" data-placement="left" data-html="true" data-content="<?=$DataInput?>">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                </div>

                <div class="wap-content second">
                    <span class="text-muted lead-field-heading no-mtop"><?=_l('cong__gender')?>: </span>
                    <span class="bold font-medium-xs mbot15">
                        <?=!empty($data->gender) ? ($data->gender == 1 ? _l('cong_male') : _l('cong_female')) : '-'?>
                    </span>
                    <?php
                        $DataInput = "
                                <input type='radio' value='1' class='male' name='gender' ".((empty($data->gender) || $data->gender == 1) ? 'checked' : '').">"._l('cong_male')."
                                <input type='radio' value='2' class='female mleft10' name='gender' ".((!empty($data->gender) && $data->gender == 2) ? 'checked' : '').">"._l('cong_female')."
                                <div class='mtop10'>
                                    <button type='submit' class='btn btn-icon btn-info'> "._l('submit')."</button>
                                    <button type='button' class='btn btn-icon btn-default'>"._l('close')."</button>
                                </div>";
                    ?>
                    <a data-toggle="popover" class="pull-right" title="<?=_l('edit').' '._l('cong__gender')?>" data-placement="left" data-html="true" data-content="<?=$DataInput?>">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                </div>

                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading no-mtop"><?=_l('cong_email')?>: </span>
                    <span class="bold font-medium-xs mbot15">
                         <?=!empty($data->email) ? $data->email : '-'?>
                    </span>
                    <?php
                        $DataInput = "
                                <input type='text' id='email_client' class='email_listfb form-control' name='email' placeholder='"._l('cong_email')."' value='".(!empty($data->email) ? $data->email : '')."'>
                                <div class='mtop10'>
                                    <button type='submit' class='btn btn-icon btn-info'> "._l('submit')."</button>
                                    <button type='button' class='btn btn-icon btn-default'>"._l('close')."</button>
                                </div>";
                    ?>
                    <a data-toggle="popover" class="pull-right" title="<?=_l('edit').' '._l('cong_email')?>" data-placement="left" data-html="true" data-content="<?=$DataInput?>">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                </div>

                <div class="wap-content second">
                    <span class="text-muted lead-field-heading no-mtop">
                        <?=_l('cong_birtday')?>:
                    </span>
                    <span class="bold font-medium-xs mbot15">
                            <?=!empty($data->birtday) ? _d($data->birtday) : '-'?>
                        </span>
                    <?php
                        $DataInput = "
                            <div class='input-group date'>
                                <input type='text' id='birtday' class='datepicker form-control' name='birtday' placeholder='"._l('cong_birtday')."' value='".(!empty($data->birtday) ? _d($data->birtday) : '')."'>
                                <div class='input-group-addon'>
                                    <i class='fa fa-calendar calendar-icon'></i>
                                </div>
                            </div>
                            <div class='mtop10'>
                                <button type='submit' class='btn btn-icon btn-info'> "._l('submit')."</button>
                                <button type='button' class='btn btn-icon btn-default'>"._l('close')."</button>
                            </div>";
                    ?>
                    <a data-toggle="popover" class="pull-right" title="<?=_l('edit').' '._l('cong_birtday')?>" data-placement="left" data-html="true" data-content="<?=$DataInput?>">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                </div>
                <div class="wap-content firt">
                    <span class="text-muted lead-field-heading no-mtop">
                        <?=_l('cong_zcode')?>:
                    </span>
                    <span class="bold font-medium-xs mbot15">
                            <?=!empty($data->zcode) ? $data->zcode : '-'?>
                        </span>
                    <?php
                        $DataInput = "
                                <input type='text' id='zcode' class='zcode_listfb form-control' name='zcode' placeholder='"._l('cong_zcode')."' value='".(!empty($data->zcode) ? $data->zcode : '')."'>
                                <div class='mtop10'>
                                    <button type='submit' class='btn btn-icon btn-info'> "._l('submit')."</button>
                                    <button type='button' class='btn btn-icon btn-default'>"._l('close')."</button>
                                </div>";
                    ?>
                    <a data-toggle="popover" class="pull-right" title="<?=_l('edit').' '._l('cong_zcode')?>" data-placement="left" data-html="true" data-content="<?=$DataInput?>">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                </div>


                <div class="wap-content second">
                    <span class="text-muted lead-field-heading no-mtop">
                        <?=_l('cong_note')?>:
                    </span>
                    <span class="bold font-medium-xs mbot15">
                            <?=!empty($data->description) ? $data->description : '-'?>
                    </span>
                    <?php
                        $DataInput = "
                                <textarea class='note_profile form-control' id='note_profile' name='note' placeholder='"._l('cong_note')."'>".(!empty($data->description) ? $data->description : '')."</textarea>
                                <div class='mtop10'>
                                    <button type='submit' class='btn btn-icon btn-info'> "._l('submit')."</button>
                                    <button type='button' class='btn btn-icon btn-default'>"._l('close')."</button>
                                </div>";
                    ?>
                    <a data-toggle="popover" class="pull-right" title="<?=_l('edit').' '._l('cong_note')?>" data-placement="left" data-html="true" data-content="<?=$DataInput?>">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="tag" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
            <input type="text" class="tagstype" id="tag" name="tag" value="<?=!empty($data->id) ? GetDataTag($data->id, 'listfb') : ''?>" data-role="tagstype">
        </div>
    </div>
</form>
<?php
if(!empty($data->id))
{
    $staff_assigned = getAssignedListFb($data->id);
}
?>
<script>
    $(function() {
        InitTagMessage();
        init_datepicker();
        appValidateForm($('#form_action_client'), {
            phonenumber: 'required',
            address: 'required',
            email: 'required',
            id_facebook: 'required'
        }, manageAction_client);
        function manageAction_client(form) {
            var button = $('#form_action_client').find('button[type="submit"]');
            button.button({loadingText: "<i class='fa fa-spinner fa-spin'></i>"});
            button.button('loading');
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function(response) {
                response = JSON.parse(response);
                alert_float(response.alert_type, response.message);
                if (response.success == true) {
                    $('#form_action_client').find('.action_profile').addClass('hide');
                    $('#form_action_client').find('#update_profile').removeClass('hide');
                    var id_facebook = $('#id_facebook').val();
                    varInfoUser(id_facebook);
                }
            }).always(function() {
                button.button('reset');
            });
            return false;
        }
    })


</script>

<script>
    $(function() {
        $('.profile_staff_assigned').addClass('hide');
        $('#browsers_staff_assigned').selectpicker('val',[]);
        <?php
        if(!empty($data)){
            if(!empty($staff_assigned->list_staff)){?>
                $('#browsers_staff_assigned').selectpicker('val', [<?=$staff_assigned->list_staff?>]);
            <?php } ?>
            $('.profile_staff_assigned').removeClass('hide');
        <?php } ?>
    })
    $('body').on('click','.ClosePopover', function(e){
        var id = $(this).parents('.popover').attr('id');
        $('body').find('a[aria-describedby="'+id+'"]').click();
    })
</script>