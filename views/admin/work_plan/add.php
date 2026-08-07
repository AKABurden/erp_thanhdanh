<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/work_plan/handling/'.$id, array('id' => 'work_plan_handling')); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <!-- <?//= $this->load->view('admin/breadcrumb') ?> -->
        </div>
    </div>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <?= lang('month', 'month') ?>
                    <select name="month" id="month" class="month" data-placeholder="<?= lang('month') ?>" style="width: 100%;" required>
                        <?php foreach (getMonth() as $key => $value) : ?>
                            <option <?=  !empty($work_plan) && $work_plan['month'] == $key ? 'selected' : (date('m') == $key ? 'selected' : '') ?> value="<?= $key ?>"><?= $value ?></option>
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
                        <?php if($process_work_plan): ?>
                            <?php foreach($process_work_plan as $kP => $vP): ?>
                                <?php echo '<option value="'.$kP.'">'.$vP['name'].'</option>' ?>
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
                <a href="<?=base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx')?>"><?= lang('Download Mẫu...') ?></a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" name="id" id="id" class="form-control" value="0">
                <table id="tb-items" class="table table-hover table-bordered dataTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;"><?= lang('#') ?></th>
                            <th class="text-center" style="width: 400px;"><?= lang('Tên') ?></th>
                            <th class="text-center" style="width: 100px;"><?= lang('Tuần 1') ?></th>
                            <th class="text-center" style="width: 100px;"><?= lang('Tuần 2') ?></th>
                            <th class="text-center" style="width: 100px;"><?= lang('Tuần 3') ?></th>
                            <th class="text-center" style="width: 100px;"><?= lang('Tuần 4') ?></th>
                            <th class="text-center" style="width: 100px;"><?= lang('Mức độ ưu tiên') ?></th>
                            <th class="text-center" style="width: 150px; max-width: 150px;"><?= lang('Qui trình') ?></th>
                            <th class="text-center" style="width: 150px; max-width: 150px;"><?= lang('Người phụ trách') ?></th>
                            <th class="text-center" style="width: 150px; max-width: 150px;"><?= lang('Người giám sát - Báo cáo') ?></th>
                            <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $counter = 0;
                            $trOne = '';
                            $trTwo = '';
                            $trThree = '';
                            $trFour = '';
                            if (!empty($work_plan)) {
                                $items = $this->work_plan_model->getWorkPlanItems($id);
                                foreach ($items as $key => $value) {
                                    $type = $value['type'];

                                    $tdNumber = '<td class="text-center td-numbers"></td>';
                                    $tdName = '<td>
                                        <input type="hidden" name="items['.$counter.'][type]" class="form-control type" value="'.$type.'">
                                        <input type="hidden" name="items['.$counter.'][work_plan_items_id]" class="form-control work_plan_items_id" value="'.$value['id'].'">
                                        <input type="text" name="items['.$counter.'][name]" class="form-control name" placeholder="'.lang('Tên').'" value="'.$value['name'].'">
                                    </td>';
                                    $tdWeekOne = '<td>
                                        <input type="text" name="items['.$counter.'][week_one]" class="form-control week_one" placeholder="'.lang('Tuần 1').'" value="'.$value['week_one'].'">
                                    </td>';
                                    $tdWeekTwo = '<td>
                                        <input type="text" name="items['.$counter.'][week_two]" class="form-control week_two" placeholder="'.lang('Tuần 2').'" value="'.$value['week_two'].'">
                                    </td>';
                                    $tdWeekThree = '<td>
                                        <input type="text" name="items['.$counter.'][week_three]" class="form-control week_three" placeholder="'.lang('Tuần 3').'" value="'.$value['week_three'].'">
                                    </td>';
                                    $tdWeekFour = '<td>
                                        <input type="text" name="items['.$counter.'][week_four]" class="form-control week_four" placeholder="'.lang('Tuần 4').'" value="'.$value['week_four'].'">
                                    </td>';
                                    $tdPriorityLevel = '<td>
                                        <input type="text" name="items['.$counter.'][priority_level]" class="form-control priority_level number-format" placeholder="'.lang('Mức độ ưu tiên').'" value="'.$value['priority_level'].'">
                                    </td>';

                                    $optionsProcess = '<option></option>';
                                    foreach ($process_work_plan as $kP => $vP) {
                                        $selected = $kP == $value['process'] ? 'selected' : '';
                                        $optionsProcess.= '<option data-content="<span style=\'color: '.$vP['color'].';\'>'.$vP['name'].'</span>" '.$selected.' value="'.$kP.'">'.$vP['name'].'</option>';
                                    }

                                    $tdProcess = '<td style="max-width: 150px !important;">
                                        <select name="items['.$counter.'][process]" class="form-control selectpicker process" data-live-search="true" data-none-selected-text="'.lang('Quy trình').'">
                                            '.$optionsProcess.'
                                        </select>
                                    </td>';

                                    $optionsStaff = '';
                                    $optionsManageReports = '';
                                    $dtWorkPlanItemsStaffs = $this->work_plan_model->getWorkPlanItemsStaffs($value['id'], 1);
                                    $dtWorkPlanItemsStaffsManage = $this->work_plan_model->getWorkPlanItemsStaffs($value['id'], 2);
                                    if (!empty($staffs)) {
                                        foreach ($staffs as $kS => $vS) {
                                            $selectedStaff = '';
                                            foreach ($dtWorkPlanItemsStaffs as $kWPS => $vWPS) {
                                                if ($vS['staffid'] == $vWPS['staff_id']) {
                                                    $selectedStaff = 'selected';
                                                    break;
                                                }
                                            }

                                            $selectedStaffManage = '';
                                            foreach ($dtWorkPlanItemsStaffsManage as $kWPS => $vWPS) {
                                                if ($vS['staffid'] == $vWPS['staff_id']) {
                                                    $selectedStaffManage = 'selected';
                                                    break;
                                                }
                                            }

                                            $optionsStaff.= '<option '.$selectedStaff.' value="'.$vS['staffid'].'">'.$vS['fullname'].'</option>';
                                            $optionsManageReports.= '<option '.$selectedStaffManage.' value="'.$vS['staffid'].'">'.$vS['fullname'].'</option>';
                                        }
                                    }
                                    
                                    $tdStaffs = '<td style="max-width: 150px !important;">
                                        <select name="items['.$counter.'][staffs][]" class="form-control selectpicker" data-live-search="true" data-none-selected-text="'.lang('Người phụ trách').'" multiple>
                                            '.$optionsStaff.'
                                        </select>
                                    </td>';
                                    $tdManageReports = '<td style="max-width: 150px !important;">
                                        <select name="items['.$counter.'][manage_reports][]" class="form-control selectpicker" data-live-search="true" data-none-selected-text="'.lang('Người giám sát - báo cáo').'" multiple>
                                            '.$optionsManageReports.'
                                        </select>
                                    </td>';
                                    $tdActions = '<td class="text-danger text-center">
                                        <i onclick="removeItemWorkPlan(this)" class="fa fa-remove" style="cursor: pointer;"></i>
                                    </td>';

                                    $trItem = '<tr>
                                        '.$tdNumber.'
                                        '.$tdName.'
                                        '.$tdWeekOne.'
                                        '.$tdWeekTwo.'
                                        '.$tdWeekThree.'
                                        '.$tdWeekFour.'
                                        '.$tdPriorityLevel.'
                                        '.$tdProcess.'
                                        '.$tdStaffs.'
                                        '.$tdManageReports.'
                                        '.$tdActions.'
                                    </tr>';
                                    $counter++;

                                    if ($type == 1) {
                                        $trOne.= $trItem;
                                    } else if ($type == 2) {
                                        $trTwo.= $trItem;
                                    } else if ($type == 3) {
                                        $trThree.= $trItem;
                                    } else if ($type == 4) {
                                        $trFour.= $trItem;
                                    }
                                }
                            }
                        ?>
                        <tr class="tr-group" style="background: #ddddddd1;">
                            <td class="text-center">
                                <a onclick="addItemWorkPlan(this, 1)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                        <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                    </svg>
                                </a>
                            </td>
                            <td class="text-left" colspan="8"><?= lang('KHỐI : VĂN PHÒNG ( Link Công Việc Khối)') ?></td>
                            <td class="text-right" colspan="1"><a class="hide" href="<?=base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx')?>"><?= lang('Download Mẫu...') ?></a></td>
                            <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="1"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>
                        </tr>
                        <?= $trOne ?>
                        <tr class="tr-group" style="background: #ddddddd1;">
                            <td class="text-center">
                                <a onclick="addItemWorkPlan(this, 2)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                        <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                    </svg>
                                </a>
                            </td>
                            <td class="text-left" colspan="8"><?= lang('HOÀN THÀNH Cập Nhật Full Thông Tin SP') ?></td>
                            <td class="text-right" colspan="1"><a class="hide" href="<?=base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx')?>"><?= lang('Download Mẫu...') ?></a></td>
                            <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="2"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>
                        </tr>
                        <?= $trTwo ?>
                        <tr class="tr-group" style="background: #ddddddd1;">
                            <td class="text-center">
                                <a onclick="addItemWorkPlan(this, 3)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                        <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                    </svg>
                                </a>
                            </td>
                            <td class="text-left" colspan="8"><?= lang('Qui Trình') ?></td>
                            <td class="text-right" colspan="1"><a class="hide" href="<?=base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx')?>"><?= lang('Download Mẫu...') ?></a></td>
                            <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="3"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>
                        </tr>
                        <?= $trThree ?>
                        <tr class="tr-group" style="background: #ddddddd1;">
                            <td class="text-center">
                                <a onclick="addItemWorkPlan(this, 4)" class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                        <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                    </svg>
                                </a>
                            </td>
                            <td class="text-left" colspan="8"><?= lang('Sản Xuất - Chất Lượng') ?></td>
                            <td class="text-right" colspan="1"><a class="hide" href="<?=base_url('uploads/template/Mau_ke_hoach_cong_viec.xlsx')?>"><?= lang('Download Mẫu...') ?></a></td>
                            <td><button type="button" class="hide btn btn-info btn-info btn-icon importTable" data-id="4"><i class="fa fa-upload" aria-hidden="true"></i> Import File Excel</button></td>
                        </tr>
                        <?= $trFour ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php if($this->perEditWorkPlan): ?>
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
</div>
<?php echo form_close(); ?>
<div class="hide">
    <input type="file" id="file_import_table" name="file_import_table">
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
    var counter = <?= $counter ?>;

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
            stt++;
            var element = $(tb)[ii];
            if ($(element).hasClass('tr-group')) {
                stt = 0;
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
            url: site.base_url+'admin/work_plan/loadWorkPlan',
            data: dataPOST,
            dataType: 'html',
            success: function (response) {
                $('#tb-items tbody').html(response);
            }
        });

    }

    function removeItemWorkPlan(_this) {
        $(_this).closest('tr').remove();
        totalWorkPlan();
    }

    function addItemWorkPlan(_this, _value) {
        var cTr = $(_this).closest('tr');

        var tdNumber = `<td class="text-center td-numbers"></td>`;
        var tdName = `<td>
            <input type="hidden" name="items[${counter}][type]" class="form-control type" value="${_value}">
            <input type="hidden" name="items[${counter}][number]" class="form-control number" value="">
            <input type="text" name="items[${counter}][name]" class="form-control name" placeholder="<?= lang('Tên') ?>" value="">
        </td>`;
        var tdWeekOne = `<td>
            <input type="text" name="items[${counter}][week_one]" class="form-control week_one" placeholder="<?= lang('Tuần 1') ?>" value="">
        </td>`;
        var tdWeekTwo = `<td>
            <input type="text" name="items[${counter}][week_two]" class="form-control week_two" placeholder="<?= lang('Tuần 2') ?>" value="">
        </td>`;
        var tdWeekThree = `<td>
            <input type="text" name="items[${counter}][week_three]" class="form-control week_three" placeholder="<?= lang('Tuần 3') ?>" value="">
        </td>`;
        var tdWeekFour = `<td>
            <input type="text" name="items[${counter}][week_four]" class="form-control week_four" placeholder="<?= lang('Tuần 4') ?>" value="">
        </td>`;
        var tdPriorityLevel = `<td>
            <input type="text" name="items[${counter}][priority_level]" class="form-control priority_level number-format" placeholder="<?= lang('Mức độ ưu tiên') ?>" value="">
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
        counter++;
        totalWorkPlan();
        init_selectpicker();
    }

    $(document).ready(function() {
        $('#month').select2();
        $('#year').select2();
        $('#filter_process').select2({'allowClear': true});
        init_selectpicker();

        $(document).on('change', '#month, #year', function(event) {
            loadWorkPlan();
        });

        loadWorkPlan();
        appValidateForm($('#work_plan_handling'), {
            month: 'required',
            year: 'required',
            content: 'required',
        }, db);

        function db(form) {
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
                } else {
                    alert_float('danger', data.message);
                    $('.add-work-plan').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add-work-plan').removeAttr('disabled', 'disabled');
            });
            return false;
        }
    });
</script>

<script>
    
    $('body').on('click', '.importTable', function () {
        console.log($(this).data('id'))
        $('#file_import_table').attr('data-id', $(this).data('id'));
        $('#file_import_table').click();
    })
    
    $('#file_import_table').change(function() {
        var id = $(this).attr('data-id');
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var filePath = $('#file_import_table').val();//lấy giá trị input theo id
        if (filePath != "") {
            var allowedExtensions = /(\.XLSX|\.XLS)$/i;//các tập tin cho phép
            //Kiểm tra định dạng
            if (!allowedExtensions.exec(filePath)) {
                alert('Vui lòng upload các file có định dạng: .XLSX/.XLS only.');
                $('#file_import_table').val('');
                return false;
            }
        }
        else {
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
            success: function (data) {
                $('#file_import_table').val('');
                data = JSON.parse(data);
                $.each(data, function(index, value) {
                    if(!$.isNumeric(value.id)) {
                        value.id = id;
                    }
                    addItemWorkPlanVal($(`.importTable[data-id="${value.id}"]`), value)
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
            <input type="hidden" name="items[${counter}][number]" class="form-control number" value="">
            <input type="text" name="items[${counter}][name]" class="form-control name" placeholder="<?= lang('Tên') ?>" value="${_value.name}">
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
</script>