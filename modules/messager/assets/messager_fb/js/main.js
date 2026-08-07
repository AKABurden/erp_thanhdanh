function InitTagMessage(e) {
    var dataTag = $("body").find("input.tagstype").val();

    void 0 === e && (e = $("body").find("input.tagstype")), e.length && e.tagit({
        availableTags: C_available_tags,
        allowSpaces: !0,
        animate: !1,
        placeholderText: text_lableTag,
        showAutocompleteOnFocus: !0,
        caseSensitive: !1,
        autocomplete: {appendTo: "#inputTagsWrapper"},
        afterTagAdded: function (e, t) {
            var tname = $.trim($(t.tag).find(".tagit-label").text());
            var a = C_available_tags.indexOf(tname);
            if (a > -1) {
                var n = C_available_tags_ids[a];
                $(t.tag).addClass("tag-id-" + n);
                var color = C_available_tags_color[a];
                var background_color = C_available_tags_background_color[a];
                if(color != "")
                {
                    $(t.tag).find('.tagit-label').css('color', color);
                }


                var span_profile = $('<span class="label label-default inline-block pointer mtop5" id="tag-lef-'+n+'"></span>');
                if(background_color != "")
                {
                    $(t.tag).css('background-color', background_color);
                    $('#style_append').append("ul.tagit li.tagit-choice.tag-id-" + n +":hover {background:"+background_color+"!important;}");

                    span_profile.append('<i class="fa fa-circle" style="color:'+background_color+'" aria-hidden="true"></i> ');
                }
                else
                {
                    span_profile.append('<i class="fa fa-circle" aria-hidden="true"></i> ');
                }
                if($('.content-profile[id_user="'+$('#id_facebook').val()+'"]').find('.tag_left').find('#tag-lef-'+n).length == 0)
                {
                    span_profile.append(tname);
                    $('.content-profile[id_user="'+$('#id_facebook').val()+'"]').find('.tag_left').append(span_profile);
                }
            }
            else
            {
                $(t.tag).find('.tagit-close').click();
                alert_float('warning', 'Không tìm thấy tag');
            }
            showHideTagsPlaceholder($(this))
        },
        afterTagRemoved: function (e, t) {
            var tname = $.trim($(t.tag).find(".tagit-label").text());
            var a = C_available_tags.indexOf(tname);
            if (a > -1) {
                var n = C_available_tags_ids[a];
                $('.content-profile[id_user="'+$('#id_facebook').val()+'"]').find('.tag_left').find('#tag-lef-'+n).remove();
            }
            showHideTagsPlaceholder($(this))
        }
    })

    var form = $("body").find("input.tagstype").parents('form');
    if(form.length > 0)
    {
        ActionChangeType(dataTag, form);
    }
}

function InitTagMessage_Hidden(e) {
    void 0 === e && (e = $("body").find("input.tagstypehidden")), e.length && e.tagit({
        availableTags: C_available_tags,
        allowSpaces: !0,
        animate: !1,
        placeholderText: text_lableTag,
        showAutocompleteOnFocus: !0,
        caseSensitive: !1,
        autocomplete: {appendTo: "#inputTagsWrapper"},
        afterTagAdded: function (e, t) {
            var a = C_available_tags.indexOf($.trim($(t.tag).find(".tagit-label").text()));
            if (a > -1) {
                var n = C_available_tags_ids[a];
                $(t.tag).addClass("tag-id-" + n);
                $(".tagstypehidden").tagit("removeAll");
                $('.tagstype').tagit('createTag', $.trim($(t.tag).find(".tagit-label").text()));
                $('.tagstype').trigger('change');
            }
            else
            {
                $(t.tag).find('.tagit-close').click();
                alert_float('warning', 'Không tìm thấy tag');
            }
            showHideTagsPlaceholder($(this))
        },
        afterTagRemoved: function (e, t) {}
    })
}

//Chang thẻ tag
$(document).on('change', '.tagstype', function(e)
{
    var inputTag = $(this);
    var InVal = inputTag.val();
    var form = $(this).parents('form');
    ActionChangeType(InVal, form);
})

function ActionChangeType(InVal, form ,type =0)
{
    var KTinVal = InVal.split(',');
    var ActiveVal = 0;
    $.each(KTinVal, function(i, v){
        var KT = C_available_tags.indexOf(v);
        if(KT < 0)
        {
            ActiveVal = 1;
            return false;
        }
    })
    if(ActiveVal == 1)
    {
        return false;
    }
    if(form.hasClass('form_customer'))
    {
        if($('input[name="userid"]').val() != "")
        {
            var id = $('input[name="userid"]').val();
            var data = {};
            if (typeof (csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['tag'] = InVal;
            data['rel_type'] = 'client';
            $.post(admin_url+'messager/updateDataTag/'+id, data, function(res){
                console.log(res);
            })
        }
    }
    else if(form.hasClass('form_lead'))
    {
        if($('input[name="id"]').val() != "")
        {
            var id = $('input[name="id"]').val();
            var data = {};
            if (typeof (csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            data['tag'] = InVal;
            data['rel_type'] = 'lead';
            $.post(admin_url+'messager/updateDataTag/'+id, data, function(res){
                console.log(res);
            })
        }
    }
    else if(form.hasClass('form_listfb'))
    {
        var id = $('input[name="id"]').val();
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['tag'] = InVal;
        data['rel_type'] = 'listfb';
        $.post(admin_url+'messager/updateDataTag/'+id, data, function(res){
            console.log(res);
        })
    }
}

//Chang phân quyền
$(document).on('change', '#browsers_staff_assigned', function(e){
    var id_staff = $(this).val();
    var form = $('#form_action_client');
    if(form.hasClass('form_customer'))
    {
        if(form.find('input[name="userid"]').val())
        {
            var userid = form.find('input[name="userid"]').val();
            var data = {};
            data['userid'] = userid;
            data['id_staff'] = id_staff;
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(admin_url+'messager/staff_assigned_client', data, function(data){
                data = JSON.parse(data);
                alert_float(data.alert_type, data.message);
            })
        }
    }
    else if(form.hasClass('form_lead'))
    {
        if(form.find('input[name="id"]').val())
        {
            var id = form.find('input[name="id"]').val();
            var data = {};
            data['id'] = id;
            data['id_staff'] = id_staff;
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(admin_url+'messager/staff_assigned_lead', data, function(data){
                data = JSON.parse(data);
                alert_float(data.alert_type, data.message);
            })
        }
    }
    else if(form.hasClass('form_listfb'))
    {
        if(form.find('input[name="id"]').val())
        {
            var id = form.find('input[name="id"]').val();
            var data = {};
            data['id'] = id;
            data['id_staff'] = id_staff;
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(admin_url+'messager/staff_assigned_listfb', data, function(data){
                data = JSON.parse(data);
                alert_float(data.alert_type, data.message);
            })
        }
    }
})

//Button thêm khách hàng hoặc khách hàng tìm năng
$(document).on('click', '.btn_add_data', function(e){

    var button = $(this);
    button.button({loadingText: "<i class='fa fa-spinner fa-spin'></i>"});
    button.button('loading');
    var id_facebook = $('.content-profile.active').attr('id_user');
    var type = $(this).attr('id-data');
    var data = {};
    data['id_facebook'] = id_facebook;
    data['type'] = type;
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'messager/load_new_client', data, function(data){
        data = JSON.parse(data);
        if(data.data)
        {
            $('#content_customer').html(data.data);
            var content_profile = $('.content-profile.active');
            var src_img = content_profile.find('.img-info').find('img').attr('src');
            $('#img-customer').find('img').attr('src', src_img);

            var name = content_profile.find('.name-profile').text();
            $('#name-customer-right').text(name);
            $('#name-customer-right').parent().find('input[name="company"]').val(name);
            button.button('reset');
        }
    }).always(function() {
        button.button('reset')
    });
})

//lấy thông tin khách hoặc khách hàng tiềm năng có facebook
function varInfoUser(id_facebook) {
    var data = {id_facebook : id_facebook};
    var profile = $('.content-profile[id_user="'+id_facebook+'"]');
    var name = profile.find('.name-profile').html();
    if(name.length > 0)
    {
        data['name'] = name;
    }

    var from = $('#form_action_client');
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'messager/get_lead_to_facebook', data, function(data){
        data = JSON.parse(data);
        if(data.data)
        {
            if(data.type_data)
            {
                if(data.data)
                {
                    $('#content_customer').html(data.data);
                    $('.countOrder').html(intVal(data.countOrder));
                    $('.countAdvisory').html(intVal(data.countAdvisory));
                    if(data.advisory) {
                        $('.emtry-advisory').html(data.advisory);
                    }
                    else
                    {
                        $('.emtry-advisory').html("");
                    }

                    if(data.orders) {
                        $('#list_order').html(data.orders);
                    }
                    else
                    {
                        $('#list_order').html("");
                    }
                }
            }
            else
            {
                if(from.find('input[name="userid"]').val() || from.find('input[name="id"]').val() || $('#form_action_client').length == 0)
                {
                    $('.countOrder').html(intVal(data.countOrder));
                    $('.countAdvisory').html(intVal(data.countAdvisory));

                    $('#content_customer').html(data.data);
                    if(data.advisory) {
                        $('.emtry-advisory').html(data.advisory);
                    }
                    else
                    {
                        $('.emtry-advisory').html("");
                    }

                    if(data.orders) {
                        $('#list_order').html(data.orders);
                    }
                    else
                    {
                        $('#list_order').html("");
                    }
                }
                else
                {
                    $('#id_facebook').val(id_facebook);
                }
            }

        }
        else
        {
            if(data.type_data) {
                $('.countOrder').html(intVal(data.countOrder));
                $('.countAdvisory').html(intVal(data.countAdvisory));
                $('#content_customer').html(data.data);
                if(data.advisory) {
                    $('.emtry-advisory').html(data.advisory);
                }
                else
                {
                    $('.emtry-advisory').html("");
                }

                if(data.orders) {
                    $('#list_order').html(data.orders);
                }
                else
                {
                    $('#list_order').html("");
                }
            }
            else
            {
                if(from.find('input[name="userid"]').val() || from.find('input[name="id"]').val() || $('#form_action_client').length == 0)
                {
                    $('#content_customer').html(data.data);

                    $('.countOrder').html(intVal(data.countOrder));
                    $('.countAdvisory').html(intVal(data.countAdvisory));
                    if(data.advisory) {
                        $('.emtry-advisory').html(data.advisory);
                    }
                    else
                    {
                        $('.emtry-advisory').html("");
                    }

                    if(data.orders) {
                        $('#list_order').html(data.orders);
                    }
                    else
                    {
                        $('#list_order').html("");
                    }
                }
                else
                {
                    $('#id_facebook').val(id_facebook);
                }
            }
        }

        $('.content-profile').removeClass('active');
        if(!$('#id_facebook').val())
        {
            $('.content-profile[id_user="'+id_facebook+'"]').addClass('active');
        }
        else
        {
            $('.content-profile[id_user="'+$('#id_facebook').val()+'"]').addClass('active');
        }

        $('#list_advisory').collapse('hide');
        $('#list_order').collapse('hide');
    })
}

//Search khách hàng hoặc khtn
$(document).on('keyup','#search_customer',function(e){
    var phone_number = $(this).val();
    var data = {phone_number:phone_number};
    var from = $('#form_action_client');
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'messager/get_lead_to_phone', data, function(data){
        data = JSON.parse(data);
        $('#form_action_client').find('#browsers_list_from').html('');
        if(data.search)
        {
            if(data.data)
            {
                $('#content_customer').html(data.data);
                $('.countOrder').html(intVal(data.countOrder));
                $('.countAdvisory').html(intVal(data.countAdvisory));
                if(data.advisory) {
                    $('.emtry-advisory').html(data.advisory);
                }
                else
                {
                    $('.emtry-advisory').html("");
                }

                if(data.orders) {
                    $('#list_order').html(data.orders);
                }
                else
                {
                    $('#list_order').html("");
                }
            }
        }
        else
        {
            $.each(data.client, function(i, v){
                $('#browsers_list_from').append('<option value="'+v.company+' - '+v.phonenumber+' - '+v.userid+' - KH" data-id="'+v.userid+'">');
            })
            $.each(data.lead, function(i, v){
                $('#browsers_list_from').append('<option value="'+v.name+' - '+v.phonenumber+' - '+v.id+' - KHTN" data-id="'+v.id+'">');
            })
        }
    })
})

//Lấy thông tin tin nhắn message
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

// click profile facebook
$(document).on('click', '.content-profile', function(e){
    if(!$(this).hasClass('active'))
    {
        $('#replyMessager').attr('id_user',"");
        var id_user = $(this).attr('id_user');
        var name = $(this).find('.name-profile').html();
        $('.chat-area-reply').removeClass('hide');
        $('.close_file').trigger('click');
        var id_message = $(this).attr('id_senders');
        var src = $(this).find('img').prop('src');
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
            $.get(admin_url+'messager/getJson_message', {id: id_message}, function (data) {
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
        var img = '<img src="https://graph.facebook.com/'+id_user+'/picture?height=100&width=100&access_token='+$.cookie('access_token_page_active')+'">';
        $('#img-customer').html(img);
        varInfoUser(id_user); // lấy thông tin khashc hàng hàng tiềm năng
        $('#search_customer').val('');
    }
})

//scroll nội dung chat
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

//thêm tin nhắn vào khi gửi chính mình
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

//thêm tin nhắn vào khi gửi của khách hàng
function addClient_Send(replyMessager, id_client = "", date, message_id = "", err = false, type = 'last', id_message)
{
    if($('#tab_'+id_message).find('.chat-area-content-profile').find('.client-message[id="'+message_id+'"]').length > 0)
    {
        return;
    }
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

//Các function update file

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
    $.post(admin_url+"messegar/deleteFile",{url:url},function(data){

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
        $.post('https://graph.facebook.com/'+VersionAppFB+'/me/messages',
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
//End function update file



//Sắp xếp và tạo list khách hàng mới
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
//END Sắp xếp và tạo list khách hàng mới


//Thay đổi fanpage facebook

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

//log out facebook
function LogoutFB(){
    $.cookie("page_active", '', { expires: -1, path: '/' });
    $.cookie("access_token_page_active", '', { expires: -1, path: '/' });
    $.cookie("name_page_active", '', { expires: -1, path: '/' });
    $.cookie("access_token_page", '', { expires: -1, path: '/' });
    $.cookie("user_token", '', { expires: -1, path: '/' });
    FB.getLoginStatus(function(response) {
        FB.logout(function (res) {
            location.reload();
        });
    })
}

//nhận và xữ lý Pusher
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
                                    var id_data_response = response_messager.data[i].id.split('m_');
                                    if(id_data_response.length > 1)
                                    {
                                        id_data_response = id_data_response[1];
                                    }
                                    else {
                                        id_data_response = response_messager.data[i].id;
                                    }
                                    console.log(id_data_response)
                                    addClient_Send(response_messager.data[i].message, data.message.recipient.id, date, id_data_response, true, 'last', id_senders);
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

$('body').on('click', '.createTag', function(e){
    var tname = $(this).attr('title');
    $('.tagstype').tagit('createTag', $.trim(tname));
    $('.tagstype').trigger('change');
})

function copy_content(string) {
    var textArea = document.createElement("textarea");
    textArea.value = string;
    document.body.appendChild(textArea);
    // textArea.focus();
    textArea.select();
    document.execCommand("copy");
    document.body.removeChild(textArea);
    alert_float('info',alert_success_copy);
}

function convert_lead_to_customerFB(e) {
    var t = $("#lead-modal");
        t.find(".data").html(""), requestGet("messager/get_convert_dataFB/" + e).done(function(e) {
            $("#lead_convert_to_customer").html(e), $("#convert_lead_to_client_modal").modal({
                show: !0,
                backdrop: "static",
                keyboard: !1
            })
        }).fail(function(e) {
            alert_float("danger", e.responseText)
        }).always(function() {
            t.off("hidden.bs.modal.convert")
        })
}

$('body').on('click', '.war_client', function(e){
    var form = $(this).parents('form');
    var id = form.find('#id').val();
    if($.isNumeric(id))
    {
        var data = {id:id};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'messager/WarClient', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                varInfoUser(data.id_facebook);
            }
            alert_float(data.alert_type, data.message);
        })
    }
})

$('body').on('click', '.war_lead', function(e){
    var form = $(this).parents('form');
    var id = form.find('#id').val();
    if($.isNumeric(id))
    {
        var data = {id:id};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'messager/WarLead', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                varInfoUser(data.id_facebook);
            }
            alert_float(data.alert_type, data.message);
        })
    }
})

//search data client
$('body').on('click', '.TSearch', function(e){
    var LiActive = $(this);
    var id_data = LiActive.attr('id-data');
    if(id_data != 'tag')
    {
        $('.TSearch').parent('li').removeClass('active');
        LiActive.parent('li').addClass('active');
        SearchLeft();
    }
})

$('body').on('click', '.DSearchTag', function(e){
    var TagActive = $(this);
    var id = TagActive.attr('id-data');
    $('.TSearch').parent('li').removeClass('active');
    TagActive.parents('li').parents('li').addClass('active');
    // $('.content-profile').removeClass('hide');
    SearchLeft();
    return;
})

//search profile chat
$('body').on('keyup', '#search_Chat', function(e){
    SearchLeft();
    return;
})

$('body').on('click', '.TLeftSearch', function(e){
    if($(this).hasClass('active'))
    {
        $(this).removeClass('active');
    }
    else
    {
        $(this).addClass('active');
    }
    if($(this).hasClass('TAssigned'))
    {
        if($('#assignedSearch').val() != "")
        {
            $(this).addClass('active');
            $(this).find('.DeleteSreach').removeClass('hide');
        }
        else
        {
            $(this).removeClass('active');
            $(this).find('.DeleteSreach').addClass('hide');
        }
    }
    SearchLeft();
})

$('body').on('click', '.ItemSearchAssigned', function(e){
    var assignedSearch = $('#assignedSearch').val();
    if($(this).hasClass('active'))
    {
        var id = $(this).attr('id-data');
        $(this).removeClass('active');
        if($.isNumeric(id))
        {
            var assignedArray = assignedSearch.split(',');

            var assignedArrayNew = [];
            $.each(assignedArray, function(i, v){
                if(id != v)
                {
                    assignedArrayNew.push(v);
                }
            })
            assignedSearch = assignedArrayNew.join(',');
            $('#assignedSearch').val(assignedSearch);
        }
    }
    else
    {
        $(this).addClass('active');
        var id = $(this).attr('id-data');
        if(assignedSearch == "")
        {
           var assignedArray = [];
        }
        else
        {
            var assignedArray = assignedSearch.split(',');
        }

        assignedArray.push(id);
        assignedSearch = assignedArray.join(',');
        $('#assignedSearch').val(assignedSearch);

    }
    var html_popver = $(this).parents('.popover-content').html();
    console.log(html_popver)
    $('.TLeftSearch.TAssigned').find('a[data-toggle="popover"]').attr('data-content', html_popver);
    SearchLeft();
    return;
})

var ObjectHtmlAssignedDefaule = $('.TLeftSearch.TAssigned').find('a[data-toggle="popover"]').attr('data-content');

$('body').on('click', '.DeleteSreach', function(e){
    var LeftSearch = $(this).parents('.TLeftSearch');
    LeftSearch.find('a[data-toggle="popover"]').attr('data-content', ObjectHtmlAssignedDefaule);
    $('#assignedSearch').val('');
    $('.TLeftSearch.TAssigned').trigger('click');

})

function CreateOrders(id = "", type = "")
{
    if(id != "" && type != "")
    {
        var data = {id : id ,type : type};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'messager/ViewCreateOrder', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('#modalOne').html(data.data);
            }
            else
            {
                alert_float(data.alert_type, data.message);
            }
        })
    }

}

function SearchLeft()
{
    var content_profile = $('.content-profile');
    $('.content-profile').removeClass('hide');
    var ValChat = $('#search_Chat').val().toLowerCase();
    if(ValChat != "")
    {
        $.each(content_profile, function(i, v){
            var content = $(v).find('.name-profile').text().toLowerCase();
            if(content.search(ValChat) >= 0)
            {
                $(v).removeClass('hide');
            }
            else
            {
                $(v).addClass('hide');
            }
        })
    }
    var TagActive = $('li.active .DSearchTag');
    if(TagActive.length > 0)
    {
        var id = TagActive.attr('id-data');
        if($.isNumeric(id))
        {
            $.each(content_profile, function(i, v){
                if($(v).find('#tag-lef-'+id).length == 0){
                    $(v).addClass('hide');
                }
            })
        }
        else
        {
            $.each(content_profile, function(i, v){
                if($(v).find('.tag_left').find('span').length > 0){
                    $(v).addClass('hide');
                }
            })
        }
    }

    var LiActive = $('.TSearch.active a');
    if(LiActive.length > 0)
    {
        var id_data = LiActive.attr('id-data');
        if(id_data == 'comment')
        {
            $('.content-profile').addClass('hide');
        }
        else if(id_data == 'not_see')
        {
            var content_profile = $('.content-profile');
            $.each(content_profile, function(i, v){
                if($(v).find('.count-inbox').text() == ''){
                    $(v).addClass('hide');
                }
            })
        }
    }

    var LeftSearch = $('.TLeftSearch.active a');
    $.each(LeftSearch, function(i, v){
        var id_data = $(v).attr('id-data');
        if(id_data == 'phone')
        {
            $('.content-profile[phone=""]').addClass('hide');
        }
        else if(id_data == 'not_phone')
        {
            $('.content-profile[phone!=""]').addClass('hide');
        }
        else if(id_data == 'orders')
        {
            $('.content-profile[orders=""]').addClass('hide');
        }
    })


    var assignedSearch = $('#assignedSearch').val();
    if(assignedSearch != "")
    {
        assignedSearch = assignedSearch.split(',');
        var content_profile = $('.content-profile[assigned != ""]');
        $.each(content_profile, function(i, v) {
            var assigned = $(v).attr('assigned').split(',');
            var success = false;
            $.each(assignedSearch, function(ia,va){
                $.each(assigned, function(ii, vv){
                    if(va == vv)
                    {
                        success = true;
                        console.log(va+'-'+vv)
                        return false;
                    }
                })
                if(success == true)
                {
                    return false;
                }
            })
            if(success == false)
            {
                $(v).addClass('hide');
            }

        })
        if(assignedSearch.length > 0)
        {
            $('.content-profile[assigned=""]').addClass('hide');
        }

    }
}

function manageSubmitFB(form) {
    var button = $(form).find('button[type="submit"]');
    button.button({loadingText: please_wait});
    button.button('loading');
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        console.log(response);
        response = JSON.parse(response);
        if (response.success == true) {
            alert_float('success', response.message);
            varInfoUser(response.id_facebook);
            $(form).parents('.modal').modal('hide');
        }
    }).always(function() {
        button.button('reset')
    });
    return false;
}




