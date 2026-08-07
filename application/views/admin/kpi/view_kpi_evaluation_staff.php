<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style>
    #tnhModal2 {
        z-index: 10002;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <table id="table-items" class="table table-hover dataTable dont-responsive-table" style="width: 100%;">
                            <thead>
                            <tr style="">
                                <th style="width: 30px;background-color: #D9F5D6 !important;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                <th style="width: 200px;background-color: #D9F5D6 !important;" class="text-center"><?= lang('Mục tiêu') ?></th>
                                <th style="background-color: #D9F5D6 !important;" class="text-center"><?= lang('Mục đánh giá') ?></th>
                                <th style="width: 200px;background-color: #D9F5D6 !important;" class="text-center"><?= lang('Vi phạm') ?></th>
                                <th style="width: 100px;background-color: #D9F5D6 !important;" class="text-center"><?= lang('Số lần vi phạm') ?></th>
                                <th style="width: 100px;background-color: #D9F5D6 !important;" class="text-center"><?= lang('Lần vi phạm') ?></th>
                                <th style="width: 100px;background-color: #D9F5D6 !important;" class="text-center"><?= lang('Xử Lý - Điểm Trừ') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?= $html ?>
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
