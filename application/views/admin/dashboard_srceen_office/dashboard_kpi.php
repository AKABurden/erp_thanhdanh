<style>
    .box_new_vs1{
        min-height: 160px;
    }
    .rating_kpi_a{
        color: #f5b896;
    }
    .rating_kpi_a_vs1{
        color: #f1a572;
    }
    .rating_kpi_b{
        color: #e68e4d;
    }
    .rating_kpi_c{
        color: #e17a28;
    }
    .rating_kpi_d{
        color: #ce6500;
    }
    .child-kpi{
        max-width: 107px;
        min-height: 35px;
        text-align: center;
        display: flex;
        align-items: center;
    }
</style>
<div id="dashboard_kpi" class="dashboard-kpi hide">
    <div class="container-detail">
        <section class="" style="height: calc(100vh - 210px);width: 25%;margin:20px 0; float: left;">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new_vs1">
                    <div class="label">TỔNG XẾP LOẠI A+</div>
                    <div class="value count_kpi_a" style="cursor: pointer" onclick="detailKpi(this,1)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new_vs1">
                    <div class="label">TỔNG XẾP LOẠI C</div>
                    <div class="value count_kpi_c" style="cursor: pointer" onclick="detailKpi(this,4)">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 25%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new_vs1">
                    <div class="label">TỔNG XẾP LOẠI A</div>
                    <div class="value count_kpi_a_vs1" style="cursor: pointer" onclick="detailKpi(this,2)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0; ">
                <div class="box box_new_vs1">
                    <div class="label">TỔNG XẾP LOẠI D</div>
                    <div class="value count_kpi_d" style="cursor: pointer" onclick="detailKpi(this,5)">-</div>
                </div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 25%; float: left;margin:20px 0">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div class="box box_new_vs1">
                    <div class="label">TỔNG XẾP LOẠI B</div>
                    <div class="value count_kpi_b" style="cursor: pointer" onclick="detailKpi(this,3)">-</div>
                </div>
                <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                <div class="box box_new_vs1"></div>
            </div>
        </section>
        <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
        <section class="" style="height: calc(100vh - 210px);width: 24.5%; float: left;">
            <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                <div id="container_kpi"></div>
            </div>
        </section>
    </div>
    <div class="sub-menu">
        <div class="sub-menu-child-new active child-kpi" data-value="-1" onclick="changeFilterKpi(this,-1)">Tất cả</div>
        <?php if(!empty($dtDepartment)){ ?>
            <?php foreach ($dtDepartment as $key => $value){ ?>
                <div class="sub-menu-child-new child-kpi" data-value="<?= $value['departmentid'] ?>" onclick="changeFilterKpi(this,<?= $value['departmentid'] ?>)"><?= $value['name'] ?></div>
            <?php } ?>
        <?php } ?>
    </div>
</div>
<script>
    function detailKpi(_this,type = 1){
        department_id = $('.child-kpi.active').data('value');
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_kpi/') ?>${type}/${department_id}`,
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
    function changeFilterKpi(_this,id){
        $('.child-kpi').removeClass('active');
        $(_this).addClass('active');
        title = $(_this).text();
        $(".sub-title").text(' - '+title);
        count_kpi(id);
    }
    function getNumberFromText(selector) {
        let text = $(selector).text().trim();
        return text === '-' ? 0 : parseFloat(text) || 0;
    }
    function count_kpi(department_id = -1) {
        dataChartKpi = [];
        let count_a_old = getNumberFromText('.count_kpi_a');
        let count_a_vs1_old = getNumberFromText('.count_kpi_a_vs1');
        let count_b_old = getNumberFromText('.count_kpi_b');
        let count_c_old = getNumberFromText('.count_kpi_c');
        let count_d_old = getNumberFromText('.count_kpi_d');
        if ($('#dashboard-kpi').hasClass('active')) {
            $.ajax({
                url: "<?= site_url('dashboard_srceen_office/count_kpi') ?>",
                type: 'GET',
                dataType: 'JSON',
                data: {
                    department_id: department_id
                },
            })
                .done(function (data) {
                    dataChartKpi = [];
                    if (!data || !data.success) return;
                    $('.count_kpi_a').text(mainFmt(mainNum(data.data.count_a)));
                    $('.count_kpi_a_vs1').text(mainFmt(mainNum(data.data.count_a_vs1)));
                    $('.count_kpi_b').text(mainFmt(mainNum(data.data.count_b)));
                    $('.count_kpi_c').text(mainFmt(mainNum(data.data.count_c)));
                    $('.count_kpi_d').text(mainFmt(mainNum(data.data.count_d)));

                    dataChartKpi.push({
                        name: 'Xếp loại A+ - ' + mainFmt(mainNum(data.data.count_a)),
                        y: mainNum(data.data.count_a),
                    });
                    dataChartKpi.push({
                        name: 'Xếp loại A - ' + mainFmt(mainNum(data.data.count_a_vs1)),
                        y: mainNum(data.data.count_a_vs1),
                    });
                    dataChartKpi.push({
                        name: 'Xếp loại B - ' + mainFmt(mainNum(data.data.count_b)),
                        y: mainNum(data.data.count_b),
                    });
                    dataChartKpi.push({
                        name: 'Xếp loại C - ' + mainFmt(mainNum(data.data.count_c)),
                        y: mainNum(data.data.count_c),
                    });
                    dataChartKpi.push({
                        name: 'Xếp loại D - ' + mainFmt(mainNum(data.data.count_d)),
                        y: mainNum(data.data.count_d),
                    });

                    if (count_a_old != data.data.count_a ||
                        count_a_vs1_old != data.data.count_a_vs1 ||
                        count_b_old != data.data.count_b ||
                        count_c_old != data.data.count_c ||
                        count_d_old != data.data.count_d) {
                        loadChartKpi(dataChartKpi);
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        }
    }
    count_kpi($('.child-kpi.active').data('value'));
    setInterval(function() {
        count_kpi($('.child-kpi.active').data('value'));
    }, 20000);

    function loadChartKpi(dataChart = {}) {
        Highcharts.chart('container_kpi', {
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
                text: 'Tổng quan KPI'
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

    let autoLoopKpiIndex = 0;
    const autoLoopKpi = [
        '-1'
    ];

    <?php if(!empty($dtDepartment)){ ?>
    <?php foreach ($dtDepartment as $key => $value){ ?>
    autoLoopKpi.push('<?= $value['departmentid'] ?>');
    <?php } ?>
    <?php } ?>
    let autoLoopTimerKpi = null;
    var timeKpi = '<?= get_option('time_dashboard_srceen') ? get_option('time_dashboard_srceen') : 30 ?>';
    var timeKpi = 5;

    function startAutoLoopKpi(reset = false) {
        if (reset) {
            autoLoopKpiIndex = -1;
        }
        if (autoLoopKpiIndex) clearTimeout(autoLoopTimerKpi);

        function nextKpi() {
            autoLoopKpiIndex = (autoLoopKpiIndex + 1) % autoLoopKpi.length;
            autoLoopTimerKpi = setTimeout(nextKpi,  timeKpi * 60 * 1000); // 10 phút
            changeFilterKpi($('.child-kpi[data-value="' + autoLoopKpi[autoLoopKpiIndex] + '"]'), autoLoopKpi[autoLoopKpiIndex]);
        }
        nextKpi();
    }
    function stopAutoLoopKpi() {
        if (autoLoopTimerKpi) {
            clearTimeout(autoLoopTimerKpi);
            autoLoopTimerKpi = null;
        }
    }
</script>