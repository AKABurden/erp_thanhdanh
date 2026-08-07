<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<div class="modal-dialog modal-lg" style="width:40%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <table id="table_machine_productivity_manu"
                               class="table table-hover dataTable dont-responsive-table" style="width: 100%;">
                            <thead>
                            <tr style="">
                                <th style="width: 30px;" class="text-center">STT</th>
                                <th style="width: 200px;" class="text-center"><?= lang('Ngày') ?></th>
                                <th style="width: 100px;" class="text-center"><?= lang('Mã Lệnh') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($query)) { ?>
                                <?php foreach ($query as $key => $value) { ?>
                                    <tr>
                                        <td class="text-center"><?= (++$key) ?></td>
                                        <td><?= _dt($value['date']) ?></td>
                                        <td><?= $value['reference_no'] ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
</script>
