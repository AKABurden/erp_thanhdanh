<style>
    .box .value_ksnb{
        font-size: 45px;
        cursor: pointer;
    }
    .box .label_technial{
        padding: 0 20px;
        font-size: 20px;
    }
    .box_new{
        min-height: 140px;
    }
</style>
<div id="dashboard_technial" class="dashboard-technial hide">
    <div class="container-detail">
        <section class="" style="height: calc(100vh - 210px);width: 16.66%;margin:20px 0; float: left;">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_technial">TỔNG SỐ ĐÁNH GIÁ THIẾT BỊ</div>
                    <div class="value value_ksnb count_all_rating" onclick="detailModalTechnialRatingMachines(this,1)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">TỔNG SỐ HIỆU CHUẨN MÁY MÓC THIẾT BỊ</div>
                    <div class="value value_ksnb count_all_calibration" onclick="detailModalTechnialCalibration(this,1)">-</div>
                </div>
                <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">TỔNG SỐ BẢO DƯỠNG MÁY MÓC THIẾT BỊ</div>
                    <div class="value value_ksnb count_all_maintenance" onclick="detailModalTechnialMaintenance(this,1)">-</div>
                </div>
                <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">TỔNG SỐ SỬA CHỮA MÁY MÓC THIẾT BỊ</div>
                    <div class="value value_ksnb count_all_repair" onclick="detailModalTechnialRepair(this,1)">-</div>
                </div>
                <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">TỔNG BÁO CÁO KHÔNG PHÙ HỢP THIẾT BỊ</div>
                    <div class="value value_ksnb count_all_report" onclick="detailModalTechnialBCKPH(this,1)">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 16.66%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_technial red">ĐÁNH GIÁ THIẾT BỊ CHƯA DUYỆT</div>
                    <div class="value value_ksnb red count_un_approve_rating" onclick="detailModalTechnialRatingMachines(this,2)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial red">HIỆU CHUẨN MÁY MÓC THIẾT BỊ CHƯA DUYỆT</div>
                    <div class="value value_ksnb red count_un_approve_calibration" onclick="detailModalTechnialCalibration(this,2)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial red">BẢO DƯỠNG MÁY MÓC THIẾT BỊ CHƯA DUYỆT</div>
                    <div class="value value_ksnb red count_un_approve_maintenance" onclick="detailModalTechnialMaintenance(this,2)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial red">SỬA CHỮA MÁY MÓC THIẾT BỊ CHƯA DUYỆT</div>
                    <div class="value value_ksnb red count_un_approve_repair" onclick="detailModalTechnialRepair(this,2)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial red">BCKPH THIẾT BỊ CHƯA HOÀNH THÀNH</div>
                    <div class="value value_ksnb red count_un_approve_report" onclick="detailModalTechnialBCKPH(this,2)">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 16.66%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_technial">ĐÁNH GIÁ THIẾT BỊ ĐÃ DUYỆT</div>
                    <div class="value value_ksnb count_approved_rating" onclick="detailModalTechnialRatingMachines(this,3)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">HIỆU CHUẨN MÁY MÓC THIẾT BỊ ĐÃ DUYỆT</div>
                    <div class="value value_ksnb count_approved_calibration" onclick="detailModalTechnialCalibration(this,3)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">BẢO DƯỠNG MÁY MÓC THIẾT BỊ ĐÃ DUYỆT</div>
                    <div class="value value_ksnb count_approved_maintenance" onclick="detailModalTechnialMaintenance(this,3)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">SỬA CHỮA MÁY MÓC THIẾT BỊ ĐÃ DUYỆT</div>
                    <div class="value value_ksnb count_approved_repair" onclick="detailModalTechnialRepair(this,3)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">BÁO CÁO KHÔNG PHÙ HỢP THIẾT BỊ HOÀN THÀNH</div>
                    <div class="value value_ksnb count_approved_report" onclick="detailModalTechnialBCKPH(this,3)">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 16.66%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_technial red">ĐÁNH GIÁ THIẾT BỊ CHƯA HOÀN THÀNH</div>
                    <div class="value value_ksnb red count_un_approve_finish_rating" onclick="detailModalTechnialRatingMachines(this,4)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial red">HIỆU CHUẨN MÁY MÓC THIẾT CHƯA HOÀN THÀNH</div>
                    <div class="value value_ksnb red count_un_approve_finish_calibration" onclick="detailModalTechnialCalibration(this,4)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial red">BẢO DƯỠNG THIẾT BỊ CHƯA HOÀN THÀNH</div>
                    <div class="value value_ksnb red count_un_approve_finish_maintenance" onclick="detailModalTechnialMaintenance(this,4)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial red">SỬA CHỮA MÁY MÓC THIẾT BỊ CHƯA HOÀN THÀNH</div>
                    <div class="value value_ksnb red count_un_approve_finish_repair" onclick="detailModalTechnialRepair(this,4)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial"></div>
                    <div class="value value_ksnb "></div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 16.66%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_technial">ĐÁNH GIÁ THIẾT BỊ HOÀN THÀNH</div>
                    <div class="value value_ksnb count_approved_finish_rating" onclick="detailModalTechnialRatingMachines(this,5)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">HIỆU CHUẨN MÁY MÓC THIẾT BỊ HOÀN THÀNH</div>
                    <div class="value value_ksnb count_approved_finish_calibration" onclick="detailModalTechnialCalibration(this,5)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">BẢO DƯỠNG THIẾT BỊ HOÀN THÀNH</div>
                    <div class="value value_ksnb count_approved_finish_maintenance" onclick="detailModalTechnialMaintenance(this,5)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial">SỬA CHỮA MÁY MÓC THIẾT BỊ HOÀN THÀNH</div>
                    <div class="value value_ksnb count_approved_finish_repair" onclick="detailModalTechnialRepair(this,5)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_technial"></div>
                    <div class="value value_ksnb "></div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 16%; float: left;">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;margin-top: 66px">
                <div id="container_technial" style="margin-top: 50px"></div>
            </div>
        </section>
    </div>
</div>
<script>
    function detailModalTechnialRatingMachines(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_technial_rating_machines/') ?>${type}`,
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
    function detailModalTechnialCalibration(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_technial_calibration/') ?>${type}`,
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
    function detailModalTechnialMaintenance(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_technial_maintenance/') ?>${type}`,
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
    function detailModalTechnialRepair(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_technial_repair/') ?>${type}`,
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
    function detailModalTechnialBCKPH(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_technial_bckph/') ?>${type}`,
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
    function count_technial() {
        dataChartTechnial = [];
        if ($('#dashboard-technial').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_technial') ?>", res => {
                dataChartTechnial = [];
                if (!res || !res.success) return;
                $('.count_all_report').text(mainFmt(mainNum(res.data.count_all_report)));
                $('.count_un_approve_report').text(mainFmt(mainNum(res.data.count_un_approve_report)));
                $('.count_approved_report').text(mainFmt(mainNum(res.data.count_approved_report)));
                $('.count_all_rating').text(mainFmt(mainNum(res.data.count_all_rating)));
                $('.count_approved_rating').text(mainFmt(mainNum(res.data.count_approved_rating)));
                $('.count_un_approve_rating').text(mainFmt(mainNum(res.data.count_un_approve_rating)));
                $('.count_un_approve_finish_rating').text(mainFmt(mainNum(res.data.count_un_approve_finish_rating)));
                $('.count_approved_finish_rating').text(mainFmt(mainNum(res.data.count_approved_finish_rating)));
                $('.count_all_calibration').text(mainFmt(mainNum(res.data.count_all_calibration)));
                $('.count_approved_calibration').text(mainFmt(mainNum(res.data.count_approved_calibration)));
                $('.count_un_approve_calibration').text(mainFmt(mainNum(res.data.count_un_approve_calibration)));
                $('.count_un_approve_finish_calibration').text(mainFmt(mainNum(res.data.count_un_approve_finish_calibration)));
                $('.count_approved_finish_calibration').text(mainFmt(mainNum(res.data.count_approved_finish_calibration)));
                $('.count_all_maintenance').text(mainFmt(mainNum(res.data.count_all_maintenance)));
                $('.count_approved_maintenance').text(mainFmt(mainNum(res.data.count_approved_maintenance)));
                $('.count_un_approve_maintenance').text(mainFmt(mainNum(res.data.count_un_approve_maintenance)));
                $('.count_un_approve_finish_maintenance').text(mainFmt(mainNum(res.data.count_un_approve_finish_maintenance)));
                $('.count_approved_finish_maintenance').text(mainFmt(mainNum(res.data.count_approved_finish_maintenance)));
                $('.count_all_repair').text(mainFmt(mainNum(res.data.count_all_repair)));
                $('.count_approved_repair').text(mainFmt(mainNum(res.data.count_approved_repair)));
                $('.count_un_approve_repair').text(mainFmt(mainNum(res.data.count_un_approve_repair)));
                $('.count_un_approve_finish_repair').text(mainFmt(mainNum(res.data.count_un_approve_finish_repair)));
                $('.count_approved_finish_repair').text(mainFmt(mainNum(res.data.count_approved_finish_repair)));

                dataChartTechnial = [
                    {
                        name: 'Tất cả',
                        data: [
                            res.data.count_all_rating,
                            res.data.count_all_calibration,
                            res.data.count_all_maintenance,
                            res.data.count_all_repair,
                            res.data.count_all_report,
                        ]
                    },
                    {
                        name: 'Chưa duyệt',
                        data: [
                            res.data.count_un_approve_rating,
                            res.data.count_un_approve_calibration,
                            res.data.count_un_approve_maintenance,
                            res.data.count_un_approve_repair,
                            0,
                        ]
                    },
                    {
                        name: 'Đã duyệt',
                        data: [
                            res.data.count_approved_rating,
                            res.data.count_approved_calibration,
                            res.data.count_approved_maintenance,
                            res.data.count_approved_repair,
                            0,
                        ]
                    },
                    {
                        name: 'Chưa hoàn thành',
                        data: [
                            res.data.count_un_approve_finish_rating,
                            res.data.count_un_approve_finish_calibration,
                            res.data.count_un_approve_finish_maintenance,
                            res.data.count_un_approve_finish_repair,
                            res.data.count_un_approve_report,
                        ]
                    },
                    {
                        name: 'Đã hoàn thành',
                        data: [
                            res.data.count_approved_finish_rating,
                            res.data.count_approved_finish_calibration,
                            res.data.count_approved_finish_maintenance,
                            res.data.count_approved_finish_repair,
                            res.data.count_approved_report,
                        ]
                    },
                ];

                loadChartTechnial(dataChartTechnial);
            });
        }
    }
    count_technial();
    setInterval(count_technial, 20000);

    function loadChartTechnial(dataChart = {}){
        Highcharts.chart('container_technial', {
            chart: {
                type: 'bar',
                height: 650, // tăng chiều cao
            },
            title: {
                text: 'Tổng quan dữ liệu kỹ thuật'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: ['Đánh giá thiết bị', 'Hiệu chuẩn máy móc', 'Bảo dưỡng máy móc', 'Sửa chữa máy móc', 'BCKPH thiết bị'],
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
                x: -10,
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