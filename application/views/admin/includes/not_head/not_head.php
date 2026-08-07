<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $isRTL = (is_rtl() ? 'true' : 'false'); ?>
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">

<!-- Hoàng CRM bổ xung flow chart -->
<script type="text/javascript" src="<?= base_url('assets/plugins/OrgChart/common/jquery.min.js');?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/OrgChart/common/jquery-ui.min.js');?>"></script>

<script src="<?=base_url('assets/plugins/chart-GoJS/release/go.js')?>"></script>

<script src="<?=base_url('assets/plugins/chart-GoJS/extensions/Figures.js')?>"></script>
<!-- end -->

<title><?php echo isset($title) ? $title : get_option('companyname'); ?></title>

<?php echo app_compile_css(); ?>
<?php render_admin_js_variables(); ?>

<script>
    totalUnreadNotifications = 0;
    isRTL = '<?php echo $isRTL; ?>';
</script>

