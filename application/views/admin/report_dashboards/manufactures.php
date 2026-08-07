<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(false); ?>
<style>
    #wrapper {
        margin: 0;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div role="tabpanel">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#tab" aria-controls="tab" role="tab" onclick="clickTabManu(2)" data-toggle="tab"><?= lang('Báo cáo tổng quan sản xuất') ?></a>
                </li>
                <li role="presentation">
                    <a href="#tab" aria-controls="tab" role="tab" onclick="clickTabManu(3)" data-toggle="tab"><?= lang('Báo cáo tổng quan QC') ?></a>
                </li>
                <li role="presentation" >
                    <a href="#home" aria-controls="home" role="tab" onclick="clickTabManu(1)" data-toggle="tab"><?= lang('Báo cáo sản xuất') ?></a>
                </li>
            </ul>
        
            <div class="tab-content">
                <div id="content-report"></div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('hightcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?= js('hightcharts/exporting.js') ?>"></script>

<script>
    var fnserverparams = {
        year: "#year",
    };

    Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: 'Arial, sans-serif' // Thiết lập font chữ cho tất cả biểu đồ
            }
        }
    });

    function clickTabManu(_value) {
        $.ajax({
            type: "GET",
            url: site.base_url+'admin/report_dashboards/tabManufactures',
            data: {
                '_value': _value
            },
            dataType: "html",
            success: function (response) {
                $('#content-report').html(response);
            }
        });
    }
    
    $(document).ready(function() {
        clickTabManu(2);
    });
</script>