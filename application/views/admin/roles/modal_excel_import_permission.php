<div id="modal_excel_import_permission" class="modal fade" role="dialog">
    <form action="<?= admin_url('roles/export_excel_permission') ?>" id="import_form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate="novalidate">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= $title ?></h4>
                </div>
                <div class="modal-body">
                    <label for="role_ids" class="control-label">Vị trí</label>
                    <select id="role_ids" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                        <option></option>
                        <?php if (!empty($data_roles)) {
                            foreach ($data_roles as $key => $value) { ?>
                                <option data-subtext="<?= $value['code_role'] ?>" value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="clearfix"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" onclick="exportExcel()">Export</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $('#modal_excel_import_permission').modal('show');
    init_selectpicker();

    function exportExcel() {
        role_ids = $('#role_ids').val();
        if (role_ids == '') {
            alert_float('danger', 'Vui lòng chọn chức vụ cần xuất');
            return false;
        }
        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/roles/export_excel_permission/' + role_ids,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
</script>