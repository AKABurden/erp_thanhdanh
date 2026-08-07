<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-staff tbody tr td:nth-child(9) {
        white-space: inherit;
        min-width: 110px;
        max-width: 130px;
        text-align: center;
    }

    .table-staff tbody tr td:nth-child(3) {
        white-space: inherit;
        min-width: 150px;
        max-width: 200px;
    }

    .table-staff tbody tr td:nth-child(11) {
        white-space: inherit;
        min-width: 150px;
        max-width: 150px;
    }
</style>
<div id="wrapper">

    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <?php if (has_permission('staff', '', 'create')) { ?>
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a class="btn btn-info pull-right H_action_button mleft5" onclick="updateShift()"><?= lang('Cập nhập ca làm việc') ?></a>
                <a class="btn btn-info pull-right hide mleft5 H_action_button" onclick="print_barcode(); return false;"><?php echo _l('print_barcode'); ?></a>
                <a class="btn btn-info pull-right H_action_button mleft5 hide" onclick="printCodeStaff(); return false;"><i class="fa fa-print"></i> <?php echo _l('In QR'); ?></a>
                <a class="btn btn-info pull-right H_action_button mleft5" onclick="ViewPDF(); return false;"><i class="fa fa-print"></i> <?php echo _l('In QR'); ?></a>
                <?php
                $staff = get_table_where('tblstaff');
                ?>
                <a href="<?php echo admin_url('staff/member'); ?>" class="btn btn-info pull-right H_action_button mleft5">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?>
                </a>

                <a href="<?php echo admin_url('staff/import_staff'); ?>" class="btn btn-info pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('tnh_import_excel'); ?>
                </a>
            <?php } ?>

            <?php if (has_permission('staff', '', 'export')) { ?>
                <!-- <a onclick="export_excel()" class="btn btn-info pull-right H_action_button mright5">
                    <?php echo _l('c_export_excel'); ?>
                </a> -->
                <div class="pull-right mright5 H_border">
                    <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo render_select('fullname_search', (!empty($list_staff) ? $list_staff : []), ['staffid', 'fullname'], 'Nhân viên'); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_select('departments_search[]', (!empty($departments) ? $departments : []), ['departmentid', 'name'], 'Phòng ban', [], ['multiple' => true, 'data-actions-box' => true]); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_select('role_search[]', (!empty($roles) ? $roles : []), ['roleid', 'name'], 'Chức vụ', [], ['multiple' => true, 'data-actions-box' => true]); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <li role="presentation" class="">
                                        <a href="#all" aria-controls="all" role="tab" value="all" data-toggle="tab"><?= lang('all') ?>(<span><?= $all ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#status_work0" aria-controls="status_work0" role="tab" value="status_work0" data-toggle="tab"><?= lang('Thử việc') ?>(<span><?= $status_work0 ?></span>)</a>
                                    </li>
                                    <li role="presentation" class="active">
                                        <a href="#status_work1" aria-controls="status_work1" role="tab" value="status_work1" data-toggle="tab"><?= lang('Đang làm việc') ?>(<span><?= $status_work1 ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#status_work2" aria-controls="status_work2" role="tab" value="status_work2" data-toggle="tab"><?= lang('Nghỉ việc') ?>(<span><?= $status_work2 ?></span>)</a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="status_work1">
                            </div>
                        </div>
                        <?php
                        $table_data = array(
                            '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="staff"><label></label></div>',
                            _l('code_staff'),
                            _l('staff_dt_name'),
                            _l('staff_dt_email'),
                            _l('departments'),
                            _l('role'),
                            _l('staff_dt_last_Login'),
                            _l('staff_dt_active'),
                            _l('Trạng thái'),
                            _l('Ca làm việc'),
                            _l('Đề xuất tăng ca'),
                            _l('Chi nhánh xưởng'),
                            _l('Chi nhánh tính lương'),
                        );
                        $custom_fields = get_custom_fields('staff', array('show_on_table' => 1));
                        foreach ($custom_fields as $field) {
                            array_push($table_data, $field['name']);
                        }
                        render_datatable($table_data, 'staff');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete_staff" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('staff/delete', array('delete_staff_form'))); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('delete_staff'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="delete_id">
                    <?php echo form_hidden('id'); ?>
                </div>
                <p><?php echo _l('delete_staff_info'); ?></p>
                <?php
                echo render_select('transfer_data_to', $staff_members, array('staffid', array('firstname', 'lastname')), 'staff_member', get_staff_user_id(), array(), array(), '', '', false);
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-danger _delete"><?php echo _l('confirm'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- form print barcode -->
<?php echo form_open(admin_url('staff/pdf'), array('id' => 'form_print_barcode')); ?>
<input type="hidden" name="arrID" class="arrID" value="">
<?php echo form_close(); ?>
<!-- end -->
<?php init_tail(); ?>
<script>
    function exportExcel() {
        // groups_ch = $('[name="groups_ch"]').val();
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/staff/exportExcelStaff',
            data: {
                csrf_token_name: hash,
                // groups_ch: groups_ch,
                export_excel: 1,
            },
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
    var tAPI;
    var CustomersServerParams = {
        'fullname_search': '[name="fullname_search"]',
        'departments_search': '[name="departments_search[]"]',
        'role_search': '[name="role_search[]"]',
        'status_table': '[name="status_table"]',
    };
    $(function() {
        tAPI = initDataTable('.table-staff', window.location.href, [0], [0], CustomersServerParams, [1, 'asc']);
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $(filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });
        tAPI.columns(10).visible(false, false);
    });

    function delete_staff_member(id) {
        $('#delete_staff').modal('show');
        $('#transfer_data_to').find('option').prop('disabled', false);
        $('#transfer_data_to').find('option[value="' + id + '"]').prop('disabled', true);
        $('#delete_staff .delete_id input').val(id);
        $('#transfer_data_to').selectpicker('refresh');
    }
    $('.table-staff').on('draw.dt', function() {
        var total_tr = $('.table-staff').find('tbody').find('tr');
        $.each(total_tr, function(i, v) {
            $("#branch_salary_" + i).select2({
                'allowClear': true
            });
        });

        $('.table-staff').find('select.selectpicker').selectpicker('refresh')
    });
    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        tAPI.draw();
    });

    function format1(state) {
        if (!state.id) {
            return state.text;
        }
        var optimage = $(state.element).attr('data-image');
        var $state = $(
            '<span><img sytle="display: inline-block;" src="' + optimage + '" width="29px" /> ' + state.text.toUpperCase() + '</span>'
        );
        return $state;
    }
    $(document).on('change', '.staff_id_zalo', function(e) {
        var id_staff = $(this).attr('data-staff');
        var id_zalo = $(this).val();
        var athis = $(this);
        var data = {};
        if (!id_zalo) {
            id_zalo = 0;
        }
        data['id_staff'] = id_staff;
        data['id_zalo'] = id_zalo;
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'staff/set_id_zalo', data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == 0) {
                athis.select2('val', '');
            }
            alert_float(response.alert_type, response.message);
        });
    });
    $(document).on('change', '.branch_salary', function(e) {
        var id_staff = $(this).attr('data-staff');
        var id_branch = $(this).val();
        var athis = $(this);
        var data = {};
        data['id_staff'] = id_staff;
        data['id_branch'] = id_branch;
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'staff/set_id_branch', data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == 0) {
                athis.select2('val', '');
            }
            alert_float(response.alert_type, response.message);
        });
    });

    function print_barcode() {
        var arr_id = [];
        var rows = $('.table-staff').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
                arr_id.push(checkbox.val());
            }
        });
        if (arr_id.length > 0) {
            var str = arr_id.toString();
            $('.arrID').val(str);
            $('#form_print_barcode').submit();
        } else {
            alert_float('danger', 'Vui lòng chọn nhân viên!');
        }
    }

    function printCodeStaff() {
        var ids = '';
        var rows = $('.table-staff').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });
        ids = ids.slice(0, -1);

        if (!ids) {
            bootbox.alert('Xin vui lòng chọn nhân viên cần in mã vạch');
            return;
        }

        window.open(site.base_url + 'admin/staff/print_code?ids=' + ids, "_blank");
    }

    function ViewPDF() {
        var ids = '';
        var rows = $('.table-staff').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });
        ids = ids.slice(0, -1);

        if (!ids) {
            bootbox.alert('Xin vui lòng chọn nhân viên cần in mã vạch');
            return;
        }

        url = admin_url + 'staff/print_pdf_html?ids=' + ids;
        var iframe = document.createElement('iframe');
        // iframe.id = 'pdfIframe'
        iframe.className = 'pdfIframe'
        document.body.appendChild(iframe);
        iframe.style.display = 'none';
        iframe.onload = function() {
            setTimeout(function() {
                iframe.focus();
                iframe.contentWindow.print();
                URL.revokeObjectURL(url)
                // document.body.removeChild(iframe)
            }, 1);
        };
        iframe.src = url;
    }


    function export_excel() {
        var get = "?data=true";
        $.each(CustomersServerParams, function(index, value) {
            var dataItems = $(value).val();
            if (dataItems) {
                if ($.isArray(dataItems)) {
                    $.each(dataItems, function(i, v) {
                        get += '&' + index + '[]=' + v;
                    })
                } else {
                    get += '&' + index + '=' + dataItems;
                }
            }
        })
        window.open(admin_url + 'staff/export_staff' + get, '_blank');
    }

    $('body').on('change', 'select.staff_branch', function() {
        var id_branch = $(this).val();
        console.log(id_branch);
        var id_staff = $(this).data('staff');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['staffid'] = id_staff;
        data['id_branch'] = id_branch;
        $.post(admin_url + 'staff/add_branch', data, function(result) {
            result = JSON.parse(result);
            alert_float(result.alert_type, result.message);
        })
    })

    $(document).on('change', '.check_salary', function(
        event) {
        id = $(this).attr('data-id');
        checked = $(this).is(':checked')
        if (checked == true) {
            value = 1;
        } else {
            value = 0;
        }
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/staff/changeStatusSalary',
            data: {
                csrf_token_name: hash,
                id: id,
                value: value,
            },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    tAPI.draw();
                } else {
                    alert_float('danger', response.message);
                    tAPI.draw();
                }
            }
        });
    });

    function updateShift(){
        var ids = '';
        var rows = $('.table-staff').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });
        ids = ids.slice(0, -1);

        if (!ids) {
            bootbox.alert('Xin vui lòng chọn nhân viên cập nhập');
            return;
        }
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/staff/loadViewUpdateShift',
            data: {
                csrf_token_name: hash,
                ids: ids,
            },
            dataType: "html",
            success: function(response) {
                $('#tnhModal').html(response);
                $('#tnhModal').modal({backdrop: 'static', keyboard: true});
            }
        });
    }
</script>
</body>

</html>