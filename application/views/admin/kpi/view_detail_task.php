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
            <div>
                <div class="text-center bold font-size-20"><?= lang('CAM KẾT MÔ TẢ CÔNG VIỆC') ?></div>
                <div class="col-md-12">
                    <table id="tb-purchases" class="dt-tnh table table-hover dataTable" style="width: 100%;">
                        <thead>
                        <tr>
                            <th rowspan="3" class="text-center" style="width: 30px;"><?= lang('STT') ?></th>
                            <th style="width: 100px;" class="text-center"><?= lang('Mã Vị Trí') ?></th>
                            <th style="width: 100px;" class="text-center"><?= lang('Tên Vị Trí') ?></th>
                            <th style="width: 250px;" class="text-center"><?= lang('Chức Vụ') ?></th>
                            <th style="width: 100px;" class="text-center"><?= lang('Mã KPI') ?></th>
                            <th style="width: 100px;" class="text-center"><?= lang('Loại KPIs') ?></th>
                            <th style="width: 100px;" class="text-center"><?= lang('QUI ĐỊNH') ?></th>
                        </tr>
                        <tr>
                            <th style="width: 100px;" class="text-center"><?= $dtData['code_role'] ?></th>
                            <th style="width: 100px;" class="text-center"><?= $dtData['name_role'] ?></th>
                            <th style="width: 150px;" class="text-center"><?= $dtData['name_position'] ?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th style="width: 100px;" class="bold text-center"><?= lang('Danh Mục') ?></th>
                            <th style="width: 100px;" class="text-center"></th>
                            <th style="width: 150px;" class="bold text-center"><?= lang('DIỄN GIẢI') ?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $stt = 1; if (!empty($arrItems)){ ?>
                            <?php foreach ($arrItems as $key => $value){ ?>
                                <tr>
                                    <td class="text-center"><?= $stt ?></td>
                                    <td class="bold"><?= $key ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <?php $sttNew = 1; if (!empty($value)){ ?>
                                    <?php foreach ($value as $kk => $vv){ ?>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right"><?= $stt.'.'.$sttNew ?></td>
                                            <td><?= $vv['note'] ?></td>
                                            <td><?= $vv['code_kpi'] ?></td>
                                            <td><?= !empty($vv['type_kpi']) ? ($vv['type_kpi'] == 1 ? 'Năng Lực' : 'Tuân Thủ') : '' ?></td>
                                            <td><?= $vv['regulations'] ?></td>
                                        </tr>
                                <?php $sttNew++; } ?>
                                <?php } ?>
                        <?php $stt++; } ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
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
