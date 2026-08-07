<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .width20{
        width:20px;
    }
</style>
<?php
$files_and_folder = glob('assets/img_menu/*.png');
?>


<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" onclick="save_menu();return false;"
                               class="btn btn-info"><?php echo _l('utilities_menu_save'); ?></a>
                            <a href="<?php echo admin_url('menu_setup/reset_aside_menu'); ?>"
                               class="btn btn-default"><?php echo _l('reset'); ?></a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading no-mbot"/>
                        <div class="clearfix"></div>
                        <h4 class="bold mtop15"><?php echo _l('active_menu_items'); ?></h4>
                        <hr/>

                        <div class="dd active">
                            <?php
//                                echo "<pre>";
//                                var_dump($menu_options->sales);die();
                            echo '<ol class="dd-list">';
                            foreach ($menu_items as $k_item => $item)
                            {
                                $disabled = isset($menu_options->{$item['slug']}) && $menu_options->{$item['slug']}->disabled == 'true';
                                $item['url']        =   isset($menu_options->{$item['slug']}->url) ? $menu_options->{$item['slug']}->url : '';
                                $item['location']   =   isset($menu_options->{$item['slug']}->location) ? $menu_options->{$item['slug']}->location : '';
                                ?>
                                <li class="dd-item dd3-item main<?php echo(!isset($item['collapse']) ? ' dd-nochildren' : ''); ?>" data-id="<?php echo $item['slug']; ?>" <?=!empty($disabled) ? 'style="opacity:0.5"' : ''?>
                                    data-url="<?=(isset($item['url']) ? $item['url'] : '')?>" data-name="<?=$item['slug']?>" data-location="<?=(isset($item['location']) ? $item['location'] : '')?>">
                                    <div class="dd-handle dd3-handle"></div>
                                    <div class="dd3-content"><?php echo _l($item['name'], '', false); ?>
                                        <a href="#" class="text-muted toggle-menu-options main-item-options pull-right">
                                            <i class="fa fa-cog"></i>
                                        </a>
                                    </div>
                                    <div class="menu-options main-item-options" style="display:none;" data-menu-options="<?php echo $item['slug']; ?>">
                                        <?php if (!isset($item['collapse'])) { ?>
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <input type="checkbox" class="is-disabled-main" value="1"
                                                           id="menu_disabled_<?php echo $item['slug']; ?>"
                                                           name="disabled"<?php if ($disabled) {
                                                        echo ' checked';
                                                    } ?>>
                                                    <label for="menu_disabled_<?php echo $item['slug']; ?>">Disabled?</label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <label class="control-label">
                                            <?php echo _l('utilities_menu_icon'); ?>
                                        </label>
                                        <div class="input-group">
                                            <?php $icon = app_get_menu_setup_icon($menu_options, $item['slug'], 'sidebar'); ?>
                                            <input type="text" value="<?php if ($icon) { echo $icon; } ?>" class="form-control icon-picker" id="icon-<?php echo $item['slug']; ?>">
                                            <span class="input-group-addon">
                                              <i class="<?php if ($icon) { echo $icon; } ?>"></i>
                                            </span>
                                        </div>

                                        <div class="form-group mtop10">
                                            <label for="type-<?php echo $item['slug']; ?>" class="control-label">Nhóm</label>
                                            <select id="type-<?php echo $item['slug']; ?>" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                <option value=""></option>
                                                <option value="1" <?php echo(isset($menu_options->{$item['slug']}->type) && $menu_options->{$item['slug']}->type == 1 ? 'selected' : ''); ?>>
                                                    CRM
                                                </option>
                                                <option value="2" <?php echo(isset($menu_options->{$item['slug']}->type) && $menu_options->{$item['slug']}->type == 2 ? 'selected' : ''); ?>>
                                                    MUA HÀNG & NHẬP HÀNG
                                                </option>
                                                <option value="3" <?php echo(isset($menu_options->{$item['slug']}->type) && $menu_options->{$item['slug']}->type == 3 ? 'selected' : ''); ?>>
                                                    KHO & SẢN XUẤT
                                                </option>
                                                <option value="4" <?php echo(isset($menu_options->{$item['slug']}->type) && $menu_options->{$item['slug']}->type == 4 ? 'selected' : ''); ?>>
                                                    BÁN HÀNG & XUẤT HÀNG
                                                </option>
                                                <option value="5" <?php echo(isset($menu_options->{$item['slug']}->type) && $menu_options->{$item['slug']}->type == 5 ? 'selected' : ''); ?>>
                                                    KHÁC
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="img-<?php echo $item['slug']; ?>" class="control-label">IMG</label>
                                            <select id="img-<?php echo $item['slug']; ?>" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                <option value=""></option>
                                                <?php if(!empty($files_and_folder)){?>
                                                    <?php foreach($files_and_folder as $key => $value){?>
                                                        <option value="<?=$value?>" <?php echo(isset($menu_options->{$item['slug']}->img) && $menu_options->{$item['slug']}->img == $value ? 'selected' : ''); ?> data-content="<img class='width20' src='<?=base_url($value)?>'/>"></option>
                                                    <?php } ?>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                    <?php if (count($item['children']) > 0) { ?>
                                        <ol class="dd-list dd-list-sub-items">
                                            <?php foreach ($item['children'] as $submenu) {
                                                $child_disabled         = (isset($menu_options->{$item['slug']}->children->{$submenu['slug']}) && $menu_options->{$item['slug']}->children->{$submenu['slug']}->disabled == 'true');
                                                $submenu['url']         = (isset($menu_options->{$item['slug']}->children->{$submenu['slug']}->url) ? $menu_options->{$item['slug']}->children->{$submenu['slug']}->url : '');
                                                $submenu['location']    = (isset($menu_options->{$item['slug']}->children->{$submenu['slug']}->location) ? $menu_options->{$item['slug']}->children->{$submenu['slug']}->location : '');
                                                ?>
                                                <li class="dd-item dd3-item sub-items"
                                                    data-id="<?php echo $submenu['slug']; ?>"<?php if ($child_disabled) {
                                                    echo '  style="opacity:0.5"';
                                                } ?>  data-name="<?=$submenu['slug']?>" data-url="<?=(isset($submenu['url']) ? $submenu['url'] : '')?>" data-location="<?=(isset($submenu['location']) ? $submenu['location'] : '')?>" >

                                                    <div class="dd-handle dd3-handle"></div>
                                                    <div class="dd3-content"><?php echo _l($submenu['name'], '', false); ?>
                                                        <a href="#" class="text-muted toggle-menu-options sub-item-options pull-right">
                                                            <i class="fa fa-cog"></i>
                                                        </a>
                                                    </div>
                                                    <div class="menu-options sub-item-options" style="display:none;" data-menu-options="<?php echo $submenu['slug']; ?>">
                                                        <div class="form-group">
                                                            <div class="checkbox">
                                                                <input type="checkbox" class="is-disabled-child" value="1" id="menu_disabled_<?php echo $submenu['slug']; ?>" name="disabled" <?php if ($child_disabled) { echo ' checked'; } ?>>
                                                                <label for="menu_disabled_<?php echo $submenu['slug']; ?>">Disabled?</label>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="type-<?php echo $submenu['slug']; ?>" class="control-label">Nhóm</label>
                                                            <select id="type-<?php echo $submenu['slug']; ?>" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                                <option value=""></option>
                                                                <option value="1" <?php echo(isset($menu_options->{$submenu['slug']}->type) && $menu_options->{$submenu['slug']}->type == 1 ? 'selected' : ''); ?>>
                                                                    CRM
                                                                </option>
                                                                <option value="2" <?php echo(isset($menu_options->{$submenu['slug']}->type) && $menu_options->{$submenu['slug']}->type == 2 ? 'selected' : ''); ?>>
                                                                    MUA HÀNG & NHẬP HÀNG
                                                                </option>
                                                                <option value="3" <?php echo(isset($menu_options->{$submenu['slug']}->type) && $menu_options->{$submenu['slug']}->type == 3 ? 'selected' : ''); ?>>
                                                                    KHO & SẢN XUẤT
                                                                </option>
                                                                <option value="4" <?php echo(isset($menu_options->{$submenu['slug']}->type) && $menu_options->{$submenu['slug']}->type == 4 ? 'selected' : ''); ?>>
                                                                    BÁN HÀNG & XUẤT HÀNG
                                                                </option>
                                                                <option value="5" <?php echo(isset($menu_options->{$submenu['slug']}->type) && $menu_options->{$submenu['slug']}->type == 5 ? 'selected' : ''); ?>>
                                                                    KHÁC
                                                                </option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="img-<?php echo $submenu['slug']; ?>" class="control-label">Hình</label>
                                                            <select id="img-<?php echo $submenu['slug']; ?>" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                                <option value=""></option>
                                                                <?php if(!empty($files_and_folder)){?>
                                                                    <?php foreach($files_and_folder as $key => $value){?>
                                                                        <option value="<?=$value?>" <?php echo (isset($menu_options->{$submenu['slug']}->img) && $menu_options->{$submenu['slug']}->img == $value ? 'selected' : ''); ?> data-content="<img class='width20' src='<?=base_url($value)?>'/>"></option>
                                                                    <?php } ?>
                                                                <?php } ?>

                                                            </select>
                                                        </div>

                                                        <label class="control-label"><?php echo _l('utilities_menu_icon'); ?></label>
                                                        <div class="input-group">
                                                            <?php
                                                                $icon = app_get_menu_setup_icon($menu_options, $submenu['slug'], 'sidebar');
                                                            ?>
                                                            <input type="text" value="<?php if ($icon) { echo $icon; } ?>" class="form-control icon-picker" id="icon-<?php echo $submenu['slug']; ?>">
                                                            <span class="input-group-addon">
                                                                <i class="<?php if ($icon) { echo $icon; } ?>"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <?php if (!empty($submenu['children']) && count($submenu['children']) > 0) {?>
                                                        <ol class="dd-list dd-list-sub-items">
                                                            <?php foreach ($submenu['children'] as $sub) {
                                                                $child_disabled   = (isset($menu_options->{$item['slug']}->children->{$submenu['slug']}->children->{$sub['slug']}) && $menu_options->{$item['slug']}->children->{$submenu['slug']}->children->{$sub['slug']}->disabled == 'true');
                                                                $sub['url']       = (isset($menu_options->{$item['slug']}->children->{$submenu['slug']}->children->{$sub['slug']}->url) ? $menu_options->{$item['slug']}->children->{$submenu['slug']}->children->{$sub['slug']}->url : '');
                                                                $sub['location']  = (isset($menu_options->{$item['slug']}->children->{$submenu['slug']}->children->{$sub['slug']}->location) ? $menu_options->{$item['slug']}->children->{$submenu['slug']}->children->{$sub['slug']}->location : '');
                                                                ?>
                                                                <li class="dd-item dd3-item sub-items"
                                                                    data-id="<?php echo $sub['slug']; ?>"<?php if ($child_disabled) {
                                                                    echo '  style="opacity:0.5"';
                                                                } ?>  data-name="<?=$sub['slug']?>" data-url="<?=(isset($sub['url']) ? $sub['url'] : '')?>" data-location="<?=(isset($sub['location']) ? $sub['location'] : '')?>" >

                                                                    <div class="dd-handle dd3-handle"></div>
                                                                    <div class="dd3-content"><?php echo _l($sub['name'], '', false); ?>
                                                                        <a href="#" class="text-muted toggle-menu-options sub-item-options pull-right">
                                                                            <i class="fa fa-cog"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="menu-options sub-item-options" style="display:none;" data-menu-options="<?php echo $sub['slug']; ?>">
                                                                        <div class="form-group">
                                                                            <div class="checkbox">
                                                                                <input type="checkbox" class="is-disabled-child" value="1" id="menu_disabled_<?php echo $sub['slug']; ?>" name="disabled" <?php if ($child_disabled) { echo ' checked'; } ?>>
                                                                                <label for="menu_disabled_<?php echo $sub['slug']; ?>">Disabled?</label>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="type-<?php echo $sub['slug']; ?>" class="control-label">Nhóm</label>
                                                                            <select id="type-<?php echo $sub['slug']; ?>" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                                                <option value=""></option>
                                                                                <option value="1" <?php echo(isset($menu_options->{$sub['slug']}->type) && $menu_options->{$sub['slug']}->type == 1 ? 'selected' : ''); ?>>
                                                                                    CRM
                                                                                </option>
                                                                                <option value="2" <?php echo(isset($menu_options->{$sub['slug']}->type) && $menu_options->{$sub['slug']}->type == 2 ? 'selected' : ''); ?>>
                                                                                    MUA HÀNG & NHẬP HÀNG
                                                                                </option>
                                                                                <option value="3" <?php echo(isset($menu_options->{$sub['slug']}->type) && $menu_options->{$sub['slug']}->type == 3 ? 'selected' : ''); ?>>
                                                                                    KHO & SẢN XUẤT
                                                                                </option>
                                                                                <option value="4" <?php echo(isset($menu_options->{$sub['slug']}->type) && $menu_options->{$sub['slug']}->type == 4 ? 'selected' : ''); ?>>
                                                                                    BÁN HÀNG & XUẤT HÀNG
                                                                                </option>
                                                                                <option value="5" <?php echo(isset($menu_options->{$sub['slug']}->type) && $menu_options->{$sub['slug']}->type == 5 ? 'selected' : ''); ?>>
                                                                                    KHÁC
                                                                                </option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="img-<?php echo $sub['slug']; ?>" class="control-label">Hình</label>
                                                                            <select id="img-<?php echo $sub['slug']; ?>" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                                                <option value=""></option>
                                                                                <?php if(!empty($files_and_folder)){?>
                                                                                    <?php foreach($files_and_folder as $key => $value){?>
                                                                                        <option value="<?=$value?>" <?php echo (isset($menu_options->{$sub['slug']}->img) && $menu_options->{$sub['slug']}->img == $value ? 'selected' : ''); ?> data-content="<img class='width20' src='<?=base_url($value)?>'/>"></option>
                                                                                    <?php } ?>
                                                                                <?php } ?>

                                                                            </select>
                                                                        </div>

                                                                        <label class="control-label"><?php echo _l('utilities_menu_icon'); ?></label>
                                                                        <div class="input-group">
                                                                            <?php
                                                                                $icon = app_get_menu_setup_icon($menu_options, $sub['slug'], 'sidebar');
                                                                            ?>
                                                                            <input type="text" value="<?php if ($icon) { echo $icon; } ?>" class="form-control icon-picker" id="icon-<?php echo $sub['slug']; ?>">
                                                                            <span class="input-group-addon">
                                                                                <i class="<?php if ($icon) { echo $icon; } ?>"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            <?php } ?>
                                                        </ol>
                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                        </ol>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo module_dir_url('menu_setup', 'assets/jquery-nestable/jquery.nestable.js'); ?>"></script>
<link href="<?php echo module_dir_url('menu_setup', 'assets/font-awesome-icon-picker/css/fontawesome-iconpicker.min.css'); ?>"
      rel="stylesheet">
<script src="<?php echo module_dir_url('menu_setup', 'assets/font-awesome-icon-picker/js/fontawesome-iconpicker.js'); ?>"></script>
<script>
    var iconPickerInitialized = false;
    $(function () {
        _formatMenuIconInput();
        $('.dd').nestable({
            maxDepth: 3
        });

        $('.toggle-menu-options').on('click', function (e) {
            e.preventDefault();
            if (iconPickerInitialized == false) {
                $('.icon-picker').iconpicker()
                    .on({
                        'iconpickerSetSourceValue': function (e) {
                            _formatMenuIconInput(e);
                        }
                    })
                iconPickerInitialized = true;
            }
            menu_id = $(this).parents('li').data('id');
            if ($(this).hasClass('main-item-options')) {
                $(this).parents('li').find('.main-item-options[data-menu-options="' + menu_id + '"]').slideToggle();
            } else {
                $(this).parents('li').find('.sub-item-options[data-menu-options="' + menu_id + '"]').slideToggle();
            }
        });
    });

    function save_menu() {
        var items = $('body').find('.dd.active li').not(".dd-list-sub-items li");
        var mainPosition = false;
        $.each(items, function (key, val) {
            var main_menu = $(this);
            if (mainPosition === false) {
                mainPosition = key + 5;
            } else {
                mainPosition = mainPosition + 5;
            }
            main_menu.data('location', main_menu.prop('data-location'));
            main_menu.data('url', main_menu.prop('data-url'));
            main_menu.data('name', main_menu.prop('data-name'));

            main_menu.data('icon', main_menu.find('#icon-' + main_menu.data('id')).val());
            main_menu.data('type', main_menu.find('#type-' + main_menu.data('id')).val());
            main_menu.data('img', main_menu.find('#img-' + main_menu.data('id')).val());
            main_menu.data('disabled', main_menu.find('.is-disabled-main').prop('checked') === true);
            main_menu.data('position', mainPosition);

            // console.log()

            var sub_items = main_menu.find('.dd-list-sub-items li');
            var subPosition = false;
            $.each(sub_items, function (subKey, val) {
                if (subPosition === false) {
                    subPosition = subKey + 5;
                } else {
                    subPosition = subPosition + 5;
                }
                var sub_item = $(this);

                sub_item.data('location', sub_item.prop('data-location'));
                sub_item.data('url', sub_item.prop('data-url'));
                sub_item.data('name', sub_item.prop('data-name'));

                sub_item.data('disabled', sub_item.find('.is-disabled-child').prop('checked') === true);
                sub_item.data('icon', sub_item.find('#icon-' + sub_item.data('id')).val());
                sub_item.data('type', sub_item.find('#type-' + sub_item.data('id')).val());
                sub_item.data('img', sub_item.find('#img-' + sub_item.data('id')).val());
                sub_item.data('position', subPosition);


                var subs = sub_item.find('.dd-list-sub-items li');
                var sub_Position = false;
                $.each(subs, function (sub_Key, val) {
                    if (sub_Position === false) {
                        sub_Position = sub_Key + 5;
                    } else {
                        sub_Position = sub_Position + 5;
                    }
                    var sub = $(this);

                    sub.data('location', sub.prop('data-location'));
                    sub.data('url', sub.prop('data-url'));
                    sub.data('name', sub.prop('data-name'));

                    sub.data('disabled', sub.find('.is-disabled-child').prop('checked') === true);
                    sub.data('icon', sub.find('#icon-' + sub.data('id')).val());
                    sub.data('type', sub.find('#type-' + sub.data('id')).val());
                    sub.data('img', sub.find('#img-' + sub.data('id')).val());
                    sub.data('position', sub_Position);
                });
            });


        });

        var data = {};
        data.options = $('.dd').nestable('serialize');
        $.post(admin_url + 'menu_setup/update_aside_menu', data).done(function () {
            window.location.reload();
        });
    }

</script>
</body>
</html>
