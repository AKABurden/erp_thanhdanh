<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<style>
    .width100 {
        width: 100px;
    }

    #tb-items td {
        white-space: nowrap !important;
    }

    #tb-items td input[type="text"] {
        min-width: 100px !important;
    }
</style>
<?php echo form_open('admin/work_plan/handling/' . $id, array('id' => 'work_plan_handling')); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a onclick="export_excel()" class="btn btn-info H_action_button">
                    <?php echo _l('Xuất excel'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <?= lang('month', 'month') ?>
                    <select name="month" id="month" class="month" data-placeholder="<?= lang('month') ?>" style="width: 100%;" required>
                        <?php foreach (getMonth() as $key => $value) : ?>
                            <option <?= !empty($work_plan) && $work_plan['month'] == $key ? 'selected' : (date('m') == $key ? 'selected' : '') ?> value="<?= $key ?>"><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?= lang('year', 'year') ?>
                    <select name="year" id="year" data-placeholder="<?= lang('year') ?>" style="width: 100%;" required>
                        <?php
                        $data = date('Y');
                        for ($i = $data - 5; $i <= $data + 5; $i++) {
                        ?>
                            <option value="<?= $i ?>" <?= !empty($work_plan) && $work_plan['year'] == $i ? 'selected' : (($i == $data) ? 'selected' : '') ?>><?= $i ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?= lang('Lọc qui trình', 'filter_process') ?>
                    <select name="filter_process" onchange="totalWorkPlan()" data-placeholder="<?= lang('Lọc qui trình') ?>" id="filter_process" style="width: 100%;">
                        <option value=""></option>
                        <?php if ($process_work_plan) : ?>
                            <?php foreach ($process_work_plan as $kP => $vP) : ?>
                                <?php echo '<option value="' . $kP . '">' . $vP['name'] . '</option>' ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= lang('tnh_content', 'content') ?>
                    <input type="text" name="content" id="content" class="form-control content" value="<?= !empty($work_plan) ? $work_plan['content'] : '' ?>" placeholder="<?= lang('tnh_content') ?>" required="required">
                </div>
            </div>
            <div class="col-md-4 mtop30">
                <button type="button" class="btn btn-info btn-info importTable"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button>
                <a href="<?= base_url('uploads/template/Mau_ke_hoach_cong_viec_v3.xlsx?vs=6') ?>"><?= lang('Download Mẫu...') ?></a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" name="id" id="id" class="form-control" value="0">
                <div class="table-responsive">
                    <table id="tb-items" class="table table-hover table-bordered dataTable">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <a onclick="addTaskRow()" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                            <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                        </svg>
                                    </a>
                                </th>
                                <th class="text-center" style=""><?= lang('Mã Công Việc') ?></th>
                                <th class="text-center" style=""><?= lang('Tên Công Việc') ?></th>
                                <th class="text-center" style=""><?= lang('Ngày Bắt Đầu Công Việc') ?></th>
                                <th class="text-center" style=""><?= lang('Ngày Kết Thúc Công Việc') ?></th>
                                <th class="text-center" style=""><?= lang('Ngày nhắc việc') ?></th>
                                <th class="text-center" style=""><?= lang('Chi Nhánh') ?></th>
                                <th class="text-center" style=""><?= lang('Bộ Phận') ?></th>
                                <th class="text-center" style=""><?= lang('Nội Dung') ?></th>
                                <th class="text-center" style=""><?= lang('Người Giao Việc') ?></th>
                                <th class="text-center" style=""><?= lang('Người Được Phân Công') ?></th>
                                <th class="text-center" style=""><?= lang('Người Giám Sát') ?></th>
                                <th class="text-center" style=""><?= lang('Qui Trình') ?></th>
                                <th class="text-center" style=""><?= lang('Nhân viên theo quy trình') ?></th>
                                <th class="text-center" style="width: 80px"><?= lang('Tuần 1') ?></th>
                                <th class="text-center" style="width: 80px"><?= lang('Tuần 2') ?></th>
                                <th class="text-center" style="width: 80px"><?= lang('Tuần 3') ?></th>
                                <th class="text-center" style="width: 80px"><?= lang('Tuần 4') ?></th>
                                <!-- <th class="text-center" style="width: 100px;"><?= lang('Mức Độ Ưu Tiên') ?></th>
                                <th class="text-center" style="width: 150px; max-width: 150px;"><?= lang('Người phụ trách') ?></th>
                                <th class="text-center" style="width: 150px; max-width: 150px;"><?= lang('Người giám sát - Báo cáo') ?></th> -->
                                <th class="text-center" style=""><?= lang('Đạt/Không Đạt') ?></th>
                                <th class="text-center" style=""><?= lang('Điểm KPI') ?></th>
                                <th class="text-center" style=""><?= lang('Đã Có/Chưa Quy Trình') ?></th>
                                <th class="text-center" style=""><?= lang('QR') ?></th>
                                <th class="text-center" style=""><i class="fa fa-trash-o"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php if ($this->perEditWorkPlan) : ?>
        <div class="row">
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <input type="hidden" name="add" id="" class="form-control" value="1">
                <button type="submit" class="btn btn-info add-work-plan">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php echo form_close(); ?>
<div class="hide">
    <input type="file" id="file_import_table" name="file_import_table">
</div>
<div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top: 10%; transform: translateY(10%)">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('manage_service_category'); ?>
                </h4>
            </div> -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h3 class="red">Hệ thống đang import và tạo phiếu công việc!
                            <br>Vui lòng chờ trong giây lát.
                        </h3>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-footer">
                <button type="submit" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div> -->
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript">
    var token = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var process_work_plan = <?= !empty($process_work_plan) ? json_encode($process_work_plan) : '{}' ?>;
    var staffs = <?= !empty($staffs) ? json_encode($staffs) : '{}' ?>;
</script>
<script>
    function getProcessWorkPlan() {
        var options = `<option></option>`;
        $.each(process_work_plan, function(index, value) {
            options += `<option data-content="<span style=\'color: ${value.color};\'>${value.name}</span>" value="${index}">${value.name}</option>`;
        });
        return options;
    }

    function getStaffs() {
        var options = `<option></option>`;
        $.each(staffs, function(index, value) {
            options += `<option value="${value.staffid}">${value.fullname}</option>`;
        });
        return options;
    }

    function totalWorkPlan() {
        var tb = '#tb-items tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;

        var _filter_process = $('#filter_process').val();

        for (ii = 0; ii < n; ii++) {
            // stt++;
            var element = $(tb)[ii];
            if ($(element).hasClass('tr-group')) {
                stt++;
            }
            $(element).find('.td-numbers').html(stt);
            $(element).find('.number').val(stt);
            _process_cur = $(element).find('select.process').val();
            if (_filter_process != '' && !$(element).hasClass('tr-group')) {
                if (_filter_process != _process_cur && _process_cur != '') {
                    $(element).hide();
                } else {
                    $(element).show();
                }
            } else {
                $(element).show();
            }
        }
    }

    function loadWorkPlan() {
        var dataPOST = {};
        var month = $('#month').val();
        var year = $('#year').val();
        dataPOST[token] = hash;
        dataPOST['month'] = month;
        dataPOST['year'] = year;

        if (!month || !year) {
            alert_float('danger', 'Vui lòng chọn tháng năm');
            return;
        }

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/work_plan/loadWorkPlan',
            data: dataPOST,
            dataType: 'html',
            success: function(response) {
                $('#tb-items tbody').html(response);
            }
        });

    }

    function removeItemWorkPlan(_this) {
        var thisTr = $(_this).closest('tr');
        rowId = thisTr.data('id');
        // console.log(rowId);
        var thisTr = thisTr.remove();
        $('.row-' + rowId).remove();
        totalWorkPlan();
    }

    function addItemWorkPlan(_this, _value, rowId) {
        var cTr = $(_this).closest('tr');

        _value.week_one = (_value.week_one !== undefined && _value.week_one !== null) ? _value.week_one : '';
        _value.week_two = (_value.week_two !== undefined && _value.week_two !== null) ? _value.week_two : '';
        _value.week_three = (_value.week_three !== undefined && _value.week_three !== null) ? _value.week_three : '';
        _value.week_four = (_value.week_four !== undefined && _value.week_four !== null) ? _value.week_four : '';
        _value.week_two = (_value.week_two !== undefined && _value.week_two !== null) ? _value.week_two : '';
        if (_value && (typeof _value.staff_id == 'undefined' || _value.staff_id == null)) {
            _value.staff_id = '';
        }
        if (_value && (typeof _value.text_staff_id == 'undefined' || _value.text_staff_id == null)) {
            _value.text_staff_id = '';
        }
        var pass_check = (_value.pass_status !== undefined && _value.pass_status !== null && _value.pass_status == 1) ? 'checked' : '';
        var fail_check = (_value.pass_status !== undefined && _value.pass_status !== null && _value.pass_status == 0) ? 'checked' : '';

        var kpi_plus = (_value.kpi_plus !== undefined && _value.kpi_plus !== null) ? _value.kpi_plus : 0;
        var kpi_minus = (_value.kpi_minus !== undefined && _value.kpi_minus !== null) ? _value.kpi_minus : 0;
        var kpi_val = (_value.kpi !== undefined && _value.kpi !== null) ? _value.kpi.toString() : '';
        var kpi_type = (_value.kpi_type !== undefined && _value.kpi_type !== null) ? _value.kpi_type.toString() : '';

        var prefix = '';
        var absVal = '0';

        if (kpi_type === '1') {
            prefix = '+';
            absVal = kpi_val;
        } else if (kpi_type === '2') {
            prefix = '-';
            absVal = kpi_val;
        } else {
            if (_value.pass_status !== undefined && _value.pass_status !== null && _value.pass_status !== '') {
                prefix = _value.pass_status == 1 ? '+' : '-';
                absVal = kpi_val !== '' ? kpi_val : (_value.pass_status == 1 ? kpi_plus : kpi_minus);
                kpi_type = _value.pass_status == 1 ? '1' : '2';
            } else {
                prefix = '';
                absVal = kpi_val !== '' ? kpi_val : '0';
                kpi_type = '';
            }
        }

        var have_qt = (_value.problem !== undefined && _value.problem !== null && _value.problem == 'have_qt') ? 'selected' : '';
        var no_qt = (_value.problem !== undefined && _value.problem !== null && _value.problem == 'not_qt') ? 'selected' : '';

        var tdNumber = `<td class="text-center "></td>`;
        var tdName = `<td colspan="11">
            <input type="hidden" name="items[${rowId}][${counter}][category_tasks_process_name]" class="form-control" value="${_value.process}">
            <input type="hidden" name="items[${rowId}][${counter}][process]" class="form-control" value="${_value.process_id}">
        </td>`;
        var tbstaff_id = `
        <td style="max-width: 150px !important;">
            <input name="items[${rowId}][${counter}][staff_id]" type="hidden" class="form-control" value="${_value.staff_id}">${_value.text_staff_id}
        </td>`;
        var tdWeekOne = `<td>
            <input type="text" name="items[${rowId}][${counter}][week_one]" class="form-control week_one" placeholder="<?= lang('Tuần 1') ?>" value="${_value.week_one}">
        </td>`;
        var tdWeekTwo = `<td>
            <input type="text" name="items[${rowId}][${counter}][week_two]" class="form-control week_two" placeholder="<?= lang('Tuần 2') ?>" value="${_value.week_two}">
        </td>`;
        var tdWeekThree = `<td>
            <input type="text" name="items[${rowId}][${counter}][week_three]" class="form-control week_three" placeholder="<?= lang('Tuần 3') ?>" value="${_value.week_three}">
        </td>`;
        var tdWeekFour = `<td>
            <input type="text" name="items[${rowId}][${counter}][week_four]" class="form-control week_four" placeholder="<?= lang('Tuần 4') ?>" value="${_value.week_four}">
        </td>`;

        var tdProcess = `<td style="max-width: 150px !important;">
            ${_value.process}
        </td>`;
        var tdResult = `<td class="text-left">
            <input class="radio-pass" type="radio" id="past_${counter}" name="items[${rowId}][${counter}][pass_status]" value="1" data-kpi-plus="${kpi_plus}" ${pass_check}>
            <label for="past_${counter}">Đạt</label>
            <br>
            <input class="radio-fail" type="radio" id="fail_${counter}" name="items[${rowId}][${counter}][pass_status]" value="0" data-kpi-minus="${kpi_minus}" ${fail_check}>
            <label for="fail_${counter}">Không đạt</label>
        </td>`;
        var tdKpi = `<td class="text-center" style="white-space: nowrap !important;">
            <div style="white-space: nowrap;">
                <span class="kpi-prefix" style="font-weight: bold; margin-right: 5px; display: inline-block; vertical-align: middle; width: 12px; text-align: center;">${prefix}</span>
                <input type="text" name="items[${rowId}][${counter}][kpi]" class="form-control kpi-num-input" value="${absVal}" style="width: 70px; display: inline-block; text-align: center; vertical-align: middle;">
                <input type="hidden" name="items[${rowId}][${counter}][kpi_type]" class="kpi-type-input" value="${kpi_type}">
            </div>
        </td>`;
        var tdProblem = `<td class="text-center">
            <div class="">
                <select name="items[${rowId}][${counter}][problem]" class="form-control selectpicker" style="">
                    <option value=""></option>
                    <option value="have_qt" ${have_qt}>Đã có QT</option>
                    <option value="not_qt" ${no_qt}>Chưa có QT</option>
                </select>
            </div>
        </td>`;

        var trItem = `<tr class="row-${rowId}">
            ${tdNumber}
            ${tdName}
            ${tdProcess}
            ${tbstaff_id}
            ${tdWeekOne}
            ${tdWeekTwo}
            ${tdWeekThree}
            ${tdWeekFour}
            ${tdResult}
            ${tdKpi}
            ${tdProblem}
        </tr>`;
        cTr.after(trItem);
        counter++;
        totalWorkPlan();
        init_selectpicker();
    }

    $(document).ready(function() {
        $('#month').select2();
        $('#year').select2();
        $('#filter_process').select2({
            'allowClear': true
        });
        init_selectpicker();

        $(document).on('change', '#month, #year', function(event) {
            loadWorkPlan();
        });

        loadWorkPlan();

        // Dynamically update KPI value when toggling Achieved/Not Achieved status
        $(document).on('change', 'input[name^="items"][name$="[pass_status]"]', function() {
            var thisTr = $(this).closest('tr');
            var passStatus = $(this).val();
            var kpiPlus = parseFloat(thisTr.find('.radio-pass').data('kpi-plus')) || 0;
            var kpiMinus = parseFloat(thisTr.find('.radio-fail').data('kpi-minus')) || 0;
            var prefix = passStatus == 1 ? '+' : '-';
            var absVal = passStatus == 1 ? kpiPlus : kpiMinus;
            var kpiType = passStatus == 1 ? 1 : 2;
            
            thisTr.find('.kpi-prefix').text(prefix);
            thisTr.find('.kpi-num-input').val(absVal);
            thisTr.find('.kpi-type-input').val(kpiType);
        });

        appValidateForm($('#work_plan_handling'), {
            month: 'required',
            year: 'required',
            content: 'required',
        }, db);

        function db(form) {
            // $('#loading_modal').modal('show');
            $('#loading_modal').modal({
                backdrop: 'static', // Ngăn modal đóng khi click ra ngoài
                keyboard: false // Ngăn modal đóng khi nhấn phím ESC
            });

            $('.add-work-plan').attr('disabled', 'disabled');
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
                    if (data.result) {
                        alert_float('success', data.message);
                        // window.location.href = site.base_url + 'admin/work_plan';
                        $('.add-work-plan').removeAttr('disabled', 'disabled');
                        loadWorkPlan();
                        $('#loading_modal').modal('hide');
                        // setTimeout(function() {
                        // }, 2000)
                    } else {
                        alert_float('danger', data.message);
                        $('.add-work-plan').removeAttr('disabled', 'disabled');
                        $('#loading_modal').modal('hide');
                    }
                })
                .fail(function() {
                    alert_float('danger', lang_core['errors']);
                    $('.add-work-plan').removeAttr('disabled', 'disabled');
                    $('#loading_modal').modal('hide');
                });

            return false;
        }
    });
</script>

<script>
    $('body').on('click', '.importTable', function() {
        console.log($(this).data('id'))
        $('#file_import_table').attr('data-id', $(this).data('id'));
        $('#file_import_table').click();
    })

    $('#file_import_table').change(function() {
        var id = $(this).attr('data-id');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var filePath = $('#file_import_table').val(); //lấy giá trị input theo id
        if (filePath != "") {
            var allowedExtensions = /(\.XLSX|\.XLS)$/i; //các tập tin cho phép
            //Kiểm tra định dạng
            if (!allowedExtensions.exec(filePath)) {
                alert('Vui lòng upload các file có định dạng: .XLSX/.XLS only.');
                $('#file_import_table').val('');
                return false;
            }
        } else {
            alert_float('danger', 'Không tìm thấy file import');
            return false;
        }
        var url = admin_url + 'work_plan/import_data_table';
        var file_data = $('#file_import_table').prop('files')[0];
        var nameFile = $('#file_import_table').attr('name');
        var form_data = new FormData();
        form_data.append(nameFile, file_data);
        form_data.append(csrfData['token_name'], csrfData['hash']);
        console.log(id);
        $.ajax({
            url: url,
            type: 'POST',
            contentType: false,
            cache: false,
            processData: false,
            data: form_data,
            success: function(data) {
                $('#file_import_table').val('');
                data = JSON.parse(data);
                $.each(data, function(index, value) {
                    // if(!$.isNumeric(value.id)) {
                    //     value.id = id;
                    // }
                    // addItemWorkPlanVal($(`.importTable[data-id="${value.id}"]`), value)
                    rowIndex = addTaskRow(value);
                    var _thisTr = $('.tr-group[data-id="' + rowIndex + '"]');
                    if (value.process) {
                        $.each(value.process, function(indexItem, valueItem) {
                            addItemWorkPlan(_thisTr, valueItem, rowIndex);
                        });
                    }

                })
                return false;
            }
        });
        return false;
    })

    function addItemWorkPlanVal(_this, _value) {
        var cTr = $(_this).closest('tr');

        var tdNumber = `<td class="text-center td-numbers"></td>`;
        var tdName = `<td>
            <input type="hidden" name="items[${counter}][type]" class="form-control type" value="${_value.id}">
            <input type="hidden" name="items[${counter}][process]" class="form-control process" value="${_value.process_id}">
            <input type="hidden" name="items[${counter}][number]" class="form-control number" value="">
            <input type="text" name="items[${counter}][name]" class="form-control name" placeholder="<?= lang('Tên') ?>" value="${_value.name}">
        </td>`;
        var tbstaff_id = `
        <td style="max-width: 150px !important;">
            <select name="items[${rowId}][${counter}][staff_id]" class="form-control selectpicker" data-live-search="true" data-none-selected-text="<?= lang('Nhân viên theo quy trình') ?>">
                ${getStaffs()}
            </select>
        </td>`;
        var tdWeekOne = `<td>
            <input type="text" name="items[${counter}][week_one]" class="form-control week_one" placeholder="<?= lang('Tuần 1') ?>" value="${_value.week_one}">
        </td>`;
        var tdWeekTwo = `<td>
            <input type="text" name="items[${counter}][week_two]" class="form-control week_two" placeholder="<?= lang('Tuần 2') ?>" value="${_value.week_two}">
        </td>`;
        var tdWeekThree = `<td>
            <input type="text" name="items[${counter}][week_three]" class="form-control week_three" placeholder="<?= lang('Tuần 3') ?>" value="${_value.week_three}">
        </td>`;
        var tdWeekFour = `<td>
            <input type="text" name="items[${counter}][week_four]" class="form-control week_four" placeholder="<?= lang('Tuần 4') ?>" value="${_value.week_four}">
        </td>`;
        var tdPriorityLevel = `<td>
            <input type="text" name="items[${counter}][priority_level]" class="form-control priority_level number-format" placeholder="<?= lang('Mức độ ưu tiên') ?>" value="${_value.priority_level}">
        </td>`;

        var tdProcess = `<td style="max-width: 150px !important;">
            <select name="items[${counter}][process]" class="form-control selectpicker process" data-live-search="true" data-none-selected-text="<?= lang('Quy trình') ?>">
                ${getProcessWorkPlan()}
            </select>
        </td>`;
        var tdStaffs = `<td style="max-width: 150px !important;">
            <select name="items[${counter}][staffs][]" class="form-control selectpicker" data-live-search="true" data-none-selected-text="<?= lang('Người phụ trách') ?>" multiple>
                ${getStaffs()}
            </select>
        </td>`;
        var tdManageReports = `<td style="max-width: 150px !important;">
            <select name="items[${counter}][manage_reports][]" class="form-control selectpicker" data-live-search="true" data-none-selected-text="<?= lang('Người giám sát - báo cáo') ?>" multiple>
                ${getStaffs()}
            </select>
        </td>`;
        var tdActions = `<td class="text-danger text-center">
            <i onclick="removeItemWorkPlan(this)" class="fa fa-remove" style="cursor: pointer;"></i>
        </td>`;

        var trItem = `<tr>
            ${tdNumber}
            ${tdName}
            ${tbstaff_id}
            ${tdWeekOne}
            ${tdWeekTwo}
            ${tdWeekThree}
            ${tdWeekFour}
            ${tdPriorityLevel}
            ${tdProcess}
            ${tdStaffs}
            ${tdManageReports}
            ${tdActions}
        </tr>`;
        cTr.after(trItem);
        $(`[name="items[${counter}][process]"]`).val(_value.process);
        $(`[name="items[${counter}][staffs][]"]`).val(_value.staffs);
        $(`[name="items[${counter}][manage_reports][]"]`).val(_value.manage_reports);
        console.log(_value.staffs);
        counter++;
        totalWorkPlan();
        init_selectpicker();
    }

    $(document).on('change', '.category_task', function() {
        var category_task_id = $(this).val();
        loadCategoryTaskData(category_task_id, this);
    });

    function loadCategoryTaskData(category_task_id, _this) {
        var thisTr = $(_this).closest('tr');
        var rowId = $(_this).data('id');
        console.log(rowId);
        if (rowId) {
            $('.row-' + rowId).remove();
        }

        if (category_task_id && rowId) {
            var dataPOST = {};
            dataPOST[token] = hash;
            $.ajax({
                url: site.base_url + 'admin/work_plan/getCategoryTaskData/' + category_task_id,
                method: 'GET',
                data: dataPOST,
                dataType: "json",
                success: function(data) {
                    thisTr.find('.task_name').html(data.content);
                    thisTr.find('.task_department').html(data.department);
                    thisTr.find('.task_content').html(data.content);
                    var processArray = data.process;
                    $.each(processArray.reverse(), function(index, value) {
                        var _value = {
                            'process': value.name,
                            'process_id': value.id,
                            'kpi_plus': value.kpi_plus,
                            'kpi_minus': value.kpi_minus
                        };
                        addItemWorkPlan(_this, _value, rowId);
                    });
                    // Xử lý dữ liệu nhận được từ máy chủ
                    // Ví dụ: Hiển thị danh sách công việc trong #task-list
                    // $('#task-list').html(data);
                    // console.log(data);
                },
                error: function(xhr, status, error) {
                    // Xử lý lỗi nếu có
                    console.error('Lỗi: ' + error);
                }
            });
        }
    }

    var mainItemIndex = 0;

    function addTaskRow(_value) {
        // var cTr = $(_this).closest('tr');
        // const d = new Date();
        // let randomId = d.getTime();
        mainItemIndex++;
        // console.log(_value);

        if (_value) {
            if (_value && (typeof _value.category_task == 'undefined' || _value.category_task == null)) {
                _value.category_task = '';
            } else {
                // console.log(_value.category_task);
            }
            if (_value && (typeof _value.branch == 'undefined' || _value.branch == null)) {
                _value.branch = '';
            }
            if (_value && (typeof _value.task_name == 'undefined' || _value.task_name == null)) {
                _value.task_name = '';
            }
            if (_value && (typeof _value.task_department == 'undefined' || _value.task_department == null)) {
                _value.task_department = '';
            }
            if (_value && (typeof _value.task_content == 'undefined' || _value.task_content == null)) {
                _value.task_content = '';
            }
            if (_value && (typeof _value.content == 'undefined' || _value.content == null)) {
                _value.content = '';
            }
            if (_value && (typeof _value.staff_assigner == 'undefined' || _value.staff_assigner == null)) {
                _value.staff_assigner = '';
            }
            if (_value && (typeof _value.staff_assigned == 'undefined' || _value.staff_assigned == null)) {
                _value.staff_assigned = '';
            }
            if (_value && (typeof _value.staff_assigner_id == 'undefined' || _value.staff_assigner_id == null)) {
                _value.staff_assigner_id = '';
            }
            if (_value && (typeof _value.staff_assigned_id == 'undefined' || _value.staff_assigned_id == null)) {
                _value.staff_assigned_id = '';
            }
            if (_value && (typeof _value.staff_monitor_id == 'undefined' || _value.staff_monitor_id == null)) {
                _value.staff_monitor_id = '';
            }
            if (_value && (typeof _value.date_start == 'undefined' || _value.date_start == null)) {
                _value.date_start = '';
            }
            if (_value && (typeof _value.date_end == 'undefined' || _value.date_end == null)) {
                _value.date_end = '';
            }
            if (_value && (typeof _value.date_tasks == 'undefined' || _value.date_tasks == null)) {
                _value.date_tasks = '';
            }
            if (_value && (typeof _value.process_id == 'undefined' || _value.process_id == null)) {
                _value.process_id = '';
            }
        } else {
            var _value = {
                category_task: "",
                branch: "",
                task_name: "",
                task_department: "",
                task_content: "",
                content: "",
                staff_assigner: "",
                staff_assigner_id: "",
                staff_assigned: "",
                staff_assigned_id: "",
                staff_monitor_id: "",
                staff_monitor: "",
                date_start: "",
                date_end: "",
                date_tasks: "",
                process_id: "",
            };
        }

        var date_limit_start = $('#year').val() + '-' + $('#month').val() + '-01';
        var dayLast = new Date($('#year').val(), $('#month').val(), 0).getDate();
        var date_limit_end = $('#year').val() + '-' + $('#month').val() + '-' + dayLast;

        var trNew = `<tr class="tr-group" style="background: #ddddddd1;" data-id="${mainItemIndex}">
            <td class="text-center td-numbers"></td>
            <td class="text-left"><?= render_select('main_item[${mainItemIndex}][category_task]', $arrCategoryTask, ['id', 'code'], '', '', ['data-id' => '${mainItemIndex}'], [], '', 'category_task') ?><br><span class="dropdown-toggle no_background label label-danger mtop10">Chưa tạo phiếu công việc</span></td>
            <td class="text-left task_name">${_value.task_name}</td>
            <td><input name="main_item[${mainItemIndex}][date_start]" data-lazy="false" data-date-min-date="${date_limit_start}"  data-date-max-date="${date_limit_end}" class="form-control datepicker width100" value="${_value.date_start}"></td>
            <td><input name="main_item[${mainItemIndex}][date_end]" class="form-control datepicker width100" value="${_value.date_end}"></td>
            <td><input name="main_item[${mainItemIndex}][date_tasks]" data-lazy="false" data-date-min-date="${date_limit_start}"  data-date-max-date="${date_limit_end}" class="form-control datepicker width100" value="${_value.date_tasks}"></td>
            <td class="text-left"><?= render_select('main_item[${mainItemIndex}][branch]', $arrBranch, ['id', 'name']), '', '' ?></td>
            <td class="text-left task_department">${_value.task_department}</td>
            <td class="text-left" style="min-width: 150px"><input name="main_item[${mainItemIndex}][content]" type="text" class="form-control" value="${_value.content}"></td>
            <td class="text-left"><input name="main_item[${mainItemIndex}][staff_assigner]" type="hidden" class="form-control" value="${_value.staff_assigner_id}">${_value.staff_assigner}</td>
            <td class="text-left"><input name="main_item[${mainItemIndex}][staff_assigned]" type="hidden" class="form-control" value="${_value.staff_assigned_id}">${_value.staff_assigned}</td>
            <td class="text-left"><input name="main_item[${mainItemIndex}][staff_monitor]" type="hidden" class="form-control" value="${_value.staff_monitor_id}">${_value.staff_monitor}</td>
            <td colspan="10" class="text-left"></td>
            <td class="text-danger text-center">
                <i onclick="removeItemWorkPlan(this)" class="fa fa-remove" style="cursor: pointer;"></i>
            </td>
        </tr>`;


        $('#tb-items tbody').append(trNew);
        init_selectpicker();
        init_datepicker();
        totalWorkPlan()
        if (_value.category_task) {
            var category_task_select = $('select[name="main_item[' + mainItemIndex + '][category_task]"]');
            category_task_select.val(_value.category_task);
            category_task_select.selectpicker('refresh');
            // category_task_select.change();
        }
        if (_value.branch) {
            var branch_select = $('select[name="main_item[' + mainItemIndex + '][branch]"]');
            branch_select.val(_value.branch);
            branch_select.selectpicker('refresh');
            branch_select.change();
        }
        return mainItemIndex;
    }


    function export_excel() {
        if ($('#id').val() != "") {
            // Tạo một form ẩn để gửi dữ liệu POST
            var form = document.createElement('form');
            form.style.display = 'none';
            form.method = 'POST';
            form.action = site.base_url + 'admin/work_plan/export_handling'; // Điều chỉnh URL đích

            var inputID = document.createElement("input");
            $(inputID).attr('name', 'id');
            $(inputID).val($('#id').val());
            form.appendChild(inputID);


            if (typeof(csrfData) !== 'undefined') {
                var inputHash = document.createElement("input");
                $(inputHash).attr('name', csrfData['token_name']);
                $(inputHash).val(csrfData['hash']);
                form.appendChild(inputHash);
            }
            // Thêm form ẩn vào body và submit nó
            document.body.appendChild(form);
            form.submit();
        } else {
            alert_float('danger', 'Vui lòng chọn phiếu cần xuất');
        }
    }
</script>