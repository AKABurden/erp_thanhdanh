<?php
    $VersionAppFB   =   get_option('VersionAppFB');
    $IdAppFB        =   get_option('IdAppFB');
    $basePath       =   module_dir_url('messager', 'uploads/');
    $baseAssets     =   module_dir_url('messager', 'assets/');
?>

<script src="<?= $baseAssets.'plugins/app-build/jquery-with-ui.min.js';?>"></script>
<script src="<?= $baseAssets.'plugins/bootstrap/js/bootstrap.min.js';?>"></script>
<script src="<?= $baseAssets.'messager_fb/js/jquery.cookie.js';?>"></script>
<script src="<?= $baseAssets.'messager_fb/js/main.js';?>"></script>
<script src="https://connect.facebook.net/vi_VN/sdk.js"></script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=<?=$VersionAppFB?>&appId=<?=$IdAppFB?>&autoLogAppEvents=1"></script>


<script>

    $(document).ready(function() {
        $.ajaxSetup({ cache: true });
        FB.init({
            appId: '<?=$IdAppFB?>',
            version: '<?=$VersionAppFB?>'
        });
    });

</script>

<script>
    $('body').on('click','.content-profile',function(e){
        $('.comment-empty').addClass('hide');
        var id_post = $(this).attr('id_post');
        $('#replyMessager').attr('id_comment', id_post);
        if(id_post != "")
        {
            FB.api(
                "/"+id_post+"?access_token="+$.cookie('access_token_page_active')+'&fields=full_picture,attachments,updated_time,type,link,permalink_url&sdk=0&suppress_http_code=1&pretty=0&sdk=joey',
                function (response_messager) {
                    if (response_messager && !response_messager.error)
                    {
                        console.log(response_messager)
                        GetComment(id_post);
                    }
                }
            )
        }
    })


    function GetComment(id_post)
    {
        FB.api(
            "/"+id_post+"/comments?access_token="+$.cookie('access_token_page_active')+'&fields=attachment,message,from,created_time,link,comments.limit(5)&sdk=0&suppress_http_code=1&pretty=0&sdk=joey',
            function (response_messager) {
                if (response_messager && !response_messager.error)
                {
                    console.log(response_messager)
                }
            }
        )
    }


    $('body').on('keypress','#replyMessager',function(event){
        if(event.keyCode == 13)
        {
            if(!event.shiftKey){
                var replyMessager = $(this).val();
                $('#replyMessager').val("");
                var id_comment = $(this).attr('id_comment');
                if($.trim(replyMessager) != "" && id_comment != "")
                {
                    replyMessager =$.trim(replyMessager);

                    var date = new Date();
                    var id_message = $('.content-profile.active').attr('id_senders');
                    $.post('https://graph.facebook.com/<?=$VersionAppFB?>/'+id_comment+'/comments?access_token='+$.cookie('access_token_page_active'),{message:replyMessager, pretty:0, sdk:'joey', suppress_http_code:1},function(response){
                        if(response.error)
                        {
                            $('#replyMessager').val("");
                            // addMy_Send(replyMessager, date, "", false, id_message, true, 'last', id_message);
                        }
                        else
                        {
                            $('#replyMessager').val("");
                            // addMy_Send(replyMessager, date, response.message_id, true, 'last', id_message);
                        }
                    })
                }

                //if(id_user != "" && $('#all_file_send').find('.file_send').length > 0)
                //{
                //    var FileSend =  $('#all_file_send').find('.file_send');
                //    $.each(FileSend, function(i, v){
                //        var url = $(v).attr('url');
                //        $.post('https://graph.facebook.com/<?//=$VersionAppFB?>///me/messages',
                //            {
                //                access_token:$.cookie('access_token_page_active'),
                //                recipient:{"id": id_user},
                //                message:{
                //                    "attachment": {
                //                        'type' : 'image',
                //                        'payload' : {'url' : url, 'is_reusable':true},
                //                    }
                //                }
                //            },function(response){
                //                $(v).find('.close_file').click();
                //            })
                //    })
                //}
            }
        }
    });

    function AddPost()
    {

    }
</script>

<script src="https://js.pusher.com/4.4/pusher.min.js"></script>