<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="dropdown pull-right">
                <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                    <?= lang('actions') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                    <li>
                        <a href="<?= base_url('admin/personnel/edit_personnel/'.$id) ?>"><i class="fa fa-pencil"></i> <?= lang('tnh_edit_personnel') ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="panel_s">
                <div class="panel-body" style="padding-top: 0px; padding-bottom: 40px;">
                    <div class="text-right"><?= $this->load->view('admin/breadcrumb', ['pull_right' => 'not']) ?></div>
                    <div class="horizontal-scrollable-tabs">
                        <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                        <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                        <div class="horizontal-tabs">
                            <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#general" aria-controls="info-general" role="tab" data-toggle="tab"><?= lang('tnh_detail') ?></a>
                                </li>
                                <li role="presentation">
                                    <a href="#assigned" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('assigned') ?></a>
                                </li>
                                <li role="presentation">
                                    <a href="#insurrance" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_insurrance') ?></a>
                                </li>
                                <li role="presentation">
                                    <a href="#contract" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_contract') ?></a>
                                </li>
                                <li role="presentation">
                                    <a href="#payroll" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_payroll') ?></a>
                                </li>
                                <li role="presentation">
                                    <a href="#asset" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_asset') ?></a>
                                </li>
                                <li role="presentation">
                                    <a href="#receive" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_receive') ?></a>
                                </li>
                                <li role="presentation">
                                    <a href="#attachments" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_attachments') ?> <span>(<?= count($attachments) ?>)</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="general">
                                <table class="table-personnel">
                                    <tr>
                                        <td colspan="5" class="text-primary bg-primary bold"><?= lang('tnh_info_general') ?></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="4" style="width: 10%;">
                                            <?php
                                                $images = $personnel['images'];
                                                if (empty($images)) {
                                                    $images = base_url('assets/images/tnh/default-avatar-male.png');
                                                } else {
                                                    $images = base_url('uploads/personnel/').$personnel['folder'].'/'.$personnel['images'];
                                                }
                                            ?>
                                            <div class="preview_image" style="width: auto;">
                                                <div class="display-block contract-attachment-wrapper img">
                                                    <div style="width:200px; margin: auto;">
                                                        <a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                            <div class="">
                                                                <img src="<?= $images ?>"/>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 20%;"><?= lang('tnh_fullname') ?></td>
                                        <td style="width: 25%;" class="bold"><?= $personnel['fullname'] ?></td>
                                        <td style="width: 20%;"><?= lang('tnh_code_personnel') ?></td>
                                        <td style="width: 25%;" class="bold"><?= $personnel['code'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('tnh_birthday') ?></td>
                                        <td class="bold"><?= _d($personnel['birthday']) ?></td>
                                        <td><?= lang('tnh_gender') ?></td>
                                        <td class="bold">
                                            <?php
                                                $gender = $personnel['gender'];
                                                if ($gender == 'male') {
                                                    $gender = lang('tnh_male');
                                                } else if ($gender == 'female') {
                                                    $gender = lang('tnh_female');
                                                } else if ($gender == 'other') {
                                                    $gender = lang('tnh_other');
                                                }
                                                echo $gender;
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('tnh_marital_status') ?></td>
                                        <td class="bold">
                                            <?php
                                                $marital_status = $personnel['marital_status'];
                                                if ($marital_status == 'alone') {
                                                    $marital_status = lang('tnh_alone');
                                                } else if ($marital_status == 'marriage') {
                                                    $marital_status = lang('tnh_marriage');
                                                } else if ($marital_status == 'divorce') {
                                                    $marital_status = lang('tnh_divorce');
                                                }
                                                echo $marital_status;
                                            ?>
                                        </td>
                                        <td><?= lang('tnh_nationality') ?></td>
                                        <td class="bold"><?= $personnel['nationality'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('tnh_telephone') ?></td>
                                        <td class="bold"><?= $personnel['telephone'] ?></td>
                                        <td><?= lang('Email') ?></td>
                                        <td class="bold"><?= $personnel['email'] ?></td>
                                    </tr>
                                </table>
                                <table class="table-personnel mtop10">
                                    <tr>
                                        <td colspan="4" class="text-primary bg-primary bold"><?= lang('tnh_info_other') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;"><?= lang('tnh_nation') ?></td>
                                        <td style="width: 25%;" class="bold"><?= $personnel['nation'] ?></td>
                                        <td style="width: 25%;"><?= lang('tnh_religion') ?></td>
                                        <td style="width: 25%;" class="bold"><?= $personnel['religion'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('tnh_account_name') ?></td>
                                        <td class="bold"><?= $personnel['account_name'] ?></td>
                                        <td><?= lang('tnh_bank') ?></td>
                                        <td class="bold"><?= $personnel['bank'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('tnh_branch') ?></td>
                                        <td class="bold"><?= $personnel['branch'] ?></td>
                                        <td><?= lang('tnh_personal_tax_code') ?></td>
                                        <td class="bold"><?= $personnel['personal_tax_code'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('Skype') ?></td>
                                        <td class="bold"><?= $personnel['skype'] ?></td>
                                        <td><?= lang('Facebook') ?></td>
                                        <td class="bold"><?= $personnel['facebook'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('tnh_resident') ?></td>
                                        <td class="bold"><?= $personnel['resident'] ?></td>
                                        <td><?= lang('tnh_current_accommodation') ?></td>
                                        <td class="bold"><?= $personnel['current_accommodation'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('note') ?></td>
                                        <td class="bold" colspan="3"><?= $personnel['note'] ?></td>
                                    </tr>
                                </table>
                                <table class="table-personnel mtop10">
                                    <tr>
                                        <td class="text-primary bg-primary bold"><?= lang('tnh_family_information') ?></td>
                                    </tr>
                                </table>
                                <div class="mtop5">
                                    <table id="tb-family" class="table dt-tnh tnh-table table-hover table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th><?= lang('tnh_numbers') ?></th>
                                                <th><?= lang('tnh_relationship') ?></th>
                                                <th><?= lang('tnh_fullname') ?></th>
                                                <th><?= lang('tnh_year_birthday') ?></th>
                                                <th><?= lang('tnh_career') ?></th>
                                                <th><?= lang('tnh_address') ?></th>
                                                <th><?= lang('tnh_telephone') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($family)): ?>
                                                <?php foreach ($family as $key => $value): ?>
                                                    <tr>
                                                        <td class="text-center"><?= ++$key ?></td>
                                                        <td><?= getRelationship($value['relationship_family']) ?></td>
                                                        <td><?= $value['fullname_family'] ?></td>
                                                        <td><?= $value['year_birthday_family'] ?></td>
                                                        <td><?= $value['career_family'] ?></td>
                                                        <td><?= $value['address_family'] ?></td>
                                                        <td><?= $value['telephone_family'] ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                                <table class="table-personnel mtop10">
                                    <tr>
                                        <td class="text-primary bg-primary bold"><?= lang('tnh_literacy') ?></td>
                                    </tr>
                                </table>
                                <div class="mtop5">
                                    <table id="tb-literacy" class="table dt-tnh tnh-table table-hover table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th><?= lang('tnh_numbers') ?></th>
                                                <th><?= lang('from_date') ?></th>
                                                <th><?= lang('to_date') ?></th>
                                                <th><?= lang('tnh_literacy') ?></th>
                                                <th><?= lang('tnh_training_places') ?></th>
                                                <th><?= lang('tnh_specialized') ?></th>
                                                <th><?= lang('tnh_classification') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($literacy)): ?>
                                                <?php foreach ($literacy as $key => $value): ?>
                                                    <tr>
                                                        <td class="text-center"><?= ++$key ?></td>
                                                        <td><?= _d($value['from_date_literacy']) ?></td>
                                                        <td><?= _d($value['to_date_literacy']) ?></td>
                                                        <td><?= getLiteracy($value['literacy']) ?></td>
                                                        <td><?= $value['training_places_literacy'] ?></td>
                                                        <td><?= $value['specialized_literacy'] ?></td>
                                                        <td><?= getClassification($value['classification_literacy']) ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="assigned">
                                <table class="table-personnel mtop10">
                                    <tr>
                                        <td colspan="4" class="text-primary bg-primary bold"><?= lang('assigned') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;"><?= lang('departments') ?></td>
                                        <td style="width: 25%;" class="bold"><?= $deparment['name'] ?></td>
                                        <td style="width: 25%;"><?= lang('tnh_vt') ?></td>
                                        <td style="width: 25%;" class="bold"><?= $location['name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('role') ?></td>
                                        <td class="bold"><?= $role['name'] ?></td>
                                        <td><?= lang('tnh_workplace') ?></td>
                                        <td class="bold"><?= $workplace['name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('tnh_day_in') ?></td>
                                        <td class="bold"><?= _d($personnel['day_in']) ?></td>
                                        <td><?= lang('tnh_day_in_primary') ?></td>
                                        <td class="bold"><?= _d($personnel['day_in_primary']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('tnh_status') ?></td>
                                        <td class="bold"><?= $personnel['status'] == 1 ? lang('tnh_working') : '' ?></td>
                                        <td><?= lang('tnh_account') ?></td>
                                        <td class="bold">Chưa có tài khoản phần mềm</td>
                                    </tr>
                                </table>
                                <table class="table-personnel mtop10">
                                    <tr>
                                        <td colspan="4" class="text-primary bg-primary bold"><?= lang('tnh_concurrently') ?></td>
                                    </tr>
                                </table>
                                <div class="mtop5">
                                    <table id="tb-concurrently" class="table dt-tnh tnh-table table-hover table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th><?= lang('tnh_numbers') ?></th>
                                                <th><?= lang('tnh_depart_concurrently') ?></th>
                                                <th><?= lang('tnh_vt') ?></th>
                                                <th><?= lang('role') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($concurrently)): ?>
                                                <?php foreach ($concurrently as $key => $value): ?>
                                                    <tr>
                                                        <td class="text-center"><?= ++$key ?></td>
                                                        <td><?= $value['name_department'] ?></td>
                                                        <td><?= $value['name_location'] ?></td>
                                                        <td><?= $value['name_role'] ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                                <table class="table-personnel mtop10">
                                    <tr>
                                        <td colspan="4" class="text-primary bg-primary bold"><?= lang('tnh_salary_and_allowance') ?></td>
                                    </tr>
                                </table>
                                <div class="mtop5">
                                    <table id="tb-salary_and_allowance" class="table dataTable dt-tnh tnh-table table-hover table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                                <th><?= lang('from_date') ?></th>
                                                <th><?= lang('tnh_salary_form') ?></th>
                                                <th><?= lang('tnh_amount_of_money') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($salary)): ?>
                                                <?php foreach ($salary as $key => $value): ?>
                                                    <tr>
                                                        <td class="text-center"><?= ++$key ?></td>
                                                        <td><?= _d($value['from_date_salary']) ?></td>
                                                        <td><?= $value['name_salary'] ?></td>
                                                        <td class="text-right"><?= formatMoney($value['money_salary']) ?></td>
                                                    </tr>
                                                    <?php
                                                        $allowance = $this->personnel_model->getPersonnelSalaryAllowance($value['id']);
                                                    ?>
                                                    <?php if (!empty($allowance)): ?>
                                                        <?php foreach ($allowance as $k => $val): ?>
                                                            <tr>
                                                                <td class="text-center"><?= $key.'.'.(++$k) ?></td>
                                                                <td><span class="label label-warning"><?= lang('tnh_allowance') ?></span></td>
                                                                <td><?= $val['name_allowance'] ?></td>
                                                                <td class="text-right"><?= formatMoney($val['money_salary_allowance']) ?></td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                    <?php endif ?>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                                <table class="table-personnel mtop10">
                                    <tr>
                                        <td colspan="4" class="text-primary bg-primary bold"><?= lang('tnh_history_job') ?></td>
                                    </tr>
                                </table>
                                <div class="mtop5">
                                    <table id="tb-history-job" class="table dt-tnh tnh-table table-hover table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th><?= lang('tnh_numbers') ?></th>
                                                <th><?= lang('from_date') ?></th>
                                                <th><?= lang('status') ?></th>
                                                <th><?= lang('departments') ?></th>
                                                <th><?= lang('acs_roles') ?></th>
                                                <th><?= lang('role') ?></th>
                                                <th><?= lang('tnh_contract_type') ?></th>
                                                <th><?= lang('tnh_contract_code') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($historyJob)): ?>
                                                <?php foreach ($historyJob as $key => $value): ?>
                                                    <tr>
                                                        <td class="text-center"><?= ++$key ?></td>
                                                        <td><?= _d($value['date']) ?></td>
                                                        <td><?= $value['status'] == 1 ? lang('tnh_working') : '' ?></td>
                                                        <td><?= $value['name_department'] ?></td>
                                                        <td><?= $value['name_location'] ?></td>
                                                        <td><?= $value['name_role'] ?></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="insurrance">
                                <table class="table-personnel mtop10">
                                    <tr>
                                        <td colspan="4" class="text-primary bg-primary bold"><?= lang('tnh_insurrance') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;"><?= lang('tnh_insurrance_book_number') ?></td>
                                        <td style="width: 25%;" class="bold"><?= $personnel['insurrance_book_number'] ?></td>
                                        <td style="width: 25%;"><?= lang('tnh_number_bhty') ?></td>
                                        <td style="width: 25%;" class="bold"><?= $personnel['number_bhty'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= lang('tnh_province_code') ?></td>
                                        <td class="bold"><?= $province['name'] ?></td>
                                        <td><?= lang('tnh_hospital_registration') ?></td>
                                        <td class="bold"><?= $hospital['name'] ?></td>
                                    </tr>
                                </table>
                                <table class="table-personnel mtop10">
                                    <tr>
                                        <td colspan="4" class="text-primary bg-primary bold"><?= lang('tnh_history_insurrance') ?></td>
                                    </tr>
                                </table>
                                <div class="mtop5">
                                    <table id="tb-history-insurrance" class="table dataTable dt-tnh tnh-table table-hover table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                                <th><?= lang('tnh_form_month') ?></th>
                                                <th><?= lang('tnh_hinhthuc') ?></th>
                                                <th><?= lang('tnh_insurrance') ?></th>
                                                <th><?= lang('tnh_premium_rates') ?></th>
                                                <th><?= lang('tnh_rate_company') ?></th>
                                                <th><?= lang('tnh_rate_worker') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($insurrance)): ?>
                                                <?php foreach ($insurrance as $key => $value): ?>
                                                    <tr>
                                                        <td class="text-center"><?= (++$key) ?></td>
                                                        <td><?= $value['from_month_insurrance'] ?></td>
                                                        <td><?= getFormInsurrance($value['form_insurrance']) ?></td>
                                                        <td class=""><?= $value['name_insurrance'] ?></td>
                                                        <td class="text-center"><?= formatMoney($value['money_insurrance']) ?></td>
                                                        <td class="text-center"><?= $value['rate_company_insurrance'] ?></td>
                                                        <td class="text-center"><?= $value['rate_worker_insurrance'] ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="contract">...</div>
                            <div role="tabpanel" class="tab-pane" id="payroll">...</div>
                            <div role="tabpanel" class="tab-pane" id="asset">...</div>
                            <div role="tabpanel" class="tab-pane" id="receive">
                                <?php foreach (getReceivePersonnel() as $key => $value): ?>
                                    <div class="checkbox checkbox-info">
                                        <input type="checkbox" class="disabled" <?= in_array($key, $arrReceive) ? 'checked' : '' ?> name="receive[]" id="<?= $key ?>" value="<?= $key ?>" disabled>
                                        <label style="opacity: 1;" for="<?= $key ?>"><?= $value ?></label>
                                    </div>
                                <?php endforeach ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="attachments">
                                <table class="table-personnel mtop10">
                                    <tr>
                                        <td colspan="4" class="text-primary bg-primary bold"><?= lang('tnh_attachments') ?></td>
                                    </tr>
                                </table>
                                <div class="mtop5">
                                    <table id="tb-attach" class="table dataTable dt-tnh tnh-table table-hover table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                                <th><?= lang('media_files') ?></th>
                                                <th><?= lang('item_size') ?></th>
                                                <th><?= lang('tnh_format') ?></th>
                                                <th><?= lang('tnh_created_by') ?></th>
                                                <th><?= lang('date_created') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($attachments)): ?>
                                                <?php foreach ($attachments as $key => $value): ?>
                                                    <tr>
                                                        <td class="text-center"><?= (++$key) ?></td>
                                                        <td><a target="_blank" href="<?= base_url('uploads/personnel/').$personnel['folder'].'/'.$value['name'] ?>"><?= $value['name'] ?></a></td>
                                                        <td><?= formatNumber($value['size']/(1024 * 1024)) ?> MB</td>
                                                        <td><?= $value['extension'] ?></td>
                                                        <td><?= $value['update_by'] ?></td>
                                                        <td><?= _d($value['date_updated']) ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        var dtFamily = $('#tb-family').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
            }
        });

        var dtLiteracy = $('#tb-literacy').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
            }
        });

        var dtConcurrently = $('#tb-concurrently').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
            }
        });

        var dtSalary = $('#tb-salary_and_allowance').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
            }
        });

        var dtInsurrance = $('#tb-history-insurrance').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
            }
        });

        var dtAttach = $('#tb-attach').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
            }
        });

        var dtHistoryJob = $('#tb-history-job').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
            }
        });
    });
</script>