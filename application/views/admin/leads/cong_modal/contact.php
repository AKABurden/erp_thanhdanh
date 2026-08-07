<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Modal Contact -->
<div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open(admin_url('leads/form_contact/' . $leadid . '/' . $contractid), array('id' => 'contact-lead-form', 'autocomplete' => 'off')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $title; ?><br/>
                    <small class="color-white" id="">
                        <?php echo get_table_where('tblleads', ['id' => $leadid], '', 'row')->name; ?>
                    </small>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- // For email exist check -->
                        <?php echo form_hidden('contactid', $contractid); ?>
                        <?php $value = (isset($contact) ? $contact->firstname : ''); ?>
                        <?php echo render_input('firstname', 'cong_last_firstname', $value); ?>
                        <?php $value = (isset($contact) ? $contact->title : ''); ?>
                        <?php echo render_input('title', 'contact_position', $value); ?>
                        <?php $value = (isset($contact) ? $contact->email : ''); ?>
                        <?php echo render_input('email', 'client_email', $value, 'email'); ?>

                        <?php $value = (!empty($contact->birtday) ? _d($contact->birtday) : ''); ?>
                        <?php echo render_date_input('birtday', 'cong_client_birtday', $value); ?>
                        <?php $value = (isset($contact) ? $contact->phonenumber : ''); ?>
                        <?php echo render_input('phonenumber', 'client_phonenumber', $value, 'text', array('autocomplete' => 'off')); ?>
                        <hr/>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="is_primary" id="is_primary" <?= (!empty($contact->is_primary) ? 'checked' : '') ?>>
                            <label for="contact_primary">
                                <?php echo _l('cong_contacts_is_primary'); ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#contact-form">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
