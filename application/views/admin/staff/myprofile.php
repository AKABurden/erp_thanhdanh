<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.tab_profile {
    background: #fff;
    /* position: relative; */
    /* margin-bottom: 50px; */
}

.tab_profile>input,
.tab_profile>span {
    width: 15%;
    height: 35px;
    line-height: 35px;
    position: absolute;
}

.tab_profile>input {
    cursor: pointer;
    filter: alpha(opacity=0);
    opacity: 0;
    position: absolute;
    z-index: 99;
}

.tab_profile>span {
    background: #f0f0f0;
    text-align: center;
    overflow: hidden;
}

.tab_profile>span i,
.tab_profile>span {
    -webkit-transition: all .5s;
    -moz-transition: all .5s;
    -o-transition: all .5s;
    transition: all .5s;
}

.tab_profile>input:hover+span {
    background: rgba(255, 255, 255, .1);
}

.tab_profile>input:checked+span {
    background: #fff;
}

.tab_profile>input:checked+span,
.tab_profile>input:hover+span {
    color: #3498DB;
}

#tab-profile-1,
#tab-profile-1+span {
    left: unset;
}

#tab-profile-2,
#tab-profile-2+span {
    left: 15%;
}

#tab-profile-3,
#tab-profile-3+span {
    left: 30%;
}

#tab-profile-4,
#tab-profile-4+span {
    left: 45%;
}

#tab-profile-5,
#tab-profile-5+span {
    left: 60%;
}
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php if (($staff_p->staffid == get_staff_user_id() || is_admin()) && !$this->input->get('notifications')) { ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body no-padding-bottom">
                        <?php $this->load->view('admin/staff/stats'); ?>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php hooks()->do_action('before_staff_myprofile'); ?>
            <div class="col-md-5<?php if ($this->input->get('notifications')) { echo ' hide'; } ?>">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo _l('staff_profile_string'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php if ($staff_p->active == 0) { ?>
                        <div class="alert alert-danger text-center"><?php echo _l('staff_profile_inactive_account'); ?>
                        </div>
                        <hr />
                        <?php } ?>
                        <div class="button-group mtop10 pull-right">
                            <?php if (!empty($staff_p->facebook)) { ?>
                            <a href="<?php echo $staff_p->facebook; ?>" target="_blank"
                                class="btn btn-default btn-icon">
                                <i class="fa fa-facebook"></i>
                            </a>
                            <?php } ?>
                            <?php if (!empty($staff_p->linkedin)) { ?>
                            <a href="<?php echo $staff_p->linkedin; ?>" class="btn btn-default btn-icon">
                                <i class="fa fa-linkedin"></i>
                            </a>
                            <?php } ?>
                            <?php if (!empty($staff_p->skype)) { ?>
                            <a href="skype:<?php echo $staff_p->skype; ?>" data-toggle="tooltip"
                                title="<?php echo $staff_p->skype; ?>" target="_blank"
                                class="btn btn-default btn-icon"><i class="fa fa-skype"></i></a>
                            <?php } ?>
                            <?php if (has_permission('staff', '', 'edit') && has_permission('staff', '', 'view')) { ?>
                            <a href="<?php echo admin_url('staff/member/' . $staff_p->staffid); ?>"
                                class="btn btn-default btn-icon"><i class="fa fa-pencil-square"></i></a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <?php if (is_admin($staff_p->staffid)) { ?>
                        <p class="pull-right text-info">
                            <?php echo _l('staff_admin_profile'); ?>
                        </p>
                        <?php } ?>
                        <?php echo staff_profile_image($staff_p->staffid, array('staff-profile-image-thumb'), 'thumb'); ?>
                        <div class="profile mtop20 display-inline-block">
                            <h4>
                                <?php echo $staff_p->firstname . ' ' . $staff_p->lastname; ?>
                                <?php if ($staff_p->last_activity && $staff_p->staffid != get_staff_user_id()) { ?>
                                <small> - <?php echo _l('last_active'); ?>:
                                    <span class="text-has-action" data-toggle="tooltip"
                                        data-title="<?php echo _dt($staff_p->last_activity); ?>">
                                        <?php echo time_ago($staff_p->last_activity); ?>
                                    </span>
                                </small>
                                <?php } ?>
                            </h4>
                            <p class="display-block"><i class="fa fa-envelope"></i>
                                <a href="mailto:<?php echo $staff_p->email; ?>"><?php echo $staff_p->email; ?></a>
                            </p>
                            <?php if ($staff_p->phonenumber != '') { ?>
                            <p>
                                <i class="fa fa-phone-square"></i>
                                <?php echo $staff_p->phonenumber; ?>
                            </p>
                            <?php } ?>
                            <?php if (count($staff_departments) > 0) { ?>
                            <div class="form-group mtop10">
                                <label for="departments" class="control-label">
                                    <?php echo _l('staff_profile_departments'); ?>
                                </label>
                                <div class="clearfix"></div>
                                <?php
                                    foreach ($departments as $department) { ?>
                                <?php
                                        foreach ($staff_departments as $staff_department) {
                                            if ($staff_department['departmentid'] == $department['departmentid']) { ?>
                                <div class="chip-circle"><?php echo $staff_department['name']; ?></div>
                                <?php }
                                        }
                                        ?>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php if (($staff_p->staffid == get_staff_user_id() || is_admin()) && !$this->input->get('notifications')) { ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo _l('projects'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <div class="_filters _hidden_inputs hidden staff_projects_filter">
                            <?php echo form_hidden('staff_id', $staff_p->staffid); ?>
                        </div>
                        <?php render_datatable(array(
                                _l('project_name'),
                                _l('project_start_date'),
                                _l('project_deadline'),
                                _l('project_status'),
                            ), 'staff-projects', [], [
                                'data-last-order-identifier' => 'my-projects',
                                'data-default-order' => get_table_last_order('my-projects'),
                            ]); ?>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php if ($staff_p->staffid == get_staff_user_id()) { ?>
            <div class="col-md-7<?php if ($this->input->get('notifications')) {
                    echo ' col-md-offset-2';
                } ?>">
                <?php $type_noti = 1; if($this->input->get('type_noti')){ 
                    $type_noti = $this->input->get('type_noti');
                }?>

                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo _l('staff_profile_notifications'); ?>

                        </h4>
                        <div class="clearfix"></div>
                        <div style="display: flex;justify-content: space-between;margin-top:10px">
                            <div class="tab_noti_profile">
                                <div class="tab_profile effect-1">
                                    <input type="radio" id="tab-profile-1" <?= $type_noti == 1 ? 'checked' : ''?>
                                        value="1" name="tab-profile" checked="checked">
                                    <span>Sản xuất<span
                                            class="manu label icon-total-indicator bg-warning icon-notifications"
                                            style="top: 0;position: absolute;z-index:99999;border-radius: 50%;font-size: 10px;"></span></span>

                                    <input type="radio" id="tab-profile-2" <?= $type_noti == 2 ? 'checked' : ''?>
                                        value="2" name="tab-profile">
                                    <span>Đơn hàng<span
                                            class="order label icon-total-indicator bg-warning icon-notifications"
                                            style="top: 0;position: absolute;z-index:99999;border-radius: 50%;font-size: 10px;"></span></span>

                                    <input type="radio" id="tab-profile-3" <?= $type_noti == 3 ? 'checked' : ''?>
                                        value="3" name="tab-profile">
                                    <span>Mua hàng<span
                                            class="purchase label icon-total-indicator bg-warning icon-notifications"
                                            style="top: 0;position: absolute;z-index:99999;border-radius: 50%;font-size: 10px;"></span></span>

                                    <input type="radio" id="tab-profile-4" <?= $type_noti == 4 ? 'checked' : ''?>
                                        value="4" name="tab-profile">
                                    <span>Kho<span
                                            class="warehouse label icon-total-indicator bg-warning icon-notifications"
                                            style="top: 0;position: absolute;z-index:99999;right:1px;border-radius: 50%;font-size: 10px;"></span></span>

                                </div>
                            </div>
                            <div class="read_noti_profile">
                                <a href="#" onclick="mark_all_notifications_as_read_inline(); return false;">
                                    <?php echo _l('mark_all_as_read'); ?>
                                </a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" style="margin-left:unset;margin-right:unset;margin-top:20px" />
                        <div id="notifications"></div>
                        <a href="#" class="btn btn-info loader"><?php echo _l('load_more'); ?></a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(document).on('click', 'input[name="tab-profile"]', function(event) {
    type_noti = $(this).attr('value');
    var data = {};
    var page = 0;
    var total_pages = '<?php echo $total_pages; ?>';
    var notifications = $('#notifications');
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
        data['type_noti'] = type_noti;
        data['page'] = page;
    }
    $.ajax({
            url: admin_url + 'staff/notifications',
            type: 'POST',
            data: data,
        })
        .done(function(response) {
            response = JSON.parse(response);
            var notifications = '';
            $.each(response.notifications, function(i, obj) {
                notifications +=
                    '<div class="notification-wrapper" data-notification-id="' +
                    obj.id + '">';
                notifications += '<div class="notification-box-all' + (obj
                    .isread_inline == 0 ? ' unread' : '') + '">';
                var link_notification = '';
                var link_class_indicator = '';
                if (obj.link) {
                    link_notification = ' data-link="' + admin_url + obj.link +
                        '"';
                    link_class_indicator = ' notification_link';
                }
                notifications += obj.profile_image;
                notifications += '<div class="media-body' +
                    link_class_indicator + '"' + link_notification + '>';
                notifications += '<div class="description">';
                if (obj.from_fullname) {
                    notifications += obj.from_fullname + ' - ';
                }
                notifications += obj.description;
                notifications += '</div>';
                notifications +=
                    '<small class="text-muted text-right text-has-action" data-placement="right" data-toggle="tooltip" data-title="' +
                    obj.full_date + '">' + obj.date + '</small>';
                if (obj.isread_inline == 0) {
                    notifications +=
                        '<a href="#" class="text-muted pull-right not-mark-as-read-inline notification-profile" onclick="set_notification_read_inline(' +
                        obj.id +
                        ')" data-placement="left" data-toggle="tooltip" data-title="<?php echo _l('mark_as_read'); ?>"><small><i class="fa fa-circle-thin" aria-hidden="true"></i></a></small>';
                }
                notifications += '</div>';
                notifications += '</div>';
                notifications += '</div>';
            });
            if (notifications == '') {
                notifications = '<div style="font-weight:bold">Không có thông báo</div>'
            }
            $('#notifications').html(notifications);
            total_pages = response.total_pages;
            page++;
            if (page >= total_pages - 1) {
                $(".loader").addClass("hide");
            } else {
                $(".loader").removeClass("hide");
            }
            count_noti();
        })
        .fail(function() {
            console.log("error");
        })
});
count_noti();
$(function() {
    var notifications = $('#notifications');
    if (notifications.length > 0) {
        var page = 0;
        var total_pages = '<?php echo $total_pages; ?>';
        var type_noti = $('input[name=tab-profile]:checked').val();
        $('.loader').on('click', function(e) {
            e.preventDefault();
            if (page <= total_pages) {
                var data = {};
                if (typeof(csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                data['page'] = page;
                data['type_noti'] = type_noti;
                $.post(admin_url + 'staff/notifications', data).done(function(response) {
                    response = JSON.parse(response);
                    var notifications = '';
                    $.each(response.notifications, function(i, obj) {
                        notifications +=
                            '<div class="notification-wrapper" data-notification-id="' +
                            obj.id + '">';
                        notifications += '<div class="notification-box-all' + (obj
                            .isread_inline == 0 ? ' unread' : '') + '">';
                        var link_notification = '';
                        var link_class_indicator = '';
                        if (obj.link) {
                            link_notification = ' data-link="' + admin_url + obj.link +
                                '"';
                            link_class_indicator = ' notification_link';
                        }
                        notifications += obj.profile_image;
                        notifications += '<div class="media-body' +
                            link_class_indicator + '"' + link_notification + '>';
                        notifications += '<div class="description">';
                        if (obj.from_fullname) {
                            notifications += obj.from_fullname + ' - ';
                        }
                        notifications += obj.description;
                        notifications += '</div>';
                        notifications +=
                            '<small class="text-muted text-right text-has-action" data-placement="right" data-toggle="tooltip" data-title="' +
                            obj.full_date + '">' + obj.date + '</small>';
                        if (obj.isread_inline == 0) {
                            notifications +=
                                '<a href="#" class="text-muted pull-right not-mark-as-read-inline notification-profile" onclick="set_notification_read_inline(' +
                                obj.id +
                                ')" data-placement="left" data-toggle="tooltip" data-title="<?php echo _l('mark_as_read'); ?>"><small><i class="fa fa-circle-thin" aria-hidden="true"></i></a></small>';
                        }
                        notifications += '</div>';
                        notifications += '</div>';
                        notifications += '</div>';
                    });

                    $('#notifications').append(notifications);
                    total_pages = response.total_pages;
                    page++;
                });

                if (page >= total_pages - 1) {
                    $(".loader").addClass("hide");
                }
            }
        });

        $('.loader').click();
    }
});
</script>
</body>

</html>