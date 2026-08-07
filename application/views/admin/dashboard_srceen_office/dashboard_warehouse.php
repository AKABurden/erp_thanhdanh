<style>
    .box .value_qa{
        font-size: 45px;
        cursor: pointer;
    }
    .box .label_qa{
        font-size: 30px;
    }
    .box_new{
        min-height: 140px;
    }
</style>
<div id="dashboard_warehouse" class="dashboard-warehouse hide">
    <div class="container-detail">
        <section class="" style="height: calc(100vh - 210px);width: 25%;margin:20px 0; float: left;">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_technial">TỔNG MÃ NPL TỒN KHO</div>
                    <div class="value value_ksnb count_all_nvl_warehouse" onclick="detailWarehouseKsnb(this,1)">-</div>
                </div>
                <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">TỔNG MÃ BTP TỒN KHO</div>
                    <div class="value value_ksnb count_all_btp_warehouse" onclick="detailWarehouseKsnb(this,4)">-</div>
                </div>
                <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">TỔNG MÃ TP TỒN KHO</div>
                    <div class="value value_ksnb count_all_tp_warehouse" onclick="detailWarehouseKsnb(this,7)">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width:25%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_technial">NPL TỒN KHO QUÁ HẠN TRÊN 6 THÁNG</div>
                    <div class="value value_ksnb count_6_nvl_warehouse" onclick="detailWarehouseKsnb(this,2)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">BTP TỒN KHO QUÁ HẠN TRÊN 6 THÁNG</div>
                    <div class="value value_ksnb count_6_btp_warehouse" onclick="detailWarehouseKsnb(this,5)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">TP TỒN KHO QUÁ HẠN TRÊN 6 THÁNG</div>
                    <div class="value value_ksnb count_6_tp_warehouse" onclick="detailWarehouseKsnb(this,8)">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 25%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_technial">NPL TỒN KHO QUÁ HẠN TRÊN 12 THÁNG</div>
                    <div class="value value_ksnb count_12_nvl_warehouse" onclick="detailWarehouseKsnb(this,3)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">BTR TỒN KHO QUÁ HẠN TRÊN 12 THÁNG</div>
                    <div class="value value_ksnb count_12_btp_warehouse" onclick="detailWarehouseKsnb(this,6)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">TP TỒN KHO QUÁ HẠN TRÊN 12 THÁNG</div>
                    <div class="value value_ksnb count_12_tp_warehouse" onclick="detailWarehouseKsnb(this,9)">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 24.5%; float: left;">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;margin-top: 20px">
                <div id="container_warehouse_new" style="margin-top: 10px"></div>
            </div>
        </section>
    </div>
</div>
<script>
    var intVal = function(i) {
        let rs = 0;

        if (typeof i === 'string') {
            rs = parseFloat(i.replace(/[\$,\.]/g, '')) || 0;
        } else if (typeof i === 'number') {
            rs = i;
        }

        return rs;
    };
    function getNumberFromText(selector) {
        let text = $(selector).text().trim();
        return text === '-' ? 0 : intVal(text) || 0;
    }
    function count_warehouse() {
        let count_all_nvl_warehouse_old = getNumberFromText('.count_all_nvl_warehouse');
        let count_all_btp_warehouse_old = getNumberFromText('.count_all_btp_warehouse');
        let count_all_tp_warehouse_old = getNumberFromText('.count_all_tp_warehouse');
        let count_6_nvl_warehouse_old = getNumberFromText('.count_6_nvl_warehouse');
        let count_6_btp_warehouse_old = getNumberFromText('.count_6_btp_warehouse');
        let count_6_tp_warehouse_old = getNumberFromText('.count_6_tp_warehouse');
        let count_12_nvl_warehouse_old = getNumberFromText('.count_12_nvl_warehouse');
        let count_12_btp_warehouse_old = getNumberFromText('.count_12_btp_warehouse');
        let count_12_tp_warehouse_old = getNumberFromText('.count_12_tp_warehouse');

        dataChartWarehouse = [];
        if ($('#dashboard-warehouse').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_warehouse') ?>", res => {
                dataChartWarehouse = [];
                if (!res || !res.success) return;
                $('.count_all_nvl_warehouse').text(mainFmt(mainNum(res.data.count_all_nvl_ksnb)));
                $('.count_all_btp_warehouse').text(mainFmt(mainNum(res.data.count_all_btp_ksnb)));
                $('.count_all_tp_warehouse').text(mainFmt(mainNum(res.data.count_all_tp_ksnb)));
                $('.count_6_nvl_warehouse').text(mainFmt(mainNum(res.data.count_6_nvl_ksnb)));
                $('.count_6_btp_warehouse').text(mainFmt(mainNum(res.data.count_6_btp_ksnb)));
                $('.count_6_tp_warehouse').text(mainFmt(mainNum(res.data.count_6_tp_ksnb)));
                $('.count_12_nvl_warehouse').text(mainFmt(mainNum(res.data.count_12_nvl_ksnb)));
                $('.count_12_btp_warehouse').text(mainFmt(mainNum(res.data.count_12_btp_ksnb)));
                $('.count_12_tp_warehouse').text(mainFmt(mainNum(res.data.count_12_tp_ksnb)));


                dataChartWarehouse = [
                    {
                        name: 'Tất cả',
                        data: [
                            res.data.count_all_nvl_ksnb,
                            res.data.count_all_btp_ksnb,
                            res.data.count_all_tp_ksnb,
                        ]
                    },
                    {
                        name: 'Quá hạn trên 6 tháng',
                        data: [
                            res.data.count_6_nvl_ksnb,
                            res.data.count_6_btp_ksnb,
                            res.data.count_6_tp_ksnb,
                        ]
                    },
                    {
                        name: 'Quá hạn trên 12 tháng',
                        data: [
                            res.data.count_12_nvl_ksnb,
                            res.data.count_12_btp_ksnb,
                            res.data.count_12_tp_ksnb,
                        ]
                    }
                ];

                if (count_all_nvl_warehouse_old != res.data.count_all_nvl_ksnb ||
                    count_all_btp_warehouse_old !=  res.data.count_all_btp_ksnb ||
                    count_all_tp_warehouse_old !=  res.data.count_all_tp_ksnb ||
                    count_6_nvl_warehouse_old !=  res.data.count_6_nvl_ksnb ||
                    count_6_btp_warehouse_old !=  res.data.count_6_btp_ksnb ||
                    count_6_tp_warehouse_old !=  res.data.count_6_tp_ksnb ||
                    count_12_nvl_warehouse_old !=  res.data.count_12_nvl_ksnb ||
                    count_12_btp_warehouse_old !=  res.data.count_12_btp_ksnb ||
                    count_12_tp_warehouse_old !=  res.data.count_12_tp_ksnb
                ) {
                    loadChartWarehouseNew(dataChartWarehouse);
                }
            });
        }
    }
    count_warehouse();
    setInterval(count_warehouse, 20000);

    function loadChartWarehouseNew(dataChart = {}){
        Highcharts.chart('container_warehouse_new', {
            chart: {
                type: 'bar',
                height: 650,
            },
            title: {
                text: 'Tổng quan tồn kho'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: ['NPL', 'BTP', 'TP'],
                title: {
                    text: null
                },
                gridLineWidth: 1,
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                labels: {
                    overflow: 'justify'
                },
                gridLineWidth: 0
            },
            tooltip: {
                valueSuffix: ' số lượng'
            },
            plotOptions: {
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: true
                    },
                    groupPadding: 0.1
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -40,
                y: -10,
                floating: true,
                borderWidth: 1,
                backgroundColor: 'var(--highcharts-background-color, #ffffff)',
                shadow: true
            },
            credits: {
                enabled: false
            },
            series: dataChart
        });
    }
</script>