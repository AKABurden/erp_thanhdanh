<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="customer_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php if (!empty($dtGroup)) { ?>
                        <span class="edit-title"><?php echo _l('Sửa Brand'); ?></span>
                    <?php } else { ?>
                        <span class="add-title"><?php echo _l('Thêm Brand'); ?></span>
                    <?php } ?>
                </h4>
            </div>
            <?php echo form_open('admin/clients/detail_brand', array('id' => 'customer-brand-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        $code = !empty($dtGroup) ? $dtGroup['code'] : '';
                        $id = !empty($dtGroup) ? $dtGroup['id'] : 0;
                        echo render_input('code', 'Mã Brand', $code);
                        echo render_input('id', '', $id,'hidden');
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $name = !empty($dtGroup) ? $dtGroup['name'] : '';
                        echo render_input('name', 'Tên Brand', $name); ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $classify = !empty($dtGroup) ? $dtGroup['classify'] : '';
                        echo render_input('classify', 'Phân Loại', $classify); ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $certification_group = !empty($dtGroup) ? $dtGroup['certification_group'] : '';
                        echo render_input('certification_group', 'Nhóm Chứng Nhận', $certification_group); ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $applied_standard = !empty($dtGroup) ? $dtGroup['applied_standard'] : '';
                        echo render_input('applied_standard', 'Tiêu Chuẩn Áp Dụng', $applied_standard); ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $verification_unit = !empty($dtGroup) ? $dtGroup['verification_unit'] : '';
                        echo render_input('verification_unit', 'ĐV Kiểm-Chứng Nhận', $verification_unit); ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $date_start = !empty($dtGroup) ? (!empty($dtGroup['date_start']) ? _dhau($dtGroup['date_start']) : '') : '';
                        echo render_date_input('date_start', 'Ngày Bắt Đầu', $date_start); ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $date_end = !empty($dtGroup) ? (!empty($dtGroup['date_end']) ? _dhau($dtGroup['date_end']) : '') : '';
                        echo render_date_input('date_end', 'Ngày Tái Tục', $date_end); ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $product_standards = !empty($dtGroup) ? $dtGroup['product_standards'] : '';
                        echo render_input('product_standards', 'Tiêu Chuẩn Sản Phẩm', $product_standards); ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        $percent_discount = !empty($dtGroup) ? $dtGroup['percent_discount'] : 0;
                        echo render_input('percent_discount', '%Chiếu Khấu', $percent_discount, 'text', [], [], '',
                            'number-format'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>

    $(function (){
        init_datepicker();
        appValidateForm($('#customer-brand-modal'), {
            code: 'required',
            name: 'required',
        }, manage_customer_groups);
    })

    $('body').on('click', '.colorpicker-with-alpha', function () {
        $.each($('input.colorpicker'), function (i, v) {
            $(v).parent('div').find('i:nth-child(1)').css('background-color', $(v).val());
        })
    })


    function manage_customer_groups(form) {
        var button = $(form).find('button[type="submit"]');
        button.button({loadingText: 'please wait...'});
        button.button('loading');
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if ($.fn.DataTable.isDataTable('.table-brand')) {
                    $('.table-brand').DataTable().ajax.reload();
                }
                alert_float('success', response.message);
            }


            $('#customer_group_modal').modal('hide');
        }).always(function () {
            button.button('reset')
        });
        return false;
    }
</script>
