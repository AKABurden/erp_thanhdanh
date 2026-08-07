<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include_once(APPPATH . 'views/admin/includes/modals/post_likes.php'); ?>
<?php include_once(APPPATH . 'views/admin/includes/modals/post_comment_likes.php'); ?>
<div id="event"></div>
<div id="newsfeed" class="animated fadeIn hide" <?php if($this->session->flashdata('newsfeed_auto')){echo 'data-newsfeed-auto';} ?>>
</div>
<!-- Task modal view -->
<div class="modal fade task-modal-single" id="task-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog <?php echo get_option('task_modal_class'); ?>">
    <div class="modal-content data">

    </div>
  </div>
</div>

<!--Add/edit task modal-->
<div id="_task"></div>

<!-- Lead Data Add/Edit-->
<div class="modal fade lead-modal" id="lead-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog <?php echo get_option('lead_modal_class'); ?>">
    <div class="modal-content data">

    </div>
  </div>
</div>

<div id="timers-logout-template-warning" class="hide">
  <h2 class="bold"><?php echo _l('timers_started_confirm_logout'); ?></h2>
  <hr />
  <a href="<?php echo admin_url('authentication/logout'); ?>" class="btn btn-danger"><?php echo _l('confirm_logout'); ?></a>
</div>

<!--Lead convert to customer modal-->
<div id="lead_convert_to_customer"></div>

<!--Lead reminder modal-->
<div id="lead_reminder_modal"></div>


<!-- cong thêm-->
<div class="div_modal_assigned"></div>
<div class="div_modal_orders"></div>
<div id="cong_modal"></div>
<!--end_cong theem-->

<!-- hoàng crm bổ xung thêm thông báo hết hạn -->
<div class="modal fade" id="notification_software_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="wrap-header-notification">
                  gia hạn SERVER
                </div>
                <div class="wrap-detail-notification">
                  Công ty <span class="wrap-danger-notification"><?= get_option('invoice_company_name'); ?></span> sử dụng gói <span class="wrap-danger-notification"><?= get_option('package_software'); ?></span> hết hạn ngày <span class="wrap-danger-notification"><?= get_option('expire_software'); ?></span>
                </div>
                <div class="wrap-notification-support">
                  Tổng đài hỗ trợ
                </div>
                <div class="wrap-number-support">
                  <?= get_option('switchboard_software'); ?>
                </div>
                <div class="wrap-notification-footer">
                  Cảm ơn quý khách đã đồng hành cùng <span class="wrap-info-notification"><?= get_option('switchboard_company_software'); ?></span>.
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default check-close" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- end -->