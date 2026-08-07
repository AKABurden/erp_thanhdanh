<?php init_head(false); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<style>
    #wrapper {
        margin: unset !important;
    }
    .div_row{
        display: flex;
        font-weight: bold;
    }
</style>
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
                        <div class="div_row">
                            <div style="min-width: 100px"><?= lang('Mã Nhóm KPIs:') ?></div>
                            <div><?= !empty($dtCategoryKpi) ? $dtCategoryKpi['code'] : '' ?></div>
                        </div>
                        <div class="div_row">
                            <div style="min-width: 100px"><?= lang('Tên Nhóm KPIs:') ?></div>
                            <div><?= !empty($dtCategoryKpi) ? $dtCategoryKpi['name'] : '' ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div>
                <div class="col-md-12">
                    <table id="tb-purchases" class="dt-tnh table table-hover dataTable" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 100px;"><?= lang('Diễn giải KPIs') ?></th>
                            <th style="width: 100px;"><?= lang('Tiêu Chí') ?></th>
                            <th style="width: 150px;"><?= lang('Mã KPI') ?></th>
                            <th style="width: 200px;"><?= lang('Đo lường') ?></th>
                            <th style="width: 150px;"><?= lang('Target') ?></th>
                            <th style="width: 150px;"><?= lang('Trọng số (%)') ?></th>
                            <th style="width: 150px;"><?= lang('Chu kỳ báo cáo') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $total = 0; if (!empty($dtCategoryKpiCriNL)) { ?>
                            <?php foreach ($dtCategoryKpiCriNL as $key => $value) { ?>
                                <tr>
                                    <td class="text-center"><?= (++$key) ?></td>
                                    <td><?= $value['name'] ?></td>
                                    <td>Năng Lực</td>
                                    <td><?= $value['code'] ?></td>
                                    <td><?= $value['measure'] ?></td>
                                    <td class="text-center"><?= $value['time'] ?></td>
                                    <td class="text-center"><?= $value['weight'] ?></td>
                                    <td class="text-left"><?= $value['reporting_cycle'] ?></td>
                                </tr>
                            <?php $total += $value['weight']; } ?>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="bold">Tổng cộng</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center bold"><?= $total ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="bold">% KPI</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center bold" style="color: red">80</td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 mtop30">
                    <table id="tb-purchases1" class="dt-tnh table table-hover dataTable" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 100px;"><?= lang('Diễn giải KPIs') ?></th>
                            <th style="width: 100px;"><?= lang('Tiêu Chí') ?></th>
                            <th style="width: 150px;"><?= lang('Mã KPI') ?></th>
                            <th style="width: 200px;"><?= lang('Đo lường') ?></th>
                            <th style="width: 150px;"><?= lang('Target') ?></th>
                            <th style="width: 150px;"><?= lang('Trọng số (%)') ?></th>
                            <th style="width: 150px;"><?= lang('Chu kỳ báo cáo') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $total = 0; if (!empty($dtCategoryKpiCriTT)) { ?>
                            <?php foreach ($dtCategoryKpiCriTT as $key => $value) { ?>
                                <tr>
                                    <td class="text-center"><?= (++$key) ?></td>
                                    <td><?= $value['name'] ?></td>
                                    <td>Tuân Thủ</td>
                                    <td><?= $value['code'] ?></td>
                                    <td><?= $value['measure'] ?></td>
                                    <td class="text-center"><?= $value['time'] ?></td>
                                    <td class="text-center"><?= $value['weight'] ?></td>
                                    <td class="text-left"><?= $value['reporting_cycle'] ?></td>
                                </tr>
                            <?php $total += $value['weight']; } ?>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="bold">Tổng cộng</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center bold"><?= $total ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="bold">% KPI</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center bold" style="color: red">20</td>
                            <td></td>
                        </tr>
                        </tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="bold">Tổng KPI</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center bold" style="color: red">100</td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
        </div>
    </div>
</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
