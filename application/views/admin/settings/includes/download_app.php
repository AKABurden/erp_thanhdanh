<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div role="tabpanel" class="tab-pane" id="download_app">
    <p class="text-muted">
        <?php echo _l('Tải link phiên bản app'); ?>
    </p>
    <?php echo render_input('settings[version_app]','Phiên bản',get_option('version_app')); ?>
    <?php echo render_input('settings[link_app_android]','Link Tải cho android',get_option('link_app_android')); ?>
    <?php echo render_input('settings[link_app_ios]','Link Tải cho ios',get_option('link_app_ios')); ?>
    <?php echo render_textarea('settings[note_app]','Lưu ý',get_option('note_app')); ?>
    <div class="checkbox checkbox-primary radio-inline">
        <input type="checkbox" id="checkbox_send_tb" name="settings[send_tb]" value="1">
        <label for="checkbox_send_tb"> Gửi thông báo lên app</label>
    </div>
    <hr />
</div>
