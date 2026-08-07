
<div class="modal-dialog modal-lg" style="width: 100%; max-width: 70vw;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="closeModal('chModal_dashboard')" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= ($type == 1) ? lang('Phát triển mẫu chưa tạo đơn hàng') : lang('Phát triển mẫu chưa duyệt') ?></h4>
        </div>
        <div class="modal-body">
            <style>

            </style>
            <table class="table table-sample dataTable ">
                <thead>
                    <tr>
                        <th class="text-center"><?= ucwords(_l('STT')); ?></th>
                        <th class="text-center"><?= ucwords(_l('Ngày tạo')); ?></th>
                        <th class="text-center"><?= ucwords(_l('Mã Phát triển mẫu')); ?></th>
                        <th class="text-center"><?= ucwords(_l('Khách hàng')); ?></th>
                        <th class="text-center"><?= ucwords(_l('Người tạo')); ?></th>
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
        tAPI = initDataTable_ch('.table-sample', '<?= base_url('dashboard_srceen_vp/table_sample/' . $type . '?csrf_protection=true') ?>', [0], [0], CustomersServerParams, [1, 'desc']);
    });
</script>