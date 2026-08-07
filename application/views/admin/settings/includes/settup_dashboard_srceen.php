<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<style>
    .menu-options{
        margin-left: 30px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <?php
                $this->db->where('active', 1);
                $this->db->order_by('soft', 'asc');
                $settup_dashboard_srceen = $this->db->get('tbl_settup_dashboard_srceen')->result_array();
                ?>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="bold"><?php echo _l('Danh sách các màn dashboard'); ?></h4>
                        <hr />
                        <div class="dd active">
                            <?php
                            $i = 1;
                            foreach ($settup_dashboard_srceen as $item) {
                            ?>
                                <li class="dd-item dd3-item main" data-id="<?php echo $item['id']; ?>">
                                    <div class="dd-handle dd3-handle"></div>
                                    <div class="dd3-content"><?php echo _l($item['name']); ?>
                                        <a href="#" class="text-muted toggle-menu-options main-item-options pull-right"><i class="fa fa-cog"></i></a>
                                    </div>
                                    <div class="menu-options main-item-options" style="display:none;" data-menu-options="<?php echo $item['id']; ?>">
                                        <label class="control-label"><?php echo _l('Số giây chờ'); ?></label>
                                        <div class="input-group">
                                            <input type="number" value="<?php echo $item['dwell']; ?>" class="form-control main-item-name" name="name-menu-item-<?php echo $item['id']; ?>">
                                            <span class="input-group-addon"><i class="fa fa-question" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('Số giây chờ khi đang ở 1 trang dashboard'); ?>"></i></span>
                                        </div>
                                        <label class="control-label"><?php echo _l('Ký tự trên bàn phím để nhảy nhanh đến màn'); ?></label>
                                        <div class="input-group">
                                            <input type="text" value="<?php echo $item['keydown']; ?>" class="form-control main-item-name" name="name-menu-item-<?php echo $item['id']; ?>">
                                            <span class="input-group-addon"><i class="fa fa-question" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('Ký tự trên bàn phím để nhảy nhanh đến màn'); ?>"></i></span>
                                        </div>
                                    </div>
                                </li>
                            <?php $i++;
                            } ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-nestable/jquery.nestable.js"></script>
<script>
    $(document).ready(function() {
        _formatMenuIconInput();
        $('.dd').nestable({
            maxDepth: 2
        });
        $('.toggle-menu-options').on('click', function(e) {
            e.preventDefault();
            menu_id = $(this).parents('li').data('id');
            if ($(this).hasClass('main-item-options')) {
                $(this).parents('li').find('.main-item-options[data-menu-options="' + menu_id + '"]').slideToggle();
            } else {
                $(this).parents('li').find('.sub-item-options[data-menu-options="' + menu_id + '"]').slideToggle();
            }
        });

    });


    function save_menu() {
        var items = $('.dd.active').find('li.main');
        $.each(items, function() {
            var main_menu = $(this);
            var name = $(this).find('.main-item-options input.main-item-name').val();
            var url = $(this).find('.main-item-options input.main-item-url').val();
            var icon = $(this).find('.main-item-icon').val();
            var type = $(this).find('.main-item-options select.main-item-type').val();
            main_menu.data('name', name);
            main_menu.data('url', url);
            main_menu.data('permission', $(this).data('permission'));
            main_menu.data('icon', icon);
            main_menu.data('type', type);

        });

        var sub_items = $('.dd.active li.sub-items');
        $.each(sub_items, function() {
            var sub_item = $(this);
            var name = $(this).find('.sub-item-options input.sub-item-name').val();
            var url = $(this).find('.sub-item-options input.sub-item-url').val();
            var icon = $(this).find('.main-item-icon').val();
            sub_item.data('name', name);
            sub_item.data('url', url);
            sub_item.data('permission', $(this).data('permission'));
            sub_item.data('icon', icon);
        });

        var aside_menu_active = $('.dd.active').nestable('serialize');
        /* Inactive */
        var items_inactive = $('.dd.inactive').find('li.main');
        $.each(items_inactive, function() {
            var main_menu = $(this);
            var name = $(this).find('.main-item-options input.main-item-name').val();
            var url = $(this).find('.main-item-options input.main-item-url').val();
            var icon = $(this).find('.main-item-icon').val();
            main_menu.data('name', name);
            main_menu.data('url', url);
            main_menu.data('permission', $(this).data('permission'));
            main_menu.data('icon', icon);

        });

        var sub_items = $('.dd.inactive li.sub-items');
        $.each(sub_items, function() {
            var sub_item = $(this);
            var name = $(this).find('.sub-item-options input.sub-item-name').val();
            var url = $(this).find('.sub-item-options input.sub-item-url').val();
            var icon = $(this).find('.main-item-icon').val();
            sub_item.data('name', name);
            sub_item.data('url', url);
            sub_item.data('permission', $(this).data('permission'));
            sub_item.data('icon', icon);
        });

        var aside_menu_inactive = $('.dd.inactive').nestable('serialize');
        var data = {};
        data.active = aside_menu_active;
        data.inactive = aside_menu_inactive;
        $.post(admin_url + 'utilities/update_aside_menu', data).done(function() {
            window.location.reload();
        })
    }
</script>
</body>

</html>