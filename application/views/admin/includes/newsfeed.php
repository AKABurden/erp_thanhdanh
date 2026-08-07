<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $notificationCS = $this->site_model->getNotificationCustom(); ?>
<a href="#" class="dropdown-toggle notifications-icon" onclick="loadNotificationCustom(this)" data-toggle="dropdown" aria-expanded="false">
    <i class="fa fa-clock-o fa-fw fa-lg"></i>
    <span class="label icon-total-indicator bg-warning icon-notifications"><?= count($notificationCS) ?></span>
</a>
<ul class="dropdown-menu notifications animated fadeIn width400" data-total-unread="1">
    <li class="not_mark_all_as_read not-outside" style="border-bottom: 1px solid #9e9e9e; text-align: left !important;">
        <!-- <a href="#" onclick=""></a> -->
        <span class="bold"><?= lang('tnh_coupon_approved') ?></span>
    </li>
    <?php if (!empty($notificationCS)): ?>
        <?php foreach ($notificationCS as $key => $value): ?>
            <?php
                $typeCS = $value['type_cs'];
                $strTypeCS = lang($typeCS);
                $link = '';
                if ($typeCS == "quotes") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/quotes/view_quotes/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "orders") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/orders/view_order/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "business_plan") {
                    $strTypeCS = lang('tnh_business_plan');
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/business_plan/view_business_plan/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "deliveries") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/releases/view_delivery/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "productions_plan") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/manufactures/view_productions_plan/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "productions_capacity") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/manufactures/view_productions_capacity/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "productions_orders") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/manufactures/view_productions_orders/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "list_suggest_exporting") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/manufactures/view_suggest_exporting/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "exporting_producion") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/stock/view_exporting_production/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "purchase_products") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/stock/view_purchase_product/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "purchase_internal") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/stock/view_purchase_internal/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else if ($typeCS == "returned_goods") {
                    $link = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/returned_goods/view_returned_goods/'.$value['id']).'" data-toggle="modal" data-target="#myModal">'.$value['reference_no'].'</a>';
                } else {
                    $link = '<a href="javascript:void(0)" class="notification-top notification-link" style="padding: 0;">
                        <span class="notification-title">'.$value['reference_no'].'</span><br/>
                    </a>';
                }
                ?>
            <li class="relative notification-wrapper not-outside" style="text-align: left; padding: 5px;" data-noti-custom-id="1">
                <div class="notification-box wap-li" style="border-bottom: 1px solid #28b8da;">
                    <div>
                        <?= staff_profile_image($value['staffid'], array('staff-profile-image-small', 'img-circle notification-image', 'pull-left')) ?><?= lang('tnh_employees') ?>: <span class="bold">[<?= $value['created_by'] ?>]</span> vừa tạo <span class="bold">[<?= $strTypeCS ?>] </span> <?= _l('tnh_reference_no') ?> <?= $link ?>
                    </div>
                    <!-- <div style="margin: 5px 0px;">
                        <span class="text-default"><?= $strTypeCS ?>: </span>
                        <?= $link ?>
                    </div> -->
                    <small class="text-muted">
                        <div class="mbot15">
                            <span class="text-has-action" data-placement="right" data-toggle="tooltip" data-title="<?php echo _dt($value['date']); ?>">
                                <?php echo time_ago($value['date']); ?>
                            </span>
                        </div>
                        <div class="pull-left">
                            <span style="cursor: pointer;" onclick="agreeNotification(this, '<?= $value['type_cs'] ?>', '<?= $value['id'] ?>', '<?= get_staff_user_id() ?>')" class="label btn-agree"><i class="fa fa-check"></i> <?= lang('tnh_agree') ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </small>
                </div>
            </li>
        <?php endforeach ?>
    <?php endif ?>
</ul>