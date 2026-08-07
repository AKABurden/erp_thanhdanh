<?php

init_tail();
$VersionAppFB   =   get_option('VersionAppFB');
$IdAppFB        =   get_option('IdAppFB');
$basePath       =   module_dir_url('messager', 'uploads/');
$baseAssets     =   module_dir_url('messager', 'assets/');
?>
<script src="<?= $baseAssets.'messager_fb/js/main.js';?>"></script>


<script src="<?= $baseAssets.'messager_fb/js/jquery.cookie.js';?>"></script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=<?=$VersionAppFB?>&appId=<?=$IdAppFB?>&autoLogAppEvents=1"></script>
<script>
    var leadUniqueValidationFields = ["email"];
    var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;

    $(document).ready(function() {
        $.ajaxSetup({ cache: true });
        FB.init({
            appId: '<?=$IdAppFB?>',
            version: '<?=$VersionAppFB?>'
        });
    });


    $(document).on('click','.content-profile',function(e){
        reloader();
        $('#replyMessager').attr('id_user',"");
        var id_user = $(this).attr('id_user');
        var name = $(this).find('.name-profile').html();
        $('.chat-area-reply').removeClass('hide');



        $('.close_file').trigger('click');
        //hau
        $('#id_facebook').val(id_user);
        // find_uid_facebook(id_user);
        //hau


        var id_message = $(this).attr('id_senders');
        var src = $(this).find('img').prop('src');
        
        $('.content-profile').removeClass('active');
        $(this).addClass('active');
        $('#replyMessager').attr('id_user',id_user);
        if($('#tab_'+id_message).length > 0)
        {
            $('div[href="#tab_'+id_message+'"]').tab('show');
            $('.id_profile_chat').prop('src', src);
            $('.id_name_profile_chat').html(name);

            setTimeout(function(){
                var div_content = $('#tab_'+id_message);
                $('#chat_content_body').scrollTop(div_content.innerHeight());
            }, 500);
        }
        else
        {
            $.get('<?=admin_url()?>messager/getJson_message', {id: id_message}, function (data) {

                $('#chat_content_body').append(data);
                $('div[href="#tab_' + id_message + '"]').tab('show');
                $('.id_profile_chat').prop('src', src);
                $('.id_name_profile_chat').html(name);

                var div_content = $('#tab_'+id_message);
                $('#chat_content_body').scrollTop(div_content.innerHeight());
            })
        }

        $('#name-customer-right').html(name);
        $('#form_action_client').find('input[name="company"]').val(name);
        var img = '<img src="https://graph.facebook.com/'+id_user+'/picture?height=100&width=100&access_token=<?=$_COOKIE['access_token_page_active']?>">';
        $('#img-customer').html(img);
        varInfoUser(id_user); // lấy thông tin khashc hàng hàng tiềm năng
    })

    function change_fanpage(_this)
    {
        var name_fanpage = $(_this).attr('name_fanpage');
        var access_token = $(_this).attr('access_token');
        var id_fanpage   = $(_this).attr('id_fanpage');
        $.cookie("page_active", id_fanpage, { path: '/' });
        $.cookie("access_token_page_active", access_token, { path: '/' });
        $.cookie("name_page_active", name_fanpage, { path: '/' });
        location.reload();
    }


    $( "#chat_content_body" ).scroll(function() {
        if($(this).scrollTop() == 0)
        {
            var id_message = $('.content-profile.active').attr('id_senders');
            var id_client = $('#replyMessager').attr('id_user');

            var limit = $('#chat_content_body').find('.messages-container').length;
            var new_limit = limit+10;
            $('#chat_content_body').find('.tab-pane.fade.active').attr('limit', new_limit);
            FB.api(
                "/"+id_message+"/messages?access_token="+$.cookie('access_token_page_active')+'&fields=message,from,to,created_time,tags,attachments&limit='+new_limit,
                function (response_messager) {
                    if (response_messager && !response_messager.error)
                    {
                        for(var i = 0; i < (response_messager.data.length - 1); i++)
                        {
                            if($('#'+response_messager.data[i].id).length == 0)
                            {
                                var date = new Date(response_messager.data[i].created_time);

                                if(response_messager.data[i].attachments)
                                {
                                    if(!response_messager.data[i].message)
                                    {
                                        response_messager.data[i].message = "";
                                    }
                                    $.each(response_messager.data[i].attachments.data, function(ii,vv){
                                        if(vv.mime_type == 'image/jpeg')
                                        {
                                            response_messager.data[i].message += '<img class="mtop10" src="'+vv.image_data.url+'">';
                                        }
                                    })
                                }
                                if(response_messager.data[i].from['id'] == $.cookie('page_active'))
                                {
                                    addMy_Send(response_messager.data[i].message, date, response_messager.data[i].id, true, 'first', id_message);

                                }
                                else
                                {
                                    addClient_Send(response_messager.data[i].message,id_client, date, response_messager.data[i].id, true, 'first', id_message);
                                }
                            }

                        }
                    }
                }
            )
        }
    })


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

    function addMy_Send(replyMessager, date, message_id = "", err = true, type = 'last', id_message)
    {
        var div_messager = $('#tab_'+id_message).find('.chat-area-content-profile').find('.content-message:'+type);
        var time_now = Math.floor(date.getTime()/1000);
        if(div_messager.hasClass('my-message'))
        {
            if(type == 'last')
            {
                time_messager = div_messager.attr('time');
                if((time_now - time_messager) > 3600)
                {
                    $('.chat-area-content-profile').append('<div class="time-chat"><span>'+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+'</span></div>');
                    $('.chat-area-content-profile').append('<div class="my-batch-content-container"><div class="my-messages"><div class="my-messages-container content-message my-message" time="'+time_now+'" id="'+message_id+'"><span>'+replyMessager+'</span></div></div></div>');
                }
                else
                {
                    $('.chat-area-content-profile').find('.my-messages:'+type).append('<div class="my-messages-container content-message my-message" time="'+time_now+'" id="'+message_id+'"><span>'+replyMessager+'</span></div>');

                }
            }
            else
            {
                time_messager = div_messager.attr('time');
                if((time_now - time_messager) < 3600)
                {
                    $('#tab_'+id_message).find('.chat-area-content-profile').prepend('<div class="my-batch-content-container"><div class="my-messages"><div class="my-messages-container content-message my-message" time="'+time_now+'" id="'+message_id+'"><span>'+replyMessager+'</span></div></div></div>');
                    $('#tab_'+id_message).find('.chat-area-content-profile').prepend('<div class="time-chat"><span>'+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+'</span></div>');
                }
                else
                {
                    $('#tab_'+id_message).find('.chat-area-content-profile').find('.my-messages:'+type).prepend('<div class="my-messages-container content-message my-message" time="'+time_now+'" id="'+message_id+'"><span>'+replyMessager+'</span></div>');

                }
            }

        }
        else
        {
            if(type == 'last')
            {
                var date = new Date();
                time_messager = div_messager.attr('time');
                if((time_now - time_messager) > 3600)
                {
                    $('.chat-area-content-profile').append('<div class="time-chat"><span>'+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+'</span></div>');
                }
                $('.chat-area-content-profile').append('<div class="my-batch-content-container"><div class="my-messages"><div class="my-messages-container content-message my-message" time="'+time_now+'" id="'+message_id+'"><span>'+replyMessager+'</span></div></div></div>');
            }
            else
            {
                var date = new Date();
                time_messager = div_messager.attr('time');
                if((time_now - time_messager) < 3600)
                {
                    $('.chat-area-content-profile').prepend('<div class="time-chat"><span>'+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+'</span></div>');
                }
                $('.chat-area-content-profile').prepend('<div class="my-batch-content-container"><div class="my-messages"><div class="my-messages-container content-message my-message" time="'+time_now+'" id="'+message_id+'"><span>'+replyMessager+'</span></div></div></div>');

            }
        }

        if(type == "last")
        {
            var div_content = $('#tab_'+id_message);
            $('#chat_content_body').scrollTop(div_content.innerHeight());
        }
    }

    function addClient_Send(replyMessager, id_client = "", date, message_id = "", err = false, type = 'last', id_message)
    {
        var div_messager = $('#tab_'+id_message).find('.chat-area-content-profile').find('.content-message:'+type);
        var time_now = Math.floor(date.getTime()/1000);
        if(div_messager.hasClass('client-message'))
        {
            time_messager = div_messager.attr('time');
            if(type == 'last')
            {
                if((time_now - time_messager) > 3600)
                {
                    $('#tab_'+id_message).find('.chat-area-content-profile').append('<div class="time-chat"><span>'+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+'</span></div>');
                    $('#tab_'+id_message).find('.chat-area-content-profile').append('<div class="batch-content-container">' +
                        '    <div class="avatar">'+
                        '        <img src="https://graph.facebook.com/'+id_client+'/picture?height=100&width=100&access_token='+$.cookie('access_token_page_active')+'">'+
                        '    </div>'+
                        '    <div class="messages">' +
                        '        <div class="messages-container content-message client-message" time="'+time_now+'" id="'+message_id+'">' +
                        '            <span>'+replyMessager+'</span>' +
                        '        </div>' +
                        '    </div>' +
                        '</div>');
                }
                else
                {
                    $('#tab_'+id_message).find('.chat-area-content-profile').find('.messages:'+type).append('<div class="messages-container content-message client-message" time="'+Math.floor(date.getTime()/1000)+'" id="'+message_id+'"><span>'+replyMessager+'</span></div>');

                }
            }
            else
            {

                if((time_messager - time_now) > 3600)
                {
                    $('#tab_'+id_message).find('.chat-area-content-profile').prepend('<div class="batch-content-container">' +
                        '    <div class="avatar">'+
                        '        <img src="https://graph.facebook.com/'+id_client+'/picture?height=100&width=100&access_token='+$.cookie('access_token_page_active')+'">'+
                        '    </div>'+
                        '    <div class="messages">' +
                        '        <div class="messages-container content-message client-message" time="'+time_now+'" id="'+message_id+'">' +
                        '            <span>'+replyMessager+'</span>' +
                        '        </div>' +
                        '    </div>' +
                        '</div>');
                    $('#tab_'+id_message).find('.chat-area-content-profile').prepend('<div class="time-chat"><span>'+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+'</span></div>');

                }
                else
                {
                    $('#tab_'+id_message).find('.chat-area-content-profile').find('.messages:'+type).prepend('<div class="messages-container content-message client-message" time="'+time_messager+'" id="'+message_id+'"><span>'+replyMessager+'</span></div>');

                }
            }
        }
        else
        {
            var date = new Date();
            time_messager = div_messager.attr('time');
            if(type == 'last') {
                if ((time_now - time_messager) > 3600) {
                    $('.chat-area-content-profile').append('<div class="time-chat"><span>' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds() + '</span></div>');
                }
                $('#tab_'+id_message).find('.chat-area-content-profile').append('<div class="batch-content-container">' +
                    '    <div class="avatar">' +
                    '        <img src="https://graph.facebook.com/' + id_client + '/picture?height=100&width=100&access_token=' + $.cookie('access_token_page_active') + '">' +
                    '    </div>' +
                    '    <div class="messages">' +
                    '        <div class="messages-container content-message client-message" time="' + time_now + '" id="' + message_id + '">' +
                    '            <span>' + replyMessager + '</span>' +
                    '        </div>' +
                    '    </div>' +
                    '</div>');
            }
            else
            {

                if ((time_messager - time_now) > 3600) {
                    $('.chat-area-content-profile').prepend('<div class="time-chat"><span>' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds() + '</span></div>');
                }
                $('#tab_'+id_message).find('.chat-area-content-profile').prepend('<div class="batch-content-container">' +
                    '    <div class="avatar">' +
                    '        <img src="https://graph.facebook.com/' + id_client + '/picture?height=100&width=100&access_token=' + $.cookie('access_token_page_active') + '">' +
                    '    </div>' +
                    '    <div class="messages">' +
                    '        <div class="messages-container content-message client-message" time="' + time_messager + '" id="' + message_id + '">' +
                    '            <span>' + replyMessager + '</span>' +
                    '        </div>' +
                    '    </div>' +
                    '</div>');
            }
        }

        if(type == "last")
        {
            var div_content = $('#tab_'+id_message);
            $('#chat_content_body').scrollTop(div_content.innerHeight());
        }
        OrderByDiv(id_client);
    }


    function GetMessager()
    {
        var Client_chat = $('#replyMessager').attr('id_user');
        if(typeof(Client_chat) == 'undefined')
        {
            Client_chat = "";
        }
        FB.api(
            "/"+$.cookie('page_active')+"/conversations?access_token="+$.cookie('access_token_page_active')+'&fields=updated_time,senders',
            function (response) {
                if (response && !response.error) {
                    $.each(response.data, function(i,v){
                        if(Client_chat.length > 0 && Client_chat == v.senders.data[0].id)
                        {
                            FB.api(
                                "/"+v.id+"/messages?access_token="+$.cookie('access_token_page_active')+'&fields=message,from,to,created_time,tags,attachments&limit=16',
                                function (response_messager) {
                                    if (response_messager && !response_messager.error)
                                    {
                                        var CountWarting = 0;
                                        for(var i = (response_messager.data.length - 1); i >= 0; i--)
                                        {
                                            if($('#'+response_messager.data[i].id).length == 0)
                                            {
                                                var date = new Date(response_messager.data[i].created_time);
                                                if(response_messager.data[i].attachments)
                                                {
                                                    if(!response_messager.data[i].message)
                                                    {
                                                        response_messager.data[i].message = "";
                                                    }
                                                    $.each(response_messager.data[i].attachments.data, function(ii,vv){
                                                        if(vv.mime_type == 'image/jpeg')
                                                        {
                                                            response_messager.data[i].message += '<img class="mtop10" src="'+vv.image_data.url+'">';
                                                        }
                                                    })
                                                }

                                                if(response_messager.data[i].from['id'] == $.cookie('page_active'))
                                                {
                                                    addMy_Send(response_messager.data[i].message, date, response_messager.data[i].id, true, 'last', v.id);

                                                }
                                                else
                                                {

                                                    addClient_Send(response_messager.data[i].message,v.senders.data[0].id, date, response_messager.data[i].id, true, 'last', v.id);
                                                }
                                            }

                                            if(v.senders.data[0].id == response_messager.data[i].from['id'])
                                            {
                                                CountWarting++;
                                                $.each(response_messager.data[i].tags.data, function(ii,vv){
                                                    if(vv.name == 'read')
                                                    {
                                                        CountWarting--;
                                                        return false;
                                                    }
                                                })
                                            }
                                            if(i == 0)
                                            {
                                                if(!response_messager.data[i].attachments) {
                                                    var string_data_messager = response_messager.data[i].message;
                                                    if (response_messager.data[i].message.length > 25) {
                                                        string_data_messager = response_messager.data[i].message.substr(0, 25) + '...';
                                                    }
                                                    $('#chat_' + v.senders.data[0].id).html(string_data_messager);
                                                }
                                                else
                                                {
                                                    $('#chat_' + v.senders.data[0].id).html('<i class="fa fa-picture-o"></i>');
                                                }
                                            }

                                        }
                                        if(CountWarting > 0)
                                        {
                                            $('#'+v.senders.data[0].id).html( (CountWarting > 5 ? '5+' : CountWarting) );
                                            $('#'+v.senders.data[0].id).removeClass('hide');
                                        }
                                        else
                                        {
                                            $('#'+v.senders.data[0].id).html("");
                                            $('#'+v.senders.data[0].id).addClass('hide');
                                        }
                                    }
                                }
                            )
                        }
                        else
                        {

                            FB.api(
                                "/"+v.id+"/messages?access_token="+$.cookie('access_token_page_active')+'&fields=message,from,to,created_time,tags,attachments&limit=16',
                                function (response_messager) {
                                    if (response_messager && !response_messager.error)
                                    {
                                        var CountWarting = 0;
                                        for(var i = (response_messager.data.length - 1); i >= 0; i--) {
                                            if (v.senders.data[0].id == response_messager.data[i].from['id']) {
                                                CountWarting++;
                                                $.each(response_messager.data[i].tags.data, function (ii, vv) {
                                                    if (vv.name == 'read') {
                                                        CountWarting--;
                                                        return false;
                                                    }
                                                })
                                            }
                                            if(i == 0)
                                            {

                                                if(!response_messager.data[i].attachments) {
                                                    var string_data_messager = response_messager.data[i].message;
                                                    if (response_messager.data[i].message.length > 25) {
                                                        string_data_messager = response_messager.data[i].message.substr(0, 25) + '...';
                                                    }
                                                    $('#chat_' + v.senders.data[0].id).html(string_data_messager);
                                                }
                                                else
                                                {
                                                    $('#chat_' + v.senders.data[0].id).html('<i class="fa fa-picture-o"></i>');
                                                }
                                            }
                                        }
                                        if(CountWarting > 0)
                                        {
                                            $('#'+v.senders.data[0].id).html( (CountWarting > 5 ? '5+' : CountWarting) );
                                            $('#'+v.senders.data[0].id).removeClass('hide');
                                        }
                                        else
                                        {
                                            $('#'+v.senders.data[0].id).html("");
                                            $('#'+v.senders.data[0].id).addClass('hide');
                                        }

                                    }
                                }
                            )
                        }
                    })
                }
            }
        )

    }



    function LogoutFB(){
        $.cookie("page_active", '', { expires: -1, path: '/' });
        $.cookie("access_token_page_active", '', { expires: -1, path: '/' });
        $.cookie("name_page_active", '', { expires: -1, path: '/' });
        $.cookie("access_token_page", '', { expires: -1, path: '/' });
        $.cookie("user_token", '', { expires: -1, path: '/' });
        location.reload();
    }
    $(window).bind("load", function() {
        GetMessager();
    })


    $(document).on('keyup','#search_customer',function(e){
        var phone_number = $(this).val();
        var data = {phone_number:phone_number};
        var from = $('#form_action_client');
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post('<?=base_url()?>messager/get_lead_to_phone', data, function(data){
            data = JSON.parse(data);
            $('#form_action_client').find('#browsers_list_from').html('');
            if(data.search)
            {
                if(data.data)
                {
                    from.trigger('reset');
                    if(data.data.id_facebook)
                    {
                        if($('.content-profile[id_user="'+data.data.id_facebook+'"]').length)
                        {
                            $('.content-profile[id_user="'+data.data.id_facebook+'"]').trigger('click');
                            return false;
                        }
                        else
                        {
                            alert_float('danger', 'Không tìm thấy khách hàng trong cuộc trò truyện');
                        }
                    }

                    from.find('#img-customer').attr('href', '<?=base_url('assets/images/user-placeholder.jpg')?>');
                    if(data.type_data == 'KHTN')
                    {
                        from.find('.span_type_client').html('(KHTN)');
                        from.find('.email_client').val(data.data.email);
                        from.find('.note_profile').val(data.data.description);
                        from.find('input[name="id"]').val(data.data.id);
                        from.find('input[name="zcode"]').val(data.data.zcode);
                        from.find('input[name="chang_action"][value="1"]').prop('checked', true);
                        if(data.data.lead_image)
                        {
                            from.find('#img-customer').find('img').attr('src', data.data.lead_image);
                        }
                        $('#name-customer-right').html(data.data.name);
                        from.find('input[name="company"]').val(data.data.name);
                    }
                    else
                    {
                        from.find('.span_type_client').html('(KH)');
                        from.find('.email_client').val(data.data.client);
                        from.find('.note_profile').val(data.data.note);
                        from.find('input[name="userid"]').val(data.data.userid);
                        from.find('input[name="id"]').val(data.data.leadid);
                        from.find('input[name="zcode"]').val(data.data.zcode);
                        from.find('input[name="chang_action"][value="2"]').prop('checked', true);
                        if(data.data.client_image)
                        {
                            from.find('#img-customer').find('img').attr('src', data.data.client_image);
                        }
                        $('#name-customer-right').html(data.data.company);
                        from.find('input[name="company"]').val(data.data.company);
                    }
                    from.find('.phone_number_client').val(data.data.phonenumber);
                    from.find('.address_client').val(data.data.address);
                    from.find('input[name="gender"][value="'+data.data.gender+'"]').prop('checked', true);
                    from.find('.action_profile').addClass('hide');
                    from.find('#update_profile').removeClass('hide');
                    return  true;
                }
            }
            else
            {
                $.each(data.client, function(i, v){
                    from.find('#browsers_list_from').append('<option value="'+v.company+' - '+v.phonenumber+' - '+v.userid+' - KH" data-id="'+v.userid+'">');
                })
                $.each(data.lead, function(i, v){
                    from.find('#browsers_list_from').append('<option value="'+v.name+' - '+v.phonenumber+' - '+v.id+' - KHTN" data-id="'+v.id+'">');
                })
            }
            return  false;
        })
    })
    function varInfoUser(id_facebook) {
        var data = {id_facebook:id_facebook};
        var from = $('#form_action_client');
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post('<?=base_url()?>messager/get_lead_to_facebook', data, function(data){
            data = JSON.parse(data);
            if(data.data)
            {
                from.trigger('reset');
                from.find('.phone_number_client').val(data.data.phonenumber);
                from.find('.address_client').val(data.data.address);
                from.find('input[name="gender"][value="'+data.data.gender+'"]').prop('checked', true);
                from.find('.zcode').val(data.data.zcode);
                if(data.type_data == 'KH')
                {
                    from.find('.email_client').val(data.data.email_client);
                    from.find('.note_profile').val(data.data.note);
                    from.find('.span_type_client').html('(KH)');
                    from.find('input[name="userid"]').val(data.data.userid);
                    from.find('input[name="id"]').val('');
                    from.find('input[name="chang_action"][value="2"]').prop('checked', true);
                    if(data.data.client_image != "")
                    {
                        from.find('#img-customer').find('img').attr('src', data.data.client_image);
                    }
                    $('#name-customer-right').html(data.data.company);
                    from.find('input[name="company"]').val(data.data.company);
                }
                else if(data.type_data == 'KHTN')
                {
                    from.find('.email_client').val(data.data.email);
                    from.find('.note_profile').val(data.data.description);
                    from.find('.span_type_client').html('(KHTN)');
                    from.find('input[name="id"]').val(data.data.id);
                    from.find('input[name="chang_action"][value="1"]').prop('checked', true);
                    if(data.data.lead_image!= "")
                    {
                        from.find('#img-customer').find('img').attr('src', data.data.lead_image);
                    }
                    $('#name-customer-right').html(data.data.name);
                    from.find('input[name="company"]').val(data.data.name);
                }
                from.find('.action_profile').addClass('hide');
                from.find('#update_profile').removeClass('hide');
            }
            else
            {
                from.trigger('reset');
                from.find('input[name="gender"][value="1"]').prop('checked', true);
                from.find('input[name="company"]').val($.trim($('#name-customer-right').html()));
                from.find('.span_type_client').html('');
                from.find('.action_profile').removeClass('hide');
                from.find('#update_profile').addClass('hide');
            }
        })
    }

    $(function() {
        appValidateForm($('#form_action_client'), {
            phonenumber: 'required',
            address: 'required',
            email: 'required',
            id_facebook: 'required'
        }, manageAction_client);
        function manageAction_client(form) {
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function(response) {
                console.log(response);
                response = JSON.parse(response);
                alert_float(response.alert_type, response.message);
                if (response.success == true) {
                    $('#form_action_client').find('.action_profile').addClass('hide');
                    $('#form_action_client').find('#update_profile').removeClass('hide');

                }
            });
            return false;
        }
    })


</script>


<script src="https://js.pusher.com/4.4/pusher.min.js"></script>


<script>
    Pusher.logToConsole = false;

    var pusher = new Pusher('3ffdad22ae304306f311', {
        cluster: 'eu',
        forceTLS: true
    });

    var channel = pusher.subscribe($.cookie('page_active'));
    channel.bind('GetNewMessager', function(data)
    {
        var id_senders = $('.content-profile[id_user="'+data.message.sender.id+'"]').attr('id_senders');
        if(data.message.sender.id == $.cookie('page_active'))
        {
            var date = new Date();
            addMy_Send(data.message.message.text, data.message.timestamp, data.message.message.mid, true, 'last', id_senders);
        }
        else
        {

            if(data.message.message)
            {
                if($('.content-profile[id_user="'+data.message.sender.id+'"]').length > 0) {
                    if (data.message.message.attachments)
                    {
                        data.message.message.text = "";
                        $.each(data.message.message.attachments, function (i, v) {
                            if (v.type == 'image') {
                                data.message.message.text += '<img class="mtop10" src="' + v.payload.url + '"/>';
                            }
                        })
                    }

                    var date = new Date();
                    addClient_Send(data.message.message.text, data.message.sender.id, date, data.message.message.mid, true, 'last', id_senders);

                    if (!data.message.message.attachments) {
                        var string_data_messager = data.message.message.text;
                        if (data.message.message.text.length > 25) {
                            string_data_messager = data.message.message.text.substr(0, 25) + '...';
                        }
                        $('#chat_' + data.message.sender.id).html(string_data_messager);
                    } else {
                        $('#chat_' + data.message.sender.id).html('<i class="fa fa-picture-o"></i>');
                    }

                    var CountWarting = $('#' + data.message.sender.id).html();
                    $('#' + data.message.sender.id).removeClass('hide');
                    if ($.isNumeric(CountWarting) && CountWarting <= 4) {
                        $('#' + data.message.sender.id).html(parseFloat(CountWarting) + 1);
                    } else {
                        if (CountWarting == "") {
                            $('#' + data.message.sender.id).html(1);
                        } else {
                            $('#' + data.message.sender.id).html('5+');
                        }
                    }
                }
                else
                {

                    if (data.message.message.attachments) {
                        data.message.message.text = "";
                        $.each(data.message.message.attachments, function (i, v) {
                            if (v.type == 'image') {
                                data.message.message.text += '<img class="mtop10" src="' + v.payload.url + '"/>';
                            }
                        })
                    }
                    CreateProfileMessager(data.message.sender.id,data.message.message.text);
                }

            }
            else
            {
                FB.api(
                    "/"+id_senders+"/messages?access_token="+$.cookie('access_token_page_active')+'&fields=message,from,to,created_time,tags,attachments&limit=16',
                    function (response_messager) {
                        if (response_messager && !response_messager.error)
                        {
                            var CountWarting = 0;
                            for(var i = (response_messager.data.length - 1); i >= 0; i--)
                            {
                                if($('#'+response_messager.data[i].id).length == 0)
                                {
                                    var date = new Date(response_messager.data[i].created_time)

                                    if(response_messager.data[i].attachments)
                                    {
                                        if(!response_messager.data[i].message)
                                        {
                                            response_messager.data[i].message = "";
                                        }
                                        $.each(response_messager.data[i].attachments.data, function(ii,vv){
                                            if(vv.mime_type == 'image/jpeg')
                                            {
                                                response_messager.data[i].message += '<img class="mtop10" src="'+vv.image_data.url+'">';
                                            }
                                            if(vv.mime_type == 'file')
                                            {
                                                response_messager.data[i].message += '<a target="_blank"  href="'+vv.image_data.url+'">'+File+'</a>';
                                            }
                                        })
                                    }
                                    if(response_messager.data[i].from['id'] == $.cookie('page_active'))
                                    {
                                        addMy_Send(response_messager.data[i].message, date, response_messager.data[i].id, true, 'last', id_senders);
                                        OrderByDiv(response_messager.data[i].to['id']);
                                        
                                    }
                                    else
                                    {
                                        addClient_Send(response_messager.data[i].message, data.message.recipient.id, date, response_messager.data[i].id, true, 'last', id_senders);
                                        if(!response_messager.data[i].attachments) {
                                            var string_data_messager = response_messager.data[i].message;
                                            if (response_messager.data[i].message.length > 25) {
                                                string_data_messager = response_messager.data[i].message.substr(0, 25) + '...';
                                            }
                                            $('#chat_' + data.message.recipient.id).html(string_data_messager);
                                        }
                                        else
                                        {
                                            $('#chat_' + data.message.recipient.id).html('<i class="fa fa-picture-o"></i>');
                                        }
                                    }
                                }


                            }
                        }
                    }
                )
            }
        }
    });
</script>

<script>
    function GetFilePC()
    {
        $('#form_uploadfile')[0].reset();
        $('input#file').click();
    }
    $('body').on('change','input#file',function(e){
        if($('#replyMessager').attr('id_user'))
        {
            var form = $('#form_uploadfile');
            var file_data = $('input[type="file"]').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('userid', $('#replyMessager').attr('id_user'));
            $.ajax({
                url: form.attr('action'),
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (data) {
                    console.log(data);
                    if(data.success)
                    {
                        $('#all_file_send').append('<div class="file_send"  url="'+data.url+'">\
                                                        <div class="name_file">\
                                                            <i class="fa fa-picture-o" aria-hidden="true"></i>\
                                                            '+data.name+'\
                                                        </div>\
                                                        <div class="close_file" newfile="'+data.newfile+'">X</div>\
                                                    </div>'
                                                   );
                    }
                }
            });
        }
    })
    $('body').on('click', '.close_file', function(e){
        var url = $(this).attr('newfile');
        $.post("<?=base_url('messegar/deleteFile')?>",{url:url},function(data){

        })
        $(this).parent('div').remove();
    })

    function GetFileLink()
    {
        $('#file_link').modal('show');
    }
    $('#file_link').on('hidden.bs.modal', function (e){
        ('#file_link').val('');
    })
    function SendFileLink()
    {
        var id_user = $('#replyMessager').attr('id_user');
        var url = $('#input_file_link').val();
        if(url != "")
        {
            $('#input_file_link').val('');
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
                    $('#file_link').modal('hide');
                })
        }
    }


    function AddPhotoModal()
    {
        $('#fanpage_photo_modal').find('.body_modal').html('');
        var access_token = $.cookie('access_token_page_active');
        FB.api(
            "/"+$.cookie('page_active')+"/photos?access_token="+access_token,
            function (response) {
                console.log(response)
                if (response && !response.error) {
                    $.each(response.data, function(i, v){
                        $('#fanpage_photo_modal').find('.body_modal').append('<div class="col-md-4"><a onclick="AddImgSend('+v.id+')"><img style="width:100px;height:100px;" src="https://graph.facebook.com/'+v.id+'/picture?height=100&width=100&access_token='+$.cookie('access_token_page_active')+'"></a></div>');
                    })
                }
            }
        );
        $('#fanpage_photo_modal').modal('show');
    }

    function AddImgSend(id)
    {
        var url = 'https://graph.facebook.com/'+id+'/picture?height=100&width=100&access_token='+$.cookie('access_token_page_active');
        var time = new Date();
        $('#all_file_send').append('<div class="file_send"  url="'+url+'">\
                                                        <div class="name_file">\
                                                            <i class="fa fa-picture-o" aria-hidden="true"></i>\
                                                            '+time.getTime()+'\
                                                        </div>\
                                                        <div class="close_file" newfile="">X</div>\
                                                    </div>'
        );
        $('#fanpage_photo_modal').modal('hide');
    }
</script>

<script>
    function OrderByDiv(id_user)
    {
        if(id_user != "")
        {
            var div_profile_first = $('.list-profile').find('.content-profile[id_user="'+id_user+'"]');
            var string_html = div_profile_first;
            $('.list-profile').prepend(string_html);
        }
    }

    function CreateProfileMessager(idClient, Messager)
    {
        if(idClient != "")
        {
            FB.api(
                "/"+$.cookie('page_active')+"/conversations?access_token="+$.cookie('access_token_page_active')+'&fields=updated_time,senders',
                function (response) {
                    if (response && !response.error) {
                        $.each(response.data, function(i,v) {
                            if (idClient == v.senders.data[0].id) {
                                console.log(v);
                                $('.list-profile').append('<div class="content-profile" id_senders="'+v.id+'" id_user="' + idClient + '" data-toggle="tab" href="#tab_'+v.id+'">\
                                                                <div class="img-info">\
                                                                    <img src="https://graph.facebook.com/' + idClient + '/picture?height=100&width=100&access_token=' + $.cookie('access_token_page_active') + '">\
                                                                </div>\
                                                                <div class="some-info">\
                                                                    <div class="name-profile">\
                                                                       '+v.senders.data[0].name+'\
                                                                    </div>\
                                                                    <div class="chat-profile" id="chat_'+idClient+'">'+Messager+'</div>\
                                                                </div>\
                                                                <div class="time-info">\
                                                                    Just now\
                                                                </div>\
                                                                <div class="count-inbox" id="'+idClient+'">1</div>\
                                                                <div class="clearfix"></div>\
                                                            </div>');
                                OrderByDiv(idClient);
                            }
                        })
                    }
                })
        }
    }
</script>