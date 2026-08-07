<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.2') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<style type="text/css">
    .activity-feed .feed-item {
        padding-bottom: 10px;
    }

    #tb-export-materials-warehouses_filter {
        text-align: left;
    }

    .fixed {
        position: sticky;
        top: 0;
        width: 100%;
        z-index: 9999999;
    }
</style>
<div>
    <!-- <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script> -->

    <script src="<?= js('hightcharts/highcharts.js') ?>"></script>
    <script src="<?= js('hightcharts/exporting.js') ?>"></script>
    <script src="<?= js('hightcharts/export-data.js') ?>"></script>
    <script src="<?= js('hightcharts/accessibility.js') ?>"></script>
    <figure class="highcharts-figure">
        <div id="container"></div>
        <p class="highcharts-description">
            Chart showing stacked columns for comparing quantities. Stacked charts
            are often used to visualize data that accumulates to a sum. This chart
            is showing data labels for each individual section of the stack.
        </p>
    </figure>
</div>
<?php init_tail(); ?>

<script>
    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Stacked column chart'
        },
        xAxis: {
            categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total fruit consumption'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: ( // theme
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || 'gray'
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true
                }
            }
        },
        series: [{
            name: 'John',
            data: [5, 3, 4, 7, 2]
        }, {
            name: 'Jane',
            data: [2, 2, 3, 2, 1]
        }, {
            name: 'Joe',
            data: [3, 4, 4, 2, 5]
        }]
    });
</script>