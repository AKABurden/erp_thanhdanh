<?php
    if($type_noti == 'manu'){
        $type_noti_check = 1;
    } elseif($type_noti == 'order'){
        $type_noti_check = 2;
    } elseif($type_noti == 'purchase'){
        $type_noti_check = 3;
    } elseif($type_noti == 'warehouse'){
        $type_noti_check = 4;
    } elseif($type_noti == 'task'){
		$type_noti_check = 6;
	} elseif($type_noti == 'maintenance'){
		$type_noti_check = 10;
	} elseif($type_noti == 'internal_proposal'){
		$type_noti_check = 13;
	} elseif($type_noti == 'kpi'){
		$type_noti_check = 14;
	}
    $_notifications = $this->misc_model->get_user_notifications(false, $type_noti_check);
    foreach ($_notifications as $notification) { ?>
<!--        <li class="relative notification-wrapper" data-notification-id="--><?php //echo $notification['id']; ?><!--">-->
<!--            --><?php //if (!empty($notification['link'])){ ?>
<!--            <a href="--><?php //echo admin_url($notification['link']); ?><!--" class="notification-top notification-link">-->
<!--                --><?php //} ?>
        <li class="relative notification-wrapper" data-notification-id="<?php echo $notification['id']; ?>">
        <?php if (!empty($notification['object_type']) && $notification['object_type'] == 'notifications_stock_delivery'){ ?>
            <a href="<?= base_url('admin/releases/view_delivery/'.$notification['object_id']) ?>" onclick="set_notification_read_inline(<?= $notification['id'] ?>)"  class="tnh-modal">
        <?php }
        if (!empty($notification['object_type']) && $notification['object_type'] == 'internal_proposal'){ ?>
            <a href="<?= admin_url($notification['link']) ?>" onclick="set_notification_read_inline(<?= $notification['id'] ?>)"  class="c_modal">
        <?php }
             elseif(!empty($notification['onclick'])) {?>
                <a <?=!empty($notification['onclick']) ? 'onclick="'.$notification['onclick'].'" ' : 'href="'.admin_url($notification['link']).'"'?> class="notification-link">
            <?php } elseif(!empty($notification['link'])){  ?>
            <a <?=!empty($notification['onclick']) ? 'onclick="'.$notification['onclick'].'"  href="#"' : 'href="'.admin_url($notification['link']).'"'?>
                    <?= ($notification['object_type'] == "agree_orders" || $notification['object_type'] == "kpi" || $notification['object_type'] == "agree_quotes" || $notification['object_type'] == "agree_orders_n" || $notification['object_type'] == "create_quotes" || $notification['object_type'] == "create_orders" || $notification['object_type'] == 'finished_payment' || $notification['object_type'] == 'export_supplies_task') ? ' data-tnh="modal" onclick="set_notification_read_inline('.$notification['id'].')" class="tnh-modal" data-toggle="modal" data-target="#myModal"' : '' ?>
                class="notification-top notification-link">
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
            <a href="#" class="text-muted pull-right not-mark-as-read-inline "
                onclick="set_notification_read_inline(<?php echo $notification['id']; ?>);" data-placement="left"
                data-toggle="tooltip" data-title="<?php echo _l('mark_as_read'); ?>">
                <small><i class="fa fa-circle-thin" aria-hidden="true"></i></small>
            </a>
            <?php } ?>
        </li>
    <?php } ?>
<?php if (count($_notifications) != 0) { ?>
<li class="divider no-mbot"></li>
<?php } ?>
<li class="text-center">
    <?php if (count($_notifications) > 0) { ?>
    <a
        href="<?php echo admin_url('profile?notifications=true&type_noti='.$type_noti_check); ?>"><?php echo _l('nav_view_all_notifications'); ?></a>
    <?php } else { ?>
    <?php echo _l('nav_no_notifications'); ?>
    <?php } ?>
</li>