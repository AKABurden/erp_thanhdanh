<?php 
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Show options for chat pusher in Setup->Settings->Pusher
 */
$enabled = get_option('pusher_chat_enabled'); ?>
<div class="form-group"> 
    <label for="pusher_chat" class="control-label clearfix">
        <?php echo _l('chat_enable_option'); ?>
    </label> 
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_pusher_enable_chat" name="settings[pusher_chat_enabled]" value="1"<?php if ($enabled == '1') {
            echo ' checked';
        } ?>>
        <label for="y_opt_1_pusher_enable_chat"><?php echo _l('settings_yes'); ?></label>
    </div> 
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_pusher_enable_chat" name="settings[pusher_chat_enabled]" value="0" <?php if ($enabled == '0') {
            echo ' checked';
        } ?>>
        <label for="y_opt_2_pusher_enable_chat">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>
<hr>
<!--  
* Show options for chat pusher in Setup->Settings->Pusher
* get_option chat_staff_can_delete_messages is by default 1 
-->
<?php $can_delete = get_option('chat_staff_can_delete_messages');  ?>
<div class="form-group"> 
    <label for="pusher_chat" class="control-label clearfix">
        <?php echo _l('chat_allow_delete_messages'); ?>
    </label> 
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_chat_staff_can_delete_messages" name="settings[chat_staff_can_delete_messages]" value="1"<?php if ($can_delete == '1') {
            echo ' checked';
        } ?>>
        <label for="y_opt_1_chat_staff_can_delete_messages"><?php echo _l('settings_yes'); ?></label>
    </div> 
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_chat_staff_can_delete_messages" name="settings[chat_staff_can_delete_messages]" value="0" <?php if ($can_delete == '0') {
            echo ' checked';
        } ?>>
        <label for="y_opt_2_chat_staff_can_delete_messages">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>


