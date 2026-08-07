<style>
    .box .value_new{
        font-size: 45px;
        cursor: pointer;
    }
    .box .label_new{
        font-size: 22px;
    }
</style>
<div id="dashboard_hcns" class="dashboard-hcns hide">
    <div class="page_hcns_1 page_hcns">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 210px);width: 33.33%;margin:20px 0; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label label_new red">TỔNG SỐ NHÂN VIÊN CHƯA CHECK IN</div>
                        <div class="value value_new red count-not-checkin" onclick="detailHcns(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new red">TỔNG SỐ NHÂN VIÊN CHƯA CHECK OUT</div>
                        <div class="value value_new red count-not-checkout" onclick="detailHcns(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ NHÂN VIÊN ĐÃ CHECK IN</div>
                        <div class="value value_new count-checkin" onclick="detailHcns(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ NHÂN VIÊN ĐÃ CHECK OUT</div>
                        <div class="value value_new count-checkout" onclick="detailHcns(this,4)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ NHÂN VIÊN NGHỈ PHÉP</div>
                        <div class="value value_new total_paid_holiday" onclick="detailHcns(this,5)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ NHÂN VIÊN TĂNG CA</div>
                        <div class="value value_new green total_overtime" onclick="detailHcns(this,6)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 33.33%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ NHÂN VIÊN ĐI TRỄ SAU 8H SÁNG</div>
                        <div class="value value_new total_late" onclick="detailHcns(this,7)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ NHÂN VIÊN VỀ SỚM TRƯỚC 17H CHIỀU</div>
                        <div class="value value_new total_late_new" onclick="detailHcns(this,8)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ HỢP ĐỒNG NHÂN VIÊN CẦN TÁI KÝ HỢP ĐỒNG</div>
                        <div class="value value_new total_signed" onclick="detailEvaluateHcns(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ ĐÁNH GIÁ CẦN TÁI ĐÁNH GIÁ</div>
                        <div class="value value_new total_evaluate" onclick="detailEvaluateHcns(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ CHỨNG NHẬN CẦN TÁI ĐÁNH GIÁ</div>
                        <div class="value value_new total_certification" onclick="detailEvaluateHcns(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ CHỨNG CHỈ CẦN TÁI ĐÁNH GIÁ</div>
                        <div class="value value_new total_certificate" onclick="detailEvaluateHcns(this,4)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div id="container_cham_cong"></div>
                    <div id="container_evaluate"></div>
                </div>
            </section>
        </div>
    </div>
    <div class="page_hcns_2 hide page_hcns">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 210px);width: 25%;margin:20px 0; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_new green">TỔNG PHIẾU KẾ HOẠCH GIA CÔNG</div>
                        <div class="value value_new green count_all_outsource_hcns" onclick="detailKsnbOutsource(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_new green">TỔNG BÁO CÁO VI PHẠM</div>
                        <div class="value value_new green count_all_vi_pham_hcns" onclick="detailKsnbKPH(this,4)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_new">TỔNG SỐ LỆNH TÁI SẢN XUẤT</div>
                        <div class="value value_new count_all_production_hcns" onclick="detailHcnsProduction(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_new">TỔNG SỐ ĐỀ XUẤT KHẨN</div>
                        <div class="value value_new total_internal_proposal_khan_hcns" onclick="detailHcnsInteralProposalKhan(this,1)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 25%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_new red">PHIẾU KẾ HOẠCH GIA CÔNG CHƯA DUYỆT</div>
                        <div class="value value_new red count_un_approve_outsource_hcns" onclick="detailKsnbOutsource(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_new red">BÁO CÁO VI PHẠM CHƯA HOÀN THÀNH</div>
                        <div class="value value_new red count_un_approve_vi_pham_hcns" onclick="detailKsnbKPH(this,5)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_new red">LỆNH TÁI SẢN XUẤT CHƯA HOÀN THÀNH</div>
                        <div class="value value_new red count_un_approve_production_hcns" onclick="detailHcnsProduction(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">

                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 25%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_new green">PHIẾU KẾ HOẠCH GIA CÔNG ĐÃ DUYỆT</div>
                        <div class="value value_new green count_approved_outsource_hcns" onclick="detailKsnbOutsource(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_new green">BÁO CÁO VI PHẠM HOÀN THÀNH</div>
                        <div class="value value_new green count_approved_vi_pham_hcns" onclick="detailKsnbKPH(this,6)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_new">LỆNH TÁI SẢN XUẤT HOÀN THÀNH</div>
                        <div class="value value_new count_approved_production_hcns" onclick="detailHcnsProduction(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 24.5%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div id="container_hcns_page_2"></div>
                </div>
            </section>
        </div>
    </div>
    <div class="sub-menu">
        <div class="sub-menu-child-new active child-hcns" data-value="1" onclick="changeFilterHcns(this,1)">Trang 1 (a)</div>
        <div class="sub-menu-child-new child-hcns" data-value="2" onclick="changeFilterHcns(this,2)">Trang 2 (s)</div>
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

    function detailHcns(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_hcns/') ?>${type}`,
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

    function detailHcnsProduction(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_hcns_production/') ?>${type}`,
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
    function detailEvaluateHcns(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_evaluate_hcns/') ?>${type}`,
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
    function detailHcnsInteralProposalKhan(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_internal_proposal_khan_hcns/') ?>${type}`,
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

    function changeFilterHcns(_this,id){
        $('.child-hcns').removeClass('active');
        $(_this).addClass('active');

        $('.page_hcns').addClass('hide');
        $(`.page_hcns_${id}`).removeClass('hide');
    }
    function getNumberFromText(selector) {
        let text = $(selector).text().trim();
        return text === '-' ? 0 : parseFloat(text) || 0;
    }
    function count_hcns() {
        dataChart = [];
        dataChartNew = [];
        dataChartHcns = [];
        let total_not_checkin_old = getNumberFromText('.count-not-checkin');
        let total_not_checkout_old = getNumberFromText('.count-not-checkout');
        let total_checkin_old = getNumberFromText('.count-checkin');
        let total_checkout_old = getNumberFromText('.count-checkout');
        let total_paid_holiday_old = getNumberFromText('.total_paid_holiday');
        let total_overtime_old = getNumberFromText('.total_overtime');
        let total_late_old = getNumberFromText('.total_late');
        let total_late_new_old = getNumberFromText('.total_late_new');

        let total_evaluate_old = getNumberFromText('.total_evaluate');
        let total_certification_old = getNumberFromText('.total_certification');
        let total_certificate_old = getNumberFromText('.total_certificate');

        let count_all_outsource_hcns_old = getNumberFromText('.count_all_outsource_hcns');
        let count_un_approve_outsource_hcns_old = getNumberFromText('.count_un_approve_outsource_hcns');
        let count_approved_outsource_hcns_old = getNumberFromText('.count_approved_outsource_hcns');
        let count_all_vi_pham_hcns_old = getNumberFromText('.count_all_vi_pham_hcns');
        let count_un_approve_vi_pham_hcns_old = getNumberFromText('.count_un_approve_vi_pham_hcns');
        let count_approved_vi_pham_hcns_old = getNumberFromText('.count_approved_vi_pham_hcns');
        let count_all_production_hcns_old = getNumberFromText('.count_all_production_hcns');
        let count_un_approve_production_hcns_old = getNumberFromText('.count_un_approve_production_hcns');
        let count_approved_production_hcns_old = getNumberFromText('.count_approved_production_hcns');
        if ($('#dashboard-hcns').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_hcns') ?>", res => {
                dataChart = [];
                dataChartNew = [];
                if (!res || !res.success) return;
                $('.count-not-checkin').text(mainFmt(mainNum(res.data.total_not_checkin)));
                $('.count-not-checkout').text(mainFmt(mainNum(res.data.total_not_checkout)));
                $('.count-checkin').text(mainFmt(mainNum(res.data.total_checkin)));
                $('.count-checkout').text(mainFmt(mainNum(res.data.total_checkout)));
                $('.total_paid_holiday').text(mainFmt(mainNum(res.data.total_paid_holiday)));
                $('.total_overtime').text(mainFmt(mainNum(res.data.total_overtime)));
                $('.total_late').text(mainFmt(mainNum(res.data.total_late)));
                $('.total_late_new').text(mainFmt(mainNum(res.data.total_late_new)));
                $('.total_signed').text(mainFmt(mainNum(res.data.total_signed)));
                $('.total_evaluate').text(mainFmt(mainNum(res.data.total_evaluate)));
                $('.total_certification').text(mainFmt(mainNum(res.data.total_certification)));
                $('.total_certificate').text(mainFmt(mainNum(res.data.total_certificate)));

                $('.count_all_outsource_hcns').text(mainFmt(mainNum(res.data.count_all_outsource_hcns)));
                $('.count_un_approve_outsource_hcns').text(mainFmt(mainNum(res.data.count_un_approve_outsource_hcns)));
                $('.count_approved_outsource_hcns').text(mainFmt(mainNum(res.data.count_approved_outsource_hcns)));
                $('.count_all_vi_pham_hcns').text(mainFmt(mainNum(res.data.count_all_vi_pham_hcns)));
                $('.count_un_approve_vi_pham_hcns').text(mainFmt(mainNum(res.data.count_un_approve_vi_pham_hcns)));
                $('.count_approved_vi_pham_hcns').text(mainFmt(mainNum(res.data.count_approved_vi_pham_hcns)));
                $('.count_all_production_hcns').text(mainFmt(mainNum(res.data.count_all_production_hcns)));
                $('.count_un_approve_production_hcns').text(mainFmt(mainNum(res.data.count_un_approve_production_hcns)));
                $('.count_approved_production_hcns').text(mainFmt(mainNum(res.data.count_approved_production_hcns)));
                $('.total_internal_proposal_khan_hcns').text(mainFmt(mainNum(res.data.total_internal_proposal_khan_hcns)));

                dataChart.push({
                    name: `Chưa CheckIn - ${res.data.total_not_checkin}`,
                    y: parseInt(res.data.total_not_checkin)
                });
                dataChart.push({
                    name: 'Chưa CheckOut - ' + res.data.total_not_checkout,
                    y: parseInt(res.data.total_not_checkout)
                });
                dataChart.push({
                    name: 'Đã CheckIn - ' + res.data.total_checkin,
                    y: parseInt(res.data.total_checkin)
                });
                dataChart.push({
                    name: 'Đã CheckOut - ' + res.data.total_checkout,
                    y: parseInt(res.data.total_checkout)
                });
                dataChart.push({
                    name: 'Nghỉ Phép - ' + res.data.total_paid_holiday,
                    y: parseInt(res.data.total_paid_holiday)
                });
                dataChart.push({
                    name: 'Tăng Ca - ' + res.data.total_overtime,
                    y: parseInt(res.data.total_overtime)
                });
                dataChart.push({
                    name: 'Đi trễ sau 8H sáng - ' + res.data.total_late,
                    y: parseInt(res.data.total_late)
                });
                dataChart.push({
                    name: 'Về sớm trước 17H chiều - ' + res.data.total_late_new,
                    y: parseInt(res.data.total_late_new)
                });


                if (total_not_checkin_old != res.data.total_not_checkin ||
                    total_not_checkout_old != res.data.total_not_checkout ||
                    total_checkin_old != res.data.total_checkin ||
                    total_checkout_old != res.data.total_checkout ||
                    total_paid_holiday_old != res.data.total_paid_holiday ||
                    total_overtime_old != res.data.total_overtime ||
                    total_late_old != res.data.total_late ||
                    total_late_new_old != res.data.total_late_new
                ) {
                    loadChartHcns(dataChart);
                }

                dataChartNew.push({
                    name: `Cần tái đánh giá - ${res.data.total_evaluate}`,
                    y: parseInt(res.data.total_evaluate)
                });
                dataChartNew.push({
                    name: 'Cần tái đánh giá chứng nhận - ' + res.data.total_certification,
                    y: parseInt(res.data.total_certification)
                });
                dataChartNew.push({
                    name: 'Cần tái đánh giá chứng chỉ - ' + res.data.total_certificate,
                    y: parseInt(res.data.total_certificate)
                });

                if (total_evaluate_old != res.data.total_evaluate ||
                    total_certification_old != res.data.total_certification ||
                    total_certificate_old != res.data.total_certificate) {
                    loadChartEvaluate(dataChartNew);
                }

                dataChartHcns = [
                    {
                        name: 'Tất cả',
                        data: [
                            res.data.count_all_outsource_hcns,
                            res.data.count_all_vi_pham_hcns,
                            res.data.count_all_production_hcns,
                        ]
                    },
                    {
                        name: 'Chưa duyệt, hoàn thành',
                        data: [
                            res.data.count_un_approve_outsource_hcns,
                            res.data.count_un_approve_vi_pham_hcns,
                            res.data.count_un_approve_production_hcns,
                        ]
                    },
                    {
                        name: 'Đã duyêt, hoàn thành',
                        data: [
                            res.data.count_approved_outsource_hcns,
                            res.data.count_approved_vi_pham_hcns,
                            res.data.count_approved_production_hcns,
                        ]
                    }
                ]
                loadChartHcnsPage2(dataChartHcns);

            });
        }

    }
    count_hcns();
    setInterval(count_hcns, 20000);

    function loadChartHcns(dataChart = {}) {
        Highcharts.chart('container_cham_cong', {
            chart: {
                type: 'pie',
                zooming: {
                    type: 'xy'
                },
                panning: {
                    enabled: true,
                    type: 'xy'
                },
                panKey: 'shift'
            },
            title: {
                text: 'Tổng quan nhân viên'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        style: {
                            fontSize: '15px',
                        },
                    }
                }
            },
            series: [
                {
                    name: 'Số lượng',
                    colorByPoint: true,
                    data: dataChart
                }
            ]
        });
    }

    function loadChartEvaluate(dataChart = {}) {
        Highcharts.chart('container_evaluate', {
            chart: {
                type: 'pie',
                zooming: {
                    type: 'xy'
                },
                panning: {
                    enabled: true,
                    type: 'xy'
                },
                panKey: 'shift'
            },
            title: {
                text: 'Tổng quan cần tái đánh giá'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        style: {
                            fontSize: '15px',
                        },
                    }
                }
            },
            series: [
                {
                    name: 'Số lượng',
                    colorByPoint: true,
                    data: dataChart
                }
            ]
        });
    }


    function loadChartHcnsPage2(dataChart = {}){
        Highcharts.chart('container_hcns_page_2', {
            chart: {
                type: 'bar',
                height: 650, // tăng chiều cao
            },
            title: {
                text: 'Tổng quan hành chính nhân sự'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: ['Kế hoạch gia công', 'Báo cáo vi phạm', 'Số lệnh tái sản xuất'],
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