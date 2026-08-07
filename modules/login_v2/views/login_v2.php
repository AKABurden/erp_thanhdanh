<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?=get_option('companyname')?></title>

    <meta name="viewport" content="width=device-width, user-scalable=no">


    <?php echo init_not_head()?>
    <?php echo init_tail()?>
    <link rel="stylesheet" href="<?=base_url('assets/login_v3/style.min.css')?>">
    <style>
        body {
            background: white!important;
        }
    </style>
</head>
<body>
<style>
    .left {
        background: url(<?=base_url('assets/login_v3/img_left3.png')?>);
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }
    input[type="text"],input[type="password"] {
        display: block;
        margin-bottom: 25px;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 15px!important;
        transition: .4s;
        height: 100%;
    }
    .right .option .remember .check:after {
        width: 8px;
        height: 14px;
    }
    .control-label, label {
        color: #737272;
        font-size: 16px;
    }
    input:focus {
        outline: 0;
        border-color: #f15822!important;
        box-shadow: 0 0 0 7px rgb(241 88 34 / 7%)!important;
    }
</style>
<div class="wrap">
    <div class="left">
        <div class="caption hide">
            <h2>Không chỉ là một giải pháp quản lý</h2>
            <span>Làm việc mọi lúc mọi nơi</span>
        </div>
    </div>

    <div class="right">
        <?php echo form_open($this->uri->uri_string(), array('class'=>'login-form')); ?>
            <?php $company_logo = get_option('company_logo'); ?>
            <span class="img-logo-foso" style="text-align: center">
                <img src="<?php echo base_url('uploads/company/'.$company_logo); ?>" width="220">
            </span>
            <h1 class="title-login">Đăng nhập</h1>
            <input class="input_login_v2" id="input_login_email" name="email" required type="text" autocomplete="off" value="" placeholder="Tên đăng nhập">
            <input class="input_login_v2" id="input_login_password" name="password" required type="password" autocomplete="off" value="" placeholder="Mật khẩu">
            <div class="option">
                <label class="remember" for="persistent">
                    <input type="checkbox" id="persistent" name="remember" value="1" checked="checked"> <?php echo _l('admin_auth_login_remember_me'); ?>
                    <span class="check"></span>
                </label>
                <label class="helps">
                    <a href="<?php echo admin_url('authentication/forgot_password'); ?>"><?php echo _l('admin_auth_login_fp'); ?></a>
                </label>
            </div>
            <div class="login-btn-area">
                <input type="submit" value="Đăng nhập" class="submit" autofocus="yes">
            </div>
        <?php echo form_close(); ?>

        <div class="copy-right">FOSOSOFT © 2021</div>
    </div>
</div>


</body>
</html>