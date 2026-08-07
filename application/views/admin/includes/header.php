<?php defined('BASEPATH') or exit('No direct script access allowed');
ob_start();
?>
<li id="top_search" class="dropdown hide" data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
    <input type="search" id="search_input" class="form-control" placeholder="<?php echo _l('top_search_placeholder'); ?>">
    <div id="search_results">
    </div>
    <ul class="dropdown-menu search-results animated fadeIn no-mtop search-history" id="search-history">
    </ul>
    <div id="top_search_button">
        <button class="btn"><i class="fa fa-search"></i></button>
    </div>
</li>
<!-- <li id="top_search_button">
   <button class="btn"><i class="fa fa-search"></i></button>
</li> -->
<?php
$top_search_area = ob_get_contents();
ob_end_clean();
?>
<style>
    #header {
        background: #542901 !important;
    }

    .div-menu {
        font-weight: 400 !important;
        font-size: 16px !important;
        color: #FFFFFF !important;
    }

    #top_search_new input {
        background: unset !important;
        border: 1px solid #a09d9d !important;
        margin-bottom: 10px;
        margin-top: 15px;
    }
</style>
<style type="text/css">
    .w-container {
        min-width: 230px;
    }

    .w-content {
        padding: 10px;
        border-bottom: 1px solid #ececec;
    }

    .w-content-icon {
        margin-top: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        float: left;
        width: 20px;
        font-size: 14px;
        color: #256faa;
        transition: 0.3s all;
        transform: rotate(0deg);
    }

    .w-content-action {
        float: left;
        width: calc(100% - 25px);
        margin-left: 5px;
    }

    .w-content-a {
        color: #252525;
        text-transform: uppercase;
        font-size: 14px;
        font-weight: 500;
    }

    .w-content:hover {
        background: #f7f7f7;
    }

    .w-content:hover .w-content-icon {
        transform: rotate(90deg);
    }

    #top_search_order input {
        background: #fff;
        border-radius: 3px;
        margin-top: 15px;
        border: 0;
        width: 100%;
        color: #000;
        font-size: 15px;
    }
</style>

<div id="header">

    <div class="hide-menu hide"><i class="fa fa-bars"></i></div>
    <div id="logo">
        <a href="<?= admin_url('dashboard') ?>"><img src="<?= base_url('uploads/logo_thanh_danh.png') ?>"><a>
    </div>
    <nav>
        <!-- <div class="small-logo">
         <span class="text-primary">
            <?php get_company_logo(get_admin_uri() . '/') ?>
         </span>
      </div> -->

        <div class="small-logo js-menu">
            <i class="fa fa-list"></i>
        </div>

        <div class="mobile-menu">
            <button type="button" class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed" data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
                <i class="fa fa-chevron-down"></i>
            </button>
            
            <ul class="mobile-icon-menu">
                <?php
                // To prevent not loading the timers twice
                if (is_mobile()) { ?>
                    <li class="dropdown notifications-wrapper header-notifications">
                        <!--                    --><?php //$this->load->view('admin/includes/notifications'); 
                                                    ?>
                    </li>
                    <li class="header-timers hide">
                        <a href="#" id="top-timers" class="dropdown-toggle top-timers" data-toggle="dropdown"><i class="fa fa-clock-o fa-fw fa-lg"></i>
                            <span class="label bg-success icon-total-indicator icon-started-timers<?php if ($totalTimers = count($startedTimers) == 0) {
                                                                                                        echo ' hide';
                                                                                                    } ?>"><?php echo count($startedTimers); ?></span>
                        </a>
                        <ul class="dropdown-menu animated fadeIn started-timers-top width300" id="started-timers-top">
                            <?php $this->load->view('admin/tasks/started_timers', array('startedTimers' => $startedTimers)); ?>
                        </ul>
                    </li>
                    <li class="icon header-timers timer-button" id="header-timers" data-placement="bottom" data-toggle="tooltip" data-title="<?= lang('Menu') ?>">
                        <a href="#" id="top-timers" class="dropdown-toggle top-timers" data-toggle="dropdown">
                            <i class="fa fa-th fa-lg" aria-hidden="true"></i> <span class="div-menu"><?= lang('Menu') ?></span>
                        </a>
                        <ul class="dropdown-menu animated fadeIn" id="H_menu_mobile" style="right: -40px;">
                            <?php $this->load->view('admin/includes/menu_mobile'); ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
            <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;" role="navigation">
                <ul class="nav navbar-nav">
                    <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
                    <li class="header-my-timesheets"><a href="<?php echo admin_url('staff/timesheets'); ?>"><?php echo _l('my_timesheets'); ?></a>
                    </li>
                    <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a>
                    </li>
                    <?php if (is_staff_member()) { ?>
                        <li class="header-newsfeed">
                            <a href="#" class="open_newsfeed mobile">
                                <?php echo _l('whats_on_your_mind'); ?>
                            </a>
                        </li>
                    <?php } ?>
                    <li class="header-logout"><a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
        <ul class="nav navbar-nav navbar-right" style="display: flex">
            <li id="top_search_order" class="dropdown" data-toggle="tooltip" data-placement="bottom" data-title="Quét QR..." style="width: 620px;">
                <input type="hidden" id="search_input" class="form-control" placeholder="<?php echo _l('top_search_placeholder'); ?>">
                <input type="search" id="SearchQR_orders" class="form-control" placeholder="<?php echo _l('Quét QR...'); ?>">
                <div id="search_results"></div>
                <ul class="dropdown-menu search-results animated fadeIn no-mtop search-history" id="search-history"></ul>
                <div id="top_search_button">
                    <!-- <i class="fa fa-barcode" aria-hidden="true"></i> -->
                    <button class="btn"><i class="fa fa-barcode" aria-hidden="true"></i></button>
                </div>
            </li>
            <!-- <li class="" id="">
                <a href="https://erp.fososoft.com/fososystem/service" style="font-weight: 500;color: #fff;font-size: 20px;padding: 4px 12px 4px 12px;line-height: 57px;height: 63px;"  class="" data-toggle="">
                    <p>(<?= mb_strtoupper('ERP NGÀNH ' . get_option('majors'), 'UTF-8'); ?>)</p>
                </a>
            </li> -->
            <!-- <li class="dropdown" id="top-plus">
                <a href="#" class="dropdown-toggle top-plus" data-toggle="dropdown">
                    <i class="wrap-circle fa fa-plus" aria-hidden="true"></i>
                </a>
                <ul class="dropdown-menu animated fadeIn">
                    <?php //$this->load->view('admin/includes/action_addon'); 
                    ?>
                </ul>
            </li> -->
            <?php
            if (!is_mobile()) {
                echo $top_search_area;
            } ?>
            <?php hooks()->do_action('after_render_top_search'); ?>
            <li class="icon header-timers timer-button" id="header-timers" data-placement="bottom" data-toggle="tooltip" data-title="<?= lang('Menu') ?>">
                <a href="#" id="top-timers" class="dropdown-toggle top-timers" data-toggle="dropdown">
                    <i class="fa fa-th fa-lg" aria-hidden="true"></i> <span class="div-menu"><?= lang('Menu') ?></span>
                </a>
                <ul class="dropdown-menu animated fadeIn" id="H_menu_v2" style="right: -180px;width: 1200px;overflow-x: auto;">
                    <?php //$this->load->view('admin/includes/menu_v2'); 
                    ?>
                    <?php $this->load->view('admin/general/menu_v2'); ?>
                </ul>
            </li>
            <?php $this->load->view('admin/includes/menu_setting'); ?>
            <!-- onclick="loadNotificationCustom(this)" -->
            <!-- <li class="dropdown" id="notifications-custom" title="<?= lang('tnh_coupon_approved') ?>" data-toggle="tooltip" title="" data-placement="bottom"> -->
            <!-- <li class="dropdown" id="notifications-premium">
                <a href="<?= admin_url('premium'); ?>" style="line-height: 51px;">
                    <img width="28" src="<?= base_url('assets/images/premium.png') ?>">
                </a>
                <div class="wrap-container-premium hide">
                    MARKETPLACE - Tích hợp nhiều ứng dụng hơn
                </div>
            </li> -->
            <?php //if(checkWarningWarehouse()){ 
            ?>
            <li class="dropdown notifications-wrapper header-notifications" data-toggle="tooltip" title="<?php echo _l('Cảnh báo tồn kho'); ?>" data-placement="bottom" style="display: flex;justify-content: center;align-items: center;">
                <buton class="btn btn-info" style="color: black;background-color: #ecf3f7;"><a href="<?php echo base_url('admin/warning_warehouse'); ?>" style="color:unset"><span class="bell fa fa-bell"></span> Cảnh báo tồn kho</a>
                </buton>
            </li>
            <?php //} 
            ?>
            <li class="dropdown notifications-customs" id="notifications-customs" data-toggle="tooltip" title="" data-placement="bottom">
                <a href="#" class="dropdown-toggle notifications-icon" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-question-circle-o fa-fw fa-lg" aria-hidden="true"></i>
                    <span class="label icon-total-indicator bg-warning icon-notifications"></span>
                </a>
                <ul class="dropdown-menu notifications animated fadeIn width400" data-total-unread="1">
                    <li class="relative notification-wrapper not-outside" style="text-align: left; padding: 5px;" data-noti-custom-id="1">
                        <div class="notification-box wap-li" style="border-bottom: 1px solid #28b8da;">
                            <a style="color: #000000" target="_blank" href="https://fososoft-2.gitbook.io/foso-erp/">
                                <div>
                                    <img style="width: 45px;height: 45px;" src="<?= base_url('uploads/document.png') ?>"><span class="bold">Tài liệu hướng
                                        dẫn sử dụng</span>
                                </div>
                            </a>
                        </div>
                    </li>
                    <li class="relative notification-wrapper not-outside" style="text-align: left; padding: 5px;" data-noti-custom-id="1">
                        <div class="notification-box wap-li">
                            <a style="color: #000000" target="_blank" href="<?= admin_url('videos') ?>">
                                <div>
                                    <img style="width: 45px;height: 45px;" src="<?= base_url('uploads/videos.png') ?>"><span>Video hướng dẫn sử dụng</span>
                                </div>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>

            <li class="dropdown notifications-custom" id="notifications-custom" data-toggle="tooltip" title="" data-placement="bottom">
                <?php //$this->load->view('admin/includes/newsfeed'); 
                ?>
            </li>
            <li class="dropdown notifications-wrapper header-notifications" data-toggle="tooltip" title="<?php echo _l('nav_notifications'); ?>" data-placement="bottom">
                <?php //$this->load->view('admin/includes/notifications'); 
                ?>
            </li>
            <li class="icon header-user-profile" data-toggle="tooltip" title="<?php echo get_staff_full_name(); ?>" data-placement="bottom">
                <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="false">
                    <?php echo staff_profile_image($current_user->staffid, array('img', 'img-responsive', 'staff-profile-image-small', 'pull-left')); ?>
                </a>
                <ul class="dropdown-menu animated fadeIn">
                    <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
                    <li class="header-my-timesheets"><a href="<?php echo admin_url('staff/timesheets'); ?>"><?php echo _l('my_timesheets'); ?></a>
                    </li>
                    <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a>
                    </li>
                    <?php if (get_option('disable_language') == 0) { ?>
                        <li class="dropdown-submenu pull-left header-languages">
                            <a href="#" tabindex="-1"><?php echo _l('language'); ?></a>
                            <ul class="dropdown-menu dropdown-menu">
                                <li class="<?php if ($current_user->default_language == "") {
                                                echo 'active';
                                            } ?>">
                                    <a href="<?php echo admin_url('staff/change_language'); ?>"><?php echo _l('system_default_string'); ?></a>
                                </li>
                                <?php foreach ($this->app->get_available_languages() as $user_lang) { ?>
                                    <li<?php if ($current_user->default_language == $user_lang) {
                                            echo ' class="active"';
                                        } ?>>
                                        <a href="<?php echo admin_url('staff/change_language/' . $user_lang); ?>"><?php echo ucfirst($user_lang); ?></a>
                                    <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                    <li class="header-logout">
                        <a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>
<div id="mobile-search" class="<?php if (!is_mobile()) {
                                    echo 'hide';
                                } ?>">
    <ul>
        <?php
        if (is_mobile()) {
            echo $top_search_area;
        } ?>
    </ul>
</div>
<!-- Start of fososofthelp Zendesk Widget script -->
<!-- <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=69127496-b66c-49d3-9540-d7315a7765cb"> </script> -->
<!-- End of fososofthelp Zendesk Widget script -->
<!-- Start of fososofthelp Zendesk Widget script -->
<!-- <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=69127496-b66c-49d3-9540-d7315a7765cb"> </script> -->
<!-- End of fososofthelp Zendesk Widget script -->