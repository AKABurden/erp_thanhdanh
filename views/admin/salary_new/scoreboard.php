<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="table-scoreboard" class="table dt-tnh table-hover table-scoreboard" style="min-width: 100%; max-width: none; width: 1500px;">
                                <thead>
                                    <tr>
                                        <th colspan="2" style="width: 200px;" class="text-center"><?= lang('role') ?></th>
                                        <?php
                                            $thLeve = '';
                                            $tdFooter = '';
                                        ?>
                                        <?php if(!empty($level)): ?>
                                            <?php foreach($level as $key => $value): ?>
                                                <th style="width: 120px;" colspan="2" class="text-center"><?= $value['name'] ?></th>
                                                <?php
                                                    $thLeve.= '<th class="text-center" style="width: 80px;">'.lang('tnh_muc').'</th>
                                                    <th class="text-center" style="width: 80px;">'.lang('tnh_point').'</th>';

                                                    $tdFooter.= '<td class="text-center"></td>
                                                    <td class="text-center"></td>';
                                                ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <th rowspan="2" class="text-center" style="width: 100px;"><?= lang('tnh_total_point') ?></th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
                                        <th class="text-center" style="width: 170px;"><?= lang('tnh_vt') ?></th>
                                        <?= $thLeve ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($roles)): ?>
                                        <?php foreach($roles as $key => $value): ?>
                                            <tr>
                                                <td class="text-center"><?= ++$key ?></td>
                                                <td class="text-center">
                                                    <?= $value['name'] ?>
                                                </td>
                                                <?php if(!empty($level)): ?>
                                                    <?php foreach($level as $k => $val): ?>
                                                        <td>
                                                            <input type="text" onchange="changeScoreboard(<?= $value['roleid'] ?>, <?= $val['id'] ?>, 'muc', this)" name="muc[]" class="form-control number-format muc" value="">
                                                        </td>
                                                        <td>
                                                            <input type="text" onchange="changeScoreboard(<?= $value['roleid'] ?>, <?= $val['id'] ?>, 'point', this)" name="point[]" class="form-control number-format point" value="">
                                                        </td>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                <td class="td-total-point text-center"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bold">
                                        <td></td>
                                        <td class="text-center">
                                            <?= lang('tnh_grand_total') ?>
                                        </td>
                                        <?= $tdFooter ?>
                                        <td class="text-center total-point">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
    };
    var oTable = '';

    function changeScoreboard(role_id, leve_id, type, _this) {
        value = $(_this).val();
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['role_id'] = role_id;
        dataPOST['leve_id'] = leve_id;
        dataPOST['type'] = type;
        dataPOST['value'] = value;

        $.ajax({
            type: "POST",
            url: "url",
            data: dataPOST,
            dataType: "dataType",
            success: function (response) {
                
            }
        });
    }

    function totalScoreboard() {
        tb = '#table-scoreboard tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        count_errors = 0;
        total_point = 0;
        for (ii = 0; ii < n; ii++)
        {
            element = $(tb)[ii];
            point = intVal($(element).find('.td-total-point').html());
            total_point+= point;
        }

        $('.total-point').html(tnhFormatNumber(total_point));
    }

    $(document).ready(function() {
        $(document).on('change', '.point', function(event) {
            cTr = $(this).closest('tr');
            listPoint = cTr.find('.point');
            if (typeof listPoint !== 'undefined' && listPoint.length > 0) {
                total_point_item = 0;
                $.each(listPoint, function (index, value) { 
                    point = intVal($(value).val());
                    total_point_item+= point;
                });
                cTr.find('.td-total-point').html(tnhFormatMoney(total_point_item));
            }
            totalScoreboard();
        });
    });
</script>