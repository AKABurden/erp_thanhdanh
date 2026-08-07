<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div role="tabpanel" class="tab-pane">
    <?php echo render_input('settings[package_software]','Gói đang dùng',get_option('package_software')); ?>
    <?php echo render_date_input('settings[expire_software]','Ngày hết hạn',get_option('expire_software')); ?>
    <?php echo render_input('settings[notification_before_software]','Thông báo gia hạn trước (Số ngày)',get_option('notification_before_software'),'number'); ?>
    <?php echo render_input('settings[notification_after_software]','Bắt buột gia hạn sau (Số ngày)',get_option('notification_after_software'),'number'); ?>
    <?php echo render_input('settings[switchboard_software]','Tổng đài hỗ trợ',get_option('switchboard_software')); ?>
    <?php echo render_input('settings[switchboard_company_software]','Tên công ty hõ trợ',get_option('switchboard_company_software')); ?>
</div>
