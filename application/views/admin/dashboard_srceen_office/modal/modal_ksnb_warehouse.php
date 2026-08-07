<div class="modal-dialog modal-lg" style="width: 100%; max-width: 70vw;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="closeModal('chModal_dashboard')" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <input type="hidden" name="type" id="type" value="<?= $type ?>">
            <table class="table table_ksnb_warehouse dataTable" id="table_ksnb_warehouse">
                <thead>
                <tr>
                    <th class="text-center"><?= ucwords(_l('STT')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Hạn sử dụng')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Quá hạn (Ngày)')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Mã')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Tên')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Số lượng tồn')); ?></th>
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
    $(document).ready(function() {
        var fnserverparams = {
            'type': '#type',
        };
        oTable = initDataTable_ch('.table_ksnb_warehouse', '<?= base_url('dashboard_srceen_office/getDetailModalKsnbWarehouse?csrf_protection=true') ?>', [0], [0], fnserverparams, [0, 'desc']);

    });
</script>