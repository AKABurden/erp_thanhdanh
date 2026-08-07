<?php

defined('BASEPATH') or exit('No direct script access allowed');

$color    = pr_get_chat_color(get_staff_user_id(),'chat_color');

$getColor = !empty($color) ? $color : '#343a40';

?>
<div id="pusherChat">
  <div id="mainChatId" class="draggable" style="display:none;">
    <div id="membersContent">
      <div class="chatMain">
        <div class="topInfo" onclick="slideChat(this)" style="background:<?php echo $getColor; ?>;">
          <p class="cname">
            <?php echo get_option('companyname'); ?>
          </p>
        </div>
      </div>
      <div class="scroll">
        <div id="members-list"></div>
        <input class="form-control searchBox inputHidden" placeholder="<?php echo _l('chat_search_chat_members'); ?>" />
      </div>
      <div class="chat-footer" style="background:<?php echo $getColor; ?>">
        <div class="online" onclick="slideChat(this)">
          <?php echo _l('chat_online_users'); ?>
          <span id="count">0</span>
        </div>
        <i class="fa fa-volume-up" aria-hidden="true" id="disableSound"></i>
        <i class="fa fa-search" id="searchUsers" aria-hidden="true"></i>
        <i class="fa fa-paint-brush" id="colorChanger" aria-hidden="true"></i>
        <div class="form-inline colorHolder">
          <form method="post" style="display: none" action="<?php echo site_url('prchat/prchat_controller/colorchange/'); ?>" onsubmit="changeColor(this); return false;">
            <input type="text" name="color" class="form-control jscolor float-right chat_color" value="<?php echo $getColor; ?>" required placeholder="<?php echo _l('chat_example_type'); ?>" />
            <button class="btn btn-success btn-sm" id="chColor" type="submit">
              <?php echo _l('chat_change_color'); ?>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>  
    // Positions of chat and main chat append on browser when load
    var positions = JSON.parse(localStorage.positions || "{}");
    $.each(positions, function(id, pos) {
      $("#pusherChat #" + id).css(pos);
    });
    delay(function(){
      $('#mainChatId').css('display', 'block');
    }, 200);
  </script>
  <!-- Chat Box Template -->
  <div id="templateChatBox">
    <div class="pusherChatBox">
      <span class="state">
        <span class="userIsTyping"><img src="<?php echo module_dir_url('prchat', 'assets/chat_implements/userIsTyping.gif'); ?>"/></span>
        <span class="quote">
         <div class="notification-box">
          <span class="notification-count">0</span>
          <div class="notification-bell">
            <span class="bell-top"></span>
            <span class="bell-middle"></span>
            <span class="bell-bottom"></span>
            <span class="bell-rad"></span>
          </div>
        </div>
      </span>
    </span>
    <span class="closeBox">
     <i class="fa fa-close"></i>
   </span>
   <chatHead class="chat-head" style="background:<?php echo $getColor; ?>" onclick="slideChat(this)">
    <span class="userName"></span>
  </chatHead>
  <div class="slider">
    <div class="logMsg">
      <svg class="message_loader" viewBox="0 0 50 50">
        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
      </svg>
      <div class="msgTxt">
      </div>
    </div>
    <div class="fileUpload">
      <i class="fa fa-paperclip" aria-hidden="true"></i>
    </div>
    <form hidden enctype="multipart/form-data" name="fileForm" method="post" onsubmit="postform(this);return false;">
      <input type="file" class="file" name="userfile" required />
      <input type="submit" name="submit" class="save" value="save" />
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    </form>
    <form method="post" enctype="multipart/form-data" name="pusherMessagesForm" onsubmit="return false;">
      <div class="enterBtn">
        <i class="fa fa-paper-plane" aria-hidden="true"></i>
      </div>
      <textarea name="msg" class="chatbox" rows="3" placeholder="<?php echo _l('chat_type_a_message'); ?>"></textarea>
      <input type="hidden" name="from" class="from" />
      <input type="hidden" name="to" class="to" />
      <input type="hidden" name="typing" class="typing" value="false" />
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    </form>
  </div>
</div>
</div>
<!-- Chat Box Template End -->
<div class="chatBoxWrap">
  <div class="chatBoxslide"></div>
  <span id="slideLeft"><i class="fa fa-angle-double-left" aria-hidden="true"></i></span>
  <span id="slideRight"><i class="fa fa-angle-double-right" aria-hidden="true"></i></span>
</div>
</div>


<style type="text/css" media="screen">
  #pusherChat #membersContent a:hover { background: <?php echo $getColor; ?>; }
  #pusherChat .pusherChatBox .msgTxt p.you { background: <?php echo $getColor; ?>; }
  #pusherChat .chatBoxWrap #slideRight .fa-angle-double-right { color: <?php echo $getColor; ?> }
  #pusherChat .chatBoxWrap #slideLeft .fa-angle-double-left { color: <?php echo $getColor; ?> }
</style>
<!-- Chat Template End -->

<script>
 /*---------------* Start of main Chat helper function  *---------------*/    
 var prchatSettings =  
 {
  'usersList' : '<?php echo site_url('prchat/prchat_controller/users'); ?>',
  'getMessages' : '<?php echo site_url('prchat/prchat_controller/getMessages'); ?>',
  'updateUnread' : '<?php echo site_url('prchat/prchat_controller/updateUnread'); ?>',
  'serverPath' : '<?php echo site_url('prchat/prchat_controller/initiateChat'); ?>',
  'chatLastSeenText' : "<?php echo _l('chat_last_seen'); ?>",
  'getChatColor' : "<?php echo pr_get_chat_color(get_staff_user_id(),'chat_color'); ?>",
  'noMoreMessagesText' : "<?php echo _l('chat_no_more_messages_to_show'); ?>",
  'deleteMessage' : "<?php echo site_url('prchat/prchat_controller/deleteMessage'); ?>",
  'messageIsDeleted' : "<?php echo _l('chat_message_deleted'); ?>",
  'unreadMessages' : <?php echo $unreadMessages ? json_encode($unreadMessages) : 'null'; ?>,
  'debug':<?php if(ENVIRONMENT != 'production') { ?> true <?php } else { ?> false <?php } ; ?>,
};

    // important do not remove
    $.ajaxSetup({
      data: {
        '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'
      }
    });
    // Parse emojies in chat area do not touch
    emojify.setConfig({emojify_tag_type:'div','img_dir':site_url+'/modules/prchat/assets/chat_implements/emojis'});
    emojify.run();

    var getCurrentBackgound = '';
    var prevBackground      = "<?php echo $getColor; ?>";
    var pageTitle           = $('title').html(); 
    var pusherKey           = "<?php echo get_option('pusher_app_key') ?>";
    var appCluster          = "<?php echo get_option('pusher_cluster') ?>";
    var staffFullName       = "<?php echo get_staff_full_name(); ?>";
    var userSessionId       = "<?php echo get_staff_user_id(); ?>";

    $('#pusherChat').on('click','.fileUpload',function(){
      $(this).parents('.pusherChatBox').find('form input:first').trigger('click');
    }); 

    $('#pusherChat').on('change','input[type=file]',function(){
      var id = $(this).attr('name');
      $('form#'+id).submit();
    }); 

    function postform(file){
      var formData = new FormData();
      var fileForm = $(file).children('input[type=file]')[0].files[0]; 
      var sentTo = $(file).attr('id');
      var token_name = $(file).children('input:nth-child(3)').val();
      var formId = $(file).attr('id');

      formData.append('userfile',fileForm);
      formData.append('send_to',sentTo);
      formData.append('send_from',userSessionId);
      formData.append('csrf_token_name',token_name);

      $.ajax({
        type: 'POST',
        url: '<?php echo site_url('prchat/prchat_controller/uploadMethod'); ?>',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(r){

          if(r.error) {
            alert_float('danger',r.error);
            return;
          }

          const uploadSend = $.Event( "keypress", {  which:13 } );
          var basePath = "<?php echo module_dir_url('prchat', 'uploads/'); ?>";

          $('form#'+formId).trigger("reset");

          $('#pusherChat .pusherChatBox#'+formId+' textarea').val(basePath+r.upload_data.file_name);

          if($('#pusherChat .pusherChatBox#'+formId+' textarea').trigger(uploadSend)){
           alert_float('info', 'File '+r.upload_data.file_name+' sent.');
         }

         var messagesContainer = $('#pusherChat .pusherChatBox#'+formId+' .logMsg');
         messagesContainer.animate({ scrollTop: messagesContainer.prop("scrollHeight")}, 1000);

       }
     });
    }

    $('#pusherChat').on('click','#disableSound',function(){
      if(isSoundMuted == ''){
       isSoundMuted = 'muted';
       $(this).toggleClass("fa fa-volume-up fa fa-volume-off");
     } else if(isSoundMuted == 'muted'){
       $(this).toggleClass("fa fa-volume-off fa fa-volume-up");
       isSoundMuted = '';
     }
   });

    $('#pusherChat').on('click','.enterBtn',function(){
      const eventEnter = $.Event( "keypress", {  which:13 } );
      $(this).parents('.pusherChatBox').find('textarea').trigger(eventEnter);
    });

    if (prchatSettings.debug)
    {
      try {
       Pusher.log = function(message) {
        if (window.console && window.console.log) {
          window.console.log(message);
        }
      };
    } catch (e) {
      if (e instanceof ReferenceError) {
        alert_float('danger',e);
      }
    }
  }
  var pusher = new Pusher(pusherKey, { 
    authEndpoint: "<?php echo site_url('prchat/prchat_controller/pusher_auth'); ?>",
    authTransport: 'jsonp',
    'cluster':appCluster,
  });

  /*---------------* Pusher Trigger accessing channel *---------------*/     
  var presenceChannel = pusher.subscribe('presence-mychanel');

  /*---------------* Pusher Trigger subscription succeeded *---------------*/      
  presenceChannel.bind('pusher:subscription_succeeded', function(members){ 
    chatMemberUpdate(true); 
  });

  /*---------------* Pusher Trigger user connected *---------------*/      
  presenceChannel.bind('pusher:member_added', function(members) { 
    chatMemberUpdate(); 
    addChatMember(members); 
  });

  /*---------------* Pusher Trigger user logout *---------------*/      
  presenceChannel.bind('pusher:member_removed', function(members) { 
    removeChatMember(members); 
  });


  /*---------------* Bind the 'send-event' & update the chat box message log *---------------*/      
  presenceChannel.bind('send-event', function(data) { 
    var obj = $("a[href=\\#"+data.from+"]");
    if(presenceChannel.members.me.id == data.to && data.from != presenceChannel.members.me.id){
      if(presenceChannel.members.me.id != data.from){
       if(!$('.pusherChatBox.on#id_'+data.from).is(':visible')){
        playPushSound();
      }
    }
    if($('.pusherChatBox.on#id_'+data.from).hasClass('stillActive')){
      $('.pusherChatBox#id_'+data.from).css('display','block');
      if($('.pusherChatBox#id_'+data.from).hasClass('on') && $('.pusherChatBox#id_'+data.from).find('.slider').is(':hidden')){
        playChatSound(getSound2.src);
        stopSound();
      }
      updateBoxPosition(obj);
    }
    data.message = createTextLinks_(emojify.replace(data.message));
    var pusherFrom = $('#pusherChat .pusherChatBox#id_'+data.from);
    var pusherDataLogMsg = $('#pusherChat .pusherChatBox#id_'+data.from+' .logMsg');
    var name = pusherFrom.find('chatHead').find('.userName').html();
    if(pusherFrom.hasClass('hanging')) {
      pusherFrom.find('.chat-head').click();
    }
    $('#pusherChat .pusherChatBox#id_'+data.from+' .state').show();
    pusherFrom.addClass('stillActive');
    pusherFrom.addClass('receiveMsg').removeClass('writing');
    pusherDataLogMsg.find('.msgTxt').show();
    $('#pusherChat .pusherChatBox#id_'+data.from+' .msgTxt').append('<div class="conversation_from"><img class="friendProfilePic" src="'+fetchUserAvatar(data.from,data.sender_image)+'"/><small class="chatFriendUsername">'+name+'</small></br><p class="friend">'+ data.message+'</p></div>');   
    $('title').html('');
    if ($('title').text().search('sent you a message') == -1){
      $('title').prepend(name+' sent you a message');
      if($('.pusherChatBox#id_'+data.from).is(':hidden')){
        playPushSound(); 
      }
    }
    createChatBox(obj);
    pusherDataLogMsg.scrollTop(pusherDataLogMsg[0].scrollHeight); 
  }  
  if (presenceChannel.members.me.id == data.from){
   data.message = createTextLinks_(emojify.replace(data.message));
   $('#pusherChat .pusherChatBox#id_'+data.to+' .msgTxt').append('<div class="conversation_me"><img class="myProfilePic" src="'+fetchUserAvatar(userSessionId,data.sender_image)+'"/><small class="chatUsername">'+staffFullName+'</small></br><div class="message_container" id="'+data.last_insert_id+'"><span class="chat_options show_delete"><span class="confirm_delete fa fa-trash-o"></span></span><p class="you" id="'+data.id+'" style="background:'+getCurrentBackgound+'">'+ data.message+'</p></div></div>');
   var pusherDatalogMsgTo = $('#pusherChat .pusherChatBox#id_'+data.to+' .logMsg');
   pusherDatalogMsgTo.scrollTop(pusherDatalogMsgTo[0].scrollHeight);
 }
});


  /*---------------* Detect when a user is typing a message *---------------*/   
  presenceChannel.bind('typing-event', function(data) {
    if(presenceChannel.members.me.id == data.to && data.from != presenceChannel.members.me.id && data.message == 'true'){
      $('#id_'+data.from).find('span.userIsTyping img').show();
      $('#id_'+data.from).addClass('writing');
    }
    else if(presenceChannel.members.me.id == data.to && data.from != presenceChannel.members.me.id && data.message == 'null'){
      $('#id_'+data.to).find('span.userIsTyping img').fadeOut();
      $('#id_'+data.to).removeClass('writing');
    }
  });

  /*---------------* Trigger notification popup increment*---------------*/   
  presenceChannel.bind('notify-event', function(data) {
    var chatBox = $('.pusherChatBox.on#id_'+data.from).find('.chatbox');
    var notiBox = $('.pusherChatBox.on#id_'+data.from).find('.notification-box');
    var notiCount = $('.pusherChatBox.on#id_'+data.from).find('.notification-count');
    if(!chatBox.is(':focus')){
      var notiValue = parseInt(notiCount.html());
      if(notiBox.is(':hidden')){
        $(notiBox.show());
      }
      $(notiCount.html(notiValue = notiValue+1));
    } else {
      $(notiBox).hide();
    }
  });

  /*---------------* Trigger when user stop typing *---------------*/        
  $("#pusherChat").on("focusout",".chatbox",function(){
    var from = $(this).parents('form');
    if($(this).next().next().next().val() == 'true'){
      $.post(prchatSettings.serverPath, from.serialize());
      $(this).next().next().next().val('null');
    } 
  });

  /*---------------* Slide up & down users list & chat boxes, update messages *---------------*/ 
  $('#pusherChat').on( "click", ".pusherChatBox chathead", function( event ) {
    var obj = $(this);
    var id = obj.parent().attr('id'); 
    var selector = $('#pusherChat .pusherChatBox#'+id+' .slider');
    if($(obj).hasClass('hanging')){
     $(selector).find('.fileUpload').animate({ height: [ "toggle", "swing" ],opacity: "toggle"});
     $(selector).find('.enterBtn').animate({ height: [ "toggle", "swing" ],opacity: "toggle"});
   }
   var notiBox = $('.pusherChatBox#'+id).find('.notification-box');
   var chatBox = obj.parents('.pusherChatBox');
   var sideLinkConnId = obj.parents('.pusherChatBox').attr('id').replace('id_','');
   var sideLink = $('#membersContent #members-list a#'+sideLinkConnId);
   $('#pusherChat .pusherChatBox#'+id+' .logMsg').scrollTop($('#pusherChat .pusherChatBox#'+id+' .logMsg')[0].scrollHeight);
   updateUnreadMessages(this,chatBox);
   notiBox.hide();
   sideLink.removeClass('animated flash');
 });

  /*---------------* Close chatbox, update messages *---------------*/        
  $('#pusherChat').on( "click", ".closeBox", function( event ) {
    soundFinished = false;
    var id = $(this).parents('.pusherChatBox').attr('id');
    var updateId  = $(this).parents('.pusherChatBox').attr('id').replace("id_", "");
    removeActiveChatWindow(updateId);
    var chatBox = $(this).parents('.pusherChatBox#'+id);
    var selector = $('#pusherChat .pusherChatBox#'+id+' .slider');
    $(selector).find('.fileUpload').css("display","block");
    $(selector).find('.enterBtn').css("display","block");
    updateUnreadMessages(this);
    $(this).parents('.pusherChatBox#'+id).hide();
    $(this).parents('.pusherChatBox.on#'+id).addClass('stillActive');
    $(chatBox).find('.slider').addClass('animated fadeIn').show();
    $(chatBox).find('.notification-count').text('0');
    updateBoxPosition();
    return false;
  });

  /*---------------* Trigger click on user & create chat box and check for messages *---------------*/        
  $('#pusherChat #members-list').on( "click", "a", function( event ) {

    $('#pusherChat .scroll').animate({ scrollTop: 0 });
    var obj = $(this);
    var id = obj.attr('id');

    addActiveChatWindow({
      id: id,
      fullName: obj.find('.user-name').text().trim()
    });

    var hasActiveWindowClickClass = $(this).hasClass('active-windows-click');
    createChatBox(obj);

    var chatBox = obj.parents('#pusherChat').find('.pusherChatBox#id_'+id);
    var notiBox = $(this).children('.unread-notifications').data('badge');
    if(!hasActiveWindowClickClass && notiBox > 0){
     updateUnreadMessages(this, chatBox);
   }

   stopSound();

   if($(chatBox).is(':visible') && !$(chatBox).hasClass('manually-added')){
    $(chatBox).find('.slider').addClass('animated fadeIn').show();
  } 

  $(chatBox).removeClass('manually-added')

  if($(chatBox).hasClass('on')){
   $('#pusherChat .pusherChatBox#id_'+id+' .logMsg').scrollTop($('#pusherChat .pusherChatBox#id_'+id+' .logMsg')[0].scrollHeight);
 }
});


  $('#slideLeft').on('click',function(){
    $('.chatBoxslide .pusherChatBox:visible:first').addClass('overFlowHide');
    $('.chatBoxslide .pusherChatBox.overFlow').removeClass('overFlow');
    updateBoxPosition();
  });

  $('#slideRight').on('click',function(){
    $('.chatBoxslide .pusherChatBox.overFlowHide:last').removeClass('overFlowHide');
    updateBoxPosition();
  });

  /*--------------------  * send message & typing event to server  * ------------------- */ 
  $("#pusherChat").on('keypress','.pusherChatBox textarea',function(e) {    
    var form = $(this).parents('form');
    var chatId = $(form).parents().parent('.pusherChatBox').attr('id');
    if ( e.which == 13 ) {
     var message = $(this).val();
     if(message.trim() == '') {
      return false;
    }
    var msgTxt = $('.logMsg').find('.msgTxt');
    if(!$(msgTxt).is(':visible'))
    {
     $('.logMsg').find('.msgTxt').show();
   }
   $('#pusherChat #'+chatId+' .logMsg').scrollTop($('#pusherChat #'+chatId+' .logMsg')[0].scrollHeight); // just in case
   $(this).next().next().next().val('false');
// Send event
$.post(prchatSettings.serverPath, form.serialize());
e.preventDefault();
$(this).val('');
$(this).focus();
} 
else if (!$(this).val() || ($(this).next().next().next().val() == 'null' && $(this).val()))
{
 // Typing event
 $(this).next().next().next().val('true');
 $.post(prchatSettings.serverPath, form.serialize());
}
});     

  /*-----------------------    * additional dynamic styling  *-----------------------*/             
  $('#pusherChat .chatBoxWrap').css({
    'width':$(window).width() -  $('#membersContent').width()-30 
  });     

  $(window).resize(function(){
    $('#pusherChat .chatBoxWrap').css({
      'width':$(window).width() - $('#membersContent').width() -30
    }); 
    updateBoxPosition();
  });

  /*---------------* Additional checks for chatbox and unread message update control *---------------*/
  $('#pusherChat').on( "click", ".msgTxt, chatHead, textarea", function() {
    updateUnreadMessages(this);
  });        

  $('#pusherChat').on( "click", ".pusherChatBox", function() {
    var linkId = $(this).attr('id').replace('id_','');
    var checkNoti = $('#membersContent a#'+linkId).find('.unread-notifications');
    var notiText = $($(this)).find('.notification-box');
    if(notiText.text() > 0){
      notiText.children('.notification-count').text('0');
    }
    if(checkNoti){
      checkNoti.remove();
    }
    var newMessage = false;
    $(this).removeClass('receiveMsg');
    $('.pusherChatBox').each(function(){
      if ($(this).hasClass('receiveMsg')){
        newMessage = true;
        return false; 
      } 
    });  
    if (newMessage == false)
      $('title').text(pageTitle);
  });

  /*---------------* prevent showing dots if user is not typing *---------------*/      
  $("#pusherChat").on("focus",".chatbox",function(){ 
    $('.pusherChatBox.on.writing').find('span.userIsTyping img').fadeOut().removeClass('writing receiveMsg');
  });

  /*---------------* Search users *---------------*/        
  $(".searchBox").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#members-list a").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  /*---------------* On click show input search field and focus *---------------*/        
  $('#searchUsers').click(function(){
    if($('.searchBox').hasClass('inputHidden')){ 
      $('.searchBox').removeClass('inputHidden').css('display','block');
      $('.searchBox').focus();
    }
  });

  /*---------------* On focus out clear out input field and show all users if not found in searchbox *---------------*/       

  $('#membersContent').keyup('.searchBox',function(e){
    if (e.keyCode === 27) {
      clearSearchValues();
    }
  }); 
  $('#membersContent').focusout('.searchBox',function(){
    clearSearchValues();
  });

  /*---------------* Change Boxes, Chat color update in database and dynamically set color *---------------*/       
  $(document).on('click','#colorChanger',function(){
    $('#membersContent').find('form').toggle();
  });

  function slideChat(chatHead) {
    if ($(chatHead).hasClass('topInfo') || $(chatHead).hasClass('online')) {

     if(!$('#mainChatId').hasClass('main-chat-dragging')){
      localStorage.chat_head_position = 'none';
      $(chatHead).parents('#membersContent').find('.scroll').slideToggle('fast');

      if($(chatHead).hasClass('online')) {
        var scroll = $('#membersContent .scroll');
        if($(scroll).is(':visible')){
          localStorage.chat_head_position = 'block';
        } else {
          localStorage.chat_head_position = 'none';
        }
      }
    } else {
      $('#mainChatId').removeClass('main-chat-dragging');
    }

  } else {
    if (prevBackground != getCurrentBackgound) {
      $(chatHead).parents('.pusherChatBox').find('p.you').attr('style', 'background: '+getCurrentBackgound+' !important');
    }
    $(chatHead).next().slideToggle('fast');
    var box = $(chatHead).parents('.pusherChatBox');
    if(box.hasClass('hanging')){
      var id = box.attr('id').replace('id_','');
      $('#members-list').find('a#'+id).click();
    }
  }
}

$(document).keyup(function(e) {
  if (e.keyCode == 27) {
    var $prChatChatboxes = $("body").find('.closeBox');
    $.each($prChatChatboxes, function() {
      if($(this).parents('.pusherChatBox').find('.chatbox').is(':focus')) {
        $(this).trigger('click');
      }
    });
  }
});

<?php 
$option = get_option('chat_staff_can_delete_messages');
if($option == '1' || is_admin()) :  ?>
  $(function(){
    $('#pusherChat').on('mouseenter','p.you',function() {
     if($(this).text() !== prchatSettings.messageIsDeleted) {
      $(this).parents().children('.show_delete').css({"display":"inline-block"}).show('fast');
    }
  }).on('mouseleave','p.you',function() {
    var _this = $(this);
    setTimeout(function () {
      if ($(".show_delete").is(':visible')) {
       if(_this.text() !== prchatSettings.messageIsDeleted) {
        _this.parents().children('.show_delete').css({"display":"none"}).hide('fast');
      }
    }
  }, 1000);
  });

  $('#pusherChat').on('click','.chat_options',function(){
    var msg_id = $(this).parent().attr('id');
    var parent = $(this).parent();
    $.post(prchatSettings.deleteMessage,{id:msg_id}).done(function(response){
     if(response == 'true'){
      $(parent).children("p").html('<small>' + prchatSettings.messageIsDeleted +'</small>');  
      $(parent).children('.show_delete').remove();
    }
  });
  });
});
<?php endif; ?>
</script>