<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        </div>
    </div>
    <?php echo form_open('admin/tools_supplies/import_category',array('id'=>'add-product')); ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="text-danger">
                            <?= lang('data_fields_required') ?>: <?= implode(', ', $required) ?>
                        </div>
                        <hr class="hr-panel-heading" />
                        <div class="mbot10">
                            <button type="button" class="btn btn-primary btn-automatic"><?= lang('tnh_auto_data_fields') ?></button>
                            <button type="button" class="btn btn-warning btn-referesh"><?= lang('tnh_referesh') ?></button>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mbot10">
                                <?= lang('tnh_row_start', 'row_start') ?>
                                <input type="number" name="row_start" id="row_start" class="form-control" value="2" min="1">
                            </div>
                            <div class="col-md-2 mbot10">
                                <?= lang('tnh_row_end', 'row_end') ?>
                                <input type="number" name="row_end" id="row_end" class="form-control" value="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mbot10">
                                <div class="">
                                    <div class="input-group input-file" name="file">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default btn-choose" type="button"><?= lang('file') ?></button>
                                        </span>
                                        <input type="text" name="text_file" class="form-control" placeholder='<?= lang('choose') ?>' />
                                        <span class="input-group-btn">
                                           <button class="btn btn-warning btn-reset" type="button">Reset</button>
                                       </span>
                                   </div>
                               </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mbot10">
                                <button type="submit" class="btn btn-success add" name="save" value="1"><?= lang('save') ?></button>
                            </div>
                        </div>
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="">
                            <table class="tnh-tb table-hover table-bordered table-condensed table-import" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="primary-table" style="width: 5%; text-align: center;">
                                            <button type="button" class="btn btn-danger btn-add"><i class="fa fa-plus"></i></button>
                                        </th>
                                        <th class="primary-table" style="width: 25%;"><?= lang('tnh_data_fields') ?></th>
                                        <th class="primary-table" style="width: 20%;"><?= lang('tnh_column') ?></th>
                                        <th class="primary-table" style="width: 35%;"><?= lang('note') ?></th>
                                        <th class="primary-table" style="width: 5%;"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="" style="width: 5%; text-align: center;">
                                            <button type="button" class="btn btn-danger btn-add"><i class="fa fa-plus"></i></button>
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?php init_tail(); ?>
<?php $this->load->view('loader')?>
<!-- plugin tnh -->
<!-- <link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css') ?>"> -->
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<!-- end -->
<script type="text/javascript">
    var cloumn_excel = <?= json_encode(cloumns_excel()) ?>;
    var fields = <?= json_encode($list) ?>;
    var site = <?= json_encode(array('base_url' => base_url())) ?>;

    function totalStages()
    {
        var table = $('.table-import tbody tr').length;
        var stt = 0;
        for (ii = 0; ii < table; ii++)
        {
            stt++;
            element = $('.table-import tbody tr')[ii];
            $(element).find('.stt').html(stt);
        }
    }

    function addRow(selected_fields = 0, selected_cloumn = 0) {
        tr_html = '';
        tr_html+= '<tr>';
        tr_html+= '<td class="text-center stt"></td>';
        tr_html+= '<td>'+
                        '<select name="fields[]" id="fields" class="form-control fields" data-live-search="true" data-none-selected-text="<?= lang('choose') ?>" required="required">'+
                        optionFields(fields, selected_fields)+
                        '</select>'+
                   '</td>';
        tr_html+= '<td>'+
                        '<select name="cloumn_excel[]" id="cloumn_excel" class="form-control cloumn_excel" data-live-search="true" data-none-selected-text="<?= lang('choose') ?>" required="required">'+
                            optionCloumnExcel(cloumn_excel, selected_cloumn)+
                        '</select>'+
                   '</td>';
        tr_html+= '<td class="td-note"></td>';
        tr_html+= '<td class="text-center"><button type="button" class="btn btn-warning btn-remove"><i class="fa fa-remove"></i></button></td>';
        tr_html+= '</tr>';

        return tr_html;
    }

    function checkWarning(field, row)
    {
        if (field == "code") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_required_unique') ?>'));
        } else if (field == "name") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_required') ?>'));
        } else {
            row.find('.td-note').html('');
        }
    }

    $(document).ready(function() {
        bs_input_file();

        $('.btn-add').click(function(event) {
            tr_html = addRow();

            $('.table-import tbody').append(tr_html);
            $('.cloumn_excel').selectpicker();
            $('.fields').selectpicker();
            totalStages();
        });

        $(document).on('click', '.btn-remove', function(event) {
            event.preventDefault();
            $(this).closest('tr').remove();
            totalStages();
        });

        $(document).on('changed.bs.select', 'select.fields', function (e, clickedIndex, isSelected, previousValue) {
            row = $(this).closest('tr');
            field = $(this).val();
            checkWarning(field, row);
            lastrow = $('.table-import tbody tr')[$('.table-import tbody tr').length - 1];
            if ($(lastrow).find('select.fields').val()) {
                $('.table-import thead tr th .btn-add').trigger('click');
            }
        });

        $(document).on('click', '.btn-automatic', function(event) {
            event.preventDefault();
            var button = $(this);
            var k = 0;
            bootbox.confirm({
                message: '<?= lang('tnh_you_want_to_automatically_create_field') ?>',
                buttons: {
                    confirm: {
                        label: '<?= lang('yes') ?>',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: '<?= lang('no') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $('.table-import tbody').html('');
                        button.button({loadingText: '<i class="fa fa-spinner fa-spin"></i> <?= lang('please_waiting') ?>'});
                        button.button('loading');
                        $.each(fields, function(index, el) {
                            cl = cloumn_excel[k];
                            tr_html = addRow(index, cl);
                            $('.table-import tbody').append(tr_html);
                            row = $($('.table-import tbody tr')[k]);
                            checkWarning(index, row);
                            k++;
                        });
                        $('.cloumn_excel').selectpicker();
                        $('.fields').selectpicker();
                        button.button('reset');
                        totalStages();
                    }
                }
            });
        });

        $(document).on('click', '.btn-referesh', function(event) {
            bootbox.confirm({
                message: '<?= lang('tnh_you_are_referesh') ?>',
                buttons: {
                    confirm: {
                        label: '<?= lang('yes') ?>',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: '<?= lang('no') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $('.table-import tbody').html('');
                    }
                }
            });
        });
    });

    $(document).ready(function() {
        appValidateForm($('#add-product'),
            {
                text_file: {required: true, extension: "xlsx,xls"},
            },
            importExcel,
            {text_file: '<?= lang('tnh_please_choose_excel') ?>'}
        );

        function importExcel(form) {
            $('.add').attr('disabled', 'disabled');

            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });
            //
            var url = form.action;
            $.ajax({
                url : site.base_url+'admin/tools_supplies/import_category',
                type : 'POST',
                dataType: 'JSON',
                cache : false,
                contentType : false,
                processData : false,
                data: formData,
            })
            .done(function(data) {
                $('.add').removeAttr('disabled', 'disabled');
                if (data.result) {
                    alert_float('success', data.message);
                } else {
                    alert_float('danger', data.message);
                }
                if (typeof data.errors != "undefined" && data.errors) {
                    $('.show-alert').show();
                    $('.show-errors').html(data.errors);
                }
            })
            .fail(function() {
                alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
            });
            return false;
        }
    });
</script>

