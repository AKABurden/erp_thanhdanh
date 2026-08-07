<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<style>
    #wrapper {
        min-height: calc(100vh - 65px);
    }
    .page-wrapper {
        padding: 20px;
    }

    .card {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .panel-heading {
        background: #fff !important;
        border-bottom: 1px solid #eee;
        font-size: 16px;
    }

    .score {
        font-size: 48px;
        font-weight: bold;
        color: #2a5bd7;
    }

    .score-note {
        color: #999;
    }

    .m-t-20 {
        margin-top: 20px;
    }

    .recommend {
        margin-top: 15px;
        font-size: 14px;
    }

    /* Question */
    .question-block h4 {
        margin-top: 15px;
        font-weight: 600;
    }

    .answer {
        border: 1px solid #eee;
        border-radius: 6px;
        padding: 10px 15px;
        margin-bottom: 10px;
        position: relative;
        cursor: pointer;
    }

    .answer:hover {
        background: #f9fafc;
    }

    .answer.active {
        border-color: #337ab7;
        background: #f0f6ff;
    }

    .answer input {
        margin-right: 10px;
    }

    .answer .desc {
        display: block;
        color: #666;
        margin-left: 22px;
    }

    .point {
        position: absolute;
        right: 10px;
        top: 10px;
    }
    .form-group{
        border-bottom: 1px solid #eee;
        padding: 5px 10px;
    }
    .label-title{
        font-weight: 500 !important;
    }
    .panel-heading{
        background: #d2e9fd !important;
        border-radius: 10px;
    }
    .title-header{
    }
    .fixed-action-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #ffffff;
        border-top: 1px solid #ddd;
        padding: 10px 20px;
        z-index: 999;
        box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
    }

    .fixed-action-bar .btn {
        min-width: 120px;
    }

    .group{
        display: flex;
        align-items: center;
    }
    .score-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        text-align: center;
    }

    .score-header {
        text-align: left;
        font-size: 16px;
        color: #111827;
        margin-bottom: 15px;
    }

    .score-header .glyphicon {
        color: #22c55e;
        margin-right: 6px;
    }

    .score-value {
        font-size: 30px;
        font-weight: 700;
        color: #2563eb;
        line-height: 1;
    }

    .score-scale {
        color: #6b7280;
        margin-bottom: 15px;
    }

    .score-status {
        background: #ecfdf5;
        color: #15803d;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 10px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .score-action {
        text-align: left;
        font-size: 13px;
        color: #374151;
        margin-bottom: 15px;
    }

    .radar-wrapper {
        height: 220px;
    }
    .rule-list {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        padding: 15px;
        border-radius: 5px;
    }
    .rule-list li {
        margin-bottom: 10px;
    }
</style>

<?php echo form_open('admin/personnel_assessment/process_evaluate/' . $id . '',
    array('id' => 'process_evaluate')); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <div class="container-fluid page-wrapper">
                    <div class="row">

                        <!-- LEFT PANEL -->
                        <div class="col-md-4">

                            <!-- Chọn đối tượng -->
                            <div class="panel card">
                                <div class="panel-heading">
                                    <strong class="title-header">Thông tin chi tiết</strong>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group group">
                                        <div class="label-title">Mã đánh giá: </div>
                                        <div><?= $dtData['code'] ?></div>
                                    </div>
                                    <div class="form-group group">
                                        <div class="label-title">Loại đánh giá: </div>
                                        <div><?= $dtData['type'] == 1 ? 'Nhân viên' : 'Ứng viên' ?></div>
                                    </div>
                                    <div class="form-group group">
                                        <div class="label-title">Nhân viên/Ứng viên: </div>
                                        <div><?= $dtData['type'] == 1 ? $dtData['staff_name'] : $dtData['hr_name'] ?></div>
                                    </div>
                                    <div class="form-group group">
                                        <div class="label-title">Vị trí: </div>
                                        <div><?= $dtData['code_role'] ?></div>
                                    </div>
                                    <div class="form-group group">
                                        <div class="label-title">Vai trò cấp bậc: </div>
                                        <div><?= $dtData['code_role_level'] ?></div>
                                    </div>
                                    <div class="" style="margin-top:30px;margin-bottom: 20px">
                                        <div class="score-card">
                                            <!-- Header -->
                                            <div class="score-header">
                                                <span class="glyphicon glyphicon-stats"></span>
                                                <strong>Kết Quả Thời Thực</strong>
                                            </div>

                                            <!-- Score -->
                                            <div class="score-value total_point"><?= $dtData['point'] ?></div>
                                            <div class="score-scale">Thang điểm 5.0</div>

                                            <!-- Status -->
                                            <div class="score-status rating"><?= $dtData['rating'] ?></div>

                                            <!-- Action -->
                                            <div class="score-action">
                                                <strong>Hành động khuyến nghị:</strong>
                                                <div class="warning"><?= $dtData['warning'] ?></div>
                                            </div>

                                            <!-- Radar -->
                                            <div style="width:100%; height:240px;">
                                                <div id="radarHighchart" style="height: 240px"></div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="panel panel-default" style="padding: 5px;border-radius: 14px">
                                        <h4 style="margin-top: 10px;margin-left: 15px">Ghi chú xếp hạng</h4>
                                        <div class="panel-body">
                                            <ul id="rule-list" class="rule-list">
                                                <?php foreach ($dtRatingList as $k => $v) { ?>
                                                <li>⛔ <?= $v['point_start'] ?> -> <?= $v['point_end'] ?> điểm : <?= $v['rating'] ?>.</li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT PANEL -->
                        <div class="col-md-8">
                            <div class="panel card">
                                <div class="panel-heading clearfix">
                                    <strong class="title-header">Bảng Câu Hỏi Đánh Giá</strong>
                                    <span class="badge pull-right"><?= $countQuestion ?> câu hỏi</span>
                                </div>

                                <div class="panel-body">

                                    <!-- Question -->
                                    <?php if (!empty($dtDataQuestion)){ ?>
                                        <?php foreach ($dtDataQuestion as $key => $value){ ?>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="question-block">

                                                    <?php if (!empty($value)){ ?>
                                                        <?php foreach ($value as $k => $v){ ?>
                                                            <div class="question-title">
                                                                <span class="label label-default"><?= $key ?></span>
                                                            </div>
                                                            <input type="hidden" name="question[]" value="<?= $v['id'] ?>">
                                                            <input type="hidden" class="type_question" value="<?= $v['type'] ?>">
                                                            <h4><?= (++$k) ?>. <?= $v['question'] ?></h4>
                                                            <?php $dtDataQuestionAnswer = $v['dtDataQuestionAnswer']; ?>
                                                            <?php if(!empty($dtDataQuestionAnswer)){ ?>
                                                                <?php foreach ($dtDataQuestionAnswer as $kk => $vv){ ?>
                                                                    <div class="answer">
                                                                        <label>
                                                                            <input value="<?= $vv['id'] ?>" class="ans" onchange="changeAnswer(this)" data-weight="<?= $v['weight'] ?>" data-point="<?= $vv['point'] ?>" type="radio" name="answer[<?= $v['id'] ?>]" <?= $v['answer'] == $vv['prefix'] ? 'checked' : '' ?>>
                                                                            <strong>Phương án <?= $vv['prefix'] ?></strong>
                                                                            <span class="desc"><?= $vv['answer'] ?></span>
                                                                            <span class="point label label-success"><?= $vv['point'] ?> điểm</span>
                                                                        </label>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                    <!-- End Question -->

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fixed-action-bar text-center">
    <input type="hidden" name="check" value="1">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-check"></i> Xác nhận
    </button>
</div>

<?php echo form_close(); ?>
<?php init_tail(); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>

<script>
    var dtRatingList = <?= json_encode($dtRatingList) ?>;
    var dtTypeQuestion = <?= json_encode($dtTypeQuestion) ?>;
    calculatePoint();
    function changeAnswer(_this){
        calculatePoint();
    }
    function calculatePoint(){
        totalPoint = 0;
        var groupPoint = {};
        $('.ans:checked').each(function () {
            var point  = parseFloat($(this).data('point')) || 0;
            var weight = parseFloat($(this).data('weight')) || 1;
            type_question = $(this).closest('div.question-block').find('.type_question').val();

            totalPoint += (point * weight) / 100;
            totalPoint = parseFloat(totalPoint.toFixed(2));


            if (!groupPoint[type_question]) {
                groupPoint[type_question] = {
                    total: 0
                };
            }

            groupPoint[type_question].total += (point * weight) / 100;
        });
        $(".total_point").html(totalPoint);

        ratingList = null;
        var ratingList = null;
        $.each(dtRatingList, function (k, v) {
            if (totalPoint >= v.point_start && totalPoint < v.point_end) {
                ratingList = v;
                return false;
            }
        });
        if (ratingList) {
            $('.rating').text(ratingList.rating);
            $('.warning').text(ratingList.warning);
        }

        renderRadar(groupPoint);
    }
    _validate_form($('#process_evaluate'), {
    },db);

    function db(form) {

        $('.add').attr('disabled', 'disabled');
        var url = form.action;
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });

        $.ajax({
            url : url,
            type : 'POST',
            dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
            data: formData,
        })
            .done(function(data) {
                console.log(data);
                if (data.result) {
                    alert_float('success', data.message);
                    window.location.href = site.base_url+'admin/personnel_assessment/view/'+data.id+'';
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }

    function renderRadar(groupPoint) {

        var categories = [];
        var data = [];

        $.each(dtTypeQuestion, function (i, type) {
            categories.push(type.name);

            var value = groupPoint[type.id]
                ? groupPoint[type.id].total
                : 0;

            data.push(parseFloat(value.toFixed(2)));
        });

        Highcharts.chart('radarHighchart', {
            chart: {
                polar: true,
                type: 'area'
            },
            title: { text: null },
            pane: { size: '75%' },
            xAxis: {
                categories: categories,
                tickmarkPlacement: 'on',
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                max: 5,
                tickInterval: 1,
                gridLineInterpolation: 'polygon',
                labels: { enabled: false }
            },
            legend: { enabled: false },
            series: [{
                name: 'Năng lực',
                data: data,
                color: '#2563eb',
                fillColor: 'rgba(59,130,246,0.4)'
            }],
            credits: { enabled: false }
        });
    }


</script>