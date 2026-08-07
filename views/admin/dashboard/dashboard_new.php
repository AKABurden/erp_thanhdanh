<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(false); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<div id="wrapper" class="pannel-box" style="margin-left: 0px !important;">
    <?php $this->load->view('admin/general/menu'); ?>
    <?php $this->load->view('admin/general/dashboard'); ?>
</div>
<?php init_tail(); ?>
<?php
$date_end_after = date('Y-m-d');
$date_start_after = strtotime('-6 month', strtotime($date_end_after));
$date_start_after = date('Y-m-d', $date_start_after);
$date_end_before = $date_start_after;
$date_end_before = strtotime('-1 day', strtotime($date_end_before));
$date_end_before = date('Y-m-d', $date_end_before);
$date_start_before = strtotime('-6 month', strtotime($date_end_before));
$date_start_before = date('Y-m-d', $date_start_before);
$data_chart_manufactures = get_product_production_top($date_start_after, $date_end_after);
$data_chart_productions_orders = get_productions_orders($date_start_after, $date_end_after);
?>
<script>
    const ctx_manufactures = document.getElementById('charjs-manufactures-top').getContext('2d');
    const Chart_manufactures = new Chart(ctx_manufactures, {
        type: 'doughnut',
        data: <?= !empty($data_chart_manufactures) ? json_encode($data_chart_manufactures) : '[]' ?>
    })

    const ctx_productions = document.getElementById('charjs-productions').getContext('2d');
    var dataCharjsTask = <?= !empty($data_chart_productions_orders) ? json_encode($data_chart_productions_orders) : '[]' ?>;
    const Chart_productions = new Chart(ctx_productions, {
        type: 'bar',
        data: dataCharjsTask,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>