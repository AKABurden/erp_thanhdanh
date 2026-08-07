<div class="notifi-wrap-classify-container" style="bottom: -100%;">
</div>
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include_once(APPPATH.'views/admin/includes/helpers_bottom.php'); ?>
<?php include_once(APPPATH.'views/admin/includes/hau_scripts.php'); ?>
<?php hooks()->do_action('before_js_scripts_render'); ?>
<script type="text/javascript" id="new_js" src="<?=base_url()?>assets/functinjs/cong_js.js?v=1"></script>
<?php if (!empty($tnh)): ?>
<?php endif ?>
<?php $this->load->view('loader')?>
<script type="text/javascript">
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
// var lang_daterangepicker = <?= json_encode(lang('daterangepicker')) ?>;
var lang_datatables = <?= json_encode(lang('lang_datatables')) ?>;
var lang_core =
    <?= json_encode(['choose' => lang('choose'), 'product_name' => lang('tnh_product_name'), 'expected_date' => lang('tnh_expected_date'), 'date' => lang('date'), 'quantity' => lang('quantity'), 'total_quantity_less' => lang('tnh_total_quantity_less'), 'check_date_enter' => lang('tnh_check_date_enter'), 'yes' => lang('yes'), 'no' => lang('no'), 'you_want_remove' => lang('tnh_you_want_remove'), 'semi_products' => lang('semi_products'), 'materials' => lang('materials'), 'items' => lang('ch_items'), 'products' => lang('products'), 'origin' => lang('tnh_origin'), 'name' => lang('name'), 'errors' => lang('tnh_error_please_reload_page'), 'semi_products_outside' => lang('semi_products_outside'), 'delete' => lang('delete'), 'tax' => lang('tax'), 'tnh_you_are_referesh' => lang('tnh_you_are_referesh'), 'you_want_agree' => lang('you_want_agree')]); ?>;

var site =
    <?= json_encode(['sac' => get_option('sac'), 'decimals_number' => get_option('decimals_number'), 'decimals_money' => get_option('decimals_money'), 'thousands_sep' => get_option('thousands_sep'), 'decimals_sep' => get_option('decimals_sep'), 'base_url' => base_url()]) ?>
</script>
<div class="modal fade" id="tnhModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<div class="modal fade" id="tnhModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<div class="modal fade" id="tnhModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<div class="modal fade" id="tnhModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<div class="hide" id="show-form-detail"></div>
<script type="text/javascript" src="<?= js('accounting/accounting.js') ?>"></script>
<script type="text/javascript" src="<?= js('core.js?vs=4.7') ?>"></script>
<script src="<?php echo js('panigation/my_panigation.js?vs=1.1'); ?>"></script>
<script type="text/javascript">
var lang_daterangepicker = <?= json_encode(lang('daterangepicker')) ?>;
</script>
<?php echo app_compile_scripts();

/**
 * Global function for custom field of type hyperlink
 */
echo get_custom_fields_hyperlink_js_function(); ?>
<?php
/**
 * Check for any alerts stored in session
 */
app_js_alerts();
?>

<!--<script type="text/javascript" id="new_js" src="-->
<?//=base_url()?>
<!--assets/cjs/js/dataTables.fixedColumns.min.js"></script>-->
<!---->
<!--<script type="text/javascript" id="new_js" src="-->
<?//=base_url()?>
<!--assets/cjs/js/cong_js.js"></script>-->
<?php
/**
 * Check pusher real time notifications
 */

if(get_option('pusher_realtime_notifications') == 1){ ?>
<script type="text/javascript">
$(function() {
    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;
    <?php $pusher_options = hooks()->apply_filters('pusher_options', array());
            if(!isset($pusher_options['cluster']) && get_option('pusher_cluster') != ''){
                  $pusher_options['cluster'] = get_option('pusher_cluster');
            }
         ?>
    var pusher_options = <?php echo json_encode($pusher_options); ?>;
    var pusher = new Pusher("<?php echo get_option('pusher_app_key'); ?>", pusher_options);
    var channel = pusher.subscribe('notifications-channel-<?php echo get_staff_user_id(); ?>');
    channel.bind('notification', function(data) {
        fetch_notifications();
    });


    var channelTnh = pusher.subscribe('tnh-notification');
    channelTnh.bind('tnh-notification', function(data) {
        loadNotificationCustom();
    });

    var channelCustom = pusher.subscribe('tnh-notification-popup-<?php echo get_staff_user_id(); ?>');
    channelCustom.bind('tnh-notification-popup', function(data) {
        $('.notifi-wrap-classify-container').html(data);
        $('.notifi-wrap-classify-container').css('bottom', 0);
    });

    <?php
        $strUri = uri_string();
        $dtUri = explode('/', $strUri);
        ?>
    <?php if (!empty($dtUri[1]) && $dtUri[1] == "purchases"): ?>
    var channelTnhPurchase = pusher.subscribe('tnh-purchases');
    console.log(channelTnhPurchase);
    channelTnhPurchase.bind('tnh-purchases', function(data) {
        if (typeof tAPI != "undefined") {
            tAPI.draw();
        }
    });
    <?php endif ?>

});
</script>
<?php } ?>
<?php app_admin_footer(); ?>

<script type="text/javascript">
$(document).on('click', '.change_menu_child', function(e) {
    var data_object = $(this).attr('object');
    $('#side-menu').find('li:gt(0):not("#setup-menu-item")').remove();
    $.get(admin_url + 'dashboard/load_menu_child', {
        data_object: data_object
    }, function(data) {
        $('#setup-menu-item').before(data);
    })
})
</script>
<?php $this->load->view('loader');?>
<script>
Backloader();
</script>
<script type="text/javascript">
$(document).ready(function() {
    var url_admin = "<?=base_url('admin')?>";
    var url_admin_two = "<?=base_url('admin/')?>";
    var curentURL = "<?=current_full_url()?>";
    if (url_admin === curentURL || url_admin_two === curentURL) {
        window.location.href = "<?=base_url('admin/dashboard')?>";
    }
});
</script>
<?php if(is_mobile()) { ?>
<script>
$(document).ready(function() {
    var nav_second = $('ul.nav-second-level');
    $.each(nav_second, function(i, v) {
        $(v).parents('li').find('a:eq(0)').removeAttr('href');
    });
});
</script>
<?php } ?>

<!-- //plugin tnh -->
<script type="text/javascript" src="<?= js('daterangepicker/daterangepicker.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?= css('daterangepicker.css') ?>" />
<link rel="stylesheet" type="text/css" href="<?= css('select2/select2.css') ?>">
<script type="text/javascript" src="<?= js('select2/select2.js') ?>"></script>
<script type="text/javascript" src="<?= js('select2/select2_locale_vi.js') ?>"></script>
<script type="text/javascript" src="<?= js('number/jquery.number.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script src="<?php echo js('panigation/jquery.twbsPagination.js'); ?>"></script>
<!-- <script type="text/javascript" src="<?= js('masknumber/jquery.masknumber.js') ?>"></script> -->
<!-- <script type="text/javascript" src="<?= js('fm.numbertor.jquery.js') ?>"></script> -->
<!-- //end plugin tnh -->

<script>
$.extend(true, $.fn.dataTable.defaults, {
    "lengthMenu": dataTableLengthMenu(),
});
</script>
<?php include_once(APPPATH . 'views/admin/includes/c_js/pusher.php'); ?>


<script>
    $(document).ready(function() {
        var sell = $('.content-menu-v2').find('.none-a.sell').find('.div-icon-item-right');
        if(sell.length > 0) {
            $.get(admin_url + 'dashboard/get_number_orders_un_approved', function (result_number) {
                if(result_number == 0) {
                    result_number = "";
                }
                sell.after(`<span class="badge menu-badge bg-warning" id="group_25" style="position: absolute;top: 10px;right: 42px;; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;">${result_number}</span>`)
            })
        }

        var assigned = $('.content-menu-v2').find('.none-a.assigned').find('.div-icon-item-right');
        if(assigned.length > 0) {
            $.get(admin_url + 'dashboard/get_total_tasks', function (result_number) {
                if(result_number == 0) {
                    result_number = "";
                }
                assigned.after(`<span class="badge menu-badge bg-warning" id="group_25" style="position: absolute;top: 10px;right: 42px;; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;">${result_number}</span>`)

            })
        }
    });

    function createQcByProductionDetail(pod_id, stage_id) {
        var url = site.base_url + 'admin/quality_control/add_check_quality';
        var inputs = '';
        inputs += `<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">`;
        inputs += `<input type="hidden" name="pod_id" value="${pod_id}">`;
        inputs += `<input type="hidden" name="stage_id" value="${stage_id}">`;
        $("#show-form-detail").append('<form target="_blank" action="' + url + '" method="post" id="poster-detail">' + inputs + '</form>');
        $("#poster-detail").submit();
    }

    function tnhFormatMoneyNew(x, d = 0) {
        if (!d) { d = site.decimals_money; }

        x = Number(x);

        x = Math.round(x);
        d = 0;

        return accounting.formatNumber(
            x,
            d,
            site.thousands_sep == 0 ? ' ' : site.thousands_sep,
            site.decimals_sep
        );
    }
</script>
