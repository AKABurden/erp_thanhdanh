<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <?php //if (has_permission('spending_plan', '', 'create')) { 
                ?>
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/work_norm_group/modal_excel') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
                </a>
                <!-- <a href="<?= admin_url('work_norm_group/submit') ?>" class="btn btn-info mright5 pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?>
                </a> -->
                <?php //} 
                ?>
                <a href="javascript:void(0)" onclick="exportExcel()" class="btn btn-info H_action_button pull-right mright5"><?= lang('Xuất excel') ?></a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12 hide">
                <div id="search-tnh" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_date" class="control-label"><?= _l('Ngày lập phiếu') ?></label>
                            <div class="input-group">
                                <input type="text" id="search_date" name="search_date" class="form-control search_date" aria-invalid="false">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php $table_data = array(
                            _l('STT'),
                            _l('Mã Nhóm CV'),
                            _l('Tên Nhóm CV'),
                            _l('Công Việc'),
                            _l('ĐVT'),
                            _l('Năng Suất/Giờ'),
                            _l('Công Thức Tính Định Mức'),
                            _l('Định Mức'),
                            _l('Số Lần Thực Hiện'),
                            _l('action'),
                        );
                        render_datatable($table_data, 'tb-main');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    ajaxSelectParamsCallback('#client_id', 'admin/clients/searchCustomers/', $('#client_id').val(), false, true);

    var fnserverparams = {
        'search_date': '[name="search_date"]',
        'client_id': '[name="client_id"]',
    };

    $(document).ready(function() {
        search_daterangepicker();
        $.each(fnserverparams, function(key, value) {
            $(value).on('change', function() {
                // Gọi oTable.draw() khi có sự thay đổi
                oTable.draw();
            });
        });

        oTable = tnhInitDataTable('.table-tb-main', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': false,
            "ajax": {
                "url": site.base_url + 'admin/work_norm_group/',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    // if (json.tFoot) {
                    //     $.each(json.tFoot, function(index, value) {
                    //         $('#tb-main tfoot').find('td:nth-child(' + (++index) + ')').html(`<div class="${value.class}">${value.format == 'money' ? tnhFormatMoney(value.number) : tnhFormatNumber(value.number)}</div>`);
                    //     });
                    // }

                    // $.each(json.arrStatus, function (index, value) { 
                    //     $('.span-'+index).html(`(${tnhFormatNumber(value)})`);
                    // });
                    return json.aaData;
                }
            },
            // "columnDefs": [{
            //         targets: [0, 11],
            //         searchable: false,
            //         sortable: false,
            //     },
            //     // {
            //     //     targets: [8],
            //     //     searchable: false,
            //     // }
            // ],
        });
    });

    function exportExcel() {
        // search_date = $("#search_date").val();
        // client_id = $("#client_id").val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/work_norm_group/export_excel',
            data: {
                csrf_token_name: hash,
                // search_date: search_date,
                // client_id: client_id,
            },
            dataType: "json",
            success: function(response) {
                console.log(response);
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }

    var search_daterangepicker = () => {
        $('input[name="search_date"]').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            isInvalidDate: false,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": lang_daterangepicker.applyLabel,
                "cancelLabel": lang_daterangepicker.cancelLabel,
                "fromLabel": lang_daterangepicker.fromLabel,
                "toLabel": lang_daterangepicker.toLabel,
                "customRangeLabel": lang_daterangepicker.customRangeLabel,
                "daysOfWeek": lang_daterangepicker.daysOfWeek,
                "monthNames": lang_daterangepicker.monthNames
            },
        }, function(start, end, label) {});
        $('input[name="search_date"]').val('').datepicker("refresh");
        $('input[name="search_date"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $("#search_date").trigger("change");
        });
        $('input[name="search_date"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#search_date").trigger("change");
        });
    };

    
    $('body').on('click', '._delete_row', function() {
        var _href = $(this).attr('href');
        if (confirm('Bạn có chắc chắn muốn xóa?')) {
            $.get(_href, function(result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                if (result.isSuccess) {
                    oTable.ajax.reload();
                }
                return false;
            }).fail(function(error) {
                alert_float('danger', error.responseText);
            });
        }
        return false;
    })
</script>