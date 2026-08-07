var offsetPush = {};
var endOfScroll = {}

function removeActiveChatWindow(id) {
    var activeChatWindows = getActiveChatWindowsFromStorage();
    var indexToRemove = null;

    $.each(activeChatWindows, function(index, obj) {
        if (obj.id == id) {
            indexToRemove = index;
        }
    })
    if (indexToRemove !== null) {
        activeChatWindows.splice(indexToRemove, 1);
    }

    localStorage.activeChatWindows = JSON.stringify(activeChatWindows)
}

function addActiveChatWindow(obj) {

    if (typeof(localStorage.activeChatWindows) == 'undefined') {
        localStorage.activeChatWindows = '';
    }

    if (isChatBoxInLocalStorageActiveChats(obj.id)) {
        return false;
    }

    var currentActiveChatWindows = getActiveChatWindowsFromStorage();

    currentActiveChatWindows.push(obj)

    localStorage.activeChatWindows = JSON.stringify(currentActiveChatWindows);
}

function getActiveChatWindowsFromStorage() {
    if (typeof(localStorage.activeChatWindows) == 'undefined') {
        return [];
    }

    var activeChatWindows = localStorage.activeChatWindows;

    if (activeChatWindows == '') {
        return [];
    }

    return JSON.parse(activeChatWindows);
}

function isChatBoxInLocalStorageActiveChats(id) {

    var retVal = false;
    $.each(getActiveChatWindowsFromStorage(), function(index, obj) {
        if (obj.id == id) {
            retVal = true;
        }
    })

    return retVal;
}

function createTextLinks_(text) {
    var regex = (/\.(gif|jpg|jpeg|tiff|png|swf)$/i);
    return (text || "").replace(/([^\S]|^)(((https?\:\/\/)|(www\.))(\S+))/gi, function(match, string, url) {
        var hyperlink = url;
        if (!hyperlink.match('^https?:\/\/')) {
            hyperlink = '//' + hyperlink;
        }
        if (hyperlink.match(regex)) {
            return string + '<a href="' + hyperlink + '" target="blank"><img style="width:100%;height:100%;padding-top:2px;" rel="nofollow" src="' + hyperlink + '"/></a>';
        } else {
            return string + '<a target="blank" rel="nofollow" href="' + hyperlink + '">' + url + '</a>';
        }
    });
}

function clearSearchValues() {
    $('.searchBox').slideUp(200, function() {
        $('.searchBox').addClass('inputHidden');
        $('.searchBox').val('');
        $("#members-list a").filter(function() {
            $(this).css('display', 'block');
        });
    });
}

function changeColor(that) {
    var url = $(that).attr('action');
    var id = userSessionId;
    var color = $(that).find('input[name=color]').val();
    getCurrentBackgound = color;
    $.post(url, {
        id: id,
        color: color
    }).done(function(r) {
        if (r.success != 'false') {
            $('#pusherChat #membersContent .topInfo').css('background', color);
            $('#pusherChat chatHead').css('background', color);
            $('#pusherChat .chat-footer').css('background', color);
            $('#pusherChat .msgTxt p.you').css('background', color);
            changeHoverColor(color);
            prchatSettings.getChatColor = color;
        }
      return false;
  });
}

function changeHoverColor(color) {
    $("#members-list a").filter(function() {
        $(this).hover(
            function() {
                $(this).css('background', color);
            },
            function() {
                $(this).css('background', '');
            });
    });
}

/*---------------* updating unread messages trigger and notification trigger *---------------*/
function updateUnreadMessages(chatBox, pusherChatBox) {
    if (pusherChatBox) {
        var linkId = pusherChatBox.attr('id').replace("id_", "");
        pusherChatBox.find('.notification-count').text('0');
        $('#membersContent a#' + linkId).find('.unread-notifications').remove();
        $('#membersContent a#' + linkId).removeClass('animated flash');
        updateLatestMessages(linkId);
        return false;
    }
    if (chatBox) {
        var id = $(chatBox).parents('.pusherChatBox').attr('id').replace("id_", "");
        if (id) {
            var notiVal = $(chatBox).parents('.pusherChatBox').find('.notification-count').text();
            if (notiVal > 0) {
                updateLatestMessages(id);
                $('#membersContent a#' + id).removeClass('animated flash');
                $('.pusherChatBox#id_' + id).find('.notification-count').text('0');
            }
        }
    }
}

/*---------------*  Function removeChatMember and addChatMember must remain untouched and not moved to another place ! *---------------*/
var pendingRemoves = [];

function addChatMember(members) {
    var pendingRemoveTimeout = pendingRemoves[members.id];
    $('a#' + members.id).addClass('on').removeClass('off');
    $('.pusherChatBox#id_' + members.id).addClass('on').removeClass('off');
    if (pendingRemoveTimeout) {
        clearTimeout(pendingRemoveTimeout);
    }
}

function removeChatMember(members) {
    pendingRemoves[members.id] = setTimeout(function() {
        if (presenceChannel.members.count > 0) {
            $("#count").html(presenceChannel.members.count - 1);
        }
        $('a#' + members.id).removeClass('on').addClass('off');
        $('.pusherChatBox#id_' + members.id).addClass('off').removeClass('on').removeClass('stillActive');
        chatMemberUpdate();
    }, 5000);
}


/*-----------------------------* reorganize the chat box position on adding or removing users * -----------------------------*/
function updateBoxPosition() {
    var right = 0;
    var slideLeft = false;
    $('.chatBoxslide .pusherChatBox:visible').each(function() {
        $(this).css({
            'right': right
        });
        right += $(this).width() + 20;
        $('.chatBoxslide').css({
            'width': right
        });
        if ($(this).offset().left - 20 < 0) {
            $(this).addClass('overFlow');
            slideLeft = true;
        } else {
            $(this).removeClass('overFlow');
        }
    });
    if (slideLeft) {
        $('#slideLeft').show();
    } else {
        $('#slideLeft').hide();
    }
    if ($('.overFlowHide').html()) {
        $('#slideRight').show();
    } else {
        $('#slideRight').hide();
    }
}

function activateLoader(id, prepending) {
    var initLoader = $('.pusherChatBox .logMsg#' + id);
    initLoader.find('.message_loader').show(function() {
        initLoader.find('.message_loader').hide();
        if (prepending == true) {
            initiatePrepending();
        }
    });
}

/*---------------* Creating chat box from the html template to the DOM *---------------*/
function createChatBox(obj) {

    var id = obj.attr('href');
    var message = '';
    var fullName = obj.children('span').text();
    var getMsgId = id.replace("#", "");
    id = id.replace("#", "id_");
    var off = 'on';

    if (obj.hasClass('off')) {
        off = 'off';
    }

    var fromActiveChatWindowsClick = obj.hasClass('active-windows-click');
    var onlyLoadMessages = $('.pusherChatBox#' + id).hasClass('hanging');

    var dfd = $.Deferred();
    var promise = dfd.promise();

    if (!$('.pusherChatBox#' + id).html() || onlyLoadMessages) {

        $('.pusherChatBox#' + id).removeClass('hanging')

        if (!fromActiveChatWindowsClick) {
            $.get(prchatSettings.getMessages, {
                from: userSessionId,
                to: getMsgId,
                offset: 0
            }).done(function(r) {


                r = JSON.parse(r);
                message = r;

                if (typeof(offsetPush[getMsgId]) == 'undefined') {
                    offsetPush[getMsgId] = 0;
                }

                if (typeof(endOfScroll[getMsgId]) == 'undefined') {
                    endOfScroll[getMsgId] = 0;
                }

                offsetPush[getMsgId] += 10;
                dfd.resolve(message);
            });
        } else {
            dfd.resolve([]);
        }

        $('#templateChatBox .pusherChatBox chatHead .userName').html(fullName);

        promise.then(function(message) {

            $('.pusherChatBox#' + id + ' .logMsg .msgTxt').css('display', 'block');
            if (!$('.pusherChatBox#' + id + ' form:hidden').attr('id')) {
                $('.pusherChatBox#' + id + ' form:hidden').attr('id', id);
                $('.pusherChatBox#' + id + ' form:hidden input:first').attr('name', id);
                $('.pusherChatBox#' + id + ' .enterBtn').attr('id', id);
            }
            if (obj.hasClass('on')) {
                $(message).each(function(key, value) {
                    if (value.is_deleted == 1) {
                        value.message = '<small>' + prchatSettings.messageIsDeleted + '</small>';
                    } else {
                        value.message = emojify.replace(value.message);
                    }
                    if (value.reciever_id == userSessionId) {
                        $('.pusherChatBox#' + id + ' .logMsg .msgTxt').prepend('<div class="conversation_from"><img class="friendProfilePic" src="' + fetchUserAvatar(value.sender_id, value.user_image) + '"/><small class="chatFriendUsername">' + strCapitalize(value.sender_fullname) + '</small></br><p data-toggle="tooltip" title="' + value.time_sent_formatted + '" class="friend">' + value.message + '</p></div>');
                    } else {
                        $('.pusherChatBox#' + id + ' .logMsg .msgTxt').prepend('<div class="conversation_me"><img class="myProfilePic" src="' + fetchUserAvatar(userSessionId, value.user_image) + '"/><small class="chatUsername">' + staffFullName + '</small></br><div class="message_container" id="' + value.id + '"><span class="chat_options show_delete"><span class="confirm_delete fa fa-trash-o"></span></span><p data-toggle="tooltip"  id="' + value.id + '" title="' + value.time_sent_formatted + '" class="you">' + value.message + '</p></div></div>');
                    }
                });
                $('#pusherChat #' + id + ' .logMsg').scrollTop($('#pusherChat #' + id + ' .logMsg')[0].scrollHeight);
            } else if (obj.hasClass('off')) {
                $(message).each(function(key, value) {
                    if (value.is_deleted == 1) {
                        value.message = '<small>' + prchatSettings.messageIsDeleted + '</small>';
                    } else {
                        value.message = emojify.replace(value.message);
                    }

                    if (value.reciever_id == userSessionId) {
                        $('.pusherChatBox#' + id + ' .logMsg .msgTxt').prepend('<div class="conversation_from"><img class="friendProfilePic" src="' + fetchUserAvatar(value.sender_id, value.user_image) + '"/><small class="chatFriendUsername">' + strCapitalize(value.sender_fullname) + '</small></br><p data-toggle="tooltip" title="' + value.time_sent_formatted + '" class="friend">' + value.message + '</p></div>');
                    } else {
                        $('.pusherChatBox#' + id + ' .logMsg .msgTxt').prepend('<div class="conversation_me"><img class="myProfilePic" src="' + fetchUserAvatar(value.sender_id, value.user_image) + '"/><small class="chatUsername">' + staffFullName + '</small></br><div class="message_container" id="' + value.id + '"><span class="chat_options show_delete"><span class="confirm_delete fa fa-trash-o"></span></span><p data-toggle="tooltip"  id="' + value.id + '" title="' + value.time_sent_formatted + '" class="you">' + value.message + '</p></div></div>');
                    }
                });
            }
            $('#pusherChat #' + id + ' .logMsg').scrollTop($('#pusherChat #' + id + ' .logMsg')[0].scrollHeight);

        })

        if (!onlyLoadMessages) {
            var $cloned = $('#templateChatBox .pusherChatBox').clone().attr('id', id);
            if (fromActiveChatWindowsClick) {
                $cloned.find('.slider').css('display', 'none');
                $cloned.addClass('manually-added');
                $cloned.addClass('hanging');
                obj.removeClass('active-windows-click');
            }
            $('.chatBoxslide').prepend($cloned);
        }

        $('.pusherChatBox#' + id + ' .logMsg').attr('id', id);
        $('.pusherChatBox#' + id + ' .logMsg').attr('onscroll', 'loadMessages(this)');

        setTimeout(function() {
            // UI Experience
            activateLoader(id, false);
        }, 800);

        setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
            if (prevBackground != getCurrentBackgound) {
                $('.pusherChatBox#' + id + ' .msgTxt p.you').filter(function() {
                    $(this).css('background', '' + getCurrentBackgound + '');
                });
            }
            $('#pusherChat #' + id + ' .logMsg').scrollTop($('#pusherChat #' + id + ' .logMsg')[0].scrollHeight);
            $('.pusherChatBox#' + id + ' textarea').focus();
        }, 300);
    } else if (!$('.pusherChatBox#' + id).is(':visible')) {
        setTimeout(function() {
            $('.pusherChatBox#' + id + ' textarea').focus();
            $('.pusherChatBox#' + id + ' .logMsg').scrollTop($('.pusherChatBox#' + id + ' .logMsg')[0].scrollHeight);
        }, 300);
        clone = $('.pusherChatBox#' + id).clone();
        $('.pusherChatBox#' + id).remove();

        if (!$('.chatBoxslide .pusherChatBox:visible:first').html()) {
            $('.chatBoxslide').prepend(clone.show());
        } else {
            $(clone.show()).insertBefore('.chatBoxslide .pusherChatBox:visible:first');
        }
    }
    $('.pusherChatBox#' + id + ' textarea').focus();
    $('.pusherChatBox#' + id + ' .from').val(presenceChannel.members.me.id);
    $('.pusherChatBox#' + id + ' .to').val(obj.attr('href'));
    $('.pusherChatBox#' + id).addClass(off);
    updateBoxPosition();

    return false;

}



/*---------------* chatMemberUpdate() place & update users on user page, unred messages notifications *---------------*/
function chatMemberUpdate(subscribed_event) {
    var insertId = '';
    var notification = '';

    $.get(prchatSettings.usersList, function(data) {
        var offlineUser = '';
        var onlineUser = '';
        data = JSON.parse(data);
        $.each(data, function(user_id, value) {
            if (value.staffid != presenceChannel.members.me.id) {
                user = presenceChannel.members.get(value.staffid);
                if (user != null) {
                    onlineUser += '<a href="#' + value.staffid + '" id="' + value.staffid + '" class="on"><span class="user-name onlineUsername">' + strCapitalize(value.firstname + ' ' + value.lastname) + '</span><img src="' + fetchUserAvatar(value.staffid, value.profile_image) + '" class="imgFriend" /></a>';
                    if (presenceChannel.members.count > 0) {
                        $("#count").html(presenceChannel.members.count - 1);
                    }
                } else {
                    offlineUser += '<a href="#' + value.staffid + '" id="' + value.staffid + '" class="off"';
                    var lastLoginText = '';
                    if (value.last_login) {
                        lastLoginText = moment(value.last_login, "YYYYMMDD h:mm:ss").fromNow();
                    } else {
                        lastLoginText = 'Never';
                    }
                    offlineUser += ' data-toggle="tooltip" title="' + prchatSettings.chatLastSeenText + ': ' + lastLoginText + '">';
                    offlineUser += '<span class="user-name">' + strCapitalize(value.firstname + ' ' + value.lastname) + '</span><img src="' + fetchUserAvatar(value.staffid, value.profile_image) + '" class="imgOther" /></a>';
                }
            }
        });
        $('#pusherChat #members-list').html('');
        $('#pusherChat #members-list').prepend(onlineUser + offlineUser);

        if (subscribed_event === true) {
            if (prchatSettings.unreadMessages != null) {
                $.each(prchatSettings.unreadMessages, function(i, sender) {
                    insertId = $('#pusherChat #members-list a#' + sender.sender_id);
                    if (sender.sender_id === $(insertId).attr('id')) {
                        notification = '<span class="unread-notifications" data-badge="' + sender.count_messages + '"></span>';
                        $(insertId).addClass('animated flash');
                        $(notification).insertBefore('#pusherChat #members-list a#' + sender.sender_id + ' span');
                    }
                });
            }

            $.each(getActiveChatWindowsFromStorage(), function(index, obj) {
                var $userList = $('body').find('#members-list a[href="#' + obj.id + '"]');
                $userList.addClass('active-windows-click');
                $userList.click();
            });
        }
    });
}

function strCapitalize(string) {
    if (string != undefined) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
}

function updateLatestMessages(id) {
    $.post(prchatSettings.updateUnread, {
        id: id
    }).done(function(r) {
        if (r != 'true') {
            return false;
        }
    });
}

function fetchUserAvatar(id, image_name) {
    var type = 'small';
    var url = site_url + '/assets/images/user-placeholder.jpg';
    if (image_name == false) {
        return url;
    }
    if (image_name != null) {
        url = site_url + '/uploads/staff_profile_images/' + id + '/' + type + '_' + image_name;
    } else {
        url = site_url + '/assets/images/user-placeholder.jpg';
    }
    return url;
}

function prchat_setNoMoreMessages(to) {
    if ($('#no_messages_' + to).length == 0) {
        $('.logMsg#id_' + to).prepend('<div class="text-center" style="margin-top:5px;" id="no_messages_' + to + '">' + prchatSettings.noMoreMessagesText + '</div>')
    }
}

function loadMessages(el) {

    var pos = $(el).scrollTop();
    var id = $(el).attr("id");
    var to = $(el).parents().find('.pusherChatBox#' + id).attr('id').replace("id_", "");
    var from = userSessionId;

    if (endOfScroll[to] == true) {
        prchat_setNoMoreMessages(to);
        return false;
    }

    if (pos == 0) {

        activateLoader(id, true);
        initiatePrepending = function() {
            $.get(prchatSettings.getMessages, {
                from: from,
                to: to,
                offset: offsetPush[to]
            }).done(function(message) {

                message = JSON.parse(message);

                if (Array.isArray(message) == false) {
                    endOfScroll[to] = true;
                    prchat_setNoMoreMessages(to);
                } else {
                    offsetPush[to] += 10;
                }

                $(message).each(function(key, value) {
                    value.message = emojify.replace(value.message);
                    var element = $('.pusherChatBox#id_' + to + ' .logMsg .msgTxt');
                    if (value.reciever_id == from) {
                        element.prepend('<em><div class="conversation_from"><img class="friendProfilePic" src="' + fetchUserAvatar(value.sender_id, value.user_image) + '"/><small class="chatFriendUsername">' + strCapitalize(value.sender_fullname) + '</small></br><p data-toggle="tooltip" title="' + value.time_sent_formatted + '" class="friend">' + value.message + '</p></div></em>');
                    } else {
                        element.prepend('<em><div class="conversation_me"><img class="myProfilePic" src="' + fetchUserAvatar(value.sender_id, value.user_image) + '"/><small class="chatUsername">' + strCapitalize(value.sender_fullname) + '</small></br><div class="message_container" id="' + value.id + '"><span class="chat_options show_delete"><span class="confirm_delete fa fa-trash-o"></span></span><p data-toggle="tooltip" title="' + value.time_sent_formatted + '" class="you" style="background:' + prchatSettings.getChatColor + '">' + value.message + '</p></div></div></em>');
                    }
                });
                if (endOfScroll[to] == false) {
                    $(el).scrollTop(200);
                }
            });
        };
    }
}


/*---------------* Sound functions *---------------*/
var getSound = new Audio(site_url + 'modules/prchat/assets/chat_implements/sounds/push.mp3');
var getSound2 = new Audio(site_url + 'modules/prchat/assets/chat_implements/sounds/second_push.mp3');
var isSoundMuted = '';
var soundFinished = false;

function playChatSound() {
    return $(
        '<audio class="sound-player" autoplay="autoplay" ' + isSoundMuted + ' style="display:none;">' + '<source src="' + arguments[0] + '" />' + '<embed src="' + arguments[0] + '" hidden="true" autostart="true" loop="false"/>' + '</audio>').appendTo('body');
}

function stopSound() {
    setTimeout(function() {
        $(".sound-player").remove();
    }, 1000);
}

var playPushSound = (function() {
    return function() {
        if (!soundFinished) {
            soundFinished = true;
            playChatSound(getSound.src);
            setTimeout(function() {
                stopSound();
            }, 1000);
        }
    };
})();

var positions = JSON.parse(localStorage.positions || "{}");
var availableWidth = document.body.clientWidth - 305;
var availableHeight = document.body.clientHeight - 250;

$("#pusherChat .draggable").draggable({
    axis: "x,y",
    scroll: false,
    handle: '#membersContent .topInfo, #membersContent .chat-footer',
    start: function(event, ui) {
        $('#mainChatId').addClass('main-chat-dragging');
    },
    drag: function(event, ui) {
        if (ui.position.left > 0) {
            ui.position.left = 0;
            positions[this.id] = ui.position;
        }
        if (ui.position.left < -availableWidth) {
            ui.position.left = -availableWidth;
            positions[this.id] = -availableWidth;
        }
        if (ui.position.top > 0) {
            ui.position.top = 0;
            positions[this.id] = ui.position;
        }
        if (ui.position.top < -availableHeight) {
            ui.position.top = -availableHeight;
            positions[this.id] = -availableHeight;
        }
        positions[this.id] = ui.position;
        localStorage.positions = JSON.stringify(positions);
    }
});

window.onload = function() {
    var scroll_pos = $('#pusherChat .scroll');
    var localStoragePos = localStorage.chat_head_position;

    if (typeof(localStoragePos) != 'undefined') {
        scroll_pos.css('display', localStoragePos);
    } else {
        localStorage.chat_head_position = 'block';
        scroll_pos.css('display', localStoragePos);
    }
}