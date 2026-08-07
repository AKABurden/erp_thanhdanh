<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row" style="padding-bottom: 50px;">
            <?php if (isset($member)) { ?>
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body no-padding-bottom">
                            <?php $this->load->view('admin/staff/stats'); ?>
                        </div>
                    </div>
                </div>
                <div class="member">
                    <?php echo form_hidden('isedit'); ?>
                    <?php echo form_hidden('memberid', $member->staffid); ?>
                </div>
            <?php } ?>
            <?php if (isset($member)) { ?>

                <div class="col-md-12">
                    <?php if (total_rows(db_prefix() . 'departments', array('email' => $member->email)) > 0) { ?>
                        <div class="alert alert-danger">
                            The staff member email exists also as support department email, according to the docs, the
                            support department email must be unique email in the system, you must change the staff email
                            or the support department email in order all the features to work properly.
                        </div>
                    <?php } ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="no-margin"><?php echo $member->firstname . ' ' . $member->lastname; ?>
                                <?php if ($member->last_activity && $member->staffid != get_staff_user_id()) { ?>
                                    <small> - <?php echo _l('last_active'); ?>:
                                        <span class="text-has-action" data-toggle="tooltip"
                                            data-title="<?php echo _dt($member->last_activity); ?>">
                                            <?php echo time_ago($member->last_activity); ?>
                                        </span>
                                    </small>
                                <?php } ?>
                                <a href="#" onclick="small_table_full_view(); return false;" data-placement="left"
                                    data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>"
                                    class="toggle_view pull-right">
                                    <i class="fa fa-expand"></i></a>
                            </h4>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php echo form_open_multipart(
                $this->uri->uri_string(),
                array('class' => 'staff-form', 'autocomplete' => 'off')
            ); ?>
            <!-- col-md-offset-2 -->
            <div class="col-md-12 <?php if (!isset($member)) {
                                        echo '8';
                                    } else {
                                        echo '5';
                                    } ?>" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tab_staff_profile" aria-controls="tab_staff_profile" role="tab"
                                    data-toggle="tab">
                                    <?php echo _l('staff_profile_string'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#staff_permissions" aria-controls="staff_permissions" role="tab"
                                    data-toggle="tab">
                                    <?php echo _l('staff_add_edit_permissions'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="<?= (get_option('dahahi') == 1 ? '' : 'hide') ?>">
                                <a href="#dahahi" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('ch_dahahi') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane " id="dahahi">
                                <table class="table-personnel">
                                    <tr>
                                        <td colspan="5" class="text-primary bg-primary bold"><?= lang('Thông tin liên kết máy chấm công') ?></td>
                                    </tr>
                                </table>
                                <table class="table-personnel">
                                    <?php
                                                    $checkdahahi = 0;

                                    if (empty($getStaff['FaceID']) && $MachineBoxId['result'] != 2) { ?>
                                        <tr>
                                            <td style="width: 10%;"><?= lang('Mã FaceID') ?></td>
                                            <td style="width: 20%;" class="bold"><?= ((!empty($getStaff['FaceID'])) ? ($MachineBoxId['result'] == 2 ? $getStaff['FaceID'] : $MachineBoxId['message']) : 'Chưa liên kết') ?></td>
                                            <td style="width: 10%;"><?= lang('ID Thiết bị') ?></td>
                                            <td style="width: 20%;" class="bold"><?= ((!empty($getStaff['MachineBoxId'])) ? ($MachineBoxId['result'] == 2 ? $getStaff['MachineBoxId'] : $MachineBoxId['message']) : 'Chưa liên kết') ?></td>
                                            <td style="width: 10%;"><?= lang('Ảnh FaceID') ?></td>
                                            <td style="width: 20%;" class="bold">
                                                Chưa liên kết
                                            </td>
                                        </tr>
                                    <?php } else {
                                    ?>
                                        <?php foreach ($check_dahahi as $key => $value) { ?>
                                            <tr>
                                                <td style="width: 10%;"><?= lang('Mã FaceID') ?></td>
                                                <td style="width: 20%;" class="bold"><?= ((!empty($getStaff['FaceID'])) ? ($value['result'] == 2 ? $getStaff['FaceID'] : $value['message']) : 'Chưa liên kết') ?></td>
                                                <td style="width: 10%;"><?= lang('ID Thiết bị') ?></td>
                                                <td style="width: 20%;" class="bold"><?= ((!empty($getStaff['MachineBoxId'])) ? ($value['result'] == 2 ? $value['data']['MachineBoxId'] : $value['message']) : 'Chưa liên kết') ?></td>
                                                <td style="width: 10%;"><?= lang('Ảnh FaceID') ?></td>
                                                <td style="width: 20%;" class="bold">
                                                    <?php
                                                    $checkdahahi = 0;
                                                    if (!empty($getStaff['FaceID']) && ($value['result'] == 2)) {
                                                        $checkdahahi = 1;
                                                    ?>
                                                        <?php
                                                        $images = $value['data']['Base64Image'];
                                                        if (empty($images)) {
                                                            $images = base_url('assets/images/tnh/default-avatar-male.png');
                                                        }
                                                        ?>
                                                        <div class="preview_image" style="width: auto;">
                                                            <div class="display-block contract-attachment-wrapper img">
                                                                <div style="width:100px; margin: auto;">
                                                                    <a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                        <div class="">
                                                                            <img src="<?= $images ?>" />
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } else { ?>
                                                        Chưa liên kết
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                    <tr>
                                        <?php
                                        if (!empty($getStaff['FaceID']) && ($MachineBoxId['result'] == 2)) {
                                        ?>
                                            <td><a class="btn btn-info tnh-modal" href="<?= base_url('admin/staff/timekeeping/' . $getStaff['staffid'] . '/' . $checkdahahi) ?>" style="margin-top: 27px;"> Sửa kết với thiết bị chấm công</a></td>
                                        <?php } else { ?>
                                            <td><a class="btn btn-info tnh-modal" href="<?= base_url('admin/staff/timekeeping/' . $getStaff['staffid'] . '/' . $checkdahahi) ?>" style="margin-top: 27px;">Liên kết với thiết bị chấm công</a></td>
                                        <?php } ?>
                                    </tr>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane active" id="tab_staff_profile">
                                <div class="row">
                                    <div class="col-md-3" style="border-right: 1px solid #bfcbd9;">
                                        <?php if (total_rows(
                                            db_prefix() . 'emailtemplates',
                                            array('slug' => 'two-factor-authentication', 'active' => 0)
                                        ) == 0) { ?>
                                            <!-- <div class="checkbox checkbox-primary">
                                        <input type="checkbox" value="1" name="two_factor_auth_enabled"
                                               id="two_factor_auth_enabled"<?php if (isset($member) && $member->two_factor_auth_enabled == 1) {
                                                                                echo ' checked';
                                                                            } ?>>
                                        <label for="two_factor_auth_enabled"><i class="fa fa-question-circle"
                                                                                data-toggle="tooltip"
                                                                                data-title="<?php echo _l('two_factor_authentication_info'); ?>"></i>
                                            <?php echo _l('enable_two_factor_authentication'); ?></label>
                                    </div> -->
                                        <?php } ?>
                                        <div class="hide is-not-staff<?php if (isset($member) && $member->admin == 1) {
                                                                            echo ' hide';
                                                                        } ?>">
                                            <div class="checkbox checkbox-primary">
                                                <?php
                                                $checked = '';
                                                if (isset($member)) {
                                                    if ($member->is_not_staff == 1) {
                                                        $checked = ' checked';
                                                    }
                                                }
                                                ?>
                                                <input type="checkbox" value="1" name="is_not_staff"
                                                    id="is_not_staff" <?php echo $checked; ?>>
                                                <label for="is_not_staff"><?php echo _l('is_not_staff_member'); ?></label>
                                            </div>
                                            <hr />
                                        </div>
                                        <?php if ((isset($member) && $member->profile_image == null) || !isset($member)) { ?>
                                            <div class="form-group">
                                                <label for="profile_image"
                                                    class="profile-image"><?php echo _l('staff_edit_profile_image'); ?></label>
                                                <input type="file" name="profile_image" class="form-control"
                                                    id="profile_image">
                                            </div>
                                        <?php } ?>
                                        <?php if (isset($member) && $member->profile_image != null) { ?>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <?php echo staff_profile_image(
                                                            $member->staffid,
                                                            array('img', 'img-responsive', 'staff-profile-image-thumb'),
                                                            'thumb'
                                                        ); ?>
                                                    </div>
                                                    <div class="col-md-3 text-right">
                                                        <a href="<?php echo admin_url('staff/remove_staff_profile_image/' . $member->staffid); ?>"><i
                                                                class="fa fa-remove"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php $value = (isset($member) ? $member->firstname : ''); ?>
                                        <?php $attrs = (isset($member) ? array() : array('autofocus' => true)); ?>
                                        <?php echo render_input('firstname', 'Họ', $value, 'text', $attrs); ?>
                                        <?php $value = (isset($member) ? $member->lastname : ''); ?>
                                        <?php echo render_input('lastname', 'Tên', $value); ?>
                                        <?php $value = (isset($member) ? $member->code : ''); ?>
                                        <?php echo render_input('code', 'code_staff', $value); ?>
                                        <?php $value = (isset($member) ? $member->email : ''); ?>
                                        <?php echo render_input(
                                            'email',
                                            'staff_add_edit_email',
                                            $value,
                                            'email',
                                            array('autocomplete' => 'off')
                                        ); ?>
                                        <?php $value = (isset($member) ? _d($member->birthday) : ''); ?>
                                        <?php echo render_date_input('birthday', 'cong_day_birtday', $value); ?>
                                        <div class="form-group hide">
                                            <label for="salary"><?php echo _l('staff_salary'); ?></label>
                                            <div class="input-group">
                                                <input type="text" name="salary" value="<?php if (isset($member)) {
                                                                                            echo $member->salary;
                                                                                        } else {
                                                                                            echo '';
                                                                                        } ?>" id="salary" class="form-control">
                                                <span class="input-group-addon">
                                                    <?php echo $base_currency->symbol; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <?php $value = (isset($member) ? $member->phonenumber : ''); ?>
                                        <?php echo render_input('phonenumber', 'staff_add_edit_phonenumber', $value); ?>
                                        <?php $role_level_id = (isset($member) ? $member->role_level_id : ''); ?>
                                        <div class="form-group">
                                            <?= lang('Cấp bậc vai trò', 'role_level_id') ?>
                                            <select name="role_level_id" id="role_level_id"
                                                    data-placeholder="<?= lang('Cấp bậc vai trò') ?>"
                                                    class="role_level_id" style="width: 100%;">
                                                <option value=""></option>
                                                <?php if(!empty($dtRoleLevel)){?>
                                                    <?php foreach ($dtRoleLevel as $key => $value) { ?>
                                                            <option <?= $role_level_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php } ?>    
                                                <?php } ?>    
                                            </select>
                                        </div>
                                        <?php $setup_shift_id = (isset($member) ? $member->setup_shift_id : ''); ?>
                                        <div class="form-group">
                                            <?= lang('Ca làm việc', 'setup_shift_id') ?>
                                            <select name="setup_shift_id" id="setup_shift_id"
                                                    data-placeholder="<?= lang('Ca làm việc') ?>"
                                                    class="setup_shift_id" style="width: 100%;">
                                                <option value=""></option>
                                                <?php if(!empty($dtSetupShift)){?>
                                                    <?php foreach ($dtSetupShift as $key => $value) { ?>
                                                        <option <?= $setup_shift_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="facebook" class="control-label"><i
                                                    class="fa fa-facebook"></i> <?php echo _l('staff_add_edit_facebook'); ?>
                                            </label>
                                            <input type="text" class="form-control" name="facebook"
                                                value="<?php if (isset($member)) {
                                                            echo $member->facebook;
                                                        } ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="linkedin" class="control-label"><i
                                                    class="fa fa-linkedin"></i> <?php echo _l('staff_add_edit_linkedin'); ?>
                                            </label>
                                            <input type="text" class="form-control" name="linkedin"
                                                value="<?php if (isset($member)) {
                                                            echo $member->linkedin;
                                                        } ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="skype" class="control-label"><i
                                                    class="fa fa-skype"></i> <?php echo _l('staff_add_edit_skype'); ?>
                                            </label>
                                            <input type="text" class="form-control" name="skype"
                                                value="<?php if (isset($member)) {
                                                            echo $member->skype;
                                                        } ?>">
                                        </div>
                                        <?php if (get_option('disable_language') == 0) { ?>
                                            <div class="form-group select-placeholder">
                                                <label for="default_language"
                                                    class="control-label"><?php echo _l('localization_default_language'); ?></label>
                                                <select name="default_language" data-live-search="true"
                                                    id="default_language" class="form-control selectpicker"
                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value=""><?php echo _l('system_default_string'); ?></option>
                                                    <?php foreach ($this->app->get_available_languages() as $language) {
                                                        $selected = '';
                                                        if (isset($member)) {
                                                            if ($member->default_language == $language) {
                                                                $selected = 'selected';
                                                            }
                                                        }
                                                    ?>
                                                        <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        <?php } ?>
                                        <i class="fa fa-question-circle pull-left" data-toggle="tooltip"
                                            data-title="<?php echo _l('staff_email_signature_help'); ?>"></i>
                                        <?php $value = (isset($member) ? $member->email_signature : ''); ?>
                                        <?php echo render_textarea(
                                            'email_signature',
                                            'settings_email_signature',
                                            $value,
                                            ['data-entities-encode' => 'true']
                                        ); ?>
                                        <div class="form-group select-placeholder">
                                            <label for="direction"><?php echo _l('document_direction'); ?></label>
                                            <select class="selectpicker"
                                                data-none-selected-text="<?php echo _l('system_default_string'); ?>"
                                                data-width="100%" name="direction" id="direction">
                                                <option value="" <?php if (isset($member) && empty($member->direction)) {
                                                                        echo 'selected';
                                                                    } ?>></option>
                                                <option value="ltr" <?php if (isset($member) && $member->direction == 'ltr') {
                                                                        echo 'selected';
                                                                    } ?>>LTR
                                                </option>
                                                <option value="rtl" <?php if (isset($member) && $member->direction == 'rtl') {
                                                                        echo 'selected';
                                                                    } ?>>RTL
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <?php if (count($departments) > 0) { ?>
                                                <label for="departments"><?php echo _l('staff_add_edit_departments'); ?></label>
                                            <?php } ?>
                                            <?php foreach ($departments as $department) { ?>
                                                <?php
                                                $checked = '';
                                                if (isset($member)) {
                                                    foreach ($staff_departments as $staff_department) {
                                                        if ($staff_department['departmentid'] == $department['departmentid']) {
                                                            $checked = ' checked';
                                                        }
                                                    }
                                                }

                                                if (!$checked && !$department['active_departments']) {
                                                    continue;
                                                }
                                                ?>
                                                <div class="checkbox checkbox-primary">
                                                    <input type="checkbox"
                                                        id="dep_<?php echo $department['departmentid']; ?>"
                                                        name="departments[]"
                                                        value="<?php echo $department['departmentid']; ?>" <?php echo $checked; ?>>
                                                    <label for="dep_<?php echo $department['departmentid']; ?>"><?php echo $department['name']; ?></label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <?php $rel_id = (isset($member) ? $member->staffid : false); ?>
                                        <?php echo render_custom_fields('staff', $rel_id); ?>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <hr class="hr-10" />
                                                <?php if (is_admin()) { ?>
                                                    <div class="checkbox checkbox-primary">
                                                        <?php
                                                        $isadmin = '';
                                                        if (isset($member) && ($member->staffid == get_staff_user_id() || is_admin($member->staffid))) {
                                                            $isadmin = ' checked';
                                                        }
                                                        ?>
                                                        <input type="checkbox" name="administrator"
                                                            id="administrator" <?php echo $isadmin; ?>>
                                                        <label for="administrator"><?php echo _l('staff_add_edit_administrator'); ?></label>
                                                    </div>
                                                <?php } ?>
                                                <?php if (!isset($member) && total_rows(
                                                    db_prefix() . 'emailtemplates',
                                                    array('slug' => 'new-staff-created', 'active' => 0)
                                                ) === 0) { ?>
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="send_welcome_email"
                                                            id="send_welcome_email" checked>
                                                        <label for="send_welcome_email"><?php echo _l('staff_send_welcome_email'); ?></label>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="row hide">
                                            <div class="col-md-12">
                                                <div class="checkbox checkbox-danger">
                                                    <?php
                                                    $is_internal_proposal_selected = !empty($member->is_internal_proposal) ? 'checked' : '';
                                                    ?>
                                                    <input type="checkbox" id="is_internal_proposal"
                                                        name="is_internal_proposal"
                                                        <?= $is_internal_proposal_selected ?> value="1">
                                                    <label for="is_internal_proposal"><?= lang('Admin chỉ thấy đề xuất nội bộ chưa duyệt hoặc mình đã duyệt') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (!isset($member) || is_admin() || !is_admin() && $member->admin == 0) { ?>
                                            <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                                            <input type="text" class="fake-autofill-field" name="fakeusernameremembered"
                                                value='' tabindex="-1" />
                                            <input type="password" class="fake-autofill-field"
                                                name="fakepasswordremembered" value='' tabindex="-1" />
                                            <div class="clearfix form-group"></div>
                                            <label for="password"
                                                class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control password" name="password"
                                                    autocomplete="off">
                                                <span class="input-group-addon">
                                                    <a href="#password" class="show_password"
                                                        onclick="showPassword('password'); return false;"><i
                                                            class="fa fa-eye"></i></a>
                                                </span>
                                                <span class="input-group-addon">
                                                    <a href="#" class="generate_password"
                                                        onclick="generatePassword(this);return false;"><i
                                                            class="fa fa-refresh"></i></a>
                                                </span>
                                            </div>
                                            <?php if (isset($member)) { ?>
                                                <p class="text-muted"><?php echo _l('staff_add_edit_password_note'); ?></p>
                                                <?php if ($member->last_password_change != null) { ?>
                                                    <?php echo _l('staff_add_edit_password_last_changed'); ?>:
                                                    <span class="text-has-action" data-toggle="tooltip"
                                                        data-title="<?php echo _dt($member->last_password_change); ?>">
                                                        <?php echo time_ago($member->last_password_change); ?>
                                                    </span>
                                            <?php }
                                            } ?>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-9 small-table-right-col">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_gender', 'gender') ?>
                                                    <select name="gender" id="gender"
                                                        data-placeholder="<?= lang('tnh_gender') ?>" class="gender"
                                                        style="width: 100%;">
                                                        <option value=""></option>
                                                        <option
                                                            <?= !empty($member->gender) && $member->gender == "male" ? 'selected' : '' ?>
                                                            value="male"><?= lang('tnh_male') ?></option>
                                                        <option
                                                            <?= !empty($member->gender) && $member->gender == "female" ? 'selected' : '' ?>
                                                            value="female"><?= lang('tnh_female') ?></option>
                                                        <option
                                                            <?= !empty($member->gender) && $member->gender == "other" ? 'selected' : '' ?>
                                                            value="other"><?= lang('tnh_other') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_birthplace', 'birthplace') ?>
                                                    <input type="text" name="birthplace" id="birthplace"
                                                        placeholder="<?= lang('tnh_birthplace') ?>"
                                                        class="form-control"
                                                        value="<?= !empty($member->birthplace) ? $member->birthplace : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_domicile', 'domicile') ?>
                                                    <input type="text" name="domicile" id="domicile"
                                                        placeholder="<?= lang('tnh_domicile') ?>"
                                                        class="form-control"
                                                        value="<?= !empty($member->domicile) ? $member->domicile : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_cmnd_id_passport', 'cmnd_id_passport') ?>
                                                    <input type="text" name="cmnd_id_passport" id="cmnd_id_passport"
                                                        placeholder="<?= lang('tnh_cmnd_id_passport') ?>"
                                                        class="form-control"
                                                        value="<?= !empty($member->cmnd_id_passport) ? $member->cmnd_id_passport : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_date_range', 'date_range') ?>
                                                    <?= form_input(
                                                        'date_range',
                                                        (!empty($member->date_range) ? _d($member->date_range) : ''),
                                                        'id="date_range" class="form-control datepicker" placeholder="' . lang('dd/mm/yyyy') . '"'
                                                    ) ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_issued_by', 'issued_by') ?>
                                                    <input type="text" name="issued_by" id="issued_by"
                                                        placeholder="<?= lang('tnh_issued_by') ?>"
                                                        class="form-control"
                                                        value="<?= !empty($member->issued_by) ? $member->issued_by : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_marital_status', 'marital_status') ?>
                                                    <select name="marital_status" id="marital_status"
                                                        data-placeholder="<?= lang('tnh_marital_status') ?>"
                                                        class="marital_status" style="width: 100%;">
                                                        <option value=""></option>
                                                        <option
                                                            <?= !empty($member->marital_status) && $member->marital_status == "alone" ? 'selected' : '' ?>
                                                            value="alone"><?= lang('tnh_alone') ?></option>
                                                        <option
                                                            <?= !empty($member->marital_status) && $member->marital_status == "marriage" ? 'selected' : '' ?>
                                                            value="marriage"><?= lang('tnh_marriage') ?></option>
                                                        <option
                                                            <?= !empty($member->marital_status) && $member->marital_status == "divorce" ? 'selected' : '' ?>
                                                            value="divorce"><?= lang('tnh_divorce') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_nationality', 'nationality') ?>
                                                    <input type="text" name="nationality" id="nationality"
                                                        placeholder="<?= lang('tnh_nationality') ?>"
                                                        class="form-control nationality"
                                                        value="<?= !empty($member->nationality) ? $member->nationality : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_nation', 'nation') ?>
                                                    <input type="text" name="nation" id="nation"
                                                        placeholder="<?= lang('tnh_nation') ?>"
                                                        class="form-control nation"
                                                        value="<?= !empty($member->nation) ? $member->nation : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_account_name', 'account_name') ?>
                                                    <input type="text" name="account_name" id="account_name"
                                                        placeholder="<?= lang('tnh_account_name') ?>"
                                                        class="form-control account_name"
                                                        value="<?= !empty($member->account_name) ? $member->account_name : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_bank', 'bank') ?>
                                                    <input type="text" name="bank" id="bank"
                                                        placeholder="<?= lang('tnh_bank') ?>"
                                                        class="form-control bank"
                                                        value="<?= !empty($member->bank) ? $member->bank : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Chi nhánh ngân hàng', 'branch_bank') ?>
                                                    <input type="text" name="branch_bank" id="branch_bank"
                                                        placeholder="<?= lang('Chi nhánh ngân hàng') ?>"
                                                        class="form-control branch_bank"
                                                        value="<?= !empty($member->branch_bank) ? $member->branch_bank : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_personal_tax_code', 'personal_tax_code') ?>
                                                    <input type="text" name="personal_tax_code" id="personal_tax_code"
                                                        placeholder="<?= lang('tnh_personal_tax_code') ?>"
                                                        class="form-control personal_tax_code"
                                                        value="<?= !empty($member->personal_tax_code) ? $member->personal_tax_code : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_religion', 'religion') ?>
                                                    <input type="text" name="religion" id="religion"
                                                        placeholder="<?= lang('tnh_religion') ?>"
                                                        class="form-control religion"
                                                        value="<?= !empty($member->religion) ? $member->religion : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_resident', 'resident') ?>
                                                    <input type="text" name="resident" id="resident"
                                                        placeholder="<?= lang('tnh_resident') ?>"
                                                        class="form-control resident"
                                                        value="<?= !empty($member->resident) ? $member->resident : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_current_accommodation', 'current_accommodation') ?>
                                                    <input type="text" name="current_accommodation"
                                                        id="current_accommodation"
                                                        placeholder="<?= lang('tnh_current_accommodation') ?>"
                                                        class="form-control current_accommodation"
                                                        value="<?= !empty($member->current_accommodation) ? $member->current_accommodation : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Lương vị trí (LCB)', 'salary_bhxh') ?>
                                                    <input type="text" name="salary_bhxh" id="salary_bhxh"
                                                        class="form-control salary_bhxh number-format"
                                                        value="<?= !empty($member->salary_bhxh) ? formatMoney($member->salary_bhxh) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Lương cơ bản BHXH', 'salary_bhxh_new') ?>
                                                    <input type="text" name="salary_bhxh_new" id="salary_bhxh_new"
                                                        class="form-control salary_bhxh_new number-format"
                                                        value="<?= !empty($member->salary_bhxh_new) ? formatMoney($member->salary_bhxh_new) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4 hide">
                                                <div class="form-group">
                                                    <?= lang('Phụ cấp', 'allowance') ?>
                                                    <input type="text" name="allowance" id="allowance"
                                                        class="form-control allowance number-format"
                                                        value="<?= !empty($member->allowance) ? formatMoney($member->allowance) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Tham gia bảo hiểm', 'check_bhxh') ?>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="radio radio-primary">
                                                                <input type="radio" value="1" id="check_bhxh1"
                                                                    <?= !empty($member) ? ($member->check_bhxh == 1 ? 'checked' : '') : 'checked' ?>
                                                                    name="check_bhxh">
                                                                <label for="check_bhxh1">Có</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="radio radio-primary">
                                                                <input type="radio" value="0" id="check_bhxh2"
                                                                    <?= !empty($member) ? ($member->check_bhxh == 0 ? 'checked' : '') : '' ?>
                                                                    name="check_bhxh">
                                                                <label for="check_bhxh2">Không</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Ngày vào làm', 'day_in') ?>
                                                    <?= form_input(
                                                        'day_in',
                                                        (!empty($member->day_in) ? _d($member->day_in) : ''),
                                                        'id="day_in" class="form-control datepicker" placeholder="' . lang('dd/mm/yyyy') . '"'
                                                    ) ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Trạng thái làm việc', 'status_work') ?>
                                                    <select name="status_work" id="status_work" required
                                                        data-placeholder="<?= lang('Trạng thái làm việc') ?>"
                                                        class="status_work" style="width: 100%;">
                                                        <option value=""></option>
                                                        <option
                                                            <?= !empty($member->status_work) && $member->status_work == 0 ? 'selected' : '' ?>
                                                            value="0"><?= lang('Thử việc') ?></option>
                                                        <option
                                                            <?= !empty($member->status_work) && $member->status_work == 1 ? 'selected' : '' ?>
                                                            value="1"><?= lang('Đang làm việc') ?></option>
                                                        <option
                                                            <?= !empty($member->status_work) && $member->status_work == 2 ? 'selected' : '' ?>
                                                            value="2"><?= lang('Nghỉ việc') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Tham gia đoàn phí', 'check_union') ?>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="radio radio-primary">
                                                                <input type="radio" value="1" id="check_union1"
                                                                    <?= !empty($member) ? ($member->check_union == 1 ? 'checked' : '') : 'checked' ?>
                                                                    name="check_union">
                                                                <label for="check_union1">Có</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="radio radio-primary">
                                                                <input type="radio" value="0" id="check_union2"
                                                                    <?= !empty($member) ? ($member->check_union == 0 ? 'checked' : '') : '' ?>
                                                                    name="check_union">
                                                                <label for="check_union2">Không</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Tiền ứng lương', 'total_advance') ?>
                                                    <input type="text" name="total_advance" id="total_advance"
                                                        class="form-control total_advance number-format"
                                                        value="<?= !empty($member->total_advance) ? formatMoney($member->total_advance) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Hệ số lương chức vụ', 'coefficient_position') ?>
                                                    <input type="text" name="coefficient_position"
                                                        id="coefficient_position"
                                                        class="form-control coefficient_position number-format"
                                                        value="<?= !empty($member->coefficient_position) ? ($member->coefficient_position) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Hệ số lương chức vụ', 'coefficient_responsibility') ?>
                                                    <input type="text" name="coefficient_responsibility"
                                                        id="coefficient_responsibility"
                                                        class="form-control coefficient_responsibility number-format"
                                                        value="<?= !empty($member->coefficient_responsibility) ? ($member->coefficient_responsibility) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Doanh số', 'sales') ?>
                                                    <input type="text" name="sales" id="sales"
                                                        class="form-control sales number-format"
                                                        value="<?= !empty($member->sales) ? formatMoney($member->sales) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Tiền điện thoại', 'phone') ?>
                                                    <input type="text" name="phone" id="phone"
                                                        class="form-control phone number-format"
                                                        value="<?= !empty($member->phone) ? formatMoney($member->phone) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Xăng xe đi lại', 'gasonline_cars') ?>
                                                    <input type="text" name="gasonline_cars" id="gasonline_cars"
                                                        class="form-control gasonline_cars number-format"
                                                        value="<?= !empty($member->gasonline_cars) ? formatMoney($member->gasonline_cars) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Nhà trọ', 'motel') ?>
                                                    <input type="text" name="motel" id="motel"
                                                        class="form-control motel number-format"
                                                        value="<?= !empty($member->motel) ? formatMoney($member->motel) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Số người giảm trừ', 'number_reduce') ?>
                                                    <input type="text" name="number_reduce" id="number_reduce"
                                                        class="form-control number_reduce number-format"
                                                        value="<?= !empty($member->number_reduce) ? formatNumber($member->number_reduce) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Kiêm nhiệm', 'concurrently') ?>
                                                    <input type="text" name="concurrently" id="concurrently"
                                                        class="form-control concurrently number-format"
                                                        value="<?= !empty($member->concurrently) ? formatMoney($member->concurrently) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Công tác phí', 'business_fee_staff') ?>
                                                    <input type="text" name="business_fee_staff" id="business_fee_staff"
                                                        class="form-control business_fee_staff number-format"
                                                        value="<?= !empty($member->business_fee_staff) ? formatMoney($member->business_fee_staff) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Thâm niên', 'seniority') ?>
                                                    <input type="text" name="seniority" id="seniority"
                                                        class="form-control seniority number-format"
                                                        value="<?= !empty($member->seniority) ? formatMoney($member->seniority) : 0 ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('Ngày tính độc hại', 'date_dochai') ?>
                                                    <input type="text" name="date_dochai" id="date_dochai"
                                                        class="form-control date_dochai datepicker"
                                                        value="<?= !empty($member->date_dochai) ? _dhau($member->date_dochai) : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 class="panel-title"
                                                    style="cursor: pointer; color: #fff;background-color: #337ab7;border-color: #337ab7; padding: 5px;"
                                                    data-toggle="collapse"
                                                    href="#collapse-family"><?= lang('Thông tin lương') ?></h3>
                                                <table id="tb-salary-new"
                                                    class="dt-tnh table tnh-table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 50px;">
                                                                <a class="btn btn-info btn-icon add-row-salary-new"><i
                                                                        class="fa fa-plus"></i></a>
                                                            </th>
                                                            <th style="width: 120px;"><?= lang('Tháng') ?><span class="red">*</span>
                                                            </th>
                                                            <th style="width: 150px;"><?= lang('Năm') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 150px;"><?= lang('Số tiền') ?></th>
                                                            <th style="width: 50px;"><?= lang('Áp dụng') ?></th>
                                                            <th style="width: 80px;"><?= lang('actions') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $counterSalaryNew = 0;
                                                        $salaryNew = !empty($member) ? get_table_where(
                                                            'tbl_staff_salary',
                                                            ['staff_id' => $member->staffid]
                                                        ) : [];
                                                        ?>
                                                        <?php if (!empty($salaryNew)) : ?>
                                                            <?php foreach ($salaryNew as $key => $value) : ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="stt-salary-new text-center"><?= ++$key ?></div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-month">
                                                                            <input type="hidden"
                                                                                name="id_salary_staff[<?= $counterSalaryNew ?>]"
                                                                                class="form-control id_salary_staff"
                                                                                value="<?= $value['id'] ?>">
                                                                            <select name="month[<?= $counterSalaryNew ?>]"
                                                                                id="month" class="month"
                                                                                style="width: 100%;">
                                                                                <option value=""></option>
                                                                                <?php foreach (getMonth() as $k => $val) : ?>
                                                                                    <option
                                                                                        <?= $k == $value['month'] ? 'selected' : '' ?>
                                                                                        value="<?= $k ?>"><?= $val ?></option>
                                                                                <?php endforeach ?>
                                                                            </select>
                                                                        </div>
                                                                        <input type="hidden" name="counterSalaryNew[]"
                                                                            id="counterSalaryNew" class="form-control"
                                                                            value="<?= $counterSalaryNew ?>">
                                                                        <input type="hidden" class="form-control staff_id"
                                                                            value="<?= $value['staff_id'] ?>">
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-year">
                                                                            <select name="year[<?= $counterSalaryNew ?>]"
                                                                                id="year" class="year"
                                                                                style="width: 100%;">
                                                                                <option value=""></option>
                                                                                <?php foreach (getYear() as $k => $val) : ?>
                                                                                    <option
                                                                                        <?= $k == $value['year'] ? 'selected' : '' ?>
                                                                                        value="<?= $k ?>"><?= $val ?></option>
                                                                                <?php endforeach ?>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-salary"><input type="text"
                                                                                name="salary[<?= $counterSalaryNew ?>]"
                                                                                id="salary"
                                                                                class="form-control salary number-format"
                                                                                style="width: 100%;"
                                                                                value="<?= formatMoney($value['salary']) ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-active text-center"><input
                                                                                type="checkbox"
                                                                                <?= $value['active'] == 1 ? 'checked' : '' ?>
                                                                                name="active_new[<?= $counterSalaryNew ?>]"
                                                                                id="active_new"
                                                                                onchange="changeActive(this)"
                                                                                class="form-control active_new"
                                                                                style="width: 20px;"
                                                                                value="<?= $value['active'] ?>"></div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-actions text-center"><span
                                                                                class="fa fa-remove btn btn-danger remove-row-salary-new"></span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php $counterSalaryNew++; ?>
                                                            <?php endforeach ?>
                                                        <?php endif ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 class="panel-title"
                                                    style="cursor: pointer; color: #fff;background-color: #337ab7;border-color: #337ab7; padding: 5px;"
                                                    data-toggle="collapse"
                                                    href="#collapse-family"><?= lang('Thông tin phụ cấp') ?></h3>
                                                <table id="table_phucap"
                                                    class="dt-tnh table tnh-table table-bordered table-hover"
                                                    style="margin-top: 10px !important;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 50px;">
                                                                <?= lang('STT') ?>
                                                            </th>
                                                            <th style="width: 120px;"><?= lang('Tiêu chí') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 150px;"><?= lang('Số tiền') ?></th>
                                                            <th class="hide"
                                                                style="width: 80px;"><?= lang('actions') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $counterAllowance = 0;
                                                        $allowanceStaff = !empty($member) ? get_table_where(
                                                            'tbl_staff_allowance',
                                                            ['staff_id' => $member->staffid]
                                                        ) : [];
                                                        ?>
                                                        <?php if (!empty($allowanceStaff)) : ?>
                                                            <?php foreach ($allowanceStaff as $key => $value) : ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="stt-salary-new text-center"><?= ++$key ?></div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-month">
                                                                            <input type="hidden"
                                                                                name="id_allowance_staff[<?= $counterAllowance ?>]"
                                                                                class="form-control id_allowance_staff"
                                                                                value="<?= $value['id'] ?>">
                                                                            <input type="text" data-placeholder="Tiêu chí"
                                                                                style="width: 100%"
                                                                                name="title[<?= $counterAllowance ?>]"
                                                                                id="title_<?= $counterAllowance ?>"
                                                                                value="<?= $value['category_id'] ?>"
                                                                                class="title none-event">
                                                                        </div>
                                                                        <input type="hidden" name="counterAllowance[]"
                                                                            id="counterAllowance" class="form-control"
                                                                            value="<?= $counterAllowance ?>">
                                                                        <input type="hidden"
                                                                            class="form-control staff_id_pc"
                                                                            name="staff_id_pc[<?= $counterAllowance ?>]"
                                                                            value="<?= $value['staff_id'] ?>">
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-salary"><input type="text"
                                                                                name="amount[<?= $counterAllowance ?>]"
                                                                                id="amount"
                                                                                class="form-control amount number-format"
                                                                                style="width: 100%;"
                                                                                value="<?= formatMoney($value['amount']) ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td class="hide" style="width: 100px">
                                                                        <div class="td-actions text-center"><span
                                                                                class="fa fa-remove btn btn-danger"
                                                                                onclick="removePC(this)"></span></div>
                                                                    </td>
                                                                </tr>
                                                                <?php $counterAllowance++; ?>
                                                            <?php endforeach ?>
                                                        <?php endif ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 class="panel-title"
                                                    style="cursor: pointer; color: #fff;background-color: #337ab7;border-color: #337ab7; padding: 5px;"
                                                    data-toggle="collapse"
                                                    href="#collapse-family"><?= lang('Thông tin giảm trừ') ?></h3>
                                                <table id="table_giamtru"
                                                    class="dt-tnh table tnh-table table-bordered table-hover"
                                                    style="margin-top: 10px !important;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 50px;">
                                                                <?= lang('STT') ?>
                                                            </th>
                                                            <th style="width: 120px;"><?= lang('Tiêu chí') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 150px;"><?= lang('Số tiền') ?></th>
                                                            <th class="hide"
                                                                style="width: 80px;"><?= lang('actions') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $countGiamTru = 0;
                                                        $reduceStaff = !empty($member) ? get_table_where(
                                                            'tbl_staff_reduce',
                                                            ['staff_id' => $member->staffid]
                                                        ) : [];
                                                        ?>
                                                        <?php if (!empty($reduceStaff)) : ?>
                                                            <?php foreach ($reduceStaff as $key => $value) : ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="stt-salary-new text-center"><?= ++$key ?></div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-month">
                                                                            <input type="hidden"
                                                                                name="id_reduce_staff[<?= $countGiamTru ?>]"
                                                                                class="form-control id_reduce_staff"
                                                                                value="<?= $value['id'] ?>">
                                                                            <input type="text" data-placeholder="Tiêu chí"
                                                                                style="width: 100%"
                                                                                name="title_gt[<?= $countGiamTru ?>]"
                                                                                id="title1_<?= $countGiamTru ?>"
                                                                                value="<?= $value['category_id'] ?>"
                                                                                class="title_gt none-event">
                                                                        </div>
                                                                        <input type="hidden" name="countGiamTru[]"
                                                                            id="countGiamTru" class="form-control"
                                                                            value="<?= $countGiamTru ?>">
                                                                        <input type="hidden"
                                                                            class="form-control staff_id_gt"
                                                                            name="staff_id_gt[<?= $countGiamTru ?>]"
                                                                            value="<?= $value['staff_id'] ?>">
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-salary"><input type="text"
                                                                                name="amount_gt[<?= $countGiamTru ?>]"
                                                                                id="amount_gt"
                                                                                class="form-control amount_gt number-format"
                                                                                style="width: 100%;"
                                                                                value="<?= formatMoney($value['amount']) ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td class="hide" style="width: 100px">
                                                                        <div class="td-actions text-center"><span
                                                                                class="fa fa-remove btn btn-danger"
                                                                                onclick="removeGT(this)"></span></div>
                                                                    </td>
                                                                </tr>
                                                                <?php $countGiamTru++; ?>
                                                            <?php endforeach ?>
                                                        <?php endif ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 class="panel-title"
                                                    style="cursor: pointer; color: #fff;background-color: #337ab7;border-color: #337ab7; padding: 5px;"
                                                    data-toggle="collapse"
                                                    href="#collapse-family"><?= lang('tnh_family_information') ?></h3>
                                                <table id="tb-family"
                                                    class="dt-tnh table tnh-table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 50px;">
                                                                <a class="btn btn-info btn-icon add-row-family"><i
                                                                        class="fa fa-plus"></i></a>
                                                            </th>
                                                            <th style="width: 120px;"><?= lang('tnh_relationship') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 150px;"><?= lang('tnh_fullname') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 100px;"><?= lang('tnh_year_birthday') ?></th>
                                                            <th style="width: 150px;"><?= lang('tnh_career') ?></th>
                                                            <th style="width: 200px;"><?= lang('tnh_address') ?></th>
                                                            <th style="width: 150px;"><?= lang('tnh_telephone') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 80px;"><?= lang('actions') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $counterFamily = 0;
                                                        $family = !empty($member) ? $this->staff_model->getStaffFamily($member->staffid) : [];
                                                        ?>
                                                        <?php if (!empty($family)) : ?>
                                                            <?php foreach ($family as $key => $value) : ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="stt-family text-center"><?= ++$key ?></div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-relationship">
                                                                            <input type="hidden"
                                                                                name="staff_family_id[<?= $counterFamily ?>]"
                                                                                class="form-control staff_family_id"
                                                                                value="<?= $value['id'] ?>">
                                                                            <select name="relationship_family[<?= $counterFamily ?>]"
                                                                                data-placeholder="<?= lang('tnh_relationship') ?>"
                                                                                id="relationship-family"
                                                                                class="relationship-family"
                                                                                style="width: 100%;">
                                                                                <option value=""></option>
                                                                                <?php foreach (getRelationship() as $k => $val) : ?>
                                                                                    <option
                                                                                        <?= $k == $value['relationship_family'] ? 'selected' : '' ?>
                                                                                        value="<?= $k ?>"><?= $val ?></option>
                                                                                <?php endforeach ?>
                                                                            </select>
                                                                        </div>
                                                                        <input type="hidden" name="counterFamily[]"
                                                                            id="counterFamily" class="form-control"
                                                                            value="<?= $counterFamily ?>">
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-fullname"><input type="text"
                                                                                name="fullname_family[<?= $counterFamily ?>]"
                                                                                id="fullname_family"
                                                                                placeholder="<?= lang('tnh_fullname') ?>"
                                                                                class="form-control fullname_family"
                                                                                style="width: 100%;"
                                                                                value="<?= $value['fullname_family'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-year-birthday"><input type="number"
                                                                                name="year_birthday_family[<?= $counterFamily ?>]"
                                                                                id="year_birthday_family"
                                                                                placeholder="<?= lang('tnh_year_birthday') ?>"
                                                                                class="form-control fullname_family"
                                                                                style="width: 100%;"
                                                                                value="<?= $value['year_birthday_family'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-career"><input type="text"
                                                                                name="career_family[<?= $counterFamily ?>]"
                                                                                id="career_family"
                                                                                placeholder="<?= lang('tnh_career') ?>"
                                                                                class="form-control career_family"
                                                                                style="width: 100%;"
                                                                                value="<?= $value['career_family'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-address"><input type="text"
                                                                                name="address_family[<?= $counterFamily ?>]"
                                                                                id="address_family"
                                                                                placeholder="<?= lang('tnh_address') ?>"
                                                                                class="form-control address_family"
                                                                                style="width: 100%;"
                                                                                value="<?= $value['address_family'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-telephone"><input type="text"
                                                                                name="telephone_family[<?= $counterFamily ?>]"
                                                                                id="telephone_family"
                                                                                placeholder="<?= lang('tnh_telephone') ?>'"
                                                                                class="form-control telephone_family"
                                                                                style="width: 100%;"
                                                                                value="<?= $value['telephone_family'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-actions text-center"><span
                                                                                class="fa fa-remove btn btn-danger remove-row-family"></span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php $counterFamily++; ?>
                                                            <?php endforeach ?>
                                                        <?php endif ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 class="panel-title"
                                                    style="cursor: pointer; color: #fff;background-color: #337ab7;border-color: #337ab7; padding: 5px;"
                                                    data-toggle="collapse"
                                                    href="#collapse-family"><?= lang('tnh_literacy') ?></h3>
                                                <table id="tb-literacy"
                                                    class="dt-tnh tnh-table table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 50px;">
                                                                <a class="btn btn-info btn-icon add-row-literacy"><i
                                                                        class="fa fa-plus"></i></a>
                                                            </th>
                                                            <th style="width: 120px;"><?= lang('from_date') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 120px;"><?= lang('to_date') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 150px;"><?= lang('tnh_literacy') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 150px;"><?= lang('tnh_training_places') ?>
                                                                <span class="red">*</span>
                                                            </th>
                                                            <th style="width: 150px;"><?= lang('tnh_specialized') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 100px;"><?= lang('tnh_classification') ?><span
                                                                    class="red">*</span></th>
                                                            <th style="width: 80px;"><?= lang('actions') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $counterLiteracy = 0;
                                                        $literacy = !empty($member) ? $this->staff_model->getStaffLiteracy($member->staffid) : [];
                                                        ?>
                                                        <?php if (!empty($literacy)): ?>
                                                            <?php foreach ($literacy as $key => $value): ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="stt-literacy text-center"><?= ++$key ?></div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="hidden"
                                                                            name="staff_literacy_id[<?= $counterFamily ?>]"
                                                                            class="form-control staff_literacy_id"
                                                                            value="<?= $value['id'] ?>">
                                                                        <div class="td-from-date"><input type="text"
                                                                                name="from_date_literacy[<?= $counterLiteracy ?>]"
                                                                                id="from_date_literacy"
                                                                                placeholder="dd/mm/yyyy"
                                                                                class="form-control datepicker"
                                                                                style="width: 100%;"
                                                                                value="<?= _d($value['from_date_literacy']) ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-from-date">
                                                                            <input type="text"
                                                                                name="to_date_literacy[<?= $counterLiteracy ?>]"
                                                                                id="to_date_literacy"
                                                                                placeholder="dd/mm/yyyy"
                                                                                class="form-control datepicker"
                                                                                style="width: 100%;"
                                                                                value="<?= _d($value['to_date_literacy']) ?>">
                                                                            <input type="hidden" name="counterLiteracy[]"
                                                                                id="counterLiteracy" class="form-control"
                                                                                value="<?= $counterLiteracy ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-literacy">
                                                                            <select name="literacy[<?= $counterLiteracy ?>]"
                                                                                data-placeholder="<?= lang('tnh_literacy') ?>'"
                                                                                id="literacy" class="literacy"
                                                                                style="width: 100%;">
                                                                                <option></option>
                                                                                <?php foreach (getLiteracy() as $k => $val): ?>
                                                                                    <option
                                                                                        <?= $k == $value['literacy'] ? 'selected' : '' ?>
                                                                                        value="<?= $k ?>"><?= $val ?></option>
                                                                                <?php endforeach ?>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-training-places">
                                                                            <input type="text"
                                                                                name="training_places_literacy[<?= $counterLiteracy ?>]"
                                                                                id="training_places_literacy"
                                                                                placeholder="<?= lang('tnh_training_places') ?>"
                                                                                class="form-control training_places_literacy"
                                                                                style="width: 100%;"
                                                                                value="<?= $value['training_places_literacy'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-specialized"><input type="text"
                                                                                name="specialized_literacy[<?= $counterLiteracy ?>]"
                                                                                id="specialized_literacy"
                                                                                placeholder="<?= lang('tnh_training_places') ?>"
                                                                                class="form-control specialized_literacy"
                                                                                style="width: 100%;"
                                                                                value="<?= $value['specialized_literacy'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-classification">
                                                                            <select name="classification_literacy[<?= $counterLiteracy ?>]"
                                                                                data-placeholder="'+ langPersonnel['tnh_classification'] +'"
                                                                                id="classification-literacy"
                                                                                class="classification_literacy"
                                                                                style="width: 100%;">
                                                                                <option></option>
                                                                                <?php foreach (getClassification() as $k => $val): ?>
                                                                                    <option
                                                                                        <?= $k == $value['classification_literacy'] ? 'selected' : '' ?>
                                                                                        value="<?= $k ?>"><?= $val ?></option>
                                                                                <?php endforeach ?>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-actions text-center"><span
                                                                                class="fa fa-remove btn btn-danger remove-row-literacy"></span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                $counterLiteracy++;
                                                                ?>
                                                            <?php endforeach ?>
                                                        <?php endif ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 class="panel-title"
                                                    style="cursor: pointer; color: #fff;background-color: #337ab7;border-color: #337ab7; padding: 5px;"
                                                    data-toggle="collapse"
                                                    href="#collapse-family"><?= lang('tnh_insurrance') ?></h3>
                                            </div>
                                        </div>
                                        <div class="row mtop10">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_insurrance_book_number', 'insurrance_book_number') ?>
                                                    <input type="text" name="insurrance_book_number"
                                                        id="insurrance_book_number"
                                                        placeholder="<?= lang('tnh_insurrance_book_number') ?>"
                                                        class="form-control insurrance_book_number"
                                                        value="<?= !empty($member->insurrance_book_number) ? $member->insurrance_book_number : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_number_bhty', 'number_bhty') ?>
                                                    <input type="text" name="number_bhty" id="number_bhty"
                                                        placeholder="<?= lang('tnh_number_bhty') ?>"
                                                        class="form-control number_bhty"
                                                        value="<?= !empty($member->number_bhty) ? $member->number_bhty : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_province_code', 'province_code_staff') ?>
                                                    <input type="text" name="province_code_staff"
                                                        id="province_code_staff"
                                                        placeholder="<?= lang('tnh_province_code') ?>"
                                                        class="form-control province_code_staff"
                                                        value="<?= !empty($member->province_code_staff) ? $member->province_code_staff : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?= lang('tnh_hospital_registration', 'hospital_registration') ?>
                                                    <input type="text" name="hospital_registration"
                                                        id="hospital_registration"
                                                        placeholder="<?= lang('tnh_hospital_registration') ?>"
                                                        class="form-control hospital_registration"
                                                        value="<?= !empty($member->hospital_registration) ? $member->hospital_registration : '' ?>"
                                                        title="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php
                                                $arrReceive = [];
                                                if (!empty($member)) {
                                                    $dtReceive = $this->staff_model->getStaffReceive($member->staffid);
                                                    foreach ($dtReceive as $key => $value) {
                                                        $arrReceive[] = $value['receive_id'];
                                                    }
                                                }
                                                ?>
                                                <h3 class="panel-title"
                                                    style="cursor: pointer; color: #fff;background-color: #337ab7;border-color: #337ab7; padding: 5px;"
                                                    data-toggle="collapse"
                                                    href="#collapse-family"><?= lang('tnh_receive') ?></h3>
                                                <div class="form-group">
                                                    <?php foreach (getReceivePersonnel() as $key => $value) : ?>
                                                        <div class="checkbox checkbox-info">
                                                            <input <?= in_array($key, $arrReceive) ? 'checked' : '' ?>
                                                                type="checkbox" name="receive[]" id="<?= $key ?>"
                                                                value="<?= $key ?>">
                                                            <label for="<?= $key ?>"><?= $value ?></label>
                                                        </div>
                                                    <?php endforeach ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="staff_permissions">
                                <div class="row">
                                    <div class="col-md-5">
                                        <?php
                                        hooks()->do_action('staff_render_permissions');
                                        $selected = '';
                                        foreach ($roles as $role) {
                                            if (isset($member)) {
                                                if ($member->role == $role['roleid']) {
                                                    $selected = $role['roleid'];
                                                }
                                            }
                                            //để mặt định rỗng
                                            // else {
                                            //    $default_staff_role = get_option('default_staff_role');
                                            //    if($default_staff_role == $role['roleid'] ){
                                            //       $selected = $role['roleid'];
                                            //    }
                                            // }
                                        }
                                        ?>
                                        <?php echo render_select(
                                            'role',
                                            $roles,
                                            array('roleid', 'name'),
                                            'cong_title',
                                            $selected
                                        ); ?>

                                        <!-- staff management -->
                                        <?php
                                        $employee_manage = [];
                                        if (!empty($member)) {
                                            foreach ($employee_manage_staff as $key => $value) {
                                                $employee_manage[] = $value['employee_id'];
                                            }
                                        }
                                        ?>

                                        <?php //echo render_select('employee_manage[]', $employees, array('staffid', 'name'), 'tnh_manage', $employee_manage, ['multiple' => true]); 
                                        ?>

                                        <?php echo render_select(
                                            'employee_manage[]',
                                            $employees_v2,
                                            array('staffid', 'name'),
                                            'tnh_manage',
                                            $employee_manage,
                                            ['multiple' => true]
                                        ); ?>
                                        <!-- end staff management -->

                                        <hr />
                                        <h4 class="font-medium mbot15 bold"><?php echo _l('staff_add_edit_permissions'); ?></h4>
                                        <?php
                                        if (isset($member)) {
                                            $data_permission['staff'] = $arr_parent;
                                            $data_permission['staffid'] = $member->staffid;
                                        } else {
                                            $data_permission['staff'] = array();
                                        }
                                        if (isset($member)) {
                                            $permissionsData['member'] = $member;
                                        }
                                        $this->load->view('admin/staff/permissions', $data_permission);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-bottom-toolbar text-right btn-toolbar-container-out">
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
                <?php if (isset($member)) { ?>
                    <div class="col-md-7 small-table-right-col hide">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h4 class="no-margin">
                                    <?php echo _l('tasks'); ?>
                                </h4>
                                <hr class="hr-panel-heading" />
                                <div class="_filters _hidden_inputs hidden staff_tasks_filter">
                                    <?php echo form_hidden('staff_id', $member->staffid); ?>
                                </div>
                                <?php render_datatable(array(
                                    _l('#'),
                                    _l('tasks_dt_name'),
                                    _l('task_status'),
                                    _l('tasks_dt_datestart'),
                                    _l('task_duedate'),
                                    _l('tags'),
                                    _l('tasks_list_priority'),
                                ), 'staff-tasks'); ?>
                            </div>
                        </div>
                        <div class="panel_s">
                            <div class="panel-body">
                                <h4 class="no-margin">
                                    <?php echo _l('cong_orders'); ?>
                                </h4>
                                <hr class="hr-panel-heading" />
                                <div class="_filters _hidden_inputs hidden staff_orders_filter">
                                    <?php echo form_hidden('staff_id', $member->staffid); ?>
                                </div>
                                <?php render_datatable(array(
                                    _l('#'),
                                    _l('date'),
                                    _l('tnh_reference_orders'),
                                    _l('clients'),
                                    _l('proposal_total'),
                                ), 'staff-orders'); ?>
                            </div>
                        </div>
                        <div class="panel_s">
                            <div class="panel-body">
                                <h4 class="no-margin">
                                    <?php echo _l('staff_add_edit_notes'); ?>
                                </h4>
                                <hr class="hr-panel-heading" />
                                <a href="#" class="btn btn-success"
                                    onclick="slideToggle('.usernote'); return false;"><?php echo _l('new_note'); ?></a>
                                <div class="clearfix"></div>
                                <hr class="hr-panel-heading" />
                                <div class="mbot15 usernote hide inline-block full-width">
                                    <?php echo form_open(admin_url('misc/add_note/' . $member->staffid . '/staff')); ?>
                                    <?php echo render_textarea(
                                        'description',
                                        'staff_add_edit_note_description',
                                        '',
                                        array('rows' => 5)
                                    ); ?>
                                    <button class="btn btn-info pull-right mbot15"><?php echo _l('submit'); ?></button>
                                    <?php echo form_close(); ?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="mtop15">
                                    <table class="table dt-table scroll-responsive" data-order-col="2"
                                        data-order-type="desc">
                                        <thead>
                                            <tr>
                                                <th width="50%"><?php echo _l('staff_notes_table_description_heading'); ?></th>
                                                <th><?php echo _l('staff_notes_table_addedfrom_heading'); ?></th>
                                                <th><?php echo _l('staff_notes_table_dateadded_heading'); ?></th>
                                                <th><?php echo _l('options'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($user_notes as $note) { ?>
                                                <tr>
                                                    <td width="50%">
                                                        <div data-note-description="<?php echo $note['id']; ?>">
                                                            <?php echo check_for_links($note['description']); ?>
                                                        </div>
                                                        <div data-note-edit-textarea="<?php echo $note['id']; ?>"
                                                            class="hide inline-block full-width">
                                                            <textarea name="description" class="form-control"
                                                                rows="4"><?php echo clear_textarea_breaks($note['description']); ?></textarea>
                                                            <div class="text-right mtop15">
                                                                <button type="button" class="btn btn-default"
                                                                    onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                                                <button type="button" class="btn btn-info"
                                                                    onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $note['firstname'] . ' ' . $note['lastname']; ?></td>
                                                    <td data-order="<?php echo $note['dateadded']; ?>"><?php echo _dt($note['dateadded']); ?></td>
                                                    <td>
                                                        <?php if ($note['addedfrom'] == get_staff_user_id() || has_permission(
                                                            'staff',
                                                            '',
                                                            'delete'
                                                        )) { ?>
                                                            <a href="#" class="btn btn-default btn-icon"
                                                                onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i
                                                                    class="fa fa-pencil-square-o"></i></a>
                                                            <a href="<?php echo admin_url('misc/delete_note/' . $note['id']); ?>"
                                                                class="btn btn-danger btn-icon _delete"><i
                                                                    class="fa fa-remove"></i></a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="no-margin">
                                <?php echo _l('task_timesheets'); ?> & <?php echo _l('als_reports'); ?>
                            </h4>
                            <hr class="hr-panel-heading"/>
                            <?php echo form_open($this->uri->uri_string(), array('method' => 'GET')); ?>
                            <?php echo form_hidden('filter', 'true'); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="select-placeholder">
                                        <select name="range" id="range" class="selectpicker" data-width="100%">
                                            <option value="this_month" <?php if (!$this->input->get('range') || $this->input->get('range') == 'this_month') {
                                                                            echo 'selected';
                                                                        } ?>><?php echo _l('staff_stats_this_month_total_logged_time'); ?></option>
                                            <option value="last_month" <?php if ($this->input->get('range') == 'last_month') {
                                                                            echo 'selected';
                                                                        } ?>><?php echo _l('staff_stats_last_month_total_logged_time'); ?></option>
                                            <option value="this_week" <?php if ($this->input->get('range') == 'this_week') {
                                                                            echo 'selected';
                                                                        } ?>><?php echo _l('staff_stats_this_week_total_logged_time'); ?></option>
                                            <option value="last_week" <?php if ($this->input->get('range') == 'last_week') {
                                                                            echo 'selected';
                                                                        } ?>><?php echo _l('staff_stats_last_week_total_logged_time'); ?></option>
                                            <option value="period" <?php if ($this->input->get('range') == 'period') {
                                                                        echo 'selected';
                                                                    } ?>><?php echo _l('period_datepicker'); ?></option>
                                        </select>
                                    </div>
                                    <div class="row mtop15">
                                        <div class="col-md-12 period <?php if ($this->input->get('range') != 'period') {
                                                                            echo 'hide';
                                                                        } ?>">
                                            <?php echo render_date_input(
                                                'period-from',
                                                '',
                                                $this->input->get('period-from')
                                            ); ?>
                                        </div>
                                        <div class="col-md-12 period <?php if ($this->input->get('range') != 'period') {
                                                                            echo 'hide';
                                                                        } ?>">
                                            <?php echo render_date_input(
                                                'period-to',
                                                '',
                                                $this->input->get('period-to')
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-right">
                                    <button type="submit"
                                            class="btn btn-success apply-timesheets-filters"><?php echo _l('apply'); ?></button>
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                            <hr class="hr-panel-heading"/>
                            <table class="table dt-table scroll-responsive">
                                <thead>
                                <th><?php echo _l('task'); ?></th>
                                <th><?php echo _l('timesheet_start_time'); ?></th>
                                <th><?php echo _l('timesheet_end_time'); ?></th>
                                <th><?php echo _l('task_relation'); ?></th>
                                <th><?php echo _l('staff_hourly_rate'); ?> (<?php echo _l('als_staff'); ?>)</th>
                                <th><?php echo _l('time_h'); ?></th>
                                <th><?php echo _l('time_decimal'); ?></th>
                                <th data-sortable="false"></th>
                                </thead>
                                <tbody>
                                <?php
                                $total_logged_time = 0;
                                foreach ($timesheets as $t) { ?>
                                    <tr>
                                        <td><a href="#"
                                               onclick="init_task_modal(<?php echo $t['task_id']; ?>); return false;"><?php echo $t['name']; ?></a>
                                        </td>
                                        <td data-order="<?php echo $t['start_time']; ?>"><?php echo _dt(
                                                                                                $t['start_time'],
                                                                                                true
                                                                                            ); ?></td>
                                        <td data-order="<?php echo $t['end_time']; ?>">
                                            <?php
                                            // Allow admins or timer user to stop forgotten timers by staff member
                                            if ($t['not_finished'] && (is_admin() || $t['staff_id'] === get_staff_user_id())) {
                                            ?>
                                                <a href="#"
                                                    <?php
                                                    // Do not show the note popover when there is no associated task
                                                    // The user will be able to add note and select task in the popup window that will open
                                                    if ($t['task_id'] != 0) { ?>
                                                        data-toggle="popover"
                                                        data-placement="bottom"
                                                        data-html="true"
                                                        data-trigger="manual"
                                                        data-title="<?php echo _l('note'); ?>"
                                                        data-content='<?php echo render_textarea('timesheet_note'); ?><button type="button"
                                          onclick="timer_action(this, <?php echo $t['task_id']; ?>, <?php echo $t['id']; ?>, 1);" class="btn btn-info btn-xs"><?php echo _l('save'); ?></button>'
                                                        onclick="return false;"
                                                    <?php } else { ?>
                                                        onclick="timer_action(this, <?php echo $t['task_id']; ?>, <?php echo $t['id']; ?>, 1); return false;"
                                                    <?php } ?>
                                                   class="text-danger"
                                                >
                                                    <i class="fa fa-clock-o"></i>
                                                    <?php echo _l('task_stop_timer'); ?>
                                                </a>
                                                <?php
                                            } elseif ($t['not_finished']) {
                                                echo '<b>' . _l('timer_not_stopped_yet') . '</b>';
                                            } else {
                                                echo _dt($t['end_time'], true);
                                            }
                                                ?>
                                        </td>
                                        <td>
                                            <?php
                                            $rel_data = get_relation_data($t['rel_type'], $t['rel_id']);
                                            $rel_values = get_relation_values($rel_data, $t['rel_type']);
                                            echo '<a href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
                                            ?>
                                        </td>
                                        <td><?php echo app_format_money($t['hourly_rate'], $base_currency); ?></td>
                                        <td>
                                            <?php echo '<b>' . seconds_to_time_format($t['end_time'] - $t['start_time']) . '</b>'; ?>
                                        </td>
                                        <td data-order="<?php echo sec2qty($t['total']); ?>">
                                            <?php
                                            $total_logged_time += $t['total'];
                                            echo '<b>' . sec2qty($t['total']) . '</b>';
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!$t['billed']) {
                                                if (
                                                    has_permission('tasks', '', 'delete')
                                                    || (has_permission('projects', '', 'delete') && $t['rel_type'] == 'project')
                                                    || $t['staff_id'] == get_staff_user_id()
                                                ) {
                                                    echo '<a href="' . admin_url('tasks/delete_timesheet/' . $t['id']) . '" class="pull-right text-danger mtop5"><i class="fa fa-remove"></i></a>';
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td align="right"><?php echo '<b>' . _l('total_by_hourly_rate') . ':</b> ' . app_format_money((sec2qty($total_logged_time) * $member->hourly_rate),
                                                            $base_currency
                                                        ); ?></td>
                                    <td align="right">
                                        <?php echo '<b>' . _l('total_logged_hours_by_staff') . ':</b> ' . seconds_to_time_format($total_logged_time); ?>
                                    </td>
                                    <td align="right">
                                        <?php echo '<b>' . _l('total_logged_hours_by_staff') . ':</b> ' . sec2qty($total_logged_time); ?>
                                    </td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="no-margin">
                                <?php echo _l('projects'); ?>
                            </h4>
                            <hr class="hr-panel-heading"/>
                            <div class="_filters _hidden_inputs hidden staff_projects_filter">
                                <?php echo form_hidden('staff_id', $member->staffid); ?>
                            </div>
                            <?php render_datatable(array(
                                _l('project_name'),
                                _l('project_start_date'),
                                _l('project_deadline'),
                                _l('project_status'),
                            ), 'staff-projects'); ?>
                        </div>
                    </div> -->
                    </div>
                <?php } ?>
            </div>
            <div class="btn-bottom-pusher"></div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {

        $('select[name="role"]').on('change', function() {
            var roleid = $(this).val();
            init_roles_permissions(roleid, true);
        });

        $('input[name="administrator"]').on('change', function() {
            var checked = $(this).prop('checked');
            var isNotStaffMember = $('.is-not-staff');
            if (checked == true) {
                isNotStaffMember.addClass('hide');
                $('.roles').find('input').prop('disabled', true).prop('checked', false);
            } else {
                isNotStaffMember.removeClass('hide');
                isNotStaffMember.find('input').prop('checked', false);
                $('.roles').find('.capability').not('[data-not-applicable="true"]').prop('disabled', false)
            }
        });

        $('#is_not_staff').on('change', function() {
            var checked = $(this).prop('checked');
            var row_permission_leads = $('tr[data-name="leads"]');
            if (checked == true) {
                row_permission_leads.addClass('hide');
                row_permission_leads.find('input').prop('checked', false);
            } else {
                row_permission_leads.removeClass('hide');
            }
        });

        init_roles_permissions();

        appValidateForm($('.staff-form'), {
            firstname: 'required',
            lastname: 'required',
            username: 'required',
            phonenumber: 'required',
            role_level_id: 'required',
            setup_shift_id: 'required',
            // password: {
            //     required: {
            //         depends: function(element) {
            //             return ($('input[name="isedit"]').length == 0) ? true : false
            //         }
            //     }
            // },
            email: {
                // required: true,
                email: true,
                remote: {
                    url: site_url + "admin/misc/staff_email_exists",
                    type: 'post',
                    data: {
                        email: function() {
                            return $('input[name="email"]').val();
                        },
                        memberid: function() {
                            return $('input[name="memberid"]').val();
                        },
                        [csrfData['token_name']]: csrfData['hash']
                    }
                }
            }
        });
        show_permission();
    });

    $('select[name=role]').change(function() {
        var permission_parent = $('.js_permission_parent');
        var permission_child = $('.permission_child');
        $.each(permission_parent, function(i, v) {
            $(this).prop('checked', false);
        });
        $.each(permission_child, function(i, v) {
            $(this).prop('checked', false);
        });
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id_role'] = $(this).val();

        $.post(admin_url + 'staff/getData_permission', data).done(function(response) {
            response = JSON.parse(response);
            $.each(response.arr_parent, function(i, v) {
                $('div[data-name=' + v + ']').find('.js_permission_parent').prop('checked', true);
            });
            $.each(response.child, function(i, v) {
                $.each(v.arr_child, function(i_c, v_c) {
                    $('input[data-child=' + v.permission + '][data-can=' + v_c + ']').prop('checked', true);
                });
            });
            show_permission();
        });

        $.post(admin_url + 'staff/getStaff_roles', data).done(function(response) {
            response = JSON.parse(response);
            var option = '<option value=""></option>';
            $.each(response, function(i, v) {
                option += '<option value="' + v.staffid + '">' + v.fullname + '</option>';
            });

            $('select[name="employee_manage[]"]').html(option);
            $('select[name="employee_manage[]"]').selectpicker('refresh');
        });
    });
</script>
</body>

</html>
<script>
    $(document).ready(function() {
        $('#gender').select2();
        $('#marital_status').select2();
        $('#role_level_id').select2();
        $('#setup_shift_id').select2();
        $('#status_work').select2();
    });
</script>

<script type="text/javascript">
    var langPersonnel = <?= json_encode([
                            'tnh_relationship' => lang('tnh_relationship'),
                            'tnh_fullname' => lang('tnh_fullname'),
                            'tnh_career' => lang('tnh_career'),
                            'tnh_address' => lang('tnh_address'),
                            'tnh_telephone' => lang('tnh_telephone'),
                            'tnh_year_birthday' => lang('tnh_year_birthday'),
                            'from_date' => lang('from_date'),
                            'to_date' => lang('to_date'),
                            'tnh_literacy' => lang('tnh_literacy'),
                            'tnh_training_places' => lang('tnh_training_places'),
                            'tnh_specialized' => lang('tnh_specialized'),
                            'tnh_classification' => lang('tnh_classification'),
                            'tnh_depart_concurrently' => lang('tnh_depart_concurrently'),
                            'tnh_vt' => lang('tnh_vt'),
                            'tnh_allowance' => lang('tnh_allowance'),
                            'from_date' => lang('from_date'),
                            'note' => lang('note'),
                            'tnh_salary_form' => lang('tnh_salary_form'),
                            'tnh_amount_of_money' => lang('tnh_amount_of_money'),
                            'actions' => lang('actions'),
                            'tnh_salary' => lang('tnh_salary'),
                            'tnh_hinhthuc' => lang('tnh_hinhthuc'),
                            'role' => lang('role'),
                        ]) ?>;

    var token = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var relationship = <?= !empty(getRelationship()) ? json_encode(getRelationship()) : '{}' ?>;
    var literacy = <?= !empty(getLiteracy()) ? json_encode(getLiteracy()) : '{}' ?>;
    var classification = <?= !empty(getClassification()) ? json_encode(getClassification()) : '{}' ?>;
    var locations = <?= !empty($locations) ? json_encode($locations) : '{}' ?>;
    var roles = <?= !empty($roles) ? json_encode($roles) : '{}' ?>;
    var deparments = <?= !empty($deparments) ? json_encode($deparments) : '{}' ?>;
    var allowance = <?= !empty($allowance) ? json_encode($allowance) : '{}' ?>;
    var salaryForm = <?= !empty($salaryForm) ? json_encode($salaryForm) : '{}' ?>;
    var formInsurrance = <?= !empty(getFormInsurrance()) ? json_encode(getFormInsurrance()) : '{}' ?>;

    var year = <?= !empty(getYear()) ? json_encode(getYear()) : '{}' ?>;
    var month = <?= !empty(getMonth()) ? json_encode(getMonth()) : '{}' ?>;

    var edit = 0;
    var counterAllowance = <?= !empty($counterAllowance) ? $counterAllowance : 0 ?>;
    var countGiamTru = <?= !empty($countGiamTru) ? $countGiamTru : 0 ?>;
    var counterFamily = <?= !empty($counterFamily) ? $counterFamily : 0 ?>;
    var counterSalaryNew = <?= !empty($counterSalaryNew) ? $counterSalaryNew : 0 ?>;
    var counterLiteracy = <?= !empty($counterLiteracy) ? $counterLiteracy : 0 ?>;
    var counterConcurrently = <?= !empty($counterConcurrently) ? $counterConcurrently : 0 ?>;
    var counterSalary = <?= !empty($counterSalary) ? $counterConcurrently : 0 ?>;
    var counterInsurrance = <?= !empty($counterInsurrance) ? $counterInsurrance : 0 ?>;
    var count_errors = 0;
    var arrIdPc = [];
    var arrIdGt = [];
    // var table = '';
</script>

<script type="text/javascript" src="<?= js('personnel.js?vs=1.4') ?>"></script>
<script>
    $('body').on('change', 'input[name="departments[]"]', function() {
        var list_departments = $('input[name="departments[]"]:checked');
        var list_id = [];
        $.each(list_departments, function(index, value) {
            list_id.push($(value).val())
        })
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['list_departments'] = list_id;
        $.post(admin_url + 'staff/get_role_parent_departments', data, function(result) {
            result = JSON.parse(result);
            var html_option = '<option></option>';
            $.each(result, function(index, value) {
                html_option += '<option value="' + value.roleid + '">' + value.name + '</option>';
            })
            $('#role').html(html_option).selectpicker('refresh');
        })
    })
</script>