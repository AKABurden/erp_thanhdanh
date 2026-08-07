<div class="modal fade" id="view_modal" role="dialog">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? $title : ''; ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/internal_proposal/add_task_process/', array('id' => 'task')); ?>
            <div class="modal-body">
                <input class="hide" id="process_id" name="process_id" value="<?= $process_id ?>">
                <input class="hide" id="detail_id" name="detail_id" value="<?= $detail_id ?>">
                <input class="hide" id="id" name="id" value="<?= $id ?>">
                <div class="row">
                    <div class="col-md-12">
                        <div class="div-items-delivery_records"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <?php if ($is == 0) { ?>
                    <button type="submit" class="btn btn-primary add add-finished-stages"><?= _l('save') ?></button>
                    <div class="checkbox checkbox-danger pull-right hide">
                        <input type="checkbox" class="save_create_task" id="save_create_task" value="1">
                        <label for="save_create_task">Tạo phiếu báo cáo</label>
                    </div>
                <?php } ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    $('#view_modal').modal('show');
    var data = {
        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
        'process_id': '<?= $process_id ?>',
        'id': '<?= $id ?>',
        'is': '<?= $is ?>',
        'detail_id': '<?= $detail_id ?>',
    };
    $.post(admin_url + 'internal_proposal/get_table_delivery_records_internal_proposal', data, function (result) {
        $('.div-items-delivery_records').html(result);
    })
    $(function () {
        appValidateForm($('#task'), {
            receiver: "required",
        }, saveHandlingProducts);

        function saveHandlingProducts(form) {
            $('.add').attr('disabled', 'disabled');
            // var data = $(form).serialize();
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function (i, tag) {
                $.each($(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(formParams, function (i, val) {
                formData.append(val.name, val.value);
            });
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
                .done(function (data) {
                    if (data.success) {
                        if (data.id_delivery_records && $('#save_create_task').prop('checked')) {
                            window.open(data.href, '_blank');
                        }
                        $('.add').removeAttr('disabled', 'disabled');
                        alert_float(data.alert_type, data.message);
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw(false);
                        }
                        if (data.id_task) {
                            init_task_modal(data.id_task);
                        }
                        $('.modal-dialog .close').trigger('click');
                        return false;
                    }

                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw(false);
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function () {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
        <?php if (empty($category_hand_over) && empty($is)) { ?>
            $('.add-finished-stages').click()
        <?php } ?>
    })
    init_selectpicker();

    function checkResult(id, _this, type) {
        if ($(_this).prop('checked')) {
            var result = $(_this).attr('data-value');
            $(`.isCheck_` + id).prop('checked', false);
            $(_this).prop('checked', true);
            if (type == 2) {
                var r = confirm("Check không duyệt sẽ tạo tạo ra phiếu báo cáo không phù hợp. Bạn có chắc chắn không?");
                if (r == false) {
                    $(`.isCheck_` + id + `_yes`).prop('checked', true);
                    $(_this).prop('checked', false);
                    return false;
                } else {
                    // Lưu toàn bộ form trước khi tạo phiếu báo cáo không phù hợp
                    var form = $('#task');
                    var url = form.attr('action');
                    var formData = new FormData(form[0]);
                    $.ajax({
                        url: admin_url + 'internal_proposal/add_task_process_reject',
                        type: 'POST',
                        dataType: 'JSON',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (data) {
                            if (data.success) {
                                var url = '<?= base_url('admin/production_report/detail') . '?id_internal_proposal=' . $id . '&id_internal_proposal_process=' . $detail_id . '&id_internal_proposal_process_child=' ?>' + id
                                window.open(url, '_blank');
                                alert_float('success', data.message);
                                if (typeof oTable != 'undefined' && oTable != '') {
                                    oTable.draw(false);
                                }
                                $('.modal-dialog .close').trigger('click');
                            } else {
                                alert_float('danger', data.message || 'Có lỗi xảy ra khi lưu dữ liệu.');
                                $(`.isCheck_` + id + `_yes`).prop('checked', true);
                                $(_this).prop('checked', false);
                            }
                        },
                        error: function () {
                            alert_float('danger', 'Có lỗi xảy ra khi lưu dữ liệu.');
                        }
                    });
                }
            }
        } else {
            $(_this).prop('checked', true);
        }
    }
</script>