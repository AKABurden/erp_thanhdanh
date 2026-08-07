<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
           
            <?php if (is_admin()) { ?>
            <div class="line-sp"></div>
            <a href="<?=admin_url('videos/detail')?>"   class="btn btn-info mright5 test pull-right H_action_button">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
               <?php echo _l('Thêm và sửa video'); ?></a>
            <?php } ?>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="f-content">
                    <?php if(!empty($detail_videos)){ ?>
                    <div style="text-align: center;">
                    <video playsinline width="70%" controls src="<?=base_url($detail_videos->link)?>" autoplay>
                      <!-- <source src="<?=base_url($detail_videos->link)?>" type="<?=$detail_videos->type_videos?>"> -->
                    </video>
                    <br>
                    
                    <h3><b>
                    <?=$detail_videos->name?>
                </b>
                    </h3>
                    </div>
                    <br>
                    <br>
                    <div style="padding: 8px; border: 2px solid #03a9f4; word-wrap: break-word;"><?=$detail_videos->note?></div>
                    
                </div>
                <?php }else{?>
                    <div style="text-align: center; word-wrap: break-word;"><h1><img src="<?=base_url('uploads/manage_video.png')?>"></h1></div>
                <?php }?>
            </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
        <script type="text/javascript" src="<?=base_url('assets/libs/popper/umd/popper.js')?>"></script>
        <script type="text/javascript" src="<?=base_url('assets/libs/bootstrap/js/bootstrap.min.js')?>"></script>
        <script type="text/javascript" src="<?=base_url('assets/libs/owlcarousel/owl.carousel.min.js')?>"></script>
        <script type="text/javascript">
            $('.carousel').owlCarousel({
                loop:false,
                margin:0,
                nav:true,
                dots: false,
                responsive:{
                    0:{
                        items:1
                    },
                    600:{
                        items:1
                    },
                    1000:{
                        items:1
                    }
                },
                navText: ["<i class='fa fa-chevron-left' aria-hidden='true'></i>","<i class='fa fa-chevron-right' aria-hidden='true'></i>"]
            })
        </script>

        <script>
            $(document).ready(function(){
    $("#nav-input-search" ).focus(function() {
        $( "#nav-input-search" ).addClass('focus');
    });

    $('#new-post-slide').owlCarousel({
        loop:false,
        margin:20,
        nav:true,
        dots: false,
        autoplay: true,
        responsive:{
            0:{
                items:1
            },
            500:{
                items:2
            },
            1000:{
                items:2
            }
        },
        navText: ["<i class='fa fa-angle-left' aria-hidden='true'></i>","<i class='fa fa-angle-right' aria-hidden='true'></i>"]
    });

    $('.cate-post-slide').owlCarousel({
        loop:false,
        margin:20,
        nav:true,
        dots: false,
        autoplay: true,
        responsive:{
            0:{
                items:2
            },
            500:{
                items:3
            },
            1000:{
                items:4
            }
        },
        navText: ["<i class='fa fa-angle-left' aria-hidden='true'></i>","<i class='fa fa-angle-right' aria-hidden='true'></i>"]
    });

    var height_thumn = $(".cate-post-slide .cate-post-item .post-avatar").height();
    top_nav_slide = (height_thumn / 2) - 15 +'px';
    $(".cate-post-slide .owl-nav .owl-prev").css("top", top_nav_slide);
    $(".cate-post-slide .owl-nav .owl-next").css("top", top_nav_slide);

    // recently post

    $('#recent-post-tab').click(function() {
        $('#recent-tabContent #more-post').removeClass('active');
        $('#recent-tabContent #more-post').removeClass('show');
    });

    $('body').append('<h1 class="sr-only">Blog fuvavi.com - Thủ thuật Frontend - Kinh nghiệm thiết kế web - Design - Share code</h1> <h2 class="sr-only">Bản quyền của FUVAVI BLOG</h2>');
});
            window.fbAsyncInit = function() {
            FB.init({
              appId            : '131375914163212',
              autoLogAppEvents : true,
              xfbml            : true,
              version          : 'v2.11'
            });
            };
            (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "https://connect.facebook.net/vi_VN/sdk.js";
             fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

        </script>
