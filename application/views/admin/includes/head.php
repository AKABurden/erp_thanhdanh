<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="<?php echo $locale; ?>">

<head>
    <?php $isRTL = (is_rtl() ? 'true' : 'false'); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="https://kit-pro.fontawesome.com/releases/v5.12.0/css/pro.min.css" />

    <!-- Hoàng CRM bổ xung flow chart -->
    <script type="text/javascript" src="<?= base_url('assets/plugins/OrgChart/common/jquery.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/plugins/OrgChart/common/jquery-ui.min.js');?>"></script>

    <script src="<?=base_url('assets/plugins/chart-GoJS/release/go.js')?>"></script>

    <script src="<?=base_url('assets/plugins/chart-GoJS/extensions/Figures.js')?>"></script>
    <!-- end -->

    <link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= css('notifications.css') ?>">
    <script src="https://cdn.rawgit.com/serratus/quaggaJS/0.12.1/dist/quagga.min.js"></script>
    <title><?php echo isset($title) ? $title : get_option('companyname'); ?></title>

    <?php echo app_compile_css(); ?>
    <!--    <link rel="stylesheet" type="text/css" href="-->
    <?//=base_url('assets/cjs/css/jquery.dataTables.min.css')?>
    <!--">-->
    <!--    <link rel="stylesheet" type="text/css" href="-->
    <?//=base_url('assets/cjs/css/fixedColumns.dataTables.min.css')?>
    <!--">-->
    <?php render_admin_js_variables(); ?>

    <script>
    var totalUnreadNotifications = <?php echo $current_user->total_unread_notifications; ?>,
        proposalsTemplates = <?php echo json_encode(get_proposal_templates()); ?>,
        contractsTemplates = <?php echo json_encode(get_contract_templates()); ?>,
        billingAndShippingFields = ['billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country',
            'shipping_street', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'
        ],
        isRTL = '<?php echo $isRTL; ?>',
        taskid, taskTrackingStatsData, taskAttachmentDropzone, taskCommentAttachmentDropzone, newsFeedDropzone,
        expensePreviewDropzone, taskTrackingChart, cfh_popover_templates = {},
        _table_api;
    </script>
    <?php app_admin_head(); ?>
</head>

<body
    <?php echo admin_body_class(isset($bodyclass) ? $bodyclass : ''); ?><?php if($isRTL === 'true'){ echo 'dir="rtl"';}; ?>>
    <?php hooks()->do_action('after_body_start'); ?>
    <style>
    .bell {
        font-size: 18px;
        color: #9e9e9e;
        -webkit-animation: ring 4s .7s ease-in-out infinite;
        -webkit-transform-origin: 50% 4px;
        -moz-animation: ring 4s .7s ease-in-out infinite;
        -moz-transform-origin: 50% 4px;
        animation: ring 4s .7s ease-in-out infinite;
        transform-origin: 50% 4px;
    }

    @-webkit-keyframes ring {
        0% {
            -webkit-transform: rotateZ(0);
        }

        1% {
            -webkit-transform: rotateZ(30deg);
        }

        3% {
            -webkit-transform: rotateZ(-28deg);
        }

        5% {
            -webkit-transform: rotateZ(34deg);
        }

        7% {
            -webkit-transform: rotateZ(-32deg);
        }

        9% {
            -webkit-transform: rotateZ(30deg);
        }

        11% {
            -webkit-transform: rotateZ(-28deg);
        }

        13% {
            -webkit-transform: rotateZ(26deg);
        }

        15% {
            -webkit-transform: rotateZ(-24deg);
        }

        17% {
            -webkit-transform: rotateZ(22deg);
        }

        19% {
            -webkit-transform: rotateZ(-20deg);
        }

        21% {
            -webkit-transform: rotateZ(18deg);
        }

        23% {
            -webkit-transform: rotateZ(-16deg);
        }

        25% {
            -webkit-transform: rotateZ(14deg);
        }

        27% {
            -webkit-transform: rotateZ(-12deg);
        }

        29% {
            -webkit-transform: rotateZ(10deg);
        }

        31% {
            -webkit-transform: rotateZ(-8deg);
        }

        33% {
            -webkit-transform: rotateZ(6deg);
        }

        35% {
            -webkit-transform: rotateZ(-4deg);
        }

        37% {
            -webkit-transform: rotateZ(2deg);
        }

        39% {
            -webkit-transform: rotateZ(-1deg);
        }

        41% {
            -webkit-transform: rotateZ(1deg);
        }

        43% {
            -webkit-transform: rotateZ(0);
        }

        100% {
            -webkit-transform: rotateZ(0);
        }
    }

    @-moz-keyframes ring {
        0% {
            -moz-transform: rotate(0);
        }

        1% {
            -moz-transform: rotate(30deg);
        }

        3% {
            -moz-transform: rotate(-28deg);
        }

        5% {
            -moz-transform: rotate(34deg);
        }

        7% {
            -moz-transform: rotate(-32deg);
        }

        9% {
            -moz-transform: rotate(30deg);
        }

        11% {
            -moz-transform: rotate(-28deg);
        }

        13% {
            -moz-transform: rotate(26deg);
        }

        15% {
            -moz-transform: rotate(-24deg);
        }

        17% {
            -moz-transform: rotate(22deg);
        }

        19% {
            -moz-transform: rotate(-20deg);
        }

        21% {
            -moz-transform: rotate(18deg);
        }

        23% {
            -moz-transform: rotate(-16deg);
        }

        25% {
            -moz-transform: rotate(14deg);
        }

        27% {
            -moz-transform: rotate(-12deg);
        }

        29% {
            -moz-transform: rotate(10deg);
        }

        31% {
            -moz-transform: rotate(-8deg);
        }

        33% {
            -moz-transform: rotate(6deg);
        }

        35% {
            -moz-transform: rotate(-4deg);
        }

        37% {
            -moz-transform: rotate(2deg);
        }

        39% {
            -moz-transform: rotate(-1deg);
        }

        41% {
            -moz-transform: rotate(1deg);
        }

        43% {
            -moz-transform: rotate(0);
        }

        100% {
            -moz-transform: rotate(0);
        }
    }

    @keyframes ring {
        0% {
            transform: rotate(0);
        }

        1% {
            transform: rotate(30deg);
        }

        3% {
            transform: rotate(-28deg);
        }

        5% {
            transform: rotate(34deg);
        }

        7% {
            transform: rotate(-32deg);
        }

        9% {
            transform: rotate(30deg);
        }

        11% {
            transform: rotate(-28deg);
        }

        13% {
            transform: rotate(26deg);
        }

        15% {
            transform: rotate(-24deg);
        }

        17% {
            transform: rotate(22deg);
        }

        19% {
            transform: rotate(-20deg);
        }

        21% {
            transform: rotate(18deg);
        }

        23% {
            transform: rotate(-16deg);
        }

        25% {
            transform: rotate(14deg);
        }

        27% {
            transform: rotate(-12deg);
        }

        29% {
            transform: rotate(10deg);
        }

        31% {
            transform: rotate(-8deg);
        }

        33% {
            transform: rotate(6deg);
        }

        35% {
            transform: rotate(-4deg);
        }

        37% {
            transform: rotate(2deg);
        }

        39% {
            transform: rotate(-1deg);
        }

        41% {
            transform: rotate(1deg);
        }

        43% {
            transform: rotate(0);
        }

        100% {
            transform: rotate(0);
        }
    }
    </style>