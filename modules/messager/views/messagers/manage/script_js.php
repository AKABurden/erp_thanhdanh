<?php
    init_tail();
    $VersionAppFB   =   get_option('VersionAppFB');
    $IdAppFB        =   get_option('IdAppFB');
    $basePath       =   module_dir_url('messager', 'uploads/');
    $baseAssets     =   module_dir_url('messager', 'assets/');
    $C_TagFB = getFullDataTag();
?>




<script src="https://js.pusher.com/4.4/pusher.min.js"></script>
<script src="<?= $baseAssets.'messager_fb/js/jquery.cookie.js';?>"></script>
<script>
    var text_lableTag = "<?=!empty(_l('cong_infomation_tag')) ? _l('cong_infomation_tag') : ''?>";
    var admin_url = "<?=admin_url()?>";
    var VersionAppFB = "<?=$VersionAppFB?>";
    var C_available_tags = <?= json_encode($C_TagFB['name']) ?>;
    var C_available_tags_color = <?= json_encode($C_TagFB['color']) ?>;
    var C_available_tags_background_color = <?= json_encode($C_TagFB['background_color']) ?>;
    var C_available_tags_ids = <?= json_encode($C_TagFB['id']) ?>;
    var please_wait = '<?=_l('cong_please_wait')?>';


    //Pusher
    Pusher.logToConsole = false;
    var pusher = new Pusher('3ffdad22ae304306f311', {
        cluster: 'eu',
        forceTLS: true
    });

    var channel = pusher.subscribe($.cookie('page_active'));
</script>

<script src="<?= $baseAssets.'messager_fb/js/main.js';?>"></script>

<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=<?=$VersionAppFB?>&appId=<?=$IdAppFB?>&autoLogAppEvents=1"></script>
<script>
    InitTagMessage_Hidden();
    var leadUniqueValidationFields = ["email"];
    var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;

    $(document).ready(function() {
        $.ajaxSetup({ cache: true });
        //FB.init({
        //    appId: '<?//=$IdAppFB?>//',
        //    version: '<?//=$VersionAppFB?>//'
        //});
    });

    $('body').on('keypress','#replyMessager',function(event){
        if(event.keyCode == 13)
        {
            if(!event.shiftKey){
                var replyMessager = $(this).val();
                $('#replyMessager').val("");
                var id_user = $(this).attr('id_user');
                if($.trim(replyMessager) != "" && id_user != "")
                {
                    replyMessager =$.trim(replyMessager);

                    var date = new Date();
                    var id_message = $('.content-profile.active').attr('id_senders');
                    $.post('https://graph.facebook.com/<?=$VersionAppFB?>/me/messages',{access_token:$.cookie('access_token_page_active'), recipient:{"id": id_user}, message:{"text": replyMessager}},function(response){
                        if(response.error)
                        {
                            $('#replyMessager').val("");
                            addMy_Send(replyMessager, date, "", false, id_message, true, 'last', id_message);
                        }
                        else
                        {
                            $('#replyMessager').val("");
                            addMy_Send(replyMessager, date, response.message_id, true, 'last', id_message);
                        }
                    })
                }

                if(id_user != "" && $('#all_file_send').find('.file_send').length > 0)
                {
                    var FileSend =  $('#all_file_send').find('.file_send');
                    $.each(FileSend, function(i, v){
                        var url = $(v).attr('url');
                        $.post('https://graph.facebook.com/<?=$VersionAppFB?>/me/messages',
                            {
                                access_token:$.cookie('access_token_page_active'),
                                recipient:{"id": id_user},
                                message:{
                                    "attachment": {
                                        'type' : 'image',
                                        'payload' : {'url' : url, 'is_reusable':true},
                                    }
                                }
                            },function(response){
                                $(v).find('.close_file').click();
                            })
                    })
                }
            }
        }
    });

    $(window).bind("load", function() {
        $(document).ready(function() {
            GetMessager();
        })
    })


    $('body').on('click', 'label[for="taghidden"]', function(e){
         $('.div_tag_hidden').find('.ui-widget-content').click();
    })

</script>
