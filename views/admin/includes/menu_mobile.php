<style type="text/css">
    @media (max-width: 767px) {
        .app-menu-item-mobile img {
            width: 25px;
        }
        .app-menu-item-mobile {
            width: 100%;
        }
        .wap-off-mobile {
            background: linear-gradient(to right, #6322aa 0%, #226ca9 37%, #3b8293 100%);
            padding: 0px;
            width: calc(300px - 20px);
            margin: 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wrap-img-mobile {
            float: left;
            margin-left: 10px;
        }
        .app-menu-item-mobile span {
            color: #fff;
            margin-left: 10px;
        }
    }
</style>

<?php
    $aside_menu_active = json_decode(get_option('aside_menu_active'));
    $list_title = array();
    if(!empty($aside_menu_active))
    {
        foreach($aside_menu_active as $key => $value)
        {
            if(!empty($value->type))
            {
                $value->object = $key;
                $list_title[1][] = $value;
            }
        }
    }
?>
<div class="menu_mobile">
	<div class="app-menu-group-mobile">
		<div class="content-menu-v2-mobile">
            <?php
                if(!empty(!empty($list_title[1])))
                {
                    foreach($list_title[1] as $key => $value)
                    {?>
                        <div class="wap-off-mobile <?=empty($value->off) ? (has_permission_parent($value->parent)? '' : 'no-event' ) : 'no-event'?>">
                            <a class="app-menu-item-mobile <?=empty($value->off) ? (has_permission_parent($value->parent)? '' : 'no-event' ) : 'no-event'?> <?=empty($value->url) ? 'change_menu_child' : ''?>" <?=!empty($value->url) ? 'href="'.admin_url($value->url).'"' : ' object = "'.$value->object.'" '?>>
                                <?php if(!empty($value->img)){?>
                                    <div class="wrap-img-mobile">
                                        <img src="<?=empty($value->off) ? (has_permission_parent($value->parent)? base_url($value->img) : base_url($value->img_black)): base_url($value->img_black) ?>">
                                    </div>
                                <?php } ?>
                                <span><?php echo ucwords(_l($value->name, '', false)); ?></span>
                                <div class="clearfix"></div>
                            </a>
                        </div>
                <?php }
                }
            ?>
		</div>
	</div>
</div>