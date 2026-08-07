<?php defined('BASEPATH') or exit('No direct script access allowed');
$totalQuickActionsRemoved = 0;
$quickActions = $this->app->get_quick_actions_links();
foreach ($quickActions as $key => $item) {
    // if(isset($item['permission'])){
    //  if(!has_permission($item['permission'],'','create')){
    $totalQuickActionsRemoved++;
    //   }
    // }
}
?>

<style>
    @media (max-width: 767px) {
        body {
            height: unset;
            position: relative;
        }
        aside#menu {
            background: #f4f4f4;
            z-index: 999;
        }
    }
</style>
<aside id="menu" class="sidebar">
    <ul class="nav metis-menu" id="side-menu">
        <li class="dashboard_user">
            <?php if($img_menu) { ?>
                <img src="<?= base_url($img_menu); ?>" style="width: 20px; position: relative; top: -3px;">
            <?php } ?>
            <?= $name_menu ?>
            <i class="pull-right fa fa-angle-double-left action-menu"></i>
        </li>
        <?php
        hooks()->do_action('before_render_aside_menu');
        ?>

        <?php foreach ($sidebar_menu as $key => $item) {
            if (isset($item->collapse) && count($item->children) === 0) {
                continue;
            }
            ?>
            <?php if (!empty($item->not_menu)) { ?>
                <li class="menu-item-<?php echo isset($item->slug) ? $item->slug : ''; ?> <?= !empty($item->active) ? 'active' : '' ?>">
                    <a href="<?php echo !empty(admin_url($item->url_v2)) ? admin_url($item->url_v2) : '#'; ?>"
                       aria-expanded="false" style="padding-right: 0;">
                        <img class="wap-img" src="<?php echo(!empty($item->img) ? base_url($item->img) : ''); ?>">
                        <span class="menu-text">
                            <?php echo substr_string25(_l($item->name, '', false)); ?>
                        </span>
                        <span class="wap-count">
                            <?php echo !empty($item->table) ? getQuantity_Status($item->table) : ''; ?>
                        </span>
                    </a>
                </li>
            <?php } else { ?>
                <li class="menu-item-<?php echo isset($item->slug) ? $item->slug : ''; ?> <?= !empty($item->active) ? 'active' : '' ?>">
                    <a href="<?php echo !empty(admin_url($item->url)) ? admin_url($item->url) : '#'; ?>"
                       aria-expanded="false">
                        <i class="<?php echo(!empty($item->icon) ? $item->icon : ''); ?> menu-icon"></i>
                        <span class="menu-text">
                            <?php echo _l($item->name, '', false); ?>
                        </span>
                        <?php
                        $this->countLSX = $this->manufactures_model->countNotApprovePO();
                        $this->countSug = $this->manufactures_model->countNotApproveSug();
                        ?>
                        <?php if ($item->url === "manufactures/productions_orders" && $this->countLSX > 0): ?>
                            <span class="badge menu-badge bg-warning show-lsxt"><?= $this->countLSX ?></span>
                        <?php endif ?>
                        <?php if ($item->url === "manufactures/list_suggest_exporting" && $this->countSug > 0): ?>
                            <span class="badge menu-badge bg-warning show-dnxvt"><?= $this->countSug ?></span>
                        <?php endif ?>

                        <?php //sell ?>
                        <?php if ($item->url === "quotes"): ?>
                            <?php $this->countQuotes = $this->site_model->countNotApproveQuotes(); ?>
                            <?php if ($this->countQuotes > 0): ?>
                                <span class="badge menu-badge bg-warning show-quotes"><?= $this->countQuotes ?></span>
                            <?php endif ?>
                        <?php endif ?>
                        <?php if ($item->url === "orders"): ?>
                            <?php $this->countOrders = $this->site_model->countNotApproveOrders(); ?>
                            <?php if ($this->countOrders > 0): ?>
                                <span class="badge menu-badge bg-warning show-orders"><?= $this->countOrders ?></span>
                            <?php endif ?>
                        <?php endif ?>
                        <?php if ($item->url === "returned_goods"): ?>
                            <?php $this->countReturnedGoods = $this->site_model->countNotReturnedGoods(); ?>
                            <?php if ($this->countReturnedGoods > 0): ?>
                                <span class="badge menu-badge bg-warning show-returned-goods"><?= $this->countReturnedGoods ?></span>
                            <?php endif ?>
                        <?php endif ?>
                        <?php if ($item->url === "business_plan"): ?>
                            <?php $this->countBusinessPlan = $this->site_model->countNotBusinessPlan(); ?>
                            <?php if ($this->countBusinessPlan > 0): ?>
                                <span style="position: absolute; top: 7px; right: 15px;" class="badge menu-badge bg-warning show-business-plan"><?= $this->countBusinessPlan ?></span>
                            <?php endif ?>
                        <?php endif ?>
                        <?php //end sell ?>

                        <?php if (!empty($item->children)) { ?>
                            <span class="fa arrow"></span>
                        <?php } ?>
                    </a>
                    <?php if (!empty($item->children)) { ?>
                        <ul class="nav nav-second-level collapse" aria-expanded="false">
                            <?php foreach ($item->children as $keyChildren => $submenu) {
                                ?>
                                <li class="H_li sub-menu-item-<?php echo !empty($submenu->slug) ? $submenu->slug : ''; ?> <?= !empty($submenu->active) ? 'active' : '' ?>">
                                    <a href="<?php echo admin_url($submenu->url); ?>">
                                        <?php if (!empty($submenu->icon)) { ?>
                                            <i class="<?php echo $submenu->icon; ?> menu-icon"></i>
                                        <?php } ?>
                                        <span class="sub-menu-text"><?php echo _l($submenu->name, '', false); ?></span>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </li>
            <?php } ?>
            <?php hooks()->do_action('after_render_single_aside_menu', $item); ?>
        <?php } ?>
        <?php if (!empty($menu_hau)) { ?>
            <?php foreach ($menu_hau as $key => $submenu) { ?>
                <li class="<?= !empty($submenu['active']) ? 'active' : '' ?>">
                    <a href="#" aria-expanded="false">
                        <span class="menu-text">
                            <?php echo _l($submenu['name'], '', false); ?>
                        </span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse" aria-expanded="false">
                        <?php foreach ($submenu['videos'] as $k => $s) { ?>
                            <li class="H_li <?= !empty($s['active']) ? 'active' : '' ?>">
                                <a href="<?php echo admin_url('videos?id=' . $s['id']); ?>">
                                    <span class="sub-menu-text"><?php echo _l($s['name'], '', false); ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        <?php } ?>
        <?php if ($this->app->show_setup_menu() == true && (is_staff_member() || is_admin())){ ?>
        <li<?php if (get_option('show_setup_menu_item_only_on_hover') == 1) {
            echo ' style="display:none;"';
        } ?> id="setup-menu-item">
            <a href="#" class="open-customizer">
                <i class="fa fa-cog menu-icon"></i>
                <span class="menu-text">
                    <?php echo _l('setting_bar_heading'); ?>
                    <?php
                    if ($modulesNeedsUpgrade = $this->app_modules->number_of_modules_that_require_database_upgrade()) {
                        echo '<span class="badge menu-badge bg-warning">' . $modulesNeedsUpgrade . '</span>';
                    }
                    ?>
                </span>
            </a>
            <?php } ?>
        </li>
        <?php hooks()->do_action('after_render_aside_menu'); ?>
        <?php $this->load->view('admin/projects/pinned'); ?>
    </ul>
</aside>