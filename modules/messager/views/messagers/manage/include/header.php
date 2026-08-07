<!DOCTYPE html>
<html>
    <head>

        <?php
            $VersionAppFB   =   get_option('VersionAppFB');
            $IdAppFB        =   get_option('IdAppFB');
            $basePath       =   module_dir_url('messager', 'uploads/');
            $baseAssets     =   module_dir_url('messager', 'assets/');
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php init_not_head(); ?>
        <link href="<?= $baseAssets.'messager_fb/css/style_manage.css';?>" rel="stylesheet">
        <link href="<?= $baseAssets.'messager_fb/css/style_loader.css';?>" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
        <?php $this->load->view('messagers/manage/style_css')?>
        <style id="style_append"></style>
    </head>
    <body>
        <div class="container">
            <div class="top-bar">
                <div class="top-logo">
                    <img src="<?= $basePath.'foso_while.png'; ?>">
                    <p>FOSO - Bán hàng Online To Offline</p>
                </div>
                <div class="top-center">
                    <a href="#" data-toggle="tooltip" data-placement="bottom" title="Quản lý">
                        <i class="lnr lnr-home"></i>
                    </a>
                    <a href="<?=admin_url('messager')?>" data-toggle="tooltip" data-placement="bottom" title="Hội thoại">
                        <i class="lnr lnr-bubble"></i>
                    </a>
                    <a href="<?=admin_url('messager/comment')?>" data-toggle="tooltip" data-placement="bottom" title="Bình luận">
                        <i class="lnr lnr-select"></i>
                    </a>
                    <a href="#" data-toggle="tooltip" data-placement="bottom" title="Thiết lập">
                        <i class="lnr lnr-cog"></i>
                    </a>
                </div>
                <div class="top-right">
                    <div class="btn-group dropdown-content">
                        <a type="button" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-sort-desc i_desc"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php $access_token_page = json_decode(base64_decode($_COOKIE['access_token_page']));?>
                            <?php foreach($access_token_page as $key => $value){?>
                                <a class="dropdown-item dropdown_hover" onclick="change_fanpage(this)" type="button" name_fanpage="<?= $value->name?>" access_token="<?= $value->access_token?>" id_fanpage="<?= $value->id?>">
                                    <img class="img_drop" src="https://graph.facebook.com/<?= $value->id?>/picture?height=100&width=100&access_token=<?=$value->access_token?>">
                                    <span class="mleft5"><?= $value->name?></span>
                                </a>
                                <div class="dropdown-divider"></div>
                            <?php }?>
                            <a class="dropdown-item" type="button" onclick="LogoutFB()">

                                <svg _ngcontent-c3="" class="ng-tns-c3-0" style="margin-left: 0px;width: 10px;" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path _ngcontent-c3="" class="ng-tns-c3-0" d="M48 64h132c6.6 0 12 5.4 12 12v8c0 6.6-5.4 12-12 12H48c-8.8 0-16 7.2-16 16v288c0 8.8 7.2 16 16 16h132c6.6 0 12 5.4 12 12v8c0 6.6-5.4 12-12 12H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48zm279 19.5l-7.1 7.1c-4.7 4.7-4.7 12.3 0 17l132 131.4H172c-6.6 0-12 5.4-12 12v10c0 6.6 5.4 12 12 12h279.9L320 404.4c-4.7 4.7-4.7 12.3 0 17l7.1 7.1c4.7 4.7 12.3 4.7 17 0l164.5-164c4.7-4.7 4.7-12.3 0-17L344 83.5c-4.7-4.7-12.3-4.7-17 0z" fill="currentColor">
                                    </path>
                                </svg>
                                <span class="mleft5" style="margin:0;padding:0;">
                                    Đăng xuất
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="fanpage">
                        <img src="https://graph.facebook.com/<?= $_COOKIE['page_active']?>/picture?height=100&width=100&access_token=<?=$_COOKIE['access_token_page_active']?>">
                        <p><?= $_COOKIE['name_page_active']?></p>
                    </div>
                    <div class="profile">
                        <?php if(!empty($_COOKIE['userid'])){?>
                            <img src="https://graph.facebook.com/<?=$_COOKIE['userid']?>/picture?height=100&width=100&access_token=<?=$_COOKIE['access_token_page_active']?>">
                        <?php } ?>
                        <p><?= $_COOKIE['name_user']?></p>
                    </div>
                </div>
            </div>