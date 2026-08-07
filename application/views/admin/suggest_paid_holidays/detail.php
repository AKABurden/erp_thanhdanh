<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<style>
    .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn){
        width: 100%;
    }
</style>
<?php echo form_open('admin/suggest_paid_holidays/detail/' . $id . '',
    array('id' => 'suggest_paid_holiday')); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('info') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover">
                            <tbody>
                            <tr>
                                <td style="width: 15%;">
                                    <?= lang('dt_reference_suggest', 'reference_no') ?>
                                </td>
                                <td style="width: 35%;">
                                    <div class="form-group">
                                        <input type="text" name="reference_no" class="form-control" id="reference_no"
                                               value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
                                    </div>
                                </td>
                                <td style="width: 15%;">
                                    <?= lang('date', 'date') ?>
                                </td>
                                <td style="width: 35%;">
                                    <?= form_input('date', set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) :date('d/m/Y H:i'),
                                        'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Người đề xuất', 'staff_id') ?></td>
                                <td colspan="1">
                                    <select name="staff_id" id="staff_id"
                                            data-placeholder="<?= lang('Người đề xuất') ?>" style="width: 100%;"
                                            class="">
                                        <option value=""></option>
                                        <?php foreach ($employees as $key => $value) : ?>
                                            <option <?= !empty($dtData) ? ( $dtData['staff_id'] == $value['staffid'] ? 'selected' : '' ) : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                                    value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                                <td><?= lang('Báo cáo không phù hợp', 'production_report_id') ?></td>
                                <td>
                                    <input type="text" name="production_report_id" id="production_report_id" class="production_report_id"
                                           data-placeholder="<?= lang('Báo cáo không phù hợp') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['production_report_id'] : '' ?>"
                                           title="">
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Người duyệt', 'staff_agree') ?></td>
                                <td>
                                    <select name="staff_agree" id="staff_agree"
                                            data-placeholder="<?= lang('Người duyệt') ?>" style="width: 100%;"
                                            class="">
                                        <option value=""></option>
                                        <?php foreach ($employees as $key => $value) : ?>
                                            <option <?= !empty($dtData) ? ( $dtData['staff_agree'] == $value['staffid'] ? 'selected' : '' ) : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                                value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                                <td><?= lang('Người tiếp nhận tạm thời', 'staff_reciever') ?></td>
                                <td>
                                    <select name="staff_reciever" id="staff_reciever"
                                            data-placeholder="<?= lang('Người tiếp nhận tạm thời') ?>" style="width: 100%;"
                                            class="">
                                        <option value=""></option>
                                        <?php foreach ($employees as $key => $value) : ?>
                                            <option <?= !empty($dtData) ? ( $dtData['staff_reciever'] == $value['staffid'] ? 'selected' : '' ) : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?>
                                                    value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Quy định', 'regulations') ?></td>
                                <td colspan="1">
                                    <input type="text" name="regulations" class="regulations form-control" id="regulations">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="month" class="control-label bold"><?= lang('Tháng') ?></label>
                        <select class="selectpicker month form-control" name="month" multiple id="month"
                                data-live-search="true"
                                onchange="changeMonth(this)"
                                title='<?php echo _l('Tháng'); ?>'
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php foreach (getMonth() as $k => $v) : ?>
                                <?php if ($k == '') {
                                    continue;
                                } ?>
                                <option <?= (!empty($arrSelect) && in_array($v,
                                        $arrSelect)) ? 'selected' : ($k == date('m') ? 'selected' : '') ?>
                                        value="<?= $k ?>"><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="year" class="control-label bold"><?= lang('Năm') ?></label>
                        <select class="selectpicker year form-control" name="year" id="year"
                                data-live-search="true"
                                onchange="changeYear(this)"
                                title='<?php echo _l('Năm'); ?>'
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php foreach (getYear() as $k => $v) : ?>
                                <option <?= (!empty($arrSelect) && in_array($v,
                                        $arrSelect)) ? 'selected' : ($k == date('Y') ? 'selected' : '') ?>
                                        value="<?= $k ?>"><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="type_magic_new" class="control-label bold"><?= lang('Loại phép') ?></label>
                        <select class="type_magic_new modal-select2 selectpicker"
                                data-live-search="true"
                                title='<?php echo _l('Loại phép'); ?>'>
                            <?php foreach ($typeMagic as $key => $value) { ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date_paid_holiday"
                               class="control-label bold"><?= lang('Ngày nghỉ phép') ?></label>
                        <select class="date_paid_holiday modal-select2 selectpicker"
                                data-live-search="true"
                                multiple
                                title='<?php echo _l('Ngày nghỉ phép'); ?>'>
                            <?php foreach ($allDateNew as $key => $value) { ?>
                                <option value="<?= $value['date'] ?>"
                                        data-subtext="<?= $value['day'] ?>"><?= $value['date'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2" style="margin-top: 27px">
                    <button type="button" class="btn btn-primary" onclick="chosenDate(this)">Chọn</button>
                </div>
                <div class="clearfix"></div>
                <div class="">
                    <table id="tb-payment-methods" class="dt-tnh table table-hover" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 150px;"><?= lang('Loại Phép') ?></th>
                            <th style="width: 100px;"><?= lang('Ngày Bắt Đầu Nghỉ') ?></th>
                            <th style="width: 100px;"><?= lang('Ngày Kết Thúc Nghỉ') ?></th>
                            <th style="width: 150px;"><?= lang('Số Ngày Nghỉ') ?></th>
                            <th style="width: 100px;"><?= lang('Ngày Đi Làm Lại') ?></th>
                            <th style="width: 100px;"><?= lang('Lý Do Nghỉ') ?></th>
                            <th style="width: 50px;"><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 0;
                        if (!empty($dtItems)){ ?>
                            <?php foreach ($dtItems as $key => $value){ ?>
                                <?php
                                $htmlTypeMagic = '';
                                foreach ($typeMagic as $kk => $val) {
                                    $htmlTypeMagic .= '<option ' . ($val['id'] == $value['type_magic'] ? 'selected' : '') . ' value="' . $val['id'] . '">' . $val['name'] . '</option>';
                                }
                                $month = date_format(date_create($value['date_end']), 'm')
                                ?>
                                <tr class="edit_date" data-date="<?= _dhau($value['date_end']) ?>">
                                    <td class="text-center stt"></td>
                                    <td>
                                        <input type="hidden" name="conter[<?= $counter ?>]" class="conter"
                                               value="<?= $counter ?>">
                                        <input type="hidden" name="suggest_paid_holiday_item_id[<?= $counter ?>]"
                                               value="<?= $value['id'] ?>">
                                        <select class="type_magic modal-select2 selectpicker"
                                                data-live-search="true"
                                                onchange="changeType(this);getTotal();"
                                                title='<?php echo _l('Loại phép'); ?>'
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                style="width: 100%;height: 30px"
                                                name="type_magic[<?= $counter ?>]"
                                                id="type_magic<?= $counter ?>">
                                            <?= $htmlTypeMagic ?>
                                        </select>
                                        <div class="text_error_magic" style="color:red"></div>
                                    </td>
                                    <td>
                                        <div class="html_date_start"><?= _dhau($value['date_start']) ?></div>
                                        <input type="hidden" required onchange="getTotal();changeDate(this);"
                                               name="date_start[<?= $counter ?>]" autocomplete="off"
                                               class="form-control date_start datepicker"
                                               value="<?= _dhau($value['date_start']) ?>">
                                    </td>
                                    <td>
                                        <div class="html_date_end"><?= _dhau($value['date_end']) ?></div>
                                        <input type="hidden" required onchange="getTotal();changeDate(this);"
                                               name="date_end[<?= $counter ?>]" autocomplete="off" readonly
                                               class="form-control none-event date_end datepicker"
                                               value="<?= _dhau($value['date_end']) ?>">
                                    </td>
                                    <td class="td-date">
                                        <input type="hidden" required onchange="getTotal()"
                                               name="number_day[<?= $counter ?>]"
                                               class="form-control number_day number-format"
                                               value="<?= ($value['number_date']) ?>">
                                        <div class="sub">
                                            <div class="sb" style="display: flex;align-items: center">
                                                <div class="col-md-7" style="padding: 0px;"><span
                                                            class="bold"
                                                            style="font-style: italic">Tháng  <?= $month ?></span><input
                                                            type="hidden"
                                                            name="month_sub[<?= $counter ?>][]"
                                                            value="<?= $month ?>" style="width: 100%;"
                                                            title=""></div>
                                                <div class="col-md-5" style="padding: 0px;"><input
                                                            type="text" onchange="getTotalNew()" required
                                                            style="width: 100%;"
                                                            name="quantity_sub[<?= $counter ?>][]"
                                                            id="input"
                                                            class="form-control quantity_sub number-format"
                                                            value="<?= $value['number_date'] ?>"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" required name="day_work[<?= $counter ?>]"
                                               onchange="changeDateWork(this);getTotal()" autocomplete="off"
                                               class="form-control day_work datepicker"
                                               value="<?= _dhau($value['day_work']) ?>">
                                    </td>
                                    <td style="width: 120px" class="text-left">
                                            <textarea class="form-control note" name="note[<?= $counter ?>]"
                                                      cols="2" rows="2"><?= $value['note'] ?></textarea>
                                    </td>
                                    <td class="text-center"><span class="fa fa-remove text-danger pointer"
                                              onclick="removePaymentMethods(this)"></span></td>
                                </tr>
                                <?php $counter++;} ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <input type="hidden" name="add" id="" class="form-control" value="1">
            <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                <?php echo _l('submit'); ?>
            </button>
        </div>
    </div>
</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
    var dt = '';
    var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var edit = <?= !empty($dtData) ? 1 : 0 ?>;
    var counter = <?= $counter ?>;
    var count_errors = 0;
    var arrDateExist = [];
    var dtTypeMagic = <?= !empty($typeMagic) ? json_encode($typeMagic) : '{}' ?>;
</script>
<?php $this->load->view('admin/suggest_paid_holidays/script_js.php') ?>
