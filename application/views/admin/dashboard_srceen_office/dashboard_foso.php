<style>
    .box .value_new{
        font-size: 45px;
    }
    .box .label_new{
        font-size: 22px;
    }
</style>
<div id="dashboard_foso" class="dashboard-foso hide">
    <div class="container-detail">
        <section class="" style="height: calc(100vh - 210px);width: 33.33%;margin:20px 0; float: left;">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box">
                    <div class="label ">TỔNG SỐ DỮ LIỆU TẠO MỚI</div>
                    <div class="value count_add" style="cursor: pointer" onclick="detailFOSO(this,1)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box">
                    <div class="label red">TỔNG SỐ DỮ LIỆU HỦY</div>
                    <div class="value red count_cancel" style="cursor: pointer" onclick="detailFOSO(this,2)">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 33.33%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box">
                    <div class="label">TỔNG SỐ DỮ LIỆU CẬP NHẬT-ĐIỀU CHỈNH</div>
                    <div class="value count_edit" style="cursor: pointer" onclick="detailFOSO(this,3)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box">
                    <div class="label">TỔNG SỐ DỮ LIỆU XÓA</div>
                    <div class="value count_delete" style="cursor: pointer" onclick="detailFOSO(this,4)">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 33%; float: left;">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div id="container_foso"></div>
            </div>
        </section>
    </div>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/modules/funnel.js"></script>
<script>
    function detailFOSO(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_foso/') ?>${type}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }
    function getNumberFromText(selector) {
        let text = $(selector).text().trim();
        return text === '-' ? 0 : parseFloat(text) || 0;
    }
    function count_foso() {
        dataChart = [];
        let count_add_old = getNumberFromText('.count_add');
        let count_cancel_old = getNumberFromText('.count_cancel');
        let count_edit_old = getNumberFromText('.count_edit');
        let count_delete_old = getNumberFromText('.count_delete');

        if ($('#dashboard-foso').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_foso') ?>", res => {
                dataChart = [];
                if (!res || !res.success) return;
                $('.count_add').text(mainFmt(mainNum(res.data.count_add)));
                $('.count_cancel').text(mainFmt(mainNum(res.data.count_cancel)));
                $('.count_edit').text(mainFmt(mainNum(res.data.count_edit)));
                $('.count_delete').text(mainFmt(mainNum(res.data.count_delete)));


                dataChart.push(res.data.count_add, res.data.count_edit, res.data.count_cancel, res.data.count_delete);
                if (count_add_old != res.data.count_add || count_cancel_old != res.data.count_cancel ||
                    count_edit_old != res.data.count_edit || count_delete_old != res.data.count_delete) {
                    loadChartFoso(dataChart);
                }
            });
        }

    }
    count_foso();
    setInterval(count_foso, 20000);

    function loadChartFoso(dataChart = {}) {
        Highcharts.chart('container_foso', {
            chart: {
                type: 'column',
                height: 650,
            },
            title: {
                text: 'Dữ liệu FOSO'
            },
            xAxis: {
                categories: ['Tạo mới', 'Cập nhập', 'Hủy phiếu', 'Xóa phiếu',],
                crosshair: true,
                accessibility: {
                    description: 'Countries'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Số lượng'
                }
            },
            tooltip: {
                valueSuffix: ' (SL)'
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,  // Bật hiển thị số trên mỗi cột
                        color: '#000000', // Màu chữ
                        style: {
                            fontWeight: 'bold',
                            fontSize: '15px',
                            textOutline: false // Tắt viền chữ
                        }
                    }
                }
            },
            series: [
                {
                    name: 'Phiếu',
                    data: dataChart,
                    color: '#0348A2',
                    showInLegend: false,
                }
            ]
        });
    }

</script>