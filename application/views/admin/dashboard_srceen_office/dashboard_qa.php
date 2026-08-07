<style>
    .box .value_qa {
        font-size: 45px;
        cursor: pointer;
    }

    .box .label_qa {
        font-size: 30px;
    }

    .box_new {
        min-height: 140px;
    }
</style>
<div id="dashboard_qa" class="dashboard-qa hide">
    <div class="container-detail">
        <section class="" style="height: calc(100vh - 210px);width: 25%;margin:20px 0; float: left;">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_qa">TỔNG SỐ LSX</div>
                    <div class="value value_qa count_all_production_order" onclick="detailQaProduction(this,1)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_qa">TỔNG SỐ GIA CÔNG</div>
                    <div class="value value_qa count_all_suggest_outsource" onclick="detailQaOutsource(this,1)">-</div>
                </div>
                <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_qa">TỔNG SỐ ĐỀ XUẤT TĂNG CA</div>
                    <div class="value value_qa count_all_suggest_overtime" onclick="detailQaOvertime(this,1)">-</div>
                </div>
                <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label">TỔNG SỐ PHIẾU NHẬP ĐỦ</div>
                    <div class="value value_qa green js-purchase_orders-full">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 25%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_qa red">LSX CHƯA HOÀNH THÀNH</div>
                    <div class="value value_qa red count_un_approve_production_order" onclick="detailQaProduction(this,2)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_qa red">GIA CÔNG CHƯA HOÀN THÀNH</div>
                    <div class="value value_qa red count_un_approve_suggest_outsource" onclick="detailQaOutsource(this,2)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_qa red">ĐỀ XUẤT TĂNG CA CHƯA DUYỆT</div>
                    <div class="value value_qa red count_un_approve_suggest_overtime" onclick="detailQaOvertime(this,2)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label red">TỔNG SỐ PHIẾU CHƯA NHẬP</div>
                    <div class="value value_qa red js-purchase_orders-not_import">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 25%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new">
                    <div class="label label_qa">LSX HOÀN THÀNH</div>
                    <div class="value value_qa count_approved_production_order" onclick="detailQaProduction(this,3)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_qa">GIA CÔNG HOÀN THÀNH</div>
                    <div class="value value_qa count_approved_suggest_outsource" onclick="detailQaOutsource(this,3)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label label_qa">ĐỀ XUẤT TĂNG CA ĐÃ DUYỆT</div>
                    <div class="value value_qa count_approved_suggest_overtime" onclick="detailQaOvertime(this,3)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new">
                    <div class="label red">TỔNG SỐ PHIẾU NHẬP 1 PHẦN</div>
                    <div class="value value_qa red js-purchase_orders-part_import">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 24.5%; float: left;">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;margin-top: 66px">
                <div class="box">
                    <div class="label label_qa">TỔNG SỐ LỆNH CÓ PHẾ</div>
                    <div class="value value_qa total_production_items_errors" onclick="detailQaProductionError(this,1)">-</div>
                </div>
                <div id="container_qa_lsx" style="height: 350px"></div>
                <div id="container_qa" style="height: 350px"></div>
            </div>
        </section>
    </div>
</div>
<script>
    function detailQaProduction(_this, type = 1) {
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_qa_production/') ?>${type}`,
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

    function detailQaOutsource(_this, type = 1) {
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_qa_outsource/') ?>${type}`,
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

    function detailQaOvertime(_this, type = 1) {
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_qa_overtime/') ?>${type}`,
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

    function detailQaProductionError(_this, type = 1) {
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_qa_production_error/') ?>${type}`,
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

    function count_qa() {
        let count_un_approve_overtime_old = getNumberFromText('.count_un_approve_suggest_overtime');
        let count_approved_overtime_old = getNumberFromText('.count_approved_suggest_overtime');
        let count_un_approve_production_old = getNumberFromText('.count_un_approve_production_order');
        let count_approved_production_old = getNumberFromText('.count_approved_production_order');
        let total_production_items_errors = getNumberFromText('.total_production_items_errors');
        dataChartQA = [];
        dataChartQALSX = [];
        if ($('#dashboard-qa').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_qa') ?>", res => {
                dataChartQA = [];
                dataChartQALSX = [];
                if (!res || !res.success) return;
                $('.count_all_production_order').text(mainFmt(mainNum(res.data.count_all_production)));
                $('.count_un_approve_production_order').text(mainFmt(mainNum(res.data.count_un_approve_production)));
                $('.count_approved_production_order').text(mainFmt(mainNum(res.data.count_approved_production)));
                $('.count_all_suggest_outsource').text(mainFmt(mainNum(res.data.count_all_outsource)));
                $('.count_un_approve_suggest_outsource').text(mainFmt(mainNum(res.data.count_un_approve_outsource)));
                $('.count_approved_suggest_outsource').text(mainFmt(mainNum(res.data.count_approved_outsource)));
                $('.count_all_suggest_overtime').text(mainFmt(mainNum(res.data.count_all_overtime)));
                $('.count_un_approve_suggest_overtime').text(mainFmt(mainNum(res.data.count_un_approve_overtime)));
                $('.count_approved_suggest_overtime').text(mainFmt(mainNum(res.data.count_approved_overtime)));
                $('.total_production_items_errors').text(mainFmt(mainNum(res.data.total_production_items_errors)));


                if (count_un_approve_overtime_old != (res.data.count_un_approve_overtime) || count_approved_overtime_old != (res.data.count_approved_overtime)) {
                    dataChartQA.push({
                        name: 'Chưa duyệt - ' + (mainNum(res.data.count_un_approve_overtime)),
                        y: (mainNum(res.data.count_un_approve_overtime))
                    });
                    dataChartQA.push({
                        name: 'Đã duyệt - ' + (mainNum(res.data.count_approved_overtime)),
                        y: (mainNum(res.data.count_approved_overtime))
                    });

                    loadChartQA(dataChartQA);
                }

                if (count_un_approve_production_old != (res.data.count_un_approve_production) || count_approved_production_old != (res.data.count_approved_production) || total_production_items_errors != (res.data.total_production_items_errors)) {
                    dataChartQALSX.push({
                        name: 'Chưa hoàn thành - ' + (mainNum(res.data.count_un_approve_production)),
                        y: (mainNum(res.data.count_un_approve_production))
                    });
                    dataChartQALSX.push({
                        name: 'Đã hoàn thành - ' + (mainNum(res.data.count_approved_production)),
                        y: (mainNum(res.data.count_approved_production))
                    });
                    // dataChartQALSX.push({
                    //     name: 'Phế - ' + (mainNum(res.data.total_production_items_errors)),
                    //     y: (mainNum(res.data.total_production_items_errors))
                    // });
                    loadCharQALSX(dataChartQALSX);
                }

            });
        }
        countpurchase_orders_qa();
    }
    count_qa();
    setInterval(count_qa, 20000);

    function countpurchase_orders_qa() {
        if ($('#dashboard-qa').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_purchase_orders') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatspurchase_orders(res.stats || {});
            });
        }
    }

    function loadChartQA(dataChart = {}) {
        Highcharts.chart('container_qa', {
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
                text: 'Tổng quan đề xuất tăng ca'
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
            series: [{
                name: 'Số lượng',
                colorByPoint: true,
                data: dataChart
            }]
        });
    }

    function loadCharQALSX(dataChart = {}) {
        Highcharts.chart('container_qa_lsx', {
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
                text: 'Tổng quan lệnh sản xuất'
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
            series: [{
                name: 'Số lượng',
                colorByPoint: true,
                data: dataChart
            }]
        });
    }
</script>