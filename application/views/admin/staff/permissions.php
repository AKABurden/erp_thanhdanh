<style>
    .permission-matrix-container {
        width: 100%;
        overflow-x: auto;
    }
    .table-permissions {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .table-permissions th,
    .table-permissions td {
        border: 1px solid #dcdcdc;
        padding: 10px;
        vertical-align: middle;
    }
    .table-permissions th {
        background: #4ccdd6; /* Màu xanh giống hình mẫu */
        color: #000;
        text-align: center;
        font-weight: bold;
        white-space: nowrap;
    }
    .table-permissions th:nth-child(1),
    .table-permissions th:nth-child(2),
    .table-permissions th:last-child {
        text-align: left;
    }
    .table-permissions td.text-center {
        text-align: center;
    }
    /* Checkbox ở các cột ma trận */
    .table-permissions .checkbox {
        margin-top: 0;
        margin-bottom: 0;
    }
    .table-permissions td.text-center .checkbox {
        display: inline-block;
    }
    .table-permissions td.text-center .checkbox label {
        padding-left: 20px;
    }
    /* Checkbox ở cột Quyền mở rộng */
    .extra-permissions-col {
        min-width: 250px; /* Đảm bảo cột mở rộng có độ rộng vừa đủ */
    }
    .extra-checkbox-item {
        display: inline-block;
        margin-right: 15px;
        margin-top: 5px !important;
        margin-bottom: 5px !important;
    }
    .extra-checkbox-item label {
        font-weight: normal;
    }
</style>

<div class="permission-matrix-container">
    <table class="table table-permissions table-hover">
        <?php
        if(isset($member)){
            $is_admin = is_admin($member->staffid);
        }

        // 1. ĐỊNH NGHĨA CÁC CỘT CỐT LÕI (Sẽ hiển thị thành ma trận ngang)
        $core_columns = [
                'view'     => 'Xem chung',
                'view_own' => 'Xem riêng',
                'create'   => 'Thêm',
                'edit'     => 'Sửa',
                'delete'   => 'Xóa',
                'import'   => 'Import',
                'export'   => 'Export'
        ];
        ?>

        <thead>
        <tr>
            <th>Module</th>
            <th>Function</th>
            <?php foreach($core_columns as $col_key => $col_label) { ?>
                <th><?= $col_label ?></th>
            <?php } ?>
            <th>Quyền mở rộng khác</th> <!-- Cột gom các quyền phụ -->
        </tr>
        </thead>
        <tbody>
        <?php
        foreach(get_available_staff_permissions() as $feature => $permission) {
            if($feature == 'goals' || $feature == 'surveys') {
                continue;
            }

            if(isset($permission['child']) && is_array($permission['child']) && count($permission['child']) > 0) {
                foreach ($permission['child'] as $key_child => $value_child) {
                    ?>
                    <tr>
                        <td><b><?php echo $permission['name']; ?></b></td>
                        <td><?php echo $value_child['name']; ?></td>

                        <?php
                        // ==========================================
                        // PHẦN 1: IN CÁC CHECKBOX CHO CỘT CỐT LÕI
                        // ==========================================
                        foreach($core_columns as $col_key => $col_label) {
                            ?>
                            <td class="text-center">
                                <?php
                                if(isset($value_child['permissions'][$col_key])) {
                                    $checked = '';
                                    if(isset($roleid)) {
                                        $checkTrue_permission = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $roleid, 'obj_permission' => $key_child, 'can_'.$col_key => 1), '', 'row');
                                        if($checkTrue_permission) $checked = 'checked';
                                    }
                                    if(isset($staffid)) {
                                        $checkTrue_permission = get_table_where('tbl_staff_child_permission_v2', array('id_staff' => $staffid, 'obj_permission' => $key_child, 'can_'.$col_key => 1), '', 'row');
                                        if($checkTrue_permission) $checked = 'checked';
                                    }
                                    ?>
                                    <div class="checkbox checkbox-primary" title="<?= strip_tags($value_child['permissions'][$col_key]) ?>">
                                        <input type="checkbox"
                                               class="permission_child"
                                               id="chk_<?= $feature ?>_<?= $key_child ?>_<?= $col_key ?>"
                                               name="permission[<?=$feature?>][child][<?=$key_child?>][<?=$col_key?>]"
                                               data-child="<?=$key_child?>"
                                               data-can="<?=$col_key?>"
                                                <?= $checked ?>>
                                        <label for="chk_<?= $feature ?>_<?= $key_child ?>_<?= $col_key ?>"></label>
                                    </div>
                                <?php } ?>
                            </td>
                        <?php } ?>

                        <?php
                        // ==========================================
                        // PHẦN 2: IN CÁC CHECKBOX CHO CỘT "MỞ RỘNG"
                        // ==========================================
                        echo '<td class="extra-permissions-col">';

                        if(isset($value_child['permissions']) && is_array($value_child['permissions'])) {
                            foreach($value_child['permissions'] as $extra_key => $extra_label_string) {
                                // Nếu quyền này KHÔNG nằm trong các cột cốt lõi thì in ra đây
                                if(!array_key_exists($extra_key, $core_columns)) {
                                    $checked = '';
                                    if(isset($roleid)) {
                                        $checkTrue_permission = get_table_where('tbl_roles_child_permission_v2', array('id_role' => $roleid, 'obj_permission' => $key_child, 'can_'.$extra_key => 1), '', 'row');
                                        if($checkTrue_permission) $checked = 'checked';
                                    }
                                    if(isset($staffid)) {
                                        $checkTrue_permission = get_table_where('tbl_staff_child_permission_v2', array('id_staff' => $staffid, 'obj_permission' => $key_child, 'can_'.$extra_key => 1), '', 'row');
                                        if($checkTrue_permission) $checked = 'checked';
                                    }
                                    ?>
                                    <div class="checkbox checkbox-primary extra-checkbox-item">
                                        <input type="checkbox"
                                               class="permission_child"
                                               id="chk_<?= $feature ?>_<?= $key_child ?>_<?= $extra_key ?>"
                                               name="permission[<?=$feature?>][child][<?=$key_child?>][<?=$extra_key?>]"
                                               data-child="<?=$key_child?>"
                                               data-can="<?=$extra_key?>"
                                                <?= $checked ?>>
                                        <!-- Hiện hẳn Text tên quyền (ví dụ: "Duyệt QC") bên cạnh ô tick -->
                                        <label for="chk_<?= $feature ?>_<?= $key_child ?>_<?= $extra_key ?>"><?= strip_tags($extra_label_string) ?></label>
                                    </div>
                                    <?php
                                }
                            }
                        }

                        echo '</td>';
                        ?>
                    </tr>
                    <?php
                }
            }
        }
        ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('.permission_child:checked').trigger('change');
    });

    $(document).on('change', '.permission_child', function (e) {
        var parentRow = $(this).closest('tr');

        if($(this).prop('checked')) {
            if($(this).attr('data-can') == 'view') {
                parentRow.find('input.permission_child[data-can="view_own"]').prop('checked', false).attr('disabled', true);
            }
            else if($(this).attr('data-can') == 'view_own') {
                parentRow.find('input.permission_child[data-can="view"]').prop('checked', false).attr('disabled', true);
            }
        }
        else {
            if($(this).attr('data-can') == 'view') {
                parentRow.find('input.permission_child[data-can="view_own"]').attr('disabled', false);
            }
            else if($(this).attr('data-can') == 'view_own') {
                parentRow.find('input.permission_child[data-can="view"]').attr('disabled', false);
            }
        }
    });
</script>