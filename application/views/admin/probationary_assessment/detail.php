<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .container {
        background-color: #fff;
        padding: 50px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        max-width: 900px;
    }

    .header-title {
        text-align: center;
        font-weight: 700;
        color: #2c3e50;
        margin-top: 0;
        text-transform: uppercase;
        font-size: 22px;
    }

    .sub-title {
        text-align: center;
        font-style: italic;
        color: #7f8c8d;
        margin-bottom: 30px;
    }

    .section-header {
        background-color: #337ab7;
        color: white;
        padding: 10px 15px;
        margin-top: 30px;
        margin-bottom: 15px;
        font-weight: bold;
        border-radius: 4px;
        font-size: 16px;
        text-transform: uppercase;
    }

    .table>thead>tr>th {
        background-color: #ecf0f1 !important;
        vertical-align: middle !important;
        text-align: center;
        font-weight: 600;
        border: 1px solid #ddd !important;
    }

    .table>tbody>tr>td {
        vertical-align: middle !important;
    }

    .input-score {
        text-align: center;
        font-weight: bold;
    }

    .total-row {
        background-color: #f9f9f9;
        font-weight: bold;
        font-size: 15px;
    }

    .result-card {
        border: 2px solid #ddd;
        padding: 20px;
        text-align: center;
        border-radius: 8px;
        /* margin-top: 20px; Removed margin top to align with table */
        height: 100%;
        transition: all 0.3s;
    }

    .result-score {
        font-size: 36px;
        font-weight: bold;
        display: block;
        margin: 10px 0;
    }

    .result-status {
        font-size: 24px;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Status Colors */
    .status-pass {
        background-color: #dff0d8;
        border-color: #d6e9c6;
        color: #3c763d;
    }

    .status-extend {
        background-color: #fcf8e3;
        border-color: #faebcc;
        color: #8a6d3b;
    }

    .status-fail {
        background-color: #f2dede;
        border-color: #ebccd1;
        color: #a94442;
    }

    /* Gate Alert */
    #gate-alert {
        display: none;
        margin-bottom: 15px;
        animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .signature-box {
        margin-top: 50px;
        border-top: 1px solid #eee;
        padding-top: 20px;
    }

    .bg-info {
        background-color: #d9edf7;
    }

    table.table {
        margin-top: unset !important;
    }

    .radio label::before {
        width: 0 !important;
        border: unset !important;
    }

    .radio input[type=radio] {
        opacity: 1 !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<?php echo form_open(
    'admin/probationary_assessment/detail/' . $id . '',
    array('id' => 'evaluation_employee', 'class' => 'form-horizontal')
); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <!-- Header -->
            <h1 class="header-title"><?php
                                        if ($type == 1) {
                                            echo "PHIẾU YÊU CẦU ĐÁNH GIÁ THỬ VIỆC<br>NÂNG BẬC THEO THANG SỰ NGHIỆP";
                                        } else {
                                            echo "PHIẾU YÊU CẦU ĐÁNH GIÁ";
                                        }
                                        ?></h1>
            <p class="sub-title">(Theo SOP – KPI – Audit – BCKPH – Thang sự nghiệp)</p>

            <div class="well well-sm">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Mã phiếu:</label>
                    <div class="col-sm-10">
                        <input name="code" id="code" class="form-control" readonly value="<?= !empty($dtData) ? $dtData['code'] : $code ?>"
                            style="width: 100%;" />
                        <input type="hidden" name="type" id="type" class="form-control" value="<?= $type ?>"
                            style="width: 100%;" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Họ tên:</label>
                    <div class="col-sm-4">
                        <input name="staff_id" id="staff_id" class="" data-placeholder="<?= lang('Nhân viên') ?>" value="<?= !empty($dtData) ? $dtData['staff_id'] : ($dtHr['id'] ?? '') ?>"
                            style="width: 100%;" />
                    </div>
                    <label class="col-sm-2 control-label">Vị trí:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control role" name="role" readonly value="<?= $dtData['name_role'] ?? '' ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Phòng ban:</label>
                    <div class="col-sm-4"><input type="text" class="form-control room" name="room" readonly value="<?= $dtData['name_room'] ?? '' ?>"></div>
                    <label class="col-sm-2 control-label">Level mục tiêu:</label>
                    <div class="col-sm-4" style="padding-top: 7px;">
                        <?php foreach ($levelChecklist as $key => $value) { ?>
                            <label class="radio-inline"><input <?= !empty($dtData) && $dtData['level_target'] == $value['id'] ? 'checked' : '' ?> type="radio" name="level_target" value="<?= $value['id'] ?>"> <?= $value['code'] ?></label>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Thử việc từ:</label>
                    <div class="col-sm-4"><input type="text" class="form-control datepicker date_start" name="date_start" autocomplete="off" value="<?= !empty($dtData) ? _dhau($dtData['date_start']) : '' ?>"></div>
                    <label class="col-sm-2 control-label">Đến ngày:</label>
                    <div class="col-sm-4"><input type="text" class="form-control datepicker date_end" name="date_end" autocomplete="off" value="<?= !empty($dtData) ? _dhau($dtData['date_end']) : '' ?>"></div>
                </div>
            </div>

            <!-- Section A: GATE -->
            <div class="section-header">A. <?= getTypeCheckList('A')['name'] ?? '' ?></div>

            <div class="alert alert-danger" id="gate-alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <strong>CẢNH BÁO:</strong> Bạn có tiêu chí chọn "NO". Kết luận mặc định: <strong>KHÔNG ĐẠT</strong>.
            </div>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Điều kiện bắt buộc</th>
                        <th width="80">YES</th>
                        <th width="80">NO</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($checkList['A'])) { ?>
                        <?php foreach ($checkList['A'] as $key => $value) { ?>
                            <?php
                            $saved = $checkListItems['A'][$value['id']] ?? null;
                            $gate  = $saved['gate'] ?? '';
                            $note  = $saved['note'] ?? '';
                            ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td class="text-center"><input type="radio" <?= $gate != '' && $gate == 1 ? 'checked' : '' ?> name="gate[<?= $value['id'] ?>]" value="1" class="gate-check"></td>
                                <td class="text-center"><input type="radio" <?= $gate != '' && $gate == 0 ? 'checked' : '' ?> name="gate[<?= $value['id'] ?>]" value="0" class="gate-check"></td>
                                <td><input type="text" name="note_a[<?= $value['id'] ?>]" class="form-control note_a input-sm" value="<?= $note ?>"></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Section B: KPI -->
            <div class="section-header">B. <?= getTypeCheckList('B')['name'] ?? '' ?></div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tiêu chí</th>
                        <th width="150">Chuẩn</th>
                        <th width="120">Thực tế (%)</th>
                        <th width="150">Điểm</th>
                    </tr>
                </thead>
                <tbody>

                    <?php $totalPointB = 0;
                    if (!empty($checkList['B'])) { ?>
                        <?php foreach ($checkList['B'] as $key => $value) {  ?>
                            <?php $dtOpera = getTypeOperation($value['operation']);
                            $saved = $checkListItems['B'][$value['id']] ?? null;
                            $percent_b = $saved['percent'] ?? '';
                            $point_b = $saved['point'] ?? '';

                            ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td><?= $dtOpera['name'] ?? '' ?> <?= $value['conditions'] ?> <?= $value['prefix'] ?></td>
                                <td><input type="number" name="percent_b[<?= $value['id'] ?>]" class="form-control input-sm text-center" value="<?= $percent_b ?>" placeholder="%"></td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" name="point_b[<?= $value['id'] ?>]" value="<?= $point_b ?>" class="form-control input-sm input-score calc-b" max="<?= $value['point'] ?>" placeholder="0">
                                        <span class="input-group-addon">/<?= $value['point'] ?></span>
                                    </div>
                                </td>
                            </tr>
                        <?php $totalPointB += $value['point'];
                        } ?>
                    <?php } ?>
                    <tr class="total-row">
                        <td colspan="3" class="text-right">Tổng điểm Phần B:</td>
                        <td class="text-center"><span id="total_b"><?= $dtData['point_b'] ?? 0 ?></span> / <?= $totalPointB ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- Section C: Compliance -->
            <div class="section-header">C. <?= getTypeCheckList('C')['name'] ?? '' ?></div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nội dung</th>
                        <th width="150">Chuẩn</th>
                        <th width="120">Thực tế</th>
                        <th width="150">Điểm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $totalPointC = 0;
                    if (!empty($checkList['C'])) { ?>
                        <?php foreach ($checkList['C'] as $key => $value) { ?>
                            <?php $dtOpera = getTypeOperation($value['operation']);
                            $saved = $checkListItems['C'][$value['id']] ?? null;
                            $percent_c = $saved['percent'] ?? '';
                            $point_c = $saved['point'] ?? '';

                            ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td><?= $dtOpera['name'] ?? '' ?> <?= $value['conditions'] ?> <?= $value['prefix'] ?></td>
                                <td><input type="text" name="percent_c[<?= $value['id'] ?>]" value="<?= $percent_c ?>" class="form-control input-sm text-center"></td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" name="point_c[<?= $value['id'] ?>]" value="<?= $point_c ?>" class="form-control input-sm input-score calc-c" max="<?= $value['point'] ?>" placeholder="0">
                                        <span class="input-group-addon">/<?= $value['point'] ?></span>
                                    </div>
                                </td>
                            </tr>
                        <?php $totalPointC += $value['point'];
                        } ?>
                    <?php } ?>
                    <tr class="total-row">
                        <td colspan="3" class="text-right">Tổng điểm Phần C:</td>
                        <td class="text-center"><span id="total_c"><?= $dtData['point_c'] ?? 0 ?></span> / <?= $totalPointC ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- Section D: Competency -->
            <div class="section-header">D. <?= getTypeCheckList('D')['name'] ?? '' ?></div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tiêu chí đánh giá</th>
                        <th width="150">Điểm (Max 5)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $totalPointD = 0;
                    if (!empty($checkList['D'])) { ?>
                        <?php foreach ($checkList['D'] as $key => $value) { ?>
                            <?php
                            $saved = $checkListItems['D'][$value['id']] ?? null;
                            $point_d = $saved['point'] ?? '';
                            ?>
                            <tr>
                                <td><?= $value['name'] ?></td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" name="point_d[<?= $value['id'] ?>]" value="<?= $point_d ?>" class="form-control input-sm input-score calc-d" max="<?= $value['point'] ?>" placeholder="0">
                                        <span class="input-group-addon">/<?= $value['point'] ?></span>
                                    </div>
                                </td>
                            </tr>
                        <?php $totalPointD += $value['point'];
                        } ?>
                    <?php } ?>
                    <tr class="total-row">
                        <td class="text-right">Tổng điểm Phần D:</td>
                        <td class="text-center"><span id="total_d"><?= $dtData['point_d'] ?? 0 ?></span> / <?= $totalPointD ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- Section E: Career Path -->
            <div class="section-header">E. ĐỐI CHIẾU THANG SỰ NGHIỆP</div>
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered table-condensed table-striped" style="font-size: 13px;">
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Chuẩn bắt buộc</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($levelChecklist as $key => $value) { ?>
                                <tr>
                                    <td class="text-center text-bold"><?= $value['code'] ?></td>
                                    <td><?= $value['name'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body bg-info">
                            <p><strong>Level đạt được:</strong></p>
                            <?php foreach ($levelChecklist as $key => $value) { ?>
                                <div class="radio"><label><input type="radio" <?= !empty($dtData) && $dtData['level_achieved'] == $value['id'] ? 'checked' : '' ?> name="level_achieved" value="<?= $value['id'] ?>"> <?= $value['code'] ?></label></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section F: Summary & Conclusion -->
            <div class="section-header">F. TỔNG HỢP & KẾT LUẬN</div>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Hạng mục</th>
                                <th class="text-center">Điểm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= getTypeCheckList('B')['name_result'] ?? '' ?></td>
                                <td class="text-center"><span id="final_p2" style="font-weight:bold;"><?= $dtData['point_b'] ?? 0 ?></span> / <?= $totalPointB ?></td>
                            </tr>
                            <tr>
                                <td><?= getTypeCheckList('C')['name_result'] ?? '' ?></td>
                                <td class="text-center"><span id="final_c" style="font-weight:bold;"><?= $dtData['point_c'] ?? 0 ?></span> / <?= $totalPointC ?></td>
                            </tr>
                            <tr>
                                <td><?= getTypeCheckList('D')['name_result'] ?? '' ?></td>
                                <td class="text-center"><span id="final_d" style="font-weight:bold;"><?= $dtData['point_d'] ?? 0 ?></span> / <?= $totalPointD ?></td>
                            </tr>
                            <tr class="total-row" style="background-color: #d9edf7;">
                                <td>TỔNG CỘNG</td>
                                <td class="text-center" style="font-size: 1.5em; color: #31708f;"><span id="grand_total"><?= $dtData['point'] ?? 0 ?></span> / <?= $totalPointB + $totalPointC + $totalPointD ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="result-card" id="classification-box">
                        <div>KẾT QUẢ XẾP LOẠI</div>
                        <div class="result-status" id="classification-text" style="margin: 15px 0;"><?= $dtData['rating'] ?? '' ?></div>

                        <hr style="border-top: 1px dashed #ccc;">

                        <div class="text-left" style="padding-left: 20px;">
                            <p><strong>Đề xuất:</strong></p>
                            <?php foreach ($resultChecklist as $key => $value) { ?>
                                <?php
                                $htmlFail = '';
                                if ($value['check_fail_gate'] == 1) {
                                    $htmlFail = 'hoặc Fail Gate';
                                }
                                ?>
                                <div class="radio none-event">
                                    <label><input class="none-event" type="radio" <?= !empty($dtData) && $dtData['rating_list'] == $value['id'] ? 'checked' : '' ?> name="final_decision" value="<?= $value['id'] ?>" id="final_decision_<?= $value['id'] ?>"> <strong><?= $value['name'] ?></strong> (<?= $value['point_start'] . ' - ' . $value['point_end'] . ' ' . $htmlFail ?>)</label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row signature-box">
                <div class="col-xs-6 text-center">
                    <p><strong>NGƯỜI ĐÁNH GIÁ</strong></p>
                    <p style="margin-top: 50px;">(Ký & ghi rõ họ tên)</p>
                </div>
                <div class="col-xs-6 text-center">
                    <p><strong>TRƯỞNG BỘ PHẬN</strong></p>
                    <p style="margin-top: 50px;">(Ký & ghi rõ họ tên)</p>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <input type="hidden" name="add" id="" class="form-control" value="1">
                <input type="hidden" name="view_detail" id="" class="form-control" value="1">
                <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php //echo form_close(); 
?>
<?php init_tail(); ?>
<script>
    const resultChecklist = <?= json_encode($resultChecklist) ?>;
    $(document).ready(function() {

        _validate_form($('#evaluation_employee'), {
            code: "required",
            date_start: "required",
            date_end: "required",
            staff_id: "required",
        }, db);


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
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                })
                .done(function(data) {
                    console.log(data);
                    if (data.result) {
                        alert_float('success', data.message);
                        window.location.href = site.base_url + 'admin/probationary_assessment?type=' + data.type + '';
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

        init_datepicker();
        ajaxSelectCallBack('#staff_id', 'admin/personnel_assessment/searchStaffAndHrProfile', $("#staff_id").val());
        ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', $("#role_id").val(), true, true);
        // --- LOGIC 1: GATE CHECK ---
        $('.gate-check').change(function() {
            checkGate();
        });

        function checkGate() {
            let hasNo = false;
            $('input.gate-check[value="no"]').each(function() {
                if ($(this).is(':checked')) hasNo = true;
            });

            if (hasNo) {
                $('#gate-alert').slideDown();
                forceFail();
            } else {
                $('#gate-alert').slideUp();
                calculateTotal(); // Recalculate if Gate is cleared
            }
        }

        // --- LOGIC 2: CALCULATION ---
        // Listen for changes on any score input
        $('.calc-b, .calc-c, .calc-d').on('input keyup', function() {
            // Limit input to max value
            let max = parseInt($(this).attr('max'));
            let val = parseInt($(this).val());
            if (val > max) $(this).val(max);
            if (val < 0) $(this).val(0);

            calculateTotal();
        });

        function calculateTotal() {
            // 1. Check Gate first
            let hasNo = false;
            $('input.gate-check[value="no"]').each(function() {
                if ($(this).is(':checked')) hasNo = true;
            });
            if (hasNo) {
                forceFail();
                return;
            }

            // 2. Sum Section B
            let totalB = 0;
            $('.calc-b').each(function() {
                totalB += Number($(this).val()) || 0;
            });
            $('#total_b').text(totalB);
            $('#final_p2').text(totalB);

            // 3. Sum Section C
            let totalC = 0;
            $('.calc-c').each(function() {
                totalC += Number($(this).val()) || 0;
            });
            $('#total_c').text(totalC);
            $('#final_c').text(totalC);

            // 4. Sum Section D
            let totalD = 0;
            $('.calc-d').each(function() {
                totalD += Number($(this).val()) || 0;
            });
            $('#total_d').text(totalD);
            $('#final_d').text(totalD);

            // 5. Grand Total & Classification
            let grandTotal = totalB + totalC + totalD;
            $('#grand_total').text(grandTotal);

            updateClassification(grandTotal);
        }

        function updateClassification(score) {
            let box = $('#classification-box');
            let text = $('#classification-text');

            // Remove old classes
            box.removeClass('status-pass status-extend status-fail');
            $('input[name="final_decision"]').prop('checked', false);

            let matched = resultChecklist.find(item =>
                score >= item.point_start && score <= item.point_end
            );

            if (!matched) return;

            text.text(matched.name);

            const statusMap = {
                3: 'status-pass', // Ký HĐ
                2: 'status-extend', // Gia hạn
                1: 'status-fail' // Chấm dứt
            };

            if (statusMap[matched.id]) {
                box.addClass(statusMap[matched.id]);
                $(`#final_decision_${matched.id}`).prop('checked', true);
            }
        }

        function forceFail() {
            let box = $('#classification-box');
            let text = $('#classification-text');

            box.removeClass('status-pass status-extend').addClass('status-fail');
            text.text("KHÔNG ĐẠT (VI PHẠM GATE)");

            // Auto select Terminate
            $('input[name="final_decision"]').prop('checked', false);
            $('#final_decision_1').prop('checked', true);
        }

    });

    function ajaxSelectCallBack(element, url, id, text = '', types = '') {
        if (id != 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                initSelection: function(element, callback) {
                    if (id && text) {
                        callback({
                            id: id,
                            text: text
                        });
                    } else {
                        $.ajax({
                            type: "get",
                            async: false,
                            url: site.base_url + url + '/' + $(element).val() + '/' + 1,
                            dataType: "json",
                            success: function(data) {
                                callback(data.row);
                            }
                        });
                    }
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            types: types,
                            term: term,
                            type: 1,
                            type_staff: <?= $type == 1 ? 3 : 1 ?>,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            types: types,
                            term: term,
                            type: 1,
                            type_staff: <?= $type == 1 ? 3 : 1 ?>,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                }
            });
        }
    }

    $(document).on('change', '#staff_id', function(event) {
        event.preventDefault();
        var currentQuantityInput = $(event.currentTarget);
        data = $(currentQuantityInput).select2('data');

        $(".role").val(data.name_role);
        $(".room").val(data.name_room);
    });
</script>