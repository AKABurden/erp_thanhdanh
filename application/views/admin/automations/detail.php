<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .uppercase {
        text-transform: uppercase;
        border-bottom: 1px solid #cecece;
    }

    .bold {
        font-weight: bold;
    }

    .red {
        color: red;
    }

    .notification-pad {
        padding: 15px;
        background: #ffc379;
        margin-top: 15px;
    }

    .pd20 {
        padding: 20px;
    }

    .border-ds {
        border: 1px dashed #383232 !important;
        cursor: pointer;
        padding-left: 0px
    }
    .border-dt {
        border: 1px solid #383232 !important;
        cursor: pointer;
        padding-left: 0px
    }

    .add-action:hover {
        opacity: 1 !important;
    }

    .mborder {
        border-bottom: 1px solid #d0d0d0 !important;
    }

    .pborder {
        border: 1px solid #d0d0d0 !important;
    }

    .font-40 {
        font-size: 40px;
    }

    .btn-white {
        background-color: white;
        font-size: 30px;
        margin-left: 12px !important;
    }
    .btn-bg-white {
        background-color: white;
        font-size: 14px;
    }
    .btn-white:first-child {
        margin-left: 0px !important;
    }

    td.vertical_middle {
        vertical-align: middle !important;
    }

    button.btn.btn-white:hover {
        border: 1px solid black;
    }


    .border-padding {
        border: 1px solid black;
        padding: 15px;
    }
    .bg-white
    {
        background-color: white;
    }
    .padding-top20{
        padding-top: 20px;
    }
    .delete_wall{
        margin-top: -15px;
        padding-right: 0px;
    }
</style>
<div id="wrapper" class="customer_profile">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <?php echo form_open($this->uri->uri_string(), array('class' => 'automations-form', 'autocomplete' => 'off')); ?>
                    <div class="panel-body">
                        <div class="uppercase bold mbot20">
                            <?= _l('cong_infomation_automations') ?>
                        </div>
                        <div class="col-md-6">
                            <?php $value = !empty($automations) ? $automations->name : ''; ?>
                            <?php echo render_input('name', 'cong_name_automations', $value); ?>

                            <div class="checkbox checkbox-primary">
                                <?php $checked = (!empty($automations) && $automations->status == 2) ? 'checked' : ''; ?>
                                <input type="checkbox" id="status" class="is_primary" name="status" value="2" <?= $checked ?>>
                                <label for="status" data-toggle="tooltip"><?=_l('cong_activing')?></label>
                            </div>

                            <div class="checkbox checkbox-primary hide">
                                <input type="checkbox" id="run_now" class="is_primary" name="run_now" value="1">
                                <label for="run_now" data-toggle="tooltip"><?=_l('cong_run_now')?></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php
                            $value = !empty($automations) ? $automations->note : '';
                            echo render_textarea('note', 'cong_note', $value, array('rows' => 7));
                            ?>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="active"><a data-toggle="tab" href="#automation_action"><?=_l('cong_object')?></a></li>
                                <li><a data-toggle="tab" href="#automation_time"><?=_l('cong_time')?></a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="automation_action" class="tab-pane fade in active">
                                    <div class="uppercase bold mbot20">
                                        <?= _l('cong_object') ?>
                                    </div>
                                    <?php
                                        $array_action = GetActionAutomation();
                                        $value = !empty($automations) ? $automations->action : '';
                                        echo render_select('action', $array_action, array('id', 'name'),'cong_object', $value);
                                    ?>
                                    <div>
                                        <div id="theme-action">
                                            <?php
                                                $Cinit = 0;
                                                if(!empty($automations->where))
                                                {
                                                    foreach($automations->where as $key => $value)
                                                    {

                                                        $value['Cinit'] = $key;
                                                        $Cinit = ($key+1);
                                                        if($automations->action == 1)
                                                        {
                                                            $value['province'] = !empty($province) ? $province : array();
                                                            $value['group_customer'] = !empty($group_customer) ? $group_customer : array();
                                                            $this->load->view('admin/automations/html/condition_client', $value);
                                                        }
                                                        else if($automations->action == 2)
                                                        {
                                                            $value['leads_sources'] = !empty($leads_sources) ? $leads_sources : array();
                                                            $value['leads_status'] = !empty($leads_status) ? $leads_status : array();
                                                            $value['province'] = !empty($province) ? $province : array();
                                                            $this->load->view('admin/automations/html/condition_lead', $value);
                                                        }
                                                    }
                                                }
                                            ?>
                                        </div>
                                        <div class="border-ds text-center font-40">
                                            <a class="btn btn-bg-white" id="AddCondition"><i class="fa fa-plus" aria-hidden="true"></i> <b><?=_l('cong_dk_add')?></b></a>
                                        </div>
                                    </div>
                                </div>
                                <div id="automation_time" class="tab-pane fade">
                                    <div class="uppercase bold mbot20">
                                        <?= _l('cong_time_action') ?>
                                    </div>
                                    <?php
                                    $week = array();
                                    $day = array();
                                    $time = '';
                                    $disabled_week = '';
                                    $disabled_day = '';
                                    $disabled_time = '';
                                    //Lấy danh sách active của tuần ngày và tháng
                                    //Những class ko chọn đc active disabled
                                    if (!empty($automations->proviso)) {
                                        foreach ($automations->proviso as $key => $value) {
                                            if ($value['type'] == 1) {
                                                $day[$value['day']] = $value['day'];
                                                $disabled_week = 'disabled';
                                                $disabled_time = 'disabled';
                                            } else if ($value['type'] == 2) {
                                                $week[$value['week']] = $value['week'];
                                                $disabled_day = 'disabled';
                                                $disabled_time = 'disabled';
                                            } else {
                                                $time = $value['time'];
                                                $disabled_day = 'disabled';
                                                $disabled_week = 'disabled';
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="col-md-12">
                                        <div class="form-group" app-field-wrapper="day">
                                            <label for="day" class="control-label"><?= _l('cong_day_of_month'); ?></label>
                                            <select id="day" name="day[]" class="selectpicker" multiple="true" data-width="100%" <?=$disabled_day?> data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                <option></option>
                                                <?php for ($i = 0; $i < 31; $i++) {
                                                    echo "<option " . (!empty($day[$i + 1]) ? 'selected' : '') . " value='" . ($i + 1) . "'>" . ($i + 1) . "</option>";
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2"><p class="mtop10"><?= _l('cong_week') ?></p></div>
                                    <div class="col-md-1">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="day_2rd" class="is_primary check_week" name="week[2]" <?=$disabled_week?> value="2" <?= !empty($week[2]) ? 'checked' : '' ?>>
                                            <label for="day_2rd" data-toggle="tooltip"><?= _l('cong_t2') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="day_3rd" class="is_primary check_week" name="week[3]" <?=$disabled_week?> value="3" <?= !empty($week[3]) ? 'checked' : '' ?>>
                                            <label for="day_3rd" data-toggle="tooltip"><?= _l('cong_t3') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="day_4rd" class="is_primary check_week" name="week[4]" <?=$disabled_week?> value="4" <?= !empty($week[4]) ? 'checked' : '' ?>>
                                            <label for="day_4rd" data-toggle="tooltip"><?= _l('cong_t4') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="day_5rd" class="is_primary check_week" name="week[5]" <?=$disabled_week?> value="5" <?= !empty($week[5]) ? 'checked' : '' ?>>
                                            <label for="day_5rd" data-toggle="tooltip"><?= _l('cong_t5') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="day_6rd" class="is_primary check_week" name="week[6]" <?=$disabled_week?> value="6" <?= !empty($week[6]) ? 'checked' : '' ?>>
                                            <label for="day_6rd" data-toggle="tooltip"><?= _l('cong_t6') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="day_7rd" class="is_primary check_week" name="week[7]" <?=$disabled_week?> value="7" <?= !empty($week[7]) ? 'checked' : '' ?>>
                                            <label for="day_7rd" data-toggle="tooltip"><?= _l('cong_t7') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="checkbox checkbox-primary">
                                            <!--Chủ nhật -->
                                            <input type="checkbox" id="day_1rd" class="is_primary check_week" name="week[1]" <?=$disabled_week?> value="1" <?= !empty($week[1]) ? 'checked' : '' ?>>
                                            <label for="day_1rd" data-toggle="tooltip"><?= _l('cong_cn') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="day_all" class="is_primary check_week" <?=$disabled_week?> value="1" <?= count($week) == 7 ? 'checked' : '' ?>>
                                            <label for="day_all" data-toggle="tooltip"><?= _l('all_week') ?></label>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-3">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="check_time" class="is_primary" name="check_time" value="1" <?= !empty($time) ? 'checked' : '' ?>>
                                            <label for="check_time" data-toggle="tooltip"><?= _l('cong_time') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <?php
                                        $value = !empty($time) ? _dt($time) : '';
                                        $array_time = array();
                                        if(!empty($disabled_time))
                                        {
                                            $array_time =  array($disabled_time => true);
                                        }
                                        echo render_datetime_input('time', '', $value, $array_time);
                                        ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="uppercase bold mbot20">
                                <?= _l('cong_then') ?>
                            </div>
                            <div class="col-md-12 border-ds">
                                <div class="btn-group mr-2" role="group">
                                    <button type="button" class="btn btn-white" id="send-infomation-staff" title="<?=_l('send_infomation_staff');?>" data-toggle="tooltip" data-original-title="<?=_l('send_infomation_staff');?>">
                                        <i class="fa fa-bullhorn"></i>
                                    </button>
                                    <button type="button" class="btn btn-white" id="send-email-staff-client" title="<?=_l('send_email_staff_client');?>" data-toggle="tooltip" data-original-title="<?=_l('send_email_staff_client');?>">
                                        <i class="fa fa-envelope"></i>
                                    </button>
                                    <button type="button" class="btn btn-white" id="create-tasks-auto" title="<?=_l('create_tasks_auto');?>" data-toggle="tooltip" data-original-title="<?=_l('send_email_staff_client');?>">
                                        <i class="lnr lnr-wheelchair menu-icon"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <table class="table table_information">
                                <tbody>
                                <?php
                                    if(!empty($automations->detail))
                                    {
                                        foreach($automations->detail as $i => $value){?>
                                            <tr class="TrInfomation_<?=$i?> pointer" id_data="infomation_<?=$i?>" onclick="View_infomation_staff(<?=$i?>)">
                                                <td class="vertical_middle">
                                                    <?php
                                                        if($value['type'] == 1)
                                                        {
                                                            echo _l('send_infomation_staff');
                                                        }
                                                        else if($value['type'] == 2)
                                                        {
                                                            echo _l('send_email_staff_client');
                                                        }
                                                        else if($value['type'] == 3)
                                                        {
                                                            echo _l('create_tasks_auto');
                                                        }
                                                    ?>
                                                </td>
                                                <td class="vertical_middle text-center"><a class="removeTr pointer text-danger">X</a></td>
                                            </tr>
                                        <?php
                                        }
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div id="modal_toggle" class="tab-content mtop30" style="margin-bottom: 50px;">

                                <?php $i = 0;?>
                                <?php
                                    if(!empty($automations->detail))
                                    {
                                        foreach($automations->detail as $key => $value){
                                            $value['unit'] = $key;
                                            ++$i;
                                            if($value['type'] == 1)
                                            {
                                                $value['staff'] = $staff;
                                                $this->load->view('admin/automations/modal_toggle/create_infomation', $value);
                                            }
                                            else if($value['type'] == 2)
                                            {
                                                $value['staff'] = $staff;
                                                $this->load->view('admin/automations/modal_toggle/send_email_client', $value);
                                            }
                                            else if($value['type'] == 3)
                                            {
                                                $value['staff'] = $staff;
                                                $this->load->view('admin/automations/modal_toggle/create_tasks', $value);
                                            }
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                            <button class="btn btn-info" type="submit"><?=_l('submit')?></button>
                            <button class="btn btn-info subrun" type="submit"><?=_l('cong_submit_and_run')?></button>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>

    $(function () {
        var vRules = {};
        vRules = {
            name: 'required',
            action: 'required'
        };
        appValidateForm($('.automations-form'), vRules);
        $('body').on('click', '.subrun', function(e){
            $('#run_now').prop('checked', true);
        })

        $( ".automations-form" ).submit(function() {
            var button = $(this).find('button[type="submit"]');
            button.button({loadingText: 'please wait...'});
            button.button('loading');
            $('#loader').removeClass('hide');
        })

    })
</script>
<?php include_once APPPATH."views/admin/automations/automation_js.php";?>
</body>
</html>
