<div class="modal-dialog modal-lg" style="width: 100%; max-width: 70vw;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="closeModal('chModal_dashboard')" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
                <?php if($type == 1): ?>
                    <?= lang('TỔNG LỆNH CHƯA XUẤT ĐỦ NPL') ?>
                <?php elseif($type == 2): ?>
                    <?= lang('TỔNG LỆNH XUẤT ĐỦ NPL') ?>
                <?php elseif($type == 3): ?>
                    <?= lang('TỔNG LỆNH CHƯA XUẤT ĐỦ VTSX') ?>
                <?php elseif($type == 4): ?>
                    <?= lang('TỔNG LỆNH XUẤT ĐỦ VTSX') ?>
                <?php endif; ?>

            </h4>
        </div>
        <div class="modal-body">
            <style>

            </style>
            <table class="table table-export_vtsx dataTable ">
                <thead>
                    <tr>
                        <th class="text-center"><?= ucwords(_l('STT')); ?></th>
                        <th class="text-center"><?= ucwords(_l('Ngày tạo')); ?></th>
                        <th class="text-center"><?= ucwords(_l('Mã lệnh')); ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="closeModal('chModal_dashboard')">Đóng</button>
        </div>
    </div>
</div>

<script>
    var tAPI;
    $(function() {
        var CustomersServerParams = {};
        tAPI = initDataTable_ch('.table-export_vtsx', '<?= base_url('dashboard_srceen_vp/table_export_vtsx/' . $type . '?csrf_protection=true') ?>', [0], [0], CustomersServerParams, [1, 'desc']);
    });
</script>