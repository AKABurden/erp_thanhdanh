<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .fixedHeader-floating {
        position: fixed !important;
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(16px);
        -ms-transform: translateX(16px);
        transform: translateX(16px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <!-- <div> -->
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php if ($this->perAddCategory): ?>
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/products/add_category_stages') ?>" class="btn btn-info mright5 pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('add'); ?>
                </a>
                <a href="<?= base_url('admin/products/modal_excel_category_stages') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
                </a>
            <?php endif ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="table-category" class="table table-hover table-bordered table-condensed dataTable table-category" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class=""><div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="category"><label for="mass_select_all"></label></div></th>
                                        <th class="text-center"><?= lang('tnh_code_category_stages') ?></th>
                                        <th class="text-center"><?= lang('tnh_name_category_stages') ?></th>
                                        <th class="text-center"><?= lang('type') ?></th>
                                        <th class="text-center"><?= lang('Năng suất') ?></th>
                                        <th class="text-center"><?= lang('Bàn giao') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
    var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {};
    var oTable = '';
    var arr = [];

    $(document).ready(function() {
        oTable = tnhDatatable(
            '#table-category',
            {
                'order': [[1, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                scrollY: height_body,
                scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/products/getCategoryStages') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            return '<div class="checkbox"><input type="checkbox" class="category_id" name="category_id[]" id="check-item'+data+'" value="'+ data +'"><label for="check-item'+data+'"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '50px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>'+ data +'</div>';
                        },
                        "targets": 1,
                        "name": 'code'
                    },
                    {"targets": 2, "name": 'name'},
                    {"targets": 3, "name": 'type'},
                    {
                        "render": function(data, type, row) {
                            if (data > 0) {
                                return '<div style="width: 80px" class="center"><label class="switch">' +
                                    '<input type="checkbox" onchange="update_type(1,' + row[0] + ', this.checked)" value="new" checked>' +
                                    '<span class="slider round"></span>' +
                                    '</label></div>';
                            } else {
                                return '<div style="width: 80px" class="center"><label class="switch">' +
                                    '<input type="checkbox" onchange="update_type(1,' + row[0] + ', this.checked)" value="new">' +
                                    '<span class="slider round"></span>' +
                                    '</label></div>';
                            }
                        },
                        "targets": 4,
                        "width": "100px",
                        "name": 'check_productivity'
                    },
                    {
                        "render": function(data, type, row) {
                            if (data > 0) {
                                return '<div style="width: 80px" class="center"><label class="switch">' +
                                    '<input type="checkbox" onchange="update_type(2,' + row[0] + ', this.checked)" value="new" checked>' +
                                    '<span class="slider round"></span>' +
                                    '</label></div>';
                            } else {
                                return '<div style="width: 80px" class="center"><label class="switch">' +
                                    '<input type="checkbox" onchange="update_type(2,' + row[0] + ', this.checked)" value="new">' +
                                    '<span class="slider round"></span>' +
                                    '</label></div>';
                            }
                        },
                        "targets": 5,
                        "width": "100px",
                        "name": 'is_bangiao'
                    },
                    {"targets": 6, "name": 'actions', 'orderable': false, 'searchable': false, 'width': '100px'}
                ]
            }
        );
    });
    function update_type(type, id, check) {
        dataString = {
            [csrfData['token_name']]: csrfData['hash'],
            isChecked: check,
            type: type
        };
        jQuery.ajax({
            type: "post",
            url: site.base_url + 'admin/products/updateis_type_category_stages/' + id,
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                if (response.isSuccess) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
                oTable.draw('page');
            }
        });
    }
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>

