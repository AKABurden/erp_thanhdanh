<style>
    .h_menu_setting {
        min-width: 150px;
    }
    .notification-box-sub {
        padding: 10px 10px;
        border-bottom: 1px solid #f0f0f0;
    }
    /*ul#h_menu_setting {*/
    /*    width: 100%;*/
    /*}*/
</style>
<li class="icon header-setting timer-button" id="header-setting" data-placement="bottom"
    data-toggle="tooltip" data-title="Cài đặt">
    <a href="#" id="top-setting" class="dropdown-toggle top-timers" data-toggle="dropdown">
        <i class="fa fa-cog fa-lg" aria-hidden="true"></i>
    </a>
    <ul class="dropdown-menu animated fadeIn h_menu_setting">
        <?php foreach($setup_menu as $key => $value) {?>
            <li class="relative notification-wrapper not-outside dropdown-submenu not-outside-c w-container" style="text-align: left; " data-noti-custom-id="1">
                <div class="notification-box-sub wap-li w-content">
                    <a style="color: #000000" <?=!empty($value->url) ? ' href="'.admin_url($value->url).'"' : ''?>>
                        <div class="w-content-icon">
                            <i class="fa fa-plus"></i>
                        </div>
                        <div class="w-content-action">
                            <span class="w-content-a"><?=_l($value->name)?></span> <?=!empty($value->children) ? '<span class="caret"></span>' : ''?>
                        </div>
                        <div class="clearfix"></div>
                    </a>
                </div>
                <?php if(!empty($value->children)) {?>
                    <ul class="dropdown-menu animated fadeIn h_menu_setting" style="width: 100%;overflow-x: hidden!important;">
                        <?php foreach($value->children as $k => $v) {?>
							<?php if (!is_admin()){
								if ($value->name == 'tnh_categories' && $v->url != 'categories/machines'){
									continue;
								}
							}?>
                            <li class="relative notification-wrapper not-outside-c w-container" style="text-align: left;" data-noti-custom-id="1">
                                <div class="notification-box-sub wap-li w-content">
                                    <a style="color: #000000" <?=!empty($v->url) ? ' href="'.admin_url($v->url).'"' : ''?>>
                                        <div class="w-content-icon">
                                            <i class="fa fa-plus"></i>
                                        </div>
                                        <div class="w-content-action">
                                            <span class="w-content-a"><?=_l($v->name)?></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </a>
                                </div>
                            </li>
                        <?php } ?>

                    </ul>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</li>
<script>
    $('.not-outside-c').click(function() {
        $.get(admin_url + 'misc/set_setup_menu_open')
    })
    $('.none-a').click(function() {
        $.get(admin_url + 'misc/set_setup_menu_closed')
    })
    $('#side-menu li').click(function() {
        $.get(admin_url + 'misc/set_setup_menu_closed')
    })
</script>
