<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.tabss {
    background: #fff;
    /* position: relative; */
    /* margin-bottom: 50px; */
}

.tabss>input,
.tabss>span {
    width: 12.5%;
    height: 35px;
    line-height: 35px;
    position: absolute;
    top: 0;
}

.tabss>input {
    cursor: pointer;
    filter: alpha(opacity=0);
    opacity: 0;
    position: absolute;
    z-index: 99;
}

.tabss>span {
    background: #f0f0f0;
    text-align: center;
    overflow: hidden;
}

.tabss>span i,
.tabss>span {
    -webkit-transition: all .5s;
    -moz-transition: all .5s;
    -o-transition: all .5s;
    transition: all .5s;
}

.tabss>input:hover+span {
    background: rgba(255, 255, 255, .1);
}

.tabss>input:checked+span {
    background: #fff;
}

.tabss>input:checked+span,
.tabss>input:hover+span {
    color: #3498DB;
}

#tab-1,
#tab-1+span {
    left: 0;
}

#tab-2,
#tab-2+span {
    left: 11%;
}

#tab-3,
#tab-3+span {
    left: 22%;
}

#tab-4,
#tab-4+span {
    left: 34.5%;
}

#tab-5,
#tab-5+span {
    left: 46.5%;
}
#tab-6,
#tab-6+span {
    left: 57.5%;
}
#tab-7,
#tab-7+span {
    left: 68.5%;
}

#tab-8,
#tab-8+span {
    left: 79.5%;
}
</style>
<a href="#" class="dropdown-toggle notifications-icon" onclick="clickTab(this)" data-toggle="dropdown"
    aria-expanded="false">
    <i class="fa fa-bell-o fa-fw fa-lg"></i>
    <?php if ($current_user->total_unread_notifications > 0) { ?>
    <span
        class="label icon-total-indicator bg-warning icon-notifications"><?php echo $current_user->total_unread_notifications; ?></span>
    <?php } ?>
</a>
<ul style="width:800px !important;" class="dropdown-menu notifications animated fadeIn width400"
    data-total-unread="<?php echo $current_user->total_unread_notifications; ?>">
    <div style="display:flex;justify-content: space-between;height:35px">
        <div class="tab_noti" style="display: flex;align-items: center;">
            <div class="tabss effect-1">
                <!-- tab-title -->
                <input type="radio" id="tab-1" value="task" name="tab-effect-1" checked="checked">
                <span>Công việc <span class="task label icon-total-indicator bg-warning "
                        style="top: 0;position: absolute;right: 1px;border-radius: 50%;font-size: 10px;"></span></span>
                <input type="radio" id="tab-2" value="manu" name="tab-effect-1">
                <span>Sản xuất <span class="manu label icon-total-indicator bg-warning "
                        style="top: 0;position: absolute;right: 1px;border-radius: 50%;font-size: 10px;"></span></span>

                <input type="radio" id="tab-3" value="order" name="tab-effect-1">
                <span>Đơn hàng <span class="order label icon-total-indicator bg-warning "
                        style="top: 0;position: absolute;right: 1px;border-radius: 50%;font-size: 10px;"></span></span>

                <input type="radio" id="tab-4" value="purchase" name="tab-effect-1">
                <span>Mua hàng <span class="purchase label icon-total-indicator bg-warning "
                        style="top: 0;position: absolute;right: 1px;border-radius: 50%;font-size: 10px;"></span></span>

                <input type="radio" id="tab-5" value="warehouse" name="tab-effect-1">
                <span>Kho <span class="warehouse label icon-total-indicator bg-warning "
                        style="top: 0;position: absolute;right: 1px;border-radius: 50%;font-size: 10px;"></span></span>

                <input type="radio" id="tab-6" value="internal_proposal" name="tab-effect-1">
                <span>ĐX nội bộ <span class="internal_proposal label icon-total-indicator bg-warning "
                        style="top: 0;position: absolute;right: 1px;border-radius: 50%;font-size: 10px;"></span></span>

                <input type="radio" id="tab-7" value="maintenance" name="tab-effect-1">
                <span>Bảo trì <span class="maintenance label icon-total-indicator bg-warning "
                        style="top: 0;position: absolute;right: 1px;border-radius: 50%;font-size: 10px;"></span></span>
                <input type="radio" id="tab-8" value="kpi" name="tab-effect-1">
                <span>HRM <span class="kpi label icon-total-indicator bg-warning "
                        style="top: 0;position: absolute;right: 1px;border-radius: 50%;font-size: 10px;"></span></span>

            </div>
        </div>
        <div class="read_noti" style="display: flex;align-items: center;">
            <li class="not_mark_all_as_read">
                <a style="color: #333" href="#"
                    onclick="mark_all_notifications_as_read_inline();count_noti(); return false;"><?php echo _l('Đọc tất cả'); ?></a>
            </li>
        </div>
    </div>
    <span class="result_noti">
    <?php
    $type_noti_check = 1;
//    $_notifications = $this->misc_model->get_user_notifications(false,$type_noti_check);
		if(!empty($_notifications)) {
        foreach ($_notifications as $notification) { ?>
            <li class="relative notification-wrapper" data-notification-id="<?php echo $notification['id']; ?>">
                <?php if (!empty($notification['link'])){ ?>
                <a href="<?php echo admin_url($notification['link']); ?>" <?=!empty($notification['onclick']) ? 'onclick="'.$notification['onclick'].'"' : ''?> class="notification-top notification-link">
                    <?php } ?>
                    <div class="notification-box<?php if ($notification['isread_inline'] == 0) {
                        echo ' unread';
                    } ?>">
                        <?php
                        if (($notification['fromcompany'] == NULL && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == NULL && $notification['fromclientid'] != 0)) {
                            if ($notification['fromuserid'] != 0) {
                                echo staff_profile_image($notification['fromuserid'], array('staff-profile-image-small', 'img-circle notification-image', 'pull-left'));
                            } else {
                                echo '<img src="' . contact_profile_image_url($notification['fromclientid']) . '" class="client-profile-image-small img-circle pull-left notification-image">';
                            }
                        }
                        ?>
                        <div class="media-body">
                            <?php
                            $additional_data = '';
                            if (!empty($notification['additional_data'])) {
                                $additional_data = unserialize($notification['additional_data']);
    
                                $i = 0;
                                foreach ($additional_data as $data) {
                                    if (strpos($data, '<lang>') !== false) {
                                        $lang = get_string_between($data, '<lang>', '</lang>');
                                        $temp = _l($lang);
                                        if (strpos($temp, 'project_status_') !== FALSE) {
                                            $status = get_project_status_by_id(strafter($temp, 'project_status_'));
                                            $temp = $status['name'];
                                        }
                                        $additional_data[$i] = $temp;
                                    }
                                    $i++;
                                }
                            }
                            $description = _l($notification['description'], $additional_data);
                            if (($notification['fromcompany'] == NULL && $notification['fromuserid'] != 0)
                                || ($notification['fromcompany'] == NULL && $notification['fromclientid'] != 0)) {
                                if ($notification['fromuserid'] != 0) {
                                    $description = $notification['from_fullname'] . ' - ' . $description;
                                } else {
                                    $description = $notification['from_fullname'] . ' - ' . $description . '<br /><span class="label inline-block mtop5 label-info">' . _l('is_customer_indicator') . '</span>';
                                }
                            }
                            echo '<span class="notification-title">' . $description . '</span>'; ?><br />
                            <small class="text-muted">
                                <span class="text-has-action" data-placement="right" data-toggle="tooltip"
                                    data-title="<?php echo _dt($notification['date']); ?>">
                                    <?php echo time_ago($notification['date']); ?>
                                </span>
                            </small>
                        </div>
                    </div>
                    <?php if (!empty($notification['link'])){ ?>
                </a>
                <?php } ?>
                <?php if ($notification['isread_inline'] == 0) { ?>
                <a href="#" class="text-muted pull-right not-mark-as-read-inline"
                    onclick="set_notification_read_inline(<?php echo $notification['id']; ?>);" data-placement="left"
                    data-toggle="tooltip" data-title="<?php echo _l('mark_as_read'); ?>">
                    <small><i class="fa fa-circle-thin" aria-hidden="true"></i></small>
                </a>
                <?php } ?>
            </li>
            <?php } ?>
            <?php } ?>
            <?php if (!empty($_notifications) && count($_notifications) != 0) { ?>
            <li class="divider no-mbot"></li>
            <?php } ?>
            <li class="text-center">
                <?php if (!empty($_notifications) && count($_notifications) > 0) { ?>
                <a
                    href="<?php echo admin_url('profile?notifications=true&type_noti='.$type_noti_check); ?>"><?php echo _l('nav_view_all_notifications'); ?></a>
                <?php } else { ?>
                <?php echo _l('nav_no_notifications'); ?>
                <?php } ?>
            </li>
    </span>
</ul>
<script>
function clickTab() {
    $("#tab-1").trigger('click');
}
$(document).on('click', 'input[name="tab-effect-1"]', function(event) {
    type_noti = $(this).attr('value');
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
        data['type_noti'] = type_noti;
    }
    $.ajax({
            url: admin_url + 'dashboard/loadNotification',
            type: 'GET',
            dataType: 'html',
            data: data,
        })
        .done(function(response) {
            $('.result_noti').html(response);
        })
        .fail(function() {
            console.log("error");
        })
    count_noti();
});

function count_noti() {
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $.ajax({
            url: admin_url + 'dashboard/countNoti',
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            data: data,
        })
        .done(function(response) {
            if (response.total_manu > 0) {
                $('.notifications ').find(".manu").html(response.total_manu);
            } else {
                $('.notifications ').find(".manu").html('');
            }
            if (response.total_order > 0) {
                $('.notifications ').find(".order").html(response.total_order);
            } else {
                $('.notifications ').find(".order").html('');
            }
            if (response.total_purchase > 0) {
                $('.notifications ').find(".purchase").html(response.total_purchase);
            } else {
                $('.notifications ').find(".purchase").html('');
            }
            if (response.total_warehouse > 0) {
                $('.notifications ').find(".warehouse").html(response.total_warehouse);
            } else {
                $('.notifications ').find(".warehouse").html('');
            }

            if (response.total_kpi > 0) {
                $('.notifications').find(".kpi").html(response.total_kpi);
            } else {
                $('.notifications').find(".kpi").html('');
            }
        })
        .fail(function() {
            console.log("error");
        })
}
</script>