<div class="modal-dialog modal-lg" style="width: 100%; max-width: 70vw;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="closeModal('chModal_dashboard')" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <input type="hidden" name="type" id="type" value="<?= $type ?>">
            <input type="hidden" name="department_id" id="department_id" value="<?= $department_id ?>">
            <table class="table table_kpi dataTable" id="table_kpi">
                <thead>
                <tr>
                    <th class="text-center"><?= ucwords(_l('STT')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Tháng/Quý')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Nhân viên')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Số điểm')); ?></th>
                    <th class="text-center"><?= ucwords(_l('Xếp hạng')); ?></th>
                </tr>
                </thead>
                <tbody id="html_modal_detail_kpi"></tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="closeModal('chModal_dashboard')">Đóng</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        view_kpi_evaluation_new();
    });
    function view_kpi_evaluation_new() {
        var type = $("#type").val();
        var department_id = $("#department_id").val();
        $.ajax({
            type: 'POST',
            url: "<?= base_url('dashboard_srceen_office/getDetailModalKPI?csrf_protection=true') ?>",
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                type: type,
                department_id: department_id,
            },
            dataType: "JSON",
            success: function (response) {
                $('tbody#html_modal_detail_kpi').html(response.html);
            }
        });
    }
</script>