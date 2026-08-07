<!DOCTYPE html>
<html>
<head>
    <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            background: -webkit-linear-gradient(right, #1874d2, #21d4fd, #21d4fd, #1874d2);
            background: -o-linear-gradient(right, #1874d2, #21d4fd, #21d4fd, #1874d2);
            background: -moz-linear-gradient(right, #1874d2, #21d4fd, #21d4fd, #1874d2);
            background: linear-gradient(right, #1874d2, #21d4fd, #21d4fd, #1874d2);
        }
        
        html {
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
        
        body {
            font: normal 75% Arial, Helvetica, sans-serif;
        }
        
        canvas {
            display: block;
            vertical-align: bottom;
        }
        a {
            color: #000;
            text-decoration: none;
        }
        a:hover {
            color: #000;
            text-decoration: none;
        }
        .hide {
            display: none;
        }
        .mleft20 {
            margin-left: 20px;
        }
        .pull-right {
            float: right;
        }
        #ms, #fps {
            display: none !important;
        }
        .count-particles {
            background: #000022;
            position: absolute;
            top: 48px;
            left: 0;
            width: 80px;
            color: #13E8E9;
            font-size: .8em;
            text-align: left;
            text-indent: 4px;
            line-height: 14px;
            padding-bottom: 2px;
            font-family: Helvetica, Arial, sans-serif;
            font-weight: bold;
        }
        
        .js-count-particles {
            font-size: 1.1em;
        }
        
        #stats,
        .count-particles {
            -webkit-user-select: none;
            margin-top: 5px;
            margin-left: 5px;
        }
        
        #stats {
            border-radius: 3px 3px 0 0;
            overflow: hidden;
        }
        
        .count-particles {
            border-radius: 0 0 3px 3px;
        }
        
        #particles-js {
            width: 100%;
            height: 100%;
            background: -webkit-linear-gradient(to right, #226faa 0%, #2989d8 37%, #72c0d3 100%);
            background: -o-linear-gradient(to right, #226faa 0%, #2989d8 37%, #72c0d3 100%);
            background: -moz-linear-gradient(to right, #226faa 0%, #2989d8 37%, #72c0d3 100%);
            background: linear-gradient(to right, #226faa 0%, #2989d8 37%, #72c0d3 100%);
            /*background: linear-gradient(right, #1874d2, #21d4fd, #21d4fd, #1874d2);*/
            background-position: 50% 50%;
            background-repeat: no-repeat;
            position: relative;
        }
        .login-container {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .wrap-login {
            float: right;
            width: 400px;
            height: 530px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            padding: 0px 55px 33px 50px;
            box-shadow: 0 5px 10px 2px rgba(0, 0, 0, 0.1);
            -moz-box-shadow: 0 5px 10px 2px rgba(0, 0, 0, 0.1);
            -webkit-box-shadow: 0 5px 10px 2px rgba(0, 0, 0, 0.1);
            -o-box-shadow: 0 5px 10px 2px rgba(0, 0, 0, 0.1);
            -ms-box-shadow: 0 5px 10px 2px rgba(0, 0, 0, 0.1);
        }
        .login-form {
            width: 100%;
        }
        .wrap-login-welcome {
            display: block;
            text-transform: uppercase;
            font-size: 20px;
            font-weight: 700;
            color: #333333;
            line-height: 1.2;
            text-align: center;
            padding-bottom: 40px;
        }
        .img-logo-foso {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100px;
        }
        .input-login {
            width: 100%;
            position: relative;
            margin-bottom: 35px;
        }
        .input-login input{
            font-size: 14px;
            margin-top: 10px;
            border-radius: 35px;
            color: #555555;
            line-height: 1.2;
            display: block;
            width: 100%;
            height: 45px;
            outline: none;
            border: 1px solid #E8EEF0;
            background: #F6F8FA;
            padding: 0 25px;
        }
        .container-login-form {
            display: -webkit-box;
            display: -webkit-flex;
            display: -moz-box;
            display: -ms-flexbox;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding-top: 15px;
        }
        .wrap-login-form-btn {
            width: 100%;
            display: block;
            position: relative;
            z-index: 1;
            border-radius: 25px;
            overflow: hidden;
            margin: 0 auto;
        }
        .login-form-bgbtn {
            position: absolute;
            z-index: -1;
            width: 300%;
            height: 100%;
            background: #a64bf4;
            background: -webkit-linear-gradient(right, #21d4fd, #4439ef, #21d4fd, #4439ef);
            background: -o-linear-gradient(right, #21d4fd, #4439ef, #21d4fd, #4439ef);
            background: -moz-linear-gradient(right, #21d4fd, #4439ef, #21d4fd, #4439ef);
            background: linear-gradient(right, #21d4fd, #4439ef, #21d4fd, #4439ef);
            top: 0;
            left: -100%;
            -webkit-transition: all 0.5s;
            -o-transition: all 0.5s;
            -moz-transition: all 0.5s;
            transition: all 0.5s;
        }
        .login-form-btn {
            outline: none;
            border: none;
            background: transparent;
            font-size: 15px;
            color: #fff;
            line-height: 1.2;
            text-transform: uppercase;
            display: -webkit-box;
            display: -webkit-flex;
            display: -moz-box;
            display: -ms-flexbox;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 20px;
            width: 101%;
            height: 50px;
        }
        .wrap-login-form-btn:hover .login-form-bgbtn {
            left: 0%;
            cursor: pointer;
        }
        .login-footer {
            position: absolute;
            z-index: 999;
            display: flex;
            justify-content: left;
            align-items: center;
            bottom: 0;
            background: #fff;
            width: 100%;
            height: 40px;
        }
        .footer-container {
            height: 100%;
            width: calc(100% - 150px);
        }
        .footer-left {
            float: left;
            display: flex;
            align-items: center;
            width: 30%;
            height: 100%;
        }
        .footer-left img {
            height: 36px;
            margin-left: 20px;
        }
        .footer-center {
            float: left;
            text-transform: uppercase;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            width: calc(70% - 400px);
            height: 100%;
            color: #123e60;
        }
        .footer-right {
            float: left;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 400px;
            height: 100%;
        }
        
        /*button footer*/
        .btn-footer {
            cursor: pointer;
            background: transparent; outline: none;
            position: relative;
            border: 1px solid #adadad;
            padding: 5px 20px;
            overflow: hidden;
        }
        .btn-footer:hover:before {
            opacity: 1;
            transform: translate(0,0);
        }
        .btn-footer:before {
            content: attr(data-hover);
            position: absolute;
            left: 0;
            width: 100%;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 1px;
            font-weight: 600;
            font-size: .8em;
            color: #afafaf;
            opacity: 0;
            transform: translate(-100%,0);
            transition: all .3s ease-in-out;
        }
        .btn-footer:hover div{
            opacity: 0;
            transform: translate(100%,0);
        }
        .btn-footer div {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            font-size: .8em;
            transition: all .3s ease-in-out;
        }
        /*end*/

        .login-content {
            width: calc(100% - 300px);
        }
        .wap-slide {
            float: left;
            width: calc(100% - 400px);
        }
        .change-content {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            height: 400px;
            margin-bottom: 20px;
            margin-right: 40px;
        }
        .content-slide {
            position: relative;
            margin-right: 50px;
            display: flex;
            justify-content: center;
            height: 110px;
            background: #fff0;
        }
        .wap-conten-slide {
            display: flex;
            align-items: flex-end;
            position: relative;
            width: 425px;
            overflow: hidden;
        }
        .slide-child {
            cursor: pointer;
            padding: 2px;
            margin: 0 2px;
            background: #fff;
            position: absolute;
            border: 1px solid #5a5a5a;
            width: calc(25% - 8px);
            transition: all 0.5s;
            -webkit-transition: all 0.5s;

        }
        .slide-child.active {
            top: 8px;
            box-shadow: 0 5px 10px -5px rgba(0, 0, 0, 1);
            -moz-box-shadow: 0 5px 10px -5px rgba(0, 0, 0, 1);
            -webkit-box-shadow: 0 5px 10px -5px rgba(0, 0, 0, 1);
            -o-box-shadow: 0 5px 10px -5px rgba(0, 0, 0, 1);
            -ms-box-shadow: 0 5px 10px -5px rgba(0, 0, 0, 1);
        }
        .content-child {
            margin-left: 50px;
            color: #000;
            display: flex;
            align-items: center;
            float: left;
            width: calc(70% - 50px);
            height: 100%;
        }
        .img-child {
            position: absolute;
            margin-right: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            float: left;
            width: calc(100%);
            height: 100%;
            transition: all 0.5s;
            -webkit-transition: all 0.5s;
        }
        .img-child img {
            width: 100%;
            height: 400px;
            border-radius: 20px;
        }
        .content-banner img {
            width: 93px;
            height: 89px;
        }
        .next {
            position: absolute;
            cursor: pointer;
            color: #fff;
            top: 20%;
            font-size: 55px;
            font-weight: bold;
        }
        .next:hover {
            color: #c2c2c2;
        }
        .next-left {
            left: 0;
        }
        .next-right {
            right: 0;
        }
        .no-event {
            cursor: no-drop;
            color: #c2c2c2;
        }
        .forcus-input {
            color: #717171;
        }
        @media only screen and (max-width: 768px) {
            .login-content {
                width: 100%;
                height: 100%;
            }
            .wrap-login {
                margin-right: 0px;
                height: 100%;
                width: 100%;
            }
            .login-footer {
                display: none;
            }
            .wap-slide {
                display: none;
            }
        }
    </style>
    <?php if(get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != ''){ ?>
      <script src='https://www.google.com/recaptcha/api.js'></script>
    <?php } ?>
    <?php if(file_exists(FCPATH.'assets/css/custom.css')){ ?>
      <link href="<?php echo base_url('assets/css/custom.css'); ?>" rel="stylesheet" id="custom-css">
    <?php } ?>
    <?php hooks()->do_action('app_admin_authentication_head'); ?>
</head>
<body>
<div class="css_slide"></div>
<div id="particles-js">
    <div class="login-container">
        <div class="login-content">
            <div class="wap-slide">
                <div class="change-content">
                    <?php $get_slide_banner = get_table_where('tbl_slideshow',array(),'stt ASC'); ?>
                    <?php foreach ($get_slide_banner as $key => $value) { ?>
                        <?php $left = $key*100; ?>
                        <style>
                            .img-child[data-child="<?=$key?>"] {
                                left: <?=$left.'%'?>;
                            }
                        </style>
                        <div class="img-child" data-child="<?=$key?>" data-left="<?=$left?>">
                            <span><img src="<?=base_url($value['image'])?>"></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="content-slide">
                    <div class="next next-left no-event">&#10094;</div>
                    <div class="next next-right">&#10095;</div>
                    <div class="wap-conten-slide">
                        <?php $get_slide = get_table_where('tbl_slideshow',array(),'stt ASC'); ?>
                        <?php foreach ($get_slide as $key => $value) { ?>
                            <?php $left = $key*25; ?>
                            <style>
                                .slide-child[data-child="<?=$key?>"] {
                                    left: <?=$left.'%'?>;
                                }
                            </style>
                            <div class="slide-child <?=($key == 0) ? 'active' : ''?>" data-child="<?=$key?>" data-left="<?=$left?>">
                                <div class="content-banner">
                                    <img src="<?=base_url($value['image'])?>">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="wrap-login">
                <?php echo form_open($this->uri->uri_string(),array('class'=>'login-form')); ?>
                <?php hooks()->do_action('after_admin_login_form_start'); ?>
                    <?php $company_logo = get_option('company_logo'); ?>
                    <span class="img-logo-foso">
                        <img src="<?php echo base_url('uploads/company/'.$company_logo); ?>" width="220">
                    </span>
                    <span class="wrap-login-welcome">đăng nhập hệ thống</span>
                    <div class="input-login">
                        <span class="forcus-input">Tên đăng nhập</span>
                        <input class="input_login_v2" id="input_login_email" name="email" type="text" autocomplete="off" value="">
                    </div>
                    <div class="input-login">
                        <span class="forcus-input">Mật khẩu</span>
                        <input class="input_login_v2" id="input_login_password" name="password" type="password" autocomplete="off" value="">
                    </div>
                    <div class="checkbox">
                        <label for="remember">
                            <input type="checkbox" id="remember" name="remember"> <?php echo _l('admin_auth_login_remember_me'); ?>
                        </label>
                    </div>
                    <div class="form-group">
                        <a href="<?php echo admin_url('authentication/forgot_password'); ?>"><?php echo _l('admin_auth_login_fp'); ?></a>
                    </div>
                    <div class="container-login-form">
                        <div class="wrap-login-form-btn">
                            <div class="login-form-bgbtn"></div>
                            <button type="submit" class="login-form-btn"><?php echo _l('admin_auth_login_button'); ?></button>
                        </div>
                    </div>
                    
                    <?php if(get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != ''){ ?>
                        <div class="g-recaptcha" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>
                    <?php } ?>
                    <?php hooks()->do_action('before_admin_login_form_close'); ?>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <div class="login-footer">
        <div class="footer-container">
            <div class="footer-left">
                <img src="<?=base_url('uploads/logo-foso1.png')?>">
            </div>
            <div class="footer-center">
                www.fososoft.com
            </div>
            <div class="footer-right">
                <a href="https://www.facebook.com/groups/2165905850131379/" target="_blank">
                    <div class="btn-footer" data-hover="Đi ngay!">
                        <div>Group trao đổi</div>
                    </div>
                </a>
                <a href="https://fososoft-1.gitbook.io/help-foso/" target="_blank">
                    <div class="btn-footer mleft20" data-hover="Đi ngay!">
                        <div>HƯỚNG DẪN SỬ DỤNG</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script>
    var dem_temp_delay = 5;
    var dem_temp = 1;
    var stt_slide = 1;
    $(function(){
        if(<?=count($get_slide)?> > 1) {
            setInterval(sub_delay, 1000);
        }
    });

    function sub_delay() {
        dem_temp_delay--;
        if(<?=count($get_slide)?> > 1) {
            if(dem_temp_delay == 0) {
                if(stt_slide == <?=count($get_slide)?>) {
                    stt_slide = 0;
                    $('div[data-child="'+stt_slide+'"]').trigger('click');
                    var all_slide = $('.slide-child');
                    $.each(all_slide, function(i, v){
                        var left = i*25;
                        $(v).css({"left":left+"%"});
                        $(v).attr('data-left',left);
                    });
                    if(<?=count($get_slide)?> > 4) {
                        $('.next-right').removeClass('no-event');
                    }
                    $('.next-left').addClass('no-event');
                }
                else {
                    $('div[data-child="'+stt_slide+'"]').trigger('click');
                    if(stt_slide > 3) {
                        var all_slide = $('.slide-child');
                        $.each(all_slide, function(i, v){
                            var left = Number($(v).attr('data-left'));
                            left = left - 25;
                            $(v).css({"left":left+"%"});

                            $(v).attr('data-left',left);
                        });
                        $('.next-left').removeClass('no-event');
                    }
                }
                stt_slide++;
                dem_temp_delay = 5;
            }
        }
    }

    $(function(){
        if(<?=count($get_slide)?> < 4) {
            $('.next-right').addClass('no-event');
        }
    });
    $(document).on('click','.slide-child', function (e) {
        dem_temp_delay = 7;
        $('.slide-child').removeClass('active');
        var target = $(e.currentTarget);
        target.addClass('active');

        var child = target.attr('data-child');
        var left = Number(child) * 100;
        var all_slide = $('.img-child');
        $.each(all_slide, function(i, v){
            var data_left = Number($(v).attr('data-left'));
            data_left = data_left - Number(left);
            $(v).css({"left":data_left+"%"});
        });
    });

    $(document).on('click','.next-left', function (e) {
        if(<?=count($get_slide)?> > 4) {
            dem_temp_delay = 7;
            $('.next-right').removeClass('no-event');
            
            if(dem_temp > 1) {
                var all_slide = $('.slide-child');
                $.each(all_slide, function(i, v){
                    var left = Number($(v).attr('data-left'));
                    left = left + 25;
                    $(v).css({"left":left+"%"});

                    $(v).attr('data-left',left);
                });
                dem_temp--;
            }
            if (dem_temp == 1) {
                $('.next-left').addClass('no-event');
            }
        }
    });

    $(document).on('click','.next-right', function (e) {
        if(<?=count($get_slide)?> > 4) {
            dem_temp_delay = 7;
            var total = <?=count($get_slide)?>;
            total = total - 3;
            $('.next-left').removeClass('no-event');

            if(dem_temp < total) {
                var all_slide = $('.slide-child');
                $.each(all_slide, function(i, v){
                    var left = Number($(v).attr('data-left'));
                    left = left - 25;
                    $(v).css({"left":left+"%"});

                    $(v).attr('data-left',left);
                });
                dem_temp++;
            }
            if(dem_temp == total) {
                $('.next-right').addClass('no-event');
            }
        }
    });
    var pJS = function(tag_id, params) {
        var canvas_el = document.querySelector('#' + tag_id + ' > .particles-js-canvas-el');

        /* particles.js variables with default values */
        this.pJS = {
            canvas: {
                el: canvas_el,
                w: canvas_el.offsetWidth,
                h: canvas_el.offsetHeight
            },
            particles: {
                number: {
                    value: 400,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: '#fff'
                },
                shape: {
                    type: 'circle',
                    stroke: {
                        width: 0,
                        color: '#ff0000'
                    },
                    polygon: {
                        nb_sides: 5
                    },
                    image: {
                        src: '',
                        width: 100,
                        height: 100
                    }
                },
                opacity: {
                    value: 1,
                    random: false,
                    anim: {
                        enable: false,
                        speed: 2,
                        opacity_min: 0,
                        sync: false
                    }
                },
                size: {
                    value: 20,
                    random: false,
                    anim: {
                        enable: false,
                        speed: 20,
                        size_min: 0,
                        sync: false
                    }
                },
                line_linked: {
                    enable: true,
                    distance: 100,
                    color: '#fff',
                    opacity: 1,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: 'none',
                    random: false,
                    straight: false,
                    out_mode: 'out',
                    bounce: false,
                    attract: {
                        enable: false,
                        rotateX: 3000,
                        rotateY: 3000
                    }
                },
                array: []
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: {
                        enable: true,
                        mode: 'grab'
                    },
                    onclick: {
                        enable: true,
                        mode: 'push'
                    },
                    resize: true
                },
                modes: {
                    grab: {
                        distance: 100,
                        line_linked: {
                            opacity: 1
                        }
                    },
                    bubble: {
                        distance: 200,
                        size: 80,
                        duration: 0.4
                    },
                    repulse: {
                        distance: 200,
                        duration: 0.4
                    },
                    push: {
                        particles_nb: 4
                    },
                    remove: {
                        particles_nb: 2
                    }
                },
                mouse: {}
            },
            retina_detect: false,
            fn: {
                interact: {},
                modes: {},
                vendors: {}
            },
            tmp: {}
        };

        var pJS = this.pJS;

        /* params settings */
        if (params) {
            Object.deepExtend(pJS, params);
        }

        pJS.tmp.obj = {
            size_value: pJS.particles.size.value,
            size_anim_speed: pJS.particles.size.anim.speed,
            move_speed: pJS.particles.move.speed,
            line_linked_distance: pJS.particles.line_linked.distance,
            line_linked_width: pJS.particles.line_linked.width,
            mode_grab_distance: pJS.interactivity.modes.grab.distance,
            mode_bubble_distance: pJS.interactivity.modes.bubble.distance,
            mode_bubble_size: pJS.interactivity.modes.bubble.size,
            mode_repulse_distance: pJS.interactivity.modes.repulse.distance
        };


        pJS.fn.retinaInit = function() {

            if (pJS.retina_detect && window.devicePixelRatio > 1) {
                pJS.canvas.pxratio = window.devicePixelRatio;
                pJS.tmp.retina = true;
            } else {
                pJS.canvas.pxratio = 1;
                pJS.tmp.retina = false;
            }

            pJS.canvas.w = pJS.canvas.el.offsetWidth * pJS.canvas.pxratio;
            pJS.canvas.h = pJS.canvas.el.offsetHeight * pJS.canvas.pxratio;

            pJS.particles.size.value = pJS.tmp.obj.size_value * pJS.canvas.pxratio;
            pJS.particles.size.anim.speed = pJS.tmp.obj.size_anim_speed * pJS.canvas.pxratio;
            pJS.particles.move.speed = pJS.tmp.obj.move_speed * pJS.canvas.pxratio;
            pJS.particles.line_linked.distance = pJS.tmp.obj.line_linked_distance * pJS.canvas.pxratio;
            pJS.interactivity.modes.grab.distance = pJS.tmp.obj.mode_grab_distance * pJS.canvas.pxratio;
            pJS.interactivity.modes.bubble.distance = pJS.tmp.obj.mode_bubble_distance * pJS.canvas.pxratio;
            pJS.particles.line_linked.width = pJS.tmp.obj.line_linked_width * pJS.canvas.pxratio;
            pJS.interactivity.modes.bubble.size = pJS.tmp.obj.mode_bubble_size * pJS.canvas.pxratio;
            pJS.interactivity.modes.repulse.distance = pJS.tmp.obj.mode_repulse_distance * pJS.canvas.pxratio;

        };



        /* ---------- pJS functions - canvas ------------ */

        pJS.fn.canvasInit = function() {
            pJS.canvas.ctx = pJS.canvas.el.getContext('2d');
        };

        pJS.fn.canvasSize = function() {

            pJS.canvas.el.width = pJS.canvas.w;
            pJS.canvas.el.height = pJS.canvas.h;

            if (pJS && pJS.interactivity.events.resize) {

                window.addEventListener('resize', function() {

                    pJS.canvas.w = pJS.canvas.el.offsetWidth;
                    pJS.canvas.h = pJS.canvas.el.offsetHeight;

                    /* resize canvas */
                    if (pJS.tmp.retina) {
                        pJS.canvas.w *= pJS.canvas.pxratio;
                        pJS.canvas.h *= pJS.canvas.pxratio;
                    }

                    pJS.canvas.el.width = pJS.canvas.w;
                    pJS.canvas.el.height = pJS.canvas.h;

                    /* repaint canvas on anim disabled */
                    if (!pJS.particles.move.enable) {
                        pJS.fn.particlesEmpty();
                        pJS.fn.particlesCreate();
                        pJS.fn.particlesDraw();
                        pJS.fn.vendors.densityAutoParticles();
                    }

                    /* density particles enabled */
                    pJS.fn.vendors.densityAutoParticles();

                });

            }

        };


        pJS.fn.canvasPaint = function() {
            pJS.canvas.ctx.fillRect(0, 0, pJS.canvas.w, pJS.canvas.h);
        };

        pJS.fn.canvasClear = function() {
            pJS.canvas.ctx.clearRect(0, 0, pJS.canvas.w, pJS.canvas.h);
        };


        /* --------- pJS functions - particles ----------- */

        pJS.fn.particle = function(color, opacity, position) {

            /* size */
            this.radius = (pJS.particles.size.random ? Math.random() : 1) * pJS.particles.size.value;
            if (pJS.particles.size.anim.enable) {
                this.size_status = false;
                this.vs = pJS.particles.size.anim.speed / 100;
                if (!pJS.particles.size.anim.sync) {
                    this.vs = this.vs * Math.random();
                }
            }

            /* position */
            this.x = position ? position.x : Math.random() * pJS.canvas.w;
            this.y = position ? position.y : Math.random() * pJS.canvas.h;

            /* check position  - into the canvas */
            if (this.x > pJS.canvas.w - this.radius * 2) this.x = this.x - this.radius;
            else if (this.x < this.radius * 2) this.x = this.x + this.radius;
            if (this.y > pJS.canvas.h - this.radius * 2) this.y = this.y - this.radius;
            else if (this.y < this.radius * 2) this.y = this.y + this.radius;

            /* check position - avoid overlap */
            if (pJS.particles.move.bounce) {
                pJS.fn.vendors.checkOverlap(this, position);
            }

            /* color */
            this.color = {};
            if (typeof(color.value) == 'object') {

                if (color.value instanceof Array) {
                    var color_selected = color.value[Math.floor(Math.random() * pJS.particles.color.value.length)];
                    this.color.rgb = hexToRgb(color_selected);
                } else {
                    if (color.value.r != undefined && color.value.g != undefined && color.value.b != undefined) {
                        this.color.rgb = {
                            r: color.value.r,
                            g: color.value.g,
                            b: color.value.b
                        }
                    }
                    if (color.value.h != undefined && color.value.s != undefined && color.value.l != undefined) {
                        this.color.hsl = {
                            h: color.value.h,
                            s: color.value.s,
                            l: color.value.l
                        }
                    }
                }

            } else if (color.value == 'random') {
                this.color.rgb = {
                    r: (Math.floor(Math.random() * (255 - 0 + 1)) + 0),
                    g: (Math.floor(Math.random() * (255 - 0 + 1)) + 0),
                    b: (Math.floor(Math.random() * (255 - 0 + 1)) + 0)
                }
            } else if (typeof(color.value) == 'string') {
                this.color = color;
                this.color.rgb = hexToRgb(this.color.value);
            }

            /* opacity */
            this.opacity = (pJS.particles.opacity.random ? Math.random() : 1) * pJS.particles.opacity.value;
            if (pJS.particles.opacity.anim.enable) {
                this.opacity_status = false;
                this.vo = pJS.particles.opacity.anim.speed / 100;
                if (!pJS.particles.opacity.anim.sync) {
                    this.vo = this.vo * Math.random();
                }
            }

            /* animation - velocity for speed */
            var velbase = {}
            switch (pJS.particles.move.direction) {
                case 'top':
                    velbase = {
                        x: 0,
                        y: -1
                    };
                    break;
                case 'top-right':
                    velbase = {
                        x: 0.5,
                        y: -0.5
                    };
                    break;
                case 'right':
                    velbase = {
                        x: 1,
                        y: -0
                    };
                    break;
                case 'bottom-right':
                    velbase = {
                        x: 0.5,
                        y: 0.5
                    };
                    break;
                case 'bottom':
                    velbase = {
                        x: 0,
                        y: 1
                    };
                    break;
                case 'bottom-left':
                    velbase = {
                        x: -0.5,
                        y: 1
                    };
                    break;
                case 'left':
                    velbase = {
                        x: -1,
                        y: 0
                    };
                    break;
                case 'top-left':
                    velbase = {
                        x: -0.5,
                        y: -0.5
                    };
                    break;
                default:
                    velbase = {
                        x: 0,
                        y: 0
                    };
                    break;
            }

            if (pJS.particles.move.straight) {
                this.vx = velbase.x;
                this.vy = velbase.y;
                if (pJS.particles.move.random) {
                    this.vx = this.vx * (Math.random());
                    this.vy = this.vy * (Math.random());
                }
            } else {
                this.vx = velbase.x + Math.random() - 0.5;
                this.vy = velbase.y + Math.random() - 0.5;
            }

            // var theta = 2.0 * Math.PI * Math.random();
            // this.vx = Math.cos(theta);
            // this.vy = Math.sin(theta);

            this.vx_i = this.vx;
            this.vy_i = this.vy;



            /* if shape is image */

            var shape_type = pJS.particles.shape.type;
            if (typeof(shape_type) == 'object') {
                if (shape_type instanceof Array) {
                    var shape_selected = shape_type[Math.floor(Math.random() * shape_type.length)];
                    this.shape = shape_selected;
                }
            } else {
                this.shape = shape_type;
            }

            if (this.shape == 'image') {
                var sh = pJS.particles.shape;
                this.img = {
                    src: sh.image.src,
                    ratio: sh.image.width / sh.image.height
                }
                if (!this.img.ratio) this.img.ratio = 1;
                if (pJS.tmp.img_type == 'svg' && pJS.tmp.source_svg != undefined) {
                    pJS.fn.vendors.createSvgImg(this);
                    if (pJS.tmp.pushing) {
                        this.img.loaded = false;
                    }
                }
            }



        };


        pJS.fn.particle.prototype.draw = function() {

            var p = this;

            if (p.radius_bubble != undefined) {
                var radius = p.radius_bubble;
            } else {
                var radius = p.radius;
            }

            if (p.opacity_bubble != undefined) {
                var opacity = p.opacity_bubble;
            } else {
                var opacity = p.opacity;
            }

            if (p.color.rgb) {
                var color_value = 'rgba(' + p.color.rgb.r + ',' + p.color.rgb.g + ',' + p.color.rgb.b + ',' + opacity + ')';
            } else {
                var color_value = 'hsla(' + p.color.hsl.h + ',' + p.color.hsl.s + '%,' + p.color.hsl.l + '%,' + opacity + ')';
            }

            pJS.canvas.ctx.fillStyle = color_value;
            pJS.canvas.ctx.beginPath();

            switch (p.shape) {

                case 'circle':
                    pJS.canvas.ctx.arc(p.x, p.y, radius, 0, Math.PI * 2, false);
                    break;

                case 'edge':
                    pJS.canvas.ctx.rect(p.x - radius, p.y - radius, radius * 2, radius * 2);
                    break;

                case 'triangle':
                    pJS.fn.vendors.drawShape(pJS.canvas.ctx, p.x - radius, p.y + radius / 1.66, radius * 2, 3, 2);
                    break;

                case 'polygon':
                    pJS.fn.vendors.drawShape(
                        pJS.canvas.ctx,
                        p.x - radius / (pJS.particles.shape.polygon.nb_sides / 3.5), // startX
                        p.y - radius / (2.66 / 3.5), // startY
                        radius * 2.66 / (pJS.particles.shape.polygon.nb_sides / 3), // sideLength
                        pJS.particles.shape.polygon.nb_sides, // sideCountNumerator
                        1 // sideCountDenominator
                    );
                    break;

                case 'star':
                    pJS.fn.vendors.drawShape(
                        pJS.canvas.ctx,
                        p.x - radius * 2 / (pJS.particles.shape.polygon.nb_sides / 4), // startX
                        p.y - radius / (2 * 2.66 / 3.5), // startY
                        radius * 2 * 2.66 / (pJS.particles.shape.polygon.nb_sides / 3), // sideLength
                        pJS.particles.shape.polygon.nb_sides, // sideCountNumerator
                        2 // sideCountDenominator
                    );
                    break;

                case 'image':

                    function draw() {
                        pJS.canvas.ctx.drawImage(
                            img_obj,
                            p.x - radius,
                            p.y - radius,
                            radius * 2,
                            radius * 2 / p.img.ratio
                        );
                    }

                    if (pJS.tmp.img_type == 'svg') {
                        var img_obj = p.img.obj;
                    } else {
                        var img_obj = pJS.tmp.img_obj;
                    }

                    if (img_obj) {
                        draw();
                    }

                    break;

            }

            pJS.canvas.ctx.closePath();

            if (pJS.particles.shape.stroke.width > 0) {
                pJS.canvas.ctx.strokeStyle = pJS.particles.shape.stroke.color;
                pJS.canvas.ctx.lineWidth = pJS.particles.shape.stroke.width;
                pJS.canvas.ctx.stroke();
            }

            pJS.canvas.ctx.fill();

        };


        pJS.fn.particlesCreate = function() {
            for (var i = 0; i < pJS.particles.number.value; i++) {
                pJS.particles.array.push(new pJS.fn.particle(pJS.particles.color, pJS.particles.opacity.value));
            }
        };

        pJS.fn.particlesUpdate = function() {

            for (var i = 0; i < pJS.particles.array.length; i++) {

                /* the particle */
                var p = pJS.particles.array[i];

                // var d = ( dx = pJS.interactivity.mouse.click_pos_x - p.x ) * dx + ( dy = pJS.interactivity.mouse.click_pos_y - p.y ) * dy;
                // var f = -BANG_SIZE / d;
                // if ( d < BANG_SIZE ) {
                //     var t = Math.atan2( dy, dx );
                //     p.vx = f * Math.cos(t);
                //     p.vy = f * Math.sin(t);
                // }

                /* move the particle */
                if (pJS.particles.move.enable) {
                    var ms = pJS.particles.move.speed / 2;
                    p.x += p.vx * ms;
                    p.y += p.vy * ms;
                }

                /* change opacity status */
                if (pJS.particles.opacity.anim.enable) {
                    if (p.opacity_status == true) {
                        if (p.opacity >= pJS.particles.opacity.value) p.opacity_status = false;
                        p.opacity += p.vo;
                    } else {
                        if (p.opacity <= pJS.particles.opacity.anim.opacity_min) p.opacity_status = true;
                        p.opacity -= p.vo;
                    }
                    if (p.opacity < 0) p.opacity = 0;
                }

                /* change size */
                if (pJS.particles.size.anim.enable) {
                    if (p.size_status == true) {
                        if (p.radius >= pJS.particles.size.value) p.size_status = false;
                        p.radius += p.vs;
                    } else {
                        if (p.radius <= pJS.particles.size.anim.size_min) p.size_status = true;
                        p.radius -= p.vs;
                    }
                    if (p.radius < 0) p.radius = 0;
                }

                /* change particle position if it is out of canvas */
                if (pJS.particles.move.out_mode == 'bounce') {
                    var new_pos = {
                        x_left: p.radius,
                        x_right: pJS.canvas.w,
                        y_top: p.radius,
                        y_bottom: pJS.canvas.h
                    }
                } else {
                    var new_pos = {
                        x_left: -p.radius,
                        x_right: pJS.canvas.w + p.radius,
                        y_top: -p.radius,
                        y_bottom: pJS.canvas.h + p.radius
                    }
                }

                if (p.x - p.radius > pJS.canvas.w) {
                    p.x = new_pos.x_left;
                    p.y = Math.random() * pJS.canvas.h;
                } else if (p.x + p.radius < 0) {
                    p.x = new_pos.x_right;
                    p.y = Math.random() * pJS.canvas.h;
                }
                if (p.y - p.radius > pJS.canvas.h) {
                    p.y = new_pos.y_top;
                    p.x = Math.random() * pJS.canvas.w;
                } else if (p.y + p.radius < 0) {
                    p.y = new_pos.y_bottom;
                    p.x = Math.random() * pJS.canvas.w;
                }

                /* out of canvas modes */
                switch (pJS.particles.move.out_mode) {
                    case 'bounce':
                        if (p.x + p.radius > pJS.canvas.w) p.vx = -p.vx;
                        else if (p.x - p.radius < 0) p.vx = -p.vx;
                        if (p.y + p.radius > pJS.canvas.h) p.vy = -p.vy;
                        else if (p.y - p.radius < 0) p.vy = -p.vy;
                        break;
                }

                /* events */
                if (isInArray('grab', pJS.interactivity.events.onhover.mode)) {
                    pJS.fn.modes.grabParticle(p);
                }

                if (isInArray('bubble', pJS.interactivity.events.onhover.mode) || isInArray('bubble', pJS.interactivity.events.onclick.mode)) {
                    pJS.fn.modes.bubbleParticle(p);
                }

                if (isInArray('repulse', pJS.interactivity.events.onhover.mode) || isInArray('repulse', pJS.interactivity.events.onclick.mode)) {
                    pJS.fn.modes.repulseParticle(p);
                }

                /* interaction auto between particles */
                if (pJS.particles.line_linked.enable || pJS.particles.move.attract.enable) {
                    for (var j = i + 1; j < pJS.particles.array.length; j++) {
                        var p2 = pJS.particles.array[j];

                        /* link particles */
                        if (pJS.particles.line_linked.enable) {
                            pJS.fn.interact.linkParticles(p, p2);
                        }

                        /* attract particles */
                        if (pJS.particles.move.attract.enable) {
                            pJS.fn.interact.attractParticles(p, p2);
                        }

                        /* bounce particles */
                        if (pJS.particles.move.bounce) {
                            pJS.fn.interact.bounceParticles(p, p2);
                        }

                    }
                }


            }

        };

        pJS.fn.particlesDraw = function() {

            /* clear canvas */
            pJS.canvas.ctx.clearRect(0, 0, pJS.canvas.w, pJS.canvas.h);

            /* update each particles param */
            pJS.fn.particlesUpdate();

            /* draw each particle */
            for (var i = 0; i < pJS.particles.array.length; i++) {
                var p = pJS.particles.array[i];
                p.draw();
            }

        };

        pJS.fn.particlesEmpty = function() {
            pJS.particles.array = [];
        };

        pJS.fn.particlesRefresh = function() {

            /* init all */
            cancelRequestAnimFrame(pJS.fn.checkAnimFrame);
            cancelRequestAnimFrame(pJS.fn.drawAnimFrame);
            pJS.tmp.source_svg = undefined;
            pJS.tmp.img_obj = undefined;
            pJS.tmp.count_svg = 0;
            pJS.fn.particlesEmpty();
            pJS.fn.canvasClear();

            /* restart */
            pJS.fn.vendors.start();

        };


        /* ---------- pJS functions - particles interaction ------------ */

        pJS.fn.interact.linkParticles = function(p1, p2) {

            var dx = p1.x - p2.x,
                dy = p1.y - p2.y,
                dist = Math.sqrt(dx * dx + dy * dy);

            /* draw a line between p1 and p2 if the distance between them is under the config distance */
            if (dist <= pJS.particles.line_linked.distance) {

                var opacity_line = pJS.particles.line_linked.opacity - (dist / (1 / pJS.particles.line_linked.opacity)) / pJS.particles.line_linked.distance;

                if (opacity_line > 0) {

                    /* style */
                    var color_line = pJS.particles.line_linked.color_rgb_line;
                    pJS.canvas.ctx.strokeStyle = 'rgba(' + color_line.r + ',' + color_line.g + ',' + color_line.b + ',' + opacity_line + ')';
                    pJS.canvas.ctx.lineWidth = pJS.particles.line_linked.width;
                    //pJS.canvas.ctx.lineCap = 'round'; /* performance issue */

                    /* path */
                    pJS.canvas.ctx.beginPath();
                    pJS.canvas.ctx.moveTo(p1.x, p1.y);
                    pJS.canvas.ctx.lineTo(p2.x, p2.y);
                    pJS.canvas.ctx.stroke();
                    pJS.canvas.ctx.closePath();

                }

            }

        };


        pJS.fn.interact.attractParticles = function(p1, p2) {

            /* condensed particles */
            var dx = p1.x - p2.x,
                dy = p1.y - p2.y,
                dist = Math.sqrt(dx * dx + dy * dy);

            if (dist <= pJS.particles.line_linked.distance) {

                var ax = dx / (pJS.particles.move.attract.rotateX * 1000),
                    ay = dy / (pJS.particles.move.attract.rotateY * 1000);

                p1.vx -= ax;
                p1.vy -= ay;

                p2.vx += ax;
                p2.vy += ay;

            }


        }


        pJS.fn.interact.bounceParticles = function(p1, p2) {

            var dx = p1.x - p2.x,
                dy = p1.y - p2.y,
                dist = Math.sqrt(dx * dx + dy * dy),
                dist_p = p1.radius + p2.radius;

            if (dist <= dist_p) {
                p1.vx = -p1.vx;
                p1.vy = -p1.vy;

                p2.vx = -p2.vx;
                p2.vy = -p2.vy;
            }

        }


        /* ---------- pJS functions - modes events ------------ */

        pJS.fn.modes.pushParticles = function(nb, pos) {

            pJS.tmp.pushing = true;

            for (var i = 0; i < nb; i++) {
                pJS.particles.array.push(
                    new pJS.fn.particle(
                        pJS.particles.color,
                        pJS.particles.opacity.value, {
                            'x': pos ? pos.pos_x : Math.random() * pJS.canvas.w,
                            'y': pos ? pos.pos_y : Math.random() * pJS.canvas.h
                        }
                    )
                )
                if (i == nb - 1) {
                    if (!pJS.particles.move.enable) {
                        pJS.fn.particlesDraw();
                    }
                    pJS.tmp.pushing = false;
                }
            }

        };


        pJS.fn.modes.removeParticles = function(nb) {

            pJS.particles.array.splice(0, nb);
            if (!pJS.particles.move.enable) {
                pJS.fn.particlesDraw();
            }

        };


        pJS.fn.modes.bubbleParticle = function(p) {

            /* on hover event */
            if (pJS.interactivity.events.onhover.enable && isInArray('bubble', pJS.interactivity.events.onhover.mode)) {

                var dx_mouse = p.x - pJS.interactivity.mouse.pos_x,
                    dy_mouse = p.y - pJS.interactivity.mouse.pos_y,
                    dist_mouse = Math.sqrt(dx_mouse * dx_mouse + dy_mouse * dy_mouse),
                    ratio = 1 - dist_mouse / pJS.interactivity.modes.bubble.distance;

                function init() {
                    p.opacity_bubble = p.opacity;
                    p.radius_bubble = p.radius;
                }

                /* mousemove - check ratio */
                if (dist_mouse <= pJS.interactivity.modes.bubble.distance) {

                    if (ratio >= 0 && pJS.interactivity.status == 'mousemove') {

                        /* size */
                        if (pJS.interactivity.modes.bubble.size != pJS.particles.size.value) {

                            if (pJS.interactivity.modes.bubble.size > pJS.particles.size.value) {
                                var size = p.radius + (pJS.interactivity.modes.bubble.size * ratio);
                                if (size >= 0) {
                                    p.radius_bubble = size;
                                }
                            } else {
                                var dif = p.radius - pJS.interactivity.modes.bubble.size,
                                    size = p.radius - (dif * ratio);
                                if (size > 0) {
                                    p.radius_bubble = size;
                                } else {
                                    p.radius_bubble = 0;
                                }
                            }

                        }

                        /* opacity */
                        if (pJS.interactivity.modes.bubble.opacity != pJS.particles.opacity.value) {

                            if (pJS.interactivity.modes.bubble.opacity > pJS.particles.opacity.value) {
                                var opacity = pJS.interactivity.modes.bubble.opacity * ratio;
                                if (opacity > p.opacity && opacity <= pJS.interactivity.modes.bubble.opacity) {
                                    p.opacity_bubble = opacity;
                                }
                            } else {
                                var opacity = p.opacity - (pJS.particles.opacity.value - pJS.interactivity.modes.bubble.opacity) * ratio;
                                if (opacity < p.opacity && opacity >= pJS.interactivity.modes.bubble.opacity) {
                                    p.opacity_bubble = opacity;
                                }
                            }

                        }

                    }

                } else {
                    init();
                }


                /* mouseleave */
                if (pJS.interactivity.status == 'mouseleave') {
                    init();
                }

            }

            /* on click event */
            else if (pJS.interactivity.events.onclick.enable && isInArray('bubble', pJS.interactivity.events.onclick.mode)) {


                if (pJS.tmp.bubble_clicking) {
                    var dx_mouse = p.x - pJS.interactivity.mouse.click_pos_x,
                        dy_mouse = p.y - pJS.interactivity.mouse.click_pos_y,
                        dist_mouse = Math.sqrt(dx_mouse * dx_mouse + dy_mouse * dy_mouse),
                        time_spent = (new Date().getTime() - pJS.interactivity.mouse.click_time) / 1000;

                    if (time_spent > pJS.interactivity.modes.bubble.duration) {
                        pJS.tmp.bubble_duration_end = true;
                    }

                    if (time_spent > pJS.interactivity.modes.bubble.duration * 2) {
                        pJS.tmp.bubble_clicking = false;
                        pJS.tmp.bubble_duration_end = false;
                    }
                }


                function process(bubble_param, particles_param, p_obj_bubble, p_obj, id) {

                    if (bubble_param != particles_param) {

                        if (!pJS.tmp.bubble_duration_end) {
                            if (dist_mouse <= pJS.interactivity.modes.bubble.distance) {
                                if (p_obj_bubble != undefined) var obj = p_obj_bubble;
                                else var obj = p_obj;
                                if (obj != bubble_param) {
                                    var value = p_obj - (time_spent * (p_obj - bubble_param) / pJS.interactivity.modes.bubble.duration);
                                    if (id == 'size') p.radius_bubble = value;
                                    if (id == 'opacity') p.opacity_bubble = value;
                                }
                            } else {
                                if (id == 'size') p.radius_bubble = undefined;
                                if (id == 'opacity') p.opacity_bubble = undefined;
                            }
                        } else {
                            if (p_obj_bubble != undefined) {
                                var value_tmp = p_obj - (time_spent * (p_obj - bubble_param) / pJS.interactivity.modes.bubble.duration),
                                    dif = bubble_param - value_tmp;
                                value = bubble_param + dif;
                                if (id == 'size') p.radius_bubble = value;
                                if (id == 'opacity') p.opacity_bubble = value;
                            }
                        }

                    }

                }

                if (pJS.tmp.bubble_clicking) {
                    /* size */
                    process(pJS.interactivity.modes.bubble.size, pJS.particles.size.value, p.radius_bubble, p.radius, 'size');
                    /* opacity */
                    process(pJS.interactivity.modes.bubble.opacity, pJS.particles.opacity.value, p.opacity_bubble, p.opacity, 'opacity');
                }

            }

        };


        pJS.fn.modes.repulseParticle = function(p) {

            if (pJS.interactivity.events.onhover.enable && isInArray('repulse', pJS.interactivity.events.onhover.mode) && pJS.interactivity.status == 'mousemove') {

                var dx_mouse = p.x - pJS.interactivity.mouse.pos_x,
                    dy_mouse = p.y - pJS.interactivity.mouse.pos_y,
                    dist_mouse = Math.sqrt(dx_mouse * dx_mouse + dy_mouse * dy_mouse);

                var normVec = {
                        x: dx_mouse / dist_mouse,
                        y: dy_mouse / dist_mouse
                    },
                    repulseRadius = pJS.interactivity.modes.repulse.distance,
                    velocity = 100,
                    repulseFactor = clamp((1 / repulseRadius) * (-1 * Math.pow(dist_mouse / repulseRadius, 2) + 1) * repulseRadius * velocity, 0, 50);

                var pos = {
                    x: p.x + normVec.x * repulseFactor,
                    y: p.y + normVec.y * repulseFactor
                }

                if (pJS.particles.move.out_mode == 'bounce') {
                    if (pos.x - p.radius > 0 && pos.x + p.radius < pJS.canvas.w) p.x = pos.x;
                    if (pos.y - p.radius > 0 && pos.y + p.radius < pJS.canvas.h) p.y = pos.y;
                } else {
                    p.x = pos.x;
                    p.y = pos.y;
                }

            } else if (pJS.interactivity.events.onclick.enable && isInArray('repulse', pJS.interactivity.events.onclick.mode)) {

                if (!pJS.tmp.repulse_finish) {
                    pJS.tmp.repulse_count++;
                    if (pJS.tmp.repulse_count == pJS.particles.array.length) {
                        pJS.tmp.repulse_finish = true;
                    }
                }

                if (pJS.tmp.repulse_clicking) {

                    var repulseRadius = Math.pow(pJS.interactivity.modes.repulse.distance / 6, 3);

                    var dx = pJS.interactivity.mouse.click_pos_x - p.x,
                        dy = pJS.interactivity.mouse.click_pos_y - p.y,
                        d = dx * dx + dy * dy;

                    var force = -repulseRadius / d * 1;

                    function process() {

                        var f = Math.atan2(dy, dx);
                        p.vx = force * Math.cos(f);
                        p.vy = force * Math.sin(f);

                        if (pJS.particles.move.out_mode == 'bounce') {
                            var pos = {
                                x: p.x + p.vx,
                                y: p.y + p.vy
                            }
                            if (pos.x + p.radius > pJS.canvas.w) p.vx = -p.vx;
                            else if (pos.x - p.radius < 0) p.vx = -p.vx;
                            if (pos.y + p.radius > pJS.canvas.h) p.vy = -p.vy;
                            else if (pos.y - p.radius < 0) p.vy = -p.vy;
                        }

                    }

                    // default
                    if (d <= repulseRadius) {
                        process();
                    }

                    // bang - slow motion mode
                    // if(!pJS.tmp.repulse_finish){
                    //   if(d <= repulseRadius){
                    //     process();
                    //   }
                    // }else{
                    //   process();
                    // }


                } else {

                    if (pJS.tmp.repulse_clicking == false) {

                        p.vx = p.vx_i;
                        p.vy = p.vy_i;

                    }

                }

            }

        }


        pJS.fn.modes.grabParticle = function(p) {

            if (pJS.interactivity.events.onhover.enable && pJS.interactivity.status == 'mousemove') {

                var dx_mouse = p.x - pJS.interactivity.mouse.pos_x,
                    dy_mouse = p.y - pJS.interactivity.mouse.pos_y,
                    dist_mouse = Math.sqrt(dx_mouse * dx_mouse + dy_mouse * dy_mouse);

                /* draw a line between the cursor and the particle if the distance between them is under the config distance */
                if (dist_mouse <= pJS.interactivity.modes.grab.distance) {

                    var opacity_line = pJS.interactivity.modes.grab.line_linked.opacity - (dist_mouse / (1 / pJS.interactivity.modes.grab.line_linked.opacity)) / pJS.interactivity.modes.grab.distance;

                    if (opacity_line > 0) {

                        /* style */
                        var color_line = pJS.particles.line_linked.color_rgb_line;
                        pJS.canvas.ctx.strokeStyle = 'rgba(' + color_line.r + ',' + color_line.g + ',' + color_line.b + ',' + opacity_line + ')';
                        pJS.canvas.ctx.lineWidth = pJS.particles.line_linked.width;
                        //pJS.canvas.ctx.lineCap = 'round'; /* performance issue */

                        /* path */
                        pJS.canvas.ctx.beginPath();
                        pJS.canvas.ctx.moveTo(p.x, p.y);
                        pJS.canvas.ctx.lineTo(pJS.interactivity.mouse.pos_x, pJS.interactivity.mouse.pos_y);
                        pJS.canvas.ctx.stroke();
                        pJS.canvas.ctx.closePath();

                    }

                }

            }

        };



        /* ---------- pJS functions - vendors ------------ */

        pJS.fn.vendors.eventsListeners = function() {

            /* events target element */
            if (pJS.interactivity.detect_on == 'window') {
                pJS.interactivity.el = window;
            } else {
                pJS.interactivity.el = pJS.canvas.el;
            }


            /* detect mouse pos - on hover / click event */
            if (pJS.interactivity.events.onhover.enable || pJS.interactivity.events.onclick.enable) {

                /* el on mousemove */
                pJS.interactivity.el.addEventListener('mousemove', function(e) {

                    if (pJS.interactivity.el == window) {
                        var pos_x = e.clientX,
                            pos_y = e.clientY;
                    } else {
                        var pos_x = e.offsetX || e.clientX,
                            pos_y = e.offsetY || e.clientY;
                    }

                    pJS.interactivity.mouse.pos_x = pos_x;
                    pJS.interactivity.mouse.pos_y = pos_y;

                    if (pJS.tmp.retina) {
                        pJS.interactivity.mouse.pos_x *= pJS.canvas.pxratio;
                        pJS.interactivity.mouse.pos_y *= pJS.canvas.pxratio;
                    }

                    pJS.interactivity.status = 'mousemove';

                });

                /* el on onmouseleave */
                pJS.interactivity.el.addEventListener('mouseleave', function(e) {

                    pJS.interactivity.mouse.pos_x = null;
                    pJS.interactivity.mouse.pos_y = null;
                    pJS.interactivity.status = 'mouseleave';

                });

            }

            /* on click event */
            if (pJS.interactivity.events.onclick.enable) {

                pJS.interactivity.el.addEventListener('click', function() {

                    pJS.interactivity.mouse.click_pos_x = pJS.interactivity.mouse.pos_x;
                    pJS.interactivity.mouse.click_pos_y = pJS.interactivity.mouse.pos_y;
                    pJS.interactivity.mouse.click_time = new Date().getTime();

                    if (pJS.interactivity.events.onclick.enable) {

                        switch (pJS.interactivity.events.onclick.mode) {

                            case 'push':
                                if (pJS.particles.move.enable) {
                                    pJS.fn.modes.pushParticles(pJS.interactivity.modes.push.particles_nb, pJS.interactivity.mouse);
                                } else {
                                    if (pJS.interactivity.modes.push.particles_nb == 1) {
                                        pJS.fn.modes.pushParticles(pJS.interactivity.modes.push.particles_nb, pJS.interactivity.mouse);
                                    } else if (pJS.interactivity.modes.push.particles_nb > 1) {
                                        pJS.fn.modes.pushParticles(pJS.interactivity.modes.push.particles_nb);
                                    }
                                }
                                break;

                            case 'remove':
                                pJS.fn.modes.removeParticles(pJS.interactivity.modes.remove.particles_nb);
                                break;

                            case 'bubble':
                                pJS.tmp.bubble_clicking = true;
                                break;

                            case 'repulse':
                                pJS.tmp.repulse_clicking = true;
                                pJS.tmp.repulse_count = 0;
                                pJS.tmp.repulse_finish = false;
                                setTimeout(function() {
                                    pJS.tmp.repulse_clicking = false;
                                }, pJS.interactivity.modes.repulse.duration * 1000)
                                break;

                        }

                    }

                });

            }


        };

        pJS.fn.vendors.densityAutoParticles = function() {

            if (pJS.particles.number.density.enable) {

                /* calc area */
                var area = pJS.canvas.el.width * pJS.canvas.el.height / 1000;
                if (pJS.tmp.retina) {
                    area = area / (pJS.canvas.pxratio * 2);
                }

                /* calc number of particles based on density area */
                var nb_particles = area * pJS.particles.number.value / pJS.particles.number.density.value_area;

                /* add or remove X particles */
                var missing_particles = pJS.particles.array.length - nb_particles;
                if (missing_particles < 0) pJS.fn.modes.pushParticles(Math.abs(missing_particles));
                else pJS.fn.modes.removeParticles(missing_particles);

            }

        };


        pJS.fn.vendors.checkOverlap = function(p1, position) {
            for (var i = 0; i < pJS.particles.array.length; i++) {
                var p2 = pJS.particles.array[i];

                var dx = p1.x - p2.x,
                    dy = p1.y - p2.y,
                    dist = Math.sqrt(dx * dx + dy * dy);

                if (dist <= p1.radius + p2.radius) {
                    p1.x = position ? position.x : Math.random() * pJS.canvas.w;
                    p1.y = position ? position.y : Math.random() * pJS.canvas.h;
                    pJS.fn.vendors.checkOverlap(p1);
                }
            }
        };


        pJS.fn.vendors.createSvgImg = function(p) {

            /* set color to svg element */
            var svgXml = pJS.tmp.source_svg,
                rgbHex = /#([0-9A-F]{3,6})/gi,
                coloredSvgXml = svgXml.replace(rgbHex, function(m, r, g, b) {
                    if (p.color.rgb) {
                        var color_value = 'rgba(' + p.color.rgb.r + ',' + p.color.rgb.g + ',' + p.color.rgb.b + ',' + p.opacity + ')';
                    } else {
                        var color_value = 'hsla(' + p.color.hsl.h + ',' + p.color.hsl.s + '%,' + p.color.hsl.l + '%,' + p.opacity + ')';
                    }
                    return color_value;
                });

            /* prepare to create img with colored svg */
            var svg = new Blob([coloredSvgXml], {
                    type: 'image/svg+xml;charset=utf-8'
                }),
                DOMURL = window.URL || window.webkitURL || window,
                url = DOMURL.createObjectURL(svg);

            /* create particle img obj */
            var img = new Image();
            img.addEventListener('load', function() {
                p.img.obj = img;
                p.img.loaded = true;
                DOMURL.revokeObjectURL(url);
                pJS.tmp.count_svg++;
            });
            img.src = url;

        };


        pJS.fn.vendors.destroypJS = function() {
            cancelAnimationFrame(pJS.fn.drawAnimFrame);
            canvas_el.remove();
            pJSDom = null;
        };


        pJS.fn.vendors.drawShape = function(c, startX, startY, sideLength, sideCountNumerator, sideCountDenominator) {

            // By Programming Thomas - https://programmingthomas.wordpress.com/2013/04/03/n-sided-shapes/
            var sideCount = sideCountNumerator * sideCountDenominator;
            var decimalSides = sideCountNumerator / sideCountDenominator;
            var interiorAngleDegrees = (180 * (decimalSides - 2)) / decimalSides;
            var interiorAngle = Math.PI - Math.PI * interiorAngleDegrees / 180; // convert to radians
            c.save();
            c.beginPath();
            c.translate(startX, startY);
            c.moveTo(0, 0);
            for (var i = 0; i < sideCount; i++) {
                c.lineTo(sideLength, 0);
                c.translate(sideLength, 0);
                c.rotate(interiorAngle);
            }
            //c.stroke();
            c.fill();
            c.restore();

        };

        pJS.fn.vendors.exportImg = function() {
            window.open(pJS.canvas.el.toDataURL('image/png'), '_blank');
        };


        pJS.fn.vendors.loadImg = function(type) {

            pJS.tmp.img_error = undefined;

            if (pJS.particles.shape.image.src != '') {

                if (type == 'svg') {

                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', pJS.particles.shape.image.src);
                    xhr.onreadystatechange = function(data) {
                        if (xhr.readyState == 4) {
                            if (xhr.status == 200) {
                                pJS.tmp.source_svg = data.currentTarget.response;
                                pJS.fn.vendors.checkBeforeDraw();
                            } else {
                                console.log('Error pJS - Image not found');
                                pJS.tmp.img_error = true;
                            }
                        }
                    }
                    xhr.send();

                } else {

                    var img = new Image();
                    img.addEventListener('load', function() {
                        pJS.tmp.img_obj = img;
                        pJS.fn.vendors.checkBeforeDraw();
                    });
                    img.src = pJS.particles.shape.image.src;

                }

            } else {
                console.log('Error pJS - No image.src');
                pJS.tmp.img_error = true;
            }

        };


        pJS.fn.vendors.draw = function() {

            if (pJS.particles.shape.type == 'image') {

                if (pJS.tmp.img_type == 'svg') {

                    if (pJS.tmp.count_svg >= pJS.particles.number.value) {
                        pJS.fn.particlesDraw();
                        if (!pJS.particles.move.enable) cancelRequestAnimFrame(pJS.fn.drawAnimFrame);
                        else pJS.fn.drawAnimFrame = requestAnimFrame(pJS.fn.vendors.draw);
                    } else {
                        //console.log('still loading...');
                        if (!pJS.tmp.img_error) pJS.fn.drawAnimFrame = requestAnimFrame(pJS.fn.vendors.draw);
                    }

                } else {

                    if (pJS.tmp.img_obj != undefined) {
                        pJS.fn.particlesDraw();
                        if (!pJS.particles.move.enable) cancelRequestAnimFrame(pJS.fn.drawAnimFrame);
                        else pJS.fn.drawAnimFrame = requestAnimFrame(pJS.fn.vendors.draw);
                    } else {
                        if (!pJS.tmp.img_error) pJS.fn.drawAnimFrame = requestAnimFrame(pJS.fn.vendors.draw);
                    }

                }

            } else {
                pJS.fn.particlesDraw();
                if (!pJS.particles.move.enable) cancelRequestAnimFrame(pJS.fn.drawAnimFrame);
                else pJS.fn.drawAnimFrame = requestAnimFrame(pJS.fn.vendors.draw);
            }

        };


        pJS.fn.vendors.checkBeforeDraw = function() {

            // if shape is image
            if (pJS.particles.shape.type == 'image') {

                if (pJS.tmp.img_type == 'svg' && pJS.tmp.source_svg == undefined) {
                    pJS.tmp.checkAnimFrame = requestAnimFrame(check);
                } else {
                    //console.log('images loaded! cancel check');
                    cancelRequestAnimFrame(pJS.tmp.checkAnimFrame);
                    if (!pJS.tmp.img_error) {
                        pJS.fn.vendors.init();
                        pJS.fn.vendors.draw();
                    }

                }

            } else {
                pJS.fn.vendors.init();
                pJS.fn.vendors.draw();
            }

        };


        pJS.fn.vendors.init = function() {

            /* init canvas + particles */
            pJS.fn.retinaInit();
            pJS.fn.canvasInit();
            pJS.fn.canvasSize();
            pJS.fn.canvasPaint();
            pJS.fn.particlesCreate();
            pJS.fn.vendors.densityAutoParticles();

            /* particles.line_linked - convert hex colors to rgb */
            pJS.particles.line_linked.color_rgb_line = hexToRgb(pJS.particles.line_linked.color);

        };


        pJS.fn.vendors.start = function() {

            if (isInArray('image', pJS.particles.shape.type)) {
                pJS.tmp.img_type = pJS.particles.shape.image.src.substr(pJS.particles.shape.image.src.length - 3);
                pJS.fn.vendors.loadImg(pJS.tmp.img_type);
            } else {
                pJS.fn.vendors.checkBeforeDraw();
            }

        };




        /* ---------- pJS - start ------------ */


        pJS.fn.vendors.eventsListeners();

        pJS.fn.vendors.start();



    };

    /* ---------- global functions - vendors ------------ */

    Object.deepExtend = function(destination, source) {
        for (var property in source) {
            if (source[property] && source[property].constructor &&
                source[property].constructor === Object) {
                destination[property] = destination[property] || {};
                arguments.callee(destination[property], source[property]);
            } else {
                destination[property] = source[property];
            }
        }
        return destination;
    };

    window.requestAnimFrame = (function() {
        return window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame ||
            function(callback) {
                window.setTimeout(callback, 1000 / 60);
            };
    })();

    window.cancelRequestAnimFrame = (function() {
        return window.cancelAnimationFrame ||
            window.webkitCancelRequestAnimationFrame ||
            window.mozCancelRequestAnimationFrame ||
            window.oCancelRequestAnimationFrame ||
            window.msCancelRequestAnimationFrame ||
            clearTimeout
    })();

    function hexToRgb(hex) {
        // By Tim Down - http://stackoverflow.com/a/5624139/3493650
        // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
        var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
        hex = hex.replace(shorthandRegex, function(m, r, g, b) {
            return r + r + g + g + b + b;
        });
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    };

    function clamp(number, min, max) {
        return Math.min(Math.max(number, min), max);
    };

    function isInArray(value, array) {
        return array.indexOf(value) > -1;
    }


    /* ---------- particles.js functions - start ------------ */

    window.pJSDom = [];

    window.particlesJS = function(tag_id, params) {

        //console.log(params);

        /* no string id? so it's object params, and set the id with default id */
        if (typeof(tag_id) != 'string') {
            params = tag_id;
            tag_id = 'particles-js';
        }

        /* no id? set the id to default id */
        if (!tag_id) {
            tag_id = 'particles-js';
        }

        /* pJS elements */
        var pJS_tag = document.getElementById(tag_id),
            pJS_canvas_class = 'particles-js-canvas-el',
            exist_canvas = pJS_tag.getElementsByClassName(pJS_canvas_class);

        /* remove canvas if exists into the pJS target tag */
        if (exist_canvas.length) {
            while (exist_canvas.length > 0) {
                pJS_tag.removeChild(exist_canvas[0]);
            }
        }

        /* create canvas element */
        var canvas_el = document.createElement('canvas');
        canvas_el.className = pJS_canvas_class;

        /* set size canvas */
        canvas_el.style.width = "100%";
        canvas_el.style.height = "100%";

        /* append canvas */
        var canvas = document.getElementById(tag_id).appendChild(canvas_el);

        /* launch particle.js */
        if (canvas != null) {
            pJSDom.push(new pJS(tag_id, params));
        }

    };

    window.particlesJS.load = function(tag_id, path_config_json, callback) {

        /* load json config */
        var xhr = new XMLHttpRequest();
        xhr.open('GET', path_config_json);
        xhr.onreadystatechange = function(data) {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    var params = JSON.parse(data.currentTarget.response);
                    window.particlesJS(tag_id, params);
                    if (callback) callback();
                } else {
                    console.log('Error pJS - XMLHttpRequest status: ' + xhr.status);
                    console.log('Error pJS - File config not found');
                }
            }
        };
        xhr.send();

    };

    particlesJS('particles-js',

        {
            "particles": {
                "number": {
                    "value": 80,
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": "#fff"
                },
                "shape": {
                    "type": "circle",
                    "stroke": {
                        "width": 0,
                        "color": "#000000"
                    },
                    "polygon": {
                        "nb_sides": 5
                    },
                    "image": {
                        "src": "img/github.svg",
                        "width": 100,
                        "height": 100
                    }
                },
                "opacity": {
                    "value": 0.5,
                    "random": false,
                    "anim": {
                        "enable": false,
                        "speed": 1,
                        "opacity_min": 0.1,
                        "sync": false
                    }
                },
                "size": {
                    "value": 5,
                    "random": true,
                    "anim": {
                        "enable": false,
                        "speed": 40,
                        "size_min": 0.1,
                        "sync": false
                    }
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#fff",
                    "opacity": 0.4,
                    "width": 2
                },
                "move": {
                    "enable": true,
                    "speed": 6,
                    "direction": "none",
                    "random": false,
                    "straight": false,
                    "out_mode": "out",
                    "attract": {
                        "enable": false,
                        "rotateX": 600,
                        "rotateY": 1200
                    }
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "repulse"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "push"
                    },
                    "resize": true
                },
                "modes": {
                    "grab": {
                        "distance": 400,
                        "line_linked": {
                            "opacity": 1
                        }
                    },
                    "bubble": {
                        "distance": 400,
                        "size": 40,
                        "duration": 2,
                        "opacity": 8,
                        "speed": 3
                    },
                    "repulse": {
                        "distance": 200
                    },
                    "push": {
                        "particles_nb": 4
                    },
                    "remove": {
                        "particles_nb": 2
                    }
                }
            },
            "retina_detect": true,
            "config_demo": {
                "hide_card": false,
                "background_color": "#b61924",
                "background_image": "",
                "background_position": "50% 50%",
                "background_repeat": "no-repeat",
                "background_size": "cover"
            }
        }

    );


    var Stats = function() {

        var startTime = Date.now(),
            prevTime = startTime;
        var ms = 0,
            msMin = Infinity,
            msMax = 0;
        var fps = 0,
            fpsMin = Infinity,
            fpsMax = 0;
        var frames = 0,
            mode = 0;

        var container = document.createElement('div');
        container.id = 'stats';
        container.addEventListener('mousedown', function(event) {
            event.preventDefault();
            setMode(++mode % 2)
        }, false);
        container.style.cssText = 'width:80px;opacity:0.9;cursor:pointer';

        var fpsDiv = document.createElement('div');
        fpsDiv.id = 'fps';
        fpsDiv.style.cssText = 'padding:0 0 3px 3px;text-align:left;background-color:#002';
        container.appendChild(fpsDiv);

        var fpsText = document.createElement('div');
        fpsText.id = 'fpsText';
        fpsText.style.cssText = 'color:#0ff;font-family:Helvetica,Arial,sans-serif;font-size:9px;font-weight:bold;line-height:15px';
        fpsText.innerHTML = 'FPS';
        fpsDiv.appendChild(fpsText);

        var fpsGraph = document.createElement('div');
        fpsGraph.id = 'fpsGraph';
        fpsGraph.style.cssText = 'position:relative;width:74px;height:30px;background-color:#0ff';
        fpsDiv.appendChild(fpsGraph);

        while (fpsGraph.children.length < 74) {

            var bar = document.createElement('span');
            bar.style.cssText = 'width:1px;height:30px;float:left;background-color:#113';
            fpsGraph.appendChild(bar);

        }

        var msDiv = document.createElement('div');
        msDiv.id = 'ms';
        msDiv.style.cssText = 'padding:0 0 3px 3px;text-align:left;background-color:#020;display:none';
        container.appendChild(msDiv);

        var msText = document.createElement('div');
        msText.id = 'msText';
        msText.style.cssText = 'color:#0f0;font-family:Helvetica,Arial,sans-serif;font-size:9px;font-weight:bold;line-height:15px';
        msText.innerHTML = 'MS';
        msDiv.appendChild(msText);

        var msGraph = document.createElement('div');
        msGraph.id = 'msGraph';
        msGraph.style.cssText = 'position:relative;width:74px;height:30px;background-color:#0f0';
        msDiv.appendChild(msGraph);

        while (msGraph.children.length < 74) {

            var bar = document.createElement('span');
            bar.style.cssText = 'width:1px;height:30px;float:left;background-color:#131';
            msGraph.appendChild(bar);

        }

        var setMode = function(value) {

            mode = value;

            switch (mode) {

                case 0:
                    fpsDiv.style.display = 'block';
                    msDiv.style.display = 'none';
                    break;
                case 1:
                    fpsDiv.style.display = 'none';
                    msDiv.style.display = 'block';
                    break;
            }

        };

        var updateGraph = function(dom, value) {

            var child = dom.appendChild(dom.firstChild);
            child.style.height = value + 'px';

        };

        return {

            REVISION: 12,

            domElement: container,

            setMode: setMode,

            begin: function() {

                startTime = Date.now();

            },

            end: function() {

                var time = Date.now();

                ms = time - startTime;
                msMin = Math.min(msMin, ms);
                msMax = Math.max(msMax, ms);

                msText.textContent = ms + ' MS (' + msMin + '-' + msMax + ')';
                updateGraph(msGraph, Math.min(30, 30 - (ms / 200) * 30));

                frames++;

                if (time > prevTime + 1000) {

                    fps = Math.round((frames * 1000) / (time - prevTime));
                    fpsMin = Math.min(fpsMin, fps);
                    fpsMax = Math.max(fpsMax, fps);

                    fpsText.textContent = fps + ' FPS (' + fpsMin + '-' + fpsMax + ')';
                    updateGraph(fpsGraph, Math.min(30, 30 - (fps / 100) * 30));

                    prevTime = time;
                    frames = 0;

                }

                return time;

            },

            update: function() {

                startTime = this.end();

            }

        }

    };

    if (typeof module === 'object') {

        module.exports = Stats;

    }

    var count_particles, stats, update;
    stats = new Stats;
    stats.setMode(0);
    stats.domElement.style.position = 'absolute';
    stats.domElement.style.left = '0px';
    stats.domElement.style.top = '0px';
    document.body.appendChild(stats.domElement);
    count_particles = document.querySelector('.js-count-particles');
    update = function() {
        stats.begin();
        stats.end();
        if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) {
            // count_particles.innerText = window.pJSDom[0].pJS.particles.array.length;
        }
        requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
</script>